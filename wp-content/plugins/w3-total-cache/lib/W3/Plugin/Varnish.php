<?php

/**
 * W3 Varnish plugin
 */
if (!defined('W3TC')) {
    die();
}

w3_require_once(W3TC_LIB_W3_DIR . '/Plugin.php');

/**
 * Class W3_Plugin_Varnish
 */
class W3_Plugin_Varnish extends W3_Plugin {
    /**
     * Runs plugin
     */
    function run() {

        add_action('publish_phone', array(
            &$this,
            'on_post_edit'
        ), 10);

        add_action('wp_trash_post', array(
            &$this,
            'on_post_change'
        ), 10);

        add_action('save_post', array(
            &$this,
            'on_post_change'
        ), 10);

        global $wp_version;
        if (version_compare($wp_version,'3.5', '>=')) {
            add_action('clean_post_cache', array(
                &$this,
                'on_post_change'
            ), 10, 2);
        }
        add_action('comment_post', array(
            &$this,
            'on_comment_change'
        ), 10);

        add_action('edit_comment', array(
            &$this,
            'on_comment_change'
        ), 10);

        add_action('delete_comment', array(
            &$this,
            'on_comment_change'
        ), 10);

        add_action('wp_set_comment_status', array(
            &$this,
            'on_comment_status'
        ), 10, 2);

        add_action('trackback_post', array(
            &$this,
            'on_comment_change'
        ), 10);

        add_action('pingback_post', array(
            &$this,
            'on_comment_change'
        ), 10);

        add_action('switch_theme', array(
            &$this,
            'on_change'
        ), 10);

        add_action('wp_update_nav_menu', array(
            &$this,
            'on_change'
        ), 10);

        add_action('edit_user_profile_update', array(
            &$this,
            'on_change'
        ), 10);

        add_action('w3tc_purge_from_pgcache', array(
            &$this,
            'on_post_change'
        ), 10);

        if (w3_is_multisite()) {
            add_action('delete_blog', array(
                &$this,
                'on_change'
            ), 10);
        }

        add_action('delete_post', array(
            &$this,
            'on_post_edit'
        ), 10);

        add_filter('wp_redirect', array(
            &$this,
            'on_redirect_cleanup'
        ), 10, 1);

        add_action('sns_actions_executed', array(
            &$this,
            'flush_post_cleanup'
        ), 10);
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

            if (!w3_is_flushable_post($post, 'varnish', $this->_config)) {
                return;
            }

            $w3_cacheflush = w3_instance('W3_CacheFlush');
            $w3_cacheflush->varnish_flush_post($post_id);

            $flushed_posts[] = $post_id;
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

    /**
     * Change action
     */
    function on_change() {
        static $flushed = false;

        if (!$flushed) {
            $w3_cacheflush = w3_instance('W3_CacheFlush');
            $w3_cacheflush->varnish_flush();
        }
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
        $w3_varnish = w3_instance('W3_VarnishFlush');
        $w3_varnish->flush_post_cleanup();
    }
}