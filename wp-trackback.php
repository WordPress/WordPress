<?php

function add_magic_quotes($array) {
	foreach ($array as $k => $v) {
		if (is_array($v)) {
			$array[$k] = add_magic_quotes($v);
		} else {
			$array[$k] = addslashes($v);
		}
	}
	return $array;
}

if (!get_magic_quotes_gpc()) {
	$_GET    = add_magic_quotes($_GET);
	$_POST   = add_magic_quotes($_POST);
	$_COOKIE = add_magic_quotes($_COOKIE);
}

if ( !$doing_trackback) {
    $doing_trackback = 1;
    require('wp-blog-header.php');
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
$tb_url = $_POST['url'];
$title = $_POST['title'];
$excerpt = $_POST['excerpt'];
$blog_name = $_POST['blog_name'];
$charset = $_POST['charset'];

if ($charset)
	$charset = strtoupper( trim($charset) );
else
	$charset = 'auto';

if ( function_exists('mb_convert_encoding') ) {
	$title = mb_convert_encoding($title, get_settings('blog_charset'), $charset);
	$excerpt = mb_convert_encoding($excerpt, get_settings('blog_charset'), $charset);
	$blog_name = mb_convert_encoding($blog_name, get_settings('blog_charset'), $charset);
}

if ( is_single() ) 
    $tb_id = $posts[0]->ID;

if ( !$tb_id)
	trackback_response(1, 'I really need an ID for this to work.');

if (empty($title) && empty($tb_url) && empty($blog_name)) {
	// If it doesn't look like a trackback at all...
	header('Location: ' . get_permalink($tb_id));
	exit;
}

if ( !empty($tb_url) && !empty($title) && !empty($tb_url) ) {
	header('Content-Type: text/xml; charset=' . get_option('blog_charset') );

	$pingstatus = $wpdb->get_var("SELECT ping_status FROM $wpdb->posts WHERE ID = $tb_id");

	if ('closed' == $pingstatus)
		trackback_response(1, 'Sorry, trackbacks are closed for this item.');

	$title = strip_tags( htmlspecialchars( $title ) );
	$title = (strlen($title) > 250) ? substr($title, 0, 250) . '...' : $title;
	$excerpt = strip_tags($excerpt);
	$excerpt = (strlen($excerpt) > 255) ? substr($excerpt, 0, 252) . '...' : $excerpt;
	$blog_name = htmlspecialchars($blog_name);
	$blog_name = (strlen($blog_name) > 250) ? substr($blog_name, 0, 250) . '...' : $blog_name;

	$comment_post_ID = $tb_id;
	$comment_author = $blog_name;
	$comment_author_email = '';
	$comment_author_url = $tb_url;
	$comment_content = "<strong>$title</strong>\n\n$excerpt";
	$comment_type = 'trackback';

	$commentdata = compact('comment_post_ID', 'comment_author', 'comment_author_email', 'comment_author_url', 'comment_content', 'comment_type');

	wp_new_comment($commentdata);

	trackback_response(0);
	do_action('trackback_post', $wpdb->insert_id);

}
?>