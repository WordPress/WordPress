<?php
function wp_cache_add($key, $data, $flag = '', $expire = 0) {
	global $wp_object_cache;

	return $wp_object_cache->add($key, $data, $flag, $expire);
}

function wp_cache_close() {
	global $wp_object_cache;

	return $wp_object_cache->save();
}

function wp_cache_delete($id, $flag = '') {
	global $wp_object_cache;

	return $wp_object_cache->delete($id, $flag);
}

function wp_cache_flush() {
	global $wp_object_cache;
}

function wp_cache_get($id, $flag = '') {
	global $wp_object_cache;

	return $wp_object_cache->get($id, $flag);
}

function wp_cache_init() {
	global $wp_object_cache;

	$wp_object_cache = new WP_Object_Cache();
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
	var $cache_dir;
	var $cache_enabled = false;
	var $use_flock = false;
	var $flock_filename = 'wp_object_cache.lock';
	var $sem_id = 5454;
	var $mutex;
	var $cache = array ();
	var $dirty_objects = array ();
	var $global_groups = array('users', 'usermeta');
	var $blog_id;
	var $cold_cache_hits = 0;
	var $warm_cache_hits = 0;
	var $cache_misses = 0;

	function add($id, $data, $group = 'default', $expire = '') {
		if ( empty($group) )
			$group = 'default';

		if (isset ($this->cache[$group][$id]))
			return false;

		return $this->set($id, $data, $group, $expire);
	}

	function delete($id, $group = 'default') {
		if ( empty($group) )
			$group = 'default';

		if (!isset ($this->cache[$group][$id]))
			return false;

		unset ($this->cache[$group][$id]);
		$this->dirty_objects[$group][] = $id;
		return true;
	}

	function get($id, $group = 'default') {
		if ( empty($group) )
			$group = 'default';

		if (isset ($this->cache[$group][$id])) {
			$this->warm_cache_hits += 1;
			return $this->cache[$group][$id];
		}

		//  If caching is not enabled, we have to fall back to pulling from the DB.
		if (!$this->cache_enabled) {
			if (!isset ($this->cache[$group]))
				$this->load_group_from_db($group);

			if (isset ($this->cache[$group][$id])) {
				$this->cold_cache_hits += 1;
				return $this->cache[$group][$id];
			}

			$this->cache_misses += 1;
			return false;
		}

		$cache_file = $this->cache_dir . $this->get_group_dir($group) . "/" . md5($id . DB_PASSWORD);
		if (!file_exists($cache_file)) {
			$this->cache_misses += 1;
			return false;
		}
		$this->cache[$group][$id] = unserialize(@ file_get_contents($cache_file));
		if ( false === $this->cache[$group][$id])
			$this->cache[$group][$id] = '';
		$this->cold_cache_hits += 1;
		return $this->cache[$group][$id];
	}

	function get_group_dir($group) {
		if ( false !== array_search($group, $this->global_groups) )
			return $group;

		return "{$this->blog_id}/$group";
	}

	function load_group_from_db($group) {
		global $wpdb;

		if ('category' == $group) {
			$this->cache['category'] = array ();
			if ($dogs = $wpdb->get_results("SELECT * FROM $wpdb->categories")) {
				foreach ($dogs as $catt)
					$this->cache['category'][$catt->cat_ID] = $catt;

				foreach ($this->cache['category'] as $catt) {
					$curcat = $catt->cat_ID;
					$fullpath = '/'.$this->cache['category'][$catt->cat_ID]->category_nicename;
					while ($this->cache['category'][$curcat]->category_parent != 0) {
						$curcat = $this->cache['category'][$curcat]->category_parent;
						$fullpath = '/'.$this->cache['category'][$curcat]->category_nicename.$fullpath;
					}
					$this->cache['category'][$catt->cat_ID]->fullpath = $fullpath;
				}
			}
		} else if ( 'options' == $group ) {
			$wpdb->hide_errors();
			if ( !$options = $wpdb->get_results("SELECT option_name, option_value FROM $wpdb->options WHERE autoload = 'yes'") ) {
				$options = $wpdb->get_results("SELECT option_name, option_value FROM $wpdb->options");
			}
			$wpdb->show_errors();

			foreach ($options as $option) {	
				$this->cache['options'][$option->option_name] = $option->option_value;
			}
		}
	}

	function make_group_dir($group, $perms) {
		$group_dir = $this->get_group_dir($group);
		$make_dir = '';
		foreach ( split('/', $group_dir) as $subdir) {
			$make_dir .= "$subdir/";
			if (!file_exists($this->cache_dir . $make_dir)) {
					if (!mkdir($this->cache_dir . $make_dir))
						break;
					@ chmod($this->cache_dir . $make_dir, $perms);
			}

			if (!file_exists($this->cache_dir . $make_dir . "index.php")) {
				touch($this->cache_dir . $make_dir . "index.php");
			}
		}
		
		return $this->cache_dir . "$group_dir/";
	}

	function replace($id, $data, $group = 'default', $expire = '') {
		if ( empty($group) )
			$group = 'default';

		if (!isset ($this->cache[$group][$id]))
			return false;

		return $this->set($id, $data, $group, $expire);
	}

	function set($id, $data, $group = 'default', $expire = '') {
		if ( empty($group) )
			$group = 'default';

		$this->cache[$group][$id] = $data;
		$this->dirty_objects[$group][] = $id;
		return true;
	}
	
	function save() {
		$this->stats();

		if (!$this->cache_enabled)
			return;

		if (empty ($this->dirty_objects))
			return;

		// Give the new dirs the same perms as wp-content.
		$stat = stat(ABSPATH.'wp-content');
		$dir_perms = $stat['mode'] & 0000777; // Get the permission bits.

		// Make the base cache dir.
		if (!file_exists($this->cache_dir)) {
			if (!mkdir($this->cache_dir))
				return;
			@ chmod($this->cache_dir, $dir_perms);
		}
		
		if (!file_exists($this->cache_dir . "index.php")) {
			touch($this->cache_dir . "index.php");
		}

		// Acquire a write lock.  Semaphore preferred.  Fallback to flock.
		if (function_exists('sem_get')) {
			$this->use_flock = false;
			$mutex = sem_get($this->sem_id, 1, 0644 | IPC_CREAT, 1);
			sem_acquire($mutex);
		} else {
			$this->use_flock = true;
			$mutex = fopen($this->cache_dir.$this->flock_filename, 'w');
			flock($mutex, LOCK_EX);
		}

		// Loop over dirty objects and save them.
		foreach ($this->dirty_objects as $group => $ids) {
			$group_dir = $this->make_group_dir($group, $dir_perms);

			$ids = array_unique($ids);
			foreach ($ids as $id) {
				// TODO:  If the id is no longer in the cache, it was deleted and
				// the file should be removed.
				$cache_file = $group_dir . md5($id . DB_PASSWORD);
				$temp_file = tempnam($group_dir, 'tmp');
				$serial = serialize($this->cache[$group][$id]);
				$fd = fopen($temp_file, 'w');
				fputs($fd, $serial);
				fclose($fd);
				rename($temp_file, $cache_file);
			}
		}

		// Release write lock.
		if ($this->use_flock)
			flock($mutex, LOCK_UN);
		else
			sem_release($mutex);
	}

	function stats() {
		echo "<p>";
		echo "<strong>Cold Cache Hits:</strong> {$this->cold_cache_hits}<br/>";
		echo "<strong>Warm Cache Hits:</strong> {$this->warm_cache_hits}<br/>";
		echo "<strong>Cache Misses:</strong> {$this->cache_misses}<br/>";
		echo "</p>";

		foreach ($this->cache as $group => $cache) {
			echo "<p>";
			echo "<strong>Group:</strong> $group<br/>";
			echo "<strong>Cache:</strong>";
			echo "<pre>";
			print_r($cache);
			echo "</pre>";
			if ( isset($this->dirty_objects[$group]) ) {
				echo "<strong>Dirty Objects:</strong>";
				echo "<pre>";
				print_r(array_unique($this->dirty_objects[$group]));
				echo "</pre>";
				echo "</p>";
			}				
		}
	}

	function WP_Object_Cache() {
		global $blog_id;

		if ( defined('DISABLE_CACHE') )
			return;

		if ( defined('CACHE_PATH') )
			$this->cache_dir = CACHE_PATH;
		else
			$this->cache_dir = ABSPATH.'wp-content/cache/';

		if ( is_dir($this->cache_dir) ) {
			if ( is_writable($this->cache_dir) )
				$this->cache_enabled = true;
		} else if (is_writable(ABSPATH.'wp-content')) {
			$this->cache_enabled = true;
		}
		
		$this->blog_id = md5($blog_id);
	}
}
?>
