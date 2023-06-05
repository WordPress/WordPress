<?php
/**
 * REST API Webhooks controller
 *
 * Handles requests to the /webhooks endpoint.
 *
 * @package WooCommerce\RestApi
 * @since   2.6.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * REST API Webhooks controller class.
 *
 * @package WooCommerce\RestApi
 * @extends WC_REST_Webhooks_V2_Controller
 */
class WC_REST_Webhooks_Controller extends WC_REST_Webhooks_V2_Controller {

	/**
	 * Endpoint namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'wc/v3';

	/**
	 * Get the default REST API version.
	 *
	 * @since  3.0.0
	 * @return string
	 */
	protected function get_default_api_version() {
		return 'wp_api_v3';
	}
}
