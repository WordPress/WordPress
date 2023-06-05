<?php
/**
 * Rule processor that performs a comparison operation against the base
 * location - state.
 */

namespace Automattic\WooCommerce\Admin\RemoteInboxNotifications;

defined( 'ABSPATH' ) || exit;

/**
 * Rule processor that performs a comparison operation against the base
 * location - state.
 */
class BaseLocationStateRuleProcessor implements RuleProcessorInterface {
	/**
	 * Performs a comparison operation against the base location - state.
	 *
	 * @param object $rule         The specific rule being processed by this rule processor.
	 * @param object $stored_state Stored state.
	 *
	 * @return bool The result of the operation.
	 */
	public function process( $rule, $stored_state ) {
		$base_location = wc_get_base_location();
		if ( ! $base_location ) {
			return false;
		}

		return ComparisonOperation::compare(
			$base_location['state'],
			$rule->value,
			$rule->operation
		);
	}

	/**
	 * Validates the rule.
	 *
	 * @param object $rule The rule to validate.
	 *
	 * @return bool Pass/fail.
	 */
	public function validate( $rule ) {
		if ( ! isset( $rule->value ) ) {
			return false;
		}

		if ( ! isset( $rule->operation ) ) {
			return false;
		}

		return true;
	}
}
