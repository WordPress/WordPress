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
 * Renders the background styles to the block wrapper.
 * This block support uses the `render_block` hook to ensure that
 * it is also applied to non-server-rendered blocks.
 *
 * @since 6.4.0
 * @access private
 *
 * @param  string $block_content Rendered block content.
 * @param  array  $block         Block object.
 * @return string Filtered block content.
 */
function wp_render_background_support( $block_content, $block ) {
	$block_type                   = WP_Block_Type_Registry::get_instance()->get_registered( $block['blockName'] );
	$block_attributes             = ( isset( $block['attrs'] ) && is_array( $block['attrs'] ) ) ? $block['attrs'] : array();
	$has_background_image_support = block_has_support( $block_type, array( 'background', 'backgroundImage' ), false );

	if (
		! $has_background_image_support ||
		wp_should_skip_block_supports_serialization( $block_type, 'background', 'backgroundImage' )
	) {
		return $block_content;
	}

	$background_image_source = isset( $block_attributes['style']['background']['backgroundImage']['source'] )
		? $block_attributes['style']['background']['backgroundImage']['source']
		: null;
	$background_image_url    = isset( $block_attributes['style']['background']['backgroundImage']['url'] )
		? $block_attributes['style']['background']['backgroundImage']['url']
		: null;
	$background_size         = isset( $block_attributes['style']['background']['backgroundSize'] )
		? $block_attributes['style']['background']['backgroundSize']
		: 'cover';

	$background_block_styles = array();

	if (
		'file' === $background_image_source &&
		$background_image_url
	) {
		// Set file based background URL.
		$background_block_styles['backgroundImage']['url'] = $background_image_url;
		// Only output the background size when an image url is set.
		$background_block_styles['backgroundSize'] = $background_size;
	}

	$styles = wp_style_engine_get_styles( array( 'background' => $background_block_styles ) );

	if ( ! empty( $styles['css'] ) ) {
		// Inject background styles to the first element, presuming it's the wrapper, if it exists.
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
