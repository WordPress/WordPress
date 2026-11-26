<?php
/**
 * Background block support flag.
 *
 * @package WordPress
 * @since 6.4.0
 */

/**
 * Registers the style block attribute for block types that support it.
 *
 * @since 6.4.0
 * @access private
 *
 * @param WP_Block_Type $block_type Block Type.
 */
function wp_register_background_support( $block_type ) {
	// Setup attributes and styles within that if needed.
	if ( ! $block_type->attributes ) {
		$block_type->attributes = array();
	}

	// Check for existing style attribute definition e.g. from block.json.
	if ( array_key_exists( 'style', $block_type->attributes ) ) {
		return;
	}

	$has_background_support = block_has_support( $block_type, array( 'background' ), false );

	if ( $has_background_support ) {
		$block_type->attributes['style'] = array(
			'type' => 'object',
		);
	}
}

/**
 * Returns background classes and inline styles for block attributes, like the JS
 * `getBackgroundClassesAndStyles()`. Does not check block support or skipped
 * serialization.
 *
 * @since 7.3.0
 *
 * @param array<string, mixed> $block_attributes Block attributes.
 * @return array<string, string> Array with `class` and `style` keys, each present only when non-empty.
 *
 * @phpstan-param array{ style?: array<string, mixed>, ...<string, mixed> } $block_attributes
 * @phpstan-return array{ class?: 'has-background', style?: non-empty-string }
 */
function wp_get_background_classes_and_styles( $block_attributes ) {
	if ( ! is_array( $block_attributes ) ) {
		return array();
	}

	$background = $block_attributes['style']['background'] ?? null;

	if ( ! is_array( $background ) ) {
		return array();
	}

	$background_styles = array(
		'backgroundImage'      => $background['backgroundImage'] ?? null,
		'backgroundSize'       => $background['backgroundSize'] ?? null,
		'backgroundPosition'   => $background['backgroundPosition'] ?? null,
		'backgroundRepeat'     => $background['backgroundRepeat'] ?? null,
		'backgroundAttachment' => $background['backgroundAttachment'] ?? null,
		'gradient'             => $background['gradient'] ?? null,
		'backgroundClip'       => $background['backgroundClip'] ?? null,
	);

	if ( ! empty( $background_styles['backgroundImage'] ) ) {
		$background_styles['backgroundSize'] ??= 'cover';
		if ( 'contain' === $background_styles['backgroundSize'] && ! $background_styles['backgroundPosition'] ) {
			$background_styles['backgroundPosition'] = '50% 50%';
		}
	}

	$styles = wp_style_engine_get_styles( array( 'background' => $background_styles ) );

	if ( empty( $styles['css'] ) ) {
		return array();
	}

	$attributes = array();

	$has_background = ! empty( $background_styles['backgroundImage'] ) || ! empty( $background_styles['gradient'] );

	// A text clip paints the glyphs, not the box, so there is no block background.
	if ( $has_background && 'text' !== $background_styles['backgroundClip'] ) {
		$attributes['class'] = 'has-background';
	}

	$attributes['style'] = $styles['css'];

	return $attributes;
}

/**
 * Renders the background styles to the block wrapper.
 * This block support uses the `render_block` hook to ensure that
 * it is also applied to non-server-rendered blocks.
 *
 * @since 6.4.0
 * @since 6.5.0 Added support for `backgroundPosition` and `backgroundRepeat` output.
 * @since 6.6.0 Removed requirement for `backgroundImage.source`. A file/url is the default.
 * @since 6.7.0 Added support for `backgroundAttachment` output.
 * @since 7.1.0 Added support for `background.gradient` output.
 * @since 7.2.0 Added support for `background.backgroundClip` output.
 *
 * @access private
 *
 * @param string $block_content Rendered block content.
 * @param array  $block         Block object.
 * @return string Filtered block content.
 *
 * @phpstan-param array{
 *     blockName: string,
 *     attrs: array{
 *         style?: array{
 *             background?: array<string, mixed>,
 *             ...<string, mixed>
 *         },
 *         ...<string, mixed>
 *     },
 *     ...
 * } $block
 */
function wp_render_background_support( $block_content, $block ) {
	$block_type = WP_Block_Type_Registry::get_instance()->get_registered( $block['blockName'] );
	if ( ! $block_type ) {
		return $block_content;
	}

	$block_attributes                = ( isset( $block['attrs'] ) && is_array( $block['attrs'] ) ) ? $block['attrs'] : array();
	$has_background_image_support    = block_has_support( $block_type, array( 'background', 'backgroundImage' ), false );
	$has_background_gradient_support = block_has_support( $block_type, array( 'background', 'gradient' ), false );
	$has_background_clip_support     = block_has_support( $block_type, array( 'background', 'backgroundClip' ), false );

	if (
		( ! $has_background_image_support && ! $has_background_gradient_support && ! $has_background_clip_support ) ||
		! isset( $block_attributes['style']['background'] )
	) {
		return $block_content;
	}

	// Check serialization skip for each feature individually.
	$skip_background_image    = ! $has_background_image_support || wp_should_skip_block_supports_serialization( $block_type, 'background', 'backgroundImage' );
	$skip_background_gradient = ! $has_background_gradient_support || wp_should_skip_block_supports_serialization( $block_type, 'background', 'gradient' );
	$skip_background_clip     = ! $has_background_clip_support || wp_should_skip_block_supports_serialization( $block_type, 'background', 'backgroundClip' );

	if ( $skip_background_image && $skip_background_gradient && $skip_background_clip ) {
		return $block_content;
	}

	// unset() fatals on a string, and the helper ignores one anyway.
	if ( is_array( $block_attributes['style']['background'] ) ) {
		if ( $skip_background_image ) {
			unset(
				$block_attributes['style']['background']['backgroundImage'],
				$block_attributes['style']['background']['backgroundSize'],
				$block_attributes['style']['background']['backgroundPosition'],
				$block_attributes['style']['background']['backgroundRepeat'],
				$block_attributes['style']['background']['backgroundAttachment']
			);
		}

		if ( $skip_background_gradient ) {
			unset( $block_attributes['style']['background']['gradient'] );
		}

		// Serialized values only, so a skipped clip cannot drop `has-background`.
		if ( $skip_background_clip ) {
			unset( $block_attributes['style']['background']['backgroundClip'] );
		}
	}

	$styles = wp_get_background_classes_and_styles( $block_attributes );

	if ( ! empty( $styles['style'] ) ) {
		// Inject background styles to the first element, presuming it's the wrapper, if it exists.
		$tags = new WP_HTML_Tag_Processor( $block_content );

		if ( $tags->next_tag() ) {
			$existing_style = $tags->get_attribute( 'style' );
			if ( is_string( $existing_style ) && '' !== $existing_style ) {
				$separator     = str_ends_with( $existing_style, ';' ) ? '' : ';';
				$updated_style = "{$existing_style}{$separator}{$styles['style']}";
			} else {
				$updated_style = $styles['style'];
			}

			$tags->set_attribute( 'style', $updated_style );

			if ( ! empty( $styles['class'] ) ) {
				$tags->add_class( $styles['class'] );
			}
		}

		return $tags->get_updated_html();
	}

	return $block_content;
}

// Register the block support.
WP_Block_Supports::get_instance()->register(
	'background',
	array(
		'register_attribute' => 'wp_register_background_support',
	)
);

add_filter( 'render_block', 'wp_render_background_support', 10, 2 );
