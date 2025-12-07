<?php
/**
 * Webfont API's utility helpers.
 *
 * BACKPORT NOTE: Do not backport this file to Core.
 * This file is now part of the API's Backwards-Compatibility (BC) layer.
 *
 * @package    WordPress
 * @subpackage Fonts API
 * @since      X.X.X
 */

if ( ! class_exists( 'WP_Webfonts_Utils' ) ) {

	/**
	 * Utility helpers for the Webfonts API.
	 *
	 * @since X.X.X
	 * @deprecated 15.1 Use WP_Fonts_Utils instead.
	 * @deprecated 16.3.0 Webfonts API is not supported.
	 */
	class WP_Webfonts_Utils {

		/**
		 * Converts the given font family into a handle.
		 *
		 * @since X.X.X
		 * @deprecated 15.1 Use WP_Fonts_Utils::convert_font_family_into_handle() instead.
		 * @deprecated 16.3.0 This method is not supported.
		 *
		 * @return null
		 */
		public static function convert_font_family_into_handle() {
			_deprecated_function( __METHOD__, 'Gutenberg 15.1' );
			return null;
		}

		/**
		 * Converts the given variation and its font-family into a handle.
		 *
		 * @since X.X.X
		 * @deprecated 15.1 Use WP_Fonts_Utils::convert_variation_into_handle() instead.
		 * @deprecated 16.3.0 This method is not supported.
		 *
		 * @return null
		 */
		public static function convert_variation_into_handle() {
			_deprecated_function( __METHOD__, 'Gutenberg 15.1' );
			return null;
		}

		/**
		 * Gets the font family from the variation.
		 *
		 * @since X.X.X
		 * @deprecated 15.1 Use WP_Fonts_Utils::get_font_family_from_variation() instead.
		 * @deprecated 16.3.0 This method is not supported.
		 *
		 * @return null
		 */
		public static function get_font_family_from_variation() {
			_deprecated_function( __METHOD__, 'Gutenberg 15.1' );
			return null;
		}

		/**
		 * Checks if the given input is defined, i.e. meaning is a non-empty string.
		 *
		 * @since X.X.X
		 * @deprecated 15.1 Use WP_Fonts_Utils::is_defined() instead.
		 * @deprecated 16.3.0 This method is not supported.
		 *
		 * @param string $input The input to check.
		 * @return bool True when non-empty string. Else false.
		 */
		public static function is_defined( $input ) {
			_deprecated_function( __METHOD__, 'Gutenberg 15.1' );
			return ( is_string( $input ) && ! empty( $input ) );
		}
	}
}
