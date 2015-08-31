<?php

/**
 * W3 PgCache plugin - administrative interface
 */
if (!defined('W3TC')) {
    die();
}

/**
 * Class W3_DbCacheAdminEnvironment
 */
class W3_DbCacheAdminEnvironment {
    /*
     * Fixes environment in each wp-admin request
     * @param W3_Config $config
     * @param bool $force_all_checks
     *
     * @throws SelfTestExceptions
     **/
    public function fix_on_wpadmin_request($config, $force_all_checks) {
        $exs = new SelfTestExceptions();
        try {
            if ($config->get_boolean('dbcache.enabled'))
                $this->create_addin();
            else
                $this->delete_addin();
        } catch (FilesystemOperationException $ex) {
            $exs->push($ex);
        }

        if (count($exs->exceptions()) > 0)
            throw $exs;
    }

    /**
     * Fixes environment once event occurs
     * @throws SelfTestExceptions
     **/
    public function fix_on_event($config, $event, $old_config = null) {
        if ($config->get_boolean('dbcache.enabled') && 
                $config->get_string('dbcache.engine') == 'file') {
            if (!wp_next_scheduled('w3_dbcache_cleanup')) {
                wp_schedule_event(time(), 
                    'w3_dbcache_cleanup', 'w3_dbcache_cleanup');
            }
        } else {
            $this->unschedule();
        }
    }

    /**
     * Fixes environment after plugin deactivation
     * @throws SelfTestExceptions
     * @return array
     */
    public function fix_after_deactivation() {
        $exs = new SelfTestExceptions();

        try {
            $this->delete_addin();
        } catch (FilesystemOperationException $ex) {
            $exs->push($ex);
        }
    
        $this->unschedule();

        if (count($exs->exceptions()) > 0)
            throw $exs;
    }

    /**
     * Returns required rules for module
     * @var W3_Config $config
     * @return array
     */
    function get_required_rules($config) {
        return null;
    }

    /**
     * scheduling stuff
     **/
    private function unschedule() {
        if (wp_next_scheduled('w3_dbcache_cleanup')) {
            wp_clear_scheduled_hook('w3_dbcache_cleanup');
        }
    }

    /**
     * Creates add-in
     * @throws FilesystemOperationException
     */
    private function create_addin() {
        $src = W3TC_INSTALL_FILE_DB;
        $dst = W3TC_ADDIN_FILE_DB;


        if ($this->db_installed()) {
            if ($this->is_dbcache_add_in()) {
                $script_data = @file_get_contents($dst);
                if ($script_data == @file_get_contents($src))
                    return;
            } elseif (!$this->db_check_old_add_in()) {
                w3_require_once(W3TC_INC_FUNCTIONS_DIR . '/other.php');
                w3_require_once(W3TC_INC_FUNCTIONS_DIR . '/ui.php');
                if (isset($_GET['page']))
                    $url = 'admin.php?page=' . $_GET['page'] . '&amp;';
                else
                    $url = basename(w3_remove_query($_SERVER['REQUEST_URI'])) . '?page=w3tc_dashboard&amp;';
                $remove_url = w3_admin_url($url . 'w3tc_default_remove_add_in=dbcache');
                throw new FilesystemOperationException(
                    sprintf(__('The Database add-in file db.php is not a W3 Total Cache drop-in.
                    Remove it or disable Database Caching. %s', 'w3-total-cache'),
                    w3tc_button_link(__('Remove it for me', 'w3-total-cache'), wp_nonce_url($remove_url,'w3tc'))));
            }
        }

        w3_wp_copy_file($src, $dst);
    }

    /**
     * Deletes add-in
     * @throws FilesystemOperationException
     */
    private function delete_addin() {
        if ($this->is_dbcache_add_in())
            w3_wp_delete_file(W3TC_ADDIN_FILE_DB);
    }

    /**
     * Returns true if db.php is installed
     *
     * @return boolean
     */
    public function db_installed() {
        return file_exists(W3TC_ADDIN_FILE_DB);
    }

    /**
     * Returns true if db.php is old version.
     * @return boolean
     */
    public function db_check_old_add_in() {
        return (($script_data = @file_get_contents(W3TC_ADDIN_FILE_DB))
            && strstr($script_data, '& w3_instance') !== false);
    }

    /**
     * Checks if db.php is W3TC drop in
     *
     * @return boolean
     */
    public function is_dbcache_add_in() {
        return (($script_data = @file_get_contents(W3TC_ADDIN_FILE_DB))
            && strstr($script_data, 'W3 Total Cache Database module') !== false);
    }
}