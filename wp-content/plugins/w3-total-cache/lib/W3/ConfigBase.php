<?php

/**
 * W3 Config object
 */

/**
 * Class W3_ConfigBase
 */
class W3_ConfigBase {
    /**
     * Array of config values
     *
     * @var array
     */
    protected $_data = array();

    /**
     * Constructor
     */
    function __construct($data) {
        $this->_data = $data;
    }

    /**
     * Returns string value
     *
     * @param string $key
     * @param string $default
     * @param boolean $trim
     * @return string
     */
    function get_string($key, $default = '', $trim = true) {
        $value = (string)$this->get($key, $default);

        return ($trim ? trim($value) : $value);
    }

    /**
     * Returns integer value
     *
     * @param string $key
     * @param integer $default
     * @return integer
     */
    function get_integer($key, $default = 0) {
        return (integer)$this->get($key, $default);
    }

    /**
     * Returns boolean value
     *
     * @param string $key
     * @param boolean $default
     * @return boolean
     */
    function get_boolean($key, $default = false) {
        return (boolean)$this->get($key, $default);
    }

    /**
     * Returns array value
     *
     * @param string $key
     * @param array $default
     * @return array
     */
    function get_array($key, $default = array()) {
        return (array)$this->get($key, $default);
    }

    /**
     * Sets config value. 
     * Method to override
     *
     * @param string $key
     * @param string $value
     * @return object value set
     */
    function set($key, $value) {
        $this->_data[$key] = $value;
        return $value;
    }

    /**
     * Returns config value. Implementation for overriding
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function get($key, $default = null) {
        if (array_key_exists($key, $this->_data)) {
            $v = $this->_data[$key];
            return $v;
        }
        
        return $default;
    }
}
