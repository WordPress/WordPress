<?php
/**
 * Helper functions used for WebP Support.
 *
 * @package performance-lab
 * @since 2.1.0
 */

// @codeCoverageIgnoreStart
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
// @codeCoverageIgnoreEnd

/**
 * Callback for webp_enabled test.
 *
 * @since 1.0.0
 *
 * @return array{label: string, status: string, badge: array{label: string, color: string}, description: string, actions: string, test: string} Result.
 */
function webp_uploads_check_webp_supported_test(): array {
	$result = array(
		'label'       => __( 'Your site supports WebP', 'performance-lab' ),
		'status'      => 'good',
		'badge'       => array(
			'label' => __( 'Performance', 'performance-lab' ),
			'color' => 'blue',
		),
		'description' => sprintf(
			'<p>%s</p>',
			__( 'The WebP image format produces images that are usually smaller in size than JPEG images, which can reduce page load time and consume less bandwidth.', 'performance-lab' )
		),
		'actions'     => '',
		'test'        => 'is_webp_uploads_enabled',
	);

	$webp_supported = wp_image_editor_supports( array( 'mime_type' => 'image/webp' ) );

	if ( ! $webp_supported ) {
		$result['status']  = 'recommended';
		$result['label']   = __( 'Your site does not support WebP', 'performance-lab' );
		$result['actions'] = sprintf(
			'<p>%s</p>',
			/* translators: Accessibility text. */
			__( 'WebP support can only be enabled by your hosting provider. Please contact them to inquire about switching to a plan that supports WebP, or consider switching to a host that offers this capability. <a href="https://make.wordpress.org/hosting/2022/03/30/wordpress-hosting-and-webp-support/" target="_blank">Learn more</a> about WebP image support for WordPress sites.', 'performance-lab' )
		);
	}

	return $result;
}
