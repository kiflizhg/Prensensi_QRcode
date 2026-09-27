@extends('layouts.admin')
@section('title', 'Laporan Presensi')

@section('content')
<style>
.report-letterhead{display:flex;align-items:center;justify-content:center;gap:24px;text-align:center;border-bottom:4px double #174b65;padding:12px 0 22px;margin-bottom:24px}
.report-letterhead img{width:80px;height:80px;object-fit:contain}.report-letterhead h2{margin:4px 0;font-size:26px;color:#174b65}.report-letterhead p{margin:4px 0}
.report-document-title{text-align:center;margin-bottom:24px}.report-document-title h3{margin:0 0 8px}
@media print{
    @page{size:A4 landscape;margin:14mm}
    .sidebar,.navbar,.topbar,nav,.toolbar,form,.report-print-button,.sidebar-foot{display:none!important}
    .shell,.content{display:block!important;margin:0!important;padding:0!important;width:100%!important;max-width:none!important}
    body,.card{background:white!important;box-shadow:none!important}.card{border:0!important;padding:0!important}
    .report-letterhead{break-inside:avoid}.report-letterhead h2{font-size:22pt}
    table{width:100%!important}thead{display:table-header-group}tr{break-inside:avoid}
    .table-wrap{overflow:visible!important}[data-chart-bars]{min-width:0!important;gap:3px!important}[data-chart-bars]>div{min-width:0!important}
}
</style>
<div class="toolbar">
    <div>
        <span class="page-kicker">Rekapitulasi Kehadiran</span>
        <h2 class="page-title">Laporan Presensi</h2>
        <p class="page-subtitle">Tinjau kehadiran mingguan atau bulanan dan kirimkan laporan kepada kepala sekolah.</p>
    </div>
</div>

<form class="card" method="get" action="{{ route('admin.laporan.index') }}">
    <div class="form-grid">
        <div><label for="periode">Periode</label><select id="periode" name="periode"><option value="bulanan" @selected($periode === 'bulanan')>Bulanan</option><option value="mingguan" @selected($periode === 'mingguan')>Mingguan</option></select></div>
        <div><label for="bulan">Bulan</label><input id="bulan" type="month" name="bulan" value="{{ $bulan }}"></div>
        <div><label for="tanggal">Tanggal dalam Minggu</label><input id="tanggal" type="date" name="tanggal" value="{{ $tanggal }}"></div>
    </div>
    <button class="btn" type="submit">Tampilkan Laporan</button>
</form>

<form class="card report-send-form" method="post" action="{{ route('admin.laporan.kirim') }}">
    @csrf
    <div class="form-grid">
        <div>
            <label>Bulan Laporan</label>
            <input type="month" name="bulan" value="{{ request('bulan', now()->format('Y-m')) }}">
        </div>
        <div>
            <label>Catatan Laporan</label>
            <input name="catatan" value="{{ old('catatan') }}" placeholder="Tambahkan konteks singkat untuk kepala sekolah">
        </div>
    </div>
    <button class="btn" type="submit">@include('components.icon', ['name' => 'send']) Kirim ke Kepala Sekolah</button>
</form>

<section class="card report-document">
    <header class="report-letterhead">
        <img src="{{ asset('assets/images/logo.jpeg') }}" alt="Logo SMK Islam Cipasung">
        <div>
            <p>Hargai waktu, hadir tepat waktu, berkarya sepenuh hati.</p>
            <h2>SMK ISLAM CIPASUNG</h2>
            <p>Laporan Presensi Guru</p>
        </div>
    </header>
    <div class="report-document-title"><h3>LAPORAN PRESENSI {{ strtoupper($periode) }}</h3><p>Periode: {{ $rentang }}</p></div>
    <button class="btn report-print-button" type="button" onclick="window.print()">Cetak / Simpan PDF Laporan</button>
    @include('admin.partials.grafik', ['judulGrafik' => 'Grafik Presensi '.ucfirst($periode)])
    <div class="table-wrap">@include('admin.presensi.table')</div>
</section>
@endsection
