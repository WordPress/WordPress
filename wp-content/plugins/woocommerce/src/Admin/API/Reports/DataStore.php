<?php
/**
 * Admin\API\Reports\DataStore class file.
 */

namespace Automattic\WooCommerce\Admin\API\Reports;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Automattic\WooCommerce\Admin\API\Reports\TimeInterval;

/**
 * Admin\API\Reports\DataStore: Common parent for custom report data stores.
 */
class DataStore extends SqlQuery {

	/**
	 * Cache group for the reports.
	 *
	 * @var string
	 */
	protected $cache_group = 'reports';

	/**
	 * Time out for the cache.
	 *
	 * @var int
	 */
	protected $cache_timeout = 3600;

	/**
	 * Table used as a data store for this report.
	 *
	 * @var string
	 */
	protected static $table_name = '';

	/**
	 * Date field name.
	 *
	 * @var string
	 */
	protected $date_column_name = 'date_created';

	/**
	 * Mapping columns to data type to return correct response types.
	 *
	 * @var array
	 */
	protected $column_types = array();

	// @todo This does not really belong here, maybe factor out the comparison as separate class?
	/**
	 * Order by property, used in the cmp function.
	 *
	 * @var string
	 */
	private $order_by = '';

	/**
	 * Order property, used in the cmp function.
	 *
	 * @var string
	 */
	private $order = '';

	/**
	 * Query limit parameters.
	 *
	 * @var array
	 */
	private $limit_parameters = array();

	/**
	 * Data store context used to pass to filters.
	 *
	 * @var string
	 */
	protected $context = 'reports';

	/**
	 * Subquery object for query nesting.
	 *
	 * @var SqlQuery
	 */
	protected $subquery;

	/**
	 * Totals query object.
	 *
	 * @var SqlQuery
	 */
	protected $total_query;

	/**
	 * Intervals query object.
	 *
	 * @var SqlQuery
	 */
	protected $interval_query;

	/**
	 * Refresh the cache for the current query when true.
	 *
	 * @var bool
	 */
	protected $force_cache_refresh = false;

	/**
	 * Include debugging information in the returned data when true.
	 *
	 * @var bool
	 */
	protected $debug_cache = true;

	/**
	 * Debugging information to include in the returned data.
	 *
	 * @var array
	 */
	protected $debug_cache_data = array();

	/**
	 * Class constructor.
	 */
	public function __construct() {
		self::set_db_table_name();
		$this->assign_report_columns();

		if ( property_exists( $this, 'report_columns' ) ) {
			$this->report_columns = apply_filters(
				'woocommerce_admin_report_columns',
				$this->report_columns,
				$this->context,
				self::get_db_table_name()
			);
		}

		// Utilize enveloped responses to include debugging info.
		// See https://querymonitor.com/blog/2021/05/debugging-wordpress-rest-api-requests/
		if ( isset( $_GET['_envelope'] ) ) {
			$this->debug_cache = true;
			add_filter( 'rest_envelope_response', array( $this, 'add_debug_cache_to_envelope' ), 999, 2 );
		}
	}

	/**
	 * Get table name from database class.
	 */
	public static function get_db_table_name() {
		global $wpdb;
		return isset( $wpdb->{static::$table_name} ) ? $wpdb->{static::$table_name} : $wpdb->prefix . static::$table_name;
	}

	/**
	 * Set table name from database class.
	 */
	protected static function set_db_table_name() {
		global $wpdb;
		if ( static::$table_name && ! isset( $wpdb->{static::$table_name} ) ) {
			$wpdb->{static::$table_name} = $wpdb->prefix . static::$table_name;
		}
	}

	/**
	 * Whether or not the report should use the caching layer.
	 *
	 * Provides an opportunity for plugins to prevent reports from using cache.
	 *
	 * @return boolean Whether or not to utilize caching.
	 */
	protected function should_use_cache() {
		/**
		 * Determines if a report will utilize caching.
		 *
		 * @param bool $use_cache Whether or not to use cache.
		 * @param string $cache_key The report's cache key. Used to identify the report.
		 */
		return (bool) apply_filters( 'woocommerce_analytics_report_should_use_cache', true, $this->cache_key );
	}

	/**
	 * Returns string to be used as cache key for the data.
	 *
	 * @param array $params Query parameters.
	 * @return string
	 */
	protected function get_cache_key( $params ) {
		if ( isset( $params['force_cache_refresh'] ) ) {
			if ( true === $params['force_cache_refresh'] ) {
				$this->force_cache_refresh = true;
			}

			// We don't want this param in the key.
			unset( $params['force_cache_refresh'] );
		}

		if ( true === $this->debug_cache ) {
			$this->debug_cache_data['query_args'] = $params;
		}

		return implode(
			'_',
			array(
				'wc_report',
				$this->cache_key,
				md5( wp_json_encode( $params ) ),
			)
		);
	}

	/**
	 * Wrapper around Cache::get().
	 *
	 * @param string $cache_key Cache key.
	 * @return mixed
	 */
	protected function get_cached_data( $cache_key ) {
		if ( true === $this->debug_cache ) {
			$this->debug_cache_data['should_use_cache']    = $this->should_use_cache();
			$this->debug_cache_data['force_cache_refresh'] = $this->force_cache_refresh;
			$this->debug_cache_data['cache_hit']           = false;
		}

		if ( $this->should_use_cache() && false === $this->force_cache_refresh ) {
			$cached_data = Cache::get( $cache_key );

			$cache_hit = false !== $cached_data;
			if ( true === $this->debug_cache ) {
				$this->debug_cache_data['cache_hit'] = $cache_hit;
			}

			return $cached_data;
		}

		// Cached item has now functionally been refreshed. Reset the option.
		$this->force_cache_refresh = false;

		return false;
	}

	/**
	 * Wrapper around Cache::set().
	 *
	 * @param string $cache_key Cache key.
	 * @param mixed  $value     New value.
	 * @return bool
	 */
	protected function set_cached_data( $cache_key, $value ) {
		if ( $this->should_use_cache() ) {
			return Cache::set( $cache_key, $value );
		}

		return true;
	}

	/**
	 * Add cache debugging information to an enveloped API response.
	 *
	 * @param array             $envelope
	 * @param \WP_REST_Response $response
	 *
	 * @return array
	 */
	public function add_debug_cache_to_envelope( $envelope, $response ) {
		if ( 0 !== strncmp( '/wc-analytics', $response->get_matched_route(), 13 ) ) {
			return $envelope;
		}

		if ( ! empty( $this->debug_cache_data ) ) {
			$envelope['debug_cache'] = $this->debug_cache_data;
		}

		return $envelope;
	}

	/**
	 * Compares two report data objects by pre-defined object property and ASC/DESC ordering.
	 *
	 * @param stdClass $a Object a.
	 * @param stdClass $b Object b.
	 * @return string
	 */
	private function interval_cmp( $a, $b ) {
		if ( '' === $this->order_by || '' === $this->order ) {
			return 0;
			// @todo Should return WP_Error here perhaps?
		}
		if ( $a[ $this->order_by ] === $b[ $this->order_by ] ) {
			// As relative order is undefined in case of equality in usort, second-level sorting by date needs to be enforced
			// so that paging is stable.
			if ( $a['time_interval'] === $b['time_interval'] ) {
				return 0; // This should never happen.
			} elseif ( $a['time_interval'] > $b['time_interval'] ) {
				return 1;
			} elseif ( $a['time_interval'] < $b['time_interval'] ) {
				return -1;
			}
		} elseif ( $a[ $this->order_by ] > $b[ $this->order_by ] ) {
			return strtolower( $this->order ) === 'desc' ? -1 : 1;
		} elseif ( $a[ $this->order_by ] < $b[ $this->order_by ] ) {
			return strtolower( $this->order ) === 'desc' ? 1 : -1;
		}
	}

	/**
	 * Sorts intervals according to user's request.
	 *
	 * They are pre-sorted in SQL, but after adding gaps, they need to be sorted including the added ones.
	 *
	 * @param stdClass $data      Data object, must contain an array under $data->intervals.
	 * @param string   $sort_by   Ordering property.
	 * @param string   $direction DESC/ASC.
	 */
	protected function sort_intervals( &$data, $sort_by, $direction ) {
		$this->sort_array( $data->intervals, $sort_by, $direction );
	}

	/**
	 * Sorts array of arrays based on subarray key $sort_by.
	 *
	 * @param array  $arr       Array to sort.
	 * @param string $sort_by   Ordering property.
	 * @param string $direction DESC/ASC.
	 */
	protected function sort_array( &$arr, $sort_by, $direction ) {
		$this->order_by = $this->normalize_order_by( $sort_by );
		$this->order    = $direction;
		usort( $arr, array( $this, 'interval_cmp' ) );
	}

	/**
	 * Fills in interval gaps from DB with 0-filled objects.
	 *
	 * @param array    $db_intervals   Array of all intervals present in the db.
	 * @param DateTime $start_datetime Start date.
	 * @param DateTime $end_datetime   End date.
	 * @param string   $time_interval  Time interval, e.g. day, week, month.
	 * @param stdClass $data           Data with SQL extracted intervals.
	 * @return stdClass
	 */
	protected function fill_in_missing_intervals( $db_intervals, $start_datetime, $end_datetime, $time_interval, &$data ) {
		// @todo This is ugly and messy.
		$local_tz = new \DateTimeZone( wc_timezone_string() );
		// At this point, we don't know when we can stop iterating, as the ordering can be based on any value.
		$time_ids     = array_flip( wp_list_pluck( $data->intervals, 'time_interval' ) );
		$db_intervals = array_flip( $db_intervals );
		// Totals object used to get all needed properties.
		$totals_arr = get_object_vars( $data->totals );
		foreach ( $totals_arr as $key => $val ) {
			$totals_arr[ $key ] = 0;
		}
		// @todo Should 'products' be in intervals?
		unset( $totals_arr['products'] );
		while ( $start_datetime <= $end_datetime ) {
			$next_start = TimeInterval::iterate( $start_datetime, $time_interval );
			$time_id    = TimeInterval::time_interval_id( $time_interval, $start_datetime );
			// Either create fill-zero interval or use data from db.
			if ( $next_start > $end_datetime ) {
				$interval_end = $end_datetime->format( 'Y-m-d H:i:s' );
			} else {
				$prev_end_timestamp = (int) $next_start->format( 'U' ) - 1;
				$prev_end           = new \DateTime();
				$prev_end->setTimestamp( $prev_end_timestamp );
				$prev_end->setTimezone( $local_tz );
				$interval_end = $prev_end->format( 'Y-m-d H:i:s' );
			}
			if ( array_key_exists( $time_id, $time_ids ) ) {
				// For interval present in the db for this time frame, just fill in dates.
				$record               = &$data->intervals[ $time_ids[ $time_id ] ];
				$record['date_start'] = $start_datetime->format( 'Y-m-d H:i:s' );
				$record['date_end']   = $interval_end;
			} elseif ( ! array_key_exists( $time_id, $db_intervals ) ) {
				// For intervals present in the db outside of this time frame, do nothing.
				// For intervals not present in the db, fabricate it.
				$record_arr                  = array();
				$record_arr['time_interval'] = $time_id;
				$record_arr['date_start']    = $start_datetime->format( 'Y-m-d H:i:s' );
				$record_arr['date_end']      = $interval_end;
				$data->intervals[]           = array_merge( $record_arr, $totals_arr );
			}
			$start_datetime = $next_start;
		}
		return $data;
	}

	/**
	 * Converts input datetime parameters to local timezone. If there are no inputs from the user in query_args,
	 * uses default from $defaults.
	 *
	 * @param array $query_args Array of query arguments.
	 * @param array $defaults Array of default values.
	 */
	protected function normalize_timezones( &$query_args, $defaults ) {
		$local_tz = new \DateTimeZone( wc_timezone_string() );
		foreach ( array( 'before', 'after' ) as $query_arg_key ) {
			if ( isset( $query_args[ $query_arg_key ] ) && is_string( $query_args[ $query_arg_key ] ) ) {
				// Assume that unspecified timezone is a local timezone.
				$datetime = new \DateTime( $query_args[ $query_arg_key ], $local_tz );
				// In case timezone was forced by using +HH:MM, convert to local timezone.
				$datetime->setTimezone( $local_tz );
				$query_args[ $query_arg_key ] = $datetime;
			} elseif ( isset( $query_args[ $query_arg_key ] ) && is_a( $query_args[ $query_arg_key ], 'DateTime' ) ) {
				// In case timezone is in other timezone, convert to local timezone.
				$query_args[ $query_arg_key ]->setTimezone( $local_tz );
			} else {
				$query_args[ $query_arg_key ] = isset( $defaults[ $query_arg_key ] ) ? $defaults[ $query_arg_key ] : null;
			}
		}
	}

	/**
	 * Removes extra records from intervals so that only requested number of records get returned.
	 *
	 * @param stdClass $data           Data from whose intervals the records get removed.
	 * @param int      $page_no        Offset requested by the user.
	 * @param int      $items_per_page Number of records requested by the user.
	 * @param int      $db_interval_count Database interval count.
	 * @param int      $expected_interval_count Expected interval count on the output.
	 * @param string   $order_by Order by field.
	 * @param string   $order ASC or DESC.
	 */
	protected function remove_extra_records( &$data, $page_no, $items_per_page, $db_interval_count, $expected_interval_count, $order_by, $order ) {
		if ( 'date' === strtolower( $order_by ) ) {
			$offset = 0;
		} else {
			if ( 'asc' === strtolower( $order ) ) {
				$offset = ( $page_no - 1 ) * $items_per_page;
			} else {
				$offset = ( $page_no - 1 ) * $items_per_page - $db_interval_count;
			}
			$offset = $offset < 0 ? 0 : $offset;
		}
		$count = $expected_interval_count - ( $page_no - 1 ) * $items_per_page;
		if ( $count < 0 ) {
			$count = 0;
		} elseif ( $count > $items_per_page ) {
			$count = $items_per_page;
		}
		$data->intervals = array_slice( $data->intervals, $offset, $count );
	}

	/**
	 * Returns expected number of items on the page in case of date ordering.
	 *
	 * @param int $expected_interval_count Expected number of intervals in total.
	 * @param int $items_per_page          Number of items per page.
	 * @param int $page_no                 Page number.
	 *
	 * @return float|int
	 */
	protected function expected_intervals_on_page( $expected_interval_count, $items_per_page, $page_no ) {
		$total_pages = (int) ceil( $expected_interval_count / $items_per_page );
		if ( $page_no < $total_pages ) {
			return $items_per_page;
		} elseif ( $page_no === $total_pages ) {
			return $expected_interval_count - ( $page_no - 1 ) * $items_per_page;
		} else {
			return 0;
		}
	}

	/**
	 * Returns true if there are any intervals that need to be filled in the response.
	 *
	 * @param int    $expected_interval_count Expected number of intervals in total.
	 * @param int    $db_records              Total number of records for given period in the database.
	 * @param int    $items_per_page          Number of items per page.
	 * @param int    $page_no                 Page number.
	 * @param string $order                   asc or desc.
	 * @param string $order_by                Column by which the result will be sorted.
	 * @param int    $intervals_count         Number of records for given (possibly shortened) time interval.
	 *
	 * @return bool
	 */
	protected function intervals_missing( $expected_interval_count, $db_records, $items_per_page, $page_no, $order, $order_by, $intervals_count ) {
		if ( $expected_interval_count <= $db_records ) {
			return false;
		}
		if ( 'date' === $order_by ) {
			$expected_intervals_on_page = $this->expected_intervals_on_page( $expected_interval_count, $items_per_page, $page_no );
			return $intervals_count < $expected_intervals_on_page;
		}
		if ( 'desc' === $order ) {
			return $page_no > floor( $db_records / $items_per_page );
		}
		if ( 'asc' === $order ) {
			return $page_no <= ceil( ( $expected_interval_count - $db_records ) / $items_per_page );
		}
		// Invalid ordering.
		return false;
	}

	/**
	 * Updates the LIMIT query part for Intervals query of the report.
	 *
	 * If there are less records in the database than time intervals, then we need to remap offset in SQL query
	 * to fetch correct records.
	 *
	 * @param array  $query_args Query arguments.
	 * @param int    $db_interval_count Database interval count.
	 * @param int    $expected_interval_count Expected interval count on the output.
	 * @param string $table_name Name of the db table relevant for the date constraint.
	 */
	protected function update_intervals_sql_params( &$query_args, $db_interval_count, $expected_interval_count, $table_name ) {
		if ( $db_interval_count === $expected_interval_count ) {
			return;
		}

		$params   = $this->get_limit_params( $query_args );
		$local_tz = new \DateTimeZone( wc_timezone_string() );
		if ( 'date' === strtolower( $query_args['orderby'] ) ) {
			// page X in request translates to slightly different dates in the db, in case some
			// records are missing from the db.
			$start_iteration = 0;
			$end_iteration   = 0;
			if ( 'asc' === strtolower( $query_args['order'] ) ) {
				// ORDER BY date ASC.
				$new_start_date    = $query_args['after'];
				$intervals_to_skip = ( $query_args['page'] - 1 ) * $params['per_page'];
				$latest_end_date   = $query_args['before'];
				for ( $i = 0; $i < $intervals_to_skip; $i++ ) {
					if ( $new_start_date > $latest_end_date ) {
						$new_start_date  = $latest_end_date;
						$start_iteration = 0;
						break;
					}
					$new_start_date = TimeInterval::iterate( $new_start_date, $query_args['interval'] );
					$start_iteration ++;
				}

				$new_end_date = clone $new_start_date;
				for ( $i = 0; $i < $params['per_page']; $i++ ) {
					if ( $new_end_date > $latest_end_date ) {
						break;
					}
					$new_end_date = TimeInterval::iterate( $new_end_date, $query_args['interval'] );
					$end_iteration ++;
				}
				if ( $new_end_date > $latest_end_date ) {
					$new_end_date  = $latest_end_date;
					$end_iteration = 0;
				}
				if ( $end_iteration ) {
					$new_end_date_timestamp = (int) $new_end_date->format( 'U' ) - 1;
					$new_end_date->setTimestamp( $new_end_date_timestamp );
				}
			} else {
				// ORDER BY date DESC.
				$new_end_date        = $query_args['before'];
				$intervals_to_skip   = ( $query_args['page'] - 1 ) * $params['per_page'];
				$earliest_start_date = $query_args['after'];
				for ( $i = 0; $i < $intervals_to_skip; $i++ ) {
					if ( $new_end_date < $earliest_start_date ) {
						$new_end_date  = $earliest_start_date;
						$end_iteration = 0;
						break;
					}
					$new_end_date = TimeInterval::iterate( $new_end_date, $query_args['interval'], true );
					$end_iteration ++;
				}

				$new_start_date = clone $new_end_date;
				for ( $i = 0; $i < $params['per_page']; $i++ ) {
					if ( $new_start_date < $earliest_start_date ) {
						break;
					}
					$new_start_date = TimeInterval::iterate( $new_start_date, $query_args['interval'], true );
					$start_iteration ++;
				}
				if ( $new_start_date < $earliest_start_date ) {
					$new_start_date  = $earliest_start_date;
					$start_iteration = 0;
				}
				if ( $start_iteration ) {
					// @todo Is this correct? should it only be added if iterate runs? other two iterate instances, too?
					$new_start_date_timestamp = (int) $new_start_date->format( 'U' ) + 1;
					$new_start_date->setTimestamp( $new_start_date_timestamp );
				}
			}
			// @todo - Do this without modifying $query_args?
			$query_args['adj_after']  = $new_start_date;
			$query_args['adj_before'] = $new_end_date;
			$adj_after                = $new_start_date->format( TimeInterval::$sql_datetime_format );
			$adj_before               = $new_end_date->format( TimeInterval::$sql_datetime_format );
			$this->interval_query->clear_sql_clause( array( 'where_time', 'limit' ) );
			$this->interval_query->add_sql_clause( 'where_time', "AND {$table_name}.`{$this->date_column_name}` <= '$adj_before'" );
			$this->interval_query->add_sql_clause( 'where_time', "AND {$table_name}.`{$this->date_column_name}` >= '$adj_after'" );
			$this->clear_sql_clause( 'limit' );
			$this->add_sql_clause( 'limit', 'LIMIT 0,' . $params['per_page'] );
		} else {
			if ( 'asc' === $query_args['order'] ) {
				$offset = ( ( $query_args['page'] - 1 ) * $params['per_page'] ) - ( $expected_interval_count - $db_interval_count );
				$offset = $offset < 0 ? 0 : $offset;
				$count  = $query_args['page'] * $params['per_page'] - ( $expected_interval_count - $db_interval_count );
				if ( $count < 0 ) {
					$count = 0;
				} elseif ( $count > $params['per_page'] ) {
					$count = $params['per_page'];
				}

				$this->clear_sql_clause( 'limit' );
				$this->add_sql_clause( 'limit', 'LIMIT ' . $offset . ',' . $count );
			}
			// Otherwise no change in limit clause.
			// @todo - Do this without modifying $query_args?
			$query_args['adj_after']  = $query_args['after'];
			$query_args['adj_before'] = $query_args['before'];
		}
	}

	/**
	 * Casts strings returned from the database to appropriate data types for output.
	 *
	 * @param array $array Associative array of values extracted from the database.
	 * @return array|WP_Error
	 */
	protected function cast_numbers( $array ) {
		$retyped_array = array();
		$column_types  = apply_filters( 'woocommerce_rest_reports_column_types', $this->column_types, $array );
		foreach ( $array as $column_name => $value ) {
			if ( is_array( $value ) ) {
				$value = $this->cast_numbers( $value );
			}

			if ( isset( $column_types[ $column_name ] ) ) {
				$retyped_array[ $column_name ] = $column_types[ $column_name ]( $value );
			} else {
				$retyped_array[ $column_name ] = $value;
			}
		}
		return $retyped_array;
	}

	/**
	 * Returns a list of columns selected by the query_args formatted as a comma separated string.
	 *
	 * @param array $query_args User-supplied options.
	 * @return string
	 */
	protected function selected_columns( $query_args ) {
		$selections = $this->report_columns;

		if ( isset( $query_args['fields'] ) && is_array( $query_args['fields'] ) ) {
			$keep = array();
			foreach ( $query_args['fields'] as $field ) {
				if ( isset( $selections[ $field ] ) ) {
					$keep[ $field ] = $selections[ $field ];
				}
			}
			$selections = implode( ', ', $keep );
		} else {
			$selections = implode( ', ', $selections );
		}
		return $selections;
	}

	/**
	 * Get the excluded order statuses used when calculating reports.
	 *
	 * @return array
	 */
	protected static function get_excluded_report_order_statuses() {
		$excluded_statuses = \WC_Admin_Settings::get_option( 'woocommerce_excluded_report_order_statuses', array( 'pending', 'failed', 'cancelled' ) );
		$excluded_statuses = array_merge( array( 'auto-draft', 'trash' ), array_map( 'esc_sql', $excluded_statuses ) );
		return apply_filters( 'woocommerce_analytics_excluded_order_statuses', $excluded_statuses );
	}

	/**
	 * Maps order status provided by the user to the one used in the database.
	 *
	 * @param string $status Order status.
	 * @return string
	 */
	protected static function normalize_order_status( $status ) {
		$status = trim( $status );
		return 'wc-' . $status;
	}

	/**
	 * Normalizes order_by clause to match to SQL query.
	 *
	 * @param string $order_by Order by option requested by user.
	 * @return string
	 */
	protected function normalize_order_by( $order_by ) {
		if ( 'date' === $order_by ) {
			return 'time_interval';
		}

		return $order_by;
	}

	/**
	 * Updates start and end dates for intervals so that they represent intervals' borders, not times when data in db were recorded.
	 *
	 * E.g. if there are db records for only Tuesday and Thursday this week, the actual week interval is [Mon, Sun], not [Tue, Thu].
	 *
	 * @param DateTime $start_datetime Start date.
	 * @param DateTime $end_datetime End date.
	 * @param string   $time_interval Time interval, e.g. day, week, month.
	 * @param array    $intervals Array of intervals extracted from SQL db.
	 */
	protected function update_interval_boundary_dates( $start_datetime, $end_datetime, $time_interval, &$intervals ) {
		$local_tz = new \DateTimeZone( wc_timezone_string() );
		foreach ( $intervals as $key => $interval ) {
			$datetime = new \DateTime( $interval['datetime_anchor'], $local_tz );

			$prev_start = TimeInterval::iterate( $datetime, $time_interval, true );
			// @todo Not sure if the +1/-1 here are correct, especially as they are applied before the ?: below.
			$prev_start_timestamp = (int) $prev_start->format( 'U' ) + 1;
			$prev_start->setTimestamp( $prev_start_timestamp );
			if ( $start_datetime ) {
				$date_start                      = $prev_start < $start_datetime ? $start_datetime : $prev_start;
				$intervals[ $key ]['date_start'] = $date_start->format( 'Y-m-d H:i:s' );
			} else {
				$intervals[ $key ]['date_start'] = $prev_start->format( 'Y-m-d H:i:s' );
			}

			$next_end           = TimeInterval::iterate( $datetime, $time_interval );
			$next_end_timestamp = (int) $next_end->format( 'U' ) - 1;
			$next_end->setTimestamp( $next_end_timestamp );
			if ( $end_datetime ) {
				$date_end                      = $next_end > $end_datetime ? $end_datetime : $next_end;
				$intervals[ $key ]['date_end'] = $date_end->format( 'Y-m-d H:i:s' );
			} else {
				$intervals[ $key ]['date_end'] = $next_end->format( 'Y-m-d H:i:s' );
			}

			$intervals[ $key ]['interval'] = $time_interval;
		}
	}

	/**
	 * Change structure of intervals to form a correct response.
	 *
	 * Also converts local datetimes to GMT and adds them to the intervals.
	 *
	 * @param array $intervals Time interval, e.g. day, week, month.
	 */
	protected function create_interval_subtotals( &$intervals ) {
		foreach ( $intervals as $key => $interval ) {
			$start_gmt = TimeInterval::convert_local_datetime_to_gmt( $interval['date_start'] );
			$end_gmt   = TimeInterval::convert_local_datetime_to_gmt( $interval['date_end'] );
			// Move intervals result to subtotals object.
			$intervals[ $key ] = array(
				'interval'       => $interval['time_interval'],
				'date_start'     => $interval['date_start'],
				'date_start_gmt' => $start_gmt->format( TimeInterval::$sql_datetime_format ),
				'date_end'       => $interval['date_end'],
				'date_end_gmt'   => $end_gmt->format( TimeInterval::$sql_datetime_format ),
			);

			unset( $interval['interval'] );
			unset( $interval['date_start'] );
			unset( $interval['date_end'] );
			unset( $interval['datetime_anchor'] );
			unset( $interval['time_interval'] );
			$intervals[ $key ]['subtotals'] = (object) $this->cast_numbers( $interval );
		}
	}

	/**
	 * Fills WHERE clause of SQL request with date-related constraints.
	 *
	 * @param array  $query_args Parameters supplied by the user.
	 * @param string $table_name Name of the db table relevant for the date constraint.
	 */
	protected function add_time_period_sql_params( $query_args, $table_name ) {
		$this->clear_sql_clause( array( 'from', 'where_time', 'where' ) );
		if ( isset( $this->subquery ) ) {
			$this->subquery->clear_sql_clause( 'where_time' );
		}

		if ( isset( $query_args['before'] ) && '' !== $query_args['before'] ) {
			if ( is_a( $query_args['before'], 'WC_DateTime' ) ) {
				$datetime_str = $query_args['before']->date( TimeInterval::$sql_datetime_format );
			} else {
				$datetime_str = $query_args['before']->format( TimeInterval::$sql_datetime_format );
			}
			if ( isset( $this->subquery ) ) {
				$this->subquery->add_sql_clause( 'where_time', "AND {$table_name}.`{$this->date_column_name}` <= '$datetime_str'" );
			} else {
				$this->add_sql_clause( 'where_time', "AND {$table_name}.`{$this->date_column_name}` <= '$datetime_str'" );
			}
		}

		if ( isset( $query_args['after'] ) && '' !== $query_args['after'] ) {
			if ( is_a( $query_args['after'], 'WC_DateTime' ) ) {
				$datetime_str = $query_args['after']->date( TimeInterval::$sql_datetime_format );
			} else {
				$datetime_str = $query_args['after']->format( TimeInterval::$sql_datetime_format );
			}
			if ( isset( $this->subquery ) ) {
				$this->subquery->add_sql_clause( 'where_time', "AND {$table_name}.`{$this->date_column_name}` >= '$datetime_str'" );
			} else {
				$this->add_sql_clause( 'where_time', "AND {$table_name}.`{$this->date_column_name}` >= '$datetime_str'" );
			}
		}
	}

	/**
	 * Fills LIMIT clause of SQL request based on user supplied parameters.
	 *
	 * @param array $query_args Parameters supplied by the user.
	 * @return array
	 */
	protected function get_limit_sql_params( $query_args ) {
		global $wpdb;
		$params = $this->get_limit_params( $query_args );

		$this->clear_sql_clause( 'limit' );
		$this->add_sql_clause( 'limit', $wpdb->prepare( 'LIMIT %d, %d', $params['offset'], $params['per_page'] ) );
		return $params;
	}

	/**
	 * Fills LIMIT parameters of SQL request based on user supplied parameters.
	 *
	 * @param array $query_args Parameters supplied by the user.
	 * @return array
	 */
	protected function get_limit_params( $query_args = array() ) {
		if ( isset( $query_args['per_page'] ) && is_numeric( $query_args['per_page'] ) ) {
			$this->limit_parameters['per_page'] = (int) $query_args['per_page'];
		} else {
			$this->limit_parameters['per_page'] = get_option( 'posts_per_page' );
		}

		$this->limit_parameters['offset'] = 0;
		if ( isset( $query_args['page'] ) ) {
			$this->limit_parameters['offset'] = ( (int) $query_args['page'] - 1 ) * $this->limit_parameters['per_page'];
		}

		return $this->limit_parameters;
	}

	/**
	 * Generates a virtual table given a list of IDs.
	 *
	 * @param array $ids          Array of IDs.
	 * @param array $id_field     Name of the ID field.
	 * @param array $other_values Other values that must be contained in the virtual table.
	 * @return array
	 */
	protected function get_ids_table( $ids, $id_field, $other_values = array() ) {
		global $wpdb;
		$selects = array();
		foreach ( $ids as $id ) {
			// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$new_select = $wpdb->prepare( "SELECT %s AS {$id_field}", $id );
			foreach ( $other_values as $key => $value ) {
				$new_select .= $wpdb->prepare( ", %s AS {$key}", $value );
			}
			// phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			array_push( $selects, $new_select );
		}
		return join( ' UNION ', $selects );
	}

	/**
	 * Returns a comma separated list of the fields in the `query_args`, if there aren't, returns `report_columns` keys.
	 *
	 * @param array $query_args Parameters supplied by the user.
	 * @return array
	 */
	protected function get_fields( $query_args ) {
		if ( isset( $query_args['fields'] ) && is_array( $query_args['fields'] ) ) {
			return $query_args['fields'];
		}
		return array_keys( $this->report_columns );
	}

	/**
	 * Returns a comma separated list of the field names prepared to be used for a selection after a join with `default_results`.
	 *
	 * @param array $fields                 Array of fields name.
	 * @param array $default_results_fields Fields to load from `default_results` table.
	 * @param array $outer_selections       Array of fields that are not selected in the inner query.
	 * @return string
	 */
	protected function format_join_selections( $fields, $default_results_fields, $outer_selections = array() ) {
		foreach ( $fields as $i => $field ) {
			foreach ( $default_results_fields as $default_results_field ) {
				if ( $field === $default_results_field ) {
					$field        = esc_sql( $field );
					$fields[ $i ] = "default_results.{$field} AS {$field}";
				}
			}
			if ( in_array( $field, $outer_selections, true ) && array_key_exists( $field, $this->report_columns ) ) {
				$fields[ $i ] = $this->report_columns[ $field ];
			}
		}
		return implode( ', ', $fields );
	}

	/**
	 * Fills ORDER BY clause of SQL request based on user supplied parameters.
	 *
	 * @param array $query_args Parameters supplied by the user.
	 */
	protected function add_order_by_sql_params( $query_args ) {
		if ( isset( $query_args['orderby'] ) ) {
			$order_by_clause = $this->normalize_order_by( esc_sql( $query_args['orderby'] ) );
		} else {
			$order_by_clause = '';
		}

		$this->clear_sql_clause( 'order_by' );
		$this->add_sql_clause( 'order_by', $order_by_clause );
		$this->add_orderby_order_clause( $query_args, $this );
	}

	/**
	 * Fills FROM and WHERE clauses of SQL request for 'Intervals' section of data response based on user supplied parameters.
	 *
	 * @param array  $query_args Parameters supplied by the user.
	 * @param string $table_name Name of the db table relevant for the date constraint.
	 */
	protected function add_intervals_sql_params( $query_args, $table_name ) {
		$this->clear_sql_clause( array( 'from', 'where_time', 'where' ) );

		$this->add_time_period_sql_params( $query_args, $table_name );

		if ( isset( $query_args['interval'] ) && '' !== $query_args['interval'] ) {
			$interval = $query_args['interval'];
			$this->clear_sql_clause( 'select' );
			$this->add_sql_clause( 'select', TimeInterval::db_datetime_format( $interval, $table_name, $this->date_column_name ) );
		}
	}

	/**
	 * Get join and where clauses for refunds based on user supplied parameters.
	 *
	 * @param array $query_args Parameters supplied by the user.
	 * @return array
	 */
	protected function get_refund_subquery( $query_args ) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'wc_order_stats';
		$sql_query  = array(
			'where_clause' => '',
			'from_clause'  => '',
		);

		if ( ! isset( $query_args['refunds'] ) ) {
			return $sql_query;
		}

		if ( 'all' === $query_args['refunds'] ) {
			$sql_query['where_clause'] .= 'parent_id != 0';
		}

		if ( 'none' === $query_args['refunds'] ) {
			$sql_query['where_clause'] .= 'parent_id = 0';
		}

		if ( 'full' === $query_args['refunds'] || 'partial' === $query_args['refunds'] ) {
			$operator                   = 'full' === $query_args['refunds'] ? '=' : '!=';
			$sql_query['from_clause']  .= " JOIN {$table_name} parent_order_stats ON {$table_name}.parent_id = parent_order_stats.order_id";
			$sql_query['where_clause'] .= "parent_order_stats.status {$operator} '{$this->normalize_order_status( 'refunded' )}'";
		}

		return $sql_query;
	}

	/**
	 * Returns an array of products belonging to given categories.
	 *
	 * @param array $categories List of categories IDs.
	 * @return array|stdClass
	 */
	protected function get_products_by_cat_ids( $categories ) {
		$terms = get_terms(
			array(
				'taxonomy' => 'product_cat',
				'include'  => $categories,
			)
		);

		if ( is_wp_error( $terms ) || empty( $terms ) ) {
			return array();
		}

		$args = array(
			'category' => wc_list_pluck( $terms, 'slug' ),
			'limit'    => -1,
			'return'   => 'ids',
		);
		return wc_get_products( $args );
	}

	/**
	 * Get WHERE filter by object ids subquery.
	 *
	 * @param string $select_table Select table name.
	 * @param string $select_field Select table object ID field name.
	 * @param string $filter_table Lookup table name.
	 * @param string $filter_field Lookup table object ID field name.
	 * @param string $compare      Comparison string (IN|NOT IN).
	 * @param string $id_list      Comma separated ID list.
	 *
	 * @return string
	 */
	protected function get_object_where_filter( $select_table, $select_field, $filter_table, $filter_field, $compare, $id_list ) {
		global $wpdb;
		if ( empty( $id_list ) ) {
			return '';
		}

		$lookup_name = isset( $wpdb->$filter_table ) ? $wpdb->$filter_table : $wpdb->prefix . $filter_table;
		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		return " {$select_table}.{$select_field} {$compare} (
			SELECT
				DISTINCT {$filter_table}.{$select_field}
			FROM
				{$filter_table}
			WHERE
				{$filter_table}.{$filter_field} IN ({$id_list})
		)";
		// phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	}

	/**
	 * Returns an array of ids of allowed products, based on query arguments from the user.
	 *
	 * @param array $query_args Parameters supplied by the user.
	 * @return array
	 */
	protected function get_included_products_array( $query_args ) {
		$included_products = array();
		$operator          = $this->get_match_operator( $query_args );

		if ( isset( $query_args['category_includes'] ) && is_array( $query_args['category_includes'] ) && count( $query_args['category_includes'] ) > 0 ) {
			$included_products = $this->get_products_by_cat_ids( $query_args['category_includes'] );

			// If no products were found in the specified categories, we will force an empty set
			// by matching a product ID of -1, unless the filters are OR/any and products are specified.
			if ( empty( $included_products ) ) {
				$included_products = array( '-1' );
			}
		}

		if ( isset( $query_args['product_includes'] ) && is_array( $query_args['product_includes'] ) && count( $query_args['product_includes'] ) > 0 ) {
			if ( count( $included_products ) > 0 ) {
				if ( 'AND' === $operator ) {
					// AND results in an intersection between products from selected categories and manually included products.
					$included_products = array_intersect( $included_products, $query_args['product_includes'] );
				} elseif ( 'OR' === $operator ) {
					// OR results in a union of products from selected categories and manually included products.
					$included_products = array_merge( $included_products, $query_args['product_includes'] );
				}
			} else {
				$included_products = $query_args['product_includes'];
			}
		}

		return $included_products;
	}

	/**
	 * Returns comma separated ids of allowed products, based on query arguments from the user.
	 *
	 * @param array $query_args Parameters supplied by the user.
	 * @return string
	 */
	protected function get_included_products( $query_args ) {
		$included_products = $this->get_included_products_array( $query_args );
		return implode( ',', $included_products );
	}

	/**
	 * Returns comma separated ids of allowed variations, based on query arguments from the user.
	 *
	 * @param array $query_args Parameters supplied by the user.
	 * @return string
	 */
	protected function get_included_variations( $query_args ) {
		return $this->get_filtered_ids( $query_args, 'variation_includes' );
	}

	/**
	 * Returns comma separated ids of excluded variations, based on query arguments from the user.
	 *
	 * @param array $query_args Parameters supplied by the user.
	 * @return string
	 */
	protected function get_excluded_variations( $query_args ) {
		return $this->get_filtered_ids( $query_args, 'variation_excludes' );
	}

	/**
	 * Returns an array of ids of disallowed products, based on query arguments from the user.
	 *
	 * @param array $query_args Parameters supplied by the user.
	 * @return array
	 */
	protected function get_excluded_products_array( $query_args ) {
		$excluded_products = array();
		$operator          = $this->get_match_operator( $query_args );

		if ( isset( $query_args['category_excludes'] ) && is_array( $query_args['category_excludes'] ) && count( $query_args['category_excludes'] ) > 0 ) {
			$excluded_products = $this->get_products_by_cat_ids( $query_args['category_excludes'] );
		}

		if ( isset( $query_args['product_excludes'] ) && is_array( $query_args['product_excludes'] ) && count( $query_args['product_excludes'] ) > 0 ) {
			$excluded_products = array_merge( $excluded_products, $query_args['product_excludes'] );
		}

		return $excluded_products;
	}

	/**
	 * Returns comma separated ids of excluded products, based on query arguments from the user.
	 *
	 * @param array $query_args Parameters supplied by the user.
	 * @return string
	 */
	protected function get_excluded_products( $query_args ) {
		$excluded_products = $this->get_excluded_products_array( $query_args );
		return implode( ',', $excluded_products );
	}

	/**
	 * Returns comma separated ids of included categories, based on query arguments from the user.
	 *
	 * @param array $query_args Parameters supplied by the user.
	 * @return string
	 */
	protected function get_included_categories( $query_args ) {
		return $this->get_filtered_ids( $query_args, 'category_includes' );
	}

	/**
	 * Returns comma separated ids of included coupons, based on query arguments from the user.
	 *
	 * @param array  $query_args Parameters supplied by the user.
	 * @param string $field      Field name in the parameter list.
	 * @return string
	 */
	protected function get_included_coupons( $query_args, $field = 'coupon_includes' ) {
		return $this->get_filtered_ids( $query_args, $field );
	}

	/**
	 * Returns comma separated ids of excluded coupons, based on query arguments from the user.
	 *
	 * @param array $query_args Parameters supplied by the user.
	 * @return string
	 */
	protected function get_excluded_coupons( $query_args ) {
		return $this->get_filtered_ids( $query_args, 'coupon_excludes' );
	}

	/**
	 * Returns comma separated ids of included orders, based on query arguments from the user.
	 *
	 * @param array $query_args Parameters supplied by the user.
	 * @return string
	 */
	protected function get_included_orders( $query_args ) {
		return $this->get_filtered_ids( $query_args, 'order_includes' );
	}

	/**
	 * Returns comma separated ids of excluded orders, based on query arguments from the user.
	 *
	 * @param array $query_args Parameters supplied by the user.
	 * @return string
	 */
	protected function get_excluded_orders( $query_args ) {
		return $this->get_filtered_ids( $query_args, 'order_excludes' );
	}

	/**
	 * Returns comma separated ids of included users, based on query arguments from the user.
	 *
	 * @param array $query_args Parameters supplied by the user.
	 * @return string
	 */
	protected function get_included_users( $query_args ) {
		return $this->get_filtered_ids( $query_args, 'user_includes' );
	}

	/**
	 * Returns comma separated ids of excluded users, based on query arguments from the user.
	 *
	 * @param array $query_args Parameters supplied by the user.
	 * @return string
	 */
	protected function get_excluded_users( $query_args ) {
		return $this->get_filtered_ids( $query_args, 'user_excludes' );
	}

	/**
	 * Returns order status subquery to be used in WHERE SQL query, based on query arguments from the user.
	 *
	 * @param array  $query_args Parameters supplied by the user.
	 * @param string $operator   AND or OR, based on match query argument.
	 * @return string
	 */
	protected function get_status_subquery( $query_args, $operator = 'AND' ) {
		global $wpdb;

		$subqueries        = array();
		$excluded_statuses = array();
		if ( isset( $query_args['status_is'] ) && is_array( $query_args['status_is'] ) && count( $query_args['status_is'] ) > 0 ) {
			$allowed_statuses = array_map( array( $this, 'normalize_order_status' ), esc_sql( $query_args['status_is'] ) );
			if ( $allowed_statuses ) {
				$subqueries[] = "{$wpdb->prefix}wc_order_stats.status IN ( '" . implode( "','", $allowed_statuses ) . "' )";
			}
		}

		if ( isset( $query_args['status_is_not'] ) && is_array( $query_args['status_is_not'] ) && count( $query_args['status_is_not'] ) > 0 ) {
			$excluded_statuses = array_map( array( $this, 'normalize_order_status' ), $query_args['status_is_not'] );
		}

		if ( ( ! isset( $query_args['status_is'] ) || empty( $query_args['status_is'] ) )
			&& ( ! isset( $query_args['status_is_not'] ) || empty( $query_args['status_is_not'] ) )
		) {
			$excluded_statuses = array_map( array( $this, 'normalize_order_status' ), $this->get_excluded_report_order_statuses() );
		}

		if ( $excluded_statuses ) {
			$subqueries[] = "{$wpdb->prefix}wc_order_stats.status NOT IN ( '" . implode( "','", $excluded_statuses ) . "' )";
		}

		return implode( " $operator ", $subqueries );
	}

	/**
	 * Add order status SQL clauses if included in query.
	 *
	 * @param array    $query_args Parameters supplied by the user.
	 * @param string   $table_name Database table name.
	 * @param SqlQuery $sql_query  Query object.
	 */
	protected function add_order_status_clause( $query_args, $table_name, &$sql_query ) {
		global $wpdb;
		$order_status_filter = $this->get_status_subquery( $query_args );
		if ( $order_status_filter ) {
			$sql_query->add_sql_clause( 'join', "JOIN {$wpdb->prefix}wc_order_stats ON {$table_name}.order_id = {$wpdb->prefix}wc_order_stats.order_id" );
			$sql_query->add_sql_clause( 'where', "AND ( {$order_status_filter} )" );
		}
	}

	/**
	 * Add order by SQL clause if included in query.
	 *
	 * @param array    $query_args Parameters supplied by the user.
	 * @param SqlQuery $sql_query  Query object.
	 * @return string Order by clause.
	 */
	protected function add_order_by_clause( $query_args, &$sql_query ) {
		$order_by_clause = '';

		$sql_query->clear_sql_clause( array( 'order_by' ) );
		if ( isset( $query_args['orderby'] ) ) {
			$order_by_clause = $this->normalize_order_by( esc_sql( $query_args['orderby'] ) );
			$sql_query->add_sql_clause( 'order_by', $order_by_clause );
		}

		// Return ORDER BY clause to allow adding the sort field(s) to query via a JOIN.
		return $order_by_clause;
	}

	/**
	 * Add order by order SQL clause.
	 *
	 * @param array    $query_args Parameters supplied by the user.
	 * @param SqlQuery $sql_query  Query object.
	 */
	protected function add_orderby_order_clause( $query_args, &$sql_query ) {
		if ( isset( $query_args['order'] ) ) {
			$sql_query->add_sql_clause( 'order_by', esc_sql( $query_args['order'] ) );
		} else {
			$sql_query->add_sql_clause( 'order_by', 'DESC' );
		}
	}

	/**
	 * Returns customer subquery to be used in WHERE SQL query, based on query arguments from the user.
	 *
	 * @param array $query_args Parameters supplied by the user.
	 * @return string
	 */
	protected function get_customer_subquery( $query_args ) {
		global $wpdb;

		$customer_filter = '';
		if ( isset( $query_args['customer_type'] ) ) {
			if ( 'new' === strtolower( $query_args['customer_type'] ) ) {
				$customer_filter = " {$wpdb->prefix}wc_order_stats.returning_customer = 0";
			} elseif ( 'returning' === strtolower( $query_args['customer_type'] ) ) {
				$customer_filter = " {$wpdb->prefix}wc_order_stats.returning_customer = 1";
			}
		}

		return $customer_filter;
	}

	/**
	 * Returns product attribute subquery elements used in JOIN and WHERE clauses,
	 * based on query arguments from the user.
	 *
	 * @param array $query_args Parameters supplied by the user.
	 * @return array
	 */
	protected function get_attribute_subqueries( $query_args ) {
		global $wpdb;

		$sql_clauses           = array(
			'join'  => array(),
			'where' => array(),
		);
		$match_operator        = $this->get_match_operator( $query_args );
		$post_meta_comparators = array(
			'='  => 'attribute_is',
			'!=' => 'attribute_is_not',
		);

		foreach ( $post_meta_comparators as $comparator => $arg ) {
			if ( ! isset( $query_args[ $arg ] ) || ! is_array( $query_args[ $arg ] ) ) {
				continue;
			}
			foreach ( $query_args[ $arg ] as $attribute_term ) {
				// We expect tuples.
				if ( ! is_array( $attribute_term ) || 2 !== count( $attribute_term ) ) {
					continue;
				}

				// If the tuple is numeric, assume these are IDs.
				if ( is_numeric( $attribute_term[0] ) && is_numeric( $attribute_term[1] ) ) {
					$attribute_id = intval( $attribute_term[0] );
					$term_id      = intval( $attribute_term[1] );

					// Invalid IDs.
					if ( 0 === $attribute_id || 0 === $term_id ) {
						continue;
					}

					// @todo: Use wc_get_attribute () instead ?
					$attr_taxonomy = wc_attribute_taxonomy_name_by_id( $attribute_id );
					// Invalid attribute ID.
					if ( empty( $attr_taxonomy ) ) {
						continue;
					}

					$attr_term = get_term_by( 'id', $term_id, $attr_taxonomy );
					// Invalid term ID.
					if ( false === $attr_term ) {
						continue;
					}

					$meta_key   = sanitize_title( $attr_taxonomy );
					$meta_value = $attr_term->slug;
				} else {
					// Assume these are a custom attribute slug/value pair.
					$meta_key   = esc_sql( $attribute_term[0] );
					$meta_value = esc_sql( $attribute_term[1] );
				}

				$join_alias       = 'orderitemmeta1';
				$table_to_join_on = "{$wpdb->prefix}wc_order_product_lookup";

				if ( empty( $sql_clauses['join'] ) ) {
					$sql_clauses['join'][] = "JOIN {$wpdb->prefix}woocommerce_order_items orderitems ON orderitems.order_id = {$table_to_join_on}.order_id";
				}

				// If we're matching all filters (AND), we'll need multiple JOINs on postmeta.
				// If not, just one.
				if ( 'AND' === $match_operator || 1 === count( $sql_clauses['join'] ) ) {
					$join_idx              = count( $sql_clauses['join'] );
					$join_alias            = 'orderitemmeta' . $join_idx;
					$sql_clauses['join'][] = "JOIN {$wpdb->prefix}woocommerce_order_itemmeta as {$join_alias} ON {$join_alias}.order_item_id = {$table_to_join_on}.order_item_id";
				}

				// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				$sql_clauses['where'][] = $wpdb->prepare( "( {$join_alias}.meta_key = %s AND {$join_alias}.meta_value {$comparator} %s )", $meta_key, $meta_value );
			}
		}

		// If we're matching multiple attributes and all filters (AND), make sure
		// we're matching attributes on the same product.
		$num_attribute_filters = count( $sql_clauses['join'] );

		for ( $i = 2; $i < $num_attribute_filters; $i++ ) {
			$join_alias            = 'orderitemmeta' . $i;
			$sql_clauses['join'][] = "AND orderitemmeta1.order_item_id = {$join_alias}.order_item_id";
		}

		return $sql_clauses;
	}

	/**
	 * Returns logic operator for WHERE subclause based on 'match' query argument.
	 *
	 * @param array $query_args Parameters supplied by the user.
	 * @return string
	 */
	protected function get_match_operator( $query_args ) {
		$operator = 'AND';

		if ( ! isset( $query_args['match'] ) ) {
			return $operator;
		}

		if ( 'all' === strtolower( $query_args['match'] ) ) {
			$operator = 'AND';
		} elseif ( 'any' === strtolower( $query_args['match'] ) ) {
			$operator = 'OR';
		}
		return $operator;
	}

	/**
	 * Returns filtered comma separated ids, based on query arguments from the user.
	 *
	 * @param array  $query_args Parameters supplied by the user.
	 * @param string $field      Query field to filter.
	 * @param string $separator  Field separator.
	 * @return string
	 */
	protected function get_filtered_ids( $query_args, $field, $separator = ',' ) {
		global $wpdb;

		$ids_str = '';
		$ids     = isset( $query_args[ $field ] ) && is_array( $query_args[ $field ] ) ? $query_args[ $field ] : array();

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
		$ids = apply_filters( 'woocommerce_analytics_' . $field, $ids, $query_args, $field, $this->context );

		if ( ! empty( $ids ) ) {
			$placeholders = implode( $separator, array_fill( 0, count( $ids ), '%d' ) );
			/* phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared */
			$ids_str = $wpdb->prepare( "{$placeholders}", $ids );
			/* phpcs:enable */
		}
		return $ids_str;
	}

	/**
	 * Assign report columns once full table name has been assigned.
	 */
	protected function assign_report_columns() {}
}
