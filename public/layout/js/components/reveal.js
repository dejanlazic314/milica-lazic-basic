/* =========================================================
   Fade in while scrolling (design README: "on headings, cards and blocks")
   CSS: css/components/reveal.css

   The class is added here, not in the markup, so nothing stays hidden without JS,
   and only below the first screen, so nothing visible blinks on load. Elements that
   enter together (a row of cards) follow each other a little (STAGGER). After the
   fade the class is removed again, so the hover transitions (layer "states") apply.
   ========================================================= */

const TARGETS = [
    '.head',
    '.section-intro',
    '.card',
    '.step',
    '.band',
    '.featured',
    '.bio',
    '.about__photo',
    '.quote__content',
    '.form',
].join(',');

const STAGGER = 0.08; // seconds between elements that enter together
const MAX_DELAY = 4; // at most 4 × STAGGER

const main = document.querySelector('main');
const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

const finish = (element) => {
    element.classList.remove('reveal', 'is-visible');
    element.style.removeProperty('--reveal-delay');
};

const show = (element, index) => {
    if (index > 0) {
        element.style.setProperty('--reveal-delay', `${Math.min(index, MAX_DELAY) * STAGGER}s`);
    }

    element.addEventListener('transitionend', function end(event) {
        if (event.target === element && event.propertyName === 'opacity') {
            element.removeEventListener('transitionend', end);
            finish(element);
        }
    });

    element.classList.add('is-visible');
};

const initReveal = () => {
    const firstScreen = window.innerHeight;

    // Not nested in another target, and starting below the first screen
    const elements = [...main.querySelectorAll(TARGETS)].filter(
        (element) =>
            !element.parentElement.closest(TARGETS) && element.getBoundingClientRect().top > firstScreen,
    );

    const observer = new IntersectionObserver(
        (entries) => {
            entries
                .filter((entry) => entry.isIntersecting)
                .forEach((entry, index) => {
                    observer.unobserve(entry.target);
                    show(entry.target, index);
                });
        },
        { rootMargin: '0px 0px -8% 0px', threshold: 0.1 },
    );

    elements.forEach((element) => {
        element.classList.add('reveal');
        observer.observe(element);
    });
};

if (main && !reducedMotion) {
    initReveal();
}
