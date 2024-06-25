<?php
/**
 * Server-side rendering of the `core/post-author-name` block.
 *
 * @package WordPress
 */

/**
 * Renders the `core/post-author-name` block on the server.
 *
 * @since 6.2.0
 *
 * @param  array    $attributes Block attributes.
 * @param  string   $content    Block default content.
 * @param  WP_Block $block      Block instance.
 * @return string Returns the rendered post author name block.
 */
function render_block_core_post_author_name( $attributes, $content, $block ) {
	if ( isset( $block->context['postId'] ) ) {
		$author_id = get_post_field( 'post_author', $block->context['postId'] );
	} else {
		$author_id = get_query_var( 'author' );
	}

	if ( empty( $author_id ) ) {
		return '';
	}

	$author_name = get_the_author_meta( 'display_name', $author_id );
	if ( isset( $attributes['isLink'] ) && $attributes['isLink'] ) {
		$author_name = sprintf( '<a href="%1$s" target="%2$s" class="wp-block-post-author-name__link">%3$s</a>', get_author_posts_url( $author_id ), esc_attr( $attributes['linkTarget'] ), $author_name );
	}

	$classes = array();
	if ( isset( $attributes['textAlign'] ) ) {
		$classes[] = 'has-text-align-' . $attributes['textAlign'];
	}
	if ( isset( $attributes['style']['elements']['link']['color']['text'] ) ) {
		$classes[] = 'has-link-color';
	}
	$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => implode( ' ', $classes ) ) );

	return sprintf( '<div %1$s>%2$s</div>', $wrapper_attributes, $author_name );
}

/**
 * Registers the `core/post-author-name` block on the server.
 *
 * @since 6.2.0
 */
function register_block_core_post_author_name() {
	register_block_type_from_metadata(
		__DIR__ . '/post-author-name',
		array(
			'render_callback' => 'render_block_core_post_author_name',
		)
	);
}
add_action( 'init', 'register_block_core_post_author_name' );
