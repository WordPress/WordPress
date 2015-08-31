<?php

if (!defined('W3TC')) {
    die();
}

w3_require_once(W3TC_LIB_W3_DIR . '/UI/PluginView.php');

class W3_UI_MonitoringAdminView extends W3_UI_PluginView {
    /**
     * Current page
     *
     * @var string
     */
    var $_page = 'w3tc_monitoring';


    /**
     * New Relic tab
     */
    function view() {
        $applications = array();
        $dashboard = '';
        /**
         * @var $nerser W3_NewRelicService
         */
        $nerser = w3_instance('W3_NewRelicService');
        $new_relic_configured = $this->_config->get_string('newrelic.account_id') &&
            $this->_config->get_string('newrelic.api_key') &&
            $this->_config->get_string('newrelic.application_id');
        $view_application = $this->_config->get_string('newrelic.application_id');
        $new_relic_enabled = $this->_config->get_boolean('newrelic.enabled');
        $verify_running = $nerser->verify_running();
        $application_settings = array();
        if (!is_array($verify_running)) {
            try {
                $application_settings = $nerser->get_application_settings();
            }catch(Exception $ex) {
                $application_settings = array();
            }
        }
        if ($view_metric = W3_Request::get_boolean('view_metric', false)) {
            $metric_names = $nerser->get_metric_names(W3_Request::get_string('regex', ''));
        }
        include W3TC_INC_DIR . '/options/new_relic.php';
    }
}
