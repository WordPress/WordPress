<?php
/**
 * Webfonts API: Provider for locally-hosted fonts.
 *
 * @package    WordPress
 * @subpackage Fonts API
 * @since      X.X.X
 */

if ( ! class_exists( 'WP_Fonts_Provider_Local' ) ) {

	/**
	 * A core bundled provider for generating `@font-face` styles
	 * from locally-hosted font files.
	 *
	 * @since X.X.X
	 * @deprecated 16.3.0 Providers are not supported.
	 *                    Local provider is in WP_Font_Face.
	 */
	class WP_Fonts_Provider_Local extends WP_Fonts_Provider {

		/**
		 * The provider's unique ID.
		 *
		 * @since X.X.X
		 * @deprecated 16.3.0
		 *
		 * @var string
		 */
		protected $id = 'local';

		/**
		 * Gets the `@font-face` CSS styles for locally-hosted font files.
		 *
		 * @since X.X.X
		 * @deprecated 16.3.0 Get styles is not supported.
		 *
		 * @return string Empty string.
		 */
		public function get_css() {
			_deprecated_function( __FUNCTION__, 'Gutenberg 16.3' );
			return '';
		}
	}
}
