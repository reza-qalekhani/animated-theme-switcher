(function () {
  'use strict';

  var root = document.documentElement;
  var media = window.matchMedia('(prefers-color-scheme: dark)');
  var labels = window.wpttsThemeSwitcher || {
    lightLabel: 'Switch to light theme',
    darkLabel: 'Switch to dark theme',
    transition: 'circle-reveal'
  };
  var gifMasks = {
    'gif-frog': 'https://media.tenor.com/jNj-TzGDB9YAAAAm/cute-frog.gif',
    'gif-penguin': 'https://media.tenor.com/tGCwmrNRc9wAAAAi/dance-dancer.gif',
    'gif-cat': 'https://media.tenor.com/GQAsycjoZG8AAAAi/scuba-scuba-cat.gif',
    'gif-michael-jackson': 'https://media.tenor.com/MdlKGirpTAQAAAAi/michael-jackson-moon-walk.gif',
    'gif-deadpool': 'https://media.tenor.com/VJlZzo7UdagAAAAi/deadpool-marvel.gif',
    'gif-chika': 'https://media.tenor.com/gVPazpEOQ3kAAAAi/chika.gif',
    'gif-hakari-dance': 'https://media.tenor.com/uRlxzRNgp2MAAAAi/anime-girl.gif'
  };

  function storedTheme() {
    try {
      var value = window.localStorage.getItem('wptts-theme');
      return value === 'light' || value === 'dark' ? value : null;
    } catch (error) {
      return null;
    }
  }

  function currentTheme() {
    return root.dataset.wpttsTheme || (media.matches ? 'dark' : 'light');
  }

  function updateButtons(theme) {
    document.querySelectorAll('[data-wptts-toggle]').forEach(function (button) {
      var isDark = theme === 'dark';
      button.setAttribute('aria-pressed', String(isDark));
      button.setAttribute(
        'aria-label',
        isDark ? labels.lightLabel : labels.darkLabel
      );
      button.dataset.theme = theme;
    });
  }

  function applyTheme(theme) {
    root.dataset.wpttsTheme = theme;
    root.classList.toggle('wptts-dark', theme === 'dark');
    updateButtons(theme);
    window.dispatchEvent(new CustomEvent('wptts:change', { detail: { theme: theme } }));
  }

  function saveTheme(theme) {
    try {
      window.localStorage.setItem('wptts-theme', theme);
    } catch (error) {
      // Storage can be unavailable in privacy modes; the switch still works.
    }
  }

  function toggleTheme(event) {
    var next = currentTheme() === 'dark' ? 'light' : 'dark';
    var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    var canTransition = typeof document.startViewTransition === 'function' && !reduceMotion;
    var x = event.clientX || window.innerWidth / 2;
    var y = event.clientY || window.innerHeight / 2;
    var radius = Math.hypot(Math.max(x, window.innerWidth - x), Math.max(y, window.innerHeight - y));

    saveTheme(next);

    if (!canTransition) {
      applyTheme(next);
      return;
    }

    root.style.setProperty('--wptts-x', x + 'px');
    root.style.setProperty('--wptts-y', y + 'px');
    root.style.setProperty('--wptts-radius', radius + 'px');
    root.dataset.wpttsTransitioning = 'true';

    var transition = document.startViewTransition(function () {
      applyTheme(next);
    });

    transition.finished.finally(function () {
      delete root.dataset.wpttsTransitioning;
    });
  }

  root.dataset.wpttsTransition = labels.transition || 'circle-reveal';
  if (gifMasks[root.dataset.wpttsTransition]) {
    var mask = new Image();
    mask.src = gifMasks[root.dataset.wpttsTransition];
  }

  document.addEventListener('click', function (event) {
    var button = event.target.closest('[data-wptts-toggle]');
    if (button) {
      toggleTheme(event);
    }
  });

  function followSystemTheme(event) {
    if (!storedTheme()) {
      applyTheme(event.matches ? 'dark' : 'light');
    }
  }

  if (typeof media.addEventListener === 'function') {
    media.addEventListener('change', followSystemTheme);
  } else {
    media.addListener(followSystemTheme);
  }

  applyTheme(currentTheme());
}());
