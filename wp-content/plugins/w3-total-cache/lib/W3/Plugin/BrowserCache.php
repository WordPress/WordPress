<?php

/**
 * W3 ObjectCache plugin
 */
if (!defined('W3TC')) {
    die();
}

w3_require_once(W3TC_LIB_W3_DIR . '/Plugin.php');

/**
 * Class W3_Plugin_BrowserCache
 */
class W3_Plugin_BrowserCache extends W3_Plugin {
    /**
     * Runs plugin
     */
    function run() {
        if ($this->_config->get_boolean('browsercache.html.w3tc')) {
            add_action('send_headers', array(
                &$this,
                'send_headers'
            ));
        }

        if (!$this->_config->get_boolean('browsercache.html.etag')) {
            add_filter('wp_headers', array(
                &$this,
                'filter_wp_headers')
                ,0,2);
        }

        if ($this->can_ob()) {
            w3tc_add_ob_callback('browsercache', array($this,'ob_callback'));

            // modify CDN urls too
            add_filter('w3tc_cdn_url', array(
                &$this,
                'w3tc_cdn_url')
                ,0, 2);
        }
    }

    /**
     * Check if we can start OB
     *
     * @return boolean
     */
    function can_ob() {
        /**
         * Replace feature should be enabled
         */
        if (!$this->_config->get_boolean('browsercache.cssjs.replace') && !$this->_config->get_boolean('browsercache.html.replace') && !$this->_config->get_boolean('browsercache.other.replace')) {
            return false;
        }

        /**
         * Skip if admin
         */
        if (defined('WP_ADMIN')) {
            return false;
        }

        /**
         * Skip if doing AJAX
         */
        if (defined('DOING_AJAX')) {
            return false;
        }

        /**
         * Skip if doing cron
         */
        if (defined('DOING_CRON')) {
            return false;
        }

        /**
         * Skip if APP request
         */
        if (defined('APP_REQUEST')) {
            return false;
        }

        /**
         * Skip if XMLRPC request
         */
        if (defined('XMLRPC_REQUEST')) {
            return false;
        }

        /**
         * Check for WPMU's and WP's 3.0 short init
         */
        if (defined('SHORTINIT') && SHORTINIT) {
            return false;
        }

        /**
         * Check User Agent
         */
        if (isset($_SERVER['HTTP_USER_AGENT']) && stristr($_SERVER['HTTP_USER_AGENT'], W3TC_POWERED_BY) !== false) {
            return false;
        }

        return true;
    }

    /**
     * Output buffer callback
     *
     * @param string $buffer
     * @return mixed
     */
    function ob_callback(&$buffer) {
        if ($buffer != '' && w3_is_xml($buffer)) {
            $domain_url_regexp = w3_get_domain_url_regexp();

            $buffer = preg_replace_callback('~(href|src|action|extsrc|asyncsrc|w3tc_load_js\()=?[\'"]((' . $domain_url_regexp . ')?(/[^\'"]*\.([a-z-_]+)(\?[^\'"]*)?))[\'"]~Ui', array(
                &$this,
                'link_replace_callback'
            ), $buffer);
        }

        return $buffer;
    }

    /**
     * Link replace callback
     *
     * @param string $matches
     * @return string
     */
    function link_replace_callback($matches) {
        list ($match, $attr, $url, , , , , $extension) = $matches;

        if (!$this->_url_has_to_be_replaced($url, $extension))
            return $match;

        static $id = null;
        if ($id === null)
            $id = $this->get_replace_id();

        $url = w3_remove_query($url);
        $url .= (strstr($url, '?') !== false ? '&amp;' : '?') . $id;

        if ($attr != 'w3tc_load_js(')
            return sprintf('%s="%s"', $attr, $url);
        return sprintf('%s\'%s\'', $attr, $url);
    }

    /**
     * Link replace for CDN url
     *
     * @param string $matches
     * @return string
     */
    function w3tc_cdn_url($url, $original_url) {
        // decouple extension
        $matches = array();
        if (!preg_match('/\.([a-zA-Z0-9]+)$/', $original_url, $matches))
            return $url;
        $extension = $matches[1];

        if (!$this->_url_has_to_be_replaced($original_url, $extension))
            return $url;

        static $id = null;
        if ($id === null)
            $id = $this->get_replace_id();

        $url .= (strstr($url, '?') !== false ? '&amp;' : '?') . $id;
        return $url;
    }

    function _url_has_to_be_replaced($url, $extension) {
        static $extensions = null;
        if ($extensions === null)
            $extensions = $this->get_replace_extensions();

        static $exceptions = null;
        if ($exceptions === null)
            $exceptions = $this->_config->get_array('browsercache.replace.exceptions');

        if (!in_array($extension, $extensions))
            return false;

        $test_url = w3_remove_query($url);
        foreach ($exceptions as $exception) {
            if (trim($exception) && preg_match('/' . $exception . '/',$test_url))
                return false;
        }

        return true;
    }

    /**
     * Returns replace ID
     *
     * @return string
     */
    function get_replace_id() {
        static $cache_id = null;

        if ($cache_id === null) {
            $keys = array(
                'browsercache.cssjs.compression',
                'browsercache.cssjs.expires',
                'browsercache.cssjs.lifetime',
                'browsercache.cssjs.cache.control',
                'browsercache.cssjs.cache.policy',
                'browsercache.cssjs.etag',
                'browsercache.cssjs.w3tc',
                'browsercache.html.compression',
                'browsercache.html.expires',
                'browsercache.html.lifetime',
                'browsercache.html.cache.control',
                'browsercache.html.cache.policy',
                'browsercache.html.etag',
                'browsercache.html.w3tc',
                'browsercache.other.compression',
                'browsercache.other.expires',
                'browsercache.other.lifetime',
                'browsercache.other.cache.control',
                'browsercache.other.cache.policy',
                'browsercache.other.etag',
                'browsercache.other.w3tc',
                'browsercache.timestamp'
            );

            $values = array();

            foreach ($keys as $key) {
                $values[] = $this->_config->get($key);
            }

            $cache_id = substr(md5(implode('', $values)), 0, 6);
        }

        return $cache_id;
    }

    /**
     * Returns replace extensions
     *
     * @return array
     */
    function get_replace_extensions() {
        static $extensions = null;

        if ($extensions === null) {
            $types = array();
            $extensions = array();

            if ($this->_config->get_boolean('browsercache.cssjs.replace')) {
                $types = array_merge($types, array_keys($this->_get_cssjs_types()));
            }

            if ($this->_config->get_boolean('browsercache.html.replace')) {
                $types = array_merge($types, array_keys($this->_get_html_types()));
            }

            if ($this->_config->get_boolean('browsercache.other.replace')) {
                $types = array_merge($types, array_keys($this->_get_other_types()));
            }

            foreach ($types as $type) {
                $extensions = array_merge($extensions, explode('|', $type));
            }
        }

        return $extensions;
    }

    /**
     * Send headers
     */
    function send_headers() {
        @header('X-Powered-By: ' . W3TC_POWERED_BY);
    }

    /**
     * Returns CSS/JS mime types
     *
     * @return array
     */
    function _get_cssjs_types() {
        $mime_types = include W3TC_INC_DIR . '/mime/cssjs.php';

        return $mime_types;
    }

    /**
     * Returns HTML mime types
     *
     * @return array
     */
    function _get_html_types() {
        $mime_types = include W3TC_INC_DIR . '/mime/html.php';

        return $mime_types;
    }

    /**
     * Returns other mime types
     *
     * @return array
     */
    function _get_other_types() {
        $mime_types = include W3TC_INC_DIR . '/mime/other.php';

        return $mime_types;
    }

    /**
     * Returns cache config for CDN
     *
     * @return array
     */
    function get_cache_config() {
        $config = array();

        $e = w3_instance('W3_BrowserCacheAdminEnvironment');
        $mime_types = $e->get_mime_types();

        foreach ($mime_types as $type => $extensions)
            $this->_get_cache_config($config, $extensions, $type);

        return $config;
    }

    /**
     * Writes cache config
     *
     * @param string $config
     * @param array $mime_types
     * @param array $section
     * @return void
     */
    function _get_cache_config(&$config, $mime_types, $section) {
        $expires = $this->_config->get_boolean('browsercache.' . $section . '.expires');
        $lifetime = $this->_config->get_integer('browsercache.' . $section . '.lifetime');
        $cache_control = $this->_config->get_boolean('browsercache.' . $section . '.cache.control');
        $cache_policy = $this->_config->get_string('browsercache.' . $section . '.cache.policy');
        $etag = $this->_config->get_boolean('browsercache.' . $section . '.etag');
        $w3tc = $this->_config->get_boolean('browsercache.' . $section . '.w3tc');

        foreach ($mime_types as $mime_type) {
            if (is_array($mime_type)) {
                foreach($mime_type as $mime_type2)
                    $config[$mime_type2] = array(
                        'etag' => $etag,
                        'w3tc' => $w3tc,
                        'lifetime' => $lifetime,
                        'expires' => $expires,
                        'cache_control' => ($cache_control ? $cache_policy : false)
                    );
            } else
                $config[$mime_type] = array(
                    'etag' => $etag,
                    'w3tc' => $w3tc,
                    'lifetime' => $lifetime,
                    'expires' => $expires,
                    'cache_control' => ($cache_control ? $cache_policy : false)
                );
        }
    }

    /**
     * Filters headers set by WordPress
     * @param $headers
     * @param $wp
     * @return
     */
    function filter_wp_headers($headers, $wp) {
        if (!empty($wp->query_vars['feed']))
            unset($headers['ETag']);
        return $headers;
    }
}
