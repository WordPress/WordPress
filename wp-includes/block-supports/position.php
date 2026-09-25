<?php
/**
 * Position block support flag.
 *
 * @package WordPress
 * @since 6.2.0
 */

/**
 * Registers the style block attribute for block types that support it.
 *
 * @since 6.2.0
 * @access private
 *
 * @param WP_Block_Type $block_type Block Type.
 */
function wp_register_position_support( $block_type ) {
	$has_position_support = block_has_support( $block_type, 'position', false );

	// Set up attributes and styles within that if needed.
	if ( ! $block_type->attributes ) {
		$block_type->attributes = array();
	}

	if ( $has_position_support && ! array_key_exists( 'style', $block_type->attributes ) ) {
		$block_type->attributes['style'] = array(
			'type' => 'object',
		);
	}
}

/**
 * Returns the CSS rules for a position style configuration.
 *
 * @since 7.2.0
 *
 * @param string $selector               CSS selector to scope the rules to.
 * @param mixed  $position               Position style configuration.
 * @param array  $allowed_position_types Position types the theme supports.
 * @return array CSS rules, or an empty array when the configuration is not allowed.
 */
function wp_get_position_support_styles( $selector, $position, $allowed_position_types ) {
	$styles = array();

	if ( ! is_array( $position ) || ! is_string( $position['type'] ?? null ) ) {
		return $styles;
	}

	$position_type = $position['type'];

	if ( ! in_array( $position_type, $allowed_position_types, true ) ) {
		return $styles;
	}

	$sides = array( 'top', 'right', 'bottom', 'left' );

	foreach ( $sides as $side ) {
		$side_value = $position[ $side ] ?? null;
		if ( null !== $side_value ) {
			/*
			 * For fixed or sticky top positions,
			 * ensure the value includes an offset for the logged in admin bar.
			 */
			if ( 'top' === $side ) {
				// Ensure 0 values can be used in `calc()` calculations.
				if ( '0' === $side_value || 0 === $side_value ) {
					$side_value = '0px';
				}

				// Ensure current side value also factors in the height of the logged in admin bar.
				$side_value = "calc($side_value + var(--wp-admin--admin-bar--position-offset, 0px))";
			}

			$styles[] = array(
				'selector'     => $selector,
				'declarations' => array(
					$side => $side_value,
				),
			);
		}
	}

	$styles[] = array(
		'selector'     => $selector,
		'declarations' => array(
			'position' => $position_type,
			'z-index'  => '10',
		),
	);

	return $styles;
}

/**
 * Renders position styles to the block wrapper.
 *
 * @since 6.2.0
 * @since 7.2.0 Added support for viewport states.
 * @access private
 *
 * @param string $block_content Rendered block content.
 * @param array  $block         Block object.
 * @return string Filtered block content.
 */
function wp_render_position_support( $block_content, $block ) {
	$block_type           = WP_Block_Type_Registry::get_instance()->get_registered( $block['blockName'] );
	$has_position_support = block_has_support( $block_type, 'position', false );
	$style_attribute      = $block['attrs']['style'] ?? null;

	if ( ! $has_position_support || ! is_array( $style_attribute ) ) {
		return $block_content;
	}

	/*
	 * Position styles can exist in either the default state or a viewport state.
	 */
	$has_position_style = ! empty( $style_attribute['position'] );
	if ( ! $has_position_style ) {
		foreach ( $style_attribute as $key => $style ) {
			if (
				is_string( $key ) &&
				str_starts_with( $key, '@' ) &&
				is_array( $style ) &&
				! empty( $style['position'] )
			) {
				$has_position_style = true;
				break;
			}
		}
	}

	if ( ! $has_position_style ) {
		return $block_content;
	}

	$global_settings          = wp_get_global_settings();
	$theme_has_sticky_support = $global_settings['position']['sticky'] ?? false;
	$theme_has_fixed_support  = $global_settings['position']['fixed'] ?? false;

	// Only allow output for position types that the theme supports.
	$allowed_position_types = array();
	if ( true === $theme_has_sticky_support ) {
		$allowed_position_types[] = 'sticky';
	}
	if ( true === $theme_has_fixed_support ) {
		$allowed_position_types[] = 'fixed';
	}

	$viewport_settings        = $global_settings['viewport'] ?? null;
	$responsive_media_queries = WP_Theme_JSON::get_viewport_media_queries( $viewport_settings );
	$class_name               = wp_unique_id( 'wp-container-' );
	$selector                 = ".$class_name";
	$position_styles          = array();
	$wrapper_classes          = array();
	$base_position            = $style_attribute['position'] ?? null;

	// Default viewport (base) position styles.
	$base_styles = wp_get_position_support_styles(
		$selector,
		$base_position,
		$allowed_position_types
	);

	if ( ! empty( $base_styles ) ) {
		$position_styles   = $base_styles;
		$wrapper_classes[] = 'is-position-' . $base_position['type'];
	}

	/*
	 * Responsive viewport state styles. A viewport state inherits any values
	 * it does not set from the default state, so a state that only overrides
	 * e.g. the `top` offset keeps the base position type.
	 */
	foreach ( $responsive_media_queries as $breakpoint => $media_query ) {
		$viewport_position_style = $style_attribute[ $breakpoint ]['position'] ?? null;

		if ( empty( $viewport_position_style ) || ! is_array( $viewport_position_style ) ) {
			continue;
		}

		$viewport_position = is_array( $base_position )
			? array_replace( $base_position, $viewport_position_style )
			: $viewport_position_style;

		$viewport_styles = wp_get_position_support_styles(
			$selector,
			$viewport_position,
			$allowed_position_types
		);

		if ( ! empty( $viewport_styles ) ) {
			$wrapper_classes[] = 'is-position-' . $viewport_position['type'];
		} elseif ( ! empty( $base_styles ) ) {
			/*
			 * The viewport state can explicitly clear the position type inherited from the
			 * default state.
			 */
			$viewport_styles = array(
				array(
					'selector'     => $selector,
					'declarations' => array( 'position' => 'static' ),
				),
			);
		} else {
			continue;
		}

		foreach ( $viewport_styles as $index => $rule ) {
			$viewport_styles[ $index ]['rules_group'] = $media_query;
		}

		$position_styles = array_merge( $position_styles, $viewport_styles );
	}

	if ( ! empty( $position_styles ) ) {
		/*
		 * Add to the style engine store to enqueue and render position styles.
		 */
		wp_style_engine_get_stylesheet_from_css_rules(
			$position_styles,
			array(
				'context'  => 'block-supports',
				'prettify' => false,
			)
		);

		// Inject class name to block container markup.
		$content = new WP_HTML_Tag_Processor( $block_content );
		$content->next_tag();
		$content->add_class( $class_name );
		foreach ( array_unique( $wrapper_classes ) as $class ) {
			$content->add_class( $class );
		}
		return (string) $content;
	}

	return $block_content;
}

// Register the block support.
WP_Block_Supports::get_instance()->register(
	'position',
	array(
		'register_attribute' => 'wp_register_position_support',
	)
);
add_filter( 'render_block', 'wp_render_position_support', 10, 2 );
