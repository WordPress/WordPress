<?php

/**
 * Wraps around New Relic PHP Agent functions and makes sure the function exists.
 */
class NewRelicWrapper {
    /**
     * @param W3_Config $config
     * @param W3_Config $config_master
     * @param bool $do_merge if to merge with network main site
     * @return string
     */
    public static function get_wordpress_appname($config, $config_master, $do_merge = true) {
        if (w3_is_network()) {
            if ($config_master->get_boolean('newrelic.use_network_wide_id')) {
                $appname = $config_master->get_string('newrelic.appname');
            } else {
                $merge = $config->get_boolean('newrelic.merge_with_network');
                $merge_name = '';
                if ($do_merge && $merge && w3_get_blog_id() != 0) {
                    $merge_name = ';' . $config_master->get_string('newrelic.appname');
                }
                if (w3_get_blog_id() != 0 && !$config->get_boolean('common.force_master')) {
                    $appname = $config->get_string('newrelic.appname', '');
                    if (empty($appname)) {
                        $prefix = $config->get_string('newrelic.appname_prefix');
                        $appname = $prefix . trim(w3_get_home_domain() . w3_get_site_path(), '/');
                    }
                } else if (w3_get_blog_id() != 0) {
                    $prefix = $config->get_string('newrelic.appname_prefix');
                    $appname = $prefix . trim(w3_get_home_domain() . w3_get_site_path(), '/');
                } else {
                    $appname = $config->get_string('newrelic.appname');
                }

                $appname = $appname . $merge_name;
            }
        } else {
            $appname = $config->get_string('newrelic.appname');
        }
        return $appname;
    }

    public static function set_appname($name, $license = '', $xmit =false) {
        self::call('newrelic_set_appname',array($name, $license, $xmit));
    }

    public static function mark_as_background_job($flag = true) {
        self::call('newrelic_background_job', $flag);
    }

    public static function disable_auto_rum() {
        self::call('newrelic_disable_autorum');
    }

    public static function name_transaction($name) {
        self::call('newrelic_name_transaction', $name);
    }

    public static function get_browser_timing_header() {
        return self::call('newrelic_get_browser_timing_header');
    }

    public static function get_browser_timing_footer() {
        return self::call('newrelic_get_browser_timing_footer');
    }

    public static function ignore_transaction(){
        self::call('newrelic_ignore_transaction');
    }

    public static function ignore_apdex() {
        self::call('newrelic_ignore_apdex');
    }

    public static function start_transaction ($appname, $license = '') {
        $args = array();
        $args[] = $appname;
        if ($license)
            $args[] = $license;
        self::call('newrelic_start_transaction', $args);
    }

    public static function add_custom_parameter($name, $value) {
        self::call('newrelic_add_custom_parameter', array($name, $value));
    }
    public static function capture_params($enable = true) {
        self::call('newrelic_capture_params', $enable);
    }

    public static function custom_metric($name, $value) {
        self::call('newrelic_custom_metric', array($name, $value));
    }

    public static function add_custom_tracer($function) {
        self::call('newrelic_add_custom_tracer', $function);
    }
    public static function end_of_transaction() {
        self::call('newrelic_end_of_transaction');
    }

    public static function end_transaction() {
        self::call('newrelic_end_transaction');
    }

    public static function set_user_attributes($user = '', $account = '', $product = '') {
        self::call('newrelic_set_user_attributes', array($user, $account, $product));
    }
    private static function call($function, $args = null) {
        if (function_exists($function)) {
            if ($args)
                if (is_array($args))
                    return call_user_func_array($function, $args);
                else
                    return call_user_func($function, $args);
            else
                return call_user_func($function);

        }
    }
}
