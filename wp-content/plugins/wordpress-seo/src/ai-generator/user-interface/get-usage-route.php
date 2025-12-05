<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong -- Needed in the folder structure.
namespace Yoast\WP\SEO\AI_Generator\User_Interface;

use WP_REST_Response;
use WPSEO_Addon_Manager;
use Yoast\WP\SEO\AI_Authorization\Application\Token_Manager;
use Yoast\WP\SEO\AI_HTTP_Request\Application\Request_Handler;
use Yoast\WP\SEO\AI_HTTP_Request\Domain\Exceptions\Remote_Request_Exception;
use Yoast\WP\SEO\AI_HTTP_Request\Domain\Exceptions\Too_Many_Requests_Exception;
use Yoast\WP\SEO\AI_HTTP_Request\Domain\Exceptions\WP_Request_Exception;
use Yoast\WP\SEO\AI_HTTP_Request\Domain\Request;
use Yoast\WP\SEO\Conditionals\AI_Conditional;
use Yoast\WP\SEO\Main;
use Yoast\WP\SEO\Routes\Route_Interface;

/**
 * Registers a route to get suggestions from the AI API
 *
 * @makePublic
 *
 * @phpcs:disable Yoast.NamingConventions.ObjectNameDepth.MaxExceeded
 */
class Get_Usage_Route implements Route_Interface {

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
	public const ROUTE_PREFIX = '/ai_generator/get_usage';

	/**
	 * The token manager instance.
	 *
	 * @var Token_Manager
	 */
	private $token_manager;

	/**
	 * The request handler instance.
	 *
	 * @var Request_Handler
	 */
	private $request_handler;

	/**
	 * Represents the add-on manager.
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
	 * @param Token_Manager       $token_manager   The token manager instance.
	 * @param Request_Handler     $request_handler The request handler instance.
	 * @param WPSEO_Addon_Manager $addon_manager   The add-on manager instance.
	 */
	public function __construct( Token_Manager $token_manager, Request_Handler $request_handler, WPSEO_Addon_Manager $addon_manager ) {
		$this->addon_manager   = $addon_manager;
		$this->token_manager   = $token_manager;
		$this->request_handler = $request_handler;
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
					'is_woo_product_entity' => [
						'type'        => 'boolean',
						'default'     => false,
					],
				],
				'callback'            => [ $this, 'get_usage' ],
				'permission_callback' => [ $this, 'check_permissions' ],
			]
		);
	}

	/**
	 * Runs the callback that gets the monthly usage of the user.
	 *
	 * @param WP_REST_Request $request The request object.
	 *
	 * @return WP_REST_Response The response of the callback action.
	 */
	public function get_usage( $request ): WP_REST_Response {
		$is_woo_product_entity = $request->get_param( 'is_woo_product_entity' );
		$user                  = \wp_get_current_user();
		try {
			$token           = $this->token_manager->get_or_request_access_token( $user );
			$request_headers = [
				'Authorization' => "Bearer $token",
			];
			$action_path     = $this->get_action_path( $is_woo_product_entity );
			$response        = $this->request_handler->handle( new Request( $action_path, [], $request_headers, false ) );
			$data            = \json_decode( $response->get_body() );

		}  catch ( Remote_Request_Exception | WP_Request_Exception $e ) {
			$message = [
				'errorMessage'    => $e->getMessage(),
				'errorIdentifier' => $e->get_error_identifier(),
				'errorCode'       => $e->getCode(),
			];
			if ( $e instanceof Too_Many_Requests_Exception ) {
				$message['missingLicenses'] = $e->get_missing_licenses();
			}
			return new WP_REST_Response(
				$message,
				$e->getCode()
			);
		}

		return new WP_REST_Response( $data );
	}

	/**
	 * Get action path for the request.
	 *
	 * @param bool $is_woo_product_entity Whether the request is for a WooCommerce product entity.
	 *
	 * @return string The action path.
	 */
	public function get_action_path( $is_woo_product_entity = false ): string {
		$unlimited = '/usage/' . \gmdate( 'Y-m' );
		if ( $is_woo_product_entity && $this->addon_manager->has_valid_subscription( WPSEO_Addon_Manager::WOOCOMMERCE_SLUG ) ) {
			return $unlimited;
		}
		if ( ! $is_woo_product_entity && $this->addon_manager->has_valid_subscription( WPSEO_Addon_Manager::PREMIUM_SLUG ) ) {
			return $unlimited;
		}
		return '/usage/free-usages';
	}
}
