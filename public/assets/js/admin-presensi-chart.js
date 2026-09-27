document.querySelectorAll('[data-presensi-chart][data-refresh-url]').forEach((chart) => {
    let busy = false;
    const refresh = async () => {
        if (busy || document.hidden) return;
        busy = true;
        try {
            const response = await fetch(chart.dataset.refreshUrl, {headers: {Accept: 'application/json'}, cache: 'no-store'});
            if (!response.ok) throw new Error('Gagal memuat grafik');
            const {grafik, waktu, ringkasan} = await response.json();
            if (waktu) document.dispatchEvent(new CustomEvent('school-time', {detail: waktu}));
            Object.entries(ringkasan || {}).forEach(([key, value]) => {
                document.querySelectorAll(`[data-summary="${key}"]`).forEach((node) => { node.textContent = value; });
            });
            const maximum = Math.max(1, ...grafik.map((point) => point.jumlah));
            const bars = grafik.map((point) => {
                const column = document.createElement('div');
                column.style.cssText = 'flex:1;min-width:30px;text-align:center';
                const count = document.createElement('strong');
                count.textContent = point.jumlah;
                const bar = document.createElement('div');
                bar.style.cssText = `height:${point.jumlah / maximum * 170}px;background:var(--primary, #2563eb);border-radius:4px`;
                bar.title = `${point.tanggal}: ${point.jumlah} guru`;
                const label = document.createElement('small');
                label.textContent = new Intl.DateTimeFormat('id-ID', {timeZone: 'Asia/Jakarta', weekday: 'short', day: '2-digit', month: '2-digit'}).format(new Date(`${point.tanggal}T12:00:00+07:00`));
                column.append(count, bar, label);
                return column;
            });
            chart.querySelector('[data-chart-bars]').replaceChildren(...bars);
            chart.querySelector('[data-chart-status]').textContent = 'Diperbarui otomatis setiap 15 detik.';
        } catch {
            chart.querySelector('[data-chart-status]').textContent = 'Pembaruan gagal. Menampilkan data terakhir; mencoba kembali otomatis.';
        } finally {
            busy = false;
        }
    };
    setInterval(refresh, 15000);
    document.addEventListener('visibilitychange', refresh);
});
