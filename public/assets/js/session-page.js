// Periksa ulang sesi saat Back/Forward memulihkan halaman dari memori browser.
window.addEventListener('pageshow', (event) => {
    if (event.persisted) window.location.reload();
});
