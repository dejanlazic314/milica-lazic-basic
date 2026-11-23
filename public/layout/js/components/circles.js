/* =========================================================
   "Three circles" diagram (#gestalt on the home page and the Gestalt page)
   Markup: templates/component/_circles.html.twig · CSS: css/components/circles.css

   1. Assembles part by part once it is well in view (.is-waiting → .is-playing).
   2. Pointing at a circle or its label brings it forward and quiets the other two
      (data-focus). Mouse: on hover; touch: a tap toggles, a tap elsewhere clears.
      The center ("Vi, sada i ovde") shows all three again.
   Decoration only: the diagram is role="img" and its label says everything.
   ========================================================= */

const diagrams = document.querySelectorAll('.circles');
const mouse = window.matchMedia('(hover: hover)');
const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

const partAt = (target) => target.closest('[data-part]')?.getAttribute('data-part') ?? '';

const setFocus = (diagram, part) => {
    // Not while it assembles
    if (diagram.classList.contains('is-waiting') || diagram.classList.contains('is-playing')) {
        return;
    }

    if (part) {
        diagram.setAttribute('data-focus', part);
    } else {
        diagram.removeAttribute('data-focus');
    }
};

const initFocus = (diagram) => {
    diagram.addEventListener('mouseover', (event) => {
        if (mouse.matches) {
            setFocus(diagram, partAt(event.target));
        }
    });

    diagram.addEventListener('mouseleave', () => {
        if (mouse.matches) {
            setFocus(diagram, '');
        }
    });

    diagram.addEventListener('click', (event) => {
        if (mouse.matches) {
            return;
        }

        const part = partAt(event.target);
        setFocus(diagram, part && part !== diagram.getAttribute('data-focus') ? part : '');
    });

    document.addEventListener('click', (event) => {
        if (!mouse.matches && !diagram.contains(event.target)) {
            setFocus(diagram, '');
        }
    });
};

const initAssembly = () => {
    const firstScreen = window.innerHeight;

    const observer = new IntersectionObserver(
        (entries) => {
            entries
                .filter((entry) => entry.isIntersecting)
                .forEach(({ target: diagram }) => {
                    observer.unobserve(diagram);

                    // After the last part (the dot), drop the class so the hover transitions apply
                    diagram
                        .querySelector('.circles__center span')
                        ?.addEventListener('animationend', () => diagram.classList.remove('is-playing'), {
                            once: true,
                        });

                    diagram.classList.replace('is-waiting', 'is-playing');
                });
        },
        { threshold: 0.35 },
    );

    // Only diagrams below the first screen, inside <main>
    [...diagrams]
        .filter((diagram) => diagram.closest('main') && diagram.getBoundingClientRect().top > firstScreen)
        .forEach((diagram) => {
            diagram.classList.add('is-waiting');
            observer.observe(diagram);
        });
};

diagrams.forEach(initFocus);

if (!reducedMotion) {
    initAssembly();
}
