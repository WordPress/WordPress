<?php
/**
 * Template part for displaying the post navigation links in the same taxonomy
 * @package Twenty8teen
 */

if ( is_attachment() || is_single() ) {
	$taxes = get_object_taxonomies( get_post_type() );
	$a_tax = apply_filters( 'twenty8teen_same_cat', current( $taxes ), $taxes );
	if ( is_attachment() && wp_attachment_is_image() ) { 
		add_filter( 'navigation_markup_template', 'twenty8teen_nav_add_attachment_links', 10 );
	}
	add_filter( 'navigation_markup_template', 'twenty8teen_nav_add_widget_classes', 10 );
	the_post_navigation( array(
		'in_same_term' => true,
		'taxonomy' => empty( $a_tax ) ? 'category' : $a_tax,
	));
	remove_filter( 'navigation_markup_template', 'twenty8teen_nav_add_widget_classes', 10 );
	remove_filter( 'navigation_markup_template', 'twenty8teen_nav_add_attachment_links', 10 );
}
