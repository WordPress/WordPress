<?php

// SPDX-FileCopyrightText: 2004-2023 Ryan Parman, Sam Sneddon, Ryan McCue
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

namespace SimplePie;

use SimplePie\Cache\Base;

/**
 * Used to create cache objects
 *
 * This class can be overloaded with {@see SimplePie::set_cache_class()},
 * although the preferred way is to create your own handler
 * via {@see register()}
 *
 * @deprecated since SimplePie 1.8.0, use "SimplePie\SimplePie::set_cache()" instead
 */
class Cache
{
    /**
     * Cache handler classes
     *
     * These receive 3 parameters to their constructor, as documented in
     * {@see register()}
     * @var array<string, class-string<Base>>
     */
    protected static $handlers = [
        'mysql'     => Cache\MySQL::class,
        'memcache'  => Cache\Memcache::class,
        'memcached' => Cache\Memcached::class,
        'redis'     => Cache\Redis::class,
    ];

    /**
     * Don't call the constructor. Please.
     */
    private function __construct()
    {
    }

    /**
     * Create a new SimplePie\Cache object
     *
     * @param string $location URL location (scheme is used to determine handler)
     * @param string $filename Unique identifier for cache object
     * @param Base::TYPE_FEED|Base::TYPE_IMAGE $extension 'spi' or 'spc'
     * @return Base Type of object depends on scheme of `$location`
     */
    public static function get_handler(string $location, string $filename, $extension)
    {
        $type = explode(':', $location, 2);
        $type = $type[0];
        if (!empty(self::$handlers[$type])) {
            $class = self::$handlers[$type];
            return new $class($location, $filename, $extension);
        }

        return new \SimplePie\Cache\File($location, $filename, $extension);
    }

    /**
     * Create a new SimplePie\Cache object
     *
     * @deprecated since SimplePie 1.3.1, use {@see get_handler()} instead
     * @param string $location
     * @param string $filename
     * @param Base::TYPE_FEED|Base::TYPE_IMAGE $extension
     * @return Base
     */
    public function create(string $location, string $filename, $extension)
    {
        trigger_error('Cache::create() has been replaced with Cache::get_handler() since SimplePie 1.3.1, use the registry system instead.', \E_USER_DEPRECATED);

        return self::get_handler($location, $filename, $extension);
    }

    /**
     * Register a handler
     *
     * @param string $type DSN type to register for
     * @param class-string<Base> $class Name of handler class. Must implement Base
     * @return void
     */
    public static function register(string $type, $class)
    {
        self::$handlers[$type] = $class;
    }

    /**
     * Parse a URL into an array
     *
     * @param string $url
     * @return array<string, mixed>
     */
    public static function parse_URL(string $url)
    {
        $parsedUrl = parse_url($url);

        if ($parsedUrl === false) {
            return [];
        }

        $params = array_merge($parsedUrl, ['extras' => []]);
        if (isset($params['query'])) {
            parse_str($params['query'], $params['extras']);
        }
        return $params;
    }
}

class_alias('SimplePie\Cache', 'SimplePie_Cache');
