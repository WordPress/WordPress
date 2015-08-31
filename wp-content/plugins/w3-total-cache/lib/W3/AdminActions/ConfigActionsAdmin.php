<?php
if (!defined('W3TC')) {
    die();
}
w3_require_once(W3TC_INC_FUNCTIONS_DIR . '/admin.php');

class W3_AdminActions_ConfigActionsAdmin {

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
     * Import config action
     *
     * @return void
     */
    function action_config_import() {
        $error = '';

        $config = new W3_Config();

        if (!isset($_FILES['config_file']['error']) || $_FILES['config_file']['error'] == UPLOAD_ERR_NO_FILE) {
            $error = 'config_import_no_file';
        } elseif ($_FILES['config_file']['error'] != UPLOAD_ERR_OK) {
            $error = 'config_import_upload';
        } else {
            ob_start();
            $imported = $config->import($_FILES['config_file']['tmp_name']);
            ob_end_clean();

            if (!$imported) {
                $error = 'config_import_import';
            }
        }

        if ($error) {
            w3_admin_redirect(array(
                'w3tc_error' => $error
            ), true);
        }

        w3_config_save($this->_config, $config, $this->_config_admin);
        w3_admin_redirect(array(
            'w3tc_note' => 'config_import'
        ), true);
    }

    /**
     * Export config action
     *
     * @return void
     */
    function action_config_export() {
        $filename = substr(w3_get_home_url(), strpos(w3_get_home_url(), '//')+2);
        @header(sprintf(__('Content-Disposition: attachment; filename=%s.php', 'w3-total-cache'), $filename));
        echo $this->_config->export();
        die();
    }

    /**
     * Reset config action
     *
     * @return void
     */
    function action_config_reset() {
        $config = new W3_Config();
        $config->set_defaults();
        w3_config_save($this->_config, $config, $this->_config_admin);
        w3_admin_redirect(array(
            'w3tc_note' => 'config_reset'
        ), true);
    }


    /**
     * Save preview option
     *
     * @return void
     */
    function action_config_preview_enable() {
        $this->_config->preview_production_copy(-1);
        $this->_config_admin->set('previewmode.enabled', true);
        $this->_config_admin->save();
        w3_admin_redirect(array(
            'w3tc_note' => 'preview_enable'
        ));
    }

    /**
     * Save preview option
     *
     * @return void
     */
    function action_config_preview_disable() {
        $this->_config->preview_production_copy(1, true);
        $this->_config_admin->set('previewmode.enabled', false);
        $this->_config_admin->save();
        w3_admin_redirect(array(
            'w3tc_note' => 'preview_disable'
        ));
    }

    /**
     * Deploy preview settings action
     *
     * @return void
     */
    function action_config_preview_deploy() {
        $this->_config->preview_production_copy(1);
        w3_require_once(W3TC_LIB_W3_DIR . '/AdminActions/FlushActionsAdmin.php');
        $flush = new W3_AdminActions_FlushActionsAdmin();
        $flush->flush_all(false);
        w3_admin_redirect(array(
            'w3tc_note' => 'preview_deploy'
        ));
    }



    /**
     * Save dbcluster config action
     *
     * @return void
     */
    function action_config_dbcluster_config_save() {
        $params = array('page' => 'w3tc_general');

        if (!file_put_contents(W3TC_FILE_DB_CLUSTER_CONFIG,
            stripslashes($_REQUEST['newcontent']))) {
            w3_require_once(W3TC_INC_DIR . '/functions/activation.php');
            try {
                w3_throw_on_write_error(W3TC_FILE_DB_CLUSTER_CONFIG);
            } catch (Exception $e) {
                $error = $e->getMessage();
                w3_admin_redirect_with_custom_messages($params, array($error));
            }
        }

        w3_admin_redirect_with_custom_messages($params, null,
            array(__('Database Cluster configuration file has been successfully saved', 'w3-total-cache')));
    }

    /**
     * Save support us action
     *
     * @return void
     */
    function action_config_save_support_us() {
        $support = W3_Request::get_string('support');
        $tweeted = W3_Request::get_boolean('tweeted');
        $signmeup = W3_Request::get_boolean('signmeup');
        $this->_config->set('common.support', $support);
        $this->_config->set('common.tweeted', $tweeted);
        if ($signmeup) {
            if (w3_is_pro($this->_config))
                $license = 'pro';
            elseif (w3_is_enterprise())
                $license = 'enterprise';
            else
                $license = 'community';
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            wp_remote_post(W3TC_MAILLINGLIST_SIGNUP_URL, array(
                'body' => array( 'email' => $email, 'license' => $license )
            ));
        }
        $this->_config->save();

        w3_instance('W3_AdminLinks')->link_update($this->_config);

        w3_admin_redirect(array(
            'w3tc_note' => 'config_save'
        ));
    }

    /**
     * Update upload path action
     *
     * @return void
     */
    function action_config_update_upload_path() {
        update_option('upload_path', '');

        w3_admin_redirect();
    }
}