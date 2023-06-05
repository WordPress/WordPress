<?php


namespace Action_Scheduler\Migration;

/**
 * Class Scheduler
 *
 * @package Action_Scheduler\WP_CLI
 *
 * @since 3.0.0
 *
 * @codeCoverageIgnore
 */
class Scheduler {
	/** Migration action hook. */
	const HOOK            = 'action_scheduler/migration_hook';

	/** Migration action group. */
	const GROUP           = 'action-scheduler-migration';

	/**
	 * Set up the callback for the scheduled job.
	 */
	public function hook() {
		add_action( self::HOOK, array( $this, 'run_migration' ), 10, 0 );
	}

	/**
	 * Remove the callback for the scheduled job.
	 */
	public function unhook() {
		remove_action( self::HOOK, array( $this, 'run_migration' ), 10 );
	}

	/**
	 * The migration callback.
	 */
	public function run_migration() {
		$migration_runner = $this->get_migration_runner();
		$count            = $migration_runner->run( $this->get_batch_size() );

		if ( $count === 0 ) {
			$this->mark_complete();
		} else {
			$this->schedule_migration( time() + $this->get_schedule_interval() );
		}
	}

	/**
	 * Mark the migration complete.
	 */
	public function mark_complete() {
		$this->unschedule_migration();

		\ActionScheduler_DataController::mark_migration_complete();
		do_action( 'action_scheduler/migration_complete' );
	}

	/**
	 * Get a flag indicating whether the migration is scheduled.
	 *
	 * @return bool Whether there is a pending action in the store to handle the migration
	 */
	public function is_migration_scheduled() {
		$next = as_next_scheduled_action( self::HOOK );

		return ! empty( $next );
	}

	/**
	 * Schedule the migration.
	 *
	 * @param int $when Optional timestamp to run the next migration batch. Defaults to now.
	 *
	 * @return string The action ID
	 */
	public function schedule_migration( $when = 0 ) {
		$next = as_next_scheduled_action( self::HOOK );

		if ( ! empty( $next ) ) {
			return $next;
		}

		if ( empty( $when ) ) {
			$when = time() + MINUTE_IN_SECONDS;
		}

		return as_schedule_single_action( $when, self::HOOK, array(), self::GROUP );
	}

	/**
	 * Remove the scheduled migration action.
	 */
	public function unschedule_migration() {
		as_unschedule_action( self::HOOK, null, self::GROUP );
	}

	/**
	 * Get migration batch schedule interval.
	 *
	 * @return int Seconds between migration runs. Defaults to 0 seconds to allow chaining migration via Async Runners.
	 */
	private function get_schedule_interval() {
		return (int) apply_filters( 'action_scheduler/migration_interval', 0 );
	}

	/**
	 * Get migration batch size.
	 *
	 * @return int Number of actions to migrate in each batch. Defaults to 250.
	 */
	private function get_batch_size() {
		return (int) apply_filters( 'action_scheduler/migration_batch_size', 250 );
	}

	/**
	 * Get migration runner object.
	 *
	 * @return Runner
	 */
	private function get_migration_runner() {
		$config = Controller::instance()->get_migration_config_object();

		return new Runner( $config );
	}

}
