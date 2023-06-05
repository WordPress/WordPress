<?php
/**
 * WooCommerce Onboarding Setup Wizard
 */

namespace Automattic\WooCommerce\Internal\Admin\Onboarding;

use Automattic\WooCommerce\Admin\PageController;
use Automattic\WooCommerce\Admin\WCAdminHelper;
use Automattic\WooCommerce\Internal\Admin\Onboarding\OnboardingProfile;
use Automattic\WooCommerce\Admin\Features\OnboardingTasks\TaskLists;

/**
 * Contains backend logic for the onboarding profile and checklist feature.
 */
class OnboardingSetupWizard {
	/**
	 * Class instance.
	 *
	 * @var OnboardingSetupWizard instance
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
	 * Add onboarding actions.
	 */
	public function init() {
		if ( ! is_admin() ) {
			return;
		}

		// Old settings injection.
		// Run after Automattic\WooCommerce\Internal\Admin\Loader.
		add_filter( 'woocommerce_components_settings', array( $this, 'component_settings' ), 20 );
		// New settings injection.
		add_filter( 'woocommerce_admin_shared_settings', array( $this, 'component_settings' ), 20 );
		add_filter( 'woocommerce_admin_preload_settings', array( $this, 'preload_settings' ) );
		add_filter( 'admin_body_class', array( $this, 'add_loading_classes' ) );
		add_action( 'admin_init', array( $this, 'do_admin_redirects' ) );
		add_action( 'current_screen', array( $this, 'redirect_to_profiler' ) );
		add_filter( 'woocommerce_show_admin_notice', array( $this, 'remove_old_install_notice' ), 10, 2 );
	}

	/**
	 * Test whether the context of execution comes from async action scheduler.
	 * Note: this is a polyfill for wc_is_running_from_async_action_scheduler()
	 *       which was introduced in WC 4.0.
	 *
	 * @return bool
	 */
	private function is_running_from_async_action_scheduler() {
		if ( function_exists( '\wc_is_running_from_async_action_scheduler' ) ) {
			return \wc_is_running_from_async_action_scheduler();
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		return isset( $_REQUEST['action'] ) && 'as_async_request_queue_runner' === $_REQUEST['action'];
	}

	/**
	 * Handle redirects to setup/welcome page after install and updates.
	 *
	 * For setup wizard, transient must be present, the user must have access rights, and we must ignore the network/bulk plugin updaters.
	 */
	public function do_admin_redirects() {
		// Don't run this fn from Action Scheduler requests, as it would clear _wc_activation_redirect transient.
		// That means OBW would never be shown.
		if ( $this->is_running_from_async_action_scheduler() ) {
			return;
		}

		// Setup wizard redirect.
		if ( get_transient( '_wc_activation_redirect' ) && apply_filters( 'woocommerce_enable_setup_wizard', true ) ) {
			$do_redirect        = true;
			$current_page       = isset( $_GET['page'] ) ? wc_clean( wp_unslash( $_GET['page'] ) ) : false; // phpcs:ignore WordPress.Security.NonceVerification
			$is_onboarding_path = ! isset( $_GET['path'] ) || '/setup-wizard' === wc_clean( wp_unslash( $_GET['page'] ) ); // phpcs:ignore WordPress.Security.NonceVerification

			// On these pages, or during these events, postpone the redirect.
			if ( wp_doing_ajax() || is_network_admin() || ! current_user_can( 'manage_woocommerce' ) ) {
				$do_redirect = false;
			}

			// On these pages, or during these events, disable the redirect.
			if (
				( 'wc-admin' === $current_page && $is_onboarding_path ) ||
				apply_filters( 'woocommerce_prevent_automatic_wizard_redirect', false ) ||
				isset( $_GET['activate-multi'] ) // phpcs:ignore WordPress.Security.NonceVerification
			) {
				delete_transient( '_wc_activation_redirect' );
				$do_redirect = false;
			}

			if ( $do_redirect ) {
				delete_transient( '_wc_activation_redirect' );
				wp_safe_redirect( wc_admin_url() );
				exit;
			}
		}
	}

	/**
	 * Trigger the woocommerce_onboarding_profile_completed action
	 *
	 * @param array $old_value Previous value.
	 * @param array $value Current value.
	 */
	public function trigger_profile_completed_action( $old_value, $value ) {
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
	 * Returns true if the profiler should be displayed (not completed and not skipped).
	 *
	 * @return bool
	 */
	private function should_show() {
		if ( $this->is_setup_wizard() ) {
			return true;
		}

		return OnboardingProfile::needs_completion();
	}

	/**
	 * Redirect to the profiler on homepage if completion is needed.
	 */
	public function redirect_to_profiler() {
		if ( ! $this->is_homepage() || ! OnboardingProfile::needs_completion() ) {
			return;
		}

		wp_safe_redirect( wc_admin_url( '&path=/setup-wizard' ) );
		exit;
	}

	/**
	 * Check if the current page is the profile wizard.
	 *
	 * @return bool
	 */
	private function is_setup_wizard() {
		/* phpcs:disable WordPress.Security.NonceVerification */
		return isset( $_GET['page'] ) &&
			'wc-admin' === $_GET['page'] &&
			isset( $_GET['path'] ) &&
			'/setup-wizard' === $_GET['path'];
		/* phpcs: enable */
	}

	/**
	 * Check if the current page is the homepage.
	 *
	 * @return bool
	 */
	private function is_homepage() {
		/* phpcs:disable WordPress.Security.NonceVerification */
		return isset( $_GET['page'] ) &&
			'wc-admin' === $_GET['page'] &&
			! isset( $_GET['path'] );
		/* phpcs: enable */
	}

	/**
	 * Determine if the current page is one of the WC Admin pages.
	 *
	 * @return bool
	 */
	private function is_woocommerce_page() {
		$current_page = PageController::get_instance()->get_current_page();
		if ( ! $current_page || ! isset( $current_page['path'] ) ) {
			return false;
		}

		return 0 === strpos( $current_page['path'], 'wc-admin' );
	}

	/**
	 * Add profiler items to component settings.
	 *
	 * @param array $settings Component settings.
	 *
	 * @return array
	 */
	public function component_settings( $settings ) {
		$profile                = (array) get_option( OnboardingProfile::DATA_OPTION, array() );
		$settings['onboarding'] = array(
			'profile' => $profile,
		);

		// Only fetch if the onboarding wizard OR the task list is incomplete or currently shown
		// or the current page is one of the WooCommerce Admin pages.
		if (
			( ! $this->should_show() && ! count( TaskLists::get_visible() )
			||
			! $this->is_woocommerce_page()
		)
		) {
			return $settings;
		}

		include_once WC_ABSPATH . 'includes/admin/helper/class-wc-helper-options.php';
		$wccom_auth                 = \WC_Helper_Options::get( 'auth' );
		$profile['wccom_connected'] = empty( $wccom_auth['access_token'] ) ? false : true;

		$settings['onboarding']['currencySymbols'] = get_woocommerce_currency_symbols();
		$settings['onboarding']['euCountries']     = WC()->countries->get_european_union_countries();
		$settings['onboarding']['localeInfo']      = include WC()->plugin_path() . '/i18n/locale-info.php';
		$settings['onboarding']['profile']         = $profile;

		if ( $this->is_setup_wizard() ) {
			$settings['onboarding']['pageCount']    = (int) ( wp_count_posts( 'page' ) )->publish;
			$settings['onboarding']['postCount']    = (int) ( wp_count_posts( 'post' ) )->publish;
			$settings['onboarding']['isBlockTheme'] = wc_current_theme_is_fse_theme();
		}

		return apply_filters( 'woocommerce_admin_onboarding_preloaded_data', $settings );
	}

	/**
	 * Preload WC setting options to prime state of the application.
	 *
	 * @param array $options Array of options to preload.
	 * @return array
	 */
	public function preload_settings( $options ) {
		$options[] = 'general';

		return $options;
	}

	/**
	 * Set the admin full screen class when loading to prevent flashes of unstyled content.
	 *
	 * @param bool $classes Body classes.
	 * @return array
	 */
	public function add_loading_classes( $classes ) {
		/* phpcs:disable WordPress.Security.NonceVerification */
		if ( $this->is_setup_wizard() ) {
			$classes .= ' woocommerce-admin-full-screen';
		}
		/* phpcs: enable */

		return $classes;
	}

	/**
	 * Remove the install notice that prompts the user to visit the old onboarding setup wizard.
	 *
	 * @param bool   $show Show or hide the notice.
	 * @param string $notice The slug of the notice.
	 * @return bool
	 */
	public function remove_old_install_notice( $show, $notice ) {
		if ( 'install' === $notice ) {
			return false;
		}

		return $show;
	}
}
