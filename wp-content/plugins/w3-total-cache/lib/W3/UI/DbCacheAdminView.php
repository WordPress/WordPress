<?php

if (!defined('W3TC')) {
    die();
}

w3_require_once(W3TC_LIB_W3_DIR . '/UI/PluginView.php');

class W3_UI_DbCacheAdminView extends W3_UI_PluginView {
    /**
     * Current page
     *
     * @var string
     */
    var $_page = 'w3tc_dbcache';


    /**
     * Database cache tab
     *
     * @return void
     */
    function view() {
        $dbcache_enabled = $this->_config->get_boolean('dbcache.enabled');

        include W3TC_INC_DIR . '/options/dbcache.php';
    }

    /**
     * Database cluster config editor
     *
     * @return void
     */
    function dbcluster_config() {
        $this->_page = 'w3tc_dbcluster_config';
        if (w3_is_dbcluster())
            $content = @file_get_contents(W3TC_FILE_DB_CLUSTER_CONFIG);
        else
            $content = @file_get_contents(W3TC_DIR . '/ini/dbcluster-config-sample.php');

        include W3TC_INC_OPTIONS_DIR . '/enterprise/dbcluster-config.php';
    }
}