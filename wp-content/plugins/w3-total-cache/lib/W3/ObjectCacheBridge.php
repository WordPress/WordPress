<?php

class W3_ObjectCacheBridge {

    /**
     * Config
     *
     * @var W3_Config
     */
    var $_config = null;
    var $_caches = array();

    function __construct() {
        $this->_config = w3_instance('W3_Config');
        $this->_caches['objectcache'] = w3_instance('W3_ObjectCache');
        if (w3_is_pro($this->_config) || w3_is_enterprise($this->_config)) {
            if ($this->_config->get_boolean('fragmentcache.enabled'))
                $this->_caches['fragmentcache'] = 
                    w3_instance('W3_Pro_FragmentCache');
        }
    }

    /**
     * Get from the cache
     *
     * @param string $id
     * @param string $group
     * @return mixed
     */
    function get($id, $group = 'default') {
        $cache = $this->_get_engine($group);
        return $cache->get($id, $group);
    }

    /**
     * Set to the cache
     *
     * @param string $id
     * @param mixed $data
     * @param string $group
     * @param integer $expire
     * @return boolean
     */
    function set($id, $data, $group = 'default', $expire = 0) {
        $cache = $this->_get_engine($group);
        return $cache->set($id, $data, $group, $expire);
    }

    /**
     * Delete from the cache
     *
     * @param string $id
     * @param string $group
     * @param bool $force
     * @return boolean
     */
    function delete($id, $group = 'default', $force = false) {
        $cache = $this->_get_engine($group);
        return $cache->delete($id, $group, $force);
    }

    /**
     * Add to the cache
     *
     * @param string $id
     * @param mixed $data
     * @param string $group
     * @param integer $expire
     * @return boolean
     */
    function add($id, $data, $group = 'default', $expire = 0) {
        $cache = $this->_get_engine($group);
        return $cache->add($id, $data, $group, $expire);
    }

    /**
     * Replace in the cache
     *
     * @param string $id
     * @param mixed $data
     * @param string $group
     * @param integer $expire
     * @return boolean
     */
    function replace($id, $data, $group = 'default', $expire = 0) {
        $cache = $this->_get_engine($group);
        return $cache->replace($id, $data, $group, $expire);
    }

    /**
     * Reset keys
     *
     * @return boolean
     */
    function reset() {
        $result = true;
        foreach ($this->_caches as $engine)
            $result = $result && $engine->reset();
        return $result;
    }

    /**
     * Flush cache
     *
     * @return boolean
     */
    function flush() {
        $result = true;
        foreach ($this->_caches as $engine)
            $result = $result && $engine->flush();
        return $result;
    }

    /**
     * Add global groups
     *
     * @param array $groups
     * @return void
     */
    function add_global_groups($groups) {
        if (is_array($groups)) {
            $transient = $this->_split_groups_array($groups);
            if ($transient) {
                $cache = $this->_get_engine('transient');
                $cache->add_global_groups($transient);
            }
            $cache = $this->_get_engine();
            $cache->add_global_groups($groups);
        } else {
            $cache = $this->_get_engine($groups);
            $cache->add_global_groups($groups);
        }

    }

    /**
     * Add non-persistent groups
     *
     * @param array $groups
     * @return void
     */
    function add_nonpersistent_groups($groups) {
        if (is_array($groups)) {
            $transient = $this->_split_groups_array($groups);
            if ($transient) {
                $cache = $this->_get_engine('transient');
                $cache->add_nonpersistent_groups($transient);
            }
            $cache = $this->_get_engine();
            $cache->add_nonpersistent_groups($groups);
        } else {
            $cache = $this->_get_engine($groups);
            $cache->add_nonpersistent_groups($groups);
        }
    }

    /**
     * Checks groups and return transients in an array if exists and unset from group parameter
     * @param $groups
     * @return array
     */
    private function _split_groups_array(&$groups) {
        $transient = array();
        if (($key = array_search('site-transient', $groups)) !== false) {
            unset($groups[$key]);
            $transient[] = 'site-transient';
        }
        if (($key = array_search('transient', $groups)) !== false) {
            unset($groups[$key]);
            $transient[] = 'transient';
        }
        return $transient;
    }

    /**
     * Return engine based on which group the OC value belongs to.
     *
     * @param string $group
     * @return mixed
     */
    private function _get_engine($group = '') {
        if (!isset($this->_caches['fragmentcache']))
            return $this->_caches['objectcache'];
        switch($group) {
            case 'transient':
            case 'site-transient':
                return $this->_caches['fragmentcache'];
            default:
                return $this->_caches['objectcache'];
        }
    }

    /**
     * Decrement numeric cache item's value
     *
     * @param int|string $id The cache key to increment
     * @param int $offset The amount by which to decrement the item's value. Default is 1.
     * @param string $group The group the key is in.
     * @return bool|int False on failure, the item's new value on success.
     */
    function decr( $id, $offset = 1, $group = 'default' ) {
        $cache = $this->_get_engine($group);
        return $cache->decr($id, $offset, $group);
    }

    /**
     * Increment numeric cache item's value
     *
     * @param int|string $id The cache key to increment
     * @param int $offset The amount by which to increment the item's value. Default is 1.
     * @param string $group The group the key is in.
     * @return false|int False on failure, the item's new value on success.
     */
    function incr( $id, $offset = 1, $group = 'default' ) {
        $cache = $this->_get_engine($group);
        return $cache->incr($id, $offset, $group);
    }

    function switch_to_blog($blog_id) {
        foreach ($this->_caches as $cache)
            $cache->switch_blog($blog_id);
    }
}
