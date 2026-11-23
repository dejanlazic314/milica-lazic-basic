// Checks that every page loads the CSS it needs: a stylesheet in public/layout/css with a rule
// that matches something on the page must be loaded on that page. Elements, parts and modules
// load their own files (templates/content_element/_base.html.twig, add_assets()), and a missing
// add_assets() call shows up here.
//
//   node tests/visual/assets.mjs        (BASE_URL as in snapshot.mjs)
//
// Prints the missing files per page; exit code 1 if any.

import fs from 'node:fs';
import path from 'node:path';
import { chromium } from 'playwright';

const baseUrl = process.env.BASE_URL ?? 'https://milica-lazic.ddev.site';
const pages = JSON.parse(fs.readFileSync(new URL('./pages.json', import.meta.url), 'utf8'));
const root = path.resolve('public/layout/css');
const files = fs
    .readdirSync(root, { recursive: true })
    .map((f) => f.replace(/\\/g, '/'))
    .filter((f) => f.endsWith('.css') && f !== 'backend.css');

// Selectors of every file, without pseudo-classes and pseudo-elements
const selectorsOf = (css) => {
    const plain = css.replace(/\/\*[\s\S]*?\*\//g, '');
    return [...plain.matchAll(/([^{};@]+)\{/g)]
        .map((m) => m[1].trim())
        .filter((s) => s && !/^(from|to|\d+%|@)/.test(s) && !s.startsWith('@'))
        .flatMap((s) => s.split(','))
        .map((s) => s.replace(/::?[a-z-]+(\([^)]*\))?/gi, '').trim())
        .filter((s) => s && !/^(html|body|\*|:root|[a-z0-9]+)$/i.test(s));
};
const byFile = Object.fromEntries(
    files.map((f) => [f, selectorsOf(fs.readFileSync(path.join(root, f), 'utf8'))]),
);

const browser = await chromium.launch();
let missing = 0;

for (const width of [375, 1280]) {
    const page = await (
        await browser.newContext({ viewport: { width, height: 900 }, ignoreHTTPSErrors: true })
    ).newPage();
    for (const p of pages) {
        await page.goto(baseUrl + p, { waitUntil: 'networkidle' });
        const result = await page.evaluate((byFile) => {
            const loaded = [...document.querySelectorAll('link[rel="stylesheet"]')].map((l) =>
                new URL(l.href).pathname.replace('/layout/css/', ''),
            );
            const needed = Object.entries(byFile)
                .filter(([, selectors]) =>
                    selectors.some((s) => {
                        try {
                            return document.querySelector(s);
                        } catch {
                            return false;
                        }
                    }),
                )
                .map(([file]) => file);
            return needed.filter((f) => !loaded.includes(f));
        }, byFile);
        if (result.length) {
            missing++;
            console.log(`MISSING ${p}@${width}: ${result.join(', ')}`);
        }
    }
    await page.context().close();
}

await browser.close();
console.log(missing ? `${missing} page shots miss CSS` : 'all pages load the CSS they use');
process.exit(missing ? 1 : 0);
