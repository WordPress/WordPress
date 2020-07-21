<?php
/**
 * Sitemaps: Public functions
 *
 * This file contains a variety of public functions developers can use to interact with
 * the XML Sitemaps API.
 *
 * @package WordPress
 * @subpackage Sitemaps
 * @since 5.5.0
 */

/**
 * Retrieves the current Sitemaps server instance.
 *
 * @since 5.5.0
 *
 * @global WP_Sitemaps $wp_sitemaps Global Core Sitemaps instance.
 *
 * @return WP_Sitemaps Sitemaps instance.
 */
function wp_sitemaps_get_server() {
	global $wp_sitemaps;

	$is_enabled = (bool) get_option( 'blog_public' );

	// If there isn't a global instance, set and bootstrap the sitemaps system.
	if ( empty( $wp_sitemaps ) ) {
		$wp_sitemaps = new WP_Sitemaps();
		$wp_sitemaps->init();

		/**
		 * Fires when initializing the Sitemaps object.
		 *
		 * Additional sitemaps should be registered on this hook.
		 *
		 * @since 5.5.0
		 *
		 * @param WP_Sitemaps $wp_sitemaps Sitemaps object.
		 */
		do_action( 'wp_sitemaps_init', $wp_sitemaps );
	}

	return $wp_sitemaps;
}

/**
 * Gets an array of sitemap providers.
 *
 * @since 5.5.0
 *
 * @return WP_Sitemaps_Provider[] Array of sitemap providers.
 */
function wp_get_sitemaps() {
	$sitemaps = wp_sitemaps_get_server();

	if ( ! $sitemaps ) {
		return array();
	}

	return $sitemaps->registry->get_providers();
}

/**
 * Registers a new sitemap provider.
 *
 * @since 5.5.0
 *
 * @param string               $name     Unique name for the sitemap provider.
 * @param WP_Sitemaps_Provider $provider The `Sitemaps_Provider` instance implementing the sitemap.
 * @return bool Returns true if the sitemap was added. False on failure.
 */
function wp_register_sitemap( $name, WP_Sitemaps_Provider $provider ) {
	$sitemaps = wp_sitemaps_get_server();

	if ( ! $sitemaps ) {
		return false;
	}

	return $sitemaps->registry->add_provider( $name, $provider );
}

/**
 * Gets the maximum number of URLs for a sitemap.
 *
 * @since 5.5.0
 *
 * @param string $object_type Object type for sitemap to be filtered (e.g. 'post', 'term', 'user').
 * @return int The maximum number of URLs.
 */
function wp_sitemaps_get_max_urls( $object_type ) {
	/**
	 * Filters the maximum number of URLs displayed on a sitemap.
	 *
	 * @since 5.5.0
	 *
	 * @param int    $max_urls    The maximum number of URLs included in a sitemap. Default 2000.
	 * @param string $object_type Object type for sitemap to be filtered (e.g. 'post', 'term', 'user').
	 */
	return apply_filters( 'wp_sitemaps_max_urls', 2000, $object_type );
}
