<?php
/**
 * Secrets API: WP_Secrets_Option_Store class
 *
 * @package WordPress
 * @subpackage Secrets
 * @since 7.2.0
 */

/**
 * The default secret store.
 *
 * Keeps records as rows in the options tables, always with autoload disabled, and
 * always excluded from options.php and the REST settings endpoint.
 *
 * Site-scope secrets live under '_wp_secret_{name}' via get_option()/update_option().
 * Network-scope secrets live under '_wp_network_secret_{name}' via the *_site_option()
 * functions, which on a non-multisite install are themselves backed by wp_options –
 * so on a single site, site- and network-scope secrets differ only by prefix, in the
 * same table; on a real network, network-scope rows live in wp_sitemeta instead.
 *
 * @since 7.2.0
 */
final class WP_Secrets_Option_Store implements WP_Secrets_Store {

	/**
	 * Option name prefix for site-scope secrets.
	 *
	 * @since 7.2.0
	 * @var string
	 */
	const SITE_PREFIX = '_wp_secret_';

	/**
	 * Option name prefix for network-scope secrets.
	 *
	 * @since 7.2.0
	 * @var string
	 */
	const NETWORK_PREFIX = '_wp_network_secret_';

	/**
	 * Reads a secret's stored record.
	 *
	 * @since 7.2.0
	 *
	 * @param string $name    The secret's namespaced name.
	 * @param bool   $network Whether this is a network-scope secret.
	 * @return array<mixed>|null|WP_Error
	 */
	public function get( string $name, bool $network = false ) {
		$option_name = $this->option_name( $name, $network );
		$value       = $network ? get_site_option( $option_name, null ) : get_option( $option_name, null );

		if ( null === $value ) {
			return null;
		}

		if ( ! is_array( $value ) ) {
			return new WP_Error(
				WP_SECRETS_ERROR_RECORD_MALFORMED,
				__( 'The stored secret record is not an array.' )
			);
		}

		return $value;
	}

	/**
	 * Writes a secret's record.
	 *
	 * @since 7.2.0
	 *
	 * @param string       $name    The secret's namespaced name.
	 * @param array<mixed> $record  The record to store.
	 * @param bool         $network Whether this is a network-scope secret.
	 * @return true|WP_Error
	 */
	public function set( string $name, array $record, bool $network = false ) {
		$option_name = $this->option_name( $name, $network );

		if ( $network ) {
			$updated = update_site_option( $option_name, $record );
		} else {
			$updated = update_option( $option_name, $record, false );
		}

		if ( $updated ) {
			return true;
		}

		/*
		 * update_option()/update_site_option() return false both on genuine
		 * failure and when the new value already equals the stored one – a
		 * documented ambiguity in the Options API. Read back and compare rather
		 * than treating every false as a failure.
		 */
		$current = $network ? get_site_option( $option_name ) : get_option( $option_name );

		if ( $current === $record ) {
			return true;
		}

		return new WP_Error(
			WP_SECRETS_ERROR_STORE_UNAVAILABLE,
			__( 'The secret record could not be written.' )
		);
	}

	/**
	 * Deletes a secret's record.
	 *
	 * @since 7.2.0
	 *
	 * @param string $name    The secret's namespaced name.
	 * @param bool   $network Whether this is a network-scope secret.
	 * @return true|WP_Error
	 */
	public function delete( string $name, bool $network = false ) {
		$option_name = $this->option_name( $name, $network );
		$deleted     = $network ? delete_site_option( $option_name ) : delete_option( $option_name );

		if ( $deleted ) {
			return true;
		}

		// delete_*_option() also returns false when the option never existed.
		// Deleting something already absent is not a failure.
		$still_exists = null !== ( $network ? get_site_option( $option_name, null ) : get_option( $option_name, null ) );

		if ( ! $still_exists ) {
			return true;
		}

		return new WP_Error(
			WP_SECRETS_ERROR_STORE_UNAVAILABLE,
			__( 'The secret record could not be deleted.' )
		);
	}

	/**
	 * Lists the names of every secret in this store, for this scope.
	 *
	 * @since 7.2.0
	 *
	 * @global wpdb $wpdb WordPress database abstraction object.
	 *
	 * @param bool $network Whether to list network-scope secrets.
	 * @return string[]|WP_Error
	 */
	public function list_names( bool $network = false ) {
		global $wpdb;

		$prefix  = $network ? self::NETWORK_PREFIX : self::SITE_PREFIX;
		$pattern = $wpdb->esc_like( $prefix ) . '%';

		if ( $network && is_multisite() ) {
			$option_names = $wpdb->get_col(
				$wpdb->prepare(
					"SELECT meta_key FROM {$wpdb->sitemeta} WHERE meta_key LIKE %s AND site_id = %d",
					$pattern,
					get_current_network_id()
				)
			);
		} else {
			$option_names = $wpdb->get_col(
				$wpdb->prepare(
					"SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE %s",
					$pattern
				)
			);
		}

		if ( ! is_array( $option_names ) ) {
			return new WP_Error(
				WP_SECRETS_ERROR_STORE_UNAVAILABLE,
				__( 'Could not list secrets.' )
			);
		}

		$names = array();

		foreach ( $option_names as $option_name ) {
			if ( ! is_string( $option_name ) ) {
				continue;
			}

			$names[] = substr( $option_name, strlen( $prefix ) );
		}

		return $names;
	}


	/**
	 * Builds the option name a secret is stored under.
	 *
	 * @since 7.2.0
	 *
	 * @param string $name    The secret's namespaced name.
	 * @param bool   $network Whether this is a network-scope secret.
	 * @return string
	 */
	private function option_name( string $name, bool $network ): string {
		return ( $network ? self::NETWORK_PREFIX : self::SITE_PREFIX ) . $name;
	}
}
