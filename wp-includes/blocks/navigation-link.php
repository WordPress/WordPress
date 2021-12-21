<?php
/**
 * Server-side rendering of the `core/navigation-link` block.
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
function block_core_navigation_link_build_css_colors( $context, $attributes ) {
	$colors = array(
		'css_classes'   => array(),
		'inline_styles' => '',
	);

	$is_sub_menu = isset( $attributes['isTopLevelLink'] ) ? ( ! $attributes['isTopLevelLink'] ) : false;

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
function block_core_navigation_link_build_css_font_sizes( $context ) {
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
function block_core_navigation_link_render_submenu_icon() {
	return '<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12" fill="none" role="img" aria-hidden="true" focusable="false"><path d="M1.50002 4L6.00002 8L10.5 4" stroke-width="1.5"></path></svg>';
}

/**
 * Renders the `core/navigation-link` block.
 *
 * @param array $attributes The block attributes.
 * @param array $content The saved content.
 * @param array $block The parsed block.
 *
 * @return string Returns the post content with the legacy widget added.
 */
function render_block_core_navigation_link( $attributes, $content, $block ) {
	$navigation_link_has_id = isset( $attributes['id'] ) && is_numeric( $attributes['id'] );
	$is_post_type           = isset( $attributes['kind'] ) && 'post-type' === $attributes['kind'];
	$is_post_type           = $is_post_type || isset( $attributes['type'] ) && ( 'post' === $attributes['type'] || 'page' === $attributes['type'] );

	// Don't render the block's subtree if it is a draft or if the ID does not exist.
	if ( $is_post_type && $navigation_link_has_id ) {
		$post = get_post( $attributes['id'] );
		if ( ! $post || 'publish' !== $post->post_status ) {
			return '';
		}
	}

	// Don't render the block's subtree if it has no label.
	if ( empty( $attributes['label'] ) ) {
		return '';
	}

	$colors          = block_core_navigation_link_build_css_colors( $block->context, $attributes );
	$font_sizes      = block_core_navigation_link_build_css_font_sizes( $block->context );
	$classes         = array_merge(
		$colors['css_classes'],
		$font_sizes['css_classes']
	);
	$style_attribute = ( $colors['inline_styles'] . $font_sizes['inline_styles'] );

	$css_classes = trim( implode( ' ', $classes ) );
	$has_submenu = count( $block->inner_blocks ) > 0;
	$is_active   = ! empty( $attributes['id'] ) && ( get_the_ID() === $attributes['id'] );

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
		$html .= ' href="' . esc_url( $attributes['url'] ) . '"';
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
		$html .= wp_kses(
			$attributes['label'],
			array(
				'code'   => array(),
				'em'     => array(),
				'img'    => array(
					'scale' => array(),
					'class' => array(),
					'style' => array(),
					'src'   => array(),
					'alt'   => array(),
				),
				's'      => array(),
				'span'   => array(
					'style' => array(),
				),
				'strong' => array(),
			)
		);
	}

	$html .= '</span>';

	if ( isset( $block->context['showSubmenuIcon'] ) && $block->context['showSubmenuIcon'] && $has_submenu ) {
		// The submenu icon can be hidden by a CSS rule on the Navigation Block.
		$html .= '<span class="wp-block-navigation__submenu-icon">' . block_core_navigation_link_render_submenu_icon() . '</span>';
	}

	$html .= '</a>';
	// End anchor tag content.

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
 * @param WP_Taxonomy|WP_Post_Type $entity post type or taxonomy entity.
 * @param string                   $kind string of value 'taxonomy' or 'post-type'.
 *
 * @return array
 */
function build_variation_for_navigation_link( $entity, $kind ) {
	$title       = '';
	$description = '';

	if ( property_exists( $entity->labels, 'item_link' ) ) {
		$title = $entity->labels->item_link;
	}
	if ( property_exists( $entity->labels, 'item_link_description' ) ) {
		$description = $entity->labels->item_link_description;
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
 * Register the navigation link block.
 *
 * @uses render_block_core_navigation()
 * @throws WP_Error An WP_Error exception parsing the block definition.
 */
function register_block_core_navigation_link() {
	$post_types = get_post_types( array( 'show_in_nav_menus' => true ), 'objects' );
	$taxonomies = get_taxonomies( array( 'show_in_nav_menus' => true ), 'objects' );

	// Use two separate arrays as a way to order the variations in the UI.
	// Known variations (like Post Link and Page Link) are added to the
	// `built_ins` array. Variations for custom post types and taxonomies are
	// added to the `variations` array and will always appear after `built-ins.
	$built_ins  = array();
	$variations = array();

	if ( $post_types ) {
		foreach ( $post_types as $post_type ) {
			$variation = build_variation_for_navigation_link( $post_type, 'post-type' );
			if ( $post_type->_builtin ) {
				$built_ins[] = $variation;
			} else {
				$variations[] = $variation;
			}
		}
	}
	if ( $taxonomies ) {
		foreach ( $taxonomies as $taxonomy ) {
			$variation = build_variation_for_navigation_link( $taxonomy, 'taxonomy' );
			if ( $taxonomy->_builtin ) {
				$built_ins[] = $variation;
			} else {
				$variations[] = $variation;
			}
		}
	}

	register_block_type_from_metadata(
		__DIR__ . '/navigation-link',
		array(
			'render_callback' => 'render_block_core_navigation_link',
			'variations'      => array_merge( $built_ins, $variations ),
		)
	);
}
add_action( 'init', 'register_block_core_navigation_link' );
