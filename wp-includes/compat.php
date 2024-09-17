<?php
/**
 * WordPress implementation for PHP functions either missing from older PHP versions or not included by default.
 *
 * This file is loaded extremely early and the functions can be relied upon by drop-ins.
 * Ergo, please ensure you do not rely on external functions when writing code for this file.
 * Only use functions built into PHP or are defined in this file and have adequate testing
 * and error suppression to ensure the file will run correctly and not break websites.
 *
 * @package PHP
 * @access private
 */

// If gettext isn't available.
if ( ! function_exists( '_' ) ) {
	function _( $message ) {
		return $message;
	}
}

/**
 * Returns whether PCRE/u (PCRE_UTF8 modifier) is available for use.
 *
 * @ignore
 * @since 4.2.2
 * @access private
 *
 * @param bool $set - Used for testing only
 *             null   : default - get PCRE/u capability
 *             false  : Used for testing - return false for future calls to this function
 *             'reset': Used for testing - restore default behavior of this function
 */
function _wp_can_use_pcre_u( $set = null ) {
	static $utf8_pcre = 'reset';

	if ( null !== $set ) {
		$utf8_pcre = $set;
	}

	if ( 'reset' === $utf8_pcre ) {
		// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged -- intentional error generated to detect PCRE/u support.
		$utf8_pcre = @preg_match( '/^./u', 'a' );
	}

	return $utf8_pcre;
}

/**
 * Indicates if a given slug for a character set represents the UTF-8 text encoding.
 *
 * A charset is considered to represent UTF-8 if it is a case-insensitive match
 * of "UTF-8" with or without the hyphen.
 *
 * Example:
 *
 *     true  === _is_utf8_charset( 'UTF-8' );
 *     true  === _is_utf8_charset( 'utf8' );
 *     false === _is_utf8_charset( 'latin1' );
 *     false === _is_utf8_charset( 'UTF 8' );
 *
 *     // Only strings match.
 *     false === _is_utf8_charset( [ 'charset' => 'utf-8' ] );
 *
 * `is_utf8_charset` should be used outside of this file.
 *
 * @ignore
 * @since 6.6.1
 *
 * @param string $charset_slug Slug representing a text character encoding, or "charset".
 *                             E.g. "UTF-8", "Windows-1252", "ISO-8859-1", "SJIS".
 *
 * @return bool Whether the slug represents the UTF-8 encoding.
 */
function _is_utf8_charset( $charset_slug ) {
	if ( ! is_string( $charset_slug ) ) {
		return false;
	}

	return (
		0 === strcasecmp( 'UTF-8', $charset_slug ) ||
		0 === strcasecmp( 'UTF8', $charset_slug )
	);
}

if ( ! function_exists( 'mb_substr' ) ) :
	/**
	 * Compat function to mimic mb_substr().
	 *
	 * @ignore
	 * @since 3.2.0
	 *
	 * @see _mb_substr()
	 *
	 * @param string      $string   The string to extract the substring from.
	 * @param int         $start    Position to being extraction from in `$string`.
	 * @param int|null    $length   Optional. Maximum number of characters to extract from `$string`.
	 *                              Default null.
	 * @param string|null $encoding Optional. Character encoding to use. Default null.
	 * @return string Extracted substring.
	 */
	function mb_substr( $string, $start, $length = null, $encoding = null ) { // phpcs:ignore Universal.NamingConventions.NoReservedKeywordParameterNames.stringFound
		return _mb_substr( $string, $start, $length, $encoding );
	}
endif;

/**
 * Internal compat function to mimic mb_substr().
 *
 * Only understands UTF-8 and 8bit. All other character sets will be treated as 8bit.
 * For `$encoding === UTF-8`, the `$str` input is expected to be a valid UTF-8 byte
 * sequence. The behavior of this function for invalid inputs is undefined.
 *
 * @ignore
 * @since 3.2.0
 *
 * @param string      $str      The string to extract the substring from.
 * @param int         $start    Position to being extraction from in `$str`.
 * @param int|null    $length   Optional. Maximum number of characters to extract from `$str`.
 *                              Default null.
 * @param string|null $encoding Optional. Character encoding to use. Default null.
 * @return string Extracted substring.
 */
function _mb_substr( $str, $start, $length = null, $encoding = null ) {
	if ( null === $str ) {
		return '';
	}

	if ( null === $encoding ) {
		$encoding = get_option( 'blog_charset' );
	}

	/*
	 * The solution below works only for UTF-8, so in case of a different
	 * charset just use built-in substr().
	 */
	if ( ! _is_utf8_charset( $encoding ) ) {
		return is_null( $length ) ? substr( $str, $start ) : substr( $str, $start, $length );
	}

	if ( _wp_can_use_pcre_u() ) {
		// Use the regex unicode support to separate the UTF-8 characters into an array.
		preg_match_all( '/./us', $str, $match );
		$chars = is_null( $length ) ? array_slice( $match[0], $start ) : array_slice( $match[0], $start, $length );
		return implode( '', $chars );
	}

	$regex = '/(
		[\x00-\x7F]                  # single-byte sequences   0xxxxxxx
		| [\xC2-\xDF][\x80-\xBF]       # double-byte sequences   110xxxxx 10xxxxxx
		| \xE0[\xA0-\xBF][\x80-\xBF]   # triple-byte sequences   1110xxxx 10xxxxxx * 2
		| [\xE1-\xEC][\x80-\xBF]{2}
		| \xED[\x80-\x9F][\x80-\xBF]
		| [\xEE-\xEF][\x80-\xBF]{2}
		| \xF0[\x90-\xBF][\x80-\xBF]{2} # four-byte sequences   11110xxx 10xxxxxx * 3
		| [\xF1-\xF3][\x80-\xBF]{3}
		| \xF4[\x80-\x8F][\x80-\xBF]{2}
	)/x';

	// Start with 1 element instead of 0 since the first thing we do is pop.
	$chars = array( '' );

	do {
		// We had some string left over from the last round, but we counted it in that last round.
		array_pop( $chars );

		/*
		 * Split by UTF-8 character, limit to 1000 characters (last array element will contain
		 * the rest of the string).
		 */
		$pieces = preg_split( $regex, $str, 1000, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY );

		$chars = array_merge( $chars, $pieces );

		// If there's anything left over, repeat the loop.
	} while ( count( $pieces ) > 1 && $str = array_pop( $pieces ) );

	return implode( '', array_slice( $chars, $start, $length ) );
}

if ( ! function_exists( 'mb_strlen' ) ) :
	/**
	 * Compat function to mimic mb_strlen().
	 *
	 * @ignore
	 * @since 4.2.0
	 *
	 * @see _mb_strlen()
	 *
	 * @param string      $string   The string to retrieve the character length from.
	 * @param string|null $encoding Optional. Character encoding to use. Default null.
	 * @return int String length of `$string`.
	 */
	function mb_strlen( $string, $encoding = null ) { // phpcs:ignore Universal.NamingConventions.NoReservedKeywordParameterNames.stringFound
		return _mb_strlen( $string, $encoding );
	}
endif;

/**
 * Internal compat function to mimic mb_strlen().
 *
 * Only understands UTF-8 and 8bit. All other character sets will be treated as 8bit.
 * For `$encoding === UTF-8`, the `$str` input is expected to be a valid UTF-8 byte
 * sequence. The behavior of this function for invalid inputs is undefined.
 *
 * @ignore
 * @since 4.2.0
 *
 * @param string      $str      The string to retrieve the character length from.
 * @param string|null $encoding Optional. Character encoding to use. Default null.
 * @return int String length of `$str`.
 */
function _mb_strlen( $str, $encoding = null ) {
	if ( null === $encoding ) {
		$encoding = get_option( 'blog_charset' );
	}

	/*
	 * The solution below works only for UTF-8, so in case of a different charset
	 * just use built-in strlen().
	 */
	if ( ! _is_utf8_charset( $encoding ) ) {
		return strlen( $str );
	}

	if ( _wp_can_use_pcre_u() ) {
		// Use the regex unicode support to separate the UTF-8 characters into an array.
		preg_match_all( '/./us', $str, $match );
		return count( $match[0] );
	}

	$regex = '/(?:
		[\x00-\x7F]                  # single-byte sequences   0xxxxxxx
		| [\xC2-\xDF][\x80-\xBF]       # double-byte sequences   110xxxxx 10xxxxxx
		| \xE0[\xA0-\xBF][\x80-\xBF]   # triple-byte sequences   1110xxxx 10xxxxxx * 2
		| [\xE1-\xEC][\x80-\xBF]{2}
		| \xED[\x80-\x9F][\x80-\xBF]
		| [\xEE-\xEF][\x80-\xBF]{2}
		| \xF0[\x90-\xBF][\x80-\xBF]{2} # four-byte sequences   11110xxx 10xxxxxx * 3
		| [\xF1-\xF3][\x80-\xBF]{3}
		| \xF4[\x80-\x8F][\x80-\xBF]{2}
	)/x';

	// Start at 1 instead of 0 since the first thing we do is decrement.
	$count = 1;

	do {
		// We had some string left over from the last round, but we counted it in that last round.
		--$count;

		/*
		 * Split by UTF-8 character, limit to 1000 characters (last array element will contain
		 * the rest of the string).
		 */
		$pieces = preg_split( $regex, $str, 1000 );

		// Increment.
		$count += count( $pieces );

		// If there's anything left over, repeat the loop.
	} while ( $str = array_pop( $pieces ) );

	// Fencepost: preg_split() always returns one extra item in the array.
	return --$count;
}

if ( ! function_exists( 'hash_hmac' ) ) :
	/**
	 * Compat function to mimic hash_hmac().
	 *
	 * The Hash extension is bundled with PHP by default since PHP 5.1.2.
	 * However, the extension may be explicitly disabled on select servers.
	 * As of PHP 7.4.0, the Hash extension is a core PHP extension and can no
	 * longer be disabled.
	 * I.e. when PHP 7.4.0 becomes the minimum requirement, this polyfill
	 * and the associated `_hash_hmac()` function can be safely removed.
	 *
	 * @ignore
	 * @since 3.2.0
	 *
	 * @see _hash_hmac()
	 *
	 * @param string $algo   Hash algorithm. Accepts 'md5' or 'sha1'.
	 * @param string $data   Data to be hashed.
	 * @param string $key    Secret key to use for generating the hash.
	 * @param bool   $binary Optional. Whether to output raw binary data (true),
	 *                       or lowercase hexits (false). Default false.
	 * @return string|false The hash in output determined by `$binary`.
	 *                      False if `$algo` is unknown or invalid.
	 */
	function hash_hmac( $algo, $data, $key, $binary = false ) {
		return _hash_hmac( $algo, $data, $key, $binary );
	}
endif;

/**
 * Internal compat function to mimic hash_hmac().
 *
 * @ignore
 * @since 3.2.0
 *
 * @param string $algo   Hash algorithm. Accepts 'md5' or 'sha1'.
 * @param string $data   Data to be hashed.
 * @param string $key    Secret key to use for generating the hash.
 * @param bool   $binary Optional. Whether to output raw binary data (true),
 *                       or lowercase hexits (false). Default false.
 * @return string|false The hash in output determined by `$binary`.
 *                      False if `$algo` is unknown or invalid.
 */
function _hash_hmac( $algo, $data, $key, $binary = false ) {
	$packs = array(
		'md5'  => 'H32',
		'sha1' => 'H40',
	);

	if ( ! isset( $packs[ $algo ] ) ) {
		return false;
	}

	$pack = $packs[ $algo ];

	if ( strlen( $key ) > 64 ) {
		$key = pack( $pack, $algo( $key ) );
	}

	$key = str_pad( $key, 64, chr( 0 ) );

	$ipad = ( substr( $key, 0, 64 ) ^ str_repeat( chr( 0x36 ), 64 ) );
	$opad = ( substr( $key, 0, 64 ) ^ str_repeat( chr( 0x5C ), 64 ) );

	$hmac = $algo( $opad . pack( $pack, $algo( $ipad . $data ) ) );

	if ( $binary ) {
		return pack( $pack, $hmac );
	}

	return $hmac;
}

if ( ! function_exists( 'hash_equals' ) ) :
	/**
	 * Timing attack safe string comparison.
	 *
	 * Compares two strings using the same time whether they're equal or not.
	 *
	 * Note: It can leak the length of a string when arguments of differing length are supplied.
	 *
	 * This function was added in PHP 5.6.
	 * However, the Hash extension may be explicitly disabled on select servers.
	 * As of PHP 7.4.0, the Hash extension is a core PHP extension and can no
	 * longer be disabled.
	 * I.e. when PHP 7.4.0 becomes the minimum requirement, this polyfill
	 * can be safely removed.
	 *
	 * @since 3.9.2
	 *
	 * @param string $known_string Expected string.
	 * @param string $user_string  Actual, user supplied, string.
	 * @return bool Whether strings are equal.
	 */
	function hash_equals( $known_string, $user_string ) {
		$known_string_length = strlen( $known_string );

		if ( strlen( $user_string ) !== $known_string_length ) {
			return false;
		}

		$result = 0;

		// Do not attempt to "optimize" this.
		for ( $i = 0; $i < $known_string_length; $i++ ) {
			$result |= ord( $known_string[ $i ] ) ^ ord( $user_string[ $i ] );
		}

		return 0 === $result;
	}
endif;

// sodium_crypto_box() was introduced in PHP 7.2.
if ( ! function_exists( 'sodium_crypto_box' ) ) {
	require ABSPATH . WPINC . '/sodium_compat/autoload.php';
}

if ( ! function_exists( 'is_countable' ) ) {
	/**
	 * Polyfill for is_countable() function added in PHP 7.3.
	 *
	 * Verify that the content of a variable is an array or an object
	 * implementing the Countable interface.
	 *
	 * @since 4.9.6
	 *
	 * @param mixed $value The value to check.
	 * @return bool True if `$value` is countable, false otherwise.
	 */
	function is_countable( $value ) {
		return ( is_array( $value )
			|| $value instanceof Countable
			|| $value instanceof SimpleXMLElement
			|| $value instanceof ResourceBundle
		);
	}
}

if ( ! function_exists( 'array_key_first' ) ) {
	/**
	 * Polyfill for array_key_first() function added in PHP 7.3.
	 *
	 * Get the first key of the given array without affecting
	 * the internal array pointer.
	 *
	 * @since 5.9.0
	 *
	 * @param array $array An array.
	 * @return string|int|null The first key of array if the array
	 *                         is not empty; `null` otherwise.
	 */
	function array_key_first( array $array ) { // phpcs:ignore Universal.NamingConventions.NoReservedKeywordParameterNames.arrayFound
		foreach ( $array as $key => $value ) {
			return $key;
		}
	}
}

if ( ! function_exists( 'array_key_last' ) ) {
	/**
	 * Polyfill for `array_key_last()` function added in PHP 7.3.
	 *
	 * Get the last key of the given array without affecting the
	 * internal array pointer.
	 *
	 * @since 5.9.0
	 *
	 * @param array $array An array.
	 * @return string|int|null The last key of array if the array
	 *.                        is not empty; `null` otherwise.
	 */
	function array_key_last( array $array ) { // phpcs:ignore Universal.NamingConventions.NoReservedKeywordParameterNames.arrayFound
		if ( empty( $array ) ) {
			return null;
		}

		end( $array );

		return key( $array );
	}
}

if ( ! function_exists( 'array_is_list' ) ) {
	/**
	 * Polyfill for `array_is_list()` function added in PHP 8.1.
	 *
	 * Determines if the given array is a list.
	 *
	 * An array is considered a list if its keys consist of consecutive numbers from 0 to count($array)-1.
	 *
	 * @see https://github.com/symfony/polyfill-php81/tree/main
	 *
	 * @since 6.5.0
	 *
	 * @param array<mixed> $arr The array being evaluated.
	 * @return bool True if array is a list, false otherwise.
	 */
	function array_is_list( $arr ) {
		if ( ( array() === $arr ) || ( array_values( $arr ) === $arr ) ) {
			return true;
		}

		$next_key = -1;

		foreach ( $arr as $k => $v ) {
			if ( ++$next_key !== $k ) {
				return false;
			}
		}

		return true;
	}
}

if ( ! function_exists( 'str_contains' ) ) {
	/**
	 * Polyfill for `str_contains()` function added in PHP 8.0.
	 *
	 * Performs a case-sensitive check indicating if needle is
	 * contained in haystack.
	 *
	 * @since 5.9.0
	 *
	 * @param string $haystack The string to search in.
	 * @param string $needle   The substring to search for in the `$haystack`.
	 * @return bool True if `$needle` is in `$haystack`, otherwise false.
	 */
	function str_contains( $haystack, $needle ) {
		if ( '' === $needle ) {
			return true;
		}

		return false !== strpos( $haystack, $needle );
	}
}

if ( ! function_exists( 'str_starts_with' ) ) {
	/**
	 * Polyfill for `str_starts_with()` function added in PHP 8.0.
	 *
	 * Performs a case-sensitive check indicating if
	 * the haystack begins with needle.
	 *
	 * @since 5.9.0
	 *
	 * @param string $haystack The string to search in.
	 * @param string $needle   The substring to search for in the `$haystack`.
	 * @return bool True if `$haystack` starts with `$needle`, otherwise false.
	 */
	function str_starts_with( $haystack, $needle ) {
		if ( '' === $needle ) {
			return true;
		}

		return 0 === strpos( $haystack, $needle );
	}
}

if ( ! function_exists( 'str_ends_with' ) ) {
	/**
	 * Polyfill for `str_ends_with()` function added in PHP 8.0.
	 *
	 * Performs a case-sensitive check indicating if
	 * the haystack ends with needle.
	 *
	 * @since 5.9.0
	 *
	 * @param string $haystack The string to search in.
	 * @param string $needle   The substring to search for in the `$haystack`.
	 * @return bool True if `$haystack` ends with `$needle`, otherwise false.
	 */
	function str_ends_with( $haystack, $needle ) {
		if ( '' === $haystack ) {
			return '' === $needle;
		}

		$len = strlen( $needle );

		return substr( $haystack, -$len, $len ) === $needle;
	}
}

// IMAGETYPE_AVIF constant is only defined in PHP 8.x or later.
if ( ! defined( 'IMAGETYPE_AVIF' ) ) {
	define( 'IMAGETYPE_AVIF', 19 );
}

// IMG_AVIF constant is only defined in PHP 8.x or later.
if ( ! defined( 'IMG_AVIF' ) ) {
	define( 'IMG_AVIF', IMAGETYPE_AVIF );
}

// IMAGETYPE_HEIC constant is not yet defined in PHP as of PHP 8.3.
if ( ! defined( 'IMAGETYPE_HEIC' ) ) {
	define( 'IMAGETYPE_HEIC', 99 );
}
