<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong -- Needed in the folder structure.
namespace Yoast\WP\SEO\AI_Generator\User_Interface;

use WP_REST_Response;
use WPSEO_Addon_Manager;
use Yoast\WP\SEO\Conditionals\AI_Conditional;
use Yoast\WP\SEO\Main;
use Yoast\WP\SEO\Routes\Route_Interface;

/**
 * Registers a route to bust the subscription cache.
 *
 * @makePublic
 *
 * @phpcs:disable Yoast.NamingConventions.ObjectNameDepth.MaxExceeded
 */
class Bust_Subscription_Cache_Route implements Route_Interface {

	use Route_Permission_Trait;

	/**
	 *  The namespace for this route.
	 *
	 * @var string
	 */
	public const ROUTE_NAMESPACE = Main::API_V1_NAMESPACE;

	/**
	 *  The prefix for this route.
	 *
	 * @var string
	 */
	public const ROUTE_PREFIX = '/ai_generator/bust_subscription_cache';

	/**
	 * The addon manager instance.
	 *
	 * @var WPSEO_Addon_Manager
	 */
	private $addon_manager;

	/**
	 * Returns the conditionals based in which this loadable should be active.
	 *
	 * @return array<string> The conditionals.
	 */
	public static function get_conditionals() {
		return [ AI_Conditional::class ];
	}

	/**
	 * Class constructor.
	 *
	 * @param WPSEO_Addon_Manager $addon_manager The addon manager instance.
	 */
	public function __construct( WPSEO_Addon_Manager $addon_manager ) {
		$this->addon_manager = $addon_manager;
	}

	/**
	 * Registers routes with WordPress.
	 *
	 * @return void
	 */
	public function register_routes() {
		\register_rest_route(
			self::ROUTE_NAMESPACE,
			self::ROUTE_PREFIX,
			[
				'methods'             => 'POST',
				'args'                => [],
				'callback'            => [ $this, 'bust_subscription_cache' ],
				'permission_callback' => [ $this, 'check_permissions' ],
			]
		);
	}

	/**
	 * Runs the callback that busts the subscription cache.
	 *
	 * @return WP_REST_Response The response of the callback action.
	 */
	public function bust_subscription_cache(): WP_REST_Response {
		$this->addon_manager->remove_site_information_transients();

		return new WP_REST_Response( 'Subscription cache successfully busted.' );
	}
}
