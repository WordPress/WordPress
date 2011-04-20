<?php
/**
 * WordPress User Administration Bootstrap
 *
 * @package WordPress
 * @subpackage Administration
 * @since 3.1.0
 */

define('WP_USER_ADMIN', TRUE);

require_once( dirname(dirname(__FILE__)) . '/admin.php');

if ( ! is_multisite() ) {
	wp_redirect( admin_url() );
	exit;
}

$redirect_user_admin_request = ( ( $current_blog->domain != $current_site->domain ) || ( $current_blog->path != $current_site->path ) );
$redirect_user_admin_request = apply_filters( 'redirect_user_admin_request', $redirect_user_admin_request );
if ( $redirect_user_admin_request ) {
	wp_redirect( user_admin_url() );
	exit;
}
unset( $redirect_user_admin_request );

?>
