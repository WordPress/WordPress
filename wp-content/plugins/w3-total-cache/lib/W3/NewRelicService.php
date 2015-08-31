<?php
/**
 * Wrapper for NewRelicAPI.
 * @see NewRelicAPI
 */
class W3_NewRelicService {
    private $_api_key;
    private $_account_id;
    private $_cache_time;

    /**
     * Checks W3_Config for the params if they are not provided in the constructor.
     * @param string $api_key
     * @param string $account_id
     * @param string $application_id
     */
    function __construct($api_key = '', $account_id = '', $application_id = '') {
        /**
         * @var $config W3_Config
         */
        $config = w3_instance('W3_Config');
        if ($api_key)
            $this->_api_key = $api_key;
        else
            $this->_api_key = $config->get_string('newrelic.api_key');
        if ($account_id)
            $this->_account_id = $account_id;
        else
            $this->_account_id = $config->get_string('newrelic.account_id');
        if ($application_id)
            $this->_application_id = $application_id;
        else
            $this->_application_id = $config->get_integer('newrelic.application_id');
        $this->_cache_time = $config->get_integer('newrelic.cache_time', 5);
        if ($this->_cache_time<1)
            $this->_cache_time = 5;
    }

    /**
     * Checks if the platform running WP is supported by New Relic.
     * The verifications is based on https://newrelic.com/docs/php/new-relic-for-php
     * @return array
     */
    function verify_compatibility() {
        $supported_string = __('Supported', 'w3-total-cache');
        $php_versions = array('5.2.x','5.3.x','5.4.x');
        $verified = array();
        $version = explode('.', PHP_VERSION);
        $php_version = sprintf('%s.%s.%s', $version[0], $version[1], $version[2]);
        $php_version_check = (version_compare($php_version, '5.2','>') && version_compare($php_version, '5.5','<'));
        $verified[__('PHP version','w3-total-cache')] = ($php_version_check) ? $supported_string :
                                                            sprintf(__('Not supported: %s. Supported versions are %s.',
                                                                    'w3-total-cache'),
                                                            $php_version, implode(', ',$php_versions));

        $os_name = php_uname('s');
        switch($os_name) {
            case 'Linux':
                /**
                 * Any other version of Linux with kernel 2.6.13 or later
                 * (2.6.26 and later highly recommended) and glibc 2.5 or later
                 */
                $version = explode('.', php_uname('r'));
                $os_version = sprintf('%d.%d.%d', $version[0], $version[1], $version[2]);
                $os_check = version_compare($os_version,'2.6.13', '>=');
                break;
            case 'FreeBSD':
                /**
                 * You must enable the linkthr build option so that the New Relic agent will not cause your PHP to hang.
                 */
                $version = explode('.', php_uname('r'));
                $os_version = sprintf('%d.%d', $version[0], $version[1]);
                $os_check = version_compare($os_version,'7.3', '>=');
                break;
            case 'MacOS/X':
                /**
                 * MacOS/X configurations do not use the standard /etc/init.d/newrelic-daemon script.
                 * Instead, they use /usr/bin/newrelic-daemon-service in the same way; for example:
                 * /usr/bin/newrelic-daemon-service restart.
                 */
                $version = explode('.', php_uname('r'));
                $os_version = sprintf('%d.%d', $version[0], $version[1]);
                $os_check = version_compare($os_version,'10.5', '>=');
                break;
            case 'Open Solaris':
                /**
                 * snv_134b or later
                 */
                $version = explode('.', php_uname('r'));
                $os_version = sprintf('%d', $version[0]);
                $os_check = version_compare($os_version,'10', '==');
                break;
            default:
                $os_check = false;
                $os_name = php_uname();
                $os_version = '';
        }

        $verified[__('Operating System','w3-total-cache')] = ($os_check) ? $supported_string :
                                        sprintf(__('Not Supported. (%s %s See %s page.)', 'w3-total-cache'),
                                        $os_name, $os_version,
                                        '<a href="https://newrelic.com/docs/php/new-relic-for-php#requirements" target="_blank">
                                        NewRelic Requirements</a>'
                                        );

        /**
         * Apache 2.2 or 2.4 via mod_php
         * Or any web server that supports FastCGI using php-fpm
         */
        $server = explode('/', $_SERVER['SERVER_SOFTWARE']);
        $ws_check = false;
        $ws_name = $_SERVER['SERVER_SOFTWARE'];
        $ws_version = '';

        if (sizeof($server) > 1) {
            $ws_name = $server[0];
            $ws_version = $server[1];
            if (sizeof($version = explode('.', $ws_version))>1)
                $ws_version = sprintf('%d.%d', $version[0], $version[1]);
        }
        switch (true){
            case w3_is_apache():
                if ($ws_version)
                    $ws_check = version_compare($ws_version,'2.2','>=') || version_compare($ws_version,'2.4','>=');
                break;
            case w3_is_nginx():
                $ws_check = php_sapi_name() == 'fpm-fcgi';
                $ws_name .= php_sapi_name();
                break;
            default:
                $ws_check = php_sapi_name() == 'fpm-fcgi';
                $ws_name = $_SERVER['SERVER_SOFTWARE'];
                $ws_version = '';
        }
        $verified[__('Web Server','w3-total-cache')] = $ws_check ? $supported_string :
                                        sprintf(__('Not Supported. (%s %s See %s page.)', 'w3-total-cache'),
                                        $ws_name, $ws_version,
                                        '<a href="https://newrelic.com/docs/php/new-relic-for-php#requirements" target="_blank">
                                        NewRelic Requirements</a>'
                                        );
        return $verified;
    }

    /**
     * Verifies that detectable New Relic functionality is running and configured properly.
     * Returns array with what is wrong if verification fails.
     * @return array|bool
     */
    public function verify_running() {
        $error = array();
        if (!$this->module_is_enabled())
            $error['module_enabled'] = __('PHP module is not enabled.', 'w3-total-cache');
        if (!$this->agent_enabled())
            $error['agent_enabled'] = __('PHP agent is not enabled.', 'w3-total-cache');
        if (!$this->get_api_key())
            $error['api_key'] = __('API Key is not configured.', 'w3-total-cache');
        if (!$this->_account_id)
            $error['account_id'] = __('Account ID is not configured.', 'w3-total-cache');
        if (!$this->_application_id)
            $error['application_id'] = __('Application ID is not configured. Enter/Select application name.', 'w3-total-cache');
        try {
            if (!$this->get_license_key_from_ini())
                $error['license'] = __('License key could not be detected.', 'w3-total-cache');
            $licences = explode(' ', trim($this->get_license_key_from_ini()));
            $licences = array_map('trim', $licences);
            if ($this->get_license_key_from_ini() && $this->get_license_key_from_account()
                && !in_array(trim($this->get_license_key_from_account()), $licences))
                $error['license'] = sprintf(__('Configured license key does not match license key(s) in account: <br />%s <br />%s', 'w3-total-cache')
                                            ,$this->get_license_key_from_ini()
                                            ,implode('<br />', $licences));
            $this->get_account_id($this->get_api_key());
        } catch (Exception $ex) {
            $error['api_key'] = __('API Key is invalid.', 'w3-total-cache');
        }
        return $error ? $error : true;
    }

    /**
     * Checks the ini or conf file to see if newrelic is enabled.
     * @return string
     */
    public function agent_enabled() {
        return ini_get('newrelic.enabled');
    }

    /**
     * Checks if the New Relic PHP module is enabled
     * @return bool
     */
    public function module_is_enabled() {
        return function_exists('newrelic_set_appname');
    }

    /**
     * Retrieves the configured license key in ini/conf files.
     * @return string
     */
    public function get_license_key_from_ini() {
        return ini_get('newrelic.license');
    }

    /**
     * Returns the API key
     * @return string
     */
    public function get_api_key() {
        return $this->_api_key;
    }

    /**
     * Retrieves the account id connected with the provided API key
     * @param int $api_key
     * @return int|null
     */
    public function get_account_id($api_key = 0) {
        $account = $this->getAPI($api_key)->get_account();
        $account_id = null;
        if ($account)
            $account_id = (int)$account['id'];
        return $account_id;
    }

    /**
     * Returns a NewRelicAPI instance depending on configured params.
     * @param int $api_key
     * @return NewRelicAPI
     */
    private function getAPI($api_key = 0) {
        static $api = null;
        if (!$api) {
            w3_require_once(W3TC_LIB_NEWRELIC_DIR . '/NewRelicAPI.php');
            if ($api_key)
                $api = new NewRelicAPI($api_key);
            else
                $api = new NewRelicAPI($this->_api_key, $this->_account_id);
        }
        return $api;
    }

    /**
     * Retrieves the Dashboard HTML fragment for the provided application or
     * for all applications connected with the API key
     * @param int $application_id
     * @return bool
     */
    public function get_dashboard($application_id = 0) {
        return $this->getAPI()->get_dashboard($application_id);
    }

    /**
     * Retrieves an array with all applications.
     * @param $account_id
     * @return array
     */
    public function get_applications($account_id = 0) {
        return $this->getAPI()->get_applications($account_id);
    }

    /**
     * Retrieves a specific application
     * @param string $application_id
     * @return mixed
     */
    public function get_application($application_id) {
        $applications = $this->get_applications();
        return $applications[$application_id];
    }

    /**
     * Retrieves the application summary
     * @param string $application_id
     * @return array
     */
    public function get_application_summary($application_id) {
        return $this->getAPI()->get_application_summary($application_id);
    }

    /**
     * Retrievs the account info connected with the API key
     * @return array|mixed|null
     */
    public function get_account() {
        static $account = null;
        if (!$account)
            $account = $this->getAPI()->get_account();
        return $account;
    }

    /**
     * Returns the subscription for the account
     * @return string|null
     */
    public function get_subscription() {
        $account = $this->get_account();
        if ($account)
            return $account['subscription'];
        return null;
    }

    /**
     * Checks if account supports retrieval of metrics (names/data)
     * @return bool
     */
    public function can_get_metrics() {
        $subscription = $this->get_subscription();
        return $subscription['product-name'] != 'Lite';
    }

    /**
     * Retrieves the license key from the account
     * @return null|string
     */
    public function get_license_key_from_account(){
        $account = $this->get_account();
        if ($account)
            return $account['license-key'];
        return null;
    }

    /**
     * Retrieves the application setting. Cached for 5 minutes.
     * @return array|mixed
     */
    public function get_application_settings() {
        if (false === ($settings = get_transient('w3_nr_app_settings'))) {
            $settings = $this->getAPI()->get_application_settings($this->_application_id);
            if ($settings)
                set_transient('w3_nr_app_settings', $settings, 60*5);
            else
                $settings = array();
        }
        return $settings;
    }

    /**
     * Update applications settings
     * @param array $application
     * @return bool
     */
    public function update_application_settings($application) {
        $result = $this->getAPI()->update_application_settings($this->_application_id, $application);
        delete_transient('w3_nr_app_settings');
        return $result;
    }

    /**
     * Retrieves metric names all or those matching regex with limit. Result is cached.
     * @param string $regex
     * @param string $limit
     * @return array|mixed
     */
    public function get_metric_names($regex = '', $limit = '') {
        if (false === ($metric_names = get_transient('w3_nr_metric_names' . md5($regex . $limit)))) {
            $metric_names_object = $this->getAPI()->get_metric_names($this->_application_id, $regex, $limit);
            if ($metric_names_object) {
                $metric_names = array();
                foreach($metric_names_object as $metric) {
                    $metric_names[$metric->name] = $metric;
                }
                set_transient('w3_nr_metric_names' . md5($regex . $limit), $metric_names, $this->_cache_time*60);
            } else
                $metric_names = array();
        }
        return $metric_names;
    }

    /**
     * Retrieves metric data for the provided metrics
     * @param array $metrics
     * @param string $field metric value field. If a metric name does not have this field the metric name is excluded
     * @param int $days
     * @param bool $summary
     * @param bool $use_subgroup
     * @return array|mixed
     */
    public function get_metric_data($metrics, $field, $days=7, $summary = true, $use_subgroup = true) {
        if (!is_array($metrics))
            $metrics = array($metrics);
        $begin = new DateTime(gmdate("Y-m-d G:i:s", strtotime(($days>1 ? "-$days days" : "-$days day"))));
        $beginStr = $begin->format('Y-m-d') . 'T' . $begin->format('H:i:s') . 'Z';
        $to = new DateTime(gmdate("Y-m-d G:i:s"));
        $toStr = $to->format('Y-m-d') . 'T' . $to->format('H:i:s') . 'Z';
        $cache_key = md5(implode(',', array($this->_account_id, $this->_application_id,
                                            $beginStr, $toStr, implode(',', $metrics), $field, $summary)));

        if (false === ($formatted_data = get_transient('w3_nr_metric_data_' . $cache_key))) {
            $metric_data = $this->getAPI()->get_metric_data($this->_account_id, $this->_application_id,
                                        $beginStr, $toStr, $metrics, $field, $summary);
            if ($metric_data) {
                foreach ($metric_data as $metric) {
                    $path = explode('/', $metric->name);
                    $group = $path[0];
                    if ($use_subgroup) {
                        $subgroup = isset($path[1]) ? ($path[1] == 'all' ? 0 : $path[1]): 0;
                        $formatted_data[$group][$subgroup][] = $metric;
                    } else {
                        $formatted_data[$group][] = $metric;
                    }
                }
                set_transient('w3_nr_metric_data_' . $cache_key, $formatted_data, $this->_cache_time*60);
            } else
                $formatted_data = array();
        }
        return $formatted_data;
    }

    /**
     * Retrieves the metrics used for the New Relic Dashboard widget
     * @return array|mixed
     */
    public function get_dashboard_metrics() {
        if (false === ($metric_data = get_transient('w3_nr_dashboard_metrics'))) {
            $metrics = array('Database/all', 'WebTransaction', 'EndUser');
            $field = 'average_response_time';
            $metric_data = $this->get_metric_data($metrics, $field, 1, true);
            if ($metric_data)
                set_transient('w3_nr_dashboard_metrics',$metric_data, $this->_cache_time*60);
            else
                $metric_data = array();
        }
        return $metric_data;
    }

    /**
     * Retrieves the top 5 pages with slowest page load
     * @return array
     */
    public function get_slowest_page_load() {
        $metric_names = $this->get_metric_names('EndUser/WebTransaction/WebTransaction/');
        $metric_names_keys = array_keys($metric_names);
        $metric_data = $this->get_metric_data($metric_names_keys, 'average_response_time', 1);
        $slowest_pages = array();
        if ($metric_data) {
            $transactions = $metric_data['EndUser']['WebTransaction'];
            foreach($transactions as $transaction) {
                $slowest_pages[str_replace('EndUser/WebTransaction/WebTransaction', '', $transaction->name)] = $transaction->average_response_time;
            }
            $slowest_pages = $this->_sort_and_slice($slowest_pages, 5);
        }
        return $slowest_pages;
    }

    /**
     * Retrieves the top 5slowest webtransactions
     * @return array
     */
    public function get_slowest_webtransactions() {
        $metric_names = $this->get_metric_names('^WebTransaction/');
        $metric_names_keys = array_keys($metric_names);
        $metric_data = $this->get_metric_data($metric_names_keys, 'average_response_time', 1);
        $slowest_webtransactions = array();
        if ($metric_data) {
            $transactions = $metric_data['WebTransaction'];
            foreach($transactions as $transaction) {
                foreach ($transaction as $tr_sub)
                    $slowest_webtransactions[str_replace('WebTransaction', '', $tr_sub->name)] = $tr_sub->average_response_time;
            }
            $slowest_webtransactions = $this->_sort_and_slice($slowest_webtransactions, 5);
        }
        return $slowest_webtransactions;
    }

    /**
     * Retrieves the top 5 slowest database queries
     * @return array
     */
    public function get_slowest_database() {
        $metric_names = $this->get_metric_names('^Database/');
        $metric_names_keys = array_keys($metric_names);
        $metric_names_keys = array_slice($metric_names_keys,7);
        $metric_data = $this->get_metric_data($metric_names_keys, 'average_response_time', 1, true, false);
        $slowest_webtransactions = array();
        if ($metric_data) {
            $transactions = $metric_data['Database'];
            foreach($transactions as $transaction) {
                $slowest_webtransactions[str_replace('Database', '', $transaction->name)] = $transaction->average_response_time;
            }
            $slowest_webtransactions = $this->_sort_and_slice($slowest_webtransactions, 5);
        }
        return $slowest_webtransactions;
    }

    /**
     * Retrieves the front end response time
     * @return int
     */
    public function get_frontend_response_time() {
        $metric_data = $this->get_metric_data('EndUser', 'average_fe_response_time', 1,true,false);
        return isset($metric_data['EndUser']) ? $metric_data['EndUser'][0]->average_fe_response_time : 0;
    }

    /**
     * Retrieves appname configured in ini or in server conf.
     * @return string
     */
    public function get_appname_from_ini() {
        return ini_get('newrelic.appname');
    }

    /**
     * Sorts an array highest to lowest and returns the top $size entries in an array.
     * @param $slowest
     * @param $size
     * @return array
     */
    private function _sort_and_slice($slowest, $size) {
        arsort($slowest, SORT_NUMERIC);
        if (sizeof($slowest) > $size)
            $slowest = array_slice($slowest, 0, $size);
        return $slowest;
    }

    /**
     * Retrieves the application name thats used on New Relic
     * @param $application_id
     * @return string
     */
    public function get_application_name($application_id) {
        $apps = $this->get_applications();
        return isset($apps[$application_id]) ? $apps[$application_id] : '';
    }

    /**
     * Retrieves the application id from New Relic
     * @param $appname
     * @return int|string
     */
    public function get_application_id($appname) {
        $apps = $this->get_applications();
        foreach ($apps as $id => $name) {
            if ($name == $appname)
                return $id;
        }
        return 0;
    }

    public function set_application_id($application_id) {
        $this->_application_id = $application_id;
    }
}
