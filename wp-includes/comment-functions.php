<?php
/**
 * Manages WordPress comments
 *
 * @package WordPress
 * @subpackage Comment
 */

/**
 * Check whether a comment passes internal checks to be allowed to add.
 *
 * If manual comment moderation is set in the administration, then all checks,
 * regardless of their type and whitelist, will fail and the function will
 * return false.
 *
 * If the number of links exceeds the amount in the administration, then the
 * check fails. If any of the parameter contents match the blacklist of words,
 * then the check fails.
 *
 * If the comment author was approved before, then the comment is automatically
 * whitelisted.
 *
 * If all checks pass, the function will return true.
 *
 * @since 1.2.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param string $author       Comment author name.
 * @param string $email        Comment author email.
 * @param string $url          Comment author URL.
 * @param string $comment      Content of the comment.
 * @param string $user_ip      Comment author IP address.
 * @param string $user_agent   Comment author User-Agent.
 * @param string $comment_type Comment type, either user-submitted comment,
 *		                       trackback, or pingback.
 * @return bool If all checks pass, true, otherwise false.
 */
function check_comment($author, $email, $url, $comment, $user_ip, $user_agent, $comment_type) {
	global $wpdb;

	// If manual moderation is enabled, skip all checks and return false.
	if ( 1 == get_option('comment_moderation') )
		return false;

	/** This filter is documented in wp-includes/comment-template.php */
	$comment = apply_filters( 'comment_text', $comment );

	// Check for the number of external links if a max allowed number is set.
	if ( $max_links = get_option( 'comment_max_links' ) ) {
		$num_links = preg_match_all( '/<a [^>]*href/i', $comment, $out );

		/**
		 * Filter the maximum number of links allowed in a comment.
		 *
		 * @since 3.0.0
		 *
		 * @param int    $num_links The number of links allowed.
		 * @param string $url       Comment author's URL. Included in allowed links total.
		 */
		$num_links = apply_filters( 'comment_max_links_url', $num_links, $url );

		/*
		 * If the number of links in the comment exceeds the allowed amount,
		 * fail the check by returning false.
		 */
		if ( $num_links >= $max_links )
			return false;
	}

	$mod_keys = trim(get_option('moderation_keys'));

	// If moderation 'keys' (keywords) are set, process them.
	if ( !empty($mod_keys) ) {
		$words = explode("\n", $mod_keys );

		foreach ( (array) $words as $word) {
			$word = trim($word);

			// Skip empty lines.
			if ( empty($word) )
				continue;

			/*
			 * Do some escaping magic so that '#' (number of) characters in the spam
			 * words don't break things:
			 */
			$word = preg_quote($word, '#');

			/*
			 * Check the comment fields for moderation keywords. If any are found,
			 * fail the check for the given field by returning false.
			 */
			$pattern = "#$word#i";
			if ( preg_match($pattern, $author) ) return false;
			if ( preg_match($pattern, $email) ) return false;
			if ( preg_match($pattern, $url) ) return false;
			if ( preg_match($pattern, $comment) ) return false;
			if ( preg_match($pattern, $user_ip) ) return false;
			if ( preg_match($pattern, $user_agent) ) return false;
		}
	}

	/*
	 * Check if the option to approve comments by previously-approved authors is enabled.
	 *
	 * If it is enabled, check whether the comment author has a previously-approved comment,
	 * as well as whether there are any moderation keywords (if set) present in the author
	 * email address. If both checks pass, return true. Otherwise, return false.
	 */
	if ( 1 == get_option('comment_whitelist')) {
		if ( 'trackback' != $comment_type && 'pingback' != $comment_type && $author != '' && $email != '' ) {
			// expected_slashed ($author, $email)
			$ok_to_comment = $wpdb->get_var("SELECT comment_approved FROM $wpdb->comments WHERE comment_author = '$author' AND comment_author_email = '$email' and comment_approved = '1' LIMIT 1");
			if ( ( 1 == $ok_to_comment ) &&
				( empty($mod_keys) || false === strpos( $email, $mod_keys) ) )
					return true;
			else
				return false;
		} else {
			return false;
		}
	}
	return true;
}

/**
 * Retrieve the approved comments for post $post_id.
 *
 * @since 2.0.0
 * @since 4.1.0 Refactored to leverage {@see WP_Comment_Query} over a direct query.
 *
 * @param  int   $post_id The ID of the post.
 * @param  array $args    Optional. See {@see WP_Comment_Query::query()} for information
 *                        on accepted arguments.
 * @return int|array $comments The approved comments, or number of comments if `$count`
 *                             argument is true.
 */
function get_approved_comments( $post_id, $args = array() ) {
	if ( ! $post_id ) {
		return array();
	}

	$defaults = array(
		'status'  => 1,
		'post_id' => $post_id,
		'order'   => 'ASC',
	);
	$r = wp_parse_args( $args, $defaults );

	$query = new WP_Comment_Query;
	return $query->query( $r );
}

/**
 * Retrieves comment data given a comment ID or comment object.
 *
 * If an object is passed then the comment data will be cached and then returned
 * after being passed through a filter. If the comment is empty, then the global
 * comment variable will be used, if it is set.
 *
 * @since 2.0.0
 *
 * @global wpdb   $wpdb WordPress database abstraction object.
 * @global object $comment
 *
 * @param WP_Comment|string|int $comment Comment to retrieve.
 * @param string $output Optional. OBJECT or ARRAY_A or ARRAY_N constants.
 * @return WP_Comment|array|null Depends on $output value.
 */
function get_comment(&$comment, $output = OBJECT) {
	if ( empty( $comment ) && isset( $GLOBALS['comment'] ) ) {
		$comment = $GLOBALS['comment'];
	}

	if ( $comment instanceof WP_Comment ) {
		$_comment = $comment;
	} elseif ( is_object( $comment ) ) {
		$_comment = new WP_Comment( $comment );
	} else {
		$_comment = WP_Comment::get_instance( $comment );
	}

	if ( ! $_comment ) {
		return null;
	}

	/**
	 * Fires after a comment is retrieved.
	 *
	 * @since 2.3.0
	 *
	 * @param mixed $_comment Comment data.
	 */
	$_comment = apply_filters( 'get_comment', $_comment );

	if ( $output == OBJECT ) {
		return $_comment;
	} elseif ( $output == ARRAY_A ) {
		return $_comment->to_array();
	} elseif ( $output == ARRAY_N ) {
		return array_values( $_comment->to_array() );
	}
	return $_comment;
}

/**
 * Retrieve a list of comments.
 *
 * The comment list can be for the blog as a whole or for an individual post.
 *
 * @since 2.7.0
 *
 * @param string|array $args Optional. Array or string of arguments. See {@see WP_Comment_Query::parse_query()}
 *                           for information on accepted arguments. Default empty.
 * @return int|array List of comments or number of found comments if `$count` argument is true.
 */
function get_comments( $args = '' ) {
	$query = new WP_Comment_Query;
	return $query->query( $args );
}

/**
 * Retrieve all of the WordPress supported comment statuses.
 *
 * Comments have a limited set of valid status values, this provides the comment
 * status values and descriptions.
 *
 * @since 2.7.0
 *
 * @return array List of comment statuses.
 */
function get_comment_statuses() {
	$status = array(
		'hold'		=> __('Unapproved'),
		/* translators: comment status  */
		'approve'	=> _x('Approved', 'adjective'),
		/* translators: comment status */
		'spam'		=> _x('Spam', 'adjective'),
	);

	return $status;
}

/**
 * Gets the default comment status for a post type.
 *
 * @since 4.3.0
 *
 * @param string $post_type    Optional. Post type. Default 'post'.
 * @param string $comment_type Optional. Comment type. Default 'comment'.
 * @return string Expected return value is 'open' or 'closed'.
 */
function get_default_comment_status( $post_type = 'post', $comment_type = 'comment' ) {
	switch ( $comment_type ) {
		case 'pingback' :
		case 'trackback' :
			$supports = 'trackbacks';
			$option = 'ping';
			break;
		default :
			$supports = 'comments';
			$option = 'comment';
	}

	// Set the status.
	if ( 'page' === $post_type ) {
		$status = 'closed';
	} elseif ( post_type_supports( $post_type, $supports ) ) {
		$status = get_option( "default_{$option}_status" );
	} else {
		$status = 'closed';
	}

	/**
	 * Filter the default comment status for the given post type.
	 *
	 * @since 4.3.0
	 *
	 * @param string $status       Default status for the given post type,
	 *                             either 'open' or 'closed'.
	 * @param string $post_type    Post type. Default is `post`.
	 * @param string $comment_type Type of comment. Default is `comment`.
	 */
	return apply_filters( 'get_default_comment_status' , $status, $post_type, $comment_type );
}

/**
 * The date the last comment was modified.
 *
 * @since 1.5.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 * @staticvar array $cache_lastcommentmodified
 *
 * @param string $timezone Which timezone to use in reference to 'gmt', 'blog',
 *		or 'server' locations.
 * @return string Last comment modified date.
 */
function get_lastcommentmodified($timezone = 'server') {
	global $wpdb;
	static $cache_lastcommentmodified = array();

	if ( isset($cache_lastcommentmodified[$timezone]) )
		return $cache_lastcommentmodified[$timezone];

	$add_seconds_server = date('Z');

	switch ( strtolower($timezone)) {
		case 'gmt':
			$lastcommentmodified = $wpdb->get_var("SELECT comment_date_gmt FROM $wpdb->comments WHERE comment_approved = '1' ORDER BY comment_date_gmt DESC LIMIT 1");
			break;
		case 'blog':
			$lastcommentmodified = $wpdb->get_var("SELECT comment_date FROM $wpdb->comments WHERE comment_approved = '1' ORDER BY comment_date_gmt DESC LIMIT 1");
			break;
		case 'server':
			$lastcommentmodified = $wpdb->get_var($wpdb->prepare("SELECT DATE_ADD(comment_date_gmt, INTERVAL %s SECOND) FROM $wpdb->comments WHERE comment_approved = '1' ORDER BY comment_date_gmt DESC LIMIT 1", $add_seconds_server));
			break;
	}

	$cache_lastcommentmodified[$timezone] = $lastcommentmodified;

	return $lastcommentmodified;
}

/**
 * The amount of comments in a post or total comments.
 *
 * A lot like {@link wp_count_comments()}, in that they both return comment
 * stats (albeit with different types). The {@link wp_count_comments()} actual
 * caches, but this function does not.
 *
 * @since 2.0.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param int $post_id Optional. Comment amount in post if > 0, else total comments blog wide.
 * @return array The amount of spam, approved, awaiting moderation, and total comments.
 */
function get_comment_count( $post_id = 0 ) {
	global $wpdb;

	$post_id = (int) $post_id;

	$where = '';
	if ( $post_id > 0 ) {
		$where = $wpdb->prepare("WHERE comment_post_ID = %d", $post_id);
	}

	$totals = (array) $wpdb->get_results("
		SELECT comment_approved, COUNT( * ) AS total
		FROM {$wpdb->comments}
		{$where}
		GROUP BY comment_approved
	", ARRAY_A);

	$comment_count = array(
		'approved'            => 0,
		'awaiting_moderation' => 0,
		'spam'                => 0,
		'trash'               => 0,
		'post-trashed'        => 0,
		'total_comments'      => 0,
	);

	foreach ( $totals as $row ) {
		switch ( $row['comment_approved'] ) {
			case 'trash':
				$comment_count['trash'] = $row['total'];
				break;
			case 'post-trashed':
				$comment_count['post-trashed'] = $row['total'];
				break;
			case 'spam':
				$comment_count['spam'] = $row['total'];
				$comment_count['total_comments'] += $row['total'];
				break;
			case '1':
				$comment_count['approved'] = $row['total'];
				$comment_count['total_comments'] += $row['total'];
				break;
			case '0':
				$comment_count['awaiting_moderation'] = $row['total'];
				$comment_count['total_comments'] += $row['total'];
				break;
			default:
				break;
		}
	}

	return $comment_count;
}

//
// Comment meta functions
//

/**
 * Add meta data field to a comment.
 *
 * @since 2.9.0
 * @link https://codex.wordpress.org/Function_Reference/add_comment_meta
 *
 * @param int $comment_id Comment ID.
 * @param string $meta_key Metadata name.
 * @param mixed $meta_value Metadata value.
 * @param bool $unique Optional, default is false. Whether the same key should not be added.
 * @return int|bool Meta ID on success, false on failure.
 */
function add_comment_meta($comment_id, $meta_key, $meta_value, $unique = false) {
	return add_metadata('comment', $comment_id, $meta_key, $meta_value, $unique);
}

/**
 * Remove metadata matching criteria from a comment.
 *
 * You can match based on the key, or key and value. Removing based on key and
 * value, will keep from removing duplicate metadata with the same key. It also
 * allows removing all metadata matching key, if needed.
 *
 * @since 2.9.0
 * @link https://codex.wordpress.org/Function_Reference/delete_comment_meta
 *
 * @param int $comment_id comment ID
 * @param string $meta_key Metadata name.
 * @param mixed $meta_value Optional. Metadata value.
 * @return bool True on success, false on failure.
 */
function delete_comment_meta($comment_id, $meta_key, $meta_value = '') {
	return delete_metadata('comment', $comment_id, $meta_key, $meta_value);
}

/**
 * Retrieve comment meta field for a comment.
 *
 * @since 2.9.0
 * @link https://codex.wordpress.org/Function_Reference/get_comment_meta
 *
 * @param int $comment_id Comment ID.
 * @param string $key Optional. The meta key to retrieve. By default, returns data for all keys.
 * @param bool $single Whether to return a single value.
 * @return mixed Will be an array if $single is false. Will be value of meta data field if $single
 *  is true.
 */
function get_comment_meta($comment_id, $key = '', $single = false) {
	return get_metadata('comment', $comment_id, $key, $single);
}

/**
 * Update comment meta field based on comment ID.
 *
 * Use the $prev_value parameter to differentiate between meta fields with the
 * same key and comment ID.
 *
 * If the meta field for the comment does not exist, it will be added.
 *
 * @since 2.9.0
 * @link https://codex.wordpress.org/Function_Reference/update_comment_meta
 *
 * @param int $comment_id Comment ID.
 * @param string $meta_key Metadata key.
 * @param mixed $meta_value Metadata value.
 * @param mixed $prev_value Optional. Previous value to check before removing.
 * @return int|bool Meta ID if the key didn't exist, true on successful update, false on failure.
 */
function update_comment_meta($comment_id, $meta_key, $meta_value, $prev_value = '') {
	return update_metadata('comment', $comment_id, $meta_key, $meta_value, $prev_value);
}

/**
 * Sets the cookies used to store an unauthenticated commentator's identity. Typically used
 * to recall previous comments by this commentator that are still held in moderation.
 *
 * @param WP_Comment $comment Comment object.
 * @param object     $user    Comment author's object.
 *
 * @since 3.4.0
 */
function wp_set_comment_cookies($comment, $user) {
	if ( $user->exists() )
		return;

	/**
	 * Filter the lifetime of the comment cookie in seconds.
	 *
	 * @since 2.8.0
	 *
	 * @param int $seconds Comment cookie lifetime. Default 30000000.
	 */
	$comment_cookie_lifetime = apply_filters( 'comment_cookie_lifetime', 30000000 );
	$secure = ( 'https' === parse_url( home_url(), PHP_URL_SCHEME ) );
	setcookie( 'comment_author_' . COOKIEHASH, $comment->comment_author, time() + $comment_cookie_lifetime, COOKIEPATH, COOKIE_DOMAIN, $secure );
	setcookie( 'comment_author_email_' . COOKIEHASH, $comment->comment_author_email, time() + $comment_cookie_lifetime, COOKIEPATH, COOKIE_DOMAIN, $secure );
	setcookie( 'comment_author_url_' . COOKIEHASH, esc_url($comment->comment_author_url), time() + $comment_cookie_lifetime, COOKIEPATH, COOKIE_DOMAIN, $secure );
}

/**
 * Sanitizes the cookies sent to the user already.
 *
 * Will only do anything if the cookies have already been created for the user.
 * Mostly used after cookies had been sent to use elsewhere.
 *
 * @since 2.0.4
 */
function sanitize_comment_cookies() {
	if ( isset( $_COOKIE['comment_author_' . COOKIEHASH] ) ) {
		/**
		 * Filter the comment author's name cookie before it is set.
		 *
		 * When this filter hook is evaluated in wp_filter_comment(),
		 * the comment author's name string is passed.
		 *
		 * @since 1.5.0
		 *
		 * @param string $author_cookie The comment author name cookie.
		 */
		$comment_author = apply_filters( 'pre_comment_author_name', $_COOKIE['comment_author_' . COOKIEHASH] );
		$comment_author = wp_unslash($comment_author);
		$comment_author = esc_attr($comment_author);
		$_COOKIE['comment_author_' . COOKIEHASH] = $comment_author;
	}

	if ( isset( $_COOKIE['comment_author_email_' . COOKIEHASH] ) ) {
		/**
		 * Filter the comment author's email cookie before it is set.
		 *
		 * When this filter hook is evaluated in wp_filter_comment(),
		 * the comment author's email string is passed.
		 *
		 * @since 1.5.0
		 *
		 * @param string $author_email_cookie The comment author email cookie.
		 */
		$comment_author_email = apply_filters( 'pre_comment_author_email', $_COOKIE['comment_author_email_' . COOKIEHASH] );
		$comment_author_email = wp_unslash($comment_author_email);
		$comment_author_email = esc_attr($comment_author_email);
		$_COOKIE['comment_author_email_'.COOKIEHASH] = $comment_author_email;
	}

	if ( isset( $_COOKIE['comment_author_url_' . COOKIEHASH] ) ) {
		/**
		 * Filter the comment author's URL cookie before it is set.
		 *
		 * When this filter hook is evaluated in wp_filter_comment(),
		 * the comment author's URL string is passed.
		 *
		 * @since 1.5.0
		 *
		 * @param string $author_url_cookie The comment author URL cookie.
		 */
		$comment_author_url = apply_filters( 'pre_comment_author_url', $_COOKIE['comment_author_url_' . COOKIEHASH] );
		$comment_author_url = wp_unslash($comment_author_url);
		$_COOKIE['comment_author_url_'.COOKIEHASH] = $comment_author_url;
	}
}

/**
 * Validates whether this comment is allowed to be made.
 *
 * @since 2.0.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param array $commentdata Contains information on the comment
 * @return int|string Signifies the approval status (0|1|'spam')
 */
function wp_allow_comment( $commentdata ) {
	global $wpdb;

	// Simple duplicate check
	// expected_slashed ($comment_post_ID, $comment_author, $comment_author_email, $comment_content)
	$dupe = $wpdb->prepare(
		"SELECT comment_ID FROM $wpdb->comments WHERE comment_post_ID = %d AND comment_parent = %s AND comment_approved != 'trash' AND ( comment_author = %s ",
		wp_unslash( $commentdata['comment_post_ID'] ),
		wp_unslash( $commentdata['comment_parent'] ),
		wp_unslash( $commentdata['comment_author'] )
	);
	if ( $commentdata['comment_author_email'] ) {
		$dupe .= $wpdb->prepare(
			"OR comment_author_email = %s ",
			wp_unslash( $commentdata['comment_author_email'] )
		);
	}
	$dupe .= $wpdb->prepare(
		") AND comment_content = %s LIMIT 1",
		wp_unslash( $commentdata['comment_content'] )
	);
	if ( $wpdb->get_var( $dupe ) ) {
		/**
		 * Fires immediately after a duplicate comment is detected.
		 *
		 * @since 3.0.0
		 *
		 * @param array $commentdata Comment data.
		 */
		do_action( 'comment_duplicate_trigger', $commentdata );
		if ( defined( 'DOING_AJAX' ) ) {
			die( __('Duplicate comment detected; it looks as though you&#8217;ve already said that!') );
		}
		wp_die( __( 'Duplicate comment detected; it looks as though you&#8217;ve already said that!' ), 409 );
	}

	/**
	 * Fires immediately before a comment is marked approved.
	 *
	 * Allows checking for comment flooding.
	 *
	 * @since 2.3.0
	 *
	 * @param string $comment_author_IP    Comment author's IP address.
	 * @param string $comment_author_email Comment author's email.
	 * @param string $comment_date_gmt     GMT date the comment was posted.
	 */
	do_action(
		'check_comment_flood',
		$commentdata['comment_author_IP'],
		$commentdata['comment_author_email'],
		$commentdata['comment_date_gmt']
	);

	if ( ! empty( $commentdata['user_id'] ) ) {
		$user = get_userdata( $commentdata['user_id'] );
		$post_author = $wpdb->get_var( $wpdb->prepare(
			"SELECT post_author FROM $wpdb->posts WHERE ID = %d LIMIT 1",
			$commentdata['comment_post_ID']
		) );
	}

	if ( isset( $user ) && ( $commentdata['user_id'] == $post_author || $user->has_cap( 'moderate_comments' ) ) ) {
		// The author and the admins get respect.
		$approved = 1;
	} else {
		// Everyone else's comments will be checked.
		if ( check_comment(
			$commentdata['comment_author'],
			$commentdata['comment_author_email'],
			$commentdata['comment_author_url'],
			$commentdata['comment_content'],
			$commentdata['comment_author_IP'],
			$commentdata['comment_agent'],
			$commentdata['comment_type']
		) ) {
			$approved = 1;
		} else {
			$approved = 0;
		}

		if ( wp_blacklist_check(
			$commentdata['comment_author'],
			$commentdata['comment_author_email'],
			$commentdata['comment_author_url'],
			$commentdata['comment_content'],
			$commentdata['comment_author_IP'],
			$commentdata['comment_agent']
		) ) {
			$approved = 'spam';
		}
	}

	/**
	 * Filter a comment's approval status before it is set.
	 *
	 * @since 2.1.0
	 *
	 * @param bool|string $approved    The approval status. Accepts 1, 0, or 'spam'.
	 * @param array       $commentdata Comment data.
	 */
	$approved = apply_filters( 'pre_comment_approved', $approved, $commentdata );
	return $approved;
}

/**
 * Check whether comment flooding is occurring.
 *
 * Won't run, if current user can manage options, so to not block
 * administrators.
 *
 * @since 2.3.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param string $ip Comment IP.
 * @param string $email Comment author email address.
 * @param string $date MySQL time string.
 */
function check_comment_flood_db( $ip, $email, $date ) {
	global $wpdb;
	if ( current_user_can( 'manage_options' ) )
		return; // don't throttle admins
	$hour_ago = gmdate( 'Y-m-d H:i:s', time() - HOUR_IN_SECONDS );
	if ( $lasttime = $wpdb->get_var( $wpdb->prepare( "SELECT `comment_date_gmt` FROM `$wpdb->comments` WHERE `comment_date_gmt` >= %s AND ( `comment_author_IP` = %s OR `comment_author_email` = %s ) ORDER BY `comment_date_gmt` DESC LIMIT 1", $hour_ago, $ip, $email ) ) ) {
		$time_lastcomment = mysql2date('U', $lasttime, false);
		$time_newcomment  = mysql2date('U', $date, false);
		/**
		 * Filter the comment flood status.
		 *
		 * @since 2.1.0
		 *
		 * @param bool $bool             Whether a comment flood is occurring. Default false.
		 * @param int  $time_lastcomment Timestamp of when the last comment was posted.
		 * @param int  $time_newcomment  Timestamp of when the new comment was posted.
		 */
		$flood_die = apply_filters( 'comment_flood_filter', false, $time_lastcomment, $time_newcomment );
		if ( $flood_die ) {
			/**
			 * Fires before the comment flood message is triggered.
			 *
			 * @since 1.5.0
			 *
			 * @param int $time_lastcomment Timestamp of when the last comment was posted.
			 * @param int $time_newcomment  Timestamp of when the new comment was posted.
			 */
			do_action( 'comment_flood_trigger', $time_lastcomment, $time_newcomment );

			if ( defined('DOING_AJAX') )
				die( __('You are posting comments too quickly. Slow down.') );

			wp_die( __( 'You are posting comments too quickly. Slow down.' ), 429 );
		}
	}
}

/**
 * Separates an array of comments into an array keyed by comment_type.
 *
 * @since 2.7.0
 *
 * @param array $comments Array of comments
 * @return array Array of comments keyed by comment_type.
 */
function separate_comments(&$comments) {
	$comments_by_type = array('comment' => array(), 'trackback' => array(), 'pingback' => array(), 'pings' => array());
	$count = count($comments);
	for ( $i = 0; $i < $count; $i++ ) {
		$type = $comments[$i]->comment_type;
		if ( empty($type) )
			$type = 'comment';
		$comments_by_type[$type][] = &$comments[$i];
		if ( 'trackback' == $type || 'pingback' == $type )
			$comments_by_type['pings'][] = &$comments[$i];
	}

	return $comments_by_type;
}

/**
 * Calculate the total number of comment pages.
 *
 * @since 2.7.0
 *
 * @uses Walker_Comment
 *
 * @global WP_Query $wp_query
 *
 * @param array $comments Optional array of WP_Comment objects. Defaults to $wp_query->comments
 * @param int   $per_page Optional comments per page.
 * @param bool  $threaded Optional control over flat or threaded comments.
 * @return int Number of comment pages.
 */
function get_comment_pages_count( $comments = null, $per_page = null, $threaded = null ) {
	global $wp_query;

	if ( null === $comments && null === $per_page && null === $threaded && !empty($wp_query->max_num_comment_pages) )
		return $wp_query->max_num_comment_pages;

	if ( ( ! $comments || ! is_array( $comments ) ) && ! empty( $wp_query->comments )  )
		$comments = $wp_query->comments;

	if ( empty($comments) )
		return 0;

	if ( ! get_option( 'page_comments' ) )
		return 1;

	if ( !isset($per_page) )
		$per_page = (int) get_query_var('comments_per_page');
	if ( 0 === $per_page )
		$per_page = (int) get_option('comments_per_page');
	if ( 0 === $per_page )
		return 1;

	if ( !isset($threaded) )
		$threaded = get_option('thread_comments');

	if ( $threaded ) {
		$walker = new Walker_Comment;
		$count = ceil( $walker->get_number_of_root_elements( $comments ) / $per_page );
	} else {
		$count = ceil( count( $comments ) / $per_page );
	}

	return $count;
}

/**
 * Calculate what page number a comment will appear on for comment paging.
 *
 * @since 2.7.0
 *
 * @global wpdb $wpdb
 *
 * @param int $comment_ID Comment ID.
 * @param array $args Optional args.
 * @return int|null Comment page number or null on error.
 */
function get_page_of_comment( $comment_ID, $args = array() ) {
	global $wpdb;

	if ( !$comment = get_comment( $comment_ID ) )
		return;

	$defaults = array( 'type' => 'all', 'page' => '', 'per_page' => '', 'max_depth' => '' );
	$args = wp_parse_args( $args, $defaults );

	if ( '' === $args['per_page'] && get_option('page_comments') )
		$args['per_page'] = get_query_var('comments_per_page');
	if ( empty($args['per_page']) ) {
		$args['per_page'] = 0;
		$args['page'] = 0;
	}
	if ( $args['per_page'] < 1 )
		return 1;

	if ( '' === $args['max_depth'] ) {
		if ( get_option('thread_comments') )
			$args['max_depth'] = get_option('thread_comments_depth');
		else
			$args['max_depth'] = -1;
	}

	// Find this comment's top level parent if threading is enabled
	if ( $args['max_depth'] > 1 && 0 != $comment->comment_parent )
		return get_page_of_comment( $comment->comment_parent, $args );

	$allowedtypes = array(
		'comment' => '',
		'pingback' => 'pingback',
		'trackback' => 'trackback',
	);

	$comtypewhere = ( 'all' != $args['type'] && isset($allowedtypes[$args['type']]) ) ? " AND comment_type = '" . $allowedtypes[$args['type']] . "'" : '';

	// Count comments older than this one
	$oldercoms = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(comment_ID) FROM $wpdb->comments WHERE comment_post_ID = %d AND comment_parent = 0 AND comment_approved = '1' AND comment_date_gmt < '%s'" . $comtypewhere, $comment->comment_post_ID, $comment->comment_date_gmt ) );

	// No older comments? Then it's page #1.
	if ( 0 == $oldercoms )
		return 1;

	// Divide comments older than this one by comments per page to get this comment's page number
	return ceil( ( $oldercoms + 1 ) / $args['per_page'] );
}

/**
 * Does comment contain blacklisted characters or words.
 *
 * @since 1.5.0
 *
 * @param string $author The author of the comment
 * @param string $email The email of the comment
 * @param string $url The url used in the comment
 * @param string $comment The comment content
 * @param string $user_ip The comment author IP address
 * @param string $user_agent The author's browser user agent
 * @return bool True if comment contains blacklisted content, false if comment does not
 */
function wp_blacklist_check($author, $email, $url, $comment, $user_ip, $user_agent) {
	/**
	 * Fires before the comment is tested for blacklisted characters or words.
	 *
	 * @since 1.5.0
	 *
	 * @param string $author     Comment author.
	 * @param string $email      Comment author's email.
	 * @param string $url        Comment author's URL.
	 * @param string $comment    Comment content.
	 * @param string $user_ip    Comment author's IP address.
	 * @param string $user_agent Comment author's browser user agent.
	 */
	do_action( 'wp_blacklist_check', $author, $email, $url, $comment, $user_ip, $user_agent );

	$mod_keys = trim( get_option('blacklist_keys') );
	if ( '' == $mod_keys )
		return false; // If moderation keys are empty
	$words = explode("\n", $mod_keys );

	foreach ( (array) $words as $word ) {
		$word = trim($word);

		// Skip empty lines
		if ( empty($word) ) { continue; }

		// Do some escaping magic so that '#' chars in the
		// spam words don't break things:
		$word = preg_quote($word, '#');

		$pattern = "#$word#i";
		if (
			   preg_match($pattern, $author)
			|| preg_match($pattern, $email)
			|| preg_match($pattern, $url)
			|| preg_match($pattern, $comment)
			|| preg_match($pattern, $user_ip)
			|| preg_match($pattern, $user_agent)
		 )
			return true;
	}
	return false;
}

/**
 * Retrieve total comments for blog or single post.
 *
 * The properties of the returned object contain the 'moderated', 'approved',
 * and spam comments for either the entire blog or single post. Those properties
 * contain the amount of comments that match the status. The 'total_comments'
 * property contains the integer of total comments.
 *
 * The comment stats are cached and then retrieved, if they already exist in the
 * cache.
 *
 * @since 2.5.0
 *
 * @param int $post_id Optional. Post ID.
 * @return object|array Comment stats.
 */
function wp_count_comments( $post_id = 0 ) {
	$post_id = (int) $post_id;

	/**
	 * Filter the comments count for a given post.
	 *
	 * @since 2.7.0
	 *
	 * @param array $count   An empty array.
	 * @param int   $post_id The post ID.
	 */
	$filtered = apply_filters( 'wp_count_comments', array(), $post_id );
	if ( ! empty( $filtered ) ) {
		return $filtered;
	}

	$count = wp_cache_get( "comments-{$post_id}", 'counts' );
	if ( false !== $count ) {
		return $count;
	}

	$stats = get_comment_count( $post_id );
	$stats['moderated'] = $stats['awaiting_moderation'];
	unset( $stats['awaiting_moderation'] );

	$stats_object = (object) $stats;
	wp_cache_set( "comments-{$post_id}", $stats_object, 'counts' );

	return $stats_object;
}

/**
 * Trashes or deletes a comment.
 *
 * The comment is moved to trash instead of permanently deleted unless trash is
 * disabled, item is already in the trash, or $force_delete is true.
 *
 * The post comment count will be updated if the comment was approved and has a
 * post ID available.
 *
 * @since 2.0.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param int $comment_id Comment ID
 * @param bool $force_delete Whether to bypass trash and force deletion. Default is false.
 * @return bool True on success, false on failure.
 */
function wp_delete_comment($comment_id, $force_delete = false) {
	global $wpdb;
	if (!$comment = get_comment($comment_id))
		return false;

	if ( !$force_delete && EMPTY_TRASH_DAYS && !in_array( wp_get_comment_status($comment_id), array( 'trash', 'spam' ) ) )
		return wp_trash_comment($comment_id);

	/**
	 * Fires immediately before a comment is deleted from the database.
	 *
	 * @since 1.2.0
	 *
	 * @param int $comment_id The comment ID.
	 */
	do_action( 'delete_comment', $comment_id );

	// Move children up a level.
	$children = $wpdb->get_col( $wpdb->prepare("SELECT comment_ID FROM $wpdb->comments WHERE comment_parent = %d", $comment_id) );
	if ( !empty($children) ) {
		$wpdb->update($wpdb->comments, array('comment_parent' => $comment->comment_parent), array('comment_parent' => $comment_id));
		clean_comment_cache($children);
	}

	// Delete metadata
	$meta_ids = $wpdb->get_col( $wpdb->prepare( "SELECT meta_id FROM $wpdb->commentmeta WHERE comment_id = %d", $comment_id ) );
	foreach ( $meta_ids as $mid )
		delete_metadata_by_mid( 'comment', $mid );

	if ( ! $wpdb->delete( $wpdb->comments, array( 'comment_ID' => $comment_id ) ) )
		return false;

	/**
	 * Fires immediately after a comment is deleted from the database.
	 *
	 * @since 2.9.0
	 *
	 * @param int $comment_id The comment ID.
	 */
	do_action( 'deleted_comment', $comment_id );

	$post_id = $comment->comment_post_ID;
	if ( $post_id && $comment->comment_approved == 1 )
		wp_update_comment_count($post_id);

	clean_comment_cache($comment_id);

	/** This action is documented in wp-includes/comment.php */
	do_action( 'wp_set_comment_status', $comment_id, 'delete' );

	wp_transition_comment_status('delete', $comment->comment_approved, $comment);
	return true;
}

/**
 * Moves a comment to the Trash
 *
 * If trash is disabled, comment is permanently deleted.
 *
 * @since 2.9.0
 *
 * @param int $comment_id Comment ID.
 * @return bool True on success, false on failure.
 */
function wp_trash_comment($comment_id) {
	if ( !EMPTY_TRASH_DAYS )
		return wp_delete_comment($comment_id, true);

	if ( !$comment = get_comment($comment_id) )
		return false;

	/**
	 * Fires immediately before a comment is sent to the Trash.
	 *
	 * @since 2.9.0
	 *
	 * @param int $comment_id The comment ID.
	 */
	do_action( 'trash_comment', $comment_id );

	if ( wp_set_comment_status($comment_id, 'trash') ) {
		delete_comment_meta( $comment_id, '_wp_trash_meta_status' );
		delete_comment_meta( $comment_id, '_wp_trash_meta_time' );
		add_comment_meta( $comment_id, '_wp_trash_meta_status', $comment->comment_approved );
		add_comment_meta( $comment_id, '_wp_trash_meta_time', time() );

		/**
		 * Fires immediately after a comment is sent to Trash.
		 *
		 * @since 2.9.0
		 *
		 * @param int $comment_id The comment ID.
		 */
		do_action( 'trashed_comment', $comment_id );
		return true;
	}

	return false;
}

/**
 * Removes a comment from the Trash
 *
 * @since 2.9.0
 *
 * @param int $comment_id Comment ID.
 * @return bool True on success, false on failure.
 */
function wp_untrash_comment($comment_id) {
	if ( ! (int)$comment_id )
		return false;

	/**
	 * Fires immediately before a comment is restored from the Trash.
	 *
	 * @since 2.9.0
	 *
	 * @param int $comment_id The comment ID.
	 */
	do_action( 'untrash_comment', $comment_id );

	$status = (string) get_comment_meta($comment_id, '_wp_trash_meta_status', true);
	if ( empty($status) )
		$status = '0';

	if ( wp_set_comment_status($comment_id, $status) ) {
		delete_comment_meta($comment_id, '_wp_trash_meta_time');
		delete_comment_meta($comment_id, '_wp_trash_meta_status');
		/**
		 * Fires immediately after a comment is restored from the Trash.
		 *
		 * @since 2.9.0
		 *
		 * @param int $comment_id The comment ID.
		 */
		do_action( 'untrashed_comment', $comment_id );
		return true;
	}

	return false;
}

/**
 * Marks a comment as Spam
 *
 * @since 2.9.0
 *
 * @param int $comment_id Comment ID.
 * @return bool True on success, false on failure.
 */
function wp_spam_comment($comment_id) {
	if ( !$comment = get_comment($comment_id) )
		return false;

	/**
	 * Fires immediately before a comment is marked as Spam.
	 *
	 * @since 2.9.0
	 *
	 * @param int $comment_id The comment ID.
	 */
	do_action( 'spam_comment', $comment_id );

	if ( wp_set_comment_status($comment_id, 'spam') ) {
		delete_comment_meta( $comment_id, '_wp_trash_meta_status' );
		delete_comment_meta( $comment_id, '_wp_trash_meta_time' );
		add_comment_meta( $comment_id, '_wp_trash_meta_status', $comment->comment_approved );
		add_comment_meta( $comment_id, '_wp_trash_meta_time', time() );
		/**
		 * Fires immediately after a comment is marked as Spam.
		 *
		 * @since 2.9.0
		 *
		 * @param int $comment_id The comment ID.
		 */
		do_action( 'spammed_comment', $comment_id );
		return true;
	}

	return false;
}

/**
 * Removes a comment from the Spam
 *
 * @since 2.9.0
 *
 * @param int $comment_id Comment ID.
 * @return bool True on success, false on failure.
 */
function wp_unspam_comment($comment_id) {
	if ( ! (int)$comment_id )
		return false;

	/**
	 * Fires immediately before a comment is unmarked as Spam.
	 *
	 * @since 2.9.0
	 *
	 * @param int $comment_id The comment ID.
	 */
	do_action( 'unspam_comment', $comment_id );

	$status = (string) get_comment_meta($comment_id, '_wp_trash_meta_status', true);
	if ( empty($status) )
		$status = '0';

	if ( wp_set_comment_status($comment_id, $status) ) {
		delete_comment_meta( $comment_id, '_wp_trash_meta_status' );
		delete_comment_meta( $comment_id, '_wp_trash_meta_time' );
		/**
		 * Fires immediately after a comment is unmarked as Spam.
		 *
		 * @since 2.9.0
		 *
		 * @param int $comment_id The comment ID.
		 */
		do_action( 'unspammed_comment', $comment_id );
		return true;
	}

	return false;
}

/**
 * The status of a comment by ID.
 *
 * @since 1.0.0
 *
 * @param int $comment_id Comment ID
 * @return false|string Status might be 'trash', 'approved', 'unapproved', 'spam'. False on failure.
 */
function wp_get_comment_status($comment_id) {
	$comment = get_comment($comment_id);
	if ( !$comment )
		return false;

	$approved = $comment->comment_approved;

	if ( $approved == null )
		return false;
	elseif ( $approved == '1' )
		return 'approved';
	elseif ( $approved == '0' )
		return 'unapproved';
	elseif ( $approved == 'spam' )
		return 'spam';
	elseif ( $approved == 'trash' )
		return 'trash';
	else
		return false;
}

/**
 * Call hooks for when a comment status transition occurs.
 *
 * Calls hooks for comment status transitions. If the new comment status is not the same
 * as the previous comment status, then two hooks will be ran, the first is
 * 'transition_comment_status' with new status, old status, and comment data. The
 * next action called is 'comment_OLDSTATUS_to_NEWSTATUS' the NEWSTATUS is the
 * $new_status parameter and the OLDSTATUS is $old_status parameter; it has the
 * comment data.
 *
 * The final action will run whether or not the comment statuses are the same. The
 * action is named 'comment_NEWSTATUS_COMMENTTYPE', NEWSTATUS is from the $new_status
 * parameter and COMMENTTYPE is comment_type comment data.
 *
 * @since 2.7.0
 *
 * @param string $new_status New comment status.
 * @param string $old_status Previous comment status.
 * @param object $comment Comment data.
 */
function wp_transition_comment_status($new_status, $old_status, $comment) {
	/*
	 * Translate raw statuses to human readable formats for the hooks.
	 * This is not a complete list of comment status, it's only the ones
	 * that need to be renamed
	 */
	$comment_statuses = array(
		0         => 'unapproved',
		'hold'    => 'unapproved', // wp_set_comment_status() uses "hold"
		1         => 'approved',
		'approve' => 'approved', // wp_set_comment_status() uses "approve"
	);
	if ( isset($comment_statuses[$new_status]) ) $new_status = $comment_statuses[$new_status];
	if ( isset($comment_statuses[$old_status]) ) $old_status = $comment_statuses[$old_status];

	// Call the hooks
	if ( $new_status != $old_status ) {
		/**
		 * Fires when the comment status is in transition.
		 *
		 * @since 2.7.0
		 *
		 * @param int|string $new_status The new comment status.
		 * @param int|string $old_status The old comment status.
		 * @param object     $comment    The comment data.
		 */
		do_action( 'transition_comment_status', $new_status, $old_status, $comment );
		/**
		 * Fires when the comment status is in transition from one specific status to another.
		 *
		 * The dynamic portions of the hook name, `$old_status`, and `$new_status`,
		 * refer to the old and new comment statuses, respectively.
		 *
		 * @since 2.7.0
		 *
		 * @param WP_Comment $comment Comment object.
		 */
		do_action( "comment_{$old_status}_to_{$new_status}", $comment );
	}
	/**
	 * Fires when the status of a specific comment type is in transition.
	 *
	 * The dynamic portions of the hook name, `$new_status`, and `$comment->comment_type`,
	 * refer to the new comment status, and the type of comment, respectively.
	 *
	 * Typical comment types include an empty string (standard comment), 'pingback',
	 * or 'trackback'.
	 *
	 * @since 2.7.0
	 *
	 * @param int        $comment_ID The comment ID.
	 * @param WP_Comment $comment    Comment object.
	 */
	do_action( "comment_{$new_status}_{$comment->comment_type}", $comment->comment_ID, $comment );
}

/**
 * Get current commenter's name, email, and URL.
 *
 * Expects cookies content to already be sanitized. User of this function might
 * wish to recheck the returned array for validity.
 *
 * @see sanitize_comment_cookies() Use to sanitize cookies
 *
 * @since 2.0.4
 *
 * @return array Comment author, email, url respectively.
 */
function wp_get_current_commenter() {
	// Cookies should already be sanitized.

	$comment_author = '';
	if ( isset($_COOKIE['comment_author_'.COOKIEHASH]) )
		$comment_author = $_COOKIE['comment_author_'.COOKIEHASH];

	$comment_author_email = '';
	if ( isset($_COOKIE['comment_author_email_'.COOKIEHASH]) )
		$comment_author_email = $_COOKIE['comment_author_email_'.COOKIEHASH];

	$comment_author_url = '';
	if ( isset($_COOKIE['comment_author_url_'.COOKIEHASH]) )
		$comment_author_url = $_COOKIE['comment_author_url_'.COOKIEHASH];

	/**
	 * Filter the current commenter's name, email, and URL.
	 *
	 * @since 3.1.0
	 *
	 * @param array $comment_author_data {
	 *     An array of current commenter variables.
	 *
	 *     @type string $comment_author       The name of the author of the comment. Default empty.
	 *     @type string $comment_author_email The email address of the `$comment_author`. Default empty.
	 *     @type string $comment_author_url   The URL address of the `$comment_author`. Default empty.
	 * }
	 */
	return apply_filters( 'wp_get_current_commenter', compact('comment_author', 'comment_author_email', 'comment_author_url') );
}

/**
 * Inserts a comment into the database.
 *
 * @since 2.0.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param array $commentdata {
 *     Array of arguments for inserting a new comment.
 *
 *     @type string     $comment_agent        The HTTP user agent of the `$comment_author` when
 *                                            the comment was submitted. Default empty.
 *     @type int|string $comment_approved     Whether the comment has been approved. Default 1.
 *     @type string     $comment_author       The name of the author of the comment. Default empty.
 *     @type string     $comment_author_email The email address of the `$comment_author`. Default empty.
 *     @type string     $comment_author_IP    The IP address of the `$comment_author`. Default empty.
 *     @type string     $comment_author_url   The URL address of the `$comment_author`. Default empty.
 *     @type string     $comment_content      The content of the comment. Default empty.
 *     @type string     $comment_date         The date the comment was submitted. To set the date
 *                                            manually, `$comment_date_gmt` must also be specified.
 *                                            Default is the current time.
 *     @type string     $comment_date_gmt     The date the comment was submitted in the GMT timezone.
 *                                            Default is `$comment_date` in the site's GMT timezone.
 *     @type int        $comment_karma        The karma of the comment. Default 0.
 *     @type int        $comment_parent       ID of this comment's parent, if any. Default 0.
 *     @type int        $comment_post_ID      ID of the post that relates to the comment, if any.
 *                                            Default empty.
 *     @type string     $comment_type         Comment type. Default empty.
 *     @type int        $user_id              ID of the user who submitted the comment. Default 0.
 * }
 * @return int|false The new comment's ID on success, false on failure.
 */
function wp_insert_comment( $commentdata ) {
	global $wpdb;
	$data = wp_unslash( $commentdata );

	$comment_author       = ! isset( $data['comment_author'] )       ? '' : $data['comment_author'];
	$comment_author_email = ! isset( $data['comment_author_email'] ) ? '' : $data['comment_author_email'];
	$comment_author_url   = ! isset( $data['comment_author_url'] )   ? '' : $data['comment_author_url'];
	$comment_author_IP    = ! isset( $data['comment_author_IP'] )    ? '' : $data['comment_author_IP'];

	$comment_date     = ! isset( $data['comment_date'] )     ? current_time( 'mysql' )            : $data['comment_date'];
	$comment_date_gmt = ! isset( $data['comment_date_gmt'] ) ? get_gmt_from_date( $comment_date ) : $data['comment_date_gmt'];

	$comment_post_ID  = ! isset( $data['comment_post_ID'] )  ? '' : $data['comment_post_ID'];
	$comment_content  = ! isset( $data['comment_content'] )  ? '' : $data['comment_content'];
	$comment_karma    = ! isset( $data['comment_karma'] )    ? 0  : $data['comment_karma'];
	$comment_approved = ! isset( $data['comment_approved'] ) ? 1  : $data['comment_approved'];
	$comment_agent    = ! isset( $data['comment_agent'] )    ? '' : $data['comment_agent'];
	$comment_type     = ! isset( $data['comment_type'] )     ? '' : $data['comment_type'];
	$comment_parent   = ! isset( $data['comment_parent'] )   ? 0  : $data['comment_parent'];

	$user_id  = ! isset( $data['user_id'] ) ? 0 : $data['user_id'];

	$compacted = compact( 'comment_post_ID', 'comment_author', 'comment_author_email', 'comment_author_url', 'comment_author_IP', 'comment_date', 'comment_date_gmt', 'comment_content', 'comment_karma', 'comment_approved', 'comment_agent', 'comment_type', 'comment_parent', 'user_id' );
	if ( ! $wpdb->insert( $wpdb->comments, $compacted ) ) {
		return false;
	}

	$id = (int) $wpdb->insert_id;

	if ( $comment_approved == 1 ) {
		wp_update_comment_count( $comment_post_ID );
	}
	$comment = get_comment( $id );

	/**
	 * Fires immediately after a comment is inserted into the database.
	 *
	 * @since 2.8.0
	 *
	 * @param int        $id      The comment ID.
	 * @param WP_Comment $comment Comment object.
	 */
	do_action( 'wp_insert_comment', $id, $comment );

	wp_cache_set( 'last_changed', microtime(), 'comment' );

	return $id;
}

/**
 * Filters and sanitizes comment data.
 *
 * Sets the comment data 'filtered' field to true when finished. This can be
 * checked as to whether the comment should be filtered and to keep from
 * filtering the same comment more than once.
 *
 * @since 2.0.0
 *
 * @param array $commentdata Contains information on the comment.
 * @return array Parsed comment information.
 */
function wp_filter_comment($commentdata) {
	if ( isset( $commentdata['user_ID'] ) ) {
		/**
		 * Filter the comment author's user id before it is set.
		 *
		 * The first time this filter is evaluated, 'user_ID' is checked
		 * (for back-compat), followed by the standard 'user_id' value.
		 *
		 * @since 1.5.0
		 *
		 * @param int $user_ID The comment author's user ID.
		 */
		$commentdata['user_id'] = apply_filters( 'pre_user_id', $commentdata['user_ID'] );
	} elseif ( isset( $commentdata['user_id'] ) ) {
		/** This filter is documented in wp-includes/comment.php */
		$commentdata['user_id'] = apply_filters( 'pre_user_id', $commentdata['user_id'] );
	}

	/**
	 * Filter the comment author's browser user agent before it is set.
	 *
	 * @since 1.5.0
	 *
	 * @param int $comment_agent The comment author's browser user agent.
	 */
	$commentdata['comment_agent'] = apply_filters( 'pre_comment_user_agent', ( isset( $commentdata['comment_agent'] ) ? $commentdata['comment_agent'] : '' ) );
	/** This filter is documented in wp-includes/comment.php */
	$commentdata['comment_author'] = apply_filters( 'pre_comment_author_name', $commentdata['comment_author'] );
	/**
	 * Filter the comment content before it is set.
	 *
	 * @since 1.5.0
	 *
	 * @param int $comment_content The comment content.
	 */
	$commentdata['comment_content'] = apply_filters( 'pre_comment_content', $commentdata['comment_content'] );
	/**
	 * Filter the comment author's IP before it is set.
	 *
	 * @since 1.5.0
	 *
	 * @param int $comment_author_ip The comment author's IP.
	 */
	$commentdata['comment_author_IP'] = apply_filters( 'pre_comment_user_ip', $commentdata['comment_author_IP'] );
	/** This filter is documented in wp-includes/comment.php */
	$commentdata['comment_author_url'] = apply_filters( 'pre_comment_author_url', $commentdata['comment_author_url'] );
	/** This filter is documented in wp-includes/comment.php */
	$commentdata['comment_author_email'] = apply_filters( 'pre_comment_author_email', $commentdata['comment_author_email'] );
	$commentdata['filtered'] = true;
	return $commentdata;
}

/**
 * Whether a comment should be blocked because of comment flood.
 *
 * @since 2.1.0
 *
 * @param bool $block Whether plugin has already blocked comment.
 * @param int $time_lastcomment Timestamp for last comment.
 * @param int $time_newcomment Timestamp for new comment.
 * @return bool Whether comment should be blocked.
 */
function wp_throttle_comment_flood($block, $time_lastcomment, $time_newcomment) {
	if ( $block ) // a plugin has already blocked... we'll let that decision stand
		return $block;
	if ( ($time_newcomment - $time_lastcomment) < 15 )
		return true;
	return false;
}

/**
 * Adds a new comment to the database.
 *
 * Filters new comment to ensure that the fields are sanitized and valid before
 * inserting comment into database. Calls 'comment_post' action with comment ID
 * and whether comment is approved by WordPress. Also has 'preprocess_comment'
 * filter for processing the comment data before the function handles it.
 *
 * We use REMOTE_ADDR here directly. If you are behind a proxy, you should ensure
 * that it is properly set, such as in wp-config.php, for your environment.
 * See {@link https://core.trac.wordpress.org/ticket/9235}
 *
 * @since 1.5.0
 * @since 4.3.0 'comment_agent' and 'comment_author_IP' can be set via `$commentdata`.
 *
 * @see wp_insert_comment()
 *
 * @global wpdb $wpdb
 *
 * @param array $commentdata {
 *     Comment data.
 *
 *     @type string $comment_author       The name of the comment author.
 *     @type string $comment_author_email The comment author email address.
 *     @type string $comment_author_url   The comment author URL.
 *     @type string $comment_content      The content of the comment.
 *     @type string $comment_date         The date the comment was submitted. Default is the current time.
 *     @type string $comment_date_gmt     The date the comment was submitted in the GMT timezone.
 *                                        Default is `$comment_date` in the GMT timezone.
 *     @type int    $comment_parent       The ID of this comment's parent, if any. Default 0.
 *     @type int    $comment_post_ID      The ID of the post that relates to the comment.
 *     @type int    $user_id              The ID of the user who submitted the comment. Default 0.
 *     @type int    $user_ID              Kept for backward-compatibility. Use `$user_id` instead.
 *     @type string $comment_agent        Comment author user agent. Default is the value of 'HTTP_USER_AGENT'
 *                                        in the `$_SERVER` superglobal sent in the original request.
 *     @type string $comment_author_IP    Comment author IP address in IPv4 format. Default is the value of
 *                                        'REMOTE_ADDR' in the `$_SERVER` superglobal sent in the original request.
 * }
 * @return int|false The ID of the comment on success, false on failure.
 */
function wp_new_comment( $commentdata ) {
	global $wpdb;

	if ( isset( $commentdata['user_ID'] ) ) {
		$commentdata['user_id'] = $commentdata['user_ID'] = (int) $commentdata['user_ID'];
	}

	$prefiltered_user_id = ( isset( $commentdata['user_id'] ) ) ? (int) $commentdata['user_id'] : 0;

	/**
	 * Filter a comment's data before it is sanitized and inserted into the database.
	 *
	 * @since 1.5.0
	 *
	 * @param array $commentdata Comment data.
	 */
	$commentdata = apply_filters( 'preprocess_comment', $commentdata );

	$commentdata['comment_post_ID'] = (int) $commentdata['comment_post_ID'];
	if ( isset( $commentdata['user_ID'] ) && $prefiltered_user_id !== (int) $commentdata['user_ID'] ) {
		$commentdata['user_id'] = $commentdata['user_ID'] = (int) $commentdata['user_ID'];
	} elseif ( isset( $commentdata['user_id'] ) ) {
		$commentdata['user_id'] = (int) $commentdata['user_id'];
	}

	$commentdata['comment_parent'] = isset($commentdata['comment_parent']) ? absint($commentdata['comment_parent']) : 0;
	$parent_status = ( 0 < $commentdata['comment_parent'] ) ? wp_get_comment_status($commentdata['comment_parent']) : '';
	$commentdata['comment_parent'] = ( 'approved' == $parent_status || 'unapproved' == $parent_status ) ? $commentdata['comment_parent'] : 0;

	if ( ! isset( $commentdata['comment_author_IP'] ) ) {
		$commentdata['comment_author_IP'] = $_SERVER['REMOTE_ADDR'];
	}
	$commentdata['comment_author_IP'] = preg_replace( '/[^0-9a-fA-F:., ]/', '', $commentdata['comment_author_IP'] );

	if ( ! isset( $commentdata['comment_agent'] ) ) {
		$commentdata['comment_agent'] = isset( $_SERVER['HTTP_USER_AGENT'] ) ? $_SERVER['HTTP_USER_AGENT']: '';
	}
	$commentdata['comment_agent'] = substr( $commentdata['comment_agent'], 0, 254 );

	if ( empty( $commentdata['comment_date'] ) ) {
		$commentdata['comment_date'] = current_time('mysql');
	}

	if ( empty( $commentdata['comment_date_gmt'] ) ) {
		$commentdata['comment_date_gmt'] = current_time( 'mysql', 1 );
	}

	$commentdata = wp_filter_comment($commentdata);

	$commentdata['comment_approved'] = wp_allow_comment($commentdata);

	$comment_ID = wp_insert_comment($commentdata);
	if ( ! $comment_ID ) {
		$fields = array( 'comment_author', 'comment_author_email', 'comment_author_url', 'comment_content' );

		foreach ( $fields as $field ) {
			if ( isset( $commentdata[ $field ] ) ) {
				$commentdata[ $field ] = $wpdb->strip_invalid_text_for_column( $wpdb->comments, $field, $commentdata[ $field ] );
			}
		}

		$commentdata = wp_filter_comment( $commentdata );

		$commentdata['comment_approved'] = wp_allow_comment( $commentdata );

		$comment_ID = wp_insert_comment( $commentdata );
		if ( ! $comment_ID ) {
			return false;
		}
	}

	/**
	 * Fires immediately after a comment is inserted into the database.
	 *
	 * @since 1.2.0
	 *
	 * @param int $comment_ID       The comment ID.
	 * @param int $comment_approved 1 (true) if the comment is approved, 0 (false) if not.
	 */
	do_action( 'comment_post', $comment_ID, $commentdata['comment_approved'] );

	if ( 'spam' !== $commentdata['comment_approved'] ) { // If it's spam save it silently for later crunching
		if ( '0' == $commentdata['comment_approved'] ) {
			wp_notify_moderator( $comment_ID );
		}

		// wp_notify_postauthor() checks if notifying the author of their own comment.
		// By default, it won't, but filters can override this.
		if ( get_option( 'comments_notify' ) && $commentdata['comment_approved'] ) {
			wp_notify_postauthor( $comment_ID );
		}
	}

	return $comment_ID;
}

/**
 * Sets the status of a comment.
 *
 * The 'wp_set_comment_status' action is called after the comment is handled.
 * If the comment status is not in the list, then false is returned.
 *
 * @since 1.0.0
 *
 * global wpdb $wpdb
 *
 * @param int $comment_id Comment ID.
 * @param string $comment_status New comment status, either 'hold', 'approve', 'spam', or 'trash'.
 * @param bool $wp_error Whether to return a WP_Error object if there is a failure. Default is false.
 * @return bool|WP_Error True on success, false or WP_Error on failure.
 */
function wp_set_comment_status($comment_id, $comment_status, $wp_error = false) {
	global $wpdb;

	switch ( $comment_status ) {
		case 'hold':
		case '0':
			$status = '0';
			break;
		case 'approve':
		case '1':
			$status = '1';
			if ( get_option('comments_notify') ) {
				wp_notify_postauthor( $comment_id );
			}
			break;
		case 'spam':
			$status = 'spam';
			break;
		case 'trash':
			$status = 'trash';
			break;
		default:
			return false;
	}

	$comment_old = clone get_comment($comment_id);

	if ( !$wpdb->update( $wpdb->comments, array('comment_approved' => $status), array('comment_ID' => $comment_id) ) ) {
		if ( $wp_error )
			return new WP_Error('db_update_error', __('Could not update comment status'), $wpdb->last_error);
		else
			return false;
	}

	clean_comment_cache($comment_id);

	$comment = get_comment($comment_id);

	/**
	 * Fires immediately before transitioning a comment's status from one to another
	 * in the database.
	 *
	 * @since 1.5.0
	 *
	 * @param int         $comment_id     Comment ID.
	 * @param string|bool $comment_status Current comment status. Possible values include
	 *                                    'hold', 'approve', 'spam', 'trash', or false.
	 */
	do_action( 'wp_set_comment_status', $comment_id, $comment_status );

	wp_transition_comment_status($comment_status, $comment_old->comment_approved, $comment);

	wp_update_comment_count($comment->comment_post_ID);

	return true;
}

/**
 * Updates an existing comment in the database.
 *
 * Filters the comment and makes sure certain fields are valid before updating.
 *
 * @since 2.0.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param array $commentarr Contains information on the comment.
 * @return int Comment was updated if value is 1, or was not updated if value is 0.
 */
function wp_update_comment($commentarr) {
	global $wpdb;

	// First, get all of the original fields
	$comment = get_comment($commentarr['comment_ID'], ARRAY_A);
	if ( empty( $comment ) ) {
		return 0;
	}

	// Make sure that the comment post ID is valid (if specified).
	if ( isset( $commentarr['comment_post_ID'] ) && ! get_post( $commentarr['comment_post_ID'] ) ) {
		return 0;
	}

	// Escape data pulled from DB.
	$comment = wp_slash($comment);

	$old_status = $comment['comment_approved'];

	// Merge old and new fields with new fields overwriting old ones.
	$commentarr = array_merge($comment, $commentarr);

	$commentarr = wp_filter_comment( $commentarr );

	// Now extract the merged array.
	$data = wp_unslash( $commentarr );

	/**
	 * Filter the comment content before it is updated in the database.
	 *
	 * @since 1.5.0
	 *
	 * @param string $comment_content The comment data.
	 */
	$data['comment_content'] = apply_filters( 'comment_save_pre', $data['comment_content'] );

	$data['comment_date_gmt'] = get_gmt_from_date( $data['comment_date'] );

	if ( ! isset( $data['comment_approved'] ) ) {
		$data['comment_approved'] = 1;
	} elseif ( 'hold' == $data['comment_approved'] ) {
		$data['comment_approved'] = 0;
	} elseif ( 'approve' == $data['comment_approved'] ) {
		$data['comment_approved'] = 1;
	}

	$comment_ID = $data['comment_ID'];
	$comment_post_ID = $data['comment_post_ID'];
	$keys = array( 'comment_post_ID', 'comment_content', 'comment_author', 'comment_author_email', 'comment_approved', 'comment_karma', 'comment_author_url', 'comment_date', 'comment_date_gmt', 'comment_type', 'comment_parent', 'user_id' );
	$data = wp_array_slice_assoc( $data, $keys );
	$rval = $wpdb->update( $wpdb->comments, $data, compact( 'comment_ID' ) );

	clean_comment_cache( $comment_ID );
	wp_update_comment_count( $comment_post_ID );
	/**
	 * Fires immediately after a comment is updated in the database.
	 *
	 * The hook also fires immediately before comment status transition hooks are fired.
	 *
	 * @since 1.2.0
	 *
	 * @param int $comment_ID The comment ID.
	 */
	do_action( 'edit_comment', $comment_ID );
	$comment = get_comment($comment_ID);
	wp_transition_comment_status($comment->comment_approved, $old_status, $comment);
	return $rval;
}

/**
 * Whether to defer comment counting.
 *
 * When setting $defer to true, all post comment counts will not be updated
 * until $defer is set to false. When $defer is set to false, then all
 * previously deferred updated post comment counts will then be automatically
 * updated without having to call wp_update_comment_count() after.
 *
 * @since 2.5.0
 * @staticvar bool $_defer
 *
 * @param bool $defer
 * @return bool
 */
function wp_defer_comment_counting($defer=null) {
	static $_defer = false;

	if ( is_bool($defer) ) {
		$_defer = $defer;
		// flush any deferred counts
		if ( !$defer )
			wp_update_comment_count( null, true );
	}

	return $_defer;
}

/**
 * Updates the comment count for post(s).
 *
 * When $do_deferred is false (is by default) and the comments have been set to
 * be deferred, the post_id will be added to a queue, which will be updated at a
 * later date and only updated once per post ID.
 *
 * If the comments have not be set up to be deferred, then the post will be
 * updated. When $do_deferred is set to true, then all previous deferred post
 * IDs will be updated along with the current $post_id.
 *
 * @since 2.1.0
 * @see wp_update_comment_count_now() For what could cause a false return value
 *
 * @staticvar array $_deferred
 *
 * @param int $post_id Post ID
 * @param bool $do_deferred Whether to process previously deferred post comment counts
 * @return bool|void True on success, false on failure
 */
function wp_update_comment_count($post_id, $do_deferred=false) {
	static $_deferred = array();

	if ( $do_deferred ) {
		$_deferred = array_unique($_deferred);
		foreach ( $_deferred as $i => $_post_id ) {
			wp_update_comment_count_now($_post_id);
			unset( $_deferred[$i] ); /** @todo Move this outside of the foreach and reset $_deferred to an array instead */
		}
	}

	if ( wp_defer_comment_counting() ) {
		$_deferred[] = $post_id;
		return true;
	}
	elseif ( $post_id ) {
		return wp_update_comment_count_now($post_id);
	}

}

/**
 * Updates the comment count for the post.
 *
 * @since 2.5.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param int $post_id Post ID
 * @return bool True on success, false on '0' $post_id or if post with ID does not exist.
 */
function wp_update_comment_count_now($post_id) {
	global $wpdb;
	$post_id = (int) $post_id;
	if ( !$post_id )
		return false;
	if ( !$post = get_post($post_id) )
		return false;

	$old = (int) $post->comment_count;
	$new = (int) $wpdb->get_var( $wpdb->prepare("SELECT COUNT(*) FROM $wpdb->comments WHERE comment_post_ID = %d AND comment_approved = '1'", $post_id) );
	$wpdb->update( $wpdb->posts, array('comment_count' => $new), array('ID' => $post_id) );

	clean_post_cache( $post );

	/**
	 * Fires immediately after a post's comment count is updated in the database.
	 *
	 * @since 2.3.0
	 *
	 * @param int $post_id Post ID.
	 * @param int $new     The new comment count.
	 * @param int $old     The old comment count.
	 */
	do_action( 'wp_update_comment_count', $post_id, $new, $old );
	/** This action is documented in wp-includes/post.php */
	do_action( 'edit_post', $post_id, $post );

	return true;
}

//
// Ping and trackback functions.
//

/**
 * Finds a pingback server URI based on the given URL.
 *
 * Checks the HTML for the rel="pingback" link and x-pingback headers. It does
 * a check for the x-pingback headers first and returns that, if available. The
 * check for the rel="pingback" has more overhead than just the header.
 *
 * @since 1.5.0
 *
 * @param string $url URL to ping.
 * @param int $deprecated Not Used.
 * @return false|string False on failure, string containing URI on success.
 */
function discover_pingback_server_uri( $url, $deprecated = '' ) {
	if ( !empty( $deprecated ) )
		_deprecated_argument( __FUNCTION__, '2.7' );

	$pingback_str_dquote = 'rel="pingback"';
	$pingback_str_squote = 'rel=\'pingback\'';

	/** @todo Should use Filter Extension or custom preg_match instead. */
	$parsed_url = parse_url($url);

	if ( ! isset( $parsed_url['host'] ) ) // Not an URL. This should never happen.
		return false;

	//Do not search for a pingback server on our own uploads
	$uploads_dir = wp_upload_dir();
	if ( 0 === strpos($url, $uploads_dir['baseurl']) )
		return false;

	$response = wp_safe_remote_head( $url, array( 'timeout' => 2, 'httpversion' => '1.0' ) );

	if ( is_wp_error( $response ) )
		return false;

	if ( wp_remote_retrieve_header( $response, 'x-pingback' ) )
		return wp_remote_retrieve_header( $response, 'x-pingback' );

	// Not an (x)html, sgml, or xml page, no use going further.
	if ( preg_match('#(image|audio|video|model)/#is', wp_remote_retrieve_header( $response, 'content-type' )) )
		return false;

	// Now do a GET since we're going to look in the html headers (and we're sure it's not a binary file)
	$response = wp_safe_remote_get( $url, array( 'timeout' => 2, 'httpversion' => '1.0' ) );

	if ( is_wp_error( $response ) )
		return false;

	$contents = wp_remote_retrieve_body( $response );

	$pingback_link_offset_dquote = strpos($contents, $pingback_str_dquote);
	$pingback_link_offset_squote = strpos($contents, $pingback_str_squote);
	if ( $pingback_link_offset_dquote || $pingback_link_offset_squote ) {
		$quote = ($pingback_link_offset_dquote) ? '"' : '\'';
		$pingback_link_offset = ($quote=='"') ? $pingback_link_offset_dquote : $pingback_link_offset_squote;
		$pingback_href_pos = @strpos($contents, 'href=', $pingback_link_offset);
		$pingback_href_start = $pingback_href_pos+6;
		$pingback_href_end = @strpos($contents, $quote, $pingback_href_start);
		$pingback_server_url_len = $pingback_href_end - $pingback_href_start;
		$pingback_server_url = substr($contents, $pingback_href_start, $pingback_server_url_len);

		// We may find rel="pingback" but an incomplete pingback URL
		if ( $pingback_server_url_len > 0 ) { // We got it!
			return $pingback_server_url;
		}
	}

	return false;
}

/**
 * Perform all pingbacks, enclosures, trackbacks, and send to pingback services.
 *
 * @since 2.1.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 */
function do_all_pings() {
	global $wpdb;

	// Do pingbacks
	while ($ping = $wpdb->get_row("SELECT ID, post_content, meta_id FROM {$wpdb->posts}, {$wpdb->postmeta} WHERE {$wpdb->posts}.ID = {$wpdb->postmeta}.post_id AND {$wpdb->postmeta}.meta_key = '_pingme' LIMIT 1")) {
		delete_metadata_by_mid( 'post', $ping->meta_id );
		pingback( $ping->post_content, $ping->ID );
	}

	// Do Enclosures
	while ($enclosure = $wpdb->get_row("SELECT ID, post_content, meta_id FROM {$wpdb->posts}, {$wpdb->postmeta} WHERE {$wpdb->posts}.ID = {$wpdb->postmeta}.post_id AND {$wpdb->postmeta}.meta_key = '_encloseme' LIMIT 1")) {
		delete_metadata_by_mid( 'post', $enclosure->meta_id );
		do_enclose( $enclosure->post_content, $enclosure->ID );
	}

	// Do Trackbacks
	$trackbacks = $wpdb->get_col("SELECT ID FROM $wpdb->posts WHERE to_ping <> '' AND post_status = 'publish'");
	if ( is_array($trackbacks) )
		foreach ( $trackbacks as $trackback )
			do_trackbacks($trackback);

	//Do Update Services/Generic Pings
	generic_ping();
}

/**
 * Perform trackbacks.
 *
 * @since 1.5.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param int $post_id Post ID to do trackbacks on.
 */
function do_trackbacks($post_id) {
	global $wpdb;

	$post = get_post( $post_id );
	$to_ping = get_to_ping($post_id);
	$pinged  = get_pung($post_id);
	if ( empty($to_ping) ) {
		$wpdb->update($wpdb->posts, array('to_ping' => ''), array('ID' => $post_id) );
		return;
	}

	if ( empty($post->post_excerpt) ) {
		/** This filter is documented in wp-includes/post-template.php */
		$excerpt = apply_filters( 'the_content', $post->post_content, $post->ID );
	} else {
		/** This filter is documented in wp-includes/post-template.php */
		$excerpt = apply_filters( 'the_excerpt', $post->post_excerpt );
	}

	$excerpt = str_replace(']]>', ']]&gt;', $excerpt);
	$excerpt = wp_html_excerpt($excerpt, 252, '&#8230;');

	/** This filter is documented in wp-includes/post-template.php */
	$post_title = apply_filters( 'the_title', $post->post_title, $post->ID );
	$post_title = strip_tags($post_title);

	if ( $to_ping ) {
		foreach ( (array) $to_ping as $tb_ping ) {
			$tb_ping = trim($tb_ping);
			if ( !in_array($tb_ping, $pinged) ) {
				trackback($tb_ping, $post_title, $excerpt, $post_id);
				$pinged[] = $tb_ping;
			} else {
				$wpdb->query( $wpdb->prepare("UPDATE $wpdb->posts SET to_ping = TRIM(REPLACE(to_ping, %s, '')) WHERE ID = %d", $tb_ping, $post_id) );
			}
		}
	}
}

/**
 * Sends pings to all of the ping site services.
 *
 * @since 1.2.0
 *
 * @param int $post_id Post ID.
 * @return int Same as Post ID from parameter
 */
function generic_ping( $post_id = 0 ) {
	$services = get_option('ping_sites');

	$services = explode("\n", $services);
	foreach ( (array) $services as $service ) {
		$service = trim($service);
		if ( '' != $service )
			weblog_ping($service);
	}

	return $post_id;
}

/**
 * Pings back the links found in a post.
 *
 * @since 0.71
 *
 * @global string $wp_version
 *
 * @param string $content Post content to check for links.
 * @param int $post_ID Post ID.
 */
function pingback($content, $post_ID) {
	global $wp_version;
	include_once(ABSPATH . WPINC . '/class-IXR.php');
	include_once(ABSPATH . WPINC . '/class-wp-http-ixr-client.php');

	// original code by Mort (http://mort.mine.nu:8080)
	$post_links = array();

	$pung = get_pung($post_ID);

	// Step 1
	// Parsing the post, external links (if any) are stored in the $post_links array
	$post_links_temp = wp_extract_urls( $content );

	// Step 2.
	// Walking thru the links array
	// first we get rid of links pointing to sites, not to specific files
	// Example:
	// http://dummy-weblog.org
	// http://dummy-weblog.org/
	// http://dummy-weblog.org/post.php
	// We don't wanna ping first and second types, even if they have a valid <link/>

	foreach ( (array) $post_links_temp as $link_test ) :
		if ( !in_array($link_test, $pung) && (url_to_postid($link_test) != $post_ID) // If we haven't pung it already and it isn't a link to itself
				&& !is_local_attachment($link_test) ) : // Also, let's never ping local attachments.
			if ( $test = @parse_url($link_test) ) {
				if ( isset($test['query']) )
					$post_links[] = $link_test;
				elseif ( isset( $test['path'] ) && ( $test['path'] != '/' ) && ( $test['path'] != '' ) )
					$post_links[] = $link_test;
			}
		endif;
	endforeach;

	$post_links = array_unique( $post_links );
	/**
	 * Fires just before pinging back links found in a post.
	 *
	 * @since 2.0.0
	 *
	 * @param array &$post_links An array of post links to be checked, passed by reference.
	 * @param array &$pung       Whether a link has already been pinged, passed by reference.
	 * @param int   $post_ID     The post ID.
	 */
	do_action_ref_array( 'pre_ping', array( &$post_links, &$pung, $post_ID ) );

	foreach ( (array) $post_links as $pagelinkedto ) {
		$pingback_server_url = discover_pingback_server_uri( $pagelinkedto );

		if ( $pingback_server_url ) {
			@ set_time_limit( 60 );
			// Now, the RPC call
			$pagelinkedfrom = get_permalink($post_ID);

			// using a timeout of 3 seconds should be enough to cover slow servers
			$client = new WP_HTTP_IXR_Client($pingback_server_url);
			$client->timeout = 3;
			/**
			 * Filter the user agent sent when pinging-back a URL.
			 *
			 * @since 2.9.0
			 *
			 * @param string $concat_useragent    The user agent concatenated with ' -- WordPress/'
			 *                                    and the WordPress version.
			 * @param string $useragent           The useragent.
			 * @param string $pingback_server_url The server URL being linked to.
			 * @param string $pagelinkedto        URL of page linked to.
			 * @param string $pagelinkedfrom      URL of page linked from.
			 */
			$client->useragent = apply_filters( 'pingback_useragent', $client->useragent . ' -- WordPress/' . $wp_version, $client->useragent, $pingback_server_url, $pagelinkedto, $pagelinkedfrom );
			// when set to true, this outputs debug messages by itself
			$client->debug = false;

			if ( $client->query('pingback.ping', $pagelinkedfrom, $pagelinkedto) || ( isset($client->error->code) && 48 == $client->error->code ) ) // Already registered
				add_ping( $post_ID, $pagelinkedto );
		}
	}
}

/**
 * Check whether blog is public before returning sites.
 *
 * @since 2.1.0
 *
 * @param mixed $sites Will return if blog is public, will not return if not public.
 * @return mixed Empty string if blog is not public, returns $sites, if site is public.
 */
function privacy_ping_filter($sites) {
	if ( '0' != get_option('blog_public') )
		return $sites;
	else
		return '';
}

/**
 * Send a Trackback.
 *
 * Updates database when sending trackback to prevent duplicates.
 *
 * @since 0.71
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param string $trackback_url URL to send trackbacks.
 * @param string $title Title of post.
 * @param string $excerpt Excerpt of post.
 * @param int $ID Post ID.
 * @return int|false|void Database query from update.
 */
function trackback($trackback_url, $title, $excerpt, $ID) {
	global $wpdb;

	if ( empty($trackback_url) )
		return;

	$options = array();
	$options['timeout'] = 4;
	$options['body'] = array(
		'title' => $title,
		'url' => get_permalink($ID),
		'blog_name' => get_option('blogname'),
		'excerpt' => $excerpt
	);

	$response = wp_safe_remote_post( $trackback_url, $options );

	if ( is_wp_error( $response ) )
		return;

	$wpdb->query( $wpdb->prepare("UPDATE $wpdb->posts SET pinged = CONCAT(pinged, '\n', %s) WHERE ID = %d", $trackback_url, $ID) );
	return $wpdb->query( $wpdb->prepare("UPDATE $wpdb->posts SET to_ping = TRIM(REPLACE(to_ping, %s, '')) WHERE ID = %d", $trackback_url, $ID) );
}

/**
 * Send a pingback.
 *
 * @since 1.2.0
 *
 * @global string $wp_version
 *
 * @param string $server Host of blog to connect to.
 * @param string $path Path to send the ping.
 */
function weblog_ping($server = '', $path = '') {
	global $wp_version;
	include_once(ABSPATH . WPINC . '/class-IXR.php');
	include_once(ABSPATH . WPINC . '/class-wp-http-ixr-client.php');

	// using a timeout of 3 seconds should be enough to cover slow servers
	$client = new WP_HTTP_IXR_Client($server, ((!strlen(trim($path)) || ('/' == $path)) ? false : $path));
	$client->timeout = 3;
	$client->useragent .= ' -- WordPress/'.$wp_version;

	// when set to true, this outputs debug messages by itself
	$client->debug = false;
	$home = trailingslashit( home_url() );
	if ( !$client->query('weblogUpdates.extendedPing', get_option('blogname'), $home, get_bloginfo('rss2_url') ) ) // then try a normal ping
		$client->query('weblogUpdates.ping', get_option('blogname'), $home);
}

/**
 * Default filter attached to pingback_ping_source_uri to validate the pingback's Source URI
 *
 * @since 3.5.1
 * @see wp_http_validate_url()
 *
 * @param string $source_uri
 * @return string
 */
function pingback_ping_source_uri( $source_uri ) {
	return (string) wp_http_validate_url( $source_uri );
}

/**
 * Default filter attached to xmlrpc_pingback_error.
 *
 * Returns a generic pingback error code unless the error code is 48,
 * which reports that the pingback is already registered.
 *
 * @since 3.5.1
 * @link http://www.hixie.ch/specs/pingback/pingback#TOC3
 *
 * @param IXR_Error $ixr_error
 * @return IXR_Error
 */
function xmlrpc_pingback_error( $ixr_error ) {
	if ( $ixr_error->code === 48 )
		return $ixr_error;
	return new IXR_Error( 0, '' );
}

//
// Cache
//

/**
 * Removes comment ID from the comment cache.
 *
 * @since 2.3.0
 *
 * @param int|array $ids Comment ID or array of comment IDs to remove from cache
 */
function clean_comment_cache($ids) {
	foreach ( (array) $ids as $id )
		wp_cache_delete($id, 'comment');

	wp_cache_set( 'last_changed', microtime(), 'comment' );
}

/**
 * Updates the comment cache of given comments.
 *
 * Will add the comments in $comments to the cache. If comment ID already exists
 * in the comment cache then it will not be updated. The comment is added to the
 * cache using the comment group with the key using the ID of the comments.
 *
 * @since 2.3.0
 *
 * @param array $comments Array of comment row objects
 */
function update_comment_cache($comments) {
	foreach ( (array) $comments as $comment )
		wp_cache_add($comment->comment_ID, $comment, 'comment');
}

//
// Internal
//

/**
 * Close comments on old posts on the fly, without any extra DB queries. Hooked to the_posts.
 *
 * @access private
 * @since 2.7.0
 *
 * @param WP_Post  $posts Post data object.
 * @param WP_Query $query Query object.
 * @return array
 */
function _close_comments_for_old_posts( $posts, $query ) {
	if ( empty( $posts ) || ! $query->is_singular() || ! get_option( 'close_comments_for_old_posts' ) )
		return $posts;

	/**
	 * Filter the list of post types to automatically close comments for.
	 *
	 * @since 3.2.0
	 *
	 * @param array $post_types An array of registered post types. Default array with 'post'.
	 */
	$post_types = apply_filters( 'close_comments_for_post_types', array( 'post' ) );
	if ( ! in_array( $posts[0]->post_type, $post_types ) )
		return $posts;

	$days_old = (int) get_option( 'close_comments_days_old' );
	if ( ! $days_old )
		return $posts;

	if ( time() - strtotime( $posts[0]->post_date_gmt ) > ( $days_old * DAY_IN_SECONDS ) ) {
		$posts[0]->comment_status = 'closed';
		$posts[0]->ping_status = 'closed';
	}

	return $posts;
}

/**
 * Close comments on an old post. Hooked to comments_open and pings_open.
 *
 * @access private
 * @since 2.7.0
 *
 * @param bool $open Comments open or closed
 * @param int $post_id Post ID
 * @return bool $open
 */
function _close_comments_for_old_post( $open, $post_id ) {
	if ( ! $open )
		return $open;

	if ( !get_option('close_comments_for_old_posts') )
		return $open;

	$days_old = (int) get_option('close_comments_days_old');
	if ( !$days_old )
		return $open;

	$post = get_post($post_id);

	/** This filter is documented in wp-includes/comment.php */
	$post_types = apply_filters( 'close_comments_for_post_types', array( 'post' ) );
	if ( ! in_array( $post->post_type, $post_types ) )
		return $open;

	if ( time() - strtotime( $post->post_date_gmt ) > ( $days_old * DAY_IN_SECONDS ) )
		return false;

	return $open;
}
