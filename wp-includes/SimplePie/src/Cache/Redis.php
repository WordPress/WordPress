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
 * @copyright 2004-2016 Ryan Parman, Sam Sneddon, Ryan McCue
 * @author Ryan Parman
 * @author Sam Sneddon
 * @author Ryan McCue
 * @link http://simplepie.org/ SimplePie
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */

namespace SimplePie\Cache;

use Redis as NativeRedis;

/**
 * Caches data to redis
 *
 * Registered for URLs with the "redis" protocol
 *
 * For example, `redis://localhost:6379/?timeout=3600&prefix=sp_&dbIndex=0` will
 * connect to redis on `localhost` on port 6379. All tables will be
 * prefixed with `simple_primary-` and data will expire after 3600 seconds
 *
 * @package SimplePie
 * @subpackage Caching
 * @uses Redis
 * @deprecated since SimplePie 1.8.0, use implementation of "Psr\SimpleCache\CacheInterface" instead
 */
class Redis implements Base
{
    /**
     * Redis instance
     *
     * @var NativeRedis
     */
    protected $cache;

    /**
     * Options
     *
     * @var array
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
    public function __construct($location, $name, $options = null)
    {
        //$this->cache = \flow\simple\cache\Redis::getRedisClientInstance();
        $parsed = \SimplePie\Cache::parse_URL($location);
        $redis = new NativeRedis();
        $redis->connect($parsed['host'], $parsed['port']);
        if (isset($parsed['pass'])) {
            $redis->auth($parsed['pass']);
        }
        if (isset($parsed['path'])) {
            $redis->select((int)substr($parsed['path'], 1));
        }
        $this->cache = $redis;

        if (!is_null($options) && is_array($options)) {
            $this->options = $options;
        } else {
            $this->options = [
                'prefix' => 'rss:simple_primary:',
                'expire' => 0,
            ];
        }

        $this->name = $this->options['prefix'] . $name;
    }

    /**
     * @param NativeRedis $cache
     */
    public function setRedisClient(NativeRedis $cache)
    {
        $this->cache = $cache;
    }

    /**
     * Save data to the cache
     *
     * @param array|\SimplePie\SimplePie $data Data to store in the cache. If passed a SimplePie object, only cache the $data property
     * @return bool Successfulness
     */
    public function save($data)
    {
        if ($data instanceof \SimplePie\SimplePie) {
            $data = $data->data;
        }
        $response = $this->cache->set($this->name, serialize($data));
        if ($this->options['expire']) {
            $this->cache->expire($this->name, $this->options['expire']);
        }

        return $response;
    }

    /**
     * Retrieve the data saved to the cache
     *
     * @return array Data for SimplePie::$data
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
     * @return int Timestamp
     */
    public function mtime()
    {
        $data = $this->cache->get($this->name);

        if ($data !== false) {
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
            $return = $this->cache->set($this->name, $data);
            if ($this->options['expire']) {
                return $this->cache->expire($this->name, $this->options['expire']);
            }
            return $return;
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
        return $this->cache->set($this->name, null);
    }
}

class_alias('SimplePie\Cache\Redis', 'SimplePie_Cache_Redis');
