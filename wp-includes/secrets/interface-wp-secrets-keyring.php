<?php
/**
 * Secrets API: WP_Secrets_Keyring interface
 *
 * @package WordPress
 * @subpackage Secrets
 * @since 7.2.0
 */

/**
 * Wraps and unwraps the root key that everything else in this API derives from.
 *
 * A KMS or HSM lives behind this interface in a production deployment. An
 * implementation is never handed a plaintext secret, only 32 bytes of key material,
 * and cannot turn encryption off. There is no method here that
 * accepts a plaintext secret value at all.
 *
 * An implementation should run WP_Secrets_Keyring_Conformance against itself before
 * shipping.
 *
 * @since 7.2.0
 */
interface WP_Secrets_Keyring {

	/**
	 * Wraps (encrypts) raw key material for storage.
	 *
	 * Must not be deterministic: two calls with the same key material must return
	 * different values. WP_Secrets_Key_Manager::rotate_site_key() stores the
	 * re-wrapped value with update_site_option(), which reports an unchanged value
	 * as a failure, and WP_Secrets_Keyring_Conformance checks this.
	 *
	 * @since 7.2.0
	 *
	 * @param string $key_material Raw key material to protect.
	 * @return string|WP_Error Opaque wrapped value on success, WP_Error on failure.
	 */
	public function wrap( string $key_material );

	/**
	 * Unwraps (decrypts) previously wrapped key material.
	 *
	 * @since 7.2.0
	 *
	 * @param string $wrapped An opaque value previously returned by wrap().
	 * @return string|WP_Error Raw key material on success, WP_Error on failure.
	 */
	public function unwrap( string $wrapped );

	/**
	 * A human-readable description of the key source, for Site Health.
	 *
	 * @since 7.2.0
	 *
	 * @return string
	 */
	public function get_key_source(): string;
}
