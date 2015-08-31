<?php

/**
 * W3 Total Cache plugin
 */
if (!defined('W3TC')) {
    die();
}

w3_require_once(W3TC_INC_DIR . '/functions/activation.php');
w3_require_once(W3TC_INC_DIR . '/functions/file.php');
w3_require_once(W3TC_LIB_W3_DIR . '/Plugin.php');

/**
 * Class W3_Plugin_TotalCacheActivation
 */
class W3_RootAdminActivation {
    /**
     * Activate plugin action
     *
     * @param bool $network_wide
     * @return void
     */
    function activate($network_wide) {
        w3_require_once(W3TC_INC_DIR . '/functions/activation.php');

        // decline non-network activation at WPMU
        if (w3_is_network()) {
            if ($network_wide) {
                // we are in network activation
            } else if ($_GET['action'] == 'error_scrape' && 
                    strpos($_SERVER['REQUEST_URI'], '/network/') !== false) {
                // workaround for error_scrape page called after error
                // really we are in network activation and going to throw some error
            } else {
                echo 'Please <a href="' . network_admin_url('plugins.php') . '">network activate</a> W3 Total Cache when using WordPress Multisite.';
                die;
            }
        }

        try {
            $e = w3_instance('W3_AdminEnvironment');
            /**
             * @var W3_Config $config
             */
            $config = w3_instance('W3_Config');
            $e->fix_on_event($config, 'activate');

            w3_instance('W3_AdminLinks')->link_update($config);

            // try to save config file if needed, optional thing so exceptions 
            // hidden
            if (!$config->own_config_exists()) {
                try {
                    // create folders
                    $e->fix_in_wpadmin($config);
                } catch (Exception $ex) {
                }

                try {
                    $config_admin = w3_instance('W3_ConfigAdmin');
                    w3_require_once(W3TC_INC_FUNCTIONS_DIR . '/admin.php');
                    w3_config_save(w3_instance('W3_Config'), $config, $config_admin);
                } catch (Exception $ex) {
                }
            }
        } catch (Exception $e) {
            w3_activation_error_on_exception($e);
        }
    }

    /**
     * Deactivate plugin action
     *
     * @return void
     */
    function deactivate() {
        try {
            w3_enable_maintenance_mode();
        } catch(Exception $ex) {
        }

        try {
            $e = w3_instance('W3_AdminEnvironment');
            $config = w3_instance('W3_Config');
            $e->fix_after_deactivation($config);

            w3_instance('W3_AdminLinks')->link_delete();
        } catch (SelfTestExceptions $exs) {
            $r = w3_parse_selftest_exceptions($exs);

            if (strlen($r['required_changes']) > 0) {
                $changes_style = 'border: 1px solid black; ' .
                    'background: white; ' .
                    'margin: 10px 30px 10px 30px; ' . 
                    'padding: 10px;';

                $error = '<strong>W3 Total Cache Error:</strong> ' .
                    'Files and directories could not be automatically ' .
                    'removed to complete the deactivation. ' .
                    '<br />Please execute commands manually:<br />' .
                    '<div style="' . $changes_style . '">' . 
                    $r['required_changes'] . '</div>';

                // this is not shown since wp redirects from that page
                // not solved now
                echo '<div class="error"><p>' . $error . '</p></div>';
            }
        }

        try {
            w3_disable_maintenance_mode();
        } catch(Exception $ex) {
        }
    }
}
