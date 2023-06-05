<?php

use Action_Scheduler\Migration\Controller;

/**
 * Class ActionScheduler_DataController
 *
 * The main plugin/initialization class for the data stores.
 *
 * Responsible for hooking everything up with WordPress.
 *
 * @package Action_Scheduler
 *
 * @since 3.0.0
 */
class ActionScheduler_DataController {
	/** Action data store class name. */
	const DATASTORE_CLASS = 'ActionScheduler_DBStore';

	/** Logger data store class name. */
	const LOGGER_CLASS    = 'ActionScheduler_DBLogger';

	/** Migration status option name. */
	const STATUS_FLAG     = 'action_scheduler_migration_status';

	/** Migration status option value. */
	const STATUS_COMPLETE = 'complete';

	/** Migration minimum required PHP version. */
	const MIN_PHP_VERSION = '5.5';

	/** @var ActionScheduler_DataController */
	private static $instance;

	/** @var int */
	private static $sleep_time = 0;

	/** @var int */
	private static $free_ticks = 50;

	/**
	 * Get a flag indicating whether the migration environment dependencies are met.
	 *
	 * @return bool
	 */
	public static function dependencies_met() {
		$php_support = version_compare( PHP_VERSION, self::MIN_PHP_VERSION, '>=' );
		return $php_support && apply_filters( 'action_scheduler_migration_dependencies_met', true );
	}

	/**
	 * Get a flag indicating whether the migration is complete.
	 *
	 * @return bool Whether the flag has been set marking the migration as complete
	 */
	public static function is_migration_complete() {
		return get_option( self::STATUS_FLAG ) === self::STATUS_COMPLETE;
	}

	/**
	 * Mark the migration as complete.
	 */
	public static function mark_migration_complete() {
		update_option( self::STATUS_FLAG, self::STATUS_COMPLETE );
	}

	/**
	 * Unmark migration when a plugin is de-activated. Will not work in case of silent activation, for example in an update.
	 * We do this to mitigate the bug of lost actions which happens if there was an AS 2.x to AS 3.x migration in the past, but that plugin is now
	 * deactivated and the site was running on AS 2.x again.
	 */
	public static function mark_migration_incomplete() {
		delete_option( self::STATUS_FLAG );
	}

	/**
	 * Set the action store class name.
	 *
	 * @param string $class Classname of the store class.
	 *
	 * @return string
	 */
	public static function set_store_class( $class ) {
		return self::DATASTORE_CLASS;
	}

	/**
	 * Set the action logger class name.
	 *
	 * @param string $class Classname of the logger class.
	 *
	 * @return string
	 */
	public static function set_logger_class( $class ) {
		return self::LOGGER_CLASS;
	}

	/**
	 * Set the sleep time in seconds.
	 *
	 * @param integer $sleep_time The number of seconds to pause before resuming operation.
	 */
	public static function set_sleep_time( $sleep_time ) {
		self::$sleep_time = (int) $sleep_time;
	}

	/**
	 * Set the tick count required for freeing memory.
	 *
	 * @param integer $free_ticks The number of ticks to free memory on.
	 */
	public static function set_free_ticks( $free_ticks ) {
		self::$free_ticks = (int) $free_ticks;
	}

	/**
	 * Free memory if conditions are met.
	 *
	 * @param int $ticks Current tick count.
	 */
	public static function maybe_free_memory( $ticks ) {
		if ( self::$free_ticks && 0 === $ticks % self::$free_ticks ) {
			self::free_memory();
		}
	}

	/**
	 * Reduce memory footprint by clearing the database query and object caches.
	 */
	public static function free_memory() {
		if ( 0 < self::$sleep_time ) {
			/* translators: %d: amount of time */
			\WP_CLI::warning( sprintf( _n( 'Stopped the insanity for %d second', 'Stopped the insanity for %d seconds', self::$sleep_time, 'woocommerce' ), self::$sleep_time ) );
			sleep( self::$sleep_time );
		}

		\WP_CLI::warning( __( 'Attempting to reduce used memory...', 'woocommerce' ) );

		/**
		 * @var $wpdb            \wpdb
		 * @var $wp_object_cache \WP_Object_Cache
		 */
		global $wpdb, $wp_object_cache;

		$wpdb->queries = array();

		if ( ! is_a( $wp_object_cache, 'WP_Object_Cache' ) ) {
			return;
		}

		$wp_object_cache->group_ops      = array();
		$wp_object_cache->stats          = array();
		$wp_object_cache->memcache_debug = array();
		$wp_object_cache->cache          = array();

		if ( is_callable( array( $wp_object_cache, '__remoteset' ) ) ) {
			call_user_func( array( $wp_object_cache, '__remoteset' ) ); // important
		}
	}

	/**
	 * Connect to table datastores if migration is complete.
	 * Otherwise, proceed with the migration if the dependencies have been met.
	 */
	public static function init() {
		if ( self::is_migration_complete() ) {
			add_filter( 'action_scheduler_store_class', array( 'ActionScheduler_DataController', 'set_store_class' ), 100 );
			add_filter( 'action_scheduler_logger_class', array( 'ActionScheduler_DataController', 'set_logger_class' ), 100 );
			add_action( 'deactivate_plugin', array( 'ActionScheduler_DataController', 'mark_migration_incomplete' ) );
		} elseif ( self::dependencies_met() ) {
			Controller::init();
		}

		add_action( 'action_scheduler/progress_tick', array( 'ActionScheduler_DataController', 'maybe_free_memory' ) );
	}

	/**
	 * Singleton factory.
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new static();
		}

		return self::$instance;
	}
}
