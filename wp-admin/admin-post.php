<?php
/**
 * WordPress Generic Request (POST/GET) Handler
 *
 * Intended for form submission handling in themes and plugins.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** We are located in WordPress Administration Screens */
define('WP_ADMIN', true);

if ( defined('ABSPATH') )
	require_once(ABSPATH . 'wp-load.php');
else
	require_once( dirname( dirname( __FILE__ ) ) . '/wp-load.php' );

/** Allow for cross-domain requests (from the frontend). */
send_origin_headers();

require_once(ABSPATH . 'wp-admin/includes/admin.php');

nocache_headers();

/** This action is documented in wp-admin/admin.php */
do_action( 'admin_init' );

$action = 'admin_post';

if ( !wp_validate_auth_cookie() )
	$action .= '_nopriv';

if ( !empty($_REQUEST['action']) )
	$action .= '_' . $_REQUEST['action'];

/**
 * Fires the requested handler action.
 *
 * admin_post_nopriv_{$_REQUEST['action']} is called for not-logged-in users.
 * admin_post_{$_REQUEST['action']} is called for logged-in users.
 *
 * @since 2.6.0
 */
do_action( $action );
