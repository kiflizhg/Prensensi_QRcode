<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreGuruRequest;
use App\Http\Requests\UpdateGuruRequest;
use App\Models\Guru;
use App\Models\User;
use App\Services\JadwalGuruService;
use App\Services\QRCodeService;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class GuruController extends Controller
{
    public function index(QRCodeService $qrCodeService)
    {
        $gurus = Guru::with('user')->latest()->paginate(10);
        $qrCodes = [];
        foreach ($gurus as $guru) {
            $qrCodes[$guru->id] = $guru->token_qr ? $qrCodeService->guruSvg($guru) : null;
        }

        return view('admin.guru.index', [
            'gurus' => $gurus,
            'qrCodes' => $qrCodes,
        ]);
    }

    public function create()
    {
        return view('admin.guru.form', ['guru' => new Guru, 'jadwal' => [], 'hari' => JadwalGuruService::HARI]);
    }

    public function store(StoreGuruRequest $request)
    {
        DB::transaction(function () use ($request): void {
            $data = $request->validated();

            $user = User::create([
                'name' => $data['nama'],
                'username' => $data['username'],
                'email' => $this->emailFromUsername($data['username']),
                'password' => $data['password'],
                'role' => 'guru',
                'is_active' => $data['status'] === 'aktif',
            ]);

            $guru = Guru::create([
                ...Arr::except($data, ['username', 'password', 'jadwal', 'profile_photo']),
                'user_id' => $user->id,
            ]);

            app(QRCodeService::class)->ensureGuruToken($guru);
            if (isset($data['jadwal'])) {
                app(JadwalGuruService::class)->save($guru, $data['jadwal']);
            }
            $this->saveProfilePhoto($request, $user);
        });

        return redirect()->route('admin.guru.index')->with('success', 'Data guru dan akun login berhasil ditambahkan.');
    }

    public function show(Guru $guru)
    {
        return view('admin.guru.show', ['guru' => $guru, 'jadwal' => app(JadwalGuruService::class)->get($guru), 'hari' => JadwalGuruService::HARI]);
    }

    public function edit(Guru $guru)
    {
        return view('admin.guru.form', ['guru' => $guru, 'jadwal' => app(JadwalGuruService::class)->get($guru), 'hari' => JadwalGuruService::HARI]);
    }

    public function update(UpdateGuruRequest $request, Guru $guru)
    {
        DB::transaction(function () use ($request, $guru): void {
            $data = $request->validated();

            $user = $guru->user ?: User::create([
                'name' => $data['nama'],
                'username' => $data['username'],
                'email' => $this->emailFromUsername($data['username']),
                'password' => $data['password'],
                'role' => 'guru',
                'is_active' => $data['status'] === 'aktif',
            ]);

            $userData = [
                'name' => $data['nama'],
                'username' => $data['username'],
                'role' => 'guru',
                'is_active' => $data['status'] === 'aktif',
            ];

            if (isset($data['password']) && $data['password'] !== '') {
                $userData['password'] = $data['password'];
            }
            $user->update($userData);

            $guru->update([
                ...Arr::except($data, ['username', 'password', 'jadwal', 'profile_photo']),
                'user_id' => $user->id,
            ]);

            app(QRCodeService::class)->ensureGuruToken($guru);
            if (isset($data['jadwal'])) {
                app(JadwalGuruService::class)->save($guru, $data['jadwal']);
            }
            $this->saveProfilePhoto($request, $user);
        });

        return redirect()->route('admin.guru.index')->with('success', 'Data guru berhasil diperbarui.');
    }

    public function destroy(Guru $guru)
    {
        DB::transaction(function () use ($guru): void {
            $user = $guru->user;

            $guru->delete();
            $user?->delete();
        });

        return redirect()->route('admin.guru.index')->with('success', 'Data guru berhasil dihapus.');
    }

    private function saveProfilePhoto(Request $request, User $user): void
    {
        if (! $request->hasFile('profile_photo')) {
            return;
        }

        $oldPath = $user->profile_photo_path;
        $path = $request->file('profile_photo')->store('profile-photos', 'public');
        if (! $path) {
            throw ValidationException::withMessages(['profile_photo' => 'Foto gagal disimpan. Silakan coba kembali.']);
        }
        try {
            $user->update(['profile_photo_path' => $path]);
        } catch (\Throwable $exception) {
            Storage::disk('public')->delete($path);
            throw $exception;
        }
        if ($oldPath) {
            DB::afterCommit(fn () => Storage::disk('public')->delete($oldPath));
        }
    }

    private function emailFromUsername(string $username): string
    {
        return strtolower($username).'@sma-cipasung.local';
    }
}
