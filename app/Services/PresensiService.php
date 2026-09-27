<?php

namespace App\Services;

use App\Models\Guru;
use App\Models\Pengajuan;
use App\Models\Presensi;
use Carbon\CarbonPeriod;
use Illuminate\Validation\ValidationException;

class PresensiService
{
    public function absenMasuk(Guru $guru, ?string $kodeQr = null, string $metodeInput = 'Scan Alat'): Presensi
    {
        $waktuScan = now();
        $jadwal = app(JadwalGuruService::class)->untukPresensi($guru, $waktuScan);
        $presensi = Presensi::where('guru_id', $guru->id)
            ->whereDate('tanggal', $waktuScan->toDateString())
            ->first() ?? new Presensi([
                'guru_id' => $guru->id,
                'tanggal' => $waktuScan->toDateString(),
            ]);

        if (! $presensi->jam_masuk) {
            $presensi->jam_masuk = $waktuScan->format('H:i:s');
        }

        $presensi->status = 'hadir';
        $presensi->metode_input = $metodeInput;

        if ($presensi->jam_masuk > $jadwal['jam_masuk'].':00') {
            $presensi->keterangan = 'Terlambat scan masuk pukul '.substr($presensi->jam_masuk, 0, 5).'.';
        }

        if ($kodeQr) {
            $presensi->kode_qr = $kodeQr;
        }

        $presensi->save();

        return $presensi;
    }

    public function absenPulang(Guru $guru, ?string $kodeQr = null, string $metodeInput = 'Scan Alat'): Presensi
    {
        $waktuScan = now();
        $jadwal = app(JadwalGuruService::class)->untukPresensi($guru, $waktuScan);
        if ($waktuScan->format('H:i:s') < $jadwal['jam_pulang'].':00') {
            throw ValidationException::withMessages(['qr_code' => 'Presensi pulang tersedia mulai pukul '.$jadwal['jam_pulang'].'.']);
        }
        $presensi = Presensi::where('guru_id', $guru->id)
            ->whereDate('tanggal', $waktuScan->toDateString())
            ->first();

        if (! $presensi || ! $presensi->jam_masuk) {
            throw ValidationException::withMessages([
                'qr_code' => 'Presensi pulang tidak dapat disimpan sebelum presensi masuk.',
            ]);
        }

        $updates = [
            'kode_qr' => $kodeQr ?: $presensi->kode_qr,
            'metode_input' => $metodeInput,
        ];

        if (! $presensi->jam_pulang) {
            $updates['jam_pulang'] = $waktuScan->format('H:i:s');
        }

        $presensi->update($updates);

        return $presensi;
    }

    public function absenDariKartu(string $token, string $jenis = 'otomatis'): Presensi
    {
        $guru = Guru::where('token_qr', $token)->where('status', 'aktif')->first();

        if (! $guru) {
            throw ValidationException::withMessages([
                'qr_code' => 'Kartu QR guru tidak valid atau guru sudah nonaktif.',
            ]);
        }

        $izin = $this->pengajuanDisetujuiHariIni($guru);

        if ($izin) {
            $this->sinkronkanPengajuanDisetujui($izin);

            throw ValidationException::withMessages([
                'qr_code' => 'Guru ini memiliki pengajuan '.$izin->jenis.' yang sudah disetujui hari ini, sehingga tidak dicatat hadir dari scan alat.',
            ]);
        }

        if ($jenis === 'otomatis') {
            $jenis = $this->tentukanJenisScanOtomatis($guru);
        }

        $presensi = $jenis === 'pulang'
            ? $this->absenPulang($guru, $token)
            : $this->absenMasuk($guru, $token);

        $presensi->setAttribute('jenis_scan_terpakai', $jenis);

        return $presensi;
    }

    public function sinkronkanPengajuanDisetujui(Pengajuan $pengajuan): void
    {
        $presensiData = [];
        $now = now()->toDateTimeString();

        foreach (CarbonPeriod::create($pengajuan->tanggal_mulai, $pengajuan->tanggal_selesai) as $tanggal) {
            $presensiData[] = [
                'guru_id' => $pengajuan->guru_id,
                'tanggal' => $tanggal->toDateString(),
                'status' => $pengajuan->jenis,
                'metode_input' => 'Form Web',
                'keterangan' => $pengajuan->alasan,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        foreach (array_chunk($presensiData, 100) as $chunk) {
            Presensi::upsert(
                $chunk,
                ['guru_id', 'tanggal'],
                ['status', 'metode_input', 'keterangan', 'updated_at']
            );
        }
    }

    private function tentukanJenisScanOtomatis(Guru $guru): string
    {
        $waktuScan = now();
        $jadwal = app(JadwalGuruService::class)->untukPresensi($guru, $waktuScan);
        $presensi = Presensi::where('guru_id', $guru->id)
            ->whereDate('tanggal', $waktuScan->toDateString())
            ->first();

        if ($presensi?->jam_masuk && ! $presensi->jam_pulang && $waktuScan->format('H:i:s') >= $jadwal['jam_pulang'].':00') {
            return 'pulang';
        }

        return 'masuk';
    }

    private function pengajuanDisetujuiHariIni(Guru $guru): ?Pengajuan
    {
        return Pengajuan::where('guru_id', $guru->id)
            ->where('status', 'disetujui')
            ->whereDate('tanggal_mulai', '<=', today())
            ->whereDate('tanggal_selesai', '>=', today())
            ->first();
    }
}
