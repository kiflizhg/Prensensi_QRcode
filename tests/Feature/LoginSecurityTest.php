<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class LoginSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_third_failure_locks_login_for_two_hours_even_with_correct_password(): void
    {
        $user = User::factory()->create(['username' => 'teacher', 'password' => 'correct-password', 'role' => 'guru', 'is_active' => true]);
        for ($i = 0; $i < 3; $i++) {
            $this->post('/login', ['login' => 'wrong-'.$i, 'password' => 'wrong'])->assertSessionHasErrors('login');
        }
        $this->post('/login', ['login' => 'teacher', 'password' => 'correct-password'])->assertSessionHasErrors('login');
        $this->assertGuest();
        $this->travel(119)->minutes();
        $this->post('/login', ['login' => 'teacher', 'password' => 'correct-password'])->assertSessionHasErrors('login');
        $this->assertGuest();
        $this->travel(2)->minutes();
        $this->post('/login', ['login' => 'teacher', 'password' => 'correct-password'])->assertSessionHasNoErrors()->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_sql_injection_cannot_authenticate_and_success_resets_failures(): void
    {
        User::factory()->create(['username' => 'teacher', 'password' => 'correct-password', 'role' => 'guru', 'is_active' => true]);
        $this->post('/login', ['login' => "' OR 1=1 --", 'password' => 'wrong'])->assertSessionHasErrors('login');
        $this->assertGuest();
        $this->assertDatabaseCount('users', 1);
        $this->post('/login', ['login' => 'teacher', 'password' => 'correct-password'])->assertSessionHasNoErrors();
        $this->assertSame(0, RateLimiter::attempts('login-failures:'.hash('sha256', '127.0.0.1')));
    }
}
