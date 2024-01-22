<?php
/**
 * Fonts functions.
 *
 * @package    WordPress
 * @subpackage Fonts
 * @since      6.4.0
 */

/**
 * Generates and prints font-face styles for given fonts or theme.json fonts.
 *
 * @since 6.4.0
 *
 * @param array[][] $fonts {
 *     Optional. The font-families and their font variations. Default empty array.
 *
 *     @type string $font-family => array[] $variations {
 *         Optional. An associated array of font variations for this font-family.
 *         Each variation has the following structure.
 *
 *         @type array $font_variation {
 *             @type string          $font-family             The font-family property.
 *             @type string|string[] $src                     The URL(s) to each resource containing the font data.
 *             @type string          $font_style              Optional. The font-style property. Default 'normal'.
 *             @type string          $font-weight             Optional. The font-weight property. Default '400'.
 *             @type string          $font-display            Optional. The font-display property. Default 'fallback'.
 *             @type string          $ascent-override         Optional. The ascent-override property.
 *             @type string          $descent-override        Optional. The descent-override property.
 *             @type string          $font-stretch            Optional. The font-stretch property.
 *             @type string          $font-variant            Optional. The font-variant property.
 *             @type string          $font-feature-settings   Optional. The font-feature-settings property.
 *             @type string          $font-variation-settings Optional. The font-variation-settings property.
 *             @type string          $line-gap-override       Optional. The line-gap-override property.
 *             @type string          $size-adjust             Optional. The size-adjust property.
 *             @type string          $unicode-range           Optional. The unicode-range property.
 *         }
 *     }
 * }
 */
function wp_print_font_faces( $fonts = array() ) {

	if ( empty( $fonts ) ) {
		$fonts = WP_Font_Face_Resolver::get_fonts_from_theme_json();
	}

	if ( empty( $fonts ) ) {
		return;
	}

	$wp_font_face = new WP_Font_Face();
	$wp_font_face->generate_and_print( $fonts );
}
