<?php
/**
 * Class for parameter-based Reports querying
 */

namespace Automattic\WooCommerce\Admin\API\Reports;

defined( 'ABSPATH' ) || exit;

/**
 * Admin\API\Reports\Query
 */
abstract class Query extends \WC_Object_Query {

	/**
	 * Get report data matching the current query vars.
	 *
	 * @return array|object of WC_Product objects
	 */
	public function get_data() {
		/* translators: %s: Method name */
		return new \WP_Error( 'invalid-method', sprintf( __( "Method '%s' not implemented. Must be overridden in subclass.", 'woocommerce' ), __METHOD__ ), array( 'status' => 405 ) );
	}
}
