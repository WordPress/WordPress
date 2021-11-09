<?php
/**
 * Server-side rendering of the `core/navigation` block.
 *
 * @package gutenberg
 */

/**
 * Build an array with CSS classes and inline styles defining the colors
 * which will be applied to the navigation markup in the front-end.
 *
 * @param  array $attributes Navigation block attributes.
 * @return array Colors CSS classes and inline styles.
 */
function block_core_navigation_build_css_colors( $attributes ) {
	$colors = array(
		'css_classes'   => array(),
		'inline_styles' => '',
	);

	// Text color.
	$has_named_text_color  = array_key_exists( 'textColor', $attributes );
	$has_custom_text_color = array_key_exists( 'customTextColor', $attributes );

	// If has text color.
	if ( $has_custom_text_color || $has_named_text_color ) {
		// Add has-text-color class.
		$colors['css_classes'][] = 'has-text-color';
	}

	if ( $has_named_text_color ) {
		// Add the color class.
		$colors['css_classes'][] = sprintf( 'has-%s-color', $attributes['textColor'] );
	} elseif ( $has_custom_text_color ) {
		// Add the custom color inline style.
		$colors['inline_styles'] .= sprintf( 'color: %s;', $attributes['customTextColor'] );
	}

	// Background color.
	$has_named_background_color  = array_key_exists( 'backgroundColor', $attributes );
	$has_custom_background_color = array_key_exists( 'customBackgroundColor', $attributes );

	// If has background color.
	if ( $has_custom_background_color || $has_named_background_color ) {
		// Add has-background class.
		$colors['css_classes'][] = 'has-background';
	}

	if ( $has_named_background_color ) {
		// Add the background-color class.
		$colors['css_classes'][] = sprintf( 'has-%s-background-color', $attributes['backgroundColor'] );
	} elseif ( $has_custom_background_color ) {
		// Add the custom background-color inline style.
		$colors['inline_styles'] .= sprintf( 'background-color: %s;', $attributes['customBackgroundColor'] );
	}

	return $colors;
}

/**
 * Build an array with CSS classes and inline styles defining the font sizes
 * which will be applied to the navigation markup in the front-end.
 *
 * @param  array $attributes Navigation block attributes.
 * @return array Font size CSS classes and inline styles.
 */
function block_core_navigation_build_css_font_sizes( $attributes ) {
	// CSS classes.
	$font_sizes = array(
		'css_classes'   => array(),
		'inline_styles' => '',
	);

	$has_named_font_size  = array_key_exists( 'fontSize', $attributes );
	$has_custom_font_size = array_key_exists( 'customFontSize', $attributes );

	if ( $has_named_font_size ) {
		// Add the font size class.
		$font_sizes['css_classes'][] = sprintf( 'has-%s-font-size', $attributes['fontSize'] );
	} elseif ( $has_custom_font_size ) {
		// Add the custom font size inline style.
		$font_sizes['inline_styles'] = sprintf( 'font-size: %spx;', $attributes['customFontSize'] );
	}

	return $font_sizes;
}

/**
 * Returns the menu items for a WordPress menu location.
 *
 * @param string $location The menu location.
 * @return array Menu items for the location.
 */
function gutenberg_get_menu_items_at_location( $location ) {
	if ( empty( $location ) ) {
		return;
	}

	// Build menu data. The following approximates the code in
	// `wp_nav_menu()` and `gutenberg_output_block_nav_menu`.

	// Find the location in the list of locations, returning early if the
	// location can't be found.
	$locations = get_nav_menu_locations();
	if ( ! isset( $locations[ $location ] ) ) {
		return;
	}

	// Get the menu from the location, returning early if there is no
	// menu or there was an error.
	$menu = wp_get_nav_menu_object( $locations[ $location ] );
	if ( ! $menu || is_wp_error( $menu ) ) {
		return;
	}

	$menu_items = wp_get_nav_menu_items( $menu->term_id, array( 'update_post_term_cache' => false ) );
	_wp_menu_item_classes_by_context( $menu_items );

	return $menu_items;
}

/**
 * Sorts a standard array of menu items into a nested structure keyed by the
 * id of the parent menu.
 *
 * @param array $menu_items Menu items to sort.
 * @return array An array keyed by the id of the parent menu where each element
 *               is an array of menu items that belong to that parent.
 */
function gutenberg_sort_menu_items_by_parent_id( $menu_items ) {
	$sorted_menu_items = array();
	foreach ( (array) $menu_items as $menu_item ) {
		$sorted_menu_items[ $menu_item->menu_order ] = $menu_item;
	}
	unset( $menu_items, $menu_item );

	$menu_items_by_parent_id = array();
	foreach ( $sorted_menu_items as $menu_item ) {
		$menu_items_by_parent_id[ $menu_item->menu_item_parent ][] = $menu_item;
	}

	return $menu_items_by_parent_id;
}

/**
 * Turns menu item data into a nested array of parsed blocks
 *
 * @param array $menu_items               An array of menu items that represent
 *                                        an individual level of a menu.
 * @param array $menu_items_by_parent_id  An array keyed by the id of the
 *                                        parent menu where each element is an
 *                                        array of menu items that belong to
 *                                        that parent.
 * @return array An array of parsed block data.
 */
function gutenberg_parse_blocks_from_menu_items( $menu_items, $menu_items_by_parent_id ) {
	if ( empty( $menu_items ) ) {
		return array();
	}

	$blocks = array();

	foreach ( $menu_items as $menu_item ) {
		$class_name       = ! empty( $menu_item->classes ) ? implode( ' ', (array) $menu_item->classes ) : null;
		$id               = ( null !== $menu_item->object_id && 'custom' !== $menu_item->object ) ? $menu_item->object_id : null;
		$opens_in_new_tab = null !== $menu_item->target && '_blank' === $menu_item->target;
		$rel              = ( null !== $menu_item->xfn && '' !== $menu_item->xfn ) ? $menu_item->xfn : null;
		$kind             = null !== $menu_item->type ? str_replace( '_', '-', $menu_item->type ) : 'custom';

		$block = array(
			'blockName' => 'core/navigation-link',
			'attrs'     => array(
				'className'     => $class_name,
				'description'   => $menu_item->description,
				'id'            => $id,
				'kind'          => $kind,
				'label'         => $menu_item->title,
				'opensInNewTab' => $opens_in_new_tab,
				'rel'           => $rel,
				'title'         => $menu_item->attr_title,
				'type'          => $menu_item->object,
				'url'           => $menu_item->url,
			),
		);

		$block['innerBlocks'] = gutenberg_parse_blocks_from_menu_items(
			isset( $menu_items_by_parent_id[ $menu_item->ID ] )
					? $menu_items_by_parent_id[ $menu_item->ID ]
					: array(),
			$menu_items_by_parent_id
		);

		$blocks[] = $block;
	}

	return $blocks;
}

/**
 * Returns the top-level submenu SVG chevron icon.
 *
 * @return string
 */
function block_core_navigation_render_submenu_icon() {
	return '<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12" fill="none" role="img" aria-hidden="true" focusable="false"><path d="M1.50002 4L6.00002 8L10.5 4" stroke-width="1.5"></path></svg>';
}

/**
 * Renders the `core/navigation` block on server.
 *
 * @param array $attributes The block attributes.
 * @param array $content The saved content.
 * @param array $block The parsed block.
 *
 * @return string Returns the post content with the legacy widget added.
 */
function render_block_core_navigation( $attributes, $content, $block ) {
	/**
	 * Deprecated:
	 * The rgbTextColor and rgbBackgroundColor attributes
	 * have been deprecated in favor of
	 * customTextColor and customBackgroundColor ones.
	 * Move the values from old attrs to the new ones.
	 */
	if ( isset( $attributes['rgbTextColor'] ) && empty( $attributes['textColor'] ) ) {
		$attributes['customTextColor'] = $attributes['rgbTextColor'];
	}

	if ( isset( $attributes['rgbBackgroundColor'] ) && empty( $attributes['backgroundColor'] ) ) {
		$attributes['customBackgroundColor'] = $attributes['rgbBackgroundColor'];
	}

	unset( $attributes['rgbTextColor'], $attributes['rgbBackgroundColor'] );

	/**
	 * This is for backwards compatibility after `isResponsive` attribute has been removed.
	 */
	$has_old_responsive_attribute = ! empty( $attributes['isResponsive'] ) && $attributes['isResponsive'];
	$is_responsive_menu           = isset( $attributes['overlayMenu'] ) && 'never' !== $attributes['overlayMenu'] || $has_old_responsive_attribute;
	$should_load_view_script      = ! wp_script_is( 'wp-block-navigation-view' ) && ( $is_responsive_menu || $attributes['openSubmenusOnClick'] || $attributes['showSubmenuIcon'] );
	if ( $should_load_view_script ) {
		wp_enqueue_script( 'wp-block-navigation-view' );
	}

	$inner_blocks = $block->inner_blocks;

	// If `__unstableLocation` is defined, create inner blocks from the classic menu assigned to that location.
	if ( empty( $inner_blocks ) && array_key_exists( '__unstableLocation', $attributes ) ) {
		$menu_items = gutenberg_get_menu_items_at_location( $attributes['__unstableLocation'] );
		if ( empty( $menu_items ) ) {
			return '';
		}

		$menu_items_by_parent_id = gutenberg_sort_menu_items_by_parent_id( $menu_items );
		$parsed_blocks           = gutenberg_parse_blocks_from_menu_items( $menu_items_by_parent_id[0], $menu_items_by_parent_id );
		$inner_blocks            = new WP_Block_List( $parsed_blocks, $attributes );
	}

	if ( ! empty( $block->context['navigationArea'] ) ) {
		$area    = $block->context['navigationArea'];
		$mapping = get_option( 'fse_navigation_areas', array() );
		if ( ! empty( $mapping[ $area ] ) ) {
			$attributes['navigationMenuId'] = $mapping[ $area ];
		}
	}

	// Load inner blocks from the navigation post.
	if ( array_key_exists( 'navigationMenuId', $attributes ) ) {
		$navigation_post = get_post( $attributes['navigationMenuId'] );
		if ( ! isset( $navigation_post ) ) {
			return '';
		}

		$parsed_blocks = parse_blocks( $navigation_post->post_content );

		// 'parse_blocks' includes a null block with '\n\n' as the content when
		// it encounters whitespace. This code strips it.
		$compacted_blocks = array_filter(
			$parsed_blocks,
			function( $block ) {
				return isset( $block['blockName'] );
			}
		);

		// TODO - this uses the full navigation block attributes for the
		// context which could be refined.
		$inner_blocks = new WP_Block_List( $compacted_blocks, $attributes );
	}

	if ( empty( $inner_blocks ) ) {
		return '';
	}
	$colors     = block_core_navigation_build_css_colors( $attributes );
	$font_sizes = block_core_navigation_build_css_font_sizes( $attributes );
	$classes    = array_merge(
		$colors['css_classes'],
		$font_sizes['css_classes'],
		$is_responsive_menu ? array( 'is-responsive' ) : array()
	);

	$inner_blocks_html = '';
	$is_list_open      = false;
	foreach ( $inner_blocks as $inner_block ) {
		if ( ( 'core/navigation-link' === $inner_block->name || 'core/home-link' === $inner_block->name || 'core/site-title' === $inner_block->name || 'core/site-logo' === $inner_block->name || 'core/navigation-submenu' === $inner_block->name ) && ! $is_list_open ) {
			$is_list_open       = true;
			$inner_blocks_html .= '<ul class="wp-block-navigation__container">';
		}
		if ( 'core/navigation-link' !== $inner_block->name && 'core/home-link' !== $inner_block->name && 'core/site-title' !== $inner_block->name && 'core/site-logo' !== $inner_block->name && 'core/navigation-submenu' !== $inner_block->name && $is_list_open ) {
			$is_list_open       = false;
			$inner_blocks_html .= '</ul>';
		}
		if ( 'core/site-title' === $inner_block->name || 'core/site-logo' === $inner_block->name ) {
			$inner_blocks_html .= '<li class="wp-block-navigation-item">' . $inner_block->render() . '</li>';
		} else {
			$inner_blocks_html .= $inner_block->render();
		}
	}

	if ( $is_list_open ) {
		$inner_blocks_html .= '</ul>';
	}

	$block_styles = isset( $attributes['styles'] ) ? $attributes['styles'] : '';

	$wrapper_attributes = get_block_wrapper_attributes(
		array(
			'class' => implode( ' ', $classes ),
			'style' => $block_styles . $colors['inline_styles'] . $font_sizes['inline_styles'],
		)
	);

	$modal_unique_id = uniqid();

	// Determine whether or not navigation elements should be wrapped in the markup required to make it responsive,
	// return early if they don't.
	if ( ! $is_responsive_menu ) {
		return sprintf(
			'<nav %1$s>%2$s</nav>',
			$wrapper_attributes,
			$inner_blocks_html
		);
	}

	$is_hidden_by_default = isset( $attributes['overlayMenu'] ) && 'always' === $attributes['overlayMenu'];

	$responsive_container_classes = array(
		'wp-block-navigation__responsive-container',
		$is_hidden_by_default ? 'hidden-by-default' : '',
	);
	$open_button_classes          = array(
		'wp-block-navigation__responsive-container-open',
		$is_hidden_by_default ? 'always-shown' : '',
	);

	$responsive_container_markup = sprintf(
		'<button aria-expanded="false" aria-haspopup="true" aria-label="%3$s" class="%6$s" data-micromodal-trigger="modal-%1$s"><svg width="24" height="24" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" role="img" aria-hidden="true" focusable="false"><rect x="4" y="7.5" width="16" height="1.5" /><rect x="4" y="15" width="16" height="1.5" /></svg></button>
			<div class="%5$s" id="modal-%1$s" aria-hidden="true">
				<div class="wp-block-navigation__responsive-close" tabindex="-1" data-micromodal-close>
					<div class="wp-block-navigation__responsive-dialog" role="dialog" aria-modal="true" aria-labelledby="modal-%1$s-title" >
							<button aria-label="%4$s" data-micromodal-close class="wp-block-navigation__responsive-container-close"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" role="img" aria-hidden="true" focusable="false"><path d="M13 11.8l6.1-6.3-1-1-6.1 6.2-6.1-6.2-1 1 6.1 6.3-6.5 6.7 1 1 6.5-6.6 6.5 6.6 1-1z"></path></svg></button>
						<div class="wp-block-navigation__responsive-container-content" id="modal-%1$s-content">
							%2$s
						</div>
					</div>
				</div>
			</div>',
		$modal_unique_id,
		$inner_blocks_html,
		__( 'Open menu' ), // Open button label.
		__( 'Close menu' ), // Close button label.
		implode( ' ', $responsive_container_classes ),
		implode( ' ', $open_button_classes )
	);

	return sprintf(
		'<nav %1$s>%2$s</nav>',
		$wrapper_attributes,
		$responsive_container_markup
	);
}

/**
 * Register the navigation block.
 *
 * @uses render_block_core_navigation()
 * @throws WP_Error An WP_Error exception parsing the block definition.
 */
function register_block_core_navigation() {
	register_block_type_from_metadata(
		__DIR__ . '/navigation',
		array(
			'render_callback' => 'render_block_core_navigation',
		)
	);
}

add_action( 'init', 'register_block_core_navigation' );

/**
 * Filter that changes the parsed attribute values of navigation blocks contain typographic presets to contain the values directly.
 *
 * @param array $parsed_block The block being rendered.
 * @return array The block being rendered without typographic presets.
 */
function block_core_navigation_typographic_presets_backcompatibility( $parsed_block ) {
	if ( 'core/navigation' === $parsed_block['blockName'] ) {
		$attribute_to_prefix_map = array(
			'fontStyle'      => 'var:preset|font-style|',
			'fontWeight'     => 'var:preset|font-weight|',
			'textDecoration' => 'var:preset|text-decoration|',
			'textTransform'  => 'var:preset|text-transform|',
		);
		foreach ( $attribute_to_prefix_map as $style_attribute => $prefix ) {
			if ( ! empty( $parsed_block['attrs']['style']['typography'][ $style_attribute ] ) ) {
				$prefix_len      = strlen( $prefix );
				$attribute_value = &$parsed_block['attrs']['style']['typography'][ $style_attribute ];
				if ( 0 === strncmp( $attribute_value, $prefix, $prefix_len ) ) {
					$attribute_value = substr( $attribute_value, $prefix_len );
				}
				if ( 'textDecoration' === $style_attribute && 'strikethrough' === $attribute_value ) {
					$attribute_value = 'line-through';
				}
			}
		}
	}
	return $parsed_block;
}

add_filter( 'render_block_data', 'block_core_navigation_typographic_presets_backcompatibility' );
