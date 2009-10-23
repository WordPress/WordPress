<?php
/**
 * Send blog links to pingomatic.com to update.
 *
 * You can disable this feature by deleting the option 'use_linksupdate' or
 * setting the option to false. If no links exist, then no links are sent.
 *
 * Snoopy is included, but is not used. Fsockopen() is used instead to send link
 * URLs.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** Load WordPress Bootstrap */
require_once('../wp-load.php');

if ( !get_option('use_linksupdate') )
	wp_die(__('Feature disabled.'));

$link_uris = $wpdb->get_col("SELECT link_url FROM $wpdb->links");

if ( !$link_uris )
	wp_die(__('No links'));

$link_uris = urlencode( join( $link_uris, "\n" ) );

$query_string = "uris=$link_uris";

$options = array();
$options['timeout'] = 30;
$options['body'] = $query_string;

$options['headers'] = array(
	'content-type' => 'application/x-www-form-urlencoded; charset='.get_option('blog_charset'),
	'content-length' => strlen( $query_string ),
);

$response = wp_remote_get('http://api.pingomatic.com/updated-batch/', $options);

if ( is_wp_error( $response ) )
	wp_die(__('Request Failed.'));

if ( $response['response']['code'] != 200 )
	wp_die(__('Request Failed.'));

$body = str_replace(array("\r\n", "\r"), "\n", $response['body']);
$returns = explode("\n", $body);

foreach ($returns as $return) {
	$time = substr($return, 0, 19);
	$uri = preg_replace('/(.*?) | (.*?)/', '$2', $return);
	$wpdb->update( $wpdb->links, array('link_updated' => $time), array('link_url' => $uri) );
}

?>
