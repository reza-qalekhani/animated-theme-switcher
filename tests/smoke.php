<?php

/**
 * Minimal plugin smoke check with the WordPress functions used at load time.
 */

define('ABSPATH', dirname(__DIR__, 4) . '/');

$registered_actions    = array();
$registered_filters    = array();
$registered_shortcodes = array();

function is_admin() {
  return true;
}

function plugin_dir_url() {
  return 'https://example.test/wp-content/plugins/animated-theme-switcher/';
}

function plugin_dir_path() {
  return dirname(__DIR__) . '/';
}

function plugin_basename($file) {
  return basename(dirname($file)) . '/' . basename($file);
}

function add_action($hook, $callback) {
  $GLOBALS['registered_actions'][$hook] = $callback;
}

function add_filter($hook, $callback) {
  $GLOBALS['registered_filters'][$hook] = $callback;
}

function add_shortcode($tag, $callback) {
  $GLOBALS['registered_shortcodes'][$tag] = $callback;
}

function apply_filters($hook, $value) {
  return $value;
}

function esc_attr__($text) {
  return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

function esc_attr($text) {
  return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

function __($text) {
  return $text;
}

function sanitize_key($value) {
  return strtolower(preg_replace('/[^a-z0-9_-]/', '', $value));
}

function sanitize_hex_color($value) {
  return is_string($value) && preg_match('/^#(?:[0-9a-fA-F]{3}){1,2}$/', $value) ? strtolower($value) : null;
}

function absint($value) {
  return abs((int) $value);
}

function wp_get_nav_menu_object($menu) {
  if (is_object($menu)) {
    return $menu;
  }

  return 7 === (int) $menu ? (object) array('term_id' => 7, 'name' => 'Primary') : false;
}

function get_nav_menu_locations() {
  return array('primary' => 7);
}

require dirname(__DIR__) . '/animated-theme-switcher.php';

$markup  = wptts_shortcode();
$classes = wptts_body_classes(array());
$slugs   = wptts_transition_slugs();
$scss    = file_get_contents(dirname(__DIR__) . '/assets/scss/theme-switcher.scss');
$invalid = wptts_sanitize_options(
  array(
    'transition' => 'not-real',
    'position'   => 'sideways',
    'colors'     => array(
      'background' => 'red',
      'text'       => '#fff',
    ),
  )
);
$valid_menu = wptts_sanitize_options(
  array(
    'transition' => 'fade',
    'position'   => 'menu',
    'menu_id'    => 7,
    'colors'     => array('background' => '#112233'),
  )
);
$palette_css       = wptts_palette_css($valid_menu['colors']);
$direct_menu_args   = (object) array('menu' => 7, 'theme_location' => '');
$location_menu_args = (object) array('menu' => 0, 'theme_location' => 'primary');

if (
  'wptts_enqueue_assets' !== ($registered_actions['wp_enqueue_scripts'] ?? null) ||
  'wptts_register_settings' !== ($registered_actions['admin_init'] ?? null) ||
  'wptts_shortcode' !== ($registered_shortcodes['transition_theme_switcher'] ?? null) ||
  false === strpos($markup, 'data-wptts-toggle') ||
  ! in_array('wptts-generic-filter', $classes, true) ||
  36 !== count(array_unique($slugs)) ||
  false === strpos($scss, 'background-color: var(--wptts-background)') ||
  false === strpos($scss, 'color: var(--wptts-muted)') ||
  false === strpos($scss, '[data-wptts-palette="true"]') ||
  false !== strpos($scss, 'filter: invert') ||
  array_filter(
    $slugs,
    function ($slug) use ($scss) {
      return false === strpos($scss, '"' . $slug . '"');
    }
  ) ||
  'circle-reveal' !== $invalid['transition'] ||
  'floating-bottom' !== $invalid['position'] ||
  0 !== $invalid['menu_id'] ||
  wptts_default_colors() !== $invalid['colors'] ||
  7 !== $valid_menu['menu_id'] ||
  '#112233' !== $valid_menu['colors']['background'] ||
  false === strpos($palette_css, '--wptts-background:#112233;') ||
  false === strpos($palette_css, '--wptts-link:#93c5fd;') ||
  ! wptts_is_selected_menu(7, $direct_menu_args) ||
  ! wptts_is_selected_menu(7, $location_menu_args) ||
  wptts_is_selected_menu(8, $location_menu_args)
) {
  fwrite(STDERR, "Transition Theme Switcher smoke check failed.\n");
  exit(1);
}

echo "Transition Theme Switcher smoke check passed.\n";
