@extends('layouts.kepsek')

@section('content')
<section class="page-hero compact">
    <div>
        <span class="page-kicker">Informasi Sekolah</span>
        <h2 class="page-title">Arahan Kepala Sekolah</h2>
        <p class="page-subtitle">Kirim instruksi atau pengumuman resmi kepada admin, guru, atau seluruh pengguna operasional.</p>
    </div>
</section>

<form class="card" method="post" action="{{ route('kepsek.pesan.store') }}">
    @csrf
    <div class="form-grid">
        <div>
            <label>Tujuan</label>
            <select name="tujuan" required>
                <option value="semua">Admin dan Guru</option>
                <option value="admin">Admin</option>
                <option value="guru">Guru</option>
            </select>
        </div>
        <div>
            <label>Judul Arahan</label>
            <input name="judul" value="{{ old('judul') }}" required placeholder="Contoh: Rapat Koordinasi Hari Ini">
        </div>
    </div>
    <label>Isi Arahan</label>
    <textarea name="pesan" rows="5" required placeholder="Tuliskan pesan yang jelas, ringkas, dan siap dibaca penerima.">{{ old('pesan') }}</textarea>
    <button class="btn" type="submit">@include('components.icon', ['name' => 'send']) Kirim Arahan</button>
</form>
@endsection
