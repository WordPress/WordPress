<?php
/**
 * Report table sync related functions and actions.
 */

namespace Automattic\WooCommerce\Admin;

defined( 'ABSPATH' ) || exit;

use Automattic\WooCommerce\Internal\Admin\Schedulers\CustomersScheduler;
use Automattic\WooCommerce\Internal\Admin\Schedulers\OrdersScheduler;
use Automattic\WooCommerce\Internal\Admin\Schedulers\ImportScheduler;

/**
 * ReportsSync Class.
 */
class ReportsSync {
	/**
	 * Hook in sync methods.
	 */
	public static function init() {
		// Initialize scheduler hooks.
		foreach ( self::get_schedulers() as $scheduler ) {
			$scheduler::init();
		}
		add_action( 'woocommerce_update_product', array( __CLASS__, 'clear_stock_count_cache' ) );
		add_action( 'woocommerce_new_product', array( __CLASS__, 'clear_stock_count_cache' ) );
		add_action( 'update_option_woocommerce_notify_low_stock_amount', array( __CLASS__, 'clear_stock_count_cache' ) );
		add_action( 'update_option_woocommerce_notify_no_stock_amount', array( __CLASS__, 'clear_stock_count_cache' ) );
	}

	/**
	 * Get classes for syncing data.
	 *
	 * @return array
	 * @throws \Exception Throws exception when invalid data is found.
	 */
	public static function get_schedulers() {
		$schedulers = apply_filters(
			'woocommerce_analytics_report_schedulers',
			array(
				new CustomersScheduler(),
				new OrdersScheduler(),
			)
		);

		foreach ( $schedulers as $scheduler ) {
			if ( ! is_subclass_of( $scheduler, 'Automattic\WooCommerce\Internal\Admin\Schedulers\ImportScheduler' ) ) {
				throw new \Exception( __( 'Report sync schedulers should be derived from the Automattic\WooCommerce\Internal\Admin\Schedulers\ImportScheduler class.', 'woocommerce' ) );
			}
		}

		return $schedulers;
	}

	/**
	 * Returns true if an import is in progress.
	 *
	 * @return bool
	 */
	public static function is_importing() {
		foreach ( self::get_schedulers() as $scheduler ) {
			if ( $scheduler::is_importing() ) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Regenerate data for reports.
	 *
	 * @param int|bool $days Number of days to import.
	 * @param bool     $skip_existing Skip existing records.
	 * @return string
	 */
	public static function regenerate_report_data( $days, $skip_existing ) {
		if ( self::is_importing() ) {
			return new \WP_Error( 'wc_admin_import_in_progress', __( 'An import is already in progress. Please allow the previous import to complete before beginning a new one.', 'woocommerce' ) );
		}

		self::reset_import_stats( $days, $skip_existing );
		foreach ( self::get_schedulers() as $scheduler ) {
			$scheduler::schedule_action( 'import_batch_init', array( $days, $skip_existing ) );
		}

		/**
		 * Fires when report data regeneration begins.
		 *
		 * @param int|bool $days Number of days to import.
		 * @param bool     $skip_existing Skip existing records.
		 */
		do_action( 'woocommerce_analytics_regenerate_init', $days, $skip_existing );

		return __( 'Report table data is being rebuilt. Please allow some time for data to fully populate.', 'woocommerce' );
	}

	/**
	 * Update the import stat totals and counts.
	 *
	 * @param int|bool $days Number of days to import.
	 * @param bool     $skip_existing Skip existing records.
	 */
	public static function reset_import_stats( $days, $skip_existing ) {
		$import_stats = get_option( ImportScheduler::IMPORT_STATS_OPTION, array() );
		$totals       = self::get_import_totals( $days, $skip_existing );

		foreach ( self::get_schedulers() as $scheduler ) {
			$import_stats[ $scheduler::$name ]['imported'] = 0;
			$import_stats[ $scheduler::$name ]['total']    = $totals[ $scheduler::$name ];
		}

		// Update imported from date if older than previous.
		$previous_import_date = isset( $import_stats['imported_from'] ) ? $import_stats['imported_from'] : null;
		$current_import_date  = $days ? gmdate( 'Y-m-d 00:00:00', time() - ( DAY_IN_SECONDS * $days ) ) : -1;

		if ( ! $previous_import_date || -1 === $current_import_date || new \DateTime( $previous_import_date ) > new \DateTime( $current_import_date ) ) {
			$import_stats['imported_from'] = $current_import_date;
		}

		update_option( ImportScheduler::IMPORT_STATS_OPTION, $import_stats );
	}

	/**
	 * Get stats for current import.
	 *
	 * @return array
	 */
	public static function get_import_stats() {
		$import_stats                 = get_option( ImportScheduler::IMPORT_STATS_OPTION, array() );
		$import_stats['is_importing'] = self::is_importing();

		return $import_stats;
	}

	/**
	 * Get the import totals for all syncs.
	 *
	 * @param int|bool $days Number of days to import.
	 * @param bool     $skip_existing Skip existing records.
	 * @return array
	 */
	public static function get_import_totals( $days, $skip_existing ) {
		$totals = array();

		foreach ( self::get_schedulers() as $scheduler ) {
			$items                       = $scheduler::get_items( 1, 1, $days, $skip_existing );
			$totals[ $scheduler::$name ] = $items->total;
		}

		return $totals;
	}

	/**
	 * Clears all queued actions.
	 */
	public static function clear_queued_actions() {
		foreach ( self::get_schedulers() as $scheduler ) {
			$scheduler::clear_queued_actions();
		}
	}

	/**
	 * Delete all data for reports.
	 *
	 * @return string
	 */
	public static function delete_report_data() {
		// Cancel all pending import jobs.
		self::clear_queued_actions();

		foreach ( self::get_schedulers() as $scheduler ) {
			$scheduler::schedule_action( 'delete_batch_init', array() );
		}

		// Delete import options.
		delete_option( ImportScheduler::IMPORT_STATS_OPTION );

		return __( 'Report table data is being deleted.', 'woocommerce' );
	}

	/**
	 * Clear the count cache when products are added or updated, or when
	 * the no/low stock options are changed.
	 *
	 * @param int $id Post/product ID.
	 */
	public static function clear_stock_count_cache( $id ) {
		delete_transient( 'wc_admin_stock_count_lowstock' );
		delete_transient( 'wc_admin_product_count' );
		$status_options = wc_get_product_stock_status_options();
		foreach ( $status_options as $status => $label ) {
			delete_transient( 'wc_admin_stock_count_' . $status );
		}
	}
}
