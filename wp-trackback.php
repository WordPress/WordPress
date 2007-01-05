<?php

if (empty($wp)) {
	require_once('wp-config.php');
	wp('tb=1');
}

function trackback_response($error = 0, $error_message = '') {
	header('Content-Type: text/xml; charset=' . get_option('blog_charset') );
	if ($error) {
		echo '<?xml version="1.0" encoding="utf-8"?'.">\n";
		echo "<response>\n";
		echo "<error>1</error>\n";
		echo "<message>$error_message</message>\n";
		echo "</response>";
		die();
	} else {
		echo '<?xml version="1.0" encoding="utf-8"?'.">\n";
		echo "<response>\n";
		echo "<error>0</error>\n";
		echo "</response>";
	}
}

// trackback is done by a POST
$request_array = 'HTTP_POST_VARS';

if ( !$_GET['tb_id'] ) {
	$tb_id = explode('/', $_SERVER['REQUEST_URI']);
	$tb_id = intval( $tb_id[ count($tb_id) - 1 ] );
}

$tb_url  = $_POST['url'];
$charset = $_POST['charset'];

// These three are stripslashed here so that they can be properly escaped after mb_convert_encoding()
$title     = stripslashes($_POST['title']);
$excerpt   = stripslashes($_POST['excerpt']);
$blog_name = stripslashes($_POST['blog_name']);

if ($charset)
	$charset = strtoupper( trim($charset) );
else
	$charset = 'ASCII, UTF-8, ISO-8859-1, JIS, EUC-JP, SJIS';

if ( function_exists('mb_convert_encoding') ) { // For international trackbacks
	$title     = mb_convert_encoding($title, get_option('blog_charset'), $charset);
	$excerpt   = mb_convert_encoding($excerpt, get_option('blog_charset'), $charset);
	$blog_name = mb_convert_encoding($blog_name, get_option('blog_charset'), $charset);
}

// Now that mb_convert_encoding() has been given a swing, we need to escape these three
$title     = $wpdb->escape($title);
$excerpt   = $wpdb->escape($excerpt);
$blog_name = $wpdb->escape($blog_name);

if ( is_single() || is_page() )
	$tb_id = $posts[0]->ID;

if ( !intval( $tb_id ) )
	trackback_response(1, 'I really need an ID for this to work.');

if (empty($title) && empty($tb_url) && empty($blog_name)) {
	// If it doesn't look like a trackback at all...
	wp_redirect(get_permalink($tb_id));
	exit;
}

if ( !empty($tb_url) && !empty($title) && !empty($tb_url) ) {
	header('Content-Type: text/xml; charset=' . get_option('blog_charset') );

	$pingstatus = $wpdb->get_var("SELECT ping_status FROM $wpdb->posts WHERE ID = $tb_id");

	if ( 'open' != $pingstatus )
		trackback_response(1, 'Sorry, trackbacks are closed for this item.');

	$title =  wp_specialchars( strip_tags( $title ) );
	$excerpt = strip_tags($excerpt);
	if ( function_exists('mb_strcut') ) { // For international trackbacks
		$excerpt = mb_strcut($excerpt, 0, 252, get_option('blog_charset')) . '...';
		$title = mb_strcut($title, 0, 250, get_option('blog_charset')) . '...';
	} else {
		$excerpt = (strlen($excerpt) > 255) ? substr($excerpt, 0, 252) . '...' : $excerpt;
		$title = (strlen($title) > 250) ? substr($title, 0, 250) . '...' : $title;
	}

	$comment_post_ID = $tb_id;
	$comment_author = $blog_name;
	$comment_author_email = '';
	$comment_author_url = $tb_url;
	$comment_content = "<strong>$title</strong>\n\n$excerpt";
	$comment_type = 'trackback';

	$dupe = $wpdb->get_results("SELECT * FROM $wpdb->comments WHERE comment_post_ID = '$comment_post_ID' AND comment_author_url = '$comment_author_url'");
	if ( $dupe )
		trackback_response(1, 'We already have a ping from that URL for this post.');

	$commentdata = compact('comment_post_ID', 'comment_author', 'comment_author_email', 'comment_author_url', 'comment_content', 'comment_type');

	wp_new_comment($commentdata);

	do_action('trackback_post', $wpdb->insert_id);
	trackback_response(0);
}
?>