<?php
/**
 * Handle default dashboard widgets options AJAX.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** Load WordPress Bootstrap */
require_once( './admin.php' );

/** Load WordPress Administration Dashboard API */
require(ABSPATH . 'wp-admin/includes/dashboard.php' );

@header( 'Content-Type: ' . get_option( 'html_type' ) . '; charset=' . get_option( 'blog_charset' ) );
send_nosniff_header();

switch ( $_GET['jax'] ) {

case 'dashboard_incoming_links' :
	wp_dashboard_incoming_links_output();
	break;

case 'dashboard_primary' :
	wp_dashboard_rss_output( 'dashboard_primary' );
	break;

case 'dashboard_secondary' :
	wp_dashboard_secondary_output();
	break;

case 'dashboard_plugins' :
	wp_dashboard_plugins_output();
	break;

case 'dashboard_quick_press' :
	wp_dashboard_quick_press_output();
	break;

}

?>