<?php

if (!defined('W3TC')) {
    die();
}

/**
 * Class W3_Pro_FragmentCacheAdminEnvironment
 */
class W3_Pro_FragmentCacheAdminEnvironment {
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
            if ($config->get_boolean('fragmentcache.enabled'))
                $this->create_addin();
            elseif (!$config->get_boolean('objectcache.enabled'))
                $this->delete_addin();
        } catch (FilesystemOperationException $ex) {
            $exs->push($ex);
        }

        if (count($exs->exceptions()) > 0)
            throw $exs;
    }

    /**
     * Returns required rules for module
     * @param W3_Config $config
     * @return null
     */
    function get_required_rules($config) {
        return null;
    }

    /**
     * Fixes environment once event occurs
     * @throws SelfTestExceptions
     **/
    public function fix_on_event($config, $event, $old_config = null) {
        if ($config->get_boolean('fragmentcache.enabled') && 
                $config->get_string('fragmentcache.engine') == 'file') {
            if (!wp_next_scheduled('w3_fragmentcache_cleanup')) {
                wp_schedule_event(time(), 
                    'w3_fragmentcache_cleanup', 
                    'w3_fragmentcache_cleanup');
            }
        } else {
            $this->unschedule();
        }
    }

    /**
     * Fixes environment after plugin deactivation
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
     * scheduling stuff
     **/
    private function unschedule() {
        if (wp_next_scheduled('w3_fragmentcache_cleanup')) {
            wp_clear_scheduled_hook('w3_fragmentcache_cleanup');
        }
    }

    /**
     * Creates add-in
     * @throws FilesystemOperationException
     */
    private function create_addin() {
        $src = W3TC_INSTALL_FILE_OBJECT_CACHE;
        $dst = W3TC_ADDIN_FILE_OBJECT_CACHE;

        if (file_exists($dst)) {
            $script_data = @file_get_contents($dst);
            if ($script_data == @file_get_contents($src))
                return;
        }

        w3_wp_copy_file($src, $dst);
    }

    /**
     * Deletes add-in
     * @throws FilesystemOperationException
     */
    private function delete_addin() {
        w3_wp_delete_file(W3TC_ADDIN_FILE_OBJECT_CACHE);
    }
}