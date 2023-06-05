<?php
// phpcs:disable Generic.Commenting.Todo.TaskFound
/**
 * OrdersTableQuery class file.
 */

namespace Automattic\WooCommerce\Internal\DataStores\Orders;

use Automattic\WooCommerce\Internal\Utilities\DatabaseUtil;

defined( 'ABSPATH' ) || exit;

/**
 * This class provides a `WP_Query`-like interface to custom order tables.
 *
 * @property-read int   $found_orders  Number of found orders.
 * @property-read int   $found_posts   Alias of the `$found_orders` property.
 * @property-read int   $max_num_pages Max number of pages matching the current query.
 * @property-read array $orders        Order objects, or order IDs.
 * @property-read array $posts         Alias of the $orders property.
 */
class OrdersTableQuery {

	/**
	 * Values to ignore when parsing query arguments.
	 */
	public const SKIPPED_VALUES = array( '', array(), null );

	/**
	 * Regex used to catch "shorthand" comparisons in date-related query args.
	 */
	public const REGEX_SHORTHAND_DATES = '/([^.<>]*)(>=|<=|>|<|\.\.\.)([^.<>]+)/';

	/**
	 * Highest possible unsigned bigint value (unsigned bigints being the type of the `id` column).
	 *
	 * This is deliberately held as a string, rather than a numeric type, for inclusion within queries.
	 */
	private const MYSQL_MAX_UNSIGNED_BIGINT = '18446744073709551615';

	/**
	 * Names of all COT tables (orders, addresses, operational_data, meta) in the form 'table_id' => 'table name'.
	 *
	 * @var array
	 */
	private $tables = array();

	/**
	 * Column mappings for all COT tables.
	 *
	 * @var array
	 */
	private $mappings = array();

	/**
	 * Query vars after processing and sanitization.
	 *
	 * @var array
	 */
	private $args = array();

	/**
	 * Columns to be selected in the SELECT clause.
	 *
	 * @var array
	 */
	private $fields = array();

	/**
	 * Array of table aliases and conditions used to compute the JOIN clause of the query.
	 *
	 * @var array
	 */
	private $join = array();

	/**
	 * Array of fields and conditions used to compute the WHERE clause of the query.
	 *
	 * @var array
	 */
	private $where = array();

	/**
	 * Field to be used in the GROUP BY clause of the query.
	 *
	 * @var array
	 */
	private $groupby = array();

	/**
	 * Array of fields used to compute the ORDER BY clause of the query.
	 *
	 * @var array
	 */
	private $orderby = array();

	/**
	 * Limits used to compute the LIMIT clause of the query.
	 *
	 * @var array
	 */
	private $limits = array();

	/**
	 * Results (order IDs) for the current query.
	 *
	 * @var array
	 */
	private $results = array();

	/**
	 * Final SQL query to run after processing of args.
	 *
	 * @var string
	 */
	private $sql = '';

	/**
	 * Final SQL query to count results after processing of args.
	 *
	 * @var string
	 */
	private $count_sql = '';

	/**
	 * The number of pages (when pagination is enabled).
	 *
	 * @var int
	 */
	private $max_num_pages = 0;

	/**
	 * The number of orders found.
	 *
	 * @var int
	 */
	private $found_orders = 0;

	/**
	 * Field query parser.
	 *
	 * @var OrdersTableFieldQuery
	 */
	private $field_query = null;

	/**
	 * Meta query parser.
	 *
	 * @var OrdersTableMetaQuery
	 */
	private $meta_query = null;

	/**
	 * Search query parser.
	 *
	 * @var OrdersTableSearchQuery?
	 */
	private $search_query = null;

	/**
	 * Date query parser.
	 *
	 * @var WP_Date_Query
	 */
	private $date_query = null;

	/**
	 * Instance of the OrdersTableDataStore class.
	 *
	 * @var OrdersTableDataStore
	 */
	private $order_datastore = null;

	/**
	 * Sets up and runs the query after processing arguments.
	 *
	 * @param array $args Array of query vars.
	 */
	public function __construct( $args = array() ) {
		// Note that ideally we would inject this dependency via constructor, but that's not possible since this class needs to be backward compatible with WC_Order_Query class.
		$this->order_datastore = wc_get_container()->get( OrdersTableDataStore::class );

		$this->tables   = $this->order_datastore::get_all_table_names_with_id();
		$this->mappings = $this->order_datastore->get_all_order_column_mappings();

		$this->args = $args;

		// TODO: args to be implemented.
		unset( $this->args['customer_note'], $this->args['name'] );

		$this->build_query();
		$this->run_query();
	}

	/**
	 * Remaps some legacy and `WP_Query` specific query vars to vars available in the customer order table scheme.
	 *
	 * @return void
	 */
	private function maybe_remap_args(): void {
		$mapping = array(
			// WP_Query legacy.
			'post_date'           => 'date_created',
			'post_date_gmt'       => 'date_created_gmt',
			'post_modified'       => 'date_updated',
			'post_modified_gmt'   => 'date_updated_gmt',
			'post_status'         => 'status',
			'_date_completed'     => 'date_completed',
			'_date_paid'          => 'date_paid',
			'paged'               => 'page',
			'post_parent'         => 'parent_order_id',
			'post_parent__in'     => 'parent_order_id',
			'post_parent__not_in' => 'parent_exclude',
			'post__not_in'        => 'exclude',
			'posts_per_page'      => 'limit',
			'p'                   => 'id',
			'post__in'            => 'id',
			'post_type'           => 'type',
			'fields'              => 'return',

			'customer_user'       => 'customer_id',
			'order_currency'      => 'currency',
			'order_version'       => 'woocommerce_version',
			'cart_discount'       => 'discount_total_amount',
			'cart_discount_tax'   => 'discount_tax_amount',
			'order_shipping'      => 'shipping_total_amount',
			'order_shipping_tax'  => 'shipping_tax_amount',
			'order_tax'           => 'tax_amount',

			// Translate from WC_Order_Query to table structure.
			'version'             => 'woocommerce_version',
			'date_modified'       => 'date_updated',
			'date_modified_gmt'   => 'date_updated_gmt',
			'discount_total'      => 'discount_total_amount',
			'discount_tax'        => 'discount_tax_amount',
			'shipping_total'      => 'shipping_total_amount',
			'shipping_tax'        => 'shipping_tax_amount',
			'cart_tax'            => 'tax_amount',
			'total'               => 'total_amount',
			'customer_ip_address' => 'ip_address',
			'customer_user_agent' => 'user_agent',
			'parent'              => 'parent_order_id',
		);

		foreach ( $mapping as $query_key => $table_field ) {
			if ( isset( $this->args[ $query_key ] ) && '' !== $this->args[ $query_key ] ) {
				$this->args[ $table_field ] = $this->args[ $query_key ];
				unset( $this->args[ $query_key ] );
			}
		}

		// meta_query.
		$this->args['meta_query'] = ( $this->arg_isset( 'meta_query' ) && is_array( $this->args['meta_query'] ) ) ? $this->args['meta_query'] : array(); // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query

		$shortcut_meta_query = array();
		foreach ( array( 'key', 'value', 'compare', 'type', 'compare_key', 'type_key' ) as $key ) {
			if ( $this->arg_isset( "meta_{$key}" ) ) {
				$shortcut_meta_query[ $key ] = $this->args[ "meta_{$key}" ];
			}
		}

		if ( ! empty( $shortcut_meta_query ) ) {
			if ( ! empty( $this->args['meta_query'] ) ) {
				$this->args['meta_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
					'relation' => 'AND',
					$shortcut_meta_query,
					$this->args['meta_query'],
				);
			} else {
				$this->args['meta_query'] = array( $shortcut_meta_query ); // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
			}
		}
	}

	/**
	 * Generates a `WP_Date_Query` compatible query from a given date.
	 * YYYY-MM-DD queries have 'day' precision for backwards compatibility.
	 *
	 * @param mixed  $date The date. Can be a {@see \WC_DateTime}, a timestamp or a string.
	 * @param string $timezone The timezone to use for the date.
	 * @return array An array with keys 'year', 'month', 'day' and possibly 'hour', 'minute' and 'second'.
	 */
	private function date_to_date_query_arg( $date, $timezone ): array {
		$result    = array(
			'year'  => '',
			'month' => '',
			'day'   => '',
		);
		$precision = 'second';

		if ( is_numeric( $date ) ) {
			$date = new \WC_DateTime( "@{$date}", new \DateTimeZone( $timezone ) );
		} elseif ( ! is_a( $date, 'WC_DateTime' ) ) {
			// YYYY-MM-DD queries have 'day' precision for backwards compat.
			$date      = wc_string_to_datetime( $date );
			$precision = 'day';
		}

		$result['year']  = $date->date( 'Y' );
		$result['month'] = $date->date( 'm' );
		$result['day']   = $date->date( 'd' );

		if ( 'second' === $precision ) {
			$result['hour']   = $date->date( 'H' );
			$result['minute'] = $date->date( 'i' );
			$result['second'] = $date->date( 's' );
		}

		return $result;
	}

	/**
	 * Processes date-related query args and merges the result into 'date_query'.
	 *
	 * @return void
	 * @throws \Exception When date args are invalid.
	 */
	private function process_date_args(): void {
		if ( $this->arg_isset( 'date_query' ) ) {
			// Process already passed date queries args.
			$this->args['date_query'] = $this->map_gmt_and_post_keys_to_hpos_keys( $this->args['date_query'] );
		}

		$valid_operators        = array( '>', '>=', '=', '<=', '<', '...' );
		$date_queries           = array();
		$local_to_gmt_date_keys = array(
			'date_created'   => 'date_created_gmt',
			'date_updated'   => 'date_updated_gmt',
			'date_paid'      => 'date_paid_gmt',
			'date_completed' => 'date_completed_gmt',
		);

		$gmt_date_keys   = array_values( $local_to_gmt_date_keys );
		$local_date_keys = array_keys( $local_to_gmt_date_keys );

		$valid_date_keys = array_merge( $gmt_date_keys, $local_date_keys );
		$date_keys       = array_filter( $valid_date_keys, array( $this, 'arg_isset' ) );

		foreach ( $date_keys as $date_key ) {
			$date_value = $this->args[ $date_key ];
			$operator   = '=';
			$dates      = array();
			$timezone   = in_array( $date_key, $gmt_date_keys, true ) ? '+0000' : wc_timezone_string();

			if ( is_string( $date_value ) && preg_match( self::REGEX_SHORTHAND_DATES, $date_value, $matches ) ) {
				$operator = in_array( $matches[2], $valid_operators, true ) ? $matches[2] : '';

				if ( ! empty( $matches[1] ) ) {
					$dates[] = $this->date_to_date_query_arg( $matches[1], $timezone );
				}

				$dates[] = $this->date_to_date_query_arg( $matches[3], $timezone );
			} else {
				$dates[] = $this->date_to_date_query_arg( $date_value, $timezone );
			}

			if ( empty( $dates ) || ! $operator || ( '...' === $operator && count( $dates ) < 2 ) ) {
				throw new \Exception( 'Invalid date_query' );
			}

			$operator_to_keys = array();

			if ( in_array( $operator, array( '>', '>=', '...' ), true ) ) {
				$operator_to_keys[] = 'after';
			}

			if ( in_array( $operator, array( '<', '<=', '...' ), true ) ) {
				$operator_to_keys[] = 'before';
			}

			$date_key       = in_array( $date_key, $local_date_keys, true ) ? $local_to_gmt_date_keys[ $date_key ] : $date_key;
			$date_queries[] = array_merge(
				array(
					'column'    => $date_key,
					'inclusive' => ! in_array( $operator, array( '<', '>' ), true ),
				),
				'=' === $operator
					? end( $dates )
					: array_combine( $operator_to_keys, $dates )
			);
		}

		// Add top-level date parameters to the date_query.
		$tl_query = array();
		foreach ( array( 'hour', 'minute', 'second', 'year', 'monthnum', 'week', 'day', 'year' ) as $tl_key ) {
			if ( $this->arg_isset( $tl_key ) ) {
				$tl_query[ $tl_key ] = $this->args[ $tl_key ];
				unset( $this->args[ $tl_key ] );
			}
		}

		if ( $tl_query ) {
			$tl_query['column'] = 'date_created_gmt';
			$date_queries[]     = $tl_query;
		}

		if ( $date_queries ) {
			if ( ! $this->arg_isset( 'date_query' ) ) {
				$this->args['date_query'] = array();
			}

			$this->args['date_query'] = array_merge(
				array( 'relation' => 'AND' ),
				$date_queries,
				$this->args['date_query']
			);
		}

		$this->process_date_query_columns();
	}

	/**
	 * Helper function to map posts and gmt based keys to HPOS keys.
	 *
	 * @param array $query Date query argument.
	 *
	 * @return array|mixed Date query argument with modified keys.
	 */
	private function map_gmt_and_post_keys_to_hpos_keys( $query ) {
		if ( ! is_array( $query ) ) {
			return $query;
		}

		$post_to_hpos_mappings = array(
			'post_date'         => 'date_created',
			'post_date_gmt'     => 'date_created_gmt',
			'post_modified'     => 'date_updated',
			'post_modified_gmt' => 'date_updated_gmt',
			'_date_completed'   => 'date_completed',
			'_date_paid'        => 'date_paid',
			'date_modified'     => 'date_updated',
			'date_modified_gmt' => 'date_updated_gmt',
		);

		$local_to_gmt_date_keys = array(
			'date_created'   => 'date_created_gmt',
			'date_updated'   => 'date_updated_gmt',
			'date_paid'      => 'date_paid_gmt',
			'date_completed' => 'date_completed_gmt',
		);

		array_walk(
			$query,
			function ( &$sub_query ) {
				$sub_query = $this->map_gmt_and_post_keys_to_hpos_keys( $sub_query );
			}
		);

		if ( ! isset( $query['column'] ) ) {
			return $query;
		}

		if ( isset( $post_to_hpos_mappings[ $query['column'] ] ) ) {
			$query['column'] = $post_to_hpos_mappings[ $query['column'] ];
		}

		// Convert any local dates to GMT.
		if ( isset( $local_to_gmt_date_keys[ $query['column'] ] ) ) {
			$query['column']  = $local_to_gmt_date_keys[ $query['column'] ];
			$op               = isset( $query['after'] ) ? 'after' : 'before';
			$date_value_local = $query[ $op ];
			$date_value_gmt   = wc_string_to_timestamp( get_gmt_from_date( wc_string_to_datetime( $date_value_local ) ) );
			$query[ $op ]     = $this->date_to_date_query_arg( $date_value_gmt, 'UTC' );
		}

		return $query;
	}

	/**
	 * Makes sure all 'date_query' columns are correctly prefixed and their respective tables are being JOIN'ed.
	 *
	 * @return void
	 */
	private function process_date_query_columns() {
		global $wpdb;

		$legacy_columns = array(
			'post_date'         => 'date_created_gmt',
			'post_date_gmt'     => 'date_created_gmt',
			'post_modified'     => 'date_modified_gmt',
			'post_modified_gmt' => 'date_updated_gmt',
		);
		$table_mapping  = array(
			'date_created_gmt'   => $this->tables['orders'],
			'date_updated_gmt'   => $this->tables['orders'],
			'date_paid_gmt'      => $this->tables['operational_data'],
			'date_completed_gmt' => $this->tables['operational_data'],
		);

		if ( empty( $this->args['date_query'] ) ) {
			return;
		}

		array_walk_recursive(
			$this->args['date_query'],
			function( &$value, $key ) use ( $legacy_columns, $table_mapping, $wpdb ) {
				if ( 'column' !== $key ) {
					return;
				}

				// Translate legacy columns from wp_posts if necessary.
				$value =
					( isset( $legacy_columns[ $value ] ) || isset( $legacy_columns[ "{$wpdb->posts}.{$value}" ] ) )
					? $legacy_columns[ $value ]
					: $value;

				$table = $table_mapping[ $value ] ?? null;

				if ( ! $table ) {
					return;
				}

				$value = "{$table}.{$value}";

				if ( $table !== $this->tables['orders'] ) {
					$this->join( $table, '', '', 'inner', true );
				}
			}
		);
	}

	/**
	 * Sanitizes the 'status' query var.
	 *
	 * @return void
	 */
	private function sanitize_status(): void {
		// Sanitize status.
		$valid_statuses = array_keys( wc_get_order_statuses() );

		if ( empty( $this->args['status'] ) || 'any' === $this->args['status'] ) {
			$this->args['status'] = $valid_statuses;
		} elseif ( 'all' === $this->args['status'] ) {
			$this->args['status'] = array();
		} else {
			$this->args['status'] = is_array( $this->args['status'] ) ? $this->args['status'] : array( $this->args['status'] );

			foreach ( $this->args['status'] as &$status ) {
				$status = in_array( 'wc-' . $status, $valid_statuses, true ) ? 'wc-' . $status : $status;
			}

			$this->args['status'] = array_unique( array_filter( $this->args['status'] ) );
		}
	}

	/**
	 * Parses and sanitizes the 'orderby' query var.
	 *
	 * @return void
	 */
	private function sanitize_order_orderby(): void {
		// Allowed keys.
		// TODO: rand, meta keys, etc.
		$allowed_keys = array( 'ID', 'id', 'type', 'date', 'modified', 'parent' );

		// Translate $orderby to a valid field.
		$mapping = array(
			'ID'            => "{$this->tables['orders']}.id",
			'id'            => "{$this->tables['orders']}.id",
			'type'          => "{$this->tables['orders']}.type",
			'date'          => "{$this->tables['orders']}.date_created_gmt",
			'date_created'  => "{$this->tables['orders']}.date_created_gmt",
			'modified'      => "{$this->tables['orders']}.date_updated_gmt",
			'date_modified' => "{$this->tables['orders']}.date_updated_gmt",
			'parent'        => "{$this->tables['orders']}.parent_order_id",
			'total'         => "{$this->tables['orders']}.total_amount",
			'order_total'   => "{$this->tables['orders']}.total_amount",
		);

		$order   = $this->args['order'] ?? '';
		$orderby = $this->args['orderby'] ?? '';

		if ( 'none' === $orderby ) {
			return;
		}

		// No need to sanitize, will be processed in calling function.
		if ( 'include' === $orderby || 'post__in' === $orderby ) {
			return;
		}

		if ( is_string( $orderby ) ) {
			$orderby_fields = array_map( 'trim', explode( ' ', $orderby ) );
			$orderby        = array();
			foreach ( $orderby_fields as $field ) {
				$orderby[ $field ] = $order;
			}
		}

		$allowed_orderby = array_merge(
			array_keys( $mapping ),
			array_values( $mapping ),
			$this->meta_query ? $this->meta_query->get_orderby_keys() : array()
		);

		$this->args['orderby'] = array();
		foreach ( $orderby as $order_key => $order ) {
			if ( ! in_array( $order_key, $allowed_orderby, true ) ) {
				continue;
			}

			if ( isset( $mapping[ $order_key ] ) ) {
				$order_key = $mapping[ $order_key ];
			}

			$this->args['orderby'][ $order_key ] = $this->sanitize_order( $order );
		}
	}

	/**
	 * Makes sure the order in an ORDER BY statement is either 'ASC' o 'DESC'.
	 *
	 * @param string $order The unsanitized order.
	 * @return string The sanitized order.
	 */
	private function sanitize_order( string $order ): string {
		$order = strtoupper( $order );

		return in_array( $order, array( 'ASC', 'DESC' ), true ) ? $order : 'DESC';
	}

	/**
	 * Builds the final SQL query to be run.
	 *
	 * @return void
	 */
	private function build_query(): void {
		$this->maybe_remap_args();

		// Field queries.
		if ( ! empty( $this->args['field_query'] ) ) {
			$this->field_query = new OrdersTableFieldQuery( $this );
			$sql               = $this->field_query->get_sql_clauses();
			$this->join        = $sql['join'] ? array_merge( $this->join, $sql['join'] ) : $this->join;
			$this->where       = $sql['where'] ? array_merge( $this->where, $sql['where'] ) : $this->where;
		}

		// Build query.
		$this->process_date_args();
		$this->process_orders_table_query_args();
		$this->process_operational_data_table_query_args();
		$this->process_addresses_table_query_args();

		// Search queries.
		if ( ! empty( $this->args['s'] ) ) {
			$this->search_query = new OrdersTableSearchQuery( $this );
			$sql                = $this->search_query->get_sql_clauses();
			$this->join         = $sql['join'] ? array_merge( $this->join, $sql['join'] ) : $this->join;
			$this->where        = $sql['where'] ? array_merge( $this->where, $sql['where'] ) : $this->where;
		}

		// Meta queries.
		if ( ! empty( $this->args['meta_query'] ) ) {
			$this->meta_query = new OrdersTableMetaQuery( $this );

			$sql = $this->meta_query->get_sql_clauses();

			$this->join  = $sql['join'] ? array_merge( $this->join, $sql['join'] ) : $this->join;
			$this->where = $sql['where'] ? array_merge( $this->where, array( $sql['where'] ) ) : $this->where;

		}

		// Date queries.
		if ( ! empty( $this->args['date_query'] ) ) {
			$this->date_query = new \WP_Date_Query( $this->args['date_query'], "{$this->tables['orders']}.date_created_gmt" );
			$this->where[]    = substr( trim( $this->date_query->get_sql() ), 3 ); // WP_Date_Query includes "AND".
		}

		$this->process_orderby();
		$this->process_limit();

		$orders_table = $this->tables['orders'];

		// Group by is a faster substitute for DISTINCT, as long as we are only selecting IDs. MySQL don't like it when we join tables and use DISTINCT.
		$this->groupby[] = "{$this->tables['orders']}.id";
		$this->fields    = "{$orders_table}.id";
		$fields          = $this->fields;

		// JOIN.
		$join = implode( ' ', array_unique( array_filter( array_map( 'trim', $this->join ) ) ) );

		// WHERE.
		$where = '1=1';
		foreach ( $this->where as $_where ) {
			$where .= " AND ({$_where})";
		}

		// ORDER BY.
		$orderby = $this->orderby ? ( 'ORDER BY ' . implode( ', ', $this->orderby ) ) : '';

		// LIMITS.
		$limits = '';

		if ( ! empty( $this->limits ) && count( $this->limits ) === 2 ) {
			list( $offset, $row_count ) = $this->limits;
			$row_count                  = -1 === $row_count ? self::MYSQL_MAX_UNSIGNED_BIGINT : (int) $row_count;
			$limits                     = 'LIMIT ' . (int) $offset . ', ' . $row_count;
		}

		// GROUP BY.
		$groupby = $this->groupby ? 'GROUP BY ' . implode( ', ', (array) $this->groupby ) : '';

		$this->sql = "SELECT $fields FROM $orders_table $join WHERE $where $groupby $orderby $limits";
		$this->build_count_query( $fields, $join, $where, $groupby );
	}

	/**
	 * Build SQL query for counting total number of results.
	 *
	 * @param string $fields Prepared fields for SELECT clause.
	 * @param string $join Prepared JOIN clause.
	 * @param string $where Prepared WHERE clause.
	 * @param string $groupby Prepared GROUP BY clause.
	 */
	private function build_count_query( $fields, $join, $where, $groupby ) {
		if ( ! isset( $this->sql ) || '' === $this->sql ) {
			wc_doing_it_wrong( __FUNCTION__, 'Count query can only be build after main query is built.', '7.3.0' );
		}
		$orders_table    = $this->tables['orders'];
		$this->count_sql = "SELECT COUNT(DISTINCT $fields) FROM  $orders_table $join WHERE $where";
	}

	/**
	 * Returns the table alias for a given table mapping.
	 *
	 * @param string $mapping_id The mapping name (e.g. 'orders' or 'operational_data').
	 * @return string Table alias.
	 *
	 * @since 7.0.0
	 */
	public function get_core_mapping_alias( string $mapping_id ): string {
		return in_array( $mapping_id, array( 'billing_address', 'shipping_address' ), true )
			? $mapping_id
			: $this->tables[ $mapping_id ];
	}

	/**
	 * Returns an SQL JOIN clause that can be used to join the main orders table with another order table.
	 *
	 * @param string $mapping_id The mapping name (e.g. 'orders' or 'operational_data').
	 * @return string The JOIN clause.
	 *
	 * @since 7.0.0
	 */
	public function get_core_mapping_join( string $mapping_id ): string {
		global $wpdb;

		if ( 'orders' === $mapping_id ) {
			return '';
		}

		$is_address_mapping = in_array( $mapping_id, array( 'billing_address', 'shipping_address' ), true );

		$alias   = $this->get_core_mapping_alias( $mapping_id );
		$table   = $is_address_mapping ? $this->tables['addresses'] : $this->tables[ $mapping_id ];
		$join    = '';
		$join_on = '';

		$join .= "INNER JOIN `{$table}`" . ( $alias !== $table ? " AS `{$alias}`" : '' );

		if ( isset( $this->mappings[ $mapping_id ]['order_id'] ) ) {
			$join_on .= "`{$this->tables['orders']}`.id = `{$alias}`.order_id";
		}

		if ( $is_address_mapping ) {
			$join_on .= $wpdb->prepare( " AND `{$alias}`.address_type = %s", substr( $mapping_id, 0, -8 ) ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		}

		return $join . ( $join_on ? " ON ( {$join_on} )" : '' );
	}

	/**
	 * JOINs the main orders table with another table.
	 *
	 * @param string  $table      Table name (including prefix).
	 * @param string  $alias      Table alias to use. Defaults to $table.
	 * @param string  $on         ON clause. Defaults to "wc_orders.id = {$alias}.order_id".
	 * @param string  $join_type  JOIN type: LEFT, RIGHT or INNER.
	 * @param boolean $alias_once If TRUE, table won't be JOIN'ed again if already JOIN'ed.
	 * @return void
	 * @throws \Exception When an error occurs, such as trying to re-use an alias with $alias_once = FALSE.
	 */
	private function join( string $table, string $alias = '', string $on = '', string $join_type = 'inner', bool $alias_once = false ) {
		$alias     = empty( $alias ) ? $table : $alias;
		$join_type = strtoupper( trim( $join_type ) );

		if ( $this->tables['orders'] === $alias ) {
			// translators: %s is a table name.
			throw new \Exception( sprintf( __( '%s can not be used as a table alias in OrdersTableQuery', 'woocommerce' ), $alias ) );
		}

		if ( empty( $on ) ) {
			if ( $this->tables['orders'] === $table ) {
				$on = "`{$this->tables['orders']}`.id = `{$alias}`.id";
			} else {
				$on = "`{$this->tables['orders']}`.id = `{$alias}`.order_id";
			}
		}

		if ( isset( $this->join[ $alias ] ) ) {
			if ( ! $alias_once ) {
				// translators: %s is a table name.
				throw new \Exception( sprintf( __( 'Can not re-use table alias "%s" in OrdersTableQuery.', 'woocommerce' ), $alias ) );
			}

			return;
		}

		if ( '' === $join_type || ! in_array( $join_type, array( 'LEFT', 'RIGHT', 'INNER' ), true ) ) {
			$join_type = 'INNER';
		}

		$sql_join  = '';
		$sql_join .= "{$join_type} JOIN `{$table}` ";
		$sql_join .= ( $alias !== $table ) ? "AS `{$alias}` " : '';
		$sql_join .= "ON ( {$on} )";

		$this->join[ $alias ] = $sql_join;
	}

	/**
	 * Generates a properly escaped and sanitized WHERE condition for a given field.
	 *
	 * @param string $table    The table the field belongs to.
	 * @param string $field    The field or column name.
	 * @param string $operator The operator to use in the condition. Defaults to '=' or 'IN' depending on $value.
	 * @param mixed  $value    The value.
	 * @param string $type     The column type as specified in {@see OrdersTableDataStore} column mappings.
	 * @return string The resulting WHERE condition.
	 */
	public function where( string $table, string $field, string $operator, $value, string $type ): string {
		global $wpdb;

		$db_util  = wc_get_container()->get( DatabaseUtil::class );
		$operator = strtoupper( '' !== $operator ? $operator : '=' );

		try {
			$format = $db_util->get_wpdb_format_for_type( $type );
		} catch ( \Exception $e ) {
			$format = '%s';
		}

		// = and != can be shorthands for IN and NOT in for array values.
		if ( is_array( $value ) && '=' === $operator ) {
			$operator = 'IN';
		} elseif ( is_array( $value ) && '!=' === $operator ) {
			$operator = 'NOT IN';
		}

		if ( ! in_array( $operator, array( '=', '!=', 'IN', 'NOT IN' ), true ) ) {
			return false;
		}

		if ( is_array( $value ) ) {
			$value = array_map( array( $db_util, 'format_object_value_for_db' ), $value, array_fill( 0, count( $value ), $type ) );
		} else {
			$value = $db_util->format_object_value_for_db( $value, $type );
		}

		if ( is_array( $value ) ) {
			$placeholder = array_fill( 0, count( $value ), $format );
			$placeholder = '(' . implode( ',', $placeholder ) . ')';
		} else {
			$placeholder = $format;
		}

		$sql = $wpdb->prepare( "{$table}.{$field} {$operator} {$placeholder}", $value ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared,WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare

		return $sql;
	}

	/**
	 * Processes fields related to the orders table.
	 *
	 * @return void
	 */
	private function process_orders_table_query_args(): void {
		$this->sanitize_status();

		$fields = array_filter(
			array(
				'id',
				'status',
				'type',
				'currency',
				'tax_amount',
				'customer_id',
				'billing_email',
				'total_amount',
				'parent_order_id',
				'payment_method',
				'payment_method_title',
				'transaction_id',
				'ip_address',
				'user_agent',
			),
			array( $this, 'arg_isset' )
		);

		foreach ( $fields as $arg_key ) {
			$this->where[] = $this->where( $this->tables['orders'], $arg_key, '=', $this->args[ $arg_key ], $this->mappings['orders'][ $arg_key ]['type'] );
		}

		if ( $this->arg_isset( 'parent_exclude' ) ) {
			$this->where[] = $this->where( $this->tables['orders'], 'parent_order_id', '!=', $this->args['parent_exclude'], 'int' );
		}

		if ( $this->arg_isset( 'exclude' ) ) {
			$this->where[] = $this->where( $this->tables['orders'], 'id', '!=', $this->args['exclude'], 'int' );
		}

		// 'customer' is a very special field.
		if ( $this->arg_isset( 'customer' ) ) {
			$customer_query = $this->generate_customer_query( $this->args['customer'] );

			if ( $customer_query ) {
				$this->where[] = $customer_query;
			}
		}
	}

	/**
	 * Generate SQL conditions for the 'customer' query.
	 *
	 * @param array  $values   List of customer ids or emails.
	 * @param string $relation 'OR' or 'AND' relation used to build the customer query.
	 * @return string SQL to be used in a WHERE clause.
	 */
	private function generate_customer_query( $values, string $relation = 'OR' ): string {
		$values = is_array( $values ) ? $values : array( $values );
		$ids    = array();
		$emails = array();

		foreach ( $values as $value ) {
			if ( is_array( $value ) ) {
				$sql      = $this->generate_customer_query( $value, 'AND' );
				$pieces[] = $sql ? '(' . $sql . ')' : '';
			} elseif ( is_numeric( $value ) ) {
				$ids[] = absint( $value );
			} elseif ( is_string( $value ) && is_email( $value ) ) {
				$emails[] = sanitize_email( $value );
			} else {
				// Invalid query.
				$pieces[] = '1=0';
			}
		}

		if ( $ids ) {
			$pieces[] = $this->where( $this->tables['orders'], 'customer_id', '=', $ids, 'int' );
		}

		if ( $emails ) {
			$pieces[] = $this->where( $this->tables['orders'], 'billing_email', '=', $emails, 'string' );
		}

		return $pieces ? implode( " $relation ", $pieces ) : '';
	}

	/**
	 * Processes fields related to the operational data table.
	 *
	 * @return void
	 */
	private function process_operational_data_table_query_args(): void {
		$fields = array_filter(
			array(
				'created_via',
				'woocommerce_version',
				'prices_include_tax',
				'order_key',
				'discount_total_amount',
				'discount_tax_amount',
				'shipping_total_amount',
				'shipping_tax_amount',
			),
			array( $this, 'arg_isset' )
		);

		if ( ! $fields ) {
			return;
		}

		$this->join(
			$this->tables['operational_data'],
			'',
			'',
			'inner',
			true
		);

		foreach ( $fields as $arg_key ) {
			$this->where[] = $this->where( $this->tables['operational_data'], $arg_key, '=', $this->args[ $arg_key ], $this->mappings['operational_data'][ $arg_key ]['type'] );
		}
	}

	/**
	 * Processes fields related to the addresses table.
	 *
	 * @return void
	 */
	private function process_addresses_table_query_args(): void {
		global $wpdb;

		foreach ( array( 'billing', 'shipping' ) as $address_type ) {
			$fields = array_filter(
				array(
					$address_type . '_first_name',
					$address_type . '_last_name',
					$address_type . '_company',
					$address_type . '_address_1',
					$address_type . '_address_2',
					$address_type . '_city',
					$address_type . '_state',
					$address_type . '_postcode',
					$address_type . '_country',
					$address_type . '_phone',
				),
				array( $this, 'arg_isset' )
			);

			if ( ! $fields ) {
				continue;
			}

			$this->join(
				$this->tables['addresses'],
				$address_type,
				$wpdb->prepare( "{$this->tables['orders']}.id = {$address_type}.order_id AND {$address_type}.address_type = %s", $address_type ), // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				'inner',
				false
			);

			foreach ( $fields as $arg_key ) {
				$column_name = str_replace( "{$address_type}_", '', $arg_key );

				$this->where[] = $this->where(
					$address_type,
					$column_name,
					'=',
					$this->args[ $arg_key ],
					$this->mappings[ "{$address_type}_address" ][ $column_name ]['type']
				);
			}
		}
	}

	/**
	 * Generates the ORDER BY clause.
	 *
	 * @return void
	 */
	private function process_orderby(): void {
		// 'order' and 'orderby' vars.
		$this->args['order'] = $this->sanitize_order( $this->args['order'] ?? '' );
		$this->sanitize_order_orderby();

		$orderby = $this->args['orderby'];

		if ( 'none' === $orderby ) {
			$this->orderby = '';
			return;
		}

		if ( 'include' === $orderby || 'post__in' === $orderby ) {
			$ids = $this->args['id'] ?? $this->args['includes'];
			if ( empty( $ids ) ) {
				return;
			}
			$ids           = array_map( 'absint', $ids );
			$this->orderby = array( "FIELD( {$this->tables['orders']}.id, " . implode( ',', $ids ) . ' )' );
			return;
		}

		$meta_orderby_keys = $this->meta_query ? $this->meta_query->get_orderby_keys() : array();

		$orderby_array = array();
		foreach ( $this->args['orderby'] as $_orderby => $order ) {
			if ( in_array( $_orderby, $meta_orderby_keys, true ) ) {
				$_orderby = $this->meta_query->get_orderby_clause_for_key( $_orderby );
			}

			$orderby_array[] = "{$_orderby} {$order}";
		}

		$this->orderby = $orderby_array;
	}

	/**
	 * Generates the limits to be used in the LIMIT clause.
	 *
	 * @return void
	 */
	private function process_limit(): void {
		$row_count = ( $this->arg_isset( 'limit' ) ? (int) $this->args['limit'] : false );
		$page      = ( $this->arg_isset( 'page' ) ? absint( $this->args['page'] ) : 1 );
		$offset    = ( $this->arg_isset( 'offset' ) ? absint( $this->args['offset'] ) : false );

		// Bool false indicates no limit was specified; less than -1 means an invalid value was passed (such as -3).
		if ( false === $row_count || $row_count < -1 ) {
			return;
		}

		if ( false === $offset && $row_count > -1 ) {
			$offset = (int) ( ( $page - 1 ) * $row_count );
		}

		$this->limits = array( $offset, $row_count );
	}

	/**
	 * Checks if a query var is set (i.e. not one of the "skipped values").
	 *
	 * @param string $arg_key Query var.
	 * @return bool TRUE if query var is set.
	 */
	public function arg_isset( string $arg_key ): bool {
		return ( isset( $this->args[ $arg_key ] ) && ! in_array( $this->args[ $arg_key ], self::SKIPPED_VALUES, true ) );
	}

	/**
	 * Runs the SQL query.
	 *
	 * @return void
	 */
	private function run_query(): void {
		global $wpdb;

		// Run query.
		$this->orders = array_map( 'absint', $wpdb->get_col( $this->sql ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

		// Set max_num_pages and found_orders if necessary.
		if ( ( $this->arg_isset( 'no_found_rows' ) && ! $this->args['no_found_rows'] ) || empty( $this->orders ) ) {
			return;
		}

		if ( $this->limits ) {
			$this->found_orders  = absint( $wpdb->get_var( $this->count_sql ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
			$this->max_num_pages = (int) ceil( $this->found_orders / $this->args['limit'] );
		} else {
			$this->found_orders = count( $this->orders );
		}
	}

	/**
	 * Make some private available for backwards compatibility.
	 *
	 * @param string $name Property to get.
	 * @return mixed
	 */
	public function __get( string $name ) {
		switch ( $name ) {
			case 'found_orders':
			case 'found_posts':
				return $this->found_orders;
			case 'max_num_pages':
				return $this->max_num_pages;
			case 'posts':
			case 'orders':
				return $this->results;
			case 'request':
				return $this->sql;
			default:
				break;
		}
	}

	/**
	 * Returns the value of one of the query arguments.
	 *
	 * @param string $arg_name Query var.
	 * @return mixed
	 */
	public function get( string $arg_name ) {
		return $this->args[ $arg_name ] ?? null;
	}

	/**
	 * Returns the name of one of the OrdersTableDatastore tables.
	 *
	 * @param string $table_id Table identifier. One of 'orders', 'operational_data', 'addresses', 'meta'.
	 * @return string The prefixed table name.
	 * @throws \Exception When table ID is not found.
	 */
	public function get_table_name( string $table_id = '' ): string {
		if ( ! isset( $this->tables[ $table_id ] ) ) {
			// Translators: %s is a table identifier.
			throw new \Exception( sprintf( __( 'Invalid table id: %s.', 'woocommerce' ), $table_id ) );
		}

		return $this->tables[ $table_id ];
	}

	/**
	 * Finds table and mapping information about a field or column.
	 *
	 * @param string $field Field to look for in `<mapping|field_name>.<column|field_name>` format or just `<field_name>`.
	 * @return false|array {
	 *     @type string $table      Full table name where the field is located.
	 *     @type string $mapping_id Unprefixed table or mapping name.
	 *     @type string $field_name Name of the corresponding order field.
	 *     @type string $column     Column in $table that corresponds to the field.
	 *     @type string $type       Field type.
	 * }
	 */
	public function get_field_mapping_info( $field ) {
		global $wpdb;

		$result = array(
			'table'       => '',
			'mapping_id'  => '',
			'field_name'  => '',
			'column'      => '',
			'column_type' => '',
		);

		$mappings_to_search = array();

		if ( false !== strstr( $field, '.' ) ) {
			list( $mapping_or_table, $field_name_or_col ) = explode( '.', $field );

			$mapping_or_table = substr( $mapping_or_table, 0, strlen( $wpdb->prefix ) ) === $wpdb->prefix ? substr( $mapping_or_table, strlen( $wpdb->prefix ) ) : $mapping_or_table;
			$mapping_or_table = 'wc_' === substr( $mapping_or_table, 0, 3 ) ? substr( $mapping_or_table, 3 ) : $mapping_or_table;

			if ( isset( $this->mappings[ $mapping_or_table ] ) ) {
				if ( isset( $this->mappings[ $mapping_or_table ][ $field_name_or_col ] ) ) {
					$result['mapping_id'] = $mapping_or_table;
					$result['column']     = $field_name_or_col;
				} else {
					$mappings_to_search = array( $mapping_or_table );
				}
			}
		} else {
			$field_name_or_col  = $field;
			$mappings_to_search = array_keys( $this->mappings );
		}

		foreach ( $mappings_to_search as $mapping_id ) {
			foreach ( $this->mappings[ $mapping_id ] as $column_name => $column_data ) {
				if ( isset( $column_data['name'] ) && $column_data['name'] === $field_name_or_col ) {
					$result['mapping_id'] = $mapping_id;
					$result['column']     = $column_name;
					break 2;
				}
			}
		}

		if ( ! $result['mapping_id'] || ! $result['column'] ) {
			return false;
		}

		$field_info = $this->mappings[ $result['mapping_id'] ][ $result['column'] ];

		$result['field_name']  = $field_info['name'];
		$result['column_type'] = $field_info['type'];
		$result['table']       = ( in_array( $result['mapping_id'], array( 'billing_address', 'shipping_address' ), true ) )
								? $this->tables['addresses']
								: $this->tables[ $result['mapping_id'] ];

		return $result;
	}

}
