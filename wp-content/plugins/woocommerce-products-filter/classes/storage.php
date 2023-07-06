<?php

if (!defined('ABSPATH'))
    die('No direct access allowed');

//keeps current user data
final class WOOF_STORAGE {

    public $type = 'session'; //session, transient
    private $user_ip = null;
    private $transient_key = null;

    public function __construct($type = '') {
        if (!empty($type)) {
            $this->type = $type;
        }

        if ($this->type == 'session') {
            if (!session_id()) {
                try {
                    @session_start();
                } catch (Exception $e) {
                    //***
                }
            }
        }

        if (isset($_SERVER['REMOTE_ADDR'])) {
            $this->user_ip = filter_var(WOOF_HELPER::get_server_var('REMOTE_ADDR'), FILTER_VALIDATE_IP);
            $this->transient_key = md5($this->user_ip . 'woof_salt');
        }
    }

    public function set_val($key, $value) {
        switch ($this->type) {
            case 'session':
                WC()->session->set($key, $value);
                break;
            case 'transient':
                $data = get_transient($this->transient_key);
                if (!is_array($data)) {
                    $data = array();
                }
                $data[$key] = $value;
                set_transient($this->transient_key, $data, 1 * 24 * 3600); //1 day
                break;

            default:
                break;
        }
    }

    public function get_val($key) {
        $value = NULL;
        switch ($this->type) {
            case 'session':
                if ($this->is_isset($key)) {
                    $value = WC()->session->__get($key);
                }
                break;
            case 'transient':
                $data = get_transient($this->transient_key);
                if (!is_array($data)) {
                    $data = array();
                }
                if (isset($data[$key])) {
                    $value = $data[$key];
                }
                break;

            default:
                break;
        }

        return $value;
    }

    public function unset_val($key) {

        switch ($this->type) {
            case 'session':
                if ($this->is_isset($key)) {
                    WC()->session->__unset($key);
                }
                break;
            case 'transient':
                $data = get_transient($this->transient_key);
                if (isset($data[$key])) {
                    unset($data[$key]);
                }
                set_transient($this->transient_key, $data, 1 * 24 * 3600); //1 day
                //delete_transient($this->transient_key);
                break;

            default:
                break;
        }

        return false;
    }

    public function is_isset($key) {
        $isset = false;
        switch ($this->type) {
            case 'session':
                $isset = WC()->session->__isset($key);
                break;
            case 'transient':
                $isset = (bool) $this->get_val($key);
                break;

            default:
                break;
        }

        return $isset;
    }

}
