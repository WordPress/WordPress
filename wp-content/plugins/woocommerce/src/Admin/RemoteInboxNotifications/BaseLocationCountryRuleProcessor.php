<?php
/**
 * Rule processor that performs a comparison operation against the base
 * location - country.
 */

namespace Automattic\WooCommerce\Admin\RemoteInboxNotifications;

use Automattic\WooCommerce\Internal\Admin\Onboarding\OnboardingProfile;

defined( 'ABSPATH' ) || exit;

/**
 * Rule processor that performs a comparison operation against the base
 * location - country.
 */
class BaseLocationCountryRuleProcessor implements RuleProcessorInterface {
	/**
	 * Performs a comparison operation against the base location - country.
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

		// Return false if the location is the default country and if onboarding hasn't been finished or the store address not been updated.
		if ( 'US' === $base_location['country'] && 'CA' === $base_location['state'] && empty( get_option( 'woocommerce_store_address', '' ) ) && OnboardingProfile::needs_completion() ) {
			return false;
		}

		return ComparisonOperation::compare(
			$base_location['country'],
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
