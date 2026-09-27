@extends('layouts.admin')
@section('title', 'Rincian Guru')

@section('content')
@include('admin.guru.header', [
    'judul' => 'Rincian Data Guru', 'keterangan' => $guru->nama,
    'tautan' => route('admin.guru.index'), 'tombol' => 'Kembali ke Data Guru',
])
<div class="guru-detail-stack">
<div class="card profile-card">
    <div class="profile-summary">
        <div class="profile-avatar image-avatar">
            @if ($guru->user?->profilePhotoUrl())
                <img src="{{ $guru->user->profilePhotoUrl() }}" alt="Foto {{ $guru->nama }}">
            @else
                {{ mb_strtoupper(mb_substr($guru->nama, 0, 1)) }}
            @endif
        </div>
        <h3>{{ $guru->nama }}</h3>
    </div>
    <dl>
        <div><dt>NIP</dt><dd>{{ $guru->nip }}</dd></div>
        <div><dt>Nama Pengguna</dt><dd>{{ $guru->user?->username ?? '-' }}</dd></div>
        <div><dt>Alamat Surel</dt><dd>{{ $guru->user?->email ?? '-' }}</dd></div>
        <div><dt>Mata Pelajaran</dt><dd>{{ $guru->mata_pelajaran ?? '-' }}</dd></div>
        <div><dt>Status</dt><dd>{{ ucfirst($guru->status) }}</dd></div>
    </dl>
    <a class="btn" href="{{ route('admin.jadwal.kartu', $guru) }}">@include('components.icon', ['name' => 'card']) Unduh PDF Kartu Guru</a>
</div>
<section class="card table-wrap">
    <h3>Jadwal Guru</h3>
    <table><thead><tr><th>Hari</th><th>Status</th><th>Batas Masuk</th><th>Mulai Pulang</th></tr></thead><tbody>
    @foreach ($hari as $nomor => $nama)
        <tr><td>{{ $nama }}</td><td>{{ ($jadwal[$nomor]['aktif'] ?? false) ? 'Aktif' : 'Nonaktif' }}</td><td>{{ $jadwal[$nomor]['jam_masuk'] ?? '-' }}</td><td>{{ $jadwal[$nomor]['jam_pulang'] ?? '-' }}</td></tr>
    @endforeach
    </tbody></table>
    <a class="btn" href="{{ route('admin.guru.edit', $guru) }}">Ubah Data dan Jadwal Guru</a>
</section>
</div>
@endsection
