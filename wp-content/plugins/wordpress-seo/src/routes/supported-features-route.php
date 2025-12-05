<?php

namespace Yoast\WP\SEO\Routes;

use WP_REST_Response;
use Yoast\WP\SEO\Conditionals\Addon_Installation_Conditional;
use Yoast\WP\SEO\Main;

/**
 * Supported_Features_Route class.
 */
class Supported_Features_Route implements Route_Interface {

	/**
	 * Represents the supported features route.
	 *
	 * @var string
	 */
	public const SUPPORTED_FEATURES_ROUTE = '/supported-features';

	/**
	 * Returns the conditionals based in which this loadable should be active.
	 *
	 * @return array
	 */
	public static function get_conditionals() {
		return [
			Addon_Installation_Conditional::class,
		];
	}

	/**
	 * Registers routes with WordPress.
	 *
	 * @return void
	 */
	public function register_routes() {
		$supported_features_route = [
			'methods'             => 'GET',
			'callback'            => [ $this, 'get_supported_features' ],
			'permission_callback' => '__return_true',
		];

		\register_rest_route( Main::API_V1_NAMESPACE, self::SUPPORTED_FEATURES_ROUTE, $supported_features_route );
	}

	/**
	 * Returns a list of features supported by this yoast seo installation.
	 *
	 * @return WP_REST_Response a list of features supported by this yoast seo installation.
	 */
	public function get_supported_features() {
		return new WP_REST_Response(
			[
				'addon-installation' => 1,
			]
		);
	}
}
