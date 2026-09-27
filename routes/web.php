<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\TerminalScanController;
use App\Http\Controllers\ScanController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('welcome', ['page' => \App\Models\PublicPage::content()]))->name('home');

// Public scanner terminal — no auth required
// POST /scan is rate-limited: 60 requests per minute per IP
Route::get('/scan', [ScanController::class, 'index'])->name('scan.index');
Route::post('/scan', [ScanController::class, 'store'])
    ->middleware('throttle:60,1')
    ->name('scan.store');

// Legacy admin scan endpoint — kept intact, still guarded by auth + admin
Route::post('/terminal-scan', [TerminalScanController::class, 'store'])
    ->middleware(['auth', 'admin'])
    ->name('terminal.scan.store');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');
});

Route::post('/logout', [LoginController::class, 'destroy'])->middleware('auth')->name('logout');

Route::get('/dashboard', function () {
    $user = request()->user();

    return match ($user?->role) {
        'admin' => redirect()->route('admin.dashboard'),
        'kepala_sekolah' => redirect()->route('kepsek.dashboard'),
        'guru' => redirect()->route('guru.dashboard'),
        default => redirect('/'),
    };
})->middleware('auth')->name('dashboard');


