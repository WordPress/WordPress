<?php
/**
 * Web Fonts API class.
 *
 * @package    WordPress
 * @subpackage Fonts API
 * @since      X.X.X
 */

if ( ! class_exists( 'WP_Web_Fonts' ) ) {

	/**
	 * Class WP_Web_Fonts
	 *
	 * @since 14.9.1
	 * @deprecated 15.1 Use WP_Fonts instead.
	 * @deprecated 16.3.0 Fonts API is not supported.
	 */
	class WP_Web_Fonts extends WP_Webfonts {

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
		 * Get the list of registered providers.
		 *
		 * @since X.X.X
		 * @deprecated 16.3.0 Providers are not supported.
		 */
		public function get_providers() {
			_deprecated_function( __METHOD__, 'Gutenberg 16.3.0' );
		}

		/**
		 * Register a provider.
		 *
		 * @since X.X.X
		 * @deprecated 16.3.0 Providers are not supported.
		 *
		 * @return bool False.
		 */
		public function register_provider() {
			_deprecated_function( __METHOD__, 'Gutenberg 16.3.0' );
			return false;
		}

		/**
		 * Get the list of all registered font family handles.
		 *
		 * @since X.X.X
		 * @deprecated 16.3.0 Register is not supported.
		 *
		 * @return array Empty array.
		 */
		public function get_registered_font_families() {
			_deprecated_function( __METHOD__, 'Gutenberg 16.3.0' );
			return array();
		}

		/**
		 * Get the list of all registered font families and their variations.
		 *
		 * @since X.X.X
		 * @deprecated 16.3.0 Register is not supported.
		 *
		 * @return array Empty array.
		 */
		public function get_registered() {
			_deprecated_function( __METHOD__, 'Gutenberg 16.3.0' );
			return array();
		}

		/**
		 * Get the list of enqueued font families and their variations.
		 *
		 * @since X.X.X
		 * @deprecated 16.3.0 Enqueue is not supported.
		 *
		 * @return array Empty array.
		 */
		public function get_enqueued() {
			_deprecated_function( __METHOD__, 'Gutenberg 16.3.0' );
			return array();
		}

		/**
		 * Registers a font family.
		 *
		 * @since X.X.X
		 * @deprecated 16.3.0 Add is not supported.
		 *
		 * @return null Null.
		 */
		public function add_font_family() {
			_deprecated_function( __METHOD__, 'Gutenberg 16.3.0' );
			return null;
		}

		/**
		 * Removes a font family and all registered variations.
		 *
		 * @since X.X.X
		 * @deprecated 16.3.0 Remove is not supported.
		 */
		public function remove_font_family() {
			_deprecated_function( __METHOD__, 'Gutenberg 16.3.0' );
		}

		/**
		 * Add a variation to an existing family or register family if none exists.
		 *
		 * @since X.X.X
		 * @deprecated 16.3.0 Add is not supported.
		 *
		 * @return null Null.
		 */
		public function add_variation() {
			_deprecated_function( __METHOD__, 'Gutenberg 16.3.0' );
			return null;
		}

		/**
		 * Removes a variation.
		 *
		 * @since X.X.X
		 * @deprecated 16.3.0 Remove is not supported.
		 */
		public function remove_variation() {
			_deprecated_function( __METHOD__, 'Gutenberg 16.3.0' );
		}

		/**
		 * Processes the items and dependencies.
		 *
		 * Processes the items passed to it or the queue, and their dependencies.
		 *
		 * @since X.X.X
		 * @deprecated 16.3.0 Processing items and dependencies is not supported.
		 *
		 * @param string|string[]|bool $handles Optional. Items to be processed: queue (false),
		 *                                      single item (string), or multiple items (array of strings).
		 *                                      Default false.
		 * @param int|false            $group   Optional. Group level: level (int), no group (false).
		 *
		 * @return array Empty array.
		 */
		public function do_items( $handles = false, $group = false ) { // phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
			_deprecated_function( __METHOD__, 'Gutenberg 16.3.0' );
			return array();
		}

		/**
		 * Invokes each provider to process and print its styles.
		 *
		 * @since X.X.X
		 * @deprecated 16.3.0 Process and print provider styles is not supported.
		 *
		 * @see WP_Dependencies::do_item()
		 *
		 * @param string    $provider_id The provider to process.
		 * @param int|false $group       Not used.
		 * @return bool False.
		 */
		public function do_item( $provider_id, $group = false ) { // phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
			_deprecated_function( __METHOD__, 'Gutenberg 16.3.0' );
			return false;
		}

		/**
		 * Converts the font family and its variations into theme.json structural format.
		 *
		 * @since X.X.X
		 * @deprecated 16.3.0 Convert is not supported.
		 *
		 * @return array Empty array.
		 */
		public function to_theme_json() {
			_deprecated_function( __METHOD__, 'Gutenberg 16.3.0' );
			return array();
		}
	}
}
