<?php
/**
 * Backwards compatibility handling for routes used by the plugin consumers.
 * This doesn't need to be back ported to core and will be removed after WP 6.1,
 * to ensure that the plugin consumers have enough time to migrate to the core's
 * route (`site-editor.php`).
 *
 * Allows the `theme.php` route and redirects the following routes:
 * - `themes.php?page=gutenberg-edit-site`
 * - `admin.php?page=gutenberg-edit-site`
 *
 * To `site-editor.php`.
 *
 * The old routes have been deprecated and removed in Gutenberg 13.7.0, but third-party
 * consumer code might still be referencing them. In order to not break the Site Editor
 * flows, we don't fully remove the old routes, but redirect them to the core's one.
 *
 * @see https://github.com/WordPress/gutenberg/pull/41306
 *
 * @package gutenberg
 */

/**
 * Allows the old routes. Without this, trying to access the old Site Editor
 * routes results in a HTTP 403 error.
 *
 * Allowing the route is done by adding an wp-admin submenu page that won't be rendered.
 */
function gutenberg_site_editor_menu() {
	if ( wp_is_block_theme() ) {
		add_submenu_page( '', '', '', 'edit_theme_options', 'gutenberg-edit-site', '__return_empty_string' );
	}
}
add_action( 'admin_menu', 'gutenberg_site_editor_menu', 9 );

/**
 * Does the actual redirect to the new route upon triggering of the `load-appearance_page_gutenberg-edit-site` action.
 */
function gutenberg_redirect_deprecated_to_new_site_editor_page() {
		wp_safe_redirect( 'site-editor.php' );
		exit;
}
add_action( 'load-appearance_page_gutenberg-edit-site', 'gutenberg_redirect_deprecated_to_new_site_editor_page' );
add_action( 'load-admin_page_gutenberg-edit-site', 'gutenberg_redirect_deprecated_to_new_site_editor_page' );
