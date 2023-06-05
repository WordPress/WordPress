<?php
/**
 * Handles wcpay promotion
 */

namespace Automattic\WooCommerce\Internal\Admin\WCPayPromotion;

defined( 'ABSPATH' ) || exit;

use Automattic\WooCommerce\Admin\DataSourcePoller;
use Automattic\WooCommerce\Admin\Features\PaymentGatewaySuggestions\EvaluateSuggestion;
use Automattic\WooCommerce\Admin\Features\PaymentGatewaySuggestions\PaymentGatewaySuggestionsDataSourcePoller as PaymentGatewaySuggestionsDataSourcePoller;
use Automattic\WooCommerce\Internal\Admin\WCAdminAssets;

/**
 * WC Pay Promotion engine.
 */
class Init {
	const EXPLAT_VARIATION_PREFIX = 'woocommerce_wc_pay_promotion_payment_methods_table_';

	/**
	 * Constructor.
	 */
	public function __construct() {
		include_once __DIR__ . '/WCPaymentGatewayPreInstallWCPayPromotion.php';

		$is_payments_page = isset( $_GET['page'] ) && $_GET['page'] === 'wc-settings' && isset( $_GET['tab'] ) && $_GET['tab'] === 'checkout'; // phpcs:ignore WordPress.Security.NonceVerification
		if ( ! wp_is_json_request() && ! $is_payments_page ) {
			return;
		}

		add_filter( 'woocommerce_payment_gateways', array( __CLASS__, 'possibly_register_pre_install_wc_pay_promotion_gateway' ) );
		add_filter( 'option_woocommerce_gateway_order', [ __CLASS__, 'set_gateway_top_of_list' ] );
		add_filter( 'default_option_woocommerce_gateway_order', [ __CLASS__, 'set_gateway_top_of_list' ] );

		$rtl = is_rtl() ? '.rtl' : '';

		wp_enqueue_style(
			'wc-admin-payment-method-promotions',
			WCAdminAssets::get_url( "payment-method-promotions/style{$rtl}", 'css' ),
			array( 'wp-components' ),
			WCAdminAssets::get_file_version( 'css' )
		);

		WCAdminAssets::register_script( 'wp-admin-scripts', 'payment-method-promotions', true );
	}

	/**
	 * Possibly registers the pre install wc pay promoted gateway.
	 *
	 * @param array $gateways list of gateway classes.
	 * @return array list of gateway classes.
	 */
	public static function possibly_register_pre_install_wc_pay_promotion_gateway( $gateways ) {
		if ( self::can_show_promotion() && ! WCPaymentGatewayPreInstallWCPayPromotion::is_dismissed() ) {
			$gateways[] = 'Automattic\WooCommerce\Internal\Admin\WCPayPromotion\WCPaymentGatewayPreInstallWCPayPromotion';
		}
		return $gateways;
	}

	/**
	 * Checks if promoted gateway can be registered.
	 *
	 * @return boolean if promoted gateway should be registered.
	 */
	public static function can_show_promotion() {
		// Check if WC Pay is enabled.
		if ( class_exists( '\WC_Payments' ) ) {
			return false;
		}
		if ( get_option( 'woocommerce_show_marketplace_suggestions', 'yes' ) === 'no' ) {
			return false;
		}
		if ( ! apply_filters( 'woocommerce_allow_marketplace_suggestions', true ) ) {
			return false;
		}

		$wc_pay_spec = self::get_wc_pay_promotion_spec();
		if ( ! $wc_pay_spec ) {
			return false;
		}
		return true;
	}

	/**
	 * By default, new payment gateways are put at the bottom of the list on the admin "Payments" settings screen.
	 * For visibility, we want WooCommerce Payments to be at the top of the list.
	 *
	 * @param array $ordering Existing ordering of the payment gateways.
	 *
	 * @return array Modified ordering.
	 */
	public static function set_gateway_top_of_list( $ordering ) {
		$ordering = (array) $ordering;
		$id       = WCPaymentGatewayPreInstallWCPayPromotion::GATEWAY_ID;
		// Only tweak the ordering if the list hasn't been reordered with WooCommerce Payments in it already.
		if ( ! isset( $ordering[ $id ] ) || ! is_numeric( $ordering[ $id ] ) ) {
			$is_empty        = empty( $ordering ) || ( count( $ordering ) === 1 && $ordering[0] === false );
			$ordering[ $id ] = $is_empty ? 0 : ( min( $ordering ) - 1 );
		}
		return $ordering;
	}

	/**
	 * Get WC Pay promotion spec.
	 */
	public static function get_wc_pay_promotion_spec() {
		$promotions            = self::get_promotions();
		$wc_pay_promotion_spec = array_values(
			array_filter(
				$promotions,
				function( $promotion ) {
					return isset( $promotion->plugins ) && in_array( 'woocommerce-payments', $promotion->plugins, true );
				}
			)
		);

		return current( $wc_pay_promotion_spec );
	}

	/**
	 * Go through the specs and run them.
	 */
	public static function get_promotions() {
		$suggestions = array();
		$specs       = self::get_specs();

		foreach ( $specs as $spec ) {
			$suggestion    = EvaluateSuggestion::evaluate( $spec );
			$suggestions[] = $suggestion;
		}

		return array_values(
			array_filter(
				$suggestions,
				function( $suggestion ) {
					return ! property_exists( $suggestion, 'is_visible' ) || $suggestion->is_visible;
				}
			)
		);

	}

	/**
	 * Delete the specs transient.
	 */
	public static function delete_specs_transient() {
		WCPayPromotionDataSourcePoller::get_instance()->delete_specs_transient();
	}

	/**
	 * Get specs or fetch remotely if they don't exist.
	 */
	public static function get_specs() {
		if ( get_option( 'woocommerce_show_marketplace_suggestions', 'yes' ) === 'no' ) {
			return array();
		}
		return WCPayPromotionDataSourcePoller::get_instance()->get_specs_from_data_sources();
	}
}

