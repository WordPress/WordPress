<?php
/**
 * REST API Reports controller
 *
 * Handles requests to the reports/sales endpoint.
 *
 * @package WooCommerce\RestApi
 * @since   2.6.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * REST API Report Sales controller class.
 *
 * @package WooCommerce\RestApi
 * @extends WC_REST_Report_Sales_V2_Controller
 */
class WC_REST_Report_Sales_Controller extends WC_REST_Report_Sales_V2_Controller {

	/**
	 * Endpoint namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'wc/v3';
}
