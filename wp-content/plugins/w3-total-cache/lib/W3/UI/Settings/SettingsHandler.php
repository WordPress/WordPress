<?php
if (!defined('W3TC')) { die(); }

/**
 * Class W3_UI_Settings_SettingsHandler
 */
class W3_UI_Settings_SettingsHandler {

    /**
     * List of config modules and their related SettingsBase classes
     * @var array
     */
    private $modules = array('pgcache' => 'PageCache', 'minify' => 'Minify', 'dbcache' => 'DatabaseCache',
        'objectcache' => 'ObjectCache', 'fragmentcache' => 'FragmentCache', 'browsercache' => 'BrowserCache',
        'cdn' => 'CDN','cdncache' => 'CDN', 'newrelic' => 'Monitoring', 'varnish' => 'Varnish', 'widget' => 'General'
        ,'config' => 'General', 'common' => 'General', 'plugin' => 'General',
        'cluster' => 'SNS', 'mobile' => 'Mobile'
        );
    /**
     * @var array
     */
    private $custom_modules = array();

    function __construct() {
        if (function_exists('apply_filters'))
            $this->custom_modules = apply_filters('w3tc_custom_module_settings', array());
    }
    /**
     * Gets the label connected with a config key in the provided area
     * @param string $config_key
     * @param string $area get the
     * @return string
     */
    public function get_label($config_key, $area) {
        $a = explode('.', $config_key);
        $module = array_shift($a);
        if (isset($this->modules[$module])) {
            w3_require_once(W3TC_LIB_W3_DIR . "/UI/Settings/SettingsBase.php");
            $settings = w3_instance("W3_UI_Settings_{$this->modules[$module]}");
            return $settings->get_label($config_key, $area);
        } if(isset($this->custom_modules[$module])) {
            w3_require_once(W3TC_LIB_W3_DIR . "/UI/Settings/SettingsBase.php");
            $settings = call_user_func($this->custom_modules[$module]);
            return $settings->get_label($config_key, $area);
        } else {
            trigger_error(sprintf('Cannot find configuration key %s for area %s.', $config_key, $area), E_USER_NOTICE);
            return '';
        }
    }

    /**
     * Retrieves meta data concerning a config key, label and connected area
     * @param string $config_key
     * @return string
     */
    public function get_meta($config_key) {
        $a = explode('.', $config_key);
        $module = array_shift($a);
        if (isset($this->modules[$module])) {
            w3_require_once(W3TC_LIB_W3_DIR . "/UI/Settings/SettingsBase.php");
            /**
             * @var W3_UI_Settings_SettingsBase $settings
             */
            $settings = w3_instance("W3_UI_Settings_{$this->modules[$module]}");
            return $settings->get_meta($config_key);
        } if(isset($this->custom_modules[$module])) {
            w3_require_once(W3TC_LIB_W3_DIR . "/UI/Settings/SettingsBase.php");
            $settings = call_user_func($this->custom_modules[$module]);
            return $settings->get_meta($config_key);
        } else {
            trigger_error(sprintf('Cannot find configuration key %s.', $config_key), E_USER_NOTICE);
            return '';
        }
    }

    public function can_change($config_key, $meta) {
        $a = explode('.', $config_key);
        $module = array_shift($a);
        if (isset($this->modules[$module])) {
            w3_require_once(W3TC_LIB_W3_DIR . "/UI/Settings/SettingsBase.php");
            /**
             * @var W3_UI_Settings_SettingsBase $settings
             */
            $settings = w3_instance("W3_UI_Settings_{$this->modules[$module]}");
            return $settings->can_change($config_key, $meta);
        } if(isset($this->custom_modules[$module])) {
            w3_require_once(W3TC_LIB_W3_DIR . "/UI/Settings/SettingsBase.php");
            $settings = call_user_func($this->custom_modules[$module]);
            return $settings->can_change($config_key, $meta);
        } else {
            trigger_error(sprintf('Cannot find configuration key %s.', $config_key), E_USER_NOTICE);
            return false;
        }
    }
}
