<?php
/**
 * Elements styles block support.
 *
 * @package gutenberg
 */

/**
 * Update the block content with elements class names.
 *
 * @deprecated 6.6.0 Use `gutenberg_render_elements_class_name` instead.
 *
 * @param  string $block_content Rendered block content.
 * @return string                Filtered block content.
 */
function gutenberg_render_elements_support( $block_content ) {
	_deprecated_function( __FUNCTION__, '6.6.0', 'gutenberg_render_elements_class_name' );
	return $block_content;
}

/**
 * Determines whether an elements class name should be added to the block.
 *
 * @param  array $block   Block object.
 * @param  array $options Per element type options e.g. whether to skip serialization.
 *
 * @return boolean        Whether the block needs an elements class name.
 */
function gutenberg_should_add_elements_class_name( $block, $options ) {
	if ( ! isset( $block['attrs']['style']['elements'] ) ) {
		return false;
	}

	$element_color_properties = array(
		'button'  => array(
			'skip'  => $options['button']['skip'] ?? false,
			'paths' => array(
				array( 'button', 'color', 'text' ),
				array( 'button', 'color', 'background' ),
				array( 'button', 'color', 'gradient' ),
			),
		),
		'link'    => array(
			'skip'  => $options['link']['skip'] ?? false,
			'paths' => array(
				array( 'link', 'color', 'text' ),
				array( 'link', ':hover', 'color', 'text' ),
			),
		),
		'heading' => array(
			'skip'  => $options['heading']['skip'] ?? false,
			'paths' => array(
				array( 'heading', 'color', 'text' ),
				array( 'heading', 'color', 'background' ),
				array( 'heading', 'color', 'gradient' ),
				array( 'h1', 'color', 'text' ),
				array( 'h1', 'color', 'background' ),
				array( 'h1', 'color', 'gradient' ),
				array( 'h2', 'color', 'text' ),
				array( 'h2', 'color', 'background' ),
				array( 'h2', 'color', 'gradient' ),
				array( 'h3', 'color', 'text' ),
				array( 'h3', 'color', 'background' ),
				array( 'h3', 'color', 'gradient' ),
				array( 'h4', 'color', 'text' ),
				array( 'h4', 'color', 'background' ),
				array( 'h4', 'color', 'gradient' ),
				array( 'h5', 'color', 'text' ),
				array( 'h5', 'color', 'background' ),
				array( 'h5', 'color', 'gradient' ),
				array( 'h6', 'color', 'text' ),
				array( 'h6', 'color', 'background' ),
				array( 'h6', 'color', 'gradient' ),
			),
		),
	);

	$elements_style_attributes = $block['attrs']['style']['elements'];

	foreach ( $element_color_properties as $element_config ) {
		if ( $element_config['skip'] ) {
			continue;
		}

		foreach ( $element_config['paths'] as $path ) {
			if ( null !== _wp_array_get( $elements_style_attributes, $path, null ) ) {
				return true;
			}
		}
	}

	return false;
}

/**
 * Render the elements stylesheet and adds elements class name to block as required.
 *
 * In the case of nested blocks we want the parent element styles to be rendered before their descendants.
 * This solves the issue of an element (e.g.: link color) being styled in both the parent and a descendant:
 * we want the descendant style to take priority, and this is done by loading it after, in DOM order.
 *
 * @since 6.6.0 Element block support class and styles are generated via the `render_block_data` filter instead of `pre_render_block`
 *
 * @param array $parsed_block The parsed block.
 *
 * @return array The same parsed block with elements classname added if appropriate.
 */
function gutenberg_render_elements_support_styles( $parsed_block ) {
	/*
	 * The generation of element styles and classname were moved to the
	 * `render_block_data` filter in 6.6.0 to avoid filtered attributes
	 * breaking the application of the elements CSS class.
	 *
	 * @see https://github.com/WordPress/gutenberg/pull/59535.
	 *
	 * The change in filter means, the argument types for this function
	 * have changed and require deprecating.
	 */
	if ( is_string( $parsed_block ) ) {
		_deprecated_argument(
			__FUNCTION__,
			'6.6.0',
			__( 'Use as a `pre_render_block` filter is deprecated. Use with `render_block_data` instead.', 'gutenberg' )
		);
	}

	$block_type           = WP_Block_Type_Registry::get_instance()->get_registered( $parsed_block['blockName'] );
	$element_block_styles = $parsed_block['attrs']['style']['elements'] ?? null;

	if ( ! $element_block_styles ) {
		return $parsed_block;
	}

	$skip_link_color_serialization         = wp_should_skip_block_supports_serialization( $block_type, 'color', 'link' );
	$skip_heading_color_serialization      = wp_should_skip_block_supports_serialization( $block_type, 'color', 'heading' );
	$skip_button_color_serialization       = wp_should_skip_block_supports_serialization( $block_type, 'color', 'button' );
	$skips_all_element_color_serialization = $skip_link_color_serialization &&
		$skip_heading_color_serialization &&
		$skip_button_color_serialization;

	if ( $skips_all_element_color_serialization ) {
		return $parsed_block;
	}

	$options = array(
		'button'  => array( 'skip' => $skip_button_color_serialization ),
		'link'    => array( 'skip' => $skip_link_color_serialization ),
		'heading' => array( 'skip' => $skip_heading_color_serialization ),
	);

	if ( ! gutenberg_should_add_elements_class_name( $parsed_block, $options ) ) {
		return $parsed_block;
	}

	$class_name         = wp_get_elements_class_name( $parsed_block );
	$updated_class_name = isset( $parsed_block['attrs']['className'] ) ? $parsed_block['attrs']['className'] . " $class_name" : $class_name;

	_wp_array_set( $parsed_block, array( 'attrs', 'className' ), $updated_class_name );

	// Generate element styles based on selector and store in style engine for enqueuing.
	$element_types = array(
		'button'  => array(
			'selector' => ".$class_name .wp-element-button, .$class_name .wp-block-button__link",
			'skip'     => $skip_button_color_serialization,
		),
		'link'    => array(
			// :where(:not) matches theme.json selector.
			'selector'       => ".$class_name a:where(:not(.wp-element-button))",
			'hover_selector' => ".$class_name a:where(:not(.wp-element-button)):hover",
			'skip'           => $skip_link_color_serialization,
		),
		'heading' => array(
			'selector' => ".$class_name h1, .$class_name h2, .$class_name h3, .$class_name h4, .$class_name h5, .$class_name h6",
			'skip'     => $skip_heading_color_serialization,
			'elements' => array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6' ),
		),
	);

	foreach ( $element_types as $element_type => $element_config ) {
		if ( $element_config['skip'] ) {
			continue;
		}

		$element_style_object = _wp_array_get( $element_block_styles, array( $element_type ), null );

		// Process primary element type styles.
		if ( $element_style_object ) {
			gutenberg_style_engine_get_styles(
				$element_style_object,
				array(
					'selector' => $element_config['selector'],
					'context'  => 'block-supports',
				)
			);

			if ( isset( $element_style_object[':hover'] ) ) {
				gutenberg_style_engine_get_styles(
					$element_style_object[':hover'],
					array(
						'selector' => $element_config['hover_selector'],
						'context'  => 'block-supports',
					)
				);
			}
		}

		// Process related elements e.g. h1-h6 for headings.
		if ( isset( $element_config['elements'] ) ) {
			foreach ( $element_config['elements'] as $element ) {
				$element_style_object = _wp_array_get( $element_block_styles, array( $element ), null );

				if ( $element_style_object ) {
					gutenberg_style_engine_get_styles(
						$element_style_object,
						array(
							'selector' => ".$class_name $element",
							'context'  => 'block-supports',
						)
					);
				}
			}
		}
	}

	return $parsed_block;
}

/**
 * Ensure the elements block support class name generated, and added to
 * block attributes, in the `render_block_data` filter gets applied to the
 * block's markup.
 *
 * @see gutenberg_render_elements_support_styles
 *
 * @param  string $block_content Rendered block content.
 * @param  array  $block         Block object.
 *
 * @return string                Filtered block content.
 */
function gutenberg_render_elements_class_name( $block_content, $block ) {
	$class_string = $block['attrs']['className'] ?? '';
	preg_match( '/\bwp-elements-\S+\b/', $class_string, $matches );

	if ( empty( $matches ) ) {
		return $block_content;
	}

	$tags = new WP_HTML_Tag_Processor( $block_content );

	if ( $tags->next_tag() ) {
		$tags->add_class( $matches[0] );
	}

	return $tags->get_updated_html();
}

// Remove deprecated WordPress core filters.
remove_filter( 'render_block', 'wp_render_elements_support', 10 );
remove_filter( 'pre_render_block', 'wp_render_elements_support_styles', 10 );

// Remove WordPress core filters to avoid rendering duplicate elements stylesheet & attaching classes twice.
remove_filter( 'render_block', 'wp_render_elements_class_name', 10 );
remove_filter( 'render_block_data', 'wp_render_elements_support_styles', 10 );

add_filter( 'render_block', 'gutenberg_render_elements_class_name', 10, 2 );
add_filter( 'render_block_data', 'gutenberg_render_elements_support_styles', 10, 1 );
