<section class="card" data-presensi-chart @if (isset($refreshUrl)) data-refresh-url="{{ $refreshUrl }}" @endif>
    <h3>{{ $judulGrafik }}</h3>
    <p>{{ isset($refreshUrl) ? 'Lima hari kerja minggu berjalan, Senin–Jumat. Hari mendatang atau tanpa presensi bernilai 0.' : 'Jumlah guru dengan presensi masuk per tanggal. Hari tanpa presensi ditampilkan sebagai 0.' }}</p>
    <p data-chart-status role="status">{{ isset($refreshUrl) ? 'Diperbarui otomatis setiap 15 detik.' : $rentang }}</p>
    <div class="table-wrap">
        <div data-chart-bars style="display:flex;align-items:flex-end;gap:12px;min-width:560px;height:240px;padding:12px 0">
            @php($maksimum = max(1, collect($grafik)->max('jumlah')))
            @foreach ($grafik as $titik)
                <div style="flex:1;min-width:30px;text-align:center">
                    <strong>{{ $titik['jumlah'] }}</strong>
                    <div style="height:{{ $titik['jumlah'] / $maksimum * 170 }}px;background:var(--primary, #2563eb);border-radius:4px" title="{{ $titik['tanggal'] }}: {{ $titik['jumlah'] }} guru"></div>
                    <small>{{ \Carbon\Carbon::parse($titik['tanggal'])->locale('id')->translatedFormat('D, d/m') }}</small>
                </div>
            @endforeach
        </div>
    </div>
</section>
@once
<script src="{{ asset('assets/js/admin-presensi-chart.js') }}?v={{ filemtime(public_path('assets/js/admin-presensi-chart.js')) }}" defer></script>
@endonce
