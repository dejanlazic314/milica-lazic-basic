/* =========================================================
   Contact form: field checks, error messages, sending, result
   Markup: templates/form_*.html.twig (form[data-form], .field[data-field])
   Without JS the form works as a normal Contao form (action/method).
   The server answers (src/EventListener/FormAjaxListener.php):
     200 + the confirmation from the form settings (.form-result), or empty,
     422 if a field does not validate, anything else = not sent.
   Texts of the messages per language (<html lang>) are in TEXTS and only changed here.
   ========================================================= */
(function () {
  'use strict';
  var TEXTS = {};
  TEXTS.sr = {
    nameEmpty: 'Upišite ime ili nadimak, kako da vam se obraćam.',
    contactEmpty: 'Upišite broj telefona ili e-mail, da bih mogla da vam odgovorim.',
    contactInvalid: 'Ovo ne izgleda kao broj telefona ni kao e-mail. Na primer: 064 123 4567 ili ime@primer.rs.',
    modeEmpty: 'Izaberite uživo, online ili „Svejedno mi je“.',
    consentEmpty: 'Da bih mogla da vam odgovorim, potrebna je vaša saglasnost za korišćenje podataka.',
    summary: function (n) { return n === 1 ? 'Još jedan podatak nedostaje. Označen je iznad.' : 'Još ' + n + ' podatka nedostaju. Označeni su iznad.'; },
    sending: 'Šaljem…',
    // Fallback only: the confirmation normally comes from the server
    successTitle: 'Hvala vam, poruka je stigla.',
    successText: 'Javiću vam se što pre, na broj ili e-mail koji ste ostavili. Ako ne vidite odgovor, proverite i folder za neželjenu poštu.',
    errorTitle: 'Poruka nije poslata.',
    errorText: 'Nešto nije u redu sa vezom ili serverom. Vaši podaci su sačuvani u formi, pa samo pokušajte ponovo za minut.',
    errorCall: function (phone, email) {
      var ways = [phone && 'me i pozvati na ' + phone, email && (phone ? '' : 'mi i ') + 'pisati na ' + email].filter(Boolean);
      return ways.length ? ' Možete ' + ways.join(' ili ') + '.' : '';
    },
    tryAgain: 'Pokušajte ponovo'
  };
  TEXTS.en = {
    nameEmpty: 'Please enter your name or a nickname, so I know how to address you.',
    contactEmpty: 'Please enter a phone number or an email, so I can reply to you.',
    contactInvalid: 'This doesn’t look like a phone number or an email. For example: +381 64 123 4567 or name@example.com.',
    modeEmpty: 'Please choose in person, online or “Either is fine”.',
    consentEmpty: 'To reply to you, I need your consent to use your details.',
    summary: function (n) { return n === 1 ? 'One detail is still missing. It is marked above.' : n + ' details are still missing. They are marked above.'; },
    sending: 'Sending…',
    successTitle: 'Thank you, your message has arrived.',
    successText: 'I’ll get back to you as soon as I can, at the number or email you left. If you don’t see a reply, please check your spam folder too.',
    errorTitle: 'The message was not sent.',
    errorText: 'Something is wrong with the connection or the server. Your details are still in the form, so just try again in a minute.',
    errorCall: function (phone, email) {
      var ways = [phone && 'call me at ' + phone, email && 'write to me at ' + email].filter(Boolean);
      return ways.length ? ' You can also ' + ways.join(' or ') + '.' : '';
    },
    tryAgain: 'Try again'
  };
  var TEXT = TEXTS[(document.documentElement.lang || 'sr').slice(0, 2)] || TEXTS.sr;
  var PHONE = /^[+()\d][\d\s()\/-]{5,}$/;
  var EMAIL = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;

  /* Checks per data-field value (= name of the form field in Contao) */
  var CHECKS = {
    name: function (form) { return form.elements.name.value.trim() ? '' : TEXT.nameEmpty; },
    contact: function (form) {
      var v = form.elements.contact.value.trim();
      if (!v) return TEXT.contactEmpty;
      return EMAIL.test(v) || PHONE.test(v) ? '' : TEXT.contactInvalid;
    },
    mode: function (form) { return form.querySelector('input[name=mode]:checked') ? '' : TEXT.modeEmpty; },
    consent: function (form) { return form.querySelector('input[name=consent]:checked') ? '' : TEXT.consentEmpty; }
  };

  function setError(field, message) {
    var line = field.querySelector('.field__error');
    field.classList.toggle('is-invalid', !!message);
    if (line) line.textContent = message || '';
    field.querySelectorAll('input').forEach(function (i) { i.setAttribute('aria-invalid', message ? 'true' : 'false'); });
  }

  function check(form, field) {
    var test = CHECKS[field.getAttribute('data-field')];
    var message = test ? test(form) : '';
    setError(field, message);
    return !message;
  }

  /* Phone and e-mail for the error message: taken from the links on the page (header, footer) */
  function linkText(selector) {
    var link = document.querySelector(selector);
    return link ? link.textContent.trim() : '';
  }

  function icon(name) { return '<span class="form-result__icon"><svg class="icon" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">' + (name === 'ok' ? '<path d="m5 12.5 4.5 4.5L19 7.5"/>' : '<path d="M12 7v6M12 17v.01"/><circle cx="12" cy="12" r="9"/>') + '</svg></span>'; }

  document.querySelectorAll('[data-form]').forEach(function (form) {
    var fields = form.querySelectorAll('[data-field]');
    var summary = form.querySelector('[data-summary]');
    var button = form.querySelector('[type=submit]');
    var tried = false;

    /* No errors while the visitor types for the first time; after the first submit
       fields are checked on leaving and on change */
    fields.forEach(function (f) {
      f.addEventListener('focusout', function () { if (tried) check(form, f); });
      f.addEventListener('change', function () { if (tried || f.classList.contains('is-invalid')) check(form, f); updateSummary(); });
      f.addEventListener('input', function () { if (f.classList.contains('is-invalid')) { check(form, f); updateSummary(); } });
    });

    function updateSummary() {
      var n = form.querySelectorAll('.field.is-invalid').length;
      form.classList.toggle('has-errors', n > 0);
      if (summary) summary.textContent = n ? TEXT.summary(n) : '';
    }

    form.addEventListener('submit', function (e) {
      e.preventDefault();
      tried = true;
      var first = null;
      fields.forEach(function (f) { if (!check(form, f) && !first) first = f; });
      updateSummary();
      if (first) { var input = first.querySelector('input'); if (input) input.focus(); return; }

      form.classList.add('is-sending');
      button.setAttribute('aria-busy', 'true');
      button.disabled = true;
      button.textContent = TEXT.sending;

      fetch(form.action || location.href, { method: 'POST', body: new FormData(form), headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(function (r) { if (!r.ok) throw new Error(r.status); return r.text(); })
        .then(success)
        .catch(failure);
    });

    function success(html) {
      form.innerHTML = html.indexOf('form-result') !== -1 ? html : '<div class="form-result" role="status" tabindex="-1">' + icon('ok') +
        '<h3>' + TEXT.successTitle + '</h3><p>' + TEXT.successText + '</p></div>';
      form.querySelector('.form-result').focus();
    }

    function failure() {
      form.classList.remove('is-sending');
      button.removeAttribute('aria-busy'); button.disabled = false; button.textContent = TEXT.tryAgain;
      var old = form.querySelector('.form-result'); if (old) old.remove();
      var box = document.createElement('div');
      box.className = 'form-result form-result--error'; box.setAttribute('role', 'alert');
      box.innerHTML = icon('error') + '<p><strong>' + TEXT.errorTitle + '</strong> ' + TEXT.errorText +
        TEXT.errorCall(linkText('a[href^="tel:"]'), linkText('a[href^="mailto:"]')) + '</p>';
      button.parentNode.insertBefore(box, button);
    }
  });
})();
