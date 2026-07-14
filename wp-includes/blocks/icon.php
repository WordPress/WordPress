<?php
/**
 * Server-side rendering of the `core/icon` block.
 *
 * @package WordPress
 */

/**
 * Renders the `core/icon` block on server.
 *
 * @since 7.0.0
 *
 * @param array $attributes The block attributes.
 *
 * @return string Returns the Icon.
 */
function render_block_core_icon( $attributes ) {
	if ( empty( $attributes['icon'] ) ) {
		return;
	}

	// Text color and background color.
	$color_styles = array();

	$preset_text_color    = array_key_exists( 'textColor', $attributes ) ? "var:preset|color|{$attributes['textColor']}" : null;
	$custom_text_color    = $attributes['style']['color']['text'] ?? null;
	$color_styles['text'] = $preset_text_color ? $preset_text_color : $custom_text_color;

	$preset_background_color    = array_key_exists( 'backgroundColor', $attributes ) ? "var:preset|color|{$attributes['backgroundColor']}" : null;
	$custom_background_color    = $attributes['style']['color']['background'] ?? null;
	$color_styles['background'] = $preset_background_color ? $preset_background_color : $custom_background_color;

	// Border.
	$border_styles = array();
	$sides         = array( 'top', 'right', 'bottom', 'left' );

	if ( isset( $attributes['style']['border']['radius'] ) ) {
		$border_styles['radius'] = $attributes['style']['border']['radius'];
	}
	if ( isset( $attributes['style']['border']['style'] ) ) {
		$border_styles['style'] = $attributes['style']['border']['style'];
	}
	if ( isset( $attributes['style']['border']['width'] ) ) {
		$border_styles['width'] = $attributes['style']['border']['width'];
	}

	$preset_color           = array_key_exists( 'borderColor', $attributes ) ? "var:preset|color|{$attributes['borderColor']}" : null;
	$custom_color           = $attributes['style']['border']['color'] ?? null;
	$border_styles['color'] = $preset_color ? $preset_color : $custom_color;

	foreach ( $sides as $side ) {
		$border                 = $attributes['style']['border'][ $side ] ?? null;
		$border_styles[ $side ] = array(
			'color' => $border['color'] ?? null,
			'style' => $border['style'] ?? null,
			'width' => $border['width'] ?? null,
		);
	}

	// Spacing (Padding).
	$spacing_styles = array();
	if ( isset( $attributes['style']['spacing']['padding'] ) ) {
		$spacing_styles['padding'] = $attributes['style']['spacing']['padding'];
	}

	// Dimensions (Width).
	$dimensions_styles = array();
	if ( isset( $attributes['style']['dimensions']['width'] ) ) {
		$dimensions_styles['width'] = $attributes['style']['dimensions']['width'];
	}

	// Generate styles and classes.
	$styles = wp_style_engine_get_styles(
		array(
			'color'      => $color_styles,
			'border'     => $border_styles,
			'spacing'    => $spacing_styles,
			'dimensions' => $dimensions_styles,
		),
	);

	$svg = wp_get_icon(
		$attributes['icon'],
		array(
			// Width is applied via the dimensions block support styles below.
			'size'  => null,
			'class' => $styles['classnames'] ?? '',
			'label' => $attributes['ariaLabel'] ?? '',
		)
	);

	if ( '' === $svg ) {
		return;
	}

	$processor = new WP_HTML_Tag_Processor( $svg );
	if ( $processor->next_tag( 'svg' ) ) {
		if ( ! empty( $styles['css'] ) ) {
			$processor->set_attribute( 'style', $styles['css'] );
		}

		// Apply flip classes to the SVG.
		$flip_horizontal = $attributes['flipHorizontal'] ?? false;
		$flip_vertical   = $attributes['flipVertical'] ?? false;

		if ( $flip_horizontal ) {
			$processor->add_class( 'is-flip-horizontal' );
		}
		if ( $flip_vertical ) {
			$processor->add_class( 'is-flip-vertical' );
		}

		$rotation = isset( $attributes['rotation'] ) ? (int) $attributes['rotation'] : 0;

		if ( $rotation ) {
			$current_style = $processor->get_attribute( 'style' ) ?? '';
			$rotation_css  = 'rotate: ' . $rotation . 'deg;';
			if ( $current_style ) {
				$processor->set_attribute( 'style', $current_style . ' ' . $rotation_css );
			} else {
				$processor->set_attribute( 'style', $rotation_css );
			}
		}

		$svg = $processor->get_updated_html();
	}

	$wrapper_attributes = get_block_wrapper_attributes();
	return sprintf( '<div %s>%s</div>', $wrapper_attributes, $svg );
}


/**
 * Registers the `core/icon` block on server.
 *
 * @since 7.0.0
 */
function register_block_core_icon() {
	register_block_type_from_metadata(
		__DIR__ . '/icon',
		array(
			'render_callback' => 'render_block_core_icon',
		)
	);
}
add_action( 'init', 'register_block_core_icon' );
