<?php
/**
 * Widget administration screen.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** WordPress Administration Bootstrap */
require_once __DIR__ . '/admin.php';

/** WordPress Administration Widgets API */
require_once ABSPATH . 'wp-admin/includes/widgets.php';

if ( ! current_user_can( 'edit_theme_options' ) ) {
	wp_die(
		'<h1>' . __( 'You need a higher level of permission.' ) . '</h1>' .
		'<p>' . __( 'Sorry, you are not allowed to edit theme options on this site.' ) . '</p>',
		403
	);
}

if ( ! current_theme_supports( 'widgets' ) ) {
	wp_die( __( 'The theme you are currently using is not widget-aware, meaning that it has no sidebars that you are able to change. For information on making your theme widget-aware, please <a href="https://developer.wordpress.org/themes/functionality/widgets/">follow these instructions</a>.' ) );
}

// Used in the HTML title tag.
$title       = __( 'Widgets' );
$parent_file = 'themes.php';

if ( wp_use_widgets_block_editor() ) {
	require ABSPATH . 'wp-admin/widgets-form-blocks.php';
} else {
	require ABSPATH . 'wp-admin/widgets-form.php';
}
