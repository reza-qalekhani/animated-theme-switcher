<?php

/**
 * Frontend assets and switcher placement.
 *
 * @package TransitionThemeSwitcher
 */

defined('ABSPATH') || exit;

/**
 * Load the public assets and translated runtime data.
 */
function wptts_enqueue_assets() {
  $options = wptts_get_options();

  wp_enqueue_style(
    'wptts-theme-switcher',
    WPTTS_URL . 'assets/css/theme-switcher.css',
    array(),
    (string) filemtime(WPTTS_PATH . 'assets/css/theme-switcher.css')
  );
  wp_add_inline_style('wptts-theme-switcher', wptts_palette_css($options['colors']));

  wp_enqueue_script(
    'wptts-theme-switcher',
    WPTTS_URL . 'assets/js/theme-switcher.js',
    array(),
    (string) filemtime(WPTTS_PATH . 'assets/js/theme-switcher.js'),
    true
  );

  wp_localize_script(
    'wptts-theme-switcher',
    'wpttsThemeSwitcher',
    array(
      'lightLabel' => __('Switch to light theme', 'animated-theme-switcher'),
      'darkLabel'  => __('Switch to dark theme', 'animated-theme-switcher'),
      'transition' => $options['transition'],
    )
  );
}
add_action('wp_enqueue_scripts', 'wptts_enqueue_assets');

/**
 * Apply the saved or system theme before the page paints.
 */
function wptts_print_initial_theme_script() {
  $options         = wptts_get_options();
  $generic_palette = wptts_use_generic_palette();
?>
  <script id="wptts-initial-theme">
    (function() {
      var t;
      try {
        t = localStorage.getItem('wptts-theme')
      } catch (e) {}
      if (t !== 'light' && t !== 'dark') {
        t = matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light'
      }
      document.documentElement.dataset.wpttsTheme = t;
      document.documentElement.dataset.wpttsTransition = <?php echo wp_json_encode($options['transition']); ?>;
      document.documentElement.dataset.wpttsPalette = <?php echo wp_json_encode($generic_palette); ?>;
      document.documentElement.classList.toggle('wptts-dark', t === 'dark')
    }());
  </script>
<?php
}
add_action('wp_head', 'wptts_print_initial_theme_script', 1);

/**
 * Add a compatibility body class for custom integrations.
 *
 * @param string[] $classes Existing body classes.
 * @return string[]
 */
function wptts_body_classes($classes) {
  if (wptts_use_generic_palette()) {
    $classes[] = 'wptts-generic-filter';
  }

  return $classes;
}
add_filter('body_class', 'wptts_body_classes');

/**
 * Return the accessible switch button.
 *
 * @param string $placement inline, floating-bottom, or floating-top.
 * @return string
 */
function wptts_button_markup($placement = 'inline') {
  $classes = array('wptts-toggle');
  if ('floating-bottom' === $placement || 'floating-top' === $placement) {
    $classes[] = 'wptts-toggle--floating';
    $classes[] = 'wptts-toggle--' . $placement;
  }

  return '<button class="' . esc_attr(implode(' ', $classes)) . '" type="button" data-wptts-toggle aria-label="' . esc_attr__('Switch color theme', 'animated-theme-switcher') . '" aria-pressed="false">'
    . '<svg class="wptts-icon wptts-icon--sun" aria-hidden="true" viewBox="0 0 24 24"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.42 1.42M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.42-1.42M17.66 6.34l1.41-1.41"/></svg>'
    . '<svg class="wptts-icon wptts-icon--moon" aria-hidden="true" viewBox="0 0 24 24"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79Z"/></svg>'
    . '</button>';
}

/**
 * Render the configured floating switcher unless a shortcode already did.
 */
function wptts_render_floating_toggle() {
  $options = wptts_get_options();
  if (
    'menu' === $options['position'] ||
    ! apply_filters('wptts_show_floating_toggle', true) ||
    ! empty($GLOBALS['wptts_embedded_toggle'])
  ) {
    return;
  }

  echo wptts_button_markup($options['position']); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Static escaped markup.
}
add_action('wp_footer', 'wptts_render_floating_toggle');

/**
 * Check whether the current render belongs to the selected menu.
 *
 * @param int    $menu_id Selected menu term ID.
 * @param object $args    wp_nav_menu() arguments.
 * @return bool
 */
function wptts_is_selected_menu($menu_id, $args) {
  if (! $menu_id) {
    return false;
  }

  $requested = ! empty($args->menu) ? wp_get_nav_menu_object($args->menu) : false;
  if ($requested) {
    return $menu_id === (int) $requested->term_id;
  }

  if (! empty($args->theme_location)) {
    $locations = get_nav_menu_locations();
    return $menu_id === (int) ($locations[$args->theme_location] ?? 0);
  }

  return false;
}

/**
 * Append the switcher only to the selected navigation menu.
 *
 * @param string $items Existing menu items.
 * @param object $args  wp_nav_menu() arguments.
 * @return string
 */
function wptts_add_menu_toggle($items, $args) {
  $options = wptts_get_options();
  if ('menu' !== $options['position'] || ! wptts_is_selected_menu($options['menu_id'], $args)) {
    return $items;
  }

  return $items . '<li class="menu-item wptts-menu-item">' . wptts_button_markup() . '</li>';
}
add_filter('wp_nav_menu_items', 'wptts_add_menu_toggle', 10, 2);

/**
 * Allow placement with [animated_theme_switcher].
 *
 * @return string
 */
function wptts_shortcode() {
  $GLOBALS['wptts_embedded_toggle'] = true;

  return wptts_button_markup();
}
add_shortcode('animated_theme_switcher', 'wptts_shortcode');
