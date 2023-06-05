<?php
/**
 * Adds and controls pointers for contextual help/tutorials
 *
 * @package WooCommerce\Admin\Pointers
 * @version 2.4.0
 */

use Automattic\WooCommerce\Internal\Admin\WCAdminAssets;
use Automattic\WooCommerce\Admin\Features\Features;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_Admin_Pointers Class.
 */
class WC_Admin_Pointers {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'setup_pointers_for_screen' ) );
	}

	/**
	 * Setup pointers for screen.
	 */
	public function setup_pointers_for_screen() {
		$screen = get_current_screen();

		if ( ! $screen ) {
			return;
		}

		switch ( $screen->id ) {
			case 'product':
				$this->create_product_tutorial();
				$this->create_variable_product_tutorial();
				break;
			case 'woocommerce_page_wc-addons':
				$this->create_wc_addons_tutorial();
				break;
		}
	}

	/**
	 * Pointers for creating a product.
	 */
	public function create_product_tutorial() {
		if ( ! isset( $_GET['tutorial'] ) || ! current_user_can( 'manage_options' ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return;
		}

		global $wp_post_types;

		if ( ! isset( $wp_post_types ) ) {
			return;
		}

		$labels          = $wp_post_types['product']->labels;
		$labels->add_new = __( 'Enable guided mode', 'woocommerce' );
		WCAdminAssets::register_script( 'wp-admin-scripts', 'product-tour', true );
	}

	/**
	 * Pointers for creating a variable product.
	 */
	public function create_variable_product_tutorial() {
		if ( ! current_user_can( 'manage_options' ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return;
		}

		WCAdminAssets::register_script( 'wp-admin-scripts', 'variable-product-tour', true );
	}

	/**
	 * Pointers for accessing In-App Marketplace.
	 */
	public function create_wc_addons_tutorial() {
		if ( ! isset( $_GET['tutorial'] ) || ! current_user_can( 'manage_options' ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return;
		}

		if ( wp_is_mobile() ) {
			return; // Permit In-App Marketplace Tour on desktops only.
		}

		WCAdminAssets::register_script( 'wp-admin-scripts', 'wc-addons-tour', true );
	}
}

new WC_Admin_Pointers();
