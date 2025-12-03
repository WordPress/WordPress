<?php
/**
 * Template part for displaying the post navigation links
 * @package Twenty8teen
 */

if ( is_attachment() || is_single() ) {
	if ( is_attachment() && wp_attachment_is_image() ) { 
		add_filter( 'navigation_markup_template', 'twenty8teen_nav_add_attachment_links', 10 );
	}
	add_filter( 'navigation_markup_template', 'twenty8teen_nav_add_widget_classes', 10 );
	the_post_navigation();
	remove_filter( 'navigation_markup_template', 'twenty8teen_nav_add_widget_classes', 10 );
	remove_filter( 'navigation_markup_template', 'twenty8teen_nav_add_attachment_links', 10 );
}
