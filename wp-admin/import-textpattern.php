<?php

// For security reasons, fill in the connection details to your Textpattern database below:

$tp_database_name = 'wordpres_test';
$tp_database_username = 'wordpres_test';
$tp_database_password = 'test';
$tp_database_host = 'localhost';

if (!file_exists('../wp-config.php')) die("There doesn't seem to be a wp-config.php file. Double check that you updated wp-config.sample.php with the proper database connection information and renamed it to wp-config.php.");
require('../wp-config.php');
require('wp-install-helper.php');

$step = $HTTP_GET_VARS['step'];
if (!$step) $step = 0;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<title>WordPress &rsaquo; Textpattern Import</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
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
		height: 72px;
		border-bottom: 4px solid #333;
	}
	#logo a {
		display: block;
		text-decoration: none;
		text-indent: -100em;
		height: 72px;
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
<p><em>It doesn't look like your database information is correct. Please re-edit this file and double-check all the settings.</em></p>
<?php
}
	break;
	
	case 1:
?> 
<h1>Step 1</h1> 
<p>First let's get posts and comments.</p> 
<?php
// For people running this on .72
$query = "ALTER TABLE `$tableposts` ADD `post_name` VARCHAR(200) NOT NULL";
maybe_add_column($tableposts, 'post_name', $query);

// Create post_name field
$connection = @mysql_connect($tp_database_host, $tp_database_username, $tp_database_password);
$database = @mysql_select_db($tp_database_name);

// For now we're going to give everything the same author and same category
$author = $wpdb->get_var("SELECT ID FROM $tableusers WHERE user_level = 10 LIMIT 1");
++$querycount;
$category = $wpdb->get_var("SELECT cat_ID FROM $tablecategories LIMIT 1");
++$querycount;

$posts = mysql_query('SELECT * FROM textpattern', $connection);
++$querycount;

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
	
	$content = $post['Body_html'];
	$title = $post['Title'];
	$post_name = sanitize_title($title);

	$wpdb->query("INSERT INTO $tableposts
		(post_author, post_date, post_content, post_title, post_category, post_name)
		VALUES
		('$author', '$posted', '$content', '$title', '$category', '$post_name')");

	// Get wordpress post id
	$wp_post_ID = $wpdb->get_var("SELECT ID FROM $tableposts ORDER BY ID DESC LIMIT 1");
    ++$querycount;
	
	// Now let's insert comments if there are any for the TP post
	$tp_id = $post['ID'];
	$comments = mysql_query("SELECT * FROM txp_Discuss WHERE parentid = $tp_id");
    ++$querycount;
	if ($comments) {
		while($comment = mysql_fetch_object($comments)) {
			//  discussid, parentid, name, email, web, ip, posted, message
			// For some reason here "posted" is a real MySQL date, so we don't have to do anything about it
			//  comment_post_ID  	 comment_author  	 comment_author_email  	 comment_author_url  	 comment_author_IP  	 comment_date  	 comment_content  	 comment_karma
			$wpdb->query("INSERT INTO $tablecomments
				(comment_post_ID, comment_author, comment_author_email, comment_author_url, comment_author_IP, comment_date, comment_content)
				VALUES
				($wp_post_ID, '$comment->name', '$comment->email', '$comment->web', '$comment->ip', '$comment->posted', '$comment->message')");
		}
	}
}

?> 
<p><strong>Done.</strong></p> 
<p>Now let's populate the new field.</p> 
<p>Working
 
  <strong>Done.</strong></p>
  <p>Now on to <a href="upgrade-072-to-073.php?step=2">step 2</a>.</p>
<?php
	break;
	case 2:
?>
    <h1>Step 2</h1> 
    <p>Now we need to adjust some option data (don't worry this won't change any of your settings.) </p>
    <p>Working
<?php
        // fix timezone diff range
        $wpdb->query("UPDATE $tableoptionvalues SET optionvalue_max = 23 , optionvalue_min = -23 WHERE option_id = 51");
        echo ' .';
        flush();
        // fix upload users description
        $wpdb->query("UPDATE $tableoptions SET option_description = '...or you may authorize only some users. enter their logins here, separated by spaces. if you leave this variable blank, all users who have the minimum level are authorized to upload. example: \'barbara anne george\'' WHERE option_id = 37");
        echo ' .';
        flush();
        // and file types
        $wpdb->query("UPDATE $tableoptions SET option_description = 'accepted file types, separated by spaces. example: \'jpg gif png\'' WHERE option_id = 34");
        echo ' .';
        flush();
        // add link to date format help page
        $wpdb->query("UPDATE $tableoptions SET option_description = 'see <a href=\"help/en/dateformats.help.html\">help</a> for format characters' WHERE option_id = 52");
        $wpdb->query("UPDATE $tableoptions SET option_description = 'see <a href=\"help/en/dateformats.help.html\">help</a> for format characters' WHERE option_id = 53");
        echo ' .';
        flush();
?>
    <strong>Done.</strong></p>
<p>See, that didn&#8217;t hurt a bit. All done!</p>
<?php
	break;
}
?> 
</body>
</html>
