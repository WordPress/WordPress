<?php

namespace Automattic\WooCommerce\Admin\Features\OnboardingTasks\Tasks;

use Automattic\WooCommerce\Internal\Admin\Onboarding\OnboardingProfile;
use Automattic\WooCommerce\Admin\Features\OnboardingTasks\Task;
use Automattic\WooCommerce\Admin\PluginsHelper;
use Automattic\WooCommerce\Admin\Features\PaymentGatewaySuggestions\Init as Suggestions;
use Automattic\WooCommerce\Admin\Features\OnboardingTasks\TaskList;

/**
 * WooCommercePayments Task
 */
class WooCommercePayments extends Task {
	/**
	 * ID.
	 *
	 * @return string
	 */
	public function get_id() {
		return 'woocommerce-payments';
	}

	/**
	 * Title.
	 *
	 * @return string
	 */
	public function get_title() {
		return __( 'Set up WooCommerce Payments', 'woocommerce' );
	}

	/**
	 * Content.
	 *
	 * @return string
	 */
	public function get_content() {
		return __(
			"You're only one step away from getting paid. Verify your business details to start managing transactions with WooCommerce Payments.",
			'woocommerce'
		);
	}

	/**
	 * Time.
	 *
	 * @return string
	 */
	public function get_time() {
		return __( '2 minutes', 'woocommerce' );
	}

	/**
	 * Action label.
	 *
	 * @return string
	 */
	public function get_action_label() {
		return __( 'Finish setup', 'woocommerce' );
	}

	/**
	 * Additional info.
	 *
	 * @return string
	 */
	public function get_additional_info() {
		return __(
			'By using WooCommerce Payments you agree to be bound by our <a href="https://wordpress.com/tos/" target="_blank">Terms of Service</a> and acknowledge that you have read our <a href="https://automattic.com/privacy/" target="_blank">Privacy Policy</a>',
			'woocommerce'
		);
	}

	/**
	 * Task completion.
	 *
	 * @return bool
	 */
	public function is_complete() {
		return self::is_connected();
	}

	/**
	 * Task visibility.
	 *
	 * @return bool
	 */
	public function can_view() {
		$payments = $this->task_list->get_task( 'payments' );

		return ! $payments->is_complete() && // Do not re-display the task if the "add payments" task has already been completed.
			self::is_installed() &&
			self::is_supported() &&
			! self::is_connected();
	}

	/**
	 * Check if the plugin was requested during onboarding.
	 *
	 * @return bool
	 */
	public static function is_requested() {
		$profiler_data       = get_option( OnboardingProfile::DATA_OPTION, array() );
		$product_types       = isset( $profiler_data['product_types'] ) ? $profiler_data['product_types'] : array();
		$business_extensions = isset( $profiler_data['business_extensions'] ) ? $profiler_data['business_extensions'] : array();

		$subscriptions_and_us = in_array( 'subscriptions', $product_types, true ) && 'US' === WC()->countries->get_base_country();
		return in_array( 'woocommerce-payments', $business_extensions, true ) || $subscriptions_and_us;
	}

	/**
	 * Check if the plugin is installed.
	 *
	 * @return bool
	 */
	public static function is_installed() {
		$installed_plugins = PluginsHelper::get_installed_plugin_slugs();
		return in_array( 'woocommerce-payments', $installed_plugins, true );
	}

	/**
	 * Check if WooCommerce Payments is connected.
	 *
	 * @return bool
	 */
	public static function is_connected() {
		if ( class_exists( '\WC_Payments' ) ) {
			$wc_payments_gateway = \WC_Payments::get_gateway();
			return method_exists( $wc_payments_gateway, 'is_connected' )
				? $wc_payments_gateway->is_connected()
				: false;
		}

		return false;
	}

	/**
	 * Check if the store is in a supported country.
	 *
	 * @return bool
	 */
	public static function is_supported() {
		$suggestions              = Suggestions::get_suggestions();
		$suggestion_plugins       = array_merge(
			...array_filter(
				array_column( $suggestions, 'plugins' ),
				function( $plugins ) {
					return is_array( $plugins );
				}
			)
		);
		$woocommerce_payments_ids = array_search( 'woocommerce-payments', $suggestion_plugins, true );
		if ( false !== $woocommerce_payments_ids ) {
			return true;
		}
		return false;
	}
}
