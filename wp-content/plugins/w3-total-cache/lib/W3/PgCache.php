<?php
// To support legacy updates with old add-ins
if (class_exists('W3_PgCache'))
    return;

/**
 * W3 PgCache
 */

/**
 * Class W3_PgCache
 */
class W3_PgCache {
    /**
     * Advanced cache config
     *
     * @var W3_Config
     */
    var $_config = null;

    /**
     * Caching flag
     *
     * @var boolean
     */
    var $_caching = false;

    /**
     * Time start
     *
     * @var double
     */
    var $_time_start = 0;

    /**
     * Lifetime
     * @var integer
     */
    var $_lifetime = 0;

    /**
     * Enhanced mode flag
     *
     * @var boolean
     */
    var $_enhanced_mode = false;

    /**
     * Debug flag
     *
     * @var boolean
     */
    var $_debug = false;

    /**
     * Request host
     * @var string
     */
    var $_request_host = '';

    /**
     * Request URI
     * @var string
     */
    var $_request_uri = '';

    /**
     * Page key
     * @var string
     */
    var $_page_key = '';

    /**
     * Shutdown buffer
     * @var string
     */
    var $_shutdown_buffer = '';

    /**
     * Shutdown compression
     * @var string
     */
    var $_shutdown_compression = '';

    /**
     * Mobile object
     * @var W3_Mobile
     */
    var $_mobile = null;

    /**
     * Referrer object
     * @var W3_Referrer
     */
    var $_referrer = null;

    /**
     * Cache reject reason
     *
     * @var string
     */
    var $cache_reject_reason = '';

    /**
     * @var If sitemap was matched
     */
    var $_sitemap_matched;

    /**
     * @var bool If cached page should be displayed after init
     */
    var $_late_init = false;

    var $_cached_data = null;

    var $_old_exists = false;
    /**
     * Returns instance. 
     * For backward compatibility with 0.9.2.3 version of /wp-content files
     *
     * @return W3_PgCache
     */
    function instance() {
        $i = w3_instance('W3_PgCache');
        $i->_legacy = true;
        return $i;
    }

    /**
     * PHP5 Constructor
     */
    function __construct() {
        $this->_config = w3_instance('W3_Config');
        $this->_debug = $this->_config->get_boolean('pgcache.debug');

        $request_host = w3_get_host();
        $pos = strpos($request_host, ':');
        if ($pos !== false)
            $request_host = substr($request_host, 0, $pos);
        $this->_request_host = $request_host;

        $this->_request_uri = $_SERVER['REQUEST_URI'];
        $this->_lifetime = $this->_config->get_integer('pgcache.lifetime');
        $this->_late_init = $this->_config->get_boolean('pgcache.late_init');
        $this->_enhanced_mode = ($this->_config->get_string('pgcache.engine') == 'file_generic');

        if ($this->_config->get_boolean('mobile.enabled')) {
            $this->_mobile = w3_instance('W3_Mobile');
        }

        if ($this->_config->get_boolean('referrer.enabled')) {
            $this->_referrer = w3_instance('W3_Referrer');
        }
    }

    /**
     * Do cache logic
     */
    function process() {
        /**
         * Skip caching for some pages
         */
        switch (true) {
            case defined('DONOTCACHEPAGE'):
            case defined('DOING_AJAX'):
            case defined('DOING_CRON'):
            case defined('APP_REQUEST'):
            case defined('XMLRPC_REQUEST'):
            case defined('WP_ADMIN'):
            case (defined('SHORTINIT') && SHORTINIT):
                return;
        }

        /*
         * Legacy mode with 0.9.2.3 version of /wp-content add-ins
         */
        if (isset($this->_legacy)) {
            $redirect = w3_instance('W3_Redirect');
            $redirect->process();
        }

        /**
         * Do page cache logic
         */
        if ($this->_debug) {
            $this->_time_start = w3_microtime();
        }

        $this->_caching = $this->_can_cache();
        global $w3_late_init;
        /**
         * @var W3_Dispatcher $dispatcher
         */
        $dispatcher = w3_instance('W3_Dispatcher');
        $dispatcher->set_newrelic_appname($this->_config);

        if ($this->_caching) {
            $this->_cached_data = $this->_extract_cached_page();
            if ($this->_cached_data) {
                if ($this->_late_init) {
                    $w3_late_init = true;
                    return;
                } else {
                    $this->process_cached_page($this->_cached_data);
                    exit;
                }
            } else
                $this->_late_init = false;
        } else {
            $this->_late_init = false;
        }
        $w3_late_init = $this->_late_init;
        /**
         * Start output buffering
         */
         w3tc_add_ob_callback('pagecache', array($this,'ob_callback'));
    }

    /**
     * Extracts page from cache
     *
     * @return boolean
     */
    function _extract_cached_page() {
        $cache = $this->_get_cache();

        $mobile_group = $this->_get_mobile_group();
        $referrer_group = $this->_get_referrer_group();
        $encryption = $this->_get_encryption();
        $compression = $this->_get_compression();
        $raw = !$compression;
        $this->_page_key = $this->_get_page_key($mobile_group, $referrer_group,
            $encryption, $compression);

        $group = '0';
        $sitemap_regex = $this->_config->get_string('pgcache.purge.sitemap_regex');
        if ($sitemap_regex && preg_match('/' . $sitemap_regex . '/', basename($this->_request_uri))) {
            $group = 'sitemaps';
            $this->_sitemap_matched = true;
        } else {
            $this->_sitemap_matched = false;
        }

        /**
         * Check if page is cached
         */
        $data = $cache->get_with_old($this->_page_key, $group);
        list($data, $this->_old_exists) = $data;

        /**
         * Try to get uncompressed version of cache
         */
        if ($compression && !$data) {
            $raw = true;
            $this->_page_key = $this->_get_page_key($mobile_group,
                $referrer_group, $encryption, false);
            $data = $cache->get_with_old($this->_page_key);
            list($data, $this->_old_exists) = $data;
        }

        if (!$data)
            return null;
        $data['raw'] = $raw;
        $data['compression'] = $compression;

        return $data;
    }

    /**
     * Process extracted cached pages
     * @param $data
     */
    public function process_cached_page($data) {
        /**
         * Do Bad Behavior check
         */
        $this->_bad_behavior();

        $is_404 = $data['404'];
        $headers = $data['headers'];
        $time = $data['time'];
        $content = & $data['content'];
        $compression = $data['compression'];
        $raw = $data['raw'];

        /**
         * Calculate content etag
         */
        $etag = md5($content);

        /**
         * Send headers
         */
        $this->_send_headers($is_404, $time, $etag, $compression, $headers);
        if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'HEAD')
            return;

        /**
         * Do manual compression for uncompressed page
         */
        if ($raw) {
            /**
             * Append debug info
             */
            if ($this->_debug && w3_can_print_comment($content)) {
                $time_total = w3_microtime() - $this->_time_start;
                $debug_info = $this->_get_debug_info(true, '', true, $time_total);
                $content .= "\r\n\r\n" . $debug_info;
            }

            /**
             * Parse dynamic tags
             */
            $this->_parse_dynamic($content);

            /**
             * Compress content
             */
            $this->_compress($content, $compression);
        }

        echo $content;
    }

    /**
     * Output buffering callback
     *
     * @param string $buffer
     * @return string
     */
    function ob_callback(&$buffer) {
        if ($buffer != '' && $this->_is_cacheable_content_type()) {
            $compression = false;
            $has_dynamic = $this->_has_dynamic($buffer);
            $can_cache = apply_filters('w3tc_can_cache', $this->_can_cache2($buffer), $this);
            if ($can_cache) {
                list($compression, $buffer) = $this->_can_cache_buffer($buffer, $has_dynamic);
            } else {
                if ($this->_debug && w3_can_print_comment($buffer))
                    $buffer = $this->_debugging_not_caching($buffer, $compression);

                if ($this->cache_reject_reason) {
                    if ($this->_old_exists) {
                        /**
                         * @var W3_Cache_File_Generic $cache
                         */
                        $cache = $this->_get_cache();
                        $cache->hard_delete($this->_page_key);
                    }
                }
            }

            /**
             * We can't capture output in ob_callback
             * so we use shutdown function
             */
            if ($has_dynamic) {
                $this->_shutdown_buffer = $buffer;
                $this->_shutdown_compression = $compression;

                $buffer = '';

                register_shutdown_function(array(
                    &$this,
                    'shutdown'
                ));
            }
        }

        return $buffer;
    }

    /**
     * Shutdown callback
     * @return void
     */
    function shutdown() {
        /**
         * Parse dynamic content
         */
        $this->_parse_dynamic($this->_shutdown_buffer);

        /**
         * Compress page
         */
        $this->_compress($this->_shutdown_buffer, $this->_shutdown_compression);

        echo $this->_shutdown_buffer;
    }

    /**
     * Checks if can we do cache logic
     *
     * @return boolean
     */
    function _can_cache() {
        /**
         * Don't cache in console mode
         */
        if (PHP_SAPI === 'cli') {
            $this->cache_reject_reason = 'Console mode';

            return false;
        }

        /**
         * Skip if session defined
         */
        if (defined('SID') && SID != '') {
            $this->cache_reject_reason = 'Session started';

            return false;
        }

        /**
         * Skip if posting
         */
        if (isset($_SERVER['REQUEST_METHOD']) && in_array(strtoupper($_SERVER['REQUEST_METHOD']), array('DELETE', 'PUT','OPTIONS','TRACE', 'CONNECT','POST'))) {
            $this->cache_reject_reason = sprintf('Requested method is %s', $_SERVER['REQUEST_METHOD']);

            return false;
        }

        /**
         * Skip if HEAD request
         */
        if (isset($_SERVER['REQUEST_METHOD']) && strtoupper($_SERVER['REQUEST_METHOD']) == 'HEAD' &&
            ($this->_enhanced_mode || $this->_config->get_boolean('pgcache.reject.request_head'))) {
            $this->cache_reject_reason = 'Requested method is HEAD';

            return false;
        }

        /**
         * Skip if there is query in the request uri
         */
        if (!$this->_check_query_string() &&!$this->_config->get_boolean('pgcache.cache.query') && strstr($this->_request_uri, '?') !== false) {
            $this->cache_reject_reason = 'Requested URI contains query';

            return false;
        }

        /**
         * Check request URI
         */
        if (!in_array($_SERVER['PHP_SELF'], $this->_config->get_array('pgcache.accept.files')) && !$this->_check_request_uri()) {
            $this->cache_reject_reason = 'Requested URI is rejected';

            return false;
        }

        /**
         * Check User Agent
         */
        if (!$this->_check_ua()) {
            $this->cache_reject_reason = 'User agent is rejected';

            return false;
        }

        /**
         * Check WordPress cookies
         */
        if (!$this->_check_cookies()) {
            $this->cache_reject_reason = 'Cookie is rejected';

            return false;
        }

        /**
         * Skip if user is logged in or user role is logged in
         */
        if ($this->_config->get_boolean('pgcache.reject.logged') ) {
            if (!$this->_check_logged_in()) {
                $this->cache_reject_reason = 'User is logged in';
                return false;
            }
        } else {
            if (!$this->_check_logged_in_role_allowed()) {
                $this->cache_reject_reason = 'An user role is logged in';
                return false;
            }
        }

        return true;
    }

    /**
     * Checks if can we do cache logic
     *
     * @param string $buffer
     * @return boolean
     */
    function _can_cache2(&$buffer) {
        /**
         * Skip if caching is disabled
         */
        if (!$this->_caching) {
            return false;
        }

        /**
         * Check for database error
         */
        if (w3_is_database_error($buffer)) {
            $this->cache_reject_reason = 'Database error occurred';

            return false;
        }

        /**
         * Check for DONOTCACHEPAGE constant
         */
        if (defined('DONOTCACHEPAGE') && DONOTCACHEPAGE) {
            $this->cache_reject_reason = 'DONOTCACHEPAGE constant is defined';

            return false;
        }

        /**
         * Check hostname
         */
        if ((!w3_is_multisite() || (w3_is_multisite() && !w3_force_master())) &&
            $this->_config->get_boolean('pgcache.check.domain') && w3_get_host() != w3_get_home_domain()) {
            $this->cache_reject_reason = 'Hostname mismatch';

            return false;
        }

        /**
         * Don't cache 404 pages
         */
        if (!$this->_config->get_boolean('pgcache.cache.404') && function_exists('is_404') && is_404()) {
            $this->cache_reject_reason = 'Page is 404';

            return false;
        }

        /**
         * Don't cache homepage
         */
        if (!$this->_config->get_boolean('pgcache.cache.home') && function_exists('is_home') && is_home()) {
            $this->cache_reject_reason = is_front_page() && is_home() ? 'Page is front page' : 'Page is posts page';

            return false;
        }

        /**
         * Don't cache front page
         */
        if ($this->_config->get_boolean('pgcache.reject.front_page') && function_exists('is_front_page') && is_front_page() && !is_home()) {
            $this->cache_reject_reason = 'Page is front page';

            return false;
        }

        /**
         * Don't cache feed
         */
        if (!$this->_config->get_boolean('pgcache.cache.feed') && function_exists('is_feed') && is_feed()) {
            $this->cache_reject_reason = 'Page is feed';

            return false;
        }

        /**
         * Check if page contains dynamic tags
         */
        if ($this->_enhanced_mode && $this->_has_dynamic($buffer)) {
            $this->cache_reject_reason = 'Page contains dynamic tags (mfunc or mclude) can not be cached in enhanced mode';

            return false;
        }

        return true;
    }

    /**
     * Returns cache object
     *
     * @return W3_Cache_Base
     */
    function _get_cache() {
        static $cache = array();

        if (!isset($cache[0])) {
            $engine = $this->_config->get_string('pgcache.engine');

            switch ($engine) {
                case 'memcached':
                    $engineConfig = array(
                        'servers' => $this->_config->get_array('pgcache.memcached.servers'),
                        'persistant' => $this->_config->get_boolean('pgcache.memcached.persistant')
                    );
                    break;

                case 'file':
                    $engineConfig = array(
                        'section' => 'page',
                        'flush_parent' => (w3_get_blog_id() == 0),
                        'locking' => $this->_config->get_boolean('pgcache.file.locking'),
                        'flush_timelimit' => $this->_config->get_integer('timelimit.cache_flush')
                    );
                    break;

                case 'file_generic':
                    if (w3_get_blog_id() == 0)
                        $flush_dir = W3TC_CACHE_PAGE_ENHANCED_DIR;
                    else
                        $flush_dir = W3TC_CACHE_PAGE_ENHANCED_DIR . '/' . w3_get_domain(w3_get_host());

                    $engineConfig = array(
                        'exclude' => array(
                            '.htaccess'
                        ),
                        'expire' => $this->_lifetime,
                        'cache_dir' => W3TC_CACHE_PAGE_ENHANCED_DIR,
                        'locking' => $this->_config->get_boolean('pgcache.file.locking'),
                        'flush_timelimit' => $this->_config->get_integer('timelimit.cache_flush'),
                        'flush_dir' => $flush_dir,
                    );
                    break;

                default:
                    $engineConfig = array();
            }

            $engineConfig['use_expired_data'] = true;
            $engineConfig['module'] = 'pgcache';
            $engineConfig['host'] = w3_get_host();
            $engineConfig['instance_id'] = w3_get_instance_id();

            w3_require_once(W3TC_LIB_W3_DIR . '/Cache.php');
            $cache[0] = W3_Cache::instance($engine, $engineConfig);
        }

        return $cache[0];
    }

    /**
     * Checks request URI
     *
     * @return boolean
     */
    function _check_request_uri() {
        $auto_reject_uri = array(
            'wp-login',
            'wp-register'
        );

        foreach ($auto_reject_uri as $uri) {
            if (strstr($this->_request_uri, $uri) !== false) {
                return false;
            }
        }

        $reject_uri = $this->_config->get_array('pgcache.reject.uri');
        $reject_uri = array_map('w3_parse_path', $reject_uri);

        foreach ($reject_uri as $expr) {
            $expr = trim($expr);
            if ($expr != '' && preg_match('~' . $expr . '~i', $this->_request_uri)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Checks User Agent
     *
     * @return boolean
     */
    function _check_ua() {
        w3_require_once(W3TC_LIB_W3_DIR . '/Request.php');

        $uas = $this->_config->get_array('pgcache.reject.ua');

        $uas = array_merge($uas, array(W3TC_POWERED_BY));

        foreach ($uas as $ua) {
            if (!empty($ua)) {
                if (isset($_SERVER['HTTP_USER_AGENT']) && stristr($_SERVER['HTTP_USER_AGENT'], $ua) !== false)
                    return false;
            }
        }

        return true;
    }

    /**
     * Checks WordPress cookies
     *
     * @return boolean
     */
    function _check_cookies() {
        foreach (array_keys($_COOKIE) as $cookie_name) {
            if ($cookie_name == 'wordpress_test_cookie') {
                continue;
            }
            if (preg_match('/^(wp-postpass|comment_author)/', $cookie_name)) {
                return false;
            }
        }

        foreach ($this->_config->get_array('pgcache.reject.cookie') as $reject_cookie) {
            if (!empty($reject_cookie)) {
                foreach (array_keys($_COOKIE) as $cookie_name) {
                    if (strstr($cookie_name, $reject_cookie) !== false) {
                        return false;
                    }
                }
            }
        }

        return true;
    }

    /**
     * Check if user is logged in
     *
     * @return boolean
     */
    function _check_logged_in() {
        foreach (array_keys($_COOKIE) as $cookie_name) {
            if (strpos($cookie_name, 'wordpress_logged_in') === 0)
                return false;
        }

        return true;
    }

    /**
     * Check if logged in user role is allwed to be cached
     *
     * @return boolean
     */
    function _check_logged_in_role_allowed() {
        if (!$this->_config->get_boolean('pgcache.reject.logged_roles'))
             return true;
        $roles = $this->_config->get_array('pgcache.reject.roles');
        
        if (empty($roles))
            return true;
        
        foreach (array_keys($_COOKIE) as $cookie_name) {
            if (strpos($cookie_name, 'w3tc_logged_') === 0) {
                foreach($roles as $role) {
                    if(strstr($cookie_name, md5(NONCE_KEY . $role)))
                        return false;
                }
            }
        }

        return true;
    }

    /**
     * Checks if rules file present and creates it if not
     */
    function _check_rules_present() {
        if (w3_is_nginx())
            return;   // nginx store it in a single file

        w3_require_once(W3TC_INC_DIR . '/functions/rule_cut.php');

        $filename = w3_get_pgcache_rules_cache_path();
        if (file_exists($filename))
            return;

        // we call it as little times as possible
        // its expensive, but have to restore lost .htaccess file
        $e = w3_instance('W3_PgCacheAdminEnvironment');
        try {
            $e->fix_on_wpadmin_request($this->_config, true);
        } catch (Exception $ex) {
        }
    }

    /**
     * Compress data
     *
     * @param string $data
     * @param string $compression
     * @return string
     */
    function _compress(&$data, $compression) {
        switch ($compression) {
            case 'gzip':
                $data = gzencode($data);
                break;

            case 'deflate':
                $data = gzdeflate($data);
                break;
        }
    }

    /**
     * Returns current mobile group
     *
     * @return string
     */
    function _get_mobile_group() {
        if ($this->_mobile) {
            return $this->_mobile->get_group();
        }

        return '';
    }

    /**
     * Returns current referrer group
     *
     * @return string
     */
    function _get_referrer_group() {
        if ($this->_referrer) {
            return $this->_referrer->get_group();
        }

        return '';
    }

    /**
     * Returns current encryption
     *
     * @return string
     */
    function _get_encryption() {
        if (w3_is_https()) {
            return 'ssl';
        }

        return '';
    }

    /**
     * Returns current compression
     *
     * @return boolean
     */
    function _get_compression() {
        if ($this->_debug)   // cannt generate/use compressed files during debug mode
            return false;

        if (!w3_zlib_output_compression() && !headers_sent() && !$this->_is_buggy_ie()) {
            $compressions = $this->_get_compressions();

            foreach ($compressions as $compression) {
                if (isset($_SERVER['HTTP_ACCEPT_ENCODING']) && stristr($_SERVER['HTTP_ACCEPT_ENCODING'], $compression) !== false) {
                    return $compression;
                }
            }
        }

        return false;
    }

    /**
     * Returns array of compressions
     *
     * @return array
     */
    function _get_compressions() {
        $compressions = array(
            false
        );

        if ($this->_config->get_boolean('browsercache.enabled') && $this->_config->get_boolean('browsercache.html.compression') && function_exists('gzencode')) {
            $compressions[] = 'gzip';
        }

        return $compressions;
    }

    /**
     * Returns array of response headers
     *
     * @return array
     */
    function _get_response_headers() {
        $headers = array();

        if (function_exists('headers_list')) {
            $headers_list = headers_list();
            if ($headers_list) {
                foreach ($headers_list as $header) {
                    $pos = strpos($header, ':');
                    if ($pos) {
                        $header_name = substr($header, 0, $pos);
                        $header_value = substr($header, $pos+1);
                    } else {
                        $header_name = $header;
                        $header_value = '';
                    }
                    $headers[$header_name] = $header_value;
                }
            }
        }

        return $headers;
    }

    /**
     * Checks for buggy IE6 that doesn't support compression
     *
     * @return boolean
     */
    function _is_buggy_ie() {
        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            $ua = $_SERVER['HTTP_USER_AGENT'];

            if (strpos($ua, 'Mozilla/4.0 (compatible; MSIE ') === 0 && strpos($ua, 'Opera') === false) {
                $version = (float) substr($ua, 30);

                return ($version < 6 || ($version == 6 && strpos($ua, 'SV1') === false));
            }
        }

        return false;
    }

    /**
     * Returns array of data headers
     *
     * @return array
     */
    function _get_cached_headers() {
        $data_headers = array();
        $cache_headers = $this->_config->get_array('pgcache.cache.headers');
        $response_headers = $this->_get_response_headers();

        foreach ($response_headers as $header_name => $header_value) {
            foreach ($cache_headers as $cache_header_name) {
                if (strcasecmp($header_name, $cache_header_name) == 0) {
                    $data_headers[$header_name] = $header_value;
                }
            }
        }

        return $data_headers;
    }

    /**
     * Returns page key
     *
     * @param string $mobile_group
     * @param string $referrer_group
     * @param string $encryption
     * @param string $compression
     * @param string $content_type
     * @param string $request_uri
     * @return string
     */
    function _get_page_key($mobile_group = '', $referrer_group = '', 
        $encryption = '', $compression = '', $content_type = '', $request_uri = '') {

        if ($request_uri)
            $key = substr($request_uri, strtolower(substr($request_uri, 0, 8)) == 'https' ? 8: 7);
        else
            $key = $this->_request_host . $this->_request_uri;

        // replace fragment
        $key = preg_replace('~#.*$~', '', $key);

        if ($this->_enhanced_mode) {
            // URL decode
            $key = urldecode($key);

            // replace double slashes
            $key = preg_replace('~[/\\\]+~', '/', $key);

            // replace query string
            $key = preg_replace('~\?.*$~', '', $key);

            // replace index.php
            $key = str_replace('/index.php', '/', $key);

            // trim slash
            $key = ltrim($key, '/');

            if ($key && substr($key, -1) != '/') {
                $key .= '/';
            }

            $key .= '_index';
        } else {
            if ($this->_check_query_string())
                // replace query string
                $key = preg_replace('~\?.*$~', '', $key);

            $key = md5($key);
        }

        /**
         * Append mobile group
         */
        if ($mobile_group) {
            $key .= '_' . $mobile_group;
        }

        /**
         * Append referrer group
         */
        if ($referrer_group) {
            $key .= '_' . $referrer_group;
        }

        /**
         * Append encryption
         */
        if ($encryption) {
            $key .= '_' . $encryption;
        }

        if (w3_is_preview_mode()) {
            $key .= '_preview';
        }

        if ($this->_enhanced_mode) {
            /**
             * Append HTML extension.
             * For nginx - we create .xml cache entries and redirect to them
             */
            if (w3_is_nginx() && substr($content_type, 0, 8) == 'text/xml' &&
                  $this->_config->get_boolean('pgcache.cache.nginx_handle_xml'))
                $key .= '.xml';
            else
                $key .= '.html';
        }

        /**
         * Append compression
         */
        if ($compression) {
            $key .= '_' . $compression;
        }

        return $key;
    }

    /**
     * Returns debug info
     *
     * @param boolean $cache
     * @param string $reason
     * @param boolean $status
     * @param double $time
     * @return string
     */
    function _get_debug_info($cache, $reason, $status, $time) {
        $engine = $this->_config->get_string('pgcache.engine');
        $debug_info = "<!-- W3 Total Cache: Page cache debug info:\r\n";
        $debug_info .= sprintf("%s%s\r\n", str_pad('Engine: ', 20), w3_get_engine_name($engine));
        $debug_info .= sprintf("%s%s\r\n", str_pad('Cache key: ', 20), w3_escape_comment($this->_page_key));
        $debug_info .= sprintf("%s%s\r\n", str_pad('Caching: ', 20), ($cache ? 'enabled' : 'disabled'));

        if (!$cache) {
            $debug_info .= sprintf("%s%s\r\n", str_pad('Reject reason: ', 20), $reason);
        }

        if ($engine != 'file_generic')
            $debug_info .= sprintf("%s%s\r\n", str_pad('Status: ', 20), ($status ? 'cached' : 'not cached'));
        $debug_info .= sprintf("%s%.3fs\r\n", str_pad('Creation Time: ', 20), $time);

        $headers = $this->_get_response_headers();

        if (count($headers)) {
            $debug_info .= "Header info:\r\n";

            foreach ($headers as $header_name => $header_value) {
                $debug_info .= sprintf("%s%s\r\n", str_pad(w3_escape_comment($header_name) . ': ', 20), w3_escape_comment($header_value));
            }
        }

        $debug_info .= '-->';

        return $debug_info;
    }

    /**
     * Sends headers
     *
     * @param array $headers
     * @return boolean
     */
    function _headers($headers) {
        if (!headers_sent()) {
            foreach ($headers as $name => $value) {
                $header = ($name == 'Status' ? $value : $name . ': ' . $value);
                @header($header);
            }

            return true;
        }

        return false;
    }

    /**
     * Sends headers
     * @param boolean $is_404
     * @param string $etag
     * @param integer $time
     * @param string $compression
     * @param array $custom_headers
     * @return boolean
     */
    function _send_headers($is_404, $time, $etag, $compression, $custom_headers = array()) {
        $exit = false;
        $headers = array();
        $curr_time = time();

        $bc_lifetime = $this->_config->get_integer(
            'browsercache.html.lifetime');

        $expires = (is_null($time)? $curr_time: $time) + $bc_lifetime;
        $max_age = ($expires > $curr_time ? $expires - $curr_time : 0);

        if ($is_404) {
            /**
             * Add 404 header
             */
            $headers = array_merge($headers, array(
                'Status' => 'HTTP/1.1 404 Not Found'
            ));
        } elseif ((!is_null($time) && $this->_check_modified_since($time)) || $this->_check_match($etag)) {
            /**
             * Add 304 header
             */
            $headers = array_merge($headers, array(
                'Status' => 'HTTP/1.1 304 Not Modified'
            ));

            /**
             * Don't send content if it isn't modified
             */
            $exit = true;
        }

        if ($this->_config->get_boolean('browsercache.enabled')) {

            if ($this->_config->get_boolean('browsercache.html.last_modified')) {
                $headers = array_merge($headers, array(
                    'Last-Modified' => w3_http_date($time)
                ));
            }

            if ($this->_config->get_boolean('browsercache.html.expires')) {
                $headers = array_merge($headers, array(
                    'Expires' => w3_http_date($expires)
                ));
            }

            if ($this->_config->get_boolean('browsercache.html.cache.control')) {
                switch ($this->_config->get_string('browsercache.html.cache.policy')) {
                    case 'cache':
                        $headers = array_merge($headers, array(
                            'Pragma' => 'public',
                            'Cache-Control' => 'public'
                        ));
                        break;

                    case 'cache_public_maxage':
                        $headers = array_merge($headers, array(
                            'Pragma' => 'public',
                            'Cache-Control' => sprintf('max-age=%d, public', $max_age)
                        ));
                        break;

                    case 'cache_validation':
                        $headers = array_merge($headers, array(
                            'Pragma' => 'public',
                            'Cache-Control' => 'public, must-revalidate, proxy-revalidate'
                        ));
                        break;

                    case 'cache_noproxy':
                        $headers = array_merge($headers, array(
                            'Pragma' => 'public',
                            'Cache-Control' => 'private, must-revalidate'
                        ));
                        break;

                    case 'cache_maxage':
                        $headers = array_merge($headers, array(
                            'Pragma' => 'public',
                            'Cache-Control' => sprintf('max-age=%d, public, must-revalidate, proxy-revalidate', $max_age)
                        ));
                        break;

                    case 'no_cache':
                        $headers = array_merge($headers, array(
                            'Pragma' => 'no-cache',
                            'Cache-Control' => 'max-age=0, private, no-store, no-cache, must-revalidate'
                        ));
                        break;
                }
            }

            if ($this->_config->get_boolean('browsercache.html.etag')) {
                $headers = array_merge($headers, array(
                    'Etag' => $etag
                ));
            }

            if ($this->_config->get_boolean('browsercache.html.w3tc')) {
                $headers = array_merge($headers, array(
                    'X-Powered-By' => W3TC_POWERED_BY
                ));
            }
        }

        $vary ='';
        //compressed && UAG
        if ($compression && $this->_get_mobile_group()) {
            $vary = 'Accept-Encoding,User-Agent,Cookie';
            $headers = array_merge($headers, array(
                'Content-Encoding' => $compression
            ));
            //compressed
        } elseif ($compression) {
            $vary = 'Accept-Encoding';
            $headers = array_merge($headers, array(
                'Content-Encoding' => $compression
            ));
            //uncompressed && UAG
        } elseif ($this->_get_mobile_group()) {
            $vary = 'User-Agent,Cookie';
        }

        //Add Cookie to vary if user logged in and not previously set
        if (!$this->_check_logged_in() && strpos($vary, 'Cookie') === false) {
            if ($vary)
                $vary .= ',Cookie';
            else
                $vary = 'Cookie';
        }

        /**
         * Add vary header
         */
        if ($vary)
            $headers = array_merge($headers, array(
                'Vary' => $vary));

        /**
         * Add custom headers
         */
        $headers = array_merge($headers, $custom_headers);

        /**
         * Disable caching for preview mode
         */
        if (w3_is_preview_mode()) {
            $headers = array_merge($headers, array(
                'Pragma' => 'private',
                'Cache-Control' => 'private'
            ));
        }

        /**
         * Send headers to client
         */
        $result = $this->_headers($headers);

        if ($exit) {
            exit();
        }

        return $result;
    }

    /**
     * Check if content was modified by time
     * @param integer $time
     * @return boolean
     */
    function _check_modified_since($time) {
        if (!empty($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
            $if_modified_since = $_SERVER['HTTP_IF_MODIFIED_SINCE'];

            // IE has tacked on extra data to this header, strip it
            if (($semicolon = strrpos($if_modified_since, ';')) !== false) {
                $if_modified_since = substr($if_modified_since, 0, $semicolon);
            }

            return ($time == strtotime($if_modified_since));
        }

        return false;
    }

    /**
     * Check if content was modified by etag
     * @param string $etag
     * @return boolean
     */
    function _check_match($etag) {
        if (!empty($_SERVER['HTTP_IF_NONE_MATCH'])) {
            $if_none_match = (get_magic_quotes_gpc() ? stripslashes($_SERVER['HTTP_IF_NONE_MATCH']) : $_SERVER['HTTP_IF_NONE_MATCH']);
            $client_etags = explode(',', $if_none_match);

            foreach ($client_etags as $client_etag) {
                $client_etag = trim($client_etag);

                if ($etag == $client_etag) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Bad Behavior support
     * @return void
     */
    function _bad_behavior() {
        $bb_file = $this->_config->get_cache_option('pgcache.bad_behavior_path');
        if ($bb_file != '')
            require_once $bb_file;
    }

    /**
     * Parses dynamic tags
     */
    function _parse_dynamic(&$buffer) {
        if (!defined('W3TC_DYNAMIC_SECURITY'))
            return;

        $buffer = preg_replace_callback('~<!--\s*mfunc\s*' . W3TC_DYNAMIC_SECURITY . '(.*)-->(.*)<!--\s*/mfunc\s*' . W3TC_DYNAMIC_SECURITY . '\s*-->~Uis', array(
            &$this,
            '_parse_dynamic_mfunc'
        ), $buffer);

        $buffer = preg_replace_callback('~<!--\s*mclude\s*' . W3TC_DYNAMIC_SECURITY . '(.*)-->(.*)<!--\s*/mclude\s*' . W3TC_DYNAMIC_SECURITY . '\s*-->~Uis', array(
            &$this,
            '_parse_dynamic_mclude'
        ), $buffer);
    }

    /**
     * Parse dynamic mfunc callback
     * @param array $matches
     * @return string
     */
    function _parse_dynamic_mfunc($matches) {
        $code1 = trim($matches[1]);
        $code2 = trim($matches[2]);
        $code = ($code1 ? $code1 : $code2);

        if ($code) {
            $code = trim($code, ';') . ';';

            ob_start();
            $result = eval($code);
            $output = ob_get_contents();
            ob_end_clean();

            if ($result === false) {
                $output = sprintf('Unable to execute code: %s', htmlspecialchars($code));
            }
        } else {
            $output = htmlspecialchars('Invalid mfunc tag syntax. The correct format is: <!-- W3TC_DYNAMIC_SECURITY mfunc PHP code --><!-- /mfunc W3TC_DYNAMIC_SECURITY --> or <!-- W3TC_DYNAMIC_SECURITY mfunc -->PHP code<!-- /mfunc W3TC_DYNAMIC_SECURITY -->.');
        }

        return $output;
    }

    /**
     * Parse dynamic mclude callback
     * @param array $matches
     * @return string
     */
    function _parse_dynamic_mclude($matches) {
        $file1 = trim($matches[1]);
        $file2 = trim($matches[2]);

        $file = ($file1 ? $file1 : $file2);

        if ($file) {
            $file = ABSPATH . $file;

            if (file_exists($file) && is_readable($file)) {
                ob_start();
                include $file;
                $output = ob_get_contents();
                ob_end_clean();
            } else {
                $output = sprintf('Unable to open file: %s', htmlspecialchars($file));
            }
        } else {
            $output = htmlspecialchars('Incorrect mclude tag syntax. The correct format is: <!-- mclude W3TC_DYNAMIC_SECURITY path/to/file.php --><!-- /mclude W3TC_DYNAMIC_SECURITY --> or <!-- mclude W3TC_DYNAMIC_SECURITY -->path/to/file.php<!-- /mclude W3TC_DYNAMIC_SECURITY -->.');
        }

        return $output;
    }

    /**
     * Checks if buffer has dynamic tags
     *
     * @param string $buffer
     * @return boolean
     */
    function _has_dynamic(&$buffer) {
        if (!defined('W3TC_DYNAMIC_SECURITY'))
            return false;

        return preg_match('~<!--\s*m(func|clude)\s*' . W3TC_DYNAMIC_SECURITY . '(.*)-->(.*)<!--\s*/m(func|clude)\s*' . W3TC_DYNAMIC_SECURITY . '\s*-->~Uis', $buffer);
    }

    /**
     * Check whether requested page has content type that can be cached
     * @return bool
     */
    private function _is_cacheable_content_type() {
        $content_type = '';
        $headers = headers_list();
        foreach($headers as $header) {
            $header = strtolower($header);
            if (stripos($header, 'content-type') !== false) {
                $temp = explode(';', $header);
                $temp = array_shift($temp);
                $temp = explode(':', $temp);
                $content_type = trim($temp[1]);
            }
        }

        $cache_headers = apply_filters('w3tc_is_cacheable_content_type',
            array('application/json', 'text/html', 'text/xml', 'application/xhtml+xml'));
        return in_array($content_type, $cache_headers);
    }

    private function _check_query_string() {
        $accept_qs = $this->_config->get_array('pgcache.accept.qs');
        foreach ($_GET as $key => $value) {
            if (!in_array(strtolower($key), $accept_qs))
                return false;
        }
        return true;
    }

    /**
     *
     */
    public function delayed_cache_print() {
        if ($this->_late_init && $this->_caching) {
            $this->process_cached_page($this->_cached_data);
            exit;
        }
    }

    /**
     * @param $buffer
     * @param $has_dynamic
     * @return array
     */
    private function _can_cache_buffer(&$buffer, $has_dynamic) {
        $mobile_group = $this->_get_mobile_group();
        $referrer_group = $this->_get_referrer_group();
        $encryption = $this->_get_encryption();
        $compression = $this->_get_compression();
        $compressions = $this->_get_compressions();

        /**
         * Don't use compression for debug mode or dynamic tags
         * because we need to modify buffer before send it to client
         */
        $raw = ($this->_debug || $has_dynamic);
        if ($raw) {
            $compression = false;
            $compressions = array(false);
        }

        $content_type = '';
        $is_404 = (function_exists('is_404') ? is_404() : false);
        $headers = $this->_get_cached_headers();

        if ($this->_enhanced_mode && !$this->_late_init) {
            $this->_check_rules_present();
            if (isset($headers['Content-Type']))
                $content_type = $headers['Content-Type'];
        }

        $time = time();
        $cache = $this->_get_cache();

        /**
         * Store different versions of cache
         */
        $buffers = array();

        $group = '0';
        if (!isset($this->_sitemap_matched)) {
            $sitemap_regex = $this->_config->get_string('pgcache.purge.sitemap_regex');
            if ($sitemap_regex && preg_match('/' . $sitemap_regex . '/', basename($this->_request_uri))) {
                $group = 'sitemaps';
                $this->_sitemap_matched = true;
            }
        } elseif ($this->_sitemap_matched)
            $group = 'sitemaps';

        foreach ($compressions as $_compression) {
            $_page_key = $this->_get_page_key($mobile_group,
                $referrer_group, $encryption, $_compression,
                $content_type);

            /**
             * Compress content
             */
            $buffers[$_compression] = $buffer;

            $this->_compress($buffers[$_compression], $_compression);

            /**
             * Store cache data
             */
            $_data = array(
                '404' => $is_404,
                'headers' => $headers,
                'time' => $time,
                'content' => &$buffers[$_compression]
            );

            $cache->set($_page_key, $_data, $this->_lifetime, $group);
        }

        /**
         * Change buffer if using compression
         */
        if ($compression && isset($buffers[$compression])) {
            $buffer = & $buffers[$compression];
        }

        /**
         * Calculate content etag
         */
        $etag = md5($buffer);

        /**
         * Send headers
         */
        $this->_send_headers($is_404, $time, $etag, $compression, $headers);

        if ($raw) {
            if ($this->_debug && w3_can_print_comment($buffer)) {
                $buffer = $this->_debugging_caching($buffer, $mobile_group, $referrer_group, $encryption,
                                                    $compression, $content_type);
            }

            /**
             * Don't use shutdown function below
             */
            if (!$has_dynamic) {
                $this->_compress($buffer, $compression);
                return array($compression, $buffer);
            }
            return array($compression, $buffer);
        }
        return array($compression, $buffer);
    }

    /**
     * @param $buffer
     * @param $compression
     * @return string
     */
    private function _debugging_not_caching(&$buffer, $compression) {
        $mobile_group = $this->_get_mobile_group();
        $referrer_group = $this->_get_referrer_group();
        $encryption = $this->_get_encryption();

        /**
         * Set page key for debug
         */
        $this->_page_key = $this->_get_page_key($mobile_group,
            $referrer_group, $encryption, $compression);

        /**
         * Append debug info
         */
        $time_total = w3_microtime() - $this->_time_start;
        $debug_info = $this->_get_debug_info(false, $this->cache_reject_reason, false, $time_total);
        $buffer .= "\r\n\r\n" . $debug_info;
        return $buffer;
    }

    /**
     * @param $buffer
     * @param $mobile_group
     * @param $referrer_group
     * @param $encryption
     * @param $compression
     * @param $content_type
     * @return string
     */
    private function _debugging_caching(&$buffer, $mobile_group, $referrer_group, $encryption,
                                        $compression = '', $content_type = '') {
        /**
         * Set page key for debug
         */
        $this->_page_key = $this->_get_page_key($mobile_group,
            $referrer_group, $encryption, $compression,
            $content_type);

        /**
         * Append debug info
         */
        $time_total = w3_microtime() - $this->_time_start;
        $debug_info = $this->_get_debug_info(true, '', false, $time_total);
        $buffer .= "\r\n\r\n" . $debug_info;
        return $buffer;
    }
}
