@extends('layouts.admin')
@section('title', 'Presensi Guru')

@section('content')
<section class="page-hero compact">
    <div>
        <span class="page-kicker">Kehadiran Harian</span>
        <h2 class="page-title">Data Presensi Guru</h2>
        <p class="page-subtitle">Pantau histori masuk, pulang, status kehadiran, dan metode pencatatan presensi.</p>
    </div>
</section>
<p class="mobile-table-hint">Geser tabel ke samping untuk melihat semua kolom.</p>
<div class="table-wrap" tabindex="0" role="region" aria-label="Data presensi guru">
    @include('admin.presensi.table')
</div>
{{ $presensis->links() }}
@endsection
