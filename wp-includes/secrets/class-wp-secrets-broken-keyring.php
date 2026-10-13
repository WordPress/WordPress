<?php
/**
 * Secrets API: WP_Secrets_Broken_Keyring class
 *
 * @package WordPress
 * @subpackage Secrets
 * @since 7.2.0
 */

/**
 * Stands in for the keyring when a drop-in leaves an invalid value behind.
 *
 * Installed when a secrets.php drop-in exists but $GLOBALS['wp_secrets_keyring'] does
 * not hold a WP_Secrets_Keyring.
 *
 * Same reasoning as WP_Secrets_Broken_Store: the drop-in's presence signals the
 * operator wants a keyring other than the default, so silently falling back to
 * WP_Secrets_Config_Key_Provider would wrap the root key under a key the operator
 * did not choose. Every operation fails closed instead.
 *
 * @since 7.2.0
 */
final class WP_Secrets_Broken_Keyring implements WP_Secrets_Keyring {

	/**
	 * Always fails closed.
	 *
	 * @since 7.2.0
	 *
	 * @param string $key_material Ignored.
	 * @return WP_Error
	 */
	public function wrap( string $key_material ): WP_Error {
		return $this->error();
	}

	/**
	 * Always fails closed.
	 *
	 * @since 7.2.0
	 *
	 * @param string $wrapped Ignored.
	 * @return WP_Error
	 */
	public function unwrap( string $wrapped ): WP_Error {
		return $this->error();
	}

	/**
	 * Describes the broken state, for Site Health.
	 *
	 * @since 7.2.0
	 *
	 * @return string
	 */
	public function get_key_source(): string {
		return __( 'broken secrets.php drop-in' );
	}

	/**
	 * Builds the error every method returns.
	 *
	 * @since 7.2.0
	 *
	 * @return WP_Error
	 */
	private function error(): WP_Error {
		return new WP_Error(
			WP_SECRETS_ERROR_KEY_UNAVAILABLE,
			__( 'The secrets.php drop-in did not provide a usable keyring.' )
		);
	}
}
