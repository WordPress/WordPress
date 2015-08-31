<?php

if (!defined('W3TC')) {
    die();
}

w3_require_once(W3TC_LIB_W3_DIR . '/UI/PluginView.php');

class W3_UI_ReferrerGroupsAdminView extends W3_UI_PluginView {
    /**
     * Current page
     *
     * @var string
     */
    var $_page = 'w3tc_referrer';

    /**
     * Referrer tab
     *
     * @return void
     */
    function view() {
        $groups = $this->_config->get_array('referrer.rgroups');

        $w3_referrer = w3_instance('W3_Referrer');

        $themes = $w3_referrer->get_themes();

        include W3TC_INC_DIR . '/options/referrer.php';
    }
}
