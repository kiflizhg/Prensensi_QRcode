<!doctype html>
<html lang="id">

<head>
    @include('components.seo', [
        'title' => 'Login — Presensi QR Acanlogic',
        'description' => 'Halaman masuk sistem presensi guru berbasis QR Code untuk SMK Islam Cipasung.',
        'robots' => 'noindex, nofollow, noarchive',
        'type' => 'website'
    ])
    @vite(['resources/css/app.css'])
    <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/public-login.css') }}?v={{ filemtime(public_path('assets/css/public-login.css')) }}">
    <script src="{{ asset('assets/js/login.js') }}" defer></script>
    <script src="{{ asset('assets/js/session-page.js') }}?v={{ filemtime(public_path('assets/js/session-page.js')) }}" defer></script>
</head>

<body class="login-body">
    <main class="login-page">
        <section class="login-visual">
            <img src="{{ asset('assets/images/1.jpeg') }}" alt="Banner SMK Islam Cipasung" fetchpriority="high">
            <div>
                <span class="login-banner-kicker">SMK ISLAM CIPASUNG</span>
                <h2>Disiplin dalam waktu.<br>Bermakna dalam pendidikan.</h2>
                <p>Portal akademik untuk mendukung kehadiran dan pengabdian guru.</p>
            </div>
        </section>

        <section class="login-card">
            <a class="brand" href="/">
                <img class="brand-logo" src="{{ asset('assets/images/logo.jpeg') }}" alt="Logo SMK Islam Cipasung">
                <span>
                    <strong>SMK Islam Cipasung</strong>
                    <small>Sistem Informasi Presensi Guru</small>
                </span>
            </a>

            <span class="login-section-label">AKSES PORTAL AKADEMIK</span>
            <h1>Selamat Datang</h1>
            <a class="login-home-link" href="{{ route('home') }}">← Kembali ke Beranda Sekolah</a>
            <p class="login-intro">Gunakan akun sekolah untuk mengakses jadwal dan layanan presensi.</p>

            @include('components.alert')

            <form method="post" action="{{ route('login.store') }}">
                @csrf
                <label for="login">Username atau Email</label>
                <input id="login" name="login" value="{{ old('login') }}" required autocomplete="username" autocapitalize="none" spellcheck="false" enterkeyhint="next"
                    placeholder="Masukkan username atau email">

                <label for="password">Password</label>
                <div class="login-password-wrap">
                    <input id="password" type="password" name="password" required autocomplete="current-password" enterkeyhint="go" placeholder="Masukkan password">
                    <button class="login-password-toggle" type="button" aria-controls="password" aria-pressed="false" hidden>Tampilkan</button>
                </div>

                <label class="check-row">
                    <input type="checkbox" name="remember" value="1">
                    <span>Ingat sesi masuk</span>
                </label>

                <button class="btn login-submit" type="submit">Masuk</button>
            </form>
            <p class="login-help">Kesulitan masuk? Hubungi admin sekolah.</p>
        </section>
    </main>
</body>

</html>
