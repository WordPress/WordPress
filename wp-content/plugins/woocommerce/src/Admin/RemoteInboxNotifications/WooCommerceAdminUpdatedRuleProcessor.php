<?php
/**
 * Rule processor for sending when WooCommerce Admin has been updated.
 */

namespace Automattic\WooCommerce\Admin\RemoteInboxNotifications;

defined( 'ABSPATH' ) || exit;

/**
 * Rule processor for sending when WooCommerce Admin has been updated.
 */
class WooCommerceAdminUpdatedRuleProcessor implements RuleProcessorInterface {
	/**
	 * Process the rule.
	 *
	 * @param object $rule         The specific rule being processed by this rule processor.
	 * @param object $stored_state Stored state.
	 *
	 * @return bool Whether the rule passes or not.
	 */
	public function process( $rule, $stored_state ) {
		return get_option( RemoteInboxNotificationsEngine::WCA_UPDATED_OPTION_NAME, false );
	}

	/**
	 * Validates the rule.
	 *
	 * @param object $rule The rule to validate.
	 *
	 * @return bool Pass/fail.
	 */
	public function validate( $rule ) {
		return true;
	}
}
