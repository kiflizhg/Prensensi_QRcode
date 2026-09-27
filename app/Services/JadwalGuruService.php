<?php

namespace App\Services;

use App\Models\Guru;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

class JadwalGuruService
{
    public const HARI = [1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu', 7 => 'Minggu'];

    public static function rules(): array
    {
        $rules = ['jadwal' => ['sometimes', 'array:1,2,3,4,5,6,7']];
        foreach (self::HARI as $nomor => $nama) {
            $rules["jadwal.$nomor"] = ['required_with:jadwal', 'array:aktif,jam_masuk,jam_pulang'];
            $rules["jadwal.$nomor.aktif"] = ['required_with:jadwal', 'boolean'];
            $rules["jadwal.$nomor.jam_masuk"] = ['required_with:jadwal', 'date_format:H:i'];
            $rules["jadwal.$nomor.jam_pulang"] = ['required_with:jadwal', 'date_format:H:i', "after:jadwal.$nomor.jam_masuk"];
        }

        return $rules;
    }

    public function get(Guru $guru): array
    {
        return $guru->exists ? ($guru->fresh()->jadwal ?? []) : [];
    }

    public function save(Guru $guru, array $jadwal): void
    {
        if (! $guru->forceFill(['jadwal' => $jadwal])->save()) {
            throw ValidationException::withMessages(['jadwal' => 'Jadwal gagal disimpan. Silakan coba kembali.']);
        }
    }

    public function untukPresensi(Guru $guru, Carbon $waktu): array
    {
        $user = $guru->user()->first();
        if ($guru->status !== 'aktif' || ! $user?->isGuru() || ! $user->is_active) {
            throw ValidationException::withMessages(['qr_code' => 'Akun guru belum diaktifkan oleh Admin.']);
        }

        $jadwal = $this->get($guru)[$waktu->dayOfWeekIso] ?? null;
        if (! $jadwal || ! $jadwal['aktif']) {
            throw ValidationException::withMessages(['qr_code' => 'Jadwal guru untuk hari ini belum diaktifkan oleh Admin.']);
        }

        return $jadwal;
    }
}
