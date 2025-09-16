<?php

// SPDX-FileCopyrightText: 2004-2023 Ryan Parman, Sam Sneddon, Ryan McCue
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

namespace SimplePie\Cache;

/**
 * Base for cache objects
 *
 * Classes to be used with {@see \SimplePie\Cache::register()} are expected
 * to implement this interface.
 *
 * @deprecated since SimplePie 1.8.0, use "Psr\SimpleCache\CacheInterface" instead
 */
interface Base
{
    /**
     * Feed cache type
     *
     * @var string
     */
    public const TYPE_FEED = 'spc';

    /**
     * Image cache type
     *
     * @var string
     */
    public const TYPE_IMAGE = 'spi';

    /**
     * Create a new cache object
     *
     * @param string $location Location string (from SimplePie::$cache_location)
     * @param string $name Unique ID for the cache
     * @param Base::TYPE_FEED|Base::TYPE_IMAGE $type Either TYPE_FEED for SimplePie data, or TYPE_IMAGE for image data
     */
    public function __construct(string $location, string $name, $type);

    /**
     * Save data to the cache
     *
     * @param array<mixed>|\SimplePie\SimplePie $data Data to store in the cache. If passed a SimplePie object, only cache the $data property
     * @return bool Successfulness
     */
    public function save($data);

    /**
     * Retrieve the data saved to the cache
     *
     * @return array<mixed> Data for SimplePie::$data
     */
    public function load();

    /**
     * Retrieve the last modified time for the cache
     *
     * @return int Timestamp
     */
    public function mtime();

    /**
     * Set the last modified time to the current time
     *
     * @return bool Success status
     */
    public function touch();

    /**
     * Remove the cache
     *
     * @return bool Success status
     */
    public function unlink();
}

class_alias('SimplePie\Cache\Base', 'SimplePie_Cache_Base');
