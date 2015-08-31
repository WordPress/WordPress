<?php

w3_require_once(W3TC_INC_DIR . '/functions/activation.php');

/**
 * Class W3_Environment
 */
class W3_GenericAdminEnvironment {

    /**
     * Fixes environment
     * @param W3_Config $config
     * @param bool $force_all_checks
     * @throws SelfTestExceptions
     */
    function fix_on_wpadmin_request($config, $force_all_checks) {
        $exs = new SelfTestExceptions();
        // create add-ins
        $this->create_required_files($config, $exs);

        // create folders
        $this->create_required_folders($exs);
        $this->add_index_to_folders();

        // create wp-loader file
        $wp_loader = w3_instance('W3_Environment_WpLoader');

        if ($wp_loader->should_create()) {
            try {
                $wp_loader->create();
            } catch (FilesystemOperationException $ex) {
                $exs->push($ex);
            }
        }

        if (count($exs->exceptions()) <= 0) {
            $this->notify_no_config_present($config, $exs);
            $this->notify_config_cache_not_writeable($config, $exs);
        }

        if (count($exs->exceptions()) > 0)
            throw $exs;
    }

    /**
     * Fixes environment once event occurs
     * @throws SelfTestExceptions
     **/
    public function fix_on_event($config, $event, $old_config = null) {
        if ($event == 'activate') {
            delete_option('w3tc_request_data');
            add_option('w3tc_request_data', '', null, 'no');
        }
    }

    /**
     * Fixes environment after plugin deactivation
     * @throws SelfTestExceptions
     * @return array
     */
    public function fix_after_deactivation() {
        $exs = new SelfTestExceptions();

        $this->delete_required_files($exs);

        delete_option('w3tc_request_data');

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
     * Checks if addins in wp-content is available and correct version.
     * @param $config
     * @param SelfTestExceptions $exs
     */
    private function create_required_files($config, $exs) {
        $src = W3TC_INSTALL_FILE_ADVANCED_CACHE;
        $dst = W3TC_ADDIN_FILE_ADVANCED_CACHE;

        if ($this->advanced_cache_installed()) {
            if ($this->is_advanced_cache_add_in()) {
                $script_data = @file_get_contents($dst);
                if ($script_data == @file_get_contents($src))
                    return;
            } else {
                w3_require_once(W3TC_INC_FUNCTIONS_DIR . '/other.php');
                w3_require_once(W3TC_INC_FUNCTIONS_DIR . '/ui.php');
                $remove_url = w3_admin_url('admin.php?page=w3tc_dashboard&amp;w3tc_default_remove_add_in=pgcache');

                $exs->push(new FilesystemOperationException(
                    sprintf(__('The Page Cache add-in file advanced-cache.php is not a W3 Total Cache drop-in.
                    It should be removed. %s', 'w3-total-cache'),
                    w3tc_button_link(__('Yes, remove it for me', 'w3-total-cache'), wp_nonce_url($remove_url,'w3tc')))));
                return;
            }
        }

        try {
            w3_wp_copy_file($src, $dst);
        } catch (FilesystemOperationException $ex) {
            $exs->push($ex);
        }
    }

    /**
     * Checks if addins in wp-content are available and deletes them.
     * @param SelfTestExceptions $exs
     */
    private function delete_required_files($exs) {
        try {
            if ($this->is_advanced_cache_add_in())
                w3_wp_delete_file(W3TC_ADDIN_FILE_ADVANCED_CACHE);
        } catch (FilesystemOperationException $ex) {
            $exs->push($ex);
        }
    }

    /**
     * Checks if addins in wp-content is available and correct version.
     * @param SelfTestExceptions $exs
     */
    private function create_required_folders($exs) {
        // folders that we create if not exists
        $directories = array(
            W3TC_CACHE_DIR,
            W3TC_CONFIG_DIR
        );

        foreach ($directories as $directory) {
            try{
                w3_wp_create_writeable_folder($directory, WP_CONTENT_DIR);
            } catch (FilesystemOperationException $ex) {
                $exs->push($ex);
            }
        }

        // folders that we delete if exists and not writeable
        $directories = array(
            W3TC_CACHE_CONFIG_DIR,
            W3TC_CACHE_TMP_DIR,
            W3TC_CACHE_BLOGMAP_FILENAME,
            W3TC_CACHE_DIR . '/object',
            W3TC_CACHE_DIR . '/db'
        );

        foreach ($directories as $directory) {
            try{
                if (file_exists($directory) && !is_writeable($directory))
                    w3_wp_delete_folder($directory);
            } catch (FilesystemRmdirException $ex) {
                $exs->push($ex);
            }
        }
    }

    /**
     * Adds index files
     */
    private function add_index_to_folders() {
        $directories = array(
            W3TC_CACHE_DIR,
            W3TC_CONFIG_DIR,
            W3TC_CACHE_CONFIG_DIR);
        $add_files = array();
        foreach ($directories as $dir) {
            if (is_dir($dir) && !file_exists($dir . '/index.html'))
                @file_put_contents($dir . '/index.html', '');
        }
    }

    /**
     * Check config file
     * @param W3_Config $config
     * @param SelfTestExceptions $exs
     */
    private function notify_no_config_present($config, $exs) {
        if ($config->own_config_exists() 
                && $config->get_integer('common.instance_id', 0) != 0)
            return;

        $onclick = 'document.location.href=\'' . 
            addslashes(wp_nonce_url(
                'admin.php?page=w3tc_general&w3tc_save_options')) . 
            '\';';
        $button = '<input type="button" class="button w3tc" ' .
            'value="save the settings" onclick="' . $onclick . '" />';

        $exs->push(new SelfTestFailedException('<strong>W3 Total Cache:</strong> ' .
            'Default settings are in use. The configuration file could ' .
            'not be read or doesn\'t exist. Please ' . $button . 
            ' to create the file.'));
    }

    /**
     * Check config cache is in sync with config
     * @param W3_Config $config
     * @param SelfTestExceptions $exs
     **/
    private function notify_config_cache_not_writeable($config, $exs) {
        try {
            $config->validate_cache_actual();
        } catch (Exception $ex) {
            // we could just create cache folder, so try again
            $config->load(true);
            try {
                $config->validate_cache_actual();
            } catch (Exception $ex) {
                $exs->push(new SelfTestFailedException(
                    '<strong>W3 Total Cache Error:</strong> ' .
                    $ex->getMessage()));
            }
        }
    }


    /**
     * Returns true if advanced-cache.php is installed
     *
     * @return boolean
     */
    public function advanced_cache_installed() {
        return file_exists(W3TC_ADDIN_FILE_ADVANCED_CACHE);
    }

    /**
     * Returns true if advanced-cache.php is old version.
     * @return boolean
     */
    public function advanced_cache_check_old_add_in() {
        return (($script_data = @file_get_contents(W3TC_ADDIN_FILE_ADVANCED_CACHE))
            && strstr($script_data, '& w3_instance') !== false);
    }

    /**
     * Checks if advanced-cache.php exists
     *
     * @return boolean
     */
    public function is_advanced_cache_add_in() {
        return (($script_data = @file_get_contents(W3TC_ADDIN_FILE_ADVANCED_CACHE))
            && strstr($script_data, 'W3_PgCache') !== false);
    }
}
