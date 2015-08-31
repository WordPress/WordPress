<?php
/**
 * W3 NewRelicAdmin plugin
 */
if (!defined('W3TC')) {
    die();
}

w3_require_once(W3TC_LIB_W3_DIR . '/Plugin.php');
w3_require_once(W3TC_INC_DIR . '/functions/rule.php');

/**
 * Class W3_Plugin_NewRelicAdmin
 */
class W3_Plugin_NewRelicAdmin extends W3_Plugin {

    /**
     * Called on plugin instantiation
     */
    function run() {
        add_filter('w3tc_compatibility_test', array($this, 'verify_compatibility'));
        if (is_admin()) {
            add_action('wp_ajax_admin_w3tc_verify_newrelic_api_key', array($this, 'verify_newrelic_api_key'));
            add_action('wp_ajax_w3tc_verify_newrelic_api_key', array($this, 'verify_newrelic_api_key'));
            add_action('wp_ajax_admin_w3tc_get_newrelic_applications', array($this, 'get_newrelic_applications'));
            add_action('wp_ajax_w3tc_get_newrelic_applications', array($this, 'get_newrelic_applications'));
            $new_relic_enabled = $this->_config->get_boolean('newrelic.enabled');
            if ($new_relic_enabled) {
                global $pagenow;
                w3_require_once( W3TC_LIB_W3_DIR . '/Request.php');
                $page = W3_Request::get_string('page');
                if ($pagenow == 'plugins.php' || strpos($page, 'w3tc_') !== false) {
                    if ((!w3_is_multisite()) || (w3_is_multisite() && !$this->_config->get_boolean('common.force_master'))) {
                        add_action('admin_notices', array($this, 'admin_notices'));
                    } else {
                        add_action('network_admin_notices', array($this, 'admin_notices'));
                    }
                }
            }
        }
    }

    function admin_notices() {
        /**
         * @var $nerser W3_NewRelicService
         */
        $nerser = w3_instance('W3_NewRelicService');
        $new_relic_configured = $this->_config->get_string('newrelic.account_id') && $this->_config->get_string('newrelic.api_key');
        if (w3_get_blog_id() == 0 || !$this->_config->get_boolean('common.force_master')) {
            $new_relic_configured = $new_relic_configured && $this->_config->get_string('newrelic.application_id');
        }
        $verify_running_result = $nerser->verify_running();
        $not_running = is_array($verify_running_result);

        if ($not_running) {
            $message = '<p>' . __('New Relic is not running correctly. The plugin has detected the following issues:', 'w3-total-cache') . "</p>\n";
            $message .= "<ul class=\"w3-bullet-list\">\n";
            foreach($verify_running_result as $cause) {
                $message .= "<li>$cause</li>";
            }
            $message .= "</ul>\n";

            $message .= "<p>" . sprintf(__('Please review the <a href="%s">settings</a>.', 'w3-total-cache'), network_admin_url('admin.php?page=w3tc_general#monitoring')) . "</p>";
            w3_require_once(W3TC_INC_FUNCTIONS_DIR . '/ui.php');
            w3_e_error_box($message);
        }
    }

    /**
     * Returns a list of the verification status of the the new relic requirements. To be used on the compatibility page
     * @param $verified_list
     * @return array
     */
    function verify_compatibility($verified_list) {
        $nerser = w3_instance('W3_NewRelicService');
        $nr_verified = $nerser->verify_compatibility();
        $verified_list[] = '<strong>New Relic</strong>';
        foreach($nr_verified as $criteria => $result)
            $verified_list[] = sprintf("$criteria: %s", $result);
        return $verified_list;
    }

    /**
     * Retrieve the new relic account id. Used in AJAX requests.
     * Requires request param api_key with the API key
     */
    function verify_newrelic_api_key() {
        $api_key = W3_Request::get_string('api_key');
        /**
         * @var $nerser W3_NewRelicService
         */
        $nerser = w3_instance('W3_NewRelicService');
        try {
            $account_id = $nerser->get_account_id($api_key);
            if ($account_id) {
                $this->_config->set('newrelic.account_id', $account_id);
                $this->_config->save();
                echo $account_id;
            }
        }catch (Exception $ex) {}
        die();
    }

    /**
     * Retrieves applications. Used in AJAX requests.
     * Requires request param api_key with the API key and account_id with the Account id.
     */
    function get_newrelic_applications() {
        w3_require_once(W3TC_LIB_W3_DIR . '/NewRelicService.php');
        $api_key = W3_Request::get_string('api_key');
        $account_id = W3_Request::get_string('account_id');
        if ($api_key == '0') {
            $config_master = new W3_Config(true);
            $api_key = $config_master->get_string('newrelic.api_key');
        }
        $nerser = new W3_NewRelicService($api_key);
        $newrelic_applications = array();
        try {
            if(empty($account_id) || $account_id == '')
                $account_id = $nerser->get_account_id();
            $newrelic_applications = $nerser->get_applications($account_id);
        } catch (Exception $ex) {}
        echo json_encode($newrelic_applications);
        die();
    }
}