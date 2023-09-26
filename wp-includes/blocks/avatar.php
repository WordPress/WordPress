<?php
/**
 * Server-side rendering of the `core/avatar` block.
 *
 * @package WordPress
 */

/**
 * Renders the `core/avatar` block on the server.
 *
 * @param array    $attributes Block attributes.
 * @param string   $content    Block default content.
 * @param WP_Block $block      Block instance.
 * @return string Return the avatar.
 */
function render_block_core_avatar( $attributes, $content, $block ) {
	$size               = isset( $attributes['size'] ) ? $attributes['size'] : 96;
	$wrapper_attributes = get_block_wrapper_attributes();
	$border_attributes  = get_block_core_avatar_border_attributes( $attributes );

	// Class gets passed through `esc_attr` via `get_avatar`.
	$image_classes = ! empty( $border_attributes['class'] )
		? "wp-block-avatar__image {$border_attributes['class']}"
		: 'wp-block-avatar__image';

	// Unlike class, `get_avatar` doesn't filter the styles via `esc_attr`.
	// The style engine does pass the border styles through
	// `safecss_filter_attr` however.
	$image_styles = ! empty( $border_attributes['style'] )
		? sprintf( ' style="%s"', esc_attr( $border_attributes['style'] ) )
		: '';

	if ( ! isset( $block->context['commentId'] ) ) {
		$author_id   = isset( $attributes['userId'] ) ? $attributes['userId'] : get_post_field( 'post_author', $block->context['postId'] );
		$author_name = get_the_author_meta( 'display_name', $author_id );
		// translators: %s is the Author name.
		$alt          = sprintf( __( '%s Avatar' ), $author_name );
		$avatar_block = get_avatar(
			$author_id,
			$size,
			'',
			$alt,
			array(
				'extra_attr' => $image_styles,
				'class'      => $image_classes,
			)
		);
		if ( isset( $attributes['isLink'] ) && $attributes['isLink'] ) {
			$label = '';
			if ( '_blank' === $attributes['linkTarget'] ) {
				// translators: %s is the Author name.
				$label = 'aria-label="' . sprintf( esc_attr__( '(%s author archive, opens in a new tab)' ), $author_name ) . '"';
			}
			// translators: %1$s: Author archive link. %2$s: Link target. %3$s Aria label. %4$s Avatar image.
			$avatar_block = sprintf( '<a href="%1$s" target="%2$s" %3$s class="wp-block-avatar__link">%4$s</a>', esc_url( get_author_posts_url( $author_id ) ), esc_attr( $attributes['linkTarget'] ), $label, $avatar_block );
		}
		return sprintf( '<div %1s>%2s</div>', $wrapper_attributes, $avatar_block );
	}
	$comment = get_comment( $block->context['commentId'] );
	if ( ! $comment ) {
		return '';
	}
	/* translators: %s is the Comment Author name */
	$alt          = sprintf( __( '%s Avatar' ), $comment->comment_author );
	$avatar_block = get_avatar(
		$comment,
		$size,
		'',
		$alt,
		array(
			'extra_attr' => $image_styles,
			'class'      => $image_classes,
		)
	);
	if ( isset( $attributes['isLink'] ) && $attributes['isLink'] && isset( $comment->comment_author_url ) && '' !== $comment->comment_author_url ) {
		$label = '';
		if ( '_blank' === $attributes['linkTarget'] ) {
			// translators: %s is the Comment Author name.
			$label = 'aria-label="' . sprintf( esc_attr__( '(%s website link, opens in a new tab)' ), $comment->comment_author ) . '"';
		}
		// translators: %1$s: Comment Author website link. %2$s: Link target. %3$s Aria label. %4$s Avatar image.
		$avatar_block = sprintf( '<a href="%1$s" target="%2$s" %3$s class="wp-block-avatar__link">%4$s</a>', esc_url( $comment->comment_author_url ), esc_attr( $attributes['linkTarget'] ), $label, $avatar_block );
	}
	return sprintf( '<div %1s>%2s</div>', $wrapper_attributes, $avatar_block );
}

/**
 * Generates class names and styles to apply the border support styles for
 * the Avatar block.
 *
 * @param array $attributes The block attributes.
 * @return array The border-related classnames and styles for the block.
 */
function get_block_core_avatar_border_attributes( $attributes ) {
	$border_styles = array();
	$sides         = array( 'top', 'right', 'bottom', 'left' );

	// Border radius.
	if ( isset( $attributes['style']['border']['radius'] ) ) {
		$border_styles['radius'] = $attributes['style']['border']['radius'];
	}

	// Border style.
	if ( isset( $attributes['style']['border']['style'] ) ) {
		$border_styles['style'] = $attributes['style']['border']['style'];
	}

	// Border width.
	if ( isset( $attributes['style']['border']['width'] ) ) {
		$border_styles['width'] = $attributes['style']['border']['width'];
	}

	// Border color.
	$preset_color           = array_key_exists( 'borderColor', $attributes ) ? "var:preset|color|{$attributes['borderColor']}" : null;
	$custom_color           = $attributes['style']['border']['color'] ?? null;
	$border_styles['color'] = $preset_color ? $preset_color : $custom_color;

	// Individual border styles e.g. top, left etc.
	foreach ( $sides as $side ) {
		$border                 = $attributes['style']['border'][ $side ] ?? null;
		$border_styles[ $side ] = array(
			'color' => isset( $border['color'] ) ? $border['color'] : null,
			'style' => isset( $border['style'] ) ? $border['style'] : null,
			'width' => isset( $border['width'] ) ? $border['width'] : null,
		);
	}

	$styles     = wp_style_engine_get_styles( array( 'border' => $border_styles ) );
	$attributes = array();
	if ( ! empty( $styles['classnames'] ) ) {
		$attributes['class'] = $styles['classnames'];
	}
	if ( ! empty( $styles['css'] ) ) {
		$attributes['style'] = $styles['css'];
	}
	return $attributes;
}

/**
 * Registers the `core/avatar` block on the server.
 */
function register_block_core_avatar() {
	register_block_type_from_metadata(
		__DIR__ . '/avatar',
		array(
			'render_callback' => 'render_block_core_avatar',
		)
	);
}
add_action( 'init', 'register_block_core_avatar' );
