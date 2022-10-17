<?php
/**
 * Server-side rendering of the `core/search` block.
 *
 * @package WordPress
 */

/**
 * Dynamically renders the `core/search` block.
 *
 * @param array $attributes The block attributes.
 *
 * @return string The search block markup.
 */
function render_block_core_search( $attributes ) {
	static $instance_id = 0;

	// Older versions of the Search block defaulted the label and buttonText
	// attributes to `__( 'Search' )` meaning that many posts contain `<!--
	// wp:search /-->`. Support these by defaulting an undefined label and
	// buttonText to `__( 'Search' )`.
	$attributes = wp_parse_args(
		$attributes,
		array(
			'label'      => __( 'Search' ),
			'buttonText' => __( 'Search' ),
		)
	);

	$input_id         = 'wp-block-search__input-' . ++$instance_id;
	$classnames       = classnames_for_block_core_search( $attributes );
	$show_label       = ( ! empty( $attributes['showLabel'] ) ) ? true : false;
	$use_icon_button  = ( ! empty( $attributes['buttonUseIcon'] ) ) ? true : false;
	$show_input       = ( ! empty( $attributes['buttonPosition'] ) && 'button-only' === $attributes['buttonPosition'] ) ? false : true;
	$show_button      = ( ! empty( $attributes['buttonPosition'] ) && 'no-button' === $attributes['buttonPosition'] ) ? false : true;
	$label_markup     = '';
	$input_markup     = '';
	$button_markup    = '';
	$inline_styles    = styles_for_block_core_search( $attributes );
	$color_classes    = get_color_classes_for_block_core_search( $attributes );
	$is_button_inside = ! empty( $attributes['buttonPosition'] ) &&
		'button-inside' === $attributes['buttonPosition'];
	// Border color classes need to be applied to the elements that have a border color.
	$border_color_classes = get_border_color_classes_for_block_core_search( $attributes );

	$label_inner_html = empty( $attributes['label'] ) ? __( 'Search' ) : wp_kses_post( $attributes['label'] );

	$label_markup = sprintf(
		'<label for="%1$s" class="wp-block-search__label screen-reader-text">%2$s</label>',
		esc_attr( $input_id ),
		$label_inner_html
	);
	if ( $show_label && ! empty( $attributes['label'] ) ) {
		$label_markup = sprintf(
			'<label for="%1$s" class="wp-block-search__label">%2$s</label>',
			$input_id,
			$label_inner_html
		);
	}

	if ( $show_input ) {
		$input_classes = ! $is_button_inside ? $border_color_classes : '';
		$input_markup  = sprintf(
			'<input type="search" id="%s" class="wp-block-search__input %s" name="s" value="%s" placeholder="%s" %s required />',
			$input_id,
			esc_attr( $input_classes ),
			esc_attr( get_search_query() ),
			esc_attr( $attributes['placeholder'] ),
			$inline_styles['input']
		);
	}

	if ( $show_button ) {
		$button_internal_markup = '';
		$button_classes         = $color_classes;
		$aria_label             = '';

		if ( ! $is_button_inside ) {
			$button_classes .= ' ' . $border_color_classes;
		}
		if ( ! $use_icon_button ) {
			if ( ! empty( $attributes['buttonText'] ) ) {
				$button_internal_markup = wp_kses_post( $attributes['buttonText'] );
			}
		} else {
			$aria_label             = sprintf( 'aria-label="%s"', esc_attr( wp_strip_all_tags( $attributes['label'] ) ) );
			$button_classes        .= ' has-icon';
			$button_internal_markup =
				'<svg id="search-icon" class="search-icon" viewBox="0 0 24 24" width="24" height="24">
					<path d="M13.5 6C10.5 6 8 8.5 8 11.5c0 1.1.3 2.1.9 3l-3.4 3 1 1.1 3.4-2.9c1 .9 2.2 1.4 3.6 1.4 3 0 5.5-2.5 5.5-5.5C19 8.5 16.5 6 13.5 6zm0 9.5c-2.2 0-4-1.8-4-4s1.8-4 4-4 4 1.8 4 4-1.8 4-4 4z"></path>
				</svg>';
		}

		$button_markup = sprintf(
			'<button type="submit" class="wp-block-search__button %s" %s %s>%s</button>',
			esc_attr( $button_classes ),
			$inline_styles['button'],
			$aria_label,
			$button_internal_markup
		);
	}

	$field_markup_classes = $is_button_inside ? $border_color_classes : '';
	$field_markup         = sprintf(
		'<div class="wp-block-search__inside-wrapper %s" %s>%s</div>',
		esc_attr( $field_markup_classes ),
		$inline_styles['wrapper'],
		$input_markup . $button_markup
	);
	$wrapper_attributes   = get_block_wrapper_attributes( array( 'class' => $classnames ) );

	return sprintf(
		'<form role="search" method="get" action="%s" %s>%s</form>',
		esc_url( home_url( '/' ) ),
		$wrapper_attributes,
		$label_markup . $field_markup
	);
}

/**
 * Registers the `core/search` block on the server.
 */
function register_block_core_search() {
	register_block_type_from_metadata(
		__DIR__ . '/search',
		array(
			'render_callback' => 'render_block_core_search',
		)
	);
}
add_action( 'init', 'register_block_core_search' );

/**
 * Builds the correct top level classnames for the 'core/search' block.
 *
 * @param array $attributes The block attributes.
 *
 * @return string The classnames used in the block.
 */
function classnames_for_block_core_search( $attributes ) {
	$classnames = array();

	if ( ! empty( $attributes['buttonPosition'] ) ) {
		if ( 'button-inside' === $attributes['buttonPosition'] ) {
			$classnames[] = 'wp-block-search__button-inside';
		}

		if ( 'button-outside' === $attributes['buttonPosition'] ) {
			$classnames[] = 'wp-block-search__button-outside';
		}

		if ( 'no-button' === $attributes['buttonPosition'] ) {
			$classnames[] = 'wp-block-search__no-button';
		}

		if ( 'button-only' === $attributes['buttonPosition'] ) {
			$classnames[] = 'wp-block-search__button-only';
		}
	}

	if ( isset( $attributes['buttonUseIcon'] ) ) {
		if ( ! empty( $attributes['buttonPosition'] ) && 'no-button' !== $attributes['buttonPosition'] ) {
			if ( $attributes['buttonUseIcon'] ) {
				$classnames[] = 'wp-block-search__icon-button';
			} else {
				$classnames[] = 'wp-block-search__text-button';
			}
		}
	}

	return implode( ' ', $classnames );
}

/**
 * Builds an array of inline styles for the search block.
 *
 * The result will contain one entry for shared styles such as those for the
 * inner input or button and a second for the inner wrapper should the block
 * be positioning the button "inside".
 *
 * @param  array $attributes The block attributes.
 *
 * @return array Style HTML attribute.
 */
function styles_for_block_core_search( $attributes ) {
	$wrapper_styles = array();
	$button_styles  = array();
	$input_styles   = array();

	// Add width styles.
	$has_width   = ! empty( $attributes['width'] ) && ! empty( $attributes['widthUnit'] );
	$button_only = ! empty( $attributes['buttonPosition'] ) && 'button-only' === $attributes['buttonPosition'];

	if ( $has_width && ! $button_only ) {
		$wrapper_styles[] = sprintf(
			'width: %d%s;',
			esc_attr( $attributes['width'] ),
			esc_attr( $attributes['widthUnit'] )
		);
	}

	// Add border radius styles.
	$has_border_radius = ! empty( $attributes['style']['border']['radius'] );

	if ( $has_border_radius ) {
		$default_padding = '4px';
		$border_radius   = $attributes['style']['border']['radius'];
		// Apply wrapper border radius if button placed inside.
		$is_button_inside = ! empty( $attributes['buttonPosition'] ) &&
			'button-inside' === $attributes['buttonPosition'];

		if ( is_array( $border_radius ) ) {
			// Apply styles for individual corner border radii.
			foreach ( $border_radius as $key => $value ) {
				if ( null !== $value ) {
					// Convert camelCase key to kebab-case.
					$name = strtolower( preg_replace( '/(?<!^)[A-Z]/', '-$0', $key ) );

					// Add shared styles for individual border radii for input & button.
					$border_style    = sprintf(
						'border-%s-radius: %s;',
						esc_attr( $name ),
						esc_attr( $value )
					);
					$input_styles[]  = $border_style;
					$button_styles[] = $border_style;

					// Add adjusted border radius styles for the wrapper element
					// if button is positioned inside.
					if ( $is_button_inside && intval( $value ) !== 0 ) {
						$wrapper_styles[] = sprintf(
							'border-%s-radius: calc(%s + %s);',
							esc_attr( $name ),
							esc_attr( $value ),
							$default_padding
						);
					}
				}
			}
		} else {
			// Numeric check is for backwards compatibility purposes.
			$border_radius   = is_numeric( $border_radius ) ? $border_radius . 'px' : $border_radius;
			$border_style    = sprintf( 'border-radius: %s;', esc_attr( $border_radius ) );
			$input_styles[]  = $border_style;
			$button_styles[] = $border_style;

			if ( $is_button_inside && intval( $border_radius ) !== 0 ) {
				// Adjust wrapper border radii to maintain visual consistency
				// with inner elements when button is positioned inside.
				$wrapper_styles[] = sprintf(
					'border-radius: calc(%s + %s);',
					esc_attr( $border_radius ),
					$default_padding
				);
			}
		}
	}

	// Add border color styles.
	$has_border_color = ! empty( $attributes['style']['border']['color'] );

	if ( $has_border_color ) {
		$border_color     = $attributes['style']['border']['color'];
		$is_button_inside = ! empty( $attributes['buttonPosition'] ) &&
			'button-inside' === $attributes['buttonPosition'];

		// Apply wrapper border color if button placed inside.
		if ( $is_button_inside ) {
			$wrapper_styles[] = sprintf( 'border-color: %s;', esc_attr( $border_color ) );
		} else {
			$button_styles[] = sprintf( 'border-color: %s;', esc_attr( $border_color ) );
			$input_styles[]  = sprintf( 'border-color: %s;', esc_attr( $border_color ) );
		}
	}

	// Add color styles.
	$has_text_color = ! empty( $attributes['style']['color']['text'] );
	if ( $has_text_color ) {
		$button_styles[] = sprintf( 'color: %s;', $attributes['style']['color']['text'] );
	}

	$has_background_color = ! empty( $attributes['style']['color']['background'] );
	if ( $has_background_color ) {
		$button_styles[] = sprintf( 'background-color: %s;', $attributes['style']['color']['background'] );
	}

	$has_custom_gradient = ! empty( $attributes['style']['color']['gradient'] );
	if ( $has_custom_gradient ) {
		$button_styles[] = sprintf( 'background: %s;', $attributes['style']['color']['gradient'] );
	}

	return array(
		'input'   => ! empty( $input_styles ) ? sprintf( ' style="%s"', esc_attr( safecss_filter_attr( implode( ' ', $input_styles ) ) ) ) : '',
		'button'  => ! empty( $button_styles ) ? sprintf( ' style="%s"', esc_attr( safecss_filter_attr( implode( ' ', $button_styles ) ) ) ) : '',
		'wrapper' => ! empty( $wrapper_styles ) ? sprintf( ' style="%s"', esc_attr( safecss_filter_attr( implode( ' ', $wrapper_styles ) ) ) ) : '',
	);
}

/**
 * Returns border color classnames depending on whether there are named or custom border colors.
 *
 * @param array $attributes The block attributes.
 *
 * @return string The border color classnames to be applied to the block elements.
 */
function get_border_color_classes_for_block_core_search( $attributes ) {
	$has_custom_border_color = ! empty( $attributes['style']['border']['color'] );
	$border_color_classes    = ! empty( $attributes['borderColor'] ) ? sprintf( 'has-border-color has-%s-border-color', $attributes['borderColor'] ) : '';
	// If there's a border color style and no `borderColor` text string, we still want to add the generic `has-border-color` class name to the element.
	if ( $has_custom_border_color && empty( $attributes['borderColor'] ) ) {
		$border_color_classes = 'has-border-color';
	}
	return $border_color_classes;
}

/**
 * Returns color classnames depending on whether there are named or custom text and background colors.
 *
 * @param array $attributes The block attributes.
 *
 * @return string The color classnames to be applied to the block elements.
 */
function get_color_classes_for_block_core_search( $attributes ) {
	$classnames = array();

	// Text color.
	$has_named_text_color  = ! empty( $attributes['textColor'] );
	$has_custom_text_color = ! empty( $attributes['style']['color']['text'] );
	if ( $has_named_text_color ) {
		$classnames[] = sprintf( 'has-text-color has-%s-color', $attributes['textColor'] );
	} elseif ( $has_custom_text_color ) {
		// If a custom 'textColor' was selected instead of a preset, still add the generic `has-text-color` class.
		$classnames[] = 'has-text-color';
	}

	// Background color.
	$has_named_background_color  = ! empty( $attributes['backgroundColor'] );
	$has_custom_background_color = ! empty( $attributes['style']['color']['background'] );
	$has_named_gradient          = ! empty( $attributes['gradient'] );
	$has_custom_gradient         = ! empty( $attributes['style']['color']['gradient'] );
	if (
		$has_named_background_color ||
		$has_custom_background_color ||
		$has_named_gradient ||
		$has_custom_gradient
	) {
		$classnames[] = 'has-background';
	}
	if ( $has_named_background_color ) {
		$classnames[] = sprintf( 'has-%s-background-color', $attributes['backgroundColor'] );
	}
	if ( $has_named_gradient ) {
		$classnames[] = sprintf( 'has-%s-gradient-background', $attributes['gradient'] );
	}

	return implode( ' ', $classnames );
}
