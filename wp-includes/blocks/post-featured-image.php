<?php
/**
 * Server-side rendering of the `core/post-featured-image` block.
 *
 * @package WordPress
 */

/**
 * Renders the `core/post-featured-image` block on the server.
 *
 * @since 5.8.0
 *
 * @param array    $attributes Block attributes.
 * @param string   $content    Block default content.
 * @param WP_Block $block      Block instance.
 * @return string Returns the featured image for the current post.
 */
function render_block_core_post_featured_image( $attributes, $content, $block ) {
	if ( ! isset( $block->context['postId'] ) ) {
		return '';
	}
	$post_ID = $block->context['postId'];

	$is_link        = isset( $attributes['isLink'] ) && $attributes['isLink'];
	$size_slug      = isset( $attributes['sizeSlug'] ) ? $attributes['sizeSlug'] : 'post-thumbnail';
	$attr           = get_block_core_post_featured_image_border_attributes( $attributes );
	$overlay_markup = get_block_core_post_featured_image_overlay_element_markup( $attributes );

	if ( $is_link ) {
		if ( get_the_title( $post_ID ) ) {
			$attr['alt'] = trim( strip_tags( get_the_title( $post_ID ) ) );
		} else {
			$attr['alt'] = sprintf(
				// translators: %d is the post ID.
				__( 'Untitled post %d' ),
				$post_ID
			);
		}
	}

	$extra_styles = '';

	// Aspect ratio with a height set needs to override the default width/height.
	if ( ! empty( $attributes['aspectRatio'] ) ) {
		$extra_styles .= 'width:100%;height:100%;';
	} elseif ( ! empty( $attributes['height'] ) ) {
		$extra_styles .= "height:{$attributes['height']};";
	}

	if ( ! empty( $attributes['scale'] ) ) {
		$extra_styles .= "object-fit:{$attributes['scale']};";
	}
	if ( ! empty( $attributes['style']['shadow'] ) ) {
		$shadow_styles = wp_style_engine_get_styles( array( 'shadow' => $attributes['style']['shadow'] ) );

		if ( ! empty( $shadow_styles['css'] ) ) {
			$extra_styles .= $shadow_styles['css'];
		}
	}

	if ( ! empty( $extra_styles ) ) {
		$attr['style'] = empty( $attr['style'] ) ? $extra_styles : $attr['style'] . $extra_styles;
	}

	$featured_image = get_the_post_thumbnail( $post_ID, $size_slug, $attr );

	// Get the first image from the post.
	if ( $attributes['useFirstImageFromPost'] && ! $featured_image ) {
		$content_post = get_post( $post_ID );
		$content      = $content_post->post_content;
		$processor    = new WP_HTML_Tag_Processor( $content );

		/*
		 * Transfer the image tag from the post into a new text snippet.
		 * Because the HTML API doesn't currently expose a way to extract
		 * HTML substrings this is necessary as a workaround. Of note, this
		 * is different than directly extracting the IMG tag:
		 * - If there are duplicate attributes in the source there will only be one in the output.
		 * - If there are single-quoted or unquoted attributes they will be double-quoted in the output.
		 * - If there are named character references in the attribute values they may be replaced with their direct code points. E.g. `&hellip;` becomes `…`.
		 * In the future there will likely be a mechanism to copy snippets of HTML from
		 * one document into another, via the HTML Processor's `get_outer_html()` or
		 * equivalent. When that happens it would be appropriate to replace this custom
		 * code with that canonical code.
		 */
		if ( $processor->next_tag( 'img' ) ) {
			$tag_html = new WP_HTML_Tag_Processor( '<img>' );
			$tag_html->next_tag();
			foreach ( $processor->get_attribute_names_with_prefix( '' ) as $name ) {
				$tag_html->set_attribute( $name, $processor->get_attribute( $name ) );
			}
			$featured_image = $tag_html->get_updated_html();
		}
	}

	if ( ! $featured_image ) {
		return '';
	}

	if ( $is_link ) {
		$link_target    = $attributes['linkTarget'];
		$rel            = ! empty( $attributes['rel'] ) ? 'rel="' . esc_attr( $attributes['rel'] ) . '"' : '';
		$height         = ! empty( $attributes['height'] ) ? 'style="' . esc_attr( safecss_filter_attr( 'height:' . $attributes['height'] ) ) . '"' : '';
		$featured_image = sprintf(
			'<a href="%1$s" target="%2$s" %3$s %4$s>%5$s%6$s</a>',
			get_the_permalink( $post_ID ),
			esc_attr( $link_target ),
			$rel,
			$height,
			$featured_image,
			$overlay_markup
		);
	} else {
		$featured_image = $featured_image . $overlay_markup;
	}

	$aspect_ratio = ! empty( $attributes['aspectRatio'] )
		? esc_attr( safecss_filter_attr( 'aspect-ratio:' . $attributes['aspectRatio'] ) ) . ';'
		: '';
	$width        = ! empty( $attributes['width'] )
		? esc_attr( safecss_filter_attr( 'width:' . $attributes['width'] ) ) . ';'
		: '';
	$height       = ! empty( $attributes['height'] )
		? esc_attr( safecss_filter_attr( 'height:' . $attributes['height'] ) ) . ';'
		: '';
	if ( ! $height && ! $width && ! $aspect_ratio ) {
		$wrapper_attributes = get_block_wrapper_attributes();
	} else {
		$wrapper_attributes = get_block_wrapper_attributes( array( 'style' => $aspect_ratio . $width . $height ) );
	}
	return "<figure {$wrapper_attributes}>{$featured_image}</figure>";
}

/**
 * Generate markup for the HTML element that will be used for the overlay.
 *
 * @since 6.1.0
 *
 * @param array $attributes Block attributes.
 *
 * @return string HTML markup in string format.
 */
function get_block_core_post_featured_image_overlay_element_markup( $attributes ) {
	$has_dim_background  = isset( $attributes['dimRatio'] ) && $attributes['dimRatio'];
	$has_gradient        = isset( $attributes['gradient'] ) && $attributes['gradient'];
	$has_custom_gradient = isset( $attributes['customGradient'] ) && $attributes['customGradient'];
	$has_solid_overlay   = isset( $attributes['overlayColor'] ) && $attributes['overlayColor'];
	$has_custom_overlay  = isset( $attributes['customOverlayColor'] ) && $attributes['customOverlayColor'];
	$class_names         = array( 'wp-block-post-featured-image__overlay' );
	$styles              = array();

	if ( ! $has_dim_background ) {
		return '';
	}

	// Apply border classes and styles.
	$border_attributes = get_block_core_post_featured_image_border_attributes( $attributes );

	if ( ! empty( $border_attributes['class'] ) ) {
		$class_names[] = $border_attributes['class'];
	}

	if ( ! empty( $border_attributes['style'] ) ) {
		$styles[] = $border_attributes['style'];
	}

	// Apply overlay and gradient classes.
	if ( $has_dim_background ) {
		$class_names[] = 'has-background-dim';
		$class_names[] = "has-background-dim-{$attributes['dimRatio']}";
	}

	if ( $has_solid_overlay ) {
		$class_names[] = "has-{$attributes['overlayColor']}-background-color";
	}

	if ( $has_gradient || $has_custom_gradient ) {
		$class_names[] = 'has-background-gradient';
	}

	if ( $has_gradient ) {
		$class_names[] = "has-{$attributes['gradient']}-gradient-background";
	}

	// Apply background styles.
	if ( $has_custom_gradient ) {
		$styles[] = sprintf( 'background-image: %s;', $attributes['customGradient'] );
	}

	if ( $has_custom_overlay ) {
		$styles[] = sprintf( 'background-color: %s;', $attributes['customOverlayColor'] );
	}

	return sprintf(
		'<span class="%s" style="%s" aria-hidden="true"></span>',
		esc_attr( implode( ' ', $class_names ) ),
		esc_attr( safecss_filter_attr( implode( ' ', $styles ) ) )
	);
}

/**
 * Generates class names and styles to apply the border support styles for
 * the Post Featured Image block.
 *
 * @since 6.1.0
 *
 * @param array $attributes The block attributes.
 * @return array The border-related classnames and styles for the block.
 */
function get_block_core_post_featured_image_border_attributes( $attributes ) {
	$border_styles = array();
	$sides         = array( 'top', 'right', 'bottom', 'left' );

	// Border radius.
	if ( isset( $attributes['style']['border']['radius'] ) ) {
		$border_styles['radius'] = $attributes['style']['border']['radius'];
	}

	// Border style.
	if ( isset( $attributes['style']['border']['style'] ) ) {
		$border_styles['style'] = $attributes['style']['border']['style'];
	}

	// Border width.
	if ( isset( $attributes['style']['border']['width'] ) ) {
		$border_styles['width'] = $attributes['style']['border']['width'];
	}

	// Border color.
	$preset_color           = array_key_exists( 'borderColor', $attributes ) ? "var:preset|color|{$attributes['borderColor']}" : null;
	$custom_color           = $attributes['style']['border']['color'] ?? null;
	$border_styles['color'] = $preset_color ? $preset_color : $custom_color;

	// Individual border styles e.g. top, left etc.
	foreach ( $sides as $side ) {
		$border                 = $attributes['style']['border'][ $side ] ?? null;
		$border_styles[ $side ] = array(
			'color' => isset( $border['color'] ) ? $border['color'] : null,
			'style' => isset( $border['style'] ) ? $border['style'] : null,
			'width' => isset( $border['width'] ) ? $border['width'] : null,
		);
	}

	$styles     = wp_style_engine_get_styles( array( 'border' => $border_styles ) );
	$attributes = array();
	if ( ! empty( $styles['classnames'] ) ) {
		$attributes['class'] = $styles['classnames'];
	}
	if ( ! empty( $styles['css'] ) ) {
		$attributes['style'] = $styles['css'];
	}
	return $attributes;
}

/**
 * Registers the `core/post-featured-image` block on the server.
 *
 * @since 5.8.0
 */
function register_block_core_post_featured_image() {
	register_block_type_from_metadata(
		__DIR__ . '/post-featured-image',
		array(
			'render_callback' => 'render_block_core_post_featured_image',
		)
	);
}
add_action( 'init', 'register_block_core_post_featured_image' );
