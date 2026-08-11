(function () {
  'use strict';

  var position = document.getElementById('wptts-position');
  var menuRow = document.querySelector('.wptts-menu-setting-row');

  if (!position || !menuRow) {
    return;
  }

  function updateMenuVisibility() {
    menuRow.hidden = position.value !== 'menu';
  }

  position.addEventListener('change', updateMenuVisibility);
  updateMenuVisibility();
}());
