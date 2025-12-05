<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Admin\Roles
 */

/**
 * WordPress' default implementation of the Role Manager.
 */
final class WPSEO_Role_Manager_WP extends WPSEO_Abstract_Role_Manager {

	/**
	 * Adds a role to the system.
	 *
	 * @param string $role         Role to add.
	 * @param string $display_name Name to display for the role.
	 * @param array  $capabilities Capabilities to add to the role.
	 *
	 * @return void
	 */
	protected function add_role( $role, $display_name, array $capabilities = [] ) {
		$wp_role = get_role( $role );
		if ( $wp_role ) {
			foreach ( $capabilities as $capability => $grant ) {
				$wp_role->add_cap( $capability, $grant );
			}

			return;
		}

		add_role( $role, $display_name, $capabilities );
	}

	/**
	 * Removes a role from the system.
	 *
	 * @param string $role Role to remove.
	 *
	 * @return void
	 */
	protected function remove_role( $role ) {
		remove_role( $role );
	}

	/**
	 * Formats the capabilities to the required format.
	 *
	 * @param array $capabilities Capabilities to format.
	 * @param bool  $enabled      Whether these capabilities should be enabled or not.
	 *
	 * @return array Formatted capabilities.
	 */
	protected function format_capabilities( array $capabilities, $enabled = true ) {
		// Flip keys and values.
		$capabilities = array_flip( $capabilities );

		// Set all values to $enabled.
		return array_fill_keys( array_keys( $capabilities ), $enabled );
	}
}
