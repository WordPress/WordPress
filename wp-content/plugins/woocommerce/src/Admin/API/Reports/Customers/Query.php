<?php
/**
 * Class for parameter-based Customers Report querying
 *
 * Example usage:
 * $args = array(
 *          'registered_before'   => '2018-07-19 00:00:00',
 *          'registered_after'    => '2018-07-05 00:00:00',
 *          'page'                => 2,
 *          'avg_order_value_min' => 100,
 *          'country'             => 'GB',
 *         );
 * $report = new \Automattic\WooCommerce\Admin\API\Reports\Customers\Query( $args );
 * $mydata = $report->get_data();
 */

namespace Automattic\WooCommerce\Admin\API\Reports\Customers;

defined( 'ABSPATH' ) || exit;

use Automattic\WooCommerce\Admin\API\Reports\Query as ReportsQuery;

/**
 * API\Reports\Customers\Query
 */
class Query extends ReportsQuery {

	/**
	 * Valid fields for Customers report.
	 *
	 * @return array
	 */
	protected function get_default_query_vars() {
		return array(
			'per_page' => get_option( 'posts_per_page' ), // not sure if this should be the default.
			'page'     => 1,
			'order'    => 'DESC',
			'orderby'  => 'date_registered',
			'fields'   => '*',
		);
	}

	/**
	 * Get product data based on the current query vars.
	 *
	 * @return array
	 */
	public function get_data() {
		$args = apply_filters( 'woocommerce_analytics_customers_query_args', $this->get_query_vars() );

		$data_store = \WC_Data_Store::load( 'report-customers' );
		$results    = $data_store->get_data( $args );
		return apply_filters( 'woocommerce_analytics_customers_select_query', $results, $args );
	}
}
