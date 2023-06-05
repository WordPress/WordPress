<?php
/**
 * Rule processor that passes (or fails) when the site is on the eCommerce
 * plan.
 *
 * @package WooCommerce\Admin\Classes
 */

namespace Automattic\WooCommerce\Admin\RemoteInboxNotifications;

defined( 'ABSPATH' ) || exit;

/**
 * Rule processor that passes (or fails) when the site is on the eCommerce
 * plan.
 */
class IsEcommerceRuleProcessor implements RuleProcessorInterface {
	/**
	 * Passes (or fails) based on whether the site is on the eCommerce plan or
	 * not.
	 *
	 * @param object $rule         The rule being processed by this rule processor.
	 * @param object $stored_state Stored state.
	 *
	 * @return bool The result of the operation.
	 */
	public function process( $rule, $stored_state ) {
		if ( ! function_exists( 'wc_calypso_bridge_is_ecommerce_plan' ) ) {
			return false === $rule->value;
		}

		return (bool) wc_calypso_bridge_is_ecommerce_plan() === $rule->value;
	}

	/**
	 * Validate the rule.
	 *
	 * @param object $rule The rule to validate.
	 *
	 * @return bool Pass/fail.
	 */
	public function validate( $rule ) {
		if ( ! isset( $rule->value ) ) {
			return false;
		}

		return true;
	}
}
