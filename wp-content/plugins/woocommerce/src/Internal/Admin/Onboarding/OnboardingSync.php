<?php
/**
 * WooCommerce Onboarding
 */

namespace Automattic\WooCommerce\Internal\Admin\Onboarding;

use Automattic\WooCommerce\Internal\Admin\Onboarding\OnboardingProfile;
use Automattic\WooCommerce\Admin\Features\OnboardingTasks\TaskLists;

/**
 * Contains backend logic for the onboarding profile and checklist feature.
 */
class OnboardingSync {
	/**
	 * Class instance.
	 *
	 * @var OnboardingSync instance
	 */
	private static $instance = null;

	/**
	 * Get class instance.
	 */
	final public static function instance() {
		if ( ! static::$instance ) {
			static::$instance = new static();
		}
		return static::$instance;
	}

	/**
	 * Init.
	 */
	public function init() {
		add_action( 'update_option_' . OnboardingProfile::DATA_OPTION, array( $this, 'send_profile_data_on_update' ), 10, 2 );
		add_action( 'woocommerce_helper_connected', array( $this, 'send_profile_data_on_connect' ) );

		if ( ! is_admin() ) {
			return;
		}

		add_action( 'current_screen', array( $this, 'redirect_wccom_install' ) );
	}

	/**
	 * Send profile data to WooCommerce.com.
	 */
	private function send_profile_data() {
		if ( 'yes' !== get_option( 'woocommerce_allow_tracking', 'no' ) ) {
			return;
		}

		if ( ! class_exists( '\WC_Helper_API' ) || ! method_exists( '\WC_Helper_API', 'put' ) ) {
			return;
		}

		if ( ! class_exists( '\WC_Helper_Options' ) ) {
			return;
		}

		$auth = \WC_Helper_Options::get( 'auth' );
		if ( empty( $auth['access_token'] ) || empty( $auth['access_token_secret'] ) ) {
			return false;
		}

		$profile       = get_option( OnboardingProfile::DATA_OPTION, array() );
		$base_location = wc_get_base_location();
		$defaults      = array(
			'plugins'             => 'skipped',
			'industry'            => array(),
			'product_types'       => array(),
			'product_count'       => '0',
			'selling_venues'      => 'no',
			'number_employees'    => '1',
			'revenue'             => 'none',
			'other_platform'      => 'none',
			'business_extensions' => array(),
			'theme'               => get_stylesheet(),
			'setup_client'        => false,
			'store_location'      => $base_location['country'],
			'default_currency'    => get_woocommerce_currency(),
		);

		// Prepare industries as an array of slugs if they are in array format.
		if ( isset( $profile['industry'] ) && is_array( $profile['industry'] ) ) {
			$industry_slugs = array();
			foreach ( $profile['industry'] as $industry ) {
				$industry_slugs[] = is_array( $industry ) ? $industry['slug'] : $industry;
			}
			$profile['industry'] = $industry_slugs;
		}
		$body = wp_parse_args( $profile, $defaults );

		\WC_Helper_API::put(
			'profile',
			array(
				'authenticated' => true,
				'body'          => wp_json_encode( $body ),
				'headers'       => array(
					'Content-Type' => 'application/json',
				),
			)
		);
	}

	/**
	 * Send profiler data on profiler change to completion.
	 *
	 * @param array $old_value Previous value.
	 * @param array $value Current value.
	 */
	public function send_profile_data_on_update( $old_value, $value ) {
		if ( ! isset( $value['completed'] ) || ! $value['completed'] ) {
			return;
		}

		$this->send_profile_data();
	}

	/**
	 * Send profiler data after a site is connected.
	 */
	public function send_profile_data_on_connect() {
		$profile = get_option( OnboardingProfile::DATA_OPTION, array() );
		if ( ! isset( $profile['completed'] ) || ! $profile['completed'] ) {
			return;
		}

		$this->send_profile_data();
	}

	/**
	 * Redirects the user to the task list if the task list is enabled and finishing a wccom checkout.
	 *
	 * @todo Once URL params are added to the redirect, we can check those instead of the referer.
	 */
	public function redirect_wccom_install() {
		$task_list = TaskLists::get_list( 'setup' );

		if (
			! $task_list ||
			$task_list->is_hidden() ||
			! isset( $_SERVER['HTTP_REFERER'] ) ||
			0 !== strpos( $_SERVER['HTTP_REFERER'], 'https://woocommerce.com/checkout?utm_medium=product' ) // phpcs:ignore sanitization ok.
		) {
			return;
		}

		wp_safe_redirect( wc_admin_url() );
	}
}
