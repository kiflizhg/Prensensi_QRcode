@extends('layouts.admin')
@section('title', 'Data Guru')
@section('content')

@include('admin.guru.header', [
    'judul' => 'Data Guru', 'keterangan' => 'Identitas, foto profil, akun, dan QR presensi guru.',
    'tautan' => route('admin.guru.create'), 'tombol' => 'Tambah Guru', 'utama' => true,
])
<p class="academic-description">{{ $gurus->total() }} guru terdaftar. Menampilkan {{ $gurus->firstItem() ?? 0 }} sampai {{ $gurus->lastItem() ?? 0 }}.</p>
<div class="guru-card-grid">
@forelse ($gurus as $guru)
    <article class="card guru-identity-card">
        <header class="guru-card-heading">
            @if ($guru->user?->profilePhotoUrl())
                <img class="guru-card-photo" src="{{ $guru->user->profilePhotoUrl() }}" alt="Foto {{ $guru->nama }}">
            @else
                <div class="guru-card-photo" aria-label="Foto belum tersedia">{{ mb_strtoupper(mb_substr($guru->nama, 0, 1)) }}</div>
            @endif
            <div><h3>{{ $guru->nama }}</h3><p>{{ $guru->mata_pelajaran ?: 'Mata pelajaran belum diisi' }}</p></div>
        </header>
        <div class="guru-card-content">
            <dl class="guru-card-details">
                <div><dt>NIP</dt><dd>{{ $guru->nip }}</dd></div>
                <div><dt>Nama Pengguna</dt><dd>{{ $guru->user?->username ?? '-' }}</dd></div>
                <div><dt>Alamat Surel</dt><dd>{{ $guru->user?->email ?? '-' }}</dd></div>
                <div><dt>No. HP</dt><dd>{{ $guru->no_hp ?: '-' }}</dd></div>
                <div><dt>Alamat</dt><dd>{{ $guru->alamat ?: '-' }}</dd></div>
                <div><dt>Status Guru</dt><dd>{{ ucfirst($guru->status) }}</dd></div>
                <div><dt>Status Akun</dt><dd>{{ $guru->user?->is_active ? 'Aktif' : 'Belum aktif' }}</dd></div>
            </dl>
            <details class="guru-card-code"><summary>Lihat kode QR presensi</summary>
                @if ($qrCodes[$guru->id])
                    <img src="data:image/svg+xml;base64,{{ base64_encode($qrCodes[$guru->id]) }}" alt="QR presensi {{ $guru->nama }}">
                    <p>QR Presensi Guru</p>
                @else
                    <p>QR belum tersedia. Lengkapi data guru melalui Ubah.</p>
                @endif
            </details>
            <div class="guru-card-actions">
                <a class="btn ghost" href="{{ route('admin.guru.show', $guru) }}">Rincian</a>
                <a class="btn secondary" href="{{ route('admin.guru.edit', $guru) }}">@include('components.icon', ['name' => 'edit']) Ubah</a>
                <form method="post" action="{{ route('admin.guru.destroy', $guru) }}">
                    @csrf @method('DELETE')
                    <button class="btn danger" type="submit">@include('components.icon', ['name' => 'trash']) Hapus</button>
                </form>
            </div>
        </div>
    </article>
@empty
    <div class="card"><p>Belum ada data guru. Tambahkan guru untuk mulai mengelola akun dan jadwal.</p></div>
@endforelse
</div>
{{ $gurus->links() }}
@endsection
