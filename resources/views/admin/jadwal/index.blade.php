@extends('layouts.admin')
@section('title', 'Jadwal Guru')
@section('content')
<div class="toolbar">
    <div><h2 class="page-title">Jadwal Guru</h2><p class="page-subtitle">Hanya menampilkan guru dan hari jadwal aktif. Perubahan jadwal dilakukan melalui Data Guru.</p></div>
    <a class="btn" href="{{ route('admin.jadwal.download') }}">Unduh Jadwal Semua Guru (CSV)</a>
</div>
<p class="mobile-table-hint">Geser tabel ke samping untuk melihat jadwal dan unduh kartu guru.</p>
<div class="card table-wrap" tabindex="0" role="region" aria-label="Jadwal guru aktif">
<table>
    <thead><tr><th>Nama Guru</th><th>NIP</th><th>Mata Pelajaran</th><th>Status Guru</th><th>Hari</th><th>Status Jadwal</th><th>Batas Masuk</th><th>Mulai Pulang</th><th>Kartu Guru</th></tr></thead>
    <tbody>
    @forelse ($gurus as $guru)
        @php($hariAktif = collect($hari)->filter(fn ($nama, $nomor) => !empty($guru->jadwal[$nomor]['aktif'])))
        @foreach ($hariAktif as $nomor => $nama)
            @php($jadwalHari = $guru->jadwal[$nomor] ?? null)
            <tr>
                @if ($loop->first)
                    <td rowspan="{{ $hariAktif->count() }}">{{ $guru->nama }}</td><td rowspan="{{ $hariAktif->count() }}">{{ $guru->nip }}</td><td rowspan="{{ $hariAktif->count() }}">{{ $guru->mata_pelajaran ?: '-' }}</td><td rowspan="{{ $hariAktif->count() }}">{{ ucfirst($guru->status) }}</td>
                @endif
                <td>{{ $nama }}</td><td>{{ $jadwalHari ? ($jadwalHari['aktif'] ? 'Aktif' : 'Nonaktif') : 'Belum diatur' }}</td><td>{{ $jadwalHari['jam_masuk'] ?? '-' }}</td><td>{{ $jadwalHari['jam_pulang'] ?? '-' }}</td>
                @if ($loop->first)
                    <td rowspan="{{ $hariAktif->count() }}">
                        @if ($guru->token_qr)
                            <a href="{{ route('admin.jadwal.kartu', $guru) }}">Unduh PDF Kartu Guru</a>
                        @else
                            QR belum tersedia
                        @endif
                    </td>
                @endif
            </tr>
        @endforeach
    @empty
        <tr><td colspan="9">Belum ada jadwal guru aktif.</td></tr>
    @endforelse
    </tbody>
</table>
</div>
{{ $gurus->links() }}
@endsection
