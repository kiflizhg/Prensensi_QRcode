const {test} = require('node:test');
const assert = require('node:assert/strict');
const fs = require('node:fs');
const vm = require('node:vm');

test('jam WIB berganti dari Sabtu ke Minggu dan Senin tanpa memuat ulang', () => {
    let localTime = Date.parse('2026-09-26T10:00:00Z');
    const clock = {dateTime: '2026-09-26T23:59:59+07:00', textContent: ''};
    const listeners = {};
    let tick;
    class TestDate extends Date { static now() { return localTime; } }
    vm.runInNewContext(fs.readFileSync('public/assets/js/admin-clock.js', 'utf8'), {
        Date: TestDate, Intl,
        document: {querySelectorAll: () => [clock], addEventListener: (name, callback) => { listeners[name] = callback; }},
        setInterval: (callback) => { tick = callback; },
    });
    assert.match(clock.textContent, /Sabtu/);
    assert.match(clock.textContent, /23.59.59 WIB/);
    localTime += 2000;
    tick();
    assert.match(clock.textContent, /Minggu, 27 September 2026/);
    assert.match(clock.textContent, /00.00.01 WIB/);
    localTime += 86400000;
    listeners.visibilitychange();
    assert.match(clock.textContent, /Senin, 28 September 2026/);
    listeners['school-time']({detail: '2026-10-01T07:30:00+07:00'});
    assert.match(clock.textContent, /Kamis, 1 Oktober 2026/);
    assert.match(clock.textContent, /07.30.00 WIB/);
});
