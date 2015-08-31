<?php
if (!defined('W3TC')) { die(); }

/**
 * Class W3_Plugin_DefaultSettings
 */
class W3_Plugin_DefaultSettings {
    private $new_settings = array();

    /**
     * Setups actions
     */
    public function run() {
        add_action('admin_init', array($this, 'admin_init'));
        add_action('wp_ajax_w3tc_change_setting', array($this, 'change_setting'));
    }

    /**
     * Setups actions
     */
    public function admin_init() {
        if(current_user_can('manage_options')) {
            /**
             * @var W3_ConfigCompatibility $config_comp
             */
            $config_comp = w3_instance('W3_ConfigCompatibility');
            if ($config_comp->get_old_version() && isset($_GET['page'])) {
                w3_require_once(W3TC_INC_FUNCTIONS_DIR .'/admin.php');
                if (is_w3tc_admin_page() || 
                        (isset($GLOBALS['pagenow']) && 
                            'plugins.php' === $GLOBALS['pagenow'])) {
                    if (!w3_is_multisite() || !w3_force_master())
                        add_action('admin_notices', 
                            array($this, 'admin_notices'));
                    else
                        add_action('network_admin_notices', 
                            array($this, 'admin_notices'));
                }
                $config_comp->setup_settings();
            }
        }
    }

    public function change_setting() {
        if(current_user_can('manage_options')) {
            $post_fix = '_single';
            $success = true;
            $network_admin = W3_Request::get_string('network');
            if ($network_admin)
                $post_fix = '_network';

            $data = get_option('w3tc_new_settings' . $post_fix);
            $key = W3_Request::get_string('setting');

            if ($key != 'all' && !isset($data[$key])) {
                echo 'duplicate';
                exit;
            }
            $state = W3_Request::get_string('state');
            if ($network_admin) {
                $config = new W3_Config(true);
            } else {
                /**
                 * @var W3_Config $config
                 */
                $config = w3_instance('W3_Config');
            }

            if ($state == 'skip')
                unset($data[$key]);

            if (!in_array($state, array('dynamic', 'skip'))) {
                $meta = $data[$key];
                $config->set($key, $meta['value']);
                try {
                    $config->save();
                    unset($data[$key]);
                } catch (Exception $ex) {
                    $success = false;
                }
            } elseif ('all' == $key && 'dynamic' == $state) {
                foreach ($data as $key => $meta) {
                    $meta = $data[$key];
                    $config->set($key, $meta['value']);
                }
                try {
                    $config->save();
                    $data = array();
                } catch (Exception $ex) {
                    $success = false;
                }
            }

            if ($success) {
                if (sizeof($data)) {
                    update_option('w3tc_new_settings' . $post_fix, $data);
                    echo 'success';
                } else {
                    delete_option('w3tc_new_settings' . $post_fix);
                    delete_option('w3tc_old_version' . $post_fix);
                    echo 'done';
                }

            } else {
                echo 'failure';
            }
            exit;
        }
    }

    /**
     * Prints admin notices
     */
    public function admin_notices() {
        w3_require_once(W3TC_INC_FUNCTIONS_DIR .'/ui.php');
        /**
         * @var W3_ConfigCompatibility $config_comp
         */
        $config_comp = w3_instance('W3_ConfigCompatibility');
        $settings = $config_comp->get_new_settings();
        if (!$settings)
            return;

        $message = '<p>The following setting pages contain new options or configuration changes. Please take note of the following:</p>';
        $message .= '<p style="padding:0;margin:0;height:30px;text-align:right;">'
            . ' <input style="width:120px;" type="button" class="button" value="' . __('Apply all changes', 'w3-total-cache') . '" onclick="w3tc_change_setting(\'all\', \'dynamic\')" />'
            . '</p>';
        $message .= '<ul style="margin-top:0px;padding-top:0px;">';
        foreach ($settings as $module) {
            $page = $module['page'];
            $name = $module['name'];
            $message .= '<li>';
            if ($page)
                $message .='<a href="' . w3_admin_url('admin.php?page=' . $page) .'">'. $name . '</a>';
            else
                $message .= $name;
            $message .= '<ul>';
            foreach ($module['data'] as $setting) {
                $meta = $setting['meta'];
                $text =  'new' == $meta['state'] ? __('Append') : ('changed' == $meta['state'] ? __('Replace') : __('Remove'));
                $message .= '<li style="line-height:24px;" class="setting_changes ' . str_replace('.', '_', $setting['key']) .'">'
                    . trim(w3_config_label($setting['key'], ''), ':')
                    . '<div style="float:right;">'
                    . ' <input style="width:70px;margin-right:10px" type="button" class="button" value="' . $text . '" onclick="w3tc_change_setting(\'' . $setting['key'] .'\', \'' . $meta['state'] . '\', \''. is_network_admin() .'\')" />'
                    . ' <a href="#" style="margin-right:13px" onclick="w3tc_change_setting(\'' . $setting['key'] .'\', \'skip\', \''. is_network_admin() .'\')">Skip</a>'
                    . '</div>'
                    . '</li>';
            }
            $message .= '</ul>';
            $message .= '</li>';
        }
        $message .= '</ul>';
        $message .= '<p style="padding:0;margin:0;margin-top:20px;height:30px;text-align:right;">'
            . ' <input style="width:120px;" type="button" class="button" value="' . __('Apply all changes', 'w3-total-cache') . '" onclick="w3tc_change_setting(\'all\', \'dynamic\', \''. is_network_admin() .'\')" />'
            . '</p><p></p>';

        w3_e_notification_box($message, 'w3tc_new_settings');
    }
}
