<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Admin\Capabilities
 */

/**
 * Capability Utils collection.
 */
class WPSEO_Capability_Utils {

	/**
	 * Checks if the user has the proper capabilities.
	 *
	 * @param string $capability Capability to check.
	 *
	 * @return bool True if the user has the proper rights.
	 */
	public static function current_user_can( $capability ) {
		if ( $capability === 'wpseo_manage_options' ) {
			return self::has( $capability );
		}

		return self::has_any( [ 'wpseo_manage_options', $capability ] );
	}

	/**
	 * Retrieves the users that have the specified capability.
	 *
	 * @param string $capability The name of the capability.
	 *
	 * @return array The users that have the capability.
	 */
	public static function get_applicable_users( $capability ) {
		$applicable_roles = self::get_applicable_roles( $capability );

		if ( $applicable_roles === [] ) {
			return [];
		}

		return get_users( [ 'role__in' => $applicable_roles ] );
	}

	/**
	 * Retrieves the roles that have the specified capability.
	 *
	 * @param string $capability The name of the capability.
	 *
	 * @return array The names of the roles that have the capability.
	 */
	public static function get_applicable_roles( $capability ) {
		$roles      = wp_roles();
		$role_names = $roles->get_names();

		$applicable_roles = [];
		foreach ( array_keys( $role_names ) as $role_name ) {
			$role = $roles->get_role( $role_name );

			if ( ! $role ) {
				continue;
			}

			// Add role if it has the capability.
			if ( array_key_exists( $capability, $role->capabilities ) && $role->capabilities[ $capability ] === true ) {
				$applicable_roles[] = $role_name;
			}
		}

		return $applicable_roles;
	}

	/**
	 * Checks if the current user has at least one of the supplied capabilities.
	 *
	 * @param array $capabilities Capabilities to check against.
	 *
	 * @return bool True if the user has at least one capability.
	 */
	protected static function has_any( array $capabilities ) {
		foreach ( $capabilities as $capability ) {
			if ( self::has( $capability ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Checks if the user has a certain capability.
	 *
	 * @param string $capability Capability to check against.
	 *
	 * @return bool True if the user has the capability.
	 */
	protected static function has( $capability ) {
		return current_user_can( $capability );
	}
}
