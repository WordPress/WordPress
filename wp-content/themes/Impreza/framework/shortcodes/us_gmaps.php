<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Shortcode: us_gmaps
 *
 * Dev note: if you want to change some of the default values or acceptable attributes, overload the shortcodes config.
 *
 * @var $shortcode string Current shortcode name
 * @var $shortcode_base string The original called shortcode name (differs if called an alias)
 * @var $content string Shortcode's inner content
 * @var $atts array Shortcode attributes
 *
 * @param $atts ['marker_address'] string Marker 1 address
 * @param $atts ['marker_text'] string Marker 1 text
 * @param $atts ['show_infowindow'] bool Show Marker's InfoWindow
 * @param $atts ['type'] string Map type: 'roadmap' / 'satellite' / 'hybrid' / 'terrain'
 * @param $atts ['height'] int Map height
 * @param $atts ['zoom'] int Map zoom
 * @param $atts ['latitude'] float Map latitude
 * @param $atts ['longitude'] float Map longitude
 * @param $atts ['markers'] string Additional markers
 * @param $atts ['custom_marker_img'] int Custom marker image (from WordPress media)
 * @param $atts ['custom_marker_size'] int Custom marker size
 * @param $atts ['hide_controls'] bool Hide all map controls
 * @param $atts ['disable_dragging'] bool Disable dragging on touch screens
 * @param $atts ['disable_zoom'] bool Disable map zoom on mouse wheel scroll
 * @param $atts ['map_bg_color'] string Map Background Color
 * @param $atts ['api_key'] string API Key
 * @param $atts ['el_class'] string Extra class name
 * @param $atts ['map_style_json'] string Map Style
 *
 * @filter 'us_gmaps_js_options' Allows to filter options, passed to JavaScript
 */
$atts = us_shortcode_atts( $atts, 'us_gmaps' );

// Decoding base64-encoded HTML attributes
if ( ! empty( $atts['marker_text'] ) ) {
	$atts['marker_text'] = rawurldecode( base64_decode( $atts['marker_text'] ) );
}

$classes = '';
$inner_css = '';
$script_options = array();

if ( $atts['el_class'] != '' ) {
	$classes .= ' ' . $atts['el_class'];
}

if ( ! in_array( $atts['custom_marker_size'], array( 20, 30, 40, 50, 60, 70, 80 ) ) ) {
	$atts['custom_marker_size'] = 20;
}

if ( ! in_array( $atts['zoom'], array( 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20 ) ) ) {
	$atts['zoom'] = 14;
}

global $us_gmaps_index;
// Map indexes start from 1
$us_gmaps_index = isset( $us_gmaps_index ) ? ( $us_gmaps_index + 1 ) : 1;

// Coords-based location
if ( ! empty( $atts['latitude'] ) AND ! empty( $atts['longitude'] ) ) {
	$script_options['latitude'] = $atts['latitude'];
	$script_options['longitude'] = $atts['longitude'];
} elseif ( $atts['marker_address'] != '' ) {
	$script_options['address'] = $atts['marker_address'];
} else {
	return NULL;
}
$script_options['markers'] = array(
	array_merge( $script_options, array(
		'html' => $atts['marker_text'],
		'infowindow' => $atts['show_infowindow']
	) ),
);


if ( empty( $atts['markers'] ) ) {
	$atts['markers'] = array();
} else {
	$atts['markers'] = json_decode( urldecode( $atts['markers'] ), TRUE );
	if ( ! is_array( $atts['markers'] ) ) {
		$atts['markers'] = array();
	}
}

foreach ( $atts['markers'] as $index => $marker ) {
	/**
	 * Filtering the included markers
	 *
	 * @param $marker ['marker_address'] string Marker Address
	 * @param $marker ['marker_text'] string Marker Text
	 * @param $marker ['marker_latitude'] string Marker Latitude (optional)
	 * @param $marker ['marker_longitude'] string Marker Longitude (optional)
	 */


	if ( ( ! empty( $marker['marker_text'] ) AND ! empty( $marker['marker_address'] ) ) OR ( ! empty( $marker['marker_text'] ) AND ! empty( $marker['marker_latitude'] ) AND ! empty( $marker['marker_longitude'] ) ) ) {
		$script_options['markers'][] = array(
			'html' => $marker['marker_text'],
			'address' => ( ! empty( $marker['marker_address'] ) ) ? $marker['marker_address'] : '',
			'latitude' => ( ! empty( $marker['marker_latitude'] ) ) ? $marker['marker_latitude'] : null,
			'longitude' => ( ! empty( $marker['marker_longitude'] ) ) ? $marker['marker_longitude'] : null,
		);
	}
}



if ( ! empty( $atts['zoom'] ) ) {
	$script_options['zoom'] = intval( $atts['zoom'] );
}
if ( ! empty( $atts['type'] ) ) {
	$atts['type'] = strtoupper( $atts['type'] );
	if ( in_array( $atts['type'], array( 'ROADMAP', 'SATELLITE', 'HYBRID', 'TERRAIN' ) ) ) {
		$script_options['maptype'] = $atts['type'];
	}
}
if ( ! empty( $atts['map_bg_color'] ) ) {
	$script_options['mapBgColor'] = $atts['map_bg_color'];
}

$custom_marker_options = '';

if ( $atts['custom_marker_img'] != '' AND $atts['custom_marker_img'] != 'false' ) {
	if ( is_numeric( $atts['custom_marker_img'] ) ) {
			$atts['custom_marker_img'] = wp_get_attachment_image_src( intval( $atts['custom_marker_img'] ), 'thumbnail' );
		if ( $atts['custom_marker_img'] != NULL ) {
			$atts['custom_marker_img'] = $atts['custom_marker_img'][0];
		}
	}
	$atts['custom_marker_size'] = intval( $atts['custom_marker_size'] );
	$script_options['icon'] = array(
		'url' => $atts['custom_marker_img'],
		'size' => array( $atts['custom_marker_size'], $atts['custom_marker_size'] ),
		'anchor' => array( ceil( $atts['custom_marker_size'] / 2 ), $atts['custom_marker_size'] ),
	);
}

if ( empty( $atts['height'] ) ) {
	$atts['height'] = 400;
}
$script_options['height'] = $atts['height'];
$inner_css = ' style="height: ' . $atts['height'] . 'px"';

// Advanced options
if ( $atts['hide_controls'] ) {
	$script_options['hideControls'] = TRUE;
}
if ( $atts['disable_zoom'] ) {
	$script_options['disableZoom'] = TRUE;
}
if ( $atts['disable_dragging'] ) {
	$script_options['disableDragging'] = TRUE;
}
// Enqueued the script only once
if ( $atts['api_key'] != '' ) {
	wp_register_script( 'us-google-maps-with-key', '//maps.googleapis.com/maps/api/js?key=' . $atts['api_key'], array(), '', FALSE );
	wp_enqueue_script( 'us-google-maps-with-key' );
} else {
	wp_enqueue_script( 'us-google-maps' );
}
wp_enqueue_script( 'us-gmap' );

$script_options = apply_filters( 'us_gmaps_js_options', $script_options, get_the_ID(), $us_gmaps_index );

$output = '<div class="w-map' . $classes . '" id="us_map_' . $us_gmaps_index . '"' . $inner_css . '>';
$output .= '<div class="w-map-h"></div>';
$output .= '<div class="w-map-json"' . us_pass_data_to_js( $script_options ) . '></div>';
// Style JSON
if ( $atts['map_style_json'] != '' ) {
	$output .= '<div class="w-map-style-json" onclick=\'return ' . str_replace( "'", '&#39;', rawurldecode( base64_decode( $atts['map_style_json'] ) ) ) . '\'></div>';
}
$output .= '</div>';
echo $output;
