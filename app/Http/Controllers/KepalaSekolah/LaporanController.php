<?php

namespace App\Http\Controllers\KepalaSekolah;

use App\Http\Controllers\Controller;
use App\Services\LaporanService;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class LaporanController extends Controller
{
    public function index(Request $request, LaporanService $laporanService)
    {
        $request->validate(['bulan' => ['nullable', 'date_format:Y-m']]);
        return view('kepsek.laporan.index', [
            'presensis' => $laporanService->rekapBulanan($request->query('bulan')),
            'laporanMasuk' => $request->user()
                ->notifikasis()
                ->where('tipe', 'laporan_presensi')
                ->latest()
                ->get(),
        ]);
    }

    public function download(Request $request, LaporanService $laporanService)
    {
        $request->validate(['bulan' => ['nullable', 'date_format:Y-m']]);
        $bulan = $request->query('bulan', now()->format('Y-m'));
        $tanggal = Carbon::parse($bulan.'-01');
        $presensis = $laporanService->rekapBulanan($bulan);

        $html = view('kepsek.laporan.pdf', [
            'presensis' => $presensis,
            'bulanLabel' => $tanggal->translatedFormat('F Y'),
            'tanggalSurat' => now()->translatedFormat('d F Y'),
            'kepsek' => $request->user(),
            'logoData' => $this->imageDataUri(public_path('assets/images/logo.jpeg')),
            'rekap' => [
                'hadir' => $presensis->where('status', 'hadir')->count(),
                'izin' => $presensis->where('status', 'izin')->count(),
                'sakit' => $presensis->where('status', 'sakit')->count(),
                'cuti' => $presensis->where('status', 'cuti')->count(),
                'dinas_luar' => $presensis->where('status', 'dinas_luar')->count(),
                'total' => $presensis->count(),
            ],
        ])->render();

        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);

        $pdf = new Dompdf($options);
        $pdf->loadHtml($html);
        $pdf->setPaper('A4', 'portrait');
        $pdf->render();

        return response($pdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="surat-laporan-presensi-'.$bulan.'.pdf"',
        ]);
    }

    private function imageDataUri(string $path): ?string
    {
        if (! file_exists($path)) {
            return null;
        }

        return 'data:image/jpeg;base64,'.base64_encode(file_get_contents($path));
    }
}
