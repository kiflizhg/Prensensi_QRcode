<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\Pengajuan;
use App\Models\Presensi;
use App\Services\AdminPresensiChartService;

class DashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard.index', [
            'totalGuru' => Guru::count(),
            'presensiHariIni' => Presensi::whereDate('tanggal', today())->count(),
            'pengajuanMenunggu' => Pengajuan::where('status', 'menunggu')->count(),
            'grafik' => app(AdminPresensiChartService::class)->mingguKerja(),
        ]);
    }

    public function grafik(AdminPresensiChartService $service)
    {
        return response()->json([
            'grafik' => $service->mingguKerja(),
            'waktu' => now('Asia/Jakarta')->toIso8601String(),
            'ringkasan' => [
                'guru' => Guru::count(),
                'presensi' => Presensi::whereDate('tanggal', today())->count(),
                'pengajuan' => Pengajuan::where('status', 'menunggu')->count(),
            ],
        ])->header('Cache-Control', 'no-store');
    }
}
