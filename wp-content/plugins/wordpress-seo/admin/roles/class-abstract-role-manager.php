<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Admin\Roles
 */

/**
 * Abstract Role Manager template.
 */
abstract class WPSEO_Abstract_Role_Manager implements WPSEO_Role_Manager {

	/**
	 * Registered roles.
	 *
	 * @var array
	 */
	protected $roles = [];

	/**
	 * Registers a role.
	 *
	 * @param string      $role         Role to register.
	 * @param string      $display_name Display name to use.
	 * @param string|null $template     Optional. Role to base the new role on.
	 *
	 * @return void
	 */
	public function register( $role, $display_name, $template = null ) {
		$this->roles[ $role ] = (object) [
			'display_name' => $display_name,
			'template'     => $template,
		];
	}

	/**
	 * Returns the list of registered roles.
	 *
	 * @return string[] List or registered roles.
	 */
	public function get_roles() {
		return array_keys( $this->roles );
	}

	/**
	 * Adds the registered roles.
	 *
	 * @return void
	 */
	public function add() {
		foreach ( $this->roles as $role => $data ) {
			$capabilities = $this->get_capabilities( $data->template );
			$capabilities = $this->filter_existing_capabilties( $role, $capabilities );

			$this->add_role( $role, $data->display_name, $capabilities );
		}
	}

	/**
	 * Removes the registered roles.
	 *
	 * @return void
	 */
	public function remove() {
		$roles = array_keys( $this->roles );
		array_map( [ $this, 'remove_role' ], $roles );
	}

	/**
	 * Returns the capabilities for the specified role.
	 *
	 * @param string $role Role to fetch capabilities from.
	 *
	 * @return array List of capabilities.
	 */
	protected function get_capabilities( $role ) {
		if ( ! is_string( $role ) || empty( $role ) ) {
			return [];
		}

		$wp_role = get_role( $role );
		if ( ! $wp_role ) {
			return [];
		}

		return $wp_role->capabilities;
	}

	/**
	 * Returns true if the capability exists on the role.
	 *
	 * @param WP_Role $role       Role to check capability against.
	 * @param string  $capability Capability to check.
	 *
	 * @return bool True if the capability is defined for the role.
	 */
	protected function capability_exists( WP_Role $role, $capability ) {
		return ! array_key_exists( $capability, $role->capabilities );
	}

	/**
	 * Filters out capabilities that are already set for the role.
	 *
	 * This makes sure we don't override configurations that have been previously set.
	 *
	 * @param string $role         The role to check against.
	 * @param array  $capabilities The capabilities that should be set.
	 *
	 * @return array Capabilties that can be safely set.
	 */
	protected function filter_existing_capabilties( $role, array $capabilities ) {
		if ( $capabilities === [] ) {
			return $capabilities;
		}

		$wp_role = get_role( $role );
		if ( ! $wp_role ) {
			return $capabilities;
		}

		foreach ( $capabilities as $capability => $grant ) {
			if ( $this->capability_exists( $wp_role, $capability ) ) {
				unset( $capabilities[ $capability ] );
			}
		}

		return $capabilities;
	}

	/**
	 * Adds a role to the system.
	 *
	 * @param string $role         Role to add.
	 * @param string $display_name Name to display for the role.
	 * @param array  $capabilities Capabilities to add to the role.
	 *
	 * @return void
	 */
	abstract protected function add_role( $role, $display_name, array $capabilities = [] );

	/**
	 * Removes a role from the system.
	 *
	 * @param string $role Role to remove.
	 *
	 * @return void
	 */
	abstract protected function remove_role( $role );
}
