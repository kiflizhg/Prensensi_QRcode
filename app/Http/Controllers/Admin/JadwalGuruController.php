<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Services\JadwalGuruService;
use App\Services\QRCodeService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class JadwalGuruController extends Controller
{
    public function index()
    {
        $gurus = $this->guruDenganJadwalAktif()->with('user')->orderBy('nama')->orderBy('id')->paginate(10);

        return view('admin.jadwal.index', ['gurus' => $gurus, 'hari' => JadwalGuruService::HARI]);
    }

    public function download()
    {
        return response()->streamDownload(function () {
            $file = fopen('php://output', 'w');
            fwrite($file, "\xEF\xBB\xBF");
            fputcsv($file, ['Nama Guru', 'NIP', 'Mata Pelajaran', 'Status Guru', 'Hari', 'Status Jadwal', 'Batas Masuk', 'Mulai Pulang'], ',', '"', '');
            foreach ($this->guruDenganJadwalAktif()->orderBy('nama')->orderBy('id')->cursor() as $guru) {
                foreach (JadwalGuruService::HARI as $nomor => $hari) {
                    $jadwal = $guru->jadwal[$nomor] ?? null;
                    if (empty($jadwal['aktif'])) {
                        continue;
                    }
                    $row = [$guru->nama, $guru->nip, $guru->mata_pelajaran ?? '-', ucfirst($guru->status), $hari,
                        $jadwal ? ($jadwal['aktif'] ? 'Aktif' : 'Nonaktif') : 'Belum diatur',
                        $jadwal['jam_masuk'] ?? '-', $jadwal['jam_pulang'] ?? '-'];
                    $row = array_map(fn ($value) => preg_match('/^[\s]*[=+@-]/u', (string) $value) ? "'".$value : $value, $row);
                    fputcsv($file, $row, ',', '"', '');
                }
            }
            fclose($file);
        }, 'jadwal-semua-guru-'.now()->format('Y-m-d').'.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    private function guruDenganJadwalAktif(): Builder
    {
        return Guru::where('status', 'aktif')->where(function ($query) {
            foreach (array_keys(JadwalGuruService::HARI) as $nomor) {
                $query->orWhereIn("jadwal->{$nomor}->aktif", [true, 1, '1']);
            }
        });
    }

    public function kartu(Guru $guru, QRCodeService $qrCodeService)
    {
        abort_unless($guru->token_qr, 422, 'QR guru belum tersedia. Lengkapi data guru melalui Data Guru.');
        $guru->loadMissing('user');
        $photoImage = null;
        $photoPath = $guru->user?->profile_photo_path;
        if ($photoPath && Storage::disk('public')->exists($photoPath)) {
            $photoImage = 'data:'.Storage::disk('public')->mimeType($photoPath).';base64,'.base64_encode(Storage::disk('public')->get($photoPath));
        }

        return Pdf::loadView('admin.jadwal.kartu-pdf', [
            'guru' => $guru->loadMissing('user'),
            'photoImage' => $photoImage,
            'qrImage' => 'data:image/svg+xml;base64,'.base64_encode($qrCodeService->guruSvg($guru)),
            'logoImage' => 'data:image/jpeg;base64,'.base64_encode(file_get_contents(public_path('assets/images/logo.jpeg'))),
        ])->setPaper('a5', 'landscape')->download('kartu-guru-'.$guru->id.'.pdf');
    }

    public function update(Request $request, Guru $guru, JadwalGuruService $service)
    {
        $rules = ['jadwal' => ['required', 'array:1,2,3,4,5,6,7']];
        foreach (JadwalGuruService::HARI as $nomor => $nama) {
            $rules["jadwal.$nomor"] = ['required', 'array:aktif,jam_masuk,jam_pulang'];
            $rules["jadwal.$nomor.aktif"] = ['required', 'boolean'];
            $rules["jadwal.$nomor.jam_masuk"] = ['required', 'date_format:H:i'];
            $rules["jadwal.$nomor.jam_pulang"] = ['required', 'date_format:H:i', "after:jadwal.$nomor.jam_masuk"];
        }
        $data = $request->validate($rules);
        $service->save($guru, $data['jadwal']);

        return back()->with('success', 'Jadwal guru berhasil diperbarui.');
    }
}
