<?php

class Woo_Directive_Store {
	private static $store = array();

	static function get_data() {
		return self::$store;
	}

	static function merge_data( $data ) {
		self::$store = array_replace_recursive( self::$store, $data );
	}

	static function serialize() {
		return json_encode( self::$store );
	}

	static function reset() {
		self::$store = array();
	}

	static function render() {
		if ( empty( self::$store ) ) {
			return;
		}

		$id    = 'store';
		$store = self::serialize();
		echo "<script id=\"$id\" type=\"application/json\">$store</script>";
	}
}
