<?php if (!empty($tb)) {

} else {

if (!empty($HTTP_GET_VARS['tb_id'])) {
	// trackback is done by a GET
	$tb_id = intval($HTTP_GET_VARS['tb_id']);
	$tb_url = $HTTP_GET_VARS['url'];
	$title = $HTTP_GET_VARS['title'];
	$excerpt = $HTTP_GET_VARS['excerpt'];
	$blog_name = $HTTP_GET_VARS['blog_name'];
} elseif (!empty($HTTP_POST_VARS['url'])) {
	// trackback is done by a POST
	$request_array = 'HTTP_POST_VARS';
	$tb_id = explode('/', $HTTP_SERVER_VARS['REQUEST_URI']);
	$tb_id = inval($tb_id[count($tb_id)-1]);
	$tb_url = $HTTP_POST_VARS['url'];
	$title = $HTTP_POST_VARS['title'];
	$excerpt = $HTTP_POST_VARS['excerpt'];
	$blog_name = $HTTP_POST_VARS['blog_name'];
}

if ((strlen(''.$tb_id)) && (empty($HTTP_GET_VARS['__mode'])) && (strlen(''.$tb_url))) {

	@header('Content-Type: text/xml');


	require_once('wp-config.php');
	require_once($abspath.$b2inc.'/b2template.functions.php');
	require_once($abspath.$b2inc.'/b2vars.php');
	require_once($abspath.$b2inc.'/b2functions.php');

	if (!$use_trackback) {
		trackback_response(1, 'Sorry, this weblog does not allow you to trackback its posts.');
	}
	$pingstatus = $wpdb->get_var("SELECT ping_status FROM $tableposts WHERE ID = $tb_id");

	if ('closed' == $pingstatus)
		die('Sorry, trackbacks are closed for this item.');

	$tb_url = addslashes($tb_url);
	$title = strip_tags($title);
	$title = (strlen($title) > 255) ? substr($title, 0, 252).'...' : $title;
	$excerpt = strip_tags($excerpt);
	$excerpt = (strlen($excerpt) > 255) ? substr($excerpt, 0, 252).'...' : $excerpt;
	$blog_name = htmlspecialchars($blog_name);
	$blog_name = (strlen($blog_name) > 255) ? substr($blog_name, 0, 252).'...' : $blog_name;

	$comment = '<trackback />';
	$comment .= "<strong>$title</strong><br />$excerpt";

	$author = addslashes($blog_name);
	$email = '';
	$original_comment = $comment;
	$comment_post_ID = $tb_id;

	$user_ip = $HTTP_SERVER_VARS['REMOTE_ADDR'];
	$user_domain = gethostbyaddr($user_ip);
	$time_difference = get_settings('time_difference');
	$now = date('Y-m-d H:i:s',(time() + ($time_difference * 3600)));

	$comment = convert_chars($comment);
	$comment = format_to_post($comment);

	$comment_author = $author;
	$comment_author_email = $email;
	$comment_author_url = $tb_url;

	$author = addslashes($author);

	$result = $wpdb->query("INSERT INTO $tablecomments VALUES ('0', '$comment_post_ID', '$author', '$email', '$tb_url', '$user_ip', '$now', '$comment', '0')");
	if (!$result) {
		die ("There is an error with the database, it can't store your comment...<br />Contact the <a href=\"mailto:$admin_email\">webmaster</a>");
	} else {
			$postdata = get_postdata($comment_post_ID);
			$authordata = get_userdata($postdata["Author_ID"]);
		if ($comments_notify && '' != $authordata->user_email) {

			$notify_message  = "New trackback on your post #$comment_post_ID.\r\n\r\n";
			$notify_message .= "Website: $comment_author (IP: $user_ip , $user_domain)\r\n";
			$notify_message .= "URI    : $comment_author_url\r\n";
			$notify_message .= "Excerpt: \n".stripslashes($original_comment)."\r\n\r\n";
			$notify_message .= "You can see all trackbacks on this post here: \r\n";
			$notify_message .= "$siteurl/$blogfilename?p=$comment_post_ID&c=1\r\n\r\n";

			$subject = '[' . stripslashes($blogname) . '] Trackback: "' .stripslashes($postdata['Title']).'"';

			$from = "From: wordpress@".$HTTP_SERVER_VARS['SERVER_NAME'];
			$from .= "X-Mailer: WordPress $b2_version with PHP/" . phpversion();

			@mail($authordata->user_email, $subject, $notify_message, $from);
		}
		trackback_response(0);
	}
}
}
?>