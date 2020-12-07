<?php
/**
 * WP_Application_Passwords class
 *
 * @package WordPress
 * @since   5.6.0
 */

/**
 * Class for displaying, modifying, and sanitizing application passwords.
 *
 * @package WordPress
 */
class WP_Application_Passwords {

	/**
	 * The application passwords user meta key.
	 *
	 * @since 5.6.0
	 *
	 * @type string
	 */
	const USERMETA_KEY_APPLICATION_PASSWORDS = '_application_passwords';

	/**
	 * The option name used to store whether application passwords is in use.
	 *
	 * @since 5.6.0
	 *
	 * @type string
	 */
	const OPTION_KEY_IN_USE = 'using_application_passwords';

	/**
	 * The generated application password length.
	 *
	 * @since 5.6.0
	 *
	 * @type int
	 */
	const PW_LENGTH = 24;

	/**
	 * Checks if Application Passwords are being used by the site.
	 *
	 * This returns true if at least one App Password has ever been created.
	 *
	 * @since 5.6.0
	 *
	 * @return bool
	 */
	public static function is_in_use() {
		$network_id = get_main_network_id();
		return (bool) get_network_option( $network_id, self::OPTION_KEY_IN_USE );
	}

	/**
	 * Creates a new application password.
	 *
	 * @since 5.6.0
	 *
	 * @param int   $user_id  User ID.
	 * @param array $args     Information about the application password.
	 * @return array|WP_Error The first key in the array is the new password, the second is its detailed information.
	 *                        A WP_Error instance is returned on error.
	 */
	public static function create_new_application_password( $user_id, $args = array() ) {
		if ( empty( $args['name'] ) ) {
			return new WP_Error( 'application_password_empty_name', __( 'An application name is required to create an application password.' ) );
		}

		$new_password    = wp_generate_password( static::PW_LENGTH, false );
		$hashed_password = wp_hash_password( $new_password );

		$new_item = array(
			'uuid'      => wp_generate_uuid4(),
			'app_id'    => empty( $args['app_id'] ) ? '' : $args['app_id'],
			'name'      => $args['name'],
			'password'  => $hashed_password,
			'created'   => time(),
			'last_used' => null,
			'last_ip'   => null,
		);

		$passwords   = static::get_user_application_passwords( $user_id );
		$passwords[] = $new_item;
		$saved       = static::set_user_application_passwords( $user_id, $passwords );

		if ( ! $saved ) {
			return new WP_Error( 'db_error', __( 'Could not save application password.' ) );
		}

		$network_id = get_main_network_id();
		if ( ! get_network_option( $network_id, self::OPTION_KEY_IN_USE ) ) {
			update_network_option( $network_id, self::OPTION_KEY_IN_USE, true );
		}

		/**
		 * Fires when an application password is created.
		 *
		 * @since 5.6.0
		 *
		 * @param int    $user_id      The user ID.
		 * @param array  $new_item     The details about the created password.
		 * @param string $new_password The unhashed generated app password.
		 * @param array  $args         Information used to create the application password.
		 */
		do_action( 'wp_create_application_password', $user_id, $new_item, $new_password, $args );

		return array( $new_password, $new_item );
	}

	/**
	 * Gets a user's application passwords.
	 *
	 * @since 5.6.0
	 *
	 * @param int $user_id User ID.
	 * @return array The list of app passwords.
	 */
	public static function get_user_application_passwords( $user_id ) {
		$passwords = get_user_meta( $user_id, static::USERMETA_KEY_APPLICATION_PASSWORDS, true );

		if ( ! is_array( $passwords ) ) {
			return array();
		}

		$save = false;

		foreach ( $passwords as $i => $password ) {
			if ( ! isset( $password['uuid'] ) ) {
				$passwords[ $i ]['uuid'] = wp_generate_uuid4();
				$save                    = true;
			}
		}

		if ( $save ) {
			static::set_user_application_passwords( $user_id, $passwords );
		}

		return $passwords;
	}

	/**
	 * Gets a user's application password with the given uuid.
	 *
	 * @since 5.6.0
	 *
	 * @param int    $user_id User ID.
	 * @param string $uuid    The password's uuid.
	 * @return array|null The application password if found, null otherwise.
	 */
	public static function get_user_application_password( $user_id, $uuid ) {
		$passwords = static::get_user_application_passwords( $user_id );

		foreach ( $passwords as $password ) {
			if ( $password['uuid'] === $uuid ) {
				return $password;
			}
		}

		return null;
	}

	/**
	 * Updates an application password.
	 *
	 * @since 5.6.0
	 *
	 * @param int    $user_id User ID.
	 * @param string $uuid    The password's uuid.
	 * @param array  $update  Information about the application password to update.
	 * @return true|WP_Error True if successful, otherwise a WP_Error instance is returned on error.
	 */
	public static function update_application_password( $user_id, $uuid, $update = array() ) {
		$passwords = static::get_user_application_passwords( $user_id );

		foreach ( $passwords as &$item ) {
			if ( $item['uuid'] !== $uuid ) {
				continue;
			}

			$save = false;

			if ( ! empty( $update['name'] ) && $item['name'] !== $update['name'] ) {
				$item['name'] = $update['name'];
				$save         = true;
			}

			if ( $save ) {
				$saved = static::set_user_application_passwords( $user_id, $passwords );

				if ( ! $saved ) {
					return new WP_Error( 'db_error', __( 'Could not save application password.' ) );
				}
			}

			/**
			 * Fires when an application password is updated.
			 *
			 * @since 5.6.0
			 *
			 * @param int   $user_id The user ID.
			 * @param array $item    The updated app password details.
			 * @param array $update  The information to update.
			 */
			do_action( 'wp_update_application_password', $user_id, $item, $update );

			return true;
		}

		return new WP_Error( 'application_password_not_found', __( 'Could not find an application password with that id.' ) );
	}

	/**
	 * Records that an application password has been used.
	 *
	 * @since 5.6.0
	 *
	 * @param int    $user_id User ID.
	 * @param string $uuid    The password's uuid.
	 * @return true|WP_Error True if the usage was recorded, a WP_Error if an error occurs.
	 */
	public static function record_application_password_usage( $user_id, $uuid ) {
		$passwords = static::get_user_application_passwords( $user_id );

		foreach ( $passwords as &$password ) {
			if ( $password['uuid'] !== $uuid ) {
				continue;
			}

			// Only record activity once a day.
			if ( $password['last_used'] + DAY_IN_SECONDS > time() ) {
				return true;
			}

			$password['last_used'] = time();
			$password['last_ip']   = $_SERVER['REMOTE_ADDR'];

			$saved = static::set_user_application_passwords( $user_id, $passwords );

			if ( ! $saved ) {
				return new WP_Error( 'db_error', __( 'Could not save application password.' ) );
			}

			return true;
		}

		// Specified Application Password not found!
		return new WP_Error( 'application_password_not_found', __( 'Could not find an application password with that id.' ) );
	}

	/**
	 * Deletes an application password.
	 *
	 * @since 5.6.0
	 *
	 * @param int    $user_id User ID.
	 * @param string $uuid    The password's uuid.
	 * @return true|WP_Error Whether the password was successfully found and deleted, a WP_Error otherwise.
	 */
	public static function delete_application_password( $user_id, $uuid ) {
		$passwords = static::get_user_application_passwords( $user_id );

		foreach ( $passwords as $key => $item ) {
			if ( $item['uuid'] === $uuid ) {
				unset( $passwords[ $key ] );
				$saved = static::set_user_application_passwords( $user_id, $passwords );

				if ( ! $saved ) {
					return new WP_Error( 'db_error', __( 'Could not delete application password.' ) );
				}

				/**
				 * Fires when an application password is deleted.
				 *
				 * @since 5.6.0
				 *
				 * @param int   $user_id The user ID.
				 * @param array $item    The data about the application password.
				 */
				do_action( 'wp_delete_application_password', $user_id, $item );

				return true;
			}
		}

		return new WP_Error( 'application_password_not_found', __( 'Could not find an application password with that id.' ) );
	}

	/**
	 * Deletes all application passwords for the given user.
	 *
	 * @since 5.6.0
	 *
	 * @param int $user_id User ID.
	 * @return int|WP_Error The number of passwords that were deleted or a WP_Error on failure.
	 */
	public static function delete_all_application_passwords( $user_id ) {
		$passwords = static::get_user_application_passwords( $user_id );

		if ( $passwords ) {
			$saved = static::set_user_application_passwords( $user_id, array() );

			if ( ! $saved ) {
				return new WP_Error( 'db_error', __( 'Could not delete application passwords.' ) );
			}

			foreach ( $passwords as $item ) {
				/** This action is documented in wp-includes/class-wp-application-passwords.php */
				do_action( 'wp_delete_application_password', $user_id, $item );
			}

			return count( $passwords );
		}

		return 0;
	}

	/**
	 * Sets a users application passwords.
	 *
	 * @since 5.6.0
	 *
	 * @param int   $user_id   User ID.
	 * @param array $passwords Application passwords.
	 *
	 * @return bool
	 */
	protected static function set_user_application_passwords( $user_id, $passwords ) {
		return update_user_meta( $user_id, static::USERMETA_KEY_APPLICATION_PASSWORDS, $passwords );
	}

	/**
	 * Sanitizes and then splits a password into smaller chunks.
	 *
	 * @since 5.6.0
	 *
	 * @param string $raw_password The raw application password.
	 * @return string The chunked password.
	 */
	public static function chunk_password( $raw_password ) {
		$raw_password = preg_replace( '/[^a-z\d]/i', '', $raw_password );

		return trim( chunk_split( $raw_password, 4, ' ' ) );
	}
}
