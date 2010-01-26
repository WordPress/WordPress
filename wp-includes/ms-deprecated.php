<?php
/**
 * Deprecated functions from WordPress MU and the multisite feature. You shouldn't
 * use these functions and look for the alternatives instead. The functions will be
 * removed in a later version.
 *
 * @package WordPress
 * @subpackage Deprecated
 */

/*
 * Deprecated functions come here to die.
 */

/**
 * @since unknown
 * @deprecated 3.0
 * @deprecated Use wp_generate_password()
 * @see wp_generate_password()
 */
function generate_random_password( $len = 8 ) {
	_deprecated_function( __FUNCTION__, '3.0', 'wp_generate_password()' );
	return wp_generate_password($len);
}

/**
 * Determine if user is a site admin.
 *
 * Plugins should use is_multisite() instead of checking if this function exists
 * to determine if multisite is enabled.
 *
 * This function must reside in a file included only if is_multisite() due to
 * legacy function_exists() checks to determine if multisite is enabled.
 *
 * @since unknown
 * @deprecated 3.0
 * @deprecated Use is_super_admin()
 * @see is_super_admin()
 * @see is_multisite()
 *
 */
function is_site_admin( $user_login = '' ) {
	_deprecated_function( __FUNCTION__, '3.0', 'is_super_admin()' );

	if ( empty( $user_login ) ) {
		$user_id = get_current_user_id();
		if ( !$user_id )
			return false;
	} else {
		$user = new WP_User( null, $user_login) ;
		if ( empty( $user->id ) )
			return false;
		$user_id = $user->id;
	}

	return is_super_admin( $user_id );
}

if ( !function_exists('graceful_fail') ) :
/**
 * @deprecated 3.0
 */
function graceful_fail( $message ) {
	_deprecated_function( __FUNCTION__, '3.0', 'wp_die()' );
	$message = apply_filters('graceful_fail', $message);
	$message_template = apply_filters( 'graceful_fail_template',
'<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><head profile="http://gmpg.org/xfn/11">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Error!</title>
<style type="text/css">
img {
	border: 0;
}
body {
line-height: 1.6em; font-family: Georgia, serif; width: 390px; margin: auto;
text-align: center;
}
.message {
	font-size: 22px;
	width: 350px;
	margin: auto;
}
</style>
</head>
<body>
<p class="message">%s</p>
</body>
</html>' );
	die( sprintf( $message_template, $message ) );
}
endif;

/**
 * @deprecated 3.0
 */
function get_current_user_id() {
	_deprecated_function( __FUNCTION__, '3.0', '' );

	global $current_user;
	return $current_user->ID;
}

/**
 * @deprecated 3.0
 */
function get_user_details( $username ) {
	_deprecated_function( __FUNCTION__, '3.0', 'get_user_by()' );

	return get_user_by('login', $username);
}

?>