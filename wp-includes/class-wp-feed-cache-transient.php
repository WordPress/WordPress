<?php
/**
 * Feed API: WP_Feed_Cache_Transient class
 *
 * @package WordPress
 * @subpackage Feed
 * @since 4.7.0
 */

/**
 * Core class used to implement feed cache transients.
 *
 * @since 2.8.0
 */
class WP_Feed_Cache_Transient {

	/**
	 * Holds the transient name.
	 *
	 * @since 2.8.0
	 * @var string
	 */
	public $name;

	/**
	 * Holds the transient mod name.
	 *
	 * @since 2.8.0
	 * @var string
	 */
	public $mod_name;

	/**
	 * Holds the cache duration in seconds.
	 *
	 * Defaults to 43200 seconds (12 hours).
	 *
	 * @since 2.8.0
	 * @var int
	 */
	public $lifetime = 43200;

	/**
	 * Constructor.
	 *
	 * @since 2.8.0
	 * @since 3.2.0 Updated to use a PHP5 constructor.
	 *
	 * @param string $location  URL location (scheme is used to determine handler).
	 * @param string $filename  Unique identifier for cache object.
	 * @param string $extension 'spi' or 'spc'.
	 */
	public function __construct($location, $filename, $extension) {
		$this->name = 'feed_' . $filename;
		$this->mod_name = 'feed_mod_' . $filename;

		$lifetime = $this->lifetime;
		/**
		 * Filters the transient lifetime of the feed cache.
		 *
		 * @since 2.8.0
		 *
		 * @param int    $lifetime Cache duration in seconds. Default is 43200 seconds (12 hours).
		 * @param string $filename Unique identifier for the cache object.
		 */
		$this->lifetime = apply_filters( 'wp_feed_cache_transient_lifetime', $lifetime, $filename);
	}

	/**
	 * Sets the transient.
	 *
	 * @since 2.8.0
	 *
	 * @param SimplePie $data Data to save.
	 * @return true Always true.
	 */
	public function save($data) {
		if ( $data instanceof SimplePie ) {
			$data = $data->data;
		}

		set_transient($this->name, $data, $this->lifetime);
		set_transient($this->mod_name, time(), $this->lifetime);
		return true;
	}

	/**
	 * Gets the transient.
	 *
	 * @since 2.8.0
	 *
	 * @return mixed Transient value.
	 */
	public function load() {
		return get_transient($this->name);
	}

	/**
	 * Gets mod transient.
	 *
	 * @since 2.8.0
	 *
	 * @return mixed Transient value.
	 */
	public function mtime() {
		return get_transient($this->mod_name);
	}

	/**
	 * Sets mod transient.
	 *
	 * @since 2.8.0
	 *
	 * @return bool False if value was not set and true if value was set.
	 */
	public function touch() {
		return set_transient($this->mod_name, time(), $this->lifetime);
	}

	/**
	 * Deletes transients.
	 *
	 * @since 2.8.0
	 *
	 * @return true Always true.
	 */
	public function unlink() {
		delete_transient($this->name);
		delete_transient($this->mod_name);
		return true;
	}
}
