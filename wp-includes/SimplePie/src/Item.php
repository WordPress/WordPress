<?php

// SPDX-FileCopyrightText: 2004-2023 Ryan Parman, Sam Sneddon, Ryan McCue
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

namespace SimplePie;

/**
 * Manages all item-related data
 *
 * Used by {@see \SimplePie\SimplePie::get_item()} and {@see \SimplePie\SimplePie::get_items()}
 *
 * This class can be overloaded with {@see \SimplePie\SimplePie::set_item_class()}
 */
class Item implements RegistryAware
{
    /**
     * Parent feed
     *
     * @access private
     * @var \SimplePie\SimplePie
     */
    public $feed;

    /**
     * Raw data
     *
     * @access private
     * @var array<string, mixed>
     */
    public $data = [];

    /**
     * Registry object
     *
     * @see set_registry
     * @var \SimplePie\Registry
     */
    protected $registry;

    /**
     * @var Sanitize|null
     */
    private $sanitize = null;

    /**
     * Create a new item object
     *
     * This is usually used by {@see \SimplePie\SimplePie::get_items} and
     * {@see \SimplePie\SimplePie::get_item}. Avoid creating this manually.
     *
     * @param \SimplePie\SimplePie $feed Parent feed
     * @param array<string, mixed> $data Raw data
     */
    public function __construct(\SimplePie\SimplePie $feed, array $data)
    {
        $this->feed = $feed;
        $this->data = $data;
    }

    /**
     * Set the registry handler
     *
     * This is usually used by {@see \SimplePie\Registry::create}
     *
     * @since 1.3
     * @param \SimplePie\Registry $registry
     * @return void
     */
    public function set_registry(\SimplePie\Registry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * Get a string representation of the item
     *
     * @return string
     */
    public function __toString()
    {
        return md5(serialize($this->data));
    }

    /**
     * Remove items that link back to this before destroying this object
     */
    public function __destruct()
    {
        if (!gc_enabled()) {
            unset($this->feed);
        }
    }

    /**
     * Get data for an item-level element
     *
     * This method allows you to get access to ANY element/attribute that is a
     * sub-element of the item/entry tag.
     *
     * See {@see \SimplePie\SimplePie::get_feed_tags()} for a description of the return value
     *
     * @since 1.0
     * @see http://simplepie.org/wiki/faq/supported_xml_namespaces
     * @param string $namespace The URL of the XML namespace of the elements you're trying to access
     * @param string $tag Tag name
     * @return array<array<string, mixed>>|null
     */
    public function get_item_tags(string $namespace, string $tag)
    {
        if (isset($this->data['child'][$namespace][$tag])) {
            return $this->data['child'][$namespace][$tag];
        }

        return null;
    }

    /**
     * Get base URL of the item itself.
     * Returns `<xml:base>` or feed base URL.
     * Similar to `Item::get_base()` but can safely be used during initialisation methods
     * such as `Item::get_links()` (`Item::get_base()` and `Item::get_links()` call each-other)
     * and is not affected by enclosures.
     *
     * @param array<string, mixed> $element
     * @see get_base
     */
    private function get_own_base(array $element = []): string
    {
        if (!empty($element['xml_base_explicit']) && isset($element['xml_base'])) {
            return $element['xml_base'];
        }
        return $this->feed->get_base();
    }

    /**
     * Get the base URL value.
     * Uses `<xml:base>`, or item link, or enclosure link, or feed base URL.
     *
     * @param array<string, mixed> $element
     * @return string
     */
    public function get_base(array $element = [])
    {
        if (!empty($element['xml_base_explicit']) && isset($element['xml_base'])) {
            return $element['xml_base'];
        }
        $link = $this->get_permalink();
        if ($link != null) {
            return $link;
        }
        return $this->feed->get_base($element);
    }

    /**
     * Sanitize feed data
     *
     * @access private
     * @see \SimplePie\SimplePie::sanitize()
     * @param string $data Data to sanitize
     * @param int-mask-of<SimplePie::CONSTRUCT_*> $type
     * @param string $base Base URL to resolve URLs against
     * @return string Sanitized data
     */
    public function sanitize(string $data, int $type, string $base = '')
    {
        // This really returns string|false but changing encoding is uncommon and we are going to deprecate it, so let’s just lie to PHPStan in the interest of cleaner annotations.
        return $this->feed->sanitize($data, $type, $base);
    }

    /**
     * Get the parent feed
     *
     * Note: this may not work as you think for multifeeds!
     *
     * @link http://simplepie.org/faq/typical_multifeed_gotchas#missing_data_from_feed
     * @since 1.0
     * @return \SimplePie\SimplePie
     */
    public function get_feed()
    {
        return $this->feed;
    }

    /**
     * Get the unique identifier for the item
     *
     * This is usually used when writing code to check for new items in a feed.
     *
     * Uses `<atom:id>`, `<guid>`, `<dc:identifier>` or the `about` attribute
     * for RDF. If none of these are supplied (or `$hash` is true), creates an
     * MD5 hash based on the permalink, title and content.
     *
     * @since Beta 2
     * @param bool $hash Should we force using a hash instead of the supplied ID?
     * @param string|false $fn User-supplied function to generate an hash
     * @return string|null
     */
    public function get_id(bool $hash = false, $fn = 'md5')
    {
        if (!$hash) {
            if ($return = $this->get_item_tags(\SimplePie\SimplePie::NAMESPACE_ATOM_10, 'id')) {
                return $this->sanitize($return[0]['data'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
            } elseif ($return = $this->get_item_tags(\SimplePie\SimplePie::NAMESPACE_ATOM_03, 'id')) {
                return $this->sanitize($return[0]['data'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
            } elseif ($return = $this->get_item_tags(\SimplePie\SimplePie::NAMESPACE_RSS_20, 'guid')) {
                return $this->sanitize($return[0]['data'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
            } elseif ($return = $this->get_item_tags(\SimplePie\SimplePie::NAMESPACE_DC_11, 'identifier')) {
                return $this->sanitize($return[0]['data'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
            } elseif ($return = $this->get_item_tags(\SimplePie\SimplePie::NAMESPACE_DC_10, 'identifier')) {
                return $this->sanitize($return[0]['data'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
            } elseif (isset($this->data['attribs'][\SimplePie\SimplePie::NAMESPACE_RDF]['about'])) {
                return $this->sanitize($this->data['attribs'][\SimplePie\SimplePie::NAMESPACE_RDF]['about'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
            }
        }
        if ($fn === false) {
            return null;
        } elseif (!is_callable($fn)) {
            trigger_error('User-supplied function $fn must be callable', E_USER_WARNING);
            $fn = 'md5';
        }
        return call_user_func(
            $fn,
            $this->get_permalink().$this->get_title().$this->get_content()
        );
    }

    /**
     * Get the title of the item
     *
     * Uses `<atom:title>`, `<title>` or `<dc:title>`
     *
     * @since Beta 2 (previously called `get_item_title` since 0.8)
     * @return string|null
     */
    public function get_title()
    {
        if (!isset($this->data['title'])) {
            if ($return = $this->get_item_tags(\SimplePie\SimplePie::NAMESPACE_ATOM_10, 'title')) {
                $this->data['title'] = $this->sanitize($return[0]['data'], $this->registry->call(Misc::class, 'atom_10_construct_type', [$return[0]['attribs']]), $this->get_base($return[0]));
            } elseif ($return = $this->get_item_tags(\SimplePie\SimplePie::NAMESPACE_ATOM_03, 'title')) {
                $this->data['title'] = $this->sanitize($return[0]['data'], $this->registry->call(Misc::class, 'atom_03_construct_type', [$return[0]['attribs']]), $this->get_base($return[0]));
            } elseif ($return = $this->get_item_tags(\SimplePie\SimplePie::NAMESPACE_RSS_10, 'title')) {
                $this->data['title'] = $this->sanitize($return[0]['data'], \SimplePie\SimplePie::CONSTRUCT_MAYBE_HTML, $this->get_base($return[0]));
            } elseif ($return = $this->get_item_tags(\SimplePie\SimplePie::NAMESPACE_RSS_090, 'title')) {
                $this->data['title'] = $this->sanitize($return[0]['data'], \SimplePie\SimplePie::CONSTRUCT_MAYBE_HTML, $this->get_base($return[0]));
            } elseif ($return = $this->get_item_tags(\SimplePie\SimplePie::NAMESPACE_RSS_20, 'title')) {
                $this->data['title'] = $this->sanitize($return[0]['data'], \SimplePie\SimplePie::CONSTRUCT_MAYBE_HTML, $this->get_base($return[0]));
            } elseif ($return = $this->get_item_tags(\SimplePie\SimplePie::NAMESPACE_DC_11, 'title')) {
                $this->data['title'] = $this->sanitize($return[0]['data'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
            } elseif ($return = $this->get_item_tags(\SimplePie\SimplePie::NAMESPACE_DC_10, 'title')) {
                $this->data['title'] = $this->sanitize($return[0]['data'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
            } else {
                $this->data['title'] = null;
            }
        }
        return $this->data['title'];
    }

    /**
     * Get the content for the item
     *
     * Prefers summaries over full content , but will return full content if a
     * summary does not exist.
     *
     * To prefer full content instead, use {@see get_content}
     *
     * Uses `<atom:summary>`, `<description>`, `<dc:description>` or
     * `<itunes:subtitle>`
     *
     * @since 0.8
     * @param bool $description_only Should we avoid falling back to the content?
     * @return string|null
     */
    public function get_description(bool $description_only = false)
    {
        if (($tags = $this->get_item_tags(\SimplePie\SimplePie::NAMESPACE_ATOM_10, 'summary')) &&
            ($return = $this->sanitize($tags[0]['data'], $this->registry->call(Misc::class, 'atom_10_construct_type', [$tags[0]['attribs']]), $this->get_base($tags[0])))) {
            return $return;
        } elseif (($tags = $this->get_item_tags(\SimplePie\SimplePie::NAMESPACE_ATOM_03, 'summary')) &&
                ($return = $this->sanitize($tags[0]['data'], $this->registry->call(Misc::class, 'atom_03_construct_type', [$tags[0]['attribs']]), $this->get_base($tags[0])))) {
            return $return;
        } elseif (($tags = $this->get_item_tags(\SimplePie\SimplePie::NAMESPACE_RSS_10, 'description')) &&
                ($return = $this->sanitize($tags[0]['data'], \SimplePie\SimplePie::CONSTRUCT_MAYBE_HTML, $this->get_base($tags[0])))) {
            return $return;
        } elseif (($tags = $this->get_item_tags(\SimplePie\SimplePie::NAMESPACE_RSS_20, 'description')) &&
                ($return = $this->sanitize($tags[0]['data'], \SimplePie\SimplePie::CONSTRUCT_HTML, $this->get_base($tags[0])))) {
            return $return;
        } elseif (($tags = $this->get_item_tags(\SimplePie\SimplePie::NAMESPACE_DC_11, 'description')) &&
                ($return = $this->sanitize($tags[0]['data'], \SimplePie\SimplePie::CONSTRUCT_TEXT))) {
            return $return;
        } elseif (($tags = $this->get_item_tags(\SimplePie\SimplePie::NAMESPACE_DC_10, 'description')) &&
                ($return = $this->sanitize($tags[0]['data'], \SimplePie\SimplePie::CONSTRUCT_TEXT))) {
            return $return;
        } elseif (($tags = $this->get_item_tags(\SimplePie\SimplePie::NAMESPACE_ITUNES, 'summary')) &&
                ($return = $this->sanitize($tags[0]['data'], \SimplePie\SimplePie::CONSTRUCT_HTML, $this->get_base($tags[0])))) {
            return $return;
        } elseif (($tags = $this->get_item_tags(\SimplePie\SimplePie::NAMESPACE_ITUNES, 'subtitle')) &&
                ($return = $this->sanitize($tags[0]['data'], \SimplePie\SimplePie::CONSTRUCT_TEXT))) {
            return $return;
        } elseif (($tags = $this->get_item_tags(\SimplePie\SimplePie::NAMESPACE_RSS_090, 'description')) &&
                ($return = $this->sanitize($tags[0]['data'], \SimplePie\SimplePie::CONSTRUCT_HTML))) {
            return $return;
        } elseif (!$description_only) {
            return $this->get_content(true);
        }

        return null;
    }

    /**
     * Get the content for the item
     *
     * Prefers full content over summaries, but will return a summary if full
     * content does not exist.
     *
     * To prefer summaries instead, use {@see get_description}
     *
     * Uses `<atom:content>` or `<content:encoded>` (RSS 1.0 Content Module)
     *
     * @since 1.0
     * @param bool $content_only Should we avoid falling back to the description?
     * @return string|null
     */
    public function get_content(bool $content_only = false)
    {
        if (($tags = $this->get_item_tags(\SimplePie\SimplePie::NAMESPACE_ATOM_10, 'content')) &&
            ($return = $this->sanitize($tags[0]['data'], $this->registry->call(Misc::class, 'atom_10_content_construct_type', [$tags[0]['attribs']]), $this->get_base($tags[0])))) {
            return $return;
        } elseif (($tags = $this->get_item_tags(\SimplePie\SimplePie::NAMESPACE_ATOM_03, 'content')) &&
                ($return = $this->sanitize($tags[0]['data'], $this->registry->call(Misc::class, 'atom_03_construct_type', [$tags[0]['attribs']]), $this->get_base($tags[0])))) {
            return $return;
        } elseif (($tags = $this->get_item_tags(\SimplePie\SimplePie::NAMESPACE_RSS_10_MODULES_CONTENT, 'encoded')) &&
                ($return = $this->sanitize($tags[0]['data'], \SimplePie\SimplePie::CONSTRUCT_HTML, $this->get_base($tags[0])))) {
            return $return;
        } elseif (!$content_only) {
            return $this->get_description(true);
        }

        return null;
    }

    /**
     * Get the media:thumbnail of the item
     *
     * Uses `<media:thumbnail>`
     *
     *
     * @return array{url: string, height?: string, width?: string, time?: string}|null
     */
    public function get_thumbnail()
    {
        if (!isset($this->data['thumbnail'])) {
            if ($return = $this->get_item_tags(\SimplePie\SimplePie::NAMESPACE_MEDIARSS, 'thumbnail')) {
                $thumbnail = $return[0]['attribs'][''];
                if (empty($thumbnail['url'])) {
                    $this->data['thumbnail'] = null;
                } else {
                    $thumbnail['url'] = $this->sanitize($thumbnail['url'], \SimplePie\SimplePie::CONSTRUCT_IRI, $this->get_base($return[0]));
                    $this->data['thumbnail'] = $thumbnail;
                }
            } else {
                $this->data['thumbnail'] = null;
            }
        }
        return $this->data['thumbnail'];
    }

    /**
     * Get a category for the item
     *
     * @since Beta 3 (previously called `get_categories()` since Beta 2)
     * @param int $key The category that you want to return.  Remember that arrays begin with 0, not 1
     * @return \SimplePie\Category|null
     */
    public function get_category(int $key = 0)
    {
        $categories = $this->get_categories();
        if (isset($categories[$key])) {
            return $categories[$key];
        }

        return null;
    }

    /**
     * Get all categories for the item
     *
     * Uses `<atom:category>`, `<category>` or `<dc:subject>`
     *
     * @since Beta 3
     * @return \SimplePie\Category[]|null List of {@see \SimplePie\Category} objects
     */
    public function get_categories()
    {
        $categories = [];

        $type = 'category';
        foreach ((array) $this->get_item_tags(\SimplePie\SimplePie::NAMESPACE_ATOM_10, $type) as $category) {
            $term = null;
            $scheme = null;
            $label = null;
            if (isset($category['attribs']['']['term'])) {
                $term = $this->sanitize($category['attribs']['']['term'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
            }
            if (isset($category['attribs']['']['scheme'])) {
                $scheme = $this->sanitize($category['attribs']['']['scheme'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
            }
            if (isset($category['attribs']['']['label'])) {
                $label = $this->sanitize($category['attribs']['']['label'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
            }
            $categories[] = $this->registry->create(Category::class, [$term, $scheme, $label, $type]);
        }
        foreach ((array) $this->get_item_tags(\SimplePie\SimplePie::NAMESPACE_RSS_20, $type) as $category) {
            // This is really the label, but keep this as the term also for BC.
            // Label will also work on retrieving because that falls back to term.
            $term = $this->sanitize($category['data'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
            if (isset($category['attribs']['']['domain'])) {
                $scheme = $this->sanitize($category['attribs']['']['domain'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
            } else {
                $scheme = null;
            }
            $categories[] = $this->registry->create(Category::class, [$term, $scheme, null, $type]);
        }

        $type = 'subject';
        foreach ((array) $this->get_item_tags(\SimplePie\SimplePie::NAMESPACE_DC_11, $type) as $category) {
            $categories[] = $this->registry->create(Category::class, [$this->sanitize($category['data'], \SimplePie\SimplePie::CONSTRUCT_TEXT), null, null, $type]);
        }
        foreach ((array) $this->get_item_tags(\SimplePie\SimplePie::NAMESPACE_DC_10, $type) as $category) {
            $categories[] = $this->registry->create(Category::class, [$this->sanitize($category['data'], \SimplePie\SimplePie::CONSTRUCT_TEXT), null, null, $type]);
        }

        if (!empty($categories)) {
            return array_unique($categories);
        }

        return null;
    }

    /**
     * Get an author for the item
     *
     * @since Beta 2
     * @param int $key The author that you want to return.  Remember that arrays begin with 0, not 1
     * @return \SimplePie\Author|null
     */
    public function get_author(int $key = 0)
    {
        $authors = $this->get_authors();
        if (isset($authors[$key])) {
            return $authors[$key];
        }

        return null;
    }

    /**
     * Get a contributor for the item
     *
     * @since 1.1
     * @param int $key The contrbutor that you want to return.  Remember that arrays begin with 0, not 1
     * @return \SimplePie\Author|null
     */
    public function get_contributor(int $key = 0)
    {
        $contributors = $this->get_contributors();
        if (isset($contributors[$key])) {
            return $contributors[$key];
        }

        return null;
    }

    /**
     * Get all contributors for the item
     *
     * Uses `<atom:contributor>`
     *
     * @since 1.1
     * @return \SimplePie\Author[]|null List of {@see \SimplePie\Author} objects
     */
    public function get_contributors()
    {
        $contributors = [];
        foreach ((array) $this->get_item_tags(\SimplePie\SimplePie::NAMESPACE_ATOM_10, 'contributor') as $contributor) {
            $name = null;
            $uri = null;
            $email = null;
            if (isset($contributor['child'][\SimplePie\SimplePie::NAMESPACE_ATOM_10]['name'][0]['data'])) {
                $name = $this->sanitize($contributor['child'][\SimplePie\SimplePie::NAMESPACE_ATOM_10]['name'][0]['data'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
            }
            if (isset($contributor['child'][\SimplePie\SimplePie::NAMESPACE_ATOM_10]['uri'][0]['data'])) {
                $uri = $contributor['child'][\SimplePie\SimplePie::NAMESPACE_ATOM_10]['uri'][0];
                $uri = $this->sanitize($uri['data'], \SimplePie\SimplePie::CONSTRUCT_IRI, $this->get_base($uri));
            }
            if (isset($contributor['child'][\SimplePie\SimplePie::NAMESPACE_ATOM_10]['email'][0]['data'])) {
                $email = $this->sanitize($contributor['child'][\SimplePie\SimplePie::NAMESPACE_ATOM_10]['email'][0]['data'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
            }
            if ($name !== null || $email !== null || $uri !== null) {
                $contributors[] = $this->registry->create(Author::class, [$name, $uri, $email]);
            }
        }
        foreach ((array) $this->get_item_tags(\SimplePie\SimplePie::NAMESPACE_ATOM_03, 'contributor') as $contributor) {
            $name = null;
            $url = null;
            $email = null;
            if (isset($contributor['child'][\SimplePie\SimplePie::NAMESPACE_ATOM_03]['name'][0]['data'])) {
                $name = $this->sanitize($contributor['child'][\SimplePie\SimplePie::NAMESPACE_ATOM_03]['name'][0]['data'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
            }
            if (isset($contributor['child'][\SimplePie\SimplePie::NAMESPACE_ATOM_03]['url'][0]['data'])) {
                $url = $contributor['child'][\SimplePie\SimplePie::NAMESPACE_ATOM_03]['url'][0];
                $url = $this->sanitize($url['data'], \SimplePie\SimplePie::CONSTRUCT_IRI, $this->get_base($url));
            }
            if (isset($contributor['child'][\SimplePie\SimplePie::NAMESPACE_ATOM_03]['email'][0]['data'])) {
                $email = $this->sanitize($contributor['child'][\SimplePie\SimplePie::NAMESPACE_ATOM_03]['email'][0]['data'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
            }
            if ($name !== null || $email !== null || $url !== null) {
                $contributors[] = $this->registry->create(Author::class, [$name, $url, $email]);
            }
        }

        if (!empty($contributors)) {
            return array_unique($contributors);
        }

        return null;
    }

    /**
     * Get all authors for the item
     *
     * Uses `<atom:author>`, `<author>`, `<dc:creator>` or `<itunes:author>`
     *
     * @since Beta 2
     * @return \SimplePie\Author[]|null List of {@see \SimplePie\Author} objects
     */
    public function get_authors()
    {
        $authors = [];
        foreach ((array) $this->get_item_tags(\SimplePie\SimplePie::NAMESPACE_ATOM_10, 'author') as $author) {
            $name = null;
            $uri = null;
            $email = null;
            if (isset($author['child'][\SimplePie\SimplePie::NAMESPACE_ATOM_10]['name'][0]['data'])) {
                $name = $this->sanitize($author['child'][\SimplePie\SimplePie::NAMESPACE_ATOM_10]['name'][0]['data'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
            }
            if (isset($author['child'][\SimplePie\SimplePie::NAMESPACE_ATOM_10]['uri'][0]['data'])) {
                $uri = $author['child'][\SimplePie\SimplePie::NAMESPACE_ATOM_10]['uri'][0];
                $uri = $this->sanitize($uri['data'], \SimplePie\SimplePie::CONSTRUCT_IRI, $this->get_base($uri));
            }
            if (isset($author['child'][\SimplePie\SimplePie::NAMESPACE_ATOM_10]['email'][0]['data'])) {
                $email = $this->sanitize($author['child'][\SimplePie\SimplePie::NAMESPACE_ATOM_10]['email'][0]['data'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
            }
            if ($name !== null || $email !== null || $uri !== null) {
                $authors[] = $this->registry->create(Author::class, [$name, $uri, $email]);
            }
        }
        if ($author = $this->get_item_tags(\SimplePie\SimplePie::NAMESPACE_ATOM_03, 'author')) {
            $name = null;
            $url = null;
            $email = null;
            if (isset($author[0]['child'][\SimplePie\SimplePie::NAMESPACE_ATOM_03]['name'][0]['data'])) {
                $name = $this->sanitize($author[0]['child'][\SimplePie\SimplePie::NAMESPACE_ATOM_03]['name'][0]['data'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
            }
            if (isset($author[0]['child'][\SimplePie\SimplePie::NAMESPACE_ATOM_03]['url'][0]['data'])) {
                $url = $author[0]['child'][\SimplePie\SimplePie::NAMESPACE_ATOM_03]['url'][0];
                $url = $this->sanitize($url['data'], \SimplePie\SimplePie::CONSTRUCT_IRI, $this->get_base($url));
            }
            if (isset($author[0]['child'][\SimplePie\SimplePie::NAMESPACE_ATOM_03]['email'][0]['data'])) {
                $email = $this->sanitize($author[0]['child'][\SimplePie\SimplePie::NAMESPACE_ATOM_03]['email'][0]['data'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
            }
            if ($name !== null || $email !== null || $url !== null) {
                $authors[] = $this->registry->create(Author::class, [$name, $url, $email]);
            }
        }
        if ($author = $this->get_item_tags(\SimplePie\SimplePie::NAMESPACE_RSS_20, 'author')) {
            $authors[] = $this->registry->create(Author::class, [null, null, $this->sanitize($author[0]['data'], \SimplePie\SimplePie::CONSTRUCT_TEXT)]);
        }
        foreach ((array) $this->get_item_tags(\SimplePie\SimplePie::NAMESPACE_DC_11, 'creator') as $author) {
            $authors[] = $this->registry->create(Author::class, [$this->sanitize($author['data'], \SimplePie\SimplePie::CONSTRUCT_TEXT), null, null]);
        }
        foreach ((array) $this->get_item_tags(\SimplePie\SimplePie::NAMESPACE_DC_10, 'creator') as $author) {
            $authors[] = $this->registry->create(Author::class, [$this->sanitize($author['data'], \SimplePie\SimplePie::CONSTRUCT_TEXT), null, null]);
        }
        foreach ((array) $this->get_item_tags(\SimplePie\SimplePie::NAMESPACE_ITUNES, 'author') as $author) {
            $authors[] = $this->registry->create(Author::class, [$this->sanitize($author['data'], \SimplePie\SimplePie::CONSTRUCT_TEXT), null, null]);
        }

        if (!empty($authors)) {
            return array_unique($authors);
        } elseif (($source = $this->get_source()) && ($authors = $source->get_authors())) {
            return $authors;
        } elseif ($authors = $this->feed->get_authors()) {
            return $authors;
        }

        return null;
    }

    /**
     * Get the copyright info for the item
     *
     * Uses `<atom:rights>` or `<dc:rights>`
     *
     * @since 1.1
     * @return string|null
     */
    public function get_copyright()
    {
        if ($return = $this->get_item_tags(\SimplePie\SimplePie::NAMESPACE_ATOM_10, 'rights')) {
            return $this->sanitize($return[0]['data'], $this->registry->call(Misc::class, 'atom_10_construct_type', [$return[0]['attribs']]), $this->get_base($return[0]));
        } elseif ($return = $this->get_item_tags(\SimplePie\SimplePie::NAMESPACE_DC_11, 'rights')) {
            return $this->sanitize($return[0]['data'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
        } elseif ($return = $this->get_item_tags(\SimplePie\SimplePie::NAMESPACE_DC_10, 'rights')) {
            return $this->sanitize($return[0]['data'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
        }

        return null;
    }

    /**
     * Get the posting date/time for the item
     *
     * Uses `<atom:published>`, `<atom:updated>`, `<atom:issued>`,
     * `<atom:modified>`, `<pubDate>` or `<dc:date>`
     *
     * Note: obeys PHP's timezone setting. To get a UTC date/time, use
     * {@see get_gmdate}
     *
     * @since Beta 2 (previously called `get_item_date` since 0.8)
     *
     * @param string $date_format Supports any PHP date format from {@see http://php.net/date} (empty for the raw data)
     * @return ($date_format is 'U' ? ?int : ?string)
     */
    public function get_date(string $date_format = 'j F Y, g:i a')
    {
        if (!isset($this->data['date'])) {
            if ($return = $this->get_item_tags(\SimplePie\SimplePie::NAMESPACE_ATOM_10, 'published')) {
                $this->data['date']['raw'] = $return[0]['data'];
            } elseif ($return = $this->get_item_tags(\SimplePie\SimplePie::NAMESPACE_RSS_20, 'pubDate')) {
                $this->data['date']['raw'] = $return[0]['data'];
            } elseif ($return = $this->get_item_tags(\SimplePie\SimplePie::NAMESPACE_DC_11, 'date')) {
                $this->data['date']['raw'] = $return[0]['data'];
            } elseif ($return = $this->get_item_tags(\SimplePie\SimplePie::NAMESPACE_DC_10, 'date')) {
                $this->data['date']['raw'] = $return[0]['data'];
            } elseif ($return = $this->get_item_tags(\SimplePie\SimplePie::NAMESPACE_ATOM_10, 'updated')) {
                $this->data['date']['raw'] = $return[0]['data'];
            } elseif ($return = $this->get_item_tags(\SimplePie\SimplePie::NAMESPACE_ATOM_03, 'issued')) {
                $this->data['date']['raw'] = $return[0]['data'];
            } elseif ($return = $this->get_item_tags(\SimplePie\SimplePie::NAMESPACE_ATOM_03, 'created')) {
                $this->data['date']['raw'] = $return[0]['data'];
            } elseif ($return = $this->get_item_tags(\SimplePie\SimplePie::NAMESPACE_ATOM_03, 'modified')) {
                $this->data['date']['raw'] = $return[0]['data'];
            }

            if (!empty($this->data['date']['raw'])) {
                $parser = $this->registry->call(Parse\Date::class, 'get');
                $this->data['date']['parsed'] = $parser->parse($this->data['date']['raw']) ?: null;
            } else {
                $this->data['date'] = null;
            }
        }
        if ($this->data['date']) {
            switch ($date_format) {
                case '':
                    return $this->sanitize($this->data['date']['raw'], \SimplePie\SimplePie::CONSTRUCT_TEXT);

                case 'U':
                    return $this->data['date']['parsed'];

                default:
                    return date($date_format, $this->data['date']['parsed']);
            }
        }

        return null;
    }

    /**
     * Get the update date/time for the item
     *
     * Uses `<atom:updated>`
     *
     * Note: obeys PHP's timezone setting. To get a UTC date/time, use
     * {@see get_gmdate}
     *
     * @param string $date_format Supports any PHP date format from {@see http://php.net/date} (empty for the raw data)
     * @return ($date_format is 'U' ? ?int : ?string)
     */
    public function get_updated_date(string $date_format = 'j F Y, g:i a')
    {
        if (!isset($this->data['updated'])) {
            if ($return = $this->get_item_tags(\SimplePie\SimplePie::NAMESPACE_ATOM_10, 'updated')) {
                $this->data['updated']['raw'] = $return[0]['data'];
            }

            if (!empty($this->data['updated']['raw'])) {
                $parser = $this->registry->call(Parse\Date::class, 'get');
                $this->data['updated']['parsed'] = $parser->parse($this->data['updated']['raw']) ?: null;
            } else {
                $this->data['updated'] = null;
            }
        }
        if ($this->data['updated']) {
            switch ($date_format) {
                case '':
                    return $this->sanitize($this->data['updated']['raw'], \SimplePie\SimplePie::CONSTRUCT_TEXT);

                case 'U':
                    return $this->data['updated']['parsed'];

                default:
                    return date($date_format, $this->data['updated']['parsed']);
            }
        }

        return null;
    }

    /**
     * Get the localized posting date/time for the item
     *
     * Returns the date formatted in the localized language. To display in
     * languages other than the server's default, you need to change the locale
     * with {@link http://php.net/setlocale setlocale()}. The available
     * localizations depend on which ones are installed on your web server.
     *
     * @since 1.0
     *
     * @param string $date_format Supports any PHP date format from {@see http://php.net/strftime} (empty for the raw data)
     * @return string|null|false see `strftime` for when this can return `false`
     */
    public function get_local_date(string $date_format = '%c')
    {
        if ($date_format === '') {
            if (($raw_date = $this->get_date('')) === null) {
                return null;
            }

            return $this->sanitize($raw_date, \SimplePie\SimplePie::CONSTRUCT_TEXT);
        } elseif (($date = $this->get_date('U')) !== null && $date !== false) {
            return strftime($date_format, $date);
        }

        return null;
    }

    /**
     * Get the posting date/time for the item (UTC time)
     *
     * @see get_date
     * @param string $date_format Supports any PHP date format from {@see http://php.net/date}
     * @return string|null
     */
    public function get_gmdate(string $date_format = 'j F Y, g:i a')
    {
        $date = $this->get_date('U');
        if ($date === null) {
            return null;
        }

        return gmdate($date_format, $date);
    }

    /**
     * Get the update date/time for the item (UTC time)
     *
     * @see get_updated_date
     * @param string $date_format Supports any PHP date format from {@see http://php.net/date}
     * @return string|null
     */
    public function get_updated_gmdate(string $date_format = 'j F Y, g:i a')
    {
        $date = $this->get_updated_date('U');
        if ($date === null) {
            return null;
        }

        return gmdate($date_format, $date);
    }

    /**
     * Get the permalink for the item
     *
     * Returns the first link available with a relationship of "alternate".
     * Identical to {@see get_link()} with key 0
     *
     * @see get_link
     * @since 0.8
     * @return string|null Permalink URL
     */
    public function get_permalink()
    {
        $link = $this->get_link();
        $enclosure = $this->get_enclosure(0);
        if ($link !== null) {
            return $link;
        } elseif ($enclosure !== null) {
            return $enclosure->get_link();
        }

        return null;
    }

    /**
     * Get a single link for the item
     *
     * @since Beta 3
     * @param int $key The link that you want to return.  Remember that arrays begin with 0, not 1
     * @param string $rel The relationship of the link to return
     * @return string|null Link URL
     */
    public function get_link(int $key = 0, string $rel = 'alternate')
    {
        $links = $this->get_links($rel);
        if ($links && $links[$key] !== null) {
            return $links[$key];
        }

        return null;
    }

    /**
     * Get all links for the item
     *
     * Uses `<atom:link>`, `<link>` or `<guid>`
     *
     * @since Beta 2
     * @param string $rel The relationship of links to return
     * @return array<string>|null Links found for the item (strings)
     */
    public function get_links(string $rel = 'alternate')
    {
        if (!isset($this->data['links'])) {
            $this->data['links'] = [];
            foreach ((array) $this->get_item_tags(\SimplePie\SimplePie::NAMESPACE_ATOM_10, 'link') as $link) {
                if (isset($link['attribs']['']['href'])) {
                    $link_rel = (isset($link['attribs']['']['rel'])) ? $link['attribs']['']['rel'] : 'alternate';
                    $this->data['links'][$link_rel][] = $this->sanitize($link['attribs']['']['href'], \SimplePie\SimplePie::CONSTRUCT_IRI, $this->get_own_base($link));
                }
            }
            foreach ((array) $this->get_item_tags(\SimplePie\SimplePie::NAMESPACE_ATOM_03, 'link') as $link) {
                if (isset($link['attribs']['']['href'])) {
                    $link_rel = (isset($link['attribs']['']['rel'])) ? $link['attribs']['']['rel'] : 'alternate';
                    $this->data['links'][$link_rel][] = $this->sanitize($link['attribs']['']['href'], \SimplePie\SimplePie::CONSTRUCT_IRI, $this->get_own_base($link));
                }
            }
            if ($links = $this->get_item_tags(\SimplePie\SimplePie::NAMESPACE_RSS_10, 'link')) {
                $this->data['links']['alternate'][] = $this->sanitize($links[0]['data'], \SimplePie\SimplePie::CONSTRUCT_IRI, $this->get_own_base($links[0]));
            }
            if ($links = $this->get_item_tags(\SimplePie\SimplePie::NAMESPACE_RSS_090, 'link')) {
                $this->data['links']['alternate'][] = $this->sanitize($links[0]['data'], \SimplePie\SimplePie::CONSTRUCT_IRI, $this->get_own_base($links[0]));
            }
            if ($links = $this->get_item_tags(\SimplePie\SimplePie::NAMESPACE_RSS_20, 'link')) {
                $this->data['links']['alternate'][] = $this->sanitize($links[0]['data'], \SimplePie\SimplePie::CONSTRUCT_IRI, $this->get_own_base($links[0]));
            }
            if ($links = $this->get_item_tags(\SimplePie\SimplePie::NAMESPACE_RSS_20, 'guid')) {
                if (!isset($links[0]['attribs']['']['isPermaLink']) || strtolower(trim($links[0]['attribs']['']['isPermaLink'])) === 'true') {
                    $this->data['links']['alternate'][] = $this->sanitize($links[0]['data'], \SimplePie\SimplePie::CONSTRUCT_IRI, $this->get_own_base($links[0]));
                }
            }

            $keys = array_keys($this->data['links']);
            foreach ($keys as $key) {
                if ($this->registry->call(Misc::class, 'is_isegment_nz_nc', [$key])) {
                    if (isset($this->data['links'][\SimplePie\SimplePie::IANA_LINK_RELATIONS_REGISTRY . $key])) {
                        $this->data['links'][\SimplePie\SimplePie::IANA_LINK_RELATIONS_REGISTRY . $key] = array_merge($this->data['links'][$key], $this->data['links'][\SimplePie\SimplePie::IANA_LINK_RELATIONS_REGISTRY . $key]);
                        $this->data['links'][$key] = &$this->data['links'][\SimplePie\SimplePie::IANA_LINK_RELATIONS_REGISTRY . $key];
                    } else {
                        $this->data['links'][\SimplePie\SimplePie::IANA_LINK_RELATIONS_REGISTRY . $key] = &$this->data['links'][$key];
                    }
                } elseif (substr((string) $key, 0, 41) === \SimplePie\SimplePie::IANA_LINK_RELATIONS_REGISTRY) {
                    $this->data['links'][substr((string) $key, 41)] = &$this->data['links'][$key];
                }
                $this->data['links'][$key] = array_unique($this->data['links'][$key]);
            }
        }
        if (isset($this->data['links'][$rel])) {
            return $this->data['links'][$rel];
        }

        return null;
    }

    /**
     * Get an enclosure from the item
     *
     * Supports the <enclosure> RSS tag, as well as Media RSS and iTunes RSS.
     *
     * @since Beta 2
     * @todo Add ability to prefer one type of content over another (in a media group).
     * @param int $key The enclosure that you want to return.  Remember that arrays begin with 0, not 1
     * @return \SimplePie\Enclosure|null
     */
    public function get_enclosure(int $key = 0)
    {
        $enclosures = $this->get_enclosures();
        if (isset($enclosures[$key])) {
            return $enclosures[$key];
        }

        return null;
    }

    /**
     * Get all available enclosures (podcasts, etc.)
     *
     * Supports the <enclosure> RSS tag, as well as Media RSS and iTunes RSS.
     *
     * At this point, we're pretty much assuming that all enclosures for an item
     * are the same content.  Anything else is too complicated to
     * properly support.
     *
     * @since Beta 2
     * @todo Add support for end-user defined sorting of enclosures by type/handler (so we can prefer the faster-loading FLV over MP4).
     * @todo If an element exists at a level, but its value is empty, we should fall back to the value from the parent (if it exists).
     * @return \SimplePie\Enclosure[]|null List of \SimplePie\Enclosure items
     */
    public function get_enclosures()
    {
        if (!isset($this->data['enclosures'])) {
            $this->data['enclosures'] = [];

            // Elements
            $captions_parent = null;
            $categories_parent = null;
            $copyrights_parent = null;
            $credits_parent = null;
            $description_parent = null;
            $duration_parent = null;
            $hashes_parent = null;
            $keywords_parent = null;
            $player_parent = null;
            $ratings_parent = null;
            $restrictions_parent = [];
            $thumbnails_parent = null;
            $title_parent = null;

            // Let's do the channel and item-level ones first, and just re-use them if we need to.
            $parent = $this->get_feed();

            // CAPTIONS
            if ($captions = $this->get_item_tags(\SimplePie\SimplePie::NAMESPACE_MEDIARSS, 'text')) {
                foreach ($captions as $caption) {
                    $caption_type = null;
                    $caption_lang = null;
                    $caption_startTime = null;
                    $caption_endTime = null;
                    $caption_text = null;
                    if (isset($caption['attribs']['']['type'])) {
                        $caption_type = $this->sanitize($caption['attribs']['']['type'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                    }
                    if (isset($caption['attribs']['']['lang'])) {
                        $caption_lang = $this->sanitize($caption['attribs']['']['lang'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                    }
                    if (isset($caption['attribs']['']['start'])) {
                        $caption_startTime = $this->sanitize($caption['attribs']['']['start'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                    }
                    if (isset($caption['attribs']['']['end'])) {
                        $caption_endTime = $this->sanitize($caption['attribs']['']['end'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                    }
                    if (isset($caption['data'])) {
                        $caption_text = $this->sanitize($caption['data'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                    }
                    $captions_parent[] = $this->registry->create(Caption::class, [$caption_type, $caption_lang, $caption_startTime, $caption_endTime, $caption_text]);
                }
            } elseif ($captions = $parent->get_channel_tags(\SimplePie\SimplePie::NAMESPACE_MEDIARSS, 'text')) {
                foreach ($captions as $caption) {
                    $caption_type = null;
                    $caption_lang = null;
                    $caption_startTime = null;
                    $caption_endTime = null;
                    $caption_text = null;
                    if (isset($caption['attribs']['']['type'])) {
                        $caption_type = $this->sanitize($caption['attribs']['']['type'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                    }
                    if (isset($caption['attribs']['']['lang'])) {
                        $caption_lang = $this->sanitize($caption['attribs']['']['lang'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                    }
                    if (isset($caption['attribs']['']['start'])) {
                        $caption_startTime = $this->sanitize($caption['attribs']['']['start'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                    }
                    if (isset($caption['attribs']['']['end'])) {
                        $caption_endTime = $this->sanitize($caption['attribs']['']['end'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                    }
                    if (isset($caption['data'])) {
                        $caption_text = $this->sanitize($caption['data'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                    }
                    $captions_parent[] = $this->registry->create(Caption::class, [$caption_type, $caption_lang, $caption_startTime, $caption_endTime, $caption_text]);
                }
            }
            if (is_array($captions_parent)) {
                $captions_parent = array_values(array_unique($captions_parent));
            }

            // CATEGORIES
            foreach ((array) $this->get_item_tags(\SimplePie\SimplePie::NAMESPACE_MEDIARSS, 'category') as $category) {
                $term = null;
                $scheme = null;
                $label = null;
                if (isset($category['data'])) {
                    $term = $this->sanitize($category['data'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                }
                if (isset($category['attribs']['']['scheme'])) {
                    $scheme = $this->sanitize($category['attribs']['']['scheme'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                } else {
                    $scheme = 'http://search.yahoo.com/mrss/category_schema';
                }
                if (isset($category['attribs']['']['label'])) {
                    $label = $this->sanitize($category['attribs']['']['label'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                }
                $categories_parent[] = $this->registry->create(Category::class, [$term, $scheme, $label]);
            }
            foreach ((array) $parent->get_channel_tags(\SimplePie\SimplePie::NAMESPACE_MEDIARSS, 'category') as $category) {
                $term = null;
                $scheme = null;
                $label = null;
                if (isset($category['data'])) {
                    $term = $this->sanitize($category['data'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                }
                if (isset($category['attribs']['']['scheme'])) {
                    $scheme = $this->sanitize($category['attribs']['']['scheme'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                } else {
                    $scheme = 'http://search.yahoo.com/mrss/category_schema';
                }
                if (isset($category['attribs']['']['label'])) {
                    $label = $this->sanitize($category['attribs']['']['label'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                }
                $categories_parent[] = $this->registry->create(Category::class, [$term, $scheme, $label]);
            }
            foreach ((array) $parent->get_channel_tags(\SimplePie\SimplePie::NAMESPACE_ITUNES, 'category') as $category) {
                $term = null;
                $scheme = 'http://www.itunes.com/dtds/podcast-1.0.dtd';
                $label = null;
                if (isset($category['attribs']['']['text'])) {
                    $label = $this->sanitize($category['attribs']['']['text'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                }
                $categories_parent[] = $this->registry->create(Category::class, [$term, $scheme, $label]);

                if (isset($category['child'][\SimplePie\SimplePie::NAMESPACE_ITUNES]['category'])) {
                    foreach ((array) $category['child'][\SimplePie\SimplePie::NAMESPACE_ITUNES]['category'] as $subcategory) {
                        if (isset($subcategory['attribs']['']['text'])) {
                            $label = $this->sanitize($subcategory['attribs']['']['text'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                        }
                        $categories_parent[] = $this->registry->create(Category::class, [$term, $scheme, $label]);
                    }
                }
            }
            if (is_array($categories_parent)) {
                $categories_parent = array_values(array_unique($categories_parent));
            }

            // COPYRIGHT
            if ($copyright = $this->get_item_tags(\SimplePie\SimplePie::NAMESPACE_MEDIARSS, 'copyright')) {
                $copyright_url = null;
                $copyright_label = null;
                if (isset($copyright[0]['attribs']['']['url'])) {
                    $copyright_url = $this->sanitize($copyright[0]['attribs']['']['url'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                }
                if (isset($copyright[0]['data'])) {
                    $copyright_label = $this->sanitize($copyright[0]['data'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                }
                $copyrights_parent = $this->registry->create(Copyright::class, [$copyright_url, $copyright_label]);
            } elseif ($copyright = $parent->get_channel_tags(\SimplePie\SimplePie::NAMESPACE_MEDIARSS, 'copyright')) {
                $copyright_url = null;
                $copyright_label = null;
                if (isset($copyright[0]['attribs']['']['url'])) {
                    $copyright_url = $this->sanitize($copyright[0]['attribs']['']['url'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                }
                if (isset($copyright[0]['data'])) {
                    $copyright_label = $this->sanitize($copyright[0]['data'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                }
                $copyrights_parent = $this->registry->create(Copyright::class, [$copyright_url, $copyright_label]);
            }

            // CREDITS
            if ($credits = $this->get_item_tags(\SimplePie\SimplePie::NAMESPACE_MEDIARSS, 'credit')) {
                foreach ($credits as $credit) {
                    $credit_role = null;
                    $credit_scheme = null;
                    $credit_name = null;
                    if (isset($credit['attribs']['']['role'])) {
                        $credit_role = $this->sanitize($credit['attribs']['']['role'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                    }
                    if (isset($credit['attribs']['']['scheme'])) {
                        $credit_scheme = $this->sanitize($credit['attribs']['']['scheme'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                    } else {
                        $credit_scheme = 'urn:ebu';
                    }
                    if (isset($credit['data'])) {
                        $credit_name = $this->sanitize($credit['data'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                    }
                    $credits_parent[] = $this->registry->create(Credit::class, [$credit_role, $credit_scheme, $credit_name]);
                }
            } elseif ($credits = $parent->get_channel_tags(\SimplePie\SimplePie::NAMESPACE_MEDIARSS, 'credit')) {
                foreach ($credits as $credit) {
                    $credit_role = null;
                    $credit_scheme = null;
                    $credit_name = null;
                    if (isset($credit['attribs']['']['role'])) {
                        $credit_role = $this->sanitize($credit['attribs']['']['role'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                    }
                    if (isset($credit['attribs']['']['scheme'])) {
                        $credit_scheme = $this->sanitize($credit['attribs']['']['scheme'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                    } else {
                        $credit_scheme = 'urn:ebu';
                    }
                    if (isset($credit['data'])) {
                        $credit_name = $this->sanitize($credit['data'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                    }
                    $credits_parent[] = $this->registry->create(Credit::class, [$credit_role, $credit_scheme, $credit_name]);
                }
            }
            if (is_array($credits_parent)) {
                $credits_parent = array_values(array_unique($credits_parent));
            }

            // DESCRIPTION
            if ($description_parent = $this->get_item_tags(\SimplePie\SimplePie::NAMESPACE_MEDIARSS, 'description')) {
                if (isset($description_parent[0]['data'])) {
                    $description_parent = $this->sanitize($description_parent[0]['data'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                }
            } elseif ($description_parent = $parent->get_channel_tags(\SimplePie\SimplePie::NAMESPACE_MEDIARSS, 'description')) {
                if (isset($description_parent[0]['data'])) {
                    $description_parent = $this->sanitize($description_parent[0]['data'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                }
            }

            // DURATION
            $duration_tags = $this->get_item_tags(\SimplePie\SimplePie::NAMESPACE_ITUNES, 'duration');
            if ($duration_tags !== null) {
                $seconds = null;
                $minutes = null;
                $hours = null;
                if (isset($duration_tags[0]['data'])) {
                    $temp = explode(':', $this->sanitize($duration_tags[0]['data'], \SimplePie\SimplePie::CONSTRUCT_TEXT));
                    $seconds = (int) array_pop($temp);
                    if (count($temp) > 0) {
                        $minutes = (int) array_pop($temp);
                        $seconds += $minutes * 60;
                    }
                    if (count($temp) > 0) {
                        $hours = (int) array_pop($temp);
                        $seconds += $hours * 3600;
                    }
                    unset($temp);
                    $duration_parent = $seconds;
                }
            }

            // HASHES
            if ($hashes_iterator = $this->get_item_tags(\SimplePie\SimplePie::NAMESPACE_MEDIARSS, 'hash')) {
                foreach ($hashes_iterator as $hash) {
                    $value = null;
                    $algo = null;
                    if (isset($hash['data'])) {
                        $value = $this->sanitize($hash['data'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                    }
                    if (isset($hash['attribs']['']['algo'])) {
                        $algo = $this->sanitize($hash['attribs']['']['algo'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                    } else {
                        $algo = 'md5';
                    }
                    $hashes_parent[] = $algo.':'.$value;
                }
            } elseif ($hashes_iterator = $parent->get_channel_tags(\SimplePie\SimplePie::NAMESPACE_MEDIARSS, 'hash')) {
                foreach ($hashes_iterator as $hash) {
                    $value = null;
                    $algo = null;
                    if (isset($hash['data'])) {
                        $value = $this->sanitize($hash['data'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                    }
                    if (isset($hash['attribs']['']['algo'])) {
                        $algo = $this->sanitize($hash['attribs']['']['algo'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                    } else {
                        $algo = 'md5';
                    }
                    $hashes_parent[] = $algo.':'.$value;
                }
            }
            if (is_array($hashes_parent)) {
                $hashes_parent = array_values(array_unique($hashes_parent));
            }

            // KEYWORDS
            if ($keywords = $this->get_item_tags(\SimplePie\SimplePie::NAMESPACE_MEDIARSS, 'keywords')) {
                if (isset($keywords[0]['data'])) {
                    $temp = explode(',', $this->sanitize($keywords[0]['data'], \SimplePie\SimplePie::CONSTRUCT_TEXT));
                    foreach ($temp as $word) {
                        $keywords_parent[] = trim($word);
                    }
                }
                unset($temp);
            } elseif ($keywords = $this->get_item_tags(\SimplePie\SimplePie::NAMESPACE_ITUNES, 'keywords')) {
                if (isset($keywords[0]['data'])) {
                    $temp = explode(',', $this->sanitize($keywords[0]['data'], \SimplePie\SimplePie::CONSTRUCT_TEXT));
                    foreach ($temp as $word) {
                        $keywords_parent[] = trim($word);
                    }
                }
                unset($temp);
            } elseif ($keywords = $parent->get_channel_tags(\SimplePie\SimplePie::NAMESPACE_MEDIARSS, 'keywords')) {
                if (isset($keywords[0]['data'])) {
                    $temp = explode(',', $this->sanitize($keywords[0]['data'], \SimplePie\SimplePie::CONSTRUCT_TEXT));
                    foreach ($temp as $word) {
                        $keywords_parent[] = trim($word);
                    }
                }
                unset($temp);
            } elseif ($keywords = $parent->get_channel_tags(\SimplePie\SimplePie::NAMESPACE_ITUNES, 'keywords')) {
                if (isset($keywords[0]['data'])) {
                    $temp = explode(',', $this->sanitize($keywords[0]['data'], \SimplePie\SimplePie::CONSTRUCT_TEXT));
                    foreach ($temp as $word) {
                        $keywords_parent[] = trim($word);
                    }
                }
                unset($temp);
            }
            if (is_array($keywords_parent)) {
                $keywords_parent = array_values(array_unique($keywords_parent));
            }

            // PLAYER
            if ($player_parent = $this->get_item_tags(\SimplePie\SimplePie::NAMESPACE_MEDIARSS, 'player')) {
                if (isset($player_parent[0]['attribs']['']['url'])) {
                    $player_parent = $this->sanitize($player_parent[0]['attribs']['']['url'], \SimplePie\SimplePie::CONSTRUCT_IRI, $this->get_own_base($player_parent[0]));
                }
            } elseif ($player_parent = $parent->get_channel_tags(\SimplePie\SimplePie::NAMESPACE_MEDIARSS, 'player')) {
                if (isset($player_parent[0]['attribs']['']['url'])) {
                    $player_parent = $this->sanitize($player_parent[0]['attribs']['']['url'], \SimplePie\SimplePie::CONSTRUCT_IRI, $this->get_own_base($player_parent[0]));
                }
            }

            // RATINGS
            if ($ratings = $this->get_item_tags(\SimplePie\SimplePie::NAMESPACE_MEDIARSS, 'rating')) {
                foreach ($ratings as $rating) {
                    $rating_scheme = null;
                    $rating_value = null;
                    if (isset($rating['attribs']['']['scheme'])) {
                        $rating_scheme = $this->sanitize($rating['attribs']['']['scheme'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                    } else {
                        $rating_scheme = 'urn:simple';
                    }
                    if (isset($rating['data'])) {
                        $rating_value = $this->sanitize($rating['data'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                    }
                    $ratings_parent[] = $this->registry->create(Rating::class, [$rating_scheme, $rating_value]);
                }
            } elseif ($ratings = $this->get_item_tags(\SimplePie\SimplePie::NAMESPACE_ITUNES, 'explicit')) {
                foreach ($ratings as $rating) {
                    $rating_scheme = 'urn:itunes';
                    $rating_value = null;
                    if (isset($rating['data'])) {
                        $rating_value = $this->sanitize($rating['data'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                    }
                    $ratings_parent[] = $this->registry->create(Rating::class, [$rating_scheme, $rating_value]);
                }
            } elseif ($ratings = $parent->get_channel_tags(\SimplePie\SimplePie::NAMESPACE_MEDIARSS, 'rating')) {
                foreach ($ratings as $rating) {
                    $rating_scheme = null;
                    $rating_value = null;
                    if (isset($rating['attribs']['']['scheme'])) {
                        $rating_scheme = $this->sanitize($rating['attribs']['']['scheme'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                    } else {
                        $rating_scheme = 'urn:simple';
                    }
                    if (isset($rating['data'])) {
                        $rating_value = $this->sanitize($rating['data'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                    }
                    $ratings_parent[] = $this->registry->create(Rating::class, [$rating_scheme, $rating_value]);
                }
            } elseif ($ratings = $parent->get_channel_tags(\SimplePie\SimplePie::NAMESPACE_ITUNES, 'explicit')) {
                foreach ($ratings as $rating) {
                    $rating_scheme = 'urn:itunes';
                    $rating_value = null;
                    if (isset($rating['data'])) {
                        $rating_value = $this->sanitize($rating['data'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                    }
                    $ratings_parent[] = $this->registry->create(Rating::class, [$rating_scheme, $rating_value]);
                }
            }
            if (is_array($ratings_parent)) {
                $ratings_parent = array_values(array_unique($ratings_parent));
            }

            // RESTRICTIONS
            if ($restrictions = $this->get_item_tags(\SimplePie\SimplePie::NAMESPACE_MEDIARSS, 'restriction')) {
                foreach ($restrictions as $restriction) {
                    $restriction_relationship = null;
                    $restriction_type = null;
                    $restriction_value = null;
                    if (isset($restriction['attribs']['']['relationship'])) {
                        $restriction_relationship = $this->sanitize($restriction['attribs']['']['relationship'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                    }
                    if (isset($restriction['attribs']['']['type'])) {
                        $restriction_type = $this->sanitize($restriction['attribs']['']['type'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                    }
                    if (isset($restriction['data'])) {
                        $restriction_value = $this->sanitize($restriction['data'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                    }
                    $restrictions_parent[] = $this->registry->create(Restriction::class, [$restriction_relationship, $restriction_type, $restriction_value]);
                }
            } elseif ($restrictions = $this->get_item_tags(\SimplePie\SimplePie::NAMESPACE_ITUNES, 'block')) {
                foreach ($restrictions as $restriction) {
                    $restriction_relationship = Restriction::RELATIONSHIP_ALLOW;
                    $restriction_type = null;
                    $restriction_value = 'itunes';
                    if (isset($restriction['data']) && strtolower($restriction['data']) === 'yes') {
                        $restriction_relationship = Restriction::RELATIONSHIP_DENY;
                    }
                    $restrictions_parent[] = $this->registry->create(Restriction::class, [$restriction_relationship, $restriction_type, $restriction_value]);
                }
            } elseif ($restrictions = $parent->get_channel_tags(\SimplePie\SimplePie::NAMESPACE_MEDIARSS, 'restriction')) {
                foreach ($restrictions as $restriction) {
                    $restriction_relationship = null;
                    $restriction_type = null;
                    $restriction_value = null;
                    if (isset($restriction['attribs']['']['relationship'])) {
                        $restriction_relationship = $this->sanitize($restriction['attribs']['']['relationship'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                    }
                    if (isset($restriction['attribs']['']['type'])) {
                        $restriction_type = $this->sanitize($restriction['attribs']['']['type'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                    }
                    if (isset($restriction['data'])) {
                        $restriction_value = $this->sanitize($restriction['data'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                    }
                    $restrictions_parent[] = $this->registry->create(Restriction::class, [$restriction_relationship, $restriction_type, $restriction_value]);
                }
            } elseif ($restrictions = $parent->get_channel_tags(\SimplePie\SimplePie::NAMESPACE_ITUNES, 'block')) {
                foreach ($restrictions as $restriction) {
                    $restriction_relationship = Restriction::RELATIONSHIP_ALLOW;
                    $restriction_type = null;
                    $restriction_value = 'itunes';
                    if (isset($restriction['data']) && strtolower($restriction['data']) === 'yes') {
                        $restriction_relationship = Restriction::RELATIONSHIP_DENY;
                    }
                    $restrictions_parent[] = $this->registry->create(Restriction::class, [$restriction_relationship, $restriction_type, $restriction_value]);
                }
            }
            if (count($restrictions_parent) > 0) {
                $restrictions_parent = array_values(array_unique($restrictions_parent));
            } else {
                $restrictions_parent = [new \SimplePie\Restriction(Restriction::RELATIONSHIP_ALLOW, null, 'default')];
            }

            // THUMBNAILS
            if ($thumbnails = $this->get_item_tags(\SimplePie\SimplePie::NAMESPACE_MEDIARSS, 'thumbnail')) {
                foreach ($thumbnails as $thumbnail) {
                    if (isset($thumbnail['attribs']['']['url'])) {
                        $thumbnails_parent[] = $this->sanitize($thumbnail['attribs']['']['url'], \SimplePie\SimplePie::CONSTRUCT_IRI, $this->get_own_base($thumbnail));
                    }
                }
            } elseif ($thumbnails = $parent->get_channel_tags(\SimplePie\SimplePie::NAMESPACE_MEDIARSS, 'thumbnail')) {
                foreach ($thumbnails as $thumbnail) {
                    if (isset($thumbnail['attribs']['']['url'])) {
                        $thumbnails_parent[] = $this->sanitize($thumbnail['attribs']['']['url'], \SimplePie\SimplePie::CONSTRUCT_IRI, $this->get_own_base($thumbnail));
                    }
                }
            }

            // TITLES
            if ($title_parent = $this->get_item_tags(\SimplePie\SimplePie::NAMESPACE_MEDIARSS, 'title')) {
                if (isset($title_parent[0]['data'])) {
                    $title_parent = $this->sanitize($title_parent[0]['data'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                }
            } elseif ($title_parent = $parent->get_channel_tags(\SimplePie\SimplePie::NAMESPACE_MEDIARSS, 'title')) {
                if (isset($title_parent[0]['data'])) {
                    $title_parent = $this->sanitize($title_parent[0]['data'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                }
            }

            // Clear the memory
            unset($parent);

            // Attributes
            $bitrate = null;
            $channels = null;
            $duration = null;
            $expression = null;
            $framerate = null;
            $height = null;
            $javascript = null;
            $lang = null;
            $length = null;
            $medium = null;
            $samplingrate = null;
            $type = null;
            $url = null;
            $width = null;

            // Elements
            $captions = null;
            $categories = null;
            $copyrights = null;
            $credits = null;
            $description = null;
            $hashes = null;
            $keywords = null;
            $player = null;
            $ratings = null;
            $restrictions = null;
            $thumbnails = null;
            $title = null;

            // If we have media:group tags, loop through them.
            foreach ((array) $this->get_item_tags(\SimplePie\SimplePie::NAMESPACE_MEDIARSS, 'group') as $group) {
                if (isset($group['child']) && isset($group['child'][\SimplePie\SimplePie::NAMESPACE_MEDIARSS]['content'])) {
                    // If we have media:content tags, loop through them.
                    foreach ((array) $group['child'][\SimplePie\SimplePie::NAMESPACE_MEDIARSS]['content'] as $content) {
                        if (isset($content['attribs']['']['url'])) {
                            // Attributes
                            $bitrate = null;
                            $channels = null;
                            $duration = null;
                            $expression = null;
                            $framerate = null;
                            $height = null;
                            $javascript = null;
                            $lang = null;
                            $length = null;
                            $medium = null;
                            $samplingrate = null;
                            $type = null;
                            $url = null;
                            $width = null;

                            // Elements
                            $captions = null;
                            $categories = null;
                            $copyrights = null;
                            $credits = null;
                            $description = null;
                            $hashes = null;
                            $keywords = null;
                            $player = null;
                            $ratings = null;
                            $restrictions = null;
                            $thumbnails = null;
                            $title = null;

                            // Start checking the attributes of media:content
                            if (isset($content['attribs']['']['bitrate'])) {
                                $bitrate = $this->sanitize($content['attribs']['']['bitrate'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                            }
                            if (isset($content['attribs']['']['channels'])) {
                                $channels = $this->sanitize($content['attribs']['']['channels'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                            }
                            if (isset($content['attribs']['']['duration'])) {
                                $duration = $this->sanitize($content['attribs']['']['duration'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                            } else {
                                $duration = $duration_parent;
                            }
                            if (isset($content['attribs']['']['expression'])) {
                                $expression = $this->sanitize($content['attribs']['']['expression'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                            }
                            if (isset($content['attribs']['']['framerate'])) {
                                $framerate = $this->sanitize($content['attribs']['']['framerate'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                            }
                            if (isset($content['attribs']['']['height'])) {
                                $height = $this->sanitize($content['attribs']['']['height'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                            }
                            if (isset($content['attribs']['']['lang'])) {
                                $lang = $this->sanitize($content['attribs']['']['lang'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                            }
                            if (isset($content['attribs']['']['fileSize'])) {
                                $length = intval($content['attribs']['']['fileSize']);
                            }
                            if (isset($content['attribs']['']['medium'])) {
                                $medium = $this->sanitize($content['attribs']['']['medium'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                            }
                            if (isset($content['attribs']['']['samplingrate'])) {
                                $samplingrate = $this->sanitize($content['attribs']['']['samplingrate'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                            }
                            if (isset($content['attribs']['']['type'])) {
                                $type = $this->sanitize($content['attribs']['']['type'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                            }
                            if (isset($content['attribs']['']['width'])) {
                                $width = $this->sanitize($content['attribs']['']['width'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                            }
                            $url = $this->sanitize($content['attribs']['']['url'], \SimplePie\SimplePie::CONSTRUCT_IRI, $this->get_own_base($content));

                            // Checking the other optional media: elements. Priority: media:content, media:group, item, channel

                            // CAPTIONS
                            if (isset($content['child'][\SimplePie\SimplePie::NAMESPACE_MEDIARSS]['text'])) {
                                foreach ($content['child'][\SimplePie\SimplePie::NAMESPACE_MEDIARSS]['text'] as $caption) {
                                    $caption_type = null;
                                    $caption_lang = null;
                                    $caption_startTime = null;
                                    $caption_endTime = null;
                                    $caption_text = null;
                                    if (isset($caption['attribs']['']['type'])) {
                                        $caption_type = $this->sanitize($caption['attribs']['']['type'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                                    }
                                    if (isset($caption['attribs']['']['lang'])) {
                                        $caption_lang = $this->sanitize($caption['attribs']['']['lang'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                                    }
                                    if (isset($caption['attribs']['']['start'])) {
                                        $caption_startTime = $this->sanitize($caption['attribs']['']['start'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                                    }
                                    if (isset($caption['attribs']['']['end'])) {
                                        $caption_endTime = $this->sanitize($caption['attribs']['']['end'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                                    }
                                    if (isset($caption['data'])) {
                                        $caption_text = $this->sanitize($caption['data'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                                    }
                                    $captions[] = $this->registry->create(Caption::class, [$caption_type, $caption_lang, $caption_startTime, $caption_endTime, $caption_text]);
                                }
                                if (is_array($captions)) {
                                    $captions = array_values(array_unique($captions));
                                }
                            } elseif (isset($group['child'][\SimplePie\SimplePie::NAMESPACE_MEDIARSS]['text'])) {
                                foreach ($group['child'][\SimplePie\SimplePie::NAMESPACE_MEDIARSS]['text'] as $caption) {
                                    $caption_type = null;
                                    $caption_lang = null;
                                    $caption_startTime = null;
                                    $caption_endTime = null;
                                    $caption_text = null;
                                    if (isset($caption['attribs']['']['type'])) {
                                        $caption_type = $this->sanitize($caption['attribs']['']['type'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                                    }
                                    if (isset($caption['attribs']['']['lang'])) {
                                        $caption_lang = $this->sanitize($caption['attribs']['']['lang'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                                    }
                                    if (isset($caption['attribs']['']['start'])) {
                                        $caption_startTime = $this->sanitize($caption['attribs']['']['start'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                                    }
                                    if (isset($caption['attribs']['']['end'])) {
                                        $caption_endTime = $this->sanitize($caption['attribs']['']['end'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                                    }
                                    if (isset($caption['data'])) {
                                        $caption_text = $this->sanitize($caption['data'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                                    }
                                    $captions[] = $this->registry->create(Caption::class, [$caption_type, $caption_lang, $caption_startTime, $caption_endTime, $caption_text]);
                                }
                                if (is_array($captions)) {
                                    $captions = array_values(array_unique($captions));
                                }
                            } else {
                                $captions = $captions_parent;
                            }

                            // CATEGORIES
                            if (isset($content['child'][\SimplePie\SimplePie::NAMESPACE_MEDIARSS]['category'])) {
                                foreach ((array) $content['child'][\SimplePie\SimplePie::NAMESPACE_MEDIARSS]['category'] as $category) {
                                    $term = null;
                                    $scheme = null;
                                    $label = null;
                                    if (isset($category['data'])) {
                                        $term = $this->sanitize($category['data'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                                    }
                                    if (isset($category['attribs']['']['scheme'])) {
                                        $scheme = $this->sanitize($category['attribs']['']['scheme'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                                    } else {
                                        $scheme = 'http://search.yahoo.com/mrss/category_schema';
                                    }
                                    if (isset($category['attribs']['']['label'])) {
                                        $label = $this->sanitize($category['attribs']['']['label'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                                    }
                                    $categories[] = $this->registry->create(Category::class, [$term, $scheme, $label]);
                                }
                            }
                            if (isset($group['child'][\SimplePie\SimplePie::NAMESPACE_MEDIARSS]['category'])) {
                                foreach ((array) $group['child'][\SimplePie\SimplePie::NAMESPACE_MEDIARSS]['category'] as $category) {
                                    $term = null;
                                    $scheme = null;
                                    $label = null;
                                    if (isset($category['data'])) {
                                        $term = $this->sanitize($category['data'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                                    }
                                    if (isset($category['attribs']['']['scheme'])) {
                                        $scheme = $this->sanitize($category['attribs']['']['scheme'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                                    } else {
                                        $scheme = 'http://search.yahoo.com/mrss/category_schema';
                                    }
                                    if (isset($category['attribs']['']['label'])) {
                                        $label = $this->sanitize($category['attribs']['']['label'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                                    }
                                    $categories[] = $this->registry->create(Category::class, [$term, $scheme, $label]);
                                }
                            }
                            if (is_array($categories) && is_array($categories_parent)) {
                                $categories = array_values(array_unique(array_merge($categories, $categories_parent)));
                            } elseif (is_array($categories)) {
                                $categories = array_values(array_unique($categories));
                            } elseif (is_array($categories_parent)) {
                                $categories = array_values(array_unique($categories_parent));
                            }

                            // COPYRIGHTS
                            if (isset($content['child'][\SimplePie\SimplePie::NAMESPACE_MEDIARSS]['copyright'])) {
                                $copyright_url = null;
                                $copyright_label = null;
                                if (isset($content['child'][\SimplePie\SimplePie::NAMESPACE_MEDIARSS]['copyright'][0]['attribs']['']['url'])) {
                                    $copyright_url = $this->sanitize($content['child'][\SimplePie\SimplePie::NAMESPACE_MEDIARSS]['copyright'][0]['attribs']['']['url'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                                }
                                if (isset($content['child'][\SimplePie\SimplePie::NAMESPACE_MEDIARSS]['copyright'][0]['data'])) {
                                    $copyright_label = $this->sanitize($content['child'][\SimplePie\SimplePie::NAMESPACE_MEDIARSS]['copyright'][0]['data'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                                }
                                $copyrights = $this->registry->create(Copyright::class, [$copyright_url, $copyright_label]);
                            } elseif (isset($group['child'][\SimplePie\SimplePie::NAMESPACE_MEDIARSS]['copyright'])) {
                                $copyright_url = null;
                                $copyright_label = null;
                                if (isset($group['child'][\SimplePie\SimplePie::NAMESPACE_MEDIARSS]['copyright'][0]['attribs']['']['url'])) {
                                    $copyright_url = $this->sanitize($group['child'][\SimplePie\SimplePie::NAMESPACE_MEDIARSS]['copyright'][0]['attribs']['']['url'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                                }
                                if (isset($group['child'][\SimplePie\SimplePie::NAMESPACE_MEDIARSS]['copyright'][0]['data'])) {
                                    $copyright_label = $this->sanitize($group['child'][\SimplePie\SimplePie::NAMESPACE_MEDIARSS]['copyright'][0]['data'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                                }
                                $copyrights = $this->registry->create(Copyright::class, [$copyright_url, $copyright_label]);
                            } else {
                                $copyrights = $copyrights_parent;
                            }

                            // CREDITS
                            if (isset($content['child'][\SimplePie\SimplePie::NAMESPACE_MEDIARSS]['credit'])) {
                                foreach ($content['child'][\SimplePie\SimplePie::NAMESPACE_MEDIARSS]['credit'] as $credit) {
                                    $credit_role = null;
                                    $credit_scheme = null;
                                    $credit_name = null;
                                    if (isset($credit['attribs']['']['role'])) {
                                        $credit_role = $this->sanitize($credit['attribs']['']['role'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                                    }
                                    if (isset($credit['attribs']['']['scheme'])) {
                                        $credit_scheme = $this->sanitize($credit['attribs']['']['scheme'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                                    } else {
                                        $credit_scheme = 'urn:ebu';
                                    }
                                    if (isset($credit['data'])) {
                                        $credit_name = $this->sanitize($credit['data'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                                    }
                                    $credits[] = $this->registry->create(Credit::class, [$credit_role, $credit_scheme, $credit_name]);
                                }
                                if (is_array($credits)) {
                                    $credits = array_values(array_unique($credits));
                                }
                            } elseif (isset($group['child'][\SimplePie\SimplePie::NAMESPACE_MEDIARSS]['credit'])) {
                                foreach ($group['child'][\SimplePie\SimplePie::NAMESPACE_MEDIARSS]['credit'] as $credit) {
                                    $credit_role = null;
                                    $credit_scheme = null;
                                    $credit_name = null;
                                    if (isset($credit['attribs']['']['role'])) {
                                        $credit_role = $this->sanitize($credit['attribs']['']['role'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                                    }
                                    if (isset($credit['attribs']['']['scheme'])) {
                                        $credit_scheme = $this->sanitize($credit['attribs']['']['scheme'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                                    } else {
                                        $credit_scheme = 'urn:ebu';
                                    }
                                    if (isset($credit['data'])) {
                                        $credit_name = $this->sanitize($credit['data'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                                    }
                                    $credits[] = $this->registry->create(Credit::class, [$credit_role, $credit_scheme, $credit_name]);
                                }
                                if (is_array($credits)) {
                                    $credits = array_values(array_unique($credits));
                                }
                            } else {
                                $credits = $credits_parent;
                            }

                            // DESCRIPTION
                            if (isset($content['child'][\SimplePie\SimplePie::NAMESPACE_MEDIARSS]['description'])) {
                                $description = $this->sanitize($content['child'][\SimplePie\SimplePie::NAMESPACE_MEDIARSS]['description'][0]['data'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                            } elseif (isset($group['child'][\SimplePie\SimplePie::NAMESPACE_MEDIARSS]['description'])) {
                                $description = $this->sanitize($group['child'][\SimplePie\SimplePie::NAMESPACE_MEDIARSS]['description'][0]['data'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                            } else {
                                $description = $description_parent;
                            }

                            // HASHES
                            if (isset($content['child'][\SimplePie\SimplePie::NAMESPACE_MEDIARSS]['hash'])) {
                                foreach ($content['child'][\SimplePie\SimplePie::NAMESPACE_MEDIARSS]['hash'] as $hash) {
                                    $value = null;
                                    $algo = null;
                                    if (isset($hash['data'])) {
                                        $value = $this->sanitize($hash['data'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                                    }
                                    if (isset($hash['attribs']['']['algo'])) {
                                        $algo = $this->sanitize($hash['attribs']['']['algo'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                                    } else {
                                        $algo = 'md5';
                                    }
                                    $hashes[] = $algo.':'.$value;
                                }
                                if (is_array($hashes)) {
                                    $hashes = array_values(array_unique($hashes));
                                }
                            } elseif (isset($group['child'][\SimplePie\SimplePie::NAMESPACE_MEDIARSS]['hash'])) {
                                foreach ($group['child'][\SimplePie\SimplePie::NAMESPACE_MEDIARSS]['hash'] as $hash) {
                                    $value = null;
                                    $algo = null;
                                    if (isset($hash['data'])) {
                                        $value = $this->sanitize($hash['data'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                                    }
                                    if (isset($hash['attribs']['']['algo'])) {
                                        $algo = $this->sanitize($hash['attribs']['']['algo'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                                    } else {
                                        $algo = 'md5';
                                    }
                                    $hashes[] = $algo.':'.$value;
                                }
                                if (is_array($hashes)) {
                                    $hashes = array_values(array_unique($hashes));
                                }
                            } else {
                                $hashes = $hashes_parent;
                            }

                            // KEYWORDS
                            if (isset($content['child'][\SimplePie\SimplePie::NAMESPACE_MEDIARSS]['keywords'])) {
                                if (isset($content['child'][\SimplePie\SimplePie::NAMESPACE_MEDIARSS]['keywords'][0]['data'])) {
                                    $temp = explode(',', $this->sanitize($content['child'][\SimplePie\SimplePie::NAMESPACE_MEDIARSS]['keywords'][0]['data'], \SimplePie\SimplePie::CONSTRUCT_TEXT));
                                    foreach ($temp as $word) {
                                        $keywords[] = trim($word);
                                    }
                                    unset($temp);
                                }
                                if (is_array($keywords)) {
                                    $keywords = array_values(array_unique($keywords));
                                }
                            } elseif (isset($group['child'][\SimplePie\SimplePie::NAMESPACE_MEDIARSS]['keywords'])) {
                                if (isset($group['child'][\SimplePie\SimplePie::NAMESPACE_MEDIARSS]['keywords'][0]['data'])) {
                                    $temp = explode(',', $this->sanitize($group['child'][\SimplePie\SimplePie::NAMESPACE_MEDIARSS]['keywords'][0]['data'], \SimplePie\SimplePie::CONSTRUCT_TEXT));
                                    foreach ($temp as $word) {
                                        $keywords[] = trim($word);
                                    }
                                    unset($temp);
                                }
                                if (is_array($keywords)) {
                                    $keywords = array_values(array_unique($keywords));
                                }
                            } else {
                                $keywords = $keywords_parent;
                            }

                            // PLAYER
                            if (isset($content['child'][\SimplePie\SimplePie::NAMESPACE_MEDIARSS]['player'])) {
                                $playerElem = $content['child'][\SimplePie\SimplePie::NAMESPACE_MEDIARSS]['player'][0];
                                $player = $this->sanitize($playerElem['attribs']['']['url'], \SimplePie\SimplePie::CONSTRUCT_IRI, $this->get_own_base($playerElem));
                            } elseif (isset($group['child'][\SimplePie\SimplePie::NAMESPACE_MEDIARSS]['player'])) {
                                $playerElem = $group['child'][\SimplePie\SimplePie::NAMESPACE_MEDIARSS]['player'][0];
                                $player = $this->sanitize($playerElem['attribs']['']['url'], \SimplePie\SimplePie::CONSTRUCT_IRI, $this->get_own_base($playerElem));
                            } else {
                                $player = $player_parent;
                            }

                            // RATINGS
                            if (isset($content['child'][\SimplePie\SimplePie::NAMESPACE_MEDIARSS]['rating'])) {
                                foreach ($content['child'][\SimplePie\SimplePie::NAMESPACE_MEDIARSS]['rating'] as $rating) {
                                    $rating_scheme = null;
                                    $rating_value = null;
                                    if (isset($rating['attribs']['']['scheme'])) {
                                        $rating_scheme = $this->sanitize($rating['attribs']['']['scheme'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                                    } else {
                                        $rating_scheme = 'urn:simple';
                                    }
                                    if (isset($rating['data'])) {
                                        $rating_value = $this->sanitize($rating['data'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                                    }
                                    $ratings[] = $this->registry->create(Rating::class, [$rating_scheme, $rating_value]);
                                }
                                if (is_array($ratings)) {
                                    $ratings = array_values(array_unique($ratings));
                                }
                            } elseif (isset($group['child'][\SimplePie\SimplePie::NAMESPACE_MEDIARSS]['rating'])) {
                                foreach ($group['child'][\SimplePie\SimplePie::NAMESPACE_MEDIARSS]['rating'] as $rating) {
                                    $rating_scheme = null;
                                    $rating_value = null;
                                    if (isset($rating['attribs']['']['scheme'])) {
                                        $rating_scheme = $this->sanitize($rating['attribs']['']['scheme'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                                    } else {
                                        $rating_scheme = 'urn:simple';
                                    }
                                    if (isset($rating['data'])) {
                                        $rating_value = $this->sanitize($rating['data'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                                    }
                                    $ratings[] = $this->registry->create(Rating::class, [$rating_scheme, $rating_value]);
                                }
                                if (is_array($ratings)) {
                                    $ratings = array_values(array_unique($ratings));
                                }
                            } else {
                                $ratings = $ratings_parent;
                            }

                            // RESTRICTIONS
                            if (isset($content['child'][\SimplePie\SimplePie::NAMESPACE_MEDIARSS]['restriction'])) {
                                foreach ($content['child'][\SimplePie\SimplePie::NAMESPACE_MEDIARSS]['restriction'] as $restriction) {
                                    $restriction_relationship = null;
                                    $restriction_type = null;
                                    $restriction_value = null;
                                    if (isset($restriction['attribs']['']['relationship'])) {
                                        $restriction_relationship = $this->sanitize($restriction['attribs']['']['relationship'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                                    }
                                    if (isset($restriction['attribs']['']['type'])) {
                                        $restriction_type = $this->sanitize($restriction['attribs']['']['type'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                                    }
                                    if (isset($restriction['data'])) {
                                        $restriction_value = $this->sanitize($restriction['data'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                                    }
                                    $restrictions[] = $this->registry->create(Restriction::class, [$restriction_relationship, $restriction_type, $restriction_value]);
                                }
                                if (is_array($restrictions)) {
                                    $restrictions = array_values(array_unique($restrictions));
                                }
                            } elseif (isset($group['child'][\SimplePie\SimplePie::NAMESPACE_MEDIARSS]['restriction'])) {
                                foreach ($group['child'][\SimplePie\SimplePie::NAMESPACE_MEDIARSS]['restriction'] as $restriction) {
                                    $restriction_relationship = null;
                                    $restriction_type = null;
                                    $restriction_value = null;
                                    if (isset($restriction['attribs']['']['relationship'])) {
                                        $restriction_relationship = $this->sanitize($restriction['attribs']['']['relationship'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                                    }
                                    if (isset($restriction['attribs']['']['type'])) {
                                        $restriction_type = $this->sanitize($restriction['attribs']['']['type'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                                    }
                                    if (isset($restriction['data'])) {
                                        $restriction_value = $this->sanitize($restriction['data'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                                    }
                                    $restrictions[] = $this->registry->create(Restriction::class, [$restriction_relationship, $restriction_type, $restriction_value]);
                                }
                                if (is_array($restrictions)) {
                                    $restrictions = array_values(array_unique($restrictions));
                                }
                            } else {
                                $restrictions = $restrictions_parent;
                            }

                            // THUMBNAILS
                            if (isset($content['child'][\SimplePie\SimplePie::NAMESPACE_MEDIARSS]['thumbnail'])) {
                                foreach ($content['child'][\SimplePie\SimplePie::NAMESPACE_MEDIARSS]['thumbnail'] as $thumbnail) {
                                    $thumbnails[] = $this->sanitize($thumbnail['attribs']['']['url'], \SimplePie\SimplePie::CONSTRUCT_IRI, $this->get_own_base($thumbnail));
                                }
                                if (is_array($thumbnails)) {
                                    $thumbnails = array_values(array_unique($thumbnails));
                                }
                            } elseif (isset($group['child'][\SimplePie\SimplePie::NAMESPACE_MEDIARSS]['thumbnail'])) {
                                foreach ($group['child'][\SimplePie\SimplePie::NAMESPACE_MEDIARSS]['thumbnail'] as $thumbnail) {
                                    $thumbnails[] = $this->sanitize($thumbnail['attribs']['']['url'], \SimplePie\SimplePie::CONSTRUCT_IRI, $this->get_own_base($thumbnail));
                                }
                                if (is_array($thumbnails)) {
                                    $thumbnails = array_values(array_unique($thumbnails));
                                }
                            } else {
                                $thumbnails = $thumbnails_parent;
                            }

                            // TITLES
                            if (isset($content['child'][\SimplePie\SimplePie::NAMESPACE_MEDIARSS]['title'])) {
                                $title = $this->sanitize($content['child'][\SimplePie\SimplePie::NAMESPACE_MEDIARSS]['title'][0]['data'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                            } elseif (isset($group['child'][\SimplePie\SimplePie::NAMESPACE_MEDIARSS]['title'])) {
                                $title = $this->sanitize($group['child'][\SimplePie\SimplePie::NAMESPACE_MEDIARSS]['title'][0]['data'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                            } else {
                                $title = $title_parent;
                            }

                            $this->data['enclosures'][] = $this->registry->create(Enclosure::class, [$url, $type, $length, null, $bitrate, $captions, $categories, $channels, $copyrights, $credits, $description, $duration, $expression, $framerate, $hashes, $height, $keywords, $lang, $medium, $player, $ratings, $restrictions, $samplingrate, $thumbnails, $title, $width]);
                        }
                    }
                }
            }

            // If we have standalone media:content tags, loop through them.
            if (isset($this->data['child'][\SimplePie\SimplePie::NAMESPACE_MEDIARSS]['content'])) {
                foreach ((array) $this->data['child'][\SimplePie\SimplePie::NAMESPACE_MEDIARSS]['content'] as $content) {
                    if (isset($content['attribs']['']['url']) || isset($content['child'][\SimplePie\SimplePie::NAMESPACE_MEDIARSS]['player'])) {
                        // Attributes
                        $bitrate = null;
                        $channels = null;
                        $duration = null;
                        $expression = null;
                        $framerate = null;
                        $height = null;
                        $javascript = null;
                        $lang = null;
                        $length = null;
                        $medium = null;
                        $samplingrate = null;
                        $type = null;
                        $url = null;
                        $width = null;

                        // Elements
                        $captions = null;
                        $categories = null;
                        $copyrights = null;
                        $credits = null;
                        $description = null;
                        $hashes = null;
                        $keywords = null;
                        $player = null;
                        $ratings = null;
                        $restrictions = null;
                        $thumbnails = null;
                        $title = null;

                        // Start checking the attributes of media:content
                        if (isset($content['attribs']['']['bitrate'])) {
                            $bitrate = $this->sanitize($content['attribs']['']['bitrate'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                        }
                        if (isset($content['attribs']['']['channels'])) {
                            $channels = $this->sanitize($content['attribs']['']['channels'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                        }
                        if (isset($content['attribs']['']['duration'])) {
                            $duration = $this->sanitize($content['attribs']['']['duration'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                        } else {
                            $duration = $duration_parent;
                        }
                        if (isset($content['attribs']['']['expression'])) {
                            $expression = $this->sanitize($content['attribs']['']['expression'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                        }
                        if (isset($content['attribs']['']['framerate'])) {
                            $framerate = $this->sanitize($content['attribs']['']['framerate'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                        }
                        if (isset($content['attribs']['']['height'])) {
                            $height = $this->sanitize($content['attribs']['']['height'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                        }
                        if (isset($content['attribs']['']['lang'])) {
                            $lang = $this->sanitize($content['attribs']['']['lang'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                        }
                        if (isset($content['attribs']['']['fileSize'])) {
                            $length = intval($content['attribs']['']['fileSize']);
                        }
                        if (isset($content['attribs']['']['medium'])) {
                            $medium = $this->sanitize($content['attribs']['']['medium'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                        }
                        if (isset($content['attribs']['']['samplingrate'])) {
                            $samplingrate = $this->sanitize($content['attribs']['']['samplingrate'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                        }
                        if (isset($content['attribs']['']['type'])) {
                            $type = $this->sanitize($content['attribs']['']['type'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                        }
                        if (isset($content['attribs']['']['width'])) {
                            $width = $this->sanitize($content['attribs']['']['width'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                        }
                        if (isset($content['attribs']['']['url'])) {
                            $url = $this->sanitize($content['attribs']['']['url'], \SimplePie\SimplePie::CONSTRUCT_IRI, $this->get_own_base($content));
                        }
                        // Checking the other optional media: elements. Priority: media:content, media:group, item, channel

                        // CAPTIONS
                        if (isset($content['child'][\SimplePie\SimplePie::NAMESPACE_MEDIARSS]['text'])) {
                            foreach ($content['child'][\SimplePie\SimplePie::NAMESPACE_MEDIARSS]['text'] as $caption) {
                                $caption_type = null;
                                $caption_lang = null;
                                $caption_startTime = null;
                                $caption_endTime = null;
                                $caption_text = null;
                                if (isset($caption['attribs']['']['type'])) {
                                    $caption_type = $this->sanitize($caption['attribs']['']['type'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                                }
                                if (isset($caption['attribs']['']['lang'])) {
                                    $caption_lang = $this->sanitize($caption['attribs']['']['lang'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                                }
                                if (isset($caption['attribs']['']['start'])) {
                                    $caption_startTime = $this->sanitize($caption['attribs']['']['start'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                                }
                                if (isset($caption['attribs']['']['end'])) {
                                    $caption_endTime = $this->sanitize($caption['attribs']['']['end'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                                }
                                if (isset($caption['data'])) {
                                    $caption_text = $this->sanitize($caption['data'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                                }
                                $captions[] = $this->registry->create(Caption::class, [$caption_type, $caption_lang, $caption_startTime, $caption_endTime, $caption_text]);
                            }
                            if (is_array($captions)) {
                                $captions = array_values(array_unique($captions));
                            }
                        } else {
                            $captions = $captions_parent;
                        }

                        // CATEGORIES
                        if (isset($content['child'][\SimplePie\SimplePie::NAMESPACE_MEDIARSS]['category'])) {
                            foreach ((array) $content['child'][\SimplePie\SimplePie::NAMESPACE_MEDIARSS]['category'] as $category) {
                                $term = null;
                                $scheme = null;
                                $label = null;
                                if (isset($category['data'])) {
                                    $term = $this->sanitize($category['data'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                                }
                                if (isset($category['attribs']['']['scheme'])) {
                                    $scheme = $this->sanitize($category['attribs']['']['scheme'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                                } else {
                                    $scheme = 'http://search.yahoo.com/mrss/category_schema';
                                }
                                if (isset($category['attribs']['']['label'])) {
                                    $label = $this->sanitize($category['attribs']['']['label'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                                }
                                $categories[] = $this->registry->create(Category::class, [$term, $scheme, $label]);
                            }
                        }
                        if (is_array($categories) && is_array($categories_parent)) {
                            $categories = array_values(array_unique(array_merge($categories, $categories_parent)));
                        } elseif (is_array($categories)) {
                            $categories = array_values(array_unique($categories));
                        } elseif (is_array($categories_parent)) {
                            $categories = array_values(array_unique($categories_parent));
                        } else {
                            $categories = null;
                        }

                        // COPYRIGHTS
                        if (isset($content['child'][\SimplePie\SimplePie::NAMESPACE_MEDIARSS]['copyright'])) {
                            $copyright_url = null;
                            $copyright_label = null;
                            if (isset($content['child'][\SimplePie\SimplePie::NAMESPACE_MEDIARSS]['copyright'][0]['attribs']['']['url'])) {
                                $copyright_url = $this->sanitize($content['child'][\SimplePie\SimplePie::NAMESPACE_MEDIARSS]['copyright'][0]['attribs']['']['url'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                            }
                            if (isset($content['child'][\SimplePie\SimplePie::NAMESPACE_MEDIARSS]['copyright'][0]['data'])) {
                                $copyright_label = $this->sanitize($content['child'][\SimplePie\SimplePie::NAMESPACE_MEDIARSS]['copyright'][0]['data'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                            }
                            $copyrights = $this->registry->create(Copyright::class, [$copyright_url, $copyright_label]);
                        } else {
                            $copyrights = $copyrights_parent;
                        }

                        // CREDITS
                        if (isset($content['child'][\SimplePie\SimplePie::NAMESPACE_MEDIARSS]['credit'])) {
                            foreach ($content['child'][\SimplePie\SimplePie::NAMESPACE_MEDIARSS]['credit'] as $credit) {
                                $credit_role = null;
                                $credit_scheme = null;
                                $credit_name = null;
                                if (isset($credit['attribs']['']['role'])) {
                                    $credit_role = $this->sanitize($credit['attribs']['']['role'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                                }
                                if (isset($credit['attribs']['']['scheme'])) {
                                    $credit_scheme = $this->sanitize($credit['attribs']['']['scheme'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                                } else {
                                    $credit_scheme = 'urn:ebu';
                                }
                                if (isset($credit['data'])) {
                                    $credit_name = $this->sanitize($credit['data'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                                }
                                $credits[] = $this->registry->create(Credit::class, [$credit_role, $credit_scheme, $credit_name]);
                            }
                            if (is_array($credits)) {
                                $credits = array_values(array_unique($credits));
                            }
                        } else {
                            $credits = $credits_parent;
                        }

                        // DESCRIPTION
                        if (isset($content['child'][\SimplePie\SimplePie::NAMESPACE_MEDIARSS]['description'])) {
                            $description = $this->sanitize($content['child'][\SimplePie\SimplePie::NAMESPACE_MEDIARSS]['description'][0]['data'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                        } else {
                            $description = $description_parent;
                        }

                        // HASHES
                        if (isset($content['child'][\SimplePie\SimplePie::NAMESPACE_MEDIARSS]['hash'])) {
                            foreach ($content['child'][\SimplePie\SimplePie::NAMESPACE_MEDIARSS]['hash'] as $hash) {
                                $value = null;
                                $algo = null;
                                if (isset($hash['data'])) {
                                    $value = $this->sanitize($hash['data'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                                }
                                if (isset($hash['attribs']['']['algo'])) {
                                    $algo = $this->sanitize($hash['attribs']['']['algo'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                                } else {
                                    $algo = 'md5';
                                }
                                $hashes[] = $algo.':'.$value;
                            }
                            if (is_array($hashes)) {
                                $hashes = array_values(array_unique($hashes));
                            }
                        } else {
                            $hashes = $hashes_parent;
                        }

                        // KEYWORDS
                        if (isset($content['child'][\SimplePie\SimplePie::NAMESPACE_MEDIARSS]['keywords'])) {
                            if (isset($content['child'][\SimplePie\SimplePie::NAMESPACE_MEDIARSS]['keywords'][0]['data'])) {
                                $temp = explode(',', $this->sanitize($content['child'][\SimplePie\SimplePie::NAMESPACE_MEDIARSS]['keywords'][0]['data'], \SimplePie\SimplePie::CONSTRUCT_TEXT));
                                foreach ($temp as $word) {
                                    $keywords[] = trim($word);
                                }
                                unset($temp);
                            }
                            if (is_array($keywords)) {
                                $keywords = array_values(array_unique($keywords));
                            }
                        } else {
                            $keywords = $keywords_parent;
                        }

                        // PLAYER
                        if (isset($content['child'][\SimplePie\SimplePie::NAMESPACE_MEDIARSS]['player'])) {
                            if (isset($content['child'][\SimplePie\SimplePie::NAMESPACE_MEDIARSS]['player'][0]['attribs']['']['url'])) {
                                $playerElem = $content['child'][\SimplePie\SimplePie::NAMESPACE_MEDIARSS]['player'][0];
                                $player = $this->sanitize($playerElem['attribs']['']['url'], \SimplePie\SimplePie::CONSTRUCT_IRI, $this->get_own_base($playerElem));
                            }
                        } else {
                            $player = $player_parent;
                        }

                        // RATINGS
                        if (isset($content['child'][\SimplePie\SimplePie::NAMESPACE_MEDIARSS]['rating'])) {
                            foreach ($content['child'][\SimplePie\SimplePie::NAMESPACE_MEDIARSS]['rating'] as $rating) {
                                $rating_scheme = null;
                                $rating_value = null;
                                if (isset($rating['attribs']['']['scheme'])) {
                                    $rating_scheme = $this->sanitize($rating['attribs']['']['scheme'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                                } else {
                                    $rating_scheme = 'urn:simple';
                                }
                                if (isset($rating['data'])) {
                                    $rating_value = $this->sanitize($rating['data'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                                }
                                $ratings[] = $this->registry->create(Rating::class, [$rating_scheme, $rating_value]);
                            }
                            if (is_array($ratings)) {
                                $ratings = array_values(array_unique($ratings));
                            }
                        } else {
                            $ratings = $ratings_parent;
                        }

                        // RESTRICTIONS
                        if (isset($content['child'][\SimplePie\SimplePie::NAMESPACE_MEDIARSS]['restriction'])) {
                            foreach ($content['child'][\SimplePie\SimplePie::NAMESPACE_MEDIARSS]['restriction'] as $restriction) {
                                $restriction_relationship = null;
                                $restriction_type = null;
                                $restriction_value = null;
                                if (isset($restriction['attribs']['']['relationship'])) {
                                    $restriction_relationship = $this->sanitize($restriction['attribs']['']['relationship'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                                }
                                if (isset($restriction['attribs']['']['type'])) {
                                    $restriction_type = $this->sanitize($restriction['attribs']['']['type'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                                }
                                if (isset($restriction['data'])) {
                                    $restriction_value = $this->sanitize($restriction['data'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                                }
                                $restrictions[] = $this->registry->create(Restriction::class, [$restriction_relationship, $restriction_type, $restriction_value]);
                            }
                            if (is_array($restrictions)) {
                                $restrictions = array_values(array_unique($restrictions));
                            }
                        } else {
                            $restrictions = $restrictions_parent;
                        }

                        // THUMBNAILS
                        if (isset($content['child'][\SimplePie\SimplePie::NAMESPACE_MEDIARSS]['thumbnail'])) {
                            foreach ($content['child'][\SimplePie\SimplePie::NAMESPACE_MEDIARSS]['thumbnail'] as $thumbnail) {
                                if (isset($thumbnail['attribs']['']['url'])) {
                                    $thumbnails[] = $this->sanitize($thumbnail['attribs']['']['url'], \SimplePie\SimplePie::CONSTRUCT_IRI, $this->get_own_base($thumbnail));
                                }
                            }
                            if (is_array($thumbnails)) {
                                $thumbnails = array_values(array_unique($thumbnails));
                            }
                        } else {
                            $thumbnails = $thumbnails_parent;
                        }

                        // TITLES
                        if (isset($content['child'][\SimplePie\SimplePie::NAMESPACE_MEDIARSS]['title'])) {
                            $title = $this->sanitize($content['child'][\SimplePie\SimplePie::NAMESPACE_MEDIARSS]['title'][0]['data'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                        } else {
                            $title = $title_parent;
                        }

                        $this->data['enclosures'][] = $this->registry->create(Enclosure::class, [$url, $type, $length, null, $bitrate, $captions, $categories, $channels, $copyrights, $credits, $description, $duration, $expression, $framerate, $hashes, $height, $keywords, $lang, $medium, $player, $ratings, $restrictions, $samplingrate, $thumbnails, $title, $width]);
                    }
                }
            }

            foreach ((array) $this->get_item_tags(\SimplePie\SimplePie::NAMESPACE_ATOM_10, 'link') as $link) {
                if (isset($link['attribs']['']['href']) && !empty($link['attribs']['']['rel']) && $link['attribs']['']['rel'] === 'enclosure') {
                    // Attributes
                    $bitrate = null;
                    $channels = null;
                    $duration = null;
                    $expression = null;
                    $framerate = null;
                    $height = null;
                    $javascript = null;
                    $lang = null;
                    $length = null;
                    $medium = null;
                    $samplingrate = null;
                    $type = null;
                    $url = null;
                    $width = null;

                    $url = $this->sanitize($link['attribs']['']['href'], \SimplePie\SimplePie::CONSTRUCT_IRI, $this->get_own_base($link));
                    if (isset($link['attribs']['']['type'])) {
                        $type = $this->sanitize($link['attribs']['']['type'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                    }
                    if (isset($link['attribs']['']['length'])) {
                        $length = intval($link['attribs']['']['length']);
                    }
                    if (isset($link['attribs']['']['title'])) {
                        $title = $this->sanitize($link['attribs']['']['title'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                    } else {
                        $title = $title_parent;
                    }

                    // Since we don't have group or content for these, we'll just pass the '*_parent' variables directly to the constructor
                    $this->data['enclosures'][] = $this->registry->create(Enclosure::class, [$url, $type, $length, null, $bitrate, $captions_parent, $categories_parent, $channels, $copyrights_parent, $credits_parent, $description_parent, $duration_parent, $expression, $framerate, $hashes_parent, $height, $keywords_parent, $lang, $medium, $player_parent, $ratings_parent, $restrictions_parent, $samplingrate, $thumbnails_parent, $title, $width]);
                }
            }

            foreach ((array) $this->get_item_tags(\SimplePie\SimplePie::NAMESPACE_ATOM_03, 'link') as $link) {
                if (isset($link['attribs']['']['href']) && !empty($link['attribs']['']['rel']) && $link['attribs']['']['rel'] === 'enclosure') {
                    // Attributes
                    $bitrate = null;
                    $channels = null;
                    $duration = null;
                    $expression = null;
                    $framerate = null;
                    $height = null;
                    $javascript = null;
                    $lang = null;
                    $length = null;
                    $medium = null;
                    $samplingrate = null;
                    $type = null;
                    $url = null;
                    $width = null;

                    $url = $this->sanitize($link['attribs']['']['href'], \SimplePie\SimplePie::CONSTRUCT_IRI, $this->get_own_base($link));
                    if (isset($link['attribs']['']['type'])) {
                        $type = $this->sanitize($link['attribs']['']['type'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                    }
                    if (isset($link['attribs']['']['length'])) {
                        $length = intval($link['attribs']['']['length']);
                    }

                    // Since we don't have group or content for these, we'll just pass the '*_parent' variables directly to the constructor
                    $this->data['enclosures'][] = $this->registry->create(Enclosure::class, [$url, $type, $length, null, $bitrate, $captions_parent, $categories_parent, $channels, $copyrights_parent, $credits_parent, $description_parent, $duration_parent, $expression, $framerate, $hashes_parent, $height, $keywords_parent, $lang, $medium, $player_parent, $ratings_parent, $restrictions_parent, $samplingrate, $thumbnails_parent, $title_parent, $width]);
                }
            }

            foreach ($this->get_item_tags(\SimplePie\SimplePie::NAMESPACE_RSS_20, 'enclosure') ?? [] as $enclosure) {
                if (isset($enclosure['attribs']['']['url'])) {
                    // Attributes
                    $bitrate = null;
                    $channels = null;
                    $duration = null;
                    $expression = null;
                    $framerate = null;
                    $height = null;
                    $javascript = null;
                    $lang = null;
                    $length = null;
                    $medium = null;
                    $samplingrate = null;
                    $type = null;
                    $url = null;
                    $width = null;

                    $url = $this->sanitize($enclosure['attribs']['']['url'], \SimplePie\SimplePie::CONSTRUCT_IRI, $this->get_own_base($enclosure));
                    $url = $this->get_sanitize()->https_url($url);
                    if (isset($enclosure['attribs']['']['type'])) {
                        $type = $this->sanitize($enclosure['attribs']['']['type'], \SimplePie\SimplePie::CONSTRUCT_TEXT);
                    }
                    if (isset($enclosure['attribs']['']['length'])) {
                        $length = intval($enclosure['attribs']['']['length']);
                    }

                    // Since we don't have group or content for these, we'll just pass the '*_parent' variables directly to the constructor
                    $this->data['enclosures'][] = $this->registry->create(Enclosure::class, [$url, $type, $length, null, $bitrate, $captions_parent, $categories_parent, $channels, $copyrights_parent, $credits_parent, $description_parent, $duration_parent, $expression, $framerate, $hashes_parent, $height, $keywords_parent, $lang, $medium, $player_parent, $ratings_parent, $restrictions_parent, $samplingrate, $thumbnails_parent, $title_parent, $width]);
                }
            }

            if (count($this->data['enclosures']) === 0 && ($url || $type || $length || $bitrate || $captions_parent || $categories_parent || $channels || $copyrights_parent || $credits_parent || $description_parent || $duration_parent || $expression || $framerate || $hashes_parent || $height || $keywords_parent || $lang || $medium || $player_parent || $ratings_parent || $samplingrate || $thumbnails_parent || $title_parent || $width)) {
                // Since we don't have group or content for these, we'll just pass the '*_parent' variables directly to the constructor
                $this->data['enclosures'][] = $this->registry->create(Enclosure::class, [$url, $type, $length, null, $bitrate, $captions_parent, $categories_parent, $channels, $copyrights_parent, $credits_parent, $description_parent, $duration_parent, $expression, $framerate, $hashes_parent, $height, $keywords_parent, $lang, $medium, $player_parent, $ratings_parent, $restrictions_parent, $samplingrate, $thumbnails_parent, $title_parent, $width]);
            }

            $this->data['enclosures'] = array_values(array_unique($this->data['enclosures']));
        }
        if (!empty($this->data['enclosures'])) {
            return $this->data['enclosures'];
        }

        return null;
    }

    /**
     * Get the latitude coordinates for the item
     *
     * Compatible with the W3C WGS84 Basic Geo and GeoRSS specifications
     *
     * Uses `<geo:lat>` or `<georss:point>`
     *
     * @since 1.0
     * @link http://www.w3.org/2003/01/geo/ W3C WGS84 Basic Geo
     * @link http://www.georss.org/ GeoRSS
     * @return float|null
     */
    public function get_latitude()
    {
        if ($return = $this->get_item_tags(\SimplePie\SimplePie::NAMESPACE_W3C_BASIC_GEO, 'lat')) {
            return (float) $return[0]['data'];
        } elseif (($return = $this->get_item_tags(\SimplePie\SimplePie::NAMESPACE_GEORSS, 'point')) && preg_match('/^((?:-)?[0-9]+(?:\.[0-9]+)) ((?:-)?[0-9]+(?:\.[0-9]+))$/', trim($return[0]['data']), $match)) {
            return (float) $match[1];
        }

        return null;
    }

    /**
     * Get the longitude coordinates for the item
     *
     * Compatible with the W3C WGS84 Basic Geo and GeoRSS specifications
     *
     * Uses `<geo:long>`, `<geo:lon>` or `<georss:point>`
     *
     * @since 1.0
     * @link http://www.w3.org/2003/01/geo/ W3C WGS84 Basic Geo
     * @link http://www.georss.org/ GeoRSS
     * @return float|null
     */
    public function get_longitude()
    {
        if ($return = $this->get_item_tags(\SimplePie\SimplePie::NAMESPACE_W3C_BASIC_GEO, 'long')) {
            return (float) $return[0]['data'];
        } elseif ($return = $this->get_item_tags(\SimplePie\SimplePie::NAMESPACE_W3C_BASIC_GEO, 'lon')) {
            return (float) $return[0]['data'];
        } elseif (($return = $this->get_item_tags(\SimplePie\SimplePie::NAMESPACE_GEORSS, 'point')) && preg_match('/^((?:-)?[0-9]+(?:\.[0-9]+)) ((?:-)?[0-9]+(?:\.[0-9]+))$/', trim($return[0]['data']), $match)) {
            return (float) $match[2];
        }

        return null;
    }

    /**
     * Get the `<atom:source>` for the item
     *
     * @since 1.1
     * @return \SimplePie\Source|null
     */
    public function get_source()
    {
        if ($return = $this->get_item_tags(\SimplePie\SimplePie::NAMESPACE_ATOM_10, 'source')) {
            return $this->registry->create(Source::class, [$this, $return[0]]);
        }

        return null;
    }

    public function set_sanitize(Sanitize $sanitize): void
    {
        $this->sanitize = $sanitize;
    }

    protected function get_sanitize(): Sanitize
    {
        if ($this->sanitize === null) {
            $this->sanitize = new Sanitize();
        }

        return $this->sanitize;
    }
}

class_alias('SimplePie\Item', 'SimplePie_Item');
