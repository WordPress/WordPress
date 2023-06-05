<?php

/**
 * Abstract class with common Queue Cleaner functionality.
 */
abstract class ActionScheduler_Abstract_QueueRunner_Deprecated {

	/**
	 * Get the maximum number of seconds a batch can run for.
	 *
	 * @deprecated 2.1.1
	 * @return int The number of seconds.
	 */
	protected function get_maximum_execution_time() {
		_deprecated_function( __METHOD__, '2.1.1', 'ActionScheduler_Abstract_QueueRunner::get_time_limit()' );

		$maximum_execution_time = 30;

		// Apply deprecated filter
		if ( has_filter( 'action_scheduler_maximum_execution_time' ) ) {
			_deprecated_function( 'action_scheduler_maximum_execution_time', '2.1.1', 'action_scheduler_queue_runner_time_limit' );
			$maximum_execution_time = apply_filters( 'action_scheduler_maximum_execution_time', $maximum_execution_time );
		}

		return absint( $maximum_execution_time );
	}
}
