<?php
$_wp_installing = 1;
if (!file_exists('../wp-config.php')) die("There doesn't seem to be a wp-config.php file. Double check that you updated wp-config.sample.php with the proper database connection information and renamed it to wp-config.php.");
require('../wp-config.php');
require('upgrade-functions.php');

$step = $HTTP_GET_VARS['step'];
if (!$step) $step = 0;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<title>WordPress &rsaquo; Upgrade WordPress</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<style media="screen" type="text/css">
	body {
		background-color: white;
		font-family: Georgia, "Times New Roman", Times, serif;
		margin-left: 15%;
		margin-right: 15%;
	}
	#logo {
		margin: 0;
		padding: 0;
		background-image: url(http://wordpress.org/images/logo.png);
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
<p>This file upgrades you from any previous version of WordPress to the latest. It may take a while though, so be patient. </p> 
<p>If you&#8217;re all ready, <a href="upgrade.php?step=1">let's go</a>! </p> 
<?php
	break;
	
	case 1:
	upgrade_all();
?> 
<h2>Step 1</h2> 
<p>There's actually only one step. So if you see this, you're done. <a href="../">Have fun</a>! </p>
<?php
	break;
}
?> 
</body>
</html>
