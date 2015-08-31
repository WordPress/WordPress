<?php
/**
 * W3 New Relic Widget
 */
if (!defined('W3TC')) {
    die();
}

w3_require_once(W3TC_LIB_W3_DIR . '/Plugin.php');
w3_require_once(W3TC_INC_DIR . '/functions/widgets.php');

/**
 * Class W3_Widget_NewRelic
 */
class W3_Widget_NewRelic extends W3_Plugin {
    private $_account_id;
    private $_application_id;
    function run() {
        w3_require_once(W3TC_INC_FUNCTIONS_DIR . '/admin.php');
        if(w3tc_get_current_wp_page() == 'w3tc_dashboard')
            add_action('admin_enqueue_scripts', array($this,'enqueue'));

        add_action('w3tc_dashboard_setup', array(
            &$this,
            'wp_dashboard_setup'
        ));
        add_action('w3tc_network_dashboard_setup', array(
            &$this,
            'wp_dashboard_setup'
        ));
        $this->setup();
    }

    /**
     * Dashboard setup action
     *
     * @return void
     */
    function wp_dashboard_setup() {
        w3_require_once(W3TC_LIB_NEWRELIC_DIR . '/NewRelicWrapper.php');
        /**
         * @var $nerser W3_NewRelicService
         */
        $nerser = w3_instance('W3_NewRelicService');

        $new_relic_enabled = $this->_config->get_boolean('newrelic.enabled');
        if ($new_relic_enabled && $nerser->verify_running()) {
            $view_vis = sprintf("https://rpm.newrelic.com/accounts/%d/applications/%d", $this->_account_id, $this->_application_id);
            $view = '<a href="' . $view_vis . '">' .__('view visualizations', 'w3-total-cache') . '</a>';
        } else {
            $view = '<span> </span>';
        }
        w3tc_add_dashboard_widget('w3tc_new_relic', $view, array(
            &$this,
            'widget_new_relic'
        ), null, 'normal');
    }

    /**
     * Loads and configured New Relic widget to be used in WP Dashboards.
     * @param $widget_id
     * @param array $form_inputs
     */
    function widget_new_relic($widget_id, $form_inputs = array()) {
        w3_require_once(W3TC_LIB_NEWRELIC_DIR . '/NewRelicWrapper.php');
        /**
         * @var $nerser W3_NewRelicService
         */
        $nerser = w3_instance('W3_NewRelicService');
        $new_relic_configured = $this->_config->get_string('newrelic.account_id') &&
            $this->_config->get_string('newrelic.api_key');
        if (w3_get_blog_id() == 0 || !$this->_config->get_boolean('common.force_master')) {
            $new_relic_configured = $new_relic_configured && $this->_config->get_string('newrelic.application_id');
        }
        $view_application = $this->_application_id;
        $new_relic_enabled = $this->_config->get_boolean('newrelic.enabled');
        $verify_running = $nerser->verify_running();
        $new_relic_running = !is_array($verify_running);
        $new_relic_summary  = array();

        $can_use_metrics = false;
        $metric_formatted = array();
        $metric_slow_pages = array();
        $metric_data = array();
        $new_relic_summary = array();
        $slowest_page_loads = array();
        $slowest_webtransaction = array();
        $slowest_database = array();
        $subscription_lvl = __('unknown', 'w3-total-cache');
        $can_use_metrics = false;
        if ($new_relic_configured && $new_relic_enabled) {
            try {
                $subscription = $nerser->get_subscription();
                $subscription_lvl = $subscription['product-name'];
                $can_use_metrics = $nerser->can_get_metrics();
                $new_relic_summary = $nerser->get_application_summary($view_application);
                if ($can_use_metrics) {
                    $metric_data = $nerser->get_dashboard_metrics();
                    w3_require_once(W3TC_LIB_NEWRELIC_DIR . '/NewRelicPresentation.php');
                    $metric_formatted = NewRelicPresentation::format_metrics_dashboard($metric_data);
                    $metric_slow_pages = $nerser->get_slowest_page_load();
                    $slowest_page_loads = NewRelicPresentation::format_slowest_pages($metric_slow_pages);
                    $metric_slowest_webtransactions = $nerser->get_slowest_webtransactions();
                    $slowest_webtransaction = NewRelicPresentation::format_slowest_webtransactions($metric_slowest_webtransactions);
                    $metric_slowest_database = $nerser->get_slowest_database();
                    $slowest_database = NewRelicPresentation::format_slowest_webtransactions($metric_slowest_database);
                }
            } catch(Exception $ex) {
            }
        } else {
            w3_require_once(W3TC_LIB_NEWRELIC_DIR . '/NewRelicPresentation.php');
            $new_relic_summary = array('Apdex' => 'N/A','Application Busy' => 'N/A','Error Rate' => 'N/A','Throughput' => 'N/A',
                  'Errors' => 'N/A','Response Time' => 'N/A','DB' => 'N/A','CPU' => 'N/A','Memory' => 'N/A');
            $metric_data = array('EndUser' => 'N/A', 'WebTransaction' => 'N/A', 'Database' => 'N/A');
            $metric_formatted = NewRelicPresentation::format_metrics_dashboard($metric_data);
            $can_use_metrics = false;
        }

        include W3TC_INC_WIDGET_DIR . '/new_relic.php';
    }

    function setup() {
        /**
         * @var $nerser W3_NewRelicService
         */
        $nerser = w3_instance('W3_NewRelicService');
        w3_require_once(W3TC_LIB_NEWRELIC_DIR . '/NewRelicWrapper.php');
        $config_master = new W3_Config(true);
        $view_application = 0;
        if ($this->_config->get_boolean('newrelic.enabled')) {
            if ($this->_config->get_boolean('newrelic.use_php_function') || w3_is_multisite()) {
                if (!$config_master->get_boolean('newrelic.use_network_wide_id')
                    && (w3_get_blog_id() == 0 || !$this->_config->get_boolean('common.force_master'))
                ) {
                    $view_application = $this->_config->get_string('newrelic.application_id', 0);
                } else {
                    $appname = NewRelicWrapper::get_wordpress_appname($this->_config, $config_master, false);
                    try {
                        $view_application = $nerser->get_application_id($appname);
                        $nerser->set_application_id($view_application);
                    } catch (Exception $ex) {
                    }
                }
            } else {
                $view_application = $this->_config->get_string('newrelic.application_id', 0);
            }
        }
        $this->_application_id = $view_application;
        $this->_account_id = $this->_config->get_string('newrelic.account_id', 0);
    }

    public function enqueue() {
        wp_enqueue_style('w3tc-widget');
        wp_enqueue_script('w3tc-metadata');
        wp_enqueue_script('w3tc-widget');
    }
}
