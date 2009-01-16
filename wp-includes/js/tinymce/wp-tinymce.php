<?php

while ( @ob_end_clean() );

function get_file($path) {

	if ( function_exists('realpath') )
		$path = realpath($path);

	if ( ! $path || ! @is_file($path) )
		return '';

	return @file_get_contents($path);
}

$expires_offset = 31536000;

header('Content-Type: application/x-javascript; charset=UTF-8');
header('Vary: Accept-Encoding'); // Handle proxies
header('Expires: ' . gmdate( "D, d M Y H:i:s", time() + $expires_offset ) . ' GMT');
header("Cache-Control: public, max-age=$expires_offset");

if ( isset($_GET['c']) && 1 == $_GET['c'] && ! ini_get('zlib.output_compression') && false !== strpos( strtolower($_SERVER['HTTP_ACCEPT_ENCODING']), 'gzip') ) {
	header('Content-Encoding: gzip');
	echo get_file('wp-tinymce.js.gz');
} else {
	echo get_file('wp-tinymce.js');
}
exit;
