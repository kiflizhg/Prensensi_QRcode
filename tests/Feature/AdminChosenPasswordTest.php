<?php

namespace Tests\Feature;

use App\Models\Guru;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminChosenPasswordTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_must_choose_password_and_can_use_short_password_or_zero(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));
        $data = ['nama' => 'Guru Sekolah', 'nip' => '987654321', 'username' => 'guru_baru', 'status' => 'aktif'];
        $this->post(route('admin.guru.store'), $data)->assertSessionHasErrors('password');
        $this->assertDatabaseCount('gurus', 0);
        $this->post(route('admin.guru.store'), $data + ['password' => '123', 'password_confirmation' => '123'])->assertSessionHasNoErrors();
        $guru = Guru::firstOrFail();
        $this->assertTrue(Hash::check('123', $guru->user->password));
        $this->put(route('admin.guru.update', $guru), $data + ['password' => '0', 'password_confirmation' => '0'])->assertSessionHasNoErrors();
        $this->assertTrue(Hash::check('0', $guru->user->fresh()->password));
        $this->put(route('admin.guru.update', $guru), $data + ['password' => 'abc', 'password_confirmation' => 'xyz'])->assertSessionHasErrors('password');
        $this->assertTrue(Hash::check('0', $guru->user->fresh()->password));
        $this->put(route('admin.guru.update', $guru), $data)->assertSessionHasNoErrors();
        $this->assertTrue(Hash::check('0', $guru->user->fresh()->password));
    }
}
