<?php
/**
 * Server-side rendering of the `core/navigation-submenu` block.
 *
 * @package WordPress
 */

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
 * Returns the top-level submenu SVG chevron icon.
 *
 * @since 5.9.0
 *
 * @return string
 */
function block_core_navigation_submenu_render_submenu_icon() {
	return '<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12" fill="none" aria-hidden="true" focusable="false"><path d="M1.50002 4L6.00002 8L10.5 4" stroke-width="1.5"></path></svg>';
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
	$navigation_link_has_id = isset( $attributes['id'] ) && is_numeric( $attributes['id'] );
	$is_post_type           = isset( $attributes['kind'] ) && 'post-type' === $attributes['kind'];
	$is_post_type           = $is_post_type || isset( $attributes['type'] ) && ( 'post' === $attributes['type'] || 'page' === $attributes['type'] );

	// Don't render the block's subtree if it is a draft.
	if ( $is_post_type && $navigation_link_has_id && 'publish' !== get_post_status( $attributes['id'] ) ) {
		return '';
	}

	// Don't render the block's subtree if it has no label.
	if ( empty( $attributes['label'] ) ) {
		return '';
	}

	$font_sizes      = block_core_navigation_submenu_build_css_font_sizes( $block->context );
	$style_attribute = $font_sizes['inline_styles'];

	$css_classes = trim( implode( ' ', $font_sizes['css_classes'] ) );
	$has_submenu = count( $block->inner_blocks ) > 0;
	$kind        = empty( $attributes['kind'] ) ? 'post_type' : str_replace( '-', '_', $attributes['kind'] );
	$is_active   = ! empty( $attributes['id'] ) && get_queried_object_id() === (int) $attributes['id'] && ! empty( get_queried_object()->$kind );

	if ( is_post_type_archive() ) {
		$queried_archive_link = get_post_type_archive_link( get_queried_object()->name );
		if ( $attributes['url'] === $queried_archive_link ) {
			$is_active = true;
		}
	}

	$show_submenu_indicators = isset( $block->context['showSubmenuIcon'] ) && $block->context['showSubmenuIcon'];
	$open_on_click           = isset( $block->context['openSubmenusOnClick'] ) && $block->context['openSubmenusOnClick'];
	$open_on_hover_and_click = isset( $block->context['openSubmenusOnClick'] ) && ! $block->context['openSubmenusOnClick'] &&
		$show_submenu_indicators;

	$wrapper_attributes = get_block_wrapper_attributes(
		array(
			'class' => $css_classes . ' wp-block-navigation-item' . ( $has_submenu ? ' has-child' : '' ) .
			( $open_on_click ? ' open-on-click' : '' ) . ( $open_on_hover_and_click ? ' open-on-hover-click' : '' ) .
			( $is_active ? ' current-menu-item' : '' ),
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

	// If Submenus open on hover, we render an anchor tag with attributes.
	// If submenu icons are set to show, we also render a submenu button, so the submenu can be opened on click.
	if ( ! $open_on_click ) {
		$item_url = isset( $attributes['url'] ) ? $attributes['url'] : '';
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

		$html .= $label;

		$html .= '</a>';
		// End anchor tag content.

		if ( $show_submenu_indicators ) {
			// The submenu icon is rendered in a button here
			// so that there's a clickable element to open the submenu.
			$html .= '<button aria-label="' . esc_attr( $aria_label ) . '" class="wp-block-navigation__submenu-icon wp-block-navigation-submenu__toggle" aria-expanded="false">' . block_core_navigation_submenu_render_submenu_icon() . '</button>';
		}
	} else {
		// If menus open on click, we render the parent as a button.
		$html .= '<button aria-label="' . esc_attr( $aria_label ) . '" class="wp-block-navigation-item__content wp-block-navigation-submenu__toggle" aria-expanded="false">';

		// Wrap title with span to isolate it from submenu icon.
		$html .= '<span class="wp-block-navigation-item__label">';

		$html .= $label;

		$html .= '</span>';

		$html .= '</button>';

		$html .= '<span class="wp-block-navigation__submenu-icon">' . block_core_navigation_submenu_render_submenu_icon() . '</span>';

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

		$inner_blocks_html = '';
		foreach ( $block->inner_blocks as $inner_block ) {
			$inner_blocks_html .= $inner_block->render();
		}

		if ( strpos( $inner_blocks_html, 'current-menu-item' ) ) {
			$tag_processor = new WP_HTML_Tag_Processor( $html );
			while ( $tag_processor->next_tag( array( 'class_name' => 'wp-block-navigation-item__content' ) ) ) {
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
