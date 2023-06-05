<?php

/**
 * Class ActionScheduler_FatalErrorMonitor
 */
class ActionScheduler_FatalErrorMonitor {
	/** @var ActionScheduler_ActionClaim */
	private $claim = NULL;
	/** @var ActionScheduler_Store */
	private $store = NULL;
	private $action_id = 0;

	public function __construct( ActionScheduler_Store $store ) {
		$this->store = $store;
	}

	public function attach( ActionScheduler_ActionClaim $claim ) {
		$this->claim = $claim;
		add_action( 'shutdown', array( $this, 'handle_unexpected_shutdown' ) );
		add_action( 'action_scheduler_before_execute', array( $this, 'track_current_action' ), 0, 1 );
		add_action( 'action_scheduler_after_execute',  array( $this, 'untrack_action' ), 0, 0 );
		add_action( 'action_scheduler_execution_ignored',  array( $this, 'untrack_action' ), 0, 0 );
		add_action( 'action_scheduler_failed_execution',  array( $this, 'untrack_action' ), 0, 0 );
	}

	public function detach() {
		$this->claim = NULL;
		$this->untrack_action();
		remove_action( 'shutdown', array( $this, 'handle_unexpected_shutdown' ) );
		remove_action( 'action_scheduler_before_execute', array( $this, 'track_current_action' ), 0 );
		remove_action( 'action_scheduler_after_execute',  array( $this, 'untrack_action' ), 0 );
		remove_action( 'action_scheduler_execution_ignored',  array( $this, 'untrack_action' ), 0 );
		remove_action( 'action_scheduler_failed_execution',  array( $this, 'untrack_action' ), 0 );
	}

	public function track_current_action( $action_id ) {
		$this->action_id = $action_id;
	}

	public function untrack_action() {
		$this->action_id = 0;
	}

	public function handle_unexpected_shutdown() {
		if ( $error = error_get_last() ) {
			if ( in_array( $error['type'], array( E_ERROR, E_PARSE, E_COMPILE_ERROR, E_USER_ERROR, E_RECOVERABLE_ERROR ) ) ) {
				if ( !empty($this->action_id) ) {
					$this->store->mark_failure( $this->action_id );
					do_action( 'action_scheduler_unexpected_shutdown', $this->action_id, $error );
				}
			}
			$this->store->release_claim( $this->claim );
		}
	}
}
