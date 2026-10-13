<?php
/**
 * Secrets API: WP_Secret class
 *
 * @package WordPress
 * @subpackage Secrets
 * @since 7.2.0
 */

/**
 * A decrypted secret value, returned by wp_get_secret().
 *
 * Masking is total and unconditional. Every representation short of an explicit call
 * to reveal() yields the placeholder '[secret:{name}]' – printing, logging, JSON
 * encoding, or dumping an instance never exposes the plaintext.
 *
 * The plaintext is never a declared property of this class. It lives in a private
 * static table keyed by spl_object_id(), which is what makes the masking unconditional
 * rather than something every magic method has to individually get right: var_export()
 * in particular ignores __debugInfo() and __toString() and serializes declared
 * properties directly, so if the plaintext lived in one there would be no way to mask
 * it there at all.
 *
 * @since 7.2.0
 */
final class WP_Secret implements JsonSerializable {

	/**
	 * Plaintext values, keyed by the owning instance's spl_object_id().
	 *
	 * Deliberately not an SplObjectStorage keyed by the instance itself: that holds a
	 * strong reference to its keys, which would keep every WP_Secret ever constructed
	 * alive – and its plaintext un-zeroed – for the life of the request. Keying by
	 * the integer id instead leaves an instance's own reference count to govern its
	 * lifetime normally, so __destruct() fires and zeroes the value the moment nothing
	 * else references the instance.
	 *
	 * @since 7.2.0
	 * @var array<int, string>
	 */
	private static $vault = array();

	/**
	 * The secret's namespaced name.
	 *
	 * @since 7.2.0
	 * @var string
	 */
	private $name;

	/**
	 * Keyed fingerprint of the plaintext.
	 *
	 * Not sensitive. It is a one-way, per-site, per-key digest rather than the value
	 * itself.
	 *
	 * @since 7.2.0
	 * @var string
	 */
	private $fingerprint;

	/**
	 * Why this secret's value is unavailable to PHP, or null when it is available.
	 *
	 * Set only for a secret a provider can prove exists but will not hand over –
	 * an HSM that will sign with a key but never export it, a broker that releases
	 * a credential to a subsystem rather than to WordPress. Such a secret is still
	 * a real secret with a real name and fingerprint; what it is not is readable,
	 * and reveal() says so rather than returning something misleading.
	 *
	 * @since 7.2.0
	 * @var WP_Error|null
	 */
	private $withheld_reason = null;

	/**
	 * Constructor.
	 *
	 * Throws, where the rest of this API returns WP_Error for a caller mistake.
	 * That is not an inconsistency to be tidied up later: a constructor has no
	 * return channel, so the only alternatives are throwing or building a
	 * half-valid WP_Secret and letting it fail somewhere less obvious. Every
	 * function that *can* return WP_Error does – see
	 * this file's own reasoning below. The same rule covers the serialization
	 * and clone refusals below, which are magic methods with the same problem
	 * and an additional one: silently permitting them would leak a plaintext.
	 *
	 * Nothing outside this API constructs a WP_Secret in normal use; the values
	 * passed here come from _wp_secrets_get() having just decrypted them.
	 *
	 * @since 7.2.0
	 *
	 * @param string $name        The secret's namespaced name.
	 * @param string $value       The decrypted plaintext.
	 * @param string $fingerprint Keyed fingerprint of $value.
	 *
	 * @throws InvalidArgumentException If $name or $fingerprint is empty, or if $value
	 *                                  is not a string. $value may be empty.
	 */
	public function __construct( string $name, $value, string $fingerprint ) {
		if ( '' === $name ) {
			throw new InvalidArgumentException( 'WP_Secret requires a non-empty name.' );
		}

		if ( ! is_string( $value ) ) {
			throw new InvalidArgumentException( 'WP_Secret values must be strings.' );
		}

		if ( '' === $fingerprint ) {
			throw new InvalidArgumentException( 'WP_Secret requires a non-empty fingerprint.' );
		}

		$this->name        = $name;
		$this->fingerprint = $fingerprint;

		self::$vault[ spl_object_id( $this ) ] = $value;
	}

	/**
	 * Returns the decrypted plaintext.
	 *
	 * This is the only path to the stored plaintext anywhere in the API.
	 *
	 * Returns WP_Error for a secret whose provider will not release the value to
	 * PHP at all – see withheld(). That case does not arise for the provider
	 * WordPress ships, which decrypts eagerly and therefore always has a value in
	 * hand, so most callers will never see it. It is in the signature because a
	 * return type cannot be widened after adoption: leaving reveal() as
	 * `string` would permanently foreclose brokered and use-only credentials,
	 * which is a decision better made deliberately than by omission.
	 *
	 * @since 7.2.0
	 *
	 * @return string|WP_Error The plaintext, or WP_Error if this provider does not
	 *                         release values to PHP.
	 */
	public function reveal() {
		if ( null !== $this->withheld_reason ) {
			return $this->withheld_reason;
		}

		return self::$vault[ spl_object_id( $this ) ];
	}

	/**
	 * Builds a secret whose value exists but is not available to PHP.
	 *
	 * For a provider that can prove a credential exists, and can name and
	 * fingerprint it, but will not release it – typically because releasing it
	 * would defeat the point of where it is held. Everything except reveal()
	 * behaves normally, so such a secret still lists, still reports a stable
	 * fingerprint, and still masks itself in every output path.
	 *
	 * @since 7.2.0
	 *
	 * @param string   $name        The secret's name.
	 * @param string   $fingerprint A stable per-site digest, from the provider.
	 * @param WP_Error $reason      Why the value cannot be produced. Returned
	 *                              verbatim by reveal().
	 *
	 * @throws InvalidArgumentException If $name or $fingerprint fail the constructor's
	 *                                  checks.
	 *
	 * @return WP_Secret
	 */
	public static function withheld( string $name, string $fingerprint, WP_Error $reason ): WP_Secret {
		$secret = new self( $name, '', $fingerprint );

		$secret->withheld_reason = $reason;

		return $secret;
	}

	/**
	 * Returns the keyed fingerprint of the plaintext.
	 *
	 * Stable for a given value on a given site; not a value-recovery oracle.
	 *
	 * @since 7.2.0
	 *
	 * @return string
	 */
	public function fingerprint(): string {
		return $this->fingerprint;
	}

	/**
	 * Returns the secret's namespaced name.
	 *
	 * @since 7.2.0
	 *
	 * @return string
	 */
	public function get_name(): string {
		return $this->name;
	}

	/**
	 * Builds the masked placeholder used everywhere a plaintext would otherwise appear.
	 *
	 * @since 7.2.0
	 *
	 * @return string
	 *
	 * @phpstan-return non-falsy-string
	 */
	private function mask(): string {
		return '[secret:' . $this->name . ']';
	}

	/**
	 * Masks the instance for string conversion.
	 *
	 * Covers error_log( $secret ) and any other implicit string coercion.
	 *
	 * @since 7.2.0
	 *
	 * @return string
	 *
	 * @phpstan-return non-falsy-string
	 */
	public function __toString(): string {
		return $this->mask();
	}

	/**
	 * Masks the instance for var_dump().
	 *
	 * Note that print_r() and var_export() have no equivalent hook. They are safe
	 * regardless, because the plaintext is never a declared property for them to
	 * enumerate; they will surface the (non-sensitive) name and fingerprint rather
	 * than this placeholder. See docs/journal/open-questions.md for the documented
	 * var_export() limitation.
	 *
	 * @since 7.2.0
	 *
	 * @return array<string, string>
	 */
	public function __debugInfo(): array {
		return array( 'value' => $this->mask() );
	}

	/**
	 * Masks the instance for json_encode().
	 *
	 * @since 7.2.0
	 *
	 * @return string
	 *
	 * @phpstan-return non-falsy-string
	 */
	public function jsonSerialize(): string {
		return $this->mask();
	}

	/**
	 * Refuses serialization outright.
	 *
	 * @since 7.2.0
	 *
	 * @throws LogicException Always.
	 *
	 * @return void
	 */
	public function __sleep() {
		throw new LogicException( 'WP_Secret cannot be serialized.' );
	}

	/**
	 * Refuses unserialization outright.
	 *
	 * @since 7.2.0
	 *
	 * @throws LogicException Always.
	 *
	 * @return void
	 */
	public function __wakeup() {
		throw new LogicException( 'WP_Secret cannot be unserialized.' );
	}

	/**
	 * Refuses serialization outright.
	 *
	 * PHP calls this in preference to __sleep() when both are defined, so both must
	 * refuse independently.
	 *
	 * @since 7.2.0
	 *
	 * @throws LogicException Always.
	 *
	 * @return void
	 */
	public function __serialize() {
		throw new LogicException( 'WP_Secret cannot be serialized.' );
	}

	/**
	 * Refuses unserialization outright.
	 *
	 * @since 7.2.0
	 *
	 * @param array<mixed> $data Ignored.
	 *
	 * @throws LogicException Always.
	 *
	 * @return void
	 */
	public function __unserialize( array $data ): void {
		throw new LogicException( 'WP_Secret cannot be unserialized.' );
	}

	/**
	 * Refuses cloning outright.
	 *
	 * A clone would be a second, unaudited reference to the same plaintext with an
	 * independent lifetime – and, since the vault is keyed by object id rather than
	 * copied, a bare `clone` would otherwise leave the clone's reveal() silently
	 * reading nothing.
	 *
	 * @since 7.2.0
	 *
	 * @throws LogicException Always.
	 */
	public function __clone() {
		throw new LogicException( 'WP_Secret cannot be cloned.' );
	}

	/**
	 * Zeroes the plaintext and removes it from the vault.
	 *
	 * @since 7.2.0
	 */
	public function __destruct() {
		$id = spl_object_id( $this );

		if ( isset( self::$vault[ $id ] ) ) {
			wp_secrets_memzero( self::$vault[ $id ] );
			unset( self::$vault[ $id ] );
		}
	}
}
