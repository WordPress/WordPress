<?php

/**
 * W3 Total Cache CDN Plugin
 */
if (!defined('W3TC')) {
    die();
}

w3_require_once(W3TC_INC_DIR . '/functions/file.php');
w3_require_once(W3TC_LIB_W3_DIR . '/Plugin.php');

/**
 * Class W3_Plugin_Cdn
 */
class W3_Plugin_Cdn extends W3_Plugin {

    /**
     * CDN reject reason
     *
     * @var string
     */
    var $cdn_reject_reason = '';
    var $replaced_urls;

    /**
     * Run plugin
     */
    function run() {
        add_filter('cron_schedules', array(
            &$this,
            'cron_schedules'
        ));

        $cdn_engine = $this->_config->get_string('cdn.engine');

        if (!w3_is_cdn_mirror($cdn_engine)) {
            add_action('delete_attachment', array(
                &$this,
                'delete_attachment'
            ));

            add_filter('update_attached_file', array(
                &$this,
                'update_attached_file'
            ));

            add_filter('wp_update_attachment_metadata', array(
                &$this,
                'update_attachment_metadata'
            ));

            add_action('w3_cdn_cron_queue_process', array(
                &$this,
                'cron_queue_process'
            ));

            add_action('w3_cdn_cron_upload', array(
                &$this,
                'cron_upload'
            ));

            add_action('switch_theme', array(
                &$this,
                'switch_theme'
            ));

            add_filter('update_feedback', array(
                &$this,
                'update_feedback'
            ));
        }

        if (is_admin()) {
            add_action('w3tc_saving_options-w3tc_cdn', array($this, 'change_canonical_header'),0,0);
            add_filter('w3tc_module_is_running-cdn', array($this, 'cdn_is_running'));
        }

        /**
         * Start rewrite engine
         */
        if ($this->can_cdn()) {
            w3tc_add_ob_callback('cdn', array($this,'ob_callback'));
        }

        if (is_admin() && w3_can_cdn_purge($cdn_engine)) {
            add_filter('media_row_actions', array(
                &$this,
                'media_row_actions'
            ), 0, 2);
        }
    }

    /**
     * Instantiates worker with admin functionality on demand
     *
     * @return W3_Plugin_CdnAdmin
     */
    function get_admin() {
        return w3_instance('W3_Plugin_CdnAdmin');
    }

    /**
     * Instantiates worker with common functionality on demand
     *
     * @return W3_Plugin_CdnCommon
     */
    function _get_common() {
        return w3_instance('W3_Plugin_CdnCommon');
    }

    /**
     * Cron queue process event
     */
    function cron_queue_process() {
        $queue_limit = $this->_config->get_integer('cdn.queue.limit');
        return $this->get_admin()->queue_process($queue_limit);
    }

    /**
     * Cron upload event
     */
    function cron_upload() {
        $files = $this->get_files();

        $upload = array();
        $results = array();

        $w3_plugin_cdncommon = w3_instance('W3_Plugin_CdnCommon');

        foreach ($files as $file) {
            $local_path = $w3_plugin_cdncommon->docroot_filename_to_absolute_path($file);
            $remote_path = $w3_plugin_cdncommon->uri_to_cdn_uri($w3_plugin_cdncommon->docroot_filename_to_uri($file));
            $upload[] = $w3_plugin_cdncommon->build_file_descriptor($local_path, $remote_path);
        }

        $this->_get_common()->upload($upload, true, $results);
    }

    /**
     * Update attachment file
     *
     * Upload _wp_attached_file
     *
     * @param string $attached_file
     * @return string
     */
    function update_attached_file($attached_file) {
        $files = $this->_get_common()->get_files_for_upload($attached_file);
        $files = apply_filters('w3tc_cdn_update_attachment', $files);

        $results = array();

        $this->_get_common()->upload($files, true, $results);

        return $attached_file;
    }

    /**
     * On attachment delete action
     *
     * Delete _wp_attached_file, _wp_attachment_metadata, _wp_attachment_backup_sizes
     *
     * @param integer $attachment_id
     */
    function delete_attachment($attachment_id) {
        $files = $this->_get_common()->get_attachment_files($attachment_id);
        $files = apply_filters('w3tc_cdn_delete_attachment', $files);

        $results = array();

        $this->_get_common()->delete($files, true, $results);
    }

    /**
     * Update attachment metadata filter
     *
     * Upload _wp_attachment_metadata
     *
     * @param array $metadata
     * @return array
     */
    function update_attachment_metadata($metadata) {
        $files = $this->_get_common()->get_metadata_files($metadata);
        $files = apply_filters('w3tc_cdn_update_attachment_metadata', $files);

        $results = array();

        $this->_get_common()->upload($files, true, $results);

        return $metadata;
    }

    /**
     * Cron schedules filter
     *
     * @param array $schedules
     * @return array
     */
    function cron_schedules($schedules) {
        $queue_interval = $this->_config->get_integer('cdn.queue.interval');
        $autoupload_interval = $this->_config->get_integer('cdn.autoupload.interval');

        return array_merge($schedules, array(
            'w3_cdn_cron_queue_process' => array(
                'interval' => $queue_interval,
                'display' => sprintf('[W3TC] CDN queue process (every %d seconds)', $queue_interval)
            ),
            'w3_cdn_cron_upload' => array(
                'interval' => $autoupload_interval,
                'display' => sprintf('[W3TC] CDN auto upload (every %d seconds)', $autoupload_interval)
            )
        ));
    }

    /**
     * Switch theme action
     */
    function switch_theme() {
        $this->_config->set('notes.theme_changed', true);
        $this->_config->save();
    }

    /**
     * WP Upgrade action hack
     *
     * @param string $message
     */
    function update_feedback($message) {
        if ($message == __('Upgrading database')) {
            $this->_config->set('notes.wp_upgraded', true);
            $this->_config->save();
        }
    }

    /**
     * OB Callback
     *
     * @param string $buffer
     * @return string
     */
    function ob_callback(&$buffer) {
        if ($buffer != '' && w3_is_xml($buffer)) {
            if ($this->can_cdn2($buffer)) {
                $regexps = array();
                $site_path = w3_get_site_path();
                $domain_url_regexp = w3_get_domain_url_regexp();
                $site_domain_url_regexp = false;
                if ($domain_url_regexp != w3_get_url_regexp(w3_get_domain(w3_get_site_url())))
                    $site_domain_url_regexp = w3_get_url_regexp(w3_get_domain(w3_get_site_url()));

                if ($this->_config->get_boolean('cdn.uploads.enable')) {
                    w3_require_once(W3TC_INC_DIR . '/functions/http.php');

                    $upload_info = w3_upload_info();

                    if ($upload_info) {
                        $baseurl = $upload_info['baseurl'];

                        if (defined('DOMAIN_MAPPING') && DOMAIN_MAPPING) {
                            $parsed = @parse_url($upload_info['baseurl']);
                            $baseurl = home_url() . $parsed['path'];
                        }

                        $regexps = $this->make_uploads_regexes($domain_url_regexp, $baseurl, $upload_info, $regexps);
                        if ($site_domain_url_regexp)
                            $regexps = $this->make_uploads_regexes($site_domain_url_regexp, $baseurl, $upload_info, $regexps);
                    }
                }

                if ($this->_config->get_boolean('cdn.includes.enable')) {
                    $mask = $this->_config->get_string('cdn.includes.files');
                    if ($mask != '') {
                        $regexps[] = '~(["\'(])\s*((' . $domain_url_regexp . ')?(' . w3_preg_quote($site_path . WPINC) . '/(' . $this->get_regexp_by_mask($mask) . ')))~';
                        if ($site_domain_url_regexp)
                            $regexps[] = '~(["\'(])\s*((' . $site_domain_url_regexp . ')?(' . w3_preg_quote($site_path . WPINC) . '/(' . $this->get_regexp_by_mask($mask) . ')))~';
                    }
                }

                if ($this->_config->get_boolean('cdn.theme.enable')) {
                    $theme_dir = preg_replace('~' . $domain_url_regexp . '~i', '', get_theme_root_uri());

                    $mask = $this->_config->get_string('cdn.theme.files');

                    if ($mask != '') {
                        $regexps[] = '~(["\'(])\s*((' . $domain_url_regexp . ')?(' . w3_preg_quote($theme_dir) . '/(' . $this->get_regexp_by_mask($mask) . ')))~';
                        if ($site_domain_url_regexp) {
                            $theme_dir2 = preg_replace('~' . $site_domain_url_regexp. '~i', '', get_theme_root_uri());
                            $regexps[] = '~(["\'(])\s*((' . $site_domain_url_regexp . ')?(' . w3_preg_quote($theme_dir) . '/(' . $this->get_regexp_by_mask($mask) . ')))~';
                            $regexps[] = '~(["\'(])\s*((' . $site_domain_url_regexp . ')?(' . w3_preg_quote($theme_dir2) . '/(' . $this->get_regexp_by_mask($mask) . ')))~';
                        }
                    }
                }

                if ($this->_config->get_boolean('cdn.custom.enable')) {
                    $masks = $this->_config->get_array('cdn.custom.files');
                    $masks = array_map(array($this, '_replace_folder_placeholders'), $masks);
                    $masks = array_map('w3_parse_path', $masks);

                    if (count($masks)) {
                        $mask_regexps = array();

                        foreach ($masks as $mask) {
                            if ($mask != '') {
                                $mask = w3_normalize_file($mask);
                                $mask_regexps[] = $this->get_regexp_by_mask($mask);
                            }
                        }

                        $regexps[] = '~(["\'(])\s*((' . $domain_url_regexp . ')?(' . w3_preg_quote($site_path) . '(' . implode('|', $mask_regexps) . ')))~i';
                        if ($site_domain_url_regexp)
                            $regexps[] = '~(["\'(])\s*((' . $site_domain_url_regexp . ')?(' . w3_preg_quote($site_path) . '(' . implode('|', $mask_regexps) . ')))~i';
                    }
                }

                foreach ($regexps as $regexp) {
                    $buffer = preg_replace_callback($regexp, array(
                        &$this,
                        'link_replace_callback'
                    ), $buffer);
                }

                if ($this->_config->get_boolean('cdn.minify.enable')) {
                    if ($this->_config->get_boolean('minify.auto')) {
                        $regexp = '~(["\'(])\s*' .
                            $this->_minify_url_regexp('/[a-zA-Z0-9-_]+\.(css|js)') .
                            '~U';
                        if (w3_is_cdn_mirror($this->_config->get_string('cdn.engine')))
                            $processor = 'link_replace_callback';
                        else
                            $processor = 'minify_auto_pushcdn_link_replace_callback';
                    } else {
                        $regexp = '~(["\'(])\s*' .
                            $this->_minify_url_regexp('/[a-z0-9]+/.+\.include(-(footer|body))?(-nb)?\.[a-f0-9]+\.(css|js)') .
                            '~U';
                        $processor = 'link_replace_callback';
                    }

                    $buffer = preg_replace_callback($regexp, array(
                        &$this,
                        $processor),
                        $buffer);
                }
            }

            if ($this->_config->get_boolean('cdn.debug')) {
                $buffer .= "\r\n\r\n" . $this->get_debug_info();
            }
        }

        return $buffer;
    }

    /**
     * Gets regexp for minified files
     *
     * @return string
     */
    function _minify_url_regexp($filename_mask) {
        $minify_base_url = w3_filename_to_url(w3_cache_blog_dir('minify'));
        $matches = null;
        if (!preg_match('~((https?://)?([^/]+))(.+)~i', $minify_base_url, $matches))
            return '';

        $protocol_domain_regexp = w3_get_url_regexp($matches[1]);
        $path_regexp = w3_preg_quote($matches[4]);

        $regexp =
            '(' .
                '(' . $protocol_domain_regexp . ')?' .
                '(' . $path_regexp . $filename_mask . ')' .
            ')';
        return $regexp;
    }
    
    /**
     * Returns array of files to upload
     *
     * @return array
     */
    function get_files() {
        $files = array();

        if ($this->_config->get_boolean('cdn.includes.enable')) {
            $files = array_merge($files, $this->get_files_includes());
        }

        if ($this->_config->get_boolean('cdn.theme.enable')) {
            $files = array_merge($files, $this->get_files_theme());
        }

        if ($this->_config->get_boolean('cdn.minify.enable')) {
            $files = array_merge($files, $this->get_files_minify());
        }

        if ($this->_config->get_boolean('cdn.custom.enable')) {
            $files = array_merge($files, $this->get_files_custom());
        }

        return $files;
    }

    /**
     * Exports includes to CDN
     *
     * @return array
     */
    function get_files_includes() {
        $includes_root = w3_path(ABSPATH . WPINC);
        $doc_root = w3_get_document_root();
        $includes_path = ltrim(str_replace($doc_root, '', $includes_root), '/');

        $files = $this->search_files($includes_root, $includes_path, $this->_config->get_string('cdn.includes.files'));

        return $files;
    }

    /**
     * Exports theme to CDN
     *
     * @return array
     */
    function get_files_theme() {
        /**
         * If mobile or referrer support enabled
         * we should upload whole themes directory
         */
        if ($this->_config->get_boolean('mobile.enabled') || $this->_config->get_boolean('referrer.enabled')) {
            $themes_root = get_theme_root();
        } else {
            $themes_root = get_stylesheet_directory();
        }

        $themes_root = w3_path($themes_root);
        $site_root = w3_get_document_root();
        $themes_path = ltrim(str_replace($site_root, '', $themes_root), '/');
        $files = $this->search_files($themes_root, $themes_path, $this->_config->get_string('cdn.theme.files'));

        return $files;
    }

    /**
     * Exports min files to CDN
     *
     * @return array
     */
    function get_files_minify() {
        $files = array();

        if ($this->_config->get_boolean('minify.rewrite') && (!$this->_config->get_boolean('minify.auto') || w3_is_cdn_mirror($this->_config->get_string('cdn.engine')))) {
            w3_require_once(W3TC_INC_DIR . '/functions/http.php');

            $minify = w3_instance('W3_Plugin_Minify');

            $document_root = w3_get_document_root();
            $minify_root = w3_cache_blog_dir('minify');
            $minify_path = ltrim(str_replace($document_root, '', $minify_root), '/');
            $urls = $minify->get_urls();

            if ($this->_config->get_string('minify.engine') == 'file') {
                foreach ($urls as $url) {
                    w3_http_get($url);
                }

                $files = $this->search_files($minify_root, $minify_path, '*.css;*.js');
            } else {
                foreach ($urls as $url) {
                    $file = w3_normalize_file_minify($url);
                    $file = w3_translate_file($file);

                    if (!w3_is_url($file)) {
                        $file = $document_root . '/' . $file;
                        $file = ltrim(str_replace($minify_root, '', $file), '/');

                        $dir = dirname($file);

                        if ($dir) {
                            w3_mkdir($dir, 0777, $minify_root);
                        }

                        if (w3_download($url, $minify_root . '/' . $file) !== false) {
                            $files[] = $minify_path . '/' . $file;
                        }
                    }
                }
            }
        }

        return $files;
    }

    /**
     * Exports custom files to CDN
     *
     * @return array
     */
    function get_files_custom() {
        $files = array();
        $document_root = w3_get_document_root();
        $custom_files = $this->_config->get_array('cdn.custom.files');
        $custom_files = array_map('w3_parse_path', $custom_files);
        $site_root = w3_get_site_root();
        $path = w3_get_site_path();
        $site_root_dir = str_replace($document_root, '', $site_root);
        if (strstr(WP_CONTENT_DIR, w3_get_site_root()) === false) {
            $site_root = w3_get_document_root();
            $path = '';
        }

        $content_path = trim(str_replace(WP_CONTENT_DIR, '', $site_root),'/\\');

        foreach ($custom_files as $custom_file) {
            if ($custom_file != '') {
                $custom_file = $this->_replace_folder_placeholders($custom_file);
                $custom_file = w3_normalize_file($custom_file);

                if (!w3_is_multisite()) {
                    $dir = trim(dirname($custom_file), '/\\');
                    $rel_path = trim(dirname($custom_file), '/\\');
                } else
                    $rel_path = $dir = trim(dirname($custom_file), '/\\');

                if (strpos($dir, '<currentblog>') != false) {
                   $rel_path = $dir = str_replace('<currentblog>', 'blogs.dir/' . w3_get_blog_id(), $dir);
                }

                if ($dir == '.') {
                    $rel_path = $dir = '';
                }
                $mask = basename($custom_file);
                $files = array_merge($files, $this->search_files($document_root . '/' . $dir, $rel_path, $mask));
            }
        }

        return $files;
    }

    /**
     * Link replace callback
     *
     * @param array $matches
     * @return string
     */
    function link_replace_callback($matches) {
        list($match, $quote, $url, , , , $path) = $matches;
        $path = ltrim($path, '/');
        $r = $this->_link_replace_callback_checks($match, $quote, $url, $path);
        if (is_null($r)) {
            $r = $this->_link_replace_callback_ask_cdn($match, $quote, $url, $path);
        }

        return $r;
    }

    /**
     * Link replace callback for urls from minify module using auto mode and in cdn of push type
     *
     * @param array $matches
     * @return string
     */
    function minify_auto_pushcdn_link_replace_callback($matches) {
        static $dispatcher = null;

        list($match, $quote, $url, , , , $path) = $matches;
        $path = ltrim($path, '/');
        $r = $this->_link_replace_callback_checks($match, $quote, $url, $path);

        /**
         * Check if we can replace that URL (for auto mode it should be uploaded)
         */
        if (is_null($dispatcher)) {
            $dispatcher = w3_instance('W3_Dispatcher');
        }

        if (!$dispatcher->is_url_cdn_uploaded($url)) {
            /*
             * file not yet uploaded (rare case) - push to queue
             */
            $this->_get_common()->queue_upload_url($url);

            return $match;
        }

        if (is_null($r)) {
            $r = $this->_link_replace_callback_ask_cdn($match, $quote, $url, $path);
        }

        return $r;
    }

    /**
     * Link replace callback, basic checks step
     *
     * @param $match
     * @param $quote
     * @param $url
     * @param $path
     * @return null|string
     */
    function _link_replace_callback_checks($match, $quote, $url, $path) {
        /**
         * @var wpdb $wpdb
         */
        global $wpdb;
        static $queue = null, $reject_files = null;

        /**
         * Check if URL was already replaced
         */
        if (isset($this->replaced_urls[$url])) {
            return $quote . $this->replaced_urls[$url];
        }

        /**
         * Check URL for rejected files
         */
        if ($reject_files === null) {
            $reject_files = $this->_config->get_array('cdn.reject.files');
        }

        foreach ($reject_files as $reject_file) {
            if ($reject_file != '') {
                $reject_file = $this->_replace_folder_placeholders($reject_file);

                $reject_file = w3_normalize_file($reject_file);

                $reject_file_regexp = '~^(' . $this->get_regexp_by_mask($reject_file) . ')~i';

                if (preg_match($reject_file_regexp, $path)) {
                    return $match;
                }
            }
        }

        /**
         * Don't replace URL for files that are in the CDN queue
         */
        if ($queue === null) {
            if (!w3_is_cdn_mirror($this->_config->get_string('cdn.engine'))) {
                $sql = $wpdb->prepare('SELECT remote_path FROM ' . $wpdb->prefix . W3TC_CDN_TABLE_QUEUE . ' WHERE remote_path = %s', $path);
                $queue = $wpdb->get_var($sql);
            } else {
                $queue = false;
            }
        }

        if ($queue) {
            return $match;
        }

        return null;
    }

    /**
     * Link replace callback, url replacement using cdn engine
     *
     * @param $match
     * @param $quote
     * @param $url
     * @param $path
     * @return null|string
     */
    function _link_replace_callback_ask_cdn($match, $quote, $url, $path) {
        /**
         * Do replacement
         */
        $cdn = $this->_get_common()->get_cdn();

        $remote_path = $this->_get_common()->uri_to_cdn_uri($path);

        $new_url = $cdn->format_url($remote_path);

        if ($new_url) {
            $new_url = apply_filters('w3tc_cdn_url', $new_url, $url);
            $this->replaced_urls[$url] = $new_url;

            return $quote . $new_url;
        }

        return $match;
    }

    /**
     * Search files
     *
     * @param string $search_dir
     * @param string $base_dir
     * @param string $mask
     * @param boolean $recursive
     * @return array
     */
    function search_files($search_dir, $base_dir, $mask = '*.*', $recursive = true) {
        static $stack = array();
        $files = array();
        $ignore = array(
            '.svn',
            '.git',
            '.DS_Store',
            'CVS',
            'Thumbs.db',
            'desktop.ini'
        );

        $dir = @opendir($search_dir);

        if ($dir) {
            while (($entry = @readdir($dir)) !== false) {
                if ($entry != '.' && $entry != '..' && !in_array($entry, $ignore)) {
                    $path = $search_dir . '/' . $entry;

                    if (@is_dir($path) && $recursive) {
                        array_push($stack, $entry);
                        $files = array_merge($files, $this->search_files($path, $base_dir, $mask, $recursive));
                        array_pop($stack);
                    } else {
                        $regexp = '~^(' . $this->get_regexp_by_mask($mask) . ')$~i';

                        if (preg_match($regexp, $entry)) {
                            $files[] = ($base_dir != '' ? $base_dir . '/' : '') . (($p = implode('/', $stack)) != '' ? $p . '/' : '') . $entry;
                        }
                    }
                }
            }

            @closedir($dir);
        }

        return $files;
    }

    /**
     * Returns regexp by mask
     *
     * @param string $mask
     * @return string
     */
    function get_regexp_by_mask($mask) {
        $mask = trim($mask);
        $mask = w3_preg_quote($mask);

        $mask = str_replace(array(
            '\*',
            '\?',
            ';'
        ), array(
            '@ASTERISK@',
            '@QUESTION@',
            '|'
        ), $mask);

        $regexp = str_replace(array(
            '@ASTERISK@',
            '@QUESTION@'
        ), array(
            '[^\\?\\*:\\|\'"<>]*',
            '[^\\?\\*:\\|\'"<>]'
        ), $mask);

        return $regexp;
    }

    /**
     * Returns debug info
     *
     * @return string
     */
    function get_debug_info() {
        $debug_info = "<!-- W3 Total Cache: CDN debug info:\r\n";
        $debug_info .= sprintf("%s%s\r\n", str_pad('Engine: ', 20), $this->_config->get_string('cdn.engine'));

        if ($this->cdn_reject_reason) {
            $debug_info .= sprintf("%s%s\r\n", str_pad('Reject reason: ', 20), $this->cdn_reject_reason);
        }

        if (count($this->replaced_urls)) {
            $debug_info .= "\r\nReplaced URLs:\r\n";

            foreach ($this->replaced_urls as $old_url => $new_url) {
                $debug_info .= sprintf("%s => %s\r\n", w3_escape_comment($old_url), w3_escape_comment($new_url));
            }
        }

        $debug_info .= '-->';

        return $debug_info;
    }

    /**
     * Check if we can do CDN logic
     * @return boolean
     */
    function can_cdn() {
        /**
         * Skip if admin
         */
        if (defined('WP_ADMIN')) {
            $this->cdn_reject_reason = 'wp-admin';

            return false;
        }

        /**
         * Check for WPMU's and WP's 3.0 short init
         */
        if (defined('SHORTINIT') && SHORTINIT) {
            $this->cdn_reject_reason = 'Short init';

            return false;
        }

        /**
         * Check User agent
         */
        if (!$this->check_ua()) {
            $this->cdn_reject_reason = 'user agent is rejected';

            return false;
        }

        /**
         * Check request URI
         */
        if (!$this->_check_request_uri()) {
            $this->cdn_reject_reason = 'request URI is rejected';

            return false;
        }

        /**
         * Do not replace urls if SSL and SSL support is do not replace
         */
        if (w3_is_https() && $this->_config->get_boolean('cdn.reject.ssl')) {
            $this->cdn_reject_reason = 'SSL is rejected';

            return false;
        }

        return true;
    }

    /**
     * Returns true if we can do CDN logic
     *
     * @param $buffer
     * @return string
     */
    function can_cdn2(&$buffer) {
        /**
         * Check for database error
         */
        if (w3_is_database_error($buffer)) {
            $this->cdn_reject_reason = 'Database Error occurred';

            return false;
        }

        /**
         * Check for DONOTCDN constant
         */
        if (defined('DONOTCDN') && DONOTCDN) {
            $this->cdn_reject_reason = 'DONOTCDN constant is defined';

            return false;
        }

        /**
         * Check logged users roles
         */
        if ($this->_config->get_boolean('cdn.reject.logged_roles') && !$this->_check_logged_in_role_allowed()) {
            $this->cdn_reject_reason = 'logged in role is rejected';

            return false;
        }

        return true;
    }

    /**
     * Checks User Agent
     *
     * @return boolean
     */
    function check_ua() {
        $uas = array_merge($this->_config->get_array('cdn.reject.ua'), array(
            W3TC_POWERED_BY
        ));

        foreach ($uas as $ua) {
            if (!empty($ua)) {
                if (isset($_SERVER['HTTP_USER_AGENT']) && stristr($_SERVER['HTTP_USER_AGENT'], $ua) !== false)
                    return false;
            }
        }

        return true;
    }

    /**
     * Checks request URI
     *
     * @return boolean
     */
    function _check_request_uri() {
        $reject_uri = $this->_config->get_array('cdn.reject.uri');
        $reject_uri = array_map('w3_parse_path', $reject_uri);

        foreach ($reject_uri as $expr) {
            $expr = trim($expr);
            if ($expr != '' && preg_match('~' . $expr . '~i', $_SERVER['REQUEST_URI'])) {
                return false;
            }
        }

        w3_require_once(W3TC_LIB_W3_DIR . '/Request.php');
        if (W3_Request::get_string('wp_customize'))
            return false;

        return true;
    }
    /**
     * Check if logged in user role is allwed to use CDN
     *
     * @return boolean
     */
    private function _check_logged_in_role_allowed() {
        global $current_user;

        if (!is_user_logged_in())
            return true;

        $roles = $this->_config->get_array('cdn.reject.roles');

        if (empty($roles))
            return true;

        $role = array_shift( $current_user->roles );

        if (in_array($role, $roles)) {
            return false;
        }

        return true;
    }

    private function _replace_folder_placeholders($file) {
        static $content_dir, $plugin_dir, $upload_dir;
        if (empty($content_dir)) {
            $content_dir = str_replace(w3_get_document_root(), '', WP_CONTENT_DIR);
            $content_dir = substr($content_dir, strlen(w3_get_site_path()));
            $content_dir = trim($content_dir, '/');
            if (defined('WP_PLUGIN_DIR')) {
                $plugin_dir = str_replace(w3_get_document_root(), '', WP_PLUGIN_DIR);
                $plugin_dir = trim($plugin_dir, '/');
            } else {
                $plugin_dir = str_replace(w3_get_document_root(), '', WP_CONTENT_DIR . '/plugins');
                $plugin_dir = trim($plugin_dir, '/');
            }
            $upload_dir = wp_upload_dir();
            $upload_dir = str_replace(w3_get_document_root(), '', $upload_dir['basedir']);
            $upload_dir = trim($upload_dir, '/');
        }
        $file = str_replace('{wp_content_dir}', $content_dir, $file);
        $file = str_replace('{plugins_dir}', $plugin_dir, $file);
        $file = str_replace('{uploads_dir}', $upload_dir, $file);

        return $file;
    }



    /**
     * media_row_actions filter
     *
     * @param array $actions
     * @param object $post
     * @return array
     */
    function media_row_actions($actions, $post) {
        return $this->get_admin()->media_row_actions($actions, $post);
    }


    /**
     * @param $current_state
     * @return bool
     */
    function cdn_is_running($current_state) {
        $admin = $this->get_admin();
        return $admin->is_running();
    }

    /**
     * Change canonical header
     */
    function change_canonical_header() {
        $admin = $this->get_admin();
        $admin->change_canonical_header();
    }

    /**
     * @param $domain_url_regexp
     * @param $baseurl
     * @param $upload_info
     * @param $regexps
     * @return array
     */
    private function make_uploads_regexes($domain_url_regexp, $baseurl, $upload_info, $regexps) {
        if (preg_match('~' . $domain_url_regexp . '~i', $baseurl)) {
            $regexps[] = '~(["\'(])\s*((' . $domain_url_regexp . ')?(' . w3_preg_quote($upload_info['baseurlpath']) . '([^"\')>]+)))~';
        } else {
            $parsed = @parse_url($baseurl);
            $upload_url_domain_regexp = isset($parsed['host']) ? w3_get_url_regexp($parsed['scheme'] . '://' . $parsed['host']) : $domain_url_regexp;
            $baseurlpath = isset($parsed['path']) ? rtrim($parsed['path'], '/') : '';
            if ($baseurlpath)
                $regexps[] = '~(["\'])\s*((' . $upload_url_domain_regexp . ')?(' . w3_preg_quote($baseurlpath) . '([^"\'>]+)))~';
            else
                $regexps[] = '~(["\'])\s*((' . $upload_url_domain_regexp . ')(([^"\'>]+)))~';
        }
        return $regexps;
    }
}

