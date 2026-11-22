// Interactive states for tests/visual/snapshot.mjs: things a plain page shot does not show.
// Each state opens a page, does something, and returns the element to shoot (or null for
// the visible part of the page).

const clickThrough = (selector, steps) => async (page) => {
    const exercise = page.locator(selector).first();
    await exercise.scrollIntoViewIfNeeded();

    for (const step of steps) {
        await exercise.locator(step).first().click();
        await page.waitForTimeout(150);
    }

    return exercise;
};

export default [
    {
        name: 'mobile-menu',
        path: '/',
        widths: [375, 768],
        run: async (page) => {
            await page.click('[data-menu-button]');
            await page.waitForTimeout(150);
            return null;
        },
    },
    {
        name: 'mobile-menu-topics',
        path: '/',
        widths: [375],
        run: async (page) => {
            await page.click('[data-menu-button]');
            await page.locator('[data-mm-sub]').first().click();
            await page.waitForTimeout(150);
            return null;
        },
    },
    {
        name: 'submenu',
        path: '/',
        widths: [1024, 1280],
        run: async (page) => {
            await page.hover('[data-dd] > a');
            await page.waitForTimeout(400);
            return null;
        },
    },
    {
        name: 'scrolled-header',
        path: '/o-meni',
        widths: [375, 1280],
        run: async (page) => {
            await page.mouse.wheel(0, 900);
            await page.waitForTimeout(400);
            return null;
        },
    },
    {
        name: 'hover-card',
        path: '/',
        widths: [1280],
        run: async (page) => {
            const card = page.locator('.topic-card').first();
            await card.scrollIntoViewIfNeeded();
            await card.hover();
            await page.waitForTimeout(400);
            return card.locator('xpath=..');
        },
    },
    {
        name: 'focus-keyboard',
        path: '/',
        widths: [1280],
        run: async (page) => {
            for (let i = 0; i < 3; i++) await page.keyboard.press('Tab');
            await page.waitForTimeout(150);
            return null;
        },
    },
    {
        name: 'exercise-a',
        path: '/',
        widths: [375, 1280],
        run: clickThrough('[data-exercise="a"]', [
            '[data-select]',
            '[data-next]',
            '[data-next]',
            '[data-next]',
        ]),
    },
    {
        name: 'exercise-a-question',
        path: '/',
        widths: [375, 1280],
        run: clickThrough('[data-exercise="a"]', ['[data-select]']),
    },
    {
        name: 'exercise-b',
        path: '/',
        widths: [375, 1280],
        run: clickThrough('[data-exercise="b"]', ['[data-topic="2"]', '[data-step="0"]', '[data-step="1"]']),
    },
    {
        name: 'exercise-b-end',
        path: '/',
        widths: [375, 1280],
        run: clickThrough('[data-exercise="b"]', ['[data-step="0"]', '[data-step="1"]', '[data-step="2"]']),
    },
    {
        name: 'form-errors',
        path: '/kontakt',
        widths: [375, 1280],
        run: async (page) => {
            const form = page.locator('form').first();
            await form.locator('button[type="submit"]').click();
            await page.waitForTimeout(300);
            return form;
        },
    },
    {
        name: 'form-choice',
        path: '/kontakt',
        widths: [1280],
        run: async (page) => {
            const form = page.locator('form').first();
            await form.locator('.choice').nth(1).click();
            await form.locator('.consent input').check();
            return form;
        },
    },
    {
        name: 'cookie-settings',
        path: '/',
        widths: [375, 1280],
        banner: true,
        run: async (page) => {
            await page.click('[data-cookies] [data-action="settings"]');
            await page.waitForTimeout(150);
            return null;
        },
    },
    {
        name: 'faq-open',
        path: '/cesta-pitanja',
        widths: [375, 1280],
        run: async (page) => {
            const question = page.locator('details').first();
            await question.locator('summary').click();
            await page.waitForTimeout(400);
            return question;
        },
    },
    {
        name: 'circles-focus',
        path: '/gestalt-terapija',
        widths: [1280],
        run: async (page) => {
            const circles = page.locator('.circles').first();
            await circles.scrollIntoViewIfNeeded();
            await page.waitForTimeout(500);
            await circles.locator('[data-part="feelings"]').first().hover();
            await page.waitForTimeout(500);
            return circles;
        },
    },
    {
        name: 'map-consent',
        path: '/kontakt',
        widths: [1280],
        run: async (page) => {
            const map = page.locator('.map-consent').first();
            await map.scrollIntoViewIfNeeded();
            return map;
        },
    },
];
