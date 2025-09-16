<?php

// SPDX-FileCopyrightText: 2004-2023 Ryan Parman, Sam Sneddon, Ryan McCue
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

namespace SimplePie\Cache;

use InvalidArgumentException;

/**
 * Adapter for deprecated \SimplePie\Cache\Base implementations
 *
 * @internal
 */
final class BaseDataCache implements DataCache
{
    /**
     * @var Base
     */
    private $cache;

    public function __construct(Base $cache)
    {
        $this->cache = $cache;
    }

    /**
     * Fetches a value from the cache.
     *
     * Equivalent to \Psr\SimpleCache\CacheInterface::get()
     * <code>
     * public function get(string $key, mixed $default = null): mixed;
     * </code>
     *
     * @param string $key     The unique key of this item in the cache.
     * @param mixed  $default Default value to return if the key does not exist.
     *
     * @return array|mixed The value of the item from the cache, or $default in case of cache miss.
     *
     * @throws InvalidArgumentException
     *   MUST be thrown if the $key string is not a legal value.
     */
    public function get_data(string $key, $default = null)
    {
        $data = $this->cache->load();

        if (!is_array($data)) {
            return $default;
        }

        // ignore data if internal cache expiration time is not set
        if (!array_key_exists('__cache_expiration_time', $data)) {
            return $default;
        }

        // ignore data if internal cache expiration time is expired
        if ($data['__cache_expiration_time'] < time()) {
            return $default;
        }

        // remove internal cache expiration time
        unset($data['__cache_expiration_time']);

        return $data;
    }

    /**
     * Persists data in the cache, uniquely referenced by a key with an optional expiration TTL time.
     *
     * Equivalent to \Psr\SimpleCache\CacheInterface::set()
     * <code>
     * public function set(string $key, mixed $value, null|int|\DateInterval $ttl = null): bool;
     * </code>
     *
     * @param string   $key   The key of the item to store.
     * @param array<mixed> $value The value of the item to store, must be serializable.
     * @param null|int $ttl   Optional. The TTL value of this item. If no value is sent and
     *                                      the driver supports TTL then the library may set a default value
     *                                      for it or let the driver take care of that.
     *
     * @return bool True on success and false on failure.
     *
     * @throws InvalidArgumentException
     *   MUST be thrown if the $key string is not a legal value.
     */
    public function set_data(string $key, array $value, ?int $ttl = null): bool
    {
        if ($ttl === null) {
            $ttl = 3600;
        }

        // place internal cache expiration time
        $value['__cache_expiration_time'] = time() + $ttl;

        return $this->cache->save($value);
    }

    /**
     * Delete an item from the cache by its unique key.
     *
     * Equivalent to \Psr\SimpleCache\CacheInterface::delete()
     * <code>
     * public function delete(string $key): bool;
     * </code>
     *
     * @param string $key The unique cache key of the item to delete.
     *
     * @return bool True if the item was successfully removed. False if there was an error.
     *
     * @throws InvalidArgumentException
     *   MUST be thrown if the $key string is not a legal value.
     */
    public function delete_data(string $key): bool
    {
        return $this->cache->unlink();
    }
}
