<?php
namespace Elementor\Core\Common\Modules\Connect\Rest;

use Elementor\Plugin;
use WP_Http;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor Library Connect REST API.
 *
 * REST API controller for handling library connect operations.
 */
class Rest_Api {

	/**
	 * REST API namespace.
	 */
	const REST_NAMESPACE = 'elementor/v1';

	/**
	 * REST API base.
	 */
	const REST_BASE = 'library';

	/**
	 * Authentication mode.
	 */
	const AUTH_MODE = 'rest';

	/**
	 * Register REST API routes.
	 *
	 * @access public
	 * @return void
	 */
	public function register_routes() {
		register_rest_route(
			self::REST_NAMESPACE,
			self::REST_BASE . '/connect',
			[
				[
					'methods' => \WP_REST_Server::CREATABLE,
					'callback' => [ $this, 'connect' ],
					'permission_callback' => [ $this, 'connect_permissions_check' ],
					'args' => [
						'token' => [
							'required' => true,
							'type' => 'string',
							'description' => 'Connect CLI token',
						],
					],
				],
			]
		);

		register_rest_route(
			self::REST_NAMESPACE,
			self::REST_BASE . '/connect',
			[
				[
					'methods' => \WP_REST_Server::DELETABLE,
					'callback' => [ $this, 'disconnect' ],
					'permission_callback' => [ $this, 'connect_permissions_check' ],
				],
			]
		);
	}
	public function connect( \WP_REST_Request $request ) {
		$app = $this->get_connect_app();
		if ( ! $app ) {
			return $this->elementor_library_app_not_available();
		}

		$app->set_auth_mode( self::AUTH_MODE );
		$_REQUEST['mode'] = self::AUTH_MODE;
		$_REQUEST['token'] = $request->get_param( 'token' );

		try {
			$app->action_authorize();
			$app->action_get_token();

			if ( $app->is_connected() ) {
				return $this->success_response(
					[ 'message' => __( 'Connected successfully.', 'elementor' ) ],
				WP_Http::CREATED );
			} else {
				return $this->error_response(
					'elementor_library_not_connected',
					__( 'Failed to connect to Elementor Library.', 'elementor' ),
					WP_Http::INTERNAL_SERVER_ERROR
				);
			}
		} catch ( \Exception $e ) {
			return $this->error_response(
				'elementor_library_connect_error',
				$e->getMessage(),
				WP_Http::INTERNAL_SERVER_ERROR
			);
		}
	}

	public function disconnect( \WP_REST_Request $request ) {
		$app = $this->get_connect_app();
		if ( ! $app ) {
			return $this->elementor_library_app_not_available();
		}

		$app->set_auth_mode( self::AUTH_MODE );
		$_REQUEST['mode'] = self::AUTH_MODE;

		try {
			$app->action_disconnect();
			return $this->success_response(
				[ 'message' => __( 'Disconnected successfully.', 'elementor' ) ],
				WP_Http::OK
			);
		} catch ( \Exception $e ) {
			return $this->error_response(
				'elementor_library_disconnect_error',
				$e->getMessage(),
				WP_Http::INTERNAL_SERVER_ERROR
			);
		}
	}

	public function connect_permissions_check( \WP_REST_Request $request ) {
		return current_user_can( 'manage_options' );
	}

	private function route_wrapper( callable $cb ) {
		try {
			$response = $cb();
		} catch ( \Exception $e ) {
			return $this->error_response(
				'unexpected_error',
				__( 'Something went wrong', 'elementor' ),
				WP_Http::INTERNAL_SERVER_ERROR
			);
		}
		return $response;
	}

	private function error_response( $code, $message, $status = WP_Http::BAD_REQUEST ) {
		return new \WP_Error(
			$code,
			$message,
			[ 'status' => $status ]
		);
	}

	private function success_response( $data = [], $status = WP_Http::OK ) {
		$response = rest_ensure_response( array_merge( [ 'success' => true ], $data ) );
		$response->set_status( $status );
		return $response;
	}

	private function elementor_library_app_not_available() {
		return $this->error_response(
			'elementor_library_app_not_available',
			__( 'Elementor Library app is not available.', 'elementor' ),
			WP_Http::INTERNAL_SERVER_ERROR
		);
	}

	/**
	 * Get the connect app.
	 *
	 * @return \Elementor\Core\Common\Modules\Connect\Apps\Library|null
	 */
	private function get_connect_app() {
		$connect = Plugin::$instance->common->get_component( 'connect' );
		if ( ! $connect ) {
			return null;
		}
		$app = $connect->get_app( 'library' );
		if ( ! $app ) {
			$connect->init();
			$app = $connect->get_app( 'library' );
		}
		return $app;
	}
}
