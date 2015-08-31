<?php
if (!defined('W3TC')) {
    die();
}
w3_require_once(W3TC_INC_FUNCTIONS_DIR . '/admin.php');

class W3_AdminActions_LicensingActionsAdmin {

    /**
     * @var W3_Config $_config
     */
    private $_config = null;

    /**
     * @var W3_ConfigAdmin
     */
    private $_config_admin = null;

    function __construct() {
        $this->_config = w3_instance('W3_Config');
        $this->_config_admin = w3_instance('W3_ConfigAdmin');
    }

    /**
     *  test action
     */
    function action_licensing_buy_plugin() {
        include W3TC_INC_DIR . '/lightbox/purchase.php';
    }

    /**
     * Self test action
     */
    function action_licensing_upgrade() {
        w3_require_once(W3TC_INC_FUNCTIONS_DIR . '/ui.php');
        w3_require_once(W3TC_INC_FUNCTIONS_DIR . '/admin_ui.php');
        include W3TC_INC_DIR . '/lightbox/upgrade.php';
    }

    function action_licensing_check_key() {
        set_transient('w3tc_license_status', false, 1);
        edd_w3edge_w3tc_activate_license($this->_config->get_string('plugin.license_key'), W3TC_VERSION);
        w3_admin_redirect(array(), true);
    }
}
