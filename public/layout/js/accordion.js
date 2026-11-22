/* =========================================================
   Questions: smooth opening and closing of <details>
   Markup: templates/content_element/faq_list.html.twig (details > summary.question + .answer)
   CSS: css/components/accordion.css

   Each question opens and closes on its own. Not in the design (there <details> opens
   at once). Without JS, and with prefers-reduced-motion, the native behaviour stays.
   ========================================================= */

const DURATION = 320; // ms, same feel as --hover in tokens.css
const EASING = 'cubic-bezier(.22,.61,.36,1)'; // --ease-out

const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

const initQuestion = (details) => {
    const summary = details.querySelector('summary');
    const answer = details.querySelector('.answer');
    let running = null;

    if (!summary || !answer) {
        return;
    }

    const animate = (from, to, opening) => {
        const options = { duration: DURATION, easing: EASING };

        details.style.overflow = 'hidden';
        running = details.animate({ height: [`${from}px`, `${to}px`] }, options);
        answer.animate({ opacity: opening ? [0, 1] : [1, 0] }, { ...options, fill: 'forwards' });

        running.onfinish = () => {
            running = null;

            if (!opening) {
                details.open = false;
                details.classList.remove('is-closing');
            }

            details.style.removeProperty('overflow');
            answer.getAnimations().forEach((animation) => animation.cancel());
        };
    };

    summary.addEventListener('click', (event) => {
        event.preventDefault();

        // Current height, also in the middle of an animation; then stop it and measure the target
        const from = details.offsetHeight;

        if (running) {
            running.onfinish = null;
            running.cancel();
            running = null;
        }

        const borders = details.offsetHeight - details.clientHeight;

        if (details.open && !details.classList.contains('is-closing')) {
            details.classList.add('is-closing'); // the icon turns back right away (accordion.css)
            animate(from, summary.offsetHeight + borders, false);
        } else {
            details.classList.remove('is-closing');
            details.open = true;
            animate(from, details.offsetHeight, true);
        }
    });
};

if (!reducedMotion) {
    document.querySelectorAll('.faq details').forEach(initQuestion);
}
