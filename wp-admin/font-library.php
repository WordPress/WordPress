<?php
/**
 * Font Library administration screen.
 *
 * @package WordPress
 * @subpackage Administration
 * @since 7.0.0
 */

/** WordPress Administration Bootstrap */
require_once __DIR__ . '/admin.php';

if ( ! current_user_can( 'edit_theme_options' ) ) {
	wp_die(
		'<h1>' . __( 'You need a higher level of permission.' ) . '</h1>' .
		'<p>' . __( 'Sorry, you are not allowed to manage fonts on this site.' ) . '</p>',
		403
	);
}

// Check if Gutenberg build files are available
if ( ! function_exists( 'wp_font_library_wp_admin_render_page' ) ) {
	wp_die(
		'<h1>' . __( 'Font Library is not available.' ) . '</h1>' .
		'<p>' . __( 'The Font Library requires Gutenberg build files. Please run <code>npm install</code> to build the necessary files.' ) . '</p>',
		503
	);
}

// Set the page title
$title = _x( 'Fonts', 'Font Library admin page title' );

require_once ABSPATH . 'wp-admin/admin-header.php';

// Render the Font Library page
wp_font_library_wp_admin_render_page();

require_once ABSPATH . 'wp-admin/admin-footer.php';
