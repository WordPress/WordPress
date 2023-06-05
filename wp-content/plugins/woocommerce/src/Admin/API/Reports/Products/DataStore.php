<?php
/**
 * API\Reports\Products\DataStore class file.
 */

namespace Automattic\WooCommerce\Admin\API\Reports\Products;

defined( 'ABSPATH' ) || exit;

use Automattic\WooCommerce\Admin\API\Reports\DataStore as ReportsDataStore;
use Automattic\WooCommerce\Admin\API\Reports\DataStoreInterface;
use Automattic\WooCommerce\Admin\API\Reports\TimeInterval;
use Automattic\WooCommerce\Admin\API\Reports\SqlQuery;
use Automattic\WooCommerce\Admin\API\Reports\Cache as ReportsCache;

/**
 * API\Reports\Products\DataStore.
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
	protected $cache_key = 'products';

	/**
	 * Mapping columns to data type to return correct response types.
	 *
	 * @var array
	 */
	protected $column_types = array(
		'date_start'       => 'strval',
		'date_end'         => 'strval',
		'product_id'       => 'intval',
		'items_sold'       => 'intval',
		'net_revenue'      => 'floatval',
		'orders_count'     => 'intval',
		// Extended attributes.
		'name'             => 'strval',
		'price'            => 'floatval',
		'image'            => 'strval',
		'permalink'        => 'strval',
		'stock_status'     => 'strval',
		'stock_quantity'   => 'intval',
		'low_stock_amount' => 'intval',
		'category_ids'     => 'array_values',
		'variations'       => 'array_values',
		'sku'              => 'strval',
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
		'manage_stock',
		'low_stock_amount',
		'category_ids',
		'variations',
		'sku',
	);

	/**
	 * Data store context used to pass to filters.
	 *
	 * @var string
	 */
	protected $context = 'products';

	/**
	 * Assign report columns once full table name has been assigned.
	 */
	protected function assign_report_columns() {
		$table_name           = self::get_db_table_name();
		$this->report_columns = array(
			'product_id'   => 'product_id',
			'items_sold'   => 'SUM(product_qty) as items_sold',
			'net_revenue'  => 'SUM(product_net_revenue) AS net_revenue',
			'orders_count' => "COUNT( DISTINCT ( CASE WHEN product_gross_revenue >= 0 THEN {$table_name}.order_id END ) ) as orders_count",
		);
	}

	/**
	 * Set up all the hooks for maintaining and populating table data.
	 */
	public static function init() {
		add_action( 'woocommerce_analytics_delete_order_stats', array( __CLASS__, 'sync_on_order_delete' ), 10 );
	}

	/**
	 * Fills FROM clause of SQL request based on user supplied parameters.
	 *
	 * @param array  $query_args Parameters supplied by the user.
	 * @param string $arg_name   Target of the JOIN sql param.
	 * @param string $id_cell    ID cell identifier, like `table_name.id_column_name`.
	 */
	protected function add_from_sql_params( $query_args, $arg_name, $id_cell ) {
		global $wpdb;

		$type = 'join';
		// Order by product name requires extra JOIN.
		switch ( $query_args['orderby'] ) {
			case 'product_name':
				$join = " JOIN {$wpdb->posts} AS _products ON {$id_cell} = _products.ID";
				break;
			case 'sku':
				$join = " LEFT JOIN {$wpdb->postmeta} AS postmeta ON {$id_cell} = postmeta.post_id AND postmeta.meta_key = '_sku'";
				break;
			case 'variations':
				$type = 'left_join';
				$join = "LEFT JOIN ( SELECT post_parent, COUNT(*) AS variations FROM {$wpdb->posts} WHERE post_type = 'product_variation' GROUP BY post_parent ) AS _variations ON {$id_cell} = _variations.post_parent";
				break;
			default:
				$join = '';
				break;
		}
		if ( $join ) {
			if ( 'inner' === $arg_name ) {
				$this->subquery->add_sql_clause( $type, $join );
			} else {
				$this->add_sql_clause( $type, $join );
			}
		}
	}

	/**
	 * Updates the database query with parameters used for Products report: categories and order status.
	 *
	 * @param array $query_args Query arguments supplied by the user.
	 */
	protected function add_sql_query_params( $query_args ) {
		global $wpdb;
		$order_product_lookup_table = self::get_db_table_name();

		$this->add_time_period_sql_params( $query_args, $order_product_lookup_table );
		$this->get_limit_sql_params( $query_args );
		$this->add_order_by_sql_params( $query_args );

		$included_products = $this->get_included_products( $query_args );
		if ( $included_products ) {
			$this->add_from_sql_params( $query_args, 'outer', 'default_results.product_id' );
			$this->subquery->add_sql_clause( 'where', "AND {$order_product_lookup_table}.product_id IN ({$included_products})" );
		} else {
			$this->add_from_sql_params( $query_args, 'inner', "{$order_product_lookup_table}.product_id" );
		}

		$included_variations = $this->get_included_variations( $query_args );
		if ( $included_variations ) {
			$this->subquery->add_sql_clause( 'where', "AND {$order_product_lookup_table}.variation_id IN ({$included_variations})" );
		}

		$order_status_filter = $this->get_status_subquery( $query_args );
		if ( $order_status_filter ) {
			$this->subquery->add_sql_clause( 'join', "JOIN {$wpdb->prefix}wc_order_stats ON {$order_product_lookup_table}.order_id = {$wpdb->prefix}wc_order_stats.order_id" );
			$this->subquery->add_sql_clause( 'where', "AND ( {$order_status_filter} )" );
		}
	}

	/**
	 * Maps ordering specified by the user to columns in the database/fields in the data.
	 *
	 * @param string $order_by Sorting criterion.
	 * @return string
	 */
	protected function normalize_order_by( $order_by ) {
		if ( 'date' === $order_by ) {
			return self::get_db_table_name() . '.date_created';
		}
		if ( 'product_name' === $order_by ) {
			return 'post_title';
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
	 * @param array $query_args  Query parameters.
	 */
	protected function include_extended_info( &$products_data, $query_args ) {
		global $wpdb;
		$product_names = array();

		foreach ( $products_data as $key => $product_data ) {
			$extended_info = new \ArrayObject();
			if ( $query_args['extended_info'] ) {
				$product_id = $product_data['product_id'];
				$product    = wc_get_product( $product_id );
				// Product was deleted.
				if ( ! $product ) {
					if ( ! isset( $product_names[ $product_id ] ) ) {
						$product_names[ $product_id ] = $wpdb->get_var(
							$wpdb->prepare(
								"SELECT i.order_item_name
								FROM {$wpdb->prefix}woocommerce_order_items i, {$wpdb->prefix}woocommerce_order_itemmeta m
								WHERE i.order_item_id = m.order_item_id
								AND m.meta_key = '_product_id'
								AND m.meta_value = %s
								ORDER BY i.order_item_id DESC
								LIMIT 1",
								$product_id
							)
						);
					}

					/* translators: %s is product name */
					$products_data[ $key ]['extended_info']['name'] = $product_names[ $product_id ] ? sprintf( __( '%s (Deleted)', 'woocommerce' ), $product_names[ $product_id ] ) : __( '(Deleted)', 'woocommerce' );
					continue;
				}

				$extended_attributes = apply_filters( 'woocommerce_rest_reports_products_extended_attributes', $this->extended_attributes, $product_data );
				foreach ( $extended_attributes as $extended_attribute ) {
					if ( 'variations' === $extended_attribute ) {
						if ( ! $product->is_type( 'variable' ) ) {
							continue;
						}
						$function = 'get_children';
					} else {
						$function = 'get_' . $extended_attribute;
					}
					if ( is_callable( array( $product, $function ) ) ) {
						$value                                = $product->{$function}();
						$extended_info[ $extended_attribute ] = $value;
					}
				}
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
	 * Returns the report data based on parameters supplied by the user.
	 *
	 * @param array $query_args  Query parameters.
	 * @return stdClass|WP_Error Data.
	 */
	public function get_data( $query_args ) {
		global $wpdb;

		$table_name = self::get_db_table_name();

		// These defaults are only partially applied when used via REST API, as that has its own defaults.
		$defaults   = array(
			'per_page'          => get_option( 'posts_per_page' ),
			'page'              => 1,
			'order'             => 'DESC',
			'orderby'           => 'date',
			'before'            => TimeInterval::default_before(),
			'after'             => TimeInterval::default_after(),
			'fields'            => '*',
			'category_includes' => array(),
			'product_includes'  => array(),
			'extended_info'     => false,
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

			$selections        = $this->selected_columns( $query_args );
			$included_products = $this->get_included_products_array( $query_args );
			$params            = $this->get_limit_params( $query_args );
			$this->add_sql_query_params( $query_args );

			if ( count( $included_products ) > 0 ) {
				$filtered_products = array_diff( $included_products, array( '-1' ) );
				$total_results     = count( $filtered_products );
				$total_pages       = (int) ceil( $total_results / $params['per_page'] );

				if ( 'date' === $query_args['orderby'] ) {
					$selections .= ", {$table_name}.date_created";
				}

				$fields          = $this->get_fields( $query_args );
				$join_selections = $this->format_join_selections( $fields, array( 'product_id' ) );
				$ids_table       = $this->get_ids_table( $included_products, 'product_id' );

				$this->subquery->clear_sql_clause( 'select' );
				$this->subquery->add_sql_clause( 'select', $selections );
				$this->add_sql_clause( 'select', $join_selections );
				$this->add_sql_clause( 'from', '(' );
				$this->add_sql_clause( 'from', $this->subquery->get_query_statement() );
				$this->add_sql_clause( 'from', ") AS {$table_name}" );
				$this->add_sql_clause(
					'right_join',
					"RIGHT JOIN ( {$ids_table} ) AS default_results
					ON default_results.product_id = {$table_name}.product_id"
				);
				$this->add_sql_clause( 'where', 'AND default_results.product_id != -1' );

				$products_query = $this->get_query_statement();
			} else {
				$count_query      = "SELECT COUNT(*) FROM (
						{$this->subquery->get_query_statement()}
					) AS tt";
				$db_records_count = (int) $wpdb->get_var(
					$count_query // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
				);

				$total_results = $db_records_count;
				$total_pages   = (int) ceil( $db_records_count / $params['per_page'] );

				if ( ( $query_args['page'] < 1 || $query_args['page'] > $total_pages ) ) {
					return $data;
				}

				$this->subquery->clear_sql_clause( 'select' );
				$this->subquery->add_sql_clause( 'select', $selections );
				$this->subquery->add_sql_clause( 'order_by', $this->get_sql_clause( 'order_by' ) );
				$this->subquery->add_sql_clause( 'limit', $this->get_sql_clause( 'limit' ) );
				$products_query = $this->subquery->get_query_statement();
			}

			$product_data = $wpdb->get_results(
				$products_query, // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
				ARRAY_A
			);

			if ( null === $product_data ) {
				return $data;
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

		$this->include_extended_info( $data->data, $query_args );

		return $data;
	}

	/**
	 * Create or update an entry in the wc_admin_order_product_lookup table for an order.
	 *
	 * @since 3.5.0
	 * @param int $order_id Order ID.
	 * @return int|bool Returns -1 if order won't be processed, or a boolean indicating processing success.
	 */
	public static function sync_order_products( $order_id ) {
		global $wpdb;

		$order = wc_get_order( $order_id );
		if ( ! $order ) {
			return -1;
		}

		$table_name     = self::get_db_table_name();
		$existing_items = $wpdb->get_col(
			$wpdb->prepare(
				// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				"SELECT order_item_id FROM {$table_name} WHERE order_id = %d",
				$order_id
			)
		);
		$existing_items = array_flip( $existing_items );
		$order_items    = $order->get_items();
		$num_updated    = 0;
		$decimals       = wc_get_price_decimals();
		$round_tax      = 'no' === get_option( 'woocommerce_tax_round_at_subtotal' );

		foreach ( $order_items as $order_item ) {
			$order_item_id = $order_item->get_id();
			unset( $existing_items[ $order_item_id ] );
			$product_qty         = $order_item->get_quantity( 'edit' );
			$shipping_amount     = $order->get_item_shipping_amount( $order_item );
			$shipping_tax_amount = $order->get_item_shipping_tax_amount( $order_item );
			$coupon_amount       = $order->get_item_coupon_amount( $order_item );

			// Skip line items without changes to product quantity.
			if ( ! $product_qty ) {
				$num_updated++;
				continue;
			}

			// Tax amount.
			$tax_amount  = 0;
			$order_taxes = $order->get_taxes();
			$tax_data    = $order_item->get_taxes();
			foreach ( $order_taxes as $tax_item ) {
				$tax_item_id = $tax_item->get_rate_id();
				$tax_amount += isset( $tax_data['total'][ $tax_item_id ] ) ? (float) $tax_data['total'][ $tax_item_id ] : 0;
			}

			$net_revenue = round( $order_item->get_total( 'edit' ), $decimals );
			if ( $round_tax ) {
				$tax_amount = round( $tax_amount, $decimals );
			}

			$result = $wpdb->replace(
				self::get_db_table_name(),
				array(
					'order_item_id'         => $order_item_id,
					'order_id'              => $order->get_id(),
					'product_id'            => wc_get_order_item_meta( $order_item_id, '_product_id' ),
					'variation_id'          => wc_get_order_item_meta( $order_item_id, '_variation_id' ),
					'customer_id'           => $order->get_report_customer_id(),
					'product_qty'           => $product_qty,
					'product_net_revenue'   => $net_revenue,
					'date_created'          => $order->get_date_created( 'edit' )->date( TimeInterval::$sql_datetime_format ),
					'coupon_amount'         => $coupon_amount,
					'tax_amount'            => $tax_amount,
					'shipping_amount'       => $shipping_amount,
					'shipping_tax_amount'   => $shipping_tax_amount,
					// @todo Can this be incorrect if modified by filters?
					'product_gross_revenue' => $net_revenue + $tax_amount + $shipping_amount + $shipping_tax_amount,
				),
				array(
					'%d', // order_item_id.
					'%d', // order_id.
					'%d', // product_id.
					'%d', // variation_id.
					'%d', // customer_id.
					'%d', // product_qty.
					'%f', // product_net_revenue.
					'%s', // date_created.
					'%f', // coupon_amount.
					'%f', // tax_amount.
					'%f', // shipping_amount.
					'%f', // shipping_tax_amount.
					'%f', // product_gross_revenue.
				)
			); // WPCS: cache ok, DB call ok, unprepared SQL ok.

			/**
			 * Fires when product's reports are updated.
			 *
			 * @param int $order_item_id Order Item ID.
			 * @param int $order_id      Order ID.
			 */
			do_action( 'woocommerce_analytics_update_product', $order_item_id, $order->get_id() );

			// Sum the rows affected. Using REPLACE can affect 2 rows if the row already exists.
			$num_updated += 2 === intval( $result ) ? 1 : intval( $result );
		}

		if ( ! empty( $existing_items ) ) {
			$existing_items = array_flip( $existing_items );
			$format         = array_fill( 0, count( $existing_items ), '%d' );
			$format         = implode( ',', $format );
			array_unshift( $existing_items, $order_id );
			$wpdb->query(
				$wpdb->prepare(
					// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
					"DELETE FROM {$table_name} WHERE order_id = %d AND order_item_id in ({$format})",
					$existing_items
				)
			);
		}

		return ( count( $order_items ) === $num_updated );
	}

	/**
	 * Clean products data when an order is deleted.
	 *
	 * @param int $order_id Order ID.
	 */
	public static function sync_on_order_delete( $order_id ) {
		global $wpdb;

		$wpdb->delete( self::get_db_table_name(), array( 'order_id' => $order_id ) );

		/**
		 * Fires when product's reports are removed from database.
		 *
		 * @param int $product_id Product ID.
		 * @param int $order_id   Order ID.
		 */
		do_action( 'woocommerce_analytics_delete_product', 0, $order_id );

		ReportsCache::invalidate();
	}

	/**
	 * Initialize query objects.
	 */
	protected function initialize_queries() {
		$this->clear_all_clauses();
		$this->subquery = new SqlQuery( $this->context . '_subquery' );
		$this->subquery->add_sql_clause( 'select', 'product_id' );
		$this->subquery->add_sql_clause( 'from', self::get_db_table_name() );
		$this->subquery->add_sql_clause( 'group_by', 'product_id' );
	}
}
