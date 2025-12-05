<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO
 */

/**
 * An interface for registering AJAX integrations with WordPress.
 */
interface WPSEO_WordPress_AJAX_Integration {

	/**
	 * Registers all AJAX hooks to WordPress.
	 *
	 * @return void
	 */
	public function register_ajax_hooks();
}
