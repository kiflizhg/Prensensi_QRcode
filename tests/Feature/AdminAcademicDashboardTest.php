<?php

namespace Tests\Feature;

use App\Models\Guru;
use App\Models\Presensi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAcademicDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_five_day_chart_keeps_current_week_on_weekend_and_advances_on_monday(): void
    {
        $this->withoutVite();
        $this->actingAs(User::factory()->create(['role' => 'admin']));
        foreach (['2026-09-26', '2026-09-27', '2026-09-28', '2027-01-01'] as $day) {
            $this->travelTo(\Carbon\Carbon::parse($day.' 00:00:01', 'Asia/Jakarta'));
            $monday = today()->startOfWeek(\Carbon\Carbon::MONDAY);
            $this->getJson(route('admin.dashboard.grafik'))->assertOk()->assertJsonCount(5, 'grafik')
                ->assertJsonPath('grafik.0.tanggal', $monday->toDateString())
                ->assertJsonPath('grafik.4.tanggal', $monday->copy()->addDays(4)->toDateString())
                ->assertJsonPath('waktu', now()->toIso8601String());
        }
    }

    public function test_chart_counts_arrivals_only_and_daily_summary_changes_at_midnight(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));
        $this->travelTo(\Carbon\Carbon::parse('2026-09-25 23:59:59', 'Asia/Jakarta'));
        Presensi::factory()->for(Guru::factory())->create(['tanggal' => today(), 'jam_masuk' => '07:00:00']);
        Presensi::factory()->for(Guru::factory())->create(['tanggal' => today(), 'jam_masuk' => null, 'status' => 'izin']);
        $this->getJson(route('admin.dashboard.grafik'))->assertJsonPath('grafik.4.jumlah', 1)->assertJsonPath('ringkasan.presensi', 2);
        $this->travelTo(now()->addSeconds(2));
        $this->getJson(route('admin.dashboard.grafik'))->assertJsonPath('grafik.4.jumlah', 1)->assertJsonPath('ringkasan.presensi', 0);
    }

    public function test_all_admin_pages_render_in_indonesian(): void
    {
        $this->withoutVite();
        $this->actingAs(User::factory()->create(['role' => 'admin']));
        foreach (['admin.dashboard', 'admin.guru.index', 'admin.guru.create', 'admin.jadwal.index', 'admin.presensi.index', 'admin.laporan.index', 'admin.notifikasi.index', 'admin.profil.index'] as $route) {
            $this->get(route($route))->assertOk()->assertSee('data-school-clock', false)
                ->assertDontSee('Master Data')->assertDontSee('My Profile')->assertDontSee('Download');
        }
        $this->assertSame('Sebelumnya', __('pagination.previous'));
        $this->assertSame('Menampilkan', __('Showing'));
    }
}
