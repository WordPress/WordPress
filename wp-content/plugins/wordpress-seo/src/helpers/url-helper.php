<?php

namespace Yoast\WP\SEO\Helpers;

use Yoast\WP\SEO\Models\SEO_Links;

/**
 * A helper object for URLs.
 */
class Url_Helper {

	/**
	 * Retrieve home URL with proper trailing slash.
	 *
	 * @param string      $path   Path relative to home URL.
	 * @param string|null $scheme Scheme to apply.
	 *
	 * @return string Home URL with optional path, appropriately slashed if not.
	 */
	public function home( $path = '', $scheme = null ) {
		$home_url = \home_url( $path, $scheme );

		if ( ! empty( $path ) ) {
			return $home_url;
		}

		$home_path = \wp_parse_url( $home_url, \PHP_URL_PATH );

		if ( $home_path === '/' ) { // Home at site root, already slashed.
			return $home_url;
		}

		if ( $home_path === null ) { // Home at site root, always slash.
			return \trailingslashit( $home_url );
		}

		if ( \is_string( $home_path ) ) { // Home in subdirectory, slash if permalink structure has slash.
			return \user_trailingslashit( $home_url );
		}

		return $home_url;
	}

	/**
	 * Determines whether the plugin is active for the entire network.
	 *
	 * @return bool Whether or not the plugin is network-active.
	 */
	public function is_plugin_network_active() {
		static $network_active = null;

		if ( ! \is_multisite() ) {
			return false;
		}

		// If a cached result is available, bail early.
		if ( $network_active !== null ) {
			return $network_active;
		}

		$network_active_plugins = \wp_get_active_network_plugins();

		// Consider MU plugins and network-activated plugins as network-active.
		$network_active = \strpos( \wp_normalize_path( \WPSEO_FILE ), \wp_normalize_path( \WPMU_PLUGIN_DIR ) ) === 0
			|| \in_array( \WP_PLUGIN_DIR . '/' . \WPSEO_BASENAME, $network_active_plugins, true );

		return $network_active;
	}

	/**
	 * Retrieve network home URL if plugin is network-activated, or home url otherwise.
	 *
	 * @return string Home URL with optional path, appropriately slashed if not.
	 */
	public function network_safe_home_url() {
		/**
		 * Action: 'wpseo_home_url' - Allows overriding of the home URL.
		 */
		\do_action( 'wpseo_home_url' );

		// If the plugin is network-activated, use the network home URL.
		if ( self::is_plugin_network_active() ) {
			return \network_home_url();
		}

		return \home_url();
	}

	/**
	 * Check whether a url is relative.
	 *
	 * @param string $url URL string to check.
	 *
	 * @return bool True when url is relative.
	 */
	public function is_relative( $url ) {
		return ( \strpos( $url, 'http' ) !== 0 && \strpos( $url, '//' ) !== 0 );
	}

	/**
	 * Gets the path from the passed URL.
	 *
	 * @param string $url The URL to get the path from.
	 *
	 * @return string The path of the URL. Returns an empty string if URL parsing fails.
	 */
	public function get_url_path( $url ) {
		if ( \is_string( $url ) === false
			&& ( \is_object( $url ) === false || \method_exists( $url, '__toString' ) === false )
		) {
			return '';
		}

		return (string) \wp_parse_url( $url, \PHP_URL_PATH );
	}

	/**
	 * Gets the host from the passed URL.
	 *
	 * @param string $url The URL to get the host from.
	 *
	 * @return string The host of the URL. Returns an empty string if URL parsing fails.
	 */
	public function get_url_host( $url ) {
		if ( \is_string( $url ) === false
			&& ( \is_object( $url ) === false || \method_exists( $url, '__toString' ) === false )
		) {
			return '';
		}

		return (string) \wp_parse_url( $url, \PHP_URL_HOST );
	}

	/**
	 * Determines the file extension of the given url.
	 *
	 * @param string $url The URL.
	 *
	 * @return string The extension.
	 */
	public function get_extension_from_url( $url ) {
		$path = $this->get_url_path( $url );

		if ( $path === '' ) {
			return '';
		}

		$parts = \explode( '.', $path );
		if ( empty( $parts ) || \count( $parts ) === 1 ) {
			return '';
		}

		return \end( $parts );
	}

	/**
	 * Ensures that the given url is an absolute url.
	 *
	 * @param string $url The url that needs to be absolute.
	 *
	 * @return string The absolute url.
	 */
	public function ensure_absolute_url( $url ) {
		if ( ! \is_string( $url ) || $url === '' ) {
			return $url;
		}

		if ( $this->is_relative( $url ) === true ) {
			return $this->build_absolute_url( $url );
		}

		return $url;
	}

	/**
	 * Parse the home URL setting to find the base URL for relative URLs.
	 *
	 * @param string|null $path Optional path string.
	 *
	 * @return string
	 */
	public function build_absolute_url( $path = null ) {
		$path      = \wp_parse_url( $path, \PHP_URL_PATH );
		$url_parts = \wp_parse_url( \home_url() );

		$base_url = \trailingslashit( $url_parts['scheme'] . '://' . $url_parts['host'] );

		if ( \is_string( $path ) ) {
			$base_url .= \ltrim( $path, '/' );
		}

		return $base_url;
	}

	/**
	 * Returns the link type.
	 *
	 * @param array      $url      The URL, as parsed by wp_parse_url.
	 * @param array|null $home_url Optional. The home URL, as parsed by wp_parse_url. Used to avoid reparsing the home_url.
	 * @param bool       $is_image Whether or not the link is an image.
	 *
	 * @return string The link type.
	 */
	public function get_link_type( $url, $home_url = null, $is_image = false ) {
		// If there is no scheme and no host the link is always internal.
		// Beware, checking just the scheme isn't enough as a link can be //yoast.com for instance.
		if ( empty( $url['scheme'] ) && empty( $url['host'] ) ) {
			return ( $is_image ) ? SEO_Links::TYPE_INTERNAL_IMAGE : SEO_Links::TYPE_INTERNAL;
		}

		// If there is a scheme but it's not http(s) then the link is always external.
		if ( \array_key_exists( 'scheme', $url ) && ! \in_array( $url['scheme'], [ 'http', 'https' ], true ) ) {
			return ( $is_image ) ? SEO_Links::TYPE_EXTERNAL_IMAGE : SEO_Links::TYPE_EXTERNAL;
		}

		if ( $home_url === null ) {
			$home_url = \wp_parse_url( \home_url() );
		}

		// When the base host is equal to the host.
		if ( isset( $url['host'] ) && $url['host'] !== $home_url['host'] ) {
			return ( $is_image ) ? SEO_Links::TYPE_EXTERNAL_IMAGE : SEO_Links::TYPE_EXTERNAL;
		}

		// There is no base path and thus all URLs of the same domain are internal.
		if ( empty( $home_url['path'] ) ) {
			return ( $is_image ) ? SEO_Links::TYPE_INTERNAL_IMAGE : SEO_Links::TYPE_INTERNAL;
		}

		// When there is a path and it matches the start of the url.
		if ( isset( $url['path'] ) && \strpos( $url['path'], $home_url['path'] ) === 0 ) {
			return ( $is_image ) ? SEO_Links::TYPE_INTERNAL_IMAGE : SEO_Links::TYPE_INTERNAL;
		}

		return ( $is_image ) ? SEO_Links::TYPE_EXTERNAL_IMAGE : SEO_Links::TYPE_EXTERNAL;
	}

	/**
	 * Recreate current URL.
	 *
	 * @param bool $with_request_uri Whether we want the REQUEST_URI appended.
	 *
	 * @return string
	 */
	public function recreate_current_url( $with_request_uri = true ) {
		$current_url = 'http';
		if ( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] === 'on' ) {
			$current_url .= 's';
		}
		$current_url .= '://';

		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput -- We know this is scary.
		$suffix = ( $with_request_uri && isset( $_SERVER['REQUEST_URI'] ) ) ? $_SERVER['REQUEST_URI'] : '';

		if ( isset( $_SERVER['SERVER_NAME'] ) && ! empty( $_SERVER['SERVER_NAME'] ) ) {
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput -- We know this is scary.
			$server_name = $_SERVER['SERVER_NAME'];
		}
		else {
			// Early return with just the path.
			return $suffix;
		}

		$server_port = '';
		if ( isset( $_SERVER['SERVER_PORT'] ) && $_SERVER['SERVER_PORT'] !== '80' && $_SERVER['SERVER_PORT'] !== '443' ) {
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput -- We know this is scary.
			$server_port = $_SERVER['SERVER_PORT'];
		}

		if ( ! empty( $server_port ) ) {
			$current_url .= $server_name . ':' . $server_port . $suffix;
		}
		else {
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput -- We know this is scary.
			$current_url .= $server_name . $suffix;
		}

		return $current_url;
	}

	/**
	 * Parses a URL and returns its components, this wrapper function was created to support unit tests.
	 *
	 * @param string $parsed_url The URL to parse.
	 * @return array The parsed components of the URL.
	 */
	public function parse_str_params( $parsed_url ) {
		$array = [];

		// @todo parse_str changes spaces in param names into `_`, we should find a better way to support them.
		\wp_parse_str( $parsed_url, $array );

		return $array;
	}
}
