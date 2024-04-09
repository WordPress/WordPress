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

	$image_styles = array();

	// Add border width styles.
	$has_border_width = ! empty( $attributes['style']['border']['width'] );

	if ( $has_border_width ) {
		$border_width   = $attributes['style']['border']['width'];
		$image_styles[] = sprintf( 'border-width: %s;', esc_attr( $border_width ) );
	}

	// Add border radius styles.
	$has_border_radius = ! empty( $attributes['style']['border']['radius'] );

	if ( $has_border_radius ) {
		$border_radius = $attributes['style']['border']['radius'];

		if ( is_array( $border_radius ) ) {
			// Apply styles for individual corner border radii.
			foreach ( $border_radius as $key => $value ) {
				if ( null !== $value ) {
					$name = _wp_to_kebab_case( $key );
					// Add shared styles for individual border radii.
					$border_style   = sprintf(
						'border-%s-radius: %s;',
						esc_attr( $name ),
						esc_attr( $value )
					);
					$image_styles[] = $border_style;
				}
			}
		} else {
			$border_style   = sprintf( 'border-radius: %s;', esc_attr( $border_radius ) );
			$image_styles[] = $border_style;
		}
	}

	// Add border color styles.
	$has_border_color = ! empty( $attributes['style']['border']['color'] );

	if ( $has_border_color ) {
		$border_color   = $attributes['style']['border']['color'];
		$image_styles[] = sprintf( 'border-color: %s;', esc_attr( $border_color ) );
	}

	// Add border style (solid, dashed, dotted ).
	$has_border_style = ! empty( $attributes['style']['border']['style'] );

	if ( $has_border_style ) {
		$border_style   = $attributes['style']['border']['style'];
		$image_styles[] = sprintf( 'border-style: %s;', esc_attr( $border_style ) );
	}

	// Add border classes to the avatar image for both custom colors and palette colors.
	$image_classes = '';
	if ( $has_border_color || isset( $attributes['borderColor'] ) ) {
		$image_classes .= 'has-border-color';
	}
	if ( isset( $attributes['borderColor'] ) ) {
		$image_classes .= ' has-' . $attributes['borderColor'] . '-border-color';
	}

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
				'extra_attr' => isset( $image_styles ) ? sprintf( ' style="%s"', safecss_filter_attr( implode( ' ', $image_styles ) ) ) : '',
				'class'      => "wp-block-avatar__image $image_classes ",
			)
		);
		if ( isset( $attributes['isLink'] ) && $attributes['isLink'] ) {
			$label = '';
			if ( '_blank' === $attributes['linkTarget'] ) {
				// translators: %s is the Author name.
				$label = 'aria-label="' . esc_attr( sprintf( __( '(%s author archive, opens in a new tab)' ), $author_name ) ) . '"';
			}
			// translators: %1$s: Author archive link. %2$s: Link target. %3$s Aria label. %4$s Avatar image.
			$avatar_block = sprintf( '<a href="%1$s" target="%2$s" %3$s class="wp-block-avatar__link">%4$s</a>', get_author_posts_url( $author_id ), esc_attr( $attributes['linkTarget'] ), $label, $avatar_block );
		}
		return sprintf( '<div %1s>%2s</div>', $wrapper_attributes, $avatar_block );
	}
	$comment = get_comment( $block->context['commentId'] );
	/* translators: %s is the Comment Author name */
	$alt = sprintf( __( '%s Avatar' ), $comment->comment_author );
	if ( ! $comment ) {
		return '';
	}
	$avatar_block = get_avatar(
		$comment,
		$size,
		'',
		$alt,
		array(
			'extra_attr' => isset( $image_styles ) ? sprintf( ' style="%s"', safecss_filter_attr( implode( ' ', $image_styles ) ) ) : '',
			'class'      => "wp-block-avatar__image $image_classes",
		)
	);
	if ( isset( $attributes['isLink'] ) && $attributes['isLink'] && isset( $comment->comment_author_url ) && '' !== $comment->comment_author_url ) {
		$label = '';
		if ( '_blank' === $attributes['linkTarget'] ) {
			// translators: %s is the Comment Author name.
			$label = 'aria-label="' . esc_attr( sprintf( __( '(%s website link, opens in a new tab)' ), $comment->comment_author ) ) . '"';
		}
		// translators: %1$s: Comment Author website link. %2$s: Link target. %3$s Aria label. %4$s Avatar image.
		$avatar_block = sprintf( '<a href="%1$s" target="%2$s" %3$s class="wp-block-avatar__link">%4$s</a>', $comment->comment_author_url, esc_attr( $attributes['linkTarget'] ), $label, $avatar_block );
	}
	return sprintf( '<div %1s>%2s</div>', $wrapper_attributes, $avatar_block );
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
