<!doctype html>
<html lang="id">
<head>
    @include('components.seo', ['title' => 'Beranda — SMK Islam Cipasung', 'description' => $page->description])
    <link rel="stylesheet" href="{{ asset('assets/css/public.css') }}?v={{ filemtime(public_path('assets/css/public.css')) }}">
</head>
<body class="public-body">
<a class="public-skip" href="#utama">Lewati ke isi halaman</a>
<header class="public-header"><a class="public-brand" href="{{ route('home') }}"><img src="{{ asset('assets/images/logo.jpeg') }}" alt="" width="44" height="44"><span>SMK Islam Cipasung<small>Portal informasi & presensi guru</small></span></a><nav aria-label="Navigasi utama"><a href="#sekolah">Visi & Misi</a><a href="#panduan">Panduan</a><a href="#kepala-sekolah">Kepala Sekolah</a><a class="public-button" href="{{ auth()->check() ? route('dashboard') : route('login') }}">{{ auth()->check() ? 'Dashboard' : 'Masuk' }}</a></nav></header>
<main id="utama">
<section id="kepala-sekolah" class="public-hero public-welcome">
    <figure class="public-welcome-figure"><div class="public-welcome-photo">
        @if($page->principal_photo_path)
            <img src="{{ asset('storage/'.$page->principal_photo_path) }}" alt="Foto {{ $page->principal_name ?: 'Kepala Sekolah' }}" fetchpriority="high">
        @else
            <div class="public-photo-placeholder"><img src="{{ asset('assets/images/logo.jpeg') }}" alt="Logo sekolah" width="96" height="96"><span>Foto kepala sekolah belum ditambahkan.</span></div>
        @endif
    </div>
    @if($page->principal_caption)<figcaption>{{ $page->principal_caption }}</figcaption>@endif
    </figure>
    <div class="public-hero-copy">
        <span class="public-eyebrow">SAMBUTAN KEPALA SEKOLAH</span>
        <h1>Selamat Datang di SMK Islam Cipasung</h1>
        @if($page->principal_bio)
            <p class="public-preserve">{{ $page->principal_bio }}</p>
        @endif
        <p class="public-signature"><strong>{{ $page->principal_name ?: 'Kepala Sekolah' }}</strong><span>Kepala SMK Islam Cipasung</span></p>
        <blockquote>“{{ $page->quote }}”</blockquote>
    </div>

</section>

<section id="sekolah" class="public-section"><div class="public-section-heading"><span class="public-eyebrow">ARAH PENDIDIKAN</span><h2>Visi & Misi Sekolah</h2><p>Tujuan bersama yang menjadi dasar setiap langkah.</p></div><div class="public-two-columns"><article class="public-panel"><span class="public-number">01</span><h3>Visi</h3><p class="public-preserve">{{ $page->vision ?: 'Visi resmi sekolah akan ditampilkan setelah diperbarui oleh admin.' }}</p></article><article class="public-panel"><span class="public-number">02</span><h3>Misi</h3>@if($page->mission)<ul class="public-lines">@foreach(preg_split('/\r\n|\r|\n/', $page->mission, -1, PREG_SPLIT_NO_EMPTY) as $line)<li>{{ $line }}</li>@endforeach</ul>@else<p>Misi resmi sekolah akan ditampilkan setelah diperbarui oleh admin.</p>@endif</article></div></section>

<section class="public-section public-about"><span class="public-eyebrow">PORTAL PRESENSI GURU</span><h2>{{ $page->title }}</h2><p class="public-preserve">{{ $page->description }}</p>@if($page->content_photo_path)
<figure class="public-content-figure"><img src="{{ asset('storage/'.$page->content_photo_path) }}" alt="{{ $page->content_caption ?: $page->title }}" loading="lazy">@if($page->content_caption)<figcaption>{{ $page->content_caption }}</figcaption>@endif</figure>
@endif</section>
<section id="panduan" class="public-section public-guide">
    <div class="public-section-heading"><span class="public-eyebrow">PANDUAN PENGGUNAAN</span><h2>Presensi tertib.<br>Langkah yang mudah.</h2><p>Ikuti alur berikut untuk menggunakan layanan presensi guru.</p>
    <figure class="public-guide-art"><img src="{{ $page->guide_photo_path ? asset('storage/'.$page->guide_photo_path) : asset('assets/images/guide-attendance-3d.png') }}" alt="{{ $page->guide_caption ?: 'Ilustrasi akun guru, jadwal, pemindaian kartu QR, dan laporan kehadiran' }}" loading="lazy">@if($page->guide_caption)<figcaption>{{ $page->guide_caption }}</figcaption>@endif</figure>
    <a class="public-button" href="{{ auth()->check() ? route('dashboard') : route('login') }}">Mulai Menggunakan Portal</a></div>
    <ol class="public-steps">@foreach(preg_split('/\r\n|\r|\n/', $page->guide, -1, PREG_SPLIT_NO_EMPTY) as $step)<li><span>{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span><p>{{ $step }}</p></li>@endforeach</ol>
</section>

<section class="public-closing"><span class="public-eyebrow">SETIAP MENIT BERARTI</span><h2>“{{ $page->quote }}”</h2><a class="public-button" href="{{ auth()->check() ? route('dashboard') : route('login') }}">Buka Portal Presensi ↗</a></section>
</main><footer class="public-footer" id="kontak">
    <div><strong>SMK Islam Cipasung</strong><p>Waktu tertata, langkah bermakna.</p><small>© {{ date('Y') }} SMK Islam Cipasung</small></div>
    <div><span class="public-footer-label">Temukan Kami</span><nav class="public-footer-social" aria-label="Media sosial dan website sekolah">
    @foreach(['whatsapp' => ['whatsapp_url', 'WhatsApp'], 'instagram' => ['instagram_url', 'Instagram'], 'website' => ['website_url', 'Website Sekolah']] as $icon => [$field, $label])
        @if($page->$field)
        <a class="public-social-icon public-social-{{ $icon }}" href="{{ $page->$field }}" target="_blank" rel="noopener noreferrer" aria-label="Buka {{ $label }} sekolah (tab baru)" title="{{ $label }}">@include('components.social-icon', ['name' => $icon])<span>{{ $label }}</span></a>
        @else
        <span class="public-social-icon public-social-disabled" title="{{ $label }}: Tautan belum tersedia">@include('components.social-icon', ['name' => $icon])<span>{{ $label }}</span><span class="public-sr-only">Tautan belum tersedia</span></span>
        @endif
    @endforeach
    </nav></div>
    <a href="#utama">Kembali ke atas ↑</a>
</footer>
</body>
</html>
