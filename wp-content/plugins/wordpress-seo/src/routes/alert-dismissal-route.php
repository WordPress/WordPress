<?php

namespace Yoast\WP\SEO\Routes;

use WP_REST_Request;
use WP_REST_Response;
use Yoast\WP\SEO\Actions\Alert_Dismissal_Action;
use Yoast\WP\SEO\Conditionals\No_Conditionals;
use Yoast\WP\SEO\Main;

/**
 * Class Alert_Dismissal_Route.
 */
class Alert_Dismissal_Route implements Route_Interface {

	use No_Conditionals;

	/**
	 * Represents the alerts route prefix.
	 *
	 * @var string
	 */
	public const ROUTE_PREFIX = 'alerts';

	/**
	 * Represents the dismiss route.
	 *
	 * @var string
	 */
	public const DISMISS_ROUTE = self::ROUTE_PREFIX . '/dismiss';

	/**
	 * Represents the full dismiss route.
	 *
	 * @var string
	 */
	public const FULL_DISMISS_ROUTE = Main::API_V1_NAMESPACE . '/' . self::DISMISS_ROUTE;

	/**
	 * Represents the alert dismissal action.
	 *
	 * @var Alert_Dismissal_Action
	 */
	protected $alert_dismissal_action;

	/**
	 * Constructs Alert_Dismissal_Route.
	 *
	 * @param Alert_Dismissal_Action $alert_dismissal_action The alert dismissal action.
	 */
	public function __construct( Alert_Dismissal_Action $alert_dismissal_action ) {
		$this->alert_dismissal_action = $alert_dismissal_action;
	}

	/**
	 * Registers routes with WordPress.
	 *
	 * @return void
	 */
	public function register_routes() {
		$dismiss_route_args = [
			'methods'             => 'POST',
			'callback'            => [ $this, 'dismiss' ],
			'permission_callback' => [ $this, 'can_dismiss' ],
			'args'                => [
				'key' => [
					'validate_callback' => [ $this->alert_dismissal_action, 'is_allowed' ],
					'required'          => true,
				],
			],
		];

		\register_rest_route( Main::API_V1_NAMESPACE, self::DISMISS_ROUTE, $dismiss_route_args );
	}

	/**
	 * Dismisses an alert.
	 *
	 * @param WP_REST_Request $request The request. This request should have a key param set.
	 *
	 * @return WP_REST_Response The response.
	 */
	public function dismiss( WP_REST_Request $request ) {
		$success = $this->alert_dismissal_action->dismiss( $request['key'] );
		$status  = $success === ( true ) ? 200 : 400;

		return new WP_REST_Response(
			(object) [
				'success' => $success,
				'status'  => $status,
			],
			$status
		);
	}

	/**
	 * Whether or not the current user is allowed to dismiss alerts.
	 *
	 * @return bool Whether or not the current user is allowed to dismiss alerts.
	 */
	public function can_dismiss() {
		return \current_user_can( 'edit_posts' );
	}
}
