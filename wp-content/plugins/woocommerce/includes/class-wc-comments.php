<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Comments
 *
 * Handle comments (reviews and order notes)
 *
 * @class 		WC_Post_types
 * @version		2.1.0
 * @package		WooCommerce/Classes/Products
 * @category	Class
 * @author 		WooThemes
 */
class WC_Comments {

	/**
	 * Constructor
	 */
	public function __construct() {
		// Rating posts
		add_filter( 'preprocess_comment', array( $this, 'check_comment_rating' ), 0 );
		add_action( 'comment_post', array( $this, 'add_comment_rating' ), 1 );

		// clear transients
		add_action( 'wp_set_comment_status', array( $this, 'clear_transients' ) );
		add_action( 'edit_comment', array( $this, 'clear_transients' ) );

		// Secure order notes
		add_filter( 'comments_clauses', array( __CLASS__, 'exclude_order_comments' ), 10, 1 );
		add_action( 'comment_feed_join', array( $this, 'exclude_order_comments_from_feed_join' ) );
		add_action( 'comment_feed_where', array( $this, 'exclude_order_comments_from_feed_where' ) );
	}

	/**
	 * Exclude order comments from queries and RSS
	 *
	 * This code should exclude shop_order comments from queries. Some queries (like the recent comments widget on the dashboard) are hardcoded
	 * and are not filtered, however, the code current_user_can( 'read_post', $comment->comment_post_ID ) should keep them safe since only admin and
	 * shop managers can view orders anyway.
	 *
	 * The frontend view order pages get around this filter by using remove_filter('comments_clauses', array( 'WC_Comments' ,'exclude_order_comments'), 10, 1 );
	 *
	 * @param array $clauses
	 * @return array
	 */
	public static function exclude_order_comments( $clauses ) {
		global $wpdb, $typenow, $pagenow;

		if ( is_admin() && $typenow == 'shop_order' && current_user_can( 'manage_woocommerce' ) )
			return $clauses; // Don't hide when viewing orders in admin

		if ( ! $clauses['join'] )
			$clauses['join'] = '';

		if ( ! strstr( $clauses['join'], "JOIN $wpdb->posts" ) )
			$clauses['join'] .= " LEFT JOIN $wpdb->posts ON comment_post_ID = $wpdb->posts.ID ";

		if ( $clauses['where'] )
			$clauses['where'] .= ' AND ';

		$clauses['where'] .= " $wpdb->posts.post_type NOT IN ('shop_order') ";

		return $clauses;
	}

	/**
	 * Exclude order comments from queries and RSS
	 *
	 * @param string $join
	 * @return string
	 */
	public function exclude_order_comments_from_feed_join( $join ) {
		global $wpdb;

	    if ( ! strstr( $join, $wpdb->posts ) ) 
	    	$join = " LEFT JOIN $wpdb->posts ON $wpdb->comments.comment_post_ID = $wpdb->posts.ID ";

	    return $join;
	}

	/**
	 * Exclude order comments from queries and RSS
	 *
	 * @param string $where
	 * @return string
	 */
	public function exclude_order_comments_from_feed_where( $where ) {
		global $wpdb;

	    if ( $where )
	    	$where .= ' AND ';

		$where .= " $wpdb->posts.post_type NOT IN ('shop_order') ";

	    return $where;
	}

	/**
	 * Validate the comment ratings.
	 *
	 * @param array $comment_data
	 * @return array
	 */
	public function check_comment_rating( $comment_data ) {
		// If posting a comment (not trackback etc) and not logged in
		if ( isset( $_POST['rating'] ) && empty( $_POST['rating'] ) && $comment_data['comment_type'] === '' && get_option('woocommerce_review_rating_required') === 'yes' ) {
			wp_die( __( 'Please rate the product.', 'woocommerce' ) );
			exit;
		}
		return $comment_data;
	}

	/**
	 * Rating field for comments.
	 *
	 * @param mixed $comment_id
	 */
	public function add_comment_rating( $comment_id ) {
		if ( isset( $_POST['rating'] ) ) {

			if ( ! $_POST['rating'] || $_POST['rating'] > 5 || $_POST['rating'] < 0 )
				return;

			add_comment_meta( $comment_id, 'rating', (int) esc_attr( $_POST['rating'] ), true );

			$this->clear_transients( $comment_id );
		}
	}

	/**
	 * Clear transients for a review.
	 *
	 * @param mixed $comment_id
	 */
	public function clear_transients( $comment_id ) {
		$comment = get_comment( $comment_id );

		if ( ! empty( $comment->comment_post_ID ) ) {
			delete_transient( 'wc_average_rating_' . absint( $comment->comment_post_ID ) );
			delete_transient( 'wc_rating_count_' . absint( $comment->comment_post_ID ) );
		}
	}
}

new WC_Comments();
