<?php

if ( !class_exists('SimplePie') )
	require_once (ABSPATH . WPINC . '/class-simplepie.php');

class WP_Feed_Cache extends SimplePie_Cache {
	/**
	 * Don't call the constructor. Please.
	 *
	 * @access private
	 */
	function WP_Feed_Cache() {
		trigger_error('Please call SimplePie_Cache::create() instead of the constructor', E_USER_ERROR);
	}

	/**
	 * Create a new SimplePie_Cache object
	 *
	 * @static
	 * @access public
	 */
	function create($location, $filename, $extension) {
		return new WP_Feed_Cache_Transient($location, $filename, $extension);
	}
}

class WP_Feed_Cache_Transient {
	var $name;
	var $mod_name;
	var $lifetime = 43200; //Default lifetime in cache of 12 hours

	function WP_Feed_Cache_Transient($location, $filename, $extension) {
		$this->name = 'feed_' . $filename;
		$this->mod_name = 'feed_mod_' . $filename;
		$this->lifetime = apply_filters('wp_feed_cache_transient_lifetime', $this->lifetime, $filename);
	}

	function save($data) {
		if ( is_a($data, 'SimplePie') )
			$data = $data->data;

		set_transient($this->name, $data, $this->lifetime);
		set_transient($this->mod_name, time(), $this->lifetime);
		return true;
	}

	function load() {
		return get_transient($this->name);
	}

	function mtime() {
		return get_transient($this->mod_name);
	}

	function touch() {
		return set_transient($this->mod_name, time(), $this->lifetime);
	}

	function unlink() {
		delete_transient($this->name);
		delete_transient($this->mod_name);
		return true;
	}
}

class WP_SimplePie_File extends SimplePie_File {

	function WP_SimplePie_File($url, $timeout = 10, $redirects = 5, $headers = null, $useragent = null, $force_fsockopen = false) {
		$this->url = $url;
		$this->timeout = $timeout;
		$this->redirects = $redirects;
		$this->headers = $headers;
		$this->useragent = $useragent;

		$this->method = SIMPLEPIE_FILE_SOURCE_REMOTE;

		if ( preg_match('/^http(s)?:\/\//i', $url) ) {
			$args = array( 'timeout' => $this->timeout, 'redirection' => $this->redirects);

			if ( !empty($this->headers) )
				$args['headers'] = $this->headers;

			if ( SIMPLEPIE_USERAGENT != $this->useragent ) //Use default WP user agent unless custom has been specified
				$args['user-agent'] = $this->useragent;

			$res = wp_remote_request($url, $args);

			if ( is_wp_error($res) ) {
				$this->error = 'WP HTTP Error: ' . $res->get_error_message();
				$this->success = false;
			} else {
				$this->headers = $res['headers'];
				$this->body = $res['body'];
				$this->status_code = $res['response']['code'];
			}
		} else {
			if ( ! $this->body = file_get_contents($url) ) {
				$this->error = 'file_get_contents could not read the file';
				$this->success = false;
			}
		}
	}
}