/* =========================================================
   Contact form: field checks, error messages, sending, result
   Markup: templates/form_*.html.twig (form[data-form], .field[data-field])
   CSS: css/elements/form.css

   Without JS the form works as a normal Contao form (action/method).
   The server answers (src/EventListener/FormAjaxListener.php):
     200 + the confirmation from the form settings (.form-result), or empty,
     422 if a field does not validate, anything else = not sent.
   ========================================================= */

const TEXTS = {
    sr: {
        nameEmpty: 'Upišite ime ili nadimak, kako da vam se obraćam.',
        contactEmpty: 'Upišite broj telefona ili e-mail, da bih mogla da vam odgovorim.',
        contactInvalid:
            'Ovo ne izgleda kao broj telefona ni kao e-mail. Na primer: 064 123 4567 ili ime@primer.rs.',
        modeEmpty: 'Izaberite uživo, online ili „Svejedno mi je“.',
        consentEmpty: 'Da bih mogla da vam odgovorim, potrebna je vaša saglasnost za korišćenje podataka.',
        summary: (count) =>
            count === 1
                ? 'Još jedan podatak nedostaje. Označen je iznad.'
                : `Još ${count} podatka nedostaju. Označeni su iznad.`,
        sending: 'Šaljem…',
        // Fallback only: the confirmation normally comes from the server (form settings)
        successTitle: 'Hvala vam, poruka je stigla.',
        successText:
            'Javiću vam se što pre, na broj ili e-mail koji ste ostavili. Ako ne vidite odgovor, proverite i folder za neželjenu poštu.',
        errorTitle: 'Poruka nije poslata.',
        errorText:
            'Nešto nije u redu sa vezom ili serverom. Vaši podaci su sačuvani u formi, pa samo pokušajte ponovo za minut.',
        errorCall: (phone, email) => {
            const ways = [
                phone && `me i pozvati na ${phone}`,
                email && `${phone ? '' : 'mi i '}pisati na ${email}`,
            ].filter(Boolean);
            return ways.length ? ` Možete ${ways.join(' ili ')}.` : '';
        },
        tryAgain: 'Pokušajte ponovo',
    },
    en: {
        nameEmpty: 'Please enter your name or a nickname, so I know how to address you.',
        contactEmpty: 'Please enter a phone number or an email, so I can reply to you.',
        contactInvalid:
            'This doesn’t look like a phone number or an email. For example: +381 64 123 4567 or name@example.com.',
        modeEmpty: 'Please choose in person, online or “Either is fine”.',
        consentEmpty: 'To reply to you, I need your consent to use your details.',
        summary: (count) =>
            count === 1
                ? 'One detail is still missing. It is marked above.'
                : `${count} details are still missing. They are marked above.`,
        sending: 'Sending…',
        successTitle: 'Thank you, your message has arrived.',
        successText:
            'I’ll get back to you as soon as I can, at the number or email you left. If you don’t see a reply, please check your spam folder too.',
        errorTitle: 'The message was not sent.',
        errorText:
            'Something is wrong with the connection or the server. Your details are still in the form, so just try again in a minute.',
        errorCall: (phone, email) => {
            const ways = [phone && `call me at ${phone}`, email && `write to me at ${email}`].filter(Boolean);
            return ways.length ? ` You can also ${ways.join(' or ')}.` : '';
        },
        tryAgain: 'Try again',
    },
};

const TEXT = document.documentElement.lang.startsWith('en') ? TEXTS.en : TEXTS.sr;

const PHONE = /^[+()\d][\d\s()/-]{5,}$/;
const EMAIL = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;

// Checks per data-field value (= name of the form field in Contao); '' means OK
const CHECKS = {
    name: (form) => (form.elements.name.value.trim() ? '' : TEXT.nameEmpty),
    contact: (form) => {
        const value = form.elements.contact.value.trim();

        if (!value) {
            return TEXT.contactEmpty;
        }

        return EMAIL.test(value) || PHONE.test(value) ? '' : TEXT.contactInvalid;
    },
    mode: (form) => (form.querySelector('input[name=mode]:checked') ? '' : TEXT.modeEmpty),
    consent: (form) => (form.querySelector('input[name=consent]:checked') ? '' : TEXT.consentEmpty),
};

const ICONS = {
    ok: '<path d="m5 12.5 4.5 4.5L19 7.5"/>',
    error: '<path d="M12 7v6M12 17v.01"/><circle cx="12" cy="12" r="9"/>',
};

const icon = (name) =>
    `<span class="form-result__icon"><svg class="icon" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">${ICONS[name]}</svg></span>`;

// Phone and e-mail for the error message, taken from the links on the page (header, footer)
const linkText = (selector) => document.querySelector(selector)?.textContent.trim() ?? '';

const setError = (field, message) => {
    field.classList.toggle('is-invalid', Boolean(message));
    field
        .querySelectorAll('input')
        .forEach((input) => input.setAttribute('aria-invalid', String(Boolean(message))));

    const line = field.querySelector('.field__error');

    if (line) {
        line.textContent = message;
    }
};

const checkField = (form, field) => {
    const message = CHECKS[field.getAttribute('data-field')]?.(form) ?? '';
    setError(field, message);

    return !message;
};

const initForm = (form) => {
    const fields = form.querySelectorAll('[data-field]');
    const summary = form.querySelector('[data-summary]');
    const button = form.querySelector('[type=submit]');
    let submitted = false;

    const updateSummary = () => {
        const count = form.querySelectorAll('.field.is-invalid').length;
        form.classList.toggle('has-errors', count > 0);

        if (summary) {
            summary.textContent = count ? TEXT.summary(count) : '';
        }
    };

    const showSuccess = (html) => {
        form.innerHTML = html.includes('form-result')
            ? html
            : `<div class="form-result" role="status" tabindex="-1">${icon('ok')}<h3>${TEXT.successTitle}</h3><p>${TEXT.successText}</p></div>`;
        form.querySelector('.form-result').focus();
    };

    const showFailure = () => {
        form.classList.remove('is-sending');
        button.removeAttribute('aria-busy');
        button.disabled = false;
        button.textContent = TEXT.tryAgain;
        form.querySelector('.form-result')?.remove();

        const call = TEXT.errorCall(linkText('a[href^="tel:"]'), linkText('a[href^="mailto:"]'));
        const box = document.createElement('div');
        box.className = 'form-result form-result--error';
        box.setAttribute('role', 'alert');
        box.innerHTML = `${icon('error')}<p><strong>${TEXT.errorTitle}</strong> ${TEXT.errorText}${call}</p>`;
        button.before(box);
    };

    const send = async () => {
        form.classList.add('is-sending');
        button.setAttribute('aria-busy', 'true');
        button.disabled = true;
        button.textContent = TEXT.sending;

        try {
            const response = await fetch(form.action || location.href, {
                method: 'POST',
                body: new FormData(form),
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
            });

            if (!response.ok) {
                throw new Error(response.status);
            }

            showSuccess(await response.text());
        } catch {
            showFailure();
        }
    };

    /* No errors while the visitor types for the first time; after the first submit
       fields are checked on leaving and on change */
    fields.forEach((field) => {
        field.addEventListener('focusout', () => {
            if (submitted) {
                checkField(form, field);
            }
        });

        field.addEventListener('change', () => {
            if (submitted || field.classList.contains('is-invalid')) {
                checkField(form, field);
            }

            updateSummary();
        });

        field.addEventListener('input', () => {
            if (field.classList.contains('is-invalid')) {
                checkField(form, field);
                updateSummary();
            }
        });
    });

    form.addEventListener('submit', (event) => {
        event.preventDefault();
        submitted = true;

        const invalid = [...fields].filter((field) => !checkField(form, field));
        updateSummary();

        if (invalid.length) {
            invalid[0].querySelector('input')?.focus();
            return;
        }

        send();
    });
};

document.querySelectorAll('[data-form]').forEach(initForm);
