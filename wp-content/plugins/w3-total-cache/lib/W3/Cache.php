<?php

/**
 * W3 Cache class
 */

/**
 * W3 Cache engine types
 */
define('W3TC_CACHE_MEMCACHED', 'memcached');
define('W3TC_CACHE_APC', 'apc');
define('W3TC_CACHE_EACCELERATOR', 'eaccelerator');
define('W3TC_CACHE_XCACHE', 'xcache');
define('W3TC_CACHE_WINCACHE', 'wincache');
define('W3TC_CACHE_FILE', 'file');
define('W3TC_CACHE_FILE_GENERIC', 'file_generic');

/**
 * Class W3_Cache
 */
class W3_Cache {
    /**
     * Returns cache engine instance
     *
     * @param string $engine
     * @param array $config
     * @return W3_Cache_Base
     */
    static function instance($engine, $config = array()) {
        static $instances = array();

        // common configuration data
        if (!isset($config['blog_id']))
            $config['blog_id'] = w3_get_blog_id();

        $instance_key = sprintf('%s_%s', $engine, md5(serialize($config)));

        if (!isset($instances[$instance_key])) {
            switch ($engine) {
                case W3TC_CACHE_MEMCACHED:
                    w3_require_once(W3TC_LIB_W3_DIR . '/Cache/Memcached.php');
                    $instances[$instance_key] = new W3_Cache_Memcached($config);
                    break;

                case W3TC_CACHE_APC:
                    w3_require_once(W3TC_LIB_W3_DIR . '/Cache/Apc.php');
                    $instances[$instance_key] = new W3_Cache_Apc($config);
                    break;

                case W3TC_CACHE_EACCELERATOR:
                    w3_require_once(W3TC_LIB_W3_DIR . '/Cache/Eaccelerator.php');
                    $instances[$instance_key] = new W3_Cache_Eaccelerator($config);
                    break;

                case W3TC_CACHE_XCACHE:
                    w3_require_once(W3TC_LIB_W3_DIR . '/Cache/Xcache.php');
                    $instances[$instance_key] = new W3_Cache_Xcache($config);
                    break;

                case W3TC_CACHE_WINCACHE:
                    w3_require_once(W3TC_LIB_W3_DIR . '/Cache/Wincache.php');
                    $instances[$instance_key] = new W3_Cache_Wincache($config);
                    break;

                case W3TC_CACHE_FILE:
                    w3_require_once(W3TC_LIB_W3_DIR . '/Cache/File.php');
                    $instances[$instance_key] = new W3_Cache_File($config);
                    break;

                case W3TC_CACHE_FILE_GENERIC:
                    w3_require_once(W3TC_LIB_W3_DIR . '/Cache/File/Generic.php');
                    $instances[$instance_key] = new W3_Cache_File_Generic($config);
                    break;

                default:
                    trigger_error('Incorrect cache engine', E_USER_WARNING);
                    w3_require_once(W3TC_LIB_W3_DIR . '/Cache/Base.php');
                    $instances[$instance_key] = new W3_Cache_Base($config);
                    break;
            }

            if (!$instances[$instance_key]->available()) {
                w3_require_once(W3TC_LIB_W3_DIR . '/Cache/Base.php');
                $instances[$instance_key] = new W3_Cache_Base($config);
            }
        }

        return $instances[$instance_key];
    }
}
