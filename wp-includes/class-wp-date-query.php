<?php
/**
 * Class for generating SQL clauses that filter a primary query according to date.
 *
 * WP_Date_Query is a helper that allows primary query classes, such as WP_Query, to filter
 * their results by date columns, by generating `WHERE` subclauses to be attached to the
 * primary SQL query string.
 *
 * Attempting to filter by an invalid date value (eg month=13) will generate SQL that will
 * return no results. In these cases, a _doing_it_wrong() error notice is also thrown.
 * See WP_Date_Query::validate_date_values().
 *
 * @link https://developer.wordpress.org/reference/classes/wp_query/
 *
 * @since 3.7.0
 */
class WP_Date_Query {
	/**
	 * Array of date queries.
	 *
	 * See WP_Date_Query::__construct() for information on date query arguments.
	 *
	 * @since 3.7.0
	 * @var array
	 */
	public $queries = array();

	/**
	 * The default relation between top-level queries. Can be either 'AND' or 'OR'.
	 *
	 * @since 3.7.0
	 * @var string
	 */
	public $relation = 'AND';

	/**
	 * The column to query against. Can be changed via the query arguments.
	 *
	 * @since 3.7.0
	 * @var string
	 */
	public $column = 'post_date';

	/**
	 * The value comparison operator. Can be changed via the query arguments.
	 *
	 * @since 3.7.0
	 * @var string
	 */
	public $compare = '=';

	/**
	 * Supported time-related parameter keys.
	 *
	 * @since 4.1.0
	 * @var array
	 */
	public $time_keys = array( 'after', 'before', 'year', 'month', 'monthnum', 'week', 'w', 'dayofyear', 'day', 'dayofweek', 'dayofweek_iso', 'hour', 'minute', 'second' );

	/**
	 * Constructor.
	 *
	 * Time-related parameters that normally require integer values ('year', 'month', 'week', 'dayofyear', 'day',
	 * 'dayofweek', 'dayofweek_iso', 'hour', 'minute', 'second') accept arrays of integers for some values of
	 * 'compare'. When 'compare' is 'IN' or 'NOT IN', arrays are accepted; when 'compare' is 'BETWEEN' or 'NOT
	 * BETWEEN', arrays of two valid values are required. See individual argument descriptions for accepted values.
	 *
	 * @since 3.7.0
	 * @since 4.0.0 The $inclusive logic was updated to include all times within the date range.
	 * @since 4.1.0 Introduced 'dayofweek_iso' time type parameter.
	 *
	 * @param array  $date_query {
	 *     Array of date query clauses.
	 *
	 *     @type array ...$0 {
	 *         @type string $column   Optional. The column to query against. If undefined, inherits the value of
	 *                                the `$default_column` parameter. See WP_Date_Query::validate_column() and
	 *                                the {@see 'date_query_valid_columns'} filter for the list of accepted values.
	 *                                Default 'post_date'.
	 *         @type string $compare  Optional. The comparison operator. Accepts '=', '!=', '>', '>=', '<', '<=',
	 *                                'IN', 'NOT IN', 'BETWEEN', 'NOT BETWEEN'. Default '='.
	 *         @type string $relation Optional. The boolean relationship between the date queries. Accepts 'OR' or 'AND'.
	 *                                Default 'OR'.
	 *         @type array  ...$0 {
	 *             Optional. An array of first-order clause parameters, or another fully-formed date query.
	 *
	 *             @type string|array $before {
	 *                 Optional. Date to retrieve posts before. Accepts `strtotime()`-compatible string,
	 *                 or array of 'year', 'month', 'day' values.
	 *
	 *                 @type string $year  The four-digit year. Default empty. Accepts any four-digit year.
	 *                 @type string $month Optional when passing array.The month of the year.
	 *                                     Default (string:empty)|(array:1). Accepts numbers 1-12.
	 *                 @type string $day   Optional when passing array.The day of the month.
	 *                                     Default (string:empty)|(array:1). Accepts numbers 1-31.
	 *             }
	 *             @type string|array $after {
	 *                 Optional. Date to retrieve posts after. Accepts `strtotime()`-compatible string,
	 *                 or array of 'year', 'month', 'day' values.
	 *
	 *                 @type string $year  The four-digit year. Accepts any four-digit year. Default empty.
	 *                 @type string $month Optional when passing array. The month of the year. Accepts numbers 1-12.
	 *                                     Default (string:empty)|(array:12).
	 *                 @type string $day   Optional when passing array.The day of the month. Accepts numbers 1-31.
	 *                                     Default (string:empty)|(array:last day of month).
	 *             }
	 *             @type string       $column        Optional. Used to add a clause comparing a column other than
	 *                                               the column specified in the top-level `$column` parameter.
	 *                                               See WP_Date_Query::validate_column() and
	 *                                               the {@see 'date_query_valid_columns'} filter for the list
	 *                                               of accepted values. Default is the value of top-level `$column`.
	 *             @type string       $compare       Optional. The comparison operator. Accepts '=', '!=', '>', '>=',
	 *                                               '<', '<=', 'IN', 'NOT IN', 'BETWEEN', 'NOT BETWEEN'. 'IN',
	 *                                               'NOT IN', 'BETWEEN', and 'NOT BETWEEN'. Comparisons support
	 *                                               arrays in some time-related parameters. Default '='.
	 *             @type bool         $inclusive     Optional. Include results from dates specified in 'before' or
	 *                                               'after'. Default false.
	 *             @type int|int[]    $year          Optional. The four-digit year number. Accepts any four-digit year
	 *                                               or an array of years if `$compare` supports it. Default empty.
	 *             @type int|int[]    $month         Optional. The two-digit month number. Accepts numbers 1-12 or an
	 *                                               array of valid numbers if `$compare` supports it. Default empty.
	 *             @type int|int[]    $week          Optional. The week number of the year. Accepts numbers 0-53 or an
	 *                                               array of valid numbers if `$compare` supports it. Default empty.
	 *             @type int|int[]    $dayofyear     Optional. The day number of the year. Accepts numbers 1-366 or an
	 *                                               array of valid numbers if `$compare` supports it.
	 *             @type int|int[]    $day           Optional. The day of the month. Accepts numbers 1-31 or an array
	 *                                               of valid numbers if `$compare` supports it. Default empty.
	 *             @type int|int[]    $dayofweek     Optional. The day number of the week. Accepts numbers 1-7 (1 is
	 *                                               Sunday) or an array of valid numbers if `$compare` supports it.
	 *                                               Default empty.
	 *             @type int|int[]    $dayofweek_iso Optional. The day number of the week (ISO). Accepts numbers 1-7
	 *                                               (1 is Monday) or an array of valid numbers if `$compare` supports it.
	 *                                               Default empty.
	 *             @type int|int[]    $hour          Optional. The hour of the day. Accepts numbers 0-23 or an array
	 *                                               of valid numbers if `$compare` supports it. Default empty.
	 *             @type int|int[]    $minute        Optional. The minute of the hour. Accepts numbers 0-59 or an array
	 *                                               of valid numbers if `$compare` supports it. Default empty.
	 *             @type int|int[]    $second        Optional. The second of the minute. Accepts numbers 0-59 or an
	 *                                               array of valid numbers if `$compare` supports it. Default empty.
	 *         }
	 *     }
	 * }
	 * @param string $default_column Optional. Default column to query against. See WP_Date_Query::validate_column()
	 *                               and the {@see 'date_query_valid_columns'} filter for the list of accepted values.
	 *                               Default 'post_date'.
	 */
	public function __construct( $date_query, $default_column = 'post_date' ) {
		if ( empty( $date_query ) || ! is_array( $date_query ) ) {
			return;
		}

		if ( isset( $date_query['relation'] ) && 'OR' === strtoupper( $date_query['relation'] ) ) {
			$this->relation = 'OR';
		} else {
			$this->relation = 'AND';
		}

		// Support for passing time-based keys in the top level of the $date_query array.
		if ( ! isset( $date_query[0] ) ) {
			$date_query = array( $date_query );
		}

		if ( ! empty( $date_query['column'] ) ) {
			$date_query['column'] = esc_sql( $date_query['column'] );
		} else {
			$date_query['column'] = esc_sql( $default_column );
		}

		$this->column = $this->validate_column( $this->column );

		$this->compare = $this->get_compare( $date_query );

		$this->queries = $this->sanitize_query( $date_query );
	}

	/**
	 * Recursive-friendly query sanitizer.
	 *
	 * Ensures that each query-level clause has a 'relation' key, and that
	 * each first-order clause contains all the necessary keys from `$defaults`.
	 *
	 * @since 4.1.0
	 *
	 * @param array $queries
	 * @param array $parent_query
	 * @return array Sanitized queries.
	 */
	public function sanitize_query( $queries, $parent_query = null ) {
		$cleaned_query = array();

		$defaults = array(
			'column'   => 'post_date',
			'compare'  => '=',
			'relation' => 'AND',
		);

		// Numeric keys should always have array values.
		foreach ( $queries as $qkey => $qvalue ) {
			if ( is_numeric( $qkey ) && ! is_array( $qvalue ) ) {
				unset( $queries[ $qkey ] );
			}
		}

		// Each query should have a value for each default key. Inherit from the parent when possible.
		foreach ( $defaults as $dkey => $dvalue ) {
			if ( isset( $queries[ $dkey ] ) ) {
				continue;
			}

			if ( isset( $parent_query[ $dkey ] ) ) {
				$queries[ $dkey ] = $parent_query[ $dkey ];
			} else {
				$queries[ $dkey ] = $dvalue;
			}
		}

		// Validate the dates passed in the query.
		if ( $this->is_first_order_clause( $queries ) ) {
			$this->validate_date_values( $queries );
		}

		foreach ( $queries as $key => $q ) {
			if ( ! is_array( $q ) || in_array( $key, $this->time_keys, true ) ) {
				// This is a first-order query. Trust the values and sanitize when building SQL.
				$cleaned_query[ $key ] = $q;
			} else {
				// Any array without a time key is another query, so we recurse.
				$cleaned_query[] = $this->sanitize_query( $q, $queries );
			}
		}

		return $cleaned_query;
	}

	/**
	 * Determine whether this is a first-order clause.
	 *
	 * Checks to see if the current clause has any time-related keys.
	 * If so, it's first-order.
	 *
	 * @since 4.1.0
	 *
	 * @param array $query Query clause.
	 * @return bool True if this is a first-order clause.
	 */
	protected function is_first_order_clause( $query ) {
		$time_keys = array_intersect( $this->time_keys, array_keys( $query ) );
		return ! empty( $time_keys );
	}

	/**
	 * Determines and validates what comparison operator to use.
	 *
	 * @since 3.7.0
	 *
	 * @param array $query A date query or a date subquery.
	 * @return string The comparison operator.
	 */
	public function get_compare( $query ) {
		if ( ! empty( $query['compare'] )
			&& in_array( $query['compare'], array( '=', '!=', '>', '>=', '<', '<=', 'IN', 'NOT IN', 'BETWEEN', 'NOT BETWEEN' ), true )
		) {
			return strtoupper( $query['compare'] );
		}

		return $this->compare;
	}

	/**
	 * Validates the given date_query values and triggers errors if something is not valid.
	 *
	 * Note that date queries with invalid date ranges are allowed to
	 * continue (though of course no items will be found for impossible dates).
	 * This method only generates debug notices for these cases.
	 *
	 * @since 4.1.0
	 *
	 * @param array $date_query The date_query array.
	 * @return bool  True if all values in the query are valid, false if one or more fail.
	 */
	public function validate_date_values( $date_query = array() ) {
		if ( empty( $date_query ) ) {
			return false;
		}

		$valid = true;

		/*
		 * Validate 'before' and 'after' up front, then let the
		 * validation routine continue to be sure that all invalid
		 * values generate errors too.
		 */
		if ( array_key_exists( 'before', $date_query ) && is_array( $date_query['before'] ) ) {
			$valid = $this->validate_date_values( $date_query['before'] );
		}

		if ( array_key_exists( 'after', $date_query ) && is_array( $date_query['after'] ) ) {
			$valid = $this->validate_date_values( $date_query['after'] );
		}

		// Array containing all min-max checks.
		$min_max_checks = array();

		// Days per year.
		if ( array_key_exists( 'year', $date_query ) ) {
			/*
			 * If a year exists in the date query, we can use it to get the days.
			 * If multiple years are provided (as in a BETWEEN), use the first one.
			 */
			if ( is_array( $date_query['year'] ) ) {
				$_year = reset( $date_query['year'] );
			} else {
				$_year = $date_query['year'];
			}

			$max_days_of_year = gmdate( 'z', mktime( 0, 0, 0, 12, 31, $_year ) ) + 1;
		} else {
			// Otherwise we use the max of 366 (leap-year).
			$max_days_of_year = 366;
		}

		$min_max_checks['dayofyear'] = array(
			'min' => 1,
			'max' => $max_days_of_year,
		);

		// Days per week.
		$min_max_checks['dayofweek'] = array(
			'min' => 1,
			'max' => 7,
		);

		// Days per week.
		$min_max_checks['dayofweek_iso'] = array(
			'min' => 1,
			'max' => 7,
		);

		// Months per year.
		$min_max_checks['month'] = array(
			'min' => 1,
			'max' => 12,
		);

		// Weeks per year.
		if ( isset( $_year ) ) {
			/*
			 * If we have a specific year, use it to calculate number of weeks.
			 * Note: the number of weeks in a year is the date in which Dec 28 appears.
			 */
			$week_count = gmdate( 'W', mktime( 0, 0, 0, 12, 28, $_year ) );

		} else {
			// Otherwise set the week-count to a maximum of 53.
			$week_count = 53;
		}

		$min_max_checks['week'] = array(
			'min' => 1,
			'max' => $week_count,
		);

		// Days per month.
		$min_max_checks['day'] = array(
			'min' => 1,
			'max' => 31,
		);

		// Hours per day.
		$min_max_checks['hour'] = array(
			'min' => 0,
			'max' => 23,
		);

		// Minutes per hour.
		$min_max_checks['minute'] = array(
			'min' => 0,
			'max' => 59,
		);

		// Seconds per minute.
		$min_max_checks['second'] = array(
			'min' => 0,
			'max' => 59,
		);

		// Concatenate and throw a notice for each invalid value.
		foreach ( $min_max_checks as $key => $check ) {
			if ( ! array_key_exists( $key, $date_query ) ) {
				continue;
			}

			// Throw a notice for each failing value.
			foreach ( (array) $date_query[ $key ] as $_value ) {
				$is_between = $_value >= $check['min'] && $_value <= $check['max'];

				if ( ! is_numeric( $_value ) || ! $is_between ) {
					$error = sprintf(
						/* translators: Date query invalid date message. 1: Invalid value, 2: Type of value, 3: Minimum valid value, 4: Maximum valid value. */
						__( 'Invalid value %1$s for %2$s. Expected value should be between %3$s and %4$s.' ),
						'<code>' . esc_html( $_value ) . '</code>',
						'<code>' . esc_html( $key ) . '</code>',
						'<code>' . esc_html( $check['min'] ) . '</code>',
						'<code>' . esc_html( $check['max'] ) . '</code>'
					);

					_doing_it_wrong( __CLASS__, $error, '4.1.0' );

					$valid = false;
				}
			}
		}

		// If we already have invalid date messages, don't bother running through checkdate().
		if ( ! $valid ) {
			return $valid;
		}

		$day_month_year_error_msg = '';

		$day_exists   = array_key_exists( 'day', $date_query ) && is_numeric( $date_query['day'] );
		$month_exists = array_key_exists( 'month', $date_query ) && is_numeric( $date_query['month'] );
		$year_exists  = array_key_exists( 'year', $date_query ) && is_numeric( $date_query['year'] );

		if ( $day_exists && $month_exists && $year_exists ) {
			// 1. Checking day, month, year combination.
			if ( ! wp_checkdate( $date_query['month'], $date_query['day'], $date_query['year'], sprintf( '%s-%s-%s', $date_query['year'], $date_query['month'], $date_query['day'] ) ) ) {
				$day_month_year_error_msg = sprintf(
					/* translators: 1: Year, 2: Month, 3: Day of month. */
					__( 'The following values do not describe a valid date: year %1$s, month %2$s, day %3$s.' ),
					'<code>' . esc_html( $date_query['year'] ) . '</code>',
					'<code>' . esc_html( $date_query['month'] ) . '</code>',
					'<code>' . esc_html( $date_query['day'] ) . '</code>'
				);

				$valid = false;
			}
		} elseif ( $day_exists && $month_exists ) {
			/*
			 * 2. checking day, month combination
			 * We use 2012 because, as a leap year, it's the most permissive.
			 */
			if ( ! wp_checkdate( $date_query['month'], $date_query['day'], 2012, sprintf( '2012-%s-%s', $date_query['month'], $date_query['day'] ) ) ) {
				$day_month_year_error_msg = sprintf(
					/* translators: 1: Month, 2: Day of month. */
					__( 'The following values do not describe a valid date: month %1$s, day %2$s.' ),
					'<code>' . esc_html( $date_query['month'] ) . '</code>',
					'<code>' . esc_html( $date_query['day'] ) . '</code>'
				);

				$valid = false;
			}
		}

		if ( ! empty( $day_month_year_error_msg ) ) {
			_doing_it_wrong( __CLASS__, $day_month_year_error_msg, '4.1.0' );
		}

		return $valid;
	}

	/**
	 * Validates a column name parameter.
	 *
	 * Column names without a table prefix (like 'post_date') are checked against a list of
	 * allowed and known tables, and then, if found, have a table prefix (such as 'wp_posts.')
	 * prepended. Prefixed column names (such as 'wp_posts.post_date') bypass this allowed
	 * check, and are only sanitized to remove illegal characters.
	 *
	 * @since 3.7.0
	 *
	 * @param string $column The user-supplied column name.
	 * @return string A validated column name value.
	 */
	public function validate_column( $column ) {
		global $wpdb;

		$valid_columns = array(
			'post_date',
			'post_date_gmt',
			'post_modified',
			'post_modified_gmt',
			'comment_date',
			'comment_date_gmt',
			'user_registered',
			'registered',
			'last_updated',
		);

		// Attempt to detect a table prefix.
		if ( false === strpos( $column, '.' ) ) {
			/**
			 * Filters the list of valid date query columns.
			 *
			 * @since 3.7.0
			 * @since 4.1.0 Added 'user_registered' to the default recognized columns.
			 * @since 4.6.0 Added 'registered' and 'last_updated' to the default recognized columns.
			 *
			 * @param string[] $valid_columns An array of valid date query columns. Defaults
			 *                                are 'post_date', 'post_date_gmt', 'post_modified',
			 *                                'post_modified_gmt', 'comment_date', 'comment_date_gmt',
			 *                                'user_registered', 'registered', 'last_updated'.
			 */
			if ( ! in_array( $column, apply_filters( 'date_query_valid_columns', $valid_columns ), true ) ) {
				$column = 'post_date';
			}

			$known_columns = array(
				$wpdb->posts    => array(
					'post_date',
					'post_date_gmt',
					'post_modified',
					'post_modified_gmt',
				),
				$wpdb->comments => array(
					'comment_date',
					'comment_date_gmt',
				),
				$wpdb->users    => array(
					'user_registered',
				),
				$wpdb->blogs    => array(
					'registered',
					'last_updated',
				),
			);

			// If it's a known column name, add the appropriate table prefix.
			foreach ( $known_columns as $table_name => $table_columns ) {
				if ( in_array( $column, $table_columns, true ) ) {
					$column = $table_name . '.' . $column;
					break;
				}
			}
		}

		// Remove unsafe characters.
		return preg_replace( '/[^a-zA-Z0-9_$\.]/', '', $column );
	}

	/**
	 * Generate WHERE clause to be appended to a main query.
	 *
	 * @since 3.7.0
	 *
	 * @return string MySQL WHERE clause.
	 */
	public function get_sql() {
		$sql = $this->get_sql_clauses();

		$where = $sql['where'];

		/**
		 * Filters the date query WHERE clause.
		 *
		 * @since 3.7.0
		 *
		 * @param string        $where WHERE clause of the date query.
		 * @param WP_Date_Query $query The WP_Date_Query instance.
		 */
		return apply_filters( 'get_date_sql', $where, $this );
	}

	/**
	 * Generate SQL clauses to be appended to a main query.
	 *
	 * Called by the public WP_Date_Query::get_sql(), this method is abstracted
	 * out to maintain parity with the other Query classes.
	 *
	 * @since 4.1.0
	 *
	 * @return string[] {
	 *     Array containing JOIN and WHERE SQL clauses to append to the main query.
	 *
	 *     @type string $join  SQL fragment to append to the main JOIN clause.
	 *     @type string $where SQL fragment to append to the main WHERE clause.
	 * }
	 */
	protected function get_sql_clauses() {
		$sql = $this->get_sql_for_query( $this->queries );

		if ( ! empty( $sql['where'] ) ) {
			$sql['where'] = ' AND ' . $sql['where'];
		}

		return $sql;
	}

	/**
	 * Generate SQL clauses for a single query array.
	 *
	 * If nested subqueries are found, this method recurses the tree to
	 * produce the properly nested SQL.
	 *
	 * @since 4.1.0
	 *
	 * @param array $query Query to parse.
	 * @param int   $depth Optional. Number of tree levels deep we currently are.
	 *                     Used to calculate indentation. Default 0.
	 * @return array {
	 *     Array containing JOIN and WHERE SQL clauses to append to a single query array.
	 *
	 *     @type string $join  SQL fragment to append to the main JOIN clause.
	 *     @type string $where SQL fragment to append to the main WHERE clause.
	 * }
	 */
	protected function get_sql_for_query( $query, $depth = 0 ) {
		$sql_chunks = array(
			'join'  => array(),
			'where' => array(),
		);

		$sql = array(
			'join'  => '',
			'where' => '',
		);

		$indent = '';
		for ( $i = 0; $i < $depth; $i++ ) {
			$indent .= '  ';
		}

		foreach ( $query as $key => $clause ) {
			if ( 'relation' === $key ) {
				$relation = $query['relation'];
			} elseif ( is_array( $clause ) ) {

				// This is a first-order clause.
				if ( $this->is_first_order_clause( $clause ) ) {
					$clause_sql = $this->get_sql_for_clause( $clause, $query );

					$where_count = count( $clause_sql['where'] );
					if ( ! $where_count ) {
						$sql_chunks['where'][] = '';
					} elseif ( 1 === $where_count ) {
						$sql_chunks['where'][] = $clause_sql['where'][0];
					} else {
						$sql_chunks['where'][] = '( ' . implode( ' AND ', $clause_sql['where'] ) . ' )';
					}

					$sql_chunks['join'] = array_merge( $sql_chunks['join'], $clause_sql['join'] );
					// This is a subquery, so we recurse.
				} else {
					$clause_sql = $this->get_sql_for_query( $clause, $depth + 1 );

					$sql_chunks['where'][] = $clause_sql['where'];
					$sql_chunks['join'][]  = $clause_sql['join'];
				}
			}
		}

		// Filter to remove empties.
		$sql_chunks['join']  = array_filter( $sql_chunks['join'] );
		$sql_chunks['where'] = array_filter( $sql_chunks['where'] );

		if ( empty( $relation ) ) {
			$relation = 'AND';
		}

		// Filter duplicate JOIN clauses and combine into a single string.
		if ( ! empty( $sql_chunks['join'] ) ) {
			$sql['join'] = implode( ' ', array_unique( $sql_chunks['join'] ) );
		}

		// Generate a single WHERE clause with proper brackets and indentation.
		if ( ! empty( $sql_chunks['where'] ) ) {
			$sql['where'] = '( ' . "\n  " . $indent . implode( ' ' . "\n  " . $indent . $relation . ' ' . "\n  " . $indent, $sql_chunks['where'] ) . "\n" . $indent . ')';
		}

		return $sql;
	}

	/**
	 * Turns a single date clause into pieces for a WHERE clause.
	 *
	 * A wrapper for get_sql_for_clause(), included here for backward
	 * compatibility while retaining the naming convention across Query classes.
	 *
	 * @since 3.7.0
	 *
	 * @param array $query Date query arguments.
	 * @return string[] {
	 *     Array containing JOIN and WHERE SQL clauses to append to the main query.
	 *
	 *     @type string $join  SQL fragment to append to the main JOIN clause.
	 *     @type string $where SQL fragment to append to the main WHERE clause.
	 * }
	 */
	protected function get_sql_for_subquery( $query ) {
		return $this->get_sql_for_clause( $query, '' );
	}

	/**
	 * Turns a first-order date query into SQL for a WHERE clause.
	 *
	 * @since 4.1.0
	 *
	 * @param array $query        Date query clause.
	 * @param array $parent_query Parent query of the current date query.
	 * @return string[] {
	 *     Array containing JOIN and WHERE SQL clauses to append to the main query.
	 *
	 *     @type string $join  SQL fragment to append to the main JOIN clause.
	 *     @type string $where SQL fragment to append to the main WHERE clause.
	 * }
	 */
	protected function get_sql_for_clause( $query, $parent_query ) {
		global $wpdb;

		// The sub-parts of a $where part.
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

		// Range queries.
		if ( ! empty( $query['after'] ) ) {
			$where_parts[] = $wpdb->prepare( "$column $gt %s", $this->build_mysql_datetime( $query['after'], ! $inclusive ) );
		}
		if ( ! empty( $query['before'] ) ) {
			$where_parts[] = $wpdb->prepare( "$column $lt %s", $this->build_mysql_datetime( $query['before'], $inclusive ) );
		}
		// Specific value queries.

		$date_units = array(
			'YEAR'           => array( 'year' ),
			'MONTH'          => array( 'month', 'monthnum' ),
			'_wp_mysql_week' => array( 'week', 'w' ),
			'DAYOFYEAR'      => array( 'dayofyear' ),
			'DAYOFMONTH'     => array( 'day' ),
			'DAYOFWEEK'      => array( 'dayofweek' ),
			'WEEKDAY'        => array( 'dayofweek_iso' ),
		);

		// Check of the possible date units and add them to the query.
		foreach ( $date_units as $sql_part => $query_parts ) {
			foreach ( $query_parts as $query_part ) {
				if ( isset( $query[ $query_part ] ) ) {
					$value = $this->build_value( $compare, $query[ $query_part ] );
					if ( $value ) {
						switch ( $sql_part ) {
							case '_wp_mysql_week':
								$where_parts[] = _wp_mysql_week( $column ) . " $compare $value";
								break;
							case 'WEEKDAY':
								$where_parts[] = "$sql_part( $column ) + 1 $compare $value";
								break;
							default:
								$where_parts[] = "$sql_part( $column ) $compare $value";
						}

						break;
					}
				}
			}
		}

		if ( isset( $query['hour'] ) || isset( $query['minute'] ) || isset( $query['second'] ) ) {
			// Avoid notices.
			foreach ( array( 'hour', 'minute', 'second' ) as $unit ) {
				if ( ! isset( $query[ $unit ] ) ) {
					$query[ $unit ] = null;
				}
			}

			$time_query = $this->build_time_query( $column, $compare, $query['hour'], $query['minute'], $query['second'] );
			if ( $time_query ) {
				$where_parts[] = $time_query;
			}
		}

		/*
		 * Return an array of 'join' and 'where' for compatibility
		 * with other query classes.
		 */
		return array(
			'where' => $where_parts,
			'join'  => array(),
		);
	}

	/**
	 * Builds and validates a value string based on the comparison operator.
	 *
	 * @since 3.7.0
	 *
	 * @param string       $compare The compare operator to use.
	 * @param string|array $value   The value.
	 * @return string|false|int The value to be used in SQL or false on error.
	 */
	public function build_value( $compare, $value ) {
		if ( ! isset( $value ) ) {
			return false;
		}

		switch ( $compare ) {
			case 'IN':
			case 'NOT IN':
				$value = (array) $value;

				// Remove non-numeric values.
				$value = array_filter( $value, 'is_numeric' );

				if ( empty( $value ) ) {
					return false;
				}

				return '(' . implode( ',', array_map( 'intval', $value ) ) . ')';

			case 'BETWEEN':
			case 'NOT BETWEEN':
				if ( ! is_array( $value ) || 2 !== count( $value ) ) {
					$value = array( $value, $value );
				} else {
					$value = array_values( $value );
				}

				// If either value is non-numeric, bail.
				foreach ( $value as $v ) {
					if ( ! is_numeric( $v ) ) {
						return false;
					}
				}

				$value = array_map( 'intval', $value );

				return $value[0] . ' AND ' . $value[1];

			default:
				if ( ! is_numeric( $value ) ) {
					return false;
				}

				return (int) $value;
		}
	}

	/**
	 * Builds a MySQL format date/time based on some query parameters.
	 *
	 * You can pass an array of values (year, month, etc.) with missing parameter values being defaulted to
	 * either the maximum or minimum values (controlled by the $default_to parameter). Alternatively you can
	 * pass a string that will be passed to date_create().
	 *
	 * @since 3.7.0
	 *
	 * @param string|array $datetime       An array of parameters or a strotime() string
	 * @param bool         $default_to_max Whether to round up incomplete dates. Supported by values
	 *                                     of $datetime that are arrays, or string values that are a
	 *                                     subset of MySQL date format ('Y', 'Y-m', 'Y-m-d', 'Y-m-d H:i').
	 *                                     Default: false.
	 * @return string|false A MySQL format date/time or false on failure
	 */
	public function build_mysql_datetime( $datetime, $default_to_max = false ) {
		if ( ! is_array( $datetime ) ) {

			/*
			 * Try to parse some common date formats, so we can detect
			 * the level of precision and support the 'inclusive' parameter.
			 */
			if ( preg_match( '/^(\d{4})$/', $datetime, $matches ) ) {
				// Y
				$datetime = array(
					'year' => (int) $matches[1],
				);

			} elseif ( preg_match( '/^(\d{4})\-(\d{2})$/', $datetime, $matches ) ) {
				// Y-m
				$datetime = array(
					'year'  => (int) $matches[1],
					'month' => (int) $matches[2],
				);

			} elseif ( preg_match( '/^(\d{4})\-(\d{2})\-(\d{2})$/', $datetime, $matches ) ) {
				// Y-m-d
				$datetime = array(
					'year'  => (int) $matches[1],
					'month' => (int) $matches[2],
					'day'   => (int) $matches[3],
				);

			} elseif ( preg_match( '/^(\d{4})\-(\d{2})\-(\d{2}) (\d{2}):(\d{2})$/', $datetime, $matches ) ) {
				// Y-m-d H:i
				$datetime = array(
					'year'   => (int) $matches[1],
					'month'  => (int) $matches[2],
					'day'    => (int) $matches[3],
					'hour'   => (int) $matches[4],
					'minute' => (int) $matches[5],
				);
			}

			// If no match is found, we don't support default_to_max.
			if ( ! is_array( $datetime ) ) {
				$wp_timezone = wp_timezone();

				// Assume local timezone if not provided.
				$dt = date_create( $datetime, $wp_timezone );

				if ( false === $dt ) {
					return gmdate( 'Y-m-d H:i:s', false );
				}

				return $dt->setTimezone( $wp_timezone )->format( 'Y-m-d H:i:s' );
			}
		}

		$datetime = array_map( 'absint', $datetime );

		if ( ! isset( $datetime['year'] ) ) {
			$datetime['year'] = current_time( 'Y' );
		}

		if ( ! isset( $datetime['month'] ) ) {
			$datetime['month'] = ( $default_to_max ) ? 12 : 1;
		}

		if ( ! isset( $datetime['day'] ) ) {
			$datetime['day'] = ( $default_to_max ) ? (int) gmdate( 't', mktime( 0, 0, 0, $datetime['month'], 1, $datetime['year'] ) ) : 1;
		}

		if ( ! isset( $datetime['hour'] ) ) {
			$datetime['hour'] = ( $default_to_max ) ? 23 : 0;
		}

		if ( ! isset( $datetime['minute'] ) ) {
			$datetime['minute'] = ( $default_to_max ) ? 59 : 0;
		}

		if ( ! isset( $datetime['second'] ) ) {
			$datetime['second'] = ( $default_to_max ) ? 59 : 0;
		}

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
	 *
	 * @param string   $column  The column to query against. Needs to be pre-validated!
	 * @param string   $compare The comparison operator. Needs to be pre-validated!
	 * @param int|null $hour    Optional. An hour value (0-23).
	 * @param int|null $minute  Optional. A minute value (0-59).
	 * @param int|null $second  Optional. A second value (0-59).
	 * @return string|false A query part or false on failure.
	 */
	public function build_time_query( $column, $compare, $hour = null, $minute = null, $second = null ) {
		global $wpdb;

		// Have to have at least one.
		if ( ! isset( $hour ) && ! isset( $minute ) && ! isset( $second ) ) {
			return false;
		}

		// Complex combined queries aren't supported for multi-value queries.
		if ( in_array( $compare, array( 'IN', 'NOT IN', 'BETWEEN', 'NOT BETWEEN' ), true ) ) {
			$return = array();

			$value = $this->build_value( $compare, $hour );
			if ( false !== $value ) {
				$return[] = "HOUR( $column ) $compare $value";
			}

			$value = $this->build_value( $compare, $minute );
			if ( false !== $value ) {
				$return[] = "MINUTE( $column ) $compare $value";
			}

			$value = $this->build_value( $compare, $second );
			if ( false !== $value ) {
				$return[] = "SECOND( $column ) $compare $value";
			}

			return implode( ' AND ', $return );
		}

		// Cases where just one unit is set.
		if ( isset( $hour ) && ! isset( $minute ) && ! isset( $second ) ) {
			$value = $this->build_value( $compare, $hour );
			if ( false !== $value ) {
				return "HOUR( $column ) $compare $value";
			}
		} elseif ( ! isset( $hour ) && isset( $minute ) && ! isset( $second ) ) {
			$value = $this->build_value( $compare, $minute );
			if ( false !== $value ) {
				return "MINUTE( $column ) $compare $value";
			}
		} elseif ( ! isset( $hour ) && ! isset( $minute ) && isset( $second ) ) {
			$value = $this->build_value( $compare, $second );
			if ( false !== $value ) {
				return "SECOND( $column ) $compare $value";
			}
		}

		// Single units were already handled. Since hour & second isn't allowed, minute must to be set.
		if ( ! isset( $minute ) ) {
			return false;
		}

		$format = '';
		$time   = '';

		// Hour.
		if ( null !== $hour ) {
			$format .= '%H.';
			$time   .= sprintf( '%02d', $hour ) . '.';
		} else {
			$format .= '0.';
			$time   .= '0.';
		}

		// Minute.
		$format .= '%i';
		$time   .= sprintf( '%02d', $minute );

		if ( isset( $second ) ) {
			$format .= '%s';
			$time   .= sprintf( '%02d', $second );
		}

		return $wpdb->prepare( "DATE_FORMAT( $column, %s ) $compare %f", $format, $time );
	}
}
