<?php
/**
 * API\Reports\Downloads\DataStore class file.
 */

namespace Automattic\WooCommerce\Admin\API\Reports\Downloads;

defined( 'ABSPATH' ) || exit;

use Automattic\WooCommerce\Admin\API\Reports\DataStore as ReportsDataStore;
use Automattic\WooCommerce\Admin\API\Reports\DataStoreInterface;
use Automattic\WooCommerce\Admin\API\Reports\TimeInterval;
use Automattic\WooCommerce\Admin\API\Reports\SqlQuery;

/**
 * API\Reports\Downloads\DataStore.
 */
class DataStore extends ReportsDataStore implements DataStoreInterface {

	/**
	 * Table used to get the data.
	 *
	 * @var string
	 */
	protected static $table_name = 'wc_download_log';

	/**
	 * Cache identifier.
	 *
	 * @var string
	 */
	protected $cache_key = 'downloads';

	/**
	 * Mapping columns to data type to return correct response types.
	 *
	 * @var array
	 */
	protected $column_types = array(
		'id'          => 'intval',
		'date'        => 'strval',
		'date_gmt'    => 'strval',
		'download_id' => 'strval', // String because this can sometimes be a hash.
		'file_name'   => 'strval',
		'product_id'  => 'intval',
		'order_id'    => 'intval',
		'user_id'     => 'intval',
		'ip_address'  => 'strval',
	);

	/**
	 * Data store context used to pass to filters.
	 *
	 * @var string
	 */
	protected $context = 'downloads';

	/**
	 * Assign report columns once full table name has been assigned.
	 */
	protected function assign_report_columns() {
		$this->report_columns = array(
			'id'          => 'download_log_id as id',
			'date'        => 'timestamp as date_gmt',
			'download_id' => 'product_permissions.download_id',
			'product_id'  => 'product_permissions.product_id',
			'order_id'    => 'product_permissions.order_id',
			'user_id'     => 'product_permissions.user_id',
			'ip_address'  => 'user_ip_address as ip_address',
		);
	}

	/**
	 * Updates the database query with parameters used for downloads report.
	 *
	 * @param array $query_args Query arguments supplied by the user.
	 */
	protected function add_sql_query_params( $query_args ) {
		global $wpdb;

		$lookup_table     = self::get_db_table_name();
		$permission_table = $wpdb->prefix . 'woocommerce_downloadable_product_permissions';
		$operator         = $this->get_match_operator( $query_args );
		$where_filters    = array();
		$join             = "JOIN {$permission_table} as product_permissions ON {$lookup_table}.permission_id = product_permissions.permission_id";

		$where_time = $this->add_time_period_sql_params( $query_args, $lookup_table );
		if ( $where_time ) {
			if ( isset( $this->subquery ) ) {
				$this->subquery->add_sql_clause( 'where_time', $where_time );
			} else {
				$this->interval_query->add_sql_clause( 'where_time', $where_time );
			}
		}
		$this->get_limit_sql_params( $query_args );

		$where_filters[] = $this->get_object_where_filter(
			$lookup_table,
			'permission_id',
			$permission_table,
			'product_id',
			'IN',
			$this->get_included_products( $query_args )
		);
		$where_filters[] = $this->get_object_where_filter(
			$lookup_table,
			'permission_id',
			$permission_table,
			'product_id',
			'NOT IN',
			$this->get_excluded_products( $query_args )
		);
		$where_filters[] = $this->get_object_where_filter(
			$lookup_table,
			'permission_id',
			$permission_table,
			'order_id',
			'IN',
			$this->get_included_orders( $query_args )
		);
		$where_filters[] = $this->get_object_where_filter(
			$lookup_table,
			'permission_id',
			$permission_table,
			'order_id',
			'NOT IN',
			$this->get_excluded_orders( $query_args )
		);

		$customer_lookup_table = $wpdb->prefix . 'wc_customer_lookup';
		$customer_lookup       = "SELECT {$customer_lookup_table}.user_id FROM {$customer_lookup_table} WHERE {$customer_lookup_table}.customer_id IN (%s)";
		$included_customers    = $this->get_included_customers( $query_args );
		$excluded_customers    = $this->get_excluded_customers( $query_args );
		if ( $included_customers ) {
			$where_filters[] = $this->get_object_where_filter(
				$lookup_table,
				'permission_id',
				$permission_table,
				'user_id',
				'IN',
				sprintf( $customer_lookup, $included_customers )
			);
		}

		if ( $excluded_customers ) {
			$where_filters[] = $this->get_object_where_filter(
				$lookup_table,
				'permission_id',
				$permission_table,
				'user_id',
				'NOT IN',
				sprintf( $customer_lookup, $excluded_customers )
			);
		}

		$included_ip_addresses = $this->get_included_ip_addresses( $query_args );
		$excluded_ip_addresses = $this->get_excluded_ip_addresses( $query_args );
		if ( $included_ip_addresses ) {
			$where_filters[] = "{$lookup_table}.user_ip_address IN ('{$included_ip_addresses}')";
		}

		if ( $excluded_ip_addresses ) {
			$where_filters[] = "{$lookup_table}.user_ip_address NOT IN ('{$excluded_ip_addresses}')";
		}

		$where_filters   = array_filter( $where_filters );
		$where_subclause = implode( " $operator ", $where_filters );
		if ( $where_subclause ) {
			if ( isset( $this->subquery ) ) {
				$this->subquery->add_sql_clause( 'where', "AND ( $where_subclause )" );
			} else {
				$this->interval_query->add_sql_clause( 'where', "AND ( $where_subclause )" );
			}
		}

		if ( isset( $this->subquery ) ) {
			$this->subquery->add_sql_clause( 'join', $join );
		} else {
			$this->interval_query->add_sql_clause( 'join', $join );
		}
		$this->add_order_by( $query_args );
	}

	/**
	 * Returns comma separated ids of included ip address, based on query arguments from the user.
	 *
	 * @param array $query_args Parameters supplied by the user.
	 * @return string
	 */
	protected function get_included_ip_addresses( $query_args ) {
		return $this->get_filtered_ip_addresses( $query_args, 'ip_address_includes' );
	}

	/**
	 * Returns comma separated ids of excluded ip address, based on query arguments from the user.
	 *
	 * @param array $query_args Parameters supplied by the user.
	 * @return string
	 */
	protected function get_excluded_ip_addresses( $query_args ) {
		return $this->get_filtered_ip_addresses( $query_args, 'ip_address_excludes' );
	}

	/**
	 * Returns filtered comma separated ids, based on query arguments from the user.
	 *
	 * @param array  $query_args  Parameters supplied by the user.
	 * @param string $field       Query field to filter.
	 * @return string
	 */
	protected function get_filtered_ip_addresses( $query_args, $field ) {
		if ( isset( $query_args[ $field ] ) && is_array( $query_args[ $field ] ) && count( $query_args[ $field ] ) > 0 ) {
			$ip_addresses = array_map( 'esc_sql', $query_args[ $field ] );

			/**
			 * Filter the IDs before retrieving report data.
			 *
			 * Allows filtering of the objects included or excluded from reports.
			 *
			 * @param array  $ids        List of object Ids.
			 * @param array  $query_args The original arguments for the request.
			 * @param string $field      The object type.
			 * @param string $context    The data store context.
			 */
			$ip_addresses = apply_filters( 'woocommerce_analytics_' . $field, $ip_addresses, $query_args, $field, $this->context );

			return implode( "','", $ip_addresses );
		}
		return '';
	}

	/**
	 * Returns comma separated ids of included customers, based on query arguments from the user.
	 *
	 * @param array $query_args Parameters supplied by the user.
	 * @return string
	 */
	protected function get_included_customers( $query_args ) {
		return self::get_filtered_ids( $query_args, 'customer_includes' );
	}

	/**
	 * Returns comma separated ids of excluded customers, based on query arguments from the user.
	 *
	 * @param array $query_args Parameters supplied by the user.
	 * @return string
	 */
	protected function get_excluded_customers( $query_args ) {
		return self::get_filtered_ids( $query_args, 'customer_excludes' );
	}

	/**
	 * Gets WHERE time clause of SQL request with date-related constraints.
	 *
	 * @param array  $query_args Parameters supplied by the user.
	 * @param string $table_name Name of the db table relevant for the date constraint.
	 * @return string
	 */
	protected function add_time_period_sql_params( $query_args, $table_name ) {
		$where_time = '';
		if ( $query_args['before'] ) {
			$datetime_str = $query_args['before']->format( TimeInterval::$sql_datetime_format );
			$where_time  .= " AND {$table_name}.timestamp <= '$datetime_str'";

		}

		if ( $query_args['after'] ) {
			$datetime_str = $query_args['after']->format( TimeInterval::$sql_datetime_format );
			$where_time  .= " AND {$table_name}.timestamp >= '$datetime_str'";
		}

		return $where_time;
	}

	/**
	 * Fills ORDER BY clause of SQL request based on user supplied parameters.
	 *
	 * @param array $query_args Parameters supplied by the user.
	 */
	protected function add_order_by( $query_args ) {
		global $wpdb;
		$this->clear_sql_clause( 'order_by' );
		$order_by = '';
		if ( isset( $query_args['orderby'] ) ) {
			$order_by = $this->normalize_order_by( esc_sql( $query_args['orderby'] ) );
			$this->add_sql_clause( 'order_by', $order_by );
		}

		if ( false !== strpos( $order_by, '_products' ) ) {
			$this->subquery->add_sql_clause( 'join', "JOIN {$wpdb->posts} AS _products ON product_permissions.product_id = _products.ID" );
		}

		$this->add_orderby_order_clause( $query_args, $this );
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
			'orderby'  => 'timestamp',
			'before'   => TimeInterval::default_before(),
			'after'    => TimeInterval::default_after(),
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
				'data'    => array(),
				'total'   => 0,
				'pages'   => 0,
				'page_no' => 0,
			);

			$selections = $this->selected_columns( $query_args );
			$this->add_sql_query_params( $query_args );

			// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$db_records_count = (int) $wpdb->get_var(
				"SELECT COUNT(*) FROM (
					{$this->subquery->get_query_statement()}
				) AS tt"
			);
			// phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared

			$params      = $this->get_limit_params( $query_args );
			$total_pages = (int) ceil( $db_records_count / $params['per_page'] );
			if ( $query_args['page'] < 1 || $query_args['page'] > $total_pages ) {
				return $data;
			}

			$this->subquery->clear_sql_clause( 'select' );
			$this->subquery->add_sql_clause( 'select', $selections );
			$this->subquery->add_sql_clause( 'order_by', $this->get_sql_clause( 'order_by' ) );
			$this->subquery->add_sql_clause( 'limit', $this->get_sql_clause( 'limit' ) );

			$download_data = $wpdb->get_results(
				$this->subquery->get_query_statement(), // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
				ARRAY_A
			);

			if ( null === $download_data ) {
				return $data;
			}

			$download_data = array_map( array( $this, 'cast_numbers' ), $download_data );
			$data          = (object) array(
				'data'    => $download_data,
				'total'   => $db_records_count,
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

		if ( 'date' === $order_by ) {
			return $wpdb->prefix . 'wc_download_log.timestamp';
		}

		if ( 'product' === $order_by ) {
			return '_products.post_title';
		}

		return $order_by;
	}

	/**
	 * Initialize query objects.
	 */
	protected function initialize_queries() {
		$this->clear_all_clauses();
		$table_name     = self::get_db_table_name();
		$this->subquery = new SqlQuery( $this->context . '_subquery' );
		$this->subquery->add_sql_clause( 'from', $table_name );
		$this->subquery->add_sql_clause( 'select', "{$table_name}.download_log_id" );
		$this->subquery->add_sql_clause( 'group_by', "{$table_name}.download_log_id" );
	}
}
