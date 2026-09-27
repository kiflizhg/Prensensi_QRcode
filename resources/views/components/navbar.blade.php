<header class="topbar">
    <div>
        <strong>{{ $title ?? 'Sistem Informasi Kehadiran Guru' }}</strong>
        <span>{{ $subtitle ?? 'SMK Islam Cipasung - Hargai waktu, hadir tepat waktu, berkarya sepenuh hati.' }}</span>
    </div>
    <div class="topbar-meta">
        <span>{{ now()->translatedFormat('d F Y') }}</span>
        @auth
            <form method="post" action="{{ route('logout') }}">
                @csrf
                <button type="submit">Keluar</button>
            </form>
        @endauth
    </div>
</header>
