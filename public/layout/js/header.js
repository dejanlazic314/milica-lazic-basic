/* =========================================================
   Header: sticky row, mobile menu, desktop submenu
   Markup: templates/frontend_module/site_header.html.twig
   CSS: css/layout/header.css, css/layout/mobile-menu.css
   ========================================================= */

const header = document.querySelector('[data-header]');

const MENU_LABELS = document.documentElement.lang.startsWith('en')
    ? { open: 'Open menu', close: 'Close menu' }
    : { open: 'Otvori meni', close: 'Zatvori meni' };

/* Sticky header gets smaller with a shadow once the page scrolls (.is-scrolled).
   The smaller header changes the page height, and the browser's scroll anchoring then
   moves scrollY by the same amount. With a single threshold this flips the state
   endlessly at that position, so it shrinks after SHRINK_AT and grows back only below
   GROW_AT. The gap must be larger than the height change. */
const SHRINK_AT = 40;
const GROW_AT = 8;

const initStickyHeader = () => {
    let waiting = false;

    const update = () => {
        const y = window.scrollY;
        const scrolled = header.classList.contains('is-scrolled');

        if (!scrolled && y > SHRINK_AT) {
            header.classList.add('is-scrolled');
        } else if (scrolled && y < GROW_AT) {
            header.classList.remove('is-scrolled');
        }

        waiting = false;
    };

    window.addEventListener(
        'scroll',
        () => {
            if (!waiting) {
                requestAnimationFrame(update);
                waiting = true;
            }
        },
        { passive: true },
    );

    update();
};

/* Mobile menu: open and close, a click on a link closes it, scrolling is locked below.
   "S čim radim" inside it opens as an accordion. */
const menuButton = header?.querySelector('[data-menu-button]');
const mobileMenu = header?.querySelector('[data-mobile-menu]');
const subButtons = header?.querySelectorAll('[data-mm-sub]') ?? [];

const setSubmenu = (button, open) => {
    button.setAttribute('aria-expanded', String(open));
    document.getElementById(button.getAttribute('aria-controls'))?.classList.toggle('is-open', open);
};

const setMobileMenu = (open) => {
    menuButton.setAttribute('aria-expanded', String(open));
    menuButton.setAttribute('aria-label', open ? MENU_LABELS.close : MENU_LABELS.open);
    mobileMenu.classList.toggle('is-open', open);
    document.body.classList.toggle('menu-open', open);

    if (!open) {
        subButtons.forEach((button) => setSubmenu(button, false));
    }
};

const initMobileMenu = () => {
    if (!menuButton || !mobileMenu) {
        return;
    }

    menuButton.addEventListener('click', () => {
        setMobileMenu(menuButton.getAttribute('aria-expanded') !== 'true');
    });

    mobileMenu.addEventListener('click', (event) => {
        if (event.target.closest('a')) {
            setMobileMenu(false);
        }
    });

    window.addEventListener('resize', () => {
        if (window.innerWidth >= 1024) {
            setMobileMenu(false);
        }
    });

    subButtons.forEach((button) => {
        button.addEventListener('click', () => {
            setSubmenu(button, button.getAttribute('aria-expanded') !== 'true');
        });
    });
};

/* Desktop submenu: opens on hover and keyboard focus; aria-expanded follows the state.
   On a touch tablet in landscape (≥ 1024 px) the first tap opens it, the second follows the link. */
const dropdowns = header?.querySelectorAll('[data-dd]') ?? [];

const setDropdown = (item, open) => {
    item.classList.toggle('is-open', open);
    item.querySelector(':scope > a')?.setAttribute('aria-expanded', String(open));
};

const initDropdowns = () => {
    const touch = window.matchMedia('(hover: none)');

    dropdowns.forEach((item) => {
        item.addEventListener('mouseenter', () => setDropdown(item, true));
        item.addEventListener('mouseleave', () => setDropdown(item, false));
        item.addEventListener('focusin', () => setDropdown(item, true));
        item.addEventListener('focusout', (event) => {
            if (!item.contains(event.relatedTarget)) {
                setDropdown(item, false);
            }
        });

        item.querySelector(':scope > a')?.addEventListener('click', (event) => {
            if (touch.matches && !item.classList.contains('is-open')) {
                event.preventDefault();
                setDropdown(item, true);
            }
        });
    });
};

/* Esc closes the submenu (focus goes back to its link) and the mobile menu */
const initEscape = () => {
    document.addEventListener('keydown', (event) => {
        if (event.key !== 'Escape') {
            return;
        }

        dropdowns.forEach((item) => {
            if (!item.classList.contains('is-open') && !item.contains(document.activeElement)) {
                return;
            }

            setDropdown(item, false);
            item.querySelector(':scope > a')?.focus();

            if (item.querySelector('.nav-dd-panel').contains(document.activeElement)) {
                document.activeElement.blur();
            }
        });

        if (menuButton?.getAttribute('aria-expanded') === 'true') {
            setMobileMenu(false);
            menuButton.focus();
        }
    });
};

if (header) {
    initStickyHeader();
    initMobileMenu();
    initDropdowns();
    initEscape();
}
