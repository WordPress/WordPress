<?php
/**
 * Manages the WC Admin settings that need to be pre-loaded.
 */

namespace Automattic\WooCommerce\Internal\Admin;

defined( 'ABSPATH' ) || exit;

/**
 * \Automattic\WooCommerce\Internal\Admin\WCAdminSharedSettings class.
 */
class WCAdminSharedSettings {
	/**
	 * Settings prefix used for the window.wcSettings object.
	 *
	 * @var string
	 */
	private $settings_prefix = 'admin';

	/**
	 * Class instance.
	 *
	 * @var WCAdminSharedSettings instance
	 */
	protected static $instance = null;

	/**
	 * Hook into WooCommerce Blocks.
	 */
	protected function __construct() {
		if ( did_action( 'woocommerce_blocks_loaded' ) ) {
			$this->on_woocommerce_blocks_loaded();
		} else {
			add_action( 'woocommerce_blocks_loaded', array( $this, 'on_woocommerce_blocks_loaded' ), 10 );
		}
	}

	/**
	 * Get class instance.
	 *
	 * @return object Instance.
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Adds settings to the Blocks AssetDataRegistry when woocommerce_blocks is loaded.
	 *
	 * @return void
	 */
	public function on_woocommerce_blocks_loaded() {
		if ( class_exists( '\Automattic\WooCommerce\Blocks\Assets\AssetDataRegistry' ) ) {
			\Automattic\WooCommerce\Blocks\Package::container()->get( \Automattic\WooCommerce\Blocks\Assets\AssetDataRegistry::class )->add(
				$this->settings_prefix,
				function() {
					return apply_filters( 'woocommerce_admin_shared_settings', array() );
				},
				true
			);
		}
	}
}

