<?php
/**
 * WP AI Client: WP_AI_Client_Cache class
 *
 * @package WordPress
 * @subpackage AI
 * @since 7.0.0
 */

use WordPress\AiClientDependencies\Psr\SimpleCache\CacheInterface;

/**
 * WordPress-specific PSR-16 cache adapter for the AI Client.
 *
 * Bridges PSR-16 cache operations to WordPress object cache functions,
 * enabling the AI client to leverage WordPress caching infrastructure.
 *
 * @since 7.0.0
 * @internal Intended only to wire up the PHP AI Client SDK to WordPress's caching system.
 * @access private
 */
class WP_AI_Client_Cache implements CacheInterface {

	/**
	 * Cache group used for all cache operations.
	 *
	 * @since 7.0.0
	 * @var string
	 */
	private const CACHE_GROUP = 'wp_ai_client';

	/**
	 * Fetches a value from the cache.
	 *
	 * @since 7.0.0
	 *
	 * @param string $key           The unique key of this item in the cache.
	 * @param mixed  $default_value Default value to return if the key does not exist.
	 * @return mixed The value of the item from the cache, or $default_value in case of cache miss.
	 */
	public function get( $key, $default_value = null ) {
		$found = false;
		$value = wp_cache_get( $key, self::CACHE_GROUP, false, $found );

		if ( ! $found ) {
			return $default_value;
		}

		return $value;
	}

	/**
	 * Persists data in the cache, uniquely referenced by a key with an optional expiration TTL time.
	 *
	 * @since 7.0.0
	 *
	 * @param string                $key   The key of the item to store.
	 * @param mixed                 $value The value of the item to store, must be serializable.
	 * @param null|int|DateInterval $ttl   Optional. The TTL value of this item.
	 * @return bool True on success and false on failure.
	 */
	public function set( $key, $value, $ttl = null ): bool {
		$expire = $this->ttl_to_seconds( $ttl );

		return wp_cache_set( $key, $value, self::CACHE_GROUP, $expire );
	}

	/**
	 * Delete an item from the cache by its unique key.
	 *
	 * @since 7.0.0
	 *
	 * @param string $key The unique cache key of the item to delete.
	 * @return bool True if the item was successfully removed. False if there was an error.
	 */
	public function delete( $key ): bool {
		return wp_cache_delete( $key, self::CACHE_GROUP );
	}

	/**
	 * Wipes clean the entire cache's keys.
	 *
	 * This method only clears the cache group used by this adapter. If the underlying
	 * cache implementation does not support group flushing, this method returns false.
	 *
	 * @since 7.0.0
	 *
	 * @return bool True on success and false on failure.
	 */
	public function clear(): bool {
		if ( ! function_exists( 'wp_cache_supports' ) || ! wp_cache_supports( 'flush_group' ) ) {
			return false;
		}

		return wp_cache_flush_group( self::CACHE_GROUP );
	}

	/**
	 * Obtains multiple cache items by their unique keys.
	 *
	 * @since 7.0.0
	 *
	 * @param iterable<string> $keys          A list of keys that can be obtained in a single operation.
	 * @param mixed            $default_value Default value to return for keys that do not exist.
	 * @return array<string, mixed> A list of key => value pairs.
	 */
	public function getMultiple( $keys, $default_value = null ) {
		/**
		 * Keys array.
		 *
		 * @var array<string> $keys_array
		 */
		$keys_array = $this->iterable_to_array( $keys );
		$values     = wp_cache_get_multiple( $keys_array, self::CACHE_GROUP );
		$result     = array();

		foreach ( $keys_array as $key ) {
			if ( false === $values[ $key ] ) {
				// Could be a stored false or a cache miss — disambiguate via get().
				$result[ $key ] = $this->get( $key, $default_value );
			} else {
				$result[ $key ] = $values[ $key ];
			}
		}

		return $result;
	}

	/**
	 * Persists a set of key => value pairs in the cache, with an optional TTL.
	 *
	 * @since 7.0.0
	 *
	 * @param iterable<string, mixed> $values A list of key => value pairs for a multiple-set operation.
	 * @param null|int|DateInterval   $ttl    Optional. The TTL value of this item.
	 * @return bool True on success and false on failure.
	 */
	public function setMultiple( $values, $ttl = null ): bool {
		$values_array = $this->iterable_to_array( $values );
		$expire       = $this->ttl_to_seconds( $ttl );
		$results      = wp_cache_set_multiple( $values_array, self::CACHE_GROUP, $expire );

		// Return true only if all operations succeeded.
		return ! in_array( false, $results, true );
	}

	/**
	 * Deletes multiple cache items in a single operation.
	 *
	 * @since 7.0.0
	 *
	 * @param iterable<string> $keys A list of string-based keys to be deleted.
	 * @return bool True if the items were successfully removed. False if there was an error.
	 */
	public function deleteMultiple( $keys ): bool {
		$keys_array = $this->iterable_to_array( $keys );
		$results    = wp_cache_delete_multiple( $keys_array, self::CACHE_GROUP );

		// Return true only if all operations succeeded.
		return ! in_array( false, $results, true );
	}

	/**
	 * Determines whether an item is present in the cache.
	 *
	 * @since 7.0.0
	 *
	 * @param string $key The cache item key.
	 * @return bool True if the item exists in the cache, false otherwise.
	 */
	public function has( $key ): bool {
		$found = false;
		wp_cache_get( $key, self::CACHE_GROUP, false, $found );

		return (bool) $found;
	}

	/**
	 * Converts a PSR-16 TTL value to seconds for WordPress cache functions.
	 *
	 * @since 7.0.0
	 *
	 * @param null|int|DateInterval $ttl The TTL value.
	 * @return int The TTL in seconds, or 0 for no expiration.
	 */
	private function ttl_to_seconds( $ttl ): int {
		if ( null === $ttl ) {
			return 0;
		}

		if ( $ttl instanceof DateInterval ) {
			$now = new DateTime();
			$end = ( clone $now )->add( $ttl );

			return $end->getTimestamp() - $now->getTimestamp();
		}

		return max( 0, (int) $ttl );
	}

	/**
	 * Converts an iterable to an array.
	 *
	 * @since 7.0.0
	 *
	 * @param iterable<mixed> $items The iterable to convert.
	 * @return array<mixed> The array.
	 */
	private function iterable_to_array( $items ): array {
		if ( is_array( $items ) ) {
			return $items;
		}

		return iterator_to_array( $items );
	}
}
