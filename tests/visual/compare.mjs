// Visual comparison of one section: local site vs. a static design page.
//
//   node tests/visual/compare.mjs <selector> <site-url> <design-url> [name]
//
// Screenshots the first element matching <selector> on both pages at 375, 768 and
// 1280 px, writes them to test-results/visual/<name>-<width>-{site,design,side}.png
// and prints the size and the number of differing pixels. The cookie banner is
// hidden and the sticky header made static on both pages. Design pages can be
// file:// URLs.

import fs from 'node:fs';
import path from 'node:path';
import { chromium } from 'playwright';

const [selector, siteUrl, designUrl, name = 'section'] = process.argv.slice(2);

if (!selector || !siteUrl || !designUrl) {
    console.error('Usage: node tests/visual/compare.mjs <selector> <site-url> <design-url> [name]');
    process.exit(1);
}

const widths = (process.env.WIDTHS ?? '375,768,1280').split(',').map(Number);
const outDir = path.resolve('test-results/visual');
fs.mkdirSync(outDir, { recursive: true });

const browser = await chromium.launch();
const helper = await browser.newPage();
let failed = false;

for (const width of widths) {
    const shots = [];

    for (const url of [siteUrl, designUrl]) {
        const context = await browser.newContext({
            viewport: { width, height: 900 },
            reducedMotion: 'reduce',
            ignoreHTTPSErrors: true,
        });
        const page = await context.newPage();
        await page.goto(url, { waitUntil: 'networkidle' });
        await page.evaluate(() => document.fonts.ready);
        // Hide the cookie banner; keep the sticky header from covering the section
        await page.addStyleTag({
            content: '[data-cookies]{display:none!important}[data-header]{position:relative!important}',
        });
        // Bring the section into view and wait for its (lazy) images
        const section = page.locator(selector).first();
        await section.scrollIntoViewIfNeeded();
        // Tall selectors (e.g. "main"): scroll through so lazy images start loading
        await section.evaluate(async (el) => {
            const end = el.getBoundingClientRect().bottom + window.scrollY;
            for (let y = window.scrollY; y < end; y += 600) {
                window.scrollTo(0, y);
                await new Promise((r) => setTimeout(r, 50));
            }
            el.scrollIntoView();
        });
        await section.evaluate((el) =>
            Promise.race([
                new Promise((r) => setTimeout(r, 5000)),
                Promise.all(
                    [...el.querySelectorAll('img')].map((img) =>
                        img.complete
                            ? null
                            : new Promise((resolve) => {
                                  img.addEventListener('load', resolve, { once: true });
                                  img.addEventListener('error', resolve, { once: true });
                              }),
                    ),
                ),
            ]),
        );
        shots.push(await section.screenshot());
        await context.close();
    }

    const [site, design] = shots;
    fs.writeFileSync(path.join(outDir, `${name}-${width}-site.png`), site);
    fs.writeFileSync(path.join(outDir, `${name}-${width}-design.png`), design);

    // Compare in the browser (no image libraries needed) and build a side-by-side image
    const result = await helper.evaluate(
        async ([a, b]) => {
            const load = (src) =>
                new Promise((resolve) => {
                    const img = new Image();
                    img.onload = () => resolve(img);
                    img.src = src;
                });
            const [imgA, imgB] = await Promise.all([load(a), load(b)]);
            const pixels = (img) => {
                const canvas = new OffscreenCanvas(img.width, img.height);
                const ctx = canvas.getContext('2d');
                ctx.drawImage(img, 0, 0);
                return ctx.getImageData(0, 0, img.width, img.height).data;
            };

            let diff = null;

            if (imgA.width === imgB.width && imgA.height === imgB.height) {
                const pa = pixels(imgA);
                const pb = pixels(imgB);
                diff = 0;

                for (let i = 0; i < pa.length; i += 4) {
                    if (
                        Math.abs(pa[i] - pb[i]) +
                            Math.abs(pa[i + 1] - pb[i + 1]) +
                            Math.abs(pa[i + 2] - pb[i + 2]) >
                        30
                    ) {
                        diff++;
                    }
                }
            }

            const gap = 24;
            const side = new OffscreenCanvas(
                imgA.width + gap + imgB.width,
                Math.max(imgA.height, imgB.height),
            );
            const ctx = side.getContext('2d');
            ctx.fillStyle = '#f00';
            ctx.fillRect(0, 0, side.width, side.height);
            ctx.drawImage(imgA, 0, 0);
            ctx.drawImage(imgB, imgA.width + gap, 0);
            const blob = await side.convertToBlob({ type: 'image/png' });
            const buffer = new Uint8Array(await blob.arrayBuffer());

            return {
                diff,
                sizeA: `${imgA.width}x${imgA.height}`,
                sizeB: `${imgB.width}x${imgB.height}`,
                side: Array.from(buffer),
            };
        },
        shots.map((s) => `data:image/png;base64,${s.toString('base64')}`),
    );

    fs.writeFileSync(path.join(outDir, `${name}-${width}-side.png`), Buffer.from(result.side));

    if (null === result.diff) {
        failed = true;
        console.log(`${name} @${width}: size differs, site ${result.sizeA} vs design ${result.sizeB}`);
    } else {
        console.log(
            `${name} @${width}: ${result.sizeA}, ${result.diff ? `${result.diff} px differ` : 'identical'}`,
        );
    }
}

await browser.close();
process.exit(failed ? 1 : 0);
