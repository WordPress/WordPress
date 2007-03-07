<?php
/*
 * Comment template functions.
 */

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

function get_comment_author_IP() {
	global $comment;
	return apply_filters('get_comment_author_IP', $comment->comment_author_IP);
}

function comment_author_IP() {
	echo get_comment_author_IP();
}

function get_comment_author_url() {
	global $comment;
	return apply_filters('get_comment_author_url', $comment->comment_author_url);
}

function comment_author_url() {
	echo apply_filters('comment_url', get_comment_author_url());
}

function get_comment_author_url_link( $linktext = '', $before = '', $after = '' ) {
	global $comment;
	$url = get_comment_author_url();
	$display = ($linktext != '') ? $linktext : $url;
	$display = str_replace( 'http://www.', '', $display );
	$display = str_replace( 'http://', '', $display );
	if ( '/' == substr($display, -1) )
		$display = substr($display, 0, -1);
	$return = "$before<a href='$url' rel='external'>$display</a>$after";
	return apply_filters('get_comment_author_url_link', $return);
}

function comment_author_url_link( $linktext = '', $before = '', $after = '' ) {
	echo get_comment_author_url_link( $linktext, $before, $after );
}

function get_comment_date( $d = '' ) {
	global $comment;
	if ( '' == $d )
		$date = mysql2date( get_option('date_format'), $comment->comment_date);
	else
		$date = mysql2date($d, $comment->comment_date);
	return apply_filters('get_comment_date', $date, $d);
}

function comment_date( $d = '' ) {
	echo get_comment_date( $d );
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

function get_comment_ID() {
	global $comment;
	return apply_filters('get_comment_ID', $comment->comment_ID);
}

function comment_ID() {
	echo get_comment_ID();
}

function get_comment_link() {
	global $comment;
	return get_permalink( $comment->comment_post_ID ) . '#comment-' . $comment->comment_ID;
}

function get_comments_link() {
	return get_permalink() . '#comments';
}

function comments_link( $file = '', $echo = true ) {
		echo get_comments_link();
}

function get_comments_number( $post_id = 0 ) {
	global $wpdb, $id;
	$post_id = (int) $post_id;

	if ( !$post_id )
		$post_id = $id;

	$post = get_post($post_id);
	if ( ! isset($post->comment_count) )
		$count = 0;
	else
		$count = $post->comment_count;

	return apply_filters('get_comments_number', $count);
}

function comments_number( $zero = false, $one = false, $more = false, $number = '' ) {
	global $id;
	$number = get_comments_number($id);

	if ( $number > 1 )
		$output = str_replace('%', $number, ( false === $more ) ? __('% Comments') : $more);
	elseif ( $number == 0 )
		$output = ( false === $zero ) ? __('No Comments') : $zero;
	else // must be one
		$output = ( false === $one ) ? __('1 Comment') : $one;

	echo apply_filters('comments_number', $output, $number);
}

function get_comment_text() {
	global $comment;
	return apply_filters('get_comment_text', $comment->comment_content);
}

function comment_text() {
	echo apply_filters('comment_text', get_comment_text() );
}

function get_comment_time( $d = '', $gmt = false ) {
	global $comment;
	$comment_date = $gmt? $comment->comment_date_gmt : $comment->comment_date;
	if ( '' == $d )
		$date = mysql2date(get_option('time_format'), $comment_date);
	else
		$date = mysql2date($d, $comment_date);
	return apply_filters('get_comment_time', $date, $d, $gmt);
}

function comment_time( $d = '' ) {
	echo get_comment_time($d);
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

function get_trackback_url() {
	global $id;
	$tb_url = get_option('siteurl') . '/wp-trackback.php?p=' . $id;

	if ( '' != get_option('permalink_structure') )
		$tb_url = trailingslashit(get_permalink()) . user_trailingslashit('trackback');

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
	if (strpos($_SERVER['HTTP_USER_AGENT'], 'W3C_Validator') !== false) {
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

function comments_template( $file = '/comments.php' ) {
	global $wp_query, $withcomments, $post, $wpdb, $id, $comment, $user_login, $user_ID, $user_identity;

	if ( ! (is_single() || is_page() || $withcomments) )
		return;

	$req = get_option('require_name_email');
	$commenter = wp_get_current_commenter();
	extract($commenter);

	// TODO: Use API instead of SELECTs.
	if ( empty($comment_author) ) {
		$comments = $wpdb->get_results("SELECT * FROM $wpdb->comments WHERE comment_post_ID = '$post->ID' AND comment_approved = '1' ORDER BY comment_date");
	} else {
		$author_db = $wpdb->escape($comment_author);
		$email_db  = $wpdb->escape($comment_author_email);
		$comments = $wpdb->get_results("SELECT * FROM $wpdb->comments WHERE comment_post_ID = '$post->ID' AND ( comment_approved = '1' OR ( comment_author = '$author_db' AND comment_author_email = '$email_db' AND comment_approved = '0' ) ) ORDER BY comment_date");
	}

	// keep $comments for legacy's sake (remember $table*? ;) )
	$comments = $wp_query->comments = apply_filters( 'comments_array', $comments, $post->ID );
	$wp_query->comment_count = count($wp_query->comments);

	define('COMMENTS_TEMPLATE', true);
	$include = apply_filters('comments_template', TEMPLATEPATH . $file );
	if ( file_exists( $include ) )
		require( $include );
	else
		require( ABSPATH . 'wp-content/themes/default/comments.php');
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

	if ( is_single() || is_page() )
		return;

	$number = get_comments_number($id);

	if ( 0 == $number && 'closed' == $post->comment_status && 'closed' == $post->ping_status ) {
		echo $none;
		return;
	}

	if ( !empty($post->post_password) ) { // if there's a password
		if ($_COOKIE['wp-postpass_'.COOKIEHASH] != $post->post_password) {  // and it doesn't match the cookie
			echo(__('Enter your password to view comments'));
			return;
		}
	}

	echo '<a href="';
	if ($wpcommentsjavascript) {
		if ( empty($wpcommentspopupfile) )
			$home = get_option('home');
		else
			$home = get_option('siteurl');
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
	$title = attribute_escape(apply_filters('the_title', get_the_title()));
	echo ' title="' . sprintf( __('Comment on %s'), $title ) .'">';
	comments_number($zero, $one, $more, $number);
	echo '</a>';
}

?>
