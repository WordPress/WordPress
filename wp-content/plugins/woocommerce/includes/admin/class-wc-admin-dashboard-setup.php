<?php
/**
 * Admin Dashboard - Setup
 *
 * @package     WooCommerce\Admin
 * @version     2.1.0
 */

use Automattic\Jetpack\Constants;
use Automattic\WooCommerce\Admin\Features\Features;
use Automattic\WooCommerce\Admin\Features\OnboardingTasks\TaskLists;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'WC_Admin_Dashboard_Setup', false ) ) :

	/**
	 * WC_Admin_Dashboard_Setup Class.
	 */
	class WC_Admin_Dashboard_Setup {

		/**
		 * Check for task list initialization.
		 */
		private $initalized = false;

		/**
		 * The task list.
		 */
		private $task_list = null;

		/**
		 * The tasks.
		 */
		private $tasks = null;

		/**
		 * # of completed tasks.
		 *
		 * @var int
		 */
		private $completed_tasks_count = 0;

		/**
		 * WC_Admin_Dashboard_Setup constructor.
		 */
		public function __construct() {
			if ( $this->should_display_widget() ) {
				add_meta_box(
					'wc_admin_dashboard_setup',
					__( 'WooCommerce Setup', 'woocommerce' ),
					array( $this, 'render' ),
					'dashboard',
					'normal',
					'high'
				);
			}
		}

		/**
		 * Render meta box output.
		 */
		public function render() {
			$version = Constants::get_constant( 'WC_VERSION' );
			wp_enqueue_style( 'wc-dashboard-setup', WC()->plugin_url() . '/assets/css/dashboard-setup.css', array(), $version );

			$task = $this->get_next_task();
			if ( ! $task ) {
				return;
			}

			$button_link           = $this->get_button_link( $task );
			$completed_tasks_count = $this->get_completed_tasks_count();
			$step_number           = $this->get_completed_tasks_count() + 1;
			$tasks_count           = count( $this->get_tasks() );

			// Given 'r' (circle element's r attr), dashoffset = ((100-$desired_percentage)/100) * PI * (r*2).
			$progress_percentage = ( $completed_tasks_count / $tasks_count ) * 100;
			$circle_r            = 6.5;
			$circle_dashoffset   = ( ( 100 - $progress_percentage ) / 100 ) * ( pi() * ( $circle_r * 2 ) );

			include __DIR__ . '/views/html-admin-dashboard-setup.php';
		}

		/**
		 * Get the button link for a given task.
		 *
		 * @param Task $task Task.
		 * @return string
		 */
		public function get_button_link( $task ) {
			$url = $task->get_json()['actionUrl'];

			if ( substr( $url, 0, 4 ) === 'http' ) {
				return $url;
			} elseif ( $url ) {
				return wc_admin_url( '&path=' . $url );
			}

			return admin_url( 'admin.php?page=wc-admin&task=' . $task->get_id() );
		}

		/**
		 * Get the task list.
		 *
		 * @return array
		 */
		public function get_task_list() {
			if ( $this->task_list || $this->initalized ) {
				return $this->task_list;
			}

			$this->set_task_list( TaskLists::get_list( 'setup' ) );
			$this->initalized = true;
			return $this->task_list;
		}

		/**
		 * Set the task list.
		 */
		public function set_task_list( $task_list ) {
			return $this->task_list = $task_list;
		}

		/**
		 * Get the tasks.
		 *
		 * @return array
		 */
		public function get_tasks() {
			if ( $this->tasks ) {
				return $this->tasks;
			}

			$this->tasks = $this->get_task_list()->get_viewable_tasks();
			return $this->tasks;
		}

		/**
		 * Return # of completed tasks
		 *
		 * @return integer
		 */
		public function get_completed_tasks_count() {
			$completed_tasks = array_filter(
				$this->get_tasks(),
				function( $task ) {
					return $task->is_complete();
				}
			);

			return count( $completed_tasks );
		}

		/**
		 * Get the next task.
		 *
		 * @return array|null
		 */
		private function get_next_task() {
			foreach ( $this->get_tasks() as $task ) {
				if ( false === $task->is_complete() ) {
					return $task;
				}
			}

			return null;
		}

		/**
		 * Check to see if we should display the widget
		 *
		 * @return bool
		 */
		public function should_display_widget() {
			if ( ! class_exists( 'Automattic\WooCommerce\Admin\Features\Features' ) || ! class_exists( 'Automattic\WooCommerce\Admin\Features\OnboardingTasks\TaskLists' ) ) {
				return false;
			}

			if ( ! Features::is_enabled( 'onboarding' ) || ! WC()->is_wc_admin_active() ) {
				return false;
			}

			if ( ! current_user_can( 'manage_woocommerce' ) ) {
				return false;
			}

			if ( ! $this->get_task_list() || $this->get_task_list()->is_hidden() || $this->get_task_list()->is_complete() ) {
				return false;
			}

			return true;
		}

	}

endif;

return new WC_Admin_Dashboard_Setup();
