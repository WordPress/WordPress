<?php

namespace Yoast\WP\SEO\Helpers;

/**
 * A helper object for the request state.
 *
 * @deprecated 23.6
 * @codeCoverageIgnore Because of deprecation.
 */
class Request_Helper {

	/**
	 * Checks if the current request is a REST request.
	 *
	 * @deprecated 23.6
	 * @codeCoverageIgnore
	 *
	 * @return bool True when the current request is a REST request.
	 */
	public function is_rest_request() {
		\_deprecated_function( __METHOD__, 'Yoast SEO 23.6', 'wp_is_serving_rest_request' );

		return \defined( 'REST_REQUEST' ) && \REST_REQUEST === true;
	}
}
