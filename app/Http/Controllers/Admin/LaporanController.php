<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Presensi;
use App\Services\AdminPresensiChartService;
use App\Services\LaporanService;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function index(Request $request, LaporanService $laporanService)
    {
        $data = $request->validate([
            'periode' => ['nullable', 'in:mingguan,bulanan'],
            'bulan' => ['nullable', 'date_format:Y-m'],
            'tanggal' => ['nullable', 'date_format:Y-m-d'],
        ]);
        $periode = $data['periode'] ?? 'bulanan';
        $tanggal = Carbon::parse($data['tanggal'] ?? today()->toDateString());
        $bulan = $data['bulan'] ?? now()->format('Y-m');
        $mulai = $periode === 'mingguan' ? $tanggal->copy()->startOfWeek() : Carbon::parse($bulan.'-01');
        $selesai = $periode === 'mingguan' ? $mulai->copy()->endOfWeek() : $mulai->copy()->endOfMonth();

        return view('admin.laporan.index', [
            'presensis' => Presensi::with('guru')->whereBetween('tanggal', [$mulai->toDateString(), $selesai->toDateTimeString()])->latest('tanggal')->get(),
            'grafik' => app(AdminPresensiChartService::class)->harian($mulai, $selesai),
            'periode' => $periode,
            'bulan' => $bulan,
            'tanggal' => $tanggal->toDateString(),
            'rentang' => $mulai->format('d/m/Y').' - '.$selesai->format('d/m/Y'),
        ]);
    }

    public function kirim(Request $request, LaporanService $laporanService, NotificationService $notificationService)
    {
        $data = $request->validate([
            'bulan' => ['nullable', 'date_format:Y-m'],
            'catatan' => ['nullable', 'string'],
        ]);

        $bulan = $data['bulan'] ?? now()->format('Y-m');
        $presensis = $laporanService->rekapBulanan($bulan);
        $hadir = $presensis->where('status', 'hadir')->count();
        $pulang = $presensis->whereNotNull('jam_pulang')->count();
        $cuti = $presensis->where('status', 'cuti')->count();

        $pesan = "Admin mengirim laporan presensi bulan {$bulan}. Ringkasan: {$hadir} hadir, {$pulang} sudah presensi pulang, {$cuti} cuti.";
        if (! empty($data['catatan'])) {
            $pesan .= ' Catatan admin: '.$data['catatan'];
        }

        $notificationService->kirimKeRole(
            'kepala_sekolah',
            'Laporan Presensi dari Admin',
            $pesan,
            'laporan_presensi'
        );

        return back()->with('success', 'Laporan presensi berhasil dikirim ke kepala sekolah.');
    }
}
