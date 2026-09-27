@extends('layouts.kepsek')
@section('content')
<section class="page-hero compact"><div><span class="page-kicker">{{ today()->locale('id')->translatedFormat('l, d F Y') }}</span><h1 class="page-title">Ringkasan Sekolah</h1><p class="page-subtitle">Pantau kehadiran guru dan tindak lanjuti pengajuan dalam satu tempat.</p></div></section>
<div class="kepsek-summary"><div><strong>{{ $totalGuruAktif }}</strong><span>Guru aktif</span></div><div><strong>{{ $presensiHariIni }}</strong><span>Presensi hari ini</span></div><div><strong>{{ $pengajuanMenunggu }}</strong><span>Perlu persetujuan</span></div></div>
<a class="btn" href="{{ route('kepsek.monitoring.index') }}">@include('components.icon', ['name' => 'users']) Pantau Kehadiran Guru</a>
<div class="kepsek-shortcuts">
<a class="card" href="{{ route('kepsek.persetujuan.index') }}"><strong>Pengajuan Guru</strong><p>{{ $pengajuanMenunggu }} pengajuan menunggu ditinjau. Buka surat, setujui, atau tolak pengajuan.</p></a>
<a class="card" href="{{ route('kepsek.laporan.index') }}"><strong>Laporan Bulanan</strong><p>Tinjau rekap dan unduh laporan presensi sekolah.</p></a>
<a class="card" href="{{ route('kepsek.pesan.index') }}"><strong>Kirim Arahan</strong><p>Sampaikan informasi kepada guru dan admin sekolah.</p></a>
<a class="card" href="{{ route('kepsek.profil.index') }}"><strong>Profil Saya</strong><p>Perbarui foto, identitas akun, dan kata sandi.</p></a>
</div>
@endsection
