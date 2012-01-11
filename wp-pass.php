<?php
/**
 * Creates the password cookie and redirects back to where the
 * visitor was before.
 *
 * @package WordPress
 */

/** Make sure that the WordPress bootstrap has run before continuing. */
require( dirname( __FILE__ ) . '/wp-load.php');

if ( empty( $wp_hasher ) ) {
	require_once( ABSPATH . 'wp-includes/class-phpass.php');
	// By default, use the portable hash from phpass
	$wp_hasher = new PasswordHash(8, true);
}

// 10 days
setcookie( 'wp-postpass_' . COOKIEHASH, $wp_hasher->HashPassword( stripslashes( $_POST['post_password'] ) ), time() + 864000, COOKIEPATH );

wp_safe_redirect( wp_get_referer() );
exit;
