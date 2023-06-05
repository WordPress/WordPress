<?php
/**
 * WCCOM Site Installer REST API Controller Version 2
 *
 * Handles requests to /installer.
 *
 * @package WooCommerce\WCCom\API
 * @since 7.7.0
 */

use WC_REST_WCCOM_Site_Installer_Error_Codes as Installer_Error_Codes;
use WC_REST_WCCOM_Site_Installer_Error as Installer_Error;

defined( 'ABSPATH' ) || exit;

/**
 * REST API WCCOM Site Installer Controller Class.
 *
 * @extends WC_REST_Controller
 */
class WC_REST_WCCOM_Site_Installer_Controller_V2 extends WC_REST_Controller {

	/**
	 * Endpoint namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'wccom-site/v2';

	/**
	 * Route base.
	 *
	 * @var string
	 */
	protected $rest_base = 'installer';

	/**
	 * Register the routes for product reviews.
	 *
	 * @since 7.7.0
	 */
	public function register_routes() {

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base,
			array(
				array(
					'methods'             => WP_REST_Server::EDITABLE,
					'callback'            => array( $this, 'install' ),
					'permission_callback' => array( $this, 'check_permission' ),
					'args'                => array(
						'product-id'      => array(
							'required' => true,
							'type'     => 'integer',
						),
						'run-until-step'  => array(
							'required' => true,
							'type'     => 'string',
							'enum'     => WC_WCCOM_Site_Installation_Manager::STEPS,
						),
						'idempotency-key' => array(
							'required' => true,
							'type'     => 'string',
						),
					),
				),
				array(
					'methods'             => WP_REST_Server::DELETABLE,
					'callback'            => array( $this, 'reset_install' ),
					'permission_callback' => array( $this, 'check_permission' ),
					'args'                => array(
						'product-id'      => array(
							'required' => true,
							'type'     => 'integer',
						),
						'idempotency-key' => array(
							'required' => true,
							'type'     => 'string',
						),
					),
				),
			)
		);
	}

	/**
	 * Check permissions.
	 *
	 * @since 7.7.0
	 * @param WP_REST_Request $request Full details about the request.
	 * @return bool|WP_Error
	 */
	public function check_permission( $request ) {
		$current_user = wp_get_current_user();

		if ( empty( $current_user ) || ( $current_user instanceof WP_User && ! $current_user->exists() ) ) {
			/**
			 * This filter allows to provide a custom error message when the user is not authenticated.
			 *
			 * @since 3.7.0
			 */
			$error = apply_filters(
				WC_WCCOM_Site::AUTH_ERROR_FILTER_NAME,
				new Installer_Error( Installer_Error_Codes::NOT_AUTHENTICATED )
			);
			return new WP_Error(
				$error->get_error_code(),
				$error->get_error_message(),
				array( 'status' => $error->get_http_code() )
			);
		}

		if ( ! user_can( $current_user, 'install_plugins' ) || ! user_can( $current_user, 'install_themes' ) ) {
			$error = new Installer_Error( Installer_Error_Codes::NO_PERMISSION );
			return new WP_Error(
				$error->get_error_code(),
				$error->get_error_message(),
				array( 'status' => $error->get_http_code() )
			);
		}

		return true;
	}

	/**
	 * Install WooCommerce.com products.
	 *
	 * @since 7.7.0
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response|WP_Error
	 */
	public function install( $request ) {
		try {
			$product_id      = $request['product-id'];
			$run_until_step  = $request['run-until-step'];
			$idempotency_key = $request['idempotency-key'];

			$installation_manager = new WC_WCCOM_Site_Installation_Manager( $product_id, $idempotency_key );
			$installation_manager->run_installation( $run_until_step );

			$response = $this->success_response( $product_id );

		} catch ( Installer_Error $exception ) {
			$response = $this->failure_response( $product_id, $exception );
		}

		return $response;
	}

	/**
	 * Reset installation state.
	 *
	 * @since 7.7.0
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response|WP_Error
	 */
	public function reset_install( $request ) {
		try {
			$product_id      = $request['product-id'];
			$idempotency_key = $request['idempotency-key'];

			$installation_manager = new WC_WCCOM_Site_Installation_Manager( $product_id, $idempotency_key );
			$installation_manager->reset_installation();

			$response = $this->success_response( $product_id );

		} catch ( Installer_Error $exception ) {
			$response = $this->failure_response( $product_id, $exception );
		}

		return $response;
	}

	/**
	 * Generate a standardized response for a successful request.
	 *
	 * @param int $product_id Product ID.
	 * @return WP_REST_Response|WP_Error
	 */
	protected function success_response( $product_id ) {
		$state    = WC_WCCOM_Site_Installation_State_Storage::get_state( $product_id );
		$response = rest_ensure_response(
			array(
				'success' => true,
				'state'   => $state ? $this->map_state_to_response( $state ) : null,
			)
		);
		$response->set_status( 200 );
		return $response;
	}

	/**
	 * Generate a standardized response for a failed request.
	 *
	 * @param int             $product_id Product ID.
	 * @param Installer_Error $exception The exception.
	 * @return WP_REST_Response|WP_Error
	 */
	protected function failure_response( $product_id, $exception ) {
		$state    = WC_WCCOM_Site_Installation_State_Storage::get_state( $product_id );
		$response = rest_ensure_response(
			array(
				'success'       => false,
				'error_code'    => $exception->get_error_code(),
				'error_message' => $exception->get_error_message(),
				'state'         => $state ? $this->map_state_to_response( $state ) : null,
			)
		);
		$response->set_status( $exception->get_http_code() );
		return $response;
	}

	/**
	 * Map the installation state to a response.
	 *
	 * @param WC_WCCOM_Site_Installation_State $state The installation state.
	 * @return array
	 */
	protected function map_state_to_response( $state ) {
		return array(
			'product_id'                    => $state->get_product_id(),
			'idempotency_key'               => $state->get_idempotency_key(),
			'last_step_name'                => $state->get_last_step_name(),
			'last_step_status'              => $state->get_last_step_status(),
			'last_step_error'               => $state->get_last_step_error(),
			'product_type'                  => $state->get_product_type(),
			'product_name'                  => $state->get_product_name(),
			'already_installed_plugin_info' => $state->get_already_installed_plugin_info(),
			'started_seconds_ago'           => time() - $state->get_started_date(),
		);
	}
}

