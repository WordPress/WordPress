<?php
/**
 * Server-side rendering of the `core/navigation-submenu` block.
 *
 * @package WordPress
 */

/**
 * Returns the submenu visibility value with backward compatibility
 * for the deprecated openSubmenusOnClick attribute.
 *
 * This function centralizes the migration logic from the boolean
 * openSubmenusOnClick to the new submenuVisibility enum.
 *
 * Backward compatibility handling:
 * - Legacy blocks (saved before migration, never opened in editor):
 *   Have openSubmenusOnClick in database. Parent Navigation block passes it via context.
 *   We prioritize openSubmenusOnClick to preserve the original behavior.
 *
 * - Migrated blocks (opened in editor after migration):
 *   JavaScript deprecation removes openSubmenusOnClick and sets submenuVisibility.
 *   We use submenuVisibility since openSubmenusOnClick is null.
 *
 * - New blocks (created after migration):
 *   Only have submenuVisibility, openSubmenusOnClick is null.
 *   We use submenuVisibility.
 *
 * @since 6.9.0
 *
 * @param array $context Block context from parent Navigation block.
 * @return string The visibility mode: 'hover', 'click', or 'always'.
 */
function block_core_navigation_submenu_get_submenu_visibility( $context ) {
	$deprecated_open_submenus_on_click = $context['openSubmenusOnClick'] ?? null;

	// For backward compatibility, prioritize the legacy attribute if present. If it has been loaded and saved in the editor, then
	// the deprecated attribute will be replaced by submenuVisibility.
	if ( null !== $deprecated_open_submenus_on_click ) {
		// Convert boolean to string: true -> 'click', false -> 'hover'.
		return ! empty( $deprecated_open_submenus_on_click ) ? 'click' : 'hover';
	}

	$submenu_visibility = $context['submenuVisibility'] ?? null;

	// Use submenuVisibility for migrated/new blocks.
	return $submenu_visibility ?? 'hover';
}

// Path differs between source and build: '../navigation-link/shared/' in source, './navigation-link/shared/' in build.
if ( file_exists( __DIR__ . '/../navigation-link/shared/item-should-render.php' ) ) {
	require_once __DIR__ . '/../navigation-link/shared/item-should-render.php';
	require_once __DIR__ . '/../navigation-link/shared/render-submenu-icon.php';
} else {
	require_once __DIR__ . '/navigation-link/shared/item-should-render.php';
	require_once __DIR__ . '/navigation-link/shared/render-submenu-icon.php';
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
function block_core_navigation_submenu_build_css_font_sizes( $context ) {
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
			wp_get_typography_font_size_value(
				array(
					'size' => $context['style']['typography']['fontSize'],
				)
			)
		);
	}

	return $font_sizes;
}

/**
 * Renders the `core/navigation-submenu` block.
 *
 * @since 5.9.0
 *
 * @param array    $attributes The block attributes.
 * @param string   $content    The saved content.
 * @param WP_Block $block      The parsed block.
 *
 * @return string Returns the post content with the legacy widget added.
 */
function render_block_core_navigation_submenu( $attributes, $content, $block ) {
	// Check if this navigation item should render based on post status.
	if ( defined( 'IS_GUTENBERG_PLUGIN' ) && IS_GUTENBERG_PLUGIN ) {
		if ( ! gutenberg_block_core_shared_navigation_item_should_render( $attributes, $block ) ) {
			return '';
		}
	}

	// Don't render the block's subtree if it has no label.
	if ( empty( $attributes['label'] ) ) {
		return '';
	}

	$font_sizes      = block_core_navigation_submenu_build_css_font_sizes( $block->context );
	$style_attribute = $font_sizes['inline_styles'];

	// Render inner blocks first to check if any menu items will actually display.
	$inner_blocks_html = '';
	foreach ( $block->inner_blocks as $inner_block ) {
		$inner_blocks_html .= $inner_block->render();
	}
	$has_submenu = ! empty( trim( $inner_blocks_html ) );

	$kind      = empty( $attributes['kind'] ) ? 'post_type' : str_replace( '-', '_', $attributes['kind'] );
	$is_active = ! empty( $attributes['id'] ) && get_queried_object_id() === (int) $attributes['id'] && ! empty( get_queried_object()->$kind );

	if ( is_post_type_archive() && ! empty( $attributes['url'] ) ) {
		$queried_archive_link = get_post_type_archive_link( get_queried_object()->name );
		if ( $attributes['url'] === $queried_archive_link ) {
			$is_active = true;
		}
	}

	$show_submenu_indicators = isset( $block->context['showSubmenuIcon'] ) && $block->context['showSubmenuIcon'];
	$computed_visibility     = block_core_navigation_submenu_get_submenu_visibility( $block->context );
	$open_on_click           = 'click' === $computed_visibility;
	$open_on_hover           = 'hover' === $computed_visibility;
	$open_on_hover_and_click = $open_on_hover && $show_submenu_indicators;

	$classes = array(
		'wp-block-navigation-item',
	);
	$classes = array_merge(
		$classes,
		$font_sizes['css_classes']
	);
	if ( $has_submenu ) {
		$classes[] = 'has-child';
	}
	if ( $open_on_click ) {
		$classes[] = 'open-on-click';
	}
	if ( $open_on_hover_and_click ) {
		$classes[] = 'open-on-hover-click';
	}
	if ( 'always' === $computed_visibility ) {
		$classes[] = 'open-always';
	}
	if ( $is_active ) {
		$classes[] = 'current-menu-item';
	}

	$wrapper_attributes = get_block_wrapper_attributes(
		array(
			'class' => implode( ' ', $classes ),
			'style' => $style_attribute,
		)
	);

	$label = '';

	if ( isset( $attributes['label'] ) ) {
		$label .= wp_kses_post( $attributes['label'] );
	}

	$aria_label = sprintf(
		/* translators: Accessibility text. %s: Parent page title. */
		__( '%s submenu' ),
		wp_strip_all_tags( $label )
	);

	$html = '<li ' . $wrapper_attributes . '>';

	// If Submenus open on hover or are always open, we render an anchor tag with attributes.
	// If submenu icons are set to show, we also render a submenu button, so the submenu can be opened on click.
	if ( ! $open_on_click ) {
		$item_url = $attributes['url'] ?? '';
		// Start appending HTML attributes to anchor tag.
		$html .= '<a class="wp-block-navigation-item__content"';

		// The href attribute on a and area elements is not required;
		// when those elements do not have href attributes they do not create hyperlinks.
		// But also The href attribute must have a value that is a valid URL potentially
		// surrounded by spaces.
		// see: https://html.spec.whatwg.org/multipage/links.html#links-created-by-a-and-area-elements.
		if ( ! empty( $item_url ) ) {
			$html .= ' href="' . esc_url( $item_url ) . '"';
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

		$html .= '>';
		// End appending HTML attributes to anchor tag.

		$html .= '<span class="wp-block-navigation-item__label">';
		$html .= $label;
		$html .= '</span>';

		// Add description if available.
		if ( ! empty( $attributes['description'] ) ) {
			$html .= '<span class="wp-block-navigation-item__description">';
			$html .= wp_kses_post( $attributes['description'] );
			$html .= '</span>';
		}

		$html .= '</a>';
		// End anchor tag content.

		if ( $show_submenu_indicators && $has_submenu ) {
			// The submenu icon is rendered in a button here
			// so that there's a clickable element to open the submenu.
			$html .= '<button aria-label="' . esc_attr( $aria_label ) . '" class="wp-block-navigation__submenu-icon wp-block-navigation-submenu__toggle" aria-expanded="false">' . block_core_navigation_render_submenu_icon() . '</button>';
		}
	} else {
		$html .= '<button aria-label="' . esc_attr( $aria_label ) . '" class="wp-block-navigation-item__content wp-block-navigation-submenu__toggle" aria-expanded="false">';

		// Wrap title with span to isolate it from submenu icon.
		$html .= '<span class="wp-block-navigation-item__label">';

		$html .= $label;

		$html .= '</span>';

		// Add description if available.
		if ( ! empty( $attributes['description'] ) ) {
			$html .= '<span class="wp-block-navigation-item__description">';
			$html .= wp_kses_post( $attributes['description'] );
			$html .= '</span>';
		}

		$html .= '</button>';

		if ( $has_submenu ) {
			$html .= '<span class="wp-block-navigation__submenu-icon">' . block_core_navigation_render_submenu_icon() . '</span>';
		}
	}

	if ( $has_submenu ) {
		// Copy some attributes from the parent block to this one.
		// Ideally this would happen in the client when the block is created.
		if ( array_key_exists( 'overlayTextColor', $block->context ) ) {
			$attributes['textColor'] = $block->context['overlayTextColor'];
		}
		if ( array_key_exists( 'overlayBackgroundColor', $block->context ) ) {
			$attributes['backgroundColor'] = $block->context['overlayBackgroundColor'];
		}
		if ( array_key_exists( 'customOverlayTextColor', $block->context ) ) {
			$attributes['style']['color']['text'] = $block->context['customOverlayTextColor'];
		}
		if ( array_key_exists( 'customOverlayBackgroundColor', $block->context ) ) {
			$attributes['style']['color']['background'] = $block->context['customOverlayBackgroundColor'];
		}

		// This allows us to be able to get a response from wp_apply_colors_support.
		$block->block_type->supports['color'] = true;
		$colors_supports                      = wp_apply_colors_support( $block->block_type, $attributes );
		$css_classes                          = 'wp-block-navigation__submenu-container';
		if ( array_key_exists( 'class', $colors_supports ) ) {
			$css_classes .= ' ' . $colors_supports['class'];
		}

		$style_attribute = '';
		if ( array_key_exists( 'style', $colors_supports ) ) {
			$style_attribute = $colors_supports['style'];
		}

		if ( strpos( $inner_blocks_html, 'current-menu-item' ) ) {
			$tag_processor = new WP_HTML_Tag_Processor( $html );
			while ( $tag_processor->next_tag( array( 'class_name' => 'wp-block-navigation-item' ) ) ) {
				$tag_processor->add_class( 'current-menu-ancestor' );
			}
			$html = $tag_processor->get_updated_html();
		}

		$wrapper_attributes = get_block_wrapper_attributes(
			array(
				'class' => $css_classes,
				'style' => $style_attribute,
			)
		);

		$html .= sprintf(
			'<ul %s>%s</ul>',
			$wrapper_attributes,
			$inner_blocks_html
		);

	}

	$html .= '</li>';

	return $html;
}

/**
 * Register the navigation submenu block.
 *
 * @since 5.9.0
 *
 * @uses render_block_core_navigation_submenu()
 * @throws WP_Error An WP_Error exception parsing the block definition.
 */
function register_block_core_navigation_submenu() {
	register_block_type_from_metadata(
		__DIR__ . '/navigation-submenu',
		array(
			'render_callback' => 'render_block_core_navigation_submenu',
		)
	);
}
add_action( 'init', 'register_block_core_navigation_submenu' );
