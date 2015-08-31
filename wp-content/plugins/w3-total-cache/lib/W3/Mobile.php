<?php

/**
 * W3TC Mobile detection
 */

w3_require_once( W3TC_LIB_W3_DIR . '/CacheCase.php');

/**
 * Class W3_Mobile
 */
class W3_Mobile extends W3_CacheCase{
    /**
     * PHP5-style constructor
     */
    function __construct() {
        parent::__construct('mobile.rgroups', 'agents');
    }

    function group_verifier($group_compare_value) {
        return isset($_SERVER['HTTP_USER_AGENT']) && preg_match('~' . $group_compare_value . '~i', $_SERVER['HTTP_USER_AGENT']);
    }
}
