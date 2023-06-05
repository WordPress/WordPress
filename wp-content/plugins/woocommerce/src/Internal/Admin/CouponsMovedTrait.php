<?php
/**
 * A Trait to help with managing the legacy coupon menu.
 */

namespace Automattic\WooCommerce\Internal\Admin;

use Automattic\WooCommerce\Admin\Features\Features;

/**
 * CouponsMovedTrait trait.
 */
trait CouponsMovedTrait {

	/**
	 * The GET query key for the legacy menu.
	 *
	 * @var string
	 */
	protected static $query_key = 'legacy_coupon_menu';

	/**
	 * The key for storing an option in the DB.
	 *
	 * @var string
	 */
	protected static $option_key = 'wc_admin_show_legacy_coupon_menu';

	/**
	 * Get the URL for the legacy coupon management.
	 *
	 * @return string The unescaped URL for the legacy coupon management page.
	 */
	protected static function get_legacy_coupon_url() {
		return self::get_coupon_url( [ self::$query_key => true ] );
	}

	/**
	 * Get the URL for the coupon management page.
	 *
	 * @param array $args Additional URL query arguments.
	 *
	 * @return string
	 */
	protected static function get_coupon_url( $args = [] ) {
		$args = array_merge(
			[
				'post_type' => 'shop_coupon',
			],
			$args
		);

		return add_query_arg( $args, admin_url( 'edit.php' ) );
	}

	/**
	 * Get the new URL for managing coupons.
	 *
	 * @param string $page The management page.
	 *
	 * @return string
	 */
	protected static function get_management_url( $page ) {
		$path = '';
		switch ( $page ) {
			case 'coupon':
			case 'coupons':
				return self::get_coupon_url();

			case 'marketing':
				$path = self::get_marketing_path();
				break;
		}

		return "wc-admin&path={$path}";
	}

	/**
	 * Get the WC Admin path for the marking page.
	 *
	 * @return string
	 */
	protected static function get_marketing_path() {
		return '/marketing/overview';
	}

	/**
	 * Whether we should display the legacy coupon menu item.
	 *
	 * @return bool
	 */
	protected static function should_display_legacy_menu() {
		return ( get_option( self::$option_key, 1 ) && ! Features::is_enabled( 'navigation' ) );
	}

	/**
	 * Set whether we should display the legacy coupon menu item.
	 *
	 * @param bool $display Whether the menu should be displayed or not.
	 */
	protected static function display_legacy_menu( $display = false ) {
		update_option( self::$option_key, $display ? 1 : 0 );
	}
}
