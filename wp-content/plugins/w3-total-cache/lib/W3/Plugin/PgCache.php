<?php

/**
 * W3 PgCache plugin
 */
if (!defined('W3TC')) {
    die();
}

w3_require_once(W3TC_LIB_W3_DIR . '/Plugin.php');

/**
 * Class W3_Plugin_PgCache
 */
class W3_Plugin_PgCache extends W3_Plugin {
    /**
     * Runs plugin
     */
    function run() {
        add_filter('cron_schedules', array(
            &$this,
            'cron_schedules'
        ));
    
        if ($this->_config->get_string('pgcache.engine') == 'file' || 
                $this->_config->get_string('pgcache.engine') == 'file_generic') {
            add_action('w3_pgcache_cleanup', array(
                &$this,
                'cleanup'
            ));
        }

        add_action('w3_pgcache_prime', array(
            &$this,
            'prime'
        ));

        add_action('publish_phone', array(
            &$this,
            'on_post_edit'
        ), 0);

        add_action('wp_trash_post', array(
            &$this,
            'on_post_change'
        ), 0);

        add_action('save_post', array(
            &$this,
            'on_post_change'
        ), 0);

        global $wp_version;
        if (version_compare($wp_version,'3.5', '>=')) {
            add_action('clean_post_cache', array(
                &$this,
                'on_post_change'
            ), 0, 2);
        }

        add_action('comment_post', array(
            &$this,
            'on_comment_change'
        ), 0);

        add_action('edit_comment', array(
            &$this,
            'on_comment_change'
        ), 0);

        add_action('delete_comment', array(
            &$this,
            'on_comment_change'
        ), 0);

        add_action('wp_set_comment_status', array(
            &$this,
            'on_comment_status'
        ), 0, 2);

        add_action('trackback_post', array(
            &$this,
            'on_comment_change'
        ), 0);

        add_action('pingback_post', array(
            &$this,
            'on_comment_change'
        ), 0);

        add_action('switch_theme', array(
            &$this,
            'on_change'
        ), 0);

        add_action('wp_update_nav_menu', array(
            &$this,
            'on_change'
        ), 0);

        add_action('edit_user_profile_update', array(
            &$this,
            'on_change'
        ), 0);
        
        add_filter('comment_cookie_lifetime', array(
            &$this,
            'comment_cookie_lifetime'
        ));

        add_action('w3tc_purge_from_pgcache', array(
            &$this,
            'on_post_change'
        ), 0);

        if (w3_is_multisite()) {
            add_action('delete_blog', array(
                &$this,
                'on_change'
            ), 0);
        }

        add_action('delete_post', array(
            &$this,
            'on_post_edit'
        ), 0);

        if ($this->_config->get_string('pgcache.engine') == 'file_generic') {
            add_action('wp_logout', array (
                &$this,
                'on_logout'
            ), 0);

            add_action('wp_login', array (
                &$this,
                'on_login'
            ), 0);
        }

        add_filter('wp_redirect', array(
            &$this,
            'on_redirect_cleanup'
        ), 0, 1);

        add_action('sns_actions_executed', array(
            &$this,
            'flush_post_cleanup'
        ), 0);

        if ($this->_config->get_boolean('pgcache.prime.post.enabled', false)) {
            add_action('publish_post', array(
                &$this,
                'prime_post'
            ), 30);
        }

        if ($this->_config->get_boolean('pgcache.late_init') && !is_admin()) {
            add_action('init', array($this,'delayed_cache_print'), 99999);
        }
    }

    /**
     * Does disk cache cleanup
     *
     * @return void
     */
    function cleanup() {
        $this->_get_admin()->cleanup();
    }

    /**
     * Prime cache
     *
     * @param integer $start
     * @return void
     */
    function prime($start = 0) {
        $this->_get_admin()->prime($start);
    }

    /**
     * Instantiates worker on demand
     *
     * @return W3_Plugin_PgCacheAdmin
     */
    private function _get_admin() {
        return w3_instance('W3_Plugin_PgCacheAdmin');
    }

    /**
     * Cron schedules filter
     *
     * @param array $schedules
     * @return array
     */
    function cron_schedules($schedules) {
        $gc_interval = $this->_config->get_integer('pgcache.file.gc');
        $prime_interval = $this->_config->get_integer('pgcache.prime.interval');

        return array_merge($schedules, array(
            'w3_pgcache_cleanup' => array(
                'interval' => $gc_interval,
                'display' => sprintf('[W3TC] Page Cache file GC (every %d seconds)', $gc_interval)
            ),
            'w3_pgcache_prime' => array(
                'interval' => $prime_interval,
                'display' => sprintf('[W3TC] Page Cache prime (every %d seconds)', $prime_interval)
            )
        ));
    }

    /**
     * Post edit action
     *
     * @param integer $post_id
     */
    function on_post_edit($post_id) {
        if ($this->_config->get_boolean('pgcache.cache.flush')) {
            $this->on_change();
        } else {
            $this->on_post_change($post_id);
        }
    }

    /**
     * Post change action
     *
     * @param integer $post_id
     * @param null $post
     * @return void
     */
    function on_post_change($post_id, $post = null) {
        static $flushed_posts = array();
        if (!in_array($post_id, $flushed_posts)) {

            if (is_null($post))
                $post = $post_id;

            if (!w3_is_flushable_post($post, 'pgcache', $this->_config)) {
                return;
            }

            $w3_cacheflush = w3_instance('W3_CacheFlush');
            $w3_cacheflush->pgcache_flush_post($post_id);

            $flushed_posts[] = $post_id;
        }
    }

    /**
     * 
     * @param integer $lifetime
     * @return integer
     */
    function comment_cookie_lifetime($lifetime) {
        $l = $this->_config->get_integer('pgcache.comment_cookie_ttl');
        if ($l != -1)
            return $l;
        else
            return $lifetime;
    }
    
    /**
     * Comment change action
     *
     * @param integer $comment_id
     */
    function on_comment_change($comment_id) {
        $post_id = 0;

        if ($comment_id) {
            $comment = get_comment($comment_id, ARRAY_A);
            $post_id = !empty($comment['comment_post_ID']) ? (int) $comment['comment_post_ID'] : 0;
        }

        $this->on_post_change($post_id);
    }

    /**
     * Comment status action
     *
     * @param integer $comment_id
     * @param string $status
     */
    function on_comment_status($comment_id, $status) {
        if ($status === 'approve' || $status === '1') {
            $this->on_comment_change($comment_id);
        }
    }

    /**
     * Change action
     */
    function on_change() {
        static $flushed = false;

        if (!$flushed) {
            $w3_pgcache = w3_instance('W3_CacheFlush');
            $w3_pgcache->pgcache_flush();
        }
    }

    /**
     * Add cookie on logout to circumvent pagecache due to browser cache resulting in 304s
     */
    function on_logout() {
        setcookie('w3tc_logged_out');
    }

    /**
     * Remove logout cookie on logins
     */
    function on_login() {
        if (isset($_COOKIE['w3tc_logged_out']))
            setcookie('w3tc_logged_out', '', 1);
    }

    /**
     * @param $location
     * @return mixed
     */
    function on_redirect_cleanup($location) {
        $this->flush_post_cleanup();
        return $location;
    }

    /**
     * Runs after multiple post edits
     */
    function flush_post_cleanup() {
        $w3_pgcache = w3_instance('W3_PgCacheFlush');
        $w3_pgcache->flush_post_cleanup();
    }

    /**
     * @param $post_id
     * @return boolean
     */
    function prime_post($post_id) {
        $w3_pgcache = w3_instance('W3_CacheFlush');
        return $w3_pgcache->prime_post($post_id);
    }

    public function delayed_cache_print() {
        $w3_pgcache = w3_instance('W3_PgCache');
        $w3_pgcache->delayed_cache_print();
    }
}