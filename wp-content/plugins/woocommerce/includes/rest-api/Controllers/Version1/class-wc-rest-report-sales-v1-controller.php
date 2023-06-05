<?php
/**
 * REST API Reports controller
 *
 * Handles requests to the reports/sales endpoint.
 *
 * @author   WooThemes
 * @category API
 * @package WooCommerce\RestApi
 * @since    3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * REST API Report Sales controller class.
 *
 * @package WooCommerce\RestApi
 * @extends WC_REST_Controller
 */
class WC_REST_Report_Sales_V1_Controller extends WC_REST_Controller {

	/**
	 * Endpoint namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'wc/v1';

	/**
	 * Route base.
	 *
	 * @var string
	 */
	protected $rest_base = 'reports/sales';

	/**
	 * Report instance.
	 *
	 * @var WC_Admin_Report
	 */
	protected $report;

	/**
	 * Register the routes for sales reports.
	 */
	public function register_routes() {
		register_rest_route( $this->namespace, '/' . $this->rest_base, array(
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_items' ),
				'permission_callback' => array( $this, 'get_items_permissions_check' ),
				'args'                => $this->get_collection_params(),
			),
			'schema' => array( $this, 'get_public_item_schema' ),
		) );
	}

	/**
	 * Check whether a given request has permission to read report.
	 *
	 * @param  WP_REST_Request $request Full details about the request.
	 * @return WP_Error|boolean
	 */
	public function get_items_permissions_check( $request ) {
		if ( ! wc_rest_check_manager_permissions( 'reports', 'read' ) ) {
			return new WP_Error( 'woocommerce_rest_cannot_view', __( 'Sorry, you cannot list resources.', 'woocommerce' ), array( 'status' => rest_authorization_required_code() ) );
		}

		return true;
	}

	/**
	 * Get sales reports.
	 *
	 * @param WP_REST_Request $request
	 * @return array|WP_Error
	 */
	public function get_items( $request ) {
		$data   = array();
		$item   = $this->prepare_item_for_response( null, $request );
		$data[] = $this->prepare_response_for_collection( $item );

		return rest_ensure_response( $data );
	}

	/**
	 * Prepare a report sales object for serialization.
	 *
	 * @param null $_
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response $response Response data.
	 */
	public function prepare_item_for_response( $_, $request ) {
		// Set date filtering.
		$filter = array(
			'period'   => $request['period'],
			'date_min' => $request['date_min'],
			'date_max' => $request['date_max'],
		);
		$this->setup_report( $filter );

		// New customers.
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

		// Setup period totals by ensuring each period in the interval has data.
		for ( $i = 0; $i <= $this->report->chart_interval; $i++ ) {

			switch ( $this->report->chart_groupby ) {
				case 'day' :
					$time = date( 'Y-m-d', strtotime( "+{$i} DAY", $this->report->start_date ) );
					break;
				default :
					$time = date( 'Y-m', strtotime( "+{$i} MONTH", $this->report->start_date ) );
					break;
			}

			// Set the customer signups for each period.
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

		// Add total order items for each period.
		foreach ( $report_data->order_items as $order_item ) {
			$time = ( 'day' === $this->report->chart_groupby ) ? date( 'Y-m-d', strtotime( $order_item->post_date ) ) : date( 'Y-m', strtotime( $order_item->post_date ) );

			if ( ! isset( $period_totals[ $time ] ) ) {
				continue;
			}

			$period_totals[ $time ]['items'] = (int) $order_item->order_item_count;
		}

		// Add total discount for each period.
		foreach ( $report_data->coupons as $discount ) {
			$time = ( 'day' === $this->report->chart_groupby ) ? date( 'Y-m-d', strtotime( $discount->post_date ) ) : date( 'Y-m', strtotime( $discount->post_date ) );

			if ( ! isset( $period_totals[ $time ] ) ) {
				continue;
			}

			$period_totals[ $time ]['discount'] = wc_format_decimal( $discount->discount_amount, 2 );
		}

		$sales_data = array(
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

		$context = ! empty( $request['context'] ) ? $request['context'] : 'view';
		$data    = $this->add_additional_fields_to_object( $sales_data, $request );
		$data    = $this->filter_response_by_context( $data, $context );

		// Wrap the data in a response object.
		$response = rest_ensure_response( $data );
		$response->add_links( array(
			'about' => array(
				'href' => rest_url( sprintf( '%s/reports', $this->namespace ) ),
			),
		) );

		/**
		 * Filter a report sales returned from the API.
		 *
		 * Allows modification of the report sales data right before it is returned.
		 *
		 * @param WP_REST_Response $response The response object.
		 * @param stdClass         $data     The original report object.
		 * @param WP_REST_Request  $request  Request used to generate the response.
		 */
		return apply_filters( 'woocommerce_rest_prepare_report_sales', $response, (object) $sales_data, $request );
	}

	/**
	 * Setup the report object and parse any date filtering.
	 *
	 * @param array $filter date filtering
	 */
	protected function setup_report( $filter ) {
		include_once( WC()->plugin_path() . '/includes/admin/reports/class-wc-admin-report.php' );
		include_once( WC()->plugin_path() . '/includes/admin/reports/class-wc-report-sales-by-date.php' );

		$this->report = new WC_Report_Sales_By_Date();

		if ( empty( $filter['period'] ) ) {
			// Custom date range.
			$filter['period'] = 'custom';

			if ( ! empty( $filter['date_min'] ) || ! empty( $filter['date_max'] ) ) {

				// Overwrite _GET to make use of WC_Admin_Report::calculate_current_range() for custom date ranges.
				$_GET['start_date'] = $filter['date_min'];
				$_GET['end_date'] = isset( $filter['date_max'] ) ? $filter['date_max'] : null;

			} else {

				// Default custom range to today.
				$_GET['start_date'] = $_GET['end_date'] = date( 'Y-m-d', current_time( 'timestamp' ) );
			}
		} else {
			$filter['period'] = empty( $filter['period'] ) ? 'week' : $filter['period'];

			// Change "week" period to "7day".
			if ( 'week' === $filter['period'] ) {
				$filter['period'] = '7day';
			}
		}

		$this->report->calculate_current_range( $filter['period'] );
	}

	/**
	 * Get the Report's schema, conforming to JSON Schema.
	 *
	 * @return array
	 */
	public function get_item_schema() {
		$schema = array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'sales_report',
			'type'       => 'object',
			'properties' => array(
				'total_sales' => array(
					'description' => __( 'Gross sales in the period.', 'woocommerce' ),
					'type'        => 'string',
					'context'     => array( 'view' ),
					'readonly'    => true,
				),
				'net_sales' => array(
					'description' => __( 'Net sales in the period.', 'woocommerce' ),
					'type'        => 'string',
					'context'     => array( 'view' ),
					'readonly'    => true,
				),
				'average_sales' => array(
					'description' => __( 'Average net daily sales.', 'woocommerce' ),
					'type'        => 'string',
					'context'     => array( 'view' ),
					'readonly'    => true,
				),
				'total_orders' => array(
					'description' => __( 'Total of orders placed.', 'woocommerce' ),
					'type'        => 'integer',
					'context'     => array( 'view' ),
					'readonly'    => true,
				),
				'total_items' => array(
					'description' => __( 'Total of items purchased.', 'woocommerce' ),
					'type'        => 'integer',
					'context'     => array( 'view' ),
					'readonly'    => true,
				),
				'total_tax' => array(
					'description' => __( 'Total charged for taxes.', 'woocommerce' ),
					'type'        => 'string',
					'context'     => array( 'view' ),
					'readonly'    => true,
				),
				'total_shipping' => array(
					'description' => __( 'Total charged for shipping.', 'woocommerce' ),
					'type'        => 'string',
					'context'     => array( 'view' ),
					'readonly'    => true,
				),
				'total_refunds' => array(
					'description' => __( 'Total of refunded orders.', 'woocommerce' ),
					'type'        => 'integer',
					'context'     => array( 'view' ),
					'readonly'    => true,
				),
				'total_discount' => array(
					'description' => __( 'Total of coupons used.', 'woocommerce' ),
					'type'        => 'integer',
					'context'     => array( 'view' ),
					'readonly'    => true,
				),
				'totals_grouped_by' => array(
					'description' => __( 'Group type.', 'woocommerce' ),
					'type'        => 'string',
					'context'     => array( 'view' ),
					'readonly'    => true,
				),
				'totals' => array(
					'description' => __( 'Totals.', 'woocommerce' ),
					'type'        => 'array',
					'items'       => array(
						'type'    => 'array',
					),
					'context'     => array( 'view' ),
					'readonly'    => true,
				),
			),
		);

		return $this->add_additional_fields_schema( $schema );
	}

	/**
	 * Get the query params for collections.
	 *
	 * @return array
	 */
	public function get_collection_params() {
		return array(
			'context' => $this->get_context_param( array( 'default' => 'view' ) ),
			'period' => array(
				'description'       => __( 'Report period.', 'woocommerce' ),
				'type'              => 'string',
				'enum'              => array( 'week', 'month', 'last_month', 'year' ),
				'validate_callback' => 'rest_validate_request_arg',
				'sanitize_callback' => 'sanitize_text_field',
			),
			'date_min' => array(
				/* translators: %s: date format */
				'description'       => sprintf( __( 'Return sales for a specific start date, the date need to be in the %s format.', 'woocommerce' ), 'YYYY-MM-DD' ),
				'type'              => 'string',
				'format'            => 'date',
				'validate_callback' => 'wc_rest_validate_reports_request_arg',
				'sanitize_callback' => 'sanitize_text_field',
			),
			'date_max' => array(
				/* translators: %s: date format */
				'description'       => sprintf( __( 'Return sales for a specific end date, the date need to be in the %s format.', 'woocommerce' ), 'YYYY-MM-DD' ),
				'type'              => 'string',
				'format'            => 'date',
				'validate_callback' => 'wc_rest_validate_reports_request_arg',
				'sanitize_callback' => 'sanitize_text_field',
			),
		);
	}
}
