<?php

if ( ! class_exists( 'Endurance_Page_Cache' ) ) {
	return;
}

// https://github.com/bluehost/endurance-page-cache
class ET_Core_LIB_BluehostCache extends Endurance_Page_Cache {

	private static $_instance;

	public function __construct() {
		$this->purged       = array();
		$this->trigger      = null;
		$this->cache_level  = get_option( 'endurance_cache_level', 2 );
		$this->cache_dir    = WP_CONTENT_DIR . '/endurance-page-cache';
		$this->cache_exempt = array( 'wp-admin', '.', 'checkout', 'cart', 'wp-json', '%', '=', '@', '&', ':', ';', );
	}

	public static function get_instance() {
		if ( null === self::$_instance ) {
			self::$_instance = new self;
		}

		return self::$_instance;
	}

	public function clear( $post_id = '' ) {
		'' !== $post_id ? $this->purge_single( get_the_permalink( $post_id ) ) : $this->purge_all();
	}
}
