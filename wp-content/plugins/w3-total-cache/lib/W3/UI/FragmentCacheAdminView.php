<?php

if (!defined('W3TC')) {
    die();
}

w3_require_once(W3TC_LIB_W3_DIR . '/UI/PluginView.php');

class W3_UI_FragmentCacheAdminView extends W3_UI_PluginView {
    /**
     * Current page
     *
     * @var string
     */
    var $_page = 'w3tc_fragmentcache';

    /**
     * Fragment cache tab
     *
     * @return void
     */
    function view() {
        $fragmentcache_enabled = $this->_config->get_boolean('fragmentcache.enabled');
        /**
         * @var W3_Pro_Plugin_FragmentCache $w3_plugin_fragmentcache
         */
        $w3_plugin_fragmentcache = w3_instance('W3_Pro_Plugin_FragmentCache');

        $registered_groups = $w3_plugin_fragmentcache->get_registered_fragment_groups();
        $registered_global_groups = $w3_plugin_fragmentcache->get_registered_global_fragment_groups();
        include W3TC_INC_DIR . '/options/pro/fragmentcache.php';
    }
}
