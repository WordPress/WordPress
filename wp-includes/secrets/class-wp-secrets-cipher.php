<?php
/**
 * Secrets API: WP_Secrets_Cipher class
 *
 * @package WordPress
 * @subpackage Secrets
 * @since 7.2.0
 */

/**
 * Encrypts and decrypts a single record slot.
 *
 * A slot holds a per-secret data key wrapped by a master key, plus a value encrypted
 * under that data key.
 *
 * This class does not know where a master key comes from (that is
 * WP_Secrets_Key_Manager's job), does not know about the 'v'/current/previous record
 * envelope (that is assembled by the functions in secrets.php), and does not set
 * 'created' or 'needs_rotation' (those are facts about a write, not about the
 * cryptography). It knows exactly one thing: given a 32-byte master key and a single
 * slot's worth of material, encrypt or decrypt it correctly.
 *
 * @since 7.2.0
 */
final class WP_Secrets_Cipher {

	/**
	 * AAD purpose tag for a wrapped data key.
	 *
	 * @since 7.2.0
	 * @var string
	 */
	const AAD_DATA_KEY = 'wp-secrets-data-key-v1';

	/**
	 * AAD purpose tag for an encrypted value.
	 *
	 * @since 7.2.0
	 * @var string
	 */
	const AAD_VALUE = 'wp-secrets-value-v1';

	/**
	 * KDF context used to derive the fingerprint key from a master key.
	 *
	 * Exactly 8 bytes, as sodium_crypto_kdf_derive_from_key() requires.
	 *
	 * @since 7.2.0
	 * @var string
	 */
	const FINGERPRINT_KDF_CONTEXT = 'wpsecfpr';

	/**
	 * Encrypts a plaintext into a single record slot.
	 *
	 * @since 7.2.0
	 *
	 * @param string $master_key 32-byte master key for this scope.
	 * @param string $scope      'site' or 'network'.
	 * @param int    $site_id    Binds the AAD to a specific blog for site scope; use
	 *                           0 for network scope, which is not bound to any one blog.
	 * @param string $name       The secret's namespaced name.
	 * @param string $slot       A WP_Secret_Version constant.
	 * @param string $plaintext  The value to encrypt.
	 * @return array|WP_Error Slot array with keys 'dk', 'dk_nonce', 'ct', 'nonce',
	 *                        'fingerprint' on success. WP_Error on failure.
	 *
	 * @phpstan-return array{dk: string, dk_nonce: string, ct: string, nonce: string, fingerprint: string}|WP_Error
	 * @phpstan-param 'site'|'network' $scope
	 * @phpstan-param WP_Secret_Version::CURRENT|WP_Secret_Version::PREVIOUS $slot
	 */
	public function encrypt_value( string $master_key, string $scope, int $site_id, string $name, string $slot, $plaintext ) {
		$check = $this->validate_common( $master_key, $scope, $site_id, $name, $slot );

		if ( is_wp_error( $check ) ) {
			return $check;
		}

		if ( ! is_string( $plaintext ) ) {
			return new WP_Error(
				WP_SECRETS_ERROR_INVALID_VALUE,
				__( 'Secret values must be strings.' )
			);
		}

		if ( ! function_exists( 'sodium_crypto_aead_xchacha20poly1305_ietf_encrypt' ) ) {
			return new WP_Error(
				WP_SECRETS_ERROR_CRYPTO_UNAVAILABLE,
				__( 'No libsodium implementation is available.' )
			);
		}

		$fingerprint = $this->fingerprint( $master_key, $plaintext );

		if ( is_wp_error( $fingerprint ) ) {
			return $fingerprint;
		}

		$data_key = random_bytes( SODIUM_CRYPTO_AEAD_XCHACHA20POLY1305_IETF_KEYBYTES );

		$dk_nonce   = random_bytes( SODIUM_CRYPTO_AEAD_XCHACHA20POLY1305_IETF_NPUBBYTES );
		$wrapped_dk = sodium_crypto_aead_xchacha20poly1305_ietf_encrypt(
			$data_key,
			$this->build_aad( self::AAD_DATA_KEY, $scope, $site_id, $name, $slot ),
			$dk_nonce,
			$master_key
		);

		$value_nonce = random_bytes( SODIUM_CRYPTO_AEAD_XCHACHA20POLY1305_IETF_NPUBBYTES );
		$ciphertext  = sodium_crypto_aead_xchacha20poly1305_ietf_encrypt(
			$plaintext,
			$this->build_aad( self::AAD_VALUE, $scope, $site_id, $name, $slot ),
			$value_nonce,
			$data_key
		);

		wp_secrets_memzero( $data_key );

		return array(
			'dk'          => base64_encode( $wrapped_dk ),
			'dk_nonce'    => base64_encode( $dk_nonce ),
			'ct'          => base64_encode( $ciphertext ),
			'nonce'       => base64_encode( $value_nonce ),
			'fingerprint' => $fingerprint,
		);
	}

	/**
	 * Decrypts a single record slot back to its plaintext.
	 *
	 * @since 7.2.0
	 *
	 * @param string $master_key 32-byte master key for this scope.
	 * @param string $scope      'site' or 'network'. Must match what encrypt_value()
	 *                           was called with, or decryption fails.
	 * @param int    $site_id    Must match what encrypt_value() was called with.
	 * @param string $name       Must match what encrypt_value() was called with.
	 * @param string $slot       Must match what encrypt_value() was called with.
	 * @param mixed  $record     The slot array previously returned by encrypt_value().
	 * @return string|WP_Error Plaintext on success. WP_Error on failure.
	 *
	 * @phpstan-param 'site'|'network' $scope
	 * @phpstan-param WP_Secret_Version::CURRENT|WP_Secret_Version::PREVIOUS $slot
	 */
	public function decrypt_value( string $master_key, string $scope, int $site_id, string $name, string $slot, $record ) {
		$check = $this->validate_common( $master_key, $scope, $site_id, $name, $slot );

		if ( is_wp_error( $check ) ) {
			return $check;
		}

		$decoded = $this->decode_record_fields( $record );

		if ( is_wp_error( $decoded ) ) {
			return $decoded;
		}

		if ( ! function_exists( 'sodium_crypto_aead_xchacha20poly1305_ietf_decrypt' ) ) {
			return new WP_Error(
				WP_SECRETS_ERROR_CRYPTO_UNAVAILABLE,
				__( 'No libsodium implementation is available.' )
			);
		}

		$data_key = sodium_crypto_aead_xchacha20poly1305_ietf_decrypt(
			$decoded['dk'],
			$this->build_aad( self::AAD_DATA_KEY, $scope, $site_id, $name, $slot ),
			$decoded['dk_nonce'],
			$master_key
		);

		if ( ! is_string( $data_key ) ) {
			return new WP_Error(
				WP_SECRETS_ERROR_DECRYPTION_FAILED,
				__( "The secret's data key could not be decrypted." )
			);
		}

		$plaintext = sodium_crypto_aead_xchacha20poly1305_ietf_decrypt(
			$decoded['ct'],
			$this->build_aad( self::AAD_VALUE, $scope, $site_id, $name, $slot ),
			$decoded['nonce'],
			$data_key
		);

		wp_secrets_memzero( $data_key );

		if ( ! is_string( $plaintext ) ) {
			return new WP_Error(
				WP_SECRETS_ERROR_DECRYPTION_FAILED,
				__( 'The secret value could not be decrypted.' )
			);
		}

		return $plaintext;
	}

	/**
	 * Computes the keyed fingerprint of a plaintext under a master key.
	 *
	 * Keyed so a fingerprint is not a cross-site rainbow-table oracle: the same
	 * plaintext fingerprints differently under a different master key. Callers
	 * verifying a value against a previously stored fingerprint (for example, a
	 * migration's verify-before-delete step) must recompute this from freshly
	 * decrypted plaintext and compare – never trust a fingerprint read back from a
	 * record, which sits outside the AAD and is not authenticated.
	 *
	 * @since 7.2.0
	 *
	 * @param string $master_key 32-byte master key.
	 * @param string $plaintext  Value to fingerprint.
	 * @return string|WP_Error 32-character hex string on success. WP_Error on failure.
	 */
	public function fingerprint( string $master_key, $plaintext ) {
		if ( ! $this->is_valid_master_key( $master_key ) ) {
			return new WP_Error(
				WP_SECRETS_ERROR_KEY_UNAVAILABLE,
				__( 'A 32-byte master key is required.' )
			);
		}

		if ( ! is_string( $plaintext ) ) {
			return new WP_Error(
				WP_SECRETS_ERROR_INVALID_VALUE,
				__( 'Secret values must be strings.' )
			);
		}

		if ( ! function_exists( 'sodium_crypto_kdf_derive_from_key' ) || ! function_exists( 'sodium_crypto_generichash' ) ) {
			return new WP_Error(
				WP_SECRETS_ERROR_CRYPTO_UNAVAILABLE,
				__( 'No libsodium implementation is available.' )
			);
		}

		$fingerprint_key = sodium_crypto_kdf_derive_from_key( 32, 1, self::FINGERPRINT_KDF_CONTEXT, $master_key );
		$fingerprint     = sodium_crypto_generichash( $plaintext, $fingerprint_key, 16 );

		wp_secrets_memzero( $fingerprint_key );

		return bin2hex( $fingerprint );
	}

	/**
	 * Builds the AAD that binds a ciphertext to its full context.
	 *
	 * Binding covers purpose, scope, site, name, and slot, so that a record can never
	 * be replayed under any of them.
	 *
	 * Centralized in one place rather than inlined at each call site, and only ever
	 * called after validate_common() has confirmed $name passes
	 * wp_secrets_validate_name() – the validated character set contains no '|', which
	 * is what makes this delimiter safe to join on.
	 *
	 * @since 7.2.0
	 *
	 * @param string $purpose One of the AAD_* class constants.
	 * @param string $scope   'site' or 'network'.
	 * @param int    $site_id Blog id for site scope, 0 for network scope.
	 * @param string $name    Validated secret name.
	 * @param string $slot    A WP_Secret_Version constant.
	 * @return string
	 *
	 * @phpstan-param 'site'|'network' $scope
	 * @phpstan-param WP_Secret_Version::CURRENT|WP_Secret_Version::PREVIOUS $slot
	 */
	private function build_aad( string $purpose, string $scope, int $site_id, string $name, string $slot ): string {
		return sprintf( '%s|%s|%d|%s|%s', $purpose, $scope, $site_id, $name, $slot );
	}

	/**
	 * Validates the parameters shared by encrypt_value() and decrypt_value().
	 *
	 * $scope, $site_id, and $slot are never influenced by external input – every
	 * call site in this API supplies them internally – so a bad value here means
	 * the calling code is wrong, not that a runtime condition failed. That still
	 * reports as a WP_Error rather than an exception: WordPress functions return
	 * WP_Error or false, they do not throw. The _doing_it_wrong() notice is what
	 * makes the distinction visible during development. $name and $master_key
	 * legitimately vary at runtime (a plugin author's typo, an unavailable key
	 * backend) and have always reported the same way.
	 *
	 * @since 7.2.0
	 *
	 * @param string $master_key Candidate master key.
	 * @param string $scope      Candidate scope.
	 * @param int    $site_id    Candidate site id.
	 * @param string $name       Candidate secret name.
	 * @param string $slot       Candidate slot.
	 * @return true|WP_Error
	 *
	 * @phpstan-param 'site'|'network' $scope
	 * @phpstan-param WP_Secret_Version::CURRENT|WP_Secret_Version::PREVIOUS $slot
	 */
	private function validate_common( string $master_key, string $scope, int $site_id, string $name, string $slot ) {
		if ( ! in_array( $scope, array( 'site', 'network' ), true ) ) {
			return $this->invalid_argument( __( 'The scope must be "site" or "network".' ) );
		}

		if ( $site_id < 0 ) {
			return $this->invalid_argument( __( 'The site id must be a non-negative integer.' ) );
		}

		if ( ! in_array( $slot, array( WP_Secret_Version::CURRENT, WP_Secret_Version::PREVIOUS ), true ) ) {
			return $this->invalid_argument( __( 'The slot must be a WP_Secret_Version constant.' ) );
		}

		if ( ! $this->is_valid_master_key( $master_key ) ) {
			return new WP_Error(
				WP_SECRETS_ERROR_KEY_UNAVAILABLE,
				__( 'A 32-byte master key is required.' )
			);
		}

		$name_check = wp_secrets_validate_name( $name );

		if ( is_wp_error( $name_check ) ) {
			return $name_check;
		}

		return true;
	}

	/**
	 * Reports a caller error the way WordPress does.
	 *
	 * Emits a _doing_it_wrong() notice for the developer, and returns a WP_Error the
	 * caller can act on.
	 *
	 * @since 7.2.0
	 *
	 * @param string $message What the caller got wrong.
	 * @return WP_Error
	 */
	private function invalid_argument( string $message ): WP_Error {
		_doing_it_wrong( __CLASS__ . '::encrypt_value()/decrypt_value()', $message, '7.2.0' );

		return new WP_Error( WP_SECRETS_ERROR_INVALID_ARGUMENT, $message );
	}

	/**
	 * Whether a candidate value is usable as a 32-byte master key.
	 *
	 * @since 7.2.0
	 *
	 * @param string $master_key Candidate master key.
	 * @return bool
	 */
	private function is_valid_master_key( string $master_key ): bool {
		return SODIUM_CRYPTO_AEAD_XCHACHA20POLY1305_IETF_KEYBYTES === strlen( $master_key );
	}

	/**
	 * Validates and base64-decodes the four crypto fields of a record slot.
	 *
	 * @since 7.2.0
	 *
	 * @param mixed $record Candidate slot array.
	 * @return array<string, string>|WP_Error Decoded ('dk', 'dk_nonce', 'ct', 'nonce') on success.
	 */
	private function decode_record_fields( $record ) {
		if ( ! is_array( $record ) ) {
			return new WP_Error(
				WP_SECRETS_ERROR_RECORD_MALFORMED,
				__( 'Secret record slot must be an array.' )
			);
		}

		$decoded = array();

		foreach ( array( 'dk', 'dk_nonce', 'ct', 'nonce' ) as $field ) {
			if ( ! isset( $record[ $field ] ) || ! is_string( $record[ $field ] ) ) {
				return new WP_Error(
					WP_SECRETS_ERROR_RECORD_MALFORMED,
					sprintf(
						/* translators: %s: Record field name. */
						__( 'Secret record slot is missing the "%s" field.' ),
						$field
					)
				);
			}

			$value = base64_decode( $record[ $field ], true );

			if ( false === $value ) {
				return new WP_Error(
					WP_SECRETS_ERROR_RECORD_MALFORMED,
					sprintf(
						/* translators: %s: Record field name. */
						__( 'Secret record slot field "%s" is not valid base64.' ),
						$field
					)
				);
			}

			$decoded[ $field ] = $value;
		}

		if ( SODIUM_CRYPTO_AEAD_XCHACHA20POLY1305_IETF_NPUBBYTES !== strlen( $decoded['dk_nonce'] )
			|| SODIUM_CRYPTO_AEAD_XCHACHA20POLY1305_IETF_NPUBBYTES !== strlen( $decoded['nonce'] ) ) {
			return new WP_Error(
				WP_SECRETS_ERROR_RECORD_MALFORMED,
				__( 'Secret record slot has a malformed nonce.' )
			);
		}

		return $decoded;
	}
}
