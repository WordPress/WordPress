<?php
/**
 * Core Comment API
 *
 * @package WordPress
 * @subpackage Comment
 */

/**
 * Check whether a comment passes internal checks to be allowed to add.
 *
 * If manual comment moderation is set in the administration, then all checks,
 * regardless of their type and substance, will fail and the function will
 * return false.
 *
 * If the number of links exceeds the amount in the administration, then the
 * check fails. If any of the parameter contents contain any disallowed words,
 * then the check fails.
 *
 * If the comment author was approved before, then the comment is automatically
 * approved.
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
 *                             trackback, or pingback.
 * @return bool If all checks pass, true, otherwise false.
 */
function check_comment( $author, $email, $url, $comment, $user_ip, $user_agent, $comment_type ) {
	global $wpdb;

	// If manual moderation is enabled, skip all checks and return false.
	if ( 1 == get_option( 'comment_moderation' ) ) {
		return false;
	}

	/** This filter is documented in wp-includes/comment-template.php */
	$comment = apply_filters( 'comment_text', $comment, null, array() );

	// Check for the number of external links if a max allowed number is set.
	$max_links = get_option( 'comment_max_links' );
	if ( $max_links ) {
		$num_links = preg_match_all( '/<a [^>]*href/i', $comment, $out );

		/**
		 * Filters the number of links found in a comment.
		 *
		 * @since 3.0.0
		 * @since 4.7.0 Added the `$comment` parameter.
		 *
		 * @param int    $num_links The number of links found.
		 * @param string $url       Comment author's URL. Included in allowed links total.
		 * @param string $comment   Content of the comment.
		 */
		$num_links = apply_filters( 'comment_max_links_url', $num_links, $url, $comment );

		/*
		 * If the number of links in the comment exceeds the allowed amount,
		 * fail the check by returning false.
		 */
		if ( $num_links >= $max_links ) {
			return false;
		}
	}

	$mod_keys = trim( get_option( 'moderation_keys' ) );

	// If moderation 'keys' (keywords) are set, process them.
	if ( ! empty( $mod_keys ) ) {
		$words = explode( "\n", $mod_keys );

		foreach ( (array) $words as $word ) {
			$word = trim( $word );

			// Skip empty lines.
			if ( empty( $word ) ) {
				continue;
			}

			/*
			 * Do some escaping magic so that '#' (number of) characters in the spam
			 * words don't break things:
			 */
			$word = preg_quote( $word, '#' );

			/*
			 * Check the comment fields for moderation keywords. If any are found,
			 * fail the check for the given field by returning false.
			 */
			$pattern = "#$word#i";
			if ( preg_match( $pattern, $author ) ) {
				return false;
			}
			if ( preg_match( $pattern, $email ) ) {
				return false;
			}
			if ( preg_match( $pattern, $url ) ) {
				return false;
			}
			if ( preg_match( $pattern, $comment ) ) {
				return false;
			}
			if ( preg_match( $pattern, $user_ip ) ) {
				return false;
			}
			if ( preg_match( $pattern, $user_agent ) ) {
				return false;
			}
		}
	}

	/*
	 * Check if the option to approve comments by previously-approved authors is enabled.
	 *
	 * If it is enabled, check whether the comment author has a previously-approved comment,
	 * as well as whether there are any moderation keywords (if set) present in the author
	 * email address. If both checks pass, return true. Otherwise, return false.
	 */
	if ( 1 == get_option( 'comment_previously_approved' ) ) {
		if ( 'trackback' !== $comment_type && 'pingback' !== $comment_type && '' !== $author && '' !== $email ) {
			$comment_user = get_user_by( 'email', wp_unslash( $email ) );
			if ( ! empty( $comment_user->ID ) ) {
				$ok_to_comment = $wpdb->get_var( $wpdb->prepare( "SELECT comment_approved FROM $wpdb->comments WHERE user_id = %d AND comment_approved = '1' LIMIT 1", $comment_user->ID ) );
			} else {
				// expected_slashed ($author, $email)
				$ok_to_comment = $wpdb->get_var( $wpdb->prepare( "SELECT comment_approved FROM $wpdb->comments WHERE comment_author = %s AND comment_author_email = %s and comment_approved = '1' LIMIT 1", $author, $email ) );
			}
			if ( ( 1 == $ok_to_comment ) &&
				( empty( $mod_keys ) || false === strpos( $email, $mod_keys ) ) ) {
					return true;
			} else {
				return false;
			}
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
 * @since 4.1.0 Refactored to leverage WP_Comment_Query over a direct query.
 *
 * @param int   $post_id The ID of the post.
 * @param array $args    Optional. See WP_Comment_Query::__construct() for information on accepted arguments.
 * @return int|array The approved comments, or number of comments if `$count`
 *                   argument is true.
 */
function get_approved_comments( $post_id, $args = array() ) {
	if ( ! $post_id ) {
		return array();
	}

	$defaults    = array(
		'status'  => 1,
		'post_id' => $post_id,
		'order'   => 'ASC',
	);
	$parsed_args = wp_parse_args( $args, $defaults );

	$query = new WP_Comment_Query;
	return $query->query( $parsed_args );
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
 * @global WP_Comment $comment Global comment object.
 *
 * @param WP_Comment|string|int $comment Comment to retrieve.
 * @param string                $output  Optional. The required return type. One of OBJECT, ARRAY_A, or ARRAY_N, which
 *                                       correspond to a WP_Comment object, an associative array, or a numeric array,
 *                                       respectively. Default OBJECT.
 * @return WP_Comment|array|null Depends on $output value.
 */
function get_comment( $comment = null, $output = OBJECT ) {
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
	 * @param WP_Comment $_comment Comment data.
	 */
	$_comment = apply_filters( 'get_comment', $_comment );

	if ( OBJECT === $output ) {
		return $_comment;
	} elseif ( ARRAY_A === $output ) {
		return $_comment->to_array();
	} elseif ( ARRAY_N === $output ) {
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
 * @param string|array $args Optional. Array or string of arguments. See WP_Comment_Query::__construct()
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
 * @return string[] List of comment status labels keyed by status.
 */
function get_comment_statuses() {
	$status = array(
		'hold'    => __( 'Unapproved' ),
		'approve' => _x( 'Approved', 'comment status' ),
		'spam'    => _x( 'Spam', 'comment status' ),
		'trash'   => _x( 'Trash', 'comment status' ),
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
		case 'pingback':
		case 'trackback':
			$supports = 'trackbacks';
			$option   = 'ping';
			break;
		default:
			$supports = 'comments';
			$option   = 'comment';
			break;
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
	 * Filters the default comment status for the given post type.
	 *
	 * @since 4.3.0
	 *
	 * @param string $status       Default status for the given post type,
	 *                             either 'open' or 'closed'.
	 * @param string $post_type    Post type. Default is `post`.
	 * @param string $comment_type Type of comment. Default is `comment`.
	 */
	return apply_filters( 'get_default_comment_status', $status, $post_type, $comment_type );
}

/**
 * The date the last comment was modified.
 *
 * @since 1.5.0
 * @since 4.7.0 Replaced caching the modified date in a local static variable
 *              with the Object Cache API.
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param string $timezone Which timezone to use in reference to 'gmt', 'blog', or 'server' locations.
 * @return string|false Last comment modified date on success, false on failure.
 */
function get_lastcommentmodified( $timezone = 'server' ) {
	global $wpdb;

	$timezone = strtolower( $timezone );
	$key      = "lastcommentmodified:$timezone";

	$comment_modified_date = wp_cache_get( $key, 'timeinfo' );
	if ( false !== $comment_modified_date ) {
		return $comment_modified_date;
	}

	switch ( $timezone ) {
		case 'gmt':
			$comment_modified_date = $wpdb->get_var( "SELECT comment_date_gmt FROM $wpdb->comments WHERE comment_approved = '1' ORDER BY comment_date_gmt DESC LIMIT 1" );
			break;
		case 'blog':
			$comment_modified_date = $wpdb->get_var( "SELECT comment_date FROM $wpdb->comments WHERE comment_approved = '1' ORDER BY comment_date_gmt DESC LIMIT 1" );
			break;
		case 'server':
			$add_seconds_server = gmdate( 'Z' );

			$comment_modified_date = $wpdb->get_var( $wpdb->prepare( "SELECT DATE_ADD(comment_date_gmt, INTERVAL %s SECOND) FROM $wpdb->comments WHERE comment_approved = '1' ORDER BY comment_date_gmt DESC LIMIT 1", $add_seconds_server ) );
			break;
	}

	if ( $comment_modified_date ) {
		wp_cache_set( $key, $comment_modified_date, 'timeinfo' );

		return $comment_modified_date;
	}

	return false;
}

/**
 * Retrieves the total comment counts for the whole site or a single post.
 *
 * Unlike wp_count_comments(), this function always returns the live comment counts without caching.
 *
 * @since 2.0.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param int $post_id Optional. Restrict the comment counts to the given post. Default 0, which indicates that
 *                     comment counts for the whole site will be retrieved.
 * @return array() {
 *     The number of comments keyed by their status.
 *
 *     @type int $approved            The number of approved comments.
 *     @type int $awaiting_moderation The number of comments awaiting moderation (a.k.a. pending).
 *     @type int $spam                The number of spam comments.
 *     @type int $trash               The number of trashed comments.
 *     @type int $post-trashed        The number of comments for posts that are in the trash.
 *     @type int $total_comments      The total number of non-trashed comments, including spam.
 *     @type int $all                 The total number of pending or approved comments.
 * }
 */
function get_comment_count( $post_id = 0 ) {
	global $wpdb;

	$post_id = (int) $post_id;

	$where = '';
	if ( $post_id > 0 ) {
		$where = $wpdb->prepare( 'WHERE comment_post_ID = %d', $post_id );
	}

	$totals = (array) $wpdb->get_results(
		"
		SELECT comment_approved, COUNT( * ) AS total
		FROM {$wpdb->comments}
		{$where}
		GROUP BY comment_approved
	",
		ARRAY_A
	);

	$comment_count = array(
		'approved'            => 0,
		'awaiting_moderation' => 0,
		'spam'                => 0,
		'trash'               => 0,
		'post-trashed'        => 0,
		'total_comments'      => 0,
		'all'                 => 0,
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
				$comment_count['spam']            = $row['total'];
				$comment_count['total_comments'] += $row['total'];
				break;
			case '1':
				$comment_count['approved']        = $row['total'];
				$comment_count['total_comments'] += $row['total'];
				$comment_count['all']            += $row['total'];
				break;
			case '0':
				$comment_count['awaiting_moderation'] = $row['total'];
				$comment_count['total_comments']     += $row['total'];
				$comment_count['all']                += $row['total'];
				break;
			default:
				break;
		}
	}

	return array_map( 'intval', $comment_count );
}

//
// Comment meta functions.
//

/**
 * Add meta data field to a comment.
 *
 * @since 2.9.0
 *
 * @link https://developer.wordpress.org/reference/functions/add_comment_meta/
 *
 * @param int    $comment_id Comment ID.
 * @param string $meta_key   Metadata name.
 * @param mixed  $meta_value Metadata value. Must be serializable if non-scalar.
 * @param bool   $unique     Optional. Whether the same key should not be added.
 *                           Default false.
 * @return int|false Meta ID on success, false on failure.
 */
function add_comment_meta( $comment_id, $meta_key, $meta_value, $unique = false ) {
	return add_metadata( 'comment', $comment_id, $meta_key, $meta_value, $unique );
}

/**
 * Remove metadata matching criteria from a comment.
 *
 * You can match based on the key, or key and value. Removing based on key and
 * value, will keep from removing duplicate metadata with the same key. It also
 * allows removing all metadata matching key, if needed.
 *
 * @since 2.9.0
 *
 * @link https://developer.wordpress.org/reference/functions/delete_comment_meta/
 *
 * @param int    $comment_id Comment ID.
 * @param string $meta_key   Metadata name.
 * @param mixed  $meta_value Optional. Metadata value. If provided,
 *                           rows will only be removed that match the value.
 *                           Must be serializable if non-scalar. Default empty.
 * @return bool True on success, false on failure.
 */
function delete_comment_meta( $comment_id, $meta_key, $meta_value = '' ) {
	return delete_metadata( 'comment', $comment_id, $meta_key, $meta_value );
}

/**
 * Retrieve comment meta field for a comment.
 *
 * @since 2.9.0
 *
 * @link https://developer.wordpress.org/reference/functions/get_comment_meta/
 *
 * @param int    $comment_id Comment ID.
 * @param string $key        Optional. The meta key to retrieve. By default,
 *                           returns data for all keys.
 * @param bool   $single     Optional. Whether to return a single value.
 *                           This parameter has no effect if `$key` is not specified.
 *                           Default false.
 * @return mixed An array of values if `$single` is false.
 *               The value of meta data field if `$single` is true.
 *               False for an invalid `$comment_id` (non-numeric, zero, or negative value).
 *               An empty string if a valid but non-existing comment ID is passed.
 */
function get_comment_meta( $comment_id, $key = '', $single = false ) {
	return get_metadata( 'comment', $comment_id, $key, $single );
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
 *
 * @link https://developer.wordpress.org/reference/functions/update_comment_meta/
 *
 * @param int    $comment_id Comment ID.
 * @param string $meta_key   Metadata key.
 * @param mixed  $meta_value Metadata value. Must be serializable if non-scalar.
 * @param mixed  $prev_value Optional. Previous value to check before updating.
 *                           If specified, only update existing metadata entries with
 *                           this value. Otherwise, update all entries. Default empty.
 * @return int|bool Meta ID if the key didn't exist, true on successful update,
 *                  false on failure or if the value passed to the function
 *                  is the same as the one that is already in the database.
 */
function update_comment_meta( $comment_id, $meta_key, $meta_value, $prev_value = '' ) {
	return update_metadata( 'comment', $comment_id, $meta_key, $meta_value, $prev_value );
}

/**
 * Queues comments for metadata lazy-loading.
 *
 * @since 4.5.0
 *
 * @param WP_Comment[] $comments Array of comment objects.
 */
function wp_queue_comments_for_comment_meta_lazyload( $comments ) {
	// Don't use `wp_list_pluck()` to avoid by-reference manipulation.
	$comment_ids = array();
	if ( is_array( $comments ) ) {
		foreach ( $comments as $comment ) {
			if ( $comment instanceof WP_Comment ) {
				$comment_ids[] = $comment->comment_ID;
			}
		}
	}

	if ( $comment_ids ) {
		$lazyloader = wp_metadata_lazyloader();
		$lazyloader->queue_objects( 'comment', $comment_ids );
	}
}

/**
 * Sets the cookies used to store an unauthenticated commentator's identity. Typically used
 * to recall previous comments by this commentator that are still held in moderation.
 *
 * @since 3.4.0
 * @since 4.9.6 The `$cookies_consent` parameter was added.
 *
 * @param WP_Comment $comment         Comment object.
 * @param WP_User    $user            Comment author's user object. The user may not exist.
 * @param bool       $cookies_consent Optional. Comment author's consent to store cookies. Default true.
 */
function wp_set_comment_cookies( $comment, $user, $cookies_consent = true ) {
	// If the user already exists, or the user opted out of cookies, don't set cookies.
	if ( $user->exists() ) {
		return;
	}

	if ( false === $cookies_consent ) {
		// Remove any existing cookies.
		$past = time() - YEAR_IN_SECONDS;
		setcookie( 'comment_author_' . COOKIEHASH, ' ', $past, COOKIEPATH, COOKIE_DOMAIN );
		setcookie( 'comment_author_email_' . COOKIEHASH, ' ', $past, COOKIEPATH, COOKIE_DOMAIN );
		setcookie( 'comment_author_url_' . COOKIEHASH, ' ', $past, COOKIEPATH, COOKIE_DOMAIN );

		return;
	}

	/**
	 * Filters the lifetime of the comment cookie in seconds.
	 *
	 * @since 2.8.0
	 *
	 * @param int $seconds Comment cookie lifetime. Default 30000000.
	 */
	$comment_cookie_lifetime = time() + apply_filters( 'comment_cookie_lifetime', 30000000 );

	$secure = ( 'https' === parse_url( home_url(), PHP_URL_SCHEME ) );

	setcookie( 'comment_author_' . COOKIEHASH, $comment->comment_author, $comment_cookie_lifetime, COOKIEPATH, COOKIE_DOMAIN, $secure );
	setcookie( 'comment_author_email_' . COOKIEHASH, $comment->comment_author_email, $comment_cookie_lifetime, COOKIEPATH, COOKIE_DOMAIN, $secure );
	setcookie( 'comment_author_url_' . COOKIEHASH, esc_url( $comment->comment_author_url ), $comment_cookie_lifetime, COOKIEPATH, COOKIE_DOMAIN, $secure );
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
	if ( isset( $_COOKIE[ 'comment_author_' . COOKIEHASH ] ) ) {
		/**
		 * Filters the comment author's name cookie before it is set.
		 *
		 * When this filter hook is evaluated in wp_filter_comment(),
		 * the comment author's name string is passed.
		 *
		 * @since 1.5.0
		 *
		 * @param string $author_cookie The comment author name cookie.
		 */
		$comment_author = apply_filters( 'pre_comment_author_name', $_COOKIE[ 'comment_author_' . COOKIEHASH ] );
		$comment_author = wp_unslash( $comment_author );
		$comment_author = esc_attr( $comment_author );

		$_COOKIE[ 'comment_author_' . COOKIEHASH ] = $comment_author;
	}

	if ( isset( $_COOKIE[ 'comment_author_email_' . COOKIEHASH ] ) ) {
		/**
		 * Filters the comment author's email cookie before it is set.
		 *
		 * When this filter hook is evaluated in wp_filter_comment(),
		 * the comment author's email string is passed.
		 *
		 * @since 1.5.0
		 *
		 * @param string $author_email_cookie The comment author email cookie.
		 */
		$comment_author_email = apply_filters( 'pre_comment_author_email', $_COOKIE[ 'comment_author_email_' . COOKIEHASH ] );
		$comment_author_email = wp_unslash( $comment_author_email );
		$comment_author_email = esc_attr( $comment_author_email );

		$_COOKIE[ 'comment_author_email_' . COOKIEHASH ] = $comment_author_email;
	}

	if ( isset( $_COOKIE[ 'comment_author_url_' . COOKIEHASH ] ) ) {
		/**
		 * Filters the comment author's URL cookie before it is set.
		 *
		 * When this filter hook is evaluated in wp_filter_comment(),
		 * the comment author's URL string is passed.
		 *
		 * @since 1.5.0
		 *
		 * @param string $author_url_cookie The comment author URL cookie.
		 */
		$comment_author_url = apply_filters( 'pre_comment_author_url', $_COOKIE[ 'comment_author_url_' . COOKIEHASH ] );
		$comment_author_url = wp_unslash( $comment_author_url );

		$_COOKIE[ 'comment_author_url_' . COOKIEHASH ] = $comment_author_url;
	}
}

/**
 * Validates whether this comment is allowed to be made.
 *
 * @since 2.0.0
 * @since 4.7.0 The `$avoid_die` parameter was added, allowing the function
 *              to return a WP_Error object instead of dying.
 * @since 5.5.0 The `$avoid_die` parameter was renamed to `$wp_error`.
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param array $commentdata Contains information on the comment.
 * @param bool  $wp_error    When true, a disallowed comment will result in the function
 *                           returning a WP_Error object, rather than executing wp_die().
 *                           Default false.
 * @return int|string|WP_Error Allowed comments return the approval status (0|1|'spam'|'trash').
 *                             If `$wp_error` is true, disallowed comments return a WP_Error.
 */
function wp_allow_comment( $commentdata, $wp_error = false ) {
	global $wpdb;

	// Simple duplicate check.
	// expected_slashed ($comment_post_ID, $comment_author, $comment_author_email, $comment_content)
	$dupe = $wpdb->prepare(
		"SELECT comment_ID FROM $wpdb->comments WHERE comment_post_ID = %d AND comment_parent = %s AND comment_approved != 'trash' AND ( comment_author = %s ",
		wp_unslash( $commentdata['comment_post_ID'] ),
		wp_unslash( $commentdata['comment_parent'] ),
		wp_unslash( $commentdata['comment_author'] )
	);
	if ( $commentdata['comment_author_email'] ) {
		$dupe .= $wpdb->prepare(
			'AND comment_author_email = %s ',
			wp_unslash( $commentdata['comment_author_email'] )
		);
	}
	$dupe .= $wpdb->prepare(
		') AND comment_content = %s LIMIT 1',
		wp_unslash( $commentdata['comment_content'] )
	);

	$dupe_id = $wpdb->get_var( $dupe );

	/**
	 * Filters the ID, if any, of the duplicate comment found when creating a new comment.
	 *
	 * Return an empty value from this filter to allow what WP considers a duplicate comment.
	 *
	 * @since 4.4.0
	 *
	 * @param int   $dupe_id     ID of the comment identified as a duplicate.
	 * @param array $commentdata Data for the comment being created.
	 */
	$dupe_id = apply_filters( 'duplicate_comment_id', $dupe_id, $commentdata );

	if ( $dupe_id ) {
		/**
		 * Fires immediately after a duplicate comment is detected.
		 *
		 * @since 3.0.0
		 *
		 * @param array $commentdata Comment data.
		 */
		do_action( 'comment_duplicate_trigger', $commentdata );

		/**
		 * Filters duplicate comment error message.
		 *
		 * @since 5.2.0
		 *
		 * @param string $comment_duplicate_message Duplicate comment error message.
		 */
		$comment_duplicate_message = apply_filters( 'comment_duplicate_message', __( 'Duplicate comment detected; it looks as though you&#8217;ve already said that!' ) );

		if ( $wp_error ) {
			return new WP_Error( 'comment_duplicate', $comment_duplicate_message, 409 );
		} else {
			if ( wp_doing_ajax() ) {
				die( $comment_duplicate_message );
			}

			wp_die( $comment_duplicate_message, 409 );
		}
	}

	/**
	 * Fires immediately before a comment is marked approved.
	 *
	 * Allows checking for comment flooding.
	 *
	 * @since 2.3.0
	 * @since 4.7.0 The `$avoid_die` parameter was added.
	 * @since 5.5.0 The `$avoid_die` parameter was renamed to `$wp_error`.
	 *
	 * @param string $comment_author_IP    Comment author's IP address.
	 * @param string $comment_author_email Comment author's email.
	 * @param string $comment_date_gmt     GMT date the comment was posted.
	 * @param bool   $wp_error             Whether to return a WP_Error object instead of executing
	 *                                     wp_die() or die() if a comment flood is occurring.
	 */
	do_action(
		'check_comment_flood',
		$commentdata['comment_author_IP'],
		$commentdata['comment_author_email'],
		$commentdata['comment_date_gmt'],
		$wp_error
	);

	/**
	 * Filters whether a comment is part of a comment flood.
	 *
	 * The default check is wp_check_comment_flood(). See check_comment_flood_db().
	 *
	 * @since 4.7.0
	 * @since 5.5.0 The `$avoid_die` parameter was renamed to `$wp_error`.
	 *
	 * @param bool   $is_flood             Is a comment flooding occurring? Default false.
	 * @param string $comment_author_IP    Comment author's IP address.
	 * @param string $comment_author_email Comment author's email.
	 * @param string $comment_date_gmt     GMT date the comment was posted.
	 * @param bool   $wp_error             Whether to return a WP_Error object instead of executing
	 *                                     wp_die() or die() if a comment flood is occurring.
	 */
	$is_flood = apply_filters(
		'wp_is_comment_flood',
		false,
		$commentdata['comment_author_IP'],
		$commentdata['comment_author_email'],
		$commentdata['comment_date_gmt'],
		$wp_error
	);

	if ( $is_flood ) {
		/** This filter is documented in wp-includes/comment-template.php */
		$comment_flood_message = apply_filters( 'comment_flood_message', __( 'You are posting comments too quickly. Slow down.' ) );

		return new WP_Error( 'comment_flood', $comment_flood_message, 429 );
	}

	if ( ! empty( $commentdata['user_id'] ) ) {
		$user        = get_userdata( $commentdata['user_id'] );
		$post_author = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT post_author FROM $wpdb->posts WHERE ID = %d LIMIT 1",
				$commentdata['comment_post_ID']
			)
		);
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

		if ( wp_check_comment_disallowed_list(
			$commentdata['comment_author'],
			$commentdata['comment_author_email'],
			$commentdata['comment_author_url'],
			$commentdata['comment_content'],
			$commentdata['comment_author_IP'],
			$commentdata['comment_agent']
		) ) {
			$approved = EMPTY_TRASH_DAYS ? 'trash' : 'spam';
		}
	}

	/**
	 * Filters a comment's approval status before it is set.
	 *
	 * @since 2.1.0
	 * @since 4.9.0 Returning a WP_Error value from the filter will short-circuit comment insertion
	 *              and allow skipping further processing.
	 *
	 * @param int|string|WP_Error $approved    The approval status. Accepts 1, 0, 'spam', 'trash',
	 *                                         or WP_Error.
	 * @param array               $commentdata Comment data.
	 */
	return apply_filters( 'pre_comment_approved', $approved, $commentdata );
}

/**
 * Hooks WP's native database-based comment-flood check.
 *
 * This wrapper maintains backward compatibility with plugins that expect to
 * be able to unhook the legacy check_comment_flood_db() function from
 * 'check_comment_flood' using remove_action().
 *
 * @since 2.3.0
 * @since 4.7.0 Converted to be an add_filter() wrapper.
 */
function check_comment_flood_db() {
	add_filter( 'wp_is_comment_flood', 'wp_check_comment_flood', 10, 5 );
}

/**
 * Checks whether comment flooding is occurring.
 *
 * Won't run, if current user can manage options, so to not block
 * administrators.
 *
 * @since 4.7.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param bool   $is_flood  Is a comment flooding occurring?
 * @param string $ip        Comment author's IP address.
 * @param string $email     Comment author's email address.
 * @param string $date      MySQL time string.
 * @param bool   $avoid_die When true, a disallowed comment will result in the function
 *                          returning without executing wp_die() or die(). Default false.
 * @return bool Whether comment flooding is occurring.
 */
function wp_check_comment_flood( $is_flood, $ip, $email, $date, $avoid_die = false ) {

	global $wpdb;

	// Another callback has declared a flood. Trust it.
	if ( true === $is_flood ) {
		return $is_flood;
	}

	// Don't throttle admins or moderators.
	if ( current_user_can( 'manage_options' ) || current_user_can( 'moderate_comments' ) ) {
		return false;
	}

	$hour_ago = gmdate( 'Y-m-d H:i:s', time() - HOUR_IN_SECONDS );

	if ( is_user_logged_in() ) {
		$user         = get_current_user_id();
		$check_column = '`user_id`';
	} else {
		$user         = $ip;
		$check_column = '`comment_author_IP`';
	}

	$sql = $wpdb->prepare(
		"SELECT `comment_date_gmt` FROM `$wpdb->comments` WHERE `comment_date_gmt` >= %s AND ( $check_column = %s OR `comment_author_email` = %s ) ORDER BY `comment_date_gmt` DESC LIMIT 1",
		$hour_ago,
		$user,
		$email
	);

	$lasttime = $wpdb->get_var( $sql );

	if ( $lasttime ) {
		$time_lastcomment = mysql2date( 'U', $lasttime, false );
		$time_newcomment  = mysql2date( 'U', $date, false );

		/**
		 * Filters the comment flood status.
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

			if ( $avoid_die ) {
				return true;
			} else {
				/**
				 * Filters the comment flood error message.
				 *
				 * @since 5.2.0
				 *
				 * @param string $comment_flood_message Comment flood error message.
				 */
				$comment_flood_message = apply_filters( 'comment_flood_message', __( 'You are posting comments too quickly. Slow down.' ) );

				if ( wp_doing_ajax() ) {
					die( $comment_flood_message );
				}

				wp_die( $comment_flood_message, 429 );
			}
		}
	}

	return false;
}

/**
 * Separates an array of comments into an array keyed by comment_type.
 *
 * @since 2.7.0
 *
 * @param WP_Comment[] $comments Array of comments
 * @return WP_Comment[] Array of comments keyed by comment_type.
 */
function separate_comments( &$comments ) {
	$comments_by_type = array(
		'comment'   => array(),
		'trackback' => array(),
		'pingback'  => array(),
		'pings'     => array(),
	);

	$count = count( $comments );

	for ( $i = 0; $i < $count; $i++ ) {
		$type = $comments[ $i ]->comment_type;

		if ( empty( $type ) ) {
			$type = 'comment';
		}

		$comments_by_type[ $type ][] = &$comments[ $i ];

		if ( 'trackback' === $type || 'pingback' === $type ) {
			$comments_by_type['pings'][] = &$comments[ $i ];
		}
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
 * @global WP_Query $wp_query WordPress Query object.
 *
 * @param WP_Comment[] $comments Optional. Array of WP_Comment objects. Defaults to `$wp_query->comments`.
 * @param int          $per_page Optional. Comments per page.
 * @param bool         $threaded Optional. Control over flat or threaded comments.
 * @return int Number of comment pages.
 */
function get_comment_pages_count( $comments = null, $per_page = null, $threaded = null ) {
	global $wp_query;

	if ( null === $comments && null === $per_page && null === $threaded && ! empty( $wp_query->max_num_comment_pages ) ) {
		return $wp_query->max_num_comment_pages;
	}

	if ( ( ! $comments || ! is_array( $comments ) ) && ! empty( $wp_query->comments ) ) {
		$comments = $wp_query->comments;
	}

	if ( empty( $comments ) ) {
		return 0;
	}

	if ( ! get_option( 'page_comments' ) ) {
		return 1;
	}

	if ( ! isset( $per_page ) ) {
		$per_page = (int) get_query_var( 'comments_per_page' );
	}
	if ( 0 === $per_page ) {
		$per_page = (int) get_option( 'comments_per_page' );
	}
	if ( 0 === $per_page ) {
		return 1;
	}

	if ( ! isset( $threaded ) ) {
		$threaded = get_option( 'thread_comments' );
	}

	if ( $threaded ) {
		$walker = new Walker_Comment;
		$count  = ceil( $walker->get_number_of_root_elements( $comments ) / $per_page );
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
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param int   $comment_ID Comment ID.
 * @param array $args {
 *     Array of optional arguments.
 *
 *     @type string     $type      Limit paginated comments to those matching a given type.
 *                                 Accepts 'comment', 'trackback', 'pingback', 'pings'
 *                                 (trackbacks and pingbacks), or 'all'. Default 'all'.
 *     @type int        $per_page  Per-page count to use when calculating pagination.
 *                                 Defaults to the value of the 'comments_per_page' option.
 *     @type int|string $max_depth If greater than 1, comment page will be determined
 *                                 for the top-level parent `$comment_ID`.
 *                                 Defaults to the value of the 'thread_comments_depth' option.
 * } *
 * @return int|null Comment page number or null on error.
 */
function get_page_of_comment( $comment_ID, $args = array() ) {
	global $wpdb;

	$page = null;

	$comment = get_comment( $comment_ID );
	if ( ! $comment ) {
		return;
	}

	$defaults      = array(
		'type'      => 'all',
		'page'      => '',
		'per_page'  => '',
		'max_depth' => '',
	);
	$args          = wp_parse_args( $args, $defaults );
	$original_args = $args;

	// Order of precedence: 1. `$args['per_page']`, 2. 'comments_per_page' query_var, 3. 'comments_per_page' option.
	if ( get_option( 'page_comments' ) ) {
		if ( '' === $args['per_page'] ) {
			$args['per_page'] = get_query_var( 'comments_per_page' );
		}

		if ( '' === $args['per_page'] ) {
			$args['per_page'] = get_option( 'comments_per_page' );
		}
	}

	if ( empty( $args['per_page'] ) ) {
		$args['per_page'] = 0;
		$args['page']     = 0;
	}

	if ( $args['per_page'] < 1 ) {
		$page = 1;
	}

	if ( null === $page ) {
		if ( '' === $args['max_depth'] ) {
			if ( get_option( 'thread_comments' ) ) {
				$args['max_depth'] = get_option( 'thread_comments_depth' );
			} else {
				$args['max_depth'] = -1;
			}
		}

		// Find this comment's top-level parent if threading is enabled.
		if ( $args['max_depth'] > 1 && 0 != $comment->comment_parent ) {
			return get_page_of_comment( $comment->comment_parent, $args );
		}

		$comment_args = array(
			'type'       => $args['type'],
			'post_id'    => $comment->comment_post_ID,
			'fields'     => 'ids',
			'count'      => true,
			'status'     => 'approve',
			'parent'     => 0,
			'date_query' => array(
				array(
					'column' => "$wpdb->comments.comment_date_gmt",
					'before' => $comment->comment_date_gmt,
				),
			),
		);

		if ( is_user_logged_in() ) {
			$comment_args['include_unapproved'] = array( get_current_user_id() );
		} else {
			$unapproved_email = wp_get_unapproved_comment_author_email();

			if ( $unapproved_email ) {
				$comment_args['include_unapproved'] = array( $unapproved_email );
			}
		}

		/**
		 * Filters the arguments used to query comments in get_page_of_comment().
		 *
		 * @since 5.5.0
		 *
		 * @see WP_Comment_Query::__construct()
		 *
		 * @param array $comment_args {
		 *     Array of WP_Comment_Query arguments.
		 *
		 *     @type string $type               Limit paginated comments to those matching a given type.
		 *                                      Accepts 'comment', 'trackback', 'pingback', 'pings'
		 *                                      (trackbacks and pingbacks), or 'all'. Default 'all'.
		 *     @type int    $post_id            ID of the post.
		 *     @type string $fields             Comment fields to return.
		 *     @type bool   $count              Whether to return a comment count (true) or array
		 *                                      of comment objects (false).
		 *     @type string $status             Comment status.
		 *     @type int    $parent             Parent ID of comment to retrieve children of.
		 *     @type array  $date_query         Date query clauses to limit comments by. See WP_Date_Query.
		 *     @type array  $include_unapproved Array of IDs or email addresses whose unapproved comments
		 *                                      will be included in paginated comments.
		 * }
		 */
		$comment_args = apply_filters( 'get_page_of_comment_query_args', $comment_args );

		$comment_query       = new WP_Comment_Query();
		$older_comment_count = $comment_query->query( $comment_args );

		// No older comments? Then it's page #1.
		if ( 0 == $older_comment_count ) {
			$page = 1;

			// Divide comments older than this one by comments per page to get this comment's page number.
		} else {
			$page = ceil( ( $older_comment_count + 1 ) / $args['per_page'] );
		}
	}

	/**
	 * Filters the calculated page on which a comment appears.
	 *
	 * @since 4.4.0
	 * @since 4.7.0 Introduced the `$comment_ID` parameter.
	 *
	 * @param int   $page          Comment page.
	 * @param array $args {
	 *     Arguments used to calculate pagination. These include arguments auto-detected by the function,
	 *     based on query vars, system settings, etc. For pristine arguments passed to the function,
	 *     see `$original_args`.
	 *
	 *     @type string $type      Type of comments to count.
	 *     @type int    $page      Calculated current page.
	 *     @type int    $per_page  Calculated number of comments per page.
	 *     @type int    $max_depth Maximum comment threading depth allowed.
	 * }
	 * @param array $original_args {
	 *     Array of arguments passed to the function. Some or all of these may not be set.
	 *
	 *     @type string $type      Type of comments to count.
	 *     @type int    $page      Current comment page.
	 *     @type int    $per_page  Number of comments per page.
	 *     @type int    $max_depth Maximum comment threading depth allowed.
	 * }
	 * @param int $comment_ID ID of the comment.
	 */
	return apply_filters( 'get_page_of_comment', (int) $page, $args, $original_args, $comment_ID );
}

/**
 * Retrieves the maximum character lengths for the comment form fields.
 *
 * @since 4.5.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @return int[] Array of maximum lengths keyed by field name.
 */
function wp_get_comment_fields_max_lengths() {
	global $wpdb;

	$lengths = array(
		'comment_author'       => 245,
		'comment_author_email' => 100,
		'comment_author_url'   => 200,
		'comment_content'      => 65525,
	);

	if ( $wpdb->is_mysql ) {
		foreach ( $lengths as $column => $length ) {
			$col_length = $wpdb->get_col_length( $wpdb->comments, $column );
			$max_length = 0;

			// No point if we can't get the DB column lengths.
			if ( is_wp_error( $col_length ) ) {
				break;
			}

			if ( ! is_array( $col_length ) && (int) $col_length > 0 ) {
				$max_length = (int) $col_length;
			} elseif ( is_array( $col_length ) && isset( $col_length['length'] ) && (int) $col_length['length'] > 0 ) {
				$max_length = (int) $col_length['length'];

				if ( ! empty( $col_length['type'] ) && 'byte' === $col_length['type'] ) {
					$max_length = $max_length - 10;
				}
			}

			if ( $max_length > 0 ) {
				$lengths[ $column ] = $max_length;
			}
		}
	}

	/**
	 * Filters the lengths for the comment form fields.
	 *
	 * @since 4.5.0
	 *
	 * @param int[] $lengths Array of maximum lengths keyed by field name.
	 */
	return apply_filters( 'wp_get_comment_fields_max_lengths', $lengths );
}

/**
 * Compares the lengths of comment data against the maximum character limits.
 *
 * @since 4.7.0
 *
 * @param array $comment_data Array of arguments for inserting a comment.
 * @return WP_Error|true WP_Error when a comment field exceeds the limit,
 *                       otherwise true.
 */
function wp_check_comment_data_max_lengths( $comment_data ) {
	$max_lengths = wp_get_comment_fields_max_lengths();

	if ( isset( $comment_data['comment_author'] ) && mb_strlen( $comment_data['comment_author'], '8bit' ) > $max_lengths['comment_author'] ) {
		return new WP_Error( 'comment_author_column_length', __( '<strong>Error</strong>: Your name is too long.' ), 200 );
	}

	if ( isset( $comment_data['comment_author_email'] ) && strlen( $comment_data['comment_author_email'] ) > $max_lengths['comment_author_email'] ) {
		return new WP_Error( 'comment_author_email_column_length', __( '<strong>Error</strong>: Your email address is too long.' ), 200 );
	}

	if ( isset( $comment_data['comment_author_url'] ) && strlen( $comment_data['comment_author_url'] ) > $max_lengths['comment_author_url'] ) {
		return new WP_Error( 'comment_author_url_column_length', __( '<strong>Error</strong>: Your URL is too long.' ), 200 );
	}

	if ( isset( $comment_data['comment_content'] ) && mb_strlen( $comment_data['comment_content'], '8bit' ) > $max_lengths['comment_content'] ) {
		return new WP_Error( 'comment_content_column_length', __( '<strong>Error</strong>: Your comment is too long.' ), 200 );
	}

	return true;
}

/**
 * Checks if a comment contains disallowed characters or words.
 *
 * @since 5.5.0
 *
 * @param string $author The author of the comment
 * @param string $email The email of the comment
 * @param string $url The url used in the comment
 * @param string $comment The comment content
 * @param string $user_ip The comment author's IP address
 * @param string $user_agent The author's browser user agent
 * @return bool True if comment contains disallowed content, false if comment does not
 */
function wp_check_comment_disallowed_list( $author, $email, $url, $comment, $user_ip, $user_agent ) {
	/**
	 * Fires before the comment is tested for disallowed characters or words.
	 *
	 * @since 1.5.0
	 * @deprecated 5.5.0 Use {@see 'wp_check_comment_disallowed_list'} instead.
	 *
	 * @param string $author     Comment author.
	 * @param string $email      Comment author's email.
	 * @param string $url        Comment author's URL.
	 * @param string $comment    Comment content.
	 * @param string $user_ip    Comment author's IP address.
	 * @param string $user_agent Comment author's browser user agent.
	 */
	do_action_deprecated(
		'wp_blacklist_check',
		array( $author, $email, $url, $comment, $user_ip, $user_agent ),
		'5.5.0',
		'wp_check_comment_disallowed_list',
		__( 'Please consider writing more inclusive code.' )
	);

	/**
	 * Fires before the comment is tested for disallowed characters or words.
	 *
	 * @since 5.5.0
	 *
	 * @param string $author     Comment author.
	 * @param string $email      Comment author's email.
	 * @param string $url        Comment author's URL.
	 * @param string $comment    Comment content.
	 * @param string $user_ip    Comment author's IP address.
	 * @param string $user_agent Comment author's browser user agent.
	 */
	do_action( 'wp_check_comment_disallowed_list', $author, $email, $url, $comment, $user_ip, $user_agent );

	$mod_keys = trim( get_option( 'disallowed_keys' ) );
	if ( '' === $mod_keys ) {
		return false; // If moderation keys are empty.
	}

	// Ensure HTML tags are not being used to bypass the list of disallowed characters and words.
	$comment_without_html = wp_strip_all_tags( $comment );

	$words = explode( "\n", $mod_keys );

	foreach ( (array) $words as $word ) {
		$word = trim( $word );

		// Skip empty lines.
		if ( empty( $word ) ) {
			continue; }

		// Do some escaping magic so that '#' chars
		// in the spam words don't break things:
		$word = preg_quote( $word, '#' );

		$pattern = "#$word#i";
		if ( preg_match( $pattern, $author )
			|| preg_match( $pattern, $email )
			|| preg_match( $pattern, $url )
			|| preg_match( $pattern, $comment )
			|| preg_match( $pattern, $comment_without_html )
			|| preg_match( $pattern, $user_ip )
			|| preg_match( $pattern, $user_agent )
		) {
			return true;
		}
	}
	return false;
}

/**
 * Retrieves the total comment counts for the whole site or a single post.
 *
 * The comment stats are cached and then retrieved, if they already exist in the
 * cache.
 *
 * @see get_comment_count() Which handles fetching the live comment counts.
 *
 * @since 2.5.0
 *
 * @param int $post_id Optional. Restrict the comment counts to the given post. Default 0, which indicates that
 *                     comment counts for the whole site will be retrieved.
 * @return stdClass {
 *     The number of comments keyed by their status.
 *
 *     @type int $approved       The number of approved comments.
 *     @type int $moderated      The number of comments awaiting moderation (a.k.a. pending).
 *     @type int $spam           The number of spam comments.
 *     @type int $trash          The number of trashed comments.
 *     @type int $post-trashed   The number of comments for posts that are in the trash.
 *     @type int $total_comments The total number of non-trashed comments, including spam.
 *     @type int $all            The total number of pending or approved comments.
 * }
 */
function wp_count_comments( $post_id = 0 ) {
	$post_id = (int) $post_id;

	/**
	 * Filters the comments count for a given post or the whole site.
	 *
	 * @since 2.7.0
	 *
	 * @param array|stdClass $count   An empty array or an object containing comment counts.
	 * @param int            $post_id The post ID. Can be 0 to represent the whole site.
	 */
	$filtered = apply_filters( 'wp_count_comments', array(), $post_id );
	if ( ! empty( $filtered ) ) {
		return $filtered;
	}

	$count = wp_cache_get( "comments-{$post_id}", 'counts' );
	if ( false !== $count ) {
		return $count;
	}

	$stats              = get_comment_count( $post_id );
	$stats['moderated'] = $stats['awaiting_moderation'];
	unset( $stats['awaiting_moderation'] );

	$stats_object = (object) $stats;
	wp_cache_set( "comments-{$post_id}", $stats_object, 'counts' );

	return $stats_object;
}

/**
 * Trashes or deletes a comment.
 *
 * The comment is moved to Trash instead of permanently deleted unless Trash is
 * disabled, item is already in the Trash, or $force_delete is true.
 *
 * The post comment count will be updated if the comment was approved and has a
 * post ID available.
 *
 * @since 2.0.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param int|WP_Comment $comment_id   Comment ID or WP_Comment object.
 * @param bool           $force_delete Whether to bypass Trash and force deletion. Default false.
 * @return bool True on success, false on failure.
 */
function wp_delete_comment( $comment_id, $force_delete = false ) {
	global $wpdb;
	$comment = get_comment( $comment_id );
	if ( ! $comment ) {
		return false;
	}

	if ( ! $force_delete && EMPTY_TRASH_DAYS && ! in_array( wp_get_comment_status( $comment ), array( 'trash', 'spam' ), true ) ) {
		return wp_trash_comment( $comment_id );
	}

	/**
	 * Fires immediately before a comment is deleted from the database.
	 *
	 * @since 1.2.0
	 * @since 4.9.0 Added the `$comment` parameter.
	 *
	 * @param int        $comment_id The comment ID.
	 * @param WP_Comment $comment    The comment to be deleted.
	 */
	do_action( 'delete_comment', $comment->comment_ID, $comment );

	// Move children up a level.
	$children = $wpdb->get_col( $wpdb->prepare( "SELECT comment_ID FROM $wpdb->comments WHERE comment_parent = %d", $comment->comment_ID ) );
	if ( ! empty( $children ) ) {
		$wpdb->update( $wpdb->comments, array( 'comment_parent' => $comment->comment_parent ), array( 'comment_parent' => $comment->comment_ID ) );
		clean_comment_cache( $children );
	}

	// Delete metadata.
	$meta_ids = $wpdb->get_col( $wpdb->prepare( "SELECT meta_id FROM $wpdb->commentmeta WHERE comment_id = %d", $comment->comment_ID ) );
	foreach ( $meta_ids as $mid ) {
		delete_metadata_by_mid( 'comment', $mid );
	}

	if ( ! $wpdb->delete( $wpdb->comments, array( 'comment_ID' => $comment->comment_ID ) ) ) {
		return false;
	}

	/**
	 * Fires immediately after a comment is deleted from the database.
	 *
	 * @since 2.9.0
	 * @since 4.9.0 Added the `$comment` parameter.
	 *
	 * @param int        $comment_id The comment ID.
	 * @param WP_Comment $comment    The deleted comment.
	 */
	do_action( 'deleted_comment', $comment->comment_ID, $comment );

	$post_id = $comment->comment_post_ID;
	if ( $post_id && 1 == $comment->comment_approved ) {
		wp_update_comment_count( $post_id );
	}

	clean_comment_cache( $comment->comment_ID );

	/** This action is documented in wp-includes/comment.php */
	do_action( 'wp_set_comment_status', $comment->comment_ID, 'delete' );

	wp_transition_comment_status( 'delete', $comment->comment_approved, $comment );

	return true;
}

/**
 * Moves a comment to the Trash
 *
 * If Trash is disabled, comment is permanently deleted.
 *
 * @since 2.9.0
 *
 * @param int|WP_Comment $comment_id Comment ID or WP_Comment object.
 * @return bool True on success, false on failure.
 */
function wp_trash_comment( $comment_id ) {
	if ( ! EMPTY_TRASH_DAYS ) {
		return wp_delete_comment( $comment_id, true );
	}

	$comment = get_comment( $comment_id );
	if ( ! $comment ) {
		return false;
	}

	/**
	 * Fires immediately before a comment is sent to the Trash.
	 *
	 * @since 2.9.0
	 * @since 4.9.0 Added the `$comment` parameter.
	 *
	 * @param int        $comment_id The comment ID.
	 * @param WP_Comment $comment    The comment to be trashed.
	 */
	do_action( 'trash_comment', $comment->comment_ID, $comment );

	if ( wp_set_comment_status( $comment, 'trash' ) ) {
		delete_comment_meta( $comment->comment_ID, '_wp_trash_meta_status' );
		delete_comment_meta( $comment->comment_ID, '_wp_trash_meta_time' );
		add_comment_meta( $comment->comment_ID, '_wp_trash_meta_status', $comment->comment_approved );
		add_comment_meta( $comment->comment_ID, '_wp_trash_meta_time', time() );

		/**
		 * Fires immediately after a comment is sent to Trash.
		 *
		 * @since 2.9.0
		 * @since 4.9.0 Added the `$comment` parameter.
		 *
		 * @param int        $comment_id The comment ID.
		 * @param WP_Comment $comment    The trashed comment.
		 */
		do_action( 'trashed_comment', $comment->comment_ID, $comment );

		return true;
	}

	return false;
}

/**
 * Removes a comment from the Trash
 *
 * @since 2.9.0
 *
 * @param int|WP_Comment $comment_id Comment ID or WP_Comment object.
 * @return bool True on success, false on failure.
 */
function wp_untrash_comment( $comment_id ) {
	$comment = get_comment( $comment_id );
	if ( ! $comment ) {
		return false;
	}

	/**
	 * Fires immediately before a comment is restored from the Trash.
	 *
	 * @since 2.9.0
	 * @since 4.9.0 Added the `$comment` parameter.
	 *
	 * @param int        $comment_id The comment ID.
	 * @param WP_Comment $comment    The comment to be untrashed.
	 */
	do_action( 'untrash_comment', $comment->comment_ID, $comment );

	$status = (string) get_comment_meta( $comment->comment_ID, '_wp_trash_meta_status', true );
	if ( empty( $status ) ) {
		$status = '0';
	}

	if ( wp_set_comment_status( $comment, $status ) ) {
		delete_comment_meta( $comment->comment_ID, '_wp_trash_meta_time' );
		delete_comment_meta( $comment->comment_ID, '_wp_trash_meta_status' );

		/**
		 * Fires immediately after a comment is restored from the Trash.
		 *
		 * @since 2.9.0
		 * @since 4.9.0 Added the `$comment` parameter.
		 *
		 * @param int        $comment_id The comment ID.
		 * @param WP_Comment $comment    The untrashed comment.
		 */
		do_action( 'untrashed_comment', $comment->comment_ID, $comment );

		return true;
	}

	return false;
}

/**
 * Marks a comment as Spam
 *
 * @since 2.9.0
 *
 * @param int|WP_Comment $comment_id Comment ID or WP_Comment object.
 * @return bool True on success, false on failure.
 */
function wp_spam_comment( $comment_id ) {
	$comment = get_comment( $comment_id );
	if ( ! $comment ) {
		return false;
	}

	/**
	 * Fires immediately before a comment is marked as Spam.
	 *
	 * @since 2.9.0
	 * @since 4.9.0 Added the `$comment` parameter.
	 *
	 * @param int        $comment_id The comment ID.
	 * @param WP_Comment $comment    The comment to be marked as spam.
	 */
	do_action( 'spam_comment', $comment->comment_ID, $comment );

	if ( wp_set_comment_status( $comment, 'spam' ) ) {
		delete_comment_meta( $comment->comment_ID, '_wp_trash_meta_status' );
		delete_comment_meta( $comment->comment_ID, '_wp_trash_meta_time' );
		add_comment_meta( $comment->comment_ID, '_wp_trash_meta_status', $comment->comment_approved );
		add_comment_meta( $comment->comment_ID, '_wp_trash_meta_time', time() );

		/**
		 * Fires immediately after a comment is marked as Spam.
		 *
		 * @since 2.9.0
		 * @since 4.9.0 Added the `$comment` parameter.
		 *
		 * @param int        $comment_id The comment ID.
		 * @param WP_Comment $comment    The comment marked as spam.
		 */
		do_action( 'spammed_comment', $comment->comment_ID, $comment );

		return true;
	}

	return false;
}

/**
 * Removes a comment from the Spam
 *
 * @since 2.9.0
 *
 * @param int|WP_Comment $comment_id Comment ID or WP_Comment object.
 * @return bool True on success, false on failure.
 */
function wp_unspam_comment( $comment_id ) {
	$comment = get_comment( $comment_id );
	if ( ! $comment ) {
		return false;
	}

	/**
	 * Fires immediately before a comment is unmarked as Spam.
	 *
	 * @since 2.9.0
	 * @since 4.9.0 Added the `$comment` parameter.
	 *
	 * @param int        $comment_id The comment ID.
	 * @param WP_Comment $comment    The comment to be unmarked as spam.
	 */
	do_action( 'unspam_comment', $comment->comment_ID, $comment );

	$status = (string) get_comment_meta( $comment->comment_ID, '_wp_trash_meta_status', true );
	if ( empty( $status ) ) {
		$status = '0';
	}

	if ( wp_set_comment_status( $comment, $status ) ) {
		delete_comment_meta( $comment->comment_ID, '_wp_trash_meta_status' );
		delete_comment_meta( $comment->comment_ID, '_wp_trash_meta_time' );

		/**
		 * Fires immediately after a comment is unmarked as Spam.
		 *
		 * @since 2.9.0
		 * @since 4.9.0 Added the `$comment` parameter.
		 *
		 * @param int        $comment_id The comment ID.
		 * @param WP_Comment $comment    The comment unmarked as spam.
		 */
		do_action( 'unspammed_comment', $comment->comment_ID, $comment );

		return true;
	}

	return false;
}

/**
 * The status of a comment by ID.
 *
 * @since 1.0.0
 *
 * @param int|WP_Comment $comment_id Comment ID or WP_Comment object
 * @return string|false Status might be 'trash', 'approved', 'unapproved', 'spam'. False on failure.
 */
function wp_get_comment_status( $comment_id ) {
	$comment = get_comment( $comment_id );
	if ( ! $comment ) {
		return false;
	}

	$approved = $comment->comment_approved;

	if ( null == $approved ) {
		return false;
	} elseif ( '1' == $approved ) {
		return 'approved';
	} elseif ( '0' == $approved ) {
		return 'unapproved';
	} elseif ( 'spam' === $approved ) {
		return 'spam';
	} elseif ( 'trash' === $approved ) {
		return 'trash';
	} else {
		return false;
	}
}

/**
 * Call hooks for when a comment status transition occurs.
 *
 * Calls hooks for comment status transitions. If the new comment status is not the same
 * as the previous comment status, then two hooks will be ran, the first is
 * {@see 'transition_comment_status'} with new status, old status, and comment data.
 * The next action called is {@see 'comment_$old_status_to_$new_status'}. It has
 * the comment data.
 *
 * The final action will run whether or not the comment statuses are the same.
 * The action is named {@see 'comment_$new_status_$comment->comment_type'}.
 *
 * @since 2.7.0
 *
 * @param string     $new_status New comment status.
 * @param string     $old_status Previous comment status.
 * @param WP_Comment $comment    Comment object.
 */
function wp_transition_comment_status( $new_status, $old_status, $comment ) {
	/*
	 * Translate raw statuses to human-readable formats for the hooks.
	 * This is not a complete list of comment status, it's only the ones
	 * that need to be renamed.
	 */
	$comment_statuses = array(
		0         => 'unapproved',
		'hold'    => 'unapproved', // wp_set_comment_status() uses "hold".
		1         => 'approved',
		'approve' => 'approved',   // wp_set_comment_status() uses "approve".
	);
	if ( isset( $comment_statuses[ $new_status ] ) ) {
		$new_status = $comment_statuses[ $new_status ];
	}
	if ( isset( $comment_statuses[ $old_status ] ) ) {
		$old_status = $comment_statuses[ $old_status ];
	}

	// Call the hooks.
	if ( $new_status != $old_status ) {
		/**
		 * Fires when the comment status is in transition.
		 *
		 * @since 2.7.0
		 *
		 * @param int|string $new_status The new comment status.
		 * @param int|string $old_status The old comment status.
		 * @param WP_Comment $comment    Comment object.
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
 * Clear the lastcommentmodified cached value when a comment status is changed.
 *
 * Deletes the lastcommentmodified cache key when a comment enters or leaves
 * 'approved' status.
 *
 * @since 4.7.0
 * @access private
 *
 * @param string $new_status The new comment status.
 * @param string $old_status The old comment status.
 */
function _clear_modified_cache_on_transition_comment_status( $new_status, $old_status ) {
	if ( 'approved' === $new_status || 'approved' === $old_status ) {
		foreach ( array( 'server', 'gmt', 'blog' ) as $timezone ) {
			wp_cache_delete( "lastcommentmodified:$timezone", 'timeinfo' );
		}
	}
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
 * @return array {
 *     An array of current commenter variables.
 *
 *     @type string $comment_author       The name of the current commenter, or an empty string.
 *     @type string $comment_author_email The email address of the current commenter, or an empty string.
 *     @type string $comment_author_url   The URL address of the current commenter, or an empty string.
 * }
 */
function wp_get_current_commenter() {
	// Cookies should already be sanitized.

	$comment_author = '';
	if ( isset( $_COOKIE[ 'comment_author_' . COOKIEHASH ] ) ) {
		$comment_author = $_COOKIE[ 'comment_author_' . COOKIEHASH ];
	}

	$comment_author_email = '';
	if ( isset( $_COOKIE[ 'comment_author_email_' . COOKIEHASH ] ) ) {
		$comment_author_email = $_COOKIE[ 'comment_author_email_' . COOKIEHASH ];
	}

	$comment_author_url = '';
	if ( isset( $_COOKIE[ 'comment_author_url_' . COOKIEHASH ] ) ) {
		$comment_author_url = $_COOKIE[ 'comment_author_url_' . COOKIEHASH ];
	}

	/**
	 * Filters the current commenter's name, email, and URL.
	 *
	 * @since 3.1.0
	 *
	 * @param array $comment_author_data {
	 *     An array of current commenter variables.
	 *
	 *     @type string $comment_author       The name of the current commenter, or an empty string.
	 *     @type string $comment_author_email The email address of the current commenter, or an empty string.
	 *     @type string $comment_author_url   The URL address of the current commenter, or an empty string.
	 * }
	 */
	return apply_filters( 'wp_get_current_commenter', compact( 'comment_author', 'comment_author_email', 'comment_author_url' ) );
}

/**
 * Get unapproved comment author's email.
 *
 * Used to allow the commenter to see their pending comment.
 *
 * @since 5.1.0
 * @since 5.7.0 The window within which the author email for an unapproved comment
 *              can be retrieved was extended to 10 minutes.
 *
 * @return string The unapproved comment author's email (when supplied).
 */
function wp_get_unapproved_comment_author_email() {
	$commenter_email = '';

	if ( ! empty( $_GET['unapproved'] ) && ! empty( $_GET['moderation-hash'] ) ) {
		$comment_id = (int) $_GET['unapproved'];
		$comment    = get_comment( $comment_id );

		if ( $comment && hash_equals( $_GET['moderation-hash'], wp_hash( $comment->comment_date_gmt ) ) ) {
			// The comment will only be viewable by the comment author for 10 minutes.
			$comment_preview_expires = strtotime( $comment->comment_date_gmt . '+10 minutes' );

			if ( time() < $comment_preview_expires ) {
				$commenter_email = $comment->comment_author_email;
			}
		}
	}

	if ( ! $commenter_email ) {
		$commenter       = wp_get_current_commenter();
		$commenter_email = $commenter['comment_author_email'];
	}

	return $commenter_email;
}

/**
 * Inserts a comment into the database.
 *
 * @since 2.0.0
 * @since 4.4.0 Introduced the `$comment_meta` argument.
 * @since 5.5.0 Default value for `$comment_type` argument changed to `comment`.
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
 *                                            Default 0.
 *     @type string     $comment_type         Comment type. Default 'comment'.
 *     @type array      $comment_meta         Optional. Array of key/value pairs to be stored in commentmeta for the
 *                                            new comment.
 *     @type int        $user_id              ID of the user who submitted the comment. Default 0.
 * }
 * @return int|false The new comment's ID on success, false on failure.
 */
function wp_insert_comment( $commentdata ) {
	global $wpdb;
	$data = wp_unslash( $commentdata );

	$comment_author       = ! isset( $data['comment_author'] ) ? '' : $data['comment_author'];
	$comment_author_email = ! isset( $data['comment_author_email'] ) ? '' : $data['comment_author_email'];
	$comment_author_url   = ! isset( $data['comment_author_url'] ) ? '' : $data['comment_author_url'];
	$comment_author_IP    = ! isset( $data['comment_author_IP'] ) ? '' : $data['comment_author_IP'];

	$comment_date     = ! isset( $data['comment_date'] ) ? current_time( 'mysql' ) : $data['comment_date'];
	$comment_date_gmt = ! isset( $data['comment_date_gmt'] ) ? get_gmt_from_date( $comment_date ) : $data['comment_date_gmt'];

	$comment_post_ID  = ! isset( $data['comment_post_ID'] ) ? 0 : $data['comment_post_ID'];
	$comment_content  = ! isset( $data['comment_content'] ) ? '' : $data['comment_content'];
	$comment_karma    = ! isset( $data['comment_karma'] ) ? 0 : $data['comment_karma'];
	$comment_approved = ! isset( $data['comment_approved'] ) ? 1 : $data['comment_approved'];
	$comment_agent    = ! isset( $data['comment_agent'] ) ? '' : $data['comment_agent'];
	$comment_type     = empty( $data['comment_type'] ) ? 'comment' : $data['comment_type'];
	$comment_parent   = ! isset( $data['comment_parent'] ) ? 0 : $data['comment_parent'];

	$user_id = ! isset( $data['user_id'] ) ? 0 : $data['user_id'];

	$compacted = compact( 'comment_post_ID', 'comment_author', 'comment_author_email', 'comment_author_url', 'comment_author_IP', 'comment_date', 'comment_date_gmt', 'comment_content', 'comment_karma', 'comment_approved', 'comment_agent', 'comment_type', 'comment_parent', 'user_id' );
	if ( ! $wpdb->insert( $wpdb->comments, $compacted ) ) {
		return false;
	}

	$id = (int) $wpdb->insert_id;

	if ( 1 == $comment_approved ) {
		wp_update_comment_count( $comment_post_ID );

		foreach ( array( 'server', 'gmt', 'blog' ) as $timezone ) {
			wp_cache_delete( "lastcommentmodified:$timezone", 'timeinfo' );
		}
	}

	clean_comment_cache( $id );

	$comment = get_comment( $id );

	// If metadata is provided, store it.
	if ( isset( $commentdata['comment_meta'] ) && is_array( $commentdata['comment_meta'] ) ) {
		foreach ( $commentdata['comment_meta'] as $meta_key => $meta_value ) {
			add_comment_meta( $comment->comment_ID, $meta_key, $meta_value, true );
		}
	}

	/**
	 * Fires immediately after a comment is inserted into the database.
	 *
	 * @since 2.8.0
	 *
	 * @param int        $id      The comment ID.
	 * @param WP_Comment $comment Comment object.
	 */
	do_action( 'wp_insert_comment', $id, $comment );

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
function wp_filter_comment( $commentdata ) {
	if ( isset( $commentdata['user_ID'] ) ) {
		/**
		 * Filters the comment author's user ID before it is set.
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
	 * Filters the comment author's browser user agent before it is set.
	 *
	 * @since 1.5.0
	 *
	 * @param string $comment_agent The comment author's browser user agent.
	 */
	$commentdata['comment_agent'] = apply_filters( 'pre_comment_user_agent', ( isset( $commentdata['comment_agent'] ) ? $commentdata['comment_agent'] : '' ) );
	/** This filter is documented in wp-includes/comment.php */
	$commentdata['comment_author'] = apply_filters( 'pre_comment_author_name', $commentdata['comment_author'] );
	/**
	 * Filters the comment content before it is set.
	 *
	 * @since 1.5.0
	 *
	 * @param string $comment_content The comment content.
	 */
	$commentdata['comment_content'] = apply_filters( 'pre_comment_content', $commentdata['comment_content'] );
	/**
	 * Filters the comment author's IP address before it is set.
	 *
	 * @since 1.5.0
	 *
	 * @param string $comment_author_ip The comment author's IP address.
	 */
	$commentdata['comment_author_IP'] = apply_filters( 'pre_comment_user_ip', $commentdata['comment_author_IP'] );
	/** This filter is documented in wp-includes/comment.php */
	$commentdata['comment_author_url'] = apply_filters( 'pre_comment_author_url', $commentdata['comment_author_url'] );
	/** This filter is documented in wp-includes/comment.php */
	$commentdata['comment_author_email'] = apply_filters( 'pre_comment_author_email', $commentdata['comment_author_email'] );
	$commentdata['filtered']             = true;
	return $commentdata;
}

/**
 * Whether a comment should be blocked because of comment flood.
 *
 * @since 2.1.0
 *
 * @param bool $block            Whether plugin has already blocked comment.
 * @param int  $time_lastcomment Timestamp for last comment.
 * @param int  $time_newcomment  Timestamp for new comment.
 * @return bool Whether comment should be blocked.
 */
function wp_throttle_comment_flood( $block, $time_lastcomment, $time_newcomment ) {
	if ( $block ) { // A plugin has already blocked... we'll let that decision stand.
		return $block;
	}
	if ( ( $time_newcomment - $time_lastcomment ) < 15 ) {
		return true;
	}
	return false;
}

/**
 * Adds a new comment to the database.
 *
 * Filters new comment to ensure that the fields are sanitized and valid before
 * inserting comment into database. Calls {@see 'comment_post'} action with comment ID
 * and whether comment is approved by WordPress. Also has {@see 'preprocess_comment'}
 * filter for processing the comment data before the function handles it.
 *
 * We use `REMOTE_ADDR` here directly. If you are behind a proxy, you should ensure
 * that it is properly set, such as in wp-config.php, for your environment.
 *
 * See {@link https://core.trac.wordpress.org/ticket/9235}
 *
 * @since 1.5.0
 * @since 4.3.0 Introduced the `comment_agent` and `comment_author_IP` arguments.
 * @since 4.7.0 The `$avoid_die` parameter was added, allowing the function
 *              to return a WP_Error object instead of dying.
 * @since 5.5.0 The `$avoid_die` parameter was renamed to `$wp_error`.
 * @since 5.5.0 Introduced the `comment_type` argument.
 *
 * @see wp_insert_comment()
 * @global wpdb $wpdb WordPress database abstraction object.
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
 *     @type string $comment_type         Comment type. Default 'comment'.
 *     @type int    $comment_parent       The ID of this comment's parent, if any. Default 0.
 *     @type int    $comment_post_ID      The ID of the post that relates to the comment.
 *     @type int    $user_id              The ID of the user who submitted the comment. Default 0.
 *     @type int    $user_ID              Kept for backward-compatibility. Use `$user_id` instead.
 *     @type string $comment_agent        Comment author user agent. Default is the value of 'HTTP_USER_AGENT'
 *                                        in the `$_SERVER` superglobal sent in the original request.
 *     @type string $comment_author_IP    Comment author IP address in IPv4 format. Default is the value of
 *                                        'REMOTE_ADDR' in the `$_SERVER` superglobal sent in the original request.
 * }
 * @param bool  $wp_error Should errors be returned as WP_Error objects instead of
 *                        executing wp_die()? Default false.
 * @return int|false|WP_Error The ID of the comment on success, false or WP_Error on failure.
 */
function wp_new_comment( $commentdata, $wp_error = false ) {
	global $wpdb;

	if ( isset( $commentdata['user_ID'] ) ) {
		$commentdata['user_ID'] = (int) $commentdata['user_ID'];
		$commentdata['user_id'] = $commentdata['user_ID'];
	}

	$prefiltered_user_id = ( isset( $commentdata['user_id'] ) ) ? (int) $commentdata['user_id'] : 0;

	if ( ! isset( $commentdata['comment_author_IP'] ) ) {
		$commentdata['comment_author_IP'] = $_SERVER['REMOTE_ADDR'];
	}

	if ( ! isset( $commentdata['comment_agent'] ) ) {
		$commentdata['comment_agent'] = isset( $_SERVER['HTTP_USER_AGENT'] ) ? $_SERVER['HTTP_USER_AGENT'] : '';
	}

	/**
	 * Filters a comment's data before it is sanitized and inserted into the database.
	 *
	 * @since 1.5.0
	 * @since 5.6.0 Comment data includes the `comment_agent` and `comment_author_IP` values.
	 *
	 * @param array $commentdata Comment data.
	 */
	$commentdata = apply_filters( 'preprocess_comment', $commentdata );

	$commentdata['comment_post_ID'] = (int) $commentdata['comment_post_ID'];
	if ( isset( $commentdata['user_ID'] ) && $prefiltered_user_id !== (int) $commentdata['user_ID'] ) {
		$commentdata['user_ID'] = (int) $commentdata['user_ID'];
		$commentdata['user_id'] = $commentdata['user_ID'];
	} elseif ( isset( $commentdata['user_id'] ) ) {
		$commentdata['user_id'] = (int) $commentdata['user_id'];
	}

	$commentdata['comment_parent'] = isset( $commentdata['comment_parent'] ) ? absint( $commentdata['comment_parent'] ) : 0;

	$parent_status = ( $commentdata['comment_parent'] > 0 ) ? wp_get_comment_status( $commentdata['comment_parent'] ) : '';

	$commentdata['comment_parent'] = ( 'approved' === $parent_status || 'unapproved' === $parent_status ) ? $commentdata['comment_parent'] : 0;

	$commentdata['comment_author_IP'] = preg_replace( '/[^0-9a-fA-F:., ]/', '', $commentdata['comment_author_IP'] );

	$commentdata['comment_agent'] = substr( $commentdata['comment_agent'], 0, 254 );

	if ( empty( $commentdata['comment_date'] ) ) {
		$commentdata['comment_date'] = current_time( 'mysql' );
	}

	if ( empty( $commentdata['comment_date_gmt'] ) ) {
		$commentdata['comment_date_gmt'] = current_time( 'mysql', 1 );
	}

	if ( empty( $commentdata['comment_type'] ) ) {
		$commentdata['comment_type'] = 'comment';
	}

	$commentdata = wp_filter_comment( $commentdata );

	$commentdata['comment_approved'] = wp_allow_comment( $commentdata, $wp_error );
	if ( is_wp_error( $commentdata['comment_approved'] ) ) {
		return $commentdata['comment_approved'];
	}

	$comment_ID = wp_insert_comment( $commentdata );
	if ( ! $comment_ID ) {
		$fields = array( 'comment_author', 'comment_author_email', 'comment_author_url', 'comment_content' );

		foreach ( $fields as $field ) {
			if ( isset( $commentdata[ $field ] ) ) {
				$commentdata[ $field ] = $wpdb->strip_invalid_text_for_column( $wpdb->comments, $field, $commentdata[ $field ] );
			}
		}

		$commentdata = wp_filter_comment( $commentdata );

		$commentdata['comment_approved'] = wp_allow_comment( $commentdata, $wp_error );
		if ( is_wp_error( $commentdata['comment_approved'] ) ) {
			return $commentdata['comment_approved'];
		}

		$comment_ID = wp_insert_comment( $commentdata );
		if ( ! $comment_ID ) {
			return false;
		}
	}

	/**
	 * Fires immediately after a comment is inserted into the database.
	 *
	 * @since 1.2.0
	 * @since 4.5.0 The `$commentdata` parameter was added.
	 *
	 * @param int        $comment_ID       The comment ID.
	 * @param int|string $comment_approved 1 if the comment is approved, 0 if not, 'spam' if spam.
	 * @param array      $commentdata      Comment data.
	 */
	do_action( 'comment_post', $comment_ID, $commentdata['comment_approved'], $commentdata );

	return $comment_ID;
}

/**
 * Send a comment moderation notification to the comment moderator.
 *
 * @since 4.4.0
 *
 * @param int $comment_ID ID of the comment.
 * @return bool True on success, false on failure.
 */
function wp_new_comment_notify_moderator( $comment_ID ) {
	$comment = get_comment( $comment_ID );

	// Only send notifications for pending comments.
	$maybe_notify = ( '0' == $comment->comment_approved );

	/** This filter is documented in wp-includes/comment.php */
	$maybe_notify = apply_filters( 'notify_moderator', $maybe_notify, $comment_ID );

	if ( ! $maybe_notify ) {
		return false;
	}

	return wp_notify_moderator( $comment_ID );
}

/**
 * Send a notification of a new comment to the post author.
 *
 * @since 4.4.0
 *
 * Uses the {@see 'notify_post_author'} filter to determine whether the post author
 * should be notified when a new comment is added, overriding site setting.
 *
 * @param int $comment_ID Comment ID.
 * @return bool True on success, false on failure.
 */
function wp_new_comment_notify_postauthor( $comment_ID ) {
	$comment = get_comment( $comment_ID );

	$maybe_notify = get_option( 'comments_notify' );

	/**
	 * Filters whether to send the post author new comment notification emails,
	 * overriding the site setting.
	 *
	 * @since 4.4.0
	 *
	 * @param bool $maybe_notify Whether to notify the post author about the new comment.
	 * @param int  $comment_ID   The ID of the comment for the notification.
	 */
	$maybe_notify = apply_filters( 'notify_post_author', $maybe_notify, $comment_ID );

	/*
	 * wp_notify_postauthor() checks if notifying the author of their own comment.
	 * By default, it won't, but filters can override this.
	 */
	if ( ! $maybe_notify ) {
		return false;
	}

	// Only send notifications for approved comments.
	if ( ! isset( $comment->comment_approved ) || '1' != $comment->comment_approved ) {
		return false;
	}

	return wp_notify_postauthor( $comment_ID );
}

/**
 * Sets the status of a comment.
 *
 * The {@see 'wp_set_comment_status'} action is called after the comment is handled.
 * If the comment status is not in the list, then false is returned.
 *
 * @since 1.0.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param int|WP_Comment $comment_id     Comment ID or WP_Comment object.
 * @param string         $comment_status New comment status, either 'hold', 'approve', 'spam', or 'trash'.
 * @param bool           $wp_error       Whether to return a WP_Error object if there is a failure. Default false.
 * @return bool|WP_Error True on success, false or WP_Error on failure.
 */
function wp_set_comment_status( $comment_id, $comment_status, $wp_error = false ) {
	global $wpdb;

	switch ( $comment_status ) {
		case 'hold':
		case '0':
			$status = '0';
			break;
		case 'approve':
		case '1':
			$status = '1';
			add_action( 'wp_set_comment_status', 'wp_new_comment_notify_postauthor' );
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

	$comment_old = clone get_comment( $comment_id );

	if ( ! $wpdb->update( $wpdb->comments, array( 'comment_approved' => $status ), array( 'comment_ID' => $comment_old->comment_ID ) ) ) {
		if ( $wp_error ) {
			return new WP_Error( 'db_update_error', __( 'Could not update comment status.' ), $wpdb->last_error );
		} else {
			return false;
		}
	}

	clean_comment_cache( $comment_old->comment_ID );

	$comment = get_comment( $comment_old->comment_ID );

	/**
	 * Fires immediately after transitioning a comment's status from one to another in the database
	 * and removing the comment from the object cache, but prior to all status transition hooks.
	 *
	 * @since 1.5.0
	 *
	 * @param int    $comment_id     Comment ID.
	 * @param string $comment_status Current comment status. Possible values include
	 *                               'hold', '0', 'approve', '1', 'spam', and 'trash'.
	 */
	do_action( 'wp_set_comment_status', $comment->comment_ID, $comment_status );

	wp_transition_comment_status( $comment_status, $comment_old->comment_approved, $comment );

	wp_update_comment_count( $comment->comment_post_ID );

	return true;
}

/**
 * Updates an existing comment in the database.
 *
 * Filters the comment and makes sure certain fields are valid before updating.
 *
 * @since 2.0.0
 * @since 4.9.0 Add updating comment meta during comment update.
 * @since 5.5.0 The `$wp_error` parameter was added.
 * @since 5.5.0 The return values for an invalid comment or post ID
 *              were changed to false instead of 0.
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param array $commentarr Contains information on the comment.
 * @param bool  $wp_error   Optional. Whether to return a WP_Error on failure. Default false.
 * @return int|false|WP_Error The value 1 if the comment was updated, 0 if not updated.
 *                            False or a WP_Error object on failure.
 */
function wp_update_comment( $commentarr, $wp_error = false ) {
	global $wpdb;

	// First, get all of the original fields.
	$comment = get_comment( $commentarr['comment_ID'], ARRAY_A );
	if ( empty( $comment ) ) {
		if ( $wp_error ) {
			return new WP_Error( 'invalid_comment_id', __( 'Invalid comment ID.' ) );
		} else {
			return false;
		}
	}

	// Make sure that the comment post ID is valid (if specified).
	if ( ! empty( $commentarr['comment_post_ID'] ) && ! get_post( $commentarr['comment_post_ID'] ) ) {
		if ( $wp_error ) {
			return new WP_Error( 'invalid_post_id', __( 'Invalid post ID.' ) );
		} else {
			return false;
		}
	}

	$filter_comment = false;
	if ( ! has_filter( 'pre_comment_content', 'wp_filter_kses' ) ) {
		$filter_comment = ! user_can( isset( $comment['user_id'] ) ? $comment['user_id'] : 0, 'unfiltered_html' );
	}

	if ( $filter_comment ) {
		add_filter( 'pre_comment_content', 'wp_filter_kses' );
	}

	// Escape data pulled from DB.
	$comment = wp_slash( $comment );

	$old_status = $comment['comment_approved'];

	// Merge old and new fields with new fields overwriting old ones.
	$commentarr = array_merge( $comment, $commentarr );

	$commentarr = wp_filter_comment( $commentarr );

	if ( $filter_comment ) {
		remove_filter( 'pre_comment_content', 'wp_filter_kses' );
	}

	// Now extract the merged array.
	$data = wp_unslash( $commentarr );

	/**
	 * Filters the comment content before it is updated in the database.
	 *
	 * @since 1.5.0
	 *
	 * @param string $comment_content The comment data.
	 */
	$data['comment_content'] = apply_filters( 'comment_save_pre', $data['comment_content'] );

	$data['comment_date_gmt'] = get_gmt_from_date( $data['comment_date'] );

	if ( ! isset( $data['comment_approved'] ) ) {
		$data['comment_approved'] = 1;
	} elseif ( 'hold' === $data['comment_approved'] ) {
		$data['comment_approved'] = 0;
	} elseif ( 'approve' === $data['comment_approved'] ) {
		$data['comment_approved'] = 1;
	}

	$comment_ID      = $data['comment_ID'];
	$comment_post_ID = $data['comment_post_ID'];

	/**
	 * Filters the comment data immediately before it is updated in the database.
	 *
	 * Note: data being passed to the filter is already unslashed.
	 *
	 * @since 4.7.0
	 * @since 5.5.0 Returning a WP_Error value from the filter will short-circuit comment update
	 *              and allow skipping further processing.
	 *
	 * @param array|WP_Error $data       The new, processed comment data, or WP_Error.
	 * @param array          $comment    The old, unslashed comment data.
	 * @param array          $commentarr The new, raw comment data.
	 */
	$data = apply_filters( 'wp_update_comment_data', $data, $comment, $commentarr );

	// Do not carry on on failure.
	if ( is_wp_error( $data ) ) {
		if ( $wp_error ) {
			return $data;
		} else {
			return false;
		}
	}

	$keys = array( 'comment_post_ID', 'comment_content', 'comment_author', 'comment_author_email', 'comment_approved', 'comment_karma', 'comment_author_url', 'comment_date', 'comment_date_gmt', 'comment_type', 'comment_parent', 'user_id', 'comment_agent', 'comment_author_IP' );
	$data = wp_array_slice_assoc( $data, $keys );

	$rval = $wpdb->update( $wpdb->comments, $data, compact( 'comment_ID' ) );

	if ( false === $rval ) {
		if ( $wp_error ) {
			return new WP_Error( 'db_update_error', __( 'Could not update comment in the database.' ), $wpdb->last_error );
		} else {
			return false;
		}
	}

	// If metadata is provided, store it.
	if ( isset( $commentarr['comment_meta'] ) && is_array( $commentarr['comment_meta'] ) ) {
		foreach ( $commentarr['comment_meta'] as $meta_key => $meta_value ) {
			update_comment_meta( $comment_ID, $meta_key, $meta_value );
		}
	}

	clean_comment_cache( $comment_ID );
	wp_update_comment_count( $comment_post_ID );

	/**
	 * Fires immediately after a comment is updated in the database.
	 *
	 * The hook also fires immediately before comment status transition hooks are fired.
	 *
	 * @since 1.2.0
	 * @since 4.6.0 Added the `$data` parameter.
	 *
	 * @param int   $comment_ID The comment ID.
	 * @param array $data       Comment data.
	 */
	do_action( 'edit_comment', $comment_ID, $data );

	$comment = get_comment( $comment_ID );

	wp_transition_comment_status( $comment->comment_approved, $old_status, $comment );

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
 *
 * @param bool $defer
 * @return bool
 */
function wp_defer_comment_counting( $defer = null ) {
	static $_defer = false;

	if ( is_bool( $defer ) ) {
		$_defer = $defer;
		// Flush any deferred counts.
		if ( ! $defer ) {
			wp_update_comment_count( null, true );
		}
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
 *
 * @see wp_update_comment_count_now() For what could cause a false return value
 *
 * @param int|null $post_id     Post ID.
 * @param bool     $do_deferred Optional. Whether to process previously deferred
 *                              post comment counts. Default false.
 * @return bool|void True on success, false on failure or if post with ID does
 *                   not exist.
 */
function wp_update_comment_count( $post_id, $do_deferred = false ) {
	static $_deferred = array();

	if ( empty( $post_id ) && ! $do_deferred ) {
		return false;
	}

	if ( $do_deferred ) {
		$_deferred = array_unique( $_deferred );
		foreach ( $_deferred as $i => $_post_id ) {
			wp_update_comment_count_now( $_post_id );
			unset( $_deferred[ $i ] );
			/** @todo Move this outside of the foreach and reset $_deferred to an array instead */
		}
	}

	if ( wp_defer_comment_counting() ) {
		$_deferred[] = $post_id;
		return true;
	} elseif ( $post_id ) {
		return wp_update_comment_count_now( $post_id );
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
 * @return bool True on success, false if the post does not exist.
 */
function wp_update_comment_count_now( $post_id ) {
	global $wpdb;
	$post_id = (int) $post_id;
	if ( ! $post_id ) {
		return false;
	}

	wp_cache_delete( 'comments-0', 'counts' );
	wp_cache_delete( "comments-{$post_id}", 'counts' );

	$post = get_post( $post_id );
	if ( ! $post ) {
		return false;
	}

	$old = (int) $post->comment_count;

	/**
	 * Filters a post's comment count before it is updated in the database.
	 *
	 * @since 4.5.0
	 *
	 * @param int|null $new     The new comment count. Default null.
	 * @param int      $old     The old comment count.
	 * @param int      $post_id Post ID.
	 */
	$new = apply_filters( 'pre_wp_update_comment_count_now', null, $old, $post_id );

	if ( is_null( $new ) ) {
		$new = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $wpdb->comments WHERE comment_post_ID = %d AND comment_approved = '1'", $post_id ) );
	} else {
		$new = (int) $new;
	}

	$wpdb->update( $wpdb->posts, array( 'comment_count' => $new ), array( 'ID' => $post_id ) );

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
	do_action( "edit_post_{$post->post_type}", $post_id, $post );

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
 * @param string $url        URL to ping.
 * @param string $deprecated Not Used.
 * @return string|false String containing URI on success, false on failure.
 */
function discover_pingback_server_uri( $url, $deprecated = '' ) {
	if ( ! empty( $deprecated ) ) {
		_deprecated_argument( __FUNCTION__, '2.7.0' );
	}

	$pingback_str_dquote = 'rel="pingback"';
	$pingback_str_squote = 'rel=\'pingback\'';

	/** @todo Should use Filter Extension or custom preg_match instead. */
	$parsed_url = parse_url( $url );

	if ( ! isset( $parsed_url['host'] ) ) { // Not a URL. This should never happen.
		return false;
	}

	// Do not search for a pingback server on our own uploads.
	$uploads_dir = wp_get_upload_dir();
	if ( 0 === strpos( $url, $uploads_dir['baseurl'] ) ) {
		return false;
	}

	$response = wp_safe_remote_head(
		$url,
		array(
			'timeout'     => 2,
			'httpversion' => '1.0',
		)
	);

	if ( is_wp_error( $response ) ) {
		return false;
	}

	if ( wp_remote_retrieve_header( $response, 'x-pingback' ) ) {
		return wp_remote_retrieve_header( $response, 'x-pingback' );
	}

	// Not an (x)html, sgml, or xml page, no use going further.
	if ( preg_match( '#(image|audio|video|model)/#is', wp_remote_retrieve_header( $response, 'content-type' ) ) ) {
		return false;
	}

	// Now do a GET since we're going to look in the HTML headers (and we're sure it's not a binary file).
	$response = wp_safe_remote_get(
		$url,
		array(
			'timeout'     => 2,
			'httpversion' => '1.0',
		)
	);

	if ( is_wp_error( $response ) ) {
		return false;
	}

	$contents = wp_remote_retrieve_body( $response );

	$pingback_link_offset_dquote = strpos( $contents, $pingback_str_dquote );
	$pingback_link_offset_squote = strpos( $contents, $pingback_str_squote );
	if ( $pingback_link_offset_dquote || $pingback_link_offset_squote ) {
		$quote                   = ( $pingback_link_offset_dquote ) ? '"' : '\'';
		$pingback_link_offset    = ( '"' === $quote ) ? $pingback_link_offset_dquote : $pingback_link_offset_squote;
		$pingback_href_pos       = strpos( $contents, 'href=', $pingback_link_offset );
		$pingback_href_start     = $pingback_href_pos + 6;
		$pingback_href_end       = strpos( $contents, $quote, $pingback_href_start );
		$pingback_server_url_len = $pingback_href_end - $pingback_href_start;
		$pingback_server_url     = substr( $contents, $pingback_href_start, $pingback_server_url_len );

		// We may find rel="pingback" but an incomplete pingback URL.
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
 * @since 5.6.0 Introduced `do_all_pings` action hook for individual services.
 */
function do_all_pings() {
	/**
	 * Fires immediately after the `do_pings` event to hook services individually.
	 *
	 * @since 5.6.0
	 */
	do_action( 'do_all_pings' );
}

/**
 * Perform all pingbacks.
 *
 * @since 5.6.0
 */
function do_all_pingbacks() {
	$pings = get_posts(
		array(
			'post_type'        => get_post_types(),
			'suppress_filters' => false,
			'nopaging'         => true,
			'meta_key'         => '_pingme',
			'fields'           => 'ids',
		)
	);

	foreach ( $pings as $ping ) {
		delete_post_meta( $ping, '_pingme' );
		pingback( null, $ping );
	}
}

/**
 * Perform all enclosures.
 *
 * @since 5.6.0
 */
function do_all_enclosures() {
	$enclosures = get_posts(
		array(
			'post_type'        => get_post_types(),
			'suppress_filters' => false,
			'nopaging'         => true,
			'meta_key'         => '_encloseme',
			'fields'           => 'ids',
		)
	);

	foreach ( $enclosures as $enclosure ) {
		delete_post_meta( $enclosure, '_encloseme' );
		do_enclose( null, $enclosure );
	}
}

/**
 * Perform all trackbacks.
 *
 * @since 5.6.0
 */
function do_all_trackbacks() {
	$trackbacks = get_posts(
		array(
			'post_type'        => get_post_types(),
			'suppress_filters' => false,
			'nopaging'         => true,
			'meta_key'         => '_trackbackme',
			'fields'           => 'ids',
		)
	);

	foreach ( $trackbacks as $trackback ) {
		delete_post_meta( $trackback, '_trackbackme' );
		do_trackbacks( $trackback );
	}
}

/**
 * Perform trackbacks.
 *
 * @since 1.5.0
 * @since 4.7.0 `$post_id` can be a WP_Post object.
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param int|WP_Post $post_id Post object or ID to do trackbacks on.
 */
function do_trackbacks( $post_id ) {
	global $wpdb;
	$post = get_post( $post_id );
	if ( ! $post ) {
		return false;
	}

	$to_ping = get_to_ping( $post );
	$pinged  = get_pung( $post );
	if ( empty( $to_ping ) ) {
		$wpdb->update( $wpdb->posts, array( 'to_ping' => '' ), array( 'ID' => $post->ID ) );
		return;
	}

	if ( empty( $post->post_excerpt ) ) {
		/** This filter is documented in wp-includes/post-template.php */
		$excerpt = apply_filters( 'the_content', $post->post_content, $post->ID );
	} else {
		/** This filter is documented in wp-includes/post-template.php */
		$excerpt = apply_filters( 'the_excerpt', $post->post_excerpt );
	}

	$excerpt = str_replace( ']]>', ']]&gt;', $excerpt );
	$excerpt = wp_html_excerpt( $excerpt, 252, '&#8230;' );

	/** This filter is documented in wp-includes/post-template.php */
	$post_title = apply_filters( 'the_title', $post->post_title, $post->ID );
	$post_title = strip_tags( $post_title );

	if ( $to_ping ) {
		foreach ( (array) $to_ping as $tb_ping ) {
			$tb_ping = trim( $tb_ping );
			if ( ! in_array( $tb_ping, $pinged, true ) ) {
				trackback( $tb_ping, $post_title, $excerpt, $post->ID );
				$pinged[] = $tb_ping;
			} else {
				$wpdb->query(
					$wpdb->prepare(
						"UPDATE $wpdb->posts SET to_ping = TRIM(REPLACE(to_ping, %s,
					'')) WHERE ID = %d",
						$tb_ping,
						$post->ID
					)
				);
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
	$services = get_option( 'ping_sites' );

	$services = explode( "\n", $services );
	foreach ( (array) $services as $service ) {
		$service = trim( $service );
		if ( '' !== $service ) {
			weblog_ping( $service );
		}
	}

	return $post_id;
}

/**
 * Pings back the links found in a post.
 *
 * @since 0.71
 * @since 4.7.0 `$post_id` can be a WP_Post object.
 *
 * @param string      $content Post content to check for links. If empty will retrieve from post.
 * @param int|WP_Post $post_id Post Object or ID.
 */
function pingback( $content, $post_id ) {
	include_once ABSPATH . WPINC . '/class-IXR.php';
	include_once ABSPATH . WPINC . '/class-wp-http-ixr-client.php';

	// Original code by Mort (http://mort.mine.nu:8080).
	$post_links = array();

	$post = get_post( $post_id );
	if ( ! $post ) {
		return;
	}

	$pung = get_pung( $post );

	if ( empty( $content ) ) {
		$content = $post->post_content;
	}

	/*
	 * Step 1.
	 * Parsing the post, external links (if any) are stored in the $post_links array.
	 */
	$post_links_temp = wp_extract_urls( $content );

	/*
	 * Step 2.
	 * Walking through the links array.
	 * First we get rid of links pointing to sites, not to specific files.
	 * Example:
	 * http://dummy-weblog.org
	 * http://dummy-weblog.org/
	 * http://dummy-weblog.org/post.php
	 * We don't wanna ping first and second types, even if they have a valid <link/>.
	 */
	foreach ( (array) $post_links_temp as $link_test ) {
		// If we haven't pung it already and it isn't a link to itself.
		if ( ! in_array( $link_test, $pung, true ) && ( url_to_postid( $link_test ) != $post->ID )
			// Also, let's never ping local attachments.
			&& ! is_local_attachment( $link_test )
		) {
			$test = parse_url( $link_test );
			if ( $test ) {
				if ( isset( $test['query'] ) ) {
					$post_links[] = $link_test;
				} elseif ( isset( $test['path'] ) && ( '/' !== $test['path'] ) && ( '' !== $test['path'] ) ) {
					$post_links[] = $link_test;
				}
			}
		}
	}

	$post_links = array_unique( $post_links );

	/**
	 * Fires just before pinging back links found in a post.
	 *
	 * @since 2.0.0
	 *
	 * @param string[] $post_links Array of link URLs to be checked (passed by reference).
	 * @param string[] $pung       Array of link URLs already pinged (passed by reference).
	 * @param int      $post_ID    The post ID.
	 */
	do_action_ref_array( 'pre_ping', array( &$post_links, &$pung, $post->ID ) );

	foreach ( (array) $post_links as $pagelinkedto ) {
		$pingback_server_url = discover_pingback_server_uri( $pagelinkedto );

		if ( $pingback_server_url ) {
			set_time_limit( 60 );
			// Now, the RPC call.
			$pagelinkedfrom = get_permalink( $post );

			// Using a timeout of 3 seconds should be enough to cover slow servers.
			$client          = new WP_HTTP_IXR_Client( $pingback_server_url );
			$client->timeout = 3;
			/**
			 * Filters the user agent sent when pinging-back a URL.
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
			$client->useragent = apply_filters( 'pingback_useragent', $client->useragent . ' -- WordPress/' . get_bloginfo( 'version' ), $client->useragent, $pingback_server_url, $pagelinkedto, $pagelinkedfrom );
			// When set to true, this outputs debug messages by itself.
			$client->debug = false;

			if ( $client->query( 'pingback.ping', $pagelinkedfrom, $pagelinkedto ) || ( isset( $client->error->code ) && 48 == $client->error->code ) ) { // Already registered.
				add_ping( $post, $pagelinkedto );
			}
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
function privacy_ping_filter( $sites ) {
	if ( '0' != get_option( 'blog_public' ) ) {
		return $sites;
	} else {
		return '';
	}
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
 * @param string $title         Title of post.
 * @param string $excerpt       Excerpt of post.
 * @param int    $ID            Post ID.
 * @return int|false|void Database query from update.
 */
function trackback( $trackback_url, $title, $excerpt, $ID ) {
	global $wpdb;

	if ( empty( $trackback_url ) ) {
		return;
	}

	$options            = array();
	$options['timeout'] = 10;
	$options['body']    = array(
		'title'     => $title,
		'url'       => get_permalink( $ID ),
		'blog_name' => get_option( 'blogname' ),
		'excerpt'   => $excerpt,
	);

	$response = wp_safe_remote_post( $trackback_url, $options );

	if ( is_wp_error( $response ) ) {
		return;
	}

	$wpdb->query( $wpdb->prepare( "UPDATE $wpdb->posts SET pinged = CONCAT(pinged, '\n', %s) WHERE ID = %d", $trackback_url, $ID ) );
	return $wpdb->query( $wpdb->prepare( "UPDATE $wpdb->posts SET to_ping = TRIM(REPLACE(to_ping, %s, '')) WHERE ID = %d", $trackback_url, $ID ) );
}

/**
 * Send a pingback.
 *
 * @since 1.2.0
 *
 * @param string $server Host of blog to connect to.
 * @param string $path Path to send the ping.
 */
function weblog_ping( $server = '', $path = '' ) {
	include_once ABSPATH . WPINC . '/class-IXR.php';
	include_once ABSPATH . WPINC . '/class-wp-http-ixr-client.php';

	// Using a timeout of 3 seconds should be enough to cover slow servers.
	$client             = new WP_HTTP_IXR_Client( $server, ( ( ! strlen( trim( $path ) ) || ( '/' === $path ) ) ? false : $path ) );
	$client->timeout    = 3;
	$client->useragent .= ' -- WordPress/' . get_bloginfo( 'version' );

	// When set to true, this outputs debug messages by itself.
	$client->debug = false;
	$home          = trailingslashit( home_url() );
	if ( ! $client->query( 'weblogUpdates.extendedPing', get_option( 'blogname' ), $home, get_bloginfo( 'rss2_url' ) ) ) { // Then try a normal ping.
		$client->query( 'weblogUpdates.ping', get_option( 'blogname' ), $home );
	}
}

/**
 * Default filter attached to pingback_ping_source_uri to validate the pingback's Source URI
 *
 * @since 3.5.1
 *
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
 *
 * @link https://www.hixie.ch/specs/pingback/pingback#TOC3
 *
 * @param IXR_Error $ixr_error
 * @return IXR_Error
 */
function xmlrpc_pingback_error( $ixr_error ) {
	if ( 48 === $ixr_error->code ) {
		return $ixr_error;
	}
	return new IXR_Error( 0, '' );
}

//
// Cache.
//

/**
 * Removes a comment from the object cache.
 *
 * @since 2.3.0
 *
 * @param int|array $ids Comment ID or an array of comment IDs to remove from cache.
 */
function clean_comment_cache( $ids ) {
	foreach ( (array) $ids as $id ) {
		wp_cache_delete( $id, 'comment' );

		/**
		 * Fires immediately after a comment has been removed from the object cache.
		 *
		 * @since 4.5.0
		 *
		 * @param int $id Comment ID.
		 */
		do_action( 'clean_comment_cache', $id );
	}

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
 * @since 4.4.0 Introduced the `$update_meta_cache` parameter.
 *
 * @param WP_Comment[] $comments          Array of comment objects
 * @param bool         $update_meta_cache Whether to update commentmeta cache. Default true.
 */
function update_comment_cache( $comments, $update_meta_cache = true ) {
	foreach ( (array) $comments as $comment ) {
		wp_cache_add( $comment->comment_ID, $comment, 'comment' );
	}

	if ( $update_meta_cache ) {
		// Avoid `wp_list_pluck()` in case `$comments` is passed by reference.
		$comment_ids = array();
		foreach ( $comments as $comment ) {
			$comment_ids[] = $comment->comment_ID;
		}
		update_meta_cache( 'comment', $comment_ids );
	}
}

/**
 * Adds any comments from the given IDs to the cache that do not already exist in cache.
 *
 * @since 4.4.0
 * @access private
 *
 * @see update_comment_cache()
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param int[] $comment_ids       Array of comment IDs.
 * @param bool  $update_meta_cache Optional. Whether to update the meta cache. Default true.
 */
function _prime_comment_caches( $comment_ids, $update_meta_cache = true ) {
	global $wpdb;

	$non_cached_ids = _get_non_cached_ids( $comment_ids, 'comment' );
	if ( ! empty( $non_cached_ids ) ) {
		$fresh_comments = $wpdb->get_results( sprintf( "SELECT $wpdb->comments.* FROM $wpdb->comments WHERE comment_ID IN (%s)", implode( ',', array_map( 'intval', $non_cached_ids ) ) ) );

		update_comment_cache( $fresh_comments, $update_meta_cache );
	}
}

//
// Internal.
//

/**
 * Close comments on old posts on the fly, without any extra DB queries. Hooked to the_posts.
 *
 * @since 2.7.0
 * @access private
 *
 * @param WP_Post  $posts Post data object.
 * @param WP_Query $query Query object.
 * @return array
 */
function _close_comments_for_old_posts( $posts, $query ) {
	if ( empty( $posts ) || ! $query->is_singular() || ! get_option( 'close_comments_for_old_posts' ) ) {
		return $posts;
	}

	/**
	 * Filters the list of post types to automatically close comments for.
	 *
	 * @since 3.2.0
	 *
	 * @param string[] $post_types An array of post type names.
	 */
	$post_types = apply_filters( 'close_comments_for_post_types', array( 'post' ) );
	if ( ! in_array( $posts[0]->post_type, $post_types, true ) ) {
		return $posts;
	}

	$days_old = (int) get_option( 'close_comments_days_old' );
	if ( ! $days_old ) {
		return $posts;
	}

	if ( time() - strtotime( $posts[0]->post_date_gmt ) > ( $days_old * DAY_IN_SECONDS ) ) {
		$posts[0]->comment_status = 'closed';
		$posts[0]->ping_status    = 'closed';
	}

	return $posts;
}

/**
 * Close comments on an old post. Hooked to comments_open and pings_open.
 *
 * @since 2.7.0
 * @access private
 *
 * @param bool $open    Comments open or closed.
 * @param int  $post_id Post ID.
 * @return bool $open
 */
function _close_comments_for_old_post( $open, $post_id ) {
	if ( ! $open ) {
		return $open;
	}

	if ( ! get_option( 'close_comments_for_old_posts' ) ) {
		return $open;
	}

	$days_old = (int) get_option( 'close_comments_days_old' );
	if ( ! $days_old ) {
		return $open;
	}

	$post = get_post( $post_id );

	/** This filter is documented in wp-includes/comment.php */
	$post_types = apply_filters( 'close_comments_for_post_types', array( 'post' ) );
	if ( ! in_array( $post->post_type, $post_types, true ) ) {
		return $open;
	}

	// Undated drafts should not show up as comments closed.
	if ( '0000-00-00 00:00:00' === $post->post_date_gmt ) {
		return $open;
	}

	if ( time() - strtotime( $post->post_date_gmt ) > ( $days_old * DAY_IN_SECONDS ) ) {
		return false;
	}

	return $open;
}

/**
 * Handles the submission of a comment, usually posted to wp-comments-post.php via a comment form.
 *
 * This function expects unslashed data, as opposed to functions such as `wp_new_comment()` which
 * expect slashed data.
 *
 * @since 4.4.0
 *
 * @param array $comment_data {
 *     Comment data.
 *
 *     @type string|int $comment_post_ID             The ID of the post that relates to the comment.
 *     @type string     $author                      The name of the comment author.
 *     @type string     $email                       The comment author email address.
 *     @type string     $url                         The comment author URL.
 *     @type string     $comment                     The content of the comment.
 *     @type string|int $comment_parent              The ID of this comment's parent, if any. Default 0.
 *     @type string     $_wp_unfiltered_html_comment The nonce value for allowing unfiltered HTML.
 * }
 * @return WP_Comment|WP_Error A WP_Comment object on success, a WP_Error object on failure.
 */
function wp_handle_comment_submission( $comment_data ) {

	$comment_post_ID      = 0;
	$comment_parent       = 0;
	$user_ID              = 0;
	$comment_author       = null;
	$comment_author_email = null;
	$comment_author_url   = null;
	$comment_content      = null;

	if ( isset( $comment_data['comment_post_ID'] ) ) {
		$comment_post_ID = (int) $comment_data['comment_post_ID'];
	}
	if ( isset( $comment_data['author'] ) && is_string( $comment_data['author'] ) ) {
		$comment_author = trim( strip_tags( $comment_data['author'] ) );
	}
	if ( isset( $comment_data['email'] ) && is_string( $comment_data['email'] ) ) {
		$comment_author_email = trim( $comment_data['email'] );
	}
	if ( isset( $comment_data['url'] ) && is_string( $comment_data['url'] ) ) {
		$comment_author_url = trim( $comment_data['url'] );
	}
	if ( isset( $comment_data['comment'] ) && is_string( $comment_data['comment'] ) ) {
		$comment_content = trim( $comment_data['comment'] );
	}
	if ( isset( $comment_data['comment_parent'] ) ) {
		$comment_parent = absint( $comment_data['comment_parent'] );
	}

	$post = get_post( $comment_post_ID );

	if ( empty( $post->comment_status ) ) {

		/**
		 * Fires when a comment is attempted on a post that does not exist.
		 *
		 * @since 1.5.0
		 *
		 * @param int $comment_post_ID Post ID.
		 */
		do_action( 'comment_id_not_found', $comment_post_ID );

		return new WP_Error( 'comment_id_not_found' );

	}

	// get_post_status() will get the parent status for attachments.
	$status = get_post_status( $post );

	if ( ( 'private' === $status ) && ! current_user_can( 'read_post', $comment_post_ID ) ) {
		return new WP_Error( 'comment_id_not_found' );
	}

	$status_obj = get_post_status_object( $status );

	if ( ! comments_open( $comment_post_ID ) ) {

		/**
		 * Fires when a comment is attempted on a post that has comments closed.
		 *
		 * @since 1.5.0
		 *
		 * @param int $comment_post_ID Post ID.
		 */
		do_action( 'comment_closed', $comment_post_ID );

		return new WP_Error( 'comment_closed', __( 'Sorry, comments are closed for this item.' ), 403 );

	} elseif ( 'trash' === $status ) {

		/**
		 * Fires when a comment is attempted on a trashed post.
		 *
		 * @since 2.9.0
		 *
		 * @param int $comment_post_ID Post ID.
		 */
		do_action( 'comment_on_trash', $comment_post_ID );

		return new WP_Error( 'comment_on_trash' );

	} elseif ( ! $status_obj->public && ! $status_obj->private ) {

		/**
		 * Fires when a comment is attempted on a post in draft mode.
		 *
		 * @since 1.5.1
		 *
		 * @param int $comment_post_ID Post ID.
		 */
		do_action( 'comment_on_draft', $comment_post_ID );

		if ( current_user_can( 'read_post', $comment_post_ID ) ) {
			return new WP_Error( 'comment_on_draft', __( 'Sorry, comments are not allowed for this item.' ), 403 );
		} else {
			return new WP_Error( 'comment_on_draft' );
		}
	} elseif ( post_password_required( $comment_post_ID ) ) {

		/**
		 * Fires when a comment is attempted on a password-protected post.
		 *
		 * @since 2.9.0
		 *
		 * @param int $comment_post_ID Post ID.
		 */
		do_action( 'comment_on_password_protected', $comment_post_ID );

		return new WP_Error( 'comment_on_password_protected' );

	} else {

		/**
		 * Fires before a comment is posted.
		 *
		 * @since 2.8.0
		 *
		 * @param int $comment_post_ID Post ID.
		 */
		do_action( 'pre_comment_on_post', $comment_post_ID );

	}

	// If the user is logged in.
	$user = wp_get_current_user();
	if ( $user->exists() ) {
		if ( empty( $user->display_name ) ) {
			$user->display_name = $user->user_login;
		}
		$comment_author       = $user->display_name;
		$comment_author_email = $user->user_email;
		$comment_author_url   = $user->user_url;
		$user_ID              = $user->ID;
		if ( current_user_can( 'unfiltered_html' ) ) {
			if ( ! isset( $comment_data['_wp_unfiltered_html_comment'] )
				|| ! wp_verify_nonce( $comment_data['_wp_unfiltered_html_comment'], 'unfiltered-html-comment_' . $comment_post_ID )
			) {
				kses_remove_filters(); // Start with a clean slate.
				kses_init_filters();   // Set up the filters.
				remove_filter( 'pre_comment_content', 'wp_filter_post_kses' );
				add_filter( 'pre_comment_content', 'wp_filter_kses' );
			}
		}
	} else {
		if ( get_option( 'comment_registration' ) ) {
			return new WP_Error( 'not_logged_in', __( 'Sorry, you must be logged in to comment.' ), 403 );
		}
	}

	$comment_type = 'comment';

	if ( get_option( 'require_name_email' ) && ! $user->exists() ) {
		if ( '' == $comment_author_email || '' == $comment_author ) {
			return new WP_Error( 'require_name_email', __( '<strong>Error</strong>: Please fill the required fields (name, email).' ), 200 );
		} elseif ( ! is_email( $comment_author_email ) ) {
			return new WP_Error( 'require_valid_email', __( '<strong>Error</strong>: Please enter a valid email address.' ), 200 );
		}
	}

	$commentdata = compact(
		'comment_post_ID',
		'comment_author',
		'comment_author_email',
		'comment_author_url',
		'comment_content',
		'comment_type',
		'comment_parent',
		'user_ID'
	);

	/**
	 * Filters whether an empty comment should be allowed.
	 *
	 * @since 5.1.0
	 *
	 * @param bool  $allow_empty_comment Whether to allow empty comments. Default false.
	 * @param array $commentdata         Array of comment data to be sent to wp_insert_comment().
	 */
	$allow_empty_comment = apply_filters( 'allow_empty_comment', false, $commentdata );
	if ( '' === $comment_content && ! $allow_empty_comment ) {
		return new WP_Error( 'require_valid_comment', __( '<strong>Error</strong>: Please type your comment text.' ), 200 );
	}

	$check_max_lengths = wp_check_comment_data_max_lengths( $commentdata );
	if ( is_wp_error( $check_max_lengths ) ) {
		return $check_max_lengths;
	}

	$comment_id = wp_new_comment( wp_slash( $commentdata ), true );
	if ( is_wp_error( $comment_id ) ) {
		return $comment_id;
	}

	if ( ! $comment_id ) {
		return new WP_Error( 'comment_save_error', __( '<strong>Error</strong>: The comment could not be saved. Please try again later.' ), 500 );
	}

	return get_comment( $comment_id );
}

/**
 * Registers the personal data exporter for comments.
 *
 * @since 4.9.6
 *
 * @param array $exporters An array of personal data exporters.
 * @return array An array of personal data exporters.
 */
function wp_register_comment_personal_data_exporter( $exporters ) {
	$exporters['wordpress-comments'] = array(
		'exporter_friendly_name' => __( 'WordPress Comments' ),
		'callback'               => 'wp_comments_personal_data_exporter',
	);

	return $exporters;
}

/**
 * Finds and exports personal data associated with an email address from the comments table.
 *
 * @since 4.9.6
 *
 * @param string $email_address The comment author email address.
 * @param int    $page          Comment page.
 * @return array An array of personal data.
 */
function wp_comments_personal_data_exporter( $email_address, $page = 1 ) {
	// Limit us to 500 comments at a time to avoid timing out.
	$number = 500;
	$page   = (int) $page;

	$data_to_export = array();

	$comments = get_comments(
		array(
			'author_email'              => $email_address,
			'number'                    => $number,
			'paged'                     => $page,
			'order_by'                  => 'comment_ID',
			'order'                     => 'ASC',
			'update_comment_meta_cache' => false,
		)
	);

	$comment_prop_to_export = array(
		'comment_author'       => __( 'Comment Author' ),
		'comment_author_email' => __( 'Comment Author Email' ),
		'comment_author_url'   => __( 'Comment Author URL' ),
		'comment_author_IP'    => __( 'Comment Author IP' ),
		'comment_agent'        => __( 'Comment Author User Agent' ),
		'comment_date'         => __( 'Comment Date' ),
		'comment_content'      => __( 'Comment Content' ),
		'comment_link'         => __( 'Comment URL' ),
	);

	foreach ( (array) $comments as $comment ) {
		$comment_data_to_export = array();

		foreach ( $comment_prop_to_export as $key => $name ) {
			$value = '';

			switch ( $key ) {
				case 'comment_author':
				case 'comment_author_email':
				case 'comment_author_url':
				case 'comment_author_IP':
				case 'comment_agent':
				case 'comment_date':
					$value = $comment->{$key};
					break;

				case 'comment_content':
					$value = get_comment_text( $comment->comment_ID );
					break;

				case 'comment_link':
					$value = get_comment_link( $comment->comment_ID );
					$value = sprintf(
						'<a href="%s" target="_blank" rel="noopener">%s</a>',
						esc_url( $value ),
						esc_html( $value )
					);
					break;
			}

			if ( ! empty( $value ) ) {
				$comment_data_to_export[] = array(
					'name'  => $name,
					'value' => $value,
				);
			}
		}

		$data_to_export[] = array(
			'group_id'          => 'comments',
			'group_label'       => __( 'Comments' ),
			'group_description' => __( 'User&#8217;s comment data.' ),
			'item_id'           => "comment-{$comment->comment_ID}",
			'data'              => $comment_data_to_export,
		);
	}

	$done = count( $comments ) < $number;

	return array(
		'data' => $data_to_export,
		'done' => $done,
	);
}

/**
 * Registers the personal data eraser for comments.
 *
 * @since 4.9.6
 *
 * @param array $erasers An array of personal data erasers.
 * @return array An array of personal data erasers.
 */
function wp_register_comment_personal_data_eraser( $erasers ) {
	$erasers['wordpress-comments'] = array(
		'eraser_friendly_name' => __( 'WordPress Comments' ),
		'callback'             => 'wp_comments_personal_data_eraser',
	);

	return $erasers;
}

/**
 * Erases personal data associated with an email address from the comments table.
 *
 * @since 4.9.6
 *
 * @param string $email_address The comment author email address.
 * @param int    $page          Comment page.
 * @return array
 */
function wp_comments_personal_data_eraser( $email_address, $page = 1 ) {
	global $wpdb;

	if ( empty( $email_address ) ) {
		return array(
			'items_removed'  => false,
			'items_retained' => false,
			'messages'       => array(),
			'done'           => true,
		);
	}

	// Limit us to 500 comments at a time to avoid timing out.
	$number         = 500;
	$page           = (int) $page;
	$items_removed  = false;
	$items_retained = false;

	$comments = get_comments(
		array(
			'author_email'       => $email_address,
			'number'             => $number,
			'paged'              => $page,
			'order_by'           => 'comment_ID',
			'order'              => 'ASC',
			'include_unapproved' => true,
		)
	);

	/* translators: Name of a comment's author after being anonymized. */
	$anon_author = __( 'Anonymous' );
	$messages    = array();

	foreach ( (array) $comments as $comment ) {
		$anonymized_comment                         = array();
		$anonymized_comment['comment_agent']        = '';
		$anonymized_comment['comment_author']       = $anon_author;
		$anonymized_comment['comment_author_email'] = '';
		$anonymized_comment['comment_author_IP']    = wp_privacy_anonymize_data( 'ip', $comment->comment_author_IP );
		$anonymized_comment['comment_author_url']   = '';
		$anonymized_comment['user_id']              = 0;

		$comment_id = (int) $comment->comment_ID;

		/**
		 * Filters whether to anonymize the comment.
		 *
		 * @since 4.9.6
		 *
		 * @param bool|string $anon_message       Whether to apply the comment anonymization (bool) or a custom
		 *                                        message (string). Default true.
		 * @param WP_Comment  $comment            WP_Comment object.
		 * @param array       $anonymized_comment Anonymized comment data.
		 */
		$anon_message = apply_filters( 'wp_anonymize_comment', true, $comment, $anonymized_comment );

		if ( true !== $anon_message ) {
			if ( $anon_message && is_string( $anon_message ) ) {
				$messages[] = esc_html( $anon_message );
			} else {
				/* translators: %d: Comment ID. */
				$messages[] = sprintf( __( 'Comment %d contains personal data but could not be anonymized.' ), $comment_id );
			}

			$items_retained = true;

			continue;
		}

		$args = array(
			'comment_ID' => $comment_id,
		);

		$updated = $wpdb->update( $wpdb->comments, $anonymized_comment, $args );

		if ( $updated ) {
			$items_removed = true;
			clean_comment_cache( $comment_id );
		} else {
			$items_retained = true;
		}
	}

	$done = count( $comments ) < $number;

	return array(
		'items_removed'  => $items_removed,
		'items_retained' => $items_retained,
		'messages'       => $messages,
		'done'           => $done,
	);
}

/**
 * Sets the last changed time for the 'comment' cache group.
 *
 * @since 5.0.0
 */
function wp_cache_set_comments_last_changed() {
	wp_cache_set( 'last_changed', microtime(), 'comment' );
}

/**
 * Updates the comment type for a batch of comments.
 *
 * @since 5.5.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 */
function _wp_batch_update_comment_type() {
	global $wpdb;

	$lock_name = 'update_comment_type.lock';

	// Try to lock.
	$lock_result = $wpdb->query( $wpdb->prepare( "INSERT IGNORE INTO `$wpdb->options` ( `option_name`, `option_value`, `autoload` ) VALUES (%s, %s, 'no') /* LOCK */", $lock_name, time() ) );

	if ( ! $lock_result ) {
		$lock_result = get_option( $lock_name );

		// Bail if we were unable to create a lock, or if the existing lock is still valid.
		if ( ! $lock_result || ( $lock_result > ( time() - HOUR_IN_SECONDS ) ) ) {
			wp_schedule_single_event( time() + ( 5 * MINUTE_IN_SECONDS ), 'wp_update_comment_type_batch' );
			return;
		}
	}

	// Update the lock, as by this point we've definitely got a lock, just need to fire the actions.
	update_option( $lock_name, time() );

	// Check if there's still an empty comment type.
	$empty_comment_type = $wpdb->get_var(
		"SELECT comment_ID FROM $wpdb->comments
		WHERE comment_type = ''
		LIMIT 1"
	);

	// No empty comment type, we're done here.
	if ( ! $empty_comment_type ) {
		update_option( 'finished_updating_comment_type', true );
		delete_option( $lock_name );
		return;
	}

	// Empty comment type found? We'll need to run this script again.
	wp_schedule_single_event( time() + ( 2 * MINUTE_IN_SECONDS ), 'wp_update_comment_type_batch' );

	/**
	 * Filters the comment batch size for updating the comment type.
	 *
	 * @since 5.5.0
	 *
	 * @param int $comment_batch_size The comment batch size. Default 100.
	 */
	$comment_batch_size = (int) apply_filters( 'wp_update_comment_type_batch_size', 100 );

	// Get the IDs of the comments to update.
	$comment_ids = $wpdb->get_col(
		$wpdb->prepare(
			"SELECT comment_ID
			FROM {$wpdb->comments}
			WHERE comment_type = ''
			ORDER BY comment_ID DESC
			LIMIT %d",
			$comment_batch_size
		)
	);

	if ( $comment_ids ) {
		$comment_id_list = implode( ',', $comment_ids );

		// Update the `comment_type` field value to be `comment` for the next batch of comments.
		$wpdb->query(
			"UPDATE {$wpdb->comments}
			SET comment_type = 'comment'
			WHERE comment_type = ''
			AND comment_ID IN ({$comment_id_list})" // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		);

		// Make sure to clean the comment cache.
		clean_comment_cache( $comment_ids );
	}

	delete_option( $lock_name );
}

/**
 * In order to avoid the _wp_batch_update_comment_type() job being accidentally removed,
 * check that it's still scheduled while we haven't finished updating comment types.
 *
 * @ignore
 * @since 5.5.0
 */
function _wp_check_for_scheduled_update_comment_type() {
	if ( ! get_option( 'finished_updating_comment_type' ) && ! wp_next_scheduled( 'wp_update_comment_type_batch' ) ) {
		wp_schedule_single_event( time() + MINUTE_IN_SECONDS, 'wp_update_comment_type_batch' );
	}
}
