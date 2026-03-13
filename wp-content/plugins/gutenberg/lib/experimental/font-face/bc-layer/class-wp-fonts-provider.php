<?php
/**
 * Fonts API: Provider abstract class.
 *
 * Individual fonts providers should extend this class and implement.
 *
 * @package    WordPress
 * @subpackage Fonts API
 * @since      X.X.X
 */

if ( ! class_exists( 'WP_Fonts_Provider' ) ) {

	/**
	 * Abstract class for Fonts API providers.
	 *
	 * @since X.X.X
	 * @deprecated 16.3.0 Custom providers are not supported.
	 */
	abstract class WP_Fonts_Provider {

		/**
		 * The provider's unique ID.
		 *
		 * @since X.X.X
		 * @deprecated 16.3.0
		 *
		 * @var string
		 */
		protected $id = '';

		/**
		 * Fonts to be processed.
		 *
		 * @since X.X.X
		 * @deprecated 16.3.0
		 *
		 * @var array[]
		 */
		protected $fonts = array();

		/**
		 * Array of Font-face style tag's attribute(s)
		 * where the key is the attribute name and the
		 * value is its value.
		 *
		 * @since X.X.X
		 * @deprecated 16.3.0
		 *
		 * @var string[]
		 */
		protected $style_tag_atts = array();

		/**
		 * Sets this provider's fonts property.
		 *
		 * @since X.X.X
		 * @deprecated 16.3.0 Set is not supported.
		 */
		public function set_fonts() {
			_deprecated_function( __METHOD__, 'Gutenberg 16.3.0' );
		}

		/**
		 * Prints the generated styles.
		 *
		 * @since X.X.X
		 * @deprecated 16.3.0 Use wp_print_font_faces() instead.
		 */
		public function print_styles() {
			_deprecated_function( __METHOD__, 'Gutenberg 16.3.0', 'wp_print_font_faces' );
		}

		/**
		 * Gets the `@font-face` CSS for the provider's fonts.
		 *
		 * @since X.X.X
		 * @deprecated 16.3.0
		 *
		 * @return string The `@font-face` CSS.
		 */
		abstract public function get_css();

		/**
		 * Gets the `<style>` element for wrapping the `@font-face` CSS.
		 *
		 * @since X.X.X
		 * @deprecated 16.3.0 Get style element is not supported.
		 *
		 * @return string Empty string.
		 */
		protected function get_style_element() {
			_deprecated_function( __METHOD__, 'Gutenberg 16.3.0' );
			return '';
		}
	}
}
