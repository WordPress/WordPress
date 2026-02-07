<?php
/**
 * REST API integration for the plugin.
 *
 * @package performance-lab
 * @since 3.6.0
 */

// @codeCoverageIgnoreStart
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
// @codeCoverageIgnoreEnd

/**
 * Namespace for performance-lab REST API.
 *
 * @since 3.6.0
 * @var string
 */
const PERFLAB_REST_API_NAMESPACE = 'performance-lab/v1';

/**
 * Route for activating plugin/feature.
 *
 * Note the `:activate` art of the endpoint follows Google's guidance in AIP-136 for the use of the POST method in a way
 * that does not strictly follow the standard usage.
 *
 * @since 3.6.0
 * @link https://google.aip.dev/136
 * @var string
 */
const PERFLAB_FEATURES_ACTIVATE_ROUTE = '/features/(?P<slug>[a-z0-9_-]+):activate';

/**
 * Route for fetching plugin/feature information.
 *
 * @since 3.6.0
 * @var string
 */
const PERFLAB_FEATURES_INFORMATION_ROUTE = '/features/(?P<slug>[a-z0-9_-]+)';

/**
 * Registers endpoint for performance-lab REST API.
 *
 * @since 3.6.0
 * @access private
 */
function perflab_register_endpoint(): void {
	register_rest_route(
		PERFLAB_REST_API_NAMESPACE,
		PERFLAB_FEATURES_ACTIVATE_ROUTE,
		array(
			'methods'             => 'POST',
			'args'                => array(
				'slug' => array(
					'type'              => 'string',
					'description'       => __( 'Plugin slug of the Performance Lab feature to be activated.', 'performance-lab' ),
					'required'          => true,
					'validate_callback' => 'perflab_validate_slug_endpoint_arg',
				),
			),
			'callback'            => 'perflab_handle_feature_activation',
			'permission_callback' => static function () {
				// Important: The endpoint calls perflab_install_and_activate_plugin() which does more granular capability checks.
				if ( current_user_can( 'activate_plugins' ) ) {
					return true;
				}

				return new WP_Error( 'cannot_activate', __( 'Sorry, you are not allowed to activate this feature.', 'performance-lab' ) );
			},
		)
	);

	register_rest_route(
		PERFLAB_REST_API_NAMESPACE,
		PERFLAB_FEATURES_INFORMATION_ROUTE,
		array(
			'methods'             => 'GET',
			'args'                => array(
				'slug' => array(
					'type'              => 'string',
					'description'       => __( 'Plugin slug of plugin/feature whose information is needed.', 'performance-lab' ),
					'required'          => true,
					'validate_callback' => 'perflab_validate_slug_endpoint_arg',
				),
			),
			'callback'            => 'perflab_handle_get_feature_information',
			'permission_callback' => static function () {
				if ( current_user_can( 'manage_options' ) ) {
					return true;
				}

				return new WP_Error( 'cannot_access_plugin_settings_url', __( 'Sorry, you are not allowed to access plugin/feature information on this site.', 'performance-lab' ) );
			},
		)
	);
}
add_action( 'rest_api_init', 'perflab_register_endpoint' );

/**
 * Validates whether the provided plugin slug is a valid Performance Lab plugin.
 *
 * Note that an enum is not being used because additional PHP files have to be required to access the necessary functions,
 * and this would not be ideal to do at rest_api_init.
 *
 * @since 3.6.0
 * @access private
 *
 * @param string $slug Plugin slug.
 * @return bool Whether valid.
 */
function perflab_validate_slug_endpoint_arg( string $slug ): bool {
	require_once ABSPATH . 'wp-admin/includes/plugin.php';
	require_once PERFLAB_PLUGIN_DIR_PATH . 'includes/admin/load.php';
	require_once PERFLAB_PLUGIN_DIR_PATH . 'includes/admin/plugins.php';
	return in_array( $slug, perflab_get_standalone_plugins(), true );
}

/**
 * Handles REST API request to activate plugin/feature.
 *
 * @since 3.6.0
 * @access private
 *
 * @phpstan-param WP_REST_Request<array<string, mixed>> $request
 *
 * @param WP_REST_Request $request Request.
 * @return WP_REST_Response|WP_Error Response.
 */
function perflab_handle_feature_activation( WP_REST_Request $request ) {
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
	require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
	require_once ABSPATH . 'wp-admin/includes/class-wp-ajax-upgrader-skin.php';

	// Install and activate the plugin/feature and its dependencies.
	$result = perflab_install_and_activate_plugin( $request['slug'] );
	if ( is_wp_error( $result ) ) {
		switch ( $result->get_error_code() ) {
			case 'cannot_install_plugin':
			case 'cannot_activate_plugin':
				$response_code = rest_authorization_required_code();
				break;
			case 'plugin_not_found':
				$response_code = 404;
				break;
			default:
				$response_code = 500;
		}
		return new WP_Error(
			$result->get_error_code(),
			$result->get_error_message(),
			array( 'status' => $response_code )
		);
	}

	return new WP_REST_Response(
		array(
			'success' => true,
		)
	);
}

/**
 * Handles REST API request to get plugin/feature information.
 *
 * @since 3.6.0
 * @access private
 *
 * @phpstan-param WP_REST_Request<array<string, mixed>> $request
 *
 * @param WP_REST_Request $request Request.
 * @return WP_REST_Response Response.
 */
function perflab_handle_get_feature_information( WP_REST_Request $request ): WP_REST_Response {
	$plugin_settings_url = perflab_get_plugin_settings_url( $request['slug'] );

	return new WP_REST_Response(
		array(
			'slug'        => $request['slug'],
			'settingsUrl' => $plugin_settings_url,
		)
	);
}
