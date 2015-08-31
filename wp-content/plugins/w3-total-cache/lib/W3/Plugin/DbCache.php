<?php

/**
 * W3 DbCache plugin
 */
if (!defined('W3TC')) {
    die();
}

w3_require_once(W3TC_LIB_W3_DIR . '/Plugin.php');

/**
 * Class W3_Plugin_DbCache
 */
class W3_Plugin_DbCache extends W3_Plugin {
    /**
     * Runs plugin
     */
    function run() {
        add_filter('cron_schedules', array(
            &$this,
            'cron_schedules'
        ));

        if ($this->_config->get_string('dbcache.engine') == 'file') {
            add_action('w3_dbcache_cleanup', array(
                &$this,
                'cleanup'
            ));
        }

        add_action('publish_phone', array(
            &$this,
            'on_change'
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

        add_action('edit_user_profile_update', array(
            &$this,
            'on_change'
        ), 0);

        if (w3_is_multisite()) {
            add_action('delete_blog', array(
                &$this,
                'on_change'
            ), 0);
        }

        add_action('delete_post', array(
            &$this,
            'on_post_change'
        ), 0);
    }

    /**
     * Does disk cache cleanup
     *
     * @return void
     */
    function cleanup() {
        w3_require_once(W3TC_LIB_W3_DIR . '/Cache/File/Cleaner.php');
        
        $w3_cache_file_cleaner = new W3_Cache_File_Cleaner(array(
            'cache_dir' => w3_cache_blog_dir('db'),
            'clean_timelimit' => $this->_config->get_integer('timelimit.cache_gc')
        ));
        
        $w3_cache_file_cleaner->clean();
    }
    
    /**
     * Cron schedules filter
     *
     * @param array $schedules
     * @return array
     */
    function cron_schedules($schedules) {
        $gc = $this->_config->get_integer('dbcache.file.gc');

        return array_merge($schedules, array(
            'w3_dbcache_cleanup' => array(
                'interval' => $gc,
                'display' => sprintf('[W3TC] Database Cache file GC (every %d seconds)', $gc)
            )
        ));
    }

    /**
     * Change action
     */
    function on_change() {
        static $flushed = false;

        if (!$flushed) {
            $flusher = w3_instance('W3_CacheFlush');
            $flusher->dbcache_flush();

            $flushed = true;
        }
    }

    /**
     * Change post action
     */
    function on_post_change($post_id = 0, $post = null) {
        static $flushed = false;

        if (!$flushed) {
            if (is_null($post))
                $post = $post_id;

            if ($post_id>0 && !w3_is_flushable_post($post, 'dbcache', $this->_config)) {
                return;
            }

            $flusher = w3_instance('W3_CacheFlush');
            $flusher->dbcache_flush();

            $flushed = true;
        }
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
}
