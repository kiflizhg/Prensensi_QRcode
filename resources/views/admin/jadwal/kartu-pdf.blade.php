<!doctype html>
<html lang="id">
<head><meta charset="utf-8"><title>Kartu Guru - {{ $guru->nama }}</title>
<style>
@page { margin: 22pt; }
body { font-family: DejaVu Sans, sans-serif; font-size: 10pt; color: #20334a; }
.card { border: 1pt solid #cedce5; padding: 18pt; }
.letterhead { width: 100%; border-bottom: 3pt solid #174b65; padding-bottom: 12pt; }
.school { font-size: 19pt; font-weight: bold; color: #174b65; }
.foundation { font-size: 8pt; margin-top: 4pt; color: #58687a; }
h1 { font-size: 13pt; letter-spacing: 1pt; text-align: center; margin: 14pt 0; }
.identity { width: 100%; table-layout: fixed; }
.identity td { vertical-align: middle; }
.photo { width: 88pt; text-align: center; }
.photo img { max-width: 80pt; max-height: 105pt; }
.placeholder { background: #edf3f7; padding: 30pt 0; color: #174b65; font-size: 24pt; }
.details { padding: 0 14pt; word-wrap: break-word; }
.label { font-size: 8pt; color: #58687a; margin: 0 0 4pt; }
.name { font-size: 13pt; font-weight: bold; margin: 0 0 12pt; }
.nip { font-size: 11pt; margin: 0; }
.qr { width: 106pt; text-align: center; }
.qr img { width: 100pt; height: 100pt; }
.qr p { font-size: 7pt; color: #58687a; margin: 4pt 0 0; }
footer { border-top: 1pt solid #e1e7ed; margin-top: 16pt; padding-top: 8pt; text-align: center; font-size: 8pt; color: #58687a; }
</style></head>
<body><div class="card">
<table class="letterhead"><tr>
<td style="width:58pt"><img src="{{ $logoImage }}" width="52" height="52" alt="Logo sekolah"></td>
<td><div class="school">SMK ISLAM CIPASUNG</div><div class="foundation">Hargai waktu, hadir tepat waktu, berkarya sepenuh hati.</div></td>
</tr></table>
<h1>KARTU IDENTITAS GURU</h1>
<table class="identity"><tr>
<td class="photo">
@if (!empty($photoImage))
<img src="{{ $photoImage }}" alt="Foto {{ $guru->nama }}">
@else
<div class="placeholder">{{ mb_strtoupper(mb_substr($guru->nama, 0, 1)) }}</div>
@endif
</td>
<td class="details"><p class="label">Nama Guru</p><p class="name">{{ $guru->nama }}</p><p class="label">Nomor Induk Pegawai (NIP)</p><p class="nip">{{ $guru->nip }}</p></td>
<td class="qr"><img src="{{ $qrImage }}" alt="QR presensi guru"><p>QR Presensi Guru</p></td>
</tr></table>
<footer>SMK Islam Cipasung</footer>
</div></body></html>