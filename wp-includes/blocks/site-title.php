<?php
/**
 * Server-side rendering of the `core/site-title` block.
 *
 * @package WordPress
 */

/**
 * Renders the `core/site-title` block on the server.
 *
 * @param array $attributes The block attributes.
 *
 * @return string The render.
 */
function render_block_core_site_title( $attributes ) {
	$site_title = get_bloginfo( 'name' );
	if ( ! $site_title ) {
		return;
	}

	$tag_name         = 'h1';
	$align_class_name = empty( $attributes['textAlign'] ) ? '' : "has-text-align-{$attributes['textAlign']}";

	if ( isset( $attributes['level'] ) ) {
		$tag_name = 0 === $attributes['level'] ? 'p' : 'h' . (int) $attributes['level'];
	}

	if ( $attributes['isLink'] ) {
		$aria_current = is_home() || ( is_front_page() && 'page' === get_option( 'show_on_front' ) ) ? ' aria-current="page"' : '';
		$link_target  = ! empty( $attributes['linkTarget'] ) ? $attributes['linkTarget'] : '_self';

		$site_title = sprintf(
			'<a href="%1$s" target="%2$s" rel="home"%3$s>%4$s</a>',
			esc_url( home_url() ),
			esc_attr( $link_target ),
			$aria_current,
			esc_html( $site_title )
		);
	}
	$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => $align_class_name ) );

	return sprintf(
		'<%1$s %2$s>%3$s</%1$s>',
		$tag_name,
		$wrapper_attributes,
		// already pre-escaped if it is a link.
		$attributes['isLink'] ? $site_title : esc_html( $site_title )
	);
}

/**
 * Registers the `core/site-title` block on the server.
 */
function register_block_core_site_title() {
	register_block_type_from_metadata(
		__DIR__ . '/site-title',
		array(
			'render_callback' => 'render_block_core_site_title',
		)
	);
}
add_action( 'init', 'register_block_core_site_title' );
