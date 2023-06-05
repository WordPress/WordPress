<?php
/**
 * WooCommerce Onboarding Helper
 */

namespace Automattic\WooCommerce\Internal\Admin\Onboarding;

use Automattic\WooCommerce\Admin\PageController;
use Automattic\WooCommerce\Admin\Features\OnboardingTasks\TaskLists;

/**
 * Contains backend logic for the onboarding profile and checklist feature.
 */
class OnboardingHelper {

	/**
	 * Class instance.
	 *
	 * @var OnboardingHelper instance
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
		if ( ! is_admin() ) {
			return;
		}

		add_action( 'current_screen', array( $this, 'add_help_tab' ), 60 );
		add_action( 'current_screen', array( $this, 'reset_task_list' ) );
		add_action( 'current_screen', array( $this, 'reset_extended_task_list' ) );
	}

	/**
	 * Update the help tab setup link to reset the onboarding profiler.
	 */
	public function add_help_tab() {
		if ( ! function_exists( 'wc_get_screen_ids' ) ) {
			return;
		}

		$screen = get_current_screen();

		if ( ! $screen || ! in_array( $screen->id, wc_get_screen_ids(), true ) ) {
			return;
		}

		// Remove the old help tab if it exists.
		$help_tabs = $screen->get_help_tabs();
		foreach ( $help_tabs as $help_tab ) {
			if ( 'woocommerce_onboard_tab' !== $help_tab['id'] ) {
				continue;
			}

			$screen->remove_help_tab( 'woocommerce_onboard_tab' );
		}

		// Add the new help tab.
		$help_tab = array(
			'title' => __( 'Setup wizard', 'woocommerce' ),
			'id'    => 'woocommerce_onboard_tab',
		);

		$setup_list    = TaskLists::get_list( 'setup' );
		$extended_list = TaskLists::get_list( 'extended' );

		if ( $setup_list ) {
			$help_tab['content'] = '<h2>' . __( 'WooCommerce Onboarding', 'woocommerce' ) . '</h2>';

			$help_tab['content'] .= '<h3>' . __( 'Profile Setup Wizard', 'woocommerce' ) . '</h3>';
			$help_tab['content'] .= '<p>' . __( 'If you need to access the setup wizard again, please click on the button below.', 'woocommerce' ) . '</p>' .
				'<p><a href="' . wc_admin_url( '&path=/setup-wizard' ) . '" class="button button-primary">' . __( 'Setup wizard', 'woocommerce' ) . '</a></p>';

			$help_tab['content'] .= '<h3>' . __( 'Task List', 'woocommerce' ) . '</h3>';
			$help_tab['content'] .= '<p>' . __( 'If you need to enable or disable the task lists, please click on the button below.', 'woocommerce' ) . '</p>' .
			( $setup_list->is_hidden()
				? '<p><a href="' . wc_admin_url( '&reset_task_list=1' ) . '" class="button button-primary">' . __( 'Enable', 'woocommerce' ) . '</a></p>'
				: '<p><a href="' . wc_admin_url( '&reset_task_list=0' ) . '" class="button button-primary">' . __( 'Disable', 'woocommerce' ) . '</a></p>'
			);
		}

		if ( $extended_list ) {
			$help_tab['content'] .= '<h3>' . __( 'Extended task List', 'woocommerce' ) . '</h3>';
			$help_tab['content'] .= '<p>' . __( 'If you need to enable or disable the extended task lists, please click on the button below.', 'woocommerce' ) . '</p>' .
			( $extended_list->is_hidden()
				? '<p><a href="' . wc_admin_url( '&reset_extended_task_list=1' ) . '" class="button button-primary">' . __( 'Enable', 'woocommerce' ) . '</a></p>'
				: '<p><a href="' . wc_admin_url( '&reset_extended_task_list=0' ) . '" class="button button-primary">' . __( 'Disable', 'woocommerce' ) . '</a></p>'
			);
		}

		$screen->add_help_tab( $help_tab );
	}

	/**
	 * Reset the onboarding task list and redirect to the dashboard.
	 */
	public function reset_task_list() {
		if (
			! PageController::is_admin_page() ||
			! isset( $_GET['reset_task_list'] ) // phpcs:ignore CSRF ok.
		) {
			return;
		}

		$task_list = TaskLists::get_list( 'setup' );

		if ( ! $task_list ) {
			return;
		}
		$show   = 1 === absint( $_GET['reset_task_list'] ); // phpcs:ignore CSRF ok.
		$update = $show ? $task_list->unhide() : $task_list->hide(); // phpcs:ignore CSRF ok.

		if ( $update ) {
			wc_admin_record_tracks_event(
				'tasklist_toggled',
				array(
					'status' => $show ? 'enabled' : 'disabled',
				)
			);
		}

		wp_safe_redirect( wc_admin_url() );
		exit;
	}

	/**
	 * Reset the extended task list and redirect to the dashboard.
	 */
	public function reset_extended_task_list() {
		if (
			! PageController::is_admin_page() ||
			! isset( $_GET['reset_extended_task_list'] ) // phpcs:ignore CSRF ok.
		) {
			return;
		}

		$task_list = TaskLists::get_list( 'extended' );

		if ( ! $task_list ) {
			return;
		}
		$show   = 1 === absint( $_GET['reset_extended_task_list'] ); // phpcs:ignore CSRF ok.
		$update = $show ? $task_list->unhide() : $task_list->hide(); // phpcs:ignore CSRF ok.

		if ( $update ) {
			wc_admin_record_tracks_event(
				'extended_tasklist_toggled',
				array(
					'status' => $show ? 'disabled' : 'enabled',
				)
			);
		}

		wp_safe_redirect( wc_admin_url() );
		exit;
	}
}
