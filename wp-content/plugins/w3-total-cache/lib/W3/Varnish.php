<?php

/**
 * Varnish purge object
 */

/**
 * Class W3_Varnish
 */
class W3_Varnish {
    /**
     * Debug flag
     *
     * @var bool
     */
    var $_debug = false;

    /**
     * Varnish servers
     *
     * @var array
     */
    var $_servers = array();

    /**
     * Operation timeout
     *
     * @var int
     */
    var $_timeout = 30;

    /**
     * Advanced cache config
     *
     * @var W3_Config
     */
    var $_config = null;

    /**
     * PHP5-style constructor
     */
    function __construct() {
        $this->_config = w3_instance('W3_Config');

        $this->_debug = $this->_config->get_boolean('varnish.debug');
        $this->_servers = $this->_config->get_array('varnish.servers');
        $this->_timeout = $this->_config->get_integer('timelimit.varnish_purge');
    }

    /**
     * Purge URI
     *
     * @param string $url
     * @return boolean
     */
    protected function _purge($url) {
        w3_require_once(W3TC_INC_DIR . '/functions/http.php');

        @set_time_limit($this->_timeout);

        foreach ((array) $this->_servers as $server) {
            $response = $this->_request($server, $url);

            if (is_wp_error($response)) {
                $this->_log($url, sprintf('Unable to send request: %s.', implode('; ', $response->get_error_messages())));

                return false;
            }

            if ($response['response']['code'] !== 200) {
                $this->_log($url, 'Bad response code: ' . $response['response']['code']);

                return false;
            }

            $this->_log($url, 'PURGE OK');
        }

        return true;
    }

    /*
     * Sends purge request. Cannt use default wp HTTP implementation
     * if we send request to different host than specified in $url
     * 
     * @param $url string
     */
    function _request($varnish_server, $url) {
        $parse_url = @parse_url($url);

        if (!$parse_url || !isset($parse_url['host']))
            return new WP_Error('http_request_failed', 'Unrecognized URL format ' . $url);
        
        $host = $parse_url['host'];
        $port = (isset($parse_url['port']) ? (int) $parse_url['port'] : 80);
        $path = (!empty($parse_url['path']) ? $parse_url['path'] : '/');
        $query = (isset($parse_url['query']) ? $parse_url['query'] : '');
        $request_uri = $path . ($query != '' ? '?' . $query : '');

        if (strpos($varnish_server, ':'))
            list($varnish_host, $varnish_port) = explode(':', $varnish_server);
        else {
            $varnish_host = $varnish_server;
            $varnish_port = 80;
        }

        // if url host is the same as varnish server - we can use regular
        // wordpress http infrastructure, otherwise custom request should be 
        // sent using fsockopen, since we send request to other server than
        // specified by $url 
        if ($host == $varnish_host && $port == $varnish_port)
            return w3_http_request($url, array('method' => 'PURGE'));
        
        $request_headers_array = array(
            sprintf('PURGE %s HTTP/1.1', $request_uri),
            sprintf('Host: %s', $host),
            sprintf('User-Agent: %s', W3TC_POWERED_BY),
            'Connection: close'
        );

        $request_headers = implode("\r\n", $request_headers_array);
        $request = $request_headers . "\r\n\r\n";

        // log what we are about to do
        $this->_log($url, sprintf('Connecting to %s ...', $varnish_host));
        $this->_log($url, sprintf('PURGE %s HTTP/1.1', $request_uri));
        $this->_log($url, sprintf('Host: %s', $host));
        
        $errno = null;
        $errstr = null;
        $fp = @fsockopen($varnish_host, $varnish_port, $errno, $errstr, 10);
        if (!$fp)
            return new WP_Error('http_request_failed', $errno . ': ' . $errstr);

        @stream_set_timeout($fp, 60);

        @fputs($fp, $request);

        $response = '';
        while (!@feof($fp))
            $response .= @fgets($fp, 4096);

        @fclose($fp);

        list($response_headers, $contents) = explode("\r\n\r\n", $response, 2);
        $matches = null;
        if (preg_match('~^HTTP/1.[01] (\d+)~', $response_headers, $matches)) {
            $status = (int)$matches[1];
            $return = array(
                'response' => array(
                    'code' => $status));
            return $return;
        }

        return new WP_Error('http_request_failed', 
            'Unrecognized response header' . $response_headers);
    }
    
    /**
     * Write log entry
     *
     * @param string $url
     * @param string $msg
     * @return bool|int
     */
    function _log($url, $msg) {
        if ($this->_debug) {
            $data = sprintf("[%s] [%s] %s\n", date('r'), $url, $msg);
            $data = strtr($data, '<>', '..');

            $filename = w3_debug_log('varnish');

            return @file_put_contents($filename, $data, FILE_APPEND);
        }

        return true;
    }
}
