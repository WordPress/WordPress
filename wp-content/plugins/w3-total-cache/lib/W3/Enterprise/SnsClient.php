<?php

/**
 * Purge using AmazonSNS object
 */

w3_require_once(W3TC_LIB_W3_DIR . '/Enterprise/SnsBase.php');

/**
 * Class W3_Sns
 */
class W3_Enterprise_SnsClient extends W3_Enterprise_SnsBase {

    private $messages = array();
    private $messages_by_signature = array();
    private $send_action_configured = false;

    /**
     * Sends subscription request
     */
    function subscribe($url, $topic_arn) {
        $this->_log('Sending subscription to ' . $topic_arn . ' ' . $url);
        
        $response = $this->_get_api()->subscribe($topic_arn, 'http', $url);
        if (!$response->isOK())
            throw new Exception('Subscription failed');
    }
    
    /**
     * Flushes DB caches
     *
     */
    function dbcache_flush() {
        $this->_prepare_message(array('action' => 'dbcache_flush'));
    }
    
    /**
     * Flushes minify caches
     *
     */
    function minifycache_flush() {
        $this->_prepare_message(array('action' => 'minifycache_flush'));
    }

    /**
     * Flushes object caches
     *
     */
    function objectcache_flush() {
        $this->_prepare_message(array('action' => 'objectcache_flush'));
    }

    /**
     * Flushes fragment caches
     *
     */
    function fragmentcache_flush() {
        $this->_prepare_message(array('action' => 'fragmentcache_flush'));
    }

    /**
     * Flushes fragment cache based on group
     *
     */
    function fragmentcache_flush_group($group, $global = false) {
        $this->_prepare_message(array('action' => 'fragmentcache_flush_group', 'group' => $group, 'global' => $global));
    }

    /**
     * Flushes query string
     *
     */
    function browsercache_flush() {
        $this->_prepare_message(array('action' => 'browsercache_flush'));
    }
    
    /**
     * Purges Files from Varnish (If enabled) and CDN
     *
     */
    function cdn_purge_files($purgefiles) {
        $this->_prepare_message(array('action' => 'cdn_purge_files', 'purgefiles' => $purgefiles));
    }

    /**
     * Flushes all caches
     * @return boolean
     */
    function pgcache_flush() {
        return $this->_prepare_message(array('action' => 'pgcache_flush'));
    }

    /**
     * Flushes post cache
     *
     * @param integer $post_id
     * @return boolean
     */
    function pgcache_flush_post($post_id) {
        return $this->_prepare_message(
            array('action' => 'pgcache_flush_post', 'post_id' => $post_id));
    }

    /**
     * Flushes post cache
     *
     * @param string $url
     * @return boolean
     */
    function pgcache_flush_url($url) {
        return $this->_prepare_message(
            array('action' => 'pgcache_flush_url', 'url' => $url));
    }
    
    /**
     * Performs garbage collection on the pgcache
     */
    function pgcache_cleanup() {
        $this->_prepare_message(array('action' => 'pgcache_cleanup'));
    }

    /**
     * Purges post from varnish cache
     * @param $post_id
     * @return mixed
     */
    function varnish_flush_post($post_id) {
        return $this->_prepare_message(array('action' => 'varnish_flush_post', 'post_id' => $post_id));
    }

    /**
     * Purges url from varnish cache
     * @param string $url
     * @return mixed
     */
    function varnish_flush_url($url) {
        return $this->_prepare_message(array('action' => 'varnish_flush_url', 'url' => $url));
    }

    /**
     * Purges varnish cache
     * @return mixed
     * @return boolean
     */
    function varnish_flush() {
        return $this->_prepare_message(array('action' => 'varnish_flush'));
    }

    /**
     * Purges post from CDN cache
     * @param $post_id
     * @return boolean
     */
    function cdncache_purge_post($post_id) {
        return $this->_prepare_message(array('action' => 'cdncache_purge_post', 'post_id' => $post_id));
    }

    /**
     * Purge CDN cache
     */
    function cdncache_purge() {
        return $this->_prepare_message(array('action' => 'cdncache_purge'));
    }

    /**
     * Purges post from CDN cache
     * @param $url
     * @return boolean
     */
    function cdncache_purge_url($url) {
        return $this->_prepare_message(array('action' => 'cdncache_purge_url', 'url' => $url));
    }

    /**
     * Flushes the system APC
     * @return bool
     */
    function apc_system_flush() {
        $this->_prepare_message(array('action' => 'apc_system_flush'));
    }

    /**
     * Reloads/compiles a PHP file.
     * @param string $filename
     * @return mixed
     */
    function apc_reload_file($filename) {
        return $this->_prepare_message(array('action' => 'apc_reload_file', 'filename' => $filename));
    }

    /**
     * Reloads/compiles a PHP file.
     * @param string[] $filenames
     * @return mixed
     */
    function apc_reload_files($filenames) {
        return $this->_prepare_message(array('action' => 'apc_reload_files', 'filenames' => $filenames));
    }


    /**
     * Deletes files based on regular expression matching.
     * @param string $mask
     * @return mixed
     */
    function apc_delete_files_based_on_regex($mask) {
        return $this->_prepare_message(array('action' => 'apc_delete_files_based_on_regex', 'regex' => $mask));
    }

    /**
     * Purges/Flushes post from page caches, varnish and cdncache
     * @param $post_id
     * @return boolean
     */
    function flush_post($post_id) {
        return $this->_prepare_message(array('action' => 'flush_post', 'post_id' => $post_id));
    }

    /**
     * Purges/Flushes page caches, varnish and cdncache
     * @return boolean
     */
    function flush() {
        return $this->_prepare_message(array('action' => 'flush'));
    }

    /**
     * Purges/Flushes all enabled caches
     * @return boolean
     */
    function flush_all() {
        return $this->_prepare_message(array('action' => 'flush_all'));
    }

    /**
     * Purges/Flushes url from page caches, varnish and cdncache
     * @param string $url
     * @return boolean
     */
    function flush_url($url) {
        return $this->_prepare_message(array('action' => 'flush_url', 'url' => $url));
    }

    /**
     * Makes get request to url specific to post, ie permalinks
     * @param $post_id
     * @return mixed
     */
    function prime_post($post_id) {
        return $this->_prepare_message(array('action' => 'prime_post', 'post_id' => $post_id));
    }

    /**
     * Setups message list and if it should be combined or separate
     * @param $message
     * @return boolean
     */
    private function _prepare_message($message) {
        $message_signature = json_encode($message);
        if (isset($this->messages_by_signature[$message_signature]))
            return true;
        $this->messages_by_signature[$message_signature] = '*';
        $this->messages[] = $message;

        $action = $this->_get_action();
        if (!$action) {
            $this->send_messages();
            return true;
        }
        
        if (!$this->send_action_configured) {
            add_action('w3_redirect', array(
                &$this,
                'send_messages_w3_redirect'
            ), 100000, 0);
            add_filter('wp_redirect', array(
                &$this,
                'send_messages_wp_redirect'
            ), 100000, 1);
            add_action('shutdown', array(
                &$this,
                'send_messages'
            ), 100000, 0);
    
            $this->send_action_configured = true;
        }
        
        return true;
    }

    /**
     * Sends messages stored in $messages
     *
     * @return boolean
     */
    public function send_messages() {
        if (count($this->messages) <= 0)
            return true;
        
        $this->_log($this->_get_action() . ' sending messages');
        
        $message = array();
        $message['actions'] = $this->messages;
        $message['blog_id'] = w3_get_blog_id();
        $message['host'] = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : null;
        $message['hostname'] = @gethostname();
        $v = json_encode($message);

        try {
            $api = $this->_get_api();
            if (defined('WP_CLI') && WP_CLI)
                $origin = 'WP CLI';
            else
                $origin = 'WP';
            $this->_log($origin . ' sending message ' . $v);
            $this->_log('Host: ' . $message['host']);
            if (isset($_SERVER['REQUEST_URI']))
                $this->_log('URL: ' . $_SERVER['REQUEST_URI']);
            if (function_exists('current_filter'))
                $this->_log('Current WP hook: ' . current_filter());
            
            $backtrace = debug_backtrace();
            $backtrace_optimized = array();
            foreach ($backtrace as $b) {
                $opt = isset($b['function']) ? $b['function'] . ' ' : '';
                $opt .= isset($b['file']) ? $b['file'] . ' ' : '';
                $opt .= isset($b['line']) ?  '#' . $b['line'] . ' ' : '';
                $backtrace_optimized[] = $opt;

            }
            $this->_log('Backtrace ', $backtrace_optimized);
            
            $r = $api->publish($this->_topic_arn, $v);
            if ($r->status != 200) {
                $this->_log("Error: {$r->body->Error->Message}");
                return false;
            }
        } catch (Exception $e) {
            $this->_log('Error ' . $e->getMessage());
            return false;
        }

        // on success - reset messages array, but not hash (not resent repeatedly the same messages)
        $this->messages = array();
        
        return true;
    }

    /**
     * Send messages on wp_redirect
     * @param $location
     * @return mixed
     */
    public function send_messages_wp_redirect($location) {
        $this->_log($this->_get_action() . ' sending messages wp_redirect');
        $this->send_messages();
        return $location;
    }

    /**
     * Send messages on w3_redirect
     */
    public function send_messages_w3_redirect() {
        $this->_log($this->_get_action() . ' sending messages w3_redirect');
        $this->send_messages();
    }

    /**
     * Gets the current running WP action if any. Returns empty string if not found.
     * @return string
     */
    private function _get_action() {
        $action = '';
        if (function_exists('current_filter'))
            $action = current_filter();
        return $action;
    }
}
