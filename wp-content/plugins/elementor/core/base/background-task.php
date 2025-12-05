<?php
/**
 * Based on https://github.com/woocommerce/woocommerce/blob/master/includes/abstracts/class-wc-background-process.php
 * & https://github.com/woocommerce/woocommerce/blob/master/includes/class-wc-background-updater.php
 *
 * @package Elementor\Core\Base
 */

namespace Elementor\Core\Base;

use Elementor\Plugin;
use Elementor\Core\Base\BackgroundProcess\WP_Background_Process;


defined( 'ABSPATH' ) || exit;

/**
 * WC_Background_Process class.
 */
abstract class Background_Task extends WP_Background_Process {
	protected $current_item;

	/**
	 * Dispatch updater.
	 *
	 * Updater will still run via cron job if this fails for any reason.
	 */
	public function dispatch() {
		$dispatched = parent::dispatch();

		if ( is_wp_error( $dispatched ) ) {
			wp_die( esc_html( $dispatched ) );
		}
	}

	public function query_col( $sql ) {
		global $wpdb;

		// Add Calc.
		$item = $this->get_current_item();
		if ( empty( $item['total'] ) ) {
			$sql = preg_replace( '/^SELECT/', 'SELECT SQL_CALC_FOUND_ROWS', $sql );
		}

		// Add offset & limit.
		$sql = preg_replace( '/;$/', '', $sql );
		$sql .= ' LIMIT %d, %d;';

		$results = $wpdb->get_col( $wpdb->prepare( $sql, $this->get_current_offset(), $this->get_limit() ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

		if ( ! empty( $results ) ) {
			$this->set_total();
		}

		return $results;
	}

	public function should_run_again( $updated_rows ) {
		return count( $updated_rows ) === $this->get_limit();
	}

	public function get_current_offset() {
		$limit = $this->get_limit();
		return ( $this->current_item['iterate_num'] - 1 ) * $limit;
	}

	public function get_limit() {
		return $this->manager->get_query_limit();
	}

	public function set_total() {
		global $wpdb;

		if ( empty( $this->current_item['total'] ) ) {
			$total_rows = $wpdb->get_var( 'SELECT FOUND_ROWS();' );
			$total_iterates = ceil( $total_rows / $this->get_limit() );
			$this->current_item['total'] = $total_iterates;
		}
	}

	/**
	 * Complete
	 *
	 * Override if applicable, but ensure that the below actions are
	 * performed, or, call parent::complete().
	 */
	protected function complete() {
		$this->manager->on_runner_complete( true );

		parent::complete();
	}

	public function continue_run() {
		// Used to fire an action added in WP_Background_Process::_construct() that calls WP_Background_Process::handle_cron_healthcheck().
		// This method will make sure the database updates are executed even if cron is disabled. Nothing will happen if the updates are already running.
		do_action( $this->cron_hook_identifier );
	}

	/**
	 * @return mixed
	 */
	public function get_current_item() {
		return $this->current_item;
	}

	/**
	 * Get batch.
	 *
	 * @return \stdClass Return the first batch from the queue.
	 */
	protected function get_batch() {
		$batch = parent::get_batch();
		$batch->data = array_filter( (array) $batch->data );

		return $batch;
	}

	/**
	 * Handle cron healthcheck
	 *
	 * Restart the background process if not already running
	 * and data exists in the queue.
	 */
	public function handle_cron_healthcheck() {
		if ( $this->is_process_running() ) {
			// Background process already running.
			return;
		}

		if ( $this->is_queue_empty() ) {
			// No data to process.
			$this->clear_scheduled_event();
			return;
		}

		$this->handle();
	}

	/**
	 * Schedule fallback event.
	 */
	protected function schedule_event() {
		if ( ! wp_next_scheduled( $this->cron_hook_identifier ) ) {
			wp_schedule_event( time() + 10, $this->cron_interval_identifier, $this->cron_hook_identifier );
		}
	}

	/**
	 * Is the updater running?
	 *
	 * @return boolean
	 */
	public function is_running() {
		return false === $this->is_queue_empty();
	}

	/**
	 * See if the batch limit has been exceeded.
	 *
	 * @return bool
	 */
	protected function batch_limit_exceeded() {
		return $this->time_exceeded() || $this->memory_exceeded();
	}

	/**
	 * Handle.
	 *
	 * Pass each queue item to the task handler, while remaining
	 * within server memory and time limit constraints.
	 */
	protected function handle() {
		$this->manager->on_runner_start();

		$this->lock_process();

		do {
			$batch = $this->get_batch();

			foreach ( $batch->data as $key => $value ) {
				$task = $this->task( $value );

				if ( false !== $task ) {
					$batch->data[ $key ] = $task;
				} else {
					unset( $batch->data[ $key ] );
				}

				if ( $this->batch_limit_exceeded() ) {
					// Batch limits reached.
					break;
				}
			}

			// Update or delete current batch.
			if ( ! empty( $batch->data ) ) {
				$this->update( $batch->key, $batch->data );
			} else {
				$this->delete( $batch->key );
			}
		} while ( ! $this->batch_limit_exceeded() && ! $this->is_queue_empty() );

		$this->unlock_process();

		// Start next batch or complete process.
		if ( ! $this->is_queue_empty() ) {
			$this->dispatch();
		} else {
			$this->complete();
		}
	}

	/**
	 * Use the protected `is_process_running` method as a public method.
	 *
	 * @return bool
	 */
	public function is_process_locked() {
		return $this->is_process_running();
	}

	public function handle_immediately( $callbacks ) {
		$this->manager->on_runner_start();

		$this->lock_process();

		foreach ( $callbacks as $callback ) {
			$item = [
				'callback' => $callback,
			];

			do {
				$item = $this->task( $item );
			} while ( $item );
		}

		$this->unlock_process();
	}

	/**
	 * Task
	 *
	 * Override this method to perform any actions required on each
	 * queue item. Return the modified item for further processing
	 * in the next pass through. Or, return false to remove the
	 * item from the queue.
	 *
	 * @param array $item
	 *
	 * @return array|bool
	 */
	protected function task( $item ) {
		$result = false;

		if ( ! isset( $item['iterate_num'] ) ) {
			$item['iterate_num'] = 1;
		}

		$logger = Plugin::$instance->logger->get_logger();
		$callback = $this->format_callback_log( $item );

		if ( is_callable( $item['callback'] ) ) {
			$progress = '';

			if ( 1 < $item['iterate_num'] ) {
				if ( empty( $item['total'] ) ) {
					$progress = sprintf( '(x%s)', $item['iterate_num'] );
				} else {
					$percent = ceil( $item['iterate_num'] / ( $item['total'] / 100 ) );
					$progress = sprintf( '(%s of %s, %s%%)', $item['iterate_num'], $item['total'], $percent );
				}
			}

			$logger->info( sprintf( '%s Start %s', $callback, $progress ) );

			$this->current_item = $item;

			$result = (bool) call_user_func( $item['callback'], $this );

			// get back the updated item.
			$item = $this->current_item;
			$this->current_item = null;

			if ( $result ) {
				if ( empty( $item['total'] ) ) {
					$logger->info( sprintf( '%s callback needs to run again', $callback ) );
				} elseif ( 1 === $item['iterate_num'] ) {
					$logger->info( sprintf( '%s callback needs to run more %d times', $callback, $item['total'] - $item['iterate_num'] ) );
				}

				++$item['iterate_num'];
			} else {
				$logger->info( sprintf( '%s Finished', $callback ) );
			}
		} else {
			$logger->notice( sprintf( 'Could not find %s callback', $callback ) );
		}

		return $result ? $item : false;
	}

	/**
	 * Schedule cron healthcheck.
	 *
	 * @param array $schedules Schedules.
	 * @return array
	 */
	public function schedule_cron_healthcheck( $schedules ) {
		$interval = apply_filters( $this->identifier . '_cron_interval', 5 );

		// Adds every 5 minutes to the existing schedules.
		$schedules[ $this->identifier . '_cron_interval' ] = [
			'interval' => MINUTE_IN_SECONDS * $interval,
			'display' => sprintf(
				/* translators: %d: Interval in minutes. */
				esc_html__( 'Every %d minutes', 'elementor' ),
				$interval
			),
		];

		return $schedules;
	}

	/**
	 * See if the batch limit has been exceeded.
	 *
	 * @return bool
	 */
	public function is_memory_exceeded() {
		return $this->memory_exceeded();
	}

	/**
	 * Delete all batches.
	 *
	 * @return self
	 */
	public function delete_all_batches() {
		global $wpdb;

		$table = $wpdb->options;
		$column = 'option_name';

		if ( is_multisite() ) {
			$table = $wpdb->sitemeta;
			$column = 'meta_key';
		}

		$key = $wpdb->esc_like( $this->identifier . '_batch_' ) . '%';

		$wpdb->query( $wpdb->prepare( "DELETE FROM {$table} WHERE {$column} LIKE %s", $key ) ); // @codingStandardsIgnoreLine.

		return $this;
	}

	/**
	 * Kill process.
	 *
	 * Stop processing queue items, clear cronjob and delete all batches.
	 */
	public function kill_process() {
		if ( ! $this->is_queue_empty() ) {
			$this->delete_all_batches();
			wp_clear_scheduled_hook( $this->cron_hook_identifier );
		}
	}

	public function set_current_item( $item ) {
		$this->current_item = $item;
	}

	protected function format_callback_log( $item ) {
		return implode( '::', (array) $item['callback'] );
	}

	/**
	 * @var \Elementor\Core\Base\Background_Task_Manager
	 */
	protected $manager;

	public function __construct( $manager ) {
		$this->manager = $manager;
		// Uses unique prefix per blog so each blog has separate queue.
		$this->prefix = 'elementor_' . get_current_blog_id();
		$this->action = $this->manager->get_action();

		parent::__construct();
	}
}
