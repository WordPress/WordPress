<?php
/**
 * Hook callbacks used for Server Timing.
 *
 * @package performance-lab
 *
 * @since 3.1.0
 */

// @codeCoverageIgnoreStart
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
// @codeCoverageIgnoreEnd

/**
 * Adds server timing to REST API response.
 *
 * @since 3.1.0
 *
 * @param WP_REST_Response|WP_Error $response Result to send to the client. Usually a `WP_REST_Response`.
 * @return WP_REST_Response|WP_Error Filtered response.
 */
function perflab_rest_post_dispatch_add_server_timing( $response ) {
	if ( ! wp_is_rest_endpoint() || ! $response instanceof WP_REST_Response ) {
		return $response;
	}

	$server_timing = perflab_server_timing();

	/** This filter is documented in includes/server-timing/class-perflab-server-timing.php */
	do_action( 'perflab_server_timing_send_header' );

	$header_value = $server_timing->get_header();

	if ( '' !== $header_value ) {
		$response->header( 'Server-Timing', $header_value, false );
	}

	return $response;
}
add_filter( 'rest_post_dispatch', 'perflab_rest_post_dispatch_add_server_timing', PHP_INT_MAX );
