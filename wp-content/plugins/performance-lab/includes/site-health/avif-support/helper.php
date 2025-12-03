<?php
/**
 * Helper functions used for AVIF Support.
 *
 * @package performance-lab
 * @since 3.1.0
 */

// @codeCoverageIgnoreStart
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
// @codeCoverageIgnoreEnd

/**
 * Callback for avif_enabled test.
 *
 * @since 3.1.0
 *
 * @return array{label: string, status: string, badge: array{label: string, color: string}, description: string, actions: string, test: string} Result.
 */
function avif_uploads_check_avif_supported_test(): array {
	$result = array(
		'label'       => __( 'Your site supports AVIF', 'performance-lab' ),
		'status'      => 'good',
		'badge'       => array(
			'label' => __( 'Performance', 'performance-lab' ),
			'color' => 'blue',
		),
		'description' => sprintf(
			'<p>%s</p>',
			__( 'The AVIF image format generally has better compression than WebP, JPEG, PNG and GIF and is designed to supersede them, which can reduce page load time and consume less bandwidth.', 'performance-lab' )
		),
		'actions'     => '',
		'test'        => 'is_avif_uploads_enabled',
	);

	$avif_supported = wp_image_editor_supports( array( 'mime_type' => 'image/avif' ) );

	if ( ! $avif_supported ) {
		$result['status']  = 'recommended';
		$result['label']   = __( 'Your site does not support AVIF', 'performance-lab' );
		$result['actions'] = sprintf(
			'<p>%s</p>',
			/* translators: Accessibility text. */
			__( 'AVIF support can only be enabled by your hosting provider, so contact them for more information.', 'performance-lab' )
		);
	}

	return $result;
}
