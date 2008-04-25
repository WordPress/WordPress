<?php
/**
 * These functions can be replaced via plugins. They are loaded after
 * plugins are loaded.
 *
 * @package WordPress
 */

if ( !function_exists('set_current_user') ) :
/**
 * set_current_user() - Populates global user information for any user
 *
 * Set $id to null and specify a name if you do not know a user's ID
 *
 * @since 2.0.1
 * @see wp_set_current_user() An alias of wp_set_current_user()
 *
 * @param int|null $id User ID.
 * @param string $name Optional. The user's username
 * @return object returns wp_set_current_user()
 */
function set_current_user($id, $name = '') {
	return wp_set_current_user($id, $name);
}
endif;

if ( !function_exists('wp_set_current_user') ) :
/**
 * wp_set_current_user() - Changes the current user by ID or name
 *
 * Set $id to null and specify a name if you do not know a user's ID
 *
 * Some WordPress functionality is based on the current user and
 * not based on the signed in user. Therefore, it opens the ability
 * to edit and perform actions on users who aren't signed in.
 *
 * @since 2.0.4
 * @global object $current_user The current user object which holds the user data.
 * @uses do_action() Calls 'set_current_user' hook after setting the current user.
 *
 * @param int $id User ID
 * @param string $name User's username
 * @return WP_User Current user User object
 */
function wp_set_current_user($id, $name = '') {
	global $current_user;

	if ( isset($current_user) && ($id == $current_user->ID) )
		return $current_user;

	$current_user = new WP_User($id, $name);

	setup_userdata($current_user->ID);

	do_action('set_current_user');

	return $current_user;
}
endif;

if ( !function_exists('wp_get_current_user') ) :
/**
 * wp_get_current_user() - Retrieve the current user object
 *
 * @since 2.0.4
 *
 * @return WP_User Current user WP_User object
 */
function wp_get_current_user() {
	global $current_user;

	get_currentuserinfo();

	return $current_user;
}
endif;

if ( !function_exists('get_currentuserinfo') ) :
/**
 * get_currentuserinfo() - Populate global variables with information about the currently logged in user
 *
 * Will set the current user, if the current user is not set. The current
 * user will be set to the logged in person. If no user is logged in, then
 * it will set the current user to 0, which is invalid and won't have any
 * permissions.
 *
 * @since 0.71
 * @uses $current_user Checks if the current user is set
 * @uses wp_validate_auth_cookie() Retrieves current logged in user.
 *
 * @return bool|null False on XMLRPC Request and invalid auth cookie. Null when current user set
 */
function get_currentuserinfo() {
	global $current_user;

	if ( defined('XMLRPC_REQUEST') && XMLRPC_REQUEST )
		return false;

	if ( ! empty($current_user) )
		return;

	if ( ! $user = wp_validate_auth_cookie() ) {
		wp_set_current_user(0);
		return false;
	}

	wp_set_current_user($user);
}
endif;

if ( !function_exists('get_userdata') ) :
/**
 * get_userdata() - Retrieve user info by user ID
 *
 * @since 0.71
 *
 * @param int $user_id User ID
 * @return bool|object False on failure, User DB row object
 */
function get_userdata( $user_id ) {
	global $wpdb;

	$user_id = absint($user_id);
	if ( $user_id == 0 )
		return false;

	$user = wp_cache_get($user_id, 'users');

	if ( $user )
		return $user;

	if ( !$user = $wpdb->get_row($wpdb->prepare("SELECT * FROM $wpdb->users WHERE ID = %d LIMIT 1", $user_id)) )
		return false;

	_fill_user($user);

	return $user;
}
endif;

if ( !function_exists('update_user_cache') ) :
/**
 * update_user_cache() - Updates a users cache when overridden by a plugin
 *
 * Core function does nothing.
 *
 * @since 1.5
 *
 * @return bool Only returns true
 */
function update_user_cache() {
	return true;
}
endif;

if ( !function_exists('get_userdatabylogin') ) :
/**
 * get_userdatabylogin() - Retrieve user info by login name
 *
 * @since 0.71
 *
 * @param string $user_login User's username
 * @return bool|object False on failure, User DB row object
 */
function get_userdatabylogin($user_login) {
	global $wpdb;
	$user_login = sanitize_user( $user_login );

	if ( empty( $user_login ) )
		return false;

	$user_id = wp_cache_get($user_login, 'userlogins');

	$user = false;
	if ( false !== $user_id )
		$user = wp_cache_get($user_id, 'users');

	if ( false !== $user )
		return $user;

	if ( !$user = $wpdb->get_row($wpdb->prepare("SELECT * FROM $wpdb->users WHERE user_login = %s", $user_login)) )
		return false;

	_fill_user($user);

	return $user;
}
endif;

if ( !function_exists('get_user_by_email') ) :
/**
 * get_user_by_email() - Retrieve user info by email
 *
 * @since 2.5
 *
 * @param string $email User's email address
 * @return bool|object False on failure, User DB row object
 */
function get_user_by_email($email) {
	global $wpdb;

	$user_id = wp_cache_get($email, 'useremail');

	$user = false;
	if ( false !== $user_id )
		$user = wp_cache_get($user_id, 'users');

	if ( false !== $user )
		return $user;

	if ( !$user = $wpdb->get_row($wpdb->prepare("SELECT * FROM $wpdb->users WHERE user_email = %s", $email)) )
		return false;

	_fill_user($user);

	return $user;
}
endif;

if ( !function_exists( 'wp_mail' ) ) :
/**
 * wp_mail() - Function to send mail, similar to PHP's mail
 *
 * A true return value does not automatically mean that the
 * user received the email successfully. It just only means
 * that the method used was able to process the request
 * without any errors.
 *
 * Using the two 'wp_mail_from' and 'wp_mail_from_name' hooks
 * allow from creating a from address like 'Name <email@address.com>'
 * when both are set. If just 'wp_mail_from' is set, then just
 * the email address will be used with no name.
 *
 * The default content type is 'text/plain' which does not
 * allow using HTML. However, you can set the content type
 * of the email by using the 'wp_mail_content_type' filter.
 *
 * The default charset is based on the charset used on the
 * blog. The charset can be set using the 'wp_mail_charset'
 * filter.
 *
 * @since 1.2.1
 * @uses apply_filters() Calls 'wp_mail' hook on an array of all of the parameters.
 * @uses apply_filters() Calls 'wp_mail_from' hook to get the from email address.
 * @uses apply_filters() Calls 'wp_mail_from_name' hook to get the from address name.
 * @uses apply_filters() Calls 'wp_mail_content_type' hook to get the email content type.
 * @uses apply_filters() Calls 'wp_mail_charset' hook to get the email charset
 * @uses do_action_ref_array() Calls 'phpmailer_init' hook on the reference to
 *		phpmailer object.
 * @uses PHPMailer
 * @
 *
 * @param string $to Email address to send message
 * @param string $subject Email subject
 * @param string $message Message contents
 * @param string|array $headers Optional. Additional headers.
 * @return bool Whether the email contents were sent successfully.
 */
function wp_mail( $to, $subject, $message, $headers = '' ) {
	// Compact the input, apply the filters, and extract them back out
	extract( apply_filters( 'wp_mail', compact( 'to', 'subject', 'message', 'headers' ) ) );

	global $phpmailer;

	// (Re)create it, if it's gone missing
	if ( !is_object( $phpmailer ) || !is_a( $phpmailer, 'PHPMailer' ) ) {
		require_once ABSPATH . WPINC . '/class-phpmailer.php';
		require_once ABSPATH . WPINC . '/class-smtp.php';
		$phpmailer = new PHPMailer();
	}

	// Headers
	if ( empty( $headers ) ) {
		$headers = array();
	} elseif ( !is_array( $headers ) ) {
		// Explode the headers out, so this function can take both
		// string headers and an array of headers.
		$tempheaders = (array) explode( "\n", $headers );
		$headers = array();

		// If it's actually got contents
		if ( !empty( $tempheaders ) ) {
			// Iterate through the raw headers
			foreach ( $tempheaders as $header ) {
				if ( strpos($header, ':') === false )
					continue;
				// Explode them out
				list( $name, $content ) = explode( ':', trim( $header ), 2 );

				// Cleanup crew
				$name = trim( $name );
				$content = trim( $content );

				// Mainly for legacy -- process a From: header if it's there
				if ( 'from' == strtolower($name) ) {
					if ( strpos($content, '<' ) !== false ) {
						// So... making my life hard again?
						$from_name = substr( $content, 0, strpos( $content, '<' ) - 1 );
						$from_name = str_replace( '"', '', $from_name );
						$from_name = trim( $from_name );

						$from_email = substr( $content, strpos( $content, '<' ) + 1 );
						$from_email = str_replace( '>', '', $from_email );
						$from_email = trim( $from_email );
					} else {
						$from_name = trim( $content );
					}
				} elseif ( 'content-type' == strtolower($name) ) {
					if ( strpos( $content,';' ) !== false ) {
						list( $type, $charset ) = explode( ';', $content );
						$content_type = trim( $type );
						$charset = trim( str_replace( array( 'charset=', '"' ), '', $charset ) );
					} else {
						$content_type = trim( $content );
					}
				} else {
					// Add it to our grand headers array
					$headers[trim( $name )] = trim( $content );
				}
			}
		}
	}

	// Empty out the values that may be set
	$phpmailer->ClearAddresses();
	$phpmailer->ClearAllRecipients();
	$phpmailer->ClearAttachments();
	$phpmailer->ClearBCCs();
	$phpmailer->ClearCCs();
	$phpmailer->ClearCustomHeaders();
	$phpmailer->ClearReplyTos();

	// From email and name
	// If we don't have a name from the input headers
	if ( !isset( $from_name ) ) {
		$from_name = 'WordPress';
	}

	// If we don't have an email from the input headers
	if ( !isset( $from_email ) ) {
		// Get the site domain and get rid of www.
		$sitename = strtolower( $_SERVER['SERVER_NAME'] );
		if ( substr( $sitename, 0, 4 ) == 'www.' ) {
			$sitename = substr( $sitename, 4 );
		}

		$from_email = 'wordpress@' . $sitename;
	}

	// Set the from name and email
	$phpmailer->From = apply_filters( 'wp_mail_from', $from_email );
	$phpmailer->FromName = apply_filters( 'wp_mail_from_name', $from_name );

	// Set destination address
	$phpmailer->AddAddress( $to );

	// Set mail's subject and body
	$phpmailer->Subject = $subject;
	$phpmailer->Body = $message;

	// Set to use PHP's mail()
	$phpmailer->IsMail();

	// Set Content-Type and charset
	// If we don't have a content-type from the input headers
	if ( !isset( $content_type ) ) {
		$content_type = 'text/plain';
	}

	$content_type = apply_filters( 'wp_mail_content_type', $content_type );

	// Set whether it's plaintext or not, depending on $content_type
	if ( $content_type == 'text/html' ) {
		$phpmailer->IsHTML( true );
	} else {
		$phpmailer->IsHTML( false );
	}

	// If we don't have a charset from the input headers
	if ( !isset( $charset ) ) {
		$charset = get_bloginfo( 'charset' );
	}

	// Set the content-type and charset
	$phpmailer->CharSet = apply_filters( 'wp_mail_charset', $charset );

	// Set custom headers
	if ( !empty( $headers ) ) {
		foreach ( $headers as $name => $content ) {
			$phpmailer->AddCustomHeader( sprintf( '%1$s: %2$s', $name, $content ) );
		}
	}

	do_action_ref_array( 'phpmailer_init', array( &$phpmailer ) );

	// Send!
	$result = @$phpmailer->Send();

	return $result;
}
endif;

/**
 * wp_authenticate() - Checks a user's login information and logs them in if it checks out
 * @since 2.5
 *
 * @param string $username User's username
 * @param string $password User's password
 * @return WP_Error|WP_User WP_User object if login successful, otherwise WP_Error object.
 */
if ( !function_exists('wp_authenticate') ) :
function wp_authenticate($username, $password) {
	$username = sanitize_user($username);

	if ( '' == $username )
		return new WP_Error('empty_username', __('<strong>ERROR</strong>: The username field is empty.'));

	if ( '' == $password )
		return new WP_Error('empty_password', __('<strong>ERROR</strong>: The password field is empty.'));

	$user = get_userdatabylogin($username);

	if ( !$user || ($user->user_login != $username) ) {
		do_action( 'wp_login_failed', $username );
		return new WP_Error('invalid_username', __('<strong>ERROR</strong>: Invalid username.'));
	}

	$user = apply_filters('wp_authenticate_user', $user, $password);
	if ( is_wp_error($user) ) {
		do_action( 'wp_login_failed', $username );
		return $user;
	}

	if ( !wp_check_password($password, $user->user_pass, $user->ID) ) {
		do_action( 'wp_login_failed', $username );
		return new WP_Error('incorrect_password', __('<strong>ERROR</strong>: Incorrect password.'));
	}

	return new WP_User($user->ID);
}
endif;

/**
 * wp_logout() - Log the current user out
 * @since 2.5
 *
 */
if ( !function_exists('wp_logout') ) :
function wp_logout() {
	wp_clear_auth_cookie();
	do_action('wp_logout');
}
endif;

if ( !function_exists('wp_validate_auth_cookie') ) :
/**
 * wp_validate_auth_cookie() - Validates authentication cookie
 *
 * The checks include making sure that the authentication cookie
 * is set and pulling in the contents (if $cookie is not used).
 *
 * Makes sure the cookie is not expired. Verifies the hash in
 * cookie is what is should be and compares the two.
 *
 * @since 2.5
 *
 * @param string $cookie Optional. If used, will validate contents instead of cookie's
 * @return bool|int False if invalid cookie, User ID if valid.
 */
function wp_validate_auth_cookie($cookie = '') {
	if ( empty($cookie) ) {
		if ( empty($_COOKIE[AUTH_COOKIE]) )
			return false;
		$cookie = $_COOKIE[AUTH_COOKIE];
	}

	$cookie_elements = explode('|', $cookie);
	if ( count($cookie_elements) != 3 )
		return false;

	list($username, $expiration, $hmac) = $cookie_elements;

	$expired = $expiration;

	// Allow a grace period for POST and AJAX requests
	if ( defined('DOING_AJAX') || 'POST' == $_SERVER['REQUEST_METHOD'] )
		$expired += 3600;

	// Quick check to see if an honest cookie has expired
	if ( $expired < time() )
		return false;

	$key = wp_hash($username . '|' . $expiration);
	$hash = hash_hmac('md5', $username . '|' . $expiration, $key);

	if ( $hmac != $hash )
		return false;

	$user = get_userdatabylogin($username);
	if ( ! $user )
		return false;

	return $user->ID;
}
endif;

if ( !function_exists('wp_generate_auth_cookie') ) :
/**
 * wp_generate_auth_cookie() - Generate authentication cookie contents
 *
 * @since 2.5
 * @uses apply_filters() Calls 'auth_cookie' hook on $cookie contents, User ID
 *		and expiration of cookie.
 *
 * @param int $user_id User ID
 * @param int $expiration Cookie expiration in seconds
 * @return string Authentication cookie contents
 */
function wp_generate_auth_cookie($user_id, $expiration) {
	$user = get_userdata($user_id);

	$key = wp_hash($user->user_login . '|' . $expiration);
	$hash = hash_hmac('md5', $user->user_login . '|' . $expiration, $key);

	$cookie = $user->user_login . '|' . $expiration . '|' . $hash;

	return apply_filters('auth_cookie', $cookie, $user_id, $expiration);
}
endif;

if ( !function_exists('wp_set_auth_cookie') ) :
/**
 * wp_set_auth_cookie() - Sets the authentication cookies based User ID
 *
 * The $remember parameter increases the time that the cookie will
 * be kept. The default the cookie is kept without remembering is
 * two days. When $remember is set, the cookies will be kept for
 * 14 days or two weeks.
 *
 * @since 2.5
 *
 * @param int $user_id User ID
 * @param bool $remember Whether to remember the user or not
 */
function wp_set_auth_cookie($user_id, $remember = false) {
	if ( $remember ) {
		$expiration = $expire = time() + 1209600;
	} else {
		$expiration = time() + 172800;
		$expire = 0;
	}

	$cookie = wp_generate_auth_cookie($user_id, $expiration);

	do_action('set_auth_cookie', $cookie, $expire);

	setcookie(AUTH_COOKIE, $cookie, $expire, COOKIEPATH, COOKIE_DOMAIN);
	if ( COOKIEPATH != SITECOOKIEPATH )
		setcookie(AUTH_COOKIE, $cookie, $expire, SITECOOKIEPATH, COOKIE_DOMAIN);
}
endif;

if ( !function_exists('wp_clear_auth_cookie') ) :
/**
 * wp_clear_auth_cookie() - Deletes all of the cookies associated with authentication
 *
 * @since 2.5
 */
function wp_clear_auth_cookie() {
	setcookie(AUTH_COOKIE, ' ', time() - 31536000, COOKIEPATH, COOKIE_DOMAIN);
	setcookie(AUTH_COOKIE, ' ', time() - 31536000, SITECOOKIEPATH, COOKIE_DOMAIN);

	// Old cookies
	setcookie(USER_COOKIE, ' ', time() - 31536000, COOKIEPATH, COOKIE_DOMAIN);
	setcookie(PASS_COOKIE, ' ', time() - 31536000, COOKIEPATH, COOKIE_DOMAIN);
	setcookie(USER_COOKIE, ' ', time() - 31536000, SITECOOKIEPATH, COOKIE_DOMAIN);
	setcookie(PASS_COOKIE, ' ', time() - 31536000, SITECOOKIEPATH, COOKIE_DOMAIN);
}
endif;

if ( !function_exists('is_user_logged_in') ) :
/**
 * is_user_logged_in() - Checks if the current visitor is a logged in user
 *
 * @since 2.0.0
 *
 * @return bool True if user is logged in, false if not logged in.
 */
function is_user_logged_in() {
	$user = wp_get_current_user();

	if ( $user->id == 0 )
		return false;

	return true;
}
endif;

if ( !function_exists('auth_redirect') ) :
/**
 * auth_redirect() - Checks if a user is logged in, if not it redirects them to the login page
 *
 * @since 1.5
 */
function auth_redirect() {
	// Checks if a user is logged in, if not redirects them to the login page
	if ( (!empty($_COOKIE[AUTH_COOKIE]) &&
				!wp_validate_auth_cookie($_COOKIE[AUTH_COOKIE])) ||
			(empty($_COOKIE[AUTH_COOKIE])) ) {
		nocache_headers();

		wp_redirect(get_option('siteurl') . '/wp-login.php?redirect_to=' . urlencode($_SERVER['REQUEST_URI']));
		exit();
	}
}
endif;

if ( !function_exists('check_admin_referer') ) :
/**
 * check_admin_referer() - Makes sure that a user was referred from another admin page, to avoid security exploits
 *
 * @since 1.2.0
 * @uses do_action() Calls 'check_admin_referer' on $action.
 *
 * @param string $action Action nonce
 * @param string $query_arg where to look for nonce in $_REQUEST (since 2.5)
 */
function check_admin_referer($action = -1, $query_arg = '_wpnonce') {
	$adminurl = strtolower(get_option('siteurl')).'/wp-admin';
	$referer = strtolower(wp_get_referer());
	$result = wp_verify_nonce($_REQUEST[$query_arg], $action);
	if ( !$result && !(-1 == $action && strpos($referer, $adminurl) !== false) ) {
		wp_nonce_ays($action);
		die();
	}
	do_action('check_admin_referer', $action, $result);
	return $result;
}endif;

if ( !function_exists('check_ajax_referer') ) :
/**
 * check_ajax_referer() - Verifies the AJAX request to prevent processing requests external of the blog.
 *
 * @since 2.0.4
 *
 * @param string $action Action nonce
 * @param string $query_arg where to look for nonce in $_REQUEST (since 2.5)
 */
function check_ajax_referer( $action = -1, $query_arg = false, $die = true ) {
	if ( $query_arg )
		$nonce = $_REQUEST[$query_arg];
	else
		$nonce = $_REQUEST['_ajax_nonce'] ? $_REQUEST['_ajax_nonce'] : $_REQUEST['_wpnonce'];

	$result = wp_verify_nonce( $nonce, $action );

	if ( $die && false == $result )
		die('-1');

	do_action('check_ajax_referer', $action, $result);

	return $result;
}
endif;

if ( !function_exists('wp_redirect') ) :
/**
 * wp_redirect() - Redirects to another page, with a workaround for the IIS Set-Cookie bug
 *
 * @link http://support.microsoft.com/kb/q176113/
 * @since 1.5.1
 * @uses apply_filters() Calls 'wp_redirect' hook on $location and $status.
 *
 * @param string $location The path to redirect to
 * @param int $status Status code to use
 * @return bool False if $location is not set
 */
function wp_redirect($location, $status = 302) {
	global $is_IIS;

	$location = apply_filters('wp_redirect', $location, $status);
	$status = apply_filters('wp_redirect_status', $status, $location);
	
	if ( !$location ) // allows the wp_redirect filter to cancel a redirect
		return false;

	$location = wp_sanitize_redirect($location);

	if ( $is_IIS ) {
		header("Refresh: 0;url=$location");
	} else {
		if ( php_sapi_name() != 'cgi-fcgi' )
			status_header($status); // This causes problems on IIS and some FastCGI setups
		header("Location: $location");
	}
}
endif;

if ( !function_exists('wp_sanitize_redirect') ) :
/**
 * wp_sanitize_redirect() - Sanitizes a URL for use in a redirect
 *
 * @since 2.3
 *
 * @return string redirect-sanitized URL
 **/
function wp_sanitize_redirect($location) {
	$location = preg_replace('|[^a-z0-9-~+_.?#=&;,/:%]|i', '', $location);
	$location = wp_kses_no_null($location);

	// remove %0d and %0a from location
	$strip = array('%0d', '%0a');
	$found = true;
	while($found) {
		$found = false;
		foreach($strip as $val) {
			while(strpos($location, $val) !== false) {
				$found = true;
				$location = str_replace($val, '', $location);
			}
		}
	}
	return $location;
}
endif;

if ( !function_exists('wp_safe_redirect') ) :
/**
 * wp_safe_redirect() - Performs a safe (local) redirect, using wp_redirect()
 *
 * Checks whether the $location is using an allowed host, if it has an absolute
 * path. A plugin can therefore set or remove allowed host(s) to or from the list.
 *
 * If the host is not allowed, then the redirect is to wp-admin on the siteurl
 * instead. This prevents malicious redirects which redirect to another host, but
 * only used in a few places.
 *
 * @since 2.3
 * @uses apply_filters() Calls 'allowed_redirect_hosts' on an array containing
 *		WordPress host string and $location host string.
 *
 * @return void Does not return anything
 **/
function wp_safe_redirect($location, $status = 302) {

	// Need to look at the URL the way it will end up in wp_redirect()
	$location = wp_sanitize_redirect($location);

	// browsers will assume 'http' is your protocol, and will obey a redirect to a URL starting with '//'
	if ( substr($location, 0, 2) == '//' )
		$location = 'http:' . $location;

	$lp  = parse_url($location);
	$wpp = parse_url(get_option('home'));

	$allowed_hosts = (array) apply_filters('allowed_redirect_hosts', array($wpp['host']), isset($lp['host']) ? $lp['host'] : '');

	if ( isset($lp['host']) && ( !in_array($lp['host'], $allowed_hosts) && $lp['host'] != strtolower($wpp['host'])) )
		$location = get_option('siteurl') . '/wp-admin/';

	wp_redirect($location, $status);
}
endif;

if ( ! function_exists('wp_notify_postauthor') ) :
/**
 * wp_notify_postauthor() - Notify an author of a comment/trackback/pingback to one of their posts
 *
 * @since 1.0.0
 *
 * @param int $comment_id Comment ID
 * @param string $comment_type Optional. The comment type either 'comment' (default), 'trackback', or 'pingback'
 * @return bool False if user email does not exist. True on completion.
 */
function wp_notify_postauthor($comment_id, $comment_type='') {
	$comment = get_comment($comment_id);
	$post    = get_post($comment->comment_post_ID);
	$user    = get_userdata( $post->post_author );

	if ('' == $user->user_email) return false; // If there's no email to send the comment to

	$comment_author_domain = @gethostbyaddr($comment->comment_author_IP);

	$blogname = get_option('blogname');

	if ( empty( $comment_type ) ) $comment_type = 'comment';

	if ('comment' == $comment_type) {
		$notify_message  = sprintf( __('New comment on your post #%1$s "%2$s"'), $comment->comment_post_ID, $post->post_title ) . "\r\n";
		$notify_message .= sprintf( __('Author : %1$s (IP: %2$s , %3$s)'), $comment->comment_author, $comment->comment_author_IP, $comment_author_domain ) . "\r\n";
		$notify_message .= sprintf( __('E-mail : %s'), $comment->comment_author_email ) . "\r\n";
		$notify_message .= sprintf( __('URL    : %s'), $comment->comment_author_url ) . "\r\n";
		$notify_message .= sprintf( __('Whois  : http://ws.arin.net/cgi-bin/whois.pl?queryinput=%s'), $comment->comment_author_IP ) . "\r\n";
		$notify_message .= __('Comment: ') . "\r\n" . $comment->comment_content . "\r\n\r\n";
		$notify_message .= __('You can see all comments on this post here: ') . "\r\n";
		$subject = sprintf( __('[%1$s] Comment: "%2$s"'), $blogname, $post->post_title );
	} elseif ('trackback' == $comment_type) {
		$notify_message  = sprintf( __('New trackback on your post #%1$s "%2$s"'), $comment->comment_post_ID, $post->post_title ) . "\r\n";
		$notify_message .= sprintf( __('Website: %1$s (IP: %2$s , %3$s)'), $comment->comment_author, $comment->comment_author_IP, $comment_author_domain ) . "\r\n";
		$notify_message .= sprintf( __('URL    : %s'), $comment->comment_author_url ) . "\r\n";
		$notify_message .= __('Excerpt: ') . "\r\n" . $comment->comment_content . "\r\n\r\n";
		$notify_message .= __('You can see all trackbacks on this post here: ') . "\r\n";
		$subject = sprintf( __('[%1$s] Trackback: "%2$s"'), $blogname, $post->post_title );
	} elseif ('pingback' == $comment_type) {
		$notify_message  = sprintf( __('New pingback on your post #%1$s "%2$s"'), $comment->comment_post_ID, $post->post_title ) . "\r\n";
		$notify_message .= sprintf( __('Website: %1$s (IP: %2$s , %3$s)'), $comment->comment_author, $comment->comment_author_IP, $comment_author_domain ) . "\r\n";
		$notify_message .= sprintf( __('URL    : %s'), $comment->comment_author_url ) . "\r\n";
		$notify_message .= __('Excerpt: ') . "\r\n" . sprintf('[...] %s [...]', $comment->comment_content ) . "\r\n\r\n";
		$notify_message .= __('You can see all pingbacks on this post here: ') . "\r\n";
		$subject = sprintf( __('[%1$s] Pingback: "%2$s"'), $blogname, $post->post_title );
	}
	$notify_message .= get_permalink($comment->comment_post_ID) . "#comments\r\n\r\n";
	$notify_message .= sprintf( __('Delete it: %s'), get_option('siteurl')."/wp-admin/comment.php?action=cdc&c=$comment_id" ) . "\r\n";
	$notify_message .= sprintf( __('Spam it: %s'), get_option('siteurl')."/wp-admin/comment.php?action=cdc&dt=spam&c=$comment_id" ) . "\r\n";

	$wp_email = 'wordpress@' . preg_replace('#^www\.#', '', strtolower($_SERVER['SERVER_NAME']));

	if ( '' == $comment->comment_author ) {
		$from = "From: \"$blogname\" <$wp_email>";
		if ( '' != $comment->comment_author_email )
			$reply_to = "Reply-To: $comment->comment_author_email";
	} else {
		$from = "From: \"$comment->comment_author\" <$wp_email>";
		if ( '' != $comment->comment_author_email )
			$reply_to = "Reply-To: \"$comment->comment_author_email\" <$comment->comment_author_email>";
	}

	$message_headers = "$from\n"
		. "Content-Type: text/plain; charset=\"" . get_option('blog_charset') . "\"\n";

	if ( isset($reply_to) )
		$message_headers .= $reply_to . "\n";

	$notify_message = apply_filters('comment_notification_text', $notify_message, $comment_id);
	$subject = apply_filters('comment_notification_subject', $subject, $comment_id);
	$message_headers = apply_filters('comment_notification_headers', $message_headers, $comment_id);

	@wp_mail($user->user_email, $subject, $notify_message, $message_headers);

	return true;
}
endif;

if ( !function_exists('wp_notify_moderator') ) :
/**
 * wp_notify_moderator() - Notifies the moderator of the blog about a new comment that is awaiting approval
 *
 * @since 1.0
 * @uses $wpdb
 *
 * @param int $comment_id Comment ID
 * @return bool Always returns true
 */
function wp_notify_moderator($comment_id) {
	global $wpdb;

	if( get_option( "moderation_notify" ) == 0 )
		return true;

	$comment = $wpdb->get_row($wpdb->prepare("SELECT * FROM $wpdb->comments WHERE comment_ID=%d LIMIT 1", $comment_id));
	$post = $wpdb->get_row($wpdb->prepare("SELECT * FROM $wpdb->posts WHERE ID=%d LIMIT 1", $comment->comment_post_ID));

	$comment_author_domain = @gethostbyaddr($comment->comment_author_IP);
	$comments_waiting = $wpdb->get_var("SELECT count(comment_ID) FROM $wpdb->comments WHERE comment_approved = '0'");

	switch ($comment->comment_type)
	{
		case 'trackback':
			$notify_message  = sprintf( __('A new trackback on the post #%1$s "%2$s" is waiting for your approval'), $post->ID, $post->post_title ) . "\r\n";
			$notify_message .= get_permalink($comment->comment_post_ID) . "\r\n\r\n";
			$notify_message .= sprintf( __('Website : %1$s (IP: %2$s , %3$s)'), $comment->comment_author, $comment->comment_author_IP, $comment_author_domain ) . "\r\n";
			$notify_message .= sprintf( __('URL    : %s'), $comment->comment_author_url ) . "\r\n";
			$notify_message .= __('Trackback excerpt: ') . "\r\n" . $comment->comment_content . "\r\n\r\n";
			break;
		case 'pingback':
			$notify_message  = sprintf( __('A new pingback on the post #%1$s "%2$s" is waiting for your approval'), $post->ID, $post->post_title ) . "\r\n";
			$notify_message .= get_permalink($comment->comment_post_ID) . "\r\n\r\n";
			$notify_message .= sprintf( __('Website : %1$s (IP: %2$s , %3$s)'), $comment->comment_author, $comment->comment_author_IP, $comment_author_domain ) . "\r\n";
			$notify_message .= sprintf( __('URL    : %s'), $comment->comment_author_url ) . "\r\n";
			$notify_message .= __('Pingback excerpt: ') . "\r\n" . $comment->comment_content . "\r\n\r\n";
			break;
		default: //Comments
			$notify_message  = sprintf( __('A new comment on the post #%1$s "%2$s" is waiting for your approval'), $post->ID, $post->post_title ) . "\r\n";
			$notify_message .= get_permalink($comment->comment_post_ID) . "\r\n\r\n";
			$notify_message .= sprintf( __('Author : %1$s (IP: %2$s , %3$s)'), $comment->comment_author, $comment->comment_author_IP, $comment_author_domain ) . "\r\n";
			$notify_message .= sprintf( __('E-mail : %s'), $comment->comment_author_email ) . "\r\n";
			$notify_message .= sprintf( __('URL    : %s'), $comment->comment_author_url ) . "\r\n";
			$notify_message .= sprintf( __('Whois  : http://ws.arin.net/cgi-bin/whois.pl?queryinput=%s'), $comment->comment_author_IP ) . "\r\n";
			$notify_message .= __('Comment: ') . "\r\n" . $comment->comment_content . "\r\n\r\n";
			break;
	}

	$notify_message .= sprintf( __('Approve it: %s'),  get_option('siteurl')."/wp-admin/comment.php?action=mac&c=$comment_id" ) . "\r\n";
	$notify_message .= sprintf( __('Delete it: %s'), get_option('siteurl')."/wp-admin/comment.php?action=cdc&c=$comment_id" ) . "\r\n";
	$notify_message .= sprintf( __('Spam it: %s'), get_option('siteurl')."/wp-admin/comment.php?action=cdc&dt=spam&c=$comment_id" ) . "\r\n";

	$strCommentsPending = sprintf( __ngettext('%s comment', '%s comments', $comments_waiting), $comments_waiting );
	$notify_message .= sprintf( __('Currently %s are waiting for approval. Please visit the moderation panel:'), $strCommentsPending ) . "\r\n";
	$notify_message .= get_option('siteurl') . "/wp-admin/edit-comments.php?comment_status=moderated\r\n";

	$subject = sprintf( __('[%1$s] Please moderate: "%2$s"'), get_option('blogname'), $post->post_title );
	$admin_email = get_option('admin_email');

	$notify_message = apply_filters('comment_moderation_text', $notify_message, $comment_id);
	$subject = apply_filters('comment_moderation_subject', $subject, $comment_id);

	@wp_mail($admin_email, $subject, $notify_message);

	return true;
}
endif;

if ( !function_exists('wp_new_user_notification') ) :
/**
 * wp_new_user_notification() - Notify the blog admin of a new user, normally via email
 *
 * @since 2.0
 *
 * @param int $user_id User ID
 * @param string $plaintext_pass Optional. The user's plaintext password
 */
function wp_new_user_notification($user_id, $plaintext_pass = '') {
	$user = new WP_User($user_id);

	$user_login = stripslashes($user->user_login);
	$user_email = stripslashes($user->user_email);

	$message  = sprintf(__('New user registration on your blog %s:'), get_option('blogname')) . "\r\n\r\n";
	$message .= sprintf(__('Username: %s'), $user_login) . "\r\n\r\n";
	$message .= sprintf(__('E-mail: %s'), $user_email) . "\r\n";

	@wp_mail(get_option('admin_email'), sprintf(__('[%s] New User Registration'), get_option('blogname')), $message);

	if ( empty($plaintext_pass) )
		return;

	$message  = sprintf(__('Username: %s'), $user_login) . "\r\n";
	$message .= sprintf(__('Password: %s'), $plaintext_pass) . "\r\n";
	$message .= get_option('siteurl') . "/wp-login.php\r\n";

	wp_mail($user_email, sprintf(__('[%s] Your username and password'), get_option('blogname')), $message);

}
endif;

if ( !function_exists('wp_nonce_tick') ) :
/**
 * wp_nonce_tick() - Get the time-dependent variable for nonce creation
 *
 * A nonce has a lifespan of two ticks. Nonces in their second tick may be updated, e.g. by autosave.
 *
 * @since 2.5
 *
 * @return int
 */
function wp_nonce_tick() {
	$nonce_life = apply_filters('nonce_life', 86400);

	return ceil(time() / ( $nonce_life / 2 ));
}
endif;

if ( !function_exists('wp_verify_nonce') ) :
/**
 * wp_verify_nonce() - Verify that correct nonce was used with time limit
 *
 * The user is given an amount of time to use the token, so therefore, since
 * the UID and $action remain the same, the independent variable is the time.
 *
 * @since 2.0.4
 *
 * @param string $nonce Nonce that was used in the form to verify
 * @param string|int $action Should give context to what is taking place and be the same when nonce was created.
 * @return bool Whether the nonce check passed or failed.
 */
function wp_verify_nonce($nonce, $action = -1) {
	$user = wp_get_current_user();
	$uid = (int) $user->id;

	$i = wp_nonce_tick();

	// Nonce generated 0-12 hours ago
	if ( substr(wp_hash($i . $action . $uid), -12, 10) == $nonce )
		return 1;
	// Nonce generated 12-24 hours ago
	if ( substr(wp_hash(($i - 1) . $action . $uid), -12, 10) == $nonce )
		return 2;
	// Invalid nonce
	return false;
}
endif;

if ( !function_exists('wp_create_nonce') ) :
/**
 * wp_create_nonce() - Creates a random, one time use token
 *
 * @since 2.0.4
 *
 * @param string|int $action Scalar value to add context to the nonce.
 * @return string The one use form token
 */
function wp_create_nonce($action = -1) {
	$user = wp_get_current_user();
	$uid = (int) $user->id;

	$i = wp_nonce_tick();

	return substr(wp_hash($i . $action . $uid), -12, 10);
}
endif;

if ( !function_exists('wp_salt') ) :
/**
 * wp_salt() - Get salt to add to hashes to help prevent attacks
 *
 * You can set the salt by defining two areas. One is in the database and
 * the other is in your wp-config.php file. The database location is defined
 * in the option named 'secret', but most likely will not need to be changed.
 *
 * The second, located in wp-config.php, is a constant named 'SECRET_KEY', but
 * is not required. If the constant is not defined then the database constants
 * will be used, since they are most likely given to be unique. However, given
 * that the salt will be added to the password and can be seen, the constant
 * is recommended to be set manually.
 *
 * <code>
 * define('SECRET_KEY', 'mAry1HadA15|\/|b17w55w1t3asSn09w');
 * </code>
 *
 * Attention: Do not use above example!
 *
 * Salting passwords helps against tools which has stored hashed values
 * of common dictionary strings. The added values makes it harder to crack
 * if given salt string is not weak.
 *
 * Salting only helps if the string is not predictable and should be
 * made up of various characters. Think of the salt as a password for
 * securing your passwords, but common among all of your passwords.
 * Therefore the salt should be as long as possible as as difficult as
 * possible, because you will not have to remember it.
 *
 * @since 2.5
 *
 * @return string Salt value from either 'SECRET_KEY' or 'secret' option
 */
function wp_salt() {
	global $wp_default_secret_key;
	$secret_key = '';
	if ( defined('SECRET_KEY') && ('' != SECRET_KEY) && ( $wp_default_secret_key != SECRET_KEY) )
		$secret_key = SECRET_KEY;

	if ( defined('SECRET_SALT') ) {
		$salt = SECRET_SALT;
	} else {
		$salt = get_option('secret');
		if ( empty($salt) ) {
			$salt = wp_generate_password();
			update_option('secret', $salt);
		}
	}

	return apply_filters('salt', $secret_key . $salt);
}
endif;

if ( !function_exists('wp_hash') ) :
/**
 * wp_hash() - Get hash of given string
 *
 * @since 2.0.4
 * @uses wp_salt() Get WordPress salt
 *
 * @param string $data Plain text to hash
 * @return string Hash of $data
 */
function wp_hash($data) {
	$salt = wp_salt();

	return hash_hmac('md5', $data, $salt);
}
endif;

if ( !function_exists('wp_hash_password') ) :
/**
 * wp_hash_password() - Create a hash (encrypt) of a plain text password
 *
 * For integration with other applications, this function can be
 * overwritten to instead use the other package password checking
 * algorithm.
 *
 * @since 2.5
 * @global object $wp_hasher PHPass object
 * @uses PasswordHash::HashPassword
 *
 * @param string $password Plain text user password to hash
 * @return string The hash string of the password
 */
function wp_hash_password($password) {
	global $wp_hasher;

	if ( empty($wp_hasher) ) {
		require_once( ABSPATH . 'wp-includes/class-phpass.php');
		// By default, use the portable hash from phpass
		$wp_hasher = new PasswordHash(8, TRUE);
	}

	return $wp_hasher->HashPassword($password);
}
endif;

if ( !function_exists('wp_check_password') ) :
/**
 * wp_check_password() - Checks the plaintext password against the encrypted Password
 *
 * Maintains compatibility between old version and the new cookie
 * authentication protocol using PHPass library. The $hash parameter
 * is the encrypted password and the function compares the plain text
 * password when encypted similarly against the already encrypted
 * password to see if they match.
 *
 * For integration with other applications, this function can be
 * overwritten to instead use the other package password checking
 * algorithm.
 *
 * @since 2.5
 * @global object $wp_hasher PHPass object used for checking the password
 *	against the $hash + $password
 * @uses PasswordHash::CheckPassword
 *
 * @param string $password Plaintext user's password
 * @param string $hash Hash of the user's password to check against.
 * @return bool False, if the $password does not match the hashed password
 */
function wp_check_password($password, $hash, $user_id = '') {
	global $wp_hasher;

	// If the hash is still md5...
	if ( strlen($hash) <= 32 ) {
		$check = ( $hash == md5($password) );
		if ( $check && $user_id ) {
			// Rehash using new hash.
			wp_set_password($password, $user_id);
			$hash = wp_hash_password($password);
		}

		return apply_filters('check_password', $check, $password, $hash, $user_id);
	}

	// If the stored hash is longer than an MD5, presume the
	// new style phpass portable hash.
	if ( empty($wp_hasher) ) {
		require_once( ABSPATH . 'wp-includes/class-phpass.php');
		// By default, use the portable hash from phpass
		$wp_hasher = new PasswordHash(8, TRUE);
	}

	$check = $wp_hasher->CheckPassword($password, $hash);

	return apply_filters('check_password', $check, $password, $hash, $user_id);
}
endif;

if ( !function_exists('wp_generate_password') ) :
/**
 * wp_generate_password() - Generates a random password drawn from the defined set of characters
 *
 * @since 2.5
 *
 * @return string The random password
 **/
function wp_generate_password($length = 12) {
	$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()";
	$password = '';
	for ( $i = 0; $i < $length; $i++ )
		$password .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
	return $password;
}
endif;

if ( !function_exists('wp_set_password') ) :
/**
 * wp_set_password() - Updates the user's password with a new encrypted one
 *
 * For integration with other applications, this function can be
 * overwritten to instead use the other package password checking
 * algorithm.
 *
 * @since 2.5
 * @uses $wpdb WordPress database object for queries
 * @uses wp_hash_password() Used to encrypt the user's password before passing to the database
 *
 * @param string $password The plaintext new user password
 * @param int $user_id User ID
 */
function wp_set_password( $password, $user_id ) {
	global $wpdb;

	$hash = wp_hash_password($password);
	$query = $wpdb->prepare("UPDATE $wpdb->users SET user_pass = %s, user_activation_key = '' WHERE ID = %d", $hash, $user_id);
	$wpdb->query($query);
	wp_cache_delete($user_id, 'users');
}
endif;

if ( !function_exists( 'get_avatar' ) ) :
/**
 * get_avatar() - Get avatar for a user
 *
 * Retrieve the avatar for a user provided a user ID or email address
 *
 * @since 2.5
 * @param int|string|object $id_or_email A user ID,  email address, or comment object
 * @param int $size Size of the avatar image
 * @param string $default URL to a default image to use if no avatar is available
 * @return string <img> tag for the user's avatar
*/
function get_avatar( $id_or_email, $size = '96', $default = '' ) {
	if ( ! get_option('show_avatars') )
		return false;

	if ( !is_numeric($size) )
		$size = '96';

	$email = '';
	if ( is_numeric($id_or_email) ) {
		$id = (int) $id_or_email;
		$user = get_userdata($id);
		if ( $user )
			$email = $user->user_email;
	} elseif ( is_object($id_or_email) ) {
		if ( !empty($id_or_email->user_id) ) {
			$id = (int) $id_or_email->user_id;
			$user = get_userdata($id);
			if ( $user)
				$email = $user->user_email;
		} elseif ( !empty($id_or_email->comment_author_email) ) {
			$email = $id_or_email->comment_author_email;
		}
	} else {
		$email = $id_or_email;
	}

	if ( empty($default) )
		$default = "http://www.gravatar.com/avatar/ad516503a11cd5ca435acc9bb6523536?s=$size"; // ad516503a11cd5ca435acc9bb6523536 == md5('unknown@gravatar.com')

	if ( !empty($email) ) {
		$out = 'http://www.gravatar.com/avatar/';
		$out .= md5( strtolower( $email ) );
		$out .= '?s='.$size;
		$out .= '&amp;d=' . urlencode( $default );

		$rating = get_option('avatar_rating');
		if ( !empty( $rating ) )
			$out .= "&amp;r={$rating}";

		$avatar = "<img alt='' src='{$out}' class='avatar avatar-{$size}' height='{$size}' width='{$size}' />";
	} else {
		$avatar = "<img alt='' src='{$default}' class='avatar avatar-{$size} avatar-default' height='{$size}' width='{$size}' />";
	}

	return apply_filters('get_avatar', $avatar, $id_or_email, $size, $default);
}
endif;

if ( !function_exists('wp_setcookie') ) :
/**
 * wp_setcookie() - Sets a cookie for a user who just logged in
 *
 * @since 1.5
 * @deprecated Use wp_set_auth_cookie()
 * @see wp_set_auth_cookie()
 *
 * @param string  $username The user's username
 * @param string  $password Optional. The user's password
 * @param bool $already_md5 Optional. Whether the password has already been through MD5
 * @param string $home Optional. Will be used instead of COOKIEPATH if set
 * @param string $siteurl Optional. Will be used instead of SITECOOKIEPATH if set
 * @param bool $remember Optional. Remember that the user is logged in
 */
function wp_setcookie($username, $password = '', $already_md5 = false, $home = '', $siteurl = '', $remember = false) {
	_deprecated_function( __FUNCTION__, '2.5', 'wp_set_auth_cookie()' );
	$user = get_userdatabylogin($username);
	wp_set_auth_cookie($user->ID, $remember);
}
endif;

if ( !function_exists('wp_clearcookie') ) :
/**
 * wp_clearcookie() - Clears the authentication cookie, logging the user out
 *
 * @since 1.5
 * @deprecated Use wp_clear_auth_cookie()
 * @see wp_clear_auth_cookie()
 */
function wp_clearcookie() {
	_deprecated_function( __FUNCTION__, '2.5', 'wp_clear_auth_cookie()' );
	wp_clear_auth_cookie();
}
endif;

if ( !function_exists('wp_get_cookie_login') ):
/**
 * wp_get_cookie_login() - Gets the user cookie login
 *
 * This function is deprecated and should no longer be extended as it won't
 * be used anywhere in WordPress. Also, plugins shouldn't use it either.
 *
 * @since 2.0.4
 * @deprecated No alternative
 *
 * @return bool Always returns false
 */
function wp_get_cookie_login() {
	_deprecated_function( __FUNCTION__, '2.5', '' );
	return false;
}
endif;

if ( !function_exists('wp_login') ) :
/**
 * wp_login() - Checks a users login information and logs them in if it checks out
 *
 * Use the global $error to get the reason why the login failed.
 * If the username is blank, no error will be set, so assume
 * blank username on that case.
 *
 * Plugins extending this function should also provide the global
 * $error and set what the error is, so that those checking the
 * global for why there was a failure can utilize it later.
 *
 * @since 1.2.2
 * @deprecated Use wp_signon()
 * @global string $error Error when false is returned
 *
 * @param string $username User's username
 * @param string $password User's password
 * @param bool $deprecated Not used
 * @return bool False on login failure, true on successful check
 */
function wp_login($username, $password, $deprecated = '') {
	global $error;

	$user = wp_authenticate($username, $password);

	if ( ! is_wp_error($user) )
		return true;

	$error = $user->get_error_message();
	return false;
}
endif;

?>
