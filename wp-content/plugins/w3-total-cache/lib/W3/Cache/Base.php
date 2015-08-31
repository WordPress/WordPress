<?php

/**
 * Base cache class
 */

/**
 * Class W3_Cache_Base
 */
class W3_Cache_Base {
    /*
     * Blog id
     *
     * @var integer
     */
    protected  $_blog_id = 0;

    /**
     * To separate the caching for different modules
     * @var string
     */
    protected $_module = '';

    /**
     * Host
     * @var string
     */
    protected $_host = '';

    /**
     * Host
     * @var int
     */
    protected $_instance_id = 0;

    /*
     * If we are going to return expired data when some other process
     * is working on new data calculation
     *
     * @var boolean
     */
    protected $_use_expired_data = false;

    /**
     * Constructor
     *
     * @param array $config
     */
    public function __construct($config = array()) {
        $this->_blog_id = $config['blog_id'];
        $this->_use_expired_data = isset($config['use_expired_data'])?$config['use_expired_data']:false;
        $this->_module = isset($config['module']) ? $config['module'] : 'default';
        $this->_host = isset($config['host']) ? $config['host'] : '';
        $this->_instance_id = isset($config['instance_id']) ? $config['instance_id'] : 0;
    }
    /**
     * Adds data
     *
     * @abstract
     * @param string $key
     * @param mixed $data
     * @param integer $expire
     * @param string $group Used to differentiate between groups of cache values
     * @return boolean
     */
    function add($key, &$data, $expire = 0, $group = '') {
        return false;
    }

    /**
     * Sets data
     *
     * @abstract
     * @param string $key
     * @param mixed $data
     * @param integer $expire
     * @param string $group Used to differentiate between groups of cache values
     * @return boolean
     */
    function set($key, $data, $expire = 0, $group = '') {
        return false;
    }

    /**
     * Returns data
     *
     * @param string $key
     * @param string $group Used to differentiate between groups of cache values
     * @return mixed
     */
    function get($key, $group = '') {
        list($data, $has_old) = $this->get_with_old($key, $group);
        return $data;
    }

    /**
     * Return primary data and if old exists
     *
     * @abstract
     * @param string $key
     * @param string $group Used to differentiate between groups of cache values
     * @return array|mixed
     */
    function get_with_old($key, $group = '') {
        return array(null, false);
    }

    /**
     * Alias for get for minify cache
     *
     * @param string $key
     * @param string $group Used to differentiate between groups of cache values
     * @return mixed
     */
    function fetch($key, $group = '') {
        return $this->get($key, $group = '');
    }

    /**
     * Replaces data
     *
     * @abstract
     * @param string $key
     * @param mixed $data
     * @param integer $expire
     * @param string $group Used to differentiate between groups of cache values
     * @return boolean
     */
    function replace($key, &$data, $expire = 0, $group = '') {
        return false;
    }

    /**
     * Deletes data
     *
     * @abstract
     * @param string $key
     * @param string $group Used to differentiate between groups of cache values
     * @return boolean
     */
    function delete($key, $group = '') {
        return false;
    }

    /**
     * Deletes primary data and old data
     *
     * @abstract
     * @param string $key
     * @return boolean
     */
    function hard_delete($key) {
        return false;
    }

    /**
     * Flushes all data
     *
     * @abstract
     * @param string $group Used to differentiate between groups of cache values
     * @return boolean
     */
    function flush($group = '') {
        return false;
    }

    /**
     * Checks if engine can function properly in this environment
     * @return bool
     */
    public function available() {
        return true;
    }

    /**
     * Constructs key version key
     * @param $group
     * @return string
     */
    protected function _get_key_version_key($group = '0') {
        return sprintf('w3tc_%d_%s_%s_%d_key_version', $this->_blog_id, $this->_module, $group, $this->_instance_id);
    }

    /**
     * Constructs item key
     * @param $name
     * @return string
     */
    public function get_item_key($name) {
        $key = sprintf('w3tc_key_%s_%d_%s_%s', $this->_host, $this->_blog_id, $this->_module, $name);
         /**
          * Allow to modify cache key by W3TC plugins
          */
        $key = w3tc_do_action('w3tc_' . $this->_module . '_cache_key', $key);
        return $key;
    }
}
