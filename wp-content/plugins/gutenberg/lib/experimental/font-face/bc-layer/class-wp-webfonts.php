<?php
/**
 * Webfonts API class: backwards-compatibility (BC) layer for all
 * deprecated publicly exposed methods and functionality.
 *
 * This class/file will NOT be backported to Core. Rather for sites
 * using the previous API, it exists to prevent breakages, giving
 * developers time to upgrade their code.
 *
 * @package    Gutenberg
 * @subpackage Fonts API's BC Layer
 * @since      X.X.X
 */

if ( ! class_exists( 'WP_Webfonts' ) ) {

	/**
	 * Class WP_Webfonts
	 *
	 * @since X.X.X
	 * @deprecated 15.1 Use WP_Fonts instead.
	 * @deprecated 16.3.0 Fonts API is not supported.
	 */
	class WP_Webfonts {

		/**
		 * Constructor.
		 *
		 * @since X.X.X
		 * @deprecated 16.3.0
		 */
		public function __construct() {
			_deprecated_function( __METHOD__, 'Gutenberg 16.3.0' );
		}

		/**
		 * Gets the font slug.
		 *
		 * @since X.X.X
		 * @deprecated Use WP_Fonts_Utils::convert_font_family_into_handle() or WP_Fonts_Utils::get_font_family_from_variation().
		 * @deprecated 16.3.0 This method is not supported.
		 *
		 * @return false False.
		 */
		public static function get_font_slug() {
			_deprecated_function( __METHOD__, 'Gutenberg 16.3.0' );
			return false;
		}

		/**
		 * Initializes the API.
		 *
		 * @since 6.0.0
		 * @deprecated 14.9.1 Use wp_fonts().
		 */
		public static function init() {
			_deprecated_function( __METHOD__, 'GB 14.9.1', 'wp_fonts()' );
		}

		/**
		 * Get the list of all registered font family handles.
		 *
		 * @since X.X.X
		 * @deprecated GB 15.8.0 Use wp_fonts()->get_registered_font_families().
		 * @deprecated 16.3.0 Register is not supported.
		 *
		 * @return array Empty array.
		 */
		public function get_registered_font_families() {
			_deprecated_function( __METHOD__, 'Gutenberg 15.8.0' );
			return array();
		}

		/**
		 * Gets the list of registered fonts.
		 *
		 * @since 6.0.0
		 * @deprecated 14.9.1 Use wp_fonts()->get_registered().
		 * @deprecated 16.3.0 Register is not supported.
		 *
		 * @return array Empty array.
		 */
		public function get_registered_webfonts() {
			_deprecated_function( __METHOD__, 'Gutenberg 14.9.1' );

			return array();
		}

		/**
		 * Gets the list of enqueued fonts.
		 *
		 * @since 6.0.0
		 * @deprecated 14.9.1 Use wp_fonts()->get_enqueued().
		 * @deprecated 16.3.0 Enqueue is not supported.
		 *
		 * @return array Empty array.
		 */
		public function get_enqueued_webfonts() {
			_deprecated_function( __METHOD__, 'Gutenberg 14.9.1' );
			return array();
		}

		/**
		 * Gets the list of all fonts.
		 *
		 * @since X.X.X
		 * @deprecated GB 14.9.1 Use wp_fonts()->get_registered().
		 * @deprecated 16.3.0 This method is not supported.
		 *
		 * @return array[]
		 */
		public function get_all_webfonts() {
			_deprecated_function( __METHOD__, 'Gutenberg 14.9.1', 'wp_fonts()->get_registered()' );
			return array();
		}

		/**
		 * Registers a webfont.
		 *
		 * @since 6.0.0
		 * @deprecated GB 14.9.1 Use wp_register_fonts().
		 * @deprecated 16.3.0 Register is not supported.
		 *
		 * @return bool False.
		 */
		public function register_webfont() {
			_deprecated_function( __METHOD__, 'GB 14.9.1', 'wp_register_fonts()' );
			return false;
		}

		/**
		 * Enqueue a font-family that has been already registered.
		 *
		 * @since XX.X
		 * @deprecated 14.9.1 Use wp_enqueue_fonts().
		 * @deprecated 16.3.0 Register is not supported.
		 *
		 * @return bool False.
		 */
		public function enqueue_webfont() {
			_deprecated_function( __METHOD__, 'Gutenberg 14.9.1' );
			return false;
		}
	}
}
