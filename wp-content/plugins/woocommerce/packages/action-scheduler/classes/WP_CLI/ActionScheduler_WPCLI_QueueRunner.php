<?php

use Action_Scheduler\WP_CLI\ProgressBar;

/**
 * WP CLI Queue runner.
 *
 * This class can only be called from within a WP CLI instance.
 */
class ActionScheduler_WPCLI_QueueRunner extends ActionScheduler_Abstract_QueueRunner {

	/** @var array */
	protected $actions;

	/** @var  ActionScheduler_ActionClaim */
	protected $claim;

	/** @var \cli\progress\Bar */
	protected $progress_bar;

	/**
	 * ActionScheduler_WPCLI_QueueRunner constructor.
	 *
	 * @param ActionScheduler_Store             $store
	 * @param ActionScheduler_FatalErrorMonitor $monitor
	 * @param ActionScheduler_QueueCleaner      $cleaner
	 *
	 * @throws Exception When this is not run within WP CLI
	 */
	public function __construct( ActionScheduler_Store $store = null, ActionScheduler_FatalErrorMonitor $monitor = null, ActionScheduler_QueueCleaner $cleaner = null ) {
		if ( ! ( defined( 'WP_CLI' ) && WP_CLI ) ) {
			/* translators: %s php class name */
			throw new Exception( sprintf( __( 'The %s class can only be run within WP CLI.', 'woocommerce' ), __CLASS__ ) );
		}

		parent::__construct( $store, $monitor, $cleaner );
	}

	/**
	 * Set up the Queue before processing.
	 *
	 * @author Jeremy Pry
	 *
	 * @param int    $batch_size The batch size to process.
	 * @param array  $hooks      The hooks being used to filter the actions claimed in this batch.
	 * @param string $group      The group of actions to claim with this batch.
	 * @param bool   $force      Whether to force running even with too many concurrent processes.
	 *
	 * @return int The number of actions that will be run.
	 * @throws \WP_CLI\ExitException When there are too many concurrent batches.
	 */
	public function setup( $batch_size, $hooks = array(), $group = '', $force = false ) {
		$this->run_cleanup();
		$this->add_hooks();

		// Check to make sure there aren't too many concurrent processes running.
		if ( $this->has_maximum_concurrent_batches() ) {
			if ( $force ) {
				WP_CLI::warning( __( 'There are too many concurrent batches, but the run is forced to continue.', 'woocommerce' ) );
			} else {
				WP_CLI::error( __( 'There are too many concurrent batches.', 'woocommerce' ) );
			}
		}

		// Stake a claim and store it.
		$this->claim = $this->store->stake_claim( $batch_size, null, $hooks, $group );
		$this->monitor->attach( $this->claim );
		$this->actions = $this->claim->get_actions();

		return count( $this->actions );
	}

	/**
	 * Add our hooks to the appropriate actions.
	 *
	 * @author Jeremy Pry
	 */
	protected function add_hooks() {
		add_action( 'action_scheduler_before_execute', array( $this, 'before_execute' ) );
		add_action( 'action_scheduler_after_execute', array( $this, 'after_execute' ), 10, 2 );
		add_action( 'action_scheduler_failed_execution', array( $this, 'action_failed' ), 10, 2 );
	}

	/**
	 * Set up the WP CLI progress bar.
	 *
	 * @author Jeremy Pry
	 */
	protected function setup_progress_bar() {
		$count              = count( $this->actions );
		$this->progress_bar = new ProgressBar(
			/* translators: %d: amount of actions */
			sprintf( _n( 'Running %d action', 'Running %d actions', $count, 'woocommerce' ), number_format_i18n( $count ) ),
			$count
		);
	}

	/**
	 * Process actions in the queue.
	 *
	 * @author Jeremy Pry
	 *
	 * @param string $context Optional runner context. Default 'WP CLI'.
	 *
	 * @return int The number of actions processed.
	 */
	public function run( $context = 'WP CLI' ) {
		do_action( 'action_scheduler_before_process_queue' );
		$this->setup_progress_bar();
		foreach ( $this->actions as $action_id ) {
			// Error if we lost the claim.
			if ( ! in_array( $action_id, $this->store->find_actions_by_claim_id( $this->claim->get_id() ) ) ) {
				WP_CLI::warning( __( 'The claim has been lost. Aborting current batch.', 'woocommerce' ) );
				break;
			}

			$this->process_action( $action_id, $context );
			$this->progress_bar->tick();
		}

		$completed = $this->progress_bar->current();
		$this->progress_bar->finish();
		$this->store->release_claim( $this->claim );
		do_action( 'action_scheduler_after_process_queue' );

		return $completed;
	}

	/**
	 * Handle WP CLI message when the action is starting.
	 *
	 * @author Jeremy Pry
	 *
	 * @param $action_id
	 */
	public function before_execute( $action_id ) {
		/* translators: %s refers to the action ID */
		WP_CLI::log( sprintf( __( 'Started processing action %s', 'woocommerce' ), $action_id ) );
	}

	/**
	 * Handle WP CLI message when the action has completed.
	 *
	 * @author Jeremy Pry
	 *
	 * @param int $action_id
	 * @param null|ActionScheduler_Action $action The instance of the action. Default to null for backward compatibility.
	 */
	public function after_execute( $action_id, $action = null ) {
		// backward compatibility
		if ( null === $action ) {
			$action = $this->store->fetch_action( $action_id );
		}
		/* translators: 1: action ID 2: hook name */
		WP_CLI::log( sprintf( __( 'Completed processing action %1$s with hook: %2$s', 'woocommerce' ), $action_id, $action->get_hook() ) );
	}

	/**
	 * Handle WP CLI message when the action has failed.
	 *
	 * @author Jeremy Pry
	 *
	 * @param int       $action_id
	 * @param Exception $exception
	 * @throws \WP_CLI\ExitException With failure message.
	 */
	public function action_failed( $action_id, $exception ) {
		WP_CLI::error(
			/* translators: 1: action ID 2: exception message */
			sprintf( __( 'Error processing action %1$s: %2$s', 'woocommerce' ), $action_id, $exception->getMessage() ),
			false
		);
	}

	/**
	 * Sleep and help avoid hitting memory limit
	 *
	 * @param int $sleep_time Amount of seconds to sleep
	 * @deprecated 3.0.0
	 */
	protected function stop_the_insanity( $sleep_time = 0 ) {
		_deprecated_function( 'ActionScheduler_WPCLI_QueueRunner::stop_the_insanity', '3.0.0', 'ActionScheduler_DataController::free_memory' );

		ActionScheduler_DataController::free_memory();
	}

	/**
	 * Maybe trigger the stop_the_insanity() method to free up memory.
	 */
	protected function maybe_stop_the_insanity() {
		// The value returned by progress_bar->current() might be padded. Remove padding, and convert to int.
		$current_iteration = intval( trim( $this->progress_bar->current() ) );
		if ( 0 === $current_iteration % 50 ) {
			$this->stop_the_insanity();
		}
	}
}
