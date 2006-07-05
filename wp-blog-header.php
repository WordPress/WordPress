<?php

if (! isset($wp_did_header)):
if ( !file_exists( dirname(__FILE__) . '/wp-config.php') ) {
	if ( strstr( $_SERVER['PHP_SELF'], 'wp-admin') ) $path = '';
	else $path = 'wp-admin/';
    die("There doesn't seem to be a <code>wp-config.php</code> file. I need this before we can get started. Need more help? <a href='http://wordpress.org/docs/faq/#wp-config'>We got it</a>. You can <a href='{$path}setup-config.php'>create a <code>wp-config.php</code> file through a web interface</a>, but this doesn't work for all server setups. The safest way is to manually create the file.");
}

$wp_did_header = true;

require_once( dirname(__FILE__) . '/wp-config.php');

wp();
gzip_compression();

require_once(ABSPATH . WPINC . '/template-loader.php');

endif;

?>
