@extends('layouts.admin')

@section('title', 'Kelola Guru')

@section('content')
<div class="toolbar">
    <div>
        <span class="page-kicker">Master Data</span>
        <h2 class="page-title">Direktori Guru</h2>
        <p class="page-subtitle">Kelola identitas guru, akun login, status aktif, dan kartu QR presensi.</p>
    </div>
    <a class="btn" href="{{ route('admin.guru.create') }}">@include('components.icon', ['name' => 'users']) Tambah Guru</a>
</div>
<table>
    <thead><tr><th>NIP</th><th>Nama</th><th>Mata Pelajaran</th><th>Alamat</th><th>Status</th><th>Aksi</th></tr></thead>
    <tbody>
        @forelse ($gurus as $guru)
            <tr>
                <td>{{ $guru->nip }}</td>
                <td>{{ $guru->nama }}</td>
                <td>{{ $guru->mata_pelajaran ?? '-' }}</td>
                <td>{{ $guru->alamat ?? '-' }}</td>
                <td>{{ $guru->status }}</td>
                <td>
                    <div class="table-actions">
                        <a class="btn ghost" href="{{ route('admin.guru.show', $guru) }}">Detail</a>
                        <a class="btn secondary" href="{{ route('admin.guru.kartu.show', $guru) }}">@include('components.icon', ['name' => 'card']) QR</a>
                        <a class="btn secondary" href="{{ route('admin.guru.edit', $guru) }}">@include('components.icon', ['name' => 'edit']) Edit</a>
                        <form method="post" action="{{ route('admin.guru.destroy', $guru) }}">
                            @csrf @method('DELETE')
                            <button class="btn danger" type="submit">@include('components.icon', ['name' => 'trash']) Hapus</button>
                        </form>
                    </div>
                </td>
            </tr>
        @empty
            <tr><td colspan="6">Direktori guru masih kosong. Tambahkan data guru untuk mulai menerbitkan kartu QR.</td></tr>
        @endforelse
    </tbody>
</table>
{{ $gurus->links() }}
@endsection
