@extends('layouts.admin')
@section('title', 'Konten Publik')
@section('content')
<section class="page-hero compact"><div><span class="page-kicker">Pengelolaan Website</span><h1 class="page-title">Konten Publik</h1><p class="page-subtitle">Isi setiap bagian sesuai urutan beranda. Perubahan langsung tampil setelah disimpan.</p></div><a class="btn ghost" href="{{ route('home') }}" target="_blank" rel="noopener">Lihat Beranda</a></section>
<form class="public-editor" method="post" action="{{ route('admin.public-page.update') }}" enctype="multipart/form-data">
@csrf
@method('PUT')
@php
$sections = [
 ['title' => '1. Sambutan Kepala Sekolah', 'note' => 'Tampil paling atas bersama foto kepala sekolah.', 'fields' => ['principal_name' => 'Nama Kepala Sekolah', 'principal_bio' => 'Teks Sambutan', 'principal_caption' => 'Caption Foto Kepala Sekolah', 'quote' => 'Kata Bijak'], 'photo' => 'principal_photo', 'remove' => 'remove_photo'],
 ['title' => '2. Visi dan Misi', 'note' => 'Masukkan visi dan misi resmi sekolah.', 'fields' => ['vision' => 'Visi Sekolah', 'mission' => 'Misi Sekolah']],
 ['title' => '3. Konten dan Foto Sekolah', 'note' => 'Tambahkan penjelasan website atau informasi sekolah, lengkap dengan foto dan caption.', 'fields' => ['title' => 'Judul Konten', 'description' => 'Teks Konten', 'content_caption' => 'Caption Foto Konten'], 'photo' => 'content_photo', 'remove' => 'remove_content_photo'],
 ['title' => '4. Panduan Guru', 'note' => 'Tulis satu langkah per baris. Unggah gambar untuk mengganti ilustrasi bawaan; hapus gambar unggahan untuk memakai ilustrasi bawaan kembali.', 'fields' => ['guide' => 'Tata Cara Menggunakan Website', 'guide_caption' => 'Caption Ilustrasi Panduan'], 'photo' => 'guide_photo', 'remove' => 'remove_guide_photo'],
];
@endphp
@foreach($sections as $section)
<section class="card public-editor-section"><h2 class="compact-title">{{ $section['title'] }}</h2><p class="form-note">{{ $section['note'] }}</p>
@foreach($section['fields'] as $field => $label)
<div class="form-row-spaced"><label for="{{ $field }}">{{ $label }}</label>
@if(in_array($field, ['title', 'quote', 'principal_name', 'principal_caption', 'content_caption', 'guide_caption']))
<input id="{{ $field }}" name="{{ $field }}" value="{{ old($field, $page->$field) }}" maxlength="{{ in_array($field, ['quote', 'principal_caption', 'content_caption', 'guide_caption']) ? 500 : 255 }}" @required(in_array($field, ['title', 'quote']))>
@else
<textarea id="{{ $field }}" name="{{ $field }}" rows="5" @required(in_array($field, ['description', 'guide']))>{{ old($field, $page->$field) }}</textarea>
@endif
@if($field === 'mission')<p class="field-hint">Satu poin misi per baris.</p>@endif
@error($field)<div class="form-error">{{ $message }}</div>@enderror</div>
@endforeach
@isset($section['photo'])
@php($photoField = $section['photo'])
@php($photoPath = $page->{$photoField.'_path'})
<div class="public-editor-upload"><label for="{{ $photoField }}">{{ match($photoField) { 'principal_photo' => 'Gambar Kepala Sekolah', 'guide_photo' => 'Ilustrasi Panduan', default => 'Foto Konten' } }}</label>
@if($photoField === 'guide_photo' && ! $photoPath)<img class="public-editor-preview" src="{{ asset('assets/images/guide-attendance-3d.png') }}" alt="Ilustrasi panduan bawaan">@endif
@if($photoPath)<img class="public-editor-preview" src="{{ asset('storage/'.$photoPath) }}" alt="Foto tersimpan"><label class="check-row"><input type="checkbox" name="{{ $section['remove'] }}" value="1" @checked(old($section['remove']))> Hapus foto ini</label>@endif
<input id="{{ $photoField }}" type="file" name="{{ $photoField }}" accept="image/jpeg,image/png,image/webp"><p class="field-hint">JPG, PNG, atau WebP, maksimal 2 MB. Pilih foto baru untuk mengganti foto lama.</p>@error($photoField)<div class="form-error">{{ $message }}</div>@enderror</div>
@endisset
</section>
@endforeach
<section class="card public-editor-section"><h2 class="compact-title">5. Media Sosial di Footer</h2><p class="form-note">Logo WhatsApp, Instagram, dan website tampil di bagian paling bawah. Kosongkan tautan jika belum tersedia.</p>
@foreach(['whatsapp_url' => ['WhatsApp', 'https://wa.me/6281234567890'], 'instagram_url' => ['Instagram', 'https://www.instagram.com/akun_sekolah'], 'website_url' => ['Website Sekolah', 'https://sekolah.sch.id']] as $field => [$label, $example])
<div class="form-row-spaced"><label for="{{ $field }}">{{ $label }}</label><input type="url" id="{{ $field }}" name="{{ $field }}" value="{{ old($field, $page->$field) }}" placeholder="{{ $example }}" maxlength="2048">@error($field)<div class="form-error">{{ $message }}</div>@enderror</div>
@endforeach
</section>
<div class="card"><button class="btn" type="submit">Simpan Semua Perubahan</button></div>
</form>
@endsection
