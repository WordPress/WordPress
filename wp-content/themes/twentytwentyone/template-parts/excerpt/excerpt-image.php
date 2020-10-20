<?php
/**
 * Show the appropriate content for the Image post format.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package WordPress
 * @subpackage Twenty_Twenty_One
 * @since 1.0.0
 */

// If there is no featured-image, print the first image block found.
if (
	! twenty_twenty_one_can_show_post_thumbnail() &&
	has_block( 'core/image', get_the_content() )
) {

	twenty_twenty_one_print_first_instance_of_block( 'core/image', get_the_content() );
}

the_excerpt();
