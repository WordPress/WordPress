<?php
require_once('../wp-config.php');
require('wp-install-helper.php');

$step = $HTTP_GET_VARS['step'];
if (!$step) $step = 0;
if (!step) $step = 0;
update_option('blogdescription', 'hahahah');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<title>WordPress &rsaquo; .72 to .73 Upgrade</title>
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
<p>If you&#8217;re all ready, <a href="upgrade-072-to-073.php?step=1">let's go</a>! </p> 
<?php
	break;
	
	case 1:
?> 
<h1>Step 1</h1> 
<p>If it isn&#8217;t there already, let&#8217;s add a field new to this version.</p> 
<?php
// Create post_name field
$query = "ALTER TABLE `$tableposts` ADD `post_name` VARCHAR(200) NOT NULL";
maybe_add_column($tableposts, 'post_name', $query);

// Create index if it isn't there already, suppress errors if it is
$wpdb->hide_errors();
$wpdb->query("ALTER TABLE `$tableposts` ADD INDEX (`post_name`)");
$wpdb->show_errors();
?> 
<p><strong>Done.</strong></p> 
<p>Now let's populate the new field.</p> 
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

$wpdb->query("INSERT INTO `$tableoptions` (`option_id`, `blog_id`, `option_name`, `option_can_override`, `option_type`, `option_value`, `option_width`, `option_height`, `option_description`, `option_admin_level`) VALUES ('', '0', 'permalink_structure', 'Y', '3', '', '20', '8', 'How the permalinks for your site are constructed.', '8');");
?> 
  <strong>Done.</strong> </p> 
<p>See, that didn&#8217;t hurt a bit. All done!</p> 
<?php
	break;
}
?> 
</body>
</html>