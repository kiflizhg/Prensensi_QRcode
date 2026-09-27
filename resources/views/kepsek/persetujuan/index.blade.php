@extends('layouts.kepsek')
@section('content')
<section class="page-hero compact"><div><span class="page-kicker">Pengajuan Guru</span><h1 class="page-title">Persetujuan Pengajuan</h1><p class="page-subtitle">Tinjau alasan dan lampiran sebelum memberikan keputusan.</p></div></section>
<div class="kepsek-teachers">
@forelse ($pengajuans as $pengajuan)
<article class="card">
    <h2 class="compact-title">{{ $pengajuan->guru->nama }}</h2>
    <span class="status-pill">{{ ucfirst(str_replace('_', ' ', $pengajuan->jenis)) }} · {{ ucfirst($pengajuan->status) }}</span>
    <p>{{ $pengajuan->tanggal_mulai->format('d-m-Y') }} sampai {{ $pengajuan->tanggal_selesai->format('d-m-Y') }}</p>
    <p>{{ $pengajuan->alasan }}</p>
    @if ($pengajuan->lampiran)<a class="btn ghost" href="{{ route('kepsek.persetujuan.lampiran', $pengajuan) }}" target="_blank" rel="noopener">Lihat Surat</a>@endif
    @if ($pengajuan->status === 'menunggu')
    <div class="kepsek-decision">
        <form method="post" action="{{ route('kepsek.persetujuan.setujui', $pengajuan) }}">@csrf<button class="btn" type="submit">Setujui</button></form>
        <form method="post" action="{{ route('kepsek.persetujuan.tolak', $pengajuan) }}">@csrf<button class="btn danger" type="submit">Tolak</button></form>
    </div>
    @endif
</article>
@empty
<p class="card empty-state">Belum ada pengajuan yang perlu ditinjau.</p>
@endforelse
</div>
@endsection
