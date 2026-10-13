<?php
/**
 * Secrets API: WP_Secrets_Key_Manager class
 *
 * @package WordPress
 * @subpackage Secrets
 * @since 7.2.0
 */

/**
 * Manages the root key and derives per-scope master keys from it.
 *
 * Owns the root key's generation, storage, and rotation.
 *
 * There is exactly one root key per install: 32 random bytes, generated once, wrapped
 * by the active WP_Secrets_Keyring, and stored via update_site_option() (which is a
 * plain option on single-site, and network-wide on multisite). It is the only value a
 * site-key rotation ever re-wraps, on a single site or on a 500-site network.
 *
 * Master keys are derived from the root key on demand and never stored:
 *
 * - Site scope, blog N: sodium_crypto_kdf_derive_from_key( 32, N, 'wpsecsit', $root ).
 *   Distinct per blog, which shared network-wide salts otherwise cannot provide.
 * - Network scope: sodium_crypto_kdf_derive_from_key( 32, 0, 'wpsecnet', $root ).
 *   Subkey id 0 is reserved for this (blog ids start at 1) and the context string
 *   differs from the site path, so there is no collision between the two. Identical
 *   on every blog, so a network secret written on one blog reads on every other.
 *
 * One unwrapped copy of the root key lives in this object for the rest of the
 * request, in memory only, never in the object cache. It is replaced whenever the
 * stored wrapped value changes – a rotation, a re-wrap, a restore – so it is never
 * stale. Callers of get_root_key() still receive a copy and must zero it themselves;
 * this object's own copy is not theirs to zero. The practical effect: a remote
 * keyring (a KMS or HSM call) is invoked once per request, not once per secret.
 *
 * @since 7.2.0
 */
final class WP_Secrets_Key_Manager {

	/**
	 * Option name the wrapped root key is stored under.
	 *
	 * Always accessed through the *_site_option() functions directly, never through a
	 * WP_Secrets_Store. Swapping the pluggable secret store therefore never relocates
	 * the root key, and swapping the keyring never affects where ciphertext lives.
	 *
	 * @since 7.2.0
	 * @var string
	 */
	const ROOT_KEY_OPTION = '_wp_secrets_root_key';

	/**
	 * KDF context for deriving a site-scope master key. Exactly 8 bytes.
	 *
	 * @since 7.2.0
	 * @var string
	 */
	const SITE_KDF_CONTEXT = 'wpsecsit';

	/**
	 * KDF context for deriving the network-scope master key. Exactly 8 bytes.
	 *
	 * @since 7.2.0
	 * @var string
	 */
	const NETWORK_KDF_CONTEXT = 'wpsecnet';

	/**
	 * Reserved KDF subkey id for network scope.
	 *
	 * Blog ids start at 1, so this can never collide with a site-scope subkey id.
	 *
	 * @since 7.2.0
	 * @var int
	 */
	const NETWORK_SUBKEY_ID = 0;

	/**
	 * The keyring used to wrap and unwrap the root key.
	 *
	 * @since 7.2.0
	 * @var WP_Secrets_Keyring
	 */
	private $keyring;

	/**
	 * The unwrapped root key currently cached for this request, or null if nothing
	 * has been unwrapped yet.
	 *
	 * @since 7.2.0
	 * @var string|null
	 */
	private $cached_root_key = null;

	/**
	 * The wrapped value $cached_root_key was unwrapped from, or null if nothing has
	 * been unwrapped yet. Used to detect that the stored wrapped value changed
	 * underneath this object (a rotation, a re-wrap, a restore) so the cache is not
	 * served stale.
	 *
	 * @since 7.2.0
	 * @var string|null
	 */
	private $cached_wrapped = null;

	/**
	 * Constructor.
	 *
	 * @since 7.2.0
	 *
	 * @param WP_Secrets_Keyring|null $keyring Keyring to use. Defaults to the
	 *                                         built-in config-based provider.
	 */
	public function __construct( ?WP_Secrets_Keyring $keyring = null ) {
		$this->keyring = $keyring ? $keyring : new WP_Secrets_Config_Key_Provider();
	}

	/**
	 * Returns the keyring in use.
	 *
	 * Used by Site Health and by `wp secret dropin` and `wp secret health`, which all
	 * need to describe the active keyring without duplicating the logic that resolves
	 * it.
	 *
	 * @since 7.2.0
	 *
	 * @return WP_Secrets_Keyring
	 */
	public function get_keyring(): WP_Secrets_Keyring {
		return $this->keyring;
	}

	/**
	 * Derives a scope's master key from the root key.
	 *
	 * @since 7.2.0
	 *
	 * @param string   $scope   'site' or 'network'.
	 * @param int|null $site_id Blog id for site scope. Defaults to the current blog.
	 *                          Ignored for network scope.
	 * @return string|WP_Error 32-byte master key on success. WP_Error on failure,
	 *                         including when a caller passes an invalid scope or
	 *                         site id – see WP_Secrets_Cipher::validate_common()
	 *                         for why that is a WP_Error and not an exception.
	 *
	 * @phpstan-param 'site'|'network' $scope
	 */
	public function get_master_key( string $scope, ?int $site_id = null ) {
		if ( ! in_array( $scope, array( 'site', 'network' ), true ) ) {
			$message = __( 'The scope must be "site" or "network".' );

			_doing_it_wrong( __METHOD__, $message, '7.2.0' );

			return new WP_Error( WP_SECRETS_ERROR_INVALID_ARGUMENT, $message );
		}

		if ( 'network' === $scope ) {
			$subkey_id = self::NETWORK_SUBKEY_ID;
			$context   = self::NETWORK_KDF_CONTEXT;
		} else {
			$subkey_id = null === $site_id ? get_current_blog_id() : $site_id;

			if ( $subkey_id < 1 ) {
				$message = __( 'The site id must be a positive integer.' );

				_doing_it_wrong( __METHOD__, $message, '7.2.0' );

				return new WP_Error( WP_SECRETS_ERROR_INVALID_ARGUMENT, $message );
			}

			$context = self::SITE_KDF_CONTEXT;
		}

		$root_key = $this->get_root_key();

		if ( is_wp_error( $root_key ) ) {
			return $root_key;
		}

		if ( ! function_exists( 'sodium_crypto_kdf_derive_from_key' ) ) {
			wp_secrets_memzero( $root_key );

			return new WP_Error(
				WP_SECRETS_ERROR_CRYPTO_UNAVAILABLE,
				__( 'No libsodium implementation is available.' )
			);
		}

		$master_key = sodium_crypto_kdf_derive_from_key( 32, $subkey_id, $context, $root_key );

		wp_secrets_memzero( $root_key );

		return $master_key;
	}

	/**
	 * Returns the root key, generating and persisting one on first use.
	 *
	 * @since 7.2.0
	 *
	 * @return string|WP_Error 32 raw bytes on success. WP_Error on failure.
	 */
	public function get_root_key() {
		$wrapped = $this->get_wrapped_root_key();

		if ( false === $wrapped ) {
			return $this->generate_root_key();
		}

		if ( ! is_string( $wrapped ) ) {
			return new WP_Error(
				WP_SECRETS_ERROR_RECORD_MALFORMED,
				__( 'The stored root key is not a string.' )
			);
		}

		if ( null !== $this->cached_root_key && $wrapped === $this->cached_wrapped ) {
			return $this->cached_root_key;
		}

		$root_key = $this->keyring->unwrap( $wrapped );

		if ( is_string( $root_key ) ) {
			$this->cached_wrapped  = $wrapped;
			$this->cached_root_key = $root_key;
		}

		return $root_key;
	}

	/**
	 * Re-wraps the root key under a new keyring, without touching any stored secret.
	 *
	 * Every derived master key is unchanged by this: they come from the root key's
	 * raw bytes, which rotation never alters, only its wrapping. No secret value is
	 * ever re-encrypted as a result of a site-key rotation.
	 *
	 * @since 7.2.0
	 *
	 * @param WP_Secrets_Keyring $old_keyring Unwraps the current wrapped root key.
	 * @param WP_Secrets_Keyring $new_keyring Wraps it again for storage.
	 * @return true|WP_Error
	 */
	public function rotate_site_key( WP_Secrets_Keyring $old_keyring, WP_Secrets_Keyring $new_keyring ) {
		$wrapped = $this->get_wrapped_root_key();

		if ( ! is_string( $wrapped ) ) {
			return new WP_Error(
				WP_SECRETS_ERROR_STORE_UNAVAILABLE,
				__( 'No root key exists to rotate.' )
			);
		}

		if ( $old_keyring === $this->keyring && null !== $this->cached_root_key && $wrapped === $this->cached_wrapped ) {
			$root_key = $this->cached_root_key;
		} else {
			$root_key = $old_keyring->unwrap( $wrapped );
		}

		if ( is_wp_error( $root_key ) ) {
			return $root_key;
		}

		$rewrapped = $new_keyring->wrap( $root_key );

		if ( is_wp_error( $rewrapped ) ) {
			wp_secrets_memzero( $root_key );

			return $rewrapped;
		}

		/*
		 * update_site_option() returns false both on genuine failure and when the
		 * new value equals the old one – a documented ambiguity shared with
		 * update_option(). In practice this never collides here: wrap() draws a
		 * fresh random nonce every call, so the re-wrapped value is only equal to
		 * the old one if this call somehow re-wrapped under the exact same nonce,
		 * astronomically unlikely.
		 */
		if ( ! update_site_option( self::ROOT_KEY_OPTION, $rewrapped ) ) {
			wp_secrets_memzero( $root_key );

			return new WP_Error(
				WP_SECRETS_ERROR_STORE_UNAVAILABLE,
				__( 'Could not store the re-wrapped root key.' )
			);
		}

		$this->cached_wrapped  = $rewrapped;
		$this->cached_root_key = $root_key;

		wp_secrets_memzero( $root_key );

		return true;
	}

	/**
	 * Returns the wrapped root key, adopting a pre-conversion copy if needed.
	 *
	 * A single site converted to multisite keeps its wrapped root key in its own
	 * (now main site's) options row: `update_site_option()` only starts writing to
	 * sitemeta once `is_multisite()` is true, and the conversion process does not
	 * move existing option rows. Without this, a network's first read would find no
	 * sitemeta row and mint a brand new root key, stranding every secret written
	 * before conversion.
	 *
	 * @since 7.2.0
	 *
	 * @return mixed The wrapped root key, or false if none exists anywhere. Anything
	 *               but a string or false is a corrupt row, which the caller reports.
	 */
	private function get_wrapped_root_key() {
		$wrapped = get_site_option( self::ROOT_KEY_OPTION );

		if ( false !== $wrapped || ! is_multisite() ) {
			return $wrapped;
		}

		$main_site_id = get_main_site_id();
		$legacy       = get_blog_option( $main_site_id, self::ROOT_KEY_OPTION );

		if ( ! is_string( $legacy ) ) {
			return false;
		}

		if ( add_site_option( self::ROOT_KEY_OPTION, $legacy ) ) {
			delete_blog_option( $main_site_id, self::ROOT_KEY_OPTION );
		}

		// Re-read: handles a concurrent request that already adopted it.
		return get_site_option( self::ROOT_KEY_OPTION );
	}

	/**
	 * Generates a new root key and persists it.
	 *
	 * Handles the race where two requests both find no root key at the same time.
	 *
	 * @since 7.2.0
	 *
	 * @return string|WP_Error
	 */
	private function generate_root_key() {
		$candidate = random_bytes( 32 );

		$wrapped = $this->keyring->wrap( $candidate );

		if ( is_wp_error( $wrapped ) ) {
			wp_secrets_memzero( $candidate );

			return $wrapped;
		}

		if ( add_site_option( self::ROOT_KEY_OPTION, $wrapped ) ) {
			$this->cached_wrapped  = $wrapped;
			$this->cached_root_key = $candidate;

			return $candidate;
		}

		// Lost the race: another request created one first. Use theirs, not ours.
		wp_secrets_memzero( $candidate );

		$existing = get_site_option( self::ROOT_KEY_OPTION );

		if ( ! is_string( $existing ) ) {
			return new WP_Error(
				WP_SECRETS_ERROR_STORE_UNAVAILABLE,
				__( 'Could not read the root key after losing the creation race.' )
			);
		}

		$root_key = $this->keyring->unwrap( $existing );

		if ( is_string( $root_key ) ) {
			$this->cached_wrapped  = $existing;
			$this->cached_root_key = $root_key;
		}

		return $root_key;
	}
}
