<?php
/**
 * Server-side rendering of the `core/site-tagline` block.
 *
 * @package WordPress
 */

/**
 * Renders the `core/site-tagline` block on the server.
 *
 * @since 5.8.0
 *
 * @param array $attributes The block attributes.
 *
 * @return string The render.
 */
function gutenberg_render_block_core_site_tagline( $attributes ) {
	$site_tagline = get_bloginfo( 'description' );
	if ( ! $site_tagline ) {
		return;
	}

	$tag_name           = 'p';
	$align_class_name   = empty( $attributes['textAlign'] ) ? '' : "has-text-align-{$attributes['textAlign']}";
	$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => $align_class_name ) );

	if ( isset( $attributes['level'] ) && 0 !== $attributes['level'] ) {
		$tag_name = 'h' . (int) $attributes['level'];
	}

	return sprintf(
		'<%1$s %2$s>%3$s</%1$s>',
		$tag_name,
		$wrapper_attributes,
		$site_tagline
	);
}

/**
 * Registers the `core/site-tagline` block on the server.
 *
 * @since 5.8.0
 */
function gutenberg_register_block_core_site_tagline() {
	register_block_type_from_metadata(
		__DIR__ . '/site-tagline',
		array(
			'render_callback' => 'gutenberg_render_block_core_site_tagline',
		)
	);
}

add_action( 'init', 'gutenberg_register_block_core_site_tagline', 20 );
