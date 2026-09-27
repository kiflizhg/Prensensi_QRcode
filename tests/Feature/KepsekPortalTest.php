<?php

namespace Tests\Feature;

use App\Models\Guru;
use App\Models\Presensi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KepsekPortalTest extends TestCase
{
    use RefreshDatabase;

    public function test_kepsek_can_edit_profile_change_password_and_logout_from_profile(): void
    {
        $this->withoutVite();
        \Illuminate\Support\Facades\Storage::fake('public');
        $user = User::factory()->create(['role' => 'kepala_sekolah', 'password' => 'old-password', 'is_active' => true]);
        $this->actingAs($user);
        $this->get(route('kepsek.dashboard'))->assertOk()->assertDontSee('action="'.route('logout').'"', false);
        $this->get(route('kepsek.profil.index'))->assertOk()->assertSee('action="'.route('logout').'"', false)->assertSee('name="current_password"', false);
        $data = ['name' => 'Nama Kepsek Baru', 'username' => 'kepsek_baru', 'email' => $user->email];
        $this->put(route('kepsek.profil.update'), $data + ['profile_photo' => \Illuminate\Http\UploadedFile::fake()->image('kepsek.jpg')])->assertSessionHasNoErrors();
        $user->refresh();
        $this->assertSame('Nama Kepsek Baru', $user->name);
        $this->assertSame('kepsek_baru', $user->username);
        \Illuminate\Support\Facades\Storage::disk('public')->assertExists($user->profile_photo_path);
        $this->get(route('kepsek.profil.index'))->assertSee($user->profilePhotoUrl());
        $this->put(route('kepsek.profil.update'), array_replace($data, ['username' => '']))->assertSessionHasErrors('username');
        $other = User::factory()->create(['username' => 'sudah_dipakai']);
        $this->put(route('kepsek.profil.update'), array_replace($data, ['username' => $other->username]))->assertSessionHasErrors('username');
        $this->put(route('kepsek.profil.password'), ['current_password' => 'salah', 'password' => 'new-password', 'password_confirmation' => 'new-password'])->assertSessionHasErrors('current_password');
        $this->put(route('kepsek.profil.password'), ['current_password' => 'old-password', 'password' => 'new-password', 'password_confirmation' => 'new-password'])->assertSessionHasNoErrors();
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('new-password', $user->fresh()->password));
        $this->post(route('logout'))->assertRedirect('/');
        $this->assertGuest();
        $this->post(route('login.store'), ['login' => 'kepsek_baru', 'password' => 'new-password'])->assertSessionHasNoErrors()->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
        // Simulate the browser returning the session ID issued by login.
        $this->withCookie(config('session.cookie'), $user->fresh()->active_session_id)
            ->post(route('logout'))->assertRedirect('/');
        $this->assertNull($user->fresh()->active_session_id);
    }

    public function test_kepsek_can_login_and_open_every_portal_page(): void
    {
        $this->withoutVite();
        $user = User::factory()->create(['role' => 'kepala_sekolah', 'username' => 'kepksek123', 'password' => 'test-password', 'is_active' => true]);
        $this->post(route('login.store'), ['login' => 'kepksek123', 'password' => 'test-password'])->assertSessionHasNoErrors()->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
        $this->get(route('dashboard'))->assertRedirect(route('kepsek.dashboard'));
        foreach (['dashboard', 'monitoring.index', 'persetujuan.index', 'laporan.index', 'pesan.index', 'profil.index'] as $page) {
            $this->get(route('kepsek.'.$page))->assertOk()->assertSee('Menu kepala sekolah')->assertSee('kepsek.css');
        }
        $this->get(route('admin.dashboard'))->assertForbidden();
    }

    public function test_monitoring_includes_unrecorded_teachers_and_filters_name_nip_and_status(): void
    {
        $this->withoutVite();
        $this->actingAs(User::factory()->create(['role' => 'kepala_sekolah']));
        $present = Guru::factory()->create(['nama' => 'Guru Hadir', 'nip' => '111222', 'status' => 'aktif']);
        Guru::factory()->create(['nama' => 'Guru Belum', 'nip' => '333444', 'status' => 'aktif']);
        Guru::factory()->create(['nama' => 'Guru Nonaktif', 'status' => 'nonaktif']);
        Presensi::factory()->create(['guru_id' => $present->id, 'tanggal' => today(), 'status' => 'hadir']);
        $this->get(route('kepsek.monitoring.index'))->assertOk()->assertSee('Guru Hadir')->assertSee('Guru Belum')->assertDontSee('Guru Nonaktif');
        $this->get(route('kepsek.monitoring.index', ['status' => 'belum_presensi']))->assertOk()->assertSee('Guru Belum')->assertDontSee('Guru Hadir');
        $this->get(route('kepsek.monitoring.index', ['q' => '111222']))->assertOk()->assertSee('Guru Hadir')->assertDontSee('Guru Belum');
        $this->get(route('kepsek.monitoring.index', ['q' => 'guru hadir', 'status' => 'hadir']))->assertOk()->assertSee('Guru Hadir')->assertDontSee('Guru Belum');
        $this->get(route('kepsek.monitoring.index', ['q' => 'tidak ditemukan']))->assertOk()->assertSee('Tidak ada guru');
        $this->get(route('kepsek.monitoring.index', ['status' => 'invalid']))->assertSessionHasErrors('status');
        $this->actingAs(User::factory()->create(['role' => 'guru']))->get(route('kepsek.monitoring.index'))->assertForbidden();
    }
}
