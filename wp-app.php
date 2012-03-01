<?php
/**
 * Atom Publishing Protocol support for WordPress
 *
 * @version 1.0.5-dc
 */

/**
 * WordPress is handling an Atom Publishing Protocol request.
 *
 * @var bool
 */
define('APP_REQUEST', true);

/** Set up WordPress environment */
require_once('./wp-load.php');

/** Atom Publishing Protocol Class */
require_once(ABSPATH . WPINC . '/atomlib.php');

/** Atom Server **/
require_once(ABSPATH . WPINC . '/class-wp-atom-server.php');

/** Admin Image API for metadata updating */
require_once(ABSPATH . '/wp-admin/includes/image.php');

$_SERVER['PATH_INFO'] = preg_replace( '/.*\/wp-app\.php/', '', $_SERVER['REQUEST_URI'] );

/**
 * Writes logging info to a file.
 *
 * @since 2.2.0
 * @deprecated 3.4.0
 * @deprecated Use error_log()
 * @link http://www.php.net/manual/en/function.error-log.php
 *
 * @param string $label Type of logging
 * @param string $msg Information describing logging reason.
 */
function log_app( $label, $msg ) {
	_deprecated_function( __FUNCTION__, '3.4', 'error_log()' );
	if ( ! empty( $GLOBALS['app_logging'] ) )
			error_log( $label . ' - ' . $message );
}

// Allow for a plugin to insert a different class to handle requests.
$wp_atom_server_class = apply_filters('wp_atom_server_class', 'wp_atom_server');
$wp_atom_server = new $wp_atom_server_class;

// Handle the request
$wp_atom_server->handle_request();
