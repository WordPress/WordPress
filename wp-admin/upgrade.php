<?php
$_wp_installing = 1;
if (!file_exists('../wp-config.php')) die("There doesn't seem to be a wp-config.php file. Double check that you updated wp-config-sample.php with the proper database connection information and renamed it to wp-config.php.");
require('../wp-config.php');
timer_start();
require_once(ABSPATH . '/wp-admin/upgrade-functions.php');

$step = $_GET['step'];
if (!$step) $step = 0;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>WordPress &rsaquo; Upgrade</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<style media="screen" type="text/css">
	<!--
	html {
		background: #eee;
	}
	body {
		background: #fff;
		color: #000;
		font-family: Georgia, "Times New Roman", Times, serif;
		margin-left: 20%;
		margin-right: 20%;
		padding: .2em 2em;
	}
	
	h1 {
		color: #006;
		font-size: 18px;
		font-weight: lighter;
	}
	
	h2 {
		font-size: 16px;
	}
	
	p, li, dt {
		line-height: 140%;
		padding-bottom: 2px;
	}

	ul, ol {
		padding: 5px 5px 5px 20px;
	}
	#logo {
		margin-bottom: 2em;
	}
.step a, .step input {
	font-size: 2em;
}
.step, th {
	text-align: right;
}
#footer {
text-align: center; border-top: 1px solid #ccc; padding-top: 1em; font-style: italic;
}
	-->
	</style>
</head>
<body>
<h1 id="logo"><img alt="WordPress" src="http://static.wordpress.org/logo.png" /></h1>
<?php
switch($step) {

	case 0:
?> 
<p>This file upgrades you from any previous version of WordPress to the latest. It may take a while though, so be patient.</p> 
<h2 class="step"><a href="upgrade.php?step=1">Upgrade WordPress &raquo;</a></h2>
<?php
	break;
	
	case 1:
	make_db_current_silent();
	upgrade_all();
?> 
<h2>Step 1</h2> 
<p>There's actually only one step. So if you see this, you're done. <a href="../">Have fun</a>! </p>

<!--
<pre>
<?php echo $wpdb->num_queries; ?> queries

<?php timer_stop(1); ?> seconds
</pre>
-->

<?php
	break;
}
?> 
</body>
</html>
