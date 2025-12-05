<?php

namespace Yoast\WP\SEO\AI_Generator\Domain;

/**
 * Helper class to get the URLs needed for the AI Generator API.
 */
interface URLs_Interface {

	/**
	 * Gets the licence URL.
	 *
	 * @return string The license URL.
	 */
	public function get_license_url(): string;

	/**
	 * Gets the callback URL to be used by the API to send back the access token, refresh token and code challenge.
	 *
	 * @return string The callback URL.
	 */
	public function get_callback_url(): string;

	/**
	 * Gets the callback URL to be used by the API to send back the refreshed JWTs once they expire.
	 *
	 * @return string The refresh callback URL.
	 */
	public function get_refresh_callback_url(): string;
}
