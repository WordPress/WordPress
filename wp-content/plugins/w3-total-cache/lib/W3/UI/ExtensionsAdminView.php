<?php

if (!defined('W3TC')) {
    die();
}

w3_require_once(W3TC_LIB_W3_DIR . '/UI/PluginView.php');

/**
 * Class W3_UI_ExtensionsAdminView
 */
class W3_UI_ExtensionsAdminView extends W3_UI_PluginView {
    /**
     * Current page
     *
     * @var string
     */
    var $_page = 'w3tc_extensions';
    var $_active_tab;
    var $_config_settings = array();

    /**
     * Extensions view
     *
     * @return void
     */
    function view() {
        w3_require_once(W3TC_INC_FUNCTIONS_DIR . '/compat.php');
        w3_require_once(W3TC_INC_FUNCTIONS_DIR . '/ui.php');
        w3_require_once(W3TC_INC_FUNCTIONS_DIR . '/extensions.php');
        $extension = '';
        $extension_status = 'all';

        if (isset($_GET['extension_status'])) {
            if (in_array($_GET['extension_status'], array('all', 'active', 'inactive', 'core')))
                $extension_status = $_GET['extension_status'];
        }

        if (isset($_GET['extension'])) {
            $extension = $_GET['extension'];
        }

        $view = (isset($_GET['action']) && $_GET['action'] == 'view');

        $extensions_active = w3_get_active_extensions($this->_config);

        if($extension && $view) {
            $all_settings = $this->_config->get_array('extensions.settings');
            $extensions_active = w3_get_active_extensions($this->_config);
            $meta = $extensions_active[$extension];
            $sub_view = 'settings';
        } else {
            $extensions_all = w3_get_extensions($this->_config);
            $extensions_inactive = w3_get_inactive_extensions($this->_config);
            $var = "extensions_{$extension_status}";
            $extensions = $$var;
            $sub_view = 'list';
            $page = 1;
        }
        include W3TC_INC_OPTIONS_DIR . '/extensions.php';
    }

    /**
     * Returns
     *
     * @param $key
     * @param $extension
     * @return bool
     */
    function get_value($key, $extension){
        $keys = $this->get_config_settings($extension, $this->_config_settings);
        return isset($keys[$key]) ? $keys[$key] : false;
    }

    /**
     * Returns config settings for the extension
     *
     * @param $extension
     * @param $settings
     * @return bool
     */
    function get_config_settings($extension, $settings) {
        return isset($settings[$extension]) ? $settings[$extension] : false;
    }

    /**
     * Returns the name and id given the config group and setting
     * @param $group
     * @param $setting
     * @return array(name, id)
     */
    function get_name_and_id($group, $setting) {
        $name = "extensions.settings.$group.$setting";
        $id = str_replace('.','_', $name);
        return array($name, $id);
    }

    /**
     * Sets default values for lacking extension meta keys
     *
     * @param $meta
     * @return array
     */
    function default_meta($meta) {
        $default = array (
            'name' => '',
            'author' => '',
            'description' => '',
            'author uri' => '',
            'extension uri' => '',
            'extension id' => '',
            'version' => '',
            'enabled' => true,
            'requirements' => array(),
            'core' => false,
            'path' => ''
        );
        return array_merge($default, $meta);
    }

    /**
     * Prints checkbox with admin config option value
     *
     * @param string $option_id
     * @param string $extension
     * @param boolean $disabled
     */
    public function checkbox_admin_extensions($option_id, $extension, $disabled = false) {
        if (!$disabled)
            $disabled = $this->_config->get_boolean('common.force_master');
        $all_checked = $this->_config_admin->get_array($option_id);
        $checked = isset($all_checked[$extension]) && $all_checked[$extension];
        if (!$disabled)
            echo '<input type="hidden" name="' . $option_id . '['.$extension.']" value="0" />';

        echo '<label>';
        $id = str_replace('.', '_', $option_id);
        $class = $disabled ? 'disabled' : 'enabled';
        echo '<input id="' . $id . '_' . $extension . '" class="' . $class . '" type="checkbox" name="' . $option_id  . '['.$extension.']" value="1"';
        checked($checked, true);
        if ($disabled)
            echo ' disabled="disabled"';

        echo ' />';
    }
}
