<aside class="sidebar">
    <a class="brand brand-sidebar" href="/">
        <img class="brand-logo" src="{{ asset('assets/images/logo.jpeg') }}" alt="Logo SMK Islam Cipasung">
        <span>
            <strong>SMK Islam Cipasung</strong>
            <small>Hargai waktu, hadir tepat waktu, berkarya sepenuh hati.</small>
        </span>
    </a>
    <nav>
        @if ($role === 'admin')
            <a @class(['active' => request()->routeIs('admin.dashboard')]) href="{{ route('admin.dashboard') }}"><span>@include('components.icon', ['name' => 'dashboard'])</span>Command Center</a>
            <a @class(['active' => request()->routeIs('admin.guru.*')]) href="{{ route('admin.guru.index') }}"><span>@include('components.icon', ['name' => 'users'])</span>Data Guru</a>
            <a @class(['active' => request()->routeIs('admin.presensi.*')]) href="{{ route('admin.presensi.index') }}"><span>@include('components.icon', ['name' => 'clock'])</span>Presensi</a>
            <a @class(['active' => request()->routeIs('admin.jadwal.*')]) href="{{ route('admin.jadwal.index') }}"><span>@include('components.icon', ['name' => 'clock'])</span>Jadwal Guru</a>
            <a @class(['active' => request()->routeIs('admin.laporan.*')]) href="{{ route('admin.laporan.index') }}"><span>@include('components.icon', ['name' => 'report'])</span>Laporan</a>
            <a @class(['active' => request()->routeIs('admin.notifikasi.*')]) href="{{ route('admin.notifikasi.index') }}"><span>@include('components.icon', ['name' => 'bell'])</span>Arahan Kepsek</a>
            <a @class(['active' => request()->routeIs('admin.profil.*')]) href="{{ route('admin.profil.index') }}"><span>@include('components.icon', ['name' => 'shield'])</span>Profil</a>
        @elseif ($role === 'guru')
            <a @class(['active' => request()->routeIs('guru.dashboard')]) href="{{ route('guru.dashboard') }}"><span>@include('components.icon', ['name' => 'dashboard'])</span>Ruang Guru</a>
            <a @class(['active' => request()->routeIs('guru.presensi.*')]) href="{{ route('guru.presensi.index') }}"><span>@include('components.icon', ['name' => 'clock'])</span>Status Presensi</a>
            <a @class(['active' => request()->routeIs('guru.pengajuan.*')]) href="{{ route('guru.pengajuan.index') }}"><span>@include('components.icon', ['name' => 'send'])</span>Pengajuan</a>
            <a @class(['active' => request()->routeIs('guru.riwayat.*')]) href="{{ route('guru.riwayat.index') }}"><span>@include('components.icon', ['name' => 'report'])</span>Riwayat</a>
            <a @class(['active' => request()->routeIs('guru.notifikasi.*')]) href="{{ route('guru.notifikasi.index') }}"><span>@include('components.icon', ['name' => 'bell'])</span>Arahan Kepsek</a>
            <a @class(['active' => request()->routeIs('guru.profil.*')]) href="{{ route('guru.profil.index') }}"><span>@include('components.icon', ['name' => 'shield'])</span>Profil</a>
        @else
            <a @class(['active' => request()->routeIs('kepsek.dashboard')]) href="{{ route('kepsek.dashboard') }}"><span>@include('components.icon', ['name' => 'dashboard'])</span>Executive View</a>
            <a @class(['active' => request()->routeIs('kepsek.monitoring.*')]) href="{{ route('kepsek.monitoring.index') }}"><span>@include('components.icon', ['name' => 'scan'])</span>Monitoring</a>
            <a @class(['active' => request()->routeIs('kepsek.persetujuan.*')]) href="{{ route('kepsek.persetujuan.index') }}"><span>@include('components.icon', ['name' => 'check'])</span>Persetujuan</a>
            <a @class(['active' => request()->routeIs('kepsek.laporan.*')]) href="{{ route('kepsek.laporan.index') }}"><span>@include('components.icon', ['name' => 'report'])</span>Laporan</a>
            <a @class(['active' => request()->routeIs('kepsek.pesan.*')]) href="{{ route('kepsek.pesan.index') }}"><span>@include('components.icon', ['name' => 'send'])</span>Broadcast</a>
            <a @class(['active' => request()->routeIs('kepsek.profil.*')]) href="{{ route('kepsek.profil.index') }}"><span>@include('components.icon', ['name' => 'shield'])</span>Profil</a>
        @endif
    </nav>
    <div class="sidebar-foot">
        <span>Status Sistem</span>
        <strong>Aktif</strong>
    </div>
</aside>
