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
 * Base class for database-based caches
 *
 * @package SimplePie
 * @subpackage Caching
 * @deprecated since SimplePie 1.8.0, use implementation of "Psr\SimpleCache\CacheInterface" instead
 */
abstract class DB implements Base
{
    /**
     * Helper for database conversion
     *
     * Converts a given {@see SimplePie} object into data to be stored
     *
     * @param \SimplePie\SimplePie $data
     * @return array First item is the serialized data for storage, second item is the unique ID for this item
     */
    protected static function prepare_simplepie_object_for_cache($data)
    {
        $items = $data->get_items();
        $items_by_id = [];

        if (!empty($items)) {
            foreach ($items as $item) {
                $items_by_id[$item->get_id()] = $item;
            }

            if (count($items_by_id) !== count($items)) {
                $items_by_id = [];
                foreach ($items as $item) {
                    $items_by_id[$item->get_id(true)] = $item;
                }
            }

            if (isset($data->data['child'][\SimplePie\SimplePie::NAMESPACE_ATOM_10]['feed'][0])) {
                $channel = &$data->data['child'][\SimplePie\SimplePie::NAMESPACE_ATOM_10]['feed'][0];
            } elseif (isset($data->data['child'][\SimplePie\SimplePie::NAMESPACE_ATOM_03]['feed'][0])) {
                $channel = &$data->data['child'][\SimplePie\SimplePie::NAMESPACE_ATOM_03]['feed'][0];
            } elseif (isset($data->data['child'][\SimplePie\SimplePie::NAMESPACE_RDF]['RDF'][0])) {
                $channel = &$data->data['child'][\SimplePie\SimplePie::NAMESPACE_RDF]['RDF'][0];
            } elseif (isset($data->data['child'][\SimplePie\SimplePie::NAMESPACE_RSS_20]['rss'][0]['child'][\SimplePie\SimplePie::NAMESPACE_RSS_20]['channel'][0])) {
                $channel = &$data->data['child'][\SimplePie\SimplePie::NAMESPACE_RSS_20]['rss'][0]['child'][\SimplePie\SimplePie::NAMESPACE_RSS_20]['channel'][0];
            } else {
                $channel = null;
            }

            if ($channel !== null) {
                if (isset($channel['child'][\SimplePie\SimplePie::NAMESPACE_ATOM_10]['entry'])) {
                    unset($channel['child'][\SimplePie\SimplePie::NAMESPACE_ATOM_10]['entry']);
                }
                if (isset($channel['child'][\SimplePie\SimplePie::NAMESPACE_ATOM_03]['entry'])) {
                    unset($channel['child'][\SimplePie\SimplePie::NAMESPACE_ATOM_03]['entry']);
                }
                if (isset($channel['child'][\SimplePie\SimplePie::NAMESPACE_RSS_10]['item'])) {
                    unset($channel['child'][\SimplePie\SimplePie::NAMESPACE_RSS_10]['item']);
                }
                if (isset($channel['child'][\SimplePie\SimplePie::NAMESPACE_RSS_090]['item'])) {
                    unset($channel['child'][\SimplePie\SimplePie::NAMESPACE_RSS_090]['item']);
                }
                if (isset($channel['child'][\SimplePie\SimplePie::NAMESPACE_RSS_20]['item'])) {
                    unset($channel['child'][\SimplePie\SimplePie::NAMESPACE_RSS_20]['item']);
                }
            }
            if (isset($data->data['items'])) {
                unset($data->data['items']);
            }
            if (isset($data->data['ordered_items'])) {
                unset($data->data['ordered_items']);
            }
        }
        return [serialize($data->data), $items_by_id];
    }
}

class_alias('SimplePie\Cache\DB', 'SimplePie_Cache_DB');
