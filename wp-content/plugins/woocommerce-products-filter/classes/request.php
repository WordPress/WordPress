<?php

if (!defined('ABSPATH'))
    die('No direct access allowed');

//Request data what is coming by AJAX and another ways is sanitizing here
class WOOF_REQUEST {

    public static function set($key, $value) {
        $_REQUEST[$key] = self::sanitize($value);
    }

    public static function push($key, $value, $key2 = null) {
        if ($key2) {
            $_REQUEST[$key][$key2] = self::sanitize($value);
        } else {
            $_REQUEST[$key][] = self::sanitize($value);
        }
    }

    public static function get($key = null, $key2 = null) {

        if (!$key) {
            return self::sanitize($_REQUEST);
        }

        if ($key2) {
            if (self::isset($key)) {
                return isset($_REQUEST[$key][$key2]) ? self::sanitize($_REQUEST[$key][$key2]) : '';
            }
        }

        if (self::isset($key)) {
            return self::sanitize($_REQUEST[$key]);
        }

        return null;
    }

    public static function del($key) {
        if (self::isset($key)) {
            unset($_REQUEST[$key]);
        }
    }

    public static function isset($key) {
        return isset($_REQUEST[$key]);
    }

    private static function sanitize($value) {
        return $value = WOOF_HELPER::sanitize_array($value);
    }

}
