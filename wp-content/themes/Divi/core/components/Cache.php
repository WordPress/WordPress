<?php
/**
 * Core Cache implements an object cache.
 *
 * The Object Cache stores all of the cache data to memory only for the duration of the request.
 *
 * @package Core\Cache
 */

/**
 * Core class that implements an object cache.
 *
 * The Object Cache is used to save on trips to the database. The
 * Object Cache stores all of the cache data to memory and makes the cache
 * contents available by using a key, which is used to name and later retrieve
 * the cache contents.
 *
 * @private
 *
 * @package ET\Core\Cache
 */
final class ET_Core_Cache {

	/**
	 * Cached data.
	 *
	 * @since 1.0.0
	 *
	 * @type array
	 */
	private static $cache = array();

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
	public static function add( $key, $data, $group = 'group' ) {
		if ( empty( $group ) ) {
			$group = 'default';
		}

		if ( self::_exists( $key, $group ) ) {
			return false;
		}

		return self::set( $key, $data, $group );
	}

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
	public static function set( $key, $data, $group = 'default' ) {
		if ( empty( $group ) ) {
			$group = 'default';
		}

		if ( is_object( $data ) ) {
			$data = clone $data;
		}

		self::$cache[ $group ][ $key ] = $data;

		return true;
	}

	/**
	 * Retrieves the cache contents, if it exists.
	 *
	 * The contents will be first attempted to be retrieved by searching by the
	 * key in the cache group. If the cache is hit (success) then the contents
	 * are returned.
	 *
	 * @since 1.0.0
	 *
	 * @param int|string $key   What the contents in the cache are called.
	 * @param string     $group Optional. Where the cache contents are grouped. Default 'default'.
	 * @return false|mixed False on failure to retrieve contents or the cache contents on success.
	 */
	public static function get( $key, $group = 'default' ) {
		if ( empty( $group ) ) {
			$group = 'default';
		}

		if ( self::_exists( $key, $group ) ) {
			if ( is_object( self::$cache[ $group ][ $key ] ) ) {
				return clone self::$cache[ $group ][ $key ];
			} else {
				return self::$cache[ $group ][ $key ];
			}
		}

		return false;
	}

	/**
	 * Retrieves the cache contents for entire group, if it exists.
	 *
	 * If the cache is hit (success) then the contents of the group
	 * are returned.
	 *
	 * @since 1.0.0
	 *
	 * @param string $group Where the cache contents are grouped.
	 * @return false|mixed False on failure to retrieve contents or the cache contents on success.
	 */
	public static function get_group( $group ) {
		if ( isset( self::$cache[ $group ] ) ) {
			return self::$cache[ $group ];
		}

		return false;
	}

	/**
	 * Check the cache contents, if given key and (optional) group exists.
	 *
	 * @since 1.0.0
	 *
	 * @param int|string $key   What the contents in the cache are called.
	 * @param string     $group Optional. Where the cache contents are grouped. Default 'default'.
	 * @return bool False on failure to retrieve contents or True on success.
	 */
	public static function has( $key, $group = 'default' ) {
		if ( empty( $group ) ) {
			$group = 'default';
		}
		return self::_exists( $key, $group );
	}

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
	public static function delete( $key, $group = 'default' ) {
		if ( empty( $group ) ) {
			$group = 'default';
		}

		if ( ! self::$_exists( $key, $group ) ) {
			return false;
		}

		unset( self::$cache[ $group ][ $key ] );
		return true;
	}

	/**
	 * Clears the object cache of all data.
	 *
	 * @since 1.0.0
	 *
	 * @return true Always returns true.
	 */
	public static function flush() {
		self::$cache = array();

		return true;
	}

	/**
	 * Serves as a utility function to determine whether a key exists in the cache.
	 *
	 * @since 1.0.0
	 * @private
	 *
	 * @param int|string $key   Cache key to check for existence.
	 * @param string     $group Cache group for the key existence check.
	 * @return bool Whether the key exists in the cache for the given group.
	 */
	private static function _exists( $key, $group ) {
		return isset( self::$cache[ $group ] ) && isset( self::$cache[ $group ][ $key ] );
	}
}


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
