<?php

namespace Yoast\WP\SEO\Helpers;

use Yoast\WP\Lib\Model;

/**
 * A helper object for the cleanup of attachments.
 */
class Attachment_Cleanup_Helper {

	/**
	 * Removes all indexables for attachments.
	 *
	 * @param bool $suppress_errors Whether to suppress db errors when running the cleanup query.
	 *
	 * @return void
	 */
	public function remove_attachment_indexables( $suppress_errors ) {
		global $wpdb;

		if ( $suppress_errors ) {
			// If migrations haven't been completed successfully the following may give false errors. So suppress them.
			$show_errors       = $wpdb->show_errors;
			$wpdb->show_errors = false;
		}

		$indexable_table = Model::get_table_name( 'Indexable' );

		$delete_query = "DELETE FROM $indexable_table WHERE object_type = 'post' AND object_sub_type = 'attachment'";

		// phpcs:disable WordPress.DB.DirectDatabaseQuery.NoCaching -- Reason: No relevant caches.
		// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery -- Reason: Most performant way.
		// phpcs:disable WordPress.DB.PreparedSQL.NotPrepared -- Reason: Is it prepared already.
		$wpdb->query( $delete_query );
		// phpcs:enable

		if ( $suppress_errors ) {
			$wpdb->show_errors = $show_errors;
		}
	}

	/**
	 * Cleans all attachment links in the links table from target indexable ids.
	 *
	 * @param bool $suppress_errors Whether to suppress db errors when running the cleanup query.
	 *
	 * @return void
	 */
	public function clean_attachment_links_from_target_indexable_ids( $suppress_errors ) {
		global $wpdb;

		if ( $suppress_errors ) {
			// If migrations haven't been completed successfully the following may give false errors. So suppress them.
			$show_errors       = $wpdb->show_errors;
			$wpdb->show_errors = false;
		}

		$links_table = Model::get_table_name( 'SEO_Links' );

		$query = "UPDATE $links_table SET target_indexable_id = NULL WHERE type = 'image-in'";

		// phpcs:disable WordPress.DB.DirectDatabaseQuery.NoCaching -- Reason: No relevant caches.
		// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery -- Reason: Most performant way.
		// phpcs:disable WordPress.DB.PreparedSQL.NotPrepared -- Reason: Is it prepared already.
		$wpdb->query( $query );
		// phpcs:enable

		if ( $suppress_errors ) {
			$wpdb->show_errors = $show_errors;
		}
	}
}
