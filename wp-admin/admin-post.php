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

$action = '';

if ( !wp_validate_auth_cookie() )
	$action .= '_nopriv';

if ( !empty($_REQUEST['action']) )
	$action .= '_' . $_REQUEST['action'];

/**
 * Fires the requested handler action.
 *
 * The dynamic portion of the hook name, $action, refers to a combination
 * of whether the user is logged-in or not, and the requested handler action.
 *
 * If the user is logged-out, '_nopriv' will be affixed to the
 * base "admin_post" hook name. If a handler action was passed, that action
 * will also be affixed.
 * 
 * For example:
 * Hook combinations fired for logged-out users:
 * `admin_post_nopriv_{$action}` and `admin_post_nopriv` (no action supplied).
 *
 * Hook combinations fired for logged-in users:
 * `admin_post_{$action}` and `admin_post` (no action supplied).
 *
 * @since 2.6.0
 */
do_action( "admin_post{$action}" );
