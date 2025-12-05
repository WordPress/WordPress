<?php

namespace Yoast\WP\SEO\Routes;

use WP_REST_Request;
use WP_REST_Response;
use WPSEO_Utils;
use Yoast\WP\SEO\Actions\Indexables\Indexable_Head_Action;
use Yoast\WP\SEO\Conditionals\Headless_Rest_Endpoints_Enabled_Conditional;
use Yoast\WP\SEO\Main;

/**
 * Head route for indexables.
 */
class Indexables_Head_Route implements Route_Interface {

	/**
	 * The posts route constant.
	 *
	 * @var string
	 */
	public const HEAD_FOR_URL_ROUTE = 'get_head';

	/**
	 * The full posts route constant.
	 *
	 * @var string
	 */
	public const FULL_HEAD_FOR_URL_ROUTE = Main::API_V1_NAMESPACE . '/' . self::HEAD_FOR_URL_ROUTE;

	/**
	 * The head action.
	 *
	 * @var Indexable_Head_Action
	 */
	private $head_action;

	/**
	 * Indexable_Indexation_Route constructor.
	 *
	 * @param Indexable_Head_Action $head_action The head action.
	 */
	public function __construct( Indexable_Head_Action $head_action ) {
		$this->head_action = $head_action;
	}

	/**
	 * Returns the conditionals based in which this loadable should be active.
	 *
	 * @return array<string>
	 */
	public static function get_conditionals() {
		return [ Headless_Rest_Endpoints_Enabled_Conditional::class ];
	}

	/**
	 * Registers routes with WordPress.
	 *
	 * @return void
	 */
	public function register_routes() {
		$route_args = [
			'methods'             => 'GET',
			'callback'            => [ $this, 'get_head' ],
			'permission_callback' => '__return_true',
			'args'                => [
				'url' => [
					'validate_callback' => [ $this, 'is_valid_url' ],
					'required'          => true,
				],
			],
		];
		\register_rest_route( Main::API_V1_NAMESPACE, self::HEAD_FOR_URL_ROUTE, $route_args );
	}

	/**
	 * Gets the head of a page for a given URL.
	 *
	 * @param WP_REST_Request $request The request. This request should have a url param set.
	 *
	 * @return WP_REST_Response The response.
	 */
	public function get_head( WP_REST_Request $request ) {
		$url  = \esc_url_raw( \utf8_uri_encode( $request['url'] ) );
		$data = $this->head_action->for_url( $url );

		return new WP_REST_Response( $data, $data->status );
	}

	/**
	 * Checks if a url is a valid url.
	 *
	 * @param string $url The url to check.
	 *
	 * @return bool Whether or not the url is valid.
	 */
	public function is_valid_url( $url ) {
		$url = WPSEO_Utils::sanitize_url( \utf8_uri_encode( $url ) );
		if ( \filter_var( $url, \FILTER_VALIDATE_URL ) === false ) {
			return false;
		}
		return true;
	}
}
