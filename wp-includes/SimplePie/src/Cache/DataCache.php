<?php

// SPDX-FileCopyrightText: 2004-2023 Ryan Parman, Sam Sneddon, Ryan McCue
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

namespace SimplePie\Cache;

use InvalidArgumentException;

/**
 * Subset of PSR-16 Cache client for caching data arrays
 *
 * Only get(), set() and delete() methods are used,
 * but not has(), getMultiple(), setMultiple() or deleteMultiple().
 *
 * The methods names must be different, but should be compatible to the
 * methods of \Psr\SimpleCache\CacheInterface.
 *
 * @internal
 */
interface DataCache
{
    /**
     * Fetches a value from the cache.
     *
     * Equivalent to \Psr\SimpleCache\CacheInterface::get()
     * <code>
     * public function get(string $key, mixed $default = null): mixed;
     * </code>
     *
     * @param string   $key     The unique key of this item in the cache.
     * @param mixed    $default Default value to return if the key does not exist.
     *
     * @return array|mixed The value of the item from the cache, or $default in case of cache miss.
     *
     * @throws InvalidArgumentException
     *   MUST be thrown if the $key string is not a legal value.
     */
    public function get_data(string $key, $default = null);

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
    public function set_data(string $key, array $value, ?int $ttl = null): bool;

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
    public function delete_data(string $key): bool;
}
