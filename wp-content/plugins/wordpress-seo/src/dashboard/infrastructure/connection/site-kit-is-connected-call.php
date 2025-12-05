<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong
// phpcs:disable Yoast.NamingConventions.NamespaceName.MaxExceeded
namespace Yoast\WP\SEO\Dashboard\Infrastructure\Connection;

use Google\Site_Kit\Core\REST_API\REST_Routes;
use WP_REST_Request;

/**
 * Class that hold the code to do the REST call to the Site Kit api.
 *
 * @phpcs:disable Yoast.NamingConventions.ObjectNameDepth.MaxExceeded
 */
class Site_Kit_Is_Connected_Call {

	/**
	 * Runs the internal REST api call.
	 *
	 * @return bool
	 */
	public function is_setup_completed(): bool {
		if ( ! \class_exists( REST_Routes::class ) ) {
			return false;
		}

		$request = new WP_REST_Request( 'GET', '/' . REST_Routes::REST_ROOT . '/core/site/data/connection' );

		$response = \rest_do_request( $request );

		if ( $response->is_error() ) {
			return false;
		}
		return $response->get_data()['setupCompleted'];
	}

	/**
	 * Runs the internal REST api call.
	 *
	 * @return bool
	 */
	public function is_ga_connected(): bool {
		if ( ! \class_exists( REST_Routes::class ) ) {
			return false;
		}
		$request  = new WP_REST_Request( 'GET', '/' . REST_Routes::REST_ROOT . '/core/modules/data/list' );
		$response = \rest_do_request( $request );

		if ( $response->is_error() ) {
			return false;
		}
		$connected = false;
		foreach ( $response->get_data() as $module ) {
			if ( $module['slug'] === 'analytics-4' ) {
				$connected = $module['connected'];
			}
		}

		return $connected;
	}
}
