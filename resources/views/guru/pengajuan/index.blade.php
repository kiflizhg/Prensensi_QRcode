@extends('layouts.guru')
@section('title', 'Pengajuan Kehadiran')

@section('content')
<section class="page-hero compact">
    <div>
        <span class="page-kicker">Pengajuan Guru</span>
        <h1 class="page-title">Pengajuan Kehadiran</h1>
        <p class="page-subtitle">Ajukan izin, sakit, cuti, atau dinas luar dengan rentang tanggal dan lampiran pendukung.</p>
    </div>
</section>
<form class="card" method="post" action="{{ route('guru.pengajuan.store') }}" enctype="multipart/form-data">
    @csrf
    <div class="form-grid">
        <div><label for="jenis">Jenis Pengajuan</label><select id="jenis" name="jenis">@foreach (['izin' => 'Izin', 'sakit' => 'Sakit', 'cuti' => 'Cuti', 'dinas_luar' => 'Dinas Luar'] as $nilai => $nama)<option value="{{ $nilai }}" @selected(old('jenis', 'izin') === $nilai)>{{ $nama }}</option>@endforeach</select></div>
        <div><label for="lampiran">Lampiran Pendukung</label><input id="lampiran" type="file" name="lampiran"></div>
        <div><label for="tanggal_mulai">Tanggal Mulai</label><input id="tanggal_mulai" type="date" name="tanggal_mulai" value="{{ old('tanggal_mulai') }}" required></div>
        <div><label for="tanggal_selesai">Tanggal Selesai</label><input id="tanggal_selesai" type="date" name="tanggal_selesai" value="{{ old('tanggal_selesai') }}" required></div>
    </div>
    <label for="alasan">Alasan Pengajuan</label><textarea id="alasan" name="alasan" rows="4" required placeholder="Tuliskan alasan secara jelas dan singkat">{{ old('alasan') }}</textarea>
    <button class="btn" type="submit">@include('components.icon', ['name' => 'send']) Kirim Pengajuan</button>
</form>
<h3 class="section-title">Riwayat Pengajuan</h3>
<div class="table-wrap teacher-mobile-table" tabindex="0" role="region" aria-label="Pengajuan Kehadiran"><table>
    <thead><tr><th>Jenis</th><th>Tanggal</th><th>Status</th></tr></thead>
    <tbody>
        @forelse ($pengajuans as $pengajuan)
            <tr><td data-label="Jenis">{{ ucfirst(str_replace('_', ' ', $pengajuan->jenis)) }}</td><td data-label="Tanggal">{{ $pengajuan->tanggal_mulai->format('d-m-Y') }} s.d. {{ $pengajuan->tanggal_selesai->format('d-m-Y') }}</td><td data-label="Status">{{ $pengajuan->status }}</td></tr>
        @empty
            <tr><td colspan="3">Belum ada pengajuan yang tercatat.</td></tr>
        @endforelse
    </tbody>
</table></div>
@endsection
