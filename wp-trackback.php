<?php
require(dirname(__FILE__) . '/wp-config.php');

// trackback is done by a POST
$request_array = 'HTTP_POST_VARS';
$tb_id = explode('/', $_SERVER['REQUEST_URI']);
$tb_id = intval($tb_id[count($tb_id)-1]);
$tb_url = $_POST['url'];
$title = $_POST['title'];
$excerpt = $_POST['excerpt'];
$blog_name = $_POST['blog_name'];

if (! $doing_trackback) {
    $doing_trackback = 1;
    require('wp-blog-header.php');
}

if (is_single()) {
    $tb_id = $posts[0]->ID;
}

if (empty($title) && empty($tb_url) && empty($blog_name)) {
	// If it doesn't look like a trackback at all...
	header('Location: ' . get_permalink($tb_id));
}

if ((strlen(''.$tb_id)) && (empty($_GET['__mode'])) && (strlen(''.$tb_url))) {

	@header('Content-Type: text/xml; charset=' . get_settings('blog_charset'));

	$pingstatus = $wpdb->get_var("SELECT ping_status FROM $wpdb->posts WHERE ID = $tb_id");

	if ('closed' == $pingstatus)
		trackback_response(1, 'Sorry, trackbacks are closed for this item.');

	$tb_url = addslashes($tb_url);
	$title = strip_tags($title);
	$title = (strlen($title) > 255) ? substr($title, 0, 252).'...' : $title;
	$excerpt = strip_tags($excerpt);
	$excerpt = (strlen($excerpt) > 255) ? substr($excerpt, 0, 252).'...' : $excerpt;
	$blog_name = htmlspecialchars($blog_name);
	$blog_name = (strlen($blog_name) > 255) ? substr($blog_name, 0, 252).'...' : $blog_name;

	$comment = '<trackback />';
	$comment .= "<strong>$title</strong>\n$excerpt";

	$author = addslashes(stripslashes(stripslashes($blog_name)));
	$email = '';
	$original_comment = $comment;
	$comment_post_ID = $tb_id;

	$user_ip = $_SERVER['REMOTE_ADDR'];
	$user_domain = gethostbyaddr($user_ip);
	$now = current_time('mysql');
	$now_gmt = current_time('mysql', 1);

	$user_agent = addslashes($_SERVER['HTTP_USER_AGENT']);

	$comment = convert_chars($comment);
	$comment = format_to_post($comment);

	$comment_author = $author;
	$comment_author_email = $email;
	$comment_author_url = $tb_url;

	$author = addslashes($author);

	$comment_moderation = get_settings('comment_moderation');
	$moderation_notify = get_settings('moderation_notify');

	if(check_comment($author, $email, $url, $comment, $user_ip, $user_agent)) {
		$approved = 1;
	} else {
		$approved = 0;
	}

	$result = $wpdb->query("INSERT INTO $wpdb->comments 
	(comment_post_ID, comment_author, comment_author_email, comment_author_url, comment_author_IP, comment_date, comment_date_gmt, comment_content, comment_approved, comment_agent)
	VALUES 
	('$comment_post_ID', '$author', '$email', '$tb_url', '$user_ip', '$now', '$now_gmt', '$comment', '$approved', '$user_agent')
	");

	if (!$result) {
		die ("There is an error with the database, it can't store your comment...<br />Please contact the webmaster.");
	} else {
		$comment_ID = $wpdb->get_var('SELECT last_insert_id()');
		if (get_settings('comments_notify'))
			wp_notify_postauthor($comment_ID, 'trackback');
		trackback_response(0);
		do_action('trackback_post', $comment_ID);
	}
}
?>