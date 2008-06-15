<?php
/**
 * Manages WordPress comments
 *
 * @package WordPress
 */

/**
 * Checks whether a comment passes internal checks to be allowed to add.
 *
 * If comment moderation is set in the administration, then all comments,
 * regardless of their type and whitelist will be set to false.
 *
 * If the number of links exceeds the amount in the administration, then the
 * check fails.
 *
 * If any of the parameter contents match the blacklist of words, then the check
 * fails.
 *
 * If the comment is a trackback and part of the blogroll, then the trackback is
 * automatically whitelisted. If the comment author was approved before, then
 * the comment is automatically whitelisted.
 *
 * If none of the checks fail, then the failback is to set the check to pass
 * (return true).
 *
 * @since 1.2
 * @uses $wpdb
 *
 * @param string $author Comment Author's name
 * @param string $email Comment Author's email
 * @param string $url Comment Author's URL
 * @param string $comment Comment contents
 * @param string $user_ip Comment Author's IP address
 * @param string $user_agent Comment Author's User Agent
 * @param string $comment_type Comment type, either user submitted comment,
 *		trackback, or pingback
 * @return bool Whether the checks passed (true) and the comments should be
 *		displayed or set to moderated
 */
function check_comment($author, $email, $url, $comment, $user_ip, $user_agent, $comment_type) {
	global $wpdb;

	if ( 1 == get_option('comment_moderation') )
		return false; // If moderation is set to manual

	if ( preg_match_all("|(href\t*?=\t*?['\"]?)?(https?:)?//|i", $comment, $out) >= get_option('comment_max_links') )
		return false; // Check # of external links

	$mod_keys = trim(get_option('moderation_keys'));
	if ( !empty($mod_keys) ) {
		$words = explode("\n", $mod_keys );

		foreach ($words as $word) {
			$word = trim($word);

			// Skip empty lines
			if ( empty($word) )
				continue;

			// Do some escaping magic so that '#' chars in the
			// spam words don't break things:
			$word = preg_quote($word, '#');

			$pattern = "#$word#i";
			if ( preg_match($pattern, $author) ) return false;
			if ( preg_match($pattern, $email) ) return false;
			if ( preg_match($pattern, $url) ) return false;
			if ( preg_match($pattern, $comment) ) return false;
			if ( preg_match($pattern, $user_ip) ) return false;
			if ( preg_match($pattern, $user_agent) ) return false;
		}
	}

	// Comment whitelisting:
	if ( 1 == get_option('comment_whitelist')) {
		if ( 'trackback' == $comment_type || 'pingback' == $comment_type ) { // check if domain is in blogroll
			$uri = parse_url($url);
			$domain = $uri['host'];
			$uri = parse_url( get_option('home') );
			$home_domain = $uri['host'];
			if ( $wpdb->get_var($wpdb->prepare("SELECT link_id FROM $wpdb->links WHERE link_url LIKE (%s) LIMIT 1", '%'.$domain.'%')) || $domain == $home_domain )
				return true;
			else
				return false;
		} elseif ( $author != '' && $email != '' ) {
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
 * @since 2.0
 * @uses $wpdb
 *
 * @param int $post_id The ID of the post
 * @return array $comments The approved comments
 */
function get_approved_comments($post_id) {
	global $wpdb;
	return $wpdb->get_results($wpdb->prepare("SELECT * FROM $wpdb->comments WHERE comment_post_ID = %d AND comment_approved = '1' ORDER BY comment_date", $post_id));
}

/**
 * Retrieves comment data given a comment ID or comment object.
 *
 * If an object is passed then the comment data will be cached and then returned
 * after being passed through a filter.
 *
 * If the comment is empty, then the global comment variable will be used, if it
 * is set.
 *
 * @since 2.0
 * @uses $wpdb
 *
 * @param object|string|int $comment Comment to retrieve.
 * @param string $output Optional. OBJECT or ARRAY_A or ARRAY_N constants
 * @return object|array|null Depends on $output value.
 */
function &get_comment(&$comment, $output = OBJECT) {
	global $wpdb;

	if ( empty($comment) ) {
		if ( isset($GLOBALS['comment']) )
			$_comment = & $GLOBALS['comment'];
		else
			$_comment = null;
	} elseif ( is_object($comment) ) {
		wp_cache_add($comment->comment_ID, $comment, 'comment');
		$_comment = $comment;
	} else {
		if ( isset($GLOBALS['comment']) && ($GLOBALS['comment']->comment_ID == $comment) ) {
			$_comment = & $GLOBALS['comment'];
		} elseif ( ! $_comment = wp_cache_get($comment, 'comment') ) {
			$_comment = $wpdb->get_row($wpdb->prepare("SELECT * FROM $wpdb->comments WHERE comment_ID = %d LIMIT 1", $comment));
			wp_cache_add($_comment->comment_ID, $_comment, 'comment');
		}
	}

	$_comment = apply_filters('get_comment', $_comment);

	if ( $output == OBJECT ) {
		return $_comment;
	} elseif ( $output == ARRAY_A ) {
		return get_object_vars($_comment);
	} elseif ( $output == ARRAY_N ) {
		return array_values(get_object_vars($_comment));
	} else {
		return $_comment;
	}
}

/**
 * Retrieve an array of comment data about comment $comment_ID.
 *
 * get_comment() technically does the same thing as this function. This function
 * also appears to reference variables and then not use them or not update them
 * when needed. It is advised to switch to get_comment(), since this function
 * might be deprecated in favor of using get_comment().
 *
 * @deprecated Use get_comment()
 * @see get_comment()
 * @since 0.71
 *
 * @uses $postc Comment cache, might not be used any more
 * @uses $id
 * @uses $wpdb Database Object
 *
 * @param int $comment_ID The ID of the comment
 * @param int $no_cache Whether to use the cache or not (casted to bool)
 * @param bool $include_unapproved Whether to include unapproved comments or not
 * @return array The comment data
 */
function get_commentdata( $comment_ID, $no_cache = 0, $include_unapproved = false ) {
	global $postc, $wpdb;
	if ( $no_cache ) {
		$query = $wpdb->prepare("SELECT * FROM $wpdb->comments WHERE comment_ID = %d", $comment_ID);
		if ( false == $include_unapproved )
			$query .= " AND comment_approved = '1'";
		$myrow = $wpdb->get_row($query, ARRAY_A);
	} else {
		$myrow['comment_ID']           = $postc->comment_ID;
		$myrow['comment_post_ID']      = $postc->comment_post_ID;
		$myrow['comment_author']       = $postc->comment_author;
		$myrow['comment_author_email'] = $postc->comment_author_email;
		$myrow['comment_author_url']   = $postc->comment_author_url;
		$myrow['comment_author_IP']    = $postc->comment_author_IP;
		$myrow['comment_date']         = $postc->comment_date;
		$myrow['comment_content']      = $postc->comment_content;
		$myrow['comment_karma']        = $postc->comment_karma;
		$myrow['comment_approved']     = $postc->comment_approved;
		$myrow['comment_type']         = $postc->comment_type;
	}
	return $myrow;
}

/**
 * The date the last comment was modified.
 *
 * {@internal Missing Long Description}}
 *
 * @since 1.5.0
 * @uses $wpdb
 * @global array $cache_lastcommentmodified
 *
 * @param string $timezone Which timezone to use in reference to 'gmt', 'blog',
 *		or 'server' locations
 * @return string Last comment modified date
 */
function get_lastcommentmodified($timezone = 'server') {
	global $cache_lastcommentmodified, $wpdb;

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
 * {@internal Missing Long Description}}
 *
 * @since 2.0.0
 * @uses $wpdb
 *
 * @param int $post_id Optional. Comment amount in post if > 0, else total comments blog wide
 * @return array The amount of spam, approved, awaiting moderation, and total
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
		"approved"              => 0,
		"awaiting_moderation"   => 0,
		"spam"                  => 0,
		"total_comments"        => 0
	);

	foreach ( $totals as $row ) {
		switch ( $row['comment_approved'] ) {
			case 'spam':
				$comment_count['spam'] = $row['total'];
				$comment_count["total_comments"] += $row['total'];
				break;
			case 1:
				$comment_count['approved'] = $row['total'];
				$comment_count['total_comments'] += $row['total'];
				break;
			case 0:
				$comment_count['awaiting_moderation'] = $row['total'];
				$comment_count['total_comments'] += $row['total'];
				break;
			default:
				break;
		}
	}

	return $comment_count;
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
	if ( isset($_COOKIE['comment_author_'.COOKIEHASH]) ) {
		$comment_author = apply_filters('pre_comment_author_name', $_COOKIE['comment_author_'.COOKIEHASH]);
		$comment_author = stripslashes($comment_author);
		$comment_author = attribute_escape($comment_author);
		$_COOKIE['comment_author_'.COOKIEHASH] = $comment_author;
	}

	if ( isset($_COOKIE['comment_author_email_'.COOKIEHASH]) ) {
		$comment_author_email = apply_filters('pre_comment_author_email', $_COOKIE['comment_author_email_'.COOKIEHASH]);
		$comment_author_email = stripslashes($comment_author_email);
		$comment_author_email = attribute_escape($comment_author_email);
		$_COOKIE['comment_author_email_'.COOKIEHASH] = $comment_author_email;
	}

	if ( isset($_COOKIE['comment_author_url_'.COOKIEHASH]) ) {
		$comment_author_url = apply_filters('pre_comment_author_url', $_COOKIE['comment_author_url_'.COOKIEHASH]);
		$comment_author_url = stripslashes($comment_author_url);
		$_COOKIE['comment_author_url_'.COOKIEHASH] = $comment_author_url;
	}
}

/**
 * Validates whether this comment is allowed to be made or not.
 *
 * {@internal Missing Long Description}}
 *
 * @since 2.0.0
 * @uses $wpdb
 * @uses apply_filters() Calls 'pre_comment_approved' hook on the type of comment
 * @uses do_action() Calls 'check_comment_flood' hook on $comment_author_IP, $comment_author_email, and $comment_date_gmt
 *
 * @param array $commentdata Contains information on the comment
 * @return mixed Signifies the approval status (0|1|'spam')
 */
function wp_allow_comment($commentdata) {
	global $wpdb;
	extract($commentdata, EXTR_SKIP);

	// Simple duplicate check
	// expected_slashed ($comment_post_ID, $comment_author, $comment_author_email, $comment_content)
	$dupe = "SELECT comment_ID FROM $wpdb->comments WHERE comment_post_ID = '$comment_post_ID' AND ( comment_author = '$comment_author' ";
	if ( $comment_author_email )
		$dupe .= "OR comment_author_email = '$comment_author_email' ";
	$dupe .= ") AND comment_content = '$comment_content' LIMIT 1";
	if ( $wpdb->get_var($dupe) )
		wp_die( __('Duplicate comment detected; it looks as though you\'ve already said that!') );

	do_action( 'check_comment_flood', $comment_author_IP, $comment_author_email, $comment_date_gmt );

	if ( $user_id ) {
		$userdata = get_userdata($user_id);
		$user = new WP_User($user_id);
		$post_author = $wpdb->get_var($wpdb->prepare("SELECT post_author FROM $wpdb->posts WHERE ID = %d LIMIT 1", $comment_post_ID));
	}

	if ( $userdata && ( $user_id == $post_author || $user->has_cap('moderate_comments') ) ) {
		// The author and the admins get respect.
		$approved = 1;
	 } else {
		// Everyone else's comments will be checked.
		if ( check_comment($comment_author, $comment_author_email, $comment_author_url, $comment_content, $comment_author_IP, $comment_agent, $comment_type) )
			$approved = 1;
		else
			$approved = 0;
		if ( wp_blacklist_check($comment_author, $comment_author_email, $comment_author_url, $comment_content, $comment_author_IP, $comment_agent) )
			$approved = 'spam';
	}

	$approved = apply_filters('pre_comment_approved', $approved);
	return $approved;
}

/**
 * {@internal Missing Short Description}}
 *
 * {@internal Missing Long Description}}
 *
 * @since 2.3.0
 * @uses $wpdb
 * @uses apply_filters() {@internal Missing Description}}
 * @uses do_action() {@internal Missing Description}}
 *
 * @param string $ip {@internal Missing Description}}
 * @param string $email {@internal Missing Description}}
 * @param unknown_type $date {@internal Missing Description}}
 */
function check_comment_flood_db( $ip, $email, $date ) {
	global $wpdb;
	if ( current_user_can( 'manage_options' ) )
		return; // don't throttle admins
	if ( $lasttime = $wpdb->get_var( $wpdb->prepare("SELECT comment_date_gmt FROM $wpdb->comments WHERE comment_author_IP = %s OR comment_author_email = %s ORDER BY comment_date DESC LIMIT 1", $ip, $email) ) ) {
		$time_lastcomment = mysql2date('U', $lasttime);
		$time_newcomment  = mysql2date('U', $date);
		$flood_die = apply_filters('comment_flood_filter', false, $time_lastcomment, $time_newcomment);
		if ( $flood_die ) {
			do_action('comment_flood_trigger', $time_lastcomment, $time_newcomment);
			wp_die( __('You are posting comments too quickly.  Slow down.') );
		}
	}
}

/**
 * Does comment contain blacklisted characters or words.
 *
 * {@internal Missing Long Description}}
 *
 * @since 1.5.0
 * @uses do_action() Calls 'wp_blacklist_check' hook for all parameters
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
	do_action('wp_blacklist_check', $author, $email, $url, $comment, $user_ip, $user_agent);

	if ( preg_match_all('/&#(\d+);/', $comment . $author . $url, $chars) ) {
		foreach ( (array) $chars[1] as $char ) {
			// If it's an encoded char in the normal ASCII set, reject
			if ( 38 == $char )
				continue; // Unless it's &
			if ( $char < 128 )
				return true;
		}
	}

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
 * {@internal Missing Short Description}}
 *
 * {@internal Missing Long Description}}
 *
 * @param unknown_type $post_id
 * @return unknown
 */
function wp_count_comments( $post_id = 0 ) {
	global $wpdb;

	$post_id = (int) $post_id;

	$count = wp_cache_get("comments-{$post_id}", 'counts');

	if ( false !== $count )
		return $count;

	$where = '';
	if( $post_id > 0 )
		$where = $wpdb->prepare( "WHERE comment_post_ID = %d", $post_id );

	$count = $wpdb->get_results( "SELECT comment_approved, COUNT( * ) AS num_comments FROM {$wpdb->comments} {$where} GROUP BY comment_approved", ARRAY_A );

	$total = 0;
	$stats = array( );
	$approved = array('0' => 'moderated', '1' => 'approved', 'spam' => 'spam');
	foreach( (array) $count as $row_num => $row ) {
		$total += $row['num_comments'];
		$stats[$approved[$row['comment_approved']]] = $row['num_comments'];
	}

	$stats['total_comments'] = $total;
	foreach ( $approved as $key ) {
		if ( empty($stats[$key]) )
			$stats[$key] = 0;
	}

	$stats = (object) $stats;
	wp_cache_set("comments-{$post_id}", $stats, 'counts');

	return $stats;
}

/**
 * Removes comment ID and maybe updates post comment count.
 *
 * The post comment count will be updated if the comment was approved and has a
 * post ID available.
 *
 * @since 2.0.0
 * @uses $wpdb
 * @uses do_action() Calls 'delete_comment' hook on comment ID
 * @uses do_action() Calls 'wp_set_comment_status' hook on comment ID with 'delete' set for the second parameter
 *
 * @param int $comment_id Comment ID
 * @return bool False if delete comment query failure, true on success
 */
function wp_delete_comment($comment_id) {
	global $wpdb;
	do_action('delete_comment', $comment_id);

	$comment = get_comment($comment_id);

	if ( ! $wpdb->query( $wpdb->prepare("DELETE FROM $wpdb->comments WHERE comment_ID = %d LIMIT 1", $comment_id) ) )
		return false;

	$post_id = $comment->comment_post_ID;
	if ( $post_id && $comment->comment_approved == 1 )
		wp_update_comment_count($post_id);

	clean_comment_cache($comment_id);

	do_action('wp_set_comment_status', $comment_id, 'delete');
	return true;
}

/**
 * The status of a comment by ID.
 *
 * @since 1.0.0
 *
 * @param int $comment_id Comment ID
 * @return string|bool Status might be 'deleted', 'approved', 'unapproved', 'spam'. False on failure
 */
function wp_get_comment_status($comment_id) {
	$comment = get_comment($comment_id);
	if ( !$comment )
		return false;

	$approved = $comment->comment_approved;

	if ( $approved == NULL )
		return 'deleted';
	elseif ( $approved == '1' )
		return 'approved';
	elseif ( $approved == '0' )
		return 'unapproved';
	elseif ( $approved == 'spam' )
		return 'spam';
	else
		return false;
}

/**
 * Get current commenter's name, email, and URL.
 *
 * Expects cookies content to already be sanitized. User of this function
 * might wish to recheck the returned array for validity.
 *
 * @see sanitize_comment_cookies() Use to sanitize cookies
 *
 * @since 2.0.4
 *
 * @return array Comment author, email, url respectively
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

	return compact('comment_author', 'comment_author_email', 'comment_author_url');
}

/**
 * Inserts a comment to the database.
 *
 * {@internal Missing Long Description}}
 *
 * @since 2.0.0
 * @uses $wpdb
 *
 * @param array $commentdata Contains information on the comment
 * @return int The new comment's id
 */
function wp_insert_comment($commentdata) {
	global $wpdb;
	extract(stripslashes_deep($commentdata), EXTR_SKIP);

	if ( ! isset($comment_author_IP) )
		$comment_author_IP = '';
	if ( ! isset($comment_date) )
		$comment_date = current_time('mysql');
	if ( ! isset($comment_date_gmt) )
		$comment_date_gmt = get_gmt_from_date($comment_date);
	if ( ! isset($comment_parent) )
		$comment_parent = 0;
	if ( ! isset($comment_approved) )
		$comment_approved = 1;
	if ( ! isset($user_id) )
		$user_id = 0;

	$result = $wpdb->query( $wpdb->prepare("INSERT INTO $wpdb->comments
	(comment_post_ID, comment_author, comment_author_email, comment_author_url, comment_author_IP, comment_date, comment_date_gmt, comment_content, comment_approved, comment_agent, comment_type, comment_parent, user_id)
	VALUES (%d, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %d, %d)",
	$comment_post_ID, $comment_author, $comment_author_email, $comment_author_url, $comment_author_IP, $comment_date, $comment_date_gmt, $comment_content, $comment_approved, $comment_agent, $comment_type, $comment_parent, $user_id) );

	$id = (int) $wpdb->insert_id;

	if ( $comment_approved == 1)
		wp_update_comment_count($comment_post_ID);

	return $id;
}

/**
 * Parses and returns comment information.
 *
 * Sets the comment data 'filtered' field to true when finished. This can be
 * checked as to whether the comment should be filtered and to keep from
 * filtering the same comment more than once.
 *
 * @since 2.0.0
 * @uses apply_filters() Calls 'pre_user_id' hook on comment author's user ID
 * @uses apply_filters() Calls 'pre_comment_user_agent' hook on comment author's user agent
 * @uses apply_filters() Calls 'pre_comment_author_name' hook on comment author's name
 * @uses apply_filters() Calls 'pre_comment_content' hook on the comment's content
 * @uses apply_filters() Calls 'pre_comment_user_ip' hook on comment author's IP
 * @uses apply_filters() Calls 'pre_comment_author_url' hook on comment author's URL
 * @uses apply_filters() Calls 'pre_comment_author_email' hook on comment author's email address
 *
 * @param array $commentdata Contains information on the comment
 * @return array Parsed comment information
 */
function wp_filter_comment($commentdata) {
	$commentdata['user_id']              = apply_filters('pre_user_id', $commentdata['user_ID']);
	$commentdata['comment_agent']        = apply_filters('pre_comment_user_agent', $commentdata['comment_agent']);
	$commentdata['comment_author']       = apply_filters('pre_comment_author_name', $commentdata['comment_author']);
	$commentdata['comment_content']      = apply_filters('pre_comment_content', $commentdata['comment_content']);
	$commentdata['comment_author_IP']    = apply_filters('pre_comment_user_ip', $commentdata['comment_author_IP']);
	$commentdata['comment_author_url']   = apply_filters('pre_comment_author_url', $commentdata['comment_author_url']);
	$commentdata['comment_author_email'] = apply_filters('pre_comment_author_email', $commentdata['comment_author_email']);
	$commentdata['filtered'] = true;
	return $commentdata;
}

/**
 * {@internal Missing Short Description}}
 *
 * {@internal Missing Long Description}}
 *
 * @since 2.1.0
 *
 * @param unknown_type $block {@internal Missing Description}}
 * @param unknown_type $time_lastcomment {@internal Missing Description}}
 * @param unknown_type $time_newcomment {@internal Missing Description}}
 * @return unknown {@internal Missing Description}}
 */
function wp_throttle_comment_flood($block, $time_lastcomment, $time_newcomment) {
	if ( $block ) // a plugin has already blocked... we'll let that decision stand
		return $block;
	if ( ($time_newcomment - $time_lastcomment) < 15 )
		return true;
	return false;
}

/**
 * Parses and adds a new comment to the database.
 *
 * {@internal Missing Long Description}}
 *
 * @since 1.5.0
 * @uses apply_filters() Calls 'preprocess_comment' hook on $commentdata parameter array before processing
 * @uses do_action() Calls 'comment_post' hook on $comment_ID returned from adding the comment and if the comment was approved.
 * @uses wp_filter_comment() Used to filter comment before adding comment
 * @uses wp_allow_comment() checks to see if comment is approved.
 * @uses wp_insert_comment() Does the actual comment insertion to the database
 *
 * @param array $commentdata Contains information on the comment
 * @return int The ID of the comment after adding.
 */
function wp_new_comment( $commentdata ) {
	$commentdata = apply_filters('preprocess_comment', $commentdata);

	$commentdata['comment_post_ID'] = (int) $commentdata['comment_post_ID'];
	$commentdata['user_ID']         = (int) $commentdata['user_ID'];

	$commentdata['comment_author_IP'] = preg_replace( '/[^0-9a-fA-F:., ]/', '',$_SERVER['REMOTE_ADDR'] );
	$commentdata['comment_agent']     = $_SERVER['HTTP_USER_AGENT'];

	$commentdata['comment_date']     = current_time('mysql');
	$commentdata['comment_date_gmt'] = current_time('mysql', 1);

	$commentdata = wp_filter_comment($commentdata);

	$commentdata['comment_approved'] = wp_allow_comment($commentdata);

	$comment_ID = wp_insert_comment($commentdata);

	do_action('comment_post', $comment_ID, $commentdata['comment_approved']);

	if ( 'spam' !== $commentdata['comment_approved'] ) { // If it's spam save it silently for later crunching
		if ( '0' == $commentdata['comment_approved'] )
			wp_notify_moderator($comment_ID);

		$post = &get_post($commentdata['comment_post_ID']); // Don't notify if it's your own comment

		if ( get_option('comments_notify') && $commentdata['comment_approved'] && $post->post_author != $commentdata['user_ID'] )
			wp_notify_postauthor($comment_ID, $commentdata['comment_type']);
	}

	return $comment_ID;
}

/**
 * Sets the status of comment ID.
 *
 * {@internal Missing Long Description}}
 *
 * @since 1.0.0
 *
 * @param int $comment_id Comment ID
 * @param string $comment_status New comment status, either 'hold', 'approve', 'spam', or 'delete'
 * @return bool False on failure or deletion and true on success.
 */
function wp_set_comment_status($comment_id, $comment_status) {
	global $wpdb;

	switch ( $comment_status ) {
		case 'hold':
			$query = $wpdb->prepare("UPDATE $wpdb->comments SET comment_approved='0' WHERE comment_ID = %d LIMIT 1", $comment_id);
			break;
		case 'approve':
			$query = $wpdb->prepare("UPDATE $wpdb->comments SET comment_approved='1' WHERE comment_ID = %d LIMIT 1", $comment_id);
			if ( get_option('comments_notify') ) {
				$comment = get_comment($comment_id);
				wp_notify_postauthor($comment_id, $comment->comment_type);
			}
			break;
		case 'spam':
			$query = $wpdb->prepare("UPDATE $wpdb->comments SET comment_approved='spam' WHERE comment_ID = %d LIMIT 1", $comment_id);
			break;
		case 'delete':
			return wp_delete_comment($comment_id);
			break;
		default:
			return false;
	}

	if ( !$wpdb->query($query) )
		return false;

	clean_comment_cache($comment_id);

	do_action('wp_set_comment_status', $comment_id, $comment_status);
	$comment = get_comment($comment_id);
	wp_update_comment_count($comment->comment_post_ID);

	return true;
}

/**
 * Parses and updates an existing comment in the database.
 *
 * {@internal Missing Long Description}}
 *
 * @since 2.0.0
 * @uses $wpdb
 *
 * @param array $commentarr Contains information on the comment
 * @return int Comment was updated if value is 1, or was not updated if value is 0.
 */
function wp_update_comment($commentarr) {
	global $wpdb;

	// First, get all of the original fields
	$comment = get_comment($commentarr['comment_ID'], ARRAY_A);

	// Escape data pulled from DB.
	foreach ( (array) $comment as $key => $value )
		$comment[$key] = $wpdb->escape($value);

	// Merge old and new fields with new fields overwriting old ones.
	$commentarr = array_merge($comment, $commentarr);

	$commentarr = wp_filter_comment( $commentarr );

	// Now extract the merged array.
	extract(stripslashes_deep($commentarr), EXTR_SKIP);

	$comment_content = apply_filters('comment_save_pre', $comment_content);

	$comment_date_gmt = get_gmt_from_date($comment_date);

	$wpdb->query( $wpdb->prepare("UPDATE $wpdb->comments SET
			comment_content      = %s,
			comment_author       = %s,
			comment_author_email = %s,
			comment_approved     = %s,
			comment_author_url   = %s,
			comment_date         = %s,
			comment_date_gmt     = %s
		WHERE comment_ID = %d",
			$comment_content,
			$comment_author,
			$comment_author_email,
			$comment_approved,
			$comment_author_url,
			$comment_date,
			$comment_date_gmt,
			$comment_ID) );

	$rval = $wpdb->rows_affected;

	clean_comment_cache($comment_ID);
	wp_update_comment_count($comment_post_ID);
	do_action('edit_comment', $comment_ID);
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
 * @since 2.5
 * @staticvar bool $_defer
 *
 * @param bool $defer
 * @return unknown
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
 * @param int $post_id Post ID
 * @param bool $do_deferred Whether to process previously deferred post comment counts
 * @return bool True on success, false on failure
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
 * @since 2.5
 * @uses $wpdb
 * @uses do_action() Calls 'wp_update_comment_count' hook on $post_id, $new, and $old
 * @uses do_action() Calls 'edit_posts' hook on $post_id and $post
 *
 * @param int $post_id Post ID
 * @return bool False on '0' $post_id or if post with ID does not exist. True on success.
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
	$wpdb->query( $wpdb->prepare("UPDATE $wpdb->posts SET comment_count = %d WHERE ID = %d", $new, $post_id) );

	if ( 'page' == $post->post_type )
		clean_page_cache( $post_id );
	else
		clean_post_cache( $post_id );

	do_action('wp_update_comment_count', $post_id, $new, $old);
	do_action('edit_post', $post_id, $post);

	return true;
}

//
// Ping and trackback functions.
//

/**
 * Finds a pingback server URI based on the given URL.
 *
 * {@internal Missing Long Description}}
 *
 * @since 1.5.0
 * @uses $wp_version
 *
 * @param string $url URL to ping
 * @param int $timeout_bytes Number of bytes to timeout at. Prevents big file downloads, default is 2048.
 * @return bool|string False on failure, string containing URI on success.
 */
function discover_pingback_server_uri($url, $timeout_bytes = 2048) {
	global $wp_version;

	$byte_count = 0;
	$contents = '';
	$headers = '';
	$pingback_str_dquote = 'rel="pingback"';
	$pingback_str_squote = 'rel=\'pingback\'';
	$x_pingback_str = 'x-pingback: ';

	extract(parse_url($url), EXTR_SKIP);

	if ( !isset($host) ) // Not an URL. This should never happen.
		return false;

	$path  = ( !isset($path) ) ? '/'          : $path;
	$path .= ( isset($query) ) ? '?' . $query : '';
	$port  = ( isset($port)  ) ? $port        : 80;

	// Try to connect to the server at $host
	$fp = @fsockopen($host, $port, $errno, $errstr, 2);
	if ( !$fp ) // Couldn't open a connection to $host
		return false;

	// Send the GET request
	$request = "GET $path HTTP/1.1\r\nHost: $host\r\nUser-Agent: WordPress/$wp_version \r\n\r\n";
	// ob_end_flush();
	fputs($fp, $request);

	// Let's check for an X-Pingback header first
	while ( !feof($fp) ) {
		$line = fgets($fp, 512);
		if ( trim($line) == '' )
			break;
		$headers .= trim($line)."\n";
		$x_pingback_header_offset = strpos(strtolower($headers), $x_pingback_str);
		if ( $x_pingback_header_offset ) {
			// We got it!
			preg_match('#x-pingback: (.+)#is', $headers, $matches);
			$pingback_server_url = trim($matches[1]);
			return $pingback_server_url;
		}
		if ( strpos(strtolower($headers), 'content-type: ') ) {
			preg_match('#content-type: (.+)#is', $headers, $matches);
			$content_type = trim($matches[1]);
		}
	}

	if ( preg_match('#(image|audio|video|model)/#is', $content_type) ) // Not an (x)html, sgml, or xml page, no use going further
		return false;

	while ( !feof($fp) ) {
		$line = fgets($fp, 1024);
		$contents .= trim($line);
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
				fclose($fp);
				return $pingback_server_url;
			}
		}
		$byte_count += strlen($line);
		if ( $byte_count > $timeout_bytes ) {
			// It's no use going further, there probably isn't any pingback
			// server to find in this file. (Prevents loading large files.)
			fclose($fp);
			return false;
		}
	}

	// We didn't find anything.
	fclose($fp);
	return false;
}

/**
 * {@internal Missing Short Description}}
 *
 * {@internal Missing Long Description}}
 *
 * @since 2.1.0
 * @uses $wpdb
 */
function do_all_pings() {
	global $wpdb;

	// Do pingbacks
	while ($ping = $wpdb->get_row("SELECT * FROM {$wpdb->posts}, {$wpdb->postmeta} WHERE {$wpdb->posts}.ID = {$wpdb->postmeta}.post_id AND {$wpdb->postmeta}.meta_key = '_pingme' LIMIT 1")) {
		$wpdb->query("DELETE FROM {$wpdb->postmeta} WHERE post_id = {$ping->ID} AND meta_key = '_pingme';");
		pingback($ping->post_content, $ping->ID);
	}

	// Do Enclosures
	while ($enclosure = $wpdb->get_row("SELECT * FROM {$wpdb->posts}, {$wpdb->postmeta} WHERE {$wpdb->posts}.ID = {$wpdb->postmeta}.post_id AND {$wpdb->postmeta}.meta_key = '_encloseme' LIMIT 1")) {
		$wpdb->query( $wpdb->prepare("DELETE FROM {$wpdb->postmeta} WHERE post_id = %d AND meta_key = '_encloseme';", $enclosure->ID) );
		do_enclose($enclosure->post_content, $enclosure->ID);
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
 * {@internal Missing Short Description}}
 *
 * {@internal Missing Long Description}}
 *
 * @since 1.5.0
 * @uses $wpdb
 *
 * @param int $post_id Post ID to do trackbacks on
 */
function do_trackbacks($post_id) {
	global $wpdb;

	$post = $wpdb->get_row( $wpdb->prepare("SELECT * FROM $wpdb->posts WHERE ID = %d", $post_id) );
	$to_ping = get_to_ping($post_id);
	$pinged  = get_pung($post_id);
	if ( empty($to_ping) ) {
		$wpdb->query( $wpdb->prepare("UPDATE $wpdb->posts SET to_ping = '' WHERE ID = %d", $post_id) );
		return;
	}

	if ( empty($post->post_excerpt) )
		$excerpt = apply_filters('the_content', $post->post_content);
	else
		$excerpt = apply_filters('the_excerpt', $post->post_excerpt);
	$excerpt = str_replace(']]>', ']]&gt;', $excerpt);
	$excerpt = wp_html_excerpt($excerpt, 252) . '...';

	$post_title = apply_filters('the_title', $post->post_title);
	$post_title = strip_tags($post_title);

	if ( $to_ping ) {
		foreach ( (array) $to_ping as $tb_ping ) {
			$tb_ping = trim($tb_ping);
			if ( !in_array($tb_ping, $pinged) ) {
				trackback($tb_ping, $post_title, $excerpt, $post_id);
				$pinged[] = $tb_ping;
			} else {
				$wpdb->query( $wpdb->prepare("UPDATE $wpdb->posts SET to_ping = TRIM(REPLACE(to_ping, '$tb_ping', '')) WHERE ID = %d", $post_id) );
			}
		}
	}
}

/**
 * {@internal Missing Short Description}}
 *
 * {@internal Missing Long Description}}
 *
 * @since 1.2.0
 *
 * @param int $post_id Post ID. Not actually used.
 * @return int Same as Post ID from parameter
 */
function generic_ping($post_id = 0) {
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
 * {@internal Missing Long Description}}
 *
 * @since 0.71
 * @uses $wp_version
 * @uses IXR_Client
 *
 * @param string $content {@internal Missing Description}}
 * @param int $post_ID {@internal Missing Description}}
 */
function pingback($content, $post_ID) {
	global $wp_version;
	include_once(ABSPATH . WPINC . '/class-IXR.php');

	// original code by Mort (http://mort.mine.nu:8080)
	$post_links = array();

	$pung = get_pung($post_ID);

	// Variables
	$ltrs = '\w';
	$gunk = '/#~:.?+=&%@!\-';
	$punc = '.:?\-';
	$any = $ltrs . $gunk . $punc;

	// Step 1
	// Parsing the post, external links (if any) are stored in the $post_links array
	// This regexp comes straight from phpfreaks.com
	// http://www.phpfreaks.com/quickcode/Extract_All_URLs_on_a_Page/15.php
	preg_match_all("{\b http : [$any] +? (?= [$punc] * [^$any] | $)}x", $content, $post_links_temp);

	// Step 2.
	// Walking thru the links array
	// first we get rid of links pointing to sites, not to specific files
	// Example:
	// http://dummy-weblog.org
	// http://dummy-weblog.org/
	// http://dummy-weblog.org/post.php
	// We don't wanna ping first and second types, even if they have a valid <link/>

	foreach ( $post_links_temp[0] as $link_test ) :
		if ( !in_array($link_test, $pung) && (url_to_postid($link_test) != $post_ID) // If we haven't pung it already and it isn't a link to itself
				&& !is_local_attachment($link_test) ) : // Also, let's never ping local attachments.
			$test = parse_url($link_test);
			if ( isset($test['query']) )
				$post_links[] = $link_test;
			elseif ( ($test['path'] != '/') && ($test['path'] != '') )
				$post_links[] = $link_test;
		endif;
	endforeach;

	do_action_ref_array('pre_ping', array(&$post_links, &$pung));

	foreach ( (array) $post_links as $pagelinkedto ) {
		$pingback_server_url = discover_pingback_server_uri($pagelinkedto, 2048);

		if ( $pingback_server_url ) {
			@ set_time_limit( 60 );
			 // Now, the RPC call
			$pagelinkedfrom = get_permalink($post_ID);

			// using a timeout of 3 seconds should be enough to cover slow servers
			$client = new IXR_Client($pingback_server_url);
			$client->timeout = 3;
			$client->useragent .= ' -- WordPress/' . $wp_version;

			// when set to true, this outputs debug messages by itself
			$client->debug = false;

			if ( $client->query('pingback.ping', $pagelinkedfrom, $pagelinkedto) || ( isset($client->error->code) && 48 == $client->error->code ) ) // Already registered
				add_ping( $post_ID, $pagelinkedto );
		}
	}
}

/**
 * {@internal Missing Short Description}}
 *
 * {@internal Missing Long Description}}
 *
 * @since 2.1.0
 *
 * @param unknown_type $sites {@internal Missing Description}}
 * @return unknown {@internal Missing Description}}
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
 * @uses $wpdb
 * @uses $wp_version WordPress version
 *
 * @param string $trackback_url URL to send trackbacks.
 * @param string $title Title of post
 * @param string $excerpt Excerpt of post
 * @param int $ID Post ID
 * @return mixed Database query from update
 */
function trackback($trackback_url, $title, $excerpt, $ID) {
	global $wpdb, $wp_version;

	if ( empty($trackback_url) )
		return;

	$title = urlencode($title);
	$excerpt = urlencode($excerpt);
	$blog_name = urlencode(get_option('blogname'));
	$tb_url = $trackback_url;
	$url = urlencode(get_permalink($ID));
	$query_string = "title=$title&url=$url&blog_name=$blog_name&excerpt=$excerpt";
	$trackback_url = parse_url($trackback_url);
	$http_request = 'POST ' . $trackback_url['path'] . ($trackback_url['query'] ? '?'.$trackback_url['query'] : '') . " HTTP/1.0\r\n";
	$http_request .= 'Host: '.$trackback_url['host']."\r\n";
	$http_request .= 'Content-Type: application/x-www-form-urlencoded; charset='.get_option('blog_charset')."\r\n";
	$http_request .= 'Content-Length: '.strlen($query_string)."\r\n";
	$http_request .= "User-Agent: WordPress/" . $wp_version;
	$http_request .= "\r\n\r\n";
	$http_request .= $query_string;
	if ( '' == $trackback_url['port'] )
		$trackback_url['port'] = 80;
	$fs = @fsockopen($trackback_url['host'], $trackback_url['port'], $errno, $errstr, 4);
	@fputs($fs, $http_request);
	@fclose($fs);

	$tb_url = addslashes( $tb_url );
	$wpdb->query( $wpdb->prepare("UPDATE $wpdb->posts SET pinged = CONCAT(pinged, '\n', '$tb_url') WHERE ID = %d", $ID) );
	return $wpdb->query( $wpdb->prepare("UPDATE $wpdb->posts SET to_ping = TRIM(REPLACE(to_ping, '$tb_url', '')) WHERE ID = %d", $ID) );
}

/**
 * Send a pingback.
 *
 * @since 1.2.0
 * @uses $wp_version
 * @uses IXR_Client
 *
 * @param string $server Host of blog to connect to.
 * @param string $path Path to send the ping.
 */
function weblog_ping($server = '', $path = '') {
	global $wp_version;
	include_once(ABSPATH . WPINC . '/class-IXR.php');

	// using a timeout of 3 seconds should be enough to cover slow servers
	$client = new IXR_Client($server, ((!strlen(trim($path)) || ('/' == $path)) ? false : $path));
	$client->timeout = 3;
	$client->useragent .= ' -- WordPress/'.$wp_version;

	// when set to true, this outputs debug messages by itself
	$client->debug = false;
	$home = trailingslashit( get_option('home') );
	if ( !$client->query('weblogUpdates.extendedPing', get_option('blogname'), $home, get_bloginfo('rss2_url') ) ) // then try a normal ping
		$client->query('weblogUpdates.ping', get_option('blogname'), $home);
}

//
// Cache
//

/**
 * Removes comment ID from the comment cache.
 *
 * @since 2.3.0
 * @package WordPress
 * @subpackage Cache
 *
 * @param int $id Comment ID to remove from cache
 */
function clean_comment_cache($id) {
	wp_cache_delete($id, 'comment');
}

/**
 * Updates the comment cache of given comments.
 *
 * Will add the comments in $comments to the cache. If comment ID already
 * exists in the comment cache then it will not be updated.
 *
 * The comment is added to the cache using the comment group with the key
 * using the ID of the comments.
 *
 * @since 2.3.0
 *
 * @param array $comments Array of comment row objects
 */
function update_comment_cache($comments) {
	foreach ( (array) $comments as $comment )
		wp_cache_add($comment->comment_ID, $comment, 'comment');
}

?>
