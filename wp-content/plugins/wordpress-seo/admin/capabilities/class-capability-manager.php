<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Admin\Capabilities
 */

/**
 * Capability Manager interface.
 */
interface WPSEO_Capability_Manager {

	/**
	 * Registers a capability.
	 *
	 * @param string $capability Capability to register.
	 * @param array  $roles      Roles to add the capability to.
	 * @param bool   $overwrite  Optional. Use add or overwrite as registration method.
	 *
	 * @return void
	 */
	public function register( $capability, array $roles, $overwrite = false );

	/**
	 * Adds the registerd capabilities to the system.
	 *
	 * @return void
	 */
	public function add();

	/**
	 * Removes the registered capabilities from the system.
	 *
	 * @return void
	 */
	public function remove();

	/**
	 * Returns the list of registered capabilities.
	 *
	 * @return string[] List of registered capabilities.
	 */
	public function get_capabilities();
}
