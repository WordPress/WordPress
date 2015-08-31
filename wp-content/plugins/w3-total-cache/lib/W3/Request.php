<?php

/**
 * W3 Request object
 */

/**
 * Class W3_Request
 */
class W3_Request {
    /**
     * Returns request value
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    static function get($key, $default = null) {
        $request = W3_Request::get_request();

        if (isset($request[$key])) {
            $value = $request[$key];

            if (defined('TEMPLATEPATH') || get_magic_quotes_gpc()) {
                $value = w3_stripslashes($value);
            }

            return $value;
        }

        return $default;
    }

    /**
     * Returns string value
     *
     * @param string $key
     * @param string $default
     * @param boolean $trim
     * @return string
     */
    static function get_string($key, $default = '', $trim = true) {
        $value = (string) W3_Request::get($key, $default);

        return ($trim) ? trim($value) : $value;
    }

    /**
     * Returns integer value
     *
     * @param string $key
     * @param integer $default
     * @return integer
     */
    static function get_integer($key, $default = 0) {
        return (integer) W3_Request::get($key, $default);
    }

    /**
     * Returns double value
     *
     * @param string $key
     * @param double|float $default
     * @return double
     */
    static function get_double($key, $default = 0.) {
        return (double) W3_Request::get($key, $default);
    }

    /**
     * Returns boolean value
     *
     * @param string $key
     * @param boolean $default
     * @return boolean
     */
    static function get_boolean($key, $default = false) {
        return w3_to_boolean(W3_Request::get($key, $default));
    }

    /**
     * Returns array value
     *
     * @param string $key
     * @param array $default
     * @return array
     */
    static function get_array($key, $default = array()) {
        $value = W3_Request::get($key);

        if (is_array($value)) {
            return $value;
        } elseif ($value != '') {
            return preg_split("/[\r\n,;]+/", trim($value));
        }

        return $default;
    }

    /**
     * Returns array value
     *
     * @param string $prefix
     * @param array $default
     * @return array
     */
    static function get_as_array($prefix, $default = array()) {
        $request = W3_Request::get_request();
        $array = array();
        foreach ($request as $key => $value) {
            if (strpos($key, $prefix) === 0 || strpos($key, str_replace('.', '_',$prefix)) === 0) {
                $array[substr($key,strlen($prefix))] = $value;
            }
        }
        return $array;
    }

    /**
     * Returns request array
     *
     * @return array
     */
    static function get_request() {
        if (!isset($_GET)) {
            $_GET = array();
        }

        if (!isset($_POST)) {
            $_POST = array();
        }

        return array_merge($_GET, $_POST);
    }
}
