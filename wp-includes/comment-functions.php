<?php

// Template functions

function comments_template( $file = '/comments.php' ) {
	global $wp_query, $withcomments, $post, $wpdb, $id, $comment, $user_login, $user_ID, $user_identity;

	if ( is_single() || is_page() || $withcomments ) :
		$req = get_settings('require_name_email');
		$comment_author = isset($_COOKIE['comment_author_'.COOKIEHASH]) ? trim(stripslashes($_COOKIE['comment_author_'.COOKIEHASH])) : '';
		$comment_author_email = isset($_COOKIE['comment_author_email_'.COOKIEHASH]) ? trim(stripslashes($_COOKIE['comment_author_email_'.COOKIEHASH])) : '';
		$comment_author_url = isset($_COOKIE['comment_author_url_'.COOKIEHASH]) ? trim(stripslashes($_COOKIE['comment_author_url_'.COOKIEHASH])) : '';
	if ( empty($comment_author) ) {
		$comments = $wpdb->get_results("SELECT * FROM $wpdb->comments WHERE comment_post_ID = '$post->ID' AND comment_approved = '1' ORDER BY comment_date");
	} else {
		$author_db = $wpdb->escape($comment_author);
		$email_db  = $wpdb->escape($comment_author_email);
		$comments = $wpdb->get_results("SELECT * FROM $wpdb->comments WHERE comment_post_ID = '$post->ID' AND ( comment_approved = '1' OR ( comment_author = '$author_db' AND comment_author_email = '$email_db' AND comment_approved = '0' ) ) ORDER BY comment_date");
	}

	define('COMMENTS_TEMPLATE', true);
	$include = apply_filters('comments_template', TEMPLATEPATH . $file );
	if ( file_exists( $include ) )
		require( $include );
	else
		require( ABSPATH . 'wp-content/themes/default/comments.php');

	endif;
}

function wp_new_comment( $commentdata ) {
	$commentdata = apply_filters('preprocess_comment', $commentdata);

	$commentdata['comment_post_ID'] = (int) $commentdata['comment_post_ID'];
	$commentdata['user_ID']         = (int) $commentdata['user_ID'];

	$commentdata['comment_author_IP'] = $_SERVER['REMOTE_ADDR'];
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

		if ( get_settings('comments_notify') && $commentdata['comment_approved'] && $post->post_author != $commentdata['user_ID'] )
			wp_notify_postauthor($comment_ID, $commentdata['comment_type']);
	}

	return $comment_ID;
}

function wp_insert_comment($commentdata) {
	global $wpdb;
	extract($commentdata);

	if ( ! isset($comment_author_IP) )
		$comment_author_IP = $_SERVER['REMOTE_ADDR'];
	if ( ! isset($comment_date) )
		$comment_date = current_time('mysql');
	if ( ! isset($comment_date_gmt) )
		$comment_date_gmt = gmdate('Y-m-d H:i:s', strtotime($comment_date) );
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

	$id = $wpdb->insert_id;

	if ( $comment_approved == 1) {
		$count = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->comments WHERE comment_post_ID = '$comment_post_ID' AND comment_approved = '1'");
		$wpdb->query( "UPDATE $wpdb->posts SET comment_count = $count WHERE ID = '$comment_post_ID'" );
	}
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

function wp_allow_comment($commentdata) {
	global $wpdb;
	extract($commentdata);

	$comment_user_domain = apply_filters('pre_comment_user_domain', gethostbyaddr($comment_author_IP) );

	// Simple duplicate check
	$dupe = "SELECT comment_ID FROM $wpdb->comments WHERE comment_post_ID = '$comment_post_ID' AND ( comment_author = '$comment_author' ";
	if ( $comment_author_email )
		$dupe .= "OR comment_author_email = '$comment_author_email' ";
	$dupe .= ") AND comment_content = '$comment_content' LIMIT 1";
	if ( $wpdb->get_var($dupe) )
		die( __('Duplicate comment detected; it looks as though you\'ve already said that!') );

	// Simple flood-protection
	if ( $lasttime = $wpdb->get_var("SELECT comment_date_gmt FROM $wpdb->comments WHERE comment_author_IP = '$comment_author_IP' OR comment_author_email = '$comment_author_email' ORDER BY comment_date DESC LIMIT 1") ) {
		$time_lastcomment = mysql2date('U', $lasttime);
		$time_newcomment  = mysql2date('U', $comment_date_gmt);
		if ( ($time_newcomment - $time_lastcomment) < 15 ) {
			do_action('comment_flood_trigger', $time_lastcomment, $time_newcomment);
			die( __('Sorry, you can only post a new comment once every 15 seconds. Slow down cowboy.') );
		}
	}

	if ( $user_id ) {
		$userdata = get_userdata($user_id);
		$user = new WP_User($user_id);
		$post_author = $wpdb->get_var("SELECT post_author FROM $wpdb->posts WHERE ID = '$comment_post_ID' LIMIT 1");
	}

	// The author and the admins get respect.
	if ( $userdata && ( $user_id == $post_author || $user->has_cap('level_9') ) ) {
		$approved = 1;
	}

	// Everyone else's comments will be checked.
	else {
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


function wp_update_comment($commentarr) {
	global $wpdb;

	// First, get all of the original fields
	$comment = get_comment($commentarr['comment_ID'], ARRAY_A);

	// Escape data pulled from DB.
	foreach ($comment as $key => $value)
		$comment[$key] = $wpdb->escape($value);

	// Merge old and new fields with new fields overwriting old ones.
	$commentarr = array_merge($comment, $commentarr);

	// Now extract the merged array.
	extract($commentarr);

	$comment_content = apply_filters('comment_save_pre', $comment_content);

	$result = $wpdb->query(
		"UPDATE $wpdb->comments SET
			comment_content = '$comment_content',
			comment_author = '$comment_author',
			comment_author_email = '$comment_author_email',
			comment_approved = '$comment_approved',
			comment_author_url = '$comment_author_url',
			comment_date = '$comment_date'
		WHERE comment_ID = $comment_ID" );

	$rval = $wpdb->rows_affected;

	$c = $wpdb->get_row( "SELECT count(*) as c FROM {$wpdb->comments} WHERE comment_post_ID = '$comment_post_ID' AND comment_approved = '1'" );
	if( is_object( $c ) )
		$wpdb->query( "UPDATE $wpdb->posts SET comment_count = '$c->c' WHERE ID = '$comment_post_ID'" );

	do_action('edit_comment', $comment_ID);

	return $rval;
}

function wp_delete_comment($comment_id) {
	global $wpdb;
	do_action('delete_comment', $comment_id);

	$comment = get_comment($comment_id);

	if ( ! $wpdb->query("DELETE FROM $wpdb->comments WHERE comment_ID='$comment_id' LIMIT 1") )
		return false;

	$post_id = $comment->comment_post_ID;
	if ( $post_id && $comment->comment_approved == 1 )
		$wpdb->query( "UPDATE $wpdb->posts SET comment_count = comment_count - 1 WHERE ID = '$post_id'" );

	do_action('wp_set_comment_status', $comment_id, 'delete');
	return true;
}

function clean_url( $url ) {
	if ('' == $url) return $url;
	$url = preg_replace('|[^a-z0-9-~+_.?#=&;,/:]|i', '', $url);
	$url = str_replace(';//', '://', $url);
	$url = (!strstr($url, '://')) ? 'http://'.$url : $url;
	$url = preg_replace('/&([^#])(?![a-z]{2,8};)/', '&#038;$1', $url);
	return $url;
}

function get_comments_number( $post_id = 0 ) {
	global $wpdb, $comment_count_cache, $id;
	$post_id = (int) $post_id;

	if ( !$post_id )
		$post_id = $id;

	if ( !isset($comment_count_cache[$post_id]) )
		$comment_count_cache[$id] = $wpdb->get_var("SELECT comment_count FROM $wpdb->posts WHERE ID = '$post_id'");

	return apply_filters('get_comments_number', $comment_count_cache[$post_id]);
}

function comments_number( $zero = 'No Comments', $one = '1 Comment', $more = '% Comments', $number = '' ) {
	global $id, $comment;
	$number = get_comments_number( $id );
	if ($number == 0) {
		$blah = $zero;
	} elseif ($number == 1) {
		$blah = $one;
	} elseif ($number  > 1) {
		$blah = str_replace('%', $number, $more);
	}
	echo apply_filters('comments_number', $blah);
}

function get_comments_link() {
	return get_permalink() . '#comments';
}

function get_comment_link() {
	global $comment;
	return get_permalink( $comment->comment_post_ID ) . '#comment-' . $comment->comment_ID;
}

function comments_link( $file = '', $echo = true ) {
    echo get_comments_link();
}

function comments_popup_script($width=400, $height=400, $file='') {
    global $wpcommentspopupfile, $wptrackbackpopupfile, $wppingbackpopupfile, $wpcommentsjavascript;

		if (empty ($file)) {
			$wpcommentspopupfile = '';  // Use the index.
		} else {
			$wpcommentspopupfile = $file;
		}

    $wpcommentsjavascript = 1;
    $javascript = "<script type='text/javascript'>\nfunction wpopen (macagna) {\n    window.open(macagna, '_blank', 'width=$width,height=$height,scrollbars=yes,status=yes');\n}\n</script>\n";
    echo $javascript;
}

function comments_popup_link($zero='No Comments', $one='1 Comment', $more='% Comments', $CSSclass='', $none='Comments Off') {
	global $id, $wpcommentspopupfile, $wpcommentsjavascript, $post, $wpdb;
	global $comment_count_cache;

	if (! is_single() && ! is_page()) {
	if ( !isset($comment_count_cache[$id]) )
		$comment_count_cache[$id] = $wpdb->get_var("SELECT COUNT(comment_ID) FROM $wpdb->comments WHERE comment_post_ID = $id AND comment_approved = '1';");

	$number = $comment_count_cache[$id];

	if (0 == $number && 'closed' == $post->comment_status && 'closed' == $post->ping_status) {
		echo $none;
		return;
	} else {
		if (!empty($post->post_password)) { // if there's a password
			if ($_COOKIE['wp-postpass_'.COOKIEHASH] != $post->post_password) {  // and it doesn't match the cookie
				echo(__('Enter your password to view comments'));
				return;
			}
		}
		echo '<a href="';
		if ($wpcommentsjavascript) {
			if ( empty($wpcommentspopupfile) )
				$home = get_settings('home');
			else
				$home = get_settings('siteurl');
			echo $home . '/' . $wpcommentspopupfile.'?comments_popup='.$id;
			echo '" onclick="wpopen(this.href); return false"';
		} else { // if comments_popup_script() is not in the template, display simple comment link
			if ( 0 == $number )
				echo get_permalink() . '#respond';
			else
				comments_link();
			echo '"';
		}
		if (!empty($CSSclass)) {
			echo ' class="'.$CSSclass.'"';
		}
		echo ' title="' . sprintf( __('Comment on %s'), $post->post_title ) .'">';
		comments_number($zero, $one, $more, $number);
		echo '</a>';
	}
	}
}

function get_comment_ID() {
	global $comment;
	return apply_filters('get_comment_ID', $comment->comment_ID);
}

function comment_ID() {
	echo get_comment_ID();
}

function get_comment_author() {
	global $comment;
	if ( empty($comment->comment_author) )
		$author = __('Anonymous');
	else
		$author = $comment->comment_author;
	return apply_filters('get_comment_author', $author);
}

function comment_author() {
	$author = apply_filters('comment_author', get_comment_author() );
	echo $author;
}

function get_comment_author_email() {
	global $comment;
	return apply_filters('get_comment_author_email', $comment->comment_author_email);
}

function comment_author_email() {
	echo apply_filters('author_email', get_comment_author_email() );
}

function get_comment_author_link() {
	global $comment;
	$url    = get_comment_author_url();
	$author = get_comment_author();

	if ( empty( $url ) || 'http://' == $url )
		$return = $author;
	else
		$return = "<a href='$url' rel='external nofollow'>$author</a>";
	return apply_filters('get_comment_author_link', $return);
}

function comment_author_link() {
	echo get_comment_author_link();
}

function get_comment_type() {
	global $comment;

	if ( '' == $comment->comment_type )
		$comment->comment_type = 'comment';

	return apply_filters('get_comment_type', $comment->comment_type);
}

function comment_type($commenttxt = 'Comment', $trackbacktxt = 'Trackback', $pingbacktxt = 'Pingback') {
	$type = get_comment_type();
	switch( $type ) {
		case 'trackback' :
			echo $trackbacktxt;
			break;
		case 'pingback' :
			echo $pingbacktxt;
			break;
		default :
			echo $commenttxt;
	}
}

function get_comment_author_url() {
	global $comment;
	return apply_filters('get_comment_author_url', $comment->comment_author_url);
}

function comment_author_url() {
	echo apply_filters('comment_url', get_comment_author_url());
}

function comment_author_email_link($linktext='', $before='', $after='') {
	global $comment;
	$email = apply_filters('comment_email', $comment->comment_author_email);
	if ((!empty($email)) && ($email != '@')) {
	$display = ($linktext != '') ? $linktext : $email;
		echo $before;
		echo "<a href='mailto:$email'>$display</a>";
		echo $after;
	}
}

function get_comment_author_url_link( $linktext = '', $before = '', $after = '' ) {
	global $comment;
	$url = get_comment_author_url();
	$display = ($linktext != '') ? $linktext : $url;
	$return = "$before<a href='$url' rel='external'>$display</a>$after";
	return apply_filters('get_comment_author_url_link', $return);
}

function comment_author_url_link( $linktext = '', $before = '', $after = '' ) {
	echo get_comment_author_url_link( $linktext, $before, $after );
}

function get_comment_author_IP() {
	global $comment;
	return apply_filters('get_comment_author_IP', $comment->comment_author_IP);
}

function comment_author_IP() {
	echo get_comment_author_IP();
}

function get_comment_text() {
	global $comment;
	return apply_filters('get_comment_text', $comment->comment_content);
}

function comment_text() {
	echo apply_filters('comment_text', get_comment_text() );
}

function get_comment_excerpt() {
	global $comment;
	$comment_text = strip_tags($comment->comment_content);
	$blah = explode(' ', $comment_text);
	if (count($blah) > 20) {
		$k = 20;
		$use_dotdotdot = 1;
	} else {
		$k = count($blah);
		$use_dotdotdot = 0;
	}
	$excerpt = '';
	for ($i=0; $i<$k; $i++) {
		$excerpt .= $blah[$i] . ' ';
	}
	$excerpt .= ($use_dotdotdot) ? '...' : '';
	return apply_filters('get_comment_excerpt', $excerpt);
}

function comment_excerpt() {
	echo apply_filters('comment_excerpt', get_comment_excerpt() );
}

function get_comment_date( $d = '' ) {
	global $comment;
	if ( '' == $d )
		$date = mysql2date( get_settings('date_format'), $comment->comment_date);
	else
		$date = mysql2date($d, $comment->comment_date);
	return apply_filters('get_comment_date', $date);
}

function comment_date( $d = '' ) {
	echo get_comment_date( $d );
}

function get_comment_time( $d = '', $gmt = false ) {
	global $comment;
	$comment_date = $gmt? $comment->comment_date_gmt : $comment->comment_date;
	if ( '' == $d )
		$date = mysql2date(get_settings('time_format'), $comment_date);
	else
		$date = mysql2date($d, $comment_date);
	return apply_filters('get_comment_time', $date);
}

function comment_time( $d = '' ) {
	echo get_comment_time($d);
}

function get_trackback_url() {
	global $id;
	$tb_url = get_settings('siteurl') . '/wp-trackback.php?p=' . $id;

	if ( '' != get_settings('permalink_structure') )
		$tb_url = trailingslashit(get_permalink()) . 'trackback/';

	return $tb_url;
}
function trackback_url( $display = true ) {
	if ( $display)
		echo get_trackback_url();
	else
		return get_trackback_url();
}

function trackback_rdf($timezone = 0) {
	global $id;
	if (!stristr($_SERVER['HTTP_USER_AGENT'], 'W3C_Validator')) {
	echo '<rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" 
	    xmlns:dc="http://purl.org/dc/elements/1.1/"
	    xmlns:trackback="http://madskills.com/public/xml/rss/module/trackback/">
		<rdf:Description rdf:about="';
	the_permalink();
	echo '"'."\n";
	echo '    dc:identifier="';
	the_permalink();
	echo '"'."\n";
	echo '    dc:title="'.str_replace('--', '&#x2d;&#x2d;', wptexturize(strip_tags(get_the_title()))).'"'."\n";
	echo '    trackback:ping="'.trackback_url(0).'"'." />\n";
	echo '</rdf:RDF>';
	}
}

function comments_open() {
	global $post;
	if ( 'open' == $post->comment_status )
		return true;
	else
		return false;
}

function pings_open() {
	global $post;
	if ( 'open' == $post->ping_status ) 
		return true;
	else
		return false;
}

// Non-template functions

function get_lastcommentmodified($timezone = 'server') {
	global $cache_lastcommentmodified, $pagenow, $wpdb;
	$add_seconds_blog = get_settings('gmt_offset') * 3600;
	$add_seconds_server = date('Z');
	$now = current_time('mysql', 1);
	if ( !isset($cache_lastcommentmodified[$timezone]) ) {
		switch(strtolower($timezone)) {
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

function get_commentdata( $comment_ID, $no_cache = 0, $include_unapproved = false ) { // less flexible, but saves DB queries
	global $postc, $id, $commentdata, $wpdb;
	if ($no_cache) {
		$query = "SELECT * FROM $wpdb->comments WHERE comment_ID = '$comment_ID'";
		if (false == $include_unapproved) {
		    $query .= " AND comment_approved = '1'";
		}
    		$myrow = $wpdb->get_row($query, ARRAY_A);
	} else {
		$myrow['comment_ID'] = $postc->comment_ID;
		$myrow['comment_post_ID'] = $postc->comment_post_ID;
		$myrow['comment_author'] = $postc->comment_author;
		$myrow['comment_author_email'] = $postc->comment_author_email;
		$myrow['comment_author_url'] = $postc->comment_author_url;
		$myrow['comment_author_IP'] = $postc->comment_author_IP;
		$myrow['comment_date'] = $postc->comment_date;
		$myrow['comment_content'] = $postc->comment_content;
		$myrow['comment_karma'] = $postc->comment_karma;
		$myrow['comment_approved'] = $postc->comment_approved;
		$myrow['comment_type'] = $postc->comment_type;
	}
	return $myrow;
}

function pingback($content, $post_ID) {
	global $wp_version, $wpdb;
	include_once (ABSPATH . WPINC . '/class-IXR.php');

	// original code by Mort (http://mort.mine.nu:8080)
	$log = debug_fopen(ABSPATH . '/pingback.log', 'a');
	$post_links = array();
	debug_fwrite($log, 'BEGIN '.date('YmdHis', time())."\n");

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

	foreach($post_links_temp[0] as $link_test) :
		if ( !in_array($link_test, $pung) && (url_to_postid($link_test) != $post_ID) // If we haven't pung it already and it isn't a link to itself
				&& !is_local_attachment($link_test) ) : // Also, let's never ping local attachments.
			$test = parse_url($link_test);
			if (isset($test['query']))
				$post_links[] = $link_test;
			elseif(($test['path'] != '/') && ($test['path'] != ''))
				$post_links[] = $link_test;
		endif;
	endforeach;

	do_action('pre_ping',  array(&$post_links, &$pung));

	foreach ($post_links as $pagelinkedto){
		debug_fwrite($log, "Processing -- $pagelinkedto\n");
		$pingback_server_url = discover_pingback_server_uri($pagelinkedto, 2048);

		if ($pingback_server_url) {
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

	if (!isset($host)) {
		// Not an URL. This should never happen.
		return false;
	}

	$path  = (!isset($path)) ? '/'        : $path;
	$path .= (isset($query)) ? '?'.$query : '';
	$port  = (isset($port))  ? $port      : 80;

	// Try to connect to the server at $host
	$fp = @fsockopen($host, $port, $errno, $errstr, 2);
	if (!$fp) {
		// Couldn't open a connection to $host;
		return false;
	}

	// Send the GET request
	$request = "GET $path HTTP/1.1\r\nHost: $host\r\nUser-Agent: WordPress/$wp_version \r\n\r\n";
//	ob_end_flush();
	fputs($fp, $request);

	// Let's check for an X-Pingback header first
	while (!feof($fp)) {
		$line = fgets($fp, 512);
		if (trim($line) == '') {
			break;
		}
		$headers .= trim($line)."\n";
		$x_pingback_header_offset = strpos(strtolower($headers), $x_pingback_str);
		if ($x_pingback_header_offset) {
			// We got it!
			preg_match('#x-pingback: (.+)#is', $headers, $matches);
			$pingback_server_url = trim($matches[1]);
			return $pingback_server_url;
		}
		if(strpos(strtolower($headers), 'content-type: ')) {
			preg_match('#content-type: (.+)#is', $headers, $matches);
			$content_type = trim($matches[1]);
		}
	}

	if (preg_match('#(image|audio|video|model)/#is', $content_type)) {
		// Not an (x)html, sgml, or xml page, no use going further
		return false;
	}

	while (!feof($fp)) {
		$line = fgets($fp, 1024);
		$contents .= trim($line);
		$pingback_link_offset_dquote = strpos($contents, $pingback_str_dquote);
		$pingback_link_offset_squote = strpos($contents, $pingback_str_squote);
		if ($pingback_link_offset_dquote || $pingback_link_offset_squote) {
			$quote = ($pingback_link_offset_dquote) ? '"' : '\'';
			$pingback_link_offset = ($quote=='"') ? $pingback_link_offset_dquote : $pingback_link_offset_squote;
			$pingback_href_pos = @strpos($contents, 'href=', $pingback_link_offset);
			$pingback_href_start = $pingback_href_pos+6;
			$pingback_href_end = @strpos($contents, $quote, $pingback_href_start);
			$pingback_server_url_len = $pingback_href_end - $pingback_href_start;
			$pingback_server_url = substr($contents, $pingback_href_start, $pingback_server_url_len);
			// We may find rel="pingback" but an incomplete pingback URI
			if ($pingback_server_url_len > 0) {
				// We got it!
				return $pingback_server_url;
			}
		}
		$byte_count += strlen($line);
		if ($byte_count > $timeout_bytes) {
			// It's no use going further, there probably isn't any pingback
			// server to find in this file. (Prevents loading large files.)
			return false;
		}
	}

	// We didn't find anything.
	return false;
}

function is_local_attachment($url) {
	if ( !strstr($url, get_bloginfo('home') ) )
		return false;
	if ( strstr($url, get_bloginfo('home') . '/?attachment_id=') )
		return true;
	if ( $id = url_to_postid($url) ) {
		$post = & get_post($id);
		if ( 'attachment' == $post->post_type )
			return true;
	}
	return false;
}

function wp_set_comment_status($comment_id, $comment_status) {
    global $wpdb;

    switch($comment_status) {
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
    
    if ($wpdb->query($query)) {
		do_action('wp_set_comment_status', $comment_id, $comment_status);

		$comment = get_comment($comment_id);
		$comment_post_ID = $comment->comment_post_ID;
		$c = $wpdb->get_row( "SELECT count(*) as c FROM {$wpdb->comments} WHERE comment_post_ID = '$comment_post_ID' AND comment_approved = '1'" );
		if( is_object( $c ) )
			$wpdb->query( "UPDATE $wpdb->posts SET comment_count = '$c->c' WHERE ID = '$comment_post_ID'" );
		return true;
    } else {
		return false;
    }
}

function wp_get_comment_status($comment_id) {
	global $wpdb;

	$result = $wpdb->get_var("SELECT comment_approved FROM $wpdb->comments WHERE comment_ID='$comment_id' LIMIT 1");
	if ($result == NULL) {
		return 'deleted';
	} else if ($result == '1') {
		return 'approved';
	} else if ($result == '0') {
		return 'unapproved';
	} else if ($result == 'spam') {
		return 'spam';
	} else {
		return false;
	}
}

function check_comment($author, $email, $url, $comment, $user_ip, $user_agent, $comment_type) {
	global $wpdb;

	if (1 == get_settings('comment_moderation')) return false; // If moderation is set to manual

	if ( (count(explode('http:', $comment)) - 1) >= get_settings('comment_max_links') )
		return false; // Check # of external links

	$mod_keys = trim( get_settings('moderation_keys') );
	if ( !empty($mod_keys) ) {
		$words = explode("\n", $mod_keys );

		foreach ($words as $word) {
			$word = trim($word);

			// Skip empty lines
			if (empty($word)) { continue; }

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
	if ( 1 == get_settings('comment_whitelist')) {
		if ( 'trackback' == $comment_type || 'pingback' == $comment_type ) { // check if domain is in blogroll
			$uri = parse_url($url);
			$domain = $uri['host'];
			$uri = parse_url( get_option('home') );
			$home_domain = $uri['host'];
			if ( $wpdb->get_var("SELECT link_id FROM $wpdb->links WHERE link_url LIKE ('%$domain%') LIMIT 1") || $domain == $home_domain )
				return true;
			else
				return false;
		} elseif( $author != '' && $email != '' ) {
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
	return $wpdb->get_results("SELECT * FROM $wpdb->comments WHERE comment_post_ID = $post_id AND comment_approved = '1' ORDER BY comment_date");
}

?>
