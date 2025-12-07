<?php
/**
 * Helper function to detect if static assets have effective caching headers.
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
 * Callback for the effective caching headers test.
 *
 * @since 3.8.0
 * @access private
 *
 * @return array{label: string, status: string, badge: array{label: string, color: string}, description: string, actions: string, test: string} Result.
 */
function perflab_effective_asset_cache_headers_assets_test(): array {
	$result = array(
		'label'       => __( 'Your site serves static assets with an effective caching strategy', 'performance-lab' ),
		'status'      => 'good',
		'badge'       => array(
			'label' => __( 'Performance', 'performance-lab' ),
			'color' => 'blue',
		),
		'description' => sprintf(
			'<p>%s</p>',
			esc_html__(
				'Serving static assets with far-future expiration headers improves performance by allowing browsers to cache files for a long time, reducing repeated requests.',
				'performance-lab'
			)
		),
		'actions'     => '',
		'test'        => 'is_effective_asset_cache_headers_enabled',
	);

	// List of assets to check.
	$assets = array(
		includes_url( 'js/wp-embed.min.js' ),
		includes_url( 'css/buttons.min.css' ),
		includes_url( 'fonts/dashicons.woff2' ),
		includes_url( 'images/media/video.png' ),
	);

	/**
	 * Filters the list of assets to check for effective caching headers.
	 *
	 * @since 3.8.0
	 *
	 * @param string[] $assets List of asset URLs to check.
	 */
	$assets = apply_filters( 'perflab_effective_asset_cache_headers_assets_to_check', $assets );
	$assets = array_filter( (array) $assets, 'is_string' );

	// Check if effective caching headers are enabled for all assets.
	$results = perflab_effective_asset_cache_headers_check_assets( $assets );

	if ( 'good' !== $results['final_status'] ) {
		$result['status'] = $results['final_status'];
		$result['label']  = __( 'Your site does not serve static assets with an effective caching strategy', 'performance-lab' );

		if ( count( $results['details'] ) > 0 ) {
			$result['actions'] = sprintf(
				'<p>%s</p>%s<p>%s</p>',
				esc_html__( 'The following file types do not have the recommended effective Cache-Control or Expires headers. Consider adding or adjusting Cache-Control or Expires headers for these asset types.', 'performance-lab' ),
				perflab_effective_asset_cache_headers_get_status_table( $results['details'] ),
				esc_html__( 'Note: "Conditionally cached" means that the browser can re-validate the resource using ETag or Last-Modified headers. This results in fewer full downloads but still requires the browser to make requests, unlike far-future expiration headers that allow the browser to fully rely on its local cache for a longer duration.', 'performance-lab' )
			);
		}
		$result['actions'] .= sprintf(
			'<p>%s</p>',
			esc_html__( 'Effective Cache-Control or Expires headers can be added or adjusted with a small configuration change by your hosting provider.', 'performance-lab' )
		);
	}

	return $result;
}

/**
 * Checks if effective caching headers are enabled for a list of assets.
 *
 * @since 3.8.0
 * @access private
 *
 * @param  string[] $assets List of asset URLs to check.
 * @return array{final_status: string, details: array{filename: string, reason: string}[]} Final status and details.
 */
function perflab_effective_asset_cache_headers_check_assets( array $assets ): array {
	$final_status = 'good';
	$fail_details = array(); // Array of arrays with 'filename' and 'reason'.

	foreach ( $assets as $asset ) {
		$response = wp_remote_get( $asset, array( 'sslverify' => false ) );

		// Extract filename from the URL.
		$path_info = pathinfo( (string) wp_parse_url( $asset, PHP_URL_PATH ) );
		$filename  = $path_info['basename'] ?? basename( $asset );

		if ( is_wp_error( $response ) ) {
			// Can't determine headers if request failed, consider it a fail.
			$final_status   = 'recommended';
			$fail_details[] = array(
				'filename' => $filename,
				'reason'   => __( 'Could not retrieve headers', 'performance-lab' ),
			);
			continue;
		}

		$headers = wp_remote_retrieve_headers( $response );
		if ( ! is_object( $headers ) && 0 === count( $headers ) ) {
			// No valid headers retrieved.
			$final_status   = 'recommended';
			$fail_details[] = array(
				'filename' => $filename,
				'reason'   => __( 'No valid headers retrieved', 'performance-lab' ),
			);
			continue;
		}

		$check = perflab_effective_asset_cache_headers_check_headers( $headers );
		if ( $check['passed'] ) {
			// This asset passed effective caching headers test, no action needed.
			continue;
		}

		// If not passed, decide whether to try conditional request.
		if ( $check['missing_max_age'] ) {
			// Only if no effective caching headers at all, we try conditional request.
			$conditional_pass = perflab_effective_asset_cache_headers_try_conditional_request( $asset, $headers );
			$final_status     = 'recommended';
			if ( ! $conditional_pass ) {
				$fail_details[] = array(
					'filename' => $filename,
					'reason'   => __( 'No effective caching headers and no conditional caching', 'performance-lab' ),
				);
			} else {
				$fail_details[] = array(
					'filename' => $filename,
					'reason'   => __( 'No effective caching headers but conditionally cached', 'performance-lab' ),
				);
			}
		} else {
			// If there's a max-age or expires but below threshold, we skip conditional.
			$final_status   = 'recommended';
			$fail_details[] = array(
				'filename' => $filename,
				'reason'   => $check['reason'],
			);
		}
	}

	return array(
		'final_status' => $final_status,
		'details'      => $fail_details,
	);
}

/**
 * Checks if effective caching headers are enabled.
 *
 * @since 3.8.0
 * @access private
 *
 * @param WpOrg\Requests\Utility\CaseInsensitiveDictionary|array<string, string|array<string>> $headers Response headers.
 * @return array{passed: bool, reason: string, missing_max_age: bool} Detailed result of the check.
 */
function perflab_effective_asset_cache_headers_check_headers( $headers ): array {
	/**
	 * Filters the threshold for effective caching headers.
	 *
	 * @since 3.8.0
	 *
	 * @param int $threshold Threshold in seconds.
	 */
	$threshold = apply_filters( 'perflab_effective_asset_cache_headers_expiration_threshold', YEAR_IN_SECONDS );

	$cache_control = $headers['cache-control'] ?? '';
	$expires       = $headers['expires'] ?? '';

	// Check Cache-Control header for max-age.
	$max_age = 0;
	if ( '' !== $cache_control ) {
		// There can be multiple cache-control headers, we only care about max-age.
		foreach ( (array) $cache_control as $control ) {
			if ( 1 === preg_match( '/max-age\s*=\s*(\d+)/', $control, $matches ) ) {
				$max_age = (int) $matches[1];
				break;
			}
		}
	}

	// If max-age meets or exceeds the threshold, we consider it good.
	if ( $max_age >= $threshold ) {
		return array(
			'passed'          => true,
			'reason'          => '',
			'missing_max_age' => false,
		);
	}

	// If max-age is too low or not present, check Expires.
	if ( is_string( $expires ) && '' !== $expires ) {
		$expires_time   = strtotime( $expires );
		$remaining_time = is_int( $expires_time ) ? $expires_time - time() : 0;
		if ( $remaining_time >= $threshold ) {
			// Good - Expires far in the future.
			return array(
				'passed'          => true,
				'reason'          => '',
				'missing_max_age' => false,
			);
		}

		// Expires header exists but not far enough in the future.
		if ( $max_age > 0 ) {
			return array(
				'passed'          => false,
				'reason'          => sprintf(
					/* translators: 1: actual max-age value in seconds, 2: threshold in seconds */
					__( 'max-age below threshold (actual: %1$s seconds, threshold: %2$s seconds)', 'performance-lab' ),
					number_format_i18n( $max_age ),
					number_format_i18n( $threshold )
				),
				'missing_max_age' => false,
			);
		}
		return array(
			'passed'          => false,
			'reason'          => sprintf(
				/* translators: 1: actual Expires header value in seconds, 2: threshold in seconds */
				__( 'expires below threshold (actual: %1$s seconds, threshold: %2$s seconds)', 'performance-lab' ),
				number_format_i18n( $remaining_time ),
				number_format_i18n( $threshold )
			),
			'missing_max_age' => false,
		);
	}

	// No max-age or expires found at all or max-age < threshold and no expires.
	if ( 0 === $max_age ) {
		return array(
			'passed'          => false,
			'reason'          => '',
			'missing_max_age' => true,
		);
	} else {
		// max-age was present but below threshold and no expires.
		return array(
			'passed'          => false,
			'reason'          => sprintf(
				/* translators: 1: actual max-age value in seconds, 2: threshold in seconds */
				__( 'max-age below threshold (actual: %1$s seconds, threshold: %2$s seconds)', 'performance-lab' ),
				number_format_i18n( $max_age ),
				number_format_i18n( $threshold )
			),
			'missing_max_age' => false,
		);
	}
}

/**
 * Attempt a conditional request with ETag/Last-Modified.
 *
 * @since 3.8.0
 * @access private
 *
 * @param string                                                                               $url     The asset URL.
 * @param WpOrg\Requests\Utility\CaseInsensitiveDictionary|array<string, string|array<string>> $headers The initial response headers.
 * @return bool True if a 304 response was received.
 */
function perflab_effective_asset_cache_headers_try_conditional_request( string $url, $headers ): bool {
	$etag          = $headers['etag'] ?? '';
	$last_modified = $headers['last-modified'] ?? '';

	$conditional_headers = array();
	if ( '' !== $etag ) {
		$conditional_headers['If-None-Match'] = $etag;
	}
	if ( '' !== $last_modified ) {
		$conditional_headers['If-Modified-Since'] = $last_modified;
	}

	$response = wp_remote_get(
		$url,
		array(
			'sslverify' => false,
			'headers'   => $conditional_headers,
		)
	);

	if ( is_wp_error( $response ) ) {
		return false;
	}

	$status_code = wp_remote_retrieve_response_code( $response );
	return ( 304 === $status_code );
}

/**
 * Generate a table listing files that need effective caching headers, including reasons.
 *
 * @since 3.8.0
 * @access private
 *
 * @param array<array{filename: string, reason: string}> $fail_details Array of arrays with 'filename' and 'reason'.
 * @return string HTML formatted table.
 */
function perflab_effective_asset_cache_headers_get_status_table( array $fail_details ): string {
	$html_table = sprintf(
		'<table class="widefat striped"><thead><tr><th scope="col">%s</th><th scope="col">%s</th></tr></thead><tbody>',
		esc_html__( 'File', 'performance-lab' ),
		esc_html__( 'Status', 'performance-lab' )
	);

	foreach ( $fail_details as $detail ) {
		$html_table .= sprintf(
			'<tr><td>%s</td><td>%s</td></tr>',
			esc_html( $detail['filename'] ),
			esc_html( $detail['reason'] )
		);
	}

	$html_table .= '</tbody></table>';

	return $html_table;
}
