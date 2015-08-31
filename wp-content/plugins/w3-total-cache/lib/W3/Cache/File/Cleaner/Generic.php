<?php

/**
 * Generic file cache cleaner class
 */
if (!defined('ABSPATH')) {
    die();
}

w3_require_once(W3TC_LIB_W3_DIR . '/Cache/File/Cleaner.php');

/**
 * Class W3_Cache_File_Cleaner_Generic
 */
class W3_Cache_File_Cleaner_Generic extends W3_Cache_File_Cleaner {
    /**
     * Number of items processed
     *
     * @var integer
     */
    var $processed_count = 0;
    /**
     * Cache expire time
     *
     * @var int
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
            $this->_expire = 0;
        }
    }

    function _clean($path, $remove = false) {
        $dir = false;
        if (is_dir($path)) {
            $dir = @opendir($path);
        }

        if ($dir) {
            while (($entry = @readdir($dir)) !== false) {
                if ($entry == '.' || $entry == '..') {
                    continue;
                }

                $full_path = $path . DIRECTORY_SEPARATOR . $entry;

                if (substr($entry, -4) === '.old' && !$this->is_old_file_expired($full_path)) {
                    continue;
                }

                foreach ($this->_exclude as $mask) {
                    if (fnmatch($mask, basename($entry))) {
                        continue 2;
                    }
                }


                if (@is_dir($full_path)) {
                    $this->_clean($full_path);
                } elseif (substr($entry, -4) === '.old') {
                    $this->processed_count++;
                    @unlink($full_path);
                } elseif (!$this->is_valid($full_path)) {
                    $old_entry_path = $full_path . '.old';
                    $this->processed_count++;
                    if (!@rename($full_path, $old_entry_path)) {
                        // if we can delete old entry - do second attempt to store in old-entry file
                        if (@unlink($old_entry_path)) {
                            @rename($full_path, $old_entry_path);
                        }
                    }
                }
            }

            @closedir($dir);
            if ($this->is_empty_dir($path))
                @rmdir($path);
        }
    }

    /**
     * Checks if file is valid
     *
     * @param string $file
     * @return bool
     */
    function is_valid($file) {
        if ($this->_expire <= 0)
          return false;

        if (file_exists($file)) {
            $ftime = @filemtime($file);

            if ($ftime && $ftime > (time() - $this->_expire)) {
                return true;
            }
        }

        return false;
    }

    function is_old_file_expired($file) {
        $ftime = @filemtime($file);
        $expire = $this->_expire ? $this->_expire * 5 : W3TC_CACHE_FILE_EXPIRE_MAX;
        if ($ftime && $ftime < (time() - $expire)) {
            return true;
        }

        return false;
    }
    function is_empty_dir($dir){
        return (($files = @scandir($dir)) && count($files) <= 2);
    }
}
