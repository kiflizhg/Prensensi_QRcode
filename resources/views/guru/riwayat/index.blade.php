@extends('layouts.guru')
@section('title', 'Riwayat Presensi')

@section('content')
<section class="page-hero compact">
    <div>
        <span class="page-kicker">Arsip Kehadiran</span>
        <h1 class="page-title">Riwayat Presensi</h1>
        <p class="page-subtitle">Lihat catatan presensi Anda dari hari ke hari sebagai arsip kehadiran pribadi.</p>
    </div>
</section>
<div class="table-wrap teacher-mobile-table" tabindex="0" role="region" aria-label="Riwayat Presensi"><table>
    <thead><tr><th>Tanggal</th><th>Masuk</th><th>Pulang</th><th>Status</th></tr></thead>
    <tbody>
        @forelse ($presensis as $presensi)
            <tr><td data-label="Tanggal">{{ $presensi->tanggal->format('d-m-Y') }}</td><td data-label="Masuk">{{ $presensi->jam_masuk ?? '-' }}</td><td data-label="Pulang">{{ $presensi->jam_pulang ?? '-' }}</td><td data-label="Status">{{ ucfirst(str_replace('_', ' ', $presensi->status)) }}</td></tr>
        @empty
            <tr><td colspan="4">Riwayat presensi belum tersedia.</td></tr>
        @endforelse
    </tbody>
</table></div>
@endsection
