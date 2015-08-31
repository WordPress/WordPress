<?php

if (!defined('W3TC')) {
    die();
}

w3_require_once(W3TC_LIB_W3_DIR . '/UI/PluginView.php');

class W3_UI_DashboardAdminView extends W3_UI_PluginView {
    /**
     * Current page
     *
     * @var string
     */
    var $_page = 'w3tc_dashboard';


    /**
     * Dashboard tab
     */
    function view() {
        w3_require_once(W3TC_INC_DIR . '/functions/widgets.php');
        /**
         * @var $module_status W3_ModuleStatus
         */
        $module_status = w3_instance('W3_ModuleStatus');
        w3tc_dashboard_setup();
        global $current_user;
        $config_master = $this->_config_master;

        $browsercache_enabled = $module_status->is_enabled('browsercache');

        $enabled = $module_status->plugin_is_enabled();

        $can_empty_memcache = $module_status->can_empty_memcache();

        $can_empty_opcode = $module_status->can_empty_opcode();

        $can_empty_apc_system = $module_status->can_empty_apc_system();

        $can_empty_file = $module_status->can_empty_file();

        $can_empty_varnish = $module_status->can_empty_varnish();

        $cdn_enabled = $module_status->is_enabled('cdn');
        $cdn_mirror_purge = w3_cdn_can_purge_all($module_status->get_module_engine('cdn'));


        // Required for Update Media Query String button
        $browsercache_update_media_qs = ($this->_config->get_boolean('browsercache.cssjs.replace') || $this->_config->get_boolean('browsercache.other.replace'));

        include W3TC_INC_DIR . '/options/dashboard.php';
    }
}
