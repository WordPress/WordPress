<?php
/**
 * Rule processor for sending when the provided plugin is activated and
 * matches the specified version.
 */

namespace Automattic\WooCommerce\Admin\RemoteInboxNotifications;

defined( 'ABSPATH' ) || exit;

use Automattic\WooCommerce\Admin\PluginsProvider\PluginsProvider;

/**
 * Rule processor for sending when the provided plugin is activated and
 * matches the specified version.
 */
class PluginVersionRuleProcessor implements RuleProcessorInterface {
	/**
	 * Constructor.
	 *
	 * @param PluginsProviderInterface $plugins_provider The plugins provider.
	 */
	public function __construct( $plugins_provider = null ) {
		$this->plugins_provider = null === $plugins_provider
			? new PluginsProvider()
			: $plugins_provider;
	}

	/**
	 * Process the rule.
	 *
	 * @param object $rule         The specific rule being processed by this rule processor.
	 * @param object $stored_state Stored state.
	 *
	 * @return bool Whether the rule passes or not.
	 */
	public function process( $rule, $stored_state ) {
		$active_plugin_slugs = $this->plugins_provider->get_active_plugin_slugs();

		if ( ! in_array( $rule->plugin, $active_plugin_slugs, true ) ) {
			return false;
		}

		$plugin_data = $this->plugins_provider->get_plugin_data( $rule->plugin );

		if ( ! $plugin_data ) {
			return false;
		}

		$plugin_version = $plugin_data['Version'];

		return version_compare( $plugin_version, $rule->version, $rule->operator );
	}

	/**
	 * Validates the rule.
	 *
	 * @param object $rule The rule to validate.
	 *
	 * @return bool Pass/fail.
	 */
	public function validate( $rule ) {
		if ( ! isset( $rule->plugin ) ) {
			return false;
		}

		if ( ! isset( $rule->version ) ) {
			return false;
		}

		if ( ! isset( $rule->operator ) ) {
			return false;
		}

		return true;
	}
}
