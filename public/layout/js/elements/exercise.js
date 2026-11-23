/* =========================================================
   Interactive exercise "Mali eksperiment", variants A and B
   Markup: templates/content_element/exercise.html.twig · CSS: css/elements/exercise.css

   The template renders every part with its texts; this script only shows the current
   part (hidden), sets the state classes, moves the focus and announces changes.
   prefers-reduced-motion is handled in exercise.css.
   ========================================================= */

const announce = (root, text) => {
    const region = root.querySelector('[data-announce]');

    if (region) {
        region.textContent = text;
    }
};

const focus = (element) => element?.focus({ preventScroll: true });

// Show the element whose data value is the current one, hide the others
const showOnly = (elements, name, current) =>
    elements.forEach((element) => (element.hidden = Number(element.dataset[name]) !== current));

/* ---------- Variant A: choose a topic → questions with the breathing circle → ending ---------- */
const initExerciseA = (root) => {
    const dots = [...root.querySelectorAll('[data-dot]')];
    const stageLabels = [...root.querySelectorAll('[data-stage-label]')];
    const choice = root.querySelector('[data-stage="choose"]');
    const questions = [...root.querySelectorAll('[data-stage="notice"]')];
    const endings = [...root.querySelectorAll('[data-stage="pause"]')];
    let state = { stage: 0, topic: 0, question: 0 };
    let paused = false;

    const render = (withFocus) => {
        const { stage, topic, question } = state;
        const panel = [choice, questions[topic], endings[topic]][stage];

        dots.forEach((dot, i) => {
            dot.classList.toggle('is-current', i === stage);
            dot.classList.toggle('is-done', i < stage);
        });
        stageLabels.forEach((label, i) => (label.hidden = i !== stage));

        choice.hidden = stage !== 0;
        questions.forEach((element, i) => (element.hidden = stage !== 1 || i !== topic));
        endings.forEach((element, i) => (element.hidden = stage !== 2 || i !== topic));

        if (stage === 1) {
            const steps = panel.querySelectorAll('li[data-question]');
            const last = question === steps.length - 1;

            showOnly(panel.querySelectorAll('[data-question]'), 'question', question);
            panel.querySelector('[data-label="next"]').hidden = last;
            panel.querySelector('[data-label="finish"]').hidden = !last;
        }

        if (withFocus) {
            focus(panel.querySelector('[data-focus]:not([hidden])') ?? panel.querySelector('button'));
        }

        announce(root, stageLabels[stage].textContent);
    };

    // The breathing circle can be stopped (WCAG 2.2.2); the choice holds for every topic
    const setPaused = () => {
        root.querySelectorAll('[data-breath]').forEach((circle) =>
            circle.classList.toggle('is-paused', paused),
        );
        root.querySelectorAll('[data-pause]').forEach((button) => {
            button.setAttribute('aria-pressed', String(paused));
            button.querySelector('[data-label="stop"]').hidden = paused;
            button.querySelector('[data-label="start"]').hidden = !paused;
        });
    };

    root.addEventListener('click', (event) => {
        const button = event.target.closest('button');

        if (!button) {
            return;
        }

        if (button.hasAttribute('data-select')) {
            state = { stage: 1, topic: Number(button.dataset.select), question: 0 };
            render(true);
        } else if (button.hasAttribute('data-next')) {
            const steps = questions[state.topic].querySelectorAll('li[data-question]').length;

            if (state.question < steps - 1) {
                state.question++;
            } else {
                state.stage = 2;
            }
            render(true);
        } else if (button.hasAttribute('data-restart')) {
            state = { ...state, stage: 0, question: 0 };
            render(true);
        } else if (button.hasAttribute('data-pause')) {
            paused = !paused;
            setPaused();
        }
    });

    render(false);
};

/* ---------- Variant B: topics on the left, steps on the right that get ticked off ---------- */
const initExerciseB = (root) => {
    const topics = [...root.querySelectorAll('[data-topic]')];
    const panels = [...root.querySelectorAll('[data-panel]')];
    let state = { topic: 0, seen: 0 };

    const render = () => {
        const panel = panels[state.topic];
        const steps = [...panel.querySelectorAll('[data-step]')];

        topics.forEach((button, i) => {
            button.classList.toggle('is-selected', i === state.topic);
            button.setAttribute('aria-pressed', String(i === state.topic));
        });
        panels.forEach((element, i) => (element.hidden = i !== state.topic));

        steps.forEach((step, i) => {
            const done = i < state.seen;

            step.classList.toggle('is-done', done);
            step.classList.toggle('is-next', i === state.seen);
            step.classList.toggle('is-later', i > state.seen);
            if (i > state.seen) {
                step.setAttribute('aria-disabled', 'true');
            } else {
                step.removeAttribute('aria-disabled');
            }
            step.querySelectorAll('[data-done]').forEach((element) => (element.hidden = !done));
        });

        panel.querySelector('[data-end]').hidden = state.seen < steps.length;

        return { panel, steps };
    };

    root.addEventListener('click', (event) => {
        const button = event.target.closest('button');

        if (!button) {
            return;
        }

        if (button.hasAttribute('data-topic')) {
            state = { topic: Number(button.dataset.topic), seen: 0 };
            render();
            focus(button);
            announce(root, button.dataset.message);
        } else if (button.hasAttribute('data-step')) {
            const step = Number(button.dataset.step);

            if (step > state.seen) {
                return;
            }

            state.seen = Math.max(state.seen, step + 1);
            const { panel, steps } = render();
            focus(state.seen >= steps.length ? panel.querySelector('[data-end]') : steps[state.seen]);
            announce(root, button.dataset.message);
        }
    });

    render();
};

document.querySelectorAll('[data-exercise]').forEach((root) => {
    // Without topics the template renders nothing
    if (!root.querySelector('[data-select], [data-topic]')) {
        return;
    }

    if (root.dataset.exercise === 'b') {
        initExerciseB(root);
    } else {
        initExerciseA(root);
    }
});
