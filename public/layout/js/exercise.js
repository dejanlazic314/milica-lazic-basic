/* =========================================================
   Interactive exercise "Mali eksperiment" – both variants (A and B)
   Usage (templates/content_element/exercise.html.twig):
     <section class="exercise-a" data-exercise="a" data-images="/files/milica/ilustracije/" data-contact="/kontakt"></section>
     <section class="exercise-b" data-exercise="b" data-images="/files/milica/ilustracije/" data-contact="/kontakt"></section>
   Texts per language (<html lang>) in TEXTS and TOPICS, changed only here.
   Milica has to approve them (design: vezba-podaci.json, same content).
   No libraries; prefers-reduced-motion is handled in exercise.css.
   ========================================================= */
(function () {
  'use strict';
  var LANG = (document.documentElement.lang || 'sr').slice(0, 2) === 'en' ? 'en' : 'sr';

  var TOPICS = {
    sr: [
      {
        key: 'anx', choice: 'Nemir koji ne prestaje', image: 'tema-anksioznost.svg',
        steps: [
          'Spustite stopala na pod. Primetite podlogu ispod sebe.',
          'Gde u telu sada osećate nemir? U grudima, stomaku, grlu? Samo primetite, ne menjajte ništa.',
          'Udahnite malo sporije nego inače. Šta se dešava sa tim mestom?'
        ],
        reflection: 'Nemir je često signal, a ne neprijatelj. U terapiji zajedno istražujemo šta pokušava da vam kaže, umesto da se samo borite protiv njega.',
        linkText: 'Terapija za anksioznost', link: '/s-cim-radim/anksioznost'
      },
      {
        key: 'burn', choice: 'Umor koji san ne popravlja', image: 'tema-burnout.svg',
        steps: [
          'Primetite kako sedite. Da li se telo naslanja ili se drži?',
          'Pustite ramena da se spuste za centimetar. Šta se promenilo?',
          'Zapitajte se: kome ili čemu danas kažete „da“, a zapravo želite da kažete „ne“?'
        ],
        reflection: 'Iscrpljenost često govori o granicama koje su dugo bile pomerane. U terapiji tražimo gde se energija gubi i šta vam je zaista potrebno.',
        linkText: 'Terapija za burnout', link: '/s-cim-radim/burnout'
      },
      {
        key: 'rel', choice: 'Isti sukob, iznova', image: 'tema-odnosi.svg',
        steps: [
          'Setite se poslednjeg takvog trenutka. Samo jednog.',
          'Šta ste tada osećali u telu, pre nego što ste bilo šta rekli?',
          'Šta ste zapravo želeli da druga osoba čuje?'
        ],
        reflection: 'Obrasci u odnosima se ponavljaju jer su nekada imali smisla. Kada ih primetimo, dobijamo izbor da odgovorimo drugačije.',
        linkText: 'Terapija za odnose', link: '/s-cim-radim/odnosi'
      },
      {
        key: 'change', choice: 'Na raskrsnici sam', image: 'tema-promene.svg',
        steps: [
          'Zamislite dva puta ispred sebe. Ne birajte, samo ih pogledajte.',
          'Na koji put telo lakše kreće? Gde se nešto steže?',
          'Šta biste izgubili, a šta dobili, ako ništa ne promenite?'
        ],
        reflection: 'Promene retko traže brzu odluku. Češće traže da čujemo sve delove sebe, i one koji žele dalje i one koji se plaše.',
        linkText: 'Životne promene i krize', link: '/s-cim-radim/gubitak-i-promene'
      },
      {
        key: 'parent', choice: 'Kao roditelj, ne snalazim se', image: 'tema-roditeljstvo.svg',
        steps: [
          'Setite se trenutka sa detetom koji vas je juče ili danas pogodio.',
          'Šta ste vi osećali? Ne dete, nego vi.',
          'Koliko godina ste se osećali u tom trenutku?'
        ],
        reflection: 'Roditeljstvo nas često vraća u sopstveno detinjstvo. Kada razumemo šta se u nama budi, lakše ostajemo prisutni uz dete.',
        linkText: 'Podrška roditeljima', link: '/s-cim-radim/roditeljstvo'
      }
    ],
    en: [
      {
        key: 'anx', choice: 'Restlessness that won’t stop', image: 'tema-anksioznost.svg',
        steps: [
          'Put your feet on the floor. Notice the ground beneath you.',
          'Where in your body do you feel the restlessness right now? In your chest, your stomach, your throat? Just notice, don’t change anything.',
          'Breathe in a little more slowly than usual. What happens in that place?'
        ],
        reflection: 'Restlessness is often a signal, not an enemy. In therapy we explore together what it is trying to tell you, instead of only fighting it.',
        linkText: 'Therapy for anxiety', link: '/en/what-i-work-with/anxiety'
      },
      {
        key: 'burn', choice: 'Tiredness that sleep doesn’t fix', image: 'tema-burnout.svg',
        steps: [
          'Notice how you are sitting. Is your body leaning back or holding itself up?',
          'Let your shoulders drop by a centimetre. What has changed?',
          'Ask yourself: to whom or what do you say “yes” today, when you actually want to say “no”?'
        ],
        reflection: 'Exhaustion often speaks of boundaries that have been pushed for a long time. In therapy we look at where your energy goes and what you really need.',
        linkText: 'Therapy for burnout', link: '/en/what-i-work-with/burnout'
      },
      {
        key: 'rel', choice: 'The same conflict, again', image: 'tema-odnosi.svg',
        steps: [
          'Think of the last moment like that. Just one.',
          'What did you feel in your body then, before you said anything?',
          'What did you actually want the other person to hear?'
        ],
        reflection: 'Patterns in relationships repeat because they once made sense. When we notice them, we get the choice to respond differently.',
        linkText: 'Therapy for relationships', link: '/en/what-i-work-with/relationships'
      },
      {
        key: 'change', choice: 'I’m at a crossroads', image: 'tema-promene.svg',
        steps: [
          'Imagine two paths in front of you. Don’t choose, just look at them.',
          'Which path does your body move towards more easily? Where does something tighten?',
          'What would you lose, and what would you gain, if you changed nothing?'
        ],
        reflection: 'Change rarely asks for a quick decision. More often it asks us to hear every part of ourselves, the parts that want to move on and the parts that are afraid.',
        linkText: 'Life changes and crises', link: '/en/what-i-work-with/loss-and-change'
      },
      {
        key: 'parent', choice: 'As a parent, I’m struggling', image: 'tema-roditeljstvo.svg',
        steps: [
          'Think of a moment with your child, yesterday or today, that hit you.',
          'What did you feel? Not your child, you.',
          'How old did you feel in that moment?'
        ],
        reflection: 'Parenthood often takes us back to our own childhood. When we understand what wakes up in us, it is easier to stay present with our child.',
        linkText: 'Support for parents', link: '/en/what-i-work-with/parenting'
      }
    ]
  };

  var TEXTS = {
    sr: {
      note: 'Ovo je kratka vežba svesnosti, ne dijagnoza i ne zamena za terapiju. Ako vam je sada veoma teško, pozovite <a href="tel:0800309309">0800 309 309</a> (0–24h, besplatno).',
      stepNames: ['Izaberite', 'Primetite', 'Zastanite'],
      step: function (n) { return 'korak ' + n + ' od 3'; },
      breath: 'udah · izdah',
      question: function (n) { return 'pitanje ' + n + ' od 3'; },
      hint: 'Zastanite koliko vam treba. Nema tačnog odgovora.',
      breathStart: 'Pokreni krug za disanje',
      breathStop: 'Zaustavi krug za disanje',
      next: 'Sledeće pitanje',
      finish: 'Završi',
      other: 'Izaberi drugo',
      endEyebrow: 'Upravo ste uradili nešto važno',
      endTitle: 'Zastali ste i primetili. To je srž geštalt terapije.',
      book: 'Zakažite prvi razgovor',
      again: 'Probajte ponovo',
      introEyebrow: 'Mali eksperiment · 1 minut',
      introTitle: 'Šta je kod vas <em>sada</em> prisutno?',
      introText: 'Geštalt terapija počinje od onoga što se dešava ovde i sada. Izaberite ono što vam je najbliže i probajte kratku vežbu.',
      done: ' (urađeno)',
      bEyebrow: 'Eksperiment svesnosti',
      bTitle: 'Šta vam je danas najbliže?',
      bInstructions: 'Probajte sada, polako, jedno po jedno',
      selected: 'Izabrano: ',
      stepDone: function (n) { return 'Korak ' + n + ' od 3 urađen'; }
    },
    en: {
      note: 'This is a short awareness exercise, not a diagnosis and not a substitute for therapy. If things are very hard for you right now, call <a href="tel:0800309309">0800 309 309</a> (24/7, free of charge).',
      stepNames: ['Choose', 'Notice', 'Pause'],
      step: function (n) { return 'step ' + n + ' of 3'; },
      breath: 'in · out',
      question: function (n) { return 'question ' + n + ' of 3'; },
      hint: 'Take as long as you need. There is no right answer.',
      breathStart: 'Start the breathing circle',
      breathStop: 'Stop the breathing circle',
      next: 'Next question',
      finish: 'Finish',
      other: 'Choose something else',
      endEyebrow: 'You have just done something important',
      endTitle: 'You paused and noticed. That is the heart of Gestalt therapy.',
      book: 'Book a first conversation',
      again: 'Try again',
      introEyebrow: 'A small experiment · 1 minute',
      introTitle: 'What is present for you <em>right now</em>?',
      introText: 'Gestalt therapy starts from what is happening here and now. Choose what feels closest to you and try a short exercise.',
      done: ' (done)',
      bEyebrow: 'An awareness experiment',
      bTitle: 'What feels closest to you today?',
      bInstructions: 'Try it now, slowly, one at a time',
      selected: 'Selected: ',
      stepDone: function (n) { return 'Step ' + n + ' of 3 done'; }
    }
  };

  var DATA = TOPICS[LANG];
  var TEXT = TEXTS[LANG];

  function esc(t) { return String(t).replace(/[&<>"]/g, function (c) { return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;' }[c]; }); }
  function announce(el, text) { var a = el.querySelector('[data-announce]'); if (a) a.textContent = text; }

  /* ---------- Variant A: three steps (choice → three questions with the breath → reflection) ---------- */
  function ExerciseA(el) {
    var s = { step: 0, choice: 0, question: 0 }, paused = false;
    var images = el.getAttribute('data-images') || '';
    var contact = el.getAttribute('data-contact') || '/kontakt';

    function dots() {
      return [0, 1, 2].map(function (i) {
        var c = i === s.step ? 'is-current' : (i < s.step ? 'is-done' : '');
        return '<span class="exercise-a__dot ' + c + '"></span>';
      }).join('');
    }
    function render(focus) {
      var d = DATA[s.choice];
      var stepName = TEXT.stepNames[s.step] + ' · ' + TEXT.step(s.step + 1);
      var body = '';
      if (s.step === 0) {
        body = '<div class="exercise-a__choice fade-up">' + DATA.map(function (x, i) {
          return '<button class="option exercise-a__option" type="button" data-select="' + i + '"><img src="' + images + x.image + '" alt="" loading="lazy"><span>' + esc(x.choice) + '</span></button>';
        }).join('') + '</div>';
      } else if (s.step === 1) {
        body = '<div class="exercise-a__question fade-up">' +
          '<div class="exercise-a__breath' + (paused ? ' is-paused' : '') + '" aria-hidden="true"><span class="breath"></span><span class="breath breath--inner"></span><em>' + TEXT.breath + '</em></div>' +
          '<div class="exercise-a__text"><p class="exercise-a__meta">' + esc(d.choice) + ' · ' + TEXT.question(s.question + 1) + '</p>' +
          '<p class="exercise-a__q fade-up" tabindex="-1" data-focus>' + esc(d.steps[s.question]) + '</p>' +
          '<p class="exercise-a__hint">' + TEXT.hint + '</p>' +
          '<button class="exercise-a__pause" type="button" data-pause aria-pressed="' + paused + '">' + (paused ? TEXT.breathStart : TEXT.breathStop) + '</button>' +
          '<div class="exercise__actions"><button class="btn btn-dark" type="button" data-next>' + (s.question < 2 ? TEXT.next : TEXT.finish) + '</button>' +
          '<button class="btn btn-outline" type="button" data-restart>' + TEXT.other + '</button></div></div></div>';
      } else {
        body = '<div class="exercise-a__end fade-up">' +
          '<div class="exercise-a__end-text"><p class="eyebrow">' + TEXT.endEyebrow + '</p>' +
          '<h3 tabindex="-1" data-focus>' + TEXT.endTitle + '</h3><p>' + esc(d.reflection) + '</p>' +
          '<div class="exercise__actions"><a class="btn btn-apricot" href="' + contact + '">' + TEXT.book + '</a>' +
          '<a class="btn btn-outline-light" href="' + d.link + '">' + esc(d.linkText) + ' →</a>' +
          '<button class="link-button" type="button" data-restart>' + TEXT.again + '</button></div></div>' +
          '<img class="exercise-a__image" src="' + images + d.image + '" alt=""></div>';
      }
      el.innerHTML =
        '<div class="exercise-a__inner">' +
        '<div class="exercise__intro"><p class="eyebrow">' + TEXT.introEyebrow + '</p>' +
        '<h2>' + TEXT.introTitle + '</h2>' +
        '<p>' + TEXT.introText + '</p></div>' +
        '<div class="exercise-a__steps" aria-hidden="true">' + dots() + '<span>' + stepName + '</span></div>' +
        body +
        '<p class="exercise__note">' + TEXT.note + '</p>' +
        '<p class="sr-only" aria-live="polite" data-announce></p></div>';
      if (focus) { var f = el.querySelector('[data-focus]') || el.querySelector('button'); if (f) f.focus({ preventScroll: true }); }
      announce(el, stepName);
    }
    el.addEventListener('click', function (e) {
      var b = e.target.closest('button'); if (!b) return;
      if (b.hasAttribute('data-select')) { s = { step: 1, choice: +b.getAttribute('data-select'), question: 0 }; render(true); }
      else if (b.hasAttribute('data-next')) { if (s.question < 2) s.question++; else s.step = 2; render(true); }
      else if (b.hasAttribute('data-restart')) { s.step = 0; s.question = 0; render(true); }
      else if (b.hasAttribute('data-pause')) { paused = !paused; render(false); var p = el.querySelector('[data-pause]'); if (p) p.focus(); }
    });
    render(false);
  }

  /* ---------- Variant B: topics on the left, three steps on the right that get ticked off ---------- */
  function ExerciseB(el) {
    var s = { choice: 0, seen: 0 };
    var images = el.getAttribute('data-images') || '';
    var contact = el.getAttribute('data-contact') || '/kontakt';
    function render(focusSelector) {
      var d = DATA[s.choice];
      var options = DATA.map(function (x, i) {
        return '<button class="option exercise-b__topic' + (i === s.choice ? ' is-selected' : '') + '" type="button" aria-pressed="' + (i === s.choice) + '" data-topic="' + i + '">' +
          '<img src="' + images + x.image + '" alt="" loading="lazy"><span>' + esc(x.choice) + '</span></button>';
      }).join('');
      var steps = d.steps.map(function (t, i) {
        var st = i < s.seen ? 'is-done' : (i === s.seen ? 'is-next' : 'is-later');
        return '<li><button class="option exercise-b__step ' + st + '" type="button" data-step="' + i + '"' + (i > s.seen ? ' aria-disabled="true"' : '') + '>' +
          '<span class="exercise-b__label" aria-hidden="true">' + (i < s.seen ? '✓' : '') + '</span>' +
          '<span class="exercise-b__text">' + esc(t) + '</span>' +
          '<span class="sr-only">' + (i < s.seen ? TEXT.done : '') + '</span></button></li>';
      }).join('');
      var end = s.seen >= 3 ? '<div class="exercise-b__end fade-up" tabindex="-1" data-end><p><strong>' + TEXT.endTitle + '</strong> ' + esc(d.reflection) + '</p>' +
        '<div class="exercise__actions"><a class="link-arrow" href="' + contact + '">' + TEXT.book + ' →</a><a class="link-arrow" href="' + d.link + '">' + esc(d.linkText) + ' →</a></div></div>' : '';
      el.innerHTML =
        '<div class="exercise-b__inner">' +
        '<div class="exercise-b__left"><p class="eyebrow">' + TEXT.bEyebrow + '</p><h2>' + TEXT.bTitle + '</h2>' +
        '<div class="exercise-b__topics">' + options + '</div><p class="exercise__note">' + TEXT.note + '</p></div>' +
        '<div class="exercise-b__right"><img class="exercise-b__background" src="' + images + d.image + '" alt="">' +
        '<p class="exercise-b__instructions">' + TEXT.bInstructions + '</p><ol class="exercise-b__list">' + steps + '</ol>' + end + '</div>' +
        '<p class="sr-only" aria-live="polite" data-announce></p></div>';
      if (focusSelector) { var f = el.querySelector(focusSelector); if (f) f.focus({ preventScroll: true }); }
    }
    el.addEventListener('click', function (e) {
      var b = e.target.closest('button'); if (!b) return;
      if (b.hasAttribute('data-topic')) { s = { choice: +b.getAttribute('data-topic'), seen: 0 }; render('[data-topic="' + s.choice + '"]'); announce(el, TEXT.selected + DATA[s.choice].choice); }
      else if (b.hasAttribute('data-step')) {
        var i = +b.getAttribute('data-step'); if (i > s.seen) return;
        s.seen = Math.max(s.seen, i + 1);
        render(s.seen >= 3 ? '[data-end]' : '[data-step="' + Math.min(s.seen, 2) + '"]');
        announce(el, TEXT.stepDone(i + 1));
      }
    });
    render(null);
  }

  document.querySelectorAll('[data-exercise="a"]').forEach(ExerciseA);
  document.querySelectorAll('[data-exercise="b"]').forEach(ExerciseB);
})();
