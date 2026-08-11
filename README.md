# Animated Theme Switcher

An animated, accessible light/dark theme switcher for WordPress, created by [Reza Qalekhani](https://byreza.net). This plugin is inspired by [Transition Kit](https://github.com/AbdullahMukadam/Transition-kit) and independently adapted for WordPress. It uses the browser's native View Transitions API and switches themes instantly when the selected animation is unsupported or the visitor prefers reduced motion.

The visitor's choice is saved in `localStorage`. On the first visit, the plugin follows the operating system's color scheme.

## Features

- 36 selectable transition templates
- Floating top, floating bottom, shortcode, and selected-menu placement
- Configurable background, surface, text, muted text, border, and link colors
- WordPress-aware dark palette for block and classic themes
- Accessible labels and reduced-motion support
- Translation-ready public and admin strings
- Sass and npm development workflow
- Automated, tested GitHub release ZIPs

## Requirements

- WordPress 6.0 or newer
- PHP 7.4 or newer
- Node.js and npm, required only for Sass development and release builds
- Composer, required only for the optional WordPress PHP stubs

## Installation

1. Download the plugin ZIP from the [latest GitHub release](../../releases/latest).
2. Extract the ZIP and copy the `animated-theme-switcher` directory into `wp-content/plugins/`.
3. In WordPress, go to **Plugins > Installed Plugins**.
4. Activate **Animated Theme Switcher**.
5. Open **Settings > Theme Switcher**.

The default settings are **Circle Reveal**, **Floating bottom**, and the bundled dark palette.

## Configuration

### Transition template

Choose from all 36 Transition Kit effects under **Settings > Theme Switcher**. GIF templates load their animated masks from Tenor and require network access in the visitor's browser.

### Button location

Available locations are:

- **Floating bottom**
- **Floating top**
- **In navigation menu**

When menu placement is selected, choose the exact classic WordPress navigation menu that should receive the switcher. Other menus remain unchanged.

### Dark-mode colors

Administrators can configure these palette colors:

- **Background** - main page and primary content backgrounds
- **Surface** - widgets, sidebars, fields, tables, code blocks, and comments
- **Text** - headings, paragraphs, labels, lists, and default body text
- **Muted text** - dates, author details, metadata, and secondary text
- **Border** - controls, tables, widgets, comments, and horizontal rules
- **Link** - standard text links; the active theme continues to control buttons

Each setting displays its related CSS custom property and helper text in the admin screen.

## Usage

### Floating button

Select **Floating bottom** or **Floating top**, then use the sun/moon button on the front end. The selection persists across page loads.

### Navigation menu

Select **In navigation menu**, then select the desired menu in the **Navigation menu** field.

### Shortcode

Add the shortcode to a post, page, widget, or template:

```text
[animated_theme_switcher]
```

The shortcode renders an inline button and suppresses the automatic floating button on that page.

Use it in a PHP template with:

```php
<?php echo do_shortcode( '[animated_theme_switcher]' ); ?>
```

### Hide the automatic floating button

Add this filter to the active theme's `functions.php` or a site plugin:

```php
add_filter( 'wptts_show_floating_toggle', '__return_false' );
```

## Theme Integration

The plugin sets the current mode on the root element:

```html
<html data-wptts-theme="dark"></html>
```

The generic dark palette exposes:

```css
--wptts-background
--wptts-surface
--wptts-text
--wptts-muted
--wptts-border
--wptts-link
```

Themes can use the attribute and properties in custom styles:

```css
html[data-wptts-theme="dark"] {
  --site-background: var(--wptts-background);
  --site-text: var(--wptts-text);
}

body {
  color: var(--site-text);
  background: var(--site-background);
}
```

Disable the generic palette when the active theme supplies complete dark-mode styles:

```php
add_filter( 'wptts_use_generic_filter', '__return_false' );
```

## JavaScript Event

The plugin dispatches `wptts:change` on `window` after applying a theme:

```js
window.addEventListener("wptts:change", (event) => {
  console.log(event.detail.theme); // "light" or "dark"
});
```

## Development

Install the Sass development dependency:

```bash
npm install
```

Optionally install the WordPress PHP stubs used by editors and static tools:

```bash
composer install
```

Watch both Sass files:

```bash
npm run watch
```

Create minified frontend and admin stylesheets:

```bash
npm run build
```

Build the styles and run JavaScript syntax, PHP syntax, and plugin smoke checks:

```bash
npm test
```

Sass sources live in `assets/scss/`; compiled stylesheets are written to `assets/css/`. Browser scripts live in `assets/js/`.

## Plugin Structure

```text
animated-theme-switcher.php    Plugin metadata, constants, and bootstrap
includes/i18n.php              Translation loading
includes/transitions.php       Transition catalog
includes/options.php           Defaults, validation, and palette CSS
includes/frontend.php          Assets, markup, shortcode, and placement
includes/admin.php             Settings registration and admin screen
assets/scss/                   Frontend and admin Sass sources
assets/css/                    Compiled frontend and admin stylesheets
assets/js/                     Frontend and admin JavaScript
languages/                     Translation template and locale files
tests/smoke.php                Lightweight integration checks
composer.json                  Optional WordPress PHP stubs
```

The admin module and its assets load only for dashboard requests and only on the plugin's settings screen where applicable.

## Creating a GitHub Release

1. Update the version in `animated-theme-switcher.php` and `package.json`.
2. Add an entry to the **Changelog** in this `README.md` that covers every user-facing change in the release.
3. Commit the changes and push a matching tag such as `v1.1`, or run **Build and release plugin** manually from the Actions tab.

The workflow installs dependencies with `npm ci`, builds and tests the plugin, creates `animated-theme-switcher-{version}.zip`, and attaches it to a GitHub Release. Files listed in `.distignore` are omitted from the ZIP.

## Translations

All public and admin strings use the `animated-theme-switcher` text domain.
The translation template is `languages/animated-theme-switcher.pot`.
Compile locale files as `animated-theme-switcher-{locale}.po` and `animated-theme-switcher-{locale}.mo`, or use standard WordPress language packs.

## Browser Behavior

- Browsers supporting `document.startViewTransition()` receive the selected transition.
- Other browsers switch themes immediately.
- Visitors using `prefers-reduced-motion: reduce` receive no transition.

## Changelog

### v1.0

- Organized plugin code into focused frontend, admin, options, transitions, and translation modules.
- Added admin controls for all six dark-mode palette colors.
- Added a menu selector so menu placement targets one chosen navigation menu.
- Replaced body inversion with a WordPress-aware dark palette that covers full-page content.
- Added all 36 transition templates.
- Added an admin settings screen.
- Added top, bottom, and navigation-menu locations.
- Added a translation template and translatable runtime labels.
- Initial release.
