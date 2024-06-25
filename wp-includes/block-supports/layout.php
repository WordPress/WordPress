<?php
/**
 * Layout block support flag.
 *
 * @package WordPress
 * @since 5.8.0
 */

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
 * @access private
 *
 * @param string               $selector                      CSS selector.
 * @param array                $layout                        Layout object. The one that is passed has already checked
 *                                                            the existence of default block layout.
 * @param bool                 $has_block_gap_support         Optional. Whether the theme has support for the block gap. Default false.
 * @param string|string[]|null $gap_value                     Optional. The block gap value to apply. Default null.
 * @param bool                 $should_skip_gap_serialization Optional. Whether to skip applying the user-defined value set in the editor. Default false.
 * @param string               $fallback_gap_value            Optional. The block gap value to apply. Default '0.5em'.
 * @param array|null           $block_spacing                 Optional. Custom spacing set on the block. Default null.
 * @return string CSS styles on success. Else, empty string.
 */
function wp_get_layout_style( $selector, $layout, $has_block_gap_support = false, $gap_value = null, $should_skip_gap_serialization = false, $fallback_gap_value = '0.5em', $block_spacing = null ) {
	$layout_type   = isset( $layout['type'] ) ? $layout['type'] : 'default';
	$layout_styles = array();

	if ( 'default' === $layout_type ) {
		if ( $has_block_gap_support ) {
			if ( is_array( $gap_value ) ) {
				$gap_value = isset( $gap_value['top'] ) ? $gap_value['top'] : null;
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
		$content_size    = isset( $layout['contentSize'] ) ? $layout['contentSize'] : '';
		$wide_size       = isset( $layout['wideSize'] ) ? $layout['wideSize'] : '';
		$justify_content = isset( $layout['justifyContent'] ) ? $layout['justifyContent'] : 'center';

		$all_max_width_value  = $content_size ? $content_size : $wide_size;
		$wide_max_width_value = $wide_size ? $wide_size : $content_size;

		// Make sure there is a single CSS rule, and all tags are stripped for security.
		$all_max_width_value  = safecss_filter_attr( explode( ';', $all_max_width_value )[0] );
		$wide_max_width_value = safecss_filter_attr( explode( ';', $wide_max_width_value )[0] );

		$margin_left  = 'left' === $justify_content ? '0 !important' : 'auto !important';
		$margin_right = 'right' === $justify_content ? '0 !important' : 'auto !important';

		if ( $content_size || $wide_size ) {
			array_push(
				$layout_styles,
				array(
					'selector'     => "$selector > :where(:not(.alignleft):not(.alignright):not(.alignfull))",
					'declarations' => array(
						'max-width'    => $all_max_width_value,
						'margin-left'  => $margin_left,
						'margin-right' => $margin_right,
					),
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

		if ( isset( $block_spacing ) ) {
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
				$padding_right   = $block_spacing_values['declarations']['padding-right'];
				$layout_styles[] = array(
					'selector'     => "$selector > .alignfull",
					'declarations' => array( 'margin-right' => "calc($padding_right * -1)" ),
				);
			}
			if ( isset( $block_spacing_values['declarations']['padding-left'] ) ) {
				$padding_left    = $block_spacing_values['declarations']['padding-left'];
				$layout_styles[] = array(
					'selector'     => "$selector > .alignfull",
					'declarations' => array( 'margin-left' => "calc($padding_left * -1)" ),
				);
			}
		}

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

		if ( $has_block_gap_support ) {
			if ( is_array( $gap_value ) ) {
				$gap_value = isset( $gap_value['top'] ) ? $gap_value['top'] : null;
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
		$layout_orientation = isset( $layout['orientation'] ) ? $layout['orientation'] : 'horizontal';

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

		if ( ! empty( $layout['flexWrap'] ) && 'nowrap' === $layout['flexWrap'] ) {
			$layout_styles[] = array(
				'selector'     => $selector,
				'declarations' => array( 'flex-wrap' => 'nowrap' ),
			);
		}

		if ( $has_block_gap_support && isset( $gap_value ) ) {
			$combined_gap_value = '';
			$gap_sides          = is_array( $gap_value ) ? array( 'top', 'left' ) : array( 'top' );

			foreach ( $gap_sides as $gap_side ) {
				$process_value = $gap_value;
				if ( is_array( $gap_value ) ) {
					$process_value = isset( $gap_value[ $gap_side ] ) ? $gap_value[ $gap_side ] : $fallback_gap_value;
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
			if ( ! empty( $layout['justifyContent'] ) && array_key_exists( $layout['justifyContent'], $justify_content_options ) ) {
				$layout_styles[] = array(
					'selector'     => $selector,
					'declarations' => array( 'justify-content' => $justify_content_options[ $layout['justifyContent'] ] ),
				);
			}

			if ( ! empty( $layout['verticalAlignment'] ) && array_key_exists( $layout['verticalAlignment'], $vertical_alignment_options ) ) {
				$layout_styles[] = array(
					'selector'     => $selector,
					'declarations' => array( 'align-items' => $vertical_alignment_options[ $layout['verticalAlignment'] ] ),
				);
			}
		} else {
			$layout_styles[] = array(
				'selector'     => $selector,
				'declarations' => array( 'flex-direction' => 'column' ),
			);
			if ( ! empty( $layout['justifyContent'] ) && array_key_exists( $layout['justifyContent'], $justify_content_options ) ) {
				$layout_styles[] = array(
					'selector'     => $selector,
					'declarations' => array( 'align-items' => $justify_content_options[ $layout['justifyContent'] ] ),
				);
			} else {
				$layout_styles[] = array(
					'selector'     => $selector,
					'declarations' => array( 'align-items' => 'flex-start' ),
				);
			}
			if ( ! empty( $layout['verticalAlignment'] ) && array_key_exists( $layout['verticalAlignment'], $vertical_alignment_options ) ) {
				$layout_styles[] = array(
					'selector'     => $selector,
					'declarations' => array( 'justify-content' => $vertical_alignment_options[ $layout['verticalAlignment'] ] ),
				);
			}
		}
	} elseif ( 'grid' === $layout_type ) {
		if ( ! empty( $layout['columnCount'] ) ) {
			$layout_styles[] = array(
				'selector'     => $selector,
				'declarations' => array( 'grid-template-columns' => 'repeat(' . $layout['columnCount'] . ', minmax(0, 1fr))' ),
			);
		} else {
			$minimum_column_width = ! empty( $layout['minimumColumnWidth'] ) ? $layout['minimumColumnWidth'] : '12rem';

			$layout_styles[] = array(
				'selector'     => $selector,
				'declarations' => array(
					'grid-template-columns' => 'repeat(auto-fill, minmax(min(' . $minimum_column_width . ', 100%), 1fr))',
					'container-type'        => 'inline-size',
				),
			);
		}

		if ( $has_block_gap_support && isset( $gap_value ) ) {
			$combined_gap_value = '';
			$gap_sides          = is_array( $gap_value ) ? array( 'top', 'left' ) : array( 'top' );

			foreach ( $gap_sides as $gap_side ) {
				$process_value = $gap_value;
				if ( is_array( $gap_value ) ) {
					$process_value = isset( $gap_value[ $gap_side ] ) ? $gap_value[ $gap_side ] : $fallback_gap_value;
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
	}

	if ( ! empty( $layout_styles ) ) {
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
	$block_type            = WP_Block_Type_Registry::get_instance()->get_registered( $block['blockName'] );
	$block_supports_layout = block_has_support( $block_type, 'layout', false ) || block_has_support( $block_type, '__experimentalLayout', false );
	$child_layout          = isset( $block['attrs']['style']['layout'] ) ? $block['attrs']['style']['layout'] : null;

	if ( ! $block_supports_layout && ! $child_layout ) {
		return $block_content;
	}

	$outer_class_names = array();

	// Child layout specific logic.
	if ( $child_layout ) {
		$container_content_class   = wp_unique_prefixed_id( 'wp-container-content-' );
		$child_layout_declarations = array();
		$child_layout_styles       = array();

		$self_stretch = isset( $child_layout['selfStretch'] ) ? $child_layout['selfStretch'] : null;

		if ( 'fixed' === $self_stretch && isset( $child_layout['flexSize'] ) ) {
			$child_layout_declarations['flex-basis'] = $child_layout['flexSize'];
			$child_layout_declarations['box-sizing'] = 'border-box';
		} elseif ( 'fill' === $self_stretch ) {
			$child_layout_declarations['flex-grow'] = '1';
		}

		if ( isset( $child_layout['columnSpan'] ) ) {
			$column_span                              = $child_layout['columnSpan'];
			$child_layout_declarations['grid-column'] = "span $column_span";
		}
		if ( isset( $child_layout['rowSpan'] ) ) {
			$row_span                              = $child_layout['rowSpan'];
			$child_layout_declarations['grid-row'] = "span $row_span";
		}
		$child_layout_styles[] = array(
			'selector'     => ".$container_content_class",
			'declarations' => $child_layout_declarations,
		);

		/*
		 * If columnSpan is set, and the parent grid is responsive, i.e. if it has a minimumColumnWidth set,
		 * the columnSpan should be removed on small grids. If there's a minimumColumnWidth, the grid is responsive.
		 * But if the minimumColumnWidth value wasn't changed, it won't be set. In that case, if columnCount doesn't
		 * exist, we can assume that the grid is responsive.
		 */
		if ( isset( $child_layout['columnSpan'] ) && ( isset( $block['parentLayout']['minimumColumnWidth'] ) || ! isset( $block['parentLayout']['columnCount'] ) ) ) {
			$column_span_number  = floatval( $child_layout['columnSpan'] );
			$parent_column_width = isset( $block['parentLayout']['minimumColumnWidth'] ) ? $block['parentLayout']['minimumColumnWidth'] : '12rem';
			$parent_column_value = floatval( $parent_column_width );
			$parent_column_unit  = explode( $parent_column_value, $parent_column_width );

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
			$default_gap_value     = 'px' === $parent_column_unit ? 24 : 1.5;
			$container_query_value = $column_span_number * $parent_column_value + ( $column_span_number - 1 ) * $default_gap_value;
			$container_query_value = $container_query_value . $parent_column_unit;

			$child_layout_styles[] = array(
				'rules_group'  => "@container (max-width: $container_query_value )",
				'selector'     => ".$container_content_class",
				'declarations' => array(
					'grid-column' => '1/-1',
				),
			);
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
	$fallback_layout = isset( $block_type->supports['layout']['default'] )
		? $block_type->supports['layout']['default']
		: array();
	if ( empty( $fallback_layout ) ) {
		$fallback_layout = isset( $block_type->supports['__experimentalLayout']['default'] )
			? $block_type->supports['__experimentalLayout']['default']
			: array();
	}
	$used_layout = isset( $block['attrs']['layout'] ) ? $block['attrs']['layout'] : $fallback_layout;

	$class_names        = array();
	$layout_definitions = wp_get_layout_definitions();

	/*
	 * Uses an incremental ID that is independent per prefix to make sure that
	 * rendering different numbers of blocks doesn't affect the IDs of other
	 * blocks. Makes the CSS class names stable across paginations
	 * for features like the enhanced pagination of the Query block.
	 */
	$container_class = wp_unique_prefixed_id(
		'wp-container-' . sanitize_title( $block['blockName'] ) . '-is-layout-'
	);

	// Set the correct layout type for blocks using legacy content width.
	if ( isset( $used_layout['inherit'] ) && $used_layout['inherit'] || isset( $used_layout['contentSize'] ) && $used_layout['contentSize'] ) {
		$used_layout['type'] = 'constrained';
	}

	$root_padding_aware_alignments = isset( $global_settings['useRootPaddingAwareAlignments'] )
		? $global_settings['useRootPaddingAwareAlignments']
		: false;

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
		$layout_classname = isset( $layout_definitions[ $used_layout['type'] ]['className'] )
			? $layout_definitions[ $used_layout['type'] ]['className']
			: '';
	} else {
		$layout_classname = isset( $layout_definitions['default']['className'] )
			? $layout_definitions['default']['className']
			: '';
	}

	if ( $layout_classname && is_string( $layout_classname ) ) {
		$class_names[] = sanitize_title( $layout_classname );
	}

	/*
	 * Only generate Layout styles if the theme has not opted-out.
	 * Attribute-based Layout classnames are output in all cases.
	 */
	if ( ! current_theme_supports( 'disable-layout-styles' ) ) {

		$gap_value = isset( $block['attrs']['style']['spacing']['blockGap'] )
			? $block['attrs']['style']['spacing']['blockGap']
			: null;
		/*
		 * Skip if gap value contains unsupported characters.
		 * Regex for CSS value borrowed from `safecss_filter_attr`, and used here
		 * to only match against the value, not the CSS attribute.
		 */
		if ( is_array( $gap_value ) ) {
			foreach ( $gap_value as $key => $value ) {
				$gap_value[ $key ] = $value && preg_match( '%[\\\(&=}]|/\*%', $value ) ? null : $value;
			}
		} else {
			$gap_value = $gap_value && preg_match( '%[\\\(&=}]|/\*%', $gap_value ) ? null : $gap_value;
		}

		$fallback_gap_value = isset( $block_type->supports['spacing']['blockGap']['__experimentalDefault'] )
			? $block_type->supports['spacing']['blockGap']['__experimentalDefault']
			: '0.5em';
		$block_spacing      = isset( $block['attrs']['style']['spacing'] )
			? $block['attrs']['style']['spacing']
			: null;

		/*
		 * If a block's block.json skips serialization for spacing or spacing.blockGap,
		 * don't apply the user-defined value to the styles.
		 */
		$should_skip_gap_serialization = wp_should_skip_block_supports_serialization( $block_type, 'spacing', 'blockGap' );

		$block_gap             = isset( $global_settings['spacing']['blockGap'] )
			? $global_settings['spacing']['blockGap']
			: null;
		$has_block_gap_support = isset( $block_gap );

		$style = wp_get_layout_style(
			".$container_class",
			$used_layout,
			$has_block_gap_support,
			$gap_value,
			$should_skip_gap_serialization,
			$fallback_gap_value,
			$block_spacing
		);

		// Only add container class and enqueue block support styles if unique styles were generated.
		if ( ! empty( $style ) ) {
			$class_names[] = $container_class;
		}
	}

	// Add combined layout and block classname for global styles to hook onto.
	$block_name    = explode( '/', $block['blockName'] );
	$class_names[] = 'wp-block-' . end( $block_name ) . '-' . $layout_classname;

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
	$first_chunk                 = isset( $block['innerContent'][0] ) ? $block['innerContent'][0] : null;
	if ( is_string( $first_chunk ) && count( $block['innerContent'] ) > 1 ) {
		$first_chunk_processor = new WP_HTML_Tag_Processor( $first_chunk );
		while ( $first_chunk_processor->next_tag() ) {
			$class_attribute = $first_chunk_processor->get_attribute( 'class' );
			if ( is_string( $class_attribute ) && ! empty( $class_attribute ) ) {
				$inner_block_wrapper_classes = $class_attribute;
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
 * @access private
 *
 * @param string $block_content Rendered block content.
 * @param array  $block         Block object.
 * @return string Filtered block content.
 */
function wp_restore_group_inner_container( $block_content, $block ) {
	$tag_name                         = isset( $block['attrs']['tagName'] ) ? $block['attrs']['tagName'] : 'div';
	$group_with_inner_container_regex = sprintf(
		'/(^\s*<%1$s\b[^>]*wp-block-group(\s|")[^>]*>)(\s*<div\b[^>]*wp-block-group__inner-container(\s|")[^>]*>)((.|\S|\s)*)/U',
		preg_quote( $tag_name, '/' )
	);

	if (
		wp_theme_has_theme_json() ||
		1 === preg_match( $group_with_inner_container_regex, $block_content ) ||
		( isset( $block['attrs']['layout']['type'] ) && 'flex' === $block['attrs']['layout']['type'] )
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
	$image_with_align = "
/# 1) everything up to the class attribute contents
(
	^\s*
	<figure\b
	[^>]*
	\bclass=
	[\"']
)
# 2) the class attribute contents
(
	[^\"']*
	\bwp-block-image\b
	[^\"']*
	\b(?:alignleft|alignright|aligncenter)\b
	[^\"']*
)
# 3) everything after the class attribute contents
(
	[\"']
	[^>]*
	>
	.*
	<\/figure>
)/iUx";

	if (
		wp_theme_has_theme_json() ||
		0 === preg_match( $image_with_align, $block_content, $matches )
	) {
		return $block_content;
	}

	$wrapper_classnames = array( 'wp-block-image' );

	// If the block has a classNames attribute these classnames need to be removed from the content and added back
	// to the new wrapper div also.
	if ( ! empty( $block['attrs']['className'] ) ) {
		$wrapper_classnames = array_merge( $wrapper_classnames, explode( ' ', $block['attrs']['className'] ) );
	}
	$content_classnames          = explode( ' ', $matches[2] );
	$filtered_content_classnames = array_diff( $content_classnames, $wrapper_classnames );

	return '<div class="' . implode( ' ', $wrapper_classnames ) . '">' . $matches[1] . implode( ' ', $filtered_content_classnames ) . $matches[3] . '</div>';
}

add_filter( 'render_block_core/image', 'wp_restore_image_outer_container', 10, 2 );
