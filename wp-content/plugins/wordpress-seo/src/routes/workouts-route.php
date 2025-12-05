<?php

namespace Yoast\WP\SEO\Routes;

use WP_REST_Request;
use WP_REST_Response;
use Yoast\WP\SEO\Conditionals\No_Conditionals;
use Yoast\WP\SEO\Helpers\Options_Helper;
use Yoast\WP\SEO\Main;

/**
 * Workouts_Route class.
 */
class Workouts_Route implements Route_Interface {

	use No_Conditionals;

	/**
	 * Represents workouts route.
	 *
	 * @var string
	 */
	public const WORKOUTS_ROUTE = '/workouts';

	/**
	 * The Options helper.
	 *
	 * @var Options_Helper
	 */
	private $options_helper;

	/**
	 * Workouts_Route constructor.
	 *
	 * @param Options_Helper $options_helper The options helper.
	 */
	public function __construct( Options_Helper $options_helper ) {
		$this->options_helper = $options_helper;
	}

	/**
	 * Registers routes with WordPress.
	 *
	 * @return void
	 */
	public function register_routes() {
		$edit_others_posts = static function () {
			return \current_user_can( 'edit_others_posts' );
		};

		$workouts_route = [
			[
				'methods'             => 'GET',
				'callback'            => [ $this, 'get_workouts' ],
				'permission_callback' => $edit_others_posts,
			],
			[
				'methods'             => 'POST',
				'callback'            => [ $this, 'set_workouts' ],
				'permission_callback' => $edit_others_posts,
				'args'                => $this->get_workouts_routes_args(),
			],
		];

		\register_rest_route( Main::API_V1_NAMESPACE, self::WORKOUTS_ROUTE, $workouts_route );
	}

	/**
	 * Returns the workouts as configured for the site.
	 *
	 * @return WP_REST_Response the configuration of the workouts.
	 */
	public function get_workouts() {
		$workouts_option = $this->options_helper->get( 'workouts_data' );

		/**
		 * Filter: 'Yoast\WP\SEO\workouts_options' - Allows adding workouts options by the add-ons.
		 *
		 * @param array $workouts_option The content of the `workouts_data` option in Free.
		 */
		$workouts_option = \apply_filters( 'Yoast\WP\SEO\workouts_options', $workouts_option );

		return new WP_REST_Response(
			[ 'json' => $workouts_option ]
		);
	}

	/**
	 * Sets the workout configuration.
	 *
	 * @param WP_REST_Request $request The request object.
	 *
	 * @return WP_REST_Response the configuration of the workouts.
	 */
	public function set_workouts( $request ) {
		$workouts_data = $request->get_json_params();

		/**
		 * Filter: 'Yoast\WP\SEO\workouts_route_save' - Allows the add-ons to save the options data in their own options.
		 *
		 * @param mixed|null $result The result of the previous saving operation.
		 *
		 * @param array $workouts_data The full set of workouts option data to save.
		 */
		$result = \apply_filters( 'Yoast\WP\SEO\workouts_route_save', null, $workouts_data );

		return new WP_REST_Response(
			[ 'json' => $result ]
		);
	}

	/**
	 * Gets the args for all the registered workouts.
	 *
	 * @return array
	 */
	private function get_workouts_routes_args() {
		$args_array = [];

		/**
		 * Filter: 'Yoast\WP\SEO\workouts_route_args' - Allows the add-ons add their own arguments to the route registration.
		 *
		 * @param array $args_array The array of arguments for the route registration.
		 */
		return \apply_filters( 'Yoast\WP\SEO\workouts_route_args', $args_array );
	}
}
