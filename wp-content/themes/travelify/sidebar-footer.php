<?php
/**
 * Displays the footer sidebar of the theme.
 *
 */
?>

<?php
	/**
	 * travelify_before_footer_widget
	 */
	do_action( 'travelify_before_footer_widget' );
?>

<?php
	/**
	 * travelify_footer_widget hook
	 *
	 * HOOKED_FUNCTION_NAME PRIORITY
	 *
	 * travelify_display_footer_widget 10
	 */
	do_action( 'travelify_footer_widget' );
?>

<?php
	/**
	 * travelify_after_footer_widget
	 */
	do_action( 'travelify_after_footer_widget' );
?>