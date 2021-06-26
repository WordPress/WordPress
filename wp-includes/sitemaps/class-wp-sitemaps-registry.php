<?php
/**
 * Sitemaps: WP_Sitemaps_Registry class
 *
 * Handles registering sitemap providers.
 *
 * @package WordPress
 * @subpackage Sitemaps
 * @since 5.5.0
 */

/**
 * Class WP_Sitemaps_Registry.
 *
 * @since 5.5.0
 */
class WP_Sitemaps_Registry {
	/**
	 * Registered sitemap providers.
	 *
	 * @since 5.5.0
	 *
	 * @var WP_Sitemaps_Provider[] Array of registered sitemap providers.
	 */
	private $providers = array();

	/**
	 * Adds a new sitemap provider.
	 *
	 * @since 5.5.0
	 *
	 * @param string               $name     Name of the sitemap provider.
	 * @param WP_Sitemaps_Provider $provider Instance of a WP_Sitemaps_Provider.
	 * @return bool Whether the provider was added successfully.
	 */
	public function add_provider( $name, WP_Sitemaps_Provider $provider ) {
		if ( isset( $this->providers[ $name ] ) ) {
			return false;
		}

		/**
		 * Filters the sitemap provider before it is added.
		 *
		 * @since 5.5.0
		 *
		 * @param WP_Sitemaps_Provider $provider Instance of a WP_Sitemaps_Provider.
		 * @param string               $name     Name of the sitemap provider.
		 */
		$provider = apply_filters( 'wp_sitemaps_add_provider', $provider, $name );
		if ( ! $provider instanceof WP_Sitemaps_Provider ) {
			return false;
		}

		$this->providers[ $name ] = $provider;

		return true;
	}

	/**
	 * Returns a single registered sitemap provider.
	 *
	 * @since 5.5.0
	 *
	 * @param string $name Sitemap provider name.
	 * @return WP_Sitemaps_Provider|null Sitemap provider if it exists, null otherwise.
	 */
	public function get_provider( $name ) {
		if ( ! isset( $this->providers[ $name ] ) ) {
			return null;
		}

		return $this->providers[ $name ];
	}

	/**
	 * Returns all registered sitemap providers.
	 *
	 * @since 5.5.0
	 *
	 * @return WP_Sitemaps_Provider[] Array of sitemap providers.
	 */
	public function get_providers() {
		return $this->providers;
	}
}
