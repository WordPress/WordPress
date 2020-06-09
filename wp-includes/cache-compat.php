<?php
/**
 * Object Cache API functions missing from 3rd party object caches.
 *
 * @link https://codex.wordpress.org/Class_Reference/WP_Object_Cache
 *
 * @package WordPress
 * @subpackage Cache
 */

if ( ! function_exists( 'wp_cache_get_multiple' ) ) :
	/**
	 * Compat function to mimic wp_cache_get_multiple.
	 * Retrieves multiple values from the cache.
	 *
	 * @ignore
	 * @since 5.5.0
	 *
	 * @see wp_cache_get_multiple()
	 *
	 * @param array $keys        Array of keys to fetch.
	 * @param bool  $force       Optional. Unused. Whether to force a refetch rather than relying on the local
	 *                           cache. Default false.
	 *
	 * @return array Array of values organized into groups.
	 */
	function wp_cache_get_multiple( $keys, $group = 'default', $force = false ) {
		$values = array();

		foreach ( $keys as $key ) {
			$values[ $key ] = wp_cache_get( $key, $group, $force );
		}

		return $values;
	}
endif;
