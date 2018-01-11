<?php

/**
 * An extremely simple non persistent cache system
 * not as fast as using directly an array but more readable
 *
 * @since 1.7
 */
class PLL_Cache {
	protected $blog_id, $cache;

	/**
	 * Constructor
	 *
	 * @since 1.7
	 */
	public function __construct() {
		$this->blog_id = get_current_blog_id();
		add_action( 'switch_blog', array( $this, 'switch_blog' ) );
	}

	/**
	 * Called when switching blog
	 *
	 * @since 1.7
	 *
	 * @param int $new_blog
	 */
	public function switch_blog( $new_blog ) {
		$this->blog_id = $new_blog;
	}

	/**
	 * Add a value in cache
	 *
	 * @since 1.7
	 *
	 * @param string $key
	 * @param mixed  $data
	 */
	public function set( $key, $data ) {
		$this->cache[ $this->blog_id ][ $key ] = $data;
	}

	/**
	 * Get value from cache
	 *
	 * @since 1.7
	 *
	 * @param string $key
	 * @return mixed $data
	 */
	public function get( $key ) {
		return isset( $this->cache[ $this->blog_id ][ $key ] ) ? $this->cache[ $this->blog_id ][ $key ] : false;
	}

	/**
	 * Clean the cache (for this blog only)
	 *
	 * @since 1.7
	 *
	 * @param string $key
	 */
	public function clean( $key = '' ) {
		if ( empty( $key ) ) {
			unset( $this->cache[ $this->blog_id ] );
		}
		else {
			unset( $this->cache[ $this->blog_id ][ $key ] );
		}
	}
}
