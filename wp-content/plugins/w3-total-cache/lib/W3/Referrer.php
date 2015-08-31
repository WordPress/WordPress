<?php

/**
 * W3TC Referrer detection
 */
define('W3TC_REFERRER_COOKIE_NAME', 'w3tc_referrer');

w3_require_once( W3TC_LIB_W3_DIR . '/CacheCase.php');

/**
 * Class W3_Referrer
 */
class W3_Referrer extends W3_CacheCase {
    /**
     * PHP5-style constructor
     */
    function __construct() {
        parent::__construct('referrer.rgroups', 'referrer');
    }

    /**
     * Returns HTTP referrer value
     *
     * @return string
     */
    function get_http_referrer() {
        $http_referrer = '';

        if ($this->has_enabled_groups()) {
            if (isset($_COOKIE[W3TC_REFERRER_COOKIE_NAME])) {
                $http_referrer = $_COOKIE[W3TC_REFERRER_COOKIE_NAME];
            } elseif (isset($_SERVER['HTTP_REFERER'])) {
                $http_referrer = $_SERVER['HTTP_REFERER'];

                setcookie(W3TC_REFERRER_COOKIE_NAME, $http_referrer, 0, w3_get_base_path());
            }
        } elseif(isset($_COOKIE[W3TC_REFERRER_COOKIE_NAME])) {
            setcookie(W3TC_REFERRER_COOKIE_NAME, '', 1);
        }

        return $http_referrer;
    }

    function group_verifier($group_compare_value) {
        static $http_referrer = null;
        if (is_null($http_referrer))
            $http_referrer = $this->get_http_referrer();
        return $http_referrer && preg_match('~' . $group_compare_value . '~i', $http_referrer);
    }

    function do_get_group() {
        return $this->get_http_referrer();
    }
}
