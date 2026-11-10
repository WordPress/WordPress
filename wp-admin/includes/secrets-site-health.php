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
 * There is no settings screen for secrets, so these tests are how an operator
 * notices a broken key, a weak key source, or a pending rotation.
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
			'color' => $colors[ $status ] ?? 'blue',
		),
		'description' => $description,
	);
}

/**
 * Whether the active provider protects secrets with the keyring.
 *
 * A secrets.php drop-in can replace the whole provider, which then protects secrets
 * with its own keys. The default keyring, and WP_SECRETS_KEY, are not used at all.
 *
 * @since 7.2.0
 *
 * @param WP_Secrets_Provider $provider The active provider.
 * @return bool
 */
function wp_secrets_site_health_uses_keyring( WP_Secrets_Provider $provider ): bool {
	return $provider instanceof WP_Secrets_Libsodium_Provider || $provider instanceof WP_Secrets_Broken_Provider;
}

/**
 * Tests whether the site key is defined, and in the recommended form.
 *
 * @since 7.2.0
 *
 * @return array
 *
 * @phpstan-return array{test: string, label: string, status: 'good'|'recommended'|'critical', badge: array{label: string, color: string}, description: string}
 */
function wp_secrets_site_health_test_key_source(): array {
	$provider = _wp_secrets_get_provider();

	if ( ! wp_secrets_site_health_uses_keyring( $provider ) ) {
		return wp_secrets_site_health_result(
			'secrets_api_key_source',
			__( 'Secrets API keys are managed by the provider' ),
			'good',
			sprintf(
				'<p>%s</p>',
				sprintf(
					/* translators: 1: secrets.php, 2: Name of the active secrets provider, 3: WP_SECRETS_KEY */
					__( 'Secrets are protected by the provider of the %1$s drop-in: %2$s. %3$s is not used.' ),
					'<code>secrets.php</code>',
					esc_html( $provider->get_label() ),
					'<code>WP_SECRETS_KEY</code>'
				)
			)
		);
	}

	$keyring    = _wp_secrets_get_key_manager()->get_keyring();
	$key_source = $keyring->get_key_source();
	$key_status = $keyring instanceof WP_Secrets_Config_Key_Provider ? $keyring->get_key_status() : null;

	// The broken keyring's description is translated, so check its class rather than its text.
	if ( $keyring instanceof WP_Secrets_Broken_Keyring ) {
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

	/*
	 * Once a root key exists, it is wrapped with the current site key, and core has no way
	 * to re-wrap it under another one. Changing the key source then locks every stored
	 * secret, and blocks saving new ones, until the change is reverted.
	 */
	$has_root_key = false !== get_site_option( WP_Secrets_Key_Manager::ROOT_KEY_OPTION );

	if ( $has_root_key ) {
		$root_key = _wp_secrets_get_key_manager()->get_root_key();

		if ( is_wp_error( $root_key ) ) {
			/*
			 * Only the default keyring reads wp-config.php. A keyring from a secrets.php
			 * drop-in fails for its own reasons, such as an unreachable key service, and
			 * its error is the useful part.
			 */
			$cause = $keyring instanceof WP_Secrets_Config_Key_Provider
				? sprintf(
					/* translators: %s: WP_SECRETS_KEY */
					__( 'This happens when %s was changed or removed after secrets were stored. Restoring the previous value fixes it.' ),
					'<code>WP_SECRETS_KEY</code>'
				)
				: sprintf(
					/* translators: 1: secrets.php, 2: Error message from the keyring. */
					__( 'The keyring of the %1$s drop-in reported: %2$s' ),
					'<code>secrets.php</code>',
					esc_html( $root_key->get_error_message() )
				);

			return wp_secrets_site_health_result(
				'secrets_api_key_source',
				__( 'Secrets API cannot unlock the stored secrets' ),
				'critical',
				sprintf(
					'<p>%s</p><p>%s</p>',
					esc_html__( 'The configured key cannot decrypt the root key that protects the stored secrets, so no secret can be read or saved.' ),
					$cause
				)
			);
		}

		wp_secrets_memzero( $root_key );
	}

	if ( WP_Secrets_Config_Key_Provider::KEY_STATUS_LEGACY === $key_status ) {
		return wp_secrets_site_health_result(
			'secrets_api_key_source',
			__( 'Secrets API key is not in the recommended form' ),
			'recommended',
			sprintf(
				'<p>%s</p>',
				$has_root_key
					? sprintf(
						/* translators: %s: WP_SECRETS_KEY */
						__( '%s is defined, but not as a base64-encoded random 32-byte value. Keep its current value: secrets are already encrypted with it, and changing it makes them unreadable.' ),
						'<code>WP_SECRETS_KEY</code>'
					)
					: sprintf(
						/* translators: %s: WP_SECRETS_KEY */
						__( '%s is defined, but not as a base64-encoded random 32-byte value. No secrets are stored yet, so it can still be replaced with such a value.' ),
						'<code>WP_SECRETS_KEY</code>'
					)
			)
		);
	}

	if ( WP_Secrets_Config_Key_Provider::KEY_STATUS_UNDEFINED === $key_status || WP_Secrets_Config_Key_Provider::KEY_STATUS_UNUSABLE === $key_status ) {
		$is_defined = WP_Secrets_Config_Key_Provider::KEY_STATUS_UNUSABLE === $key_status;
		$example    = sprintf( "define( 'WP_SECRETS_KEY', '%s' );", base64_encode( random_bytes( 32 ) ) );

		return wp_secrets_site_health_result(
			'secrets_api_key_source',
			__( 'Secrets API has no encryption key' ),
			'recommended',
			sprintf(
				'<p>%s</p><p>%s</p><p><code>%s</code></p><p>%s</p>',
				$is_defined
					? sprintf(
						/* translators: 1: WP_SECRETS_KEY, 2: wp-config.php, 3: wp-config-sample.php */
						__( '%1$s is defined in %2$s, but its value cannot be used: it is empty, still the placeholder from %3$s, or the same as one of the authentication keys and salts. Plugins cannot store API keys and other credentials with the Secrets API until it is fixed.' ),
						'<code>WP_SECRETS_KEY</code>',
						'<code>wp-config.php</code>',
						'<code>wp-config-sample.php</code>'
					)
					: sprintf(
						/* translators: 1: WP_SECRETS_KEY, 2: wp-config.php */
						__( '%1$s is not defined in %2$s. Plugins cannot store API keys and other credentials with the Secrets API until it is.' ),
						'<code>WP_SECRETS_KEY</code>',
						'<code>wp-config.php</code>'
					),
				$is_defined
					? sprintf(
						/* translators: 1: WP_SECRETS_KEY, 2: wp-config.php */
						__( 'Replace the %1$s line in %2$s with this one:' ),
						'<code>WP_SECRETS_KEY</code>',
						'<code>wp-config.php</code>'
					)
					: sprintf(
						/* translators: 1: wp-config.php, 2: Translated version of "That's all, stop editing! Happy publishing." */
						__( 'Add this line to %1$s, above the line reading %2$s:' ),
						'<code>wp-config.php</code>',
						'<code>/* ' . __( 'That&#8217;s all, stop editing! Happy publishing.' ) . ' */</code>'
					),
				esc_html( $example ),
				esc_html__( 'A new value is generated each time this page loads. Keep a copy of the one you use with your backups: secrets stored with it cannot be read without it.' )
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
 * Tests whether every stored secret still decrypts.
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

	if ( ! is_wp_error( $broken ) && is_multisite() && is_super_admin() ) {
		$network_broken = wp_secrets_site_health_find_undecryptable( true );
		$broken         = is_wp_error( $network_broken ) ? $network_broken : array_merge( $broken, $network_broken );
	}

	// Secrets that cannot be listed cannot be checked, which must not read as all of them decrypting.
	if ( is_wp_error( $broken ) ) {
		return wp_secrets_site_health_result(
			'secrets_api_undecryptable',
			__( 'Secrets could not be checked' ),
			'critical',
			sprintf(
				'<p>%s</p><p>%s</p>',
				esc_html__( 'The stored secrets could not be listed, so none of them could be checked. Secrets probably cannot be read or written either until this is fixed.' ),
				esc_html( $broken->get_error_message() )
			)
		);
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
			esc_html__( 'The following secrets exist but could not be decrypted with the current key.' ),
			$items,
			sprintf(
				/* translators: %s: WP_SECRETS_KEY */
				__( 'This can happen after %s was changed or removed, after restoring a database backup without its matching key, or after a failed key rotation. Restoring the previous key makes the secrets readable again. Otherwise, each credential must be entered again.' ),
				'<code>WP_SECRETS_KEY</code>'
			)
		)
	);
}

/**
 * Finds secrets that fail to decrypt, for one scope.
 *
 * @since 7.2.0
 *
 * @param bool $network Whether to check network-scope secrets.
 * @return array|WP_Error List of array( 'name' => ..., 'fingerprint' => ... ),
 *                        or WP_Error when the secrets cannot be listed.
 *
 * @phpstan-return list<array{name: string, fingerprint: string}>|WP_Error
 */
function wp_secrets_site_health_find_undecryptable( bool $network ) {
	$entries = $network ? wp_list_network_secrets() : wp_list_secrets();

	if ( is_wp_error( $entries ) ) {
		return $entries;
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
 * Tests whether any secrets are flagged as needing rotation.
 *
 * @since 7.2.0
 *
 * @return array
 *
 * @phpstan-return array{test: string, label: string, status: 'good'|'recommended'|'critical', badge: array{label: string, color: string}, description: string}
 */
function wp_secrets_site_health_test_needs_rotation(): array {
	$count = wp_secrets_site_health_count_needing_rotation( false );

	if ( ! is_wp_error( $count ) && is_multisite() && is_super_admin() ) {
		$network_count = wp_secrets_site_health_count_needing_rotation( true );
		$count         = is_wp_error( $network_count ) ? $network_count : $count + $network_count;
	}

	if ( is_wp_error( $count ) ) {
		return wp_secrets_site_health_result(
			'secrets_api_needs_rotation',
			__( 'Secrets could not be checked for pending rotation' ),
			'recommended',
			sprintf( '<p>%s</p>', esc_html( $count->get_error_message() ) )
		);
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
 * @return int|WP_Error The number of secrets, or WP_Error when the secrets cannot be listed.
 *
 * @phpstan-return int<0, max>|WP_Error
 */
function wp_secrets_site_health_count_needing_rotation( bool $network ) {
	$entries = $network ? wp_list_network_secrets() : wp_list_secrets();

	if ( is_wp_error( $entries ) ) {
		return $entries;
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
	$key_manager  = _wp_secrets_get_key_manager();
	$provider     = _wp_secrets_get_provider();
	$uses_keyring = wp_secrets_site_health_uses_keyring( $provider );

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
			'value' => $uses_keyring ? get_class( $key_manager->get_keyring() ) : __( 'Not used by the provider' ),
		),
		'key_source'     => array(
			'label' => __( 'Key source' ),
			'value' => $uses_keyring ? $key_manager->get_keyring()->get_key_source() : __( 'Not used by the provider' ),
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
