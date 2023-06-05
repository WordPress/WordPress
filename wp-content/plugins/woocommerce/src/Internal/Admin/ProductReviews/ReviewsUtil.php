<?php

namespace Automattic\WooCommerce\Internal\Admin\ProductReviews;

/**
 * A utility class for handling comments that are product reviews.
 */
class ReviewsUtil {

	/**
	 * Removes product reviews from the edit-comments page to fix the "Mine" tab counter.
	 *
	 * @param  array|mixed $clauses A compacted array of comment query clauses.
	 * @return array|mixed
	 */
	public static function comments_clauses_without_product_reviews( $clauses ) {
		global $wpdb, $current_screen;

		if ( isset( $current_screen->base ) && 'edit-comments' === $current_screen->base ) {
			$clauses['join']  .= " LEFT JOIN {$wpdb->posts} AS wp_posts_to_exclude_reviews ON comment_post_ID = wp_posts_to_exclude_reviews.ID ";
			$clauses['where'] .= ( $clauses['where'] ? ' AND ' : '' ) . " wp_posts_to_exclude_reviews.post_type NOT IN ('product') ";
		}

		return $clauses;
	}
}
