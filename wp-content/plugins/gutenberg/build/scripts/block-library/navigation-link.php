<?php
/**
 * Server-side registering and rendering of the `core/navigation-link` block.
 *
 * @package WordPress
 */

/**
 * Build an array with CSS classes and inline styles defining the colors
 * which will be applied to the navigation markup in the front-end.
 *
 * @since 5.9.0
 *
 * @param  array $context     Navigation block context.
 * @param  array $attributes  Block attributes.
 * @param  bool  $is_sub_menu Whether the link is part of a sub-menu. Default false.
 * @return array Colors CSS classes and inline styles.
 */
function gutenberg_block_core_navigation_link_build_css_colors( $context, $attributes, $is_sub_menu = false ) {
	$colors = array(
		'css_classes'   => array(),
		'inline_styles' => '',
	);

	// Text color.
	$named_text_color  = null;
	$custom_text_color = null;

	if ( $is_sub_menu && array_key_exists( 'customOverlayTextColor', $context ) ) {
		$custom_text_color = $context['customOverlayTextColor'];
	} elseif ( $is_sub_menu && array_key_exists( 'overlayTextColor', $context ) ) {
		$named_text_color = $context['overlayTextColor'];
	} elseif ( array_key_exists( 'customTextColor', $context ) ) {
		$custom_text_color = $context['customTextColor'];
	} elseif ( array_key_exists( 'textColor', $context ) ) {
		$named_text_color = $context['textColor'];
	} elseif ( isset( $context['style']['color']['text'] ) ) {
		$custom_text_color = $context['style']['color']['text'];
	}

	// If has text color.
	if ( ! is_null( $named_text_color ) ) {
		// Add the color class.
		array_push( $colors['css_classes'], 'has-text-color', sprintf( 'has-%s-color', $named_text_color ) );
	} elseif ( ! is_null( $custom_text_color ) ) {
		// Add the custom color inline style.
		$colors['css_classes'][]  = 'has-text-color';
		$colors['inline_styles'] .= sprintf( 'color: %s;', $custom_text_color );
	}

	// Background color.
	$named_background_color  = null;
	$custom_background_color = null;

	if ( $is_sub_menu && array_key_exists( 'customOverlayBackgroundColor', $context ) ) {
		$custom_background_color = $context['customOverlayBackgroundColor'];
	} elseif ( $is_sub_menu && array_key_exists( 'overlayBackgroundColor', $context ) ) {
		$named_background_color = $context['overlayBackgroundColor'];
	} elseif ( array_key_exists( 'customBackgroundColor', $context ) ) {
		$custom_background_color = $context['customBackgroundColor'];
	} elseif ( array_key_exists( 'backgroundColor', $context ) ) {
		$named_background_color = $context['backgroundColor'];
	} elseif ( isset( $context['style']['color']['background'] ) ) {
		$custom_background_color = $context['style']['color']['background'];
	}

	// If has background color.
	if ( ! is_null( $named_background_color ) ) {
		// Add the background-color class.
		array_push( $colors['css_classes'], 'has-background', sprintf( 'has-%s-background-color', $named_background_color ) );
	} elseif ( ! is_null( $custom_background_color ) ) {
		// Add the custom background-color inline style.
		$colors['css_classes'][]  = 'has-background';
		$colors['inline_styles'] .= sprintf( 'background-color: %s;', $custom_background_color );
	}

	return $colors;
}

/**
 * Build an array with CSS classes and inline styles defining the font sizes
 * which will be applied to the navigation markup in the front-end.
 *
 * @since 5.9.0
 *
 * @param  array $context Navigation block context.
 * @return array Font size CSS classes and inline styles.
 */
function gutenberg_block_core_navigation_link_build_css_font_sizes( $context ) {
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
 * Returns the top-level submenu SVG chevron icon.
 *
 * @since 5.9.0
 *
 * @return string
 */
function gutenberg_block_core_navigation_link_render_submenu_icon() {
	return '<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12" fill="none" aria-hidden="true" focusable="false"><path d="M1.50002 4L6.00002 8L10.5 4" stroke-width="1.5"></path></svg>';
}

/**
 * Decodes a url if it's encoded, returning the same url if not.
 *
 * @since 6.2.0
 *
 * @param string $url The url to decode.
 *
 * @return string $url Returns the decoded url.
 */
function gutenberg_block_core_navigation_link_maybe_urldecode( $url ) {
	$is_url_encoded = false;
	$query          = parse_url( $url, PHP_URL_QUERY );
	$query_params   = wp_parse_args( $query );

	foreach ( $query_params as $query_param ) {
		$can_query_param_be_encoded = is_string( $query_param ) && ! empty( $query_param );
		if ( ! $can_query_param_be_encoded ) {
			continue;
		}
		if ( rawurldecode( $query_param ) !== $query_param ) {
			$is_url_encoded = true;
			break;
		}
	}

	if ( $is_url_encoded ) {
		return rawurldecode( $url );
	}

	return $url;
}


/**
 * Renders the `core/navigation-link` block.
 *
 * @since 5.9.0
 *
 * @param array    $attributes The block attributes.
 * @param string   $content    The saved content.
 * @param WP_Block $block      The parsed block.
 *
 * @return string Returns the post content with the legacy widget added.
 */
function gutenberg_render_block_core_navigation_link( $attributes, $content, $block ) {
	$navigation_link_has_id = isset( $attributes['id'] ) && is_numeric( $attributes['id'] );
	$is_post_type           = isset( $attributes['kind'] ) && 'post-type' === $attributes['kind'];
	$is_post_type           = $is_post_type || isset( $attributes['type'] ) && ( 'post' === $attributes['type'] || 'page' === $attributes['type'] );

	// Don't render the block's subtree if it is a draft or if the ID does not exist.
	if ( $is_post_type && $navigation_link_has_id ) {
		$post = get_post( $attributes['id'] );
		/**
		 * Filter allowed post_status for navigation link block to render.
		 *
		 * @since 6.8.0
		 *
		 * @param array $post_status
		 * @param array $attributes
		 * @param WP_Block $block
		 */
		$allowed_post_status = (array) apply_filters(
			'render_block_core_navigation_link_allowed_post_status',
			array( 'publish' ),
			$attributes,
			$block
		);
		if ( ! $post || ! in_array( $post->post_status, $allowed_post_status, true ) ) {
			return '';
		}
	}

	// Don't render the block's subtree if it has no label.
	if ( empty( $attributes['label'] ) ) {
		return '';
	}

	$font_sizes      = gutenberg_block_core_navigation_link_build_css_font_sizes( $block->context );
	$classes         = array_merge(
		$font_sizes['css_classes']
	);
	$style_attribute = $font_sizes['inline_styles'];

	$css_classes = trim( implode( ' ', $classes ) );
	$has_submenu = count( $block->inner_blocks ) > 0;
	$kind        = empty( $attributes['kind'] ) ? 'post_type' : str_replace( '-', '_', $attributes['kind'] );
	$is_active   = ! empty( $attributes['id'] ) && get_queried_object_id() === (int) $attributes['id'] && ! empty( get_queried_object()->$kind );

	if ( is_post_type_archive() && ! empty( $attributes['url'] ) ) {
		$queried_archive_link = get_post_type_archive_link( get_queried_object()->name );
		if ( $attributes['url'] === $queried_archive_link ) {
			$is_active = true;
		}
	}

	$wrapper_attributes = get_block_wrapper_attributes(
		array(
			'class' => $css_classes . ' wp-block-navigation-item' . ( $has_submenu ? ' has-child' : '' ) .
				( $is_active ? ' current-menu-item' : '' ),
			'style' => $style_attribute,
		)
	);
	$html               = '<li ' . $wrapper_attributes . '>' .
		'<a class="wp-block-navigation-item__content" ';

	// Start appending HTML attributes to anchor tag.
	if ( isset( $attributes['url'] ) ) {
		$html .= ' href="' . esc_url( gutenberg_block_core_navigation_link_maybe_urldecode( $attributes['url'] ) ) . '"';
	}

	if ( $is_active ) {
		$html .= ' aria-current="page"';
	}

	if ( isset( $attributes['opensInNewTab'] ) && true === $attributes['opensInNewTab'] ) {
		$html .= ' target="_blank"  ';
	}

	if ( isset( $attributes['rel'] ) ) {
		$html .= ' rel="' . esc_attr( $attributes['rel'] ) . '"';
	} elseif ( isset( $attributes['nofollow'] ) && $attributes['nofollow'] ) {
		$html .= ' rel="nofollow"';
	}

	if ( isset( $attributes['title'] ) ) {
		$html .= ' title="' . esc_attr( $attributes['title'] ) . '"';
	}

	// End appending HTML attributes to anchor tag.

	// Start anchor tag content.
	$html .= '>' .
		// Wrap title with span to isolate it from submenu icon.
		'<span class="wp-block-navigation-item__label">';

	if ( isset( $attributes['label'] ) ) {
		$html .= wp_kses_post( $attributes['label'] );
	}

	$html .= '</span>';

	// Add description if available.
	if ( ! empty( $attributes['description'] ) ) {
		$html .= '<span class="wp-block-navigation-item__description">';
		$html .= wp_kses_post( $attributes['description'] );
		$html .= '</span>';
	}

	$html .= '</a>';
	// End anchor tag content.

	if ( isset( $block->context['showSubmenuIcon'] ) && $block->context['showSubmenuIcon'] && $has_submenu ) {
		// The submenu icon can be hidden by a CSS rule on the Navigation Block.
		$html .= '<span class="wp-block-navigation__submenu-icon">' . gutenberg_block_core_navigation_link_render_submenu_icon() . '</span>';
	}

	if ( $has_submenu ) {
		$inner_blocks_html = '';
		foreach ( $block->inner_blocks as $inner_block ) {
			$inner_blocks_html .= $inner_block->render();
		}

		$html .= sprintf(
			'<ul class="wp-block-navigation__submenu-container">%s</ul>',
			$inner_blocks_html
		);
	}

	$html .= '</li>';

	return $html;
}

/**
 * Returns a navigation link variation
 *
 * @since 5.9.0
 *
 * @param WP_Taxonomy|WP_Post_Type $entity post type or taxonomy entity.
 * @param string                   $kind string of value 'taxonomy' or 'post-type'.
 *
 * @return array
 */
function gutenberg_build_variation_for_navigation_link( $entity, $kind ) {
	$title       = '';
	$description = '';

	// Get default labels based on entity type
	$default_labels = null;
	if ( $entity instanceof WP_Post_Type ) {
		$default_labels = WP_Post_Type::get_default_labels();
	} elseif ( $entity instanceof WP_Taxonomy ) {
		$default_labels = WP_Taxonomy::get_default_labels();
	}

	// Get title and check if it's default
	$is_default_title = false;
	if ( property_exists( $entity->labels, 'item_link' ) ) {
		$title = $entity->labels->item_link;
		if ( isset( $default_labels['item_link'] ) ) {
			$is_default_title = in_array( $title, $default_labels['item_link'], true );
		}
	}

	// Get description and check if it's default
	$is_default_description = false;
	if ( property_exists( $entity->labels, 'item_link_description' ) ) {
		$description = $entity->labels->item_link_description;
		if ( isset( $default_labels['item_link_description'] ) ) {
			$is_default_description = in_array( $description, $default_labels['item_link_description'], true );
		}
	}

	// Calculate singular name once (used for both title and description)
	$singular = isset( $entity->labels->singular_name ) ? $entity->labels->singular_name : ucfirst( $entity->name );

	// Set default title if needed
	if ( $is_default_title || '' === $title ) {
		/* translators: %s: Singular label of the entity. */
		$title = sprintf( __( '%s link' ), $singular );
	}

	// Default description if needed.
	// Use a single space character instead of an empty string to prevent fallback to the
	// block.json default description ("Add a page, link, or another item to your navigation.").
	// An empty string would be treated as missing and trigger the fallback, while a single
	// space appears blank in the UI but prevents the fallback behavior.
	// We avoid generating descriptions like "A link to a %s" to prevent grammatical errors
	// (e.g., "A link to a event" should be "A link to an event").
	if ( $is_default_description || '' === $description ) {
		$description = ' ';
	}

	$variation = array(
		'name'        => $entity->name,
		'title'       => $title,
		'description' => $description,
		'attributes'  => array(
			'type' => $entity->name,
			'kind' => $kind,
		),
	);

	// Tweak some value for the variations.
	$variation_overrides = array(
		'post_tag'    => array(
			'name'       => 'tag',
			'attributes' => array(
				'type' => 'tag',
				'kind' => $kind,
			),
		),
		'post_format' => array(
			// The item_link and item_link_description for post formats is the
			// same as for tags, so need to be overridden.
			'title'       => __( 'Post Format Link' ),
			'description' => __( 'A link to a post format' ),
			'attributes'  => array(
				'type' => 'post_format',
				'kind' => $kind,
			),
		),
	);

	if ( array_key_exists( $entity->name, $variation_overrides ) ) {
		$variation = array_merge(
			$variation,
			$variation_overrides[ $entity->name ]
		);
	}

	return $variation;
}

/**
 * Filters the registered variations for a block type.
 * Returns the dynamically built variations for all post-types and taxonomies.
 *
 * @since 6.5.0
 *
 * @param array         $variations Array of registered variations for a block type.
 * @param WP_Block_Type $block_type The full block type object.
 * @return array Numerically indexed array of block variations.
 */
function gutenberg_block_core_navigation_link_filter_variations( $variations, $block_type ) {
	if ( 'core/navigation-link' !== $block_type->name ) {
		return $variations;
	}

	$generated_variations = gutenberg_block_core_navigation_link_build_variations();

	/*
	 * IMPORTANT: Order matters for deduplication.
	 *
	 * The variations returned from this filter are bootstrapped to JavaScript and
	 * processed by the block variations reducer. The reducer uses `getUniqueItemsByName()`
	 * (packages/blocks/src/store/reducer.js:51-57) which keeps the FIRST variation with
	 * a given 'name' and discards later duplicates when processing the array in order.
	 *
	 * By placing generated variations first in `array_merge()`, the improved
	 * labels (e.g., "Product link" instead of generic "Post Link") are processed first
	 * and preserved. The generic incoming variations are then discarded as duplicates.
	 *
	 * Why `array_merge()` instead of manual deduplication?
	 * - Both arrays use numeric indices (0, 1, 2...), so `array_merge()` concatenates
	 *   and re-indexes them sequentially, preserving order
	 * - The reducer handles deduplication, so it is not needed here
	 * - This keeps the PHP code simple and relies on the established JavaScript behavior
	 *
	 * See: https://github.com/WordPress/gutenberg/pull/72517
	 */
	return array_merge( $generated_variations, $variations );
}

/**
 * Returns an array of variations for the navigation link block.
 *
 * @since 6.5.0
 *
 * @return array
 */
function gutenberg_block_core_navigation_link_build_variations() {
	$post_types = get_post_types( array( 'show_in_nav_menus' => true ), 'objects' );
	$taxonomies = get_taxonomies( array( 'show_in_nav_menus' => true ), 'objects' );

	/*
	 * Use two separate arrays as a way to order the variations in the UI.
	 * Known variations (like Post Link and Page Link) are added to the
	 * `built_ins` array. Variations for custom post types and taxonomies are
	 * added to the `variations` array and will always appear after `built-ins.
	 */
	$built_ins  = array();
	$variations = array();

	if ( $post_types ) {
		foreach ( $post_types as $post_type ) {
			$variation = gutenberg_build_variation_for_navigation_link( $post_type, 'post-type' );
			if ( $post_type->_builtin ) {
				$built_ins[] = $variation;
			} else {
				$variations[] = $variation;
			}
		}
	}
	if ( $taxonomies ) {
		foreach ( $taxonomies as $taxonomy ) {
			$variation = gutenberg_build_variation_for_navigation_link( $taxonomy, 'taxonomy' );
			if ( $taxonomy->_builtin ) {
				$built_ins[] = $variation;
			} else {
				$variations[] = $variation;
			}
		}
	}

	$all_variations = array_merge( $built_ins, $variations );

	return $all_variations;
}

/**
 * Registers the navigation link block.
 *
 * @since 5.9.0
 *
 * @uses gutenberg_render_block_core_navigation_link()
 * @throws WP_Error An WP_Error exception parsing the block definition.
 */
function gutenberg_register_block_core_navigation_link() {
	register_block_type_from_metadata(
		__DIR__ . '/navigation-link',
		array(
			'render_callback' => 'gutenberg_render_block_core_navigation_link',
		)
	);
}
add_action( 'init', 'gutenberg_register_block_core_navigation_link', 20 );
/**
 * Creates all variations for post types / taxonomies dynamically (= each time when variations are requested).
 * Do not use variation_callback, to also account for unregistering post types/taxonomies later on.
 */
add_action( 'get_block_type_variations', 'gutenberg_block_core_navigation_link_filter_variations', 10, 2 );
