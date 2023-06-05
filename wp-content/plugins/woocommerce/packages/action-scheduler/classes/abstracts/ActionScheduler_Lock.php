<?php

/**
 * Abstract class for setting a basic lock to throttle some action.
 *
 * Class ActionScheduler_Lock
 */
abstract class ActionScheduler_Lock {

	/** @var ActionScheduler_Lock */
	private static $locker = NULL;

	/** @var int */
	protected static $lock_duration = MINUTE_IN_SECONDS;

	/**
	 * Check if a lock is set for a given lock type.
	 *
	 * @param string $lock_type A string to identify different lock types.
	 * @return bool
	 */
	public function is_locked( $lock_type ) {
		return ( $this->get_expiration( $lock_type ) >= time() );
	}

	/**
	 * Set a lock.
	 *
	 * @param string $lock_type A string to identify different lock types.
	 * @return bool
	 */
	abstract public function set( $lock_type );

	/**
	 * If a lock is set, return the timestamp it was set to expiry.
	 *
	 * @param string $lock_type A string to identify different lock types.
	 * @return bool|int False if no lock is set, otherwise the timestamp for when the lock is set to expire.
	 */
	abstract public function get_expiration( $lock_type );

	/**
	 * Get the amount of time to set for a given lock. 60 seconds by default.
	 *
	 * @param string $lock_type A string to identify different lock types.
	 * @return int
	 */
	protected function get_duration( $lock_type ) {
		return apply_filters( 'action_scheduler_lock_duration', self::$lock_duration, $lock_type );
	}

	/**
	 * @return ActionScheduler_Lock
	 */
	public static function instance() {
		if ( empty( self::$locker ) ) {
			$class = apply_filters( 'action_scheduler_lock_class', 'ActionScheduler_OptionLock' );
			self::$locker = new $class();
		}
		return self::$locker;
	}
}
