<?php
/**
 * Rule processor that performs a comparison operation against a value in the
 * onboarding profile.
 */

namespace Automattic\WooCommerce\Admin\RemoteInboxNotifications;

defined( 'ABSPATH' ) || exit;

/**
 * Rule processor that performs a comparison operation against a value in the
 * onboarding profile.
 */
class OnboardingProfileRuleProcessor implements RuleProcessorInterface {
	/**
	 * Performs a comparison operation against a value in the onboarding
	 * profile.
	 *
	 * @param object $rule         The rule being processed by this rule processor.
	 * @param object $stored_state Stored state.
	 *
	 * @return bool The result of the operation.
	 */
	public function process( $rule, $stored_state ) {
		$onboarding_profile = get_option( 'woocommerce_onboarding_profile' );

		if ( empty( $onboarding_profile ) ) {
			return false;
		}

		if ( ! isset( $onboarding_profile[ $rule->index ] ) ) {
			return false;
		}

		return ComparisonOperation::compare(
			$onboarding_profile[ $rule->index ],
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
