<?php
require_once ('./wp-config.php');
if (!empty($_SERVER["QUERY_STRING"])) {
	$location = get_bloginfo('rdf_url').'?'.$_SERVER["QUERY_STRING"];
}
else {
	$location = get_bloginfo('rdf_url');
}
header('HTTP/1.0 301 Moved Permanently');
header('Location: ' . $location . "\n");
exit;
?>