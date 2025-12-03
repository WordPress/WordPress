<?php
/**
 * Hook callbacks used for Enqueued Assets Health Check.
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
 * Adds tests to site health.
 *
 * @since 1.0.0
 *
 * @param array{direct: array<string, array{label: string, test: string}>} $tests Site Health Tests.
 * @return array{direct: array<string, array{label: string, test: string}>} Amended tests.
 */
function perflab_aea_add_enqueued_assets_test( array $tests ): array {
	$tests['async']['enqueued_blocking_assets'] = array(
		'label'             => __( 'Blocking assets', 'performance-lab' ),
		'test'              => 'enqueued-blocking-assets-test',
		'has_rest'          => false,
		'async_direct_test' => 'perflab_aea_enqueued_blocking_assets_test',
	);

	return $tests;
}
add_filter( 'site_status_tests', 'perflab_aea_add_enqueued_assets_test' );
add_action( 'wp_ajax_health-check-enqueued-blocking-assets-test', 'perflab_aea_enqueued_ajax_blocking_assets_test' );
