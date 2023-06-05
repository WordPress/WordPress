<?php
namespace Automattic\WooCommerce\StoreApi\Routes;

/**
 * RouteInterface.
 */
interface RouteInterface {
	/**
	 * Get the path of this REST route.
	 *
	 * @return string
	 */
	public function get_path();

	/**
	 * Get arguments for this REST route.
	 *
	 * @return array An array of endpoints.
	 */
	public function get_args();
}
