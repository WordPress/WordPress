<?php

/**
 * Provide a way to set simple transient locks to block behaviour
 * for up-to a given duration.
 *
 * Class ActionScheduler_OptionLock
 * @since 3.0.0
 */
class ActionScheduler_OptionLock extends ActionScheduler_Lock {

	/**
	 * Set a lock using options for a given amount of time (60 seconds by default).
	 *
	 * Using an autoloaded option avoids running database queries or other resource intensive tasks
	 * on frequently triggered hooks, like 'init' or 'shutdown'.
	 *
	 * For example, ActionScheduler_QueueRunner->maybe_dispatch_async_request() uses a lock to avoid
	 * calling ActionScheduler_QueueRunner->has_maximum_concurrent_batches() every time the 'shutdown',
	 * hook is triggered, because that method calls ActionScheduler_QueueRunner->store->get_claim_count()
	 * to find the current number of claims in the database.
	 *
	 * @param string $lock_type A string to identify different lock types.
	 * @bool True if lock value has changed, false if not or if set failed.
	 */
	public function set( $lock_type ) {
		return update_option( $this->get_key( $lock_type ), time() + $this->get_duration( $lock_type ) );
	}

	/**
	 * If a lock is set, return the timestamp it was set to expiry.
	 *
	 * @param string $lock_type A string to identify different lock types.
	 * @return bool|int False if no lock is set, otherwise the timestamp for when the lock is set to expire.
	 */
	public function get_expiration( $lock_type ) {
		return get_option( $this->get_key( $lock_type ) );
	}

	/**
	 * Get the key to use for storing the lock in the transient
	 *
	 * @param string $lock_type A string to identify different lock types.
	 * @return string
	 */
	protected function get_key( $lock_type ) {
		return sprintf( 'action_scheduler_lock_%s', $lock_type );
	}
}
