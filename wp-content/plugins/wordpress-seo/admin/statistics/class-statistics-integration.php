<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Admin\Statistics
 */

/**
 * Class WPSEO_Statistic_Integration.
 */
class WPSEO_Statistic_Integration implements WPSEO_WordPress_Integration {

	/**
	 * Adds hooks to clear the cache.
	 *
	 * @return void
	 */
	public function register_hooks() {
		add_action( 'wp_insert_post', [ $this, 'clear_cache' ] );
		add_action( 'delete_post', [ $this, 'clear_cache' ] );
	}

	/**
	 * Clears the dashboard widget items cache.
	 *
	 * @return void
	 */
	public function clear_cache() {
		// Bail if this is a multisite installation and the site has been switched.
		if ( is_multisite() && ms_is_switched() ) {
			return;
		}

		delete_transient( WPSEO_Statistics_Service::CACHE_TRANSIENT_KEY );
	}
}
