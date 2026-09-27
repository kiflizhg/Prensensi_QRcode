<nav class="teacher-bottom-nav" aria-label="Navigasi cepat guru">
    @foreach ([
        ['route' => 'guru.dashboard', 'label' => 'Beranda', 'icon' => 'dashboard'],
        ['route' => 'guru.jadwal.index', 'label' => 'Jadwal', 'icon' => 'clock'],
        ['route' => 'guru.presensi.index', 'label' => 'Presensi', 'icon' => 'check'],
        ['route' => 'guru.pengajuan.index', 'label' => 'Pengajuan', 'icon' => 'send'],
        ['route' => 'guru.profil.index', 'label' => 'Profil', 'icon' => 'users'],
    ] as $item)
        <a href="{{ route($item['route']) }}" @if(request()->routeIs($item['route'])) aria-current="page" @endif>
            @include('components.icon', ['name' => $item['icon']])<span>{{ $item['label'] }}</span>
        </a>
    @endforeach
</nav>
