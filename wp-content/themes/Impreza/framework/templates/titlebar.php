<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Outputs page's titlebar
 *
 * (!) Should be called after the current $wp_query is already defined
 *
 * @var $show_title bool Default: taken from theme option or from post's meta-setting
 * @var $show_breadcrumbs bool Default: taken from theme option or from post's meta-setting
 * @var $show_prevnext bool Default: taken from theme option or from post's meta-setting
 * @var $title string Title to show. Default: current post title
 * @var $subtitle string Subtitle. Default: taken from post's 'us_titlebar_subtitle' meta-setting
 * @var $size string Title Bar Size. Default: taken from post's 'us_titlebar_size' meta-setting
 * @var $color_style string Title Bar Color Style. Default: taken from post's 'us_titlebar_color' meta-setting
 * @var $bg_image string Background Image. Default: taken from post's 'us_titlebar_image' meta-setting
 * @var $bg_imgsize string Background Image Styling. Default: taken from post's 'us_titlebar_image_size' meta-setting
 * @var $bg_parallax string Background Image Parallax setting. Default: taken from post's 'us_titlebar_image_parallax' meta-setting
 * @var $bg_overlay_color string Background Image Overlay Color. Default: taken from post's 'us_titlebar_overlay_color' meta-setting
 *
 * @action Before the template: 'us_before_template:templates/titlebar'
 * @action After the template: 'us_after_template:templates/titlebar'
 * @filter Template variables: 'us_template_vars:templates/titlebar'
 */

// Variables defaults and filtering
$supported_custom_post_types = us_get_option( 'custom_post_types_support', array() );
if ( is_singular( array_merge( array( 'page', 'product' ), $supported_custom_post_types ) ) ) {
	$titlebar_content = us_get_option( 'titlebar_content', 'all' );
} elseif ( is_singular( 'us_portfolio' ) ) {
	$titlebar_content = us_get_option( 'titlebar_portfolio_content', 'all' );
} elseif ( is_singular( 'post' ) ) {
	$titlebar_content = us_get_option( 'titlebar_post_content', 'hide' );
} elseif ( is_singular( 'tribe_events' ) OR is_post_type_archive( 'tribe_events' ) ) {
	$titlebar_content = 'hide';
} else {
	$titlebar_content = us_get_option( 'titlebar_archive_content', 'all' );
}

if ( is_singular( array_merge( array(
		'page',
		'us_portfolio',
		'product',
		'post',
	), $supported_custom_post_types ) ) AND usof_meta( 'us_titlebar_content' ) != ''
) {
	$titlebar_content = usof_meta( 'us_titlebar_content' );
}
// $titlebar_content may be one of 3 values: 'all', 'caption', 'hide'
$show_title = isset( $show_title ) ? $show_title : ( $titlebar_content != 'hide' );
if ( $show_title ) {
	$title = isset( $title ) ? $title : get_the_title();
	if ( ! isset( $subtitle ) ) {
		$subtitle = is_singular() ? usof_meta( 'us_titlebar_subtitle' ) : '';
	}
}
if ( ! isset( $show_breadcrumbs ) ) {
	$show_breadcrumbs = ( ! is_singular( 'us_portfolio' ) AND $titlebar_content == 'all' );
}
if ( ! isset( $show_prevnext ) ) {
	$show_prevnext = ( is_singular( 'us_portfolio' ) AND $titlebar_content == 'all' );
}

// No need to do other actions: titlebar will be hidden
if ( ! $show_title AND ! $show_breadcrumbs AND ! $show_prevnext ) {
	return;
}

$classes = '';
$bg_img_atts = '';

$size = isset( $size ) ? $size : '';
$color_style = isset( $color_style ) ? $color_style : '';
$bg_imgsize = isset( $bg_imgsize ) ? $bg_imgsize : '';
$bg_parallax = isset( $bg_parallax ) ? $bg_parallax : '';
$bg_overlay_color = isset( $bg_overlay_color ) ? $bg_overlay_color : '';

if ( is_singular() ) {
	$meta_size = usof_meta( 'us_titlebar_size' );
	if ( ! empty( $meta_size ) AND empty( $size ) ) {
		$size = $meta_size;
	}
	$meta_color_style = usof_meta( 'us_titlebar_color' );
	if ( $meta_color_style != '' AND empty( $color_style ) ) {
		$color_style = $meta_color_style;
	}

	$bg_image = ! empty( $bg_image ) ? $bg_image : usof_meta( 'us_titlebar_image' );
	$bg_imgsize = ! empty( $bg_imgsize ) ? $bg_imgsize : usof_meta( 'us_titlebar_image_size' );
	$bg_parallax = ! empty( $bg_parallax ) ? $bg_parallax : usof_meta( 'us_titlebar_image_parallax' );
	$bg_overlay_color = ! empty( $bg_overlay_color ) ? $bg_overlay_color : usof_meta( 'us_titlebar_overlay_color' );
}

// Theme-options defined settings
if ( is_singular( 'page', 'product' ) ) {
	$size = ! empty( $size ) ? $size : us_get_option( 'titlebar_size', 'large' );
	$color_style = ! empty( $color_style ) ? $color_style : us_get_option( 'titlebar_color', 'alternate' );
} elseif ( is_singular( 'us_portfolio' ) ) {
	$size = ! empty( $size ) ? $size : us_get_option( 'titlebar_portfolio_size', 'large' );
	$color_style = ! empty( $color_style ) ? $color_style : us_get_option( 'titlebar_portfolio_color', 'alternate' );
} elseif ( is_singular( 'post' ) ) {
	$size = ! empty( $size ) ? $size : us_get_option( 'titlebar_post_size', 'medium' );
	$color_style = ! empty( $color_style ) ? $color_style : us_get_option( 'titlebar_post_color', 'alternate' );
} else {
	$size = ! empty( $size ) ? $size : us_get_option( 'titlebar_archive_size', 'medium' );
	$color_style = ! empty( $color_style ) ? $color_style : us_get_option( 'titlebar_archive_color', 'alternate' );
}

if ( ! empty( $bg_image ) ) {
	$bg_image_src = wp_get_attachment_image_src( (int) $bg_image, 'full' );
	if ( $bg_image_src ) {
		$bg_image = $bg_image_src[0];
		$bg_img_atts .= ' data-img-width="' . $bg_image_src[1] . '" data-img-height="' . $bg_image_src[2] . '"';
	}
	
	if ( $bg_imgsize != '' ) {
		$classes .= ' imgsize_' . $bg_imgsize;
	}
}
if ( $bg_parallax == 'vertical' ) {
	$classes .= ' parallax_ver';
	wp_enqueue_script( 'us-parallax' );
} elseif ( $bg_parallax == 'vertical_reversed' ) {
	$classes .= ' parallax_ver parallaxdir_reversed';
	wp_enqueue_script( 'us-parallax' );
} elseif ( $bg_parallax == 'still' ) {
	$classes .= ' parallax_fixed';
} elseif ( $bg_parallax == 'horizontal' ) {
	$classes .= ' parallax_hor';
	wp_enqueue_script( 'us-hor-parallax' );
}

$classes .= ' size_' . $size . ' color_' . $color_style;

if ( $show_prevnext ) {
	$prevnext = us_get_post_prevnext();
}

$output = '<div class="l-titlebar' . $classes . '">';
if ( ! empty( $bg_image ) ) {
	$output .= '<div class="l-titlebar-img" style="background-image: url(' . $bg_image . ')"' . $bg_img_atts . '></div>';
}
if ( ! empty( $bg_overlay_color ) ) {
	$output .= '<div class="l-titlebar-overlay" style="background-color:' . $bg_overlay_color . '"></div>';
}
$output .= '<div class="l-titlebar-h"><div class="l-titlebar-content">';
if ( $show_title ) {
	$output .= ( $title != '' ) ? '<h1 itemprop="headline">' . $title . '</h1>' : '';
	if ( ! empty( $subtitle ) ) {
		$output .= '<p>' . $subtitle . '</p>';
	}
}
$output .= '</div>';
if ( $show_breadcrumbs ) {
	// TODO Create the us_get_breadcrumbs function instead
	ob_start();
	us_breadcrumbs();
	$output .= ob_get_clean();
}
if ( $show_prevnext AND ! empty( $prevnext ) ) {
	$output .= '<div class="g-nav"><div class="g-nav-list">';
	$keys = array( 'next', 'prev' );
	foreach ( $keys as $key ) {
		if ( isset( $prevnext[ $key ] ) ) {
			$item = $prevnext[ $key ];
			$output .= '<a class="g-nav-item to_' . $key . '" title="' . esc_attr( $item['title'] ) . '" href="' . $item['link'] . '"></a>';
		}
	}
	$output .= '</div></div>';
}
$output .= '</div></div>';

echo $output;
