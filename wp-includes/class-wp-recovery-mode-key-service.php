<?php
/**
 * Error Protection API: WP_Recovery_Mode_Key_service class
 *
 * @package WordPress
 * @since   5.2.0
 */

/**
 * Core class used to generate and validate keys used to enter Recovery Mode.
 *
 * @since 5.2.0
 */
final class WP_Recovery_Mode_Key_Service {

	/**
	 * Creates a recovery mode key.
	 *
	 * @since 5.2.0
	 *
	 * @global PasswordHash $wp_hasher
	 *
	 * @return string Recovery mode key.
	 */
	public function generate_and_store_recovery_mode_key() {

		global $wp_hasher;

		$key = wp_generate_password( 22, false );

		/**
		 * Fires when a recovery mode key is generated for a user.
		 *
		 * @since 5.2.0
		 *
		 * @param string $key The recovery mode key.
		 */
		do_action( 'generate_recovery_mode_key', $key );

		if ( empty( $wp_hasher ) ) {
			require_once ABSPATH . WPINC . '/class-phpass.php';
			$wp_hasher = new PasswordHash( 8, true );
		}

		$hashed = $wp_hasher->HashPassword( $key );

		update_option(
			'recovery_key',
			array(
				'hashed_key' => $hashed,
				'created_at' => time(),
			)
		);

		return $key;
	}

	/**
	 * Verifies if the recovery mode key is correct.
	 *
	 * @since 5.2.0
	 *
	 * @param string $key The unhashed key.
	 * @param int    $ttl Time in seconds for the key to be valid for.
	 * @return true|WP_Error True on success, error object on failure.
	 */
	public function validate_recovery_mode_key( $key, $ttl ) {

		$record = get_option( 'recovery_key' );

		if ( ! $record ) {
			return new WP_Error( 'no_recovery_key_set', __( 'Recovery Mode not initialized.' ) );
		}

		if ( ! is_array( $record ) || ! isset( $record['hashed_key'], $record['created_at'] ) ) {
			return new WP_Error( 'invalid_recovery_key_format', __( 'Invalid recovery key format.' ) );
		}

		if ( ! wp_check_password( $key, $record['hashed_key'] ) ) {
			return new WP_Error( 'hash_mismatch', __( 'Invalid recovery key.' ) );
		}

		if ( time() > $record['created_at'] + $ttl ) {
			return new WP_Error( 'key_expired', __( 'Recovery key expired.' ) );
		}

		return true;
	}
}
