<?php

if (!defined('W3TC')) {
    die();
}

w3_require_once(W3TC_LIB_W3_DIR . '/UI/PluginView.php');

class W3_UI_PgCacheAdminView extends W3_UI_PluginView {
    /**
     * Current page
     *
     * @var string
     */
    var $_page = 'w3tc_pgcache';


    /**
     * Page cache tab
     *
     * @return void
     */
    function view() {
        global $wp_rewrite;

        $feeds = $wp_rewrite->feeds;

        $feed_key = array_search('feed', $feeds);

        if ($feed_key !== false) {
            unset($feeds[$feed_key]);
        }

        $default_feed = get_default_feed();
        $pgcache_enabled = $this->_config->get_boolean('pgcache.enabled');
        $permalink_structure = get_option('permalink_structure');

        $varnish_enabled = $this->_config->get_boolean('varnish.enabled');
        $cdn_mirror_purge_enabled = w3_is_cdn_mirror($this->_config->get_string('cdn.engine')) &&
            $this->_config->get_string('cdn.engine') != 'mirror' &&
            $this->_config->get_boolean('cdncache.enabled') &&
            w3tc_edge_mode()  && (w3_is_pro($this->_config)) || w3_is_enterprise();
        $disable_check_domain = (w3_is_multisite() && w3_force_master());
        include W3TC_INC_DIR . '/options/pgcache.php';
    }
}
