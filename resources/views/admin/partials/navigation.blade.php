@php
    $navigation = [
        ['label' => 'Beranda', 'route' => 'admin.dashboard', 'match' => 'admin.dashboard*', 'icon' => 'dashboard', 'group' => 'Ringkasan'],
        ['label' => 'Data Guru', 'route' => 'admin.guru.index', 'match' => 'admin.guru.*', 'icon' => 'users', 'group' => 'Administrasi akademik'],
        ['label' => 'Jadwal Guru', 'route' => 'admin.jadwal.index', 'match' => 'admin.jadwal.*', 'icon' => 'clock'],
        ['label' => 'Presensi', 'route' => 'admin.presensi.index', 'match' => 'admin.presensi.*', 'icon' => 'check'],
        ['label' => 'Laporan', 'route' => 'admin.laporan.index', 'match' => 'admin.laporan.*', 'icon' => 'report'],
        ['label' => 'Arahan Kepala Sekolah', 'route' => 'admin.notifikasi.index', 'match' => 'admin.notifikasi.*', 'icon' => 'bell', 'group' => 'Komunikasi & akun'],
        ['label' => 'Profil Saya', 'route' => 'admin.profil.index', 'match' => 'admin.profil.*', 'icon' => 'shield'],
        ['label' => 'Konten Publik', 'route' => 'admin.public-page.edit', 'match' => 'admin.public-page.*', 'icon' => 'report'],
    ];
@endphp
@php
    $isGuruPortal = auth()->user()->isGuru();
    if ($isGuruPortal) {
        $navigation = [
            ['label' => 'Beranda', 'route' => 'guru.dashboard', 'match' => 'guru.dashboard', 'icon' => 'dashboard', 'group' => 'Ringkasan'],
            ['label' => 'Jadwal Saya', 'route' => 'guru.jadwal.index', 'match' => 'guru.jadwal.*', 'icon' => 'clock', 'group' => 'Kehadiran guru'],
            ['label' => 'Presensi Saya', 'route' => 'guru.presensi.index', 'match' => 'guru.presensi.*', 'icon' => 'check'],
            ['label' => 'Pengajuan', 'route' => 'guru.pengajuan.index', 'match' => 'guru.pengajuan.*', 'icon' => 'send'],
            ['label' => 'Riwayat Presensi', 'route' => 'guru.riwayat.index', 'match' => 'guru.riwayat.*', 'icon' => 'report'],
            ['label' => 'Arahan Kepala Sekolah', 'route' => 'guru.notifikasi.index', 'match' => 'guru.notifikasi.*', 'icon' => 'bell', 'group' => 'Komunikasi & akun'],
            ['label' => 'Profil Saya', 'route' => 'guru.profil.index', 'match' => 'guru.profil.*', 'icon' => 'shield'],
        ];
    }
@endphp
<details class="admin-navigation" open>
    <summary class="admin-menu-toggle" aria-controls="admin-navigation-panel">@include('components.icon', ['name' => 'dashboard']) <span>{{ $isGuruPortal ? 'Menu guru' : 'Menu administrasi' }}</span><span class="menu-indicator" aria-hidden="true">+</span></summary>
    <aside class="sidebar" id="admin-navigation-panel" aria-label="{{ $isGuruPortal ? 'Navigasi guru' : 'Navigasi administrator' }}">
        <div class="portal-space"><span class="portal-space-icon">@include('components.icon', ['name' => 'shield'])</span><div><strong>{{ $isGuruPortal ? 'Ruang Guru' : 'Administrasi' }}</strong><small>{{ $isGuruPortal ? 'Jadwal dan kehadiran saya' : 'Manajemen presensi guru' }}</small></div></div>
        <nav aria-label="Menu utama">
            @foreach ($navigation as $item)
                @isset($item['group'])<p class="nav-group">{{ $item['group'] }}</p>@endisset
                <a @class(['active' => request()->routeIs($item['match'])]) href="{{ route($item['route']) }}" @if(request()->routeIs($item['match'])) aria-current="page" @endif>
                    <span>@include('components.icon', ['name' => $item['icon']])</span>{{ $item['label'] }}
                </a>
            @endforeach
        </nav>
        <div class="sidebar-foot"><span>SMK ISLAM CIPASUNG</span><strong>{{ $isGuruPortal ? 'Layanan Guru' : 'Administrasi Sekolah' }}</strong><small>Sistem Informasi Presensi Guru</small></div>
    </aside>
</details>
