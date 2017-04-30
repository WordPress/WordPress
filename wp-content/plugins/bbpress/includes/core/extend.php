<?php

/**
 * bbPress Extentions
 *
 * There's a world of really cool plugins out there, and bbPress comes with
 * support for some of the most popular ones.
 *
 * @package bbPress
 * @subpackage Extend
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Loads Akismet inside the bbPress global class
 *
 * @since bbPress (r3277)
 *
 * @return If bbPress is not active
 */
function bbp_setup_akismet() {

	// Bail if no akismet
	if ( !defined( 'AKISMET_VERSION' ) ) return;

	// Bail if Akismet is turned off
	if ( !bbp_is_akismet_active() ) return;

	// Include the Akismet Component
	require( bbpress()->includes_dir . 'extend/akismet.php' );

	// Instantiate Akismet for bbPress
	bbpress()->extend->akismet = new BBP_Akismet();
}

/**
 * Requires and creates the BuddyPress extension, and adds component creation
 * action to bp_init hook. @see bbp_setup_buddypress_component()
 *
 * @since bbPress (r3395)
 * @return If BuddyPress is not active
 */
function bbp_setup_buddypress() {

	if ( ! function_exists( 'buddypress' ) ) {

		/**
		 * Helper for BuddyPress 1.6 and earlier
		 *
		 * @since bbPress (r4395)
		 * @return BuddyPress
		 */
		function buddypress() {
			return isset( $GLOBALS['bp'] ) ? $GLOBALS['bp'] : false;
		}
	}

	// Bail if in maintenance mode
	if ( ! buddypress() || buddypress()->maintenance_mode )
		return;

	// Include the BuddyPress Component
	require( bbpress()->includes_dir . 'extend/buddypress/loader.php' );

	// Instantiate BuddyPress for bbPress
	bbpress()->extend->buddypress = new BBP_Forums_Component();
}
