<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Admin\Roles
 */

/**
 * Role Manager interface.
 */
interface WPSEO_Role_Manager {

	/**
	 * Registers a role.
	 *
	 * @param string      $role         Role to register.
	 * @param string      $display_name Display name to use.
	 * @param string|null $template     Optional. Role to base the new role on.
	 *
	 * @return void
	 */
	public function register( $role, $display_name, $template = null );

	/**
	 * Adds the registered roles.
	 *
	 * @return void
	 */
	public function add();

	/**
	 * Removes the registered roles.
	 *
	 * @return void
	 */
	public function remove();

	/**
	 * Returns the list of registered roles.
	 *
	 * @return string[] List or registered roles.
	 */
	public function get_roles();
}
