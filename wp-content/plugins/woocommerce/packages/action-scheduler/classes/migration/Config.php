<?php


namespace Action_Scheduler\Migration;

use Action_Scheduler\WP_CLI\ProgressBar;
use ActionScheduler_Logger as Logger;
use ActionScheduler_Store as Store;

/**
 * Class Config
 *
 * @package Action_Scheduler\Migration
 *
 * @since 3.0.0
 *
 * A config builder for the ActionScheduler\Migration\Runner class
 */
class Config {
	/** @var ActionScheduler_Store */
	private $source_store;

	/** @var ActionScheduler_Logger */
	private $source_logger;

	/** @var ActionScheduler_Store */
	private $destination_store;

	/** @var ActionScheduler_Logger */
	private $destination_logger;

	/** @var Progress bar */
	private $progress_bar;

	/** @var bool */
	private $dry_run = false;

	/**
	 * Config constructor.
	 */
	public function __construct() {

	}

	/**
	 * Get the configured source store.
	 *
	 * @return ActionScheduler_Store
	 */
	public function get_source_store() {
		if ( empty( $this->source_store ) ) {
			throw new \RuntimeException( __( 'Source store must be configured before running a migration', 'woocommerce' ) );
		}

		return $this->source_store;
	}

	/**
	 * Set the configured source store.
	 *
	 * @param ActionScheduler_Store $store Source store object.
	 */
	public function set_source_store( Store $store ) {
		$this->source_store = $store;
	}

	/**
	 * Get the configured source loger.
	 *
	 * @return ActionScheduler_Logger
	 */
	public function get_source_logger() {
		if ( empty( $this->source_logger ) ) {
			throw new \RuntimeException( __( 'Source logger must be configured before running a migration', 'woocommerce' ) );
		}

		return $this->source_logger;
	}

	/**
	 * Set the configured source logger.
	 *
	 * @param ActionScheduler_Logger $logger
	 */
	public function set_source_logger( Logger $logger ) {
		$this->source_logger = $logger;
	}

	/**
	 * Get the configured destination store.
	 *
	 * @return ActionScheduler_Store
	 */
	public function get_destination_store() {
		if ( empty( $this->destination_store ) ) {
			throw new \RuntimeException( __( 'Destination store must be configured before running a migration', 'woocommerce' ) );
		}

		return $this->destination_store;
	}

	/**
	 * Set the configured destination store.
	 *
	 * @param ActionScheduler_Store $store
	 */
	public function set_destination_store( Store $store ) {
		$this->destination_store = $store;
	}

	/**
	 * Get the configured destination logger.
	 *
	 * @return ActionScheduler_Logger
	 */
	public function get_destination_logger() {
		if ( empty( $this->destination_logger ) ) {
			throw new \RuntimeException( __( 'Destination logger must be configured before running a migration', 'woocommerce' ) );
		}

		return $this->destination_logger;
	}

	/**
	 * Set the configured destination logger.
	 *
	 * @param ActionScheduler_Logger $logger
	 */
	public function set_destination_logger( Logger $logger ) {
		$this->destination_logger = $logger;
	}

	/**
	 * Get flag indicating whether it's a dry run.
	 *
	 * @return bool
	 */
	public function get_dry_run() {
		return $this->dry_run;
	}

	/**
	 * Set flag indicating whether it's a dry run.
	 *
	 * @param bool $dry_run
	 */
	public function set_dry_run( $dry_run ) {
		$this->dry_run = (bool) $dry_run;
	}

	/**
	 * Get progress bar object.
	 *
	 * @return ActionScheduler\WPCLI\ProgressBar
	 */
	public function get_progress_bar() {
		return $this->progress_bar;
	}

	/**
	 * Set progress bar object.
	 *
	 * @param ActionScheduler\WPCLI\ProgressBar $progress_bar
	 */
	public function set_progress_bar( ProgressBar $progress_bar ) {
		$this->progress_bar = $progress_bar;
	}
}
