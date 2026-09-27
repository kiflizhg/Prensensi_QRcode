@extends('layouts.kepsek')
@section('content')
<section class="page-hero compact"><div><span class="page-kicker">{{ today()->locale('id')->translatedFormat('l, d F Y') }}</span><h1 class="page-title">Monitoring Guru</h1><p class="page-subtitle">Kehadiran hari ini. Diperbarui saat halaman dibuka, pukul {{ now()->format('H:i') }} WIB.</p></div><a class="btn ghost" href="{{ route('kepsek.monitoring.index', request()->only('q', 'status')) }}">Perbarui</a></section>
<div class="kepsek-summary"><div><strong>{{ $ringkasan->sum() }}</strong><span>Guru aktif</span></div><div><strong>{{ $ringkasan->get('hadir', 0) }}</strong><span>Hadir</span></div><div><strong>{{ $ringkasan->get('belum_presensi', 0) }}</strong><span>Belum presensi</span></div></div>
<form class="card kepsek-filters" method="get">
    <div><label for="q">Cari guru</label><input id="q" name="q" value="{{ request('q') }}" placeholder="Nama atau NIP" maxlength="100" type="search"></div>
    <div><label for="status">Status kehadiran</label><select id="status" name="status"><option value="">Semua status</option>@foreach (['hadir' => 'Hadir', 'belum_presensi' => 'Belum presensi', 'izin' => 'Izin', 'sakit' => 'Sakit', 'cuti' => 'Cuti', 'dinas_luar' => 'Dinas luar'] as $value => $label)<option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>@endforeach</select></div><button class="btn" type="submit">Tampilkan</button><a class="btn ghost" href="{{ route('kepsek.monitoring.index') }}">Reset</a>
</form>
<p class="kepsek-result">{{ $presensis->count() }} guru ditampilkan</p>
<div class="kepsek-teachers">
@forelse ($presensis as $presensi)
    <article class="card kepsek-teacher">
        <div class="kepsek-teacher-heading"><div class="kepsek-photo">@if ($presensi->guru->user?->profilePhotoUrl())<img src="{{ $presensi->guru->user->profilePhotoUrl() }}" alt="Foto {{ $presensi->guru->nama }}" loading="lazy">@else{{ mb_strtoupper(mb_substr($presensi->guru->nama, 0, 1)) }}@endif</div><div><h2>{{ $presensi->guru->nama }}</h2><p>NIP {{ $presensi->guru->nip }}</p></div></div>
        <span @class(['status-pill', 'status-empty' => $presensi->status === 'belum_presensi'])>{{ ucfirst(str_replace('_', ' ', $presensi->status)) }}</span>
        <dl class="kepsek-times"><div><dt>Masuk</dt><dd>{{ $presensi->jam_masuk ?? '—' }}</dd></div><div><dt>Pulang</dt><dd>{{ $presensi->jam_pulang ?? '—' }}</dd></div></dl>
    </article>
@empty
    <p class="card empty-state">Tidak ada guru yang sesuai dengan pencarian atau filter ini.</p>
@endforelse
</div>
@endsection
