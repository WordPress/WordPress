<?php
/**
 * W3 FragmentCache plugin
 */
if (!defined('W3TC')) {
    die();
}

w3_require_once(W3TC_LIB_W3_DIR . '/Plugin.php');

/**
 * Class W3_Plugin_FragmentCacheAdmin
 */
class W3_Pro_Plugin_FragmentCacheAdmin extends W3_Plugin {
    function cleanup() {
        $engine = $this->_config->get_string('fragmentcache.engine');

        switch ($engine) {
            case 'file':
                w3_require_once(W3TC_LIB_W3_DIR . '/Cache/File/Cleaner.php');

                $w3_cache_file_cleaner = new W3_Cache_File_Cleaner(array(
                    'cache_dir' => w3_cache_blog_dir('fragment'),
                    'clean_timelimit' => $this->_config->get_integer('timelimit.cache_gc')
                ));

                $w3_cache_file_cleaner->clean();
                break;
        }
    }
}
