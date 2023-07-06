<?php

if (!defined('ABSPATH'))
    die('No direct access allowed');


final class WoofTextCache {

    private $cache_key = 'wts';

    public function __construct() {

    }
    public function create_key($args) {
		return $this->cache_key . md5(json_encode($args));
	}
	public function set($key, $value) {
		wp_cache_set($key, $value );
    }

    public function get($key) {

		return wp_cache_get( $key );
    }

    public function is($key) {
		$data = $this->get($key);
		if (false == $data) {
			return false;		
		} else {
			return false;
		}
    }

    private function normalize_key($key) {
        return substr($key, 7, 23);
    }
}
