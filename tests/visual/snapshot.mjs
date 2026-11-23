// Whole-site snapshots: compare the site with itself before and after a change.
//
//   node tests/visual/snapshot.mjs baseline   take the reference shots
//   node tests/visual/snapshot.mjs check      take new shots and compare with the reference
//
// Pages are listed in tests/visual/pages.json; each one is shot full-page at 375, 768
// and 1280 px. The home page is also shot with the cookie banner open. Interactive states
// (menu open, exercise steps, form errors…) are in tests/visual/states.mjs.
// Output: test-results/visual/snapshots/{baseline,current,diff}/. Exit code 1 if anything differs.
//
// Options (environment): BASE_URL (default https://milica-lazic.ddev.site),
// WIDTHS (default 375,768,1280), ONLY (substring filter on the shot name),
// PARALLEL (shots taken at the same time, default 4; each one has its own browser context).

import fs from 'node:fs';
import path from 'node:path';
import { chromium } from 'playwright';
import { PNG } from 'pngjs';
import pixelmatch from 'pixelmatch';
import states from './states.mjs';

const mode = process.argv[2];

if (!['baseline', 'check'].includes(mode)) {
    console.error('Usage: node tests/visual/snapshot.mjs baseline|check');
    process.exit(1);
}

const baseUrl = process.env.BASE_URL ?? 'https://milica-lazic.ddev.site';
const widths = (process.env.WIDTHS ?? '375,768,1280').split(',').map(Number);
const only = process.env.ONLY ?? '';
const parallel = Math.max(1, Number(process.env.PARALLEL ?? 4));
const pages = JSON.parse(fs.readFileSync(new URL('./pages.json', import.meta.url), 'utf8'));

const root = path.resolve('test-results/visual/snapshots');
const dirs = {
    baseline: path.join(root, 'baseline'),
    current: path.join(root, 'current'),
    diff: path.join(root, 'diff'),
};

const outDir = mode === 'baseline' ? dirs.baseline : dirs.current;

// Empty the folder (removing the folder itself fails on Windows while it is open somewhere)
const empty = (folder) => {
    fs.mkdirSync(folder, { recursive: true });
    fs.readdirSync(folder).forEach((file) => fs.rmSync(path.join(folder, file)));
};

// With ONLY, keep the other shots (e.g. refresh a part of the baseline)
if (!only) empty(outDir);
fs.mkdirSync(outDir, { recursive: true });

if (mode === 'check') {
    empty(dirs.diff);
}

// "/oblasti-rada/odnosi" → "oblasti-rada_odnosi"
const slug = (pagePath) => pagePath.replace(/^\/|\/$/g, '').replace(/\//g, '_') || 'home';

// One shot per page and width, plus the home page with the cookie banner, plus the states
const jobs = [
    ...pages.flatMap((pagePath) =>
        widths.map((width) => ({ name: `${slug(pagePath)}-${width}`, path: pagePath, width })),
    ),
    ...widths.map((width) => ({ name: `home_banner-${width}`, path: '/', width, banner: true })),
    ...states.flatMap((state) =>
        state.widths.map((width) => ({ ...state, name: `state_${state.name}-${width}`, width })),
    ),
].filter((job) => job.name.includes(only));

const shoot = async (browser, job) => {
    const context = await browser.newContext({
        viewport: { width: job.width, height: 900 },
        reducedMotion: 'reduce',
        ignoreHTTPSErrors: true,
    });

    // A stored cookie choice hides the banner (see public/layout/js/components/cookies.js)
    if (!job.banner) {
        await context.addInitScript(() => {
            localStorage.setItem('cookie-choice', JSON.stringify({ v: 1, maps: false, statistics: false }));
        });
    }

    const page = await context.newPage();
    await page.goto(baseUrl + job.path, { waitUntil: 'networkidle' });

    // Load lazy images, then wait for them and for the fonts
    const waitForImages = () =>
        page.evaluate(async () => {
            document.querySelectorAll('img[loading="lazy"]').forEach((img) => (img.loading = 'eager'));
            await Promise.all(
                [...document.images].map((img) =>
                    img.complete
                        ? null
                        : new Promise((resolve) => {
                              img.addEventListener('load', resolve, { once: true });
                              img.addEventListener('error', resolve, { once: true });
                          }),
                ),
            );
            await document.fonts.ready;
        });

    await waitForImages();

    // The phone and SR/EN in the info bar render a few px wider or narrower at random
    // in full-page shots (glyph positioning), so that small area is masked.
    const mask = [page.locator('.info-bar__right')];
    let image;

    if (job.run) {
        const target = await job.run(page);
        await waitForImages();
        image = target ? await target.screenshot({ mask }) : await page.screenshot({ mask });
    } else {
        // Images loading late can move the scroll position, and the header shrinks when scrolled
        await page.evaluate(
            () =>
                new Promise((resolve) => {
                    window.scrollTo(0, 0);
                    requestAnimationFrame(() => requestAnimationFrame(resolve));
                }),
        );
        image = await page.screenshot({ fullPage: true, mask });
    }

    await context.close();

    return image;
};

const compare = (name) => {
    const basePath = path.join(dirs.baseline, name);

    if (!fs.existsSync(basePath)) {
        return 'no baseline';
    }

    const before = PNG.sync.read(fs.readFileSync(basePath));
    const after = PNG.sync.read(fs.readFileSync(path.join(dirs.current, name)));

    if (before.width !== after.width || before.height !== after.height) {
        return `size ${before.width}×${before.height} → ${after.width}×${after.height}`;
    }

    const diff = new PNG({ width: before.width, height: before.height });
    const pixels = pixelmatch(before.data, after.data, diff.data, before.width, before.height, {
        threshold: 0,
    });

    if (pixels > 0) {
        fs.writeFileSync(path.join(dirs.diff, name), PNG.sync.write(diff));
        return `${pixels} px`;
    }

    return null;
};

const browser = await chromium.launch();
const messages = new Map();
let failures = 0;
let next = 0;

const run = async (job) => {
    const name = `${job.name}.png`;

    try {
        fs.writeFileSync(path.join(outDir, name), await shoot(browser, job));
    } catch (error) {
        failures++;
        messages.set(job, `ERROR ${name}: ${error.message.split('\n')[0]}`);
        return;
    }

    if (mode === 'check') {
        const result = compare(name);

        if (result) {
            failures++;
            messages.set(job, `DIFF  ${name}: ${result}`);
        }
    }

    process.stdout.write('.');
};

// A few workers take the next job until none is left
const worker = async () => {
    while (next < jobs.length) {
        await run(jobs[next++]);
    }
};

await Promise.all(Array.from({ length: Math.min(parallel, jobs.length) }, worker));

// Messages in the order of the jobs, not in the order the shots finished
console.log('');
jobs.filter((job) => messages.has(job)).forEach((job) => console.log(messages.get(job)));

await browser.close();

console.log(
    mode === 'baseline'
        ? `\n${jobs.length} shots saved to ${dirs.baseline}`
        : `${jobs.length} shots, ${failures} different${failures ? ` (diff images in ${dirs.diff})` : ''}`,
);

process.exit(failures ? 1 : 0);
