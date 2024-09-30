<?php

/**
 * SimplePie
 *
 * A PHP-Based RSS and Atom Feed Framework.
 * Takes the hard work out of managing a complete RSS/Atom solution.
 *
 * Copyright (c) 2004-2022, Ryan Parman, Sam Sneddon, Ryan McCue, and contributors
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without modification, are
 * permitted provided that the following conditions are met:
 *
 * 	* Redistributions of source code must retain the above copyright notice, this list of
 * 	  conditions and the following disclaimer.
 *
 * 	* Redistributions in binary form must reproduce the above copyright notice, this list
 * 	  of conditions and the following disclaimer in the documentation and/or other materials
 * 	  provided with the distribution.
 *
 * 	* Neither the name of the SimplePie Team nor the names of its contributors may be used
 * 	  to endorse or promote products derived from this software without specific prior
 * 	  written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS
 * OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY
 * AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDERS
 * AND CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
 * SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR
 * OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @package SimplePie
 * @copyright 2004-2022 Ryan Parman, Sam Sneddon, Ryan McCue
 * @author Ryan Parman
 * @author Sam Sneddon
 * @author Ryan McCue
 * @link http://simplepie.org/ SimplePie
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */

namespace SimplePie\Cache;

use Psr\SimpleCache\CacheInterface;
use Psr\SimpleCache\InvalidArgumentException;

/**
 * Caches data into a PSR-16 cache implementation
 *
 * @package SimplePie
 * @subpackage Caching
 * @internal
 */
final class Psr16 implements DataCache
{
    /**
     * PSR-16 cache implementation
     *
     * @var CacheInterface
     */
    private $cache;

    /**
     * PSR-16 cache implementation
     *
     * @param CacheInterface $cache
     */
    public function __construct(CacheInterface $cache)
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
        $data = $this->cache->get($key, $default);

        if (!is_array($data) || $data === $default) {
            return $default;
        }

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
     * @param array    $value The value of the item to store, must be serializable.
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
        return $this->cache->set($key, $value, $ttl);
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
        return $this->cache->delete($key);
    }
}
