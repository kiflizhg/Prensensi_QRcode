<?php

namespace Tests\Feature;

use App\Models\Guru;
use App\Models\User;
use App\Services\JadwalGuruService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GuruPortalTest extends TestCase
{
    use RefreshDatabase;

    public function test_teacher_reads_own_schedule_and_receives_admin_changes(): void
    {
        $this->withoutVite();
        $this->travelTo(\Carbon\Carbon::parse('2026-09-28 08:00:00', 'Asia/Jakarta'));
        $user = User::factory()->create(['role' => 'guru']);
        $guru = Guru::factory()->create(['user_id' => $user->id, 'nama' => 'Guru Pertama']);
        $other = Guru::factory()->create(['nama' => 'Guru Lain']);
        app(JadwalGuruService::class)->save($other, [1 => ['aktif' => true, 'jam_masuk' => '06:13', 'jam_pulang' => '11:13']]);
        $admin = User::factory()->create(['role' => 'admin']);
        $schedule = array_fill(1, 7, ['aktif' => true, 'jam_masuk' => '07:45', 'jam_pulang' => '14:30']);
        $schedule[7]['aktif'] = false;
        $this->actingAs($admin)->put(route('admin.jadwal.update', $guru), ['jadwal' => $schedule])->assertSessionHasNoErrors();
        $this->actingAs($user)->get(route('guru.jadwal.index', ['guru_id' => $other->id]))->assertOk()
            ->assertSee('Guru Pertama')->assertSee('07:45')->assertSee('14:30')->assertSee('Tidak dijadwalkan')
            ->assertDontSee('Guru Lain')->assertDontSee('06:13');
        $this->get(route('guru.dashboard'))->assertOk()->assertSee('07:45')->assertSee('14:30');
        $schedule[1]['jam_pulang'] = '15:15';
        $this->actingAs($admin)->put(route('admin.jadwal.update', $guru), ['jadwal' => $schedule])->assertSessionHasNoErrors();
        $this->actingAs($user)->get(route('guru.jadwal.index'))->assertOk()->assertSee('15:15');
        $this->put(route('admin.jadwal.update', $guru), ['jadwal' => $schedule])->assertForbidden();
        $this->put(route('guru.jadwal.index'), ['jadwal' => $schedule])->assertStatus(405);
    }

    public function test_all_teacher_pages_share_layout_and_only_teacher_navigation(): void
    {
        $this->withoutVite();
        $user = User::factory()->create(['role' => 'guru']);
        Guru::factory()->create(['user_id' => $user->id]);
        $this->actingAs($user);
        foreach (['guru.dashboard', 'guru.jadwal.index', 'guru.presensi.index', 'guru.pengajuan.index', 'guru.riwayat.index', 'guru.notifikasi.index', 'guru.profil.index'] as $route) {
            $this->get(route($route))->assertOk()->assertSee('Portal Akademik Guru')->assertSee('Jadwal Saya')
                ->assertSee('data-school-clock', false)->assertSee('admin-workspace', false)
                ->assertDontSee('href="'.route('admin.dashboard').'"', false)
                ->assertDontSee('href="'.route('admin.guru.index').'"', false)
                ->assertDontSee('Today Attendance')->assertDontSee('Leave Request');
        }
        $this->get(route('guru.jadwal.index'))->assertSee('Jadwal belum ditetapkan');
    }

    public function test_schedule_handles_missing_teacher_and_enforces_role(): void
    {
        $this->withoutVite();
        $this->get(route('guru.jadwal.index'))->assertRedirect(route('login'));
        $this->actingAs(User::factory()->create(['role' => 'admin']))->get(route('guru.jadwal.index'))->assertForbidden();
        $this->actingAs(User::factory()->create(['role' => 'guru']))->get(route('guru.jadwal.index'))
            ->assertOk()->assertSee('Akun Anda belum terhubung');
    }
}
