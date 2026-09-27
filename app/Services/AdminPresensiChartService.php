<?php

namespace App\Services;

use App\Models\Presensi;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class AdminPresensiChartService
{
    public function mingguKerja(): array
    {
        $mulai = today('Asia/Jakarta')->startOfWeek(Carbon::MONDAY);

        return array_map(function (array $hari): array {
            if ($hari['tanggal'] > today('Asia/Jakarta')->toDateString()) {
                $hari['jumlah'] = 0;
            }

            return $hari;
        }, $this->harian($mulai, $mulai->copy()->addDays(4)));
    }

    public function harian(Carbon $mulai, Carbon $selesai): array
    {
        $counts = Presensi::query()
            ->whereBetween('tanggal', [$mulai->toDateString(), $selesai->copy()->endOfDay()->toDateTimeString()])
            ->whereNotNull('jam_masuk')
            ->selectRaw('DATE(tanggal) as hari, COUNT(DISTINCT guru_id) as jumlah')
            ->groupByRaw('DATE(tanggal)')
            ->pluck('jumlah', 'hari');

        $data = [];
        foreach (CarbonPeriod::create($mulai, $selesai) as $tanggal) {
            $data[] = ['tanggal' => $tanggal->toDateString(), 'jumlah' => (int) ($counts[$tanggal->toDateString()] ?? 0)];
        }

        return $data;
    }
}
