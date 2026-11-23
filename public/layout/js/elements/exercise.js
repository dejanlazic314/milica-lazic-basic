/* =========================================================
   Interactive exercise "Mali eksperiment", variants A and B
   Markup: templates/content_element/exercise.html.twig · CSS: css/elements/exercise.css

   The texts come from the element as JSON (entered in the back end):
     data-content  heading, ending, contact button
     data-topics   [{choice, image, steps[], reflection, link, linkText}]
     data-texts    fixed labels (translations/site.*.yaml, exercise.*)
   No libraries; prefers-reduced-motion is handled in exercise.css.
   ========================================================= */

const ESCAPES = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;' };

const escape = (text) => String(text).replace(/[&<>"]/g, (char) => ESCAPES[char]);

// "Šta je kod vas *sada* prisutno?" → "…<em>sada</em>…" (as in the other headings)
const withEmphasis = (text) => escape(text).replace(/\*([^*]+)\*/g, '<em>$1</em>');

// "pitanje {n} od {total}" → "pitanje 2 od 3"
const fill = (text, values) => text.replace(/\{(\w+)\}/g, (match, key) => values[key] ?? match);

const readJson = (element, name) => {
    try {
        return JSON.parse(element.getAttribute(name)) ?? {};
    } catch {
        return {};
    }
};

const announce = (element, text) => {
    const region = element.querySelector('[data-announce]');

    if (region) {
        region.textContent = text;
    }
};

/* ---------- Variant A: choose a topic → questions with the breathing circle → reflection ---------- */
const initExerciseA = (element, { content, topics, texts }) => {
    const STAGES = 3; // choose, notice, pause
    let state = { stage: 0, choice: 0, question: 0 };
    let paused = false;

    const dots = () =>
        [0, 1, 2]
            .map((i) => {
                const status = i === state.stage ? 'is-current' : i < state.stage ? 'is-done' : '';
                return `<span class="exercise-a__dot ${status}"></span>`;
            })
            .join('');

    const choiceStage = () =>
        '<div class="exercise-a__choice fade-up">' +
        topics
            .map(
                (topic, i) =>
                    `<button class="option exercise-a__option" type="button" data-select="${i}">` +
                    `<img src="${escape(topic.image)}" alt="" loading="lazy"><span>${escape(topic.choice)}</span></button>`,
            )
            .join('') +
        '</div>';

    const questionStage = (topic) => {
        const last = state.question >= topic.steps.length - 1;
        const counter = fill(texts.question, { n: state.question + 1, total: topic.steps.length });

        return (
            '<div class="exercise-a__question fade-up">' +
            `<div class="exercise-a__breath${paused ? ' is-paused' : ''}" aria-hidden="true">` +
            `<span class="breath"></span><span class="breath breath--inner"></span><em>${texts.breath}</em></div>` +
            '<div class="exercise-a__text">' +
            `<p class="exercise-a__meta">${escape(topic.choice)} · ${counter}</p>` +
            `<p class="exercise-a__q fade-up" tabindex="-1" data-focus>${escape(topic.steps[state.question])}</p>` +
            `<p class="exercise-a__hint">${texts.hint}</p>` +
            `<button class="exercise-a__pause" type="button" data-pause aria-pressed="${paused}">` +
            `${paused ? texts.breathStart : texts.breathStop}</button>` +
            '<div class="exercise__actions">' +
            `<button class="btn btn-dark" type="button" data-next>${last ? texts.finish : texts.next}</button>` +
            `<button class="btn btn-outline" type="button" data-restart>${texts.other}</button>` +
            '</div></div></div>'
        );
    };

    const endStage = (topic) =>
        '<div class="exercise-a__end fade-up">' +
        '<div class="exercise-a__end-text">' +
        `<p class="eyebrow">${escape(content.endEyebrow)}</p>` +
        `<h3 tabindex="-1" data-focus>${escape(content.endTitle)}</h3>` +
        `<p>${escape(topic.reflection)}</p>` +
        '<div class="exercise__actions">' +
        `<a class="btn btn-apricot" href="${escape(content.contactUrl)}">${escape(content.contactText)}</a>` +
        `<a class="btn btn-outline-light" href="${escape(topic.link)}">${escape(topic.linkText)} →</a>` +
        `<button class="link-button" type="button" data-restart>${texts.again}</button>` +
        '</div></div>' +
        `<img class="exercise-a__image" src="${escape(topic.image)}" alt="">` +
        '</div>';

    const render = (focus) => {
        const topic = topics[state.choice];
        const stageName = `${texts.stages[state.stage]} · ${fill(texts.stage, { n: state.stage + 1 })}`;
        const body = [choiceStage, questionStage, endStage][state.stage](topic);

        element.innerHTML =
            '<div class="exercise-a__inner">' +
            '<div class="exercise__intro">' +
            `<p class="eyebrow">${escape(content.eyebrow)}</p>` +
            `<h2>${withEmphasis(content.title)}</h2>` +
            `<p>${escape(content.text)}</p>` +
            '</div>' +
            `<div class="exercise-a__steps" aria-hidden="true">${dots()}<span>${stageName}</span></div>` +
            body +
            `<p class="exercise__note">${texts.note}</p>` +
            '<p class="sr-only" aria-live="polite" data-announce></p>' +
            '</div>';

        if (focus) {
            (element.querySelector('[data-focus]') ?? element.querySelector('button'))?.focus({
                preventScroll: true,
            });
        }

        announce(element, stageName);
    };

    element.addEventListener('click', (event) => {
        const button = event.target.closest('button');

        if (!button) {
            return;
        }

        if (button.hasAttribute('data-select')) {
            state = { stage: 1, choice: Number(button.getAttribute('data-select')), question: 0 };
            render(true);
        } else if (button.hasAttribute('data-next')) {
            if (state.question < topics[state.choice].steps.length - 1) {
                state.question++;
            } else {
                state.stage = STAGES - 1;
            }
            render(true);
        } else if (button.hasAttribute('data-restart')) {
            state.stage = 0;
            state.question = 0;
            render(true);
        } else if (button.hasAttribute('data-pause')) {
            paused = !paused;
            render(false);
            element.querySelector('[data-pause]')?.focus();
        }
    });

    render(false);
};

/* ---------- Variant B: topics on the left, steps on the right that get ticked off ---------- */
const initExerciseB = (element, { content, topics, texts }) => {
    let state = { choice: 0, seen: 0 };

    const topicButtons = () =>
        topics
            .map((topic, i) => {
                const selected = i === state.choice;

                return (
                    `<button class="option exercise-b__topic${selected ? ' is-selected' : ''}" type="button" aria-pressed="${selected}" data-topic="${i}">` +
                    `<img src="${escape(topic.image)}" alt="" loading="lazy"><span>${escape(topic.choice)}</span></button>`
                );
            })
            .join('');

    const stepButtons = (topic) =>
        topic.steps
            .map((step, i) => {
                const done = i < state.seen;
                const status = done ? 'is-done' : i === state.seen ? 'is-next' : 'is-later';

                return (
                    `<li><button class="option exercise-b__step ${status}" type="button" data-step="${i}"${i > state.seen ? ' aria-disabled="true"' : ''}>` +
                    `<span class="exercise-b__label" aria-hidden="true">${done ? '✓' : ''}</span>` +
                    `<span class="exercise-b__text">${escape(step)}</span>` +
                    `<span class="sr-only">${done ? ` ${texts.done}` : ''}</span></button></li>`
                );
            })
            .join('');

    const ending = (topic) =>
        `<div class="exercise-b__end fade-up" tabindex="-1" data-end>` +
        `<p><strong>${escape(content.endTitle)}</strong> ${escape(topic.reflection)}</p>` +
        '<div class="exercise__actions">' +
        `<a class="link-arrow" href="${escape(content.contactUrl)}">${escape(content.contactText)} →</a>` +
        `<a class="link-arrow" href="${escape(topic.link)}">${escape(topic.linkText)} →</a>` +
        '</div></div>';

    const render = (focusSelector) => {
        const topic = topics[state.choice];
        const finished = state.seen >= topic.steps.length;

        element.innerHTML =
            '<div class="exercise-b__inner">' +
            '<div class="exercise-b__left">' +
            `<p class="eyebrow">${escape(content.eyebrow)}</p>` +
            `<h2>${withEmphasis(content.title)}</h2>` +
            `<div class="exercise-b__topics">${topicButtons()}</div>` +
            `<p class="exercise__note">${texts.note}</p>` +
            '</div>' +
            '<div class="exercise-b__right">' +
            `<img class="exercise-b__background" src="${escape(topic.image)}" alt="">` +
            `<p class="exercise-b__instructions">${escape(content.text)}</p>` +
            `<ol class="exercise-b__list">${stepButtons(topic)}</ol>` +
            (finished ? ending(topic) : '') +
            '</div>' +
            '<p class="sr-only" aria-live="polite" data-announce></p>' +
            '</div>';

        if (focusSelector) {
            element.querySelector(focusSelector)?.focus({ preventScroll: true });
        }
    };

    element.addEventListener('click', (event) => {
        const button = event.target.closest('button');

        if (!button) {
            return;
        }

        if (button.hasAttribute('data-topic')) {
            state = { choice: Number(button.getAttribute('data-topic')), seen: 0 };
            render(`[data-topic="${state.choice}"]`);
            announce(element, fill(texts.selected, { choice: topics[state.choice].choice }));
        } else if (button.hasAttribute('data-step')) {
            const step = Number(button.getAttribute('data-step'));
            const total = topics[state.choice].steps.length;

            if (step > state.seen) {
                return;
            }

            state.seen = Math.max(state.seen, step + 1);
            render(state.seen >= total ? '[data-end]' : `[data-step="${Math.min(state.seen, total - 1)}"]`);
            announce(element, fill(texts.stepDone, { n: step + 1, total }));
        }
    });

    render(null);
};

document.querySelectorAll('[data-exercise]').forEach((element) => {
    const data = {
        content: readJson(element, 'data-content'),
        topics: readJson(element, 'data-topics'),
        texts: readJson(element, 'data-texts'),
    };

    // Topics without steps cannot be shown; without topics the section stays empty
    data.topics = Array.isArray(data.topics) ? data.topics.filter((topic) => topic.steps?.length) : [];

    if (!data.topics.length) {
        return;
    }

    if (element.getAttribute('data-exercise') === 'b') {
        initExerciseB(element, data);
    } else {
        initExerciseA(element, data);
    }
});
