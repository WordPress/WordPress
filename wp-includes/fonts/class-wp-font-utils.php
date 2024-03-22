<?php
/**
 * Font Utils class.
 *
 * Provides utility functions for working with font families.
 *
 * @package    WordPress
 * @subpackage Fonts
 * @since      6.5.0
 */

/**
 * A class of utilities for working with the Font Library.
 *
 * These utilities may change or be removed in the future and are intended for internal use only.
 *
 * @since 6.5.0
 * @access private
 */
class WP_Font_Utils {
	/**
	 * Adds surrounding quotes to font family names that contain special characters.
	 *
	 * It follows the recommendations from the CSS Fonts Module Level 4.
	 * @link https://www.w3.org/TR/css-fonts-4/#font-family-prop
	 *
	 * @since 6.5.0
	 *
	 * @param string $item A font family name.
	 * @return string The font family name with surrounding quotes, if necessary.
	 */
	private static function maybe_add_quotes( $item ) {
		// Matches strings that are not exclusively alphabetic characters or hyphens, and do not exactly follow the pattern generic(alphabetic characters or hyphens).
		$regex = '/^(?!generic\([a-zA-Z\-]+\)$)(?!^[a-zA-Z\-]+$).+/';
		$item  = trim( $item );
		if ( preg_match( $regex, $item ) ) {
			$item = trim( $item, "\"'" );
			return '"' . $item . '"';
		}
		return $item;
	}

	/**
	 * Sanitizes and formats font family names.
	 *
	 * - Applies `sanitize_text_field`.
	 * - Adds surrounding quotes to names containing any characters that are not alphabetic or dashes.
	 *
	 * It follows the recommendations from the CSS Fonts Module Level 4.
	 * @link https://www.w3.org/TR/css-fonts-4/#font-family-prop
	 *
	 * @since 6.5.0
	 * @access private
	 *
	 * @see sanitize_text_field()
	 *
	 * @param string $font_family Font family name(s), comma-separated.
	 * @return string Sanitized and formatted font family name(s).
	 */
	public static function sanitize_font_family( $font_family ) {
		if ( ! $font_family ) {
			return '';
		}

		$output          = sanitize_text_field( $font_family );
		$formatted_items = array();
		if ( str_contains( $output, ',' ) ) {
			$items = explode( ',', $output );
			foreach ( $items as $item ) {
				$formatted_item = self::maybe_add_quotes( $item );
				if ( ! empty( $formatted_item ) ) {
					$formatted_items[] = $formatted_item;
				}
			}
			return implode( ', ', $formatted_items );
		}
		return self::maybe_add_quotes( $output );
	}

	/**
	 * Generates a slug from font face properties, e.g. `open sans;normal;400;100%;U+0-10FFFF`
	 *
	 * Used for comparison with other font faces in the same family, to prevent duplicates
	 * that would both match according the CSS font matching spec. Uses only simple case-insensitive
	 * matching for fontFamily and unicodeRange, so does not handle overlapping font-family lists or
	 * unicode ranges.
	 *
	 * @since 6.5.0
	 * @access private
	 *
	 * @link https://drafts.csswg.org/css-fonts/#font-style-matching
	 *
	 * @param array $settings {
	 *     Font face settings.
	 *
	 *     @type string $fontFamily   Font family name.
	 *     @type string $fontStyle    Optional font style, defaults to 'normal'.
	 *     @type string $fontWeight   Optional font weight, defaults to 400.
	 *     @type string $fontStretch  Optional font stretch, defaults to '100%'.
	 *     @type string $unicodeRange Optional unicode range, defaults to 'U+0-10FFFF'.
	 * }
	 * @return string Font face slug.
	 */
	public static function get_font_face_slug( $settings ) {
		$defaults = array(
			'fontFamily'   => '',
			'fontStyle'    => 'normal',
			'fontWeight'   => '400',
			'fontStretch'  => '100%',
			'unicodeRange' => 'U+0-10FFFF',
		);
		$settings = wp_parse_args( $settings, $defaults );
		if ( function_exists( 'mb_strtolower' ) ) {
			$font_family = mb_strtolower( $settings['fontFamily'] );
		} else {
			$font_family = strtolower( $settings['fontFamily'] );
		}
		$font_style    = strtolower( $settings['fontStyle'] );
		$font_weight   = strtolower( $settings['fontWeight'] );
		$font_stretch  = strtolower( $settings['fontStretch'] );
		$unicode_range = strtoupper( $settings['unicodeRange'] );

		// Convert weight keywords to numeric strings.
		$font_weight = str_replace( array( 'normal', 'bold' ), array( '400', '700' ), $font_weight );

		// Convert stretch keywords to numeric strings.
		$font_stretch_map = array(
			'ultra-condensed' => '50%',
			'extra-condensed' => '62.5%',
			'condensed'       => '75%',
			'semi-condensed'  => '87.5%',
			'normal'          => '100%',
			'semi-expanded'   => '112.5%',
			'expanded'        => '125%',
			'extra-expanded'  => '150%',
			'ultra-expanded'  => '200%',
		);
		$font_stretch     = str_replace( array_keys( $font_stretch_map ), array_values( $font_stretch_map ), $font_stretch );

		$slug_elements = array( $font_family, $font_style, $font_weight, $font_stretch, $unicode_range );

		$slug_elements = array_map(
			function ( $elem ) {
				// Remove quotes to normalize font-family names, and ';' to use as a separator.
				$elem = trim( str_replace( array( '"', "'", ';' ), '', $elem ) );

				// Normalize comma separated lists by removing whitespace in between items,
				// but keep whitespace within items (e.g. "Open Sans" and "OpenSans" are different fonts).
				// CSS spec for whitespace includes: U+000A LINE FEED, U+0009 CHARACTER TABULATION, or U+0020 SPACE,
				// which by default are all matched by \s in PHP.
				return preg_replace( '/,\s+/', ',', $elem );
			},
			$slug_elements
		);

		return sanitize_text_field( implode( ';', $slug_elements ) );
	}

	/**
	 * Sanitizes a tree of data using a schema.
	 *
	 * The schema structure should mirror the data tree. Each value provided in the
	 * schema should be a callable that will be applied to sanitize the corresponding
	 * value in the data tree. Keys that are in the data tree, but not present in the
	 * schema, will be removed in the sanitized data. Nested arrays are traversed recursively.
	 *
	 * @since 6.5.0
	 *
	 * @access private
	 *
	 * @param array $tree   The data to sanitize.
	 * @param array $schema The schema used for sanitization.
	 * @return array The sanitized data.
	 */
	public static function sanitize_from_schema( $tree, $schema ) {
		if ( ! is_array( $tree ) || ! is_array( $schema ) ) {
			return array();
		}

		foreach ( $tree as $key => $value ) {
			// Remove keys not in the schema or with null/empty values.
			if ( ! array_key_exists( $key, $schema ) ) {
				unset( $tree[ $key ] );
				continue;
			}

			$is_value_array  = is_array( $value );
			$is_schema_array = is_array( $schema[ $key ] ) && ! is_callable( $schema[ $key ] );

			if ( $is_value_array && $is_schema_array ) {
				if ( wp_is_numeric_array( $value ) ) {
					// If indexed, process each item in the array.
					foreach ( $value as $item_key => $item_value ) {
						$tree[ $key ][ $item_key ] = isset( $schema[ $key ][0] ) && is_array( $schema[ $key ][0] )
							? self::sanitize_from_schema( $item_value, $schema[ $key ][0] )
							: self::apply_sanitizer( $item_value, $schema[ $key ][0] );
					}
				} else {
					// If it is an associative or indexed array, process as a single object.
					$tree[ $key ] = self::sanitize_from_schema( $value, $schema[ $key ] );
				}
			} elseif ( ! $is_value_array && $is_schema_array ) {
				// If the value is not an array but the schema is, remove the key.
				unset( $tree[ $key ] );
			} elseif ( ! $is_schema_array ) {
				// If the schema is not an array, apply the sanitizer to the value.
				$tree[ $key ] = self::apply_sanitizer( $value, $schema[ $key ] );
			}

			// Remove keys with null/empty values.
			if ( empty( $tree[ $key ] ) ) {
				unset( $tree[ $key ] );
			}
		}

		return $tree;
	}

	/**
	 * Applies a sanitizer function to a value.
	 *
	 * @since 6.5.0
	 *
	 * @param mixed $value     The value to sanitize.
	 * @param mixed $sanitizer The sanitizer function to apply.
	 * @return mixed The sanitized value.
	 */
	private static function apply_sanitizer( $value, $sanitizer ) {
		if ( null === $sanitizer ) {
			return $value;

		}
		return call_user_func( $sanitizer, $value );
	}

	/**
	 * Returns the expected mime-type values for font files, depending on PHP version.
	 *
	 * This is needed because font mime types vary by PHP version, so checking the PHP version
	 * is necessary until a list of valid mime-types for each file extension can be provided to
	 * the 'upload_mimes' filter.
	 *
	 * @since 6.5.0
	 *
	 * @access private
	 *
	 * @return array A collection of mime types keyed by file extension.
	 */
	public static function get_allowed_font_mime_types() {
		$php_7_ttf_mime_type = PHP_VERSION_ID >= 70300 ? 'application/font-sfnt' : 'application/x-font-ttf';

		return array(
			'otf'   => 'application/vnd.ms-opentype',
			'ttf'   => PHP_VERSION_ID >= 70400 ? 'font/sfnt' : $php_7_ttf_mime_type,
			'woff'  => PHP_VERSION_ID >= 80112 ? 'font/woff' : 'application/font-woff',
			'woff2' => PHP_VERSION_ID >= 80112 ? 'font/woff2' : 'application/font-woff2',
		);
	}
}
