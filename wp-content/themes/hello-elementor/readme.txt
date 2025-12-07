=== Hello Elementor ===

Contributors: elemntor, KingYes, ariel.k, bainternet
Requires at least: 6.0
Tested up to: 6.8
Stable tag: 3.4.5
Version: 3.4.5
Requires PHP: 7.4
License: GNU General Public License v3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.html

A lightweight and minimalist WordPress theme for Elementor site builder.

== Description ==

Hello Elementor is a lightweight and minimalist WordPress theme that was built specifically to work seamlessly with the Elementor site builder plugin. The theme is free, open-source, and designed for users who want a flexible, easy-to-use, and customizable website.

The theme's main focus is on providing a solid foundation for users to build their own unique designs using the Elementor drag-and-drop site builder. It is optimized for speed and performance, and its simplicity and flexibility make it a great choice for both beginners and experienced website designers.

The theme supports common WordPress features which can be extended using a child-theme. In addition, there are several ways to add custom styles. It can be done from **Elementor**, using a child-theme, or with an external plugin. To customize the theme further, visit [Elementor developers docs](https://developers.elementor.com/docs/hello-elementor-theme/).

== Copyright ==

This theme, like WordPress, is distributed under the terms of GPL.
Use it as your springboard to building a site with ***Elementor***.

Hello Elementor bundles the following third-party resources:

Font Awesome icons for theme screenshot
License: SIL Open Font License, version 1.1.
Source: https://fontawesome.com/v4.7.0/

Image for theme screenshot, Copyright Jason Blackeye
License: CC0 1.0 Universal (CC0 1.0)
Source: https://stocksnap.io/photo/4B83RD7BV9

== Changelog ==

= 3.4.5 - 2025-10-27
* New: Add theme home to Finder
* Tweak: Improve banner behavior after clicking on action button
* Fix: Load styles correctly in Gutenberg pages
* Fix: Do not change menu name after Elementor activation
* Fix: Ensure quicklinks works correctly from home page

= 3.4.4 - 2025-06-08 =
* Tweak: Improve Header/Footer edit access from theme Home

= 3.4.3 - 2025-05-26 =
* Fix: Settings page empty after 3.4.0 in translated sites
* Fix: PHP 8.4 deprecation notice

= 3.4.2 - 2025-05-19 =
* Tweak: Set Home links font weight to regular
* Tweak: Dart SASS 3.0.0 - resolve scss deprecated warnings
* Fix: Settings page empty after 3.4.0

= 3.4.1 - 2025-05-08 =
* Fix: Critical Error after theme update [critical-error-on-website-54/#post-18454740](https://wordpress.org/support/topic/critical-error-on-website-54/#post-18454740)

= 3.4.0 - 2025-05-05 =
* New: Add Theme Home
* Tweak: Theme settings page style

= 3.3.0 - 2025-01-21 =
* Tweak: Added changelog link in theme settings
* Tweak: Updated minimum required Safari version to 15.5
* Tweak: Update autoprefixer to latest versions

= 3.2.1 - 2024-12-16 =
* Fix: Gutenberg editor expanded disproportionately after adding support for `theme.json` ([#430](https://github.com/elementor/hello-theme/issues/430))
* Fix: Use CSS logical properties in the theme
* Fix: Add ARIA attributes to header nav menu

= 3.2.0 - 2024-12-15 =
* Tweak: Convert classic to hybrid theme with block-editor support
* Tweak: Added new design options to header/footer
* Tweak: Update `Tested up to 6.7`
* Fix: Minify JS files ([#419](https://github.com/elementor/hello-theme/issues/419))

= 3.1.1 - 2024-07-30 =
* Fix: Use consistent `<h2>` for comments title and comment form

= 3.1.0 - 2024-06-19 =
* Tweak: Update `Requires PHP 7.4`
* Tweak: Update `Tested up to 6.5`
* Tweak: Add the ability to style the brand layout
* Tweak: Remove deprecated Elementor code
* Tweak: Restore default focus styling inside the theme
* Tweak: Add `aria-label` attribute to various `<nav>` elements
* Tweak: Improve mobile menu keyboard accessibility
* Tweak: Semantic mobile menu toggle button
* Fix: The header renders redundant `<p>` when tagline is empty
* Fix: Single post renders redundant wrapping `<div>` when it has no tags
* Fix: Remove redundant wrapping `<div>` from `wp_nav_menu()` output
* Fix: Wrap page `<h1>` with `<div>`, not `<header>`
* Fix: Use consistent `<h3>` for comments title and comment form
* Fix: Remove heading tags from dynamic header/footer
* Fix: Mobile Menu hamburger is not visible for logged-out users in some cases ([#369](https://github.com/elementor/hello-theme/issues/369))
* Fix: Remove duplicate ID attributes in the header mobile menu
* Fix: Remove redundant table styles ([#311](https://github.com/elementor/hello-theme/issues/311))
* Fix: Remove redundant space bellow Site Logo in the header/footer
* Fix: Remove redundant CSS from dynamic header/footer layout
* Fix: Separate post tags in single post ([#304](https://github.com/elementor/hello-theme/issues/304))
* Fix: Display `the_tags()` after `wp_link_pages()`
* Fix: Remove page break navigation from archives when using `<!--nextpage-->`
* Fix: Style posts pagination component layout
* Fix: Add RTL support to pagination arrows in archive pages
* Fix: Update pagination prev/next labels and positions ([#404](https://github.com/elementor/hello-theme/issues/404))
* Fix: Check if Elementor is loaded when using dynamic header & footer

= 3.0.2 - 2024-05-28 =
* Internal: Version bump release to refresh WordPress repository

= 3.0.1 - 2024-01-24 =
* Fix: Harden security for admin notice dismiss button
* Fix: Add `alt` attribute to all the images in the dashboard

= 3.0.0 - 2023-12-26 =
* New: Option to disable cross-site header & footer
* Tweak: Update `Requires PHP 7.3`
* Tweak: Update `Tested up to 6.4`
* Tweak: Move cross-site header & footer styles to a separate CSS file
* Tweak: Don't load `header-footer.min.css` when disabling header & footer
* Tweak: Don't load `hello-frontend.min.js` when disabling header & footer
* Tweak: Replace jQuery code with vanilla JS in the frontend
* Tweak: Replace jQuery code with vanilla JS in WordPress admin
* Tweak: Remove unused JS code from the frontend
* Tweak: Remove unused CSS code from the editor
* Tweak: Remove unnecessary `role` attributes from HTML landmark elements
* Tweak: Link from Elementor Site Settings to Hello Theme Settings
* Fix: Dynamic script version for better caching

= 2.9.0 - 2023-10-25 =
* New: Introducing the new settings page for the theme
* New: Option to disable description meta tag
* New: Option to disable skip link
* New: Option to disable page title
* New: Option to unregister Hello style.css
* New: Option to unregister Hello theme.css
* Tweak: Update `Requires at least 6.0`
* Tweak: Update `Tested up to 6.3`

= 2.8.1 - 2023-07-05 =
* Tweak: Added additional CSS selectors to apply RTL on comments
* Fix: Comment area style regression

= 2.8.0 - 2023-07-04 =
* Tweak: Update `Requires PHP 7.0`
* Tweak: Added description meta tag with excerpt text
* Tweak: Use CSS logical properties rather than physical properties
* Tweak: Replace legacy `page-break-*` CSS properties with `break-*` properties
* Tweak: Remove duplicate CSS classes for screen readers
* Tweak: Merge similar translation strings (i18n)

= 2.7.1 - 2023-03-27 =
* Tweak: Add excerpt support for pages
* Tweak: When post comments are closed, display it to the user
* Fix: Empty "Skip to content" href ([#276](https://github.com/elementor/hello-theme/issues/276))
* Fix: Child themes using `hello_elementor_body_open()` no longer working ([#278](https://github.com/elementor/hello-theme/issues/278))

= 2.7.0 - 2023-03-26 =
* Tweak: Update `Requires at least 5.9`
* Tweak: Update `Tested up to 6.2`
* Tweak: Remove backwards compatibility support for `wp_body_open()`
* Tweak: Match `search.php` markup to `archive.php` markup
* Tweak: Check if posts have featured images set
* Tweak: Remove unnecessary `role` attributes from HTML landmark elements
* Tweak: Escape translation strings for secure HTML output
* Tweak: Use i18n function to make the "Menu" string translatable
* Tweak: Minify SVG assets
* Tweak: Make header nav-menu keyboard accessible
* Tweak: Add `role="button"` to the nav-menu toggle for better accessibility
* Tweak: Toggle mobile nav-menu with `Enter` & `Space` keyboard keys
* Tweak: Add `hello_elementor_enable_skip_link` filter to enable/disable the skip link
* Tweak: Add `hello_elementor_skip_link_url` filter to change skip link URL
* Tweak: Use theme CSS not Elementor plugins CSS
* Tweak: Added support for the new Elementor version
* Tweak: Update autoprefixer to exclude dead browsers
* Tweak: Delete deprecated `elementor_hello_theme_load_textdomain` filter hook
* Tweak: Delete deprecated `elementor_hello_theme_register_menus` filter hook
* Tweak: Delete deprecated `elementor_hello_theme_add_theme_support` filter hook
* Tweak: Delete deprecated `elementor_hello_theme_add_woocommerce_support` filter hook
* Tweak: Delete deprecated `elementor_hello_theme_enqueue_style` filter hook
* Tweak: Delete deprecated `elementor_hello_theme_register_elementor_locations` filter hook
* Tweak: Added additional and `custom` units to header & footer panels
* Tweak: Link to Elementor "Site Identity" panel from the header & footer panels
* Tweak: Delete the `hello_elementor_load_textdomain` filter hook

= 2.6.1 - 2022-07-11 =
* Tweak: Tables looks weird on dark backgrounds ([#126](https://github.com/elementor/hello-theme/issues/126))
* Fix: Remove unnecessary PHP tags ([#213](https://github.com/elementor/hello-theme/issues/213))

= 2.6.0 - 2022-07-10 =
* Tweak: Added `theme_support` for `script` and `style` to avoid validation warnings ([#184](https://github.com/elementor/hello-theme/issues/184))
* Tweak: Sanitized content for allowed HTML tags in post title ([#118](https://github.com/elementor/hello-theme/issues/118))
* Tweak: Changed the containers to `max-width: 1140px` instead of `960px` to align with the header-footer width
* Tweak: Centering the page title for better consistency in all cases
* Tweak: Added link between the customizer to Elementor global settings
* Tweak: Added Skip Links to custom or dynamic header for better accessibility
* Fix: Added output escaping in several places ([#194](https://github.com/elementor/hello-theme/issues/194))
* Fix: Post Password Form Submit button alignment (Props [@romanbondar](https://github.com/romanbondar))
* Fix: Fatal error when kit doesn't exist or needs to be recreated ([#175](https://github.com/elementor/hello-theme/issues/175))

= 2.5.0 - 2022-01-26 =
* Tweak: Added keyboard navigation to Hello Elementor theme menus
* Tweak: Added Skip Links and `#content` for the main wrapper for better accessibility ([#133](https://github.com/elementor/hello-theme/issues/133))
* Tweak: Added underline for text links in Post Content for better accessibility
* Tweak: Removed `outline: none` from inputs for better accessibility
* Fix: Footer menu location is not being presented on sites that are not running Elementor

= 2.4.2 - 2021-12-20 =
* Tweak: Use HTTPS in XFN profile link to prevent mixed content error ([Topic](https://wordpress.org/support/topic/url-scheme-in-xfn-profile-link/))
* Tweak: Remove comments in `style.min.css` output ([#179](https://github.com/elementor/hello-theme/issues/179))
* Tweak: Promoted Hello Elementor theme Header & Footer experiment status to Stable
* Tweak: Added compatibility for upcoming WordPress version 5.9

= 2.4.1 - 2021-07-07 =
* Fix: Hello Elementor theme Header & Footer experiment should be inactive for existing sites

= 2.4.0 - 2021-06-29 =
* New: Introducing Header and Footer site elements as an Elementor Experiment
* Tweak: Updated Elementor admin notices UI

= 2.3.1 - 2020-12-28 =
* Tweak: Improved UI for table elements
* Tweak: Added support for Gutenberg Wide and Full image formats (Props [@ramiy](https://github.com/ramiy))
* Tweak: Added font smoothing
* Tweak: Update `Tested up to 5.6`
* Tweak: Update `Requires PHP 5.6`
* Fix: Adjusted font-family in `code`, `pre`, `kbd` and `samp` elements (Props [@75th](https://github.com/75th))

= 2.3.0 - 2020-04-19 =
* Tweak: Removed caption centering by default to allow alignment using Elementor (Props [@cirkut](https://github.com/cirkut))
* Tweak: Removed `text-align` property from table elements to avoid alignment issue in RTL websites (Props [@ramiy](https://github.com/ramiy))
* Tweak: Added `input[type="url"]` to CSS reset rules ([#109](https://github.com/elementor/hello-theme/issues/109))
* Tweak: Update `Tested up to 5.4`

= 2.2.2 - 2019-12-23 =
* Fix: Conflicts with minifier `cssnano` and CSS animations (Props [@CeliaRozalenM](https://github.com/CeliaRozalenM))
* Fix: Max-width property is missing in `_archive.scss` (Props [@redpik](https://github.com/redpik))

= 2.2.1 - 2019-09-10 =
* Tweak: Added max width to `wp-caption` ([#91](https://github.com/elementor/hello-theme/issues/91))
* Tweak: Added support of `wp_body_open`

= 2.2.0 - 2019-07-22 =
* Tweak: Added viewport content filter ([#49](https://github.com/elementor/hello-theme/issues/49))
* Tweak: Added support Hide Title in Elementor
* Tweak: Adhere to TRT's Theme Sniffer

= 2.1.2 - 2019-06-19 =
* Tweak: Added theme version to enqueued styles
* Tweak: Remove header tags with `hello_elementor_page_title` filter

= 2.1.1 - 2019-06-13 =
* Tweak: Rename `Install Elementor Now` button to `Install Elementor`

= 2.1.0 - 2019-06-12 =
* New: Added basic theme styling
* New: Added tagline under the site name in header
* New: Added `hello_elementor_page_title` filter for show\hide page title
* New: Added `hello_elementor_enqueue_theme_style` filter for enqueue theme-specific style
* Tweak: Hide site name & tagline if logo file is exist
* Tweak: Hide default page list when there is no primary menu
* Tweak: Removed `#main` in `archive.php`, `single.php`, `search.php` & `404.php` files
* Tweak: Removed `#site-header` in `header.php` file
* Tweak: Replaced `#top-menu` with `.site-navigation`
* Tweak: Removed custom SCSS directory, it is recommended to use child theme instead of editing parent theme

= 2.0.7 - 2019-06-04 =
* Tweak: Added nextpage support to `single.php`
* Tweak: Keep both original and minified css files
* Tweak: Removed `flexible-header`, `custom-colors`, `editor-style` tags

= 2.0.6 - 2019-05-08 =
* Tweak: Removed irrelevant font family from `$font-family-base`
* Fix: Minified `style.css` for better optimization

= 2.0.5 - 2019-05-21 =
* New: Introducing [Hello Theme Child](https://github.com/elementor/hello-theme-child)
* Tweak: Enqueue only parent theme stylesheet
* Tweak: Added admin notice box for recommending Elementor plugin

= 2.0.4 - 2019-05-20 =
* Tweak: Removed `accessibility-ready` tag from `style.css`

= 2.0.3 - 2019-05-19 =
* Tweak: Removed `accessibility-ready` tag

= 2.0.2 - 2019-05-13 =
* Tweak: Added `hello_elementor_content_width` filter, as per WordPress best practice

= 2.0.1 - 2019-05-12 =
* Tweak: Updated theme screenshot (following comment by WP Theme Review team)

= 2.0.0 - 2019-05-12 =
* Tweak: Updated theme screenshot (following comment by WP Theme Review team)
* Tweak: Add Copyright & Image and Icon License sections in readme (following comment by WP Theme Review team)
* Tweak: Remove duplicated call to `add_theme_support( 'custom-logo')`
* Tweak: Readme file grammar & spelling
* Tweak: Update `Tested up to 5.2`
* Tweak: Change functions.php methods names prefix from `hello_elementor_theme_` to `hello_elementor_`
* Tweak: Change hook names to fit theme's name. Old hooks are deprecated, users are urged to update their code where needed
* Tweak: Update style for `img`, `textarea`, 'label'

= 1.2.0 - 2019-02-12 =
* New: Added classic-editor.css for Classic editor
* Tweak: A lot of changes to match theme review guidelines
* Tweak: Updated theme screenshot

= 1.1.1 - 2019-01-28 =
* Tweak: Removed padding reset for lists

= 1.1.0 - 2018-12-26 =
* New: Added SCSS & do thorough style reset
* New: Added readme file
* New: Added `elementor_hello_theme_load_textdomain` filter for load theme's textdomain
* New: Added `elementor_hello_theme_register_menus` filter for register the theme's default menu location
* New: Added `elementor_hello_theme_add_theme_support` filter for register the various supported features
* New: Added `elementor_hello_theme_add_woocommerce_support` filter for register woocommerce features, including product-gallery zoom, swipe & lightbox features
* New: Added `elementor_hello_theme_enqueue_style` filter for enqueue style
* New: Added `elementor_hello_theme_register_elementor_locations` filter for register elementor settings
* New: Added child-theme preparations
* New: Added template part search
* New: Added translation support
* Tweak: Re-write of already existing template parts

= 1.0.0 - 2018-03-19 =
* Initial Public Release
