<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong
// phpcs:disable Yoast.NamingConventions.NamespaceName.MaxExceeded
namespace Yoast\WP\SEO\Dashboard\Infrastructure\Analytics_4;

use Google\Site_Kit\Core\REST_API\REST_Routes;
use WP_REST_Request;
use WP_REST_Response;

/**
 * Class that hold the code to do the REST call to the Site Kit api.
 *
 * @phpcs:disable Yoast.NamingConventions.ObjectNameDepth.MaxExceeded
 */
class Site_Kit_Analytics_4_Api_Call {

	/**
	 * The Analytics 4 API route path.
	 */
	private const ANALYTICS_DATA_REPORT_ROUTE = '/modules/analytics-4/data/report';

	/**
	 * Runs the internal REST api call.
	 *
	 * @param array<string, array<string, string>> $api_parameters The api parameters.
	 *
	 * @return WP_REST_Response
	 */
	public function do_request( array $api_parameters ): WP_REST_Response {
		$request = new WP_REST_Request( 'GET', '/' . REST_Routes::REST_ROOT . self::ANALYTICS_DATA_REPORT_ROUTE );
		$request->set_query_params( $api_parameters );
		return \rest_do_request( $request );
	}
}
