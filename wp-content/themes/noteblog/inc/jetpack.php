<?php
/**
 * Jetpack Compatibility File
 * See: https://jetpack.me/
 *
 * Please browse readme.txt for credits and forking information
 * @package noteblog
 */

/**
 * Add theme support for Infinite Scroll.
 * See: https://jetpack.me/support/infinite-scroll/
 */
function noteblog_jetpack_setup() {
	add_theme_support( 'infinite-scroll', array(
		'container' => 'main',
		'render'    => 'noteblog_infinite_scroll_render',
		'footer'    => 'page',
	) );
} // end function noteblog_jetpack_setup
add_action( 'after_setup_theme', 'noteblog_jetpack_setup' );

/**
 * Custom render function for Infinite Scroll.
 */
function noteblog_infinite_scroll_render() {
	while ( have_posts() ) {
		the_post();
		get_template_part( 'template-parts/content', get_post_format() );
	}
} // end function noteblog_infinite_scroll_render
