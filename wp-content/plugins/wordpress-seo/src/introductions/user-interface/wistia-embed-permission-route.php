<?php

namespace Yoast\WP\SEO\Introductions\User_Interface;

use Exception;
use WP_Error;
use WP_REST_Request;
use WP_REST_Response;
use Yoast\WP\SEO\Conditionals\No_Conditionals;
use Yoast\WP\SEO\Helpers\User_Helper;
use Yoast\WP\SEO\Introductions\Infrastructure\Wistia_Embed_Permission_Repository;
use Yoast\WP\SEO\Main;
use Yoast\WP\SEO\Routes\Route_Interface;

/**
 * Registers a route to offer get/set of the wistia embed permission for a user.
 */
class Wistia_Embed_Permission_Route implements Route_Interface {

	use No_Conditionals;

	/**
	 * Represents the prefix.
	 *
	 * @var string
	 */
	public const ROUTE_PREFIX = '/wistia_embed_permission';

	/**
	 * Holds the repository.
	 *
	 * @var Wistia_Embed_Permission_Repository
	 */
	private $wistia_embed_permission_repository;

	/**
	 * Holds the user helper.
	 *
	 * @var User_Helper
	 */
	private $user_helper;

	/**
	 * Constructs the class.
	 *
	 * @param Wistia_Embed_Permission_Repository $wistia_embed_permission_repository The repository.
	 * @param User_Helper                        $user_helper                        The user helper.
	 */
	public function __construct(
		Wistia_Embed_Permission_Repository $wistia_embed_permission_repository,
		User_Helper $user_helper
	) {
		$this->wistia_embed_permission_repository = $wistia_embed_permission_repository;
		$this->user_helper                        = $user_helper;
	}

	/**
	 * Registers routes with WordPress.
	 *
	 * @return void
	 */
	public function register_routes() {
		\register_rest_route(
			Main::API_V1_NAMESPACE,
			self::ROUTE_PREFIX,
			[
				[
					'methods'             => 'GET',
					'callback'            => [ $this, 'get_wistia_embed_permission' ],
					'permission_callback' => [ $this, 'permission_edit_posts' ],
				],
				[
					'methods'             => 'POST',
					'callback'            => [ $this, 'set_wistia_embed_permission' ],
					'permission_callback' => [ $this, 'permission_edit_posts' ],
					'args'                => [
						'value' => [
							'required'          => false,
							'type'              => 'bool',
							'default'           => true,
						],
					],
				],
			]
		);
	}

	/**
	 * Gets the value of the wistia embed permission.
	 *
	 * @return WP_REST_Response|WP_Error The response, or an error.
	 */
	public function get_wistia_embed_permission() {
		try {
			$user_id = $this->user_helper->get_current_user_id();
			$value   = $this->wistia_embed_permission_repository->get_value_for_user( $user_id );
		} catch ( Exception $exception ) {
			return new WP_Error(
				'wpseo_wistia_embed_permission_error',
				$exception->getMessage(),
				(object) []
			);
		}

		return new WP_REST_Response(
			[
				'json' => (object) [
					'value' => $value,
				],
			]
		);
	}

	/**
	 * Sets the value of the wistia embed permission.
	 *
	 * @param WP_REST_Request $request The request object.
	 *
	 * @return WP_REST_Response|WP_Error The success or failure response.
	 */
	public function set_wistia_embed_permission( WP_REST_Request $request ) {
		$params = $request->get_json_params();
		$value  = \boolval( $params['value'] );

		try {
			$user_id = $this->user_helper->get_current_user_id();
			$result  = $this->wistia_embed_permission_repository->set_value_for_user( $user_id, $value );
		} catch ( Exception $exception ) {
			return new WP_Error(
				'wpseo_wistia_embed_permission_error',
				$exception->getMessage(),
				(object) []
			);
		}

		return new WP_REST_Response(
			[
				'json' => (object) [
					'success' => $result,
				],
			],
			( $result ) ? 200 : 400
		);
	}

	/**
	 * Permission callback.
	 *
	 * @return bool True when user has 'edit_posts' permission.
	 */
	public function permission_edit_posts() {
		return \current_user_can( 'edit_posts' );
	}
}
