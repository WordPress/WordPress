<?php
/**
 * Server-side rendering of the `core/pages` block.
 *
 * @package WordPress
 */

/**
 * Build an array with CSS classes and inline styles defining the colors
 * which will be applied to the pages markup in the front-end when it is a descendant of navigation.
 *
 * @since 5.8.0
 *
 * @param  array $attributes Block attributes.
 * @param  array $context    Navigation block context.
 * @return array Colors CSS classes and inline styles.
 */
function gutenberg_block_core_page_list_build_css_colors( $attributes, $context ) {
	$colors = array(
		'css_classes'           => array(),
		'inline_styles'         => '',
		'overlay_css_classes'   => array(),
		'overlay_inline_styles' => '',
	);

	// Text color.
	$has_named_text_color  = array_key_exists( 'textColor', $context );
	$has_picked_text_color = array_key_exists( 'customTextColor', $context );
	$has_custom_text_color = isset( $context['style']['color']['text'] );

	// If has text color.
	if ( $has_custom_text_color || $has_picked_text_color || $has_named_text_color ) {
		// Add has-text-color class.
		$colors['css_classes'][] = 'has-text-color';
	}

	if ( $has_named_text_color ) {
		// Add the color class.
		$colors['css_classes'][] = sprintf( 'has-%s-color', _wp_to_kebab_case( $context['textColor'] ) );
	} elseif ( $has_picked_text_color ) {
		$colors['inline_styles'] .= sprintf( 'color: %s;', $context['customTextColor'] );
	} elseif ( $has_custom_text_color ) {
		// Add the custom color inline style.
		$colors['inline_styles'] .= sprintf( 'color: %s;', $context['style']['color']['text'] );
	}

	// Background color.
	$has_named_background_color  = array_key_exists( 'backgroundColor', $context );
	$has_picked_background_color = array_key_exists( 'customBackgroundColor', $context );
	$has_custom_background_color = isset( $context['style']['color']['background'] );

	// If has background color.
	if ( $has_custom_background_color || $has_picked_background_color || $has_named_background_color ) {
		// Add has-background class.
		$colors['css_classes'][] = 'has-background';
	}

	if ( $has_named_background_color ) {
		// Add the background-color class.
		$colors['css_classes'][] = sprintf( 'has-%s-background-color', _wp_to_kebab_case( $context['backgroundColor'] ) );
	} elseif ( $has_picked_background_color ) {
		$colors['inline_styles'] .= sprintf( 'background-color: %s;', $context['customBackgroundColor'] );
	} elseif ( $has_custom_background_color ) {
		// Add the custom background-color inline style.
		$colors['inline_styles'] .= sprintf( 'background-color: %s;', $context['style']['color']['background'] );
	}

	// Overlay text color.
	$has_named_overlay_text_color  = array_key_exists( 'overlayTextColor', $context );
	$has_picked_overlay_text_color = array_key_exists( 'customOverlayTextColor', $context );

	// If it has a text color.
	if ( $has_named_overlay_text_color || $has_picked_overlay_text_color ) {
		$colors['overlay_css_classes'][] = 'has-text-color';
	}

	// Give overlay colors priority, fall back to Navigation block colors, then global styles.
	if ( $has_named_overlay_text_color ) {
		$colors['overlay_css_classes'][] = sprintf( 'has-%s-color', _wp_to_kebab_case( $context['overlayTextColor'] ) );
	} elseif ( $has_picked_overlay_text_color ) {
		$colors['overlay_inline_styles'] .= sprintf( 'color: %s;', $context['customOverlayTextColor'] );
	}

	// Overlay background colors.
	$has_named_overlay_background_color  = array_key_exists( 'overlayBackgroundColor', $context );
	$has_picked_overlay_background_color = array_key_exists( 'customOverlayBackgroundColor', $context );

	// If has background color.
	if ( $has_named_overlay_background_color || $has_picked_overlay_background_color ) {
		$colors['overlay_css_classes'][] = 'has-background';
	}

	if ( $has_named_overlay_background_color ) {
		$colors['overlay_css_classes'][] = sprintf( 'has-%s-background-color', _wp_to_kebab_case( $context['overlayBackgroundColor'] ) );
	} elseif ( $has_picked_overlay_background_color ) {
		$colors['overlay_inline_styles'] .= sprintf( 'background-color: %s;', $context['customOverlayBackgroundColor'] );
	}

	return $colors;
}

/**
 * Build an array with CSS classes and inline styles defining the font sizes
 * which will be applied to the pages markup in the front-end when it is a descendant of navigation.
 *
 * @since 5.8.0
 *
 * @param  array $context Navigation block context.
 * @return array Font size CSS classes and inline styles.
 */
function gutenberg_block_core_page_list_build_css_font_sizes( $context ) {
	// CSS classes.
	$font_sizes = array(
		'css_classes'   => array(),
		'inline_styles' => '',
	);

	$has_named_font_size  = array_key_exists( 'fontSize', $context );
	$has_custom_font_size = isset( $context['style']['typography']['fontSize'] );

	if ( $has_named_font_size ) {
		// Add the font size class.
		$font_sizes['css_classes'][] = sprintf( 'has-%s-font-size', $context['fontSize'] );
	} elseif ( $has_custom_font_size ) {
		// Add the custom font size inline style.
		$font_sizes['inline_styles'] = sprintf(
			'font-size: %s;',
			gutenberg_get_typography_font_size_value(
				array(
					'size' => $context['style']['typography']['fontSize'],
				)
			)
		);
	}

	return $font_sizes;
}

/**
 * Outputs Page list markup from an array of pages with nested children.
 *
 * @since 5.8.0
 *
 * @param boolean $open_submenus_on_click Whether to open submenus on click instead of hover.
 * @param boolean $show_submenu_icons Whether to show submenu indicator icons.
 * @param boolean $is_navigation_child If block is a child of Navigation block.
 * @param array   $nested_pages The array of nested pages.
 * @param boolean $is_nested Whether the submenu is nested or not.
 * @param array   $active_page_ancestor_ids An array of ancestor ids for active page.
 * @param array   $colors Color information for overlay styles.
 * @param integer $depth The nesting depth.
 *
 * @return string List markup.
 */
function gutenberg_block_core_page_list_render_nested_page_list( $open_submenus_on_click, $show_submenu_icons, $is_navigation_child, $nested_pages, $is_nested, $active_page_ancestor_ids = array(), $colors = array(), $depth = 0 ) {
	if ( empty( $nested_pages ) ) {
		return;
	}
	$front_page_id = (int) get_option( 'page_on_front' );
	$markup        = '';
	foreach ( (array) $nested_pages as $page ) {
		$css_class       = $page['is_active'] ? ' current-menu-item' : '';
		$aria_current    = $page['is_active'] ? ' aria-current="page"' : '';
		$style_attribute = '';

		$css_class .= in_array( $page['page_id'], $active_page_ancestor_ids, true ) ? ' current-menu-ancestor' : '';
		if ( isset( $page['children'] ) ) {
			$css_class .= ' has-child';
		}

		if ( $is_navigation_child ) {
			$css_class .= ' wp-block-navigation-item';

			if ( $open_submenus_on_click ) {
				$css_class .= ' open-on-click';
			} elseif ( $show_submenu_icons ) {
				$css_class .= ' open-on-hover-click';
			}
		}

		$navigation_child_content_class = $is_navigation_child ? ' wp-block-navigation-item__content' : '';

		// If this is the first level of submenus, include the overlay colors.
		if ( ( ( 0 < $depth && ! $is_nested ) || $is_nested ) && isset( $colors['overlay_css_classes'], $colors['overlay_inline_styles'] ) ) {
			$css_class .= ' ' . trim( implode( ' ', $colors['overlay_css_classes'] ) );
			if ( '' !== $colors['overlay_inline_styles'] ) {
				$style_attribute = sprintf( ' style="%s"', esc_attr( $colors['overlay_inline_styles'] ) );
			}
		}

		if ( (int) $page['page_id'] === $front_page_id ) {
			$css_class .= ' menu-item-home';
		}

		$title = wp_kses_post( $page['title'] );
		$title = $title ? $title : __( '(no title)' );

		$aria_label = sprintf(
			/* translators: Accessibility text. %s: Parent page title. */
			__( '%s submenu' ),
			wp_strip_all_tags( $title )
		);

		$markup .= '<li class="wp-block-pages-list__item' . esc_attr( $css_class ) . '"' . $style_attribute . '>';

		if ( isset( $page['children'] ) && $is_navigation_child && $open_submenus_on_click ) {
			$markup .= '<button aria-label="' . esc_attr( $aria_label ) . '" class="' . esc_attr( $navigation_child_content_class ) . ' wp-block-navigation-submenu__toggle" aria-expanded="false">' . esc_html( $title ) .
			'</button><span class="wp-block-page-list__submenu-icon wp-block-navigation__submenu-icon"><svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12" fill="none" aria-hidden="true" focusable="false"><path d="M1.50002 4L6.00002 8L10.5 4" stroke-width="1.5"></path></svg></span>';
		} else {
			$markup .= '<a class="wp-block-pages-list__item__link' . esc_attr( $navigation_child_content_class ) . '" href="' . esc_url( $page['link'] ) . '"' . $aria_current . '>' . $title . '</a>';
		}

		if ( isset( $page['children'] ) ) {
			if ( $is_navigation_child && $show_submenu_icons && ! $open_submenus_on_click ) {
				$markup .= '<button aria-label="' . esc_attr( $aria_label ) . '" class="wp-block-navigation__submenu-icon wp-block-navigation-submenu__toggle" aria-expanded="false">';
				$markup .= '<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12" fill="none" aria-hidden="true" focusable="false"><path d="M1.50002 4L6.00002 8L10.5 4" stroke-width="1.5"></path></svg>';
				$markup .= '</button>';
			}
			$markup .= '<ul class="wp-block-navigation__submenu-container">';
			$markup .= gutenberg_block_core_page_list_render_nested_page_list( $open_submenus_on_click, $show_submenu_icons, $is_navigation_child, $page['children'], $is_nested, $active_page_ancestor_ids, $colors, $depth + 1 );
			$markup .= '</ul>';
		}
		$markup .= '</li>';
	}
	return $markup;
}

/**
 * Outputs nested array of pages
 *
 * @since 5.8.0
 *
 * @param array $current_level The level being iterated through.
 * @param array $children The children grouped by parent post ID.
 *
 * @return array The nested array of pages.
 */
function gutenberg_block_core_page_list_nest_pages( $current_level, $children ) {
	if ( empty( $current_level ) ) {
		return;
	}
	foreach ( (array) $current_level as $key => $current ) {
		if ( isset( $children[ $key ] ) ) {
			$current_level[ $key ]['children'] = gutenberg_block_core_page_list_nest_pages( $children[ $key ], $children );
		}
	}
	return $current_level;
}

/**
 * Renders the `core/page-list` block on server.
 *
 * @since 5.8.0
 *
 * @param array    $attributes The block attributes.
 * @param string   $content    The saved content.
 * @param WP_Block $block      The parsed block.
 *
 * @return string Returns the page list markup.
 */
function gutenberg_render_block_core_page_list( $attributes, $content, $block ) {
	static $block_id = 0;
	++$block_id;

	$parent_page_id = $attributes['parentPageID'];
	$is_nested      = $attributes['isNested'];

	$all_pages = get_pages(
		array(
			'sort_column' => 'menu_order,post_title',
			'order'       => 'asc',
		)
	);

	// If there are no pages, there is nothing to show.
	if ( empty( $all_pages ) ) {
		return;
	}

	$top_level_pages = array();

	$pages_with_children = array();

	$active_page_ancestor_ids = array();

	foreach ( (array) $all_pages as $page ) {
		$is_active = ! empty( $page->ID ) && ( get_queried_object_id() === $page->ID );

		if ( $is_active ) {
			$active_page_ancestor_ids = get_post_ancestors( $page->ID );
		}

		if ( $page->post_parent ) {
			$pages_with_children[ $page->post_parent ][ $page->ID ] = array(
				'page_id'   => $page->ID,
				'title'     => $page->post_title,
				'link'      => get_permalink( $page ),
				'is_active' => $is_active,
			);
		} else {
			$top_level_pages[ $page->ID ] = array(
				'page_id'   => $page->ID,
				'title'     => $page->post_title,
				'link'      => get_permalink( $page ),
				'is_active' => $is_active,
			);

		}
	}

	$colors          = gutenberg_block_core_page_list_build_css_colors( $attributes, $block->context );
	$font_sizes      = gutenberg_block_core_page_list_build_css_font_sizes( $block->context );
	$classes         = array_merge(
		$colors['css_classes'],
		$font_sizes['css_classes']
	);
	$style_attribute = ( $colors['inline_styles'] . $font_sizes['inline_styles'] );
	$css_classes     = trim( implode( ' ', $classes ) );

	$nested_pages = gutenberg_block_core_page_list_nest_pages( $top_level_pages, $pages_with_children );

	if ( 0 !== $parent_page_id ) {
		// If the parent page has no child pages, there is nothing to show.
		if ( ! array_key_exists( $parent_page_id, $pages_with_children ) ) {
			return;
		}

		$nested_pages = gutenberg_block_core_page_list_nest_pages(
			$pages_with_children[ $parent_page_id ],
			$pages_with_children
		);
	}

	$is_navigation_child = array_key_exists( 'showSubmenuIcon', $block->context );

	$open_submenus_on_click = array_key_exists( 'openSubmenusOnClick', $block->context ) ? $block->context['openSubmenusOnClick'] : false;

	$show_submenu_icons = array_key_exists( 'showSubmenuIcon', $block->context ) ? $block->context['showSubmenuIcon'] : false;

	$wrapper_markup = $is_nested ? '%2$s' : '<ul %1$s>%2$s</ul>';

	$items_markup = gutenberg_block_core_page_list_render_nested_page_list( $open_submenus_on_click, $show_submenu_icons, $is_navigation_child, $nested_pages, $is_nested, $active_page_ancestor_ids, $colors );

	$wrapper_attributes = get_block_wrapper_attributes(
		array(
			'class' => $css_classes,
			'style' => $style_attribute,
		)
	);

	return sprintf(
		$wrapper_markup,
		$wrapper_attributes,
		$items_markup
	);
}

/**
 * Registers the `core/pages` block on server.
 *
 * @since 5.8.0
 */
function gutenberg_register_block_core_page_list() {
	register_block_type_from_metadata(
		__DIR__ . '/page-list',
		array(
			'render_callback' => 'gutenberg_render_block_core_page_list',
		)
	);
}
add_action( 'init', 'gutenberg_register_block_core_page_list', 20 );
