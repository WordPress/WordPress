<?php
/**
 * API\Reports\Coupons\DataStore class file.
 */

namespace Automattic\WooCommerce\Admin\API\Reports\Coupons;

defined( 'ABSPATH' ) || exit;

use Automattic\WooCommerce\Admin\API\Reports\DataStore as ReportsDataStore;
use Automattic\WooCommerce\Admin\API\Reports\DataStoreInterface;
use Automattic\WooCommerce\Admin\API\Reports\TimeInterval;
use Automattic\WooCommerce\Admin\API\Reports\SqlQuery;
use Automattic\WooCommerce\Admin\API\Reports\Cache as ReportsCache;

/**
 * API\Reports\Coupons\DataStore.
 */
class DataStore extends ReportsDataStore implements DataStoreInterface {

	/**
	 * Table used to get the data.
	 *
	 * @var string
	 */
	protected static $table_name = 'wc_order_coupon_lookup';

	/**
	 * Cache identifier.
	 *
	 * @var string
	 */
	protected $cache_key = 'coupons';

	/**
	 * Mapping columns to data type to return correct response types.
	 *
	 * @var array
	 */
	protected $column_types = array(
		'coupon_id'    => 'intval',
		'amount'       => 'floatval',
		'orders_count' => 'intval',
	);

	/**
	 * Data store context used to pass to filters.
	 *
	 * @var string
	 */
	protected $context = 'coupons';

	/**
	 * Assign report columns once full table name has been assigned.
	 */
	protected function assign_report_columns() {
		$table_name           = self::get_db_table_name();
		$this->report_columns = array(
			'coupon_id'    => 'coupon_id',
			'amount'       => 'SUM(discount_amount) as amount',
			'orders_count' => "COUNT(DISTINCT {$table_name}.order_id) as orders_count",
		);
	}

	/**
	 * Set up all the hooks for maintaining and populating table data.
	 */
	public static function init() {
		add_action( 'woocommerce_analytics_delete_order_stats', array( __CLASS__, 'sync_on_order_delete' ), 5 );
	}

	/**
	 * Returns an array of ids of included coupons, based on query arguments from the user.
	 *
	 * @param array $query_args Parameters supplied by the user.
	 * @return array
	 */
	protected function get_included_coupons_array( $query_args ) {
		if ( isset( $query_args['coupons'] ) && is_array( $query_args['coupons'] ) && count( $query_args['coupons'] ) > 0 ) {
			return $query_args['coupons'];
		}
		return array();
	}

	/**
	 * Updates the database query with parameters used for Products report: categories and order status.
	 *
	 * @param array $query_args Query arguments supplied by the user.
	 */
	protected function add_sql_query_params( $query_args ) {
		global $wpdb;
		$order_coupon_lookup_table = self::get_db_table_name();

		$this->add_time_period_sql_params( $query_args, $order_coupon_lookup_table );
		$this->get_limit_sql_params( $query_args );

		$included_coupons = $this->get_included_coupons( $query_args, 'coupons' );
		if ( $included_coupons ) {
			$this->subquery->add_sql_clause( 'where', "AND {$order_coupon_lookup_table}.coupon_id IN ({$included_coupons})" );

			$this->add_order_by_params( $query_args, 'outer', 'default_results.coupon_id' );
		} else {
			$this->add_order_by_params( $query_args, 'inner', "{$order_coupon_lookup_table}.coupon_id" );
		}

		$this->add_order_status_clause( $query_args, $order_coupon_lookup_table, $this->subquery );
	}

	/**
	 * Fills ORDER BY clause of SQL request based on user supplied parameters.
	 *
	 * @param array  $query_args Parameters supplied by the user.
	 * @param string $from_arg   Target of the JOIN sql param.
	 * @param string $id_cell    ID cell identifier, like `table_name.id_column_name`.
	 */
	protected function add_order_by_params( $query_args, $from_arg, $id_cell ) {
		global $wpdb;

		// Sanitize input: guarantee that the id cell in the join is quoted with backticks.
		$id_cell_segments   = explode( '.', str_replace( '`', '', $id_cell ) );
		$id_cell_identifier = '`' . implode( '`.`', $id_cell_segments ) . '`';

		$lookup_table    = self::get_db_table_name();
		$order_by_clause = $this->add_order_by_clause( $query_args, $this );
		$join            = "JOIN {$wpdb->posts} AS _coupons ON {$id_cell_identifier} = _coupons.ID";
		$this->add_orderby_order_clause( $query_args, $this );

		if ( 'inner' === $from_arg ) {
			$this->subquery->clear_sql_clause( 'join' );
			if ( false !== strpos( $order_by_clause, '_coupons' ) ) {
				$this->subquery->add_sql_clause( 'join', $join );
			}
		} else {
			$this->clear_sql_clause( 'join' );
			if ( false !== strpos( $order_by_clause, '_coupons' ) ) {
				$this->add_sql_clause( 'join', $join );
			}
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
			return 'time_interval';
		}
		if ( 'code' === $order_by ) {
			return '_coupons.post_title';
		}
		return $order_by;
	}

	/**
	 * Enriches the coupon data with extra attributes.
	 *
	 * @param array $coupon_data Coupon data.
	 * @param array $query_args Query parameters.
	 */
	protected function include_extended_info( &$coupon_data, $query_args ) {
		foreach ( $coupon_data as $idx => $coupon_datum ) {
			$extended_info = new \ArrayObject();
			if ( $query_args['extended_info'] ) {
				$coupon_id = $coupon_datum['coupon_id'];
				$coupon    = new \WC_Coupon( $coupon_id );

				if ( 0 === $coupon->get_id() ) {
					// Deleted or otherwise invalid coupon.
					$extended_info = array(
						'code'             => __( '(Deleted)', 'woocommerce' ),
						'date_created'     => '',
						'date_created_gmt' => '',
						'date_expires'     => '',
						'date_expires_gmt' => '',
						'discount_type'    => __( 'N/A', 'woocommerce' ),
					);
				} else {
					$gmt_timzone = new \DateTimeZone( 'UTC' );

					$date_expires = $coupon->get_date_expires();
					if ( is_a( $date_expires, 'DateTime' ) ) {
						$date_expires     = $date_expires->format( TimeInterval::$iso_datetime_format );
						$date_expires_gmt = new \DateTime( $date_expires );
						$date_expires_gmt->setTimezone( $gmt_timzone );
						$date_expires_gmt = $date_expires_gmt->format( TimeInterval::$iso_datetime_format );
					} else {
						$date_expires     = '';
						$date_expires_gmt = '';
					}

					$date_created = $coupon->get_date_created();
					if ( is_a( $date_created, 'DateTime' ) ) {
						$date_created     = $date_created->format( TimeInterval::$iso_datetime_format );
						$date_created_gmt = new \DateTime( $date_created );
						$date_created_gmt->setTimezone( $gmt_timzone );
						$date_created_gmt = $date_created_gmt->format( TimeInterval::$iso_datetime_format );
					} else {
						$date_created     = '';
						$date_created_gmt = '';
					}

					$extended_info = array(
						'code'             => $coupon->get_code(),
						'date_created'     => $date_created,
						'date_created_gmt' => $date_created_gmt,
						'date_expires'     => $date_expires,
						'date_expires_gmt' => $date_expires_gmt,
						'discount_type'    => $coupon->get_discount_type(),
					);
				}
			}
			$coupon_data[ $idx ]['extended_info'] = $extended_info;
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
			'per_page'      => get_option( 'posts_per_page' ),
			'page'          => 1,
			'order'         => 'DESC',
			'orderby'       => 'coupon_id',
			'before'        => TimeInterval::default_before(),
			'after'         => TimeInterval::default_after(),
			'fields'        => '*',
			'coupons'       => array(),
			'extended_info' => false,
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

			$selections       = $this->selected_columns( $query_args );
			$included_coupons = $this->get_included_coupons_array( $query_args );
			$limit_params     = $this->get_limit_params( $query_args );
			$this->subquery->add_sql_clause( 'select', $selections );
			$this->add_sql_query_params( $query_args );

			if ( count( $included_coupons ) > 0 ) {
				$total_results = count( $included_coupons );
				$total_pages   = (int) ceil( $total_results / $limit_params['per_page'] );

				$fields    = $this->get_fields( $query_args );
				$ids_table = $this->get_ids_table( $included_coupons, 'coupon_id' );

				$this->add_sql_clause( 'select', $this->format_join_selections( $fields, array( 'coupon_id' ) ) );
				$this->add_sql_clause( 'from', '(' );
				$this->add_sql_clause( 'from', $this->subquery->get_query_statement() );
				$this->add_sql_clause( 'from', ") AS {$table_name}" );
				$this->add_sql_clause(
					'right_join',
					"RIGHT JOIN ( {$ids_table} ) AS default_results
					ON default_results.coupon_id = {$table_name}.coupon_id"
				);

				$coupons_query = $this->get_query_statement();
			} else {
				$this->subquery->add_sql_clause( 'order_by', $this->get_sql_clause( 'order_by' ) );
				$this->subquery->add_sql_clause( 'limit', $this->get_sql_clause( 'limit' ) );
				$coupons_query = $this->subquery->get_query_statement();

				$this->subquery->clear_sql_clause( array( 'select', 'order_by', 'limit' ) );
				$this->subquery->add_sql_clause( 'select', 'coupon_id' );
				$coupon_subquery = "SELECT COUNT(*) FROM (
					{$this->subquery->get_query_statement()}
				) AS tt";

				$db_records_count = (int) $wpdb->get_var(
					$coupon_subquery // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
				);

				$total_results = $db_records_count;
				$total_pages   = (int) ceil( $db_records_count / $limit_params['per_page'] );
				if ( $query_args['page'] < 1 || $query_args['page'] > $total_pages ) {
					return $data;
				}
			}

			$coupon_data = $wpdb->get_results(
				$coupons_query, // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
				ARRAY_A
			);
			if ( null === $coupon_data ) {
				return $data;
			}

			$this->include_extended_info( $coupon_data, $query_args );

			$coupon_data = array_map( array( $this, 'cast_numbers' ), $coupon_data );
			$data        = (object) array(
				'data'    => $coupon_data,
				'total'   => $total_results,
				'pages'   => $total_pages,
				'page_no' => (int) $query_args['page'],
			);

			$this->set_cached_data( $cache_key, $data );
		}

		return $data;
	}

	/**
	 * Get coupon ID for an order.
	 *
	 * Tries to get the ID from order item meta, then falls back to a query of published coupons.
	 *
	 * @param \WC_Order_Item_Coupon $coupon_item The coupon order item object.
	 * @return int Coupon ID on success, 0 on failure.
	 */
	public static function get_coupon_id( \WC_Order_Item_Coupon $coupon_item ) {
		// First attempt to get coupon ID from order item data.
		$coupon_data = $coupon_item->get_meta( 'coupon_data', true );

		// Normal checkout orders should have this data.
		// See: https://github.com/woocommerce/woocommerce/blob/3dc7df7af9f7ca0c0aa34ede74493e856f276abe/includes/abstracts/abstract-wc-order.php#L1206.
		if ( isset( $coupon_data['id'] ) ) {
			return $coupon_data['id'];
		}

		// Try to get the coupon ID using the code.
		return wc_get_coupon_id_by_code( $coupon_item->get_code() );
	}

	/**
	 * Create or update an an entry in the wc_order_coupon_lookup table for an order.
	 *
	 * @since 3.5.0
	 * @param int $order_id Order ID.
	 * @return int|bool Returns -1 if order won't be processed, or a boolean indicating processing success.
	 */
	public static function sync_order_coupons( $order_id ) {
		global $wpdb;

		$order = wc_get_order( $order_id );

		if ( ! $order ) {
			return -1;
		}

		// Refunds don't affect coupon stats so return successfully if one is called here.
		if ( 'shop_order_refund' === $order->get_type() ) {
			return true;
		}

		$table_name     = self::get_db_table_name();
		$existing_items = $wpdb->get_col(
			$wpdb->prepare(
				// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				"SELECT coupon_id FROM {$table_name} WHERE order_id = %d",
				$order_id
			)
		);
		$existing_items     = array_flip( $existing_items );
		$coupon_items       = $order->get_items( 'coupon' );
		$coupon_items_count = count( $coupon_items );
		$num_updated        = 0;
		$num_deleted        = 0;

		foreach ( $coupon_items as $coupon_item ) {
			$coupon_id = self::get_coupon_id( $coupon_item );
			unset( $existing_items[ $coupon_id ] );

			if ( ! $coupon_id ) {
				// Insert a unique, but obviously invalid ID for this deleted coupon.
				$num_deleted++;
				$coupon_id = -1 * $num_deleted;
			}

			$result = $wpdb->replace(
				self::get_db_table_name(),
				array(
					'order_id'        => $order_id,
					'coupon_id'       => $coupon_id,
					'discount_amount' => $coupon_item->get_discount(),
					'date_created'    => $order->get_date_created( 'edit' )->date( TimeInterval::$sql_datetime_format ),
				),
				array(
					'%d',
					'%d',
					'%f',
					'%s',
				)
			);

			/**
			 * Fires when coupon's reports are updated.
			 *
			 * @param int $coupon_id Coupon ID.
			 * @param int $order_id  Order ID.
			 */
			do_action( 'woocommerce_analytics_update_coupon', $coupon_id, $order_id );

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
					// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
					"DELETE FROM {$table_name} WHERE order_id = %d AND coupon_id in ({$format})",
					$existing_items
				)
			);
		}

		return ( $coupon_items_count === $num_updated );
	}

	/**
	 * Clean coupons data when an order is deleted.
	 *
	 * @param int $order_id Order ID.
	 */
	public static function sync_on_order_delete( $order_id ) {
		global $wpdb;

		$wpdb->delete( self::get_db_table_name(), array( 'order_id' => $order_id ) );
		/**
		 * Fires when coupon's reports are removed from database.
		 *
		 * @param int $coupon_id Coupon ID.
		 * @param int $order_id  Order ID.
		 */
		do_action( 'woocommerce_analytics_delete_coupon', 0, $order_id );

		ReportsCache::invalidate();
	}

	/**
	 * Gets coupons based on the provided arguments.
	 *
	 * @todo Upon core merge, including this in core's `class-wc-coupon-data-store-cpt.php` might make more sense.
	 * @param array $args Array of args to filter the query by. Supports `include`.
	 * @return array Array of results.
	 */
	public function get_coupons( $args ) {
		global $wpdb;
		$query = "SELECT ID, post_title FROM {$wpdb->posts} WHERE post_type='shop_coupon'";

		$included_coupons = $this->get_included_coupons( $args, 'include' );
		if ( ! empty( $included_coupons ) ) {
			$query .= " AND ID IN ({$included_coupons})";
		}

		return $wpdb->get_results( $query ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
	}

	/**
	 * Initialize query objects.
	 */
	protected function initialize_queries() {
		$this->clear_all_clauses();
		$this->subquery = new SqlQuery( $this->context . '_subquery' );
		$this->subquery->add_sql_clause( 'from', self::get_db_table_name() );
		$this->subquery->add_sql_clause( 'group_by', 'coupon_id' );
	}
}
