<?php

namespace Tests\Feature;

use App\Models\Guru;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminGuruPhotoTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_upload_replace_and_keep_teacher_photo(): void
    {
        Storage::fake('public');
        $this->withoutVite();
        $this->actingAs(User::factory()->create(['role' => 'admin']));
        $data = ['nama' => 'Guru Foto', 'nip' => '123456789', 'username' => 'guru_foto', 'status' => 'aktif'];
        $this->post(route('admin.guru.store'), $data + ['password' => 'abc', 'password_confirmation' => 'abc', 'profile_photo' => UploadedFile::fake()->image('foto.jpg')])->assertSessionHasNoErrors();
        $guru = Guru::firstOrFail();
        $firstPath = $guru->user->profile_photo_path;
        Storage::disk('public')->assertExists($firstPath);
        $this->get(route('admin.guru.edit', $guru))->assertOk()->assertSee('multipart/form-data')->assertSee($guru->user->profilePhotoUrl());
        $this->put(route('admin.guru.update', $guru), $data)->assertSessionHasNoErrors();
        $this->assertSame($firstPath, $guru->user->fresh()->profile_photo_path);
        $this->put(route('admin.guru.update', $guru), $data + ['profile_photo' => UploadedFile::fake()->image('baru.png')])->assertSessionHasNoErrors();
        $newPath = $guru->user->fresh()->profile_photo_path;
        $this->assertNotSame($firstPath, $newPath);
        Storage::disk('public')->assertExists($newPath);
        $this->get(route('admin.guru.index'))->assertOk()->assertSee($guru->user->fresh()->profilePhotoUrl());
        $this->get(route('admin.guru.show', $guru))->assertOk()->assertSee($guru->user->fresh()->profilePhotoUrl());
        foreach ([UploadedFile::fake()->create('bad.txt', 10, 'text/plain'), UploadedFile::fake()->image('large.jpg')->size(2049)] as $invalid) {
            $this->put(route('admin.guru.update', $guru), $data + ['profile_photo' => $invalid])->assertSessionHasErrors('profile_photo');
            $this->assertSame($newPath, $guru->user->fresh()->profile_photo_path);
        }
        $this->actingAs($guru->user->fresh());
        $this->get(route('guru.profil.index'))->assertOk()->assertSee($guru->user->fresh()->profilePhotoUrl());
        $this->get(route('guru.dashboard'))->assertOk()->assertSee($guru->user->fresh()->profilePhotoUrl());
    }
}
