<?php

if (!defined('W3TC')) {
    die();
}

w3_require_once(W3TC_LIB_W3_DIR . '/UI/PluginView.php');

class W3_UI_InstallAdminView extends W3_UI_PluginView {
    /**
     * Current page
     *
     * @var string
     */
    var $_page = 'w3tc_install';

    /**
     * Install tab
     *
     * @return void
     */
    function view() {
        $rewrite_rules_descriptors = array();

        if (w3_can_check_rules()) {
            /**
             * @var W3_AdminEnvironment $e
             */
            $e = w3_instance('W3_AdminEnvironment');
            $rewrite_rules_descriptors = $e->get_required_rules($this->_config);
            $other_areas = $e->get_other_instructions($this->_config);
        }

        include W3TC_INC_DIR . '/options/install.php';
    }
}