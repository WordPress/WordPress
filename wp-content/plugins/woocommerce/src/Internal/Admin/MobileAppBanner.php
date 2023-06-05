<?php

namespace Automattic\WooCommerce\Internal\Admin;

defined( 'ABSPATH' ) || exit;

/**
 * Determine if the mobile app banner shows on Android devices
 */
class MobileAppBanner {
	/**
	 * Class instance.
	 *
	 * @var Analytics instance
	 */
	protected static $instance = null;

	/**
	 * Get class instance.
	 */
	public static function get_instance() {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Hook into WooCommerce.
	 */
	public function __construct() {
		add_filter( 'woocommerce_admin_get_user_data_fields', array( $this, 'add_user_data_fields' ) );
	}

	/**
	 * Adds fields so that we can store user preferences for the mobile app banner
	 *
	 * @param array $user_data_fields User data fields.
	 * @return array
	 */
	public function add_user_data_fields( $user_data_fields ) {
		return array_merge(
			$user_data_fields,
			array(
				'android_app_banner_dismissed',
			)
		);
	}
}
