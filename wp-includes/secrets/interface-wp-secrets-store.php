<?php
/**
 * Secrets API: WP_Secrets_Store interface
 *
 * @package WordPress
 * @subpackage Secrets
 * @since 7.2.0
 */

/**
 * Where a secret's encrypted record lives.
 *
 * An implementation is never handed a plaintext secret, only the record array
 * produced by WP_Secrets_Cipher and assembled by the functions in secrets.php, and
 * cannot turn encryption off; there is no method here that accepts or returns
 * anything but ciphertext-bearing structures.
 *
 * A platform store that is read-only from the application, with credentials managed
 * by a separate CLI or dashboard, returns WP_Error from set() rather than pretending
 * the write succeeded. Providers declare writability up front through
 * WP_Secrets_Provider::is_writable().
 *
 * @since 7.2.0
 */
interface WP_Secrets_Store {

	/**
	 * Reads a secret's stored record.
	 *
	 * @since 7.2.0
	 *
	 * @param string $name    The secret's namespaced name.
	 * @param bool   $network Whether this is a network-scope secret.
	 * @return array<mixed>|null|WP_Error The record array if it exists. Null if it does not.
	 *                             WP_Error if it could not be determined which.
	 */
	public function get( string $name, bool $network = false );

	/**
	 * Writes a secret's record.
	 *
	 * @since 7.2.0
	 *
	 * @param string       $name    The secret's namespaced name.
	 * @param array<mixed> $record  The record to store.
	 * @param bool         $network Whether this is a network-scope secret.
	 * @return bool|WP_Error True on success. WP_Error on failure, including when the
	 *                       store does not accept writes.
	 */
	public function set( string $name, array $record, bool $network = false );

	/**
	 * Deletes a secret's record.
	 *
	 * @since 7.2.0
	 *
	 * @param string $name    The secret's namespaced name.
	 * @param bool   $network Whether this is a network-scope secret.
	 * @return bool|WP_Error True on success (including if it did not exist).
	 *                       WP_Error on failure.
	 */
	public function delete( string $name, bool $network = false );

	/**
	 * Lists the names of every secret in this store, for this scope. Never a value.
	 *
	 * @since 7.2.0
	 *
	 * @param bool $network Whether to list network-scope secrets.
	 * @return string[]|WP_Error Array of secret names on success. WP_Error on failure.
	 */
	public function list_names( bool $network = false );
}
