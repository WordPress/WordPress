<?php
/**
 * Deprecated pluggable functions from past WordPress versions. You shouldn't use these
 * functions and look for the alternatives instead. The functions will be removed in a
 * later version.
 *
 * Deprecated warnings are also thrown if one of these functions is being defined by a plugin.
 *
 * @package WordPress
 * @subpackage Deprecated
 * @see pluggable.php
 */

/*
 * Deprecated functions come here to die.
 */

if ( !function_exists('set_current_user') ) :
/**
 * Changes the current user by ID or name.
 *
 * Set $id to null and specify a name if you do not know a user's ID.
 *
 * @since 2.0.1
 * @see wp_set_current_user() An alias of wp_set_current_user()
 * @deprecated 3.0.0
 * @deprecated Use wp_set_current_user()
 *
 * @param int|null $id User ID.
 * @param string $name Optional. The user's username
 * @return WP_User returns wp_set_current_user()
 */
function set_current_user($id, $name = '') {
	_deprecated_function( __FUNCTION__, '3.0', 'wp_set_current_user()' );
	return wp_set_current_user($id, $name);
}
endif;

if ( !function_exists('get_userdatabylogin') ) :
/**
 * Retrieve user info by login name.
 *
 * @since 0.71
 * @deprecated 3.3.0
 * @deprecated Use get_user_by('login')
 *
 * @param string $user_login User's username
 * @return bool|object False on failure, User DB row object
 */
function get_userdatabylogin($user_login) {
	_deprecated_function( __FUNCTION__, '3.3', "get_user_by('login')" );
	return get_user_by('login', $user_login);
}
endif;

if ( !function_exists('get_user_by_email') ) :
/**
 * Retrieve user info by email.
 *
 * @since 2.5.0
 * @deprecated 3.3.0
 * @deprecated Use get_user_by('email')
 *
 * @param string $email User's email address
 * @return bool|object False on failure, User DB row object
 */
function get_user_by_email($email) {
	_deprecated_function( __FUNCTION__, '3.3', "get_user_by('email')" );
	return get_user_by('email', $email);
}
endif;

if ( !function_exists('wp_setcookie') ) :
/**
 * Sets a cookie for a user who just logged in. This function is deprecated.
 *
 * @since 1.5.0
 * @deprecated 2.5.0
 * @deprecated Use wp_set_auth_cookie()
 * @see wp_set_auth_cookie()
 *
 * @param string $username The user's username
 * @param string $password Optional. The user's password
 * @param bool $already_md5 Optional. Whether the password has already been through MD5
 * @param string $home Optional. Will be used instead of COOKIEPATH if set
 * @param string $siteurl Optional. Will be used instead of SITECOOKIEPATH if set
 * @param bool $remember Optional. Remember that the user is logged in
 */
function wp_setcookie($username, $password = '', $already_md5 = false, $home = '', $siteurl = '', $remember = false) {
	_deprecated_function( __FUNCTION__, '2.5', 'wp_set_auth_cookie()' );
	$user = get_user_by('login', $username);
	wp_set_auth_cookie($user->ID, $remember);
}
else :
	_deprecated_function( 'wp_setcookie', '2.5', 'wp_set_auth_cookie()' );
endif;

if ( !function_exists('wp_clearcookie') ) :
/**
 * Clears the authentication cookie, logging the user out. This function is deprecated.
 *
 * @since 1.5.0
 * @deprecated 2.5.0
 * @deprecated Use wp_clear_auth_cookie()
 * @see wp_clear_auth_cookie()
 */
function wp_clearcookie() {
	_deprecated_function( __FUNCTION__, '2.5', 'wp_clear_auth_cookie()' );
	wp_clear_auth_cookie();
}
else :
	_deprecated_function( 'wp_clearcookie', '2.5', 'wp_clear_auth_cookie()' );
endif;

if ( !function_exists('wp_get_cookie_login') ):
/**
 * Gets the user cookie login. This function is deprecated.
 *
 * This function is deprecated and should no longer be extended as it won't be
 * used anywhere in WordPress. Also, plugins shouldn't use it either.
 *
 * @since 2.0.3
 * @deprecated 2.5.0
 * @deprecated No alternative
 *
 * @return bool Always returns false
 */
function wp_get_cookie_login() {
	_deprecated_function( __FUNCTION__, '2.5' );
	return false;
}
else :
	_deprecated_function( 'wp_get_cookie_login', '2.5' );
endif;

if ( !function_exists('wp_login') ) :
/**
 * Checks a users login information and logs them in if it checks out. This function is deprecated.
 *
 * Use the global $error to get the reason why the login failed. If the username
 * is blank, no error will be set, so assume blank username on that case.
 *
 * Plugins extending this function should also provide the global $error and set
 * what the error is, so that those checking the global for why there was a
 * failure can utilize it later.
 *
 * @since 1.2.2
 * @deprecated Use wp_signon()
 * @global string $error Error when false is returned
 *
 * @param string $username   User's username
 * @param string $password   User's password
 * @param string $deprecated Not used
 * @return bool False on login failure, true on successful check
 */
function wp_login($username, $password, $deprecated = '') {
	_deprecated_function( __FUNCTION__, '2.5', 'wp_signon()' );
	global $error;

	$user = wp_authenticate($username, $password);

	if ( ! is_wp_error($user) )
		return true;

	$error = $user->get_error_message();
	return false;
}
else :
	_deprecated_function( 'wp_login', '2.5', 'wp_signon()' );
endif;

/**
 * WordPress AtomPub API implementation.
 *
 * Originally stored in wp-app.php, and later wp-includes/class-wp-atom-server.php.
 * It is kept here in case a plugin directly referred to the class.
 *
 * @since 2.2.0
 * @deprecated 3.5.0
 * @link https://wordpress.org/plugins/atom-publishing-protocol/
 */
if ( ! class_exists( 'wp_atom_server' ) ) {
	class wp_atom_server {
		public function __call( $name, $arguments ) {
			_deprecated_function( __CLASS__ . '::' . $name, '3.5', 'the Atom Publishing Protocol plugin' );
		}

		public static function __callStatic( $name, $arguments ) {
			_deprecated_function( __CLASS__ . '::' . $name, '3.5', 'the Atom Publishing Protocol plugin' );
		}
	}
}