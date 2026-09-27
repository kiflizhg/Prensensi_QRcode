(() => {
    const clocks = [...document.querySelectorAll('[data-school-clock]')];
    if (!clocks.length) return;
    let serverTime = Date.parse(clocks[0].dateTime);
    let localTime = Date.now();
    const formatter = new Intl.DateTimeFormat('id-ID', {
        timeZone: 'Asia/Jakarta', weekday: 'long', day: 'numeric', month: 'long',
        year: 'numeric', hour: '2-digit', minute: '2-digit', second: '2-digit', hourCycle: 'h23',
    });
    const update = () => {
        const current = new Date(serverTime + Date.now() - localTime);
        clocks.forEach((clock) => {
            clock.textContent = formatter.format(current) + ' WIB';
            clock.dateTime = current.toISOString();
        });
    };
    document.addEventListener('school-time', (event) => {
        const value = Date.parse(event.detail);
        if (!Number.isNaN(value)) { serverTime = value; localTime = Date.now(); update(); }
    });
    update();
    setInterval(update, 1000);
    document.addEventListener('visibilitychange', update);
})();
