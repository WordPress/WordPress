<?php
/**
 * API\Reports\Variations\DataStore class file.
 */

namespace Automattic\WooCommerce\Admin\API\Reports\Variations;

defined( 'ABSPATH' ) || exit;

use Automattic\WooCommerce\Admin\API\Reports\DataStore as ReportsDataStore;
use Automattic\WooCommerce\Admin\API\Reports\DataStoreInterface;
use Automattic\WooCommerce\Admin\API\Reports\TimeInterval;
use Automattic\WooCommerce\Admin\API\Reports\SqlQuery;

/**
 * API\Reports\Variations\DataStore.
 */
class DataStore extends ReportsDataStore implements DataStoreInterface {

	/**
	 * Table used to get the data.
	 *
	 * @var string
	 */
	protected static $table_name = 'wc_order_product_lookup';

	/**
	 * Cache identifier.
	 *
	 * @var string
	 */
	protected $cache_key = 'variations';

	/**
	 * Mapping columns to data type to return correct response types.
	 *
	 * @var array
	 */
	protected $column_types = array(
		'date_start'   => 'strval',
		'date_end'     => 'strval',
		'product_id'   => 'intval',
		'variation_id' => 'intval',
		'items_sold'   => 'intval',
		'net_revenue'  => 'floatval',
		'orders_count' => 'intval',
		'name'         => 'strval',
		'price'        => 'floatval',
		'image'        => 'strval',
		'permalink'    => 'strval',
		'sku'          => 'strval',
	);

	/**
	 * Extended product attributes to include in the data.
	 *
	 * @var array
	 */
	protected $extended_attributes = array(
		'name',
		'price',
		'image',
		'permalink',
		'stock_status',
		'stock_quantity',
		'low_stock_amount',
		'sku',
	);

	/**
	 * Data store context used to pass to filters.
	 *
	 * @var string
	 */
	protected $context = 'variations';

	/**
	 * Assign report columns once full table name has been assigned.
	 */
	protected function assign_report_columns() {
		$table_name           = self::get_db_table_name();
		$this->report_columns = array(
			'product_id'   => 'product_id',
			'variation_id' => 'variation_id',
			'items_sold'   => 'SUM(product_qty) as items_sold',
			'net_revenue'  => 'SUM(product_net_revenue) AS net_revenue',
			'orders_count' => "COUNT(DISTINCT {$table_name}.order_id) as orders_count",
		);
	}

	/**
	 * Fills FROM clause of SQL request based on user supplied parameters.
	 *
	 * @param array  $query_args Parameters supplied by the user.
	 * @param string $arg_name   Target of the JOIN sql param.
	 */
	protected function add_from_sql_params( $query_args, $arg_name ) {
		global $wpdb;

		if ( 'sku' !== $query_args['orderby'] ) {
			return;
		}

		$table_name = self::get_db_table_name();
		$join       = "LEFT JOIN {$wpdb->postmeta} AS postmeta ON {$table_name}.variation_id = postmeta.post_id AND postmeta.meta_key = '_sku'";

		if ( 'inner' === $arg_name ) {
			$this->subquery->add_sql_clause( 'join', $join );
		} else {
			$this->add_sql_clause( 'join', $join );
		}
	}

	/**
	 * Generate a subquery for order_item_id based on the attribute filters.
	 *
	 * @param array $query_args Query arguments supplied by the user.
	 * @return string
	 */
	protected function get_order_item_by_attribute_subquery( $query_args ) {
		$order_product_lookup_table = self::get_db_table_name();
		$attribute_subqueries       = $this->get_attribute_subqueries( $query_args );

		if ( $attribute_subqueries['join'] && $attribute_subqueries['where'] ) {
			// Perform a subquery for DISTINCT order items that match our attribute filters.
			$attr_subquery = new SqlQuery( $this->context . '_attribute_subquery' );
			$attr_subquery->add_sql_clause( 'select', "DISTINCT {$order_product_lookup_table}.order_item_id" );
			$attr_subquery->add_sql_clause( 'from', $order_product_lookup_table );

			if ( $this->should_exclude_simple_products( $query_args ) ) {
				$attr_subquery->add_sql_clause( 'where', "AND {$order_product_lookup_table}.variation_id != 0" );
			}

			foreach ( $attribute_subqueries['join'] as $attribute_join ) {
				$attr_subquery->add_sql_clause( 'join', $attribute_join );
			}

			$operator = $this->get_match_operator( $query_args );
			$attr_subquery->add_sql_clause( 'where', 'AND (' . implode( " {$operator} ", $attribute_subqueries['where'] ) . ')' );

			return "AND {$order_product_lookup_table}.order_item_id IN ({$attr_subquery->get_query_statement()})";
		}

		return false;
	}

	/**
	 * Updates the database query with parameters used for Products report: categories and order status.
	 *
	 * @param array $query_args Query arguments supplied by the user.
	 */
	protected function add_sql_query_params( $query_args ) {
		global $wpdb;
		$order_product_lookup_table = self::get_db_table_name();
		$order_stats_lookup_table   = $wpdb->prefix . 'wc_order_stats';
		$order_item_meta_table      = $wpdb->prefix . 'woocommerce_order_itemmeta';
		$where_subquery             = array();

		$this->add_time_period_sql_params( $query_args, $order_product_lookup_table );
		$this->get_limit_sql_params( $query_args );
		$this->add_order_by_sql_params( $query_args );

		$included_variations = $this->get_included_variations( $query_args );
		if ( $included_variations > 0 ) {
			$this->add_from_sql_params( $query_args, 'outer' );
		} else {
			$this->add_from_sql_params( $query_args, 'inner' );
		}

		$included_products = $this->get_included_products( $query_args );
		if ( $included_products ) {
			$this->subquery->add_sql_clause( 'where', "AND {$order_product_lookup_table}.product_id IN ({$included_products})" );
		}

		$excluded_products = $this->get_excluded_products( $query_args );
		if ( $excluded_products ) {
			$this->subquery->add_sql_clause( 'where', "AND {$order_product_lookup_table}.product_id NOT IN ({$excluded_products})" );
		}

		if ( $included_variations ) {
			$this->subquery->add_sql_clause( 'where', "AND {$order_product_lookup_table}.variation_id IN ({$included_variations})" );
		} elseif ( ! $included_products ) {
			if ( $this->should_exclude_simple_products( $query_args ) ) {
				$this->subquery->add_sql_clause( 'where', "AND {$order_product_lookup_table}.variation_id != 0" );
			}
		}

		$order_status_filter = $this->get_status_subquery( $query_args );
		if ( $order_status_filter ) {
			$this->subquery->add_sql_clause( 'join', "JOIN {$order_stats_lookup_table} ON {$order_product_lookup_table}.order_id = {$order_stats_lookup_table}.order_id" );
			$this->subquery->add_sql_clause( 'where', "AND ( {$order_status_filter} )" );
		}

		$attribute_order_items_subquery = $this->get_order_item_by_attribute_subquery( $query_args );
		if ( $attribute_order_items_subquery ) {
			// JOIN on product lookup if we haven't already.
			if ( ! $order_status_filter ) {
				$this->subquery->add_sql_clause( 'join', "JOIN {$order_product_lookup_table} ON {$order_stats_lookup_table}.order_id = {$order_product_lookup_table}.order_id" );
			}

			// Add subquery for matching attributes to WHERE.
			$this->subquery->add_sql_clause( 'where', $attribute_order_items_subquery );
		}

		if ( 0 < count( $where_subquery ) ) {
			$operator = $this->get_match_operator( $query_args );
			$this->subquery->add_sql_clause( 'where', 'AND (' . implode( " {$operator} ", $where_subquery ) . ')' );
		}
	}

	/**
	 * Maps ordering specified by the user to columns in the database/fields in the data.
	 *
	 * @param string $order_by Sorting criterion.
	 *
	 * @return string
	 */
	protected function normalize_order_by( $order_by ) {
		if ( 'date' === $order_by ) {
			return self::get_db_table_name() . '.date_created';
		}
		if ( 'sku' === $order_by ) {
			return 'meta_value';
		}

		return $order_by;
	}

	/**
	 * Enriches the product data with attributes specified by the extended_attributes.
	 *
	 * @param array $products_data Product data.
	 * @param array $query_args Query parameters.
	 */
	protected function include_extended_info( &$products_data, $query_args ) {
		foreach ( $products_data as $key => $product_data ) {
			$extended_info = new \ArrayObject();
			if ( $query_args['extended_info'] ) {
				$extended_attributes = apply_filters( 'woocommerce_rest_reports_variations_extended_attributes', $this->extended_attributes, $product_data );
				$parent_product      = wc_get_product( $product_data['product_id'] );
				$attributes          = array();

				// Base extended info off the parent variable product if the variation ID is 0.
				// This is caused by simple products with prior sales being converted into variable products.
				// See: https://github.com/woocommerce/woocommerce-admin/issues/2719.
				$variation_id      = (int) $product_data['variation_id'];
				$variation_product = ( 0 === $variation_id ) ? $parent_product : wc_get_product( $variation_id );

				// Fall back to the parent product if the variation can't be found.
				$extended_attributes_product = is_a( $variation_product, 'WC_Product' ) ? $variation_product : $parent_product;
				// If both product and variation is not found, set deleted to true.
				if ( ! $extended_attributes_product ) {
					$extended_info['deleted'] = true;
				}
				foreach ( $extended_attributes as $extended_attribute ) {
					$function = 'get_' . $extended_attribute;
					if ( is_callable( array( $extended_attributes_product, $function ) ) ) {
						$value                                = $extended_attributes_product->{$function}();
						$extended_info[ $extended_attribute ] = $value;
					}
				}

				// If this is a variation, add its attributes.
				// NOTE: We don't fall back to the parent product here because it will include all possible attribute options.
				if (
					0 < $variation_id &&
					is_callable( array( $variation_product, 'get_variation_attributes' ) )
				) {
					$variation_attributes = $variation_product->get_variation_attributes();

					foreach ( $variation_attributes as $attribute_name => $attribute ) {
						$name         = str_replace( 'attribute_', '', $attribute_name );
						$option_term  = get_term_by( 'slug', $attribute, $name );
						$attributes[] = array(
							'id'     => wc_attribute_taxonomy_id_by_name( $name ),
							'name'   => str_replace( 'pa_', '', $name ),
							'option' => $option_term && ! is_wp_error( $option_term ) ? $option_term->name : $attribute,
						);
					}
				}

				$extended_info['attributes'] = $attributes;

				// If there is no set low_stock_amount, use the one in user settings.
				if ( '' === $extended_info['low_stock_amount'] ) {
					$extended_info['low_stock_amount'] = absint( max( get_option( 'woocommerce_notify_low_stock_amount' ), 1 ) );
				}
				$extended_info = $this->cast_numbers( $extended_info );
			}
			$products_data[ $key ]['extended_info'] = $extended_info;
		}
	}

	/**
	 * Returns if simple products should be excluded from the report.
	 *
	 * @internal
	 *
	 * @param array $query_args Query parameters.
	 *
	 * @return boolean
	 */
	protected function should_exclude_simple_products( array $query_args ) {
		return apply_filters( 'experimental_woocommerce_analytics_variations_should_exclude_simple_products', true, $query_args );
	}

	/**
	 * Fill missing extended_info.name for the deleted products.
	 *
	 * @param array $products Product data.
	 */
	protected function fill_deleted_product_name( array &$products ) {
		global $wpdb;
		$product_variation_ids = [];
		// Find products with missing extended_info.name.
		foreach ( $products as $key => $product ) {
			if ( ! isset( $product['extended_info']['name'] ) ) {
				$product_variation_ids[ $key ] = [
					'product_id'   => $product['product_id'],
					'variation_id' => $product['variation_id'],
				];
			}
		}

		if ( ! count( $product_variation_ids ) ) {
			return;
		}

		$where_clauses = implode(
			' or ',
			array_map(
				function( $ids ) {
					return "(
						product_lookup.product_id = {$ids['product_id']}
						and
						product_lookup.variation_id = {$ids['variation_id']}
                    )";
				},
				$product_variation_ids
			)
		);

		$query = "
			select
				product_lookup.product_id,
				product_lookup.variation_id,
				order_items.order_item_name
			from
				{$wpdb->prefix}wc_order_product_lookup as product_lookup
				left join {$wpdb->prefix}woocommerce_order_items as order_items
				on product_lookup.order_item_id = order_items.order_item_id
			where
				{$where_clauses}
			group by
				product_lookup.product_id,
				product_lookup.variation_id,
				order_items.order_item_name
		";

		// phpcs:ignore
		$results = $wpdb->get_results( $query );
		$index   = [];
		foreach ( $results as $result ) {
			$index[ $result->product_id . '_' . $result->variation_id ] = $result->order_item_name;
		}

		foreach ( $product_variation_ids as $product_key => $ids ) {
			$product   = $products[ $product_key ];
			$index_key = $product['product_id'] . '_' . $product['variation_id'];
			if ( isset( $index[ $index_key ] ) ) {
				$products[ $product_key ]['extended_info']['name'] = $index[ $index_key ];
			}
		}
	}

	/**
	 * Returns the report data based on parameters supplied by the user.
	 *
	 * @param array $query_args Query parameters.
	 *
	 * @return stdClass|WP_Error Data.
	 */
	public function get_data( $query_args ) {
		global $wpdb;

		$table_name = self::get_db_table_name();

		// These defaults are only partially applied when used via REST API, as that has its own defaults.
		$defaults   = array(
			'per_page'           => get_option( 'posts_per_page' ),
			'page'               => 1,
			'order'              => 'DESC',
			'orderby'            => 'date',
			'before'             => TimeInterval::default_before(),
			'after'              => TimeInterval::default_after(),
			'fields'             => '*',
			'product_includes'   => array(),
			'variation_includes' => array(),
			'extended_info'      => false,
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

			$selections          = $this->selected_columns( $query_args );
			$included_variations =
				( isset( $query_args['variation_includes'] ) && is_array( $query_args['variation_includes'] ) )
					? $query_args['variation_includes']
					: array();
			$params              = $this->get_limit_params( $query_args );
			$this->add_sql_query_params( $query_args );

			if ( count( $included_variations ) > 0 ) {
				$total_results = count( $included_variations );
				$total_pages   = (int) ceil( $total_results / $params['per_page'] );

				$this->subquery->clear_sql_clause( 'select' );
				$this->subquery->add_sql_clause( 'select', $selections );

				if ( 'date' === $query_args['orderby'] ) {
					$this->subquery->add_sql_clause( 'select', ", {$table_name}.date_created" );
				}

				$fields          = $this->get_fields( $query_args );
				$join_selections = $this->format_join_selections( $fields, array( 'variation_id' ) );
				$ids_table       = $this->get_ids_table( $included_variations, 'variation_id' );

				$this->add_sql_clause( 'select', $join_selections );
				$this->add_sql_clause( 'from', '(' );
				$this->add_sql_clause( 'from', $this->subquery->get_query_statement() );
				$this->add_sql_clause( 'from', ") AS {$table_name}" );
				$this->add_sql_clause(
					'right_join',
					"RIGHT JOIN ( {$ids_table} ) AS default_results
					ON default_results.variation_id = {$table_name}.variation_id"
				);

				$variations_query = $this->get_query_statement();
			} else {

				$this->subquery->clear_sql_clause( 'select' );
				$this->subquery->add_sql_clause( 'select', $selections );

				/**
				 * Experimental: Filter the Variations SQL query allowing extensions to add additional SQL clauses.
				 *
				 * @since 7.4.0
				 * @param array $query_args Query parameters.
				 * @param SqlQuery $subquery Variations query class.
				 */
				apply_filters( 'experimental_woocommerce_analytics_variations_additional_clauses', $query_args, $this->subquery );

				/* phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared */
				$db_records_count = (int) $wpdb->get_var(
					"SELECT COUNT(*) FROM (
						{$this->subquery->get_query_statement()}
					) AS tt"
				);
				/* phpcs:enable */

				$total_results = $db_records_count;
				$total_pages   = (int) ceil( $db_records_count / $params['per_page'] );

				if ( $query_args['page'] < 1 || $query_args['page'] > $total_pages ) {
					return $data;
				}

				$this->subquery->add_sql_clause( 'order_by', $this->get_sql_clause( 'order_by' ) );
				$this->subquery->add_sql_clause( 'limit', $this->get_sql_clause( 'limit' ) );
				$variations_query = $this->subquery->get_query_statement();
			}

			/* phpcs:disable WordPress.DB.PreparedSQL.NotPrepared */
			$product_data = $wpdb->get_results(
				$variations_query,
				ARRAY_A
			);
			/* phpcs:enable */

			if ( null === $product_data ) {
				return $data;
			}

			$this->include_extended_info( $product_data, $query_args );

			if ( $query_args['extended_info'] ) {
				$this->fill_deleted_product_name( $product_data );
			}

			$product_data = array_map( array( $this, 'cast_numbers' ), $product_data );
			$data         = (object) array(
				'data'    => $product_data,
				'total'   => $total_results,
				'pages'   => $total_pages,
				'page_no' => (int) $query_args['page'],
			);

			$this->set_cached_data( $cache_key, $data );
		}

		return $data;
	}

	/**
	 * Initialize query objects.
	 */
	protected function initialize_queries() {
		$this->clear_all_clauses();
		$this->subquery = new SqlQuery( $this->context . '_subquery' );
		$this->subquery->add_sql_clause( 'select', 'product_id' );
		$this->subquery->add_sql_clause( 'from', self::get_db_table_name() );
		$this->subquery->add_sql_clause( 'group_by', 'product_id, variation_id' );
	}
}
