<?php
/**
 * Secrets API: WP_Secrets_Libsodium_Provider class
 *
 * @package WordPress
 * @subpackage Secrets
 * @since 7.2.0
 */

/**
 * The provider WordPress ships.
 *
 * Uses libsodium envelope encryption and stores ciphertext in the options tables.
 *
 * This is the default rather than a privileged case. Everything it does is expressed
 * through WP_Secrets_Provider, the same interface a platform implements, so a KMS-
 * or HSM-backed provider is a peer rather than an exception carved into the side of
 * this one.
 *
 * It is assembled from two smaller pieces, and that is deliberate: a host who wants
 * their own key custody but is happy with WordPress's storage swaps only the
 * keyring and keeps everything else, and the inverse works too. Coupling them would
 * force an all-or-nothing decision most sites cannot make.
 *
 * - WP_Secrets_Store   – where the ciphertext records live.
 * - WP_Secrets_Keyring – what wraps the root key everything else derives from.
 *
 * Neither is handed a plaintext, because for this provider the encryption boundary
 * genuinely is inside WordPress. That is a property of *this* implementation rather
 * than a rule imposed on every provider; see WP_Secrets_Provider for why the
 * distinction matters.
 *
 * @since 7.2.0
 */
final class WP_Secrets_Libsodium_Provider implements WP_Secrets_Provider {

	/**
	 * Where ciphertext records live.
	 *
	 * @since 7.2.0
	 * @var WP_Secrets_Store
	 */
	private $store;

	/**
	 * Derives the per-scope master keys this provider encrypts under.
	 *
	 * @since 7.2.0
	 * @var WP_Secrets_Key_Manager
	 */
	private $key_manager;

	/**
	 * Composes the provider from its two replaceable halves.
	 *
	 * @since 7.2.0
	 *
	 * @param WP_Secrets_Store       $store       Where ciphertext records live.
	 * @param WP_Secrets_Key_Manager $key_manager Derives per-scope master keys.
	 */
	public function __construct( WP_Secrets_Store $store, WP_Secrets_Key_Manager $key_manager ) {
		$this->store       = $store;
		$this->key_manager = $key_manager;
	}

	/**
	 * A description of what is protecting these secrets, for Site Health.
	 *
	 * Reports the keyring's own description rather than a fixed string: "libsodium"
	 * is only half the answer, and which key source is in use is the half an
	 * operator cannot otherwise see.
	 *
	 * @since 7.2.0
	 *
	 * @return string
	 */
	public function get_label(): string {
		$keyring = $this->key_manager->get_keyring();

		return sprintf(
			/* translators: %s: Human-readable description of the key source. */
			__( 'WordPress (libsodium), key source: %s' ),
			$keyring->get_key_source()
		);
	}

	/**
	 * Protection happens inside WordPress for this provider.
	 *
	 * @since 7.2.0
	 *
	 * @return string
	 *
	 * @phpstan-return self::BOUNDARY_*
	 */
	public function get_protection_boundary(): string {
		return self::BOUNDARY_WORDPRESS;
	}

	/**
	 * Always true.
	 *
	 * A store that cannot accept a write reports that from set() itself. There is no
	 * separate capability flag to consult, and the shipped store accepts writes
	 * unconditionally.
	 *
	 * @since 7.2.0
	 *
	 * @return bool
	 */
	public function is_writable(): bool {
		return true;
	}
	/**
	 * Validates the shape of a record read back from a store.
	 *
	 * Runs before any decryption is attempted.
	 *
	 * @since 7.2.0
	 *
	 * @param mixed $record Candidate record.
	 * @return true|WP_Error
	 */
	private function validate_record_shape( $record ) {
		if ( ! is_array( $record ) || ! array_key_exists( 'v', $record ) || ! array_key_exists( 'current', $record ) ) {
			return new WP_Error(
				WP_SECRETS_ERROR_RECORD_MALFORMED,
				__( 'The stored secret record is missing required fields.' )
			);
		}

		if ( WP_SECRETS_RECORD_VERSION !== $record['v'] ) {
			return new WP_Error(
				WP_SECRETS_ERROR_RECORD_UNSUPPORTED_VERSION,
				sprintf(
					/* translators: %s: Unrecognized record format version. */
					__( 'Secret record format version "%s" is not supported.' ),
					is_scalar( $record['v'] ) ? $record['v'] : gettype( $record['v'] )
				)
			);
		}

		if ( ! is_array( $record['current'] ) ) {
			return new WP_Error(
				WP_SECRETS_ERROR_RECORD_MALFORMED,
				__( 'The stored secret record is missing required fields.' )
			);
		}

		return true;
	}

	/**
	 * Reads the record a write or delete is about to replace, tolerating corruption.
	 *
	 * A write path needs the prior record only to report the old fingerprint on the
	 * change hook. That is not worth refusing the operation over: if the stored record
	 * is unreadable, overwriting or deleting it is the only repair an operator has
	 * through this API, and returning WP_Error here would leave a corrupted secret
	 * permanently stuck – neither readable, nor fixable, nor removable without editing
	 * the database by hand.
	 *
	 * An unreachable *store* is different, and still aborts: that is an infrastructure
	 * failure, not a data problem, and continuing would mean writing blind.
	 *
	 * @since 7.2.0
	 *
	 * @param WP_Secrets_Store $store   The active store.
	 * @param string           $name    The secret's namespaced name.
	 * @param bool             $network Whether this is a network-scope secret.
	 * @return array<mixed>|null|WP_Error The prior record, null if absent or unreadable,
	 *                                    or WP_Error only if the store itself is
	 *                                    unavailable.
	 */
	private function read_prior_record( WP_Secrets_Store $store, string $name, bool $network ) {
		$existing = $store->get( $name, $network );

		if ( is_wp_error( $existing ) ) {
			if ( WP_SECRETS_ERROR_STORE_UNAVAILABLE === $existing->get_error_code() ) {
				return $existing;
			}

			return null;
		}

		return $existing;
	}

	/**
	 * Extracts the current slot's stored fingerprint from a record, if it has one.
	 *
	 * @since 7.2.0
	 *
	 * @param mixed $record A record previously read from the store, or null.
	 * @return string The fingerprint, or '' if there is not a usable one.
	 */
	private function stored_fingerprint( $record ): string {
		if ( ! is_array( $record ) || ! isset( $record['current'] ) || ! is_array( $record['current'] ) ) {
			return '';
		}

		return $this->slot_fingerprint( $record['current'] );
	}

	/**
	 * Extracts a single slot's stored fingerprint, if it has one.
	 *
	 * @since 7.2.0
	 *
	 * @param mixed $slot A slot previously read from the store.
	 * @return string The fingerprint, or '' if there is not a usable one.
	 */
	private function slot_fingerprint( $slot ): string {
		if ( ! is_array( $slot ) || ! isset( $slot['fingerprint'] ) || ! is_string( $slot['fingerprint'] ) ) {
			return '';
		}

		return $slot['fingerprint'];
	}

	/**
	 * Re-encrypts an outgoing current slot so it can be stored as the previous slot.
	 *
	 * A slot's ciphertext is bound, via AAD, to the exact position it was written to.
	 * Copying its stored bytes as-is into the other slot would leave it permanently
	 * undecryptable there: the authentication tag was computed for the slot it came
	 * from, and every future read asks for it under the other one. Demoting a slot
	 * means decrypting it under its old binding and re-encrypting the same plaintext
	 * under the new one.
	 *
	 * @since 7.2.0
	 *
	 * @param WP_Secrets_Cipher $cipher       A cipher instance.
	 * @param string            $master_key   32-byte master key.
	 * @param string            $scope        'site' or 'network'.
	 * @param int               $site_id      Blog id for site scope, 0 for network scope.
	 * @param string            $name         The secret's namespaced name.
	 * @param array<mixed>      $current_slot The outgoing current slot, as stored.
	 * @return array|WP_Error The same value, re-encrypted and bound to
	 *                        WP_Secret_Version::PREVIOUS. 'created' and
	 *                        'needs_rotation' carry over unchanged; 'fingerprint' is
	 *                        recomputed but identical, since the plaintext and master
	 *                        key are unchanged.
	 *
	 * @phpstan-return array{dk: string, dk_nonce: string, ct: string, nonce: string, fingerprint: string, created: int, needs_rotation: bool}|WP_Error
	 * @phpstan-param 'site'|'network' $scope
	 */
	private function demote_slot( WP_Secrets_Cipher $cipher, string $master_key, string $scope, int $site_id, string $name, array $current_slot ) {
		$plaintext = $cipher->decrypt_value( $master_key, $scope, $site_id, $name, WP_Secret_Version::CURRENT, $current_slot );

		if ( is_wp_error( $plaintext ) ) {
			return $plaintext;
		}

		$demoted = $cipher->encrypt_value( $master_key, $scope, $site_id, $name, WP_Secret_Version::PREVIOUS, $plaintext );

		wp_secrets_memzero( $plaintext );

		if ( is_wp_error( $demoted ) ) {
			return $demoted;
		}

		$demoted['created']        = isset( $current_slot['created'] ) && is_int( $current_slot['created'] ) ? $current_slot['created'] : time();
		$demoted['needs_rotation'] = ! empty( $current_slot['needs_rotation'] );

		return $demoted;
	}

	/**
	 * Shared implementation behind the three secret-writing functions.
	 *
	 * Backs wp_set_secret(), wp_set_network_secret(), and wp_import_option_as_secret(),
	 * which select their behavior through the last two parameters.
	 *
	 * @since 7.2.0
	 *
	 * @param string      $name           The secret's namespaced name.
	 * @param string      $value          The plaintext value.
	 * @param bool        $network        Whether this is a network-scope secret.
	 * @param bool        $needs_rotation Value for the new current slot's
	 *                                    'needs_rotation' flag. False for an ordinary
	 *                                    write; wp_import_option_as_secret() passes true,
	 *                                    since a credential that sat in an option is
	 *                                    already in backups and re-encrypting does not
	 *                                    fix that.
	 * @param string|null $action          When given, used as the $action reported to
	 *                                     the wp_secret_changed hook instead of the
	 *                                     usual 'created'/'updated' detection –
	 *                                     wp_import_option_as_secret() passes 'imported'.
	 * @return true|WP_Error
	 */
	public function set( string $name, $value, bool $network = false, bool $needs_rotation = false, ?string $action = null ) {
		$name_check = wp_secrets_validate_name( $name );

		if ( is_wp_error( $name_check ) ) {
			return $name_check;
		}

		if ( ! is_string( $value ) ) {
			return new WP_Error(
				WP_SECRETS_ERROR_INVALID_VALUE,
				__( 'Secret values must be strings.' )
			);
		}

		$store = $this->store;

		$scope   = $network ? 'network' : 'site';
		$site_id = $network ? 0 : get_current_blog_id();

		$existing = $this->read_prior_record( $store, $name, $network );

		if ( is_wp_error( $existing ) ) {
			return $existing;
		}

		$master_key = $this->key_manager->get_master_key( $scope, $network ? null : $site_id );

		if ( is_wp_error( $master_key ) ) {
			return $master_key;
		}

		$cipher   = new WP_Secrets_Cipher();
		$new_slot = $cipher->encrypt_value( $master_key, $scope, $site_id, $name, WP_Secret_Version::CURRENT, $value );

		if ( is_wp_error( $new_slot ) ) {
			wp_secrets_memzero( $master_key );

			return $new_slot;
		}

		$new_slot['created']        = time();
		$new_slot['needs_rotation'] = $needs_rotation;

		$record = array(
			'v'       => WP_SECRETS_RECORD_VERSION,
			'current' => $new_slot,
		);

		/*
		 * Demotion: the outgoing current slot becomes the new previous slot. Only
		 * one previous slot is ever kept – whatever was already in
		 * $existing['previous'] is never carried forward, which is what makes a
		 * third write discard the oldest value rather than accumulating history.
		 *
		 * This cannot be a plain array copy. A slot's AAD binds its ciphertext to
		 * the exact position it occupies – current or previous – so a slot moved
		 * verbatim would still carry an authentication tag computed for 'current'
		 * while every future read asks for it under 'previous', and would fail to
		 * decrypt forever. Demoting requires decrypting under the outgoing binding
		 * and re-encrypting under the incoming one.
		 *
		 * If the outgoing current slot cannot be decrypted at all – already
		 * corrupted, or orphaned by a botched key rotation – it is dropped rather
		 * than failing this write. The value was already unreadable before this
		 * call; refusing to let an operator set a new one over it would repeat the
		 * corrupted-record-blocks-everything failure this API is built to avoid.
		 */
		if ( is_array( $existing ) && isset( $existing['current'] ) && is_array( $existing['current'] ) ) {
			$demoted = $this->demote_slot( $cipher, $master_key, $scope, $site_id, $name, $existing['current'] );

			if ( ! is_wp_error( $demoted ) ) {
				$record['previous'] = $demoted;
			}
		}

		wp_secrets_memzero( $master_key );

		$result = $store->set( $name, $record, $network );

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		$is_update       = is_array( $existing );
		$old_fingerprint = $this->stored_fingerprint( $existing );

		/**
		 * Fires whenever a secret is created, updated, deleted, imported, or retired.
		 *
		 * The only hook in the core-bound Secrets API code, and it does not fire on the
		 * retrieval path. Fingerprints are readable; values are never passed.
		 *
		 * @since 7.2.0
		 *
		 * @param string $name            The secret's namespaced name.
		 * @param string $action          One of 'created', 'updated', 'deleted',
		 *                                'imported', 'retired'.
		 * @param int    $actor_id        The current user id, or 0.
		 * @param int    $timestamp       Unix timestamp of the change.
		 * @param string $old_fingerprint The previous fingerprint, or '' if none.
		 * @param string $new_fingerprint The new fingerprint, or '' if the secret was deleted.
		 * @param bool   $network         Whether the secret is network-scope. A site-scope
		 *                                secret belongs to the current site.
		 */
		do_action(
			'wp_secret_changed',
			$name,
			null !== $action ? $action : ( $is_update ? 'updated' : 'created' ),
			get_current_user_id(),
			$new_slot['created'],
			$old_fingerprint,
			$new_slot['fingerprint'],
			$network
		);

		return true;
	}

	/**
	 * Shared implementation behind wp_get_secret() and wp_get_network_secret().
	 *
	 * @since 7.2.0
	 *
	 * @param string $name    The secret's namespaced name.
	 * @param string $version A WP_Secret_Version constant.
	 * @param bool   $network Whether this is a network-scope secret.
	 * @return WP_Secret|null|WP_Error
	 *
	 * @phpstan-param WP_Secret_Version::CURRENT|WP_Secret_Version::PREVIOUS $version
	 */
	public function get( string $name, string $version, bool $network = false ) {

		$name_check = wp_secrets_validate_name( $name );

		if ( is_wp_error( $name_check ) ) {
			return $name_check;
		}

		$record = $this->store->get( $name, $network );

		if ( is_wp_error( $record ) ) {
			return $record;
		}

		if ( null === $record ) {
			return null;
		}

		$shape_check = $this->validate_record_shape( $record );

		if ( is_wp_error( $shape_check ) ) {
			return $shape_check;
		}

		if ( ! isset( $record[ $version ] ) || ! is_array( $record[ $version ] ) ) {
			// The secret exists, but this slot does not. Absence, not an error –
			// this matters most for PREVIOUS on a secret that has never been rotated.
			return null;
		}

		$scope   = $network ? 'network' : 'site';
		$site_id = $network ? 0 : get_current_blog_id();

		$master_key = $this->key_manager->get_master_key( $scope, $network ? null : $site_id );

		if ( is_wp_error( $master_key ) ) {
			return $master_key;
		}

		$cipher    = new WP_Secrets_Cipher();
		$plaintext = $cipher->decrypt_value( $master_key, $scope, $site_id, $name, $version, $record[ $version ] );

		if ( is_wp_error( $plaintext ) ) {
			wp_secrets_memzero( $master_key );

			return $plaintext;
		}

		/*
		 * Recomputed from the plaintext just decrypted, never read back from the record.
		 * The stored 'fingerprint' field sits outside the AAD and so is not
		 * authenticated: anyone who can write to the store can set it to anything
		 * without disturbing the ciphertext. Trusting it would make
		 * WP_Secret::fingerprint() attacker-controlled – which matters most where a
		 * fingerprint comparison gates something irreversible, such as a migration
		 * verifying a value before deleting its source. The stored copy still exists,
		 * but only so wp_list_secrets() has something to show without decrypting.
		 */
		$fingerprint = $cipher->fingerprint( $master_key, $plaintext );

		wp_secrets_memzero( $master_key );

		if ( is_wp_error( $fingerprint ) ) {
			wp_secrets_memzero( $plaintext );

			return $fingerprint;
		}

		$secret = new WP_Secret( $name, $plaintext, $fingerprint );

		wp_secrets_memzero( $plaintext );

		return $secret;
	}

	/**
	 * Shared implementation behind wp_delete_secret() and wp_delete_network_secret().
	 *
	 * @since 7.2.0
	 *
	 * @param string $name    The secret's namespaced name.
	 * @param bool   $network Whether this is a network-scope secret.
	 * @return true|WP_Error
	 */
	public function delete( string $name, bool $network = false ) {
		$name_check = wp_secrets_validate_name( $name );

		if ( is_wp_error( $name_check ) ) {
			return $name_check;
		}

		$store = $this->store;

		$existing = $this->read_prior_record( $store, $name, $network );

		if ( is_wp_error( $existing ) ) {
			return $existing;
		}

		$result = $store->delete( $name, $network );

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		if ( is_array( $existing ) ) {
			/** This action is documented in wp-includes/secrets/class-wp-secrets-libsodium-provider.php */
			do_action(
				'wp_secret_changed',
				$name,
				'deleted',
				get_current_user_id(),
				time(),
				$this->stored_fingerprint( $existing ),
				'',
				$network
			);
		}

		return true;
	}

	/**
	 * Shared implementation behind the two version-retirement functions.
	 *
	 * Backs wp_retire_secret_version() and wp_retire_network_secret_version().
	 *
	 * Beyond the API surface the proposal names. It states that retirement is "an
	 * explicit operator action" but names no function; this is this implementation's
	 * name for it, pending confirmation in the comments thread.
	 *
	 * @since 7.2.0
	 *
	 * @param string $name    The secret's namespaced name.
	 * @param bool   $network Whether this is a network-scope secret.
	 * @return true|WP_Error
	 */
	public function retire_previous( string $name, bool $network = false ) {
		$name_check = wp_secrets_validate_name( $name );

		if ( is_wp_error( $name_check ) ) {
			return $name_check;
		}

		$store  = $this->store;
		$record = $store->get( $name, $network );

		if ( is_wp_error( $record ) ) {
			return $record;
		}

		if ( null === $record || ! isset( $record['previous'] ) ) {
			/*
			 * The goal of retirement – "there is no previous slot" – is already
			 * true, whether because the secret doesn't exist or because it was
			 * never rotated. A successful no-op, not an error, and deliberately
			 * checked before the write-support check below: a store that can't
			 * accept writes can still report a correct "nothing to do" without
			 * that being mistaken for the store refusing the operation.
			 */
			return true;
		}

		$retired_fingerprint = $this->slot_fingerprint( $record['previous'] );

		unset( $record['previous'] );

		$result = $store->set( $name, $record, $network );

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		/** This action is documented in wp-includes/secrets/class-wp-secrets-libsodium-provider.php */
		do_action(
			'wp_secret_changed',
			$name,
			'retired',
			get_current_user_id(),
			time(),
			$retired_fingerprint,
			'',
			$network
		);

		return true;
	}

	/**
	 * Shared implementation behind wp_list_secrets() and wp_list_network_secrets().
	 *
	 * Beyond the API surface the proposal names, and justified by its statement that
	 * "the hooks and accessors an admin screen would need are in scope now; the screen
	 * itself is not."
	 *
	 * Fingerprints returned here come directly from the stored record field, not
	 * recomputed by decrypting each secret. That is a deliberate difference from
	 * WP_Secret::fingerprint(), which always recomputes – recomputing here would mean
	 * decrypting every matching secret just to list them, defeating the point of a
	 * lightweight listing call. This is safe specifically because a list entry is
	 * documented as informational only and
	 * is never used to gate anything; nothing in this codebase performs a security
	 * decision based on a fingerprint returned from this function.
	 *
	 * @since 7.2.0
	 *
	 * @param string $name_prefix Only secrets whose name starts with "{$name_prefix}/"
	 *                            are returned. Default '' returns every secret in this
	 *                            scope. Named to match the public function's own
	 *                            $namespace parameter, without using the reserved word
	 *                            'namespace' in an internal signature.
	 * @param bool   $network     Whether to list network-scope secrets.
	 * @return array|WP_Error Array of associative arrays, each with keys 'name',
	 *                        'fingerprint', 'created', 'has_previous', and
	 *                        'needs_rotation'. Never a value. WP_Error on failure.
	 *
	 * @phpstan-return list<array{name: string, fingerprint: string, created: int, has_previous: bool, needs_rotation: bool}>|WP_Error
	 */
	public function list_secrets( string $name_prefix = '', bool $network = false ) {

		$store = $this->store;

		$names = $store->list_names( $network );

		if ( is_wp_error( $names ) ) {
			return $names;
		}

		$entries = array();

		foreach ( $names as $name ) {
			if ( '' !== $name_prefix && 0 !== strpos( $name, $name_prefix . '/' ) ) {
				continue;
			}

			$record = $store->get( $name, $network );

			if ( ! is_wp_error( $record ) && null !== $record && true === $this->validate_record_shape( $record ) && is_array( $record['current'] ) ) {
				$entries[] = array(
					'name'           => $name,
					'fingerprint'    => $this->stored_fingerprint( $record ),
					'created'        => isset( $record['current']['created'] ) && is_int( $record['current']['created'] ) ? $record['current']['created'] : 0,
					'has_previous'   => isset( $record['previous'] ) && is_array( $record['previous'] ),
					'needs_rotation' => ! empty( $record['current']['needs_rotation'] ),
				);

				continue;
			}

			/*
			 * A corrupted or unreadable record is still listed, with whatever
			 * metadata could not be salvaged left blank, rather than silently
			 * omitted. Site Health's "undecryptable secrets" check depends on
			 * exactly this: a secret that has gone bad must remain visible.
			 */
			$entries[] = array(
				'name'           => $name,
				'fingerprint'    => '',
				'created'        => 0,
				'has_previous'   => false,
				'needs_rotation' => false,
			);
		}

		return $entries;
	}
}
