<?php

/**
 * Transition catalog.
 *
 * @package TransitionThemeSwitcher
 */

defined('ABSPATH') || exit;

/**
 * Return the transition catalog grouped for the settings screen.
 *
 * @return array<string,array<string,string>>
 */
function wptts_transition_groups() {
  return array(
    __('Mask reveals', 'animated-theme-switcher') => array(
      'circle-reveal'       => __('Circle Reveal', 'animated-theme-switcher'),
      'circle-blur'         => __('Circle Blur', 'animated-theme-switcher'),
      'polygon-reveal'      => __('Polygon Reveal', 'animated-theme-switcher'),
      'gif-frog'            => __('GIF Frog', 'animated-theme-switcher'),
      'gif-penguin'         => __('GIF Penguin', 'animated-theme-switcher'),
      'gif-cat'             => __('GIF Cat', 'animated-theme-switcher'),
      'gif-michael-jackson' => __('GIF Michael Jackson', 'animated-theme-switcher'),
      'gif-deadpool'        => __('GIF Deadpool', 'animated-theme-switcher'),
      'gif-chika'           => __('GIF Chika', 'animated-theme-switcher'),
      'gif-hakari-dance'    => __('GIF Hakari Dance', 'animated-theme-switcher'),
      'star-reveal'         => __('Star Reveal', 'animated-theme-switcher'),
      'heart-reveal'        => __('Heart Reveal', 'animated-theme-switcher'),
      'checkerboard-reveal' => __('Checkerboard Reveal', 'animated-theme-switcher'),
      'ripple-reveal'       => __('Ripple Reveal', 'animated-theme-switcher'),
      'spiral-reveal'       => __('Spiral Reveal', 'animated-theme-switcher'),
      'iris-wipe-page'      => __('Iris Wipe', 'animated-theme-switcher'),
    ),
    __('Simple', 'animated-theme-switcher') => array(
      'fade'   => __('Fade', 'animated-theme-switcher'),
      'slide'  => __('Slide', 'animated-theme-switcher'),
      'scale'  => __('Scale', 'animated-theme-switcher'),
      'rotate' => __('Rotate', 'animated-theme-switcher'),
      'zoom'   => __('Zoom', 'animated-theme-switcher'),
    ),
    __('3D', 'animated-theme-switcher') => array(
      'flip'       => __('Flip', 'animated-theme-switcher'),
      'cube'       => __('Cube', 'animated-theme-switcher'),
      'skew-slide' => __('Skew Slide', 'animated-theme-switcher'),
      'page-curl'  => __('Page Curl', 'animated-theme-switcher'),
      'accordion'  => __('Accordion', 'animated-theme-switcher'),
      'doorway'    => __('Doorway', 'animated-theme-switcher'),
      'book-flip'  => __('Book Flip', 'animated-theme-switcher'),
      'fold'       => __('Fold', 'animated-theme-switcher'),
    ),
    __('Composite', 'animated-theme-switcher') => array(
      'blur'                   => __('Blur', 'animated-theme-switcher'),
      'diagonal-wipe'          => __('Diagonal Wipe', 'animated-theme-switcher'),
      'venetian-blinds-theme' => __('Venetian Blinds', 'animated-theme-switcher'),
      'wave-reveal-theme'     => __('Wave Reveal', 'animated-theme-switcher'),
      'curtain'                => __('Curtain', 'animated-theme-switcher'),
      'roll'                   => __('Roll', 'animated-theme-switcher'),
      'glitch'                 => __('Glitch', 'animated-theme-switcher'),
    ),
  );
}

/**
 * Return valid transition slugs.
 *
 * @return string[]
 */
function wptts_transition_slugs() {
  $slugs = array();
  foreach (wptts_transition_groups() as $transitions) {
    $slugs = array_merge($slugs, array_keys($transitions));
  }

  return $slugs;
}
