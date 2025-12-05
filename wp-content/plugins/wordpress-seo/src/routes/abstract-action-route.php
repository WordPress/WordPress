<?php

namespace Yoast\WP\SEO\Routes;

use WP_REST_Response;

/**
 * Abstract_Action_Route class.
 *
 * Abstract class for action routes.
 */
abstract class Abstract_Action_Route implements Route_Interface {

	/**
	 * Responds to an indexing request.
	 *
	 * @param array  $objects  The objects that have been indexed.
	 * @param string $next_url The url that should be called to continue reindexing. False if done.
	 *
	 * @return WP_REST_Response The response.
	 */
	protected function respond_with( $objects, $next_url ) {
		return new WP_REST_Response(
			[
				'objects'  => $objects,
				'next_url' => $next_url,
			]
		);
	}
}
