<?php

namespace Yoast\WP\SEO\Content_Type_Visibility\User_Interface;

use WP_REST_Request;
use WP_REST_Response;
use Yoast\WP\SEO\Conditionals\No_Conditionals;
use Yoast\WP\SEO\Content_Type_Visibility\Application\Content_Type_Visibility_Dismiss_Notifications;
use Yoast\WP\SEO\Main;
use Yoast\WP\SEO\Routes\Route_Interface;

/**
 * Handles the dismiss route for "New" badges of new content types in settings menu.
 * @phpcs:disable Yoast.NamingConventions.ObjectNameDepth.MaxExceeded
 */
class Content_Type_Visibility_Dismiss_New_Route implements Route_Interface {

	use No_Conditionals;

	/**
	 * Represents the alerts route prefix.
	 *
	 * @var string
	 */
	public const ROUTE_PREFIX = 'new-content-type-visibility';

	/**
	 * Represents post type dismiss route.
	 *
	 * @var string
	 */
	public const POST_TYPE_DISMISS_ROUTE = self::ROUTE_PREFIX . '/dismiss-post-type';

	/**
	 * Represents taxonomy dismiss route.
	 *
	 * @var string
	 */
	public const TAXONOMY_DISMISS_ROUTE = self::ROUTE_PREFIX . '/dismiss-taxonomy';

	/**
	 * Holds the Options_Helper instance.
	 *
	 * @var Content_Type_Visibility_Dismiss_Notifications
	 */
	private $dismiss_notifications;

	/**
	 * Constructs Content_Type_Visibility_Dismiss_New_Route.
	 *
	 * @param Content_Type_Visibility_Dismiss_Notifications $dismiss_notifications The options helper.
	 */
	public function __construct( Content_Type_Visibility_Dismiss_Notifications $dismiss_notifications ) {
		$this->dismiss_notifications = $dismiss_notifications;
	}

	/**
	 * Registers routes with WordPress.
	 *
	 * @return void
	 */
	public function register_routes() {
		$post_type_dismiss_route_args = [
			'methods'             => 'POST',
			'callback'            => [ $this, 'post_type_dismiss_callback' ],
			'permission_callback' => [ $this, 'can_dismiss' ],
			'args'                => [
				'postTypeName' => [
					'validate_callback' => [ $this, 'validate_post_type' ],
				],
			],
		];

		$taxonomy_dismiss_route_args = [
			'methods'             => 'POST',
			'callback'            => [ $this, 'taxonomy_dismiss_callback' ],
			'permission_callback' => [ $this, 'can_dismiss' ],
			'args'                => [
				'taxonomyName' => [
					'validate_callback' => [ $this, 'validate_taxonomy' ],
				],
			],
		];

		\register_rest_route( Main::API_V1_NAMESPACE, self::POST_TYPE_DISMISS_ROUTE, $post_type_dismiss_route_args );
		\register_rest_route( Main::API_V1_NAMESPACE, self::TAXONOMY_DISMISS_ROUTE, $taxonomy_dismiss_route_args );
	}

	/**
	 * Whether or not the current user is allowed to dismiss alerts.
	 *
	 * @return bool Whether or not the current user is allowed to dismiss alerts.
	 */
	public function can_dismiss() {
		return \current_user_can( 'edit_posts' );
	}

	/**
	 * Validates post type.
	 *
	 * @param string          $param   The parameter.
	 * @param WP_REST_Request $request Full details about the request.
	 * @param string          $key     The key.
	 *
	 * @return bool
	 */
	public function validate_post_type( $param, $request, $key ) {
		return \post_type_exists( $param );
	}

	/**
	 * Wrapper method for Content_Type_Visibility_Dismiss_Notifications::post_type_dismiss().
	 *
	 * @param WP_REST_Request $request The request. This request should have a key param set.
	 *
	 * @return WP_REST_Response The response.
	 */
	public function post_type_dismiss_callback( $request ) {
		$response = $this->dismiss_notifications->post_type_dismiss( $request['post_type_name'] );

		return new WP_REST_Response(
			(object) $response,
			$response['status']
		);
	}

	/**
	 * Validates taxonomy.
	 *
	 * @param string          $param   The parameter.
	 * @param WP_REST_Request $request Full details about the request.
	 * @param string          $key     The key.
	 *
	 * @return bool
	 */
	public function validate_taxonomy( $param, $request, $key ) {
		return \taxonomy_exists( $param );
	}

	/**
	 * Wrapper method for Content_Type_Visibility_Dismiss_Notifications::taxonomy_dismiss().
	 *
	 * @param WP_REST_Request $request The request. This request should have a key param set.
	 *
	 * @return WP_REST_Response The response.
	 */
	public function taxonomy_dismiss_callback( WP_REST_Request $request ) {
		$response = $this->dismiss_notifications->taxonomy_dismiss( $request['taxonomy_name'] );

		return new WP_REST_Response(
			(object) $response,
			$response['status']
		);
	}
}
