<?php
if ( ! class_exists( 'SimplePie', false ) ) :

// Load classes we will need.
require ABSPATH . WPINC . '/SimplePie/Misc.php';
require ABSPATH . WPINC . '/SimplePie/Cache.php';
require ABSPATH . WPINC . '/SimplePie/File.php';
require ABSPATH . WPINC . '/SimplePie/Sanitize.php';
require ABSPATH . WPINC . '/SimplePie/Registry.php';
require ABSPATH . WPINC . '/SimplePie/IRI.php';
require ABSPATH . WPINC . '/SimplePie/Locator.php';
require ABSPATH . WPINC . '/SimplePie/Content/Type/Sniffer.php';
require ABSPATH . WPINC . '/SimplePie/XML/Declaration/Parser.php';
require ABSPATH . WPINC . '/SimplePie/Parser.php';
require ABSPATH . WPINC . '/SimplePie/Item.php';
require ABSPATH . WPINC . '/SimplePie/Parse/Date.php';
require ABSPATH . WPINC . '/SimplePie/Author.php';

/**
 * WordPress autoloader for SimplePie.
 *
 * @since 3.5.0
 *
 * @param string $class Class name.
 */
function wp_simplepie_autoload( $class ) {
	if ( ! str_starts_with( $class, 'SimplePie_' ) )
		return;

	$file = ABSPATH . WPINC . '/' . str_replace( '_', '/', $class ) . '.php';
	include $file;
}

/**
 * We autoload classes we may not need.
 */
spl_autoload_register( 'wp_simplepie_autoload' );

/**
 * SimplePie
 *
 * A PHP-Based RSS and Atom Feed Framework.
 * Takes the hard work out of managing a complete RSS/Atom solution.
 *
 * Copyright (c) 2004-2017, Ryan Parman, Sam Sneddon, Ryan McCue, and contributors
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
 * @version 1.5.8
 * @copyright 2004-2017 Ryan Parman, Sam Sneddon, Ryan McCue
 * @author Ryan Parman
 * @author Sam Sneddon
 * @author Ryan McCue
 * @link http://simplepie.org/ SimplePie
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */

/**
 * SimplePie Name
 */
define('SIMPLEPIE_NAME', 'SimplePie');

/**
 * SimplePie Version
 */
define('SIMPLEPIE_VERSION', '1.5.8');

/**
 * SimplePie Build
 * @todo Hardcode for release (there's no need to have to call SimplePie_Misc::get_build() only every load of simplepie.inc)
 */
define('SIMPLEPIE_BUILD', gmdate('YmdHis', SimplePie_Misc::get_build()));

/**
 * SimplePie Website URL
 */
define('SIMPLEPIE_URL', 'http://simplepie.org');

/**
 * SimplePie Useragent
 * @see SimplePie::set_useragent()
 */
define('SIMPLEPIE_USERAGENT', SIMPLEPIE_NAME . '/' . SIMPLEPIE_VERSION . ' (Feed Parser; ' . SIMPLEPIE_URL . '; Allow like Gecko) Build/' . SIMPLEPIE_BUILD);

/**
 * SimplePie Linkback
 */
define('SIMPLEPIE_LINKBACK', '<a href="' . SIMPLEPIE_URL . '" title="' . SIMPLEPIE_NAME . ' ' . SIMPLEPIE_VERSION . '">' . SIMPLEPIE_NAME . '</a>');

/**
 * No Autodiscovery
 * @see SimplePie::set_autodiscovery_level()
 */
define('SIMPLEPIE_LOCATOR_NONE', 0);

/**
 * Feed Link Element Autodiscovery
 * @see SimplePie::set_autodiscovery_level()
 */
define('SIMPLEPIE_LOCATOR_AUTODISCOVERY', 1);

/**
 * Local Feed Extension Autodiscovery
 * @see SimplePie::set_autodiscovery_level()
 */
define('SIMPLEPIE_LOCATOR_LOCAL_EXTENSION', 2);

/**
 * Local Feed Body Autodiscovery
 * @see SimplePie::set_autodiscovery_level()
 */
define('SIMPLEPIE_LOCATOR_LOCAL_BODY', 4);

/**
 * Remote Feed Extension Autodiscovery
 * @see SimplePie::set_autodiscovery_level()
 */
define('SIMPLEPIE_LOCATOR_REMOTE_EXTENSION', 8);

/**
 * Remote Feed Body Autodiscovery
 * @see SimplePie::set_autodiscovery_level()
 */
define('SIMPLEPIE_LOCATOR_REMOTE_BODY', 16);

/**
 * All Feed Autodiscovery
 * @see SimplePie::set_autodiscovery_level()
 */
define('SIMPLEPIE_LOCATOR_ALL', 31);

/**
 * No known feed type
 */
define('SIMPLEPIE_TYPE_NONE', 0);

/**
 * RSS 0.90
 */
define('SIMPLEPIE_TYPE_RSS_090', 1);

/**
 * RSS 0.91 (Netscape)
 */
define('SIMPLEPIE_TYPE_RSS_091_NETSCAPE', 2);

/**
 * RSS 0.91 (Userland)
 */
define('SIMPLEPIE_TYPE_RSS_091_USERLAND', 4);

/**
 * RSS 0.91 (both Netscape and Userland)
 */
define('SIMPLEPIE_TYPE_RSS_091', 6);

/**
 * RSS 0.92
 */
define('SIMPLEPIE_TYPE_RSS_092', 8);

/**
 * RSS 0.93
 */
define('SIMPLEPIE_TYPE_RSS_093', 16);

/**
 * RSS 0.94
 */
define('SIMPLEPIE_TYPE_RSS_094', 32);

/**
 * RSS 1.0
 */
define('SIMPLEPIE_TYPE_RSS_10', 64);

/**
 * RSS 2.0
 */
define('SIMPLEPIE_TYPE_RSS_20', 128);

/**
 * RDF-based RSS
 */
define('SIMPLEPIE_TYPE_RSS_RDF', 65);

/**
 * Non-RDF-based RSS (truly intended as syndication format)
 */
define('SIMPLEPIE_TYPE_RSS_SYNDICATION', 190);

/**
 * All RSS
 */
define('SIMPLEPIE_TYPE_RSS_ALL', 255);

/**
 * Atom 0.3
 */
define('SIMPLEPIE_TYPE_ATOM_03', 256);

/**
 * Atom 1.0
 */
define('SIMPLEPIE_TYPE_ATOM_10', 512);

/**
 * All Atom
 */
define('SIMPLEPIE_TYPE_ATOM_ALL', 768);

/**
 * All feed types
 */
define('SIMPLEPIE_TYPE_ALL', 1023);

/**
 * No construct
 */
define('SIMPLEPIE_CONSTRUCT_NONE', 0);

/**
 * Text construct
 */
define('SIMPLEPIE_CONSTRUCT_TEXT', 1);

/**
 * HTML construct
 */
define('SIMPLEPIE_CONSTRUCT_HTML', 2);

/**
 * XHTML construct
 */
define('SIMPLEPIE_CONSTRUCT_XHTML', 4);

/**
 * base64-encoded construct
 */
define('SIMPLEPIE_CONSTRUCT_BASE64', 8);

/**
 * IRI construct
 */
define('SIMPLEPIE_CONSTRUCT_IRI', 16);

/**
 * A construct that might be HTML
 */
define('SIMPLEPIE_CONSTRUCT_MAYBE_HTML', 32);

/**
 * All constructs
 */
define('SIMPLEPIE_CONSTRUCT_ALL', 63);

/**
 * Don't change case
 */
define('SIMPLEPIE_SAME_CASE', 1);

/**
 * Change to lowercase
 */
define('SIMPLEPIE_LOWERCASE', 2);

/**
 * Change to uppercase
 */
define('SIMPLEPIE_UPPERCASE', 4);

/**
 * PCRE for HTML attributes
 */
define('SIMPLEPIE_PCRE_HTML_ATTRIBUTE', '((?:[\x09\x0A\x0B\x0C\x0D\x20]+[^\x09\x0A\x0B\x0C\x0D\x20\x2F\x3E][^\x09\x0A\x0B\x0C\x0D\x20\x2F\x3D\x3E]*(?:[\x09\x0A\x0B\x0C\x0D\x20]*=[\x09\x0A\x0B\x0C\x0D\x20]*(?:"(?:[^"]*)"|\'(?:[^\']*)\'|(?:[^\x09\x0A\x0B\x0C\x0D\x20\x22\x27\x3E][^\x09\x0A\x0B\x0C\x0D\x20\x3E]*)?))?)*)[\x09\x0A\x0B\x0C\x0D\x20]*');

/**
 * PCRE for XML attributes
 */
define('SIMPLEPIE_PCRE_XML_ATTRIBUTE', '((?:\s+(?:(?:[^\s:]+:)?[^\s:]+)\s*=\s*(?:"(?:[^"]*)"|\'(?:[^\']*)\'))*)\s*');

/**
 * XML Namespace
 */
define('SIMPLEPIE_NAMESPACE_XML', 'http://www.w3.org/XML/1998/namespace');

/**
 * Atom 1.0 Namespace
 */
define('SIMPLEPIE_NAMESPACE_ATOM_10', 'http://www.w3.org/2005/Atom');

/**
 * Atom 0.3 Namespace
 */
define('SIMPLEPIE_NAMESPACE_ATOM_03', 'http://purl.org/atom/ns#');

/**
 * RDF Namespace
 */
define('SIMPLEPIE_NAMESPACE_RDF', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#');

/**
 * RSS 0.90 Namespace
 */
define('SIMPLEPIE_NAMESPACE_RSS_090', 'http://my.netscape.com/rdf/simple/0.9/');

/**
 * RSS 1.0 Namespace
 */
define('SIMPLEPIE_NAMESPACE_RSS_10', 'http://purl.org/rss/1.0/');

/**
 * RSS 1.0 Content Module Namespace
 */
define('SIMPLEPIE_NAMESPACE_RSS_10_MODULES_CONTENT', 'http://purl.org/rss/1.0/modules/content/');

/**
 * RSS 2.0 Namespace
 * (Stupid, I know, but I'm certain it will confuse people less with support.)
 */
define('SIMPLEPIE_NAMESPACE_RSS_20', '');

/**
 * DC 1.0 Namespace
 */
define('SIMPLEPIE_NAMESPACE_DC_10', 'http://purl.org/dc/elements/1.0/');

/**
 * DC 1.1 Namespace
 */
define('SIMPLEPIE_NAMESPACE_DC_11', 'http://purl.org/dc/elements/1.1/');

/**
 * W3C Basic Geo (WGS84 lat/long) Vocabulary Namespace
 */
define('SIMPLEPIE_NAMESPACE_W3C_BASIC_GEO', 'http://www.w3.org/2003/01/geo/wgs84_pos#');

/**
 * GeoRSS Namespace
 */
define('SIMPLEPIE_NAMESPACE_GEORSS', 'http://www.georss.org/georss');

/**
 * Media RSS Namespace
 */
define('SIMPLEPIE_NAMESPACE_MEDIARSS', 'http://search.yahoo.com/mrss/');

/**
 * Wrong Media RSS Namespace. Caused by a long-standing typo in the spec.
 */
define('SIMPLEPIE_NAMESPACE_MEDIARSS_WRONG', 'http://search.yahoo.com/mrss');

/**
 * Wrong Media RSS Namespace #2. New namespace introduced in Media RSS 1.5.
 */
define('SIMPLEPIE_NAMESPACE_MEDIARSS_WRONG2', 'http://video.search.yahoo.com/mrss');

/**
 * Wrong Media RSS Namespace #3. A possible typo of the Media RSS 1.5 namespace.
 */
define('SIMPLEPIE_NAMESPACE_MEDIARSS_WRONG3', 'http://video.search.yahoo.com/mrss/');

/**
 * Wrong Media RSS Namespace #4. New spec location after the RSS Advisory Board takes it over, but not a valid namespace.
 */
define('SIMPLEPIE_NAMESPACE_MEDIARSS_WRONG4', 'http://www.rssboard.org/media-rss');

/**
 * Wrong Media RSS Namespace #5. A possible typo of the RSS Advisory Board URL.
 */
define('SIMPLEPIE_NAMESPACE_MEDIARSS_WRONG5', 'http://www.rssboard.org/media-rss/');

/**
 * iTunes RSS Namespace
 */
define('SIMPLEPIE_NAMESPACE_ITUNES', 'http://www.itunes.com/dtds/podcast-1.0.dtd');

/**
 * XHTML Namespace
 */
define('SIMPLEPIE_NAMESPACE_XHTML', 'http://www.w3.org/1999/xhtml');

/**
 * IANA Link Relations Registry
 */
define('SIMPLEPIE_IANA_LINK_RELATIONS_REGISTRY', 'http://www.iana.org/assignments/relation/');

/**
 * No file source
 */
define('SIMPLEPIE_FILE_SOURCE_NONE', 0);

/**
 * Remote file source
 */
define('SIMPLEPIE_FILE_SOURCE_REMOTE', 1);

/**
 * Local file source
 */
define('SIMPLEPIE_FILE_SOURCE_LOCAL', 2);

/**
 * fsockopen() file source
 */
define('SIMPLEPIE_FILE_SOURCE_FSOCKOPEN', 4);

/**
 * cURL file source
 */
define('SIMPLEPIE_FILE_SOURCE_CURL', 8);

/**
 * file_get_contents() file source
 */
define('SIMPLEPIE_FILE_SOURCE_FILE_GET_CONTENTS', 16);



/**
 * SimplePie
 *
 * @package SimplePie
 * @subpackage API
 */
class SimplePie
{
	/**
	 * @var array Raw data
	 * @access private
	 */
	public $data = array();

	/**
	 * @var mixed Error string
	 * @access private
	 */
	public $error;

	/**
	 * @var int HTTP status code
	 * @see SimplePie::status_code()
	 * @access private
	 */
	public $status_code;

	/**
	 * @var object Instance of SimplePie_Sanitize (or other class)
	 * @see SimplePie::set_sanitize_class()
	 * @access private
	 */
	public $sanitize;

	/**
	 * @var string SimplePie Useragent
	 * @see SimplePie::set_useragent()
	 * @access private
	 */
	public $useragent = SIMPLEPIE_USERAGENT;

	/**
	 * @var string Feed URL
	 * @see SimplePie::set_feed_url()
	 * @access private
	 */
	public $feed_url;

	/**
	 * @var string Original feed URL, or new feed URL iff HTTP 301 Moved Permanently
	 * @see SimplePie::subscribe_url()
	 * @access private
	 */
	public $permanent_url = null;

	/**
	 * @var object Instance of SimplePie_File to use as a feed
	 * @see SimplePie::set_file()
	 * @access private
	 */
	public $file;

	/**
	 * @var string Raw feed data
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
	 * @var array Custom curl options
	 * @see SimplePie::set_curl_options()
	 * @access private
	 */
	public $curl_options = array();

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
	public $cache = true;

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
	 * @var string Function that creates the cache filename
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
	 * @var int Feed Autodiscovery Level
	 * @see SimplePie::set_autodiscovery_level()
	 * @access private
	 */
	public $autodiscovery = SIMPLEPIE_LOCATOR_ALL;

	/**
	 * Class registry object
	 *
	 * @var SimplePie_Registry
	 */
	public $registry;

	/**
	 * @var int Maximum number of feeds to check with autodiscovery
	 * @see SimplePie::set_max_checked_feeds()
	 * @access private
	 */
	public $max_checked_feeds = 10;

	/**
	 * @var array All the feeds found during the autodiscovery process
	 * @see SimplePie::get_all_discovered_feeds()
	 * @access private
	 */
	public $all_discovered_feeds = array();

	/**
	 * @var string Web-accessible path to the handler_image.php file.
	 * @see SimplePie::set_image_handler()
	 * @access private
	 */
	public $image_handler = '';

	/**
	 * @var array Stores the URLs when multiple feeds are being initialized.
	 * @see SimplePie::set_feed_url()
	 * @access private
	 */
	public $multifeed_url = array();

	/**
	 * @var array Stores SimplePie objects when multiple feeds initialized.
	 * @access private
	 */
	public $multifeed_objects = array();

	/**
	 * @var array Stores the get_object_vars() array for use with multifeeds.
	 * @see SimplePie::set_feed_url()
	 * @access private
	 */
	public $config_settings = null;

	/**
	 * @var integer Stores the number of items to return per-feed with multifeeds.
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
	 * @var array Stores the default attributes to be stripped by strip_attributes().
	 * @see SimplePie::strip_attributes()
	 * @access private
	 */
	public $strip_attributes = array('bgsound', 'class', 'expr', 'id', 'style', 'onclick', 'onerror', 'onfinish', 'onmouseover', 'onmouseout', 'onfocus', 'onblur', 'lowsrc', 'dynsrc');

	/**
	 * @var array Stores the default attributes to add to different tags by add_attributes().
	 * @see SimplePie::add_attributes()
	 * @access private
	 */
	public $add_attributes = array('audio' => array('preload' => 'none'), 'iframe' => array('sandbox' => 'allow-scripts allow-same-origin'), 'video' => array('preload' => 'none'));

	/**
	 * @var array Stores the default tags to be stripped by strip_htmltags().
	 * @see SimplePie::strip_htmltags()
	 * @access private
	 */
	public $strip_htmltags = array('base', 'blink', 'body', 'doctype', 'embed', 'font', 'form', 'frame', 'frameset', 'html', 'iframe', 'input', 'marquee', 'meta', 'noscript', 'object', 'param', 'script', 'style');

	/**
	 * @var bool Should we throw exceptions, or use the old-style error property?
	 * @access private
	 */
	public $enable_exceptions = false;

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
		if (version_compare(PHP_VERSION, '5.6', '<'))
		{
			trigger_error('Please upgrade to PHP 5.6 or newer.');
			die();
		}

		// Other objects, instances created here so we can set options on them
		$this->sanitize = new SimplePie_Sanitize();
		$this->registry = new SimplePie_Registry();

		if (func_num_args() > 0)
		{
			$level = defined('E_USER_DEPRECATED') ? E_USER_DEPRECATED : E_USER_WARNING;
			trigger_error('Passing parameters to the constructor is no longer supported. Please use set_feed_url(), set_cache_location(), and set_cache_duration() directly.', $level);

			$args = func_get_args();
			switch (count($args)) {
				case 3:
					$this->set_cache_duration($args[2]);
				case 2:
					$this->set_cache_location($args[1]);
				case 1:
					$this->set_feed_url($args[0]);
					$this->init();
			}
		}
	}

	/**
	 * Used for converting object to a string
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
		if (!gc_enabled())
		{
			if (!empty($this->data['items']))
			{
				foreach ($this->data['items'] as $item)
				{
					$item->__destruct();
				}
				unset($item, $this->data['items']);
			}
			if (!empty($this->data['ordered_items']))
			{
				foreach ($this->data['ordered_items'] as $item)
				{
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
	 */
	public function force_feed($enable = false)
	{
		$this->force_feed = (bool) $enable;
	}

	/**
	 * Set the URL of the feed you want to parse
	 *
	 * This allows you to enter the URL of the feed you want to parse, or the
	 * website you want to try to use auto-discovery on. This takes priority
	 * over any set raw data.
	 *
	 * You can set multiple feeds to mash together by passing an array instead
	 * of a string for the $url. Remember that with each additional feed comes
	 * additional processing and resources.
	 *
	 * @since 1.0 Preview Release
	 * @see set_raw_data()
	 * @param string|array $url This is the URL (or array of URLs) that you want to parse.
	 */
	public function set_feed_url($url)
	{
		$this->multifeed_url = array();
		if (is_array($url))
		{
			foreach ($url as $value)
			{
				$this->multifeed_url[] = $this->registry->call('Misc', 'fix_protocol', array($value, 1));
			}
		}
		else
		{
			$this->feed_url = $this->registry->call('Misc', 'fix_protocol', array($url, 1));
			$this->permanent_url = $this->feed_url;
		}
	}

	/**
	 * Set an instance of {@see SimplePie_File} to use as a feed
	 *
	 * @param SimplePie_File &$file
	 * @return bool True on success, false on failure
	 */
	public function set_file(&$file)
	{
		if ($file instanceof SimplePie_File)
		{
			$this->feed_url = $file->url;
			$this->permanent_url = $this->feed_url;
			$this->file =& $file;
			return true;
		}
		return false;
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
	 */
	public function set_raw_data($data)
	{
		$this->raw_data = $data;
	}

	/**
	 * Set the default timeout for fetching remote feeds
	 *
	 * This allows you to change the maximum time the feed's server to respond
	 * and send the feed back.
	 *
	 * @since 1.0 Beta 3
	 * @param int $timeout The maximum number of seconds to spend waiting to retrieve a feed.
	 */
	public function set_timeout($timeout = 10)
	{
		$this->timeout = (int) $timeout;
	}

	/**
	 * Set custom curl options
	 *
	 * This allows you to change default curl options
	 *
	 * @since 1.0 Beta 3
	 * @param array $curl_options Curl options to add to default settings
	 */
	public function set_curl_options(array $curl_options = array())
	{
		$this->curl_options = $curl_options;
	}

	/**
	 * Force SimplePie to use fsockopen() instead of cURL
	 *
	 * @since 1.0 Beta 3
	 * @param bool $enable Force fsockopen() to be used
	 */
	public function force_fsockopen($enable = false)
	{
		$this->force_fsockopen = (bool) $enable;
	}

	/**
	 * Enable/disable caching in SimplePie.
	 *
	 * This option allows you to disable caching all-together in SimplePie.
	 * However, disabling the cache can lead to longer load times.
	 *
	 * @since 1.0 Preview Release
	 * @param bool $enable Enable caching
	 */
	public function enable_cache($enable = true)
	{
		$this->cache = (bool) $enable;
	}

	/**
	 * SimplePie to continue to fall back to expired cache, if enabled, when
	 * feed is unavailable.
	 *
	 * This tells SimplePie to ignore any file errors and fall back to cache
	 * instead. This only works if caching is enabled and cached content
	 * still exists.

	 * @param bool $enable Force use of cache on fail.
	 */
	public function force_cache_fallback($enable = false)
	{
		$this->force_cache_fallback= (bool) $enable;
	}

	/**
	 * Set the length of time (in seconds) that the contents of a feed will be
	 * cached
	 *
	 * @param int $seconds The feed content cache duration
	 */
	public function set_cache_duration($seconds = 3600)
	{
		$this->cache_duration = (int) $seconds;
	}

	/**
	 * Set the length of time (in seconds) that the autodiscovered feed URL will
	 * be cached
	 *
	 * @param int $seconds The autodiscovered feed URL cache duration.
	 */
	public function set_autodiscovery_cache_duration($seconds = 604800)
	{
		$this->autodiscovery_cache_duration = (int) $seconds;
	}

	/**
	 * Set the file system location where the cached files should be stored
	 *
	 * @param string $location The file system location.
	 */
	public function set_cache_location($location = './cache')
	{
		$this->cache_location = (string) $location;
	}

	/**
	 * Return the filename (i.e. hash, without path and without extension) of the file to cache a given URL.
	 * @param string $url The URL of the feed to be cached.
	 * @return string A filename (i.e. hash, without path and without extension).
	 */
	public function get_cache_filename($url)
	{
		// Append custom parameters to the URL to avoid cache pollution in case of multiple calls with different parameters.
		$url .= $this->force_feed ? '#force_feed' : '';
		$options = array();
		if ($this->timeout != 10)
		{
			$options[CURLOPT_TIMEOUT] = $this->timeout;
		}
		if ($this->useragent !== SIMPLEPIE_USERAGENT)
		{
			$options[CURLOPT_USERAGENT] = $this->useragent;
		}
		if (!empty($this->curl_options))
		{
			foreach ($this->curl_options as $k => $v)
			{
				$options[$k] = $v;
			}
		}
		if (!empty($options))
		{
			ksort($options);
			$url .= '#' . urlencode(var_export($options, true));
		}
		return call_user_func($this->cache_name_function, $url);
	}

	/**
	 * Set whether feed items should be sorted into reverse chronological order
	 *
	 * @param bool $enable Sort as reverse chronological order.
	 */
	public function enable_order_by_date($enable = true)
	{
		$this->order_by_date = (bool) $enable;
	}

	/**
	 * Set the character encoding used to parse the feed
	 *
	 * This overrides the encoding reported by the feed, however it will fall
	 * back to the normal encoding detection if the override fails
	 *
	 * @param string $encoding Character encoding
	 */
	public function set_input_encoding($encoding = false)
	{
		if ($encoding)
		{
			$this->input_encoding = (string) $encoding;
		}
		else
		{
			$this->input_encoding = false;
		}
	}

	/**
	 * Set how much feed autodiscovery to do
	 *
	 * @see SIMPLEPIE_LOCATOR_NONE
	 * @see SIMPLEPIE_LOCATOR_AUTODISCOVERY
	 * @see SIMPLEPIE_LOCATOR_LOCAL_EXTENSION
	 * @see SIMPLEPIE_LOCATOR_LOCAL_BODY
	 * @see SIMPLEPIE_LOCATOR_REMOTE_EXTENSION
	 * @see SIMPLEPIE_LOCATOR_REMOTE_BODY
	 * @see SIMPLEPIE_LOCATOR_ALL
	 * @param int $level Feed Autodiscovery Level (level can be a combination of the above constants, see bitwise OR operator)
	 */
	public function set_autodiscovery_level($level = SIMPLEPIE_LOCATOR_ALL)
	{
		$this->autodiscovery = (int) $level;
	}

	/**
	 * Get the class registry
	 *
	 * Use this to override SimplePie's default classes
	 * @see SimplePie_Registry
	 * @return SimplePie_Registry
	 */
	public function &get_registry()
	{
		return $this->registry;
	}

	/**#@+
	 * Useful when you are overloading or extending SimplePie's default classes.
	 *
	 * @deprecated Use {@see get_registry()} instead
	 * @link http://php.net/manual/en/language.oop5.basic.php#language.oop5.basic.extends PHP5 extends documentation
	 * @param string $class Name of custom class
	 * @return boolean True on success, false otherwise
	 */
	/**
	 * Set which class SimplePie uses for caching
	 */
	public function set_cache_class($class = 'SimplePie_Cache')
	{
		return $this->registry->register('Cache', $class, true);
	}

	/**
	 * Set which class SimplePie uses for auto-discovery
	 */
	public function set_locator_class($class = 'SimplePie_Locator')
	{
		return $this->registry->register('Locator', $class, true);
	}

	/**
	 * Set which class SimplePie uses for XML parsing
	 */
	public function set_parser_class($class = 'SimplePie_Parser')
	{
		return $this->registry->register('Parser', $class, true);
	}

	/**
	 * Set which class SimplePie uses for remote file fetching
	 */
	public function set_file_class($class = 'SimplePie_File')
	{
		return $this->registry->register('File', $class, true);
	}

	/**
	 * Set which class SimplePie uses for data sanitization
	 */
	public function set_sanitize_class($class = 'SimplePie_Sanitize')
	{
		return $this->registry->register('Sanitize', $class, true);
	}

	/**
	 * Set which class SimplePie uses for handling feed items
	 */
	public function set_item_class($class = 'SimplePie_Item')
	{
		return $this->registry->register('Item', $class, true);
	}

	/**
	 * Set which class SimplePie uses for handling author data
	 */
	public function set_author_class($class = 'SimplePie_Author')
	{
		return $this->registry->register('Author', $class, true);
	}

	/**
	 * Set which class SimplePie uses for handling category data
	 */
	public function set_category_class($class = 'SimplePie_Category')
	{
		return $this->registry->register('Category', $class, true);
	}

	/**
	 * Set which class SimplePie uses for feed enclosures
	 */
	public function set_enclosure_class($class = 'SimplePie_Enclosure')
	{
		return $this->registry->register('Enclosure', $class, true);
	}

	/**
	 * Set which class SimplePie uses for `<media:text>` captions
	 */
	public function set_caption_class($class = 'SimplePie_Caption')
	{
		return $this->registry->register('Caption', $class, true);
	}

	/**
	 * Set which class SimplePie uses for `<media:copyright>`
	 */
	public function set_copyright_class($class = 'SimplePie_Copyright')
	{
		return $this->registry->register('Copyright', $class, true);
	}

	/**
	 * Set which class SimplePie uses for `<media:credit>`
	 */
	public function set_credit_class($class = 'SimplePie_Credit')
	{
		return $this->registry->register('Credit', $class, true);
	}

	/**
	 * Set which class SimplePie uses for `<media:rating>`
	 */
	public function set_rating_class($class = 'SimplePie_Rating')
	{
		return $this->registry->register('Rating', $class, true);
	}

	/**
	 * Set which class SimplePie uses for `<media:restriction>`
	 */
	public function set_restriction_class($class = 'SimplePie_Restriction')
	{
		return $this->registry->register('Restriction', $class, true);
	}

	/**
	 * Set which class SimplePie uses for content-type sniffing
	 */
	public function set_content_type_sniffer_class($class = 'SimplePie_Content_Type_Sniffer')
	{
		return $this->registry->register('Content_Type_Sniffer', $class, true);
	}

	/**
	 * Set which class SimplePie uses item sources
	 */
	public function set_source_class($class = 'SimplePie_Source')
	{
		return $this->registry->register('Source', $class, true);
	}
	/**#@-*/

	/**
	 * Set the user agent string
	 *
	 * @param string $ua New user agent string.
	 */
	public function set_useragent($ua = SIMPLEPIE_USERAGENT)
	{
		$this->useragent = (string) $ua;
	}

	/**
	 * Set callback function to create cache filename with
	 *
	 * @param mixed $function Callback function
	 */
	public function set_cache_name_function($function = 'md5')
	{
		if (is_callable($function))
		{
			$this->cache_name_function = $function;
		}
	}

	/**
	 * Set options to make SP as fast as possible
	 *
	 * Forgoes a substantial amount of data sanitization in favor of speed. This
	 * turns SimplePie into a dumb parser of feeds.
	 *
	 * @param bool $set Whether to set them or not
	 */
	public function set_stupidly_fast($set = false)
	{
		if ($set)
		{
			$this->enable_order_by_date(false);
			$this->remove_div(false);
			$this->strip_comments(false);
			$this->strip_htmltags(false);
			$this->strip_attributes(false);
			$this->add_attributes(false);
			$this->set_image_handler(false);
			$this->set_https_domains(array());
		}
	}

	/**
	 * Set maximum number of feeds to check with autodiscovery
	 *
	 * @param int $max Maximum number of feeds to check
	 */
	public function set_max_checked_feeds($max = 10)
	{
		$this->max_checked_feeds = (int) $max;
	}

	public function remove_div($enable = true)
	{
		$this->sanitize->remove_div($enable);
	}

	public function strip_htmltags($tags = '', $encode = null)
	{
		if ($tags === '')
		{
			$tags = $this->strip_htmltags;
		}
		$this->sanitize->strip_htmltags($tags);
		if ($encode !== null)
		{
			$this->sanitize->encode_instead_of_strip($tags);
		}
	}

	public function encode_instead_of_strip($enable = true)
	{
		$this->sanitize->encode_instead_of_strip($enable);
	}

	public function strip_attributes($attribs = '')
	{
		if ($attribs === '')
		{
			$attribs = $this->strip_attributes;
		}
		$this->sanitize->strip_attributes($attribs);
	}

	public function add_attributes($attribs = '')
	{
		if ($attribs === '')
		{
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
	 */
	public function set_output_encoding($encoding = 'UTF-8')
	{
		$this->sanitize->set_output_encoding($encoding);
	}

	public function strip_comments($strip = false)
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
	 * @param array|null $element_attribute Element/attribute key/value pairs, null for default
	 */
	public function set_url_replacements($element_attribute = null)
	{
		$this->sanitize->set_url_replacements($element_attribute);
	}

	/**
	 * Set the list of domains for which to force HTTPS.
	 * @see SimplePie_Sanitize::set_https_domains()
	 * @param array List of HTTPS domains. Example array('biz', 'example.com', 'example.org', 'www.example.net').
	 */
	public function set_https_domains($domains = array())
	{
		if (is_array($domains))
		{
			$this->sanitize->set_https_domains($domains);
		}
	}

	/**
	 * Set the handler to enable the display of cached images.
	 *
	 * @param string $page Web-accessible path to the handler_image.php file.
	 * @param string $qs The query string that the value should be passed to.
	 */
	public function set_image_handler($page = false, $qs = 'i')
	{
		if ($page !== false)
		{
			$this->sanitize->set_image_handler($page . '?' . $qs . '=');
		}
		else
		{
			$this->image_handler = '';
		}
	}

	/**
	 * Set the limit for items returned per-feed with multifeeds
	 *
	 * @param integer $limit The maximum number of items to return.
	 */
	public function set_item_limit($limit = 0)
	{
		$this->item_limit = (int) $limit;
	}

	/**
	 * Enable throwing exceptions
	 *
	 * @param boolean $enable Should we throw exceptions, or use the old-style error property?
	 */
	public function enable_exceptions($enable = true)
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
	 * @return boolean True if successful, false otherwise
	 */
	public function init()
	{
		// Check absolute bare minimum requirements.
		if (!extension_loaded('xml') || !extension_loaded('pcre'))
		{
			$this->error = 'XML or PCRE extensions not loaded!';
			return false;
		}
		// Then check the xml extension is sane (i.e., libxml 2.7.x issue on PHP < 5.2.9 and libxml 2.7.0 to 2.7.2 on any version) if we don't have xmlreader.
		elseif (!extension_loaded('xmlreader'))
		{
			static $xml_is_sane = null;
			if ($xml_is_sane === null)
			{
				$parser_check = xml_parser_create();
				xml_parse_into_struct($parser_check, '<foo>&amp;</foo>', $values);
				xml_parser_free($parser_check);
				$xml_is_sane = isset($values[0]['value']);
			}
			if (!$xml_is_sane)
			{
				return false;
			}
		}

		// The default sanitize class gets set in the constructor, check if it has
		// changed.
		if ($this->registry->get_class('Sanitize') !== 'SimplePie_Sanitize') {
			$this->sanitize = $this->registry->create('Sanitize');
		}
		if (method_exists($this->sanitize, 'set_registry'))
		{
			$this->sanitize->set_registry($this->registry);
		}

		// Pass whatever was set with config options over to the sanitizer.
		// Pass the classes in for legacy support; new classes should use the registry instead
		$this->sanitize->pass_cache_data($this->cache, $this->cache_location, $this->cache_name_function, $this->registry->get_class('Cache'));
		$this->sanitize->pass_file_data($this->registry->get_class('File'), $this->timeout, $this->useragent, $this->force_fsockopen, $this->curl_options);

		if (!empty($this->multifeed_url))
		{
			$i = 0;
			$success = 0;
			$this->multifeed_objects = array();
			$this->error = array();
			foreach ($this->multifeed_url as $url)
			{
				$this->multifeed_objects[$i] = clone $this;
				$this->multifeed_objects[$i]->set_feed_url($url);
				$single_success = $this->multifeed_objects[$i]->init();
				$success |= $single_success;
				if (!$single_success)
				{
					$this->error[$i] = $this->multifeed_objects[$i]->error();
				}
				$i++;
			}
			return (bool) $success;
		}
		elseif ($this->feed_url === null && $this->raw_data === null)
		{
			return false;
		}

		$this->error = null;
		$this->data = array();
		$this->check_modified = false;
		$this->multifeed_objects = array();
		$cache = false;

		if ($this->feed_url !== null)
		{
			$parsed_feed_url = $this->registry->call('Misc', 'parse_url', array($this->feed_url));

			// Decide whether to enable caching
			if ($this->cache && $parsed_feed_url['scheme'] !== '')
			{
				$filename = $this->get_cache_filename($this->feed_url);
				$cache = $this->registry->call('Cache', 'get_handler', array($this->cache_location, $filename, 'spc'));
			}

			// Fetch the data via SimplePie_File into $this->raw_data
			if (($fetched = $this->fetch_data($cache)) === true)
			{
				return true;
			}
			elseif ($fetched === false) {
				return false;
			}

			list($headers, $sniffed) = $fetched;
		}

		// Empty response check
		if(empty($this->raw_data)){
			$this->error = "A feed could not be found at `$this->feed_url`. Empty body.";
			$this->registry->call('Misc', 'error', array($this->error, E_USER_NOTICE, __FILE__, __LINE__));
			return false;
		}

		// Set up array of possible encodings
		$encodings = array();

		// First check to see if input has been overridden.
		if ($this->input_encoding !== false)
		{
			$encodings[] = strtoupper($this->input_encoding);
		}

		$application_types = array('application/xml', 'application/xml-dtd', 'application/xml-external-parsed-entity');
		$text_types = array('text/xml', 'text/xml-external-parsed-entity');

		// RFC 3023 (only applies to sniffed content)
		if (isset($sniffed))
		{
			if (in_array($sniffed, $application_types) || substr($sniffed, 0, 12) === 'application/' && substr($sniffed, -4) === '+xml')
			{
				if (isset($headers['content-type']) && preg_match('/;\x20?charset=([^;]*)/i', $headers['content-type'], $charset))
				{
					$encodings[] = strtoupper($charset[1]);
				}
				$encodings = array_merge($encodings, $this->registry->call('Misc', 'xml_encoding', array($this->raw_data, &$this->registry)));
				$encodings[] = 'UTF-8';
			}
			elseif (in_array($sniffed, $text_types) || substr($sniffed, 0, 5) === 'text/' && substr($sniffed, -4) === '+xml')
			{
				if (isset($headers['content-type']) && preg_match('/;\x20?charset=([^;]*)/i', $headers['content-type'], $charset))
				{
					$encodings[] = strtoupper($charset[1]);
				}
				$encodings[] = 'US-ASCII';
			}
			// Text MIME-type default
			elseif (substr($sniffed, 0, 5) === 'text/')
			{
				$encodings[] = 'UTF-8';
			}
		}

		// Fallback to XML 1.0 Appendix F.1/UTF-8/ISO-8859-1
		$encodings = array_merge($encodings, $this->registry->call('Misc', 'xml_encoding', array($this->raw_data, &$this->registry)));
		$encodings[] = 'UTF-8';
		$encodings[] = 'ISO-8859-1';

		// There's no point in trying an encoding twice
		$encodings = array_unique($encodings);

		// Loop through each possible encoding, till we return something, or run out of possibilities
		foreach ($encodings as $encoding)
		{
			// Change the encoding to UTF-8 (as we always use UTF-8 internally)
			if ($utf8_data = $this->registry->call('Misc', 'change_encoding', array($this->raw_data, $encoding, 'UTF-8')))
			{
				// Create new parser
				$parser = $this->registry->create('Parser');

				// If it's parsed fine
				if ($parser->parse($utf8_data, 'UTF-8', $this->permanent_url))
				{
					$this->data = $parser->get_data();
					if (!($this->get_type() & ~SIMPLEPIE_TYPE_NONE))
					{
						$this->error = "A feed could not be found at `$this->feed_url`. This does not appear to be a valid RSS or Atom feed.";
						$this->registry->call('Misc', 'error', array($this->error, E_USER_NOTICE, __FILE__, __LINE__));
						return false;
					}

					if (isset($headers))
					{
						$this->data['headers'] = $headers;
					}
					$this->data['build'] = SIMPLEPIE_BUILD;

					// Cache the file if caching is enabled
					if ($cache && !$cache->save($this))
					{
						trigger_error("$this->cache_location is not writable. Make sure you've set the correct relative or absolute path, and that the location is server-writable.", E_USER_WARNING);
					}
					return true;
				}
			}
		}

		if (isset($parser))
		{
			// We have an error, just set SimplePie_Misc::error to it and quit
			$this->error = $this->feed_url;
			$this->error .= sprintf(' is invalid XML, likely due to invalid characters. XML error: %s at line %d, column %d', $parser->get_error_string(), $parser->get_current_line(), $parser->get_current_column());
		}
		else
		{
			$this->error = 'The data could not be converted to UTF-8.';
			if (!extension_loaded('mbstring') && !extension_loaded('iconv') && !class_exists('\UConverter')) {
				$this->error .= ' You MUST have either the iconv, mbstring or intl (PHP 5.5+) extension installed and enabled.';
			} else {
				$missingExtensions = array();
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

		$this->registry->call('Misc', 'error', array($this->error, E_USER_NOTICE, __FILE__, __LINE__));

		return false;
	}

	/**
	 * Fetch the data via SimplePie_File
	 *
	 * If the data is already cached, attempt to fetch it from there instead
	 * @param SimplePie_Cache_Base|false $cache Cache handler, or false to not load from the cache
	 * @return array|true Returns true if the data was loaded from the cache, or an array of HTTP headers and sniffed type
	 */
	protected function fetch_data(&$cache)
	{
		// If it's enabled, use the cache
		if ($cache)
		{
			// Load the Cache
			$this->data = $cache->load();
			if (!empty($this->data))
			{
				// If the cache is for an outdated build of SimplePie
				if (!isset($this->data['build']) || $this->data['build'] !== SIMPLEPIE_BUILD)
				{
					$cache->unlink();
					$this->data = array();
				}
				// If we've hit a collision just rerun it with caching disabled
				elseif (isset($this->data['url']) && $this->data['url'] !== $this->feed_url)
				{
					$cache = false;
					$this->data = array();
				}
				// If we've got a non feed_url stored (if the page isn't actually a feed, or is a redirect) use that URL.
				elseif (isset($this->data['feed_url']))
				{
					// If the autodiscovery cache is still valid use it.
					if ($cache->mtime() + $this->autodiscovery_cache_duration > time())
					{
						// Do not need to do feed autodiscovery yet.
						if ($this->data['feed_url'] !== $this->data['url'])
						{
							$this->set_feed_url($this->data['feed_url']);
							return $this->init();
						}

						$cache->unlink();
						$this->data = array();
					}
				}
				// Check if the cache has been updated
				elseif ($cache->mtime() + $this->cache_duration < time())
				{
					// Want to know if we tried to send last-modified and/or etag headers
					// when requesting this file. (Note that it's up to the file to
					// support this, but we don't always send the headers either.)
					$this->check_modified = true;
					if (isset($this->data['headers']['last-modified']) || isset($this->data['headers']['etag']))
					{
						$headers = array(
							'Accept' => 'application/atom+xml, application/rss+xml, application/rdf+xml;q=0.9, application/xml;q=0.8, text/xml;q=0.8, text/html;q=0.7, unknown/unknown;q=0.1, application/unknown;q=0.1, */*;q=0.1',
						);
						if (isset($this->data['headers']['last-modified']))
						{
							$headers['if-modified-since'] = $this->data['headers']['last-modified'];
						}
						if (isset($this->data['headers']['etag']))
						{
							$headers['if-none-match'] = $this->data['headers']['etag'];
						}

						$file = $this->registry->create('File', array($this->feed_url, $this->timeout/10, 5, $headers, $this->useragent, $this->force_fsockopen, $this->curl_options));
						$this->status_code = $file->status_code;

						if ($file->success)
						{
							if ($file->status_code === 304)
							{
								// Set raw_data to false here too, to signify that the cache
								// is still valid.
								$this->raw_data = false;
								$cache->touch();
								return true;
							}
						}
						else
						{
							$this->check_modified = false;
							if($this->force_cache_fallback)
							{
								$cache->touch();
								return true;
							}

							unset($file);
						}
					}
				}
				// If the cache is still valid, just return true
				else
				{
					$this->raw_data = false;
					return true;
				}
			}
			// If the cache is empty, delete it
			else
			{
				$cache->unlink();
				$this->data = array();
			}
		}
		// If we don't already have the file (it'll only exist if we've opened it to check if the cache has been modified), open it.
		if (!isset($file))
		{
			if ($this->file instanceof SimplePie_File && $this->file->url === $this->feed_url)
			{
				$file =& $this->file;
			}
			else
			{
				$headers = array(
					'Accept' => 'application/atom+xml, application/rss+xml, application/rdf+xml;q=0.9, application/xml;q=0.8, text/xml;q=0.8, text/html;q=0.7, unknown/unknown;q=0.1, application/unknown;q=0.1, */*;q=0.1',
				);
				$file = $this->registry->create('File', array($this->feed_url, $this->timeout, 5, $headers, $this->useragent, $this->force_fsockopen, $this->curl_options));
			}
		}
		$this->status_code = $file->status_code;

		// If the file connection has an error, set SimplePie::error to that and quit
		if (!$file->success && !($file->method & SIMPLEPIE_FILE_SOURCE_REMOTE === 0 || ($file->status_code === 200 || $file->status_code > 206 && $file->status_code < 300)))
		{
			$this->error = $file->error;
			return !empty($this->data);
		}

		if (!$this->force_feed)
		{
			// Check if the supplied URL is a feed, if it isn't, look for it.
			$locate = $this->registry->create('Locator', array(&$file, $this->timeout, $this->useragent, $this->max_checked_feeds, $this->force_fsockopen, $this->curl_options));

			if (!$locate->is_feed($file))
			{
				$copyStatusCode = $file->status_code;
				$copyContentType = $file->headers['content-type'];
				try
				{
					$microformats = false;
					if (class_exists('DOMXpath') && function_exists('Mf2\parse')) {
						$doc = new DOMDocument();
						@$doc->loadHTML($file->body);
						$xpath = new DOMXpath($doc);
						// Check for both h-feed and h-entry, as both a feed with no entries
						// and a list of entries without an h-feed wrapper are both valid.
						$query = '//*[contains(concat(" ", @class, " "), " h-feed ") or '.
							'contains(concat(" ", @class, " "), " h-entry ")]';
						$result = $xpath->query($query);
						$microformats = $result->length !== 0;
					}
					// Now also do feed discovery, but if microformats were found don't
					// overwrite the current value of file.
					$discovered = $locate->find($this->autodiscovery,
					                            $this->all_discovered_feeds);
					if ($microformats)
					{
						if ($hub = $locate->get_rel_link('hub'))
						{
							$self = $locate->get_rel_link('self');
							$this->store_links($file, $hub, $self);
						}
						// Push the current file onto all_discovered feeds so the user can
						// be shown this as one of the options.
						if (isset($this->all_discovered_feeds)) {
							$this->all_discovered_feeds[] = $file;
						}
					}
					else
					{
						if ($discovered)
						{
							$file = $discovered;
						}
						else
						{
							// We need to unset this so that if SimplePie::set_file() has
							// been called that object is untouched
							unset($file);
							$this->error = "A feed could not be found at `$this->feed_url`; the status code is `$copyStatusCode` and content-type is `$copyContentType`";
							$this->registry->call('Misc', 'error', array($this->error, E_USER_NOTICE, __FILE__, __LINE__));
							return false;
						}
					}
				}
				catch (SimplePie_Exception $e)
				{
					// We need to unset this so that if SimplePie::set_file() has been called that object is untouched
					unset($file);
					// This is usually because DOMDocument doesn't exist
					$this->error = $e->getMessage();
					$this->registry->call('Misc', 'error', array($this->error, E_USER_NOTICE, $e->getFile(), $e->getLine()));
					return false;
				}
				if ($cache)
				{
					$this->data = array('url' => $this->feed_url, 'feed_url' => $file->url, 'build' => SIMPLEPIE_BUILD);
					if (!$cache->save($this))
					{
						trigger_error("$this->cache_location is not writable. Make sure you've set the correct relative or absolute path, and that the location is server-writable.", E_USER_WARNING);
					}
					$cache = $this->registry->call('Cache', 'get_handler', array($this->cache_location, call_user_func($this->cache_name_function, $file->url), 'spc'));
				}
			}
			$this->feed_url = $file->url;
			$locate = null;
		}

		$this->raw_data = $file->body;
		$this->permanent_url = $file->permanent_url;
		$headers = $file->headers;
		$sniffer = $this->registry->create('Content_Type_Sniffer', array(&$file));
		$sniffed = $sniffer->get_type();

		return array($headers, $sniffed);
	}

	/**
	 * Get the error message for the occurred error
	 *
	 * @return string|array Error message, or array of messages for multifeeds
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
	 * @return string|boolean Raw XML data, false if the cache is used
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
	 * Send the Content-Type header with correct encoding
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
	 */
	public function handle_content_type($mime = 'text/html')
	{
		if (!headers_sent())
		{
			$header = "Content-Type: $mime;";
			if ($this->get_encoding())
			{
				$header .= ' charset=' . $this->get_encoding();
			}
			else
			{
				$header .= ' charset=UTF-8';
			}
			header($header);
		}
	}

	/**
	 * Get the type of the feed
	 *
	 * This returns a SIMPLEPIE_TYPE_* constant, which can be tested against
	 * using {@link http://php.net/language.operators.bitwise bitwise operators}
	 *
	 * @since 0.8 (usage changed to using constants in 1.0)
	 * @see SIMPLEPIE_TYPE_NONE Unknown.
	 * @see SIMPLEPIE_TYPE_RSS_090 RSS 0.90.
	 * @see SIMPLEPIE_TYPE_RSS_091_NETSCAPE RSS 0.91 (Netscape).
	 * @see SIMPLEPIE_TYPE_RSS_091_USERLAND RSS 0.91 (Userland).
	 * @see SIMPLEPIE_TYPE_RSS_091 RSS 0.91.
	 * @see SIMPLEPIE_TYPE_RSS_092 RSS 0.92.
	 * @see SIMPLEPIE_TYPE_RSS_093 RSS 0.93.
	 * @see SIMPLEPIE_TYPE_RSS_094 RSS 0.94.
	 * @see SIMPLEPIE_TYPE_RSS_10 RSS 1.0.
	 * @see SIMPLEPIE_TYPE_RSS_20 RSS 2.0.x.
	 * @see SIMPLEPIE_TYPE_RSS_RDF RDF-based RSS.
	 * @see SIMPLEPIE_TYPE_RSS_SYNDICATION Non-RDF-based RSS (truly intended as syndication format).
	 * @see SIMPLEPIE_TYPE_RSS_ALL Any version of RSS.
	 * @see SIMPLEPIE_TYPE_ATOM_03 Atom 0.3.
	 * @see SIMPLEPIE_TYPE_ATOM_10 Atom 1.0.
	 * @see SIMPLEPIE_TYPE_ATOM_ALL Any version of Atom.
	 * @see SIMPLEPIE_TYPE_ALL Any known/supported feed type.
	 * @return int SIMPLEPIE_TYPE_* constant
	 */
	public function get_type()
	{
		if (!isset($this->data['type']))
		{
			$this->data['type'] = SIMPLEPIE_TYPE_ALL;
			if (isset($this->data['child'][SIMPLEPIE_NAMESPACE_ATOM_10]['feed']))
			{
				$this->data['type'] &= SIMPLEPIE_TYPE_ATOM_10;
			}
			elseif (isset($this->data['child'][SIMPLEPIE_NAMESPACE_ATOM_03]['feed']))
			{
				$this->data['type'] &= SIMPLEPIE_TYPE_ATOM_03;
			}
			elseif (isset($this->data['child'][SIMPLEPIE_NAMESPACE_RDF]['RDF']))
			{
				if (isset($this->data['child'][SIMPLEPIE_NAMESPACE_RDF]['RDF'][0]['child'][SIMPLEPIE_NAMESPACE_RSS_10]['channel'])
				|| isset($this->data['child'][SIMPLEPIE_NAMESPACE_RDF]['RDF'][0]['child'][SIMPLEPIE_NAMESPACE_RSS_10]['image'])
				|| isset($this->data['child'][SIMPLEPIE_NAMESPACE_RDF]['RDF'][0]['child'][SIMPLEPIE_NAMESPACE_RSS_10]['item'])
				|| isset($this->data['child'][SIMPLEPIE_NAMESPACE_RDF]['RDF'][0]['child'][SIMPLEPIE_NAMESPACE_RSS_10]['textinput']))
				{
					$this->data['type'] &= SIMPLEPIE_TYPE_RSS_10;
				}
				if (isset($this->data['child'][SIMPLEPIE_NAMESPACE_RDF]['RDF'][0]['child'][SIMPLEPIE_NAMESPACE_RSS_090]['channel'])
				|| isset($this->data['child'][SIMPLEPIE_NAMESPACE_RDF]['RDF'][0]['child'][SIMPLEPIE_NAMESPACE_RSS_090]['image'])
				|| isset($this->data['child'][SIMPLEPIE_NAMESPACE_RDF]['RDF'][0]['child'][SIMPLEPIE_NAMESPACE_RSS_090]['item'])
				|| isset($this->data['child'][SIMPLEPIE_NAMESPACE_RDF]['RDF'][0]['child'][SIMPLEPIE_NAMESPACE_RSS_090]['textinput']))
				{
					$this->data['type'] &= SIMPLEPIE_TYPE_RSS_090;
				}
			}
			elseif (isset($this->data['child'][SIMPLEPIE_NAMESPACE_RSS_20]['rss']))
			{
				$this->data['type'] &= SIMPLEPIE_TYPE_RSS_ALL;
				if (isset($this->data['child'][SIMPLEPIE_NAMESPACE_RSS_20]['rss'][0]['attribs']['']['version']))
				{
					switch (trim($this->data['child'][SIMPLEPIE_NAMESPACE_RSS_20]['rss'][0]['attribs']['']['version']))
					{
						case '0.91':
							$this->data['type'] &= SIMPLEPIE_TYPE_RSS_091;
							if (isset($this->data['child'][SIMPLEPIE_NAMESPACE_RSS_20]['rss'][0]['child'][SIMPLEPIE_NAMESPACE_RSS_20]['skiphours']['hour'][0]['data']))
							{
								switch (trim($this->data['child'][SIMPLEPIE_NAMESPACE_RSS_20]['rss'][0]['child'][SIMPLEPIE_NAMESPACE_RSS_20]['skiphours']['hour'][0]['data']))
								{
									case '0':
										$this->data['type'] &= SIMPLEPIE_TYPE_RSS_091_NETSCAPE;
										break;

									case '24':
										$this->data['type'] &= SIMPLEPIE_TYPE_RSS_091_USERLAND;
										break;
								}
							}
							break;

						case '0.92':
							$this->data['type'] &= SIMPLEPIE_TYPE_RSS_092;
							break;

						case '0.93':
							$this->data['type'] &= SIMPLEPIE_TYPE_RSS_093;
							break;

						case '0.94':
							$this->data['type'] &= SIMPLEPIE_TYPE_RSS_094;
							break;

						case '2.0':
							$this->data['type'] &= SIMPLEPIE_TYPE_RSS_20;
							break;
					}
				}
			}
			else
			{
				$this->data['type'] = SIMPLEPIE_TYPE_NONE;
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
	public function subscribe_url($permanent = false)
	{
		if ($permanent)
		{
			if ($this->permanent_url !== null)
			{
				// sanitize encodes ampersands which are required when used in a url.
				return str_replace('&amp;', '&',
				                   $this->sanitize($this->permanent_url,
				                                   SIMPLEPIE_CONSTRUCT_IRI));
			}
		}
		else
		{
			if ($this->feed_url !== null)
			{
				return str_replace('&amp;', '&',
				                   $this->sanitize($this->feed_url,
				                                   SIMPLEPIE_CONSTRUCT_IRI));
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
	 * $group = $item->get_item_tags(SIMPLEPIE_NAMESPACE_MEDIARSS, 'group');
	 * $content = $group[0]['child'][SIMPLEPIE_NAMESPACE_MEDIARSS]['content'];
	 * $file = $content[0]['attribs']['']['url'];
	 * echo $file;
	 * </pre>
	 *
	 * @since 1.0
	 * @see http://simplepie.org/wiki/faq/supported_xml_namespaces
	 * @param string $namespace The URL of the XML namespace of the elements you're trying to access
	 * @param string $tag Tag name
	 * @return array
	 */
	public function get_feed_tags($namespace, $tag)
	{
		$type = $this->get_type();
		if ($type & SIMPLEPIE_TYPE_ATOM_10)
		{
			if (isset($this->data['child'][SIMPLEPIE_NAMESPACE_ATOM_10]['feed'][0]['child'][$namespace][$tag]))
			{
				return $this->data['child'][SIMPLEPIE_NAMESPACE_ATOM_10]['feed'][0]['child'][$namespace][$tag];
			}
		}
		if ($type & SIMPLEPIE_TYPE_ATOM_03)
		{
			if (isset($this->data['child'][SIMPLEPIE_NAMESPACE_ATOM_03]['feed'][0]['child'][$namespace][$tag]))
			{
				return $this->data['child'][SIMPLEPIE_NAMESPACE_ATOM_03]['feed'][0]['child'][$namespace][$tag];
			}
		}
		if ($type & SIMPLEPIE_TYPE_RSS_RDF)
		{
			if (isset($this->data['child'][SIMPLEPIE_NAMESPACE_RDF]['RDF'][0]['child'][$namespace][$tag]))
			{
				return $this->data['child'][SIMPLEPIE_NAMESPACE_RDF]['RDF'][0]['child'][$namespace][$tag];
			}
		}
		if ($type & SIMPLEPIE_TYPE_RSS_SYNDICATION)
		{
			if (isset($this->data['child'][SIMPLEPIE_NAMESPACE_RSS_20]['rss'][0]['child'][$namespace][$tag]))
			{
				return $this->data['child'][SIMPLEPIE_NAMESPACE_RSS_20]['rss'][0]['child'][$namespace][$tag];
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
	 * @return array
	 */
	public function get_channel_tags($namespace, $tag)
	{
		$type = $this->get_type();
		if ($type & SIMPLEPIE_TYPE_ATOM_ALL)
		{
			if ($return = $this->get_feed_tags($namespace, $tag))
			{
				return $return;
			}
		}
		if ($type & SIMPLEPIE_TYPE_RSS_10)
		{
			if ($channel = $this->get_feed_tags(SIMPLEPIE_NAMESPACE_RSS_10, 'channel'))
			{
				if (isset($channel[0]['child'][$namespace][$tag]))
				{
					return $channel[0]['child'][$namespace][$tag];
				}
			}
		}
		if ($type & SIMPLEPIE_TYPE_RSS_090)
		{
			if ($channel = $this->get_feed_tags(SIMPLEPIE_NAMESPACE_RSS_090, 'channel'))
			{
				if (isset($channel[0]['child'][$namespace][$tag]))
				{
					return $channel[0]['child'][$namespace][$tag];
				}
			}
		}
		if ($type & SIMPLEPIE_TYPE_RSS_SYNDICATION)
		{
			if ($channel = $this->get_feed_tags(SIMPLEPIE_NAMESPACE_RSS_20, 'channel'))
			{
				if (isset($channel[0]['child'][$namespace][$tag]))
				{
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
	 * @return array
	 */
	public function get_image_tags($namespace, $tag)
	{
		$type = $this->get_type();
		if ($type & SIMPLEPIE_TYPE_RSS_10)
		{
			if ($image = $this->get_feed_tags(SIMPLEPIE_NAMESPACE_RSS_10, 'image'))
			{
				if (isset($image[0]['child'][$namespace][$tag]))
				{
					return $image[0]['child'][$namespace][$tag];
				}
			}
		}
		if ($type & SIMPLEPIE_TYPE_RSS_090)
		{
			if ($image = $this->get_feed_tags(SIMPLEPIE_NAMESPACE_RSS_090, 'image'))
			{
				if (isset($image[0]['child'][$namespace][$tag]))
				{
					return $image[0]['child'][$namespace][$tag];
				}
			}
		}
		if ($type & SIMPLEPIE_TYPE_RSS_SYNDICATION)
		{
			if ($image = $this->get_channel_tags(SIMPLEPIE_NAMESPACE_RSS_20, 'image'))
			{
				if (isset($image[0]['child'][$namespace][$tag]))
				{
					return $image[0]['child'][$namespace][$tag];
				}
			}
		}
		return null;
	}

	/**
	 * Get the base URL value from the feed
	 *
	 * Uses `<xml:base>` if available, otherwise uses the first link in the
	 * feed, or failing that, the URL of the feed itself.
	 *
	 * @see get_link
	 * @see subscribe_url
	 *
	 * @param array $element
	 * @return string
	 */
	public function get_base($element = array())
	{
		if (!($this->get_type() & SIMPLEPIE_TYPE_RSS_SYNDICATION) && !empty($element['xml_base_explicit']) && isset($element['xml_base']))
		{
			return $element['xml_base'];
		}
		elseif ($this->get_link() !== null)
		{
			return $this->get_link();
		}

		return $this->subscribe_url();
	}

	/**
	 * Sanitize feed data
	 *
	 * @access private
	 * @see SimplePie_Sanitize::sanitize()
	 * @param string $data Data to sanitize
	 * @param int $type One of the SIMPLEPIE_CONSTRUCT_* constants
	 * @param string $base Base URL to resolve URLs against
	 * @return string Sanitized data
	 */
	public function sanitize($data, $type, $base = '')
	{
		try
		{
			return $this->sanitize->sanitize($data, $type, $base);
		}
		catch (SimplePie_Exception $e)
		{
			if (!$this->enable_exceptions)
			{
				$this->error = $e->getMessage();
				$this->registry->call('Misc', 'error', array($this->error, E_USER_WARNING, $e->getFile(), $e->getLine()));
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
		if ($return = $this->get_channel_tags(SIMPLEPIE_NAMESPACE_ATOM_10, 'title'))
		{
			return $this->sanitize($return[0]['data'], $this->registry->call('Misc', 'atom_10_construct_type', array($return[0]['attribs'])), $this->get_base($return[0]));
		}
		elseif ($return = $this->get_channel_tags(SIMPLEPIE_NAMESPACE_ATOM_03, 'title'))
		{
			return $this->sanitize($return[0]['data'], $this->registry->call('Misc', 'atom_03_construct_type', array($return[0]['attribs'])), $this->get_base($return[0]));
		}
		elseif ($return = $this->get_channel_tags(SIMPLEPIE_NAMESPACE_RSS_10, 'title'))
		{
			return $this->sanitize($return[0]['data'], SIMPLEPIE_CONSTRUCT_MAYBE_HTML, $this->get_base($return[0]));
		}
		elseif ($return = $this->get_channel_tags(SIMPLEPIE_NAMESPACE_RSS_090, 'title'))
		{
			return $this->sanitize($return[0]['data'], SIMPLEPIE_CONSTRUCT_MAYBE_HTML, $this->get_base($return[0]));
		}
		elseif ($return = $this->get_channel_tags(SIMPLEPIE_NAMESPACE_RSS_20, 'title'))
		{
			return $this->sanitize($return[0]['data'], SIMPLEPIE_CONSTRUCT_MAYBE_HTML, $this->get_base($return[0]));
		}
		elseif ($return = $this->get_channel_tags(SIMPLEPIE_NAMESPACE_DC_11, 'title'))
		{
			return $this->sanitize($return[0]['data'], SIMPLEPIE_CONSTRUCT_TEXT);
		}
		elseif ($return = $this->get_channel_tags(SIMPLEPIE_NAMESPACE_DC_10, 'title'))
		{
			return $this->sanitize($return[0]['data'], SIMPLEPIE_CONSTRUCT_TEXT);
		}

		return null;
	}

	/**
	 * Get a category for the feed
	 *
	 * @since Unknown
	 * @param int $key The category that you want to return. Remember that arrays begin with 0, not 1
	 * @return SimplePie_Category|null
	 */
	public function get_category($key = 0)
	{
		$categories = $this->get_categories();
		if (isset($categories[$key]))
		{
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
	 * @return array|null List of {@see SimplePie_Category} objects
	 */
	public function get_categories()
	{
		$categories = array();

		foreach ((array) $this->get_channel_tags(SIMPLEPIE_NAMESPACE_ATOM_10, 'category') as $category)
		{
			$term = null;
			$scheme = null;
			$label = null;
			if (isset($category['attribs']['']['term']))
			{
				$term = $this->sanitize($category['attribs']['']['term'], SIMPLEPIE_CONSTRUCT_TEXT);
			}
			if (isset($category['attribs']['']['scheme']))
			{
				$scheme = $this->sanitize($category['attribs']['']['scheme'], SIMPLEPIE_CONSTRUCT_TEXT);
			}
			if (isset($category['attribs']['']['label']))
			{
				$label = $this->sanitize($category['attribs']['']['label'], SIMPLEPIE_CONSTRUCT_TEXT);
			}
			$categories[] = $this->registry->create('Category', array($term, $scheme, $label));
		}
		foreach ((array) $this->get_channel_tags(SIMPLEPIE_NAMESPACE_RSS_20, 'category') as $category)
		{
			// This is really the label, but keep this as the term also for BC.
			// Label will also work on retrieving because that falls back to term.
			$term = $this->sanitize($category['data'], SIMPLEPIE_CONSTRUCT_TEXT);
			if (isset($category['attribs']['']['domain']))
			{
				$scheme = $this->sanitize($category['attribs']['']['domain'], SIMPLEPIE_CONSTRUCT_TEXT);
			}
			else
			{
				$scheme = null;
			}
			$categories[] = $this->registry->create('Category', array($term, $scheme, null));
		}
		foreach ((array) $this->get_channel_tags(SIMPLEPIE_NAMESPACE_DC_11, 'subject') as $category)
		{
			$categories[] = $this->registry->create('Category', array($this->sanitize($category['data'], SIMPLEPIE_CONSTRUCT_TEXT), null, null));
		}
		foreach ((array) $this->get_channel_tags(SIMPLEPIE_NAMESPACE_DC_10, 'subject') as $category)
		{
			$categories[] = $this->registry->create('Category', array($this->sanitize($category['data'], SIMPLEPIE_CONSTRUCT_TEXT), null, null));
		}

		if (!empty($categories))
		{
			return array_unique($categories);
		}

		return null;
	}

	/**
	 * Get an author for the feed
	 *
	 * @since 1.1
	 * @param int $key The author that you want to return. Remember that arrays begin with 0, not 1
	 * @return SimplePie_Author|null
	 */
	public function get_author($key = 0)
	{
		$authors = $this->get_authors();
		if (isset($authors[$key]))
		{
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
	 * @return array|null List of {@see SimplePie_Author} objects
	 */
	public function get_authors()
	{
		$authors = array();
		foreach ((array) $this->get_channel_tags(SIMPLEPIE_NAMESPACE_ATOM_10, 'author') as $author)
		{
			$name = null;
			$uri = null;
			$email = null;
			if (isset($author['child'][SIMPLEPIE_NAMESPACE_ATOM_10]['name'][0]['data']))
			{
				$name = $this->sanitize($author['child'][SIMPLEPIE_NAMESPACE_ATOM_10]['name'][0]['data'], SIMPLEPIE_CONSTRUCT_TEXT);
			}
			if (isset($author['child'][SIMPLEPIE_NAMESPACE_ATOM_10]['uri'][0]['data']))
			{
				$uri = $this->sanitize($author['child'][SIMPLEPIE_NAMESPACE_ATOM_10]['uri'][0]['data'], SIMPLEPIE_CONSTRUCT_IRI, $this->get_base($author['child'][SIMPLEPIE_NAMESPACE_ATOM_10]['uri'][0]));
			}
			if (isset($author['child'][SIMPLEPIE_NAMESPACE_ATOM_10]['email'][0]['data']))
			{
				$email = $this->sanitize($author['child'][SIMPLEPIE_NAMESPACE_ATOM_10]['email'][0]['data'], SIMPLEPIE_CONSTRUCT_TEXT);
			}
			if ($name !== null || $email !== null || $uri !== null)
			{
				$authors[] = $this->registry->create('Author', array($name, $uri, $email));
			}
		}
		if ($author = $this->get_channel_tags(SIMPLEPIE_NAMESPACE_ATOM_03, 'author'))
		{
			$name = null;
			$url = null;
			$email = null;
			if (isset($author[0]['child'][SIMPLEPIE_NAMESPACE_ATOM_03]['name'][0]['data']))
			{
				$name = $this->sanitize($author[0]['child'][SIMPLEPIE_NAMESPACE_ATOM_03]['name'][0]['data'], SIMPLEPIE_CONSTRUCT_TEXT);
			}
			if (isset($author[0]['child'][SIMPLEPIE_NAMESPACE_ATOM_03]['url'][0]['data']))
			{
				$url = $this->sanitize($author[0]['child'][SIMPLEPIE_NAMESPACE_ATOM_03]['url'][0]['data'], SIMPLEPIE_CONSTRUCT_IRI, $this->get_base($author[0]['child'][SIMPLEPIE_NAMESPACE_ATOM_03]['url'][0]));
			}
			if (isset($author[0]['child'][SIMPLEPIE_NAMESPACE_ATOM_03]['email'][0]['data']))
			{
				$email = $this->sanitize($author[0]['child'][SIMPLEPIE_NAMESPACE_ATOM_03]['email'][0]['data'], SIMPLEPIE_CONSTRUCT_TEXT);
			}
			if ($name !== null || $email !== null || $url !== null)
			{
				$authors[] = $this->registry->create('Author', array($name, $url, $email));
			}
		}
		foreach ((array) $this->get_channel_tags(SIMPLEPIE_NAMESPACE_DC_11, 'creator') as $author)
		{
			$authors[] = $this->registry->create('Author', array($this->sanitize($author['data'], SIMPLEPIE_CONSTRUCT_TEXT), null, null));
		}
		foreach ((array) $this->get_channel_tags(SIMPLEPIE_NAMESPACE_DC_10, 'creator') as $author)
		{
			$authors[] = $this->registry->create('Author', array($this->sanitize($author['data'], SIMPLEPIE_CONSTRUCT_TEXT), null, null));
		}
		foreach ((array) $this->get_channel_tags(SIMPLEPIE_NAMESPACE_ITUNES, 'author') as $author)
		{
			$authors[] = $this->registry->create('Author', array($this->sanitize($author['data'], SIMPLEPIE_CONSTRUCT_TEXT), null, null));
		}

		if (!empty($authors))
		{
			return array_unique($authors);
		}

		return null;
	}

	/**
	 * Get a contributor for the feed
	 *
	 * @since 1.1
	 * @param int $key The contrbutor that you want to return. Remember that arrays begin with 0, not 1
	 * @return SimplePie_Author|null
	 */
	public function get_contributor($key = 0)
	{
		$contributors = $this->get_contributors();
		if (isset($contributors[$key]))
		{
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
	 * @return array|null List of {@see SimplePie_Author} objects
	 */
	public function get_contributors()
	{
		$contributors = array();
		foreach ((array) $this->get_channel_tags(SIMPLEPIE_NAMESPACE_ATOM_10, 'contributor') as $contributor)
		{
			$name = null;
			$uri = null;
			$email = null;
			if (isset($contributor['child'][SIMPLEPIE_NAMESPACE_ATOM_10]['name'][0]['data']))
			{
				$name = $this->sanitize($contributor['child'][SIMPLEPIE_NAMESPACE_ATOM_10]['name'][0]['data'], SIMPLEPIE_CONSTRUCT_TEXT);
			}
			if (isset($contributor['child'][SIMPLEPIE_NAMESPACE_ATOM_10]['uri'][0]['data']))
			{
				$uri = $this->sanitize($contributor['child'][SIMPLEPIE_NAMESPACE_ATOM_10]['uri'][0]['data'], SIMPLEPIE_CONSTRUCT_IRI, $this->get_base($contributor['child'][SIMPLEPIE_NAMESPACE_ATOM_10]['uri'][0]));
			}
			if (isset($contributor['child'][SIMPLEPIE_NAMESPACE_ATOM_10]['email'][0]['data']))
			{
				$email = $this->sanitize($contributor['child'][SIMPLEPIE_NAMESPACE_ATOM_10]['email'][0]['data'], SIMPLEPIE_CONSTRUCT_TEXT);
			}
			if ($name !== null || $email !== null || $uri !== null)
			{
				$contributors[] = $this->registry->create('Author', array($name, $uri, $email));
			}
		}
		foreach ((array) $this->get_channel_tags(SIMPLEPIE_NAMESPACE_ATOM_03, 'contributor') as $contributor)
		{
			$name = null;
			$url = null;
			$email = null;
			if (isset($contributor['child'][SIMPLEPIE_NAMESPACE_ATOM_03]['name'][0]['data']))
			{
				$name = $this->sanitize($contributor['child'][SIMPLEPIE_NAMESPACE_ATOM_03]['name'][0]['data'], SIMPLEPIE_CONSTRUCT_TEXT);
			}
			if (isset($contributor['child'][SIMPLEPIE_NAMESPACE_ATOM_03]['url'][0]['data']))
			{
				$url = $this->sanitize($contributor['child'][SIMPLEPIE_NAMESPACE_ATOM_03]['url'][0]['data'], SIMPLEPIE_CONSTRUCT_IRI, $this->get_base($contributor['child'][SIMPLEPIE_NAMESPACE_ATOM_03]['url'][0]));
			}
			if (isset($contributor['child'][SIMPLEPIE_NAMESPACE_ATOM_03]['email'][0]['data']))
			{
				$email = $this->sanitize($contributor['child'][SIMPLEPIE_NAMESPACE_ATOM_03]['email'][0]['data'], SIMPLEPIE_CONSTRUCT_TEXT);
			}
			if ($name !== null || $email !== null || $url !== null)
			{
				$contributors[] = $this->registry->create('Author', array($name, $url, $email));
			}
		}

		if (!empty($contributors))
		{
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
	public function get_link($key = 0, $rel = 'alternate')
	{
		$links = $this->get_links($rel);
		if (isset($links[$key]))
		{
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
	 * @return array|null Links found for the feed (strings)
	 */
	public function get_links($rel = 'alternate')
	{
		if (!isset($this->data['links']))
		{
			$this->data['links'] = array();
			if ($links = $this->get_channel_tags(SIMPLEPIE_NAMESPACE_ATOM_10, 'link'))
			{
				foreach ($links as $link)
				{
					if (isset($link['attribs']['']['href']))
					{
						$link_rel = (isset($link['attribs']['']['rel'])) ? $link['attribs']['']['rel'] : 'alternate';
						$this->data['links'][$link_rel][] = $this->sanitize($link['attribs']['']['href'], SIMPLEPIE_CONSTRUCT_IRI, $this->get_base($link));
					}
				}
			}
			if ($links = $this->get_channel_tags(SIMPLEPIE_NAMESPACE_ATOM_03, 'link'))
			{
				foreach ($links as $link)
				{
					if (isset($link['attribs']['']['href']))
					{
						$link_rel = (isset($link['attribs']['']['rel'])) ? $link['attribs']['']['rel'] : 'alternate';
						$this->data['links'][$link_rel][] = $this->sanitize($link['attribs']['']['href'], SIMPLEPIE_CONSTRUCT_IRI, $this->get_base($link));

					}
				}
			}
			if ($links = $this->get_channel_tags(SIMPLEPIE_NAMESPACE_RSS_10, 'link'))
			{
				$this->data['links']['alternate'][] = $this->sanitize($links[0]['data'], SIMPLEPIE_CONSTRUCT_IRI, $this->get_base($links[0]));
			}
			if ($links = $this->get_channel_tags(SIMPLEPIE_NAMESPACE_RSS_090, 'link'))
			{
				$this->data['links']['alternate'][] = $this->sanitize($links[0]['data'], SIMPLEPIE_CONSTRUCT_IRI, $this->get_base($links[0]));
			}
			if ($links = $this->get_channel_tags(SIMPLEPIE_NAMESPACE_RSS_20, 'link'))
			{
				$this->data['links']['alternate'][] = $this->sanitize($links[0]['data'], SIMPLEPIE_CONSTRUCT_IRI, $this->get_base($links[0]));
			}

			$keys = array_keys($this->data['links']);
			foreach ($keys as $key)
			{
				if ($this->registry->call('Misc', 'is_isegment_nz_nc', array($key)))
				{
					if (isset($this->data['links'][SIMPLEPIE_IANA_LINK_RELATIONS_REGISTRY . $key]))
					{
						$this->data['links'][SIMPLEPIE_IANA_LINK_RELATIONS_REGISTRY . $key] = array_merge($this->data['links'][$key], $this->data['links'][SIMPLEPIE_IANA_LINK_RELATIONS_REGISTRY . $key]);
						$this->data['links'][$key] =& $this->data['links'][SIMPLEPIE_IANA_LINK_RELATIONS_REGISTRY . $key];
					}
					else
					{
						$this->data['links'][SIMPLEPIE_IANA_LINK_RELATIONS_REGISTRY . $key] =& $this->data['links'][$key];
					}
				}
				elseif (substr($key, 0, 41) === SIMPLEPIE_IANA_LINK_RELATIONS_REGISTRY)
				{
					$this->data['links'][substr($key, 41)] =& $this->data['links'][$key];
				}
				$this->data['links'][$key] = array_unique($this->data['links'][$key]);
			}
		}

		if (isset($this->data['headers']['link']))
		{
			$link_headers = $this->data['headers']['link'];
			if (is_string($link_headers)) {
				$link_headers = array($link_headers);
			}
			$matches = preg_filter('/<([^>]+)>; rel='.preg_quote($rel).'/', '$1', $link_headers);
			if (!empty($matches)) {
				return $matches;
			}
		}

		if (isset($this->data['links'][$rel]))
		{
			return $this->data['links'][$rel];
		}

		return null;
	}

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
		if ($return = $this->get_channel_tags(SIMPLEPIE_NAMESPACE_ATOM_10, 'subtitle'))
		{
			return $this->sanitize($return[0]['data'], $this->registry->call('Misc', 'atom_10_construct_type', array($return[0]['attribs'])), $this->get_base($return[0]));
		}
		elseif ($return = $this->get_channel_tags(SIMPLEPIE_NAMESPACE_ATOM_03, 'tagline'))
		{
			return $this->sanitize($return[0]['data'], $this->registry->call('Misc', 'atom_03_construct_type', array($return[0]['attribs'])), $this->get_base($return[0]));
		}
		elseif ($return = $this->get_channel_tags(SIMPLEPIE_NAMESPACE_RSS_10, 'description'))
		{
			return $this->sanitize($return[0]['data'], SIMPLEPIE_CONSTRUCT_MAYBE_HTML, $this->get_base($return[0]));
		}
		elseif ($return = $this->get_channel_tags(SIMPLEPIE_NAMESPACE_RSS_090, 'description'))
		{
			return $this->sanitize($return[0]['data'], SIMPLEPIE_CONSTRUCT_MAYBE_HTML, $this->get_base($return[0]));
		}
		elseif ($return = $this->get_channel_tags(SIMPLEPIE_NAMESPACE_RSS_20, 'description'))
		{
			return $this->sanitize($return[0]['data'], SIMPLEPIE_CONSTRUCT_HTML, $this->get_base($return[0]));
		}
		elseif ($return = $this->get_channel_tags(SIMPLEPIE_NAMESPACE_DC_11, 'description'))
		{
			return $this->sanitize($return[0]['data'], SIMPLEPIE_CONSTRUCT_TEXT);
		}
		elseif ($return = $this->get_channel_tags(SIMPLEPIE_NAMESPACE_DC_10, 'description'))
		{
			return $this->sanitize($return[0]['data'], SIMPLEPIE_CONSTRUCT_TEXT);
		}
		elseif ($return = $this->get_channel_tags(SIMPLEPIE_NAMESPACE_ITUNES, 'summary'))
		{
			return $this->sanitize($return[0]['data'], SIMPLEPIE_CONSTRUCT_HTML, $this->get_base($return[0]));
		}
		elseif ($return = $this->get_channel_tags(SIMPLEPIE_NAMESPACE_ITUNES, 'subtitle'))
		{
			return $this->sanitize($return[0]['data'], SIMPLEPIE_CONSTRUCT_HTML, $this->get_base($return[0]));
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
		if ($return = $this->get_channel_tags(SIMPLEPIE_NAMESPACE_ATOM_10, 'rights'))
		{
			return $this->sanitize($return[0]['data'], $this->registry->call('Misc', 'atom_10_construct_type', array($return[0]['attribs'])), $this->get_base($return[0]));
		}
		elseif ($return = $this->get_channel_tags(SIMPLEPIE_NAMESPACE_ATOM_03, 'copyright'))
		{
			return $this->sanitize($return[0]['data'], $this->registry->call('Misc', 'atom_03_construct_type', array($return[0]['attribs'])), $this->get_base($return[0]));
		}
		elseif ($return = $this->get_channel_tags(SIMPLEPIE_NAMESPACE_RSS_20, 'copyright'))
		{
			return $this->sanitize($return[0]['data'], SIMPLEPIE_CONSTRUCT_TEXT);
		}
		elseif ($return = $this->get_channel_tags(SIMPLEPIE_NAMESPACE_DC_11, 'rights'))
		{
			return $this->sanitize($return[0]['data'], SIMPLEPIE_CONSTRUCT_TEXT);
		}
		elseif ($return = $this->get_channel_tags(SIMPLEPIE_NAMESPACE_DC_10, 'rights'))
		{
			return $this->sanitize($return[0]['data'], SIMPLEPIE_CONSTRUCT_TEXT);
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
		if ($return = $this->get_channel_tags(SIMPLEPIE_NAMESPACE_RSS_20, 'language'))
		{
			return $this->sanitize($return[0]['data'], SIMPLEPIE_CONSTRUCT_TEXT);
		}
		elseif ($return = $this->get_channel_tags(SIMPLEPIE_NAMESPACE_DC_11, 'language'))
		{
			return $this->sanitize($return[0]['data'], SIMPLEPIE_CONSTRUCT_TEXT);
		}
		elseif ($return = $this->get_channel_tags(SIMPLEPIE_NAMESPACE_DC_10, 'language'))
		{
			return $this->sanitize($return[0]['data'], SIMPLEPIE_CONSTRUCT_TEXT);
		}
		elseif (isset($this->data['child'][SIMPLEPIE_NAMESPACE_ATOM_10]['feed'][0]['xml_lang']))
		{
			return $this->sanitize($this->data['child'][SIMPLEPIE_NAMESPACE_ATOM_10]['feed'][0]['xml_lang'], SIMPLEPIE_CONSTRUCT_TEXT);
		}
		elseif (isset($this->data['child'][SIMPLEPIE_NAMESPACE_ATOM_03]['feed'][0]['xml_lang']))
		{
			return $this->sanitize($this->data['child'][SIMPLEPIE_NAMESPACE_ATOM_03]['feed'][0]['xml_lang'], SIMPLEPIE_CONSTRUCT_TEXT);
		}
		elseif (isset($this->data['child'][SIMPLEPIE_NAMESPACE_RDF]['RDF'][0]['xml_lang']))
		{
			return $this->sanitize($this->data['child'][SIMPLEPIE_NAMESPACE_RDF]['RDF'][0]['xml_lang'], SIMPLEPIE_CONSTRUCT_TEXT);
		}
		elseif (isset($this->data['headers']['content-language']))
		{
			return $this->sanitize($this->data['headers']['content-language'], SIMPLEPIE_CONSTRUCT_TEXT);
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
	 * @return string|null
	 */
	public function get_latitude()
	{

		if ($return = $this->get_channel_tags(SIMPLEPIE_NAMESPACE_W3C_BASIC_GEO, 'lat'))
		{
			return (float) $return[0]['data'];
		}
		elseif (($return = $this->get_channel_tags(SIMPLEPIE_NAMESPACE_GEORSS, 'point')) && preg_match('/^((?:-)?[0-9]+(?:\.[0-9]+)) ((?:-)?[0-9]+(?:\.[0-9]+))$/', trim($return[0]['data']), $match))
		{
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
	 * @return string|null
	 */
	public function get_longitude()
	{
		if ($return = $this->get_channel_tags(SIMPLEPIE_NAMESPACE_W3C_BASIC_GEO, 'long'))
		{
			return (float) $return[0]['data'];
		}
		elseif ($return = $this->get_channel_tags(SIMPLEPIE_NAMESPACE_W3C_BASIC_GEO, 'lon'))
		{
			return (float) $return[0]['data'];
		}
		elseif (($return = $this->get_channel_tags(SIMPLEPIE_NAMESPACE_GEORSS, 'point')) && preg_match('/^((?:-)?[0-9]+(?:\.[0-9]+)) ((?:-)?[0-9]+(?:\.[0-9]+))$/', trim($return[0]['data']), $match))
		{
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
		if ($return = $this->get_image_tags(SIMPLEPIE_NAMESPACE_RSS_10, 'title'))
		{
			return $this->sanitize($return[0]['data'], SIMPLEPIE_CONSTRUCT_TEXT);
		}
		elseif ($return = $this->get_image_tags(SIMPLEPIE_NAMESPACE_RSS_090, 'title'))
		{
			return $this->sanitize($return[0]['data'], SIMPLEPIE_CONSTRUCT_TEXT);
		}
		elseif ($return = $this->get_image_tags(SIMPLEPIE_NAMESPACE_RSS_20, 'title'))
		{
			return $this->sanitize($return[0]['data'], SIMPLEPIE_CONSTRUCT_TEXT);
		}
		elseif ($return = $this->get_image_tags(SIMPLEPIE_NAMESPACE_DC_11, 'title'))
		{
			return $this->sanitize($return[0]['data'], SIMPLEPIE_CONSTRUCT_TEXT);
		}
		elseif ($return = $this->get_image_tags(SIMPLEPIE_NAMESPACE_DC_10, 'title'))
		{
			return $this->sanitize($return[0]['data'], SIMPLEPIE_CONSTRUCT_TEXT);
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
		if ($return = $this->get_channel_tags(SIMPLEPIE_NAMESPACE_ITUNES, 'image'))
		{
			return $this->sanitize($return[0]['attribs']['']['href'], SIMPLEPIE_CONSTRUCT_IRI);
		}
		elseif ($return = $this->get_channel_tags(SIMPLEPIE_NAMESPACE_ATOM_10, 'logo'))
		{
			return $this->sanitize($return[0]['data'], SIMPLEPIE_CONSTRUCT_IRI, $this->get_base($return[0]));
		}
		elseif ($return = $this->get_channel_tags(SIMPLEPIE_NAMESPACE_ATOM_10, 'icon'))
		{
			return $this->sanitize($return[0]['data'], SIMPLEPIE_CONSTRUCT_IRI, $this->get_base($return[0]));
		}
		elseif ($return = $this->get_image_tags(SIMPLEPIE_NAMESPACE_RSS_10, 'url'))
		{
			return $this->sanitize($return[0]['data'], SIMPLEPIE_CONSTRUCT_IRI, $this->get_base($return[0]));
		}
		elseif ($return = $this->get_image_tags(SIMPLEPIE_NAMESPACE_RSS_090, 'url'))
		{
			return $this->sanitize($return[0]['data'], SIMPLEPIE_CONSTRUCT_IRI, $this->get_base($return[0]));
		}
		elseif ($return = $this->get_image_tags(SIMPLEPIE_NAMESPACE_RSS_20, 'url'))
		{
			return $this->sanitize($return[0]['data'], SIMPLEPIE_CONSTRUCT_IRI, $this->get_base($return[0]));
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
		if ($return = $this->get_image_tags(SIMPLEPIE_NAMESPACE_RSS_10, 'link'))
		{
			return $this->sanitize($return[0]['data'], SIMPLEPIE_CONSTRUCT_IRI, $this->get_base($return[0]));
		}
		elseif ($return = $this->get_image_tags(SIMPLEPIE_NAMESPACE_RSS_090, 'link'))
		{
			return $this->sanitize($return[0]['data'], SIMPLEPIE_CONSTRUCT_IRI, $this->get_base($return[0]));
		}
		elseif ($return = $this->get_image_tags(SIMPLEPIE_NAMESPACE_RSS_20, 'link'))
		{
			return $this->sanitize($return[0]['data'], SIMPLEPIE_CONSTRUCT_IRI, $this->get_base($return[0]));
		}

		return null;
	}

	/**
	 * Get the feed logo's link
	 *
	 * RSS 2.0 feeds are allowed to have a "feed logo" width.
	 *
	 * Uses `<image><width>` or defaults to 88.0 if no width is specified and
	 * the feed is an RSS 2.0 feed.
	 *
	 * @return int|float|null
	 */
	public function get_image_width()
	{
		if ($return = $this->get_image_tags(SIMPLEPIE_NAMESPACE_RSS_20, 'width'))
		{
			return round($return[0]['data']);
		}
		elseif ($this->get_type() & SIMPLEPIE_TYPE_RSS_SYNDICATION && $this->get_image_tags(SIMPLEPIE_NAMESPACE_RSS_20, 'url'))
		{
			return 88.0;
		}

		return null;
	}

	/**
	 * Get the feed logo's height
	 *
	 * RSS 2.0 feeds are allowed to have a "feed logo" height.
	 *
	 * Uses `<image><height>` or defaults to 31.0 if no height is specified and
	 * the feed is an RSS 2.0 feed.
	 *
	 * @return int|float|null
	 */
	public function get_image_height()
	{
		if ($return = $this->get_image_tags(SIMPLEPIE_NAMESPACE_RSS_20, 'height'))
		{
			return round($return[0]['data']);
		}
		elseif ($this->get_type() & SIMPLEPIE_TYPE_RSS_SYNDICATION && $this->get_image_tags(SIMPLEPIE_NAMESPACE_RSS_20, 'url'))
		{
			return 31.0;
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
	public function get_item_quantity($max = 0)
	{
		$max = (int) $max;
		$qty = count($this->get_items());
		if ($max === 0)
		{
			return $qty;
		}

		return ($qty > $max) ? $max : $qty;
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
	 * @return SimplePie_Item|null
	 */
	public function get_item($key = 0)
	{
		$items = $this->get_items();
		if (isset($items[$key]))
		{
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
	 * @return SimplePie_Item[]|null List of {@see SimplePie_Item} objects
	 */
	public function get_items($start = 0, $end = 0)
	{
		if (!isset($this->data['items']))
		{
			if (!empty($this->multifeed_objects))
			{
				$this->data['items'] = SimplePie::merge_items($this->multifeed_objects, $start, $end, $this->item_limit);
				if (empty($this->data['items']))
				{
					return array();
				}
				return $this->data['items'];
			}
			$this->data['items'] = array();
			if ($items = $this->get_feed_tags(SIMPLEPIE_NAMESPACE_ATOM_10, 'entry'))
			{
				$keys = array_keys($items);
				foreach ($keys as $key)
				{
					$this->data['items'][] = $this->registry->create('Item', array($this, $items[$key]));
				}
			}
			if ($items = $this->get_feed_tags(SIMPLEPIE_NAMESPACE_ATOM_03, 'entry'))
			{
				$keys = array_keys($items);
				foreach ($keys as $key)
				{
					$this->data['items'][] = $this->registry->create('Item', array($this, $items[$key]));
				}
			}
			if ($items = $this->get_feed_tags(SIMPLEPIE_NAMESPACE_RSS_10, 'item'))
			{
				$keys = array_keys($items);
				foreach ($keys as $key)
				{
					$this->data['items'][] = $this->registry->create('Item', array($this, $items[$key]));
				}
			}
			if ($items = $this->get_feed_tags(SIMPLEPIE_NAMESPACE_RSS_090, 'item'))
			{
				$keys = array_keys($items);
				foreach ($keys as $key)
				{
					$this->data['items'][] = $this->registry->create('Item', array($this, $items[$key]));
				}
			}
			if ($items = $this->get_channel_tags(SIMPLEPIE_NAMESPACE_RSS_20, 'item'))
			{
				$keys = array_keys($items);
				foreach ($keys as $key)
				{
					$this->data['items'][] = $this->registry->create('Item', array($this, $items[$key]));
				}
			}
		}

		if (empty($this->data['items']))
		{
			return array();
		}

		if ($this->order_by_date)
		{
			if (!isset($this->data['ordered_items']))
			{
				$this->data['ordered_items'] = $this->data['items'];
				usort($this->data['ordered_items'], array(get_class($this), 'sort_items'));
		 	}
			$items = $this->data['ordered_items'];
		}
		else
		{
			$items = $this->data['items'];
		}
		// Slice the data as desired
		if ($end === 0)
		{
			return array_slice($items, $start);
		}

		return array_slice($items, $start, $end);
	}

	/**
	 * Set the favicon handler
	 *
	 * @deprecated Use your own favicon handling instead
	 */
	public function set_favicon_handler($page = false, $qs = 'i')
	{
		$level = defined('E_USER_DEPRECATED') ? E_USER_DEPRECATED : E_USER_WARNING;
		trigger_error('Favicon handling has been removed, please use your own handling', $level);
		return false;
	}

	/**
	 * Get the favicon for the current feed
	 *
	 * @deprecated Use your own favicon handling instead
	 */
	public function get_favicon()
	{
		$level = defined('E_USER_DEPRECATED') ? E_USER_DEPRECATED : E_USER_WARNING;
		trigger_error('Favicon handling has been removed, please use your own handling', $level);

		if (($url = $this->get_link()) !== null)
		{
			return 'https://www.google.com/s2/favicons?domain=' . urlencode($url);
		}

		return false;
	}

	/**
	 * Magic method handler
	 *
	 * @param string $method Method name
	 * @param array $args Arguments to the method
	 * @return mixed
	 */
	public function __call($method, $args)
	{
		if (strpos($method, 'subscribe_') === 0)
		{
			$level = defined('E_USER_DEPRECATED') ? E_USER_DEPRECATED : E_USER_WARNING;
			trigger_error('subscribe_*() has been deprecated, implement the callback yourself', $level);
			return '';
		}
		if ($method === 'enable_xml_dump')
		{
			$level = defined('E_USER_DEPRECATED') ? E_USER_DEPRECATED : E_USER_WARNING;
			trigger_error('enable_xml_dump() has been deprecated, use get_raw_data() instead', $level);
			return false;
		}

		$class = get_class($this);
		$trace = debug_backtrace(); // phpcs:ignore PHPCompatibility.FunctionUse.ArgumentFunctionsReportCurrentValue.NeedsInspection
		$file = $trace[0]['file'];
		$line = $trace[0]['line'];
		trigger_error("Call to undefined method $class::$method() in $file on line $line", E_USER_ERROR);
	}

	/**
	 * Sorting callback for items
	 *
	 * @access private
	 * @param SimplePie $a
	 * @param SimplePie $b
	 * @return boolean
	 */
	public static function sort_items($a, $b)
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
	 * @param array $urls List of SimplePie feed objects to merge
	 * @param int $start Starting item
	 * @param int $end Number of items to return
	 * @param int $limit Maximum number of items per feed
	 * @return array
	 */
	public static function merge_items($urls, $start = 0, $end = 0, $limit = 0)
	{
		if (is_array($urls) && sizeof($urls) > 0)
		{
			$items = array();
			foreach ($urls as $arg)
			{
				if ($arg instanceof SimplePie)
				{
					$items = array_merge($items, $arg->get_items(0, $limit));
				}
				else
				{
					trigger_error('Arguments must be SimplePie objects', E_USER_WARNING);
				}
			}

			usort($items, array(get_class($urls[0]), 'sort_items'));

			if ($end === 0)
			{
				return array_slice($items, $start);
			}

			return array_slice($items, $start, $end);
		}

		trigger_error('Cannot merge zero SimplePie objects', E_USER_WARNING);
		return array();
	}

	/**
	 * Store PubSubHubbub links as headers
	 *
	 * There is no way to find PuSH links in the body of a microformats feed,
	 * so they are added to the headers when found, to be used later by get_links.
	 * @param SimplePie_File $file
	 * @param string $hub
	 * @param string $self
	 */
	private function store_links(&$file, $hub, $self) {
		if (isset($file->headers['link']['hub']) ||
			  (isset($file->headers['link']) &&
			   preg_match('/rel=hub/', $file->headers['link'])))
		{
			return;
		}

		if ($hub)
		{
			if (isset($file->headers['link']))
			{
				if ($file->headers['link'] !== '')
				{
					$file->headers['link'] = ', ';
				}
			}
			else
			{
				$file->headers['link'] = '';
			}
			$file->headers['link'] .= '<'.$hub.'>; rel=hub';
			if ($self)
			{
				$file->headers['link'] .= ', <'.$self.'>; rel=self';
			}
		}
	}
}
endif;
