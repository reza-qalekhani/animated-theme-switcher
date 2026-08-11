<?php

/**
 * Translation loading.
 *
 * @package TransitionThemeSwitcher
 */

defined('ABSPATH') || exit;

/**
 * Load translations supplied by WordPress language packs or this plugin.
 */
function wptts_load_textdomain() {
  load_plugin_textdomain(
    'animated-theme-switcher',
    false,
    dirname(plugin_basename(WPTTS_FILE)) . '/languages'
  );
}
add_action('plugins_loaded', 'wptts_load_textdomain');
