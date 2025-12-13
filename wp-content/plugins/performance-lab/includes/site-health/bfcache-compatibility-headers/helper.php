<?php
/**
 * Helper functions used for Cache-Control headers for bfcache compatibility site health check.
 *
 * @package performance-lab
 * @since 3.8.0
 */

// @codeCoverageIgnoreStart
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
// @codeCoverageIgnoreEnd

/**
 * Tests the Cache-Control headers for bfcache compatibility.
 *
 * @since 3.8.0
 * @access private
 *
 * @return array{label: string, status: string, badge: array{label: string, color: string}, description: string, actions: string, test: string} Result.
 */
function perflab_bfcache_compatibility_headers_check(): array {
	$result = array(
		'label'       => __( 'The Cache-Control page header is compatible with fast back/forward navigations', 'performance-lab' ),
		'status'      => 'good',
		'badge'       => array(
			'label' => __( 'Performance', 'performance-lab' ),
			'color' => 'blue',
		),
		'description' => '<p>' . wp_kses(
			__( "If the <code>Cache-Control</code> page response header includes the <code>no-store</code> directive then it can prevent instant back/forward navigations (using the browser's bfcache). This is not present for unauthenticated requests on your site, so it is configured properly. Note that there are other ways that bfcache can be disabled (e.g. you have JavaScript which uses a <code>unload</code> event listener). Also note that WordPress adds this directive for logged-in page responses for privacy/security reasons.", 'performance-lab' ),
			array( 'code' => array() )
		) . '</p>',
		'actions'     => '',
		'test'        => 'perflab_cch_cache_control_header_check',
	);

	$response = wp_remote_get(
		home_url( '/' ),
		array(
			'headers'   => array( 'Accept' => 'text/html' ),
			'sslverify' => false,
		)
	);

	if ( is_wp_error( $response ) ) {
		$result['label']       = __( 'Unable to check whether the Cache-Control page header is compatible with fast back/forward navigations', 'performance-lab' );
		$result['status']      = 'recommended';
		$result['description'] = '<p>' . wp_kses(
			sprintf(
				/* translators: 1: the error code, 2: the error message */
				__( 'The unauthenticated request to check the <code>Cache-Control</code> response header for the home page resulted in an error with code <code>%1$s</code> and the following message: %2$s.', 'performance-lab' ),
				esc_html( (string) $response->get_error_code() ),
				esc_html( rtrim( $response->get_error_message(), '.' ) )
			),
			array( 'code' => array() )
		) . '</p>';
		return $result;
	}

	$cache_control_headers = wp_remote_retrieve_header( $response, 'cache-control' );
	if ( '' === $cache_control_headers ) {
		// The Cache-Control header is not set, so it does not prevent bfcache. Return the default result.
		return $result;
	}

	foreach ( (array) $cache_control_headers as $cache_control_header ) {
		if ( str_contains( strtolower( $cache_control_header ), 'no-store' ) ) {
			$result['label']       = __( 'The Cache-Control page header is preventing fast back/forward navigations', 'performance-lab' );
			$result['status']      = 'recommended';
			$result['description'] = sprintf(
				'<p>%s</p>',
				sprintf(
					/* translators: 1: Cache-Control, 2: no-store */
					esc_html__( 'The %1$s response header for an unauthenticated request to the home page includes the %2$s directive. This can affect the performance of your site by preventing fast back/forward navigations (via the browser\'s bfcache).', 'performance-lab' ),
					'<code>Cache-Control</code>',
					'<code>no-store</code>'
				)
			);
			break;
		}
	}

	return $result;
}
