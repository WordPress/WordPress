<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong
// phpcs:disable Yoast.NamingConventions.NamespaceName.MaxExceeded
namespace Yoast\WP\SEO\Dashboard\Infrastructure\Search_Console;

use WP_REST_Request;
use WP_REST_Response;

/**
 * Class that hold the code to do the REST call to the Site Kit api.
 *
 * @phpcs:disable Yoast.NamingConventions.ObjectNameDepth.MaxExceeded
 */
class Site_Kit_Search_Console_Api_Call {

	/**
	 * The search analytics API route path.
	 */
	private const SEARCH_CONSOLE_DATA_SEARCH_ANALYTICS_ROUTE = '/google-site-kit/v1/modules/search-console/data/searchanalytics';

	/**
	 * Runs the internal REST api call.
	 *
	 * @param array<string, array<string, string>> $api_parameters The api parameters.
	 *
	 * @return WP_REST_Response
	 */
	public function do_request( array $api_parameters ): WP_REST_Response {
		$request = new WP_REST_Request( 'GET', self::SEARCH_CONSOLE_DATA_SEARCH_ANALYTICS_ROUTE );
		$request->set_query_params( $api_parameters );
		return \rest_do_request( $request );
	}
}
