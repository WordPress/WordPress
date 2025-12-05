<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong -- Needed in the folder structure.
namespace Yoast\WP\SEO\AI_Free_Sparks\User_Interface;

use WP_REST_Response;
use Yoast\WP\SEO\AI_Free_Sparks\Application\Free_Sparks_Handler_Interface;
use Yoast\WP\SEO\Conditionals\AI_Conditional;
use Yoast\WP\SEO\Main;
use Yoast\WP\SEO\Routes\Route_Interface;

/**
 * Registers a route to start free sparks.
 */
class Free_Sparks_Route implements Route_Interface {

	/**
	 * The namespace for this route.
	 *
	 * @var string
	 */
	public const ROUTE_NAMESPACE = Main::API_V1_NAMESPACE;

	/**
	 * The prefix for this route.
	 *
	 * @var string
	 */
	public const ROUTE_PREFIX = '/ai/free_sparks';

	/**
	 * The free sparks handler instance.
	 *
	 * @var Free_Sparks_Handler_Interface
	 */
	private $free_sparks_handler;

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
	 * @param Free_Sparks_Handler_Interface $free_sparks_handler The free sparks handler instance.
	 */
	public function __construct( Free_Sparks_Handler_Interface $free_sparks_handler ) {
		$this->free_sparks_handler = $free_sparks_handler;
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
				'callback'            => [ $this, 'start' ],
				'permission_callback' => [ $this, 'can_edit_posts' ],
			]
		);
	}

	/**
	 * Runs the callback to start the free sparks.
	 *
	 * @return WP_REST_Response The response of the callback action.
	 */
	public function start(): WP_REST_Response {
		$result = $this->free_sparks_handler->start( null );
		if ( ! $result ) {
			new WP_REST_Response( 'Failed to start free sparks.', 500 );
		}

		return new WP_REST_Response( 'Free sparks successfully started.' );
	}

	/**
	 * Checks whether the user is logged in and can edit posts.
	 *
	 * @return bool Whether the user is logged in and can edit posts.
	 */
	public function can_edit_posts(): bool {
		$user = \wp_get_current_user();
		if ( $user === null || $user->ID < 1 ) {
			return false;
		}

		return \user_can( $user, 'edit_posts' );
	}
}
