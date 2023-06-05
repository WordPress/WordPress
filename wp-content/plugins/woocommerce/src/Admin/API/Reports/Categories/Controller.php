<?php
/**
 * REST API Reports categories controller
 *
 * Handles requests to the /reports/categories endpoint.
 */

namespace Automattic\WooCommerce\Admin\API\Reports\Categories;

defined( 'ABSPATH' ) || exit;

use Automattic\WooCommerce\Admin\API\Reports\Controller as ReportsController;
use Automattic\WooCommerce\Admin\API\Reports\ExportableInterface;

/**
 * REST API Reports categories controller class.
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
	protected $rest_base = 'reports/categories';

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
		$args['interval']            = $request['interval'];
		$args['page']                = $request['page'];
		$args['per_page']            = $request['per_page'];
		$args['orderby']             = $request['orderby'];
		$args['order']               = $request['order'];
		$args['extended_info']       = $request['extended_info'];
		$args['category_includes']   = (array) $request['categories'];
		$args['status_is']           = (array) $request['status_is'];
		$args['status_is_not']       = (array) $request['status_is_not'];
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
		$query_args       = $this->prepare_reports_query( $request );
		$categories_query = new Query( $query_args );
		$report_data      = $categories_query->get_data();

		if ( is_wp_error( $report_data ) ) {
			return $report_data;
		}

		if ( ! isset( $report_data->data ) || ! isset( $report_data->page_no ) || ! isset( $report_data->pages ) ) {
			return new \WP_Error( 'woocommerce_rest_reports_categories_invalid_response', __( 'Invalid response from data store.', 'woocommerce' ), array( 'status' => 500 ) );
		}

		$out_data = array();

		foreach ( $report_data->data as $datum ) {
			$item       = $this->prepare_item_for_response( $datum, $request );
			$out_data[] = $this->prepare_response_for_collection( $item );
		}

		$response = rest_ensure_response( $out_data );
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
		return apply_filters( 'woocommerce_rest_prepare_report_categories', $response, $report, $request );
	}

	/**
	 * Prepare links for the request.
	 *
	 * @param \Automattic\WooCommerce\Admin\API\Reports\Query $object Object data.
	 * @return array
	 */
	protected function prepare_links( $object ) {
		$links = array(
			'category' => array(
				'href' => rest_url( sprintf( '/%s/products/categories/%d', $this->namespace, $object['category_id'] ) ),
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
			'title'      => 'report_categories',
			'type'       => 'object',
			'properties' => array(
				'category_id'    => array(
					'description' => __( 'Category ID.', 'woocommerce' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'items_sold'     => array(
					'description' => __( 'Amount of items sold.', 'woocommerce' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'net_revenue'    => array(
					'description' => __( 'Total sales.', 'woocommerce' ),
					'type'        => 'number',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'orders_count'   => array(
					'description' => __( 'Number of orders.', 'woocommerce' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'products_count' => array(
					'description' => __( 'Amount of products.', 'woocommerce' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'extended_info'  => array(
					'name' => array(
						'type'        => 'string',
						'readonly'    => true,
						'context'     => array( 'view', 'edit' ),
						'description' => __( 'Category name.', 'woocommerce' ),
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
		$params                  = array();
		$params['context']       = $this->get_context_param( array( 'default' => 'view' ) );
		$params['page']          = array(
			'description'       => __( 'Current page of the collection.', 'woocommerce' ),
			'type'              => 'integer',
			'default'           => 1,
			'sanitize_callback' => 'absint',
			'validate_callback' => 'rest_validate_request_arg',
			'minimum'           => 1,
		);
		$params['per_page']      = array(
			'description'       => __( 'Maximum number of items to be returned in result set.', 'woocommerce' ),
			'type'              => 'integer',
			'default'           => 10,
			'minimum'           => 1,
			'maximum'           => 100,
			'sanitize_callback' => 'absint',
			'validate_callback' => 'rest_validate_request_arg',
		);
		$params['after']         = array(
			'description'       => __( 'Limit response to resources published after a given ISO8601 compliant date.', 'woocommerce' ),
			'type'              => 'string',
			'format'            => 'date-time',
			'validate_callback' => 'rest_validate_request_arg',
		);
		$params['before']        = array(
			'description'       => __( 'Limit response to resources published before a given ISO8601 compliant date.', 'woocommerce' ),
			'type'              => 'string',
			'format'            => 'date-time',
			'validate_callback' => 'rest_validate_request_arg',
		);
		$params['order']         = array(
			'description'       => __( 'Order sort attribute ascending or descending.', 'woocommerce' ),
			'type'              => 'string',
			'default'           => 'desc',
			'enum'              => array( 'asc', 'desc' ),
			'validate_callback' => 'rest_validate_request_arg',
		);
		$params['orderby']       = array(
			'description'       => __( 'Sort collection by object attribute.', 'woocommerce' ),
			'type'              => 'string',
			'default'           => 'category_id',
			'enum'              => array(
				'category_id',
				'items_sold',
				'net_revenue',
				'orders_count',
				'products_count',
				'category',
			),
			'validate_callback' => 'rest_validate_request_arg',
		);
		$params['interval']      = array(
			'description'       => __( 'Time interval to use for buckets in the returned data.', 'woocommerce' ),
			'type'              => 'string',
			'default'           => 'week',
			'enum'              => array(
				'hour',
				'day',
				'week',
				'month',
				'quarter',
				'year',
			),
			'validate_callback' => 'rest_validate_request_arg',
		);
		$params['status_is']     = array(
			'description'       => __( 'Limit result set to items that have the specified order status.', 'woocommerce' ),
			'type'              => 'array',
			'sanitize_callback' => 'wp_parse_slug_list',
			'validate_callback' => 'rest_validate_request_arg',
			'items'             => array(
				'enum' => self::get_order_statuses(),
				'type' => 'string',
			),
		);
		$params['status_is_not'] = array(
			'description'       => __( 'Limit result set to items that don\'t have the specified order status.', 'woocommerce' ),
			'type'              => 'array',
			'sanitize_callback' => 'wp_parse_slug_list',
			'validate_callback' => 'rest_validate_request_arg',
			'items'             => array(
				'enum' => self::get_order_statuses(),
				'type' => 'string',
			),
		);
		$params['categories']    = array(
			'description'       => __( 'Limit result set to all items that have the specified term assigned in the categories taxonomy.', 'woocommerce' ),
			'type'              => 'array',
			'sanitize_callback' => 'wp_parse_id_list',
			'validate_callback' => 'rest_validate_request_arg',
			'items'             => array(
				'type' => 'integer',
			),
		);
		$params['extended_info'] = array(
			'description'       => __( 'Add additional piece of info about each category to the report.', 'woocommerce' ),
			'type'              => 'boolean',
			'default'           => false,
			'sanitize_callback' => 'wc_string_to_bool',
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
	 * Get the column names for export.
	 *
	 * @return array Key value pair of Column ID => Label.
	 */
	public function get_export_columns() {
		$export_columns = array(
			'category'       => __( 'Category', 'woocommerce' ),
			'items_sold'     => __( 'Items sold', 'woocommerce' ),
			'net_revenue'    => __( 'Net Revenue', 'woocommerce' ),
			'products_count' => __( 'Products', 'woocommerce' ),
			'orders_count'   => __( 'Orders', 'woocommerce' ),
		);

		/**
		 * Filter to add or remove column names from the categories report for
		 * export.
		 *
		 * @since 1.6.0
		 */
		return apply_filters(
			'woocommerce_report_categories_export_columns',
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
			'category'       => $item['extended_info']['name'],
			'items_sold'     => $item['items_sold'],
			'net_revenue'    => $item['net_revenue'],
			'products_count' => $item['products_count'],
			'orders_count'   => $item['orders_count'],
		);

		/**
		 * Filter to prepare extra columns in the export item for the
		 * categories export.
		 *
		 * @since 1.6.0
		 */
		return apply_filters(
			'woocommerce_report_categories_prepare_export_item',
			$export_item,
			$item
		);
	}
}
