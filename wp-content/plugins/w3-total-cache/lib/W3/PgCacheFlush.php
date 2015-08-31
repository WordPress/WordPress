<?php

/**
 * W3 PgCache flushing
 */

w3_require_once(W3TC_LIB_W3_DIR . '/PgCache.php');

/**
 * Class W3_PgCacheFlush
 */
class W3_PgCacheFlush extends W3_PgCache {

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

    /**
     * Number of flushes thats been run on request
     * @var int
     */
    private $_flushes = 0;

    /**
     * PHP5 Constructor
     */
    function __construct() {
        parent::__construct();
    }

    /**
     * Flushes all caches
     *
     * @return boolean
     */
    function flush() {
        $cache = $this->_get_cache();
        return $cache->flush();
    }

    /**
     * Flushes post cache
     *
     * @param integer $post_id
     * @return boolean
     */
    function flush_post($post_id = null) {
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

            if ($this->_config->get_boolean('pgcache.purge.terms') || $this->_config->get_boolean('pgcache.purge.feed.terms')) {
                $taxonomies = get_post_taxonomies($post_id);
                $terms = wp_get_post_terms($post_id, $taxonomies);
            }

            /**
             * @var $purge_urls W3_SharedPageUrls
             */
            $purge_urls = w3_instance('W3_SharedPageUrls');

            $post = get_post($post_id);
            $post_type = in_array($post->post_type, array('post', 'page', 'attachment', 'revision')) ? null : $post->post_type;
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
            if ($this->_config->get_boolean('pgcache.purge.author')) {
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
                $global_urls = array_merge($global_urls, $purge_urls->get_feed_urls($feeds, $post_type));
            }

            if ($this->_config->get_boolean('pgcache.purge.feed.comments')) {
                $full_urls = array_merge($full_urls, $purge_urls->get_feed_comments_urls($post_id, $feeds));
            }

            if ($this->_config->get_boolean('pgcache.purge.feed.author')) {
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

            /**
             * Purge sitemaps if a sitemap option has a regex
             */
            if($this->_config->get_string('pgcache.purge.sitemap_regex')) {
                $cache = $this->_get_cache();
                $cache->flush('sitemaps');
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
                $cache = $this->_get_cache();
                $mobile_groups = $this->_get_mobile_groups();
                $referrer_groups = $this->_get_referrer_groups();
                $encryptions = $this->_get_encryptions();
                $compressions = $this->_get_compressions();
                foreach ($full_urls as $url) {
                    if (!in_array($url, $this->_repeated_urls) && !in_array($url, $this->_flushed_urls)) {
                        $this->_flushed_urls[] = $url;
                        $this->flush_url($url, $cache, $mobile_groups, $referrer_groups, $encryptions, $compressions);
                    } elseif (!in_array($url, $this->_repeated_urls)) {
                        $this->_repeated_urls[] = $url;
                    }
                }
            }

            return true;
        }

        return false;
    }

    /**
     * Flush a single url
     * @param $url
     * @param $cache
     * @param $mobile_groups
     * @param $referrer_groups
     * @param $encryptions
     * @param $compressions
     */
    function _flush_url($url, $cache, $mobile_groups, $referrer_groups, $encryptions, $compressions) {
        foreach ($mobile_groups as $mobile_group) {
            foreach ($referrer_groups as $referrer_group) {
                foreach ($encryptions as $encryption) {
                    foreach ($compressions as $compression) {
                        $page_key = $this->_get_page_key($mobile_group, $referrer_group, $encryption, $compression, false, $url);
                        $cache->delete($page_key);
                    }
                }
            }
        }
    }

    /**
     * Flush a single url
     * @param $url
     */
    function flush_url($url) {
        static $cache, $mobile_groups, $referrer_groups, $encryptions, $compressions;
        if (!isset($cache)) $cache = $this->_get_cache();
        if (!isset($mobile_groups)) $mobile_groups  = $this->_get_mobile_groups();
        if (!isset($referrer_groups)) $referrer_groups = $this->_get_referrer_groups();
        if (!isset($encryptions)) $encryptions = $this->_get_encryptions();
        if (!isset($compressions)) $compressions = $this->_get_compressions();
        $this->_flush_url($url, $cache, $mobile_groups, $referrer_groups, $encryptions, $compressions);
    }

    /**
     * Flushes global and repeated urls
     */
    function flush_post_cleanup() {
        if ($this->_repeated_urls) {
            $cache = $this->_get_cache();
            $mobile_groups = $this->_get_mobile_groups();
            $referrer_groups = $this->_get_referrer_groups();
            $encryptions = $this->_get_encryptions();
            $compressions = $this->_get_compressions();
            foreach($this->_repeated_urls as $url) {
                $this->_flush_url($url, $cache, $mobile_groups, $referrer_groups, $encryptions, $compressions);
            }
        }
    }

    /**
     * Returns array of mobile groups
     *
     * @return array
     */
    function _get_mobile_groups() {
        $mobile_groups = array('');

        if ($this->_mobile) {
            $mobile_groups = array_merge($mobile_groups, array_keys($this->_mobile->get_groups()));
        }

        return $mobile_groups;
    }

    /**
     * Returns array of referrer groups
     *
     * @return array
     */
    function _get_referrer_groups() {
        $referrer_groups = array('');

        if ($this->_referrer) {
            $referrer_groups = array_merge($referrer_groups, array_keys($this->_referrer->get_groups()));
        }

        return $referrer_groups;
    }

    /**
     * Returns array of encryptions
     *
     * @return array
     */
    function _get_encryptions() {
        $encryptions = array(false);
        if ($this->_config->get_boolean('pgcache.cache.ssl')) {
            $encryptions[] = 'ssl';
        }
        return $encryptions;
    }

    function _do_flush_global_urls() {
        global $pagenow;
        if (isset($pagenow) && $pagenow == 'edit.php' || (defined('DOING_SNS') && DOING_SNS))
            return false;
        return true;
    }
}
