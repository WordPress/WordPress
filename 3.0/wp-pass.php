<?php
/**
 * Creates the password cookie and redirects back to where the
 * visitor was before.
 *
 * @package WordPress
 */

/** Make sure that the WordPress bootstrap has run before continuing. */
require( dirname(__FILE__) . '/wp-load.php');

if ( get_magic_quotes_gpc() )
	$_POST['post_password'] = stripslashes($_POST['post_password']);

// 10 days
setcookie('wp-postpass_' . COOKIEHASH, $_POST['post_password'], time() + 864000, COOKIEPATH);

wp_safe_redirect(wp_get_referer());
?>