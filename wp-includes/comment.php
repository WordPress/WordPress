<?php

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
			if ( $wpdb->get_var("SELECT link_id FROM $wpdb->links WHERE link_url LIKE ('%$domain%') LIMIT 1") || $domain == $home_domain )
				return true;
			else
				return false;
		} elseif ( $author != '' && $email != '' ) {
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


function get_approved_comments($post_id) {
	global $wpdb;

	$post_id = (int) $post_id;
	return $wpdb->get_results("SELECT * FROM $wpdb->comments WHERE comment_post_ID = '$post_id' AND comment_approved = '1' ORDER BY comment_date");
}


// Retrieves comment data given a comment ID or comment object.
// Handles comment caching.
function &get_comment(&$comment, $output = OBJECT) {
	global $comment_cache, $wpdb;

	if ( empty($comment) )
		return null;

	if ( is_object($comment) ) {
		if ( !isset($comment_cache[$comment->comment_ID]) )
			$comment_cache[$comment->comment_ID] = &$comment;
		$_comment = & $comment_cache[$comment->comment_ID];
	} else {
		if ( !isset($comment_cache[$comment]) ) {
			$_comment = $wpdb->get_row("SELECT * FROM $wpdb->comments WHERE comment_ID = '$comment' LIMIT 1");
			$comment_cache[$comment->comment_ID] = & $_comment;
		} else {
			$_comment = & $comment_cache[$comment];
		}
	}

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


// Deprecate in favor of get_comment()?
function get_commentdata( $comment_ID, $no_cache = 0, $include_unapproved = false ) { // less flexible, but saves DB queries
	global $postc, $id, $commentdata, $wpdb;
	if ( $no_cache ) {
		$query = "SELECT * FROM $wpdb->comments WHERE comment_ID = '$comment_ID'";
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


function get_lastcommentmodified($timezone = 'server') {
	global $cache_lastcommentmodified, $pagenow, $wpdb;
	$add_seconds_blog = get_option('gmt_offset') * 3600;
	$add_seconds_server = date('Z');
	$now = current_time('mysql', 1);
	if ( !isset($cache_lastcommentmodified[$timezone]) ) {
		switch ( strtolower($timezone)) {
			case 'gmt':
				$lastcommentmodified = $wpdb->get_var("SELECT comment_date_gmt FROM $wpdb->comments WHERE comment_date_gmt <= '$now' ORDER BY comment_date_gmt DESC LIMIT 1");
				break;
			case 'blog':
				$lastcommentmodified = $wpdb->get_var("SELECT comment_date FROM $wpdb->comments WHERE comment_date_gmt <= '$now' ORDER BY comment_date_gmt DESC LIMIT 1");
				break;
			case 'server':
				$lastcommentmodified = $wpdb->get_var("SELECT DATE_ADD(comment_date_gmt, INTERVAL '$add_seconds_server' SECOND) FROM $wpdb->comments WHERE comment_date_gmt <= '$now' ORDER BY comment_date_gmt DESC LIMIT 1");
				break;
		}
		$cache_lastcommentmodified[$timezone] = $lastcommentmodified;
	} else {
		$lastcommentmodified = $cache_lastcommentmodified[$timezone];
	}
	return $lastcommentmodified;
}


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
		$comment_author_url = clean_url($comment_author_url);
		$_COOKIE['comment_author_url_'.COOKIEHASH] = $comment_author_url;
	}
}


function wp_allow_comment($commentdata) {
	global $wpdb;
	extract($commentdata);

	// Simple duplicate check
	$dupe = "SELECT comment_ID FROM $wpdb->comments WHERE comment_post_ID = '$comment_post_ID' AND ( comment_author = '$comment_author' ";
	if ( $comment_author_email )
		$dupe .= "OR comment_author_email = '$comment_author_email' ";
	$dupe .= ") AND comment_content = '$comment_content' LIMIT 1";
	if ( $wpdb->get_var($dupe) )
		wp_die( __('Duplicate comment detected; it looks as though you\'ve already said that!') );

	// Simple flood-protection
	if ( $lasttime = $wpdb->get_var("SELECT comment_date_gmt FROM $wpdb->comments WHERE comment_author_IP = '$comment_author_IP' OR comment_author_email = '$comment_author_email' ORDER BY comment_date DESC LIMIT 1") ) {
		$time_lastcomment = mysql2date('U', $lasttime);
		$time_newcomment  = mysql2date('U', $comment_date_gmt);
		$flood_die = apply_filters('comment_flood_filter', false, $time_lastcomment, $time_newcomment);
		if ( $flood_die ) {
			do_action('comment_flood_trigger', $time_lastcomment, $time_newcomment);
			wp_die( __('You are posting comments too quickly.  Slow down.') );
		}
	}

	if ( $user_id ) {
		$userdata = get_userdata($user_id);
		$user = new WP_User($user_id);
		$post_author = $wpdb->get_var("SELECT post_author FROM $wpdb->posts WHERE ID = '$comment_post_ID' LIMIT 1");
	}

	if ( $userdata && ( $user_id == $post_author || $user->has_cap('level_9') ) ) {
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


function wp_blacklist_check($author, $email, $url, $comment, $user_ip, $user_agent) {
	global $wpdb;

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


function wp_delete_comment($comment_id) {
	global $wpdb;
	do_action('delete_comment', $comment_id);

	$comment = get_comment($comment_id);

	if ( ! $wpdb->query("DELETE FROM $wpdb->comments WHERE comment_ID='$comment_id' LIMIT 1") )
		return false;

	$post_id = $comment->comment_post_ID;
	if ( $post_id && $comment->comment_approved == 1 )
		wp_update_comment_count($post_id);

	do_action('wp_set_comment_status', $comment_id, 'delete');
	return true;
}


function wp_get_comment_status($comment_id) {
	global $wpdb;

	$result = $wpdb->get_var("SELECT comment_approved FROM $wpdb->comments WHERE comment_ID='$comment_id' LIMIT 1");

	if ( $result == NULL )
		return 'deleted';
	elseif ( $result == '1' )
		return 'approved';
	elseif ( $result == '0' )
		return 'unapproved';
	elseif ( $result == 'spam' )
		return 'spam';
	else
		return false;
}


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


function wp_insert_comment($commentdata) {
	global $wpdb;
	extract($commentdata);

	if ( ! isset($comment_author_IP) )
		$comment_author_IP = preg_replace( '/[^0-9., ]/', '',$_SERVER['REMOTE_ADDR'] );
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

	$result = $wpdb->query("INSERT INTO $wpdb->comments
	(comment_post_ID, comment_author, comment_author_email, comment_author_url, comment_author_IP, comment_date, comment_date_gmt, comment_content, comment_approved, comment_agent, comment_type, comment_parent, user_id)
	VALUES
	('$comment_post_ID', '$comment_author', '$comment_author_email', '$comment_author_url', '$comment_author_IP', '$comment_date', '$comment_date_gmt', '$comment_content', '$comment_approved', '$comment_agent', '$comment_type', '$comment_parent', '$user_id')
	");

	$id = (int) $wpdb->insert_id;

	if ( $comment_approved == 1)
		wp_update_comment_count($comment_post_ID);

	return $id;
}


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


function wp_throttle_comment_flood($block, $time_lastcomment, $time_newcomment) {
	if ( $block ) // a plugin has already blocked... we'll let that decision stand
		return $block;
	if ( ($time_newcomment - $time_lastcomment) < 15 )
		return true;
	return false;
}


function wp_new_comment( $commentdata ) {
	$commentdata = apply_filters('preprocess_comment', $commentdata);

	$commentdata['comment_post_ID'] = (int) $commentdata['comment_post_ID'];
	$commentdata['user_ID']         = (int) $commentdata['user_ID'];

	$commentdata['comment_author_IP'] = preg_replace( '/[^0-9., ]/', '',$_SERVER['REMOTE_ADDR'] );
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


function wp_set_comment_status($comment_id, $comment_status) {
	global $wpdb;

	switch ( $comment_status ) {
		case 'hold':
			$query = "UPDATE $wpdb->comments SET comment_approved='0' WHERE comment_ID='$comment_id' LIMIT 1";
			break;
		case 'approve':
			$query = "UPDATE $wpdb->comments SET comment_approved='1' WHERE comment_ID='$comment_id' LIMIT 1";
			break;
		case 'spam':
			$query = "UPDATE $wpdb->comments SET comment_approved='spam' WHERE comment_ID='$comment_id' LIMIT 1";
			break;
		case 'delete':
			return wp_delete_comment($comment_id);
			break;
		default:
			return false;
	}

	if ( !$wpdb->query($query) )
		return false;

	do_action('wp_set_comment_status', $comment_id, $comment_status);
	$comment = get_comment($comment_id);
	wp_update_comment_count($comment->comment_post_ID);
	return true;
}


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
	extract($commentarr);

	$comment_content = apply_filters('comment_save_pre', $comment_content);

	$comment_date_gmt = get_gmt_from_date($comment_date);

	$result = $wpdb->query(
		"UPDATE $wpdb->comments SET
			comment_content      = '$comment_content',
			comment_author       = '$comment_author',
			comment_author_email = '$comment_author_email',
			comment_approved     = '$comment_approved',
			comment_author_url   = '$comment_author_url',
			comment_date         = '$comment_date',
			comment_date_gmt     = '$comment_date_gmt'
		WHERE comment_ID = $comment_ID" );

	$rval = $wpdb->rows_affected;
	wp_update_comment_count($comment_post_ID);
	do_action('edit_comment', $comment_ID);
	return $rval;
}


function wp_update_comment_count($post_id) {
	global $wpdb, $comment_count_cache;
	$post_id = (int) $post_id;
	if ( !$post_id )
		return false;
	$count = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->comments WHERE comment_post_ID = '$post_id' AND comment_approved = '1'");
	$wpdb->query("UPDATE $wpdb->posts SET comment_count = $count WHERE ID = '$post_id'");
	$comment_count_cache[$post_id] = $count;

	$post = get_post($post_id);
	if ( 'page' == $post->post_type )
		clean_page_cache( $post_id );
	else
		clean_post_cache( $post_id );

	do_action('edit_post', $post_id);

	return true;
}


//
// Ping and trackback functions.
//

function discover_pingback_server_uri($url, $timeout_bytes = 2048) {
	global $wp_version;

	$byte_count = 0;
	$contents = '';
	$headers = '';
	$pingback_str_dquote = 'rel="pingback"';
	$pingback_str_squote = 'rel=\'pingback\'';
	$x_pingback_str = 'x-pingback: ';
	$pingback_href_original_pos = 27;

	extract(parse_url($url));

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
			if ( $pingback_server_url_len > 0 ) // We got it!
				return $pingback_server_url;
		}
		$byte_count += strlen($line);
		if ( $byte_count > $timeout_bytes ) {
			// It's no use going further, there probably isn't any pingback
			// server to find in this file. (Prevents loading large files.)
			return false;
		}
	}

	// We didn't find anything.
	return false;
}


function do_all_pings() {
	global $wpdb;

	// Do pingbacks
	while ($ping = $wpdb->get_row("SELECT * FROM {$wpdb->posts}, {$wpdb->postmeta} WHERE {$wpdb->posts}.ID = {$wpdb->postmeta}.post_id AND {$wpdb->postmeta}.meta_key = '_pingme' LIMIT 1")) {
		$wpdb->query("DELETE FROM {$wpdb->postmeta} WHERE post_id = {$ping->ID} AND meta_key = '_pingme';");
		pingback($ping->post_content, $ping->ID);
	}

	// Do Enclosures
	while ($enclosure = $wpdb->get_row("SELECT * FROM {$wpdb->posts}, {$wpdb->postmeta} WHERE {$wpdb->posts}.ID = {$wpdb->postmeta}.post_id AND {$wpdb->postmeta}.meta_key = '_encloseme' LIMIT 1")) {
		$wpdb->query("DELETE FROM {$wpdb->postmeta} WHERE post_id = {$enclosure->ID} AND meta_key = '_encloseme';");
		do_enclose($enclosure->post_content, $enclosure->ID);
	}

	// Do Trackbacks
	$trackbacks = $wpdb->get_results("SELECT ID FROM $wpdb->posts WHERE CHAR_LENGTH(TRIM(to_ping)) > 7 AND post_status = 'publish'");
	if ( is_array($trackbacks) ) {
		foreach ( $trackbacks as $trackback )
			do_trackbacks($trackback->ID);
	}

	//Do Update Services/Generic Pings
	generic_ping();
}

function do_trackbacks($post_id) {
	global $wpdb;

	$post = $wpdb->get_row("SELECT * FROM $wpdb->posts WHERE ID = $post_id");
	$to_ping = get_to_ping($post_id);
	$pinged  = get_pung($post_id);
	if ( empty($to_ping) ) {
		$wpdb->query("UPDATE $wpdb->posts SET to_ping = '' WHERE ID = '$post_id'");
		return;
	}

	if ( empty($post->post_excerpt) )
		$excerpt = apply_filters('the_content', $post->post_content);
	else
		$excerpt = apply_filters('the_excerpt', $post->post_excerpt);
	$excerpt = str_replace(']]>', ']]&gt;', $excerpt);
	$excerpt = strip_tags($excerpt);
	if ( function_exists('mb_strcut') ) // For international trackbacks
    	$excerpt = mb_strcut($excerpt, 0, 252, get_option('blog_charset')) . '...';
	else
		$excerpt = substr($excerpt, 0, 252) . '...';

	$post_title = apply_filters('the_title', $post->post_title);
	$post_title = strip_tags($post_title);

	if ( $to_ping ) {
		foreach ( (array) $to_ping as $tb_ping ) {
			$tb_ping = trim($tb_ping);
			if ( !in_array($tb_ping, $pinged) ) {
				trackback($tb_ping, $post_title, $excerpt, $post_id);
				$pinged[] = $tb_ping;
			} else {
				$wpdb->query("UPDATE $wpdb->posts SET to_ping = TRIM(REPLACE(to_ping, '$tb_ping', '')) WHERE ID = '$post_id'");
			}
		}
	}
}


function generic_ping($post_id = 0) {
	$services = get_option('ping_sites');
	$services = preg_replace("|(\s)+|", '$1', $services); // Kill dupe lines
	$services = trim($services);
	if ( '' != $services ) {
		$services = explode("\n", $services);
		foreach ( (array) $services as $service )
			weblog_ping($service);
	}

	return $post_id;
}


function pingback($content, $post_ID) {
	global $wp_version, $wpdb;
	include_once(ABSPATH . WPINC . '/class-IXR.php');

	// original code by Mort (http://mort.mine.nu:8080)
	$log = debug_fopen(ABSPATH . '/pingback.log', 'a');
	$post_links = array();
	debug_fwrite($log, 'BEGIN ' . date('YmdHis', time()) . "\n");

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

	// Debug
	debug_fwrite($log, 'Post contents:');
	debug_fwrite($log, $content."\n");

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
		debug_fwrite($log, "Processing -- $pagelinkedto\n");
		$pingback_server_url = discover_pingback_server_uri($pagelinkedto, 2048);

		if ( $pingback_server_url ) {
			@ set_time_limit( 60 );
			 // Now, the RPC call
			debug_fwrite($log, "Page Linked To: $pagelinkedto \n");
			debug_fwrite($log, 'Page Linked From: ');
			$pagelinkedfrom = get_permalink($post_ID);
			debug_fwrite($log, $pagelinkedfrom."\n");

			// using a timeout of 3 seconds should be enough to cover slow servers
			$client = new IXR_Client($pingback_server_url);
			$client->timeout = 3;
			$client->useragent .= ' -- WordPress/' . $wp_version;

			// when set to true, this outputs debug messages by itself
			$client->debug = false;

			if ( $client->query('pingback.ping', $pagelinkedfrom, $pagelinkedto ) )
				add_ping( $post_ID, $pagelinkedto );
			else
				debug_fwrite($log, "Error.\n Fault code: ".$client->getErrorCode()." : ".$client->getErrorMessage()."\n");
		}
	}

	debug_fwrite($log, "\nEND: ".time()."\n****************************\n");
	debug_fclose($log);
}


function privacy_ping_filter($sites) {
	if ( '0' != get_option('blog_public') )
		return $sites;
	else
		return '';
}

// Send a Trackback
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
/*
	$debug_file = 'trackback.log';
	$fp = fopen($debug_file, 'a');
	fwrite($fp, "\n*****\nRequest:\n\n$http_request\n\nResponse:\n\n");
	while(!@feof($fs)) {
		fwrite($fp, @fgets($fs, 4096));
	}
	fwrite($fp, "\n\n");
	fclose($fp);
*/
	@fclose($fs);

	$tb_url = addslashes( $tb_url );
	$wpdb->query("UPDATE $wpdb->posts SET pinged = CONCAT(pinged, '\n', '$tb_url') WHERE ID = '$ID'");
	return $wpdb->query("UPDATE $wpdb->posts SET to_ping = TRIM(REPLACE(to_ping, '$tb_url', '')) WHERE ID = '$ID'");
}


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

?>
