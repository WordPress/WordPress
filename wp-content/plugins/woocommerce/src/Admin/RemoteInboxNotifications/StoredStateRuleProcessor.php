<?php
/**
 * Rule processor that performs a comparison operation against a value in the
 * stored state object.
 */

namespace Automattic\WooCommerce\Admin\RemoteInboxNotifications;

defined( 'ABSPATH' ) || exit;

/**
 * Rule processor that performs a comparison operation against a value in the
 * stored state object.
 */
class StoredStateRuleProcessor implements RuleProcessorInterface {
	/**
	 * Performs a comparison operation against a value in the stored state object.
	 *
	 * @param object $rule         The rule being processed by this rule processor.
	 * @param object $stored_state Stored state.
	 *
	 * @return bool The result of the operation.
	 */
	public function process( $rule, $stored_state ) {
		if ( ! isset( $stored_state->{$rule->index} ) ) {
			return false;
		}

		return ComparisonOperation::compare(
			$stored_state->{$rule->index},
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
		if ( ! isset( $rule->index ) ) {
			return false;
		}

		if ( ! isset( $rule->value ) ) {
			return false;
		}

		if ( ! isset( $rule->operation ) ) {
			return false;
		}

		return true;
	}
}
