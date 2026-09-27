<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Services\JadwalGuruService;
use Illuminate\Http\Request;

class JadwalController extends Controller
{
    public function index(Request $request, JadwalGuruService $service)
    {
        $guru = $request->user()->guru;

        return response()->view('guru.jadwal.index', [
            'guru' => $guru,
            'jadwal' => $guru ? $service->get($guru) : [],
            'hari' => JadwalGuruService::HARI,
        ])->header('Cache-Control', 'no-store');
    }
}
