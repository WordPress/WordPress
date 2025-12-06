<?php
/**
 * Server-side rendering of the `core/comment-author-avatar` block.
 *
 * @package WordPress
 */

/**
 * Renders the `core/comment-author-avatar` block on the server.
 *
 * @param array    $attributes Block attributes.
 * @param string   $content    Block default content.
 * @param WP_Block $block      Block instance.
 * @return string Return the post comment's avatar.
 */
function gutenberg_render_block_core_comment_author_avatar( $attributes, $content, $block ) {
	if ( ! isset( $block->context['commentId'] ) ) {
		return '';
	}

	$comment = get_comment( $block->context['commentId'] );
	if ( ! $comment ) {
		return '';
	}

	// This is the only way to retrieve style and classes on different instances.
	$wrapper_attributes = WP_Block_Supports::get_instance()->apply_block_supports();

	/**
	 * We get the spacing attributes and transform the array provided into a string formatted for being applied as a style html tag.
	 * Good candidate to be moved to a separate function in core.
	*/
	$spacing_attributes = isset( $attributes['style']['spacing'] ) ? $attributes['style']['spacing'] : null;
	if ( isset( $spacing_attributes ) && ! empty( $spacing_attributes ) ) {
		$spacing_array = array();
		foreach ( $spacing_attributes as $spacing_attribute_key => $spacing_attribute_value ) {
			foreach ( $spacing_attribute_value as $position_key => $position_value ) {
				$spacing_array[] = $spacing_attribute_key . '-' . $position_key . ': ' . $position_value;
			}
		}
		$spacing_string = implode( ';', $spacing_array );
	}

	$width   = isset( $attributes['width'] ) ? $attributes['width'] : 96;
	$height  = isset( $attributes['height'] ) ? $attributes['height'] : 96;
	$styles  = isset( $wrapper_attributes['style'] ) ? $wrapper_attributes['style'] : '';
	$classes = isset( $wrapper_attributes['class'] ) ? $wrapper_attributes['class'] : '';

	/* translators: %s: Author name. */
	$alt = sprintf( __( '%s Avatar' ), $comment->comment_author );

	$avatar_block = get_avatar(
		$comment,
		null,
		'',
		$alt,
		array(
			'height'     => $height,
			'width'      => $width,
			'extra_attr' => sprintf( 'style="%1s"', $styles ),
			'class'      => $classes,
		)
	);
	if ( isset( $spacing_attributes ) ) {
		return sprintf( '<div style="%1s">%2s</div>', esc_attr( $spacing_string ), $avatar_block );
	}
	return sprintf( '<div>%1s</div>', $avatar_block );
}

/**
 * Registers the `core/comment-author-avatar` block on the server.
 */
function gutenberg_register_block_core_comment_author_avatar() {
	register_block_type_from_metadata(
		__DIR__ . '/comment-author-avatar',
		array(
			'render_callback' => 'gutenberg_render_block_core_comment_author_avatar',
		)
	);
}
add_action( 'init', 'gutenberg_register_block_core_comment_author_avatar', 20 );
