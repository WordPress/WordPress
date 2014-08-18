<?php
/**
 * WooCommerce Formatting
 *
 * Functions for formatting data.
 *
 * @author 		WooThemes
 * @category 	Core
 * @package 	WooCommerce/Functions
 * @version     2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Sanitize taxonomy names. Slug format (no spaces, lowercase).
 *
 * Doesn't use sanitize_title as this destroys utf chars.
 *
 * @access public
 * @param mixed $taxonomy
 * @return string
 */
function wc_sanitize_taxonomy_name( $taxonomy ) {
	$filtered = strtolower( remove_accents( stripslashes( strip_tags( $taxonomy ) ) ) );
	$filtered = preg_replace( '/&.+?;/', '', $filtered ); // Kill entities
	$filtered = str_replace( array( '.', '\'', '"' ), '', $filtered ); // Kill quotes and full stops.
	$filtered = str_replace( array( ' ', '_' ), '-', $filtered ); // Replace spaces and underscores.

	return apply_filters( 'sanitize_taxonomy_name', $filtered, $taxonomy );
}

/**
 * Gets the filename part of a download URL
 *
 * @access public
 * @param string $file_url
 * @return string
 */
function wc_get_filename_from_url( $file_url ) {
	$parts = parse_url( $file_url );
	if ( isset( $parts['path'] ) ) {
		return basename( $parts['path'] );
	}
}

/**
 * Normalise dimensions, unify to cm then convert to wanted unit value
 *
 * Usage: wc_get_dimension(55, 'in');
 *
 * @access public
 * @param mixed $dim
 * @param mixed $to_unit 'in', 'm', 'cm', 'm'
 * @return float
 */
function wc_get_dimension( $dim, $to_unit ) {

	$from_unit 	= strtolower( get_option( 'woocommerce_dimension_unit' ) );
	$to_unit	= strtolower( $to_unit );

	// Unify all units to cm first
	if ( $from_unit !== $to_unit ) {

		switch ( $from_unit ) {
			case 'in':
				$dim *= 2.54;
			break;
			case 'm':
				$dim *= 100;
			break;
			case 'mm':
				$dim *= 0.1;
			break;
			case 'yd':
				$dim *= 91.44;
			break;
		}

		// Output desired unit
		switch ( $to_unit ) {
			case 'in':
				$dim *= 0.3937;
			break;
			case 'm':
				$dim *= 0.01;
			break;
			case 'mm':
				$dim *= 10;
			break;
			case 'yd':
				$dim *= 0.010936133;
			break;
		}
	}
	return ( $dim < 0 ) ? 0 : $dim;
}

/**
 * Normalise weights, unify to cm then convert to wanted unit value
 *
 * Usage: wc_get_weight(55, 'kg');
 *
 * @access public
 * @param mixed $weight
 * @param mixed $to_unit 'g', 'kg', 'lbs'
 * @return float
 */
function wc_get_weight( $weight, $to_unit ) {

	$from_unit 	= strtolower( get_option('woocommerce_weight_unit') );
	$to_unit	= strtolower( $to_unit );

	//Unify all units to kg first
	if ( $from_unit !== $to_unit ) {

		switch ( $from_unit ) {
			case 'g':
				$weight *= 0.001;
			break;
			case 'lbs':
				$weight *= 0.4536;
			break;
			case 'oz':
				$weight *= 0.0283;
			break;
		}

		// Output desired unit
		switch ( $to_unit ) {
			case 'g':
				$weight *= 1000;
			break;
			case 'lbs':
				$weight *= 2.2046;
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
 * @access public
 * @param mixed $price
 * @return string
 */
function wc_trim_zeros( $price ) {
	return preg_replace( '/' . preg_quote( get_option( 'woocommerce_price_decimal_sep' ), '/' ) . '0++$/', '', $price );
}

/**
 * Round a tax amount
 *
 * @access public
 * @param mixed $price
 * @return string
 */
function wc_round_tax_total( $tax ) {
	$dp = (int) get_option( 'woocommerce_price_num_decimals' );

	if ( version_compare( phpversion(), '5.3', '<' ) ) {
		$tax = round( $tax, $dp );
	} else {
		$tax = round( $tax, $dp, WC_TAX_ROUNDING_MODE );
	}
	return $tax;
}

/**
 * Format decimal numbers ready for DB storage
 *
 * Sanitize, remove locale formatting, and optionally round + trim off zeros
 *
 * @param  float|string $number Expects either a float or a string with a decimal separator only (no thousands)
 * @param  mixed $dp number of decimal points to use, blank to use woocommerce_price_num_decimals, or false to avoid all rounding.
 * @param  boolean $trim_zeros from end of string
 * @return string
 */
function wc_format_decimal( $number, $dp = false, $trim_zeros = false ) {
	// Remove locale from string
	if ( ! is_float( $number ) ) {
		$locale   = localeconv();
		$decimals = array( get_option( 'woocommerce_price_decimal_sep' ), $locale['decimal_point'], $locale['mon_decimal_point'] );
		$number   = wc_clean( str_replace( $decimals, '.', $number ) );
	}

	// DP is false - don't use number format, just return a string in our format
	if ( $dp !== false ) {
		$dp     = intval( $dp == "" ? get_option( 'woocommerce_price_num_decimals' ) : $dp );
		$number = number_format( floatval( $number ), $dp, '.', '' );
	}

	if ( $trim_zeros && strstr( $number, '.' ) ) {
		$number = rtrim( rtrim( $number, '0' ), '.' );
	}

	return $number;
}

/**
 * Convert a float to a string without locale formatting which PHP adds when changing floats to strings
 * @param  float $float
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
 * Format a price with WC Currency Locale settings
 * @param  string $value
 * @return string
 */
function wc_format_localized_price( $value ) {
	return str_replace( '.', get_option( 'woocommerce_price_decimal_sep' ), strval( $value ) );
}

/**
 * Format a decimal with PHP Locale settings
 * @param  string $value
 * @return string
 */
function wc_format_localized_decimal( $value ) {
	$locale = localeconv();
	return str_replace( '.', $locale['decimal_point'], strval( $value ) );
}

/**
 * Clean variables
 *
 * @access public
 * @param string $var
 * @return string
 */
function wc_clean( $var ) {
	return sanitize_text_field( $var );
}

/**
 * Merge two arrays
 *
 * @access public
 * @param array $a1
 * @param array $a2
 * @return array
 */
function wc_array_overlay( $a1, $a2 ) {
    foreach( $a1 as $k => $v ) {
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
 * Get the price format depending on the currency position
 *
 * @return string
 */
function get_woocommerce_price_format() {
	$currency_pos = get_option( 'woocommerce_currency_pos' );

	switch ( $currency_pos ) {
		case 'left' :
			$format = '%1$s%2$s';
		break;
		case 'right' :
			$format = '%2$s%1$s';
		break;
		case 'left_space' :
			$format = '%1$s&nbsp;%2$s';
		break;
		case 'right_space' :
			$format = '%2$s&nbsp;%1$s';
		break;
	}

	return apply_filters( 'woocommerce_price_format', $format, $currency_pos );
}

/**
 * Format the price with a currency symbol.
 *
 * @access public
 * @param float $price
 * @param array $args (default: array())
 * @return string
 */
function wc_price( $price, $args = array() ) {
	extract( shortcode_atts( array(
		'ex_tax_label' 	=> '0'
	), $args ) );

	$return          = '';
	$num_decimals    = absint( get_option( 'woocommerce_price_num_decimals' ) );
	$currency        = isset( $args['currency'] ) ? $args['currency'] : '';
	$currency_symbol = get_woocommerce_currency_symbol($currency);
	$decimal_sep     = wp_specialchars_decode( stripslashes( get_option( 'woocommerce_price_decimal_sep' ) ), ENT_QUOTES );
	$thousands_sep   = wp_specialchars_decode( stripslashes( get_option( 'woocommerce_price_thousand_sep' ) ), ENT_QUOTES );

	$price           = apply_filters( 'raw_woocommerce_price', floatval( $price ) );
	$price           = apply_filters( 'formatted_woocommerce_price', number_format( $price, $num_decimals, $decimal_sep, $thousands_sep ), $price, $num_decimals, $decimal_sep, $thousands_sep );

	if ( apply_filters( 'woocommerce_price_trim_zeros', false ) && $num_decimals > 0 ) {
		$price = wc_trim_zeros( $price );
	}

	$return = '<span class="amount">' . sprintf( get_woocommerce_price_format(), $currency_symbol, $price ) . '</span>';

	if ( $ex_tax_label && get_option( 'woocommerce_calc_taxes' ) == 'yes' ) {
		$return .= ' <small>' . WC()->countries->ex_tax_or_vat() . '</small>';
	}

	return $return;
}

/**
 * let_to_num function.
 *
 * This function transforms the php.ini notation for numbers (like '2M') to an integer.
 *
 * @access public
 * @param $size
 * @return int
 */
function wc_let_to_num( $size ) {
    $l 		= substr( $size, -1 );
    $ret 	= substr( $size, 0, -1 );
    switch( strtoupper( $l ) ) {
	    case 'P':
	        $ret *= 1024;
	    case 'T':
	        $ret *= 1024;
	    case 'G':
	        $ret *= 1024;
	    case 'M':
	        $ret *= 1024;
	    case 'K':
	        $ret *= 1024;
    }
    return $ret;
}

/**
 * WooCommerce Date Format - Allows to change date format for everything WooCommerce
 *
 * @access public
 * @return string
 */
function wc_date_format() {
	return apply_filters( 'woocommerce_date_format', get_option( 'date_format' ) );
}

/**
 * WooCommerce Time Format - Allows to change time format for everything WooCommerce
 *
 * @access public
 * @return string
 */
function wc_time_format() {
	return apply_filters( 'woocommerce_time_format', get_option( 'time_format' ) );
}

/**
 * WooCommerce Timezone - helper to retrieve the timezone string for a site until
 * a WP core method exists (see http://core.trac.wordpress.org/ticket/24730)
 *
 * Adapted from http://www.php.net/manual/en/function.timezone-name-from-abbr.php#89155
 *
 * @since 2.1
 * @access public
 * @return string a valid PHP timezone string for the site
 */
function wc_timezone_string() {

	// if site timezone string exists, return it
	if ( $timezone = get_option( 'timezone_string' ) ) {
		return $timezone;
	}

	// get UTC offset, if it isn't set then return UTC
	if ( 0 === ( $utc_offset = get_option( 'gmt_offset', 0 ) ) ) {
		return 'UTC';
	}

	// adjust UTC offset from hours to seconds
	$utc_offset *= 3600;

	// attempt to guess the timezone string from the UTC offset
	$timezone = timezone_name_from_abbr( '', $utc_offset );

	// last try, guess timezone string manually
	if ( false === $timezone ) {

		$is_dst = date( 'I' );

		foreach ( timezone_abbreviations_list() as $abbr ) {
			foreach ( $abbr as $city ) {

				if ( $city['dst'] == $is_dst && $city['offset'] == $utc_offset ) {
					return $city['timezone_id'];
				}
			}
		}
	}

	// fallback to UTC
	return 'UTC';
}

if ( ! function_exists( 'wc_rgb_from_hex' ) ) {

	/**
	 * Hex darker/lighter/contrast functions for colours
	 *
	 * @access public
	 * @param mixed $color
	 * @return string
	 */
	function wc_rgb_from_hex( $color ) {
		$color = str_replace( '#', '', $color );
		// Convert shorthand colors to full format, e.g. "FFF" -> "FFFFFF"
		$color = preg_replace( '~^(.)(.)(.)$~', '$1$1$2$2$3$3', $color );

		$rgb['R'] = hexdec( $color{0}.$color{1} );
		$rgb['G'] = hexdec( $color{2}.$color{3} );
		$rgb['B'] = hexdec( $color{4}.$color{5} );
		return $rgb;
	}
}

if ( ! function_exists( 'wc_hex_darker' ) ) {

	/**
	 * Hex darker/lighter/contrast functions for colours
	 *
	 * @access public
	 * @param mixed $color
	 * @param int $factor (default: 30)
	 * @return string
	 */
	function wc_hex_darker( $color, $factor = 30 ) {
		$base = wc_rgb_from_hex( $color );
		$color = '#';

		foreach ($base as $k => $v) :
	        $amount = $v / 100;
	        $amount = round($amount * $factor);
	        $new_decimal = $v - $amount;

	        $new_hex_component = dechex($new_decimal);
	        if(strlen($new_hex_component) < 2) :
	        	$new_hex_component = "0".$new_hex_component;
	        endif;
	        $color .= $new_hex_component;
		endforeach;

		return $color;
	}
}

if ( ! function_exists( 'wc_hex_lighter' ) ) {

	/**
	 * Hex darker/lighter/contrast functions for colours
	 *
	 * @access public
	 * @param mixed $color
	 * @param int $factor (default: 30)
	 * @return string
	 */
	function wc_hex_lighter( $color, $factor = 30 ) {
		$base = wc_rgb_from_hex( $color );
		$color = '#';

	    foreach ($base as $k => $v) :
	        $amount = 255 - $v;
	        $amount = $amount / 100;
	        $amount = round($amount * $factor);
	        $new_decimal = $v + $amount;

	        $new_hex_component = dechex($new_decimal);
	        if(strlen($new_hex_component) < 2) :
	        	$new_hex_component = "0".$new_hex_component;
	        endif;
	        $color .= $new_hex_component;
	   	endforeach;

	   	return $color;
	}
}

if ( ! function_exists( 'wc_light_or_dark' ) ) {

	/**
	 * Detect if we should use a light or dark colour on a background colour
	 *
	 * @access public
	 * @param mixed $color
	 * @param string $dark (default: '#000000')
	 * @param string $light (default: '#FFFFFF')
	 * @return string
	 */
	function wc_light_or_dark( $color, $dark = '#000000', $light = '#FFFFFF' ) {
	    //return ( hexdec( $color ) > 0xffffff / 2 ) ? $dark : $light;
	    $hex = str_replace( '#', '', $color );

		$c_r = hexdec( substr( $hex, 0, 2 ) );
		$c_g = hexdec( substr( $hex, 2, 2 ) );
		$c_b = hexdec( substr( $hex, 4, 2 ) );
		$brightness = ( ( $c_r * 299 ) + ( $c_g * 587 ) + ( $c_b * 114 ) ) / 1000;

		return $brightness > 155 ? $dark : $light;
	}
}

if ( ! function_exists( 'wc_format_hex' ) ) {

	/**
	 * Format string as hex
	 *
	 * @access public
	 * @param string $hex
	 * @return string
	 */
	function wc_format_hex( $hex ) {

	    $hex = trim( str_replace( '#', '', $hex ) );

	    if ( strlen( $hex ) == 3 ) {
			$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
	    }

	    if ( $hex ) return '#' . $hex;
	}
}

/**
 * Format the postcode according to the country and length of the postcode
 *
 * @param   string	postcode
 * @param	string	country
 * @return  string	formatted postcode
 */
function wc_format_postcode( $postcode, $country ) {
	$postcode = strtoupper(trim($postcode));
	$postcode = trim(preg_replace('/[\s]/', '', $postcode));

	if ( in_array( $country, array('GB', 'CA') ) ) {
		$postcode = trim( substr_replace( $postcode, ' ', -3, 0 ) );
	}

	return $postcode;
}

/**
 * format_phone function.
 *
 * @access public
 * @param mixed $tel
 * @return string
 */
function wc_format_phone_number( $tel ) {
	$tel = str_replace( '.', '-', $tel );
	return $tel;
}
