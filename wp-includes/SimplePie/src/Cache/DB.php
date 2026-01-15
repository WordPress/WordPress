<?php

// SPDX-FileCopyrightText: 2004-2023 Ryan Parman, Sam Sneddon, Ryan McCue
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

namespace SimplePie\Cache;

use SimplePie\Item;

/**
 * Base class for database-based caches
 *
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
     * @return array{string, array<string, Item>} First item is the serialized data for storage, second item is the unique ID for this item
     */
    protected static function prepare_simplepie_object_for_cache(\SimplePie\SimplePie $data)
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
