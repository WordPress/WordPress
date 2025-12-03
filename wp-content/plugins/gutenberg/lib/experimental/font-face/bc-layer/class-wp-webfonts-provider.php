<?php
/**
 * Fonts API: Provider abstract class.
 *
 * Part of the backwards-compatibility (BC) layer for all
 * deprecated publicly exposed methods and functionality.
 *
 * This class/file will NOT be backported to Core. Rather for sites
 * using the previous API, it exists to prevent breakages, giving
 * developers time to upgrade their code.
 *
 * @package    WordPress
 * @subpackage Fonts API
 * @since      X.X.X
 */

if ( ! class_exists( 'WP_Webfonts_Provider' ) ) {

	/**
	 * Deprecated abstract class for Fonts API providers.
	 *
	 * BACKPORT NOTE: Do not backport this file to Core.
	 *
	 * @since X.X.X
	 * @deprecated 15.1 Use `WP_Fonts_Provider` instead.
	 * @deprecated 16.3.0 Custom providers are not supported.
	 */
	abstract class WP_Webfonts_Provider extends WP_Fonts_Provider {

		/**
		 * Fonts to be processed.
		 *
		 * @since X.X.X
		 * @deprecated 15.1 Use WP_Fonts_Provider::$fonts property instead.
		 * @deprecated 16.3.0
		 *
		 * @var array[]
		 */
		protected $webfonts = array();

		/**
		 * Sets this provider's fonts property.
		 *
		 * @since X.X.X
		 * @deprecated GB 15.1 Use WP_Fonts_Provider::set_fonts() instead.
		 * @deprecated 16.3.0 Set is not supported.
		 */
		public function set_webfonts() {
			_deprecated_function( __METHOD__, 'Gutenberg 15.1' );
		}
	}
}
