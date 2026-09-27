<?php

namespace Tests\Feature;

use App\Models\Guru;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class GuruProfileSyncTest extends TestCase
{
    use RefreshDatabase;

    public function test_teacher_profile_updates_same_record_displayed_to_admin(): void
    {
        $this->withoutVite();
        Storage::fake('public');
        $user = User::factory()->create(['role' => 'guru']);
        $guru = Guru::factory()->create(['user_id' => $user->id, 'token_qr' => 'qr-profil-tetap']);
        $original = $guru->getAttributes();
        $password = $user->password;
        $this->actingAs($user)->put(route('guru.profil.update'), [
            'name' => 'Nama Guru Diperbarui', 'username' => 'guru_diperbarui', 'email' => 'baru@example.test',
            'no_hp' => '08123456789', 'alamat' => 'Alamat baru sekolah',
            'profile_photo' => UploadedFile::fake()->image('profil.jpg'),
            'nip' => 'tidak-boleh-berubah', 'status' => 'nonaktif', 'jadwal' => [], 'role' => 'admin',
        ])->assertSessionHasNoErrors();
        $this->assertSame('Nama Guru Diperbarui', $guru->fresh()->nama);
        $this->assertSame('Nama Guru Diperbarui', $user->fresh()->name);
        $this->assertSame($original['nip'], $guru->fresh()->nip);
        $this->assertSame($original['status'], $guru->fresh()->status);
        $this->assertSame($original['token_qr'], $guru->fresh()->token_qr);
        $this->assertSame('guru', $user->fresh()->role);
        $this->assertSame($password, $user->fresh()->password);
        Storage::disk('public')->assertExists($user->fresh()->profile_photo_path);
        $this->actingAs($user->fresh())->get(route('guru.dashboard'))
            ->assertOk()->assertSee($user->fresh()->profilePhotoUrl());
        $this->get(route('guru.profil.index'))->assertOk()->assertSee($user->fresh()->profilePhotoUrl());
        $this->actingAs(User::factory()->create(['role' => 'admin']))->get(route('admin.guru.index'))
            ->assertOk()->assertSee('Nama Guru Diperbarui')->assertSee('guru_diperbarui')
            ->assertSee('08123456789')->assertSee('Alamat baru sekolah')->assertSee('baru@example.test')->assertSee($user->fresh()->profilePhotoUrl());
        $this->put(route('admin.guru.update', $guru), [
            'nama' => 'Nama Guru Diperbarui', 'username' => 'guru_diperbarui', 'nip' => $guru->nip, 'status' => 'aktif',
        ])->assertSessionHasNoErrors();
        $this->assertSame('baru@example.test', $user->fresh()->email);
        $this->get(route('admin.guru.show', $guru))->assertOk()->assertSee($user->fresh()->profilePhotoUrl());
    }

    public function test_teacher_cannot_change_password_through_any_profile_endpoint(): void
    {
        $this->withoutVite();
        $user = User::factory()->create(['role' => 'guru']);
        Guru::factory()->create(['user_id' => $user->id]);
        $password = $user->password;
        $this->actingAs($user)->get(route('guru.profil.index'))->assertOk()
            ->assertDontSee('name="password"', false)->assertDontSee('Ubah Kata Sandi');
        $this->put('/guru/profil/password', ['password' => 'baru'])->assertNotFound();
        $this->put(route('admin.profil.password'), ['password' => 'baru'])->assertForbidden();
        $this->put(route('guru.profil.update'), [
            'name' => $user->name, 'username' => 'guru_saya', 'email' => $user->email,
            'password' => 'baru', 'password_confirmation' => 'baru',
        ])->assertSessionHasErrors('password');
        $this->assertSame($password, $user->fresh()->password);
    }
}
