@extends('layouts.guru')
@section('title', 'Arahan Kepala Sekolah')

@section('content')
<section class="page-hero compact">
    <div>
        <span class="page-kicker">Komunikasi Sekolah</span>
        <h1 class="page-title">Arahan Kepala Sekolah</h1>
        <p class="page-subtitle">Ikuti pesan dan pengumuman resmi yang dikirim kepala sekolah kepada guru.</p>
    </div>
</section>

<section class="notification-list">
    @forelse ($notifikasis as $notifikasi)
        <article @class(['notification-item', 'unread' => ! $notifikasi->dibaca_pada])>
            <div>
                <strong>{{ $notifikasi->judul }}</strong>
                <span>{{ $notifikasi->created_at->format('d-m-Y H:i') }}</span>
            </div>
            <p>{{ $notifikasi->pesan }}</p>
        </article>
    @empty
        <p class="empty-state">Belum ada arahan terbaru dari kepala sekolah.</p>
    @endforelse
</section>
@endsection
