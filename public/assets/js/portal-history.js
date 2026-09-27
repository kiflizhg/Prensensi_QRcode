// Navigasi halaman privat; formulir dan kontrol interaktif memakai tombol normal.
document.addEventListener('keydown', (event) => {
    if (event.defaultPrevented || event.repeat || event.altKey || event.ctrlKey || event.metaKey || event.shiftKey) return;
    if (event.key !== 'ArrowLeft' && event.key !== 'ArrowRight') return;
    if (event.target instanceof Element && event.target.closest('input, textarea, select, [contenteditable]:not([contenteditable="false"]), [role="slider"], [role="tablist"], [role="menu"]')) return;
    event.preventDefault();
    if (event.key === 'ArrowLeft') window.history.back();
    else window.history.forward();
});
