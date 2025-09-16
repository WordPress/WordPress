<?php

// SPDX-FileCopyrightText: 2004-2023 Ryan Parman, Sam Sneddon, Ryan McCue
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

namespace SimplePie\Cache;

use Memcache as NativeMemcache;

/**
 * Caches data to memcache
 *
 * Registered for URLs with the "memcache" protocol
 *
 * For example, `memcache://localhost:11211/?timeout=3600&prefix=sp_` will
 * connect to memcache on `localhost` on port 11211. All tables will be
 * prefixed with `sp_` and data will expire after 3600 seconds
 *
 * @uses Memcache
 * @deprecated since SimplePie 1.8.0, use implementation of "Psr\SimpleCache\CacheInterface" instead
 */
class Memcache implements Base
{
    /**
     * Memcache instance
     *
     * @var NativeMemcache
     */
    protected $cache;

    /**
     * Options
     *
     * @var array<string, mixed>
     */
    protected $options;

    /**
     * Cache name
     *
     * @var string
     */
    protected $name;

    /**
     * Create a new cache object
     *
     * @param string $location Location string (from SimplePie::$cache_location)
     * @param string $name Unique ID for the cache
     * @param Base::TYPE_FEED|Base::TYPE_IMAGE $type Either TYPE_FEED for SimplePie data, or TYPE_IMAGE for image data
     */
    public function __construct(string $location, string $name, $type)
    {
        $this->options = [
            'host' => '127.0.0.1',
            'port' => 11211,
            'extras' => [
                'timeout' => 3600, // one hour
                'prefix' => 'simplepie_',
            ],
        ];
        $this->options = array_replace_recursive($this->options, \SimplePie\Cache::parse_URL($location));

        $this->name = $this->options['extras']['prefix'] . md5("$name:$type");

        $this->cache = new NativeMemcache();
        $this->cache->addServer($this->options['host'], (int) $this->options['port']);
    }

    /**
     * Save data to the cache
     *
     * @param array<mixed>|\SimplePie\SimplePie $data Data to store in the cache. If passed a SimplePie object, only cache the $data property
     * @return bool Successfulness
     */
    public function save($data)
    {
        if ($data instanceof \SimplePie\SimplePie) {
            $data = $data->data;
        }
        return $this->cache->set($this->name, serialize($data), MEMCACHE_COMPRESSED, (int) $this->options['extras']['timeout']);
    }

    /**
     * Retrieve the data saved to the cache
     *
     * @return array<mixed>|false Data for SimplePie::$data
     */
    public function load()
    {
        $data = $this->cache->get($this->name);

        if ($data !== false) {
            return unserialize($data);
        }
        return false;
    }

    /**
     * Retrieve the last modified time for the cache
     *
     * @return int|false Timestamp
     */
    public function mtime()
    {
        $data = $this->cache->get($this->name);

        if ($data !== false) {
            // essentially ignore the mtime because Memcache expires on its own
            return time();
        }

        return false;
    }

    /**
     * Set the last modified time to the current time
     *
     * @return bool Success status
     */
    public function touch()
    {
        $data = $this->cache->get($this->name);

        if ($data !== false) {
            return $this->cache->set($this->name, $data, MEMCACHE_COMPRESSED, (int) $this->options['extras']['timeout']);
        }

        return false;
    }

    /**
     * Remove the cache
     *
     * @return bool Success status
     */
    public function unlink()
    {
        return $this->cache->delete($this->name, 0);
    }
}

class_alias('SimplePie\Cache\Memcache', 'SimplePie_Cache_Memcache');
