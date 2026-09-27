<?php

namespace Tests\Feature;

use App\Models\Guru;
use App\Models\Presensi;
use App\Models\User;
use App\Services\JadwalGuruService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminConsolidationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
        $this->withoutVite();
        $this->travelTo(now()->setDate(2026, 9, 23)->setTime(10, 0));
        $this->actingAs(User::factory()->create(['role' => 'admin']));
    }

    public function test_teacher_account_schedule_and_password_are_managed_together(): void
    {
        $data = ['nama' => 'Guru Sekolah', 'nip' => '123456789', 'username' => 'guru_sekolah', 'status' => 'aktif',
            'password' => 'password-guru', 'password_confirmation' => 'password-guru',
            'jadwal' => array_fill(1, 7, ['aktif' => '1', 'jam_masuk' => '07:30', 'jam_pulang' => '14:00'])];
        $this->post(route('admin.guru.store'), $data)->assertSessionHasNoErrors()->assertRedirect(route('admin.guru.index'));
        $guru = Guru::firstOrFail();
        $this->assertTrue(Hash::check('password-guru', $guru->user->password));
        $this->assertSame($data['jadwal'], app(JadwalGuruService::class)->get($guru));
        $this->get(route('admin.guru.show', $guru))->assertOk()->assertSee('14:00');
        $this->get(route('admin.guru.edit', $guru))->assertOk()->assertSee('Kata Sandi Guru')->assertSee('Jadwal Guru');
        $data['password'] = $data['password_confirmation'] = 'password-baru';
        $data['jadwal'][3]['jam_pulang'] = '15:30';
        $this->put(route('admin.guru.update', $guru), $data)->assertSessionHasNoErrors();
        $this->assertTrue(Hash::check('password-baru', $guru->user->fresh()->password));
        $this->assertSame('15:30', app(JadwalGuruService::class)->get($guru)[3]['jam_pulang']);
        $data['password_confirmation'] = 'tidak-sama';
        $this->put(route('admin.guru.update', $guru), $data)->assertSessionHasErrors('password');
        $data['password_confirmation'] = $data['password'];
        $data['jadwal'][3]['jam_pulang'] = '06:00';
        $this->put(route('admin.guru.update', $guru), $data)->assertSessionHasErrors('jadwal.3.jam_pulang');
        $this->assertSame('15:30', app(JadwalGuruService::class)->get($guru)[3]['jam_pulang']);
    }

    public function test_dashboard_menu_and_chart_use_real_attendance(): void
    {
        $this->get(route('admin.dashboard'))->assertOk()
            ->assertDontSee('Direktori Guru')->assertDontSee('Kelola Jadwal Guru')->assertDontSee('Ubah Password Admin');
        $this->getJson(route('admin.dashboard.grafik'))->assertOk()->assertJsonPath('grafik.2.jumlah', 0);
        Presensi::factory()->for(Guru::factory())->create(['tanggal' => today(), 'jam_masuk' => '07:00:00']);
        Presensi::factory()->for(Guru::factory())->create(['tanggal' => today(), 'jam_masuk' => null, 'status' => 'izin']);
        $this->getJson(route('admin.dashboard.grafik'))->assertOk()->assertJsonPath('grafik.2.jumlah', 1);
        $this->actingAs(User::factory()->create(['role' => 'guru']));
        $this->getJson(route('admin.dashboard.grafik'))->assertForbidden();
    }

    public function test_report_periods_share_the_same_real_data_boundaries(): void
    {
        Presensi::factory()->for(Guru::factory())->create(['tanggal' => '2026-09-21', 'jam_masuk' => '07:00:00']);
        Presensi::factory()->for(Guru::factory())->create(['tanggal' => '2026-09-27', 'jam_masuk' => '07:00:00']);
        Presensi::factory()->for(Guru::factory())->create(['tanggal' => '2026-09-20', 'jam_masuk' => '07:00:00']);
        Presensi::factory()->for(Guru::factory())->create(['tanggal' => '2026-10-01', 'jam_masuk' => '07:00:00']);
        $this->get(route('admin.laporan.index', ['periode' => 'mingguan', 'tanggal' => '2026-09-23']))
            ->assertOk()->assertSee('SMK ISLAM CIPASUNG')->assertSee('report-letterhead')
            ->assertViewHas('presensis', fn ($rows) => $rows->count() === 2)
            ->assertViewHas('grafik', fn ($rows) => count($rows) === 7 && array_sum(array_column($rows, 'jumlah')) === 2);
        $this->get(route('admin.laporan.index', ['periode' => 'bulanan', 'bulan' => '2026-09']))
            ->assertOk()->assertViewHas('presensis', fn ($rows) => $rows->count() === 3)
            ->assertViewHas('grafik', fn ($rows) => count($rows) === 30 && array_sum(array_column($rows, 'jumlah')) === 3);
        $this->get(route('admin.laporan.index', ['bulan' => 'invalid']))->assertSessionHasErrors('bulan');
    }
}
