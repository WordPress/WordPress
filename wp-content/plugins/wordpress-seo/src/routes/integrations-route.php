<?php

namespace Yoast\WP\SEO\Routes;

use WP_REST_Request;
use WP_REST_Response;
use Yoast\WP\SEO\Actions\Integrations_Action;
use Yoast\WP\SEO\Conditionals\No_Conditionals;
use Yoast\WP\SEO\Main;

/**
 * Integrations_Route class.
 */
class Integrations_Route implements Route_Interface {

	use No_Conditionals;

	/**
	 * Represents the integrations route.
	 *
	 * @var string
	 */
	public const INTEGRATIONS_ROUTE = '/integrations';

	/**
	 * Represents a route to set the state of an integration.
	 *
	 * @var string
	 */
	public const SET_ACTIVE_ROUTE = '/set_active';

	/**
	 *  The integrations action.
	 *
	 * @var Integrations_Action
	 */
	private $integrations_action;

	/**
	 * Integrations_Route constructor.
	 *
	 * @param Integrations_Action $integrations_action The integrations action.
	 */
	public function __construct( Integrations_Action $integrations_action ) {
		$this->integrations_action = $integrations_action;
	}

	/**
	 * Registers routes with WordPress.
	 *
	 * @return void
	 */
	public function register_routes() {
		$set_active_route = [
			'methods'             => 'POST',
			'callback'            => [ $this, 'set_integration_active' ],
			'permission_callback' => [ $this, 'can_manage_options' ],
			'args'                => [
				'active' => [
					'type'     => 'boolean',
					'required' => true,
				],
				'integration' => [
					'type'     => 'string',
					'required' => true,
				],
			],
		];
		\register_rest_route( Main::API_V1_NAMESPACE, self::INTEGRATIONS_ROUTE . self::SET_ACTIVE_ROUTE, $set_active_route );
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
	 * Sets integration state.
	 *
	 * @param WP_REST_Request $request The request.
	 *
	 * @return WP_REST_Response
	 */
	public function set_integration_active( WP_REST_Request $request ) {
		$params           = $request->get_json_params();
		$integration_name = $params['integration'];
		$value            = $params['active'];

		$data = $this
			->integrations_action
			->set_integration_active( $integration_name, $value );

		return new WP_REST_Response(
			[ 'json' => $data ]
		);
	}
}
