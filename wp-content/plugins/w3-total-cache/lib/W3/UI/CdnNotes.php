<?php
/**
 * Created by JetBrains PhpStorm.
 * User: cyonite
 * Date: 6/24/13
 * Time: 11:23 AM
 * To change this template use File | Settings | File Templates.
 */

class W3_UI_CdnNotes {
    /**
     * @param W3_Config $config
     * @param W3_ConfigAdmin|null $config_admin
     * @return array
     */
    function notifications($config, $config_admin) {
        w3_require_once(W3TC_LIB_W3_DIR . '/Request.php');
        $page = W3_Request::get_string('page');

        $notes = array();

        if ( !w3_is_cdn_mirror($config->get_string('cdn.engine'))) {
            /**
             * Show notification after theme change
             */
            if ($config->get_boolean('notes.theme_changed')) {
                $notes[] = sprintf(__('The active theme has changed, please %s now to ensure proper operation. %s', 'w3-total-cache'), w3_button_popup(__('upload active theme files', 'w3-total-cache'), 'cdn_export', 'cdn_export_type=theme'), w3_button_hide_note(__('Hide this message', 'w3-total-cache'), 'theme_changed'));
            }

            /**
             * Show notification after WP upgrade
             */
            if ($config->get_boolean('notes.wp_upgraded')) {
                $notes[] = sprintf(__('Upgraded WordPress? Please %s files now to ensure proper operation. %s', 'w3-total-cache'), w3_button_popup('upload wp-includes', 'cdn_export', 'cdn_export_type=includes'), w3_button_hide_note(__('Hide this message', 'w3-total-cache'), 'wp_upgraded'));
            }

            /**
             * Show notification after CDN enable
             */
            if ($config->get_boolean('notes.cdn_upload') || $config->get_boolean('notes.cdn_reupload')) {
                $cdn_upload_buttons = array();

                if ($config->get_boolean('cdn.includes.enable')) {
                    $cdn_upload_buttons[] = w3_button_popup('wp-includes', 'cdn_export', 'cdn_export_type=includes');
                }

                if ($config->get_boolean('cdn.theme.enable')) {
                    $cdn_upload_buttons[] = w3_button_popup('theme files', 'cdn_export', 'cdn_export_type=theme');
                }

                if ($config->get_boolean('minify.enabled') && $config->get_boolean('cdn.minify.enable') &&
                    !$config->get_boolean('minify.auto')) {
                    $cdn_upload_buttons[] = w3_button_popup('minify files', 'cdn_export', 'cdn_export_type=minify');
                }

                if ($config->get_boolean('cdn.custom.enable')) {
                    $cdn_upload_buttons[] = w3_button_popup('custom files', 'cdn_export', 'cdn_export_type=custom');
                }

                if ($config->get_boolean('notes.cdn_upload')) {
                    $notes[] = sprintf(__('Make sure to %s and upload the %s, files to the <acronym title="Content Delivery Network">CDN</acronym> to ensure proper operation. %s', 'w3-total-cache'), w3_button_popup('export the media library', 'cdn_export_library'), implode(', ', $cdn_upload_buttons), w3_button_hide_note('Hide this message', 'cdn_upload'));
                }

                if ($config->get_boolean('notes.cdn_reupload')) {
                    $notes[] = sprintf(__('Settings that affect Browser Cache settings for files hosted by the CDN have been changed. To apply the new settings %s and %s. %s', 'w3-total-cache'), w3_button_popup(__('export the media library', 'w3-total-cache'), 'cdn_export_library'), implode(', ', $cdn_upload_buttons), w3_button_hide_note(__('Hide this message', 'w3-total-cache'), 'cdn_reupload'));
                }
            }
        }

        if (in_array($config->get_string('cdn.engine'), array('netdna', 'maxcdn')) &&
            $config_admin->get_boolean('notes.maxcdn_whitelist_ip') &&
            $config_admin->get_integer('track.maxcdn_authorize') == 0 &&
            $config->get_string('cdn.' . $config->get_string('cdn.engine') .'.authorization_key')) {
            $notes[] = sprintf(__('Make sure to whitelist your servers IPs. Follow the instructions on %s. The IP for this server is %s. %s', 'w3-total-cache'), '<a href="http://support.maxcdn.com/tutorials/how-to-whitelist-your-server-ip-to-use-the-api/">MaxCDN</a>', $_SERVER['SERVER_ADDR'],w3_button_hide_note('Hide this message', 'maxcdn_whitelist_ip', '', true));
        }
        return $notes;
    }

    function errors() {
        $errors = array();
        /**
         * Show notification if upload queue is not empty
         */
        try {
            if (!($error = get_transient('w3tc_cdn_error')) && !$this->_is_queue_empty()) {
                $errors[] = sprintf(__('The %s has unresolved errors. Empty the queue to restore normal operation.', 'w3-total-cache'), w3_button_popup(__('unsuccessful transfer queue', 'w3-total-cache'), 'cdn_queue'));
            } elseif ($error) {
                $errors[] = $error;
            }
        } catch(Exception $ex) {
            $errors[] = $ex->getMessage();
            set_transient('w3tc_cdn_error', $ex->getMessage(), 30);
        }
        return $errors;
    }


    /**
     * Returns true if upload queue is empty
     *
     * @return bool
     * @throws Exception
     */
    private function _is_queue_empty() {
        global $wpdb;
        $wpdb->hide_errors();
        $sql = sprintf('SELECT COUNT(*) FROM %s', $wpdb->prefix . W3TC_CDN_TABLE_QUEUE);
        $result = $wpdb->get_var($sql);
        if (($error = $wpdb->last_error)) {
            if (strpos($error, "doesn't exist") !== false) {
                $url = is_network_admin() ? network_admin_url('admin.php?page=w3tc_install') : admin_url('admin.php?page=w3tc_install');
                throw new Exception(sprintf(
                    __('Encountered issue with CDN: %s. See %s for instructions of creating correct table.', 'w3-total-cache'),
                    $wpdb->last_error,
                    '<a href="' . $url . '">' . __('Install page', 'w3-total-cache') . '</a>'));
            }
            else
                throw new Exception(sprintf(__('Encountered issue with CDN: %s.', 'w3-total-cache'), $wpdb->last_error));
        }
        return ($result == 0);
    }
}
