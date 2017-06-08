<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Theme Options Field: Button Preview
 *
 * Shows how buttons will look.
 *
 */

$prefixes = array( 'heading', 'body', 'menu' );
$font_families = array();
$default_font_weights = array_fill_keys( $prefixes, 400 );
foreach ( $prefixes as $prefix ) {
	$font = explode( '|', us_get_option( $prefix . '_font_family', 'none' ), 2 );
	if ( $font[0] == 'none' ) {
		// Use the default font
		$font_families[ $prefix ] = '';
	} elseif ( strpos( $font[0], ',' ) === FALSE ) {
		// Use some specific font from Google Fonts
		if ( ! isset( $font[1] ) OR empty( $font[1] ) ) {
			// Fault tolerance for missing font-variants
			$font[1] = '400,700';
		}
		// The first active font-weight will be used for "normal" weight
		$default_font_weights[ $prefix ] = intval( $font[1] );
		$fallback_font_family = us_config( 'google-fonts.' . $font[0] . '.fallback', 'sans-serif' );
		$font_families[ $prefix ] = 'font-family: "' . $font[0] . '", ' . $fallback_font_family . ";\n";
	} else {
		// Web-safe font combination
		$font_families[ $prefix ] = 'font-family: ' . $font[0] . ";\n";
	}
}

$style = '
box-shadow:0 0 0 2px ' . us_get_option( 'color_content_primary' ) . ' inset;
background-color:' . us_get_option( 'color_content_primary' ) . ';
color:' . us_get_option( 'color_content_primary' ) . ';
font-size: ' . us_get_option( 'button_fontsize' ) . 'px;
line-height: ' . us_get_option( 'button_height' ) . ';
padding: 0 ' . us_get_option( 'button_width' ) . 'em;
border-radius: ' . us_get_option( 'button_border_radius' ) . 'em;
letter-spacing: ' . us_get_option( 'button_letterspacing' ) . 'px;
';

if ( in_array( 'bold', us_get_option( 'button_text_style' ) ) ) {
	$style .= ' font-weight: bold;';
}

if ( in_array( 'uppercase', us_get_option( 'button_text_style' ) ) ) {
	$style .= ' text-transform: uppercase;';
}

$style .= $font_families[ us_get_option( 'button_font' ) ];



$output = '<div class="usof-button-preview">';
$output .= '<div class="usof-button-example solid" style=\'' . $style . '\'><span>' . __( 'Button Example', 'us' ) . '</span></div>';
$output .= '<div class="usof-button-example outlined" style=\'' . $style . '\'><span>' . __( 'Button Example', 'us' ) . '</span></div>';
$output .= '</div>';

echo $output;
