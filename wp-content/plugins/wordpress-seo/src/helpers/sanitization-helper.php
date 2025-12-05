<?php

namespace Yoast\WP\SEO\Helpers;

use WPSEO_Utils;

/**
 * A helper object for sanitization.
 */
class Sanitization_Helper {

	/**
	 * Emulate the WP native sanitize_text_field function in a %%variable%% safe way.
	 *
	 * @codeCoverageIgnore We have to write test when this method contains own code.
	 *
	 * @param string $value String value to sanitize.
	 *
	 * @return string The sanitized string.
	 */
	public function sanitize_text_field( $value ) {
		return WPSEO_Utils::sanitize_text_field( $value );
	}

	/**
	 * Sanitize a url for saving to the database.
	 * Not to be confused with the old native WP function.
	 *
	 * @codeCoverageIgnore We have to write test when this method contains own code.
	 *
	 * @param string $value             String URL value to sanitize.
	 * @param array  $allowed_protocols Optional set of allowed protocols.
	 *
	 * @return string The sanitized URL.
	 */
	public function sanitize_url( $value, $allowed_protocols = [ 'http', 'https' ] ) {
		return WPSEO_Utils::sanitize_url( $value, $allowed_protocols );
	}
}
