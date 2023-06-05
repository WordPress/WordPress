<?php
/**
 * REST API Reports products controller
 *
 * Handles requests to the /reports/products endpoint.
 */

namespace Automattic\WooCommerce\Admin\API\Reports\Variations;

defined( 'ABSPATH' ) || exit;

use Automattic\WooCommerce\Admin\API\Reports\Controller as ReportsController;
use Automattic\WooCommerce\Admin\API\Reports\ExportableInterface;
use Automattic\WooCommerce\Admin\API\Reports\ExportableTraits;

/**
 * REST API Reports products controller class.
 *
 * @internal
 * @extends ReportsController
 */
class Controller extends ReportsController implements ExportableInterface {
	/**
	 * Exportable traits.
	 */
	use ExportableTraits;

	/**
	 * Route base.
	 *
	 * @var string
	 */
	protected $rest_base = 'reports/variations';

	/**
	 * Mapping between external parameter name and name used in query class.
	 *
	 * @var array
	 */
	protected $param_mapping = array(
		'variations' => 'variation_includes',
	);

	/**
	 * Get items.
	 *
	 * @param WP_REST_Request $request Request data.
	 *
	 * @return array|WP_Error
	 */
	public function get_items( $request ) {
		$args = array();
		/**
		 * Experimental: Filter the list of parameters provided when querying data from the data store.
		 *
		 * @ignore
		 *
		 * @param array $collection_params List of parameters.
		 */
		$collection_params = apply_filters(
			'experimental_woocommerce_analytics_variations_collection_params',
			$this->get_collection_params()
		);
		$registered        = array_keys( $collection_params );
		foreach ( $registered as $param_name ) {
			if ( isset( $request[ $param_name ] ) ) {
				if ( isset( $this->param_mapping[ $param_name ] ) ) {
					$args[ $this->param_mapping[ $param_name ] ] = $request[ $param_name ];
				} else {
					$args[ $param_name ] = $request[ $param_name ];
				}
			}
		}

		$reports       = new Query( $args );
		$products_data = $reports->get_data();

		$data = array();

		foreach ( $products_data->data as $product_data ) {
			$item   = $this->prepare_item_for_response( $product_data, $request );
			$data[] = $this->prepare_response_for_collection( $item );
		}

		$response = rest_ensure_response( $data );
		$response->header( 'X-WP-Total', (int) $products_data->total );
		$response->header( 'X-WP-TotalPages', (int) $products_data->pages );

		$page      = $products_data->page_no;
		$max_pages = $products_data->pages;
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
	 * @param array           $report  Report data.
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
		return apply_filters( 'woocommerce_rest_prepare_report_variations', $response, $report, $request );
	}

	/**
	 * Prepare links for the request.
	 *
	 * @param array $object Object data.
	 * @return array        Links for the given post.
	 */
	protected function prepare_links( $object ) {
		$links = array(
			'product'   => array(
				'href' => rest_url( sprintf( '/%s/%s/%d', $this->namespace, 'products', $object['product_id'] ) ),
			),
			'variation' => array(
				'href' => rest_url( sprintf( '/%s/%s/%d/%s/%d', $this->namespace, 'products', $object['product_id'], 'variation', $object['variation_id'] ) ),
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
			'title'      => 'report_varitations',
			'type'       => 'object',
			'properties' => array(
				'product_id'    => array(
					'type'        => 'integer',
					'readonly'    => true,
					'context'     => array( 'view', 'edit' ),
					'description' => __( 'Product ID.', 'woocommerce' ),
				),
				'variation_id'  => array(
					'type'        => 'integer',
					'readonly'    => true,
					'context'     => array( 'view', 'edit' ),
					'description' => __( 'Product ID.', 'woocommerce' ),
				),
				'items_sold'    => array(
					'type'        => 'integer',
					'readonly'    => true,
					'context'     => array( 'view', 'edit' ),
					'description' => __( 'Number of items sold.', 'woocommerce' ),
				),
				'net_revenue'   => array(
					'type'        => 'number',
					'readonly'    => true,
					'context'     => array( 'view', 'edit' ),
					'description' => __( 'Total Net sales of all items sold.', 'woocommerce' ),
				),
				'orders_count'  => array(
					'type'        => 'integer',
					'readonly'    => true,
					'context'     => array( 'view', 'edit' ),
					'description' => __( 'Number of orders product appeared in.', 'woocommerce' ),
				),
				'extended_info' => array(
					'name'             => array(
						'type'        => 'string',
						'readonly'    => true,
						'context'     => array( 'view', 'edit' ),
						'description' => __( 'Product name.', 'woocommerce' ),
					),
					'price'            => array(
						'type'        => 'number',
						'readonly'    => true,
						'context'     => array( 'view', 'edit' ),
						'description' => __( 'Product price.', 'woocommerce' ),
					),
					'image'            => array(
						'type'        => 'string',
						'readonly'    => true,
						'context'     => array( 'view', 'edit' ),
						'description' => __( 'Product image.', 'woocommerce' ),
					),
					'permalink'        => array(
						'type'        => 'string',
						'readonly'    => true,
						'context'     => array( 'view', 'edit' ),
						'description' => __( 'Product link.', 'woocommerce' ),
					),
					'attributes'       => array(
						'type'        => 'array',
						'readonly'    => true,
						'context'     => array( 'view', 'edit' ),
						'description' => __( 'Product attributes.', 'woocommerce' ),
					),
					'stock_status'     => array(
						'type'        => 'string',
						'readonly'    => true,
						'context'     => array( 'view', 'edit' ),
						'description' => __( 'Product inventory status.', 'woocommerce' ),
					),
					'stock_quantity'   => array(
						'type'        => 'integer',
						'readonly'    => true,
						'context'     => array( 'view', 'edit' ),
						'description' => __( 'Product inventory quantity.', 'woocommerce' ),
					),
					'low_stock_amount' => array(
						'type'        => 'integer',
						'readonly'    => true,
						'context'     => array( 'view', 'edit' ),
						'description' => __( 'Product inventory threshold for low stock.', 'woocommerce' ),
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
		$params                      = array();
		$params['context']           = $this->get_context_param( array( 'default' => 'view' ) );
		$params['page']              = array(
			'description'       => __( 'Current page of the collection.', 'woocommerce' ),
			'type'              => 'integer',
			'default'           => 1,
			'sanitize_callback' => 'absint',
			'validate_callback' => 'rest_validate_request_arg',
			'minimum'           => 1,
		);
		$params['per_page']          = array(
			'description'       => __( 'Maximum number of items to be returned in result set.', 'woocommerce' ),
			'type'              => 'integer',
			'default'           => 10,
			'minimum'           => 1,
			'maximum'           => 100,
			'sanitize_callback' => 'absint',
			'validate_callback' => 'rest_validate_request_arg',
		);
		$params['after']             = array(
			'description'       => __( 'Limit response to resources published after a given ISO8601 compliant date.', 'woocommerce' ),
			'type'              => 'string',
			'format'            => 'date-time',
			'validate_callback' => 'rest_validate_request_arg',
		);
		$params['before']            = array(
			'description'       => __( 'Limit response to resources published before a given ISO8601 compliant date.', 'woocommerce' ),
			'type'              => 'string',
			'format'            => 'date-time',
			'validate_callback' => 'rest_validate_request_arg',
		);
		$params['match']             = array(
			'description'       => __( 'Indicates whether all the conditions should be true for the resulting set, or if any one of them is sufficient. Match affects the following parameters: status_is, status_is_not, product_includes, product_excludes, coupon_includes, coupon_excludes, customer, categories', 'woocommerce' ),
			'type'              => 'string',
			'default'           => 'all',
			'enum'              => array(
				'all',
				'any',
			),
			'validate_callback' => 'rest_validate_request_arg',
		);
		$params['order']             = array(
			'description'       => __( 'Order sort attribute ascending or descending.', 'woocommerce' ),
			'type'              => 'string',
			'default'           => 'desc',
			'enum'              => array( 'asc', 'desc' ),
			'validate_callback' => 'rest_validate_request_arg',
		);
		$params['orderby']           = array(
			'description'       => __( 'Sort collection by object attribute.', 'woocommerce' ),
			'type'              => 'string',
			'default'           => 'date',
			'enum'              => array(
				'date',
				'net_revenue',
				'orders_count',
				'items_sold',
				'sku',
			),
			'validate_callback' => 'rest_validate_request_arg',
		);
		$params['product_includes']  = array(
			'description'       => __( 'Limit result set to items that have the specified parent product(s).', 'woocommerce' ),
			'type'              => 'array',
			'items'             => array(
				'type' => 'integer',
			),
			'default'           => array(),
			'sanitize_callback' => 'wp_parse_id_list',
			'validate_callback' => 'rest_validate_request_arg',
		);
		$params['product_excludes']  = array(
			'description'       => __( 'Limit result set to items that don\'t have the specified parent product(s).', 'woocommerce' ),
			'type'              => 'array',
			'items'             => array(
				'type' => 'integer',
			),
			'default'           => array(),
			'validate_callback' => 'rest_validate_request_arg',
			'sanitize_callback' => 'wp_parse_id_list',
		);
		$params['variations']        = array(
			'description'       => __( 'Limit result to items with specified variation ids.', 'woocommerce' ),
			'type'              => 'array',
			'sanitize_callback' => 'wp_parse_id_list',
			'validate_callback' => 'rest_validate_request_arg',
			'items'             => array(
				'type' => 'integer',
			),
		);
		$params['extended_info']     = array(
			'description'       => __( 'Add additional piece of info about each variation to the report.', 'woocommerce' ),
			'type'              => 'boolean',
			'default'           => false,
			'sanitize_callback' => 'wc_string_to_bool',
			'validate_callback' => 'rest_validate_request_arg',
		);
		$params['attribute_is']      = array(
			'description'       => __( 'Limit result set to variations that include the specified attributes.', 'woocommerce' ),
			'type'              => 'array',
			'items'             => array(
				'type' => 'array',
			),
			'default'           => array(),
			'validate_callback' => 'rest_validate_request_arg',
		);
		$params['attribute_is_not']  = array(
			'description'       => __( 'Limit result set to variations that don\'t include the specified attributes.', 'woocommerce' ),
			'type'              => 'array',
			'items'             => array(
				'type' => 'array',
			),
			'default'           => array(),
			'validate_callback' => 'rest_validate_request_arg',
		);
		$params['category_includes'] = array(
			'description'       => __( 'Limit result set to variations in the specified categories.', 'woocommerce' ),
			'type'              => 'array',
			'sanitize_callback' => 'wp_parse_id_list',
			'validate_callback' => 'rest_validate_request_arg',
			'items'             => array(
				'type' => 'integer',
			),
		);
		$params['category_excludes'] = array(
			'description'       => __( 'Limit result set to variations not in the specified categories.', 'woocommerce' ),
			'type'              => 'array',
			'sanitize_callback' => 'wp_parse_id_list',
			'validate_callback' => 'rest_validate_request_arg',
			'items'             => array(
				'type' => 'integer',
			),
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
	 * Get stock status column export value.
	 *
	 * @param array $status Stock status from report row.
	 * @return string
	 */
	protected function get_stock_status( $status ) {
		$statuses = wc_get_product_stock_status_options();

		return isset( $statuses[ $status ] ) ? $statuses[ $status ] : '';
	}

	/**
	 * Get the column names for export.
	 *
	 * @return array Key value pair of Column ID => Label.
	 */
	public function get_export_columns() {
		$export_columns = array(
			'product_name' => __( 'Product / Variation title', 'woocommerce' ),
			'sku'          => __( 'SKU', 'woocommerce' ),
			'items_sold'   => __( 'Items sold', 'woocommerce' ),
			'net_revenue'  => __( 'N. Revenue', 'woocommerce' ),
			'orders_count' => __( 'Orders', 'woocommerce' ),
		);

		if ( 'yes' === get_option( 'woocommerce_manage_stock' ) ) {
			$export_columns['stock_status'] = __( 'Status', 'woocommerce' );
			$export_columns['stock']        = __( 'Stock', 'woocommerce' );
		}

		return $export_columns;
	}

	/**
	 * Get the column values for export.
	 *
	 * @param array $item Single report item/row.
	 * @return array Key value pair of Column ID => Row Value.
	 */
	public function prepare_item_for_export( $item ) {
		$export_item = array(
			'product_name' => $item['extended_info']['name'],
			'sku'          => $item['extended_info']['sku'],
			'items_sold'   => $item['items_sold'],
			'net_revenue'  => self::csv_number_format( $item['net_revenue'] ),
			'orders_count' => $item['orders_count'],
		);

		if ( 'yes' === get_option( 'woocommerce_manage_stock' ) ) {
			$export_item['stock_status'] = $this->get_stock_status( $item['extended_info']['stock_status'] );
			$export_item['stock']        = $item['extended_info']['stock_quantity'];
		}

		return $export_item;
	}
}
