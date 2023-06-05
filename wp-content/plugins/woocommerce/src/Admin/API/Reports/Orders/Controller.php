<?php
/**
 * REST API Reports orders controller
 *
 * Handles requests to the /reports/orders endpoint.
 */

namespace Automattic\WooCommerce\Admin\API\Reports\Orders;

defined( 'ABSPATH' ) || exit;

use Automattic\WooCommerce\Admin\API\Reports\Controller as ReportsController;
use Automattic\WooCommerce\Admin\API\Reports\ExportableInterface;

/**
 * REST API Reports orders controller class.
 *
 * @internal
 * @extends \Automattic\WooCommerce\Admin\API\Reports\Controller
 */
class Controller extends ReportsController implements ExportableInterface {

	/**
	 * Route base.
	 *
	 * @var string
	 */
	protected $rest_base = 'reports/orders';

	/**
	 * Maps query arguments from the REST request.
	 *
	 * @param array $request Request array.
	 * @return array
	 */
	protected function prepare_reports_query( $request ) {
		$args                        = array();
		$args['before']              = $request['before'];
		$args['after']               = $request['after'];
		$args['page']                = $request['page'];
		$args['per_page']            = $request['per_page'];
		$args['orderby']             = $request['orderby'];
		$args['order']               = $request['order'];
		$args['product_includes']    = (array) $request['product_includes'];
		$args['product_excludes']    = (array) $request['product_excludes'];
		$args['variation_includes']  = (array) $request['variation_includes'];
		$args['variation_excludes']  = (array) $request['variation_excludes'];
		$args['coupon_includes']     = (array) $request['coupon_includes'];
		$args['coupon_excludes']     = (array) $request['coupon_excludes'];
		$args['tax_rate_includes']   = (array) $request['tax_rate_includes'];
		$args['tax_rate_excludes']   = (array) $request['tax_rate_excludes'];
		$args['status_is']           = (array) $request['status_is'];
		$args['status_is_not']       = (array) $request['status_is_not'];
		$args['customer_type']       = $request['customer_type'];
		$args['extended_info']       = $request['extended_info'];
		$args['refunds']             = $request['refunds'];
		$args['match']               = $request['match'];
		$args['order_includes']      = $request['order_includes'];
		$args['order_excludes']      = $request['order_excludes'];
		$args['attribute_is']        = (array) $request['attribute_is'];
		$args['attribute_is_not']    = (array) $request['attribute_is_not'];
		$args['force_cache_refresh'] = $request['force_cache_refresh'];

		return $args;
	}

	/**
	 * Get all reports.
	 *
	 * @param WP_REST_Request $request Request data.
	 * @return array|WP_Error
	 */
	public function get_items( $request ) {
		$query_args   = $this->prepare_reports_query( $request );
		$orders_query = new Query( $query_args );
		$report_data  = $orders_query->get_data();

		$data = array();

		foreach ( $report_data->data as $orders_data ) {
			$orders_data['order_number']    = $this->get_order_number( $orders_data['order_id'] );
			$orders_data['total_formatted'] = $this->get_total_formatted( $orders_data['order_id'] );
			$item                           = $this->prepare_item_for_response( $orders_data, $request );
			$data[]                         = $this->prepare_response_for_collection( $item );
		}

		$response = rest_ensure_response( $data );
		$response->header( 'X-WP-Total', (int) $report_data->total );
		$response->header( 'X-WP-TotalPages', (int) $report_data->pages );

		$page      = $report_data->page_no;
		$max_pages = $report_data->pages;
		$base      = add_query_arg( $request->get_query_params(), rest_url( sprintf( '/%s/%s', $this->namespace, $this->rest_base ) ) );
		if ( $page > 1 ) {
			$prev_page = $page - 1;
			if ( $prev_page > $max_pages ) {
				$prev_page = $max_pages;
			}
			$prev_link = add_query_arg( 'page', $prev_page, $base );
			$response->link_header( 'prev', $prev_link );
		}
		if ( $max_pages > $page ) {
			$next_page = $page + 1;
			$next_link = add_query_arg( 'page', $next_page, $base );
			$response->link_header( 'next', $next_link );
		}

		return $response;
	}

	/**
	 * Prepare a report object for serialization.
	 *
	 * @param stdClass        $report  Report data.
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response
	 */
	public function prepare_item_for_response( $report, $request ) {
		$data = $report;

		$context = ! empty( $request['context'] ) ? $request['context'] : 'view';
		$data    = $this->add_additional_fields_to_object( $data, $request );
		$data    = $this->filter_response_by_context( $data, $context );

		// Wrap the data in a response object.
		$response = rest_ensure_response( $data );
		$response->add_links( $this->prepare_links( $report ) );

		/**
		 * Filter a report returned from the API.
		 *
		 * Allows modification of the report data right before it is returned.
		 *
		 * @param WP_REST_Response $response The response object.
		 * @param object           $report   The original report object.
		 * @param WP_REST_Request  $request  Request used to generate the response.
		 */
		return apply_filters( 'woocommerce_rest_prepare_report_orders', $response, $report, $request );
	}

	/**
	 * Prepare links for the request.
	 *
	 * @param WC_Reports_Query $object Object data.
	 * @return array
	 */
	protected function prepare_links( $object ) {
		$links = array(
			'order' => array(
				'href' => rest_url( sprintf( '/%s/orders/%d', $this->namespace, $object['order_id'] ) ),
			),
		);

		return $links;
	}

	/**
	 * Get the Report's schema, conforming to JSON Schema.
	 *
	 * @return array
	 */
	public function get_item_schema() {
		$schema = array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'report_orders',
			'type'       => 'object',
			'properties' => array(
				'order_id'         => array(
					'description' => __( 'Order ID.', 'woocommerce' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'order_number'     => array(
					'description' => __( 'Order Number.', 'woocommerce' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'date_created'     => array(
					'description' => __( "Date the order was created, in the site's timezone.", 'woocommerce' ),
					'type'        => 'date-time',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'date_created_gmt' => array(
					'description' => __( 'Date the order was created, as GMT.', 'woocommerce' ),
					'type'        => 'date-time',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'status'           => array(
					'description' => __( 'Order status.', 'woocommerce' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'customer_id'      => array(
					'description' => __( 'Customer ID.', 'woocommerce' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'num_items_sold'   => array(
					'description' => __( 'Number of items sold.', 'woocommerce' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'net_total'        => array(
					'description' => __( 'Net total revenue.', 'woocommerce' ),
					'type'        => 'float',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'total_formatted'  => array(
					'description' => __( 'Net total revenue (formatted).', 'woocommerce' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'customer_type'    => array(
					'description' => __( 'Returning or new customer.', 'woocommerce' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'extended_info'    => array(
					'products' => array(
						'type'        => 'array',
						'readonly'    => true,
						'context'     => array( 'view', 'edit' ),
						'description' => __( 'List of order product IDs, names, quantities.', 'woocommerce' ),
					),
					'coupons'  => array(
						'type'        => 'array',
						'readonly'    => true,
						'context'     => array( 'view', 'edit' ),
						'description' => __( 'List of order coupons.', 'woocommerce' ),
					),
					'customer' => array(
						'type'        => 'object',
						'readonly'    => true,
						'context'     => array( 'view', 'edit' ),
						'description' => __( 'Order customer information.', 'woocommerce' ),
					),
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
		$params                       = array();
		$params['context']            = $this->get_context_param( array( 'default' => 'view' ) );
		$params['page']               = array(
			'description'       => __( 'Current page of the collection.', 'woocommerce' ),
			'type'              => 'integer',
			'default'           => 1,
			'sanitize_callback' => 'absint',
			'validate_callback' => 'rest_validate_request_arg',
			'minimum'           => 1,
		);
		$params['per_page']           = array(
			'description'       => __( 'Maximum number of items to be returned in result set.', 'woocommerce' ),
			'type'              => 'integer',
			'default'           => 10,
			'minimum'           => 0,
			'maximum'           => 100,
			'sanitize_callback' => 'absint',
			'validate_callback' => 'rest_validate_request_arg',
		);
		$params['after']              = array(
			'description'       => __( 'Limit response to resources published after a given ISO8601 compliant date.', 'woocommerce' ),
			'type'              => 'string',
			'format'            => 'date-time',
			'validate_callback' => 'rest_validate_request_arg',
		);
		$params['before']             = array(
			'description'       => __( 'Limit response to resources published before a given ISO8601 compliant date.', 'woocommerce' ),
			'type'              => 'string',
			'format'            => 'date-time',
			'validate_callback' => 'rest_validate_request_arg',
		);
		$params['order']              = array(
			'description'       => __( 'Order sort attribute ascending or descending.', 'woocommerce' ),
			'type'              => 'string',
			'default'           => 'desc',
			'enum'              => array( 'asc', 'desc' ),
			'validate_callback' => 'rest_validate_request_arg',
		);
		$params['orderby']            = array(
			'description'       => __( 'Sort collection by object attribute.', 'woocommerce' ),
			'type'              => 'string',
			'default'           => 'date',
			'enum'              => array(
				'date',
				'num_items_sold',
				'net_total',
			),
			'validate_callback' => 'rest_validate_request_arg',
		);
		$params['product_includes']   = array(
			'description'       => __( 'Limit result set to items that have the specified product(s) assigned.', 'woocommerce' ),
			'type'              => 'array',
			'items'             => array(
				'type' => 'integer',
			),
			'default'           => array(),
			'sanitize_callback' => 'wp_parse_id_list',
			'validate_callback' => 'rest_validate_request_arg',
		);
		$params['product_excludes']   = array(
			'description'       => __( 'Limit result set to items that don\'t have the specified product(s) assigned.', 'woocommerce' ),
			'type'              => 'array',
			'items'             => array(
				'type' => 'integer',
			),
			'default'           => array(),
			'validate_callback' => 'rest_validate_request_arg',
			'sanitize_callback' => 'wp_parse_id_list',
		);
		$params['variation_includes'] = array(
			'description'       => __( 'Limit result set to items that have the specified variation(s) assigned.', 'woocommerce' ),
			'type'              => 'array',
			'items'             => array(
				'type' => 'integer',
			),
			'default'           => array(),
			'sanitize_callback' => 'wp_parse_id_list',
			'validate_callback' => 'rest_validate_request_arg',
		);
		$params['variation_excludes'] = array(
			'description'       => __( 'Limit result set to items that don\'t have the specified variation(s) assigned.', 'woocommerce' ),
			'type'              => 'array',
			'items'             => array(
				'type' => 'integer',
			),
			'default'           => array(),
			'validate_callback' => 'rest_validate_request_arg',
			'sanitize_callback' => 'wp_parse_id_list',
		);
		$params['coupon_includes']    = array(
			'description'       => __( 'Limit result set to items that have the specified coupon(s) assigned.', 'woocommerce' ),
			'type'              => 'array',
			'items'             => array(
				'type' => 'integer',
			),
			'default'           => array(),
			'sanitize_callback' => 'wp_parse_id_list',
			'validate_callback' => 'rest_validate_request_arg',
		);
		$params['coupon_excludes']    = array(
			'description'       => __( 'Limit result set to items that don\'t have the specified coupon(s) assigned.', 'woocommerce' ),
			'type'              => 'array',
			'items'             => array(
				'type' => 'integer',
			),
			'default'           => array(),
			'validate_callback' => 'rest_validate_request_arg',
			'sanitize_callback' => 'wp_parse_id_list',
		);
		$params['tax_rate_includes']  = array(
			'description'       => __( 'Limit result set to items that have the specified tax rate(s) assigned.', 'woocommerce' ),
			'type'              => 'array',
			'items'             => array(
				'type' => 'integer',
			),
			'default'           => array(),
			'sanitize_callback' => 'wp_parse_id_list',
			'validate_callback' => 'rest_validate_request_arg',
		);
		$params['tax_rate_excludes']  = array(
			'description'       => __( 'Limit result set to items that don\'t have the specified tax rate(s) assigned.', 'woocommerce' ),
			'type'              => 'array',
			'items'             => array(
				'type' => 'integer',
			),
			'default'           => array(),
			'validate_callback' => 'rest_validate_request_arg',
			'sanitize_callback' => 'wp_parse_id_list',
		);
		$params['status_is']          = array(
			'description'       => __( 'Limit result set to items that have the specified order status.', 'woocommerce' ),
			'type'              => 'array',
			'sanitize_callback' => 'wp_parse_slug_list',
			'validate_callback' => 'rest_validate_request_arg',
			'items'             => array(
				'enum' => self::get_order_statuses(),
				'type' => 'string',
			),
		);
		$params['status_is_not']      = array(
			'description'       => __( 'Limit result set to items that don\'t have the specified order status.', 'woocommerce' ),
			'type'              => 'array',
			'sanitize_callback' => 'wp_parse_slug_list',
			'validate_callback' => 'rest_validate_request_arg',
			'items'             => array(
				'enum' => self::get_order_statuses(),
				'type' => 'string',
			),
		);
		$params['customer_type']      = array(
			'description'       => __( 'Limit result set to returning or new customers.', 'woocommerce' ),
			'type'              => 'string',
			'default'           => '',
			'enum'              => array(
				'',
				'returning',
				'new',
			),
			'validate_callback' => 'rest_validate_request_arg',
		);
		$params['refunds']            = array(
			'description'       => __( 'Limit result set to specific types of refunds.', 'woocommerce' ),
			'type'              => 'string',
			'default'           => '',
			'enum'              => array(
				'',
				'all',
				'partial',
				'full',
				'none',
			),
			'validate_callback' => 'rest_validate_request_arg',
		);
		$params['extended_info']      = array(
			'description'       => __( 'Add additional piece of info about each coupon to the report.', 'woocommerce' ),
			'type'              => 'boolean',
			'default'           => false,
			'sanitize_callback' => 'wc_string_to_bool',
			'validate_callback' => 'rest_validate_request_arg',
		);
		$params['order_includes']     = array(
			'description'       => __( 'Limit result set to items that have the specified order ids.', 'woocommerce' ),
			'type'              => 'array',
			'sanitize_callback' => 'wp_parse_id_list',
			'validate_callback' => 'rest_validate_request_arg',
			'items'             => array(
				'type' => 'integer',
			),
		);
		$params['order_excludes']     = array(
			'description'       => __( 'Limit result set to items that don\'t have the specified order ids.', 'woocommerce' ),
			'type'              => 'array',
			'sanitize_callback' => 'wp_parse_id_list',
			'validate_callback' => 'rest_validate_request_arg',
			'items'             => array(
				'type' => 'integer',
			),
		);
		$params['attribute_is']       = array(
			'description'       => __( 'Limit result set to orders that include products with the specified attributes.', 'woocommerce' ),
			'type'              => 'array',
			'items'             => array(
				'type' => 'array',
			),
			'default'           => array(),
			'validate_callback' => 'rest_validate_request_arg',
		);
		$params['attribute_is_not']   = array(
			'description'       => __( 'Limit result set to orders that don\'t include products with the specified attributes.', 'woocommerce' ),
			'type'              => 'array',
			'items'             => array(
				'type' => 'array',
			),
			'default'           => array(),
			'validate_callback' => 'rest_validate_request_arg',
		);
		$params['force_cache_refresh'] = array(
			'description'       => __( 'Force retrieval of fresh data instead of from the cache.', 'woocommerce' ),
			'type'              => 'boolean',
			'sanitize_callback' => 'wp_validate_boolean',
			'validate_callback' => 'rest_validate_request_arg',
		);

		return $params;
	}

	/**
	 * Get customer name column export value.
	 *
	 * @param array $customer Customer from report row.
	 * @return string
	 */
	protected function get_customer_name( $customer ) {
		return $customer['first_name'] . ' ' . $customer['last_name'];
	}

	/**
	 * Get products column export value.
	 *
	 * @param array $products Products from report row.
	 * @return string
	 */
	protected function get_products( $products ) {
		$products_list = array();

		foreach ( $products as $product ) {
			$products_list[] = sprintf(
				/* translators: 1: numeric product quantity, 2: name of product */
				__( '%1$s× %2$s', 'woocommerce' ),
				$product['quantity'],
				$product['name']
			);
		}

		return implode( ', ', $products_list );
	}

	/**
	 * Get coupons column export value.
	 *
	 * @param array $coupons Coupons from report row.
	 * @return string
	 */
	protected function get_coupons( $coupons ) {
		return implode( ', ', wp_list_pluck( $coupons, 'code' ) );
	}

	/**
	 * Get the column names for export.
	 *
	 * @return array Key value pair of Column ID => Label.
	 */
	public function get_export_columns() {
		$export_columns = array(
			'date_created'    => __( 'Date', 'woocommerce' ),
			'order_number'    => __( 'Order #', 'woocommerce' ),
			'total_formatted' => __( 'N. Revenue (formatted)', 'woocommerce' ),
			'status'          => __( 'Status', 'woocommerce' ),
			'customer_name'   => __( 'Customer', 'woocommerce' ),
			'customer_type'   => __( 'Customer type', 'woocommerce' ),
			'products'        => __( 'Product(s)', 'woocommerce' ),
			'num_items_sold'  => __( 'Items sold', 'woocommerce' ),
			'coupons'         => __( 'Coupon(s)', 'woocommerce' ),
			'net_total'       => __( 'N. Revenue', 'woocommerce' ),
		);

		/**
		 * Filter to add or remove column names from the orders report for
		 * export.
		 *
		 * @since 1.6.0
		 */
		return apply_filters(
			'woocommerce_report_orders_export_columns',
			$export_columns
		);
	}

	/**
	 * Get the column values for export.
	 *
	 * @param array $item Single report item/row.
	 * @return array Key value pair of Column ID => Row Value.
	 */
	public function prepare_item_for_export( $item ) {
		$export_item = array(
			'date_created'    => $item['date_created'],
			'order_number'    => $item['order_number'],
			'total_formatted' => $item['total_formatted'],
			'status'          => $item['status'],
			'customer_name'   => isset( $item['extended_info']['customer'] ) ? $this->get_customer_name( $item['extended_info']['customer'] ) : null,
			'customer_type'   => $item['customer_type'],
			'products'        => isset( $item['extended_info']['products'] ) ? $this->get_products( $item['extended_info']['products'] ) : null,
			'num_items_sold'  => $item['num_items_sold'],
			'coupons'         => isset( $item['extended_info']['coupons'] ) ? $this->get_coupons( $item['extended_info']['coupons'] ) : null,
			'net_total'       => $item['net_total'],
		);

		/**
		 * Filter to prepare extra columns in the export item for the orders
		 * report.
		 *
		 * @since 1.6.0
		 */
		return apply_filters(
			'woocommerce_report_orders_prepare_export_item',
			$export_item,
			$item
		);
	}
}
