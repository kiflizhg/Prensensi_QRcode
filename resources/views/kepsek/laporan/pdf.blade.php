<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Surat Laporan Presensi {{ $bulanLabel }}</title>
    <style>
        @page { margin: 24px 34px; }
        body { font-family: DejaVu Sans, Arial, sans-serif; color: #111827; font-size: 11px; }
        .kop { display: table; width: 100%; border-bottom: 3px double #111827; padding-bottom: 12px; margin-bottom: 18px; }
        .kop-logo { display: table-cell; width: 84px; vertical-align: middle; }
        .kop-logo img { width: 70px; height: 70px; object-fit: contain; }
        .kop-text { display: table-cell; text-align: center; vertical-align: middle; }
        .kop-text h1, .kop-text h2, .kop-text p { margin: 0; }
        .kop-text h1 { font-size: 14px; letter-spacing: .6px; }
        .kop-text h2 { font-size: 20px; margin-top: 3px; letter-spacing: 1px; }
        .kop-text p { font-size: 10.5px; margin-top: 4px; }
        .title { text-align: center; margin: 18px 0 16px; }
        .title h3 { margin: 0; font-size: 15px; text-decoration: underline; }
        .title p { margin: 5px 0 0; }
        .meta { width: 100%; margin-bottom: 14px; }
        .meta td { padding: 2px 0; border: 0; }
        .summary { display: table; width: 100%; margin: 10px 0 16px; }
        .summary div { display: table-cell; border: 1px solid #cbd5e1; padding: 8px; text-align: center; }
        .summary strong { display: block; font-size: 15px; }
        table.data { width: 100%; border-collapse: collapse; }
        table.data th, table.data td { border: 1px solid #9ca3af; padding: 6px; vertical-align: top; }
        table.data th { background: #eef2f7; text-align: center; font-size: 10px; }
        .intro { line-height: 1.6; text-align: justify; margin-bottom: 12px; }
        .signature { width: 100%; margin-top: 34px; }
        .signature td { border: 0; vertical-align: top; }
        .signature .right { width: 250px; text-align: center; }
        .signature-space { height: 72px; }
        .name { font-weight: 700; text-decoration: underline; }
        .muted { color: #4b5563; }
    </style>
</head>
<body>
    <section class="kop">
        <div class="kop-logo">
            @if ($logoData)
                <img src="{{ $logoData }}" alt="Logo">
            @endif
        </div>
        <div class="kop-text">
            <h1>Hargai waktu, hadir tepat waktu, berkarya sepenuh hati.</h1>
            <h2>SMK ISLAM CIPASUNG</h2>
            <p>Cipasung, Singaparna, Kabupaten Tasikmalaya</p>
            <p class="muted">Dokumen laporan presensi guru resmi sekolah</p>
        </div>
    </section>

    <section class="title">
        <h3>SURAT LAPORAN PRESENSI GURU</h3>
        <p>Periode: {{ $bulanLabel }}</p>
    </section>

    <p class="intro">
        Dengan hormat, berikut disampaikan laporan presensi guru SMK Islam Cipasung untuk periode
        {{ $bulanLabel }} sebagai bahan dokumentasi dan tindak lanjut administrasi sekolah.
    </p>

    <table class="meta">
        <tr><td style="width: 120px;">Nama Sekolah</td><td>: SMK Islam Cipasung</td></tr>
        <tr><td>Periode Laporan</td><td>: {{ $bulanLabel }}</td></tr>
        <tr><td>Tanggal Cetak</td><td>: {{ $tanggalSurat }}</td></tr>
    </table>

    <section class="summary">
        <div><span>Total Data</span><strong>{{ $rekap['total'] }}</strong></div>
        <div><span>Hadir</span><strong>{{ $rekap['hadir'] }}</strong></div>
        <div><span>Izin</span><strong>{{ $rekap['izin'] }}</strong></div>
        <div><span>Sakit</span><strong>{{ $rekap['sakit'] }}</strong></div>
        <div><span>Cuti</span><strong>{{ $rekap['cuti'] }}</strong></div>
        <div><span>Dinas Luar</span><strong>{{ $rekap['dinas_luar'] }}</strong></div>
    </section>

    <table class="data">
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Nama Guru</th>
                <th>Jam Masuk</th>
                <th>Jam Pulang</th>
                <th>Status</th>
                <th>Metode</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($presensis as $presensi)
                <tr>
                    <td style="text-align:center;">{{ $loop->iteration }}</td>
                    <td>{{ $presensi->tanggal->format('d-m-Y') }}</td>
                    <td>{{ $presensi->guru->nama }}</td>
                    <td style="text-align:center;">{{ $presensi->jam_masuk ?? '-' }}</td>
                    <td style="text-align:center;">{{ $presensi->jam_pulang ?? '-' }}</td>
                    <td>{{ ucwords(str_replace('_', ' ', $presensi->status)) }}</td>
                    <td>{{ $presensi->metode_input ?? '-' }}</td>
                </tr>
            @empty
                <tr><td colspan="7" style="text-align:center;">Belum ada data presensi pada periode ini.</td></tr>
            @endforelse
        </tbody>
    </table>

    <p class="intro" style="margin-top: 16px;">
        Demikian surat laporan presensi ini dibuat untuk digunakan sebagaimana mestinya.
    </p>

    <table class="signature">
        <tr>
            <td></td>
            <td class="right">
                <p>Tasikmalaya, {{ $tanggalSurat }}</p>
                <p>Kepala SMK Islam Cipasung</p>
                <div class="signature-space"></div>
                <p class="name">{{ $kepsek->name }}</p>
            </td>
        </tr>
    </table>
</body>
</html>
