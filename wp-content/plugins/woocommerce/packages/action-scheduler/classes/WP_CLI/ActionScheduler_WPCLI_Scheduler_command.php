<?php

/**
 * Commands for Action Scheduler.
 */
class ActionScheduler_WPCLI_Scheduler_command extends WP_CLI_Command {

	/**
	 * Force tables schema creation for Action Scheduler
	 *
	 * ## OPTIONS
	 *
	 * @param array $args Positional arguments.
	 * @param array $assoc_args Keyed arguments.
	 *
	 * @subcommand fix-schema
	 */
	public function fix_schema( $args, $assoc_args ) {
		$schema_classes = array( ActionScheduler_LoggerSchema::class, ActionScheduler_StoreSchema::class );

		foreach ( $schema_classes as $classname ) {
			if ( is_subclass_of( $classname, ActionScheduler_Abstract_Schema::class ) ) {
				$obj = new $classname();
				$obj->init();
				$obj->register_tables( true );

				WP_CLI::success(
					sprintf(
						/* translators: %s refers to the schema name*/
						__( 'Registered schema for %s', 'woocommerce' ),
						$classname
					)
				);
			}
		}
	}

	/**
	 * Run the Action Scheduler
	 *
	 * ## OPTIONS
	 *
	 * [--batch-size=<size>]
	 * : The maximum number of actions to run. Defaults to 100.
	 *
	 * [--batches=<size>]
	 * : Limit execution to a number of batches. Defaults to 0, meaning batches will continue being executed until all actions are complete.
	 *
	 * [--cleanup-batch-size=<size>]
	 * : The maximum number of actions to clean up. Defaults to the value of --batch-size.
	 *
	 * [--hooks=<hooks>]
	 * : Only run actions with the specified hook. Omitting this option runs actions with any hook. Define multiple hooks as a comma separated string (without spaces), e.g. `--hooks=hook_one,hook_two,hook_three`
	 *
	 * [--group=<group>]
	 * : Only run actions from the specified group. Omitting this option runs actions from all groups.
	 *
	 * [--free-memory-on=<count>]
	 * : The number of actions to process between freeing memory. 0 disables freeing memory. Default 50.
	 *
	 * [--pause=<seconds>]
	 * : The number of seconds to pause when freeing memory. Default no pause.
	 *
	 * [--force]
	 * : Whether to force execution despite the maximum number of concurrent processes being exceeded.
	 *
	 * @param array $args Positional arguments.
	 * @param array $assoc_args Keyed arguments.
	 * @throws \WP_CLI\ExitException When an error occurs.
	 *
	 * @subcommand run
	 */
	public function run( $args, $assoc_args ) {
		// Handle passed arguments.
		$batch   = absint( \WP_CLI\Utils\get_flag_value( $assoc_args, 'batch-size', 100 ) );
		$batches = absint( \WP_CLI\Utils\get_flag_value( $assoc_args, 'batches', 0 ) );
		$clean   = absint( \WP_CLI\Utils\get_flag_value( $assoc_args, 'cleanup-batch-size', $batch ) );
		$hooks   = explode( ',', WP_CLI\Utils\get_flag_value( $assoc_args, 'hooks', '' ) );
		$hooks   = array_filter( array_map( 'trim', $hooks ) );
		$group   = \WP_CLI\Utils\get_flag_value( $assoc_args, 'group', '' );
		$free_on = \WP_CLI\Utils\get_flag_value( $assoc_args, 'free-memory-on', 50 );
		$sleep   = \WP_CLI\Utils\get_flag_value( $assoc_args, 'pause', 0 );
		$force   = \WP_CLI\Utils\get_flag_value( $assoc_args, 'force', false );

		ActionScheduler_DataController::set_free_ticks( $free_on );
		ActionScheduler_DataController::set_sleep_time( $sleep );

		$batches_completed = 0;
		$actions_completed = 0;
		$unlimited         = $batches === 0;

		try {
			// Custom queue cleaner instance.
			$cleaner = new ActionScheduler_QueueCleaner( null, $clean );

			// Get the queue runner instance
			$runner = new ActionScheduler_WPCLI_QueueRunner( null, null, $cleaner );

			// Determine how many tasks will be run in the first batch.
			$total = $runner->setup( $batch, $hooks, $group, $force );

			// Run actions for as long as possible.
			while ( $total > 0 ) {
				$this->print_total_actions( $total );
				$actions_completed += $runner->run();
				$batches_completed++;

				// Maybe set up tasks for the next batch.
				$total = ( $unlimited || $batches_completed < $batches ) ? $runner->setup( $batch, $hooks, $group, $force ) : 0;
			}
		} catch ( Exception $e ) {
			$this->print_error( $e );
		}

		$this->print_total_batches( $batches_completed );
		$this->print_success( $actions_completed );
	}

	/**
	 * Print WP CLI message about how many actions are about to be processed.
	 *
	 * @author Jeremy Pry
	 *
	 * @param int $total
	 */
	protected function print_total_actions( $total ) {
		WP_CLI::log(
			sprintf(
				/* translators: %d refers to how many scheduled taks were found to run */
				_n( 'Found %d scheduled task', 'Found %d scheduled tasks', $total, 'woocommerce' ),
				number_format_i18n( $total )
			)
		);
	}

	/**
	 * Print WP CLI message about how many batches of actions were processed.
	 *
	 * @author Jeremy Pry
	 *
	 * @param int $batches_completed
	 */
	protected function print_total_batches( $batches_completed ) {
		WP_CLI::log(
			sprintf(
				/* translators: %d refers to the total number of batches executed */
				_n( '%d batch executed.', '%d batches executed.', $batches_completed, 'woocommerce' ),
				number_format_i18n( $batches_completed )
			)
		);
	}

	/**
	 * Convert an exception into a WP CLI error.
	 *
	 * @author Jeremy Pry
	 *
	 * @param Exception $e The error object.
	 *
	 * @throws \WP_CLI\ExitException
	 */
	protected function print_error( Exception $e ) {
		WP_CLI::error(
			sprintf(
				/* translators: %s refers to the exception error message */
				__( 'There was an error running the action scheduler: %s', 'woocommerce' ),
				$e->getMessage()
			)
		);
	}

	/**
	 * Print a success message with the number of completed actions.
	 *
	 * @author Jeremy Pry
	 *
	 * @param int $actions_completed
	 */
	protected function print_success( $actions_completed ) {
		WP_CLI::success(
			sprintf(
				/* translators: %d refers to the total number of taskes completed */
				_n( '%d scheduled task completed.', '%d scheduled tasks completed.', $actions_completed, 'woocommerce' ),
				number_format_i18n( $actions_completed )
			)
		);
	}
}
