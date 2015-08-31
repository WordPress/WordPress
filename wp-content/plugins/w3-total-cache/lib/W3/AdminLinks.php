<?php

/**
 * Class W3_Environment
 */
class W3_AdminLinks {
    /**
     * Update plugin link
     *
     * @return void
     */
    function link_update($config) {
        $this->link_delete();
        $this->link_insert($config);
    }

    /**
     * Insert plugin link into Blogroll
     *
     * @return void
     */
    function link_insert($config) {
        $support = $config->get_string('common.support');
        $matches = null;
        if ($support != '' && preg_match('~^link_category_(\d+)$~', $support, $matches)) {
            require_once ABSPATH . 'wp-admin/includes/bookmark.php';

            wp_insert_link(array(
                'link_url' => W3TC_LINK_URL,
                'link_name' => W3TC_LINK_NAME,
                'link_category' => array(
                    (int) $matches[1]
                ),
                'link_rel' => 'nofollow'
            ));
        }
    }

    /**
     * Deletes plugin link from Blogroll
     *
     * @return void
     */
    function link_delete() {
        $bookmarks = get_bookmarks();
        $link_id = 0;
        foreach ($bookmarks as $bookmark) {
            if ($bookmark->link_url == W3TC_LINK_URL) {
                $link_id = $bookmark->link_id;
                break;
            }
        }
        if ($link_id) {
            require_once ABSPATH . 'wp-admin/includes/bookmark.php';
            wp_delete_link($link_id);
        }
    }
}
