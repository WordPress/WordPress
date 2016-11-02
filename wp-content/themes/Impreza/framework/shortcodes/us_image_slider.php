<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Shortcode: us_image_slider
 *
 * Dev note: if you want to change some of the default values or acceptable attributes, overload the shortcodes config.
 *
 * @var $shortcode string Current shortcode name
 * @var $shortcode_base string The original called shortcode name (differs if called an alias)
 * @var $content string Shortcode's inner content
 * @var $atts array Shortcode attributes
 *
 * @param $atts ['ids'] string Comma-separated list of image IDs (from media library)
 * @param $atts ['arrows'] string Navigation arrows: 'always' / 'hover' / 'hide'
 * @param $atts ['nav'] string Additional navigation: 'none' / 'dots' / 'thumbs'
 * @param $atts ['transition'] string Transition effect: 'slide' / 'crossfade'
 * @param $atts ['autoplay'] bool Enable auto-rotation?
 * @param $atts ['autoplay_period'] int Auto-rotation period (in milliseconds)
 * @param $atts ['fullscreen'] bool Allow fullscreen view?
 * @param $atts ['orderby'] string Elements order: '' / 'rand'
 * @param $atts ['img_size'] string Images size: 'large' / 'medium' / 'thumbnail' / 'full'
 * @param $atts ['img_fit'] bool How to fim an image: 'scaledown' / 'contain' / 'cover'
 * @param $atts ['frame'] string Frame type: 'none' / 'phone6-1' / 'phone6-2' / 'phone6-3' / 'phone6-4'
 * @param $atts ['meta'] bool Show items titles and description?
 * @param $atts ['el_class'] string Extra class name
 */

$atts = us_shortcode_atts( $atts, 'us_image_slider' );

if ( empty( $atts['ids'] ) ) {
	return;
}

global $us_image_slider_index;
// Image sliders indexes start from 1
$us_image_slider_index = isset( $us_image_slider_index ) ? ( $us_image_slider_index + 1 ) : 1;

$classes = '';
$frame_classes = '';

// Royal Slider options
$js_options = array(
	'transitionSpeed' => 300,
	'loopRewind' => TRUE,
	'slidesSpacing' => 0,
	'imageScalePadding' => 0,
	'numImagesToPreload' => 2,
	'arrowsNav' => ( $atts['arrows'] != 'hide' ),
	'arrowsNavAutoHide' => ( $atts['arrows'] == 'hover' ),
	'transitionType' => ( $atts['transition'] == 'crossfade' ) ? 'fade' : 'move',
    'block' => array(
		'moveEffect' => 'none',
		'speed' => 300,
	),
);
if ( $atts['nav'] == 'dots' ) {
	$js_options['controlNavigation'] = 'bullets';
} elseif ( $atts['nav'] == 'thumbs' ) {
	$js_options['controlNavigation'] = 'thumbnails';
} else {
	$js_options['controlNavigation'] = 'none';
}

if ( $atts['autoplay'] AND $atts['autoplay_period'] ) {
	$js_options['autoplay'] = array(
		'enabled' => TRUE,
		'pauseOnHover' => TRUE,
		'delay' => intval( $atts['autoplay_period'] ),
	);
}

if ( $atts['fullscreen'] ) {
	$js_options['fullscreen'] = array(
		'enabled' => TRUE,
	);
}

if ( $atts['img_fit'] == 'contain' ) {
	$js_options['imageScaleMode'] = 'fit';
} elseif ( $atts['img_fit'] == 'cover' ) {
	$js_options['imageScaleMode'] = 'fill';
} else/*if ( $atts['img_fit'] == 'scaledown' )*/ {
	$js_options['imageScaleMode'] = 'fit-if-smaller';
}

if ( ! in_array( $atts['img_size'], get_intermediate_image_sizes() ) ) {
	$atts['img_size'] = 'full';
}

// Getting images
$query_args = array(
	'include' => $atts['ids'],
	'post_status' => 'inherit',
	'post_type' => 'attachment',
	'post_mime_type' => 'image',
	'orderby' => 'post__in',
	'numberposts' => -1,
);

if ( $atts['orderby'] == 'rand' ) {
	$query_args['orderby'] = 'rand';
}
$attachments = get_posts( $query_args );
if ( ! is_array( $attachments ) OR empty( $attachments ) ) {
	return;
}

if ( $atts['frame'] != '' AND $atts['frame'] != 'none') {
	$classes .= ' us-frame-wrapper';
	$frame_classes .= ' ' . $atts['frame'];
}

if ( $atts['el_class'] != '' ) {
	$classes .= ' ' . $atts['el_class'];
}

$i = 1;
$data_ratio = NULL;
$images_html = '';
foreach ( $attachments as $index => $attachment ) {
	$image = wp_get_attachment_image_src( $attachment->ID, $atts['img_size'] );
	if ( ! $image ) {
		continue;
	}
	if ( ! isset( $js_options['autoScaleSlider'] ) ) {
		$js_options['autoScaleSlider'] = TRUE;
		$js_options['autoScaleSliderWidth'] = $image[1];
		$js_options['autoScaleSliderHeight'] = $image[2];
		$js_options['fitInViewport'] = FALSE;
	}
	$full_image_attr = '';
	if ( $atts['fullscreen'] ) {
		$full_image = wp_get_attachment_image_src( $attachment->ID, 'full' );
		if ( ! $full_image ) {
			$full_image = $image;
		}
		$full_image_attr = ' data-rsBigImg="' . $full_image[0] . '"';
	}

	$image_alt = trim( strip_tags( get_post_meta( $attachment->ID, '_wp_attachment_image_alt', true ) ) );
	if ( empty( $image_alt ) )
		$image_alt = trim( strip_tags( $attachment->post_excerpt ) ); // If not, Use the Caption
	if ( empty( $image_alt ) )
		$image_alt = trim( strip_tags( $attachment->post_title ) ); // Finally, use the title

	$images_html .= '<div class="rsContent">';
	$images_html .= '<img class="rsImg" data-rsw="' . $image[1] . '" data-rsh="' . $image[2] . '"' . $full_image_attr . ' src="' . $image[0] . '">';
	if ( $atts['nav'] == 'thumbs' ) {
		$images_html .= wp_get_attachment_image( $attachment->ID, 'thumbnail', FALSE, array(
			'class' => 'rsTmb',
		) );
	}
	if ( $atts['meta'] ) {
		$images_html .= '<div class="rsABlock" data-fadeEffect="false" data-moveEffect="none">';
		if ( $image_alt != '' ) {
			$images_html .= '<div class="w-slider-item-title">' . $image_alt . '</div>';
		}
		if ( $attachment->post_content != '' ) {
			$images_html .= '<div class="w-slider-item-description">' . $attachment->post_content . '</div>';
		}
		$images_html .= '</div>';
	}
	$images_html .= '</div>';
}

// We need Roayl Slider script for this
wp_enqueue_script( 'us-royalslider' );
wp_enqueue_style( 'us-royalslider' );

$output = '<div class="w-slider' . $classes . '">';
$output .= '<div class="us-frame' . $frame_classes . '">';
$output .= '<div class="royalSlider rsDefault">' . $images_html . '</div>';
$output .= '<div class="w-slider-json"' . us_pass_data_to_js( $js_options ) . '></div>';
$output .= '</div>';
$output .= '</div>';

echo $output;
