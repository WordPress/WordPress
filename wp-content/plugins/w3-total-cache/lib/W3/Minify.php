<?php

/**
 * W3 Minify object
 */

// Define repeated regex to simplify changes
define('MINIFY_AUTO_FILENAME_REGEX', '([a-zA-Z0-9-_]+)\\.(css|js)');
define('MINIFY_MANUAL_FILENAME_REGEX', '([a-f0-9]+)\\/(.+)\\.(include(\\-(footer|body))?)\\.[a-f0-9]+\\.(css|js)');
/**
 * Class W3_Minify
 */
class W3_Minify {
    /**
     * Config
     *
     * @var W3_Config
     */
    var $_config = null;

    /**
     * Admin configuration
     *
     * @var W3_ConfigAdmin
     */
    var $_config_admin = null;

    /**
     * Tracks if an error has occurred.
     *
     * @var bool
     */
    var $_error_occurred = false;

    /**
     * Returns instance. for backward compatibility with 0.9.2.3 version of /wp-content files
     *
     * @return W3_Minify
     */
    function instance() {
        return w3_instance('W3_Minify');
    }

    /**
     * PHP5 constructor
     */
    function __construct() {
        $this->_config = w3_instance('W3_Config');
        $this->_config_admin = w3_instance('W3_ConfigAdmin');
    }

    /**
     * PHP4 constructor
     */
    function W3_Minify() {
        $this->__construct();
    }

    /**
     * Runs minify
     *
     * @param string|null $file
     *
     * @return void
     */
    function process($file = NULL) {
        w3_require_once(W3TC_LIB_W3_DIR . '/Request.php');

        /**
         * Check for rewrite test request
         */
        $rewrite_test = W3_Request::get_boolean('w3tc_rewrite_test');

        if ($rewrite_test) {
            echo 'OK';
            exit();
        }

        $rewrite_test = W3_Request::get_string('test_file');

        if ($rewrite_test) {
            $cache = $this->_get_cache();
            header('Content-type: text/css');

            if ($cache->store(basename($rewrite_test), 'content ok')) {
                if ((function_exists('gzencode') &&
                    $this->_config->get_boolean('browsercache.enabled') &&
                    $this->_config->get_boolean('browsercache.cssjs.compression')))
                    if (!$cache->store(basename($rewrite_test) . '.gzip', gzencode('content ok'))) {
                        echo 'error storing';
                        exit();
                    }

                if ($this->_config->get_string('minify.engine') != 'file') {
                    if ($cache->fetch(basename($rewrite_test)) == 'content ok') {
                        echo 'content ok';
                    } else
                        echo 'error storing';
                } else
                    echo 'retry';
            } else {
                echo 'error storing';
            }
            exit();
        }

        if (is_null($file))
            $file = W3_Request::get_string('file');

        if (!$file) {
            $this->error('File param is missing', false);
            return;
        }

        // remove blog_id
        $levels = '';
        if (defined('W3TC_BLOG_LEVELS')) {
            for ($n = 0; $n < W3TC_BLOG_LEVELS; $n++)
                $levels .= '[0-9]+\/';
        }

        if (preg_match('~^(' . $levels . '[0-9]+)\/(.+)$~', $file, $matches))
            $file = $matches[2];

        // parse file
        $hash = '';
        $matches = null;
        $location = '';
        $type = '';

        if (preg_match('~^' . MINIFY_AUTO_FILENAME_REGEX .'$~', $file, $matches)) {
            list(, $hash, $type) = $matches;
        } elseif (preg_match('~^' . MINIFY_MANUAL_FILENAME_REGEX . '$~', $file, $matches)) {
                list(, $theme, $template, $location, , , $type) = $matches;
        } else {
            $this->error(sprintf('Bad file param format: "%s"', $file), false);
            return;
        }

        w3_require_once(W3TC_LIB_MINIFY_DIR . '/Minify.php');
        w3_require_once(W3TC_LIB_MINIFY_DIR . '/HTTP/Encoder.php');

        /**
         * Fix DOCUMENT_ROOT
         */
        $_SERVER['DOCUMENT_ROOT'] = w3_get_document_root();

        /**
         * Set cache engine
         */
        Minify::setCache($this->_get_cache());

        /**
         * Set cache ID
         */
        $cache_id = $this->get_cache_id($file);

        Minify::setCacheId($cache_id);

        /**
         * Set logger
         */
        w3_require_once(W3TC_LIB_MINIFY_DIR . '/Minify/Logger.php');
        Minify_Logger::setLogger(array(
            &$this,
            'error'));

        /**
         * Set options
         */
        $browsercache = $this->_config->get_boolean('browsercache.enabled');

        $serve_options = array_merge($this->_config->get_array('minify.options'), array(
             'debug' => $this->_config->get_boolean('minify.debug'),
             'maxAge' => $this->_config->get_integer('browsercache.cssjs.lifetime'),
             'encodeOutput' => ($browsercache && $this->_config->get_boolean('browsercache.cssjs.compression')),
             'bubbleCssImports' => ($this->_config->get_string('minify.css.imports') == 'bubble'),
             'processCssImports' => ($this->_config->get_string('minify.css.imports') == 'process'),
             'cacheHeaders' => array(
                 'use_etag' => ($browsercache && $this->_config->get_boolean('browsercache.cssjs.etag')),
                 'expires_enabled' => ($browsercache && $this->_config->get_boolean('browsercache.cssjs.expires')),
                 'cacheheaders_enabled' => ($browsercache && $this->_config->get_boolean('browsercache.cssjs.cache.control')),
                 'cacheheaders' => $this->_config->get_string('browsercache.cssjs.cache.policy')
             )
        ));

        /**
         * Set sources
         */
        if ($hash) {
            $_GET['f'] = $this->get_custom_files($hash, $type);
        } else {
            $_GET['g'] = $location;
            $serve_options['minApp']['groups'] = $this->get_groups($theme, $template, $type);
        }

        /**
         * Set minifier
         */
        $w3_minifier = w3_instance('W3_Minifier');

        if ($type == 'js') {
            $minifier_type = 'application/x-javascript';

            switch (true) {
                case (($hash || $location == 'include') && $this->_config->get_boolean('minify.js.combine.header')):
                case ($location == 'include-body' && $this->_config->get_boolean('minify.js.combine.body')):
                case ($location == 'include-footer' && $this->_config->get_boolean('minify.js.combine.footer')):
                    $engine = 'combinejs';
                    break;

                default:
                    $engine = $this->_config->get_string('minify.js.engine');

                    if (!$w3_minifier->exists($engine) || !$w3_minifier->available($engine)) {
                        $engine = 'js';
                    }
                    break;
            }

        } elseif ($type == 'css') {
            $minifier_type = 'text/css';

            if (($hash || $location == 'include') && $this->_config->get_boolean('minify.css.combine')) {
                $engine = 'combinecss';
            } else {
                $engine = $this->_config->get_string('minify.css.engine');

                if (!$w3_minifier->exists($engine) || !$w3_minifier->available($engine)) {
                    $engine = 'css';
                }
            }
        }

        /**
         * Initialize minifier
         */
        $w3_minifier->init($engine);

        $serve_options['minifiers'][$minifier_type] = $w3_minifier->get_minifier($engine);
        $serve_options['minifierOptions'][$minifier_type] = $w3_minifier->get_options($engine);

        /**
         * Send X-Powered-By header
         */
        if ($browsercache && $this->_config->get_boolean('browsercache.cssjs.w3tc')) {
            @header('X-Powered-By: ' . W3TC_POWERED_BY);
        }

        /**
         * Minify!
         */
        try {
            Minify::serve('MinApp', $serve_options);
        } catch (Exception $exception) {
            $this->error($exception->getMessage());
        }

        if (!$this->_error_occurred && $this->_config_admin->get_boolean('notes.minify_error')) {
            $error_file = $this->_config_admin->get_string('minify.error.file');
            if ($error_file == $file) {
                $this->_config_admin->set('notes.minify_error', false);
                $this->_config_admin->save();
            }
        }
    }

    /**
     * Flushes cache
     *
     * @return boolean
     */
    function flush() {
        $cache = $this->_get_cache();

        return $cache->flush();
    }

    /**
     * Creates file with content of minify file
     * 
     * @param string $file
     * @param string $file_name
     */
    function store_to_file($file, $file_name) {
        ob_start();
        $this->process($file);
        $data = ob_get_clean();
        
        if (!file_exists($file_name)) {
            w3_require_once(W3TC_INC_DIR . '/functions/file.php');
            if(!file_exists(dirname($file_name)))
                w3_mkdir_from(dirname($file_name), W3TC_CACHE_DIR);
        }
        @file_put_contents($file_name, $data);
    }
    
    /**
     * Returns custom data storage for minify file, based on url
     * 
     * @param string $url
     * @return mixed
     */
    function get_url_custom_data($url) {
        if (preg_match('~/' . MINIFY_AUTO_FILENAME_REGEX .'$~', $url, $matches)) {
            list(, $hash, $type) = $matches;

            $key = $this->get_custom_data_key($hash, $type);
            return $this->_cache_get($key);
        }

        return null;
    }

    /**
     * Returns custom data storage for minify file
     * 
     * @param string $file
     * @param mixed $data
     */
    function set_file_custom_data($file, $data) {
        if (preg_match('~' . MINIFY_AUTO_FILENAME_REGEX .'$~', $file, $matches)) {
            list(, $hash, $type) = $matches;

            $key = $this->get_custom_data_key($hash, $type);
            $this->_cache_set($key, $data);
        }
    }

    /**
     * Log
     *
     * @param string $msg
     * @return bool
     */
    function log($msg) {
        $data = sprintf("[%s] [%s] [%s] %s\n", date('r'), $_SERVER['REQUEST_URI'], (!empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '-'), $msg);
        $data = strtr($data, '<>', '..');

        $filename = w3_debug_log('minify');
        return @file_put_contents($filename, $data, FILE_APPEND);
    }

    /**
     * Returns minify groups
     *
     * @param string $theme
     * @param string $template
     * @param string $type
     * @return array
     */
    function get_groups($theme, $template, $type) {
        $result = array();

        switch ($type) {
            case 'css':
                $groups = $this->_config->get_array('minify.css.groups');
                break;

            case 'js':
                $groups = $this->_config->get_array('minify.js.groups');
                break;

            default:
                return $result;
        }

        if (isset($groups[$theme]['default'])) {
            $locations = (array) $groups[$theme]['default'];
        } else {
            $locations = array();
        }

        if ($template != 'default' && isset($groups[$theme][$template])) {
            $locations = array_merge_recursive($locations, (array) $groups[$theme][$template]);
        }

        foreach ($locations as $location => $config) {
            if (!empty($config['files'])) {
                foreach ((array) $config['files'] as $file) {
                    $file = w3_normalize_file_minify2($file);

                    if (w3_is_url($file)) {
                        $precached_file = $this->_precache_file($file, $type);

                        if ($precached_file) {
                            $result[$location][$file] = $precached_file;
                        } else {
                            $this->error(sprintf('Unable to cache remote file: "%s"', $file));
                        }
                    } else {
                        if (!w3_is_multisite() && strpos(trailingslashit(WP_CONTENT_DIR), trailingslashit(w3_get_site_root())) !== false)
                            $file = ltrim(w3_get_site_path(), '/') . str_replace(ltrim(w3_get_site_path(), '/'), '', $file);

                        $path = w3_get_document_root() . '/' . $file;

                        if (file_exists($path)) {
                            $result[$location][$file] = '//' . $file;
                        } else {
                            $this->error(sprintf('File "%s" doesn\'t exist', $path));
                        }
                    }
                }
            }
        }

        return $result;
    }

    /**
     * Returns minify cache ID
     *
     * @param string $file
     * @return string
     */
    function get_cache_id($file) {
        if ($this->_config->get_string('minify.engine') == 'file') {
            $cache_id = $file;
        } else {
            $cache_id = sprintf('w3tc_%s_minify_%s', w3_get_host_id(), md5($file));
        }

        return $cache_id;
    }

    /**
     * Returns array of group sources
     *
     * @param string $theme
     * @param string $template
     * @param string $location
     * @param string $type
     * @return array
     */
    function get_sources_group($theme, $template, $location, $type) {
        $sources = array();
        $groups = $this->get_groups($theme, $template, $type);

        if (isset($groups[$location])) {
            $files = (array) $groups[$location];

            $document_root = w3_get_document_root();

            foreach ($files as $file) {
                if (is_a($file, 'Minify_Source')) {
                    $path = $file->filepath;
                } else {
                    $path = rtrim($document_root,'/') . '/' . ltrim($file, '/');
                }

                $sources[] = $path;
            }
        }

        return $sources;
    }

    /**
     * Returns ID key for group
     *
     * @param  $theme
     * @param  $template
     * @param  $location
     * @param  $type
     * @return string
     */
    function get_id_key_group($theme, $template, $location, $type) {
        if ($this->_config->get_string('minify.engine') == 'file') {
            $key = sprintf('%s/%s.%s.%s.id', $theme, $template, $location, $type);
        } else {
            $key = sprintf('w3tc_%s_minify_id_%s', w3_get_host_id(), md5($theme . $template . $location . $type));
        }

        return $key;
    }

    /**
     * Returns id for group
     *
     * @param string $theme
     * @param string $template
     * @param string $location
     * @param string $type
     * @return integer
     */
    function get_id_group($theme, $template, $location, $type) {
        $key = $this->get_id_key_group($theme, $template, $location, $type);
        $id = $this->_cache_get($key);

        if ($id === false) {
            $sources = $this->get_sources_group($theme, $template, $location, $type);

            if (count($sources)) {
                $id = $this->_generate_id($sources, $type);

                if ($id) {
                    $this->_cache_set($key, $id);
                }
            }
        }

        return $id;
    }

    /**
     * Returns custom files key
     *
     * @param string $hash
     * @param string $type
     * @return string
     */
    function get_custom_data_key($hash, $type) {
        if ($this->_config->get_string('minify.engine') == 'file') {
            $key = sprintf('%s.%s.customdata', $hash, $type);
        } else {
            $key = sprintf('w3tc_%s_minify_customdata_%s', w3_get_host_id(), md5($hash . $type));
        }

        return $key;
    }

    /**
     * Returns custom files
     *
     * @param string $hash
     * @param string $type
     * @return array
     */
    function get_custom_files($hash, $type) {
        $files = $this->uncompress_minify_files($hash, $type);
        $result = array();
        if ($files) {
            foreach ($files as $file) {
                $file = w3_normalize_file_minify2($file);

                if (w3_is_url($file)) {
                    $precached_file = $this->_precache_file($file, $type);

                    if ($precached_file) {
                        $result[] = $precached_file;
                    } else {
                        $this->error(sprintf('Unable to cache remote file: "%s"', $file));
                    }
                } else {
                    $path = w3_get_document_root() . '/' . $file;

                    if (file_exists($path)) {
                        $result[] = $file;
                    } else {
                        $this->error(sprintf('File "%s" doesn\'t exist', $path));
                    }
                }
            }
        } else {
            $this->error(sprintf('Unable to fetch custom files list: "%s.%s"', $hash, $type), false, 404);
        }

        return $result;
    }

    /**
     * Sends error response
     *
     * @param string $error
     * @param boolean $handle
     * @param integer $status
     * @return void
     */
    function error($error, $handle = true, $status = 400) {
        $debug = $this->_config->get_boolean('minify.debug');

        $this->_error_occurred = true;

        if ($debug) {
            $this->log($error);
        }

        if ($handle) {
            $this->_handle_error($error);
        }

        if (defined('W3TC_IN_MINIFY')) {
            status_header($status);

            echo '<h1>W3TC Minify Error</h1>';

            if ($debug) {
                echo sprintf('<p>%s.</p>', $error);
            } else {
                echo '<p>Enable debug mode to see error message.</p>';
            }

            die();
        }
    }

    /**
     * Pre-caches external file
     *
     * @param string $url
     * @param string $type
     * @return string
     */
    function _precache_file($url, $type) {
        $lifetime = $this->_config->get_integer('minify.lifetime');
        $cache_path = sprintf('%s/minify_%s.%s', w3_cache_blog_dir('minify'), md5($url), $type);

        if (!file_exists($cache_path) || @filemtime($cache_path) < (time() - $lifetime)) {
            w3_require_once(W3TC_INC_DIR . '/functions/http.php');
            if (!@is_dir(dirname($cache_path))) {
                w3_require_once(W3TC_INC_DIR . '/functions/file.php');
                w3_mkdir_from(dirname($cache_path), W3TC_CACHE_DIR);
            }
            w3_download($url, $cache_path);
        }

        return (file_exists($cache_path) ? $this->_get_minify_source($cache_path, $url) : false);
    }

    /**
     * Returns minify source
     *
     * @param $file_path
     * @param $url
     * @return Minify_Source
     */
    function _get_minify_source($file_path, $url) {
        w3_require_once(W3TC_LIB_MINIFY_DIR . '/Minify/Source.php');

        return new Minify_Source(array(
                                      'filepath' => $file_path,
                                      'minifyOptions' => array(
                                          'prependRelativePath' => $url
                                      )
                                 ));
    }

    /**
     * Returns minify cache object
     *
     * @return object
     */
    function _get_cache() {
        static $cache = array();

        if (!isset($cache[0])) {
            switch ($this->_config->get_string('minify.engine')) {
                case 'memcached':
                    w3_require_once(W3TC_LIB_W3_DIR . '/Cache/Memcached.php');
                    w3_require_once(W3TC_LIB_MINIFY_DIR . '/Minify/Cache/Memcache.php');
                    $w3_cache_memcached = new W3_Cache_Memcached(array('blog_id' => w3_get_blog_id(),
                                                                        'instance_id' => w3_get_instance_id(),
                                                                        'host' =>  w3_get_host(),
                                                                        'module' => 'minify',
                                                                         'servers' => $this->_config->get_array('minify.memcached.servers'),
                                                                         'persistant' => $this->_config->get_boolean('minify.memcached.persistant')
                                                                    ));
                    $cache[0] = new Minify_Cache_Memcache($w3_cache_memcached, 0 , w3_get_blog_id(), w3_get_instance_id());
                    break;

                case 'apc':
                    w3_require_once(W3TC_LIB_MINIFY_DIR . '/Minify/Cache/APC.php');
                    $cache[0] = new Minify_Cache_APC(0, w3_get_blog_id(), w3_get_instance_id());
                    break;

                case 'eaccelerator':
                    w3_require_once(W3TC_LIB_MINIFY_DIR . '/Minify/Cache/Eaccelerator.php');
                    $cache[0] = new Minify_Cache_Eaccelerator(0, w3_get_blog_id(), w3_get_instance_id());
                    break;

                case 'xcache':
                    w3_require_once(W3TC_LIB_MINIFY_DIR . '/Minify/Cache/XCache.php');
                    $cache[0] = new Minify_Cache_XCache(0, w3_get_blog_id(), w3_get_instance_id());
                    break;

                case 'wincache':
                    w3_require_once(W3TC_LIB_MINIFY_DIR . '/Minify/Cache/Wincache.php');
                    $cache[0] = new Minify_Cache_Wincache(0, w3_get_blog_id(), w3_get_instance_id());
                    break;

                case 'file':
                default:
                    w3_require_once(W3TC_LIB_MINIFY_DIR . '/Minify/Cache/File.php');

                    $cache[0] = new Minify_Cache_File(
                        w3_cache_blog_dir('minify'),
                        array(
                             '.htaccess',
                             'index.php',
                             '*.old'
                        ),
                        $this->_config->get_boolean('minify.file.locking'),
                        $this->_config->get_integer('timelimit.cache_flush'),
                        (w3_get_blog_id() == 0 ? W3TC_CACHE_MINIFY_DIR : null)
                    );
                    break;
            }
        }

        return $cache[0];
    }

    /**
     * Handle minify error
     *
     * @param string $error
     * @return void
     */
    function _handle_error($error) {
        $notification = $this->_config_admin->get_string('minify.error.notification');

        if ($notification) {
            $file = W3_Request::get_string('file');
            if ($file) {
                $this->_config_admin->set('minify.error.file', $file);
            }

            if (stristr($notification, 'admin') !== false) {
                $this->_config_admin->set('minify.error.last', $error);
                $this->_config_admin->set('notes.minify_error', true);
            }

            if (stristr($notification, 'email') !== false) {
                $last = $this->_config_admin->get_integer('minify.error.notification.last');

                /**
                 * Prevent email flood: send email every 5 min
                 */
                if ((time() - $last) > 300) {
                    $this->_config_admin->set('minify.error.notification.last', time());
                    $this->_send_notification();
                }
            }

            $this->_config_admin->save();
        }
    }

    /**
     * Send E-mail notification when error occurred
     *
     * @return boolean
     */
    function _send_notification() {
        $from_email = 'wordpress@' . w3_get_domain($_SERVER['SERVER_NAME']);
        $from_name = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
        $to_name = $to_email = get_option('admin_email');
        $body = @file_get_contents(W3TC_INC_DIR . '/email/minify_error_notification.php');

        $headers = array(
            sprintf('From: "%s" <%s>', addslashes($from_name), $from_email),
            sprintf('Reply-To: "%s" <%s>', addslashes($to_name), $to_email),
            'Content-Type: text/html; charset=UTF-8'
        );

        @set_time_limit($this->_config->get_integer('timelimit.email_send'));

        $result = @wp_mail($to_email, 'W3 Total Cache Error Notification', $body, implode("\n", $headers));

        return $result;
    }

    /**
     * Generates file ID
     *
     * @param array $sources
     * @param string $type
     * @return string
     */
    function _generate_id($sources, $type) {
        $values =array();
        foreach ($sources as $source)
            if (is_string($source))
                $values[] = $source;
            else
                $values[] = $source->filepath;
        foreach ($sources as $source) {
            if (is_string($source) && file_exists($source)) {
                $data = @file_get_contents($source);

                if ($data !== false) {
                    $values[] = md5($data);
                } else {
                    return false;
                }
            } else {
                $headers = @get_headers($source->minifyOptions['prependRelativePath']);
                if(strpos($headers[0],'200') !== false) {
                    $segments = explode('.', $source->minifyOptions['prependRelativePath']);
                    $ext = strtolower(array_pop($segments));
                    $pc_source = $this->_precache_file($source->minifyOptions['prependRelativePath'], $ext);
                    $data = @file_get_contents($pc_source->filepath);

                    if ($data !== false) {
                        $values[] = md5($data);
                    } else {
                        return false;
                    }
                }else {
                    return false;
                }
            }
        }

        $keys = array(
            'minify.debug',
            'minify.engine',
            'minify.options',
            'minify.symlinks',
        );

        if ($type == 'js') {
            $engine = $this->_config->get_string('minify.js.engine');

            switch ($engine) {
                case 'js':
                    $keys = array_merge($keys, array(
                                                    'minify.js.combine.header',
                                                    'minify.js.combine.body',
                                                    'minify.js.combine.footer',
                                                    'minify.js.strip.comments',
                                                    'minify.js.strip.crlf',
                                               ));
                    break;

                case 'yuijs':
                    $keys = array_merge($keys, array(
                                                    'minify.yuijs.options.line-break',
                                                    'minify.yuijs.options.nomunge',
                                                    'minify.yuijs.options.preserve-semi',
                                                    'minify.yuijs.options.disable-optimizations',
                                               ));
                    break;

                case 'ccjs':
                    $keys = array_merge($keys, array(
                                                    'minify.ccjs.options.compilation_level',
                                                    'minify.ccjs.options.formatting',
                                               ));
                    break;
            }
        } elseif ($type == 'css') {
            $engine = $this->_config->get_string('minify.css.engine');

            switch ($engine) {
                case 'css':
                    $keys = array_merge($keys, array(
                                                    'minify.css.combine',
                                                    'minify.css.strip.comments',
                                                    'minify.css.strip.crlf',
                                                    'minify.css.imports',
                                               ));
                    break;

                case 'yuicss':
                    $keys = array_merge($keys, array(
                                                    'minify.yuicss.options.line-break',
                                               ));
                    break;

                case 'csstidy':
                    $keys = array_merge($keys, array(
                                                    'minify.csstidy.options.remove_bslash',
                                                    'minify.csstidy.options.compress_colors',
                                                    'minify.csstidy.options.compress_font-weight',
                                                    'minify.csstidy.options.lowercase_s',
                                                    'minify.csstidy.options.optimise_shorthands',
                                                    'minify.csstidy.options.remove_last_;',
                                                    'minify.csstidy.options.case_properties',
                                                    'minify.csstidy.options.sort_properties',
                                                    'minify.csstidy.options.sort_selectors',
                                                    'minify.csstidy.options.merge_selectors',
                                                    'minify.csstidy.options.discard_invalid_properties',
                                                    'minify.csstidy.options.css_level',
                                                    'minify.csstidy.options.preserve_css',
                                                    'minify.csstidy.options.timestamp',
                                                    'minify.csstidy.options.template',
                                               ));
                    break;
            }
        }

        foreach ($keys as $key) {
            $values[] = $this->_config->get($key);
        }

        $id = substr(md5(implode('', $this->_flatten_array($values))), 0, 6);

        return $id;
    }

    /**
     * Takes a multidimensional array and makes it singledimensional
     *
     * @param $values
     * @return array
     */
    private function _flatten_array($values) {
        $flatten = array();

        foreach ($values as $key => $value) {
            if (is_array($value))
                $flatten = array_merge($flatten, $this->_flatten_array($value));
            else
                $flatten[$key] = $value;
        }
        return $flatten;
    }

    /**
     * Returns cache data
     *
     * @param string $key
     * @return bool|array
     */
    function _cache_get($key) {
        $cache = $this->_get_cache();

        $data = $cache->fetch($key);

        if ($data) {
            $value = @unserialize($data);

            return $value;
        }

        return false;
    }

    /**
     * Sets cache date
     *
     * @param string $key
     * @param string $value
     * @return boolean
     */
    function _cache_set($key, $value) {
        $cache = $this->_get_cache();

        return $cache->store($key, serialize($value));
    }


    /**
     * Compresses an array of files into a filename containing all files.
     * If filename length exceeds 246 characters or value defined in minify.auto.filename_length when gzcompress/gzdeflate is available
     * multiple compressed filenames will be returned
     * @param $files
     * @param $type
     * @return array
     */
    function compress_minify_files($files, $type) {
        $optimized = array();
        foreach ($files as $file) {
            // from PHP.net
            $pattern = '/\w+\/\.\.\//';
            while(preg_match($pattern, $file)) {
                $file = preg_replace($pattern, '', $file);
            }
            if (!w3_is_url($file))
                $optimized[] = dirname($file) . '/' . basename($file, '.' . $type);
            else
                $optimized[] = $file;
        }
        $input = array();
        $replace = $this->_minify_path_replacements();
        $replaced = false;
        foreach ($optimized as $file) {
            foreach($replace as $key => $path) {
                if (strpos($file, $path) === 0) {
                    $input[] =  str_replace($path, $key, $file);
                    $replaced = true;
                    break;
                }
            }
            if ($replaced)
                $replaced = false;
            else
                $input[] = $file;
        }

        $minify_filename = array();

        $imploded = implode(',',$input);
        $config = w3_instance('W3_Config');
        if (!W3TC_WIN) {
            $fn_length = $config->get_integer('minify.auto.filename_length',246);
            $fn_length = $fn_length>246 ? 246 : $fn_length;
        } else {
            $dir = w3_cache_blog_dir('minify');
            $fn_length = 246-strlen($dir);
        }
        $compressed = $this->_compress($imploded);
        if (strlen($compressed) >= $fn_length) {
            $arr_chunks = $this->_combine_and_check_filenames($input, $fn_length);
            foreach ($arr_chunks as $part) {
                $part_imploded = implode(',', $part);
                $base = rtrim(strtr(base64_encode($this->_compress($part_imploded)), '+/', '-_'), '=');
                $minify_filename[] = $base . '.' . $type;
            }
        } else {
            $base = rtrim(strtr(base64_encode($compressed), '+/', '-_'), '=');
            $minify_filename[] = $base . '.' . $type;
        }
        return $minify_filename;
    }

    /**
     * Make a a list where each value is an array that consists of length verified filenames
     * @param string[] $filename_list
     * @param int $length maximum length of imploded filenames
     * @return array
     */
    private function _combine_and_check_filenames($filename_list, $length) {
        $parts = array();
        $place = 0;
        foreach($filename_list as $file) {
            if (strlen($this->_compress($file)) > $length) {
                $this->error('Url/Filename is too long: ' . $file . '. Max length is ' . $length);
                return array();
            }
            if (!isset($parts[$place]))
                $parts[$place] = array();
            $temp = implode(',',$parts[$place]) .',' . $file;
            if (strlen($this->_compress(trim($temp,',')))>$length) {
                $place++;
                $parts[$place][] = $file;
                $place++;
            } else {
                $parts[$place][] = $file;
            }
        }
        return $parts;
    }

    /**
     * Uncompresses a minify auto filename into an array of files.
     * @param $compressed
     * @param $type
     * @return array
     */
    function uncompress_minify_files($compressed, $type) {
        $no_type_files = array();
        $compressed = basename($compressed, '.' . $type);
        $uncompressed =$this->_uncompress(base64_decode(strtr($compressed, '-_', '+/')));

        $exploded = explode(',', $uncompressed);
        $replacements = $this->_minify_path_replacements();
        foreach($exploded as $file) {
            if (!w3_is_url($file)) {
                $prefix = substr($file,0,1);
                $after_pre = substr($file, 1, 1);
                if (isset($replacements[$prefix]) && ($after_pre == '/')) {
                    $file = $replacements[$prefix].substr($file,1);
                    $no_type_files[] = $file;
                } else {
                    $no_type_files[] = $file;
                }
            } else
                $no_type_files[] = $file;
        }

        $files = array();

        foreach ($no_type_files as $no_type_file) {
            $file = !w3_is_url($no_type_file) ? $no_type_file . '.' . $type : $no_type_file;
            $verified = false;
            if (w3_is_url($file)) {
                $external = $this->_config->get_array('minify.cache.files');
                foreach($external as $ext) {
                    if(preg_match('#'.w3_get_url_regexp($ext).'#',$file) && !$verified){
                        $verified = true;
                    }
                }
                if (!$verified) {
                    $this->error(sprintf('Remote file not in external files/libraries list: "%s"', $file));
                }
            } elseif (  /* no .. */  strpos($file, '..') != false
                    // no "//"
                    || strpos($file, '//') !== false
                    // no "\"
                    || (strpos($file, '\\') !== false && strtoupper(substr(PHP_OS, 0, 3)) != 'WIN')
                    // no "./"
                    || preg_match('/(?:^|[^\\.])\\.\\//', $file)
                    /* no unwanted chars */ ||
                    !preg_match('/^[a-zA-Z0-9_.\\/-]|[\\\\]+$/', $file)) {
                $verified = false;
                $this->error(sprintf('File path invalid: "%s"', $file));
            } else {
                $verified = true;
            }

            if ($verified) {
                $files[] = $file;
            }
        }

        return $files;
    }

    /**
     * Paths used to minify minifyfile paths
     * @return array
     */
    private function _minify_path_replacements() {
        $theme = get_theme_root();

        return array(
            ltrim(str_replace(w3_get_document_root(), '', w3_path($theme)), '/'),
            ltrim(str_replace(w3_get_document_root(), '', w3_path(WP_PLUGIN_DIR)), '/'),
            ltrim(str_replace(w3_get_document_root(), '', w3_path(WPMU_PLUGIN_DIR)), '/'),
            WPINC . '/js/jquery',
            WPINC . '/js',
            WPINC . '/css',
            WPINC
        );
    }

    /**
     * Compresses a string using inorder of availability.
     * gzdeflate
     * gzcompress
     * none
     * @param $string
     * @return string
     */
    private function _compress($string) {
        if (function_exists('gzdeflate'))
            return gzdeflate($string, 9);
        if (function_exists('gzcompress'))
            return gzcompress($string, 9);
        return $string;
    }

    /**
     * Uncompresses a string using inorder of availability.
     * gzinflate
     * gzuncompress
     * none
     * @param $string
     * @return string
     */
    private function _uncompress($string) {
        if (function_exists('gzinflate'))
            return gzinflate($string);
        if (function_exists('gzuncompress'))
            return gzuncompress($string);
        return $string;
    }
}
