<?php

if (!defined('W3TC')) {
    die();
}

w3_require_once(W3TC_INC_DIR . '/functions/activation.php');
w3_require_once(W3TC_INC_DIR . '/functions/file.php');
w3_require_once(W3TC_INC_DIR . '/functions/rule.php');

/**
 * Class W3_NewRelicAdminEnvironment
 */
class W3_NewRelicAdminEnvironment {
    /**
     * Fixes environment in each wp-admin request
     *
     * @param W3_Config $config
     * @param bool $force_all_checks
     * @throws SelfTestExceptions
     **/
    public function fix_on_wpadmin_request($config, $force_all_checks) {
        $exs = new SelfTestExceptions();

        if ($config->get_boolean('config.check') || $force_all_checks) {
            if (!$config->get_boolean('newrelic.enabled') || ($config->get_boolean('newrelic.enabled') && $config->get_boolean('newrelic.use_php_function'))) {
                $this->rules_remove($exs);
            }
        }

        if (count($exs->exceptions()) > 0)
            throw $exs;
    }

    /**
     * Fixes environment once event occurs
     * @throws SelfTestExceptions
     **/
    public function fix_on_event($config, $event, $old_config = null) {
    }

    /**
     * Fixes environment after plugin deactivation
     *
     * @throws SelfTestExceptions
     */
    public function fix_after_deactivation() {
        $exs = new SelfTestExceptions();

        $this->rules_remove($exs);

        if (count($exs->exceptions()) > 0)
            throw $exs;
    }

    /**
     * Returns required rules for module
     * @param W3_Config $config
     * @return array
     */
    public function get_required_rules($config) {
        return null;
    }



    /**
     * rules core modification
     **/

    /**
     * Removes Page Cache core directives
     *
     * @throws FilesystemOperationException with S/FTP form if it can't get the required filesystem credentials
     * @throws FileOperationException
     */
    private function rules_remove($exs) {
        w3_remove_rules($exs,w3_get_new_relic_rules_core_path(),
            W3TC_MARKER_BEGIN_NEW_RELIC_CORE,
            W3TC_MARKER_END_NEW_RELIC_CORE);
    }
}
