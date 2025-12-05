<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Admin\Capabilities
 */

/**
 * Capabilities registration class.
 */
class WPSEO_Register_Capabilities implements WPSEO_WordPress_Integration {

	/**
	 * Registers the hooks.
	 *
	 * @return void
	 */
	public function register_hooks() {
		add_action( 'wpseo_register_capabilities', [ $this, 'register' ] );

		if ( is_multisite() ) {
			add_action( 'user_has_cap', [ $this, 'filter_user_has_wpseo_manage_options_cap' ], 10, 4 );
		}

		/**
		 * Maybe add manage_privacy_options capability for wpseo_manager user role.
		 */
		add_filter( 'map_meta_cap', [ $this, 'map_meta_cap_for_seo_manager' ], 10, 2 );
	}

	/**
	 * Registers the capabilities.
	 *
	 * @return void
	 */
	public function register() {
		$manager = WPSEO_Capability_Manager_Factory::get();

		$manager->register( 'wpseo_bulk_edit', [ 'editor', 'wpseo_editor', 'wpseo_manager' ] );
		$manager->register( 'wpseo_edit_advanced_metadata', [ 'editor', 'wpseo_editor', 'wpseo_manager' ] );

		$manager->register( 'wpseo_manage_options', [ 'administrator', 'wpseo_manager' ] );
		$manager->register( 'view_site_health_checks', [ 'wpseo_manager' ] );
	}

	/**
	 * Revokes the 'wpseo_manage_options' capability from administrator users if it should
	 * only be granted to network administrators.
	 *
	 * @param array   $allcaps An array of all the user's capabilities.
	 * @param array   $caps    Actual capabilities being checked.
	 * @param array   $args    Optional parameters passed to has_cap(), typically object ID.
	 * @param WP_User $user    The user object.
	 *
	 * @return array Possibly modified array of the user's capabilities.
	 */
	public function filter_user_has_wpseo_manage_options_cap( $allcaps, $caps, $args, $user ) {

		// We only need to do something if 'wpseo_manage_options' is being checked.
		if ( ! in_array( 'wpseo_manage_options', $caps, true ) ) {
			return $allcaps;
		}

		// If the user does not have 'wpseo_manage_options' anyway, we don't need to revoke access.
		if ( empty( $allcaps['wpseo_manage_options'] ) ) {
			return $allcaps;
		}

		// If the user does not have 'delete_users', they are not an administrator.
		if ( empty( $allcaps['delete_users'] ) ) {
			return $allcaps;
		}

		$options = WPSEO_Options::get_instance();

		if ( $options->get( 'access' ) === 'superadmin' && ! is_super_admin( $user->ID ) ) {
			unset( $allcaps['wpseo_manage_options'] );
		}

		return $allcaps;
	}

	/**
	 * Maybe add manage_privacy_options capability for wpseo_manager user role.
	 *
	 * @param string[] $caps Primitive capabilities required of the user.
	 * @param string[] $cap  Capability being checked.
	 *
	 * @return string[] Filtered primitive capabilities required of the user.
	 */
	public function map_meta_cap_for_seo_manager( $caps, $cap ) {
		$user = wp_get_current_user();

		// No multisite support.
		if ( is_multisite() ) {
			return $caps;
		}

		if ( ! is_array( $user->roles ) ) {
			return $caps;
		}

		// User must be of role wpseo_manager.
		if ( ! in_array( 'wpseo_manager', $user->roles, true ) ) {
			return $caps;
		}

		// Remove manage_options cap requirement if requested cap is manage_privacy_options.
		if ( $cap === 'manage_privacy_options' ) {
			return array_diff( $caps, [ 'manage_options' ] );
		}

		return $caps;
	}
}
