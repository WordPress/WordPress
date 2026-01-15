<?php

// SPDX-FileCopyrightText: 2004-2023 Ryan Parman, Sam Sneddon, Ryan McCue
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

namespace SimplePie;

use InvalidArgumentException;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;
use Psr\SimpleCache\CacheInterface;
use SimplePie\Cache\Base;
use SimplePie\Cache\BaseDataCache;
use SimplePie\Cache\CallableNameFilter;
use SimplePie\Cache\DataCache;
use SimplePie\Cache\NameFilter;
use SimplePie\Cache\Psr16;
use SimplePie\Content\Type\Sniffer;
use SimplePie\Exception as SimplePieException;
use SimplePie\HTTP\Client;
use SimplePie\HTTP\ClientException;
use SimplePie\HTTP\FileClient;
use SimplePie\HTTP\Psr18Client;
use SimplePie\HTTP\Response;

/**
 * SimplePie
 */
class SimplePie
{
    /**
     * SimplePie Name
     */
    public const NAME = 'SimplePie';

    /**
     * SimplePie Version
     */
    public const VERSION = '1.9.0';

    /**
     * SimplePie Website URL
     */
    public const URL = 'http://simplepie.org';

    /**
     * SimplePie Linkback
     */
    public const LINKBACK = '<a href="' . self::URL . '" title="' . self::NAME . ' ' . self::VERSION . '">' . self::NAME . '</a>';

    /**
     * No Autodiscovery
     * @see SimplePie::set_autodiscovery_level()
     */
    public const LOCATOR_NONE = 0;

    /**
     * Feed Link Element Autodiscovery
     * @see SimplePie::set_autodiscovery_level()
     */
    public const LOCATOR_AUTODISCOVERY = 1;

    /**
     * Local Feed Extension Autodiscovery
     * @see SimplePie::set_autodiscovery_level()
     */
    public const LOCATOR_LOCAL_EXTENSION = 2;

    /**
     * Local Feed Body Autodiscovery
     * @see SimplePie::set_autodiscovery_level()
     */
    public const LOCATOR_LOCAL_BODY = 4;

    /**
     * Remote Feed Extension Autodiscovery
     * @see SimplePie::set_autodiscovery_level()
     */
    public const LOCATOR_REMOTE_EXTENSION = 8;

    /**
     * Remote Feed Body Autodiscovery
     * @see SimplePie::set_autodiscovery_level()
     */
    public const LOCATOR_REMOTE_BODY = 16;

    /**
     * All Feed Autodiscovery
     * @see SimplePie::set_autodiscovery_level()
     */
    public const LOCATOR_ALL = 31;

    /**
     * No known feed type
     */
    public const TYPE_NONE = 0;

    /**
     * RSS 0.90
     */
    public const TYPE_RSS_090 = 1;

    /**
     * RSS 0.91 (Netscape)
     */
    public const TYPE_RSS_091_NETSCAPE = 2;

    /**
     * RSS 0.91 (Userland)
     */
    public const TYPE_RSS_091_USERLAND = 4;

    /**
     * RSS 0.91 (both Netscape and Userland)
     */
    public const TYPE_RSS_091 = 6;

    /**
     * RSS 0.92
     */
    public const TYPE_RSS_092 = 8;

    /**
     * RSS 0.93
     */
    public const TYPE_RSS_093 = 16;

    /**
     * RSS 0.94
     */
    public const TYPE_RSS_094 = 32;

    /**
     * RSS 1.0
     */
    public const TYPE_RSS_10 = 64;

    /**
     * RSS 2.0
     */
    public const TYPE_RSS_20 = 128;

    /**
     * RDF-based RSS
     */
    public const TYPE_RSS_RDF = 65;

    /**
     * Non-RDF-based RSS (truly intended as syndication format)
     */
    public const TYPE_RSS_SYNDICATION = 190;

    /**
     * All RSS
     */
    public const TYPE_RSS_ALL = 255;

    /**
     * Atom 0.3
     */
    public const TYPE_ATOM_03 = 256;

    /**
     * Atom 1.0
     */
    public const TYPE_ATOM_10 = 512;

    /**
     * All Atom
     */
    public const TYPE_ATOM_ALL = 768;

    /**
     * All feed types
     */
    public const TYPE_ALL = 1023;

    /**
     * No construct
     */
    public const CONSTRUCT_NONE = 0;

    /**
     * Text construct
     */
    public const CONSTRUCT_TEXT = 1;

    /**
     * HTML construct
     */
    public const CONSTRUCT_HTML = 2;

    /**
     * XHTML construct
     */
    public const CONSTRUCT_XHTML = 4;

    /**
     * base64-encoded construct
     */
    public const CONSTRUCT_BASE64 = 8;

    /**
     * IRI construct
     */
    public const CONSTRUCT_IRI = 16;

    /**
     * A construct that might be HTML
     */
    public const CONSTRUCT_MAYBE_HTML = 32;

    /**
     * All constructs
     */
    public const CONSTRUCT_ALL = 63;

    /**
     * Don't change case
     */
    public const SAME_CASE = 1;

    /**
     * Change to lowercase
     */
    public const LOWERCASE = 2;

    /**
     * Change to uppercase
     */
    public const UPPERCASE = 4;

    /**
     * PCRE for HTML attributes
     */
    public const PCRE_HTML_ATTRIBUTE = '((?:[\x09\x0A\x0B\x0C\x0D\x20]+[^\x09\x0A\x0B\x0C\x0D\x20\x2F\x3E][^\x09\x0A\x0B\x0C\x0D\x20\x2F\x3D\x3E]*(?:[\x09\x0A\x0B\x0C\x0D\x20]*=[\x09\x0A\x0B\x0C\x0D\x20]*(?:"(?:[^"]*)"|\'(?:[^\']*)\'|(?:[^\x09\x0A\x0B\x0C\x0D\x20\x22\x27\x3E][^\x09\x0A\x0B\x0C\x0D\x20\x3E]*)?))?)*)[\x09\x0A\x0B\x0C\x0D\x20]*';

    /**
     * PCRE for XML attributes
     */
    public const PCRE_XML_ATTRIBUTE = '((?:\s+(?:(?:[^\s:]+:)?[^\s:]+)\s*=\s*(?:"(?:[^"]*)"|\'(?:[^\']*)\'))*)\s*';

    /**
     * XML Namespace
     */
    public const NAMESPACE_XML = 'http://www.w3.org/XML/1998/namespace';

    /**
     * Atom 1.0 Namespace
     */
    public const NAMESPACE_ATOM_10 = 'http://www.w3.org/2005/Atom';

    /**
     * Atom 0.3 Namespace
     */
    public const NAMESPACE_ATOM_03 = 'http://purl.org/atom/ns#';

    /**
     * RDF Namespace
     */
    public const NAMESPACE_RDF = 'http://www.w3.org/1999/02/22-rdf-syntax-ns#';

    /**
     * RSS 0.90 Namespace
     */
    public const NAMESPACE_RSS_090 = 'http://my.netscape.com/rdf/simple/0.9/';

    /**
     * RSS 1.0 Namespace
     */
    public const NAMESPACE_RSS_10 = 'http://purl.org/rss/1.0/';

    /**
     * RSS 1.0 Content Module Namespace
     */
    public const NAMESPACE_RSS_10_MODULES_CONTENT = 'http://purl.org/rss/1.0/modules/content/';

    /**
     * RSS 2.0 Namespace
     * (Stupid, I know, but I'm certain it will confuse people less with support.)
     */
    public const NAMESPACE_RSS_20 = '';

    /**
     * DC 1.0 Namespace
     */
    public const NAMESPACE_DC_10 = 'http://purl.org/dc/elements/1.0/';

    /**
     * DC 1.1 Namespace
     */
    public const NAMESPACE_DC_11 = 'http://purl.org/dc/elements/1.1/';

    /**
     * W3C Basic Geo (WGS84 lat/long) Vocabulary Namespace
     */
    public const NAMESPACE_W3C_BASIC_GEO = 'http://www.w3.org/2003/01/geo/wgs84_pos#';

    /**
     * GeoRSS Namespace
     */
    public const NAMESPACE_GEORSS = 'http://www.georss.org/georss';

    /**
     * Media RSS Namespace
     */
    public const NAMESPACE_MEDIARSS = 'http://search.yahoo.com/mrss/';

    /**
     * Wrong Media RSS Namespace. Caused by a long-standing typo in the spec.
     */
    public const NAMESPACE_MEDIARSS_WRONG = 'http://search.yahoo.com/mrss';

    /**
     * Wrong Media RSS Namespace #2. New namespace introduced in Media RSS 1.5.
     */
    public const NAMESPACE_MEDIARSS_WRONG2 = 'http://video.search.yahoo.com/mrss';

    /**
     * Wrong Media RSS Namespace #3. A possible typo of the Media RSS 1.5 namespace.
     */
    public const NAMESPACE_MEDIARSS_WRONG3 = 'http://video.search.yahoo.com/mrss/';

    /**
     * Wrong Media RSS Namespace #4. New spec location after the RSS Advisory Board takes it over, but not a valid namespace.
     */
    public const NAMESPACE_MEDIARSS_WRONG4 = 'http://www.rssboard.org/media-rss';

    /**
     * Wrong Media RSS Namespace #5. A possible typo of the RSS Advisory Board URL.
     */
    public const NAMESPACE_MEDIARSS_WRONG5 = 'http://www.rssboard.org/media-rss/';

    /**
     * iTunes RSS Namespace
     */
    public const NAMESPACE_ITUNES = 'http://www.itunes.com/dtds/podcast-1.0.dtd';

    /**
     * XHTML Namespace
     */
    public const NAMESPACE_XHTML = 'http://www.w3.org/1999/xhtml';

    /**
     * IANA Link Relations Registry
     */
    public const IANA_LINK_RELATIONS_REGISTRY = 'http://www.iana.org/assignments/relation/';

    /**
     * No file source
     */
    public const FILE_SOURCE_NONE = 0;

    /**
     * Remote file source
     */
    public const FILE_SOURCE_REMOTE = 1;

    /**
     * Local file source
     */
    public const FILE_SOURCE_LOCAL = 2;

    /**
     * fsockopen() file source
     */
    public const FILE_SOURCE_FSOCKOPEN = 4;

    /**
     * cURL file source
     */
    public const FILE_SOURCE_CURL = 8;

    /**
     * file_get_contents() file source
     */
    public const FILE_SOURCE_FILE_GET_CONTENTS = 16;

    /**
     * @internal Default value of the HTTP Accept header when fetching/locating feeds
     */
    public const DEFAULT_HTTP_ACCEPT_HEADER = 'application/atom+xml, application/rss+xml, application/rdf+xml;q=0.9, application/xml;q=0.8, text/xml;q=0.8, text/html;q=0.7, unknown/unknown;q=0.1, application/unknown;q=0.1, */*;q=0.1';

    /**
     * @var array<string, mixed> Raw data
     * @access private
     */
    public $data = [];

    /**
     * @var string|string[]|null Error string (or array when multiple feeds are initialized)
     * @access private
     */
    public $error = null;

    /**
     * @var int HTTP status code
     * @see SimplePie::status_code()
     * @access private
     */
    public $status_code = 0;

    /**
     * @var Sanitize instance of Sanitize class
     * @see SimplePie::set_sanitize_class()
     * @access private
     */
    public $sanitize;

    /**
     * @var string SimplePie Useragent
     * @see SimplePie::set_useragent()
     * @access private
     */
    public $useragent = '';

    /**
     * @var string Feed URL
     * @see SimplePie::set_feed_url()
     * @access private
     */
    public $feed_url;

    /**
     * @var ?string Original feed URL, or new feed URL iff HTTP 301 Moved Permanently
     * @see SimplePie::subscribe_url()
     * @access private
     */
    public $permanent_url = null;

    /**
     * @var File Instance of File class to use as a feed
     * @see SimplePie::set_file()
     */
    private $file;

    /**
     * @var string|false Raw feed data
     * @see SimplePie::set_raw_data()
     * @access private
     */
    public $raw_data;

    /**
     * @var int Timeout for fetching remote files
     * @see SimplePie::set_timeout()
     * @access private
     */
    public $timeout = 10;

    /**
     * @var array<int, mixed> Custom curl options
     * @see SimplePie::set_curl_options()
     * @access private
     */
    public $curl_options = [];

    /**
     * @var bool Forces fsockopen() to be used for remote files instead
     * of cURL, even if a new enough version is installed
     * @see SimplePie::force_fsockopen()
     * @access private
     */
    public $force_fsockopen = false;

    /**
     * @var bool Force the given data/URL to be treated as a feed no matter what
     * it appears like
     * @see SimplePie::force_feed()
     * @access private
     */
    public $force_feed = false;

    /**
     * @var bool Enable/Disable Caching
     * @see SimplePie::enable_cache()
     * @access private
     */
    private $enable_cache = true;

    /**
     * @var DataCache|null
     * @see SimplePie::set_cache()
     */
    private $cache = null;

    /**
     * @var NameFilter
     * @see SimplePie::set_cache_namefilter()
     */
    private $cache_namefilter;

    /**
     * @var bool Force SimplePie to fallback to expired cache, if enabled,
     * when feed is unavailable.
     * @see SimplePie::force_cache_fallback()
     * @access private
     */
    public $force_cache_fallback = false;

    /**
     * @var int Cache duration (in seconds)
     * @see SimplePie::set_cache_duration()
     * @access private
     */
    public $cache_duration = 3600;

    /**
     * @var int Auto-discovery cache duration (in seconds)
     * @see SimplePie::set_autodiscovery_cache_duration()
     * @access private
     */
    public $autodiscovery_cache_duration = 604800; // 7 Days.

    /**
     * @var string Cache location (relative to executing script)
     * @see SimplePie::set_cache_location()
     * @access private
     */
    public $cache_location = './cache';

    /**
     * @var string&(callable(string): string) Function that creates the cache filename
     * @see SimplePie::set_cache_name_function()
     * @access private
     */
    public $cache_name_function = 'md5';

    /**
     * @var bool Reorder feed by date descending
     * @see SimplePie::enable_order_by_date()
     * @access private
     */
    public $order_by_date = true;

    /**
     * @var mixed Force input encoding to be set to the follow value
     * (false, or anything type-cast to false, disables this feature)
     * @see SimplePie::set_input_encoding()
     * @access private
     */
    public $input_encoding = false;

    /**
     * @var self::LOCATOR_* Feed Autodiscovery Level
     * @see SimplePie::set_autodiscovery_level()
     * @access private
     */
    public $autodiscovery = self::LOCATOR_ALL;

    /**
     * Class registry object
     *
     * @var Registry
     */
    public $registry;

    /**
     * @var int Maximum number of feeds to check with autodiscovery
     * @see SimplePie::set_max_checked_feeds()
     * @access private
     */
    public $max_checked_feeds = 10;

    /**
     * @var array<Response>|null All the feeds found during the autodiscovery process
     * @see SimplePie::get_all_discovered_feeds()
     * @access private
     */
    public $all_discovered_feeds = [];

    /**
     * @var string Web-accessible path to the handler_image.php file.
     * @see SimplePie::set_image_handler()
     * @access private
     */
    public $image_handler = '';

    /**
     * @var array<string> Stores the URLs when multiple feeds are being initialized.
     * @see SimplePie::set_feed_url()
     * @access private
     */
    public $multifeed_url = [];

    /**
     * @var array<int, static> Stores SimplePie objects when multiple feeds initialized.
     * @access private
     */
    public $multifeed_objects = [];

    /**
     * @var array<mixed> Stores the get_object_vars() array for use with multifeeds.
     * @see SimplePie::set_feed_url()
     * @access private
     */
    public $config_settings = null;

    /**
     * @var int Stores the number of items to return per-feed with multifeeds.
     * @see SimplePie::set_item_limit()
     * @access private
     */
    public $item_limit = 0;

    /**
     * @var bool Stores if last-modified and/or etag headers were sent with the
     * request when checking a feed.
     */
    public $check_modified = false;

    /**
     * @var array<string> Stores the default attributes to be stripped by strip_attributes().
     * @see SimplePie::strip_attributes()
     * @access private
     */
    public $strip_attributes = ['bgsound', 'class', 'expr', 'id', 'style', 'onclick', 'onerror', 'onfinish', 'onmouseover', 'onmouseout', 'onfocus', 'onblur', 'lowsrc', 'dynsrc'];

    /**
     * @var array<string, array<string, string>> Stores the default attributes to add to different tags by add_attributes().
     * @see SimplePie::add_attributes()
     * @access private
     */
    public $add_attributes = ['audio' => ['preload' => 'none'], 'iframe' => ['sandbox' => 'allow-scripts allow-same-origin'], 'video' => ['preload' => 'none']];

    /**
     * @var array<string> Stores the default tags to be stripped by strip_htmltags().
     * @see SimplePie::strip_htmltags()
     * @access private
     */
    public $strip_htmltags = ['base', 'blink', 'body', 'doctype', 'embed', 'font', 'form', 'frame', 'frameset', 'html', 'iframe', 'input', 'marquee', 'meta', 'noscript', 'object', 'param', 'script', 'style'];

    /**
     * @var string[]|string Stores the default attributes to be renamed by rename_attributes().
     * @see SimplePie::rename_attributes()
     * @access private
     */
    public $rename_attributes = [];

    /**
     * @var bool Should we throw exceptions, or use the old-style error property?
     * @access private
     */
    public $enable_exceptions = false;

    /**
     * @var Client|null
     */
    private $http_client = null;

    /** @var bool Whether HTTP client has been injected */
    private $http_client_injected = false;

    /**
     * The SimplePie class contains feed level data and options
     *
     * To use SimplePie, create the SimplePie object with no parameters. You can
     * then set configuration options using the provided methods. After setting
     * them, you must initialise the feed using $feed->init(). At that point the
     * object's methods and properties will be available to you.
     *
     * Previously, it was possible to pass in the feed URL along with cache
     * options directly into the constructor. This has been removed as of 1.3 as
     * it caused a lot of confusion.
     *
     * @since 1.0 Preview Release
     */
    public function __construct()
    {
        if (version_compare(PHP_VERSION, '7.2', '<')) {
            exit('Please upgrade to PHP 7.2 or newer.');
        }

        $this->set_useragent();

        $this->set_cache_namefilter(new CallableNameFilter($this->cache_name_function));

        // Other objects, instances created here so we can set options on them
        $this->sanitize = new Sanitize();
        $this->registry = new Registry();

        if (func_num_args() > 0) {
            trigger_error('Passing parameters to the constructor is no longer supported. Please use set_feed_url(), set_cache_location(), and set_cache_duration() directly.', \E_USER_DEPRECATED);

            $args = func_get_args();
            switch (count($args)) {
                case 3:
                    $this->set_cache_duration($args[2]);
                    // no break
                case 2:
                    $this->set_cache_location($args[1]);
                    // no break
                case 1:
                    $this->set_feed_url($args[0]);
                    $this->init();
            }
        }
    }

    /**
     * Used for converting object to a string
     * @return string
     */
    public function __toString()
    {
        return md5(serialize($this->data));
    }

    /**
     * Remove items that link back to this before destroying this object
     * @return void
     */
    public function __destruct()
    {
        if (!gc_enabled()) {
            if (!empty($this->data['items'])) {
                foreach ($this->data['items'] as $item) {
                    $item->__destruct();
                }
                unset($item, $this->data['items']);
            }
            if (!empty($this->data['ordered_items'])) {
                foreach ($this->data['ordered_items'] as $item) {
                    $item->__destruct();
                }
                unset($item, $this->data['ordered_items']);
            }
        }
    }

    /**
     * Force the given data/URL to be treated as a feed
     *
     * This tells SimplePie to ignore the content-type provided by the server.
     * Be careful when using this option, as it will also disable autodiscovery.
     *
     * @since 1.1
     * @param bool $enable Force the given data/URL to be treated as a feed
     * @return void
     */
    public function force_feed(bool $enable = false)
    {
        $this->force_feed = $enable;
    }

    /**
     * Set the URL of the feed you want to parse
     *
     * This allows you to enter the URL of the feed you want to parse, or the
     * website you want to try to use auto-discovery on. This takes priority
     * over any set raw data.
     *
     * Deprecated since 1.9.0: You can set multiple feeds to mash together by passing an array instead
     * of a string for the $url. Remember that with each additional feed comes
     * additional processing and resources.
     *
     * @since 1.0 Preview Release
     * @see set_raw_data()
     * @param string|string[] $url This is the URL (or (deprecated) array of URLs) that you want to parse.
     * @return void
     */
    public function set_feed_url($url)
    {
        $this->multifeed_url = [];
        if (is_array($url)) {
            trigger_error('Fetching multiple feeds with single SimplePie instance is deprecated since SimplePie 1.9.0, create one SimplePie instance per feed and use SimplePie::merge_items to get a single list of items.', \E_USER_DEPRECATED);
            foreach ($url as $value) {
                $this->multifeed_url[] = $this->registry->call(Misc::class, 'fix_protocol', [$value, 1]);
            }
        } else {
            $this->feed_url = $this->registry->call(Misc::class, 'fix_protocol', [$url, 1]);
            $this->permanent_url = $this->feed_url;
        }
    }

    /**
     * Set an instance of {@see File} to use as a feed
     *
     * @deprecated since SimplePie 1.9.0, use \SimplePie\SimplePie::set_http_client() or \SimplePie\SimplePie::set_raw_data() instead.
     *
     * @param File &$file
     * @return bool True on success, false on failure
     */
    public function set_file(File &$file)
    {
        // trigger_error(sprintf('SimplePie\SimplePie::set_file() is deprecated since SimplePie 1.9.0, please use "SimplePie\SimplePie::set_http_client()" or "SimplePie\SimplePie::set_raw_data()" instead.'), \E_USER_DEPRECATED);

        $this->feed_url = $file->get_final_requested_uri();
        $this->permanent_url = $this->feed_url;
        $this->file = &$file;

        return true;
    }

    /**
     * Set the raw XML data to parse
     *
     * Allows you to use a string of RSS/Atom data instead of a remote feed.
     *
     * If you have a feed available as a string in PHP, you can tell SimplePie
     * to parse that data string instead of a remote feed. Any set feed URL
     * takes precedence.
     *
     * @since 1.0 Beta 3
     * @param string $data RSS or Atom data as a string.
     * @see set_feed_url()
     * @return void
     */
    public function set_raw_data(string $data)
    {
        $this->raw_data = $data;
    }

    /**
     * Set a PSR-18 client and PSR-17 factories
     *
     * Allows you to use your own HTTP client implementations.
     * This will become required with SimplePie 2.0.0.
     */
    final public function set_http_client(
        ClientInterface $http_client,
        RequestFactoryInterface $request_factory,
        UriFactoryInterface $uri_factory
    ): void {
        $this->http_client = new Psr18Client($http_client, $request_factory, $uri_factory);
    }

    /**
     * Set the default timeout for fetching remote feeds
     *
     * This allows you to change the maximum time the feed's server to respond
     * and send the feed back.
     *
     * @since 1.0 Beta 3
     * @param int $timeout The maximum number of seconds to spend waiting to retrieve a feed.
     * @return void
     */
    public function set_timeout(int $timeout = 10)
    {
        if ($this->http_client_injected) {
            throw new SimplePieException(sprintf(
                'Using "%s()" has no effect, because you already provided a HTTP client with "%s::set_http_client()". Configure timeout in your HTTP client instead.',
                __METHOD__,
                self::class
            ));
        }

        $this->timeout = (int) $timeout;

        // Reset a possible existing FileClient,
        // so a new client with the changed value will be created
        if (is_object($this->http_client) && $this->http_client instanceof FileClient) {
            $this->http_client = null;
        } elseif (is_object($this->http_client)) {
            // Trigger notice if a PSR-18 client was set
            trigger_error(sprintf(
                'Using "%s()" has no effect, because you already provided a HTTP client with "%s::set_http_client()". Configure the timeout in your HTTP client instead.',
                __METHOD__,
                get_class($this)
            ), \E_USER_NOTICE);
        }
    }

    /**
     * Set custom curl options
     *
     * This allows you to change default curl options
     *
     * @since 1.0 Beta 3
     * @param array<int, mixed> $curl_options Curl options to add to default settings
     * @return void
     */
    public function set_curl_options(array $curl_options = [])
    {
        if ($this->http_client_injected) {
            throw new SimplePieException(sprintf(
                'Using "%s()" has no effect, because you already provided a HTTP client with "%s::set_http_client()". Configure custom curl options in your HTTP client instead.',
                __METHOD__,
                self::class
            ));
        }

        $this->curl_options = $curl_options;

        // Reset a possible existing FileClient,
        // so a new client with the changed value will be created
        if (is_object($this->http_client) && $this->http_client instanceof FileClient) {
            $this->http_client = null;
        } elseif (is_object($this->http_client)) {
            // Trigger notice if a PSR-18 client was set
            trigger_error(sprintf(
                'Using "%s()" has no effect, because you already provided a HTTP client with "%s::set_http_client()". Configure the curl options in your HTTP client instead.',
                __METHOD__,
                get_class($this)
            ), \E_USER_NOTICE);
        }
    }

    /**
     * Force SimplePie to use fsockopen() instead of cURL
     *
     * @since 1.0 Beta 3
     * @param bool $enable Force fsockopen() to be used
     * @return void
     */
    public function force_fsockopen(bool $enable = false)
    {
        if ($this->http_client_injected) {
            throw new SimplePieException(sprintf(
                'Using "%s()" has no effect, because you already provided a HTTP client with "%s::set_http_client()". Configure fsockopen in your HTTP client instead.',
                __METHOD__,
                self::class
            ));
        }

        $this->force_fsockopen = $enable;

        // Reset a possible existing FileClient,
        // so a new client with the changed value will be created
        if (is_object($this->http_client) && $this->http_client instanceof FileClient) {
            $this->http_client = null;
        } elseif (is_object($this->http_client)) {
            // Trigger notice if a PSR-18 client was set
            trigger_error(sprintf(
                'Using "%s()" has no effect, because you already provided a HTTP client with "%s::set_http_client()". Configure fsockopen in your HTTP client instead.',
                __METHOD__,
                get_class($this)
            ), \E_USER_NOTICE);
        }
    }

    /**
     * Enable/disable caching in SimplePie.
     *
     * This option allows you to disable caching all-together in SimplePie.
     * However, disabling the cache can lead to longer load times.
     *
     * @since 1.0 Preview Release
     * @param bool $enable Enable caching
     * @return void
     */
    public function enable_cache(bool $enable = true)
    {
        $this->enable_cache = $enable;
    }

    /**
     * Set a PSR-16 implementation as cache
     *
     * @param CacheInterface $cache The PSR-16 cache implementation
     *
     * @return void
     */
    public function set_cache(CacheInterface $cache)
    {
        $this->cache = new Psr16($cache);
    }

    /**
     * SimplePie to continue to fall back to expired cache, if enabled, when
     * feed is unavailable.
     *
     * This tells SimplePie to ignore any file errors and fall back to cache
     * instead. This only works if caching is enabled and cached content
     * still exists.
     *
     * @deprecated since SimplePie 1.8.0, expired cache will not be used anymore.
     *
     * @param bool $enable Force use of cache on fail.
     * @return void
     */
    public function force_cache_fallback(bool $enable = false)
    {
        // @trigger_error(sprintf('SimplePie\SimplePie::force_cache_fallback() is deprecated since SimplePie 1.8.0, expired cache will not be used anymore.'), \E_USER_DEPRECATED);
        $this->force_cache_fallback = $enable;
    }

    /**
     * Set the length of time (in seconds) that the contents of a feed will be
     * cached
     *
     * @param int $seconds The feed content cache duration
     * @return void
     */
    public function set_cache_duration(int $seconds = 3600)
    {
        $this->cache_duration = $seconds;
    }

    /**
     * Set the length of time (in seconds) that the autodiscovered feed URL will
     * be cached
     *
     * @param int $seconds The autodiscovered feed URL cache duration.
     * @return void
     */
    public function set_autodiscovery_cache_duration(int $seconds = 604800)
    {
        $this->autodiscovery_cache_duration = $seconds;
    }

    /**
     * Set the file system location where the cached files should be stored
     *
     * @deprecated since SimplePie 1.8.0, use SimplePie::set_cache() instead.
     *
     * @param string $location The file system location.
     * @return void
     */
    public function set_cache_location(string $location = './cache')
    {
        // @trigger_error(sprintf('SimplePie\SimplePie::set_cache_location() is deprecated since SimplePie 1.8.0, please use "SimplePie\SimplePie::set_cache()" instead.'), \E_USER_DEPRECATED);
        $this->cache_location = $location;
    }

    /**
     * Return the filename (i.e. hash, without path and without extension) of the file to cache a given URL.
     *
     * @param string $url The URL of the feed to be cached.
     * @return string A filename (i.e. hash, without path and without extension).
     */
    public function get_cache_filename(string $url)
    {
        // Append custom parameters to the URL to avoid cache pollution in case of multiple calls with different parameters.
        $url .= $this->force_feed ? '#force_feed' : '';
        $options = [];
        if ($this->timeout != 10) {
            $options[CURLOPT_TIMEOUT] = $this->timeout;
        }
        if ($this->useragent !== Misc::get_default_useragent()) {
            $options[CURLOPT_USERAGENT] = $this->useragent;
        }
        if (!empty($this->curl_options)) {
            foreach ($this->curl_options as $k => $v) {
                $options[$k] = $v;
            }
        }
        if (!empty($options)) {
            ksort($options);
            $url .= '#' . urlencode(var_export($options, true));
        }

        return $this->cache_namefilter->filter($url);
    }

    /**
     * Set whether feed items should be sorted into reverse chronological order
     *
     * @param bool $enable Sort as reverse chronological order.
     * @return void
     */
    public function enable_order_by_date(bool $enable = true)
    {
        $this->order_by_date = $enable;
    }

    /**
     * Set the character encoding used to parse the feed
     *
     * This overrides the encoding reported by the feed, however it will fall
     * back to the normal encoding detection if the override fails
     *
     * @param string|false $encoding Character encoding
     * @return void
     */
    public function set_input_encoding($encoding = false)
    {
        if ($encoding) {
            $this->input_encoding = (string) $encoding;
        } else {
            $this->input_encoding = false;
        }
    }

    /**
     * Set how much feed autodiscovery to do
     *
     * @see self::LOCATOR_NONE
     * @see self::LOCATOR_AUTODISCOVERY
     * @see self::LOCATOR_LOCAL_EXTENSION
     * @see self::LOCATOR_LOCAL_BODY
     * @see self::LOCATOR_REMOTE_EXTENSION
     * @see self::LOCATOR_REMOTE_BODY
     * @see self::LOCATOR_ALL
     * @param self::LOCATOR_* $level Feed Autodiscovery Level (level can be a combination of the above constants, see bitwise OR operator)
     * @return void
     */
    public function set_autodiscovery_level(int $level = self::LOCATOR_ALL)
    {
        $this->autodiscovery = $level;
    }

    /**
     * Get the class registry
     *
     * Use this to override SimplePie's default classes
     *
     * @return Registry
     */
    public function &get_registry()
    {
        return $this->registry;
    }

    /**
     * Set which class SimplePie uses for caching
     *
     * @deprecated since SimplePie 1.3, use {@see set_cache()} instead
     *
     * @param class-string<Cache> $class Name of custom class
     *
     * @return bool True on success, false otherwise
     */
    public function set_cache_class(string $class = Cache::class)
    {
        trigger_error(sprintf('"%s()" is deprecated since SimplePie 1.3, please use "SimplePie\SimplePie::set_cache()" instead.', __METHOD__), \E_USER_DEPRECATED);

        return $this->registry->register(Cache::class, $class, true);
    }

    /**
     * Set which class SimplePie uses for auto-discovery
     *
     * @deprecated since SimplePie 1.3, use {@see get_registry()} instead
     *
     * @param class-string<Locator> $class Name of custom class
     *
     * @return bool True on success, false otherwise
     */
    public function set_locator_class(string $class = Locator::class)
    {
        trigger_error(sprintf('"%s()" is deprecated since SimplePie 1.3, please use "SimplePie\SimplePie::get_registry()" instead.', __METHOD__), \E_USER_DEPRECATED);

        return $this->registry->register(Locator::class, $class, true);
    }

    /**
     * Set which class SimplePie uses for XML parsing
     *
     * @deprecated since SimplePie 1.3, use {@see get_registry()} instead
     *
     * @param class-string<Parser> $class Name of custom class
     *
     * @return bool True on success, false otherwise
     */
    public function set_parser_class(string $class = Parser::class)
    {
        trigger_error(sprintf('"%s()" is deprecated since SimplePie 1.3, please use "SimplePie\SimplePie::get_registry()" instead.', __METHOD__), \E_USER_DEPRECATED);

        return $this->registry->register(Parser::class, $class, true);
    }

    /**
     * Set which class SimplePie uses for remote file fetching
     *
     * @deprecated since SimplePie 1.3, use {@see get_registry()} instead
     *
     * @param class-string<File> $class Name of custom class
     *
     * @return bool True on success, false otherwise
     */
    public function set_file_class(string $class = File::class)
    {
        trigger_error(sprintf('"%s()" is deprecated since SimplePie 1.3, please use "SimplePie\SimplePie::get_registry()" instead.', __METHOD__), \E_USER_DEPRECATED);

        return $this->registry->register(File::class, $class, true);
    }

    /**
     * Set which class SimplePie uses for data sanitization
     *
     * @deprecated since SimplePie 1.3, use {@see get_registry()} instead
     *
     * @param class-string<Sanitize> $class Name of custom class
     *
     * @return bool True on success, false otherwise
     */
    public function set_sanitize_class(string $class = Sanitize::class)
    {
        trigger_error(sprintf('"%s()" is deprecated since SimplePie 1.3, please use "SimplePie\SimplePie::get_registry()" instead.', __METHOD__), \E_USER_DEPRECATED);

        return $this->registry->register(Sanitize::class, $class, true);
    }

    /**
     * Set which class SimplePie uses for handling feed items
     *
     * @deprecated since SimplePie 1.3, use {@see get_registry()} instead
     *
     * @param class-string<Item> $class Name of custom class
     *
     * @return bool True on success, false otherwise
     */
    public function set_item_class(string $class = Item::class)
    {
        trigger_error(sprintf('"%s()" is deprecated since SimplePie 1.3, please use "SimplePie\SimplePie::get_registry()" instead.', __METHOD__), \E_USER_DEPRECATED);

        return $this->registry->register(Item::class, $class, true);
    }

    /**
     * Set which class SimplePie uses for handling author data
     *
     * @deprecated since SimplePie 1.3, use {@see get_registry()} instead
     *
     * @param class-string<Author> $class Name of custom class
     *
     * @return bool True on success, false otherwise
     */
    public function set_author_class(string $class = Author::class)
    {
        trigger_error(sprintf('"%s()" is deprecated since SimplePie 1.3, please use "SimplePie\SimplePie::get_registry()" instead.', __METHOD__), \E_USER_DEPRECATED);

        return $this->registry->register(Author::class, $class, true);
    }

    /**
     * Set which class SimplePie uses for handling category data
     *
     * @deprecated since SimplePie 1.3, use {@see get_registry()} instead
     *
     * @param class-string<Category> $class Name of custom class
     *
     * @return bool True on success, false otherwise
     */
    public function set_category_class(string $class = Category::class)
    {
        trigger_error(sprintf('"%s()" is deprecated since SimplePie 1.3, please use "SimplePie\SimplePie::get_registry()" instead.', __METHOD__), \E_USER_DEPRECATED);

        return $this->registry->register(Category::class, $class, true);
    }

    /**
     * Set which class SimplePie uses for feed enclosures
     *
     * @deprecated since SimplePie 1.3, use {@see get_registry()} instead
     *
     * @param class-string<Enclosure> $class Name of custom class
     *
     * @return bool True on success, false otherwise
     */
    public function set_enclosure_class(string $class = Enclosure::class)
    {
        trigger_error(sprintf('"%s()" is deprecated since SimplePie 1.3, please use "SimplePie\SimplePie::get_registry()" instead.', __METHOD__), \E_USER_DEPRECATED);

        return $this->registry->register(Enclosure::class, $class, true);
    }

    /**
     * Set which class SimplePie uses for `<media:text>` captions
     *
     * @deprecated since SimplePie 1.3, use {@see get_registry()} instead
     *
     * @param class-string<Caption> $class Name of custom class
     *
     * @return bool True on success, false otherwise
     */
    public function set_caption_class(string $class = Caption::class)
    {
        trigger_error(sprintf('"%s()" is deprecated since SimplePie 1.3, please use "SimplePie\SimplePie::get_registry()" instead.', __METHOD__), \E_USER_DEPRECATED);

        return $this->registry->register(Caption::class, $class, true);
    }

    /**
     * Set which class SimplePie uses for `<media:copyright>`
     *
     * @deprecated since SimplePie 1.3, use {@see get_registry()} instead
     *
     * @param class-string<Copyright> $class Name of custom class
     *
     * @return bool True on success, false otherwise
     */
    public function set_copyright_class(string $class = Copyright::class)
    {
        trigger_error(sprintf('"%s()" is deprecated since SimplePie 1.3, please use "SimplePie\SimplePie::get_registry()" instead.', __METHOD__), \E_USER_DEPRECATED);

        return $this->registry->register(Copyright::class, $class, true);
    }

    /**
     * Set which class SimplePie uses for `<media:credit>`
     *
     * @deprecated since SimplePie 1.3, use {@see get_registry()} instead
     *
     * @param class-string<Credit> $class Name of custom class
     *
     * @return bool True on success, false otherwise
     */
    public function set_credit_class(string $class = Credit::class)
    {
        trigger_error(sprintf('"%s()" is deprecated since SimplePie 1.3, please use "SimplePie\SimplePie::get_registry()" instead.', __METHOD__), \E_USER_DEPRECATED);

        return $this->registry->register(Credit::class, $class, true);
    }

    /**
     * Set which class SimplePie uses for `<media:rating>`
     *
     * @deprecated since SimplePie 1.3, use {@see get_registry()} instead
     *
     * @param class-string<Rating> $class Name of custom class
     *
     * @return bool True on success, false otherwise
     */
    public function set_rating_class(string $class = Rating::class)
    {
        trigger_error(sprintf('"%s()" is deprecated since SimplePie 1.3, please use "SimplePie\SimplePie::get_registry()" instead.', __METHOD__), \E_USER_DEPRECATED);

        return $this->registry->register(Rating::class, $class, true);
    }

    /**
     * Set which class SimplePie uses for `<media:restriction>`
     *
     * @deprecated since SimplePie 1.3, use {@see get_registry()} instead
     *
     * @param class-string<Restriction> $class Name of custom class
     *
     * @return bool True on success, false otherwise
     */
    public function set_restriction_class(string $class = Restriction::class)
    {
        trigger_error(sprintf('"%s()" is deprecated since SimplePie 1.3, please use "SimplePie\SimplePie::get_registry()" instead.', __METHOD__), \E_USER_DEPRECATED);

        return $this->registry->register(Restriction::class, $class, true);
    }

    /**
     * Set which class SimplePie uses for content-type sniffing
     *
     * @deprecated since SimplePie 1.3, use {@see get_registry()} instead
     *
     * @param class-string<Sniffer> $class Name of custom class
     *
     * @return bool True on success, false otherwise
     */
    public function set_content_type_sniffer_class(string $class = Sniffer::class)
    {
        trigger_error(sprintf('"%s()" is deprecated since SimplePie 1.3, please use "SimplePie\SimplePie::get_registry()" instead.', __METHOD__), \E_USER_DEPRECATED);

        return $this->registry->register(Sniffer::class, $class, true);
    }

    /**
     * Set which class SimplePie uses item sources
     *
     * @deprecated since SimplePie 1.3, use {@see get_registry()} instead
     *
     * @param class-string<Source> $class Name of custom class
     *
     * @return bool True on success, false otherwise
     */
    public function set_source_class(string $class = Source::class)
    {
        trigger_error(sprintf('"%s()" is deprecated since SimplePie 1.3, please use "SimplePie\SimplePie::get_registry()" instead.', __METHOD__), \E_USER_DEPRECATED);

        return $this->registry->register(Source::class, $class, true);
    }

    /**
     * Set the user agent string
     *
     * @param string $ua New user agent string.
     * @return void
     */
    public function set_useragent(?string $ua = null)
    {
        if ($this->http_client_injected) {
            throw new SimplePieException(sprintf(
                'Using "%s()" has no effect, because you already provided a HTTP client with "%s::set_http_client()". Configure user agent string in your HTTP client instead.',
                __METHOD__,
                self::class
            ));
        }

        if ($ua === null) {
            $ua = Misc::get_default_useragent();
        }

        $this->useragent = (string) $ua;

        // Reset a possible existing FileClient,
        // so a new client with the changed value will be created
        if (is_object($this->http_client) && $this->http_client instanceof FileClient) {
            $this->http_client = null;
        } elseif (is_object($this->http_client)) {
            // Trigger notice if a PSR-18 client was set
            trigger_error(sprintf(
                'Using "%s()" has no effect, because you already provided a HTTP client with "%s::set_http_client()". Configure the useragent in your HTTP client instead.',
                __METHOD__,
                get_class($this)
            ), \E_USER_NOTICE);
        }
    }

    /**
     * Set a namefilter to modify the cache filename with
     *
     * @param NameFilter $filter
     *
     * @return void
     */
    public function set_cache_namefilter(NameFilter $filter): void
    {
        $this->cache_namefilter = $filter;
    }

    /**
     * Set callback function to create cache filename with
     *
     * @deprecated since SimplePie 1.8.0, use {@see set_cache_namefilter()} instead
     *
     * @param (string&(callable(string): string))|null $function Callback function
     * @return void
     */
    public function set_cache_name_function(?string $function = null)
    {
        // trigger_error(sprintf('"%s()" is deprecated since SimplePie 1.8.0, please use "SimplePie\SimplePie::set_cache_namefilter()" instead.', __METHOD__), \E_USER_DEPRECATED);

        if ($function === null) {
            $function = 'md5';
        }

        $this->cache_name_function = $function;

        $this->set_cache_namefilter(new CallableNameFilter($this->cache_name_function));
    }

    /**
     * Set options to make SP as fast as possible
     *
     * Forgoes a substantial amount of data sanitization in favor of speed. This
     * turns SimplePie into a dumb parser of feeds.
     *
     * @param bool $set Whether to set them or not
     * @return void
     */
    public function set_stupidly_fast(bool $set = false)
    {
        if ($set) {
            $this->enable_order_by_date(false);
            $this->remove_div(false);
            $this->strip_comments(false);
            $this->strip_htmltags([]);
            $this->strip_attributes([]);
            $this->add_attributes([]);
            $this->set_image_handler(false);
            $this->set_https_domains([]);
        }
    }

    /**
     * Set maximum number of feeds to check with autodiscovery
     *
     * @param int $max Maximum number of feeds to check
     * @return void
     */
    public function set_max_checked_feeds(int $max = 10)
    {
        $this->max_checked_feeds = $max;
    }

    /**
     * @return void
     */
    public function remove_div(bool $enable = true)
    {
        $this->sanitize->remove_div($enable);
    }

    /**
     * @param string[]|string|false $tags Set a list of tags to strip, or set empty string to use default tags, or false to strip nothing.
     * @return void
     */
    public function strip_htmltags($tags = '', ?bool $encode = null)
    {
        if ($tags === '') {
            $tags = $this->strip_htmltags;
        }
        $this->sanitize->strip_htmltags($tags);
        if ($encode !== null) {
            $this->sanitize->encode_instead_of_strip($encode);
        }
    }

    /**
     * @return void
     */
    public function encode_instead_of_strip(bool $enable = true)
    {
        $this->sanitize->encode_instead_of_strip($enable);
    }

    /**
     * @param string[]|string $attribs
     * @return void
     */
    public function rename_attributes($attribs = '')
    {
        if ($attribs === '') {
            $attribs = $this->rename_attributes;
        }
        $this->sanitize->rename_attributes($attribs);
    }

    /**
     * @param string[]|string $attribs
     * @return void
     */
    public function strip_attributes($attribs = '')
    {
        if ($attribs === '') {
            $attribs = $this->strip_attributes;
        }
        $this->sanitize->strip_attributes($attribs);
    }

    /**
     * @param array<string, array<string, string>>|'' $attribs
     * @return void
     */
    public function add_attributes($attribs = '')
    {
        if ($attribs === '') {
            $attribs = $this->add_attributes;
        }
        $this->sanitize->add_attributes($attribs);
    }

    /**
     * Set the output encoding
     *
     * Allows you to override SimplePie's output to match that of your webpage.
     * This is useful for times when your webpages are not being served as
     * UTF-8. This setting will be obeyed by {@see handle_content_type()}, and
     * is similar to {@see set_input_encoding()}.
     *
     * It should be noted, however, that not all character encodings can support
     * all characters. If your page is being served as ISO-8859-1 and you try
     * to display a Japanese feed, you'll likely see garbled characters.
     * Because of this, it is highly recommended to ensure that your webpages
     * are served as UTF-8.
     *
     * The number of supported character encodings depends on whether your web
     * host supports {@link http://php.net/mbstring mbstring},
     * {@link http://php.net/iconv iconv}, or both. See
     * {@link http://simplepie.org/wiki/faq/Supported_Character_Encodings} for
     * more information.
     *
     * @param string $encoding
     * @return void
     */
    public function set_output_encoding(string $encoding = 'UTF-8')
    {
        $this->sanitize->set_output_encoding($encoding);
    }

    /**
     * @return void
     */
    public function strip_comments(bool $strip = false)
    {
        $this->sanitize->strip_comments($strip);
    }

    /**
     * Set element/attribute key/value pairs of HTML attributes
     * containing URLs that need to be resolved relative to the feed
     *
     * Defaults to |a|@href, |area|@href, |blockquote|@cite, |del|@cite,
     * |form|@action, |img|@longdesc, |img|@src, |input|@src, |ins|@cite,
     * |q|@cite
     *
     * @since 1.0
     * @param array<string, string|string[]>|null $element_attribute Element/attribute key/value pairs, null for default
     * @return void
     */
    public function set_url_replacements(?array $element_attribute = null)
    {
        $this->sanitize->set_url_replacements($element_attribute);
    }

    /**
     * Set the list of domains for which to force HTTPS.
     * @see Sanitize::set_https_domains()
     * @param array<string> $domains List of HTTPS domains. Example array('biz', 'example.com', 'example.org', 'www.example.net').
     * @return void
     */
    public function set_https_domains(array $domains = [])
    {
        $this->sanitize->set_https_domains($domains);
    }

    /**
     * Set the handler to enable the display of cached images.
     *
     * @param string|false $page Web-accessible path to the handler_image.php file.
     * @param string $qs The query string that the value should be passed to.
     * @return void
     */
    public function set_image_handler($page = false, string $qs = 'i')
    {
        if ($page !== false) {
            $this->sanitize->set_image_handler($page . '?' . $qs . '=');
        } else {
            $this->image_handler = '';
        }
    }

    /**
     * Set the limit for items returned per-feed with multifeeds
     *
     * @param int $limit The maximum number of items to return.
     * @return void
     */
    public function set_item_limit(int $limit = 0)
    {
        $this->item_limit = $limit;
    }

    /**
     * Enable throwing exceptions
     *
     * @param bool $enable Should we throw exceptions, or use the old-style error property?
     * @return void
     */
    public function enable_exceptions(bool $enable = true)
    {
        $this->enable_exceptions = $enable;
    }

    /**
     * Initialize the feed object
     *
     * This is what makes everything happen. Period. This is where all of the
     * configuration options get processed, feeds are fetched, cached, and
     * parsed, and all of that other good stuff.
     *
     * @return bool True if successful, false otherwise
     */
    public function init()
    {
        // Check absolute bare minimum requirements.
        if (!extension_loaded('xml') || !extension_loaded('pcre')) {
            $this->error = 'XML or PCRE extensions not loaded!';
            return false;
        }
        // Then check the xml extension is sane (i.e., libxml 2.7.x issue on PHP < 5.2.9 and libxml 2.7.0 to 2.7.2 on any version) if we don't have xmlreader.
        elseif (!extension_loaded('xmlreader')) {
            static $xml_is_sane = null;
            if ($xml_is_sane === null) {
                $parser_check = xml_parser_create();
                xml_parse_into_struct($parser_check, '<foo>&amp;</foo>', $values);
                if (\PHP_VERSION_ID < 80000) {
                    xml_parser_free($parser_check);
                }
                $xml_is_sane = isset($values[0]['value']);
            }
            if (!$xml_is_sane) {
                return false;
            }
        }

        // The default sanitize class gets set in the constructor, check if it has
        // changed.
        if ($this->registry->get_class(Sanitize::class) !== Sanitize::class) {
            $this->sanitize = $this->registry->create(Sanitize::class);
        }
        if (method_exists($this->sanitize, 'set_registry')) {
            $this->sanitize->set_registry($this->registry);
        }

        // Pass whatever was set with config options over to the sanitizer.
        // Pass the classes in for legacy support; new classes should use the registry instead
        $cache = $this->registry->get_class(Cache::class);
        \assert($cache !== null, 'Cache must be defined');
        $this->sanitize->pass_cache_data(
            $this->enable_cache,
            $this->cache_location,
            $this->cache_namefilter,
            $cache,
            $this->cache
        );

        $http_client = $this->get_http_client();

        if ($http_client instanceof Psr18Client) {
            $this->sanitize->set_http_client(
                $http_client->getHttpClient(),
                $http_client->getRequestFactory(),
                $http_client->getUriFactory()
            );
        }

        if (!empty($this->multifeed_url)) {
            $i = 0;
            $success = 0;
            $this->multifeed_objects = [];
            $this->error = [];
            foreach ($this->multifeed_url as $url) {
                $this->multifeed_objects[$i] = clone $this;
                $this->multifeed_objects[$i]->set_feed_url($url);
                $single_success = $this->multifeed_objects[$i]->init();
                $success |= $single_success;
                if (!$single_success) {
                    $this->error[$i] = $this->multifeed_objects[$i]->error();
                }
                $i++;
            }
            return (bool) $success;
        } elseif ($this->feed_url === null && $this->raw_data === null) {
            return false;
        }

        $this->error = null;
        $this->data = [];
        $this->check_modified = false;
        $this->multifeed_objects = [];
        $cache = false;

        if ($this->feed_url !== null) {
            $parsed_feed_url = $this->registry->call(Misc::class, 'parse_url', [$this->feed_url]);

            // Decide whether to enable caching
            if ($this->enable_cache && $parsed_feed_url['scheme'] !== '') {
                $cache = $this->get_cache($this->feed_url);
            }

            // Fetch the data into $this->raw_data
            if (($fetched = $this->fetch_data($cache)) === true) {
                return true;
            } elseif ($fetched === false) {
                return false;
            }

            [$headers, $sniffed] = $fetched;
        }

        // Empty response check
        if (empty($this->raw_data)) {
            $this->error = "A feed could not be found at `$this->feed_url`. Empty body.";
            $this->registry->call(Misc::class, 'error', [$this->error, E_USER_NOTICE, __FILE__, __LINE__]);
            return false;
        }

        // Set up array of possible encodings
        $encodings = [];

        // First check to see if input has been overridden.
        if ($this->input_encoding !== false) {
            $encodings[] = strtoupper($this->input_encoding);
        }

        $application_types = ['application/xml', 'application/xml-dtd', 'application/xml-external-parsed-entity'];
        $text_types = ['text/xml', 'text/xml-external-parsed-entity'];

        // RFC 3023 (only applies to sniffed content)
        if (isset($sniffed)) {
            if (in_array($sniffed, $application_types) || substr($sniffed, 0, 12) === 'application/' && substr($sniffed, -4) === '+xml') {
                if (isset($headers['content-type']) && preg_match('/;\x20?charset=([^;]*)/i', $headers['content-type'], $charset)) {
                    $encodings[] = strtoupper($charset[1]);
                }
                $encodings = array_merge($encodings, $this->registry->call(Misc::class, 'xml_encoding', [$this->raw_data, &$this->registry]));
                $encodings[] = 'UTF-8';
            } elseif (in_array($sniffed, $text_types) || substr($sniffed, 0, 5) === 'text/' && substr($sniffed, -4) === '+xml') {
                if (isset($headers['content-type']) && preg_match('/;\x20?charset=([^;]*)/i', $headers['content-type'], $charset)) {
                    $encodings[] = strtoupper($charset[1]);
                }
                $encodings[] = 'US-ASCII';
            }
            // Text MIME-type default
            elseif (substr($sniffed, 0, 5) === 'text/') {
                $encodings[] = 'UTF-8';
            }
        }

        // Fallback to XML 1.0 Appendix F.1/UTF-8/ISO-8859-1
        $encodings = array_merge($encodings, $this->registry->call(Misc::class, 'xml_encoding', [$this->raw_data, &$this->registry]));
        $encodings[] = 'UTF-8';
        $encodings[] = 'ISO-8859-1';

        // There's no point in trying an encoding twice
        $encodings = array_unique($encodings);

        // Loop through each possible encoding, till we return something, or run out of possibilities
        foreach ($encodings as $encoding) {
            // Change the encoding to UTF-8 (as we always use UTF-8 internally)
            if ($utf8_data = $this->registry->call(Misc::class, 'change_encoding', [$this->raw_data, $encoding, 'UTF-8'])) {
                // Create new parser
                $parser = $this->registry->create(Parser::class);

                // If it's parsed fine
                if ($parser->parse($utf8_data, 'UTF-8', $this->permanent_url ?? '')) {
                    $this->data = $parser->get_data();
                    if (!($this->get_type() & ~self::TYPE_NONE)) {
                        $this->error = "A feed could not be found at `$this->feed_url`. This does not appear to be a valid RSS or Atom feed.";
                        $this->registry->call(Misc::class, 'error', [$this->error, E_USER_NOTICE, __FILE__, __LINE__]);
                        return false;
                    }

                    if (isset($headers)) {
                        $this->data['headers'] = $headers;
                    }
                    $this->data['build'] = Misc::get_build();

                    // Cache the file if caching is enabled
                    $this->data['cache_expiration_time'] = $this->cache_duration + time();

                    if ($cache && !$cache->set_data($this->get_cache_filename($this->feed_url), $this->data, $this->cache_duration)) {
                        trigger_error("$this->cache_location is not writable. Make sure you've set the correct relative or absolute path, and that the location is server-writable.", E_USER_WARNING);
                    }
                    return true;
                }
            }
        }

        if (isset($parser)) {
            // We have an error, just set Misc::error to it and quit
            $this->error = $this->feed_url;
            $this->error .= sprintf(' is invalid XML, likely due to invalid characters. XML error: %s at line %d, column %d', $parser->get_error_string(), $parser->get_current_line(), $parser->get_current_column());
        } else {
            $this->error = 'The data could not be converted to UTF-8.';
            if (!extension_loaded('mbstring') && !extension_loaded('iconv') && !class_exists('\UConverter')) {
                $this->error .= ' You MUST have either the iconv, mbstring or intl (PHP 5.5+) extension installed and enabled.';
            } else {
                $missingExtensions = [];
                if (!extension_loaded('iconv')) {
                    $missingExtensions[] = 'iconv';
                }
                if (!extension_loaded('mbstring')) {
                    $missingExtensions[] = 'mbstring';
                }
                if (!class_exists('\UConverter')) {
                    $missingExtensions[] = 'intl (PHP 5.5+)';
                }
                $this->error .= ' Try installing/enabling the ' . implode(' or ', $missingExtensions) . ' extension.';
            }
        }

        $this->registry->call(Misc::class, 'error', [$this->error, E_USER_NOTICE, __FILE__, __LINE__]);

        return false;
    }

    /**
     * Fetch the data
     *
     * If the data is already cached, attempt to fetch it from there instead
     *
     * @param Base|DataCache|false $cache Cache handler, or false to not load from the cache
     * @return array{array<string, string>, string}|bool Returns true if the data was loaded from the cache, or an array of HTTP headers and sniffed type
     */
    protected function fetch_data(&$cache)
    {
        if ($cache instanceof Base) {
            // @trigger_error(sprintf('Providing $cache as "\SimplePie\Cache\Base" in %s() is deprecated since SimplePie 1.8.0, please provide "\SimplePie\Cache\DataCache" implementation instead.', __METHOD__), \E_USER_DEPRECATED);
            $cache = new BaseDataCache($cache);
        }

        // @phpstan-ignore-next-line Enforce PHPDoc type.
        if ($cache !== false && !$cache instanceof DataCache) {
            throw new InvalidArgumentException(sprintf(
                '%s(): Argument #1 ($cache) must be of type %s|false',
                __METHOD__,
                DataCache::class
            ), 1);
        }

        $cacheKey = $this->get_cache_filename($this->feed_url);

        // If it's enabled, use the cache
        if ($cache) {
            // Load the Cache
            $this->data = $cache->get_data($cacheKey, []);

            if (!empty($this->data)) {
                // If the cache is for an outdated build of SimplePie
                if (!isset($this->data['build']) || $this->data['build'] !== Misc::get_build()) {
                    $cache->delete_data($cacheKey);
                    $this->data = [];
                }
                // If we've hit a collision just rerun it with caching disabled
                elseif (isset($this->data['url']) && $this->data['url'] !== $this->feed_url) {
                    $cache = false;
                    $this->data = [];
                }
                // If we've got a non feed_url stored (if the page isn't actually a feed, or is a redirect) use that URL.
                elseif (isset($this->data['feed_url'])) {
                    // Do not need to do feed autodiscovery yet.
                    if ($this->data['feed_url'] !== $this->data['url']) {
                        $this->set_feed_url($this->data['feed_url']);
                        $this->data['url'] = $this->data['feed_url'];

                        $cache->set_data($this->get_cache_filename($this->feed_url), $this->data, $this->autodiscovery_cache_duration);

                        return $this->init();
                    }

                    $cache->delete_data($this->get_cache_filename($this->feed_url));
                    $this->data = [];
                }
                // Check if the cache has been updated
                elseif (!isset($this->data['cache_expiration_time']) || $this->data['cache_expiration_time'] < time()) {
                    // Want to know if we tried to send last-modified and/or etag headers
                    // when requesting this file. (Note that it's up to the file to
                    // support this, but we don't always send the headers either.)
                    $this->check_modified = true;
                    if (isset($this->data['headers']['last-modified']) || isset($this->data['headers']['etag'])) {
                        $headers = [
                            'Accept' => SimplePie::DEFAULT_HTTP_ACCEPT_HEADER,
                        ];
                        if (isset($this->data['headers']['last-modified'])) {
                            $headers['if-modified-since'] = $this->data['headers']['last-modified'];
                        }
                        if (isset($this->data['headers']['etag'])) {
                            $headers['if-none-match'] = $this->data['headers']['etag'];
                        }

                        try {
                            $file = $this->get_http_client()->request(Client::METHOD_GET, $this->feed_url, $headers);
                            $this->status_code = $file->get_status_code();
                        } catch (ClientException $th) {
                            $this->check_modified = false;
                            $this->status_code = 0;

                            if ($this->force_cache_fallback) {
                                $this->data['cache_expiration_time'] = $this->cache_duration + time();
                                $cache->set_data($cacheKey, $this->data, $this->cache_duration);

                                return true;
                            }

                            $failedFileReason = $th->getMessage();
                        }

                        if ($this->status_code === 304) {
                            // Set raw_data to false here too, to signify that the cache
                            // is still valid.
                            $this->raw_data = false;
                            $this->data['cache_expiration_time'] = $this->cache_duration + time();
                            $cache->set_data($cacheKey, $this->data, $this->cache_duration);

                            return true;
                        }
                    }
                }
                // If the cache is still valid, just return true
                else {
                    $this->raw_data = false;
                    return true;
                }
            }
            // If the cache is empty
            else {
                $this->data = [];
            }
        }

        // If we don't already have the file (it'll only exist if we've opened it to check if the cache has been modified), open it.
        if (!isset($file)) {
            if ($this->file instanceof File && $this->file->get_final_requested_uri() === $this->feed_url) {
                $file = &$this->file;
            } elseif (isset($failedFileReason)) {
                // Do not try to fetch again if we already failed once.
                // If the file connection had an error, set SimplePie::error to that and quit
                $this->error = $failedFileReason;

                return !empty($this->data);
            } else {
                $headers = [
                    'Accept' => SimplePie::DEFAULT_HTTP_ACCEPT_HEADER,
                ];
                try {
                    $file = $this->get_http_client()->request(Client::METHOD_GET, $this->feed_url, $headers);
                } catch (ClientException $th) {
                    // If the file connection has an error, set SimplePie::error to that and quit
                    $this->error = $th->getMessage();

                    return !empty($this->data);
                }
            }
        }
        $this->status_code = $file->get_status_code();

        // If the file connection has an error, set SimplePie::error to that and quit
        if (!(!Misc::is_remote_uri($file->get_final_requested_uri()) || ($file->get_status_code() === 200 || $file->get_status_code() > 206 && $file->get_status_code() < 300))) {
            $this->error = 'Retrieved unsupported status code "' . $this->status_code . '"';
            return !empty($this->data);
        }

        if (!$this->force_feed) {
            // Check if the supplied URL is a feed, if it isn't, look for it.
            $locate = $this->registry->create(Locator::class, [
                (!$file instanceof File) ? File::fromResponse($file) : $file,
                $this->timeout,
                $this->useragent,
                $this->max_checked_feeds,
                $this->force_fsockopen,
                $this->curl_options
            ]);

            $http_client = $this->get_http_client();

            if ($http_client instanceof Psr18Client) {
                $locate->set_http_client(
                    $http_client->getHttpClient(),
                    $http_client->getRequestFactory(),
                    $http_client->getUriFactory()
                );
            }

            if (!$locate->is_feed($file)) {
                $copyStatusCode = $file->get_status_code();
                $copyContentType = $file->get_header_line('content-type');
                try {
                    $microformats = false;
                    if (class_exists('DOMXpath') && function_exists('Mf2\parse')) {
                        $doc = new \DOMDocument();
                        @$doc->loadHTML($file->get_body_content());
                        $xpath = new \DOMXpath($doc);
                        // Check for both h-feed and h-entry, as both a feed with no entries
                        // and a list of entries without an h-feed wrapper are both valid.
                        $query = '//*[contains(concat(" ", @class, " "), " h-feed ") or '.
                            'contains(concat(" ", @class, " "), " h-entry ")]';

                        /** @var \DOMNodeList<\DOMElement> $result */
                        $result = $xpath->query($query);
                        $microformats = $result->length !== 0;
                    }
                    // Now also do feed discovery, but if microformats were found don't
                    // overwrite the current value of file.
                    $discovered = $locate->find(
                        $this->autodiscovery,
                        $this->all_discovered_feeds
                    );
                    if ($microformats) {
                        $hub = $locate->get_rel_link('hub');
                        $self = $locate->get_rel_link('self');
                        if ($hub || $self) {
                            $file = $this->store_links($file, $hub, $self);
                        }
                        // Push the current file onto all_discovered feeds so the user can
                        // be shown this as one of the options.
                        if ($this->all_discovered_feeds !== null) {
                            $this->all_discovered_feeds[] = $file;
                        }
                    } else {
                        if ($discovered) {
                            $file = $discovered;
                        } else {
                            // We need to unset this so that if SimplePie::set_file() has
                            // been called that object is untouched
                            unset($file);
                            $this->error = "A feed could not be found at `$this->feed_url`; the status code is `$copyStatusCode` and content-type is `$copyContentType`";
                            $this->registry->call(Misc::class, 'error', [$this->error, E_USER_NOTICE, __FILE__, __LINE__]);
                            return false;
                        }
                    }
                } catch (SimplePieException $e) {
                    // We need to unset this so that if SimplePie::set_file() has been called that object is untouched
                    unset($file);
                    // This is usually because DOMDocument doesn't exist
                    $this->error = $e->getMessage();
                    $this->registry->call(Misc::class, 'error', [$this->error, E_USER_NOTICE, $e->getFile(), $e->getLine()]);
                    return false;
                }

                if ($cache) {
                    $this->data = [
                        'url' => $this->feed_url,
                        'feed_url' => $file->get_final_requested_uri(),
                        'build' => Misc::get_build(),
                        'cache_expiration_time' => $this->cache_duration + time(),
                    ];

                    if (!$cache->set_data($cacheKey, $this->data, $this->cache_duration)) {
                        trigger_error("$this->cache_location is not writable. Make sure you've set the correct relative or absolute path, and that the location is server-writable.", E_USER_WARNING);
                    }
                }
            }
            $this->feed_url = $file->get_final_requested_uri();
            $locate = null;
        }

        $this->raw_data = $file->get_body_content();
        $this->permanent_url = $file->get_permanent_uri();

        $headers = [];
        foreach ($file->get_headers() as $key => $values) {
            $headers[$key] = implode(', ', $values);
        }

        $sniffer = $this->registry->create(Sniffer::class, [&$file]);
        $sniffed = $sniffer->get_type();

        return [$headers, $sniffed];
    }

    /**
     * Get the error message for the occurred error
     *
     * @return string|string[]|null Error message, or array of messages for multifeeds
     */
    public function error()
    {
        return $this->error;
    }

    /**
     * Get the last HTTP status code
     *
     * @return int Status code
     */
    public function status_code()
    {
        return $this->status_code;
    }

    /**
     * Get the raw XML
     *
     * This is the same as the old `$feed->enable_xml_dump(true)`, but returns
     * the data instead of printing it.
     *
     * @return string|false Raw XML data, false if the cache is used
     */
    public function get_raw_data()
    {
        return $this->raw_data;
    }

    /**
     * Get the character encoding used for output
     *
     * @since Preview Release
     * @return string
     */
    public function get_encoding()
    {
        return $this->sanitize->output_encoding;
    }

    /**
     * Send the content-type header with correct encoding
     *
     * This method ensures that the SimplePie-enabled page is being served with
     * the correct {@link http://www.iana.org/assignments/media-types/ mime-type}
     * and character encoding HTTP headers (character encoding determined by the
     * {@see set_output_encoding} config option).
     *
     * This won't work properly if any content or whitespace has already been
     * sent to the browser, because it relies on PHP's
     * {@link http://php.net/header header()} function, and these are the
     * circumstances under which the function works.
     *
     * Because it's setting these settings for the entire page (as is the nature
     * of HTTP headers), this should only be used once per page (again, at the
     * top).
     *
     * @param string $mime MIME type to serve the page as
     * @return void
     */
    public function handle_content_type(string $mime = 'text/html')
    {
        if (!headers_sent()) {
            $header = "Content-type: $mime;";
            if ($this->get_encoding()) {
                $header .= ' charset=' . $this->get_encoding();
            } else {
                $header .= ' charset=UTF-8';
            }
            header($header);
        }
    }

    /**
     * Get the type of the feed
     *
     * This returns a self::TYPE_* constant, which can be tested against
     * using {@link http://php.net/language.operators.bitwise bitwise operators}
     *
     * @since 0.8 (usage changed to using constants in 1.0)
     * @see self::TYPE_NONE Unknown.
     * @see self::TYPE_RSS_090 RSS 0.90.
     * @see self::TYPE_RSS_091_NETSCAPE RSS 0.91 (Netscape).
     * @see self::TYPE_RSS_091_USERLAND RSS 0.91 (Userland).
     * @see self::TYPE_RSS_091 RSS 0.91.
     * @see self::TYPE_RSS_092 RSS 0.92.
     * @see self::TYPE_RSS_093 RSS 0.93.
     * @see self::TYPE_RSS_094 RSS 0.94.
     * @see self::TYPE_RSS_10 RSS 1.0.
     * @see self::TYPE_RSS_20 RSS 2.0.x.
     * @see self::TYPE_RSS_RDF RDF-based RSS.
     * @see self::TYPE_RSS_SYNDICATION Non-RDF-based RSS (truly intended as syndication format).
     * @see self::TYPE_RSS_ALL Any version of RSS.
     * @see self::TYPE_ATOM_03 Atom 0.3.
     * @see self::TYPE_ATOM_10 Atom 1.0.
     * @see self::TYPE_ATOM_ALL Any version of Atom.
     * @see self::TYPE_ALL Any known/supported feed type.
     * @return int-mask-of<self::TYPE_*> constant
     */
    public function get_type()
    {
        if (!isset($this->data['type'])) {
            $this->data['type'] = self::TYPE_ALL;
            if (isset($this->data['child'][self::NAMESPACE_ATOM_10]['feed'])) {
                $this->data['type'] &= self::TYPE_ATOM_10;
            } elseif (isset($this->data['child'][self::NAMESPACE_ATOM_03]['feed'])) {
                $this->data['type'] &= self::TYPE_ATOM_03;
            } elseif (isset($this->data['child'][self::NAMESPACE_RDF]['RDF'])) {
                if (isset($this->data['child'][self::NAMESPACE_RDF]['RDF'][0]['child'][self::NAMESPACE_RSS_10]['channel'])
                || isset($this->data['child'][self::NAMESPACE_RDF]['RDF'][0]['child'][self::NAMESPACE_RSS_10]['image'])
                || isset($this->data['child'][self::NAMESPACE_RDF]['RDF'][0]['child'][self::NAMESPACE_RSS_10]['item'])
                || isset($this->data['child'][self::NAMESPACE_RDF]['RDF'][0]['child'][self::NAMESPACE_RSS_10]['textinput'])) {
                    $this->data['type'] &= self::TYPE_RSS_10;
                }
                if (isset($this->data['child'][self::NAMESPACE_RDF]['RDF'][0]['child'][self::NAMESPACE_RSS_090]['channel'])
                || isset($this->data['child'][self::NAMESPACE_RDF]['RDF'][0]['child'][self::NAMESPACE_RSS_090]['image'])
                || isset($this->data['child'][self::NAMESPACE_RDF]['RDF'][0]['child'][self::NAMESPACE_RSS_090]['item'])
                || isset($this->data['child'][self::NAMESPACE_RDF]['RDF'][0]['child'][self::NAMESPACE_RSS_090]['textinput'])) {
                    $this->data['type'] &= self::TYPE_RSS_090;
                }
            } elseif (isset($this->data['child'][self::NAMESPACE_RSS_20]['rss'])) {
                $this->data['type'] &= self::TYPE_RSS_ALL;
                if (isset($this->data['child'][self::NAMESPACE_RSS_20]['rss'][0]['attribs']['']['version'])) {
                    switch (trim($this->data['child'][self::NAMESPACE_RSS_20]['rss'][0]['attribs']['']['version'])) {
                        case '0.91':
                            $this->data['type'] &= self::TYPE_RSS_091;
                            if (isset($this->data['child'][self::NAMESPACE_RSS_20]['rss'][0]['child'][self::NAMESPACE_RSS_20]['skiphours']['hour'][0]['data'])) {
                                switch (trim($this->data['child'][self::NAMESPACE_RSS_20]['rss'][0]['child'][self::NAMESPACE_RSS_20]['skiphours']['hour'][0]['data'])) {
                                    case '0':
                                        $this->data['type'] &= self::TYPE_RSS_091_NETSCAPE;
                                        break;

                                    case '24':
                                        $this->data['type'] &= self::TYPE_RSS_091_USERLAND;
                                        break;
                                }
                            }
                            break;

                        case '0.92':
                            $this->data['type'] &= self::TYPE_RSS_092;
                            break;

                        case '0.93':
                            $this->data['type'] &= self::TYPE_RSS_093;
                            break;

                        case '0.94':
                            $this->data['type'] &= self::TYPE_RSS_094;
                            break;

                        case '2.0':
                            $this->data['type'] &= self::TYPE_RSS_20;
                            break;
                    }
                }
            } else {
                $this->data['type'] = self::TYPE_NONE;
            }
        }
        return $this->data['type'];
    }

    /**
     * Get the URL for the feed
     *
     * When the 'permanent' mode is enabled, returns the original feed URL,
     * except in the case of an `HTTP 301 Moved Permanently` status response,
     * in which case the location of the first redirection is returned.
     *
     * When the 'permanent' mode is disabled (default),
     * may or may not be different from the URL passed to {@see set_feed_url()},
     * depending on whether auto-discovery was used, and whether there were
     * any redirects along the way.
     *
     * @since Preview Release (previously called `get_feed_url()` since SimplePie 0.8.)
     * @todo Support <itunes:new-feed-url>
     * @todo Also, |atom:link|@rel=self
     * @param bool $permanent Permanent mode to return only the original URL or the first redirection
     * iff it is a 301 redirection
     * @return string|null
     */
    public function subscribe_url(bool $permanent = false)
    {
        if ($permanent) {
            if ($this->permanent_url !== null) {
                // sanitize encodes ampersands which are required when used in a url.
                return str_replace(
                    '&amp;',
                    '&',
                    $this->sanitize(
                        $this->permanent_url,
                        self::CONSTRUCT_IRI
                    )
                );
            }
        } else {
            if ($this->feed_url !== null) {
                return str_replace(
                    '&amp;',
                    '&',
                    $this->sanitize(
                        $this->feed_url,
                        self::CONSTRUCT_IRI
                    )
                );
            }
        }
        return null;
    }

    /**
     * Get data for an feed-level element
     *
     * This method allows you to get access to ANY element/attribute that is a
     * sub-element of the opening feed tag.
     *
     * The return value is an indexed array of elements matching the given
     * namespace and tag name. Each element has `attribs`, `data` and `child`
     * subkeys. For `attribs` and `child`, these contain namespace subkeys.
     * `attribs` then has one level of associative name => value data (where
     * `value` is a string) after the namespace. `child` has tag-indexed keys
     * after the namespace, each member of which is an indexed array matching
     * this same format.
     *
     * For example:
     * <pre>
     * // This is probably a bad example because we already support
     * // <media:content> natively, but it shows you how to parse through
     * // the nodes.
     * $group = $item->get_item_tags(\SimplePie\SimplePie::NAMESPACE_MEDIARSS, 'group');
     * $content = $group[0]['child'][\SimplePie\SimplePie::NAMESPACE_MEDIARSS]['content'];
     * $file = $content[0]['attribs']['']['url'];
     * echo $file;
     * </pre>
     *
     * @since 1.0
     * @see http://simplepie.org/wiki/faq/supported_xml_namespaces
     * @param string $namespace The URL of the XML namespace of the elements you're trying to access
     * @param string $tag Tag name
     * @return array<array<string, mixed>>|null
     */
    public function get_feed_tags(string $namespace, string $tag)
    {
        $type = $this->get_type();
        if ($type & self::TYPE_ATOM_10) {
            if (isset($this->data['child'][self::NAMESPACE_ATOM_10]['feed'][0]['child'][$namespace][$tag])) {
                return $this->data['child'][self::NAMESPACE_ATOM_10]['feed'][0]['child'][$namespace][$tag];
            }
        }
        if ($type & self::TYPE_ATOM_03) {
            if (isset($this->data['child'][self::NAMESPACE_ATOM_03]['feed'][0]['child'][$namespace][$tag])) {
                return $this->data['child'][self::NAMESPACE_ATOM_03]['feed'][0]['child'][$namespace][$tag];
            }
        }
        if ($type & self::TYPE_RSS_RDF) {
            if (isset($this->data['child'][self::NAMESPACE_RDF]['RDF'][0]['child'][$namespace][$tag])) {
                return $this->data['child'][self::NAMESPACE_RDF]['RDF'][0]['child'][$namespace][$tag];
            }
        }
        if ($type & self::TYPE_RSS_SYNDICATION) {
            if (isset($this->data['child'][self::NAMESPACE_RSS_20]['rss'][0]['child'][$namespace][$tag])) {
                return $this->data['child'][self::NAMESPACE_RSS_20]['rss'][0]['child'][$namespace][$tag];
            }
        }
        return null;
    }

    /**
     * Get data for an channel-level element
     *
     * This method allows you to get access to ANY element/attribute in the
     * channel/header section of the feed.
     *
     * See {@see SimplePie::get_feed_tags()} for a description of the return value
     *
     * @since 1.0
     * @see http://simplepie.org/wiki/faq/supported_xml_namespaces
     * @param string $namespace The URL of the XML namespace of the elements you're trying to access
     * @param string $tag Tag name
     * @return array<array<string, mixed>>|null
     */
    public function get_channel_tags(string $namespace, string $tag)
    {
        $type = $this->get_type();
        if ($type & self::TYPE_ATOM_ALL) {
            if ($return = $this->get_feed_tags($namespace, $tag)) {
                return $return;
            }
        }
        if ($type & self::TYPE_RSS_10) {
            if ($channel = $this->get_feed_tags(self::NAMESPACE_RSS_10, 'channel')) {
                if (isset($channel[0]['child'][$namespace][$tag])) {
                    return $channel[0]['child'][$namespace][$tag];
                }
            }
        }
        if ($type & self::TYPE_RSS_090) {
            if ($channel = $this->get_feed_tags(self::NAMESPACE_RSS_090, 'channel')) {
                if (isset($channel[0]['child'][$namespace][$tag])) {
                    return $channel[0]['child'][$namespace][$tag];
                }
            }
        }
        if ($type & self::TYPE_RSS_SYNDICATION) {
            if ($channel = $this->get_feed_tags(self::NAMESPACE_RSS_20, 'channel')) {
                if (isset($channel[0]['child'][$namespace][$tag])) {
                    return $channel[0]['child'][$namespace][$tag];
                }
            }
        }
        return null;
    }

    /**
     * Get data for an channel-level element
     *
     * This method allows you to get access to ANY element/attribute in the
     * image/logo section of the feed.
     *
     * See {@see SimplePie::get_feed_tags()} for a description of the return value
     *
     * @since 1.0
     * @see http://simplepie.org/wiki/faq/supported_xml_namespaces
     * @param string $namespace The URL of the XML namespace of the elements you're trying to access
     * @param string $tag Tag name
     * @return array<array<string, mixed>>|null
     */
    public function get_image_tags(string $namespace, string $tag)
    {
        $type = $this->get_type();
        if ($type & self::TYPE_RSS_10) {
            if ($image = $this->get_feed_tags(self::NAMESPACE_RSS_10, 'image')) {
                if (isset($image[0]['child'][$namespace][$tag])) {
                    return $image[0]['child'][$namespace][$tag];
                }
            }
        }
        if ($type & self::TYPE_RSS_090) {
            if ($image = $this->get_feed_tags(self::NAMESPACE_RSS_090, 'image')) {
                if (isset($image[0]['child'][$namespace][$tag])) {
                    return $image[0]['child'][$namespace][$tag];
                }
            }
        }
        if ($type & self::TYPE_RSS_SYNDICATION) {
            if ($image = $this->get_channel_tags(self::NAMESPACE_RSS_20, 'image')) {
                if (isset($image[0]['child'][$namespace][$tag])) {
                    return $image[0]['child'][$namespace][$tag];
                }
            }
        }
        return null;
    }

    /**
     * Get the base URL value from the feed
     *
     * Uses `<xml:base>` if available,
     * otherwise uses the first 'self' link or the first 'alternate' link of the feed,
     * or failing that, the URL of the feed itself.
     *
     * @see get_link
     * @see subscribe_url
     *
     * @param array<string, mixed> $element
     * @return string
     */
    public function get_base(array $element = [])
    {
        if (!empty($element['xml_base_explicit']) && isset($element['xml_base'])) {
            return $element['xml_base'];
        }
        if (($link = $this->get_link(0, 'alternate')) !== null) {
            return $link;
        }
        if (($link = $this->get_link(0, 'self')) !== null) {
            return $link;
        }

        return $this->subscribe_url() ?? '';
    }

    /**
     * Sanitize feed data
     *
     * @access private
     * @see Sanitize::sanitize()
     * @param string $data Data to sanitize
     * @param int-mask-of<SimplePie::CONSTRUCT_*> $type
     * @param string $base Base URL to resolve URLs against
     * @return string Sanitized data
     */
    public function sanitize(string $data, int $type, string $base = '')
    {
        try {
            // This really returns string|false but changing encoding is uncommon and we are going to deprecate it, so let’s just lie to PHPStan in the interest of cleaner annotations.
            return $this->sanitize->sanitize($data, $type, $base);
        } catch (SimplePieException $e) {
            if (!$this->enable_exceptions) {
                $this->error = $e->getMessage();
                $this->registry->call(Misc::class, 'error', [$this->error, E_USER_WARNING, $e->getFile(), $e->getLine()]);
                return '';
            }

            throw $e;
        }
    }

    /**
     * Get the title of the feed
     *
     * Uses `<atom:title>`, `<title>` or `<dc:title>`
     *
     * @since 1.0 (previously called `get_feed_title` since 0.8)
     * @return string|null
     */
    public function get_title()
    {
        if ($return = $this->get_channel_tags(self::NAMESPACE_ATOM_10, 'title')) {
            return $this->sanitize($return[0]['data'], $this->registry->call(Misc::class, 'atom_10_construct_type', [$return[0]['attribs']]), $this->get_base($return[0]));
        } elseif ($return = $this->get_channel_tags(self::NAMESPACE_ATOM_03, 'title')) {
            return $this->sanitize($return[0]['data'], $this->registry->call(Misc::class, 'atom_03_construct_type', [$return[0]['attribs']]), $this->get_base($return[0]));
        } elseif ($return = $this->get_channel_tags(self::NAMESPACE_RSS_10, 'title')) {
            return $this->sanitize($return[0]['data'], self::CONSTRUCT_MAYBE_HTML, $this->get_base($return[0]));
        } elseif ($return = $this->get_channel_tags(self::NAMESPACE_RSS_090, 'title')) {
            return $this->sanitize($return[0]['data'], self::CONSTRUCT_MAYBE_HTML, $this->get_base($return[0]));
        } elseif ($return = $this->get_channel_tags(self::NAMESPACE_RSS_20, 'title')) {
            return $this->sanitize($return[0]['data'], self::CONSTRUCT_MAYBE_HTML, $this->get_base($return[0]));
        } elseif ($return = $this->get_channel_tags(self::NAMESPACE_DC_11, 'title')) {
            return $this->sanitize($return[0]['data'], self::CONSTRUCT_TEXT);
        } elseif ($return = $this->get_channel_tags(self::NAMESPACE_DC_10, 'title')) {
            return $this->sanitize($return[0]['data'], self::CONSTRUCT_TEXT);
        }

        return null;
    }

    /**
     * Get a category for the feed
     *
     * @since Unknown
     * @param int $key The category that you want to return. Remember that arrays begin with 0, not 1
     * @return Category|null
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
     * Get all categories for the feed
     *
     * Uses `<atom:category>`, `<category>` or `<dc:subject>`
     *
     * @since Unknown
     * @return array<Category>|null List of {@see Category} objects
     */
    public function get_categories()
    {
        $categories = [];

        foreach ((array) $this->get_channel_tags(self::NAMESPACE_ATOM_10, 'category') as $category) {
            $term = null;
            $scheme = null;
            $label = null;
            if (isset($category['attribs']['']['term'])) {
                $term = $this->sanitize($category['attribs']['']['term'], self::CONSTRUCT_TEXT);
            }
            if (isset($category['attribs']['']['scheme'])) {
                $scheme = $this->sanitize($category['attribs']['']['scheme'], self::CONSTRUCT_TEXT);
            }
            if (isset($category['attribs']['']['label'])) {
                $label = $this->sanitize($category['attribs']['']['label'], self::CONSTRUCT_TEXT);
            }
            $categories[] = $this->registry->create(Category::class, [$term, $scheme, $label]);
        }
        foreach ((array) $this->get_channel_tags(self::NAMESPACE_RSS_20, 'category') as $category) {
            // This is really the label, but keep this as the term also for BC.
            // Label will also work on retrieving because that falls back to term.
            $term = $this->sanitize($category['data'], self::CONSTRUCT_TEXT);
            if (isset($category['attribs']['']['domain'])) {
                $scheme = $this->sanitize($category['attribs']['']['domain'], self::CONSTRUCT_TEXT);
            } else {
                $scheme = null;
            }
            $categories[] = $this->registry->create(Category::class, [$term, $scheme, null]);
        }
        foreach ((array) $this->get_channel_tags(self::NAMESPACE_DC_11, 'subject') as $category) {
            $categories[] = $this->registry->create(Category::class, [$this->sanitize($category['data'], self::CONSTRUCT_TEXT), null, null]);
        }
        foreach ((array) $this->get_channel_tags(self::NAMESPACE_DC_10, 'subject') as $category) {
            $categories[] = $this->registry->create(Category::class, [$this->sanitize($category['data'], self::CONSTRUCT_TEXT), null, null]);
        }

        if (!empty($categories)) {
            return array_unique($categories);
        }

        return null;
    }

    /**
     * Get an author for the feed
     *
     * @since 1.1
     * @param int $key The author that you want to return. Remember that arrays begin with 0, not 1
     * @return Author|null
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
     * Get all authors for the feed
     *
     * Uses `<atom:author>`, `<author>`, `<dc:creator>` or `<itunes:author>`
     *
     * @since 1.1
     * @return array<Author>|null List of {@see Author} objects
     */
    public function get_authors()
    {
        $authors = [];
        foreach ((array) $this->get_channel_tags(self::NAMESPACE_ATOM_10, 'author') as $author) {
            $name = null;
            $uri = null;
            $email = null;
            if (isset($author['child'][self::NAMESPACE_ATOM_10]['name'][0]['data'])) {
                $name = $this->sanitize($author['child'][self::NAMESPACE_ATOM_10]['name'][0]['data'], self::CONSTRUCT_TEXT);
            }
            if (isset($author['child'][self::NAMESPACE_ATOM_10]['uri'][0]['data'])) {
                $uri = $author['child'][self::NAMESPACE_ATOM_10]['uri'][0];
                $uri = $this->sanitize($uri['data'], self::CONSTRUCT_IRI, $this->get_base($uri));
            }
            if (isset($author['child'][self::NAMESPACE_ATOM_10]['email'][0]['data'])) {
                $email = $this->sanitize($author['child'][self::NAMESPACE_ATOM_10]['email'][0]['data'], self::CONSTRUCT_TEXT);
            }
            if ($name !== null || $email !== null || $uri !== null) {
                $authors[] = $this->registry->create(Author::class, [$name, $uri, $email]);
            }
        }
        if ($author = $this->get_channel_tags(self::NAMESPACE_ATOM_03, 'author')) {
            $name = null;
            $url = null;
            $email = null;
            if (isset($author[0]['child'][self::NAMESPACE_ATOM_03]['name'][0]['data'])) {
                $name = $this->sanitize($author[0]['child'][self::NAMESPACE_ATOM_03]['name'][0]['data'], self::CONSTRUCT_TEXT);
            }
            if (isset($author[0]['child'][self::NAMESPACE_ATOM_03]['url'][0]['data'])) {
                $url = $author[0]['child'][self::NAMESPACE_ATOM_03]['url'][0];
                $url = $this->sanitize($url['data'], self::CONSTRUCT_IRI, $this->get_base($url));
            }
            if (isset($author[0]['child'][self::NAMESPACE_ATOM_03]['email'][0]['data'])) {
                $email = $this->sanitize($author[0]['child'][self::NAMESPACE_ATOM_03]['email'][0]['data'], self::CONSTRUCT_TEXT);
            }
            if ($name !== null || $email !== null || $url !== null) {
                $authors[] = $this->registry->create(Author::class, [$name, $url, $email]);
            }
        }
        foreach ((array) $this->get_channel_tags(self::NAMESPACE_DC_11, 'creator') as $author) {
            $authors[] = $this->registry->create(Author::class, [$this->sanitize($author['data'], self::CONSTRUCT_TEXT), null, null]);
        }
        foreach ((array) $this->get_channel_tags(self::NAMESPACE_DC_10, 'creator') as $author) {
            $authors[] = $this->registry->create(Author::class, [$this->sanitize($author['data'], self::CONSTRUCT_TEXT), null, null]);
        }
        foreach ((array) $this->get_channel_tags(self::NAMESPACE_ITUNES, 'author') as $author) {
            $authors[] = $this->registry->create(Author::class, [$this->sanitize($author['data'], self::CONSTRUCT_TEXT), null, null]);
        }

        if (!empty($authors)) {
            return array_unique($authors);
        }

        return null;
    }

    /**
     * Get a contributor for the feed
     *
     * @since 1.1
     * @param int $key The contrbutor that you want to return. Remember that arrays begin with 0, not 1
     * @return Author|null
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
     * Get all contributors for the feed
     *
     * Uses `<atom:contributor>`
     *
     * @since 1.1
     * @return array<Author>|null List of {@see Author} objects
     */
    public function get_contributors()
    {
        $contributors = [];
        foreach ((array) $this->get_channel_tags(self::NAMESPACE_ATOM_10, 'contributor') as $contributor) {
            $name = null;
            $uri = null;
            $email = null;
            if (isset($contributor['child'][self::NAMESPACE_ATOM_10]['name'][0]['data'])) {
                $name = $this->sanitize($contributor['child'][self::NAMESPACE_ATOM_10]['name'][0]['data'], self::CONSTRUCT_TEXT);
            }
            if (isset($contributor['child'][self::NAMESPACE_ATOM_10]['uri'][0]['data'])) {
                $uri = $contributor['child'][self::NAMESPACE_ATOM_10]['uri'][0];
                $uri = $this->sanitize($uri['data'], self::CONSTRUCT_IRI, $this->get_base($uri));
            }
            if (isset($contributor['child'][self::NAMESPACE_ATOM_10]['email'][0]['data'])) {
                $email = $this->sanitize($contributor['child'][self::NAMESPACE_ATOM_10]['email'][0]['data'], self::CONSTRUCT_TEXT);
            }
            if ($name !== null || $email !== null || $uri !== null) {
                $contributors[] = $this->registry->create(Author::class, [$name, $uri, $email]);
            }
        }
        foreach ((array) $this->get_channel_tags(self::NAMESPACE_ATOM_03, 'contributor') as $contributor) {
            $name = null;
            $url = null;
            $email = null;
            if (isset($contributor['child'][self::NAMESPACE_ATOM_03]['name'][0]['data'])) {
                $name = $this->sanitize($contributor['child'][self::NAMESPACE_ATOM_03]['name'][0]['data'], self::CONSTRUCT_TEXT);
            }
            if (isset($contributor['child'][self::NAMESPACE_ATOM_03]['url'][0]['data'])) {
                $url = $contributor['child'][self::NAMESPACE_ATOM_03]['url'][0];
                $url = $this->sanitize($url['data'], self::CONSTRUCT_IRI, $this->get_base($url));
            }
            if (isset($contributor['child'][self::NAMESPACE_ATOM_03]['email'][0]['data'])) {
                $email = $this->sanitize($contributor['child'][self::NAMESPACE_ATOM_03]['email'][0]['data'], self::CONSTRUCT_TEXT);
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
     * Get a single link for the feed
     *
     * @since 1.0 (previously called `get_feed_link` since Preview Release, `get_feed_permalink()` since 0.8)
     * @param int $key The link that you want to return. Remember that arrays begin with 0, not 1
     * @param string $rel The relationship of the link to return
     * @return string|null Link URL
     */
    public function get_link(int $key = 0, string $rel = 'alternate')
    {
        $links = $this->get_links($rel);
        if (isset($links[$key])) {
            return $links[$key];
        }

        return null;
    }

    /**
     * Get the permalink for the item
     *
     * Returns the first link available with a relationship of "alternate".
     * Identical to {@see get_link()} with key 0
     *
     * @see get_link
     * @since 1.0 (previously called `get_feed_link` since Preview Release, `get_feed_permalink()` since 0.8)
     * @internal Added for parity between the parent-level and the item/entry-level.
     * @return string|null Link URL
     */
    public function get_permalink()
    {
        return $this->get_link(0);
    }

    /**
     * Get all links for the feed
     *
     * Uses `<atom:link>` or `<link>`
     *
     * @since Beta 2
     * @param string $rel The relationship of links to return
     * @return array<string>|null Links found for the feed (strings)
     */
    public function get_links(string $rel = 'alternate')
    {
        if (!isset($this->data['links'])) {
            $this->data['links'] = [];
            if ($links = $this->get_channel_tags(self::NAMESPACE_ATOM_10, 'link')) {
                foreach ($links as $link) {
                    if (isset($link['attribs']['']['href'])) {
                        $link_rel = (isset($link['attribs']['']['rel'])) ? $link['attribs']['']['rel'] : 'alternate';
                        $this->data['links'][$link_rel][] = $this->sanitize($link['attribs']['']['href'], self::CONSTRUCT_IRI, $this->get_base($link));
                    }
                }
            }
            if ($links = $this->get_channel_tags(self::NAMESPACE_ATOM_03, 'link')) {
                foreach ($links as $link) {
                    if (isset($link['attribs']['']['href'])) {
                        $link_rel = (isset($link['attribs']['']['rel'])) ? $link['attribs']['']['rel'] : 'alternate';
                        $this->data['links'][$link_rel][] = $this->sanitize($link['attribs']['']['href'], self::CONSTRUCT_IRI, $this->get_base($link));
                    }
                }
            }
            if ($links = $this->get_channel_tags(self::NAMESPACE_RSS_10, 'link')) {
                $this->data['links']['alternate'][] = $this->sanitize($links[0]['data'], self::CONSTRUCT_IRI, $this->get_base($links[0]));
            }
            if ($links = $this->get_channel_tags(self::NAMESPACE_RSS_090, 'link')) {
                $this->data['links']['alternate'][] = $this->sanitize($links[0]['data'], self::CONSTRUCT_IRI, $this->get_base($links[0]));
            }
            if ($links = $this->get_channel_tags(self::NAMESPACE_RSS_20, 'link')) {
                $this->data['links']['alternate'][] = $this->sanitize($links[0]['data'], self::CONSTRUCT_IRI, $this->get_base($links[0]));
            }

            $keys = array_keys($this->data['links']);
            foreach ($keys as $key) {
                if ($this->registry->call(Misc::class, 'is_isegment_nz_nc', [$key])) {
                    if (isset($this->data['links'][self::IANA_LINK_RELATIONS_REGISTRY . $key])) {
                        $this->data['links'][self::IANA_LINK_RELATIONS_REGISTRY . $key] = array_merge($this->data['links'][$key], $this->data['links'][self::IANA_LINK_RELATIONS_REGISTRY . $key]);
                        $this->data['links'][$key] = &$this->data['links'][self::IANA_LINK_RELATIONS_REGISTRY . $key];
                    } else {
                        $this->data['links'][self::IANA_LINK_RELATIONS_REGISTRY . $key] = &$this->data['links'][$key];
                    }
                } elseif (substr($key, 0, 41) === self::IANA_LINK_RELATIONS_REGISTRY) {
                    $this->data['links'][substr($key, 41)] = &$this->data['links'][$key];
                }
                $this->data['links'][$key] = array_unique($this->data['links'][$key]);
            }
        }

        if (isset($this->data['headers']['link'])) {
            $link_headers = $this->data['headers']['link'];
            if (is_array($link_headers)) {
                $link_headers = implode(',', $link_headers);
            }
            // https://datatracker.ietf.org/doc/html/rfc8288
            if (is_string($link_headers) &&
                preg_match_all('/<(?P<uri>[^>]+)>\s*;\s*rel\s*=\s*(?P<quote>"?)' . preg_quote($rel) . '(?P=quote)\s*(?=,|$)/i', $link_headers, $matches)) {
                return $matches['uri'];
            }
        }

        if (isset($this->data['links'][$rel])) {
            return $this->data['links'][$rel];
        }

        return null;
    }

    /**
     * @return ?array<Response>
     */
    public function get_all_discovered_feeds()
    {
        return $this->all_discovered_feeds;
    }

    /**
     * Get the content for the item
     *
     * Uses `<atom:subtitle>`, `<atom:tagline>`, `<description>`,
     * `<dc:description>`, `<itunes:summary>` or `<itunes:subtitle>`
     *
     * @since 1.0 (previously called `get_feed_description()` since 0.8)
     * @return string|null
     */
    public function get_description()
    {
        if ($return = $this->get_channel_tags(self::NAMESPACE_ATOM_10, 'subtitle')) {
            return $this->sanitize($return[0]['data'], $this->registry->call(Misc::class, 'atom_10_construct_type', [$return[0]['attribs']]), $this->get_base($return[0]));
        } elseif ($return = $this->get_channel_tags(self::NAMESPACE_ATOM_03, 'tagline')) {
            return $this->sanitize($return[0]['data'], $this->registry->call(Misc::class, 'atom_03_construct_type', [$return[0]['attribs']]), $this->get_base($return[0]));
        } elseif ($return = $this->get_channel_tags(self::NAMESPACE_RSS_10, 'description')) {
            return $this->sanitize($return[0]['data'], self::CONSTRUCT_MAYBE_HTML, $this->get_base($return[0]));
        } elseif ($return = $this->get_channel_tags(self::NAMESPACE_RSS_090, 'description')) {
            return $this->sanitize($return[0]['data'], self::CONSTRUCT_MAYBE_HTML, $this->get_base($return[0]));
        } elseif ($return = $this->get_channel_tags(self::NAMESPACE_RSS_20, 'description')) {
            return $this->sanitize($return[0]['data'], self::CONSTRUCT_HTML, $this->get_base($return[0]));
        } elseif ($return = $this->get_channel_tags(self::NAMESPACE_DC_11, 'description')) {
            return $this->sanitize($return[0]['data'], self::CONSTRUCT_TEXT);
        } elseif ($return = $this->get_channel_tags(self::NAMESPACE_DC_10, 'description')) {
            return $this->sanitize($return[0]['data'], self::CONSTRUCT_TEXT);
        } elseif ($return = $this->get_channel_tags(self::NAMESPACE_ITUNES, 'summary')) {
            return $this->sanitize($return[0]['data'], self::CONSTRUCT_HTML, $this->get_base($return[0]));
        } elseif ($return = $this->get_channel_tags(self::NAMESPACE_ITUNES, 'subtitle')) {
            return $this->sanitize($return[0]['data'], self::CONSTRUCT_HTML, $this->get_base($return[0]));
        }

        return null;
    }

    /**
     * Get the copyright info for the feed
     *
     * Uses `<atom:rights>`, `<atom:copyright>` or `<dc:rights>`
     *
     * @since 1.0 (previously called `get_feed_copyright()` since 0.8)
     * @return string|null
     */
    public function get_copyright()
    {
        if ($return = $this->get_channel_tags(self::NAMESPACE_ATOM_10, 'rights')) {
            return $this->sanitize($return[0]['data'], $this->registry->call(Misc::class, 'atom_10_construct_type', [$return[0]['attribs']]), $this->get_base($return[0]));
        } elseif ($return = $this->get_channel_tags(self::NAMESPACE_ATOM_03, 'copyright')) {
            return $this->sanitize($return[0]['data'], $this->registry->call(Misc::class, 'atom_03_construct_type', [$return[0]['attribs']]), $this->get_base($return[0]));
        } elseif ($return = $this->get_channel_tags(self::NAMESPACE_RSS_20, 'copyright')) {
            return $this->sanitize($return[0]['data'], self::CONSTRUCT_TEXT);
        } elseif ($return = $this->get_channel_tags(self::NAMESPACE_DC_11, 'rights')) {
            return $this->sanitize($return[0]['data'], self::CONSTRUCT_TEXT);
        } elseif ($return = $this->get_channel_tags(self::NAMESPACE_DC_10, 'rights')) {
            return $this->sanitize($return[0]['data'], self::CONSTRUCT_TEXT);
        }

        return null;
    }

    /**
     * Get the language for the feed
     *
     * Uses `<language>`, `<dc:language>`, or @xml_lang
     *
     * @since 1.0 (previously called `get_feed_language()` since 0.8)
     * @return string|null
     */
    public function get_language()
    {
        if ($return = $this->get_channel_tags(self::NAMESPACE_RSS_20, 'language')) {
            return $this->sanitize($return[0]['data'], self::CONSTRUCT_TEXT);
        } elseif ($return = $this->get_channel_tags(self::NAMESPACE_DC_11, 'language')) {
            return $this->sanitize($return[0]['data'], self::CONSTRUCT_TEXT);
        } elseif ($return = $this->get_channel_tags(self::NAMESPACE_DC_10, 'language')) {
            return $this->sanitize($return[0]['data'], self::CONSTRUCT_TEXT);
        } elseif (isset($this->data['child'][self::NAMESPACE_ATOM_10]['feed'][0]['xml_lang'])) {
            return $this->sanitize($this->data['child'][self::NAMESPACE_ATOM_10]['feed'][0]['xml_lang'], self::CONSTRUCT_TEXT);
        } elseif (isset($this->data['child'][self::NAMESPACE_ATOM_03]['feed'][0]['xml_lang'])) {
            return $this->sanitize($this->data['child'][self::NAMESPACE_ATOM_03]['feed'][0]['xml_lang'], self::CONSTRUCT_TEXT);
        } elseif (isset($this->data['child'][self::NAMESPACE_RDF]['RDF'][0]['xml_lang'])) {
            return $this->sanitize($this->data['child'][self::NAMESPACE_RDF]['RDF'][0]['xml_lang'], self::CONSTRUCT_TEXT);
        } elseif (isset($this->data['headers']['content-language'])) {
            return $this->sanitize($this->data['headers']['content-language'], self::CONSTRUCT_TEXT);
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
        if ($return = $this->get_channel_tags(self::NAMESPACE_W3C_BASIC_GEO, 'lat')) {
            return (float) $return[0]['data'];
        } elseif (($return = $this->get_channel_tags(self::NAMESPACE_GEORSS, 'point')) && preg_match('/^((?:-)?[0-9]+(?:\.[0-9]+)) ((?:-)?[0-9]+(?:\.[0-9]+))$/', trim($return[0]['data']), $match)) {
            return (float) $match[1];
        }

        return null;
    }

    /**
     * Get the longitude coordinates for the feed
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
        if ($return = $this->get_channel_tags(self::NAMESPACE_W3C_BASIC_GEO, 'long')) {
            return (float) $return[0]['data'];
        } elseif ($return = $this->get_channel_tags(self::NAMESPACE_W3C_BASIC_GEO, 'lon')) {
            return (float) $return[0]['data'];
        } elseif (($return = $this->get_channel_tags(self::NAMESPACE_GEORSS, 'point')) && preg_match('/^((?:-)?[0-9]+(?:\.[0-9]+)) ((?:-)?[0-9]+(?:\.[0-9]+))$/', trim($return[0]['data']), $match)) {
            return (float) $match[2];
        }

        return null;
    }

    /**
     * Get the feed logo's title
     *
     * RSS 0.9.0, 1.0 and 2.0 feeds are allowed to have a "feed logo" title.
     *
     * Uses `<image><title>` or `<image><dc:title>`
     *
     * @return string|null
     */
    public function get_image_title()
    {
        if ($return = $this->get_image_tags(self::NAMESPACE_RSS_10, 'title')) {
            return $this->sanitize($return[0]['data'], self::CONSTRUCT_TEXT);
        } elseif ($return = $this->get_image_tags(self::NAMESPACE_RSS_090, 'title')) {
            return $this->sanitize($return[0]['data'], self::CONSTRUCT_TEXT);
        } elseif ($return = $this->get_image_tags(self::NAMESPACE_RSS_20, 'title')) {
            return $this->sanitize($return[0]['data'], self::CONSTRUCT_TEXT);
        } elseif ($return = $this->get_image_tags(self::NAMESPACE_DC_11, 'title')) {
            return $this->sanitize($return[0]['data'], self::CONSTRUCT_TEXT);
        } elseif ($return = $this->get_image_tags(self::NAMESPACE_DC_10, 'title')) {
            return $this->sanitize($return[0]['data'], self::CONSTRUCT_TEXT);
        }

        return null;
    }

    /**
     * Get the feed logo's URL
     *
     * RSS 0.9.0, 2.0, Atom 1.0, and feeds with iTunes RSS tags are allowed to
     * have a "feed logo" URL. This points directly to the image itself.
     *
     * Uses `<itunes:image>`, `<atom:logo>`, `<atom:icon>`,
     * `<image><title>` or `<image><dc:title>`
     *
     * @return string|null
     */
    public function get_image_url()
    {
        if ($return = $this->get_channel_tags(self::NAMESPACE_ITUNES, 'image')) {
            return $this->sanitize($return[0]['attribs']['']['href'], self::CONSTRUCT_IRI);
        } elseif ($return = $this->get_channel_tags(self::NAMESPACE_ATOM_10, 'logo')) {
            return $this->sanitize($return[0]['data'], self::CONSTRUCT_IRI, $this->get_base($return[0]));
        } elseif ($return = $this->get_channel_tags(self::NAMESPACE_ATOM_10, 'icon')) {
            return $this->sanitize($return[0]['data'], self::CONSTRUCT_IRI, $this->get_base($return[0]));
        } elseif ($return = $this->get_image_tags(self::NAMESPACE_RSS_10, 'url')) {
            return $this->sanitize($return[0]['data'], self::CONSTRUCT_IRI, $this->get_base($return[0]));
        } elseif ($return = $this->get_image_tags(self::NAMESPACE_RSS_090, 'url')) {
            return $this->sanitize($return[0]['data'], self::CONSTRUCT_IRI, $this->get_base($return[0]));
        } elseif ($return = $this->get_image_tags(self::NAMESPACE_RSS_20, 'url')) {
            return $this->sanitize($return[0]['data'], self::CONSTRUCT_IRI, $this->get_base($return[0]));
        }

        return null;
    }


    /**
     * Get the feed logo's link
     *
     * RSS 0.9.0, 1.0 and 2.0 feeds are allowed to have a "feed logo" link. This
     * points to a human-readable page that the image should link to.
     *
     * Uses `<itunes:image>`, `<atom:logo>`, `<atom:icon>`,
     * `<image><title>` or `<image><dc:title>`
     *
     * @return string|null
     */
    public function get_image_link()
    {
        if ($return = $this->get_image_tags(self::NAMESPACE_RSS_10, 'link')) {
            return $this->sanitize($return[0]['data'], self::CONSTRUCT_IRI, $this->get_base($return[0]));
        } elseif ($return = $this->get_image_tags(self::NAMESPACE_RSS_090, 'link')) {
            return $this->sanitize($return[0]['data'], self::CONSTRUCT_IRI, $this->get_base($return[0]));
        } elseif ($return = $this->get_image_tags(self::NAMESPACE_RSS_20, 'link')) {
            return $this->sanitize($return[0]['data'], self::CONSTRUCT_IRI, $this->get_base($return[0]));
        }

        return null;
    }

    /**
     * Get the feed logo's link
     *
     * RSS 2.0 feeds are allowed to have a "feed logo" width.
     *
     * Uses `<image><width>` or defaults to 88 if no width is specified and
     * the feed is an RSS 2.0 feed.
     *
     * @return int|null
     */
    public function get_image_width()
    {
        if ($return = $this->get_image_tags(self::NAMESPACE_RSS_20, 'width')) {
            return intval($return[0]['data']);
        } elseif ($this->get_type() & self::TYPE_RSS_SYNDICATION && $this->get_image_tags(self::NAMESPACE_RSS_20, 'url')) {
            return 88;
        }

        return null;
    }

    /**
     * Get the feed logo's height
     *
     * RSS 2.0 feeds are allowed to have a "feed logo" height.
     *
     * Uses `<image><height>` or defaults to 31 if no height is specified and
     * the feed is an RSS 2.0 feed.
     *
     * @return int|null
     */
    public function get_image_height()
    {
        if ($return = $this->get_image_tags(self::NAMESPACE_RSS_20, 'height')) {
            return intval($return[0]['data']);
        } elseif ($this->get_type() & self::TYPE_RSS_SYNDICATION && $this->get_image_tags(self::NAMESPACE_RSS_20, 'url')) {
            return 31;
        }

        return null;
    }

    /**
     * Get the number of items in the feed
     *
     * This is well-suited for {@link http://php.net/for for()} loops with
     * {@see get_item()}
     *
     * @param int $max Maximum value to return. 0 for no limit
     * @return int Number of items in the feed
     */
    public function get_item_quantity(int $max = 0)
    {
        $qty = count($this->get_items());
        if ($max === 0) {
            return $qty;
        }

        return min($qty, $max);
    }

    /**
     * Get a single item from the feed
     *
     * This is better suited for {@link http://php.net/for for()} loops, whereas
     * {@see get_items()} is better suited for
     * {@link http://php.net/foreach foreach()} loops.
     *
     * @see get_item_quantity()
     * @since Beta 2
     * @param int $key The item that you want to return. Remember that arrays begin with 0, not 1
     * @return Item|null
     */
    public function get_item(int $key = 0)
    {
        $items = $this->get_items();
        if (isset($items[$key])) {
            return $items[$key];
        }

        return null;
    }

    /**
     * Get all items from the feed
     *
     * This is better suited for {@link http://php.net/for for()} loops, whereas
     * {@see get_items()} is better suited for
     * {@link http://php.net/foreach foreach()} loops.
     *
     * @see get_item_quantity
     * @since Beta 2
     * @param int $start Index to start at
     * @param int $end Number of items to return. 0 for all items after `$start`
     * @return Item[] List of {@see Item} objects
     */
    public function get_items(int $start = 0, int $end = 0)
    {
        if (!isset($this->data['items'])) {
            if (!empty($this->multifeed_objects)) {
                $this->data['items'] = SimplePie::merge_items($this->multifeed_objects, $start, $end, $this->item_limit);
                if (empty($this->data['items'])) {
                    return [];
                }
                return $this->data['items'];
            }
            $this->data['items'] = [];
            if ($items = $this->get_feed_tags(self::NAMESPACE_ATOM_10, 'entry')) {
                $keys = array_keys($items);
                foreach ($keys as $key) {
                    $this->data['items'][] = $this->make_item($items[$key]);
                }
            }
            if ($items = $this->get_feed_tags(self::NAMESPACE_ATOM_03, 'entry')) {
                $keys = array_keys($items);
                foreach ($keys as $key) {
                    $this->data['items'][] = $this->make_item($items[$key]);
                }
            }
            if ($items = $this->get_feed_tags(self::NAMESPACE_RSS_10, 'item')) {
                $keys = array_keys($items);
                foreach ($keys as $key) {
                    $this->data['items'][] = $this->make_item($items[$key]);
                }
            }
            if ($items = $this->get_feed_tags(self::NAMESPACE_RSS_090, 'item')) {
                $keys = array_keys($items);
                foreach ($keys as $key) {
                    $this->data['items'][] = $this->make_item($items[$key]);
                }
            }
            if ($items = $this->get_channel_tags(self::NAMESPACE_RSS_20, 'item')) {
                $keys = array_keys($items);
                foreach ($keys as $key) {
                    $this->data['items'][] = $this->make_item($items[$key]);
                }
            }
        }

        if (empty($this->data['items'])) {
            return [];
        }

        if ($this->order_by_date) {
            if (!isset($this->data['ordered_items'])) {
                $this->data['ordered_items'] = $this->data['items'];
                usort($this->data['ordered_items'], [get_class($this), 'sort_items']);
            }
            $items = $this->data['ordered_items'];
        } else {
            $items = $this->data['items'];
        }
        // Slice the data as desired
        if ($end === 0) {
            return array_slice($items, $start);
        }

        return array_slice($items, $start, $end);
    }

    /**
     * Set the favicon handler
     *
     * @deprecated Use your own favicon handling instead
     * @param string|false $page
     * @return bool
     */
    public function set_favicon_handler($page = false, string $qs = 'i')
    {
        trigger_error('Favicon handling has been removed since SimplePie 1.3, please use your own handling', \E_USER_DEPRECATED);
        return false;
    }

    /**
     * Get the favicon for the current feed
     *
     * @deprecated Use your own favicon handling instead
     * @return string|bool
     */
    public function get_favicon()
    {
        trigger_error('Favicon handling has been removed since SimplePie 1.3, please use your own handling', \E_USER_DEPRECATED);

        if (($url = $this->get_link()) !== null) {
            return 'https://www.google.com/s2/favicons?domain=' . urlencode($url);
        }

        return false;
    }

    /**
     * Magic method handler
     *
     * @param string $method Method name
     * @param array<mixed> $args Arguments to the method
     * @return mixed
     */
    public function __call(string $method, array $args)
    {
        if (strpos($method, 'subscribe_') === 0) {
            trigger_error('subscribe_*() has been deprecated since SimplePie 1.3, implement the callback yourself', \E_USER_DEPRECATED);
            return '';
        }
        if ($method === 'enable_xml_dump') {
            trigger_error('enable_xml_dump() has been deprecated since SimplePie 1.3, use get_raw_data() instead', \E_USER_DEPRECATED);
            return false;
        }

        $class = get_class($this);
        $trace = debug_backtrace(); // phpcs:ignore PHPCompatibility.FunctionUse.ArgumentFunctionsReportCurrentValue.NeedsInspection
        $file = $trace[0]['file'] ?? '';
        $line = $trace[0]['line'] ?? '';
        throw new SimplePieException("Call to undefined method $class::$method() in $file on line $line");
    }

    /**
     * Item factory
     *
     * @param array<string, mixed> $data
     */
    private function make_item(array $data): Item
    {
        $item = $this->registry->create(Item::class, [$this, $data]);
        $item->set_sanitize($this->sanitize);

        return $item;
    }

    /**
     * Sorting callback for items
     *
     * @access private
     * @param Item $a
     * @param Item $b
     * @return -1|0|1
     */
    public static function sort_items(Item $a, Item $b)
    {
        $a_date = $a->get_date('U');
        $b_date = $b->get_date('U');
        if ($a_date && $b_date) {
            return $a_date > $b_date ? -1 : 1;
        }
        // Sort items without dates to the top.
        if ($a_date) {
            return 1;
        }
        if ($b_date) {
            return -1;
        }
        return 0;
    }

    /**
     * Merge items from several feeds into one
     *
     * If you're merging multiple feeds together, they need to all have dates
     * for the items or else SimplePie will refuse to sort them.
     *
     * @link http://simplepie.org/wiki/tutorial/sort_multiple_feeds_by_time_and_date#if_feeds_require_separate_per-feed_settings
     * @param array<SimplePie> $urls List of SimplePie feed objects to merge
     * @param int $start Starting item
     * @param int $end Number of items to return
     * @param int $limit Maximum number of items per feed
     * @return array<Item>
     */
    public static function merge_items(array $urls, int $start = 0, int $end = 0, int $limit = 0)
    {
        if (count($urls) > 0) {
            $items = [];
            foreach ($urls as $arg) {
                if ($arg instanceof SimplePie) {
                    $items = array_merge($items, $arg->get_items(0, $limit));

                    // @phpstan-ignore-next-line Enforce PHPDoc type.
                } else {
                    trigger_error('Arguments must be SimplePie objects', E_USER_WARNING);
                }
            }

            usort($items, [get_class($urls[0]), 'sort_items']);

            if ($end === 0) {
                return array_slice($items, $start);
            }

            return array_slice($items, $start, $end);
        }

        trigger_error('Cannot merge zero SimplePie objects', E_USER_WARNING);
        return [];
    }

    /**
     * Store PubSubHubbub links as headers
     *
     * There is no way to find PuSH links in the body of a microformats feed,
     * so they are added to the headers when found, to be used later by get_links.
     */
    private function store_links(Response $file, ?string $hub, ?string $self): Response
    {
        $linkHeaderLine = $file->get_header_line('link');
        $linkHeader = $file->get_header('link');

        if ($hub && !preg_match('/rel=hub/', $linkHeaderLine)) {
            $linkHeader[] = '<'.$hub.'>; rel=hub';
        }

        if ($self && !preg_match('/rel=self/', $linkHeaderLine)) {
            $linkHeader[] = '<'.$self.'>; rel=self';
        }

        if (count($linkHeader) > 0) {
            $file = $file->with_header('link', $linkHeader);
        }

        return $file;
    }

    /**
     * Get a DataCache
     *
     * @param string $feed_url Only needed for BC, can be removed in SimplePie 2.0.0
     *
     * @return DataCache
     */
    private function get_cache(string $feed_url = ''): DataCache
    {
        if ($this->cache === null) {
            // @trigger_error(sprintf('Not providing as PSR-16 cache implementation is deprecated since SimplePie 1.8.0, please use "SimplePie\SimplePie::set_cache()".'), \E_USER_DEPRECATED);
            $cache = $this->registry->call(Cache::class, 'get_handler', [
                $this->cache_location,
                $this->get_cache_filename($feed_url),
                Base::TYPE_FEED
            ]);

            return new BaseDataCache($cache);
        }

        return $this->cache;
    }

    /**
     * Get a HTTP client
     */
    private function get_http_client(): Client
    {
        if ($this->http_client === null) {
            $this->http_client = new FileClient(
                $this->get_registry(),
                [
                    'timeout' => $this->timeout,
                    'redirects' => 5,
                    'useragent' => $this->useragent,
                    'force_fsockopen' => $this->force_fsockopen,
                    'curl_options' => $this->curl_options,
                ]
            );
            $this->http_client_injected = true;
        }

        return $this->http_client;
    }
}

class_alias('SimplePie\SimplePie', 'SimplePie');
