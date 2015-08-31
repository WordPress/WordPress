<?php

if (!defined('W3TC')) {
    die();
}

w3_require_once(W3TC_LIB_W3_DIR . '/UI/PluginView.php');

class W3_UI_SupportAdminView extends W3_UI_PluginView {
    /**
     * Array of request types
     *
     * @var array
     */
    var $_request_types;

    /**
     * Array of request groups
     *
     * @var array
     */
    var $_request_groups = array(
        'Free Support' => array(
            'bug_report',
            'new_feature'
        ),
        'Premium Services (per site pricing)' => array(
            'email_support',
            'phone_support',
            'plugin_config',
            'theme_config',
            'linux_config'
        )
    );

    /**
     * Request price list
     *
     * @var array
     */
    var $_request_prices = array(
        'email_support' => 75,
        'phone_support' => 150,
        'plugin_config' => 100,
        'theme_config' => 150,
        'linux_config' => 200
    );


    /**
     * Current page
     *
     * @var string
     */
    var $_page = 'w3tc_support';


    function __construct() {
        parent::__construct();
        $this->_request_types = array(
            'bug_report' => __('Submit a Bug Report', 'w3-total-cache'),
            'new_feature' => __('Suggest a New Feature', 'w3-total-cache'),
            'email_support' => __('Less than 15 Minute Email Support Response (M-F 9AM - 5PM EDT): $75 USD', 'w3-total-cache'),
            'phone_support' => __('Less than 15 Minute Phone Support Response (M-F 9AM - 5PM EDT): $150 USD', 'w3-total-cache'),
            'plugin_config' => __('Professional Plugin Configuration: Starting @ $100 USD', 'w3-total-cache'),
            'theme_config' => __('Theme Performance Optimization & Plugin Configuration: Starting @ $150 USD', 'w3-total-cache'),
            'linux_config' => __('Linux Server Optimization & Plugin Configuration: Starting @ $200 USD', 'w3-total-cache')
        );
    }

    /**
     * Support tab
     *
     * @return void
     */
    function view() {
        w3_require_once(W3TC_LIB_W3_DIR . '/Request.php');

        $request_type = W3_Request::get_string('request_type');
        $payment = W3_Request::get_boolean('payment');

        include W3TC_INC_DIR . '/options/support.php';
    }


    /**
     * Support select action
     *
     * @return void
     */
    function action_support_select() {
        $admin = w3_instance('W3_AdminActions_SupportActionsAdmin');
        $admin->action_support_select();
    }

    /**
     * Support form action
     *
     * @return void
     */
    function action_support_form() {
        $admin = w3_instance('W3_AdminActions_SupportActionsAdmin');
        $admin->action_support_form();
    }
}
