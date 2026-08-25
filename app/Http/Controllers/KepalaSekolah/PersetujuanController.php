<?php

namespace App\Http\Controllers\KepalaSekolah;

use App\Http\Controllers\Controller;
use App\Models\Pengajuan;
use App\Services\PresensiService;
use Illuminate\Support\Facades\Storage;

class PersetujuanController extends Controller
{
    public function index()
    {
        return view('kepsek.persetujuan.index', [
            'pengajuans' => Pengajuan::with('guru')->latest()->get(),
        ]);
    }

    public function setujui(Pengajuan $pengajuan, PresensiService $presensiService)
    {
        \Illuminate\Support\Facades\DB::transaction(function () use ($pengajuan, $presensiService) {
            $pengajuan->update([
                'status' => 'disetujui',
                'disetujui_oleh' => auth()->id(),
                'diproses_pada' => now(),
            ]);

            $presensiService->sinkronkanPengajuanDisetujui($pengajuan);
        });

        return back()->with('success', 'Pengajuan disetujui.');
    }

    public function tolak(Pengajuan $pengajuan)
    {
        $pengajuan->update([
            'status' => 'ditolak',
            'disetujui_oleh' => auth()->id(),
            'diproses_pada' => now(),
        ]);

        return back()->with('success', 'Pengajuan ditolak.');
    }

    public function lampiran(Pengajuan $pengajuan)
    {
        abort_if(! $pengajuan->lampiran || ! Storage::disk('local')->exists($pengajuan->lampiran), 404);

        return response()->file(Storage::disk('local')->path($pengajuan->lampiran));
    }
}
