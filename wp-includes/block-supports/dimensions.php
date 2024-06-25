<?php
/**
 * Dimensions block support flag.
 *
 * This does not include the `spacing` block support even though that visually
 * appears under the "Dimensions" panel in the editor. It remains in its
 * original `spacing.php` file for compatibility with core.
 *
 * @package WordPress
 * @since 5.9.0
 */

/**
 * Registers the style block attribute for block types that support it.
 *
 * @since 5.9.0
 * @access private
 *
 * @param WP_Block_Type $block_type Block Type.
 */
function wp_register_dimensions_support( $block_type ) {
	// Setup attributes and styles within that if needed.
	if ( ! $block_type->attributes ) {
		$block_type->attributes = array();
	}

	// Check for existing style attribute definition e.g. from block.json.
	if ( array_key_exists( 'style', $block_type->attributes ) ) {
		return;
	}

	$has_dimensions_support = block_has_support( $block_type, 'dimensions', false );

	if ( $has_dimensions_support ) {
		$block_type->attributes['style'] = array(
			'type' => 'object',
		);
	}
}

/**
 * Adds CSS classes for block dimensions to the incoming attributes array.
 * This will be applied to the block markup in the front-end.
 *
 * @since 5.9.0
 * @since 6.2.0 Added `minHeight` support.
 * @access private
 *
 * @param WP_Block_Type $block_type       Block Type.
 * @param array         $block_attributes Block attributes.
 * @return array Block dimensions CSS classes and inline styles.
 */
function wp_apply_dimensions_support( $block_type, $block_attributes ) {
	if ( wp_should_skip_block_supports_serialization( $block_type, 'dimensions' ) ) {
		return array();
	}

	$attributes = array();

	// Width support to be added in near future.

	$has_min_height_support = block_has_support( $block_type, array( 'dimensions', 'minHeight' ), false );
	$block_styles           = isset( $block_attributes['style'] ) ? $block_attributes['style'] : null;

	if ( ! $block_styles ) {
		return $attributes;
	}

	$skip_min_height                      = wp_should_skip_block_supports_serialization( $block_type, 'dimensions', 'minHeight' );
	$dimensions_block_styles              = array();
	$dimensions_block_styles['minHeight'] = null;
	if ( $has_min_height_support && ! $skip_min_height ) {
		$dimensions_block_styles['minHeight'] = isset( $block_styles['dimensions']['minHeight'] )
			? $block_styles['dimensions']['minHeight']
			: null;
	}
	$styles = wp_style_engine_get_styles( array( 'dimensions' => $dimensions_block_styles ) );

	if ( ! empty( $styles['css'] ) ) {
		$attributes['style'] = $styles['css'];
	}

	return $attributes;
}

/**
 * Renders server-side dimensions styles to the block wrapper.
 * This block support uses the `render_block` hook to ensure that
 * it is also applied to non-server-rendered blocks.
 *
 * @since 6.5.0
 * @access private
 *
 * @param  string $block_content Rendered block content.
 * @param  array  $block         Block object.
 * @return string                Filtered block content.
 */
function wp_render_dimensions_support( $block_content, $block ) {
	$block_type               = WP_Block_Type_Registry::get_instance()->get_registered( $block['blockName'] );
	$block_attributes         = ( isset( $block['attrs'] ) && is_array( $block['attrs'] ) ) ? $block['attrs'] : array();
	$has_aspect_ratio_support = block_has_support( $block_type, array( 'dimensions', 'aspectRatio' ), false );

	if (
		! $has_aspect_ratio_support ||
		wp_should_skip_block_supports_serialization( $block_type, 'dimensions', 'aspectRatio' )
	) {
		return $block_content;
	}

	$dimensions_block_styles                = array();
	$dimensions_block_styles['aspectRatio'] = $block_attributes['style']['dimensions']['aspectRatio'] ?? null;

	// To ensure the aspect ratio does not get overridden by `minHeight` unset any existing rule.
	if (
		isset( $dimensions_block_styles['aspectRatio'] )
	) {
		$dimensions_block_styles['minHeight'] = 'unset';
	} elseif (
		isset( $block_attributes['style']['dimensions']['minHeight'] ) ||
		isset( $block_attributes['minHeight'] )
	) {
		$dimensions_block_styles['aspectRatio'] = 'unset';
	}

	$styles = wp_style_engine_get_styles( array( 'dimensions' => $dimensions_block_styles ) );

	if ( ! empty( $styles['css'] ) ) {
		// Inject dimensions styles to the first element, presuming it's the wrapper, if it exists.
		$tags = new WP_HTML_Tag_Processor( $block_content );

		if ( $tags->next_tag() ) {
			$existing_style = $tags->get_attribute( 'style' );
			$updated_style  = '';

			if ( ! empty( $existing_style ) ) {
				$updated_style = $existing_style;
				if ( ! str_ends_with( $existing_style, ';' ) ) {
					$updated_style .= ';';
				}
			}

			$updated_style .= $styles['css'];
			$tags->set_attribute( 'style', $updated_style );

			if ( ! empty( $styles['classnames'] ) ) {
				foreach ( explode( ' ', $styles['classnames'] ) as $class_name ) {
					if (
						str_contains( $class_name, 'aspect-ratio' ) &&
						! isset( $block_attributes['style']['dimensions']['aspectRatio'] )
					) {
						continue;
					}
					$tags->add_class( $class_name );
				}
			}
		}

		return $tags->get_updated_html();
	}

	return $block_content;
}

add_filter( 'render_block', 'wp_render_dimensions_support', 10, 2 );

// Register the block support.
WP_Block_Supports::get_instance()->register(
	'dimensions',
	array(
		'register_attribute' => 'wp_register_dimensions_support',
		'apply'              => 'wp_apply_dimensions_support',
	)
);
