<?php
/**
 * REST API Shipping Zone Methods controller
 *
 * Handles requests to the /shipping/zones/<id>/methods endpoint.
 *
 * @package WooCommerce\RestApi
 * @since   3.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * REST API Shipping Zone Methods class.
 *
 * @package WooCommerce\RestApi
 * @extends WC_REST_Shipping_Zone_Methods_V2_Controller
 */
class WC_REST_Shipping_Zone_Methods_Controller extends WC_REST_Shipping_Zone_Methods_V2_Controller {

	/**
	 * Endpoint namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'wc/v3';

	/**
	 * Get the settings schema, conforming to JSON Schema.
	 *
	 * @return array
	 */
	public function get_item_schema() {
		// Get parent schema to append additional supported settings types for shipping zone method.
		$schema = parent::get_item_schema();

		// Append additional settings supported types (class, order).
		$schema['properties']['settings']['properties']['type']['enum'][] = 'class';
		$schema['properties']['settings']['properties']['type']['enum'][] = 'order';

		return $this->add_additional_fields_schema( $schema );
	}
}
