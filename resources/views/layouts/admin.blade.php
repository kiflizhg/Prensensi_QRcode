<!doctype html>
<html lang="id">
<head>
    @php
        $pageTitle = View::hasSection('title') ? View::getSection('title') . ' — Presensi QR Acanlogic' : 'Admin Panel — Presensi QR Acanlogic';
    @endphp
    @include('components.seo', ['title' => $pageTitle])
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="{{ asset('assets/js/portal-history.js') }}?v={{ filemtime(public_path('assets/js/portal-history.js')) }}" defer></script>
    <script src="{{ asset('assets/js/session-page.js') }}?v={{ filemtime(public_path('assets/js/session-page.js')) }}" defer></script>
    @vite(['resources/css/app.css'])
    <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/admin.css') }}?v={{ filemtime(public_path('assets/css/admin.css')) }}">
    @if (auth()->user()->isGuru())
        <link rel="stylesheet" href="{{ asset('assets/css/guru.css') }}?v={{ filemtime(public_path('assets/css/guru.css')) }}">
    @endif
    <script src="{{ asset('assets/js/admin-navigation.js') }}?v={{ filemtime(public_path('assets/js/admin-navigation.js')) }}" defer></script>
    <script src="{{ asset('assets/js/admin-clock.js') }}?v={{ filemtime(public_path('assets/js/admin-clock.js')) }}" defer></script>
</head>
<body class="app-body admin-body {{ auth()->user()->isGuru() ? 'guru-body' : '' }}">
    <a class="skip-link" href="#main-content">Lewati ke konten utama</a>
    @include('admin.partials.header')
    <div class="shell">
        @include('admin.partials.navigation')
        <div class="admin-workspace">
        <main class="content" id="main-content" tabindex="-1">
            @include('components.alert')
            @yield('content')
        </main>
        <footer class="admin-footer">SMK Islam Cipasung <span>Sistem Informasi Presensi Guru</span></footer>
        </div>
    </div>
    @if (auth()->user()->isGuru())
        @include('guru.partials.mobile-navigation')
    @endif
</body>
</html>
