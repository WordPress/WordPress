<?php
/**
 * ActionScheduler_AsyncRequest_QueueRunner
 */

defined( 'ABSPATH' ) || exit;

/**
 * ActionScheduler_AsyncRequest_QueueRunner class.
 */
class ActionScheduler_AsyncRequest_QueueRunner extends WP_Async_Request {

	/**
	 * Data store for querying actions
	 *
	 * @var ActionScheduler_Store
	 * @access protected
	 */
	protected $store;

	/**
	 * Prefix for ajax hooks
	 *
	 * @var string
	 * @access protected
	 */
	protected $prefix = 'as';

	/**
	 * Action for ajax hooks
	 *
	 * @var string
	 * @access protected
	 */
	protected $action = 'async_request_queue_runner';

	/**
	 * Initiate new async request
	 */
	public function __construct( ActionScheduler_Store $store ) {
		parent::__construct();
		$this->store = $store;
	}

	/**
	 * Handle async requests
	 *
	 * Run a queue, and maybe dispatch another async request to run another queue
	 * if there are still pending actions after completing a queue in this request.
	 */
	protected function handle() {
		do_action( 'action_scheduler_run_queue', 'Async Request' ); // run a queue in the same way as WP Cron, but declare the Async Request context

		$sleep_seconds = $this->get_sleep_seconds();

		if ( $sleep_seconds ) {
			sleep( $sleep_seconds );
		}

		$this->maybe_dispatch();
	}

	/**
	 * If the async request runner is needed and allowed to run, dispatch a request.
	 */
	public function maybe_dispatch() {
		if ( ! $this->allow() ) {
			return;
		}

		$this->dispatch();
		ActionScheduler_QueueRunner::instance()->unhook_dispatch_async_request();
	}

	/**
	 * Only allow async requests when needed.
	 *
	 * Also allow 3rd party code to disable running actions via async requests.
	 */
	protected function allow() {

		if ( ! has_action( 'action_scheduler_run_queue' ) || ActionScheduler::runner()->has_maximum_concurrent_batches() || ! $this->store->has_pending_actions_due() ) {
			$allow = false;
		} else {
			$allow = true;
		}

		return apply_filters( 'action_scheduler_allow_async_request_runner', $allow );
	}

	/**
	 * Chaining async requests can crash MySQL. A brief sleep call in PHP prevents that.
	 */
	protected function get_sleep_seconds() {
		return apply_filters( 'action_scheduler_async_request_sleep_seconds', 5, $this );
	}
}
