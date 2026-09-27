@extends('layouts.guru')
@section('title', 'Jadwal Saya')
@section('content')
<header class="page-hero compact">
    <div><span class="page-kicker">Kehadiran Guru</span><h1 class="page-title">Jadwal Saya</h1><p class="page-subtitle">Jadwal presensi yang ditetapkan admin sekolah. Hubungi admin jika diperlukan perubahan.</p></div>
</header>
@if (! $guru)
    <p class="empty-state">Akun Anda belum terhubung dengan data guru. Hubungi admin sekolah.</p>
@else
    <section class="card teacher-schedule">
        <h2>{{ $guru->nama }}</h2>
        <p class="academic-description">NIP: {{ $guru->nip }} · {{ $guru->mata_pelajaran ?: 'Mata pelajaran belum diisi' }}</p>
        @if ($guru->status !== 'aktif' || ! auth()->user()->is_active)
            <p class="alert alert-danger">Akun atau status guru belum aktif. Hubungi admin sebelum melakukan presensi.</p>
        @endif
        @if (empty($jadwal))
            <p class="empty-state">Jadwal belum ditetapkan oleh admin sekolah.</p>
        @endif
        <div class="teacher-week" aria-label="Jadwal presensi saya">
            @foreach ($hari as $nomor => $nama)
                @php($jadwalHari = $jadwal[$nomor] ?? null)
                <article @class(['teacher-day', 'is-today' => $nomor === now()->dayOfWeekIso])>
                    <header><h3>{{ $nama }}</h3>@if ($nomor === now()->dayOfWeekIso)<span>Hari ini</span>@endif</header>
                    <p class="teacher-day-status">{{ $jadwalHari ? (!empty($jadwalHari['aktif']) ? 'Jadwal aktif' : 'Tidak dijadwalkan') : 'Belum diatur' }}</p>
                    @if (!empty($jadwalHari['aktif']))
                        <dl><div><dt>Batas masuk · WIB</dt><dd>{{ $jadwalHari['jam_masuk'] ?? '—' }}</dd></div><div><dt>Mulai pulang · WIB</dt><dd>{{ $jadwalHari['jam_pulang'] ?? '—' }}</dd></div></dl>
                    @else
                        <p class="academic-description">{{ $jadwalHari ? 'Tidak ada jadwal presensi pada hari ini.' : 'Menunggu pengaturan dari admin sekolah.' }}</p>
                    @endif
                </article>
            @endforeach
        </div>
        <p class="academic-description">Semua jam menggunakan WIB. Muat ulang halaman untuk melihat perubahan terbaru dari admin.</p>
    </section>
@endif
@endsection
