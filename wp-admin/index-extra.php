<?php
require_once('admin.php');
require( 'includes/dashboard.php' );
require_once (ABSPATH . WPINC . '/rss.php');

@header('Content-Type: ' . get_option('html_type') . '; charset=' . get_option('blog_charset'));

switch ( $_GET['jax'] ) {

case 'incominglinks' :
	wp_dashboard_incoming_links_output();
	break;

case 'devnews' :
	wp_dashboard_rss_output( 'dashboard_primary' );
	break;

case 'planetnews' :
	wp_dashboard_secondary_output();
	break;

case 'plugins' :
	wp_dashboard_plugins_output();
	break;

}

?>
