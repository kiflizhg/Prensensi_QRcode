@extends('layouts.admin')

@section('title', $guru->exists ? 'Ubah Guru' : 'Tambah Guru')

@section('content')
@include('admin.guru.header', [
    'judul' => $guru->exists ? 'Ubah Data Guru' : 'Tambah Data Guru',
    'keterangan' => 'Lengkapi identitas, akun, dan jadwal presensi guru.',
    'tautan' => route('admin.guru.index'), 'tombol' => 'Kembali ke Data Guru',
])
<form class="card guru-form" method="post" enctype="multipart/form-data" action="{{ $guru->exists ? route('admin.guru.update', $guru) : route('admin.guru.store') }}">
    @csrf
    @if ($guru->exists) @method('PUT') @endif
    <h2 class="guru-section-title">Identitas Guru</h2>
    <div class="profile-photo-editor guru-photo-editor">
        @if ($guru->user?->profilePhotoUrl())
            <img class="guru-form-photo" src="{{ $guru->user->profilePhotoUrl() }}" alt="Foto {{ $guru->nama }}" width="80" height="96">
        @else
            <div class="guru-form-photo guru-photo-placeholder" aria-label="Foto belum tersedia">@include('components.icon', ['name' => 'users'])</div>
        @endif
        <div>
            <label for="profile_photo">Foto Profil Guru</label>
            <input id="profile_photo" type="file" name="profile_photo" accept="image/jpeg,image/png,image/webp">
            <p class="field-hint">JPG, PNG, atau WebP. Maksimal 2 MB.@if ($guru->exists) Kosongkan untuk mempertahankan foto saat ini.@endif</p>
            @error('profile_photo')<div class="form-error">{{ $message }}</div>@enderror
        </div>
    </div>
    <div class="form-grid">
        <div><label>NIP</label><input name="nip" value="{{ old('nip', $guru->nip) }}" required></div>
        <div><label>Nama</label><input name="nama" value="{{ old('nama', $guru->nama) }}" required></div>
        <div><label>Mata Pelajaran</label><input name="mata_pelajaran" value="{{ old('mata_pelajaran', $guru->mata_pelajaran) }}"></div>
        <div><label>No HP</label><input name="no_hp" value="{{ old('no_hp', $guru->no_hp) }}"></div>
        <div><label>Status</label><select name="status"><option value="aktif">Aktif</option><option value="nonaktif" @selected(old('status', $guru->status) === 'nonaktif')>Nonaktif</option></select></div>
    </div>
    <label>Alamat Domisili</label><textarea name="alamat" placeholder="Alamat lengkap guru">{{ old('alamat', $guru->alamat) }}</textarea>
    <section class="guru-account-section" aria-labelledby="guru-account-title">
        <h2 id="guru-account-title" class="guru-section-title">Akun Guru</h2>
        <div class="guru-account-name"><div><label>Nama Pengguna</label><input name="username" value="{{ old('username', $guru->user?->username) }}" required placeholder="contoh: guru001"></div></div>
        <div class="form-grid">
        <div><label for="password">Kata Sandi Guru</label><input id="password" type="password" name="password" autocomplete="new-password" @required(! $guru->user_id)>@if ($guru->user_id)<small>Kosongkan jika kata sandi tidak diubah.</small>@endif</div>
        <div><label for="password_confirmation">Konfirmasi Kata Sandi Guru</label><input id="password_confirmation" type="password" name="password_confirmation" autocomplete="new-password" @required(! $guru->user_id)></div>
        </div>
    </section>
    @include('admin.guru.jadwal')
    <div class="guru-form-actions">
        <button class="btn" type="submit">@include('components.icon', ['name' => 'check']) Simpan Data Guru</button>
        <a class="btn ghost" href="{{ route('admin.guru.index') }}">Batal</a>
    </div>
</form>
@endsection
