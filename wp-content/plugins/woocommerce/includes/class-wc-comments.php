<?php
/**
 * Comments
 *
 * Handle comments (reviews and order notes).
 *
 * @package WooCommerce\Classes\Products
 * @version 2.3.0
 */

use Automattic\WooCommerce\Internal\Admin\ProductReviews\ReviewsUtil;

defined( 'ABSPATH' ) || exit;

/**
 * Comments class.
 */
class WC_Comments {

	/**
	 * Hook in methods.
	 */
	public static function init() {
		// Rating posts.
		add_filter( 'comments_open', array( __CLASS__, 'comments_open' ), 10, 2 );
		add_filter( 'preprocess_comment', array( __CLASS__, 'check_comment_rating' ), 0 );
		add_action( 'comment_post', array( __CLASS__, 'add_comment_rating' ), 1 );
		add_action( 'comment_moderation_recipients', array( __CLASS__, 'comment_moderation_recipients' ), 10, 2 );

		// Clear transients.
		add_action( 'wp_update_comment_count', array( __CLASS__, 'clear_transients' ) );

		// Secure order notes.
		add_filter( 'comments_clauses', array( __CLASS__, 'exclude_order_comments' ), 10, 1 );
		add_filter( 'comment_feed_where', array( __CLASS__, 'exclude_order_comments_from_feed_where' ) );

		// Secure webhook comments.
		add_filter( 'comments_clauses', array( __CLASS__, 'exclude_webhook_comments' ), 10, 1 );
		add_filter( 'comment_feed_where', array( __CLASS__, 'exclude_webhook_comments_from_feed_where' ) );

		// Exclude product reviews.
		add_filter( 'comments_clauses', array( ReviewsUtil::class, 'comments_clauses_without_product_reviews' ), 10, 1 );

		// Count comments.
		add_filter( 'wp_count_comments', array( __CLASS__, 'wp_count_comments' ), 10, 2 );

		// Delete comments count cache whenever there is a new comment or a comment status changes.
		add_action( 'wp_insert_comment', array( __CLASS__, 'delete_comments_count_cache' ) );
		add_action( 'wp_set_comment_status', array( __CLASS__, 'delete_comments_count_cache' ) );

		// Support avatars for `review` comment type.
		add_filter( 'get_avatar_comment_types', array( __CLASS__, 'add_avatar_for_review_comment_type' ) );

		// Add Product Reviews filter for `review` comment type.
		add_filter( 'admin_comment_types_dropdown', array( __CLASS__, 'add_review_comment_filter' ) );

		// Review of verified purchase.
		add_action( 'comment_post', array( __CLASS__, 'add_comment_purchase_verification' ) );

		// Set comment type.
		add_action( 'preprocess_comment', array( __CLASS__, 'update_comment_type' ), 1 );

		// Validate product reviews if requires verified owners.
		add_action( 'pre_comment_on_post', array( __CLASS__, 'validate_product_review_verified_owners' ) );
	}

	/**
	 * See if comments are open.
	 *
	 * @since  3.1.0
	 * @param  bool $open    Whether the current post is open for comments.
	 * @param  int  $post_id Post ID.
	 * @return bool
	 */
	public static function comments_open( $open, $post_id ) {
		if ( 'product' === get_post_type( $post_id ) && ! post_type_supports( 'product', 'comments' ) ) {
			$open = false;
		}
		return $open;
	}

	/**
	 * Exclude order comments from queries and RSS.
	 *
	 * This code should exclude shop_order comments from queries. Some queries (like the recent comments widget on the dashboard) are hardcoded.
	 * and are not filtered, however, the code current_user_can( 'read_post', $comment->comment_post_ID ) should keep them safe since only admin and.
	 * shop managers can view orders anyway.
	 *
	 * The frontend view order pages get around this filter by using remove_filter('comments_clauses', array( 'WC_Comments' ,'exclude_order_comments'), 10, 1 );
	 *
	 * @param  array $clauses A compacted array of comment query clauses.
	 * @return array
	 */
	public static function exclude_order_comments( $clauses ) {
		$clauses['where'] .= ( $clauses['where'] ? ' AND ' : '' ) . " comment_type != 'order_note' ";
		return $clauses;
	}

	/**
	 * Exclude order comments from feed.
	 *
	 * @deprecated 3.1
	 * @param mixed $join Deprecated.
	 */
	public static function exclude_order_comments_from_feed_join( $join ) {
		wc_deprecated_function( 'WC_Comments::exclude_order_comments_from_feed_join', '3.1' );
	}

	/**
	 * Exclude order comments from queries and RSS.
	 *
	 * @param  string $where The WHERE clause of the query.
	 * @return string
	 */
	public static function exclude_order_comments_from_feed_where( $where ) {
		return $where . ( $where ? ' AND ' : '' ) . " comment_type != 'order_note' ";
	}

	/**
	 * Exclude webhook comments from queries and RSS.
	 *
	 * @since  2.2
	 * @param  array $clauses A compacted array of comment query clauses.
	 * @return array
	 */
	public static function exclude_webhook_comments( $clauses ) {
		$clauses['where'] .= ( $clauses['where'] ? ' AND ' : '' ) . " comment_type != 'webhook_delivery' ";
		return $clauses;
	}

	/**
	 * Exclude webhooks comments from feed.
	 *
	 * @deprecated 3.1
	 * @param mixed $join Deprecated.
	 */
	public static function exclude_webhook_comments_from_feed_join( $join ) {
		wc_deprecated_function( 'WC_Comments::exclude_webhook_comments_from_feed_join', '3.1' );
	}

	/**
	 * Exclude webhook comments from queries and RSS.
	 *
	 * @since  2.1
	 * @param  string $where The WHERE clause of the query.
	 * @return string
	 */
	public static function exclude_webhook_comments_from_feed_where( $where ) {
		return $where . ( $where ? ' AND ' : '' ) . " comment_type != 'webhook_delivery' ";
	}

	/**
	 * Validate the comment ratings.
	 *
	 * @param  array $comment_data Comment data.
	 * @return array
	 */
	public static function check_comment_rating( $comment_data ) {
		// If posting a comment (not trackback etc) and not logged in.
		if ( ! is_admin() && isset( $_POST['comment_post_ID'], $_POST['rating'], $comment_data['comment_type'] ) && 'product' === get_post_type( absint( $_POST['comment_post_ID'] ) ) && empty( $_POST['rating'] ) && self::is_default_comment_type( $comment_data['comment_type'] ) && wc_review_ratings_enabled() && wc_review_ratings_required() ) { // WPCS: input var ok, CSRF ok.
			wp_die( esc_html__( 'Please rate the product.', 'woocommerce' ) );
			exit;
		}
		return $comment_data;
	}

	/**
	 * Rating field for comments.
	 *
	 * @param int $comment_id Comment ID.
	 */
	public static function add_comment_rating( $comment_id ) {
		if ( isset( $_POST['rating'], $_POST['comment_post_ID'] ) && 'product' === get_post_type( absint( $_POST['comment_post_ID'] ) ) ) { // WPCS: input var ok, CSRF ok.
			if ( ! $_POST['rating'] || $_POST['rating'] > 5 || $_POST['rating'] < 0 ) { // WPCS: input var ok, CSRF ok, sanitization ok.
				return;
			}
			add_comment_meta( $comment_id, 'rating', intval( $_POST['rating'] ), true ); // WPCS: input var ok, CSRF ok.

			$post_id = isset( $_POST['comment_post_ID'] ) ? absint( $_POST['comment_post_ID'] ) : 0; // WPCS: input var ok, CSRF ok.
			if ( $post_id ) {
				self::clear_transients( $post_id );
			}
		}
	}

	/**
	 * Modify recipient of review email.
	 *
	 * @param array $emails     Emails.
	 * @param int   $comment_id Comment ID.
	 * @return array
	 */
	public static function comment_moderation_recipients( $emails, $comment_id ) {
		$comment = get_comment( $comment_id );

		if ( $comment && 'product' === get_post_type( $comment->comment_post_ID ) ) {
			$emails = array( get_option( 'admin_email' ) );
		}

		return $emails;
	}

	/**
	 * Ensure product average rating and review count is kept up to date.
	 *
	 * @param int $post_id Post ID.
	 */
	public static function clear_transients( $post_id ) {
		if ( 'product' === get_post_type( $post_id ) ) {
			$product = wc_get_product( $post_id );
			$product->set_rating_counts( self::get_rating_counts_for_product( $product ) );
			$product->set_average_rating( self::get_average_rating_for_product( $product ) );
			$product->set_review_count( self::get_review_count_for_product( $product ) );
			$product->save();
		}
	}

	/**
	 * Delete comments count cache whenever there is
	 * new comment or the status of a comment changes. Cache
	 * will be regenerated next time WC_Comments::wp_count_comments()
	 * is called.
	 */
	public static function delete_comments_count_cache() {
		delete_transient( 'wc_count_comments' );
	}

	/**
	 * Remove order notes, webhook delivery logs, and product reviews from wp_count_comments().
	 *
	 * @since  2.2
	 * @param  object $stats   Comment stats.
	 * @param  int    $post_id Post ID.
	 * @return object
	 */
	public static function wp_count_comments( $stats, $post_id ) {
		global $wpdb;

		if ( 0 === $post_id ) {
			$stats = get_transient( 'wc_count_comments' );

			if ( ! $stats ) {
				$stats = array(
					'total_comments' => 0,
					'all'            => 0,
				);

				$count = $wpdb->get_results(
					"
					SELECT comment_approved, COUNT(*) AS num_comments
					FROM {$wpdb->comments}
					LEFT JOIN {$wpdb->posts} ON comment_post_ID = {$wpdb->posts}.ID
					WHERE comment_type NOT IN ('action_log', 'order_note', 'webhook_delivery') AND {$wpdb->posts}.post_type NOT IN ('product')
					GROUP BY comment_approved
					",
					ARRAY_A
				);

				$approved = array(
					'0'            => 'moderated',
					'1'            => 'approved',
					'spam'         => 'spam',
					'trash'        => 'trash',
					'post-trashed' => 'post-trashed',
				);

				foreach ( (array) $count as $row ) {
					// Don't count post-trashed toward totals.
					if ( ! in_array( $row['comment_approved'], array( 'post-trashed', 'trash', 'spam' ), true ) ) {
						$stats['all']            += $row['num_comments'];
						$stats['total_comments'] += $row['num_comments'];
					} elseif ( ! in_array( $row['comment_approved'], array( 'post-trashed', 'trash' ), true ) ) {
						$stats['total_comments'] += $row['num_comments'];
					}
					if ( isset( $approved[ $row['comment_approved'] ] ) ) {
						$stats[ $approved[ $row['comment_approved'] ] ] = $row['num_comments'];
					}
				}

				foreach ( $approved as $key ) {
					if ( empty( $stats[ $key ] ) ) {
						$stats[ $key ] = 0;
					}
				}

				$stats = (object) $stats;
				set_transient( 'wc_count_comments', $stats );
			}
		}

		return $stats;
	}

	/**
	 * Make sure WP displays avatars for comments with the `review` type.
	 *
	 * @since  2.3
	 * @param  array $comment_types Comment types.
	 * @return array
	 */
	public static function add_avatar_for_review_comment_type( $comment_types ) {
		return array_merge( $comment_types, array( 'review' ) );
	}

	/**
	 * Add Product Reviews filter for `review` comment type.
	 *
	 * @since 6.0.0
	 *
	 * @param array $comment_types Array of comment type labels keyed by their name.
	 *
	 * @return array
	 */
	public static function add_review_comment_filter( array $comment_types ): array {
		$comment_types['review'] = __( 'Product Reviews', 'woocommerce' );
		return $comment_types;
	}

	/**
	 * Determine if a review is from a verified owner at submission.
	 *
	 * @param int $comment_id Comment ID.
	 * @return bool
	 */
	public static function add_comment_purchase_verification( $comment_id ) {
		$comment  = get_comment( $comment_id );
		$verified = false;
		if ( 'product' === get_post_type( $comment->comment_post_ID ) ) {
			$verified = wc_customer_bought_product( $comment->comment_author_email, $comment->user_id, $comment->comment_post_ID );
			add_comment_meta( $comment_id, 'verified', (int) $verified, true );
		}
		return $verified;
	}

	/**
	 * Get product rating for a product. Please note this is not cached.
	 *
	 * @since 3.0.0
	 * @param WC_Product $product Product instance.
	 * @return float
	 */
	public static function get_average_rating_for_product( &$product ) {
		global $wpdb;

		$count = $product->get_rating_count();

		if ( $count ) {
			$ratings = $wpdb->get_var(
				$wpdb->prepare(
					"
				SELECT SUM(meta_value) FROM $wpdb->commentmeta
				LEFT JOIN $wpdb->comments ON $wpdb->commentmeta.comment_id = $wpdb->comments.comment_ID
				WHERE meta_key = 'rating'
				AND comment_post_ID = %d
				AND comment_approved = '1'
				AND meta_value > 0
					",
					$product->get_id()
				)
			);
			$average = number_format( $ratings / $count, 2, '.', '' );
		} else {
			$average = 0;
		}

		return $average;
	}

	/**
	 * Utility function for getting review counts for multiple products in one query. This is not cached.
	 *
	 * @since 5.0.0
	 *
	 * @param array $product_ids Array of product IDs.
	 *
	 * @return array
	 */
	public static function get_review_counts_for_product_ids( $product_ids ) {
		global $wpdb;

		if ( empty( $product_ids ) ) {
			return array();
		}

		$product_id_string_placeholder = substr( str_repeat( ',%s', count( $product_ids ) ), 1 );

		$review_counts = $wpdb->get_results(
			// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Ignored for allowing interpolation in IN query.
			$wpdb->prepare(
				"
					SELECT comment_post_ID as product_id, COUNT( comment_post_ID ) as review_count
					FROM $wpdb->comments
					WHERE
						comment_parent = 0
						AND comment_post_ID IN ( $product_id_string_placeholder )
						AND comment_approved = '1'
						AND comment_type in ( 'review', '', 'comment' )
					GROUP BY product_id
				",
				$product_ids
			),
			// phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared.
			ARRAY_A
		);

		// Convert to key value pairs.
		$counts = array_replace( array_fill_keys( $product_ids, 0 ), array_column( $review_counts, 'review_count', 'product_id' ) );

		return $counts;
	}

	/**
	 * Get product review count for a product (not replies). Please note this is not cached.
	 *
	 * @since 3.0.0
	 * @param WC_Product $product Product instance.
	 * @return int
	 */
	public static function get_review_count_for_product( &$product ) {
		$counts = self::get_review_counts_for_product_ids( array( $product->get_id() ) );

		return $counts[ $product->get_id() ];
	}

	/**
	 * Get product rating count for a product. Please note this is not cached.
	 *
	 * @since 3.0.0
	 * @param WC_Product $product Product instance.
	 * @return int[]
	 */
	public static function get_rating_counts_for_product( &$product ) {
		global $wpdb;

		$counts     = array();
		$raw_counts = $wpdb->get_results(
			$wpdb->prepare(
				"
			SELECT meta_value, COUNT( * ) as meta_value_count FROM $wpdb->commentmeta
			LEFT JOIN $wpdb->comments ON $wpdb->commentmeta.comment_id = $wpdb->comments.comment_ID
			WHERE meta_key = 'rating'
			AND comment_post_ID = %d
			AND comment_approved = '1'
			AND meta_value > 0
			GROUP BY meta_value
				",
				$product->get_id()
			)
		);

		foreach ( $raw_counts as $count ) {
			$counts[ $count->meta_value ] = absint( $count->meta_value_count ); // WPCS: slow query ok.
		}

		return $counts;
	}

	/**
	 * Update comment type of product reviews.
	 *
	 * @since 3.5.0
	 * @param array $comment_data Comment data.
	 * @return array
	 */
	public static function update_comment_type( $comment_data ) {
		if ( ! is_admin() && isset( $_POST['comment_post_ID'], $comment_data['comment_type'] ) && self::is_default_comment_type( $comment_data['comment_type'] ) && 'product' === get_post_type( absint( $_POST['comment_post_ID'] ) ) ) { // WPCS: input var ok, CSRF ok.
			$comment_data['comment_type'] = 'review';
		}

		return $comment_data;
	}

	/**
	 * Validate product reviews if requires a verified owner.
	 *
	 * @param int $comment_post_id Post ID.
	 */
	public static function validate_product_review_verified_owners( $comment_post_id ) {
		// Only validate if option is enabled.
		if ( 'yes' !== get_option( 'woocommerce_review_rating_verification_required' ) ) {
			return;
		}

		// Validate only products.
		if ( 'product' !== get_post_type( $comment_post_id ) ) {
			return;
		}

		// Skip if is a verified owner.
		if ( wc_customer_bought_product( '', get_current_user_id(), $comment_post_id ) ) {
			return;
		}

		wp_die(
			esc_html__( 'Only logged in customers who have purchased this product may leave a review.', 'woocommerce' ),
			esc_html__( 'Reviews can only be left by "verified owners"', 'woocommerce' ),
			array(
				'code' => 403,
			)
		);
	}

	/**
	 * Determines if a comment is of the default type.
	 *
	 * Prior to WordPress 5.5, '' was the default comment type.
	 * As of 5.5, the default type is 'comment'.
	 *
	 * @since 4.3.0
	 * @param string $comment_type Comment type.
	 * @return bool
	 */
	private static function is_default_comment_type( $comment_type ) {
		return ( '' === $comment_type || 'comment' === $comment_type );
	}
}

WC_Comments::init();
