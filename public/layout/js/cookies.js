/* =========================================================
   Cookie consent – a small version without libraries.
   Categories: necessary (always), maps (Google Maps), statistics (only if it is ever added).
   The choice is kept in localStorage 'cookie-choice' {v:1, maps:bool, statistics:bool, date}.
   Markup: templates/component/_cookie_banner.html.twig. Any [data-cookies-open] opens the settings again.
   ========================================================= */
(function () {
  'use strict';
  var KEY = 'cookie-choice', VERSION = 1;
  var MAP_TITLE = { sr: 'Mapa: lokacija ordinacije', en: 'Map: location of the practice' };
  var lang = (document.documentElement.lang || 'sr').slice(0, 2);
  var banner = document.querySelector('[data-cookies]');
  var confirmation = document.querySelector('[data-cookies-confirm]');

  function read() { try { var x = JSON.parse(localStorage.getItem(KEY)); return x && x.v === VERSION ? x : null; } catch (e) { return null; } }
  function save(choice) {
    choice.v = VERSION; choice.date = new Date().toISOString();
    try { localStorage.setItem(KEY, JSON.stringify(choice)); } catch (e) {}
    apply(choice);
    if (banner) banner.hidden = true;
    reserveSpace();
    if (confirmation) { confirmation.hidden = false; setTimeout(function () { confirmation.hidden = true; }, 3500); }
    var trigger = document.querySelector('[data-cookies-open][aria-expanded="true"]');
    if (trigger) { trigger.setAttribute('aria-expanded', 'false'); trigger.focus(); }
  }
  function apply(choice) {
    if (choice && choice.maps) document.querySelectorAll('[data-map]').forEach(loadMap);
    document.dispatchEvent(new CustomEvent('cookies:choice', { detail: choice }));
  }
  function loadMap(el) {
    if (el.querySelector('iframe')) return;
    var f = document.createElement('iframe');
    f.src = el.getAttribute('data-src'); f.title = MAP_TITLE[lang] || MAP_TITLE.sr; f.loading = 'lazy'; f.referrerPolicy = 'no-referrer-when-downgrade';
    el.appendChild(f);
    var p = el.querySelector('.map-consent__message'); if (p) p.hidden = true;
  }
  function reserveSpace() {
    /* WCAG 2.4.11: the focused element must not be hidden under the banner */
    var h = banner && !banner.hidden ? banner.getBoundingClientRect().height + 24 : 0;
    document.documentElement.style.scrollPaddingBottom = h ? h + 'px' : '';
  }
  function showSettings(on) {
    if (!banner) return;
    banner.querySelector('[data-cookies-first]').hidden = on;
    banner.querySelector('[data-cookies-settings]').hidden = !on;
    var choice = read() || {};
    banner.querySelectorAll('[data-category]').forEach(function (i) { i.checked = !!choice[i.getAttribute('data-category')]; });
  }
  function open(settings) {
    if (!banner) return;
    banner.hidden = false; showSettings(!!settings); reserveSpace();
    var heading = banner.querySelector(settings ? '[data-cookies-settings] h2' : '[data-cookies-first] h2');
    if (heading) { heading.setAttribute('tabindex', '-1'); heading.focus(); }
  }

  if (banner) {
    banner.addEventListener('click', function (e) {
      var b = e.target.closest('button'); if (!b) return;
      var a = b.getAttribute('data-action');
      if (a === 'all') save({ maps: true, statistics: true });
      else if (a === 'necessary') save({ maps: false, statistics: false });
      else if (a === 'settings') showSettings(true);
      else if (a === 'back') showSettings(false);
      else if (a === 'save') {
        var choice = {};
        banner.querySelectorAll('[data-category]').forEach(function (i) { choice[i.getAttribute('data-category')] = i.checked; });
        save(choice);
      }
    });
    banner.addEventListener('keydown', function (e) { if (e.key === 'Escape' && read()) { banner.hidden = true; } });
  }

  /* Footer link and the Cookies page: open the settings again */
  document.addEventListener('click', function (e) {
    var o = e.target.closest('[data-cookies-open]'); if (!o) return;
    e.preventDefault(); o.setAttribute('aria-expanded', 'true'); open(true);
  });

  /* Map: the "Show map" button is consent for the "maps" category only */
  document.addEventListener('click', function (e) {
    var m = e.target.closest('[data-map-show]'); if (!m) return;
    var choice = read() || { statistics: false }; choice.maps = true; save(choice);
  });

  var existing = read();
  if (existing) apply(existing); else if (banner) open(false);
})();
