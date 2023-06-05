<?php
/**
 * REST API Reports stock controller
 *
 * Handles requests to the /reports/stock endpoint.
 */

namespace Automattic\WooCommerce\Admin\API\Reports\Stock;

defined( 'ABSPATH' ) || exit;

use Automattic\WooCommerce\Admin\API\Reports\ExportableInterface;

/**
 * REST API Reports stock controller class.
 *
 * @internal
 * @extends WC_REST_Reports_Controller
 */
class Controller extends \WC_REST_Reports_Controller implements ExportableInterface {

	/**
	 * Endpoint namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'wc-analytics';

	/**
	 * Route base.
	 *
	 * @var string
	 */
	protected $rest_base = 'reports/stock';

	/**
	 * Registered stock status options.
	 *
	 * @var array
	 */
	protected $status_options;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->status_options = wc_get_product_stock_status_options();
	}

	/**
	 * Maps query arguments from the REST request.
	 *
	 * @param  WP_REST_Request $request Request array.
	 * @return array
	 */
	protected function prepare_reports_query( $request ) {
		$args                        = array();
		$args['offset']              = $request['offset'];
		$args['order']               = $request['order'];
		$args['orderby']             = $request['orderby'];
		$args['paged']               = $request['page'];
		$args['post__in']            = $request['include'];
		$args['post__not_in']        = $request['exclude'];
		$args['posts_per_page']      = $request['per_page'];
		$args['post_parent__in']     = $request['parent'];
		$args['post_parent__not_in'] = $request['parent_exclude'];

		if ( 'date' === $args['orderby'] ) {
			$args['orderby'] = 'date ID';
		} elseif ( 'include' === $args['orderby'] ) {
			$args['orderby'] = 'post__in';
		} elseif ( 'id' === $args['orderby'] ) {
			$args['orderby'] = 'ID'; // ID must be capitalized.
		}

		$args['post_type'] = array( 'product', 'product_variation' );

		if ( 'lowstock' === $request['type'] ) {
			$args['low_in_stock'] = true;
		} elseif ( in_array( $request['type'], array_keys( $this->status_options ), true ) ) {
			$args['stock_status'] = $request['type'];
		}

		$args['ignore_sticky_posts'] = true;

		return $args;
	}

	/**
	 * Query products.
	 *
	 * @param  array $query_args Query args.
	 * @return array
	 */
	protected function get_products( $query_args ) {
		$query  = new \WP_Query();
		$result = $query->query( $query_args );

		$total_posts = $query->found_posts;
		if ( $total_posts < 1 ) {
			// Out-of-bounds, run the query again without LIMIT for total count.
			unset( $query_args['paged'] );
			$count_query = new \WP_Query();
			$count_query->query( $query_args );
			$total_posts = $count_query->found_posts;
		}

		return array(
			'objects' => array_map( 'wc_get_product', $result ),
			'total'   => (int) $total_posts,
			'pages'   => (int) ceil( $total_posts / (int) $query->query_vars['posts_per_page'] ),
		);
	}

	/**
	 * Get all reports.
	 *
	 * @param  WP_REST_Request $request Request data.
	 * @return array|WP_Error
	 */
	public function get_items( $request ) {
		add_filter( 'posts_where', array( __CLASS__, 'add_wp_query_filter' ), 10, 2 );
		add_filter( 'posts_join', array( __CLASS__, 'add_wp_query_join' ), 10, 2 );
		add_filter( 'posts_groupby', array( __CLASS__, 'add_wp_query_group_by' ), 10, 2 );
		add_filter( 'posts_clauses', array( __CLASS__, 'add_wp_query_orderby' ), 10, 2 );
		$query_args    = $this->prepare_reports_query( $request );
		$query_results = $this->get_products( $query_args );
		remove_filter( 'posts_where', array( __CLASS__, 'add_wp_query_filter' ), 10 );
		remove_filter( 'posts_join', array( __CLASS__, 'add_wp_query_join' ), 10 );
		remove_filter( 'posts_groupby', array( __CLASS__, 'add_wp_query_group_by' ), 10 );
		remove_filter( 'posts_clauses', array( __CLASS__, 'add_wp_query_orderby' ), 10 );

		$objects = array();
		foreach ( $query_results['objects'] as $object ) {
			$data      = $this->prepare_item_for_response( $object, $request );
			$objects[] = $this->prepare_response_for_collection( $data );
		}

		$page      = (int) $query_args['paged'];
		$max_pages = $query_results['pages'];

		$response = rest_ensure_response( $objects );
		$response->header( 'X-WP-Total', $query_results['total'] );
		$response->header( 'X-WP-TotalPages', (int) $max_pages );

		$base = add_query_arg( $request->get_query_params(), rest_url( sprintf( '/%s/%s', $this->namespace, $this->rest_base ) ) );

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
	 * Add in conditional search filters for products.
	 *
	 * @internal
	 * @param string $where Where clause used to search posts.
	 * @param object $wp_query WP_Query object.
	 * @return string
	 */
	public static function add_wp_query_filter( $where, $wp_query ) {
		global $wpdb;

		$stock_status = $wp_query->get( 'stock_status' );
		if ( $stock_status ) {
			$where .= $wpdb->prepare(
				' AND wc_product_meta_lookup.stock_status = %s ',
				$stock_status
			);
		}

		if ( $wp_query->get( 'low_in_stock' ) ) {
			// We want products with stock < low stock amount, but greater than no stock amount.
			$no_stock_amount  = absint( max( get_option( 'woocommerce_notify_no_stock_amount' ), 0 ) );
			$low_stock_amount = absint( max( get_option( 'woocommerce_notify_low_stock_amount' ), 1 ) );
			$where           .= "
			AND wc_product_meta_lookup.stock_quantity IS NOT NULL
			AND wc_product_meta_lookup.stock_status = 'instock'
			AND (
				(
					low_stock_amount_meta.meta_value > ''
					AND wc_product_meta_lookup.stock_quantity <= CAST(low_stock_amount_meta.meta_value AS SIGNED)
					AND wc_product_meta_lookup.stock_quantity > {$no_stock_amount}
				)
				OR (
					(
						low_stock_amount_meta.meta_value IS NULL OR low_stock_amount_meta.meta_value <= ''
					)
					AND wc_product_meta_lookup.stock_quantity <= {$low_stock_amount}
					AND wc_product_meta_lookup.stock_quantity > {$no_stock_amount}
				)
			)";
		}

		return $where;
	}

	/**
	 * Join posts meta tables when product search or low stock query is present.
	 *
	 * @internal
	 * @param string $join Join clause used to search posts.
	 * @param object $wp_query WP_Query object.
	 * @return string
	 */
	public static function add_wp_query_join( $join, $wp_query ) {
		global $wpdb;

		$stock_status = $wp_query->get( 'stock_status' );
		if ( $stock_status ) {
			$join = self::append_product_sorting_table_join( $join );
		}

		if ( $wp_query->get( 'low_in_stock' ) ) {
			$join  = self::append_product_sorting_table_join( $join );
			$join .= " LEFT JOIN {$wpdb->postmeta} AS low_stock_amount_meta ON {$wpdb->posts}.ID = low_stock_amount_meta.post_id AND low_stock_amount_meta.meta_key = '_low_stock_amount' ";
		}

		return $join;
	}

	/**
	 * Join wc_product_meta_lookup to posts if not already joined.
	 *
	 * @internal
	 * @param string $sql SQL join.
	 * @return string
	 */
	protected static function append_product_sorting_table_join( $sql ) {
		global $wpdb;

		if ( ! strstr( $sql, 'wc_product_meta_lookup' ) ) {
			$sql .= " LEFT JOIN {$wpdb->wc_product_meta_lookup} wc_product_meta_lookup ON $wpdb->posts.ID = wc_product_meta_lookup.product_id ";
		}
		return $sql;
	}

	/**
	 * Group by post ID to prevent duplicates.
	 *
	 * @internal
	 * @param string $groupby Group by clause used to organize posts.
	 * @param object $wp_query WP_Query object.
	 * @return string
	 */
	public static function add_wp_query_group_by( $groupby, $wp_query ) {
		global $wpdb;

		if ( empty( $groupby ) ) {
			$groupby = $wpdb->posts . '.ID';
		}
		return $groupby;
	}

	/**
	 * Custom orderby clauses using the lookup tables.
	 *
	 * @internal
	 * @param array  $args Query args.
	 * @param object $wp_query WP_Query object.
	 * @return array
	 */
	public static function add_wp_query_orderby( $args, $wp_query ) {
		global $wpdb;

		$orderby = $wp_query->get( 'orderby' );
		$order   = esc_sql( $wp_query->get( 'order' ) ? $wp_query->get( 'order' ) : 'desc' );

		switch ( $orderby ) {
			case 'stock_quantity':
				$args['join']    = self::append_product_sorting_table_join( $args['join'] );
				$args['orderby'] = " wc_product_meta_lookup.stock_quantity {$order}, wc_product_meta_lookup.product_id {$order} ";
				break;
			case 'stock_status':
				$args['join']    = self::append_product_sorting_table_join( $args['join'] );
				$args['orderby'] = " wc_product_meta_lookup.stock_status {$order}, wc_product_meta_lookup.stock_quantity {$order} ";
				break;
			case 'sku':
				$args['join']    = self::append_product_sorting_table_join( $args['join'] );
				$args['orderby'] = " wc_product_meta_lookup.sku {$order}, wc_product_meta_lookup.product_id {$order} ";
				break;
		}

		return $args;
	}

	/**
	 * Prepare a report object for serialization.
	 *
	 * @param  WC_Product      $product  Report data.
	 * @param  WP_REST_Request $request Request object.
	 * @return WP_REST_Response
	 */
	public function prepare_item_for_response( $product, $request ) {
		$data = array(
			'id'               => $product->get_id(),
			'parent_id'        => $product->get_parent_id(),
			'name'             => wp_strip_all_tags( $product->get_name() ),
			'sku'              => $product->get_sku(),
			'stock_status'     => $product->get_stock_status(),
			'stock_quantity'   => (float) $product->get_stock_quantity(),
			'manage_stock'     => $product->get_manage_stock(),
			'low_stock_amount' => $product->get_low_stock_amount(),
		);

		if ( '' === $data['low_stock_amount'] ) {
			$data['low_stock_amount'] = absint( max( get_option( 'woocommerce_notify_low_stock_amount' ), 1 ) );
		}

		$context = ! empty( $request['context'] ) ? $request['context'] : 'view';
		$data    = $this->add_additional_fields_to_object( $data, $request );
		$data    = $this->filter_response_by_context( $data, $context );

		// Wrap the data in a response object.
		$response = rest_ensure_response( $data );
		$response->add_links( $this->prepare_links( $product ) );

		/**
		 * Filter a report returned from the API.
		 *
		 * Allows modification of the report data right before it is returned.
		 *
		 * @param WP_REST_Response $response The response object.
		 * @param WC_Product       $product   The original product object.
		 * @param WP_REST_Request  $request  Request used to generate the response.
		 */
		return apply_filters( 'woocommerce_rest_prepare_report_stock', $response, $product, $request );
	}

	/**
	 * Prepare links for the request.
	 *
	 * @param  WC_Product $product Object data.
	 * @return array
	 */
	protected function prepare_links( $product ) {
		if ( $product->is_type( 'variation' ) ) {
			$links = array(
				'product' => array(
					'href' => rest_url( sprintf( '/%s/products/%d/variations/%d', $this->namespace, $product->get_parent_id(), $product->get_id() ) ),
				),
				'parent'  => array(
					'href' => rest_url( sprintf( '/%s/products/%d', $this->namespace, $product->get_parent_id() ) ),
				),
			);
		} elseif ( $product->get_parent_id() ) {
			$links = array(
				'product' => array(
					'href' => rest_url( sprintf( '/%s/products/%d', $this->namespace, $product->get_id() ) ),
				),
				'parent'  => array(
					'href' => rest_url( sprintf( '/%s/products/%d', $this->namespace, $product->get_parent_id() ) ),
				),
			);
		} else {
			$links = array(
				'product' => array(
					'href' => rest_url( sprintf( '/%s/products/%d', $this->namespace, $product->get_id() ) ),
				),
			);
		}

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
			'title'      => 'report_stock',
			'type'       => 'object',
			'properties' => array(
				'id'             => array(
					'description' => __( 'Unique identifier for the resource.', 'woocommerce' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'parent_id'      => array(
					'description' => __( 'Product parent ID.', 'woocommerce' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'name'           => array(
					'description' => __( 'Product name.', 'woocommerce' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'sku'            => array(
					'description' => __( 'Unique identifier.', 'woocommerce' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'stock_status'   => array(
					'description' => __( 'Stock status.', 'woocommerce' ),
					'type'        => 'string',
					'enum'        => array_keys( wc_get_product_stock_status_options() ),
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'stock_quantity' => array(
					'description' => __( 'Stock quantity.', 'woocommerce' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'manage_stock'   => array(
					'description' => __( 'Manage stock.', 'woocommerce' ),
					'type'        => 'boolean',
					'context'     => array( 'view', 'edit' ),
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
		$params                   = array();
		$params['context']        = $this->get_context_param( array( 'default' => 'view' ) );
		$params['page']           = array(
			'description'       => __( 'Current page of the collection.', 'woocommerce' ),
			'type'              => 'integer',
			'default'           => 1,
			'sanitize_callback' => 'absint',
			'validate_callback' => 'rest_validate_request_arg',
			'minimum'           => 1,
		);
		$params['per_page']       = array(
			'description'       => __( 'Maximum number of items to be returned in result set.', 'woocommerce' ),
			'type'              => 'integer',
			'default'           => 10,
			'minimum'           => 1,
			'maximum'           => 100,
			'sanitize_callback' => 'absint',
			'validate_callback' => 'rest_validate_request_arg',
		);
		$params['exclude']        = array(
			'description'       => __( 'Ensure result set excludes specific IDs.', 'woocommerce' ),
			'type'              => 'array',
			'items'             => array(
				'type' => 'integer',
			),
			'default'           => array(),
			'sanitize_callback' => 'wp_parse_id_list',
		);
		$params['include']        = array(
			'description'       => __( 'Limit result set to specific ids.', 'woocommerce' ),
			'type'              => 'array',
			'items'             => array(
				'type' => 'integer',
			),
			'default'           => array(),
			'sanitize_callback' => 'wp_parse_id_list',
		);
		$params['offset']         = array(
			'description'       => __( 'Offset the result set by a specific number of items.', 'woocommerce' ),
			'type'              => 'integer',
			'sanitize_callback' => 'absint',
			'validate_callback' => 'rest_validate_request_arg',
		);
		$params['order']          = array(
			'description'       => __( 'Order sort attribute ascending or descending.', 'woocommerce' ),
			'type'              => 'string',
			'default'           => 'asc',
			'enum'              => array( 'asc', 'desc' ),
			'validate_callback' => 'rest_validate_request_arg',
		);
		$params['orderby']        = array(
			'description'       => __( 'Sort collection by object attribute.', 'woocommerce' ),
			'type'              => 'string',
			'default'           => 'stock_status',
			'enum'              => array(
				'stock_status',
				'stock_quantity',
				'date',
				'id',
				'include',
				'title',
				'sku',
			),
			'validate_callback' => 'rest_validate_request_arg',
		);
		$params['parent']         = array(
			'description'       => __( 'Limit result set to those of particular parent IDs.', 'woocommerce' ),
			'type'              => 'array',
			'items'             => array(
				'type' => 'integer',
			),
			'sanitize_callback' => 'wp_parse_id_list',
			'default'           => array(),
		);
		$params['parent_exclude'] = array(
			'description'       => __( 'Limit result set to all items except those of a particular parent ID.', 'woocommerce' ),
			'type'              => 'array',
			'items'             => array(
				'type' => 'integer',
			),
			'sanitize_callback' => 'wp_parse_id_list',
			'default'           => array(),
		);
		$params['type']           = array(
			'description' => __( 'Limit result set to items assigned a stock report type.', 'woocommerce' ),
			'type'        => 'string',
			'default'     => 'all',
			'enum'        => array_merge( array( 'all', 'lowstock' ), array_keys( wc_get_product_stock_status_options() ) ),
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
			'title'          => __( 'Product / Variation', 'woocommerce' ),
			'sku'            => __( 'SKU', 'woocommerce' ),
			'stock_status'   => __( 'Status', 'woocommerce' ),
			'stock_quantity' => __( 'Stock', 'woocommerce' ),
		);

		/**
		 * Filter to add or remove column names from the stock report for
		 * export.
		 *
		 * @since 1.6.0
		 */
		return apply_filters(
			'woocommerce_report_stock_export_columns',
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
		$status = $item['stock_status'];
		if ( array_key_exists( $item['stock_status'], $this->status_options ) ) {
			$status = $this->status_options[ $item['stock_status'] ];
		}

		$export_item = array(
			'title'          => $item['name'],
			'sku'            => $item['sku'],
			'stock_status'   => $status,
			'stock_quantity' => $item['stock_quantity'],
		);

		/**
		 * Filter to prepare extra columns in the export item for the stock
		 * report.
		 *
		 * @since 1.6.0
		 */
		return apply_filters(
			'woocommerce_report_stock_prepare_export_item',
			$export_item,
			$item
		);
	}
}
