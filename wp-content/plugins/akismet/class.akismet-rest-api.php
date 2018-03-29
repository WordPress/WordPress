<?php

class Akismet_REST_API {
	/**
	 * Register the REST API routes.
	 */
	public static function init() {
		if ( ! function_exists( 'register_rest_route' ) ) {
			// The REST API wasn't integrated into core until 4.4, and we support 4.0+ (for now).
			return false;
		}

		register_rest_route( 'akismet/v1', '/key', array(
			array(
				'methods' => WP_REST_Server::READABLE,
				'permission_callback' => array( 'Akismet_REST_API', 'privileged_permission_callback' ),
				'callback' => array( 'Akismet_REST_API', 'get_key' ),
			), array(
				'methods' => WP_REST_Server::EDITABLE,
				'permission_callback' => array( 'Akismet_REST_API', 'privileged_permission_callback' ),
				'callback' => array( 'Akismet_REST_API', 'set_key' ),
				'args' => array(
					'key' => array(
						'required' => true,
						'type' => 'string',
						'sanitize_callback' => array( 'Akismet_REST_API', 'sanitize_key' ),
						'description' => __( 'A 12-character Akismet API key. Available at akismet.com/get/', 'akismet' ),
					),
				),
			), array(
				'methods' => WP_REST_Server::DELETABLE,
				'permission_callback' => array( 'Akismet_REST_API', 'privileged_permission_callback' ),
				'callback' => array( 'Akismet_REST_API', 'delete_key' ),
			)
		) );

		register_rest_route( 'akismet/v1', '/settings/', array(
			array(
				'methods' => WP_REST_Server::READABLE,
				'permission_callback' => array( 'Akismet_REST_API', 'privileged_permission_callback' ),
				'callback' => array( 'Akismet_REST_API', 'get_settings' ),
			),
			array(
				'methods' => WP_REST_Server::EDITABLE,
				'permission_callback' => array( 'Akismet_REST_API', 'privileged_permission_callback' ),
				'callback' => array( 'Akismet_REST_API', 'set_boolean_settings' ),
				'args' => array(
					'akismet_strictness' => array(
						'required' => false,
						'type' => 'boolean',
						'description' => __( 'If true, Akismet will automatically discard the worst spam automatically rather than putting it in the spam folder.', 'akismet' ),
					),
					'akismet_show_user_comments_approved' => array(
						'required' => false,
						'type' => 'boolean',
						'description' => __( 'If true, show the number of approved comments beside each comment author in the comments list page.', 'akismet' ),
					),
				),
			)
		) );

		register_rest_route( 'akismet/v1', '/stats', array(
			'methods' => WP_REST_Server::READABLE,
			'permission_callback' => array( 'Akismet_REST_API', 'privileged_permission_callback' ),
			'callback' => array( 'Akismet_REST_API', 'get_stats' ),
			'args' => array(
				'interval' => array(
					'required' => false,
					'type' => 'string',
					'sanitize_callback' => array( 'Akismet_REST_API', 'sanitize_interval' ),
					'description' => __( 'The time period for which to retrieve stats. Options: 60-days, 6-months, all', 'akismet' ),
					'default' => 'all',
				),
			),
		) );

		register_rest_route( 'akismet/v1', '/stats/(?P<interval>[\w+])', array(
			'args' => array(
				'interval' => array(
					'description' => __( 'The time period for which to retrieve stats. Options: 60-days, 6-months, all', 'akismet' ),
					'type' => 'string',
				),
			),
			array(
				'methods' => WP_REST_Server::READABLE,
				'permission_callback' => array( 'Akismet_REST_API', 'privileged_permission_callback' ),
				'callback' => array( 'Akismet_REST_API', 'get_stats' ),
			)
		) );
	}

	/**
	 * Get the current Akismet API key.
	 *
	 * @param WP_REST_Request $request
	 * @return WP_Error|WP_REST_Response
	 */
	public static function get_key( $request = null ) {
		return rest_ensure_response( Akismet::get_api_key() );
	}

	/**
	 * Set the API key, if possible.
	 *
	 * @param WP_REST_Request $request
	 * @return WP_Error|WP_REST_Response
	 */
	public static function set_key( $request ) {
		if ( defined( 'WPCOM_API_KEY' ) ) {
			return rest_ensure_response( new WP_Error( 'hardcoded_key', __( 'This site\'s API key is hardcoded and cannot be changed via the API.', 'akismet' ), array( 'status'=> 409 ) ) );
		}

		$new_api_key = $request->get_param( 'key' );

		if ( ! self::key_is_valid( $new_api_key ) ) {
			return rest_ensure_response( new WP_Error( 'invalid_key', __( 'The value provided is not a valid and registered API key.', 'akismet' ), array( 'status' => 400 ) ) );
		}

		update_option( 'wordpress_api_key', $new_api_key );

		return self::get_key();
	}

	/**
	 * Unset the API key, if possible.
	 *
	 * @param WP_REST_Request $request
	 * @return WP_Error|WP_REST_Response
	 */
	public static function delete_key( $request ) {
		if ( defined( 'WPCOM_API_KEY' ) ) {
			return rest_ensure_response( new WP_Error( 'hardcoded_key', __( 'This site\'s API key is hardcoded and cannot be deleted.', 'akismet' ), array( 'status'=> 409 ) ) );
		}

		delete_option( 'wordpress_api_key' );

		return rest_ensure_response( true );
	}

	/**
	 * Get the Akismet settings.
	 *
	 * @param WP_REST_Request $request
	 * @return WP_Error|WP_REST_Response
	 */
	public static function get_settings( $request = null ) {
		return rest_ensure_response( array(
			'akismet_strictness' => ( get_option( 'akismet_strictness', '1' ) === '1' ),
			'akismet_show_user_comments_approved' => ( get_option( 'akismet_show_user_comments_approved', '1' ) === '1' ),
		) );
	}

	/**
	 * Update the Akismet settings.
	 *
	 * @param WP_REST_Request $request
	 * @return WP_Error|WP_REST_Response
	 */
	public static function set_boolean_settings( $request ) {
		foreach ( array(
			'akismet_strictness',
			'akismet_show_user_comments_approved',
		) as $setting_key ) {

			$setting_value = $request->get_param( $setting_key );
			if ( is_null( $setting_value ) ) {
				// This setting was not specified.
				continue;
			}

			// From 4.7+, WP core will ensure that these are always boolean
			// values because they are registered with 'type' => 'boolean',
			// but we need to do this ourselves for prior versions.
			$setting_value = Akismet_REST_API::parse_boolean( $setting_value );

			update_option( $setting_key, $setting_value ? '1' : '0' );
		}

		return self::get_settings();
	}

	/**
	 * Parse a numeric or string boolean value into a boolean.
	 *
	 * @param mixed $value The value to convert into a boolean.
	 * @return bool The converted value.
	 */
	public static function parse_boolean( $value ) {
		switch ( $value ) {
			case true:
			case 'true':
			case '1':
			case 1:
				return true;

			case false:
			case 'false':
			case '0':
			case 0:
				return false;

			default:
				return (bool) $value;
		}
	}

	/**
	 * Get the Akismet stats for a given time period.
	 *
	 * Possible `interval` values:
	 * - all
	 * - 60-days
	 * - 6-months
	 *
	 * @param WP_REST_Request $request
	 * @return WP_Error|WP_REST_Response
	 */
	public static function get_stats( $request ) {
		$api_key = Akismet::get_api_key();

		$interval = $request->get_param( 'interval' );

		$stat_totals = array();

		$response = Akismet::http_post( Akismet::build_query( array( 'blog' => get_option( 'home' ), 'key' => $api_key, 'from' => $interval ) ), 'get-stats' );

		if ( ! empty( $response[1] ) ) {
			$stat_totals[$interval] = json_decode( $response[1] );
		}

		return rest_ensure_response( $stat_totals );
	}

	private static function key_is_valid( $key ) {
		$response = Akismet::http_post(
			Akismet::build_query(
				array(
					'key' => $key,
					'blog' => get_option( 'home' )
				)
			),
			'verify-key'
		);

		if ( $response[1] == 'valid' ) {
			return true;
		}

		return false;
	}

	public static function privileged_permission_callback() {
		return current_user_can( 'manage_options' );
	}

	public static function sanitize_interval( $interval, $request, $param ) {
		$interval = trim( $interval );

		$valid_intervals = array( '60-days', '6-months', 'all', );

		if ( ! in_array( $interval, $valid_intervals ) ) {
			$interval = 'all';
		}

		return $interval;
	}

	public static function sanitize_key( $key, $request, $param ) {
		return trim( $key );
	}
}
