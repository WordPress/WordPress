<?php
/**
 * Displays the right sidebar of the theme.
 *
 */
?>

<?php
	/**
	 * travelify_before_right_sidebar
	 */
	do_action( 'travelify_before_right_sidebar' );
?>

<?php
	/**
	 * travelify_right_sidebar hook
	 *
	 * HOOKED_FUNCTION_NAME PRIORITY
	 *
	 * travelify_display_right_sidebar 10
	 */
	do_action( 'travelify_right_sidebar' );
?>

<?php
	/**
	 * travelify_after_right_sidebar
	 */
	do_action( 'travelify_after_right_sidebar' );
?>