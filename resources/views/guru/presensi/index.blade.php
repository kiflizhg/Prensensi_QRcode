@extends('layouts.guru')
@section('title', 'Presensi Saya')

@section('content')
<section class="page-hero compact">
    <div>
        <span class="page-kicker">Kehadiran Harian</span>
        <h1 class="page-title">Ringkasan Presensi Hari Ini</h1>
        <p class="page-subtitle">Informasi presensi masuk dan pulang akan muncul setelah kartu QR dipindai di terminal sekolah.</p>
    </div>
</section>

<div class="stats">
    <div class="card stat-card">
        @include('components.icon', ['name' => 'check'])
        <span>Status Hari Ini</span>
        <strong>{{ $presensiHariIni?->status ? ucfirst(str_replace('_', ' ', $presensiHariIni->status)) : 'Belum' }}</strong>
    </div>
    <div class="card stat-card">
        @include('components.icon', ['name' => 'clock'])
        <span>Jam Masuk</span>
        <strong>{{ $presensiHariIni?->jam_masuk ?? '-' }}</strong>
    </div>
    <div class="card stat-card">
        @include('components.icon', ['name' => 'clock'])
        <span>Jam Pulang</span>
        <strong>{{ $presensiHariIni?->jam_pulang ?? '-' }}</strong>
    </div>
</div>

<div class="card">
    <p class="empty-state">Presensi dicatat melalui terminal QR sekolah. Jika berhalangan hadir, ajukan izin, sakit, cuti, atau dinas luar melalui menu Pengajuan.</p>
</div>
@endsection
