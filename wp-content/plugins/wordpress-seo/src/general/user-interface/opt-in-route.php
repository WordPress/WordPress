<?php

namespace Yoast\WP\SEO\General\User_Interface;

use WP_REST_Request;
use WP_REST_Response;
use Yoast\WP\SEO\Conditionals\User_Can_Manage_Wpseo_Options_Conditional;
use Yoast\WP\SEO\Helpers\Capability_Helper;
use Yoast\WP\SEO\Helpers\User_Helper;
use Yoast\WP\SEO\Main;
use Yoast\WP\SEO\Routes\Route_Interface;

/**
 * Registers a route to get dismiss opt in notification.
 */
class Opt_In_Route implements Route_Interface {

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
	public const SEEN_ROUTE = '/seen-opt-in-notification';

	/**
	 * Holds the user helper.
	 *
	 * @var User_Helper
	 */
	private $user_helper;

	/**
	 * Holds the capability helper instance.
	 *
	 * @var Capability_Helper
	 */
	private $capability_helper;

	/**
	 * Returns the conditionals based on which this integration should be active.
	 *
	 * @return array<User_Can_Manage_Wpseo_Options_Conditional> The array of conditionals.
	 */
	public static function get_conditionals() {
		return [
			User_Can_Manage_Wpseo_Options_Conditional::class,
		];
	}

	/**
	 * Constructs Opt_In_Route.
	 *
	 * @param User_Helper       $user_helper       The user helper.
	 * @param Capability_Helper $capability_helper The capability helper.
	 */
	public function __construct( User_Helper $user_helper, Capability_Helper $capability_helper ) {
		$this->user_helper       = $user_helper;
		$this->capability_helper = $capability_helper;
	}

	/**
	 * Registers routes with WordPress.
	 *
	 * @return void
	 */
	public function register_routes() {
		$opt_in_seen_route_args = [
			'methods'             => 'POST',
			'callback'            => [ $this, 'set_opt_in_seen' ],
			'permission_callback' => [ $this, 'can_see_opt_in' ],
			'args'                => [
				'key' => [
					'required'          => true,
					'type'              => 'string',
					'sanitize_callback' => 'sanitize_text_field',
					'validate_callback' => [ $this, 'validate_key' ],
				],
			],
		];

		\register_rest_route( Main::API_V1_NAMESPACE, self::SEEN_ROUTE, $opt_in_seen_route_args );
	}

	/**
	 * Sets the opt-in notification as seen.
	 *
	 * @param WP_REST_Request $request The request. This request should have a key param set.
	 *
	 * @return WP_REST_Response The response.
	 */
	public function set_opt_in_seen( $request ) {
		$key             = $request->get_param( 'key' );
		$current_user_id = $this->user_helper->get_current_user_id();

		$result  = $this->user_helper->update_meta( $current_user_id, $key, true );
		$success = $result !== false;
		$status  = ( $success ) ? 200 : 400;

		return new WP_REST_Response(
			(object) [
				'success' => $success,
				'status'  => $status,
			],
			$status
		);
	}

	/**
	 * Whether or not the current user is allowed to see the opt-in notification.
	 *
	 * @return bool Whether or not the current user is allowed to see the opt-in notification.
	 */
	public function can_see_opt_in() {
		return $this->capability_helper->current_user_can( 'wpseo_manage_options' );
	}

	/**
	 * Validates the key parameter.
	 *
	 * @param string $key The key to validate.
	 *
	 * @return bool Whether the key is valid.
	 */
	public function validate_key( $key ) {
		$allowed_keys = [
			'wpseo_seen_llm_txt_opt_in_notification',
		];

		return \in_array( $key, $allowed_keys, true );
	}
}
