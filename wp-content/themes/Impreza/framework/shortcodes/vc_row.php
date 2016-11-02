<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Shortcode: vc_row
 *
 * Overloaded by UpSolution custom implementation to allow creating fullwidth sections and provide lots of additional
 * features.
 *
 * Dev note: if you want to change some of the default values or acceptable attributes, overload the shortcodes config.
 *
 * @var $shortcode string Current shortcode name
 * @var $shortcode_base string The original called shortcode name (differs if called an alias)
 * @var $content string Shortcode's inner content
 * @var $atts array Shortcode attributes
 *
 * @param $atts ['content_placement'] string Columns Content Position: 'default' / 'top' / 'middle' / 'bottom'
 * @param $atts ['columns_type'] string Columns type: 'small' / 'medium' / 'large' / 'none'
 * @param $atts ['height'] string Height type. Possible values: 'small' / 'medium' / 'large' / 'huge' / 'auto' /  'full'
 * @param $atts ['valign'] string Vertical align for full-height sections: '' / 'center'
 * @param $atts ['width'] string Section width: '' / 'full'
 * @param $atts ['color_scheme'] string Color scheme: '' / 'alternate' / 'primary' / 'secondary' / 'custom'
 * @param $atts ['us_bg_color'] string
 * @param $atts ['us_text_color'] string
 * @param $atts ['us_bg_image'] int Background image ID (from WordPress media)
 * @param $atts ['us_bg_size'] string Background size: 'cover' / 'contain' / 'initial'
 * @param $atts ['us_bg_repeat'] string Background size: 'repeat' / 'repeat-x' / 'repeat-y' / 'no-repeat'
 * @param $atts ['us_bg_pos'] string Background position: 'top left' / 'top center' / 'top right' / 'center left' / 'center center' / 'center right' /  'bottom left' / 'bottom center' / 'bottom right'
 * @param $atts ['us_bg_parallax'] string Parallax type: '' / 'vertical' / 'horizontal' / 'still'
 * @param $atts ['us_bg_parallax_width'] string Parallax background width: '110' / '120' / '130' / '140' / '150'
 * @param $atts ['us_bg_parallax_reverse'] bool Reverse vertival parllax effect?
 * @param $atts ['us_bg_video'] string Link to video file
 * @param $atts ['us_bg_overlay_color'] string
 * @param $atts ['el_id'] string
 * @param $atts ['el_class'] string
 * @param $atts ['disable_element'] string
 * @param $atts ['css'] string
 */

$atts = us_shortcode_atts( $atts, 'vc_row' );

if ( 'yes' === $atts['disable_element'] ) {
	return '';
}

// .l-section container additional classes and inner CSS-styles
$classes = '';
$inner_css = '';

$classes .= ' height_' . $atts['height'];

if ( $atts['height'] == 'full' AND ! empty( $atts['valign'] ) ) {
	$classes .= ' valign_' . $atts['valign'];
}

if ( $atts['width'] == 'full' ) {
	$classes .= ' width_full';
}

if ( $atts['color_scheme'] != '' ) {
	$classes .= ' color_' . $atts['color_scheme'];
	if ( $atts['color_scheme'] == 'custom' ) {
		// Custom background
		if ( $atts['us_bg_color'] != '' ) {
			$inner_css .= 'background-color: ' . $atts['us_bg_color'] . ';';
		}
		if ( $atts['us_text_color'] != '' ) {
			$inner_css .= ' color: ' . $atts['us_text_color'] . ';';
		}
	}
}

$bg_image_html = '';
if ( ! empty( $atts['us_bg_image'] ) ) {
	$bg_image_url = '';
	$bg_img_atts = '';
	if ( is_numeric( $atts['us_bg_image'] ) ) {
		$wp_image = wp_get_attachment_image_src( (int) $atts['us_bg_image'], 'full' );
		if ( $wp_image != NULL ) {
			$bg_image_url = $wp_image[0];
			$bg_img_atts .= ' data-img-width="' . $wp_image[1] . '" data-img-height="' . $wp_image[2] . '"';
		}
	} else {
		$bg_image_url = $atts['us_bg_image'];
	}
	$classes .= ' with_img';
	$bg_image_inner_css = 'background-image: url(' . $bg_image_url . ');';

	if ( $atts['us_bg_pos'] != 'center center' ) {
		$bg_image_inner_css .= 'background-position: ' . $atts['us_bg_pos'] . ';';
	}
	if ( $atts['us_bg_repeat'] != 'repeat' ) {
		$bg_image_inner_css .= 'background-repeat: ' . $atts['us_bg_repeat'] . ';';
	}
	if ( $atts['us_bg_size'] != 'cover' ) {
		$bg_image_inner_css .= 'background-size: ' . $atts['us_bg_size'] . ';';
	}
	$bg_image_html = '<div class="l-section-img" style="' . $bg_image_inner_css .'"' . $bg_img_atts . '></div>';
}

$bg_video_html = '';
if ( $atts['us_bg_video'] != '' ) {
	$classes .= ' with_video';
	$bg_video_html = '<div class="l-section-video"><video muted loop autoplay preload="auto">';
	$video_ext = 'mp4'; //use mp4 as default extension
	$file_path_info = pathinfo( $atts['us_bg_video'] );
	if ( isset( $file_path_info['extension'] ) ) {
		if ( in_array( $file_path_info['extension'], array( 'ogg', 'ogv' ) ) ) {
			$video_ext = 'ogg';
		} elseif ( $file_path_info['extension'] == 'webm' ) {
			$video_ext = 'webm';
		}
	}
	$bg_video_html .= '<source type="video/' . $video_ext .'" src="' . $atts['us_bg_video'] . '" />';
	$bg_video_html .= '</video></div>';
} else {
	if ( $atts['us_bg_parallax'] == 'vertical' ) {
		$classes .= ' parallax_ver';
		wp_enqueue_script( 'us-parallax' );
		if ( $atts['us_bg_parallax_reverse'] ) {
			$classes .= ' parallaxdir_reversed';
		}

		if ( in_array( $atts['us_bg_pos'], array( 'top right', 'center right', 'bottom right' ) ) ) {
			$classes .= ' parallax_xpos_right';
		} elseif ( in_array( $atts['us_bg_pos'], array( 'top left', 'center left', 'bottom left' ) ) ) {
			$classes .= ' parallax_xpos_left';
		}
		
	} elseif ( $atts['us_bg_parallax'] == 'fixed' OR $atts['us_bg_parallax'] == 'still' ) {
		$classes .= ' parallax_fixed';
	} elseif ( $atts['us_bg_parallax'] == 'horizontal' ) {
		$classes .= ' parallax_hor';
		wp_enqueue_script( 'us-hor-parallax' );
		$classes .= ' bgwidth_' . $atts['us_bg_parallax_width'];
	}
}

$bg_overlay_html = '';
if ( ! empty( $atts['us_bg_overlay_color'] ) ) {
	$classes .= ' with_overlay';
	$bg_overlay_html = '<div class="l-section-overlay" style="background-color: ' . $atts['us_bg_overlay_color'] . '"></div>';
}

// Additional class set by a user in a shortcode attributes
if ( ! empty( $atts['el_class'] ) ) {
	$classes .= ' ' . $atts['el_class'];
}

if ( ! empty( $atts['css'] ) AND preg_match( '~\{([^\}]+?)\;?\}~', $atts['css'], $matches ) ) {
	// We cannot use VC's method directly for rows: as it uses !important values, so we're moving the defined css
	// that don't duplicate the theme's features to inline style attribute.
	$vc_css_rules = array_map( 'trim', explode( ';', $matches[1] ) );
	$overloaded_params = array( 'background', 'background-position', 'background-repeat', 'background-size' );
	foreach ( $vc_css_rules as $vc_css_rule ) {
		$vc_css_rule = explode( ':', $vc_css_rule );
		if ( count( $vc_css_rule ) == 2 AND ! in_array( $vc_css_rule[0], $overloaded_params ) ) {
			$inner_css .= $vc_css_rule[0] . ':' . $vc_css_rule[1] . ';';
		}
	}
}
$classes = apply_filters( 'vc_shortcodes_css_class', $classes, $shortcode_base, $atts );

// Preparing html output
$output = '<section class="l-section wpb_row' . $classes . '"';
if ( ! empty( $atts['el_id'] ) ) {
	$output .= ' id="' . $atts['el_id'] . '"';
}

if ( ! empty( $inner_css ) ) {
	$output .= ' style="' . $inner_css . '"';
}
$output .= '>' . $bg_image_html . $bg_video_html . $bg_overlay_html . '<div class="l-section-h i-cf">';

$inner_output = do_shortcode( $content );

// If the row has no inner rows, preparing wrapper for inner columns
if ( substr( $inner_output, 0, 18 ) != '<div class="g-cols' ) {
	// Offset modificator
	$cols_class_name = ' offset_' . $atts['columns_type'];
	if ( ! empty( $atts['content_placement'] ) AND $atts['content_placement'] != 'default' ) {
		$cols_class_name .= ' valign_' . $atts['content_placement'];
	}
	$output .= '<div class="g-cols' . $cols_class_name . '">' . $inner_output . '</div>';
} else {
	$output .= $inner_output;
}

$output .= '</div></section>';

echo $output;
