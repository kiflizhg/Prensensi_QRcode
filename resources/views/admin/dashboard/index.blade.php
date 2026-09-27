@extends('layouts.admin')

@section('title', 'Beranda Administrasi')

@section('content')
<div class="portal-page-heading">
    <div><p class="portal-eyebrow">ADMINISTRASI SEKOLAH</p><h1>Beranda Administrasi</h1></div>
    <p class="academic-description">Ringkasan administrasi dan kehadiran guru SMK Islam Cipasung.</p>
</div>
<section class="portal-metrics" aria-label="Ringkasan data sekolah">
    <a class="portal-metric" href="{{ route('admin.guru.index') }}"><span class="metric-icon">@include('components.icon', ['name' => 'users'])</span><div><span>Guru Terdaftar</span><strong data-summary="guru">{{ $totalGuru }}</strong><small>Data guru dalam sistem</small></div><span class="metric-arrow" aria-hidden="true">&nearr;</span></a>
    <a class="portal-metric" href="{{ route('admin.presensi.index') }}"><span class="metric-icon">@include('components.icon', ['name' => 'check'])</span><div><span>Presensi Hari Ini</span><strong data-summary="presensi">{{ $presensiHariIni }}</strong><small>Catatan kehadiran hari ini</small></div><span class="metric-arrow" aria-hidden="true">&nearr;</span></a>
    <div class="portal-metric"><span class="metric-icon metric-icon-gold">@include('components.icon', ['name' => 'send'])</span><div><span>Pengajuan Menunggu</span><strong data-summary="pengajuan">{{ $pengajuanMenunggu }}</strong><small>Menunggu keputusan kepala sekolah</small></div></div>
</section>
<div class="portal-overview">
    @include('admin.partials.grafik', ['judulGrafik' => 'Kehadiran Lima Hari Kerja', 'refreshUrl' => route('admin.dashboard.grafik')])
    <section class="portal-shortcuts" aria-labelledby="shortcut-heading">
        <div class="portal-section-heading"><span class="portal-eyebrow">RUANG KERJA</span><h2 id="shortcut-heading">Akses cepat</h2><p>Keperluan administrasi sehari-hari.</p></div>
        <a href="{{ route('admin.guru.create') }}"><span class="shortcut-icon">@include('components.icon', ['name' => 'users'])</span><span><strong>Tambah data guru</strong><small>Daftarkan akun dan jadwal guru</small></span><span aria-hidden="true">&rarr;</span></a>
        <a href="{{ route('admin.jadwal.index') }}"><span class="shortcut-icon">@include('components.icon', ['name' => 'clock'])</span><span><strong>Jadwal & kartu guru</strong><small>Lihat jadwal dan unduh kartu QR</small></span><span aria-hidden="true">&rarr;</span></a>
        <a href="{{ route('admin.laporan.index') }}"><span class="shortcut-icon">@include('components.icon', ['name' => 'report'])</span><span><strong>Laporan kehadiran</strong><small>Tinjau rekap mingguan dan bulanan</small></span><span aria-hidden="true">&rarr;</span></a>
        <a href="{{ route('admin.notifikasi.index') }}"><span class="shortcut-icon">@include('components.icon', ['name' => 'bell'])</span><span><strong>Arahan kepala sekolah</strong><small>Baca pesan dan informasi sekolah</small></span><span aria-hidden="true">&rarr;</span></a>
    </section>
</div>
@endsection
