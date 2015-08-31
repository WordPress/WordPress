<?php

/**
 * W3 Total Cache plugin
 */
if (!defined('W3TC')) {
    die();
}

w3_require_once(W3TC_LIB_W3_DIR . '/Plugin.php');

/**
 * Class W3_Plugin_TotalCache
 */
class W3_Plugin_TotalCache extends W3_Plugin {

    private $_translations = array();
    /**
     * Runs plugin
     *
     * @return void
     */
    function run() {
        add_action('init', array(
            &$this,
            'init'
        ));
        if (w3tc_is_pro_dev_mode() && w3_is_pro($this->_config))
            add_action('wp_footer', array($this, 'pro_dev_mode'));

        add_action('admin_bar_menu', array(
            &$this,
            'admin_bar_menu'
        ), 150);

        if (isset($_REQUEST['w3tc_theme']) && isset($_SERVER['HTTP_USER_AGENT']) &&
                $_SERVER['HTTP_USER_AGENT'] == W3TC_POWERED_BY) {
            add_filter('template', array(
                &$this,
                'template_preview'
            ));

            add_filter('stylesheet', array(
                &$this,
                'stylesheet_preview'
            ));
        } elseif ($this->_config->get_boolean('mobile.enabled') || $this->_config->get_boolean('referrer.enabled')) {
            add_filter('template', array(
                &$this,
                'template'
            ));

            add_filter('stylesheet', array(
                &$this,
                'stylesheet'
            ));
        }
        
        /**
         * Create cookies to flag if a pgcache role was loggedin
         */
        if (!$this->_config->get_boolean('pgcache.reject.logged') && $this->_config->get_array('pgcache.reject.logged_roles')) {
            add_action( 'set_logged_in_cookie', array(
                &$this,
                'check_login_action'
            ), 0, 5);
            add_action( 'clear_auth_cookie', array(
                &$this,
                'check_login_action'
            ), 0, 5);
        }

        if ($this->_config->get_string('common.support') == 'footer') {
            add_action('wp_footer', array(
                &$this,
                'footer'
            ));
        }

        if ($this->can_ob()) {
            ob_start(array(
                &$this,
                'ob_callback'
            ));
        }
    }

    /**
     * Init action
     *
     * @return void
     */
    function init() {
        // Load plugin text domain
        load_plugin_textdomain(W3TC_TEXT_DOMAIN, null, plugin_basename(W3TC_DIR) . '/languages/');
        
        if (is_multisite()) {
            global $w3_current_blog_id, $current_blog;
            if ($w3_current_blog_id != $current_blog->blog_id && !isset($GLOBALS['w3tc_blogmap_register_new_item'])) {
                $url = w3_get_host() . $_SERVER['REQUEST_URI'];
                $pos = strpos($url, '?');
                if ($pos !== false)
                    $url = substr($url, 0, $pos);
                $GLOBALS['w3tc_blogmap_register_new_item'] = $url;
            }
        }

         if (isset($GLOBALS['w3tc_blogmap_register_new_item'])) {
            $do_redirect = false;
            // true value is a sign to just generate config cache
            if ($GLOBALS['w3tc_blogmap_register_new_item'] != 'cache_options') {
                if (w3_is_subdomain_install())
                    $blog_home_url = $GLOBALS['w3tc_blogmap_register_new_item'];
                else {
                    $home_url = rtrim(get_home_url(), '/');
                    if (substr($home_url, 0, 7) == 'http://')
                        $home_url = substr($home_url, 7);
                    else if (substr($home_url, 0, 8) == 'https://')
                        $home_url = substr($home_url, 8);

                    if (substr($GLOBALS['w3tc_blogmap_register_new_item'], 0,
                            strlen($home_url)) == $home_url)
                        $blog_home_url = $home_url;
                    else
                        $blog_home_url = $GLOBALS['w3tc_blogmap_register_new_item'];
                }

                w3_require_once(W3TC_INC_DIR . '/functions/multisite.php');
                $do_redirect = w3_blogmap_register_new_item($blog_home_url,
                    $this->_config);

                // reset cache of blog_id
                global $w3_current_blog_id;
                $w3_current_blog_id = null;

                // change config to actual blog, it was master before
                $this->_config = new W3_Config();
            }

            $do_redirect |= $this->_config->fill_missing_cache_options_and_save();

            // need to repeat request processing, since we was not able to realize
            // blog_id before so we are running with master config now.
            // redirect to the same url causes "redirect loop" error in browser,
            // so need to redirect to something a bit different
            if ($do_redirect) {
                if (strpos($_SERVER['REQUEST_URI'], '?') === false)
                    w3_redirect_temp($_SERVER['REQUEST_URI'] . '?repeat=w3tc');
                else {
                    if (strpos($_SERVER['REQUEST_URI'], 'repeat=w3tc') === false)
                        w3_redirect_temp($_SERVER['REQUEST_URI'] . '&repeat=w3tc');
                }
            }
        }

        /**
         * Check request and handle w3tc_request_data requests
         */
        $pos = strpos($_SERVER['REQUEST_URI'], '/w3tc_request_data/');

        if ($pos !== false) {
            $hash = substr($_SERVER['REQUEST_URI'], $pos + 19, 32);

            if (strlen($hash) == 32) {
                $request_data = (array) get_option('w3tc_request_data');

                if (isset($request_data[$hash])) {
                    echo '<pre>';
                    foreach ($request_data[$hash] as $key => $value) {
                        printf("%s: %s\n", $key, $value);
                    }
                    echo '</pre>';

                    unset($request_data[$hash]);
                    update_option('w3tc_request_data', $request_data);
                } else {
                    echo 'Requested hash expired or invalid';
                }

                exit();
            }
        }

        /**
         * Check for rewrite test request
         */
        w3_require_once(W3TC_LIB_W3_DIR . '/Request.php');

        $rewrite_test = W3_Request::get_boolean('w3tc_rewrite_test');

        if ($rewrite_test) {
            echo 'OK';
            exit();
        }
        $admin_bar = false;
        if (function_exists('is_admin_bar_showing'))
            $admin_bar = is_admin_bar_showing();

        if (current_user_can('manage_options') && $admin_bar) {
            add_action('wp_print_scripts', array($this, 'popup_script'));
        }
    }

    /**
     * Admin bar menu
     *
     * @return void
     */
    function admin_bar_menu() {
        global $wp_admin_bar;

        if (current_user_can('manage_options')) {
            /**
             * @var $modules W3_ModuleStatus
             */
            $modules = w3_instance('W3_ModuleStatus');

            $can_empty_memcache = $modules->can_empty_memcache();

            $can_empty_opcode = $modules->can_empty_opcode();

            $can_empty_file = $modules->can_empty_file();

            $can_empty_varnish = $modules->can_empty_varnish();

            $browsercache_update_media_qs = ($this->_config->get_boolean('browsercache.cssjs.replace') || $this->_config->get_boolean('browsercache.other.replace'));

            //$cdn_enabled = $modules->is_enabled('cdn');
            $cdn_engine = $modules->get_module_engine('cdn');
            $cdn_mirror = w3_is_cdn_mirror($cdn_engine);

            $menu_items = array(
                array(
                    'id' => 'w3tc',
                    'title' => __('Performance', 'w3-total-cache'),
                    'href' => admin_url('admin.php?page=w3tc_dashboard')
                ));

            if ($modules->is_enabled('pgcache') && w3_detect_post_id() && (!defined('DOING_AJAX') || !DOING_AJAX)) {
                $menu_items[] = array(
                    'id' => 'w3tc-pgcache-purge-post',
                    'parent' => 'w3tc',
                    'title' => __('Purge From Cache', 'w3-total-cache'),
                    'href' => wp_nonce_url(admin_url('admin.php?page=w3tc_dashboard&amp;w3tc_flush_pgcache_purge_post&amp;post_id=' . w3_detect_post_id()), 'w3tc')
                );
            }

            if ($can_empty_file && ($can_empty_opcode || $can_empty_memcache)) {
                $menu_items[] = array(
                    'id' => 'w3tc-flush-file',
                    'parent' => 'w3tc-empty-caches',
                    'title' => __('Empty Disc Cache(s)', 'w3-total-cache'),
                    'href' => wp_nonce_url(admin_url('admin.php?page=w3tc_dashboard&amp;w3tc_flush_file'), 'w3tc')
                );
            }

            if ($can_empty_opcode && ($can_empty_file || $can_empty_memcache)) {
                $menu_items[] = array(
                    'id' => 'w3tc-flush-opcode',
                    'parent' => 'w3tc-empty-caches',
                    'title' => __('Empty Opcode Cache', 'w3-total-cache'),
                    'href' => wp_nonce_url(admin_url('admin.php?page=w3tc_dashboard&amp;w3tc_flush_opcode'), 'w3tc')
                );
            }

            if ($can_empty_memcache && ($can_empty_file || $can_empty_opcode)) {
                $menu_items[] = array(
                    'id' => 'w3tc-flush-memcached',
                    'parent' => 'w3tc-empty-caches',
                    'title' => __('Empty Memcached Cache(s)', 'w3-total-cache'),
                    'href' => wp_nonce_url(admin_url('admin.php?page=w3tc_dashboard&amp;w3tc_flush_memcached'), 'w3tc')
                );
            }

            if ($modules->is_enabled('browsercache') && $browsercache_update_media_qs) {
                $menu_items[] = array(
                    'id' => 'w3tc-update-media-qs',
                    'parent' => 'w3tc',
                    'title' => __('Update Media Query String', 'w3-total-cache'),
                    'href' => wp_nonce_url(admin_url('admin.php?page=w3tc_dashboard&amp;w3tc_flush_browser_cache'), 'w3tc')
                );
            }

            if ($modules->plugin_is_enabled()) {
                $menu_items[] = array(
                    'id' => 'w3tc-empty-caches',
                    'parent' => 'w3tc',
                    'title' => __('Empty All Caches', 'w3-total-cache'),
                    'href' => wp_nonce_url(admin_url('admin.php?page=w3tc_dashboard&amp;w3tc_flush_all'), 'w3tc')
                );

                $menu_items[] = array(
                    'id' => 'w3tc-modules',
                    'parent' => 'w3tc',
                    'title' => __('Empty Modules', 'w3-total-cache')
                );
            }

            if ($modules->is_enabled('pgcache')) {
                $menu_items[] = array(
                    'id' => 'w3tc-flush-pgcache',
                    'parent' => 'w3tc-modules',
                    'title' => __('Empty Page Cache', 'w3-total-cache'),
                    'href' => wp_nonce_url(admin_url('admin.php?page=w3tc_dashboard&amp;w3tc_flush_pgcache'), 'w3tc')
                );
            }

            if ($modules->is_enabled('minify')) {
                $menu_items[] = array(
                    'id' => 'w3tc-flush-minify',
                    'parent' => 'w3tc-modules',
                    'title' => __('Empty Minify Cache', 'w3-total-cache'),
                    'href' => wp_nonce_url(admin_url('admin.php?page=w3tc_dashboard&amp;w3tc_flush_minify'), 'w3tc')
                );
            }

            if ($modules->is_enabled('dbcache')) {
                $menu_items[] = array(
                    'id' => 'w3tc-flush-dbcache',
                    'parent' => 'w3tc-modules',
                    'title' => __('Empty Database Cache', 'w3-total-cache'),
                    'href' => wp_nonce_url(admin_url('admin.php?page=w3tc_dashboard&amp;w3tc_flush_dbcache'), 'w3tc')
                );
            }

            if ($modules->is_enabled('objectcache')) {
                $menu_items[] = array(
                    'id' => 'w3tc-flush-objectcache',
                    'parent' => 'w3tc-modules',
                    'title' => __('Empty Object Cache', 'w3-total-cache'),
                    'href' => wp_nonce_url(admin_url('admin.php?page=w3tc_dashboard&amp;w3tc_flush_objectcache'), 'w3tc')
                );
            }

            if (w3_is_pro($this->_config) || w3_is_enterprise($this->_config)) {
                if ($modules->is_enabled('fragmentcache')) {
                    $menu_items[] = array(
                        'id' => 'w3tc-flush-fragmentcache',
                        'parent' => 'w3tc-modules',
                        'title' => __('Empty Fragment Cache', 'w3-total-cache'),
                        'href' => wp_nonce_url(admin_url('admin.php?page=w3tc_dashboard&amp;w3tc_flush_fragmentcache'), 'w3tc')
                    );
                }
            }

            if ($modules->is_enabled('varnish')) {
                $menu_items[] = array(
                    'id' => 'w3tc-flush-varnish',
                    'parent' => 'w3tc-modules',
                    'title' => __('Purge Varnish Cache', 'w3-total-cache'),
                    'href' => wp_nonce_url(admin_url('admin.php?page=w3tc_dashboard&amp;w3tc_flush_varnish'), 'w3tc')
                );
            }

            if ($modules->is_enabled('cdn')) {
                if (w3_can_cdn_purge($cdn_engine)) {
                    $menu_items[] = array(
                        'id' => 'w3tc-cdn-purge',
                        'parent' => 'w3tc',
                        'title' => __('Purge CDN', 'w3-total-cache'),
                        'href' => wp_nonce_url(admin_url('admin.php?page=w3tc_cdn&amp;w3tc_cdn_purge'), 'w3tc'),
                        'meta' => array('onclick' => "w3tc_popupadmin_bar(this.href); return false")
                    );
                }

                if (w3_cdn_can_purge_all($cdn_engine)) {
                    $menu_items[] = array(
                        'id' => 'w3tc-cdn-purge-full',
                        'parent' => 'w3tc',
                        'title' => __('Purge CDN Completely', 'w3-total-cache'),
                        'href' => wp_nonce_url(admin_url('admin.php?page=w3tc_cdn&amp;w3tc_flush_cdn'), 'w3tc')
                    );
                }
                if (!$cdn_mirror) {
                    $menu_items[] = array(
                        'id' => 'w3tc-cdn-queue',
                        'parent' => 'w3tc',
                        'title' => __('Unsuccessful file transfers', 'w3-total-cache'),
                        'href' => wp_nonce_url(admin_url('admin.php?page=w3tc_cdn&amp;w3tc_cdn_queue'), 'w3tc'),
                        'meta' => array('onclick' => "w3tc_popupadmin_bar(this.href); return false")
                    );
                }
            }

            $menu_items = array_merge($menu_items, array(
                array(
                    'id' => 'w3tc-faq',
                    'parent' => 'w3tc',
                    'title' => __('FAQ', 'w3-total-cache'),
                    'href' => admin_url('admin.php?page=w3tc_faq')
                ),
                array(
                    'id' => 'w3tc-support',
                    'parent' => 'w3tc',
                    'title' => __('<span style="color: red; background: none;">Support</span>', 'w3-total-cache'),
                    'href' => admin_url('admin.php?page=w3tc_support')
                )
            ));


            foreach ($menu_items as $menu_item) {
                $wp_admin_bar->add_menu($menu_item);
            }
        }
    }

    /**
     * Template filter
     *
     * @param $template
     * @return string
     */
    function template($template) {
        $w3_mobile = w3_instance('W3_Mobile');

        $mobile_template = $w3_mobile->get_template();

        if ($mobile_template) {
            return $mobile_template;
        } else {
            $w3_referrer = w3_instance('W3_Referrer');

            $referrer_template = $w3_referrer->get_template();

            if ($referrer_template) {
                return $referrer_template;
            }
        }

        return $template;
    }

    /**
     * Stylesheet filter
     *
     * @param $stylesheet
     * @return string
     */
    function stylesheet($stylesheet) {
        $w3_mobile = w3_instance('W3_Mobile');

        $mobile_stylesheet = $w3_mobile->get_stylesheet();

        if ($mobile_stylesheet) {
            return $mobile_stylesheet;
        } else {
            $w3_referrer = w3_instance('W3_Referrer');

            $referrer_stylesheet = $w3_referrer->get_stylesheet();

            if ($referrer_stylesheet) {
                return $referrer_stylesheet;
            }
        }

        return $stylesheet;
    }

    /**
     * Template filter
     *
     * @param $template
     * @return string
     */
    function template_preview($template) {
        w3_require_once(W3TC_LIB_W3_DIR . '/Request.php');
        $theme_name = W3_Request::get_string('w3tc_theme');

        $theme = w3tc_get_theme($theme_name);

        if ($theme) {
            return $theme['Template'];
        }

        return $template;
    }

    /**
     * Stylesheet filter
     *
     * @param $stylesheet
     * @return string
     */
    function stylesheet_preview($stylesheet) {
        w3_require_once(W3TC_LIB_W3_DIR . '/Request.php');
        $theme_name = W3_Request::get_string('w3tc_theme');

        $theme = w3tc_get_theme($theme_name);

        if ($theme) {
            return $theme['Stylesheet'];
        }

        return $stylesheet;
    }

    /**
     * Footer plugin action
     *
     * @return void
     */
    function footer() {
        echo '<div style="text-align: center;"><a href="http://www.w3-edge.com/wordpress-plugins/" rel="external">Optimization WordPress Plugins &amp; Solutions by W3 EDGE</a></div>';
    }

    /**
     * Output buffering callback
     *
     * @param string $buffer
     * @return string
     */
    function ob_callback(&$buffer) {
        global $wpdb;

        if ($buffer != '') {
            if (w3_is_database_error($buffer)) {
                status_header(503);
            } else {
                if (w3_can_print_comment($buffer)) {
                    /**
                     * Add footer comment
                     */
                    $date = date_i18n('Y-m-d H:i:s');
                    $host = (!empty($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : 'localhost');

                    if (w3_is_preview_mode())
                        $buffer .= "\r\n<!-- W3 Total Cache used in preview mode -->";
                    if ($this->_config->get_string('common.support') != '' || $this->_config->get_boolean('common.tweeted')) {
                        $buffer .= sprintf("\r\n<!-- Served from: %s @ %s by W3 Total Cache -->", w3_escape_comment($host), $date);
                    } else {
                        $strings = array();

                        if ($this->_config->get_boolean('minify.enabled') && !$this->_config->get_boolean('minify.debug')) {
                            $w3_plugin_minify = w3_instance('W3_Plugin_Minify');

                            $strings[] = sprintf(__('Minified using %s%s', 'w3-total-cache'), w3_get_engine_name($this->_config->get_string('minify.engine')), ($w3_plugin_minify->minify_reject_reason != '' ? sprintf(' (%s)', $w3_plugin_minify->minify_reject_reason) : ''));
                        }

                        if ($this->_config->get_boolean('pgcache.enabled') && !$this->_config->get_boolean('pgcache.debug')) {
                            $w3_pgcache = w3_instance('W3_PgCache');

                            $strings[] = sprintf(__('Page Caching using %s%s', 'w3-total-cache'), w3_get_engine_name($this->_config->get_string('pgcache.engine')), ($w3_pgcache->cache_reject_reason != '' ? sprintf(' (%s)', $w3_pgcache->cache_reject_reason) : ''));
                        }

                        if ($this->_config->get_boolean('dbcache.enabled') &&
                                !$this->_config->get_boolean('dbcache.debug')) {
                            /**
                             * @var W3_DbCache $db
                             */
                            $db = w3_instance('W3_DbCache');
                            $append = ($reason = $db->get_reject_reason()) ? sprintf(' (%s)', $reason) : '';

                            if ($db->query_hits) {
                                $strings[] = sprintf(__('Database Caching %d/%d queries in %.3f seconds using %s%s', 'w3-total-cache'),
                                    $db->query_hits, $db->query_total, $db->time_total,
                                    w3_get_engine_name($this->_config->get_string('dbcache.engine')),
                                    $append);
                            } else {
                                $strings[] = sprintf(__('Database Caching using %s%s', 'w3-total-cache'),
                                    w3_get_engine_name($this->_config->get_string('dbcache.engine')),
                                    $append);
                            }
                        }

                        if (w3_is_dbcluster()) {
                            $db_cluster = w3_instance('W3_Enterprise_DbCluster');
                            $strings[] = $db_cluster->status_message();
                        }

                        if ($this->_config->get_boolean('objectcache.enabled') && !$this->_config->get_boolean('objectcache.debug')) {
                            /**
                             * @var W3_ObjectCache $w3_objectcache
                             */
                            $w3_objectcache = w3_instance('W3_ObjectCache');
                            $append = ($reason = $w3_objectcache->get_reject_reason())? sprintf(' (%s)', $reason) : '';

                            $strings[] = sprintf(__('Object Caching %d/%d objects using %s%s', 'w3-total-cache'),
                                $w3_objectcache->cache_hits, $w3_objectcache->cache_total,
                                w3_get_engine_name($this->_config->get_string('objectcache.engine')),
                                $append);
                        }

                        if (w3_is_pro($this->_config) || w3_is_enterprise($this->_config)) {
                            if ($this->_config->get_boolean('fragmentcache.enabled') && !$this->_config->get_boolean('fragmentcache.debug')) {
                                $w3_fragmentcache = w3_instance('W3_Pro_FragmentCache');
                                $append = ($w3_fragmentcache->cache_reject_reason != '' ?
                                    sprintf(' (%s)', $w3_fragmentcache->cache_reject_reason) :'');
                                $strings[] = sprintf(__('Fragment Caching %d/%d fragments using %s%s', 'w3-total-cache'),
                                    $w3_fragmentcache->cache_hits, $w3_fragmentcache->cache_total,
                                    w3_get_engine_name($this->_config->get_string('fragmentcache.engine')),
                                    $append);
                            }
                        }

                        if ($this->_config->get_boolean('cdn.enabled') && !$this->_config->get_boolean('cdn.debug')) {
                            $w3_plugin_cdn = w3_instance('W3_Plugin_Cdn');
                            $w3_plugin_cdncommon = w3_instance('W3_Plugin_CdnCommon');
                            $cdn = $w3_plugin_cdncommon->get_cdn();
                            $via = $cdn->get_via();

                            $strings[] = sprintf(__('Content Delivery Network via %s%s', 'w3-total-cache'), ($via ? $via : 'N/A'), ($w3_plugin_cdn->cdn_reject_reason != '' ? sprintf(' (%s)', $w3_plugin_cdn->cdn_reject_reason) : ''));
                        }

                        if ($this->_config->get_boolean('newrelic.enabled')) {
                            $w3_newrelic = w3_instance('W3_Plugin_NewRelic');
                            $append = ($w3_newrelic->newrelic_reject_reason != '') ?
                                                sprintf(' (%s)', $w3_newrelic->newrelic_reject_reason) : '';
                            $strings[] = sprintf(__("Application Monitoring using New Relic%s", 'w3-total-cache'), $append);
                        }
                        $buffer .= "\r\n<!-- Performance optimized by W3 Total Cache. Learn more: http://www.w3-edge.com/wordpress-plugins/\r\n";

                        if (count($strings)) {
                            $buffer .= "\r\n" . implode("\r\n", $strings) . "\r\n";
                        }

                        $buffer .= sprintf("\r\n Served from: %s @ %s by W3 Total Cache -->", w3_escape_comment($host), $date);
                    }

                    if ($this->is_debugging()) {
                        if ($this->_config->get_boolean('dbcache.enabled') && $this->_config->get_boolean('dbcache.debug')) {
                            $db = w3_instance('W3_DbCache');
                            $buffer .= "\r\n\r\n" . $db->_get_debug_info();
                        }

                        if ($this->_config->get_boolean('objectcache.enabled') && $this->_config->get_boolean('objectcache.debug')) {
                            $w3_objectcache = w3_instance('W3_ObjectCache');
                            $buffer .= "\r\n\r\n" . $w3_objectcache->_get_debug_info();
                        }

                        if (w3_is_pro($this->_config) || w3_is_enterprise($this->_config)) {
                            if ($this->_config->get_boolean('fragmentcache.enabled') && 
                                    $this->_config->get_boolean('fragmentcache.debug')) {
                                $w3_fragmentcache = w3_instance('W3_Pro_FragmentCache');
                                $buffer .= "\r\n\r\n" . $w3_fragmentcache->_get_debug_info();
                            }
                        }
                    }
                }
                $buffer = w3tc_do_ob_callbacks(array('minify', 'newrelic', 'cdn', 'browsercache', 'pagecache'), $buffer);
            }
        }

        return $buffer;
    }

    /**
     * Check if we can do modify contents
     *
     * @return boolean
     */
    function can_ob() {
        global $w3_late_init;
        $enabled = w3_is_preview_mode();
        $enabled = $enabled || $this->_config->get_boolean('pgcache.enabled');
        $enabled = $enabled || $this->_config->get_boolean('dbcache.enabled');
        $enabled = $enabled || $this->_config->get_boolean('objectcache.enabled');
        $enabled = $enabled || $this->_config->get_boolean('browsercache.enabled');
        $enabled = $enabled || $this->_config->get_boolean('minify.enabled');
        $enabled = $enabled || $this->_config->get_boolean('cdn.enabled');
        $enabled = $enabled || $this->_config->get_boolean('fragmentcache.enabled');
        $enabled = $enabled || w3_is_dbcluster();
        $enabled = $enabled && !$w3_late_init;

        /**
         * Check if plugin enabled
         */
        if (!$enabled) {
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
     * User login hook
     * Check if current user is not listed in pgcache.reject.* rules
     * If so, set a role cookie so the requests wont be cached
     */
    function check_login_action($logged_in_cookie = false, $expire = ' ', $expiration = 0, $user_id = 0, $action = 'logged_out') {
        global $current_user;
        if (isset($current_user->ID) && !$current_user->ID)
            $user_id = new WP_User($user_id);
        else
            $user_id = $current_user;
        if (is_string($user_id->roles)) {
            $role = $user_id->roles;
        } elseif (!is_array($user_id->roles)) {
            return;
        } else {
            $role = array_shift( $user_id->roles );
        }

        $role_hash = md5(NONCE_KEY . $role);

        if ('logged_out' == $action) {
            setcookie('w3tc_logged_' . $role_hash, $expire, time() - 31536000, COOKIEPATH, COOKIE_DOMAIN);
            return;
        }
        
        if ('logged_in' != $action)
            return;
        
        if (in_array( $role, $this->_config->get_array('pgcache.reject.roles')))
            setcookie('w3tc_logged_' . $role_hash, true, $expire, COOKIEPATH, COOKIE_DOMAIN, is_ssl(), true);
    }

    function popup_script() {
        ?>
        <script type="text/javascript">
            function w3tc_popupadmin_bar(url) {
                return window.open(url, '', 'width=800,height=600,status=no,toolbar=no,menubar=no,scrollbars=yes');
            }
        </script>
            <?php
    }

    private function is_debugging() {
        $debug = $this->_config->get_boolean('pgcache.enabled') && $this->_config->get_boolean('pgcache.debug');
        $debug = $debug || ($this->_config->get_boolean('dbcache.enabled') && $this->_config->get_boolean('dbcache.debug'));
        $debug = $debug || ($this->_config->get_boolean('objectcache.enabled') && $this->_config->get_boolean('objectcache.debug'));
        $debug = $debug || ($this->_config->get_boolean('browsercache.enabled') && $this->_config->get_boolean('browsercache.debug'));
        $debug = $debug || ($this->_config->get_boolean('minify.enabled') && $this->_config->get_boolean('minify.debug'));
        $debug = $debug || ($this->_config->get_boolean('cdn.enabled') && $this->_config->get_boolean('cdn.debug'));
        $debug = $debug || ($this->_config->get_boolean('fragmentcache.enabled') && $this->_config->get_boolean('fragmentcache.debug'));

        return $debug;
    }

    public function pro_dev_mode() {
        echo '<!-- W3 Total Cache is currently running in Pro version Development mode. --><div style="border:2px solid red;text-align:center;font-size:1.2em;color:red"><p><strong>W3 Total Cache is currently running in Pro version Development mode.</strong></p></div>';
    }
}
