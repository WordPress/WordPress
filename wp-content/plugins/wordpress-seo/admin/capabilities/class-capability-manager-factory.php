<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Admin\Capabilities
 */

/**
 * Capability Manager Factory.
 */
class WPSEO_Capability_Manager_Factory {

	/**
	 * Returns the Manager to use.
	 *
	 * @param string $plugin_type Whether it's Free or Premium.
	 *
	 * @return WPSEO_Capability_Manager Manager to use.
	 */
	public static function get( $plugin_type = 'free' ) {
		static $manager = [];

		if ( ! array_key_exists( $plugin_type, $manager ) ) {
			if ( function_exists( 'wpcom_vip_add_role_caps' ) ) {
				$manager[ $plugin_type ] = new WPSEO_Capability_Manager_VIP();
			}

			if ( ! function_exists( 'wpcom_vip_add_role_caps' ) ) {
				$manager[ $plugin_type ] = new WPSEO_Capability_Manager_WP();
			}
		}

		return $manager[ $plugin_type ];
	}
}
