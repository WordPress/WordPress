<?php
/**
 * Twenty Thirteen back compat functionality
 *
 * Prevents Twenty Thirteen from running on WordPress versions prior to 3.6,
 * since this theme is not meant to be backward compatible and relies on
 * many new functions and markup changes introduced in 3.6.
 *
 * @package WordPress
 * @subpackage Twenty_Thirteen
 * @since Twenty Thirteen 1.0
 */

/**
 * Prevent switching to Twenty Thirteen on old versions of WordPress.
 *
 * Switches to the default theme.
 *
 * @since Twenty Thirteen 1.0
 */
function twentythirteen_switch_theme() {
	switch_theme( WP_DEFAULT_THEME, WP_DEFAULT_THEME );
	unset( $_GET['activated'] );
	add_action( 'admin_notices', 'twentythirteen_upgrade_notice' );
}
add_action( 'after_switch_theme', 'twentythirteen_switch_theme' );

/**
 * Add message for unsuccessful theme switch.
 *
 * Prints an update nag after an unsuccessful attempt to switch to
 * Twenty Thirteen on WordPress versions prior to 3.6.
 *
 * @since Twenty Thirteen 1.0
 */
function twentythirteen_upgrade_notice() {
	printf(
		'<div class="error"><p>%s</p></div>',
		sprintf(
			/* translators: %s: WordPress version. */
			__( 'Twenty Thirteen requires at least WordPress version 3.6. You are running version %s. Please upgrade and try again.', 'twentythirteen' ),
			$GLOBALS['wp_version']
		)
	);
}

/**
 * Prevent the Customizer from being loaded on WordPress versions prior to 3.6.
 *
 * @since Twenty Thirteen 1.0
 */
function twentythirteen_customize() {
	wp_die(
		sprintf(
			/* translators: %s: WordPress version. */
			__( 'Twenty Thirteen requires at least WordPress version 3.6. You are running version %s. Please upgrade and try again.', 'twentythirteen' ),
			$GLOBALS['wp_version']
		),
		'',
		array(
			'back_link' => true,
		)
	);
}
add_action( 'load-customize.php', 'twentythirteen_customize' );

/**
 * Prevent the Theme Preview from being loaded on WordPress versions prior to 3.4.
 *
 * @since Twenty Thirteen 1.0
 */
function twentythirteen_preview() {
	if ( isset( $_GET['preview'] ) ) {
		wp_die(
			sprintf(
				/* translators: %s: WordPress version. */
				__( 'Twenty Thirteen requires at least WordPress version 3.6. You are running version %s. Please upgrade and try again.', 'twentythirteen' ),
				$GLOBALS['wp_version']
			)
		);
	}
}
add_action( 'template_redirect', 'twentythirteen_preview' );
