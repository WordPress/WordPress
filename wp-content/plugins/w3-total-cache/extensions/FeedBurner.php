<?php

class W3_FeedBurner {
    /**
     * @var W3_Config $_config
     */
    private $_config;

    function run() {
        $this->_config = w3_instance('W3_Config');
        add_action('publish_post', array(
            &$this,
            'flush_feedburner'
        ), 100, 1);
    }

    /**
     * If FeedBurner is enabled flush the registered FB feeds
     */
    function flush_feedburner($post_id) {
        if (!w3_is_flushable_post($post_id, 'pgcache', $this->_config)) {
            return;
        }
        $fb_urls = w3tc_get_extension_config('feedburner','urls');
        if ($fb_urls)
            $fb_urls = explode("\n", $fb_urls);

        $fb_urls[] = home_url();
        foreach($fb_urls as $url) {
            wp_remote_get('http://feedburner.google.com/fb/a/pingSubmit?bloglink=' . urlencode($url));
        }
    }
}


$ext = new W3_FeedBurner();
$ext->run();