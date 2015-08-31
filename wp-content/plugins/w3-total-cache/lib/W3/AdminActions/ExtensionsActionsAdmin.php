<?php
if (!defined('W3TC')) {
    die();
}
w3_require_once(W3TC_INC_FUNCTIONS_DIR . '/admin.php');

class W3_AdminActions_ExtensionsActionsAdmin {

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
     * @throws Exception
     */
    function action_extensions_activate() {
        w3_require_once(W3TC_LIB_W3_DIR . '/Request.php');
        $extension = W3_Request::get_string('w3tc_extensions_activate');
        if ($extension) {
            w3tc_activate_extension($extension, $this->_config);
        }
        w3_admin_redirect(array(
            'w3tc_note' => 'extension_activated'
        ));
    }
}
