/* =========================================================
   FAQ accordion: smooth opening and closing of <details>
   Markup: templates/content_element/faq_list.html.twig (details > summary.question + .answer)
   Each question opens and closes on its own. Not in the design (there <details>
   opens at once). Without JS, and with prefers-reduced-motion, the native behaviour stays.
   ========================================================= */
(function () {
  'use strict';
  if (window.matchMedia('(prefers-reduced-motion: reduce)').matches || !Element.prototype.animate) return;

  var DURATION = 320; /* ms, same feel as --hover in tokens.css */
  var EASING = 'cubic-bezier(.22,.61,.36,1)'; /* --ease-out */

  document.querySelectorAll('.faq details').forEach(function (details) {
    var summary = details.querySelector('summary');
    var answer = details.querySelector('.answer');
    if (!summary || !answer) return;
    var running = null;

    function run(from, to, opening) {
      details.style.overflow = 'hidden';
      running = details.animate({ height: [from + 'px', to + 'px'] }, { duration: DURATION, easing: EASING });
      answer.animate({ opacity: opening ? [0, 1] : [1, 0] }, { duration: DURATION, easing: EASING, fill: 'forwards' });
      running.onfinish = function () {
        running = null;
        if (!opening) { details.open = false; details.classList.remove('is-closing'); }
        details.style.removeProperty('overflow');
        answer.getAnimations().forEach(function (a) { a.cancel(); });
      };
    }

    summary.addEventListener('click', function (e) {
      e.preventDefault();
      /* current height, also in the middle of an animation; then stop it and measure the target */
      var from = details.offsetHeight;
      if (running) { running.onfinish = null; running.cancel(); running = null; }
      var borders = details.offsetHeight - details.clientHeight;
      if (details.open && !details.classList.contains('is-closing')) {
        details.classList.add('is-closing'); /* the icon turns back right away (components.css) */
        run(from, summary.offsetHeight + borders, false);
      } else {
        details.classList.remove('is-closing');
        details.open = true;
        run(from, details.offsetHeight, true);
      }
    });
  });
})();
