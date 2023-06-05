<?php
/**
 * WooCommerce Marketing > Coupons.
 */

namespace Automattic\WooCommerce\Internal\Admin;

use Automattic\WooCommerce\Admin\Features\Features;
use Automattic\WooCommerce\Internal\Admin\Notes\CouponPageMoved;
use Automattic\WooCommerce\Admin\PageController;

/**
 * Contains backend logic for the Coupons feature.
 */
class Coupons {

	use CouponsMovedTrait;

	/**
	 * Class instance.
	 *
	 * @var Coupons instance
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
		if ( ! is_admin() ) {
			return;
		}

		// If the main marketing feature is disabled, don't modify coupon behavior.
		if ( ! Features::is_enabled( 'marketing' ) ) {
			return;
		}

		// Only support coupon modifications if coupons are enabled.
		if ( ! wc_coupons_enabled() ) {
			return;
		}

		( new CouponPageMoved() )->init();

		add_action( 'admin_enqueue_scripts', array( $this, 'maybe_add_marketing_coupon_script' ) );
		add_action( 'woocommerce_register_post_type_shop_coupon', array( $this, 'move_coupons' ) );
		add_action( 'admin_head', array( $this, 'fix_coupon_menu_highlight' ), 99 );
		add_action( 'admin_menu', array( $this, 'maybe_add_coupon_menu_redirect' ) );
	}

	/**
	 * Maybe add menu item back in original spot to help people transition
	 */
	public function maybe_add_coupon_menu_redirect() {
		if ( ! $this->should_display_legacy_menu() ) {
			return;
		}

		add_submenu_page(
			'woocommerce',
			__( 'Coupons', 'woocommerce' ),
			__( 'Coupons', 'woocommerce' ),
			'manage_options',
			'coupons-moved',
			[ $this, 'coupon_menu_moved' ]
		);
	}

	/**
	 * Call back for transition menu item
	 */
	public function coupon_menu_moved() {
		wp_safe_redirect( $this->get_legacy_coupon_url(), 301 );
		exit();
	}

	/**
	 * Modify registered post type shop_coupon
	 *
	 * @param array $args Array of post type parameters.
	 *
	 * @return array the filtered parameters.
	 */
	public function move_coupons( $args ) {
		$args['show_in_menu'] = current_user_can( 'manage_woocommerce' ) ? 'woocommerce-marketing' : true;
		return $args;
	}

	/**
	 * Undo WC modifications to $parent_file for 'shop_coupon'
	 */
	public function fix_coupon_menu_highlight() {
		global $parent_file, $post_type;

		if ( $post_type === 'shop_coupon' ) {
			$parent_file = 'woocommerce-marketing'; // phpcs:ignore WordPress.WP.GlobalVariablesOverride
		}
	}

	/**
	 * Maybe add our wc-admin coupon scripts if viewing coupon pages
	 */
	public function maybe_add_marketing_coupon_script() {
		$curent_screen = PageController::get_instance()->get_current_page();
		if ( ! isset( $curent_screen['id'] ) || $curent_screen['id'] !== 'woocommerce-coupons' ) {
			return;
		}

		$rtl = is_rtl() ? '-rtl' : '';

		wp_enqueue_style(
			'wc-admin-marketing-coupons',
			WCAdminAssets::get_url( "marketing-coupons/style{$rtl}", 'css' ),
			array(),
			WCAdminAssets::get_file_version( 'css' )
		);

		WCAdminAssets::register_script( 'wp-admin-scripts', 'marketing-coupons', true );
	}
}
