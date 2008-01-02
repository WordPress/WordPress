<?php
function wp_cache_add($key, $data, $flag = '', $expire = 0) {
	global $wp_object_cache;

	return $wp_object_cache->add($key, $data, $flag, $expire);
}

function wp_cache_close() {
	global $wp_object_cache;

	if ( ! isset($wp_object_cache) )
		return;
	return $wp_object_cache->save();
}

function wp_cache_delete($id, $flag = '') {
	global $wp_object_cache;

	return $wp_object_cache->delete($id, $flag);
}

function wp_cache_flush() {
	global $wp_object_cache;

	return $wp_object_cache->flush();
}

function wp_cache_get($id, $flag = '') {
	global $wp_object_cache;

	return $wp_object_cache->get($id, $flag);
}

function wp_cache_init() {
	$GLOBALS['wp_object_cache'] =& new WP_Object_Cache();
}

function wp_cache_replace($key, $data, $flag = '', $expire = 0) {
	global $wp_object_cache;

	return $wp_object_cache->replace($key, $data, $flag, $expire);
}

function wp_cache_set($key, $data, $flag = '', $expire = 0) {
	global $wp_object_cache;

	return $wp_object_cache->set($key, $data, $flag, $expire);
}

class WP_Object_Cache {
	var $cache = array ();
	var $non_existant_objects = array ();
	var $global_groups = array ('users', 'userlogins', 'usermeta');
	var $cache_hits = 0;
	var $cache_misses = 0;

	function add($id, $data, $group = 'default', $expire = '') {
		if (empty ($group))
			$group = 'default';

		if (false !== $this->get($id, $group, false))
			return false;

		return $this->set($id, $data, $group, $expire);
	}

	function delete($id, $group = 'default', $force = false) {
		if (empty ($group))
			$group = 'default';

		if (!$force && false === $this->get($id, $group, false))
			return false;

		unset ($this->cache[$group][$id]);
		$this->non_existant_objects[$group][$id] = true;
		return true;
	}

	function flush() {
		$this->cache = array ();

		return true;
	}

	function get($id, $group = 'default') {
		if (empty ($group))
			$group = 'default';

		if (isset ($this->cache[$group][$id])) {
			$this->cache_hits += 1;
			return $this->cache[$group][$id];
		}

		if ( isset ($this->non_existant_objects[$group][$id]) )
			return false;

		$this->non_existant_objects[$group][$id] = true;
		$this->cache_misses += 1;
		return false;
	}

	function replace($id, $data, $group = 'default', $expire = '') {
		if (empty ($group))
			$group = 'default';

		if (false === $this->get($id, $group, false))
			return false;

		return $this->set($id, $data, $group, $expire);
	}

	function set($id, $data, $group = 'default', $expire = '') {
		if (empty ($group))
			$group = 'default';

		if (NULL === $data)
			$data = '';

		$this->cache[$group][$id] = $data;
		unset ($this->non_existant_objects[$group][$id]);

		return true;
	}

	function stats() {
		echo "<p>";
		echo "<strong>Cache Hits:</strong> {$this->cache_hits}<br />";
		echo "<strong>Cache Misses:</strong> {$this->cache_misses}<br />";
		echo "</p>";

		foreach ($this->cache as $group => $cache) {
			echo "<p>";
			echo "<strong>Group:</strong> $group<br />";
			echo "<strong>Cache:</strong>";
			echo "<pre>";
			print_r($cache);
			echo "</pre>";
		}
	}

	function WP_Object_Cache() {
		return $this->__construct();
	}

	function __construct() {
		register_shutdown_function(array(&$this, "__destruct"));
	}

	function __destruct() {
		return true;
	}
}
?>
