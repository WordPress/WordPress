<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong -- Needed in the folder structure.
namespace Yoast\WP\SEO\AI_Consent\User_Interface;

use RuntimeException;
use WP_REST_Request;
use WP_REST_Response;
use Yoast\WP\SEO\AI_Authorization\Application\Token_Manager;
use Yoast\WP\SEO\AI_Consent\Application\Consent_Handler;
use Yoast\WP\SEO\AI_HTTP_Request\Domain\Exceptions\Bad_Request_Exception;
use Yoast\WP\SEO\AI_HTTP_Request\Domain\Exceptions\Forbidden_Exception;
use Yoast\WP\SEO\AI_HTTP_Request\Domain\Exceptions\Internal_Server_Error_Exception;
use Yoast\WP\SEO\AI_HTTP_Request\Domain\Exceptions\Not_Found_Exception;
use Yoast\WP\SEO\AI_HTTP_Request\Domain\Exceptions\Payment_Required_Exception;
use Yoast\WP\SEO\AI_HTTP_Request\Domain\Exceptions\Request_Timeout_Exception;
use Yoast\WP\SEO\AI_HTTP_Request\Domain\Exceptions\Service_Unavailable_Exception;
use Yoast\WP\SEO\AI_HTTP_Request\Domain\Exceptions\Too_Many_Requests_Exception;
use Yoast\WP\SEO\Conditionals\AI_Conditional;
use Yoast\WP\SEO\Main;
use Yoast\WP\SEO\Routes\Route_Interface;

/**
 * Registers a route toget suggestions from the AI API
 *
 * @makePublic
 *
 * @phpcs:disable Yoast.NamingConventions.ObjectNameDepth.MaxExceeded
 */
class Consent_Route implements Route_Interface {
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
	public const ROUTE_PREFIX = '/ai_generator/consent';

	/**
	 * The consent handler instance.
	 *
	 * @var Consent_Handler
	 */
	private $consent_handler;

	/**
	 * The token manager instance.
	 *
	 * @var Token_Manager
	 */
	private $token_manager;

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
	 * @param Consent_Handler $consent_handler The consent handler.
	 * @param Token_Manager   $token_manager   The token manager.
	 */
	public function __construct( Consent_Handler $consent_handler, Token_Manager $token_manager ) {
		$this->consent_handler = $consent_handler;
		$this->token_manager   = $token_manager;
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
				'args'                => [
					'consent' => [
						'required'    => true,
						'type'        => 'boolean',
						'description' => 'Whether the consent to use AI-based services has been given by the user.',
					],
				],
				'callback'            => [ $this, 'consent' ],
				'permission_callback' => [ $this, 'check_permissions' ],
			]
		);
	}

	/**
	 * Runs the callback to store the consent given by the user to use AI-based services.
	 *
	 * @param WP_REST_Request $request The request object.
	 *
	 * @return WP_REST_Response The response of the callback action.
	 */
	public function consent( WP_REST_Request $request ): WP_REST_Response {
		$user_id = \get_current_user_id();
		$consent = \boolval( $request->get_param( 'consent' ) );

		try {
			if ( $consent ) {
				// Store the consent at user level.
				$this->consent_handler->grant_consent( $user_id );
			}
			else {
				// Delete the consent at user level.
				$this->consent_handler->revoke_consent( $user_id );
				// Invalidate the token if the user revoked the consent.
				$this->token_manager->token_invalidate( $user_id );
			}
		} catch ( Bad_Request_Exception | Forbidden_Exception | Internal_Server_Error_Exception | Not_Found_Exception | Payment_Required_Exception | Request_Timeout_Exception | Service_Unavailable_Exception | Too_Many_Requests_Exception | RuntimeException $e ) {
			return new WP_REST_Response( ( $consent ) ? 'Failed to store consent.' : 'Failed to revoke consent.', 500 );
		}

			return new WP_REST_Response( ( $consent ) ? 'Consent successfully stored.' : 'Consent successfully revoked.' );
	}

	/**
	 * Checks:
	 * - if the user is logged
	 * - if the user can edit posts
	 *
	 * @return bool Whether the user is logged in, can edit posts and the feature is active.
	 */
	public function check_permissions(): bool {
		$user = \wp_get_current_user();
		if ( $user === null || $user->ID < 1 ) {
			return false;
		}

		return \user_can( $user, 'edit_posts' );
	}
}
