<?php
/**
 * Handle default dashboard widgets options AJAX.
 *
 * @package WordPress
 * @subpackage Administration
 */

define('DOING_AJAX', true);

/** Load WordPress Bootstrap */
require_once( './admin.php' );

/** Load WordPress Administration Dashboard API */
require(ABSPATH . 'wp-admin/includes/dashboard.php' );

@header( 'Content-Type: ' . get_option( 'html_type' ) . '; charset=' . get_option( 'blog_charset' ) );
send_nosniff_header();

switch ( $_GET['jax'] ) {

case 'dashboard_incoming_links' :
	wp_dashboard_incoming_links();
	break;

case 'dashboard_primary' :
	wp_dashboard_primary();
	break;

case 'dashboard_secondary' :
	wp_dashboard_secondary();
	break;

case 'dashboard_plugins' :
	wp_dashboard_plugins();
	break;

}

?>