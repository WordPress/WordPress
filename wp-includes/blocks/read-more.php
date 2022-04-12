<?php
/**
 * Server-side rendering of the `core/read-more` block.
 *
 * @package WordPress
 */

/**
 * Renders the `core/read-more` block on the server.
 *
 * @param array    $attributes Block attributes.
 * @param string   $content    Block default content.
 * @param WP_Block $block      Block instance.
 * @return string  Returns the post link.
 */
function render_block_core_read_more( $attributes, $content, $block ) {
	if ( ! isset( $block->context['postId'] ) ) {
		return '';
	}

	$post_ID            = $block->context['postId'];
	$justify_class_name = empty( $attributes['justifyContent'] ) ? '' : "is-justified-{$attributes['justifyContent']}";
	$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => $justify_class_name ) );
	$more_text          = ! empty( $attributes['content'] ) ? wp_kses_post( $attributes['content'] ) : __( 'Read more' );
	return sprintf(
		'<a %1s href="%2s" target="%3s">%4s</a>',
		$wrapper_attributes,
		get_the_permalink( $post_ID ),
		esc_attr( $attributes['linkTarget'] ),
		$more_text
	);
}

/**
 * Registers the `core/read-more` block on the server.
 */
function register_block_core_read_more() {
	register_block_type_from_metadata(
		__DIR__ . '/read-more',
		array(
			'render_callback' => 'render_block_core_read_more',
		)
	);
}
add_action( 'init', 'register_block_core_read_more' );
