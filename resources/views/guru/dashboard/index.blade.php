@extends('layouts.guru')
@section('title', 'Beranda Guru')
@section('content')
<header class="page-hero compact">
    <div><span class="page-kicker">Layanan Guru</span><h1 class="page-title">Beranda Guru</h1><p class="page-subtitle">{{ $guru?->nama ?? auth()->user()->name }}, berikut ringkasan jadwal dan kehadiran Anda.</p></div>
</header>
@if (! $guru)
    <p class="empty-state">Akun Anda belum terhubung dengan data guru. Hubungi admin sekolah.</p>
@elseif ($guru->status !== 'aktif' || ! auth()->user()->is_active)
    <p class="alert alert-danger">Akun atau status guru belum aktif. Hubungi admin sekolah.</p>
@endif
<aside class="teacher-guidance">@include('components.icon', ['name' => 'card'])<div><strong>Presensi menggunakan kartu QR</strong><p>Pindai kartu Anda di terminal sekolah saat datang dan pulang. Periksa hasilnya melalui menu Presensi.</p></div></aside>
<section class="portal-metrics teacher-metrics" aria-label="Ringkasan kehadiran saya">
    <a class="portal-metric" href="{{ route('guru.presensi.index') }}"><span class="metric-icon">@include('components.icon', ['name' => 'check'])</span><div><span>Presensi Hari Ini</span><strong>{{ ucfirst(str_replace('_', ' ', $presensiHariIni?->status ?? 'belum presensi')) }}</strong><small>Lihat catatan masuk dan pulang</small></div></a>
    <a class="portal-metric" href="{{ route('guru.jadwal.index') }}"><span class="metric-icon">@include('components.icon', ['name' => 'clock'])</span><div><span>Jadwal Hari Ini</span><strong>{{ $jadwalHariIni ? (!empty($jadwalHariIni['aktif']) ? 'Aktif' : 'Tidak dijadwalkan') : 'Belum diatur' }}</strong><small>{{ !empty($jadwalHariIni['aktif']) ? 'Batas masuk '.$jadwalHariIni['jam_masuk'].' · Mulai pulang '.$jadwalHariIni['jam_pulang'].' WIB' : 'Jadwal ditetapkan admin sekolah' }}</small></div></a>
    <a class="portal-metric" href="{{ route('guru.pengajuan.index') }}"><span class="metric-icon">@include('components.icon', ['name' => 'send'])</span><div><span>Total Pengajuan</span><strong>{{ $jumlahPengajuan }}</strong><small>Lihat pengajuan dan hasil persetujuan</small></div></a>
</section>
<section class="portal-shortcuts teacher-shortcuts" aria-labelledby="teacher-services">
    <div class="portal-section-heading"><span class="portal-eyebrow">LAYANAN SEKOLAH</span><h2 id="teacher-services">Apa yang ingin Anda lakukan?</h2><p>Jadwal dan data kehadiran terhubung dengan administrasi sekolah.</p></div>
    <a href="{{ route('guru.jadwal.index') }}"><span class="shortcut-icon">@include('components.icon', ['name' => 'clock'])</span><span><strong>Jadwal Saya</strong><small>Lihat hari dan jam presensi yang ditetapkan admin</small></span></a>
    <a href="{{ route('guru.pengajuan.index') }}"><span class="shortcut-icon">@include('components.icon', ['name' => 'send'])</span><span><strong>Ajukan Izin / Sakit</strong><small>Termasuk cuti dan dinas luar; ditinjau kepala sekolah</small></span></a>
    <a href="{{ route('guru.riwayat.index') }}"><span class="shortcut-icon">@include('components.icon', ['name' => 'report'])</span><span><strong>Riwayat Presensi</strong><small>Periksa catatan kehadiran pribadi</small></span></a>
    <a href="{{ route('guru.notifikasi.index') }}"><span class="shortcut-icon">@include('components.icon', ['name' => 'bell'])</span><span><strong>Arahan Kepala Sekolah</strong><small>Baca informasi dan arahan sekolah</small></span></a>
</section>
@endsection
