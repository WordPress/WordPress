<?php

/**
 * File class
 */
if (!defined('W3TC')) {
    die();
}

define('W3TC_CACHE_FILE_EXPIRE_MAX', 2592000);

w3_require_once(W3TC_INC_DIR . '/functions/file.php');
w3_require_once(W3TC_LIB_W3_DIR . '/Cache/Base.php');

/**
 * Class W3_Cache_File
 */
class W3_Cache_File extends W3_Cache_Base {
    /**
     * Path to cache dir
     *
     * @var string
     */
    protected $_cache_dir = '';

    /**
     * Directory to flush
     * @var string
     */
    protected $_flush_dir = '';
    /**
     * Exclude files
     *
     * @var array
     */
    protected $_exclude = array();

    /**
     * Flush time limit
     *
     * @var int
     */
    protected $_flush_timelimit = 0;

    /**
     * File locking
     *
     * @var boolean
     */
    protected $_locking = false;

    /**
     * If path should be generated based on wp_hash
     *
     * @var bool
     */
    protected $_use_wp_hash = false;

    /**
     * Constructor
     *
     * @param array $config
     */
    function __construct($config = array()) {
        parent::__construct($config);
        if (isset($config['cache_dir']))
            $this->_cache_dir = trim($config['cache_dir']);
        else
            $this->_cache_dir = w3_cache_blog_dir($config['section'], $config['blog_id']);

        $this->_exclude = isset($config['exclude']) ? (array) $config['exclude'] : array();
        $this->_flush_timelimit = isset($config['flush_timelimit']) ? (int) $config['flush_timelimit'] : 180;
        $this->_locking = isset($config['locking']) ? (boolean) $config['locking'] : false;

        if (isset($config['flush_dir']))
            $this->_flush_dir = $config['flush_dir'];
        else {
            if ($config['blog_id'] <= 0) {
                // clear whole section if we operate on master cache
                $this->_flush_dir = w3_cache_dir($config['section']);
            } else
                $this->_flush_dir = $this->_cache_dir;
        }
        if (isset($config['use_wp_hash']) && $config['use_wp_hash'] && function_exists('wp_hash'))
            $this->_use_wp_hash = true;
    }

    /**
     * Adds data
     *
     * @param string $key
     * @param mixed $var
     * @param integer $expire
     * @param string $group Used to differentiate between groups of cache values
     * @return boolean
     */
    function add($key, &$var, $expire = 0, $group = '') {
        if ($this->get($key, $group) === false) {
            return $this->set($key, $var, $expire, $group);
        }

        return false;
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
    function set($key, $var, $expire = 0, $group = '') {
        $key = $this->get_item_key($key);

        $sub_path = $this->_get_path($key);
        $path = $this->_cache_dir . DIRECTORY_SEPARATOR . ($group ? $group . DIRECTORY_SEPARATOR : '') . $sub_path;

        $dir = dirname($path);

        if (!@is_dir($dir)) {
            if (!w3_mkdir_from($dir, W3TC_CACHE_DIR))
                return false;
        }

        $fp = @fopen($path, 'wb');

        if (!$fp)
            return false;
        
        if ($this->_locking)
            @flock($fp, LOCK_EX);

        if ($expire <= 0 || $expire > W3TC_CACHE_FILE_EXPIRE_MAX)
            $expire = W3TC_CACHE_FILE_EXPIRE_MAX;

        $expires_at = time() + $expire;
        @fputs($fp, pack('L', $expires_at));
        @fputs($fp, '<?php exit; ?>');
        @fputs($fp, @serialize($var));
        @fclose($fp);

        if ($this->_locking)
            @flock($fp, LOCK_UN);

        return true;
    }

    /**
     * Returns data
     *
     * @param string $key
     * @param string $group Used to differentiate between groups of cache values
     * @return mixed
     */
    function get_with_old($key, $group = '') {
        $has_old_data = false;

        $key = $this->get_item_key($key);

        $path = $this->_cache_dir . DIRECTORY_SEPARATOR . ($group ? $group . DIRECTORY_SEPARATOR : '') . $this->_get_path($key);
        if (!is_readable($path))
            return array(null, $has_old_data);

        $fp = @fopen($path, 'rb');
        if (!$fp)
            return array(null, $has_old_data);

        if ($this->_locking)
            @flock($fp, LOCK_SH);

        $expires_at = @fread($fp, 4);
        $data_unserialized = null;

        if ($expires_at !== false) {
            list(, $expires_at) = @unpack('L', $expires_at);

            if (time() > $expires_at) {
                if ($this->_use_expired_data) {
                    // update expiration so other threads will use old data
                    $fp2 = @fopen($path, 'cb');

                    if ($fp2) {
                        @fputs($fp2, pack('L', time() + 30));
                        @fclose($fp2);
                    }
                    $has_old_data = true;
                }
            } else {
                $data = '';

                while (!@feof($fp)) {
                    $data .= @fread($fp, 4096);
                }
                $data = substr($data, 14);
                $data_unserialized = @unserialize($data);
            }

        }

        if ($this->_locking)
            @flock($fp, LOCK_UN);

        @fclose($fp);

        return array($data_unserialized, $has_old_data);
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
    function replace($key, &$var, $expire = 0, $group = '') {
        if ($this->get($key, $group) !== false) {
            return $this->set($key, $var, $expire, $group);
        }

        return false;
    }

    /**
     * Deletes data
     *
     * @param string $key
     * @param string $group Used to differentiate between groups of cache values
     * @return boolean
     */
    function delete($key, $group = '') {
        $key = $this->get_item_key($key);

        $path = $this->_cache_dir . DIRECTORY_SEPARATOR . ($group ? $group . DIRECTORY_SEPARATOR : '') . $this->_get_path($key);

        if (!file_exists($path))
            return true;

        if ($this->_use_expired_data) {
            $fp = @fopen($path, 'cb');

            if ($fp) {
                if ($this->_locking)
                    @flock($fp, LOCK_EX);

                @fputs($fp, pack('L', 0));   // make it expired
                @fclose($fp);

                if ($this->_locking)
                    @flock($fp, LOCK_UN);
                return true;
            }

        }

        return @unlink($path);
    }

    /**
     * Key to delete, deletes .old and primary if exists.
     * @param string $key
     *
     * @return bool
     */
    function hard_delete($key) {
        $key = $this->get_item_key($key);
        $path = $this->_cache_dir . DIRECTORY_SEPARATOR . $this->_get_path($key);
        return @unlink($path);
    }

    /**
     * Flushes all data
     *
     * @param string $group Used to differentiate between groups of cache values
     * @return boolean
     */
    function flush($group = '') {
        @set_time_limit($this->_flush_timelimit);
        $flush_dir = $group ? $this->_cache_dir . DIRECTORY_SEPARATOR . $group . DIRECTORY_SEPARATOR : $this->_flush_dir;
		w3_emptydir($flush_dir, $this->_exclude);
        return true;
    }

    /**
     * Returns modification time of cache file
     *
     * @param integer $key
     * @param string $group Used to differentiate between groups of cache values
     * @return boolean|string
     */
    function mtime($key, $group = '') {
        $path = $this->_cache_dir . DIRECTORY_SEPARATOR . ($group ? $group . DIRECTORY_SEPARATOR : '') . $this->_get_path($key);

        if (file_exists($path)) {
            return @filemtime($path);
        }

        return false;
    }

    /**
     * Returns file path for key
     *
     * @param string $key
     * @return string
     */
    function _get_path($key) {
        if ($this->_use_wp_hash)
            $hash = wp_hash($key);
        else
            $hash = md5($key);

        $path = sprintf('%s/%s/%s.php', substr($hash, 0, 3), substr($hash, 3, 3), $hash);

        return $path;
    }
}
