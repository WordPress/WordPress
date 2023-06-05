<?php
/**
 * REST API Products Controller
 *
 * Handles requests to /products/*
 */

namespace Automattic\WooCommerce\Admin\API;

defined( 'ABSPATH' ) || exit;

/**
 * Products controller.
 *
 * @internal
 * @extends WC_REST_Products_Controller
 */
class Products extends \WC_REST_Products_Controller {

	/**
	 * Endpoint namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'wc-analytics';

	/**
	 * Local cache of last order dates by ID.
	 *
	 * @var array
	 */
	protected $last_order_dates = array();

	/**
	 * Adds properties that can be embed via ?_embed=1.
	 *
	 * @return array
	 */
	public function get_item_schema() {
		$schema = parent::get_item_schema();

		$properties_to_embed = array(
			'id',
			'name',
			'slug',
			'permalink',
			'images',
			'description',
			'short_description',
		);

		foreach ( $properties_to_embed as $property ) {
			$schema['properties'][ $property ]['context'][] = 'embed';
		}

		$schema['properties']['last_order_date'] = array(
			'description' => __( "The date the last order for this product was placed, in the site's timezone.", 'woocommerce' ),
			'type'        => 'date-time',
			'context'     => array( 'view', 'edit' ),
			'readonly'    => true,
		);

		return $schema;
	}

	/**
	 * Get the query params for collections.
	 *
	 * @return array
	 */
	public function get_collection_params() {
		$params                 = parent::get_collection_params();
		$params['low_in_stock'] = array(
			'description'       => __( 'Limit result set to products that are low or out of stock. (Deprecated)', 'woocommerce' ),
			'type'              => 'boolean',
			'default'           => false,
			'sanitize_callback' => 'wc_string_to_bool',
		);
		$params['search']       = array(
			'description'       => __( 'Search by similar product name or sku.', 'woocommerce' ),
			'type'              => 'string',
			'validate_callback' => 'rest_validate_request_arg',
		);
		return $params;
	}


	/**
	 * Add product name and sku filtering to the WC API.
	 *
	 * @param WP_REST_Request $request Request data.
	 * @return array
	 */
	protected function prepare_objects_query( $request ) {
		$args = parent::prepare_objects_query( $request );

		if ( ! empty( $request['search'] ) ) {
			$args['search'] = trim( $request['search'] );
			unset( $args['s'] );
		}
		if ( ! empty( $request['low_in_stock'] ) ) {
			$args['low_in_stock'] = $request['low_in_stock'];
			$args['post_type']    = array( 'product', 'product_variation' );
		}

		return $args;
	}

	/**
	 * Get a collection of posts and add the post title filter option to WP_Query.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_Error|WP_REST_Response
	 */
	public function get_items( $request ) {
		add_filter( 'posts_fields', array( __CLASS__, 'add_wp_query_fields' ), 10, 2 );
		add_filter( 'posts_where', array( __CLASS__, 'add_wp_query_filter' ), 10, 2 );
		add_filter( 'posts_join', array( __CLASS__, 'add_wp_query_join' ), 10, 2 );
		add_filter( 'posts_groupby', array( __CLASS__, 'add_wp_query_group_by' ), 10, 2 );
		$response = parent::get_items( $request );
		remove_filter( 'posts_fields', array( __CLASS__, 'add_wp_query_fields' ), 10 );
		remove_filter( 'posts_where', array( __CLASS__, 'add_wp_query_filter' ), 10 );
		remove_filter( 'posts_join', array( __CLASS__, 'add_wp_query_join' ), 10 );
		remove_filter( 'posts_groupby', array( __CLASS__, 'add_wp_query_group_by' ), 10 );

		/**
		 * The low stock query caused performance issues in WooCommerce 5.5.1
		 * due to a) being slow, and b) multiple requests being made to this endpoint
		 * from WC Admin.
		 *
		 * This is a temporary measure to trigger the user’s browser to cache the
		 * endpoint response for 1 minute, limiting the amount of requests overall.
		 *
		 * https://github.com/woocommerce/woocommerce-admin/issues/7358
		 */
		if ( $this->is_low_in_stock_request( $request ) ) {
			$response->header( 'Cache-Control', 'max-age=300' );
		}
		return $response;
	}

	/**
	 * Check whether the request is for products low in stock.
	 *
	 * It matches requests with paramaters:
	 *
	 * low_in_stock = true
	 * page = 1
	 * fields[0] = id
	 *
	 * @param string $request WP REST API request.
	 * @return boolean Whether the request matches.
	 */
	private function is_low_in_stock_request( $request ) {
		if (
			$request->get_param( 'low_in_stock' ) === true &&
			$request->get_param( 'page' ) === 1 &&
			is_array( $request->get_param( '_fields' ) ) &&
			count( $request->get_param( '_fields' ) ) === 1 &&
			in_array( 'id', $request->get_param( '_fields' ), true )
		) {
			return true;
		}

		return false;
	}

	/**
	 * Hang onto last order date since it will get removed by wc_get_product().
	 *
	 * @param stdClass $object_data Single row from query results.
	 * @return WC_Data
	 */
	public function get_object( $object_data ) {
		if ( isset( $object_data->last_order_date ) ) {
			$this->last_order_dates[ $object_data->ID ] = $object_data->last_order_date;
		}
		return parent::get_object( $object_data );
	}

	/**
	 * Add `low_stock_amount` property to product data
	 *
	 * @param WC_Data         $object  Object data.
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response
	 */
	public function prepare_object_for_response( $object, $request ) {
		$data        = parent::prepare_object_for_response( $object, $request );
		$object_data = $object->get_data();
		$product_id  = $object_data['id'];

		if ( $request->get_param( 'low_in_stock' ) ) {
			if ( is_numeric( $object_data['low_stock_amount'] ) ) {
				$data->data['low_stock_amount'] = $object_data['low_stock_amount'];
			}
			if ( isset( $this->last_order_dates[ $product_id ] ) ) {
				$data->data['last_order_date'] = wc_rest_prepare_date_response( $this->last_order_dates[ $product_id ] );
			}
		}
		if ( isset( $data->data['name'] ) ) {
			$data->data['name'] = wp_strip_all_tags( $data->data['name'] );
		}

		return $data;
	}

	/**
	 * Add in conditional select fields to the query.
	 *
	 * @internal
	 * @param string $select Select clause used to select fields from the query.
	 * @param object $wp_query WP_Query object.
	 * @return string
	 */
	public static function add_wp_query_fields( $select, $wp_query ) {
		if ( $wp_query->get( 'low_in_stock' ) ) {
			$fields  = array(
				'low_stock_amount_meta.meta_value AS low_stock_amount',
				'MAX( product_lookup.date_created ) AS last_order_date',
			);
			$select .= ', ' . implode( ', ', $fields );
		}

		return $select;
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

		$search = $wp_query->get( 'search' );
		if ( $search ) {
			$title_like = '%' . $wpdb->esc_like( $search ) . '%';
			$where     .= $wpdb->prepare( " AND ({$wpdb->posts}.post_title LIKE %s", $title_like );  // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$where     .= wc_product_sku_enabled() ? $wpdb->prepare( ' OR wc_product_meta_lookup.sku LIKE %s)', $search ) : ')';
		}

		if ( $wp_query->get( 'low_in_stock' ) ) {
			$low_stock_amount = absint( max( get_option( 'woocommerce_notify_low_stock_amount' ), 1 ) );
			$where           .= "
			AND wc_product_meta_lookup.stock_quantity IS NOT NULL
			AND wc_product_meta_lookup.stock_status IN('instock','outofstock')
			AND (
				(
					low_stock_amount_meta.meta_value > ''
					AND wc_product_meta_lookup.stock_quantity <= CAST(low_stock_amount_meta.meta_value AS SIGNED)
				)
				OR (
					(
						low_stock_amount_meta.meta_value IS NULL OR low_stock_amount_meta.meta_value <= ''
					)
					AND wc_product_meta_lookup.stock_quantity <= {$low_stock_amount}
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

		$search = $wp_query->get( 'search' );
		if ( $search && wc_product_sku_enabled() ) {
			$join = self::append_product_sorting_table_join( $join );
		}

		if ( $wp_query->get( 'low_in_stock' ) ) {
			$product_lookup_table = $wpdb->prefix . 'wc_order_product_lookup';

			$join  = self::append_product_sorting_table_join( $join );
			$join .= " LEFT JOIN {$wpdb->postmeta} AS low_stock_amount_meta ON {$wpdb->posts}.ID = low_stock_amount_meta.post_id AND low_stock_amount_meta.meta_key = '_low_stock_amount' ";
			$join .= " LEFT JOIN {$product_lookup_table} product_lookup ON {$wpdb->posts}.ID = CASE
				WHEN {$wpdb->posts}.post_type = 'product' THEN product_lookup.product_id
				WHEN {$wpdb->posts}.post_type = 'product_variation' THEN product_lookup.variation_id
			END";
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

		$search       = $wp_query->get( 'search' );
		$low_in_stock = $wp_query->get( 'low_in_stock' );
		if ( empty( $groupby ) && ( $search || $low_in_stock ) ) {
			$groupby = $wpdb->posts . '.ID';
		}
		return $groupby;
	}
}
