<?php
require_once('../wp-config.php');
require('install-helper.php');

$step = $HTTP_GET_VARS['step'];
if (!$step) $step = 0;
//update_option('blogdescription', 'hahahah');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<title>WordPress &rsaquo; .72 to .80 Upgrade</title>
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
<p>This file seeks to upgrade you to the latest version of WordPress. If you are upgrading from any version other than .72, you should run the previous upgrade files to get everything up to date before running this.</p> 
<p>If you&#8217;re all ready, <a href="upgrade-072-to-080.php?step=1">let's go</a>! </p> 
<?php
	break;
	
	case 1:
?> 
<h1>Step 1</h1> 
<p>If it isn&#8217;t there already, let&#8217;s add fields new to this version.</p> 
<?php
// Create post_name field
$query = "ALTER TABLE `$tableposts` ADD `post_name` VARCHAR(200) NOT NULL";
maybe_add_column($tableposts, 'post_name', $query);

// Create index if it isn't there already, suppress errors if it is
$wpdb->hide_errors();
$wpdb->query("ALTER TABLE `$tableposts` ADD INDEX (`post_name`)");
$wpdb->show_errors();



// Create category_nicename field
$query = "ALTER TABLE `$tablecategories` ADD `category_nicename` VARCHAR(200) NOT NULL";
maybe_add_column($tablecategories, 'category_nicename', $query);

// Create index if it isn't there already, suppress errors if it is
$wpdb->hide_errors();
$wpdb->query("ALTER TABLE `$tablecategories` ADD INDEX (`category_nicename`)");
$wpdb->show_errors();

// Create category description field
$query = "ALTER TABLE `$tablecategories` ADD `category_description` TEXT NOT NULL";
maybe_add_column($tablecategories, 'category_description', $query);

// Create category parent field
$query = "ALTER TABLE `$tablecategories` ADD `category_parent` INT(4) NOT NULL";
maybe_add_column($tablecategories, 'category_parent', $query);
?> 
<p><strong>Groovy.</strong></p> 
<p>Now let&#8217;s populate the new fields.</p> 
<p>Working
  <?php
// Get the title and ID of every post, post_name to check if it already has a value
$posts = $wpdb->get_results("SELECT ID, post_title, post_name FROM $tableposts");

foreach($posts as $post) {
	if ('' == $post->post_name) { 
		$newtitle = sanitize_title($post->post_title);
		$wpdb->query("UPDATE $tableposts SET post_name = '$newtitle' WHERE ID = $post->ID");
	}
	echo ' .';
	flush();
}

$categories = $wpdb->get_results("SELECT cat_ID, cat_name, category_nicename FROM $tablecategories");
foreach ($categories as $category) {
	if ('' == $category->category_nicename) { 
		$newtitle = sanitize_title($category->cat_name);
		$wpdb->query("UPDATE $tablecategories SET category_nicename = '$newtitle' WHERE cat_ID = $category->cat_ID");
	}
	echo ' .';
	flush();
}

if (!$wpdb->get_var("SELECT option_name FROM $tableoptions WHERE option_name = 'permalink_structure'")) { // If it's not already there
	$wpdb->query("INSERT INTO `$tableoptions` 
		(`option_id`, `blog_id`, `option_name`, `option_can_override`, `option_type`, `option_value`, `option_width`, `option_height`, `option_description`, `option_admin_level`) 
		VALUES 
		('', '0', 'permalink_structure', 'Y', '3', '', '20', '8', 'How the permalinks for your site are constructed. See <a href=\"options-permalink.php\">permalink options page</a> for necessary mod_rewrite rules and more information.', '8');");
	}
?> 
  Done with the name game. Now a little option action. </p>
  <p>Now on to <a href="upgrade-072-to-080.php?step=2">step 2</a>.</p>
<?php
	break;
	case 2:
?>
    <h1>Step 2</h1> 
    <p>Now we need to adjust some option data (don't worry this won't change any of your settings.) </p>
    <p>Working
<?php
$wpdb->hide_errors(); // Turn this off for dev and we should probably just fix the queries anyway
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
        // add link to php date format. this could be to a wordpress.org page in the future
        $wpdb->query("UPDATE $tableoptions SET option_description = 'see <a href=\"http://php.net/date\">help</a> for format characters' WHERE option_id = 52");
        $wpdb->query("UPDATE $tableoptions SET option_description = 'see <a href=\"http://php.net/date\">help</a> for format characters' WHERE option_id = 53");
        echo ' .';
        flush();
?>
    <strong>Done with the options updates. Now for a bit of comment action</strong></p>
<?php
$result = '';
$error_count = 0;
$continue = true;

// Insert new column "comment_approved" to $tablecomments
if ($continue) {
	$ddl = "ALTER TABLE $tablecomments ADD COLUMN comment_approved ENUM('0', '1') DEFAULT '1' NOT NULL";
	if (maybe_add_column($tablecomments, $tablecol, $ddl)) {
		$wpdb->query("ALTER TABLE $tablecomments ADD INDEX (comment_approved)");
	}
}

// Insert new option "comment_moderation" to settings	
if (!$wpdb->get_var("SELECT option_id FROM $tableoptions WHERE option_name = 'comment_moderation'")) {
	$wpdb->query("INSERT INTO $tableoptions
		(option_id, blog_id, option_name, option_can_override, option_type, option_value, option_width, option_height, option_description, option_admin_level)
		VALUES 
		('0', '0', 'comment_moderation', 'Y', '5',' none', 20, 8, 'If enabled, comments will only be shown after they have been approved.', 8)");
}

// attach option to group "General blog settings"
if ($continue) {
	$oid = $wpdb->get_var("SELECT option_id FROM $tableoptions WHERE option_name = 'comment_moderation'");	    
	$gid = $wpdb->get_var("SELECT group_id FROM $tableoptiongroups WHERE group_name = 'General blog settings'");
	
	$seq = $wpdb->get_var("SELECT MAX(seq) FROM $tableoptiongroup_options WHERE group_id = '$gid'");
	
	++$seq;

	$wpdb->query("INSERT INTO $tableoptiongroup_options 
		(group_id, option_id, seq) 
		VALUES 
		('$gid', '$oid', '$seq')");
}

// Insert option values for new option "comment_moderation"
if ($continue) {
	$ddl = array();	    
	$ddl[] = "INSERT INTO $tableoptionvalues 
		(option_id, optionvalue, optionvalue_desc, optionvalue_max, optionvalue_min, optionvalue_seq)
		VALUES 
		('$oid', 'none', 'None', NULL, NULL, 1)";
	$ddl[] = "INSERT INTO $tableoptionvalues 
		(option_id, optionvalue, optionvalue_desc, optionvalue_max, optionvalue_min, optionvalue_seq)
		VALUES 
		('$oid', 'manual', 'Manual', NULL, NULL, 2)";
	$ddl[] = "INSERT INTO $tableoptionvalues 
		(option_id, optionvalue, optionvalue_desc, optionvalue_max, optionvalue_min, optionvalue_seq)
		VALUES 
		('$oid','auto', 'Automatic', NULL, NULL, 3)";
	   
	foreach ($ddl as $query) {
		$wpdb->query($query);
	}
	
}

// Insert new option "moderation_notify" to settings	
if (!$wpdb->get_var("SELECT option_id FROM $tableoptions WHERE option_name = 'moderation_notify'")) {
	$wpdb->query("INSERT INTO $tableoptions 
		(option_id, blog_id, option_name, option_can_override, option_type, option_value, option_width, option_height, option_description, option_admin_level) 
		VALUES 
		('0', '0', 'moderation_notify' , 'Y', '2', '1', 20, 8, 'Set this to true if you want to be notified about new comments that wait for approval', 8)");
}

// attach option to group "General blog settings"
if ($continue) {
	$oid = $wpdb->get_var("SELECT option_id FROM $tableoptions WHERE option_name = 'moderation_notify'");	    
	$gid = $wpdb->get_var("SELECT group_id FROM $tableoptiongroups WHERE group_name = 'General blog settings'");
	
	$seq = $wpdb->get_var("SELECT MAX(seq) FROM $tableoptiongroup_options WHERE group_id = '$gid'");

	++$seq;
	$wpdb->query("INSERT INTO $tableoptiongroup_options 
		(group_id, option_id, seq)
		VALUES 
		('$gid', '$oid', '$seq')");
}
$wpdb->show_errors();
?>
<p>Comment spammers should now watch out for you.</p>
<p>See, that didn&#8217;t hurt a bit (again). Now on to the <a href="upgrade-072-to-080.php?step=3">final step</a>.</p>
  <?php
	break;
	case 3:
?>

<h1>Step 3</h1>
<p>This is the final step, and the most exciting. We&#8217;re going to make it so each post can now have multiple categories. Hold on tight.</p>
<?php
// Create post2cat table
if ( maybe_create_table($tablepost2cat, "
	CREATE TABLE `$tablepost2cat` (
	`rel_id` INT NOT NULL AUTO_INCREMENT ,
	`post_id` INT NOT NULL ,
	`category_id` INT NOT NULL ,
	PRIMARY KEY ( `rel_id` ) ,
	INDEX ( `post_id` , `category_id` )
	)
	") )
	echo '<p>Table created successfully.</p>';
	else echo '<p>New category table already created. Skipping.</p>';
?>
<p>Now we need to transfer all your old categories into the new table.</p>
<p>Working...
<?php
// Transfer old categories in
$allposts = $wpdb->get_results("SELECT ID, post_category FROM $tableposts");

foreach ($allposts as $post) {
	// Check to see if it's already been imported
	$cat = $wpdb->get_row("SELECT * FROM $tablepost2cat WHERE post_id = $post->ID AND category_id = $post->post_category");
	if (!$cat) { // If there's no result
		$wpdb->query("
			INSERT INTO $tablepost2cat
			(post_id, category_id)
			VALUES
			($post->ID, $post->post_category)
			");
		echo '. ';
		flush();
	}
}

// Going to leave the post table category field in for now. We can remove it in a future version.
?>
 All done!</p>
 <p>You are ready to rock &#8217;n roll. <a href="../wp-login.php">Go crazy</a>.</p>
<?php
	break;
}
?> 
</body>
</html>
