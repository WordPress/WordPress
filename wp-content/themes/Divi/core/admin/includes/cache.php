<?php
/**
 * Core Cache implements an object cache.
 *
 * The Object Cache stores all of the cache data to memory only for the duration of the request.
 *
 * @package Core\Cache
 */

if ( ! function_exists( 'et_core_cache_add' ) ) :
/**
 * Adds data to the cache if it doesn't already exist.
 *
 * @since 1.0.0
 *
 * @param int|string $key    What to call the contents in the cache.
 * @param mixed      $data   The contents to store in the cache.
 * @param string     $group  Optional. Where to group the cache contents. Default 'default'.
 * @return bool False if cache key and group already exist, true on success
 */
function et_core_cache_add( $key, $data, $group = '' ) {
	return ET_Core_Cache::add( $key, $data, $group );
}
endif;

if ( ! function_exists( 'et_core_cache_set' ) ) :
/**
 * Sets the data contents into the cache.
 *
 * The cache contents is grouped by the $group parameter followed by the
 * $key. This allows for duplicate ids in unique groups.
 *
 * @since 1.0.0
 *
 * @param int|string $key    What to call the contents in the cache.
 * @param mixed      $data   The contents to store in the cache.
 * @param string     $group  Optional. Where to group the cache contents. Default 'default'.
 * @param int        $expire Not Used.
 * @return true Always returns true.
 */
function et_core_cache_set( $key, $data, $group = '' ) {
	return ET_Core_Cache::set( $key, $data, $group );
}
endif;

if ( ! function_exists( 'et_core_cache_get' ) ) :
/**
 * Retrieves the cache contents, if it exists.
 *
 * The contents will be first attempted to be retrieved by searching by the
 * key in the cache group. If the cache is hit (success) then the contents
 * are returned.
 *
 * @since 1.0.0
 *
 * @param int|string $key    What the contents in the cache are called.
 * @param string     $group  Optional. Where the cache contents are grouped. Default 'default'.
 * @return false|mixed False on failure to retrieve contents or the cache contents on success.
 */
function et_core_cache_get( $key, $group = '' ) {
	return ET_Core_Cache::get( $key, $group );
}
endif;

if ( ! function_exists( 'et_core_cache_get_group' ) ) :
/**
 * Retrieves the cache contents for entire group, if it exists.
 *
 * If the cache is hit (success) then the contents of the group
 * are returned.
 *
 * @since 1.0.0
 *
 * @param string     $group Where the cache contents are grouped.
 * @return false|mixed False on failure to retrieve contents or the cache contents on success.
 */
function et_core_cache_get_group( $group ) {
	return ET_Core_Cache::get_group( $group );
}
endif;

if ( ! function_exists( 'et_core_cache_has' ) ) :
/**
 * Check the cache contents, if given key and (optional) group exists.
 *
 * @since 1.0.0
 *
 * @param int|string $key   What the contents in the cache are called.
 * @param string     $group Optional. Where the cache contents are grouped. Default 'default'.
 * @return bool False on failure to retrieve contents or True on success.
 */
function et_core_cache_has( $key, $group = '' ) {
	return ET_Core_Cache::has( $key, $group );
}
endif;

if ( ! function_exists( 'et_core_cache_delete' ) ) :
/**
 * Removes the contents of the cache key in the group.
 *
 * If the cache key does not exist in the group, then nothing will happen.
 *
 * @since 1.0.0
 *
 * @param int|string $key   What the contents in the cache are called.
 * @param string     $group Optional. Where the cache contents are grouped. Default 'default'.
 * @return bool False if the contents weren't deleted and true on success.
 */
function et_core_cache_delete( $key, $group = '' ) {
	return ET_Core_Cache::delete( $key, $group );
}
endif;

if ( ! function_exists( 'et_core_cache_flush' ) ) :
/**
 * Clears the object cache of all data.
 *
 * @since 1.0.0
 * @return true Always returns true.
 */
function et_core_cache_flush() {
	return ET_Core_Cache::flush();
}
endif;
