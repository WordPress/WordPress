<?php
/**
 * Admin report functionality.
 *
 * @package WooCommerce\Admin\Reports
 */

use Automattic\WooCommerce\Utilities\ArrayUtil;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Admin Report.
 *
 * Extended by reports to show charts and stats in admin.
 *
 * @package     WooCommerce\Admin\Reports
 * @version     2.1.0
 */
class WC_Admin_Report {

	/**
	 * List of transients name that have been updated and need persisting.
	 *
	 * @var array
	 */
	protected static $transients_to_update = array();

	/**
	 * The list of transients.
	 *
	 * @var array
	 */
	protected static $cached_results = array();

	/**
	 * The chart interval.
	 *
	 * @var int
	 */
	public $chart_interval;

	/**
	 * Group by SQL query.
	 *
	 * @var string
	 */
	public $group_by_query;

	/**
	 * The bar width.
	 *
	 * @var int
	 */
	public $barwidth;

	/**
	 * Group chart item by day or month.
	 *
	 * @var string
	 */
	public $chart_groupby;

	/**
	 * The start date of the report.
	 *
	 * @var int timestamp
	 */
	public $start_date;

	/**
	 * The end date of the report.
	 *
	 * @var int timestamp
	 */
	public $end_date;

	/**
	 * Get report totals such as order totals and discount amounts.
	 *
	 * Data example:
	 *
	 * '_order_total' => array(
	 *     'type'     => 'meta',
	 *     'function' => 'SUM',
	 *     'name'     => 'total_sales'
	 * )
	 *
	 * @param  array $args arguments for the report.
	 * @return mixed depending on query_type
	 */
	public function get_order_report_data( $args = array() ) {
		global $wpdb;

		$default_args = array(
			'data'                => array(),
			'where'               => array(),
			'where_meta'          => array(),
			'query_type'          => 'get_row',
			'group_by'            => '',
			'order_by'            => '',
			'limit'               => '',
			'filter_range'        => false,
			'nocache'             => false,
			'debug'               => false,
			'order_types'         => wc_get_order_types( 'reports' ),
			'order_status'        => array( 'completed', 'processing', 'on-hold' ),
			'parent_order_status' => false,
		);
		$args         = apply_filters( 'woocommerce_reports_get_order_report_data_args', $args );
		$args         = wp_parse_args( $args, $default_args );

		// phpcs:ignore WordPress.PHP.DontExtract.extract_extract
		extract( $args );

		if ( empty( $data ) ) {
			return '';
		}

		$order_status = apply_filters( 'woocommerce_reports_order_statuses', $order_status );

		$query  = array();
		$select = array();

		foreach ( $data as $raw_key => $value ) {
			$key      = sanitize_key( $raw_key );
			$distinct = '';

			if ( isset( $value['distinct'] ) ) {
				$distinct = 'DISTINCT';
			}

			switch ( $value['type'] ) {
				case 'meta':
					$get_key = "meta_{$key}.meta_value";
					break;
				case 'parent_meta':
					$get_key = "parent_meta_{$key}.meta_value";
					break;
				case 'post_data':
					$get_key = "posts.{$key}";
					break;
				case 'order_item_meta':
					$get_key = "order_item_meta_{$key}.meta_value";
					break;
				case 'order_item':
					$get_key = "order_items.{$key}";
					break;
			}

			if ( empty( $get_key ) ) {
				// Skip to the next foreach iteration else the query will be invalid.
				continue;
			}

			if ( $value['function'] ) {
				$get = "{$value['function']}({$distinct} {$get_key})";
			} else {
				$get = "{$distinct} {$get_key}";
			}

			$select[] = "{$get} as {$value['name']}";
		}

		$query['select'] = 'SELECT ' . implode( ',', $select );
		$query['from']   = "FROM {$wpdb->posts} AS posts";

		// Joins.
		$joins = array();

		foreach ( ( $data + $where ) as $raw_key => $value ) {
			$join_type = isset( $value['join_type'] ) ? $value['join_type'] : 'INNER';
			$type      = isset( $value['type'] ) ? $value['type'] : false;
			$key       = sanitize_key( $raw_key );

			switch ( $type ) {
				case 'meta':
					$joins[ "meta_{$key}" ] = "{$join_type} JOIN {$wpdb->postmeta} AS meta_{$key} ON ( posts.ID = meta_{$key}.post_id AND meta_{$key}.meta_key = '{$raw_key}' )";
					break;
				case 'parent_meta':
					$joins[ "parent_meta_{$key}" ] = "{$join_type} JOIN {$wpdb->postmeta} AS parent_meta_{$key} ON (posts.post_parent = parent_meta_{$key}.post_id) AND (parent_meta_{$key}.meta_key = '{$raw_key}')";
					break;
				case 'order_item_meta':
					$joins['order_items'] = "{$join_type} JOIN {$wpdb->prefix}woocommerce_order_items AS order_items ON (posts.ID = order_items.order_id)";

					if ( ! empty( $value['order_item_type'] ) ) {
						$joins['order_items'] .= " AND (order_items.order_item_type = '{$value['order_item_type']}')";
					}

					$joins[ "order_item_meta_{$key}" ] = "{$join_type} JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS order_item_meta_{$key} ON " .
														"(order_items.order_item_id = order_item_meta_{$key}.order_item_id) " .
														" AND (order_item_meta_{$key}.meta_key = '{$raw_key}')";
					break;
				case 'order_item':
					$joins['order_items'] = "{$join_type} JOIN {$wpdb->prefix}woocommerce_order_items AS order_items ON posts.ID = order_items.order_id";
					break;
			}
		}

		if ( ! empty( $where_meta ) ) {
			foreach ( $where_meta as $value ) {
				if ( ! is_array( $value ) ) {
					continue;
				}
				$join_type = isset( $value['join_type'] ) ? $value['join_type'] : 'INNER';
				$type      = isset( $value['type'] ) ? $value['type'] : false;
				$key       = sanitize_key( is_array( $value['meta_key'] ) ? $value['meta_key'][0] . '_array' : $value['meta_key'] );

				if ( 'order_item_meta' === $type ) {

					$joins['order_items']              = "{$join_type} JOIN {$wpdb->prefix}woocommerce_order_items AS order_items ON posts.ID = order_items.order_id";
					$joins[ "order_item_meta_{$key}" ] = "{$join_type} JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS order_item_meta_{$key} ON order_items.order_item_id = order_item_meta_{$key}.order_item_id";

				} else {
					// If we have a where clause for meta, join the postmeta table.
					$joins[ "meta_{$key}" ] = "{$join_type} JOIN {$wpdb->postmeta} AS meta_{$key} ON posts.ID = meta_{$key}.post_id";
				}
			}
		}

		if ( ! empty( $parent_order_status ) ) {
			$joins['parent'] = "LEFT JOIN {$wpdb->posts} AS parent ON posts.post_parent = parent.ID";
		}

		$query['join'] = implode( ' ', $joins );

		$query['where'] = "
			WHERE 	posts.post_type 	IN ( '" . implode( "','", $order_types ) . "' )
			";

		if ( ! empty( $order_status ) ) {
			$query['where'] .= "
				AND 	posts.post_status 	IN ( 'wc-" . implode( "','wc-", $order_status ) . "')
			";
		}

		if ( ! empty( $parent_order_status ) ) {
			if ( ! empty( $order_status ) ) {
				$query['where'] .= " AND ( parent.post_status IN ( 'wc-" . implode( "','wc-", $parent_order_status ) . "') OR parent.ID IS NULL ) ";
			} else {
				$query['where'] .= " AND parent.post_status IN ( 'wc-" . implode( "','wc-", $parent_order_status ) . "') ";
			}
		}

		// phpcs:disable WordPress.DateTime.RestrictedFunctions.date_date
		if ( $filter_range ) {
			$query['where'] .= "
				AND 	posts.post_date >= '" . date( 'Y-m-d H:i:s', $this->start_date ) . "'
				AND 	posts.post_date < '" . date( 'Y-m-d H:i:s', strtotime( '+1 DAY', $this->end_date ) ) . "'
			";
		}
		// phpcs:enable WordPress.DateTime.RestrictedFunctions.date_date

		if ( ! empty( $where_meta ) ) {

			$relation = isset( $where_meta['relation'] ) ? $where_meta['relation'] : 'AND';

			$query['where'] .= ' AND (';

			foreach ( $where_meta as $index => $value ) {

				if ( ! is_array( $value ) ) {
					continue;
				}

				$key = sanitize_key( is_array( $value['meta_key'] ) ? $value['meta_key'][0] . '_array' : $value['meta_key'] );

				if ( strtolower( $value['operator'] ) === 'in' || strtolower( $value['operator'] ) === 'not in' ) {

					if ( is_array( $value['meta_value'] ) ) {
						// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
						$value['meta_value'] = implode( "','", $value['meta_value'] );
					}

					if ( ! empty( $value['meta_value'] ) ) {
						$where_value = "{$value['operator']} ('{$value['meta_value']}')";
					}
				} else {
					$where_value = "{$value['operator']} '{$value['meta_value']}'";
				}

				if ( ! empty( $where_value ) ) {
					if ( $index > 0 ) {
						$query['where'] .= ' ' . $relation;
					}

					if ( isset( $value['type'] ) && 'order_item_meta' === $value['type'] ) {

						if ( is_array( $value['meta_key'] ) ) {
							$query['where'] .= " ( order_item_meta_{$key}.meta_key   IN ('" . implode( "','", $value['meta_key'] ) . "')";
						} else {
							$query['where'] .= " ( order_item_meta_{$key}.meta_key   = '{$value['meta_key']}'";
						}

						$query['where'] .= " AND order_item_meta_{$key}.meta_value {$where_value} )";
					} else {

						if ( is_array( $value['meta_key'] ) ) {
							$query['where'] .= " ( meta_{$key}.meta_key   IN ('" . implode( "','", $value['meta_key'] ) . "')";
						} else {
							$query['where'] .= " ( meta_{$key}.meta_key   = '{$value['meta_key']}'";
						}

						$query['where'] .= " AND meta_{$key}.meta_value {$where_value} )";
					}
				}
			}

			$query['where'] .= ')';
		}

		if ( ! empty( $where ) ) {

			foreach ( $where as $value ) {

				if ( strtolower( $value['operator'] ) === 'in' || strtolower( $value['operator'] ) === 'not in' ) {

					if ( is_array( $value['value'] ) ) {
						$value['value'] = implode( "','", $value['value'] );
					}

					if ( ! empty( $value['value'] ) ) {
						$where_value = "{$value['operator']} ('{$value['value']}')";
					}
				} else {
					$where_value = "{$value['operator']} '{$value['value']}'";
				}

				if ( ! empty( $where_value ) ) {
					$query['where'] .= " AND {$value['key']} {$where_value}";
				}
			}
		}

		if ( $group_by ) {
			$query['group_by'] = "GROUP BY {$group_by}";
		}

		if ( $order_by ) {
			$query['order_by'] = "ORDER BY {$order_by}";
		}

		if ( $limit ) {
			$query['limit'] = "LIMIT {$limit}";
		}

		$query = apply_filters( 'woocommerce_reports_get_order_report_query', $query );
		$query = implode( ' ', $query );

		if ( $debug ) {
			echo '<pre>';
			wc_print_r( $query );
			echo '</pre>';
		}

		if ( $debug || $nocache ) {
			self::enable_big_selects();

			$result = apply_filters( 'woocommerce_reports_get_order_report_data', $wpdb->$query_type( $query ), $data );
		} else {
			$query_hash = md5( $query_type . $query );
			$result     = $this->get_cached_query( $query_hash );
			if ( null === $result ) {
				self::enable_big_selects();

				$result = apply_filters( 'woocommerce_reports_get_order_report_data', $wpdb->$query_type( $query ), $data );
			}
			$this->set_cached_query( $query_hash, $result );
		}

		return $result;
	}

	/**
	 * Init the static hooks of the class.
	 */
	protected static function add_update_transients_hook() {
		if ( ! has_action( 'shutdown', array( 'WC_Admin_Report', 'maybe_update_transients' ) ) ) {
			add_action( 'shutdown', array( 'WC_Admin_Report', 'maybe_update_transients' ) );
		}
	}

	/**
	 * Enables big mysql selects for reports, just once for this session.
	 */
	protected static function enable_big_selects() {
		static $big_selects = false;

		global $wpdb;

		if ( ! $big_selects ) {
			$wpdb->query( 'SET SESSION SQL_BIG_SELECTS=1' );
			$big_selects = true;
		}
	}

	/**
	 * Get the cached query result or null if it's not in the cache.
	 *
	 * @param string $query_hash The query hash.
	 *
	 * @return mixed
	 */
	protected function get_cached_query( $query_hash ) {
		$class = strtolower( get_class( $this ) );

		if ( ! isset( self::$cached_results[ $class ] ) ) {
			self::$cached_results[ $class ] = get_transient( strtolower( get_class( $this ) ) );
		}

		if ( isset( self::$cached_results[ $class ][ $query_hash ] ) ) {
			return self::$cached_results[ $class ][ $query_hash ];
		}

		return null;
	}

	/**
	 * Set the cached query result.
	 *
	 * @param string $query_hash The query hash.
	 * @param mixed  $data The data to cache.
	 */
	protected function set_cached_query( $query_hash, $data ) {
		$class = strtolower( get_class( $this ) );

		if ( ! isset( self::$cached_results[ $class ] ) ) {
			self::$cached_results[ $class ] = get_transient( $class );
		}

		if ( false === self::$cached_results[ $class ] ) {
			self::$cached_results[ $class ] = array();
		}

		self::add_update_transients_hook();

		self::$transients_to_update[ $class ]          = $class;
		self::$cached_results[ $class ][ $query_hash ] = $data;
	}

	/**
	 * Function to update the modified transients at the end of the request.
	 */
	public static function maybe_update_transients() {
		foreach ( self::$transients_to_update as $key => $transient_name ) {
			set_transient( $transient_name, self::$cached_results[ $transient_name ], DAY_IN_SECONDS );
		}
		// Transients have been updated reset the list.
		self::$transients_to_update = array();
	}

	/**
	 * Put data with post_date's into an array of times.
	 *
	 * @param  array  $data array of your data.
	 * @param  string $date_key key for the 'date' field. e.g. 'post_date'.
	 * @param  string $data_key key for the data you are charting.
	 * @param  int    $interval interval to use.
	 * @param  string $start_date start date.
	 * @param  string $group_by group by.
	 * @return array
	 */
	public function prepare_chart_data( $data, $date_key, $data_key, $interval, $start_date, $group_by ) {
		// phpcs:disable WordPress.DateTime.RestrictedFunctions.date_date

		$prepared_data = array();

		// Ensure all days (or months) have values in this range.
		if ( 'day' === $group_by ) {
			for ( $i = 0; $i <= $interval; $i ++ ) {
				$time = strtotime( date( 'Ymd', strtotime( "+{$i} DAY", $start_date ) ) ) . '000';

				if ( ! isset( $prepared_data[ $time ] ) ) {
					$prepared_data[ $time ] = array( esc_js( $time ), 0 );
				}
			}
		} else {
			$current_yearnum  = date( 'Y', $start_date );
			$current_monthnum = date( 'm', $start_date );

			for ( $i = 0; $i <= $interval; $i ++ ) {
				$time = strtotime( $current_yearnum . str_pad( $current_monthnum, 2, '0', STR_PAD_LEFT ) . '01' ) . '000';

				if ( ! isset( $prepared_data[ $time ] ) ) {
					$prepared_data[ $time ] = array( esc_js( $time ), 0 );
				}

				$current_monthnum ++;

				if ( $current_monthnum > 12 ) {
					$current_monthnum = 1;
					$current_yearnum  ++;
				}
			}
		}

		foreach ( $data as $d ) {
			switch ( $group_by ) {
				case 'day':
					$time = strtotime( date( 'Ymd', strtotime( $d->$date_key ) ) ) . '000';
					break;
				case 'month':
				default:
					$time = strtotime( date( 'Ym', strtotime( $d->$date_key ) ) . '01' ) . '000';
					break;
			}

			if ( ! isset( $prepared_data[ $time ] ) ) {
				continue;
			}

			if ( $data_key ) {
				$prepared_data[ $time ][1] += $d->$data_key;
			} else {
				$prepared_data[ $time ][1] ++;
			}
		}

		return $prepared_data;

		// phpcs:enable WordPress.DateTime.RestrictedFunctions.date_date
	}

	/**
	 * Prepares a sparkline to show sales in the last X days.
	 *
	 * @param  int    $id ID of the product to show. Blank to get all orders.
	 * @param  int    $days Days of stats to get.
	 * @param  string $type Type of sparkline to get. Ignored if ID is not set.
	 * @return string
	 */
	public function sales_sparkline( $id = '', $days = 7, $type = 'sales' ) {

		// phpcs:disable WordPress.DateTime.RestrictedFunctions.date_date, WordPress.DateTime.CurrentTimeTimestamp.Requested

		if ( $id ) {
			$meta_key = ( 'sales' === $type ) ? '_line_total' : '_qty';

			$data = $this->get_order_report_data(
				array(
					'data'         => array(
						'_product_id' => array(
							'type'            => 'order_item_meta',
							'order_item_type' => 'line_item',
							'function'        => '',
							'name'            => 'product_id',
						),
						$meta_key     => array(
							'type'            => 'order_item_meta',
							'order_item_type' => 'line_item',
							'function'        => 'SUM',
							'name'            => 'sparkline_value',
						),
						'post_date'   => array(
							'type'     => 'post_data',
							'function' => '',
							'name'     => 'post_date',
						),
					),
					'where'        => array(
						array(
							'key'      => 'post_date',
							'value'    => date( 'Y-m-d', strtotime( 'midnight -' . ( $days - 1 ) . ' days', current_time( 'timestamp' ) ) ),
							'operator' => '>',
						),
						array(
							'key'      => 'order_item_meta__product_id.meta_value',
							'value'    => $id,
							'operator' => '=',
						),
					),
					'group_by'     => 'YEAR(posts.post_date), MONTH(posts.post_date), DAY(posts.post_date)',
					'query_type'   => 'get_results',
					'filter_range' => false,
				)
			);
		} else {

			$data = $this->get_order_report_data(
				array(
					'data'         => array(
						'_order_total' => array(
							'type'     => 'meta',
							'function' => 'SUM',
							'name'     => 'sparkline_value',
						),
						'post_date'    => array(
							'type'     => 'post_data',
							'function' => '',
							'name'     => 'post_date',
						),
					),
					'where'        => array(
						array(
							'key'      => 'post_date',
							'value'    => date( 'Y-m-d', strtotime( 'midnight -' . ( $days - 1 ) . ' days', current_time( 'timestamp' ) ) ),
							'operator' => '>',
						),
					),
					'group_by'     => 'YEAR(posts.post_date), MONTH(posts.post_date), DAY(posts.post_date)',
					'query_type'   => 'get_results',
					'filter_range' => false,
				)
			);
		}

		$total = 0;
		foreach ( $data as $d ) {
			$total += $d->sparkline_value;
		}

		if ( 'sales' === $type ) {
			/* translators: 1: total income 2: days */
			$tooltip = sprintf( __( 'Sold %1$s worth in the last %2$d days', 'woocommerce' ), wp_strip_all_tags( wc_price( $total ) ), $days );
		} else {
			/* translators: 1: total items sold 2: days */
			$tooltip = sprintf( _n( 'Sold %1$d item in the last %2$d days', 'Sold %1$d items in the last %2$d days', $total, 'woocommerce' ), $total, $days );
		}

		$sparkline_data = array_values( $this->prepare_chart_data( $data, 'post_date', 'sparkline_value', $days - 1, strtotime( 'midnight -' . ( $days - 1 ) . ' days', current_time( 'timestamp' ) ), 'day' ) );

		return '<span class="wc_sparkline ' . ( ( 'sales' === $type ) ? 'lines' : 'bars' ) . ' tips" data-color="#777" data-tip="' . esc_attr( $tooltip ) . '" data-barwidth="' . 60 * 60 * 16 * 1000 . '" data-sparkline="' . wc_esc_json( wp_json_encode( $sparkline_data ) ) . '"></span>';

		// phpcs:enable WordPress.DateTime.RestrictedFunctions.date_date, WordPress.DateTime.CurrentTimeTimestamp.Requested
	}

	/**
	 * Get the current range and calculate the start and end dates.
	 *
	 * @param  string $current_range Type of range.
	 */
	public function calculate_current_range( $current_range ) {

		// phpcs:disable WordPress.DateTime.RestrictedFunctions.date_date, WordPress.DateTime.CurrentTimeTimestamp.Requested
		// phpcs:disable WordPress.Security.NonceVerification.Recommended

		switch ( $current_range ) {

			case 'custom':
				// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated
				$this->start_date = max( strtotime( '-20 years' ), strtotime( sanitize_text_field( wp_unslash( $_GET['start_date'] ) ) ) );

				if ( empty( $_GET['end_date'] ) ) {
					$this->end_date = strtotime( 'midnight', current_time( 'timestamp' ) );
				} else {
					$this->end_date = strtotime( 'midnight', strtotime( sanitize_text_field( wp_unslash( $_GET['end_date'] ) ) ) );
				}

				$interval = 0;
				$min_date = $this->start_date;

				// phpcs:ignore WordPress.CodeAnalysis.AssignmentInCondition.FoundInWhileCondition
				while ( ( $min_date = strtotime( '+1 MONTH', $min_date ) ) <= $this->end_date ) {
					$interval ++;
				}

				// 3 months max for day view
				if ( $interval > 3 ) {
					$this->chart_groupby = 'month';
				} else {
					$this->chart_groupby = 'day';
				}
				break;

			case 'year':
				$this->start_date    = strtotime( date( 'Y-01-01', current_time( 'timestamp' ) ) );
				$this->end_date      = strtotime( 'midnight', current_time( 'timestamp' ) );
				$this->chart_groupby = 'month';
				break;

			case 'last_month':
				$first_day_current_month = strtotime( date( 'Y-m-01', current_time( 'timestamp' ) ) );
				$this->start_date        = strtotime( date( 'Y-m-01', strtotime( '-1 DAY', $first_day_current_month ) ) );
				$this->end_date          = strtotime( date( 'Y-m-t', strtotime( '-1 DAY', $first_day_current_month ) ) );
				$this->chart_groupby     = 'day';
				break;

			case 'month':
				$this->start_date    = strtotime( date( 'Y-m-01', current_time( 'timestamp' ) ) );
				$this->end_date      = strtotime( 'midnight', current_time( 'timestamp' ) );
				$this->chart_groupby = 'day';
				break;

			case '7day':
				$this->start_date    = strtotime( '-6 days', strtotime( 'midnight', current_time( 'timestamp' ) ) );
				$this->end_date      = strtotime( 'midnight', current_time( 'timestamp' ) );
				$this->chart_groupby = 'day';
				break;
		}

		// Group by.
		switch ( $this->chart_groupby ) {

			case 'day':
				$this->group_by_query = 'YEAR(posts.post_date), MONTH(posts.post_date), DAY(posts.post_date)';
				$this->chart_interval = absint( ceil( max( 0, ( $this->end_date - $this->start_date ) / ( 60 * 60 * 24 ) ) ) );
				$this->barwidth       = 60 * 60 * 24 * 1000;
				break;

			case 'month':
				$this->group_by_query = 'YEAR(posts.post_date), MONTH(posts.post_date)';
				$this->chart_interval = 0;
				$min_date             = strtotime( date( 'Y-m-01', $this->start_date ) );

				// phpcs:ignore WordPress.CodeAnalysis.AssignmentInCondition.FoundInWhileCondition
				while ( ( $min_date = strtotime( '+1 MONTH', $min_date ) ) <= $this->end_date ) {
					$this->chart_interval ++;
				}

				$this->barwidth = 60 * 60 * 24 * 7 * 4 * 1000;
				break;
		}

		// phpcs:enable WordPress.Security.NonceVerification.Recommended
		// phpcs:enable WordPress.DateTime.RestrictedFunctions.date_date, WordPress.DateTime.CurrentTimeTimestamp.Requested
	}

	/**
	 * Return currency tooltip JS based on WooCommerce currency position settings.
	 *
	 * @return string
	 */
	public function get_currency_tooltip() {
		switch ( get_option( 'woocommerce_currency_pos' ) ) {
			case 'right':
				$currency_tooltip = 'append_tooltip: "' . get_woocommerce_currency_symbol() . '"';
				break;
			case 'right_space':
				$currency_tooltip = 'append_tooltip: "&nbsp;' . get_woocommerce_currency_symbol() . '"';
				break;
			case 'left':
				$currency_tooltip = 'prepend_tooltip: "' . get_woocommerce_currency_symbol() . '"';
				break;
			case 'left_space':
			default:
				$currency_tooltip = 'prepend_tooltip: "' . get_woocommerce_currency_symbol() . '&nbsp;"';
				break;
		}

		return $currency_tooltip;
	}

	/**
	 * Get the main chart.
	 */
	public function get_main_chart() {}

	/**
	 * Get the legend for the main chart sidebar.
	 *
	 * @return array
	 */
	public function get_chart_legend() {
		return array();
	}

	/**
	 * Get chart widgets.
	 *
	 * @return array
	 */
	public function get_chart_widgets() {
		return array();
	}

	/**
	 * Get an export link if needed.
	 */
	public function get_export_button() {}

	/**
	 * Output the report.
	 */
	public function output_report() {}

	/**
	 * Check nonce for current range.
	 *
	 * @since  3.0.4
	 * @param  string $current_range Current range.
	 */
	public function check_current_range_nonce( $current_range ) {
		if ( 'custom' !== $current_range ) {
			return;
		}

		if ( ! isset( $_GET['wc_reports_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_GET['wc_reports_nonce'] ), 'custom_range' ) ) {
			// phpcs:disable WordPress.Security.ValidatedSanitizedInput.InputNotValidated
			wp_die(
				/* translators: %1$s: open link, %2$s: close link */
				sprintf( esc_html__( 'This report link has expired. %1$sClick here to view the filtered report%2$s.', 'woocommerce' ), '<a href="' . esc_url( wp_nonce_url( esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ), 'custom_range', 'wc_reports_nonce' ) ) . '">', '</a>' ),
				esc_attr__( 'Confirm navigation', 'woocommerce' )
			);
			// phpcs:enable WordPress.Security.ValidatedSanitizedInput.InputNotValidated
			exit;
		}
	}
}
