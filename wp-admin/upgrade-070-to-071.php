<?php
require('../b2config.php');

$step = $HTTP_GET_VARS['step'];
if (!$step) $step = 0;
if (!step) $step = 0;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<title>WordPress > .70 to .71 Upgrade</title>
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
		height: 72px;
	}
	#logo a span {
		display: none;
	}
	p {
		line-height: 140%;
	}
	</style>
</head>
<body>
<h1 id="logo"><a href="http://wordpress.org"><span>WordPress</span></a></h1>
<?php
switch($step) {

	case 0:
?>
<p>Welcome to WordPress. You're already part of the family so this should be familiar 
  to you now. We think you'll find to like in this latest version, here are some 
  things to watch out for:</p>
<ul>
  <li>Some files have been eliminated, so it's generally safest to delete all 
    your old files before re-uploading the new ones.</li>
  <li>You should update your templates to no longer include or call trackback 
    or pingback functions. See the new <code>index.php</code> for an example.</li>
  <li>The configuration file is different so it's best if you redo that as well. 
    This is the very last time you'll have to, we promise.</li>
  <li>If you have any troubles try out the <a href="http://wordpress.org/support/">support 
    forums</a>.</li>
  <li><strong>Back up</strong> your database before you do anything. Yes, you. 
    Right now.</li>
</ul>
<p><code></code>Have you looked at the <a href="../readme.html">readme</a>? If 
  you&#8217;re all ready, <a href="upgrade-070-to-071.php?step=1">let's go</a>! </p>
<?php
	break;
	
	case 1:
?>
<h1>Step 1</h1>
<p>There are a few database changes with this version, so lets get those out of 
  the way.</p>
<?php
$query = "ALTER TABLE $tableposts ADD `post_status` ENUM('publish','draft','private') NOT NULL,
ADD `comment_status` ENUM('open','closed') NOT NULL,
ADD `ping_status` ENUM('open','closed') NOT NULL,
ADD post_password varchar(20) NOT NULL;";
$q = $wpdb->query($query);
?>
<p>See, that didn't hurt a bit. All done!</p>
<?php
	break;
}
?>

</body>
</html>