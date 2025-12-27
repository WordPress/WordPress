<?php
/**
 * WP_Fonts_Resolver class.
 *
 * @package    WordPress
 * @subpackage Fonts API
 * @since      X.X.X
 */

if ( ! class_exists( 'WP_Fonts_Resolver' ) ) {

	/**
	 * The Fonts API Resolver abstracts the processing of different data sources
	 * (such as theme.json and global styles) for font interactions with the API.
	 *
	 * This class is for internal core usage and is not supposed to be used by
	 * extenders (plugins and/or themes).
	 *
	 * @access private
	 *
	 * @since X.X.X
	 * @deprecated 16.3.0 Fonts API is not supported.
	 */
	class WP_Fonts_Resolver {

		/**
		 * Enqueues user-selected fonts via global styles.
		 *
		 * @since X.X.X
		 * @deprecated 16.3.0 Enqueue is not supported.
		 *
		 * @return array Empty array.
		 */
		public static function enqueue_user_selected_fonts() {
			_deprecated_function( __FUNCTION__, 'Gutenberg 16.3' );
			return array();
		}

		/**
		 * Register fonts defined in theme.json.
		 *
		 * @since X.X.X
		 * @deprecated 16.3.0 Register is not supported.
		 */
		public static function register_fonts_from_theme_json() {
			_deprecated_function( __FUNCTION__, 'Gutenberg 16.3' );
		}

		/**
		 * Add missing fonts to the global styles.
		 *
		 * @since X.X.X
		 * @deprecated 16.3.0 Adding fonts into theme.json theme data layer is not supported.
		 *
		 * @param WP_Theme_JSON_Gutenberg|WP_Theme_JSON $data The global styles.
		 * @return WP_Theme_JSON_Gutenberg|WP_Theme_JSON Unchanged global styles.
		 */
		public static function add_missing_fonts_to_theme_json( $data ) {
			_deprecated_function( __FUNCTION__, 'Gutenberg 16.3' );
			return $data;
		}
	}
}
