<?php

/**
 * File cache cleaner class
 */
if (!defined('ABSPATH')) {
    die();
}

w3_require_once(W3TC_LIB_W3_DIR . '/Cache/File.php');

/**
 * Class W3_Cache_File_Cleaner
 */
class W3_Cache_File_Cleaner {
    /**
     * Cache directory
     *
     * @var string
     */
    var $_cache_dir = '';

    /**
     * Clean operation time limit
     *
     * @var int
     */
    var $_clean_timelimit = 0;

    /**
     * Exclude files
     *
     * @var array
     */
    var $_exclude = array();

    /**
     * PHP5-style constructor
     *
     * @param array $config
     */
    function __construct($config = array()) {
        $this->_cache_dir = (isset($config['cache_dir']) ? trim($config['cache_dir']) : 'cache');
        $this->_clean_timelimit = (isset($config['clean_timelimit']) ? (int) $config['clean_timelimit'] : 180);
        $this->_exclude = (isset($config['exclude']) ? (array) $config['exclude'] : array());
    }

    /**
     * Run clean operation
     *
     * @return boolean
     */
    function clean() {
        @set_time_limit($this->_clean_timelimit);

        $this->_clean($this->_cache_dir, false);
    }

    /**
     * Clean
     *
     * @param string $path
     * @param bool $remove
     * @return void
     */
    function _clean($path, $remove = true) {
        $dir = @opendir($path);

        if ($dir) {
            while (($entry = @readdir($dir)) !== false) {
                if ($entry == '.' || $entry == '..') {
                    continue;
                }

                foreach ($this->_exclude as $mask) {
                    if (fnmatch($mask, basename($entry))) {
                        continue 2;
                    }
                }

                $full_path = $path . DIRECTORY_SEPARATOR . $entry;

                if (@is_dir($full_path)) {
                    $this->_clean($full_path);
                } elseif (!$this->is_valid($full_path)) {
                    @unlink($full_path);
                }
            }

            @closedir($dir);

            if ($remove) {
                @rmdir($path);
            }
        }
    }

    /**
     * Check if file is valid
     *
     * @param string $file
     * @return bool
     */
    function is_valid($file) {
        $valid = false;

        if (file_exists($file)) {
            $ftime = @filemtime($file);

            if ($ftime) {
                $fp = @fopen($file, 'rb');

                if ($fp) {
                    $expires = @fread($fp, 4);

                    if ($expires !== false) {
                        list(, $expire) = @unpack('L', $expires);

                        $expire = ($expire && $expire <= W3TC_CACHE_FILE_EXPIRE_MAX ? $expire : W3TC_CACHE_FILE_EXPIRE_MAX);

                        if ($ftime > (time() - $expire)) {
                            $valid = true;
                        }
                    }

                    @fclose($fp);
                }
            }
        }

        return $valid;
    }
}
