<?php

namespace App\Http\Controllers\KepalaSekolah;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\Presensi;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;

class MonitoringController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate(['q' => ['nullable', 'string', 'max:100'], 'status' => ['nullable', 'in:hadir,belum_presensi,izin,sakit,cuti,dinas_luar']]);
        $presensis = $this->presensiSemuaGuruHariIni();
        $ringkasan = $presensis->countBy('status');
        if (! empty($filters['q'])) {
            $search = mb_strtolower($filters['q']);
            $presensis = $presensis->filter(fn ($item) => str_contains(mb_strtolower($item->guru->nama.' '.$item->guru->nip), $search));
        }
        if (! empty($filters['status'])) {
            $presensis = $presensis->where('status', $filters['status']);
        }
        return view('kepsek.monitoring.index', [
            'presensis' => $presensis,
            'ringkasan' => $ringkasan,
        ]);
    }

    private function presensiSemuaGuruHariIni(): Collection
    {
        return Guru::where('status', 'aktif')
            ->with(['user', 'presensis' => fn ($query) => $query->whereDate('tanggal', today())])
            ->orderBy('nama')
            ->get()
            ->map(function (Guru $guru): Presensi {
                $presensi = $guru->presensis->first() ?: new Presensi([
                    'tanggal' => today(),
                    'status' => 'belum_presensi',
                ]);

                $presensi->setRelation('guru', $guru);

                return $presensi;
            });
    }
}
