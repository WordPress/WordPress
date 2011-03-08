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

if ( ( $current_blog->domain != $current_site->domain ) || ( $current_blog->path != $current_site->path ) ) {
	wp_redirect( network_admin_url() );
	exit;
}
?>
