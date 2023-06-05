<?php
/**
 * API\Reports\Customers\Stats\DataStore class file.
 */

namespace Automattic\WooCommerce\Admin\API\Reports\Customers\Stats;

defined( 'ABSPATH' ) || exit;

use Automattic\WooCommerce\Admin\API\Reports\Customers\DataStore as CustomersDataStore;
use Automattic\WooCommerce\Admin\API\Reports\DataStoreInterface;

/**
 * API\Reports\Customers\Stats\DataStore.
 */
class DataStore extends CustomersDataStore implements DataStoreInterface {
	/**
	 * Mapping columns to data type to return correct response types.
	 *
	 * @var array
	 */
	protected $column_types = array(
		'customers_count'     => 'intval',
		'avg_orders_count'    => 'floatval',
		'avg_total_spend'     => 'floatval',
		'avg_avg_order_value' => 'floatval',
	);

	/**
	 * Cache identifier.
	 *
	 * @var string
	 */
	protected $cache_key = 'customers_stats';

	/**
	 * Data store context used to pass to filters.
	 *
	 * @var string
	 */
	protected $context = 'customers_stats';

	/**
	 * Assign report columns once full table name has been assigned.
	 */
	protected function assign_report_columns() {
		$this->report_columns = array(
			'customers_count'     => 'COUNT( * ) as customers_count',
			'avg_orders_count'    => 'AVG( orders_count ) as avg_orders_count',
			'avg_total_spend'     => 'AVG( total_spend ) as avg_total_spend',
			'avg_avg_order_value' => 'AVG( avg_order_value ) as avg_avg_order_value',
		);
	}

	/**
	 * Returns the report data based on parameters supplied by the user.
	 *
	 * @param array $query_args  Query parameters.
	 * @return stdClass|WP_Error Data.
	 */
	public function get_data( $query_args ) {
		global $wpdb;

		$customers_table_name = self::get_db_table_name();

		// These defaults are only partially applied when used via REST API, as that has its own defaults.
		$defaults   = array(
			'per_page' => get_option( 'posts_per_page' ),
			'page'     => 1,
			'order'    => 'DESC',
			'orderby'  => 'date_registered',
			'fields'   => '*',
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
				'customers_count'     => 0,
				'avg_orders_count'    => 0,
				'avg_total_spend'     => 0.0,
				'avg_avg_order_value' => 0.0,
			);

			$selections = $this->selected_columns( $query_args );
			$this->add_sql_query_params( $query_args );
			// Clear SQL clauses set for parent class queries that are different here.
			$this->subquery->clear_sql_clause( 'select' );
			$this->subquery->add_sql_clause( 'select', 'SUM( total_sales ) AS total_spend,' );
			$this->subquery->add_sql_clause(
				'select',
				'SUM( CASE WHEN parent_id = 0 THEN 1 END ) as orders_count,'
			);
			$this->subquery->add_sql_clause(
				'select',
				'CASE WHEN SUM( CASE WHEN parent_id = 0 THEN 1 ELSE 0 END ) = 0 THEN NULL ELSE SUM( total_sales ) / SUM( CASE WHEN parent_id = 0 THEN 1 ELSE 0 END ) END AS avg_order_value'
			);

			$this->clear_sql_clause( array( 'order_by', 'limit' ) );
			$this->add_sql_clause( 'select', $selections );
			$this->add_sql_clause( 'from', "({$this->subquery->get_query_statement()}) AS tt" );

			$report_data = $wpdb->get_results(
				$this->get_query_statement(), // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
				ARRAY_A
			);

			if ( null === $report_data ) {
				return $data;
			}

			$data = (object) $this->cast_numbers( $report_data[0] );

			$this->set_cached_data( $cache_key, $data );
		}

		return $data;
	}
}
