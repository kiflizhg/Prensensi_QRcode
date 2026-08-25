@extends('layouts.kepsek')

@section('content')
<section class="page-hero compact">
    <div>
        <span class="page-kicker">Approval Desk</span>
        <h2 class="page-title">Persetujuan Pengajuan Guru</h2>
        <p class="page-subtitle">Tinjau pengajuan guru dan tetapkan keputusan agar status presensi tersinkronisasi.</p>
    </div>
</section>
<table>
    <thead><tr><th>Guru</th><th>Jenis</th><th>Tanggal</th><th>Surat</th><th>Status</th><th>Aksi</th></tr></thead>
    <tbody>
        @forelse ($pengajuans as $pengajuan)
            <tr>
                <td>{{ $pengajuan->guru->nama }}</td>
                <td>{{ $pengajuan->jenis }}</td>
                <td>{{ $pengajuan->tanggal_mulai->format('d-m-Y') }} s.d. {{ $pengajuan->tanggal_selesai->format('d-m-Y') }}</td>
                <td>
                    @if ($pengajuan->lampiran)
                        <a class="btn ghost" href="{{ route('kepsek.persetujuan.lampiran', $pengajuan) }}" target="_blank">@include('components.icon', ['name' => 'report']) Lihat Surat</a>
                    @else
                        -
                    @endif
                </td>
                <td>{{ $pengajuan->status }}</td>
                <td>
                    @if ($pengajuan->status === 'menunggu')
                        <form method="post" action="{{ route('kepsek.persetujuan.setujui', $pengajuan) }}" style="display:inline">@csrf<button class="btn" type="submit">@include('components.icon', ['name' => 'check']) Setujui</button></form>
                        <form method="post" action="{{ route('kepsek.persetujuan.tolak', $pengajuan) }}" style="display:inline">@csrf<button class="btn danger" type="submit">Tolak</button></form>
                    @else
                        <span class="badge {{ $pengajuan->status === 'disetujui' ? 'success' : 'danger' }}">{{ ucfirst($pengajuan->status) }}</span>
                    @endif
                </td>
            </tr>
        @empty
            <tr><td colspan="6">Belum ada pengajuan yang perlu ditinjau.</td></tr>
        @endforelse
    </tbody>
</table>
@endsection
