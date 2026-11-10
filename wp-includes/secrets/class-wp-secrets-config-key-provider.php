<?php
/**
 * Secrets API: WP_Secrets_Config_Key_Provider class
 *
 * @package WordPress
 * @subpackage Secrets
 * @since 7.2.0
 */

/**
 * The default keyring.
 *
 * Derives a site key from wp-config.php and uses it to wrap and unwrap the root key
 * with authenticated encryption.
 *
 * Site key derivation:
 *
 * 1. WP_SECRETS_KEY base64-decodes (strictly) to exactly 32 bytes: those decoded
 *    bytes are used raw. This is the recommended form, such as the output of
 *    base64_encode( random_bytes( 32 ) ).
 * 2. WP_SECRETS_KEY is any other string: the literal constant string is hashed
 *    with BLAKE2b to 32 bytes. This is the legacy interpretation – sites arriving
 *    from a prior plugin with a constant of arbitrary shape are not locked out of
 *    their own credentials by a hard failure here.
 *
 * There is no fallback. Without a usable WP_SECRETS_KEY, every Secrets API call
 * that needs the key returns WP_Error. Deriving a key from the authentication keys
 * and salts would make every stored secret unreadable whenever the salts are
 * rotated, which is routine advice for them.
 *
 * The wp-config-sample.php placeholder is not usable, and neither is a value that
 * one of the authentication keys and salts also has. A sample that was never
 * filled in gives every constant the same phrase, translated in localized samples,
 * so the second check rejects the placeholder in any language.
 *
 * WP_SECRETS_KEY_PREVIOUS follows the same rules and exists only so a site-key
 * rotation can unwrap under the old key before wrapping under the new one.
 *
 * @since 7.2.0
 */
final class WP_Secrets_Config_Key_Provider extends WP_Secrets_Keyring {

	/**
	 * AAD binding the root key's wrapped form to this purpose.
	 *
	 * Prevents the wrapped root key from being replayed as some other wrapped value.
	 *
	 * @since 7.2.0
	 * @var string
	 */
	const AAD = 'wp-secrets-root-key-v1';

	/**
	 * The exact text wp-config-sample.php ships for every key and salt constant.
	 *
	 * A site running with this literal value never ran the secret-key generator.
	 * Treating it as usable would derive a key that any attacker can also derive.
	 *
	 * @since 7.2.0
	 * @var string
	 */
	const KNOWN_PLACEHOLDER = 'put your unique phrase here';

	/**
	 * WP_SECRETS_KEY is not defined.
	 *
	 * @since 7.2.0
	 * @var string
	 */
	const KEY_STATUS_UNDEFINED = 'undefined';

	/**
	 * WP_SECRETS_KEY is defined, but empty, the placeholder, or equal to a key or salt.
	 *
	 * @since 7.2.0
	 * @var string
	 */
	const KEY_STATUS_UNUSABLE = 'unusable';

	/**
	 * WP_SECRETS_KEY is the base64 encoding of 32 bytes, the recommended form.
	 *
	 * @since 7.2.0
	 * @var string
	 */
	const KEY_STATUS_BASE64 = 'base64';

	/**
	 * WP_SECRETS_KEY is usable but in another form, so it is hashed to 32 bytes.
	 *
	 * @since 7.2.0
	 * @var string
	 */
	const KEY_STATUS_LEGACY = 'legacy';

	/**
	 * The authentication keys and salts that wp-config-sample.php defines.
	 *
	 * @since 7.2.0
	 * @var string[]
	 */
	const AUTH_CONSTANTS = array(
		'AUTH_KEY',
		'SECURE_AUTH_KEY',
		'LOGGED_IN_KEY',
		'NONCE_KEY',
		'AUTH_SALT',
		'SECURE_AUTH_SALT',
		'LOGGED_IN_SALT',
		'NONCE_SALT',
	);

	/**
	 * Whether to derive from WP_SECRETS_KEY_PREVIOUS instead of WP_SECRETS_KEY.
	 *
	 * @since 7.2.0
	 * @var bool
	 */
	private $use_previous_key;

	/**
	 * Constructor.
	 *
	 * @since 7.2.0
	 *
	 * @param bool $use_previous_key Derive from WP_SECRETS_KEY_PREVIOUS rather than
	 *                               WP_SECRETS_KEY. Used only during a site-key
	 *                               rotation, to unwrap under the outgoing key.
	 */
	public function __construct( bool $use_previous_key = false ) {
		$this->use_previous_key = $use_previous_key;
	}

	/**
	 * Wraps raw key material under the derived site key.
	 *
	 * @since 7.2.0
	 *
	 * @param string $key_material Raw key material to protect.
	 * @return string|WP_Error
	 */
	public function wrap(
		#[\SensitiveParameter]
		string $key_material
	) {
		if ( '' === $key_material ) {
			return new WP_Error(
				WP_SECRETS_ERROR_INVALID_VALUE,
				__( 'Key material to wrap must not be empty.' )
			);
		}

		if ( ! function_exists( 'sodium_crypto_aead_xchacha20poly1305_ietf_encrypt' ) ) {
			return new WP_Error(
				WP_SECRETS_ERROR_CRYPTO_UNAVAILABLE,
				__( 'No libsodium implementation is available.' )
			);
		}

		$site_key = $this->get_site_key();

		if ( is_wp_error( $site_key ) ) {
			return $site_key;
		}

		$nonce      = random_bytes( SODIUM_CRYPTO_AEAD_XCHACHA20POLY1305_IETF_NPUBBYTES );
		$ciphertext = sodium_crypto_aead_xchacha20poly1305_ietf_encrypt( $key_material, self::AAD, $nonce, $site_key );

		wp_secrets_memzero( $site_key );

		return base64_encode( $nonce . $ciphertext );
	}

	/**
	 * Unwraps key material previously wrapped by wrap().
	 *
	 * @since 7.2.0
	 *
	 * @param string $wrapped An opaque value previously returned by wrap().
	 * @return string|WP_Error
	 */
	public function unwrap( string $wrapped ) {
		if ( '' === $wrapped ) {
			return new WP_Error(
				WP_SECRETS_ERROR_KEY_UNAVAILABLE,
				__( 'Nothing to unwrap.' )
			);
		}

		if ( ! function_exists( 'sodium_crypto_aead_xchacha20poly1305_ietf_decrypt' ) ) {
			return new WP_Error(
				WP_SECRETS_ERROR_CRYPTO_UNAVAILABLE,
				__( 'No libsodium implementation is available.' )
			);
		}

		$raw          = base64_decode( $wrapped, true );
		$nonce_length = SODIUM_CRYPTO_AEAD_XCHACHA20POLY1305_IETF_NPUBBYTES;

		if ( false === $raw || strlen( $raw ) <= $nonce_length ) {
			return new WP_Error(
				WP_SECRETS_ERROR_KEY_UNAVAILABLE,
				__( 'The wrapped key material is malformed.' )
			);
		}

		$nonce      = substr( $raw, 0, $nonce_length );
		$ciphertext = substr( $raw, $nonce_length );

		$site_key = $this->get_site_key();

		if ( is_wp_error( $site_key ) ) {
			return $site_key;
		}

		$key_material = sodium_crypto_aead_xchacha20poly1305_ietf_decrypt( $ciphertext, self::AAD, $nonce, $site_key );

		wp_secrets_memzero( $site_key );

		if ( ! is_string( $key_material ) ) {
			return new WP_Error(
				WP_SECRETS_ERROR_KEY_UNAVAILABLE,
				__( 'The wrapped key material could not be decrypted with the configured site key.' )
			);
		}

		return $key_material;
	}

	/**
	 * Reports the state of WP_SECRETS_KEY.
	 *
	 * Site Health and wp_is_secrets_api_available() use this rather than the text of
	 * get_key_source(), which is translated.
	 *
	 * @since 7.2.0
	 *
	 * @return string One of the KEY_STATUS_* constants.
	 */
	public function get_key_status(): string {
		if ( ! defined( 'WP_SECRETS_KEY' ) ) {
			return self::KEY_STATUS_UNDEFINED;
		}

		if ( ! $this->is_usable_key_value( WP_SECRETS_KEY, $this->get_auth_constant_values() ) ) {
			return self::KEY_STATUS_UNUSABLE;
		}

		return $this->is_canonical_base64_32( WP_SECRETS_KEY ) ? self::KEY_STATUS_BASE64 : self::KEY_STATUS_LEGACY;
	}

	/**
	 * Describes the active key source, for Site Health.
	 *
	 * @since 7.2.0
	 *
	 * @return string
	 */
	public function get_key_source(): string {
		switch ( $this->get_key_status() ) {
			case self::KEY_STATUS_UNDEFINED:
				/* translators: %s: WP_SECRETS_KEY */
				return sprintf( __( '%s is not defined' ), 'WP_SECRETS_KEY' );

			case self::KEY_STATUS_UNUSABLE:
				/* translators: %s: WP_SECRETS_KEY */
				return sprintf( __( '%s is defined but not usable' ), 'WP_SECRETS_KEY' );

			case self::KEY_STATUS_BASE64:
				/* translators: %s: WP_SECRETS_KEY */
				return sprintf( __( '%s (base64-encoded 32 bytes)' ), 'WP_SECRETS_KEY' );

			default:
				/* translators: %s: WP_SECRETS_KEY */
				return sprintf( __( '%s (legacy interpretation, hashed as an opaque string)' ), 'WP_SECRETS_KEY' );
		}
	}

	/**
	 * Derives the 32-byte site key per the rules documented on this class.
	 *
	 * @since 7.2.0
	 *
	 * @return string|WP_Error
	 */
	private function get_site_key() {
		$constant_name = $this->use_previous_key ? 'WP_SECRETS_KEY_PREVIOUS' : 'WP_SECRETS_KEY';

		if ( ! defined( $constant_name ) ) {
			return new WP_Error(
				WP_SECRETS_ERROR_KEY_UNAVAILABLE,
				sprintf(
					/* translators: %s: PHP constant name. */
					__( '%s is not defined.' ),
					$constant_name
				)
			);
		}

		$raw_constant = constant( $constant_name );

		if ( ! $this->is_usable_key_value( $raw_constant, $this->get_auth_constant_values() ) ) {
			return new WP_Error(
				WP_SECRETS_ERROR_KEY_UNAVAILABLE,
				sprintf(
					/* translators: %s: PHP constant name. */
					__( '%s is defined but is not usable: it must be a non-empty string, not the wp-config-sample.php placeholder, and not the value of one of the authentication keys and salts.' ),
					$constant_name
				)
			);
		}

		$decoded = $this->decode_canonical_base64_32( $raw_constant );

		if ( null !== $decoded ) {
			return $decoded;
		}

		return sodium_crypto_generichash( $raw_constant, '', SODIUM_CRYPTO_AEAD_XCHACHA20POLY1305_IETF_KEYBYTES );
	}

	/**
	 * Whether a candidate key constant value is usable as key material.
	 *
	 * It must be a string that is not empty or only whitespace, must not be the
	 * wp-config-sample.php placeholder, and must not equal any of the authentication
	 * keys and salts.
	 *
	 * Takes explicit arguments rather than reading the constants directly so the
	 * decision logic is testable without touching real constants, which – once
	 * WordPress has bootstrapped once in a process – can never be redefined to a
	 * different value for a test.
	 *
	 * @since 7.2.0
	 *
	 * @param mixed    $value       Candidate value of WP_SECRETS_KEY or WP_SECRETS_KEY_PREVIOUS.
	 * @param string[] $auth_values Values of the authentication keys and salts that are defined.
	 * @return bool
	 *
	 * @phpstan-assert-if-true non-empty-string $value
	 */
	private function is_usable_key_value( $value, array $auth_values ): bool {
		return is_string( $value )
			&& '' !== trim( $value )
			&& self::KNOWN_PLACEHOLDER !== $value
			&& ! in_array( $value, $auth_values, true );
	}

	/**
	 * Returns the values of the authentication keys and salts that are defined.
	 *
	 * @since 7.2.0
	 *
	 * @return string[]
	 */
	private function get_auth_constant_values(): array {
		$values = array();

		foreach ( self::AUTH_CONSTANTS as $constant_name ) {
			if ( defined( $constant_name ) && is_string( constant( $constant_name ) ) ) {
				$values[] = constant( $constant_name );
			}
		}

		return $values;
	}

	/**
	 * Whether $value is the canonical base64 encoding of exactly 32 bytes.
	 *
	 * Uses strict-mode decoding plus a re-encode comparison, rather than a bare
	 * base64_decode(), so that a malformed or non-canonically-padded constant falls
	 * through to the legacy (hashed) interpretation instead of being silently
	 * accepted as 32 bytes of something else.
	 *
	 * @since 7.2.0
	 *
	 * @param mixed $value Candidate value.
	 * @return bool
	 */
	private function is_canonical_base64_32( $value ): bool {
		return null !== $this->decode_canonical_base64_32( $value );
	}

	/**
	 * Decodes $value if it is the canonical base64 encoding of exactly 32 bytes.
	 *
	 * @since 7.2.0
	 *
	 * @param mixed $value Candidate value.
	 * @return string|null The 32 decoded bytes, or null if $value is anything else.
	 */
	private function decode_canonical_base64_32( $value ): ?string {
		if ( ! is_string( $value ) ) {
			return null;
		}

		$decoded = base64_decode( $value, true );

		if ( false === $decoded || SODIUM_CRYPTO_AEAD_XCHACHA20POLY1305_IETF_KEYBYTES !== strlen( $decoded ) ) {
			return null;
		}

		return base64_encode( $decoded ) === $value ? $decoded : null;
	}
}
