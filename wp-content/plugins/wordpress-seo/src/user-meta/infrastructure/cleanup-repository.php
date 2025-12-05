<?php

namespace Yoast\WP\SEO\User_Meta\Infrastructure;

/**
 * Repository going into the database to clean up.
 */
class Cleanup_Repository {

	/**
	 * Deletes empty usermeta based on their meta_keys and returns the number of the deleted meta.
	 *
	 * @param array<string> $meta_keys The meta to be potentially deleted.
	 * @param int           $limit     The number of maximum deletions.
	 *
	 * @return int|false The number of rows that was deleted or false if the query failed.
	 */
	public function delete_empty_usermeta_query( $meta_keys, $limit ) {
		global $wpdb;

		// phpcs:disable WordPress.DB.PreparedSQLPlaceholders.ReplacementsWrongNumber -- Reason: we're passing an array instead.
		$delete_query = $wpdb->prepare(
			'DELETE FROM %i
			WHERE meta_key IN ( ' . \implode( ', ', \array_fill( 0, \count( $meta_keys ), '%s' ) ) . ' )
			AND meta_value = ""
			ORDER BY user_id
			LIMIT %d',
			\array_merge( [ $wpdb->usermeta ], $meta_keys, [ $limit ] )
		);
		// phpcs:enable

		// phpcs:disable WordPress.DB.DirectDatabaseQuery.NoCaching -- Reason: No relevant caches.
		// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery -- Reason: Most performant way.
		// phpcs:disable WordPress.DB.PreparedSQL.NotPrepared -- Reason: Is it prepared already.
		return $wpdb->query( $delete_query );
		// phpcs:enable
	}
}
