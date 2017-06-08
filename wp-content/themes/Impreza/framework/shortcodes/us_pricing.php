<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Shortcode: us_pricing
 *
 * Dev note: if you want to change some of the default values or acceptable attributes, overload the shortcodes config.
 *
 * @var $shortcode string Current shortcode name
 * @var $shortcode_base string The original called shortcode name (differs if called an alias)
 * @var $content string Shortcode's inner content
 * @var $atts array Shortcode attributes
 *
 * @param $atts ['style'] string Table style: '1' / '2'
 * @param $atts ['items'] string Pricing table items
 * @param $atts ['el_class'] string Extra class name
 */

$atts = us_shortcode_atts( $atts, 'us_pricing' );

if ( empty( $atts['items'] ) ) {
	$atts['items'] = array();
} else {
	$atts['items'] = json_decode( urldecode( $atts['items'] ), TRUE );
	if ( ! is_array( $atts['items'] ) ) {
		$atts['items'] = array();
	}
}

$classes = ' style_' . $atts['style'];
$items_html = '';

if ( ! empty( $atts['el_class'] ) ) {
	$classes .= ' ' . $atts['el_class'];
}

if ( count( $atts['items'] ) > 0 ) {
	$classes .= ' items_' . count( $atts['items'] );
}

foreach ( $atts['items'] as $index => $item ) {
	/**
	 * Filtering the included items
	 *
	 * @param $item ['title'] string Item title
	 * @param $item ['type'] string Item type: 'default' / 'featured'
	 * @param $item ['price'] string Item price
	 * @param $item ['substring'] string Price substring
	 * @param $item ['features'] string Comma-separated list of features
	 * @param $item ['btn_text'] string Button label
	 * @param $item ['btn_color'] string Button color: 'primary' / 'secondary' / 'light' / 'contrast' / 'black' / 'white'
	 * @param $item ['btn_bg_color'] string Button background color
	 * @param $item ['btn_text_color'] string Button text color
	 * @param $item ['btn_size'] string Button size
	 * @param $item ['btn_style'] string Button style: 'raised' / 'flat'
	 * @param $item ['btn_icon'] string Button icon
	 * @param $item ['btn_iconpos'] string Icon position: 'left' / 'right'
	 * @param $item ['btn_link'] string Button link in a serialized format: 'url:http%3A%2F%2Fwordpress.org|title:WP%20Website|target:_blank|rel:nofollow'
	 */
	//$item = us_shortcode_atts( array_filter( $item ), 'us_pricing', 'items_atts' );
	$item['type'] = ( isset( $item['type'] ) ) ? $item['type'] : 'default';
	$item['btn_icon'] = ( isset( $item['btn_icon'] ) ) ? $item['btn_icon'] : '';
	$item['btn_link'] = ( isset( $item['btn_link'] ) ) ? $item['btn_link'] : '';

	$items_html .= '<div class="w-pricing-item type_' . $item['type'] . '"><div class="w-pricing-item-h"><div class="w-pricing-item-header">';
	if ( ! empty( $item['title'] ) ) {
		$items_html .= '<h5 class="w-pricing-item-title">' . $item['title'] . '</h5>';
	}
	if ( ! empty( $item['price'] ) OR ! empty( $item['substring'] ) ) {
		$items_html .= '<div class="w-pricing-item-price">' . $item['price'];
		if ( ! empty( $item['substring'] ) ) {
			$items_html .= '<small>' . $item['substring'] . '</small>';
		}
		$items_html .= '</div>';
	}
	$items_html .= '</div>';
	if ( ! empty( $item['features'] ) ) {
		$items_html .= '<ul class="w-pricing-item-features">';
		$features = explode( "\n", trim( $item['features'] ) );
		foreach ( $features as $feature ) {
			$items_html .= '<li class="w-pricing-item-feature">' . $feature . '</li>';
		}
		$items_html .= '</ul>';
	}
	if ( ! empty( $item['btn_text'] ) ) {
		$btn_classes = '';
		if ( ! empty( $item['btn_style'] ) ) {
			$btn_classes .= ' style_' . $item['btn_style'];
		}
		$btn_classes .= ' color_' . $item['btn_color'];
		$btn_inner_css = '';
		if ( $item['btn_color'] == 'custom' ) {
			if ( $item['btn_bg_color'] != '' ) {
				$btn_inner_css .= 'background-color: ' . $item['btn_bg_color'] . ';';
			}
			if ( $item['btn_text_color'] != '' ) {
				$btn_inner_css .= 'color: ' . $item['btn_text_color'] . ';';
			}
		}
		if ( ! empty( $item['btn_size'] ) AND $item['btn_size'] != '15px' ) {
			$btn_inner_css .= 'font-size: ' . $item['btn_size'] . ';';
		}

		$icon_html = '';
		$item['btn_icon'] = trim( $item['btn_icon'] );
		if ( $item['btn_icon'] != '' ) {
			$icon_html = '<i class="' . us_prepare_icon_class( $item['btn_icon'] ) . '"></i>';
			if ( ! empty( $item['btn_iconpos'] ) ) {
				$btn_classes .= ' icon_at' . $item['btn_iconpos'];
			} else {
				$btn_classes .= ' icon_atleft';
			}

		} else {
			$btn_classes .= ' icon_none';
		}
		$btn_link = us_vc_build_link( $item['btn_link'] );
		$btn_link_target = ( $btn_link['target'] == '_blank' ) ? ' target="_blank"' : '';
		$btn_link_rel = ( $btn_link['rel'] == 'nofollow' ) ? ' rel="nofollow"' : '';
		$btn_link_title = empty( $btn_link['title'] ) ? '' : ( ' title="' . esc_attr( $btn_link['title'] ) . '"' );
		$items_html .= '<div class="w-pricing-item-footer">';
		$items_html .= '<a class="w-btn' . $btn_classes . '" href="' . esc_url( $btn_link['url'] ) . '"' . $btn_link_target . $btn_link_rel . $btn_link_title;
		if ( ! empty( $btn_inner_css ) ) {
			$items_html .= ' style="' . $btn_inner_css . '"';
		}
		$items_html .= '>';
		$items_html .= $icon_html . '<span class="w-btn-label">' . $item['btn_text'] . '</span></a>';
		$items_html .= '</div>';
	}
	$items_html .= '</div></div>';
}

$output = '<div class="w-pricing' . $classes . '">' . $items_html . '</div>';
echo $output;
