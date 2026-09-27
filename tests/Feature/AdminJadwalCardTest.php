<?php

namespace Tests\Feature;

use App\Models\Guru;
use App\Models\User;
use App\Services\JadwalGuruService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminJadwalCardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        Storage::fake('local');
        $this->actingAs(User::factory()->create(['role' => 'admin']));
    }

    public function test_schedule_page_and_pdf_use_existing_qr_and_latest_schedule(): void
    {
        $guru = Guru::factory()->create(['token_qr' => 'GURU-EXISTING-TOKEN']);
        $jadwal = array_fill(1, 7, ['aktif' => '1', 'jam_masuk' => '07:30', 'jam_pulang' => '14:00']);
        app(JadwalGuruService::class)->save($guru, $jadwal);
        $this->get(route('admin.guru.index'))->assertOk()->assertSee($guru->nama)
            ->assertSee('guru-card-photo')->assertSee('QR Presensi Guru')
            ->assertDontSee(route('admin.guru.kartu.show', $guru));
        $this->get(route('admin.guru.kartu.show', $guru))->assertRedirect(route('admin.guru.index'));
        $this->get(route('admin.scan.index'))->assertRedirect(route('admin.jadwal.index'));
        $this->get(route('admin.jadwal.index'))->assertOk()->assertSee($guru->nama)->assertSee('14:00')->assertSee('Unduh PDF Kartu Guru')->assertDontSee('Scan Presensi');
        $pdf = $this->get(route('admin.jadwal.kartu', $guru));
        $pdf->assertOk()->assertHeader('content-type', 'application/pdf');
        $this->assertStringStartsWith('%PDF-', $pdf->getContent());
        $this->assertSame('GURU-EXISTING-TOKEN', $guru->fresh()->token_qr);
        $jadwal[1]['jam_pulang'] = '16:15';
        app(JadwalGuruService::class)->save($guru, $jadwal);
        $this->get(route('admin.jadwal.index'))->assertOk()->assertSee('16:15');
        $html = view('admin.jadwal.kartu-pdf', ['guru' => $guru->fresh(), 'hari' => JadwalGuruService::HARI, 'qrImage' => '', 'logoImage' => ''])->render();
        $this->assertStringNotContainsString('16:15', $html);
        $this->assertStringNotContainsString('JADWAL MINGGUAN', $html);
        $this->assertStringContainsString('KARTU IDENTITAS GURU', $html);
        $this->assertSame('16:15', $guru->fresh()->jadwal[1]['jam_pulang']);
    }

    public function test_missing_qr_is_not_generated_by_schedule_menu(): void
    {
        $guru = Guru::factory()->create(['token_qr' => null]);
        app(JadwalGuruService::class)->save($guru, [1 => ['aktif' => true, 'jam_masuk' => '07:30', 'jam_pulang' => '15:00']]);
        $this->get(route('admin.jadwal.index'))->assertOk()->assertSee('QR belum tersedia');
        $this->get(route('admin.jadwal.kartu', $guru))->assertStatus(422);
        $this->assertNull($guru->fresh()->token_qr);
    }

    public function test_pdf_is_admin_only(): void
    {
        $guru = Guru::factory()->create(['token_qr' => 'existing-token']);
        foreach (['guru', 'kepala_sekolah'] as $role) {
            $this->actingAs(User::factory()->create(['role' => $role]));
            $this->get(route('admin.jadwal.kartu', $guru))->assertForbidden();
            $this->get(route('admin.jadwal.download'))->assertForbidden();
        }
    }

    public function test_schedule_download_includes_all_pages_and_days(): void
    {
        Guru::factory()->count(11)->create()->each(fn ($guru) => app(JadwalGuruService::class)->save($guru, [1 => ['aktif' => '1', 'jam_masuk' => '07:30', 'jam_pulang' => '15:00']]));
        $guru = Guru::factory()->create(['nama' => '=FORMULA', 'nip' => '001234567890']);
        app(JadwalGuruService::class)->save($guru, [1 => ['aktif' => true, 'jam_masuk' => '08:15', 'jam_pulang' => '16:45']]);
        $response = $this->get(route('admin.jadwal.download'));
        $response->assertOk()->assertDownload();
        $csv = $response->streamedContent();
        $this->assertStringContainsString("'=FORMULA", $csv);
        $this->assertStringContainsString('08:15', $csv);
        $this->assertStringContainsString('16:45', $csv);
        $this->assertStringContainsString('001234567890', $csv);
        $this->assertCount(13, array_filter(explode("\n", trim($csv))));
    }

    public function test_migration_imports_legacy_schedule_without_changing_token(): void
    {
        $guru = Guru::factory()->create(['token_qr' => 'existing-token']);
        $jadwal = [1 => ['aktif' => '1', 'jam_masuk' => '07:30', 'jam_pulang' => '15:00']];
        Storage::disk('local')->put('jadwal-guru/'.$guru->id.'.json', json_encode($jadwal));
        $migration = require database_path('migrations/2026_09_24_000001_add_jadwal_to_gurus_table.php');
        $migration->up();
        $this->assertSame($jadwal, $guru->fresh()->jadwal);
        $this->assertSame('existing-token', $guru->fresh()->token_qr);
        Storage::disk('local')->assertExists('jadwal-guru/'.$guru->id.'.json');
    }

    public function test_inactive_teachers_and_days_are_hidden_in_table_and_export(): void
    {
        $guru = Guru::factory()->create(['nama' => 'Guru Tampil']);
        $inactive = Guru::factory()->create(['nama' => 'Guru Disembunyikan', 'status' => 'nonaktif']);
        $empty = Guru::factory()->create(['nama' => 'Guru Tanpa Jadwal']);
        $jadwal = [1 => ['aktif' => '0', 'jam_masuk' => '06:13', 'jam_pulang' => '12:13'], 2 => ['aktif' => 1, 'jam_masuk' => '08:15', 'jam_pulang' => '15:00']];
        app(JadwalGuruService::class)->save($guru, $jadwal);
        app(JadwalGuruService::class)->save($inactive, $jadwal);
        $this->get(route('admin.jadwal.index'))->assertOk()->assertSee('Guru Tampil')
            ->assertSee('Selasa')->assertDontSee('Senin')->assertDontSee($inactive->nama)->assertDontSee($empty->nama)
            ->assertSee('rowspan="1"', false)->assertViewHas('gurus', fn ($rows) => $rows->total() === 1);
        $csv = $this->get(route('admin.jadwal.download'))->assertOk()->streamedContent();
        $this->assertStringContainsString('Guru Tampil', $csv);
        $this->assertStringNotContainsString('Senin', $csv);
        $this->assertStringNotContainsString($inactive->nama, $csv);
        $this->assertStringNotContainsString($empty->nama, $csv);
    }
}
