<?php
/**
 * Gets the processor for the specified rule type.
 */

namespace Automattic\WooCommerce\Admin\RemoteInboxNotifications;

defined( 'ABSPATH' ) || exit;

/**
 * Class encapsulating getting the processor for a given rule type.
 */
class GetRuleProcessor {
	/**
	 * Get the processor for the specified rule type.
	 *
	 * @param string $rule_type The rule type.
	 *
	 * @return RuleProcessorInterface The matching processor for the specified rule type, or a FailRuleProcessor if no matching processor is found.
	 */
	public static function get_processor( $rule_type ) {
		switch ( $rule_type ) {
			case 'plugins_activated':
				return new PluginsActivatedRuleProcessor();
			case 'publish_after_time':
				return new PublishAfterTimeRuleProcessor();
			case 'publish_before_time':
				return new PublishBeforeTimeRuleProcessor();
			case 'not':
				return new NotRuleProcessor();
			case 'or':
				return new OrRuleProcessor();
			case 'fail':
				return new FailRuleProcessor();
			case 'pass':
				return new PassRuleProcessor();
			case 'plugin_version':
				return new PluginVersionRuleProcessor();
			case 'stored_state':
				return new StoredStateRuleProcessor();
			case 'order_count':
				return new OrderCountRuleProcessor();
			case 'wcadmin_active_for':
				return new WCAdminActiveForRuleProcessor();
			case 'product_count':
				return new ProductCountRuleProcessor();
			case 'onboarding_profile':
				return new OnboardingProfileRuleProcessor();
			case 'is_ecommerce':
				return new IsEcommerceRuleProcessor();
			case 'base_location_country':
				return new BaseLocationCountryRuleProcessor();
			case 'base_location_state':
				return new BaseLocationStateRuleProcessor();
			case 'note_status':
				return new NoteStatusRuleProcessor();
			case 'option':
				return new OptionRuleProcessor();
			case 'wca_updated':
				return new WooCommerceAdminUpdatedRuleProcessor();
			case 'total_payments_value':
				return new TotalPaymentsVolumeProcessor();
		}

		return new FailRuleProcessor();
	}
}
