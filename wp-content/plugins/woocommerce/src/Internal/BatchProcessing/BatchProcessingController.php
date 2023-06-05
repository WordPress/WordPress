<?php
/**
 * This class is a helper intended to handle data processings that need to happen in batches in a deferred way.
 * It abstracts away the nuances of (re)scheduling actions and dealing with errors.
 *
 * Usage:
 *
 * 1. Create a class that implements BatchProcessorInterface.
 *    The class must either be registered in the dependency injection container, or have a public parameterless constructor,
 *    or an instance must be provided via the 'woocommerce_get_batch_processor' filter.
 * 2. Whenever there's data to be processed invoke the 'enqueue_processor' method in this class,
 *    passing the class name of the processor.
 *
 * That's it, processing will be performed in batches inside scheduled actions; enqueued processors will only
 * be dequeued once they notify that no more items are left to process (or when `force_clear_all_processes` is invoked).
 * Failed batches will be retried after a while.
 *
 * There are also a few public methods to get the list of currently enqueued processors
 * and to check if a given processor is enqueued/actually scheduled.
 */

namespace Automattic\WooCommerce\Internal\BatchProcessing;

/**
 * Class BatchProcessingController
 *
 * @package Automattic\WooCommerce\Internal\Updates.
 */
class BatchProcessingController {
	/*
	 * Identifier of a "watchdog" action that will schedule a processing action
	 * for any processor that is enqueued but not yet scheduled
	 * (because it's been just enqueued or because it threw an error while processing a batch),
	 * that's one single action that reschedules itself continuously.
	 */
	const WATCHDOG_ACTION_NAME = 'wc_schedule_pending_batch_processes';

	/*
	 * Identifier of the action that will do the actual batch processing.
	 * There's one action per enqueued processor that will keep rescheduling itself
	 * as long as there are still pending items to process
	 * (except if there's an error that caused no items to be processed at all).
	 */
	const PROCESS_SINGLE_BATCH_ACTION_NAME = 'wc_run_batch_process';

	const ENQUEUED_PROCESSORS_OPTION_NAME = 'wc_pending_batch_processes';
	const ACTION_GROUP                    = 'wc_batch_processes';

	/**
	 * Instance of WC_Logger class.
	 *
	 * @var \WC_Logger_Interface
	 */
	private $logger;

	/**
	 * BatchProcessingController constructor.
	 *
	 * Schedules the necessary actions to process batches.
	 */
	public function __construct() {
		add_action(
			self::WATCHDOG_ACTION_NAME,
			function () {
				$this->handle_watchdog_action();
			}
		);

		add_action(
			self::PROCESS_SINGLE_BATCH_ACTION_NAME,
			function ( $batch_process ) {
				$this->process_next_batch_for_single_processor( $batch_process );
			},
			10,
			2
		);

		$this->logger = wc_get_logger();
	}

	/**
	 * Enqueue a processor so that it will get batch processing requests from within scheduled actions.
	 *
	 * @param string $processor_class_name Fully qualified class name of the processor, must implement `BatchProcessorInterface`.
	 */
	public function enqueue_processor( string $processor_class_name ): void {
		$pending_updates = $this->get_enqueued_processors();
		if ( ! in_array( $processor_class_name, array_keys( $pending_updates ), true ) ) {
			$pending_updates[] = $processor_class_name;
			$this->set_enqueued_processors( $pending_updates );
		}
		$this->schedule_watchdog_action( false, true );
	}

	/**
	 * Schedule the watchdog action.
	 *
	 * @param bool $with_delay Whether to delay the action execution. Should be true when rescheduling, false when enqueueing.
	 * @param bool $unique     Whether to make the action unique.
	 */
	private function schedule_watchdog_action( bool $with_delay = false, bool $unique = false ): void {
		$time = $with_delay ? time() + HOUR_IN_SECONDS : time();
		as_schedule_single_action(
			$time,
			self::WATCHDOG_ACTION_NAME,
			array(),
			self::ACTION_GROUP,
			$unique
		);
	}

	/**
	 * Schedule a processing action for all the processors that are enqueued but not scheduled
	 * (because they have just been enqueued, or because the processing for a batch failed).
	 */
	private function handle_watchdog_action(): void {
		$pending_processes = $this->get_enqueued_processors();
		if ( empty( $pending_processes ) ) {
			return;
		}
		foreach ( $pending_processes as $process_name ) {
			if ( ! $this->is_scheduled( $process_name ) ) {
				$this->schedule_batch_processing( $process_name );
			}
		}
		$this->schedule_watchdog_action( true );
	}

	/**
	 * Process a batch for a single processor, and handle any required rescheduling or state cleanup.
	 *
	 * @param string $processor_class_name Fully qualified class name of the processor.
	 *
	 * @throws \Exception If error occurred during batch processing.
	 */
	private function process_next_batch_for_single_processor( string $processor_class_name ): void {
		if ( ! $this->is_enqueued( $processor_class_name ) ) {
			return;
		}

		$batch_processor      = $this->get_processor_instance( $processor_class_name );
		$pending_count_before = $batch_processor->get_total_pending_count();
		$error                = $this->process_next_batch_for_single_processor_core( $batch_processor );
		$pending_count_after  = $batch_processor->get_total_pending_count();
		if ( ( $error instanceof \Exception ) && $pending_count_before === $pending_count_after ) {
			// The batch processing failed and no items were processed:
			// reschedule the processing with a delay, and also throw the error
			// so Action Scheduler will ignore the rescheduling if this happens repeatedly.
			$this->schedule_batch_processing( $processor_class_name, true );
			throw $error;
		}
		if ( $pending_count_after > 0 ) {
			$this->schedule_batch_processing( $processor_class_name );
		} else {
			$this->dequeue_processor( $processor_class_name );
		}
	}

	/**
	 * Process a batch for a single processor, updating state and logging any error.
	 *
	 * @param BatchProcessorInterface $batch_processor Batch processor instance.
	 *
	 * @return null|\Exception Exception if error occurred, null otherwise.
	 */
	private function process_next_batch_for_single_processor_core( BatchProcessorInterface $batch_processor ): ?\Exception {
		$details    = $this->get_process_details( $batch_processor );
		$time_start = microtime( true );
		$batch      = $batch_processor->get_next_batch_to_process( $details['current_batch_size'] );
		if ( empty( $batch ) ) {
			return null;
		}
		try {
			$batch_processor->process_batch( $batch );
			$time_taken = microtime( true ) - $time_start;
			$this->update_processor_state( $batch_processor, $time_taken );
		} catch ( \Exception $exception ) {
			$time_taken = microtime( true ) - $time_start;
			$this->log_error( $exception, $batch_processor, $batch );
			$this->update_processor_state( $batch_processor, $time_taken, $exception );
			return $exception;
		}
		return null;
	}

	/**
	 * Get the current state for a given enqueued processor.
	 *
	 * @param BatchProcessorInterface $batch_processor Batch processor instance.
	 *
	 * @return array Current state for the processor, or a "blank" state if none exists yet.
	 */
	private function get_process_details( BatchProcessorInterface $batch_processor ): array {
		return get_option(
			$this->get_processor_state_option_name( $batch_processor ),
			array(
				'total_time_spent'   => 0,
				'current_batch_size' => $batch_processor->get_default_batch_size(),
				'last_error'         => null,
			)
		);
	}

	/**
	 * Get the name of the option where we will be saving state for a given processor.
	 *
	 * @param BatchProcessorInterface $batch_processor Batch processor instance.
	 *
	 * @return string Option name.
	 */
	private function get_processor_state_option_name( BatchProcessorInterface $batch_processor ): string {
		$class_name = get_class( $batch_processor );
		$class_md5  = md5( $class_name );
		// truncate the class name so we know that it will fit in the option name column along with md5 hash and prefix.
		$class_name = substr( $class_name, 0, 140 );
		return 'wc_batch_' . $class_name . '_' . $class_md5;
	}

	/**
	 * Update the state for a processor after a batch has completed processing.
	 *
	 * @param BatchProcessorInterface $batch_processor Batch processor instance.
	 * @param float                   $time_taken Time take by the batch to complete processing.
	 * @param \Exception|null         $last_error Exception object in processing the batch, if there was one.
	 */
	private function update_processor_state( BatchProcessorInterface $batch_processor, float $time_taken, \Exception $last_error = null ): void {
		$current_status                      = $this->get_process_details( $batch_processor );
		$current_status['total_time_spent'] += $time_taken;
		$current_status['last_error']        = null !== $last_error ? $last_error->getMessage() : null;
		update_option( $this->get_processor_state_option_name( $batch_processor ), $current_status, false );
	}

	/**
	 * Schedule a processing action for a single processor.
	 *
	 * @param string $processor_class_name Fully qualified class name of the processor.
	 * @param bool   $with_delay   Whether to schedule the action for immediate execution or for later.
	 */
	private function schedule_batch_processing( string $processor_class_name, bool $with_delay = false ) : void {
		$time = $with_delay ? time() + MINUTE_IN_SECONDS : time();
		as_schedule_single_action( $time, self::PROCESS_SINGLE_BATCH_ACTION_NAME, array( $processor_class_name ) );
	}

	/**
	 * Check if a batch processing action is already scheduled for a given processor.
	 * Differs from `as_has_scheduled_action` in that this excludes actions in progress.
	 *
	 * @param string $processor_class_name Fully qualified class name of the batch processor.
	 *
	 * @return bool True if a batch processing action is already scheduled for the processor.
	 */
	public function is_scheduled( string $processor_class_name ): bool {
		return as_has_scheduled_action( self::PROCESS_SINGLE_BATCH_ACTION_NAME, array( $processor_class_name ) );
	}

	/**
	 * Get an instance of a processor given its class name.
	 *
	 * @param string $processor_class_name Full class name of the batch processor.
	 *
	 * @return BatchProcessorInterface Instance of batch processor for the given class.
	 * @throws \Exception If it's not possible to get an instance of the class.
	 */
	private function get_processor_instance( string $processor_class_name ) : BatchProcessorInterface {
		$processor = wc_get_container()->get( $processor_class_name );

		/**
		 * Filters the instance of a processor for a given class name.
		 *
		 * @param object|null $processor The processor instance given by the dependency injection container, or null if none was obtained.
		 * @param string $processor_class_name The full class name of the processor.
		 * @return BatchProcessorInterface|null The actual processor instance to use, or null if none could be retrieved.
		 *
		 * @since 6.8.0.
		 */
		$processor = apply_filters( 'woocommerce_get_batch_processor', $processor, $processor_class_name );
		if ( ! isset( $processor ) && class_exists( $processor_class_name ) ) {
			// This is a fallback for when the batch processor is not registered in the container.
			$processor = new $processor_class_name();
		}
		if ( ! is_a( $processor, BatchProcessorInterface::class ) ) {
			throw new \Exception( "Unable to initialize batch processor instance for $processor_class_name" );
		}
		return $processor;
	}

	/**
	 * Helper method to get list of all the enqueued processors.
	 *
	 * @return array List (of string) of the class names of the enqueued processors.
	 */
	public function get_enqueued_processors() : array {
		return get_option( self::ENQUEUED_PROCESSORS_OPTION_NAME, array() );
	}

	/**
	 * Dequeue a processor once it has no more items pending processing.
	 *
	 * @param string $processor_class_name Full processor class name.
	 */
	private function dequeue_processor( string $processor_class_name ): void {
		$pending_processes = $this->get_enqueued_processors();
		if ( in_array( $processor_class_name, $pending_processes, true ) ) {
			$pending_processes = array_diff( $pending_processes, array( $processor_class_name ) );
			$this->set_enqueued_processors( $pending_processes );
		}
	}

	/**
	 * Helper method to set the enqueued processor class names.
	 *
	 * @param array $processors List of full processor class names.
	 */
	private function set_enqueued_processors( array $processors ): void {
		update_option( self::ENQUEUED_PROCESSORS_OPTION_NAME, $processors, false );
	}

	/**
	 * Check if a particular processor is enqueued.
	 *
	 * @param string $processor_class_name Fully qualified class name of the processor.
	 *
	 * @return bool True if the processor is enqueued.
	 */
	public function is_enqueued( string $processor_class_name ) : bool {
		return in_array( $processor_class_name, $this->get_enqueued_processors(), true );
	}

	/**
	 * Dequeue and de-schedule a processor instance so that it won't be processed anymore.
	 *
	 * @param string $processor_class_name Fully qualified class name of the processor.
	 * @return bool True if the processor has been dequeued, false if the processor wasn't enqueued (so nothing has been done).
	 */
	public function remove_processor( string $processor_class_name ): bool {
		$enqueued_processors = $this->get_enqueued_processors();
		if ( ! in_array( $processor_class_name, $enqueued_processors, true ) ) {
			return false;
		}

		$enqueued_processors = array_diff( $enqueued_processors, array( $processor_class_name ) );
		if ( empty( $enqueued_processors ) ) {
			$this->force_clear_all_processes();
		} else {
			update_option( self::ENQUEUED_PROCESSORS_OPTION_NAME, $enqueued_processors, false );
			as_unschedule_all_actions( self::PROCESS_SINGLE_BATCH_ACTION_NAME, array( $processor_class_name ) );
		}

		return true;
	}

	/**
	 * Dequeues and de-schedules all the processors.
	 */
	public function force_clear_all_processes(): void {
		as_unschedule_all_actions( self::PROCESS_SINGLE_BATCH_ACTION_NAME );
		as_unschedule_all_actions( self::WATCHDOG_ACTION_NAME );
		update_option( self::ENQUEUED_PROCESSORS_OPTION_NAME, array(), false );
	}

	/**
	 * Log an error that happened while processing a batch.
	 *
	 * @param \Exception              $error Exception object to log.
	 * @param BatchProcessorInterface $batch_processor Batch processor instance.
	 * @param array                   $batch Batch that was being processed.
	 */
	protected function log_error( \Exception $error, BatchProcessorInterface $batch_processor, array $batch ) : void {
		$batch_detail_string = '';
		// Log only first and last, as the entire batch may be too big.
		if ( count( $batch ) > 0 ) {
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r -- Logging is for debugging.
			$batch_detail_string = '\n' . print_r(
				array(
					'batch_start' => $batch[0],
					'batch_end'   => end( $batch ),
				),
				true
			);
		}
		$error_message = "Error processing batch for {$batch_processor->get_name()}: {$error->getMessage()}" . $batch_detail_string;

		/**
		 * Filters the error message for a batch processing.
		 *
		 * @param string $error_message The error message that will be logged.
		 * @param \Exception $error The exception that was thrown by the processor.
		 * @param BatchProcessorInterface $batch_processor The processor that threw the exception.
		 * @param array $batch The batch that was being processed.
		 * @return string The actual error message that will be logged.
		 *
		 * @since 6.8.0
		 */
		$error_message = apply_filters( 'wc_batch_processing_log_message', $error_message, $error, $batch_processor, $batch );

		$this->logger->error( $error_message, array( 'exception' => $error ) );
	}
}
