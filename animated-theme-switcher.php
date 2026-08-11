<?php

/**
 * Plugin Name:       Animated Theme Switcher
 * Plugin URI:        https://byreza.net/wordpress/animated-theme-switcher-plugin/
 * Description:       Adds an animated, accessible light/dark theme switcher to any WordPress site.
 * Version:           1.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Reza Qalekhani
 * Author URI:        https://byreza.net
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       animated-theme-switcher
 * Domain Path:       /languages
 */


// Exit if accessed directly
if (!defined('ABSPATH')) {
  exit('Do not open this file directly.');
}

// Define plugin constants
define('WPTTS_VERSION', '1.0');
define('WPTTS_FILE', __FILE__);
define('WPTTS_URL', plugin_dir_url(WPTTS_FILE));
define('WPTTS_PATH', plugin_dir_path(WPTTS_FILE));

// Include required files
require_once WPTTS_PATH . 'includes/i18n.php';
require_once WPTTS_PATH . 'includes/transitions.php';
require_once WPTTS_PATH . 'includes/options.php';
require_once WPTTS_PATH . 'includes/frontend.php';

if (is_admin()) {
  require_once WPTTS_PATH . 'includes/admin.php';
}
