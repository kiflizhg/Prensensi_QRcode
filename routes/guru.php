<?php

use App\Http\Controllers\Guru\DashboardController;
use App\Http\Controllers\Guru\JadwalController;
use App\Http\Controllers\Guru\NotifikasiController;
use App\Http\Controllers\Guru\PengajuanController;
use App\Http\Controllers\Guru\PresensiController;
use App\Http\Controllers\Guru\RiwayatController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::prefix('guru')->name('guru.')->middleware(['auth', 'guru'])->group(function (): void {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/jadwal', [JadwalController::class, 'index'])->name('jadwal.index');
    Route::get('/presensi', [PresensiController::class, 'index'])->name('presensi.index');
    Route::get('/pengajuan', [PengajuanController::class, 'index'])->name('pengajuan.index');
    Route::post('/pengajuan', [PengajuanController::class, 'store'])->name('pengajuan.store');
    Route::get('/riwayat', [RiwayatController::class, 'index'])->name('riwayat.index');
    Route::get('/notifikasi', [NotifikasiController::class, 'index'])->name('notifikasi.index');
    Route::get('/profil', [ProfileController::class, 'index'])->name('profil.index');
    Route::put('/profil', [ProfileController::class, 'update'])->name('profil.update');
});
