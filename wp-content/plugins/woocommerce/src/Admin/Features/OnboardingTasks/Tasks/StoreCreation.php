<?php

namespace Automattic\WooCommerce\Admin\Features\OnboardingTasks\Tasks;

use Automattic\WooCommerce\Admin\Features\Onboarding;
use Automattic\WooCommerce\Admin\Features\OnboardingTasks\Task;

/**
 * Store Details Task
 */
class StoreCreation extends Task {

	/**
	 * ID.
	 *
	 * @return string
	 */
	public function get_id() {
		return 'store_creation';
	}

	/**
	 * Title.
	 *
	 * @return string
	 */
	public function get_title() {
		/* translators: Store name */
		return sprintf( __( 'You created %s', 'woocommerce' ), get_bloginfo( 'name' ) );
	}

	/**
	 * Content.
	 *
	 * @return string
	 */
	public function get_content() {
		return '';
	}

	/**
	 * Time.
	 *
	 * @return string
	 */
	public function get_time() {
		return '';
	}

	/**
	 * Time.
	 *
	 * @return string
	 */
	public function get_action_url() {
		return '';
	}

	/**
	 * Task completion.
	 *
	 * @return bool
	 */
	public function is_complete() {
		return true;
	}

	/**
	 * Check if task is disabled.
	 *
	 * @return bool
	 */
	public function is_disabled() {
		return true;
	}
}

