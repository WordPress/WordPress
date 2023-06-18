<?php

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'str_contains' ) ) {
	/**
	 * Polyfill for str_contains() function added in PHP 8.0.
	 *
	 * @param string $haystack The string to search in.
	 * @param string $needle The substring to search for in the haystack.
	 *
	 * @return bool True if $needle is in $haystack, otherwise false.
	 */
	function str_contains( $haystack, $needle ) {
		return ( '' === $needle || false !== strpos( $haystack, $needle ) );
	}
}
