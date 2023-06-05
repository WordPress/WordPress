<?php
/**
 * Handles running payment gateway suggestion specs
 */

namespace Automattic\WooCommerce\Admin\Features\PaymentGatewaySuggestions;

defined( 'ABSPATH' ) || exit;

use Automattic\WooCommerce\Admin\Features\PaymentGatewaySuggestions\DefaultPaymentGateways;
use Automattic\WooCommerce\Admin\Features\PaymentGatewaySuggestions\PaymentGatewaysController;

/**
 * Remote Payment Methods engine.
 * This goes through the specs and gets eligible payment gateways.
 */
class Init {
	/**
	 * Option name for dismissed payment method suggestions.
	 */
	const RECOMMENDED_PAYMENT_PLUGINS_DISMISS_OPTION = 'woocommerce_setting_payments_recommendations_hidden';

	/**
	 * Constructor.
	 */
	public function __construct() {
		PaymentGatewaysController::init();
	}

	/**
	 * Go through the specs and run them.
	 *
	 * @param array|null $specs payment suggestion spec array.
	 * @return array
	 */
	public static function get_suggestions( array $specs = null ) {
		$suggestions = array();
		if ( null === $specs ) {
			$specs = self::get_specs();
		}

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
		PaymentGatewaySuggestionsDataSourcePoller::get_instance()->delete_specs_transient();
	}

	/**
	 * Get specs or fetch remotely if they don't exist.
	 */
	public static function get_specs() {
		if ( 'no' === get_option( 'woocommerce_show_marketplace_suggestions', 'yes' ) ) {
			return apply_filters( 'woocommerce_admin_payment_gateway_suggestion_specs', DefaultPaymentGateways::get_all() );
		}
		$specs = PaymentGatewaySuggestionsDataSourcePoller::get_instance()->get_specs_from_data_sources();

		// Fetch specs if they don't yet exist.
		if ( false === $specs || ! is_array( $specs ) || 0 === count( $specs ) ) {
			return apply_filters( 'woocommerce_admin_payment_gateway_suggestion_specs', DefaultPaymentGateways::get_all() );
		}

		return apply_filters( 'woocommerce_admin_payment_gateway_suggestion_specs', $specs );
	}

	/**
	 * Check if suggestions should be shown in the settings screen.
	 *
	 * @return bool
	 */
	public static function should_display() {
		if ( 'yes' === get_option( self::RECOMMENDED_PAYMENT_PLUGINS_DISMISS_OPTION, 'no' ) ) {
			return false;
		}

		if ( 'no' === get_option( 'woocommerce_show_marketplace_suggestions', 'yes' ) ) {
			return false;
		}

		return apply_filters( 'woocommerce_allow_payment_recommendations', true );
	}

	/**
	 * Dismiss the suggestions.
	 */
	public static function dismiss() {
		return update_option( self::RECOMMENDED_PAYMENT_PLUGINS_DISMISS_OPTION, 'yes' );
	}
}
