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
$title               = _x( 'Fonts', 'Font Library admin page title' );
$js_required_message = __( 'The Fonts screen requires JavaScript. Please enable JavaScript in your browser settings to install and manage fonts.' );

require_once ABSPATH . 'wp-admin/admin-header.php';

?>
<div class="wrap hide-if-js">
	<h1 class="wp-heading-inline"><?php echo esc_html( $title ); ?></h1>
	<?php
		wp_admin_notice(
			$js_required_message,
			array(
				'type'               => 'error',
				'additional_classes' => array( 'hide-if-js' ),
			)
		);
		?>
</div>
<?php

// Render the Font Library page
wp_font_library_wp_admin_render_page();

require_once ABSPATH . 'wp-admin/admin-footer.php';
