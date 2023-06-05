<?php
/**
 * Filters for maintaining backwards compatibility with deprecated options.
 */

namespace Automattic\WooCommerce\Admin\Features\OnboardingTasks;

use Automattic\WooCommerce\Admin\Features\OnboardingTasks\Tasks\TaskList;
use WC_Install;

/**
 * DeprecatedOptions class.
 */
class DeprecatedOptions {
	/**
	 * Initialize.
	 */
	public static function init() {
		add_filter( 'pre_option_woocommerce_task_list_hidden', array( __CLASS__, 'get_deprecated_options' ), 10, 2 );
		add_filter( 'pre_option_woocommerce_extended_task_list_hidden', array( __CLASS__, 'get_deprecated_options' ), 10, 2 );
		add_action( 'pre_update_option_woocommerce_task_list_hidden', array( __CLASS__, 'update_deprecated_options' ), 10, 3 );
		add_action( 'pre_update_option_woocommerce_extended_task_list_hidden', array( __CLASS__, 'update_deprecated_options' ), 10, 3 );
	}

	/**
	 * Get the values from the correct source when attempting to retrieve deprecated options.
	 *
	 * @param string $pre_option Pre option value.
	 * @param string $option Option name.
	 * @return string
	 */
	public static function get_deprecated_options( $pre_option, $option ) {
		if ( defined( 'WC_INSTALLING' ) && WC_INSTALLING === true ) {
			return $pre_option;
		}

		$hidden = get_option( 'woocommerce_task_list_hidden_lists', array() );
		switch ( $option ) {
			case 'woocommerce_task_list_hidden':
				return in_array( 'setup', $hidden, true ) ? 'yes' : 'no';
			case 'woocommerce_extended_task_list_hidden':
				return in_array( 'extended', $hidden, true ) ? 'yes' : 'no';
		}
	}

	/**
	 * Updates the new option names when deprecated options are updated.
	 * This is a temporary fallback until we can fully remove the old task list components.
	 *
	 * @param string $value New value.
	 * @param string $old_value Old value.
	 * @param string $option Option name.
	 * @return string
	 */
	public static function update_deprecated_options( $value, $old_value, $option ) {
		switch ( $option ) {
			case 'woocommerce_task_list_hidden':
				$task_list = TaskLists::get_list( 'setup' );
				if ( ! $task_list ) {
					return;
				}
				$update = 'yes' === $value ? $task_list->hide() : $task_list->unhide();
				delete_option( 'woocommerce_task_list_hidden' );
				return false;
			case 'woocommerce_extended_task_list_hidden':
				$task_list = TaskLists::get_list( 'extended' );
				if ( ! $task_list ) {
					return;
				}
				$update = 'yes' === $value ? $task_list->hide() : $task_list->unhide();
				delete_option( 'woocommerce_extended_task_list_hidden' );
				return false;
		}
	}
}
