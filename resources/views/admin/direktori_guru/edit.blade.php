@extends('layouts.admin')

@section('title', 'Ubah Profil Pengguna')

@section('content')
<section class="page-hero compact">
    <div>
        <span class="page-kicker">Ubah Profil</span>
        <h2 class="page-title">Ubah Profil {{ $user->role === 'kepala_sekolah' ? 'Kepala Sekolah' : 'Guru' }}</h2>
        <p class="page-subtitle">Perbarui identitas, status akun, dan foto profil yang akan terlihat di halaman profil pengguna.</p>
    </div>
    <a class="btn ghost" href="{{ route('admin.direktori_guru.index') }}">Kembali</a>
</section>

<form class="card profile-edit-form" method="post" action="{{ route('admin.direktori_guru.update', $user) }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="profile-photo-editor">
        <div class="profile-avatar image-avatar">
            @if ($user->profilePhotoUrl())
                <img src="{{ $user->profilePhotoUrl() }}" alt="Foto {{ $user->guru?->nama ?? $user->name }}">
            @else
                {{ strtoupper(substr($user->guru?->nama ?? $user->name, 0, 1)) }}
            @endif
        </div>
        <div>
            <label>Foto Profil</label>
            <input type="file" name="profile_photo" accept="image/png,image/jpeg,image/webp">
            <p class="field-hint">Format JPG, PNG, atau WebP. Maksimal 2 MB.</p>
        </div>
    </div>

    <div class="form-grid">
        <div>
            <label>Nama Profil</label>
            <input name="name" value="{{ old('name', $user->guru?->nama ?? $user->name) }}" required>
        </div>
        <div>
            <label>Nama Pengguna</label>
            <input name="username" value="{{ old('username', $user->username) }}" placeholder="contoh: guru001">
        </div>
        <div>
            <label>Alamat Surel</label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}" required>
        </div>
        <div>
            <label>Status Akun</label>
            <select name="is_active" required>
                <option value="1" @selected(old('is_active', $user->is_active ? '1' : '0') === '1')>Aktif</option>
                <option value="0" @selected(old('is_active', $user->is_active ? '1' : '0') === '0')>Nonaktif</option>
            </select>
        </div>
    </div>

    @if ($user->isGuru())
        <h3 class="section-title">Data Guru</h3>
        <div class="form-grid">
            <div>
                <label>NIP</label>
                <input name="nip" value="{{ old('nip', $user->guru?->nip) }}" required>
            </div>
            <div>
                <label>Mata Pelajaran</label>
                <input name="mata_pelajaran" value="{{ old('mata_pelajaran', $user->guru?->mata_pelajaran) }}">
            </div>
            <div>
                <label>No HP</label>
                <input name="no_hp" value="{{ old('no_hp', $user->guru?->no_hp) }}">
            </div>
            <div>
                <label>Status Guru</label>
                <select name="status" required>
                    <option value="aktif" @selected(old('status', $user->guru?->status) === 'aktif')>Aktif</option>
                    <option value="nonaktif" @selected(old('status', $user->guru?->status) === 'nonaktif')>Nonaktif</option>
                </select>
            </div>
        </div>
        <label>Alamat</label>
        <textarea name="alamat" rows="4" placeholder="Alamat lengkap guru">{{ old('alamat', $user->guru?->alamat) }}</textarea>
    @endif

    <button class="btn" type="submit">@include('components.icon', ['name' => 'check']) Simpan Perubahan Profil</button>
</form>
@endsection
