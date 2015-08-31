<?php
/**
 * W3 Forum Widget
 */
if (!defined('W3TC')) {
    die();
}

w3_require_once(W3TC_LIB_W3_DIR . '/Plugin.php');
w3_require_once(W3TC_INC_DIR . '/functions/widgets.php');

/**
 * Class W3_Widget_Forum
 */
class W3_Widget_Services extends W3_Plugin {
    /**
     * Array of request types
     *
     * @var array
     */
    var $_request_types = array();
    var $_json_request_types = array();

    /**
     * Array of request groups
     *
     * @var array
     */
    var $_request_groups = array(
            'email_support',
            'phone_support',
            'plugin_config',
            'theme_config',
            'linux_config'
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

    function run() {
        w3_require_once(W3TC_INC_FUNCTIONS_DIR . '/admin.php');
        if(w3tc_get_current_wp_page() == 'w3tc_dashboard')
            add_action('admin_enqueue_scripts', array($this,'enqueue'));

        $this->_json_request_types = array(
            'email_support' => sprintf(__('Less than 15 Minute Email Support Response %s', 'w3-total-cache'), '(M-F 9AM - 5PM EDT): $75 USD'),
            'phone_support' => sprintf(__('Less than 15 Minute Phone Support Response %s', 'w3-total-cache'), '(M-F 9AM - 5PM EDT): $150 USD'),
            'plugin_config' => sprintf(__('Professional Plugin Configuration %s', 'w3-total-cache'),'Starting @ $100 USD'),
            'theme_config' => sprintf(__('Theme Performance Optimization & Plugin Configuration %s', 'w3-total-cache'),'Starting @ $150 USD'),
            'linux_config' => sprintf(__('Linux Server Optimization & Plugin Configuration %s', 'w3-total-cache'), 'Starting @ $200 USD')
        );
        $this->_request_types = array(
            'email_support' => sprintf(__('Less than 15 Minute Email Support Response %s', 'w3-total-cache'), '<br /><span>(M-F 9AM - 5PM EDT): $75 USD</span>'),
            'phone_support' => sprintf(__('Less than 15 Minute Phone Support Response %s', 'w3-total-cache'), '<br /><span>(M-F 9AM - 5PM EDT): $150 USD</span>'),
            'plugin_config' => sprintf(__('Professional Plugin Configuration %s', 'w3-total-cache'),'<br /><span>Starting @ $100 USD</span>'),
            'theme_config' => sprintf(__('Theme Performance Optimization & Plugin Configuration %s', 'w3-total-cache'),'<br /><span>Starting @ $150 USD</span>'),
            'linux_config' => sprintf(__('Linux Server Optimization & Plugin Configuration %s', 'w3-total-cache'), '<br /><span>Starting @ $200 USD</span>')
        );
        add_action('w3tc_dashboard_setup', array(
            &$this,
            'wp_dashboard_setup'
        ));
        add_action('w3tc_network_dashboard_setup', array(
            &$this,
            'wp_dashboard_setup'
        ));

        if (is_admin()) {
            add_action('wp_ajax_w3tc_action_payment_code', array($this, 'action_payment_code'));
        }
    }

    /**
     * Dashboard setup action
     *
     * @return void
     */
    function wp_dashboard_setup() {
        w3tc_add_dashboard_widget('w3tc_services', __('Premium Services', 'w3-total-cache'), array(
            &$this,
            'widget_form'
        ),null, 'normal',
        'div'
        );
    }

    function widget_form() {
        include W3TC_INC_WIDGET_DIR . '/services.php';
    }

    function action_payment_code() {
        w3_require_once(W3TC_LIB_W3_DIR . '/Request.php');

        $request_type = W3_Request::get_string('request_type');

        $request_id = date('YmdHi');
        $return_url = admin_url('admin.php?page=w3tc_support&request_type=' . $request_type . '&payment=1&request_id=' . $request_id);
        $cancel_url = admin_url('admin.php?page=w3tc_dashboard');
        $form_values = array(
            "cmd" => "_xclick",
            "business" =>  W3TC_PAYPAL_BUSINESS,
            "item_name" => esc_attr(sprintf('%s: %s (#%s)', ucfirst(w3_get_host()), $this->_json_request_types[$request_type], $request_id)),
            "amount" => sprintf('%.2f', $this->_request_prices[$request_type]),
            "currency_code" => "USD",
            "no_shipping" => "1",
            "rm" => "2",
            "return" => esc_attr($return_url),
            "cancel_return" => esc_attr($cancel_url));
        echo json_encode($form_values);
        die();
    }

    public function enqueue() {
        wp_enqueue_style('w3tc-widget');
        wp_enqueue_script('w3tc-metadata');
        wp_enqueue_script('w3tc-widget');
    }
}
