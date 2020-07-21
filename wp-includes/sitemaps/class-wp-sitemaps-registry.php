<?php
/**
 * Sitemaps: WP_Sitemaps_Registry class
 *
 * Handles registering sitemaps.
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
	 * Registered sitemaps.
	 *
	 * @since 5.5.0
	 *
	 * @var WP_Sitemaps_Provider[] Array of registered sitemaps.
	 */
	private $sitemaps = array();

	/**
	 * Adds a sitemap with route to the registry.
	 *
	 * @since 5.5.0
	 *
	 * @param string               $name     Name of the sitemap.
	 * @param WP_Sitemaps_Provider $provider Instance of a WP_Sitemaps_Provider.
	 * @return bool True if the sitemap was added, false if it is already registered.
	 */
	public function add_sitemap( $name, WP_Sitemaps_Provider $provider ) {
		if ( isset( $this->sitemaps[ $name ] ) ) {
			return false;
		}

		$this->sitemaps[ $name ] = $provider;

		return true;
	}

	/**
	 * Returns a single registered sitemaps provider.
	 *
	 * @since 5.5.0
	 *
	 * @param string $name Sitemap provider name.
	 * @return WP_Sitemaps_Provider|null Sitemaps provider if it exists, null otherwise.
	 */
	public function get_sitemap( $name ) {
		if ( ! isset( $this->sitemaps[ $name ] ) ) {
			return null;
		}

		return $this->sitemaps[ $name ];
	}

	/**
	 * Returns all registered sitemap providers.
	 *
	 * @since 5.5.0
	 *
	 * @return WP_Sitemaps_Provider[] Array of sitemap providers.
	 */
	public function get_sitemaps() {
		return $this->sitemaps;
	}
}
