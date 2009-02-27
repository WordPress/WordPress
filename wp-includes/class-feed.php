<?php

require_once (ABSPATH . WPINC . '/simplepie.inc');

class WP_Feed_Cache extends SimplePie_Cache
{
	/**
	 * Don't call the constructor. Please.
	 *
	 * @access private
	 */
	function WP_Feed_Cache()
	{
		trigger_error('Please call SimplePie_Cache::create() instead of the constructor', E_USER_ERROR);
	}

	/**
	 * Create a new SimplePie_Cache object
	 *
	 * @static
	 * @access public
	 */
	function create($location, $filename, $extension)
	{
		return new WP_Feed_Cache_Transient($location, $filename, $extension);
	}
}

class WP_Feed_Cache_Transient
{
	var $location;
	var $filename;
	var $extension;
	var $name;

	function WP_Feed_Cache_Transient($location, $filename, $extension)
	{
		//$this->location = $location;
		//$this->filename = rawurlencode($filename);
		//$this->extension = rawurlencode($extension);
		//$this->name = "$location/$this->filename.$this->extension";
		$this->name = 'feed_' . $filename;
		$this->mod_name = 'feed_mod_' . $filename;
	}

	function save($data)
	{
		if (is_a($data, 'SimplePie'))
		{
			$data = $data->data;
		}

		set_transient($this->name, $data, 43200);
		set_transient($this->mod_name, time(), 43200);
		return true;
	}

	function load()
	{
		return get_transient($this->name);
	}

	function mtime()
	{
		return get_transient($this->mod_name);
	}

	function touch()
	{
		return set_transient($this->mod_name, time(), 43200);
	}

	function unlink()
	{
		delete_transient($this->name);
		delete_transient($this->mod_name);
		return true;
	}
}
