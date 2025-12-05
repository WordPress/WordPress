<?php

namespace Yoast\WP\SEO\Actions\Indexing;

/**
 * Interface definition of a reindexing action for indexables that have a limited unindexed count.
 */
interface Limited_Indexing_Action_Interface {

	/**
	 * Returns a limited number of unindexed posts.
	 *
	 * @param int $limit Limit the maximum number of unindexed posts that are counted.
	 *
	 * @return int|false The limited number of unindexed posts. False if the query fails.
	 */
	public function get_limited_unindexed_count( $limit );
}
