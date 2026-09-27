const { test } = require('node:test');
const assert = require('node:assert/strict');
const { readFileSync } = require('node:fs');
const vm = require('node:vm');

test('arrows work after menu focus but leave editable controls alone', () => {
    let handler;
    let back = 0;
    let forward = 0;
    class Element {
        constructor(tag) { this.tag = tag; }
        closest(selector) { return selector.split(', ').includes(this.tag) ? this : null; }
    }
    vm.runInNewContext(readFileSync('public/assets/js/portal-history.js', 'utf8'), {
        Element,
        document: { addEventListener: (_, fn) => { handler = fn; } },
        window: { history: { back: () => back++, forward: () => forward++ } },
    });
    const event = (key, tag) => ({ key, target: new Element(tag), preventDefault() {} });
    handler(event('ArrowLeft', 'a'));
    handler(event('ArrowRight', 'body'));
    handler(event('ArrowLeft', 'input'));
    handler(event('ArrowRight', 'textarea'));
    handler({ ...event('ArrowLeft', 'body'), altKey: true });
    assert.equal(back, 1);
    assert.equal(forward, 1);
});
