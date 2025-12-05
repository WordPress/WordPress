<?php

namespace Yoast\WP\SEO\Routes;

use WP_REST_Request;
use WP_REST_Response;
use Yoast\WP\SEO\Actions\Configuration\First_Time_Configuration_Action;
use Yoast\WP\SEO\Conditionals\No_Conditionals;
use Yoast\WP\SEO\Main;

/**
 * First_Time_Configuration_Route class.
 */
class First_Time_Configuration_Route implements Route_Interface {

	use No_Conditionals;

	/**
	 * Represents the first time configuration route.
	 *
	 * @var string
	 */
	public const CONFIGURATION_ROUTE = '/configuration';

	/**
	 * Represents a site representation route.
	 *
	 * @var string
	 */
	public const SITE_REPRESENTATION_ROUTE = '/site_representation';

	/**
	 * Represents a social profiles route.
	 *
	 * @var string
	 */
	public const SOCIAL_PROFILES_ROUTE = '/social_profiles';

	/**
	 * Represents a route to enable/disable tracking.
	 *
	 * @var string
	 */
	public const ENABLE_TRACKING_ROUTE = '/enable_tracking';

	/**
	 * Represents a route to check if current user has the correct capabilities to edit another user's profile.
	 *
	 * @var string
	 */
	public const CHECK_CAPABILITY_ROUTE = '/check_capability';

	/**
	 * Represents a route to save the first time configuration state.
	 *
	 * @var string
	 */
	public const SAVE_CONFIGURATION_STATE_ROUTE = '/save_configuration_state';

	/**
	 * Represents a route to save the first time configuration state.
	 *
	 * @var string
	 */
	public const GET_CONFIGURATION_STATE_ROUTE = '/get_configuration_state';

	/**
	 *  The first tinme configuration action.
	 *
	 * @var First_Time_Configuration_Action
	 */
	private $first_time_configuration_action;

	/**
	 * First_Time_Configuration_Route constructor.
	 *
	 * @param First_Time_Configuration_Action $first_time_configuration_action The first-time configuration action.
	 */
	public function __construct( First_Time_Configuration_Action $first_time_configuration_action ) {
		$this->first_time_configuration_action = $first_time_configuration_action;
	}

	/**
	 * Registers routes with WordPress.
	 *
	 * @return void
	 */
	public function register_routes() {
		$site_representation_route = [
			'methods'             => 'POST',
			'callback'            => [ $this, 'set_site_representation' ],
			'permission_callback' => [ $this, 'can_manage_options' ],
			'args'                => [
				'company_or_person' => [
					'type'     => 'string',
					'enum'     => [
						'company',
						'person',
					],
					'required' => true,
				],
				'company_name' => [
					'type'     => 'string',
				],
				'company_logo' => [
					'type'     => 'string',
				],
				'company_logo_id' => [
					'type'     => 'integer',
				],
				'person_logo' => [
					'type'     => 'string',
				],
				'person_logo_id' => [
					'type'     => 'integer',
				],
				'company_or_person_user_id' => [
					'type'     => 'integer',
				],
				'description' => [
					'type'     => 'string',
				],
			],
		];
		\register_rest_route( Main::API_V1_NAMESPACE, self::CONFIGURATION_ROUTE . self::SITE_REPRESENTATION_ROUTE, $site_representation_route );

		$social_profiles_route = [
			'methods'             => 'POST',
			'callback'            => [ $this, 'set_social_profiles' ],
			'permission_callback' => [ $this, 'can_manage_options' ],
			'args'                => [
				'facebook_site' => [
					'type'     => 'string',
				],
				'twitter_site' => [
					'type'     => 'string',
				],
				'other_social_urls' => [
					'type'     => 'array',
				],
			],
		];
		\register_rest_route( Main::API_V1_NAMESPACE, self::CONFIGURATION_ROUTE . self::SOCIAL_PROFILES_ROUTE, $social_profiles_route );

		$check_capability_route = [
			'methods'             => 'GET',
			'callback'            => [ $this, 'check_capability' ],
			'permission_callback' => [ $this, 'can_manage_options' ],
			'args'                => [
				'user_id' => [
					'required' => true,
				],
			],
		];
		\register_rest_route( Main::API_V1_NAMESPACE, self::CONFIGURATION_ROUTE . self::CHECK_CAPABILITY_ROUTE, $check_capability_route );

		$enable_tracking_route = [
			'methods'             => 'POST',
			'callback'            => [ $this, 'set_enable_tracking' ],
			'permission_callback' => [ $this, 'can_manage_options' ],
			'args'                => [
				'tracking' => [
					'type'     => 'boolean',
					'required' => true,
				],
			],
		];
		\register_rest_route( Main::API_V1_NAMESPACE, self::CONFIGURATION_ROUTE . self::ENABLE_TRACKING_ROUTE, $enable_tracking_route );

		$save_configuration_state_route = [
			'methods'             => 'POST',
			'callback'            => [ $this, 'save_configuration_state' ],
			'permission_callback' => [ $this, 'can_manage_options' ],
			'args'                => [
				'finishedSteps' => [
					'type'     => 'array',
					'required' => true,
				],
			],
		];
		\register_rest_route( Main::API_V1_NAMESPACE, self::CONFIGURATION_ROUTE . self::SAVE_CONFIGURATION_STATE_ROUTE, $save_configuration_state_route );

		$get_configuration_state_route = [
			[
				'methods'             => 'GET',
				'callback'            => [ $this, 'get_configuration_state' ],
				'permission_callback' => [ $this, 'can_manage_options' ],
			],
		];
		\register_rest_route( Main::API_V1_NAMESPACE, self::CONFIGURATION_ROUTE . self::GET_CONFIGURATION_STATE_ROUTE, $get_configuration_state_route );
	}

	/**
	 * Sets the site representation values.
	 *
	 * @param WP_REST_Request $request The request.
	 *
	 * @return WP_REST_Response
	 */
	public function set_site_representation( WP_REST_Request $request ) {
		$data = $this
			->first_time_configuration_action
			->set_site_representation( $request->get_json_params() );

		return new WP_REST_Response( $data, $data->status );
	}

	/**
	 * Sets the social profiles values.
	 *
	 * @param WP_REST_Request $request The request.
	 *
	 * @return WP_REST_Response
	 */
	public function set_social_profiles( WP_REST_Request $request ) {
		$data = $this
			->first_time_configuration_action
			->set_social_profiles( $request->get_json_params() );

		return new WP_REST_Response(
			[ 'json' => $data ]
		);
	}

	/**
	 * Checks if the current user has the correct capability to edit a specific user.
	 *
	 * @param WP_REST_Request $request The request.
	 *
	 * @return WP_REST_Response
	 */
	public function check_capability( WP_REST_Request $request ) {
		$data = $this
			->first_time_configuration_action
			->check_capability( $request->get_param( 'user_id' ) );

		return new WP_REST_Response( $data );
	}

	/**
	 * Enables or disables tracking.
	 *
	 * @param WP_REST_Request $request The request.
	 *
	 * @return WP_REST_Response
	 */
	public function set_enable_tracking( WP_REST_Request $request ) {
		$data = $this
			->first_time_configuration_action
			->set_enable_tracking( $request->get_json_params() );

		return new WP_REST_Response( $data, $data->status );
	}

	/**
	 * Checks if the current user has the right capability.
	 *
	 * @return bool
	 */
	public function can_manage_options() {
		return \current_user_can( 'wpseo_manage_options' );
	}

	/**
	 * Checks if the current user has the capability to edit a specific user.
	 *
	 * @param WP_REST_Request $request The request.
	 *
	 * @return bool
	 */
	public function can_edit_user( WP_REST_Request $request ) {
		$response = $this->first_time_configuration_action->check_capability( $request->get_param( 'user_id' ) );
		return $response->success;
	}

	/**
	 * Checks if the current user has the capability to edit posts of other users.
	 *
	 * @return bool
	 */
	public function can_edit_other_posts() {
		return \current_user_can( 'edit_others_posts' );
	}

	/**
	 * Saves the first time configuration state.
	 *
	 * @param WP_REST_Request $request The request.
	 *
	 * @return WP_REST_Response
	 */
	public function save_configuration_state( WP_REST_Request $request ) {
		$data = $this
			->first_time_configuration_action
			->save_configuration_state( $request->get_json_params() );

		return new WP_REST_Response( $data, $data->status );
	}

	/**
	 * Returns the first time configuration state.
	 *
	 * @return WP_REST_Response the state of the configuration.
	 */
	public function get_configuration_state() {
		$data = $this
			->first_time_configuration_action
			->get_configuration_state();

		return new WP_REST_Response( $data, $data->status );
	}
}
