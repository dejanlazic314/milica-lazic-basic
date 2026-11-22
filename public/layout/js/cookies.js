/* =========================================================
   Cookie consent, a small version without libraries
   Markup: templates/component/_cookie_banner.html.twig · CSS: css/components/cookies.css

   Categories: necessary (always), maps (Google Maps), statistics (only if it is ever added).
   The choice is kept in localStorage 'cookie-choice': {v: 1, maps, statistics, date}.
   Any [data-cookies-open] (footer, Cookies page) opens the settings again.
   ========================================================= */

const KEY = 'cookie-choice';
const VERSION = 1;
const CONFIRMATION_TIME = 3500; // ms

const MAP_TITLE = document.documentElement.lang.startsWith('en')
    ? 'Map: location of the practice'
    : 'Mapa: lokacija ordinacije';

const banner = document.querySelector('[data-cookies]');
const confirmation = document.querySelector('[data-cookies-confirm]');

const readChoice = () => {
    try {
        const choice = JSON.parse(localStorage.getItem(KEY));
        return choice?.v === VERSION ? choice : null;
    } catch {
        return null;
    }
};

const loadMap = (element) => {
    if (element.querySelector('iframe')) {
        return;
    }

    const iframe = document.createElement('iframe');
    iframe.src = element.getAttribute('data-src');
    iframe.title = MAP_TITLE;
    iframe.loading = 'lazy';
    iframe.referrerPolicy = 'no-referrer-when-downgrade';
    element.appendChild(iframe);

    const message = element.querySelector('.map-consent__message');

    if (message) {
        message.hidden = true;
    }
};

const applyChoice = (choice) => {
    if (choice?.maps) {
        document.querySelectorAll('[data-map]').forEach(loadMap);
    }

    document.dispatchEvent(new CustomEvent('cookies:choice', { detail: choice }));
};

// WCAG 2.4.11: an element with keyboard focus must not be hidden under the banner
const reserveSpace = () => {
    const height = banner && !banner.hidden ? banner.getBoundingClientRect().height + 24 : 0;
    document.documentElement.style.scrollPaddingBottom = height ? `${height}px` : '';
};

const saveChoice = (choice) => {
    const saved = { ...choice, v: VERSION, date: new Date().toISOString() };

    try {
        localStorage.setItem(KEY, JSON.stringify(saved));
    } catch {
        // private mode or storage blocked: the choice applies to this page only
    }

    applyChoice(saved);

    if (banner) {
        banner.hidden = true;
    }

    reserveSpace();

    if (confirmation) {
        confirmation.hidden = false;
        setTimeout(() => (confirmation.hidden = true), CONFIRMATION_TIME);
    }

    const trigger = document.querySelector('[data-cookies-open][aria-expanded="true"]');

    if (trigger) {
        trigger.setAttribute('aria-expanded', 'false');
        trigger.focus();
    }
};

const showSettings = (settings) => {
    const choice = readChoice() ?? {};

    banner.querySelector('[data-cookies-first]').hidden = settings;
    banner.querySelector('[data-cookies-settings]').hidden = !settings;
    banner.querySelectorAll('[data-category]').forEach((input) => {
        input.checked = Boolean(choice[input.getAttribute('data-category')]);
    });
};

const openBanner = (settings) => {
    if (!banner) {
        return;
    }

    banner.hidden = false;
    showSettings(settings);
    reserveSpace();

    const heading = banner.querySelector(settings ? '[data-cookies-settings] h2' : '[data-cookies-first] h2');

    if (heading) {
        heading.setAttribute('tabindex', '-1');
        heading.focus();
    }
};

// What each banner button does (data-action in the template)
const ACTIONS = {
    all: () => saveChoice({ maps: true, statistics: true }),
    necessary: () => saveChoice({ maps: false, statistics: false }),
    settings: () => showSettings(true),
    back: () => showSettings(false),
    save: () => {
        const choice = {};

        banner.querySelectorAll('[data-category]').forEach((input) => {
            choice[input.getAttribute('data-category')] = input.checked;
        });

        saveChoice(choice);
    },
};

if (banner) {
    banner.addEventListener('click', (event) => {
        const action = event.target.closest('button')?.getAttribute('data-action');
        ACTIONS[action]?.();
    });

    banner.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && readChoice()) {
            banner.hidden = true;
        }
    });
}

document.addEventListener('click', (event) => {
    // Footer link and the Cookies page: open the settings again
    const opener = event.target.closest('[data-cookies-open]');

    if (opener) {
        event.preventDefault();
        opener.setAttribute('aria-expanded', 'true');
        openBanner(true);
    }

    // Map: the "Show map" button is consent for the "maps" category only
    if (event.target.closest('[data-map-show]')) {
        saveChoice({ statistics: false, ...readChoice(), maps: true });
    }
});

const existing = readChoice();

if (existing) {
    applyChoice(existing);
} else {
    openBanner(false);
}
