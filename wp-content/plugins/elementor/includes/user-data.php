<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class User_Data {
	const API_NAMESPACE = 'elementor/v1';
	const API_BASE = '/user-data/current-user';

	public static function init() {
		add_action( 'rest_api_init', fn() => self::register_routes() );
	}

	private static function register_routes() {
		register_rest_route( self::API_NAMESPACE, self::API_BASE, [
			[
				'methods' => 'GET',
				'callback' => fn( $request ) => self::route_wrapper( fn() => self::get_current_user( $request ) ),
				'permission_callback' => fn() => is_user_logged_in(),
			],
			[
				'methods' => 'PATCH',
				'callback' => fn( $request ) => self::route_wrapper( fn() => self::update_current_user( $request ) ),
				'permission_callback' => fn() => is_user_logged_in(),
				'args' => [
					'suppressedMessages' => [
						'required' => false,
						'type' => 'array',
						'description' => 'Array of suppressed message keys',
						'items' => [
							'type' => 'string',
						],
						'validate_callback' => function( $param, $request, $key ) {
							return is_array( $param );
						},
						'sanitize_callback' => fn( $param, $request, $key ) => self::sanitize_suppressed_messages( $param, $request, $key ),
					],
				],
			],
		] );
	}

	/**
	 * @param \WP_REST_Request $request The request object.
	 * @return \WP_REST_Response|\WP_Error Response object or error.
	 */
	public static function get_current_user( $request ) {
		$current_user = wp_get_current_user();
		$introduction_meta = User::get_introduction_meta();

		$suppressed_messages = [];
		if ( is_array( $introduction_meta ) ) {
			foreach ( $introduction_meta as $key => $value ) {
				if ( $value ) {
					$suppressed_messages[] = $key;
				}
			}
		}

		$capabilities = array_keys( $current_user->allcaps );

		$data = [
			'suppressedMessages' => $suppressed_messages,
			'capabilities' => $capabilities,
		];

		return new \WP_REST_Response( $data, 200 );
	}

	/**
	 * @param \WP_REST_Request $request The request object.
	 * @return \WP_REST_Response|\WP_Error Response object or error.
	 */
	public static function update_current_user( $request ) {
		$user_id = get_current_user_id();

		$suppressed_messages = $request->get_param( 'suppressedMessages' );

		if ( $request->has_param( 'suppressedMessages' ) && is_array( $suppressed_messages ) ) {
			$introduction_meta = [];
			foreach ( $suppressed_messages as $message ) {
				$introduction_meta[ $message ] = true;
			}

			update_user_meta( $user_id, User::INTRODUCTION_KEY, $introduction_meta );
		}

		return self::get_current_user( $request );
	}

	/**
	 * @param array            $param The parameter value.
	 * @param \WP_REST_Request $request The request object.
	 * @param string           $key The parameter key.
	 *
	 * @return array|null The sanitized array or null.
	 */
	public static function sanitize_suppressed_messages( $param, $request, $key ) {
		if ( ! is_array( $param ) ) {
			return null;
		}

		$sanitized_messages = [];
		foreach ( $param as $message ) {
			if ( is_string( $message ) ) {
				$sanitized_message = sanitize_text_field( $message );

				if ( ! empty( $sanitized_message ) ) {
					$sanitized_messages[] = $sanitized_message;
				}
			}
		}

		return $sanitized_messages;
	}

	private static function route_wrapper( callable $cb ) {
		try {
			$response = $cb();
		} catch ( \Exception $e ) {
			return new \WP_Error( 'unexpected_error', 'Something went wrong', [ 'status' => 500 ] );
		}

		return $response;
	}
}
