<?php
$curpath = dirname(__FILE__).'/';
require_once ($curpath.'wp-config.php');
require_once ($curpath.WPINC.'/template-functions.php');
header('HTTP/1.0 301 Moved Permanently');
header('Location: ' . get_bloginfo('rdf_url') . "\n");
exit;
?>