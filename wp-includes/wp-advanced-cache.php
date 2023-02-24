<?php
/**
 * Plugin Name: Ratcache
 * Description: An advanced caching plugin that uses an external object cache to store full pages.
 * Author: Christopher Davis
 * Author URI: http://christopherdavis.me
 * Version: 0.1
 *
 * Place this file in your `wp-content` directory and add
 * `define('WP_CACHE', true)` to your wp config file.
 *
 * This was written for fun and see how advanced caching plugins work. It was
 * inspired by batcache: https://github.com/skeltoac/batcache
 *
 * @author      Christopher Davis <chris [AT] classicalguitar.org>
 * @copyright   Christopher Davis 2012
 * @license     MIT
 * @version     0.2
 */

!defined('ABSPATH') && exit;

//options
!defined('RATCACHE_THRESH') && define('RATCACHE_THRESH', 3);
!defined('RATCACHE_TIMER') && define('RATCACHE_TIMER', 120);
!defined('RATCACHE_GROUP') && define('RATCACHE_GROUP', 'ratcache');
!defined('RATCACHE_MAX_AGE') && define('RATCACHE_MAX_AGE', 300);

class Ratcache
{
    // how many hits to a page before caching it.
    const THRESH = RATCACHE_THRESH;

    // self::THRESH hits in this many seconds
    const TIMER = RATCACHE_TIMER;

    // cache group
    const GROUP = RATCACHE_GROUP;

    // how long to cache things
    const MAX_AGE = RATCACHE_MAX_AGE;

    // container for the instance
    private static $ins = null;

    // script files not to cache
    private static $nocache = array(
        'wp-app.php', 'xmlrpc.php', 'ms-files.php',
        'admin-ajax.php', 'load-scripts.php',);

    // headers to not cache
    private static $nocache_headers = array('transfer-encoding',);

    // Settings for the current request
    private $settings = array();

    // don't cache by default
    private $enabled = false;

    // status header of the current request
    private $status;

    // cache key
    private $key;

    // traffic threshold key
    private $req_key;

    /**
     * Get the instance of Ratcache.
     *
     * @since   0.1
     * @access  public
     * @return  Ratcache object
     */
    public static function instance()
    {
        is_null(self::$ins) && self::$ins = new self;
        return self::$ins;
    }

    /**
     * Enabled the cache.
     *
     * @since   0.1
     * @access  public
     * @return  void
     */
    public static function enable()
    {
        self::instance()->enabled = true;
    }

    /**
     * Disable the cache.
     *
     * @since   0.1
     * @access  public
     * @return  void
     */
    public static function disable()
    {
        self::instance()->enabled = false;
    }

    /**
     * Getter.  Helps do settings and such.
     *
     * @since   0.1
     * @access  public
     * @return  void
     */
    public function __set($key, $val)
    {
        $this->settings[$key] = $val;
    }

    /**
     * Setter. Helps do settings.
     *
     * @since   0.1
     * @access  public
     * @return  mixed
     */
    public function __get($key)
    {
        return isset($this->settings[$key]) ? $this->settings[$key] : false;
    }

    public function __isset($key)
    {
        return isset($this->settings[$key]);
    }

    public function set_status_header($h)
    {
        $this->status = $h;
        return $h;
    }

    public function run($uri, $host)
    {
        global $wp_filter;

        // don't cache WP api endpoints
        if(in_array(basename($_SERVER['SCRIPT_FILENAME']), self::$nocache))
            return false;

        // don't cache POST requests
        if(!empty($GLOBALS['HTTP_RAW_POST_DATA']) || !empty($_POST))
            return false;

        // make sure we're not cookied
        $c = array_filter(array_keys((array)$_COOKIE), function($c) {
            if(
                substr($c, 0, 2) == 'wp' ||
                substr($c, 0, 9) == 'wordpress' ||
                substr($c, 0, 14) == 'comment_author'
            ) return true;

            return false;
        });

        if(!empty($c))
            return false;

        // eff no content dir
        if(!defined('WP_CONTENT_DIR'))
            return false;

        // no external object cache
        if(!$this->start_cache())
            return false;

        // make sure the client knows we change stuff based on cookies
        header('Vary: Cookie', false);

        $keys = array(
            'host'      => $host,
            'path'      => $uri,
            'settings'  => $this->settings,
        );

        $this->key = md5(serialize($keys));
        $this->req_key = $this->key . '_req';

        // attempt to serve cache content
        if(
            ($cache = wp_cache_get($this->key, self::GROUP)) &&
            isset($cache['time']) &&
            $cache['time'] + self::MAX_AGE > time()
        ) {
            $ma = $cache['time'] + self::MAX_AGE - time();
            header('Last-Modified: ' . date('r', $cache['time']), true);
            header("Cache-Control: max-age={$ma}, must-revalidate", true);

            if(!empty($cache['headers']))
            {
                foreach((array)$cache['headers'] as $h => $v)
                    header("{$h}: {$v}", false);
            }

            if(!empty($cache['status_header']))
                header($cache['status_header'], true);

            $hits = wp_cache_incr($this->key . '_count', 1, self::GROUP);

            die($cache['output'] . "<!-- ratcached : {$hits} -->");
        }

        // no cached content, do we need to cache it?
        wp_cache_add($this->req_key, 0, self::GROUP, self::TIMER);
        if(wp_cache_incr($this->req_key, 1, self::GROUP) >= self::THRESH)
        {
            self::enable();
        }

        // this is here to let someone else enable caching somewhere
        // further down the line by calling Ratcache::enable();
        ob_start(array($this, 'ob'));

        // hack to add a filter to status header
        $wp_filter['status_header'][10]['ratcache'] = array(
            'function'          => array($this, 'set_status_header'),
            'accepted_args'     => 1,
        );
    }

    public function ob($output)
    {
        // bail if we're disabled
        if(!$this->enabled)
            return $output;

        $output = trim($output);

        // bail if empty output
        if(!$output)
            return $output;

        // restart the cache
        if(!$this->start_cache())
            return $output;

        $cache = array(
            'output'        => $output,
            'time'          => time(),
            'status_header' => $this->status,
            'headers'       => array(),
        );

        if(function_exists('apache_response_headers'))
            $cache['headers'] = apache_response_headers();

        foreach($cache['headers'] as $k => $v)
        {
            if(in_array(strtolower($k), self::$nocache_headers))
                unset($cache['headers'][$k]);
        }

        wp_cache_set($this->key, $cache, self::GROUP, self::MAX_AGE);

        header('Last-Modified: ' . date('r', $cache['time']), true);
        header('Cache-Control: max-age=' . self::MAX_AGE . ', must-revalidate', true);

        return $output;
    }

    private function start_cache()
    {
        global $wp_object_cache;

        if(!file_exists(WPINC . '/wp-object-cache.php'))
            return false;

        include_once(WPINC . '/wp-object-cache.php');
        wp_cache_init();

        if(!is_object($wp_object_cache))
            return false;

        if(function_exists('wp_cache_add_global_groups'))
            wp_cache_add_global_groups(array(self::GROUP));

        return true;
    }
}

// run the cache
if(defined('RATCACHE_HOST'))
{
    // let people define a host in wp-config for servers that
    // don't support HTTP_HOST or SERVER_NAME
    Ratcache::instance()->run($_SERVER['REQUEST_URI'], RATCACHE_HOST);
}
elseif(isset($_SERVER['HTTP_HOST']))
{
    Ratcache::instance()->run($_SERVER['REQUEST_URI'], $_SERVER['HTTP_HOST']);
}
elseif(isset($_SERVER['SERVER_NAME']))
{
    Ratcache::instance()->run($_SERVER['REQUEST_URI'], $_SERVER['SERVER_NAME']);
}
