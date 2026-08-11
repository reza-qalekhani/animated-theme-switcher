<?php

/**
 * Admin settings screen.
 *
 * @package TransitionThemeSwitcher
 */

defined('ABSPATH') || exit;

/**
 * Load admin assets only on this plugin's settings screen.
 *
 * @param string $hook_suffix Current admin page hook.
 */
function wptts_enqueue_admin_assets($hook_suffix) {
  if ('settings_page_animated-theme-switcher' !== $hook_suffix) {
    return;
  }

  wp_enqueue_style(
    'wptts-admin',
    WPTTS_URL . 'assets/css/admin.css',
    array(),
    (string) filemtime(WPTTS_PATH . 'assets/css/admin.css')
  );

  wp_enqueue_script(
    'wptts-admin',
    WPTTS_URL . 'assets/js/admin.js',
    array(),
    (string) filemtime(WPTTS_PATH . 'assets/js/admin.js'),
    true
  );
}
add_action('admin_enqueue_scripts', 'wptts_enqueue_admin_assets');

/**
 * Register the settings page and fields.
 */
function wptts_register_settings() {
  register_setting(
    'wptts_settings',
    'wptts_options',
    array(
      'type'              => 'array',
      'sanitize_callback' => 'wptts_sanitize_options',
      'default'           => array(
        'transition' => 'circle-reveal',
        'position'   => 'floating-bottom',
        'menu_id'    => 0,
        'colors'     => wptts_default_colors(),
      ),
    )
  );

  add_settings_section(
    'wptts_display',
    __('Switcher display', 'animated-theme-switcher'),
    '__return_false',
    'animated-theme-switcher'
  );

  add_settings_field(
    'wptts_transition',
    __('Transition template', 'animated-theme-switcher'),
    'wptts_transition_field',
    'animated-theme-switcher',
    'wptts_display'
  );

  add_settings_field(
    'wptts_position',
    __('Button location', 'animated-theme-switcher'),
    'wptts_position_field',
    'animated-theme-switcher',
    'wptts_display'
  );

  add_settings_field(
    'wptts_menu_id',
    __('Navigation menu', 'animated-theme-switcher'),
    'wptts_menu_field',
    'animated-theme-switcher',
    'wptts_display',
    array('class' => 'wptts-menu-setting-row')
  );

  add_settings_field(
    'wptts_colors',
    __('Dark-mode colors', 'animated-theme-switcher'),
    'wptts_colors_field',
    'animated-theme-switcher',
    'wptts_display'
  );
}
add_action('admin_init', 'wptts_register_settings');

/**
 * Render the transition field.
 */
function wptts_transition_field() {
  $current = wptts_get_options()['transition'];
?>
  <select id="wptts-transition" name="wptts_options[transition]">
    <?php foreach (wptts_transition_groups() as $group => $transitions) : ?>
      <optgroup label="<?php echo esc_attr($group); ?>">
        <?php foreach ($transitions as $slug => $label) : ?>
          <option value="<?php echo esc_attr($slug); ?>" <?php selected($current, $slug); ?>><?php echo esc_html($label); ?></option>
        <?php endforeach; ?>
      </optgroup>
    <?php endforeach; ?>
  </select>
  <p class="description"><?php esc_html_e('GIF templates load their mask animation from Tenor.', 'animated-theme-switcher'); ?></p>
<?php
}

/**
 * Render the button-location field.
 */
function wptts_position_field() {
  $current   = wptts_get_options()['position'];
  $positions = array(
    'floating-bottom' => __('Floating bottom', 'animated-theme-switcher'),
    'floating-top'    => __('Floating top', 'animated-theme-switcher'),
    'menu'            => __('In navigation menu', 'animated-theme-switcher'),
  );
?>
  <select id="wptts-position" name="wptts_options[position]">
    <?php foreach ($positions as $value => $label) : ?>
      <option value="<?php echo esc_attr($value); ?>" <?php selected($current, $value); ?>><?php echo esc_html($label); ?></option>
    <?php endforeach; ?>
  </select>
  <p class="description"><?php esc_html_e('Choose the exact menu in the field below when using menu placement.', 'animated-theme-switcher'); ?></p>
<?php
}

/**
 * Render the navigation-menu field.
 */
function wptts_menu_field() {
  $current = wptts_get_options()['menu_id'];
  $menus   = wp_get_nav_menus(array('hide_empty' => false));
?>
  <select id="wptts-menu-id" name="wptts_options[menu_id]" <?php disabled(empty($menus)); ?>>
    <option value="0"><?php esc_html_e('Select a menu', 'animated-theme-switcher'); ?></option>
    <?php foreach ($menus as $menu) : ?>
      <option value="<?php echo esc_attr($menu->term_id); ?>" <?php selected($current, $menu->term_id); ?>><?php echo esc_html($menu->name); ?></option>
    <?php endforeach; ?>
  </select>
  <?php if (empty($menus)) : ?>
    <p class="description"><?php esc_html_e('No classic navigation menus were found.', 'animated-theme-switcher'); ?></p>
  <?php endif; ?>
<?php
}

/**
 * Render the dark-palette color fields.
 */
function wptts_colors_field() {
  $current = wptts_get_options()['colors'];
  $fields  = array(
    'background' => array(
      'label' => __('Background', 'animated-theme-switcher'),
      'help'  => __('Main page and primary content backgrounds.', 'animated-theme-switcher'),
    ),
    'surface'    => array(
      'label' => __('Surface', 'animated-theme-switcher'),
      'help'  => __('Widgets, sidebars, form fields, tables, code blocks, and comments.', 'animated-theme-switcher'),
    ),
    'text'       => array(
      'label' => __('Text', 'animated-theme-switcher'),
      'help'  => __('Headings, paragraphs, labels, lists, and default body text.', 'animated-theme-switcher'),
    ),
    'muted'      => array(
      'label' => __('Muted text', 'animated-theme-switcher'),
      'help'  => __('Post dates, author details, metadata, and other secondary text.', 'animated-theme-switcher'),
    ),
    'border'     => array(
      'label' => __('Border', 'animated-theme-switcher'),
      'help'  => __('Form controls, tables, widgets, comments, and horizontal rules.', 'animated-theme-switcher'),
    ),
    'link'       => array(
      'label' => __('Link', 'animated-theme-switcher'),
      'help'  => __('Standard text links; button colors remain controlled by the active theme.', 'animated-theme-switcher'),
    ),
  );
?>
  <p class="description"><?php esc_html_e("These colors are used by the plugin's generic dark palette.", 'animated-theme-switcher'); ?></p><br>
  <fieldset class="wptts-color-fields">
    <?php foreach ($fields as $name => $field) : ?>
      <label class="wptts-color-field" for="wptts-color-<?php echo esc_attr($name); ?>">
        <input id="wptts-color-<?php echo esc_attr($name); ?>" type="color" name="wptts_options[colors][<?php echo esc_attr($name); ?>]" value="<?php echo esc_attr($current[$name]); ?>">
        <span><?php echo esc_html($field['label']); ?></span>
        <code><?php echo esc_html('--wptts-' . $name); ?></code>
        <p class="description wptts-color-help"><?php echo esc_html($field['help']); ?></p>
      </label>
    <?php endforeach; ?>
  </fieldset>
<?php
}

/**
 * Add the page beneath Settings.
 */
function wptts_add_settings_page() {
  add_options_page(
    __('Theme Switcher', 'animated-theme-switcher'),
    __('Theme Switcher', 'animated-theme-switcher'),
    'manage_options',
    'animated-theme-switcher',
    'wptts_render_settings_page'
  );
}
add_action('admin_menu', 'wptts_add_settings_page');

/**
 * Render the settings page.
 */
function wptts_render_settings_page() {
  if (! current_user_can('manage_options')) {
    return;
  }
?>
  <div class="wrap">
    <h1><?php esc_html_e('Transition Theme Switcher', 'animated-theme-switcher'); ?></h1>
    <p><?php esc_html_e('Choose the animation, button location, and dark-mode palette.', 'animated-theme-switcher'); ?></p>
    <form action="options.php" method="post">
      <?php
      settings_fields('wptts_settings');
      do_settings_sections('animated-theme-switcher');
      submit_button();
      ?>
    </form>
  </div>
<?php
}

/**
 * Add a direct Settings link on the Plugins screen.
 *
 * @param string[] $links Existing action links.
 * @return string[]
 */
function wptts_plugin_action_links($links) {
  array_unshift(
    $links,
    '<a href="' . esc_url(admin_url('options-general.php?page=animated-theme-switcher')) . '">' . esc_html__('Settings', 'animated-theme-switcher') . '</a>'
  );

  return $links;
}
add_filter('plugin_action_links_' . plugin_basename(WPTTS_FILE), 'wptts_plugin_action_links');
