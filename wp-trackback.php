<?php
require_once( dirname(__FILE__) . '/wp-config.php' );

if ( empty($doing_trackback) ) {
	$doing_trackback = true;
	require_once('wp-blog-header.php');
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

if (!$tb_id) {
	$tb_id = explode('/', $_SERVER['REQUEST_URI']);
	$tb_id = intval($tb_id[count($tb_id)-1]);
}

$tb_url    = $_POST['url'];
$title     = $_POST['title'];
$excerpt   = $_POST['excerpt'];
$blog_name = $_POST['blog_name'];
$charset   = $_POST['charset'];

if ($charset)
	$charset = strtoupper( trim($charset) );
else
	$charset = 'auto';

if ( function_exists('mb_convert_encoding') ) { // For international trackbacks
	$title     = mb_convert_encoding($title, get_settings('blog_charset'), $charset);
	$excerpt   = mb_convert_encoding($excerpt, get_settings('blog_charset'), $charset);
	$blog_name = mb_convert_encoding($blog_name, get_settings('blog_charset'), $charset);
}

if ( is_single() ) 
    $tb_id = $posts[0]->ID;

if ( !$tb_id )
	trackback_response(1, 'I really need an ID for this to work.');

if (empty($title) && empty($tb_url) && empty($blog_name)) {
	// If it doesn't look like a trackback at all...
	header('Location: ' . get_permalink($tb_id));
	exit;
}

if ( !empty($tb_url) && !empty($title) && !empty($tb_url) ) {
	header('Content-Type: text/xml; charset=' . get_option('blog_charset') );

	$pingstatus = $wpdb->get_var("SELECT ping_status FROM $wpdb->posts WHERE ID = $tb_id");

	if ('open' != $pingstatus)
		trackback_response(1, 'Sorry, trackbacks are closed for this item.');

	$title =  wp_specialchars( strip_tags( $title ) );
	$title = (strlen($title) > 250) ? substr($title, 0, 250) . '...' : $title;
	$excerpt = strip_tags($excerpt);
	$excerpt = (strlen($excerpt) > 255) ? substr($excerpt, 0, 252) . '...' : $excerpt;

	$comment_post_ID = $tb_id;
	$comment_author = $blog_name;
	$comment_author_email = '';
	$comment_author_url = $tb_url;
	$comment_content = "<strong>$title</strong>\n\n$excerpt";
	$comment_type = 'trackback';

	$dupe = $wpdb->get_results("SELECT * FROM $wpdb->comments WHERE comment_post_ID = '$comment_post_ID' AND comment_author_url = '$comment_author_url'");
	if ( $dupe )
		trackback_response(1, 'We already have a ping from that URI for this post.');

	$commentdata = compact('comment_post_ID', 'comment_author', 'comment_author_email', 'comment_author_url', 'comment_content', 'comment_type');

	wp_new_comment($commentdata);

	trackback_response(0);
	do_action('trackback_post', $wpdb->insert_id);

}
?>