@php
    $portalPrefix = auth()->user()->isGuru() ? 'guru' : 'admin';
    $portalLabel = $portalPrefix === 'guru' ? 'Guru' : 'Administrator';
    $currentPage = match (true) {
        request()->routeIs('admin.dashboard*') => 'Beranda',
        request()->routeIs('admin.guru.*') => 'Data Guru',
        request()->routeIs('admin.jadwal.*') => 'Jadwal Guru',
        request()->routeIs('admin.presensi.*') => 'Presensi',
        request()->routeIs('admin.laporan.*') => 'Laporan',
        request()->routeIs('admin.notifikasi.*') => 'Arahan Kepala Sekolah',
        request()->routeIs('admin.public-page.*') => 'Konten Publik',
        default => 'Profil Saya',
    };
@endphp
<header class="admin-header">
    <a class="portal-school" href="{{ route($portalPrefix.'.dashboard') }}">
        <img src="{{ asset('assets/images/logo.jpeg') }}" alt="Logo SMK Islam Cipasung" width="44" height="44">
        <span><strong>SMK Islam Cipasung</strong><small>{{ $portalPrefix === 'guru' ? 'Portal Akademik Guru' : 'Portal Administrasi Akademik' }}</small></span>
    </a>
    <div class="admin-header-title"><span>RUANG {{ strtoupper($portalLabel) }}</span><strong>{{ $portalPrefix === 'guru' ? 'Layanan Guru' : $currentPage }}</strong></div>
    <div class="admin-header-actions">
        <time class="academic-clock" data-school-clock datetime="{{ now()->toIso8601String() }}">{{ now()->locale('id')->translatedFormat('l, d F Y · H:i:s') }} WIB</time>
        <a class="admin-account" href="{{ route($portalPrefix.'.profil.index') }}" aria-label="Buka profil {{ auth()->user()->name }}">
            <span class="admin-avatar" aria-hidden="true">
                @if (auth()->user()->profilePhotoUrl())
                    <img src="{{ auth()->user()->profilePhotoUrl() }}" alt="" width="38" height="38">
                @else
                    {{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}
                @endif
            </span>
            <span class="admin-account-name">{{ auth()->user()->name }}<small>{{ $portalLabel }}</small></span>
        </a>
        <form method="post" action="{{ route('logout') }}">@csrf<button type="submit" class="admin-logout">Keluar</button></form>
    </div>
</header>
