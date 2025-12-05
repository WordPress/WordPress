<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Admin\Endpoints
 */

/**
 * Dictates the required methods for an Endpoint implementation.
 */
interface WPSEO_Endpoint {

	/**
	 * Registers the routes for the endpoints.
	 *
	 * @return void
	 */
	public function register();

	/**
	 * Determines whether or not data can be retrieved for the registered endpoints.
	 *
	 * @return bool Whether or not data can be retrieved.
	 */
	public function can_retrieve_data();
}
