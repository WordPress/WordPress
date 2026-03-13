<?php
/**
 * Hook callbacks used for AVIF Support.
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
 * Adds tests to site health.
 *
 * @since 3.1.0
 *
 * @param array{direct: array<string, array{label: string, test: string}>} $tests Site Health Tests.
 * @return array{direct: array<string, array{label: string, test: string}>} Amended tests.
 */
function avif_uploads_add_is_avif_supported_test( array $tests ): array {
	$tests['direct']['avif_supported'] = array(
		'label' => __( 'AVIF Support', 'performance-lab' ),
		'test'  => 'avif_uploads_check_avif_supported_test',
	);
	return $tests;
}
add_filter( 'site_status_tests', 'avif_uploads_add_is_avif_supported_test' );
