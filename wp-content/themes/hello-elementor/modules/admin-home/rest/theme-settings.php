<?php

namespace HelloTheme\Modules\AdminHome\Rest;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use HelloTheme\Modules\AdminHome\Components\Settings_Controller;
use WP_REST_Server;

class Theme_Settings extends Rest_Base {

	public function register_routes() {
		register_rest_route(
			self::ROUTE_NAMESPACE,
			'/theme-settings',
			[
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => [ $this, 'get_theme_settings' ],
				'permission_callback' => [ $this, 'permission_callback' ],
			]
		);

		register_rest_route(
			self::ROUTE_NAMESPACE,
			'/theme-settings',
			[
				'methods'             => WP_REST_Server::EDITABLE,
				'callback'            => [ $this, 'set_theme_settings' ],
				'permission_callback' => [ $this, 'permission_callback' ],
			]
		);
	}

	public function get_theme_settings() {
		return rest_ensure_response(
			[
				'settings' => Settings_Controller::get_settings(),
			]
		);
	}

	public function set_theme_settings( \WP_REST_Request $request ) {
		$settings = $request->get_param( 'settings' );

		if ( ! is_array( $settings ) ) {
			return new \WP_Error(
				'invalid_settings',
				esc_html__( 'Settings must be an array', 'hello-elementor' ),
				[ 'status' => 400 ]
			);
		}

		$settings_map = Settings_Controller::get_settings_mapping();

		foreach ( $settings as $key => $value ) {
			if ( ! array_key_exists( $key, $settings_map ) ) {
				continue;
			}

			$value = $value ? 'true' : 'false';

			update_option( $settings_map[ $key ], $value );
		}

		return rest_ensure_response( [ 'settings' => $settings ] );
	}
}
