<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Admin\Roles
 */

/**
 * Role registration class.
 */
class WPSEO_Register_Roles implements WPSEO_WordPress_Integration {

	/**
	 * Adds hooks.
	 *
	 * @return void
	 */
	public function register_hooks() {
		add_action( 'wpseo_register_roles', [ $this, 'register' ] );
	}

	/**
	 * Registers the roles.
	 *
	 * @return void
	 */
	public function register() {
		$role_manager = WPSEO_Role_Manager_Factory::get();

		$role_manager->register( 'wpseo_manager', 'SEO Manager', 'editor' );
		$role_manager->register( 'wpseo_editor', 'SEO Editor', 'editor' );
	}
}
