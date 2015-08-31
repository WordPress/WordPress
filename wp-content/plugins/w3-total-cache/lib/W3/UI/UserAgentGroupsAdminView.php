<?php

if (!defined('W3TC')) {
    die();
}

w3_require_once(W3TC_LIB_W3_DIR . '/UI/PluginView.php');

class W3_UI_UserAgentGroupsAdminView extends W3_UI_PluginView {
    /**
     * Current page
     *
     * @var string
     */
    var $_page = 'w3tc_mobile';


    /**
     * Mobile tab
     *
     * @return void
     */
    function view() {
        $groups = $this->_config->get_array('mobile.rgroups');

        $w3_mobile = w3_instance('W3_Mobile');
        $themes = $w3_mobile->get_themes();

        include W3TC_INC_DIR . '/options/mobile.php';
    }

}
