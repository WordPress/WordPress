<?php

// Template functions

function comments_template() {
	global $wp_query, $withcomments, $post, $wpdb, $id, $comment;

	if ( is_single() || is_page() || $withcomments ) :
		$req = get_settings('require_name_email');
        $comment_author = isset($_COOKIE['comment_author_'.COOKIEHASH]) ? trim(stripslashes($_COOKIE['comment_author_'.COOKIEHASH])) : '';
		$comment_author_email = isset($_COOKIE['comment_author_email_'.COOKIEHASH]) ? trim(stripslashes($_COOKIE['comment_author_email_'.COOKIEHASH])) : '';
		$comment_author_url = isset($_COOKIE['comment_author_url_'.COOKIEHASH]) ? trim(stripslashes($_COOKIE['comment_author_url_'.COOKIEHASH])) : '';
		$comments = $wpdb->get_results("SELECT * FROM $wpdb->comments WHERE comment_post_ID = '$post->ID' AND comment_approved = '1' ORDER BY comment_date");

	if ( file_exists( TEMPLATEPATH . '/comments.php') )
		require( TEMPLATEPATH . '/comments.php');
	else
		require( ABSPATH . 'wp-content/themes/default/comments.php');

	endif;
}

function clean_url( $url ) {
	if ('' == $url) return $url;
	$url = preg_replace('|[^a-z0-9-~+_.?#=&;,/:]|i', '', $url);
	$url = str_replace(';//', '://', $url);
	$url = (!strstr($url, '://')) ? 'http://'.$url : $url;
	$url = preg_replace('/&([^#])(?![a-z]{2,8};)/', '&#038;$1', $url);
	return $url;
}

function get_comments_number( $comment_id ) {
	global $wpdb, $comment_count_cache;
	$comment_id = (int) $comment_id;
	if (!isset($comment_count_cache[$comment_id])) 
		$number =  $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->comments WHERE comment_post_ID = '$comment_id' AND comment_approved = '1'");
	else 
		$number =  $comment_count_cache[$comment_id];
	return apply_filters('get_comments_number', $number);
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

function comments_link( $file = '', $echo = true ) {
    echo get_comments_link();
}

function comment_link_rss() {
	echo get_comments_link();
}

function comments_popup_script($width=400, $height=400, $file='') {
    global $wpcommentspopupfile, $wptrackbackpopupfile, $wppingbackpopupfile, $wpcommentsjavascript;

		if (empty ($file)) {
			if ( file_exists( TEMPLATEPATH . '/comments-popup.php') )
				$wpcommentspopupfile = str_replace(ABSPATH, '', TEMPLATEPATH . '/comments-popup.php');
			else
				$wpcommentspopupfile = 'wp-content/themes/default/comments-popup.php';
		} else {
			$wpcommentspopupfile = $file;
		}

    $wpcommentsjavascript = 1;
    $javascript = "<script type='text/javascript'>\nfunction wpopen (macagna) {\n    window.open(macagna, '_blank', 'width=$width,height=$height,scrollbars=yes,status=yes');\n}\n</script>\n";
    echo $javascript;
}

function comments_popup_link($zero='No Comments', $one='1 Comment', $more='% Comments', $CSSclass='', $none='Comments Off') {
    global $id, $wpcommentspopupfile, $wpcommentsjavascript, $post, $wpdb;
    global $querystring_start, $querystring_equal, $querystring_separator;
    global $comment_count_cache;

	if (! is_single() && ! is_page()) {
    if ('' == $comment_count_cache["$id"]) {
        $number = $wpdb->get_var("SELECT COUNT(comment_ID) FROM $wpdb->comments WHERE comment_post_ID = $id AND comment_approved = '1';");
    } else {
        $number = $comment_count_cache["$id"];
    }
    if (0 == $number && 'closed' == $post->comment_status && 'closed' == $post->ping_status) {
        echo $none;
        return;
    } else {
        if (!empty($post->post_password)) { // if there's a password
            if ($_COOKIE['wp-postpass_'.COOKIEHASH] != $post->post_password) {  // and it doesn't match the cookie
                echo('Enter your password to view comments');
                return;
            }
        }
        echo '<a href="';
        if ($wpcommentsjavascript) {
            echo get_settings('siteurl') . '/' . $wpcommentspopupfile.$querystring_start.'p'.$querystring_equal.$id.$querystring_separator.'c'.$querystring_equal.'1';
            //echo get_permalink();
            echo '" onclick="wpopen(this.href); return false"';
        } else {
            // if comments_popup_script() is not in the template, display simple comment link
            comments_link();
            echo '"';
        }
        if (!empty($CSSclass)) {
            echo ' class="'.$CSSclass.'"';
        }
        echo '>';
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
		$author = 'Anonymous';
	else
		$author = $comment->comment_author;
	return apply_filters('get_comment_author', $author);
}

function comment_author() {
	$author = apply_filters('comment_author', get_comment_author() );
	echo $author;
}

function comment_author_rss() {
	$author = apply_filters('comment_author_rss', get_comment_author() );
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

	if ( empty( $url ) )
		$return = $author;
	else
		$return = "<a href='$url' rel='external'>$author</a>";
	return apply_filters('get_comment_author_link', $return);
}

function comment_author_link() {
	echo get_comment_author_link();
}

function get_comment_type() {
	global $comment;
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

function comment_text_rss() {
	$comment_text = get_comment_text();
	$comment_text = apply_filters('comment_text_rss', $comment_text);
	echo $comment_text;
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

function get_comment_time( $d = '' ) {
	global $comment;
	if ( '' == $d )
		$date = mysql2date(get_settings('time_format'), $comment->comment_date);
	else
		$date = mysql2date($d, $comment->comment_date);
	return apply_filters('get_comment_time', $date);
}

function comment_time( $d = '' ) {
	echo get_comment_time();
}

function comments_rss_link($link_text = 'Comments RSS', $commentsrssfilename = 'wp-commentsrss2.php') {
	$url = comments_rss($commentsrssfilename);
	echo "<a href='$url'>$link_text</a>";
}

function comments_rss($commentsrssfilename = 'wp-commentsrss2.php') {
	global $id;
	global $querystring_start, $querystring_equal, $querystring_separator;

	if ('' != get_settings('permalink_structure'))
		$url = trailingslashit( get_permalink() ) . 'feed/';
	else
		$url = get_settings('siteurl') . "/$commentsrssfilename?p=$id";

	return $url;
}

function get_trackback_url() {
	global $id;
	$tb_url = get_settings('siteurl') . '/wp-trackback.php/' . $id;

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
	global $tablecomments, $cache_lastcommentmodified, $pagenow, $wpdb;
	$add_seconds_blog = get_settings('gmt_offset') * 3600;
	$add_seconds_server = date('Z');
	$now = current_time('mysql', 1);
	if ( !isset($cache_lastcommentmodified[$timezone]) ) {
		switch(strtolower($timezone)) {
			case 'gmt':
				$lastcommentmodified = $wpdb->get_var("SELECT comment_date_gmt FROM $tablecomments WHERE comment_date_gmt <= '$now' ORDER BY comment_date_gmt DESC LIMIT 1");
				break;
			case 'blog':
				$lastcommentmodified = $wpdb->get_var("SELECT comment_date FROM $tablecomments WHERE comment_date_gmt <= '$now' ORDER BY comment_date_gmt DESC LIMIT 1");
				break;
			case 'server':
				$lastcommentmodified = $wpdb->get_var("SELECT DATE_ADD(comment_date_gmt, INTERVAL '$add_seconds_server' SECOND) FROM $tablecomments WHERE comment_date_gmt <= '$now' ORDER BY comment_date_gmt DESC LIMIT 1");
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
		if ( !in_array($link_test, $pung) ) : // If we haven't pung it already
			$test = parse_url($link_test);
			if (isset($test['query']))
				$post_links[] = $link_test;
			elseif(($test['path'] != '/') && ($test['path'] != ''))
				$post_links[] = $link_test;
		endif;
	endforeach;

	foreach ($post_links as $pagelinkedto){
		debug_fwrite($log, "Processing -- $pagelinkedto\n");
		$pingback_server_url = discover_pingback_server_uri($pagelinkedto, 2048);

		if ($pingback_server_url) {
                        set_time_limit( 60 ); 
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
			$client->query('pingback.ping', array($pagelinkedfrom, $pagelinkedto)); 
			
			if ( !$client->query('pingback.ping', array($pagelinkedfrom, $pagelinkedto) ) )
				debug_fwrite($log, "Error.\n Fault code: ".$client->getErrorCode()." : ".$client->getErrorMessage()."\n");
			else
				add_ping( $post_ID, $pagelinkedto );
		}
	}

	debug_fwrite($log, "\nEND: ".time()."\n****************************\n");
	debug_fclose($log);
}

function discover_pingback_server_uri($url, $timeout_bytes = 2048) {

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
	$request = "GET $path HTTP/1.1\r\nHost: $host\r\nUser-Agent: WordPress/$wp_version PHP/" . phpversion() . "\r\n\r\n";
	ob_end_flush();
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


/* wp_set_comment_status:
   part of otaku42's comment moderation hack
   changes the status of a comment according to $comment_status.
   allowed values:
   hold   : set comment_approve field to 0
   approve: set comment_approve field to 1
   delete : remove comment out of database
   
   returns true if change could be applied
   returns false on database error or invalid value for $comment_status
 */
function wp_set_comment_status($comment_id, $comment_status) {
    global $wpdb;

    switch($comment_status) {
		case 'hold':
			$query = "UPDATE $wpdb->comments SET comment_approved='0' WHERE comment_ID='$comment_id' LIMIT 1";
		break;
		case 'approve':
			$query = "UPDATE $wpdb->comments SET comment_approved='1' WHERE comment_ID='$comment_id' LIMIT 1";
		break;
		case 'delete':
			$query = "DELETE FROM $wpdb->comments WHERE comment_ID='$comment_id' LIMIT 1";
		break;
		default:
			return false;
    }
    
    if ($wpdb->query($query)) {
		do_action('wp_set_comment_status', $comment_id);
		return true;
    } else {
		return false;
    }
}


/* wp_get_comment_status
   part of otaku42's comment moderation hack
   gets the current status of a comment

   returned values:
   "approved"  : comment has been approved
   "unapproved": comment has not been approved
   "deleted   ": comment not found in database

   a (boolean) false signals an error
 */
function wp_get_comment_status($comment_id) {
    global $wpdb;
    
    $result = $wpdb->get_var("SELECT comment_approved FROM $wpdb->comments WHERE comment_ID='$comment_id' LIMIT 1");
    if ($result == NULL) {
        return "deleted";
    } else if ($result == "1") {
        return "approved";
    } else if ($result == "0") {
        return "unapproved";
    } else {
        return false;
    }
}

function wp_notify_postauthor($comment_id, $comment_type='comment') {
    global $wpdb;
    global $querystring_start, $querystring_equal, $querystring_separator;
    
    $comment = $wpdb->get_row("SELECT * FROM $wpdb->comments WHERE comment_ID='$comment_id' LIMIT 1");
    $post = $wpdb->get_row("SELECT * FROM $wpdb->posts WHERE ID='$comment->comment_post_ID' LIMIT 1");
    $user = $wpdb->get_row("SELECT * FROM $wpdb->users WHERE ID='$post->post_author' LIMIT 1");

    if ('' == $user->user_email) return false; // If there's no email to send the comment to

	$comment_author_domain = gethostbyaddr($comment->comment_author_IP);

	$blogname = get_settings('blogname');
	
	if ('comment' == $comment_type) {
		$notify_message  = "New comment on your post #$comment->comment_post_ID \"".$post->post_title."\"\r\n\r\n";
		$notify_message .= "Author : $comment->comment_author (IP: $comment->comment_author_IP , $comment_author_domain)\r\n";
		$notify_message .= "E-mail : $comment->comment_author_email\r\n";
		$notify_message .= "URI    : $comment->comment_author_url\r\n";
		$notify_message .= "Whois  : http://ws.arin.net/cgi-bin/whois.pl?queryinput=$comment->comment_author_IP\r\n";
		$notify_message .= "Comment:\r\n".$comment->comment_content."\r\n\r\n";
		$notify_message .= "You can see all comments on this post here: \r\n";
		$subject = '[' . $blogname . '] Comment: "' .$post->post_title.'"';
	} elseif ('trackback' == $comment_type) {
		$notify_message  = "New trackback on your post #$comment_post_ID \"".$post->post_title."\"\r\n\r\n";
		$notify_message .= "Website: $comment->comment_author (IP: $comment->comment_author_IP , $comment_author_domain)\r\n";
		$notify_message .= "URI    : $comment->comment_author_url\r\n";
		$notify_message .= "Excerpt: \n".$comment->comment_content."\r\n\r\n";
		$notify_message .= "You can see all trackbacks on this post here: \r\n";
		$subject = '[' . $blogname . '] Trackback: "' .$post->post_title.'"';
	} elseif ('pingback' == $comment_type) {
		$notify_message  = "New pingback on your post #$comment_post_ID \"".$post->post_title."\"\r\n\r\n";
		$notify_message .= "Website: $comment->comment_author\r\n";
		$notify_message .= "URI    : $comment->comment_author_url\r\n";
		$notify_message .= "Excerpt: \n[...] $original_context [...]\r\n\r\n";
		$notify_message .= "You can see all pingbacks on this post here: \r\n";
		$subject = '[' . $blogname . '] Pingback: "' .$post->post_title.'"';
	}
	$notify_message .= get_permalink($comment->comment_post_ID) . '#comments';

	if ('' == $comment->comment_author_email || '' == $comment->comment_author) {
		$from = "From: \"$blogname\" <wordpress@" . $_SERVER['SERVER_NAME'] . '>';
	} else {
		$from = 'From: "' . $comment->comment_author . "\" <$comment->comment_author_email>";
	}

	$message_headers = "MIME-Version: 1.0\n"
		. "$from\n"
		. "Content-Type: text/plain; charset=\"" . get_settings('blog_charset') . "\"\n";

	@wp_mail($user->user_email, $subject, $notify_message, $message_headers);
   
    return true;
}

/* wp_notify_moderator
   notifies the moderator of the blog (usually the admin)
   about a new comment that waits for approval
   always returns true
 */
function wp_notify_moderator($comment_id) {
    global $wpdb;

    if( get_settings( "moderation_notify" ) == 0 )
        return true; 
    
    $comment = $wpdb->get_row("SELECT * FROM $wpdb->comments WHERE comment_ID='$comment_id' LIMIT 1");
    $post = $wpdb->get_row("SELECT * FROM $wpdb->posts WHERE ID='$comment->comment_post_ID' LIMIT 1");
    $user = $wpdb->get_row("SELECT * FROM $wpdb->users WHERE ID='$post->post_author' LIMIT 1");

    $comment_author_domain = gethostbyaddr($comment->comment_author_IP);
    $comments_waiting = $wpdb->get_var("SELECT count(comment_ID) FROM $wpdb->comments WHERE comment_approved = '0'");

    $notify_message  = "A new comment on the post #$post->ID \"$post->post_title\" is waiting for your approval\r\n";
	$notify_message .= get_permalink($comment->comment_post_ID);
    $notify_message .= "\n\nAuthor : $comment->comment_author (IP: $comment->comment_author_IP , $comment_author_domain)\r\n";
    $notify_message .= "E-mail : $comment->comment_author_email\r\n";
    $notify_message .= "URL    : $comment->comment_author_url\r\n";
    $notify_message .= "Whois  : http://ws.arin.net/cgi-bin/whois.pl?queryinput=$comment->comment_author_IP\r\n";
    $notify_message .= "Comment:\r\n".$comment->comment_content."\r\n\r\n";
    $notify_message .= "To approve this comment, visit: " . get_settings('siteurl') . "/wp-admin/post.php?action=mailapprovecomment&p=".$comment->comment_post_ID."&comment=$comment_id\r\n";
    $notify_message .= "To delete this comment, visit: " . get_settings('siteurl') . "/wp-admin/post.php?action=confirmdeletecomment&p=".$comment->comment_post_ID."&comment=$comment_id\r\n";
    $notify_message .= "Currently $comments_waiting comments are waiting for approval. Please visit the moderation panel:\r\n";
    $notify_message .= get_settings('siteurl') . "/wp-admin/moderation.php\r\n";

    $subject = '[' . get_settings('blogname') . '] Please moderate: "' .$post->post_title.'"';
    $admin_email = get_settings("admin_email");
    $from  = "From: $admin_email";

    $message_headers = "MIME-Version: 1.0\n"
    	. "$from\n"
    	. "Content-Type: text/plain; charset=\"" . get_settings('blog_charset') . "\"\n";

    @wp_mail($admin_email, $subject, $notify_message, $message_headers);
    
    return true;
}

function check_comment($author, $email, $url, $comment, $user_ip, $user_agent) {
	global $wpdb;

	if (1 == get_settings('comment_moderation')) return false; // If moderation is set to manual

	if ( (count(explode('http:', $comment)) - 1) >= get_settings('comment_max_links') )
		return false; // Check # of external links

	// Comment whitelisting:
	if ( 1 == get_settings('comment_whitelist')) {
		if( $author != '' && $email != '' ) {
		    $ok_to_comment = $wpdb->get_var("SELECT comment_approved FROM $wpdb->comments WHERE comment_author_email = '$email' and comment_approved = '1' ");
		    if ( 1 == $ok_to_comment && false === strpos( $email, get_settings('moderation_keys')) )
			return true;
		} else {
			return false;
		}
	}

	// Useless numeric encoding is a pretty good spam indicator:
	// Extract entities:
	if (preg_match_all('/&#(\d+);/',$comment,$chars)) {
		foreach ($chars[1] as $char) {
			// If it's an encoded char in the normal ASCII set, reject
			if ($char < 128)
				return false;
		}
	}

	$mod_keys = trim( get_settings('moderation_keys') );
	if ('' == $mod_keys )
		return true; // If moderation keys are empty
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

	return true;
}

?>