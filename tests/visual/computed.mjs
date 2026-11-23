// Computed styles of every element, compared before and after a CSS change that must not
// change anything (moving rules between files or layers, removing !important…).
//
//   node tests/visual/computed.mjs baseline
//   node tests/visual/computed.mjs check
//
// For every page in pages.json at 375 and 1280 px it stores a hash of the computed style of
// each element, and for elements that have a hover, focus or active rule also the style with
// that state forced (Chrome DevTools Protocol), so hover and focus are checked without a mouse.
// Output: test-results/visual/computed/{baseline,current}.json. Exit code 1 if anything differs.
//
// Options (environment): BASE_URL (default https://milica-lazic.ddev.site), ONLY (path filter).

import fs from 'node:fs';
import path from 'node:path';
import crypto from 'node:crypto';
import { chromium } from 'playwright';

const mode = process.argv[2];

if (!['baseline', 'check'].includes(mode)) {
    console.error('Usage: node tests/visual/computed.mjs baseline|check');
    process.exit(1);
}

const baseUrl = process.env.BASE_URL ?? 'https://milica-lazic.ddev.site';
const only = process.env.ONLY ?? '';
const pages = JSON.parse(fs.readFileSync(new URL('./pages.json', import.meta.url), 'utf8')).filter((p) =>
    p.includes(only),
);
const dir = path.resolve('test-results/visual/computed');
const STATES = ['hover', 'focus', 'focus-visible', 'active'];

fs.mkdirSync(dir, { recursive: true });

// Runs in the page: a stable path for every element and the hash input of its computed style
const collect = () => {
    const pathOf = (el) => {
        const parts = [];
        for (let e = el; e && e !== document.documentElement; e = e.parentElement) {
            const index = [...e.parentElement.children].indexOf(e);
            parts.unshift(`${e.tagName.toLowerCase()}${e.id ? '#' + e.id : ''}:${index}`);
        }
        return parts.join('>');
    };
    const styleOf = (el, pseudo) => {
        const cs = getComputedStyle(el, pseudo);
        // Sorted: the order in which the browser lists custom properties is not stable
        return [...cs]
            .sort()
            .map((name) => name + ':' + cs.getPropertyValue(name))
            .join(';');
    };
    const all = [document.documentElement, ...document.querySelectorAll('body *')];
    return all.map((el) => [
        pathOf(el),
        styleOf(el) + '|before|' + styleOf(el, '::before') + '|after|' + styleOf(el, '::after'),
    ]);
};

// Selectors with a state pseudo-class, from every stylesheet on the page, without the state
const stateTargets = () => {
    const found = new Set();
    const walk = (rules) => {
        for (const rule of rules) {
            if (rule.selectorText) {
                for (const sel of rule.selectorText.split(',')) {
                    if (/:(hover|focus|focus-visible|focus-within|active)/.test(sel)) {
                        const base = sel
                            .replace(/:(hover|focus-visible|focus-within|focus|active)/g, '')
                            .replace(/::?[a-z-]+(\([^)]*\))?$/i, '')
                            .trim();
                        if (base) found.add(base);
                    }
                }
            } else if (rule.cssRules) walk(rule.cssRules);
        }
    };
    for (const sheet of document.styleSheets) {
        try {
            walk(sheet.cssRules);
        } catch {}
    }
    return [...found];
};

const hash = (text) => crypto.createHash('sha1').update(text).digest('hex').slice(0, 16);

const browser = await chromium.launch();
const result = {};

for (const width of [375, 1280]) {
    const context = await browser.newContext({
        viewport: { width, height: 900 },
        ignoreHTTPSErrors: true,
        reducedMotion: 'reduce',
    });
    await context.addInitScript(() =>
        localStorage.setItem('cookie-choice', JSON.stringify({ v: 1, maps: false, statistics: false })),
    );
    const page = await context.newPage();
    const cdp = await context.newCDPSession(page);
    await cdp.send('DOM.enable');
    await cdp.send('CSS.enable');

    for (const p of pages) {
        await page.goto(baseUrl + p, { waitUntil: 'networkidle' });
        await page.evaluate(() => document.fonts.ready);
        const key = `${p}@${width}`;
        const entries = {};

        for (const [elPath, style] of await page.evaluate(collect)) entries[elPath] = hash(style);

        // Forced states: every element that one of the state selectors matches
        const selectors = await page.evaluate(stateTargets);
        const { root } = await cdp.send('DOM.getDocument', { depth: -1 });
        const nodeIds = new Set();
        for (const selector of selectors) {
            try {
                const { nodeIds: ids } = await cdp.send('DOM.querySelectorAll', {
                    nodeId: root.nodeId,
                    selector,
                });
                ids.forEach((id) => nodeIds.add(id));
            } catch {}
        }
        for (const nodeId of nodeIds) {
            const { object } = await cdp.send('DOM.resolveNode', { nodeId });
            for (const state of STATES) {
                await cdp.send('CSS.forcePseudoState', { nodeId, forcedPseudoClasses: [state] });
                const { result: value } = await cdp.send('Runtime.callFunctionOn', {
                    objectId: object.objectId,
                    returnByValue: true,
                    // Transitions started by the forced state are finished first, so the end value is read
                    functionDeclaration: `function () {
                        const self = this;
                        getComputedStyle(self).color;
                        document.getAnimations().forEach((a) => { try { a.finish(); } catch {} });
                        const all = [self, ...self.querySelectorAll('*')];
                        const cs = (el, pseudo) => { const s = getComputedStyle(el, pseudo); return [...s].sort().map((n) => n + ':' + s.getPropertyValue(n)).join(';'); };
                        return all.map((el) => cs(el) + cs(el, '::before') + cs(el, '::after')).join('#');
                    }`,
                });
                await cdp.send('CSS.forcePseudoState', { nodeId, forcedPseudoClasses: [] });
                const { node } = await cdp.send('DOM.describeNode', { nodeId });
                entries[
                    `state:${state}:${node.localName}#${nodeId}:${node.attributes?.join(' ').slice(0, 80)}`
                ] = hash(value.value);
            }
        }

        result[key] = entries;
        process.stdout.write('.');
    }
    await context.close();
}

await browser.close();
const file = path.join(dir, mode === 'baseline' ? 'baseline.json' : 'current.json');
fs.writeFileSync(file, JSON.stringify(result));

if (mode === 'baseline') {
    console.log(`\n${Object.keys(result).length} page shots saved to ${file}`);
    process.exit(0);
}

// Compare; forced-state keys contain DevTools node ids, so they are matched by order
const before = JSON.parse(fs.readFileSync(path.join(dir, 'baseline.json'), 'utf8'));
let differences = 0;
for (const [key, entries] of Object.entries(result)) {
    const old = before[key] ?? {};
    // <script>, <link>, <style> are not rendered; which scripts a page loads may change
    const plain = (o) =>
        Object.entries(o).filter(
            ([k]) => !k.startsWith('state:') && !/(^|>)(script|link|style|template)[:#]/.test(k),
        );
    const states = (o) =>
        Object.entries(o)
            .filter(([k]) => k.startsWith('state:'))
            .map(([k, v]) => k.replace(/#\d+:/, ':') + '=' + v)
            .sort();
    const changed = plain(entries)
        .filter(([k, v]) => old[k] !== v)
        .map(([k]) => k);
    const missing = plain(old)
        .filter(([k]) => !(k in entries))
        .map(([k]) => k);
    const a = states(old);
    const b = states(entries);
    const stateDiff = b.filter((x) => !a.includes(x)).length + a.filter((x) => !b.includes(x)).length;
    if (changed.length || missing.length || stateDiff) {
        differences++;
        console.log(
            `\nDIFF ${key}: ${changed.length} elements changed, ${missing.length} missing, ${stateDiff} state differences`,
        );
        changed.slice(0, 5).forEach((k) => console.log('  ' + k));
    }
}
console.log(`\n${Object.keys(result).length} page shots, ${differences} different`);
process.exit(differences ? 1 : 0);
