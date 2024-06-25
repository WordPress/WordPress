<?php
/**
 * Twenty Fifteen back compat functionality
 *
 * Prevents Twenty Fifteen from running on WordPress versions prior to 4.1,
 * since this theme is not meant to be backward compatible beyond that and
 * relies on many newer functions and markup changes introduced in 4.1.
 *
 * @package WordPress
 * @subpackage Twenty_Fifteen
 * @since Twenty Fifteen 1.0
 */

/**
 * Prevent switching to Twenty Fifteen on old versions of WordPress.
 *
 * Switches to the default theme.
 *
 * @since Twenty Fifteen 1.0
 */
function twentyfifteen_switch_theme() {
	switch_theme( WP_DEFAULT_THEME, WP_DEFAULT_THEME );
	unset( $_GET['activated'] );
	add_action( 'admin_notices', 'twentyfifteen_upgrade_notice' );
}
add_action( 'after_switch_theme', 'twentyfifteen_switch_theme' );

/**
 * Add message for unsuccessful theme switch.
 *
 * Prints an update nag after an unsuccessful attempt to switch to
 * Twenty Fifteen on WordPress versions prior to 4.1.
 *
 * @since Twenty Fifteen 1.0
 */
function twentyfifteen_upgrade_notice() {
	printf(
		'<div class="error"><p>%s</p></div>',
		sprintf(
			/* translators: %s: WordPress version. */
			__( 'Twenty Fifteen requires at least WordPress version 4.1. You are running version %s. Please upgrade and try again.', 'twentyfifteen' ),
			$GLOBALS['wp_version']
		)
	);
}

/**
 * Prevent the Customizer from being loaded on WordPress versions prior to 4.1.
 *
 * @since Twenty Fifteen 1.0
 */
function twentyfifteen_customize() {
	wp_die(
		sprintf(
			/* translators: %s: WordPress version. */
			__( 'Twenty Fifteen requires at least WordPress version 4.1. You are running version %s. Please upgrade and try again.', 'twentyfifteen' ),
			$GLOBALS['wp_version']
		),
		'',
		array(
			'back_link' => true,
		)
	);
}
add_action( 'load-customize.php', 'twentyfifteen_customize' );

/**
 * Prevent the Theme Preview from being loaded on WordPress versions prior to 4.1.
 *
 * @since Twenty Fifteen 1.0
 */
function twentyfifteen_preview() {
	if ( isset( $_GET['preview'] ) ) {
		wp_die(
			sprintf(
				/* translators: %s: WordPress version. */
				__( 'Twenty Fifteen requires at least WordPress version 4.1. You are running version %s. Please upgrade and try again.', 'twentyfifteen' ),
				$GLOBALS['wp_version']
			)
		);
	}
}
add_action( 'template_redirect', 'twentyfifteen_preview' );
