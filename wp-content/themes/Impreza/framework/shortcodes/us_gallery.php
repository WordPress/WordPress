<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Shortcode: us_gallery
 *
 * Dev note: if you want to change some of the default values or acceptable attributes, overload the shortcodes config.
 *
 * @var $shortcode string Current shortcode name
 * @var $shortcode_base string The original called shortcode name (differs if called an alias)
 * @var $content string Shortcode's inner content
 * @var $atts array Shortcode attributes
 *
 * @param $atts ['ids'] string Comma-separated list of attachment ids
 * @param $atts ['columns'] int Number of columns
 * @param $atts ['layout'] string Gallery layout: 'default' / 'masonry'
 * @param $atts ['masonry'] bool Enable masonry layout? (masonry checkbox used in WP gallery)
 * @param $atts ['orderby'] string Elements order: '' / 'rand'
 * @param $atts ['indents'] bool Add indents between items?
 * @param $atts ['meta'] bool Show items titles and description?
 * @param $atts ['meta_style'] string Title and Description Style: 'simple' / 'modern'
 * @param $atts ['el_class'] string Extra class name
 */

// Translating attributes from [gallery] to [us_gallery] format (may be used in both shortcodes
$link_type = 'media';
if ( $shortcode_base == 'gallery' ) {
	if ( ! isset( $atts['columns'] ) OR empty( $atts['columns'] ) ) {
		// Default [gallery] shortcode has 3 columns by default
		$atts['columns'] = '3';
	}
	if ( ! isset( $atts['link'] ) ) {
		$link_type = 'attachment';
	} elseif ( $atts['link'] == 'none' ) {
		$link_type = 'none';
	}
	if ( isset( $atts['indents'] ) ) {
		$atts['indents'] = ( $atts['indents'] == 'true' );
	}
	if ( isset( $atts['meta'] ) ) {
		$atts['meta'] = ( $atts['meta'] == 'true' );
	}
}
if ( ! isset( $atts['ids'] ) OR empty( $atts['ids'] ) ) {
	if ( isset( $atts['include'] ) AND ! empty( $atts['include'] ) ) {
		$atts['ids'] = $atts['include'];
	} else {
		if ( ! isset( $atts['id'] ) OR empty( $atts['id'] ) ) {
			// Default fallback as from https://codex.wordpress.org/Gallery_Shortcode
			$atts['id'] = get_the_ID();
		}
		$query_args = array(
			'post_parent' => $atts['id'],
			'post_status' => 'inherit',
			'post_type' => 'attachment',
			'post_mime_type' => 'image',
			'posts_per_page' => - 1,
			'numberposts' => - 1,
		);
		if ( isset( $atts['exclude'] ) AND ! empty( $atts['exclude'] ) ) {
			$query_args['exclude'] = $atts['exclude'];
		}
		if ( isset( $atts['orderby'] ) AND in_array( $atts['orderby'], array( 'title', 'post_date', 'ID' ) ) ) {
			$query_args['orderby'] = $atts['orderby'];
			if ( ! isset( $atts['order'] ) OR empty( $atts['order'] ) ) {
				$atts['order'] = ( $atts['orderby'] == 'post_date' ) ? 'DESC' : 'ASC';
			}
			$query_args['order'] = ( strtoupper( $atts['order'] ) == 'ASC' ) ? 'ASC' : 'DESC';
		}
		$atts['ids'] = array();
		foreach ( get_posts( $query_args ) as $post ) {
			$atts['ids'][] = $post->ID;
		}
		$atts['ids'] = implode( ',', $atts['ids'] );
	}
}

$atts = us_shortcode_atts( $atts, 'us_gallery' );

if ( empty( $atts['ids'] ) ) {
	return;
}

global $us_gallery_index;
// Gallery indexes start from 1
$us_gallery_index = isset( $us_gallery_index ) ? ( $us_gallery_index + 1 ) : 1;

$classes = '';

$atts['columns'] = intval( $atts['columns'] );
if ( $atts['columns'] < 1 OR $atts['columns'] > 10 ) {
	$atts['columns'] = 6;
}

// Masonry checkbox used in WP gallery
if ( $atts['layout'] == 'default' AND $atts['masonry'] == 'true' ) {
	$atts['layout'] = 'masonry';
}
if ( $atts['layout'] == 'masonry' AND $atts['columns'] > 1 ) {
	// We'll need the isotope script for this
	wp_enqueue_script( 'us-isotope' );
	$tnail_size = ( $atts['columns'] < 8 ) ? 'tnail-masonry' : 'medium';
} else/*if($atts['layout'] == 'default')*/ {
	if ( $atts['columns'] == 1 ) {
		$tnail_size = 'full';
	} elseif ( $atts['columns'] < 5 ) {
		$tnail_size = 'tnail-1x1';
	} elseif ( $atts['columns'] < 8 ) {
		$tnail_size = 'tnail-1x1-small';
	} else {
		$tnail_size = 'thumbnail';
	}
}
$classes .= ' layout_' . $atts['layout'];
$classes .= ' cols_' . $atts['columns'];

if ( $atts['indents'] ) {
	$classes .= ' with_indents';
}
if ( ! empty( $atts['el_class'] ) ) {
	$classes .= ' ' . $atts['el_class'];
}
if ( ! empty( $atts['meta_style'] ) ) {
	$classes .= ' style_' . $atts['meta_style'];
}

// Getting images
$query_args = array(
	'include' => $atts['ids'],
	'post_status' => 'inherit',
	'post_type' => 'attachment',
	'post_mime_type' => 'image',
	'orderby' => 'post__in',
);
if ( $atts['orderby'] == 'rand' ) {
	$query_args['orderby'] = 'rand';
}
$attachments = get_posts( $query_args );
if ( ! is_array( $attachments ) OR empty( $attachments ) ) {
	return;
}

// Gallery shortcode usage in feeds
if ( is_feed() ) {
	$output = "\n";
	foreach ( $attachments as $attachment ) {
		$output .= wp_get_attachment_link( $attachment->ID, 'thumbnail', TRUE ) . "\n";
	}

	return $output;
}

$classes .= ' link_' . $link_type;

$classes = apply_filters( 'us_gallery_listing_classes', $classes );

$output = '<div class="w-gallery' . $classes . '"><div class="w-gallery-list">';

$item_tag_name = ( $link_type == 'none' ) ? 'div' : 'a';
foreach ( $attachments as $index => $attachment ) {

	$title = trim( strip_tags( get_post_meta( $attachment->ID, '_wp_attachment_image_alt', TRUE ) ) );
	if ( empty( $title ) ) {
		// If not, Use the Caption
		$title = trim( strip_tags( $attachment->post_excerpt ) );
	}
	if ( empty( $title ) ) {
		// Finally, use the title
		$title = trim( strip_tags( $attachment->post_title ) );
	}

	$output .= '<' . $item_tag_name . ' class="w-gallery-item order_' . ( $index + 1 );
	$output .= apply_filters( 'us_gallery_listing_item_classes', '' );
	$output .= '"';
	if ( $link_type == 'media' ) {
		$output .= ' href="' . wp_get_attachment_url( $attachment->ID ) . '" title="' . esc_attr( $title ) . '"';
	} elseif ( $link_type == 'attachment' ) {
		$output .= ' href="' . get_attachment_link( $attachment->ID ) . '" title="' . esc_attr( $title ) . '"';
	}
	$output .= '>';
	$output .= '<div class="w-gallery-item-img">';
	$output .= wp_get_attachment_image( $attachment->ID, $tnail_size, FALSE );
	$output .= '</div>';
	if ( $atts['meta'] ) {
		$output .= '<div class="w-gallery-item-meta">';
		$output .= '<div class="w-gallery-item-title">' . $title . '</div>';
		$output .= ( ! empty( $attachment->post_content ) ) ? '<div class="w-gallery-item-description">' . $attachment->post_content . '</div>' : '';
		$output .= '</div>';
	}
	$output .= '</' . $item_tag_name . '>';
}

$output .= "</div></div>\n";

echo $output;
