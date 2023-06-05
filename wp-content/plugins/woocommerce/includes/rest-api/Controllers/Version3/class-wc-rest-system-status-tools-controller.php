<?php
/**
 * REST API WC System Status Tools Controller
 *
 * Handles requests to the /system_status/tools/* endpoints.
 *
 * @package WooCommerce\RestApi
 * @since   3.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * System status tools controller.
 *
 * @package WooCommerce\RestApi
 * @extends WC_REST_System_Status_Tools_V2_Controller
 */
class WC_REST_System_Status_Tools_Controller extends WC_REST_System_Status_Tools_V2_Controller {

	/**
	 * Endpoint namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'wc/v3';
}
