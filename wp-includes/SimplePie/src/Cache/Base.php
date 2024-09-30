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

/**
 * Base for cache objects
 *
 * Classes to be used with {@see \SimplePie\Cache::register()} are expected
 * to implement this interface.
 *
 * @package SimplePie
 * @subpackage Caching
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
    public function __construct($location, $name, $type);

    /**
     * Save data to the cache
     *
     * @param array|\SimplePie\SimplePie $data Data to store in the cache. If passed a SimplePie object, only cache the $data property
     * @return bool Successfulness
     */
    public function save($data);

    /**
     * Retrieve the data saved to the cache
     *
     * @return array Data for SimplePie::$data
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
