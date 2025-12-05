<?php
namespace Elementor\Core\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Str {

	/**
	 * Convert a non-latin URL to an IDN one.
	 * Note: Max length is 64 chars.
	 *
	 * @param string $url - A URL to encode.
	 *
	 * @return string - IDN encoded URL ( e.g. `http://é.com` will be encoded to `http://xn--9ca.com` ).
	 */
	public static function encode_idn_url( $url ) {
		return preg_replace_callback( '/(https?:\/\/)(.+)/', function ( $matches ) {
			// WP >= 6.2-alpha
			if ( class_exists( '\WpOrg\Requests\IdnaEncoder' ) ) {
				$class = \WpOrg\Requests\IdnaEncoder::class;
			} else {
				$class = \Requests_IDNAEncoder::class;
			}

			return $matches[1] . $class::encode( $matches[2] );
		}, $url );
	}

	/**
	 * Checks if a string ends with a given substring
	 *
	 * @param $haystack
	 * @param $needle
	 * @return bool
	 */
	public static function ends_with( $haystack, $needle ) {
		return substr( $haystack, -strlen( $needle ) ) === $needle;
	}
}
