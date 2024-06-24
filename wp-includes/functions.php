<?php
/**
 * Main WordPress API
 *
 * @package WordPress
 */

require( ABSPATH . WPINC . '/option.php' );

/**
 * Convert given date string into a different format.
 *
 * $format should be either a PHP date format string, e.g. 'U' for a Unix
 * timestamp, or 'G' for a Unix timestamp assuming that $date is GMT.
 *
 * If $translate is true then the given date and format string will
 * be passed to date_i18n() for translation.
 *
 * @since 0.71
 *
 * @param string $format    Format of the date to return.
 * @param string $date      Date string to convert.
 * @param bool   $translate Whether the return date should be translated. Default true.
 * @return string|int|bool Formatted date string or Unix timestamp. False if $date is empty.
 */
function mysql2date( $format, $date, $translate = true ) {
	if ( empty( $date ) ) {
		return false;
	}

	if ( 'G' == $format ) {
		return strtotime( $date . ' +0000' );
	}

	$i = strtotime( $date );

	if ( 'U' == $format ) {
		return $i;
	}

	if ( $translate ) {
		return date_i18n( $format, $i );
	} else {
		return date( $format, $i );
	}
}

/**
 * Retrieve the current time based on specified type.
 *
 * The 'mysql' type will return the time in the format for MySQL DATETIME field.
 * The 'timestamp' type will return the current timestamp.
 * Other strings will be interpreted as PHP date formats (e.g. 'Y-m-d').
 *
 * If $gmt is set to either '1' or 'true', then both types will use GMT time.
 * if $gmt is false, the output is adjusted with the GMT offset in the WordPress option.
 *
 * @since 1.0.0
 *
 * @param string   $type Type of time to retrieve. Accepts 'mysql', 'timestamp', or PHP date
 *                       format string (e.g. 'Y-m-d').
 * @param int|bool $gmt  Optional. Whether to use GMT timezone. Default false.
 * @return int|string Integer if $type is 'timestamp', string otherwise.
 */
function current_time( $type, $gmt = 0 ) {
	switch ( $type ) {
		case 'mysql':
			return ( $gmt ) ? gmdate( 'Y-m-d H:i:s' ) : gmdate( 'Y-m-d H:i:s', ( time() + ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS ) ) );
		case 'timestamp':
			return ( $gmt ) ? time() : time() + ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS );
		default:
			return ( $gmt ) ? gmdate( $type ) : gmdate( $type, time() + ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS ) );
	}
}

/**
 * Retrieve the date in localized format, based on a sum of Unix timestamp and
 * timezone offset in seconds.
 *
 * If the locale specifies the locale month and weekday, then the locale will
 * take over the format for the date. If it isn't, then the date format string
 * will be used instead.
 *
 * @since 0.71
 *
 * @global WP_Locale $wp_locale
 *
 * @param string   $dateformatstring      Format to display the date.
 * @param int|bool $timestamp_with_offset Optional. A sum of Unix timestamp and timezone offset in seconds.
 *                                        Default false.
 * @param bool     $gmt                   Optional. Whether to use GMT timezone. Only applies if timestamp is
 *                                        not provided. Default false.
 *
 * @return string The date, translated if locale specifies it.
 */
function date_i18n( $dateformatstring, $timestamp_with_offset = false, $gmt = false ) {
	global $wp_locale;
	$i = $timestamp_with_offset;

	if ( false === $i ) {
		$i = current_time( 'timestamp', $gmt );
	}

	/*
	 * Store original value for language with untypical grammars.
	 * See https://core.trac.wordpress.org/ticket/9396
	 */
	$req_format = $dateformatstring;

	$dateformatstring = preg_replace( '/(?<!\\\\)c/', DATE_W3C, $dateformatstring );
	$dateformatstring = preg_replace( '/(?<!\\\\)r/', DATE_RFC2822, $dateformatstring );

	if ( ( ! empty( $wp_locale->month ) ) && ( ! empty( $wp_locale->weekday ) ) ) {
		$datemonth            = $wp_locale->get_month( date( 'm', $i ) );
		$datemonth_abbrev     = $wp_locale->get_month_abbrev( $datemonth );
		$dateweekday          = $wp_locale->get_weekday( date( 'w', $i ) );
		$dateweekday_abbrev   = $wp_locale->get_weekday_abbrev( $dateweekday );
		$datemeridiem         = $wp_locale->get_meridiem( date( 'a', $i ) );
		$datemeridiem_capital = $wp_locale->get_meridiem( date( 'A', $i ) );
		$dateformatstring     = ' ' . $dateformatstring;
		$dateformatstring     = preg_replace( '/([^\\\])D/', "\\1" . backslashit( $dateweekday_abbrev ), $dateformatstring );
		$dateformatstring     = preg_replace( '/([^\\\])F/', "\\1" . backslashit( $datemonth ), $dateformatstring );
		$dateformatstring     = preg_replace( '/([^\\\])l/', "\\1" . backslashit( $dateweekday ), $dateformatstring );
		$dateformatstring     = preg_replace( '/([^\\\])M/', "\\1" . backslashit( $datemonth_abbrev ), $dateformatstring );
		$dateformatstring     = preg_replace( '/([^\\\])a/', "\\1" . backslashit( $datemeridiem ), $dateformatstring );
		$dateformatstring     = preg_replace( '/([^\\\])A/', "\\1" . backslashit( $datemeridiem_capital ), $dateformatstring );

		$dateformatstring = substr( $dateformatstring, 1, strlen( $dateformatstring ) - 1 );
	}
	$timezone_formats    = array( 'P', 'I', 'O', 'T', 'Z', 'e' );
	$timezone_formats_re = implode( '|', $timezone_formats );
	if ( preg_match( "/$timezone_formats_re/", $dateformatstring ) ) {
		$timezone_string = get_option( 'timezone_string' );
		if ( false === $timestamp_with_offset && $gmt ) {
			$timezone_string = 'UTC';
		}
		if ( $timezone_string ) {
			$timezone_object = timezone_open( $timezone_string );
			$date_object     = date_create( null, $timezone_object );
			foreach ( $timezone_formats as $timezone_format ) {
				if ( false !== strpos( $dateformatstring, $timezone_format ) ) {
					$formatted        = date_format( $date_object, $timezone_format );
					$dateformatstring = ' ' . $dateformatstring;
					$dateformatstring = preg_replace( "/([^\\\])$timezone_format/", "\\1" . backslashit( $formatted ), $dateformatstring );
					$dateformatstring = substr( $dateformatstring, 1, strlen( $dateformatstring ) - 1 );
				}
			}
		} else {
			$offset = get_option( 'gmt_offset' );
			foreach ( $timezone_formats as $timezone_format ) {
				if ( 'I' === $timezone_format ) {
					continue;
				}

				if ( false !== strpos( $dateformatstring, $timezone_format ) ) {
					if ( 'Z' === $timezone_format ) {
						$formatted = (string) ( $offset * HOUR_IN_SECONDS );
					} else {
						$prefix    = '';
						$hours     = (int) $offset;
						$separator = '';
						$minutes   = abs( ( $offset - $hours ) * 60 );

						if ( 'T' === $timezone_format ) {
							$prefix = 'GMT';
						} elseif ( 'e' === $timezone_format || 'P' === $timezone_format ) {
							$separator = ':';
						}

						$formatted = sprintf( '%s%+03d%s%02d', $prefix, $hours, $separator, $minutes );
					}

					$dateformatstring = ' ' . $dateformatstring;
					$dateformatstring = preg_replace( "/([^\\\])$timezone_format/", "\\1" . backslashit( $formatted ), $dateformatstring );
					$dateformatstring = substr( $dateformatstring, 1 );
				}
			}
		}
	}
	$j = @date( $dateformatstring, $i );

	/**
	 * Filters the date formatted based on the locale.
	 *
	 * @since 2.8.0
	 *
	 * @param string $j          Formatted date string.
	 * @param string $req_format Format to display the date.
	 * @param int    $i          A sum of Unix timestamp and timezone offset in seconds.
	 * @param bool   $gmt        Whether to use GMT timezone. Only applies if timestamp was
	 *                           not provided. Default false.
	 */
	$j = apply_filters( 'date_i18n', $j, $req_format, $i, $gmt );
	return $j;
}

/**
 * Determines if the date should be declined.
 *
 * If the locale specifies that month names require a genitive case in certain
 * formats (like 'j F Y'), the month name will be replaced with a correct form.
 *
 * @since 4.4.0
 *
 * @global WP_Locale $wp_locale
 *
 * @param string $date Formatted date string.
 * @return string The date, declined if locale specifies it.
 */
function wp_maybe_decline_date( $date ) {
	global $wp_locale;

	// i18n functions are not available in SHORTINIT mode
	if ( ! function_exists( '_x' ) ) {
		return $date;
	}

	/* translators: If months in your language require a genitive case,
	 * translate this to 'on'. Do not translate into your own language.
	 */
	if ( 'on' === _x( 'off', 'decline months names: on or off' ) ) {
		// Match a format like 'j F Y' or 'j. F'
		if ( @preg_match( '#^\d{1,2}\.? [^\d ]+#u', $date ) ) {
			$months          = $wp_locale->month;
			$months_genitive = $wp_locale->month_genitive;

			foreach ( $months as $key => $month ) {
				$months[ $key ] = '# ' . $month . '( |$)#u';
			}

			foreach ( $months_genitive as $key => $month ) {
				$months_genitive[ $key ] = ' ' . $month . '$1';
			}

			$date = preg_replace( $months, $months_genitive, $date );
		}
	}

	// Used for locale-specific rules
	$locale = get_locale();

	if ( 'ca' === $locale ) {
		// " de abril| de agost| de octubre..." -> " d'abril| d'agost| d'octubre..."
		$date = preg_replace( '# de ([ao])#i', " d'\\1", $date );
	}

	return $date;
}

/**
 * Convert float number to format based on the locale.
 *
 * @since 2.3.0
 *
 * @global WP_Locale $wp_locale
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
 * Convert number of bytes largest unit bytes will fit into.
 *
 * It is easier to read 1 KB than 1024 bytes and 1 MB than 1048576 bytes. Converts
 * number of bytes to human readable number by taking the number of that unit
 * that the bytes will go into it. Supports TB value.
 *
 * Please note that integers in PHP are limited to 32 bits, unless they are on
 * 64 bit architecture, then they have 64 bit size. If you need to place the
 * larger size then what PHP integer type will hold, then use a string. It will
 * be converted to a double, which should always have 64 bit length.
 *
 * Technically the correct unit names for powers of 1024 are KiB, MiB etc.
 *
 * @since 2.3.0
 *
 * @param int|string $bytes    Number of bytes. Note max integer size for integers.
 * @param int        $decimals Optional. Precision of number of decimal places. Default 0.
 * @return string|false False on failure. Number string on success.
 */
function size_format( $bytes, $decimals = 0 ) {
	$quant = array(
		'TB' => TB_IN_BYTES,
		'GB' => GB_IN_BYTES,
		'MB' => MB_IN_BYTES,
		'KB' => KB_IN_BYTES,
		'B'  => 1,
	);

	if ( 0 === $bytes ) {
		return number_format_i18n( 0, $decimals ) . ' B';
	}

	foreach ( $quant as $unit => $mag ) {
		if ( doubleval( $bytes ) >= $mag ) {
			return number_format_i18n( $bytes / $mag, $decimals ) . ' ' . $unit;
		}
	}

	return false;
}

/**
 * Convert a duration to human readable format.
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
	if ( '-' === substr( $duration, 0, 1 ) ) {
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
		/* translators: Time duration in hour or hours. */
		$human_readable_duration[] = sprintf( _n( '%s hour', '%s hours', $hour ), (int) $hour );
	}

	// Add the minute part to the string.
	if ( is_numeric( $minute ) ) {
		/* translators: Time duration in minute or minutes. */
		$human_readable_duration[] = sprintf( _n( '%s minute', '%s minutes', $minute ), (int) $minute );
	}

	// Add the second part to the string.
	if ( is_numeric( $second ) ) {
		/* translators: Time duration in second or seconds. */
		$human_readable_duration[] = sprintf( _n( '%s second', '%s seconds', $second ), (int) $second );
	}

	return implode( ', ', $human_readable_duration );
}

/**
 * Get the week start and end from the datetime or date string from MySQL.
 *
 * @since 0.71
 *
 * @param string     $mysqlstring   Date or datetime field type from MySQL.
 * @param int|string $start_of_week Optional. Start of the week as an integer. Default empty string.
 * @return array Keys are 'start' and 'end'.
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
	$weekday = date( 'w', $day );

	if ( ! is_numeric( $start_of_week ) ) {
		$start_of_week = get_option( 'start_of_week' );
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
 * Unserialize value only if it was serialized.
 *
 * @since 2.0.0
 *
 * @param string $original Maybe unserialized original, if is needed.
 * @return mixed Unserialized data can be any type.
 */
function maybe_unserialize( $original ) {
	if ( is_serialized( $original ) ) { // don't attempt to unserialize data that wasn't serialized going in
		return @unserialize( $original );
	}
	return $original;
}

/**
 * Check value to find if it was serialized.
 *
 * If $data is not an string, then returned value will always be false.
 * Serialized data is always a string.
 *
 * @since 2.0.5
 *
 * @param string $data   Value to check to see if was serialized.
 * @param bool   $strict Optional. Whether to be strict about the end of the string. Default true.
 * @return bool False if not serialized and true if it was.
 */
function is_serialized( $data, $strict = true ) {
	// if it isn't a string, it isn't serialized.
	if ( ! is_string( $data ) ) {
		return false;
	}
	$data = trim( $data );
	if ( 'N;' == $data ) {
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
			} elseif ( false === strpos( $data, '"' ) ) {
				return false;
			}
			// or else fall through
		case 'a':
		case 'O':
			return (bool) preg_match( "/^{$token}:[0-9]+:/s", $data );
		case 'b':
		case 'i':
		case 'd':
			$end = $strict ? '$' : '';
			return (bool) preg_match( "/^{$token}:[0-9.E-]+;$end/", $data );
	}
	return false;
}

/**
 * Check whether serialized data is of string type.
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
	} elseif ( ';' !== substr( $data, -1 ) ) {
		return false;
	} elseif ( $data[0] !== 's' ) {
		return false;
	} elseif ( '"' !== substr( $data, -2, 1 ) ) {
		return false;
	} else {
		return true;
	}
}

/**
 * Serialize data, if needed.
 *
 * @since 2.0.5
 *
 * @param string|array|object $data Data that might be serialized.
 * @return mixed A scalar data
 */
function maybe_serialize( $data ) {
	if ( is_array( $data ) || is_object( $data ) ) {
		return serialize( $data );
	}

	// Double serialization is required for backward compatibility.
	// See https://core.trac.wordpress.org/ticket/12930
	// Also the world will end. See WP 3.6.1.
	if ( is_serialized( $data, false ) ) {
		return serialize( $data );
	}

	return $data;
}

/**
 * Retrieve post title from XMLRPC XML.
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
 * Retrieve the post category or categories from XMLRPC XML.
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
 * Use RegEx to extract URLs from arbitrary content.
 *
 * @since 3.7.0
 *
 * @param string $content Content to extract URLs from.
 * @return array URLs found in passed string.
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
					. "[^`!()\[\]{};:'\".,<>«»“”‘’\s]|"
					. '(?:[:]\d+)?/?'
				. ')+'
			. ')'
		. ")\\1#",
		$content,
		$post_links
	);

	$post_links = array_unique( array_map( 'html_entity_decode', $post_links[2] ) );

	return array_values( $post_links );
}

/**
 * Check content for video and audio links to add as enclosures.
 *
 * Will not add enclosures that have already been added and will
 * remove enclosures that are no longer in the post. This is called as
 * pingbacks and trackbacks.
 *
 * @since 1.5.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param string $content Post Content.
 * @param int    $post_ID Post ID.
 */
function do_enclose( $content, $post_ID ) {
	global $wpdb;

	//TODO: Tidy this ghetto code up and make the debug code optional
	include_once( ABSPATH . WPINC . '/class-IXR.php' );

	$post_links = array();

	$pung = get_enclosed( $post_ID );

	$post_links_temp = wp_extract_urls( $content );

	foreach ( $pung as $link_test ) {
		if ( ! in_array( $link_test, $post_links_temp ) ) { // link no longer in post
			$mids = $wpdb->get_col( $wpdb->prepare( "SELECT meta_id FROM $wpdb->postmeta WHERE post_id = %d AND meta_key = 'enclosure' AND meta_value LIKE %s", $post_ID, $wpdb->esc_like( $link_test ) . '%' ) );
			foreach ( $mids as $mid ) {
				delete_metadata_by_mid( 'post', $mid );
			}
		}
	}

	foreach ( (array) $post_links_temp as $link_test ) {
		if ( ! in_array( $link_test, $pung ) ) { // If we haven't pung it already
			$test = @parse_url( $link_test );
			if ( false === $test ) {
				continue;
			}
			if ( isset( $test['query'] ) ) {
				$post_links[] = $link_test;
			} elseif ( isset( $test['path'] ) && ( $test['path'] != '/' ) && ( $test['path'] != '' ) ) {
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
	 * @param array $post_links An array of enclosure links.
	 * @param int   $post_ID    Post ID.
	 */
	$post_links = apply_filters( 'enclosure_links', $post_links, $post_ID );

	foreach ( (array) $post_links as $url ) {
		if ( $url != '' && ! $wpdb->get_var( $wpdb->prepare( "SELECT post_id FROM $wpdb->postmeta WHERE post_id = %d AND meta_key = 'enclosure' AND meta_value LIKE %s", $post_ID, $wpdb->esc_like( $url ) . '%' ) ) ) {

			if ( $headers = wp_get_http_headers( $url ) ) {
				$len           = isset( $headers['content-length'] ) ? (int) $headers['content-length'] : 0;
				$type          = isset( $headers['content-type'] ) ? $headers['content-type'] : '';
				$allowed_types = array( 'video', 'audio' );

				// Check to see if we can figure out the mime type from
				// the extension
				$url_parts = @parse_url( $url );
				if ( false !== $url_parts ) {
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

				if ( in_array( substr( $type, 0, strpos( $type, '/' ) ), $allowed_types ) ) {
					add_post_meta( $post_ID, 'enclosure', "$url\n$len\n$mime\n" );
				}
			}
		}
	}
}

/**
 * Retrieve HTTP Headers from URL.
 *
 * @since 1.5.1
 *
 * @param string $url        URL to retrieve HTTP headers from.
 * @param bool   $deprecated Not Used.
 * @return bool|string False on failure, headers on success.
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
	if ( $currentday != $previousday ) {
		return 1;
	} else {
		return 0;
	}
}

/**
 * Build URL query based on an associative and, or indexed array.
 *
 * This is a convenient function for easily building url queries. It sets the
 * separator to '&' and uses _http_build_query() function.
 *
 * @since 2.3.0
 *
 * @see _http_build_query() Used to build the query
 * @link https://secure.php.net/manual/en/function.http-build-query.php for more on what
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
 * @see https://secure.php.net/manual/en/function.http-build-query.php
 *
 * @param array|object  $data       An array or object of data. Converted to array.
 * @param string        $prefix     Optional. Numeric index. If set, start parameter numbering with it.
 *                                  Default null.
 * @param string        $sep        Optional. Argument separator; defaults to 'arg_separator.output'.
 *                                  Default null.
 * @param string        $key        Optional. Used to prefix key name. Default empty.
 * @param bool          $urlencode  Optional. Whether to use urlencode() in the result. Default true.
 *
 * @return string The query string.
 */
function _http_build_query( $data, $prefix = null, $sep = null, $key = '', $urlencode = true ) {
	$ret = array();

	foreach ( (array) $data as $k => $v ) {
		if ( $urlencode ) {
			$k = urlencode( $k );
		}
		if ( is_int( $k ) && $prefix != null ) {
			$k = $prefix . $k;
		}
		if ( ! empty( $key ) ) {
			$k = $key . '%5B' . $k . '%5D';
		}
		if ( $v === null ) {
			continue;
		} elseif ( $v === false ) {
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
 *
 * @param string|array $key   Either a query variable key, or an associative array of query variables.
 * @param string       $value Optional. Either a query variable value, or a URL to act upon.
 * @param string       $url   Optional. A URL to act upon.
 * @return string New URL query string (unescaped).
 */
function add_query_arg() {
	$args = func_get_args();
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

	if ( $frag = strstr( $uri, '#' ) ) {
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

	if ( strpos( $uri, '?' ) !== false ) {
		list( $base, $query ) = explode( '?', $uri, 2 );
		$base                .= '?';
	} elseif ( $protocol || strpos( $uri, '=' ) === false ) {
		$base  = $uri . '?';
		$query = '';
	} else {
		$base  = '';
		$query = $uri;
	}

	wp_parse_str( $query, $qs );
	$qs = urlencode_deep( $qs ); // this re-URL-encodes things that were already in the query string
	if ( is_array( $args[0] ) ) {
		foreach ( $args[0] as $k => $v ) {
			$qs[ $k ] = $v;
		}
	} else {
		$qs[ $args[0] ] = $args[1];
	}

	foreach ( $qs as $k => $v ) {
		if ( $v === false ) {
			unset( $qs[ $k ] );
		}
	}

	$ret = build_query( $qs );
	$ret = trim( $ret, '?' );
	$ret = preg_replace( '#=(&|$)#', '$1', $ret );
	$ret = $protocol . $base . $ret . $frag;
	$ret = rtrim( $ret, '?' );
	return $ret;
}

/**
 * Removes an item or items from a query string.
 *
 * @since 1.5.0
 *
 * @param string|array $key   Query key or keys to remove.
 * @param bool|string  $query Optional. When false uses the current URL. Default false.
 * @return string New URL query string.
 */
function remove_query_arg( $key, $query = false ) {
	if ( is_array( $key ) ) { // removing multiple keys
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
 * @return array An array of parameters to remove from the URL.
 */
function wp_removable_query_args() {
	$removable_query_args = array(
		'activate',
		'activated',
		'approved',
		'deactivate',
		'deleted',
		'disabled',
		'enabled',
		'error',
		'hotkeys_highlight_first',
		'hotkeys_highlight_last',
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
	 * Filters the list of query variables to remove.
	 *
	 * @since 4.2.0
	 *
	 * @param array $removable_query_args An array of query variables to remove from a URL.
	 */
	return apply_filters( 'removable_query_args', $removable_query_args );
}

/**
 * Walks the array while sanitizing the contents.
 *
 * @since 0.71
 *
 * @param array $array Array to walk while sanitizing contents.
 * @return array Sanitized $array.
 */
function add_magic_quotes( $array ) {
	foreach ( (array) $array as $k => $v ) {
		if ( is_array( $v ) ) {
			$array[ $k ] = add_magic_quotes( $v );
		} else {
			$array[ $k ] = addslashes( $v );
		}
	}
	return $array;
}

/**
 * HTTP request for URI to retrieve content.
 *
 * @since 1.5.1
 *
 * @see wp_safe_remote_get()
 *
 * @param string $uri URI/URL of web page to retrieve.
 * @return false|string HTTP content. False on failure.
 */
function wp_remote_fopen( $uri ) {
	$parsed_url = @parse_url( $uri );

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
 * Set up the WordPress query.
 *
 * @since 2.0.0
 *
 * @global WP       $wp_locale
 * @global WP_Query $wp_query
 * @global WP_Query $wp_the_query
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
 * Retrieve the description for the HTTP status.
 *
 * @since 2.3.0
 * @since 3.9.0 Added status codes 418, 428, 429, 431, and 511.
 * @since 4.5.0 Added status codes 308, 421, and 451.
 * @since 5.1.0 Added status code 103.
 *
 * @global array $wp_header_to_desc
 *
 * @param int $code HTTP status code.
 * @return string Empty string if not found, or description if found.
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
 * Set HTTP status header.
 *
 * @since 2.0.0
 * @since 4.4.0 Added the `$description` parameter.
 *
 * @see get_status_header_desc()
 *
 * @param int    $code        HTTP status code.
 * @param string $description Optional. A custom description for the HTTP status.
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

	@header( $status_header, true, $code );
}

/**
 * Get the header information to prevent caching.
 *
 * The several different headers cover the different ways cache prevention
 * is handled by different browsers
 *
 * @since 2.8.0
 *
 * @return array The associative array of header names and field values.
 */
function wp_get_nocache_headers() {
	$headers = array(
		'Expires'       => 'Wed, 11 Jan 1984 05:00:00 GMT',
		'Cache-Control' => 'no-cache, must-revalidate, max-age=0',
	);

	if ( function_exists( 'apply_filters' ) ) {
		/**
		 * Filters the cache-controlling headers.
		 *
		 * @since 2.8.0
		 *
		 * @see wp_get_nocache_headers()
		 *
		 * @param array $headers {
		 *     Header names and field values.
		 *
		 *     @type string $Expires       Expires header.
		 *     @type string $Cache-Control Cache-Control header.
		 * }
		 */
		$headers = (array) apply_filters( 'nocache_headers', $headers );
	}
	$headers['Last-Modified'] = false;
	return $headers;
}

/**
 * Set the headers to prevent caching for the different browsers.
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
	$headers = wp_get_nocache_headers();

	unset( $headers['Last-Modified'] );

	// In PHP 5.3+, make sure we are not sending a Last-Modified header.
	if ( function_exists( 'header_remove' ) ) {
		@header_remove( 'Last-Modified' );
	} else {
		// In PHP 5.2, send an empty Last-Modified header, but only as a
		// last resort to override a header already sent. #WP23021
		foreach ( headers_list() as $header ) {
			if ( 0 === stripos( $header, 'Last-Modified' ) ) {
				$headers['Last-Modified'] = '';
				break;
			}
		}
	}

	foreach ( $headers as $name => $field_value ) {
		@header( "{$name}: {$field_value}" );
	}
}

/**
 * Set the headers for caching for 10 days with JavaScript content type.
 *
 * @since 2.1.0
 */
function cache_javascript_headers() {
	$expiresOffset = 10 * DAY_IN_SECONDS;

	header( 'Content-Type: text/javascript; charset=' . get_bloginfo( 'charset' ) );
	header( 'Vary: Accept-Encoding' ); // Handle proxies
	header( 'Expires: ' . gmdate( 'D, d M Y H:i:s', time() + $expiresOffset ) . ' GMT' );
}

/**
 * Retrieve the number of database queries during the WordPress execution.
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
 * Whether input is yes or no.
 *
 * Must be 'y' to be true.
 *
 * @since 1.0.0
 *
 * @param string $yn Character string containing either 'y' (yes) or 'n' (no).
 * @return bool True if yes, false on anything else.
 */
function bool_from_yn( $yn ) {
	return ( strtolower( $yn ) == 'y' );
}

/**
 * Load the feed template from the use of an action hook.
 *
 * If the feed action does not have a hook, then the function will die with a
 * message telling the visitor that the feed is not valid.
 *
 * It is better to only have one hook for each feed.
 *
 * @since 2.1.0
 *
 * @global WP_Query $wp_query Used to tell if the use a comment feed.
 */
function do_feed() {
	global $wp_query;

	$feed = get_query_var( 'feed' );

	// Remove the pad, if present.
	$feed = preg_replace( '/^_+/', '', $feed );

	if ( $feed == '' || $feed == 'feed' ) {
		$feed = get_default_feed();
	}

	if ( ! has_action( "do_feed_{$feed}" ) ) {
		wp_die( __( 'ERROR: This is not a valid feed template.' ), '', array( 'response' => 404 ) );
	}

	/**
	 * Fires once the given feed is loaded.
	 *
	 * The dynamic portion of the hook name, `$feed`, refers to the feed template name.
	 * Possible values include: 'rdf', 'rss', 'rss2', and 'atom'.
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
 * Load the RDF RSS 0.91 Feed template.
 *
 * @since 2.1.0
 *
 * @see load_template()
 */
function do_feed_rdf() {
	load_template( ABSPATH . WPINC . '/feed-rdf.php' );
}

/**
 * Load the RSS 1.0 Feed Template.
 *
 * @since 2.1.0
 *
 * @see load_template()
 */
function do_feed_rss() {
	load_template( ABSPATH . WPINC . '/feed-rss.php' );
}

/**
 * Load either the RSS2 comment feed or the RSS2 posts feed.
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
 * Load either Atom comment feed or Atom posts feed.
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
 * Display the robots.txt file content.
 *
 * The echo content should be with usage of the permalinks or for creating the
 * robots.txt file.
 *
 * @since 2.1.0
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
	$public = get_option( 'blog_public' );
	if ( '0' == $public ) {
		$output .= "Disallow: /\n";
	} else {
		$site_url = parse_url( site_url() );
		$path     = ( ! empty( $site_url['path'] ) ) ? $site_url['path'] : '';
		$output  .= "Disallow: $path/wp-admin/\n";
		$output  .= "Allow: $path/wp-admin/admin-ajax.php\n";
	}

	/**
	 * Filters the robots.txt output.
	 *
	 * @since 3.0.0
	 *
	 * @param string $output Robots.txt output.
	 * @param bool   $public Whether the site is considered "public".
	 */
	echo apply_filters( 'robots_txt', $output, $public );
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
	// If siteurl is not set to autoload, check it specifically
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
		// The existence of custom user tables shouldn't suggest an insane state or prevent a clean installation.
		if ( defined( 'CUSTOM_USER_TABLE' ) && CUSTOM_USER_TABLE == $table ) {
			continue;
		}
		if ( defined( 'CUSTOM_USER_META_TABLE' ) && CUSTOM_USER_META_TABLE == $table ) {
			continue;
		}

		$described_table = $wpdb->get_results( "DESCRIBE $table;" );
		if (
			( ! $described_table && empty( $wpdb->last_error ) ) ||
			( is_array( $described_table ) && 0 === count( $described_table ) )
		) {
			continue;
		}

		// One or more tables exist. We are insane.

		wp_load_translations_early();

		// Die with a DB error.
		$wpdb->error = sprintf(
			/* translators: %s: database repair URL */
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
 * Retrieve URL with nonce added to URL query.
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
 * Retrieve or display nonce hidden field for forms.
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
 * @param bool       $echo    Optional. Whether to display or return hidden form field. Default true.
 * @return string Nonce field HTML markup.
 */
function wp_nonce_field( $action = -1, $name = '_wpnonce', $referer = true, $echo = true ) {
	$name        = esc_attr( $name );
	$nonce_field = '<input type="hidden" id="' . $name . '" name="' . $name . '" value="' . wp_create_nonce( $action ) . '" />';

	if ( $referer ) {
		$nonce_field .= wp_referer_field( false );
	}

	if ( $echo ) {
		echo $nonce_field;
	}

	return $nonce_field;
}

/**
 * Retrieve or display referer hidden field for forms.
 *
 * The referer link is the current Request URI from the server super global. The
 * input name is '_wp_http_referer', in case you wanted to check manually.
 *
 * @since 2.0.4
 *
 * @param bool $echo Optional. Whether to echo or return the referer field. Default true.
 * @return string Referer field HTML markup.
 */
function wp_referer_field( $echo = true ) {
	$referer_field = '<input type="hidden" name="_wp_http_referer" value="' . esc_attr( wp_unslash( $_SERVER['REQUEST_URI'] ) ) . '" />';

	if ( $echo ) {
		echo $referer_field;
	}
	return $referer_field;
}

/**
 * Retrieve or display original referer hidden field for forms.
 *
 * The input name is '_wp_original_http_referer' and will be either the same
 * value of wp_referer_field(), if that was posted already or it will be the
 * current page, if it doesn't exist.
 *
 * @since 2.0.4
 *
 * @param bool   $echo         Optional. Whether to echo the original http referer. Default true.
 * @param string $jump_back_to Optional. Can be 'previous' or page you want to jump back to.
 *                             Default 'current'.
 * @return string Original referer field.
 */
function wp_original_referer_field( $echo = true, $jump_back_to = 'current' ) {
	if ( ! $ref = wp_get_original_referer() ) {
		$ref = 'previous' == $jump_back_to ? wp_get_referer() : wp_unslash( $_SERVER['REQUEST_URI'] );
	}
	$orig_referer_field = '<input type="hidden" name="_wp_original_http_referer" value="' . esc_attr( $ref ) . '" />';
	if ( $echo ) {
		echo $orig_referer_field;
	}
	return $orig_referer_field;
}

/**
 * Retrieve referer from '_wp_http_referer' or HTTP referer.
 *
 * If it's the same as the current request URL, will return false.
 *
 * @since 2.0.4
 *
 * @return false|string False on failure. Referer URL on success.
 */
function wp_get_referer() {
	if ( ! function_exists( 'wp_validate_redirect' ) ) {
		return false;
	}

	$ref = wp_get_raw_referer();

	if ( $ref && $ref !== wp_unslash( $_SERVER['REQUEST_URI'] ) && $ref !== home_url() . wp_unslash( $_SERVER['REQUEST_URI'] ) ) {
		return wp_validate_redirect( $ref, false );
	}

	return false;
}

/**
 * Retrieves unvalidated referer from '_wp_http_referer' or HTTP referer.
 *
 * Do not use for redirects, use wp_get_referer() instead.
 *
 * @since 4.5.0
 *
 * @return string|false Referer URL on success, false on failure.
 */
function wp_get_raw_referer() {
	if ( ! empty( $_REQUEST['_wp_http_referer'] ) ) {
		return wp_unslash( $_REQUEST['_wp_http_referer'] );
	} elseif ( ! empty( $_SERVER['HTTP_REFERER'] ) ) {
		return wp_unslash( $_SERVER['HTTP_REFERER'] );
	}

	return false;
}

/**
 * Retrieve original referer that was posted, if it exists.
 *
 * @since 2.0.4
 *
 * @return string|false False if no original referer or original referer if set.
 */
function wp_get_original_referer() {
	if ( ! empty( $_REQUEST['_wp_original_http_referer'] ) && function_exists( 'wp_validate_redirect' ) ) {
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
	if ( $wrapper !== null ) {
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
	if ( false !== strpos( $target, '../' ) || false !== strpos( $target, '..' . DIRECTORY_SEPARATOR ) ) {
		return false;
	}

	// We need to find the permissions of the parent folder that exists and inherit that.
	$target_parent = dirname( $target );
	while ( '.' != $target_parent && ! is_dir( $target_parent ) && dirname( $target_parent ) !== $target_parent ) {
		$target_parent = dirname( $target_parent );
	}

	// Get the permission bits.
	if ( $stat = @stat( $target_parent ) ) {
		$dir_perms = $stat['mode'] & 0007777;
	} else {
		$dir_perms = 0777;
	}

	if ( @mkdir( $target, $dir_perms, true ) ) {

		/*
		 * If a umask is set that modifies $dir_perms, we'll have to re-set
		 * the $dir_perms correctly with chmod()
		 */
		if ( $dir_perms != ( $dir_perms & ~umask() ) ) {
			$folder_parts = explode( '/', substr( $target, strlen( $target_parent ) + 1 ) );
			for ( $i = 1, $c = count( $folder_parts ); $i <= $c; $i++ ) {
				@chmod( $target_parent . '/' . implode( '/', array_slice( $folder_parts, 0, $i ) ), $dir_perms );
			}
		}

		return true;
	}

	return false;
}

/**
 * Test if a given filesystem path is absolute.
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
	if ( realpath( $path ) == $path ) {
		return true;
	}

	if ( strlen( $path ) == 0 || $path[0] == '.' ) {
		return false;
	}

	// Windows allows absolute paths like this.
	if ( preg_match( '#^[a-zA-Z]:\\\\#', $path ) ) {
		return true;
	}

	// A path starting with / or \ is absolute; anything else is relative.
	return ( $path[0] == '/' || $path[0] == '\\' );
}

/**
 * Join two filesystem paths together.
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

	return rtrim( $base, '/' ) . '/' . ltrim( $path, '/' );
}

/**
 * Normalize a filesystem path.
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
 *
 * @param string $path Path to normalize.
 * @return string Normalized path.
 */
function wp_normalize_path( $path ) {
	$wrapper = '';
	if ( wp_is_stream( $path ) ) {
		list( $wrapper, $path ) = explode( '://', $path, 2 );
		$wrapper               .= '://';
	}

	// Standardise all paths to use /
	$path = str_replace( '\\', '/', $path );

	// Replace multiple slashes down to a singular, allowing for network shares having two slashes.
	$path = preg_replace( '|(?<=.)/+|', '/', $path );

	// Windows paths should uppercase the drive letter
	if ( ':' === substr( $path, 1, 1 ) ) {
		$path = ucfirst( $path );
	}

	return $wrapper . $path;
}

/**
 * Determine a writable directory for temporary files.
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
 * @staticvar string $temp
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
 * Determine if a directory is writable.
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
	if ( 'WIN' === strtoupper( substr( PHP_OS, 0, 3 ) ) ) {
		return win_is_writable( $path );
	} else {
		return @is_writable( $path );
	}
}

/**
 * Workaround for Windows bug in is_writable() function
 *
 * PHP has issues with Windows ACL's for determine if a
 * directory is writable or not, this works around them by
 * checking the ability to open files rather than relying
 * upon PHP to interprate the OS ACL.
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

	if ( $path[ strlen( $path ) - 1 ] == '/' ) { // if it looks like a directory, check a random file within the directory
		return win_is_writable( $path . uniqid( mt_rand() ) . '.tmp' );
	} elseif ( is_dir( $path ) ) { // If it's a directory (and not a file) check a random file within the directory
		return win_is_writable( $path . '/' . uniqid( mt_rand() ) . '.tmp' );
	}
	// check tmp file for read/write capabilities
	$should_delete_tmp_file = ! file_exists( $path );
	$f                      = @fopen( $path, 'a' );
	if ( $f === false ) {
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
 * Get an array containing the current upload directory's path and url.
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
 * On success, the returned array will have many indices:
 * 'path' - base directory and sub directory or full path to upload directory.
 * 'url' - base url and sub directory or absolute URL to upload directory.
 * 'subdir' - sub directory if uploads use year/month folders option is on.
 * 'basedir' - path without subdir.
 * 'baseurl' - URL path without subdir.
 * 'error' - false or error message.
 *
 * @since 2.0.0
 * @uses _wp_upload_dir()
 *
 * @staticvar array $cache
 * @staticvar array $tested_paths
 *
 * @param string $time Optional. Time formatted in 'yyyy/mm'. Default null.
 * @param bool   $create_dir Optional. Whether to check and create the uploads directory.
 *                           Default true for backward compatibility.
 * @param bool   $refresh_cache Optional. Whether to refresh the cache. Default false.
 * @return array See above for description.
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
	 * @param array $uploads Array of upload directory data with keys of 'path',
	 *                       'url', 'subdir, 'basedir', and 'error'.
	 */
	$uploads = apply_filters( 'upload_dir', $cache[ $key ] );

	if ( $create_dir ) {
		$path = $uploads['path'];

		if ( array_key_exists( $path, $tested_paths ) ) {
			$uploads['error'] = $tested_paths[ $path ];
		} else {
			if ( ! wp_mkdir_p( $path ) ) {
				if ( 0 === strpos( $uploads['basedir'], ABSPATH ) ) {
					$error_path = str_replace( ABSPATH, '', $uploads['basedir'] ) . $uploads['subdir'];
				} else {
					$error_path = wp_basename( $uploads['basedir'] ) . $uploads['subdir'];
				}

				$uploads['error'] = sprintf(
					/* translators: %s: directory path */
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
 * @param string $time Optional. Time formatted in 'yyyy/mm'. Default null.
 * @return array See wp_upload_dir()
 */
function _wp_upload_dir( $time = null ) {
	$siteurl     = get_option( 'siteurl' );
	$upload_path = trim( get_option( 'upload_path' ) );

	if ( empty( $upload_path ) || 'wp-content/uploads' == $upload_path ) {
		$dir = WP_CONTENT_DIR . '/uploads';
	} elseif ( 0 !== strpos( $upload_path, ABSPATH ) ) {
		// $dir is absolute, $upload_path is (maybe) relative to ABSPATH
		$dir = path_join( ABSPATH, $upload_path );
	} else {
		$dir = $upload_path;
	}

	if ( ! $url = get_option( 'upload_url_path' ) ) {
		if ( empty( $upload_path ) || ( 'wp-content/uploads' == $upload_path ) || ( $upload_path == $dir ) ) {
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

	// If multisite (and if not the main site in a post-MU network)
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
		// Generate the yearly and monthly dirs
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
 * Get a filename that is sanitized and unique for the given directory.
 *
 * If the filename is not unique, then a number will be added to the filename
 * before the extension, and will continue adding numbers until the filename is
 * unique.
 *
 * The callback is passed three parameters, the first one is the directory, the
 * second is the filename, and the third is the extension.
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
		$number = '';

		// Change '.ext' to lower case.
		if ( $ext && strtolower( $ext ) != $ext ) {
			$ext2      = strtolower( $ext );
			$filename2 = preg_replace( '|' . preg_quote( $ext ) . '$|', $ext2, $filename );

			// Check for both lower and upper case extension or image sub-sizes may be overwritten.
			while ( file_exists( $dir . "/$filename" ) || file_exists( $dir . "/$filename2" ) ) {
				$new_number = (int) $number + 1;
				$filename   = str_replace( array( "-$number$ext", "$number$ext" ), "-$new_number$ext", $filename );
				$filename2  = str_replace( array( "-$number$ext2", "$number$ext2" ), "-$new_number$ext2", $filename2 );
				$number     = $new_number;
			}

			/**
			 * Filters the result when generating a unique file name.
			 *
			 * @since 4.5.0
			 *
			 * @param string        $filename                 Unique file name.
			 * @param string        $ext                      File extension, eg. ".png".
			 * @param string        $dir                      Directory path.
			 * @param callable|null $unique_filename_callback Callback function that generates the unique file name.
			 */
			return apply_filters( 'wp_unique_filename', $filename2, $ext, $dir, $unique_filename_callback );
		}

		while ( file_exists( $dir . "/$filename" ) ) {
			$new_number = (int) $number + 1;
			if ( '' == "$number$ext" ) {
				$filename = "$filename-" . $new_number;
			} else {
				$filename = str_replace( array( "-$number$ext", "$number$ext" ), '-' . $new_number . $ext, $filename );
			}
			$number = $new_number;
		}
	}

	/** This filter is documented in wp-includes/functions.php */
	return apply_filters( 'wp_unique_filename', $filename, $ext, $dir, $unique_filename_callback );
}

/**
 * Create a file in the upload folder with given content.
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
 * @param string       $name       Filename.
 * @param null|string  $deprecated Never used. Set to null.
 * @param mixed        $bits       File content
 * @param string       $time       Optional. Time formatted in 'yyyy/mm'. Default null.
 * @return array
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
		return array( 'error' => __( 'Sorry, this file type is not permitted for security reasons.' ) );
	}

	$upload = wp_upload_dir( $time );

	if ( $upload['error'] !== false ) {
		return $upload;
	}

	/**
	 * Filters whether to treat the upload bits as an error.
	 *
	 * Passing a non-array to the filter will effectively short-circuit preparing
	 * the upload bits, returning that value instead.
	 *
	 * @since 3.0.0
	 *
	 * @param mixed $upload_bits_error An array of upload bits data, or a non-array error to return.
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
		if ( 0 === strpos( $upload['basedir'], ABSPATH ) ) {
			$error_path = str_replace( ABSPATH, '', $upload['basedir'] ) . $upload['subdir'];
		} else {
			$error_path = wp_basename( $upload['basedir'] ) . $upload['subdir'];
		}

		$message = sprintf(
			/* translators: %s: directory path */
			__( 'Unable to create directory %s. Is its parent directory writable by the server?' ),
			$error_path
		);
		return array( 'error' => $message );
	}

	$ifp = @ fopen( $new_file, 'wb' );
	if ( ! $ifp ) {
		return array( 'error' => sprintf( __( 'Could not write file %s' ), $new_file ) );
	}

	@fwrite( $ifp, $bits );
	fclose( $ifp );
	clearstatcache();

	// Set correct file permissions
	$stat  = @ stat( dirname( $new_file ) );
	$perms = $stat['mode'] & 0007777;
	$perms = $perms & 0000666;
	@ chmod( $new_file, $perms );
	clearstatcache();

	// Compute the URL
	$url = $upload['url'] . "/$filename";

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
 * Retrieve the file type based on the extension name.
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
		if ( in_array( $ext, $exts ) ) {
			return $type;
		}
	}
}

/**
 * Retrieve the file type from the file name.
 *
 * You can optionally define the mime array, if needed.
 *
 * @since 2.0.4
 *
 * @param string $filename File name or path.
 * @param array  $mimes    Optional. Key is the file extension with value as the mime type.
 * @return array Values with extension first and mime type.
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
 * Attempt to determine the real file type of a file.
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
 * @param string $file     Full path to the file.
 * @param string $filename The name of the file (may differ from $file due to $file being
 *                         in a tmp directory).
 * @param array   $mimes   Optional. Key is the file extension with value as the mime type.
 * @return array Values for the extension, MIME, and either a corrected filename or false
 *               if original $filename is valid.
 */
function wp_check_filetype_and_ext( $file, $filename, $mimes = null ) {
	$proper_filename = false;

	// Do basic extension validation and MIME mapping
	$wp_filetype = wp_check_filetype( $filename, $mimes );
	$ext         = $wp_filetype['ext'];
	$type        = $wp_filetype['type'];

	// We can't do any further validation without a file to work with
	if ( ! file_exists( $file ) ) {
		return compact( 'ext', 'type', 'proper_filename' );
	}

	$real_mime = false;

	// Validate image types.
	if ( $type && 0 === strpos( $type, 'image/' ) ) {

		// Attempt to figure out what type of image it actually is
		$real_mime = wp_get_image_mime( $file );

		if ( $real_mime && $real_mime != $type ) {
			/**
			 * Filters the list mapping image mime types to their respective extensions.
			 *
			 * @since 3.0.0
			 *
			 * @param  array $mime_to_ext Array of image mime types and their matching extensions.
			 */
			$mime_to_ext = apply_filters(
				'getimagesize_mimes_to_exts',
				array(
					'image/jpeg' => 'jpg',
					'image/png'  => 'png',
					'image/gif'  => 'gif',
					'image/bmp'  => 'bmp',
					'image/tiff' => 'tif',
				)
			);

			// Replace whatever is after the last period in the filename with the correct extension
			if ( ! empty( $mime_to_ext[ $real_mime ] ) ) {
				$filename_parts = explode( '.', $filename );
				array_pop( $filename_parts );
				$filename_parts[] = $mime_to_ext[ $real_mime ];
				$new_filename     = implode( '.', $filename_parts );

				if ( $new_filename != $filename ) {
					$proper_filename = $new_filename; // Mark that it changed
				}
				// Redefine the extension / MIME
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

		// fileinfo often misidentifies obscure files as one of these types
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
			if ( ! in_array( substr( $type, 0, strcspn( $type, '/' ) ), array( 'application', 'video', 'audio' ) ) ) {
				$type = $ext = false;
			}
		} elseif ( 0 === strpos( $real_mime, 'video/' ) || 0 === strpos( $real_mime, 'audio/' ) ) {
			/*
			 * For these types, only the major type must match the real value.
			 * This means that common mismatches are forgiven: application/vnd.apple.numbers is often misidentified as application/zip,
			 * and some media files are commonly named with the wrong extension (.mov instead of .mp4)
			 */
			if ( substr( $real_mime, 0, strcspn( $real_mime, '/' ) ) !== substr( $type, 0, strcspn( $type, '/' ) ) ) {
				$type = $ext = false;
			}
		} elseif ( 'text/plain' === $real_mime ) {
			// A few common file types are occasionally detected as text/plain; allow those.
			if ( ! in_array(
				$type,
				array(
					'text/plain',
					'text/csv',
					'text/richtext',
					'text/tsv',
					'text/vtt',
				)
			)
			) {
				$type = $ext = false;
			}
		} elseif ( 'text/rtf' === $real_mime ) {
			// Special casing for RTF files.
			if ( ! in_array(
				$type,
				array(
					'text/rtf',
					'text/plain',
					'application/rtf',
				)
			)
			) {
				$type = $ext = false;
			}
		} else {
			if ( $type !== $real_mime ) {
				/*
				 * Everything else including image/* and application/*:
				 * If the real content type doesn't match the file extension, assume it's dangerous.
				 */
				$type = $ext = false;
			}
		}
	}

	// The mime type must be allowed
	if ( $type ) {
		$allowed = get_allowed_mime_types();

		if ( ! in_array( $type, $allowed ) ) {
			$type = $ext = false;
		}
	}

	/**
	 * Filters the "real" file type of the given file.
	 *
	 * @since 3.0.0
	 * @since 5.1.0 The $real_mime parameter was added.
	 *
	 * @param array       $wp_check_filetype_and_ext File data array containing 'ext', 'type', and
	 *                                               'proper_filename' keys.
	 * @param string      $file                      Full path to the file.
	 * @param string      $filename                  The name of the file (may differ from $file due to
	 *                                               $file being in a tmp directory).
	 * @param array       $mimes                     Key is the file extension with value as the mime type.
	 * @param string|bool $real_mime                 The actual mime type or false if the type cannot be determined.
	 */
	return apply_filters( 'wp_check_filetype_and_ext', compact( 'ext', 'type', 'proper_filename' ), $file, $filename, $mimes, $real_mime );
}

/**
 * Returns the real mime type of an image file.
 *
 * This depends on exif_imagetype() or getimagesize() to determine real mime types.
 *
 * @since 4.7.1
 *
 * @param string $file Full path to the file.
 * @return string|false The actual mime type or false if the type cannot be determined.
 */
function wp_get_image_mime( $file ) {
	/*
	 * Use exif_imagetype() to check the mimetype if available or fall back to
	 * getimagesize() if exif isn't avaialbe. If either function throws an Exception
	 * we assume the file could not be validated.
	 */
	try {
		if ( is_callable( 'exif_imagetype' ) ) {
			$imagetype = exif_imagetype( $file );
			$mime      = ( $imagetype ) ? image_type_to_mime_type( $imagetype ) : false;
		} elseif ( function_exists( 'getimagesize' ) ) {
			$imagesize = getimagesize( $file );
			$mime      = ( isset( $imagesize['mime'] ) ) ? $imagesize['mime'] : false;
		} else {
			$mime = false;
		}
	} catch ( Exception $e ) {
		$mime = false;
	}

	return $mime;
}

/**
 * Retrieve list of mime types and file extensions.
 *
 * @since 3.5.0
 * @since 4.2.0 Support was added for GIMP (xcf) files.
 *
 * @return array Array of mime types keyed by the file extension regex corresponding to those types.
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
	 * @param array $wp_get_mime_types Mime types keyed by the file extension regex
	 *                                 corresponding to those types.
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
			'ico'                          => 'image/x-icon',
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
			'3gp|3gpp'                     => 'video/3gpp', // Can also be audio
			'3g2|3gp2'                     => 'video/3gpp2', // Can also be audio
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
			'wav'                          => 'audio/wav',
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
 * @return array Array of file extensions types keyed by the type of file.
 */
function wp_get_ext_types() {

	/**
	 * Filters file type based on the extension name.
	 *
	 * @since 2.5.0
	 *
	 * @see wp_ext2type()
	 *
	 * @param array $ext2type Multi-dimensional array with extensions for a default set
	 *                        of file types.
	 */
	return apply_filters(
		'ext2type',
		array(
			'image'       => array( 'jpg', 'jpeg', 'jpe', 'gif', 'png', 'bmp', 'tif', 'tiff', 'ico' ),
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
 * Retrieve list of allowed mime types and file extensions.
 *
 * @since 2.8.6
 *
 * @param int|WP_User $user Optional. User to check. Defaults to current user.
 * @return array Array of mime types keyed by the file extension regex corresponding
 *               to those types.
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
	 * Filters list of allowed mime types and file extensions.
	 *
	 * @since 2.0.0
	 *
	 * @param array            $t    Mime types keyed by the file extension regex corresponding to
	 *                               those types. 'swf' and 'exe' removed from full list. 'htm|html' also
	 *                               removed depending on '$user' capabilities.
	 * @param int|WP_User|null $user User ID, User object or null if not provided (indicates current user).
	 */
	return apply_filters( 'upload_mimes', $t, $user );
}

/**
 * Display "Are You Sure" message to confirm the action being taken.
 *
 * If the action has the nonce explain message, then it will be displayed
 * along with the "Are you sure?" message.
 *
 * @since 2.0.4
 *
 * @param string $action The nonce action.
 */
function wp_nonce_ays( $action ) {
	if ( 'log-out' == $action ) {
		$html = sprintf(
			/* translators: %s: site name */
			__( 'You are attempting to log out of %s' ),
			get_bloginfo( 'name' )
		);
		$html       .= '</p><p>';
		$redirect_to = isset( $_REQUEST['redirect_to'] ) ? $_REQUEST['redirect_to'] : '';
		$html       .= sprintf(
			/* translators: %s: logout URL */
			__( 'Do you really want to <a href="%s">log out</a>?' ),
			wp_logout_url( $redirect_to )
		);
	} else {
		$html = __( 'The link you followed has expired.' );
		if ( wp_get_referer() ) {
			$wp_http_referer = remove_query_arg( 'updated', wp_get_referer() );
			$wp_http_referer = wp_validate_redirect( esc_url_raw( $wp_http_referer ) );
			$html           .= '</p><p>';
			$html           .= sprintf(
				'<a href="%s">%s</a>',
				esc_url( $wp_http_referer ),
				__( 'Please try again.' )
			);
		}
	}

	wp_die( $html, __( 'Something went wrong.' ), 403 );
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
 *
 * @global WP_Query $wp_query Global WP_Query instance.
 *
 * @param string|WP_Error  $message Optional. Error message. If this is a WP_Error object,
 *                                  and not an Ajax or XML-RPC request, the error's messages are used.
 *                                  Default empty.
 * @param string|int       $title   Optional. Error title. If `$message` is a `WP_Error` object,
 *                                  error data with the key 'title' may be used to specify the title.
 *                                  If `$title` is an integer, then it is treated as the response
 *                                  code. Default empty.
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
 *     @type string $text_direction The text direction. This is only useful internally, when WordPress
 *                                  is still loading and the site's locale is not set up yet. Accepts 'rtl'.
 *                                  Default is the value of is_rtl().
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
		 * @param callable $function Callback function name.
		 */
		$function = apply_filters( 'wp_die_ajax_handler', '_ajax_wp_die_handler' );
	} elseif ( wp_is_json_request() ) {
		/**
		 * Filters the callback for killing WordPress execution for JSON requests.
		 *
		 * @since 5.1.0
		 *
		 * @param callable $function Callback function name.
		 */
		$function = apply_filters( 'wp_die_json_handler', '_json_wp_die_handler' );
	} elseif ( defined( 'REST_REQUEST' ) && REST_REQUEST && wp_is_jsonp_request() ) {
		/**
		 * Filters the callback for killing WordPress execution for JSONP REST requests.
		 *
		 * @since 5.2.0
		 *
		 * @param callable $function Callback function name.
		 */
		$function = apply_filters( 'wp_die_jsonp_handler', '_jsonp_wp_die_handler' );
	} elseif ( defined( 'XMLRPC_REQUEST' ) && XMLRPC_REQUEST ) {
		/**
		 * Filters the callback for killing WordPress execution for XML-RPC requests.
		 *
		 * @since 3.4.0
		 *
		 * @param callable $function Callback function name.
		 */
		$function = apply_filters( 'wp_die_xmlrpc_handler', '_xmlrpc_wp_die_handler' );
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
		 * @param callable $function Callback function name.
		 */
		$function = apply_filters( 'wp_die_xml_handler', '_xml_wp_die_handler' );
	} else {
		/**
		 * Filters the callback for killing WordPress execution for all non-Ajax, non-JSON, non-XML requests.
		 *
		 * @since 3.0.0
		 *
		 * @param callable $function Callback function name.
		 */
		$function = apply_filters( 'wp_die_handler', '_default_wp_die_handler' );
	}

	call_user_func( $function, $message, $title, $args );
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
 * @param string          $title   Optional. Error title. Default empty.
 * @param string|array    $args    Optional. Arguments to control behavior. Default empty array.
 */
function _default_wp_die_handler( $message, $title = '', $args = array() ) {
	list( $message, $title, $r ) = _wp_die_process_input( $message, $title, $args );

	if ( is_string( $message ) ) {
		if ( ! empty( $r['additional_errors'] ) ) {
			$message = array_merge(
				array( $message ),
				wp_list_pluck( $r['additional_errors'], 'message' )
			);
			$message = "<ul>\n\t\t<li>" . join( "</li>\n\t\t<li>", $message ) . "</li>\n\t</ul>";
		} else {
			$message = "<p>$message</p>";
		}
	}

	$have_gettext = function_exists( '__' );

	if ( ! empty( $r['link_url'] ) && ! empty( $r['link_text'] ) ) {
		$link_url = $r['link_url'];
		if ( function_exists( 'esc_url' ) ) {
			$link_url = esc_url( $link_url );
		}
		$link_text = $r['link_text'];
		$message  .= "\n<p><a href='{$link_url}'>{$link_text}</a></p>";
	}

	if ( isset( $r['back_link'] ) && $r['back_link'] ) {
		$back_text = $have_gettext ? __( '&laquo; Back' ) : '&laquo; Back';
		$message  .= "\n<p><a href='javascript:history.back()'>$back_text</a></p>";
	}

	if ( ! did_action( 'admin_head' ) ) :
		if ( ! headers_sent() ) {
			header( 'Content-Type: text/html; charset=utf-8' );
			status_header( $r['response'] );
			nocache_headers();
		}

		$text_direction = $r['text_direction'];
		if ( function_exists( 'language_attributes' ) && function_exists( 'is_rtl' ) ) {
			$dir_attr = get_language_attributes();
		} else {
			$dir_attr = "dir='$text_direction'";
		}
		?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" <?php echo $dir_attr; ?>>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width">
		<?php
		if ( function_exists( 'wp_no_robots' ) ) {
			wp_no_robots();
		}
		?>
	<title><?php echo $title; ?></title>
	<style type="text/css">
		html {
			background: #f1f1f1;
		}
		body {
			background: #fff;
			color: #444;
			font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
			margin: 2em auto;
			padding: 1em 2em;
			max-width: 700px;
			-webkit-box-shadow: 0 1px 3px rgba(0, 0, 0, 0.13);
			box-shadow: 0 1px 3px rgba(0, 0, 0, 0.13);
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
		#error-page p {
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
			color: #0073aa;
		}
		a:hover,
		a:active {
			color: #00a0d2;
		}
		a:focus {
			color: #124964;
			-webkit-box-shadow:
				0 0 0 1px #5b9dd9,
				0 0 2px 1px rgba(30, 140, 190, 0.8);
			box-shadow:
				0 0 0 1px #5b9dd9,
				0 0 2px 1px rgba(30, 140, 190, 0.8);
			outline: none;
		}
		.button {
			background: #f7f7f7;
			border: 1px solid #ccc;
			color: #555;
			display: inline-block;
			text-decoration: none;
			font-size: 13px;
			line-height: 26px;
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

			-webkit-box-shadow: 0 1px 0 #ccc;
			box-shadow: 0 1px 0 #ccc;
			 vertical-align: top;
		}

		.button.button-large {
			height: 30px;
			line-height: 28px;
			padding: 0 12px 2px;
		}

		.button:hover,
		.button:focus {
			background: #fafafa;
			border-color: #999;
			color: #23282d;
		}

		.button:focus {
			border-color: #5b9dd9;
			-webkit-box-shadow: 0 0 3px rgba(0, 115, 170, 0.8);
			box-shadow: 0 0 3px rgba(0, 115, 170, 0.8);
			outline: none;
		}

		.button:active {
			background: #eee;
			border-color: #999;
			 -webkit-box-shadow: inset 0 2px 5px -3px rgba(0, 0, 0, 0.5);
			 box-shadow: inset 0 2px 5px -3px rgba(0, 0, 0, 0.5);
			 -webkit-transform: translateY(1px);
			 -ms-transform: translateY(1px);
			 transform: translateY(1px);
		}

		<?php
		if ( 'rtl' == $text_direction ) {
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
	if ( $r['exit'] ) {
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
 * @param string       $title   Optional. Error title (unused). Default empty.
 * @param string|array $args    Optional. Arguments to control behavior. Default empty array.
 */
function _ajax_wp_die_handler( $message, $title = '', $args = array() ) {
	// Set default 'response' to 200 for AJAX requests.
	$args = wp_parse_args(
		$args,
		array( 'response' => 200 )
	);

	list( $message, $title, $r ) = _wp_die_process_input( $message, $title, $args );

	if ( ! headers_sent() ) {
		// This is intentional. For backward-compatibility, support passing null here.
		if ( null !== $args['response'] ) {
			status_header( $r['response'] );
		}
		nocache_headers();
	}

	if ( is_scalar( $message ) ) {
		$message = (string) $message;
	} else {
		$message = '0';
	}

	if ( $r['exit'] ) {
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
 * @param string       $title   Optional. Error title. Default empty.
 * @param string|array $args    Optional. Arguments to control behavior. Default empty array.
 */
function _json_wp_die_handler( $message, $title = '', $args = array() ) {
	list( $message, $title, $r ) = _wp_die_process_input( $message, $title, $args );

	$data = array(
		'code'              => $r['code'],
		'message'           => $message,
		'data'              => array(
			'status' => $r['response'],
		),
		'additional_errors' => $r['additional_errors'],
	);

	if ( ! headers_sent() ) {
		header( 'Content-Type: application/json; charset=utf-8' );
		if ( null !== $r['response'] ) {
			status_header( $r['response'] );
		}
		nocache_headers();
	}

	echo wp_json_encode( $data );
	if ( $r['exit'] ) {
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
 * @param string       $title   Optional. Error title. Default empty.
 * @param string|array $args    Optional. Arguments to control behavior. Default empty array.
 */
function _jsonp_wp_die_handler( $message, $title = '', $args = array() ) {
	list( $message, $title, $r ) = _wp_die_process_input( $message, $title, $args );

	$data = array(
		'code'              => $r['code'],
		'message'           => $message,
		'data'              => array(
			'status' => $r['response'],
		),
		'additional_errors' => $r['additional_errors'],
	);

	if ( ! headers_sent() ) {
		header( 'Content-Type: application/javascript; charset=utf-8' );
		header( 'X-Content-Type-Options: nosniff' );
		header( 'X-Robots-Tag: noindex' );
		if ( null !== $r['response'] ) {
			status_header( $r['response'] );
		}
		nocache_headers();
	}

	$result         = wp_json_encode( $data );
	$jsonp_callback = $_GET['_jsonp'];
	echo '/**/' . $jsonp_callback . '(' . $result . ')';
	if ( $r['exit'] ) {
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
 * @param string       $title   Optional. Error title. Default empty.
 * @param string|array $args    Optional. Arguments to control behavior. Default empty array.
 */
function _xmlrpc_wp_die_handler( $message, $title = '', $args = array() ) {
	global $wp_xmlrpc_server;

	list( $message, $title, $r ) = _wp_die_process_input( $message, $title, $args );

	if ( ! headers_sent() ) {
		nocache_headers();
	}

	if ( $wp_xmlrpc_server ) {
		$error = new IXR_Error( $r['response'], $message );
		$wp_xmlrpc_server->output( $error->getXml() );
	}
	if ( $r['exit'] ) {
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
 * @param string       $title   Optional. Error title. Default empty.
 * @param string|array $args    Optional. Arguments to control behavior. Default empty array.
 */
function _xml_wp_die_handler( $message, $title = '', $args = array() ) {
	list( $message, $title, $r ) = _wp_die_process_input( $message, $title, $args );

	$message = htmlspecialchars( $message );
	$title   = htmlspecialchars( $title );

	$xml = <<<EOD
<error>
    <code>{$r['code']}</code>
    <title><![CDATA[{$title}]]></title>
    <message><![CDATA[{$message}]]></message>
    <data>
        <status>{$r['response']}</status>
    </data>
</error>

EOD;

	if ( ! headers_sent() ) {
		header( 'Content-Type: text/xml; charset=utf-8' );
		if ( null !== $r['response'] ) {
			status_header( $r['response'] );
		}
		nocache_headers();
	}

	echo $xml;
	if ( $r['exit'] ) {
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
 * @param string       $message Optional. Response to print. Default empty.
 * @param string       $title   Optional. Error title (unused). Default empty.
 * @param string|array $args    Optional. Arguments to control behavior. Default empty array.
 */
function _scalar_wp_die_handler( $message = '', $title = '', $args = array() ) {
	list( $message, $title, $r ) = _wp_die_process_input( $message, $title, $args );

	if ( $r['exit'] ) {
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
 * @param string       $message Error message.
 * @param string       $title   Optional. Error title. Default empty.
 * @param string|array $args    Optional. Arguments to control behavior. Default empty array.
 * @return array List of processed $message string, $title string, and $args array.
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

	return array( $message, $title, $args );
}

/**
 * Encode a variable into JSON, with some sanity checks.
 *
 * @since 4.1.0
 *
 * @param mixed $data    Variable (usually an array or object) to encode as JSON.
 * @param int   $options Optional. Options to be passed to json_encode(). Default 0.
 * @param int   $depth   Optional. Maximum depth to walk through $data. Must be
 *                       greater than 0. Default 512.
 * @return string|false The JSON encoded string, or false if it cannot be encoded.
 */
function wp_json_encode( $data, $options = 0, $depth = 512 ) {
	/*
	 * json_encode() has had extra params added over the years.
	 * $options was added in 5.3, and $depth in 5.5.
	 * We need to make sure we call it with the correct arguments.
	 */
	if ( version_compare( PHP_VERSION, '5.5', '>=' ) ) {
		$args = array( $data, $options, $depth );
	} elseif ( version_compare( PHP_VERSION, '5.3', '>=' ) ) {
		$args = array( $data, $options );
	} else {
		$args = array( $data );
	}

	// Prepare the data for JSON serialization.
	$args[0] = _wp_json_prepare_data( $data );

	$json = @call_user_func_array( 'json_encode', $args );

	// If json_encode() was successful, no need to do more sanity checking.
	// ... unless we're in an old version of PHP, and json_encode() returned
	// a string containing 'null'. Then we need to do more sanity checking.
	if ( false !== $json && ( version_compare( PHP_VERSION, '5.5', '>=' ) || false === strpos( $json, 'null' ) ) ) {
		return $json;
	}

	try {
		$args[0] = _wp_json_sanity_check( $data, $depth );
	} catch ( Exception $e ) {
		return false;
	}

	return call_user_func_array( 'json_encode', $args );
}

/**
 * Perform sanity checks on data that shall be encoded to JSON.
 *
 * @ignore
 * @since 4.1.0
 * @access private
 *
 * @see wp_json_encode()
 *
 * @param mixed $data  Variable (usually an array or object) to encode as JSON.
 * @param int   $depth Maximum depth to walk through $data. Must be greater than 0.
 * @return mixed The sanitized data that shall be encoded to JSON.
 */
function _wp_json_sanity_check( $data, $depth ) {
	if ( $depth < 0 ) {
		throw new Exception( 'Reached depth limit' );
	}

	if ( is_array( $data ) ) {
		$output = array();
		foreach ( $data as $id => $el ) {
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
	} elseif ( is_object( $data ) ) {
		$output = new stdClass;
		foreach ( $data as $id => $el ) {
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
	} elseif ( is_string( $data ) ) {
		return _wp_json_convert_string( $data );
	} else {
		return $data;
	}

	return $output;
}

/**
 * Convert a string to UTF-8, so that it can be safely encoded to JSON.
 *
 * @ignore
 * @since 4.1.0
 * @access private
 *
 * @see _wp_json_sanity_check()
 *
 * @staticvar bool $use_mb
 *
 * @param string $string The string which is to be converted.
 * @return string The checked string.
 */
function _wp_json_convert_string( $string ) {
	static $use_mb = null;
	if ( is_null( $use_mb ) ) {
		$use_mb = function_exists( 'mb_convert_encoding' );
	}

	if ( $use_mb ) {
		$encoding = mb_detect_encoding( $string, mb_detect_order(), true );
		if ( $encoding ) {
			return mb_convert_encoding( $string, 'UTF-8', $encoding );
		} else {
			return mb_convert_encoding( $string, 'UTF-8', 'UTF-8' );
		}
	} else {
		return wp_check_invalid_utf8( $string, true );
	}
}

/**
 * Prepares response data to be serialized to JSON.
 *
 * This supports the JsonSerializable interface for PHP 5.2-5.3 as well.
 *
 * @ignore
 * @since 4.4.0
 * @access private
 *
 * @param mixed $data Native representation.
 * @return bool|int|float|null|string|array Data ready for `json_encode()`.
 */
function _wp_json_prepare_data( $data ) {
	if ( ! defined( 'WP_JSON_SERIALIZE_COMPATIBLE' ) || WP_JSON_SERIALIZE_COMPATIBLE === false ) {
		return $data;
	}

	switch ( gettype( $data ) ) {
		case 'boolean':
		case 'integer':
		case 'double':
		case 'string':
		case 'NULL':
			// These values can be passed through.
			return $data;

		case 'array':
			// Arrays must be mapped in case they also return objects.
			return array_map( '_wp_json_prepare_data', $data );

		case 'object':
			// If this is an incomplete object (__PHP_Incomplete_Class), bail.
			if ( ! is_object( $data ) ) {
				return null;
			}

			if ( $data instanceof JsonSerializable ) {
				$data = $data->jsonSerialize();
			} else {
				$data = get_object_vars( $data );
			}

			// Now, pass the array (or whatever was returned from jsonSerialize through).
			return _wp_json_prepare_data( $data );

		default:
			return null;
	}
}

/**
 * Send a JSON response back to an Ajax request.
 *
 * @since 3.5.0
 * @since 4.7.0 The `$status_code` parameter was added.
 *
 * @param mixed $response    Variable (usually an array or object) to encode as JSON,
 *                           then print and die.
 * @param int   $status_code The HTTP status code to output.
 */
function wp_send_json( $response, $status_code = null ) {
	@header( 'Content-Type: application/json; charset=' . get_option( 'blog_charset' ) );
	if ( null !== $status_code ) {
		status_header( $status_code );
	}
	echo wp_json_encode( $response );

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
 * Send a JSON response back to an Ajax request, indicating success.
 *
 * @since 3.5.0
 * @since 4.7.0 The `$status_code` parameter was added.
 *
 * @param mixed $data        Data to encode as JSON, then print and die.
 * @param int   $status_code The HTTP status code to output.
 */
function wp_send_json_success( $data = null, $status_code = null ) {
	$response = array( 'success' => true );

	if ( isset( $data ) ) {
		$response['data'] = $data;
	}

	wp_send_json( $response, $status_code );
}

/**
 * Send a JSON response back to an Ajax request, indicating failure.
 *
 * If the `$data` parameter is a WP_Error object, the errors
 * within the object are processed and output as an array of error
 * codes and corresponding messages. All other types are output
 * without further processing.
 *
 * @since 3.5.0
 * @since 4.1.0 The `$data` parameter is now processed if a WP_Error object is passed in.
 * @since 4.7.0 The `$status_code` parameter was added.
 *
 * @param mixed $data        Data to encode as JSON, then print and die.
 * @param int   $status_code The HTTP status code to output.
 */
function wp_send_json_error( $data = null, $status_code = null ) {
	$response = array( 'success' => false );

	if ( isset( $data ) ) {
		if ( is_wp_error( $data ) ) {
			$result = array();
			foreach ( $data->errors as $code => $messages ) {
				foreach ( $messages as $message ) {
					$result[] = array(
						'code'    => $code,
						'message' => $message,
					);
				}
			}

			$response['data'] = $result;
		} else {
			$response['data'] = $data;
		}
	}

	wp_send_json( $response, $status_code );
}

/**
 * Checks that a JSONP callback is a valid JavaScript callback.
 *
 * Only allows alphanumeric characters and the dot character in callback
 * function names. This helps to mitigate XSS attacks caused by directly
 * outputting user input.
 *
 * @since 4.6.0
 *
 * @param string $callback Supplied JSONP callback function.
 * @return bool True if valid callback, otherwise false.
 */
function wp_check_jsonp_callback( $callback ) {
	if ( ! is_string( $callback ) ) {
		return false;
	}

	preg_replace( '/[^\w\.]/', '', $callback, -1, $illegal_char_count );

	return 0 === $illegal_char_count;
}

/**
 * Retrieve the WordPress home page URL.
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
 * Retrieve the WordPress site URL.
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
 * @return string The WordPress Site URL.
 */
function _config_wp_siteurl( $url = '' ) {
	if ( defined( 'WP_SITEURL' ) ) {
		return untrailingslashit( WP_SITEURL );
	}
	return $url;
}

/**
 * Delete the fresh site option.
 *
 * @since 4.7.0
 * @access private
 */
function _delete_option_fresh_site() {
	update_option( 'fresh_site', '0' );
}

/**
 * Set the localized direction for MCE plugin.
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

		if ( ! empty( $mce_init['plugins'] ) && strpos( $mce_init['plugins'], 'directionality' ) === false ) {
			$mce_init['plugins'] .= ',directionality';
		}

		if ( ! empty( $mce_init['toolbar1'] ) && ! preg_match( '/\bltr\b/', $mce_init['toolbar1'] ) ) {
			$mce_init['toolbar1'] .= ',ltr';
		}
	}

	return $mce_init;
}


/**
 * Convert smiley code to the icon graphic file equivalent.
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
 * @global array $wpsmiliestrans
 * @global array $wp_smiliessearch
 *
 * @since 2.2.0
 */
function smilies_init() {
	global $wpsmiliestrans, $wp_smiliessearch;

	// don't bother setting up smilies if they are disabled
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
	 * @param array $wpsmiliestrans List of the smilies.
	 */
	$wpsmiliestrans = apply_filters( 'smilies', $wpsmiliestrans );

	if ( count( $wpsmiliestrans ) == 0 ) {
		return;
	}

	/*
	 * NOTE: we sort the smilies in reverse key order. This is to make sure
	 * we match the longest possible smilie (:???: vs :?) as the regular
	 * expression used below is first-match
	 */
	krsort( $wpsmiliestrans );

	$spaces = wp_spaces_regexp();

	// Begin first "subpattern"
	$wp_smiliessearch = '/(?<=' . $spaces . '|^)';

	$subchar = '';
	foreach ( (array) $wpsmiliestrans as $smiley => $img ) {
		$firstchar = substr( $smiley, 0, 1 );
		$rest      = substr( $smiley, 1 );

		// new subpattern?
		if ( $firstchar != $subchar ) {
			if ( $subchar != '' ) {
				$wp_smiliessearch .= ')(?=' . $spaces . '|$)';  // End previous "subpattern"
				$wp_smiliessearch .= '|(?<=' . $spaces . '|^)'; // Begin another "subpattern"
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
 * Merge user defined arguments into defaults array.
 *
 * This function is used throughout WordPress to allow for both string or array
 * to be merged into another array.
 *
 * @since 2.2.0
 * @since 2.3.0 `$args` can now also be an object.
 *
 * @param string|array|object $args     Value to merge with $defaults.
 * @param array               $defaults Optional. Array that serves as the defaults. Default empty.
 * @return array Merged user defined values with defaults.
 */
function wp_parse_args( $args, $defaults = '' ) {
	if ( is_object( $args ) ) {
		$r = get_object_vars( $args );
	} elseif ( is_array( $args ) ) {
		$r =& $args;
	} else {
		wp_parse_str( $args, $r );
	}

	if ( is_array( $defaults ) ) {
		return array_merge( $defaults, $r );
	}
	return $r;
}

/**
 * Cleans up an array, comma- or space-separated list of scalar values.
 *
 * @since 5.1.0
 *
 * @param array|string $list List of values.
 * @return array Sanitized array of values.
 */
function wp_parse_list( $list ) {
	if ( ! is_array( $list ) ) {
		return preg_split( '/[\s,]+/', $list, -1, PREG_SPLIT_NO_EMPTY );
	}

	return $list;
}

/**
 * Clean up an array, comma- or space-separated list of IDs.
 *
 * @since 3.0.0
 *
 * @param array|string $list List of ids.
 * @return array Sanitized array of IDs.
 */
function wp_parse_id_list( $list ) {
	$list = wp_parse_list( $list );

	return array_unique( array_map( 'absint', $list ) );
}

/**
 * Clean up an array, comma- or space-separated list of slugs.
 *
 * @since 4.7.0
 *
 * @param  array|string $list List of slugs.
 * @return array Sanitized array of slugs.
 */
function wp_parse_slug_list( $list ) {
	$list = wp_parse_list( $list );

	return array_unique( array_map( 'sanitize_title', $list ) );
}

/**
 * Extract a slice of an array, given a list of keys.
 *
 * @since 3.1.0
 *
 * @param array $array The original array.
 * @param array $keys  The list of keys.
 * @return array The array slice.
 */
function wp_array_slice_assoc( $array, $keys ) {
	$slice = array();
	foreach ( $keys as $key ) {
		if ( isset( $array[ $key ] ) ) {
			$slice[ $key ] = $array[ $key ];
		}
	}

	return $slice;
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
 * @since 3.0.0
 * @since 4.7.0 Uses `WP_List_Util` class.
 *
 * @param array       $list     An array of objects to filter
 * @param array       $args     Optional. An array of key => value arguments to match
 *                              against each object. Default empty array.
 * @param string      $operator Optional. The logical operation to perform. 'or' means
 *                              only one element from the array needs to match; 'and'
 *                              means all elements must match; 'not' means no elements may
 *                              match. Default 'and'.
 * @param bool|string $field    A field from the object to place instead of the entire object.
 *                              Default false.
 * @return array A list of objects or object fields.
 */
function wp_filter_object_list( $list, $args = array(), $operator = 'and', $field = false ) {
	if ( ! is_array( $list ) ) {
		return array();
	}

	$util = new WP_List_Util( $list );

	$util->filter( $args, $operator );

	if ( $field ) {
		$util->pluck( $field );
	}

	return $util->get_output();
}

/**
 * Filters a list of objects, based on a set of key => value arguments.
 *
 * @since 3.1.0
 * @since 4.7.0 Uses `WP_List_Util` class.
 *
 * @param array  $list     An array of objects to filter.
 * @param array  $args     Optional. An array of key => value arguments to match
 *                         against each object. Default empty array.
 * @param string $operator Optional. The logical operation to perform. 'AND' means
 *                         all elements from the array must match. 'OR' means only
 *                         one element needs to match. 'NOT' means no elements may
 *                         match. Default 'AND'.
 * @return array Array of found values.
 */
function wp_list_filter( $list, $args = array(), $operator = 'AND' ) {
	if ( ! is_array( $list ) ) {
		return array();
	}

	$util = new WP_List_Util( $list );
	return $util->filter( $args, $operator );
}

/**
 * Pluck a certain field out of each object in a list.
 *
 * This has the same functionality and prototype of
 * array_column() (PHP 5.5) but also supports objects.
 *
 * @since 3.1.0
 * @since 4.0.0 $index_key parameter added.
 * @since 4.7.0 Uses `WP_List_Util` class.
 *
 * @param array      $list      List of objects or arrays
 * @param int|string $field     Field from the object to place instead of the entire object
 * @param int|string $index_key Optional. Field from the object to use as keys for the new array.
 *                              Default null.
 * @return array Array of found values. If `$index_key` is set, an array of found values with keys
 *               corresponding to `$index_key`. If `$index_key` is null, array keys from the original
 *               `$list` will be preserved in the results.
 */
function wp_list_pluck( $list, $field, $index_key = null ) {
	$util = new WP_List_Util( $list );
	return $util->pluck( $field, $index_key );
}

/**
 * Sorts a list of objects, based on one or more orderby arguments.
 *
 * @since 4.7.0
 *
 * @param array        $list          An array of objects to sort.
 * @param string|array $orderby       Optional. Either the field name to order by or an array
 *                                    of multiple orderby fields as $orderby => $order.
 * @param string       $order         Optional. Either 'ASC' or 'DESC'. Only used if $orderby
 *                                    is a string.
 * @param bool         $preserve_keys Optional. Whether to preserve keys. Default false.
 * @return array The sorted array.
 */
function wp_list_sort( $list, $orderby = array(), $order = 'ASC', $preserve_keys = false ) {
	if ( ! is_array( $list ) ) {
		return array();
	}

	$util = new WP_List_Util( $list );
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
	 * Passing a falsey value to the filter will effectively short-circuit
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

	require_once( ABSPATH . WPINC . '/default-widgets.php' );

	add_action( '_admin_menu', 'wp_widgets_add_menu' );
}

/**
 * Append the Widgets menu to the themes main menu.
 *
 * @since 2.2.0
 *
 * @global array $submenu
 */
function wp_widgets_add_menu() {
	global $submenu;

	if ( ! current_theme_supports( 'widgets' ) ) {
		return;
	}

	$submenu['themes.php'][7] = array( __( 'Widgets' ), 'edit_theme_options', 'widgets.php' );
	ksort( $submenu['themes.php'], SORT_NUMERIC );
}

/**
 * Flush all output buffers for PHP 5.2.
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
 * Load custom DB error or display WordPress DB error.
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
		require_once( WP_CONTENT_DIR . '/db-error.php' );
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
 * Convert a value to non-negative integer.
 *
 * @since 2.5.0
 *
 * @param mixed $maybeint Data you wish to have converted to a non-negative integer.
 * @return int A non-negative integer.
 */
function absint( $maybeint ) {
	return abs( intval( $maybeint ) );
}

/**
 * Mark a function as deprecated and inform when it has been used.
 *
 * There is a {@see 'hook deprecated_function_run'} that will be called that can be used
 * to get the backtrace up to what file and function called the deprecated
 * function.
 *
 * The current behavior is to trigger a user error if `WP_DEBUG` is true.
 *
 * This function is to be used in every function that is deprecated.
 *
 * @since 2.5.0
 * @access private
 *
 * @param string $function    The function that was called.
 * @param string $version     The version of WordPress that deprecated the function.
 * @param string $replacement Optional. The function that should have been called. Default null.
 */
function _deprecated_function( $function, $version, $replacement = null ) {

	/**
	 * Fires when a deprecated function is called.
	 *
	 * @since 2.5.0
	 *
	 * @param string $function    The function that was called.
	 * @param string $replacement The function that should have been called.
	 * @param string $version     The version of WordPress that deprecated the function.
	 */
	do_action( 'deprecated_function_run', $function, $replacement, $version );

	/**
	 * Filters whether to trigger an error for deprecated functions.
	 *
	 * @since 2.5.0
	 *
	 * @param bool $trigger Whether to trigger the error for deprecated functions. Default true.
	 */
	if ( WP_DEBUG && apply_filters( 'deprecated_function_trigger_error', true ) ) {
		if ( function_exists( '__' ) ) {
			if ( ! is_null( $replacement ) ) {
				/* translators: 1: PHP function name, 2: version number, 3: alternative function name */
				trigger_error( sprintf( __( '%1$s is <strong>deprecated</strong> since version %2$s! Use %3$s instead.' ), $function, $version, $replacement ) );
			} else {
				/* translators: 1: PHP function name, 2: version number */
				trigger_error( sprintf( __( '%1$s is <strong>deprecated</strong> since version %2$s with no alternative available.' ), $function, $version ) );
			}
		} else {
			if ( ! is_null( $replacement ) ) {
				trigger_error( sprintf( '%1$s is <strong>deprecated</strong> since version %2$s! Use %3$s instead.', $function, $version, $replacement ) );
			} else {
				trigger_error( sprintf( '%1$s is <strong>deprecated</strong> since version %2$s with no alternative available.', $function, $version ) );
			}
		}
	}
}

/**
 * Marks a constructor as deprecated and informs when it has been used.
 *
 * Similar to _deprecated_function(), but with different strings. Used to
 * remove PHP4 style constructors.
 *
 * The current behavior is to trigger a user error if `WP_DEBUG` is true.
 *
 * This function is to be used in every PHP4 style constructor method that is deprecated.
 *
 * @since 4.3.0
 * @since 4.5.0 Added the `$parent_class` parameter.
 *
 * @access private
 *
 * @param string $class        The class containing the deprecated constructor.
 * @param string $version      The version of WordPress that deprecated the function.
 * @param string $parent_class Optional. The parent class calling the deprecated constructor.
 *                             Default empty string.
 */
function _deprecated_constructor( $class, $version, $parent_class = '' ) {

	/**
	 * Fires when a deprecated constructor is called.
	 *
	 * @since 4.3.0
	 * @since 4.5.0 Added the `$parent_class` parameter.
	 *
	 * @param string $class        The class containing the deprecated constructor.
	 * @param string $version      The version of WordPress that deprecated the function.
	 * @param string $parent_class The parent class calling the deprecated constructor.
	 */
	do_action( 'deprecated_constructor_run', $class, $version, $parent_class );

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
			if ( ! empty( $parent_class ) ) {
				/* translators: 1: PHP class name, 2: PHP parent class name, 3: version number, 4: __construct() method */
				trigger_error(
					sprintf(
						__( 'The called constructor method for %1$s in %2$s is <strong>deprecated</strong> since version %3$s! Use %4$s instead.' ),
						$class,
						$parent_class,
						$version,
						'<pre>__construct()</pre>'
					)
				);
			} else {
				/* translators: 1: PHP class name, 2: version number, 3: __construct() method */
				trigger_error(
					sprintf(
						__( 'The called constructor method for %1$s is <strong>deprecated</strong> since version %2$s! Use %3$s instead.' ),
						$class,
						$version,
						'<pre>__construct()</pre>'
					)
				);
			}
		} else {
			if ( ! empty( $parent_class ) ) {
				trigger_error(
					sprintf(
						'The called constructor method for %1$s in %2$s is <strong>deprecated</strong> since version %3$s! Use %4$s instead.',
						$class,
						$parent_class,
						$version,
						'<pre>__construct()</pre>'
					)
				);
			} else {
				trigger_error(
					sprintf(
						'The called constructor method for %1$s is <strong>deprecated</strong> since version %2$s! Use %3$s instead.',
						$class,
						$version,
						'<pre>__construct()</pre>'
					)
				);
			}
		}
	}

}

/**
 * Mark a file as deprecated and inform when it has been used.
 *
 * There is a hook {@see 'deprecated_file_included'} that will be called that can be used
 * to get the backtrace up to what file and function included the deprecated
 * file.
 *
 * The current behavior is to trigger a user error if `WP_DEBUG` is true.
 *
 * This function is to be used in every file that is deprecated.
 *
 * @since 2.5.0
 * @access private
 *
 * @param string $file        The file that was included.
 * @param string $version     The version of WordPress that deprecated the file.
 * @param string $replacement Optional. The file that should have been included based on ABSPATH.
 *                            Default null.
 * @param string $message     Optional. A message regarding the change. Default empty.
 */
function _deprecated_file( $file, $version, $replacement = null, $message = '' ) {

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
			if ( ! is_null( $replacement ) ) {
				/* translators: 1: PHP file name, 2: version number, 3: alternative file name */
				trigger_error( sprintf( __( '%1$s is <strong>deprecated</strong> since version %2$s! Use %3$s instead.' ), $file, $version, $replacement ) . $message );
			} else {
				/* translators: 1: PHP file name, 2: version number */
				trigger_error( sprintf( __( '%1$s is <strong>deprecated</strong> since version %2$s with no alternative available.' ), $file, $version ) . $message );
			}
		} else {
			if ( ! is_null( $replacement ) ) {
				trigger_error( sprintf( '%1$s is <strong>deprecated</strong> since version %2$s! Use %3$s instead.', $file, $version, $replacement ) . $message );
			} else {
				trigger_error( sprintf( '%1$s is <strong>deprecated</strong> since version %2$s with no alternative available.', $file, $version ) . $message );
			}
		}
	}
}
/**
 * Mark a function argument as deprecated and inform when it has been used.
 *
 * This function is to be used whenever a deprecated function argument is used.
 * Before this function is called, the argument must be checked for whether it was
 * used by comparing it to its default value or evaluating whether it is empty.
 * For example:
 *
 *     if ( ! empty( $deprecated ) ) {
 *         _deprecated_argument( __FUNCTION__, '3.0.0' );
 *     }
 *
 * There is a hook deprecated_argument_run that will be called that can be used
 * to get the backtrace up to what file and function used the deprecated
 * argument.
 *
 * The current behavior is to trigger a user error if WP_DEBUG is true.
 *
 * @since 3.0.0
 * @access private
 *
 * @param string $function The function that was called.
 * @param string $version  The version of WordPress that deprecated the argument used.
 * @param string $message  Optional. A message regarding the change. Default null.
 */
function _deprecated_argument( $function, $version, $message = null ) {

	/**
	 * Fires when a deprecated argument is called.
	 *
	 * @since 3.0.0
	 *
	 * @param string $function The function that was called.
	 * @param string $message  A message regarding the change.
	 * @param string $version  The version of WordPress that deprecated the argument used.
	 */
	do_action( 'deprecated_argument_run', $function, $message, $version );

	/**
	 * Filters whether to trigger an error for deprecated arguments.
	 *
	 * @since 3.0.0
	 *
	 * @param bool $trigger Whether to trigger the error for deprecated arguments. Default true.
	 */
	if ( WP_DEBUG && apply_filters( 'deprecated_argument_trigger_error', true ) ) {
		if ( function_exists( '__' ) ) {
			if ( ! is_null( $message ) ) {
				/* translators: 1: PHP function name, 2: version number, 3: optional message regarding the change */
				trigger_error( sprintf( __( '%1$s was called with an argument that is <strong>deprecated</strong> since version %2$s! %3$s' ), $function, $version, $message ) );
			} else {
				/* translators: 1: PHP function name, 2: version number */
				trigger_error( sprintf( __( '%1$s was called with an argument that is <strong>deprecated</strong> since version %2$s with no alternative available.' ), $function, $version ) );
			}
		} else {
			if ( ! is_null( $message ) ) {
				trigger_error( sprintf( '%1$s was called with an argument that is <strong>deprecated</strong> since version %2$s! %3$s', $function, $version, $message ) );
			} else {
				trigger_error( sprintf( '%1$s was called with an argument that is <strong>deprecated</strong> since version %2$s with no alternative available.', $function, $version ) );
			}
		}
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
 * @access private
 *
 * @param string $hook        The hook that was used.
 * @param string $version     The version of WordPress that deprecated the hook.
 * @param string $replacement Optional. The hook that should have been used.
 * @param string $message     Optional. A message regarding the change.
 */
function _deprecated_hook( $hook, $version, $replacement = null, $message = null ) {
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
		if ( ! is_null( $replacement ) ) {
			/* translators: 1: WordPress hook name, 2: version number, 3: alternative hook name */
			trigger_error( sprintf( __( '%1$s is <strong>deprecated</strong> since version %2$s! Use %3$s instead.' ), $hook, $version, $replacement ) . $message );
		} else {
			/* translators: 1: WordPress hook name, 2: version number */
			trigger_error( sprintf( __( '%1$s is <strong>deprecated</strong> since version %2$s with no alternative available.' ), $hook, $version ) . $message );
		}
	}
}

/**
 * Mark something as being incorrectly called.
 *
 * There is a hook {@see 'doing_it_wrong_run'} that will be called that can be used
 * to get the backtrace up to what file and function called the deprecated
 * function.
 *
 * The current behavior is to trigger a user error if `WP_DEBUG` is true.
 *
 * @since 3.1.0
 * @access private
 *
 * @param string $function The function that was called.
 * @param string $message  A message explaining what has been done incorrectly.
 * @param string $version  The version of WordPress where the message was added.
 */
function _doing_it_wrong( $function, $message, $version ) {

	/**
	 * Fires when the given function is being used incorrectly.
	 *
	 * @since 3.1.0
	 *
	 * @param string $function The function that was called.
	 * @param string $message  A message explaining what has been done incorrectly.
	 * @param string $version  The version of WordPress where the message was added.
	 */
	do_action( 'doing_it_wrong_run', $function, $message, $version );

	/**
	 * Filters whether to trigger an error for _doing_it_wrong() calls.
	 *
	 * @since 3.1.0
	 * @since 5.1.0 Added the $function, $message and $version parameters.
	 *
	 * @param bool   $trigger  Whether to trigger the error for _doing_it_wrong() calls. Default true.
	 * @param string $function The function that was called.
	 * @param string $message  A message explaining what has been done incorrectly.
	 * @param string $version  The version of WordPress where the message was added.
	 */
	if ( WP_DEBUG && apply_filters( 'doing_it_wrong_trigger_error', true, $function, $message, $version ) ) {
		if ( function_exists( '__' ) ) {
			if ( is_null( $version ) ) {
				$version = '';
			} else {
				/* translators: %s: version number */
				$version = sprintf( __( '(This message was added in version %s.)' ), $version );
			}
			/* translators: %s: Codex URL */
			$message .= ' ' . sprintf(
				__( 'Please see <a href="%s">Debugging in WordPress</a> for more information.' ),
				__( 'https://codex.wordpress.org/Debugging_in_WordPress' )
			);
			/* translators: Developer debugging message. 1: PHP function name, 2: Explanatory message, 3: Version information message */
			trigger_error( sprintf( __( '%1$s was called <strong>incorrectly</strong>. %2$s %3$s' ), $function, $message, $version ) );
		} else {
			if ( is_null( $version ) ) {
				$version = '';
			} else {
				$version = sprintf( '(This message was added in version %s.)', $version );
			}
			$message .= sprintf(
				' Please see <a href="%s">Debugging in WordPress</a> for more information.',
				'https://codex.wordpress.org/Debugging_in_WordPress'
			);
			trigger_error( sprintf( '%1$s was called <strong>incorrectly</strong>. %2$s %3$s', $function, $message, $version ) );
		}
	}
}

/**
 * Is the server running earlier than 1.5.0 version of lighttpd?
 *
 * @since 2.5.0
 *
 * @return bool Whether the server is running lighttpd < 1.5.0.
 */
function is_lighttpd_before_150() {
	$server_parts    = explode( '/', isset( $_SERVER['SERVER_SOFTWARE'] ) ? $_SERVER['SERVER_SOFTWARE'] : '' );
	$server_parts[1] = isset( $server_parts[1] ) ? $server_parts[1] : '';
	return  'lighttpd' == $server_parts[0] && -1 == version_compare( $server_parts[1], '1.5.0' );
}

/**
 * Does the specified module exist in the Apache config?
 *
 * @since 2.5.0
 *
 * @global bool $is_apache
 *
 * @param string $mod     The module, e.g. mod_rewrite.
 * @param bool   $default Optional. The default return value if the module is not found. Default false.
 * @return bool Whether the specified module is loaded.
 */
function apache_mod_loaded( $mod, $default = false ) {
	global $is_apache;

	if ( ! $is_apache ) {
		return false;
	}

	if ( function_exists( 'apache_get_modules' ) ) {
		$mods = apache_get_modules();
		if ( in_array( $mod, $mods ) ) {
			return true;
		}
	} elseif ( function_exists( 'phpinfo' ) && false === strpos( ini_get( 'disable_functions' ), 'phpinfo' ) ) {
			ob_start();
			phpinfo( 8 );
			$phpinfo = ob_get_clean();
		if ( false !== strpos( $phpinfo, $mod ) ) {
			return true;
		}
	}
	return $default;
}

/**
 * Check if IIS 7+ supports pretty permalinks.
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
		 * Next we check if the URL Rewrite Module 1.1 is loaded and enabled for the web site. When
		 * URL Rewrite 1.1 is loaded it always sets a server variable called 'IIS_UrlRewriteModule'.
		 * Lastly we make sure that PHP is running via FastCGI. This is important because if it runs
		 * via ISAPI then pretty permalinks will not work.
		 */
		$supports_permalinks = class_exists( 'DOMDocument', false ) && isset( $_SERVER['IIS_UrlRewriteModule'] ) && ( PHP_SAPI == 'cgi-fcgi' );
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
 * @param string $file          File path.
 * @param array  $allowed_files Optional. List of allowed files.
 * @return int 0 means nothing is wrong, greater than 0 means something was wrong.
 */
function validate_file( $file, $allowed_files = array() ) {
	// Normalize path for Windows servers
	$file = wp_normalize_path( $file );

	// `../` on its own is not allowed:
	if ( '../' === $file ) {
		return 1;
	}

	// More than one occurence of `../` is not allowed:
	if ( preg_match_all( '#\.\./#', $file, $matches, PREG_SET_ORDER ) && ( count( $matches ) > 1 ) ) {
		return 1;
	}

	// `../` which does not occur at the end of the path is not allowed:
	if ( false !== strpos( $file, '../' ) && '../' !== mb_substr( $file, -3, 3 ) ) {
		return 1;
	}

	// Files not in the allowed file list are not allowed:
	if ( ! empty( $allowed_files ) && ! in_array( $file, $allowed_files ) ) {
		return 3;
	}

	// Absolute Windows drive paths are not allowed:
	if ( ':' == substr( $file, 1, 1 ) ) {
		return 2;
	}

	return 0;
}

/**
 * Whether to force SSL used for the Administration Screens.
 *
 * @since 2.6.0
 *
 * @staticvar bool $forced
 *
 * @param string|bool $force Optional. Whether to force SSL in admin screens. Default null.
 * @return bool True if forced, false if not forced.
 */
function force_ssl_admin( $force = null ) {
	static $forced = false;

	if ( ! is_null( $force ) ) {
		$old_forced = $forced;
		$forced     = $force;
		return $old_forced;
	}

	return $forced;
}

/**
 * Guess the URL for the site.
 *
 * Will remove wp-admin links to retrieve only return URLs not in the wp-admin
 * directory.
 *
 * @since 2.6.0
 *
 * @return string The guessed URL.
 */
function wp_guess_url() {
	if ( defined( 'WP_SITEURL' ) && '' != WP_SITEURL ) {
		$url = WP_SITEURL;
	} else {
		$abspath_fix         = str_replace( '\\', '/', ABSPATH );
		$script_filename_dir = dirname( $_SERVER['SCRIPT_FILENAME'] );

		// The request is for the admin
		if ( strpos( $_SERVER['REQUEST_URI'], 'wp-admin' ) !== false || strpos( $_SERVER['REQUEST_URI'], 'wp-login.php' ) !== false ) {
			$path = preg_replace( '#/(wp-admin/.*|wp-login.php)#i', '', $_SERVER['REQUEST_URI'] );

			// The request is for a file in ABSPATH
		} elseif ( $script_filename_dir . '/' == $abspath_fix ) {
			// Strip off any file/query params in the path
			$path = preg_replace( '#/[^/]*$#i', '', $_SERVER['PHP_SELF'] );

		} else {
			if ( false !== strpos( $_SERVER['SCRIPT_FILENAME'], $abspath_fix ) ) {
				// Request is hitting a file inside ABSPATH
				$directory = str_replace( ABSPATH, '', $script_filename_dir );
				// Strip off the sub directory, and any file/query params
				$path = preg_replace( '#/' . preg_quote( $directory, '#' ) . '/[^/]*$#i', '', $_SERVER['REQUEST_URI'] );
			} elseif ( false !== strpos( $abspath_fix, $script_filename_dir ) ) {
				// Request is hitting a file above ABSPATH
				$subdirectory = substr( $abspath_fix, strpos( $abspath_fix, $script_filename_dir ) + strlen( $script_filename_dir ) );
				// Strip off any file/query params from the path, appending the sub directory to the installation
				$path = preg_replace( '#/[^/]*$#i', '', $_SERVER['REQUEST_URI'] ) . $subdirectory;
			} else {
				$path = $_SERVER['REQUEST_URI'];
			}
		}

		$schema = is_ssl() ? 'https://' : 'http://'; // set_url_scheme() is not defined yet
		$url    = $schema . $_SERVER['HTTP_HOST'] . $path;
	}

	return rtrim( $url, '/' );
}

/**
 * Temporarily suspend cache additions.
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
 * @staticvar bool $_suspend
 *
 * @param bool $suspend Optional. Suspends additions if true, re-enables them if false.
 * @return bool The current suspend setting
 */
function wp_suspend_cache_addition( $suspend = null ) {
	static $_suspend = false;

	if ( is_bool( $suspend ) ) {
		$_suspend = $suspend;
	}

	return $_suspend;
}

/**
 * Suspend cache invalidation.
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
 * Determine whether a site is the main site of the current network.
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

	return $site_id === get_main_site_id( $network_id );
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
 * Determine whether a network is the main network of the Multisite installation.
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

	return ( $network_id === get_main_network_id() );
}

/**
 * Get the main network ID.
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
 * Determine whether global terms are enabled.
 *
 * @since 3.0.0
 *
 * @staticvar bool $global_terms
 *
 * @return bool True if multisite and global terms enabled.
 */
function global_terms_enabled() {
	if ( ! is_multisite() ) {
		return false;
	}

	static $global_terms = null;
	if ( is_null( $global_terms ) ) {

		/**
		 * Filters whether global terms are enabled.
		 *
		 * Passing a non-null value to the filter will effectively short-circuit the function,
		 * returning the value of the 'global_terms_enabled' site option instead.
		 *
		 * @since 3.0.0
		 *
		 * @param null $enabled Whether global terms are enabled.
		 */
		$filter = apply_filters( 'global_terms_enabled', null );
		if ( ! is_null( $filter ) ) {
			$global_terms = (bool) $filter;
		} else {
			$global_terms = (bool) get_site_option( 'global_terms_enabled', false );
		}
	}
	return $global_terms;
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
 * gmt_offset modification for smart timezone handling.
 *
 * Overrides the gmt_offset option if we have a timezone_string available.
 *
 * @since 2.8.0
 *
 * @return float|false Timezone GMT offset, false otherwise.
 */
function wp_timezone_override_offset() {
	if ( ! $timezone_string = get_option( 'timezone_string' ) ) {
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
	// Don't use translated versions of Etc
	if ( 'Etc' === $a['continent'] && 'Etc' === $b['continent'] ) {
		// Make the order of these more like the old dropdown
		if ( 'GMT+' === substr( $a['city'], 0, 4 ) && 'GMT+' === substr( $b['city'], 0, 4 ) ) {
			return -1 * ( strnatcasecmp( $a['city'], $b['city'] ) );
		}
		if ( 'UTC' === $a['city'] ) {
			if ( 'GMT+' === substr( $b['city'], 0, 4 ) ) {
				return 1;
			}
			return -1;
		}
		if ( 'UTC' === $b['city'] ) {
			if ( 'GMT+' === substr( $a['city'], 0, 4 ) ) {
				return -1;
			}
			return 1;
		}
		return strnatcasecmp( $a['city'], $b['city'] );
	}
	if ( $a['t_continent'] == $b['t_continent'] ) {
		if ( $a['t_city'] == $b['t_city'] ) {
			return strnatcasecmp( $a['t_subcity'], $b['t_subcity'] );
		}
		return strnatcasecmp( $a['t_city'], $b['t_city'] );
	} else {
		// Force Etc to the bottom of the list
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
 * @staticvar bool $mo_loaded
 * @staticvar string $locale_loaded
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
		unload_textdomain( 'continents-cities' );
		load_textdomain( 'continents-cities', $mofile );
		$mo_loaded = true;
	}

	$zonen = array();
	foreach ( timezone_identifiers_list() as $zone ) {
		$zone = explode( '/', $zone );
		if ( ! in_array( $zone[0], $continents ) ) {
			continue;
		}

		// This determines what gets set and translated - we don't translate Etc/* strings here, they are done later
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

	foreach ( $zonen as $key => $zone ) {
		// Build value in an array to join later
		$value = array( $zone['continent'] );

		if ( empty( $zone['city'] ) ) {
			// It's at the continent level (generally won't happen)
			$display = $zone['t_continent'];
		} else {
			// It's inside a continent group

			// Continent optgroup
			if ( ! isset( $zonen[ $key - 1 ] ) || $zonen[ $key - 1 ]['continent'] !== $zone['continent'] ) {
				$label       = $zone['t_continent'];
				$structure[] = '<optgroup label="' . esc_attr( $label ) . '">';
			}

			// Add the city to the value
			$value[] = $zone['city'];

			$display = $zone['t_city'];
			if ( ! empty( $zone['subcity'] ) ) {
				// Add the subcity to the value
				$value[]  = $zone['subcity'];
				$display .= ' - ' . $zone['t_subcity'];
			}
		}

		// Build the value
		$value    = join( '/', $value );
		$selected = '';
		if ( $value === $selected_zone ) {
			$selected = 'selected="selected" ';
		}
		$structure[] = '<option ' . $selected . 'value="' . esc_attr( $value ) . '">' . esc_html( $display ) . '</option>';

		// Close continent optgroup
		if ( ! empty( $zone['city'] ) && ( ! isset( $zonen[ $key + 1 ] ) || ( isset( $zonen[ $key + 1 ] ) && $zonen[ $key + 1 ]['continent'] !== $zone['continent'] ) ) ) {
			$structure[] = '</optgroup>';
		}
	}

	// Do UTC
	$structure[] = '<optgroup label="' . esc_attr__( 'UTC' ) . '">';
	$selected    = '';
	if ( 'UTC' === $selected_zone ) {
		$selected = 'selected="selected" ';
	}
	$structure[] = '<option ' . $selected . 'value="' . esc_attr( 'UTC' ) . '">' . __( 'UTC' ) . '</option>';
	$structure[] = '</optgroup>';

	// Do manual UTC offsets
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

	return join( "\n", $structure );
}

/**
 * Strip close comment and close php tags from file headers used by WP.
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
 * Permanently delete comments or posts of any type that have held a status
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

		if ( ! $del_post || 'trash' != $del_post->post_status ) {
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

		if ( ! $del_comment || 'trash' != $del_comment->comment_approved ) {
			delete_comment_meta( $comment_id, '_wp_trash_meta_time' );
			delete_comment_meta( $comment_id, '_wp_trash_meta_status' );
		} else {
			wp_delete_comment( $del_comment );
		}
	}
}

/**
 * Retrieve metadata from a file.
 *
 * Searches for metadata in the first 8kiB of a file, such as a plugin or theme.
 * Each piece of metadata must be on its own line. Fields can not span multiple
 * lines, the value will get cut at the end of the first line.
 *
 * If the file data is not within that first 8kiB, then the author should correct
 * their plugin file and move the data headers to the top.
 *
 * @link https://codex.wordpress.org/File_Header
 *
 * @since 2.9.0
 *
 * @param string $file            Absolute path to the file.
 * @param array  $default_headers List of headers, in the format `array('HeaderKey' => 'Header Name')`.
 * @param string $context         Optional. If specified adds filter hook {@see 'extra_$context_headers'}.
 *                                Default empty.
 * @return array Array of file headers in `HeaderKey => Header Value` format.
 */
function get_file_data( $file, $default_headers, $context = '' ) {
	// We don't need to write to the file, so just open for reading.
	$fp = fopen( $file, 'r' );

	// Pull only the first 8kiB of the file in.
	$file_data = fread( $fp, 8192 );

	// PHP will close file handle, but we are good citizens.
	fclose( $fp );

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
	if ( $context && $extra_headers = apply_filters( "extra_{$context}_headers", array() ) ) {
		$extra_headers = array_combine( $extra_headers, $extra_headers ); // keys equal values
		$all_headers   = array_merge( $extra_headers, (array) $default_headers );
	} else {
		$all_headers = $default_headers;
	}

	foreach ( $all_headers as $field => $regex ) {
		if ( preg_match( '/^[ \t\/*#@]*' . preg_quote( $regex, '/' ) . ':(.*)$/mi', $file_data, $match ) && $match[1] ) {
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
function __return_true() {
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
function __return_false() {
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
function __return_zero() {
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
function __return_empty_array() {
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
function __return_null() {
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
function __return_empty_string() {
	return '';
}

/**
 * Send a HTTP header to disable content type sniffing in browsers which support it.
 *
 * @since 3.0.0
 *
 * @see https://blogs.msdn.com/ie/archive/2008/07/02/ie8-security-part-v-comprehensive-protection.aspx
 * @see https://src.chromium.org/viewvc/chrome?view=rev&revision=6985
 */
function send_nosniff_header() {
	@header( 'X-Content-Type-Options: nosniff' );
}

/**
 * Return a MySQL expression for selecting the week number based on the start_of_week option.
 *
 * @ignore
 * @since 3.0.0
 *
 * @param string $column Database column.
 * @return string SQL clause.
 */
function _wp_mysql_week( $column ) {
	switch ( $start_of_week = (int) get_option( 'start_of_week' ) ) {
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
 * Find hierarchy loops using a callback function that maps object IDs to parent IDs.
 *
 * @since 3.1.0
 * @access private
 *
 * @param callable $callback      Function that accepts ( ID, $callback_args ) and outputs parent_ID.
 * @param int      $start         The ID to start the loop check at.
 * @param int      $start_parent  The parent_ID of $start to use instead of calling $callback( $start ).
 *                                Use null to always use $callback
 * @param array    $callback_args Optional. Additional arguments to send to $callback.
 * @return array IDs of all members of loop.
 */
function wp_find_hierarchy_loop( $callback, $start, $start_parent, $callback_args = array() ) {
	$override = is_null( $start_parent ) ? array() : array( $start => $start_parent );

	if ( ! $arbitrary_loop_member = wp_find_hierarchy_loop_tortoise_hare( $callback, $start, $override, $callback_args ) ) {
		return array();
	}

	return wp_find_hierarchy_loop_tortoise_hare( $callback, $arbitrary_loop_member, $override, $callback_args, true );
}

/**
 * Use the "The Tortoise and the Hare" algorithm to detect loops.
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
	$tortoise = $hare = $evanescent_hare = $start;
	$return   = array();

	// Set evanescent_hare to one past hare
	// Increment hare two steps
	while (
		$tortoise
	&&
		( $evanescent_hare = isset( $override[ $hare ] ) ? $override[ $hare ] : call_user_func_array( $callback, array_merge( array( $hare ), $callback_args ) ) )
	&&
		( $hare = isset( $override[ $evanescent_hare ] ) ? $override[ $evanescent_hare ] : call_user_func_array( $callback, array_merge( array( $evanescent_hare ), $callback_args ) ) )
	) {
		if ( $_return_loop ) {
			$return[ $tortoise ] = $return[ $evanescent_hare ] = $return[ $hare ] = true;
		}

		// tortoise got lapped - must be a loop
		if ( $tortoise == $evanescent_hare || $tortoise == $hare ) {
			return $_return_loop ? $return : $tortoise;
		}

		// Increment tortoise by one step
		$tortoise = isset( $override[ $tortoise ] ) ? $override[ $tortoise ] : call_user_func_array( $callback, array_merge( array( $tortoise ), $callback_args ) );
	}

	return false;
}

/**
 * Send a HTTP header to limit rendering of pages to same origin iframes.
 *
 * @since 3.1.3
 *
 * @see https://developer.mozilla.org/en/the_x-frame-options_response_header
 */
function send_frame_options_header() {
	@header( 'X-Frame-Options: SAMEORIGIN' );
}

/**
 * Retrieve a list of protocols to allow in HTML attributes.
 *
 * @since 3.3.0
 * @since 4.3.0 Added 'webcal' to the protocols array.
 * @since 4.7.0 Added 'urn' to the protocols array.
 *
 * @see wp_kses()
 * @see esc_url()
 *
 * @staticvar array $protocols
 *
 * @return string[] Array of allowed protocols. Defaults to an array containing 'http', 'https',
 *                  'ftp', 'ftps', 'mailto', 'news', 'irc', 'gopher', 'nntp', 'feed', 'telnet',
 *                  'mms', 'rtsp', 'svn', 'tel', 'fax', 'xmpp', 'webcal', and 'urn'. This covers
 *                  all common link protocols, except for 'javascript' which should not be
 *                  allowed for untrusted users.
 */
function wp_allowed_protocols() {
	static $protocols = array();

	if ( empty( $protocols ) ) {
		$protocols = array( 'http', 'https', 'ftp', 'ftps', 'mailto', 'news', 'irc', 'gopher', 'nntp', 'feed', 'telnet', 'mms', 'rtsp', 'svn', 'tel', 'fax', 'xmpp', 'webcal', 'urn' );
	}

	if ( ! did_action( 'wp_loaded' ) ) {
		/**
		 * Filters the list of protocols allowed in HTML attributes.
		 *
		 * @since 3.0.0
		 *
		 * @param array $protocols Array of allowed protocols e.g. 'http', 'ftp', 'tel', and more.
		 */
		$protocols = array_unique( (array) apply_filters( 'kses_allowed_protocols', $protocols ) );
	}

	return $protocols;
}

/**
 * Return a comma-separated string of functions that have been called to get
 * to the current point in code.
 *
 * @since 3.4.0
 *
 * @see https://core.trac.wordpress.org/ticket/19589
 *
 * @staticvar array $truncate_paths Array of paths to truncate.
 *
 * @param string $ignore_class Optional. A class to ignore all function calls within - useful
 *                             when you want to just give info about the callee. Default null.
 * @param int    $skip_frames  Optional. A number of stack frames to skip - useful for unwinding
 *                             back to the source of the issue. Default 0.
 * @param bool   $pretty       Optional. Whether or not you want a comma separated string or raw
 *                             array returned. Default true.
 * @return string|array Either a string containing a reversed comma separated trace or an array
 *                      of individual calls.
 */
function wp_debug_backtrace_summary( $ignore_class = null, $skip_frames = 0, $pretty = true ) {
	static $truncate_paths;

	if ( version_compare( PHP_VERSION, '5.2.5', '>=' ) ) {
		$trace = debug_backtrace( false );
	} else {
		$trace = debug_backtrace();
	}

	$caller      = array();
	$check_class = ! is_null( $ignore_class );
	$skip_frames++; // skip this function

	if ( ! isset( $truncate_paths ) ) {
		$truncate_paths = array(
			wp_normalize_path( WP_CONTENT_DIR ),
			wp_normalize_path( ABSPATH ),
		);
	}

	foreach ( $trace as $call ) {
		if ( $skip_frames > 0 ) {
			$skip_frames--;
		} elseif ( isset( $call['class'] ) ) {
			if ( $check_class && $ignore_class == $call['class'] ) {
				continue; // Filter out calls
			}

			$caller[] = "{$call['class']}{$call['type']}{$call['function']}";
		} else {
			if ( in_array( $call['function'], array( 'do_action', 'apply_filters', 'do_action_ref_array', 'apply_filters_ref_array' ) ) ) {
				$caller[] = "{$call['function']}('{$call['args'][0]}')";
			} elseif ( in_array( $call['function'], array( 'include', 'include_once', 'require', 'require_once' ) ) ) {
				$filename = isset( $call['args'][0] ) ? $call['args'][0] : '';
				$caller[] = $call['function'] . "('" . str_replace( $truncate_paths, '', wp_normalize_path( $filename ) ) . "')";
			} else {
				$caller[] = $call['function'];
			}
		}
	}
	if ( $pretty ) {
		return join( ', ', array_reverse( $caller ) );
	} else {
		return $caller;
	}
}

/**
 * Retrieve IDs that are not already present in the cache.
 *
 * @since 3.4.0
 * @access private
 *
 * @param int[]  $object_ids Array of IDs.
 * @param string $cache_key  The cache bucket to check against.
 * @return int[] Array of IDs not present in the cache.
 */
function _get_non_cached_ids( $object_ids, $cache_key ) {
	$clean = array();
	foreach ( $object_ids as $id ) {
		$id = (int) $id;
		if ( ! wp_cache_get( $id, $cache_key ) ) {
			$clean[] = $id;
		}
	}

	return $clean;
}

/**
 * Test if the current device has the capability to upload files.
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

	if ( strpos( $ua, 'iPhone' ) !== false
		|| strpos( $ua, 'iPad' ) !== false
		|| strpos( $ua, 'iPod' ) !== false ) {
			return preg_match( '#OS ([\d_]+) like Mac OS X#', $ua, $version ) && version_compare( $version[1], '6', '>=' );
	}

	return true;
}

/**
 * Test if a given path is a stream URL
 *
 * @since 3.5.0
 *
 * @param string $path The resource path or URL.
 * @return bool True if the path is a stream URL.
 */
function wp_is_stream( $path ) {
	$scheme_separator = strpos( $path, '://' );

	if ( false === $scheme_separator ) {
		// $path isn't a stream
		return false;
	}

	$stream = substr( $path, 0, $scheme_separator );

	return in_array( $stream, stream_get_wrappers(), true );
}

/**
 * Test if the supplied date is valid for the Gregorian calendar.
 *
 * @since 3.5.0
 *
 * @link https://secure.php.net/manual/en/function.checkdate.php
 *
 * @param  int    $month       Month number.
 * @param  int    $day         Day number.
 * @param  int    $year        Year number.
 * @param  string $source_date The date to filter.
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
 * Load the auth check for monitoring whether the user is still logged in.
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
	$show   = ! in_array( $screen->id, $hidden );

	/**
	 * Filters whether to load the authentication check.
	 *
	 * Passing a falsey value to the filter will effectively short-circuit
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
 * Output the HTML that shows the wp-login dialog when the user is no longer logged in.
 *
 * @since 3.6.0
 */
function wp_auth_check_html() {
	$login_url      = wp_login_url();
	$current_domain = ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'];
	$same_domain    = ( strpos( $login_url, $current_domain ) === 0 );

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
	<button type="button" class="wp-auth-check-close button-link"><span class="screen-reader-text"><?php _e( 'Close dialog' ); ?></span></button>
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
 * Check whether a user is still logged in, for the heartbeat.
 *
 * Send a result that shows a log-in box if the user is no longer logged in,
 * or if their cookie is within the grace period.
 *
 * @since 3.6.0
 *
 * @global int $login_grace_period
 *
 * @param array $response  The Heartbeat response.
 * @return array $response The Heartbeat response with 'wp-auth-check' value set.
 */
function wp_auth_check( $response ) {
	$response['wp-auth-check'] = is_user_logged_in() && empty( $GLOBALS['login_grace_period'] );
	return $response;
}

/**
 * Return RegEx body to liberally match an opening HTML tag.
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
		return;
	}
	return sprintf( '<%1$s[^<]*(?:>[\s\S]*<\/%1$s>|\s*\/>)', tag_escape( $tag ) );
}

/**
 * Retrieve a canonical form of the provided charset appropriate for passing to PHP
 * functions such as htmlspecialchars() and charset html attributes.
 *
 * @since 3.6.0
 * @access private
 *
 * @see https://core.trac.wordpress.org/ticket/23688
 *
 * @param string $charset A charset name.
 * @return string The canonical form of the charset.
 */
function _canonical_charset( $charset ) {
	if ( 'utf-8' === strtolower( $charset ) || 'utf8' === strtolower( $charset ) ) {

		return 'UTF-8';
	}

	if ( 'iso-8859-1' === strtolower( $charset ) || 'iso8859-1' === strtolower( $charset ) ) {

		return 'ISO-8859-1';
	}

	return $charset;
}

/**
 * Set the mbstring internal encoding to a binary safe encoding when func_overload
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
 * @staticvar array $encodings
 * @staticvar bool  $overloaded
 *
 * @param bool $reset Optional. Whether to reset the encoding back to a previously-set encoding.
 *                    Default false.
 */
function mbstring_binary_safe_encoding( $reset = false ) {
	static $encodings  = array();
	static $overloaded = null;

	if ( is_null( $overloaded ) ) {
		$overloaded = function_exists( 'mb_internal_encoding' ) && ( ini_get( 'mbstring.func_overload' ) & 2 );
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
 * Reset the mbstring internal encoding to a users previously set encoding.
 *
 * @see mbstring_binary_safe_encoding()
 *
 * @since 3.7.0
 */
function reset_mbstring_encoding() {
	mbstring_binary_safe_encoding( true );
}

/**
 * Filter/validate a variable as a boolean.
 *
 * Alternative to `filter_var( $var, FILTER_VALIDATE_BOOLEAN )`.
 *
 * @since 4.0.0
 *
 * @param mixed $var Boolean value to validate.
 * @return bool Whether the value is validated.
 */
function wp_validate_boolean( $var ) {
	if ( is_bool( $var ) ) {
		return $var;
	}

	if ( is_string( $var ) && 'false' === strtolower( $var ) ) {
		return false;
	}

	return (bool) $var;
}

/**
 * Delete a file
 *
 * @since 4.2.0
 *
 * @param string $file The path to the file to delete.
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
		@unlink( $delete );
	}
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

	if ( false === $real_file || false === $real_directory || strpos( $real_file, trailingslashit( $real_directory ) ) !== 0 ) {
		return false;
	}

	wp_delete_file( $file );

	return true;
}

/**
 * Outputs a small JS snippet on preview tabs/windows to remove `window.name` on unload.
 *
 * This prevents reusing the same tab for a preview when the user has navigated away.
 *
 * @since 4.3.0
 *
 * @global WP_Post $post
 */
function wp_post_preview_js() {
	global $post;

	if ( ! is_preview() || empty( $post ) ) {
		return;
	}

	// Has to match the window name used in post_submit_meta_box()
	$name = 'wp-preview-' . (int) $post->ID;

	?>
	<script>
	( function() {
		var query = document.location.search;

		if ( query && query.indexOf( 'preview=true' ) !== -1 ) {
			window.name = '<?php echo $name; ?>';
		}

		if ( window.addEventListener ) {
			window.addEventListener( 'unload', function() { window.name = ''; }, false );
		}
	}());
	</script>
	<?php
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
 *                        'image', or an arbitrary other context. If an arbitrary context is passed,
 *                        the similarly arbitrary {@see '{$context}_memory_limit'} filter will be
 *                        invoked. Default 'admin'.
 * @return bool|int|string The limit that was set or false on failure.
 */
function wp_raise_memory_limit( $context = 'admin' ) {
	// Exit early if the limit cannot be changed.
	if ( false === wp_is_ini_value_changeable( 'memory_limit' ) ) {
		return false;
	}

	$current_limit     = @ini_get( 'memory_limit' );
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
			 * @param int|string $filtered_limit Maximum memory limit to allocate for images.
			 *                                   Default `WP_MAX_MEMORY_LIMIT` or the original
			 *                                   php.ini `memory_limit`, whichever is higher.
			 *                                   Accepts an integer (bytes), or a shorthand string
			 *                                   notation, such as '256M'.
			 */
			$filtered_limit = apply_filters( 'image_memory_limit', $filtered_limit );
			break;

		default:
			/**
			 * Filters the memory limit allocated for arbitrary contexts.
			 *
			 * The dynamic portion of the hook name, `$context`, refers to an arbitrary
			 * context passed on calling the function. This allows for plugins to define
			 * their own contexts for raising the memory limit.
			 *
			 * @since 4.6.0
			 *
			 * @param int|string $filtered_limit Maximum memory limit to allocate for images.
			 *                                   Default '256M' or the original php.ini `memory_limit`,
			 *                                   whichever is higher. Accepts an integer (bytes), or a
			 *                                   shorthand string notation, such as '256M'.
			 */
			$filtered_limit = apply_filters( "{$context}_memory_limit", $filtered_limit );
			break;
	}

	$filtered_limit_int = wp_convert_hr_to_bytes( $filtered_limit );

	if ( -1 === $filtered_limit_int || ( $filtered_limit_int > $wp_max_limit_int && $filtered_limit_int > $current_limit_int ) ) {
		if ( false !== @ini_set( 'memory_limit', $filtered_limit ) ) {
			return $filtered_limit;
		} else {
			return false;
		}
	} elseif ( -1 === $wp_max_limit_int || $wp_max_limit_int > $current_limit_int ) {
		if ( false !== @ini_set( 'memory_limit', $wp_max_limit ) ) {
			return $wp_max_limit;
		} else {
			return false;
		}
	}

	return false;
}

/**
 * Generate a random UUID (version 4).
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
 * Get unique ID.
 *
 * This is a PHP implementation of Underscore's uniqueId method. A static variable
 * contains an integer that is incremented with each call. This number is returned
 * with the optional prefix. As such the returned value is not universally unique,
 * but it is unique across the life of the PHP process.
 *
 * @since 5.0.3
 *
 * @staticvar int $id_counter
 *
 * @param string $prefix Prefix for the returned ID.
 * @return string Unique ID.
 */
function wp_unique_id( $prefix = '' ) {
	static $id_counter = 0;
	return $prefix . (string) ++$id_counter;
}

/**
 * Get last changed date for the specified cache group.
 *
 * @since 4.7.0
 *
 * @param string $group Where the cache contents are grouped.
 *
 * @return string $last_changed UNIX timestamp with microseconds representing when the group was last changed.
 */
function wp_cache_get_last_changed( $group ) {
	$last_changed = wp_cache_get( 'last_changed', $group );

	if ( ! $last_changed ) {
		$last_changed = microtime();
		wp_cache_set( 'last_changed', $last_changed, $group );
	}

	return $last_changed;
}

/**
 * Send an email to the old site admin email address when the site admin email address changes.
 *
 * @since 4.9.0
 *
 * @param string $old_email   The old site admin email address.
 * @param string $new_email   The new site admin email address.
 * @param string $option_name The relevant database option name.
 */
function wp_site_admin_email_change_notification( $old_email, $new_email, $option_name ) {
	$send = true;

	// Don't send the notification to the default 'admin_email' value.
	if ( 'you@example.com' === $old_email ) {
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
		/* translators: Site admin email change notification email subject. %s: Site title */
		'subject' => __( '[%s] Admin Email Changed' ),
		'message' => $email_change_text,
		'headers' => '',
	);
	// get site name
	$site_name = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );

	/**
	 * Filters the contents of the email notification sent when the site admin email address is changed.
	 *
	 * @since 4.9.0
	 *
	 * @param array $email_change_email {
	 *            Used to build wp_mail().
	 *
	 *            @type string $to      The intended recipient.
	 *            @type string $subject The subject of the email.
	 *            @type string $message The content of the email.
	 *                The following strings have a special meaning and will get replaced dynamically:
	 *                - ###OLD_EMAIL### The old site admin email address.
	 *                - ###NEW_EMAIL### The new site admin email address.
	 *                - ###SITENAME###  The name of the site.
	 *                - ###SITEURL###   The URL to the site.
	 *            @type string $headers Headers.
	 *        }
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
 * Return an anonymized IPv4 or IPv6 address.
 *
 * @since 4.9.6 Abstracted from `WP_Community_Events::get_unsafe_client_ip()`.
 *
 * @param  string $ip_addr        The IPv4 or IPv6 address to be anonymized.
 * @param  bool   $ipv6_fallback  Optional. Whether to return the original IPv6 address if the needed functions
 *                                to anonymize it are not present. Default false, return `::` (unspecified address).
 * @return string  The anonymized IP address.
 */
function wp_privacy_anonymize_ip( $ip_addr, $ipv6_fallback = false ) {
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
 * Return uniform "anonymous" data by type.
 *
 * @since 4.9.6
 *
 * @param  string $type The type of data to be anonymized.
 * @param  string $data Optional The data to be anonymized.
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
			/* translators: deleted text */
			$anonymous = __( '[deleted]' );
			break;
		case 'longtext':
			/* translators: deleted long text */
			$anonymous = __( 'This content was deleted by the author.' );
			break;
		default:
			$anonymous = '';
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
	 *
	 * @param string $exports_url Exports directory URL.
	 */
	return apply_filters( 'wp_privacy_exports_url', $exports_url );
}

/**
 * Schedule a `WP_Cron` job to delete expired export files.
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

	require_once( ABSPATH . 'wp-admin/includes/file.php' );
	$export_files = list_files( $exports_dir, 100, array( 'index.html' ) );

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
 *
 * @param string $before Markup to output before the annotation. Default `<p class="description">`.
 * @param string $after  Markup to output after the annotation. Default `</p>`.
 */
function wp_update_php_annotation( $before = '<p class="description">', $after = '</p>' ) {
	$annotation = wp_get_update_php_annotation();

	if ( $annotation ) {
		echo $before . $annotation . $after;
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
 * @return string $message Update PHP page annotation. An empty string if no custom URLs are provided.
 */
function wp_get_update_php_annotation() {
	$update_url  = wp_get_update_php_url();
	$default_url = wp_get_default_update_php_url();

	if ( $update_url === $default_url ) {
		return '';
	}

	$annotation = sprintf(
		/* translators: %s: default Update PHP page URL */
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
 * Display a button directly linking to a PHP update process.
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
		'<a class="button button-primary" href="%1$s" target="_blank" rel="noopener noreferrer">%2$s <span class="screen-reader-text">%3$s</span><span aria-hidden="true" class="dashicons dashicons-external"></span></a>',
		esc_url( $direct_update_url ),
		__( 'Update PHP' ),
		/* translators: accessibility text */
		__( '(opens in a new tab)' )
	);
	echo '</p>';
}

/**
 * Get the size of a directory.
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
	$dirsize = get_transient( 'dirsize_cache' );

	if ( is_array( $dirsize ) && isset( $dirsize[ $directory ]['size'] ) ) {
		return $dirsize[ $directory ]['size'];
	}

	if ( ! is_array( $dirsize ) ) {
		$dirsize = array();
	}

	// Exclude individual site directories from the total when checking the main site of a network
	// as they are subdirectories and should not be counted.
	if ( is_multisite() && is_main_site() ) {
		$dirsize[ $directory ]['size'] = recurse_dirsize( $directory, $directory . '/sites', $max_execution_time );
	} else {
		$dirsize[ $directory ]['size'] = recurse_dirsize( $directory, null, $max_execution_time );
	}

	set_transient( 'dirsize_cache', $dirsize, HOUR_IN_SECONDS );
	return $dirsize[ $directory ]['size'];
}

/**
 * Get the size of a directory recursively.
 *
 * Used by get_dirsize() to get a directory's size when it contains
 * other directories.
 *
 * @since MU (3.0.0)
 * @since 4.3.0 $exclude parameter added.
 * @since 5.2.0 $max_execution_time parameter added.
 *
 * @param string $directory       Full path of a directory.
 * @param string|array $exclude   Optional. Full path of a subdirectory to exclude from the total, or array of paths.
 *                                Expected without trailing slash(es).
 * @param int $max_execution_time Maximum time to run before giving up. In seconds.
 *                                The timeout is global and is measured from the moment WordPress started to load.
 * @return int|false|null Size in bytes if a valid directory. False if not. Null if timeout.
 */
function recurse_dirsize( $directory, $exclude = null, $max_execution_time = null ) {
	$size = 0;

	$directory = untrailingslashit( $directory );

	if ( ! file_exists( $directory ) || ! is_dir( $directory ) || ! is_readable( $directory ) ) {
		return false;
	}

	if (
		( is_string( $exclude ) && $directory === $exclude ) ||
		( is_array( $exclude ) && in_array( $directory, $exclude, true ) )
	) {
		return false;
	}

	if ( $max_execution_time === null ) {
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

	if ( $handle = opendir( $directory ) ) {
		while ( ( $file = readdir( $handle ) ) !== false ) {
			$path = $directory . '/' . $file;
			if ( $file != '.' && $file != '..' ) {
				if ( is_file( $path ) ) {
					$size += filesize( $path );
				} elseif ( is_dir( $path ) ) {
					$handlesize = recurse_dirsize( $path, $exclude, $max_execution_time );
					if ( $handlesize > 0 ) {
						$size += $handlesize;
					}
				}

				if ( $max_execution_time > 0 && microtime( true ) - WP_START_TIMESTAMP > $max_execution_time ) {
					// Time exceeded. Give up instead of risking a fatal timeout.
					$size = null;
					break;
				}
			}
		}
		closedir( $handle );
	}
	return $size;
}

/**
* Checks compatibility with the current WordPress version.
*
* @since 5.2.0
*
* @param string $required Minimum required WordPress version.
* @return bool True if required version is compatible or empty, false if not.
*/
function is_wp_version_compatible( $required ) {
	return empty( $required ) || version_compare( get_bloginfo( 'version' ), $required, '>=' );
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
	return empty( $required ) || version_compare( phpversion(), $required, '>=' );
}
