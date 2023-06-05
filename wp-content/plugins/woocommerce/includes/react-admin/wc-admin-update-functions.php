<?php
/**
 * WooCommerce Admin Updates
 *
 * Functions for updating data, used by the background updater.
 *
 * @package WooCommerce\Admin
 */

use Automattic\WooCommerce\Admin\Features\OnboardingTasks\TaskLists;
use Automattic\WooCommerce\Admin\Notes\Notes;
use Automattic\WooCommerce\Internal\Admin\Notes\UnsecuredReportFiles;
use Automattic\WooCommerce\Admin\ReportExporter;

/**
 * Update order stats `status` index length.
 * See: https://github.com/woocommerce/woocommerce-admin/issues/2969.
 */
function wc_admin_update_0201_order_status_index() {
	global $wpdb;

	// Max DB index length. See wp_get_db_schema().
	$max_index_length = 191;

	$index = $wpdb->get_row( "SHOW INDEX FROM {$wpdb->prefix}wc_order_stats WHERE key_name = 'status'" );

	if ( property_exists( $index, 'Sub_part' ) ) {
		// The index was created with the right length. Time to bail.
		if ( $max_index_length === $index->Sub_part ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName
			return;
		}

		// We need to drop the index so it can be recreated.
		$wpdb->query( "DROP INDEX `status` ON {$wpdb->prefix}wc_order_stats" );
	}

	// Recreate the status index with a max length.
	$wpdb->query( $wpdb->prepare( "ALTER TABLE {$wpdb->prefix}wc_order_stats ADD INDEX status (status(%d))", $max_index_length ) );
}

/**
 * Rename "gross_total" to "total_sales".
 * See: https://github.com/woocommerce/woocommerce-admin/issues/3175
 */
function wc_admin_update_0230_rename_gross_total() {
	global $wpdb;

	// We first need to drop the new `total_sales` column, since dbDelta() will have created it.
	$wpdb->query( "ALTER TABLE {$wpdb->prefix}wc_order_stats DROP COLUMN `total_sales`" );
	// Then we can rename the existing `gross_total` column.
	$wpdb->query( "ALTER TABLE {$wpdb->prefix}wc_order_stats CHANGE COLUMN `gross_total` `total_sales` double DEFAULT 0 NOT NULL" );
}

/**
 * Remove the note unsnoozing scheduled action.
 */
function wc_admin_update_0251_remove_unsnooze_action() {
	as_unschedule_action( Notes::UNSNOOZE_HOOK, null, 'wc-admin-data' );
	as_unschedule_action( Notes::UNSNOOZE_HOOK, null, 'wc-admin-notes' );
}

/**
 * Remove Facebook Extension note.
 */
function wc_admin_update_110_remove_facebook_note() {
	Notes::delete_notes_with_name( 'wc-admin-facebook-extension' );
}

/**
 * Remove Dismiss action from tracking opt-in admin note.
 */
function wc_admin_update_130_remove_dismiss_action_from_tracking_opt_in_note() {
	global $wpdb;

	$wpdb->query( "DELETE actions FROM {$wpdb->prefix}wc_admin_note_actions actions INNER JOIN {$wpdb->prefix}wc_admin_notes notes USING (note_id) WHERE actions.name = 'tracking-dismiss' AND notes.name = 'wc-admin-usage-tracking-opt-in'" );
}

/**

 * Update DB Version.
 */
function wc_admin_update_130_db_version() {
	Installer::update_db_version( '1.3.0' );
}

/**
 * Update DB Version.
 */
function wc_admin_update_140_db_version() {
	Installer::update_db_version( '1.4.0' );
}

/**
 * Remove Facebook Experts note.
 */
function wc_admin_update_160_remove_facebook_note() {
	Notes::delete_notes_with_name( 'wc-admin-facebook-marketing-expert' );
}

/**
 * Set "two column" homescreen layout as default for existing stores.
 */
function wc_admin_update_170_homescreen_layout() {
	add_option( 'woocommerce_default_homepage_layout', 'two_columns', '', 'no' );
}

/**
 * Delete the preexisting export files.
 */
function wc_admin_update_270_delete_report_downloads() {
	$upload_dir = wp_upload_dir();
	$base_dir   = trailingslashit( $upload_dir['basedir'] );

	$failed_files   = array();
	$exports_status = get_option( ReportExporter::EXPORT_STATUS_OPTION, array() );
	$has_failure    = false;

	if ( ! is_array( $exports_status ) ) {
		// This is essentially the same path as files failing deletion. Handle as such.
		return;
	}

	// Delete all export files based on the status option values.
	foreach ( $exports_status as $key => $progress ) {
		list( $report_type, $export_id ) = explode( ':', $key );

		if ( ! $export_id ) {
			continue;
		}

		$file   = "{$base_dir}wc-{$report_type}-report-export-{$export_id}.csv";
		$header = $file . '.headers';

		// phpcs:ignore
		if ( @file_exists( $file ) && false === @unlink( $file ) ) {
			array_push( $failed_files, $file );
		}

		// phpcs:ignore
		if ( @file_exists( $header ) && false === @unlink( $header ) ) {
			array_push( $failed_files, $header );
		}
	}

	// If the status option was missing or corrupt, there will be files left over.
	$potential_exports = glob( $base_dir . 'wc-*-report-export-*.csv' );
	$reports_pattern   = '(revenue|products|variations|orders|categories|coupons|taxes|stock|customers|downloads)';

	/**
	 * Look for files we can be reasonably sure were created by the report export.
	 *
	 * Export files we created will match the 'wc-*-report-export-*.csv' glob, with
	 * the first wildcard being one of the exportable report slugs, and the second
	 * being an integer with 11-14 digits (from microtime()'s output) that represents
	 * a time in the past.
	 */
	foreach ( $potential_exports as $potential_export ) {
		$matches = array();
		// See if the filename matches an unfiltered export pattern.
		if ( ! preg_match( "/wc-{$reports_pattern}-report-export-(?P<export_id>\d{11,14})\.csv\$/", $potential_export, $matches ) ) {
			$has_failure = true;
			continue;
		}

		// Validate the timestamp (anything in the past).
		$timestamp = (int) substr( $matches['export_id'], 0, 10 );

		if ( ! $timestamp || $timestamp > time() ) {
			$has_failure = true;
			continue;
		}

		// phpcs:ignore
		if ( false === @unlink( $potential_export ) ) {
			array_push( $failed_files, $potential_export );
		}
	}

	// Try deleting failed files once more.
	foreach ( $failed_files as $failed_file ) {
		// phpcs:ignore
		if ( false === @unlink( $failed_file ) ) {
			$has_failure = true;
		}
	}

	if ( $has_failure ) {
		UnsecuredReportFiles::possibly_add_note();
	}
}

/**
 * Update the old task list options.
 */
function wc_admin_update_271_update_task_list_options() {
	$hidden_lists         = get_option( 'woocommerce_task_list_hidden_lists', array() );
	$setup_list_hidden    = get_option( 'woocommerce_task_list_hidden', 'no' );
	$extended_list_hidden = get_option( 'woocommerce_extended_task_list_hidden', 'no' );
	if ( 'yes' === $setup_list_hidden ) {
		$hidden_lists[] = 'setup';
	}
	if ( 'yes' === $extended_list_hidden ) {
		$hidden_lists[] = 'extended';
	}

	update_option( 'woocommerce_task_list_hidden_lists', array_unique( $hidden_lists ) );
	delete_option( 'woocommerce_task_list_hidden' );
	delete_option( 'woocommerce_extended_task_list_hidden' );
}

/**
 * Update order stats `status`.
 */
function wc_admin_update_280_order_status() {
	global $wpdb;

	$wpdb->query(
		"UPDATE {$wpdb->prefix}wc_order_stats refunds
		INNER JOIN {$wpdb->prefix}wc_order_stats orders
			ON orders.order_id = refunds.parent_id
		SET refunds.status = orders.status
		WHERE refunds.parent_id != 0"
	);
}

/**
 * Update the old task list options.
 */
function wc_admin_update_290_update_apperance_task_option() {
	$is_actioned = get_option( 'woocommerce_task_list_appearance_complete', false );

	$task = TaskLists::get_task( 'appearance' );
	if ( $task && $is_actioned ) {
		$task->mark_actioned();
	}

	delete_option( 'woocommerce_task_list_appearance_complete' );
}

/**
 * Delete the old woocommerce_default_homepage_layout option.
 */
function wc_admin_update_290_delete_default_homepage_layout_option() {
	delete_option( 'woocommerce_default_homepage_layout' );
}

/**
 * Use woocommerce_admin_activity_panel_inbox_last_read from the user meta to set wc_admin_notes.is_read col.
 */
function wc_admin_update_300_update_is_read_from_last_read() {
	global $wpdb;
	$meta_key = 'woocommerce_admin_activity_panel_inbox_last_read';
	// phpcs:ignore
	$users    = get_users( "meta_key={$meta_key}&orderby={$meta_key}&fields=all_with_meta&number=1" );

	if ( count( $users ) ) {
		$last_read   = current( $users )->{$meta_key};
		$date_in_utc = gmdate( 'Y-m-d H:i:s', intval( $last_read ) / 1000 );
		$wpdb->query(
			$wpdb->prepare(
				"
				update {$wpdb->prefix}wc_admin_notes set is_read = 1
				where
				date_created <= %s",
				$date_in_utc
			)
		);
		$wpdb->query( $wpdb->prepare( "delete from {$wpdb->usermeta} where meta_key=%s", $meta_key ) );
	}
}

/**
 * Delete "is_primary" column from the wc_admin_notes table.
 */
function wc_admin_update_340_remove_is_primary_from_note_action() {
	global $wpdb;
	$wpdb->query( "ALTER TABLE {$wpdb->prefix}wc_admin_note_actions DROP COLUMN `is_primary`" );
}

/**
 * Delete the deprecated remote inbox notifications option since transients are now used.
 */
function wc_update_670_delete_deprecated_remote_inbox_notifications_option() {
	delete_option( 'wc_remote_inbox_notifications_specs' );
}