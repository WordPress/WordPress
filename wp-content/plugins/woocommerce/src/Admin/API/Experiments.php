<?php
/**
 * REST API Experiment Controller
 *
 * Handles requests to /experiment
 */

namespace Automattic\WooCommerce\Admin\API;

defined( 'ABSPATH' ) || exit;

/**
 * Data controller.
 *
 * @extends WC_REST_Data_Controller
 */
class Experiments extends \WC_REST_Data_Controller {

	/**
	 * Endpoint namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'wc-admin';

	/**
	 * Route base.
	 *
	 * @var string
	 */
	protected $rest_base = 'experiments';

	/**
	 * Register routes.
	 */
	public function register_routes() {
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/assignment',
			array(
				array(
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_assignment' ),
					'permission_callback' => array( $this, 'get_item_permissions_check' ),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);
	}


	/**
	 * Forward the experiment request to WP.com and return the WP.com response.
	 *
	 * @param \WP_REST_Request $request Request data.
	 *
	 * @return \WP_Error|\WP_REST_Response
	 */
	public function get_assignment( $request ) {
		$args = $request->get_query_params();

		if ( ! isset( $args['experiment_name'] ) ) {
			return new \WP_Error(
				'woocommerce_rest_experiment_name_required',
				__( 'Sorry, experiment_name is required.', 'woocommerce' ),
				array( 'status' => 400 )
			);
		}

		unset( $args['rest_route'] );

		$abtest   = new \WooCommerce\Admin\Experimental_Abtest(
			$request->get_param( 'anon_id' ) ?? '',
			'woocommerce',
			true, // set consent to true here since frontend has checked it already.
			true  // set true to send request as auth user.
		);
		$response = $abtest->request_assignment( $args );
		if ( is_wp_error( $response ) ) {
			return $response;
		}

		return json_decode( $response['body'], true );
	}
}
