<?php
/**
 * Server-side rendering of the `core/post-title` block.
 *
 * @package WordPress
 */

/**
 * Renders the `core/post-title` block on the server.
 *
 * @since 6.3.0 Omitting the $post argument from the `get_the_title`.
 *
 * @param array    $attributes Block attributes.
 * @param string   $content    Block default content.
 * @param WP_Block $block      Block instance.
 *
 * @return string Returns the filtered post title for the current post wrapped inside "h1" tags.
 */
function render_block_core_post_title( $attributes, $content, $block ) {
	if ( ! isset( $block->context['postId'] ) ) {
		return '';
	}

	/**
	 * The `$post` argument is intentionally omitted so that changes are reflected when previewing a post.
	 * See: https://github.com/WordPress/gutenberg/pull/37622#issuecomment-1000932816.
	 */
	$title = get_the_title();

	if ( ! $title ) {
		return '';
	}

	$tag_name = 'h2';
	if ( isset( $attributes['level'] ) ) {
		$tag_name = 0 === $attributes['level'] ? 'p' : 'h' . (int) $attributes['level'];
	}

	if ( isset( $attributes['isLink'] ) && $attributes['isLink'] ) {
		$rel   = ! empty( $attributes['rel'] ) ? 'rel="' . esc_attr( $attributes['rel'] ) . '"' : '';
		$title = sprintf( '<a href="%1$s" target="%2$s" %3$s>%4$s</a>', esc_url( get_the_permalink( $block->context['postId'] ) ), esc_attr( $attributes['linkTarget'] ), $rel, $title );
	}

	$classes = array();
	if ( isset( $attributes['textAlign'] ) ) {
		$classes[] = 'has-text-align-' . $attributes['textAlign'];
	}
	if ( isset( $attributes['style']['elements']['link']['color']['text'] ) ) {
		$classes[] = 'has-link-color';
	}
	$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => implode( ' ', $classes ) ) );

	return sprintf(
		'<%1$s %2$s>%3$s</%1$s>',
		$tag_name,
		$wrapper_attributes,
		$title
	);
}

/**
 * Registers the `core/post-title` block on the server.
 *
 * @since 5.8.0
 */
function register_block_core_post_title() {
	register_block_type_from_metadata(
		__DIR__ . '/post-title',
		array(
			'render_callback' => 'render_block_core_post_title',
		)
	);
}
add_action( 'init', 'register_block_core_post_title' );
