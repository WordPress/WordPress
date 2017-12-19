<?php
/**
 * Displays the left sidebar of the theme.
 *
 */
?>

<?php
	/**
	 * travelify_before_left_sidebar
	 */
	do_action( 'travelify_before_left_sidebar' );
?>

<?php
	/**
	 * travelify_left_sidebar hook
	 *
	 * HOOKED_FUNCTION_NAME PRIORITY
	 *
	 * travelify_display_left_sidebar 10
	 */
	do_action( 'travelify_left_sidebar' );
?>

<?php
	/**
	 * travelify_after_left_sidebar
	 */
	do_action( 'travelify_after_left_sidebar' );
?>