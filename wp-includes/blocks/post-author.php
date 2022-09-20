<?php
/**
 * Server-side rendering of the `core/post-author` block.
 *
 * @package WordPress
 */

/**
 * Renders the `core/post-author` block on the server.
 *
 * @param  array    $attributes Block attributes.
 * @param  string   $content    Block default content.
 * @param  WP_Block $block      Block instance.
 * @return string Returns the rendered author block.
 */
function render_block_core_post_author( $attributes, $content, $block ) {
	if ( ! isset( $block->context['postId'] ) ) {
		$author_id = get_query_var( 'author' );
	} else {
		$author_id = get_post_field( 'post_author', $block->context['postId'] );
	}

	if ( empty( $author_id ) ) {
		return '';
	}

	$avatar = ! empty( $attributes['avatarSize'] ) ? get_avatar(
		$author_id,
		$attributes['avatarSize']
	) : null;

	$byline  = ! empty( $attributes['byline'] ) ? $attributes['byline'] : false;
	$classes = array_merge(
		isset( $attributes['itemsJustification'] ) ? array( 'items-justified-' . $attributes['itemsJustification'] ) : array(),
		isset( $attributes['textAlign'] ) ? array( 'has-text-align-' . $attributes['textAlign'] ) : array()
	);

	$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => implode( ' ', $classes ) ) );

	return sprintf( '<div %1$s>', $wrapper_attributes ) .
	( ! empty( $attributes['showAvatar'] ) ? '<div class="wp-block-post-author__avatar">' . $avatar . '</div>' : '' ) .
	'<div class="wp-block-post-author__content">' .
	( ! empty( $byline ) ? '<p class="wp-block-post-author__byline">' . wp_kses_post( $byline ) . '</p>' : '' ) .
	'<p class="wp-block-post-author__name">' . get_the_author_meta( 'display_name', $author_id ) . '</p>' .
	( ! empty( $attributes['showBio'] ) ? '<p class="wp-block-post-author__bio">' . get_the_author_meta( 'user_description', $author_id ) . '</p>' : '' ) .
	'</div>' .
	'</div>';
}

/**
 * Registers the `core/post-author` block on the server.
 */
function register_block_core_post_author() {
	register_block_type_from_metadata(
		__DIR__ . '/post-author',
		array(
			'render_callback' => 'render_block_core_post_author',
		)
	);
}
add_action( 'init', 'register_block_core_post_author' );
