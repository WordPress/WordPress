<?php
if (!defined('W3TC')) { die(); }

/**
 * Class W3_ConfigCompatibility
 */
class W3_ConfigCompatibility {

    /**
     * @var array
     */
    private $settings = array();
    /**
     * @var string
     */
    private $old_version = '';

    /**
     * Reads legacy config file
     * @param int $blog_id
     * @param bool $force_master
     * @return array
     */
    public function get_imported_legacy_config_keys($blog_id, $force_master = false) {
        $suffix = '';

        if ($force_master) {

        } else if ($blog_id > 0) {
            if (w3_is_network()) {
                if (w3_is_subdomain_install())
                    $suffix = '-' . w3_get_domain(w3_get_host());
                else {
                    // try subdir blog
                    $request_uri = rtrim($_SERVER['REQUEST_URI'], '/');
                    $site_home_uri = w3_get_base_path();

                    if (substr($request_uri, 0, strlen($site_home_uri)) == $site_home_uri) {
                        $request_path_in_wp = '/' . substr($request_uri, strlen($site_home_uri));

                        $n = strpos($request_path_in_wp, '/', 1);
                        if ($n === false)
                            $blog_path_in_wp = substr($request_path_in_wp, 1);
                        else
                            $blog_path_in_wp = substr($request_path_in_wp, 1, $n - 1);

                        $suffix = '-' . ($blog_path_in_wp != 'wp-admin'? $blog_path_in_wp . '.': '') . w3_get_domain(w3_get_host());
                    }

                }
            }
        }

        $filename = WP_CONTENT_DIR . '/w3-total-cache-config' . $suffix . '.php';

        $legacy_config = W3_ConfigData::get_array_from_file($filename);
        if (is_array($legacy_config) &&
            isset($legacy_config['pgcache.engine']) &&
            $legacy_config['pgcache.engine'] == 'file_pgcache')
            $legacy_config['pgcache.engine'] = 'file_generic';
        return $legacy_config;
    }

    /**
     * Setups new settings
     *
     * @return array
     */
    public function get_new_settings() {
        $data = $this->_get_new_settings();
        if (empty($data))
            return array();

        list($current_version_default_configuration, $current_version_sealing_keys_scope) = $this->get_current_default_configuration_keys();
        $modules = array();
        $modules['general'] = array();
        foreach ($current_version_sealing_keys_scope as $scope) {
            $a = explode('.', $scope['key']);
            $module = array_shift($a);
            $modules[$module] = array();
        }
        $modules['monitoring'] = array();
        $modules['mobile'] = array();
        $modules['referrer'] = array();

        foreach ($data as $key => $value) {
            $a = explode('.', $key);
            $module = array_shift($a);
            if ('newrelic' == $module)
                $module = 'monitoring';
            $config_meta = w3_config_meta($key);
            if (array_key_exists($module, $modules) && $config_meta['area'] == 'settings') {
                $modules[$module][] = array('key' => $key, 'meta' => $value);
            } else {
                $modules['general'][] = array('key' => $key, 'meta' => $value);
            }
        }
        $menus = new W3_Menus();
        $menus = $menus->generate_menu_array();
        $new_settings = array();
        foreach ($modules as $module => $settings) {
            if ($settings) {
                $name = ucfirst($module);
                $page = '';
                foreach ($menus as $link => $meta) {
                    if (strpos($link, $module) !== false) {
                        $name = $meta[0];
                        $page = $link;
                    }
                }
                $new_settings[] = array('page' => $page, 'name' => $name, 'data' => $settings);
            }
        }
        return $new_settings;
    }

    /**
     * @param bool $network
     * @return mixed|void
     */
    private function _get_new_settings($network = false) {
        $post_fix = '_single';
        if (is_network_admin() || $network)
            $post_fix = '_network';
        return get_option('w3tc_new_settings' . $post_fix, array());
    }

    /**
     * @param $old_version_number
     */
    public function load_new_settings($old_version_number) {
        if (empty($this->old_version)) {
            $this->old_version = $old_version_number;
            list($current_version_default_configuration, $current_version_sealing_keys_scope) = $this->get_current_default_configuration_keys();
            $this->settings = $this->find_new_settings($current_version_default_configuration, $old_version_number);
        }
    }

    /**
     * Stores the internal values of version and changed setting in options.
     * w3tc_old_version_single and w3tc_old_version_network if in network admin
     * w3tc_new_settings_single and w3tc_new_settings_network if in network admin
     */
    public function setup_settings() {
        if ($this->old_version)
            $this->set_old_version($this->old_version);
        $post_fix = '_single';
        if (is_network_admin())
            $post_fix = '_network';
        if (!(get_option('w3tc_new_settings' . $post_fix))) {
            if ($this->settings) {
                update_option('w3tc_new_settings' . $post_fix, $this->settings);
            } else {
                delete_option('w3tc_new_settings' . $post_fix);
                delete_option('w3tc_old_version' . $post_fix);
            }
        }
    }

    /**
     * @param string $old_version_number
     * @return array(keys, scope)
     */
    private function get_previous_version_default_configuration($old_version_number) {
        /**
         * @var array $keys
         * @var array $sealing_keys_scope
         */
        include W3TC_DIR . '/configs/' . $old_version_number . '-ConfigKeys.php';
        $old_default_configuration_keys = $keys;
        $old_sealing_keys_scope = $sealing_keys_scope;
        return array($old_default_configuration_keys, $old_sealing_keys_scope);
    }

    /**
     * @return array($current_version_keys, $current_version_sealing_keys_scope)
     */
    private function get_current_default_configuration_keys() {
        /**
         * defines default $keys with descriptors
         * @var array $keys config keys
         * @var array $sealing_keys_scope config keys
         */
        include W3TC_LIB_W3_DIR . '/ConfigKeys.php';
        $current_version_keys = $keys;
        $current_version_sealing_keys_scope = $sealing_keys_scope;
        return array($current_version_keys, $current_version_sealing_keys_scope);
    }

    /**
     * @return array
     */
    private function get_current_configuration() {
        $old_config = W3_ConfigWriter::get_config_filename();
        $old_configuration_keys = include $old_config;
        return $old_configuration_keys;
    }

    /**
     * @param $current_version_default_configuration
     * @param $old_default_configuration
     * @return array
     */
    private function get_changed_default_configs($current_version_default_configuration, $old_default_configuration) {
        $defaults_changed = array();
        foreach ($current_version_default_configuration as $key => $meta) {
            foreach ($old_default_configuration as $oKey => $oMeta) {
                if ($oKey == $key && $meta['default'] != $oMeta['default'])
                    $defaults_changed[$key] = array('old' => $oMeta, 'new' => $meta);
            }
        }
        return $defaults_changed;
    }

    /**
     * Returns true if config section is sealed
     * @param string $section
     * @param W3_Config $config_master
     * @param W3_ConfigAdmin $config_admin
     * @return boolean
     */
    private function is_sealed($section, $config_master, $config_admin) {
        if (w3_get_blog_id() == 0)
            return false;
        if (w3_is_network() && w3_get_blog_id() !=0 && w3_force_master())
            return true;
        // browsercache settings change rules, so not available in child settings
        if ($section == 'browsercache')
            return true;

        if ($section == 'minify' && !$config_master->get_boolean('minify.enabled'))
            return true;

        return $config_admin->get_boolean($section . '.configuration_sealed');
    }

    /**
     * @param array $current_version_default_configuration
     * @param string $old_version_number
     * @return array
     */
    private function find_new_settings($current_version_default_configuration, $old_version_number) {
        w3_require_once(W3TC_INC_FUNCTIONS_DIR . '/admin_ui.php');

        $current_configuration = $this->get_current_configuration();
        list($old_default_configuration, $old_sealing_keys_scope) = $this->get_previous_version_default_configuration($old_version_number);

        $changed_data = array();
        $defaults_changed = $this->get_changed_default_configs($current_version_default_configuration, $old_default_configuration);
        $config_master = new W3_Config(true);
        $config_admin = new W3_ConfigAdmin();
        foreach ($defaults_changed as $key => $meta) {
            $a = explode('.', $key);
            if ($this->is_sealed(array_shift($a), $config_master, $config_admin))
                continue;
            if (isset($current_configuration[$key])) {
                $current_conf_value = $current_configuration[$key];
                if ($current_conf_value == $meta['old']['default'] &&
                    $current_conf_value != $meta['new']['default'] &&
                    w3_config_can_change($key, $meta)
                ) {
                    $changed_data[$key] = array('value' => $meta['new']['default'], 'state' => 'changed');
                }
            } else {
                if (w3_config_can_change($key, $meta))
                    $changed_data[$key] = array('value' => $meta['new']['default'], 'state' => 'new');
            }
        }
        return $changed_data;
    }

    /**
     * Returns the older version used on install if the plugin has been recently updated
     * @return string
     */
    public function get_old_version() {
        $post_fix = '_single';
        if (is_network_admin())
            $post_fix = '_network';
        if ($version = get_option('w3tc_old_version' . $post_fix, ''))
            return $version;
        return $this->old_version;
    }

    public function set_old_version($version) {
        $post_fix = '_single';
        if (is_network_admin())
            $post_fix = '_network';
        update_option('w3tc_old_version' . $post_fix, $version);
    }
}