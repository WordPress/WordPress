<?php
	define('DEBUG',              false);    // Write debugging information to a log file
    define('SEND_ETAG',          true);     // You will want to disable this if you load balance multiple servers
    define('SEND_EXPIRES',       true);     //
    define('SEND_CACHE_CONTROL', true);     //
    define('DOWNSIZE_NOT_FOUND', true);     // If a regular image is requested and not found, send a retina file instead?
    define('CACHE_TIME',         24*60*60); // default: 1 day
	
	// Retina Images doesn't handle the float value for the cookie devicePixelRatio, so let's ceil it!
	if ( isset( $_COOKIE['devicePixelRatio'] ) ) {
		$_COOKIE['devicePixelRatio'] = ceil(floatval($_COOKIE['devicePixelRatio']));
	}
	
	require('wr2x_retinaimages.php');
?>
