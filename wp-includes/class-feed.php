<?php

if ( !class_exists('SimplePie') )
	require_once (ABSPATH . WPINC . '/class-simplepie.php');

class WP_Feed_Cache extends SimplePie_Cache {
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

	function __construct($location, $filename, $extension) {
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

	function __construct($url, $timeout = 10, $redirects = 5, $headers = null, $useragent = null, $force_fsockopen = false) {
		$this->url = $url;
		$this->timeout = $timeout;
		$this->redirects = $redirects;
		$this->headers = $headers;
		$this->useragent = $useragent;

		$this->method = SIMPLEPIE_FILE_SOURCE_REMOTE;

		if ( preg_match('/^http(s)?:\/\//i', $url) ) {
			$args = array(
				'timeout' => $this->timeout,
				'redirection' => $this->redirects,
				'reject_unsafe_urls' => true,
			);

			if ( !empty($this->headers) )
				$args['headers'] = $this->headers;

			if ( SIMPLEPIE_USERAGENT != $this->useragent ) //Use default WP user agent unless custom has been specified
				$args['user-agent'] = $this->useragent;

			$res = wp_remote_request($url, $args);

			if ( is_wp_error($res) ) {
				$this->error = 'WP HTTP Error: ' . $res->get_error_message();
				$this->success = false;
			} else {
				$this->headers = wp_remote_retrieve_headers( $res );
				$this->body = wp_remote_retrieve_body( $res );
				$this->status_code = wp_remote_retrieve_response_code( $res );
			}
		} else {
			$this->error = '';
			$this->success = false;
		}
	}
}

/**
 * WordPress SimplePie Sanitization Class
 *
 * Extension of the SimplePie_Sanitize class to use KSES, because
 * we cannot universally count on DOMDocument being available
 *
 * @package WordPress
 * @since 3.5.0
 */
class WP_SimplePie_Sanitize_KSES extends SimplePie_Sanitize {
	public function sanitize( $data, $type, $base = '' ) {
		$data = trim( $data );
		if ( $type & SIMPLEPIE_CONSTRUCT_MAYBE_HTML ) {
			if (preg_match('/(&(#(x[0-9a-fA-F]+|[0-9]+)|[a-zA-Z0-9]+)|<\/[A-Za-z][^\x09\x0A\x0B\x0C\x0D\x20\x2F\x3E]*' . SIMPLEPIE_PCRE_HTML_ATTRIBUTE . '>)/', $data)) {
				$type |= SIMPLEPIE_CONSTRUCT_HTML;
			}
			else {
				$type |= SIMPLEPIE_CONSTRUCT_TEXT;
			}
		}
		if ( $type & SIMPLEPIE_CONSTRUCT_BASE64 ) {
			$data = base64_decode( $data );
		}
		if ( $type & ( SIMPLEPIE_CONSTRUCT_HTML | SIMPLEPIE_CONSTRUCT_XHTML ) ) {
			$data = wp_kses_post( $data );
			if ( $this->output_encoding !== 'UTF-8' ) {
				$data = $this->registry->call( 'Misc', 'change_encoding', array( $data, 'UTF-8', $this->output_encoding ) );
			}
			return $data;
		} else {
			return parent::sanitize( $data, $type, $base );
		}
	}
}
