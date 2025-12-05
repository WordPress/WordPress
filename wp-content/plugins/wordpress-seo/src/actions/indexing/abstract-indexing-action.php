<?php

namespace Yoast\WP\SEO\Actions\Indexing;

/**
 * Base class of indexing actions.
 */
abstract class Abstract_Indexing_Action implements Indexation_Action_Interface, Limited_Indexing_Action_Interface {

	/**
	 * The transient name.
	 *
	 * This is a trick to force derived classes to define a transient themselves.
	 *
	 * @var string
	 */
	public const UNINDEXED_COUNT_TRANSIENT = null;

	/**
	 * The transient cache key for limited counts.
	 *
	 * @var string
	 */
	public const UNINDEXED_LIMITED_COUNT_TRANSIENT = self::UNINDEXED_COUNT_TRANSIENT . '_limited';

	/**
	 * Builds a query for selecting the ID's of unindexed posts.
	 *
	 * @param bool $limit The maximum number of post IDs to return.
	 *
	 * @return string The prepared query string.
	 */
	abstract protected function get_select_query( $limit );

	/**
	 * Builds a query for counting the number of unindexed posts.
	 *
	 * @return string The prepared query string.
	 */
	abstract protected function get_count_query();

	/**
	 * Returns a limited number of unindexed posts.
	 *
	 * @param int $limit Limit the maximum number of unindexed posts that are counted.
	 *
	 * @return int The limited number of unindexed posts. 0 if the query fails.
	 */
	public function get_limited_unindexed_count( $limit ) {
		$transient = \get_transient( static::UNINDEXED_LIMITED_COUNT_TRANSIENT );
		if ( $transient !== false ) {
			return (int) $transient;
		}

		\set_transient( static::UNINDEXED_LIMITED_COUNT_TRANSIENT, 0, ( \MINUTE_IN_SECONDS * 15 ) );

		$query = $this->get_select_query( $limit );

		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- Function get_count_query returns a prepared query.
		$unindexed_object_ids = ( $query === '' ) ? [] : $this->wpdb->get_col( $query );
		$count                = (int) \count( $unindexed_object_ids );

		\set_transient( static::UNINDEXED_LIMITED_COUNT_TRANSIENT, $count, ( \MINUTE_IN_SECONDS * 15 ) );

		return $count;
	}

	/**
	 * Returns the total number of unindexed posts.
	 *
	 * @return int|false The total number of unindexed posts. False if the query fails.
	 */
	public function get_total_unindexed() {
		$transient = \get_transient( static::UNINDEXED_COUNT_TRANSIENT );
		if ( $transient !== false ) {
			return (int) $transient;
		}

		// Store transient before doing the query so multiple requests won't make multiple queries.
		// Only store this for 15 minutes to ensure that if the query doesn't complete a wrong count is not kept too long.
		\set_transient( static::UNINDEXED_COUNT_TRANSIENT, 0, ( \MINUTE_IN_SECONDS * 15 ) );

		$query = $this->get_count_query();

		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- Function get_count_query returns a prepared query.
		$count = ( $query === '' ) ? 0 : $this->wpdb->get_var( $query );

		if ( $count === null ) {
			return false;
		}

		\set_transient( static::UNINDEXED_COUNT_TRANSIENT, $count, \DAY_IN_SECONDS );

		/**
		 * Action: 'wpseo_indexables_unindexed_calculated' - sets an option to timestamp when there are no unindexed indexables left.
		 *
		 * @internal
		 */
		\do_action( 'wpseo_indexables_unindexed_calculated', static::UNINDEXED_COUNT_TRANSIENT, $count );

		return (int) $count;
	}
}
