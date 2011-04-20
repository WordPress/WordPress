<?php
/**
 * WordPress Network Administration Bootstrap
 *
 * @package WordPress
 * @subpackage Multisite
 * @since 3.1.0
 */

define( 'WP_NETWORK_ADMIN', TRUE );

/** Load WordPress Administration Bootstrap */
require_once( dirname( dirname( __FILE__ ) ) . '/admin.php' );

if ( ! is_multisite() )
	wp_die( __( 'Multisite support is not enabled.' ) );

$redirect_network_admin_request = ( ( $current_blog->domain != $current_site->domain ) || ( $current_blog->path != $current_site->path ) );
$redirect_network_admin_request = apply_filters( 'redirect_network_admin_request', $redirect_network_admin_request );
if ( $redirect_network_admin_request ) {
	wp_redirect( network_admin_url() );
	exit;
}
unset( $redirect_network_admin_request );
?>
