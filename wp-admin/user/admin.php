<?php
/**
 * WordPress User Administration Bootstrap
 *
 * @package WordPress
 * @subpackage Administration
 * @since 3.1.0
 */

define( 'WP_USER_ADMIN', true );

require_once dirname( __DIR__ ) . '/admin.php';

if ( ! is_multisite() ) {
	wp_redirect( admin_url() );
	exit;
}

$redirect_user_admin_request = ( 0 !== strcasecmp( $current_blog->domain, $current_site->domain ) || 0 !== strcasecmp( $current_blog->path, $current_site->path ) );

/**
 * Filters whether to redirect the request to the User Admin in Multisite.
 *
 * @since 3.2.0
 *
 * @param bool $redirect_user_admin_request Whether the request should be redirected.
 */
$redirect_user_admin_request = apply_filters( 'redirect_user_admin_request', $redirect_user_admin_request );

if ( $redirect_user_admin_request ) {
	wp_redirect( user_admin_url() );
	exit;
}

unset( $redirect_user_admin_request );
