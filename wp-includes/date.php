<?php
/**
 * WP_Date_Query will generate a MySQL WHERE clause for the specified date-based parameters.
 *
 * Initialize the class by passing an array of arrays of parameters.
 *
 * @link http://codex.wordpress.org/Function_Reference/WP_Query Codex page.
 *
 * @since 3.7.0
 */
class WP_Date_Query {
	/**
	 * List of date queries.
	 *
	 * @since 3.7.0
	 * @access public
	 * @var array
	 */
	public $queries = array();

	/**
	 * The relation between the queries. Can be either 'AND' or 'OR' and can be changed via the query arguments.
	 *
	 * @since 3.7.0
	 * @access public
	 * @var string
	 */
	public $relation = 'AND';

	/**
	 * The column to query against. Can be changed via the query arguments.
	 *
	 * @since 3.7.0
	 * @access public
	 * @var string
	 */
	public $column = 'post_date';

	/**
	 * The value comparison operator. Can be changed via the query arguments.
	 *
	 * @since 3.7.0
	 * @access public
	 * @var array
	 */
	public $compare = '=';

	/**
	 * Constructor.
	 *
	 * @since 3.7.0
	 * @since 4.0.0 The $inclusive logic was updated to include all times within the date range.
	 *
	 * @param array $date_query {
	 *     One or more associative arrays of date query parameters.
	 *
	 *     @type array {
	 *         @type string $column   Optional. The column to query against. If undefined, inherits the value of
	 *                                the $default_column parameter. Default 'post_date'. Accepts 'post_date',
	 *                                'post_date_gmt', 'post_modified','post_modified_gmt', 'comment_date',
	 *                                'comment_date_gmt'.
	 *         @type string $compare  Optional. The comparison operator.
	 *                                Default '='. Accepts '=', '!=', '>', '>=', '<', '<=', 'IN', 'NOT IN',
	 *                                'BETWEEN', 'NOT BETWEEN'.
	 *         @type string $relation Optional. The boolean relationship between the date queryies.
	 *                                Default 'OR'. Accepts 'OR', 'AND'.
	 *         @type array {
	 *             @type string|array $before Optional. Date to retrieve posts before. Accepts strtotime()-compatible
	 *                                        string, or array of 'year', 'month', 'day' values. {
	 *
	 *                 @type string $year  The four-digit year. Default empty. Accepts any four-digit year.
	 *                 @type string $month Optional when passing array.The month of the year.
	 *                                     Default (string:empty)|(array:1). Accepts numbers 1-12.
	 *                 @type string $day   Optional when passing array.The day of the month.
	 *                                     Default (string:empty)|(array:1). Accepts numbers 1-31.
	 *             }
	 *             @type string|array $after Optional. Date to retrieve posts after. Accepts strtotime()-compatible
	 *                                       string, or array of 'year', 'month', 'day' values. {
	 *
	 *                 @type string $year  The four-digit year. Default empty. Accepts any four-digit year.
	 *                 @type string $month Optional when passing array.The month of the year.
	 *                                     Default (string:empty)|(array:12). Accepts numbers 1-12.
	 *                 @type string $day   Optional when passing array.The day of the month.
	 *                                     Default (string:empty)|(array:last day of month). Accepts numbers 1-31.
	 *             }
	 *             @type string       $column    Optional. Used to add a clause comparing a column other than the column
	 *                                           specified in the top-level $column parameter.  Default is the value
	 *                                           of top-level $column. Accepts 'post_date', 'post_date_gmt',
	 *                                           'post_modified', 'post_modified_gmt', 'comment_date', 'comment_date_gmt'.
	 *             @type string       $compare   Optional. The comparison operator. Default '='. Accepts '=', '!=',
	 *                                           '>', '>=', '<', '<=', 'IN', 'NOT IN', 'BETWEEN', 'NOT BETWEEN'.
	 *             @type bool         $inclusive Optional. Include results from dates specified in 'before' or 'after'.
	 *                                           Default. Accepts.
	 *             @type int          $year      Optional. The four-digit year number. Default empty. Accepts any
	 *                                           four-digit year.
	 *             @type int          $month     Optional. The two-digit month number. Default empty. Accepts numbers 1-12.
	 *             @type int          $week      Optional. The week number of the year. Default empty. Accepts numbers 0-53.
	 *             @type int          $dayofyear Optional. The day number of the year. Default empty. Accepts numbers 1-366.
	 *             @type int          $day       Optional. The day of the month. Default empty. Accepts numbers 1-31.
	 *             @type int          $dayofweek Optional. The day number of the week. Default empty. Accepts numbers 1-7.
	 *             @type int          $hour      Optional. The hour of the day. Default empty. Accepts numbers 0-23.
	 *             @type int          $minute    Optional. The minute of the hour. Default empty. Accepts numbers 0-60.
	 *             @type int          $second    Optional. The second of the minute. Default empty. Accepts numbers 0-60.
	 *         }
	 *     }
	 * }
	 * @param array $default_column Optional. Default column to query against. Default 'post_date'.
	 *                              Accepts 'post_date', 'post_date_gmt', 'post_modified', 'post_modified_gmt',
	 *                              'comment_date', 'comment_date_gmt'.
	 */
	public function __construct( $date_query, $default_column = 'post_date' ) {
		if ( empty( $date_query ) || ! is_array( $date_query ) )
			return;

		if ( isset( $date_query['relation'] ) && strtoupper( $date_query['relation'] ) == 'OR' )
			$this->relation = 'OR';
		else
			$this->relation = 'AND';

		if ( ! empty( $date_query['column'] ) )
			$this->column = esc_sql( $date_query['column'] );
		else
			$this->column = esc_sql( $default_column );

		$this->column = $this->validate_column( $this->column );

		$this->compare = $this->get_compare( $date_query );

		// If an array of arrays wasn't passed, fix it
		if ( ! isset( $date_query[0] ) )
			$date_query = array( $date_query );

		$this->queries = array();
		foreach ( $date_query as $key => $query ) {
			if ( ! is_array( $query ) )
				continue;

			$this->queries[$key] = $query;
		}
	}

	/**
	 * Determines and validates what comparison operator to use.
	 *
	 * @since 3.7.0
	 * @access public
	 *
	 * @param array $query A date query or a date subquery
	 * @return string The comparison operator
	 */
	public function get_compare( $query ) {
		if ( ! empty( $query['compare'] ) && in_array( $query['compare'], array( '=', '!=', '>', '>=', '<', '<=', 'IN', 'NOT IN', 'BETWEEN', 'NOT BETWEEN' ) ) )
			return strtoupper( $query['compare'] );

		return $this->compare;
	}

	/**
	 * Validates a column name parameter.
	 *
	 * @since 3.7.0
	 * @access public
	 *
	 * @param string $column The user-supplied column name.
	 * @return string A validated column name value.
	 */
	public function validate_column( $column ) {
		$valid_columns = array(
			'post_date', 'post_date_gmt', 'post_modified',
			'post_modified_gmt', 'comment_date', 'comment_date_gmt'
		);
		/**
		 * Filter the list of valid date query columns.
		 *
		 * @since 3.7.0
		 *
		 * @param array $valid_columns An array of valid date query columns. Defaults are 'post_date', 'post_date_gmt',
		 *                             'post_modified', 'post_modified_gmt', 'comment_date', 'comment_date_gmt'
		 */
		if ( ! in_array( $column, apply_filters( 'date_query_valid_columns', $valid_columns ) ) )
			$column = 'post_date';

		return $column;
	}

	/**
	 * Turns an array of date query parameters into a MySQL string.
	 *
	 * @since 3.7.0
	 * @access public
	 *
	 * @return string MySQL WHERE parameters
	 */
	public function get_sql() {
		// The parts of the final query
		$where = array();

		foreach ( $this->queries as $key => $query ) {
			$where_parts = $this->get_sql_for_subquery( $query );
			if ( $where_parts ) {
				// Combine the parts of this subquery into a single string
				$where[ $key ] = '( ' . implode( ' AND ', $where_parts ) . ' )';
			}
		}

		// Combine the subquery strings into a single string
		if ( $where )
			$where = ' AND ( ' . implode( " {$this->relation} ", $where ) . ' )';
		else
			$where = '';

		/**
		 * Filter the date query WHERE clause.
		 *
		 * @since 3.7.0
		 *
		 * @param string        $where WHERE clause of the date query.
		 * @param WP_Date_Query $this  The WP_Date_Query instance.
		 */
		return apply_filters( 'get_date_sql', $where, $this );
	}

	/**
	 * Turns a single date subquery into pieces for a WHERE clause.
	 *
	 * @since 3.7.0
	 * return array
	 */
	protected function get_sql_for_subquery( $query ) {
		global $wpdb;

		// The sub-parts of a $where part
		$where_parts = array();

		$column = ( ! empty( $query['column'] ) ) ? esc_sql( $query['column'] ) : $this->column;

		$column = $this->validate_column( $column );

		$compare = $this->get_compare( $query );

		$inclusive = ! empty( $query['inclusive'] );

		// Assign greater- and less-than values.
		$lt = '<';
		$gt = '>';

		if ( $inclusive ) {
			$lt .= '=';
			$gt .= '=';
		}

		// Range queries
		if ( ! empty( $query['after'] ) )
			$where_parts[] = $wpdb->prepare( "$column $gt %s", $this->build_mysql_datetime( $query['after'], ! $inclusive ) );

		if ( ! empty( $query['before'] ) )
			$where_parts[] = $wpdb->prepare( "$column $lt %s", $this->build_mysql_datetime( $query['before'], $inclusive ) );

		// Specific value queries

		if ( isset( $query['year'] ) && $value = $this->build_value( $compare, $query['year'] ) )
			$where_parts[] = "YEAR( $column ) $compare $value";

		if ( isset( $query['month'] ) && $value = $this->build_value( $compare, $query['month'] ) )
			$where_parts[] = "MONTH( $column ) $compare $value";
		else if ( isset( $query['monthnum'] ) && $value = $this->build_value( $compare, $query['monthnum'] ) )
			$where_parts[] = "MONTH( $column ) $compare $value";

		if ( isset( $query['week'] ) && false !== ( $value = $this->build_value( $compare, $query['week'] ) ) )
			$where_parts[] = _wp_mysql_week( $column ) . " $compare $value";
		else if ( isset( $query['w'] ) && false !== ( $value = $this->build_value( $compare, $query['w'] ) ) )
			$where_parts[] = _wp_mysql_week( $column ) . " $compare $value";

		if ( isset( $query['dayofyear'] ) && $value = $this->build_value( $compare, $query['dayofyear'] ) )
			$where_parts[] = "DAYOFYEAR( $column ) $compare $value";

		if ( isset( $query['day'] ) && $value = $this->build_value( $compare, $query['day'] ) )
			$where_parts[] = "DAYOFMONTH( $column ) $compare $value";

		if ( isset( $query['dayofweek'] ) && $value = $this->build_value( $compare, $query['dayofweek'] ) )
			$where_parts[] = "DAYOFWEEK( $column ) $compare $value";

		if ( isset( $query['hour'] ) || isset( $query['minute'] ) || isset( $query['second'] ) ) {
			// Avoid notices
			foreach ( array( 'hour', 'minute', 'second' ) as $unit ) {
				if ( ! isset( $query[$unit] ) ) {
					$query[$unit] = null;
				}
			}

			if ( $time_query = $this->build_time_query( $column, $compare, $query['hour'], $query['minute'], $query['second'] ) ) {
				$where_parts[] = $time_query;
			}
		}

		return $where_parts;
	}

	/**
	 * Builds and validates a value string based on the comparison operator.
	 *
	 * @since 3.7.0
	 * @access public
	 *
	 * @param string $compare The compare operator to use
	 * @param string|array $value The value
	 * @return string|int|false The value to be used in SQL or false on error.
	 */
	public function build_value( $compare, $value ) {
		if ( ! isset( $value ) )
			return false;

		switch ( $compare ) {
			case 'IN':
			case 'NOT IN':
				return '(' . implode( ',', array_map( 'intval', (array) $value ) ) . ')';

			case 'BETWEEN':
			case 'NOT BETWEEN':
				if ( ! is_array( $value ) || 2 != count( $value ) || ! isset( $value[0] ) || ! isset( $value[1] ) )
					$value = array( $value, $value );

				$value = array_map( 'intval', $value );

				return $value[0] . ' AND ' . $value[1];

			default;
				return (int) $value;
		}
	}

	/**
	 * Builds a MySQL format date/time based on some query parameters.
	 *
	 * You can pass an array of values (year, month, etc.) with missing parameter values being defaulted to
	 * either the maximum or minimum values (controlled by the $default_to parameter). Alternatively you can
	 * pass a string that that will be run through strtotime().
	 *
	 * @since 3.7.0
	 * @access public
	 *
	 * @param string|array $datetime An array of parameters or a strotime() string
	 * @param string $default_to Controls what values default to if they are missing from $datetime. Pass "min" or "max".
	 * @return string|false A MySQL format date/time or false on failure
	 */
	public function build_mysql_datetime( $datetime, $default_to_max = false ) {
		$now = current_time( 'timestamp' );

		if ( ! is_array( $datetime ) ) {
			// @todo Timezone issues here possibly
			return gmdate( 'Y-m-d H:i:s', strtotime( $datetime, $now ) );
		}

		$datetime = array_map( 'absint', $datetime );

		if ( ! isset( $datetime['year'] ) )
			$datetime['year'] = gmdate( 'Y', $now );

		if ( ! isset( $datetime['month'] ) )
			$datetime['month'] = ( $default_to_max ) ? 12 : 1;

		if ( ! isset( $datetime['day'] ) )
			$datetime['day'] = ( $default_to_max ) ? (int) date( 't', mktime( 0, 0, 0, $datetime['month'], 1, $datetime['year'] ) ) : 1;

		if ( ! isset( $datetime['hour'] ) )
			$datetime['hour'] = ( $default_to_max ) ? 23 : 0;

		if ( ! isset( $datetime['minute'] ) )
			$datetime['minute'] = ( $default_to_max ) ? 59 : 0;

		if ( ! isset( $datetime['second'] ) )
			$datetime['second'] = ( $default_to_max ) ? 59 : 0;

		return sprintf( '%04d-%02d-%02d %02d:%02d:%02d', $datetime['year'], $datetime['month'], $datetime['day'], $datetime['hour'], $datetime['minute'], $datetime['second'] );
	}

	/**
	 * Builds a query string for comparing time values (hour, minute, second).
	 *
	 * If just hour, minute, or second is set than a normal comparison will be done.
	 * However if multiple values are passed, a pseudo-decimal time will be created
	 * in order to be able to accurately compare against.
	 *
	 * @since 3.7.0
	 * @access public
	 *
	 * @param string $column The column to query against. Needs to be pre-validated!
	 * @param string $compare The comparison operator. Needs to be pre-validated!
	 * @param int|null $hour Optional. An hour value (0-23).
	 * @param int|null $minute Optional. A minute value (0-59).
	 * @param int|null $second Optional. A second value (0-59).
	 * @return string|false A query part or false on failure.
	 */
	public function build_time_query( $column, $compare, $hour = null, $minute = null, $second = null ) {
		global $wpdb;

		// Have to have at least one
		if ( ! isset( $hour ) && ! isset( $minute ) && ! isset( $second ) )
			return false;

		// Complex combined queries aren't supported for multi-value queries
		if ( in_array( $compare, array( 'IN', 'NOT IN', 'BETWEEN', 'NOT BETWEEN' ) ) ) {
			$return = array();

			if ( isset( $hour ) && false !== ( $value = $this->build_value( $compare, $hour ) ) )
				$return[] = "HOUR( $column ) $compare $value";

			if ( isset( $minute ) && false !== ( $value = $this->build_value( $compare, $minute ) ) )
				$return[] = "MINUTE( $column ) $compare $value";

			if ( isset( $second ) && false !== ( $value = $this->build_value( $compare, $second ) ) )
				$return[] = "SECOND( $column ) $compare $value";

			return implode( ' AND ', $return );
		}

		// Cases where just one unit is set
		if ( isset( $hour ) && ! isset( $minute ) && ! isset( $second ) && false !== ( $value = $this->build_value( $compare, $hour ) ) ) {
			return "HOUR( $column ) $compare $value";
		} elseif ( ! isset( $hour ) && isset( $minute ) && ! isset( $second ) && false !== ( $value = $this->build_value( $compare, $minute ) ) ) {
			return "MINUTE( $column ) $compare $value";
		} elseif ( ! isset( $hour ) && ! isset( $minute ) && isset( $second ) && false !== ( $value = $this->build_value( $compare, $second ) ) ) {
			return "SECOND( $column ) $compare $value";
		}

		// Single units were already handled. Since hour & second isn't allowed, minute must to be set.
		if ( ! isset( $minute ) )
			return false;

		$format = $time = '';

		// Hour
		if ( $hour ) {
			$format .= '%H.';
			$time   .= sprintf( '%02d', $hour ) . '.';
		} else {
			$format .= '0.';
			$time   .= '0.';
		}

		// Minute
		$format .= '%i';
		$time   .= sprintf( '%02d', $minute );

		if ( isset( $second ) ) {
			$format .= '%s';
			$time   .= sprintf( '%02d', $second );
		}

		return $wpdb->prepare( "DATE_FORMAT( $column, %s ) $compare %f", $format, $time );
	}
}
