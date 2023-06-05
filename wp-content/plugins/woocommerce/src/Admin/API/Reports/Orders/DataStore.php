<?php
/**
 * API\Reports\Orders\DataStore class file.
 */

namespace Automattic\WooCommerce\Admin\API\Reports\Orders;

defined( 'ABSPATH' ) || exit;

use Automattic\WooCommerce\Admin\API\Reports\DataStore as ReportsDataStore;
use Automattic\WooCommerce\Admin\API\Reports\DataStoreInterface;
use Automattic\WooCommerce\Admin\API\Reports\SqlQuery;
use Automattic\WooCommerce\Admin\API\Reports\Cache;
use Automattic\WooCommerce\Admin\API\Reports\TimeInterval;


/**
 * API\Reports\Orders\DataStore.
 */
class DataStore extends ReportsDataStore implements DataStoreInterface {

	/**
	 * Dynamically sets the date column name based on configuration
	 */
	public function __construct() {
		$this->date_column_name = get_option( 'woocommerce_date_type', 'date_paid' );
		parent::__construct();
	}

	/**
	 * Table used to get the data.
	 *
	 * @var string
	 */
	protected static $table_name = 'wc_order_stats';

	/**
	 * Cache identifier.
	 *
	 * @var string
	 */
	protected $cache_key = 'orders';

	/**
	 * Mapping columns to data type to return correct response types.
	 *
	 * @var array
	 */
	protected $column_types = array(
		'order_id'         => 'intval',
		'parent_id'        => 'intval',
		'date_created'     => 'strval',
		'date_created_gmt' => 'strval',
		'status'           => 'strval',
		'customer_id'      => 'intval',
		'net_total'        => 'floatval',
		'total_sales'      => 'floatval',
		'num_items_sold'   => 'intval',
		'customer_type'    => 'strval',
	);

	/**
	 * Data store context used to pass to filters.
	 *
	 * @var string
	 */
	protected $context = 'orders';

	/**
	 * Assign report columns once full table name has been assigned.
	 */
	protected function assign_report_columns() {
		$table_name = self::get_db_table_name();
		// Avoid ambigious columns in SQL query.
		$this->report_columns = array(
			'order_id'         => "DISTINCT {$table_name}.order_id",
			'parent_id'        => "{$table_name}.parent_id",
			// Add 'date' field based on date type setting.
			'date'             => "{$table_name}.{$this->date_column_name} AS date",
			'date_created'     => "{$table_name}.date_created",
			'date_created_gmt' => "{$table_name}.date_created_gmt",
			'status'           => "REPLACE({$table_name}.status, 'wc-', '') as status",
			'customer_id'      => "{$table_name}.customer_id",
			'net_total'        => "{$table_name}.net_total",
			'total_sales'      => "{$table_name}.total_sales",
			'num_items_sold'   => "{$table_name}.num_items_sold",
			'customer_type'    => "(CASE WHEN {$table_name}.returning_customer = 0 THEN 'new' ELSE 'returning' END) as customer_type",
		);
	}

	/**
	 * Updates the database query with parameters used for orders report: coupons and products filters.
	 *
	 * @param array $query_args Query arguments supplied by the user.
	 */
	protected function add_sql_query_params( $query_args ) {
		global $wpdb;
		$order_stats_lookup_table   = self::get_db_table_name();
		$order_coupon_lookup_table  = $wpdb->prefix . 'wc_order_coupon_lookup';
		$order_product_lookup_table = $wpdb->prefix . 'wc_order_product_lookup';
		$order_tax_lookup_table     = $wpdb->prefix . 'wc_order_tax_lookup';
		$operator                   = $this->get_match_operator( $query_args );
		$where_subquery             = array();
		$have_joined_products_table = false;

		$this->add_time_period_sql_params( $query_args, $order_stats_lookup_table );
		$this->get_limit_sql_params( $query_args );
		$this->add_order_by_sql_params( $query_args );

		$status_subquery = $this->get_status_subquery( $query_args );
		if ( $status_subquery ) {
			if ( empty( $query_args['status_is'] ) && empty( $query_args['status_is_not'] ) ) {
				$this->subquery->add_sql_clause( 'where', "AND {$status_subquery}" );
			} else {
				$where_subquery[] = $status_subquery;
			}
		}

		$included_orders = $this->get_included_orders( $query_args );
		if ( $included_orders ) {
			$where_subquery[] = "{$order_stats_lookup_table}.order_id IN ({$included_orders})";
		}

		$excluded_orders = $this->get_excluded_orders( $query_args );
		if ( $excluded_orders ) {
			$where_subquery[] = "{$order_stats_lookup_table}.order_id NOT IN ({$excluded_orders})";
		}

		if ( $query_args['customer_type'] ) {
			$returning_customer = 'returning' === $query_args['customer_type'] ? 1 : 0;
			$where_subquery[]   = "{$order_stats_lookup_table}.returning_customer = {$returning_customer}";
		}

		$refund_subquery = $this->get_refund_subquery( $query_args );
		$this->subquery->add_sql_clause( 'from', $refund_subquery['from_clause'] );
		if ( $refund_subquery['where_clause'] ) {
			$where_subquery[] = $refund_subquery['where_clause'];
		}

		$included_coupons = $this->get_included_coupons( $query_args );
		$excluded_coupons = $this->get_excluded_coupons( $query_args );
		if ( $included_coupons || $excluded_coupons ) {
			$this->subquery->add_sql_clause( 'join', "LEFT JOIN {$order_coupon_lookup_table} ON {$order_stats_lookup_table}.order_id = {$order_coupon_lookup_table}.order_id" );
		}
		if ( $included_coupons ) {
			$where_subquery[] = "{$order_coupon_lookup_table}.coupon_id IN ({$included_coupons})";
		}
		if ( $excluded_coupons ) {
			$where_subquery[] = "({$order_coupon_lookup_table}.coupon_id IS NULL OR {$order_coupon_lookup_table}.coupon_id NOT IN ({$excluded_coupons}))";
		}

		$included_products = $this->get_included_products( $query_args );
		$excluded_products = $this->get_excluded_products( $query_args );
		if ( $included_products || $excluded_products ) {
			$this->subquery->add_sql_clause( 'join', "LEFT JOIN {$order_product_lookup_table} product_lookup" );
			$this->subquery->add_sql_clause( 'join', "ON {$order_stats_lookup_table}.order_id = product_lookup.order_id" );
		}
		if ( $included_products ) {
			$this->subquery->add_sql_clause( 'join', "AND product_lookup.product_id IN ({$included_products})" );
			$where_subquery[] = 'product_lookup.order_id IS NOT NULL';
		}
		if ( $excluded_products ) {
			$this->subquery->add_sql_clause( 'join', "AND product_lookup.product_id IN ({$excluded_products})" );
			$where_subquery[] = 'product_lookup.order_id IS NULL';
		}

		$included_variations = $this->get_included_variations( $query_args );
		$excluded_variations = $this->get_excluded_variations( $query_args );
		if ( $included_variations || $excluded_variations ) {
			$this->subquery->add_sql_clause( 'join', "LEFT JOIN {$order_product_lookup_table} variation_lookup" );
			$this->subquery->add_sql_clause( 'join', "ON {$order_stats_lookup_table}.order_id = variation_lookup.order_id" );
		}
		if ( $included_variations ) {
			$this->subquery->add_sql_clause( 'join', "AND variation_lookup.variation_id IN ({$included_variations})" );
			$where_subquery[] = 'variation_lookup.order_id IS NOT NULL';
		}
		if ( $excluded_variations ) {
			$this->subquery->add_sql_clause( 'join', "AND variation_lookup.variation_id IN ({$excluded_variations})" );
			$where_subquery[] = 'variation_lookup.order_id IS NULL';
		}

		$included_tax_rates = ! empty( $query_args['tax_rate_includes'] ) ? implode( ',', array_map( 'esc_sql', $query_args['tax_rate_includes'] ) ) : false;
		$excluded_tax_rates = ! empty( $query_args['tax_rate_excludes'] ) ? implode( ',', array_map( 'esc_sql', $query_args['tax_rate_excludes'] ) ) : false;
		if ( $included_tax_rates || $excluded_tax_rates ) {
			$this->subquery->add_sql_clause( 'join', "LEFT JOIN {$order_tax_lookup_table} ON {$order_stats_lookup_table}.order_id = {$order_tax_lookup_table}.order_id" );
		}
		if ( $included_tax_rates ) {
			$where_subquery[] = "{$order_tax_lookup_table}.tax_rate_id IN ({$included_tax_rates})";
		}
		if ( $excluded_tax_rates ) {
			$where_subquery[] = "{$order_tax_lookup_table}.tax_rate_id NOT IN ({$excluded_tax_rates}) OR {$order_tax_lookup_table}.tax_rate_id IS NULL";
		}

		$attribute_subqueries = $this->get_attribute_subqueries( $query_args );
		if ( $attribute_subqueries['join'] && $attribute_subqueries['where'] ) {
			$this->subquery->add_sql_clause( 'join', "JOIN {$order_product_lookup_table} ON {$order_stats_lookup_table}.order_id = {$order_product_lookup_table}.order_id" );

			// Add JOINs for matching attributes.
			foreach ( $attribute_subqueries['join'] as $attribute_join ) {
				$this->subquery->add_sql_clause( 'join', $attribute_join );
			}
			// Add WHEREs for matching attributes.
			$where_subquery = array_merge( $where_subquery, $attribute_subqueries['where'] );
		}

		if ( 0 < count( $where_subquery ) ) {
			$this->subquery->add_sql_clause( 'where', 'AND (' . implode( " {$operator} ", $where_subquery ) . ')' );
		}
	}

	/**
	 * Returns the report data based on parameters supplied by the user.
	 *
	 * @param array $query_args  Query parameters.
	 * @return stdClass|WP_Error Data.
	 */
	public function get_data( $query_args ) {
		global $wpdb;

		// These defaults are only partially applied when used via REST API, as that has its own defaults.
		$defaults   = array(
			'per_page'          => get_option( 'posts_per_page' ),
			'page'              => 1,
			'order'             => 'DESC',
			'orderby'           => $this->date_column_name,
			'before'            => TimeInterval::default_before(),
			'after'             => TimeInterval::default_after(),
			'fields'            => '*',
			'product_includes'  => array(),
			'product_excludes'  => array(),
			'coupon_includes'   => array(),
			'coupon_excludes'   => array(),
			'tax_rate_includes' => array(),
			'tax_rate_excludes' => array(),
			'customer_type'     => null,
			'status_is'         => array(),
			'extended_info'     => false,
			'refunds'           => null,
			'order_includes'    => array(),
			'order_excludes'    => array(),
		);
		$query_args = wp_parse_args( $query_args, $defaults );
		$this->normalize_timezones( $query_args, $defaults );

		/*
		 * We need to get the cache key here because
		 * parent::update_intervals_sql_params() modifies $query_args.
		 */
		$cache_key = $this->get_cache_key( $query_args );
		$data      = $this->get_cached_data( $cache_key );

		if ( false === $data ) {
			$this->initialize_queries();

			$data = (object) array(
				'data'    => array(),
				'total'   => 0,
				'pages'   => 0,
				'page_no' => 0,
			);

			$selections = $this->selected_columns( $query_args );
			$params     = $this->get_limit_params( $query_args );
			$this->add_sql_query_params( $query_args );
			/* phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared */
			$db_records_count = (int) $wpdb->get_var(
				"SELECT COUNT( DISTINCT tt.order_id ) FROM (
					{$this->subquery->get_query_statement()}
				) AS tt"
			);
			/* phpcs:enable */

			if ( 0 === $params['per_page'] ) {
				$total_pages = 0;
			} else {
				$total_pages = (int) ceil( $db_records_count / $params['per_page'] );
			}
			if ( $query_args['page'] < 1 || $query_args['page'] > $total_pages ) {
				$data = (object) array(
					'data'    => array(),
					'total'   => $db_records_count,
					'pages'   => 0,
					'page_no' => 0,
				);
				return $data;
			}

			$this->subquery->clear_sql_clause( 'select' );
			$this->subquery->add_sql_clause( 'select', $selections );
			$this->subquery->add_sql_clause( 'order_by', $this->get_sql_clause( 'order_by' ) );
			$this->subquery->add_sql_clause( 'limit', $this->get_sql_clause( 'limit' ) );
			/* phpcs:disable WordPress.DB.PreparedSQL.NotPrepared */
			$orders_data = $wpdb->get_results(
				$this->subquery->get_query_statement(),
				ARRAY_A
			);
			/* phpcs:enable */

			if ( null === $orders_data ) {
				return $data;
			}

			if ( $query_args['extended_info'] ) {
				$this->include_extended_info( $orders_data, $query_args );
			}

			$orders_data = array_map( array( $this, 'cast_numbers' ), $orders_data );
			$data        = (object) array(
				'data'    => $orders_data,
				'total'   => $db_records_count,
				'pages'   => $total_pages,
				'page_no' => (int) $query_args['page'],
			);

			$this->set_cached_data( $cache_key, $data );
		}
		return $data;
	}

	/**
	 * Normalizes order_by clause to match to SQL query.
	 *
	 * @param string $order_by Order by option requeste by user.
	 * @return string
	 */
	protected function normalize_order_by( $order_by ) {
		if ( 'date' === $order_by ) {
			return $this->date_column_name;
		}

		return $order_by;
	}

	/**
	 * Enriches the order data.
	 *
	 * @param array $orders_data Orders data.
	 * @param array $query_args  Query parameters.
	 */
	protected function include_extended_info( &$orders_data, $query_args ) {
		$mapped_orders    = $this->map_array_by_key( $orders_data, 'order_id' );
		$related_orders   = $this->get_orders_with_parent_id( $mapped_orders );
		$order_ids        = array_merge( array_keys( $mapped_orders ), array_keys( $related_orders ) );
		$products         = $this->get_products_by_order_ids( $order_ids );
		$coupons          = $this->get_coupons_by_order_ids( array_keys( $mapped_orders ) );
		$customers        = $this->get_customers_by_orders( $orders_data );
		$mapped_customers = $this->map_array_by_key( $customers, 'customer_id' );

		$mapped_data = array();
		foreach ( $products as $product ) {
			if ( ! isset( $mapped_data[ $product['order_id'] ] ) ) {
				$mapped_data[ $product['order_id'] ]['products'] = array();
			}

			$is_variation = '0' !== $product['variation_id'];
			$product_data = array(
				'id'       => $is_variation ? $product['variation_id'] : $product['product_id'],
				'name'     => $product['product_name'],
				'quantity' => $product['product_quantity'],
			);

			if ( $is_variation ) {
				$variation = wc_get_product( $product_data['id'] );
				/**
				 * Used to determine the separator for products and their variations titles.
				 *
				 * @since 4.0.0
				 */
				$separator = apply_filters( 'woocommerce_product_variation_title_attributes_separator', ' - ', $variation );

				if ( false === strpos( $product_data['name'], $separator ) ) {
					$attributes            = wc_get_formatted_variation( $variation, true, false );
					$product_data['name'] .= $separator . $attributes;
				}
			}

			$mapped_data[ $product['order_id'] ]['products'][] = $product_data;

			// If this product's order has another related order, it will be added to our mapped_data.
			if ( isset( $related_orders [ $product['order_id'] ] ) ) {
				$mapped_data[ $related_orders[ $product['order_id'] ]['order_id'] ] ['products'] [] = $product_data;
			}
		}

		foreach ( $coupons as $coupon ) {
			if ( ! isset( $mapped_data[ $coupon['order_id'] ] ) ) {
				$mapped_data[ $product['order_id'] ]['coupons'] = array();
			}

			$mapped_data[ $coupon['order_id'] ]['coupons'][] = array(
				'id'   => $coupon['coupon_id'],
				'code' => wc_format_coupon_code( $coupon['coupon_code'] ),
			);
		}

		foreach ( $orders_data as $key => $order_data ) {
			$defaults                             = array(
				'products' => array(),
				'coupons'  => array(),
				'customer' => array(),
			);
			$orders_data[ $key ]['extended_info'] = isset( $mapped_data[ $order_data['order_id'] ] ) ? array_merge( $defaults, $mapped_data[ $order_data['order_id'] ] ) : $defaults;
			if ( $order_data['customer_id'] && isset( $mapped_customers[ $order_data['customer_id'] ] ) ) {
				$orders_data[ $key ]['extended_info']['customer'] = $mapped_customers[ $order_data['customer_id'] ];
			}
		}
	}

	/**
	 * Returns oreders that have a parent id
	 *
	 * @param array $orders Orders array.
	 * @return array
	 */
	protected function get_orders_with_parent_id( $orders ) {
		$related_orders = array();
		foreach ( $orders as $order ) {
			if ( '0' !== $order['parent_id'] ) {
				$related_orders[ $order['parent_id'] ] = $order;
			}
		}
		return $related_orders;
	}

	/**
	 * Returns the same array index by a given key
	 *
	 * @param array  $array Array to be looped over.
	 * @param string $key Key of values used for new array.
	 * @return array
	 */
	protected function map_array_by_key( $array, $key ) {
		$mapped = array();
		foreach ( $array as $item ) {
			$mapped[ $item[ $key ] ] = $item;
		}
		return $mapped;
	}

	/**
	 * Get product IDs, names, and quantity from order IDs.
	 *
	 * @param array $order_ids Array of order IDs.
	 * @return array
	 */
	protected function get_products_by_order_ids( $order_ids ) {
		global $wpdb;
		$order_product_lookup_table = $wpdb->prefix . 'wc_order_product_lookup';
		$included_order_ids         = implode( ',', $order_ids );

		/* phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared */
		$products = $wpdb->get_results(
			"SELECT
				order_id,
				product_id,
				variation_id,
				post_title as product_name,
				product_qty as product_quantity
			FROM {$wpdb->posts}
			JOIN
				{$order_product_lookup_table}
				ON {$wpdb->posts}.ID = (
					CASE WHEN variation_id > 0
						THEN variation_id
						ELSE product_id
					END
				)
			WHERE
				order_id IN ({$included_order_ids})
			",
			ARRAY_A
		);
		/* phpcs:enable */

		return $products;
	}

	/**
	 * Get customer data from Order data.
	 *
	 * @param array $orders Array of orders data.
	 * @return array
	 */
	protected function get_customers_by_orders( $orders ) {
		global $wpdb;

		$customer_lookup_table = $wpdb->prefix . 'wc_customer_lookup';
		$customer_ids          = array();

		foreach ( $orders as $order ) {
			if ( $order['customer_id'] ) {
				$customer_ids[] = intval( $order['customer_id'] );
			}
		}

		if ( empty( $customer_ids ) ) {
			return array();
		}

		/* phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared */
		$customer_ids = implode( ',', $customer_ids );
		$customers    = $wpdb->get_results(
			"SELECT * FROM {$customer_lookup_table} WHERE customer_id IN ({$customer_ids})",
			ARRAY_A
		);
		/* phpcs:enable */

		return $customers;
	}

	/**
	 * Get coupon information from order IDs.
	 *
	 * @param array $order_ids Array of order IDs.
	 * @return array
	 */
	protected function get_coupons_by_order_ids( $order_ids ) {
		global $wpdb;
		$order_coupon_lookup_table = $wpdb->prefix . 'wc_order_coupon_lookup';
		$included_order_ids        = implode( ',', $order_ids );

		/* phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared */
		$coupons = $wpdb->get_results(
			"SELECT order_id, coupon_id, post_title as coupon_code
				FROM {$wpdb->posts}
				JOIN {$order_coupon_lookup_table} ON {$order_coupon_lookup_table}.coupon_id = {$wpdb->posts}.ID
				WHERE
					order_id IN ({$included_order_ids})
				",
			ARRAY_A
		);
		/* phpcs:enable */

		return $coupons;
	}

	/**
	 * Get all statuses that have been synced.
	 *
	 * @return array Unique order statuses.
	 */
	public static function get_all_statuses() {
		global $wpdb;

		$cache_key = 'orders-all-statuses';
		$statuses  = Cache::get( $cache_key );

		if ( false === $statuses ) {
			/* phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared */
			$table_name = self::get_db_table_name();
			$statuses   = $wpdb->get_col(
				"SELECT DISTINCT status FROM {$table_name}"
			);
			/* phpcs:enable */

			Cache::set( $cache_key, $statuses );
		}

		return $statuses;
	}

	/**
	 * Initialize query objects.
	 */
	protected function initialize_queries() {
		$this->clear_all_clauses();
		$this->subquery = new SqlQuery( $this->context . '_subquery' );
		$this->subquery->add_sql_clause( 'select', self::get_db_table_name() . '.order_id' );
		$this->subquery->add_sql_clause( 'from', self::get_db_table_name() );
	}
}
