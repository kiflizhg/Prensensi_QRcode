<?php

namespace Tests\Feature;

use App\Models\Guru;
use App\Models\User;
use App\Services\JadwalGuruService;
use App\Services\PresensiService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class AdminModuleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
        $this->withoutVite();
        $this->travelTo(now()->setDate(2026, 9, 21)->setTime(8, 0));
    }

    private function guru(): Guru
    {
        return Guru::factory()->create([
            'user_id' => User::factory()->create(['role' => 'guru', 'is_active' => true])->id,
            'token_qr' => 'test-token',
        ]);
    }

    private function jadwal(): array
    {
        return array_fill(1, 7, ['aktif' => '1', 'jam_masuk' => '08:30', 'jam_pulang' => '15:00']);
    }

    public function test_admin_password_validation_and_account_isolation(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'password' => 'old-password', 'is_active' => true]);
        $others = collect(['guru', 'kepala_sekolah', 'admin'])->map(fn ($role) => User::factory()->create(['role' => $role]));
        $passwords = $others->pluck('password', 'id');
        $this->actingAs($admin)->get(route('admin.profil.index'))->assertOk();
        $this->put(route('admin.profil.password'), ['current_password' => 'wrong', 'password' => 'new-password', 'password_confirmation' => 'new-password'])->assertSessionHasErrors('current_password');
        $this->assertTrue(Hash::check('old-password', $admin->fresh()->password));
        $this->put(route('admin.profil.password'), ['current_password' => 'old-password', 'password' => 'new-password', 'password_confirmation' => 'different'])->assertSessionHasErrors('password');
        $this->put(route('admin.profil.password'), ['current_password' => 'old-password', 'password' => 'new-password', 'password_confirmation' => 'new-password', 'user_id' => $others->first()->id])->assertSessionHasNoErrors();
        $this->assertTrue(Hash::check('new-password', $admin->fresh()->password));
        foreach ($others as $other) {
            $this->assertSame($passwords[$other->id], $other->fresh()->password);
        }
        $this->post('/logout');
        $this->post('/login', ['login' => $admin->email, 'password' => 'new-password'])->assertSessionHasNoErrors();
        $this->assertAuthenticatedAs($admin);
    }

    public function test_admin_can_manage_schedule_and_invalid_times_are_rejected(): void
    {
        $guru = $this->guru();
        $this->actingAs(User::factory()->create(['role' => 'admin']));
        $this->get(route('admin.jadwal.index'))->assertOk();
        $this->get(route('admin.guru.edit', $guru))->assertOk();
        $jadwal = $this->jadwal();
        $this->put(route('admin.jadwal.update', $guru), ['jadwal' => $jadwal])->assertSessionHasNoErrors();
        $this->assertSame($jadwal, app(JadwalGuruService::class)->get($guru));
        $jadwal[1]['jam_pulang'] = '07:00';
        $this->put(route('admin.jadwal.update', $guru), ['jadwal' => $jadwal])->assertSessionHasErrors('jadwal.1.jam_pulang');
        $this->assertSame('15:00', app(JadwalGuruService::class)->get($guru)[1]['jam_pulang']);
        $jadwal[1]['jam_pulang'] = '16:00';
        $this->put(route('admin.jadwal.update', $guru), ['jadwal' => $jadwal])->assertSessionHasNoErrors();
        $this->assertSame('16:00', app(JadwalGuruService::class)->get($guru)[1]['jam_pulang']);
    }

    public function test_non_admin_cannot_manage_schedule_or_admin_password(): void
    {
        $guru = $this->guru();
        foreach (['guru', 'kepala_sekolah'] as $role) {
            $this->actingAs(User::factory()->create(['role' => $role]));
            $this->get(route('admin.password.edit'))->assertForbidden();
            $this->put(route('admin.profil.password'), [])->assertForbidden();
            $this->get(route('admin.jadwal.index'))->assertForbidden();
            $this->put(route('admin.jadwal.update', $guru), ['jadwal' => $this->jadwal()])->assertForbidden();
        }
    }

    public function test_attendance_requires_active_account_and_schedule(): void
    {
        $guru = $this->guru();
        $service = app(PresensiService::class);
        foreach (['missing', 'inactive_day', 'inactive_account', 'inactive_guru', 'missing_account'] as $case) {
            $jadwal = $this->jadwal();
            if ($case === 'inactive_day') {
                $jadwal[1]['aktif'] = '0';
            }
            if ($case !== 'missing') {
                app(JadwalGuruService::class)->save($guru, $jadwal);
            }
            $guru->user()->update(['is_active' => $case !== 'inactive_account']);
            $guru->update(['status' => $case === 'inactive_guru' ? 'nonaktif' : 'aktif']);
            if ($case === 'missing_account') {
                $guru->update(['user_id' => null]);
            }
            try {
                $service->absenMasuk($guru);
                $this->fail("Attendance accepted for $case");
            } catch (ValidationException $exception) {
                $this->assertArrayHasKey('qr_code', $exception->errors());
            }
        }
        $this->assertDatabaseCount('presensis', 0);
    }

    public function test_scan_uses_admin_times_and_rejects_early_checkout(): void
    {
        $guru = $this->guru();
        app(JadwalGuruService::class)->save($guru, $this->jadwal());
        $service = app(PresensiService::class);
        $presensi = $service->absenDariKartu('test-token');
        $this->assertNull($presensi->keterangan);
        $this->travelTo(now()->setTime(13, 0));
        try {
            $service->absenDariKartu('test-token', 'pulang');
            $this->fail('Early checkout accepted');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('qr_code', $exception->errors());
        }
        $this->assertNull($presensi->fresh()->jam_pulang);
        $this->travelTo(now()->setTime(15, 0));
        $this->assertSame('pulang', $service->absenDariKartu('test-token')->jenis_scan_terpakai);
        $this->assertDatabaseCount('presensis', 1);
    }

    public function test_editing_teacher_keeps_existing_password(): void
    {
        $guru = $this->guru();
        $password = $guru->user->password;
        $this->actingAs(User::factory()->create(['role' => 'admin']))
            ->put(route('admin.guru.update', $guru), ['nama' => 'Nama Baru', 'nip' => '123456789', 'username' => 'guru_baru', 'status' => 'aktif'])
            ->assertSessionHasNoErrors();
        $this->assertSame($password, $guru->user->fresh()->password);
    }

    public function test_admin_can_activate_existing_teacher_without_an_account(): void
    {
        $guru = Guru::factory()->create(['status' => 'nonaktif']);
        $this->actingAs(User::factory()->create(['role' => 'admin']))
            ->put(route('admin.guru.update', $guru), ['nama' => $guru->nama, 'nip' => $guru->nip, 'username' => 'guru_aktif', 'status' => 'aktif', 'password' => '123', 'password_confirmation' => '123'])
            ->assertSessionHasNoErrors();
        $user = $guru->fresh()->user;
        $this->assertTrue($user->is_active);
        $this->assertSame('guru', $user->role);
        $this->assertTrue(Hash::check('123', $user->password));
        $this->assertDatabaseCount('gurus', 1);
    }

    public function test_lateness_uses_updated_schedule_and_first_scan_time(): void
    {
        $guru = $this->guru();
        $jadwal = $this->jadwal();
        $jadwal[1]['jam_masuk'] = '07:45';
        app(JadwalGuruService::class)->save($guru, $jadwal);
        $service = app(PresensiService::class);
        $presensi = $service->absenDariKartu('test-token');
        $this->assertSame('Terlambat scan masuk pukul 08:00.', $presensi->keterangan);
        $this->travelTo(now()->setTime(9, 0));
        $ulang = $service->absenDariKartu('test-token');
        $this->assertSame('08:00:00', $ulang->jam_masuk);
        $this->assertSame($presensi->keterangan, $ulang->keterangan);
    }
}
