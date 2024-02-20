<?php
/**
 * Elements styles block support.
 *
 * @package WordPress
 * @since 5.8.0
 */

/**
 * Gets the elements class names.
 *
 * @since 6.0.0
 * @access private
 *
 * @param array $block Block object.
 * @return string The unique class name.
 */
function wp_get_elements_class_name( $block ) {
	return 'wp-elements-' . md5( serialize( $block ) );
}

/**
 * Updates the block content with elements class names.
 *
 * @since 5.8.0
 * @since 6.4.0 Added support for button and heading element styling.
 * @access private
 *
 * @param string $block_content Rendered block content.
 * @param array  $block         Block object.
 * @return string Filtered block content.
 */
function wp_render_elements_support( $block_content, $block ) {
	if ( ! $block_content || ! isset( $block['attrs']['style']['elements'] ) ) {
		return $block_content;
	}

	$block_type = WP_Block_Type_Registry::get_instance()->get_registered( $block['blockName'] );
	if ( ! $block_type ) {
		return $block_content;
	}

	$element_color_properties = array(
		'button'  => array(
			'skip'  => wp_should_skip_block_supports_serialization( $block_type, 'color', 'button' ),
			'paths' => array(
				array( 'button', 'color', 'text' ),
				array( 'button', 'color', 'background' ),
				array( 'button', 'color', 'gradient' ),
			),
		),
		'link'    => array(
			'skip'  => wp_should_skip_block_supports_serialization( $block_type, 'color', 'link' ),
			'paths' => array(
				array( 'link', 'color', 'text' ),
				array( 'link', ':hover', 'color', 'text' ),
			),
		),
		'heading' => array(
			'skip'  => wp_should_skip_block_supports_serialization( $block_type, 'color', 'heading' ),
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

	$skip_all_element_color_serialization = $element_color_properties['button']['skip'] &&
		$element_color_properties['link']['skip'] &&
		$element_color_properties['heading']['skip'];

	if ( $skip_all_element_color_serialization ) {
		return $block_content;
	}

	$elements_style_attributes = $block['attrs']['style']['elements'];

	foreach ( $element_color_properties as $element_config ) {
		if ( $element_config['skip'] ) {
			continue;
		}

		foreach ( $element_config['paths'] as $path ) {
			if ( null !== _wp_array_get( $elements_style_attributes, $path, null ) ) {
				/*
				 * It only takes a single custom attribute to require that the custom
				 * class name be added to the block, so once one is found there's no
				 * need to continue looking for others.
				 *
				 * As is done with the layout hook, this code assumes that the block
				 * contains a single wrapper and that it's the first element in the
				 * rendered output. That first element, if it exists, gets the class.
				 */
				$tags = new WP_HTML_Tag_Processor( $block_content );
				if ( $tags->next_tag() ) {
					$tags->add_class( wp_get_elements_class_name( $block ) );
				}

				return $tags->get_updated_html();
			}
		}
	}

	// If no custom attributes were found then there's nothing to modify.
	return $block_content;
}

/**
 * Renders the elements stylesheet.
 *
 * In the case of nested blocks we want the parent element styles to be rendered before their descendants.
 * This solves the issue of an element (e.g.: link color) being styled in both the parent and a descendant:
 * we want the descendant style to take priority, and this is done by loading it after, in DOM order.
 *
 * @since 6.0.0
 * @since 6.1.0 Implemented the style engine to generate CSS and classnames.
 * @access private
 *
 * @param string|null $pre_render The pre-rendered content. Default null.
 * @param array       $block      The block being rendered.
 * @return null
 */
function wp_render_elements_support_styles( $pre_render, $block ) {
	$block_type           = WP_Block_Type_Registry::get_instance()->get_registered( $block['blockName'] );
	$element_block_styles = isset( $block['attrs']['style']['elements'] ) ? $block['attrs']['style']['elements'] : null;

	if ( ! $element_block_styles ) {
		return null;
	}

	$skip_link_color_serialization         = wp_should_skip_block_supports_serialization( $block_type, 'color', 'link' );
	$skip_heading_color_serialization      = wp_should_skip_block_supports_serialization( $block_type, 'color', 'heading' );
	$skip_button_color_serialization       = wp_should_skip_block_supports_serialization( $block_type, 'color', 'button' );
	$skips_all_element_color_serialization = $skip_link_color_serialization &&
		$skip_heading_color_serialization &&
		$skip_button_color_serialization;

	if ( $skips_all_element_color_serialization ) {
		return null;
	}

	$class_name = wp_get_elements_class_name( $block );

	$element_types = array(
		'button'  => array(
			'selector' => ".$class_name .wp-element-button, .$class_name .wp-block-button__link",
			'skip'     => $skip_button_color_serialization,
		),
		'link'    => array(
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

		$element_style_object = isset( $element_block_styles[ $element_type ] ) ? $element_block_styles[ $element_type ] : null;

		// Process primary element type styles.
		if ( $element_style_object ) {
			wp_style_engine_get_styles(
				$element_style_object,
				array(
					'selector' => $element_config['selector'],
					'context'  => 'block-supports',
				)
			);

			if ( isset( $element_style_object[':hover'] ) ) {
				wp_style_engine_get_styles(
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
				$element_style_object = isset( $element_block_styles[ $element ] )
					? $element_block_styles[ $element ]
					: null;

				if ( $element_style_object ) {
					wp_style_engine_get_styles(
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

	return null;
}

add_filter( 'render_block', 'wp_render_elements_support', 10, 2 );
add_filter( 'pre_render_block', 'wp_render_elements_support_styles', 10, 2 );
