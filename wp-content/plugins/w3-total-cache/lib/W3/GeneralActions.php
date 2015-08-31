<?php
class W3_GeneralActions extends W3_Plugin {
    function run() {
        add_filter('post_row_actions', array(
            &$this,
            'post_row_actions'
        ), 0, 2);

        add_filter('page_row_actions', array(
            &$this,
            'page_row_actions'
        ), 0, 2);

        add_action('post_submitbox_start', array(
            &$this,
            'post_submitbox_start'
        ));
    }


    /**
     * post_row_actions filter
     *
     * @param array $actions
     * @param object $post
     * @return array
     */
    function post_row_actions($actions, $post) {
        if (current_user_can('manage_options'))
            $actions = array_merge($actions, array(
                'pgcache_purge' => sprintf('<a href="%s">' . __('Purge from cache', 'w3-total-cache') . '</a>', wp_nonce_url(sprintf('admin.php?page=w3tc_dashboard&w3tc_flush_pgcache_purge_post&post_id=%d', $post->ID), 'w3tc'))
            ));

        return $actions;
    }

    /**
     * page_row_actions filter
     *
     * @param array $actions
     * @param object $post
     * @return array
     */
    function page_row_actions($actions, $post) {
        if (current_user_can('manage_options')) {
            $actions = array_merge($actions, array(
                'pgcache_purge' => sprintf('<a href="%s">' . __('Purge from cache', 'w3-total-cache') . '</a>', wp_nonce_url(sprintf('admin.php?page=w3tc_dashboard&w3tc_flush_pgcache_purge_page&post_id=%d', $post->ID), 'w3tc'))
            ));
        }
        return $actions;
    }

    /**
     * Display Purge from cache on Page/Post post.php.
     */
    function post_submitbox_start() {
        if (current_user_can('manage_options'))  {
            global $post;
            echo '<div>', sprintf('<a href="%s">' . __('Purge from cache', 'w3-total-cache') . '</a>', wp_nonce_url(sprintf('admin.php?page=w3tc_dashboard&w3tc_flush_pgcache_purge_page&post_id=%d', $post->ID), 'w3tc')), '</div>';
        }
    }
}