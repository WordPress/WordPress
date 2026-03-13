<?php
/**
 * Hook callbacks used for AVIF Headers.
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
 * Adds tests to site health.
 *
 * @since 3.6.0
 *
 * @param array{direct: array<string, array{label: string, test: string}>} $tests Site Health Tests.
 * @return array{direct: array<string, array{label: string, test: string}>} Amended tests.
 */
function avif_headers_add_is_avif_headers_enabled_test( array $tests ): array {
	$tests['direct']['avif_headers'] = array(
		'label' => __( 'AVIF Headers', 'performance-lab' ),
		'test'  => 'avif_headers_check_avif_headers_test',
	);
	return $tests;
}
add_filter( 'site_status_tests', 'avif_headers_add_is_avif_headers_enabled_test' );
