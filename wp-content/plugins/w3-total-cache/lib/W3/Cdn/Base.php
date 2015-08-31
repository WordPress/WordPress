<?php

/**
 * W3 CDN Base class
 */
define('W3TC_CDN_RESULT_HALT', -1);
define('W3TC_CDN_RESULT_ERROR', 0);
define('W3TC_CDN_RESULT_OK', 1);
define('W3TC_CDN_HEADER_NONE', 'none');
define('W3TC_CDN_HEADER_UPLOADABLE', 'uploadable');
define('W3TC_CDN_HEADER_MIRRORING', 'mirroring');

/**
 * Class W3_Cdn_Base
 */
class W3_Cdn_Base {
    /**
     * Engine configuration
     *
     * @var array
     */
    var $_config = array();

    /**
     * Cache config
     * @var array
     */
    var $cache_config = array();

    /**
     * gzip extension
     *
     * @var string
     */
    var $_gzip_extension = '.gzip';

    /**
     * Last error
     *
     * @var string
     */
    var $_last_error = '';

    /**
     * PHP5 Constructor
     *
     * @param array $config
     */
    function __construct($config = array()) {
        $this->_config = array_merge(array(
            'debug' => false,
            'ssl' => 'auto',
            'compression' => false
        ), $config);
    }

    /**
     * Upload files to CDN
     *
     * @param array $files takes array consisting of array(array('local_path'=>'', 'remote_path'=>''))
     * @param array $results
     * @param boolean $force_rewrite
     * @return boolean
     */
    function upload($files, &$results, $force_rewrite = false) {
        $results = $this->_get_results($files, W3TC_CDN_RESULT_HALT, 'Not implemented.');

        return false;
    }

    /**
     * Delete files from CDN
     *
     * @param array $files
     * @param array $results
     * @return boolean
     */
    function delete($files, &$results) {
        $results = $this->_get_results($files, W3TC_CDN_RESULT_HALT, 'Not implemented.');

        return false;
    }

    /**
     * Purge files from CDN
     *
     * @param array $files
     * @param array $results
     * @return boolean
     */
    function purge($files, &$results) {
        return $this->upload($files, $results, true);
    }

    /**
     * Purge CDN completely
     * @param $results
     * @return bool
     */
    function purge_all(&$results) {
        $results = $this->_get_results(array(), W3TC_CDN_RESULT_HALT, 'Not implemented.');

        return false;
    }

    /**
     * Test CDN server
     *
     * @param string $error
     * @return boolean
     */
    function test(&$error) {
        if (!$this->_test_domains($error)) {
            return false;
        }

        return true;
    }

    /**
     * Create bucket / container for some CDN engines
     *
     * @param string $container_id
     * @param string $error
     * @return boolean
     */
    function create_container(&$container_id, &$error) {
        $error = 'Not implemented.';

        return false;
    }

    /**
     * Returns first domain
     *
     * @param string $path
     * @return string
     */
    function get_domain($path = '') {
        $domains = $this->get_domains();
        $count = count($domains);

        if ($count) {
            switch (true) {
                /**
                 * Reserved CSS
                 */
                case (isset($domains[0]) && $this->_is_css($path)):
                    $domain = $domains[0];
                    break;


                /**
                 * Reserved JS after body
                 */
                case (isset($domains[2]) && $this->_is_js_body($path)):
                    $domain = $domains[2];
                    break;

                /**
                 * Reserved JS before /body
                 */
                case (isset($domains[3]) && $this->_is_js_footer($path)):
                    $domain = $domains[3];
                    break;

                /**
                 * Reserved JS in head, moved here due to greedy regex
                 */
                case (isset($domains[1]) && $this->_is_js($path)):
                    $domain = $domains[1];
                    break;

                default:
                    if ($count > 4) {
                        $domain = $this->_get_domain(array_slice($domains, 4), $path);
                    } else {
                        $domain = $this->_get_domain($domains, $path);
                    }
            }

            /**
             * Custom host for SSL
             */
            list($domain_http, $domain_https) = array_map('trim', explode(',', $domain . ','));

            $scheme = $this->_get_scheme();

            switch ($scheme) {
                case 'http':
                    $domain = $domain_http;
                    break;

                case 'https':
                    $domain = ($domain_https ? $domain_https : $domain_http);
                    break;
            }

            return $domain;
        }

        return false;
    }

    /**
     * Returns array of CDN domains
     *
     * @return array
     */
    function get_domains() {
        return array();
    }

    /**
     * Returns via string
     *
     * @return string
     */
    function get_via() {
        $domain = $this->get_domain();

        if ($domain) {
            return $domain;
        }

        return 'N/A';
    }

    /**
     * Formats URL
     *
     * @param string $path
	 * @return string
     */
    function format_url($path) {
        $url = $this->_format_url($path);

        if ($url && $this->_config['compression'] && isset($_SERVER['HTTP_ACCEPT_ENCODING']) && stristr($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== false && $this->_may_gzip($path)) {
            if (($qpos = strpos($url, '?')) !== false) {
                $url = substr_replace($url, $this->_gzip_extension, $qpos, 0);
            } else {
                $url .= $this->_gzip_extension;
            }
        }

        return $url;
    }

    /**
     * Returns prepend path
     *
     * @param string $path
     * @return string
     */
    function get_prepend_path($path) {
        $domain = $this->get_domain($path);

        if ($domain) {
            $scheme = $this->_get_scheme();
            $url = sprintf('%s://%s', $scheme, $domain);

            return $url;
        }

        return false;
    }

    /**
     * Formats URL
     *
     * @param string $path
     * @return string
     */
    function _format_url($path) {
        $domain = $this->get_domain($path);

        if ($domain) {
            $scheme = $this->_get_scheme();
            $url = sprintf('%s://%s/%s', $scheme, $domain, $path);

            return $url;
        }

        return false;
    }

    /**
     * Returns results
     *
     * @param array $files
     * @param integer $result
     * @param string $error
     * @return array
     */
    function _get_results($files, $result = W3TC_CDN_RESULT_OK, $error = 'OK') {
        $results = array();

        foreach ($files as $key => $file) {
            if (is_array($file)) {
                $local_path = $file['local_path'];
                $remote_path = $file['remote_path'];
            } else {
                $local_path = $key;
                $remote_path = $file;
            }

            $results[] = $this->_get_result($local_path, $remote_path, $result, $error);
        }

        return $results;
    }

    /**
     * Returns file process result
     *
     * @param string $local_path
     * @param string $remote_path
     * @param integer $result
     * @param string $error
     * @return array
     */
    function _get_result($local_path, $remote_path, $result = W3TC_CDN_RESULT_OK, $error = 'OK') {
        if ($this->_config['debug']) {
            $this->_log($local_path, $remote_path, $error);
        }

        return array(
            'local_path' => $local_path,
            'remote_path' => $remote_path,
            'result' => $result,
            'error' => $error
        );
    }

    /**
     * Check for errors
     *
     * @param array $results
     * @return bool
     */
    function _is_error($results) {
        foreach ($results as $result) {
            if ($result['result'] != W3TC_CDN_RESULT_OK) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns headers for file
     *
     * @param array $file CDN file array
     * @return array
     */
    function _get_headers($file, $block_expires = false) {
        w3_require_once(W3TC_INC_DIR . '/functions/mime.php');
        $local_path = $file['local_path'];
        $mime_type = w3_get_mime_type($local_path);
        $last_modified = time();

        $link = $file['original_url'];

        $headers = array(
            'Content-Type' => $mime_type,
            'Last-Modified' => w3_http_date($last_modified),
            'Access-Control-Allow-Origin' => '*',
            'Link' => '<' . $link  .'>; rel="canonical"'
        );

        if (isset($this->cache_config[$mime_type])) {
            if ($this->cache_config[$mime_type]['etag']) {
                $headers['Etag'] = @md5_file($local_path);
            }

            if ($this->cache_config[$mime_type]['w3tc']) {
                $headers['X-Powered-By'] = W3TC_POWERED_BY;
            }

            
            $expires_set = false;
            if (!$block_expires && $this->cache_config[$mime_type]['expires']) {
                $headers['Expires'] = w3_http_date(time() + 
                    $this->cache_config[$mime_type]['lifetime']);
                $expires_set = true;
            }

            switch ($this->cache_config[$mime_type]['cache_control']) {
                case 'cache':
                    $headers = array_merge($headers, array(
                        'Pragma' => 'public',
                        'Cache-Control' => 'public'
                    ));
                    break;

                case 'cache_public_maxage':
                    $headers = array_merge($headers, array(
                        'Pragma' => 'public',
                        'Cache-Control' => ($expires_set ? '' : 'max-age=' . $this->cache_config[$mime_type]['lifetime'] .', ') . 'public'
                    ));
                    break;

                case 'cache_validation':
                    $headers = array_merge($headers, array(
                        'Pragma' => 'public',
                        'Cache-Control' => 'public, must-revalidate, proxy-revalidate'
                    ));
                    break;

                case 'cache_noproxy':
                    $headers = array_merge($headers, array(
                        'Pragma' => 'public',
                        'Cache-Control' => 'private, must-revalidate'
                    ));
                    break;

                case 'cache_maxage':
                    $headers = array_merge($headers, array(
                        'Pragma' => 'public',
                        'Cache-Control' => ($expires_set ? '' : 'max-age=' . $this->cache_config[$mime_type]['lifetime'] .', ') . 'public, must-revalidate, proxy-revalidate'
                    ));
                    break;

                case 'no_cache':
                    $headers = array_merge($headers, array(
                        'Pragma' => 'no-cache',
                        'Cache-Control' => 'max-age=0, private, no-store, no-cache, must-revalidate'
                    ));
                    break;
            }
        }

        return $headers;
    }

    /**
     * Use gzip compression only for text-based files
     *
     * @param string $file
     * @return boolean
     */
    function _may_gzip($file) {
        /**
         * Remove query string
         */
        $file = preg_replace('~\?.*$~', '', $file);

        /**
         * Check by file extension
         */
        if (preg_match('~\.(ico|js|css|xml|xsd|xsl|svg|htm|html|txt)$~i', $file)) {
            return true;
        }

        return false;
    }

    /**
     * Test domains
     *
     * @param string $error
     * @return boolean
     */
    function _test_domains(&$error) {
        $domains = $this->get_domains();

        if (!count($domains)) {
            $error = 'Empty hostname / CNAME list.';

            return false;

        }

        foreach ($domains as $domain) {
            $_domains = array_map('trim', explode(',', $domain));

            foreach ($_domains as $_domain) {
                $matches = null;

                if (preg_match('~^([a-z0-9\-\.]*)~i', $_domain, $matches)) {
                    $hostname = $matches[1];
                } else {
                    $hostname = $_domain;
                }

                if (!$hostname) {
                    $error = 'Empty hostname';

                    return false;
                }

                if (gethostbyname($hostname) === $hostname) {
                    $error = sprintf('Unable to resolve hostname: %s.', $hostname);

                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Check if css file
     *
     * @param string $path
     * @return boolean
     */
    function _is_css($path) {
        return preg_match('~[a-zA-Z0-9\-_]*(\.include\.[0-9]+)?\.css$~', $path);
    }

    /**
     * Check if JS file in heeader
     *
     * @param string $path
     * @return boolean
     */
    function _is_js($path) {
        return preg_match('~([a-z0-9\-_]+(\.include\.[a-z0-9]+)\.js)$~', $path) ||
        preg_match('~[\w\d\-_]+\.js~', $path);
    }

    /**
     * Check if JS file after body
     *
     * @param string $path
     * @return boolean
     */
    function _is_js_body($path) {
        return preg_match('~[a-z0-9\-_]+(\.include-body\.[a-z0-9]+)\.js$~', $path);
    }

    /**
     * Check if JS file before /body
     *
     * @param string $path
     * @return boolean
     */
    function _is_js_footer($path) {
        return preg_match('~[a-z0-9\-_]+(\.include-footer\.[a-z0-9]+)\.js$~', $path);
    }

    /**
     * Returns domain for path
     *
     * @param array $domains
     * @param string $path
     * @return string
     */
    function _get_domain($domains, $path) {
        $count = count($domains);

        if ($count) {
            /**
             * Use for equal URLs same host to allow caching by browser
             */
            $hash = $this->_get_hash($path);
            $domain = $domains[$hash % $count];

            return $domain;
        }

        return false;
    }

    /**
     * Returns integer hash for key
     *
     * @param string $key
     * @return integer
     */
    function _get_hash($key) {
        $hash = abs(crc32($key));

        return $hash;
    }

    /**
     * Returns scheme
     *
     * @return string
     */
    function _get_scheme() {
        switch ($this->_config['ssl']) {
            default:
            case 'auto':
                $scheme = (w3_is_https() ? 'https' : 'http');
                break;

            case 'enabled':
                $scheme = 'https';
                break;

            case 'disabled':
                $scheme = 'http';
                break;
            case 'rejected':
                $scheme = 'http';
                break;
        }

        return $scheme;
    }

    /**
     * Write log entry
     *
     * @param string $local_path
     * @param string $remote_path
     * @param string $error
     * @return bool|int
     */
    function _log($local_path, $remote_path, $error) {
        $data = sprintf("[%s] [%s => %s] %s\n", date('r'), $local_path, $remote_path, $error);
        $data = strtr($data, '<>', '..');

        $filename = w3_debug_log('cdn');

        return @file_put_contents($filename, $data, FILE_APPEND);
    }

    /**
     * Our error handler
     *
     * @param integer $errno
     * @param string $errstr
     * @return boolean
     */
    function _error_handler($errno, $errstr) {
        $this->_last_error = $errstr;

        return false;
    }

    /**
     * Returns last error
     *
     * @return string
     */
    function _get_last_error() {
        return $this->_last_error;
    }

    /**
     * Set our error handler
     *
     * @return void
     */
    function _set_error_handler() {
        set_error_handler(array(
            &$this,
            '_error_handler'
        ));
    }

    /**
     * Restore prev error handler
     *
     * @return void
     */
    function _restore_error_handler() {
        restore_error_handler();
    }

    /**
     * If CDN suppost path of type folder/*
     * @return bool
     */
    function supports_folder_asterisk() {
        return false;
    }

    /**
     * How and if headers should be set
     * @return string W3TC_CDN_HEADER_NONE, W3TC_CDN_HEADER_UPLOADABLE, W3TC_CDN_HEADER_MIRRORING
     */
    function headers_support() {
        return W3TC_CDN_HEADER_NONE;
    }

    /**
     * If the CDN supports fullpage mirroring
     * @return bool
     */
    function supports_full_page_mirroring() {
        return false;
    }
}
