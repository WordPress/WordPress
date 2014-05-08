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

$action = empty( $_REQUEST['action'] ) ? '' : '_' . $_REQUEST['action'];

if ( ! wp_validate_auth_cookie() ) {
	/**
	 * Fires the requested handler action for logged-out users.
	 *
	 * The dynamic portion of the hook name, $action, refers to the handler action.
	 *
	 * @since 2.6.0
	 */
	do_action( "admin_post_nopriv{$action}" );
} else {
	/**
	 * Fires the requested handler action for logged-in users.
	 *
	 * The dynamic portion of the hook name, $action, refers to the handler action.
	 *
	 * @since 2.6.0
	 */
	do_action( "admin_post{$action}" );
}
