<?php

namespace Tests\Feature;

use App\Models\Guru;
use App\Models\Pengajuan;
use App\Models\User;
use App\Models\Presensi;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PersetujuanTest extends TestCase
{
    use RefreshDatabase;

    public function test_approving_leave_request_creates_attendance_successfully()
    {
        $kepsek = User::factory()->create(['role' => 'kepala_sekolah']);
        $guru = Guru::factory()->create(['status' => 'aktif']);
        
        $pengajuan = Pengajuan::create([
            'guru_id' => $guru->id,
            'jenis' => 'izin',
            'alasan' => 'Ada acara keluarga',
            'status' => 'menunggu',
            'tanggal_mulai' => today(),
            'tanggal_selesai' => today(),
        ]);

        $response = $this->actingAs($kepsek)->post(route('kepsek.persetujuan.setujui', $pengajuan));
        
        $response->assertRedirect();
        
        $this->assertDatabaseHas('presensis', [
            'guru_id' => $guru->id,
            'tanggal' => today()->toDateString(),
            'status' => 'izin',
        ]);
        
        $this->assertDatabaseHas('pengajuans', [
            'id' => $pengajuan->id,
            'status' => 'disetujui'
        ]);
    }

    public function test_approving_same_request_multiple_times_does_not_create_duplicate()
    {
        $kepsek = User::factory()->create(['role' => 'kepala_sekolah']);
        $guru = Guru::factory()->create(['status' => 'aktif']);
        
        $pengajuan = Pengajuan::create([
            'guru_id' => $guru->id,
            'jenis' => 'izin',
            'alasan' => 'Ada acara keluarga',
            'status' => 'menunggu',
            'tanggal_mulai' => today(),
            'tanggal_selesai' => today(),
        ]);

        $this->actingAs($kepsek)->post(route('kepsek.persetujuan.setujui', $pengajuan));
        $this->actingAs($kepsek)->post(route('kepsek.persetujuan.setujui', $pengajuan));
        
        $this->assertEquals(1, Presensi::where('guru_id', $guru->id)->where('tanggal', today()->toDateString())->count());
    }

    public function test_approving_long_date_range_processes_chunks_correctly()
    {
        $kepsek = User::factory()->create(['role' => 'kepala_sekolah']);
        $guru = Guru::factory()->create(['status' => 'aktif']);
        
        $pengajuan = Pengajuan::create([
            'guru_id' => $guru->id,
            'jenis' => 'cuti',
            'alasan' => 'Cuti panjang',
            'status' => 'menunggu',
            'tanggal_mulai' => today(),
            'tanggal_selesai' => today()->addDays(150),
        ]);

        $this->actingAs($kepsek)->post(route('kepsek.persetujuan.setujui', $pengajuan));
        
        $this->assertEquals(151, Presensi::where('guru_id', $guru->id)->where('status', 'cuti')->count());
    }

    public function test_approval_rolls_back_if_synchronization_fails()
    {
        $kepsek = User::factory()->create(['role' => 'kepala_sekolah']);
        $guru = Guru::factory()->create(['status' => 'aktif']);
        
        $pengajuan = Pengajuan::create([
            'guru_id' => $guru->id,
            'jenis' => 'izin',
            'alasan' => 'Gagal',
            'status' => 'menunggu',
            'tanggal_mulai' => today(),
            'tanggal_selesai' => today(),
        ]);

        $this->mock(\App\Services\PresensiService::class, function ($mock) {
            $mock->shouldReceive('sinkronkanPengajuanDisetujui')->andThrow(new \Exception('Sync failed'));
        });

        try {
            $this->actingAs($kepsek)->post(route('kepsek.persetujuan.setujui', $pengajuan));
        } catch (\Exception $e) {
            $this->assertEquals('Sync failed', $e->getMessage());
        }
        
        $this->assertDatabaseHas('pengajuans', [
            'id' => $pengajuan->id,
            'status' => 'menunggu'
        ]);
        
        $this->assertEquals(0, Presensi::where('guru_id', $guru->id)->count());
    }
}
