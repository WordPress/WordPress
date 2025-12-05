<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO
 */

/**
 * Helps with creating shortlinks in the plugin.
 */
class WPSEO_Shortlinker {

	/**
	 * Builds a URL to use in the plugin as shortlink.
	 *
	 * @param string $url The URL to build upon.
	 *
	 * @return string The final URL.
	 */
	public function build_shortlink( $url ) {
		return YoastSEO()->helpers->short_link->build( $url );
	}

	/**
	 * Returns a version of the URL with a utm_content with the current version.
	 *
	 * @param string $url The URL to build upon.
	 *
	 * @return string The final URL.
	 */
	public static function get( $url ) {
		return YoastSEO()->helpers->short_link->get( $url );
	}

	/**
	 * Echoes a version of the URL with a utm_content with the current version.
	 *
	 * @param string $url The URL to build upon.
	 *
	 * @return void
	 */
	public static function show( $url ) {
		YoastSEO()->helpers->short_link->show( $url );
	}

	/**
	 * Gets the shortlink's query params.
	 *
	 * @return array The shortlink's query params.
	 */
	public static function get_query_params() {
		return YoastSEO()->helpers->short_link->get_query_params();
	}
}
