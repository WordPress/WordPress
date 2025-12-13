<?php
/**
 * Helper functions used for Enqueued Assets Health Check.
 *
 * @package performance-lab
 * @since 1.0.0
 */

// @codeCoverageIgnoreStart
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
// @codeCoverageIgnoreEnd

/**
 * Audit blocking assets on the front page.
 *
 * @since 4.0.0
 *
 * @return array{
 *             response: WP_Error|array{
 *                 headers: WpOrg\Requests\Utility\CaseInsensitiveDictionary,
 *                 body: string,
 *                 response: array{
 *                     code: int|false,
 *                     message: string|false,
 *                 },
 *             },
 *             assets: array{
 *                 scripts: array<array{ src: string, size: int|null, error: WP_Error|null }>,
 *                 styles: array<array{ src: string, size: int|null, error: WP_Error|null }>,
 *             }
 *         } An array containing response and blocking assets.
 */
function perflab_aea_audit_blocking_assets(): array {
	$response = wp_remote_get(
		add_query_arg( 'cache_bust', (string) wp_rand(), home_url( '/' ) ),
		array(
			'timeout' => 10,
			'headers' => array_merge(
				array(
					'Accept' => 'text/html',
				),
				perflab_get_http_basic_authorization_headers()
			),
		)
	);

	$result = array(
		'response' => $response,
		'assets'   => array(
			'scripts' => array(),
			'styles'  => array(),
		),
	);

	if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
		return $result;
	}

	$html = wp_remote_retrieve_body( $response );
	if ( '' === $html ) {
		return $result;
	}

	$processor = new WP_HTML_Tag_Processor( $html );

	while ( $processor->next_tag() ) {
		$tag = $processor->get_tag();

		if ( 'SCRIPT' === $tag ) {
			$src = $processor->get_attribute( 'src' );
			if ( ! is_string( $src ) ) {
				continue;
			}

			// Note that when the "type" attribute is absent or empty, the element is treated as a classic JavaScript script.
			$type = $processor->get_attribute( 'type' );

			// Skip external script with "async" or "defer" attributes.
			if ( null !== $processor->get_attribute( 'async' ) || null !== $processor->get_attribute( 'defer' ) ) {
				continue;
			}

			// Skip external script with a "type" attribute set to "module" as they are deferred by default.
			if ( 'module' === strtolower( (string) $type ) ) {
				continue;
			}

			// Skip external script with a "type" attribute that is not JavaScript.
			if (
				is_string( $type ) &&
				'' !== $type &&
				! (
					str_contains( $type, 'javascript' ) ||
					str_contains( $type, 'ecmascript' ) ||
					str_contains( $type, 'jscript' ) ||
					str_contains( $type, 'livescript' )
				)
			) {
				continue;
			}

			$size                          = perflab_aea_get_asset_size( $src );
			$result['assets']['scripts'][] = array(
				'src'   => $src,
				'size'  => is_wp_error( $size ) ? null : $size,
				'error' => is_wp_error( $size ) ? $size : null,
			);
		} elseif ( 'LINK' === $tag ) {
			$rel = $processor->get_attribute( 'rel' );
			if ( 'stylesheet' !== strtolower( (string) $rel ) ) {
				continue;
			}

			$media = $processor->get_attribute( 'media' );
			if ( is_string( $media ) && 1 !== preg_match( '/^\s*(all|screen)\b/i', $media ) ) {
				continue;
			}

			$href = $processor->get_attribute( 'href' );
			if ( ! is_string( $href ) ) {
				continue;
			}

			$size                         = perflab_aea_get_asset_size( $href );
			$result['assets']['styles'][] = array(
				'src'   => $href,
				'size'  => is_wp_error( $size ) ? null : $size,
				'error' => is_wp_error( $size ) ? $size : null,
			);
		}
	}

	return $result;
}

/**
 * Callback for enqueued_blocking_assets test.
 *
 * @since 4.0.0
 *
 * @return array{
 *             label: string,
 *             status: 'good'|'recommended',
 *             badge: array{label: string, color: non-empty-string},
 *             description: string,
 *             actions: string,
 *             test: string
 *         } Result.
 */
function perflab_aea_enqueued_blocking_assets_test(): array {
	$result = array(
		'label'       => __( 'Any blocking assets do not appear to be particularly problematic', 'performance-lab' ),
		'status'      => 'good',
		'badge'       => array(
			'label' => __( 'Performance', 'performance-lab' ),
			'color' => 'blue',
		),
		'description' => '',
		'actions'     => '',
		'test'        => 'enqueued_blocking_assets',
	);

	$audit_result = perflab_aea_audit_blocking_assets();

	$retrieval_failure_result = perflab_aea_blocking_assets_retrieval_failure( $audit_result['response'] );
	if ( null !== $retrieval_failure_result ) {
		return array_merge( $result, $retrieval_failure_result );
	}

	$scripts_result = perflab_aea_enqueued_blocking_scripts( $audit_result['assets'] );
	$styles_result  = perflab_aea_enqueued_blocking_styles( $audit_result['assets'] );

	$result['description'] .= perflab_aea_generate_blocking_assets_table( $audit_result['assets'] );

	$result['description'] .= $scripts_result['description'];
	$result['description'] .= $styles_result['description'];

	if (
		'good' !== $scripts_result['status'] ||
		'good' !== $styles_result['status']
	) {
		$result['label']   = __( 'Your site may have a problem with blocking assets', 'performance-lab' );
		$result['status']  = 'recommended';
		$result['actions'] = sprintf(
			/* translators: 1: HelpHub URL. 2: Link description. 3.URL to clean cache. 4. Clean Cache text. */
			'<p><a target="_blank" href="%1$s">%2$s</a></p>',
			esc_url( __( 'https://wordpress.org/support/article/optimization/', 'performance-lab' ) ),
			__( 'More info about performance optimization', 'performance-lab' )
		);
	}

	return $result;
}

/**
 * Callback for enqueued_blocking_assets test via AJAX.
 *
 * @since 4.0.0
 */
function perflab_aea_enqueued_ajax_blocking_assets_test(): void {
	check_ajax_referer( 'health-check-site-status' );

	if ( ! current_user_can( 'view_site_health_checks' ) ) {
		wp_send_json_error();
	}

	wp_send_json_success( perflab_aea_enqueued_blocking_assets_test() );
}

/**
 * Prepares the blocking scripts audit result.
 *
 * @since 4.0.0
 *
 * @phpstan-param array{
 *                    scripts: array<array{ src: string, size: int|null, error: WP_Error|null }>,
 *                    styles: array<array{ src: string, size: int|null, error: WP_Error|null }>,
 *                } $blocking_assets
 *
 * @param array<string, mixed> $blocking_assets Array of blocking assets.
 * @return array{status: 'good'|'recommended', description: string} Result.
 */
function perflab_aea_enqueued_blocking_scripts( array $blocking_assets ): array {
	$enqueued_scripts = count( $blocking_assets['scripts'] );
	$bytes_enqueued   = array_reduce(
		$blocking_assets['scripts'],
		static function ( $carry, $asset ): int {
			return $carry + ( $asset['size'] ?? 0 );
		},
		0
	);

	$result = array(
		'status'      => 'good',
		'description' => sprintf(
			'<p>%s</p>',
			esc_html(
				sprintf(
					/* translators: 1: Number of blocking styles. 2.Styles size. */
					_n(
						'The amount of %1$s blocking script (size: %2$s) is acceptable.',
						'The amount of %1$s blocking scripts (size: %2$s) is acceptable.',
						$enqueued_scripts,
						'performance-lab'
					),
					$enqueued_scripts,
					size_format( $bytes_enqueued )
				)
			)
		),
	);

	/**
	 * Filters number of enqueued scripts to trigger warning.
	 *
	 * @since 1.0.0
	 *
	 * @param int $scripts_threshold Scripts threshold number. Default 30.
	 */
	$scripts_threshold = apply_filters( 'perflab_aea_enqueued_scripts_threshold', 30 );

	/**
	 * Filters size of enqueued scripts to trigger warning.
	 *
	 * @since 1.0.0
	 *
	 * @param int $scripts_size_threshold Enqueued Scripts size (in bytes) threshold. Default 300000.
	 */
	$scripts_size_threshold = apply_filters( 'perflab_aea_enqueued_scripts_byte_size_threshold', 300000 );

	if ( $enqueued_scripts > $scripts_threshold || $bytes_enqueued > $scripts_size_threshold ) {
		$result['status'] = 'recommended';

		$result['description'] = sprintf(
			'<p>%s</p>',
			esc_html(
				sprintf(
					/* translators: 1: Number of blocking scripts. 2. Scripts size. */
					_n(
						'Your website has %1$s blocking script (size: %2$s). Try to reduce the number or to concatenate them.',
						'Your website has %1$s blocking scripts (size: %2$s). Try to reduce the number or to concatenate them.',
						$enqueued_scripts,
						'performance-lab'
					),
					$enqueued_scripts,
					size_format( $bytes_enqueued )
				)
			)
		);
	}

	// If one of the assets had an error, then fail the test even if under the threshold.
	foreach ( $blocking_assets['scripts'] as $script ) {
		if ( is_wp_error( $script['error'] ) ) {
			$result['status'] = 'recommended';
			break;
		}
	}

	return $result;
}

/**
 * Prepares the blocking styles audit result.
 *
 * @since 4.0.0
 *
 * @phpstan-param array{
 *                    scripts: array<array{ src: string, size: int|null, error: WP_Error|null }>,
 *                    styles: array<array{ src: string, size: int|null, error: WP_Error|null }>,
 *                } $blocking_assets
 *
 * @param array<string, mixed> $blocking_assets Array of blocking assets.
 * @return array{status: 'good'|'recommended', description: string} Result.
 */
function perflab_aea_enqueued_blocking_styles( array $blocking_assets ): array {
	$enqueued_styles = count( $blocking_assets['styles'] );
	$bytes_enqueued  = array_reduce(
		$blocking_assets['styles'],
		static function ( $carry, $asset ): int {
			return $carry + ( $asset['size'] ?? 0 );
		},
		0
	);

	$result = array(
		'status'      => 'good',
		'description' => sprintf(
			'<p>%s</p>',
			esc_html(
				sprintf(
					/* translators: 1: Number of blocking styles. 2. Styles size. */
					_n(
						'The amount of %1$s blocking style (size: %2$s) is acceptable.',
						'The amount of %1$s blocking styles (size: %2$s) is acceptable.',
						$enqueued_styles,
						'performance-lab'
					),
					$enqueued_styles,
					size_format( $bytes_enqueued )
				)
			)
		),
	);

	/**
	 * Filters number of enqueued styles to trigger warning.
	 *
	 * @since 1.0.0
	 *
	 * @param int $styles_threshold Styles threshold number. Default 10.
	 */
	$styles_threshold = apply_filters( 'perflab_aea_enqueued_styles_threshold', 10 );

	/**
	 * Filters size of enqueued styles to trigger warning.
	 *
	 * @since 1.0.0
	 *
	 * @param int $styles_size_threshold Enqueued styles size (in bytes) threshold. Default 100000.
	 */
	$styles_size_threshold = apply_filters( 'perflab_aea_enqueued_styles_byte_size_threshold', 100000 );

	if ( $enqueued_styles > $styles_threshold || $bytes_enqueued > $styles_size_threshold ) {
		$result['status'] = 'recommended';

		$result['description'] = sprintf(
			'<p>%s</p>',
			esc_html(
				sprintf(
					/* translators: 1: Number of blocking styles. 2.Styles size. */
					_n(
						'Your website has %1$s blocking style (size: %2$s). Try to reduce the number or to concatenate them.',
						'Your website has %1$s blocking styles (size: %2$s). Try to reduce the number or to concatenate them.',
						$enqueued_styles,
						'performance-lab'
					),
					$enqueued_styles,
					size_format( $bytes_enqueued )
				)
			)
		);
	}

	// If one of the assets had an error, then fail the test even if under the threshold.
	foreach ( $blocking_assets['styles'] as $style ) {
		if ( is_wp_error( $style['error'] ) ) {
			$result['status'] = 'recommended';
			break;
		}
	}

	return $result;
}

/**
 * Handles the failure of retrieving the home page to analyze blocking assets.
 *
 * @since 4.0.0
 *
 * @phpstan-param WP_Error|array{
 *                    headers: WpOrg\Requests\Utility\CaseInsensitiveDictionary,
 *                    body: string,
 *                    response: array{
 *                        code: int|false,
 *                        message: string|false,
 *                    },
 *                } $response
 *
 * @param WP_Error|array<string, mixed> $response The response from the home page retrieval.
 * @return array{status: 'recommended', description: string}|null Result, or null if there was no failure.
 */
function perflab_aea_blocking_assets_retrieval_failure( $response ): ?array {
	$result = array(
		'label'       => __( 'Unable to check site for blocking assets', 'performance-lab' ),
		'status'      => 'recommended',
		'description' => '',
	);

	if ( is_array( $response ) ) {
		$code         = wp_remote_retrieve_response_code( $response );
		$message      = wp_remote_retrieve_response_message( $response );
		$body         = wp_remote_retrieve_body( $response );
		$content_type = wp_remote_retrieve_header( $response, 'content-type' );
		if ( is_array( $content_type ) ) {
			$content_type = array_pop( $content_type );
		}

		// No error.
		if ( 200 === $code && '' !== $body ) {
			return null;
		}

		if ( '' === $body ) {
			$result['description'] .= '<p>' . esc_html__( 'While retrieving the home page to analyze the blocking assets, the request was successfully but response body was empty.', 'performance-lab' ) . '</p>';
		}

		if ( 200 !== $code ) {
			$result['description'] .= '<p>' . wp_kses(
				sprintf(
					/* translators: %d is the HTTP status code, %s is the status header description */
					__( 'While retrieving the home page to analyze the blocking assets, the request returned with an HTTP status of <code>%1$d %2$s</code>.', 'performance-lab' ),
					(int) $code,
					esc_html( $message )
				),
				array( 'code' => array() )
			) . '</p>';
		}

		if ( '' !== $body ) {
			$result['description'] .= '<details>';
			$result['description'] .= '<summary>' . esc_html__( 'Raw response:', 'performance-lab' ) . '</summary>';

			if ( is_string( $content_type ) && str_contains( $content_type, 'html' ) ) {
				$escaped_content        = htmlspecialchars( $body, ENT_QUOTES, 'UTF-8' );
				$result['description'] .= '<iframe srcdoc="' . $escaped_content . '" sandbox width="100%" height="300"></iframe>';
			} else {
				$result['description'] .= '<pre style="white-space: pre-wrap">' . esc_html( $body ) . '</pre>';
			}
			$result['description'] .= '</details>';
		}
	} else {
		$result['description'] = '<p>' . wp_kses(
			sprintf(
				/* translators: %1$s is the error code */
				esc_html__( 'There was an error while retrieving the home page to analyze the blocking assets, with the error code %1$s and the following message:', 'performance-lab' ),
				'<code>' . esc_html( (string) $response->get_error_code() ) . '</code>'
			),
			array( 'code' => array() )
		) . '</p><blockquote>' . esc_html( $response->get_error_message() ) . '</blockquote>';
	}
	return $result;
}

/**
 * Gets the size of the asset in bytes.
 *
 * @since 4.0.0
 *
 * @param string $resource_url URL of the resource.
 * @return int|WP_Error Size of the asset in bytes or WP_Error if the request fails.
 */
function perflab_aea_get_asset_size( string $resource_url ) {
	$response = wp_remote_get(
		$resource_url,
		array(
			'timeout' => 10,
			'headers' => perflab_get_http_basic_authorization_headers(),
		)
	);

	if ( is_wp_error( $response ) ) {
		return $response;
	}

	if ( 200 !== wp_remote_retrieve_response_code( $response ) ) {
		return new WP_Error(
			'http_error',
			wp_kses(
				sprintf(
					/* translators: %d is the HTTP status code, %s is the status header description */
					__( 'Failed to retrieve the above asset with an HTTP status of <code>%1$d %2$s</code>.', 'performance-lab' ),
					(int) wp_remote_retrieve_response_code( $response ),
					esc_html( wp_remote_retrieve_response_message( $response ) )
				),
				array( 'code' => array() )
			)
		);
	}

	// TODO: A non-cacheable response should also be considered an error.
	// TODO: A size of zero could be considered an error too.
	return strlen( wp_remote_retrieve_body( $response ) );
}

/**
 * Gets headers for HTTP Basic authorization headers.
 *
 * @since 4.0.0
 *
 * @return array{ Authorization?: non-empty-string } Headers with copied Basic auth headers.
 */
function perflab_get_http_basic_authorization_headers(): array {
	$headers = array();
	if ( isset( $_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW'] ) ) {
		$user                     = sanitize_text_field( wp_unslash( $_SERVER['PHP_AUTH_USER'] ) );
		$pass                     = sanitize_text_field( wp_unslash( $_SERVER['PHP_AUTH_PW'] ) );
		$headers['Authorization'] = 'Basic ' . base64_encode( $user . ':' . $pass ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode -- base64_encode() is used here to encode the credentials for forwarding basic auth headers.
	}
	return $headers;
}

/**
 * Generates a table of blocking assets.
 *
 * @since 4.0.0
 *
 * @phpstan-param array{
 *                    scripts: array<array{ src: string, size: int|null, error: WP_Error|null }>,
 *                    styles: array<array{ src: string, size: int|null, error: WP_Error|null }>,
 *                } $blocking_assets
 *
 * @param array<string, mixed> $blocking_assets Array of blocking assets.
 * @return string HTML table of blocking assets.
 */
function perflab_aea_generate_blocking_assets_table( array $blocking_assets ): string {
	if ( 0 === count( $blocking_assets['scripts'] ) && 0 === count( $blocking_assets['styles'] ) ) {
		return '';
	}

	$table  = '<table class="wp-list-table widefat striped"><thead><tr>';
	$table .= '<th scope="col">' . esc_html__( 'Type', 'performance-lab' ) . '</th>';
	$table .= '<th scope="col">' . esc_html__( 'Source', 'performance-lab' ) . '</th>';
	$table .= '<th scope="col">' . esc_html__( 'Size', 'performance-lab' ) . '</th>';
	$table .= '<th scope="col">' . esc_html__( 'Status', 'performance-lab' ) . '</th>';
	$table .= '</tr></thead><tbody>';

	$asset_types = array(
		'scripts' => __( 'Script', 'performance-lab' ),
		'styles'  => __( 'Style', 'performance-lab' ),
	);
	foreach ( $asset_types as $type => $label ) {
		if ( isset( $blocking_assets[ $type ] ) && is_array( $blocking_assets[ $type ] ) ) {
			foreach ( $blocking_assets[ $type ] as $asset ) {
				$table .= is_wp_error( $asset['error'] ) ? '<tr style="background-color: #ffecec;">' : '<tr>';
				$table .= '<td>' . esc_html( $label ) . '</td>';
				$table .= '<td>' . esc_url( $asset['src'] );
				if ( is_wp_error( $asset['error'] ) ) {
					$table .= '<p>' . wp_kses( $asset['error']->get_error_message(), array( 'code' => array() ) ) . '</p>';
				}
				$table .= '</td>';
				$table .= '<td>';
				if ( is_int( $asset['size'] ) ) {
					$table .= str_replace( ' ', '&nbsp;', (string) size_format( $asset['size'] ) );
				} else {
					$table .= esc_html__( 'N/A', 'performance-lab' );
				}
				$table .= '</td>';
				$table .= '<td>';
				if ( is_wp_error( $asset['error'] ) ) {
					$table .= esc_html__( 'Error', 'performance-lab' );
				} else {
					$table .= esc_html__( 'OK', 'performance-lab' );
				}
				$table .= '</td>';
				$table .= '</tr>';
			}
		}
	}

	$table .= '</tbody></table>';

	return $table;
}
