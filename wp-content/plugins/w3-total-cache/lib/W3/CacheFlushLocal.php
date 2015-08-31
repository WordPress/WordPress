<?php

/**
 * W3 Cache flushing
 */

/**
 * Class W3_PgCacheFlushLocal
 */
class W3_CacheFlushLocal {
    /**
     * Cleans db cache
     */
    function dbcache_flush() {
        do_action('w3tc_dbcache_flush');
        $dbcache = w3_instance('W3_DbCache');
        $dbcache->flush_cache();
    }

    /**
     * Cleans object cache
     */
    function objectcache_flush() {
        do_action('w3tc_objectcache_flush');
        $objectcache = w3_instance('W3_ObjectCache');
        $objectcache->flush();
    }

    /**
     * Cleans fragment cache
     */
    function fragmentcache_flush() {
        do_action('w3tc_fragmentcache_flush');
        $objectcache = w3_instance('W3_Pro_FragmentCache');
        $objectcache->flush();
    }

    /**
     * Cleans fragment cache
     */
    function fragmentcache_flush_group($group, $global = false) {
        do_action('w3tc_fragmentcache_flush_group', $group, $global);
        $objectcache = w3_instance('W3_Pro_FragmentCache');
        $objectcache->flush_group($group, $global);
    }

     /**
     * Cleans object cache
     */
    function minifycache_flush() {
        do_action('w3tc_minifycache_flush');
        $minifycache = w3_instance('W3_Minify');
        $minifycache->flush();
    }
    
    /**
     * Updates Query String
     */
    function browsercache_flush() {
        do_action('w3tc_browsercache_flush');
        $config = w3_instance('W3_Config');
        if ($config->get_boolean('browsercache.enabled')) {
          $config->set('browsercache.timestamp', time());
          $config->save();
        }
    }

    /**
     * Purges Files from Varnish (If enabled) and CDN
     *
     * @param array $purgefiles array consisting of CdnCommon file descriptors
     *                          array(array('local_path'=>'', 'remote_path'=> ''))
     * @return boolean
     */
    function cdn_purge_files($purgefiles) {
        do_action('w3tc_cdn_purge_files', $purgefiles);
        $w3_plugin_cdncommon = w3_instance('W3_Plugin_CdnCommon');
        $results = array();

        return $w3_plugin_cdncommon->purge($purgefiles, false, $results);
    }

    /**
     * Flushes all caches
     *
     * @return boolean
     */
    function pgcache_flush() {
        do_action('w3tc_pgcache_flush');
        $pgcacheflush = w3_instance('W3_PgCacheFlush');
        return $pgcacheflush->flush();
    }

    /**
     * Flushes post cache
     *
     * @param integer $post_id
     * @return boolean
     */
    function pgcache_flush_post($post_id) {
        do_action('w3tc_pgcache_flush_post', $post_id);
        $pgcacheflush = w3_instance('W3_PgCacheFlush');
        return $pgcacheflush->flush_post($post_id);
    }

    /**
     * Flushes post cache
     *
     * @param string $url
     * @return boolean
     */
    function pgcache_flush_url($url) {
        do_action('w3tc_pgcache_flush_url', $url);
        $pgcacheflush = w3_instance('W3_PgCacheFlush');
        return $pgcacheflush->flush_url($url);
    }

    /**
     * Purges varnish cache
     * @return mixed
     */
    function varnish_flush() {
        do_action('w3tc_varnish_flush');
        $varnishflush = w3_instance('W3_VarnishFlush');
        return $varnishflush->flush();
    }

    /**
     * Purges post from varnish
     * @param integer $post_id
     * @return mixed
     */
    function varnish_flush_post($post_id) {
        do_action('w3tc_varnish_flush_post', $post_id);
        $varnishflush = w3_instance('W3_VarnishFlush');
        return $varnishflush->flush_post($post_id);
    }

    /**
     * Purges post from varnish
     * @param string $url
     * @return mixed
     */
    function varnish_flush_url($url) {
        do_action('w3tc_varnish_flush_url', $url);
        $varnishflush = w3_instance('W3_VarnishFlush');
        return $varnishflush->flush_url($url);
    }

    /**
     * Purge CDN mirror cache
     */
    function cdncache_purge() {
        do_action('w3tc_cdncache_purge');
        $cdncacheflush = w3_instance('W3_CdnCacheFlush');
        return $cdncacheflush->purge();
    }

    /**
     * Purge CDN mirror cache
     * @param $post_id
     */
    function cdncache_purge_post($post_id) {
        do_action('w3tc_cdncache_purge_post', $post_id);
        $cdncacheflush = w3_instance('W3_CdnCacheFlush');
        return $cdncacheflush->purge_post($post_id);
    }

    /**
     * Purge CDN mirror cache
     * @param string $url
     */
    function cdncache_purge_url($url) {
        do_action('w3tc_cdncache_purge_url', $url);
        $cdncacheflush = w3_instance('W3_CdnCacheFlush');
        return $cdncacheflush->purge_url($url);
    }

    /**
     * Flushes the system APC
     * @return bool
     */
    function apc_system_flush() {
        if (function_exists('apc_clear_cache') && ini_get('apc.stat') == '0') {
            $result = apc_clear_cache();
            $result |= apc_clear_cache('opcode');
            return $result;
        }
        return false;
    }

    /**
     * Reload/compile a PHP file
     * @param $filename
     * @return bool
     */
    function apc_reload_file($filename) {
        if (function_exists('apc_compile_file')) {
            if (!file_exists($filename)) {
                if (file_exists(ABSPATH . $filename))
                    $filename = ABSPATH . DIRECTORY_SEPARATOR . $filename;
                elseif (file_exists(WP_CONTENT_DIR . DIRECTORY_SEPARATOR . $filename))
                    $filename = WP_CONTENT_DIR . DIRECTORY_SEPARATOR . $filename;
                elseif (file_exists(WPINC . DIRECTORY_SEPARATOR . $filename))
                    $filename = WPINC . DIRECTORY_SEPARATOR . $filename;
                elseif (file_exists(WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . $filename))
                    $filename = WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . $filename;
                else
                    return false;
            }
            return apc_compile_file($filename);
        }
        return false;
    }

    /**
     * Reload/compile a PHP file
     * @param $filenames
     */
    function apc_reload_files($filenames) {
        if (function_exists('apc_compile_file')) {
            foreach ($filenames as $filename) {
                $this->apc_reload_file($filename);
            }
        }
    }

    /**
     * Deletes files based on regular expression matching.
     * @param string $mask
     * @return boolean
     */
    function apc_delete_files_based_on_regex($mask) {
        $apc_info = @apc_cache_info();
        $cached_files = isset($apc_info['cache_list']) ? $apc_info['cache_list'] : array();
        $delete_files = array();
        foreach ($cached_files as $cached_file) {
            $file = $cached_file['filename'];
            if (preg_match('/' . $mask . '/', $file))
                $delete_files[] = $file;
        }
        //returns empty array on success
        $result = apc_delete_file($delete_files);
        return empty($result);
    }

    /**
     * Purges/Flushes post from page cache, varnish and cdn cache
     */
    function flush_post($post_id) {
        do_action('w3tc_flush_post', $post_id);

        $config = w3_instance('W3_Config');
        if ($config->get_boolean('pgcache.enabled'))
            $this->pgcache_flush_post($post_id);
        if ($config->get_boolean('varnish.enabled'))
            $this->varnish_flush_post($post_id);
        if ($config->get_boolean('cdn.enabled') && $config->get_boolean('cdncache.enabled'))
            $this->cdncache_purge_post($post_id);
    }

    /**
     * Purges/Flushes page cache, varnish and cdn cache
     */
    function flush() {
        do_action('w3tc_flush');

        $config = w3_instance('W3_Config');
        if ($config->get_boolean('pgcache.enabled'))
            $this->pgcache_flush();
        if ($config->get_boolean('varnish.enabled'))
            $this->varnish_flush();
        if ($config->get_boolean('cdn.enabled') && $config->get_boolean('cdncache.enabled'))
            $this->cdncache_purge();
    }

    /**
     * Flushes all enabled caches.
     */
    function flush_all() {
        do_action('w3tc_flush_all');

        $config = w3_instance('W3_Config');
        if ($config->get_boolean('minify.enabled'))
            $this->minifycache_flush();
        if ($config->get_boolean('objectcache.enabled'))
            $this->objectcache_flush();
        if ($config->get_boolean('dbcache.enabled'))
            $this->dbcache_flush();
        if ($config->get_boolean('fragmentcache.enabled'))
            $this->fragmentcache_flush();
        if ($config->get_boolean('pgcache.enabled'))
            $this->pgcache_flush();
        if ($config->get_boolean('varnish.enabled'))
            $this->varnish_flush();
        if ($config->get_boolean('cdn.enabled') && $config->get_boolean('cdncache.enabled'))
            $this->cdncache_purge();
    }

    /**
     * Purges/Flushes url from page cache, varnish and cdn cache
     */
    function flush_url($url) {
        do_action('w3tc_flush_url', $url);

        $config = w3_instance('W3_Config');
        if ($config->get_boolean('pgcache.enabled'))
            $this->pgcache_flush_url($url);
        if ($config->get_boolean('varnish.enabled'))
            $this->varnish_flush_url($url);
        if ($config->get_boolean('cdn.enabled') && $config->get_boolean('cdncache.enabled'))
            $this->cdncache_purge_url($url);
    }

    /**
     * Makes get request to url specific to post, ie permalinks
     * @param $post_id
     * @return mixed
     */
    function prime_post($post_id) {
        /** @var $pgcache W3_Plugin_PgCacheAdmin */
        $pgcache = w3_instance('W3_Plugin_PgCacheAdmin');
        return $pgcache->prime_post($post_id);
    }
}
