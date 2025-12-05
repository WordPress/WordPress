<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Admin\Capabilities
 */

/**
 * VIP implementation of the Capability Manager.
 */
final class WPSEO_Capability_Manager_VIP extends WPSEO_Abstract_Capability_Manager {

	/**
	 * Adds the registered capabilities to the system.
	 *
	 * @return void
	 */
	public function add() {
		$role_capabilities = [];
		foreach ( $this->capabilities as $capability => $roles ) {
			$role_capabilities = $this->get_role_capabilities( $role_capabilities, $capability, $roles );
		}

		foreach ( $role_capabilities as $role => $capabilities ) {
			wpcom_vip_add_role_caps( $role, $capabilities );
		}
	}

	/**
	 * Removes the registered capabilities from the system
	 *
	 * @return void
	 */
	public function remove() {
		// Remove from any role it has been added to.
		$roles = wp_roles()->get_names();
		$roles = array_keys( $roles );

		$role_capabilities = [];
		foreach ( array_keys( $this->capabilities ) as $capability ) {
			// Allow filtering of roles.
			$role_capabilities = $this->get_role_capabilities( $role_capabilities, $capability, $roles );
		}

		foreach ( $role_capabilities as $role => $capabilities ) {
			wpcom_vip_remove_role_caps( $role, $capabilities );
		}
	}

	/**
	 * Returns the roles which the capability is registered on.
	 *
	 * @param array  $role_capabilities List of all roles with their capabilities.
	 * @param string $capability        Capability to filter roles for.
	 * @param array  $roles             List of default roles.
	 *
	 * @return array List of capabilities.
	 */
	protected function get_role_capabilities( $role_capabilities, $capability, $roles ) {
		// Allow filtering of roles.
		$filtered_roles = $this->filter_roles( $capability, $roles );

		foreach ( $filtered_roles as $role ) {
			if ( ! isset( $add_role_caps[ $role ] ) ) {
				$role_capabilities[ $role ] = [];
			}

			$role_capabilities[ $role ][] = $capability;
		}

		return $role_capabilities;
	}
}
