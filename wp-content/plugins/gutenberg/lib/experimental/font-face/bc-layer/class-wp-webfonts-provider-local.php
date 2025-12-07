<?php
/**
 * Fonts API: Provider for locally-hosted fonts.
 *
 * Part of the backwards-compatibility (BC) layer for all
 * deprecated publicly exposed methods and functionality.
 *
 * This class/file will NOT be backported to Core. Rather for sites
 * using the previous API, it exists to prevent breakages, giving
 * developers time to upgrade their code.
 *
 * @package    WordPress
 * @subpackage Fonts
 * @since      X.X.X
 */

if ( ! class_exists( 'WP_Webfonts_Provider_Local' ) ) {

	/**
	 * A deprecated core bundled provider for generating `@font-face` styles
	 * from locally-hosted font files.
	 *
	 * BACKPORT NOTE: Do not backport this file to Core.
	 *
	 * @since X.X.X
	 * @deprecated 15.1 Use `WP_Fonts_Provider_Local` instead.
	 * @deprecated 16.3.0 Providers are not supported.
	 *                    Local provider is in WP_Font_Face.
	 */
	class WP_Webfonts_Provider_Local extends WP_Webfonts_Provider {

		/**
		 * The provider's unique ID.
		 *
		 * @since 6.0.0
		 * @deprecated 16.3.0
		 *
		 * @var string
		 */
		protected $id = 'local';

		/**
		 * Constructor.
		 *
		 * @since 6.1.0
		 */
		public function __construct() {
			_deprecated_function( __METHOD__, 'Gutenberg 15.1' );
		}

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
