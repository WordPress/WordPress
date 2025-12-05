<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Admin\Roles
 */

/**
 * Role Manager Factory.
 */
class WPSEO_Role_Manager_Factory {

	/**
	 * Retrieves the Role manager to use.
	 *
	 * @return WPSEO_Role_Manager
	 */
	public static function get() {
		static $manager = null;

		if ( $manager === null ) {
			$manager = new WPSEO_Role_Manager_WP();
		}

		return $manager;
	}
}
