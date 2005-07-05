<?php
require_once( dirname( dirname(__FILE__) ) . '/wp-config.php');
require_once( ABSPATH . 'wp-includes/class-snoopy.php');

if ( !get_option('use_linksupdate') )
	die('Feature disabled.');

$link_uris = $wpdb->get_col("SELECT link_url FROM $wpdb->links");

if ( !$link_uris )
	die('No links');

$link_uris = urlencode( join( $link_uris, "\n" ) );

$query_string = "uris=$link_uris";

$http_request  = "POST /updated-batch/ HTTP/1.0\r\n";
$http_request .= "Host: api.pingomatic.com\r\n";
$http_request .= 'Content-Type: application/x-www-form-urlencoded; charset='.get_settings('blog_charset')."\r\n";
$http_request .= 'Content-Length: ' . strlen($query_string) . "\r\n";
$http_request .= 'User-Agent: WordPress/' . $wp_version . "\r\n";
$http_request .= "\r\n";
$http_request .= $query_string;

$response = '';
if( false !== ( $fs = fsockopen('api.pingomatic.com', 80, $errno, $errstr, 5) ) ) {
	fwrite($fs, $http_request);
	while ( !feof($fs) )
		$response .= fgets($fs, 1160); // One TCP-IP packet
	fclose($fs);
    
	$response = explode("\r\n\r\n", $response, 2);
	$body = trim( $response[1] );
	$body = str_replace(array("\r\n", "\r"), "\n", $body);
    
	$returns = explode("\n", $body);
    
	foreach ($returns as $return) :
		$time = $wpdb->escape( substr($return, 0, 19) );
		$uri = $wpdb->escape( preg_replace('/(.*?) | (.*?)/', '$2', $return) );
		$wpdb->query("UPDATE $wpdb->links SET link_updated = '$time' WHERE link_url = '$uri'");
	endforeach;
}
?>