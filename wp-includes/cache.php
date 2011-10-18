<?php
/**
 * Object Cache API
 *
 * @link http://codex.wordpress.org/Function_Reference/WP_Cache
 *
 * @package WordPress
 * @subpackage Cache
 */

/**
 * Adds data to the cache, if the cache key doesn't already exist.
 *
 * @since 2.0.0
 * @uses $wp_object_cache Object Cache Class
 * @see WP_Object_Cache::add()
 *
 * @param int|string $key The cache key to use for retrieval later
 * @param mixed $data The data to add to the cache store
 * @param string $group The group to add the cache to
 * @param int $expire When the cache data should be expired
 * @return unknown
 */
function wp_cache_add($key, $data, $group = '', $expire = 0) {
	global $wp_object_cache;

	return $wp_object_cache->add($key, $data, $group, $expire);
}

/**
 * Closes the cache.
 *
 * This function has ceased to do anything since WordPress 2.5. The
 * functionality was removed along with the rest of the persistent cache. This
 * does not mean that plugins can't implement this function when they need to
 * make sure that the cache is cleaned up after WordPress no longer needs it.
 *
 * @since 2.0.0
 *
 * @return bool Always returns True
 */
function wp_cache_close() {
	return true;
}

/**
 * Decrement numeric cache item's value
 *
 * @since 3.3.0
 * @uses $wp_object_cache Object Cache Class
 * @see WP_Object_Cache::decr()
 *
 * @param int|string $key The cache key to increment
 * @param int $offset The amount by which to decrement the item's value.  Default is 1.
 * @param string $group The group the key is in.
 * @return false|int False on failure, the item's new value on success.
 */
function wp_cache_decr( $key, $offset = 1, $group = '' ) {
	global $wp_object_cache;

	return $wp_object_cache->decr( $key, $offset, $group );
}

/**
 * Removes the cache contents matching key and group.
 *
 * @since 2.0.0
 * @uses $wp_object_cache Object Cache Class
 * @see WP_Object_Cache::delete()
 *
 * @param int|string $key What the contents in the cache are called
 * @param string $group Where the cache contents are grouped
 * @return bool True on successful removal, false on failure
 */
function wp_cache_delete($key, $group = '') {
	global $wp_object_cache;

	return $wp_object_cache->delete($key, $group);
}

/**
 * Removes all cache items.
 *
 * @since 2.0.0
 * @uses $wp_object_cache Object Cache Class
 * @see WP_Object_Cache::flush()
 *
 * @return bool Always returns true
 */
function wp_cache_flush() {
	global $wp_object_cache;

	return $wp_object_cache->flush();
}

/**
 * Retrieves the cache contents from the cache by key and group.
 *
 * @since 2.0.0
 * @uses $wp_object_cache Object Cache Class
 * @see WP_Object_Cache::get()
 *
 * @param int|string $key What the contents in the cache are called
 * @param string $group Where the cache contents are grouped
 * @param bool $force Whether to force an update of the local cache from the persistent cache (default is false)
 * @return bool|mixed False on failure to retrieve contents or the cache
 *		contents on success
 */
function wp_cache_get( $key, $group = '', $force = false ) {
	global $wp_object_cache;

	return $wp_object_cache->get( $key, $group, $force );
}

/**
 * Increment numeric cache item's value
 *
 * @since 3.3.0
 * @uses $wp_object_cache Object Cache Class
 * @see WP_Object_Cache::incr()
 *
 * @param int|string $key The cache key to increment
 * @param int $offset The amount by which to increment the item's value.  Default is 1.
 * @param string $group The group the key is in.
 * @return false|int False on failure, the item's new value on success.
 */
function wp_cache_incr( $key, $offset = 1, $group = '' ) {
	global $wp_object_cache;

	return $wp_object_cache->incr( $key, $offset, $group );
}

/**
 * Sets up Object Cache Global and assigns it.
 *
 * @since 2.0.0
 * @global WP_Object_Cache $wp_object_cache WordPress Object Cache
 */
function wp_cache_init() {
	$GLOBALS['wp_object_cache'] = new WP_Object_Cache();
}

/**
 * Replaces the contents of the cache with new data.
 *
 * @since 2.0.0
 * @uses $wp_object_cache Object Cache Class
 * @see WP_Object_Cache::replace()
 *
 * @param int|string $key What to call the contents in the cache
 * @param mixed $data The contents to store in the cache
 * @param string $group Where to group the cache contents
 * @param int $expire When to expire the cache contents
 * @return bool False if cache key and group already exist, true on success
 */
function wp_cache_replace($key, $data, $group = '', $expire = 0) {
	global $wp_object_cache;

	return $wp_object_cache->replace($key, $data, $group, $expire);
}

/**
 * Saves the data to the cache.
 *
 * @since 2.0
 * @uses $wp_object_cache Object Cache Class
 * @see WP_Object_Cache::set()
 *
 * @param int|string $key What to call the contents in the cache
 * @param mixed $data The contents to store in the cache
 * @param string $group Where to group the cache contents
 * @param int $expire When to expire the cache contents
 * @return bool False if cache key and group already exist, true on success
 */
function wp_cache_set($key, $data, $group = '', $expire = 0) {
	global $wp_object_cache;

	return $wp_object_cache->set($key, $data, $group, $expire);
}

/**
 * Adds a group or set of groups to the list of global groups.
 *
 * @since 2.6.0
 *
 * @param string|array $groups A group or an array of groups to add
 */
function wp_cache_add_global_groups( $groups ) {
	global $wp_object_cache;

	return $wp_object_cache->add_global_groups($groups);
}

/**
 * Adds a group or set of groups to the list of non-persistent groups.
 *
 * @since 2.6.0
 *
 * @param string|array $groups A group or an array of groups to add
 */
function wp_cache_add_non_persistent_groups( $groups ) {
	// Default cache doesn't persist so nothing to do here.
	return;
}

/**
 * Reset internal cache keys and structures.  If the cache backend uses global blog or site IDs as part of its cache keys,
 * this function instructs the backend to reset those keys and perform any cleanup since blog or site IDs have changed since cache init.
 *
 * @since 2.6.0
 */
function wp_cache_reset() {
	global $wp_object_cache;

	return $wp_object_cache->reset();
}

/**
 * WordPress Object Cache
 *
 * The WordPress Object Cache is used to save on trips to the database. The
 * Object Cache stores all of the cache data to memory and makes the cache
 * contents available by using a key, which is used to name and later retrieve
 * the cache contents.
 *
 * The Object Cache can be replaced by other caching mechanisms by placing files
 * in the wp-content folder which is looked at in wp-settings. If that file
 * exists, then this file will not be included.
 *
 * @package WordPress
 * @subpackage Cache
 * @since 2.0
 */
class WP_Object_Cache {

	/**
	 * Holds the cached objects
	 *
	 * @var array
	 * @access private
	 * @since 2.0.0
	 */
	var $cache = array ();

	/**
	 * The amount of times the cache data was already stored in the cache.
	 *
	 * @since 2.5.0
	 * @access private
	 * @var int
	 */
	var $cache_hits = 0;

	/**
	 * Amount of times the cache did not have the request in cache
	 *
	 * @var int
	 * @access public
	 * @since 2.0.0
	 */
	var $cache_misses = 0;

	/**
	 * List of global groups
	 *
	 * @var array
	 * @access protected
	 * @since 3.0.0
	 */
	var $global_groups = array();

	/**
	 * Adds data to the cache if it doesn't already exist.
	 *
	 * @uses WP_Object_Cache::get Checks to see if the cache already has data.
	 * @uses WP_Object_Cache::set Sets the data after the checking the cache
	 *		contents existence.
	 *
	 * @since 2.0.0
	 *
	 * @param int|string $key What to call the contents in the cache
	 * @param mixed $data The contents to store in the cache
	 * @param string $group Where to group the cache contents
	 * @param int $expire When to expire the cache contents
	 * @return bool False if cache key and group already exist, true on success
	 */
	function add( $key, $data, $group = 'default', $expire = '' ) {
		if ( wp_suspend_cache_addition() )
			return false;

		if ( empty ($group) )
			$group = 'default';

		if (false !== $this->get($key, $group))
			return false;

		return $this->set($key, $data, $group, $expire);
	}

	/**
	 * Sets the list of global groups.
	 *
	 * @since 3.0.0
	 *
	 * @param array $groups List of groups that are global.
	 */
	function add_global_groups( $groups ) {
		$groups = (array) $groups;

		$this->global_groups = array_merge($this->global_groups, $groups);
		$this->global_groups = array_unique($this->global_groups);
	}

	/**
	 * Decrement numeric cache item's value
	 *
	 * @since 3.3.0
	 *
	 * @param int|string $key The cache key to increment
	 * @param int $offset The amount by which to decrement the item's value.  Default is 1.
	 * @param string $group The group the key is in.
	 * @return false|int False on failure, the item's new value on success.
	 */
	function decr( $key, $offset = 1, $group = 'default' ) {
		if ( ! isset( $this->cache[ $group ][ $key ] ) )
			return false;

		if ( ! is_numeric( $this->cache[ $group ][ $key ] ) )
			$this->cache[ $group ][ $key ] = 0;

		$offset = (int) $offset;

		$this->cache[ $group ][ $key ] -= $offset;

		if ( $this->cache[ $group ][ $key ] < 0 )
			$this->cache[ $group ][ $key ] = 0;

		return $this->cache[ $group ][ $key ];
	}

	/**
	 * Remove the contents of the cache key in the group
	 *
	 * If the cache key does not exist in the group and $force parameter is set
	 * to false, then nothing will happen. The $force parameter is set to false
	 * by default.
	 *
	 * @since 2.0.0
	 *
	 * @param int|string $key What the contents in the cache are called
	 * @param string $group Where the cache contents are grouped
	 * @param bool $force Optional. Whether to force the unsetting of the cache
	 *		key in the group
	 * @return bool False if the contents weren't deleted and true on success
	 */
	function delete($key, $group = 'default', $force = false) {
		if (empty ($group))
			$group = 'default';

		if (!$force && false === $this->get($key, $group))
			return false;

		unset ($this->cache[$group][$key]);
		return true;
	}

	/**
	 * Clears the object cache of all data
	 *
	 * @since 2.0.0
	 *
	 * @return bool Always returns true
	 */
	function flush() {
		$this->cache = array ();

		return true;
	}

	/**
	 * Retrieves the cache contents, if it exists
	 *
	 * The contents will be first attempted to be retrieved by searching by the
	 * key in the cache group. If the cache is hit (success) then the contents
	 * are returned.
	 *
	 * On failure, the number of cache misses will be incremented.
	 *
	 * @since 2.0.0
	 *
	 * @param int|string $key What the contents in the cache are called
	 * @param string $group Where the cache contents are grouped
	 * @param string $force Whether to force a refetch rather than relying on the local cache (default is false)
	 * @return bool|mixed False on failure to retrieve contents or the cache
	 *		contents on success
	 */
	function get( $key, $group = 'default', $force = false) {
		if ( empty ($group) )
			$group = 'default';

		if ( isset ($this->cache[$group][$key]) ) {
			$this->cache_hits += 1;
			if ( is_object($this->cache[$group][$key]) )
				return clone $this->cache[$group][$key];
			else
				return $this->cache[$group][$key];
		}

		$this->cache_misses += 1;
		return false;
	}

	/**
	 * Increment numeric cache item's value
	 *
	 * @since 3.3.0
	 *
	 * @param int|string $key The cache key to increment
	 * @param int $offset The amount by which to increment the item's value.  Default is 1.
	 * @param string $group The group the key is in.
	 * @return false|int False on failure, the item's new value on success.
	 */
	function incr( $key, $offset = 1, $group = 'default' ) {
		if ( ! isset( $this->cache[ $group ][ $key ] ) )
			return false;

		if ( ! is_numeric( $this->cache[ $group ][ $key ] ) )
			$this->cache[ $group ][ $key ] = 0;

		$offset = (int) $offset;

		$this->cache[ $group ][ $key ] += $offset;

		if ( $this->cache[ $group ][ $key ] < 0 )
			$this->cache[ $group ][ $key ] = 0;

		return $this->cache[ $group ][ $key ];
	}

	/**
	 * Replace the contents in the cache, if contents already exist
	 *
	 * @since 2.0.0
	 * @see WP_Object_Cache::set()
	 *
	 * @param int|string $key What to call the contents in the cache
	 * @param mixed $data The contents to store in the cache
	 * @param string $group Where to group the cache contents
	 * @param int $expire When to expire the cache contents
	 * @return bool False if not exists, true if contents were replaced
	 */
	function replace($key, $data, $group = 'default', $expire = '') {
		if (empty ($group))
			$group = 'default';

		if ( false === $this->get($key, $group) )
			return false;

		return $this->set($key, $data, $group, $expire);
	}

	/**
	 * Reset keys
	 *
	 * @since 3.0.0
	 */
	function reset() {
		// Clear out non-global caches since the blog ID has changed.
		foreach ( array_keys($this->cache) as $group ) {
			if ( !in_array($group, $this->global_groups) )
				unset($this->cache[$group]);
		}
	}

	/**
	 * Sets the data contents into the cache
	 *
	 * The cache contents is grouped by the $group parameter followed by the
	 * $key. This allows for duplicate ids in unique groups. Therefore, naming of
	 * the group should be used with care and should follow normal function
	 * naming guidelines outside of core WordPress usage.
	 *
	 * The $expire parameter is not used, because the cache will automatically
	 * expire for each time a page is accessed and PHP finishes. The method is
	 * more for cache plugins which use files.
	 *
	 * @since 2.0.0
	 *
	 * @param int|string $key What to call the contents in the cache
	 * @param mixed $data The contents to store in the cache
	 * @param string $group Where to group the cache contents
	 * @param int $expire Not Used
	 * @return bool Always returns true
	 */
	function set($key, $data, $group = 'default', $expire = '') {
		if ( empty ($group) )
			$group = 'default';

		if ( NULL === $data )
			$data = '';

		if ( is_object($data) )
			$data = clone $data;

		$this->cache[$group][$key] = $data;
		return true;
	}

	/**
	 * Echoes the stats of the caching.
	 *
	 * Gives the cache hits, and cache misses. Also prints every cached group,
	 * key and the data.
	 *
	 * @since 2.0.0
	 */
	function stats() {
		echo "<p>";
		echo "<strong>Cache Hits:</strong> {$this->cache_hits}<br />";
		echo "<strong>Cache Misses:</strong> {$this->cache_misses}<br />";
		echo "</p>";
		echo '<ul>';
		foreach ($this->cache as $group => $cache) {
			echo "<li><strong>Group:</strong> $group - ( " . number_format( strlen( serialize( $cache ) ) / 1024, 2 ) . 'k )</li>';
		}
		echo '</ul>';
	}

	/**
	 * Sets up object properties; PHP 5 style constructor
	 *
	 * @since 2.0.8
	 * @return null|WP_Object_Cache If cache is disabled, returns null.
	 */
	function __construct() {
		/**
		 * @todo This should be moved to the PHP4 style constructor, PHP5
		 * already calls __destruct()
		 */
		register_shutdown_function(array(&$this, "__destruct"));
	}

	/**
	 * Will save the object cache before object is completely destroyed.
	 *
	 * Called upon object destruction, which should be when PHP ends.
	 *
	 * @since  2.0.8
	 *
	 * @return bool True value. Won't be used by PHP
	 */
	function __destruct() {
		return true;
	}
}
?>
