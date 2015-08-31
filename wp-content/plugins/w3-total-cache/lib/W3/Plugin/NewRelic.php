<?php
/**
 * W3 NewRelic plugin
 */
if (!defined('W3TC')) {
    die();
}

w3_require_once(W3TC_LIB_NEWRELIC_DIR . '/NewRelicWrapper.php');
w3_require_once(W3TC_LIB_W3_DIR . '/Plugin.php');
class W3_Plugin_NewRelic extends W3_Plugin{

    /**
     * New Relic reject reason
     *
     * @var string
     */
    var $newrelic_reject_reason = '';

    function run() {
        if (defined('DOING_CRON') && DOING_CRON)
            $this->background_task();

        add_action('init', array($this, 'init'));
        /**
         * Start output buffering
         */
        w3tc_add_ob_callback('newrelic', array($this,'ob_callback'));
    }

    function ob_callback(&$buffer) {
        if ($this->_config->get_boolean('newrelic.include_rum')) {
            if (($this->_config->get_boolean('browsercache.html.compression') ||
                $this->_config->get_string('pgcache.engine') == 'file_generic') && !$this->_should_disable_auto_rum()) {
                $buffer = preg_replace('~<head(\s+[^<>]+)*>~Ui', '\\0' . NewRelicWrapper::get_browser_timing_header(), $buffer, 1);
                $buffer = preg_replace('~<\\/body>~', NewRelicWrapper::get_browser_timing_footer() . '\\0', $buffer, 1);
            }
        }
        return $buffer;
    }

    /**
     * Sets up New Relic for current transaction
     */
    function init() {
        if ($this->_should_disable_auto_rum())
            $this->disable_auto_rum();
        $this->set_appname();
    }

    /**
     * Mark current transaction as an background job in New Relic.
     */
    function background_task() {
        NewRelicWrapper::mark_as_background_job();
    }

    /**
     * Disable auto rum for current transaction.
     */
    function disable_auto_rum() {
        NewRelicWrapper::disable_auto_rum();
    }

    function _should_disable_auto_rum() {

        /**
         * Disable for AJAX so its not messed up
         */
        if (defined('DOING_AJAX') && DOING_AJAX) {
            $this->newrelic_reject_reason = __('DOING_AJAX constant is defined', 'w3-total-cache');

            return true;
        }


        /**
         * Check for DONOTAUTORUM constant
         */
        if (defined('DONOTAUTORUM') && DONOTAUTORUM) {
            $this->newrelic_reject_reason = __('DONOTAUTORUM constant is defined', 'w3-total-cache');

            return true;
        }

        /**
         * Check logged users roles
         */
        if ($this->_config->get_boolean('newrelic.accept.logged_roles') && $this->_check_logged_in_role_not_allowed()) {
            $this->newrelic_reject_reason = __('logged in role is rejected', 'w3-total-cache');

            return true;
        }

        return false;
    }

    /**
     * Check if logged in user role is allowed to use New Relic Auto RUM
     *
     * @return boolean
     */
    private function _check_logged_in_role_not_allowed() {
        global $current_user;

        if (!is_user_logged_in())
            return false;

        $roles = $this->_config->get_array('newrelic.accept.roles');

        if (empty($roles))
            return false;

        $role = array_shift( $current_user->roles );

        if (in_array($role, $roles)) {
            return false;
        }

        return true;
    }

    public function set_appname() {
        static $appname_set;
        if (!$appname_set &&  ($this->_config->get_boolean('newrelic.use_php_function') || w3_is_network())) {
            $appname_set = true;
            $appname = NewRelicWrapper::get_wordpress_appname($this->_config, new W3_Config(true));
            $enable_xmit = $this->_config->get_boolean('newrelic.enable_xmit');
            NewRelicWrapper::set_appname($appname, '', $enable_xmit);
        }
    }
}
