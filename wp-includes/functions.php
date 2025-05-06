<?php
/**
 * Main WordPress API
 *
 * @package WordPress
 */

// Don't load directly.
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

require ABSPATH . WPINC . '/option.php';

/**
 * Converts given MySQL date string into a different format.
 *
 *  - `$format` should be a PHP date format string.
 *  - 'U' and 'G' formats will return an integer sum of timestamp with timezone offset.
 *  - `$date` is expected to be local time in MySQL format (`Y-m-d H:i:s`).
 *
 * Historically UTC time could be passed to the function to produce Unix timestamp.
 *
 * If `$translate` is true then the given date and format string will
 * be passed to `wp_date()` for translation.
 *
 * @since 0.71
 *
 * @param string $format    Format of the date to return.
 * @param string $date      Date string to convert.
 * @param bool   $translate Whether the return date should be translated. Default true.
 * @return string|int|false Integer if `$format` is 'U' or 'G', string otherwise.
 *                          False on failure.
 */
function mysql2date( $format, $date, $translate = true ) {
	if ( empty( $date ) ) {
		return false;
	}

	$timezone = wp_timezone();
	$datetime = date_create( $date, $timezone );

	if ( false === $datetime ) {
		return false;
	}

	// Returns a sum of timestamp with timezone offset. Ideally should never be used.
	if ( 'G' === $format || 'U' === $format ) {
		return $datetime->getTimestamp() + $datetime->getOffset();
	}

	if ( $translate ) {
		return wp_date( $format, $datetime->getTimestamp(), $timezone );
	}

	return $datetime->format( $format );
}

/**
 * Retrieves the current time based on specified type.
 *
 *  - The 'mysql' type will return the time in the format for MySQL DATETIME field.
 *  - The 'timestamp' or 'U' types will return the current timestamp or a sum of timestamp
 *    and timezone offset, depending on `$gmt`.
 *  - Other strings will be interpreted as PHP date formats (e.g. 'Y-m-d').
 *
 * If `$gmt` is a truthy value then both types will use GMT time, otherwise the
 * output is adjusted with the GMT offset for the site.
 *
 * @since 1.0.0
 * @since 5.3.0 Now returns an integer if `$type` is 'U'. Previously a string was returned.
 *
 * @param string $type Type of time to retrieve. Accepts 'mysql', 'timestamp', 'U',
 *                     or PHP date format string (e.g. 'Y-m-d').
 * @param bool   $gmt  Optional. Whether to use GMT timezone. Default false.
 * @return int|string Integer if `$type` is 'timestamp' or 'U', string otherwise.
 */
function current_time( $type, $gmt = false ) {
	// Don't use non-GMT timestamp, unless you know the difference and really need to.
	if ( 'timestamp' === $type || 'U' === $type ) {
		return $gmt ? time() : time() + (int) ( (float) get_option( 'gmt_offset' ) * HOUR_IN_SECONDS );
	}

	if ( 'mysql' === $type ) {
		$type = 'Y-m-d H:i:s';
	}

	$timezone = $gmt ? new DateTimeZone( 'UTC' ) : wp_timezone();
	$datetime = new DateTime( 'now', $timezone );

	return $datetime->format( $type );
}

/**
 * Retrieves the current time as an object using the site's timezone.
 *
 * @since 5.3.0
 *
 * @return DateTimeImmutable Date and time object.
 */
function current_datetime() {
	return new DateTimeImmutable( 'now', wp_timezone() );
}

/**
 * Retrieves the timezone of the site as a string.
 *
 * Uses the `timezone_string` option to get a proper timezone name if available,
 * otherwise falls back to a manual UTC ± offset.
 *
 * Example return values:
 *
 *  - 'Europe/Rome'
 *  - 'America/North_Dakota/New_Salem'
 *  - 'UTC'
 *  - '-06:30'
 *  - '+00:00'
 *  - '+08:45'
 *
 * @since 5.3.0
 *
 * @return string PHP timezone name or a ±HH:MM offset.
 */
function wp_timezone_string() {
	$timezone_string = get_option( 'timezone_string' );

	if ( $timezone_string ) {
		return $timezone_string;
	}

	$offset  = (float) get_option( 'gmt_offset' );
	$hours   = (int) $offset;
	$minutes = ( $offset - $hours );

	$sign      = ( $offset < 0 ) ? '-' : '+';
	$abs_hour  = abs( $hours );
	$abs_mins  = abs( $minutes * 60 );
	$tz_offset = sprintf( '%s%02d:%02d', $sign, $abs_hour, $abs_mins );

	return $tz_offset;
}

/**
 * Retrieves the timezone of the site as a `DateTimeZone` object.
 *
 * Timezone can be based on a PHP timezone string or a ±HH:MM offset.
 *
 * @since 5.3.0
 *
 * @return DateTimeZone Timezone object.
 */
function wp_timezone() {
	return new DateTimeZone( wp_timezone_string() );
}

/**
 * Retrieves the date in localized format, based on a sum of Unix timestamp and
 * timezone offset in seconds.
 *
 * If the locale specifies the locale month and weekday, then the locale will
 * take over the format for the date. If it isn't, then the date format string
 * will be used instead.
 *
 * Note that due to the way WP typically generates a sum of timestamp and offset
 * with `strtotime()`, it implies offset added at a _current_ time, not at the time
 * the timestamp represents. Storing such timestamps or calculating them differently
 * will lead to invalid output.
 *
 * @since 0.71
 * @since 5.3.0 Converted into a wrapper for wp_date().
 *
 * @param string   $format                Format to display the date.
 * @param int|bool $timestamp_with_offset Optional. A sum of Unix timestamp and timezone offset
 *                                        in seconds. Default false.
 * @param bool     $gmt                   Optional. Whether to use GMT timezone. Only applies
 *                                        if timestamp is not provided. Default false.
 * @return string The date, translated if locale specifies it.
 */
function date_i18n( $format, $timestamp_with_offset = false, $gmt = false ) {
	$timestamp = $timestamp_with_offset;

	// If timestamp is omitted it should be current time (summed with offset, unless `$gmt` is true).
	if ( ! is_numeric( $timestamp ) ) {
		// phpcs:ignore WordPress.DateTime.CurrentTimeTimestamp.Requested
		$timestamp = current_time( 'timestamp', $gmt );
	}

	/*
	 * This is a legacy implementation quirk that the returned timestamp is also with offset.
	 * Ideally this function should never be used to produce a timestamp.
	 */
	if ( 'U' === $format ) {
		$date = $timestamp;
	} elseif ( $gmt && false === $timestamp_with_offset ) { // Current time in UTC.
		$date = wp_date( $format, null, new DateTimeZone( 'UTC' ) );
	} elseif ( false === $timestamp_with_offset ) { // Current time in site's timezone.
		$date = wp_date( $format );
	} else {
		/*
		 * Timestamp with offset is typically produced by a UTC `strtotime()` call on an input without timezone.
		 * This is the best attempt to reverse that operation into a local time to use.
		 */
		$local_time = gmdate( 'Y-m-d H:i:s', $timestamp );
		$timezone   = wp_timezone();
		$datetime   = date_create( $local_time, $timezone );
		$date       = wp_date( $format, $datetime->getTimestamp(), $timezone );
	}

	/**
	 * Filters the date formatted based on the locale.
	 *
	 * @since 2.8.0
	 *
	 * @param string $date      Formatted date string.
	 * @param string $format    Format to display the date.
	 * @param int    $timestamp A sum of Unix timestamp and timezone offset in seconds.
	 *                          Might be without offset if input omitted timestamp but requested GMT.
	 * @param bool   $gmt       Whether to use GMT timezone. Only applies if timestamp was not provided.
	 */
	$date = apply_filters( 'date_i18n', $date, $format, $timestamp, $gmt );

	return $date;
}

/**
 * Retrieves the date, in localized format.
 *
 * This is a newer function, intended to replace `date_i18n()` without legacy quirks in it.
 *
 * Note that, unlike `date_i18n()`, this function accepts a true Unix timestamp, not summed
 * with timezone offset.
 *
 * @since 5.3.0
 *
 * @global WP_Locale $wp_locale WordPress date and time locale object.
 *
 * @param string            $format    PHP date format.
 * @param int|null          $timestamp Optional. Unix timestamp. Defaults to current time.
 * @param DateTimeZone|null $timezone  Optional. Timezone to output result in. Defaults to timezone
 *                                     from site settings.
 * @return string|false The date, translated if locale specifies it. False on invalid timestamp input.
 */
function wp_date( $format, $timestamp = null, $timezone = null ) {
	global $wp_locale;

	if ( null === $timestamp ) {
		$timestamp = time();
	} elseif ( ! is_numeric( $timestamp ) ) {
		return false;
	}

	if ( ! $timezone ) {
		$timezone = wp_timezone();
	}

	$datetime = date_create( '@' . $timestamp );
	$datetime->setTimezone( $timezone );

	if ( empty( $wp_locale->month ) || empty( $wp_locale->weekday ) ) {
		$date = $datetime->format( $format );
	} else {
		// We need to unpack shorthand `r` format because it has parts that might be localized.
		$format = preg_replace( '/(?<!\\\\)r/', DATE_RFC2822, $format );

		$new_format    = '';
		$format_length = strlen( $format );
		$month         = $wp_locale->get_month( $datetime->format( 'm' ) );
		$weekday       = $wp_locale->get_weekday( $datetime->format( 'w' ) );

		for ( $i = 0; $i < $format_length; $i++ ) {
			switch ( $format[ $i ] ) {
				case 'D':
					$new_format .= addcslashes( $wp_locale->get_weekday_abbrev( $weekday ), '\\A..Za..z' );
					break;
				case 'F':
					$new_format .= addcslashes( $month, '\\A..Za..z' );
					break;
				case 'l':
					$new_format .= addcslashes( $weekday, '\\A..Za..z' );
					break;
				case 'M':
					$new_format .= addcslashes( $wp_locale->get_month_abbrev( $month ), '\\A..Za..z' );
					break;
				case 'a':
					$new_format .= addcslashes( $wp_locale->get_meridiem( $datetime->format( 'a' ) ), '\\A..Za..z' );
					break;
				case 'A':
					$new_format .= addcslashes( $wp_locale->get_meridiem( $datetime->format( 'A' ) ), '\\A..Za..z' );
					break;
				case '\\':
					$new_format .= $format[ $i ];

					// If character follows a slash, we add it without translating.
					if ( $i < $format_length ) {
						$new_format .= $format[ ++$i ];
					}
					break;
				default:
					$new_format .= $format[ $i ];
					break;
			}
		}

		$date = $datetime->format( $new_format );
		$date = wp_maybe_decline_date( $date, $format );
	}

	/**
	 * Filters the date formatted based on the locale.
	 *
	 * @since 5.3.0
	 *
	 * @param string       $date      Formatted date string.
	 * @param string       $format    Format to display the date.
	 * @param int          $timestamp Unix timestamp.
	 * @param DateTimeZone $timezone  Timezone.
	 */
	$date = apply_filters( 'wp_date', $date, $format, $timestamp, $timezone );

	return $date;
}

/**
 * Determines if the date should be declined.
 *
 * If the locale specifies that month names require a genitive case in certain
 * formats (like 'j F Y'), the month name will be replaced with a correct form.
 *
 * @since 4.4.0
 * @since 5.4.0 The `$format` parameter was added.
 *
 * @global WP_Locale $wp_locale WordPress date and time locale object.
 *
 * @param string $date   Formatted date string.
 * @param string $format Optional. Date format to check. Default empty string.
 * @return string The date, declined if locale specifies it.
 */
function wp_maybe_decline_date( $date, $format = '' ) {
	global $wp_locale;

	// i18n functions are not available in SHORTINIT mode.
	if ( ! function_exists( '_x' ) ) {
		return $date;
	}

	/*
	 * translators: If months in your language require a genitive case,
	 * translate this to 'on'. Do not translate into your own language.
	 */
	if ( 'on' === _x( 'off', 'decline months names: on or off' ) ) {

		$months          = $wp_locale->month;
		$months_genitive = $wp_locale->month_genitive;

		/*
		 * Match a format like 'j F Y' or 'j. F' (day of the month, followed by month name)
		 * and decline the month.
		 */
		if ( $format ) {
			$decline = preg_match( '#[dj]\.? F#', $format );
		} else {
			// If the format is not passed, try to guess it from the date string.
			$decline = preg_match( '#\b\d{1,2}\.? [^\d ]+\b#u', $date );
		}

		if ( $decline ) {
			foreach ( $months as $key => $month ) {
				$months[ $key ] = '# ' . preg_quote( $month, '#' ) . '\b#u';
			}

			foreach ( $months_genitive as $key => $month ) {
				$months_genitive[ $key ] = ' ' . $month;
			}

			$date = preg_replace( $months, $months_genitive, $date );
		}

		/*
		 * Match a format like 'F jS' or 'F j' (month name, followed by day with an optional ordinal suffix)
		 * and change it to declined 'j F'.
		 */
		if ( $format ) {
			$decline = preg_match( '#F [dj]#', $format );
		} else {
			// If the format is not passed, try to guess it from the date string.
			$decline = preg_match( '#\b[^\d ]+ \d{1,2}(st|nd|rd|th)?\b#u', trim( $date ) );
		}

		if ( $decline ) {
			foreach ( $months as $key => $month ) {
				$months[ $key ] = '#\b' . preg_quote( $month, '#' ) . ' (\d{1,2})(st|nd|rd|th)?([-–]\d{1,2})?(st|nd|rd|th)?\b#u';
			}

			foreach ( $months_genitive as $key => $month ) {
				$months_genitive[ $key ] = '$1$3 ' . $month;
			}

			$date = preg_replace( $months, $months_genitive, $date );
		}
	}

	// Used for locale-specific rules.
	$locale = get_locale();

	if ( 'ca' === $locale ) {
		// " de abril| de agost| de octubre..." -> " d'abril| d'agost| d'octubre..."
		$date = preg_replace( '# de ([ao])#i', " d'\\1", $date );
	}

	return $date;
}

/**
 * Converts float number to format based on the locale.
 *
 * @since 2.3.0
 *
 * @global WP_Locale $wp_locale WordPress date and time locale object.
 *
 * @param float $number   The number to convert based on locale.
 * @param int   $decimals Optional. Precision of the number of decimal places. Default 0.
 * @return string Converted number in string format.
 */
function number_format_i18n( $number, $decimals = 0 ) {
	global $wp_locale;

	if ( isset( $wp_locale ) ) {
		$formatted = number_format( $number, absint( $decimals ), $wp_locale->number_format['decimal_point'], $wp_locale->number_format['thousands_sep'] );
	} else {
		$formatted = number_format( $number, absint( $decimals ) );
	}

	/**
	 * Filters the number formatted based on the locale.
	 *
	 * @since 2.8.0
	 * @since 4.9.0 The `$number` and `$decimals` parameters were added.
	 *
	 * @param string $formatted Converted number in string format.
	 * @param float  $number    The number to convert based on locale.
	 * @param int    $decimals  Precision of the number of decimal places.
	 */
	return apply_filters( 'number_format_i18n', $formatted, $number, $decimals );
}

/**
 * Converts a number of bytes to the largest unit the bytes will fit into.
 *
 * It is easier to read 1 KB than 1024 bytes and 1 MB than 1048576 bytes. Converts
 * number of bytes to human readable number by taking the number of that unit
 * that the bytes will go into it. Supports YB value.
 *
 * Please note that integers in PHP are limited to 32 bits, unless they are on
 * 64 bit architecture, then they have 64 bit size. If you need to place the
 * larger size then what PHP integer type will hold, then use a string. It will
 * be converted to a double, which should always have 64 bit length.
 *
 * Technically the correct unit names for powers of 1024 are KiB, MiB etc.
 *
 * @since 2.3.0
 * @since 6.0.0 Support for PB, EB, ZB, and YB was added.
 *
 * @param int|string $bytes    Number of bytes. Note max integer size for integers.
 * @param int        $decimals Optional. Precision of number of decimal places. Default 0.
 * @return string|false Number string on success, false on failure.
 */
function size_format( $bytes, $decimals = 0 ) {
	$quant = array(
		/* translators: Unit symbol for yottabyte. */
		_x( 'YB', 'unit symbol' ) => YB_IN_BYTES,
		/* translators: Unit symbol for zettabyte. */
		_x( 'ZB', 'unit symbol' ) => ZB_IN_BYTES,
		/* translators: Unit symbol for exabyte. */
		_x( 'EB', 'unit symbol' ) => EB_IN_BYTES,
		/* translators: Unit symbol for petabyte. */
		_x( 'PB', 'unit symbol' ) => PB_IN_BYTES,
		/* translators: Unit symbol for terabyte. */
		_x( 'TB', 'unit symbol' ) => TB_IN_BYTES,
		/* translators: Unit symbol for gigabyte. */
		_x( 'GB', 'unit symbol' ) => GB_IN_BYTES,
		/* translators: Unit symbol for megabyte. */
		_x( 'MB', 'unit symbol' ) => MB_IN_BYTES,
		/* translators: Unit symbol for kilobyte. */
		_x( 'KB', 'unit symbol' ) => KB_IN_BYTES,
		/* translators: Unit symbol for byte. */
		_x( 'B', 'unit symbol' )  => 1,
	);

	if ( 0 === $bytes ) {
		/* translators: Unit symbol for byte. */
		return number_format_i18n( 0, $decimals ) . ' ' . _x( 'B', 'unit symbol' );
	}

	foreach ( $quant as $unit => $mag ) {
		if ( (float) $bytes >= $mag ) {
			return number_format_i18n( $bytes / $mag, $decimals ) . ' ' . $unit;
		}
	}

	return false;
}

/**
 * Converts a duration to human readable format.
 *
 * @since 5.1.0
 *
 * @param string $duration Duration will be in string format (HH:ii:ss) OR (ii:ss),
 *                         with a possible prepended negative sign (-).
 * @return string|false A human readable duration string, false on failure.
 */
function human_readable_duration( $duration = '' ) {
	if ( ( empty( $duration ) || ! is_string( $duration ) ) ) {
		return false;
	}

	$duration = trim( $duration );

	// Remove prepended negative sign.
	if ( str_starts_with( $duration, '-' ) ) {
		$duration = substr( $duration, 1 );
	}

	// Extract duration parts.
	$duration_parts = array_reverse( explode( ':', $duration ) );
	$duration_count = count( $duration_parts );

	$hour   = null;
	$minute = null;
	$second = null;

	if ( 3 === $duration_count ) {
		// Validate HH:ii:ss duration format.
		if ( ! ( (bool) preg_match( '/^([0-9]+):([0-5]?[0-9]):([0-5]?[0-9])$/', $duration ) ) ) {
			return false;
		}
		// Three parts: hours, minutes & seconds.
		list( $second, $minute, $hour ) = $duration_parts;
	} elseif ( 2 === $duration_count ) {
		// Validate ii:ss duration format.
		if ( ! ( (bool) preg_match( '/^([0-5]?[0-9]):([0-5]?[0-9])$/', $duration ) ) ) {
			return false;
		}
		// Two parts: minutes & seconds.
		list( $second, $minute ) = $duration_parts;
	} else {
		return false;
	}

	$human_readable_duration = array();

	// Add the hour part to the string.
	if ( is_numeric( $hour ) ) {
		/* translators: %s: Time duration in hour or hours. */
		$human_readable_duration[] = sprintf( _n( '%s hour', '%s hours', $hour ), (int) $hour );
	}

	// Add the minute part to the string.
	if ( is_numeric( $minute ) ) {
		/* translators: %s: Time duration in minute or minutes. */
		$human_readable_duration[] = sprintf( _n( '%s minute', '%s minutes', $minute ), (int) $minute );
	}

	// Add the second part to the string.
	if ( is_numeric( $second ) ) {
		/* translators: %s: Time duration in second or seconds. */
		$human_readable_duration[] = sprintf( _n( '%s second', '%s seconds', $second ), (int) $second );
	}

	return implode( ', ', $human_readable_duration );
}

/**
 * Gets the week start and end from the datetime or date string from MySQL.
 *
 * @since 0.71
 *
 * @param string     $mysqlstring   Date or datetime field type from MySQL.
 * @param int|string $start_of_week Optional. Start of the week as an integer. Default empty string.
 * @return int[] {
 *     Week start and end dates as Unix timestamps.
 *
 *     @type int $start The week start date as a Unix timestamp.
 *     @type int $end   The week end date as a Unix timestamp.
 * }
 */
function get_weekstartend( $mysqlstring, $start_of_week = '' ) {
	// MySQL string year.
	$my = substr( $mysqlstring, 0, 4 );

	// MySQL string month.
	$mm = substr( $mysqlstring, 8, 2 );

	// MySQL string day.
	$md = substr( $mysqlstring, 5, 2 );

	// The timestamp for MySQL string day.
	$day = mktime( 0, 0, 0, $md, $mm, $my );

	// The day of the week from the timestamp.
	$weekday = (int) gmdate( 'w', $day );

	if ( ! is_numeric( $start_of_week ) ) {
		$start_of_week = (int) get_option( 'start_of_week' );
	}

	if ( $weekday < $start_of_week ) {
		$weekday += 7;
	}

	// The most recent week start day on or before $day.
	$start = $day - DAY_IN_SECONDS * ( $weekday - $start_of_week );

	// $start + 1 week - 1 second.
	$end = $start + WEEK_IN_SECONDS - 1;

	return compact( 'start', 'end' );
}

/**
 * Serializes data, if needed.
 *
 * @since 2.0.5
 *
 * @param string|array|object $data Data that might be serialized.
 * @return mixed A scalar data.
 */
function maybe_serialize( $data ) {
	if ( is_array( $data ) || is_object( $data ) ) {
		return serialize( $data );
	}

	/*
	 * Double serialization is required for backward compatibility.
	 * See https://core.trac.wordpress.org/ticket/12930
	 * Also the world will end. See WP 3.6.1.
	 */
	if ( is_serialized( $data, false ) ) {
		return serialize( $data );
	}

	return $data;
}

/**
 * Unserializes data only if it was serialized.
 *
 * @since 2.0.0
 *
 * @param string $data Data that might be unserialized.
 * @return mixed Unserialized data can be any type.
 */
function maybe_unserialize( $data ) {
	if ( is_serialized( $data ) ) { // Don't attempt to unserialize data that wasn't serialized going in.
		return @unserialize( trim( $data ) );
	}

	return $data;
}

/**
 * Checks value to find if it was serialized.
 *
 * If $data is not a string, then returned value will always be false.
 * Serialized data is always a string.
 *
 * @since 2.0.5
 * @since 6.1.0 Added Enum support.
 *
 * @param string $data   Value to check to see if was serialized.
 * @param bool   $strict Optional. Whether to be strict about the end of the string. Default true.
 * @return bool False if not serialized and true if it was.
 */
function is_serialized( $data, $strict = true ) {
	// If it isn't a string, it isn't serialized.
	if ( ! is_string( $data ) ) {
		return false;
	}
	$data = trim( $data );
	if ( 'N;' === $data ) {
		return true;
	}
	if ( strlen( $data ) < 4 ) {
		return false;
	}
	if ( ':' !== $data[1] ) {
		return false;
	}
	if ( $strict ) {
		$lastc = substr( $data, -1 );
		if ( ';' !== $lastc && '}' !== $lastc ) {
			return false;
		}
	} else {
		$semicolon = strpos( $data, ';' );
		$brace     = strpos( $data, '}' );
		// Either ; or } must exist.
		if ( false === $semicolon && false === $brace ) {
			return false;
		}
		// But neither must be in the first X characters.
		if ( false !== $semicolon && $semicolon < 3 ) {
			return false;
		}
		if ( false !== $brace && $brace < 4 ) {
			return false;
		}
	}
	$token = $data[0];
	switch ( $token ) {
		case 's':
			if ( $strict ) {
				if ( '"' !== substr( $data, -2, 1 ) ) {
					return false;
				}
			} elseif ( ! str_contains( $data, '"' ) ) {
				return false;
			}
			// Or else fall through.
		case 'a':
		case 'O':
		case 'E':
			return (bool) preg_match( "/^{$token}:[0-9]+:/s", $data );
		case 'b':
		case 'i':
		case 'd':
			$end = $strict ? '$' : '';
			return (bool) preg_match( "/^{$token}:[0-9.E+-]+;$end/", $data );
	}
	return false;
}

/**
 * Checks whether serialized data is of string type.
 *
 * @since 2.0.5
 *
 * @param string $data Serialized data.
 * @return bool False if not a serialized string, true if it is.
 */
function is_serialized_string( $data ) {
	// if it isn't a string, it isn't a serialized string.
	if ( ! is_string( $data ) ) {
		return false;
	}
	$data = trim( $data );
	if ( strlen( $data ) < 4 ) {
		return false;
	} elseif ( ':' !== $data[1] ) {
		return false;
	} elseif ( ! str_ends_with( $data, ';' ) ) {
		return false;
	} elseif ( 's' !== $data[0] ) {
		return false;
	} elseif ( '"' !== substr( $data, -2, 1 ) ) {
		return false;
	} else {
		return true;
	}
}

/**
 * Retrieves post title from XMLRPC XML.
 *
 * If the title element is not part of the XML, then the default post title from
 * the $post_default_title will be used instead.
 *
 * @since 0.71
 *
 * @global string $post_default_title Default XML-RPC post title.
 *
 * @param string $content XMLRPC XML Request content
 * @return string Post title
 */
function xmlrpc_getposttitle( $content ) {
	global $post_default_title;
	if ( preg_match( '/<title>(.+?)<\/title>/is', $content, $matchtitle ) ) {
		$post_title = $matchtitle[1];
	} else {
		$post_title = $post_default_title;
	}
	return $post_title;
}

/**
 * Retrieves the post category or categories from XMLRPC XML.
 *
 * If the category element is not found, then the default post category will be
 * used. The return type then would be what $post_default_category. If the
 * category is found, then it will always be an array.
 *
 * @since 0.71
 *
 * @global string $post_default_category Default XML-RPC post category.
 *
 * @param string $content XMLRPC XML Request content
 * @return string|array List of categories or category name.
 */
function xmlrpc_getpostcategory( $content ) {
	global $post_default_category;
	if ( preg_match( '/<category>(.+?)<\/category>/is', $content, $matchcat ) ) {
		$post_category = trim( $matchcat[1], ',' );
		$post_category = explode( ',', $post_category );
	} else {
		$post_category = $post_default_category;
	}
	return $post_category;
}

/**
 * XMLRPC XML content without title and category elements.
 *
 * @since 0.71
 *
 * @param string $content XML-RPC XML Request content.
 * @return string XMLRPC XML Request content without title and category elements.
 */
function xmlrpc_removepostdata( $content ) {
	$content = preg_replace( '/<title>(.+?)<\/title>/si', '', $content );
	$content = preg_replace( '/<category>(.+?)<\/category>/si', '', $content );
	$content = trim( $content );
	return $content;
}

/**
 * Uses RegEx to extract URLs from arbitrary content.
 *
 * @since 3.7.0
 * @since 6.0.0 Fixes support for HTML entities (Trac 30580).
 *
 * @param string $content Content to extract URLs from.
 * @return string[] Array of URLs found in passed string.
 */
function wp_extract_urls( $content ) {
	preg_match_all(
		"#([\"']?)("
			. '(?:([\w-]+:)?//?)'
			. '[^\s()<>]+'
			. '[.]'
			. '(?:'
				. '\([\w\d]+\)|'
				. '(?:'
					. "[^`!()\[\]{}:'\".,<>«»“”‘’\s]|"
					. '(?:[:]\d+)?/?'
				. ')+'
			. ')'
		. ")\\1#",
		$content,
		$post_links
	);

	$post_links = array_unique(
		array_map(
			static function ( $link ) {
				// Decode to replace valid entities, like &amp;.
				$link = html_entity_decode( $link );
				// Maintain backward compatibility by removing extraneous semi-colons (`;`).
				return str_replace( ';', '', $link );
			},
			$post_links[2]
		)
	);

	return array_values( $post_links );
}

/**
 * Checks content for video and audio links to add as enclosures.
 *
 * Will not add enclosures that have already been added and will
 * remove enclosures that are no longer in the post. This is called as
 * pingbacks and trackbacks.
 *
 * @since 1.5.0
 * @since 5.3.0 The `$content` parameter was made optional, and the `$post` parameter was
 *              updated to accept a post ID or a WP_Post object.
 * @since 5.6.0 The `$content` parameter is no longer optional, but passing `null` to skip it
 *              is still supported.
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param string|null $content Post content. If `null`, the `post_content` field from `$post` is used.
 * @param int|WP_Post $post    Post ID or post object.
 * @return void|false Void on success, false if the post is not found.
 */
function do_enclose( $content, $post ) {
	global $wpdb;

	// @todo Tidy this code and make the debug code optional.
	require_once ABSPATH . WPINC . '/class-IXR.php';

	$post = get_post( $post );
	if ( ! $post ) {
		return false;
	}

	if ( null === $content ) {
		$content = $post->post_content;
	}

	$post_links = array();

	$pung = get_enclosed( $post->ID );

	$post_links_temp = wp_extract_urls( $content );

	foreach ( $pung as $link_test ) {
		// Link is no longer in post.
		if ( ! in_array( $link_test, $post_links_temp, true ) ) {
			$mids = $wpdb->get_col( $wpdb->prepare( "SELECT meta_id FROM $wpdb->postmeta WHERE post_id = %d AND meta_key = 'enclosure' AND meta_value LIKE %s", $post->ID, $wpdb->esc_like( $link_test ) . '%' ) );
			foreach ( $mids as $mid ) {
				delete_metadata_by_mid( 'post', $mid );
			}
		}
	}

	foreach ( (array) $post_links_temp as $link_test ) {
		// If we haven't pung it already.
		if ( ! in_array( $link_test, $pung, true ) ) {
			$test = parse_url( $link_test );
			if ( false === $test ) {
				continue;
			}
			if ( isset( $test['query'] ) ) {
				$post_links[] = $link_test;
			} elseif ( isset( $test['path'] ) && ( '/' !== $test['path'] ) && ( '' !== $test['path'] ) ) {
				$post_links[] = $link_test;
			}
		}
	}

	/**
	 * Filters the list of enclosure links before querying the database.
	 *
	 * Allows for the addition and/or removal of potential enclosures to save
	 * to postmeta before checking the database for existing enclosures.
	 *
	 * @since 4.4.0
	 *
	 * @param string[] $post_links An array of enclosure links.
	 * @param int      $post_id    Post ID.
	 */
	$post_links = apply_filters( 'enclosure_links', $post_links, $post->ID );

	foreach ( (array) $post_links as $url ) {
		$url = strip_fragment_from_url( $url );

		if ( '' !== $url && ! $wpdb->get_var( $wpdb->prepare( "SELECT post_id FROM $wpdb->postmeta WHERE post_id = %d AND meta_key = 'enclosure' AND meta_value LIKE %s", $post->ID, $wpdb->esc_like( $url ) . '%' ) ) ) {

			$headers = wp_get_http_headers( $url );
			if ( $headers ) {
				$len           = isset( $headers['Content-Length'] ) ? (int) $headers['Content-Length'] : 0;
				$type          = isset( $headers['Content-Type'] ) ? $headers['Content-Type'] : '';
				$allowed_types = array( 'video', 'audio' );

				// Check to see if we can figure out the mime type from the extension.
				$url_parts = parse_url( $url );
				if ( false !== $url_parts && ! empty( $url_parts['path'] ) ) {
					$extension = pathinfo( $url_parts['path'], PATHINFO_EXTENSION );
					if ( ! empty( $extension ) ) {
						foreach ( wp_get_mime_types() as $exts => $mime ) {
							if ( preg_match( '!^(' . $exts . ')$!i', $extension ) ) {
								$type = $mime;
								break;
							}
						}
					}
				}

				if ( in_array( substr( $type, 0, strpos( $type, '/' ) ), $allowed_types, true ) ) {
					add_post_meta( $post->ID, 'enclosure', "$url\n$len\n$mime\n" );
				}
			}
		}
	}
}

/**
 * Retrieves HTTP Headers from URL.
 *
 * @since 1.5.1
 *
 * @param string $url        URL to retrieve HTTP headers from.
 * @param bool   $deprecated Not Used.
 * @return \WpOrg\Requests\Utility\CaseInsensitiveDictionary|false Headers on success, false on failure.
 */
function wp_get_http_headers( $url, $deprecated = false ) {
	if ( ! empty( $deprecated ) ) {
		_deprecated_argument( __FUNCTION__, '2.7.0' );
	}

	$response = wp_safe_remote_head( $url );

	if ( is_wp_error( $response ) ) {
		return false;
	}

	return wp_remote_retrieve_headers( $response );
}

/**
 * Determines whether the publish date of the current post in the loop is different
 * from the publish date of the previous post in the loop.
 *
 * For more information on this and similar theme functions, check out
 * the {@link https://developer.wordpress.org/themes/basics/conditional-tags/
 * Conditional Tags} article in the Theme Developer Handbook.
 *
 * @since 0.71
 *
 * @global string $currentday  The day of the current post in the loop.
 * @global string $previousday The day of the previous post in the loop.
 *
 * @return int 1 when new day, 0 if not a new day.
 */
function is_new_day() {
	global $currentday, $previousday;

	if ( $currentday !== $previousday ) {
		return 1;
	} else {
		return 0;
	}
}

/**
 * Builds URL query based on an associative and, or indexed array.
 *
 * This is a convenient function for easily building url queries. It sets the
 * separator to '&' and uses _http_build_query() function.
 *
 * @since 2.3.0
 *
 * @see _http_build_query() Used to build the query
 * @link https://www.php.net/manual/en/function.http-build-query.php for more on what
 *       http_build_query() does.
 *
 * @param array $data URL-encode key/value pairs.
 * @return string URL-encoded string.
 */
function build_query( $data ) {
	return _http_build_query( $data, null, '&', '', false );
}

/**
 * From php.net (modified by Mark Jaquith to behave like the native PHP5 function).
 *
 * @since 3.2.0
 * @access private
 *
 * @see https://www.php.net/manual/en/function.http-build-query.php
 *
 * @param array|object $data      An array or object of data. Converted to array.
 * @param string       $prefix    Optional. Numeric index. If set, start parameter numbering with it.
 *                                Default null.
 * @param string       $sep       Optional. Argument separator; defaults to 'arg_separator.output'.
 *                                Default null.
 * @param string       $key       Optional. Used to prefix key name. Default empty string.
 * @param bool         $urlencode Optional. Whether to use urlencode() in the result. Default true.
 * @return string The query string.
 */
function _http_build_query( $data, $prefix = null, $sep = null, $key = '', $urlencode = true ) {
	$ret = array();

	foreach ( (array) $data as $k => $v ) {
		if ( $urlencode ) {
			$k = urlencode( $k );
		}

		if ( is_int( $k ) && null !== $prefix ) {
			$k = $prefix . $k;
		}

		if ( ! empty( $key ) ) {
			$k = $key . '%5B' . $k . '%5D';
		}

		if ( null === $v ) {
			continue;
		} elseif ( false === $v ) {
			$v = '0';
		}

		if ( is_array( $v ) || is_object( $v ) ) {
			array_push( $ret, _http_build_query( $v, '', $sep, $k, $urlencode ) );
		} elseif ( $urlencode ) {
			array_push( $ret, $k . '=' . urlencode( $v ) );
		} else {
			array_push( $ret, $k . '=' . $v );
		}
	}

	if ( null === $sep ) {
		$sep = ini_get( 'arg_separator.output' );
	}

	return implode( $sep, $ret );
}

/**
 * Retrieves a modified URL query string.
 *
 * You can rebuild the URL and append query variables to the URL query by using this function.
 * There are two ways to use this function; either a single key and value, or an associative array.
 *
 * Using a single key and value:
 *
 *     add_query_arg( 'key', 'value', 'http://example.com' );
 *
 * Using an associative array:
 *
 *     add_query_arg( array(
 *         'key1' => 'value1',
 *         'key2' => 'value2',
 *     ), 'http://example.com' );
 *
 * Omitting the URL from either use results in the current URL being used
 * (the value of `$_SERVER['REQUEST_URI']`).
 *
 * Values are expected to be encoded appropriately with urlencode() or rawurlencode().
 *
 * Setting any query variable's value to boolean false removes the key (see remove_query_arg()).
 *
 * Important: The return value of add_query_arg() is not escaped by default. Output should be
 * late-escaped with esc_url() or similar to help prevent vulnerability to cross-site scripting
 * (XSS) attacks.
 *
 * @since 1.5.0
 * @since 5.3.0 Formalized the existing and already documented parameters
 *              by adding `...$args` to the function signature.
 *
 * @param string|array $key   Either a query variable key, or an associative array of query variables.
 * @param string       $value Optional. Either a query variable value, or a URL to act upon.
 * @param string       $url   Optional. A URL to act upon.
 * @return string New URL query string (unescaped).
 */
function add_query_arg( ...$args ) {
	if ( is_array( $args[0] ) ) {
		if ( count( $args ) < 2 || false === $args[1] ) {
			$uri = $_SERVER['REQUEST_URI'];
		} else {
			$uri = $args[1];
		}
	} else {
		if ( count( $args ) < 3 || false === $args[2] ) {
			$uri = $_SERVER['REQUEST_URI'];
		} else {
			$uri = $args[2];
		}
	}

	$frag = strstr( $uri, '#' );
	if ( $frag ) {
		$uri = substr( $uri, 0, -strlen( $frag ) );
	} else {
		$frag = '';
	}

	if ( 0 === stripos( $uri, 'http://' ) ) {
		$protocol = 'http://';
		$uri      = substr( $uri, 7 );
	} elseif ( 0 === stripos( $uri, 'https://' ) ) {
		$protocol = 'https://';
		$uri      = substr( $uri, 8 );
	} else {
		$protocol = '';
	}

	if ( str_contains( $uri, '?' ) ) {
		list( $base, $query ) = explode( '?', $uri, 2 );
		$base                .= '?';
	} elseif ( $protocol || ! str_contains( $uri, '=' ) ) {
		$base  = $uri . '?';
		$query = '';
	} else {
		$base  = '';
		$query = $uri;
	}

	wp_parse_str( $query, $qs );
	$qs = urlencode_deep( $qs ); // This re-URL-encodes things that were already in the query string.
	if ( is_array( $args[0] ) ) {
		foreach ( $args[0] as $k => $v ) {
			$qs[ $k ] = $v;
		}
	} else {
		$qs[ $args[0] ] = $args[1];
	}

	foreach ( $qs as $k => $v ) {
		if ( false === $v ) {
			unset( $qs[ $k ] );
		}
	}

	$ret = build_query( $qs );
	$ret = trim( $ret, '?' );
	$ret = preg_replace( '#=(&|$)#', '$1', $ret );
	$ret = $protocol . $base . $ret . $frag;
	$ret = rtrim( $ret, '?' );
	$ret = str_replace( '?#', '#', $ret );
	return $ret;
}

/**
 * Removes an item or items from a query string.
 *
 * Important: The return value of remove_query_arg() is not escaped by default. Output should be
 * late-escaped with esc_url() or similar to help prevent vulnerability to cross-site scripting
 * (XSS) attacks.
 *
 * @since 1.5.0
 *
 * @param string|string[] $key   Query key or keys to remove.
 * @param false|string    $query Optional. When false uses the current URL. Default false.
 * @return string New URL query string.
 */
function remove_query_arg( $key, $query = false ) {
	if ( is_array( $key ) ) { // Removing multiple keys.
		foreach ( $key as $k ) {
			$query = add_query_arg( $k, false, $query );
		}
		return $query;
	}
	return add_query_arg( $key, false, $query );
}

/**
 * Returns an array of single-use query variable names that can be removed from a URL.
 *
 * @since 4.4.0
 *
 * @return string[] An array of query variable names to remove from the URL.
 */
function wp_removable_query_args() {
	$removable_query_args = array(
		'activate',
		'activated',
		'admin_email_remind_later',
		'approved',
		'core-major-auto-updates-saved',
		'deactivate',
		'delete_count',
		'deleted',
		'disabled',
		'doing_wp_cron',
		'enabled',
		'error',
		'hotkeys_highlight_first',
		'hotkeys_highlight_last',
		'ids',
		'locked',
		'message',
		'same',
		'saved',
		'settings-updated',
		'skipped',
		'spammed',
		'trashed',
		'unspammed',
		'untrashed',
		'update',
		'updated',
		'wp-post-new-reload',
	);

	/**
	 * Filters the list of query variable names to remove.
	 *
	 * @since 4.2.0
	 *
	 * @param string[] $removable_query_args An array of query variable names to remove from a URL.
	 */
	return apply_filters( 'removable_query_args', $removable_query_args );
}

/**
 * Walks the array while sanitizing the contents.
 *
 * @since 0.71
 * @since 5.5.0 Non-string values are left untouched.
 *
 * @param array $input_array Array to walk while sanitizing contents.
 * @return array Sanitized $input_array.
 */
function add_magic_quotes( $input_array ) {
	foreach ( (array) $input_array as $k => $v ) {
		if ( is_array( $v ) ) {
			$input_array[ $k ] = add_magic_quotes( $v );
		} elseif ( is_string( $v ) ) {
			$input_array[ $k ] = addslashes( $v );
		}
	}

	return $input_array;
}

/**
 * HTTP request for URI to retrieve content.
 *
 * @since 1.5.1
 *
 * @see wp_safe_remote_get()
 *
 * @param string $uri URI/URL of web page to retrieve.
 * @return string|false HTTP content. False on failure.
 */
function wp_remote_fopen( $uri ) {
	$parsed_url = parse_url( $uri );

	if ( ! $parsed_url || ! is_array( $parsed_url ) ) {
		return false;
	}

	$options            = array();
	$options['timeout'] = 10;

	$response = wp_safe_remote_get( $uri, $options );

	if ( is_wp_error( $response ) ) {
		return false;
	}

	return wp_remote_retrieve_body( $response );
}

/**
 * Sets up the WordPress query.
 *
 * @since 2.0.0
 *
 * @global WP       $wp           Current WordPress environment instance.
 * @global WP_Query $wp_query     WordPress Query object.
 * @global WP_Query $wp_the_query Copy of the WordPress Query object.
 *
 * @param string|array $query_vars Default WP_Query arguments.
 */
function wp( $query_vars = '' ) {
	global $wp, $wp_query, $wp_the_query;

	$wp->main( $query_vars );

	if ( ! isset( $wp_the_query ) ) {
		$wp_the_query = $wp_query;
	}
}

/**
 * Retrieves the description for the HTTP status.
 *
 * @since 2.3.0
 * @since 3.9.0 Added status codes 418, 428, 429, 431, and 511.
 * @since 4.5.0 Added status codes 308, 421, and 451.
 * @since 5.1.0 Added status code 103.
 * @since 6.6.0 Added status code 425.
 *
 * @global array $wp_header_to_desc
 *
 * @param int $code HTTP status code.
 * @return string Status description if found, an empty string otherwise.
 */
function get_status_header_desc( $code ) {
	global $wp_header_to_desc;

	$code = absint( $code );

	if ( ! isset( $wp_header_to_desc ) ) {
		$wp_header_to_desc = array(
			100 => 'Continue',
			101 => 'Switching Protocols',
			102 => 'Processing',
			103 => 'Early Hints',

			200 => 'OK',
			201 => 'Created',
			202 => 'Accepted',
			203 => 'Non-Authoritative Information',
			204 => 'No Content',
			205 => 'Reset Content',
			206 => 'Partial Content',
			207 => 'Multi-Status',
			226 => 'IM Used',

			300 => 'Multiple Choices',
			301 => 'Moved Permanently',
			302 => 'Found',
			303 => 'See Other',
			304 => 'Not Modified',
			305 => 'Use Proxy',
			306 => 'Reserved',
			307 => 'Temporary Redirect',
			308 => 'Permanent Redirect',

			400 => 'Bad Request',
			401 => 'Unauthorized',
			402 => 'Payment Required',
			403 => 'Forbidden',
			404 => 'Not Found',
			405 => 'Method Not Allowed',
			406 => 'Not Acceptable',
			407 => 'Proxy Authentication Required',
			408 => 'Request Timeout',
			409 => 'Conflict',
			410 => 'Gone',
			411 => 'Length Required',
			412 => 'Precondition Failed',
			413 => 'Request Entity Too Large',
			414 => 'Request-URI Too Long',
			415 => 'Unsupported Media Type',
			416 => 'Requested Range Not Satisfiable',
			417 => 'Expectation Failed',
			418 => 'I\'m a teapot',
			421 => 'Misdirected Request',
			422 => 'Unprocessable Entity',
			423 => 'Locked',
			424 => 'Failed Dependency',
			425 => 'Too Early',
			426 => 'Upgrade Required',
			428 => 'Precondition Required',
			429 => 'Too Many Requests',
			431 => 'Request Header Fields Too Large',
			451 => 'Unavailable For Legal Reasons',

			500 => 'Internal Server Error',
			501 => 'Not Implemented',
			502 => 'Bad Gateway',
			503 => 'Service Unavailable',
			504 => 'Gateway Timeout',
			505 => 'HTTP Version Not Supported',
			506 => 'Variant Also Negotiates',
			507 => 'Insufficient Storage',
			510 => 'Not Extended',
			511 => 'Network Authentication Required',
		);
	}

	if ( isset( $wp_header_to_desc[ $code ] ) ) {
		return $wp_header_to_desc[ $code ];
	} else {
		return '';
	}
}

/**
 * Sets HTTP status header.
 *
 * @since 2.0.0
 * @since 4.4.0 Added the `$description` parameter.
 *
 * @see get_status_header_desc()
 *
 * @param int    $code        HTTP status code.
 * @param string $description Optional. A custom description for the HTTP status.
 *                            Defaults to the result of get_status_header_desc() for the given code.
 */
function status_header( $code, $description = '' ) {
	if ( ! $description ) {
		$description = get_status_header_desc( $code );
	}

	if ( empty( $description ) ) {
		return;
	}

	$protocol      = wp_get_server_protocol();
	$status_header = "$protocol $code $description";
	if ( function_exists( 'apply_filters' ) ) {

		/**
		 * Filters an HTTP status header.
		 *
		 * @since 2.2.0
		 *
		 * @param string $status_header HTTP status header.
		 * @param int    $code          HTTP status code.
		 * @param string $description   Description for the status code.
		 * @param string $protocol      Server protocol.
		 */
		$status_header = apply_filters( 'status_header', $status_header, $code, $description, $protocol );
	}

	if ( ! headers_sent() ) {
		header( $status_header, true, $code );
	}
}

/**
 * Gets the HTTP header information to prevent caching.
 *
 * The several different headers cover the different ways cache prevention
 * is handled by different browsers or intermediate caches such as proxy servers.
 *
 * @since 2.8.0
 * @since 6.3.0 The `Cache-Control` header for logged in users now includes the
 *              `no-store` and `private` directives.
 * @since 6.8.0 The `Cache-Control` header now includes the `no-store` and `private`
 *              directives regardless of whether a user is logged in.
 *
 * @return array The associative array of header names and field values.
 */
function wp_get_nocache_headers() {
	$cache_control = 'no-cache, must-revalidate, max-age=0, no-store, private';

	$headers = array(
		'Expires'       => 'Wed, 11 Jan 1984 05:00:00 GMT',
		'Cache-Control' => $cache_control,
	);

	if ( function_exists( 'apply_filters' ) ) {
		/**
		 * Filters the cache-controlling HTTP headers that are used to prevent caching.
		 *
		 * @since 2.8.0
		 *
		 * @see wp_get_nocache_headers()
		 *
		 * @param array $headers Header names and field values.
		 */
		$headers = (array) apply_filters( 'nocache_headers', $headers );
	}
	$headers['Last-Modified'] = false;
	return $headers;
}

/**
 * Sets the HTTP headers to prevent caching for the different browsers.
 *
 * Different browsers support different nocache headers, so several
 * headers must be sent so that all of them get the point that no
 * caching should occur.
 *
 * @since 2.0.0
 *
 * @see wp_get_nocache_headers()
 */
function nocache_headers() {
	if ( headers_sent() ) {
		return;
	}

	$headers = wp_get_nocache_headers();

	unset( $headers['Last-Modified'] );

	header_remove( 'Last-Modified' );

	foreach ( $headers as $name => $field_value ) {
		header( "{$name}: {$field_value}" );
	}
}

/**
 * Sets the HTTP headers for caching for 10 days with JavaScript content type.
 *
 * @since 2.1.0
 */
function cache_javascript_headers() {
	$expires_offset = 10 * DAY_IN_SECONDS;

	header( 'Content-Type: text/javascript; charset=' . get_bloginfo( 'charset' ) );
	header( 'Vary: Accept-Encoding' ); // Handle proxies.
	header( 'Expires: ' . gmdate( 'D, d M Y H:i:s', time() + $expires_offset ) . ' GMT' );
}

/**
 * Retrieves the number of database queries during the WordPress execution.
 *
 * @since 2.0.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @return int Number of database queries.
 */
function get_num_queries() {
	global $wpdb;
	return $wpdb->num_queries;
}

/**
 * Determines whether input is yes or no.
 *
 * Must be 'y' to be true.
 *
 * @since 1.0.0
 *
 * @param string $yn Character string containing either 'y' (yes) or 'n' (no).
 * @return bool True if 'y', false on anything else.
 */
function bool_from_yn( $yn ) {
	return ( 'y' === strtolower( $yn ) );
}

/**
 * Loads the feed template from the use of an action hook.
 *
 * If the feed action does not have a hook, then the function will die with a
 * message telling the visitor that the feed is not valid.
 *
 * It is better to only have one hook for each feed.
 *
 * @since 2.1.0
 *
 * @global WP_Query $wp_query WordPress Query object.
 */
function do_feed() {
	global $wp_query;

	$feed = get_query_var( 'feed' );

	// Remove the pad, if present.
	$feed = preg_replace( '/^_+/', '', $feed );

	if ( '' === $feed || 'feed' === $feed ) {
		$feed = get_default_feed();
	}

	if ( ! has_action( "do_feed_{$feed}" ) ) {
		wp_die( __( '<strong>Error:</strong> This is not a valid feed template.' ), '', array( 'response' => 404 ) );
	}

	/**
	 * Fires once the given feed is loaded.
	 *
	 * The dynamic portion of the hook name, `$feed`, refers to the feed template name.
	 *
	 * Possible hook names include:
	 *
	 *  - `do_feed_atom`
	 *  - `do_feed_rdf`
	 *  - `do_feed_rss`
	 *  - `do_feed_rss2`
	 *
	 * @since 2.1.0
	 * @since 4.4.0 The `$feed` parameter was added.
	 *
	 * @param bool   $is_comment_feed Whether the feed is a comment feed.
	 * @param string $feed            The feed name.
	 */
	do_action( "do_feed_{$feed}", $wp_query->is_comment_feed, $feed );
}

/**
 * Loads the RDF RSS 0.91 Feed template.
 *
 * @since 2.1.0
 *
 * @see load_template()
 */
function do_feed_rdf() {
	load_template( ABSPATH . WPINC . '/feed-rdf.php' );
}

/**
 * Loads the RSS 1.0 Feed Template.
 *
 * @since 2.1.0
 *
 * @see load_template()
 */
function do_feed_rss() {
	load_template( ABSPATH . WPINC . '/feed-rss.php' );
}

/**
 * Loads either the RSS2 comment feed or the RSS2 posts feed.
 *
 * @since 2.1.0
 *
 * @see load_template()
 *
 * @param bool $for_comments True for the comment feed, false for normal feed.
 */
function do_feed_rss2( $for_comments ) {
	if ( $for_comments ) {
		load_template( ABSPATH . WPINC . '/feed-rss2-comments.php' );
	} else {
		load_template( ABSPATH . WPINC . '/feed-rss2.php' );
	}
}

/**
 * Loads either Atom comment feed or Atom posts feed.
 *
 * @since 2.1.0
 *
 * @see load_template()
 *
 * @param bool $for_comments True for the comment feed, false for normal feed.
 */
function do_feed_atom( $for_comments ) {
	if ( $for_comments ) {
		load_template( ABSPATH . WPINC . '/feed-atom-comments.php' );
	} else {
		load_template( ABSPATH . WPINC . '/feed-atom.php' );
	}
}

/**
 * Displays the default robots.txt file content.
 *
 * @since 2.1.0
 * @since 5.3.0 Remove the "Disallow: /" output if search engine visibility is
 *              discouraged in favor of robots meta HTML tag via wp_robots_no_robots()
 *              filter callback.
 */
function do_robots() {
	header( 'Content-Type: text/plain; charset=utf-8' );

	/**
	 * Fires when displaying the robots.txt file.
	 *
	 * @since 2.1.0
	 */
	do_action( 'do_robotstxt' );

	$output = "User-agent: *\n";
	$public = (bool) get_option( 'blog_public' );

	$site_url = parse_url( site_url() );
	$path     = ( ! empty( $site_url['path'] ) ) ? $site_url['path'] : '';
	$output  .= "Disallow: $path/wp-admin/\n";
	$output  .= "Allow: $path/wp-admin/admin-ajax.php\n";

	/**
	 * Filters the robots.txt output.
	 *
	 * @since 3.0.0
	 *
	 * @param string $output The robots.txt output.
	 * @param bool   $public Whether the site is considered "public".
	 */
	echo apply_filters( 'robots_txt', $output, $public );
}

/**
 * Displays the favicon.ico file content.
 *
 * @since 5.4.0
 */
function do_favicon() {
	/**
	 * Fires when serving the favicon.ico file.
	 *
	 * @since 5.4.0
	 */
	do_action( 'do_faviconico' );

	wp_redirect( get_site_icon_url( 32, includes_url( 'images/w-logo-blue-white-bg.png' ) ) );
	exit;
}

/**
 * Determines whether WordPress is already installed.
 *
 * The cache will be checked first. If you have a cache plugin, which saves
 * the cache values, then this will work. If you use the default WordPress
 * cache, and the database goes away, then you might have problems.
 *
 * Checks for the 'siteurl' option for whether WordPress is installed.
 *
 * For more information on this and similar theme functions, check out
 * the {@link https://developer.wordpress.org/themes/basics/conditional-tags/
 * Conditional Tags} article in the Theme Developer Handbook.
 *
 * @since 2.1.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @return bool Whether the site is already installed.
 */
function is_blog_installed() {
	global $wpdb;

	/*
	 * Check cache first. If options table goes away and we have true
	 * cached, oh well.
	 */
	if ( wp_cache_get( 'is_blog_installed' ) ) {
		return true;
	}

	$suppress = $wpdb->suppress_errors();

	if ( ! wp_installing() ) {
		$alloptions = wp_load_alloptions();
	}

	// If siteurl is not set to autoload, check it specifically.
	if ( ! isset( $alloptions['siteurl'] ) ) {
		$installed = $wpdb->get_var( "SELECT option_value FROM $wpdb->options WHERE option_name = 'siteurl'" );
	} else {
		$installed = $alloptions['siteurl'];
	}

	$wpdb->suppress_errors( $suppress );

	$installed = ! empty( $installed );
	wp_cache_set( 'is_blog_installed', $installed );

	if ( $installed ) {
		return true;
	}

	// If visiting repair.php, return true and let it take over.
	if ( defined( 'WP_REPAIRING' ) ) {
		return true;
	}

	$suppress = $wpdb->suppress_errors();

	/*
	 * Loop over the WP tables. If none exist, then scratch installation is allowed.
	 * If one or more exist, suggest table repair since we got here because the
	 * options table could not be accessed.
	 */
	$wp_tables = $wpdb->tables();
	foreach ( $wp_tables as $table ) {
		// The existence of custom user tables shouldn't suggest an unwise state or prevent a clean installation.
		if ( defined( 'CUSTOM_USER_TABLE' ) && CUSTOM_USER_TABLE === $table ) {
			continue;
		}

		if ( defined( 'CUSTOM_USER_META_TABLE' ) && CUSTOM_USER_META_TABLE === $table ) {
			continue;
		}

		$described_table = $wpdb->get_results( "DESCRIBE $table;" );
		if (
			( ! $described_table && empty( $wpdb->last_error ) ) ||
			( is_array( $described_table ) && 0 === count( $described_table ) )
		) {
			continue;
		}

		// One or more tables exist. This is not good.

		wp_load_translations_early();

		// Die with a DB error.
		$wpdb->error = sprintf(
			/* translators: %s: Database repair URL. */
			__( 'One or more database tables are unavailable. The database may need to be <a href="%s">repaired</a>.' ),
			'maint/repair.php?referrer=is_blog_installed'
		);

		dead_db();
	}

	$wpdb->suppress_errors( $suppress );

	wp_cache_set( 'is_blog_installed', false );

	return false;
}

/**
 * Retrieves URL with nonce added to URL query.
 *
 * @since 2.0.4
 *
 * @param string     $actionurl URL to add nonce action.
 * @param int|string $action    Optional. Nonce action name. Default -1.
 * @param string     $name      Optional. Nonce name. Default '_wpnonce'.
 * @return string Escaped URL with nonce action added.
 */
function wp_nonce_url( $actionurl, $action = -1, $name = '_wpnonce' ) {
	$actionurl = str_replace( '&amp;', '&', $actionurl );
	return esc_html( add_query_arg( $name, wp_create_nonce( $action ), $actionurl ) );
}

/**
 * Retrieves or display nonce hidden field for forms.
 *
 * The nonce field is used to validate that the contents of the form came from
 * the location on the current site and not somewhere else. The nonce does not
 * offer absolute protection, but should protect against most cases. It is very
 * important to use nonce field in forms.
 *
 * The $action and $name are optional, but if you want to have better security,
 * it is strongly suggested to set those two parameters. It is easier to just
 * call the function without any parameters, because validation of the nonce
 * doesn't require any parameters, but since crackers know what the default is
 * it won't be difficult for them to find a way around your nonce and cause
 * damage.
 *
 * The input name will be whatever $name value you gave. The input value will be
 * the nonce creation value.
 *
 * @since 2.0.4
 *
 * @param int|string $action  Optional. Action name. Default -1.
 * @param string     $name    Optional. Nonce name. Default '_wpnonce'.
 * @param bool       $referer Optional. Whether to set the referer field for validation. Default true.
 * @param bool       $display Optional. Whether to display or return hidden form field. Default true.
 * @return string Nonce field HTML markup.
 */
function wp_nonce_field( $action = -1, $name = '_wpnonce', $referer = true, $display = true ) {
	$name        = esc_attr( $name );
	$nonce_field = '<input type="hidden" id="' . $name . '" name="' . $name . '" value="' . wp_create_nonce( $action ) . '" />';

	if ( $referer ) {
		$nonce_field .= wp_referer_field( false );
	}

	if ( $display ) {
		echo $nonce_field;
	}

	return $nonce_field;
}

/**
 * Retrieves or displays referer hidden field for forms.
 *
 * The referer link is the current Request URI from the server super global. The
 * input name is '_wp_http_referer', in case you wanted to check manually.
 *
 * @since 2.0.4
 *
 * @param bool $display Optional. Whether to echo or return the referer field. Default true.
 * @return string Referer field HTML markup.
 */
function wp_referer_field( $display = true ) {
	$request_url   = remove_query_arg( '_wp_http_referer' );
	$referer_field = '<input type="hidden" name="_wp_http_referer" value="' . esc_url( $request_url ) . '" />';

	if ( $display ) {
		echo $referer_field;
	}

	return $referer_field;
}

/**
 * Retrieves or displays original referer hidden field for forms.
 *
 * The input name is '_wp_original_http_referer' and will be either the same
 * value of wp_referer_field(), if that was posted already or it will be the
 * current page, if it doesn't exist.
 *
 * @since 2.0.4
 *
 * @param bool   $display      Optional. Whether to echo the original http referer. Default true.
 * @param string $jump_back_to Optional. Can be 'previous' or page you want to jump back to.
 *                             Default 'current'.
 * @return string Original referer field.
 */
function wp_original_referer_field( $display = true, $jump_back_to = 'current' ) {
	$ref = wp_get_original_referer();

	if ( ! $ref ) {
		$ref = ( 'previous' === $jump_back_to ) ? wp_get_referer() : wp_unslash( $_SERVER['REQUEST_URI'] );
	}

	$orig_referer_field = '<input type="hidden" name="_wp_original_http_referer" value="' . esc_attr( $ref ) . '" />';

	if ( $display ) {
		echo $orig_referer_field;
	}

	return $orig_referer_field;
}

/**
 * Retrieves referer from '_wp_http_referer' or HTTP referer.
 *
 * If it's the same as the current request URL, will return false.
 *
 * @since 2.0.4
 *
 * @return string|false Referer URL on success, false on failure.
 */
function wp_get_referer() {
	// Return early if called before wp_validate_redirect() is defined.
	if ( ! function_exists( 'wp_validate_redirect' ) ) {
		return false;
	}

	$ref = wp_get_raw_referer();

	if ( $ref && wp_unslash( $_SERVER['REQUEST_URI'] ) !== $ref
		&& home_url() . wp_unslash( $_SERVER['REQUEST_URI'] ) !== $ref
	) {
		return wp_validate_redirect( $ref, false );
	}

	return false;
}

/**
 * Retrieves unvalidated referer from the '_wp_http_referer' URL query variable or the HTTP referer.
 *
 * If the value of the '_wp_http_referer' URL query variable is not a string then it will be ignored.
 *
 * Do not use for redirects, use wp_get_referer() instead.
 *
 * @since 4.5.0
 *
 * @return string|false Referer URL on success, false on failure.
 */
function wp_get_raw_referer() {
	if ( ! empty( $_REQUEST['_wp_http_referer'] ) && is_string( $_REQUEST['_wp_http_referer'] ) ) {
		return wp_unslash( $_REQUEST['_wp_http_referer'] );
	} elseif ( ! empty( $_SERVER['HTTP_REFERER'] ) ) {
		return wp_unslash( $_SERVER['HTTP_REFERER'] );
	}

	return false;
}

/**
 * Retrieves original referer that was posted, if it exists.
 *
 * @since 2.0.4
 *
 * @return string|false Original referer URL on success, false on failure.
 */
function wp_get_original_referer() {
	// Return early if called before wp_validate_redirect() is defined.
	if ( ! function_exists( 'wp_validate_redirect' ) ) {
		return false;
	}

	if ( ! empty( $_REQUEST['_wp_original_http_referer'] ) ) {
		return wp_validate_redirect( wp_unslash( $_REQUEST['_wp_original_http_referer'] ), false );
	}

	return false;
}

/**
 * Recursive directory creation based on full path.
 *
 * Will attempt to set permissions on folders.
 *
 * @since 2.0.1
 *
 * @param string $target Full path to attempt to create.
 * @return bool Whether the path was created. True if path already exists.
 */
function wp_mkdir_p( $target ) {
	$wrapper = null;

	// Strip the protocol.
	if ( wp_is_stream( $target ) ) {
		list( $wrapper, $target ) = explode( '://', $target, 2 );
	}

	// From php.net/mkdir user contributed notes.
	$target = str_replace( '//', '/', $target );

	// Put the wrapper back on the target.
	if ( null !== $wrapper ) {
		$target = $wrapper . '://' . $target;
	}

	/*
	 * Safe mode fails with a trailing slash under certain PHP versions.
	 * Use rtrim() instead of untrailingslashit to avoid formatting.php dependency.
	 */
	$target = rtrim( $target, '/' );
	if ( empty( $target ) ) {
		$target = '/';
	}

	if ( file_exists( $target ) ) {
		return @is_dir( $target );
	}

	// Do not allow path traversals.
	if ( str_contains( $target, '../' ) || str_contains( $target, '..' . DIRECTORY_SEPARATOR ) ) {
		return false;
	}

	// We need to find the permissions of the parent folder that exists and inherit that.
	$target_parent = dirname( $target );
	while ( '.' !== $target_parent && ! is_dir( $target_parent ) && dirname( $target_parent ) !== $target_parent ) {
		$target_parent = dirname( $target_parent );
	}

	// Get the permission bits.
	$stat = @stat( $target_parent );
	if ( $stat ) {
		$dir_perms = $stat['mode'] & 0007777;
	} else {
		$dir_perms = 0777;
	}

	if ( @mkdir( $target, $dir_perms, true ) ) {

		/*
		 * If a umask is set that modifies $dir_perms, we'll have to re-set
		 * the $dir_perms correctly with chmod()
		 */
		if ( ( $dir_perms & ~umask() ) !== $dir_perms ) {
			$folder_parts = explode( '/', substr( $target, strlen( $target_parent ) + 1 ) );
			for ( $i = 1, $c = count( $folder_parts ); $i <= $c; $i++ ) {
				chmod( $target_parent . '/' . implode( '/', array_slice( $folder_parts, 0, $i ) ), $dir_perms );
			}
		}

		return true;
	}

	return false;
}

/**
 * Tests if a given filesystem path is absolute.
 *
 * For example, '/foo/bar', or 'c:\windows'.
 *
 * @since 2.5.0
 *
 * @param string $path File path.
 * @return bool True if path is absolute, false is not absolute.
 */
function path_is_absolute( $path ) {
	/*
	 * Check to see if the path is a stream and check to see if its an actual
	 * path or file as realpath() does not support stream wrappers.
	 */
	if ( wp_is_stream( $path ) && ( is_dir( $path ) || is_file( $path ) ) ) {
		return true;
	}

	/*
	 * This is definitive if true but fails if $path does not exist or contains
	 * a symbolic link.
	 */
	if ( realpath( $path ) === $path ) {
		return true;
	}

	if ( strlen( $path ) === 0 || '.' === $path[0] ) {
		return false;
	}

	// Windows allows absolute paths like this.
	if ( preg_match( '#^[a-zA-Z]:\\\\#', $path ) ) {
		return true;
	}

	// A path starting with / or \ is absolute; anything else is relative.
	return ( '/' === $path[0] || '\\' === $path[0] );
}

/**
 * Joins two filesystem paths together.
 *
 * For example, 'give me $path relative to $base'. If the $path is absolute,
 * then it the full path is returned.
 *
 * @since 2.5.0
 *
 * @param string $base Base path.
 * @param string $path Path relative to $base.
 * @return string The path with the base or absolute path.
 */
function path_join( $base, $path ) {
	if ( path_is_absolute( $path ) ) {
		return $path;
	}

	return rtrim( $base, '/' ) . '/' . $path;
}

/**
 * Normalizes a filesystem path.
 *
 * On windows systems, replaces backslashes with forward slashes
 * and forces upper-case drive letters.
 * Allows for two leading slashes for Windows network shares, but
 * ensures that all other duplicate slashes are reduced to a single.
 *
 * @since 3.9.0
 * @since 4.4.0 Ensures upper-case drive letters on Windows systems.
 * @since 4.5.0 Allows for Windows network shares.
 * @since 4.9.7 Allows for PHP file wrappers.
 * @since latest TODO Allows for null/falsy paths (returns whatever was passed)
 *
 * @param string $path Path to normalize.
 * @return string Normalized path.
 */
function wp_normalize_path( $path ) {
        if (!$path) {
                return $path;
        }
	$wrapper = '';

	if ( wp_is_stream( $path ) ) {
		list( $wrapper, $path ) = explode( '://', $path, 2 );

		$wrapper .= '://';
	}

	// Standardize all paths to use '/'.
	$path = str_replace( '\\', '/', $path );

	// Replace multiple slashes down to a singular, allowing for network shares having two slashes.
	$path = preg_replace( '|(?<=.)/+|', '/', $path );

	// Windows paths should uppercase the drive letter.
	if ( ':' === substr( $path, 1, 1 ) ) {
		$path = ucfirst( $path );
	}

	return $wrapper . $path;
}

/**
 * Determines a writable directory for temporary files.
 *
 * Function's preference is the return value of sys_get_temp_dir(),
 * followed by your PHP temporary upload directory, followed by WP_CONTENT_DIR,
 * before finally defaulting to /tmp/
 *
 * In the event that this function does not find a writable location,
 * It may be overridden by the WP_TEMP_DIR constant in your wp-config.php file.
 *
 * @since 2.5.0
 *
 * @return string Writable temporary directory.
 */
function get_temp_dir() {
	static $temp = '';
	if ( defined( 'WP_TEMP_DIR' ) ) {
		return trailingslashit( WP_TEMP_DIR );
	}

	if ( $temp ) {
		return trailingslashit( $temp );
	}

	if ( function_exists( 'sys_get_temp_dir' ) ) {
		$temp = sys_get_temp_dir();
		if ( @is_dir( $temp ) && wp_is_writable( $temp ) ) {
			return trailingslashit( $temp );
		}
	}

	$temp = ini_get( 'upload_tmp_dir' );
	if ( @is_dir( $temp ) && wp_is_writable( $temp ) ) {
		return trailingslashit( $temp );
	}

	$temp = WP_CONTENT_DIR . '/';
	if ( is_dir( $temp ) && wp_is_writable( $temp ) ) {
		return $temp;
	}

	return '/tmp/';
}

/**
 * Determines if a directory is writable.
 *
 * This function is used to work around certain ACL issues in PHP primarily
 * affecting Windows Servers.
 *
 * @since 3.6.0
 *
 * @see win_is_writable()
 *
 * @param string $path Path to check for write-ability.
 * @return bool Whether the path is writable.
 */
function wp_is_writable( $path ) {
	if ( 'Windows' === PHP_OS_FAMILY ) {
		return win_is_writable( $path );
	}

	return @is_writable( $path );
}

/**
 * Workaround for Windows bug in is_writable() function
 *
 * PHP has issues with Windows ACL's for determine if a
 * directory is writable or not, this works around them by
 * checking the ability to open files rather than relying
 * upon PHP to interpret the OS ACL.
 *
 * @since 2.8.0
 *
 * @see https://bugs.php.net/bug.php?id=27609
 * @see https://bugs.php.net/bug.php?id=30931
 *
 * @param string $path Windows path to check for write-ability.
 * @return bool Whether the path is writable.
 */
function win_is_writable( $path ) {
	if ( '/' === $path[ strlen( $path ) - 1 ] ) {
		// If it looks like a directory, check a random file within the directory.
		return win_is_writable( $path . uniqid( mt_rand() ) . '.tmp' );
	} elseif ( is_dir( $path ) ) {
		// If it's a directory (and not a file), check a random file within the directory.
		return win_is_writable( $path . '/' . uniqid( mt_rand() ) . '.tmp' );
	}

	// Check tmp file for read/write capabilities.
	$should_delete_tmp_file = ! file_exists( $path );

	$f = @fopen( $path, 'a' );
	if ( false === $f ) {
		return false;
	}
	fclose( $f );

	if ( $should_delete_tmp_file ) {
		unlink( $path );
	}

	return true;
}

/**
 * Retrieves uploads directory information.
 *
 * Same as wp_upload_dir() but "light weight" as it doesn't attempt to create the uploads directory.
 * Intended for use in themes, when only 'basedir' and 'baseurl' are needed, generally in all cases
 * when not uploading files.
 *
 * @since 4.5.0
 *
 * @see wp_upload_dir()
 *
 * @return array See wp_upload_dir() for description.
 */
function wp_get_upload_dir() {
	return wp_upload_dir( null, false );
}

/**
 * Returns an array containing the current upload directory's path and URL.
 *
 * Checks the 'upload_path' option, which should be from the web root folder,
 * and if it isn't empty it will be used. If it is empty, then the path will be
 * 'WP_CONTENT_DIR/uploads'. If the 'UPLOADS' constant is defined, then it will
 * override the 'upload_path' option and 'WP_CONTENT_DIR/uploads' path.
 *
 * The upload URL path is set either by the 'upload_url_path' option or by using
 * the 'WP_CONTENT_URL' constant and appending '/uploads' to the path.
 *
 * If the 'uploads_use_yearmonth_folders' is set to true (checkbox if checked in
 * the administration settings panel), then the time will be used. The format
 * will be year first and then month.
 *
 * If the path couldn't be created, then an error will be returned with the key
 * 'error' containing the error message. The error suggests that the parent
 * directory is not writable by the server.
 *
 * @since 2.0.0
 * @uses _wp_upload_dir()
 *
 * @param string|null $time          Optional. Time formatted in 'yyyy/mm'. Default null.
 * @param bool        $create_dir    Optional. Whether to check and create the uploads directory.
 *                                   Default true for backward compatibility.
 * @param bool        $refresh_cache Optional. Whether to refresh the cache. Default false.
 * @return array {
 *     Array of information about the upload directory.
 *
 *     @type string       $path    Base directory and subdirectory or full path to upload directory.
 *     @type string       $url     Base URL and subdirectory or absolute URL to upload directory.
 *     @type string       $subdir  Subdirectory if uploads use year/month folders option is on.
 *     @type string       $basedir Path without subdir.
 *     @type string       $baseurl URL path without subdir.
 *     @type string|false $error   False or error message.
 * }
 */
function wp_upload_dir( $time = null, $create_dir = true, $refresh_cache = false ) {
	static $cache = array(), $tested_paths = array();

	$key = sprintf( '%d-%s', get_current_blog_id(), (string) $time );

	if ( $refresh_cache || empty( $cache[ $key ] ) ) {
		$cache[ $key ] = _wp_upload_dir( $time );
	}

	/**
	 * Filters the uploads directory data.
	 *
	 * @since 2.0.0
	 *
	 * @param array $uploads {
	 *     Array of information about the upload directory.
	 *
	 *     @type string       $path    Base directory and subdirectory or full path to upload directory.
	 *     @type string       $url     Base URL and subdirectory or absolute URL to upload directory.
	 *     @type string       $subdir  Subdirectory if uploads use year/month folders option is on.
	 *     @type string       $basedir Path without subdir.
	 *     @type string       $baseurl URL path without subdir.
	 *     @type string|false $error   False or error message.
	 * }
	 */
	$uploads = apply_filters( 'upload_dir', $cache[ $key ] );

	if ( $create_dir ) {
		$path = $uploads['path'];

		if ( array_key_exists( $path, $tested_paths ) ) {
			$uploads['error'] = $tested_paths[ $path ];
		} else {
			if ( ! wp_mkdir_p( $path ) ) {
				if ( str_starts_with( $uploads['basedir'], ABSPATH ) ) {
					$error_path = str_replace( ABSPATH, '', $uploads['basedir'] ) . $uploads['subdir'];
				} else {
					$error_path = wp_basename( $uploads['basedir'] ) . $uploads['subdir'];
				}

				$uploads['error'] = sprintf(
					/* translators: %s: Directory path. */
					__( 'Unable to create directory %s. Is its parent directory writable by the server?' ),
					esc_html( $error_path )
				);
			}

			$tested_paths[ $path ] = $uploads['error'];
		}
	}

	return $uploads;
}

/**
 * A non-filtered, non-cached version of wp_upload_dir() that doesn't check the path.
 *
 * @since 4.5.0
 * @access private
 *
 * @param string|null $time Optional. Time formatted in 'yyyy/mm'. Default null.
 * @return array See wp_upload_dir()
 */
function _wp_upload_dir( $time = null ) {
	$siteurl     = get_option( 'siteurl' );
	$upload_path = trim( get_option( 'upload_path' ) );

	if ( empty( $upload_path ) || 'wp-content/uploads' === $upload_path ) {
		$dir = WP_CONTENT_DIR . '/uploads';
	} elseif ( ! str_starts_with( $upload_path, ABSPATH ) ) {
		// $dir is absolute, $upload_path is (maybe) relative to ABSPATH.
		$dir = path_join( ABSPATH, $upload_path );
	} else {
		$dir = $upload_path;
	}

	$url = get_option( 'upload_url_path' );
	if ( ! $url ) {
		if ( empty( $upload_path ) || ( 'wp-content/uploads' === $upload_path ) || ( $upload_path === $dir ) ) {
			$url = WP_CONTENT_URL . '/uploads';
		} else {
			$url = trailingslashit( $siteurl ) . $upload_path;
		}
	}

	/*
	 * Honor the value of UPLOADS. This happens as long as ms-files rewriting is disabled.
	 * We also sometimes obey UPLOADS when rewriting is enabled -- see the next block.
	 */
	if ( defined( 'UPLOADS' ) && ! ( is_multisite() && get_site_option( 'ms_files_rewriting' ) ) ) {
		$dir = ABSPATH . UPLOADS;
		$url = trailingslashit( $siteurl ) . UPLOADS;
	}

	// If multisite (and if not the main site in a post-MU network).
	if ( is_multisite() && ! ( is_main_network() && is_main_site() && defined( 'MULTISITE' ) ) ) {

		if ( ! get_site_option( 'ms_files_rewriting' ) ) {
			/*
			 * If ms-files rewriting is disabled (networks created post-3.5), it is fairly
			 * straightforward: Append sites/%d if we're not on the main site (for post-MU
			 * networks). (The extra directory prevents a four-digit ID from conflicting with
			 * a year-based directory for the main site. But if a MU-era network has disabled
			 * ms-files rewriting manually, they don't need the extra directory, as they never
			 * had wp-content/uploads for the main site.)
			 */

			if ( defined( 'MULTISITE' ) ) {
				$ms_dir = '/sites/' . get_current_blog_id();
			} else {
				$ms_dir = '/' . get_current_blog_id();
			}

			$dir .= $ms_dir;
			$url .= $ms_dir;

		} elseif ( defined( 'UPLOADS' ) && ! ms_is_switched() ) {
			/*
			 * Handle the old-form ms-files.php rewriting if the network still has that enabled.
			 * When ms-files rewriting is enabled, then we only listen to UPLOADS when:
			 * 1) We are not on the main site in a post-MU network, as wp-content/uploads is used
			 *    there, and
			 * 2) We are not switched, as ms_upload_constants() hardcodes these constants to reflect
			 *    the original blog ID.
			 *
			 * Rather than UPLOADS, we actually use BLOGUPLOADDIR if it is set, as it is absolute.
			 * (And it will be set, see ms_upload_constants().) Otherwise, UPLOADS can be used, as
			 * as it is relative to ABSPATH. For the final piece: when UPLOADS is used with ms-files
			 * rewriting in multisite, the resulting URL is /files. (#WP22702 for background.)
			 */

			if ( defined( 'BLOGUPLOADDIR' ) ) {
				$dir = untrailingslashit( BLOGUPLOADDIR );
			} else {
				$dir = ABSPATH . UPLOADS;
			}
			$url = trailingslashit( $siteurl ) . 'files';
		}
	}

	$basedir = $dir;
	$baseurl = $url;

	$subdir = '';
	if ( get_option( 'uploads_use_yearmonth_folders' ) ) {
		// Generate the yearly and monthly directories.
		if ( ! $time ) {
			$time = current_time( 'mysql' );
		}
		$y      = substr( $time, 0, 4 );
		$m      = substr( $time, 5, 2 );
		$subdir = "/$y/$m";
	}

	$dir .= $subdir;
	$url .= $subdir;

	return array(
		'path'    => $dir,
		'url'     => $url,
		'subdir'  => $subdir,
		'basedir' => $basedir,
		'baseurl' => $baseurl,
		'error'   => false,
	);
}

/**
 * Gets a filename that is sanitized and unique for the given directory.
 *
 * If the filename is not unique, then a number will be added to the filename
 * before the extension, and will continue adding numbers until the filename
 * is unique.
 *
 * The callback function allows the caller to use their own method to create
 * unique file names. If defined, the callback should take three arguments:
 * - directory, base filename, and extension - and return a unique filename.
 *
 * @since 2.5.0
 *
 * @param string   $dir                      Directory.
 * @param string   $filename                 File name.
 * @param callable $unique_filename_callback Callback. Default null.
 * @return string New filename, if given wasn't unique.
 */
function wp_unique_filename( $dir, $filename, $unique_filename_callback = null ) {
	// Sanitize the file name before we begin processing.
	$filename = sanitize_file_name( $filename );
	$ext2     = null;

	// Initialize vars used in the wp_unique_filename filter.
	$number        = '';
	$alt_filenames = array();

	// Separate the filename into a name and extension.
	$ext  = pathinfo( $filename, PATHINFO_EXTENSION );
	$name = pathinfo( $filename, PATHINFO_BASENAME );

	if ( $ext ) {
		$ext = '.' . $ext;
	}

	// Edge case: if file is named '.ext', treat as an empty name.
	if ( $name === $ext ) {
		$name = '';
	}

	/*
	 * Increment the file number until we have a unique file to save in $dir.
	 * Use callback if supplied.
	 */
	if ( $unique_filename_callback && is_callable( $unique_filename_callback ) ) {
		$filename = call_user_func( $unique_filename_callback, $dir, $name, $ext );
	} else {
		$fname = pathinfo( $filename, PATHINFO_FILENAME );

		// Always append a number to file names that can potentially match image sub-size file names.
		if ( $fname && preg_match( '/-(?:\d+x\d+|scaled|rotated)$/', $fname ) ) {
			$number = 1;

			// At this point the file name may not be unique. This is tested below and the $number is incremented.
			$filename = str_replace( "{$fname}{$ext}", "{$fname}-{$number}{$ext}", $filename );
		}

		/*
		 * Get the mime type. Uploaded files were already checked with wp_check_filetype_and_ext()
		 * in _wp_handle_upload(). Using wp_check_filetype() would be sufficient here.
		 */
		$file_type = wp_check_filetype( $filename );
		$mime_type = $file_type['type'];

		$is_image    = ( ! empty( $mime_type ) && str_starts_with( $mime_type, 'image/' ) );
		$upload_dir  = wp_get_upload_dir();
		$lc_filename = null;

		$lc_ext = strtolower( $ext );
		$_dir   = trailingslashit( $dir );

		/*
		 * If the extension is uppercase add an alternate file name with lowercase extension.
		 * Both need to be tested for uniqueness as the extension will be changed to lowercase
		 * for better compatibility with different filesystems. Fixes an inconsistency in WP < 2.9
		 * where uppercase extensions were allowed but image sub-sizes were created with
		 * lowercase extensions.
		 */
		if ( $ext && $lc_ext !== $ext ) {
			$lc_filename = preg_replace( '|' . preg_quote( $ext ) . '$|', $lc_ext, $filename );
		}

		/*
		 * Increment the number added to the file name if there are any files in $dir
		 * whose names match one of the possible name variations.
		 */
		while ( file_exists( $_dir . $filename ) || ( $lc_filename && file_exists( $_dir . $lc_filename ) ) ) {
			$new_number = (int) $number + 1;

			if ( $lc_filename ) {
				$lc_filename = str_replace(
					array( "-{$number}{$lc_ext}", "{$number}{$lc_ext}" ),
					"-{$new_number}{$lc_ext}",
					$lc_filename
				);
			}

			if ( '' === "{$number}{$ext}" ) {
				$filename = "{$filename}-{$new_number}";
			} else {
				$filename = str_replace(
					array( "-{$number}{$ext}", "{$number}{$ext}" ),
					"-{$new_number}{$ext}",
					$filename
				);
			}

			$number = $new_number;
		}

		// Change the extension to lowercase if needed.
		if ( $lc_filename ) {
			$filename = $lc_filename;
		}

		/*
		 * Prevent collisions with existing file names that contain dimension-like strings
		 * (whether they are subsizes or originals uploaded prior to #42437).
		 */

		$files = array();
		$count = 10000;

		// The (resized) image files would have name and extension, and will be in the uploads dir.
		if ( $name && $ext && @is_dir( $dir ) && str_contains( $dir, $upload_dir['basedir'] ) ) {
			/**
			 * Filters the file list used for calculating a unique filename for a newly added file.
			 *
			 * Returning an array from the filter will effectively short-circuit retrieval
			 * from the filesystem and return the passed value instead.
			 *
			 * @since 5.5.0
			 *
			 * @param array|null $files    The list of files to use for filename comparisons.
			 *                             Default null (to retrieve the list from the filesystem).
			 * @param string     $dir      The directory for the new file.
			 * @param string     $filename The proposed filename for the new file.
			 */
			$files = apply_filters( 'pre_wp_unique_filename_file_list', null, $dir, $filename );

			if ( null === $files ) {
				// List of all files and directories contained in $dir.
				$files = @scandir( $dir );
			}

			if ( ! empty( $files ) ) {
				// Remove "dot" dirs.
				$files = array_diff( $files, array( '.', '..' ) );
			}

			if ( ! empty( $files ) ) {
				$count = count( $files );

				/*
				 * Ensure this never goes into infinite loop as it uses pathinfo() and regex in the check,
				 * but string replacement for the changes.
				 */
				$i = 0;

				while ( $i <= $count && _wp_check_existing_file_names( $filename, $files ) ) {
					$new_number = (int) $number + 1;

					// If $ext is uppercase it was replaced with the lowercase version after the previous loop.
					$filename = str_replace(
						array( "-{$number}{$lc_ext}", "{$number}{$lc_ext}" ),
						"-{$new_number}{$lc_ext}",
						$filename
					);

					$number = $new_number;
					++$i;
				}
			}
		}

		/*
		 * Check if an image will be converted after uploading or some existing image sub-size file names may conflict
		 * when regenerated. If yes, ensure the new file name will be unique and will produce unique sub-sizes.
		 */
		if ( $is_image ) {
			$output_formats = wp_get_image_editor_output_format( $_dir . $filename, $mime_type );
			$alt_types      = array();

			if ( ! empty( $output_formats[ $mime_type ] ) ) {
				// The image will be converted to this format/mime type.
				$alt_mime_type = $output_formats[ $mime_type ];

				// Other types of images whose names may conflict if their sub-sizes are regenerated.
				$alt_types   = array_keys( array_intersect( $output_formats, array( $mime_type, $alt_mime_type ) ) );
				$alt_types[] = $alt_mime_type;
			} elseif ( ! empty( $output_formats ) ) {
				$alt_types = array_keys( array_intersect( $output_formats, array( $mime_type ) ) );
			}

			// Remove duplicates and the original mime type. It will be added later if needed.
			$alt_types = array_unique( array_diff( $alt_types, array( $mime_type ) ) );

			foreach ( $alt_types as $alt_type ) {
				$alt_ext = wp_get_default_extension_for_mime_type( $alt_type );

				if ( ! $alt_ext ) {
					continue;
				}

				$alt_ext      = ".{$alt_ext}";
				$alt_filename = preg_replace( '|' . preg_quote( $lc_ext ) . '$|', $alt_ext, $filename );

				$alt_filenames[ $alt_ext ] = $alt_filename;
			}

			if ( ! empty( $alt_filenames ) ) {
				/*
				 * Add the original filename. It needs to be checked again
				 * together with the alternate filenames when $number is incremented.
				 */
				$alt_filenames[ $lc_ext ] = $filename;

				// Ensure no infinite loop.
				$i = 0;

				while ( $i <= $count && _wp_check_alternate_file_names( $alt_filenames, $_dir, $files ) ) {
					$new_number = (int) $number + 1;

					foreach ( $alt_filenames as $alt_ext => $alt_filename ) {
						$alt_filenames[ $alt_ext ] = str_replace(
							array( "-{$number}{$alt_ext}", "{$number}{$alt_ext}" ),
							"-{$new_number}{$alt_ext}",
							$alt_filename
						);
					}

					/*
					 * Also update the $number in (the output) $filename.
					 * If the extension was uppercase it was already replaced with the lowercase version.
					 */
					$filename = str_replace(
						array( "-{$number}{$lc_ext}", "{$number}{$lc_ext}" ),
						"-{$new_number}{$lc_ext}",
						$filename
					);

					$number = $new_number;
					++$i;
				}
			}
		}
	}

	/**
	 * Filters the result when generating a unique file name.
	 *
	 * @since 4.5.0
	 * @since 5.8.1 The `$alt_filenames` and `$number` parameters were added.
	 *
	 * @param string        $filename                 Unique file name.
	 * @param string        $ext                      File extension. Example: ".png".
	 * @param string        $dir                      Directory path.
	 * @param callable|null $unique_filename_callback Callback function that generates the unique file name.
	 * @param string[]      $alt_filenames            Array of alternate file names that were checked for collisions.
	 * @param int|string    $number                   The highest number that was used to make the file name unique
	 *                                                or an empty string if unused.
	 */
	return apply_filters( 'wp_unique_filename', $filename, $ext, $dir, $unique_filename_callback, $alt_filenames, $number );
}

/**
 * Helper function to test if each of an array of file names could conflict with existing files.
 *
 * @since 5.8.1
 * @access private
 *
 * @param string[] $filenames Array of file names to check.
 * @param string   $dir       The directory containing the files.
 * @param array    $files     An array of existing files in the directory. May be empty.
 * @return bool True if the tested file name could match an existing file, false otherwise.
 */
function _wp_check_alternate_file_names( $filenames, $dir, $files ) {
	foreach ( $filenames as $filename ) {
		if ( file_exists( $dir . $filename ) ) {
			return true;
		}

		if ( ! empty( $files ) && _wp_check_existing_file_names( $filename, $files ) ) {
			return true;
		}
	}

	return false;
}

/**
 * Helper function to check if a file name could match an existing image sub-size file name.
 *
 * @since 5.3.1
 * @access private
 *
 * @param string $filename The file name to check.
 * @param array  $files    An array of existing files in the directory.
 * @return bool True if the tested file name could match an existing file, false otherwise.
 */
function _wp_check_existing_file_names( $filename, $files ) {
	$fname = pathinfo( $filename, PATHINFO_FILENAME );
	$ext   = pathinfo( $filename, PATHINFO_EXTENSION );

	// Edge case, file names like `.ext`.
	if ( empty( $fname ) ) {
		return false;
	}

	if ( $ext ) {
		$ext = ".$ext";
	}

	$regex = '/^' . preg_quote( $fname ) . '-(?:\d+x\d+|scaled|rotated)' . preg_quote( $ext ) . '$/i';

	foreach ( $files as $file ) {
		if ( preg_match( $regex, $file ) ) {
			return true;
		}
	}

	return false;
}

/**
 * Creates a file in the upload folder with given content.
 *
 * If there is an error, then the key 'error' will exist with the error message.
 * If success, then the key 'file' will have the unique file path, the 'url' key
 * will have the link to the new file. and the 'error' key will be set to false.
 *
 * This function will not move an uploaded file to the upload folder. It will
 * create a new file with the content in $bits parameter. If you move the upload
 * file, read the content of the uploaded file, and then you can give the
 * filename and content to this function, which will add it to the upload
 * folder.
 *
 * The permissions will be set on the new file automatically by this function.
 *
 * @since 2.0.0
 *
 * @param string      $name       Filename.
 * @param null|string $deprecated Never used. Set to null.
 * @param string      $bits       File content
 * @param string|null $time       Optional. Time formatted in 'yyyy/mm'. Default null.
 * @return array {
 *     Information about the newly-uploaded file.
 *
 *     @type string       $file  Filename of the newly-uploaded file.
 *     @type string       $url   URL of the uploaded file.
 *     @type string       $type  File type.
 *     @type string|false $error Error message, if there has been an error.
 * }
 */
function wp_upload_bits( $name, $deprecated, $bits, $time = null ) {
	if ( ! empty( $deprecated ) ) {
		_deprecated_argument( __FUNCTION__, '2.0.0' );
	}

	if ( empty( $name ) ) {
		return array( 'error' => __( 'Empty filename' ) );
	}

	$wp_filetype = wp_check_filetype( $name );
	if ( ! $wp_filetype['ext'] && ! current_user_can( 'unfiltered_upload' ) ) {
		return array( 'error' => __( 'Sorry, you are not allowed to upload this file type.' ) );
	}

	$upload = wp_upload_dir( $time );

	if ( false !== $upload['error'] ) {
		return $upload;
	}

	/**
	 * Filters whether to treat the upload bits as an error.
	 *
	 * Returning a non-array from the filter will effectively short-circuit preparing the upload bits
	 * and return that value instead. An error message should be returned as a string.
	 *
	 * @since 3.0.0
	 *
	 * @param array|string $upload_bits_error An array of upload bits data, or error message to return.
	 */
	$upload_bits_error = apply_filters(
		'wp_upload_bits',
		array(
			'name' => $name,
			'bits' => $bits,
			'time' => $time,
		)
	);
	if ( ! is_array( $upload_bits_error ) ) {
		$upload['error'] = $upload_bits_error;
		return $upload;
	}

	$filename = wp_unique_filename( $upload['path'], $name );

	$new_file = $upload['path'] . "/$filename";
	if ( ! wp_mkdir_p( dirname( $new_file ) ) ) {
		if ( str_starts_with( $upload['basedir'], ABSPATH ) ) {
			$error_path = str_replace( ABSPATH, '', $upload['basedir'] ) . $upload['subdir'];
		} else {
			$error_path = wp_basename( $upload['basedir'] ) . $upload['subdir'];
		}

		$message = sprintf(
			/* translators: %s: Directory path. */
			__( 'Unable to create directory %s. Is its parent directory writable by the server?' ),
			$error_path
		);
		return array( 'error' => $message );
	}

	$ifp = @fopen( $new_file, 'wb' );
	if ( ! $ifp ) {
		return array(
			/* translators: %s: File name. */
			'error' => sprintf( __( 'Could not write file %s' ), $new_file ),
		);
	}

	fwrite( $ifp, $bits );
	fclose( $ifp );
	clearstatcache();

	// Set correct file permissions.
	$stat  = @ stat( dirname( $new_file ) );
	$perms = $stat['mode'] & 0007777;
	$perms = $perms & 0000666;
	chmod( $new_file, $perms );
	clearstatcache();

	// Compute the URL.
	$url = $upload['url'] . "/$filename";

	if ( is_multisite() ) {
		clean_dirsize_cache( $new_file );
	}

	/** This filter is documented in wp-admin/includes/file.php */
	return apply_filters(
		'wp_handle_upload',
		array(
			'file'  => $new_file,
			'url'   => $url,
			'type'  => $wp_filetype['type'],
			'error' => false,
		),
		'sideload'
	);
}

/**
 * Retrieves the file type based on the extension name.
 *
 * @since 2.5.0
 *
 * @param string $ext The extension to search.
 * @return string|void The file type, example: audio, video, document, spreadsheet, etc.
 */
function wp_ext2type( $ext ) {
	$ext = strtolower( $ext );

	$ext2type = wp_get_ext_types();
	foreach ( $ext2type as $type => $exts ) {
		if ( in_array( $ext, $exts, true ) ) {
			return $type;
		}
	}
}

/**
 * Returns first matched extension for the mime-type,
 * as mapped from wp_get_mime_types().
 *
 * @since 5.8.1
 *
 * @param string $mime_type
 *
 * @return string|false
 */
function wp_get_default_extension_for_mime_type( $mime_type ) {
	$extensions = explode( '|', array_search( $mime_type, wp_get_mime_types(), true ) );

	if ( empty( $extensions[0] ) ) {
		return false;
	}

	return $extensions[0];
}

/**
 * Retrieves the file type from the file name.
 *
 * You can optionally define the mime array, if needed.
 *
 * @since 2.0.4
 *
 * @param string        $filename File name or path.
 * @param string[]|null $mimes    Optional. Array of allowed mime types keyed by their file extension regex.
 *                                Defaults to the result of get_allowed_mime_types().
 * @return array {
 *     Values for the extension and mime type.
 *
 *     @type string|false $ext  File extension, or false if the file doesn't match a mime type.
 *     @type string|false $type File mime type, or false if the file doesn't match a mime type.
 * }
 */
function wp_check_filetype( $filename, $mimes = null ) {
	if ( empty( $mimes ) ) {
		$mimes = get_allowed_mime_types();
	}
	$type = false;
	$ext  = false;

	foreach ( $mimes as $ext_preg => $mime_match ) {
		$ext_preg = '!\.(' . $ext_preg . ')$!i';
		if ( preg_match( $ext_preg, $filename, $ext_matches ) ) {
			$type = $mime_match;
			$ext  = $ext_matches[1];
			break;
		}
	}

	return compact( 'ext', 'type' );
}

/**
 * Attempts to determine the real file type of a file.
 *
 * If unable to, the file name extension will be used to determine type.
 *
 * If it's determined that the extension does not match the file's real type,
 * then the "proper_filename" value will be set with a proper filename and extension.
 *
 * Currently this function only supports renaming images validated via wp_get_image_mime().
 *
 * @since 3.0.0
 *
 * @param string        $file     Full path to the file.
 * @param string        $filename The name of the file (may differ from $file due to $file being
 *                                in a tmp directory).
 * @param string[]|null $mimes    Optional. Array of allowed mime types keyed by their file extension regex.
 *                                Defaults to the result of get_allowed_mime_types().
 * @return array {
 *     Values for the extension, mime type, and corrected filename.
 *
 *     @type string|false $ext             File extension, or false if the file doesn't match a mime type.
 *     @type string|false $type            File mime type, or false if the file doesn't match a mime type.
 *     @type string|false $proper_filename File name with its correct extension, or false if it cannot be determined.
 * }
 */
function wp_check_filetype_and_ext( $file, $filename, $mimes = null ) {
	$proper_filename = false;

	// Do basic extension validation and MIME mapping.
	$wp_filetype = wp_check_filetype( $filename, $mimes );
	$ext         = $wp_filetype['ext'];
	$type        = $wp_filetype['type'];

	// We can't do any further validation without a file to work with.
	if ( ! file_exists( $file ) ) {
		return compact( 'ext', 'type', 'proper_filename' );
	}

	$real_mime = false;

	// Validate image types.
	if ( $type && str_starts_with( $type, 'image/' ) ) {

		// Attempt to figure out what type of image it actually is.
		$real_mime = wp_get_image_mime( $file );

		$heic_images_extensions = array(
			'heif',
			'heics',
			'heifs',
		);

		if ( $real_mime && ( $real_mime !== $type || in_array( $ext, $heic_images_extensions, true ) ) ) {
			/**
			 * Filters the list mapping image mime types to their respective extensions.
			 *
			 * @since 3.0.0
			 *
			 * @param array $mime_to_ext Array of image mime types and their matching extensions.
			 */
			$mime_to_ext = apply_filters(
				'getimagesize_mimes_to_exts',
				array(
					'image/jpeg'          => 'jpg',
					'image/png'           => 'png',
					'image/gif'           => 'gif',
					'image/bmp'           => 'bmp',
					'image/tiff'          => 'tif',
					'image/webp'          => 'webp',
					'image/avif'          => 'avif',

					/*
					 * In theory there are/should be file extensions that correspond to the
					 * mime types: .heif, .heics and .heifs. However it seems that HEIC images
					 * with any of the mime types commonly have a .heic file extension.
					 * Seems keeping the status quo here is best for compatibility.
					 */
					'image/heic'          => 'heic',
					'image/heif'          => 'heic',
					'image/heic-sequence' => 'heic',
					'image/heif-sequence' => 'heic',
				)
			);

			// Replace whatever is after the last period in the filename with the correct extension.
			if ( ! empty( $mime_to_ext[ $real_mime ] ) ) {
				$filename_parts = explode( '.', $filename );

				array_pop( $filename_parts );
				$filename_parts[] = $mime_to_ext[ $real_mime ];
				$new_filename     = implode( '.', $filename_parts );

				if ( $new_filename !== $filename ) {
					$proper_filename = $new_filename; // Mark that it changed.
				}

				// Redefine the extension / MIME.
				$wp_filetype = wp_check_filetype( $new_filename, $mimes );
				$ext         = $wp_filetype['ext'];
				$type        = $wp_filetype['type'];
			} else {
				// Reset $real_mime and try validating again.
				$real_mime = false;
			}
		}
	}

	// Validate files that didn't get validated during previous checks.
	if ( $type && ! $real_mime && extension_loaded( 'fileinfo' ) ) {
		$finfo     = finfo_open( FILEINFO_MIME_TYPE );
		$real_mime = finfo_file( $finfo, $file );
		finfo_close( $finfo );

		$google_docs_types = array(
			'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
			'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
		);

		foreach ( $google_docs_types as $google_docs_type ) {
			/*
			 * finfo_file() can return duplicate mime type for Google docs,
			 * this conditional reduces it to a single instance.
			 *
			 * @see https://bugs.php.net/bug.php?id=77784
			 * @see https://core.trac.wordpress.org/ticket/57898
			 */
			if ( 2 === substr_count( $real_mime, $google_docs_type ) ) {
				$real_mime = $google_docs_type;
			}
		}

		// fileinfo often misidentifies obscure files as one of these types.
		$nonspecific_types = array(
			'application/octet-stream',
			'application/encrypted',
			'application/CDFV2-encrypted',
			'application/zip',
		);

		/*
		 * If $real_mime doesn't match the content type we're expecting from the file's extension,
		 * we need to do some additional vetting. Media types and those listed in $nonspecific_types are
		 * allowed some leeway, but anything else must exactly match the real content type.
		 */
		if ( in_array( $real_mime, $nonspecific_types, true ) ) {
			// File is a non-specific binary type. That's ok if it's a type that generally tends to be binary.
			if ( ! in_array( substr( $type, 0, strcspn( $type, '/' ) ), array( 'application', 'video', 'audio' ), true ) ) {
				$type = false;
				$ext  = false;
			}
		} elseif ( str_starts_with( $real_mime, 'video/' ) || str_starts_with( $real_mime, 'audio/' ) ) {
			/*
			 * For these types, only the major type must match the real value.
			 * This means that common mismatches are forgiven: application/vnd.apple.numbers is often misidentified as application/zip,
			 * and some media files are commonly named with the wrong extension (.mov instead of .mp4)
			 */
			if ( substr( $real_mime, 0, strcspn( $real_mime, '/' ) ) !== substr( $type, 0, strcspn( $type, '/' ) ) ) {
				$type = false;
				$ext  = false;
			}
		} elseif ( 'text/plain' === $real_mime ) {
			// A few common file types are occasionally detected as text/plain; allow those.
			if ( ! in_array(
				$type,
				array(
					'text/plain',
					'text/csv',
					'application/csv',
					'text/richtext',
					'text/tsv',
					'text/vtt',
				),
				true
			)
			) {
				$type = false;
				$ext  = false;
			}
		} elseif ( 'application/csv' === $real_mime ) {
			// Special casing for CSV files.
			if ( ! in_array(
				$type,
				array(
					'text/csv',
					'text/plain',
					'application/csv',
				),
				true
			)
			) {
				$type = false;
				$ext  = false;
			}
		} elseif ( 'text/rtf' === $real_mime ) {
			// Special casing for RTF files.
			if ( ! in_array(
				$type,
				array(
					'text/rtf',
					'text/plain',
					'application/rtf',
				),
				true
			)
			) {
				$type = false;
				$ext  = false;
			}
		} else {
			if ( $type !== $real_mime ) {
				/*
				 * Everything else including image/* and application/*:
				 * If the real content type doesn't match the file extension, assume it's dangerous.
				 */
				$type = false;
				$ext  = false;
			}
		}
	}

	// The mime type must be allowed.
	if ( $type ) {
		$allowed = get_allowed_mime_types();

		if ( ! in_array( $type, $allowed, true ) ) {
			$type = false;
			$ext  = false;
		}
	}

	/**
	 * Filters the "real" file type of the given file.
	 *
	 * @since 3.0.0
	 * @since 5.1.0 The $real_mime parameter was added.
	 *
	 * @param array         $wp_check_filetype_and_ext {
	 *     Values for the extension, mime type, and corrected filename.
	 *
	 *     @type string|false $ext             File extension, or false if the file doesn't match a mime type.
	 *     @type string|false $type            File mime type, or false if the file doesn't match a mime type.
	 *     @type string|false $proper_filename File name with its correct extension, or false if it cannot be determined.
	 * }
	 * @param string        $file                      Full path to the file.
	 * @param string        $filename                  The name of the file (may differ from $file due to
	 *                                                 $file being in a tmp directory).
	 * @param string[]|null $mimes                     Array of mime types keyed by their file extension regex, or null if
	 *                                                 none were provided.
	 * @param string|false  $real_mime                 The actual mime type or false if the type cannot be determined.
	 */
	return apply_filters( 'wp_check_filetype_and_ext', compact( 'ext', 'type', 'proper_filename' ), $file, $filename, $mimes, $real_mime );
}

/**
 * Returns the real mime type of an image file.
 *
 * This depends on exif_imagetype() or getimagesize() to determine real mime types.
 *
 * @since 4.7.1
 * @since 5.8.0 Added support for WebP images.
 * @since 6.5.0 Added support for AVIF images.
 * @since 6.7.0 Added support for HEIC images.
 *
 * @param string $file Full path to the file.
 * @return string|false The actual mime type or false if the type cannot be determined.
 */
function wp_get_image_mime( $file ) {
	/*
	 * Use exif_imagetype() to check the mimetype if available or fall back to
	 * getimagesize() if exif isn't available. If either function throws an Exception
	 * we assume the file could not be validated.
	 */
	try {
		if ( is_callable( 'exif_imagetype' ) ) {
			$imagetype = exif_imagetype( $file );
			$mime      = ( $imagetype ) ? image_type_to_mime_type( $imagetype ) : false;
		} elseif ( function_exists( 'getimagesize' ) ) {
			// Don't silence errors when in debug mode, unless running unit tests.
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG && ! defined( 'WP_RUN_CORE_TESTS' ) ) {
				// Not using wp_getimagesize() here to avoid an infinite loop.
				$imagesize = getimagesize( $file );
			} else {
				$imagesize = @getimagesize( $file );
			}

			$mime = ( isset( $imagesize['mime'] ) ) ? $imagesize['mime'] : false;
		} else {
			$mime = false;
		}

		if ( false !== $mime ) {
			return $mime;
		}

		$magic = file_get_contents( $file, false, null, 0, 12 );

		if ( false === $magic ) {
			return false;
		}

		/*
		 * Add WebP fallback detection when image library doesn't support WebP.
		 * Note: detection values come from LibWebP, see
		 * https://github.com/webmproject/libwebp/blob/master/imageio/image_dec.c#L30
		 */
		$magic = bin2hex( $magic );
		if (
			// RIFF.
			( str_starts_with( $magic, '52494646' ) ) &&
			// WEBP.
			( 16 === strpos( $magic, '57454250' ) )
		) {
			$mime = 'image/webp';
		}

		/**
		 * Add AVIF fallback detection when image library doesn't support AVIF.
		 *
		 * Detection based on section 4.3.1 File-type box definition of the ISO/IEC 14496-12
		 * specification and the AV1-AVIF spec, see https://aomediacodec.github.io/av1-avif/v1.1.0.html#brands.
		 */

		// Divide the header string into 4 byte groups.
		$magic = str_split( $magic, 8 );

		if ( isset( $magic[1] ) && isset( $magic[2] ) && 'ftyp' === hex2bin( $magic[1] ) ) {
			if ( 'avif' === hex2bin( $magic[2] ) || 'avis' === hex2bin( $magic[2] ) ) {
				$mime = 'image/avif';
			} elseif ( 'heic' === hex2bin( $magic[2] ) ) {
				$mime = 'image/heic';
			} elseif ( 'heif' === hex2bin( $magic[2] ) ) {
				$mime = 'image/heif';
			} else {
				/*
				 * HEIC/HEIF images and image sequences/animations may have other strings here
				 * like mif1, msf1, etc. For now fall back to using finfo_file() to detect these.
				 */
				if ( extension_loaded( 'fileinfo' ) ) {
					$fileinfo  = finfo_open( FILEINFO_MIME_TYPE );
					$mime_type = finfo_file( $fileinfo, $file );
					finfo_close( $fileinfo );

					if ( wp_is_heic_image_mime_type( $mime_type ) ) {
						$mime = $mime_type;
					}
				}
			}
		}
	} catch ( Exception $e ) {
		$mime = false;
	}

	return $mime;
}

/**
 * Retrieves the list of mime types and file extensions.
 *
 * @since 3.5.0
 * @since 4.2.0 Support was added for GIMP (.xcf) files.
 * @since 4.9.2 Support was added for Flac (.flac) files.
 * @since 4.9.6 Support was added for AAC (.aac) files.
 * @since 6.8.0 Support was added for `audio/x-wav`.
 *
 * @return string[] Array of mime types keyed by the file extension regex corresponding to those types.
 */
function wp_get_mime_types() {
	/**
	 * Filters the list of mime types and file extensions.
	 *
	 * This filter should be used to add, not remove, mime types. To remove
	 * mime types, use the {@see 'upload_mimes'} filter.
	 *
	 * @since 3.5.0
	 *
	 * @param string[] $wp_get_mime_types Mime types keyed by the file extension regex
	 *                                    corresponding to those types.
	 */
	return apply_filters(
		'mime_types',
		array(
			// Image formats.
			'jpg|jpeg|jpe'                 => 'image/jpeg',
			'gif'                          => 'image/gif',
			'png'                          => 'image/png',
			'bmp'                          => 'image/bmp',
			'tiff|tif'                     => 'image/tiff',
			'webp'                         => 'image/webp',
			'avif'                         => 'image/avif',
			'ico'                          => 'image/x-icon',

			// TODO: Needs improvement. All images with the following mime types seem to have .heic file extension.
			'heic'                         => 'image/heic',
			'heif'                         => 'image/heif',
			'heics'                        => 'image/heic-sequence',
			'heifs'                        => 'image/heif-sequence',

			// Video formats.
			'asf|asx'                      => 'video/x-ms-asf',
			'wmv'                          => 'video/x-ms-wmv',
			'wmx'                          => 'video/x-ms-wmx',
			'wm'                           => 'video/x-ms-wm',
			'avi'                          => 'video/avi',
			'divx'                         => 'video/divx',
			'flv'                          => 'video/x-flv',
			'mov|qt'                       => 'video/quicktime',
			'mpeg|mpg|mpe'                 => 'video/mpeg',
			'mp4|m4v'                      => 'video/mp4',
			'ogv'                          => 'video/ogg',
			'webm'                         => 'video/webm',
			'mkv'                          => 'video/x-matroska',
			'3gp|3gpp'                     => 'video/3gpp',  // Can also be audio.
			'3g2|3gp2'                     => 'video/3gpp2', // Can also be audio.
			// Text formats.
			'txt|asc|c|cc|h|srt'           => 'text/plain',
			'csv'                          => 'text/csv',
			'tsv'                          => 'text/tab-separated-values',
			'ics'                          => 'text/calendar',
			'rtx'                          => 'text/richtext',
			'css'                          => 'text/css',
			'htm|html'                     => 'text/html',
			'vtt'                          => 'text/vtt',
			'dfxp'                         => 'application/ttaf+xml',
			// Audio formats.
			'mp3|m4a|m4b'                  => 'audio/mpeg',
			'aac'                          => 'audio/aac',
			'ra|ram'                       => 'audio/x-realaudio',
			'wav|x-wav'                    => 'audio/wav',
			'ogg|oga'                      => 'audio/ogg',
			'flac'                         => 'audio/flac',
			'mid|midi'                     => 'audio/midi',
			'wma'                          => 'audio/x-ms-wma',
			'wax'                          => 'audio/x-ms-wax',
			'mka'                          => 'audio/x-matroska',
			// Misc application formats.
			'rtf'                          => 'application/rtf',
			'js'                           => 'application/javascript',
			'pdf'                          => 'application/pdf',
			'swf'                          => 'application/x-shockwave-flash',
			'class'                        => 'application/java',
			'tar'                          => 'application/x-tar',
			'zip'                          => 'application/zip',
			'gz|gzip'                      => 'application/x-gzip',
			'rar'                          => 'application/rar',
			'7z'                           => 'application/x-7z-compressed',
			'exe'                          => 'application/x-msdownload',
			'psd'                          => 'application/octet-stream',
			'xcf'                          => 'application/octet-stream',
			// MS Office formats.
			'doc'                          => 'application/msword',
			'pot|pps|ppt'                  => 'application/vnd.ms-powerpoint',
			'wri'                          => 'application/vnd.ms-write',
			'xla|xls|xlt|xlw'              => 'application/vnd.ms-excel',
			'mdb'                          => 'application/vnd.ms-access',
			'mpp'                          => 'application/vnd.ms-project',
			'docx'                         => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
			'docm'                         => 'application/vnd.ms-word.document.macroEnabled.12',
			'dotx'                         => 'application/vnd.openxmlformats-officedocument.wordprocessingml.template',
			'dotm'                         => 'application/vnd.ms-word.template.macroEnabled.12',
			'xlsx'                         => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
			'xlsm'                         => 'application/vnd.ms-excel.sheet.macroEnabled.12',
			'xlsb'                         => 'application/vnd.ms-excel.sheet.binary.macroEnabled.12',
			'xltx'                         => 'application/vnd.openxmlformats-officedocument.spreadsheetml.template',
			'xltm'                         => 'application/vnd.ms-excel.template.macroEnabled.12',
			'xlam'                         => 'application/vnd.ms-excel.addin.macroEnabled.12',
			'pptx'                         => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
			'pptm'                         => 'application/vnd.ms-powerpoint.presentation.macroEnabled.12',
			'ppsx'                         => 'application/vnd.openxmlformats-officedocument.presentationml.slideshow',
			'ppsm'                         => 'application/vnd.ms-powerpoint.slideshow.macroEnabled.12',
			'potx'                         => 'application/vnd.openxmlformats-officedocument.presentationml.template',
			'potm'                         => 'application/vnd.ms-powerpoint.template.macroEnabled.12',
			'ppam'                         => 'application/vnd.ms-powerpoint.addin.macroEnabled.12',
			'sldx'                         => 'application/vnd.openxmlformats-officedocument.presentationml.slide',
			'sldm'                         => 'application/vnd.ms-powerpoint.slide.macroEnabled.12',
			'onetoc|onetoc2|onetmp|onepkg' => 'application/onenote',
			'oxps'                         => 'application/oxps',
			'xps'                          => 'application/vnd.ms-xpsdocument',
			// OpenOffice formats.
			'odt'                          => 'application/vnd.oasis.opendocument.text',
			'odp'                          => 'application/vnd.oasis.opendocument.presentation',
			'ods'                          => 'application/vnd.oasis.opendocument.spreadsheet',
			'odg'                          => 'application/vnd.oasis.opendocument.graphics',
			'odc'                          => 'application/vnd.oasis.opendocument.chart',
			'odb'                          => 'application/vnd.oasis.opendocument.database',
			'odf'                          => 'application/vnd.oasis.opendocument.formula',
			// WordPerfect formats.
			'wp|wpd'                       => 'application/wordperfect',
			// iWork formats.
			'key'                          => 'application/vnd.apple.keynote',
			'numbers'                      => 'application/vnd.apple.numbers',
			'pages'                        => 'application/vnd.apple.pages',
		)
	);
}

/**
 * Retrieves the list of common file extensions and their types.
 *
 * @since 4.6.0
 *
 * @return array[] Multi-dimensional array of file extensions types keyed by the type of file.
 */
function wp_get_ext_types() {

	/**
	 * Filters file type based on the extension name.
	 *
	 * @since 2.5.0
	 *
	 * @see wp_ext2type()
	 *
	 * @param array[] $ext2type Multi-dimensional array of file extensions types keyed by the type of file.
	 */
	return apply_filters(
		'ext2type',
		array(
			'image'       => array( 'jpg', 'jpeg', 'jpe', 'gif', 'png', 'bmp', 'tif', 'tiff', 'ico', 'heic', 'heif', 'webp', 'avif' ),
			'audio'       => array( 'aac', 'ac3', 'aif', 'aiff', 'flac', 'm3a', 'm4a', 'm4b', 'mka', 'mp1', 'mp2', 'mp3', 'ogg', 'oga', 'ram', 'wav', 'wma' ),
			'video'       => array( '3g2', '3gp', '3gpp', 'asf', 'avi', 'divx', 'dv', 'flv', 'm4v', 'mkv', 'mov', 'mp4', 'mpeg', 'mpg', 'mpv', 'ogm', 'ogv', 'qt', 'rm', 'vob', 'wmv' ),
			'document'    => array( 'doc', 'docx', 'docm', 'dotm', 'odt', 'pages', 'pdf', 'xps', 'oxps', 'rtf', 'wp', 'wpd', 'psd', 'xcf' ),
			'spreadsheet' => array( 'numbers', 'ods', 'xls', 'xlsx', 'xlsm', 'xlsb' ),
			'interactive' => array( 'swf', 'key', 'ppt', 'pptx', 'pptm', 'pps', 'ppsx', 'ppsm', 'sldx', 'sldm', 'odp' ),
			'text'        => array( 'asc', 'csv', 'tsv', 'txt' ),
			'archive'     => array( 'bz2', 'cab', 'dmg', 'gz', 'rar', 'sea', 'sit', 'sqx', 'tar', 'tgz', 'zip', '7z' ),
			'code'        => array( 'css', 'htm', 'html', 'php', 'js' ),
		)
	);
}

/**
 * Wrapper for PHP filesize with filters and casting the result as an integer.
 *
 * @since 6.0.0
 *
 * @link https://www.php.net/manual/en/function.filesize.php
 *
 * @param string $path Path to the file.
 * @return int The size of the file in bytes, or 0 in the event of an error.
 */
function wp_filesize( $path ) {
	/**
	 * Filters the result of wp_filesize before the PHP function is run.
	 *
	 * @since 6.0.0
	 *
	 * @param null|int $size The unfiltered value. Returning an int from the callback bypasses the filesize call.
	 * @param string   $path Path to the file.
	 */
	$size = apply_filters( 'pre_wp_filesize', null, $path );

	if ( is_int( $size ) ) {
		return $size;
	}

	$size = file_exists( $path ) ? (int) filesize( $path ) : 0;

	/**
	 * Filters the size of the file.
	 *
	 * @since 6.0.0
	 *
	 * @param int    $size The result of PHP filesize on the file.
	 * @param string $path Path to the file.
	 */
	return (int) apply_filters( 'wp_filesize', $size, $path );
}

/**
 * Retrieves the list of allowed mime types and file extensions.
 *
 * @since 2.8.6
 *
 * @param int|WP_User $user Optional. User to check. Defaults to current user.
 * @return string[] Array of mime types keyed by the file extension regex corresponding
 *                  to those types.
 */
function get_allowed_mime_types( $user = null ) {
	$t = wp_get_mime_types();

	unset( $t['swf'], $t['exe'] );
	if ( function_exists( 'current_user_can' ) ) {
		$unfiltered = $user ? user_can( $user, 'unfiltered_html' ) : current_user_can( 'unfiltered_html' );
	}

	if ( empty( $unfiltered ) ) {
		unset( $t['htm|html'], $t['js'] );
	}

	/**
	 * Filters the list of allowed mime types and file extensions.
	 *
	 * @since 2.0.0
	 *
	 * @param array            $t    Mime types keyed by the file extension regex corresponding to those types.
	 * @param int|WP_User|null $user User ID, User object or null if not provided (indicates current user).
	 */
	return apply_filters( 'upload_mimes', $t, $user );
}

/**
 * Displays "Are You Sure" message to confirm the action being taken.
 *
 * If the action has the nonce explain message, then it will be displayed
 * along with the "Are you sure?" message.
 *
 * @since 2.0.4
 *
 * @param string $action The nonce action.
 */
function wp_nonce_ays( $action ) {
	// Default title and response code.
	$title         = __( 'An error occurred.' );
	$response_code = 403;

	if ( 'log-out' === $action ) {
		$title = sprintf(
			/* translators: %s: Site title. */
			__( 'You are attempting to log out of %s' ),
			get_bloginfo( 'name' )
		);

		$redirect_to = isset( $_REQUEST['redirect_to'] ) ? $_REQUEST['redirect_to'] : '';

		$html  = $title;
		$html .= '</p><p>';
		$html .= sprintf(
			/* translators: %s: Logout URL. */
			__( 'Do you really want to <a href="%s">log out</a>?' ),
			wp_logout_url( $redirect_to )
		);
	} else {
		$html = __( 'The link you followed has expired.' );

		if ( wp_get_referer() ) {
			$wp_http_referer = remove_query_arg( 'updated', wp_get_referer() );
			$wp_http_referer = wp_validate_redirect( sanitize_url( $wp_http_referer ) );

			$html .= '</p><p>';
			$html .= sprintf(
				'<a href="%s">%s</a>',
				esc_url( $wp_http_referer ),
				__( 'Please try again.' )
			);
		}
	}

	wp_die( $html, $title, $response_code );
}

/**
 * Kills WordPress execution and displays HTML page with an error message.
 *
 * This function complements the `die()` PHP function. The difference is that
 * HTML will be displayed to the user. It is recommended to use this function
 * only when the execution should not continue any further. It is not recommended
 * to call this function very often, and try to handle as many errors as possible
 * silently or more gracefully.
 *
 * As a shorthand, the desired HTTP response code may be passed as an integer to
 * the `$title` parameter (the default title would apply) or the `$args` parameter.
 *
 * @since 2.0.4
 * @since 4.1.0 The `$title` and `$args` parameters were changed to optionally accept
 *              an integer to be used as the response code.
 * @since 5.1.0 The `$link_url`, `$link_text`, and `$exit` arguments were added.
 * @since 5.3.0 The `$charset` argument was added.
 * @since 5.5.0 The `$text_direction` argument has a priority over get_language_attributes()
 *              in the default handler.
 *
 * @global WP_Query $wp_query WordPress Query object.
 *
 * @param string|WP_Error  $message Optional. Error message. If this is a WP_Error object,
 *                                  and not an Ajax or XML-RPC request, the error's messages are used.
 *                                  Default empty string.
 * @param string|int       $title   Optional. Error title. If `$message` is a `WP_Error` object,
 *                                  error data with the key 'title' may be used to specify the title.
 *                                  If `$title` is an integer, then it is treated as the response code.
 *                                  Default empty string.
 * @param string|array|int $args {
 *     Optional. Arguments to control behavior. If `$args` is an integer, then it is treated
 *     as the response code. Default empty array.
 *
 *     @type int    $response       The HTTP response code. Default 200 for Ajax requests, 500 otherwise.
 *     @type string $link_url       A URL to include a link to. Only works in combination with $link_text.
 *                                  Default empty string.
 *     @type string $link_text      A label for the link to include. Only works in combination with $link_url.
 *                                  Default empty string.
 *     @type bool   $back_link      Whether to include a link to go back. Default false.
 *     @type string $text_direction The text direction. This is only useful internally, when WordPress is still
 *                                  loading and the site's locale is not set up yet. Accepts 'rtl' and 'ltr'.
 *                                  Default is the value of is_rtl().
 *     @type string $charset        Character set of the HTML output. Default 'utf-8'.
 *     @type string $code           Error code to use. Default is 'wp_die', or the main error code if $message
 *                                  is a WP_Error.
 *     @type bool   $exit           Whether to exit the process after completion. Default true.
 * }
 */
function wp_die( $message = '', $title = '', $args = array() ) {
	global $wp_query;

	if ( is_int( $args ) ) {
		$args = array( 'response' => $args );
	} elseif ( is_int( $title ) ) {
		$args  = array( 'response' => $title );
		$title = '';
	}

	if ( wp_doing_ajax() ) {
		/**
		 * Filters the callback for killing WordPress execution for Ajax requests.
		 *
		 * @since 3.4.0
		 *
		 * @param callable $callback Callback function name.
		 */
		$callback = apply_filters( 'wp_die_ajax_handler', '_ajax_wp_die_handler' );
	} elseif ( wp_is_json_request() ) {
		/**
		 * Filters the callback for killing WordPress execution for JSON requests.
		 *
		 * @since 5.1.0
		 *
		 * @param callable $callback Callback function name.
		 */
		$callback = apply_filters( 'wp_die_json_handler', '_json_wp_die_handler' );
	} elseif ( wp_is_serving_rest_request() && wp_is_jsonp_request() ) {
		/**
		 * Filters the callback for killing WordPress execution for JSONP REST requests.
		 *
		 * @since 5.2.0
		 *
		 * @param callable $callback Callback function name.
		 */
		$callback = apply_filters( 'wp_die_jsonp_handler', '_jsonp_wp_die_handler' );
	} elseif ( defined( 'XMLRPC_REQUEST' ) && XMLRPC_REQUEST ) {
		/**
		 * Filters the callback for killing WordPress execution for XML-RPC requests.
		 *
		 * @since 3.4.0
		 *
		 * @param callable $callback Callback function name.
		 */
		$callback = apply_filters( 'wp_die_xmlrpc_handler', '_xmlrpc_wp_die_handler' );
	} elseif ( wp_is_xml_request()
		|| isset( $wp_query ) &&
			( function_exists( 'is_feed' ) && is_feed()
			|| function_exists( 'is_comment_feed' ) && is_comment_feed()
			|| function_exists( 'is_trackback' ) && is_trackback() ) ) {
		/**
		 * Filters the callback for killing WordPress execution for XML requests.
		 *
		 * @since 5.2.0
		 *
		 * @param callable $callback Callback function name.
		 */
		$callback = apply_filters( 'wp_die_xml_handler', '_xml_wp_die_handler' );
	} else {
		/**
		 * Filters the callback for killing WordPress execution for all non-Ajax, non-JSON, non-XML requests.
		 *
		 * @since 3.0.0
		 *
		 * @param callable $callback Callback function name.
		 */
		$callback = apply_filters( 'wp_die_handler', '_default_wp_die_handler' );
	}

	call_user_func( $callback, $message, $title, $args );
}

/**
 * Kills WordPress execution and displays HTML page with an error message.
 *
 * This is the default handler for wp_die(). If you want a custom one,
 * you can override this using the {@see 'wp_die_handler'} filter in wp_die().
 *
 * @since 3.0.0
 * @access private
 *
 * @param string|WP_Error $message Error message or WP_Error object.
 * @param string          $title   Optional. Error title. Default empty string.
 * @param string|array    $args    Optional. Arguments to control behavior. Default empty array.
 */
function _default_wp_die_handler( $message, $title = '', $args = array() ) {
	list( $message, $title, $parsed_args ) = _wp_die_process_input( $message, $title, $args );

	if ( is_string( $message ) ) {
		if ( ! empty( $parsed_args['additional_errors'] ) ) {
			$message = array_merge(
				array( $message ),
				wp_list_pluck( $parsed_args['additional_errors'], 'message' )
			);
			$message = "<ul>\n\t\t<li>" . implode( "</li>\n\t\t<li>", $message ) . "</li>\n\t</ul>";
		}

		$message = sprintf(
			'<div class="wp-die-message">%s</div>',
			$message
		);
	}

	$have_gettext = function_exists( '__' );

	if ( ! empty( $parsed_args['link_url'] ) && ! empty( $parsed_args['link_text'] ) ) {
		$link_url = $parsed_args['link_url'];
		if ( function_exists( 'esc_url' ) ) {
			$link_url = esc_url( $link_url );
		}
		$link_text = $parsed_args['link_text'];
		$message  .= "\n<p><a href='{$link_url}'>{$link_text}</a></p>";
	}

	if ( isset( $parsed_args['back_link'] ) && $parsed_args['back_link'] ) {
		$back_text = $have_gettext ? __( '&laquo; Back' ) : '&laquo; Back';
		$message  .= "\n<p><a href='javascript:history.back()'>$back_text</a></p>";
	}

	if ( ! did_action( 'admin_head' ) ) :
		if ( ! headers_sent() ) {
			header( "Content-Type: text/html; charset={$parsed_args['charset']}" );
			status_header( $parsed_args['response'] );
			nocache_headers();
		}

		$text_direction = $parsed_args['text_direction'];
		$dir_attr       = "dir='$text_direction'";

		/*
		 * If `text_direction` was not explicitly passed,
		 * use get_language_attributes() if available.
		 */
		if ( empty( $args['text_direction'] )
			&& function_exists( 'language_attributes' ) && function_exists( 'is_rtl' )
		) {
			$dir_attr = get_language_attributes();
		}
		?>
<!DOCTYPE html>
<html <?php echo $dir_attr; ?>>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $parsed_args['charset']; ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<?php
		if ( function_exists( 'wp_robots' ) && function_exists( 'wp_robots_no_robots' ) && function_exists( 'add_filter' ) ) {
			add_filter( 'wp_robots', 'wp_robots_no_robots' );
			// Prevent warnings because of $wp_query not existing.
			remove_filter( 'wp_robots', 'wp_robots_noindex_embeds' );
			remove_filter( 'wp_robots', 'wp_robots_noindex_search' );
			wp_robots();
		}
		?>
	<title><?php echo $title; ?></title>
	<style type="text/css">
		html {
			background: #f1f1f1;
		}
		body {
			background: #fff;
			border: 1px solid #ccd0d4;
			color: #444;
			font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
			margin: 2em auto;
			padding: 1em 2em;
			max-width: 700px;
			-webkit-box-shadow: 0 1px 1px rgba(0, 0, 0, .04);
			box-shadow: 0 1px 1px rgba(0, 0, 0, .04);
		}
		h1 {
			border-bottom: 1px solid #dadada;
			clear: both;
			color: #666;
			font-size: 24px;
			margin: 30px 0 0 0;
			padding: 0;
			padding-bottom: 7px;
		}
		#error-page {
			margin-top: 50px;
		}
		#error-page p,
		#error-page .wp-die-message {
			font-size: 14px;
			line-height: 1.5;
			margin: 25px 0 20px;
		}
		#error-page code {
			font-family: Consolas, Monaco, monospace;
		}
		ul li {
			margin-bottom: 10px;
			font-size: 14px ;
		}
		a {
			color: #2271b1;
		}
		a:hover,
		a:active {
			color: #135e96;
		}
		a:focus {
			color: #043959;
			box-shadow: 0 0 0 2px #2271b1;
			outline: 2px solid transparent;
		}
		.button {
			background: #f3f5f6;
			border: 1px solid #016087;
			color: #016087;
			display: inline-block;
			text-decoration: none;
			font-size: 13px;
			line-height: 2;
			height: 28px;
			margin: 0;
			padding: 0 10px 1px;
			cursor: pointer;
			-webkit-border-radius: 3px;
			-webkit-appearance: none;
			border-radius: 3px;
			white-space: nowrap;
			-webkit-box-sizing: border-box;
			-moz-box-sizing:    border-box;
			box-sizing:         border-box;

			vertical-align: top;
		}

		.button.button-large {
			line-height: 2.30769231;
			min-height: 32px;
			padding: 0 12px;
		}

		.button:hover,
		.button:focus {
			background: #f1f1f1;
		}

		.button:focus {
			background: #f3f5f6;
			border-color: #007cba;
			-webkit-box-shadow: 0 0 0 1px #007cba;
			box-shadow: 0 0 0 1px #007cba;
			color: #016087;
			outline: 2px solid transparent;
			outline-offset: 0;
		}

		.button:active {
			background: #f3f5f6;
			border-color: #7e8993;
			-webkit-box-shadow: none;
			box-shadow: none;
		}

		<?php
		if ( 'rtl' === $text_direction ) {
			echo 'body { font-family: Tahoma, Arial; }';
		}
		?>
	</style>
</head>
<body id="error-page">
<?php endif; // ! did_action( 'admin_head' ) ?>
	<?php echo $message; ?>
</body>
</html>
	<?php
	if ( $parsed_args['exit'] ) {
		die();
	}
}

/**
 * Kills WordPress execution and displays Ajax response with an error message.
 *
 * This is the handler for wp_die() when processing Ajax requests.
 *
 * @since 3.4.0
 * @access private
 *
 * @param string       $message Error message.
 * @param string       $title   Optional. Error title (unused). Default empty string.
 * @param string|array $args    Optional. Arguments to control behavior. Default empty array.
 */
function _ajax_wp_die_handler( $message, $title = '', $args = array() ) {
	// Set default 'response' to 200 for Ajax requests.
	$args = wp_parse_args(
		$args,
		array( 'response' => 200 )
	);

	list( $message, $title, $parsed_args ) = _wp_die_process_input( $message, $title, $args );

	if ( ! headers_sent() ) {
		// This is intentional. For backward-compatibility, support passing null here.
		if ( null !== $args['response'] ) {
			status_header( $parsed_args['response'] );
		}
		nocache_headers();
	}

	if ( is_scalar( $message ) ) {
		$message = (string) $message;
	} else {
		$message = '0';
	}

	if ( $parsed_args['exit'] ) {
		die( $message );
	}

	echo $message;
}

/**
 * Kills WordPress execution and displays JSON response with an error message.
 *
 * This is the handler for wp_die() when processing JSON requests.
 *
 * @since 5.1.0
 * @access private
 *
 * @param string       $message Error message.
 * @param string       $title   Optional. Error title. Default empty string.
 * @param string|array $args    Optional. Arguments to control behavior. Default empty array.
 */
function _json_wp_die_handler( $message, $title = '', $args = array() ) {
	list( $message, $title, $parsed_args ) = _wp_die_process_input( $message, $title, $args );

	$data = array(
		'code'              => $parsed_args['code'],
		'message'           => $message,
		'data'              => array(
			'status' => $parsed_args['response'],
		),
		'additional_errors' => $parsed_args['additional_errors'],
	);

	if ( isset( $parsed_args['error_data'] ) ) {
		$data['data']['error'] = $parsed_args['error_data'];
	}

	if ( ! headers_sent() ) {
		header( "Content-Type: application/json; charset={$parsed_args['charset']}" );
		if ( null !== $parsed_args['response'] ) {
			status_header( $parsed_args['response'] );
		}
		nocache_headers();
	}

	echo wp_json_encode( $data );
	if ( $parsed_args['exit'] ) {
		die();
	}
}

/**
 * Kills WordPress execution and displays JSONP response with an error message.
 *
 * This is the handler for wp_die() when processing JSONP requests.
 *
 * @since 5.2.0
 * @access private
 *
 * @param string       $message Error message.
 * @param string       $title   Optional. Error title. Default empty string.
 * @param string|array $args    Optional. Arguments to control behavior. Default empty array.
 */
function _jsonp_wp_die_handler( $message, $title = '', $args = array() ) {
	list( $message, $title, $parsed_args ) = _wp_die_process_input( $message, $title, $args );

	$data = array(
		'code'              => $parsed_args['code'],
		'message'           => $message,
		'data'              => array(
			'status' => $parsed_args['response'],
		),
		'additional_errors' => $parsed_args['additional_errors'],
	);

	if ( isset( $parsed_args['error_data'] ) ) {
		$data['data']['error'] = $parsed_args['error_data'];
	}

	if ( ! headers_sent() ) {
		header( "Content-Type: application/javascript; charset={$parsed_args['charset']}" );
		header( 'X-Content-Type-Options: nosniff' );
		header( 'X-Robots-Tag: noindex' );
		if ( null !== $parsed_args['response'] ) {
			status_header( $parsed_args['response'] );
		}
		nocache_headers();
	}

	$result         = wp_json_encode( $data );
	$jsonp_callback = $_GET['_jsonp'];
	echo '/**/' . $jsonp_callback . '(' . $result . ')';
	if ( $parsed_args['exit'] ) {
		die();
	}
}

/**
 * Kills WordPress execution and displays XML response with an error message.
 *
 * This is the handler for wp_die() when processing XMLRPC requests.
 *
 * @since 3.2.0
 * @access private
 *
 * @global wp_xmlrpc_server $wp_xmlrpc_server
 *
 * @param string       $message Error message.
 * @param string       $title   Optional. Error title. Default empty string.
 * @param string|array $args    Optional. Arguments to control behavior. Default empty array.
 */
function _xmlrpc_wp_die_handler( $message, $title = '', $args = array() ) {
	global $wp_xmlrpc_server;

	list( $message, $title, $parsed_args ) = _wp_die_process_input( $message, $title, $args );

	if ( ! headers_sent() ) {
		nocache_headers();
	}

	if ( $wp_xmlrpc_server ) {
		$error = new IXR_Error( $parsed_args['response'], $message );
		$wp_xmlrpc_server->output( $error->getXml() );
	}
	if ( $parsed_args['exit'] ) {
		die();
	}
}

/**
 * Kills WordPress execution and displays XML response with an error message.
 *
 * This is the handler for wp_die() when processing XML requests.
 *
 * @since 5.2.0
 * @access private
 *
 * @param string       $message Error message.
 * @param string       $title   Optional. Error title. Default empty string.
 * @param string|array $args    Optional. Arguments to control behavior. Default empty array.
 */
function _xml_wp_die_handler( $message, $title = '', $args = array() ) {
	list( $message, $title, $parsed_args ) = _wp_die_process_input( $message, $title, $args );

	$message = htmlspecialchars( $message );
	$title   = htmlspecialchars( $title );

	$xml = <<<EOD
<error>
    <code>{$parsed_args['code']}</code>
    <title><![CDATA[{$title}]]></title>
    <message><![CDATA[{$message}]]></message>
    <data>
        <status>{$parsed_args['response']}</status>
    </data>
</error>

EOD;

	if ( ! headers_sent() ) {
		header( "Content-Type: text/xml; charset={$parsed_args['charset']}" );
		if ( null !== $parsed_args['response'] ) {
			status_header( $parsed_args['response'] );
		}
		nocache_headers();
	}

	echo $xml;
	if ( $parsed_args['exit'] ) {
		die();
	}
}

/**
 * Kills WordPress execution and displays an error message.
 *
 * This is the handler for wp_die() when processing APP requests.
 *
 * @since 3.4.0
 * @since 5.1.0 Added the $title and $args parameters.
 * @access private
 *
 * @param string       $message Optional. Response to print. Default empty string.
 * @param string       $title   Optional. Error title (unused). Default empty string.
 * @param string|array $args    Optional. Arguments to control behavior. Default empty array.
 */
function _scalar_wp_die_handler( $message = '', $title = '', $args = array() ) {
	list( $message, $title, $parsed_args ) = _wp_die_process_input( $message, $title, $args );

	if ( $parsed_args['exit'] ) {
		if ( is_scalar( $message ) ) {
			die( (string) $message );
		}
		die();
	}

	if ( is_scalar( $message ) ) {
		echo (string) $message;
	}
}

/**
 * Processes arguments passed to wp_die() consistently for its handlers.
 *
 * @since 5.1.0
 * @access private
 *
 * @param string|WP_Error $message Error message or WP_Error object.
 * @param string          $title   Optional. Error title. Default empty string.
 * @param string|array    $args    Optional. Arguments to control behavior. Default empty array.
 * @return array {
 *     Processed arguments.
 *
 *     @type string $0 Error message.
 *     @type string $1 Error title.
 *     @type array  $2 Arguments to control behavior.
 * }
 */
function _wp_die_process_input( $message, $title = '', $args = array() ) {
	$defaults = array(
		'response'          => 0,
		'code'              => '',
		'exit'              => true,
		'back_link'         => false,
		'link_url'          => '',
		'link_text'         => '',
		'text_direction'    => '',
		'charset'           => 'utf-8',
		'additional_errors' => array(),
	);

	$args = wp_parse_args( $args, $defaults );

	if ( function_exists( 'is_wp_error' ) && is_wp_error( $message ) ) {
		if ( ! empty( $message->errors ) ) {
			$errors = array();
			foreach ( (array) $message->errors as $error_code => $error_messages ) {
				foreach ( (array) $error_messages as $error_message ) {
					$errors[] = array(
						'code'    => $error_code,
						'message' => $error_message,
						'data'    => $message->get_error_data( $error_code ),
					);
				}
			}

			$message = $errors[0]['message'];
			if ( empty( $args['code'] ) ) {
				$args['code'] = $errors[0]['code'];
			}
			if ( empty( $args['response'] ) && is_array( $errors[0]['data'] ) && ! empty( $errors[0]['data']['status'] ) ) {
				$args['response'] = $errors[0]['data']['status'];
			}
			if ( empty( $title ) && is_array( $errors[0]['data'] ) && ! empty( $errors[0]['data']['title'] ) ) {
				$title = $errors[0]['data']['title'];
			}
			if ( WP_DEBUG_DISPLAY && is_array( $errors[0]['data'] ) && ! empty( $errors[0]['data']['error'] ) ) {
				$args['error_data'] = $errors[0]['data']['error'];
			}

			unset( $errors[0] );
			$args['additional_errors'] = array_values( $errors );
		} else {
			$message = '';
		}
	}

	$have_gettext = function_exists( '__' );

	// The $title and these specific $args must always have a non-empty value.
	if ( empty( $args['code'] ) ) {
		$args['code'] = 'wp_die';
	}
	if ( empty( $args['response'] ) ) {
		$args['response'] = 500;
	}
	if ( empty( $title ) ) {
		$title = $have_gettext ? __( 'WordPress &rsaquo; Error' ) : 'WordPress &rsaquo; Error';
	}
	if ( empty( $args['text_direction'] ) || ! in_array( $args['text_direction'], array( 'ltr', 'rtl' ), true ) ) {
		$args['text_direction'] = 'ltr';
		if ( function_exists( 'is_rtl' ) && is_rtl() ) {
			$args['text_direction'] = 'rtl';
		}
	}

	if ( ! empty( $args['charset'] ) ) {
		$args['charset'] = _canonical_charset( $args['charset'] );
	}

	return array( $message, $title, $args );
}

/**
 * Encodes a variable into JSON, with some confidence checks.
 *
 * @since 4.1.0
 * @since 5.3.0 No longer handles support for PHP < 5.6.
 * @since 6.5.0 The `$data` parameter has been renamed to `$value` and
 *              the `$options` parameter to `$flags` for parity with PHP.
 *
 * @param mixed $value Variable (usually an array or object) to encode as JSON.
 * @param int   $flags Optional. Options to be passed to json_encode(). Default 0.
 * @param int   $depth Optional. Maximum depth to walk through $value. Must be
 *                     greater than 0. Default 512.
 * @return string|false The JSON encoded string, or false if it cannot be encoded.
 */
function wp_json_encode( $value, $flags = 0, $depth = 512 ) {
	$json = json_encode( $value, $flags, $depth );

	// If json_encode() was successful, no need to do more confidence checking.
	if ( false !== $json ) {
		return $json;
	}

	try {
		$value = _wp_json_sanity_check( $value, $depth );
	} catch ( Exception $e ) {
		return false;
	}

	return json_encode( $value, $flags, $depth );
}

/**
 * Performs confidence checks on data that shall be encoded to JSON.
 *
 * @ignore
 * @since 4.1.0
 * @access private
 *
 * @see wp_json_encode()
 *
 * @throws Exception If depth limit is reached.
 *
 * @param mixed $value Variable (usually an array or object) to encode as JSON.
 * @param int   $depth Maximum depth to walk through $value. Must be greater than 0.
 * @return mixed The sanitized data that shall be encoded to JSON.
 */
function _wp_json_sanity_check( $value, $depth ) {
	if ( $depth < 0 ) {
		throw new Exception( 'Reached depth limit' );
	}

	if ( is_array( $value ) ) {
		$output = array();
		foreach ( $value as $id => $el ) {
			// Don't forget to sanitize the ID!
			if ( is_string( $id ) ) {
				$clean_id = _wp_json_convert_string( $id );
			} else {
				$clean_id = $id;
			}

			// Check the element type, so that we're only recursing if we really have to.
			if ( is_array( $el ) || is_object( $el ) ) {
				$output[ $clean_id ] = _wp_json_sanity_check( $el, $depth - 1 );
			} elseif ( is_string( $el ) ) {
				$output[ $clean_id ] = _wp_json_convert_string( $el );
			} else {
				$output[ $clean_id ] = $el;
			}
		}
	} elseif ( is_object( $value ) ) {
		$output = new stdClass();
		foreach ( $value as $id => $el ) {
			if ( is_string( $id ) ) {
				$clean_id = _wp_json_convert_string( $id );
			} else {
				$clean_id = $id;
			}

			if ( is_array( $el ) || is_object( $el ) ) {
				$output->$clean_id = _wp_json_sanity_check( $el, $depth - 1 );
			} elseif ( is_string( $el ) ) {
				$output->$clean_id = _wp_json_convert_string( $el );
			} else {
				$output->$clean_id = $el;
			}
		}
	} elseif ( is_string( $value ) ) {
		return _wp_json_convert_string( $value );
	} else {
		return $value;
	}

	return $output;
}

/**
 * Converts a string to UTF-8, so that it can be safely encoded to JSON.
 *
 * @ignore
 * @since 4.1.0
 * @access private
 *
 * @see _wp_json_sanity_check()
 *
 * @param string $input_string The string which is to be converted.
 * @return string The checked string.
 */
function _wp_json_convert_string( $input_string ) {
	static $use_mb = null;
	if ( is_null( $use_mb ) ) {
		$use_mb = function_exists( 'mb_convert_encoding' );
	}

	if ( $use_mb ) {
		$encoding = mb_detect_encoding( $input_string, mb_detect_order(), true );
		if ( $encoding ) {
			return mb_convert_encoding( $input_string, 'UTF-8', $encoding );
		} else {
			return mb_convert_encoding( $input_string, 'UTF-8', 'UTF-8' );
		}
	} else {
		return wp_check_invalid_utf8( $input_string, true );
	}
}

/**
 * Prepares response data to be serialized to JSON.
 *
 * This supports the JsonSerializable interface for PHP 5.2-5.3 as well.
 *
 * @ignore
 * @since 4.4.0
 * @deprecated 5.3.0 This function is no longer needed as support for PHP 5.2-5.3
 *                   has been dropped.
 * @access private
 *
 * @param mixed $value Native representation.
 * @return bool|int|float|null|string|array Data ready for `json_encode()`.
 */
function _wp_json_prepare_data( $value ) {
	_deprecated_function( __FUNCTION__, '5.3.0' );
	return $value;
}

/**
 * Sends a JSON response back to an Ajax request.
 *
 * @since 3.5.0
 * @since 4.7.0 The `$status_code` parameter was added.
 * @since 5.6.0 The `$flags` parameter was added.
 *
 * @param mixed $response    Variable (usually an array or object) to encode as JSON,
 *                           then print and die.
 * @param int   $status_code Optional. The HTTP status code to output. Default null.
 * @param int   $flags       Optional. Options to be passed to json_encode(). Default 0.
 */
function wp_send_json( $response, $status_code = null, $flags = 0 ) {
	if ( wp_is_serving_rest_request() ) {
		_doing_it_wrong(
			__FUNCTION__,
			sprintf(
				/* translators: 1: WP_REST_Response, 2: WP_Error */
				__( 'Return a %1$s or %2$s object from your callback when using the REST API.' ),
				'WP_REST_Response',
				'WP_Error'
			),
			'5.5.0'
		);
	}

	if ( ! headers_sent() ) {
		header( 'Content-Type: application/json; charset=' . get_option( 'blog_charset' ) );
		if ( null !== $status_code ) {
			status_header( $status_code );
		}
	}

	echo wp_json_encode( $response, $flags );

	if ( wp_doing_ajax() ) {
		wp_die(
			'',
			'',
			array(
				'response' => null,
			)
		);
	} else {
		die;
	}
}

/**
 * Sends a JSON response back to an Ajax request, indicating success.
 *
 * @since 3.5.0
 * @since 4.7.0 The `$status_code` parameter was added.
 * @since 5.6.0 The `$flags` parameter was added.
 *
 * @param mixed $value       Optional. Data to encode as JSON, then print and die. Default null.
 * @param int   $status_code Optional. The HTTP status code to output. Default null.
 * @param int   $flags       Optional. Options to be passed to json_encode(). Default 0.
 */
function wp_send_json_success( $value = null, $status_code = null, $flags = 0 ) {
	$response = array( 'success' => true );

	if ( isset( $value ) ) {
		$response['data'] = $value;
	}

	wp_send_json( $response, $status_code, $flags );
}

/**
 * Sends a JSON response back to an Ajax request, indicating failure.
 *
 * If the `$value` parameter is a WP_Error object, the errors
 * within the object are processed and output as an array of error
 * codes and corresponding messages. All other types are output
 * without further processing.
 *
 * @since 3.5.0
 * @since 4.1.0 The `$value` parameter is now processed if a WP_Error object is passed in.
 * @since 4.7.0 The `$status_code` parameter was added.
 * @since 5.6.0 The `$flags` parameter was added.
 *
 * @param mixed $value       Optional. Data to encode as JSON, then print and die. Default null.
 * @param int   $status_code Optional. The HTTP status code to output. Default null.
 * @param int   $flags       Optional. Options to be passed to json_encode(). Default 0.
 */
function wp_send_json_error( $value = null, $status_code = null, $flags = 0 ) {
	$response = array( 'success' => false );

	if ( isset( $value ) ) {
		if ( is_wp_error( $value ) ) {
			$result = array();
			foreach ( $value->errors as $code => $messages ) {
				foreach ( $messages as $message ) {
					$result[] = array(
						'code'    => $code,
						'message' => $message,
					);
				}
			}

			$response['data'] = $result;
		} else {
			$response['data'] = $value;
		}
	}

	wp_send_json( $response, $status_code, $flags );
}

/**
 * Checks that a JSONP callback is a valid JavaScript callback name.
 *
 * Only allows alphanumeric characters and the dot character in callback
 * function names. This helps to mitigate XSS attacks caused by directly
 * outputting user input.
 *
 * @since 4.6.0
 *
 * @param string $callback Supplied JSONP callback function name.
 * @return bool Whether the callback function name is valid.
 */
function wp_check_jsonp_callback( $callback ) {
	if ( ! is_string( $callback ) ) {
		return false;
	}

	preg_replace( '/[^\w\.]/', '', $callback, -1, $illegal_char_count );

	return 0 === $illegal_char_count;
}

/**
 * Reads and decodes a JSON file.
 *
 * @since 5.9.0
 *
 * @param string $filename Path to the JSON file.
 * @param array  $options  {
 *     Optional. Options to be used with `json_decode()`.
 *
 *     @type bool $associative Optional. When `true`, JSON objects will be returned as associative arrays.
 *                             When `false`, JSON objects will be returned as objects. Default false.
 * }
 *
 * @return mixed Returns the value encoded in JSON in appropriate PHP type.
 *               `null` is returned if the file is not found, or its content can't be decoded.
 */
function wp_json_file_decode( $filename, $options = array() ) {
	$result   = null;
	$filename = wp_normalize_path( realpath( $filename ) );

	if ( ! $filename ) {
		wp_trigger_error(
			__FUNCTION__,
			sprintf(
				/* translators: %s: Path to the JSON file. */
				__( "File %s doesn't exist!" ),
				$filename
			)
		);
		return $result;
	}

	$options      = wp_parse_args( $options, array( 'associative' => false ) );
	$decoded_file = json_decode( file_get_contents( $filename ), $options['associative'] );

	if ( JSON_ERROR_NONE !== json_last_error() ) {
		wp_trigger_error(
			__FUNCTION__,
			sprintf(
				/* translators: 1: Path to the JSON file, 2: Error message. */
				__( 'Error when decoding a JSON file at path %1$s: %2$s' ),
				$filename,
				json_last_error_msg()
			)
		);
		return $result;
	}

	return $decoded_file;
}

/**
 * Retrieves the WordPress home page URL.
 *
 * If the constant named 'WP_HOME' exists, then it will be used and returned
 * by the function. This can be used to counter the redirection on your local
 * development environment.
 *
 * @since 2.2.0
 * @access private
 *
 * @see WP_HOME
 *
 * @param string $url URL for the home location.
 * @return string Homepage location.
 */
function _config_wp_home( $url = '' ) {
	if ( defined( 'WP_HOME' ) ) {
		return untrailingslashit( WP_HOME );
	}
	return $url;
}

/**
 * Retrieves the WordPress site URL.
 *
 * If the constant named 'WP_SITEURL' is defined, then the value in that
 * constant will always be returned. This can be used for debugging a site
 * on your localhost while not having to change the database to your URL.
 *
 * @since 2.2.0
 * @access private
 *
 * @see WP_SITEURL
 *
 * @param string $url URL to set the WordPress site location.
 * @return string The WordPress site URL.
 */
function _config_wp_siteurl( $url = '' ) {
	if ( defined( 'WP_SITEURL' ) ) {
		return untrailingslashit( WP_SITEURL );
	}
	return $url;
}

/**
 * Deletes the fresh site option.
 *
 * @since 4.7.0
 * @access private
 */
function _delete_option_fresh_site() {
	update_option( 'fresh_site', '0', false );
}

/**
 * Sets the localized direction for MCE plugin.
 *
 * Will only set the direction to 'rtl', if the WordPress locale has
 * the text direction set to 'rtl'.
 *
 * Fills in the 'directionality' setting, enables the 'directionality'
 * plugin, and adds the 'ltr' button to 'toolbar1', formerly
 * 'theme_advanced_buttons1' array keys. These keys are then returned
 * in the $mce_init (TinyMCE settings) array.
 *
 * @since 2.1.0
 * @access private
 *
 * @param array $mce_init MCE settings array.
 * @return array Direction set for 'rtl', if needed by locale.
 */
function _mce_set_direction( $mce_init ) {
	if ( is_rtl() ) {
		$mce_init['directionality'] = 'rtl';
		$mce_init['rtl_ui']         = true;

		if ( ! empty( $mce_init['plugins'] ) && ! str_contains( $mce_init['plugins'], 'directionality' ) ) {
			$mce_init['plugins'] .= ',directionality';
		}

		if ( ! empty( $mce_init['toolbar1'] ) && ! preg_match( '/\bltr\b/', $mce_init['toolbar1'] ) ) {
			$mce_init['toolbar1'] .= ',ltr';
		}
	}

	return $mce_init;
}

/**
 * Determines whether WordPress is currently serving a REST API request.
 *
 * The function relies on the 'REST_REQUEST' global. As such, it only returns true when an actual REST _request_ is
 * being made. It does not return true when a REST endpoint is hit as part of another request, e.g. for preloading a
 * REST response. See {@see wp_is_rest_endpoint()} for that purpose.
 *
 * This function should not be called until the {@see 'parse_request'} action, as the constant is only defined then,
 * even for an actual REST request.
 *
 * @since 6.5.0
 *
 * @return bool True if it's a WordPress REST API request, false otherwise.
 */
function wp_is_serving_rest_request() {
	return defined( 'REST_REQUEST' ) && REST_REQUEST;
}

/**
 * Converts smiley code to the icon graphic file equivalent.
 *
 * You can turn off smilies, by going to the write setting screen and unchecking
 * the box, or by setting 'use_smilies' option to false or removing the option.
 *
 * Plugins may override the default smiley list by setting the $wpsmiliestrans
 * to an array, with the key the code the blogger types in and the value the
 * image file.
 *
 * The $wp_smiliessearch global is for the regular expression and is set each
 * time the function is called.
 *
 * The full list of smilies can be found in the function and won't be listed in
 * the description. Probably should create a Codex page for it, so that it is
 * available.
 *
 * @since 2.2.0
 *
 * @global array $wpsmiliestrans
 * @global array $wp_smiliessearch
 */
function smilies_init() {
	global $wpsmiliestrans, $wp_smiliessearch;

	// Don't bother setting up smilies if they are disabled.
	if ( ! get_option( 'use_smilies' ) ) {
		return;
	}

	if ( ! isset( $wpsmiliestrans ) ) {
		$wpsmiliestrans = array(
			':mrgreen:' => 'mrgreen.png',
			':neutral:' => "\xf0\x9f\x98\x90",
			':twisted:' => "\xf0\x9f\x98\x88",
			':arrow:'   => "\xe2\x9e\xa1",
			':shock:'   => "\xf0\x9f\x98\xaf",
			':smile:'   => "\xf0\x9f\x99\x82",
			':???:'     => "\xf0\x9f\x98\x95",
			':cool:'    => "\xf0\x9f\x98\x8e",
			':evil:'    => "\xf0\x9f\x91\xbf",
			':grin:'    => "\xf0\x9f\x98\x80",
			':idea:'    => "\xf0\x9f\x92\xa1",
			':oops:'    => "\xf0\x9f\x98\xb3",
			':razz:'    => "\xf0\x9f\x98\x9b",
			':roll:'    => "\xf0\x9f\x99\x84",
			':wink:'    => "\xf0\x9f\x98\x89",
			':cry:'     => "\xf0\x9f\x98\xa5",
			':eek:'     => "\xf0\x9f\x98\xae",
			':lol:'     => "\xf0\x9f\x98\x86",
			':mad:'     => "\xf0\x9f\x98\xa1",
			':sad:'     => "\xf0\x9f\x99\x81",
			'8-)'       => "\xf0\x9f\x98\x8e",
			'8-O'       => "\xf0\x9f\x98\xaf",
			':-('       => "\xf0\x9f\x99\x81",
			':-)'       => "\xf0\x9f\x99\x82",
			':-?'       => "\xf0\x9f\x98\x95",
			':-D'       => "\xf0\x9f\x98\x80",
			':-P'       => "\xf0\x9f\x98\x9b",
			':-o'       => "\xf0\x9f\x98\xae",
			':-x'       => "\xf0\x9f\x98\xa1",
			':-|'       => "\xf0\x9f\x98\x90",
			';-)'       => "\xf0\x9f\x98\x89",
			// This one transformation breaks regular text with frequency.
			//     '8)' => "\xf0\x9f\x98\x8e",
			'8O'        => "\xf0\x9f\x98\xaf",
			':('        => "\xf0\x9f\x99\x81",
			':)'        => "\xf0\x9f\x99\x82",
			':?'        => "\xf0\x9f\x98\x95",
			':D'        => "\xf0\x9f\x98\x80",
			':P'        => "\xf0\x9f\x98\x9b",
			':o'        => "\xf0\x9f\x98\xae",
			':x'        => "\xf0\x9f\x98\xa1",
			':|'        => "\xf0\x9f\x98\x90",
			';)'        => "\xf0\x9f\x98\x89",
			':!:'       => "\xe2\x9d\x97",
			':?:'       => "\xe2\x9d\x93",
		);
	}

	/**
	 * Filters all the smilies.
	 *
	 * This filter must be added before `smilies_init` is run, as
	 * it is normally only run once to setup the smilies regex.
	 *
	 * @since 4.7.0
	 *
	 * @param string[] $wpsmiliestrans List of the smilies' hexadecimal representations, keyed by their smily code.
	 */
	$wpsmiliestrans = apply_filters( 'smilies', $wpsmiliestrans );

	if ( count( $wpsmiliestrans ) === 0 ) {
		return;
	}

	/*
	 * NOTE: we sort the smilies in reverse key order. This is to make sure
	 * we match the longest possible smilie (:???: vs :?) as the regular
	 * expression used below is first-match
	 */
	krsort( $wpsmiliestrans );

	$spaces = wp_spaces_regexp();

	// Begin first "subpattern".
	$wp_smiliessearch = '/(?<=' . $spaces . '|^)';

	$subchar = '';
	foreach ( (array) $wpsmiliestrans as $smiley => $img ) {
		$firstchar = substr( $smiley, 0, 1 );
		$rest      = substr( $smiley, 1 );

		// New subpattern?
		if ( $firstchar !== $subchar ) {
			if ( '' !== $subchar ) {
				$wp_smiliessearch .= ')(?=' . $spaces . '|$)';  // End previous "subpattern".
				$wp_smiliessearch .= '|(?<=' . $spaces . '|^)'; // Begin another "subpattern".
			}

			$subchar           = $firstchar;
			$wp_smiliessearch .= preg_quote( $firstchar, '/' ) . '(?:';
		} else {
			$wp_smiliessearch .= '|';
		}

		$wp_smiliessearch .= preg_quote( $rest, '/' );
	}

	$wp_smiliessearch .= ')(?=' . $spaces . '|$)/m';
}

/**
 * Merges user defined arguments into defaults array.
 *
 * This function is used throughout WordPress to allow for both string or array
 * to be merged into another array.
 *
 * @since 2.2.0
 * @since 2.3.0 `$args` can now also be an object.
 *
 * @param string|array|object $args     Value to merge with $defaults.
 * @param array               $defaults Optional. Array that serves as the defaults.
 *                                      Default empty array.
 * @return array Merged user defined values with defaults.
 */
function wp_parse_args( $args, $defaults = array() ) {
	if ( is_object( $args ) ) {
		$parsed_args = get_object_vars( $args );
	} elseif ( is_array( $args ) ) {
		$parsed_args =& $args;
	} else {
		wp_parse_str( $args, $parsed_args );
	}

	if ( is_array( $defaults ) && $defaults ) {
		return array_merge( $defaults, $parsed_args );
	}
	return $parsed_args;
}

/**
 * Converts a comma- or space-separated list of scalar values to an array.
 *
 * @since 5.1.0
 *
 * @param array|string $input_list List of values.
 * @return array Array of values.
 */
function wp_parse_list( $input_list ) {
	if ( ! is_array( $input_list ) ) {
		return preg_split( '/[\s,]+/', $input_list, -1, PREG_SPLIT_NO_EMPTY );
	}

	// Validate all entries of the list are scalar.
	$input_list = array_filter( $input_list, 'is_scalar' );

	return $input_list;
}

/**
 * Cleans up an array, comma- or space-separated list of IDs.
 *
 * @since 3.0.0
 * @since 5.1.0 Refactored to use wp_parse_list().
 *
 * @param array|string $input_list List of IDs.
 * @return int[] Sanitized array of IDs.
 */
function wp_parse_id_list( $input_list ) {
	$input_list = wp_parse_list( $input_list );

	return array_unique( array_map( 'absint', $input_list ) );
}

/**
 * Cleans up an array, comma- or space-separated list of slugs.
 *
 * @since 4.7.0
 * @since 5.1.0 Refactored to use wp_parse_list().
 *
 * @param array|string $input_list List of slugs.
 * @return string[] Sanitized array of slugs.
 */
function wp_parse_slug_list( $input_list ) {
	$input_list = wp_parse_list( $input_list );

	return array_unique( array_map( 'sanitize_title', $input_list ) );
}

/**
 * Extracts a slice of an array, given a list of keys.
 *
 * @since 3.1.0
 *
 * @param array $input_array The original array.
 * @param array $keys        The list of keys.
 * @return array The array slice.
 */
function wp_array_slice_assoc( $input_array, $keys ) {
	$slice = array();

	foreach ( $keys as $key ) {
		if ( isset( $input_array[ $key ] ) ) {
			$slice[ $key ] = $input_array[ $key ];
		}
	}

	return $slice;
}

/**
 * Sorts the keys of an array alphabetically.
 *
 * The array is passed by reference so it doesn't get returned
 * which mimics the behavior of `ksort()`.
 *
 * @since 6.0.0
 *
 * @param array $input_array The array to sort, passed by reference.
 */
function wp_recursive_ksort( &$input_array ) {
	foreach ( $input_array as &$value ) {
		if ( is_array( $value ) ) {
			wp_recursive_ksort( $value );
		}
	}

	ksort( $input_array );
}

/**
 * Accesses an array in depth based on a path of keys.
 *
 * It is the PHP equivalent of JavaScript's `lodash.get()` and mirroring it may help other components
 * retain some symmetry between client and server implementations.
 *
 * Example usage:
 *
 *     $input_array = array(
 *         'a' => array(
 *             'b' => array(
 *                 'c' => 1,
 *             ),
 *         ),
 *     );
 *     _wp_array_get( $input_array, array( 'a', 'b', 'c' ) );
 *
 * @internal
 *
 * @since 5.6.0
 * @access private
 *
 * @param array $input_array   An array from which we want to retrieve some information.
 * @param array $path          An array of keys describing the path with which to retrieve information.
 * @param mixed $default_value Optional. The return value if the path does not exist within the array,
 *                             or if `$input_array` or `$path` are not arrays. Default null.
 * @return mixed The value from the path specified.
 */
function _wp_array_get( $input_array, $path, $default_value = null ) {
	// Confirm $path is valid.
	if ( ! is_array( $path ) || 0 === count( $path ) ) {
		return $default_value;
	}

	foreach ( $path as $path_element ) {
		if ( ! is_array( $input_array ) ) {
			return $default_value;
		}

		if ( is_string( $path_element )
			|| is_integer( $path_element )
			|| null === $path_element
		) {
			/*
			 * Check if the path element exists in the input array.
			 * We check with `isset()` first, as it is a lot faster
			 * than `array_key_exists()`.
			 */
			if ( isset( $input_array[ $path_element ] ) ) {
				$input_array = $input_array[ $path_element ];
				continue;
			}

			/*
			 * If `isset()` returns false, we check with `array_key_exists()`,
			 * which also checks for `null` values.
			 */
			if ( array_key_exists( $path_element, $input_array ) ) {
				$input_array = $input_array[ $path_element ];
				continue;
			}
		}

		return $default_value;
	}

	return $input_array;
}

/**
 * Sets an array in depth based on a path of keys.
 *
 * It is the PHP equivalent of JavaScript's `lodash.set()` and mirroring it may help other components
 * retain some symmetry between client and server implementations.
 *
 * Example usage:
 *
 *     $input_array = array();
 *     _wp_array_set( $input_array, array( 'a', 'b', 'c', 1 ) );
 *
 *     $input_array becomes:
 *     array(
 *         'a' => array(
 *             'b' => array(
 *                 'c' => 1,
 *             ),
 *         ),
 *     );
 *
 * @internal
 *
 * @since 5.8.0
 * @access private
 *
 * @param array $input_array An array that we want to mutate to include a specific value in a path.
 * @param array $path        An array of keys describing the path that we want to mutate.
 * @param mixed $value       The value that will be set.
 */
function _wp_array_set( &$input_array, $path, $value = null ) {
	// Confirm $input_array is valid.
	if ( ! is_array( $input_array ) ) {
		return;
	}

	// Confirm $path is valid.
	if ( ! is_array( $path ) ) {
		return;
	}

	$path_length = count( $path );

	if ( 0 === $path_length ) {
		return;
	}

	foreach ( $path as $path_element ) {
		if (
			! is_string( $path_element ) && ! is_integer( $path_element ) &&
			! is_null( $path_element )
		) {
			return;
		}
	}

	for ( $i = 0; $i < $path_length - 1; ++$i ) {
		$path_element = $path[ $i ];
		if (
			! array_key_exists( $path_element, $input_array ) ||
			! is_array( $input_array[ $path_element ] )
		) {
			$input_array[ $path_element ] = array();
		}
		$input_array = &$input_array[ $path_element ];
	}

	$input_array[ $path[ $i ] ] = $value;
}

/**
 * This function is trying to replicate what
 * lodash's kebabCase (JS library) does in the client.
 *
 * The reason we need this function is that we do some processing
 * in both the client and the server (e.g.: we generate
 * preset classes from preset slugs) that needs to
 * create the same output.
 *
 * We can't remove or update the client's library due to backward compatibility
 * (some of the output of lodash's kebabCase is saved in the post content).
 * We have to make the server behave like the client.
 *
 * Changes to this function should follow updates in the client
 * with the same logic.
 *
 * @link https://github.com/lodash/lodash/blob/4.17/dist/lodash.js#L14369
 * @link https://github.com/lodash/lodash/blob/4.17/dist/lodash.js#L278
 * @link https://github.com/lodash-php/lodash-php/blob/master/src/String/kebabCase.php
 * @link https://github.com/lodash-php/lodash-php/blob/master/src/internal/unicodeWords.php
 *
 * @param string $input_string The string to kebab-case.
 *
 * @return string kebab-cased-string.
 */
function _wp_to_kebab_case( $input_string ) {
	// Ignore the camelCase names for variables so the names are the same as lodash so comparing and porting new changes is easier.
	// phpcs:disable WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase

	/*
	 * Some notable things we've removed compared to the lodash version are:
	 *
	 * - non-alphanumeric characters: rsAstralRange, rsEmoji, etc
	 * - the groups that processed the apostrophe, as it's removed before passing the string to preg_match: rsApos, rsOptContrLower, and rsOptContrUpper
	 *
	 */

	/** Used to compose unicode character classes. */
	$rsLowerRange       = 'a-z\\xdf-\\xf6\\xf8-\\xff';
	$rsNonCharRange     = '\\x00-\\x2f\\x3a-\\x40\\x5b-\\x60\\x7b-\\xbf';
	$rsPunctuationRange = '\\x{2000}-\\x{206f}';
	$rsSpaceRange       = ' \\t\\x0b\\f\\xa0\\x{feff}\\n\\r\\x{2028}\\x{2029}\\x{1680}\\x{180e}\\x{2000}\\x{2001}\\x{2002}\\x{2003}\\x{2004}\\x{2005}\\x{2006}\\x{2007}\\x{2008}\\x{2009}\\x{200a}\\x{202f}\\x{205f}\\x{3000}';
	$rsUpperRange       = 'A-Z\\xc0-\\xd6\\xd8-\\xde';
	$rsBreakRange       = $rsNonCharRange . $rsPunctuationRange . $rsSpaceRange;

	/** Used to compose unicode capture groups. */
	$rsBreak  = '[' . $rsBreakRange . ']';
	$rsDigits = '\\d+'; // The last lodash version in GitHub uses a single digit here and expands it when in use.
	$rsLower  = '[' . $rsLowerRange . ']';
	$rsMisc   = '[^' . $rsBreakRange . $rsDigits . $rsLowerRange . $rsUpperRange . ']';
	$rsUpper  = '[' . $rsUpperRange . ']';

	/** Used to compose unicode regexes. */
	$rsMiscLower = '(?:' . $rsLower . '|' . $rsMisc . ')';
	$rsMiscUpper = '(?:' . $rsUpper . '|' . $rsMisc . ')';
	$rsOrdLower  = '\\d*(?:1st|2nd|3rd|(?![123])\\dth)(?=\\b|[A-Z_])';
	$rsOrdUpper  = '\\d*(?:1ST|2ND|3RD|(?![123])\\dTH)(?=\\b|[a-z_])';

	$regexp = '/' . implode(
		'|',
		array(
			$rsUpper . '?' . $rsLower . '+' . '(?=' . implode( '|', array( $rsBreak, $rsUpper, '$' ) ) . ')',
			$rsMiscUpper . '+' . '(?=' . implode( '|', array( $rsBreak, $rsUpper . $rsMiscLower, '$' ) ) . ')',
			$rsUpper . '?' . $rsMiscLower . '+',
			$rsUpper . '+',
			$rsOrdUpper,
			$rsOrdLower,
			$rsDigits,
		)
	) . '/u';

	preg_match_all( $regexp, str_replace( "'", '', $input_string ), $matches );
	return strtolower( implode( '-', $matches[0] ) );
	// phpcs:enable WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
}

/**
 * Determines if the variable is a numeric-indexed array.
 *
 * @since 4.4.0
 *
 * @param mixed $data Variable to check.
 * @return bool Whether the variable is a list.
 */
function wp_is_numeric_array( $data ) {
	if ( ! is_array( $data ) ) {
		return false;
	}

	$keys        = array_keys( $data );
	$string_keys = array_filter( $keys, 'is_string' );

	return count( $string_keys ) === 0;
}

/**
 * Filters a list of objects, based on a set of key => value arguments.
 *
 * Retrieves the objects from the list that match the given arguments.
 * Key represents property name, and value represents property value.
 *
 * If an object has more properties than those specified in arguments,
 * that will not disqualify it. When using the 'AND' operator,
 * any missing properties will disqualify it.
 *
 * When using the `$field` argument, this function can also retrieve
 * a particular field from all matching objects, whereas wp_list_filter()
 * only does the filtering.
 *
 * @since 3.0.0
 * @since 4.7.0 Uses `WP_List_Util` class.
 *
 * @param array       $input_list An array of objects to filter.
 * @param array       $args       Optional. An array of key => value arguments to match
 *                                against each object. Default empty array.
 * @param string      $operator   Optional. The logical operation to perform. 'AND' means
 *                                all elements from the array must match. 'OR' means only
 *                                one element needs to match. 'NOT' means no elements may
 *                                match. Default 'AND'.
 * @param bool|string $field      Optional. A field from the object to place instead
 *                                of the entire object. Default false.
 * @return array A list of objects or object fields.
 */
function wp_filter_object_list( $input_list, $args = array(), $operator = 'and', $field = false ) {
	if ( ! is_array( $input_list ) ) {
		return array();
	}

	$util = new WP_List_Util( $input_list );

	$util->filter( $args, $operator );

	if ( $field ) {
		$util->pluck( $field );
	}

	return $util->get_output();
}

/**
 * Filters a list of objects, based on a set of key => value arguments.
 *
 * Retrieves the objects from the list that match the given arguments.
 * Key represents property name, and value represents property value.
 *
 * If an object has more properties than those specified in arguments,
 * that will not disqualify it. When using the 'AND' operator,
 * any missing properties will disqualify it.
 *
 * If you want to retrieve a particular field from all matching objects,
 * use wp_filter_object_list() instead.
 *
 * @since 3.1.0
 * @since 4.7.0 Uses `WP_List_Util` class.
 * @since 5.9.0 Converted into a wrapper for `wp_filter_object_list()`.
 *
 * @param array  $input_list An array of objects to filter.
 * @param array  $args       Optional. An array of key => value arguments to match
 *                           against each object. Default empty array.
 * @param string $operator   Optional. The logical operation to perform. 'AND' means
 *                           all elements from the array must match. 'OR' means only
 *                           one element needs to match. 'NOT' means no elements may
 *                           match. Default 'AND'.
 * @return array Array of found values.
 */
function wp_list_filter( $input_list, $args = array(), $operator = 'AND' ) {
	return wp_filter_object_list( $input_list, $args, $operator );
}

/**
 * Plucks a certain field out of each object or array in an array.
 *
 * This has the same functionality and prototype of
 * array_column() (PHP 5.5) but also supports objects.
 *
 * @since 3.1.0
 * @since 4.0.0 $index_key parameter added.
 * @since 4.7.0 Uses `WP_List_Util` class.
 *
 * @param array      $input_list List of objects or arrays.
 * @param int|string $field      Field from the object to place instead of the entire object.
 * @param int|string $index_key  Optional. Field from the object to use as keys for the new array.
 *                               Default null.
 * @return array Array of found values. If `$index_key` is set, an array of found values with keys
 *               corresponding to `$index_key`. If `$index_key` is null, array keys from the original
 *               `$input_list` will be preserved in the results.
 */
function wp_list_pluck( $input_list, $field, $index_key = null ) {
	if ( ! is_array( $input_list ) ) {
		return array();
	}

	$util = new WP_List_Util( $input_list );

	return $util->pluck( $field, $index_key );
}

/**
 * Sorts an array of objects or arrays based on one or more orderby arguments.
 *
 * @since 4.7.0
 *
 * @param array        $input_list    An array of objects or arrays to sort.
 * @param string|array $orderby       Optional. Either the field name to order by or an array
 *                                    of multiple orderby fields as `$orderby => $order`.
 *                                    Default empty array.
 * @param string       $order         Optional. Either 'ASC' or 'DESC'. Only used if `$orderby`
 *                                    is a string. Default 'ASC'.
 * @param bool         $preserve_keys Optional. Whether to preserve keys. Default false.
 * @return array The sorted array.
 */
function wp_list_sort( $input_list, $orderby = array(), $order = 'ASC', $preserve_keys = false ) {
	if ( ! is_array( $input_list ) ) {
		return array();
	}

	$util = new WP_List_Util( $input_list );

	return $util->sort( $orderby, $order, $preserve_keys );
}

/**
 * Determines if Widgets library should be loaded.
 *
 * Checks to make sure that the widgets library hasn't already been loaded.
 * If it hasn't, then it will load the widgets library and run an action hook.
 *
 * @since 2.2.0
 */
function wp_maybe_load_widgets() {
	/**
	 * Filters whether to load the Widgets library.
	 *
	 * Returning a falsey value from the filter will effectively short-circuit
	 * the Widgets library from loading.
	 *
	 * @since 2.8.0
	 *
	 * @param bool $wp_maybe_load_widgets Whether to load the Widgets library.
	 *                                    Default true.
	 */
	if ( ! apply_filters( 'load_default_widgets', true ) ) {
		return;
	}

	require_once ABSPATH . WPINC . '/default-widgets.php';

	add_action( '_admin_menu', 'wp_widgets_add_menu' );
}

/**
 * Appends the Widgets menu to the themes main menu.
 *
 * @since 2.2.0
 * @since 5.9.3 Don't specify menu order when the active theme is a block theme.
 *
 * @global array $submenu
 */
function wp_widgets_add_menu() {
	global $submenu;

	if ( ! current_theme_supports( 'widgets' ) ) {
		return;
	}

	$menu_name = __( 'Widgets' );
	if ( wp_is_block_theme() ) {
		$submenu['themes.php'][] = array( $menu_name, 'edit_theme_options', 'widgets.php' );
	} else {
		$submenu['themes.php'][8] = array( $menu_name, 'edit_theme_options', 'widgets.php' );
	}

	ksort( $submenu['themes.php'], SORT_NUMERIC );
}

/**
 * Flushes all output buffers for PHP 5.2.
 *
 * Make sure all output buffers are flushed before our singletons are destroyed.
 *
 * @since 2.2.0
 */
function wp_ob_end_flush_all() {
	$levels = ob_get_level();
	for ( $i = 0; $i < $levels; $i++ ) {
		ob_end_flush();
	}
}

/**
 * Loads custom DB error or display WordPress DB error.
 *
 * If a file exists in the wp-content directory named db-error.php, then it will
 * be loaded instead of displaying the WordPress DB error. If it is not found,
 * then the WordPress DB error will be displayed instead.
 *
 * The WordPress DB error sets the HTTP status header to 500 to try to prevent
 * search engines from caching the message. Custom DB messages should do the
 * same.
 *
 * This function was backported to WordPress 2.3.2, but originally was added
 * in WordPress 2.5.0.
 *
 * @since 2.3.2
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 */
function dead_db() {
	global $wpdb;

	wp_load_translations_early();

	// Load custom DB error template, if present.
	if ( file_exists( WP_CONTENT_DIR . '/db-error.php' ) ) {
		require_once WP_CONTENT_DIR . '/db-error.php';
		die();
	}

	// If installing or in the admin, provide the verbose message.
	if ( wp_installing() || defined( 'WP_ADMIN' ) ) {
		wp_die( $wpdb->error );
	}

	// Otherwise, be terse.
	wp_die( '<h1>' . __( 'Error establishing a database connection' ) . '</h1>', __( 'Database Error' ) );
}

/**
 * Marks a function as deprecated and inform when it has been used.
 *
 * There is a {@see 'deprecated_function_run'} hook that will be called that can be used
 * to get the backtrace up to what file and function called the deprecated function.
 *
 * The current behavior is to trigger a user error if `WP_DEBUG` is true.
 *
 * This function is to be used in every function that is deprecated.
 *
 * @since 2.5.0
 * @since 5.4.0 This function is no longer marked as "private".
 * @since 5.4.0 The error type is now classified as E_USER_DEPRECATED (used to default to E_USER_NOTICE).
 *
 * @param string $function_name The function that was called.
 * @param string $version       The version of WordPress that deprecated the function.
 * @param string $replacement   Optional. The function that should have been called. Default empty string.
 */
function _deprecated_function( $function_name, $version, $replacement = '' ) {

	/**
	 * Fires when a deprecated function is called.
	 *
	 * @since 2.5.0
	 *
	 * @param string $function_name The function that was called.
	 * @param string $replacement   The function that should have been called.
	 * @param string $version       The version of WordPress that deprecated the function.
	 */
	do_action( 'deprecated_function_run', $function_name, $replacement, $version );

	/**
	 * Filters whether to trigger an error for deprecated functions.
	 *
	 * @since 2.5.0
	 *
	 * @param bool $trigger Whether to trigger the error for deprecated functions. Default true.
	 */
	if ( WP_DEBUG && apply_filters( 'deprecated_function_trigger_error', true ) ) {
		if ( function_exists( '__' ) ) {
			if ( $replacement ) {
				$message = sprintf(
					/* translators: 1: PHP function name, 2: Version number, 3: Alternative function name. */
					__( 'Function %1$s is <strong>deprecated</strong> since version %2$s! Use %3$s instead.' ),
					$function_name,
					$version,
					$replacement
				);
			} else {
				$message = sprintf(
					/* translators: 1: PHP function name, 2: Version number. */
					__( 'Function %1$s is <strong>deprecated</strong> since version %2$s with no alternative available.' ),
					$function_name,
					$version
				);
			}
		} else {
			if ( $replacement ) {
				$message = sprintf(
					'Function %1$s is <strong>deprecated</strong> since version %2$s! Use %3$s instead.',
					$function_name,
					$version,
					$replacement
				);
			} else {
				$message = sprintf(
					'Function %1$s is <strong>deprecated</strong> since version %2$s with no alternative available.',
					$function_name,
					$version
				);
			}
		}

		wp_trigger_error( '', $message, E_USER_DEPRECATED );
	}
}

/**
 * Marks a constructor as deprecated and informs when it has been used.
 *
 * Similar to _deprecated_function(), but with different strings. Used to
 * remove PHP4-style constructors.
 *
 * The current behavior is to trigger a user error if `WP_DEBUG` is true.
 *
 * This function is to be used in every PHP4-style constructor method that is deprecated.
 *
 * @since 4.3.0
 * @since 4.5.0 Added the `$parent_class` parameter.
 * @since 5.4.0 This function is no longer marked as "private".
 * @since 5.4.0 The error type is now classified as E_USER_DEPRECATED (used to default to E_USER_NOTICE).
 *
 * @param string $class_name   The class containing the deprecated constructor.
 * @param string $version      The version of WordPress that deprecated the function.
 * @param string $parent_class Optional. The parent class calling the deprecated constructor.
 *                             Default empty string.
 */
function _deprecated_constructor( $class_name, $version, $parent_class = '' ) {

	/**
	 * Fires when a deprecated constructor is called.
	 *
	 * @since 4.3.0
	 * @since 4.5.0 Added the `$parent_class` parameter.
	 *
	 * @param string $class_name   The class containing the deprecated constructor.
	 * @param string $version      The version of WordPress that deprecated the function.
	 * @param string $parent_class The parent class calling the deprecated constructor.
	 */
	do_action( 'deprecated_constructor_run', $class_name, $version, $parent_class );

	/**
	 * Filters whether to trigger an error for deprecated functions.
	 *
	 * `WP_DEBUG` must be true in addition to the filter evaluating to true.
	 *
	 * @since 4.3.0
	 *
	 * @param bool $trigger Whether to trigger the error for deprecated functions. Default true.
	 */
	if ( WP_DEBUG && apply_filters( 'deprecated_constructor_trigger_error', true ) ) {
		if ( function_exists( '__' ) ) {
			if ( $parent_class ) {
				$message = sprintf(
					/* translators: 1: PHP class name, 2: PHP parent class name, 3: Version number, 4: __construct() method. */
					__( 'The called constructor method for %1$s class in %2$s is <strong>deprecated</strong> since version %3$s! Use %4$s instead.' ),
					$class_name,
					$parent_class,
					$version,
					'<code>__construct()</code>'
				);
			} else {
				$message = sprintf(
					/* translators: 1: PHP class name, 2: Version number, 3: __construct() method. */
					__( 'The called constructor method for %1$s class is <strong>deprecated</strong> since version %2$s! Use %3$s instead.' ),
					$class_name,
					$version,
					'<code>__construct()</code>'
				);
			}
		} else {
			if ( $parent_class ) {
				$message = sprintf(
					'The called constructor method for %1$s class in %2$s is <strong>deprecated</strong> since version %3$s! Use %4$s instead.',
					$class_name,
					$parent_class,
					$version,
					'<code>__construct()</code>'
				);
			} else {
				$message = sprintf(
					'The called constructor method for %1$s class is <strong>deprecated</strong> since version %2$s! Use %3$s instead.',
					$class_name,
					$version,
					'<code>__construct()</code>'
				);
			}
		}

		wp_trigger_error( '', $message, E_USER_DEPRECATED );
	}
}

/**
 * Marks a class as deprecated and informs when it has been used.
 *
 * There is a {@see 'deprecated_class_run'} hook that will be called that can be used
 * to get the backtrace up to what file and function called the deprecated class.
 *
 * The current behavior is to trigger a user error if `WP_DEBUG` is true.
 *
 * This function is to be used in the class constructor for every deprecated class.
 * See {@see _deprecated_constructor()} for deprecating PHP4-style constructors.
 *
 * @since 6.4.0
 *
 * @param string $class_name  The name of the class being instantiated.
 * @param string $version     The version of WordPress that deprecated the class.
 * @param string $replacement Optional. The class or function that should have been called.
 *                            Default empty string.
 */
function _deprecated_class( $class_name, $version, $replacement = '' ) {

	/**
	 * Fires when a deprecated class is called.
	 *
	 * @since 6.4.0
	 *
	 * @param string $class_name  The name of the class being instantiated.
	 * @param string $replacement The class or function that should have been called.
	 * @param string $version     The version of WordPress that deprecated the class.
	 */
	do_action( 'deprecated_class_run', $class_name, $replacement, $version );

	/**
	 * Filters whether to trigger an error for a deprecated class.
	 *
	 * @since 6.4.0
	 *
	 * @param bool $trigger Whether to trigger an error for a deprecated class. Default true.
	 */
	if ( WP_DEBUG && apply_filters( 'deprecated_class_trigger_error', true ) ) {
		if ( function_exists( '__' ) ) {
			if ( $replacement ) {
				$message = sprintf(
					/* translators: 1: PHP class name, 2: Version number, 3: Alternative class or function name. */
					__( 'Class %1$s is <strong>deprecated</strong> since version %2$s! Use %3$s instead.' ),
					$class_name,
					$version,
					$replacement
				);
			} else {
				$message = sprintf(
					/* translators: 1: PHP class name, 2: Version number. */
					__( 'Class %1$s is <strong>deprecated</strong> since version %2$s with no alternative available.' ),
					$class_name,
					$version
				);
			}
		} else {
			if ( $replacement ) {
				$message = sprintf(
					'Class %1$s is <strong>deprecated</strong> since version %2$s! Use %3$s instead.',
					$class_name,
					$version,
					$replacement
				);
			} else {
				$message = sprintf(
					'Class %1$s is <strong>deprecated</strong> since version %2$s with no alternative available.',
					$class_name,
					$version
				);
			}
		}

		wp_trigger_error( '', $message, E_USER_DEPRECATED );
	}
}

/**
 * Marks a file as deprecated and inform when it has been used.
 *
 * There is a {@see 'deprecated_file_included'} hook that will be called that can be used
 * to get the backtrace up to what file and function included the deprecated file.
 *
 * The current behavior is to trigger a user error if `WP_DEBUG` is true.
 *
 * This function is to be used in every file that is deprecated.
 *
 * @since 2.5.0
 * @since 5.4.0 This function is no longer marked as "private".
 * @since 5.4.0 The error type is now classified as E_USER_DEPRECATED (used to default to E_USER_NOTICE).
 *
 * @param string $file        The file that was included.
 * @param string $version     The version of WordPress that deprecated the file.
 * @param string $replacement Optional. The file that should have been included based on ABSPATH.
 *                            Default empty string.
 * @param string $message     Optional. A message regarding the change. Default empty string.
 */
function _deprecated_file( $file, $version, $replacement = '', $message = '' ) {

	/**
	 * Fires when a deprecated file is called.
	 *
	 * @since 2.5.0
	 *
	 * @param string $file        The file that was called.
	 * @param string $replacement The file that should have been included based on ABSPATH.
	 * @param string $version     The version of WordPress that deprecated the file.
	 * @param string $message     A message regarding the change.
	 */
	do_action( 'deprecated_file_included', $file, $replacement, $version, $message );

	/**
	 * Filters whether to trigger an error for deprecated files.
	 *
	 * @since 2.5.0
	 *
	 * @param bool $trigger Whether to trigger the error for deprecated files. Default true.
	 */
	if ( WP_DEBUG && apply_filters( 'deprecated_file_trigger_error', true ) ) {
		$message = empty( $message ) ? '' : ' ' . $message;

		if ( function_exists( '__' ) ) {
			if ( $replacement ) {
				$message = sprintf(
					/* translators: 1: PHP file name, 2: Version number, 3: Alternative file name. */
					__( 'File %1$s is <strong>deprecated</strong> since version %2$s! Use %3$s instead.' ),
					$file,
					$version,
					$replacement
				) . $message;
			} else {
				$message = sprintf(
					/* translators: 1: PHP file name, 2: Version number. */
					__( 'File %1$s is <strong>deprecated</strong> since version %2$s with no alternative available.' ),
					$file,
					$version
				) . $message;
			}
		} else {
			if ( $replacement ) {
				$message = sprintf(
					'File %1$s is <strong>deprecated</strong> since version %2$s! Use %3$s instead.',
					$file,
					$version,
					$replacement
				);
			} else {
				$message = sprintf(
					'File %1$s is <strong>deprecated</strong> since version %2$s with no alternative available.',
					$file,
					$version
				) . $message;
			}
		}

		wp_trigger_error( '', $message, E_USER_DEPRECATED );
	}
}
/**
 * Marks a function argument as deprecated and inform when it has been used.
 *
 * This function is to be used whenever a deprecated function argument is used.
 * Before this function is called, the argument must be checked for whether it was
 * used by comparing it to its default value or evaluating whether it is empty.
 *
 * For example:
 *
 *     if ( ! empty( $deprecated ) ) {
 *         _deprecated_argument( __FUNCTION__, '3.0.0' );
 *     }
 *
 * There is a {@see 'deprecated_argument_run'} hook that will be called that can be used
 * to get the backtrace up to what file and function used the deprecated argument.
 *
 * The current behavior is to trigger a user error if WP_DEBUG is true.
 *
 * @since 3.0.0
 * @since 5.4.0 This function is no longer marked as "private".
 * @since 5.4.0 The error type is now classified as E_USER_DEPRECATED (used to default to E_USER_NOTICE).
 *
 * @param string $function_name The function that was called.
 * @param string $version       The version of WordPress that deprecated the argument used.
 * @param string $message       Optional. A message regarding the change. Default empty string.
 */
function _deprecated_argument( $function_name, $version, $message = '' ) {

	/**
	 * Fires when a deprecated argument is called.
	 *
	 * @since 3.0.0
	 *
	 * @param string $function_name The function that was called.
	 * @param string $message       A message regarding the change.
	 * @param string $version       The version of WordPress that deprecated the argument used.
	 */
	do_action( 'deprecated_argument_run', $function_name, $message, $version );

	/**
	 * Filters whether to trigger an error for deprecated arguments.
	 *
	 * @since 3.0.0
	 *
	 * @param bool $trigger Whether to trigger the error for deprecated arguments. Default true.
	 */
	if ( WP_DEBUG && apply_filters( 'deprecated_argument_trigger_error', true ) ) {
		if ( function_exists( '__' ) ) {
			if ( $message ) {
				$message = sprintf(
					/* translators: 1: PHP function name, 2: Version number, 3: Optional message regarding the change. */
					__( 'Function %1$s was called with an argument that is <strong>deprecated</strong> since version %2$s! %3$s' ),
					$function_name,
					$version,
					$message
				);
			} else {
				$message = sprintf(
					/* translators: 1: PHP function name, 2: Version number. */
					__( 'Function %1$s was called with an argument that is <strong>deprecated</strong> since version %2$s with no alternative available.' ),
					$function_name,
					$version
				);
			}
		} else {
			if ( $message ) {
				$message = sprintf(
					'Function %1$s was called with an argument that is <strong>deprecated</strong> since version %2$s! %3$s',
					$function_name,
					$version,
					$message
				);
			} else {
				$message = sprintf(
					'Function %1$s was called with an argument that is <strong>deprecated</strong> since version %2$s with no alternative available.',
					$function_name,
					$version
				);
			}
		}

		wp_trigger_error( '', $message, E_USER_DEPRECATED );
	}
}

/**
 * Marks a deprecated action or filter hook as deprecated and throws a notice.
 *
 * Use the {@see 'deprecated_hook_run'} action to get the backtrace describing where
 * the deprecated hook was called.
 *
 * Default behavior is to trigger a user error if `WP_DEBUG` is true.
 *
 * This function is called by the do_action_deprecated() and apply_filters_deprecated()
 * functions, and so generally does not need to be called directly.
 *
 * @since 4.6.0
 * @since 5.4.0 The error type is now classified as E_USER_DEPRECATED (used to default to E_USER_NOTICE).
 * @access private
 *
 * @param string $hook        The hook that was used.
 * @param string $version     The version of WordPress that deprecated the hook.
 * @param string $replacement Optional. The hook that should have been used. Default empty string.
 * @param string $message     Optional. A message regarding the change. Default empty.
 */
function _deprecated_hook( $hook, $version, $replacement = '', $message = '' ) {
	/**
	 * Fires when a deprecated hook is called.
	 *
	 * @since 4.6.0
	 *
	 * @param string $hook        The hook that was called.
	 * @param string $replacement The hook that should be used as a replacement.
	 * @param string $version     The version of WordPress that deprecated the argument used.
	 * @param string $message     A message regarding the change.
	 */
	do_action( 'deprecated_hook_run', $hook, $replacement, $version, $message );

	/**
	 * Filters whether to trigger deprecated hook errors.
	 *
	 * @since 4.6.0
	 *
	 * @param bool $trigger Whether to trigger deprecated hook errors. Requires
	 *                      `WP_DEBUG` to be defined true.
	 */
	if ( WP_DEBUG && apply_filters( 'deprecated_hook_trigger_error', true ) ) {
		$message = empty( $message ) ? '' : ' ' . $message;

		if ( $replacement ) {
			$message = sprintf(
				/* translators: 1: WordPress hook name, 2: Version number, 3: Alternative hook name. */
				__( 'Hook %1$s is <strong>deprecated</strong> since version %2$s! Use %3$s instead.' ),
				$hook,
				$version,
				$replacement
			) . $message;
		} else {
			$message = sprintf(
				/* translators: 1: WordPress hook name, 2: Version number. */
				__( 'Hook %1$s is <strong>deprecated</strong> since version %2$s with no alternative available.' ),
				$hook,
				$version
			) . $message;
		}

		wp_trigger_error( '', $message, E_USER_DEPRECATED );
	}
}

/**
 * Marks something as being incorrectly called.
 *
 * There is a {@see 'doing_it_wrong_run'} hook that will be called that can be used
 * to get the backtrace up to what file and function called the deprecated function.
 *
 * The current behavior is to trigger a user error if `WP_DEBUG` is true.
 *
 * @since 3.1.0
 * @since 5.4.0 This function is no longer marked as "private".
 *
 * @param string $function_name The function that was called.
 * @param string $message       A message explaining what has been done incorrectly.
 * @param string $version       The version of WordPress where the message was added.
 */
function _doing_it_wrong( $function_name, $message, $version ) {

	/**
	 * Fires when the given function is being used incorrectly.
	 *
	 * @since 3.1.0
	 *
	 * @param string $function_name The function that was called.
	 * @param string $message       A message explaining what has been done incorrectly.
	 * @param string $version       The version of WordPress where the message was added.
	 */
	do_action( 'doing_it_wrong_run', $function_name, $message, $version );

	/**
	 * Filters whether to trigger an error for _doing_it_wrong() calls.
	 *
	 * @since 3.1.0
	 * @since 5.1.0 Added the $function_name, $message and $version parameters.
	 *
	 * @param bool   $trigger       Whether to trigger the error for _doing_it_wrong() calls. Default true.
	 * @param string $function_name The function that was called.
	 * @param string $message       A message explaining what has been done incorrectly.
	 * @param string $version       The version of WordPress where the message was added.
	 */
	if ( WP_DEBUG && apply_filters( 'doing_it_wrong_trigger_error', true, $function_name, $message, $version ) ) {
		if ( function_exists( '__' ) ) {
			if ( $version ) {
				/* translators: %s: Version number. */
				$version = sprintf( __( '(This message was added in version %s.)' ), $version );
			}

			$message .= ' ' . sprintf(
				/* translators: %s: Documentation URL. */
				__( 'Please see <a href="%s">Debugging in WordPress</a> for more information.' ),
				__( 'https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/' )
			);

			$message = sprintf(
				/* translators: Developer debugging message. 1: PHP function name, 2: Explanatory message, 3: WordPress version number. */
				__( 'Function %1$s was called <strong>incorrectly</strong>. %2$s %3$s' ),
				$function_name,
				$message,
				$version
			);
		} else {
			if ( $version ) {
				$version = sprintf( '(This message was added in version %s.)', $version );
			}

			$message .= sprintf(
				' Please see <a href="%s">Debugging in WordPress</a> for more information.',
				'https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/'
			);

			$message = sprintf(
				'Function %1$s was called <strong>incorrectly</strong>. %2$s %3$s',
				$function_name,
				$message,
				$version
			);
		}

		wp_trigger_error( '', $message );
	}
}

/**
 * Generates a user-level error/warning/notice/deprecation message.
 *
 * Generates the message when `WP_DEBUG` is true.
 *
 * @since 6.4.0
 *
 * @param string $function_name The function that triggered the error.
 * @param string $message       The message explaining the error.
 *                              The message can contain allowed HTML 'a' (with href), 'code',
 *                              'br', 'em', and 'strong' tags and http or https protocols.
 *                              If it contains other HTML tags or protocols, the message should be escaped
 *                              before passing to this function to avoid being stripped {@see wp_kses()}.
 * @param int    $error_level   Optional. The designated error type for this error.
 *                              Only works with E_USER family of constants. Default E_USER_NOTICE.
 */
function wp_trigger_error( $function_name, $message, $error_level = E_USER_NOTICE ) {

	// Bail out if WP_DEBUG is not turned on.
	if ( ! WP_DEBUG ) {
		return;
	}

	/**
	 * Fires when the given function triggers a user-level error/warning/notice/deprecation message.
	 *
	 * Can be used for debug backtracking.
	 *
	 * @since 6.4.0
	 *
	 * @param string $function_name The function that was called.
	 * @param string $message       A message explaining what has been done incorrectly.
	 * @param int    $error_level   The designated error type for this error.
	 */
	do_action( 'wp_trigger_error_run', $function_name, $message, $error_level );

	if ( ! empty( $function_name ) ) {
		$message = sprintf( '%s(): %s', $function_name, $message );
	}

	$message = wp_kses(
		$message,
		array(
			'a'      => array( 'href' => true ),
			'br'     => array(),
			'code'   => array(),
			'em'     => array(),
			'strong' => array(),
		),
		array( 'http', 'https' )
	);

	if ( E_USER_ERROR === $error_level ) {
		throw new WP_Exception( $message );
	}

	trigger_error( $message, $error_level );
}

/**
 * Determines whether the server is running an earlier than 1.5.0 version of lighttpd.
 *
 * @since 2.5.0
 *
 * @return bool Whether the server is running lighttpd < 1.5.0.
 */
function is_lighttpd_before_150() {
	$server_parts    = explode( '/', isset( $_SERVER['SERVER_SOFTWARE'] ) ? $_SERVER['SERVER_SOFTWARE'] : '' );
	$server_parts[1] = isset( $server_parts[1] ) ? $server_parts[1] : '';

	return ( 'lighttpd' === $server_parts[0] && -1 === version_compare( $server_parts[1], '1.5.0' ) );
}

/**
 * Determines whether the specified module exist in the Apache config.
 *
 * @since 2.5.0
 *
 * @global bool $is_apache
 *
 * @param string $mod           The module, e.g. mod_rewrite.
 * @param bool   $default_value Optional. The default return value if the module is not found. Default false.
 * @return bool Whether the specified module is loaded.
 */
function apache_mod_loaded( $mod, $default_value = false ) {
	global $is_apache;

	if ( ! $is_apache ) {
		return false;
	}

	$loaded_mods = array();

	if ( function_exists( 'apache_get_modules' ) ) {
		$loaded_mods = apache_get_modules();

		if ( in_array( $mod, $loaded_mods, true ) ) {
			return true;
		}
	}

	if ( empty( $loaded_mods )
		&& function_exists( 'phpinfo' )
		&& ! str_contains( ini_get( 'disable_functions' ), 'phpinfo' )
	) {
		ob_start();
		phpinfo( INFO_MODULES );
		$phpinfo = ob_get_clean();

		if ( str_contains( $phpinfo, $mod ) ) {
			return true;
		}
	}

	return $default_value;
}

/**
 * Checks if IIS 7+ supports pretty permalinks.
 *
 * @since 2.8.0
 *
 * @global bool $is_iis7
 *
 * @return bool Whether IIS7 supports permalinks.
 */
function iis7_supports_permalinks() {
	global $is_iis7;

	$supports_permalinks = false;
	if ( $is_iis7 ) {
		/* First we check if the DOMDocument class exists. If it does not exist, then we cannot
		 * easily update the xml configuration file, hence we just bail out and tell user that
		 * pretty permalinks cannot be used.
		 *
		 * Next we check if the URL Rewrite Module 1.1 is loaded and enabled for the website. When
		 * URL Rewrite 1.1 is loaded it always sets a server variable called 'IIS_UrlRewriteModule'.
		 * Lastly we make sure that PHP is running via FastCGI. This is important because if it runs
		 * via ISAPI then pretty permalinks will not work.
		 */
		$supports_permalinks = class_exists( 'DOMDocument', false ) && isset( $_SERVER['IIS_UrlRewriteModule'] ) && ( 'cgi-fcgi' === PHP_SAPI );
	}

	/**
	 * Filters whether IIS 7+ supports pretty permalinks.
	 *
	 * @since 2.8.0
	 *
	 * @param bool $supports_permalinks Whether IIS7 supports permalinks. Default false.
	 */
	return apply_filters( 'iis7_supports_permalinks', $supports_permalinks );
}

/**
 * Validates a file name and path against an allowed set of rules.
 *
 * A return value of `1` means the file path contains directory traversal.
 *
 * A return value of `2` means the file path contains a Windows drive path.
 *
 * A return value of `3` means the file is not in the allowed files list.
 *
 * @since 1.2.0
 *
 * @param string   $file          File path.
 * @param string[] $allowed_files Optional. Array of allowed files. Default empty array.
 * @return int 0 means nothing is wrong, greater than 0 means something was wrong.
 */
function validate_file( $file, $allowed_files = array() ) {
	if ( ! is_scalar( $file ) || '' === $file ) {
		return 0;
	}

	// Normalize path for Windows servers.
	$file = wp_normalize_path( $file );
	// Normalize path for $allowed_files as well so it's an apples to apples comparison.
	$allowed_files = array_map( 'wp_normalize_path', $allowed_files );

	// `../` on its own is not allowed:
	if ( '../' === $file ) {
		return 1;
	}

	// More than one occurrence of `../` is not allowed:
	if ( preg_match_all( '#\.\./#', $file, $matches, PREG_SET_ORDER ) && ( count( $matches ) > 1 ) ) {
		return 1;
	}

	// `../` which does not occur at the end of the path is not allowed:
	if ( str_contains( $file, '../' ) && '../' !== mb_substr( $file, -3, 3 ) ) {
		return 1;
	}

	// Files not in the allowed file list are not allowed:
	if ( ! empty( $allowed_files ) && ! in_array( $file, $allowed_files, true ) ) {
		return 3;
	}

	// Absolute Windows drive paths are not allowed:
	if ( ':' === substr( $file, 1, 1 ) ) {
		return 2;
	}

	return 0;
}

/**
 * Determines whether to force SSL used for the Administration Screens.
 *
 * @since 2.6.0
 *
 * @param string|bool|null $force Optional. Whether to force SSL in admin screens. Default null.
 * @return bool True if forced, false if not forced.
 */
function force_ssl_admin( $force = null ) {
	static $forced = false;

	if ( ! is_null( $force ) ) {
		$old_forced = $forced;
		$forced     = (bool) $force;
		return $old_forced;
	}

	return $forced;
}

/**
 * Guesses the URL for the site.
 *
 * Will remove wp-admin links to retrieve only return URLs not in the wp-admin
 * directory.
 *
 * @since 2.6.0
 *
 * @return string The guessed URL.
 */
function wp_guess_url() {
	if ( defined( 'WP_SITEURL' ) && '' !== WP_SITEURL ) {
		$url = WP_SITEURL;
	} else {
		$abspath_fix         = str_replace( '\\', '/', ABSPATH );
		$script_filename_dir = dirname( $_SERVER['SCRIPT_FILENAME'] );

		// The request is for the admin.
		if ( str_contains( $_SERVER['REQUEST_URI'], 'wp-admin' ) || str_contains( $_SERVER['REQUEST_URI'], 'wp-login.php' ) ) {
			$path = preg_replace( '#/(wp-admin/?.*|wp-login\.php.*)#i', '', $_SERVER['REQUEST_URI'] );

			// The request is for a file in ABSPATH.
		} elseif ( $script_filename_dir . '/' === $abspath_fix ) {
			// Strip off any file/query params in the path.
			$path = preg_replace( '#/[^/]*$#i', '', $_SERVER['PHP_SELF'] );

		} else {
			if ( str_contains( $_SERVER['SCRIPT_FILENAME'], $abspath_fix ) ) {
				// Request is hitting a file inside ABSPATH.
				$directory = str_replace( ABSPATH, '', $script_filename_dir );
				// Strip off the subdirectory, and any file/query params.
				$path = preg_replace( '#/' . preg_quote( $directory, '#' ) . '/[^/]*$#i', '', $_SERVER['REQUEST_URI'] );
			} elseif ( str_contains( $abspath_fix, $script_filename_dir ) ) {
				// Request is hitting a file above ABSPATH.
				$subdirectory = substr( $abspath_fix, strpos( $abspath_fix, $script_filename_dir ) + strlen( $script_filename_dir ) );
				// Strip off any file/query params from the path, appending the subdirectory to the installation.
				$path = preg_replace( '#/[^/]*$#i', '', $_SERVER['REQUEST_URI'] ) . $subdirectory;
			} else {
				$path = $_SERVER['REQUEST_URI'];
			}
		}

		$schema = is_ssl() ? 'https://' : 'http://'; // set_url_scheme() is not defined yet.
		$url    = $schema . $_SERVER['HTTP_HOST'] . $path;
	}

	return rtrim( $url, '/' );
}

/**
 * Temporarily suspends cache additions.
 *
 * Stops more data being added to the cache, but still allows cache retrieval.
 * This is useful for actions, such as imports, when a lot of data would otherwise
 * be almost uselessly added to the cache.
 *
 * Suspension lasts for a single page load at most. Remember to call this
 * function again if you wish to re-enable cache adds earlier.
 *
 * @since 3.3.0
 *
 * @param bool $suspend Optional. Suspends additions if true, re-enables them if false.
 *                      Defaults to not changing the current setting.
 * @return bool The current suspend setting.
 */
function wp_suspend_cache_addition( $suspend = null ) {
	static $_suspend = false;

	if ( is_bool( $suspend ) ) {
		$_suspend = $suspend;
	}

	return $_suspend;
}

/**
 * Suspends cache invalidation.
 *
 * Turns cache invalidation on and off. Useful during imports where you don't want to do
 * invalidations every time a post is inserted. Callers must be sure that what they are
 * doing won't lead to an inconsistent cache when invalidation is suspended.
 *
 * @since 2.7.0
 *
 * @global bool $_wp_suspend_cache_invalidation
 *
 * @param bool $suspend Optional. Whether to suspend or enable cache invalidation. Default true.
 * @return bool The current suspend setting.
 */
function wp_suspend_cache_invalidation( $suspend = true ) {
	global $_wp_suspend_cache_invalidation;

	$current_suspend                = $_wp_suspend_cache_invalidation;
	$_wp_suspend_cache_invalidation = $suspend;
	return $current_suspend;
}

/**
 * Determines whether a site is the main site of the current network.
 *
 * @since 3.0.0
 * @since 4.9.0 The `$network_id` parameter was added.
 *
 * @param int $site_id    Optional. Site ID to test. Defaults to current site.
 * @param int $network_id Optional. Network ID of the network to check for.
 *                        Defaults to current network.
 * @return bool True if $site_id is the main site of the network, or if not
 *              running Multisite.
 */
function is_main_site( $site_id = null, $network_id = null ) {
	if ( ! is_multisite() ) {
		return true;
	}

	if ( ! $site_id ) {
		$site_id = get_current_blog_id();
	}

	$site_id = (int) $site_id;

	return get_main_site_id( $network_id ) === $site_id;
}

/**
 * Gets the main site ID.
 *
 * @since 4.9.0
 *
 * @param int $network_id Optional. The ID of the network for which to get the main site.
 *                        Defaults to the current network.
 * @return int The ID of the main site.
 */
function get_main_site_id( $network_id = null ) {
	if ( ! is_multisite() ) {
		return get_current_blog_id();
	}

	$network = get_network( $network_id );
	if ( ! $network ) {
		return 0;
	}

	return $network->site_id;
}

/**
 * Determines whether a network is the main network of the Multisite installation.
 *
 * @since 3.7.0
 *
 * @param int $network_id Optional. Network ID to test. Defaults to current network.
 * @return bool True if $network_id is the main network, or if not running Multisite.
 */
function is_main_network( $network_id = null ) {
	if ( ! is_multisite() ) {
		return true;
	}

	if ( null === $network_id ) {
		$network_id = get_current_network_id();
	}

	$network_id = (int) $network_id;

	return ( get_main_network_id() === $network_id );
}

/**
 * Gets the main network ID.
 *
 * @since 4.3.0
 *
 * @return int The ID of the main network.
 */
function get_main_network_id() {
	if ( ! is_multisite() ) {
		return 1;
	}

	$current_network = get_network();

	if ( defined( 'PRIMARY_NETWORK_ID' ) ) {
		$main_network_id = PRIMARY_NETWORK_ID;
	} elseif ( isset( $current_network->id ) && 1 === (int) $current_network->id ) {
		// If the current network has an ID of 1, assume it is the main network.
		$main_network_id = 1;
	} else {
		$_networks       = get_networks(
			array(
				'fields' => 'ids',
				'number' => 1,
			)
		);
		$main_network_id = array_shift( $_networks );
	}

	/**
	 * Filters the main network ID.
	 *
	 * @since 4.3.0
	 *
	 * @param int $main_network_id The ID of the main network.
	 */
	return (int) apply_filters( 'get_main_network_id', $main_network_id );
}

/**
 * Determines whether site meta is enabled.
 *
 * This function checks whether the 'blogmeta' database table exists. The result is saved as
 * a setting for the main network, making it essentially a global setting. Subsequent requests
 * will refer to this setting instead of running the query.
 *
 * @since 5.1.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @return bool True if site meta is supported, false otherwise.
 */
function is_site_meta_supported() {
	global $wpdb;

	if ( ! is_multisite() ) {
		return false;
	}

	$network_id = get_main_network_id();

	$supported = get_network_option( $network_id, 'site_meta_supported', false );
	if ( false === $supported ) {
		$supported = $wpdb->get_var( "SHOW TABLES LIKE '{$wpdb->blogmeta}'" ) ? 1 : 0;

		update_network_option( $network_id, 'site_meta_supported', $supported );
	}

	return (bool) $supported;
}

/**
 * Modifies gmt_offset for smart timezone handling.
 *
 * Overrides the gmt_offset option if we have a timezone_string available.
 *
 * @since 2.8.0
 *
 * @return float|false Timezone GMT offset, false otherwise.
 */
function wp_timezone_override_offset() {
	$timezone_string = get_option( 'timezone_string' );
	if ( ! $timezone_string ) {
		return false;
	}

	$timezone_object = timezone_open( $timezone_string );
	$datetime_object = date_create();
	if ( false === $timezone_object || false === $datetime_object ) {
		return false;
	}

	return round( timezone_offset_get( $timezone_object, $datetime_object ) / HOUR_IN_SECONDS, 2 );
}

/**
 * Sort-helper for timezones.
 *
 * @since 2.9.0
 * @access private
 *
 * @param array $a
 * @param array $b
 * @return int
 */
function _wp_timezone_choice_usort_callback( $a, $b ) {
	// Don't use translated versions of Etc.
	if ( 'Etc' === $a['continent'] && 'Etc' === $b['continent'] ) {
		// Make the order of these more like the old dropdown.
		if ( str_starts_with( $a['city'], 'GMT+' ) && str_starts_with( $b['city'], 'GMT+' ) ) {
			return -1 * ( strnatcasecmp( $a['city'], $b['city'] ) );
		}

		if ( 'UTC' === $a['city'] ) {
			if ( str_starts_with( $b['city'], 'GMT+' ) ) {
				return 1;
			}

			return -1;
		}

		if ( 'UTC' === $b['city'] ) {
			if ( str_starts_with( $a['city'], 'GMT+' ) ) {
				return -1;
			}

			return 1;
		}

		return strnatcasecmp( $a['city'], $b['city'] );
	}

	if ( $a['t_continent'] === $b['t_continent'] ) {
		if ( $a['t_city'] === $b['t_city'] ) {
			return strnatcasecmp( $a['t_subcity'], $b['t_subcity'] );
		}

		return strnatcasecmp( $a['t_city'], $b['t_city'] );
	} else {
		// Force Etc to the bottom of the list.
		if ( 'Etc' === $a['continent'] ) {
			return 1;
		}

		if ( 'Etc' === $b['continent'] ) {
			return -1;
		}

		return strnatcasecmp( $a['t_continent'], $b['t_continent'] );
	}
}

/**
 * Gives a nicely-formatted list of timezone strings.
 *
 * @since 2.9.0
 * @since 4.7.0 Added the `$locale` parameter.
 *
 * @param string $selected_zone Selected timezone.
 * @param string $locale        Optional. Locale to load the timezones in. Default current site locale.
 * @return string
 */
function wp_timezone_choice( $selected_zone, $locale = null ) {
	static $mo_loaded = false, $locale_loaded = null;

	$continents = array( 'Africa', 'America', 'Antarctica', 'Arctic', 'Asia', 'Atlantic', 'Australia', 'Europe', 'Indian', 'Pacific' );

	// Load translations for continents and cities.
	if ( ! $mo_loaded || $locale !== $locale_loaded ) {
		$locale_loaded = $locale ? $locale : get_locale();
		$mofile        = WP_LANG_DIR . '/continents-cities-' . $locale_loaded . '.mo';
		unload_textdomain( 'continents-cities', true );
		load_textdomain( 'continents-cities', $mofile, $locale_loaded );
		$mo_loaded = true;
	}

	$tz_identifiers = timezone_identifiers_list();
	$zonen          = array();

	foreach ( $tz_identifiers as $zone ) {
		$zone = explode( '/', $zone );
		if ( ! in_array( $zone[0], $continents, true ) ) {
			continue;
		}

		// This determines what gets set and translated - we don't translate Etc/* strings here, they are done later.
		$exists    = array(
			0 => ( isset( $zone[0] ) && $zone[0] ),
			1 => ( isset( $zone[1] ) && $zone[1] ),
			2 => ( isset( $zone[2] ) && $zone[2] ),
		);
		$exists[3] = ( $exists[0] && 'Etc' !== $zone[0] );
		$exists[4] = ( $exists[1] && $exists[3] );
		$exists[5] = ( $exists[2] && $exists[3] );

		// phpcs:disable WordPress.WP.I18n.LowLevelTranslationFunction,WordPress.WP.I18n.NonSingularStringLiteralText
		$zonen[] = array(
			'continent'   => ( $exists[0] ? $zone[0] : '' ),
			'city'        => ( $exists[1] ? $zone[1] : '' ),
			'subcity'     => ( $exists[2] ? $zone[2] : '' ),
			't_continent' => ( $exists[3] ? translate( str_replace( '_', ' ', $zone[0] ), 'continents-cities' ) : '' ),
			't_city'      => ( $exists[4] ? translate( str_replace( '_', ' ', $zone[1] ), 'continents-cities' ) : '' ),
			't_subcity'   => ( $exists[5] ? translate( str_replace( '_', ' ', $zone[2] ), 'continents-cities' ) : '' ),
		);
		// phpcs:enable
	}
	usort( $zonen, '_wp_timezone_choice_usort_callback' );

	$structure = array();

	if ( empty( $selected_zone ) ) {
		$structure[] = '<option selected="selected" value="">' . __( 'Select a city' ) . '</option>';
	}

	// If this is a deprecated, but valid, timezone string, display it at the top of the list as-is.
	if ( in_array( $selected_zone, $tz_identifiers, true ) === false
		&& in_array( $selected_zone, timezone_identifiers_list( DateTimeZone::ALL_WITH_BC ), true )
	) {
		$structure[] = '<option selected="selected" value="' . esc_attr( $selected_zone ) . '">' . esc_html( $selected_zone ) . '</option>';
	}

	foreach ( $zonen as $key => $zone ) {
		// Build value in an array to join later.
		$value = array( $zone['continent'] );

		if ( empty( $zone['city'] ) ) {
			// It's at the continent level (generally won't happen).
			$display = $zone['t_continent'];
		} else {
			// It's inside a continent group.

			// Continent optgroup.
			if ( ! isset( $zonen[ $key - 1 ] ) || $zonen[ $key - 1 ]['continent'] !== $zone['continent'] ) {
				$label       = $zone['t_continent'];
				$structure[] = '<optgroup label="' . esc_attr( $label ) . '">';
			}

			// Add the city to the value.
			$value[] = $zone['city'];

			$display = $zone['t_city'];
			if ( ! empty( $zone['subcity'] ) ) {
				// Add the subcity to the value.
				$value[]  = $zone['subcity'];
				$display .= ' - ' . $zone['t_subcity'];
			}
		}

		// Build the value.
		$value    = implode( '/', $value );
		$selected = '';
		if ( $value === $selected_zone ) {
			$selected = 'selected="selected" ';
		}
		$structure[] = '<option ' . $selected . 'value="' . esc_attr( $value ) . '">' . esc_html( $display ) . '</option>';

		// Close continent optgroup.
		if ( ! empty( $zone['city'] ) && ( ! isset( $zonen[ $key + 1 ] ) || ( isset( $zonen[ $key + 1 ] ) && $zonen[ $key + 1 ]['continent'] !== $zone['continent'] ) ) ) {
			$structure[] = '</optgroup>';
		}
	}

	// Do UTC.
	$structure[] = '<optgroup label="' . esc_attr__( 'UTC' ) . '">';
	$selected    = '';
	if ( 'UTC' === $selected_zone ) {
		$selected = 'selected="selected" ';
	}
	$structure[] = '<option ' . $selected . 'value="' . esc_attr( 'UTC' ) . '">' . __( 'UTC' ) . '</option>';
	$structure[] = '</optgroup>';

	// Do manual UTC offsets.
	$structure[]  = '<optgroup label="' . esc_attr__( 'Manual Offsets' ) . '">';
	$offset_range = array(
		-12,
		-11.5,
		-11,
		-10.5,
		-10,
		-9.5,
		-9,
		-8.5,
		-8,
		-7.5,
		-7,
		-6.5,
		-6,
		-5.5,
		-5,
		-4.5,
		-4,
		-3.5,
		-3,
		-2.5,
		-2,
		-1.5,
		-1,
		-0.5,
		0,
		0.5,
		1,
		1.5,
		2,
		2.5,
		3,
		3.5,
		4,
		4.5,
		5,
		5.5,
		5.75,
		6,
		6.5,
		7,
		7.5,
		8,
		8.5,
		8.75,
		9,
		9.5,
		10,
		10.5,
		11,
		11.5,
		12,
		12.75,
		13,
		13.75,
		14,
	);
	foreach ( $offset_range as $offset ) {
		if ( 0 <= $offset ) {
			$offset_name = '+' . $offset;
		} else {
			$offset_name = (string) $offset;
		}

		$offset_value = $offset_name;
		$offset_name  = str_replace( array( '.25', '.5', '.75' ), array( ':15', ':30', ':45' ), $offset_name );
		$offset_name  = 'UTC' . $offset_name;
		$offset_value = 'UTC' . $offset_value;
		$selected     = '';
		if ( $offset_value === $selected_zone ) {
			$selected = 'selected="selected" ';
		}
		$structure[] = '<option ' . $selected . 'value="' . esc_attr( $offset_value ) . '">' . esc_html( $offset_name ) . '</option>';

	}
	$structure[] = '</optgroup>';

	return implode( "\n", $structure );
}

/**
 * Strips close comment and close php tags from file headers used by WP.
 *
 * @since 2.8.0
 * @access private
 *
 * @see https://core.trac.wordpress.org/ticket/8497
 *
 * @param string $str Header comment to clean up.
 * @return string
 */
function _cleanup_header_comment( $str ) {
	return trim( preg_replace( '/\s*(?:\*\/|\?>).*/', '', $str ) );
}

/**
 * Permanently deletes comments or posts of any type that have held a status
 * of 'trash' for the number of days defined in EMPTY_TRASH_DAYS.
 *
 * The default value of `EMPTY_TRASH_DAYS` is 30 (days).
 *
 * @since 2.9.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 */
function wp_scheduled_delete() {
	global $wpdb;

	$delete_timestamp = time() - ( DAY_IN_SECONDS * EMPTY_TRASH_DAYS );

	$posts_to_delete = $wpdb->get_results( $wpdb->prepare( "SELECT post_id FROM $wpdb->postmeta WHERE meta_key = '_wp_trash_meta_time' AND meta_value < %d", $delete_timestamp ), ARRAY_A );

	foreach ( (array) $posts_to_delete as $post ) {
		$post_id = (int) $post['post_id'];
		if ( ! $post_id ) {
			continue;
		}

		$del_post = get_post( $post_id );

		if ( ! $del_post || 'trash' !== $del_post->post_status ) {
			delete_post_meta( $post_id, '_wp_trash_meta_status' );
			delete_post_meta( $post_id, '_wp_trash_meta_time' );
		} else {
			wp_delete_post( $post_id );
		}
	}

	$comments_to_delete = $wpdb->get_results( $wpdb->prepare( "SELECT comment_id FROM $wpdb->commentmeta WHERE meta_key = '_wp_trash_meta_time' AND meta_value < %d", $delete_timestamp ), ARRAY_A );

	foreach ( (array) $comments_to_delete as $comment ) {
		$comment_id = (int) $comment['comment_id'];
		if ( ! $comment_id ) {
			continue;
		}

		$del_comment = get_comment( $comment_id );

		if ( ! $del_comment || 'trash' !== $del_comment->comment_approved ) {
			delete_comment_meta( $comment_id, '_wp_trash_meta_time' );
			delete_comment_meta( $comment_id, '_wp_trash_meta_status' );
		} else {
			wp_delete_comment( $del_comment );
		}
	}
}

/**
 * Retrieves metadata from a file.
 *
 * Searches for metadata in the first 8 KB of a file, such as a plugin or theme.
 * Each piece of metadata must be on its own line. Fields can not span multiple
 * lines, the value will get cut at the end of the first line.
 *
 * If the file data is not within that first 8 KB, then the author should correct
 * their plugin file and move the data headers to the top.
 *
 * @link https://codex.wordpress.org/File_Header
 *
 * @since 2.9.0
 *
 * @param string $file            Absolute path to the file.
 * @param array  $default_headers List of headers, in the format `array( 'HeaderKey' => 'Header Name' )`.
 * @param string $context         Optional. If specified adds filter hook {@see 'extra_$context_headers'}.
 *                                Default empty string.
 * @return string[] Array of file header values keyed by header name.
 */
function get_file_data( $file, $default_headers, $context = '' ) {
	// Pull only the first 8 KB of the file in.
	$file_data = file_get_contents( $file, false, null, 0, 8 * KB_IN_BYTES );

	if ( false === $file_data ) {
		$file_data = '';
	}

	// Make sure we catch CR-only line endings.
	$file_data = str_replace( "\r", "\n", $file_data );

	/**
	 * Filters extra file headers by context.
	 *
	 * The dynamic portion of the hook name, `$context`, refers to
	 * the context where extra headers might be loaded.
	 *
	 * @since 2.9.0
	 *
	 * @param array $extra_context_headers Empty array by default.
	 */
	$extra_headers = $context ? apply_filters( "extra_{$context}_headers", array() ) : array();
	if ( $extra_headers ) {
		$extra_headers = array_combine( $extra_headers, $extra_headers ); // Keys equal values.
		$all_headers   = array_merge( $extra_headers, (array) $default_headers );
	} else {
		$all_headers = $default_headers;
	}

	foreach ( $all_headers as $field => $regex ) {
		if ( preg_match( '/^(?:[ \t]*<\?php)?[ \t\/*#@]*' . preg_quote( $regex, '/' ) . ':(.*)$/mi', $file_data, $match ) && $match[1] ) {
			$all_headers[ $field ] = _cleanup_header_comment( $match[1] );
		} else {
			$all_headers[ $field ] = '';
		}
	}

	return $all_headers;
}

/**
 * Returns true.
 *
 * Useful for returning true to filters easily.
 *
 * @since 3.0.0
 *
 * @see __return_false()
 *
 * @return true True.
 */
function __return_true() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.FunctionDoubleUnderscore,PHPCompatibility.FunctionNameRestrictions.ReservedFunctionNames.FunctionDoubleUnderscore
	return true;
}

/**
 * Returns false.
 *
 * Useful for returning false to filters easily.
 *
 * @since 3.0.0
 *
 * @see __return_true()
 *
 * @return false False.
 */
function __return_false() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.FunctionDoubleUnderscore,PHPCompatibility.FunctionNameRestrictions.ReservedFunctionNames.FunctionDoubleUnderscore
	return false;
}

/**
 * Returns 0.
 *
 * Useful for returning 0 to filters easily.
 *
 * @since 3.0.0
 *
 * @return int 0.
 */
function __return_zero() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.FunctionDoubleUnderscore,PHPCompatibility.FunctionNameRestrictions.ReservedFunctionNames.FunctionDoubleUnderscore
	return 0;
}

/**
 * Returns an empty array.
 *
 * Useful for returning an empty array to filters easily.
 *
 * @since 3.0.0
 *
 * @return array Empty array.
 */
function __return_empty_array() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.FunctionDoubleUnderscore,PHPCompatibility.FunctionNameRestrictions.ReservedFunctionNames.FunctionDoubleUnderscore
	return array();
}

/**
 * Returns null.
 *
 * Useful for returning null to filters easily.
 *
 * @since 3.4.0
 *
 * @return null Null value.
 */
function __return_null() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.FunctionDoubleUnderscore,PHPCompatibility.FunctionNameRestrictions.ReservedFunctionNames.FunctionDoubleUnderscore
	return null;
}

/**
 * Returns an empty string.
 *
 * Useful for returning an empty string to filters easily.
 *
 * @since 3.7.0
 *
 * @see __return_null()
 *
 * @return string Empty string.
 */
function __return_empty_string() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.FunctionDoubleUnderscore,PHPCompatibility.FunctionNameRestrictions.ReservedFunctionNames.FunctionDoubleUnderscore
	return '';
}

/**
 * Sends a HTTP header to disable content type sniffing in browsers which support it.
 *
 * @since 3.0.0
 *
 * @see https://blogs.msdn.com/ie/archive/2008/07/02/ie8-security-part-v-comprehensive-protection.aspx
 * @see https://src.chromium.org/viewvc/chrome?view=rev&revision=6985
 */
function send_nosniff_header() {
	header( 'X-Content-Type-Options: nosniff' );
}

/**
 * Returns a MySQL expression for selecting the week number based on the start_of_week option.
 *
 * @ignore
 * @since 3.0.0
 *
 * @param string $column Database column.
 * @return string SQL clause.
 */
function _wp_mysql_week( $column ) {
	$start_of_week = (int) get_option( 'start_of_week' );
	switch ( $start_of_week ) {
		case 1:
			return "WEEK( $column, 1 )";
		case 2:
		case 3:
		case 4:
		case 5:
		case 6:
			return "WEEK( DATE_SUB( $column, INTERVAL $start_of_week DAY ), 0 )";
		case 0:
		default:
			return "WEEK( $column, 0 )";
	}
}

/**
 * Finds hierarchy loops using a callback function that maps object IDs to parent IDs.
 *
 * @since 3.1.0
 * @access private
 *
 * @param callable $callback      Function that accepts ( ID, $callback_args ) and outputs parent_ID.
 * @param int      $start         The ID to start the loop check at.
 * @param int      $start_parent  The parent_ID of $start to use instead of calling $callback( $start ).
 *                                Use null to always use $callback.
 * @param array    $callback_args Optional. Additional arguments to send to $callback. Default empty array.
 * @return array IDs of all members of loop.
 */
function wp_find_hierarchy_loop( $callback, $start, $start_parent, $callback_args = array() ) {
	$override = is_null( $start_parent ) ? array() : array( $start => $start_parent );

	$arbitrary_loop_member = wp_find_hierarchy_loop_tortoise_hare( $callback, $start, $override, $callback_args );
	if ( ! $arbitrary_loop_member ) {
		return array();
	}

	return wp_find_hierarchy_loop_tortoise_hare( $callback, $arbitrary_loop_member, $override, $callback_args, true );
}

/**
 * Uses the "The Tortoise and the Hare" algorithm to detect loops.
 *
 * For every step of the algorithm, the hare takes two steps and the tortoise one.
 * If the hare ever laps the tortoise, there must be a loop.
 *
 * @since 3.1.0
 * @access private
 *
 * @param callable $callback      Function that accepts ( ID, callback_arg, ... ) and outputs parent_ID.
 * @param int      $start         The ID to start the loop check at.
 * @param array    $override      Optional. An array of ( ID => parent_ID, ... ) to use instead of $callback.
 *                                Default empty array.
 * @param array    $callback_args Optional. Additional arguments to send to $callback. Default empty array.
 * @param bool     $_return_loop  Optional. Return loop members or just detect presence of loop? Only set
 *                                to true if you already know the given $start is part of a loop (otherwise
 *                                the returned array might include branches). Default false.
 * @return mixed Scalar ID of some arbitrary member of the loop, or array of IDs of all members of loop if
 *               $_return_loop
 */
function wp_find_hierarchy_loop_tortoise_hare( $callback, $start, $override = array(), $callback_args = array(), $_return_loop = false ) {
	$tortoise        = $start;
	$hare            = $start;
	$evanescent_hare = $start;
	$return          = array();

	// Set evanescent_hare to one past hare. Increment hare two steps.
	while (
		$tortoise
	&&
		( $evanescent_hare = isset( $override[ $hare ] ) ? $override[ $hare ] : call_user_func_array( $callback, array_merge( array( $hare ), $callback_args ) ) )
	&&
		( $hare = isset( $override[ $evanescent_hare ] ) ? $override[ $evanescent_hare ] : call_user_func_array( $callback, array_merge( array( $evanescent_hare ), $callback_args ) ) )
	) {
		if ( $_return_loop ) {
			$return[ $tortoise ]        = true;
			$return[ $evanescent_hare ] = true;
			$return[ $hare ]            = true;
		}

		// Tortoise got lapped - must be a loop.
		if ( $tortoise === $evanescent_hare || $tortoise === $hare ) {
			return $_return_loop ? $return : $tortoise;
		}

		// Increment tortoise by one step.
		$tortoise = isset( $override[ $tortoise ] ) ? $override[ $tortoise ] : call_user_func_array( $callback, array_merge( array( $tortoise ), $callback_args ) );
	}

	return false;
}

/**
 * Sends a HTTP header to limit rendering of pages to same origin iframes.
 *
 * @since 3.1.3
 *
 * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/X-Frame-Options
 */
function send_frame_options_header() {
	header( 'X-Frame-Options: SAMEORIGIN' );
}

/**
 * Sends a referrer policy header so referrers are not sent externally from administration screens.
 *
 * @since 4.9.0
 * @since 6.8.0 This function was moved from `wp-admin/includes/misc.php` to `wp-includes/functions.php`.
 */
function wp_admin_headers() {
	$policy = 'strict-origin-when-cross-origin';

	/**
	 * Filters the admin referrer policy header value.
	 *
	 * @since 4.9.0
	 * @since 4.9.5 The default value was changed to 'strict-origin-when-cross-origin'.
	 *
	 * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Referrer-Policy
	 *
	 * @param string $policy The admin referrer policy header value. Default 'strict-origin-when-cross-origin'.
	 */
	$policy = apply_filters( 'admin_referrer_policy', $policy );

	header( sprintf( 'Referrer-Policy: %s', $policy ) );
}

/**
 * Retrieves a list of protocols to allow in HTML attributes.
 *
 * @since 3.3.0
 * @since 4.3.0 Added 'webcal' to the protocols array.
 * @since 4.7.0 Added 'urn' to the protocols array.
 * @since 5.3.0 Added 'sms' to the protocols array.
 * @since 5.6.0 Added 'irc6' and 'ircs' to the protocols array.
 *
 * @see wp_kses()
 * @see esc_url()
 *
 * @return string[] Array of allowed protocols. Defaults to an array containing 'http', 'https',
 *                  'ftp', 'ftps', 'mailto', 'news', 'irc', 'irc6', 'ircs', 'gopher', 'nntp', 'feed',
 *                  'telnet', 'mms', 'rtsp', 'sms', 'svn', 'tel', 'fax', 'xmpp', 'webcal', and 'urn'.
 *                  This covers all common link protocols, except for 'javascript' which should not
 *                  be allowed for untrusted users.
 */
function wp_allowed_protocols() {
	static $protocols = array();

	if ( empty( $protocols ) ) {
		$protocols = array( 'http', 'https', 'ftp', 'ftps', 'mailto', 'news', 'irc', 'irc6', 'ircs', 'gopher', 'nntp', 'feed', 'telnet', 'mms', 'rtsp', 'sms', 'svn', 'tel', 'fax', 'xmpp', 'webcal', 'urn' );
	}

	if ( ! did_action( 'wp_loaded' ) ) {
		/**
		 * Filters the list of protocols allowed in HTML attributes.
		 *
		 * @since 3.0.0
		 *
		 * @param string[] $protocols Array of allowed protocols e.g. 'http', 'ftp', 'tel', and more.
		 */
		$protocols = array_unique( (array) apply_filters( 'kses_allowed_protocols', $protocols ) );
	}

	return $protocols;
}

/**
 * Returns a comma-separated string or array of functions that have been called to get
 * to the current point in code.
 *
 * @since 3.4.0
 *
 * @see https://core.trac.wordpress.org/ticket/19589
 *
 * @param string $ignore_class Optional. A class to ignore all function calls within - useful
 *                             when you want to just give info about the callee. Default null.
 * @param int    $skip_frames  Optional. A number of stack frames to skip - useful for unwinding
 *                             back to the source of the issue. Default 0.
 * @param bool   $pretty       Optional. Whether you want a comma separated string instead of
 *                             the raw array returned. Default true.
 * @return string|array Either a string containing a reversed comma separated trace or an array
 *                      of individual calls.
 */
function wp_debug_backtrace_summary( $ignore_class = null, $skip_frames = 0, $pretty = true ) {
	static $truncate_paths;

	$trace       = debug_backtrace( false );
	$caller      = array();
	$check_class = ! is_null( $ignore_class );
	++$skip_frames; // Skip this function.

	if ( ! isset( $truncate_paths ) ) {
		$truncate_paths = array(
			wp_normalize_path( WP_CONTENT_DIR ),
			wp_normalize_path( ABSPATH ),
		);
	}

	foreach ( $trace as $call ) {
		if ( $skip_frames > 0 ) {
			--$skip_frames;
		} elseif ( isset( $call['class'] ) ) {
			if ( $check_class && $ignore_class === $call['class'] ) {
				continue; // Filter out calls.
			}

			$caller[] = "{$call['class']}{$call['type']}{$call['function']}";
		} else {
			if ( in_array( $call['function'], array( 'do_action', 'apply_filters', 'do_action_ref_array', 'apply_filters_ref_array' ), true ) ) {
				$caller[] = "{$call['function']}('{$call['args'][0]}')";
			} elseif ( in_array( $call['function'], array( 'include', 'include_once', 'require', 'require_once' ), true ) ) {
				$filename = isset( $call['args'][0] ) ? $call['args'][0] : '';
				$caller[] = $call['function'] . "('" . str_replace( $truncate_paths, '', wp_normalize_path( $filename ) ) . "')";
			} else {
				$caller[] = $call['function'];
			}
		}
	}
	if ( $pretty ) {
		return implode( ', ', array_reverse( $caller ) );
	} else {
		return $caller;
	}
}

/**
 * Retrieves IDs that are not already present in the cache.
 *
 * @since 3.4.0
 * @since 6.1.0 This function is no longer marked as "private".
 *
 * @param int[]  $object_ids  Array of IDs.
 * @param string $cache_group The cache group to check against.
 * @return int[] Array of IDs not present in the cache.
 */
function _get_non_cached_ids( $object_ids, $cache_group ) {
	$object_ids = array_filter( $object_ids, '_validate_cache_id' );
	$object_ids = array_unique( array_map( 'intval', $object_ids ), SORT_NUMERIC );

	if ( empty( $object_ids ) ) {
		return array();
	}

	$non_cached_ids = array();
	$cache_values   = wp_cache_get_multiple( $object_ids, $cache_group );

	foreach ( $cache_values as $id => $value ) {
		if ( false === $value ) {
			$non_cached_ids[] = (int) $id;
		}
	}

	return $non_cached_ids;
}

/**
 * Checks whether the given cache ID is either an integer or an integer-like string.
 *
 * Both `16` and `"16"` are considered valid, other numeric types and numeric strings
 * (`16.3` and `"16.3"`) are considered invalid.
 *
 * @since 6.3.0
 *
 * @param mixed $object_id The cache ID to validate.
 * @return bool Whether the given $object_id is a valid cache ID.
 */
function _validate_cache_id( $object_id ) {
	/*
	 * filter_var() could be used here, but the `filter` PHP extension
	 * is considered optional and may not be available.
	 */
	if ( is_int( $object_id )
		|| ( is_string( $object_id ) && (string) (int) $object_id === $object_id ) ) {
		return true;
	}

	/* translators: %s: The type of the given object ID. */
	$message = sprintf( __( 'Object ID must be an integer, %s given.' ), gettype( $object_id ) );
	_doing_it_wrong( '_get_non_cached_ids', $message, '6.3.0' );

	return false;
}

/**
 * Tests if the current device has the capability to upload files.
 *
 * @since 3.4.0
 * @access private
 *
 * @return bool Whether the device is able to upload files.
 */
function _device_can_upload() {
	if ( ! wp_is_mobile() ) {
		return true;
	}

	$ua = $_SERVER['HTTP_USER_AGENT'];

	if ( str_contains( $ua, 'iPhone' )
		|| str_contains( $ua, 'iPad' )
		|| str_contains( $ua, 'iPod' ) ) {
			return preg_match( '#OS ([\d_]+) like Mac OS X#', $ua, $version ) && version_compare( $version[1], '6', '>=' );
	}

	return true;
}

/**
 * Tests if a given path is a stream URL
 *
 * @since 3.5.0
 *
 * @param string $path The resource path or URL.
 * @return bool True if the path is a stream URL.
 */
function wp_is_stream( $path ) {
	$scheme_separator = strpos( $path, '://' );

	if ( false === $scheme_separator ) {
		// $path isn't a stream.
		return false;
	}

	$stream = substr( $path, 0, $scheme_separator );

	return in_array( $stream, stream_get_wrappers(), true );
}

/**
 * Tests if the supplied date is valid for the Gregorian calendar.
 *
 * @since 3.5.0
 *
 * @link https://www.php.net/manual/en/function.checkdate.php
 *
 * @param int    $month       Month number.
 * @param int    $day         Day number.
 * @param int    $year        Year number.
 * @param string $source_date The date to filter.
 * @return bool True if valid date, false if not valid date.
 */
function wp_checkdate( $month, $day, $year, $source_date ) {
	/**
	 * Filters whether the given date is valid for the Gregorian calendar.
	 *
	 * @since 3.5.0
	 *
	 * @param bool   $checkdate   Whether the given date is valid.
	 * @param string $source_date Date to check.
	 */
	return apply_filters( 'wp_checkdate', checkdate( $month, $day, $year ), $source_date );
}

/**
 * Loads the auth check for monitoring whether the user is still logged in.
 *
 * Can be disabled with remove_action( 'admin_enqueue_scripts', 'wp_auth_check_load' );
 *
 * This is disabled for certain screens where a login screen could cause an
 * inconvenient interruption. A filter called {@see 'wp_auth_check_load'} can be used
 * for fine-grained control.
 *
 * @since 3.6.0
 */
function wp_auth_check_load() {
	if ( ! is_admin() && ! is_user_logged_in() ) {
		return;
	}

	if ( defined( 'IFRAME_REQUEST' ) ) {
		return;
	}

	$screen = get_current_screen();
	$hidden = array( 'update', 'update-network', 'update-core', 'update-core-network', 'upgrade', 'upgrade-network', 'network' );
	$show   = ! in_array( $screen->id, $hidden, true );

	/**
	 * Filters whether to load the authentication check.
	 *
	 * Returning a falsey value from the filter will effectively short-circuit
	 * loading the authentication check.
	 *
	 * @since 3.6.0
	 *
	 * @param bool      $show   Whether to load the authentication check.
	 * @param WP_Screen $screen The current screen object.
	 */
	if ( apply_filters( 'wp_auth_check_load', $show, $screen ) ) {
		wp_enqueue_style( 'wp-auth-check' );
		wp_enqueue_script( 'wp-auth-check' );

		add_action( 'admin_print_footer_scripts', 'wp_auth_check_html', 5 );
		add_action( 'wp_print_footer_scripts', 'wp_auth_check_html', 5 );
	}
}

/**
 * Outputs the HTML that shows the wp-login dialog when the user is no longer logged in.
 *
 * @since 3.6.0
 */
function wp_auth_check_html() {
	$login_url      = wp_login_url();
	$current_domain = ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'];
	$same_domain    = str_starts_with( $login_url, $current_domain );

	/**
	 * Filters whether the authentication check originated at the same domain.
	 *
	 * @since 3.6.0
	 *
	 * @param bool $same_domain Whether the authentication check originated at the same domain.
	 */
	$same_domain = apply_filters( 'wp_auth_check_same_domain', $same_domain );
	$wrap_class  = $same_domain ? 'hidden' : 'hidden fallback';

	?>
	<div id="wp-auth-check-wrap" class="<?php echo $wrap_class; ?>">
	<div id="wp-auth-check-bg"></div>
	<div id="wp-auth-check">
	<button type="button" class="wp-auth-check-close button-link"><span class="screen-reader-text">
		<?php
		/* translators: Hidden accessibility text. */
		_e( 'Close dialog' );
		?>
	</span></button>
	<?php

	if ( $same_domain ) {
		$login_src = add_query_arg(
			array(
				'interim-login' => '1',
				'wp_lang'       => get_user_locale(),
			),
			$login_url
		);
		?>
		<div id="wp-auth-check-form" class="loading" data-src="<?php echo esc_url( $login_src ); ?>"></div>
		<?php
	}

	?>
	<div class="wp-auth-fallback">
		<p><b class="wp-auth-fallback-expired" tabindex="0"><?php _e( 'Session expired' ); ?></b></p>
		<p><a href="<?php echo esc_url( $login_url ); ?>" target="_blank"><?php _e( 'Please log in again.' ); ?></a>
		<?php _e( 'The login page will open in a new tab. After logging in you can close it and return to this page.' ); ?></p>
	</div>
	</div>
	</div>
	<?php
}

/**
 * Checks whether a user is still logged in, for the heartbeat.
 *
 * Send a result that shows a log-in box if the user is no longer logged in,
 * or if their cookie is within the grace period.
 *
 * @since 3.6.0
 *
 * @global int $login_grace_period
 *
 * @param array $response  The Heartbeat response.
 * @return array The Heartbeat response with 'wp-auth-check' value set.
 */
function wp_auth_check( $response ) {
	$response['wp-auth-check'] = is_user_logged_in() && empty( $GLOBALS['login_grace_period'] );
	return $response;
}

/**
 * Returns RegEx body to liberally match an opening HTML tag.
 *
 * Matches an opening HTML tag that:
 * 1. Is self-closing or
 * 2. Has no body but has a closing tag of the same name or
 * 3. Contains a body and a closing tag of the same name
 *
 * Note: this RegEx does not balance inner tags and does not attempt
 * to produce valid HTML
 *
 * @since 3.6.0
 *
 * @param string $tag An HTML tag name. Example: 'video'.
 * @return string Tag RegEx.
 */
function get_tag_regex( $tag ) {
	if ( empty( $tag ) ) {
		return '';
	}
	return sprintf( '<%1$s[^<]*(?:>[\s\S]*<\/%1$s>|\s*\/>)', tag_escape( $tag ) );
}

/**
 * Indicates if a given slug for a character set represents the UTF-8
 * text encoding. If not provided, examines the current blog's charset.
 *
 * A charset is considered to represent UTF-8 if it is a case-insensitive
 * match of "UTF-8" with or without the hyphen.
 *
 * Example:
 *
 *     true  === is_utf8_charset( 'UTF-8' );
 *     true  === is_utf8_charset( 'utf8' );
 *     false === is_utf8_charset( 'latin1' );
 *     false === is_utf8_charset( 'UTF 8' );
 *
 *     // Only strings match.
 *     false === is_utf8_charset( [ 'charset' => 'utf-8' ] );
 *
 *     // Without a given charset, it depends on the site option "blog_charset".
 *     $is_utf8 = is_utf8_charset();
 *
 * @since 6.6.0
 * @since 6.6.1 A wrapper for _is_utf8_charset
 *
 * @see _is_utf8_charset
 *
 * @param string|null $blog_charset Optional. Slug representing a text character encoding, or "charset".
 *                                  E.g. "UTF-8", "Windows-1252", "ISO-8859-1", "SJIS".
 *                                  Default value is to infer from "blog_charset" option.
 * @return bool Whether the slug represents the UTF-8 encoding.
 */
function is_utf8_charset( $blog_charset = null ) {
	return _is_utf8_charset( $blog_charset ?? get_option( 'blog_charset' ) );
}

/**
 * Retrieves a canonical form of the provided charset appropriate for passing to PHP
 * functions such as htmlspecialchars() and charset HTML attributes.
 *
 * @since 3.6.0
 * @access private
 *
 * @see https://core.trac.wordpress.org/ticket/23688
 *
 * @param string $charset A charset name, e.g. "UTF-8", "Windows-1252", "SJIS".
 * @return string The canonical form of the charset.
 */
function _canonical_charset( $charset ) {
	if ( is_utf8_charset( $charset ) ) {
		return 'UTF-8';
	}

	/*
	 * Normalize the ISO-8859-1 family of languages.
	 *
	 * This is not required for htmlspecialchars(), as it properly recognizes all of
	 * the input character sets that here are transformed into "ISO-8859-1".
	 *
	 * @todo Should this entire check be removed since it's not required for the stated purpose?
	 * @todo Should WordPress transform other potential charset equivalents, such as "latin1"?
	 */
	if (
		( 0 === strcasecmp( 'iso-8859-1', $charset ) ) ||
		( 0 === strcasecmp( 'iso8859-1', $charset ) )
	) {
		return 'ISO-8859-1';
	}

	return $charset;
}

/**
 * Sets the mbstring internal encoding to a binary safe encoding when func_overload
 * is enabled.
 *
 * When mbstring.func_overload is in use for multi-byte encodings, the results from
 * strlen() and similar functions respect the utf8 characters, causing binary data
 * to return incorrect lengths.
 *
 * This function overrides the mbstring encoding to a binary-safe encoding, and
 * resets it to the users expected encoding afterwards through the
 * `reset_mbstring_encoding` function.
 *
 * It is safe to recursively call this function, however each
 * `mbstring_binary_safe_encoding()` call must be followed up with an equal number
 * of `reset_mbstring_encoding()` calls.
 *
 * @since 3.7.0
 *
 * @see reset_mbstring_encoding()
 *
 * @param bool $reset Optional. Whether to reset the encoding back to a previously-set encoding.
 *                    Default false.
 */
function mbstring_binary_safe_encoding( $reset = false ) {
	static $encodings  = array();
	static $overloaded = null;

	if ( is_null( $overloaded ) ) {
		if ( function_exists( 'mb_internal_encoding' )
			&& ( (int) ini_get( 'mbstring.func_overload' ) & 2 ) // phpcs:ignore PHPCompatibility.IniDirectives.RemovedIniDirectives.mbstring_func_overloadDeprecated
		) {
			$overloaded = true;
		} else {
			$overloaded = false;
		}
	}

	if ( false === $overloaded ) {
		return;
	}

	if ( ! $reset ) {
		$encoding = mb_internal_encoding();
		array_push( $encodings, $encoding );
		mb_internal_encoding( 'ISO-8859-1' );
	}

	if ( $reset && $encodings ) {
		$encoding = array_pop( $encodings );
		mb_internal_encoding( $encoding );
	}
}

/**
 * Resets the mbstring internal encoding to a users previously set encoding.
 *
 * @see mbstring_binary_safe_encoding()
 *
 * @since 3.7.0
 */
function reset_mbstring_encoding() {
	mbstring_binary_safe_encoding( true );
}

/**
 * Filters/validates a variable as a boolean.
 *
 * Alternative to `filter_var( $value, FILTER_VALIDATE_BOOLEAN )`.
 *
 * @since 4.0.0
 *
 * @param mixed $value Boolean value to validate.
 * @return bool Whether the value is validated.
 */
function wp_validate_boolean( $value ) {
	if ( is_bool( $value ) ) {
		return $value;
	}

	if ( is_string( $value ) && 'false' === strtolower( $value ) ) {
		return false;
	}

	return (bool) $value;
}

/**
 * Deletes a file.
 *
 * @since 4.2.0
 * @since 6.7.0 A return value was added.
 *
 * @param string $file The path to the file to delete.
 * @return bool True on success, false on failure.
 */
function wp_delete_file( $file ) {
	/**
	 * Filters the path of the file to delete.
	 *
	 * @since 2.1.0
	 *
	 * @param string $file Path to the file to delete.
	 */
	$delete = apply_filters( 'wp_delete_file', $file );

	if ( ! empty( $delete ) ) {
		return @unlink( $delete );
	}

	return false;
}

/**
 * Deletes a file if its path is within the given directory.
 *
 * @since 4.9.7
 *
 * @param string $file      Absolute path to the file to delete.
 * @param string $directory Absolute path to a directory.
 * @return bool True on success, false on failure.
 */
function wp_delete_file_from_directory( $file, $directory ) {
	if ( wp_is_stream( $file ) ) {
		$real_file      = $file;
		$real_directory = $directory;
	} else {
		$real_file      = realpath( wp_normalize_path( $file ) );
		$real_directory = realpath( wp_normalize_path( $directory ) );
	}

	if ( false !== $real_file ) {
		$real_file = wp_normalize_path( $real_file );
	}

	if ( false !== $real_directory ) {
		$real_directory = wp_normalize_path( $real_directory );
	}

	if ( false === $real_file || false === $real_directory || ! str_starts_with( $real_file, trailingslashit( $real_directory ) ) ) {
		return false;
	}

	return wp_delete_file( $file );
}

/**
 * Outputs a small JS snippet on preview tabs/windows to remove `window.name` when a user is navigating to another page.
 *
 * This prevents reusing the same tab for a preview when the user has navigated away.
 *
 * @since 4.3.0
 *
 * @global WP_Post $post Global post object.
 */
function wp_post_preview_js() {
	global $post;

	if ( ! is_preview() || empty( $post ) ) {
		return;
	}

	// Has to match the window name used in post_submit_meta_box().
	$name = 'wp-preview-' . (int) $post->ID;

	ob_start();
	?>
	<script>
	( function() {
		var query = document.location.search;

		if ( query && query.indexOf( 'preview=true' ) !== -1 ) {
			window.name = '<?php echo $name; ?>';
		}

		if ( window.addEventListener ) {
			window.addEventListener( 'pagehide', function() { window.name = ''; } );
		}
	}());
	</script>
	<?php
	wp_print_inline_script_tag( wp_remove_surrounding_empty_script_tags( ob_get_clean() ) );
}

/**
 * Parses and formats a MySQL datetime (Y-m-d H:i:s) for ISO8601 (Y-m-d\TH:i:s).
 *
 * Explicitly strips timezones, as datetimes are not saved with any timezone
 * information. Including any information on the offset could be misleading.
 *
 * Despite historical function name, the output does not conform to RFC3339 format,
 * which must contain timezone.
 *
 * @since 4.4.0
 *
 * @param string $date_string Date string to parse and format.
 * @return string Date formatted for ISO8601 without time zone.
 */
function mysql_to_rfc3339( $date_string ) {
	return mysql2date( 'Y-m-d\TH:i:s', $date_string, false );
}

/**
 * Attempts to raise the PHP memory limit for memory intensive processes.
 *
 * Only allows raising the existing limit and prevents lowering it.
 *
 * @since 4.6.0
 *
 * @param string $context Optional. Context in which the function is called. Accepts either 'admin',
 *                        'image', 'cron', or an arbitrary other context. If an arbitrary context is passed,
 *                        the similarly arbitrary {@see '$context_memory_limit'} filter will be
 *                        invoked. Default 'admin'.
 * @return int|string|false The limit that was set or false on failure.
 */
function wp_raise_memory_limit( $context = 'admin' ) {
	// Exit early if the limit cannot be changed.
	if ( false === wp_is_ini_value_changeable( 'memory_limit' ) ) {
		return false;
	}

	$current_limit     = ini_get( 'memory_limit' );
	$current_limit_int = wp_convert_hr_to_bytes( $current_limit );

	if ( -1 === $current_limit_int ) {
		return false;
	}

	$wp_max_limit     = WP_MAX_MEMORY_LIMIT;
	$wp_max_limit_int = wp_convert_hr_to_bytes( $wp_max_limit );
	$filtered_limit   = $wp_max_limit;

	switch ( $context ) {
		case 'admin':
			/**
			 * Filters the maximum memory limit available for administration screens.
			 *
			 * This only applies to administrators, who may require more memory for tasks
			 * like updates. Memory limits when processing images (uploaded or edited by
			 * users of any role) are handled separately.
			 *
			 * The `WP_MAX_MEMORY_LIMIT` constant specifically defines the maximum memory
			 * limit available when in the administration back end. The default is 256M
			 * (256 megabytes of memory) or the original `memory_limit` php.ini value if
			 * this is higher.
			 *
			 * @since 3.0.0
			 * @since 4.6.0 The default now takes the original `memory_limit` into account.
			 *
			 * @param int|string $filtered_limit The maximum WordPress memory limit. Accepts an integer
			 *                                   (bytes), or a shorthand string notation, such as '256M'.
			 */
			$filtered_limit = apply_filters( 'admin_memory_limit', $filtered_limit );
			break;

		case 'image':
			/**
			 * Filters the memory limit allocated for image manipulation.
			 *
			 * @since 3.5.0
			 * @since 4.6.0 The default now takes the original `memory_limit` into account.
			 *
			 * @param int|string $filtered_limit Maximum memory limit to allocate for image processing.
			 *                                   Default `WP_MAX_MEMORY_LIMIT` or the original
			 *                                   php.ini `memory_limit`, whichever is higher.
			 *                                   Accepts an integer (bytes), or a shorthand string
			 *                                   notation, such as '256M'.
			 */
			$filtered_limit = apply_filters( 'image_memory_limit', $filtered_limit );
			break;

		case 'cron':
			/**
			 * Filters the memory limit allocated for WP-Cron event processing.
			 *
			 * @since 6.3.0
			 *
			 * @param int|string $filtered_limit Maximum memory limit to allocate for WP-Cron.
			 *                                   Default `WP_MAX_MEMORY_LIMIT` or the original
			 *                                   php.ini `memory_limit`, whichever is higher.
			 *                                   Accepts an integer (bytes), or a shorthand string
			 *                                   notation, such as '256M'.
			 */
			$filtered_limit = apply_filters( 'cron_memory_limit', $filtered_limit );
			break;

		default:
			/**
			 * Filters the memory limit allocated for an arbitrary context.
			 *
			 * The dynamic portion of the hook name, `$context`, refers to an arbitrary
			 * context passed on calling the function. This allows for plugins to define
			 * their own contexts for raising the memory limit.
			 *
			 * @since 4.6.0
			 *
			 * @param int|string $filtered_limit Maximum memory limit to allocate for this context.
			 *                                   Default WP_MAX_MEMORY_LIMIT` or the original php.ini `memory_limit`,
			 *                                   whichever is higher. Accepts an integer (bytes), or a
			 *                                   shorthand string notation, such as '256M'.
			 */
			$filtered_limit = apply_filters( "{$context}_memory_limit", $filtered_limit );
			break;
	}

	$filtered_limit_int = wp_convert_hr_to_bytes( $filtered_limit );

	if ( -1 === $filtered_limit_int || ( $filtered_limit_int > $wp_max_limit_int && $filtered_limit_int > $current_limit_int ) ) {
		if ( false !== ini_set( 'memory_limit', $filtered_limit ) ) {
			return $filtered_limit;
		} else {
			return false;
		}
	} elseif ( -1 === $wp_max_limit_int || $wp_max_limit_int > $current_limit_int ) {
		if ( false !== ini_set( 'memory_limit', $wp_max_limit ) ) {
			return $wp_max_limit;
		} else {
			return false;
		}
	}

	return false;
}

/**
 * Generates a random UUID (version 4).
 *
 * @since 4.7.0
 *
 * @return string UUID.
 */
function wp_generate_uuid4() {
	return sprintf(
		'%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
		mt_rand( 0, 0xffff ),
		mt_rand( 0, 0xffff ),
		mt_rand( 0, 0xffff ),
		mt_rand( 0, 0x0fff ) | 0x4000,
		mt_rand( 0, 0x3fff ) | 0x8000,
		mt_rand( 0, 0xffff ),
		mt_rand( 0, 0xffff ),
		mt_rand( 0, 0xffff )
	);
}

/**
 * Validates that a UUID is valid.
 *
 * @since 4.9.0
 *
 * @param mixed $uuid    UUID to check.
 * @param int   $version Specify which version of UUID to check against. Default is none,
 *                       to accept any UUID version. Otherwise, only version allowed is `4`.
 * @return bool The string is a valid UUID or false on failure.
 */
function wp_is_uuid( $uuid, $version = null ) {

	if ( ! is_string( $uuid ) ) {
		return false;
	}

	if ( is_numeric( $version ) ) {
		if ( 4 !== (int) $version ) {
			_doing_it_wrong( __FUNCTION__, __( 'Only UUID V4 is supported at this time.' ), '4.9.0' );
			return false;
		}
		$regex = '/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/';
	} else {
		$regex = '/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/';
	}

	return (bool) preg_match( $regex, $uuid );
}

/**
 * Gets unique ID.
 *
 * This is a PHP implementation of Underscore's uniqueId method. A static variable
 * contains an integer that is incremented with each call. This number is returned
 * with the optional prefix. As such the returned value is not universally unique,
 * but it is unique across the life of the PHP process.
 *
 * @since 5.0.3
 *
 * @param string $prefix Prefix for the returned ID.
 * @return string Unique ID.
 */
function wp_unique_id( $prefix = '' ) {
	static $id_counter = 0;
	return $prefix . (string) ++$id_counter;
}

/**
 * Generates an incremental ID that is independent per each different prefix.
 *
 * It is similar to `wp_unique_id`, but each prefix has its own internal ID
 * counter to make each prefix independent from each other. The ID starts at 1
 * and increments on each call. The returned value is not universally unique,
 * but it is unique across the life of the PHP process and it's stable per
 * prefix.
 *
 * @since 6.4.0
 *
 * @param string $prefix Optional. Prefix for the returned ID. Default empty string.
 * @return string Incremental ID per prefix.
 */
function wp_unique_prefixed_id( $prefix = '' ) {
	static $id_counters = array();

	if ( ! is_string( $prefix ) ) {
		wp_trigger_error(
			__FUNCTION__,
			sprintf( 'The prefix must be a string. "%s" data type given.', gettype( $prefix ) )
		);
		$prefix = '';
	}

	if ( ! isset( $id_counters[ $prefix ] ) ) {
		$id_counters[ $prefix ] = 0;
	}

	$id = ++$id_counters[ $prefix ];

	return $prefix . (string) $id;
}

/**
 * Generates a unique ID based on the structure and values of a given array.
 *
 * This function serializes the array into a JSON string and generates a hash
 * that serves as a unique identifier. Optionally, a prefix can be added to
 * the generated ID for context or categorization.
 *
 * @since 6.8.0
 *
 * @param array  $data   The input array to generate an ID from.
 * @param string $prefix Optional. A prefix to prepend to the generated ID. Default empty string.
 * @return string The generated unique ID for the array.
 */
function wp_unique_id_from_values( array $data, string $prefix = '' ): string {
	if ( empty( $data ) ) {
		_doing_it_wrong(
			__FUNCTION__,
			sprintf(
				/* translators: %s: The parameter name. */
				__( 'The %s parameter must not be empty.' ),
				'$data'
			),
			'6.8.0'
		);
	}

	$serialized = wp_json_encode( $data );
	$hash       = substr( md5( $serialized ), 0, 8 );

	return $prefix . $hash;
}

/**
 * Gets last changed date for the specified cache group.
 *
 * @since 4.7.0
 *
 * @param string $group Where the cache contents are grouped.
 * @return string UNIX timestamp with microseconds representing when the group was last changed.
 */
function wp_cache_get_last_changed( $group ) {
	$last_changed = wp_cache_get( 'last_changed', $group );

	if ( $last_changed ) {
		return $last_changed;
	}

	return wp_cache_set_last_changed( $group );
}

/**
 * Sets last changed date for the specified cache group to now.
 *
 * @since 6.3.0
 *
 * @param string $group Where the cache contents are grouped.
 * @return string UNIX timestamp when the group was last changed.
 */
function wp_cache_set_last_changed( $group ) {
	$previous_time = wp_cache_get( 'last_changed', $group );

	$time = microtime();

	wp_cache_set( 'last_changed', $time, $group );

	/**
	 * Fires after a cache group `last_changed` time is updated.
	 * This may occur multiple times per page load and registered
	 * actions must be performant.
	 *
	 * @since 6.3.0
	 *
	 * @param string       $group         The cache group name.
	 * @param string       $time          The new last changed time (msec sec).
	 * @param string|false $previous_time The previous last changed time. False if not previously set.
	 */
	do_action( 'wp_cache_set_last_changed', $group, $time, $previous_time );

	return $time;
}

/**
 * Sends an email to the old site admin email address when the site admin email address changes.
 *
 * @since 4.9.0
 *
 * @param string $old_email   The old site admin email address.
 * @param string $new_email   The new site admin email address.
 * @param string $option_name The relevant database option name.
 */
function wp_site_admin_email_change_notification( $old_email, $new_email, $option_name ) {
	$send = true;

	// Don't send the notification for an empty email address or the default 'admin_email' value.
	if ( empty( $old_email ) || 'you@example.com' === $old_email ) {
		$send = false;
	}

	/**
	 * Filters whether to send the site admin email change notification email.
	 *
	 * @since 4.9.0
	 *
	 * @param bool   $send      Whether to send the email notification.
	 * @param string $old_email The old site admin email address.
	 * @param string $new_email The new site admin email address.
	 */
	$send = apply_filters( 'send_site_admin_email_change_email', $send, $old_email, $new_email );

	if ( ! $send ) {
		return;
	}

	/* translators: Do not translate OLD_EMAIL, NEW_EMAIL, SITENAME, SITEURL: those are placeholders. */
	$email_change_text = __(
		'Hi,

This notice confirms that the admin email address was changed on ###SITENAME###.

The new admin email address is ###NEW_EMAIL###.

This email has been sent to ###OLD_EMAIL###

Regards,
All at ###SITENAME###
###SITEURL###'
	);

	$email_change_email = array(
		'to'      => $old_email,
		/* translators: Site admin email change notification email subject. %s: Site title. */
		'subject' => __( '[%s] Admin Email Changed' ),
		'message' => $email_change_text,
		'headers' => '',
	);

	// Get site name.
	$site_name = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );

	/**
	 * Filters the contents of the email notification sent when the site admin email address is changed.
	 *
	 * @since 4.9.0
	 *
	 * @param array $email_change_email {
	 *     Used to build wp_mail().
	 *
	 *     @type string $to      The intended recipient.
	 *     @type string $subject The subject of the email.
	 *     @type string $message The content of the email.
	 *         The following strings have a special meaning and will get replaced dynamically:
	 *          - `###OLD_EMAIL###` The old site admin email address.
	 *          - `###NEW_EMAIL###` The new site admin email address.
	 *          - `###SITENAME###`  The name of the site.
	 *          - `###SITEURL###`   The URL to the site.
	 *     @type string $headers Headers.
	 * }
	 * @param string $old_email The old site admin email address.
	 * @param string $new_email The new site admin email address.
	 */
	$email_change_email = apply_filters( 'site_admin_email_change_email', $email_change_email, $old_email, $new_email );

	$email_change_email['message'] = str_replace( '###OLD_EMAIL###', $old_email, $email_change_email['message'] );
	$email_change_email['message'] = str_replace( '###NEW_EMAIL###', $new_email, $email_change_email['message'] );
	$email_change_email['message'] = str_replace( '###SITENAME###', $site_name, $email_change_email['message'] );
	$email_change_email['message'] = str_replace( '###SITEURL###', home_url(), $email_change_email['message'] );

	wp_mail(
		$email_change_email['to'],
		sprintf(
			$email_change_email['subject'],
			$site_name
		),
		$email_change_email['message'],
		$email_change_email['headers']
	);
}

/**
 * Returns an anonymized IPv4 or IPv6 address.
 *
 * @since 4.9.6 Abstracted from `WP_Community_Events::get_unsafe_client_ip()`.
 *
 * @param string $ip_addr       The IPv4 or IPv6 address to be anonymized.
 * @param bool   $ipv6_fallback Optional. Whether to return the original IPv6 address if the needed functions
 *                              to anonymize it are not present. Default false, return `::` (unspecified address).
 * @return string  The anonymized IP address.
 */
function wp_privacy_anonymize_ip( $ip_addr, $ipv6_fallback = false ) {
	if ( empty( $ip_addr ) ) {
		return '0.0.0.0';
	}

	// Detect what kind of IP address this is.
	$ip_prefix = '';
	$is_ipv6   = substr_count( $ip_addr, ':' ) > 1;
	$is_ipv4   = ( 3 === substr_count( $ip_addr, '.' ) );

	if ( $is_ipv6 && $is_ipv4 ) {
		// IPv6 compatibility mode, temporarily strip the IPv6 part, and treat it like IPv4.
		$ip_prefix = '::ffff:';
		$ip_addr   = preg_replace( '/^\[?[0-9a-f:]*:/i', '', $ip_addr );
		$ip_addr   = str_replace( ']', '', $ip_addr );
		$is_ipv6   = false;
	}

	if ( $is_ipv6 ) {
		// IPv6 addresses will always be enclosed in [] if there's a port.
		$left_bracket  = strpos( $ip_addr, '[' );
		$right_bracket = strpos( $ip_addr, ']' );
		$percent       = strpos( $ip_addr, '%' );
		$netmask       = 'ffff:ffff:ffff:ffff:0000:0000:0000:0000';

		// Strip the port (and [] from IPv6 addresses), if they exist.
		if ( false !== $left_bracket && false !== $right_bracket ) {
			$ip_addr = substr( $ip_addr, $left_bracket + 1, $right_bracket - $left_bracket - 1 );
		} elseif ( false !== $left_bracket || false !== $right_bracket ) {
			// The IP has one bracket, but not both, so it's malformed.
			return '::';
		}

		// Strip the reachability scope.
		if ( false !== $percent ) {
			$ip_addr = substr( $ip_addr, 0, $percent );
		}

		// No invalid characters should be left.
		if ( preg_match( '/[^0-9a-f:]/i', $ip_addr ) ) {
			return '::';
		}

		// Partially anonymize the IP by reducing it to the corresponding network ID.
		if ( function_exists( 'inet_pton' ) && function_exists( 'inet_ntop' ) ) {
			$ip_addr = inet_ntop( inet_pton( $ip_addr ) & inet_pton( $netmask ) );
			if ( false === $ip_addr ) {
				return '::';
			}
		} elseif ( ! $ipv6_fallback ) {
			return '::';
		}
	} elseif ( $is_ipv4 ) {
		// Strip any port and partially anonymize the IP.
		$last_octet_position = strrpos( $ip_addr, '.' );
		$ip_addr             = substr( $ip_addr, 0, $last_octet_position ) . '.0';
	} else {
		return '0.0.0.0';
	}

	// Restore the IPv6 prefix to compatibility mode addresses.
	return $ip_prefix . $ip_addr;
}

/**
 * Returns uniform "anonymous" data by type.
 *
 * @since 4.9.6
 *
 * @param string $type The type of data to be anonymized.
 * @param string $data Optional. The data to be anonymized. Default empty string.
 * @return string The anonymous data for the requested type.
 */
function wp_privacy_anonymize_data( $type, $data = '' ) {

	switch ( $type ) {
		case 'email':
			$anonymous = 'deleted@site.invalid';
			break;
		case 'url':
			$anonymous = 'https://site.invalid';
			break;
		case 'ip':
			$anonymous = wp_privacy_anonymize_ip( $data );
			break;
		case 'date':
			$anonymous = '0000-00-00 00:00:00';
			break;
		case 'text':
			/* translators: Deleted text. */
			$anonymous = __( '[deleted]' );
			break;
		case 'longtext':
			/* translators: Deleted long text. */
			$anonymous = __( 'This content was deleted by the author.' );
			break;
		default:
			$anonymous = '';
			break;
	}

	/**
	 * Filters the anonymous data for each type.
	 *
	 * @since 4.9.6
	 *
	 * @param string $anonymous Anonymized data.
	 * @param string $type      Type of the data.
	 * @param string $data      Original data.
	 */
	return apply_filters( 'wp_privacy_anonymize_data', $anonymous, $type, $data );
}

/**
 * Returns the directory used to store personal data export files.
 *
 * @since 4.9.6
 *
 * @see wp_privacy_exports_url
 *
 * @return string Exports directory.
 */
function wp_privacy_exports_dir() {
	$upload_dir  = wp_upload_dir();
	$exports_dir = trailingslashit( $upload_dir['basedir'] ) . 'wp-personal-data-exports/';

	/**
	 * Filters the directory used to store personal data export files.
	 *
	 * @since 4.9.6
	 * @since 5.5.0 Exports now use relative paths, so changes to the directory
	 *              via this filter should be reflected on the server.
	 *
	 * @param string $exports_dir Exports directory.
	 */
	return apply_filters( 'wp_privacy_exports_dir', $exports_dir );
}

/**
 * Returns the URL of the directory used to store personal data export files.
 *
 * @since 4.9.6
 *
 * @see wp_privacy_exports_dir
 *
 * @return string Exports directory URL.
 */
function wp_privacy_exports_url() {
	$upload_dir  = wp_upload_dir();
	$exports_url = trailingslashit( $upload_dir['baseurl'] ) . 'wp-personal-data-exports/';

	/**
	 * Filters the URL of the directory used to store personal data export files.
	 *
	 * @since 4.9.6
	 * @since 5.5.0 Exports now use relative paths, so changes to the directory URL
	 *              via this filter should be reflected on the server.
	 *
	 * @param string $exports_url Exports directory URL.
	 */
	return apply_filters( 'wp_privacy_exports_url', $exports_url );
}

/**
 * Schedules a `WP_Cron` job to delete expired export files.
 *
 * @since 4.9.6
 */
function wp_schedule_delete_old_privacy_export_files() {
	if ( wp_installing() ) {
		return;
	}

	if ( ! wp_next_scheduled( 'wp_privacy_delete_old_export_files' ) ) {
		wp_schedule_event( time(), 'hourly', 'wp_privacy_delete_old_export_files' );
	}
}

/**
 * Cleans up export files older than three days old.
 *
 * The export files are stored in `wp-content/uploads`, and are therefore publicly
 * accessible. A CSPRN is appended to the filename to mitigate the risk of an
 * unauthorized person downloading the file, but it is still possible. Deleting
 * the file after the data subject has had a chance to delete it adds an additional
 * layer of protection.
 *
 * @since 4.9.6
 */
function wp_privacy_delete_old_export_files() {
	$exports_dir = wp_privacy_exports_dir();
	if ( ! is_dir( $exports_dir ) ) {
		return;
	}

	require_once ABSPATH . 'wp-admin/includes/file.php';
	$export_files = list_files( $exports_dir, 100, array( 'index.php' ) );

	/**
	 * Filters the lifetime, in seconds, of a personal data export file.
	 *
	 * By default, the lifetime is 3 days. Once the file reaches that age, it will automatically
	 * be deleted by a cron job.
	 *
	 * @since 4.9.6
	 *
	 * @param int $expiration The expiration age of the export, in seconds.
	 */
	$expiration = apply_filters( 'wp_privacy_export_expiration', 3 * DAY_IN_SECONDS );

	foreach ( (array) $export_files as $export_file ) {
		$file_age_in_seconds = time() - filemtime( $export_file );

		if ( $expiration < $file_age_in_seconds ) {
			unlink( $export_file );
		}
	}
}

/**
 * Gets the URL to learn more about updating the PHP version the site is running on.
 *
 * This URL can be overridden by specifying an environment variable `WP_UPDATE_PHP_URL` or by using the
 * {@see 'wp_update_php_url'} filter. Providing an empty string is not allowed and will result in the
 * default URL being used. Furthermore the page the URL links to should preferably be localized in the
 * site language.
 *
 * @since 5.1.0
 *
 * @return string URL to learn more about updating PHP.
 */
function wp_get_update_php_url() {
	$default_url = wp_get_default_update_php_url();

	$update_url = $default_url;
	if ( false !== getenv( 'WP_UPDATE_PHP_URL' ) ) {
		$update_url = getenv( 'WP_UPDATE_PHP_URL' );
	}

	/**
	 * Filters the URL to learn more about updating the PHP version the site is running on.
	 *
	 * Providing an empty string is not allowed and will result in the default URL being used. Furthermore
	 * the page the URL links to should preferably be localized in the site language.
	 *
	 * @since 5.1.0
	 *
	 * @param string $update_url URL to learn more about updating PHP.
	 */
	$update_url = apply_filters( 'wp_update_php_url', $update_url );

	if ( empty( $update_url ) ) {
		$update_url = $default_url;
	}

	return $update_url;
}

/**
 * Gets the default URL to learn more about updating the PHP version the site is running on.
 *
 * Do not use this function to retrieve this URL. Instead, use {@see wp_get_update_php_url()} when relying on the URL.
 * This function does not allow modifying the returned URL, and is only used to compare the actually used URL with the
 * default one.
 *
 * @since 5.1.0
 * @access private
 *
 * @return string Default URL to learn more about updating PHP.
 */
function wp_get_default_update_php_url() {
	return _x( 'https://wordpress.org/support/update-php/', 'localized PHP upgrade information page' );
}

/**
 * Prints the default annotation for the web host altering the "Update PHP" page URL.
 *
 * This function is to be used after {@see wp_get_update_php_url()} to display a consistent
 * annotation if the web host has altered the default "Update PHP" page URL.
 *
 * @since 5.1.0
 * @since 5.2.0 Added the `$before` and `$after` parameters.
 * @since 6.4.0 Added the `$display` parameter.
 *
 * @param string $before  Markup to output before the annotation. Default `<p class="description">`.
 * @param string $after   Markup to output after the annotation. Default `</p>`.
 * @param bool   $display Whether to echo or return the markup. Default `true` for echo.
 *
 * @return string|void
 */
function wp_update_php_annotation( $before = '<p class="description">', $after = '</p>', $display = true ) {
	$annotation = wp_get_update_php_annotation();

	if ( $annotation ) {
		if ( $display ) {
			echo $before . $annotation . $after;
		} else {
			return $before . $annotation . $after;
		}
	}
}

/**
 * Returns the default annotation for the web hosting altering the "Update PHP" page URL.
 *
 * This function is to be used after {@see wp_get_update_php_url()} to return a consistent
 * annotation if the web host has altered the default "Update PHP" page URL.
 *
 * @since 5.2.0
 *
 * @return string Update PHP page annotation. An empty string if no custom URLs are provided.
 */
function wp_get_update_php_annotation() {
	$update_url  = wp_get_update_php_url();
	$default_url = wp_get_default_update_php_url();

	if ( $update_url === $default_url ) {
		return '';
	}

	$annotation = sprintf(
		/* translators: %s: Default Update PHP page URL. */
		__( 'This resource is provided by your web host, and is specific to your site. For more information, <a href="%s" target="_blank">see the official WordPress documentation</a>.' ),
		esc_url( $default_url )
	);

	return $annotation;
}

/**
 * Gets the URL for directly updating the PHP version the site is running on.
 *
 * A URL will only be returned if the `WP_DIRECT_UPDATE_PHP_URL` environment variable is specified or
 * by using the {@see 'wp_direct_php_update_url'} filter. This allows hosts to send users directly to
 * the page where they can update PHP to a newer version.
 *
 * @since 5.1.1
 *
 * @return string URL for directly updating PHP or empty string.
 */
function wp_get_direct_php_update_url() {
	$direct_update_url = '';

	if ( false !== getenv( 'WP_DIRECT_UPDATE_PHP_URL' ) ) {
		$direct_update_url = getenv( 'WP_DIRECT_UPDATE_PHP_URL' );
	}

	/**
	 * Filters the URL for directly updating the PHP version the site is running on from the host.
	 *
	 * @since 5.1.1
	 *
	 * @param string $direct_update_url URL for directly updating PHP.
	 */
	$direct_update_url = apply_filters( 'wp_direct_php_update_url', $direct_update_url );

	return $direct_update_url;
}

/**
 * Displays a button directly linking to a PHP update process.
 *
 * This provides hosts with a way for users to be sent directly to their PHP update process.
 *
 * The button is only displayed if a URL is returned by `wp_get_direct_php_update_url()`.
 *
 * @since 5.1.1
 */
function wp_direct_php_update_button() {
	$direct_update_url = wp_get_direct_php_update_url();

	if ( empty( $direct_update_url ) ) {
		return;
	}

	echo '<p class="button-container">';
	printf(
		'<a class="button button-primary" href="%1$s" target="_blank">%2$s<span class="screen-reader-text"> %3$s</span><span aria-hidden="true" class="dashicons dashicons-external"></span></a>',
		esc_url( $direct_update_url ),
		__( 'Update PHP' ),
		/* translators: Hidden accessibility text. */
		__( '(opens in a new tab)' )
	);
	echo '</p>';
}

/**
 * Gets the URL to learn more about updating the site to use HTTPS.
 *
 * This URL can be overridden by specifying an environment variable `WP_UPDATE_HTTPS_URL` or by using the
 * {@see 'wp_update_https_url'} filter. Providing an empty string is not allowed and will result in the
 * default URL being used. Furthermore the page the URL links to should preferably be localized in the
 * site language.
 *
 * @since 5.7.0
 *
 * @return string URL to learn more about updating to HTTPS.
 */
function wp_get_update_https_url() {
	$default_url = wp_get_default_update_https_url();

	$update_url = $default_url;
	if ( false !== getenv( 'WP_UPDATE_HTTPS_URL' ) ) {
		$update_url = getenv( 'WP_UPDATE_HTTPS_URL' );
	}

	/**
	 * Filters the URL to learn more about updating the HTTPS version the site is running on.
	 *
	 * Providing an empty string is not allowed and will result in the default URL being used. Furthermore
	 * the page the URL links to should preferably be localized in the site language.
	 *
	 * @since 5.7.0
	 *
	 * @param string $update_url URL to learn more about updating HTTPS.
	 */
	$update_url = apply_filters( 'wp_update_https_url', $update_url );
	if ( empty( $update_url ) ) {
		$update_url = $default_url;
	}

	return $update_url;
}

/**
 * Gets the default URL to learn more about updating the site to use HTTPS.
 *
 * Do not use this function to retrieve this URL. Instead, use {@see wp_get_update_https_url()} when relying on the URL.
 * This function does not allow modifying the returned URL, and is only used to compare the actually used URL with the
 * default one.
 *
 * @since 5.7.0
 * @access private
 *
 * @return string Default URL to learn more about updating to HTTPS.
 */
function wp_get_default_update_https_url() {
	/* translators: Documentation explaining HTTPS and why it should be used. */
	return __( 'https://developer.wordpress.org/advanced-administration/security/https/' );
}

/**
 * Gets the URL for directly updating the site to use HTTPS.
 *
 * A URL will only be returned if the `WP_DIRECT_UPDATE_HTTPS_URL` environment variable is specified or
 * by using the {@see 'wp_direct_update_https_url'} filter. This allows hosts to send users directly to
 * the page where they can update their site to use HTTPS.
 *
 * @since 5.7.0
 *
 * @return string URL for directly updating to HTTPS or empty string.
 */
function wp_get_direct_update_https_url() {
	$direct_update_url = '';

	if ( false !== getenv( 'WP_DIRECT_UPDATE_HTTPS_URL' ) ) {
		$direct_update_url = getenv( 'WP_DIRECT_UPDATE_HTTPS_URL' );
	}

	/**
	 * Filters the URL for directly updating the PHP version the site is running on from the host.
	 *
	 * @since 5.7.0
	 *
	 * @param string $direct_update_url URL for directly updating PHP.
	 */
	$direct_update_url = apply_filters( 'wp_direct_update_https_url', $direct_update_url );

	return $direct_update_url;
}

/**
 * Gets the size of a directory.
 *
 * A helper function that is used primarily to check whether
 * a blog has exceeded its allowed upload space.
 *
 * @since MU (3.0.0)
 * @since 5.2.0 $max_execution_time parameter added.
 *
 * @param string $directory Full path of a directory.
 * @param int    $max_execution_time Maximum time to run before giving up. In seconds.
 *                                   The timeout is global and is measured from the moment WordPress started to load.
 * @return int|false|null Size in bytes if a valid directory. False if not. Null if timeout.
 */
function get_dirsize( $directory, $max_execution_time = null ) {

	/*
	 * Exclude individual site directories from the total when checking the main site of a network,
	 * as they are subdirectories and should not be counted.
	 */
	if ( is_multisite() && is_main_site() ) {
		$size = recurse_dirsize( $directory, $directory . '/sites', $max_execution_time );
	} else {
		$size = recurse_dirsize( $directory, null, $max_execution_time );
	}

	return $size;
}

/**
 * Gets the size of a directory recursively.
 *
 * Used by get_dirsize() to get a directory size when it contains other directories.
 *
 * @since MU (3.0.0)
 * @since 4.3.0 The `$exclude` parameter was added.
 * @since 5.2.0 The `$max_execution_time` parameter was added.
 * @since 5.6.0 The `$directory_cache` parameter was added.
 *
 * @param string          $directory          Full path of a directory.
 * @param string|string[] $exclude            Optional. Full path of a subdirectory to exclude from the total,
 *                                            or array of paths. Expected without trailing slash(es).
 *                                            Default null.
 * @param int             $max_execution_time Optional. Maximum time to run before giving up. In seconds.
 *                                            The timeout is global and is measured from the moment
 *                                            WordPress started to load. Defaults to the value of
 *                                            `max_execution_time` PHP setting.
 * @param array           $directory_cache    Optional. Array of cached directory paths.
 *                                            Defaults to the value of `dirsize_cache` transient.
 * @return int|false|null Size in bytes if a valid directory. False if not. Null if timeout.
 */
function recurse_dirsize( $directory, $exclude = null, $max_execution_time = null, &$directory_cache = null ) {
	$directory  = untrailingslashit( $directory );
	$save_cache = false;

	if ( ! isset( $directory_cache ) ) {
		$directory_cache = get_transient( 'dirsize_cache' );
		$save_cache      = true;
	}

	if ( isset( $directory_cache[ $directory ] ) && is_int( $directory_cache[ $directory ] ) ) {
		return $directory_cache[ $directory ];
	}

	if ( ! file_exists( $directory ) || ! is_dir( $directory ) || ! is_readable( $directory ) ) {
		return false;
	}

	if (
		( is_string( $exclude ) && $directory === $exclude ) ||
		( is_array( $exclude ) && in_array( $directory, $exclude, true ) )
	) {
		return false;
	}

	if ( null === $max_execution_time ) {
		// Keep the previous behavior but attempt to prevent fatal errors from timeout if possible.
		if ( function_exists( 'ini_get' ) ) {
			$max_execution_time = ini_get( 'max_execution_time' );
		} else {
			// Disable...
			$max_execution_time = 0;
		}

		// Leave 1 second "buffer" for other operations if $max_execution_time has reasonable value.
		if ( $max_execution_time > 10 ) {
			$max_execution_time -= 1;
		}
	}

	/**
	 * Filters the amount of storage space used by one directory and all its children, in megabytes.
	 *
	 * Return the actual used space to short-circuit the recursive PHP file size calculation
	 * and use something else, like a CDN API or native operating system tools for better performance.
	 *
	 * @since 5.6.0
	 *
	 * @param int|false            $space_used         The amount of used space, in bytes. Default false.
	 * @param string               $directory          Full path of a directory.
	 * @param string|string[]|null $exclude            Full path of a subdirectory to exclude from the total,
	 *                                                 or array of paths.
	 * @param int                  $max_execution_time Maximum time to run before giving up. In seconds.
	 * @param array                $directory_cache    Array of cached directory paths.
	 */
	$size = apply_filters( 'pre_recurse_dirsize', false, $directory, $exclude, $max_execution_time, $directory_cache );

	if ( false === $size ) {
		$size = 0;

		$handle = opendir( $directory );
		if ( $handle ) {
			while ( ( $file = readdir( $handle ) ) !== false ) {
				$path = $directory . '/' . $file;
				if ( '.' !== $file && '..' !== $file ) {
					if ( is_file( $path ) ) {
						$size += filesize( $path );
					} elseif ( is_dir( $path ) ) {
						$handlesize = recurse_dirsize( $path, $exclude, $max_execution_time, $directory_cache );
						if ( $handlesize > 0 ) {
							$size += $handlesize;
						}
					}

					if ( $max_execution_time > 0 &&
						( microtime( true ) - WP_START_TIMESTAMP ) > $max_execution_time
					) {
						// Time exceeded. Give up instead of risking a fatal timeout.
						$size = null;
						break;
					}
				}
			}
			closedir( $handle );
		}
	}

	if ( ! is_array( $directory_cache ) ) {
		$directory_cache = array();
	}

	$directory_cache[ $directory ] = $size;

	// Only write the transient on the top level call and not on recursive calls.
	if ( $save_cache ) {
		$expiration = ( wp_using_ext_object_cache() ) ? 0 : 10 * YEAR_IN_SECONDS;
		set_transient( 'dirsize_cache', $directory_cache, $expiration );
	}

	return $size;
}

/**
 * Cleans directory size cache used by recurse_dirsize().
 *
 * Removes the current directory and all parent directories from the `dirsize_cache` transient.
 *
 * @since 5.6.0
 * @since 5.9.0 Added input validation with a notice for invalid input.
 *
 * @param string $path Full path of a directory or file.
 */
function clean_dirsize_cache( $path ) {
	if ( ! is_string( $path ) || empty( $path ) ) {
		wp_trigger_error(
			'',
			sprintf(
				/* translators: 1: Function name, 2: A variable type, like "boolean" or "integer". */
				__( '%1$s only accepts a non-empty path string, received %2$s.' ),
				'<code>clean_dirsize_cache()</code>',
				'<code>' . gettype( $path ) . '</code>'
			)
		);
		return;
	}

	$directory_cache = get_transient( 'dirsize_cache' );

	if ( empty( $directory_cache ) ) {
		return;
	}

	$expiration = ( wp_using_ext_object_cache() ) ? 0 : 10 * YEAR_IN_SECONDS;
	if (
		! str_contains( $path, '/' ) &&
		! str_contains( $path, '\\' )
	) {
		unset( $directory_cache[ $path ] );
		set_transient( 'dirsize_cache', $directory_cache, $expiration );
		return;
	}

	$last_path = null;
	$path      = untrailingslashit( $path );
	unset( $directory_cache[ $path ] );

	while (
		$last_path !== $path &&
		DIRECTORY_SEPARATOR !== $path &&
		'.' !== $path &&
		'..' !== $path
	) {
		$last_path = $path;
		$path      = dirname( $path );
		unset( $directory_cache[ $path ] );
	}

	set_transient( 'dirsize_cache', $directory_cache, $expiration );
}

/**
 * Returns the current WordPress version.
 *
 * Returns an unmodified value of `$wp_version`. Some plugins modify the global
 * in an attempt to improve security through obscurity. This practice can cause
 * errors in WordPress, so the ability to get an unmodified version is needed.
 *
 * @since 6.7.0
 *
 * @return string The current WordPress version.
 */
function wp_get_wp_version() {
	static $wp_version;

	if ( ! isset( $wp_version ) ) {
		require ABSPATH . WPINC . '/version.php';
	}

	return $wp_version;
}

/**
 * Checks compatibility with the current WordPress version.
 *
 * @since 5.2.0
 *
 * @global string $_wp_tests_wp_version The WordPress version string. Used only in Core tests.
 *
 * @param string $required Minimum required WordPress version.
 * @return bool True if required version is compatible or empty, false if not.
 */
function is_wp_version_compatible( $required ) {
	if (
		defined( 'WP_RUN_CORE_TESTS' )
		&& WP_RUN_CORE_TESTS
		&& isset( $GLOBALS['_wp_tests_wp_version'] )
	) {
		$wp_version = $GLOBALS['_wp_tests_wp_version'];
	} else {
		$wp_version = wp_get_wp_version();
	}

	// Strip off any -alpha, -RC, -beta, -src suffixes.
	list( $version ) = explode( '-', $wp_version );

	if ( is_string( $required ) ) {
		$trimmed = trim( $required );

		if ( substr_count( $trimmed, '.' ) > 1 && str_ends_with( $trimmed, '.0' ) ) {
			$required = substr( $trimmed, 0, -2 );
		}
	}

	return empty( $required ) || version_compare( $version, $required, '>=' );
}

/**
 * Checks compatibility with the current PHP version.
 *
 * @since 5.2.0
 *
 * @param string $required Minimum required PHP version.
 * @return bool True if required version is compatible or empty, false if not.
 */
function is_php_version_compatible( $required ) {
	return empty( $required ) || version_compare( PHP_VERSION, $required, '>=' );
}

/**
 * Checks if two numbers are nearly the same.
 *
 * This is similar to using `round()` but the precision is more fine-grained.
 *
 * @since 5.3.0
 *
 * @param int|float $expected  The expected value.
 * @param int|float $actual    The actual number.
 * @param int|float $precision Optional. The allowed variation. Default 1.
 * @return bool Whether the numbers match within the specified precision.
 */
function wp_fuzzy_number_match( $expected, $actual, $precision = 1 ) {
	return abs( (float) $expected - (float) $actual ) <= $precision;
}

/**
 * Creates and returns the markup for an admin notice.
 *
 * @since 6.4.0
 *
 * @param string $message The message.
 * @param array  $args {
 *     Optional. An array of arguments for the admin notice. Default empty array.
 *
 *     @type string   $type               Optional. The type of admin notice.
 *                                        For example, 'error', 'success', 'warning', 'info'.
 *                                        Default empty string.
 *     @type bool     $dismissible        Optional. Whether the admin notice is dismissible. Default false.
 *     @type string   $id                 Optional. The value of the admin notice's ID attribute. Default empty string.
 *     @type string[] $additional_classes Optional. A string array of class names. Default empty array.
 *     @type string[] $attributes         Optional. Additional attributes for the notice div. Default empty array.
 *     @type bool     $paragraph_wrap     Optional. Whether to wrap the message in paragraph tags. Default true.
 * }
 * @return string The markup for an admin notice.
 */
function wp_get_admin_notice( $message, $args = array() ) {
	$defaults = array(
		'type'               => '',
		'dismissible'        => false,
		'id'                 => '',
		'additional_classes' => array(),
		'attributes'         => array(),
		'paragraph_wrap'     => true,
	);

	$args = wp_parse_args( $args, $defaults );

	/**
	 * Filters the arguments for an admin notice.
	 *
	 * @since 6.4.0
	 *
	 * @param array  $args    The arguments for the admin notice.
	 * @param string $message The message for the admin notice.
	 */
	$args       = apply_filters( 'wp_admin_notice_args', $args, $message );
	$id         = '';
	$classes    = 'notice';
	$attributes = '';

	if ( is_string( $args['id'] ) ) {
		$trimmed_id = trim( $args['id'] );

		if ( '' !== $trimmed_id ) {
			$id = 'id="' . $trimmed_id . '" ';
		}
	}

	if ( is_string( $args['type'] ) ) {
		$type = trim( $args['type'] );

		if ( str_contains( $type, ' ' ) ) {
			_doing_it_wrong(
				__FUNCTION__,
				sprintf(
					/* translators: %s: The "type" key. */
					__( 'The %s key must be a string without spaces.' ),
					'<code>type</code>'
				),
				'6.4.0'
			);
		}

		if ( '' !== $type ) {
			$classes .= ' notice-' . $type;
		}
	}

	if ( true === $args['dismissible'] ) {
		$classes .= ' is-dismissible';
	}

	if ( is_array( $args['additional_classes'] ) && ! empty( $args['additional_classes'] ) ) {
		$classes .= ' ' . implode( ' ', $args['additional_classes'] );
	}

	if ( is_array( $args['attributes'] ) && ! empty( $args['attributes'] ) ) {
		$attributes = '';
		foreach ( $args['attributes'] as $attr => $val ) {
			if ( is_bool( $val ) ) {
				$attributes .= $val ? ' ' . $attr : '';
			} elseif ( is_int( $attr ) ) {
				$attributes .= ' ' . esc_attr( trim( $val ) );
			} elseif ( $val ) {
				$attributes .= ' ' . $attr . '="' . esc_attr( trim( $val ) ) . '"';
			}
		}
	}

	if ( false !== $args['paragraph_wrap'] ) {
		$message = "<p>$message</p>";
	}

	$markup = sprintf( '<div %1$sclass="%2$s"%3$s>%4$s</div>', $id, $classes, $attributes, $message );

	/**
	 * Filters the markup for an admin notice.
	 *
	 * @since 6.4.0
	 *
	 * @param string $markup  The HTML markup for the admin notice.
	 * @param string $message The message for the admin notice.
	 * @param array  $args    The arguments for the admin notice.
	 */
	return apply_filters( 'wp_admin_notice_markup', $markup, $message, $args );
}

/**
 * Outputs an admin notice.
 *
 * @since 6.4.0
 *
 * @param string $message The message to output.
 * @param array  $args {
 *     Optional. An array of arguments for the admin notice. Default empty array.
 *
 *     @type string   $type               Optional. The type of admin notice.
 *                                        For example, 'error', 'success', 'warning', 'info'.
 *                                        Default empty string.
 *     @type bool     $dismissible        Optional. Whether the admin notice is dismissible. Default false.
 *     @type string   $id                 Optional. The value of the admin notice's ID attribute. Default empty string.
 *     @type string[] $additional_classes Optional. A string array of class names. Default empty array.
 *     @type string[] $attributes         Optional. Additional attributes for the notice div. Default empty array.
 *     @type bool     $paragraph_wrap     Optional. Whether to wrap the message in paragraph tags. Default true.
 * }
 */
function wp_admin_notice( $message, $args = array() ) {
	/**
	 * Fires before an admin notice is output.
	 *
	 * @since 6.4.0
	 *
	 * @param string $message The message for the admin notice.
	 * @param array  $args    The arguments for the admin notice.
	 */
	do_action( 'wp_admin_notice', $message, $args );

	echo wp_kses_post( wp_get_admin_notice( $message, $args ) );
}

/**
 * Checks if a mime type is for a HEIC/HEIF image.
 *
 * @since 6.7.0
 *
 * @param string $mime_type The mime type to check.
 * @return bool Whether the mime type is for a HEIC/HEIF image.
 */
function wp_is_heic_image_mime_type( $mime_type ) {
	$heic_mime_types = array(
		'image/heic',
		'image/heif',
		'image/heic-sequence',
		'image/heif-sequence',
	);

	return in_array( $mime_type, $heic_mime_types, true );
}

/**
 * Returns a cryptographically secure hash of a message using a fast generic hash function.
 *
 * Use the wp_verify_fast_hash() function to verify the hash.
 *
 * This function does not salt the value prior to being hashed, therefore input to this function must originate from
 * a random generator with sufficiently high entropy, preferably greater than 128 bits. This function is used internally
 * in WordPress to hash security keys and application passwords which are generated with high entropy.
 *
 * Important:
 *
 *  - This function must not be used for hashing user-generated passwords. Use wp_hash_password() for that.
 *  - This function must not be used for hashing other low-entropy input. Use wp_hash() for that.
 *
 * The BLAKE2b algorithm is used by Sodium to hash the message.
 *
 * @since 6.8.0
 *
 * @throws TypeError Thrown by Sodium if the message is not a string.
 *
 * @param string $message The message to hash.
 * @return string The hash of the message.
 */
function wp_fast_hash(
	#[\SensitiveParameter]
	string $message
): string {
	$hashed = sodium_crypto_generichash( $message, 'wp_fast_hash_6.8+', 30 );
	return '$generic$' . sodium_bin2base64( $hashed, SODIUM_BASE64_VARIANT_URLSAFE_NO_PADDING );
}

/**
 * Checks whether a plaintext message matches the hashed value. Used to verify values hashed via wp_fast_hash().
 *
 * The function uses Sodium to hash the message and compare it to the hashed value. If the hash is not a generic hash,
 * the hash is treated as a phpass portable hash in order to provide backward compatibility for passwords and security
 * keys which were hashed using phpass prior to WordPress 6.8.0.
 *
 * @since 6.8.0
 *
 * @throws TypeError Thrown by Sodium if the message is not a string.
 *
 * @param string $message The plaintext message.
 * @param string $hash    Hash of the message to check against.
 * @return bool Whether the message matches the hashed message.
 */
function wp_verify_fast_hash(
	#[\SensitiveParameter]
	string $message,
	string $hash
): bool {
	if ( ! str_starts_with( $hash, '$generic$' ) ) {
		// Back-compat for old phpass hashes.
		require_once ABSPATH . WPINC . '/class-phpass.php';
		return ( new PasswordHash( 8, true ) )->CheckPassword( $message, $hash );
	}

	return hash_equals( $hash, wp_fast_hash( $message ) );
}
