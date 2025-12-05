<?php

namespace Elementor\Modules\WpRest\Classes;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Elementor_Settings {

	public function register(): void {
		register_rest_route('elementor/v1', '/settings/(?P<key>[\w_-]+)', [
			[
				'methods' => \WP_REST_Server::READABLE,
				'permission_callback' => function (): bool {
					return current_user_can( 'manage_options' );
				},
				'sanitize_callback' => function ( string $param ): string {
					return esc_attr( $param );
				},
				'validate_callback' => function ( \WP_REST_Request $request ): bool {
					$params = $request->get_params();

					return 0 === strpos( $params['key'], 'elementor' );
				},
				'callback' => function ( $request ): \WP_REST_Response {
					try {
						$key = $request->get_param( 'key' );
						$current_value = get_option( $key );

						return new \WP_REST_Response([
							'success' => true,
							// Nest in order to allow extending the response with more details.
							'data' => [
								'value' => $current_value,
							],
						], 200);
					} catch ( \Exception $e ) {
						return new \WP_REST_Response([
							'success' => false,
							'data' => [
								'message' => $e->getMessage(),
							],
						], 500);
					}
				},
			],
		]);

		register_rest_route('elementor/v1', '/settings/(?P<key>[\w_-]+)', [
			[
				'methods' => \WP_REST_Server::EDITABLE,
				'permission_callback' => function (): bool {
					return current_user_can( 'manage_options' );
				},
				'sanitize_callback' => function ( string $param ): string {
					return esc_attr( $param );
				},
				'validate_callback' => function ( \WP_REST_Request $request ): bool {
					$params = $request->get_params();
					return 0 === strpos( $params['key'], 'elementor' ) && isset( $params['value'] );
				},
				'callback' => function ( \WP_REST_Request $request ): \WP_REST_Response {
					$key = $request->get_param( 'key' );
					$new_value = $request->get_param( 'value' );
					$current_value = get_option( $key );

					if ( $new_value === $current_value ) {
						return new \WP_REST_Response([
							'success' => true,
						], 200);
					}

					$success = update_option( $key, $new_value );
					if ( $success ) {
						return new \WP_REST_Response([
							'success' => true,
							'data' => [
								'message' => 'Setting updated successfully.',
							],
						], 200);
					} else {
						return new \WP_REST_Response([
							'success' => false,
							'data' => [
								'message' => 'Failed to update setting.',
							],
						], 500);
					}
				},
			],
		]);
	}
}
