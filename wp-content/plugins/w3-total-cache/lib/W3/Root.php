<?php

class W3_Root {
    /**
     * Enabled Plugins that has been run
     * @var W3_Plugin[]
     */
    private $_loaded_plugins = array();
    /**
     * Enabled extensions that has been run
     * @var W3_Plugin[]
     */
    private $_loaded_extensions = array();

    /**
     * List of plugins and criterias to be met for them to run
     * @var array
     */
    private $_plugins = array();

    /**
     * @var W3_Config $_config
     */
    private $_config = null;

    function __construct() {
        $this->_config = w3_instance('W3_Config');

        $this->_plugins = array(
            array('class_name' => 'W3_Plugin_TotalCache', 'enable_options' => null),
            array('class_name' => 'W3_Plugin_DbCache', 'enable_options' => 'dbcache.enabled'),
            array('class_name' => 'W3_Plugin_ObjectCache', 'enable_options' => 'objectcache.enabled'),
            array('class_name' => 'W3_Pro_Plugin_FragmentCache', 'enable_options' => 'fragmentcache.enabled'),
            array('class_name' => 'W3_Plugin_PgCache', 'enable_options' => 'pgcache.enabled'),
            array('class_name' => 'W3_Plugin_Cdn', 'enable_options' => 'cdn.enabled'),
            array('class_name' => 'W3_Plugin_BrowserCache', 'enable_options' => 'browsercache.enabled'),
            array('class_name' => 'W3_Plugin_Minify', 'enable_options' => 'minify.enabled'),
            array('class_name' => 'W3_Plugin_Varnish', 'enable_options' => 'varnish.enabled'),
            array('class_name' => 'W3_Plugin_NewRelic', 'enable_options' => 'newrelic.enabled')
        );

        if (w3tc_edge_mode() && (w3_is_pro($this->_config) || w3_is_enterprise()) && w3tc_cdn_supports_realtime_purge($this->_config->get_string('cdn.engine'))) {
            $this->_plugins[] = array('class_name' => 'W3_Plugin_CdnCache', 'enable_options' => array('cdn.enabled', 'cdncache.enabled'));
        }
        if (is_admin()) {
            $this->_plugins[] = array('class_name' => 'W3_Plugin_TotalCacheAdmin', 'enable_options' => null);
            $this->_plugins[] = array('class_name' => 'W3_Plugin_PgCacheAdmin', 'enable_options' => 'pgcache.enabled');
            $this->_plugins[] = array('class_name' => 'W3_Plugin_MinifyAdmin', 'enable_options' => array('minify.enabled', 'minify.auto'));
            $this->_plugins[] = array('class_name' => 'W3_Plugin_NewRelicAdmin', 'enable_options' => null);
            $this->_plugins[] = array('class_name' => 'W3_Widget_Services', 'enable_options' => null);
            $this->_plugins[] = array('class_name' => 'W3_Widget_SpreadTheWord', 'enable_options' => null);
            $this->_plugins[] = array('class_name' => 'W3_Widget_News', 'enable_options' => null);
            $this->_plugins[] = array('class_name' => 'W3_Widget_Forum', 'enable_options' => null);
            $this->_plugins[] = array('class_name' => 'W3_Widget_MaxCDN', 'enable_options' => array(array('cdn.engine', '==', 'maxcdn'),'||', array('cdn.engine', '!=', 'netdna')));
            $this->_plugins[] = array('class_name' => 'W3_Widget_NetDNA', 'enable_options' => array(array('cdn.engine', '==', 'netdna')));
            $this->_plugins[] = array('class_name' => 'W3_Widget_NewRelic', 'enable_options' => null);
            $this->_plugins[] = array('class_name' => 'W3_Widget_PageSpeed', 'enable_options' => 'widget.pagespeed.enabled');
            $this->_plugins[] = array('class_name' => 'W3_AdminCompatibility', 'enable_options' => null);
            if (!(defined('W3TC_PRO') || w3_is_enterprise()))
                $this->_plugins[] = array('class_name' => 'W3_Licensing', 'enable_options' => null);
            $this->_plugins[] = array('class_name' => 'W3_Plugin_DefaultSettings', 'enable_options' => null);
            $this->_plugins[] = array('class_name' => 'W3_GeneralActions', 'enable_options' => array('pgcache.enabled','||', 'varnish.enabled','||', array('cdn.enabled', 'cdncache.enabled')));
            $this->_plugins[] = array('class_name' => 'W3_Plugin_ExtensionsAdmin', 'enable_options' => null);
            $this->_plugins[] = array('class_name' => 'W3_Plugin_NotificationsAdmin', 'enable_options' => null);
        }

        $this->_load_plugins();

        register_activation_hook(W3TC_FILE, array(
            &$this,
            'activate'
        ));

        register_deactivation_hook(W3TC_FILE, array(
            &$this,
            'deactivate'
        ));
    }

    /**
     * Run plugins
     */
    function run() {
        foreach ($this->_loaded_plugins as $plugin) {
            $plugin->run();
        }
        add_action('init', array($this, 'load_extensions'));
    }

    /**
     * Activation action hook
     */
    public function activate($network_wide) {
        $activation = w3_instance('W3_RootAdminActivation');
        $activation->activate($network_wide);
    }

    /**
     * Deactivation action hook
     */
    public function deactivate() {
        $activation = w3_instance('W3_RootAdminActivation');
        $activation->deactivate();
    }

    /**
     * Instantiate all plugins
     */
    private function _load_plugins() {
        foreach ($this->_plugins as $plugin) {
            $this->_load_plugin($plugin);
        }
    }

    /**
     * Instantiate plugin
     * @param $plugin_descriptor array('class_name' => '', 'enable_options' => '')
     */
    private function _load_plugin($plugin_descriptor) {
        $criteria = $plugin_descriptor['enable_options'];

        if ($this->_criteria_matched($criteria)) {
            $plugin = w3_instance($plugin_descriptor['class_name']);
            $this->_loaded_plugins[] = $plugin;
        }
    }

    private function _criteria_matched($criteria) {
        if (is_array($criteria)){
            $enabled = true;
            $compare = '&&';
            foreach ($criteria as $val) {
                if (is_array($val)){
                    if ($val[1] == '!=') {
                        $enabled = $this->_compare_criteria_values($enabled, $this->_config->get_string($val[0]) != $val[2], $compare);
                    } elseif ($val[1] == '==') {
                        $enabled = $this->_compare_criteria_values($enabled, $this->_config->get_string($val[0]) == $val[2], $compare);
                    } else {
                        $enabled = $enabled || $this->_criteria_matched($val);
                    }
                } elseif ($val != '||' && $val != '&&'  ) {
                    $enabled = $this->_compare_criteria_values($enabled, $this->_config->get_boolean($val), $compare);
                } else
                    $compare = $val;
            }
        } else {
            $enabled = is_null($criteria) || $this->_config->get_boolean($criteria);
        }
        return $enabled;
    }

    private function _compare_criteria_values($val1, $val2, $compare) {
        if ($compare == '||') {
            return $val1 || $val2;
        }
        return $val1 && $val2;
    }


    /**
     * Loads extensions stored in config
     */
    function load_extensions() {
        $extensions = $this->_config->get_array('extensions.active');
        foreach($extensions as $extension => $path) {
            if (file_exists(W3TC_EXTENSION_DIR . '/' . trim($path, '/')))
                include W3TC_EXTENSION_DIR . '/' . trim($path, '/');
        }
    }
}
