<?php
namespace Elementor\Data\V2\Base\Endpoint\Index;

use Elementor\Data\V2\Base\Endpoint\Index;
use Elementor\Data\V2\Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
/**
 * All Children class, is an optional endpoint.
 *
 * Used in cases where the endpoints are static & there no use of dynamic endpoints( alpha/{id} ), eg:
 * 'settings' - controller
 * 'settings/products' - endpoint
 * 'settings/partners' - endpoint
 *
 * When 'settings' is requested, it should return results of all endpoints ( except it self ):
 * 'settings/products
 * 'settings/partners'
 * By running 'get_items' of each endpoint.
 */
class AllChildren extends Index {
	public function get_format() {
		return $this->controller->get_name() . '/index';
	}

	/**
	 * Retrieves a result(s) of all controller endpoint(s), items.
	 *
	 * Run overall endpoints of the current controller.
	 *
	 * Example, scenario:
	 * 'settings' - controller
	 * 'settings/products' - endpoint
	 * 'settings/partners' - endpoint
	 * Result:
	 * [
	 *  'products' => [
	 *      0 => ...
	 *      1 => ...
	 *  ],
	 *  'partners' => [
	 *      0 => ...
	 *      1 => ...
	 *  ],
	 * ]
	 */
	public function get_items( $request ) {
		$response = [];

		foreach ( $this->controller->get_sub_controllers() as $controller ) {
			$controller_route = $this->get_controller()->get_base_route() . '/' . $controller->get_name();
			$result = Manager::instance()->run_request( $controller_route );

			if ( ! $result->is_error() ) {
				$response[ $controller->get_name() ] = $result->get_data();
			}
		}

		foreach ( $this->controller->endpoints as $endpoint ) {
			// Skip self.
			if ( $endpoint === $this ) {
				continue;
			}

			$result = Manager::instance()->run_request( $endpoint->get_base_route() );

			if ( ! $result->is_error() ) {
				$response[ $endpoint->get_name() ] = $result->get_data();
			}
		}

		return $response;
	}
}
