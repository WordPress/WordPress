<?php
/**
 * Secrets API: WP_Secrets_Broken_Provider class
 *
 * @package WordPress
 * @subpackage Secrets
 * @since 7.2.0
 */

/**
 * The provider installed when a secrets.php drop-in did not load correctly.
 *
 * Every operation returns WP_Error. It exists so that a broken drop-in cannot be
 * mistaken for a working site with no secrets in it yet – which is the failure
 * mode that turns "my credential backend is misconfigured" into "my credentials
 * appear to have been deleted," and is precisely what this API's three-state return
 * exists to prevent.
 *
 * Deliberately not a fallback to the default provider. Reverting to local storage
 * because a host's KMS drop-in failed would quietly downgrade a site's protection
 * at the exact moment nobody is watching.
 *
 * @since 7.2.0
 */
final class WP_Secrets_Broken_Provider implements WP_Secrets_Provider {

	/**
	 * The single error every operation reports.
	 *
	 * @since 7.2.0
	 *
	 * @return WP_Error
	 */
	private function error(): WP_Error {
		return new WP_Error(
			WP_SECRETS_ERROR_STORE_UNAVAILABLE,
			__( 'The secrets.php drop-in did not load correctly, so no secret can be read or written. Fix or remove the drop-in.' )
		);
	}

	/**
	 * Always an error.
	 *
	 * @since 7.2.0
	 *
	 * @param string $name    The secret's name.
	 * @param string $version A WP_Secret_Version constant.
	 * @param bool   $network Whether this is a network-scope secret.
	 * @return WP_Error
	 *
	 * @phpstan-param WP_Secret_Version::CURRENT|WP_Secret_Version::PREVIOUS $version
	 */
	public function get( string $name, string $version, bool $network = false ): WP_Error {
		return $this->error();
	}

	/**
	 * Always an error.
	 *
	 * @since 7.2.0
	 *
	 * @param string      $name           The secret's name.
	 * @param string      $value          The plaintext value.
	 * @param bool        $network        Whether this is a network-scope secret.
	 * @param bool        $needs_rotation Ignored.
	 * @param string|null $action         Ignored.
	 * @return WP_Error
	 */
	public function set( string $name, $value, bool $network = false, bool $needs_rotation = false, ?string $action = null ): WP_Error {
		return $this->error();
	}

	/**
	 * Always an error.
	 *
	 * @since 7.2.0
	 *
	 * @param string $name    The secret's name.
	 * @param bool   $network Whether this is a network-scope secret.
	 * @return WP_Error
	 */
	public function delete( string $name, bool $network = false ): WP_Error {
		return $this->error();
	}

	/**
	 * Always an error.
	 *
	 * @since 7.2.0
	 *
	 * @param string $name    The secret's name.
	 * @param bool   $network Whether this is a network-scope secret.
	 * @return WP_Error
	 */
	public function retire_previous( string $name, bool $network = false ): WP_Error {
		return $this->error();
	}

	/**
	 * Always an error.
	 *
	 * @since 7.2.0
	 *
	 * @param string $name_prefix Restrict to names beginning with this prefix.
	 * @param bool   $network     Whether to list network-scope secrets.
	 * @return WP_Error
	 */
	public function list_secrets( string $name_prefix = '', bool $network = false ): WP_Error {
		return $this->error();
	}

	/**
	 * Says plainly that the drop-in is the problem.
	 *
	 * @since 7.2.0
	 *
	 * @return string
	 */
	public function get_label(): string {
		return __( 'Unavailable: the secrets.php drop-in did not load correctly' );
	}

	/**
	 * Reports the provider boundary.
	 *
	 * Whatever was meant to protect these secrets is not WordPress, and is not
	 * working.
	 *
	 * @since 7.2.0
	 *
	 * @return string
	 *
	 * @phpstan-return self::BOUNDARY_*
	 */
	public function get_protection_boundary(): string {
		return self::BOUNDARY_PROVIDER;
	}

	/**
	 * Nothing is writable while the drop-in is broken.
	 *
	 * @since 7.2.0
	 *
	 * @return bool
	 */
	public function is_writable(): bool {
		return false;
	}
}
