<?php
/**
 * Hook callbacks used for effective caching headers.
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
 * Adds tests to site health.
 *
 * @since 3.8.0
 * @access private
 *
 * @param array{direct: array<string, array{label: string, test: string}>} $tests Site Health Tests.
 * @return array{direct: array<string, array{label: string, test: string}>} Amended tests.
 */
function perflab_effective_asset_cache_headers_add_test( array $tests ): array {
	/*
	 * Static assets are expected to not have effective cache headers in non-production environments.
	 *
	 * GH Issue: https://github.com/WordPress/performance/issues/2031
	 */
	if ( ! in_array( wp_get_environment_type(), array( 'local', 'development' ), true ) ) {
		$tests['direct']['effective_asset_cache_headers'] = array(
			'label' => __( 'Effective Caching Headers', 'performance-lab' ),
			'test'  => 'perflab_effective_asset_cache_headers_assets_test',
		);
	}

	return $tests;
}
add_filter( 'site_status_tests', 'perflab_effective_asset_cache_headers_add_test' );
