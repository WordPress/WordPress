<?php
require(dirname(__FILE__) . '/wp-config.php');

// trackback is done by a POST
$request_array = 'HTTP_POST_VARS';
$tb_id = explode('/', $HTTP_SERVER_VARS['REQUEST_URI']);
$tb_id = intval($tb_id[count($tb_id)-1]);
$tb_url = $HTTP_POST_VARS['url'];
$title = $HTTP_POST_VARS['title'];
$excerpt = $HTTP_POST_VARS['excerpt'];
$blog_name = $HTTP_POST_VARS['blog_name'];

require('wp-blog-header.php');

if ( (($p != '') && ($p != 'all')) || ($name != '') ) {
    $tb_id = $posts[0]->ID;
}

if (empty($title) && empty($tb_url) && empty($blog_name)) {
	// If it doesn't look like a trackback at all...
	header('Location: ' . get_permalink($tb_id));
}

if ((strlen(''.$tb_id)) && (empty($HTTP_GET_VARS['__mode'])) && (strlen(''.$tb_url))) {

	@header('Content-Type: text/xml');

	if (!$use_trackback)
		trackback_response(1, 'Sorry, this weblog does not allow you to trackback its posts.');

	$pingstatus = $wpdb->get_var("SELECT ping_status FROM $tableposts WHERE ID = $tb_id");

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

	$user_ip = $HTTP_SERVER_VARS['REMOTE_ADDR'];
	$user_domain = gethostbyaddr($user_ip);
	$time_difference = get_settings('time_difference');
	$now = gmdate('Y-m-d H:i:s');

	$comment = convert_chars($comment);
	$comment = format_to_post($comment);

	$comment_author = $author;
	$comment_author_email = $email;
	$comment_author_url = $tb_url;

	$author = addslashes($author);

	$comment_moderation = get_settings('comment_moderation');
	$moderation_notify = get_settings('moderation_notify');

	if ('manual' == $comment_moderation) {
		$approved = 0;
	} else if ('auto' == $comment_moderation) {
		$approved = 0;
	} else { // none
		$approved = 1;
	}

	$result = $wpdb->query("INSERT INTO $tablecomments 
	(comment_post_ID, comment_author, comment_author_email, comment_author_url, comment_author_IP, comment_date, comment_content, comment_approved)
	VALUES 
	('$comment_post_ID', '$author', '$email', '$tb_url', '$user_ip', '$now', '$comment', '$approved')
	");

	if (!$result) {
		die ("There is an error with the database, it can't store your comment...<br />Please contact the <a href='mailto:$admin_email'>webmaster</a>.");
	} else {
		$comment_ID = $wpdb->get_var('SELECT last_insert_id()');
		if ($comments_notify)
			wp_notify_postauthor($comment_ID, 'trackback');
		trackback_response(0);
	}
}
?>