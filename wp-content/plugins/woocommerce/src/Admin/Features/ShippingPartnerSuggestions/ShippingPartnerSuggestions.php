<?php

namespace Automattic\WooCommerce\Admin\Features\ShippingPartnerSuggestions;

use Automattic\WooCommerce\Admin\RemoteInboxNotifications\RuleEvaluator;

/**
 * Class ShippingPartnerSuggestions
 */
class ShippingPartnerSuggestions {

	/**
	 * Go through the specs and run them.
	 *
	 * @param array|null $specs shipping partner suggestion spec array.
	 * @return array
	 */
	public static function get_suggestions( $specs = null ) {
		$suggestions = array();
		if ( null === $specs ) {
			$specs = self::get_specs_from_datasource();
		}

		$rule_evaluator = new RuleEvaluator();
		foreach ( $specs as &$spec ) {
			$spec = is_array( $spec ) ? (object) $spec : $spec;
			if ( isset( $spec->is_visible ) ) {
				$is_visible = $rule_evaluator->evaluate( $spec->is_visible );
				if ( $is_visible ) {
					$spec->is_visible = true;
					$suggestions[]    = $spec;
				}
			}
		}

		return $suggestions;
	}

	/**
	 * Get specs or fetch remotely if they don't exist.
	 */
	public static function get_specs_from_datasource() {
		if ( 'no' === get_option( 'woocommerce_show_marketplace_suggestions', 'yes' ) ) {
			/**
			 * It can be used to modify shipping partner suggestions spec.
			 *
			 * @since 7.4.1
			 */
			return apply_filters( 'woocommerce_admin_shipping_partner_suggestions_specs', DefaultShippingPartners::get_all() );
		}
		$specs = ShippingPartnerSuggestionsDataSourcePoller::get_instance()->get_specs_from_data_sources();

		// Fetch specs if they don't yet exist.
		if ( false === $specs || ! is_array( $specs ) || 0 === count( $specs ) ) {
			/**
			 * It can be used to modify shipping partner suggestions spec.
			 *
			 * @since 7.4.1
			 */
			return apply_filters( 'woocommerce_admin_shipping_partner_suggestions_specs', DefaultShippingPartners::get_all() );
		}

		/**
		 * It can be used to modify shipping partner suggestions spec.
		 *
		 * @since 7.4.1
		 */
		return apply_filters( 'woocommerce_admin_shipping_partner_suggestions_specs', $specs );
	}
}
