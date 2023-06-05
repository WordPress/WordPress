<?php

namespace Automattic\WooCommerce\Admin\Features\OnboardingTasks\Tasks;

use Automattic\WooCommerce\Admin\Features\OnboardingTasks\Task;

/**
 * Tour In-App Marketplace task
 */
class TourInAppMarketplace extends Task {
	/**
	 * ID.
	 *
	 * @return string
	 */
	public function get_id() {
		return 'tour-in-app-marketplace';
	}

	/**
	 * Title.
	 *
	 * @return string
	 */
	public function get_title() {
		return __(
			'Discover where to find powerful store add-ons and integrations, with a WooCommerce Marketplace tour',
			'woocommerce'
		);
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
	 * Task completion.
	 *
	 * @return bool
	 */
	public function is_complete() {
		return get_option( 'woocommerce_admin_dismissed_in_app_marketplace_tour' ) === 'yes';
	}

	/**
	 * Action URL.
	 *
	 * @return string
	 */
	public function get_action_url() {
		return admin_url( 'admin.php?page=wc-addons&tutorial=true' );
	}

	/**
	 * Check if should record event when task is viewed
	 *
	 * @return bool
	 */
	public function get_record_view_event(): bool {
		return true;
	}
}
