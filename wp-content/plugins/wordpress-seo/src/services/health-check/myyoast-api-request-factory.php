<?php

namespace Yoast\WP\SEO\Services\Health_Check;

use WPSEO_MyYoast_Api_Request;

/**
 * Creates WPSEO_MyYoast_Api_Request objects.
 */
class MyYoast_Api_Request_Factory {

	/**
	 * Creates a new WPSEO_MyYoast_API_Request.
	 *
	 * @param string $url  The URL for the request.
	 * @param array  $args Optional arguments for the request.
	 * @return WPSEO_MyYoast_Api_Request
	 */
	public function create( $url, $args = [] ) {
		return new WPSEO_MyYoast_Api_Request( $url, $args );
	}
}
