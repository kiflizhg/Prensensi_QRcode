<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\GuruController;
use App\Http\Controllers\Admin\JadwalGuruController;
use App\Http\Controllers\Admin\LaporanController;
use App\Http\Controllers\Admin\NotifikasiController;
use App\Http\Controllers\Admin\PresensiController;
use App\Http\Controllers\Admin\ProfilPenggunaController;
use App\Http\Controllers\ProfileController;
use App\Models\Guru;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function (): void {
    Route::get('/konten-publik', [\App\Http\Controllers\Admin\PublicPageController::class, 'edit'])->name('public-page.edit');
    Route::put('/konten-publik', [\App\Http\Controllers\Admin\PublicPageController::class, 'update'])->name('public-page.update');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/grafik', [DashboardController::class, 'grafik'])->name('dashboard.grafik');
    Route::get('/guru/{guru}/kartu', fn (Guru $guru) => redirect()->route('admin.guru.index'))->name('guru.kartu.show');
    Route::get('/guru/{guru}/kartu/download', [JadwalGuruController::class, 'kartu'])->name('guru.kartu.download');
    Route::resource('guru', GuruController::class);
    Route::get('/jadwal-guru', [JadwalGuruController::class, 'index'])->name('jadwal.index');
    Route::get('/jadwal-guru/download', [JadwalGuruController::class, 'download'])->name('jadwal.download');
    Route::get('/jadwal-guru/{guru}/kartu.pdf', [JadwalGuruController::class, 'kartu'])->name('jadwal.kartu');
    Route::get('/jadwal-guru/{guru}/edit', fn (Guru $guru) => redirect()->route('admin.guru.edit', $guru))->name('jadwal.edit');
    Route::put('/jadwal-guru/{guru}', [JadwalGuruController::class, 'update'])->name('jadwal.update');
    Route::get('/presensi', [PresensiController::class, 'index'])->name('presensi.index');

    // Scanner launcher — no scan form, no directory table
    Route::redirect('/scan', '/admin/jadwal-guru')->name('scan.index');

    // Teacher directory — independent from scanner
    Route::redirect('/direktori-guru', '/admin/guru')->name('direktori_guru.index');
    Route::get('/direktori-guru/{user}/edit', function (User $user) {
        return $user->isGuru() && $user->guru
            ? redirect()->route('admin.guru.edit', $user->guru)
            : redirect()->route('admin.guru.index');
    })->name('direktori_guru.edit');
    Route::put('/direktori-guru/{user}', [ProfilPenggunaController::class, 'update'])->name('direktori_guru.update');

    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::post('/laporan/kirim', [LaporanController::class, 'kirim'])->name('laporan.kirim');
    Route::get('/notifikasi', [NotifikasiController::class, 'index'])->name('notifikasi.index');
    Route::get('/profil', [ProfileController::class, 'index'])->name('profil.index');
    Route::redirect('/ubah-password', '/admin/profil')->name('password.edit');
    Route::put('/profil', [ProfileController::class, 'update'])->name('profil.update');
    Route::put('/profil/password', [ProfileController::class, 'updatePassword'])->name('profil.password');
});
