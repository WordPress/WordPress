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

	$input_id        = 'wp-block-search__input-' . ++$instance_id;
	$classnames      = classnames_for_block_core_search( $attributes );
	$show_label      = ( ! empty( $attributes['showLabel'] ) ) ? true : false;
	$use_icon_button = ( ! empty( $attributes['buttonUseIcon'] ) ) ? true : false;
	$show_input      = ( ! empty( $attributes['buttonPosition'] ) && 'button-only' === $attributes['buttonPosition'] ) ? false : true;
	$show_button     = ( ! empty( $attributes['buttonPosition'] ) && 'no-button' === $attributes['buttonPosition'] ) ? false : true;
	$label_markup    = '';
	$input_markup    = '';
	$button_markup   = '';
	$inline_styles   = styles_for_block_core_search( $attributes );

	if ( $show_label ) {
		if ( ! empty( $attributes['label'] ) ) {
			$label_markup = sprintf(
				'<label for="%s" class="wp-block-search__label">%s</label>',
				$input_id,
				$attributes['label']
			);
		} else {
			$label_markup = sprintf(
				'<label for="%s" class="wp-block-search__label screen-reader-text">%s</label>',
				$input_id,
				__( 'Search' )
			);
		}
	}

	if ( $show_input ) {
		$input_markup = sprintf(
			'<input type="search" id="%s" class="wp-block-search__input" name="s" value="%s" placeholder="%s" %s required />',
			$input_id,
			esc_attr( get_search_query() ),
			esc_attr( $attributes['placeholder'] ),
			$inline_styles['shared']
		);
	}

	if ( $show_button ) {
		$button_internal_markup = '';
		$button_classes         = '';

		if ( ! $use_icon_button ) {
			if ( ! empty( $attributes['buttonText'] ) ) {
				$button_internal_markup = $attributes['buttonText'];
			}
		} else {
			$button_classes        .= 'has-icon';
			$button_internal_markup =
				'<svg id="search-icon" class="search-icon" viewBox="0 0 24 24" width="24" height="24">
			        <path d="M13.5 6C10.5 6 8 8.5 8 11.5c0 1.1.3 2.1.9 3l-3.4 3 1 1.1 3.4-2.9c1 .9 2.2 1.4 3.6 1.4 3 0 5.5-2.5 5.5-5.5C19 8.5 16.5 6 13.5 6zm0 9.5c-2.2 0-4-1.8-4-4s1.8-4 4-4 4 1.8 4 4-1.8 4-4 4z"></path>
			    </svg>';
		}

		$button_markup = sprintf(
			'<button type="submit" class="wp-block-search__button %s"%s>%s</button>',
			$button_classes,
			$inline_styles['shared'],
			$button_internal_markup
		);
	}

	$field_markup       = sprintf(
		'<div class="wp-block-search__inside-wrapper"%s>%s</div>',
		$inline_styles['wrapper'],
		$input_markup . $button_markup
	);
	$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => $classnames ) );

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
	$shared_styles  = array();
	$wrapper_styles = array();

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
		// Shared style for button and input radius values.
		$border_radius   = $attributes['style']['border']['radius'];
		$shared_styles[] = sprintf( 'border-radius: %spx;', esc_attr( $border_radius ) );

		// Apply wrapper border radius if button placed inside.
		$button_inside = ! empty( $attributes['buttonPosition'] ) &&
			'button-inside' === $attributes['buttonPosition'];

		if ( $button_inside ) {
			// We adjust the border radius value for the outer wrapper element
			// to make it visually consistent with the radius applied to inner
			// elements.
			$default_padding  = 4;
			$adjusted_radius  = $border_radius + $default_padding;
			$wrapper_styles[] = sprintf( 'border-radius: %dpx;', esc_attr( $adjusted_radius ) );
		}
	}

	return array(
		'shared'  => ! empty( $shared_styles ) ? sprintf( ' style="%s"', implode( ' ', $shared_styles ) ) : '',
		'wrapper' => ! empty( $wrapper_styles ) ? sprintf( ' style="%s"', implode( ' ', $wrapper_styles ) ) : '',
	);
}
