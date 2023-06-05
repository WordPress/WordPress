<?php
/**
 * Class for time interval and numeric range handling for reports.
 */

namespace Automattic\WooCommerce\Admin\API\Reports;

defined( 'ABSPATH' ) || exit;

/**
 * Date & time interval and numeric range handling class for Reporting API.
 */
class TimeInterval {

	/**
	 * Format string for ISO DateTime formatter.
	 *
	 * @var string
	 */
	public static $iso_datetime_format = 'Y-m-d\TH:i:s';

	/**
	 * Format string for use in SQL queries.
	 *
	 * @var string
	 */
	public static $sql_datetime_format = 'Y-m-d H:i:s';

	/**
	 * Converts local datetime to GMT/UTC time.
	 *
	 * @param string $datetime_string String representation of local datetime.
	 * @return DateTime
	 */
	public static function convert_local_datetime_to_gmt( $datetime_string ) {
		$datetime = new \DateTime( $datetime_string, new \DateTimeZone( wc_timezone_string() ) );
		$datetime->setTimezone( new \DateTimeZone( 'GMT' ) );
		return $datetime;
	}

	/**
	 * Returns default 'before' parameter for the reports.
	 *
	 * @return DateTime
	 */
	public static function default_before() {
		$datetime = new \WC_DateTime();
		// Set local timezone or offset.
		if ( get_option( 'timezone_string' ) ) {
			$datetime->setTimezone( new \DateTimeZone( wc_timezone_string() ) );
		} else {
			$datetime->set_utc_offset( wc_timezone_offset() );
		}
		return $datetime;
	}

	/**
	 * Returns default 'after' parameter for the reports.
	 *
	 * @return DateTime
	 */
	public static function default_after() {
		$now       = time();
		$week_back = $now - WEEK_IN_SECONDS;

		$datetime = new \WC_DateTime();
		$datetime->setTimestamp( $week_back );
		// Set local timezone or offset.
		if ( get_option( 'timezone_string' ) ) {
			$datetime->setTimezone( new \DateTimeZone( wc_timezone_string() ) );
		} else {
			$datetime->set_utc_offset( wc_timezone_offset() );
		}
		return $datetime;
	}

	/**
	 * Returns date format to be used as grouping clause in SQL.
	 *
	 * @param string $time_interval Time interval.
	 * @param string $table_name Name of the db table relevant for the date constraint.
	 * @param string $date_column_name Name of the date table column.
	 * @return mixed
	 */
	public static function db_datetime_format( $time_interval, $table_name, $date_column_name = 'date_created' ) {
		$first_day_of_week = absint( get_option( 'start_of_week' ) );

		if ( 1 === $first_day_of_week ) {
			// Week begins on Monday, ISO 8601.
			$week_format = "DATE_FORMAT({$table_name}.`{$date_column_name}`, '%x-%v')";
		} else {
			// Week begins on day other than specified by ISO 8601, needs to be in sync with function simple_week_number.
			$week_format = "CONCAT(YEAR({$table_name}.`{$date_column_name}`), '-', LPAD( FLOOR( ( DAYOFYEAR({$table_name}.`{$date_column_name}`) + ( ( DATE_FORMAT(MAKEDATE(YEAR({$table_name}.`{$date_column_name}`),1), '%w') - $first_day_of_week + 7 ) % 7 ) - 1 ) / 7  ) + 1 , 2, '0'))";

		}

		// Whenever this is changed, double check method time_interval_id to make sure they are in sync.
		$mysql_date_format_mapping = array(
			'hour'    => "DATE_FORMAT({$table_name}.`{$date_column_name}`, '%Y-%m-%d %H')",
			'day'     => "DATE_FORMAT({$table_name}.`{$date_column_name}`, '%Y-%m-%d')",
			'week'    => $week_format,
			'month'   => "DATE_FORMAT({$table_name}.`{$date_column_name}`, '%Y-%m')",
			'quarter' => "CONCAT(YEAR({$table_name}.`{$date_column_name}`), '-', QUARTER({$table_name}.`{$date_column_name}`))",
			'year'    => "YEAR({$table_name}.`{$date_column_name}`)",

		);

		return $mysql_date_format_mapping[ $time_interval ];
	}

	/**
	 * Returns quarter for the DateTime.
	 *
	 * @param DateTime $datetime Local date & time.
	 * @return int|null
	 */
	public static function quarter( $datetime ) {
		switch ( (int) $datetime->format( 'm' ) ) {
			case 1:
			case 2:
			case 3:
				return 1;
			case 4:
			case 5:
			case 6:
				return 2;
			case 7:
			case 8:
			case 9:
				return 3;
			case 10:
			case 11:
			case 12:
				return 4;

		}
		return null;
	}

	/**
	 * Returns simple week number for the DateTime, for week starting on $first_day_of_week.
	 *
	 * The first week of the year is considered to be the week containing January 1.
	 * The second week starts on the next $first_day_of_week.
	 *
	 * @param DateTime $datetime          Local date for which the week number is to be calculated.
	 * @param int      $first_day_of_week 0 for Sunday to 6 for Saturday.
	 * @return int
	 */
	public static function simple_week_number( $datetime, $first_day_of_week ) {
		$beg_of_year_day          = new \DateTime( "{$datetime->format('Y')}-01-01" );
		$adj_day_beg_of_year      = ( (int) $beg_of_year_day->format( 'w' ) - $first_day_of_week + 7 ) % 7;
		$days_since_start_of_year = (int) $datetime->format( 'z' ) + 1;

		return (int) floor( ( ( $days_since_start_of_year + $adj_day_beg_of_year - 1 ) / 7 ) ) + 1;
	}

	/**
	 * Returns ISO 8601 week number for the DateTime, if week starts on Monday,
	 * otherwise returns simple week number.
	 *
	 * @see TimeInterval::simple_week_number()
	 *
	 * @param DateTime $datetime          Local date for which the week number is to be calculated.
	 * @param int      $first_day_of_week 0 for Sunday to 6 for Saturday.
	 * @return int
	 */
	public static function week_number( $datetime, $first_day_of_week ) {
		if ( 1 === $first_day_of_week ) {
			$week_number = (int) $datetime->format( 'W' );
		} else {
			$week_number = self::simple_week_number( $datetime, $first_day_of_week );
		}
		return $week_number;
	}

	/**
	 * Returns time interval id for the DateTime.
	 *
	 * @param string   $time_interval Time interval type (week, day, etc).
	 * @param DateTime $datetime      Date & time.
	 * @return string
	 */
	public static function time_interval_id( $time_interval, $datetime ) {
		// Whenever this is changed, double check method db_datetime_format to make sure they are in sync.
		$php_time_format_for = array(
			'hour'    => 'Y-m-d H',
			'day'     => 'Y-m-d',
			'week'    => 'o-W',
			'month'   => 'Y-m',
			'quarter' => 'Y-' . self::quarter( $datetime ),
			'year'    => 'Y',
		);

		// If the week does not begin on Monday.
		$first_day_of_week = absint( get_option( 'start_of_week' ) );

		if ( 'week' === $time_interval && 1 !== $first_day_of_week ) {
			$week_no = self::simple_week_number( $datetime, $first_day_of_week );
			$week_no = str_pad( $week_no, 2, '0', STR_PAD_LEFT );
			$year_no = $datetime->format( 'Y' );
			return "$year_no-$week_no";
		}

		return $datetime->format( $php_time_format_for[ $time_interval ] );
	}

	/**
	 * Calculates number of time intervals between two dates, closed interval on both sides.
	 *
	 * @param DateTime $start_datetime Start date & time.
	 * @param DateTime $end_datetime End date & time.
	 * @param string   $interval Time interval increment, e.g. hour, day, week.
	 *
	 * @return int
	 */
	public static function intervals_between( $start_datetime, $end_datetime, $interval ) {
		switch ( $interval ) {
			case 'hour':
				$end_timestamp   = (int) $end_datetime->format( 'U' );
				$start_timestamp = (int) $start_datetime->format( 'U' );
				$addendum        = 0;
				// modulo HOUR_IN_SECONDS would normally work, but there are non-full hour timezones, e.g. Nepal.
				$start_min_sec = (int) $start_datetime->format( 'i' ) * MINUTE_IN_SECONDS + (int) $start_datetime->format( 's' );
				$end_min_sec   = (int) $end_datetime->format( 'i' ) * MINUTE_IN_SECONDS + (int) $end_datetime->format( 's' );
				if ( $end_min_sec < $start_min_sec ) {
					$addendum = 1;
				}
				$diff_timestamp = $end_timestamp - $start_timestamp;

				return (int) floor( ( (int) $diff_timestamp ) / HOUR_IN_SECONDS ) + 1 + $addendum;
			case 'day':
				$days               = $start_datetime->diff( $end_datetime )->format( '%r%a' );
				$end_hour_min_sec   = (int) $end_datetime->format( 'H' ) * HOUR_IN_SECONDS + (int) $end_datetime->format( 'i' ) * MINUTE_IN_SECONDS + (int) $end_datetime->format( 's' );
				$start_hour_min_sec = (int) $start_datetime->format( 'H' ) * HOUR_IN_SECONDS + (int) $start_datetime->format( 'i' ) * MINUTE_IN_SECONDS + (int) $start_datetime->format( 's' );
				if ( $end_hour_min_sec < $start_hour_min_sec ) {
					$days++;
				}

				return $days + 1;
			case 'week':
				// @todo Optimize? approximately day count / 7, but year end is tricky, a week can have fewer days.
				$week_count = 0;
				do {
					$start_datetime = self::next_week_start( $start_datetime );
					$week_count++;
				} while ( $start_datetime <= $end_datetime );
				return $week_count;
			case 'month':
				// Year diff in months: (end_year - start_year - 1) * 12.
				$year_diff_in_months = ( (int) $end_datetime->format( 'Y' ) - (int) $start_datetime->format( 'Y' ) - 1 ) * 12;
				// All the months in end_date year plus months from X to 12 in the start_date year.
				$month_diff = (int) $end_datetime->format( 'n' ) + ( 12 - (int) $start_datetime->format( 'n' ) );
				// Add months for number of years between end_date and start_date.
				$month_diff += $year_diff_in_months + 1;
				return $month_diff;
			case 'quarter':
				// Year diff in quarters: (end_year - start_year - 1) * 4.
				$year_diff_in_quarters = ( (int) $end_datetime->format( 'Y' ) - (int) $start_datetime->format( 'Y' ) - 1 ) * 4;
				// All the quarters in end_date year plus quarters from X to 4 in the start_date year.
				$quarter_diff = self::quarter( $end_datetime ) + ( 4 - self::quarter( $start_datetime ) );
				// Add quarters for number of years between end_date and start_date.
				$quarter_diff += $year_diff_in_quarters + 1;
				return $quarter_diff;
			case 'year':
				$year_diff = (int) $end_datetime->format( 'Y' ) - (int) $start_datetime->format( 'Y' );
				return $year_diff + 1;
		}
		return 0;
	}

	/**
	 * Returns a new DateTime object representing the next hour start/previous hour end if reversed.
	 *
	 * @param DateTime $datetime Date and time.
	 * @param bool     $reversed Going backwards in time instead of forward.
	 * @return DateTime
	 */
	public static function next_hour_start( $datetime, $reversed = false ) {
		$hour_increment         = $reversed ? 0 : 1;
		$timestamp              = (int) $datetime->format( 'U' );
		$seconds_into_hour      = (int) $datetime->format( 'i' ) * MINUTE_IN_SECONDS + (int) $datetime->format( 's' );
		$hours_offset_timestamp = $timestamp + ( $hour_increment * HOUR_IN_SECONDS - $seconds_into_hour );

		if ( $reversed ) {
			$hours_offset_timestamp --;
		}

		$hours_offset_time = new \DateTime();
		$hours_offset_time->setTimestamp( $hours_offset_timestamp );
		$hours_offset_time->setTimezone( new \DateTimeZone( wc_timezone_string() ) );
		return $hours_offset_time;
	}

	/**
	 * Returns a new DateTime object representing the next day start, or previous day end if reversed.
	 *
	 * @param DateTime $datetime Date and time.
	 * @param bool     $reversed Going backwards in time instead of forward.
	 * @return DateTime
	 */
	public static function next_day_start( $datetime, $reversed = false ) {
		$oneday       = new \DateInterval( 'P1D' );
		$new_datetime = clone $datetime;

		if ( $reversed ) {
			$new_datetime->sub( $oneday );
			$new_datetime->setTime( 23, 59, 59 );
		} else {
			$new_datetime->add( $oneday );
			$new_datetime->setTime( 0, 0, 0 );
		}

		return $new_datetime;
	}

	/**
	 * Returns DateTime object representing the next week start, or previous week end if reversed.
	 *
	 * The next week start is the first day of the next week at 00:00:00.
	 * The previous week end is the last day of the previous week at 23:59:59.
	 * The start day is determined by the "start_of_week" wp_option.
	 *
	 * @param DateTime $datetime Date and time.
	 * @param bool     $reversed Going backwards in time instead of forward.
	 * @return DateTime
	 */
	public static function next_week_start( $datetime, $reversed = false ) {
		$seven_days = new \DateInterval( 'P7D' );
		// Default timezone set in wp-settings.php.
		$default_timezone = date_default_timezone_get();
		// Timezone that the WP site uses in Settings > General.
		$original_timezone = $datetime->getTimezone();
		// @codingStandardsIgnoreStart
		date_default_timezone_set( 'UTC' );
		$start_end_timestamp  = get_weekstartend( $datetime->format( 'Y-m-d' ) );
		date_default_timezone_set( $default_timezone );
		// @codingStandardsIgnoreEnd
		if ( $reversed ) {
			$result = \DateTime::createFromFormat( 'U', $start_end_timestamp['end'] )->sub( $seven_days );
		} else {
			$result = \DateTime::createFromFormat( 'U', $start_end_timestamp['start'] )->add( $seven_days );
		}
		return \DateTime::createFromFormat( 'Y-m-d H:i:s', $result->format( 'Y-m-d H:i:s' ), $original_timezone );
	}


	/**
	 * Returns a new DateTime object representing the next month start, or previous month end if reversed.
	 *
	 * @param DateTime $datetime Date and time.
	 * @param bool     $reversed Going backwards in time instead of forward.
	 * @return DateTime
	 */
	public static function next_month_start( $datetime, $reversed = false ) {
		$month_increment = 1;
		$year            = $datetime->format( 'Y' );
		$month           = (int) $datetime->format( 'm' );

		if ( $reversed ) {
			$beg_of_month_datetime       = new \DateTime( "$year-$month-01 00:00:00", new \DateTimeZone( wc_timezone_string() ) );
			$timestamp                   = (int) $beg_of_month_datetime->format( 'U' );
			$end_of_prev_month_timestamp = $timestamp - 1;
			$datetime->setTimestamp( $end_of_prev_month_timestamp );
		} else {
			$month += $month_increment;
			if ( $month > 12 ) {
				$month = 1;
				$year ++;
			}
			$day      = '01';
			$datetime = new \DateTime( "$year-$month-$day 00:00:00", new \DateTimeZone( wc_timezone_string() ) );
		}

		return $datetime;
	}

	/**
	 * Returns a new DateTime object representing the next quarter start, or previous quarter end if reversed.
	 *
	 * @param DateTime $datetime Date and time.
	 * @param bool     $reversed Going backwards in time instead of forward.
	 * @return DateTime
	 */
	public static function next_quarter_start( $datetime, $reversed = false ) {
		$year  = $datetime->format( 'Y' );
		$month = (int) $datetime->format( 'n' );

		switch ( $month ) {
			case 1:
			case 2:
			case 3:
				if ( $reversed ) {
					$month = 1;
				} else {
					$month = 4;
				}
				break;
			case 4:
			case 5:
			case 6:
				if ( $reversed ) {
					$month = 4;
				} else {
					$month = 7;
				}
				break;
			case 7:
			case 8:
			case 9:
				if ( $reversed ) {
					$month = 7;
				} else {
					$month = 10;
				}
				break;
			case 10:
			case 11:
			case 12:
				if ( $reversed ) {
					$month = 10;
				} else {
					$month = 1;
					$year ++;
				}
				break;
		}
		$datetime = new \DateTime( "$year-$month-01 00:00:00", new \DateTimeZone( wc_timezone_string() ) );
		if ( $reversed ) {
			$timestamp                   = (int) $datetime->format( 'U' );
			$end_of_prev_month_timestamp = $timestamp - 1;
			$datetime->setTimestamp( $end_of_prev_month_timestamp );
		}

		return $datetime;
	}

	/**
	 * Return a new DateTime object representing the next year start, or previous year end if reversed.
	 *
	 * @param DateTime $datetime Date and time.
	 * @param bool     $reversed Going backwards in time instead of forward.
	 * @return DateTime
	 */
	public static function next_year_start( $datetime, $reversed = false ) {
		$year_increment = 1;
		$year           = (int) $datetime->format( 'Y' );
		$month          = '01';
		$day            = '01';

		if ( $reversed ) {
			$datetime                   = new \DateTime( "$year-$month-$day 00:00:00", new \DateTimeZone( wc_timezone_string() ) );
			$timestamp                  = (int) $datetime->format( 'U' );
			$end_of_prev_year_timestamp = $timestamp - 1;
			$datetime->setTimestamp( $end_of_prev_year_timestamp );
		} else {
			$year    += $year_increment;
			$datetime = new \DateTime( "$year-$month-$day 00:00:00", new \DateTimeZone( wc_timezone_string() ) );
		}

		return $datetime;
	}

	/**
	 * Returns beginning of next time interval for provided DateTime.
	 *
	 * E.g. for current DateTime, beginning of next day, week, quarter, etc.
	 *
	 * @param DateTime $datetime      Date and time.
	 * @param string   $time_interval Time interval, e.g. week, day, hour.
	 * @param bool     $reversed Going backwards in time instead of forward.
	 * @return DateTime
	 */
	public static function iterate( $datetime, $time_interval, $reversed = false ) {
		return call_user_func( array( __CLASS__, "next_{$time_interval}_start" ), $datetime, $reversed );
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
	public static function expected_intervals_on_page( $expected_interval_count, $items_per_page, $page_no ) {
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
	public static function intervals_missing( $expected_interval_count, $db_records, $items_per_page, $page_no, $order, $order_by, $intervals_count ) {
		if ( $expected_interval_count <= $db_records ) {
			return false;
		}
		if ( 'date' === $order_by ) {
			$expected_intervals_on_page = self::expected_intervals_on_page( $expected_interval_count, $items_per_page, $page_no );
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
	 * Normalize "*_between" parameters to "*_min" and "*_max" for numeric values
	 * and "*_after" and "*_before" for date values.
	 *
	 * @param array        $request Query params from REST API request.
	 * @param string|array $param_names One or more param names to handle. Should not include "_between" suffix.
	 * @param bool         $is_date Boolean if the param is date is related.
	 * @return array Normalized query values.
	 */
	public static function normalize_between_params( $request, $param_names, $is_date ) {
		if ( ! is_array( $param_names ) ) {
			$param_names = array( $param_names );
		}

		$normalized = array();

		foreach ( $param_names as $param_name ) {
			if ( ! is_array( $request[ $param_name . '_between' ] ) ) {
				continue;
			}

			$range = $request[ $param_name . '_between' ];

			if ( 2 !== count( $range ) ) {
				continue;
			}

			$min = $is_date ? '_after' : '_min';
			$max = $is_date ? '_before' : '_max';

			if ( $range[0] < $range[1] ) {
				$normalized[ $param_name . $min ] = $range[0];
				$normalized[ $param_name . $max ] = $range[1];
			} else {
				$normalized[ $param_name . $min ] = $range[1];
				$normalized[ $param_name . $max ] = $range[0];
			}
		}

		return $normalized;
	}

	/**
	 * Validate a "*_between" range argument (an array with 2 numeric items).
	 *
	 * @param  mixed           $value Parameter value.
	 * @param  WP_REST_Request $request REST Request.
	 * @param  string          $param Parameter name.
	 * @return WP_Error|boolean
	 */
	public static function rest_validate_between_numeric_arg( $value, $request, $param ) {
		if ( ! wp_is_numeric_array( $value ) ) {
			return new \WP_Error(
				'rest_invalid_param',
				/* translators: 1: parameter name */
				sprintf( __( '%1$s is not a numerically indexed array.', 'woocommerce' ), $param )
			);
		}

		if (
			2 !== count( $value ) ||
			! is_numeric( $value[0] ) ||
			! is_numeric( $value[1] )
		) {
			return new \WP_Error(
				'rest_invalid_param',
				/* translators: %s: parameter name */
				sprintf( __( '%s must contain 2 numbers.', 'woocommerce' ), $param )
			);
		}

		return true;
	}

	/**
	 * Validate a "*_between" range argument (an array with 2 date items).
	 *
	 * @param  mixed           $value Parameter value.
	 * @param  WP_REST_Request $request REST Request.
	 * @param  string          $param Parameter name.
	 * @return WP_Error|boolean
	 */
	public static function rest_validate_between_date_arg( $value, $request, $param ) {
		if ( ! wp_is_numeric_array( $value ) ) {
			return new \WP_Error(
				'rest_invalid_param',
				/* translators: 1: parameter name */
				sprintf( __( '%1$s is not a numerically indexed array.', 'woocommerce' ), $param )
			);
		}

		if (
			2 !== count( $value ) ||
			! rest_parse_date( $value[0] ) ||
			! rest_parse_date( $value[1] )
		) {
			return new \WP_Error(
				'rest_invalid_param',
				/* translators: %s: parameter name */
				sprintf( __( '%s must contain 2 valid dates.', 'woocommerce' ), $param )
			);
		}

		return true;
	}

	/**
	 * Get dates from a timeframe string.
	 *
	 * @param int           $timeframe Timeframe to use.  One of: last_week|last_month|last_quarter|last_6_months|last_year.
	 * @param DateTime|null $current_date DateTime of current date to compare.
	 * @return array
	 */
	public static function get_timeframe_dates( $timeframe, $current_date = null ) {
		if ( ! $current_date ) {
			$current_date = new \DateTime();
		}
		$current_year  = $current_date->format( 'Y' );
		$current_month = $current_date->format( 'm' );

		if ( 'last_week' === $timeframe ) {
			return array(
				'start' => $current_date->modify( 'last week monday' )->format( 'Y-m-d 00:00:00' ),
				'end'   => $current_date->modify( 'this sunday' )->format( 'Y-m-d 23:59:59' ),
			);
		}

		if ( 'last_month' === $timeframe ) {
			return array(
				'start' => $current_date->modify( 'first day of previous month' )->format( 'Y-m-d 00:00:00' ),
				'end'   => $current_date->modify( 'last day of this month' )->format( 'Y-m-d 23:59:59' ),
			);
		}

		if ( 'last_quarter' === $timeframe ) {
			switch ( $current_month ) {
				case $current_month >= 1 && $current_month <= 3:
					return array(
						'start' => ( $current_year - 1 ) . '-10-01 00:00:00',
						'end'   => ( $current_year - 1 ) . '-12-31 23:59:59',
					);
				case $current_month >= 4 && $current_month <= 6:
					return array(
						'start' => $current_year . '-01-01 00:00:00',
						'end'   => $current_year . '-03-31 23:59:59',
					);
				case $current_month >= 7 && $current_month <= 9:
					return array(
						'start' => $current_year . '-04-01 00:00:00',
						'end'   => $current_year . '-06-30 23:59:59',
					);
				case $current_month >= 10 && $current_month <= 12:
					return array(
						'start' => $current_year . '-07-01 00:00:00',
						'end'   => $current_year . '-09-31 23:59:59',
					);
			}
		}

		if ( 'last_6_months' === $timeframe ) {
			if ( $current_month >= 1 && $current_month <= 6 ) {
				return array(
					'start' => ( $current_year - 1 ) . '-07-01 00:00:00',
					'end'   => ( $current_year - 1 ) . '-12-31 23:59:59',
				);
			}
			return array(
				'start' => $current_year . '-01-01 00:00:00',
				'end'   => $current_year . '-06-30 23:59:59',
			);
		}

		if ( 'last_year' === $timeframe ) {
			return array(
				'start' => ( $current_year - 1 ) . '-01-01 00:00:00',
				'end'   => ( $current_year - 1 ) . '-12-31 23:59:59',
			);
		}

		return false;
	}
}
