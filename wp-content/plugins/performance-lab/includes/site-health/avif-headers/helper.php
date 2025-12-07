<?php
/**
 * Helper function to detect and test AVIF header information.
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
 * Callback for avif_headers test.
 *
 * @since 3.6.0
 *
 * @return array{label: string, status: string, badge: array{label: string, color: string}, description: string, actions: string, test: string} Result.
 */
function avif_headers_check_avif_headers_test(): array {
	$result = array(
		'label'       => __( 'Your site sends AVIF image headers', 'performance-lab' ),
		'status'      => 'good',
		'badge'       => array(
			'label' => __( 'Performance', 'performance-lab' ),
			'color' => 'blue',
		),
		'description' => sprintf(
			'<p>%s</p>',
			esc_html(
				sprintf(
					/* translators: 1: image/avif, 2: content-type */
					__( 'Images with the mime type %1$s served without the correct %2$s header may not render properly.', 'performance-lab' ),
					'image/avif',
					'content-type'
				)
			)
		),
		'actions'     => '',
		'test'        => 'is_avif_headers_enabled',
	);

	$avif_headers_enabled = avif_headers_is_enabled();

	if ( ! $avif_headers_enabled ) {
		$result['status']  = 'recommended';
		$result['label']   = __( 'Your site does not send AVIF image headers correctly', 'performance-lab' );
		$result['actions'] = sprintf(
			'<p>%s</p>',
			esc_html__( 'AVIF headers can be enabled with a small configuration change by your hosting provider.', 'performance-lab' )
		);
	}

	return $result;
}

/**
 * Checks if AVIF headers are enabled.
 *
 * @since 3.6.0
 *
 * @return bool True if AVIF headers are enabled, false otherwise.
 */
function avif_headers_is_enabled(): bool {
	// Request an AVIF image at a known URL bundled with the plugin.
	$url = plugins_url( 'avif-headers/images/lossy.avif', __DIR__ );

	$response = wp_remote_request( $url, array( 'sslverify' => false ) );

	if ( is_wp_error( $response ) ) {
		return false;
	}

	// Check the image headers; the type should be `avif` not `octet-stream`.
	$headers = wp_remote_retrieve_headers( $response );

	if ( ! isset( $headers['content-type'] ) ) {
		return false;
	}

	$content_type = $headers['content-type'];

	return ( 'image/avif' === $content_type );
}
