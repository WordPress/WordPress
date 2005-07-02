<?php

// For security reasons, fill in the connection details to your Textpattern database below:

$tp_database_name = 'textpattern';
$tp_database_username = 'username';
$tp_database_password = 'password';
$tp_database_host = 'localhost';

if (!file_exists('../wp-config.php')) die("There doesn't seem to be a wp-config.php file. Double check that you updated wp-config-sample.php with the proper database connection information and renamed it to wp-config.php.");
require_once('../wp-config.php');
require_once('upgrade-functions.php');

$step = $_GET['step'];
if (!$step) $step = 0;
header( 'Content-Type: text/html; charset=utf-8' );
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<title>WordPress &rsaquo; Textpattern Import</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<style media="screen" type="text/css">
	body {
		font-family: Georgia, "Times New Roman", Times, serif;
		margin-left: 15%;
		margin-right: 15%;
	}
	#logo {
		margin: 0;
		padding: 0;
		background-image: url(http://wordpress.org/images/wordpress.gif);
		background-repeat: no-repeat;
		height: 60px;
		border-bottom: 4px solid #333;
	}
	#logo a {
		display: block;
		text-decoration: none;
		text-indent: -100em;
		height: 60px;
	}
	p {
		line-height: 140%;
	}
	</style>
</head><body> 
<h1 id="logo"><a href="http://wordpress.org">WordPress</a></h1> 
<?php
switch($step) {

	case 0:
?> 
<p>This script imports your entries from Textpattern into WordPress. It should be relatively painless, and we hope you're happy with the result.</p>
<p>To run this, you first need to edit this file (<code>import-textpattern.php</code>) and enter your Textpattern database connection details. Let's check if the database connection information works...</p>
<?php
$connection = @mysql_connect($tp_database_host, $tp_database_username, $tp_database_password);
$database = @mysql_select_db($tp_database_name);
if ($connection && $database) {
?>
<p>Everything seems dandy so far, <a href="?step=1">let's get started</a>!</p>
<?php
} else {
?>
<p><em>It looks like your database information is incorrect. Please re-edit this file and double-check all the settings.</em></p>
<?php
}
	break;
	
	case 1:
?> 
<h1>Step 1</h1> 
<p>First let's get posts and comments.</p> 
<?php
// For people running this on .72
$query = "ALTER TABLE `$wpdb->posts` ADD `post_name` VARCHAR(200) NOT NULL";
maybe_add_column($wpdb->posts, 'post_name', $query);

// Create post_name field
$connection = @mysql_connect($tp_database_host, $tp_database_username, $tp_database_password);
$database = @mysql_select_db($tp_database_name);

// For now we're going to give everything the same author and same category
$author = $wpdb->get_var("SELECT ID FROM $wpdb->users WHERE user_level = 10 LIMIT 1");
$category = $wpdb->get_var("SELECT cat_ID FROM $wpdb->categories LIMIT 1");

$posts = mysql_query('SELECT * FROM textpattern', $connection);

while ($post = mysql_fetch_array($posts)) {
	//  ID, AuthorID, LastMod, LastModID, Posted, Title, Body, Body_html, Abstract, Category1, Category2, Annotate, AnnotateInvite, Status, Listing1, Listing2, Section
	$posted = $post['Posted'];
	// 20030216162119
	$year = substr($posted,0,4);
	$month = substr($posted,4,2);
	$day = substr($posted,6,2);
	$hour = substr($posted,8,2);
	$minute = substr($posted,10,2);
	$second = substr($posted,12,2);
	$timestamp = mktime($hour, $minute, $second, $month, $day, $year);
	$posted = date('Y-m-d H:i:s', $timestamp);
	
	$content = addslashes($post['Body_html']);
	$title = addslashes($post['Title']);
	$post_name = sanitize_title($title);

	$wpdb->query("INSERT INTO $wpdb->posts
		(post_author, post_date, post_content, post_title, post_category, post_name, post_status)
		VALUES
		('$author', '$posted', '$content', '$title', '$category', '$post_name', 'publish')");

	// Get wordpress post id
	$wp_post_ID = $wpdb->get_var("SELECT ID FROM $wpdb->posts ORDER BY ID DESC LIMIT 1");
	
	// Now let's insert comments if there are any for the TP post
	$tp_id = $post['ID'];
	$comments = mysql_query("SELECT * FROM txp_Discuss WHERE parentid = $tp_id");
	if ($comments) {
		while($comment = mysql_fetch_object($comments)) {
			//  discussid, parentid, name, email, web, ip, posted, message
			// For some reason here "posted" is a real MySQL date, so we don't have to do anything about it
			//  comment_post_ID  	 comment_author  	 comment_author_email  	 comment_author_url  	 comment_author_IP  	 comment_date  	 comment_content  	 comment_karma
			$wpdb->query("INSERT INTO $wpdb->comments
				(comment_post_ID, comment_author, comment_author_email, comment_author_url, comment_author_IP, comment_date, comment_content)
				VALUES
				($wp_post_ID, '$comment->name', '$comment->email', '$comment->web', '$comment->ip', '$comment->posted', '$comment->message')");
		}
	}
}

upgrade_all();
?>
<p><strong>All done.</strong> Wasn&#8217;t that fun? <a href="../">Have fun</a>.</p> 
<?php
break;
}
?> 

</body>
</html>
