<?php

namespace Elementor\Modules\Ai\SitePlannerConnect;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Just a simple rest api to validate new Site Planner Connect feature exists.
 */
class Wp_Rest_Api {

	public function register(): void {
		register_rest_route('elementor-ai/v1', 'permissions', [
			[
				'methods' => \WP_REST_Server::READABLE,
				'permission_callback' => function () {
					return current_user_can( 'manage_options' );
				},
				'callback' => function () {
					try {
						wp_send_json_success( [
							'site_planner_connect' => true,
						] );
					} catch ( \Exception $e ) {
						wp_send_json_error( [
							'message' => $e->getMessage(),
						] );
					}
				},
			],
		] );
	}
}
