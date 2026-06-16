<?php
/**
 * Layout block support flag.
 *
 * @package WordPress
 * @since 5.8.0
 */

/**
 * Gets the first style variation name from a className string that matches a registered style.
 *
 * @since 7.0.0
 *
 * @param string                              $class_name        CSS class string for a block.
 * @param array<string, array<string, mixed>> $registered_styles Currently registered block styles.
 * @return string|null The name of the first registered variation, or null if none found.
 */
function wp_get_block_style_variation_name_from_registered_style( string $class_name, array $registered_styles = array() ): ?string {
	if ( ! $class_name ) {
		return null;
	}

	$registered_names = array_filter( array_column( $registered_styles, 'name' ) );

	$prefix = 'is-style-';
	$length = strlen( $prefix );

	foreach ( explode( ' ', $class_name ) as $class ) {
		if ( str_starts_with( $class, $prefix ) ) {
			$variation = substr( $class, $length );
			if ( 'default' !== $variation && in_array( $variation, $registered_names, true ) ) {
				return $variation;
			}
		}
	}

	return null;
}

/**
 * Returns the child-layout-only subset of a layout object.
 *
 * @since 7.1.0
 *
 * @param mixed $layout Layout object.
 * @return array Child layout values, or an empty array.
 */
function wp_get_layout_child_values( $layout ) {
	if ( ! is_array( $layout ) ) {
		return array();
	}

	return array_intersect_key(
		$layout,
		array_flip( array( 'selfStretch', 'flexSize', 'columnStart', 'columnSpan', 'rowStart', 'rowSpan' ) )
	);
}

/**
 * Returns the container-layout subset of a layout object.
 *
 * @since 7.1.0
 *
 * @param mixed $layout Layout object.
 * @return array Container layout values, or an empty array.
 */
function wp_get_layout_container_values( $layout ) {
	if ( ! is_array( $layout ) ) {
		return array();
	}

	return array_diff_key(
		$layout,
		array_flip( array( 'selfStretch', 'flexSize', 'columnStart', 'columnSpan', 'rowStart', 'rowSpan' ) )
	);
}

/**
 * Sanitizes a block gap value before layout style generation.
 *
 * @since 7.1.0
 *
 * @param string|array|null $gap_value Block gap value.
 * @return string|array|null Sanitized block gap value.
 */
function wp_sanitize_block_gap_value( $gap_value ) {
	if ( is_array( $gap_value ) ) {
		foreach ( $gap_value as $key => $value ) {
			$gap_value[ $key ] = $value && preg_match( '%[\\\(&=}]|/\*%', $value ) ? null : $value;
		}

		return $gap_value;
	}

	return $gap_value && preg_match( '%[\\\(&=}]|/\*%', $gap_value ) ? null : $gap_value;
}

/**
 * Returns child layout styles for a block affected by its parent's layout.
 *
 * @since 7.1.0
 *
 * @param string     $selector           CSS selector.
 * @param array      $child_layout       Child layout values.
 * @param array      $parent_layout      Parent layout values.
 * @param array|null $viewport_overrides Optional. Child viewport layout overrides to emit.
 * @return array Child layout style rules.
 */
function wp_get_child_layout_style_rules( $selector, $child_layout, $parent_layout = array(), $viewport_overrides = null ) {
	$base_child_layout              = is_array( $child_layout ) ? $child_layout : array();
	$viewport_overrides             = is_array( $viewport_overrides ) ? $viewport_overrides : null;
	$child_layout                   = null === $viewport_overrides ? $base_child_layout : array_replace( $base_child_layout, $viewport_overrides );
	$child_layout_declarations      = array();
	$child_layout_styles            = array();
	$has_viewport_property_override = static function ( $property ) use ( $viewport_overrides ) {
		return array_key_exists( $property, $viewport_overrides );
	};

	$self_stretch = $child_layout['selfStretch'] ?? null;

	if ( null === $viewport_overrides || $has_viewport_property_override( 'selfStretch' ) || $has_viewport_property_override( 'flexSize' ) ) {
		if ( 'fixed' === $self_stretch && isset( $child_layout['flexSize'] ) ) {
			$child_layout_declarations['flex-basis'] = $child_layout['flexSize'];
			$child_layout_declarations['box-sizing'] = 'border-box';
		} elseif ( 'fill' === $self_stretch ) {
			$child_layout_declarations['flex-grow'] = '1';
		}
	}

	$column_start = $child_layout['columnStart'] ?? null;
	$column_span  = $child_layout['columnSpan'] ?? null;
	if ( null === $viewport_overrides || $has_viewport_property_override( 'columnStart' ) || $has_viewport_property_override( 'columnSpan' ) ) {
		if ( $column_start && $column_span ) {
			$child_layout_declarations['grid-column'] = "$column_start / span $column_span";
		} elseif ( $column_start ) {
			$child_layout_declarations['grid-column'] = "$column_start";
		} elseif ( $column_span ) {
			$child_layout_declarations['grid-column'] = "span $column_span";
		}
	}

	$row_start = $child_layout['rowStart'] ?? null;
	$row_span  = $child_layout['rowSpan'] ?? null;
	if ( null === $viewport_overrides || $has_viewport_property_override( 'rowStart' ) || $has_viewport_property_override( 'rowSpan' ) ) {
		if ( $row_start && $row_span ) {
			$child_layout_declarations['grid-row'] = "$row_start / span $row_span";
		} elseif ( $row_start ) {
			$child_layout_declarations['grid-row'] = "$row_start";
		} elseif ( $row_span ) {
			$child_layout_declarations['grid-row'] = "span $row_span";
		}
	}

	if ( ! empty( $child_layout_declarations ) ) {
		$child_layout_styles[] = array(
			'selector'     => $selector,
			'declarations' => $child_layout_declarations,
		);
	}

	$minimum_column_width = $parent_layout['minimumColumnWidth'] ?? null;
	$column_count         = $parent_layout['columnCount'] ?? null;

	/*
	 * If columnSpan or columnStart is set, and the parent grid is responsive, i.e. if it has a minimumColumnWidth set,
	 * the columnSpan should be removed once the grid is smaller than the span, and columnStart should be removed
	 * once the grid has less columns than the start.
	 * If there's a minimumColumnWidth, the grid is responsive. But if the minimumColumnWidth value wasn't changed, it won't be set.
	 * In that case, if columnCount doesn't exist, we can assume that the grid is responsive.
	 */
	if ( null === $viewport_overrides && ( $column_span || $column_start ) && ( $minimum_column_width || ! $column_count ) ) {
		$column_span_number  = floatval( $column_span );
		$column_start_number = floatval( $column_start );
		$parent_column_width = $minimum_column_width ? $minimum_column_width : '12rem';
		$parent_column_value = floatval( $parent_column_width );
		$parent_column_unit  = explode( $parent_column_value, $parent_column_width );

		$num_cols_to_break_at = 2;
		if ( $column_span_number && $column_start_number ) {
			$num_cols_to_break_at = $column_start_number + $column_span_number - 1;
		} elseif ( $column_span_number ) {
			$num_cols_to_break_at = $column_span_number;
		} else {
			$num_cols_to_break_at = $column_start_number;
		}

		/*
		 * If there is no unit, the width has somehow been mangled so we reset both unit and value
		 * to defaults.
		 * Additionally, the unit should be one of px, rem or em, so that also needs to be checked.
		 */
		if ( count( $parent_column_unit ) <= 1 ) {
			$parent_column_unit  = 'rem';
			$parent_column_value = 12;
		} else {
			$parent_column_unit = $parent_column_unit[1];

			if ( ! in_array( $parent_column_unit, array( 'px', 'rem', 'em' ), true ) ) {
				$parent_column_unit = 'rem';
			}
		}

		/*
		 * A default gap value is used for this computation because custom gap values may not be
		 * viable to use in the computation of the container query value.
		 */
		$default_gap_value             = 'px' === $parent_column_unit ? 24 : 1.5;
		$container_query_value         = $num_cols_to_break_at * $parent_column_value + ( $num_cols_to_break_at - 1 ) * $default_gap_value;
		$minimum_container_query_value = $parent_column_value * 2 + $default_gap_value - 1;
		$container_query_value         = max( $container_query_value, $minimum_container_query_value ) . $parent_column_unit;
		// If a span is set we want to preserve it as long as possible, otherwise we just reset the value.
		$grid_column_value = $column_span && $column_span > 1 ? '1/-1' : 'auto';

		$child_layout_styles[] = array(
			'rules_group'  => "@container (max-width: $container_query_value )",
			'selector'     => $selector,
			'declarations' => array(
				'grid-column' => $grid_column_value,
				'grid-row'    => 'auto',
			),
		);
	}

	return $child_layout_styles;
}

/**
 * Returns layout definitions, keyed by layout type.
 *
 * Provides a common definition of slugs, classnames, base styles, and spacing styles for each layout type.
 * When making changes or additions to layout definitions, the corresponding JavaScript definitions should
 * also be updated.
 *
 * @since 6.3.0
 * @since 6.6.0 Updated specificity for compatibility with 0-1-0 global styles specificity.
 * @access private
 *
 * @return array[] Layout definitions.
 */
function wp_get_layout_definitions() {
	$layout_definitions = array(
		'default'     => array(
			'name'          => 'default',
			'slug'          => 'flow',
			'className'     => 'is-layout-flow',
			'baseStyles'    => array(
				array(
					'selector' => ' > .alignleft',
					'rules'    => array(
						'float'               => 'left',
						'margin-inline-start' => '0',
						'margin-inline-end'   => '2em',
					),
				),
				array(
					'selector' => ' > .alignright',
					'rules'    => array(
						'float'               => 'right',
						'margin-inline-start' => '2em',
						'margin-inline-end'   => '0',
					),
				),
				array(
					'selector' => ' > .aligncenter',
					'rules'    => array(
						'margin-left'  => 'auto !important',
						'margin-right' => 'auto !important',
					),
				),
			),
			'spacingStyles' => array(
				array(
					'selector' => ' > :first-child',
					'rules'    => array(
						'margin-block-start' => '0',
					),
				),
				array(
					'selector' => ' > :last-child',
					'rules'    => array(
						'margin-block-end' => '0',
					),
				),
				array(
					'selector' => ' > *',
					'rules'    => array(
						'margin-block-start' => null,
						'margin-block-end'   => '0',
					),
				),
			),
		),
		'constrained' => array(
			'name'          => 'constrained',
			'slug'          => 'constrained',
			'className'     => 'is-layout-constrained',
			'baseStyles'    => array(
				array(
					'selector' => ' > .alignleft',
					'rules'    => array(
						'float'               => 'left',
						'margin-inline-start' => '0',
						'margin-inline-end'   => '2em',
					),
				),
				array(
					'selector' => ' > .alignright',
					'rules'    => array(
						'float'               => 'right',
						'margin-inline-start' => '2em',
						'margin-inline-end'   => '0',
					),
				),
				array(
					'selector' => ' > .aligncenter',
					'rules'    => array(
						'margin-left'  => 'auto !important',
						'margin-right' => 'auto !important',
					),
				),
				array(
					'selector' => ' > :where(:not(.alignleft):not(.alignright):not(.alignfull))',
					'rules'    => array(
						'max-width'    => 'var(--wp--style--global--content-size)',
						'margin-left'  => 'auto !important',
						'margin-right' => 'auto !important',
					),
				),
				array(
					'selector' => ' > .alignwide',
					'rules'    => array(
						'max-width' => 'var(--wp--style--global--wide-size)',
					),
				),
			),
			'spacingStyles' => array(
				array(
					'selector' => ' > :first-child',
					'rules'    => array(
						'margin-block-start' => '0',
					),
				),
				array(
					'selector' => ' > :last-child',
					'rules'    => array(
						'margin-block-end' => '0',
					),
				),
				array(
					'selector' => ' > *',
					'rules'    => array(
						'margin-block-start' => null,
						'margin-block-end'   => '0',
					),
				),
			),
		),
		'flex'        => array(
			'name'          => 'flex',
			'slug'          => 'flex',
			'className'     => 'is-layout-flex',
			'displayMode'   => 'flex',
			'baseStyles'    => array(
				array(
					'selector' => '',
					'rules'    => array(
						'flex-wrap'   => 'wrap',
						'align-items' => 'center',
					),
				),
				array(
					'selector' => ' > :is(*, div)', // :is(*, div) instead of just * increases the specificity by 001.
					'rules'    => array(
						'margin' => '0',
					),
				),
			),
			'spacingStyles' => array(
				array(
					'selector' => '',
					'rules'    => array(
						'gap' => null,
					),
				),
			),
		),
		'grid'        => array(
			'name'          => 'grid',
			'slug'          => 'grid',
			'className'     => 'is-layout-grid',
			'displayMode'   => 'grid',
			'baseStyles'    => array(
				array(
					'selector' => ' > :is(*, div)', // :is(*, div) instead of just * increases the specificity by 001.
					'rules'    => array(
						'margin' => '0',
					),
				),
			),
			'spacingStyles' => array(
				array(
					'selector' => '',
					'rules'    => array(
						'gap' => null,
					),
				),
			),
		),
	);

	return $layout_definitions;
}

/**
 * Registers the layout block attribute for block types that support it.
 *
 * @since 5.8.0
 * @since 6.3.0 Check for layout support via the `layout` key with fallback to `__experimentalLayout`.
 * @access private
 *
 * @param WP_Block_Type $block_type Block Type.
 */
function wp_register_layout_support( $block_type ) {
	$support_layout = block_has_support( $block_type, 'layout', false ) || block_has_support( $block_type, '__experimentalLayout', false );
	if ( $support_layout ) {
		if ( ! $block_type->attributes ) {
			$block_type->attributes = array();
		}

		if ( ! array_key_exists( 'layout', $block_type->attributes ) ) {
			$block_type->attributes['layout'] = array(
				'type' => 'object',
			);
		}
	}
}

/**
 * Generates the CSS corresponding to the provided layout.
 *
 * @since 5.9.0
 * @since 6.1.0 Added `$block_spacing` param, use style engine to enqueue styles.
 * @since 6.3.0 Added grid layout type.
 * @since 6.6.0 Removed duplicated selector from layout styles.
 *              Enabled negative margins for alignfull children of blocks with custom padding.
 * @since 7.1.0 Added options array with options to process responsive styles.
 * @access private
 *
 * @param string               $selector                      CSS selector.
 * @param array                $layout                        Layout object. The one that is passed has already checked
 *                                                            the existence of default block layout.
 * @param bool                 $has_block_gap_support         Optional. Whether the theme has support for the block gap. Default false.
 * @param string|string[]|null $gap_value                     Optional. The block gap value to apply. Default null.
 * @param bool                 $should_skip_gap_serialization Optional. Whether to skip applying the user-defined value set in the editor. Default false.
 * @param string|array         $fallback_gap_value            Optional. The block gap value to apply. If it's an array expected properties are "top" and/or "left". Default '0.5em'.
 * @param array|null           $block_spacing                 Optional. Custom spacing set on the block. Default null.
 * @param array                $options                       {
 *     Optional. Extra options for internal callers. Default empty array.
 *
 *     @type array       $viewport_overrides     An array of layout property overrides for the sake of style generation,
 *                                               keyed by property name.
 *     @type string|null $rules_group            Optional group name for the rules. Default null.
 *     @type bool        $has_block_gap_override Whether the block gap has been overridden. Default false.
 * }
 * @return string CSS styles on success. Else, empty string.
 */
function wp_get_layout_style( $selector, $layout, $has_block_gap_support = false, $gap_value = null, $should_skip_gap_serialization = false, $fallback_gap_value = '0.5em', $block_spacing = null, $options = array() ) {
	$base_layout                    = is_array( $layout ) ? $layout : array();
	$viewport_overrides             = $options['viewport_overrides'] ?? null;
	$layout_for_styles              = null === $viewport_overrides ? $base_layout : array_replace( $base_layout, $viewport_overrides );
	$layout_type                    = $base_layout['type'] ?? 'default';
	$rules_group                    = $options['rules_group'] ?? null;
	$has_block_gap_override         = ! empty( $options['has_block_gap_override'] );
	$should_output_block_gap        = null === $viewport_overrides || $has_block_gap_override;
	$has_viewport_property_override = static function ( $property ) use ( $viewport_overrides ) {
		return array_key_exists( $property, $viewport_overrides );
	};
	$layout_styles                  = array();

	if ( 'default' === $layout_type ) {
		if ( $has_block_gap_support && $should_output_block_gap ) {
			if ( is_array( $gap_value ) ) {
				$gap_value = $gap_value['top'] ?? null;
			}
			if ( null !== $gap_value && ! $should_skip_gap_serialization ) {
				// Get spacing CSS variable from preset value if provided.
				if ( is_string( $gap_value ) && str_contains( $gap_value, 'var:preset|spacing|' ) ) {
					$index_to_splice = strrpos( $gap_value, '|' ) + 1;
					$slug            = _wp_to_kebab_case( substr( $gap_value, $index_to_splice ) );
					$gap_value       = "var(--wp--preset--spacing--$slug)";
				}

				array_push(
					$layout_styles,
					array(
						'selector'     => "$selector > *",
						'declarations' => array(
							'margin-block-start' => '0',
							'margin-block-end'   => '0',
						),
					),
					array(
						'selector'     => "$selector > * + *",
						'declarations' => array(
							'margin-block-start' => $gap_value,
							'margin-block-end'   => '0',
						),
					)
				);
			}
		}
	} elseif ( 'constrained' === $layout_type ) {
		$content_size    = $layout_for_styles['contentSize'] ?? '';
		$wide_size       = $layout_for_styles['wideSize'] ?? '';
		$justify_content = $layout_for_styles['justifyContent'] ?? 'center';

		$all_max_width_value  = $content_size ? $content_size : $wide_size;
		$wide_max_width_value = $wide_size ? $wide_size : $content_size;

		// Make sure there is a single CSS rule, and all tags are stripped for security.
		$all_max_width_value  = safecss_filter_attr( explode( ';', $all_max_width_value )[0] );
		$wide_max_width_value = safecss_filter_attr( explode( ';', $wide_max_width_value )[0] );

		$margin_left  = 'left' === $justify_content ? '0 !important' : 'auto !important';
		$margin_right = 'right' === $justify_content ? '0 !important' : 'auto !important';

		$has_justify_content_override    = null !== $viewport_overrides && $has_viewport_property_override( 'justifyContent' );
		$should_output_constrained_sizes = null === $viewport_overrides || $has_viewport_property_override( 'contentSize' ) || $has_viewport_property_override( 'wideSize' );
		if ( $should_output_constrained_sizes && ( $content_size || $wide_size ) ) {
			$content_size_declarations = array(
				'max-width' => $all_max_width_value,
			);

			if ( null === $viewport_overrides || $has_justify_content_override ) {
				$content_size_declarations['margin-left']  = $margin_left;
				$content_size_declarations['margin-right'] = $margin_right;
			}

			array_push(
				$layout_styles,
				array(
					'selector'     => "$selector > :where(:not(.alignleft):not(.alignright):not(.alignfull))",
					'declarations' => $content_size_declarations,
				),
				array(
					'selector'     => "$selector > .alignwide",
					'declarations' => array( 'max-width' => $wide_max_width_value ),
				),
				array(
					'selector'     => "$selector .alignfull",
					'declarations' => array( 'max-width' => 'none' ),
				)
			);
		}

		if ( null === $viewport_overrides && isset( $block_spacing ) ) {
			$block_spacing_values = wp_style_engine_get_styles(
				array(
					'spacing' => $block_spacing,
				)
			);

			/*
			 * Handle negative margins for alignfull children of blocks with custom padding set.
			 * They're added separately because padding might only be set on one side.
			 */
			if ( isset( $block_spacing_values['declarations']['padding-right'] ) ) {
				$padding_right = $block_spacing_values['declarations']['padding-right'];
				// Add unit if 0.
				if ( '0' === $padding_right ) {
					$padding_right = '0px';
				}
				$layout_styles[] = array(
					'selector'     => "$selector > .alignfull",
					'declarations' => array( 'margin-right' => "calc($padding_right * -1)" ),
				);
			}
			if ( isset( $block_spacing_values['declarations']['padding-left'] ) ) {
				$padding_left = $block_spacing_values['declarations']['padding-left'];
				// Add unit if 0.
				if ( '0' === $padding_left ) {
					$padding_left = '0px';
				}
				$layout_styles[] = array(
					'selector'     => "$selector > .alignfull",
					'declarations' => array( 'margin-left' => "calc($padding_left * -1)" ),
				);
			}
		}

		if ( $has_justify_content_override && ! $should_output_constrained_sizes ) {
			$layout_styles[] = array(
				'selector'     => "$selector > :where(:not(.alignleft):not(.alignright):not(.alignfull))",
				'declarations' => array(
					'margin-left'  => $margin_left,
					'margin-right' => $margin_right,
				),
			);
		} elseif ( null === $viewport_overrides ) {
			if ( 'left' === $justify_content ) {
				$layout_styles[] = array(
					'selector'     => "$selector > :where(:not(.alignleft):not(.alignright):not(.alignfull))",
					'declarations' => array( 'margin-left' => '0 !important' ),
				);
			}

			if ( 'right' === $justify_content ) {
				$layout_styles[] = array(
					'selector'     => "$selector > :where(:not(.alignleft):not(.alignright):not(.alignfull))",
					'declarations' => array( 'margin-right' => '0 !important' ),
				);
			}
		}

		if ( $has_block_gap_support && $should_output_block_gap ) {
			if ( is_array( $gap_value ) ) {
				$gap_value = $gap_value['top'] ?? null;
			}
			if ( null !== $gap_value && ! $should_skip_gap_serialization ) {
				// Get spacing CSS variable from preset value if provided.
				if ( is_string( $gap_value ) && str_contains( $gap_value, 'var:preset|spacing|' ) ) {
					$index_to_splice = strrpos( $gap_value, '|' ) + 1;
					$slug            = _wp_to_kebab_case( substr( $gap_value, $index_to_splice ) );
					$gap_value       = "var(--wp--preset--spacing--$slug)";
				}

				array_push(
					$layout_styles,
					array(
						'selector'     => "$selector > *",
						'declarations' => array(
							'margin-block-start' => '0',
							'margin-block-end'   => '0',
						),
					),
					array(
						'selector'     => "$selector > * + *",
						'declarations' => array(
							'margin-block-start' => $gap_value,
							'margin-block-end'   => '0',
						),
					)
				);
			}
		}
	} elseif ( 'flex' === $layout_type ) {
		$layout_orientation = $layout_for_styles['orientation'] ?? 'horizontal';

		$justify_content_options = array(
			'left'   => 'flex-start',
			'right'  => 'flex-end',
			'center' => 'center',
		);

		$vertical_alignment_options = array(
			'top'    => 'flex-start',
			'center' => 'center',
			'bottom' => 'flex-end',
		);

		if ( 'horizontal' === $layout_orientation ) {
			$justify_content_options    += array( 'space-between' => 'space-between' );
			$vertical_alignment_options += array( 'stretch' => 'stretch' );
		} else {
			$justify_content_options    += array( 'stretch' => 'stretch' );
			$vertical_alignment_options += array( 'space-between' => 'space-between' );
		}

		$should_output_flex_wrap          = null === $viewport_overrides || $has_viewport_property_override( 'flexWrap' );
		$should_output_flex_orientation   = null === $viewport_overrides || $has_viewport_property_override( 'orientation' );
		$should_output_flex_justification = null === $viewport_overrides || $has_viewport_property_override( 'justifyContent' ) || $has_viewport_property_override( 'orientation' );
		$should_output_flex_alignment     = null === $viewport_overrides || $has_viewport_property_override( 'verticalAlignment' ) || $has_viewport_property_override( 'orientation' );

		if ( $should_output_flex_wrap && ! empty( $layout_for_styles['flexWrap'] ) && 'nowrap' === $layout_for_styles['flexWrap'] ) {
			$layout_styles[] = array(
				'selector'     => $selector,
				'declarations' => array( 'flex-wrap' => 'nowrap' ),
			);
		}

		if ( $has_block_gap_support && $should_output_block_gap && isset( $gap_value ) ) {
			$combined_gap_value = '';
			$gap_sides          = is_array( $gap_value ) ? array( 'top', 'left' ) : array( 'top' );

			foreach ( $gap_sides as $gap_side ) {
				$process_value = $gap_value;
				if ( is_array( $gap_value ) ) {
					if ( is_array( $fallback_gap_value ) ) {
						$fallback_value = $fallback_gap_value[ $gap_side ] ?? reset( $fallback_gap_value );
					} else {
						$fallback_value = $fallback_gap_value;
					}
					$process_value = $gap_value[ $gap_side ] ?? $fallback_value;
				}
				// Get spacing CSS variable from preset value if provided.
				if ( is_string( $process_value ) && str_contains( $process_value, 'var:preset|spacing|' ) ) {
					$index_to_splice = strrpos( $process_value, '|' ) + 1;
					$slug            = _wp_to_kebab_case( substr( $process_value, $index_to_splice ) );
					$process_value   = "var(--wp--preset--spacing--$slug)";
				}
				$combined_gap_value .= "$process_value ";
			}
			$gap_value = trim( $combined_gap_value );

			if ( null !== $gap_value && ! $should_skip_gap_serialization ) {
				$layout_styles[] = array(
					'selector'     => $selector,
					'declarations' => array( 'gap' => $gap_value ),
				);
			}
		}

		if ( 'horizontal' === $layout_orientation ) {
			/*
			 * Add this style only if is not empty for backwards compatibility,
			 * since we intend to convert blocks that had flex layout implemented
			 * by custom css.
			 */
			if ( $should_output_flex_justification && ! empty( $layout_for_styles['justifyContent'] ) && array_key_exists( $layout_for_styles['justifyContent'], $justify_content_options ) ) {
				$layout_styles[] = array(
					'selector'     => $selector,
					'declarations' => array( 'justify-content' => $justify_content_options[ $layout_for_styles['justifyContent'] ] ),
				);
			}

			if ( $should_output_flex_alignment && ! empty( $layout_for_styles['verticalAlignment'] ) && array_key_exists( $layout_for_styles['verticalAlignment'], $vertical_alignment_options ) ) {
				$layout_styles[] = array(
					'selector'     => $selector,
					'declarations' => array( 'align-items' => $vertical_alignment_options[ $layout_for_styles['verticalAlignment'] ] ),
				);
			}
		} else {
			if ( $should_output_flex_orientation ) {
				$layout_styles[] = array(
					'selector'     => $selector,
					'declarations' => array( 'flex-direction' => 'column' ),
				);
			}
			if ( $should_output_flex_justification && ! empty( $layout_for_styles['justifyContent'] ) && array_key_exists( $layout_for_styles['justifyContent'], $justify_content_options ) ) {
				$layout_styles[] = array(
					'selector'     => $selector,
					'declarations' => array( 'align-items' => $justify_content_options[ $layout_for_styles['justifyContent'] ] ),
				);
			} elseif ( $should_output_flex_justification ) {
				$layout_styles[] = array(
					'selector'     => $selector,
					'declarations' => array( 'align-items' => 'flex-start' ),
				);
			}
			if ( $should_output_flex_alignment && ! empty( $layout_for_styles['verticalAlignment'] ) && array_key_exists( $layout_for_styles['verticalAlignment'], $vertical_alignment_options ) ) {
				$layout_styles[] = array(
					'selector'     => $selector,
					'declarations' => array( 'justify-content' => $vertical_alignment_options[ $layout_for_styles['verticalAlignment'] ] ),
				);
			}
		}
	} elseif ( 'grid' === $layout_type ) {
		/*
		 * If the gap value is an array, we use the "left" value because it represents the vertical gap, which
		 * is the relevant one for computation of responsive grid columns.
		 */
		if ( is_array( $fallback_gap_value ) ) {
			$responsive_gap_value = $fallback_gap_value['left'] ?? reset( $fallback_gap_value );
		} else {
			$responsive_gap_value = $fallback_gap_value;
		}

		if ( $has_block_gap_support && isset( $gap_value ) ) {
			$combined_gap_value = '';
			$gap_sides          = is_array( $gap_value ) ? array( 'top', 'left' ) : array( 'top' );

			foreach ( $gap_sides as $gap_side ) {
				$process_value = $gap_value;
				if ( is_array( $gap_value ) ) {
					if ( is_array( $fallback_gap_value ) ) {
						$fallback_value = $fallback_gap_value[ $gap_side ] ?? reset( $fallback_gap_value );
					} else {
						$fallback_value = $fallback_gap_value;
					}
					$process_value = $gap_value[ $gap_side ] ?? $fallback_value;
				}
				// Get spacing CSS variable from preset value if provided.
				if ( is_string( $process_value ) && str_contains( $process_value, 'var:preset|spacing|' ) ) {
					$index_to_splice = strrpos( $process_value, '|' ) + 1;
					$slug            = _wp_to_kebab_case( substr( $process_value, $index_to_splice ) );
					$process_value   = "var(--wp--preset--spacing--$slug)";
				}
				$combined_gap_value .= "$process_value ";
			}
			$gap_value            = trim( $combined_gap_value );
			$responsive_gap_value = $gap_value;
		}

		// Ensure 0 values have a unit so they work in calc().
		if ( '0' === $responsive_gap_value || 0 === $responsive_gap_value ) {
			$responsive_gap_value = '0px';
		}

		$should_output_grid_columns = null === $viewport_overrides || $has_viewport_property_override( 'minimumColumnWidth' ) || $has_viewport_property_override( 'columnCount' );
		$uses_gap_in_grid_columns   = ! empty( $layout_for_styles['columnCount'] ) && ! empty( $layout_for_styles['minimumColumnWidth'] );
		if ( $has_block_gap_override && $uses_gap_in_grid_columns ) {
			$should_output_grid_columns = true;
		}

		$should_output_grid_rows = ( null === $viewport_overrides || $has_viewport_property_override( 'rowCount' ) ) && ! empty( $layout_for_styles['columnCount'] ) && ! empty( $layout_for_styles['rowCount'] );
		$grid_declarations       = array();

		if ( $should_output_grid_columns && ! empty( $layout_for_styles['columnCount'] ) && ! empty( $layout_for_styles['minimumColumnWidth'] ) ) {
			$max_value                                  = 'max(min(' . $layout_for_styles['minimumColumnWidth'] . ', 100%), (100% - (' . $responsive_gap_value . ' * (' . $layout_for_styles['columnCount'] . ' - 1))) /' . $layout_for_styles['columnCount'] . ')';
			$grid_declarations['grid-template-columns'] = 'repeat(auto-fill, minmax(' . $max_value . ', 1fr))';
		} elseif ( $should_output_grid_columns && ! empty( $layout_for_styles['columnCount'] ) ) {
			$grid_declarations['grid-template-columns'] = 'repeat(' . $layout_for_styles['columnCount'] . ', minmax(0, 1fr))';
		} elseif ( $should_output_grid_columns ) {
			$minimum_column_width                       = ! empty( $layout_for_styles['minimumColumnWidth'] ) ? $layout_for_styles['minimumColumnWidth'] : '12rem';
			$grid_declarations['grid-template-columns'] = 'repeat(auto-fill, minmax(min(' . $minimum_column_width . ', 100%), 1fr))';
		}

		if ( ! empty( $grid_declarations ) ) {
			$base_has_container_type = empty( $base_layout['columnCount'] ) || ( ! empty( $base_layout['columnCount'] ) && ! empty( $base_layout['minimumColumnWidth'] ) );
			if ( empty( $layout_for_styles['columnCount'] ) || ! empty( $layout_for_styles['minimumColumnWidth'] ) ) {
				if ( null === $viewport_overrides || ! $base_has_container_type ) {
					$grid_declarations['container-type'] = 'inline-size';
				}
			}
			$layout_styles[] = array(
				'selector'     => $selector,
				'declarations' => $grid_declarations,
			);
		}

		if ( $should_output_grid_rows ) {
			$layout_styles[] = array(
				'selector'     => $selector,
				'declarations' => array( 'grid-template-rows' => 'repeat(' . $layout_for_styles['rowCount'] . ', minmax(1rem, auto))' ),
			);
		}

		if ( $has_block_gap_support && $should_output_block_gap && null !== $gap_value && ! $should_skip_gap_serialization ) {
			$layout_styles[] = array(
				'selector'     => $selector,
				'declarations' => array( 'gap' => $gap_value ),
			);
		}
	}

	if ( ! empty( $layout_styles ) ) {
		if ( ! empty( $rules_group ) ) {
			foreach ( $layout_styles as $index => $layout_style ) {
				$layout_styles[ $index ]['rules_group'] = $rules_group;
			}
		}

		/*
		 * Add to the style engine store to enqueue and render layout styles.
		 * Return compiled layout styles to retain backwards compatibility.
		 * Since https://github.com/WordPress/gutenberg/pull/42452,
		 * wp_enqueue_block_support_styles is no longer called in this block supports file.
		 */
		return wp_style_engine_get_stylesheet_from_css_rules(
			$layout_styles,
			array(
				'context'  => 'block-supports',
				'prettify' => false,
			)
		);
	}

	return '';
}

/**
 * Renders the layout config to the block wrapper.
 *
 * @since 5.8.0
 * @since 6.3.0 Adds compound class to layout wrapper for global spacing styles.
 * @since 6.3.0 Check for layout support via the `layout` key with fallback to `__experimentalLayout`.
 * @since 6.6.0 Removed duplicate container class from layout styles.
 * @access private
 *
 * @param string $block_content Rendered block content.
 * @param array  $block         Block object.
 * @return string Filtered block content.
 */
function wp_render_layout_support_flag( $block_content, $block ) {
	static $global_styles = null;

	$block_type            = WP_Block_Type_Registry::get_instance()->get_registered( $block['blockName'] );
	$block_supports_layout = block_has_support( $block_type, 'layout', false ) || block_has_support( $block_type, '__experimentalLayout', false );
	$style_attr            = $block['attrs']['style'] ?? array();
	$child_layout          = $style_attr['layout'] ?? null;

	/*
	 * Collect responsive viewport child layout overrides so that a block with
	 * only responsive child layout (no base child layout) is still processed.
	 */
	$viewport_child_layouts = array();
	foreach ( WP_Theme_JSON::RESPONSIVE_BREAKPOINTS as $breakpoint => $media_query ) {
		$viewport_child = wp_get_layout_child_values( $style_attr[ $breakpoint ]['layout'] ?? null );

		if ( ! empty( $viewport_child ) ) {
			$viewport_child_layouts[ $breakpoint ] = array(
				'media_query'  => $media_query,
				'child_layout' => $viewport_child,
			);
		}
	}

	if ( ! $block_supports_layout && ! $child_layout && empty( $viewport_child_layouts ) ) {
		return $block_content;
	}

	$outer_class_names = array();

	// Child layout specific logic.
	if ( $child_layout || ! empty( $viewport_child_layouts ) ) {
		$base_child_layout = wp_get_layout_child_values( $child_layout );
		$parent_layout     = $block['parentLayout'] ?? array();
		/*
		 * Generates a unique class for child block layout styles.
		 *
		 * To ensure consistent class generation across different page renders,
		 * only properties that affect layout styling are used. These properties
		 * come from `$block['attrs']['style']['layout']`, viewport overrides in
		 * `$block['attrs']['style'][$breakpoint]['layout']`, and `$block['parentLayout']`.
		 *
		 * As long as these properties coincide, the generated class will be the same.
		 */
		$container_content_hash_input = array(
			'layout'       => $base_child_layout,
			'parentLayout' => array_intersect_key(
				$parent_layout,
				array_flip( array( 'minimumColumnWidth', 'columnCount' ) )
			),
		);

		foreach ( $viewport_child_layouts as $breakpoint => $viewport_data ) {
			$container_content_hash_input[ $breakpoint ] = $viewport_data['child_layout'];
		}

		$container_content_class = wp_unique_id_from_values(
			$container_content_hash_input,
			'wp-container-content-'
		);

		$child_layout_styles = wp_get_child_layout_style_rules( ".$container_content_class", $base_child_layout, $parent_layout );

		/*
		 * Emit responsive child layout CSS using the same container-content class
		 * so that base and responsive child layout share the exact same selector.
		 */
		foreach ( $viewport_child_layouts as $viewport_data ) {
			$viewport_child_styles = wp_get_child_layout_style_rules(
				".$container_content_class",
				$base_child_layout,
				$parent_layout,
				$viewport_data['child_layout']
			);

			foreach ( $viewport_child_styles as $index => $rule ) {
				$viewport_child_styles[ $index ]['rules_group'] = $viewport_data['media_query'];
			}

			$child_layout_styles = array_merge( $child_layout_styles, $viewport_child_styles );
		}

		/*
		 * Add to the style engine store to enqueue and render layout styles.
		 * Return styles here just to check if any exist.
		 */
		$child_css = wp_style_engine_get_stylesheet_from_css_rules(
			$child_layout_styles,
			array(
				'context'  => 'block-supports',
				'prettify' => false,
			)
		);

		if ( $child_css ) {
			$outer_class_names[] = $container_content_class;
		}
	}

	// Prep the processor for modifying the block output.
	$processor = new WP_HTML_Tag_Processor( $block_content );

	// Having no tags implies there are no tags onto which to add class names.
	if ( ! $processor->next_tag() ) {
		return $block_content;
	}

	/*
	 * A block may not support layout but still be affected by a parent block's layout.
	 *
	 * In these cases add the appropriate class names and then return early; there's
	 * no need to investigate on this block whether additional layout constraints apply.
	 */
	if ( ! $block_supports_layout && ! empty( $outer_class_names ) ) {
		foreach ( $outer_class_names as $class_name ) {
			$processor->add_class( $class_name );
		}
		return $processor->get_updated_html();
	} elseif ( ! $block_supports_layout ) {
		// Ensure layout classnames are not injected if there is no layout support.
		return $block_content;
	}

	$global_settings = wp_get_global_settings();
	$fallback_layout = $block_type->supports['layout']['default'] ?? array();
	if ( empty( $fallback_layout ) ) {
		$fallback_layout = $block_type->supports['__experimentalLayout']['default'] ?? array();
	}
	$used_layout = $block['attrs']['layout'] ?? $fallback_layout;

	$class_names        = array();
	$layout_definitions = wp_get_layout_definitions();

	// Set the correct layout type for blocks using legacy content width.
	if ( isset( $used_layout['inherit'] ) && $used_layout['inherit'] || isset( $used_layout['contentSize'] ) && $used_layout['contentSize'] ) {
		$used_layout['type'] = 'constrained';
	}

	$root_padding_aware_alignments = $global_settings['useRootPaddingAwareAlignments'] ?? false;

	if (
		$root_padding_aware_alignments &&
		isset( $used_layout['type'] ) &&
		'constrained' === $used_layout['type']
	) {
		$class_names[] = 'has-global-padding';
	}

	/*
	 * The following section was added to reintroduce a small set of layout classnames that were
	 * removed in the 5.9 release (https://github.com/WordPress/gutenberg/issues/38719). It is
	 * not intended to provide an extended set of classes to match all block layout attributes
	 * here.
	 */
	if ( ! empty( $block['attrs']['layout']['orientation'] ) ) {
		$class_names[] = 'is-' . sanitize_title( $block['attrs']['layout']['orientation'] );
	}

	if ( ! empty( $block['attrs']['layout']['justifyContent'] ) ) {
		$class_names[] = 'is-content-justification-' . sanitize_title( $block['attrs']['layout']['justifyContent'] );
	}

	if ( ! empty( $block['attrs']['layout']['flexWrap'] ) && 'nowrap' === $block['attrs']['layout']['flexWrap'] ) {
		$class_names[] = 'is-nowrap';
	}

	// Get classname for layout type.
	if ( isset( $used_layout['type'] ) ) {
		$layout_classname = $layout_definitions[ $used_layout['type'] ]['className'] ?? '';
	} else {
		$layout_classname = $layout_definitions['default']['className'] ?? '';
	}

	if ( $layout_classname && is_string( $layout_classname ) ) {
		$class_names[] = sanitize_title( $layout_classname );
	}

	/*
	 * Only generate Layout styles if the theme has not opted-out.
	 * Attribute-based Layout classnames are output in all cases.
	 */
	if ( ! current_theme_supports( 'disable-layout-styles' ) ) {

		$gap_value          = wp_sanitize_block_gap_value( $style_attr['spacing']['blockGap'] ?? null );
		$fallback_gap_value = $block_type->supports['spacing']['blockGap']['__experimentalDefault'] ?? '0.5em';
		$block_spacing      = $style_attr['spacing'] ?? null;

		/*
		 * If a block's block.json skips serialization for spacing or spacing.blockGap,
		 * don't apply the user-defined value to the styles.
		 */
		$should_skip_gap_serialization = wp_should_skip_block_supports_serialization( $block_type, 'spacing', 'blockGap' );

		$block_gap             = $global_settings['spacing']['blockGap'] ?? null;
		$has_block_gap_support = isset( $block_gap );

		// Get default blockGap value from global styles for use in layouts like grid.
		// Check style variation first, then block-specific styles, then fall back to root styles.
		$block_name = $block['blockName'] ?? '';
		if ( null === $global_styles ) {
			$global_styles = wp_get_global_styles();
		}

		// Check if the block has an active style variation with a blockGap value.
		// Only check the registry if the className contains a variation class to avoid unnecessary lookups.
		$variation_block_gap_value = null;
		$block_class_name          = $block['attrs']['className'] ?? '';
		if ( $block_class_name && str_contains( $block_class_name, 'is-style-' ) && $block_name ) {
			$styles_registry   = WP_Block_Styles_Registry::get_instance();
			$registered_styles = $styles_registry->get_registered_styles_for_block( $block_name );
			$variation_name    = wp_get_block_style_variation_name_from_registered_style( $block_class_name, $registered_styles );
			if ( $variation_name ) {
				$variation_block_gap_value = $global_styles['blocks'][ $block_name ]['variations'][ $variation_name ]['spacing']['blockGap'] ?? null;
			}
		}

		$global_block_gap_value = $variation_block_gap_value ?? $global_styles['blocks'][ $block_name ]['spacing']['blockGap'] ?? $global_styles['spacing']['blockGap'] ?? null;

		if ( null !== $global_block_gap_value ) {
			$fallback_gap_value = $global_block_gap_value;
		}

		$container_class_hash_input = array(
			$used_layout,
			$has_block_gap_support,
			$gap_value,
			$should_skip_gap_serialization,
			$fallback_gap_value,
			$block_spacing,
		);

		foreach ( array_keys( WP_Theme_JSON::RESPONSIVE_BREAKPOINTS ) as $breakpoint ) {
			$viewport_style = $style_attr[ $breakpoint ] ?? null;
			if ( ! is_array( $viewport_style ) ) {
				continue;
			}

			$viewport_container_layout = wp_get_layout_container_values( $viewport_style['layout'] ?? null );
			if ( ! empty( $viewport_container_layout ) ) {
				$container_class_hash_input[] = array(
					'breakpoint' => $breakpoint,
					'layout'     => $viewport_container_layout,
				);
			}

			if ( isset( $viewport_style['spacing']['blockGap'] ) ) {
				$container_class_hash_input[] = array(
					'breakpoint' => $breakpoint,
					'blockGap'   => wp_sanitize_block_gap_value( $viewport_style['spacing']['blockGap'] ),
				);
			}
		}

		/*
		 * Generates a unique ID based on all the data required to obtain the
		 * corresponding layout style. Keeps the CSS class names the same
		 * even for different blocks on different places, as long as they have
		 * the same layout definition. Makes the CSS class names stable across
		 * paginations for features like the enhanced pagination of the Query block.
		 */
		$container_class = wp_unique_id_from_values(
			$container_class_hash_input,
			'wp-container-' . sanitize_title( $block['blockName'] ) . '-is-layout-'
		);

		$style = wp_get_layout_style(
			".$container_class",
			$used_layout,
			$has_block_gap_support,
			$gap_value,
			$should_skip_gap_serialization,
			$fallback_gap_value,
			$block_spacing
		);

		/*
		 * Emit responsive container layout styles using the same $container_class
		 * selector as the base layout so they target the inner block wrapper.
		 */
		foreach ( WP_Theme_JSON::RESPONSIVE_BREAKPOINTS as $breakpoint => $media_query ) {
			$viewport_style = $style_attr[ $breakpoint ] ?? null;
			if ( ! is_array( $viewport_style ) ) {
				continue;
			}

			$viewport_container_layout = wp_get_layout_container_values( $viewport_style['layout'] ?? null );
			$has_viewport_layout       = ! empty( $viewport_container_layout );
			$has_viewport_block_gap    = isset( $viewport_style['spacing']['blockGap'] );

			if ( ! $has_viewport_layout && ! $has_viewport_block_gap ) {
				continue;
			}

			$viewport_gap_value = $has_viewport_block_gap
				? wp_sanitize_block_gap_value( $viewport_style['spacing']['blockGap'] )
				: $gap_value;

			$viewport_block_spacing = is_array( $viewport_style['spacing'] ?? null )
				? array_replace( is_array( $block_spacing ) ? $block_spacing : array(), $viewport_style['spacing'] )
				: $block_spacing;

			$viewport_styles = wp_get_layout_style(
				".$container_class",
				$used_layout,
				$has_block_gap_support,
				$viewport_gap_value,
				$should_skip_gap_serialization,
				$fallback_gap_value,
				$viewport_block_spacing,
				array(
					'rules_group'            => $media_query,
					'viewport_overrides'     => $viewport_container_layout,
					'has_block_gap_override' => $has_viewport_block_gap,
				)
			);

			if ( ! empty( $viewport_styles ) && ! in_array( $container_class, $class_names, true ) ) {
				$class_names[] = $container_class;
			}
		}

		// Only add container class and enqueue block support styles if unique styles were generated.
		if ( ! empty( $style ) ) {
			$class_names[] = $container_class;
		}
	}

	// Add combined layout and block classname for global styles to hook onto.
	$split_block_name = explode( '/', $block['blockName'] );
	$full_block_name  = 'core' === $split_block_name[0] ? end( $split_block_name ) : implode( '-', $split_block_name );
	$class_names[]    = 'wp-block-' . $full_block_name . '-' . $layout_classname;

	// Add classes to the outermost HTML tag if necessary.
	if ( ! empty( $outer_class_names ) ) {
		foreach ( $outer_class_names as $outer_class_name ) {
			$processor->add_class( $outer_class_name );
		}
	}

	/**
	 * Attempts to refer to the inner-block wrapping element by its class attribute.
	 *
	 * When examining a block's inner content, if a block has inner blocks, then
	 * the first content item will likely be a text (HTML) chunk immediately
	 * preceding the inner blocks. The last HTML tag in that chunk would then be
	 * an opening tag for an element that wraps the inner blocks.
	 *
	 * There's no reliable way to associate this wrapper in $block_content because
	 * it may have changed during the rendering pipeline (as inner contents is
	 * provided before rendering) and through previous filters. In many cases,
	 * however, the `class` attribute will be a good-enough identifier, so this
	 * code finds the last tag in that chunk and stores the `class` attribute
	 * so that it can be used later when working through the rendered block output
	 * to identify the wrapping element and add the remaining class names to it.
	 *
	 * It's also possible that no inner block wrapper even exists. If that's the
	 * case this code could apply the class names to an invalid element.
	 *
	 * Example:
	 *
	 *     $block['innerBlocks']  = array( $list_item );
	 *     $block['innerContent'] = array( '<ul class="list-wrapper is-unordered">', null, '</ul>' );
	 *
	 *     // After rendering, the initial contents may have been modified by other renderers or filters.
	 *     $block_content = <<<HTML
	 *         <figure>
	 *             <ul class="annotated-list list-wrapper is-unordered">
	 *                 <li>Code</li>
	 *             </ul><figcaption>It's a list!</figcaption>
	 *         </figure>
	 *     HTML;
	 *
	 * Although it is possible that the original block-wrapper classes are changed in $block_content
	 * from how they appear in $block['innerContent'], it's likely that the original class attributes
	 * are still present in the wrapper as they are in this example. Frequently, additional classes
	 * will also be present; rarely should classes be removed.
	 *
	 * @todo Find a better way to match the first inner block. If it's possible to identify where the
	 *       first inner block starts, then it will be possible to find the last tag before it starts
	 *       and then that tag, if an opening tag, can be solidly identified as a wrapping element.
	 *       Can some unique value or class or ID be added to the inner blocks when they process
	 *       so that they can be extracted here safely without guessing? Can the block rendering function
	 *       return information about where the rendered inner blocks start?
	 *
	 * @var string|null
	 */
	$inner_block_wrapper_classes = null;
	$first_chunk                 = $block['innerContent'][0] ?? null;
	if ( is_string( $first_chunk ) && count( $block['innerContent'] ) > 1 ) {
		$first_chunk_processor = new WP_HTML_Tag_Processor( $first_chunk );
		/*
		 * Use a stack to track open elements as tags are visited. Void elements
		 * (those without a matching closing tag) are excluded so they don't
		 * accumulate on the stack. At the end of the chunk, every element still
		 * on the stack is unclosed — meaning its closing tag lives in a later
		 * innerContent entry alongside the inner blocks, which makes it the
		 * inner-block container. Elements that open and close within this chunk
		 * are siblings that precede the inner blocks and should be ignored.
		 * The last unclosed element with a class attribute is the best candidate
		 * for the inner-block wrapper.
		 */
		$tag_stack = array();
		while ( $first_chunk_processor->next_tag( array( 'tag_closers' => 'visit' ) ) ) {
			if ( $first_chunk_processor->is_tag_closer() ) {
				array_pop( $tag_stack );
			} elseif ( ! WP_HTML_Processor::is_void( $first_chunk_processor->get_tag() ) ) {
				$tag_stack[] = $first_chunk_processor->get_attribute( 'class' );
			}
		}
		foreach ( array_reverse( $tag_stack ) as $class_attribute ) {
			if ( is_string( $class_attribute ) && ! empty( $class_attribute ) ) {
				$inner_block_wrapper_classes = $class_attribute;
				break;
			}
		}
	}

	/*
	 * If necessary, advance to what is likely to be an inner block wrapper tag.
	 *
	 * This advances until it finds the first tag containing the original class
	 * attribute from above. If none is found it will scan to the end of the block
	 * and fail to add any class names.
	 *
	 * If there is no block wrapper it won't advance at all, in which case the
	 * class names will be added to the first and outermost tag of the block.
	 * For cases where this outermost tag is the only tag surrounding inner
	 * blocks then the outer wrapper and inner wrapper are the same.
	 */
	do {
		if ( ! $inner_block_wrapper_classes ) {
			break;
		}

		$class_attribute = $processor->get_attribute( 'class' );
		if ( is_string( $class_attribute ) && str_contains( $class_attribute, $inner_block_wrapper_classes ) ) {
			break;
		}
	} while ( $processor->next_tag() );

	// Add the remaining class names.
	foreach ( $class_names as $class_name ) {
		$processor->add_class( $class_name );
	}

	return $processor->get_updated_html();
}

/**
 * Check if the parent block exists and if it has a layout attribute.
 * If it does, add the parent layout to the parsed block
 *
 * @since 6.6.0
 * @access private
 *
 * @param array    $parsed_block The parsed block.
 * @param array    $source_block The source block.
 * @param WP_Block $parent_block The parent block.
 * @return array The parsed block with parent layout attribute if it exists.
 */
function wp_add_parent_layout_to_parsed_block( $parsed_block, $source_block, $parent_block ) {
	if ( $parent_block && isset( $parent_block->parsed_block['attrs']['layout'] ) ) {
		$parsed_block['parentLayout'] = $parent_block->parsed_block['attrs']['layout'];
	}
	return $parsed_block;
}

add_filter( 'render_block_data', 'wp_add_parent_layout_to_parsed_block', 10, 3 );

// Register the block support.
WP_Block_Supports::get_instance()->register(
	'layout',
	array(
		'register_attribute' => 'wp_register_layout_support',
	)
);
add_filter( 'render_block', 'wp_render_layout_support_flag', 10, 2 );

/**
 * For themes without theme.json file, make sure
 * to restore the inner div for the group block
 * to avoid breaking styles relying on that div.
 *
 * @since 5.8.0
 * @since 6.6.1 Removed inner container from Grid variations.
 * @access private
 *
 * @param string $block_content Rendered block content.
 * @param array  $block         Block object.
 * @return string Filtered block content.
 */
function wp_restore_group_inner_container( $block_content, $block ) {
	$tag_name                         = $block['attrs']['tagName'] ?? 'div';
	$group_with_inner_container_regex = sprintf(
		'/(^\s*<%1$s\b[^>]*wp-block-group(\s|")[^>]*>)(\s*<div\b[^>]*wp-block-group__inner-container(\s|")[^>]*>)((.|\S|\s)*)/U',
		preg_quote( $tag_name, '/' )
	);

	if (
		wp_theme_has_theme_json() ||
		1 === preg_match( $group_with_inner_container_regex, $block_content ) ||
		( isset( $block['attrs']['layout']['type'] ) && ( 'flex' === $block['attrs']['layout']['type'] || 'grid' === $block['attrs']['layout']['type'] ) )
	) {
		return $block_content;
	}

	/*
	 * This filter runs after the layout classnames have been added to the block, so they
	 * have to be removed from the outer wrapper and then added to the inner.
	 */
	$layout_classes = array();
	$processor      = new WP_HTML_Tag_Processor( $block_content );

	if ( $processor->next_tag( array( 'class_name' => 'wp-block-group' ) ) ) {
		foreach ( $processor->class_list() as $class_name ) {
			if ( str_contains( $class_name, 'is-layout-' ) ) {
				$layout_classes[] = $class_name;
				$processor->remove_class( $class_name );
			}
		}
	}

	$content_without_layout_classes = $processor->get_updated_html();
	$replace_regex                  = sprintf(
		'/(^\s*<%1$s\b[^>]*wp-block-group[^>]*>)(.*)(<\/%1$s>\s*$)/ms',
		preg_quote( $tag_name, '/' )
	);
	$updated_content                = preg_replace_callback(
		$replace_regex,
		static function ( $matches ) {
			return $matches[1] . '<div class="wp-block-group__inner-container">' . $matches[2] . '</div>' . $matches[3];
		},
		$content_without_layout_classes
	);

	// Add layout classes to inner wrapper.
	if ( ! empty( $layout_classes ) ) {
		$processor = new WP_HTML_Tag_Processor( $updated_content );
		if ( $processor->next_tag( array( 'class_name' => 'wp-block-group__inner-container' ) ) ) {
			foreach ( $layout_classes as $class_name ) {
				$processor->add_class( $class_name );
			}
		}
		$updated_content = $processor->get_updated_html();
	}
	return $updated_content;
}

add_filter( 'render_block_core/group', 'wp_restore_group_inner_container', 10, 2 );

/**
 * For themes without theme.json file, make sure
 * to restore the outer div for the aligned image block
 * to avoid breaking styles relying on that div.
 *
 * @since 6.0.0
 * @access private
 *
 * @param string $block_content Rendered block content.
 * @param  array  $block        Block object.
 * @return string Filtered block content.
 */
function wp_restore_image_outer_container( $block_content, $block ) {
	if ( wp_theme_has_theme_json() ) {
		return $block_content;
	}

	$figure_processor = new WP_HTML_Tag_Processor( $block_content );
	if (
		! $figure_processor->next_tag( 'FIGURE' ) ||
		! $figure_processor->has_class( 'wp-block-image' ) ||
		! (
			$figure_processor->has_class( 'alignleft' ) ||
			$figure_processor->has_class( 'aligncenter' ) ||
			$figure_processor->has_class( 'alignright' )
		)
	) {
		return $block_content;
	}

	/*
	 * The next section of code wraps the existing figure in a new DIV element.
	 * While doing it, it needs to transfer the layout and the additional CSS
	 * class names from the original figure upward to the wrapper.
	 *
	 * Example:
	 *
	 *     // From this…
	 *     <!-- wp:image {"className":"hires"} -->
	 *     <figure class="wp-block-image wide hires">…
	 *
	 *     // To this…
	 *     <div class="wp-block-image hires"><figure class="wide">…
	 */
	$wrapper_processor = new WP_HTML_Tag_Processor( '<div>' );
	$wrapper_processor->next_token();
	$wrapper_processor->set_attribute(
		'class',
		is_string( $block['attrs']['className'] ?? null )
			? "wp-block-image {$block['attrs']['className']}"
			: 'wp-block-image'
	);

	// And remove them from the existing content; it has been transferred upward.
	$figure_processor->remove_class( 'wp-block-image' );
	foreach ( $wrapper_processor->class_list() as $class_name ) {
		$figure_processor->remove_class( $class_name );
	}

	return "{$wrapper_processor->get_updated_html()}{$figure_processor->get_updated_html()}</div>";
}

add_filter( 'render_block_core/image', 'wp_restore_image_outer_container', 10, 2 );
