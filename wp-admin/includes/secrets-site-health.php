<?php
/**
 * Secrets API: Site Health integration
 *
 * @package WordPress
 * @subpackage Secrets
 * @since 7.2.0
 */

/**
 * Registers the Secrets API's Site Health tests.
 *
 * No settings screen – the proposal defers that to 7.3 – but the health signal an
 * operator needs to notice a broken key, a weak key source, or a pending rotation is
 * in scope now.
 *
 * @since 7.2.0
 *
 * @param array<string, array<string, array<string, mixed>>> $tests Existing Site Health tests.
 * @return array<string, array<string, array<string, mixed>>>
 */
function wp_secrets_site_health_tests( $tests ) {
	$tests['direct']['secrets_api_key_source'] = array(
		'label' => __( 'Secrets API key source' ),
		'test'  => 'wp_secrets_site_health_test_key_source',
	);

	$tests['direct']['secrets_api_undecryptable'] = array(
		'label' => __( 'Secrets API: undecryptable secrets' ),
		'test'  => 'wp_secrets_site_health_test_undecryptable',
	);

	$tests['direct']['secrets_api_needs_rotation'] = array(
		'label' => __( 'Secrets API: credentials pending rotation' ),
		'test'  => 'wp_secrets_site_health_test_needs_rotation',
	);

	return $tests;
}
add_filter( 'site_status_tests', 'wp_secrets_site_health_tests' );

/**
 * Builds the standard shape a Site Health test callback returns.
 *
 * @since 7.2.0
 *
 * @param string $test        The test's own identifier, matching the key registered
 *                            in wp_secrets_site_health_tests().
 * @param string $label       Short label for the test result.
 * @param string $status      One of 'good', 'recommended', 'critical'.
 * @param string $description HTML description, normally one or more <p> elements.
 * @return array
 *
 * @phpstan-return array{test: string, label: string, status: 'good'|'recommended'|'critical', badge: array{label: string, color: string}, description: string}
 * @phpstan-param 'good'|'recommended'|'critical' $status
 */
function wp_secrets_site_health_result( string $test, string $label, string $status, string $description ): array {
	$colors = array(
		'good'        => 'green',
		'recommended' => 'orange',
		'critical'    => 'red',
	);

	return array(
		'test'        => $test,
		'label'       => $label,
		'status'      => $status,
		'badge'       => array(
			'label' => __( 'Security' ),
			'color' => isset( $colors[ $status ] ) ? $colors[ $status ] : 'blue',
		),
		'description' => $description,
	);
}

/**
 * Site Health test: is the site key a dedicated constant, or a weaker fallback?
 *
 * @since 7.2.0
 *
 * @return array
 *
 * @phpstan-return array{test: string, label: string, status: 'good'|'recommended'|'critical', badge: array{label: string, color: string}, description: string}
 */
function wp_secrets_site_health_test_key_source(): array {
	$key_source = _wp_secrets_get_key_manager()->get_keyring()->get_key_source();

	if ( false !== strpos( $key_source, 'broken' ) ) {
		return wp_secrets_site_health_result(
			'secrets_api_key_source',
			__( 'Secrets API key source is broken' ),
			'critical',
			sprintf(
				'<p>%s</p>',
				esc_html__( 'The active keyring did not load correctly. No secret can be encrypted or decrypted until this is fixed.' )
			)
		);
	}

	if ( false !== strpos( $key_source, 'legacy interpretation' ) ) {
		return wp_secrets_site_health_result(
			'secrets_api_key_source',
			__( 'Secrets API key should be regenerated' ),
			'recommended',
			sprintf(
				'<p>%s</p>',
				esc_html__( 'WP_SECRETS_KEY is defined, but not in the recommended base64-encoded 32-byte form. Regenerate it with `wp secret generate-key`.' )
			)
		);
	}

	if ( false !== strpos( $key_source, 'LOGGED_IN_KEY' ) ) {
		return wp_secrets_site_health_result(
			'secrets_api_key_source',
			__( 'Secrets API is using a fallback key' ),
			'recommended',
			sprintf(
				'<p>%s</p>',
				esc_html__( 'No dedicated WP_SECRETS_KEY constant is defined. Secrets are encrypted using a key derived from existing salts, which works but is not the recommended configuration. Define WP_SECRETS_KEY with a value from `wp secret generate-key`.' )
			)
		);
	}

	return wp_secrets_site_health_result(
		'secrets_api_key_source',
		__( 'Secrets API is using a dedicated key' ),
		'good',
		sprintf( '<p>%s</p>', esc_html( $key_source ) )
	);
}

/**
 * Site Health test: does every stored secret still decrypt?
 *
 * Network secrets are included only for a super admin on a multisite install –
 * never shown to a site administrator who is not one.
 *
 * @since 7.2.0
 *
 * @return array
 *
 * @phpstan-return array{test: string, label: string, status: 'good'|'recommended'|'critical', badge: array{label: string, color: string}, description: string}
 */
function wp_secrets_site_health_test_undecryptable(): array {
	$broken = wp_secrets_site_health_find_undecryptable( false );

	if ( is_multisite() && is_super_admin() ) {
		$broken = array_merge( $broken, wp_secrets_site_health_find_undecryptable( true ) );
	}

	if ( empty( $broken ) ) {
		return wp_secrets_site_health_result(
			'secrets_api_undecryptable',
			__( 'All secrets can be decrypted' ),
			'good',
			sprintf( '<p>%s</p>', esc_html__( 'Every stored secret decrypted successfully.' ) )
		);
	}

	$items = '';

	foreach ( $broken as $entry ) {
		$items .= sprintf(
			'<li>%s (%s)</li>',
			esc_html( $entry['name'] ),
			sprintf(
				/* translators: %s: A secret's last known fingerprint. */
				esc_html__( 'fingerprint: %s' ),
				esc_html( $entry['fingerprint'] )
			)
		);
	}

	return wp_secrets_site_health_result(
		'secrets_api_undecryptable',
		__( 'Some secrets cannot be decrypted' ),
		'critical',
		sprintf(
			'<p>%s</p><ul>%s</ul><p>%s</p>',
			esc_html__( 'The following secrets exist but could not be decrypted with the current key. There is no way to recover the original value. The credential must be re-entered.' ),
			$items,
			esc_html__( 'This can happen after losing WP_SECRETS_KEY, restoring a database backup without its matching key, or a failed key rotation.' )
		)
	);
}

/**
 * Finds secrets that fail to decrypt, for one scope.
 *
 * @since 7.2.0
 *
 * @param bool $network Whether to check network-scope secrets.
 * @return array List of array( 'name' => ..., 'fingerprint' => ... ).
 *
 * @phpstan-return list<array{name: string, fingerprint: string}>
 */
function wp_secrets_site_health_find_undecryptable( bool $network ): array {
	$entries = $network ? wp_list_network_secrets() : wp_list_secrets();

	if ( is_wp_error( $entries ) ) {
		return array();
	}

	$broken = array();

	foreach ( $entries as $entry ) {
		$secret = $network ? wp_get_network_secret( $entry['name'] ) : wp_get_secret( $entry['name'] );

		if ( is_wp_error( $secret ) ) {
			$broken[] = array(
				'name'        => $entry['name'],
				'fingerprint' => $entry['fingerprint'],
			);
		}
	}

	return $broken;
}

/**
 * Site Health test: are any secrets flagged as needing rotation?
 *
 * @since 7.2.0
 *
 * @return array
 *
 * @phpstan-return array{test: string, label: string, status: 'good'|'recommended'|'critical', badge: array{label: string, color: string}, description: string}
 */
function wp_secrets_site_health_test_needs_rotation(): array {
	$count = wp_secrets_site_health_count_needing_rotation( false );

	if ( is_multisite() && is_super_admin() ) {
		$count += wp_secrets_site_health_count_needing_rotation( true );
	}

	if ( 0 === $count ) {
		return wp_secrets_site_health_result(
			'secrets_api_needs_rotation',
			__( 'No secrets are pending rotation' ),
			'good',
			sprintf( '<p>%s</p>', esc_html__( 'No secret is flagged as needing rotation.' ) )
		);
	}

	return wp_secrets_site_health_result(
		'secrets_api_needs_rotation',
		__( 'Some secrets are pending rotation' ),
		'recommended',
		sprintf(
			'<p>%s</p>',
			esc_html(
				sprintf(
					/* translators: %d: Number of secrets flagged for rotation. */
					_n(
						'%d secret was imported from an existing option and is flagged for rotation. A credential that sat in a plain option has already been through whatever backups and replication paths that option went through; re-entering it with a new value is recommended.',
						'%d secrets were imported from existing options and are flagged for rotation. A credential that sat in a plain option has already been through whatever backups and replication paths that option went through; re-entering each with a new value is recommended.',
						$count
					),
					$count
				)
			)
		)
	);
}

/**
 * Counts secrets flagged needs_rotation, for one scope.
 *
 * @since 7.2.0
 *
 * @param bool $network Whether to check network-scope secrets.
 * @return int
 *
 * @phpstan-return int<0, max>
 */
function wp_secrets_site_health_count_needing_rotation( bool $network ): int {
	$entries = $network ? wp_list_network_secrets() : wp_list_secrets();

	if ( is_wp_error( $entries ) ) {
		return 0;
	}

	$count = 0;

	foreach ( $entries as $entry ) {
		if ( ! empty( $entry['needs_rotation'] ) ) {
			++$count;
		}
	}

	return $count;
}

/**
 * Adds a Secrets API section to Site Health's debug information.
 *
 * Counts and class names only – no secret values, and no fingerprints. Network
 * scope figures are included only for a super admin on a multisite install.
 *
 * @since 7.2.0
 *
 * @param array<string, array<string, mixed>> $info Existing debug information sections.
 * @return array<string, array<string, mixed>>
 */
function wp_secrets_site_health_debug_info( $info ) {
	$key_manager = _wp_secrets_get_key_manager();
	$provider    = _wp_secrets_get_provider();

	$fields = array(
		'dropin_active'  => array(
			'label' => __( 'Drop-in active' ),
			'value' => wp_using_secrets_dropin() ? __( 'Yes' ) : __( 'No' ),
		),
		'provider_class' => array(
			'label' => __( 'Provider class' ),
			'value' => get_class( $provider ),
		),
		'protected_by'   => array(
			'label' => __( 'Secrets protected by' ),
			'value' => $provider->get_label(),
		),
		'protection_at'  => array(
			'label' => __( 'Encryption boundary' ),
			'value' => WP_Secrets_Provider::BOUNDARY_WORDPRESS === $provider->get_protection_boundary()
				? __( 'WordPress' )
				: __( 'The provider (outside WordPress)' ),
		),
		'writable'       => array(
			'label' => __( 'Accepts writes' ),
			'value' => $provider->is_writable() ? __( 'Yes' ) : __( 'No' ),
		),
		'keyring_class'  => array(
			'label' => __( 'Keyring class' ),
			'value' => get_class( $key_manager->get_keyring() ),
		),
		'key_source'     => array(
			'label' => __( 'Key source' ),
			'value' => $key_manager->get_keyring()->get_key_source(),
		),
		'record_version' => array(
			'label' => __( 'Record format version' ),
			'value' => (string) WP_SECRETS_RECORD_VERSION,
		),
	);

	$site_secrets                = wp_list_secrets();
	$fields['site_secret_count'] = array(
		'label' => __( 'Site secrets' ),
		'value' => is_wp_error( $site_secrets ) ? __( 'Unavailable' ) : (string) count( $site_secrets ),
	);

	if ( is_multisite() && is_super_admin() ) {
		$network_secrets                = wp_list_network_secrets();
		$fields['network_secret_count'] = array(
			'label' => __( 'Network secrets' ),
			'value' => is_wp_error( $network_secrets ) ? __( 'Unavailable' ) : (string) count( $network_secrets ),
		);
	}

	$info['secrets-api'] = array(
		'label'  => __( 'Secrets API' ),
		'fields' => $fields,
	);

	return $info;
}
add_filter( 'debug_information', 'wp_secrets_site_health_debug_info' );
