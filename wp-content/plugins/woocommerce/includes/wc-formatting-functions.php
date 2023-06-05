<?php
/**
 * WooCommerce Formatting
 *
 * Functions for formatting data.
 *
 * @package WooCommerce\Functions
 * @version 2.1.0
 */

use Automattic\WooCommerce\Utilities\I18nUtil;
use Automattic\WooCommerce\Utilities\NumberUtil;

defined( 'ABSPATH' ) || exit;

/**
 * Converts a string (e.g. 'yes' or 'no') to a bool.
 *
 * @since 3.0.0
 * @param string|bool $string String to convert. If a bool is passed it will be returned as-is.
 * @return bool
 */
function wc_string_to_bool( $string ) {
	return is_bool( $string ) ? $string : ( 'yes' === strtolower( $string ) || 1 === $string || 'true' === strtolower( $string ) || '1' === $string );
}

/**
 * Converts a bool to a 'yes' or 'no'.
 *
 * @since 3.0.0
 * @param bool|string $bool Bool to convert. If a string is passed it will first be converted to a bool.
 * @return string
 */
function wc_bool_to_string( $bool ) {
	if ( ! is_bool( $bool ) ) {
		$bool = wc_string_to_bool( $bool );
	}
	return true === $bool ? 'yes' : 'no';
}

/**
 * Explode a string into an array by $delimiter and remove empty values.
 *
 * @since 3.0.0
 * @param string $string    String to convert.
 * @param string $delimiter Delimiter, defaults to ','.
 * @return array
 */
function wc_string_to_array( $string, $delimiter = ',' ) {
	return is_array( $string ) ? $string : array_filter( explode( $delimiter, $string ) );
}

/**
 * Sanitize taxonomy names. Slug format (no spaces, lowercase).
 * Urldecode is used to reverse munging of UTF8 characters.
 *
 * @param string $taxonomy Taxonomy name.
 * @return string
 */
function wc_sanitize_taxonomy_name( $taxonomy ) {
	return apply_filters( 'sanitize_taxonomy_name', urldecode( sanitize_title( urldecode( $taxonomy ?? '' ) ) ), $taxonomy );
}

/**
 * Sanitize permalink values before insertion into DB.
 *
 * Cannot use wc_clean because it sometimes strips % chars and breaks the user's setting.
 *
 * @since  2.6.0
 * @param  string $value Permalink.
 * @return string
 */
function wc_sanitize_permalink( $value ) {
	global $wpdb;

	$value = $wpdb->strip_invalid_text_for_column( $wpdb->options, 'option_value', $value );

	if ( is_wp_error( $value ) ) {
		$value = '';
	}

	$value = esc_url_raw( trim( $value ) );
	$value = str_replace( 'http://', '', $value );
	return untrailingslashit( $value );
}

/**
 * Gets the filename part of a download URL.
 *
 * @param string $file_url File URL.
 * @return string
 */
function wc_get_filename_from_url( $file_url ) {
	$parts = wp_parse_url( $file_url );
	if ( isset( $parts['path'] ) ) {
		return basename( $parts['path'] );
	}
}

/**
 * Normalise dimensions, unify to cm then convert to wanted unit value.
 *
 * Usage:
 * wc_get_dimension( 55, 'in' );
 * wc_get_dimension( 55, 'in', 'm' );
 *
 * @param int|float $dimension    Dimension.
 * @param string    $to_unit      Unit to convert to.
 *                                Options: 'in', 'mm', 'cm', 'm'.
 * @param string    $from_unit    Unit to convert from.
 *                                Defaults to ''.
 *                                Options: 'in', 'mm', 'cm', 'm'.
 * @return float
 */
function wc_get_dimension( $dimension, $to_unit, $from_unit = '' ) {
	$to_unit = strtolower( $to_unit );

	if ( empty( $from_unit ) ) {
		$from_unit = strtolower( get_option( 'woocommerce_dimension_unit' ) );
	}

	// Unify all units to cm first.
	if ( $from_unit !== $to_unit ) {
		switch ( $from_unit ) {
			case 'in':
				$dimension *= 2.54;
				break;
			case 'm':
				$dimension *= 100;
				break;
			case 'mm':
				$dimension *= 0.1;
				break;
			case 'yd':
				$dimension *= 91.44;
				break;
		}

		// Output desired unit.
		switch ( $to_unit ) {
			case 'in':
				$dimension *= 0.3937;
				break;
			case 'm':
				$dimension *= 0.01;
				break;
			case 'mm':
				$dimension *= 10;
				break;
			case 'yd':
				$dimension *= 0.010936133;
				break;
		}
	}

	return ( $dimension < 0 ) ? 0 : $dimension;
}

/**
 * Normalise weights, unify to kg then convert to wanted unit value.
 *
 * Usage:
 * wc_get_weight(55, 'kg');
 * wc_get_weight(55, 'kg', 'lbs');
 *
 * @param int|float $weight    Weight.
 * @param string    $to_unit   Unit to convert to.
 *                             Options: 'g', 'kg', 'lbs', 'oz'.
 * @param string    $from_unit Unit to convert from.
 *                             Defaults to ''.
 *                             Options: 'g', 'kg', 'lbs', 'oz'.
 * @return float
 */
function wc_get_weight( $weight, $to_unit, $from_unit = '' ) {
	$weight  = (float) $weight;
	$to_unit = strtolower( $to_unit );

	if ( empty( $from_unit ) ) {
		$from_unit = strtolower( get_option( 'woocommerce_weight_unit' ) );
	}

	// Unify all units to kg first.
	if ( $from_unit !== $to_unit ) {
		switch ( $from_unit ) {
			case 'g':
				$weight *= 0.001;
				break;
			case 'lbs':
				$weight *= 0.453592;
				break;
			case 'oz':
				$weight *= 0.0283495;
				break;
		}

		// Output desired unit.
		switch ( $to_unit ) {
			case 'g':
				$weight *= 1000;
				break;
			case 'lbs':
				$weight *= 2.20462;
				break;
			case 'oz':
				$weight *= 35.274;
				break;
		}
	}

	return ( $weight < 0 ) ? 0 : $weight;
}

/**
 * Trim trailing zeros off prices.
 *
 * @param string|float|int $price Price.
 * @return string
 */
function wc_trim_zeros( $price ) {
	return preg_replace( '/' . preg_quote( wc_get_price_decimal_separator(), '/' ) . '0++$/', '', $price );
}

/**
 * Round a tax amount.
 *
 * @param  double $value Amount to round.
 * @param  int    $precision DP to round. Defaults to wc_get_price_decimals.
 * @return float
 */
function wc_round_tax_total( $value, $precision = null ) {
	$precision = is_null( $precision ) ? wc_get_price_decimals() : intval( $precision );

	if ( version_compare( PHP_VERSION, '5.3.0', '>=' ) ) {
		$rounded_tax = NumberUtil::round( $value, $precision, wc_get_tax_rounding_mode() ); // phpcs:ignore PHPCompatibility.FunctionUse.NewFunctionParameters.round_modeFound
	} elseif ( 2 === wc_get_tax_rounding_mode() ) {
		$rounded_tax = wc_legacy_round_half_down( $value, $precision );
	} else {
		$rounded_tax = NumberUtil::round( $value, $precision );
	}

	return apply_filters( 'wc_round_tax_total', $rounded_tax, $value, $precision, WC_TAX_ROUNDING_MODE );
}

/**
 * Round half down in PHP 5.2.
 *
 * @since 3.2.6
 * @param float $value Value to round.
 * @param int   $precision Precision to round down to.
 * @return float
 */
function wc_legacy_round_half_down( $value, $precision ) {
	$value = wc_float_to_string( $value );

	if ( false !== strstr( $value, '.' ) ) {
		$value = explode( '.', $value );

		if ( strlen( $value[1] ) > $precision && substr( $value[1], -1 ) === '5' ) {
			$value[1] = substr( $value[1], 0, -1 ) . '4';
		}

		$value = implode( '.', $value );
	}

	return NumberUtil::round( floatval( $value ), $precision );
}

/**
 * Make a refund total negative.
 *
 * @param float $amount Refunded amount.
 *
 * @return float
 */
function wc_format_refund_total( $amount ) {
	return $amount * -1;
}

/**
 * Format decimal numbers ready for DB storage.
 *
 * Sanitize, optionally remove decimals, and optionally round + trim off zeros.
 *
 * This function does not remove thousands - this should be done before passing a value to the function.
 *
 * @param  float|string $number     Expects either a float or a string with a decimal separator only (no thousands).
 * @param  mixed        $dp number  Number of decimal points to use, blank to use woocommerce_price_num_decimals, or false to avoid all rounding.
 * @param  bool         $trim_zeros From end of string.
 * @return string
 */
function wc_format_decimal( $number, $dp = false, $trim_zeros = false ) {
	$locale   = localeconv();
	$decimals = array( wc_get_price_decimal_separator(), $locale['decimal_point'], $locale['mon_decimal_point'] );

	// Remove locale from string.
	if ( ! is_float( $number ) ) {
		$number = str_replace( $decimals, '.', $number );

		// Convert multiple dots to just one.
		$number = preg_replace( '/\.(?![^.]+$)|[^0-9.-]/', '', wc_clean( $number ) );
	}

	if ( false !== $dp ) {
		$dp     = intval( '' === $dp ? wc_get_price_decimals() : $dp );
		$number = number_format( floatval( $number ), $dp, '.', '' );
	} elseif ( is_float( $number ) ) {
		// DP is false - don't use number format, just return a string using whatever is given. Remove scientific notation using sprintf.
		$number = str_replace( $decimals, '.', sprintf( '%.' . wc_get_rounding_precision() . 'f', $number ) );
		// We already had a float, so trailing zeros are not needed.
		$trim_zeros = true;
	}

	if ( $trim_zeros && strstr( $number, '.' ) ) {
		$number = rtrim( rtrim( $number, '0' ), '.' );
	}

	return $number;
}

/**
 * Convert a float to a string without locale formatting which PHP adds when changing floats to strings.
 *
 * @param  float $float Float value to format.
 * @return string
 */
function wc_float_to_string( $float ) {
	if ( ! is_float( $float ) ) {
		return $float;
	}

	$locale = localeconv();
	$string = strval( $float );
	$string = str_replace( $locale['decimal_point'], '.', $string );

	return $string;
}

/**
 * Format a price with WC Currency Locale settings.
 *
 * @param  string $value Price to localize.
 * @return string
 */
function wc_format_localized_price( $value ) {
	return apply_filters( 'woocommerce_format_localized_price', str_replace( '.', wc_get_price_decimal_separator(), strval( $value ) ), $value );
}

/**
 * Format a decimal with the decimal separator for prices or PHP Locale settings.
 *
 * @param  string $value Decimal to localize.
 * @return string
 */
function wc_format_localized_decimal( $value ) {
	$locale = localeconv();
	$decimal_point = isset( $locale['decimal_point'] ) ? $locale['decimal_point'] : '.';
	$decimal = ( ! empty( wc_get_price_decimal_separator() ) ) ? wc_get_price_decimal_separator() : $decimal_point;
	return apply_filters( 'woocommerce_format_localized_decimal', str_replace( '.', $decimal, strval( $value ) ), $value );
}

/**
 * Format a coupon code.
 *
 * @since  3.0.0
 * @param  string $value Coupon code to format.
 * @return string
 */
function wc_format_coupon_code( $value ) {
	return apply_filters( 'woocommerce_coupon_code', $value );
}

/**
 * Sanitize a coupon code.
 *
 * Uses sanitize_post_field since coupon codes are stored as
 * post_titles - the sanitization and escaping must match.
 *
 * @since  3.6.0
 * @param  string $value Coupon code to format.
 * @return string
 */
function wc_sanitize_coupon_code( $value ) {
	return wp_filter_kses( sanitize_post_field( 'post_title', $value, 0, 'db' ) );
}

/**
 * Clean variables using sanitize_text_field. Arrays are cleaned recursively.
 * Non-scalar values are ignored.
 *
 * @param string|array $var Data to sanitize.
 * @return string|array
 */
function wc_clean( $var ) {
	if ( is_array( $var ) ) {
		return array_map( 'wc_clean', $var );
	} else {
		return is_scalar( $var ) ? sanitize_text_field( $var ) : $var;
	}
}

/**
 * Function wp_check_invalid_utf8 with recursive array support.
 *
 * @param string|array $var Data to sanitize.
 * @return string|array
 */
function wc_check_invalid_utf8( $var ) {
	if ( is_array( $var ) ) {
		return array_map( 'wc_check_invalid_utf8', $var );
	} else {
		return wp_check_invalid_utf8( $var );
	}
}

/**
 * Run wc_clean over posted textarea but maintain line breaks.
 *
 * @since  3.0.0
 * @param  string $var Data to sanitize.
 * @return string
 */
function wc_sanitize_textarea( $var ) {
	return implode( "\n", array_map( 'wc_clean', explode( "\n", $var ) ) );
}

/**
 * Sanitize a string destined to be a tooltip.
 *
 * @since  2.3.10 Tooltips are encoded with htmlspecialchars to prevent XSS. Should not be used in conjunction with esc_attr()
 * @param  string $var Data to sanitize.
 * @return string
 */
function wc_sanitize_tooltip( $var ) {
	return htmlspecialchars(
		wp_kses(
			html_entity_decode( $var ),
			array(
				'br'     => array(),
				'em'     => array(),
				'strong' => array(),
				'small'  => array(),
				'span'   => array(),
				'ul'     => array(),
				'li'     => array(),
				'ol'     => array(),
				'p'      => array(),
			)
		)
	);
}

/**
 * Merge two arrays.
 *
 * @param array $a1 First array to merge.
 * @param array $a2 Second array to merge.
 * @return array
 */
function wc_array_overlay( $a1, $a2 ) {
	foreach ( $a1 as $k => $v ) {
		if ( ! array_key_exists( $k, $a2 ) ) {
			continue;
		}
		if ( is_array( $v ) && is_array( $a2[ $k ] ) ) {
			$a1[ $k ] = wc_array_overlay( $v, $a2[ $k ] );
		} else {
			$a1[ $k ] = $a2[ $k ];
		}
	}
	return $a1;
}

/**
 * Formats a stock amount by running it through a filter.
 *
 * @param  int|float $amount Stock amount.
 * @return int|float
 */
function wc_stock_amount( $amount ) {
	return apply_filters( 'woocommerce_stock_amount', $amount );
}

/**
 * Get the price format depending on the currency position.
 *
 * @return string
 */
function get_woocommerce_price_format() {
	$currency_pos = get_option( 'woocommerce_currency_pos' );
	$format       = '%1$s%2$s';

	switch ( $currency_pos ) {
		case 'left':
			$format = '%1$s%2$s';
			break;
		case 'right':
			$format = '%2$s%1$s';
			break;
		case 'left_space':
			$format = '%1$s&nbsp;%2$s';
			break;
		case 'right_space':
			$format = '%2$s&nbsp;%1$s';
			break;
	}

	return apply_filters( 'woocommerce_price_format', $format, $currency_pos );
}

/**
 * Return the thousand separator for prices.
 *
 * @since  2.3
 * @return string
 */
function wc_get_price_thousand_separator() {
	return stripslashes( apply_filters( 'wc_get_price_thousand_separator', get_option( 'woocommerce_price_thousand_sep' ) ) );
}

/**
 * Return the decimal separator for prices.
 *
 * @since  2.3
 * @return string
 */
function wc_get_price_decimal_separator() {
	$separator = apply_filters( 'wc_get_price_decimal_separator', get_option( 'woocommerce_price_decimal_sep' ) );
	return $separator ? stripslashes( $separator ) : '.';
}

/**
 * Return the number of decimals after the decimal point.
 *
 * @since  2.3
 * @return int
 */
function wc_get_price_decimals() {
	return absint( apply_filters( 'wc_get_price_decimals', get_option( 'woocommerce_price_num_decimals', 2 ) ) );
}

/**
 * Format the price with a currency symbol.
 *
 * @param  float $price Raw price.
 * @param  array $args  Arguments to format a price {
 *     Array of arguments.
 *     Defaults to empty array.
 *
 *     @type bool   $ex_tax_label       Adds exclude tax label.
 *                                      Defaults to false.
 *     @type string $currency           Currency code.
 *                                      Defaults to empty string (Use the result from get_woocommerce_currency()).
 *     @type string $decimal_separator  Decimal separator.
 *                                      Defaults the result of wc_get_price_decimal_separator().
 *     @type string $thousand_separator Thousand separator.
 *                                      Defaults the result of wc_get_price_thousand_separator().
 *     @type string $decimals           Number of decimals.
 *                                      Defaults the result of wc_get_price_decimals().
 *     @type string $price_format       Price format depending on the currency position.
 *                                      Defaults the result of get_woocommerce_price_format().
 * }
 * @return string
 */
function wc_price( $price, $args = array() ) {
	$args = apply_filters(
		'wc_price_args',
		wp_parse_args(
			$args,
			array(
				'ex_tax_label'       => false,
				'currency'           => '',
				'decimal_separator'  => wc_get_price_decimal_separator(),
				'thousand_separator' => wc_get_price_thousand_separator(),
				'decimals'           => wc_get_price_decimals(),
				'price_format'       => get_woocommerce_price_format(),
			)
		)
	);

	$original_price = $price;

	// Convert to float to avoid issues on PHP 8.
	$price = (float) $price;

	$unformatted_price = $price;
	$negative          = $price < 0;

	/**
	 * Filter raw price.
	 *
	 * @param float        $raw_price      Raw price.
	 * @param float|string $original_price Original price as float, or empty string. Since 5.0.0.
	 */
	$price = apply_filters( 'raw_woocommerce_price', $negative ? $price * -1 : $price, $original_price );

	/**
	 * Filter formatted price.
	 *
	 * @param float        $formatted_price    Formatted price.
	 * @param float        $price              Unformatted price.
	 * @param int          $decimals           Number of decimals.
	 * @param string       $decimal_separator  Decimal separator.
	 * @param string       $thousand_separator Thousand separator.
	 * @param float|string $original_price     Original price as float, or empty string. Since 5.0.0.
	 */
	$price = apply_filters( 'formatted_woocommerce_price', number_format( $price, $args['decimals'], $args['decimal_separator'], $args['thousand_separator'] ), $price, $args['decimals'], $args['decimal_separator'], $args['thousand_separator'], $original_price );

	if ( apply_filters( 'woocommerce_price_trim_zeros', false ) && $args['decimals'] > 0 ) {
		$price = wc_trim_zeros( $price );
	}

	$formatted_price = ( $negative ? '-' : '' ) . sprintf( $args['price_format'], '<span class="woocommerce-Price-currencySymbol">' . get_woocommerce_currency_symbol( $args['currency'] ) . '</span>', $price );
	$return          = '<span class="woocommerce-Price-amount amount"><bdi>' . $formatted_price . '</bdi></span>';

	if ( $args['ex_tax_label'] && wc_tax_enabled() ) {
		$return .= ' <small class="woocommerce-Price-taxLabel tax_label">' . WC()->countries->ex_tax_or_vat() . '</small>';
	}

	/**
	 * Filters the string of price markup.
	 *
	 * @param string       $return            Price HTML markup.
	 * @param string       $price             Formatted price.
	 * @param array        $args              Pass on the args.
	 * @param float        $unformatted_price Price as float to allow plugins custom formatting. Since 3.2.0.
	 * @param float|string $original_price    Original price as float, or empty string. Since 5.0.0.
	 */
	return apply_filters( 'wc_price', $return, $price, $args, $unformatted_price, $original_price );
}

/**
 * Notation to numbers.
 *
 * This function transforms the php.ini notation for numbers (like '2M') to an integer.
 *
 * @param  string $size Size value.
 * @return int
 */
function wc_let_to_num( $size ) {
	$l   = substr( $size, -1 );
	$ret = (int) substr( $size, 0, -1 );
	switch ( strtoupper( $l ) ) {
		case 'P':
			$ret *= 1024;
			// No break.
		case 'T':
			$ret *= 1024;
			// No break.
		case 'G':
			$ret *= 1024;
			// No break.
		case 'M':
			$ret *= 1024;
			// No break.
		case 'K':
			$ret *= 1024;
			// No break.
	}
	return $ret;
}

/**
 * WooCommerce Date Format - Allows to change date format for everything WooCommerce.
 *
 * @return string
 */
function wc_date_format() {
	$date_format = get_option( 'date_format' );
	if ( empty( $date_format ) ) {
		// Return default date format if the option is empty.
		$date_format = 'F j, Y';
	}
	return apply_filters( 'woocommerce_date_format', $date_format );
}

/**
 * WooCommerce Time Format - Allows to change time format for everything WooCommerce.
 *
 * @return string
 */
function wc_time_format() {
	$time_format = get_option( 'time_format' );
	if ( empty( $time_format ) ) {
		// Return default time format if the option is empty.
		$time_format = 'g:i a';
	}
	return apply_filters( 'woocommerce_time_format', $time_format );
}

/**
 * Convert mysql datetime to PHP timestamp, forcing UTC. Wrapper for strtotime.
 *
 * Based on wcs_strtotime_dark_knight() from WC Subscriptions by Prospress.
 *
 * @since  3.0.0
 * @param  string   $time_string    Time string.
 * @param  int|null $from_timestamp Timestamp to convert from.
 * @return int
 */
function wc_string_to_timestamp( $time_string, $from_timestamp = null ) {
	$original_timezone = date_default_timezone_get();

	// @codingStandardsIgnoreStart
	date_default_timezone_set( 'UTC' );

	if ( null === $from_timestamp ) {
		$next_timestamp = strtotime( $time_string );
	} else {
		$next_timestamp = strtotime( $time_string, $from_timestamp );
	}

	date_default_timezone_set( $original_timezone );
	// @codingStandardsIgnoreEnd

	return $next_timestamp;
}

/**
 * Convert a date string to a WC_DateTime.
 *
 * @since  3.1.0
 * @param  string $time_string Time string.
 * @return WC_DateTime
 */
function wc_string_to_datetime( $time_string ) {
	// Strings are defined in local WP timezone. Convert to UTC.
	if ( 1 === preg_match( '/^(\d{4})-(\d{2})-(\d{2})T(\d{2}):(\d{2}):(\d{2})(Z|((-|\+)\d{2}:\d{2}))$/', $time_string, $date_bits ) ) {
		$offset    = ! empty( $date_bits[7] ) ? iso8601_timezone_to_offset( $date_bits[7] ) : wc_timezone_offset();
		$timestamp = gmmktime( $date_bits[4], $date_bits[5], $date_bits[6], $date_bits[2], $date_bits[3], $date_bits[1] ) - $offset;
	} else {
		$timestamp = wc_string_to_timestamp( get_gmt_from_date( gmdate( 'Y-m-d H:i:s', wc_string_to_timestamp( $time_string ) ) ) );
	}
	$datetime = new WC_DateTime( "@{$timestamp}", new DateTimeZone( 'UTC' ) );

	// Set local timezone or offset.
	if ( get_option( 'timezone_string' ) ) {
		$datetime->setTimezone( new DateTimeZone( wc_timezone_string() ) );
	} else {
		$datetime->set_utc_offset( wc_timezone_offset() );
	}

	return $datetime;
}

/**
 * WooCommerce Timezone - helper to retrieve the timezone string for a site until.
 * a WP core method exists (see https://core.trac.wordpress.org/ticket/24730).
 *
 * Adapted from https://secure.php.net/manual/en/function.timezone-name-from-abbr.php#89155.
 *
 * @since 2.1
 * @return string PHP timezone string for the site
 */
function wc_timezone_string() {
	// Added in WordPress 5.3 Ref https://developer.wordpress.org/reference/functions/wp_timezone_string/.
	if ( function_exists( 'wp_timezone_string' ) ) {
		return wp_timezone_string();
	}

	// If site timezone string exists, return it.
	$timezone = get_option( 'timezone_string' );
	if ( $timezone ) {
		return $timezone;
	}

	// Get UTC offset, if it isn't set then return UTC.
	$utc_offset = floatval( get_option( 'gmt_offset', 0 ) );
	if ( ! is_numeric( $utc_offset ) || 0.0 === $utc_offset ) {
		return 'UTC';
	}

	// Adjust UTC offset from hours to seconds.
	$utc_offset = (int) ( $utc_offset * 3600 );

	// Attempt to guess the timezone string from the UTC offset.
	$timezone = timezone_name_from_abbr( '', $utc_offset );
	if ( $timezone ) {
		return $timezone;
	}

	// Last try, guess timezone string manually.
	foreach ( timezone_abbreviations_list() as $abbr ) {
		foreach ( $abbr as $city ) {
			// WordPress restrict the use of date(), since it's affected by timezone settings, but in this case is just what we need to guess the correct timezone.
			if ( (bool) date( 'I' ) === (bool) $city['dst'] && $city['timezone_id'] && intval( $city['offset'] ) === $utc_offset ) { // phpcs:ignore WordPress.DateTime.RestrictedFunctions.date_date
				return $city['timezone_id'];
			}
		}
	}

	// Fallback to UTC.
	return 'UTC';
}

/**
 * Get timezone offset in seconds.
 *
 * @since  3.0.0
 * @return float
 */
function wc_timezone_offset() {
	$timezone = get_option( 'timezone_string' );

	if ( $timezone ) {
		$timezone_object = new DateTimeZone( $timezone );
		return $timezone_object->getOffset( new DateTime( 'now' ) );
	} else {
		return floatval( get_option( 'gmt_offset', 0 ) ) * HOUR_IN_SECONDS;
	}
}

/**
 * Callback which can flatten post meta (gets the first value if it's an array).
 *
 * @since  3.0.0
 * @param  array $value Value to flatten.
 * @return mixed
 */
function wc_flatten_meta_callback( $value ) {
	return is_array( $value ) ? current( $value ) : $value;
}

if ( ! function_exists( 'wc_rgb_from_hex' ) ) {

	/**
	 * Convert RGB to HEX.
	 *
	 * @param mixed $color Color.
	 *
	 * @return array
	 */
	function wc_rgb_from_hex( $color ) {
		$color = str_replace( '#', '', $color );
		// Convert shorthand colors to full format, e.g. "FFF" -> "FFFFFF".
		$color = preg_replace( '~^(.)(.)(.)$~', '$1$1$2$2$3$3', $color );

		$rgb      = array();
		$rgb['R'] = hexdec( $color[0] . $color[1] );
		$rgb['G'] = hexdec( $color[2] . $color[3] );
		$rgb['B'] = hexdec( $color[4] . $color[5] );

		return $rgb;
	}
}

if ( ! function_exists( 'wc_hex_darker' ) ) {

	/**
	 * Make HEX color darker.
	 *
	 * @param mixed $color  Color.
	 * @param int   $factor Darker factor.
	 *                      Defaults to 30.
	 * @return string
	 */
	function wc_hex_darker( $color, $factor = 30 ) {
		$base  = wc_rgb_from_hex( $color );
		$color = '#';

		foreach ( $base as $k => $v ) {
			$amount      = $v / 100;
			$amount      = NumberUtil::round( $amount * $factor );
			$new_decimal = $v - $amount;

			$new_hex_component = dechex( $new_decimal );
			if ( strlen( $new_hex_component ) < 2 ) {
				$new_hex_component = '0' . $new_hex_component;
			}
			$color .= $new_hex_component;
		}

		return $color;
	}
}

if ( ! function_exists( 'wc_hex_lighter' ) ) {

	/**
	 * Make HEX color lighter.
	 *
	 * @param mixed $color  Color.
	 * @param int   $factor Lighter factor.
	 *                      Defaults to 30.
	 * @return string
	 */
	function wc_hex_lighter( $color, $factor = 30 ) {
		$base  = wc_rgb_from_hex( $color );
		$color = '#';

		foreach ( $base as $k => $v ) {
			$amount      = 255 - $v;
			$amount      = $amount / 100;
			$amount      = NumberUtil::round( $amount * $factor );
			$new_decimal = $v + $amount;

			$new_hex_component = dechex( $new_decimal );
			if ( strlen( $new_hex_component ) < 2 ) {
				$new_hex_component = '0' . $new_hex_component;
			}
			$color .= $new_hex_component;
		}

		return $color;
	}
}

if ( ! function_exists( 'wc_hex_is_light' ) ) {

	/**
	 * Determine whether a hex color is light.
	 *
	 * @param mixed $color Color.
	 * @return bool  True if a light color.
	 */
	function wc_hex_is_light( $color ) {
		$hex = str_replace( '#', '', $color );

		$c_r = hexdec( substr( $hex, 0, 2 ) );
		$c_g = hexdec( substr( $hex, 2, 2 ) );
		$c_b = hexdec( substr( $hex, 4, 2 ) );

		$brightness = ( ( $c_r * 299 ) + ( $c_g * 587 ) + ( $c_b * 114 ) ) / 1000;

		return $brightness > 155;
	}
}

if ( ! function_exists( 'wc_light_or_dark' ) ) {

	/**
	 * Detect if we should use a light or dark color on a background color.
	 *
	 * @param mixed  $color Color.
	 * @param string $dark  Darkest reference.
	 *                      Defaults to '#000000'.
	 * @param string $light Lightest reference.
	 *                      Defaults to '#FFFFFF'.
	 * @return string
	 */
	function wc_light_or_dark( $color, $dark = '#000000', $light = '#FFFFFF' ) {
		return wc_hex_is_light( $color ) ? $dark : $light;
	}
}

if ( ! function_exists( 'wc_format_hex' ) ) {

	/**
	 * Format string as hex.
	 *
	 * @param string $hex HEX color.
	 * @return string|null
	 */
	function wc_format_hex( $hex ) {
		$hex = trim( str_replace( '#', '', $hex ?? '' ) );

		if ( strlen( $hex ) === 3 ) {
			$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
		}

		return $hex ? '#' . $hex : null;
	}
}

/**
 * Format the postcode according to the country and length of the postcode.
 *
 * @param string $postcode Unformatted postcode.
 * @param string $country  Base country.
 * @return string
 */
function wc_format_postcode( $postcode, $country ) {
	$postcode = wc_normalize_postcode( $postcode );

	switch ( $country ) {
		case 'CA':
		case 'GB':
			$postcode = substr_replace( $postcode, ' ', -3, 0 );
			break;
		case 'IE':
			$postcode = substr_replace( $postcode, ' ', 3, 0 );
			break;
		case 'BR':
		case 'PL':
			$postcode = substr_replace( $postcode, '-', -3, 0 );
			break;
		case 'JP':
			$postcode = substr_replace( $postcode, '-', 3, 0 );
			break;
		case 'PT':
			$postcode = substr_replace( $postcode, '-', 4, 0 );
			break;
		case 'PR':
		case 'US':
			$postcode = rtrim( substr_replace( $postcode, '-', 5, 0 ), '-' );
			break;
		case 'NL':
			$postcode = substr_replace( $postcode, ' ', 4, 0 );
			break;
		case 'LV':
			if ( preg_match( '/(?:LV)?-?(\d+)/i', $postcode, $matches ) ) {
				$postcode = count( $matches ) >= 2 ? "LV-$matches[1]" : $postcode;
			}
			break;
		case 'DK':
			$postcode = preg_replace( '/^(DK)(.+)$/', '${1}-${2}', $postcode );
			break;
	}

	return apply_filters( 'woocommerce_format_postcode', trim( $postcode ), $country );
}

/**
 * Normalize postcodes.
 *
 * Remove spaces and convert characters to uppercase.
 *
 * @since 2.6.0
 * @param string $postcode Postcode.
 * @return string
 */
function wc_normalize_postcode( $postcode ) {
	return preg_replace( '/[\s\-]/', '', trim( wc_strtoupper( $postcode ) ) );
}

/**
 * Format phone numbers.
 *
 * @param string $phone Phone number.
 * @return string
 */
function wc_format_phone_number( $phone ) {
	if ( ! WC_Validation::is_phone( $phone ) ) {
		return '';
	}
	return preg_replace( '/[^0-9\+\-\(\)\s]/', '-', preg_replace( '/[\x00-\x1F\x7F-\xFF]/', '', $phone ) );
}

/**
 * Sanitize phone number.
 * Allows only numbers and "+" (plus sign).
 *
 * @since 3.6.0
 * @param string $phone Phone number.
 * @return string
 */
function wc_sanitize_phone_number( $phone ) {
	return preg_replace( '/[^\d+]/', '', $phone );
}

/**
 * Wrapper for mb_strtoupper which see's if supported first.
 *
 * @since  3.1.0
 * @param  string $string String to format.
 * @return string
 */
function wc_strtoupper( $string ) {
	return function_exists( 'mb_strtoupper' ) ? mb_strtoupper( $string ) : strtoupper( $string );
}

/**
 * Make a string lowercase.
 * Try to use mb_strtolower() when available.
 *
 * @since  2.3
 * @param  string $string String to format.
 * @return string
 */
function wc_strtolower( $string ) {
	return function_exists( 'mb_strtolower' ) ? mb_strtolower( $string ) : strtolower( $string );
}

/**
 * Trim a string and append a suffix.
 *
 * @param  string  $string String to trim.
 * @param  integer $chars  Amount of characters.
 *                         Defaults to 200.
 * @param  string  $suffix Suffix.
 *                         Defaults to '...'.
 * @return string
 */
function wc_trim_string( $string, $chars = 200, $suffix = '...' ) {
	if ( strlen( $string ) > $chars ) {
		if ( function_exists( 'mb_substr' ) ) {
			$string = mb_substr( $string, 0, ( $chars - mb_strlen( $suffix ) ) ) . $suffix;
		} else {
			$string = substr( $string, 0, ( $chars - strlen( $suffix ) ) ) . $suffix;
		}
	}
	return $string;
}

/**
 * Format content to display shortcodes.
 *
 * @since  2.3.0
 * @param  string $raw_string Raw string.
 * @return string
 */
function wc_format_content( $raw_string ) {
	return apply_filters( 'woocommerce_format_content', apply_filters( 'woocommerce_short_description', $raw_string ), $raw_string );
}

/**
 * Format product short description.
 * Adds support for Jetpack Markdown.
 *
 * @codeCoverageIgnore
 * @since  2.4.0
 * @param  string $content Product short description.
 * @return string
 */
function wc_format_product_short_description( $content ) {
	// Add support for Jetpack Markdown.
	if ( class_exists( 'WPCom_Markdown' ) ) {
		$markdown = WPCom_Markdown::get_instance();

		return wpautop(
			$markdown->transform(
				$content,
				array(
					'unslash' => false,
				)
			)
		);
	}

	return $content;
}

/**
 * Formats curency symbols when saved in settings.
 *
 * @codeCoverageIgnore
 * @param  string $value     Option value.
 * @param  array  $option    Option name.
 * @param  string $raw_value Raw value.
 * @return string
 */
function wc_format_option_price_separators( $value, $option, $raw_value ) {
	return wp_kses_post( $raw_value );
}
add_filter( 'woocommerce_admin_settings_sanitize_option_woocommerce_price_decimal_sep', 'wc_format_option_price_separators', 10, 3 );
add_filter( 'woocommerce_admin_settings_sanitize_option_woocommerce_price_thousand_sep', 'wc_format_option_price_separators', 10, 3 );

/**
 * Formats decimals when saved in settings.
 *
 * @codeCoverageIgnore
 * @param  string $value     Option value.
 * @param  array  $option    Option name.
 * @param  string $raw_value Raw value.
 * @return string
 */
function wc_format_option_price_num_decimals( $value, $option, $raw_value ) {
	return is_null( $raw_value ) ? 2 : absint( $raw_value );
}
add_filter( 'woocommerce_admin_settings_sanitize_option_woocommerce_price_num_decimals', 'wc_format_option_price_num_decimals', 10, 3 );

/**
 * Formats hold stock option and sets cron event up.
 *
 * @codeCoverageIgnore
 * @param  string $value     Option value.
 * @param  array  $option    Option name.
 * @param  string $raw_value Raw value.
 * @return string
 */
function wc_format_option_hold_stock_minutes( $value, $option, $raw_value ) {
	$value = ! empty( $raw_value ) ? absint( $raw_value ) : ''; // Allow > 0 or set to ''.

	wp_clear_scheduled_hook( 'woocommerce_cancel_unpaid_orders' );

	if ( '' !== $value ) {
		$cancel_unpaid_interval = apply_filters( 'woocommerce_cancel_unpaid_orders_interval_minutes', absint( $value ) );
		wp_schedule_single_event( time() + ( absint( $cancel_unpaid_interval ) * 60 ), 'woocommerce_cancel_unpaid_orders' );
	}

	return $value;
}
add_filter( 'woocommerce_admin_settings_sanitize_option_woocommerce_hold_stock_minutes', 'wc_format_option_hold_stock_minutes', 10, 3 );

/**
 * Sanitize terms from an attribute text based.
 *
 * @since  2.4.5
 * @param  string $term Term value.
 * @return string
 */
function wc_sanitize_term_text_based( $term ) {
	return trim( wp_strip_all_tags( wp_unslash( $term ) ) );
}

if ( ! function_exists( 'wc_make_numeric_postcode' ) ) {
	/**
	 * Make numeric postcode.
	 *
	 * Converts letters to numbers so we can do a simple range check on postcodes.
	 * E.g. PE30 becomes 16050300 (P = 16, E = 05, 3 = 03, 0 = 00)
	 *
	 * @since 2.6.0
	 * @param string $postcode Regular postcode.
	 * @return string
	 */
	function wc_make_numeric_postcode( $postcode ) {
		$postcode           = str_replace( array( ' ', '-' ), '', $postcode );
		$postcode_length    = strlen( $postcode );
		$letters_to_numbers = array_merge( array( 0 ), range( 'A', 'Z' ) );
		$letters_to_numbers = array_flip( $letters_to_numbers );
		$numeric_postcode   = '';

		for ( $i = 0; $i < $postcode_length; $i ++ ) {
			if ( is_numeric( $postcode[ $i ] ) ) {
				$numeric_postcode .= str_pad( $postcode[ $i ], 2, '0', STR_PAD_LEFT );
			} elseif ( isset( $letters_to_numbers[ $postcode[ $i ] ] ) ) {
				$numeric_postcode .= str_pad( $letters_to_numbers[ $postcode[ $i ] ], 2, '0', STR_PAD_LEFT );
			} else {
				$numeric_postcode .= '00';
			}
		}

		return $numeric_postcode;
	}
}

/**
 * Format the stock amount ready for display based on settings.
 *
 * @since  3.0.0
 * @param  WC_Product $product Product object for which the stock you need to format.
 * @return string
 */
function wc_format_stock_for_display( $product ) {
	$display      = __( 'In stock', 'woocommerce' );
	$stock_amount = $product->get_stock_quantity();

	switch ( get_option( 'woocommerce_stock_format' ) ) {
		case 'low_amount':
			if ( $stock_amount <= wc_get_low_stock_amount( $product ) ) {
				/* translators: %s: stock amount */
				$display = sprintf( __( 'Only %s left in stock', 'woocommerce' ), wc_format_stock_quantity_for_display( $stock_amount, $product ) );
			}
			break;
		case '':
			/* translators: %s: stock amount */
			$display = sprintf( __( '%s in stock', 'woocommerce' ), wc_format_stock_quantity_for_display( $stock_amount, $product ) );
			break;
	}

	if ( $product->backorders_allowed() && $product->backorders_require_notification() ) {
		$display .= ' ' . __( '(can be backordered)', 'woocommerce' );
	}

	return $display;
}

/**
 * Format the stock quantity ready for display.
 *
 * @since  3.0.0
 * @param  int        $stock_quantity Stock quantity.
 * @param  WC_Product $product        Product instance so that we can pass through the filters.
 * @return string
 */
function wc_format_stock_quantity_for_display( $stock_quantity, $product ) {
	return apply_filters( 'woocommerce_format_stock_quantity', $stock_quantity, $product );
}

/**
 * Format a sale price for display.
 *
 * @since  3.0.0
 * @param  string $regular_price Regular price.
 * @param  string $sale_price    Sale price.
 * @return string
 */
function wc_format_sale_price( $regular_price, $sale_price ) {
	$price = '<del aria-hidden="true">' . ( is_numeric( $regular_price ) ? wc_price( $regular_price ) : $regular_price ) . '</del> <ins>' . ( is_numeric( $sale_price ) ? wc_price( $sale_price ) : $sale_price ) . '</ins>';
	return apply_filters( 'woocommerce_format_sale_price', $price, $regular_price, $sale_price );
}

/**
 * Format a price range for display.
 *
 * @param  string $from Price from.
 * @param  string $to   Price to.
 * @return string
 */
function wc_format_price_range( $from, $to ) {
	/* translators: 1: price from 2: price to */
	$price = sprintf( _x( '%1$s &ndash; %2$s', 'Price range: from-to', 'woocommerce' ), is_numeric( $from ) ? wc_price( $from ) : $from, is_numeric( $to ) ? wc_price( $to ) : $to );
	return apply_filters( 'woocommerce_format_price_range', $price, $from, $to );
}

/**
 * Format a weight for display.
 *
 * @since  3.0.0
 * @param  float $weight Weight.
 * @return string
 */
function wc_format_weight( $weight ) {
	$weight_string = wc_format_localized_decimal( $weight );

	if ( ! empty( $weight_string ) ) {
		$weight_label = I18nUtil::get_weight_unit_label( get_option( 'woocommerce_weight_unit' ) );

		$weight_string = sprintf(
			// translators: 1. A formatted number; 2. A label for a weight unit of measure. E.g. 2.72 kg.
			_x( '%1$s %2$s', 'formatted weight', 'woocommerce' ),
			$weight_string,
			$weight_label
		);
	} else {
		$weight_string = __( 'N/A', 'woocommerce' );
	}

	return apply_filters( 'woocommerce_format_weight', $weight_string, $weight );
}

/**
 * Format dimensions for display.
 *
 * @since  3.0.0
 * @param  array $dimensions Array of dimensions.
 * @return string
 */
function wc_format_dimensions( $dimensions ) {
	$dimension_string = implode( ' &times; ', array_filter( array_map( 'wc_format_localized_decimal', $dimensions ) ) );

	if ( ! empty( $dimension_string ) ) {
		$dimension_label = I18nUtil::get_dimensions_unit_label( get_option( 'woocommerce_dimension_unit' ) );

		$dimension_string = sprintf(
			// translators: 1. A formatted number; 2. A label for a dimensions unit of measure. E.g. 3.14 cm.
			_x( '%1$s %2$s', 'formatted dimensions', 'woocommerce' ),
			$dimension_string,
			$dimension_label
		);
	} else {
		$dimension_string = __( 'N/A', 'woocommerce' );
	}

	return apply_filters( 'woocommerce_format_dimensions', $dimension_string, $dimensions );
}

/**
 * Format a date for output.
 *
 * @since  3.0.0
 * @param  WC_DateTime $date   Instance of WC_DateTime.
 * @param  string      $format Data format.
 *                             Defaults to the wc_date_format function if not set.
 * @return string
 */
function wc_format_datetime( $date, $format = '' ) {
	if ( ! $format ) {
		$format = wc_date_format();
	}
	if ( ! is_a( $date, 'WC_DateTime' ) ) {
		return '';
	}
	return $date->date_i18n( $format );
}

/**
 * Process oEmbeds.
 *
 * @since  3.1.0
 * @param  string $content Content.
 * @return string
 */
function wc_do_oembeds( $content ) {
	global $wp_embed;

	$content = $wp_embed->autoembed( $content );

	return $content;
}

/**
 * Get part of a string before :.
 *
 * Used for example in shipping methods ids where they take the format
 * method_id:instance_id
 *
 * @since  3.2.0
 * @param  string $string String to extract.
 * @return string
 */
function wc_get_string_before_colon( $string ) {
	return trim( current( explode( ':', (string) $string ) ) );
}

/**
 * Array merge and sum function.
 *
 * Source:  https://gist.github.com/Nickology/f700e319cbafab5eaedc
 *
 * @since 3.2.0
 * @return array
 */
function wc_array_merge_recursive_numeric() {
	$arrays = func_get_args();

	// If there's only one array, it's already merged.
	if ( 1 === count( $arrays ) ) {
		return $arrays[0];
	}

	// Remove any items in $arrays that are NOT arrays.
	foreach ( $arrays as $key => $array ) {
		if ( ! is_array( $array ) ) {
			unset( $arrays[ $key ] );
		}
	}

	// We start by setting the first array as our final array.
	// We will merge all other arrays with this one.
	$final = array_shift( $arrays );

	foreach ( $arrays as $b ) {
		foreach ( $final as $key => $value ) {
			// If $key does not exist in $b, then it is unique and can be safely merged.
			if ( ! isset( $b[ $key ] ) ) {
				$final[ $key ] = $value;
			} else {
				// If $key is present in $b, then we need to merge and sum numeric values in both.
				if ( is_numeric( $value ) && is_numeric( $b[ $key ] ) ) {
					// If both values for these keys are numeric, we sum them.
					$final[ $key ] = $value + $b[ $key ];
				} elseif ( is_array( $value ) && is_array( $b[ $key ] ) ) {
					// If both values are arrays, we recursively call ourself.
					$final[ $key ] = wc_array_merge_recursive_numeric( $value, $b[ $key ] );
				} else {
					// If both keys exist but differ in type, then we cannot merge them.
					// In this scenario, we will $b's value for $key is used.
					$final[ $key ] = $b[ $key ];
				}
			}
		}

		// Finally, we need to merge any keys that exist only in $b.
		foreach ( $b as $key => $value ) {
			if ( ! isset( $final[ $key ] ) ) {
				$final[ $key ] = $value;
			}
		}
	}

	return $final;
}

/**
 * Implode and escape HTML attributes for output.
 *
 * @since 3.3.0
 * @param array $raw_attributes Attribute name value pairs.
 * @return string
 */
function wc_implode_html_attributes( $raw_attributes ) {
	$attributes = array();
	foreach ( $raw_attributes as $name => $value ) {
		$attributes[] = esc_attr( $name ) . '="' . esc_attr( $value ) . '"';
	}
	return implode( ' ', $attributes );
}

/**
 * Escape JSON for use on HTML or attribute text nodes.
 *
 * @since 3.5.5
 * @param string $json JSON to escape.
 * @param bool   $html True if escaping for HTML text node, false for attributes. Determines how quotes are handled.
 * @return string Escaped JSON.
 */
function wc_esc_json( $json, $html = false ) {
	return _wp_specialchars(
		$json,
		$html ? ENT_NOQUOTES : ENT_QUOTES, // Escape quotes in attribute nodes only.
		'UTF-8',                           // json_encode() outputs UTF-8 (really just ASCII), not the blog's charset.
		true                               // Double escape entities: `&amp;` -> `&amp;amp;`.
	);
}

/**
 * Parse a relative date option from the settings API into a standard format.
 *
 * @since 3.4.0
 * @param mixed $raw_value Value stored in DB.
 * @return array Nicely formatted array with number and unit values.
 */
function wc_parse_relative_date_option( $raw_value ) {
	$periods = array(
		'days'   => __( 'Day(s)', 'woocommerce' ),
		'weeks'  => __( 'Week(s)', 'woocommerce' ),
		'months' => __( 'Month(s)', 'woocommerce' ),
		'years'  => __( 'Year(s)', 'woocommerce' ),
	);

	$value = wp_parse_args(
		(array) $raw_value,
		array(
			'number' => '',
			'unit'   => 'days',
		)
	);

	$value['number'] = ! empty( $value['number'] ) ? absint( $value['number'] ) : '';

	if ( ! in_array( $value['unit'], array_keys( $periods ), true ) ) {
		$value['unit'] = 'days';
	}

	return $value;
}

/**
 * Format the endpoint slug, strip out anything not allowed in a url.
 *
 * @since 3.5.0
 * @param string $raw_value The raw value.
 * @return string
 */
function wc_sanitize_endpoint_slug( $raw_value ) {
	return sanitize_title( $raw_value );
}
add_filter( 'woocommerce_admin_settings_sanitize_option_woocommerce_checkout_pay_endpoint', 'wc_sanitize_endpoint_slug', 10, 1 );
add_filter( 'woocommerce_admin_settings_sanitize_option_woocommerce_checkout_order_received_endpoint', 'wc_sanitize_endpoint_slug', 10, 1 );
add_filter( 'woocommerce_admin_settings_sanitize_option_woocommerce_myaccount_add_payment_method_endpoint', 'wc_sanitize_endpoint_slug', 10, 1 );
add_filter( 'woocommerce_admin_settings_sanitize_option_woocommerce_myaccount_delete_payment_method_endpoint', 'wc_sanitize_endpoint_slug', 10, 1 );
add_filter( 'woocommerce_admin_settings_sanitize_option_woocommerce_myaccount_set_default_payment_method_endpoint', 'wc_sanitize_endpoint_slug', 10, 1 );
add_filter( 'woocommerce_admin_settings_sanitize_option_woocommerce_myaccount_orders_endpoint', 'wc_sanitize_endpoint_slug', 10, 1 );
add_filter( 'woocommerce_admin_settings_sanitize_option_woocommerce_myaccount_view_order_endpoint', 'wc_sanitize_endpoint_slug', 10, 1 );
add_filter( 'woocommerce_admin_settings_sanitize_option_woocommerce_myaccount_downloads_endpoint', 'wc_sanitize_endpoint_slug', 10, 1 );
add_filter( 'woocommerce_admin_settings_sanitize_option_woocommerce_myaccount_edit_account_endpoint', 'wc_sanitize_endpoint_slug', 10, 1 );
add_filter( 'woocommerce_admin_settings_sanitize_option_woocommerce_myaccount_edit_address_endpoint', 'wc_sanitize_endpoint_slug', 10, 1 );
add_filter( 'woocommerce_admin_settings_sanitize_option_woocommerce_myaccount_payment_methods_endpoint', 'wc_sanitize_endpoint_slug', 10, 1 );
add_filter( 'woocommerce_admin_settings_sanitize_option_woocommerce_myaccount_lost_password_endpoint', 'wc_sanitize_endpoint_slug', 10, 1 );
add_filter( 'woocommerce_admin_settings_sanitize_option_woocommerce_logout_endpoint', 'wc_sanitize_endpoint_slug', 10, 1 );
