<?php
$curpath = dirname(__FILE__).'/';
require_once ($curpath.'wp-config.php');
require_once ($curpath.WPINC.'/template-functions.php');
if (!empty($_SERVER["QUERY_STRING"])) {
	$location = get_bloginfo('rss2_url').'?'.$_SERVER["QUERY_STRING"];
}
else {
	$location = get_bloginfo('rss2_url');
}
header('HTTP/1.0 301 Moved Permanently');
header('Location: ' . $location . "\n");
exit;
?>