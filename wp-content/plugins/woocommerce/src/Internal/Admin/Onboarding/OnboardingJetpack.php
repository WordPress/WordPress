<?php
/**
 * WooCommerce Onboarding Jetpack
 */

namespace Automattic\WooCommerce\Internal\Admin\Onboarding;

/**
 * Contains logic around Jetpack setup during onboarding.
 */
class OnboardingJetpack {
	/**
	 * Class instance.
	 *
	 * @var OnboardingJetpack instance
	 */
	private static $instance = null;

	/**
	 * Get class instance.
	 */
	final public static function instance() {
		if ( ! static::$instance ) {
			static::$instance = new static();
		}
		return static::$instance;
	}

	/**
	 * Init.
	 */
	public function init() {
		add_action( 'woocommerce_admin_plugins_pre_activate', array( $this, 'activate_and_install_jetpack_ahead_of_wcpay' ) );
		add_action( 'woocommerce_admin_plugins_pre_install', array( $this, 'activate_and_install_jetpack_ahead_of_wcpay' ) );

		// Always hook into Jetpack connection even if outside of admin.
		add_action( 'jetpack_site_registered', array( $this, 'set_woocommerce_setup_jetpack_opted_in' ) );
	}

	/**
	 * Sets the woocommerce_setup_jetpack_opted_in to true when Jetpack connects to WPCOM.
	 */
	public function set_woocommerce_setup_jetpack_opted_in() {
		update_option( 'woocommerce_setup_jetpack_opted_in', true );
	}

	/**
	 * Ensure that Jetpack gets installed and activated ahead of WooCommerce Payments
	 * if both are being installed/activated at the same time.
	 *
	 * See: https://github.com/Automattic/woocommerce-payments/issues/1663
	 * See: https://github.com/Automattic/jetpack/issues/19624
	 *
	 * @param array $plugins A list of plugins to install or activate.
	 *
	 * @return array
	 */
	public function activate_and_install_jetpack_ahead_of_wcpay( $plugins ) {
		if ( in_array( 'jetpack', $plugins, true ) && in_array( 'woocommerce-payments', $plugins, true ) ) {
			array_unshift( $plugins, 'jetpack' );
			$plugins = array_unique( $plugins );
		}
		return $plugins;
	}

}
