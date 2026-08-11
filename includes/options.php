<?php

/**
 * Shared settings and validation.
 *
 * @package TransitionThemeSwitcher
 */

defined('ABSPATH') || exit;

/**
 * Return the default dark-mode palette.
 *
 * @return array<string,string>
 */
function wptts_default_colors() {
  return array(
    'background' => '#0f172a',
    'surface'    => '#1e293b',
    'text'       => '#f8fafc',
    'muted'      => '#cbd5e1',
    'border'     => '#475569',
    'link'       => '#93c5fd',
  );
}

/**
 * Return saved settings with defaults.
 *
 * @return array{transition:string,position:string,menu_id:int,colors:array<string,string>}
 */
function wptts_get_options() {
  $options = wp_parse_args(
    get_option('wptts_options', array()),
    array(
      'transition' => 'circle-reveal',
      'position'   => 'floating-bottom',
      'menu_id'    => 0,
      'colors'     => array(),
    )
  );
  $options['colors'] = wp_parse_args(is_array($options['colors']) ? $options['colors'] : array(), wptts_default_colors());

  return $options;
}

/**
 * Sanitize settings against the supported values.
 *
 * @param mixed $input Submitted settings.
 * @return array{transition:string,position:string,menu_id:int,colors:array<string,string>}
 */
function wptts_sanitize_options($input) {
  $input      = is_array($input) ? $input : array();
  $transition = isset($input['transition']) ? sanitize_key($input['transition']) : '';
  $position   = isset($input['position']) ? sanitize_key($input['position']) : '';
  $menu_id    = isset($input['menu_id']) ? absint($input['menu_id']) : 0;

  if ($menu_id && ! wp_get_nav_menu_object($menu_id)) {
    $menu_id = 0;
  }

  $colors       = array();
  $input_colors = isset($input['colors']) && is_array($input['colors']) ? $input['colors'] : array();
  foreach (wptts_default_colors() as $name => $default) {
    $value           = isset($input_colors[$name]) ? sanitize_hex_color($input_colors[$name]) : '';
    $value           = 7 === strlen((string) $value) ? $value : '';
    $colors[$name] = $value ? $value : $default;
  }

  return array(
    'transition' => in_array($transition, wptts_transition_slugs(), true) ? $transition : 'circle-reveal',
    'position'   => in_array($position, array('floating-bottom', 'floating-top', 'menu'), true) ? $position : 'floating-bottom',
    'menu_id'    => $menu_id,
    'colors'     => $colors,
  );
}

/**
 * Build the sanitized custom-property declarations for the selected palette.
 *
 * @param array<string,string> $colors Palette colors.
 * @return string
 */
function wptts_palette_css($colors) {
  $declarations = '';
  foreach (wptts_default_colors() as $name => $default) {
    $color         = isset($colors[$name]) ? sanitize_hex_color($colors[$name]) : '';
    $color         = 7 === strlen((string) $color) ? $color : '';
    $declarations .= '--wptts-' . $name . ':' . ($color ? $color : $default) . ';';
  }

  return 'html[data-wptts-palette="true"]{' . $declarations . '}';
}

/**
 * Whether the plugin should supply generic site colors.
 *
 * @return bool
 */
function wptts_use_generic_palette() {
  return (bool) apply_filters('wptts_use_generic_filter', true);
}
