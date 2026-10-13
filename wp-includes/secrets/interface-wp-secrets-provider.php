<?php
/**
 * Secrets API: WP_Secrets_Provider interface
 *
 * @package WordPress
 * @subpackage Secrets
 * @since 7.2.0
 */

/**
 * Interface for a secrets provider.
 *
 * A provider is responsible for a secret: storing it, protecting it, and returning
 * it to a caller. It is the outermost extension point in the Secrets API, and the
 * one a hosting platform implements.
 *
 * WordPress ships WP_Secrets_Libsodium_Provider, which encrypts with libsodium and
 * stores ciphertext in the options tables. That provider is the default rather than
 * a privileged case: a platform that protects credentials in a key management
 * service, a hardware security module, or its own control panel implements this
 * same interface.
 *
 * A provider must be stronger than the default, never weaker. Storing a plaintext
 * where the default would have stored ciphertext is what this interface exists to
 * prevent. Receiving a value over an authenticated channel and holding it in a
 * hardware security module satisfies that requirement.
 *
 * WordPress cannot verify any of this. A provider is loaded from a drop-in, which is
 * fully trusted code: it runs before plugins, and could already read every secret by
 * implementing the keyring. get_protection_boundary() and is_writable() are
 * declarations rather than enforcement. They exist so that a developer reviewing a
 * drop-in can see what it claims, Site Health can report where credentials are
 * protected, and a settings screen can determine that a write will be refused before
 * an operator enters a credential. They are not a security boundary.
 *
 * Every implementation shares these contracts:
 *
 * - Three states, never collapsed. get() returns a WP_Secret, null when the secret
 *   does not exist, or WP_Error when it exists but cannot be produced. Reporting an
 *   unreachable backend as absent turns an outage into an apparently deleted
 *   credential.
 * - Fail closed. A provider that cannot answer returns WP_Error. It never
 *   substitutes a weaker source, and never returns a partial or placeholder value.
 * - No filter on the retrieval path. A provider replaces a component; it is not a
 *   hook. Nothing is given the opportunity to observe or alter a value in transit.
 * - Never persist a value more weakly than the provider protects it. A provider that
 *   fetches over the network must not write the plaintext into the persistent object
 *   cache. WP_Secret cannot round-trip a plaintext through wp_cache_set(), and
 *   caching the raw value alongside it would undo that. Memoize within the request
 *   only.
 *
 * @since 7.2.0
 */
interface WP_Secrets_Provider {

	/**
	 * Protection happens inside WordPress.
	 *
	 * The provider holds ciphertext that WordPress produced, and WordPress holds the
	 * key material.
	 *
	 * @since 7.2.0
	 * @var string
	 */
	const BOUNDARY_WORDPRESS = 'wordpress';

	/**
	 * Protection happens outside WordPress.
	 *
	 * The provider is itself the encryption boundary, and WordPress is a consumer of a
	 * credential it does not protect.
	 *
	 * @since 7.2.0
	 * @var string
	 */
	const BOUNDARY_PROVIDER = 'provider';

	/**
	 * Retrieves a secret.
	 *
	 * Returning null means "I am not responsible for this name" as well as "this
	 * name does not exist". The two are the same answer from a caller's point of
	 * view, and keeping them distinct would require a provider to enumerate names
	 * it has never heard of.
	 *
	 * @since 7.2.0
	 *
	 * @param string $name    The secret's name.
	 * @param string $version A WP_Secret_Version constant. A provider with no
	 *                        version history returns null for PREVIOUS, which is
	 *                        absence rather than an error.
	 * @param bool   $network Whether this is a network-scope secret.
	 * @return WP_Secret|null|WP_Error
	 *
	 * @phpstan-param WP_Secret_Version::CURRENT|WP_Secret_Version::PREVIOUS $version
	 */
	public function get( string $name, string $version, bool $network = false );

	/**
	 * Stores a secret.
	 *
	 * A provider whose credentials are managed elsewhere, by a control panel, host
	 * tooling, or a KMS with its own access policy, returns WP_Error here with code
	 * WP_SECRETS_ERROR_PROVIDER_READ_ONLY, and reports false from is_writable() so
	 * that callers can find that out without attempting the write first.
	 *
	 * Implementations are responsible for firing the `wp_secret_changed` action once
	 * the write has succeeded, with `$network` as its last argument. The API does
	 * not fire it on the provider's behalf, because only the provider knows the
	 * prior fingerprint without paying for an additional read. An audit hook that
	 * stopped firing once a host installed a provider would be unreliable in exactly
	 * the situation it matters most, so WP_Secrets_Provider_Conformance checks it.
	 * A write that fails fires nothing.
	 *
	 * @since 7.2.0
	 *
	 * @param string      $name           The secret's name.
	 * @param string      $value          The plaintext value.
	 * @param bool        $network        Whether this is a network-scope secret.
	 * @param bool        $needs_rotation Mark the stored secret as needing rotation.
	 *                                    Set for values that arrived from somewhere
	 *                                    less protected than this provider, such as
	 *                                    an imported option. A provider with nowhere
	 *                                    to record this may ignore it, but must not
	 *                                    report it as honored.
	 * @param string|null $action         Overrides the action reported to
	 *                                    `wp_secret_changed`; null means the
	 *                                    provider decides between 'created' and
	 *                                    'updated'.
	 * @return true|WP_Error
	 */
	public function set( string $name, $value, bool $network = false, bool $needs_rotation = false, ?string $action = null );

	/**
	 * Deletes a secret.
	 *
	 * Deleting a secret that does not exist is a success.
	 *
	 * Fires `wp_secret_changed` with the 'deleted' action once a secret has been
	 * deleted, for the reason given on set(). A provider that can tell nothing was
	 * there to delete fires nothing.
	 *
	 * @since 7.2.0
	 *
	 * @param string $name    The secret's name.
	 * @param bool   $network Whether this is a network-scope secret.
	 * @return true|WP_Error
	 */
	public function delete( string $name, bool $network = false );

	/**
	 * Clears a secret's previous version, if this provider keeps one.
	 *
	 * A provider with no version history treats this as a successful no-op: there
	 * is nothing to retire, which is the state the caller asked for.
	 *
	 * Fires `wp_secret_changed` with the 'retired' action once a previous version
	 * has been cleared, for the reason given on set(). Nothing fires when there was
	 * no previous version to clear.
	 *
	 * @since 7.2.0
	 *
	 * @param string $name    The secret's name.
	 * @param bool   $network Whether this is a network-scope secret.
	 * @return true|WP_Error
	 */
	public function retire_previous( string $name, bool $network = false );

	/**
	 * Lists secrets by name and metadata. Never values.
	 *
	 * @since 7.2.0
	 *
	 * @param string $name_prefix Restrict to names beginning with this prefix, or
	 *                            '' for all of them.
	 * @param bool   $network     Whether to list network-scope secrets.
	 * @return array|WP_Error List of arrays with keys 'name', 'fingerprint',
	 *                        'created', 'has_previous', 'needs_rotation'.
	 *
	 * @phpstan-return list<array{name: string, fingerprint: string, created: int, has_previous: bool, needs_rotation: bool}>|WP_Error
	 */
	public function list_secrets( string $name_prefix = '', bool $network = false );

	/**
	 * A short human-readable name for this provider, for Site Health.
	 *
	 * Describes the protection, not the vendor's marketing: "AWS KMS
	 * (alias/wp-secrets)" tells an operator something, "SuperSecure Pro" does not.
	 * Never key material, never a credential, never a value.
	 *
	 * @since 7.2.0
	 *
	 * @return string
	 */
	public function get_label(): string;

	/**
	 * Where secrets this provider holds are actually protected.
	 *
	 * One of the BOUNDARY_* constants. This is what lets Site Health and a future
	 * admin screen tell an operator whether their credentials are protected by
	 * WordPress's own libsodium envelope or by something outside it, a question
	 * they currently have no way to answer, and the one hosts asked to be able to
	 * answer honestly.
	 *
	 * @since 7.2.0
	 *
	 * @return string
	 *
	 * @phpstan-return self::BOUNDARY_*
	 */
	public function get_protection_boundary(): string;

	/**
	 * Whether wp_set_secret() can succeed against this provider.
	 *
	 * False for a provider whose credentials are managed by host tooling or a
	 * control panel. Declared rather than discovered so a settings screen can
	 * disable its save control before an operator types a credential into a field
	 * that will only reject it.
	 *
	 * @since 7.2.0
	 *
	 * @return bool
	 */
	public function is_writable(): bool;
}
