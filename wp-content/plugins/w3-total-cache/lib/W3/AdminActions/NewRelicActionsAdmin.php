<?php
if (!defined('W3TC')) {
    die();
}
w3_require_once(W3TC_INC_FUNCTIONS_DIR . '/admin.php');

class W3_AdminActions_NewRelicActionsAdmin {

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

    function action_save_new_relic() {
        if ($this->_config->get_boolean('newrelic.enabled')) {
            /**
             * @var $nerser W3_NewRelicService
             */
            $nerser = w3_instance('W3_NewRelicService');
            $application = W3_Request::get_array('application');
            $application['alerts_enabled'] = $application['alerts_enabled'] == 1 ? 'true' : 'false';
            $application['rum_enabled'] = $application['rum_enabled'] == 1 ? 'true' : 'false';
            $result=$nerser->update_application_settings($application);
            w3_admin_redirect(array(
                'w3tc_note' => 'new_relic_save'
            ), true);
        }
    }

    /**
     * New Relic tab
     */
    function action_new_relic_view_new_relic_app() {
        $nerser = w3_instance('W3_NewRelicService');
        $view_application = W3_Request::get_integer('view_application', 0);
        $dashboard = '';
        if ($view_application)
            $dashboard = $nerser->get_dashboard($view_application);
        echo $dashboard;
    }
}