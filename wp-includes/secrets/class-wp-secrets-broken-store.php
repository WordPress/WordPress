<?php
/**
 * Secrets API: WP_Secrets_Broken_Store class
 *
 * @package WordPress
 * @subpackage Secrets
 * @since 7.2.0
 */

/**
 * Stands in for the store when a drop-in leaves an invalid value behind.
 *
 * Installed when a secrets.php drop-in exists but $GLOBALS['wp_secrets_store'] does
 * not hold a WP_Secrets_Store.
 *
 * The drop-in's presence signals the operator wants storage other than the
 * default – falling back to WP_Secrets_Option_Store here would silently write
 * secrets to local options against that intent, which is exactly the silent
 * downgrade to local storage this API refuses to make. Every operation fails
 * closed instead.
 *
 * @since 7.2.0
 */
final class WP_Secrets_Broken_Store implements WP_Secrets_Store {

	/**
	 * Always fails closed.
	 *
	 * @since 7.2.0
	 *
	 * @param string $name    Ignored.
	 * @param bool   $network Ignored.
	 * @return WP_Error
	 */
	public function get( string $name, bool $network = false ): WP_Error {
		return $this->error();
	}

	/**
	 * Always fails closed.
	 *
	 * @since 7.2.0
	 *
	 * @param string       $name    Ignored.
	 * @param array<mixed> $record  Ignored.
	 * @param bool         $network Ignored.
	 * @return WP_Error
	 */
	public function set( string $name, array $record, bool $network = false ): WP_Error {
		return $this->error();
	}

	/**
	 * Always fails closed.
	 *
	 * @since 7.2.0
	 *
	 * @param string $name    Ignored.
	 * @param bool   $network Ignored.
	 * @return WP_Error
	 */
	public function delete( string $name, bool $network = false ): WP_Error {
		return $this->error();
	}

	/**
	 * Always fails closed.
	 *
	 * @since 7.2.0
	 *
	 * @param bool $network Ignored.
	 * @return WP_Error
	 */
	public function list_names( bool $network = false ): WP_Error {
		return $this->error();
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
			WP_SECRETS_ERROR_STORE_UNAVAILABLE,
			__( 'The secrets.php drop-in did not provide a usable store.' )
		);
	}
}
