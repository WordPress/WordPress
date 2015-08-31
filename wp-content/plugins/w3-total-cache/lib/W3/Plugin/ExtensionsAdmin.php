<?php

/**
 * W3 Total Cache ExtensionsAdmin plugin
 */
if (!defined('W3TC')) {
    die();
}

w3_require_once(W3TC_LIB_W3_DIR . '/Plugin.php');

class W3_Plugin_ExtensionsAdmin extends W3_Plugin {


    public function run() {
        add_filter('w3tc_menu', array($this, 'menu'));
        add_action('w3tc_menu-w3tc_extensions', array($this, 'options'));

        add_action('after_setup_theme', array(
            $this, 
            'maybe_deactivate_unsupported_extensions'));

        add_action('w3tc_saved_options', array(
            $this, 
            'on_saved_options'), 10, 2);
        w3_require_once(W3TC_INC_FUNCTIONS_DIR . '/admin.php');
        if (is_w3tc_admin_page()) {

            w3_require_once(W3TC_INC_FUNCTIONS_DIR . '/extensions.php');
            w3_extensions_admin_init();
            if (isset($_GET['extension']) && isset($_GET['action'])) {
                if (in_array($_GET['action'], array('activate', 'deactivate'))) {
                    add_action('init', array($this, 'change_extension_status'));
                }
            } elseif (isset($_POST['checked'])) {
                add_action('init', array($this, 'change_extensions_status'));
            }
        }
        add_action('w3tc_hide_button_custom-hide-extension-notification', array($this, 'hide_notification'));
    }

    /**
     * Hides an extension notification
     */
    public function hide_notification() {
        $extension = W3_Request::get_string('w3tc_default_hide_note_custom');
        /**
         * @var W3_ConfigAdmin $w3_config_admin
         */
        $w3_config_admin = w3_instance('W3_ConfigAdmin');
        $exts = $w3_config_admin->get_array('notes.hide_extensions');
        $exts[] = $extension;
        $exts = array_unique($exts);
        try {
            $w3_config_admin->set('notes.hide_extensions', $exts);
            $w3_config_admin->save();
        } catch (Exception $ex) {}
    }

    /**
     * Adds menu
     *
     * @param $menu
     * @return array
     */
    public function menu($menu) {
        $menu_item = array(
            'w3tc_extensions' => array(
                __('Extensions', 'w3-total-cache'),
                __('Extensions', 'w3-total-cache'),
                'network_show' => false
            )
        );
        return array_merge($menu, $menu_item);
    }

    /**
     * Loads options page and corresponding view
     */
    public function options() {
        /**
         * @var W3_UI_ExtensionsAdminView $options_dashboard
         */
        $options_dashboard = w3_instance('W3_UI_ExtensionsAdminView');
        $options_dashboard->options();
    }


    /**
     * Alters the active state of multiple extensions
     */
    public function change_extensions_status() {
        w3_require_once(W3TC_INC_FUNCTIONS_DIR . '/ui.php');
        $extensions = W3_Request::get_array('checked');
        $action = W3_Request::get('action');
        $message = '';
        if ('activate-selected' == $action) {
            foreach ($extensions as $extension) {
                if (w3tc_activate_extension($extension, $this->_config))
                    $message .= '&activated=' . $extension;
            }
            wp_redirect(w3_admin_url(sprintf('admin.php?page=w3tc_extensions%s', $message)));
        } elseif ('deactivate-selected' == $action) {
            foreach ($extensions as $extension) {
                if (w3tc_deactivate_extension($extension, $this->_config))
                    $message .= '&deactivated=' . $extension;
            }
            wp_redirect(w3_admin_url(sprintf('admin.php?page=w3tc_extensions%s', $message)));
        } else {
            wp_redirect(w3_admin_url('admin.php?page=w3tc_extensions'));
        }
    }

    /**
     * Alters the active state of an extension
     */
    public function change_extension_status() {
        $action = W3_Request::get_string('action');

        if (in_array($action, array('activate', 'deactivate'))) {
            w3_require_once(W3TC_INC_FUNCTIONS_DIR . '/ui.php');

            $extension = W3_Request::get_string('extension');
            if ('activate' == $action) {
                w3tc_activate_extension($extension, $this->_config);
                wp_redirect(w3_admin_url(sprintf('admin.php?page=w3tc_extensions&activated=%s', $extension)));
            } elseif ('deactivate' == $action) {
                w3tc_deactivate_extension($extension, $this->_config);
                wp_redirect(w3_admin_url(sprintf('admin.php?page=w3tc_extensions&deactivated=%s', $extension)));
            }
        }
    }

    /**
     * Maybe deactivates extensions which cannt be active according to current settings
     */
    public function maybe_deactivate_unsupported_extensions() {
        if (isset($_GET['activated']))   // page loaded after theme switch
            $this->deactivate_unsupported_extensions($this->_config);
    }

    /**
     * Saved options
     */
    public function on_saved_options($config, $admin_config) {
        $this->deactivate_unsupported_extensions($config, true);
    }

    /**
     * Deactivates extensions which cannt be active according to current settings
     */
    public function deactivate_unsupported_extensions($config, $dont_save_config = false) {
        w3_require_once(W3TC_INC_FUNCTIONS_DIR . '/extensions.php');
        
        w3_extensions_admin_init();
        $all_extensions = w3_get_extensions($config);

        foreach ($all_extensions as $name => $descriptor) {
            if (isset($descriptor['enabled']) && !$descriptor['enabled']) {
                w3tc_deactivate_extension($name, $config, $dont_save_config);
            }
        }
    }
}
