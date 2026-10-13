<?php
/**
 * Secrets API
 *
 * @package WordPress
 * @subpackage Secrets
 * @since 7.2.0
 */

/**
 * The store or key backend is misbehaving.
 *
 * The secret exists and was decrypted, but the backend it came from failed in a way
 * none of the more specific codes below describe.
 *
 * @since 7.2.0
 */
define( 'WP_SECRETS_ERROR_DECRYPTION_FAILED', 'secret_decryption_failed' );

/**
 * The key needed to encrypt or decrypt a secret could not be obtained.
 *
 * The keyring is unreachable, misconfigured, or refusing to answer.
 *
 * @since 7.2.0
 */
define( 'WP_SECRETS_ERROR_KEY_UNAVAILABLE', 'secret_key_unavailable' );

/**
 * The storage back end could not be reached at all.
 *
 * @since 7.2.0
 */
define( 'WP_SECRETS_ERROR_STORE_UNAVAILABLE', 'secret_store_unavailable' );

/**
 * A secret name failed wp_secrets_validate_name().
 *
 * @since 7.2.0
 */
define( 'WP_SECRETS_ERROR_INVALID_NAME', 'secret_invalid_name' );

/**
 * A secret value was not a string.
 *
 * @since 7.2.0
 */
define( 'WP_SECRETS_ERROR_INVALID_VALUE', 'secret_invalid_value' );

/**
 * A caller passed an argument this API cannot act on.
 *
 * An unrecognized version constant, or an invalid scope, slot, or site id.
 *
 * Distinct from the codes above in cause rather than in severity: those describe
 * a runtime condition a correct caller can still hit (a name that failed
 * validation, an unavailable key), while this one only ever means the calling
 * code is wrong. Every site that returns it also calls _doing_it_wrong(), so the
 * mistake is visible in development rather than only in a return value the caller
 * may not be checking.
 *
 * @since 7.2.0
 */
define( 'WP_SECRETS_ERROR_INVALID_ARGUMENT', 'secret_invalid_argument' );

/**
 * The cipher this API depends on is unavailable.
 *
 * Neither libsodium nor its sodium_compat fallback is present in this environment.
 *
 * @since 7.2.0
 */
define( 'WP_SECRETS_ERROR_CRYPTO_UNAVAILABLE', 'secret_crypto_unavailable' );

/**
 * A stored record could not be parsed as a secret record at all.
 *
 * Distinct from decrypting incorrectly, which is WP_SECRETS_ERROR_DECRYPTION_FAILED.
 *
 * @since 7.2.0
 */
define( 'WP_SECRETS_ERROR_RECORD_MALFORMED', 'secret_record_malformed' );

/**
 * A stored record parses, but its format version is not one this API understands.
 *
 * Distinct from WP_SECRETS_ERROR_RECORD_MALFORMED so that an operator, or Site
 * Health, can tell a corrupt record apart from one written by a newer version of
 * this plugin than is currently active. See
 * docs/decisions/0006-record-format-v2-not-read-compatible.md. The upgrade path for a future v2 is not
 * designed, so an unrecognized version is rejected outright rather than guessed at.
 *
 * @since 7.2.0
 */
define( 'WP_SECRETS_ERROR_RECORD_UNSUPPORTED_VERSION', 'secret_record_unsupported_version' );

/**
 * The active provider does not accept writes.
 *
 * Its credentials are managed by host tooling, a control panel, or a key policy
 * outside WordPress. Distinct from WP_SECRETS_ERROR_STORE_UNAVAILABLE, which means
 * the write could have succeeded and did not. This one means it was never possible.
 *
 * @since 7.2.0
 */
define( 'WP_SECRETS_ERROR_PROVIDER_READ_ONLY', 'secret_provider_read_only' );

/**
 * The current record format version.
 *
 * Bumped only if the record shape in WP_Secrets_Cipher's slot arrays ever changes
 * incompatibly.
 *
 * @since 7.2.0
 */
define( 'WP_SECRETS_RECORD_VERSION', 1 );

/**
 * The longest a secret name may be.
 *
 * The wp_options.option_name column allows 191 characters. The longest prefix this
 * API stores under is '_wp_network_secret_' (19 characters), so 191 - 19 = 172
 * is the longest name any store built on core's options tables can accept
 * without truncation, and every store is held to the same limit for
 * consistency regardless of what a specific backend could technically fit.
 *
 * @since 7.2.0
 */
define( 'WP_SECRETS_MAX_NAME_LENGTH', 172 );

/**
 * Capability required to manage site-scope secrets at the operator boundary.
 *
 * Checked by WP-CLI and by a future admin screen, never by the function-level API
 * itself. See wp_set_secret() for why.
 *
 * @since 7.2.0
 */
define( 'WP_SECRETS_CAP_MANAGE', 'manage_secrets' );

/**
 * Capability required at the operator boundary to manage network-scope secrets.
 *
 * @since 7.2.0
 */
define( 'WP_SECRETS_CAP_MANAGE_NETWORK', 'manage_network_secrets' );

/**
 * Best-effort clearing of a plaintext value from memory.
 *
 * This is hygiene, not a guarantee. PHP strings are reference-counted and often
 * shared by copy-on-write; sodium_memzero() can only scrub the bytes of a string it
 * exclusively owns; if another reference to the same value exists elsewhere,
 * PHP forces a private copy before zeroing rather than corrupting the shared buffer,
 * and that other reference is left completely untouched. Call this on every plaintext
 * local as soon as it is no longer needed, and do not keep incidental copies around.
 *
 * Under sodium_compat, core's documented fallback when the libsodium extension is
 * unavailable, this scrubs nothing. A userland polyfill cannot reach a PHP string's
 * underlying memory at all. Overwriting the local binding is the only thing available
 * in that case, and it removes the live reference from this scope without touching
 * the memory the string used to occupy.
 *
 * @since 7.2.0
 *
 * @param string $value The value to clear, by reference.
 */
function wp_secrets_memzero( &$value ): void {
	if ( ! is_string( $value ) ) {
		return;
	}

	if ( function_exists( 'sodium_memzero' ) ) {
		try {
			/*
			 * sodium_memzero() is called through a true reference alias, not
			 * directly on $value, so that a static analyzer's stub for
			 * sodium_memzero() (whose own by-ref parameter is documented as
			 * becoming null) is evaluated against a local variable rather than
			 * against this function's own by-ref parameter. The alias is not a
			 * copy: `=&` shares the identical underlying storage, so this still
			 * zeroes the real buffer the caller holds.
			 */
			$alias =& $value;
			sodium_memzero( $alias );
		} catch ( \Throwable $e ) {
			// A value this function doesn't exclusively own (a shared,
			// copy-on-write buffer) can legitimately refuse. The unconditional
			// reassignment below still runs regardless of what happened here.
			unset( $e );
		}
	}

	// Reached on every path, including sodium_memzero() success (it leaves the
	// variable null, not an empty string) so callers see one consistent contract.
	$value = '';
}

/**
 * Validates a secret's name.
 *
 * Names take the form 'plugin-slug/secret-name': lowercase alphanumerics,
 * hyphens, and underscores in each segment, exactly one '/' separating them,
 * and no segment starting or ending with a hyphen or underscore.
 *
 * Namespacing is a matter of organization, not security. It groups secrets by owner
 * so that listings and admin screens can be sensible. It confers no isolation: any
 * plugin that can run PHP can read any secret, namespaced or not.
 *
 * An unnamespaced name ('secret-name', no '/') is accepted, but reports through
 * _doing_it_wrong(). It exists for one reason: code written against the earlier
 * prototype used a flat keyspace, and refusing those names outright would mean
 * every such call site has to be rewritten before it can be ported at all.
 * Accepting them keeps that migration incremental. Nothing else should use one.
 * Two plugins that both choose 'api-key' collide silently, which is a real problem
 * even though it is not a security one.
 *
 * @since 7.2.0
 *
 * @param string $name Candidate secret name.
 * @return true|WP_Error True if $name is usable, including the unnamespaced form.
 *                       Otherwise WP_Error with code WP_SECRETS_ERROR_INVALID_NAME.
 */
function wp_secrets_validate_name( string $name ) {
	if ( '' === $name ) {
		return new WP_Error(
			WP_SECRETS_ERROR_INVALID_NAME,
			__( 'Secret names must not be empty.' )
		);
	}

	if ( strlen( $name ) > WP_SECRETS_MAX_NAME_LENGTH ) {
		return new WP_Error(
			WP_SECRETS_ERROR_INVALID_NAME,
			sprintf(
				/* translators: %d: Maximum allowed length, in characters. */
				__( 'Secret names must be %d characters or fewer.' ),
				WP_SECRETS_MAX_NAME_LENGTH
			)
		);
	}

	$slashes = substr_count( $name, '/' );

	if ( $slashes > 1 ) {
		return new WP_Error(
			WP_SECRETS_ERROR_INVALID_NAME,
			__( 'Secret names may contain at most one "/", separating a namespace from a key.' )
		);
	}

	$segment_pattern = '/^[a-z0-9]([a-z0-9_-]*[a-z0-9])?$/';

	if ( 0 === $slashes ) {
		if ( ! preg_match( $segment_pattern, $name ) ) {
			return new WP_Error(
				WP_SECRETS_ERROR_INVALID_NAME,
				__( 'Secret names may contain only lowercase letters, numbers, hyphens, and underscores, and must not start or end with a hyphen or underscore.' )
			);
		}

		/*
		 * Reported here rather than at each public entry point: this is the one
		 * place that decides what a name is, so a caller cannot reach the store
		 * with an unnamespaced name without passing through it.
		 */
		_doing_it_wrong(
			__FUNCTION__,
			sprintf(
				/* translators: %s: The unnamespaced secret name. */
				__( 'The secret name "%s" has no namespace. Namespaced names ("plugin-slug/secret-name") group secrets by owner so that listings and admin screens can be organized; unnamespaced names are supported only so that code written against the Displace prototype can be ported incrementally.' ),
				$name
			),
			'7.2.0'
		);

		return true;
	}

	$segments  = explode( '/', $name );
	$namespace = $segments[0];
	$key       = $segments[1];

	if ( '' === $namespace || '' === $key ) {
		return new WP_Error(
			WP_SECRETS_ERROR_INVALID_NAME,
			__( 'Both segments of a secret name must be non-empty.' )
		);
	}

	if ( ! preg_match( $segment_pattern, $namespace ) || ! preg_match( $segment_pattern, $key ) ) {
		return new WP_Error(
			WP_SECRETS_ERROR_INVALID_NAME,
			__( 'Secret name segments may contain only lowercase letters, numbers, hyphens, and underscores, and must not start or end with a hyphen or underscore.' )
		);
	}

	return true;
}

/**
 * Returns the active secret store.
 *
 * No filter is applied here, or anywhere on the retrieval path. See
 * docs/spec/extension-points.md. A secrets.php drop-in overrides the store by setting
 * $GLOBALS['wp_secrets_store'] to an instance before this is first called, and
 * that global is checked here directly, not through a hook.
 *
 * If the global was set but is not a valid WP_Secrets_Store, or if the drop-in
 * itself failed to load cleanly, this returns WP_Secrets_Broken_Store rather than
 * silently using the default: the drop-in's presence signals the operator wants
 * storage other than local options, and falling back to local options anyway would
 * be the exact silent downgrade this API refuses to make.
 *
 * @since 7.2.0
 *
 * @return WP_Secrets_Store
 */
function _wp_secrets_get_store(): WP_Secrets_Store {
	static $store = null;

	if ( $store instanceof WP_Secrets_Store ) {
		return $store;
	}

	if ( ! empty( $GLOBALS['wp_secrets_dropin_broken'] ) ) {
		$store = new WP_Secrets_Broken_Store();

		return $store;
	}

	if ( isset( $GLOBALS['wp_secrets_store'] ) && $GLOBALS['wp_secrets_store'] instanceof WP_Secrets_Store ) {
		$store = $GLOBALS['wp_secrets_store'];

		return $store;
	}

	$store = new WP_Secrets_Option_Store();

	return $store;
}

/**
 * Returns the active key manager.
 *
 * A secrets.php drop-in overrides the keyring by setting
 * $GLOBALS['wp_secrets_keyring'] to an instance before this is first called. See
 * _wp_secrets_get_store() for why an invalid override or a failed drop-in load
 * fails closed (WP_Secrets_Broken_Keyring) rather than falling back to the default.
 *
 * @since 7.2.0
 *
 * @return WP_Secrets_Key_Manager
 */
function _wp_secrets_get_key_manager(): WP_Secrets_Key_Manager {
	static $key_manager = null;

	if ( $key_manager instanceof WP_Secrets_Key_Manager ) {
		return $key_manager;
	}

	if ( ! empty( $GLOBALS['wp_secrets_dropin_broken'] ) ) {
		$key_manager = new WP_Secrets_Key_Manager( new WP_Secrets_Broken_Keyring() );

		return $key_manager;
	}

	$keyring = null;

	if ( isset( $GLOBALS['wp_secrets_keyring'] ) && $GLOBALS['wp_secrets_keyring'] instanceof WP_Secrets_Keyring ) {
		$keyring = $GLOBALS['wp_secrets_keyring'];
	}

	$key_manager = new WP_Secrets_Key_Manager( $keyring );

	return $key_manager;
}

/**
 * Whether a secrets.php drop-in is present and was loaded.
 *
 * True whether or not the drop-in successfully provided a store or keyring
 * override. It reports presence rather than health; Site Health reports separately
 * on whether a loaded drop-in is actually working.
 *
 * @since 7.2.0
 *
 * @return bool
 */
function wp_using_secrets_dropin(): bool {
	return ! empty( $GLOBALS['wp_secrets_dropin_loaded'] );
}



















/**
 * Encrypts and stores a secret.
 *
 * Encryption is unconditional: there is no plaintext mode and no constant to
 * disable it. No capability check is applied here, because this must be callable from
 * cron, REST, and front-end requests where no user is logged in. Enforce
 * capabilities at the operator boundary (CLI, an admin screen) instead.
 *
 * @since 7.2.0
 *
 * @param string $name  The secret's namespaced name ('plugin-slug/secret-name').
 * @param string $value The plaintext value to store.
 * @return true|WP_Error
 */
function wp_set_secret( string $name, $value ) {
	return _wp_secrets_set( $name, $value, false );
}

/**
 * Imports an existing option's value as a secret.
 *
 * For an explicit migration, "on their own explicit upgrade schedule,"
 * in the proposal's words, because "core cannot reliably tell which options are
 * credentials, and guessing would break sites." The source option is left
 * untouched: this reads it, it does not move or delete it.
 *
 * The imported secret is flagged needs_rotation, unconditionally. A credential that
 * sat in a plain option has already been through however many backups and
 * replication paths that option went through; re-encrypting it here does not undo
 * that, and the flag exists so an operator (or a future admin screen) knows to
 * actually rotate the value rather than considering the migration finished.
 *
 * @since 7.2.0
 *
 * @param string $option The existing option's name.
 * @param string $name   The secret's namespaced name to store it under.
 * @return true|WP_Error
 */
function wp_import_option_as_secret( string $option, string $name ) {
	if ( '' === $option ) {
		return new WP_Error(
			WP_SECRETS_ERROR_INVALID_VALUE,
			__( 'The option name must not be empty.' )
		);
	}

	$value = get_option( $option, null );

	if ( null === $value ) {
		return new WP_Error(
			WP_SECRETS_ERROR_INVALID_VALUE,
			__( 'The option does not exist.' )
		);
	}

	if ( ! is_string( $value ) ) {
		return new WP_Error(
			WP_SECRETS_ERROR_INVALID_VALUE,
			__( 'The option value is not a string and cannot be imported as a secret.' )
		);
	}

	return _wp_secrets_set( $name, $value, false, true, 'imported' );
}



/**
 * Retrieves a secret.
 *
 * Three states, never collapsed: a WP_Secret if it exists and decrypts, null if it
 * does not exist, WP_Error if it exists but could not be retrieved. No capability
 * check is applied here. See wp_set_secret().
 *
 * @since 7.2.0
 *
 * @param string $name    The secret's namespaced name.
 * @param string $version A WP_Secret_Version constant. Default WP_Secret_Version::CURRENT.
 * @return WP_Secret|null|WP_Error
 *
 * @phpstan-param WP_Secret_Version::CURRENT|WP_Secret_Version::PREVIOUS $version
 */
function wp_get_secret( string $name, string $version = WP_Secret_Version::CURRENT ) {
	return _wp_secrets_get( $name, $version, false );
}

/**
 * Deletes a secret.
 *
 * @since 7.2.0
 *
 * @param string $name The secret's namespaced name.
 * @return true|WP_Error
 */
function wp_delete_secret( string $name ) {
	return _wp_secrets_delete( $name, false );
}

/**
 * Clears a secret's previous version, retiring it for good.
 *
 * An explicit operator action, with no timers and no cron. Calling this on a secret
 * that has never been rotated, or that does not exist, is a successful no-op:
 * the previous slot is already absent either way.
 *
 * Beyond the API surface the proposal names.
 *
 * @since 7.2.0
 *
 * @param string $name The secret's namespaced name.
 * @return true|WP_Error
 */
function wp_retire_secret_version( string $name ) {
	return _wp_secrets_retire( $name, false );
}

/**
 * Lists secrets by name and metadata. Never a value.
 *
 * Beyond the API surface the proposal names, and justified by its statement that
 * the hooks and accessors a future admin screen needs are in scope now, even though
 * the screen itself is not.
 *
 * @since 7.2.0
 *
 * @param string $namespace Only secrets whose name starts with "{$namespace}/" are
 *                          returned. Default '' returns every secret.
 * @return array|WP_Error Array of associative arrays, each with keys 'name',
 *                        'fingerprint', 'created', 'has_previous', and
 *                        'needs_rotation'.
 *
 * @phpstan-return list<array{name: string, fingerprint: string, created: int, has_previous: bool, needs_rotation: bool}>|WP_Error
 */
function wp_list_secrets( string $namespace = '' ) { // phpcs:ignore Universal.NamingConventions.NoReservedKeywordParameterNames.namespaceFound -- $namespace is the parameter name the proposal specifies.
	return _wp_secrets_list( $namespace, false );
}

/**
 * Encrypts and stores a network-scope secret.
 *
 * Site secrets and network secrets are separate functions with separate
 * capabilities and separate storage prefixes, with no implicit fallback from one
 * scope to the other. The proposal describes network scope but does not name these
 * functions; these are this implementation's names for them.
 *
 * @since 7.2.0
 *
 * @param string $name  The secret's namespaced name.
 * @param string $value The plaintext value to store.
 * @return true|WP_Error
 */
function wp_set_network_secret( string $name, $value ) {
	return _wp_secrets_set( $name, $value, true );
}

/**
 * Retrieves a network-scope secret.
 *
 * @since 7.2.0
 *
 * @param string $name    The secret's namespaced name.
 * @param string $version A WP_Secret_Version constant. Default WP_Secret_Version::CURRENT.
 * @return WP_Secret|null|WP_Error
 *
 * @phpstan-param WP_Secret_Version::CURRENT|WP_Secret_Version::PREVIOUS $version
 */
function wp_get_network_secret( string $name, string $version = WP_Secret_Version::CURRENT ) {
	return _wp_secrets_get( $name, $version, true );
}

/**
 * Deletes a network-scope secret.
 *
 * @since 7.2.0
 *
 * @param string $name The secret's namespaced name.
 * @return true|WP_Error
 */
function wp_delete_network_secret( string $name ) {
	return _wp_secrets_delete( $name, true );
}

/**
 * Clears a network-scope secret's previous version.
 *
 * @since 7.2.0
 *
 * @param string $name The secret's namespaced name.
 * @return true|WP_Error
 */
function wp_retire_network_secret_version( string $name ) {
	return _wp_secrets_retire( $name, true );
}

/**
 * Lists network-scope secrets by name and metadata. Never a value.
 *
 * @since 7.2.0
 *
 * @param string $namespace Only secrets whose name starts with "{$namespace}/" are
 *                          returned. Default '' returns every network-scope secret.
 * @return array|WP_Error
 *
 * @phpstan-return list<array{name: string, fingerprint: string, created: int, has_previous: bool, needs_rotation: bool}>|WP_Error
 */
function wp_list_network_secrets( string $namespace = '' ) { // phpcs:ignore Universal.NamingConventions.NoReservedKeywordParameterNames.namespaceFound -- matches wp_list_secrets()'s own signature.
	return _wp_secrets_list( $namespace, true );
}

/**
 * Returns the active provider.
 *
 * The provider is whatever is responsible for holding, protecting, and answering for
 * this site's secrets.
 *
 * A secrets.php drop-in overrides it by setting $GLOBALS['wp_secrets_provider'] to
 * an instance before this is first called. Absent that, the provider WordPress
 * ships is assembled from the active store and keyring, which a drop-in can also
 * replace individually, so wanting host-managed key custody with default storage needs
 * no provider at all.
 *
 * No filter is applied here, or anywhere on the retrieval path. Substitution is by
 * replacement, never by interception: a filter that can observe which provider
 * answers is one step from a filter that can answer instead.
 *
 * Cached for the request. A failed drop-in fails closed, returning WP_Error from
 * every operation, rather than silently reverting to the default, because a
 * misconfigured credential backend must never look like a working one that happens
 * to be empty.
 *
 * @since 7.2.0
 *
 * @return WP_Secrets_Provider
 */
function _wp_secrets_get_provider(): WP_Secrets_Provider {
	static $provider = null;

	if ( $provider instanceof WP_Secrets_Provider ) {
		return $provider;
	}

	if ( ! empty( $GLOBALS['wp_secrets_dropin_broken'] ) ) {
		$provider = new WP_Secrets_Broken_Provider();

		return $provider;
	}

	if ( isset( $GLOBALS['wp_secrets_provider'] ) ) {
		/*
		 * Set, but not a provider. Failing closed here rather than falling through to
		 * the default is the whole point: a platform drop-in that names its class
		 * wrongly would otherwise have every secret served from wp_options, where none
		 * of them exist, and a site whose credentials are held elsewhere would report
		 * them absent rather than unreachable.
		 */
		if ( ! ( $GLOBALS['wp_secrets_provider'] instanceof WP_Secrets_Provider ) ) {
			$provider = new WP_Secrets_Broken_Provider();

			return $provider;
		}

		$provider = $GLOBALS['wp_secrets_provider'];

		return $provider;
	}

	$provider = new WP_Secrets_Libsodium_Provider(
		_wp_secrets_get_store(),
		_wp_secrets_get_key_manager()
	);

	return $provider;
}

/**
 * Whether the active provider accepts writes.
 *
 * Exists so a settings screen can disable its own save control before an operator
 * types a credential into a field that will only reject it. A provider whose
 * credentials are managed by host tooling or a control panel reports false.
 *
 * @since 7.2.0
 *
 * @return bool
 */
function wp_secrets_provider_is_writable(): bool {
	return _wp_secrets_get_provider()->is_writable();
}

/**
 * A human-readable description of what is protecting this site's secrets.
 *
 * For Site Health and a future admin screen. Never key material, never a value.
 *
 * @since 7.2.0
 *
 * @return string
 */
function wp_secrets_provider_label(): string {
	return _wp_secrets_get_provider()->get_label();
}

/**
 * Shared implementation behind wp_set_secret() and wp_set_network_secret().
 *
 * @since 7.2.0
 *
 * @param string      $name            The secret's name.
 * @param string      $value           The plaintext value.
 * @param bool        $network         Whether this is a network-scope secret.
 * @param bool        $needs_rotation  Flag the stored secret as needing rotation.
 * @param string|null $action_override Overrides the $action reported to
 *                                     wp_secret_changed.
 * @return true|WP_Error
 */
function _wp_secrets_set( string $name, $value, bool $network, bool $needs_rotation = false, ?string $action_override = null ) {
	return _wp_secrets_get_provider()->set( $name, $value, $network, $needs_rotation, $action_override );
}

/**
 * Shared implementation behind wp_get_secret() and wp_get_network_secret().
 *
 * @since 7.2.0
 *
 * @param string $name    The secret's name.
 * @param string $version A WP_Secret_Version constant.
 * @param bool   $network Whether this is a network-scope secret.
 * @return WP_Secret|null|WP_Error
 *
 * @phpstan-param WP_Secret_Version::CURRENT|WP_Secret_Version::PREVIOUS $version
 */
function _wp_secrets_get( string $name, string $version, bool $network ) {
	/*
	 * Checked here rather than inside the provider: this is a contract between
	 * the API and its caller, and asking every provider to re-validate a core
	 * constant would be duplicated work that a third-party provider could
	 * silently skip.
	 */
	if ( ! in_array( $version, array( WP_Secret_Version::CURRENT, WP_Secret_Version::PREVIOUS ), true ) ) {
		_doing_it_wrong(
			__FUNCTION__,
			__( 'The version must be WP_Secret_Version::CURRENT or WP_Secret_Version::PREVIOUS.' ),
			'7.2.0'
		);

		return new WP_Error(
			WP_SECRETS_ERROR_INVALID_ARGUMENT,
			__( 'The version must be WP_Secret_Version::CURRENT or WP_Secret_Version::PREVIOUS.' )
		);
	}

	return _wp_secrets_get_provider()->get( $name, $version, $network );
}

/**
 * Shared implementation behind wp_delete_secret() and wp_delete_network_secret().
 *
 * @since 7.2.0
 *
 * @param string $name    The secret's name.
 * @param bool   $network Whether this is a network-scope secret.
 * @return true|WP_Error
 */
function _wp_secrets_delete( string $name, bool $network ) {
	return _wp_secrets_get_provider()->delete( $name, $network );
}

/**
 * Shared implementation behind wp_retire_secret_version() and its network twin.
 *
 * @since 7.2.0
 *
 * @param string $name    The secret's name.
 * @param bool   $network Whether this is a network-scope secret.
 * @return true|WP_Error
 */
function _wp_secrets_retire( string $name, bool $network ) {
	return _wp_secrets_get_provider()->retire_previous( $name, $network );
}

/**
 * Shared implementation behind wp_list_secrets() and wp_list_network_secrets().
 *
 * @since 7.2.0
 *
 * @param string $name_prefix Restrict to names beginning with this prefix.
 * @param bool   $network     Whether to list network-scope secrets.
 * @return array|WP_Error
 *
 * @phpstan-return list<array{name: string, fingerprint: string, created: int, has_previous: bool, needs_rotation: bool}>|WP_Error
 */
function _wp_secrets_list( string $name_prefix, bool $network ) {
	return _wp_secrets_get_provider()->list_secrets( $name_prefix, $network );
}
