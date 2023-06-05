<?php
/**
 * Class for parameter-based Orders Reports querying
 *
 * Example usage:
 * $args = array(
 *          'before'        => '2018-07-19 00:00:00',
 *          'after'         => '2018-07-05 00:00:00',
 *          'interval'      => 'week',
 *          'products'      => array(15, 18),
 *          'coupons'       => array(138),
 *          'status_is'     => array('completed'),
 *          'status_is_not' => array('failed'),
 *          'new_customers' => false,
 *         );
 * $report = new \Automattic\WooCommerce\Admin\API\Reports\Orders\Query( $args );
 * $mydata = $report->get_data();
 */

namespace Automattic\WooCommerce\Admin\API\Reports\Orders;

defined( 'ABSPATH' ) || exit;

use Automattic\WooCommerce\Admin\API\Reports\Query as ReportsQuery;

/**
 * API\Reports\Orders\Query
 */
class Query extends ReportsQuery {

	/**
	 * Get order data based on the current query vars.
	 *
	 * @return array
	 */
	public function get_data() {
		$args       = apply_filters( 'woocommerce_analytics_orders_query_args', $this->get_query_vars() );
		$data_store = \WC_Data_Store::load( 'report-orders' );
		$results    = $data_store->get_data( $args );
		return apply_filters( 'woocommerce_analytics_orders_select_query', $results, $args );
	}
}
