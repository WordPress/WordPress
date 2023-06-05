<?php

/**
 * Class ActionScheduler_CanceledAction
 *
 * Stored action which was canceled and therefore acts like a finished action but should always return a null schedule,
 * regardless of schedule passed to its constructor.
 */
class ActionScheduler_CanceledAction extends ActionScheduler_FinishedAction {

	/**
	 * @param string $hook
	 * @param array $args
	 * @param ActionScheduler_Schedule $schedule
	 * @param string $group
	 */
	public function __construct( $hook, array $args = array(), ActionScheduler_Schedule $schedule = null, $group = '' ) {
		parent::__construct( $hook, $args, $schedule, $group );
		if ( is_null( $schedule ) ) {
			$this->set_schedule( new ActionScheduler_NullSchedule() );
		}
	}
}
