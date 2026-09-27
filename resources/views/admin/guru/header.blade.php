<header class="toolbar guru-page-header">
    <div>
        <span class="page-kicker">Data Induk Guru</span>
        <h1 class="page-title">{{ $judul }}</h1>
        <p class="page-subtitle">{{ $keterangan }}</p>
    </div>
    <a class="btn {{ ($utama ?? false) ? '' : 'ghost' }}" href="{{ $tautan }}">{{ $tombol }}</a>
</header>
