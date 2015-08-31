<?php

/**
 * Generic file cache
 */
if (!defined('ABSPATH')) {
    die();
}

w3_require_once(W3TC_INC_DIR . '/functions/file.php');
w3_require_once(W3TC_LIB_W3_DIR . '/Cache/File.php');

/**
 * Class W3_Cache_File_Generic
 */
class W3_Cache_File_Generic extends W3_Cache_File {
    /**
     * Expire
     *
     * @var integer
     */
    var $_expire = 0;

    /**
     * PHP5-style constructor
     *
     * @param array $config
     */
    function __construct($config = array()) {
        parent::__construct($config);

        $this->_expire = (isset($config['expire']) ? (int) $config['expire'] : 0);

        if (!$this->_expire || $this->_expire > W3TC_CACHE_FILE_EXPIRE_MAX) {
            $this->_expire = W3TC_CACHE_FILE_EXPIRE_MAX;
        }
    }

    /**
     * Sets data
     *
     * @param string $key
     * @param string $var
     * @param int $expire
	 * @param string $group Used to differentiate between groups of cache values
     * @return boolean
     */
    function set($key, $var, $expire = 0, $group = '') {
        $key = $this->get_item_key($key);
        $sub_path = $this->_get_path($key);
        $path = $this->_cache_dir . '/' . $sub_path;

        $dir = dirname($path);

        if (!@is_dir($dir)) {
            if (!w3_mkdir_from($dir, W3TC_CACHE_DIR))
                return false;
        }

        $fp = @fopen($path, 'w');
        if (!$fp)
            return false;
        
        if ($this->_locking)
            @flock($fp, LOCK_EX);

        @fputs($fp, $var['content']);
        @fclose($fp);

        if ($this->_locking)
            @flock($fp, LOCK_UN);

        // some hostings create files with restrictive permissions
        // not allowing apache to read it later
        @chmod($path, 0644); 

        $old_entry_path = $path . '.old';
        @unlink($old_entry_path);

        if (w3_is_apache() && isset($var['headers']) &&
                isset($var['headers']['Content-Type']) &&
                substr($var['headers']['Content-Type'], 0, 8) == 'text/xml') {
            file_put_contents(dirname($path) . '/.htaccess',
                "<IfModule mod_mime.c>\n" .
                "    RemoveType .html_gzip\n" .
                "    AddType text/xml .html_gzip\n" .
                "    RemoveType .html\n" .
                "    AddType text/xml .html\n".
                "</IfModule>");
        }

        return true;
    }

    /**
     * Returns data
     *
     * @param string $key
	 * @param string $group Used to differentiate between groups of cache values
     * @return array
     */
    function get_with_old($key, $group = '') {
        $has_old_data = false;
        $key = $this->get_item_key($key);
        $path = $this->_cache_dir . '/' . $this->_get_path($key);

        $data = $this->_read($path);
        if ($data != null)
            return array($data, $has_old_data);


        $path_old = $path . '.old';
        $too_old_time = time() - 30;

        if ($exists = file_exists($path_old) ) {
            $file_time = @filemtime($path_old);
            if ($file_time) {
                if ($file_time > $too_old_time) {
                    // return old data
                    $has_old_data = true;
                    return array($this->_read($path_old), $has_old_data);

                }

                @touch($path_old);
            }
        }
        $has_old_data = $exists;

        return array(null, $has_old_data);
    }

    /**
     * Reads file
     *
     * @param string $path
     * @return array
     */
    private function _read($path) {
        if (!is_readable($path))
            return null;

        $fp = @fopen($path, 'r');
        if (!$fp)
            return null;

        if ($this->_locking)
            @flock($fp, LOCK_SH);

        $var = '';

        while (!@feof($fp))
            $var .= @fread($fp, 4096);

        @fclose($fp);

        if ($this->_locking)
            @flock($fp, LOCK_UN);

        return array(
            '404' => false,
            'headers' => array(),
            'time' => null,
            'content' => $var
        );
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
        $path = $this->_cache_dir . DIRECTORY_SEPARATOR . $this->_get_path($key);

        if (!file_exists($path))
          return true;

        $old_entry_path = $path . '.old';
        if (@rename($path, $old_entry_path))
            return true;

        // if we can delete old entry - do second attempt to store in old-entry file
        if (@unlink($old_entry_path)) {
          if (@rename($path, $old_entry_path))
            return true;
        }

        return @unlink($path);
    }

    /**
     * Key to delete, deletes .old and primary if exists.
     * @param $key
     * @return bool
     */
    function hard_delete($key) {
        $key = $this->get_item_key($key);
        $path = $this->_cache_dir . DIRECTORY_SEPARATOR . $this->_get_path($key);
        $old_entry_path = $path . '.old';
        @unlink($old_entry_path);

        if (!file_exists($path))
            return true;
        @unlink($path);
        return true;
    }

    /**
     * Flushes all data
     *
     * @param string $group Used to differentiate between groups of cache values
     * @return boolean
     */
    function flush($group = '') {
        if ($group == 'sitemaps') {
            $config = w3_instance('W3_Config');
            $sitemap_regex = $config->get_string('pgcache.purge.sitemap_regex');
            $this->_flush_based_on_regex($sitemap_regex);
        } else {
            w3_require_once(W3TC_LIB_W3_DIR . '/Cache/File/Cleaner/Generic.php');
            $c = new W3_Cache_File_Cleaner_Generic(array(
                'cache_dir' => $this->_flush_dir,
                'exclude' => $this->_exclude,
                'clean_timelimit' => $this->_flush_timelimit
            ));

            $c->clean();
        }
    }

    /**
     * Returns cache file path by key
     *
     * @param string $key
     * @return string
     */
    function _get_path($key) {
        return $key;
    }

    function get_item_key($key) {
        /**
         * Allow to modify page key by W3TC plugins
         */
        $key = w3tc_do_action('w3tc_' . $this->_module . '_cache_key', $key);
        return $key;
    }


    /**
     * Flush cache based on regex
     * @param string $regex
     */
    private function _flush_based_on_regex($regex) {
        if (w3_is_multisite() && !w3_is_subdomain_install()) {
            $domain = w3_get_home_url();
            $parsed = parse_url($domain);
            $host = $parsed['host'];
            $path = isset($parsed['path']) ? '/' . trim($parsed['path'], '/') : '';
            $flush_dir = W3TC_CACHE_PAGE_ENHANCED_DIR . '/' . $host . $path;
        } else
            $flush_dir = W3TC_CACHE_PAGE_ENHANCED_DIR . '/' . w3_get_domain(w3_get_host());

        $dir = @opendir($flush_dir);
        if ($dir) {
            while (($entry = @readdir($dir)) !== false) {
                if ($entry == '.' || $entry == '..') {
                    continue;
                }
                if (preg_match('/' . $regex . '/', basename($entry))) {
                    w3_rmdir($flush_dir . DIRECTORY_SEPARATOR . $entry);
                }
            }

            @closedir($dir);
        }
    }
}
