<?php

/**
 * Recursive creates directory
 *
 * @param string $path
 * @param integer $mask
 * @param string $curr_path
 * @return boolean
 */
function w3_mkdir($path, $mask = 0777, $curr_path = '') {
    $path = w3_realpath($path);
    $path = trim($path, '/');
    $dirs = explode('/', $path);

    foreach ($dirs as $dir) {
        if ($dir == '') {
            return false;
        }

        $curr_path .= ($curr_path == '' ? '' : '/') . $dir;

        if (!@is_dir($curr_path)) {
            if (!@mkdir($curr_path, $mask)) {
                return false;
            }
        }
    }

    return true;
}

/**
 * Recursive creates directory from some directory
 * Does not try to create directory before from
 *
 * @param string $path
 * @param string $from_path
 * @param integer $mask
 * @return boolean
 */
function w3_mkdir_from($path, $from_path = '', $mask = 0777) {
    $path = w3_realpath($path);

    $from_path = w3_realpath($from_path);
    if (substr($path, 0, strlen($from_path)) != $from_path)
        return false;

    $path = substr($path, strlen($from_path));

    $path = trim($path, '/');
    $dirs = explode('/', $path);

    $curr_path = $from_path;

    foreach ($dirs as $dir) {
        if ($dir == '') {
            return false;
        }

        $curr_path .= ($curr_path == '' ? '' : '/') . $dir;

        if (!@is_dir($curr_path)) {
            if (!@mkdir($curr_path, $mask)) {
                return false;
            }
        }
    }

    return true;
}

/**
 * Recursive remove dir
 *
 * @param string $path
 * @param array $exclude
 * @param bool $remove
 * @return void
 */
function w3_rmdir($path, $exclude = array(), $remove = true) {
    $dir = @opendir($path);

    if ($dir) {
        while (($entry = @readdir($dir)) !== false) {
            if ($entry == '.' || $entry == '..') {
                continue;
            }

            foreach ($exclude as $mask) {
                if (fnmatch($mask, basename($entry))) {
                    continue 2;
                }
            }

            $full_path = $path . DIRECTORY_SEPARATOR . $entry;

            if (@is_dir($full_path)) {
                w3_rmdir($full_path, $exclude);
            } else {
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
 * Recursive empty dir
 *
 * @param string $path
 * @param array $exclude
 * @return void
 */
function w3_emptydir($path, $exclude = array()) {
    w3_rmdir($path, $exclude, false);
}

/**
 * Check if file is write-able
 *
 * @param string $file
 * @return boolean
 */
function w3_is_writable($file) {
    $exists = file_exists($file);

    $fp = @fopen($file, 'a');

    if ($fp) {
        fclose($fp);

        if (!$exists) {
            @unlink($file);
        }

        return true;
    }

    return false;
}

/**
 * Cehck if dir is write-able
 *
 * @param string $dir
 * @return boolean
 */
function w3_is_writable_dir($dir) {
    $file = $dir . '/' . uniqid(mt_rand()) . '.tmp';

    return w3_is_writable($file);
}

/**
 * Returns dirname of path
 *
 * @param string $path
 * @return string
 */
function w3_dirname($path) {
    $dirname = dirname($path);

    if ($dirname == '.' || $dirname == '/' || $dirname == '\\') {
        $dirname = '';
    }

    return $dirname;
}

function w3_make_relative_path($filename, $base_dir) {
    $filename = w3_realpath($filename);
    $base_dir = w3_realpath($base_dir);

    $filename_parts = explode('/', trim($filename, '/'));
    $base_dir_parts = explode('/', trim($base_dir, '/'));

    // count number of equal path parts
    for ($equal_number = 0;;$equal_number++) {
        if ($equal_number >= count($filename_parts) ||
            $equal_number >= count($base_dir_parts))
            break;
        if ($filename_parts[$equal_number] != $base_dir_parts[$equal_number])
            break;
    }

    $relative_dir = str_repeat('../', count($base_dir_parts) - $equal_number);
    $relative_dir .= implode('/', array_slice($filename_parts, $equal_number));

    return $relative_dir;
}

/**
 * Returns open basedirs
 *
 * @return array
 */
function w3_get_open_basedirs() {
    $open_basedir_ini = ini_get('open_basedir');
    $open_basedirs = (W3TC_WIN ? preg_split('~[;,]~', $open_basedir_ini) : explode(':', $open_basedir_ini));
    $result = array();

    foreach ($open_basedirs as $open_basedir) {
        $open_basedir = trim($open_basedir);
        if (!empty($open_basedir) && $open_basedir != '') {
            $result[] = w3_realpath($open_basedir);
        }
    }

    return $result;
}

/**
 * Checks if path is restricted by open_basedir
 *
 * @param string $path
 * @return boolean
 */
function w3_check_open_basedir($path) {
    $path = w3_realpath($path);
    $open_basedirs = w3_get_open_basedirs();

    if (!count($open_basedirs)) {
        return true;
    }

    foreach ($open_basedirs as $open_basedir) {
        if (strstr($path, $open_basedir) !== false) {
            return true;
        }
    }

    return false;
}

function w3_get_file_permissions($file) {
    if (function_exists('fileperms') && $fileperms = @fileperms($file)) {
        $fileperms = 0777 & $fileperms;
    } else {
        clearstatcache();
        $stat=@stat($file);
        if ($stat)
            $fileperms = 0777 & $stat['mode'];
        else
            $fileperms = 0;
    }
    return $fileperms;
}

function w3_get_file_owner($file = '') {
    $fileowner = $filegroup = 'unknown';
    if ($file) {
        if (function_exists('fileowner') && function_exists('fileowner')) {
            $fileowner = @fileowner($file);
            $filegroup = @filegroup($file);
            if (function_exists('posix_getpwuid') && function_exists('posix_getgrgid')) {
                $fileowner = @posix_getpwuid($fileowner);
                $fileowner = $fileowner['name'];
                $filegroup = @posix_getgrgid($filegroup);
                $filegroup = $filegroup['name'];
            }
        }
    } else {
        if (function_exists('getmyuid') && function_exists('getmygid')) {
            $fileowner = @getmyuid();
            $filegroup = @getmygid();
            if (function_exists('posix_getpwuid') && function_exists('posix_getgrgid')) {
                $fileowner = @posix_getpwuid($fileowner);
                $fileowner = $fileowner['name'];
                $filegroup = @posix_getgrgid($filegroup);
                $filegroup = $filegroup['name'];
            }
        }
    }
    return $fileowner . ':' . $filegroup;
}

/**
 * Atomically writes file inside W3TC_CACHE_DIR dir
 * @param $filename
 * @param $content
 * @throws Exception
 * @return void
 **/
function w3_file_put_contents_atomic($filename, $content) {
    if (!is_dir(W3TC_CACHE_TMP_DIR) || !is_writable(W3TC_CACHE_TMP_DIR)) {
        w3_mkdir_from(W3TC_CACHE_TMP_DIR, W3TC_CACHE_DIR);

        if (!is_dir(W3TC_CACHE_TMP_DIR) || !is_writable(W3TC_CACHE_TMP_DIR)) {
            throw new Exception('Can\'t create folder <strong>' .
                W3TC_CACHE_TMP_DIR . '</strong>');
        }
    }

    $temp = tempnam(W3TC_CACHE_TMP_DIR, 'temp');

    try {
        if (!($f = @fopen($temp, 'wb'))) {
            if (file_exists($temp))
                @unlink($temp);
           throw new Exception('Can\'t write to temporary file <strong>' .
                    $temp . '</strong>');
        }

        fwrite($f, $content);
        fclose($f);

        if (!@rename($temp, $filename)) {
            @unlink($filename);
            if (!@rename($temp, $filename)) {
                w3_mkdir_from(dirname($filename), W3TC_CACHE_DIR);
                if (!@rename($temp, $filename)) {
                    throw new Exception('Can\'t write to file <strong>' .
                        $filename . '</strong>');
                }
            }
        }

        $chmod = 0644;
        if (defined('FS_CHMOD_FILE'))
            $chmod = FS_CHMOD_FILE;
        @chmod($filename, $chmod);
    } catch (Exception $ex) {
        if (file_exists($temp))
            @unlink($temp);
        throw $ex;
    }
}


/**
 * Takes a W3TC settings array and formats it to a PHP String
 * @param $data
 * @return string
 */
function w3tc_format_data_as_settings_file($data) {
    $config = "<?php\r\n\r\nreturn array(\r\n";
    foreach ($data as $key => $value)
        $config .= w3tc_format_array_entry_as_settings_file_entry(1, $key, $value);
    $config .= ");";
    return $config;
}


/**
 * Writes array item to file
 *
 * @param int $tabs
 * @param string $key
 * @param mixed $value
 * @return string
 */
function w3tc_format_array_entry_as_settings_file_entry($tabs, $key, $value) {
    $item = str_repeat("\t", $tabs);

    if (is_numeric($key) && (string)(int)$key === (string)$key) {
        $item .= sprintf("%d => ", $key);
    } else {
        $item .= sprintf("'%s' => ", addcslashes($key, "'\\"));
    }

    switch (gettype($value)) {
        case 'object':
        case 'array':
            $item .= "array(\r\n";
            foreach ((array)$value as $k => $v) {
                $item .= w3tc_format_array_entry_as_settings_file_entry($tabs + 1, $k, $v);
            }
            $item .= sprintf("%s),\r\n", str_repeat("\t", $tabs));
            return $item;

        case 'integer':
            $data = (string)$value;
            break;

        case 'double':
            $data = (string)$value;
            break;

        case 'boolean':
            $data = ($value ? 'true' : 'false');
            break;

        case 'NULL':
            $data = 'null';
            break;

        default:
        case 'string':
            $data = "'" . addcslashes($value, "'\\") . "'";
            break;
    }

    $item .= $data . ",\r\n";

    return $item;
}

