<?php
/**
 * WordPress Comment Administration API.
 *
 * @package WordPress
 * @subpackage Administration
 * @since 2.3.0
 */

/**
 * Determine if a comment exists based on author and date.
 *
 * For best performance, use `$timezone = 'gmt'`, which queries a field that is properly indexed. The default value
 * for `$timezone` is 'blog' for legacy reasons.
 *
 * @since 2.0.0
 * @since 4.4.0 Added the `$timezone` parameter.
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param string $comment_author Author of the comment.
 * @param string $comment_date   Date of the comment.
 * @param string $timezone       Timezone. Accepts 'blog' or 'gmt'. Default 'blog'.
 *
 * @return mixed Comment post ID on success.
 */
function comment_exists( $comment_author, $comment_date, $timezone = 'blog' ) {
	global $wpdb;

	$date_field = 'comment_date';
	if ( 'gmt' === $timezone ) {
		$date_field = 'comment_date_gmt';
	}

	return $wpdb->get_var(
		$wpdb->prepare(
			"SELECT comment_post_ID FROM $wpdb->comments
			WHERE comment_author = %s AND $date_field = %s",
			stripslashes( $comment_author ),
			stripslashes( $comment_date )
		)
	);
}

/**
 * Update a comment with values provided in $_POST.
 *
 * @since 2.0.0
 */
function edit_comment() {
	if ( ! current_user_can( 'edit_comment', (int) $_POST['comment_ID'] ) ) {
		wp_die( __( 'Sorry, you are not allowed to edit comments on this post.' ) );
	}

	if ( isset( $_POST['newcomment_author'] ) ) {
		$_POST['comment_author'] = $_POST['newcomment_author'];
	}
	if ( isset( $_POST['newcomment_author_email'] ) ) {
		$_POST['comment_author_email'] = $_POST['newcomment_author_email'];
	}
	if ( isset( $_POST['newcomment_author_url'] ) ) {
		$_POST['comment_author_url'] = $_POST['newcomment_author_url'];
	}
	if ( isset( $_POST['comment_status'] ) ) {
		$_POST['comment_approved'] = $_POST['comment_status'];
	}
	if ( isset( $_POST['content'] ) ) {
		$_POST['comment_content'] = $_POST['content'];
	}
	if ( isset( $_POST['comment_ID'] ) ) {
		$_POST['comment_ID'] = (int) $_POST['comment_ID'];
	}

	foreach ( array( 'aa', 'mm', 'jj', 'hh', 'mn' ) as $timeunit ) {
		if ( ! empty( $_POST[ 'hidden_' . $timeunit ] ) && $_POST[ 'hidden_' . $timeunit ] != $_POST[ $timeunit ] ) {
			$_POST['edit_date'] = '1';
			break;
		}
	}

	if ( ! empty( $_POST['edit_date'] ) ) {
		$aa = $_POST['aa'];
		$mm = $_POST['mm'];
		$jj = $_POST['jj'];
		$hh = $_POST['hh'];
		$mn = $_POST['mn'];
		$ss = $_POST['ss'];
		$jj = ( $jj > 31 ) ? 31 : $jj;
		$hh = ( $hh > 23 ) ? $hh - 24 : $hh;
		$mn = ( $mn > 59 ) ? $mn - 60 : $mn;
		$ss = ( $ss > 59 ) ? $ss - 60 : $ss;

		$_POST['comment_date'] = "$aa-$mm-$jj $hh:$mn:$ss";
	}

	wp_update_comment( $_POST );
}

/**
 * Returns a WP_Comment object based on comment ID.
 *
 * @since 2.0.0
 *
 * @param int $id ID of comment to retrieve.
 * @return WP_Comment|false Comment if found. False on failure.
 */
function get_comment_to_edit( $id ) {
	$comment = get_comment( $id );
	if ( ! $comment ) {
		return false;
	}

	$comment->comment_ID      = (int) $comment->comment_ID;
	$comment->comment_post_ID = (int) $comment->comment_post_ID;

	$comment->comment_content = format_to_edit( $comment->comment_content );
	/**
	 * Filters the comment content before editing.
	 *
	 * @since 2.0.0
	 *
	 * @param string $comment->comment_content Comment content.
	 */
	$comment->comment_content = apply_filters( 'comment_edit_pre', $comment->comment_content );

	$comment->comment_author       = format_to_edit( $comment->comment_author );
	$comment->comment_author_email = format_to_edit( $comment->comment_author_email );
	$comment->comment_author_url   = format_to_edit( $comment->comment_author_url );
	$comment->comment_author_url   = esc_url( $comment->comment_author_url );

	return $comment;
}

/**
 * Get the number of pending comments on a post or posts
 *
 * @since 2.3.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param int|array $post_id Either a single Post ID or an array of Post IDs
 * @return int|array Either a single Posts pending comments as an int or an array of ints keyed on the Post IDs
 */
function get_pending_comments_num( $post_id ) {
	global $wpdb;

	$single = false;
	if ( ! is_array( $post_id ) ) {
		$post_id_array = (array) $post_id;
		$single        = true;
	} else {
		$post_id_array = $post_id;
	}
	$post_id_array = array_map( 'intval', $post_id_array );
	$post_id_in    = "'" . implode( "', '", $post_id_array ) . "'";

	$pending = $wpdb->get_results( "SELECT comment_post_ID, COUNT(comment_ID) as num_comments FROM $wpdb->comments WHERE comment_post_ID IN ( $post_id_in ) AND comment_approved = '0' GROUP BY comment_post_ID", ARRAY_A );

	if ( $single ) {
		if ( empty( $pending ) ) {
			return 0;
		} else {
			return absint( $pending[0]['num_comments'] );
		}
	}

	$pending_keyed = array();

	// Default to zero pending for all posts in request.
	foreach ( $post_id_array as $id ) {
		$pending_keyed[ $id ] = 0;
	}

	if ( ! empty( $pending ) ) {
		foreach ( $pending as $pend ) {
			$pending_keyed[ $pend['comment_post_ID'] ] = absint( $pend['num_comments'] );
		}
	}

	return $pending_keyed;
}

/**
 * Adds avatars to relevant places in admin.
 *
 * @since 2.5.0
 *
 * @param string $name User name.
 * @return string Avatar with the user name.
 */
function floated_admin_avatar( $name ) {
	$avatar = get_avatar( get_comment(), 32, 'mystery' );
	return "$avatar $name";
}

/**
 * @since 2.7.0
 */
function enqueue_comment_hotkeys_js() {
	if ( 'true' === get_user_option( 'comment_shortcuts' ) ) {
		wp_enqueue_script( 'jquery-table-hotkeys' );
	}
}

/**
 * Display error message at bottom of comments.
 *
 * @param string $msg Error Message. Assumed to contain HTML and be sanitized.
 */
function comment_footer_die( $msg ) {
	echo "<div class='wrap'><p>$msg</p></div>";
	require_once ABSPATH . 'wp-admin/admin-footer.php';
	die;
}
