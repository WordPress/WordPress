<?php

namespace Automattic\WooCommerce\Admin\Features\OnboardingTasks\Tasks;

use Automattic\WooCommerce\Admin\Features\OnboardingTasks\Task;
use Automattic\Jetpack\Connection\Manager; // https://github.com/Automattic/jetpack/blob/trunk/projects/packages/connection/src/class-manager.php .

/**
 * Get Mobile App Task
 */
class GetMobileApp extends Task {
	/**
	 * ID.
	 *
	 * @return string
	 */
	public function get_id() {
		return 'get-mobile-app';
	}

	/**
	 * Title.
	 *
	 * @return string
	 */
	public function get_title() {
		return __( 'Get the free WooCommerce mobile app', 'woocommerce' );
	}

	/**
	 * Content.
	 *
	 * @return string
	 */
	public function get_content() {
		return '';
	}

	/**
	 * Time.
	 *
	 * @return string
	 */
	public function get_time() {
		return '';
	}

	/**
	 * Task completion.
	 *
	 * @return bool
	 */
	public function is_complete() {
		return get_option( 'woocommerce_admin_dismissed_mobile_app_modal' ) === 'yes';
	}

	/**
	 * Task visibility.
	 * Can view under these conditions:
	 *  - Jetpack is installed and connected && current site user has a wordpress.com account connected to jetpack
	 *  - Jetpack is not connected && current user is capable of installing plugins
	 *
	 * @return bool
	 */
	public function can_view() {
		$jetpack_can_be_installed                        = current_user_can( 'manage_woocommerce' ) && current_user_can( 'install_plugins' ) && ! self::is_jetpack_connected();
		$jetpack_is_installed_and_current_user_connected = self::is_current_user_connected();

		return $jetpack_can_be_installed || $jetpack_is_installed_and_current_user_connected;
	}

	/**
	 * Determines if site has any users connected to WordPress.com via JetPack
	 *
	 * @return bool
	 */
	private static function is_jetpack_connected() {
		if ( class_exists( '\Automattic\Jetpack\Connection\Manager' ) && method_exists( '\Automattic\Jetpack\Connection\Manager', 'is_active' ) ) {
			$connection = new Manager();
			return $connection->is_active();
		}
		return false;
	}

	/**
	 * Determines if the current user is connected to Jetpack.
	 *
	 * @return bool
	 */
	private static function is_current_user_connected() {
		if ( class_exists( '\Automattic\Jetpack\Connection\Manager' ) && method_exists( '\Automattic\Jetpack\Connection\Manager', 'is_user_connected' ) ) {
			$connection = new Manager();
			return $connection->is_connection_owner();
		}
		return false;
	}

	/**
	 * Action URL.
	 *
	 * @return string
	 */
	public function get_action_url() {
		return admin_url( 'admin.php?page=wc-admin&mobileAppModal=true' );
	}
}
