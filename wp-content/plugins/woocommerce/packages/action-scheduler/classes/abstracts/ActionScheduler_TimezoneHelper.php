<?php

/**
 * Class ActionScheduler_TimezoneHelper
 */
abstract class ActionScheduler_TimezoneHelper {
	private static $local_timezone = NULL;

	/**
	 * Set a DateTime's timezone to the WordPress site's timezone, or a UTC offset
	 * if no timezone string is available.
	 *
	 * @since  2.1.0
	 *
	 * @param DateTime $date
	 * @return ActionScheduler_DateTime
	 */
	public static function set_local_timezone( DateTime $date ) {

		// Accept a DateTime for easier backward compatibility, even though we require methods on ActionScheduler_DateTime
		if ( ! is_a( $date, 'ActionScheduler_DateTime' ) ) {
			$date = as_get_datetime_object( $date->format( 'U' ) );
		}

		if ( get_option( 'timezone_string' ) ) {
			$date->setTimezone( new DateTimeZone( self::get_local_timezone_string() ) );
		} else {
			$date->setUtcOffset( self::get_local_timezone_offset() );
		}

		return $date;
	}

	/**
	 * Helper to retrieve the timezone string for a site until a WP core method exists
	 * (see https://core.trac.wordpress.org/ticket/24730).
	 *
	 * Adapted from wc_timezone_string() and https://secure.php.net/manual/en/function.timezone-name-from-abbr.php#89155.
	 *
	 * If no timezone string is set, and its not possible to match the UTC offset set for the site to a timezone
	 * string, then an empty string will be returned, and the UTC offset should be used to set a DateTime's
	 * timezone.
	 *
	 * @since 2.1.0
	 * @return string PHP timezone string for the site or empty if no timezone string is available.
	 */
	protected static function get_local_timezone_string( $reset = false ) {
		// If site timezone string exists, return it.
		$timezone = get_option( 'timezone_string' );
		if ( $timezone ) {
			return $timezone;
		}

		// Get UTC offset, if it isn't set then return UTC.
		$utc_offset = intval( get_option( 'gmt_offset', 0 ) );
		if ( 0 === $utc_offset ) {
			return 'UTC';
		}

		// Adjust UTC offset from hours to seconds.
		$utc_offset *= 3600;

		// Attempt to guess the timezone string from the UTC offset.
		$timezone = timezone_name_from_abbr( '', $utc_offset );
		if ( $timezone ) {
			return $timezone;
		}

		// Last try, guess timezone string manually.
		foreach ( timezone_abbreviations_list() as $abbr ) {
			foreach ( $abbr as $city ) {
				if ( (bool) date( 'I' ) === (bool) $city['dst'] && $city['timezone_id'] && intval( $city['offset'] ) === $utc_offset ) {
					return $city['timezone_id'];
				}
			}
		}

		// No timezone string
		return '';
	}

	/**
	 * Get timezone offset in seconds.
	 *
	 * @since  2.1.0
	 * @return float
	 */
	protected static function get_local_timezone_offset() {
		$timezone = get_option( 'timezone_string' );

		if ( $timezone ) {
			$timezone_object = new DateTimeZone( $timezone );
			return $timezone_object->getOffset( new DateTime( 'now' ) );
		} else {
			return floatval( get_option( 'gmt_offset', 0 ) ) * HOUR_IN_SECONDS;
		}
	}

	/**
	 * @deprecated 2.1.0
	 */
	public static function get_local_timezone( $reset = FALSE ) {
		_deprecated_function( __FUNCTION__, '2.1.0', 'ActionScheduler_TimezoneHelper::set_local_timezone()' );
		if ( $reset ) {
			self::$local_timezone = NULL;
		}
		if ( !isset(self::$local_timezone) ) {
			$tzstring = get_option('timezone_string');

			if ( empty($tzstring) ) {
				$gmt_offset = get_option('gmt_offset');
				if ( $gmt_offset == 0 ) {
					$tzstring = 'UTC';
				} else {
					$gmt_offset *= HOUR_IN_SECONDS;
					$tzstring   = timezone_name_from_abbr( '', $gmt_offset, 1 );

					// If there's no timezone string, try again with no DST.
					if ( false === $tzstring ) {
						$tzstring = timezone_name_from_abbr( '', $gmt_offset, 0 );
					}

					// Try mapping to the first abbreviation we can find.
					if ( false === $tzstring ) {
						$is_dst = date( 'I' );
						foreach ( timezone_abbreviations_list() as $abbr ) {
							foreach ( $abbr as $city ) {
								if ( $city['dst'] == $is_dst && $city['offset'] == $gmt_offset ) {
									// If there's no valid timezone ID, keep looking.
									if ( null === $city['timezone_id'] ) {
										continue;
									}

									$tzstring = $city['timezone_id'];
									break 2;
								}
							}
						}
					}

					// If we still have no valid string, then fall back to UTC.
					if ( false === $tzstring ) {
						$tzstring = 'UTC';
					}
				}
			}

			self::$local_timezone = new DateTimeZone($tzstring);
		}
		return self::$local_timezone;
	}
}
