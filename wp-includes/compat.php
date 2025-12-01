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
	/**
	 * Compat function to mimic _(), an alias of gettext().
	 *
	 * @since 0.71
	 *
	 * @see https://php.net/manual/en/function.gettext.php
	 *
	 * @param string $message The message being translated.
	 * @return string
	 */
	function _( $message ) {
		return $message;
	}
}

/**
 * Returns whether PCRE/u (PCRE_UTF8 modifier) is available for use.
 *
 * @ignore
 * @since 4.2.2
 * @since 6.9.0 Deprecated the `$set` argument.
 * @access private
 *
 * @param bool $set Deprecated. This argument is no longer used for testing purposes.
 */
function _wp_can_use_pcre_u( $set = null ) {
	static $utf8_pcre = null;

	if ( isset( $set ) ) {
		_deprecated_argument( __FUNCTION__, '6.9.0' );
	}

	if ( isset( $utf8_pcre ) ) {
		return $utf8_pcre;
	}

	$utf8_pcre = true;
	set_error_handler(
		function ( $errno, $errstr ) use ( &$utf8_pcre ) {
			if ( str_starts_with( $errstr, 'preg_match():' ) ) {
				$utf8_pcre = false;
				return true;
			}

			return false;
		},
		E_WARNING
	);

	/*
	 * Attempt to compile a PCRE pattern with the PCRE_UTF8 flag. For
	 * systems lacking Unicode support this will trigger a warning
	 * during compilation, which the error handler will intercept.
	 */
	preg_match( '//u', '' );
	restore_error_handler();

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
 * Only supports UTF-8 and non-shifting single-byte encodings. For all other encodings
 * expect the substrings to be misaligned. When the given encoding (or the `blog_charset`
 * if none is provided) isn’t UTF-8 then the function returns the output of {@see \substr()}.
 *
 * @ignore
 * @since 3.2.0
 *
 * @param string      $str      The string to extract the substring from.
 * @param int         $start    Character offset at which to start the substring extraction.
 * @param int|null    $length   Optional. Maximum number of characters to extract from `$str`.
 *                              Default null.
 * @param string|null $encoding Optional. Character encoding to use. Default null.
 * @return string Extracted substring.
 */
function _mb_substr( $str, $start, $length = null, $encoding = null ) {
	if ( null === $str ) {
		return '';
	}

	// The solution below works only for UTF-8; treat all other encodings as byte streams.
	if ( ! _is_utf8_charset( $encoding ?? get_option( 'blog_charset' ) ) ) {
		return is_null( $length ) ? substr( $str, $start ) : substr( $str, $start, $length );
	}

	$total_length = ( $start < 0 || $length < 0 )
		? _wp_utf8_codepoint_count( $str )
		: 0;

	$normalized_start = $start < 0
		? max( 0, $total_length + $start )
		: $start;

	/*
	 * The starting offset is provided as characters, which means this needs to
	 * find how many bytes that many characters occupies at the start of the string.
	 */
	$starting_byte_offset = _wp_utf8_codepoint_span( $str, 0, $normalized_start );

	$normalized_length = $length < 0
		? max( 0, $total_length - $normalized_start + $length )
		: $length;

	/*
	 * This is the main step. It finds how many bytes the given length of code points
	 * occupies in the input, starting at the byte offset calculated above.
	 */
	$byte_length = isset( $normalized_length )
		? _wp_utf8_codepoint_span( $str, $starting_byte_offset, $normalized_length )
		: ( strlen( $str ) - $starting_byte_offset );

	// The result is a normal byte-level substring using the computed ranges.
	return substr( $str, $starting_byte_offset, $byte_length );
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
 * Only supports UTF-8 and non-shifting single-byte encodings. For all other
 * encodings expect the counts to be wrong. When the given encoding (or the
 * `blog_charset` if none is provided) isn’t UTF-8 then the function returns
 * the byte-count of the provided string.
 *
 * @ignore
 * @since 4.2.0
 *
 * @param string      $str      The string to retrieve the character length from.
 * @param string|null $encoding Optional. Count characters according to this encoding.
 *                              Default is to consult `blog_charset`.
 * @return int Count of code points if UTF-8, byte length otherwise.
 */
function _mb_strlen( $str, $encoding = null ) {
	return _is_utf8_charset( $encoding ?? get_option( 'blog_charset' ) )
		? _wp_utf8_codepoint_count( $str )
		: strlen( $str );
}

if ( ! function_exists( 'utf8_encode' ) ) :
	if ( extension_loaded( 'mbstring' ) ) :
		/**
		 * Converts a string from ISO-8859-1 to UTF-8.
		 *
		 * @deprecated Use {@see \mb_convert_encoding()} instead.
		 *
		 * @since 6.9.0
		 *
		 * @param string $iso_8859_1_text Text treated as ISO-8859-1 (latin1) bytes.
		 * @return string Text converted into a UTF-8.
		 */
		function utf8_encode( $iso_8859_1_text ): string {
			_deprecated_function( __FUNCTION__, '6.9.0', 'mb_convert_encoding' );

			return mb_convert_encoding( $iso_8859_1_text, 'UTF-8', 'ISO-8859-1' );
		}

	else :
		/**
		 * @ignore
		 * @private
		 *
		 * @since 6.9.0
		 */
		function utf8_encode( $iso_8859_1_text ): string {
			_deprecated_function( __FUNCTION__, '6.9.0', 'mb_convert_encoding' );

			return _wp_utf8_encode_fallback( $iso_8859_1_text );
		}

	endif;
endif;

if ( ! function_exists( 'utf8_decode' ) ) :
	if ( extension_loaded( 'mbstring' ) ) :
		/**
		 * Converts a string from UTF-8 to ISO-8859-1.
		 *
		 * @deprecated Use {@see \mb_convert_encoding()} instead.
		 *
		 * @since 6.9.0
		 *
		 * @param string $utf8_text Text treated as UTF-8.
		 * @return string Text converted into ISO-8859-1.
		 */
		function utf8_decode( $utf8_text ): string {
			_deprecated_function( __FUNCTION__, '6.9.0', 'mb_convert_encoding' );

			return mb_convert_encoding( $utf8_text, 'ISO-8859-1', 'UTF-8' );
		}

	else :
		/**
		 * @ignore
		 * @private
		 *
		 * @since 6.9.0
		 */
		function utf8_decode( $utf8_text ): string {
			_deprecated_function( __FUNCTION__, '6.9.0', 'mb_convert_encoding' );

			return _wp_utf8_decode_fallback( $utf8_text );
		}

	endif;
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
		if ( empty( $array ) ) {
			return null;
		}

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

if ( ! function_exists( 'array_find' ) ) {
	/**
	 * Polyfill for `array_find()` function added in PHP 8.4.
	 *
	 * Searches an array for the first element that passes a given callback.
	 *
	 * @since 6.8.0
	 *
	 * @param array    $array    The array to search.
	 * @param callable $callback The callback to run for each element.
	 * @return mixed|null The first element in the array that passes the `$callback`, otherwise null.
	 */
	function array_find( array $array, callable $callback ) { // phpcs:ignore Universal.NamingConventions.NoReservedKeywordParameterNames.arrayFound
		foreach ( $array as $key => $value ) {
			if ( $callback( $value, $key ) ) {
				return $value;
			}
		}

		return null;
	}
}

if ( ! function_exists( 'array_find_key' ) ) {
	/**
	 * Polyfill for `array_find_key()` function added in PHP 8.4.
	 *
	 * Searches an array for the first key that passes a given callback.
	 *
	 * @since 6.8.0
	 *
	 * @param array    $array    The array to search.
	 * @param callable $callback The callback to run for each element.
	 * @return int|string|null The first key in the array that passes the `$callback`, otherwise null.
	 */
	function array_find_key( array $array, callable $callback ) { // phpcs:ignore Universal.NamingConventions.NoReservedKeywordParameterNames.arrayFound
		foreach ( $array as $key => $value ) {
			if ( $callback( $value, $key ) ) {
				return $key;
			}
		}

		return null;
	}
}

if ( ! function_exists( 'array_any' ) ) {
	/**
	 * Polyfill for `array_any()` function added in PHP 8.4.
	 *
	 * Checks if any element of an array passes a given callback.
	 *
	 * @since 6.8.0
	 *
	 * @param array    $array    The array to check.
	 * @param callable $callback The callback to run for each element.
	 * @return bool True if any element in the array passes the `$callback`, otherwise false.
	 */
	function array_any( array $array, callable $callback ): bool { // phpcs:ignore Universal.NamingConventions.NoReservedKeywordParameterNames.arrayFound
		foreach ( $array as $key => $value ) {
			if ( $callback( $value, $key ) ) {
				return true;
			}
		}

		return false;
	}
}

if ( ! function_exists( 'array_all' ) ) {
	/**
	 * Polyfill for `array_all()` function added in PHP 8.4.
	 *
	 * Checks if all elements of an array pass a given callback.
	 *
	 * @since 6.8.0
	 *
	 * @param array    $array    The array to check.
	 * @param callable $callback The callback to run for each element.
	 * @return bool True if all elements in the array pass the `$callback`, otherwise false.
	 */
	function array_all( array $array, callable $callback ): bool { // phpcs:ignore Universal.NamingConventions.NoReservedKeywordParameterNames.arrayFound
		foreach ( $array as $key => $value ) {
			if ( ! $callback( $value, $key ) ) {
				return false;
			}
		}

		return true;
	}
}

if ( ! function_exists( 'array_first' ) ) {
	/**
	 * Polyfill for `array_first()` function added in PHP 8.5.
	 *
	 * Returns the first element of an array.
	 *
	 * @since 6.9.0
	 *
	 * @param array $array The array to get the first element from.
	 * @return mixed|null The first element of the array, or null if the array is empty.
	 */
	function array_first( array $array ) { // phpcs:ignore Universal.NamingConventions.NoReservedKeywordParameterNames.arrayFound
		if ( empty( $array ) ) {
			return null;
		}

		foreach ( $array as $value ) {
			return $value;
		}
	}
}

if ( ! function_exists( 'array_last' ) ) {
	/**
	 * Polyfill for `array_last()` function added in PHP 8.5.
	 *
	 * Returns the last element of an array.
	 *
	 * @since 6.9.0
	 *
	 * @param array $array The array to get the last element from.
	 * @return mixed|null The last element of the array, or null if the array is empty.
	 */
	function array_last( array $array ) { // phpcs:ignore Universal.NamingConventions.NoReservedKeywordParameterNames.arrayFound
		if ( empty( $array ) ) {
			return null;
		}

		return $array[ array_key_last( $array ) ];
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

// IMAGETYPE_HEIF constant is only defined in PHP 8.5 or later.
if ( ! defined( 'IMAGETYPE_HEIF' ) ) {
	define( 'IMAGETYPE_HEIF', 20 );
}
