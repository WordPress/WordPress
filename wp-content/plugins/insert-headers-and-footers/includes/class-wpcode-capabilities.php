<?php
/**
 * Manage custom capabilities for WPCode.
 *
 * @package WPCode
 */

/**
 * The WPCode_Capabilities class.
 */
class WPCode_Capabilities {
	/**
	 * Function to call on plugin activation.
	 *
	 * @return void
	 */
	public static function add_capabilities() {

		foreach ( self::get_roles() as $role ) {
			if ( $role->has_cap( 'manage_options' ) ) {
				$role->add_cap( 'wpcode_edit_snippets' );
				$role->add_cap( 'wpcode_activate_snippets' );
			}
		}

	}

	/**
	 * Get roles as WP_Role objects.
	 *
	 * @return WP_Role[]
	 */
	public static function get_roles() {
		$roles      = wp_roles()->roles;
		$role_array = array();
		foreach ( array_keys( $roles ) as $role_key ) {
			$role_array[] = get_role( $role_key );
		}

		return $role_array;
	}

	/**
	 * Remove custom capabilities.
	 *
	 * @return void
	 */
	public static function uninstall() {
		foreach ( self::get_roles() as $role ) {
			if ( $role->has_cap( 'wpcode_edit_snippets' ) ) {
				$role->remove_cap( 'wpcode_edit_snippets' );
			}
			if ( $role->has_cap( 'wpcode_activate_snippets' ) ) {
				$role->remove_cap( 'wpcode_activate_snippets' );
			}
		}
	}
}
