<?php
/**
 * CDN cache purge object
 */

/**
 * Class W3_CdnCacheFlush
 */
class W3_CdnCacheFlush {
    /**
     * Advanced cache config
     *
     * @var W3_Config
     */
    var $_config = null;


    /**
     * Array of already flushed urls
     * @var array
     */
    private $_flushed_urls = array();

    /**
     * Array of urls that is known will be flushed more than once
     * @var array
     */
    private $_repeated_urls = array();

    private $_flushes = 0;

    /**
     * PHP5 Constructor
     */
    function __construct() {
        $this->_config = w3_instance('W3_Config');
    }

    /**
     * Purges everything from CDNs that supports it
     */
    function purge() {
        $w3_plugin_cdn = w3_instance('W3_Plugin_CdnCommon');
        $results = array();
        return $w3_plugin_cdn->purge_all($results);
    }

    /**
     * Purges cdn mirror post cache
     *
     * @param integer $post_id
     * @return boolean
     */
    function purge_post($post_id) {
        if (!$post_id) {
            $post_id = w3_detect_post_id();
        }

        if ($post_id) {
            $full_urls = array();
            $global_urls = array();
            $post = null;
            $terms = array();

            $feeds = $this->_config->get_array('pgcache.purge.feed.types');
            $limit_post_pages = $this->_config->get_integer('pgcache.purge.postpages_limit');

            if ($this->_config->get_boolean('pgcache.purge.terms') || $this->_config->get_boolean('varnish.pgcache.feed.terms')) {
                $taxonomies = get_post_taxonomies($post_id);
                $terms = wp_get_post_terms($post_id, $taxonomies);
            }
            /**
             * @var $purge_urls W3_SharedPageUrls
             */
            $purge_urls = w3_instance('W3_SharedPageUrls');

            switch (true) {
                case $this->_config->get_boolean('pgcache.purge.author'):
                case $this->_config->get_boolean('pgcache.purge.archive.daily'):
                case $this->_config->get_boolean('pgcache.purge.archive.monthly'):
                case $this->_config->get_boolean('pgcache.purge.archive.yearly'):
                case $this->_config->get_boolean('pgcache.purge.feed.author'):
                    $post = get_post($post_id);
            }

            $front_page = get_option('show_on_front');

            /**
             * Home (Frontpage) URL
             */
            if (($this->_config->get_boolean('pgcache.purge.home') && $front_page == 'posts')||
                $this->_config->get_boolean('pgcache.purge.front_page')) {
                $global_urls = array_merge($global_urls, $purge_urls->get_frontpage_urls($limit_post_pages));
            }

            /**
             * Home (Post page) URL
             */
            if($this->_config->get_boolean('pgcache.purge.home') && $front_page != 'posts') {
                $global_urls = array_merge($global_urls, $purge_urls->get_postpage_urls($limit_post_pages));
            }

            /**
             * Post URL
             */
            if ($this->_config->get_boolean('pgcache.purge.post')) {
                $full_urls = array_merge($full_urls, $purge_urls->get_post_urls($post_id));
            }

            /**
             * Post comments URLs
             */
            if ($this->_config->get_boolean('pgcache.purge.comments') && function_exists('get_comments_pagenum_link')) {
                $full_urls = array_merge($full_urls, $purge_urls->get_post_comments_urls($post_id));
            }

            /**
             * Post author URLs
             */
            if ($this->_config->get_boolean('pgcache.purge.author') && $post) {
                $full_urls = array_merge($full_urls, $purge_urls->get_post_author_urls($post->post_author, $limit_post_pages));
            }

            /**
             * Post terms URLs
             */
            if ($this->_config->get_boolean('pgcache.purge.terms')) {
                $full_urls = array_merge($full_urls, $purge_urls->get_post_terms_urls($terms, $limit_post_pages));
            }

            /**
             * Daily archive URLs
             */
            if ($this->_config->get_boolean('pgcache.purge.archive.daily') && $post) {
                $full_urls = array_merge($full_urls, $purge_urls->get_daily_archive_urls($post, $limit_post_pages));
            }

            /**
             * Monthly archive URLs
             */
            if ($this->_config->get_boolean('pgcache.purge.archive.monthly') && $post) {
                $full_urls = array_merge($full_urls, $purge_urls->get_monthly_archive_urls($post, $limit_post_pages));
            }

            /**
             * Yearly archive URLs
             */
            if ($this->_config->get_boolean('pgcache.purge.archive.yearly') && $post) {
                $full_urls = array_merge($full_urls, $purge_urls->get_yearly_archive_urls($post, $limit_post_pages));
            }

            /**
             * Feed URLs
             */
            if ($this->_config->get_boolean('pgcache.purge.feed.blog')) {
                $global_urls = array_merge($global_urls, $purge_urls->get_feed_urls($feeds));
            }

            if ($this->_config->get_boolean('pgcache.purge.feed.comments')) {
                $full_urls = array_merge($full_urls, $purge_urls->get_feed_comments_urls($post_id, $feeds));
            }

            if ($this->_config->get_boolean('pgcache.purge.feed.author') && $post) {
                $full_urls = array_merge($full_urls, $purge_urls->get_feed_author_urls($post->post_author, $feeds));
            }

            if ($this->_config->get_boolean('pgcache.purge.feed.terms')) {
                $full_urls = array_merge($full_urls, $purge_urls->get_feed_terms_urls($terms, $feeds));
            }

            /**
             * Purge selected pages
             */
            if ($this->_config->get_array('pgcache.purge.pages')) {
                $pages = $this->_config->get_array('pgcache.purge.pages');
                $global_urls = array_merge($global_urls, $purge_urls->get_pages_urls($pages));
            }

            if ($this->_do_flush_global_urls()) {
                $full_urls = array_merge($global_urls, $full_urls);
            } elseif ($this->_flushes == 0) {
                $this->_repeated_urls = $global_urls;
            }

            /**
             * Flush cache
             */
            if (count($full_urls)) {
                $this->_flushes++;

                $w3_plugin_cdncommon= w3_instance('W3_Plugin_CdnCommon');
                $results = array();
                $files = array();
                foreach ($full_urls as $url) {
                    if (!in_array($url, $this->_repeated_urls) && !in_array($url, $this->_flushed_urls)) {
                        $parsed = parse_url($url);
                        $local_site_path = isset($parsed['path'])? ltrim($parsed['path'], '/') : '';
                        $remote_path = $w3_plugin_cdncommon->uri_to_cdn_uri($local_site_path);
                        $files[] = $w3_plugin_cdncommon->build_file_descriptor($local_site_path, $remote_path);
                        $this->_flushed_urls[] = $url;
                    } elseif (!in_array($url, $this->_repeated_urls)) {
                        $this->_repeated_urls[] = $url;
                    }
                }
                return $w3_plugin_cdncommon->purge($files, false, $results);
            }
        }

        return false;
    }

    /**
     * Purge a single url
     * @param $url
     */
    function purge_url($url) {
        $w3_plugin_cdncommon= w3_instance('W3_Plugin_CdnCommon');
        $results = array();
        $files = array();
        $parsed = parse_url($url);
        $local_site_path = isset($parsed['path'])? ltrim($parsed['path'], '/') : '';
        $remote_path = $w3_plugin_cdncommon->uri_to_cdn_uri($local_site_path);
        $files[] = $w3_plugin_cdncommon->build_file_descriptor($local_site_path, $remote_path);
        $this->_flushed_urls[] = $url;
        $w3_plugin_cdncommon->purge($files, false, $results);
    }

    /**
     * Clears global and repeated urls
     */
    function purge_post_cleanup() {
        if ($this->_repeated_urls) {

            $w3_plugin_cdncommon= w3_instance('W3_Plugin_CdnCommon');
            $results = array();
            $files = array();
            foreach ($this->_repeated_urls as $url) {
                $parsed = parse_url($url);
                $local_site_path = isset($parsed['path'])? ltrim($parsed['path'], '/') : '';
                $remote_path = $w3_plugin_cdncommon->uri_to_cdn_uri($local_site_path);
                $files[] = $w3_plugin_cdncommon->build_file_descriptor($local_site_path, $remote_path);
                $this->_flushed_urls[] = $url;
            }
            $w3_plugin_cdncommon->purge($files, false, $results);
        }
    }

    /**
     * If should separate out global urls
     * @return bool
     */
    function _do_flush_global_urls() {
        global $pagenow;
        if (isset($pagenow) && $pagenow == 'edit.php' || (defined('DOING_SNS') && DOING_SNS))
            return false;
        return true;
    }

}
