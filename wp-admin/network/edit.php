<?php
/**
 * Action handler for Multisite administration panels.
 *
 * @package WordPress
 * @subpackage Multisite
 * @since 3.0.0
 */

/** Load WordPress Administration Bootstrap */
require_once( './admin.php' );

if ( ! is_multisite() )
	wp_die( __( 'Multisite support is not enabled.' ) );

if ( empty( $_GET['action'] ) ) {
	wp_redirect( network_admin_url() );
	exit;
}

do_action( 'wpmuadminedit' , '' );

// Let plugins use us as a post handler easily
do_action( 'network_admin_edit_' . $_GET['action'] );

wp_redirect( network_admin_url() );
exit();
