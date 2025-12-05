<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Admin\Capabilities
 */

/**
 * Abstract Capability Manager shared code.
 */
abstract class WPSEO_Abstract_Capability_Manager implements WPSEO_Capability_Manager {

	/**
	 * Registered capabilities.
	 *
	 * @var array
	 */
	protected $capabilities = [];

	/**
	 * Registers a capability.
	 *
	 * @param string $capability Capability to register.
	 * @param array  $roles      Roles to add the capability to.
	 * @param bool   $overwrite  Optional. Use add or overwrite as registration method.
	 *
	 * @return void
	 */
	public function register( $capability, array $roles, $overwrite = false ) {
		if ( $overwrite || ! isset( $this->capabilities[ $capability ] ) ) {
			$this->capabilities[ $capability ] = $roles;

			return;
		}

		// Combine configurations.
		$this->capabilities[ $capability ] = array_merge( $roles, $this->capabilities[ $capability ] );

		// Remove doubles.
		$this->capabilities[ $capability ] = array_unique( $this->capabilities[ $capability ] );
	}

	/**
	 * Returns the list of registered capabilitities.
	 *
	 * @return string[] Registered capabilities.
	 */
	public function get_capabilities() {
		return array_keys( $this->capabilities );
	}

	/**
	 * Returns a list of WP_Role roles.
	 *
	 * The string array of role names are converted to actual WP_Role objects.
	 * These are needed to be able to use the API on them.
	 *
	 * @param array $roles Roles to retrieve the objects for.
	 *
	 * @return WP_Role[] List of WP_Role objects.
	 */
	protected function get_wp_roles( array $roles ) {
		$wp_roles = array_map( 'get_role', $roles );

		return array_filter( $wp_roles );
	}

	/**
	 * Filter capability roles.
	 *
	 * @param string $capability Capability to filter roles for.
	 * @param array  $roles      List of roles which can be filtered.
	 *
	 * @return array Filtered list of roles for the capability.
	 */
	protected function filter_roles( $capability, array $roles ) {
		/**
		 * Filter: Allow changing roles that a capability is added to.
		 *
		 * @param array $roles The default roles to be filtered.
		 */
		$filtered = apply_filters( $capability . '_roles', $roles );

		// Make sure we have the expected type.
		if ( ! is_array( $filtered ) ) {
			return [];
		}

		return $filtered;
	}
}
