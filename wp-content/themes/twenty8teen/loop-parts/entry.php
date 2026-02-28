<?php
/**
 * Template part for displaying posts
 *
 * @package Twenty8teen
 */

	twenty8teen_widget_set_classes( wp_basename( __FILE__, '.php' ), null );
	$post_format = twenty8teen_get_type_or_format();
	get_template_part( 'loop-parts/entry-header', $post_format );
	get_template_part( 'loop-parts/featured-image', $post_format );
	get_template_part( 'loop-parts/entry-content', $post_format );
	get_template_part( 'loop-parts/entry-footer', $post_format );
