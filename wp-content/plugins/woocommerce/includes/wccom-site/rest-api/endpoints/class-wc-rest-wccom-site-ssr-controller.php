<?php
/**
 * WCCOM Site System Status Report REST API Controller
 *
 * Handles requests to /ssr.
 *
 * @package WooCommerce\WCCom\API
 * @since   7.8.0
 */

use WC_REST_WCCOM_Site_Installer_Error_Codes as Installer_Error_Codes;
use WC_REST_WCCOM_Site_Installer_Error as Installer_Error;

defined( 'ABSPATH' ) || exit;

/**
 * REST API WCCOM System Status Report Controller Class.
 *
 * @extends WC_REST_Controller
 */
class WC_REST_WCCOM_Site_SSR_Controller extends WC_REST_Controller {

	/**
	 * Endpoint namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'wccom-site/v1';

	/**
	 * Route base.
	 *
	 * @var string
	 */
	protected $rest_base = 'ssr';

	/**
	 * Register the routes for SSR Controller.
	 *
	 * @since 7.8.0
	 */
	public function register_routes() {
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base,
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'handle_ssr_request' ),
					'permission_callback' => array( $this, 'check_permission' ),
				),
			),
		);
	}

	/**
	 * Check permissions.
	 *
	 * Please note that access to this endpoint is also governed by the WC_WCCOM_Site::authenticate_wccom() method.
	 *
	 * @since  7.8.0
	 * @param  WP_REST_Request $request Full details about the request.
	 * @return bool|WP_Error
	 */
	public function check_permission( $request ) {
		$current_user = wp_get_current_user();

		if ( empty( $current_user ) || ( $current_user instanceof WP_User && ! $current_user->exists() ) ) {
			/**
			 * This filter allows to provide a custom error message when the user is not authenticated.
			 *
			 * @since 7.8.0
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

		if ( ! user_can( $current_user, 'manage_woocommerce' ) ) {
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
	 * Generate SSR data and submit it to WooCommmerce.com.
	 *
	 * @since  7.8.0
	 * @param  WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response
	 */
	public function handle_ssr_request( $request ) {
		$ssr_controller = new WC_REST_System_Status_Controller();
		$data           = $ssr_controller->get_items( $request );
		$data           = $data->get_data();

		// Submit SSR data to WooCommerce.com.
		$request = WC_Helper_API::post(
			'ssr',
			array(
				'body'          => wp_json_encode( array( 'data' => $data ) ),
				'authenticated' => true,
			)
		);

		$response_code = wp_remote_retrieve_response_code( $request );

		if ( 201 === $response_code ) {
			$response = rest_ensure_response(
				array(
					'success' => true,
					'message' => 'SSR data submitted successfully',
				)
			);
		} else {
			$response = rest_ensure_response(
				array(
					'success'       => false,
					'error_code'    => 'failed_submitting_ssr',
					'error_message' => "Submitting SSR data failed with response code: $response_code",
				)
			);
		}

		return $response;
	}
}
