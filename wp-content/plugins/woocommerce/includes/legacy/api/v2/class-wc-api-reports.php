<?php
/**
 * WooCommerce API Reports Class
 *
 * Handles requests to the /reports endpoint
 *
 * @author      WooThemes
 * @category    API
 * @package     WooCommerce\RestApi
 * @since       2.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class WC_API_Reports extends WC_API_Resource {

	/** @var string $base the route base */
	protected $base = '/reports';

	/** @var WC_Admin_Report instance */
	private $report;

	/**
	 * Register the routes for this class
	 *
	 * GET /reports
	 * GET /reports/sales
	 *
	 * @since 2.1
	 * @param array $routes
	 * @return array
	 */
	public function register_routes( $routes ) {

		# GET /reports
		$routes[ $this->base ] = array(
			array( array( $this, 'get_reports' ),     WC_API_Server::READABLE ),
		);

		# GET /reports/sales
		$routes[ $this->base . '/sales' ] = array(
			array( array( $this, 'get_sales_report' ), WC_API_Server::READABLE ),
		);

		# GET /reports/sales/top_sellers
		$routes[ $this->base . '/sales/top_sellers' ] = array(
			array( array( $this, 'get_top_sellers_report' ), WC_API_Server::READABLE ),
		);

		return $routes;
	}

	/**
	 * Get a simple listing of available reports
	 *
	 * @since 2.1
	 * @return array
	 */
	public function get_reports() {
		return array( 'reports' => array( 'sales', 'sales/top_sellers' ) );
	}

	/**
	 * Get the sales report
	 *
	 * @since 2.1
	 * @param string $fields fields to include in response
	 * @param array $filter date filtering
	 * @return array|WP_Error
	 */
	public function get_sales_report( $fields = null, $filter = array() ) {

		// check user permissions
		$check = $this->validate_request();

		// check for WP_Error
		if ( is_wp_error( $check ) ) {
			return $check;
		}

		// set date filtering
		$this->setup_report( $filter );

		// new customers
		$users_query = new WP_User_Query(
			array(
				'fields' => array( 'user_registered' ),
				'role'   => 'customer',
			)
		);

		$customers = $users_query->get_results();

		foreach ( $customers as $key => $customer ) {
			if ( strtotime( $customer->user_registered ) < $this->report->start_date || strtotime( $customer->user_registered ) > $this->report->end_date ) {
				unset( $customers[ $key ] );
			}
		}

		$total_customers = count( $customers );
		$report_data     = $this->report->get_report_data();
		$period_totals   = array();

		// setup period totals by ensuring each period in the interval has data
		for ( $i = 0; $i <= $this->report->chart_interval; $i ++ ) {

			switch ( $this->report->chart_groupby ) {
				case 'day' :
					$time = date( 'Y-m-d', strtotime( "+{$i} DAY", $this->report->start_date ) );
					break;
				default :
					$time = date( 'Y-m', strtotime( "+{$i} MONTH", $this->report->start_date ) );
					break;
			}

			// set the customer signups for each period
			$customer_count = 0;
			foreach ( $customers as $customer ) {
				if ( date( ( 'day' == $this->report->chart_groupby ) ? 'Y-m-d' : 'Y-m', strtotime( $customer->user_registered ) ) == $time ) {
					$customer_count++;
				}
 			}

			$period_totals[ $time ] = array(
				'sales'     => wc_format_decimal( 0.00, 2 ),
				'orders'    => 0,
				'items'     => 0,
				'tax'       => wc_format_decimal( 0.00, 2 ),
				'shipping'  => wc_format_decimal( 0.00, 2 ),
				'discount'  => wc_format_decimal( 0.00, 2 ),
				'customers' => $customer_count,
			);
		}

		// add total sales, total order count, total tax and total shipping for each period
		foreach ( $report_data->orders as $order ) {
			$time = ( 'day' === $this->report->chart_groupby ) ? date( 'Y-m-d', strtotime( $order->post_date ) ) : date( 'Y-m', strtotime( $order->post_date ) );

			if ( ! isset( $period_totals[ $time ] ) ) {
				continue;
			}

			$period_totals[ $time ]['sales']    = wc_format_decimal( $order->total_sales, 2 );
			$period_totals[ $time ]['tax']      = wc_format_decimal( $order->total_tax + $order->total_shipping_tax, 2 );
			$period_totals[ $time ]['shipping'] = wc_format_decimal( $order->total_shipping, 2 );
		}

		foreach ( $report_data->order_counts as $order ) {
			$time = ( 'day' === $this->report->chart_groupby ) ? date( 'Y-m-d', strtotime( $order->post_date ) ) : date( 'Y-m', strtotime( $order->post_date ) );

			if ( ! isset( $period_totals[ $time ] ) ) {
				continue;
			}

			$period_totals[ $time ]['orders']   = (int) $order->count;
		}

		// add total order items for each period
		foreach ( $report_data->order_items as $order_item ) {
			$time = ( 'day' === $this->report->chart_groupby ) ? date( 'Y-m-d', strtotime( $order_item->post_date ) ) : date( 'Y-m', strtotime( $order_item->post_date ) );

			if ( ! isset( $period_totals[ $time ] ) ) {
				continue;
			}

			$period_totals[ $time ]['items'] = (int) $order_item->order_item_count;
		}

		// add total discount for each period
		foreach ( $report_data->coupons as $discount ) {
			$time = ( 'day' === $this->report->chart_groupby ) ? date( 'Y-m-d', strtotime( $discount->post_date ) ) : date( 'Y-m', strtotime( $discount->post_date ) );

			if ( ! isset( $period_totals[ $time ] ) ) {
				continue;
			}

			$period_totals[ $time ]['discount'] = wc_format_decimal( $discount->discount_amount, 2 );
		}

		$sales_data  = array(
			'total_sales'       => $report_data->total_sales,
			'net_sales'         => $report_data->net_sales,
			'average_sales'     => $report_data->average_sales,
			'total_orders'      => $report_data->total_orders,
			'total_items'       => $report_data->total_items,
			'total_tax'         => wc_format_decimal( $report_data->total_tax + $report_data->total_shipping_tax, 2 ),
			'total_shipping'    => $report_data->total_shipping,
			'total_refunds'     => $report_data->total_refunds,
			'total_discount'    => $report_data->total_coupons,
			'totals_grouped_by' => $this->report->chart_groupby,
			'totals'            => $period_totals,
			'total_customers'   => $total_customers,
		);

		return array( 'sales' => apply_filters( 'woocommerce_api_report_response', $sales_data, $this->report, $fields, $this->server ) );
	}

	/**
	 * Get the top sellers report
	 *
	 * @since 2.1
	 * @param string $fields fields to include in response
	 * @param array $filter date filtering
	 * @return array|WP_Error
	 */
	public function get_top_sellers_report( $fields = null, $filter = array() ) {

		// check user permissions
		$check = $this->validate_request();

		if ( is_wp_error( $check ) ) {
			return $check;
		}

		// set date filtering
		$this->setup_report( $filter );

		$top_sellers = $this->report->get_order_report_data( array(
			'data' => array(
				'_product_id' => array(
					'type'            => 'order_item_meta',
					'order_item_type' => 'line_item',
					'function'        => '',
					'name'            => 'product_id',
				),
				'_qty' => array(
					'type'            => 'order_item_meta',
					'order_item_type' => 'line_item',
					'function'        => 'SUM',
					'name'            => 'order_item_qty',
				),
			),
			'order_by'     => 'order_item_qty DESC',
			'group_by'     => 'product_id',
			'limit'        => isset( $filter['limit'] ) ? absint( $filter['limit'] ) : 12,
			'query_type'   => 'get_results',
			'filter_range' => true,
		) );

		$top_sellers_data = array();

		foreach ( $top_sellers as $top_seller ) {

			$product = wc_get_product( $top_seller->product_id );

			if ( $product ) {
				$top_sellers_data[] = array(
					'title'      => $product->get_name(),
					'product_id' => $top_seller->product_id,
					'quantity'   => $top_seller->order_item_qty,
				);
			}
		}

		return array( 'top_sellers' => apply_filters( 'woocommerce_api_report_response', $top_sellers_data, $this->report, $fields, $this->server ) );
	}

	/**
	 * Setup the report object and parse any date filtering
	 *
	 * @since 2.1
	 * @param array $filter date filtering
	 */
	private function setup_report( $filter ) {

		include_once( WC()->plugin_path() . '/includes/admin/reports/class-wc-admin-report.php' );
		include_once( WC()->plugin_path() . '/includes/admin/reports/class-wc-report-sales-by-date.php' );

		$this->report = new WC_Report_Sales_By_Date();

		if ( empty( $filter['period'] ) ) {

			// custom date range
			$filter['period'] = 'custom';

			if ( ! empty( $filter['date_min'] ) || ! empty( $filter['date_max'] ) ) {

				// overwrite _GET to make use of WC_Admin_Report::calculate_current_range() for custom date ranges
				$_GET['start_date'] = $this->server->parse_datetime( $filter['date_min'] );
				$_GET['end_date'] = isset( $filter['date_max'] ) ? $this->server->parse_datetime( $filter['date_max'] ) : null;

			} else {

				// default custom range to today
				$_GET['start_date'] = $_GET['end_date'] = date( 'Y-m-d', current_time( 'timestamp' ) );
			}
		} else {

			// ensure period is valid
			if ( ! in_array( $filter['period'], array( 'week', 'month', 'last_month', 'year' ) ) ) {
				$filter['period'] = 'week';
			}

			// TODO: change WC_Admin_Report class to use "week" instead, as it's more consistent with other periods
			// allow "week" for period instead of "7day"
			if ( 'week' === $filter['period'] ) {
				$filter['period'] = '7day';
			}
		}

		$this->report->calculate_current_range( $filter['period'] );
	}

	/**
	 * Verify that the current user has permission to view reports
	 *
	 * @since 2.1
	 * @see WC_API_Resource::validate_request()
	 *
	 * @param null $id unused
	 * @param null $type unused
	 * @param null $context unused
	 *
	 * @return bool|WP_Error
	 */
	protected function validate_request( $id = null, $type = null, $context = null ) {

		if ( ! current_user_can( 'view_woocommerce_reports' ) ) {

			return new WP_Error( 'woocommerce_api_user_cannot_read_report', __( 'You do not have permission to read this report', 'woocommerce' ), array( 'status' => 401 ) );

		} else {

			return true;
		}
	}
}
