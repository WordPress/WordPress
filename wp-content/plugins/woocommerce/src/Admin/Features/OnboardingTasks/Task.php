<?php
/**
 * Handles task related methods.
 */

namespace Automattic\WooCommerce\Admin\Features\OnboardingTasks;

use Automattic\WooCommerce\Internal\Admin\WCAdminUser;

/**
 * Task class.
 */
abstract class Task {
	/**
	 * Task traits.
	 */
	use TaskTraits;

	/**
	 * Name of the dismiss option.
	 *
	 * @var string
	 */
	const DISMISSED_OPTION = 'woocommerce_task_list_dismissed_tasks';

	/**
	 * Name of the snooze option.
	 *
	 * @var string
	 *
	 * @deprecated 7.2.0
	 */
	const SNOOZED_OPTION = 'woocommerce_task_list_remind_me_later_tasks';

	/**
	 * Name of the actioned option.
	 *
	 * @var string
	 */
	const ACTIONED_OPTION = 'woocommerce_task_list_tracked_completed_actions';

	/**
	 * Option name of completed tasks.
	 *
	 * @var string
	 */
	const COMPLETED_OPTION = 'woocommerce_task_list_tracked_completed_tasks';

	/**
	 * Name of the active task transient.
	 *
	 * @var string
	 */
	const ACTIVE_TASK_TRANSIENT = 'wc_onboarding_active_task';

	/**
	 * Parent task list.
	 *
	 * @var TaskList
	 */
	protected $task_list;

	/**
	 * Duration to milisecond mapping.
	 *
	 * @var string
	 */
	protected $duration_to_ms = array(
		'day'  => DAY_IN_SECONDS * 1000,
		'hour' => HOUR_IN_SECONDS * 1000,
		'week' => WEEK_IN_SECONDS * 1000,
	);

	/**
	 * Constructor
	 *
	 * @param TaskList|null $task_list Parent task list.
	 */
	public function __construct( $task_list = null ) {
		$this->task_list = $task_list;
	}

	/**
	 * ID.
	 *
	 * @return string
	 */
	abstract public function get_id();

	/**
	 * Title.
	 *
	 * @return string
	 */
	abstract public function get_title();

	/**
	 * Content.
	 *
	 * @return string
	 */
	abstract public function get_content();

	/**
	 * Time.
	 *
	 * @return string
	 */
	abstract public function get_time();

	/**
	 * Parent ID.
	 *
	 * @return string
	 */
	public function get_parent_id() {
		if ( ! $this->task_list ) {
			return '';
		}
		return $this->task_list->get_list_id();
	}

	/**
	 * Get task list options.
	 *
	 * @return array
	 */
	public function get_parent_options() {
		if ( ! $this->task_list ) {
			return array();
		}
		return $this->task_list->options;
	}

	/**
	 * Get custom option.
	 *
	 * @param string $option_name name of custom option.
	 * @return mixed|null
	 */
	public function get_parent_option( $option_name ) {
		if ( $this->task_list && isset( $this->task_list->options[ $option_name ] ) ) {
			return $this->task_list->options[ $option_name ];
		}
		return null;
	}


	/**
	 * Prefix event for track event naming.
	 *
	 * @param string $event_name Event name.
	 * @return string
	 */
	public function prefix_event( $event_name ) {
		if ( ! $this->task_list ) {
			return '';
		}
		return $this->task_list->prefix_event( $event_name );
	}

	/**
	 * Additional info.
	 *
	 * @return string
	 */
	public function get_additional_info() {
		return '';
	}

	/**
	 * Additional data.
	 *
	 * @return mixed
	 */
	public function get_additional_data() {
		return null;
	}

	/**
	 * Level.
	 *
	 * @deprecated 7.2.0
	 *
	 * @return string
	 */
	public function get_level() {
		wc_deprecated_function( __CLASS__ . '::' . __FUNCTION__, '7.2.0' );

		return 3;
	}

	/**
	 * Action label.
	 *
	 * @return string
	 */
	public function get_action_label() {
		return __( "Let's go", 'woocommerce' );
	}

	/**
	 * Action URL.
	 *
	 * @return string
	 */
	public function get_action_url() {
		return null;
	}

	/**
	 * Check if a task is dismissable.
	 *
	 * @return bool
	 */
	public function is_dismissable() {
		return false;
	}

	/**
	 * Bool for task dismissal.
	 *
	 * @return bool
	 */
	public function is_dismissed() {
		if ( ! $this->is_dismissable() ) {
			return false;
		}

		$dismissed = get_option( self::DISMISSED_OPTION, array() );

		return in_array( $this->get_id(), $dismissed, true );
	}

	/**
	 * Dismiss the task.
	 *
	 * @return bool
	 */
	public function dismiss() {
		if ( ! $this->is_dismissable() ) {
			return false;
		}

		$dismissed   = get_option( self::DISMISSED_OPTION, array() );
		$dismissed[] = $this->get_id();
		$update      = update_option( self::DISMISSED_OPTION, array_unique( $dismissed ) );

		if ( $update ) {
			$this->record_tracks_event( 'dismiss_task', array( 'task_name' => $this->get_id() ) );
		}

		return $update;
	}

	/**
	 * Undo task dismissal.
	 *
	 * @return bool
	 */
	public function undo_dismiss() {
		$dismissed = get_option( self::DISMISSED_OPTION, array() );
		$dismissed = array_diff( $dismissed, array( $this->get_id() ) );
		$update    = update_option( self::DISMISSED_OPTION, $dismissed );

		if ( $update ) {
			$this->record_tracks_event( 'undo_dismiss_task', array( 'task_name' => $this->get_id() ) );
		}

		return $update;
	}

	/**
	 * Check if a task is snoozeable.
	 *
	 * @deprecated 7.2.0
	 *
	 * @return bool
	 */
	public function is_snoozeable() {
		wc_deprecated_function( __CLASS__ . '::' . __FUNCTION__, '7.2.0' );

		return false;
	}

	/**
	 * Get the snoozed until datetime.
	 *
	 * @deprecated 7.2.0
	 *
	 * @return string
	 */
	public function get_snoozed_until() {
		wc_deprecated_function( __CLASS__ . '::' . __FUNCTION__, '7.2.0' );

		$snoozed_tasks = get_option( self::SNOOZED_OPTION, array() );
		if ( isset( $snoozed_tasks[ $this->get_id() ] ) ) {
			return $snoozed_tasks[ $this->get_id() ];
		}

		return null;
	}

	/**
	 * Bool for task snoozed.
	 *
	 * @deprecated 7.2.0
	 *
	 * @return bool
	 */
	public function is_snoozed() {
		wc_deprecated_function( __CLASS__ . '::' . __FUNCTION__, '7.2.0' );

		if ( ! $this->is_snoozeable() ) {
			return false;
		}

		$snoozed = get_option( self::SNOOZED_OPTION, array() );

		return isset( $snoozed[ $this->get_id() ] ) && $snoozed[ $this->get_id() ] > ( time() * 1000 );
	}

	/**
	 * Snooze the task.
	 *
	 * @param string $duration Duration to snooze. day|hour|week.
	 *
	 * @deprecated 7.2.0
	 *
	 * @return bool
	 */
	public function snooze( $duration = 'day' ) {
		wc_deprecated_function( __CLASS__ . '::' . __FUNCTION__, '7.2.0' );

		if ( ! $this->is_snoozeable() ) {
			return false;
		}

		$snoozed                    = get_option( self::SNOOZED_OPTION, array() );
		$snoozed_until              = $this->duration_to_ms[ $duration ] + ( time() * 1000 );
		$snoozed[ $this->get_id() ] = $snoozed_until;
		$update                     = update_option( self::SNOOZED_OPTION, $snoozed );

		if ( $update ) {
			if ( $update ) {
				$this->record_tracks_event( 'remindmelater_task', array( 'task_name' => $this->get_id() ) );
			}
		}

		return $update;
	}

	/**
	 * Undo task snooze.
	 *
	 * @deprecated 7.2.0
	 *
	 * @return bool
	 */
	public function undo_snooze() {
		wc_deprecated_function( __CLASS__ . '::' . __FUNCTION__, '7.2.0' );

		$snoozed = get_option( self::SNOOZED_OPTION, array() );
		unset( $snoozed[ $this->get_id() ] );
		$update = update_option( self::SNOOZED_OPTION, $snoozed );

		if ( $update ) {
			$this->record_tracks_event( 'undo_remindmelater_task', array( 'task_name' => $this->get_id() ) );
		}

		return $update;
	}

	/**
	 * Check if a task list has previously been marked as complete.
	 *
	 * @return bool
	 */
	public function has_previously_completed() {
		$complete = get_option( self::COMPLETED_OPTION, array() );
		return in_array( $this->get_id(), $complete, true );
	}

	/**
	 * Track task completion if task is viewable.
	 */
	public function possibly_track_completion() {
		if ( ! $this->is_complete() ) {
			return;
		}

		if ( $this->has_previously_completed() ) {
			return;
		}

		$completed_tasks   = get_option( self::COMPLETED_OPTION, array() );
		$completed_tasks[] = $this->get_id();
		update_option( self::COMPLETED_OPTION, $completed_tasks );
		$this->record_tracks_event( 'task_completed', array( 'task_name' => $this->get_id() ) );
	}

	/**
	 * Set this as the active task across page loads.
	 */
	public function set_active() {
		if ( $this->is_complete() ) {
			return;
		}

		set_transient(
			self::ACTIVE_TASK_TRANSIENT,
			$this->get_id(),
			DAY_IN_SECONDS
		);
	}

	/**
	 * Check if this is the active task.
	 */
	public function is_active() {
		return get_transient( self::ACTIVE_TASK_TRANSIENT ) === $this->get_id();
	}

	/**
	 * Check if the store is capable of viewing the task.
	 *
	 * @return bool
	 */
	public function can_view() {
		return true;
	}

	/**
	 * Check if task is disabled.
	 *
	 * @deprecated 7.2.0
	 *
	 * @return bool
	 */
	public function is_disabled() {
		wc_deprecated_function( __CLASS__ . '::' . __FUNCTION__, '7.2.0' );

		return false;
	}

	/**
	 * Check if the task is complete.
	 *
	 * @return bool
	 */
	public function is_complete() {
		return self::is_actioned();
	}

	/**
	 * Check if the task has been visited.
	 *
	 * @return bool
	 */
	public function is_visited() {
		$user_id       = get_current_user_id();
		$response      = WCAdminUser::get_user_data_field( $user_id, 'task_list_tracked_started_tasks' );
		$tracked_tasks = $response ? json_decode( $response, true ) : array();

		return isset( $tracked_tasks[ $this->get_id() ] ) && $tracked_tasks[ $this->get_id() ] > 0;
	}

	/**
	 * Check if should record event when task is viewed
	 *
	 * @return bool
	 */
	public function get_record_view_event(): bool {
		return false;
	}

	/**
	 * Get the task as JSON.
	 *
	 * @return array
	 */
	public function get_json() {
		$this->possibly_track_completion();

		return array(
			'id'              => $this->get_id(),
			'parentId'        => $this->get_parent_id(),
			'title'           => $this->get_title(),
			'canView'         => $this->can_view(),
			'content'         => $this->get_content(),
			'additionalInfo'  => $this->get_additional_info(),
			'actionLabel'     => $this->get_action_label(),
			'actionUrl'       => $this->get_action_url(),
			'isComplete'      => $this->is_complete(),
			'time'            => $this->get_time(),
			'level'           => 3,
			'isActioned'      => $this->is_actioned(),
			'isDismissed'     => $this->is_dismissed(),
			'isDismissable'   => $this->is_dismissable(),
			'isSnoozed'       => false,
			'isSnoozeable'    => false,
			'isVisited'       => $this->is_visited(),
			'isDisabled'      => false,
			'snoozedUntil'    => null,
			'additionalData'  => self::convert_object_to_camelcase( $this->get_additional_data() ),
			'eventPrefix'     => $this->prefix_event( '' ),
			'recordViewEvent' => $this->get_record_view_event(),
		);
	}

	/**
	 * Convert object keys to camelcase.
	 *
	 * @param array $data Data to convert.
	 * @return object
	 */
	public static function convert_object_to_camelcase( $data ) {
		if ( ! is_array( $data ) ) {
			return $data;
		}

		$new_object = (object) array();

		foreach ( $data as $key => $value ) {
			$new_key              = lcfirst( implode( '', array_map( 'ucfirst', explode( '_', $key ) ) ) );
			$new_object->$new_key = $value;
		}

		return $new_object;
	}

	/**
	 * Mark a task as actioned.  Used to verify an action has taken place in some tasks.
	 *
	 * @return bool
	 */
	public function mark_actioned() {
		$actioned = get_option( self::ACTIONED_OPTION, array() );

		$actioned[] = $this->get_id();
		$update     = update_option( self::ACTIONED_OPTION, array_unique( $actioned ) );

		if ( $update ) {
			$this->record_tracks_event( 'actioned_task', array( 'task_name' => $this->get_id() ) );
		}

		return $update;
	}

	/**
	 * Check if a task has been actioned.
	 *
	 * @return bool
	 */
	public function is_actioned() {
		return self::is_task_actioned( $this->get_id() );
	}

	/**
	 * Check if a provided task ID has been actioned.
	 *
	 * @param string $id Task ID.
	 * @return bool
	 */
	public static function is_task_actioned( $id ) {
		$actioned = get_option( self::ACTIONED_OPTION, array() );
		return in_array( $id, $actioned, true );
	}

	/**
	 * Sorting function for tasks.
	 *
	 * @param Task  $a Task a.
	 * @param Task  $b Task b.
	 * @param array $sort_by list of columns with sort order.
	 * @return int
	 */
	public static function sort( $a, $b, $sort_by = array() ) {
		$result = 0;
		foreach ( $sort_by as $data ) {
			$key   = $data['key'];
			$a_val = $a->$key ?? false;
			$b_val = $b->$key ?? false;
			if ( 'asc' === $data['order'] ) {
				$result = $a_val <=> $b_val;
			} else {
				$result = $b_val <=> $a_val;
			}

			if ( 0 !== $result ) {
				break;
			}
		}
		return $result;
	}

}
