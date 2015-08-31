<?php

/**
 * Wincache class
 */
if (!defined('W3TC')) {
    die();
}

w3_require_once(W3TC_LIB_W3_DIR . '/Cache/Base.php');

/**
 * Class W3_Cache_Wincache
 */
class W3_Cache_Wincache extends W3_Cache_Base {

    /*
     * Used for faster flushing
     *
     * @var integer $_key_version
     */
    private $_key_version = array();

    /**
     * Adds data
     *
     * @param string $key
     * @param mixed $var
     * @param integer $expire
     * @param string $group Used to differentiate between groups of cache values
     * @return boolean
     */
    function add($key, &$var, $expire = 0, $group = '0') {
        return $this->set($key, $var, $expire, $group);
    }

    /**
     * Sets data
     *
     * @param string $key
     * @param mixed $var
     * @param integer $expire
     * @param string $group Used to differentiate between groups of cache values
     * @return boolean
     */
    function set($key, $var, $expire = 0, $group = '0') {
        $key = $this->get_item_key($key);

        $var['key_version'] = $this->_get_key_version($group);
        
        return wincache_ucache_set($key . '_' . $this->_blog_id, serialize($var), $expire);
    }

    /**
     * Returns data
     *
     * @param string $key
     * @param string $group Used to differentiate between groups of cache values
     * @return mixed
     */
    function get_with_old($key, $group = '0') {
        $has_old_data = false;

        $key = $this->get_item_key($key);

        $v = @unserialize(wincache_ucache_get($key .  '_' . $this->_blog_id));
        if (!is_array($v) || !isset($v['key_version']))
            return array(null, $has_old_data);

        $key_version = $this->_get_key_version($group);
        if ($v['key_version'] == $key_version)
            return array($v, $has_old_data);

        if ($v['key_version'] > $key_version) {
            $this->_set_key_version($v['key_version'], $group);
            return array($v, $has_old_data);
        }

        // key version is old
        if (!$this->_use_expired_data)
            return array(null, $has_old_data);

        // if we have expired data - update it for future use and let
        // current process recalculate it
        $expires_at = isset($v['expires_at']) ? $v['expires_at'] : null;
        if ($expires_at == null || time() > $expires_at) {
            $v['expires_at'] = time() + 30;
            wincache_ucache_set($key . '_' . $this->_blog_id, serialize($v), 0);
            $has_old_data = true;

            return array(null, $has_old_data);
        }

        // return old version
        return array($v, $has_old_data);
    }

    /**
     * Replaces data
     *
     * @param string $key
     * @param mixed $var
     * @param integer $expire
     * @param string $group Used to differentiate between groups of cache values
     * @return boolean
     */
    function replace($key, &$var, $expire = 0, $group = '0') {
        if ($this->get($key, $group) !== false) {
            return $this->set($key, $var, $expire, $group);
        }

        return false;
    }

    /**
     * Deletes data
     *
     * @param string $key
     * @param string $group
     * @return boolean
     */
    function delete($key, $group = '') {
        $key = $this->get_item_key($key);

        if ($this->_use_expired_data) {
            $v = @unserialize(wincache_ucache_get($key .  '_' . $this->_blog_id));
            if (is_array($v)) {
                $v['key_version'] = 0;
                wincache_ucache_set($key . '_' . $this->_blog_id, serialize($v), 0);
                return true;
            }
        }

        return wincache_ucache_delete($key . '_' . $this->_blog_id);
    }

    /**
     * Key to delete, deletes .old and primary if exists.
     * @param $key
     * @return bool
     */
    function hard_delete($key) {
        $key = $this->get_item_key($key);
        return wincache_ucache_delete($key . '_' . $this->_blog_id);
    }

    /**
     * Flushes all data
     *
     * @param string $group Used to differentiate between groups of cache values
     * @return boolean
     */
    function flush($group = '0') {
        $this->_get_key_version($group);   // initialize $this->_key_version
        $this->_key_version[$group]++;
        $this->_set_key_version($this->_key_version[$group], $group);
        return true;
    }

    /**
     * Checks if engine can function properly in this environment
     * @return bool
     */
    public function available() {
        return function_exists('wincache_ucache_set');
    }

    /**
     * Returns key postfix
     *
     * @param string $group Used to differentiate between groups of cache values
     * @return integer
     */
    private function _get_key_version($group = '0') {
        if (!isset($this->_key_version[$group]) || $this->_key_version[$group] <= 0) {
            $v = wincache_ucache_get($this->_get_key_version_key($group));
            $v = intval($v);
            $this->_key_version[$group] = ($v > 0 ? $v : 1);
        }

        return $this->_key_version[$group];
    }

    /**
     * Sets new key version
     *
     * @param $v
     * @param string $group Used to differentiate between groups of cache values
     * @return boolean
     */
    private function _set_key_version($v, $group) {
      wincache_ucache_set($this->_get_key_version_key($group), $v, 0);
    }
}
