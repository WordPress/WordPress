<?php
/**
 * Array API: WordPress array utilities.
 *
 * @package WordPress
 * @since 5.6.0
 */

/**
 * Accesses an array in depth based on a path of keys.
 * It is the PHP equivalent of JavaScript's lodash.get, and mirroring it may help other components
 * retain some symmetry between client and server implementations.
 *
 * @param array $array   An array from which we want to retrieve some information.
 * @param array $path    An array of keys describing the path with which to retrieve information.
 * @param array $default The return value if the path is not set on the array or if the types of array and path are not arrays.
 *
 * @return array An array matching the path specified.
 */
function wp_array_get( $array, $path, $default = array() ) {
	// Confirm input values are expected type to avoid notice warnings.
	if ( ! is_array( $array ) || ! is_array( $path ) ) {
		return $default;
	}

	$path_length = count( $path );
	for ( $i = 0; $i < $path_length; ++$i ) {
		if ( ! isset( $array[ $path[ $i ] ] ) ) {
			return $default;
		}
		$array = $array[ $path[ $i ] ];
	}
	return $array;
}
