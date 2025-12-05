<?php

namespace Yoast\WP\SEO\Introductions\User_Interface;

use Exception;
use WP_Error;
use WP_REST_Request;
use WP_REST_Response;
use Yoast\WP\SEO\Conditionals\No_Conditionals;
use Yoast\WP\SEO\Helpers\User_Helper;
use Yoast\WP\SEO\Introductions\Application\Introductions_Collector;
use Yoast\WP\SEO\Introductions\Infrastructure\Introductions_Seen_Repository;
use Yoast\WP\SEO\Main;
use Yoast\WP\SEO\Routes\Route_Interface;

/**
 * Registers a route to set whether the user has seen an introduction.
 *
 * @makePublic
 */
class Introductions_Seen_Route implements Route_Interface {

	use No_Conditionals;

	/**
	 * Represents the prefix.
	 *
	 * @var string
	 */
	public const ROUTE_PREFIX = '/introductions/(?P<introduction_id>[\w-]+)/seen';

	/**
	 * Holds the introductions collector instance.
	 *
	 * @var Introductions_Collector
	 */
	private $introductions_collector;

	/**
	 * Holds the repository.
	 *
	 * @var Introductions_Seen_Repository
	 */
	private $introductions_seen_repository;

	/**
	 * Holds the user helper.
	 *
	 * @var User_Helper
	 */
	private $user_helper;

	/**
	 * Constructs the class.
	 *
	 * @param Introductions_Seen_Repository $introductions_seen_repository The repository.
	 * @param User_Helper                   $user_helper                   The user helper.
	 * @param Introductions_Collector       $introductions_collector       The introduction collector.
	 */
	public function __construct(
		Introductions_Seen_Repository $introductions_seen_repository,
		User_Helper $user_helper,
		Introductions_Collector $introductions_collector
	) {
		$this->introductions_seen_repository = $introductions_seen_repository;
		$this->user_helper                   = $user_helper;
		$this->introductions_collector       = $introductions_collector;
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
					'methods'             => 'POST',
					'callback'            => [ $this, 'set_introduction_seen' ],
					'permission_callback' => [ $this, 'permission_edit_posts' ],
					'args'                => [
						'introduction_id' => [
							'required'          => true,
							'type'              => 'string',
							'sanitize_callback' => 'sanitize_text_field',
						],
						'is_seen'         => [
							'required'          => false,
							'type'              => 'bool',
							'default'           => true,
							'sanitize_callback' => 'rest_sanitize_boolean',
						],
					],
				],
			]
		);
	}

	/**
	 * Sets whether the introduction is seen.
	 *
	 * @param WP_REST_Request $request The request object.
	 *
	 * @return WP_REST_Response|WP_Error The success or failure response.
	 */
	public function set_introduction_seen( WP_REST_Request $request ) {
		$params          = $request->get_params();
		$introduction_id = $params['introduction_id'];
		$is_seen         = $params['is_seen'];

		if ( $this->introductions_collector->is_available_introduction( $introduction_id ) ) {
			try {
				$user_id = $this->user_helper->get_current_user_id();
				$result  = $this->introductions_seen_repository->set_introduction( $user_id, $introduction_id, $is_seen );
			} catch ( Exception $exception ) {
				return new WP_Error(
					'wpseo_introductions_seen_error',
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
		return new WP_REST_Response( [], 400 );
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
