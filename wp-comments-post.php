<?php
require(dirname(__FILE__) . '/wp-config.php');

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

$author = trim(strip_tags($_POST['author']));

$email = trim(strip_tags($_POST['email']));
if (strlen($email) < 6)
	$email = '';

$url = trim(strip_tags($_POST['url']));
$url = ((!stristr($url, '://')) && ($url != '')) ? 'http://'.$url : $url;
if (strlen($url) < 7)
	$url = '';

$comment = trim($_POST['comment']);
$original_comment = $comment;
$comment_post_ID = intval($_POST['comment_post_ID']);
$user_ip = $_SERVER['REMOTE_ADDR'];
$user_domain = gethostbyaddr($user_ip);

$commentstatus = $wpdb->get_var("SELECT comment_status FROM $tableposts WHERE ID = $comment_post_ID");
if ('closed' == $commentstatus)
	die('Sorry, comments are closed for this item.');

if (get_settings('require_name_email') && ($email == '' || $author == '')) { //original fix by Dodo, and then Drinyth
	die('Error: please fill the required fields (name, email).');
}
if ($comment == 'comment' || $comment == '') {
	die('Error: please type a comment.');
}

$now = current_time('mysql');
$now_gmt = current_time('mysql', 1);


$comment = balanceTags($comment, 1);
$comment = format_to_post($comment);

$comment_author = $author;
$comment_author_email = $email;
$comment_author_url = $url;

$author = addslashes($author);
$email = addslashes($email);
$url = addslashes($url);

/* Flood-protection */
$lasttime = $wpdb->get_var("SELECT comment_date FROM $tablecomments WHERE comment_author_IP = '$user_ip' ORDER BY comment_date DESC LIMIT 1");
$ok = true;
if (!empty($lasttime)) {
	$time_lastcomment= mysql2date('U', $lasttime);
	$time_newcomment= mysql2date('U', "$now");
	if (($time_newcomment - $time_lastcomment) < 10)
		$ok = false;
}
/* End flood-protection */



if ($ok) { // if there was no comment from this IP in the last 10 seconds
	$moderation_notify = get_settings('moderation_notify');
	$comments_notify = get_settings('comments_notify');

	if(check_comment($author, $email, $url, $comment, $user_ip)) {
		$approved = 1;
	} else {
		$approved = 0;
	}

	$wpdb->query("INSERT INTO $tablecomments 
	(comment_post_ID, comment_author, comment_author_email, comment_author_url, comment_author_IP, comment_date, comment_date_gmt, comment_content, comment_approved) 
	VALUES 
	('$comment_post_ID', '$author', '$email', '$url', '$user_ip', '$now', '$now_gmt', '$comment', '$approved')
	");

	$comment_ID = $wpdb->get_var('SELECT last_insert_id()');

	if (($moderation_notify) && (!$approved)) {
	    wp_notify_moderator($comment_ID);
	}
	
	if ((get_settings('comments_notify')) && ($approved)) {
	    wp_notify_postauthor($comment_ID, 'comment');
	}

	do_action('comment_post', $comment_ID);

	if ($email == '')
		$email = ' '; // this to make sure a cookie is set for 'no email'

	if ($url == '')
		$url = ' '; // this to make sure a cookie is set for 'no url'

	setcookie('comment_author_'.$cookiehash, $author, time()+30000000);
	setcookie('comment_author_email_'.$cookiehash, $email, time()+30000000);
	setcookie('comment_author_url_'.$cookiehash, $url, time()+30000000);

	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
	header('Cache-Control: no-cache, must-revalidate');
	header('Pragma: no-cache');
	$location = (empty($_POST['redirect_to'])) ? $_SERVER["HTTP_REFERER"] : $_POST['redirect_to'];
	if ($is_IIS) {
		header("Refresh: 0;url=$location");
	} else {
		header("Location: $location");
	}
} else {
	die('Sorry, you can only post a new comment once every 10 seconds. Slow down cowboy.');
}

?>