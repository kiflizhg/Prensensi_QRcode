@extends('layouts.kepsek')

@section('content')
<section class="page-hero compact">
    <div>
        <span class="page-kicker">Laporan Sekolah</span>
        <h2 class="page-title">Laporan Presensi</h2>
        <p class="page-subtitle">Baca laporan yang dikirim admin dan tinjau rekap presensi bulanan guru.</p>
    </div>
    <a class="btn" href="{{ route('kepsek.laporan.download', ['bulan' => request('bulan', now()->format('Y-m'))]) }}">
        @include('components.icon', ['name' => 'report']) Download Surat PDF
    </a>
</section>

<section class="notification-list">
    <h3 class="section-title">Laporan Masuk</h3>
    @forelse ($laporanMasuk as $laporan)
        <article class="notification-item">
            <div>
                <strong>{{ $laporan->judul }}</strong>
                <span>{{ $laporan->created_at->format('d-m-Y H:i') }}</span>
            </div>
            <p>{{ $laporan->pesan }}</p>
        </article>
    @empty
        <p class="empty-state">Belum ada laporan yang dikirim oleh admin.</p>
    @endforelse
</section>

<h3 class="section-title">Rekap Presensi</h3>
<form method="get" class="card kepsek-filters"><div><label for="bulan">Bulan laporan</label><input id="bulan" type="month" name="bulan" value="{{ request('bulan', now()->format('Y-m')) }}" required></div><button class="btn" type="submit">Tampilkan</button></form>
<div class="kepsek-teachers">
@forelse ($presensis as $presensi)
<article class="card"><h3>{{ $presensi->guru->nama }}</h3><p>{{ $presensi->tanggal->format('d-m-Y') }}</p><span class="status-pill">{{ ucfirst(str_replace('_', ' ', $presensi->status)) }}</span><dl class="kepsek-times"><div><dt>Masuk</dt><dd>{{ $presensi->jam_masuk ?? '—' }}</dd></div><div><dt>Pulang</dt><dd>{{ $presensi->jam_pulang ?? '—' }}</dd></div></dl></article>
@empty
<p class="card empty-state">Belum ada presensi pada bulan ini.</p>
@endforelse
</div>
@endsection
