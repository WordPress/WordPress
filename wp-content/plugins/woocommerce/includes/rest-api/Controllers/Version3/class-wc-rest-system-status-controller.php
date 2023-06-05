<?php
/**
 * REST API WC System Status controller
 *
 * Handles requests to the /system_status endpoint.
 *
 * @package WooCommerce\RestApi
 * @since   3.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * System status controller class.
 *
 * @package WooCommerce\RestApi
 * @extends WC_REST_System_Status_V2_Controller
 */
class WC_REST_System_Status_Controller extends WC_REST_System_Status_V2_Controller {

	/**
	 * Endpoint namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'wc/v3';
}
