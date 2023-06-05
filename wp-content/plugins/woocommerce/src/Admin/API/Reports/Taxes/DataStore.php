<?php
/**
 * API\Reports\Taxes\DataStore class file.
 */

namespace Automattic\WooCommerce\Admin\API\Reports\Taxes;

defined( 'ABSPATH' ) || exit;

use Automattic\WooCommerce\Admin\API\Reports\DataStore as ReportsDataStore;
use Automattic\WooCommerce\Admin\API\Reports\DataStoreInterface;
use Automattic\WooCommerce\Admin\API\Reports\TimeInterval;
use Automattic\WooCommerce\Admin\API\Reports\SqlQuery;
use Automattic\WooCommerce\Admin\API\Reports\Cache as ReportsCache;

/**
 * API\Reports\Taxes\DataStore.
 */
class DataStore extends ReportsDataStore implements DataStoreInterface {

	/**
	 * Table used to get the data.
	 *
	 * @var string
	 */
	protected static $table_name = 'wc_order_tax_lookup';

	/**
	 * Cache identifier.
	 *
	 * @var string
	 */
	protected $cache_key = 'taxes';

	/**
	 * Mapping columns to data type to return correct response types.
	 *
	 * @var array
	 */
	protected $column_types = array(
		'tax_rate_id'  => 'intval',
		'name'         => 'strval',
		'tax_rate'     => 'floatval',
		'country'      => 'strval',
		'state'        => 'strval',
		'priority'     => 'intval',
		'total_tax'    => 'floatval',
		'order_tax'    => 'floatval',
		'shipping_tax' => 'floatval',
		'orders_count' => 'intval',
	);

	/**
	 * Data store context used to pass to filters.
	 *
	 * @var string
	 */
	protected $context = 'taxes';

	/**
	 * Assign report columns once full table name has been assigned.
	 */
	protected function assign_report_columns() {
		$table_name           = self::get_db_table_name();
		$this->report_columns = array(
			'tax_rate_id'  => "{$table_name}.tax_rate_id",
			'name'         => 'tax_rate_name as name',
			'tax_rate'     => 'tax_rate',
			'country'      => 'tax_rate_country as country',
			'state'        => 'tax_rate_state as state',
			'priority'     => 'tax_rate_priority as priority',
			'total_tax'    => 'SUM(total_tax) as total_tax',
			'order_tax'    => 'SUM(order_tax) as order_tax',
			'shipping_tax' => 'SUM(shipping_tax) as shipping_tax',
			'orders_count' => "COUNT( DISTINCT ( CASE WHEN total_tax >= 0 THEN {$table_name}.order_id END ) ) as orders_count",
		);
	}

	/**
	 * Set up all the hooks for maintaining and populating table data.
	 */
	public static function init() {
		add_action( 'woocommerce_analytics_delete_order_stats', array( __CLASS__, 'sync_on_order_delete' ), 15 );
	}

	/**
	 * Fills FROM clause of SQL request based on user supplied parameters.
	 *
	 * @param array  $query_args          Query arguments supplied by the user.
	 * @param string $order_status_filter Order status subquery.
	 */
	protected function add_from_sql_params( $query_args, $order_status_filter ) {
		global $wpdb;
		$table_name = self::get_db_table_name();

		if ( $order_status_filter ) {
			$this->subquery->add_sql_clause( 'join', "JOIN {$wpdb->prefix}wc_order_stats ON {$table_name}.order_id = {$wpdb->prefix}wc_order_stats.order_id" );
		}

		if ( isset( $query_args['taxes'] ) && ! empty( $query_args['taxes'] ) ) {
			$this->add_sql_clause( 'join', "JOIN {$wpdb->prefix}woocommerce_tax_rates ON default_results.tax_rate_id = {$wpdb->prefix}woocommerce_tax_rates.tax_rate_id" );
		} else {
			$this->subquery->add_sql_clause( 'join', "JOIN {$wpdb->prefix}woocommerce_tax_rates ON {$table_name}.tax_rate_id = {$wpdb->prefix}woocommerce_tax_rates.tax_rate_id" );
		}
	}

	/**
	 * Updates the database query with parameters used for Taxes report: categories and order status.
	 *
	 * @param array $query_args Query arguments supplied by the user.
	 */
	protected function add_sql_query_params( $query_args ) {
		global $wpdb;

		$order_tax_lookup_table = self::get_db_table_name();

		$this->add_time_period_sql_params( $query_args, $order_tax_lookup_table );
		$this->get_limit_sql_params( $query_args );
		$this->add_order_by_sql_params( $query_args );
		$order_status_filter = $this->get_status_subquery( $query_args );
		$this->add_from_sql_params( $query_args, $order_status_filter );

		if ( isset( $query_args['taxes'] ) && ! empty( $query_args['taxes'] ) ) {
			$allowed_taxes = self::get_filtered_ids( $query_args, 'taxes' );
			$this->subquery->add_sql_clause( 'where', "AND {$order_tax_lookup_table}.tax_rate_id IN ({$allowed_taxes})" );
		}

		if ( $order_status_filter ) {
			$this->subquery->add_sql_clause( 'where', "AND ( {$order_status_filter} )" );
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
			'per_page' => get_option( 'posts_per_page' ),
			'page'     => 1,
			'order'    => 'DESC',
			'orderby'  => 'tax_rate_id',
			'before'   => TimeInterval::default_before(),
			'after'    => TimeInterval::default_after(),
			'fields'   => '*',
			'taxes'    => array(),
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

			$this->add_sql_query_params( $query_args );
			$params = $this->get_limit_params( $query_args );

			if ( isset( $query_args['taxes'] ) && ! empty( $query_args['taxes'] ) ) {
				$total_results = count( $query_args['taxes'] );
				$total_pages   = (int) ceil( $total_results / $params['per_page'] );

				$inner_selections = array( 'tax_rate_id', 'total_tax', 'order_tax', 'shipping_tax', 'orders_count' );
				$outer_selections = array( 'name', 'tax_rate', 'country', 'state', 'priority' );

				$selections      = $this->selected_columns( array( 'fields' => $inner_selections ) );
				$fields          = $this->get_fields( $query_args );
				$join_selections = $this->format_join_selections( $fields, array( 'tax_rate_id' ), $outer_selections );
				$ids_table       = $this->get_ids_table( $query_args['taxes'], 'tax_rate_id' );

				$this->subquery->clear_sql_clause( 'select' );
				$this->subquery->add_sql_clause( 'select', $this->selected_columns( array( 'fields' => $inner_selections ) ) );
				$this->add_sql_clause( 'select', $join_selections );
				$this->add_sql_clause( 'from', '(' );
				$this->add_sql_clause( 'from', $this->subquery->get_query_statement() );
				$this->add_sql_clause( 'from', ") AS {$table_name}" );
				$this->add_sql_clause(
					'right_join',
					"RIGHT JOIN ( {$ids_table} ) AS default_results
					ON default_results.tax_rate_id = {$table_name}.tax_rate_id"
				);

				$taxes_query = $this->get_query_statement();
			} else {
				$db_records_count = (int) $wpdb->get_var(
					"SELECT COUNT(*) FROM (
						{$this->subquery->get_query_statement()}
					) AS tt"
				); // WPCS: cache ok, DB call ok, unprepared SQL ok.

				$total_results = $db_records_count;
				$total_pages   = (int) ceil( $db_records_count / $params['per_page'] );

				if ( $query_args['page'] < 1 || $query_args['page'] > $total_pages ) {
					return $data;
				}

				$this->subquery->clear_sql_clause( 'select' );
				$this->subquery->add_sql_clause( 'select', $this->selected_columns( $query_args ) );
				$this->subquery->add_sql_clause( 'order_by', $this->get_sql_clause( 'order_by' ) );
				$taxes_query = $this->subquery->get_query_statement();
			}

			$tax_data = $wpdb->get_results(
				$taxes_query,
				ARRAY_A
			); // WPCS: cache ok, DB call ok, unprepared SQL ok.

			if ( null === $tax_data ) {
				return $data;
			}

			$tax_data = array_map( array( $this, 'cast_numbers' ), $tax_data );
			$data     = (object) array(
				'data'    => $tax_data,
				'total'   => $total_results,
				'pages'   => $total_pages,
				'page_no' => (int) $query_args['page'],
			);

			$this->set_cached_data( $cache_key, $data );
		}

		return $data;
	}

	/**
	 * Maps ordering specified by the user to columns in the database/fields in the data.
	 *
	 * @param string $order_by Sorting criterion.
	 * @return string
	 */
	protected function normalize_order_by( $order_by ) {
		global $wpdb;

		if ( 'tax_code' === $order_by ) {
			return 'CONCAT_WS( "-", NULLIF(tax_rate_country, ""), NULLIF(tax_rate_state, ""), NULLIF(tax_rate_name, ""), NULLIF(tax_rate_priority, "") )';
		} elseif ( 'rate' === $order_by ) {
			return "CAST({$wpdb->prefix}woocommerce_tax_rates.tax_rate as DECIMAL(7,4))";
		}

		return $order_by;
	}

	/**
	 * Create or update an entry in the wc_order_tax_lookup table for an order.
	 *
	 * @param int $order_id Order ID.
	 * @return int|bool Returns -1 if order won't be processed, or a boolean indicating processing success.
	 */
	public static function sync_order_taxes( $order_id ) {
		global $wpdb;

		$order = wc_get_order( $order_id );
		if ( ! $order ) {
			return -1;
		}

		$tax_items   = $order->get_items( 'tax' );
		$num_updated = 0;

		foreach ( $tax_items as $tax_item ) {
			$result = $wpdb->replace(
				self::get_db_table_name(),
				array(
					'order_id'     => $order->get_id(),
					'date_created' => $order->get_date_created( 'edit' )->date( TimeInterval::$sql_datetime_format ),
					'tax_rate_id'  => $tax_item->get_rate_id(),
					'shipping_tax' => $tax_item->get_shipping_tax_total(),
					'order_tax'    => $tax_item->get_tax_total(),
					'total_tax'    => (float) $tax_item->get_tax_total() + (float) $tax_item->get_shipping_tax_total(),
				),
				array(
					'%d',
					'%s',
					'%d',
					'%f',
					'%f',
					'%f',
				)
			);

			/**
			 * Fires when tax's reports are updated.
			 *
			 * @param int $tax_rate_id Tax Rate ID.
			 * @param int $order_id    Order ID.
			 */
			do_action( 'woocommerce_analytics_update_tax', $tax_item->get_rate_id(), $order->get_id() );

			// Sum the rows affected. Using REPLACE can affect 2 rows if the row already exists.
			$num_updated += 2 === intval( $result ) ? 1 : intval( $result );
		}

		return ( count( $tax_items ) === $num_updated );
	}

	/**
	 * Clean taxes data when an order is deleted.
	 *
	 * @param int $order_id Order ID.
	 */
	public static function sync_on_order_delete( $order_id ) {
		global $wpdb;

		$wpdb->delete( self::get_db_table_name(), array( 'order_id' => $order_id ) );

		/**
		 * Fires when tax's reports are removed from database.
		 *
		 * @param int $tax_rate_id Tax Rate ID.
		 * @param int $order_id    Order ID.
		 */
		do_action( 'woocommerce_analytics_delete_tax', 0, $order_id );

		ReportsCache::invalidate();
	}

	/**
	 * Initialize query objects.
	 */
	protected function initialize_queries() {
		$this->clear_all_clauses();
		$this->subquery = new SqlQuery( $this->context . '_subquery' );
		$this->subquery->add_sql_clause( 'select', self::get_db_table_name() . '.tax_rate_id' );
		$this->subquery->add_sql_clause( 'from', self::get_db_table_name() );
		$this->subquery->add_sql_clause( 'group_by', self::get_db_table_name() . '.tax_rate_id' );
	}
}
