<?php
if (!defined('W3TC')) {
    die();
}
w3_require_once(W3TC_LIB_W3_DIR . '/Plugin.php');

class W3_Licensing extends W3_Plugin {
    private $site_inactivated = false;
    private $site_activated = false;
    /**
     * Setup init actions
     */
    function run() {
        add_action('admin_init', array($this, 'admin_init'));
        add_action('wp_ajax_w3tc_verify_plugin_license_key', array($this, 'action_verify_plugin_license_key'));
        add_action("w3tc_saving_options-w3tc_general", array($this, 'possible_state_change'), 2, 10);

    }

    /**
     * @param W3_Config $config
     * @param W3_Config $old_config
     */
    function possible_state_change($config, $old_config) {
        if ($old_config->get_string('plugin.license_key') !='' &&  $config->get_string('plugin.license_key') == '') {
            $result = edd_w3edge_w3tc_deactivate_license($old_config->get_string('plugin.license_key'));
            if ($result) {
                $this->site_inactivated = true;
            }
            delete_transient('w3tc_license_status');
        } else if($old_config->get_string('plugin.license_key') =='' &&  $config->get_string('plugin.license_key') != '') {
            $result = edd_w3edge_w3tc_activate_license($config->get_string('plugin.license_key'), W3TC_VERSION);
            if ($result) {
                $this->site_activated = true;
            }
            delete_transient('w3tc_license_status');
        } else if($old_config->get_string('plugin.license_key') != $config->get_string('plugin.license_key')) {
            $result = edd_w3edge_w3tc_activate_license($config->get_string('plugin.license_key'), W3TC_VERSION);
            if ($result) {
                $this->site_activated = true;
            }
            delete_transient('w3tc_license_status');
        }
    }

    /**
     * Setup notices actions
     */
    function admin_init() {
        if (current_user_can('manage_options')) {
            w3_require_once(W3TC_INC_FUNCTIONS_DIR . '/admin.php');
            if (is_admin() && is_w3tc_admin_page()) {
                /**
                 * Only admin can see W3TC notices and errors
                 */
                if (!w3_is_multisite()) {
                    add_action('admin_notices', array(
                        &$this,
                        'admin_notices'
                    ));
                }
                add_action('network_admin_notices', array(
                    &$this,
                    'admin_notices'
                ));
            }
        }
    }

    /**
     * Run license status check and display messages
     */
    function admin_notices() {
        $message = '';
        $status = get_transient('w3tc_license_status');
        $set_transient = false;
        if (!$status) {
            $status = $this->update_license_status();
            $set_transient = true;
            $transient_timeout = 3600 * 24 * 5;
        }

        w3_require_once(W3TC_INC_FUNCTIONS_DIR . '/ui.php');
        switch($status) {
            case 'expired':
                $message = sprintf(__('The W3 Total Cache license key has expired. Please renew it: %s', 'w3-total-cache'),
                    '<input type="button" class="button-primary button-buy-plugin {nonce: \''. wp_create_nonce('w3tc').'\'}" value="'.__('Renew', 'w3-total-cache') . '" />' );
                break;
            case 'invalid':
                $message = __('The W3 Total Cache license key you entered is not valid.', 'w3-total-cache') .
                '<a href="' . (is_network_admin() ? network_admin_url('admin.php?page=w3tc_general#licensing'):
                    admin_url('admin.php?page=w3tc_general#licensing')) . '"> ' . __('Please enter it again.', 'w3-total-cache') . '</a>';
                break;
            case 'inactive':
                $message = __('The W3 Total Cache license key is not active.', 'w3-total-cache');
                break;
            case 'site_inactive':
                $message = __('The W3 Total Cache license key is not active for this site.', 'w3-total-cache');
                break;
            case 'valid':
                break;
            case 'host_valid':
                break;
            case 'no_key':
                break;
            default:
                $message = __('The W3 Total Cache license key can\'t be verified.', 'w3-total-cache');
                $transient_timeout = 60;
                break;
        }

        if ($set_transient) {
            set_transient('w3tc_license_status', $status, $transient_timeout);
        }

        if ($message)
            w3_e_error_box(sprintf("<p>$message. <a href='%s'>" . __('check again') . '</a></p>', 
                network_admin_url('admin.php?page=w3tc_general&w3tc_licensing_check_key'))
                );


        if ($this->site_inactivated) {
            w3_e_error_box("<p>" . __('The W3 Total Cache license key is deactivated for this site.', 'w3-total-cache') ."</p>");
        }

        if ($this->site_activated) {
            w3_e_error_box("<p>" . __('The W3 Total Cache license key is activated for this site.', 'w3-total-cache') ."</p>");
        }
    }

    /**
     * @return string
     */
    function update_license_status() {
        $status = '';
        $license_key = $this->get_license_key();

        if (!empty($license_key) || defined('W3TC_LICENSE_CHECK')) {
            $license = edd_w3edge_w3tc_check_license($license_key, W3TC_VERSION);
            $version = '';

            if ($license) {
                $status = $license->license;
                if (in_array($status, array('valid', 'host_valid'))) {
                    $version = 'pro';
                } elseif (in_array($status, array('site_inactive','valid')) && w3tc_is_pro_dev_mode()) {
                    $status = 'valid';
                    $version = 'pro_dev';
                }
            }

            $this->_config->set('plugin.type', $version);
        } else {
            $status = 'no_key';
            $this->_config->set('plugin.type', '');
        }
        try {
            $this->_config->save();
        } catch(Exception $ex) {}
        return $status;
    }

    /**
     * @return string
     */
    function get_license_key() {
        $license_key = $this->_config->get_string('plugin.license_key', '');
        if ($license_key == '')
            $license_key = ini_get('w3tc.license_key');
        return $license_key;
    }

    function action_verify_plugin_license_key() {
        w3_require_once(W3TC_LIB_W3_DIR . '/Request.php');

        $license = W3_Request::get_string('license_key', '');

        if ($license) {
            $status = edd_w3edge_w3tc_verify_license($license, W3TC_VERSION);
            echo $status->license;
        } else {
            echo 'invalid';
        }
        exit;
    }
}
