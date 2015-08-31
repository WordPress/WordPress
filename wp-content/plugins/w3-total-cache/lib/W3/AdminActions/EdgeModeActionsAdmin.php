<?php
if (!defined('W3TC')) {
    die();
}
w3_require_once(W3TC_INC_FUNCTIONS_DIR . '/admin.php');

class W3_AdminActions_EdgeModeActionsAdmin {

    /**
     * @var W3_Config $_config
     */
    private $_config = null;

    /**
     * @var W3_ConfigAdmin
     */
    private $_config_admin = null;

    function __construct() {
        $this->_config = w3_instance('W3_Config');
        $this->_config_admin = w3_instance('W3_ConfigAdmin');
    }


    /**
     *
     */
    public function action_edge_mode_enable() {
        w3_require_once(W3TC_INC_FUNCTIONS_DIR . '/activation.php');
        $config_path = w3_get_wp_config_path();

        $config_data = @file_get_contents($config_path);
        if ($config_data === false)
            return;

        $new_config_data = $this->wp_config_evaluation_mode_remove_from_content($config_data);
        $new_config_data = preg_replace(
            '~<\?(php)?~',
            "\\0\r\n" . $this->wp_config_evaluation_mode(),
            $new_config_data,
            1);

        if ($new_config_data != $config_data) {
            try {
                w3_wp_write_to_file($config_path, $new_config_data);
            } catch (FilesystemOperationException $ex) {
                throw new Exception('Configuration file not writable. Please edit file <strong>' . $config_path .
                    '</strong> and add the next lines: '. $this->wp_config_evaluation_mode());
            }
            try {
                $this->_config_admin->set('notes.edge_mode', false);
                $this->_config_admin->save();
            } catch (Exception $ex) {}
        }
        w3_admin_redirect(array('w3tc_note' => 'enabled_edge'));
    }


    /**
     * @return string Addon required for plugin in wp-config
     **/
    private function wp_config_evaluation_mode() {
        return "/** Enable W3 Total Cache Edge Mode */\r\n" .
        "define('W3TC_EDGE_MODE', true); // Added by W3 Total Cache\r\n";
    }

    /**
     * Disables WP_CACHE
     *
     * @param string $config_data wp-config.php content
     * @return string
     * @throws FilesystemOperationException with S/FTP form if it can't get the required filesystem credentials
     * @throws FileOperationException
     */
    private function wp_config_evaluation_mode_remove_from_content($config_data) {
        $config_data = preg_replace(
            "~\\/\\*\\* Enable W3 Total Cache Edge Mode \\*\\*?\\/.*?\\/\\/ Added by W3 Total Cache(\r\n)*~s",
            '', $config_data);
        $config_data = preg_replace(
            "~(\\/\\/\\s*)?define\\s*\\(\\s*['\"]?W3TC_EDGE_MODE['\"]?\\s*,.*?\\)\\s*;+\\r?\\n?~is",
            '', $config_data);

        return $config_data;
    }

    public function action_edge_mode_disable() {
        w3_require_once(W3TC_INC_FUNCTIONS_DIR . '/activation.php');
        $config_path = w3_get_wp_config_path();

        $config_data = @file_get_contents($config_path);
        if ($config_data === false)
            return;

        $new_config_data = $this->wp_config_evaluation_mode_remove_from_content($config_data);
        if ($new_config_data != $config_data) {
            try {
                w3_wp_write_to_file($config_path, $new_config_data);
            } catch (FilesystemOperationException $ex) {
                throw new FilesystemModifyException(
                    $ex->getMessage(), $ex->credentials_form(),
                    'Edit file <strong>' . $config_path .
                    '</strong> and remove next lines:',
                    $config_path,  $this->wp_config_evaluation_mode());
            }
        }
        w3_admin_redirect(array('w3tc_note' => 'disabled_edge'));
    }
}