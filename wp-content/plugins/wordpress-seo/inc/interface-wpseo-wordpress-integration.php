<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO
 */

if ( ! interface_exists( 'WPSEO_WordPress_Integration' ) ) {
	/**
	 * An interface for registering integrations with WordPress.
	 */
	interface WPSEO_WordPress_Integration {

		/**
		 * Registers all hooks to WordPress.
		 *
		 * @return void
		 */
		public function register_hooks();
	}
}
