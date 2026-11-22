// Checks of the scripts with animations on (snapshot.mjs runs with reduced motion):
// no JS errors on any page, fade-in while scrolling, circles diagram, question accordion,
// mobile menu, cookie banner and map consent.
//
//   node tests/visual/behaviour.mjs        (BASE_URL as in snapshot.mjs)
//
// Prints one line per check; exit code 1 if a check fails.

import fs from 'node:fs';
import { chromium } from 'playwright';

const baseUrl = process.env.BASE_URL ?? 'https://milica-lazic.ddev.site';
const pages = JSON.parse(fs.readFileSync(new URL('./pages.json', import.meta.url), 'utf8'));
const CHOICE = JSON.stringify({ v: 1, maps: false, statistics: false });

const browser = await chromium.launch();
const results = [];

const check = (name, ok, detail = '') => {
    results.push(ok);
    console.log(`${ok ? 'OK  ' : 'FAIL'}  ${name}${detail ? `: ${detail}` : ''}`);
};

const newPage = async ({ width = 1280, height = 900, choice = true } = {}) => {
    const context = await browser.newContext({ viewport: { width, height }, ignoreHTTPSErrors: true });

    if (choice) {
        await context.addInitScript((value) => localStorage.setItem('cookie-choice', value), CHOICE);
    }

    return context.newPage();
};

// 1. No JS errors on any page
{
    const errors = [];
    const page = await newPage();
    page.on('pageerror', (error) => errors.push(`${page.url()}: ${error.message}`));

    for (const path of pages) {
        await page.goto(baseUrl + path, { waitUntil: 'networkidle' });
    }

    check('no JS errors', errors.length === 0, errors.join(' | '));
}

// 2. Fade in while scrolling: marked below the first screen, all shown after scrolling through
{
    const page = await newPage();
    await page.goto(`${baseUrl}/`, { waitUntil: 'networkidle' });
    const marked = await page.evaluate(() => document.querySelectorAll('.reveal').length);
    await page.evaluate(async () => {
        for (let y = 0; y < document.body.scrollHeight; y += 300) {
            window.scrollTo(0, y);
            await new Promise((resolve) => setTimeout(resolve, 60));
        }
    });
    await page.waitForTimeout(1500);
    const left = await page.evaluate(() => document.querySelectorAll('.reveal').length);
    check('reveal', marked > 0 && left === 0, `${marked} marked, ${left} still hidden`);
}

// 3. Circles diagram assembles, then hover highlights a part
{
    const page = await newPage();
    await page.goto(`${baseUrl}/gestalt-terapija`, { waitUntil: 'networkidle' });
    const diagram = page.locator('.circles').first();
    const waiting = await diagram.evaluate((el) => el.classList.contains('is-waiting'));
    await diagram.scrollIntoViewIfNeeded();
    await page.waitForTimeout(4000);
    const done = await diagram.evaluate((el) => !el.classList.contains('is-playing'));
    await diagram.locator('[data-part="body"]').first().hover();
    const focus = await diagram.getAttribute('data-focus');
    check('circles', waiting && done && focus === 'body', `waiting ${waiting}, done ${done}, focus ${focus}`);
}

// 4. Accordion: opens with an animation, closes again
{
    const page = await newPage();
    await page.goto(`${baseUrl}/cesta-pitanja`, { waitUntil: 'networkidle' });
    const details = page.locator('.faq details').first();
    await details.locator('summary').click();
    const animating = await details.evaluate((el) => el.getAnimations().length > 0);
    await page.waitForTimeout(500);
    const open = await details.evaluate((el) => el.open);
    await details.locator('summary').click();
    await page.waitForTimeout(500);
    const closed = await details.evaluate((el) => !el.open);
    check('accordion', animating && open && closed, `animating ${animating}, open ${open}, closed ${closed}`);
}

// 5. Mobile menu: opens, Esc closes; header shrinks after scrolling
{
    const page = await newPage({ width: 375, height: 800 });
    await page.goto(`${baseUrl}/`, { waitUntil: 'networkidle' });
    await page.click('[data-menu-button]');
    const opened = await page.getAttribute('[data-menu-button]', 'aria-expanded');
    await page.keyboard.press('Escape');
    const closed = await page.getAttribute('[data-menu-button]', 'aria-expanded');
    await page.evaluate(() => window.scrollTo(0, 300));
    await page.waitForTimeout(200);
    const scrolled = await page.evaluate(() =>
        document.querySelector('[data-header]').classList.contains('is-scrolled'),
    );
    check(
        'mobile menu',
        opened === 'true' && closed === 'false' && scrolled,
        `open ${opened}, after Esc ${closed}, scrolled ${scrolled}`,
    );
}

// 6. Cookie banner without a stored choice, "necessary only", map consent, settings again
{
    const page = await newPage({ choice: false });
    await page.goto(`${baseUrl}/kontakt`, { waitUntil: 'networkidle' });
    const banner = await page.isVisible('[data-cookies]');
    await page.click('[data-cookies] [data-action="necessary"]');
    const stored = await page.evaluate(
        () => JSON.parse(localStorage.getItem('cookie-choice'))?.maps === false,
    );
    await page.click('[data-map-show]');
    await page.waitForTimeout(300);
    const map = (await page.locator('[data-map] iframe').count()) === 1;
    await page.click('[data-cookies-open]');
    const settings = await page.isChecked('[data-category="maps"]');
    check(
        'cookies',
        banner && stored && map && settings,
        `banner ${banner}, stored ${stored}, map ${map}, settings ${settings}`,
    );
}

await browser.close();
process.exit(results.every(Boolean) ? 0 : 1);
