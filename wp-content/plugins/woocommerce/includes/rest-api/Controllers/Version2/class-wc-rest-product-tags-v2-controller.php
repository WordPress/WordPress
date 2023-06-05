<?php
/**
 * REST API Product Tags controller
 *
 * Handles requests to the products/tags endpoint.
 *
 * @package WooCommerce\RestApi
 * @since   2.6.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * REST API Product Tags controller class.
 *
 * @package WooCommerce\RestApi
 * @extends WC_REST_Product_Tags_V1_Controller
 */
class WC_REST_Product_Tags_V2_Controller extends WC_REST_Product_Tags_V1_Controller {

	/**
	 * Endpoint namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'wc/v2';
}
