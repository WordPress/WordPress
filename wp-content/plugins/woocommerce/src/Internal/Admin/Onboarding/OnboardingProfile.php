<?php
/**
 * WooCommerce Onboarding Setup Wizard
 */

namespace Automattic\WooCommerce\Internal\Admin\Onboarding;

use Automattic\WooCommerce\Admin\Features\OnboardingTasks\TaskLists;
use Automattic\WooCommerce\Admin\PageController;
use Automattic\WooCommerce\Admin\WCAdminHelper;

/**
 * Contains backend logic for the onboarding profile and checklist feature.
 */
class OnboardingProfile {
	/**
	 * Profile data option name.
	 */
	const DATA_OPTION = 'woocommerce_onboarding_profile';

	/**
	 * Add onboarding actions.
	 */
	public static function init() {
		add_action( 'woocommerce_updated', array( __CLASS__, 'maybe_mark_complete' ) );
		add_action( 'update_option_' . self::DATA_OPTION, array( __CLASS__, 'trigger_complete' ), 10, 2 );
	}

	/**
	 * Trigger the woocommerce_onboarding_profile_completed action
	 *
	 * @param array $old_value Previous value.
	 * @param array $value Current value.
	 */
	public static function trigger_complete( $old_value, $value ) {
		if ( isset( $old_value['completed'] ) && $old_value['completed'] ) {
			return;
		}

		if ( ! isset( $value['completed'] ) || ! $value['completed'] ) {
			return;
		}

		/**
		 * Action hook fired when the onboarding profile (or onboarding wizard,
		 * or profiler) is completed.
		 *
		 * @since 1.5.0
		 */
		do_action( 'woocommerce_onboarding_profile_completed' );
	}

	/**
	 * Check if the profiler still needs to be completed.
	 *
	 * @return bool
	 */
	public static function needs_completion() {
		$onboarding_data = get_option( self::DATA_OPTION, array() );

		$is_completed = isset( $onboarding_data['completed'] ) && true === $onboarding_data['completed'];
		$is_skipped   = isset( $onboarding_data['skipped'] ) && true === $onboarding_data['skipped'];

		// @todo When merging to WooCommerce Core, we should set the `completed` flag to true during the upgrade progress.
		// https://github.com/woocommerce/woocommerce-admin/pull/2300#discussion_r287237498.
		return ! $is_completed && ! $is_skipped;
	}

	/**
	 * When updating WooCommerce, mark the profiler and task list complete.
	 *
	 * @todo The `maybe_enable_setup_wizard()` method should be revamped on onboarding enable in core.
	 * See https://github.com/woocommerce/woocommerce/blob/1ca791f8f2325fe2ee0947b9c47e6a4627366374/includes/class-wc-install.php#L341
	 */
	public static function maybe_mark_complete() {
		// The install notice still exists so don't complete the profiler.
		if ( ! class_exists( 'WC_Admin_Notices' ) || \WC_Admin_Notices::has_notice( 'install' ) ) {
			return;
		}

		$onboarding_data = get_option( self::DATA_OPTION, array() );
		// Don't make updates if the profiler is completed or skipped, but task list is potentially incomplete.
		if (
			( isset( $onboarding_data['completed'] ) && $onboarding_data['completed'] ) ||
			( isset( $onboarding_data['skipped'] ) && $onboarding_data['skipped'] )
		) {
			return;
		}

		$onboarding_data['completed'] = true;
		update_option( self::DATA_OPTION, $onboarding_data );

		if ( ! WCAdminHelper::is_wc_admin_active_for( DAY_IN_SECONDS ) ) {
			$task_list = TaskLists::get_list( 'setup' );
			if ( ! $task_list ) {
				return;
			}
			$task_list->hide();
		}
	}
}
