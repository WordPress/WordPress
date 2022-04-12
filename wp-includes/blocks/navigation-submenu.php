<?php
/**
 * Server-side rendering of the `core/navigation-submenu` block.
 *
 * @package WordPress
 */

/**
 * Build an array with CSS classes and inline styles defining the colors
 * which will be applied to the navigation markup in the front-end.
 *
 * @param  array $context    Navigation block context.
 * @param  array $attributes Block attributes.
 * @return array Colors CSS classes and inline styles.
 */
function block_core_navigation_submenu_build_css_colors( $context, $attributes ) {
	$colors = array(
		'css_classes'   => array(),
		'inline_styles' => '',
	);

	$is_sub_menu = isset( $attributes['isTopLevelItem'] ) ? ( ! $attributes['isTopLevelItem'] ) : false;

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
		$font_sizes['inline_styles'] = sprintf( 'font-size: %s;', $context['style']['typography']['fontSize'] );
	}

	return $font_sizes;
}

/**
 * Returns the top-level submenu SVG chevron icon.
 *
 * @return string
 */
function block_core_navigation_submenu_render_submenu_icon() {
	return '<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12" fill="none" aria-hidden="true" focusable="false"><path d="M1.50002 4L6.00002 8L10.5 4" stroke-width="1.5"></path></svg>';
}

/**
 * Renders the `core/navigation-submenu` block.
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

	$colors          = block_core_navigation_submenu_build_css_colors( $block->context, $attributes );
	$font_sizes      = block_core_navigation_submenu_build_css_font_sizes( $block->context );
	$classes         = array_merge(
		$colors['css_classes'],
		$font_sizes['css_classes']
	);
	$style_attribute = ( $colors['inline_styles'] . $font_sizes['inline_styles'] );

	$css_classes = trim( implode( ' ', $classes ) );
	$has_submenu = count( $block->inner_blocks ) > 0;
	$is_active   = ! empty( $attributes['id'] ) && ( get_the_ID() === $attributes['id'] );

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
		$html .= '<a class="wp-block-navigation-item__content" href="' . esc_url( $item_url ) . '"';

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
 * Register the navigation submenu block.
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
