<?php
/**
 * Server-side rendering of the `core/navigation` block.
 *
 * @package WordPress
 */

/**
 * Returns the submenu visibility value with backward compatibility
 * for the deprecated openSubmenusOnClick attribute.
 *
 * NOTE: Keep this function in sync with getSubmenuVisibility in
 * packages/block-library/src/navigation/utils/get-submenu-visibility.js
 *
 * This function centralizes the migration logic from the boolean
 * openSubmenusOnClick to the new submenuVisibility enum.
 *
 * @since 6.9.0
 *
 * @param array $attributes Block attributes containing submenuVisibility and/or openSubmenusOnClick.
 * @return string The visibility mode: 'hover', 'click', or 'always'.
 */
function block_core_navigation_get_submenu_visibility( $attributes ) {
	$submenu_visibility     = isset( $attributes['submenuVisibility'] ) ? $attributes['submenuVisibility'] : null;
	$open_submenus_on_click = isset( $attributes['openSubmenusOnClick'] ) ? $attributes['openSubmenusOnClick'] : null;

	// If new attribute is set, use it.
	if ( null !== $submenu_visibility ) {
		return $submenu_visibility;
	}

	// Fall back to old attribute for backward compatibility.
	// openSubmenusOnClick: true  -> 'click'
	// openSubmenusOnClick: false -> 'hover'
	// openSubmenusOnClick: null  -> 'hover' (default)
	return ! empty( $open_submenus_on_click ) ? 'click' : 'hover';
}

/**
 * Helper functions used to render the navigation block.
 *
 * @since 6.5.0
 */
class WP_Navigation_Block_Renderer {

	/**
	 * Used to determine whether or not a navigation has submenus.
	 *
	 * @since 6.5.0
	 */
	private static $has_submenus = false;

	/**
	 * Used to determine which blocks need an <li> wrapper.
	 *
	 * @since 6.5.0
	 *
	 * @var array
	 */
	private static $needs_list_item_wrapper = array(
		'core/site-title',
		'core/site-logo',
		'core/social-links',
	);

	/**
	 * Keeps track of all the navigation names that have been seen.
	 *
	 * @since 6.5.0
	 *
	 * @var array
	 */
	private static $seen_menu_names = array();

	/**
	 * Returns whether the navigation overlay experiment is enabled.
	 *
	 * @since 6.5.0
	 *
	 * @return bool Returns whether the navigation overlay experiment is enabled.
	 */
	private static function is_overlay_experiment_enabled() {
		$gutenberg_experiments = get_option( 'gutenberg-experiments' );
		return $gutenberg_experiments && array_key_exists( 'gutenberg-customizable-navigation-overlays', $gutenberg_experiments );
	}

	/**
	 * Returns whether or not this is responsive navigation.
	 *
	 * @since 6.5.0
	 *
	 * @param array $attributes The block attributes.
	 * @return bool Returns whether or not this is responsive navigation.
	 */
	private static function is_responsive( $attributes ) {
		/**
		 * This is for backwards compatibility after the `isResponsive` attribute was been removed.
		 */

		$has_old_responsive_attribute = ! empty( $attributes['isResponsive'] ) && $attributes['isResponsive'];
		return isset( $attributes['overlayMenu'] ) && 'never' !== $attributes['overlayMenu'] || $has_old_responsive_attribute;
	}

	/**
	 * Returns whether or not a navigation has a submenu.
	 *
	 * @since 6.5.0
	 *
	 * @param WP_Block_List $inner_blocks The list of inner blocks.
	 * @return bool Returns whether or not a navigation has a submenu and also sets the member variable.
	 */
	private static function has_submenus( $inner_blocks ) {
		if ( true === static::$has_submenus ) {
			return static::$has_submenus;
		}

		foreach ( $inner_blocks as $inner_block ) {
			// If this is a page list then work out if any of the pages have children.
			if ( 'core/page-list' === $inner_block->name ) {
				$all_pages = get_pages(
					array(
						'sort_column' => 'menu_order,post_title',
						'order'       => 'asc',
					)
				);
				foreach ( (array) $all_pages as $page ) {
					if ( $page->post_parent ) {
						static::$has_submenus = true;
						break;
					}
				}
			}
			// If this is a navigation submenu then we know we have submenus.
			if ( 'core/navigation-submenu' === $inner_block->name ) {
				static::$has_submenus = true;
				break;
			}
		}

		return static::$has_submenus;
	}

	/**
	 * Determine whether the navigation blocks is interactive.
	 *
	 * @since 6.5.0
	 *
	 * @param array         $attributes   The block attributes.
	 * @param WP_Block_List $inner_blocks The list of inner blocks.
	 * @return bool Returns whether or not to load the view script.
	 */
	private static function is_interactive( $attributes, $inner_blocks ) {
		$has_submenus        = static::has_submenus( $inner_blocks );
		$is_responsive_menu  = static::is_responsive( $attributes );
		$computed_visibility = block_core_navigation_get_submenu_visibility( $attributes );
		$open_on_click       = 'click' === $computed_visibility;
		$show_submenu_icon   = ! empty( $attributes['showSubmenuIcon'] );
		return ( $has_submenus && ( $open_on_click || $show_submenu_icon ) ) || $is_responsive_menu;
	}

	/**
	 * Returns whether or not a block needs a list item wrapper.
	 *
	 * @since 6.5.0
	 *
	 * @param WP_Block $block The block.
	 * @return bool Returns whether or not a block needs a list item wrapper.
	 */
	private static function does_block_need_a_list_item_wrapper( $block ) {

		/**
		 * Filter the list of blocks that need a list item wrapper.
		 *
		 * Affords the ability to customize which blocks need a list item wrapper when rendered
		 * within a core/navigation block.
		 * This is useful for blocks that are not list items but should be wrapped in a list
		 * item when used as a child of a navigation block.
		 *
		 * @since 6.5.0
		 *
		 * @param array $needs_list_item_wrapper The list of blocks that need a list item wrapper.
		 */
		$needs_list_item_wrapper = apply_filters( 'block_core_navigation_listable_blocks', static::$needs_list_item_wrapper );

		return in_array( $block->name, $needs_list_item_wrapper, true );
	}

	/**
	 * Returns the markup for a single inner block.
	 *
	 * @since 6.5.0
	 *
	 * @param WP_Block $inner_block The inner block.
	 * @return string Returns the markup for a single inner block.
	 */
	private static function get_markup_for_inner_block( $inner_block ) {
		$inner_block_content = $inner_block->render();
		if ( ! empty( $inner_block_content ) ) {
			if ( static::does_block_need_a_list_item_wrapper( $inner_block ) ) {
				return '<li class="wp-block-navigation-item">' . $inner_block_content . '</li>';
			}
		}

		return $inner_block_content;
	}

	/**
	 * Returns the html for blocks from a template part (without navigation container wrapper).
	 *
	 * @since 6.5.0
	 *
	 * @param WP_Block_List $blocks The list of blocks to render.
	 * @return string Returns the html for the template part blocks.
	 */
	private static function get_template_part_blocks_html( $blocks ) {
		$html = '';
		foreach ( $blocks as $block ) {
			$html .= $block->render();
		}
		return $html;
	}

	/**
	 * Returns the html for the inner blocks of the navigation block.
	 *
	 * @since 6.5.0
	 *
	 * @param array         $attributes   The block attributes.
	 * @param WP_Block_List $inner_blocks The list of inner blocks.
	 * @return string Returns the html for the inner blocks of the navigation block.
	 */
	private static function get_inner_blocks_html( $attributes, $inner_blocks ) {
		$has_submenus   = static::has_submenus( $inner_blocks );
		$is_interactive = static::is_interactive( $attributes, $inner_blocks );

		$style                = static::get_styles( $attributes );
		$class                = static::get_classes( $attributes );
		$container_attributes = get_block_wrapper_attributes(
			array(
				'class' => 'wp-block-navigation__container ' . $class,
				'style' => $style,
			)
		);

		$inner_blocks_html = '';
		$is_list_open      = false;

		foreach ( $inner_blocks as $inner_block ) {
			$inner_block_markup = static::get_markup_for_inner_block( $inner_block );
			$p                  = new WP_HTML_Tag_Processor( $inner_block_markup );
			$is_list_item       = $p->next_tag( 'LI' );

			if ( $is_list_item && ! $is_list_open ) {
				$is_list_open       = true;
				$inner_blocks_html .= sprintf(
					'<ul %1$s>',
					$container_attributes
				);
			}

			if ( ! $is_list_item && $is_list_open ) {
				$is_list_open       = false;
				$inner_blocks_html .= '</ul>';
			}

			$inner_blocks_html .= $inner_block_markup;
		}

		if ( $is_list_open ) {
			$inner_blocks_html .= '</ul>';
		}

		// Add directives to the submenu if needed.
		if ( $has_submenus && $is_interactive ) {
			$tags              = new WP_HTML_Tag_Processor( $inner_blocks_html );
			$inner_blocks_html = block_core_navigation_add_directives_to_submenu( $tags, $attributes );
		}

		return $inner_blocks_html;
	}

	/**
	 * Gets the inner blocks for the navigation block from the navigation post.
	 *
	 * @since 6.5.0
	 *
	 * @param array $attributes The block attributes.
	 * @return WP_Block_List Returns the inner blocks for the navigation block.
	 */
	private static function get_inner_blocks_from_navigation_post( $attributes ) {
		$navigation_post = get_post( $attributes['ref'] );
		if ( ! isset( $navigation_post ) ) {
			return new WP_Block_List( array(), $attributes );
		}

		// Only published posts are valid. If this is changed then a corresponding change
		// must also be implemented in `use-navigation-menu.js`.
		if ( 'publish' === $navigation_post->post_status ) {
			$parsed_blocks = parse_blocks( $navigation_post->post_content );

			// 'parse_blocks' includes a null block with '\n\n' as the content when
			// it encounters whitespace. This code strips it.
			$blocks = block_core_navigation_filter_out_empty_blocks( $parsed_blocks );

			// Re-serialize, and run Block Hooks algorithm to inject hooked blocks.
			// TODO: See if we can move the apply_block_hooks_to_content_from_post_object() call
			// before the parse_blocks() call further above, to avoid the extra serialization/parsing.
			$markup = serialize_blocks( $blocks );
			$markup = apply_block_hooks_to_content_from_post_object( $markup, $navigation_post );
			$blocks = parse_blocks( $markup );

			// TODO - this uses the full navigation block attributes for the
			// context which could be refined.
			return new WP_Block_List( $blocks, $attributes );
		}
	}

	/**
	 * Gets the inner blocks for the navigation block from the fallback.
	 *
	 * @since 6.5.0
	 *
	 * @param array $attributes The block attributes.
	 * @return WP_Block_List Returns the inner blocks for the navigation block.
	 */
	private static function get_inner_blocks_from_fallback( $attributes ) {
		$fallback_blocks = block_core_navigation_get_fallback_blocks();

		// Fallback my have been filtered so do basic test for validity.
		if ( empty( $fallback_blocks ) || ! is_array( $fallback_blocks ) ) {
			return new WP_Block_List( array(), $attributes );
		}

		return new WP_Block_List( $fallback_blocks, $attributes );
	}

	/**
	 * Recursively disables overlay menu for navigation blocks within overlay blocks.
	 * Prevents nested overlays (inception).
	 *
	 * @since 6.5.0
	 *
	 * @param array $blocks Array of parsed block arrays.
	 * @return array Modified blocks with overlayMenu set to 'never' for navigation blocks.
	 */
	private static function disable_overlay_menu_for_nested_navigation_blocks( $blocks ) {
		if ( empty( $blocks ) || ! is_array( $blocks ) ) {
			return $blocks;
		}

		foreach ( $blocks as &$block ) {
			if ( ! isset( $block['blockName'] ) ) {
				continue;
			}

			// If this is a navigation block, disable its overlay menu.
			if ( 'core/navigation' === $block['blockName'] ) {
				if ( ! isset( $block['attrs'] ) ) {
					$block['attrs'] = array();
				}
				$block['attrs']['overlayMenu'] = 'never';
				// Mark this as a nested navigation within an overlay template part
				// so we can handle its rendering differently.
				$block['attrs']['_isWithinOverlayTemplatePart'] = true;
			}

			// Recursively process inner blocks.
			if ( ! empty( $block['innerBlocks'] ) && is_array( $block['innerBlocks'] ) ) {
				$block['innerBlocks'] = static::disable_overlay_menu_for_nested_navigation_blocks( $block['innerBlocks'] );
			}
		}

		return $blocks;
	}

	/**
	 * Gets the inner blocks for the navigation block from an overlay template part.
	 *
	 * @since 6.5.0
	 *
	 * @param string $overlay_template_part_id The overlay template part ID in format "theme//slug".
	 * @param array  $attributes                The block attributes.
	 * @return WP_Block_List Returns the inner blocks for the overlay template part.
	 */
	private static function get_overlay_blocks_from_template_part( $overlay_template_part_id, $attributes ) {
		if ( empty( $overlay_template_part_id ) || ! is_string( $overlay_template_part_id ) ) {
			return new WP_Block_List( array(), $attributes );
		}

		// Parse the template part ID (format: "theme//slug").
		// If it's just a slug, construct the full ID using the current theme.
		$parts = explode( '//', $overlay_template_part_id, 2 );
		if ( count( $parts ) === 2 ) {
			// Already in "theme//slug" format (backward compatibility).
			$theme = $parts[0];
			$slug  = $parts[1];
		} else {
			// Just a slug, use current theme.
			$theme = get_stylesheet();
			$slug  = $overlay_template_part_id;
		}

		// Only query for template parts from the active theme.
		if ( get_stylesheet() !== $theme ) {
			return new WP_Block_List( array(), $attributes );
		}

		// Query for the template part post.
		$template_part_query = new WP_Query(
			array(
				'post_type'           => 'wp_template_part',
				'post_status'         => 'publish',
				'post_name__in'       => array( $slug ),
				'tax_query'           => array(
					array(
						'taxonomy' => 'wp_theme',
						'field'    => 'name',
						'terms'    => $theme,
					),
				),
				'posts_per_page'      => 1,
				'no_found_rows'       => true,
				'lazy_load_term_meta' => false, // Do not lazy load term meta, as template parts only have one term.
			)
		);

		$template_part_post = $template_part_query->have_posts() ? $template_part_query->next_post() : null;

		if ( ! $template_part_post ) {
			// Try to get from theme file if not in database.
			// Construct the full template part ID for get_block_file_template.
			$full_template_part_id = $theme . '//' . $slug;
			$block_template        = get_block_file_template( $full_template_part_id, 'wp_template_part' );
			if ( isset( $block_template->content ) ) {
				$parsed_blocks = parse_blocks( $block_template->content );
				$blocks        = block_core_navigation_filter_out_empty_blocks( $parsed_blocks );
				// Disable overlay menu for any navigation blocks within the overlay to prevent nested overlays.
				$blocks = static::disable_overlay_menu_for_nested_navigation_blocks( $blocks );
				return new WP_Block_List( $blocks, $attributes );
			}
			return new WP_Block_List( array(), $attributes );
		}

		// Get the template part content.
		$block_template = _build_block_template_result_from_post( $template_part_post );
		if ( ! isset( $block_template->content ) ) {
			return new WP_Block_List( array(), $attributes );
		}

		$parsed_blocks = parse_blocks( $block_template->content );

		// 'parse_blocks' includes a null block with '\n\n' as the content when
		// it encounters whitespace. This code strips it.
		$blocks = block_core_navigation_filter_out_empty_blocks( $parsed_blocks );

		// Re-serialize, and run Block Hooks algorithm to inject hooked blocks.
		$markup = serialize_blocks( $blocks );
		$markup = apply_block_hooks_to_content_from_post_object( $markup, $template_part_post );
		$blocks = parse_blocks( $markup );

		// Disable overlay menu for any navigation blocks within the overlay to prevent nested overlays.
		$blocks = static::disable_overlay_menu_for_nested_navigation_blocks( $blocks );

		return new WP_Block_List( $blocks, $attributes );
	}

	/**
	 * Gets the inner blocks for the navigation block.
	 *
	 * @since 6.5.0
	 *
	 * @param array    $attributes The block attributes.
	 * @param WP_Block $block The parsed block.
	 * @return WP_Block_List Returns the inner blocks for the navigation block.
	 */
	private static function get_inner_blocks( $attributes, $block ) {
		$inner_blocks = $block->inner_blocks;

		// Ensure that blocks saved with the legacy ref attribute name (navigationMenuId) continue to render.
		if ( array_key_exists( 'navigationMenuId', $attributes ) ) {
			$attributes['ref'] = $attributes['navigationMenuId'];
		}

		// If:
		// - the gutenberg plugin is active
		// - `__unstableLocation` is defined
		// - we have menu items at the defined location
		// - we don't have a relationship to a `wp_navigation` Post (via `ref`).
		// ...then create inner blocks from the classic menu assigned to that location.
		if (
			defined( 'IS_GUTENBERG_PLUGIN' ) && IS_GUTENBERG_PLUGIN &&
			array_key_exists( '__unstableLocation', $attributes ) &&
			! array_key_exists( 'ref', $attributes ) &&
			! empty( block_core_navigation_get_menu_items_at_location( $attributes['__unstableLocation'] ) )
		) {
			$inner_blocks = block_core_navigation_get_inner_blocks_from_unstable_location( $attributes );
		}

		// Load inner blocks from the navigation post.
		if ( array_key_exists( 'ref', $attributes ) ) {
			$inner_blocks = static::get_inner_blocks_from_navigation_post( $attributes );
		}

		// If there are no inner blocks then fallback to rendering an appropriate fallback.
		if ( empty( $inner_blocks ) ) {
			$inner_blocks = static::get_inner_blocks_from_fallback( $attributes );
		}

		/**
		 * Filter navigation block $inner_blocks.
		 * Allows modification of a navigation block menu items.
		 *
		 * @since 6.1.0
		 *
		 * @param \WP_Block_List $inner_blocks
		 */
		$inner_blocks = apply_filters( 'block_core_navigation_render_inner_blocks', $inner_blocks );

		$post_ids = block_core_navigation_get_post_ids( $inner_blocks );
		if ( $post_ids ) {
			_prime_post_caches( $post_ids, false, false );
		}

		return $inner_blocks;
	}

	/**
	 * Gets the name of the current navigation, if it has one.
	 *
	 * @since 6.5.0
	 *
	 * @param array $attributes The block attributes.
	 * @return string Returns the name of the navigation.
	 */
	private static function get_navigation_name( $attributes ) {

		$navigation_name = $attributes['ariaLabel'] ?? '';

		if ( ! empty( $navigation_name ) ) {
			return $navigation_name;
		}

		// Load the navigation post.
		if ( array_key_exists( 'ref', $attributes ) ) {
			$navigation_post = get_post( $attributes['ref'] );
			if ( ! isset( $navigation_post ) ) {
				return $navigation_name;
			}

			// Only published posts are valid. If this is changed then a corresponding change
			// must also be implemented in `use-navigation-menu.js`.
			if ( 'publish' === $navigation_post->post_status ) {
				return $navigation_post->post_title;
			}
		}

		return $navigation_name;
	}

	/**
	 * Returns the layout class for the navigation block.
	 *
	 * @since 6.5.0
	 *
	 * @param array $attributes The block attributes.
	 * @return string Returns the layout class for the navigation block.
	 */
	private static function get_layout_class( $attributes ) {
		$layout_justification = array(
			'left'          => 'items-justified-left',
			'right'         => 'items-justified-right',
			'center'        => 'items-justified-center',
			'space-between' => 'items-justified-space-between',
		);

		$layout_class = '';
		if (
			isset( $attributes['layout']['justifyContent'] ) &&
			isset( $layout_justification[ $attributes['layout']['justifyContent'] ] )
		) {
			$layout_class .= $layout_justification[ $attributes['layout']['justifyContent'] ];
		}
		if ( isset( $attributes['layout']['orientation'] ) && 'vertical' === $attributes['layout']['orientation'] ) {
			$layout_class .= ' is-vertical';
		}

		if ( isset( $attributes['layout']['flexWrap'] ) && 'nowrap' === $attributes['layout']['flexWrap'] ) {
			$layout_class .= ' no-wrap';
		}
		return $layout_class;
	}

	/**
	 * Return classes for the navigation block.
	 *
	 * @since 6.5.0
	 *
	 * @param array $attributes The block attributes.
	 * @return string Returns the classes for the navigation block.
	 */
	private static function get_classes( $attributes ) {
		// Restore legacy classnames for submenu positioning.
		$layout_class       = static::get_layout_class( $attributes );
		$colors             = block_core_navigation_build_css_colors( $attributes );
		$font_sizes         = block_core_navigation_build_css_font_sizes( $attributes );
		$is_responsive_menu = static::is_responsive( $attributes );

		// Manually add block support text decoration as CSS class.
		$text_decoration       = $attributes['style']['typography']['textDecoration'] ?? null;
		$text_decoration_class = sprintf( 'has-text-decoration-%s', $text_decoration );

		$classes = array_merge(
			$colors['css_classes'],
			$font_sizes['css_classes'],
			$is_responsive_menu ? array( 'is-responsive' ) : array(),
			$layout_class ? array( $layout_class ) : array(),
			$text_decoration ? array( $text_decoration_class ) : array()
		);
		return implode( ' ', $classes );
	}

	/**
	 * Get styles for the navigation block.
	 *
	 * @since 6.5.0
	 *
	 * @param array $attributes The block attributes.
	 * @return string Returns the styles for the navigation block.
	 */
	private static function get_styles( $attributes ) {
		$colors       = block_core_navigation_build_css_colors( $attributes );
		$font_sizes   = block_core_navigation_build_css_font_sizes( $attributes );
		$block_styles = $attributes['styles'] ?? '';
		return $block_styles . $colors['inline_styles'] . $font_sizes['inline_styles'];
	}

	/**
	 * Get responsive container classes for the navigation block.
	 *
	 * @since 7.0.0
	 *
	 * @param bool  $is_hidden_by_default Whether the responsive menu is hidden by default.
	 * @param bool  $has_custom_overlay Whether a custom overlay is used.
	 * @param array $colors The colors array.
	 * @return array Returns the responsive container classes.
	 */
	private static function get_responsive_container_classes( $is_hidden_by_default, $has_custom_overlay, $colors ) {
		$responsive_container_classes = array( 'wp-block-navigation__responsive-container' );

		if ( $is_hidden_by_default ) {
			$responsive_container_classes[] = 'hidden-by-default';
		}

		if ( $has_custom_overlay ) {
			// Only add the disable-default-overlay class if experiment is enabled AND overlay blocks actually rendered.
			$responsive_container_classes[] = 'disable-default-overlay';
		} else {
			// Don't apply overlay color classes if using a custom overlay template part.
			// The custom overlay is responsible for its own styling.
			$responsive_container_classes[] = implode( ' ', $colors['overlay_css_classes'] );
		}

		return $responsive_container_classes;
	}

	/**
	 * Get overlay inline styles for the navigation block.
	 *
	 * @since 7.0.0
	 *
	 * @param array $colors The colors array.
	 * @return string Returns the overlay inline styles.
	 */
	private static function get_overlay_inline_styles( $has_custom_overlay, $colors ) {
		$overlay_inline_styles = $has_custom_overlay ? '' : esc_attr( safecss_filter_attr( $colors['overlay_inline_styles'] ) );
		return ( ! empty( $overlay_inline_styles ) ) ? "style=\"$overlay_inline_styles\"" : '';
	}

	/**
	 * Get the responsive container markup
	 *
	 * @since 6.5.0
	 *
	 * @param array         $attributes The block attributes.
	 * @param WP_Block_List $inner_blocks The list of inner blocks.
	 * @param string        $inner_blocks_html The markup for the inner blocks.
	 * @return string Returns the container markup.
	 */
	private static function get_responsive_container_markup( $attributes, $inner_blocks, $inner_blocks_html ) {
		$is_interactive  = static::is_interactive( $attributes, $inner_blocks );
		$colors          = block_core_navigation_build_css_colors( $attributes );
		$modal_unique_id = wp_unique_id( 'modal-' );

		$is_hidden_by_default = isset( $attributes['overlayMenu'] ) && 'always' === $attributes['overlayMenu'];

		// Set-up variables for the custom overlay experiment.
		// Values are set to "off" so they don't affect the default behavior.
		$is_overlay_experiment_enabled  = static::is_overlay_experiment_enabled();
		$has_custom_overlay             = false;
		$close_button_markup            = '';
		$has_custom_overlay_close_block = false;
		$overlay_blocks_html            = '';
		$custom_overlay_markup          = '';

		if ( $is_overlay_experiment_enabled ) {
			// Check if an overlay template part is selected and render it.
			// This needs to happen before building classes so we know if overlay blocks actually exist.
			if ( ! empty( $attributes['overlay'] ) ) {
				// Get blocks from the overlay template part.
				$overlay_blocks = static::get_overlay_blocks_from_template_part( $attributes['overlay'], $attributes );
				// Check if overlay contains a navigation-overlay-close block.
				$has_custom_overlay_close_block = block_core_navigation_block_tree_has_block_type(
					$overlay_blocks,
					'core/navigation-overlay-close',
					array( 'core/navigation' ) // Skip navigation blocks, as they cannot contain an overlay close block
				);
				// Render template part blocks directly without navigation container wrapper.
				$overlay_blocks_html = static::get_template_part_blocks_html( $overlay_blocks );
				// Add Interactivity API directives to the overlay close block if present.
				if ( $has_custom_overlay_close_block && $is_interactive ) {
					$tags                = new WP_HTML_Tag_Processor( $overlay_blocks_html );
					$overlay_blocks_html = block_core_navigation_add_directives_to_overlay_close( $tags );
				}
			}

			$has_custom_overlay = ! empty( $overlay_blocks_html );
		}

		$responsive_container_classes = static::get_responsive_container_classes( $is_hidden_by_default, $has_custom_overlay, $colors );

		$open_button_classes = array(
			'wp-block-navigation__responsive-container-open',
			$is_hidden_by_default ? 'always-shown' : '',
		);

		$should_display_icon_label = isset( $attributes['hasIcon'] ) && true === $attributes['hasIcon'];
		$toggle_button_icon        = '<svg width="24" height="24" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M4 7.5h16v1.5H4z"></path><path d="M4 15h16v1.5H4z"></path></svg>';
		if ( isset( $attributes['icon'] ) ) {
			if ( 'menu' === $attributes['icon'] ) {
				$toggle_button_icon = '<svg width="24" height="24" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M5 5v1.5h14V5H5z"></path><path d="M5 12.8h14v-1.5H5v1.5z"></path><path d="M5 19h14v-1.5H5V19z"></path></svg>';
			}
		}
		$toggle_button_content       = $should_display_icon_label ? $toggle_button_icon : __( 'Menu' );
		$toggle_close_button_icon    = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" aria-hidden="true" focusable="false"><path d="m13.06 12 6.47-6.47-1.06-1.06L12 10.94 5.53 4.47 4.47 5.53 10.94 12l-6.47 6.47 1.06 1.06L12 13.06l6.47 6.47 1.06-1.06L13.06 12Z"></path></svg>';
		$toggle_close_button_content = $should_display_icon_label ? $toggle_close_button_icon : __( 'Close' );
		$toggle_aria_label_open      = $should_display_icon_label ? 'aria-label="' . __( 'Open menu' ) . '"' : ''; // Open button label.
		$toggle_aria_label_close     = $should_display_icon_label ? 'aria-label="' . __( 'Close menu' ) . '"' : ''; // Close button label.

		// Add Interactivity API directives to the markup if needed.
		$open_button_directives          = '';
		$responsive_container_directives = '';
		$responsive_dialog_directives    = '';
		$close_button_directives         = '';
		if ( $is_interactive ) {
			$open_button_directives                  = '
				data-wp-on--click="actions.openMenuOnClick"
				data-wp-on--keydown="actions.handleMenuKeydown"
			';
			$responsive_container_directives         = '
				data-wp-class--has-modal-open="state.isMenuOpen"
				data-wp-class--is-menu-open="state.isMenuOpen"
				data-wp-watch="callbacks.initMenu"
				data-wp-on--keydown="actions.handleMenuKeydown"
				data-wp-on--focusout="actions.handleMenuFocusout"
				tabindex="-1"
			';
			$responsive_dialog_directives            = '
				data-wp-bind--aria-modal="state.ariaModal"
				data-wp-bind--aria-label="state.ariaLabel"
				data-wp-bind--role="state.roleAttribute"
			';
			$close_button_directives                 = '
				data-wp-on--click="actions.closeMenuOnClick"
			';
			$responsive_container_content_directives = '
				data-wp-watch="callbacks.focusFirstElement"
			';
		}

		// Don't apply overlay inline styles if using a custom overlay template part.
		// The custom overlay is responsible for its own styling.
		$overlay_inline_styles = static::get_overlay_inline_styles( $has_custom_overlay, $colors );

		if ( $has_custom_overlay ) {
			$custom_overlay_markup = sprintf(
				'<div class="wp-block-navigation__overlay-container">%s</div>',
				$overlay_blocks_html
			);
		}

		// Show default close button for all responsive navigation,
		// unless custom overlay has its own close block.
		if ( ! $has_custom_overlay_close_block ) {
			$close_button_markup = sprintf(
				'<button %1$s class="wp-block-navigation__responsive-container-close" %2$s>%3$s</button>',
				$toggle_aria_label_close,
				$close_button_directives,
				$toggle_close_button_content
			);
		}

		return sprintf(
			'<button aria-haspopup="dialog" %3$s class="%6$s" %10$s>%8$s</button>
				<div class="%5$s" %7$s id="%1$s" %11$s>
					<div class="wp-block-navigation__responsive-close" tabindex="-1">
						<div class="wp-block-navigation__responsive-dialog" %12$s>
							%13$s
							<div class="wp-block-navigation__responsive-container-content" %14$s id="%1$s-content">
								%2$s
								%15$s
							</div>
						</div>
					</div>
				</div>',
			esc_attr( $modal_unique_id ),
			$inner_blocks_html,
			$toggle_aria_label_open,
			$toggle_aria_label_close,
			esc_attr( trim( implode( ' ', $responsive_container_classes ) ) ),
			esc_attr( trim( implode( ' ', $open_button_classes ) ) ),
			$overlay_inline_styles,
			$toggle_button_content,
			$toggle_close_button_content,
			$open_button_directives,
			$responsive_container_directives,
			$responsive_dialog_directives,
			$close_button_markup,
			$responsive_container_content_directives,
			$has_custom_overlay ? $custom_overlay_markup : ''
		);
	}

	/**
	 * Get the wrapper attributes
	 *
	 * @since 6.5.0
	 *
	 * @param array         $attributes    The block attributes.
	 * @param WP_Block_List $inner_blocks  A list of inner blocks.
	 * @return string Returns the navigation block markup.
	 */
	private static function get_nav_attributes( $attributes, $inner_blocks ) {
		$is_interactive     = static::is_interactive( $attributes, $inner_blocks );
		$is_responsive_menu = static::is_responsive( $attributes );
		$style              = static::get_styles( $attributes );
		$class              = static::get_classes( $attributes );
		$extra_attributes   = array(
			'class' => $class,
			'style' => $style,
		);
		// Only add aria-label for top-level navigation blocks.
		// Skip navigation blocks marked as being within overlay template parts.
		$is_within_overlay = $attributes['_isWithinOverlayTemplatePart'] ?? false;
		if ( $is_within_overlay ) {
			$nav_menu_name = static::get_navigation_name( $attributes );
		} else {
			$nav_menu_name = static::get_unique_navigation_name( $attributes );
		}

		if ( ! empty( $nav_menu_name ) ) {
			$extra_attributes['aria-label'] = $nav_menu_name;
		}
		$wrapper_attributes = get_block_wrapper_attributes( $extra_attributes );

		if ( $is_responsive_menu ) {
			$nav_element_directives = static::get_nav_element_directives( $is_interactive );
			$wrapper_attributes    .= ' ' . $nav_element_directives;
		}

		return $wrapper_attributes;
	}

	/**
	 * Gets the nav element directives.
	 *
	 * @since 6.5.0
	 *
	 * @param bool $is_interactive Whether the block is interactive.
	 * @return string the directives for the navigation element.
	 */
	private static function get_nav_element_directives( $is_interactive ) {
		if ( ! $is_interactive ) {
			return '';
		}
		// When adding to this array be mindful of security concerns.
		$nav_element_context    = wp_interactivity_data_wp_context(
			array(
				'overlayOpenedBy' => array(
					'click' => false,
					'hover' => false,
					'focus' => false,
				),
				'type'            => 'overlay',
				'roleAttribute'   => '',
				'ariaLabel'       => __( 'Menu' ),
			)
		);
		$nav_element_directives = '
		 data-wp-interactive="core/navigation" '
		. $nav_element_context;

		return $nav_element_directives;
	}

	/**
	 * Handle view script module loading.
	 *
	 * @since 6.5.0
	 *
	 * @param array         $attributes   The block attributes.
	 * @param WP_Block      $block        The parsed block.
	 * @param WP_Block_List $inner_blocks The list of inner blocks.
	 */
	private static function handle_view_script_module_loading( $attributes, $block, $inner_blocks ) {
		if ( static::is_interactive( $attributes, $inner_blocks ) ) {
			wp_enqueue_script_module( '@wordpress/block-library/navigation/view' );
		}
	}

	/**
	 * Returns the markup for the navigation block.
	 *
	 * @since 6.5.0
	 *
	 * @param array         $attributes The block attributes.
	 * @param WP_Block_List $inner_blocks The list of inner blocks.
	 * @return string Returns the navigation wrapper markup.
	 */
	private static function get_inner_block_markup( $attributes, $inner_blocks ) {
		$inner_blocks_html = static::get_inner_blocks_html( $attributes, $inner_blocks );
		if ( static::is_responsive( $attributes ) ) {
			return static::get_responsive_container_markup( $attributes, $inner_blocks, $inner_blocks_html );
		}
		return $inner_blocks_html;
	}

	/**
	 * Returns a unique name for the navigation.
	 *
	 * @since 6.5.0
	 *
	 * @param array $attributes The block attributes.
	 * @return string Returns a unique name for the navigation.
	 */
	private static function get_unique_navigation_name( $attributes ) {
		$nav_menu_name = static::get_navigation_name( $attributes );

		// This is used to count the number of times a navigation name has been seen,
		// so that we can ensure every navigation has a unique id.
		if ( isset( static::$seen_menu_names[ $nav_menu_name ] ) ) {
			++static::$seen_menu_names[ $nav_menu_name ];
		} else {
			static::$seen_menu_names[ $nav_menu_name ] = 1;
		}

		// If the menu name has been used previously then append an ID
		// to the name to ensure uniqueness across a given post.
		if ( isset( static::$seen_menu_names[ $nav_menu_name ] ) && static::$seen_menu_names[ $nav_menu_name ] > 1 ) {
			$count         = static::$seen_menu_names[ $nav_menu_name ];
			$nav_menu_name = $nav_menu_name . ' ' . ( $count );
		}

		return $nav_menu_name;
	}

	/**
	 * Renders the navigation block.
	 *
	 * @since 6.5.0
	 *
	 * @param array    $attributes The block attributes.
	 * @param string   $content    The saved content.
	 * @param WP_Block $block      The parsed block.
	 * @return string Returns the navigation block markup.
	 */
	public static function render( $attributes, $content, $block ) {
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

		$inner_blocks = static::get_inner_blocks( $attributes, $block );
		// Prevent navigation blocks referencing themselves from rendering.
		if ( block_core_navigation_block_tree_has_block_type(
			$inner_blocks,
			'core/navigation'
		) ) {
			return '';
		}

		static::handle_view_script_module_loading( $attributes, $block, $inner_blocks );

		// Use div wrapper if this navigation block is within an overlay template part.
		$is_within_overlay = $attributes['_isWithinOverlayTemplatePart'] ?? false;
		$tag_name          = $is_within_overlay ? 'div' : 'nav';

		return sprintf(
			'<%1$s %2$s>%3$s</%1$s>',
			$tag_name,
			static::get_nav_attributes( $attributes, $inner_blocks ),
			static::get_inner_block_markup( $attributes, $inner_blocks )
		);
	}
}

// These functions are used for the __unstableLocation feature and only active
// when the gutenberg plugin is active.
if ( defined( 'IS_GUTENBERG_PLUGIN' ) && IS_GUTENBERG_PLUGIN ) {
	/**
	 * Returns the menu items for a WordPress menu location.
	 *
	 * @since 5.9.0
	 *
	 * @param string $location The menu location.
	 * @return array Menu items for the location.
	 */
	function block_core_navigation_get_menu_items_at_location( $location ) {
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
	 * @since 5.9.0
	 *
	 * @param array $menu_items Menu items to sort.
	 * @return array An array keyed by the id of the parent menu where each element
	 *               is an array of menu items that belong to that parent.
	 */
	function block_core_navigation_sort_menu_items_by_parent_id( $menu_items ) {
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
	 * Gets the inner blocks for the navigation block from the unstable location attribute.
	 *
	 * @since 6.5.0
	 *
	 * @param array $attributes The block attributes.
	 * @return WP_Block_List Returns the inner blocks for the navigation block.
	 */
	function block_core_navigation_get_inner_blocks_from_unstable_location( $attributes ) {
		$menu_items = block_core_navigation_get_menu_items_at_location( $attributes['__unstableLocation'] );
		if ( empty( $menu_items ) ) {
			return new WP_Block_List( array(), $attributes );
		}

		$menu_items_by_parent_id = block_core_navigation_sort_menu_items_by_parent_id( $menu_items );
		$parsed_blocks           = block_core_navigation_parse_blocks_from_menu_items( $menu_items_by_parent_id[0], $menu_items_by_parent_id );
		return new WP_Block_List( $parsed_blocks, $attributes );
	}
}

/**
 * Add Interactivity API directives to the navigation-overlay-close block
 * markup using the Tag Processor.
 *
 * @since 6.5.0
 *
 * @param WP_HTML_Tag_Processor $tags Markup of the navigation block.
 * @return string Overlay close markup with the directives injected.
 */
function block_core_navigation_add_directives_to_overlay_close( $tags ) {
	// Find the navigation-overlay-close button.
	if ( $tags->next_tag(
		array(
			'tag_name'   => 'BUTTON',
			'class_name' => 'wp-block-navigation-overlay-close',
		)
	) ) {
		// Add the same close directive as the default close button.
		$tags->set_attribute( 'data-wp-on--click', 'actions.closeMenuOnClick' );
	}
	return $tags->get_updated_html();
}

/**
 * Add Interactivity API directives to the navigation-submenu and page-list
 * blocks markup using the Tag Processor.
 *
 * @since 6.3.0
 *
 * @param WP_HTML_Tag_Processor $tags             Markup of the navigation block.
 * @param array                 $block_attributes Block attributes.
 *
 * @return string Submenu markup with the directives injected.
 */
function block_core_navigation_add_directives_to_submenu( $tags, $block_attributes ) {
	while ( $tags->next_tag(
		array(
			'tag_name'   => 'LI',
			'class_name' => 'has-child',
		)
	) ) {
		// Add directives to the parent `<li>`.
		$tags->set_attribute( 'data-wp-interactive', 'core/navigation' );
		$tags->set_attribute( 'data-wp-context', '{ "submenuOpenedBy": { "click": false, "hover": false, "focus": false }, "type": "submenu", "modal": null, "previousFocus": null }' );
		$tags->set_attribute( 'data-wp-watch', 'callbacks.initMenu' );
		$tags->set_attribute( 'data-wp-on--focusout', 'actions.handleMenuFocusout' );
		$tags->set_attribute( 'data-wp-on--keydown', 'actions.handleMenuKeydown' );

		// This is a fix for Safari. Without it, Safari doesn't change the active
		// element when the user clicks on a button. It can be removed once we add
		// an overlay to capture the clicks, instead of relying on the focusout
		// event.
		$tags->set_attribute( 'tabindex', '-1' );

		$computed_visibility = block_core_navigation_get_submenu_visibility( $block_attributes );
		$open_on_hover       = 'hover' === $computed_visibility;

		if ( $open_on_hover ) {
			$tags->set_attribute( 'data-wp-on--mouseenter', 'actions.openMenuOnHover' );
			$tags->set_attribute( 'data-wp-on--mouseleave', 'actions.closeMenuOnHover' );
		}

		// Add directives to the toggle submenu button.
		if ( $tags->next_tag(
			array(
				'tag_name'   => 'BUTTON',
				'class_name' => 'wp-block-navigation-submenu__toggle',
			)
		) ) {
			$tags->set_attribute( 'data-wp-on--click', 'actions.toggleMenuOnClick' );
			$tags->set_attribute( 'data-wp-bind--aria-expanded', 'state.isMenuOpen' );
			// The `aria-expanded` attribute for SSR is already added in the submenu block.
		}
		// Add directives to the submenu.
		if ( $tags->next_tag(
			array(
				'tag_name'   => 'UL',
				'class_name' => 'wp-block-navigation__submenu-container',
			)
		) ) {
			$tags->set_attribute( 'data-wp-on--focus', 'actions.openMenuOnFocus' );
		}

		// Iterate through subitems if exist.
		block_core_navigation_add_directives_to_submenu( $tags, $block_attributes );
	}
	return $tags->get_updated_html();
}

/**
 * Build an array with CSS classes and inline styles defining the colors
 * which will be applied to the navigation markup in the front-end.
 *
 * @since 5.9.0
 *
 * @param array $attributes Navigation block attributes.
 *
 * @return array Colors CSS classes and inline styles.
 */
function block_core_navigation_build_css_colors( $attributes ) {
	$colors = array(
		'css_classes'           => array(),
		'inline_styles'         => '',
		'overlay_css_classes'   => array(),
		'overlay_inline_styles' => '',
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

	// Overlay text color.
	$has_named_overlay_text_color  = array_key_exists( 'overlayTextColor', $attributes );
	$has_custom_overlay_text_color = array_key_exists( 'customOverlayTextColor', $attributes );

	// If has overlay text color.
	if ( $has_custom_overlay_text_color || $has_named_overlay_text_color ) {
		// Add has-text-color class.
		$colors['overlay_css_classes'][] = 'has-text-color';
	}

	if ( $has_named_overlay_text_color ) {
		// Add the overlay color class.
		$colors['overlay_css_classes'][] = sprintf( 'has-%s-color', $attributes['overlayTextColor'] );
	} elseif ( $has_custom_overlay_text_color ) {
		// Add the custom overlay color inline style.
		$colors['overlay_inline_styles'] .= sprintf( 'color: %s;', $attributes['customOverlayTextColor'] );
	}

	// Overlay background color.
	$has_named_overlay_background_color  = array_key_exists( 'overlayBackgroundColor', $attributes );
	$has_custom_overlay_background_color = array_key_exists( 'customOverlayBackgroundColor', $attributes );

	// If has overlay background color.
	if ( $has_custom_overlay_background_color || $has_named_overlay_background_color ) {
		// Add has-background class.
		$colors['overlay_css_classes'][] = 'has-background';
	}

	if ( $has_named_overlay_background_color ) {
		// Add the overlay background-color class.
		$colors['overlay_css_classes'][] = sprintf( 'has-%s-background-color', $attributes['overlayBackgroundColor'] );
	} elseif ( $has_custom_overlay_background_color ) {
		// Add the custom overlay background-color inline style.
		$colors['overlay_inline_styles'] .= sprintf( 'background-color: %s;', $attributes['customOverlayBackgroundColor'] );
	}

	return $colors;
}

/**
 * Build an array with CSS classes and inline styles defining the font sizes
 * which will be applied to the navigation markup in the front-end.
 *
 * @since 5.9.0
 *
 * @param array $attributes Navigation block attributes.
 *
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
 * Returns the top-level submenu SVG chevron icon.
 *
 * @since 5.9.0
 *
 * @return string
 */
function block_core_navigation_render_submenu_icon() {
	return '<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12" fill="none" aria-hidden="true" focusable="false"><path d="M1.50002 4L6.00002 8L10.5 4" stroke-width="1.5"></path></svg>';
}

/**
 * Filter out empty "null" blocks from the block list.
 * 'parse_blocks' includes a null block with '\n\n' as the content when
 * it encounters whitespace. This is not a bug but rather how the parser
 * is designed.
 *
 * @since 5.9.0
 *
 * @param array $parsed_blocks the parsed blocks to be normalized.
 * @return array the normalized parsed blocks.
 */
function block_core_navigation_filter_out_empty_blocks( $parsed_blocks ) {
	$filtered = array_filter(
		$parsed_blocks,
		static function ( $block ) {
			return isset( $block['blockName'] );
		}
	);

	// Reset keys.
	return array_values( $filtered );
}

/**
 * Recursively checks if blocks contain a specific block type.
 *
 * @since 7.0.0
 *
 * @param WP_Block_List $blocks           The list of blocks to check.
 * @param string        $block_type       The block type to search for (e.g., 'core/navigation').
 * @param array         $skip_block_types Optional. Block types to skip when recursing. Default empty array.
 * @return bool Returns true if the specified block type is found.
 */
function block_core_navigation_block_tree_has_block_type( $blocks, $block_type, $skip_block_types = array() ) {
	if ( empty( $blocks ) ) {
		return false;
	}

	foreach ( $blocks as $block ) {
		if ( $block_type === $block->name ) {
			return true;
		}

		// Recursively check inner blocks, skipping specified block types.
		if ( ! in_array( $block->name, $skip_block_types, true ) && ! empty( $block->inner_blocks ) ) {
			if ( block_core_navigation_block_tree_has_block_type( $block->inner_blocks, $block_type, $skip_block_types ) ) {
				return true;
			}
		}
	}

	return false;
}

/**
 * Returns true if the navigation block contains a nested navigation block.
 *
 * @since 6.2.0
 * @deprecated 7.0.0 Use block_core_navigation_block_tree_has_block_type() instead.
 *
 * @param WP_Block_List $inner_blocks Inner block instance to be normalized.
 * @return bool true if the navigation block contains a nested navigation block.
 */
function block_core_navigation_block_contains_core_navigation( $inner_blocks ) {
	_deprecated_function( __FUNCTION__, '7.0.0', 'block_core_navigation_block_tree_has_block_type()' );

	return block_core_navigation_block_tree_has_block_type(
		$inner_blocks,
		'core/navigation'
	);
}

/**
 * Retrieves the appropriate fallback to be used on the front of the
 * site when there is no menu assigned to the Nav block.
 *
 * This aims to mirror how the fallback mechanic for wp_nav_menu works.
 * See https://developer.wordpress.org/reference/functions/wp_nav_menu/#more-information.
 *
 * @since 5.9.0
 *
 * @return array the array of blocks to be used as a fallback.
 */
function block_core_navigation_get_fallback_blocks() {
	$page_list_fallback = array(
		array(
			'blockName'    => 'core/page-list',
			'innerContent' => array(),
			'attrs'        => array(),
		),
	);

	$registry = WP_Block_Type_Registry::get_instance();

	// If `core/page-list` is not registered then return empty blocks.
	$fallback_blocks = $registry->is_registered( 'core/page-list' ) ? $page_list_fallback : array();
	$navigation_post = WP_Navigation_Fallback::get_fallback();

	// Use the first non-empty Navigation as fallback if available.
	if ( $navigation_post ) {
		$parsed_blocks  = parse_blocks( $navigation_post->post_content );
		$maybe_fallback = block_core_navigation_filter_out_empty_blocks( $parsed_blocks );

		// Normalizing blocks may result in an empty array of blocks if they were all `null` blocks.
		// In this case default to the (Page List) fallback.
		$fallback_blocks = ! empty( $maybe_fallback ) ? $maybe_fallback : $fallback_blocks;

		// Run Block Hooks algorithm to inject hooked blocks.
		// We have to run it here because we need the post ID of the Navigation block to track ignored hooked blocks.
		// TODO: See if we can move the apply_block_hooks_to_content_from_post_object() call
		// before the parse_blocks() call further above, to avoid the extra serialization/parsing.
		$markup          = serialize_blocks( $fallback_blocks );
		$markup          = apply_block_hooks_to_content_from_post_object( $markup, $navigation_post );
		$fallback_blocks = parse_blocks( $markup );
	}

	/**
	 * Filters the fallback experience for the Navigation block.
	 *
	 * Returning a falsey value will opt out of the fallback and cause the block not to render.
	 * To customise the blocks provided return an array of blocks - these should be valid
	 * children of the `core/navigation` block.
	 *
	 * @since 5.9.0
	 *
	 * @param array[] $fallback_blocks default fallback blocks provided by the default block mechanic.
	 */
	return apply_filters( 'block_core_navigation_render_fallback', $fallback_blocks );
}

/**
 * Iterate through all inner blocks recursively and get navigation link block's post IDs.
 *
 * @since 6.0.0
 *
 * @param WP_Block_List $inner_blocks Block list class instance.
 *
 * @return array Array of post IDs.
 */
function block_core_navigation_get_post_ids( $inner_blocks ) {
	$post_ids = array_map( 'block_core_navigation_from_block_get_post_ids', iterator_to_array( $inner_blocks ) );
	return array_unique( array_merge( ...$post_ids ) );
}

/**
 * Get post IDs from a navigation link block instance.
 *
 * @since 6.0.0
 *
 * @param WP_Block $block Instance of a block.
 *
 * @return array Array of post IDs.
 */
function block_core_navigation_from_block_get_post_ids( $block ) {
	$post_ids = array();

	if ( $block->inner_blocks ) {
		$post_ids = block_core_navigation_get_post_ids( $block->inner_blocks );
	}

	if ( 'core/navigation-link' === $block->name || 'core/navigation-submenu' === $block->name ) {
		if ( $block->attributes && isset( $block->attributes['kind'] ) && 'post-type' === $block->attributes['kind'] && isset( $block->attributes['id'] ) ) {
			$post_ids[] = $block->attributes['id'];
		}
	}

	return $post_ids;
}

/**
 * Renders the `core/navigation` block on server.
 *
 * @since 5.9.0
 *
 * @param array    $attributes The block attributes.
 * @param string   $content    The saved content.
 * @param WP_Block $block      The parsed block.
 *
 * @return string Returns the navigation block markup.
 */
function render_block_core_navigation( $attributes, $content, $block ) {
	return WP_Navigation_Block_Renderer::render( $attributes, $content, $block );
}

/**
 * Register the navigation block.
 *
 * @since 5.9.0
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
 * @since 5.9.0
 *
 * @param array $parsed_block The block being rendered.
 *
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

/**
 * Turns menu item data into a nested array of parsed blocks
 *
 * @since 5.9.0
 *
 * @deprecated 6.3.0 Use WP_Navigation_Fallback::parse_blocks_from_menu_items() instead.
 *
 * @param array $menu_items               An array of menu items that represent
 *                                        an individual level of a menu.
 * @param array $menu_items_by_parent_id  An array keyed by the id of the
 *                                        parent menu where each element is an
 *                                        array of menu items that belong to
 *                                        that parent.
 * @return array An array of parsed block data.
 */
function block_core_navigation_parse_blocks_from_menu_items( $menu_items, $menu_items_by_parent_id ) {

	_deprecated_function( __FUNCTION__, '6.3.0', 'WP_Navigation_Fallback::parse_blocks_from_menu_items' );

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
			'blockName' => isset( $menu_items_by_parent_id[ $menu_item->ID ] ) ? 'core/navigation-submenu' : 'core/navigation-link',
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

		$block['innerBlocks']  = isset( $menu_items_by_parent_id[ $menu_item->ID ] )
			? block_core_navigation_parse_blocks_from_menu_items( $menu_items_by_parent_id[ $menu_item->ID ], $menu_items_by_parent_id )
			: array();
		$block['innerContent'] = array_map( 'serialize_block', $block['innerBlocks'] );

		$blocks[] = $block;
	}

	return $blocks;
}

/**
 * Get the classic navigation menu to use as a fallback.
 *
 * @since 6.2.0
 *
 * @deprecated 6.3.0 Use WP_Navigation_Fallback::get_classic_menu_fallback() instead.
 *
 * @return object WP_Term The classic navigation.
 */
function block_core_navigation_get_classic_menu_fallback() {

	_deprecated_function( __FUNCTION__, '6.3.0', 'WP_Navigation_Fallback::get_classic_menu_fallback' );

	$classic_nav_menus = wp_get_nav_menus();

	// If menus exist.
	if ( $classic_nav_menus && ! is_wp_error( $classic_nav_menus ) ) {
		// Handles simple use case where user has a classic menu and switches to a block theme.

		// Returns the menu assigned to location `primary`.
		$locations = get_nav_menu_locations();
		if ( isset( $locations['primary'] ) ) {
			$primary_menu = wp_get_nav_menu_object( $locations['primary'] );
			if ( $primary_menu ) {
				return $primary_menu;
			}
		}

		// Returns a menu if `primary` is its slug.
		foreach ( $classic_nav_menus as $classic_nav_menu ) {
			if ( 'primary' === $classic_nav_menu->slug ) {
				return $classic_nav_menu;
			}
		}

		// Otherwise return the most recently created classic menu.
		usort(
			$classic_nav_menus,
			static function ( $a, $b ) {
				return $b->term_id - $a->term_id;
			}
		);
		return $classic_nav_menus[0];
	}
}

/**
 * Converts a classic navigation to blocks.
 *
 * @since 6.2.0
 *
 * @deprecated 6.3.0 Use WP_Navigation_Fallback::get_classic_menu_fallback_blocks() instead.
 *
 * @param  object $classic_nav_menu WP_Term The classic navigation object to convert.
 * @return array the normalized parsed blocks.
 */
function block_core_navigation_get_classic_menu_fallback_blocks( $classic_nav_menu ) {

	_deprecated_function( __FUNCTION__, '6.3.0', 'WP_Navigation_Fallback::get_classic_menu_fallback_blocks' );

	// BEGIN: Code that already exists in wp_nav_menu().
	$menu_items = wp_get_nav_menu_items( $classic_nav_menu->term_id, array( 'update_post_term_cache' => false ) );

	// Set up the $menu_item variables.
	_wp_menu_item_classes_by_context( $menu_items );

	$sorted_menu_items = array();
	foreach ( (array) $menu_items as $menu_item ) {
		$sorted_menu_items[ $menu_item->menu_order ] = $menu_item;
	}

	unset( $menu_items, $menu_item );

	// END: Code that already exists in wp_nav_menu().

	$menu_items_by_parent_id = array();
	foreach ( $sorted_menu_items as $menu_item ) {
		$menu_items_by_parent_id[ $menu_item->menu_item_parent ][] = $menu_item;
	}

	$inner_blocks = block_core_navigation_parse_blocks_from_menu_items(
		$menu_items_by_parent_id[0] ?? array(),
		$menu_items_by_parent_id
	);

	return serialize_blocks( $inner_blocks );
}

/**
 * If there's a classic menu then use it as a fallback.
 *
 * @since 6.2.0
 *
 * @deprecated 6.3.0 Use WP_Navigation_Fallback::create_classic_menu_fallback() instead.
 *
 * @return array the normalized parsed blocks.
 */
function block_core_navigation_maybe_use_classic_menu_fallback() {

	_deprecated_function( __FUNCTION__, '6.3.0', 'WP_Navigation_Fallback::create_classic_menu_fallback' );

	// See if we have a classic menu.
	$classic_nav_menu = block_core_navigation_get_classic_menu_fallback();

	if ( ! $classic_nav_menu ) {
		return;
	}

	// If we have a classic menu then convert it to blocks.
	$classic_nav_menu_blocks = block_core_navigation_get_classic_menu_fallback_blocks( $classic_nav_menu );

	if ( empty( $classic_nav_menu_blocks ) ) {
		return;
	}

	// Create a new navigation menu from the classic menu.
	$wp_insert_post_result = wp_insert_post(
		array(
			'post_content' => $classic_nav_menu_blocks,
			'post_title'   => $classic_nav_menu->name,
			'post_name'    => $classic_nav_menu->slug,
			'post_status'  => 'publish',
			'post_type'    => 'wp_navigation',
		),
		true // So that we can check whether the result is an error.
	);

	if ( is_wp_error( $wp_insert_post_result ) ) {
		return;
	}

	// Fetch the most recently published navigation which will be the classic one created above.
	return block_core_navigation_get_most_recently_published_navigation();
}

/**
 * Finds the most recently published `wp_navigation` Post.
 *
 * @since 6.1.0
 *
 * @deprecated 6.3.0 Use WP_Navigation_Fallback::get_most_recently_published_navigation() instead.
 *
 * @return WP_Post|null the first non-empty Navigation or null.
 */
function block_core_navigation_get_most_recently_published_navigation() {

	_deprecated_function( __FUNCTION__, '6.3.0', 'WP_Navigation_Fallback::get_most_recently_published_navigation' );

	// Default to the most recently created menu.
	$parsed_args = array(
		'post_type'              => 'wp_navigation',
		'no_found_rows'          => true,
		'update_post_meta_cache' => false,
		'update_post_term_cache' => false,
		'order'                  => 'DESC',
		'orderby'                => 'date',
		'post_status'            => 'publish',
		'posts_per_page'         => 1, // get only the most recent.
	);

	$navigation_post = new WP_Query( $parsed_args );
	if ( count( $navigation_post->posts ) > 0 ) {
		return $navigation_post->posts[0];
	}

	return null;
}
