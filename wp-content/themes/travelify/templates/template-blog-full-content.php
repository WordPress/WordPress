<?php
/**
 * Template Name: Blog Full Content Display
 *
 * Displays the Blog with Full Content Display.
 *
 */
?>

<?php get_header(); ?>

<?php
	/**
	 * travelify_before_main_container hook
	 */
	do_action( 'travelify_before_main_container' );
?>

<div id="container">
	<?php
		/**
		 * travelify_main_container hook
		 *
		 * HOOKED_FUNCTION_NAME PRIORITY
		 *
		 * travelify_content 10
		 */
		do_action( 'travelify_main_container' );
	?>
</div><!-- #container -->

<?php
	/**
	 * travelify_after_main_container hook
	 */
	do_action( 'travelify_after_main_container' );
?>

<?php get_footer(); ?>