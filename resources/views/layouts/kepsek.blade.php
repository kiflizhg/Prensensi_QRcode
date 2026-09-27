<!doctype html>
<html lang="id">
<head>
    @include('components.seo', ['title' => 'Ruang Kepala Sekolah — Presensi QR'])
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="{{ asset('assets/js/portal-history.js') }}?v={{ filemtime(public_path('assets/js/portal-history.js')) }}" defer></script>
    <script src="{{ asset('assets/js/session-page.js') }}?v={{ filemtime(public_path('assets/js/session-page.js')) }}" defer></script>
    @vite(['resources/css/app.css'])
    <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/kepsek.css') }}?v={{ filemtime(public_path('assets/css/kepsek.css')) }}">
</head>
<body class="app-body kepsek-body">
    <a class="skip-link" href="#main-content">Lewati ke konten utama</a>
    <header class="kepsek-header">
        <a class="kepsek-brand" href="{{ route('kepsek.dashboard') }}"><img src="{{ asset('assets/images/logo.jpeg') }}" alt="" width="40" height="40"><span><strong>SMK Islam Cipasung</strong><small>Ruang Kepala Sekolah</small></span></a>
        <a class="kepsek-profile-link" href="{{ route('kepsek.profil.index') }}" aria-label="Buka profil saya">
            <span class="kepsek-account">
            @if (auth()->user()->profilePhotoUrl())<img src="{{ auth()->user()->profilePhotoUrl() }}" alt="" width="40" height="40">@else<span>{{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}</span>@endif
            </span><span>Profil</span>
        </a>
    </header>
    <div class="kepsek-workspace">
        <nav class="kepsek-nav" aria-label="Menu kepala sekolah">
            @foreach ([['dashboard', 'dashboard', 'Beranda'], ['monitoring.index', 'users', 'Monitoring'], ['persetujuan.index', 'check', 'Pengajuan'], ['laporan.index', 'report', 'Laporan'], ['pesan.index', 'send', 'Arahan']] as [$page, $icon, $label])
                <a href="{{ route('kepsek.'.$page) }}" @class(['active' => request()->routeIs('kepsek.'.$page)]) @if(request()->routeIs('kepsek.'.$page)) aria-current="page" @endif>@include('components.icon', ['name' => $icon])<span>{{ $label }}</span></a>
            @endforeach
        </nav>
        <main id="main-content" class="content" tabindex="-1">
            @include('components.alert')
            @yield('content')
            <footer class="kepsek-footer"><span>{{ auth()->user()->name }}</span></footer>
        </main>
    </div>
</body>
</html>
