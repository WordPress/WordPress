<?php
/**
 * Hook callbacks used for cache-control headers.
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
 * Add the bfcache compatibility check to site health tests.
 *
 * @since 3.8.0
 * @access private
 *
 * @param array{direct: array<string, array{label: string, test: string}>} $tests Site Health Tests.
 * @return array{direct: array<string, array{label: string, test: string}>} Amended tests.
 */
function perflab_bfcache_compatibility_headers_add_test( array $tests ): array {
	$tests['direct']['perflab_bfcache_compatibility_headers'] = array(
		'label' => __( 'Cache-Control headers may prevent fast back/forward navigation', 'performance-lab' ),
		'test'  => 'perflab_bfcache_compatibility_headers_check',
	);
	return $tests;
}
add_filter( 'site_status_tests', 'perflab_bfcache_compatibility_headers_add_test' );
