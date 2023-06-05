<?php
/**
 * Handles storage and retrieval of task lists
 */

namespace Automattic\WooCommerce\Admin\Features\OnboardingTasks;

use Automattic\WooCommerce\Admin\Features\Features;
use Automattic\WooCommerce\Admin\Features\OnboardingTasks\DeprecatedExtendedTask;
use Automattic\WooCommerce\Admin\Features\OnboardingTasks\Task;
use Automattic\WooCommerce\Admin\Features\OnboardingTasks\Tasks\ReviewShippingOptions;
use Automattic\WooCommerce\Admin\Features\OnboardingTasks\Tasks\TourInAppMarketplace;
/**
 * Task Lists class.
 */
class TaskLists {
	/**
	 * Class instance.
	 *
	 * @var TaskLists instance
	 */
	protected static $instance = null;

	/**
	 * An array of all registered lists.
	 *
	 * @var array
	 */
	protected static $lists = array();

	/**
	 * Boolean value to indicate if default tasks have been added.
	 *
	 * @var boolean
	 */
	protected static $default_tasks_loaded = false;

	/**
	 * Array of default tasks.
	 *
	 * @var array
	 */
	const DEFAULT_TASKS = array(
		'StoreDetails',
		'Purchase',
		'Products',
		'WooCommercePayments',
		'Payments',
		'Tax',
		'Shipping',
		'Marketing',
		'Appearance',
		'AdditionalPayments',
		'ReviewShippingOptions',
		'GetMobileApp',
		'TourInAppMarketplace',
	);

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
	 * Initialize the task lists.
	 */
	public static function init() {
		self::init_default_lists();
		add_action( 'admin_init', array( __CLASS__, 'set_active_task' ), 5 );
		add_action( 'init', array( __CLASS__, 'init_tasks' ) );
		add_action( 'admin_menu', array( __CLASS__, 'menu_task_count' ) );
		add_filter( 'woocommerce_admin_shared_settings', array( __CLASS__, 'task_list_preloaded_settings' ), 20 );
	}

	/**
	 * Check if an experiment is the treatment or control.
	 *
	 * @param string $name Name prefix of experiment.
	 * @return bool
	 */
	public static function is_experiment_treatment( $name ) {
		$anon_id        = isset( $_COOKIE['tk_ai'] ) ? sanitize_text_field( wp_unslash( $_COOKIE['tk_ai'] ) ) : '';
		$allow_tracking = 'yes' === get_option( 'woocommerce_allow_tracking' );
		$abtest         = new \WooCommerce\Admin\Experimental_Abtest(
			$anon_id,
			'woocommerce',
			$allow_tracking
		);

		$date = new \DateTime();
		$date->setTimeZone( new \DateTimeZone( 'UTC' ) );

		$experiment_name = sprintf(
			'%s_%s_%s',
			$name,
			$date->format( 'Y' ),
			$date->format( 'm' )
		);
		return $abtest->get_variation( $experiment_name ) === 'treatment';
	}

	/**
	 * Initialize default lists.
	 */
	public static function init_default_lists() {
		self::add_list(
			array(
				'id'                      => 'setup',
				'title'                   => __( 'Get ready to start selling', 'woocommerce' ),
				'tasks'                   => array(
					'StoreDetails',
					'Purchase',
					'Products',
					'WooCommercePayments',
					'Payments',
					'Tax',
					'Shipping',
					'Marketing',
					'Appearance',
				),
				'display_progress_header' => true,
				'event_prefix'            => 'tasklist_',
				'options'                 => array(
					'use_completed_title' => true,
				),
				'visible'                 => true,
			)
		);

		self::add_list(
			array(
				'id'      => 'extended',
				'title'   => __( 'Things to do next', 'woocommerce' ),
				'sort_by' => array(
					array(
						'key'   => 'is_complete',
						'order' => 'asc',
					),
					array(
						'key'   => 'level',
						'order' => 'asc',
					),
				),
				'tasks'   => array(
					'AdditionalPayments',
					'GetMobileApp',
				),
			)
		);
		self::add_list(
			array(
				'id'           => 'setup_two_column',
				'hidden_id'    => 'setup',
				'title'        => __( 'Get ready to start selling', 'woocommerce' ),
				'tasks'        => array(
					'Products',
					'WooCommercePayments',
					'Payments',
					'Tax',
					'Shipping',
					'Marketing',
					'Appearance',
				),
				'event_prefix' => 'tasklist_',
			)
		);
		self::add_list(
			array(
				'id'           => 'extended_two_column',
				'hidden_id'    => 'extended',
				'title'        => __( 'Things to do next', 'woocommerce' ),
				'sort_by'      => array(
					array(
						'key'   => 'is_complete',
						'order' => 'asc',
					),
					array(
						'key'   => 'level',
						'order' => 'asc',
					),
				),
				'tasks'        => array(
					'AdditionalPayments',
					'GetMobileApp',
				),
				'event_prefix' => 'extended_tasklist_',
			)
		);

		if ( Features::is_enabled( 'shipping-smart-defaults' ) ) {
			self::add_task(
				'extended',
				new ReviewShippingOptions(
					self::get_list( 'extended' )
				)
			);

			self::add_task(
				'extended_two_column',
				new ReviewShippingOptions(
					self::get_list( 'extended_two_column' )
				)
			);

			// Tasklist that will never be shown in homescreen,
			// used for having tasks that are accessed by other means.
			self::add_list(
				array(
					'id'           => 'secret_tasklist',
					'hidden_id'    => 'setup',
					'tasks'        => array(
						'ExperimentalShippingRecommendation',
					),
					'event_prefix' => 'secret_tasklist_',
					'visible'      => false,
				)
			);
		}

		if ( ! wp_is_mobile() ) { // Permit In-App Marketplace Tour on desktops only.
			$tour_task = new TourInAppMarketplace();
			self::add_task( 'extended', $tour_task );
			self::add_task( 'extended_two_column', $tour_task );
		}

		if ( has_filter( 'woocommerce_admin_experimental_onboarding_tasklists' ) ) {
			/**
			 * Filter to override default task lists.
			 *
			 * @since 7.4
			 * @param array     $lists Array of tasklists.
			 */
			self::$lists = apply_filters( 'woocommerce_admin_experimental_onboarding_tasklists', self::$lists );
		}
	}

	/**
	 * Initialize tasks.
	 */
	public static function init_tasks() {
		foreach ( self::DEFAULT_TASKS as $task ) {
			$class = 'Automattic\WooCommerce\Admin\Features\OnboardingTasks\Tasks\\' . $task;
			if ( ! method_exists( $class, 'init' ) ) {
				continue;
			}
			$class::init();
		}
	}

	/**
	 * Temporarily store the active task to persist across page loads when neccessary.
	 * Most tasks do not need this.
	 */
	public static function set_active_task() {
		if ( ! isset( $_GET[ Task::ACTIVE_TASK_TRANSIENT ] ) || ! current_user_can( 'manage_woocommerce' ) ) { // phpcs:ignore csrf ok.
			return;
		}
		$referer = wp_get_referer();
		if ( ! $referer || 0 !== strpos( $referer, wc_admin_url() ) ) {
			return;
		}

		$task_id = sanitize_title_with_dashes( wp_unslash( $_GET[ Task::ACTIVE_TASK_TRANSIENT ] ) ); // phpcs:ignore csrf ok.

		$task = self::get_task( $task_id );

		if ( ! $task ) {
			return;
		}

		$task->set_active();
	}

	/**
	 * Add a task list.
	 *
	 * @param array $args Task list properties.
	 * @return \WP_Error|TaskList
	 */
	public static function add_list( $args ) {
		if ( isset( self::$lists[ $args['id'] ] ) ) {
			return new \WP_Error(
				'woocommerce_task_list_exists',
				__( 'Task list ID already exists', 'woocommerce' )
			);
		}

		self::$lists[ $args['id'] ] = new TaskList( $args );
		return self::$lists[ $args['id'] ];
	}

	/**
	 * Add task to a given task list.
	 *
	 * @param string $list_id List ID to add the task to.
	 * @param Task   $task Task object.
	 *
	 * @return \WP_Error|Task
	 */
	public static function add_task( $list_id, $task ) {
		if ( ! isset( self::$lists[ $list_id ] ) ) {
			return new \WP_Error(
				'woocommerce_task_list_invalid_list',
				__( 'Task list ID does not exist', 'woocommerce' )
			);
		}

		self::$lists[ $list_id ]->add_task( $task );
	}

	/**
	 * Add default extended task lists.
	 *
	 * @param array $extended_tasks list of extended tasks.
	 */
	public static function maybe_add_extended_tasks( $extended_tasks ) {
		$tasks = $extended_tasks ?? array();

		foreach ( self::$lists as $task_list ) {
			if ( 'extended' !== substr( $task_list->id, 0, 8 ) ) {
				continue;
			}
			foreach ( $tasks as $args ) {
				$task = new DeprecatedExtendedTask( $task_list, $args );
				$task_list->add_task( $task );
			}
		}

	}

	/**
	 * Get all task lists.
	 *
	 * @return array
	 */
	public static function get_lists() {
		return self::$lists;
	}

	/**
	 * Get all task lists.
	 *
	 * @param array $ids list of task list ids.
	 * @return array
	 */
	public static function get_lists_by_ids( $ids ) {
		return array_filter(
			self::$lists,
			function( $list ) use ( $ids ) {
				return in_array( $list->get_list_id(), $ids, true );
			}
		);
	}

	/**
	 * Get all task list ids.
	 *
	 * @return array
	 */
	public static function get_list_ids() {
		return array_keys( self::$lists );
	}

	/**
	 * Clear all task lists.
	 */
	public static function clear_lists() {
		self::$lists = array();
		return self::$lists;
	}

	/**
	 * Get visible task lists.
	 */
	public static function get_visible() {
		return array_filter(
			self::get_lists(),
			function ( $task_list ) {
				return $task_list->is_visible();
			}
		);
	}


	/**
	 * Retrieve a task list by ID.
	 *
	 * @param String $id Task list ID.
	 *
	 * @return TaskList|null
	 */
	public static function get_list( $id ) {
		if ( isset( self::$lists[ $id ] ) ) {
			return self::$lists[ $id ];
		}

		return null;
	}

	/**
	 * Retrieve single task.
	 *
	 * @param String $id Task ID.
	 * @param String $task_list_id Task list ID.
	 *
	 * @return Object
	 */
	public static function get_task( $id, $task_list_id = null ) {
		$task_list = $task_list_id ? self::get_list( $task_list_id ) : null;

		if ( $task_list_id && ! $task_list ) {
			return null;
		}

		$tasks_to_search = $task_list ? $task_list->tasks : array_reduce(
			self::get_lists(),
			function ( $all, $curr ) {
				return array_merge( $all, $curr->tasks );
			},
			array()
		);

		foreach ( $tasks_to_search as $task ) {
			if ( $id === $task->get_id() ) {
				return $task;
			}
		}

		return null;
	}

	/**
	 * Return number of setup tasks remaining
	 *
	 * @return number
	 */
	public static function setup_tasks_remaining() {
		$setup_list = self::get_list( 'setup' );

		if ( ! $setup_list || $setup_list->is_hidden() || $setup_list->is_complete() ) {
			return;
		}

		$remaining_tasks = array_values(
			array_filter(
				$setup_list->get_viewable_tasks(),
				function( $task ) {
					return ! $task->is_complete();
				}
			)
		);

		return count( $remaining_tasks );
	}

	/**
	 * Add badge to homescreen menu item for remaining tasks
	 */
	public static function menu_task_count() {
		global $submenu;

		$tasks_count = self::setup_tasks_remaining();

		if ( ! $tasks_count || ! isset( $submenu['woocommerce'] ) ) {
			return;
		}

		foreach ( $submenu['woocommerce'] as $key => $menu_item ) {
			if ( 0 === strpos( $menu_item[0], _x( 'Home', 'Admin menu name', 'woocommerce' ) ) ) {
				$submenu['woocommerce'][ $key ][0] .= ' <span class="awaiting-mod update-plugins remaining-tasks-badge count-' . esc_attr( $tasks_count ) . '">' . number_format_i18n( $tasks_count ) . '</span>'; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
				break;
			}
		}

	}

	/**
	 * Add visible list ids to component settings.
	 *
	 * @param array $settings Component settings.
	 *
	 * @return array
	 */
	public static function task_list_preloaded_settings( $settings ) {
		$settings['visibleTaskListIds'] = array_keys( self::get_visible() );

		return $settings;
	}
}
