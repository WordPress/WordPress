<?php
/**
 * Font API's utility helpers.
 *
 * @package    WordPress
 * @subpackage Fonts API
 * @since      X.X.X
 */

if ( ! class_exists( 'WP_Fonts_Utils' ) ) {

	/**
	 * Utility helpers for the Fonts API.
	 *
	 * @since X.X.X
	 * @deprecated 16.3.0 Fonts API is not supported.
	 */
	class WP_Fonts_Utils {

		/**
		 * Converts the given font family into a handle.
		 *
		 * @since X.X.X
		 * @deprecated 16.3.0 This method is not supported.
		 *
		 * @return null
		 */
		public static function convert_font_family_into_handle() {
			_deprecated_function( __METHOD__, 'Gutenberg 16.3.0' );
			return null;
		}

		/**
		 * Converts the given variation and its font-family into a handle.
		 *
		 * @since X.X.X
		 * @deprecated 16.3.0 This method is not supported.
		 *
		 * @return null
		 */
		public static function convert_variation_into_handle() {
			_deprecated_function( __METHOD__, 'Gutenberg 16.3.0' );
			return null;
		}

		/**
		 * Gets the font family from the variation.
		 *
		 * @since X.X.X
		 * @deprecated 16.3.0 This method is not supported.
		 *
		 * @return null Null.
		 */
		public static function get_font_family_from_variation() {
			_deprecated_function( __METHOD__, 'Gutenberg 16.3.0' );
			return null;
		}

		/**
		 * Checks if the given input is defined, i.e. meaning is a non-empty string.
		 *
		 * @since X.X.X
		 * @deprecated 16.3.0
		 *
		 * @param string $input The input to check.
		 * @return bool True when non-empty string. Else false.
		 */
		public static function is_defined( $input ) {
			_deprecated_function( __METHOD__, 'Gutenberg 16.3.0' );
			return ( is_string( $input ) && ! empty( $input ) );
		}
	}
}
