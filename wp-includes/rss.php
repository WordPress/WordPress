<?php

require_once('simplepie.php');

class WordPress_SimplePie_Cache extends SimplePie_Cache {
	var $name;
	
	function WordPress_SimplePie_Cache($location, $filename, $extension) {
		$this->name = rawurlencode($filename);
	}
	
	function save($data) {
		$cache_option = "rss_$this->name";
		$cache_timestamp = $cache_option . '_ts';
		if ( false !== get_option($cache_option) ) {
			update_option($cache_option, $data);
		} else {
			add_option($cache_option, $data, '', 'no');
		}
		if ( false !== get_option($cache_timestamp) ) {
			update_option($cache_timestamp, time());
		} else {
			add_option($cache_timestamp, time(), '', 'no');
		}
		return true;
	}
	
	function load() {
		return get_option("rss_$this->name");
	}
	
	function mtime() {
		return get_option('rss_' . $this->name . '_ts');
	}
	
	function touch() {
		$cache_timestamp = 'rss_' . $this->name . '_ts';
		if ( false !== get_option($cache_timestamp) ) {
			update_option($cache_timestamp, time());
		} else {
			add_option($cache_timestamp, time(), '', 'no');
		}
	}
	
	function unlink() {
		delete_option("rss_$this->name");
		delete_option('rss_' . $this->name . '_ts');
	}
}

function fetch_simplepie ($url) {
	$feed = new SimplePie;
	$feed->feed_url($url);
	$feed->set_cache_class('WordPress_SimplePie_Cache');
	$feed->strip_htmltags(false);
	$feed->strip_attributes(false);
	$feed->output_encoding(get_option('blog_charset'));
	$feed->cache_max_minutes(720);
	$feed->set_useragent('WordPress/' . $wp_version);
	if ($feed->init()) {
		return $feed;
	} else {
		return false;
	}
}

?>