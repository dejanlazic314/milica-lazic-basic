/* =========================================================
   Header E – behaviour (no libraries)
   1. Sticky header gets smaller with a shadow once the page scrolls (.is-scrolled)
   2. Mobile menu: open/close, Esc, a click on a link closes it, scroll locked below
   3. Accordion "S čim radim" in the mobile menu
   4. Desktop submenu: Esc closes, aria-expanded follows the state, touch on tablets
   5. Headings, cards and blocks fade in while scrolling (.reveal), the circles diagram assembles
   6. Circles diagram: hover (tap on touch) highlights one part
   Load with defer at the end of the page.
   ========================================================= */
(function () {
  'use strict';
  var header = document.querySelector('[data-header]');
  if (!header) return;

  /* 1. Sticky header. The smaller header changes the page height, and the browser's
     scroll anchoring then moves scrollY by the same amount. With a single threshold
     this flips the state endlessly at that position, so it shrinks after SHRINK_AT
     and grows back only below GROW_AT. The gap must be larger than the height change. */
  var SHRINK_AT = 40;
  var GROW_AT = 8;
  var tick = false;
  function checkScroll() {
    var y = window.scrollY;
    var scrolled = header.classList.contains('is-scrolled');
    if (!scrolled && y > SHRINK_AT) header.classList.add('is-scrolled');
    else if (scrolled && y < GROW_AT) header.classList.remove('is-scrolled');
    tick = false;
  }
  window.addEventListener('scroll', function () {
    if (!tick) { window.requestAnimationFrame(checkScroll); tick = true; }
  }, { passive: true });
  checkScroll();

  /* 2. Mobile menu */
  var MENU_LABELS = (document.documentElement.lang || '').slice(0, 2) === 'en' ? ['Open menu', 'Close menu'] : ['Otvori meni', 'Zatvori meni'];
  var button = header.querySelector('[data-menu-button]');
  var menu = header.querySelector('[data-mobile-menu]');
  function setMenu(open) {
    if (!button || !menu) return;
    button.setAttribute('aria-expanded', String(open));
    button.setAttribute('aria-label', MENU_LABELS[open ? 1 : 0]);
    menu.classList.toggle('is-open', open);
    document.body.classList.toggle('menu-open', open);
    if (!open) closeSubmenus();
  }
  if (button && menu) {
    button.addEventListener('click', function () {
      setMenu(button.getAttribute('aria-expanded') !== 'true');
    });
    menu.addEventListener('click', function (e) {
      if (e.target.closest('a')) setMenu(false);
    });
    window.addEventListener('resize', function () {
      if (window.innerWidth >= 1024) setMenu(false);
    });
  }

  /* 3. Accordion in the mobile menu */
  var subButtons = header.querySelectorAll('[data-mm-sub]');
  function closeSubmenus() {
    subButtons.forEach(function (b) {
      b.setAttribute('aria-expanded', 'false');
      var panel = document.getElementById(b.getAttribute('aria-controls'));
      if (panel) panel.classList.remove('is-open');
    });
  }
  subButtons.forEach(function (b) {
    b.addEventListener('click', function () {
      var open = b.getAttribute('aria-expanded') !== 'true';
      b.setAttribute('aria-expanded', String(open));
      var panel = document.getElementById(b.getAttribute('aria-controls'));
      if (panel) panel.classList.toggle('is-open', open);
    });
  });

  /* 4. Desktop submenu */
  var dropdowns = header.querySelectorAll('[data-dd]');
  dropdowns.forEach(function (item) {
    var link = item.querySelector(':scope > a');
    function setOpen(open) { item.classList.toggle('is-open', open); if (link) link.setAttribute('aria-expanded', String(open)); }
    item.addEventListener('mouseenter', function () { setOpen(true); });
    item.addEventListener('mouseleave', function () { setOpen(false); });
    item.addEventListener('focusin', function () { setOpen(true); });
    item.addEventListener('focusout', function (e) { if (!item.contains(e.relatedTarget)) setOpen(false); });
    /* Tablet in landscape (touch, ≥1024 px): the first tap opens, the second follows the link */
    if (link) link.addEventListener('click', function (e) {
      if (window.matchMedia('(hover: none)').matches && !item.classList.contains('is-open')) {
        e.preventDefault(); setOpen(true);
      }
    });
  });

  /* Esc closes everything */
  document.addEventListener('keydown', function (e) {
    if (e.key !== 'Escape') return;
    dropdowns.forEach(function (item) {
      if (item.classList.contains('is-open') || item.contains(document.activeElement)) {
        item.classList.remove('is-open');
        var l = item.querySelector(':scope > a'); if (l) { l.setAttribute('aria-expanded', 'false'); l.focus(); }
        if (document.activeElement && item.querySelector('.nav-dd-panel').contains(document.activeElement)) document.activeElement.blur();
      }
    });
    if (button && button.getAttribute('aria-expanded') === 'true') { setMenu(false); button.focus(); }
  });

  /* 5. Fade in while scrolling (design README: "on headings, cards and blocks").
     The class is added here, not in the markup, so nothing stays hidden without JS,
     and only below the first screen, so nothing visible blinks on load. Cards in the
     same row follow each other a little (STAGGER). After the fade the class is removed again,
     so the hover transitions of cards and buttons (states.css) apply as before. */
  var REVEAL = '.head,.section-intro,.card,.step,.band,.featured,.bio,.about__photo,.quote-photo__content,.form';
  var STAGGER = 0.08; /* s between siblings */

  /* 6. Circles diagram: pointing at a circle or its label brings it forward and quiets the
     other two (sections.css: .circles[data-focus]). Mouse: on hover; touch: a tap toggles,
     a tap elsewhere clears. The center ("Vi, sada i ovde") shows all three again.
     Decoration only: the diagram is role="img" and its label says everything. */
  document.querySelectorAll('.circles').forEach(function (diagram) {
    var mouse = window.matchMedia('(hover: hover)');
    function partAt(target) { var p = target.closest('[data-part]'); return p ? p.getAttribute('data-part') : ''; }
    function focus(part) {
      if (diagram.classList.contains('is-waiting') || diagram.classList.contains('is-playing')) return;
      if (part) diagram.setAttribute('data-focus', part); else diagram.removeAttribute('data-focus');
    }
    diagram.addEventListener('mouseover', function (e) { if (mouse.matches) focus(partAt(e.target)); });
    diagram.addEventListener('mouseleave', function () { if (mouse.matches) focus(''); });
    diagram.addEventListener('click', function (e) {
      if (mouse.matches) return;
      var part = partAt(e.target);
      focus(part && part !== diagram.getAttribute('data-focus') ? part : '');
    });
    document.addEventListener('click', function (e) { if (!mouse.matches && !diagram.contains(e.target)) focus(''); });
  });

  if (!('IntersectionObserver' in window) || window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

  var main = document.querySelector('main');
  if (!main) return;
  var below = window.innerHeight;
  var targets = [].filter.call(main.querySelectorAll(REVEAL), function (el) {
    return !el.parentElement.closest(REVEAL) && el.getBoundingClientRect().top > below;
  });

  function done(el) {
    el.classList.remove('reveal', 'is-visible');
    el.style.removeProperty('--reveal-delay');
  }

  var observer = new IntersectionObserver(function (entries) {
    var batch = 0; /* elements entering together (a row of cards) follow each other */
    entries.forEach(function (entry) {
      if (!entry.isIntersecting) return;
      var el = entry.target;
      observer.unobserve(el);
      if (batch) el.style.setProperty('--reveal-delay', Math.min(batch, 4) * STAGGER + 's');
      batch++;
      el.addEventListener('transitionend', function end(e) {
        if (e.target === el && e.propertyName === 'opacity') { el.removeEventListener('transitionend', end); done(el); }
      });
      el.classList.add('is-visible');
    });
  }, { rootMargin: '0px 0px -8% 0px', threshold: 0.1 });

  targets.forEach(function (el) {
    el.classList.add('reveal');
    observer.observe(el);
  });

  /* The three-circles diagram assembles part by part once it is well in view
     (sections.css: .circles.is-waiting / .is-playing) */
  var diagrams = [].filter.call(main.querySelectorAll('.circles'), function (el) {
    return el.getBoundingClientRect().top > below;
  });
  var diagramObserver = new IntersectionObserver(function (entries) {
    entries.forEach(function (entry) {
      if (!entry.isIntersecting) return;
      var diagram = entry.target;
      diagramObserver.unobserve(diagram);
      /* after the last part (the dot), drop the class so the hover transitions apply */
      var dot = diagram.querySelector('.circles__center span');
      if (dot) dot.addEventListener('animationend', function () { diagram.classList.remove('is-playing'); }, { once: true });
      diagram.classList.replace('is-waiting', 'is-playing');
    });
  }, { threshold: 0.35 });
  diagrams.forEach(function (el) {
    el.classList.add('is-waiting');
    diagramObserver.observe(el);
  });
})();
