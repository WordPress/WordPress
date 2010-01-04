<?php
/**
 * These functions can be replaced via plugins. If plugins do not redefine these
 * functions, then these will be used instead.
 *
 * @package WordPress
 */

if ( !function_exists('set_current_user') ) :
/**
 * Changes the current user by ID or name.
 *
 * Set $id to null and specify a name if you do not know a user's ID.
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
 * Changes the current user by ID or name.
 *
 * Set $id to null and specify a name if you do not know a user's ID.
 *
 * Some WordPress functionality is based on the current user and not based on
 * the signed in user. Therefore, it opens the ability to edit and perform
 * actions on users who aren't signed in.
 *
 * @since 2.0.3
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
 * Retrieve the current user object.
 *
 * @since 2.0.3
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
 * Populate global variables with information about the currently logged in user.
 *
 * Will set the current user, if the current user is not set. The current user
 * will be set to the logged in person. If no user is logged in, then it will
 * set the current user to 0, which is invalid and won't have any permissions.
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
		 if ( is_admin() || empty($_COOKIE[LOGGED_IN_COOKIE]) || !$user = wp_validate_auth_cookie($_COOKIE[LOGGED_IN_COOKIE], 'logged_in') ) {
		 	wp_set_current_user(0);
		 	return false;
		 }
	}

	wp_set_current_user($user);
}
endif;

if ( !function_exists('get_userdata') ) :
/**
 * Retrieve user info by user ID.
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

if ( !function_exists('get_user_by') ) :
/**
 * Retrieve user info by a given field
 *
 * @since 2.8.0
 *
 * @param string $field The field to retrieve the user with.  id | slug | email | login
 * @param int|string $value A value for $field.  A user ID, slug, email address, or login name.
 * @return bool|object False on failure, User DB row object
 */
function get_user_by($field, $value) {
	global $wpdb;

	switch ($field) {
		case 'id':
			return get_userdata($value);
			break;
		case 'slug':
			$user_id = wp_cache_get($value, 'userslugs');
			$field = 'user_nicename';
			break;
		case 'email':
			$user_id = wp_cache_get($value, 'useremail');
			$field = 'user_email';
			break;
		case 'login':
			$value = sanitize_user( $value );
			$user_id = wp_cache_get($value, 'userlogins');
			$field = 'user_login';
			break;
		default:
			return false;
	}

	 if ( false !== $user_id )
		return get_userdata($user_id);

	if ( !$user = $wpdb->get_row( $wpdb->prepare("SELECT * FROM $wpdb->users WHERE $field = %s", $value) ) )
		return false;

	_fill_user($user);

	return $user;
}
endif;

if ( !function_exists('get_userdatabylogin') ) :
/**
 * Retrieve user info by login name.
 *
 * @since 0.71
 *
 * @param string $user_login User's username
 * @return bool|object False on failure, User DB row object
 */
function get_userdatabylogin($user_login) {
	return get_user_by('login', $user_login);
}
endif;

if ( !function_exists('get_user_by_email') ) :
/**
 * Retrieve user info by email.
 *
 * @since 2.5
 *
 * @param string $email User's email address
 * @return bool|object False on failure, User DB row object
 */
function get_user_by_email($email) {
	return get_user_by('email', $email);
}
endif;

if ( !function_exists( 'wp_mail' ) ) :
/**
 * Send mail, similar to PHP's mail
 *
 * A true return value does not automatically mean that the user received the
 * email successfully. It just only means that the method used was able to
 * process the request without any errors.
 *
 * Using the two 'wp_mail_from' and 'wp_mail_from_name' hooks allow from
 * creating a from address like 'Name <email@address.com>' when both are set. If
 * just 'wp_mail_from' is set, then just the email address will be used with no
 * name.
 *
 * The default content type is 'text/plain' which does not allow using HTML.
 * However, you can set the content type of the email by using the
 * 'wp_mail_content_type' filter.
 *
 * The default charset is based on the charset used on the blog. The charset can
 * be set using the 'wp_mail_charset' filter.
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
 * @param string|array $attachments Optional. Files to attach.
 * @return bool Whether the email contents were sent successfully.
 */
function wp_mail( $to, $subject, $message, $headers = '', $attachments = array() ) {
	// Compact the input, apply the filters, and extract them back out
	extract( apply_filters( 'wp_mail', compact( 'to', 'subject', 'message', 'headers', 'attachments' ) ) );

	if ( !is_array($attachments) )
		$attachments = explode( "\n", $attachments );

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
	} else {
		if ( !is_array( $headers ) ) {
			// Explode the headers out, so this function can take both
			// string headers and an array of headers.
			$tempheaders = (array) explode( "\n", $headers );
		} else {
			$tempheaders = $headers;
		}
		$headers = array();

		// If it's actually got contents
		if ( !empty( $tempheaders ) ) {
			// Iterate through the raw headers
			foreach ( (array) $tempheaders as $header ) {
				if ( strpos($header, ':') === false ) {
					if ( false !== stripos( $header, 'boundary=' ) ) {
						$parts = preg_split('/boundary=/i', trim( $header ) );
						$boundary = trim( str_replace( array( "'", '"' ), '', $parts[1] ) );
					}
					continue;
				}
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
						$from_email = trim( $content );
					}
				} elseif ( 'content-type' == strtolower($name) ) {
					if ( strpos( $content,';' ) !== false ) {
						list( $type, $charset ) = explode( ';', $content );
						$content_type = trim( $type );
						if ( false !== stripos( $charset, 'charset=' ) ) {
							$charset = trim( str_replace( array( 'charset=', '"' ), '', $charset ) );
						} elseif ( false !== stripos( $charset, 'boundary=' ) ) {
							$boundary = trim( str_replace( array( 'BOUNDARY=', 'boundary=', '"' ), '', $charset ) );
							$charset = '';
						}
					} else {
						$content_type = trim( $content );
					}
				} elseif ( 'cc' == strtolower($name) ) {
					$cc = explode(",", $content);
				} elseif ( 'bcc' == strtolower($name) ) {
					$bcc = explode(",", $content);
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

	/* If we don't have an email from the input headers default to wordpress@$sitename
	 * Some hosts will block outgoing mail from this address if it doesn't exist but
	 * there's no easy alternative. Defaulting to admin_email might appear to be another
	 * option but some hosts may refuse to relay mail from an unknown domain. See
	 * http://trac.wordpress.org/ticket/5007.
	 */

	if ( !isset( $from_email ) ) {
		// Get the site domain and get rid of www.
		$sitename = strtolower( $_SERVER['SERVER_NAME'] );
		if ( substr( $sitename, 0, 4 ) == 'www.' ) {
			$sitename = substr( $sitename, 4 );
		}

		$from_email = 'wordpress@' . $sitename;
	}

	// Plugin authors can override the potentially troublesome default
	$phpmailer->From = apply_filters( 'wp_mail_from', $from_email );
	$phpmailer->FromName = apply_filters( 'wp_mail_from_name', $from_name );

	// Set destination address
	$phpmailer->AddAddress( $to );

	// Set mail's subject and body
	$phpmailer->Subject = $subject;
	$phpmailer->Body = $message;

	// Add any CC and BCC recipients
	if ( !empty($cc) ) {
		foreach ( (array) $cc as $recipient ) {
			$phpmailer->AddCc( trim($recipient) );
		}
	}
	if ( !empty($bcc) ) {
		foreach ( (array) $bcc as $recipient) {
			$phpmailer->AddBcc( trim($recipient) );
		}
	}

	// Set to use PHP's mail()
	$phpmailer->IsMail();

	// Set Content-Type and charset
	// If we don't have a content-type from the input headers
	if ( !isset( $content_type ) ) {
		$content_type = 'text/plain';
	}

	$content_type = apply_filters( 'wp_mail_content_type', $content_type );

	$phpmailer->ContentType = $content_type;

	// Set whether it's plaintext or not, depending on $content_type
	if ( $content_type == 'text/html' ) {
		$phpmailer->IsHTML( true );
	}

	// If we don't have a charset from the input headers
	if ( !isset( $charset ) ) {
		$charset = get_bloginfo( 'charset' );
	}

	// Set the content-type and charset
	$phpmailer->CharSet = apply_filters( 'wp_mail_charset', $charset );

	// Set custom headers
	if ( !empty( $headers ) ) {
		foreach( (array) $headers as $name => $content ) {
			$phpmailer->AddCustomHeader( sprintf( '%1$s: %2$s', $name, $content ) );
		}
		if ( false !== stripos( $content_type, 'multipart' ) && ! empty($boundary) ) {
			$phpmailer->AddCustomHeader( sprintf( "Content-Type: %s;\n\t boundary=\"%s\"", $content_type, $boundary ) );
		}
	}

	if ( !empty( $attachments ) ) {
		foreach ( $attachments as $attachment ) {
			$phpmailer->AddAttachment($attachment);
		}
	}

	do_action_ref_array( 'phpmailer_init', array( &$phpmailer ) );

	// Send!
	$result = @$phpmailer->Send();

	return $result;
}
endif;

if ( !function_exists('wp_authenticate') ) :
/**
 * Checks a user's login information and logs them in if it checks out.
 *
 * @since 2.5.0
 *
 * @param string $username User's username
 * @param string $password User's password
 * @return WP_Error|WP_User WP_User object if login successful, otherwise WP_Error object.
 */
function wp_authenticate($username, $password) {
	$username = sanitize_user($username);
	$password = trim($password);

	$user = apply_filters('authenticate', null, $username, $password);

	if ( $user == null ) {
		// TODO what should the error message be? (Or would these even happen?)
		// Only needed if all authentication handlers fail to return anything.
		$user = new WP_Error('authentication_failed', __('<strong>ERROR</strong>: Invalid username or incorrect password.'));
	}

	$ignore_codes = array('empty_username', 'empty_password');

	if (is_wp_error($user) && !in_array($user->get_error_code(), $ignore_codes) ) {
		do_action('wp_login_failed', $username);
	}

	return $user;
}
endif;

if ( !function_exists('wp_logout') ) :
/**
 * Log the current user out.
 *
 * @since 2.5.0
 */
function wp_logout() {
	wp_clear_auth_cookie();
	do_action('wp_logout');
}
endif;

if ( !function_exists('wp_validate_auth_cookie') ) :
/**
 * Validates authentication cookie.
 *
 * The checks include making sure that the authentication cookie is set and
 * pulling in the contents (if $cookie is not used).
 *
 * Makes sure the cookie is not expired. Verifies the hash in cookie is what is
 * should be and compares the two.
 *
 * @since 2.5
 *
 * @param string $cookie Optional. If used, will validate contents instead of cookie's
 * @param string $scheme Optional. The cookie scheme to use: auth, secure_auth, or logged_in
 * @return bool|int False if invalid cookie, User ID if valid.
 */
function wp_validate_auth_cookie($cookie = '', $scheme = '') {
	if ( ! $cookie_elements = wp_parse_auth_cookie($cookie, $scheme) ) {
		do_action('auth_cookie_malformed', $cookie, $scheme);
		return false;
	}

	extract($cookie_elements, EXTR_OVERWRITE);

	$expired = $expiration;

	// Allow a grace period for POST and AJAX requests
	if ( defined('DOING_AJAX') || 'POST' == $_SERVER['REQUEST_METHOD'] )
		$expired += 3600;

	// Quick check to see if an honest cookie has expired
	if ( $expired < time() ) {
		do_action('auth_cookie_expired', $cookie_elements);
		return false;
	}

	$user = get_userdatabylogin($username);
	if ( ! $user ) {
		do_action('auth_cookie_bad_username', $cookie_elements);
		return false;
	}

	$pass_frag = substr($user->user_pass, 8, 4);

	$key = wp_hash($username . $pass_frag . '|' . $expiration, $scheme);
	$hash = hash_hmac('md5', $username . '|' . $expiration, $key);

	if ( $hmac != $hash ) {
		do_action('auth_cookie_bad_hash', $cookie_elements);
		return false;
	}

	if ( $expiration < time() ) // AJAX/POST grace period set above
		$GLOBALS['login_grace_period'] = 1;

	do_action('auth_cookie_valid', $cookie_elements, $user);

	return $user->ID;
}
endif;

if ( !function_exists('wp_generate_auth_cookie') ) :
/**
 * Generate authentication cookie contents.
 *
 * @since 2.5
 * @uses apply_filters() Calls 'auth_cookie' hook on $cookie contents, User ID
 *		and expiration of cookie.
 *
 * @param int $user_id User ID
 * @param int $expiration Cookie expiration in seconds
 * @param string $scheme Optional. The cookie scheme to use: auth, secure_auth, or logged_in
 * @return string Authentication cookie contents
 */
function wp_generate_auth_cookie($user_id, $expiration, $scheme = 'auth') {
	$user = get_userdata($user_id);

	$pass_frag = substr($user->user_pass, 8, 4);

	$key = wp_hash($user->user_login . $pass_frag . '|' . $expiration, $scheme);
	$hash = hash_hmac('md5', $user->user_login . '|' . $expiration, $key);

	$cookie = $user->user_login . '|' . $expiration . '|' . $hash;

	return apply_filters('auth_cookie', $cookie, $user_id, $expiration, $scheme);
}
endif;

if ( !function_exists('wp_parse_auth_cookie') ) :
/**
 * Parse a cookie into its components
 *
 * @since 2.7
 *
 * @param string $cookie
 * @param string $scheme Optional. The cookie scheme to use: auth, secure_auth, or logged_in
 * @return array Authentication cookie components
 */
function wp_parse_auth_cookie($cookie = '', $scheme = '') {
	if ( empty($cookie) ) {
		switch ($scheme){
			case 'auth':
				$cookie_name = AUTH_COOKIE;
				break;
			case 'secure_auth':
				$cookie_name = SECURE_AUTH_COOKIE;
				break;
			case "logged_in":
				$cookie_name = LOGGED_IN_COOKIE;
				break;
			default:
				if ( is_ssl() ) {
					$cookie_name = SECURE_AUTH_COOKIE;
					$scheme = 'secure_auth';
				} else {
					$cookie_name = AUTH_COOKIE;
					$scheme = 'auth';
				}
	    }

		if ( empty($_COOKIE[$cookie_name]) )
			return false;
		$cookie = $_COOKIE[$cookie_name];
	}

	$cookie_elements = explode('|', $cookie);
	if ( count($cookie_elements) != 3 )
		return false;

	list($username, $expiration, $hmac) = $cookie_elements;

	return compact('username', 'expiration', 'hmac', 'scheme');
}
endif;

if ( !function_exists('wp_set_auth_cookie') ) :
/**
 * Sets the authentication cookies based User ID.
 *
 * The $remember parameter increases the time that the cookie will be kept. The
 * default the cookie is kept without remembering is two days. When $remember is
 * set, the cookies will be kept for 14 days or two weeks.
 *
 * @since 2.5
 *
 * @param int $user_id User ID
 * @param bool $remember Whether to remember the user or not
 */
function wp_set_auth_cookie($user_id, $remember = false, $secure = '') {
	if ( $remember ) {
		$expiration = $expire = time() + apply_filters('auth_cookie_expiration', 1209600, $user_id, $remember);
	} else {
		$expiration = time() + apply_filters('auth_cookie_expiration', 172800, $user_id, $remember);
		$expire = 0;
	}

	if ( '' === $secure )
		$secure = is_ssl() ? true : false;

	if ( $secure ) {
		$auth_cookie_name = SECURE_AUTH_COOKIE;
		$scheme = 'secure_auth';
	} else {
		$auth_cookie_name = AUTH_COOKIE;
		$scheme = 'auth';
	}

	$auth_cookie = wp_generate_auth_cookie($user_id, $expiration, $scheme);
	$logged_in_cookie = wp_generate_auth_cookie($user_id, $expiration, 'logged_in');

	do_action('set_auth_cookie', $auth_cookie, $expire, $expiration, $user_id, $scheme);
	do_action('set_logged_in_cookie', $logged_in_cookie, $expire, $expiration, $user_id, 'logged_in');

	// Set httponly if the php version is >= 5.2.0
	if ( version_compare(phpversion(), '5.2.0', 'ge') ) {
		setcookie($auth_cookie_name, $auth_cookie, $expire, PLUGINS_COOKIE_PATH, COOKIE_DOMAIN, $secure, true);
		setcookie($auth_cookie_name, $auth_cookie, $expire, ADMIN_COOKIE_PATH, COOKIE_DOMAIN, $secure, true);
		setcookie(LOGGED_IN_COOKIE, $logged_in_cookie, $expire, COOKIEPATH, COOKIE_DOMAIN, false, true);
		if ( COOKIEPATH != SITECOOKIEPATH )
			setcookie(LOGGED_IN_COOKIE, $logged_in_cookie, $expire, SITECOOKIEPATH, COOKIE_DOMAIN, false, true);
	} else {
		$cookie_domain = COOKIE_DOMAIN;
		if ( !empty($cookie_domain) )
			$cookie_domain .= '; HttpOnly';
		setcookie($auth_cookie_name, $auth_cookie, $expire, PLUGINS_COOKIE_PATH, $cookie_domain, $secure);
		setcookie($auth_cookie_name, $auth_cookie, $expire, ADMIN_COOKIE_PATH, $cookie_domain, $secure);
		setcookie(LOGGED_IN_COOKIE, $logged_in_cookie, $expire, COOKIEPATH, $cookie_domain);
		if ( COOKIEPATH != SITECOOKIEPATH )
			setcookie(LOGGED_IN_COOKIE, $logged_in_cookie, $expire, SITECOOKIEPATH, $cookie_domain);
	}
}
endif;

if ( !function_exists('wp_clear_auth_cookie') ) :
/**
 * Removes all of the cookies associated with authentication.
 *
 * @since 2.5
 */
function wp_clear_auth_cookie() {
	do_action('clear_auth_cookie');

	setcookie(AUTH_COOKIE, ' ', time() - 31536000, ADMIN_COOKIE_PATH, COOKIE_DOMAIN);
	setcookie(SECURE_AUTH_COOKIE, ' ', time() - 31536000, ADMIN_COOKIE_PATH, COOKIE_DOMAIN);
	setcookie(AUTH_COOKIE, ' ', time() - 31536000, PLUGINS_COOKIE_PATH, COOKIE_DOMAIN);
	setcookie(SECURE_AUTH_COOKIE, ' ', time() - 31536000, PLUGINS_COOKIE_PATH, COOKIE_DOMAIN);
	setcookie(LOGGED_IN_COOKIE, ' ', time() - 31536000, COOKIEPATH, COOKIE_DOMAIN);
	setcookie(LOGGED_IN_COOKIE, ' ', time() - 31536000, SITECOOKIEPATH, COOKIE_DOMAIN);

	// Old cookies
	setcookie(AUTH_COOKIE, ' ', time() - 31536000, COOKIEPATH, COOKIE_DOMAIN);
	setcookie(AUTH_COOKIE, ' ', time() - 31536000, SITECOOKIEPATH, COOKIE_DOMAIN);
	setcookie(SECURE_AUTH_COOKIE, ' ', time() - 31536000, COOKIEPATH, COOKIE_DOMAIN);
	setcookie(SECURE_AUTH_COOKIE, ' ', time() - 31536000, SITECOOKIEPATH, COOKIE_DOMAIN);

	// Even older cookies
	setcookie(USER_COOKIE, ' ', time() - 31536000, COOKIEPATH, COOKIE_DOMAIN);
	setcookie(PASS_COOKIE, ' ', time() - 31536000, COOKIEPATH, COOKIE_DOMAIN);
	setcookie(USER_COOKIE, ' ', time() - 31536000, SITECOOKIEPATH, COOKIE_DOMAIN);
	setcookie(PASS_COOKIE, ' ', time() - 31536000, SITECOOKIEPATH, COOKIE_DOMAIN);
}
endif;

if ( !function_exists('is_user_logged_in') ) :
/**
 * Checks if the current visitor is a logged in user.
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
 * Checks if a user is logged in, if not it redirects them to the login page.
 *
 * @since 1.5
 */
function auth_redirect() {
	// Checks if a user is logged in, if not redirects them to the login page

	if ( is_ssl() || force_ssl_admin() )
		$secure = true;
	else
		$secure = false;

	// If https is required and request is http, redirect
	if ( $secure && !is_ssl() && false !== strpos($_SERVER['REQUEST_URI'], 'wp-admin') ) {
		if ( 0 === strpos($_SERVER['REQUEST_URI'], 'http') ) {
			wp_redirect(preg_replace('|^http://|', 'https://', $_SERVER['REQUEST_URI']));
			exit();
		} else {
			wp_redirect('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
			exit();
		}
	}

	if ( $user_id = wp_validate_auth_cookie( '', apply_filters( 'auth_redirect_scheme', '' ) ) ) {
		do_action('auth_redirect', $user_id);

		// If the user wants ssl but the session is not ssl, redirect.
		if ( !$secure && get_user_option('use_ssl', $user_id) && false !== strpos($_SERVER['REQUEST_URI'], 'wp-admin') ) {
			if ( 0 === strpos($_SERVER['REQUEST_URI'], 'http') ) {
				wp_redirect(preg_replace('|^http://|', 'https://', $_SERVER['REQUEST_URI']));
				exit();
			} else {
				wp_redirect('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
				exit();
			}
		}

		return;  // The cookie is good so we're done
	}

	// The cookie is no good so force login
	nocache_headers();

	if ( is_ssl() )
		$proto = 'https://';
	else
		$proto = 'http://';

	$redirect = ( strpos($_SERVER['REQUEST_URI'], '/options.php') && wp_get_referer() ) ? wp_get_referer() : $proto . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

	$login_url = wp_login_url($redirect);

	wp_redirect($login_url);
	exit();
}
endif;

if ( !function_exists('check_admin_referer') ) :
/**
 * Makes sure that a user was referred from another admin page.
 *
 * To avoid security exploits.
 *
 * @since 1.2.0
 * @uses do_action() Calls 'check_admin_referer' on $action.
 *
 * @param string $action Action nonce
 * @param string $query_arg where to look for nonce in $_REQUEST (since 2.5)
 */
function check_admin_referer($action = -1, $query_arg = '_wpnonce') {
	$adminurl = strtolower(admin_url());
	$referer = strtolower(wp_get_referer());
	$result = isset($_REQUEST[$query_arg]) ? wp_verify_nonce($_REQUEST[$query_arg], $action) : false;
	if ( !$result && !(-1 == $action && strpos($referer, $adminurl) !== false) ) {
		wp_nonce_ays($action);
		die();
	}
	do_action('check_admin_referer', $action, $result);
	return $result;
}endif;

if ( !function_exists('check_ajax_referer') ) :
/**
 * Verifies the AJAX request to prevent processing requests external of the blog.
 *
 * @since 2.0.3
 *
 * @param string $action Action nonce
 * @param string $query_arg where to look for nonce in $_REQUEST (since 2.5)
 */
function check_ajax_referer( $action = -1, $query_arg = false, $die = true ) {
	if ( $query_arg )
		$nonce = $_REQUEST[$query_arg];
	else
		$nonce = isset($_REQUEST['_ajax_nonce']) ? $_REQUEST['_ajax_nonce'] : $_REQUEST['_wpnonce'];

	$result = wp_verify_nonce( $nonce, $action );

	if ( $die && false == $result )
		die('-1');

	do_action('check_ajax_referer', $action, $result);

	return $result;
}
endif;

if ( !function_exists('wp_redirect') ) :
/**
 * Redirects to another page, with a workaround for the IIS Set-Cookie bug.
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
		header("Location: $location", true, $status);
	}
}
endif;

if ( !function_exists('wp_sanitize_redirect') ) :
/**
 * Sanitizes a URL for use in a redirect.
 *
 * @since 2.3
 *
 * @return string redirect-sanitized URL
 **/
function wp_sanitize_redirect($location) {
	$location = preg_replace('|[^a-z0-9-~+_.?#=&;,/:%!]|i', '', $location);
	$location = wp_kses_no_null($location);

	// remove %0d and %0a from location
	$strip = array('%0d', '%0a', '%0D', '%0A');
	$location = _deep_replace($strip, $location);
	return $location;
}
endif;

if ( !function_exists('wp_safe_redirect') ) :
/**
 * Performs a safe (local) redirect, using wp_redirect().
 *
 * Checks whether the $location is using an allowed host, if it has an absolute
 * path. A plugin can therefore set or remove allowed host(s) to or from the
 * list.
 *
 * If the host is not allowed, then the redirect is to wp-admin on the siteurl
 * instead. This prevents malicious redirects which redirect to another host,
 * but only used in a few places.
 *
 * @since 2.3
 * @uses wp_validate_redirect() To validate the redirect is to an allowed host.
 *
 * @return void Does not return anything
 **/
function wp_safe_redirect($location, $status = 302) {

	// Need to look at the URL the way it will end up in wp_redirect()
	$location = wp_sanitize_redirect($location);

	$location = wp_validate_redirect($location, admin_url());

	wp_redirect($location, $status);
}
endif;

if ( !function_exists('wp_validate_redirect') ) :
/**
 * Validates a URL for use in a redirect.
 *
 * Checks whether the $location is using an allowed host, if it has an absolute
 * path. A plugin can therefore set or remove allowed host(s) to or from the
 * list.
 *
 * If the host is not allowed, then the redirect is to $default supplied
 *
 * @since 2.8.1
 * @uses apply_filters() Calls 'allowed_redirect_hosts' on an array containing
 *		WordPress host string and $location host string.
 *
 * @param string $location The redirect to validate
 * @param string $default The value to return is $location is not allowed
 * @return string redirect-sanitized URL
 **/
function wp_validate_redirect($location, $default = '') {
	// browsers will assume 'http' is your protocol, and will obey a redirect to a URL starting with '//'
	if ( substr($location, 0, 2) == '//' )
		$location = 'http:' . $location;

	// In php 5 parse_url may fail if the URL query part contains http://, bug #38143
	$test = ( $cut = strpos($location, '?') ) ? substr( $location, 0, $cut ) : $location;

	$lp  = parse_url($test);
	$wpp = parse_url(home_url());

	$allowed_hosts = (array) apply_filters('allowed_redirect_hosts', array($wpp['host']), isset($lp['host']) ? $lp['host'] : '');

	if ( isset($lp['host']) && ( !in_array($lp['host'], $allowed_hosts) && $lp['host'] != strtolower($wpp['host'])) )
		$location = $default;

	return $location;
}
endif;

if ( ! function_exists('wp_notify_postauthor') ) :
/**
 * Notify an author of a comment/trackback/pingback to one of their posts.
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
	$current_user = wp_get_current_user();

	if ( $comment->user_id == $post->post_author ) return false; // The author moderated a comment on his own post

	if ('' == $user->user_email) return false; // If there's no email to send the comment to

	$comment_author_domain = @gethostbyaddr($comment->comment_author_IP);
	
	// The blogname option is escaped with esc_html on the way into the database in sanitize_option
	// we want to reverse this for the plain text arena of emails.
	$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

	if ( empty( $comment_type ) ) $comment_type = 'comment';

	if ('comment' == $comment_type) {
		/* translators: 1: post id, 2: post title */
		$notify_message  = sprintf( __('New comment on your post #%1$s "%2$s"'), $comment->comment_post_ID, $post->post_title ) . "\r\n";
		/* translators: 1: comment author, 2: author IP, 3: author domain */
		$notify_message .= sprintf( __('Author : %1$s (IP: %2$s , %3$s)'), $comment->comment_author, $comment->comment_author_IP, $comment_author_domain ) . "\r\n";
		$notify_message .= sprintf( __('E-mail : %s'), $comment->comment_author_email ) . "\r\n";
		$notify_message .= sprintf( __('URL    : %s'), $comment->comment_author_url ) . "\r\n";
		$notify_message .= sprintf( __('Whois  : http://ws.arin.net/cgi-bin/whois.pl?queryinput=%s'), $comment->comment_author_IP ) . "\r\n";
		$notify_message .= __('Comment: ') . "\r\n" . $comment->comment_content . "\r\n\r\n";
		$notify_message .= __('You can see all comments on this post here: ') . "\r\n";
		/* translators: 1: blog name, 2: post title */
		$subject = sprintf( __('[%1$s] Comment: "%2$s"'), $blogname, $post->post_title );
	} elseif ('trackback' == $comment_type) {
		/* translators: 1: post id, 2: post title */
		$notify_message  = sprintf( __('New trackback on your post #%1$s "%2$s"'), $comment->comment_post_ID, $post->post_title ) . "\r\n";
		/* translators: 1: website name, 2: author IP, 3: author domain */
		$notify_message .= sprintf( __('Website: %1$s (IP: %2$s , %3$s)'), $comment->comment_author, $comment->comment_author_IP, $comment_author_domain ) . "\r\n";
		$notify_message .= sprintf( __('URL    : %s'), $comment->comment_author_url ) . "\r\n";
		$notify_message .= __('Excerpt: ') . "\r\n" . $comment->comment_content . "\r\n\r\n";
		$notify_message .= __('You can see all trackbacks on this post here: ') . "\r\n";
		/* translators: 1: blog name, 2: post title */
		$subject = sprintf( __('[%1$s] Trackback: "%2$s"'), $blogname, $post->post_title );
	} elseif ('pingback' == $comment_type) {
		/* translators: 1: post id, 2: post title */
		$notify_message  = sprintf( __('New pingback on your post #%1$s "%2$s"'), $comment->comment_post_ID, $post->post_title ) . "\r\n";
		/* translators: 1: comment author, 2: author IP, 3: author domain */
		$notify_message .= sprintf( __('Website: %1$s (IP: %2$s , %3$s)'), $comment->comment_author, $comment->comment_author_IP, $comment_author_domain ) . "\r\n";
		$notify_message .= sprintf( __('URL    : %s'), $comment->comment_author_url ) . "\r\n";
		$notify_message .= __('Excerpt: ') . "\r\n" . sprintf('[...] %s [...]', $comment->comment_content ) . "\r\n\r\n";
		$notify_message .= __('You can see all pingbacks on this post here: ') . "\r\n";
		/* translators: 1: blog name, 2: post title */
		$subject = sprintf( __('[%1$s] Pingback: "%2$s"'), $blogname, $post->post_title );
	}
	$notify_message .= get_permalink($comment->comment_post_ID) . "#comments\r\n\r\n";
	if ( EMPTY_TRASH_DAYS )
		$notify_message .= sprintf( __('Trash it: %s'), admin_url("comment.php?action=trash&c=$comment_id") ) . "\r\n";
	else
		$notify_message .= sprintf( __('Delete it: %s'), admin_url("comment.php?action=delete&c=$comment_id") ) . "\r\n";
	$notify_message .= sprintf( __('Spam it: %s'), admin_url("comment.php?action=spam&c=$comment_id") ) . "\r\n";

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
 * Notifies the moderator of the blog about a new comment that is awaiting approval.
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
	
	// The blogname option is escaped with esc_html on the way into the database in sanitize_option
	// we want to reverse this for the plain text arena of emails.
	$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
	
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

	$notify_message .= sprintf( __('Approve it: %s'),  admin_url("comment.php?action=approve&c=$comment_id") ) . "\r\n";
	if ( EMPTY_TRASH_DAYS )
		$notify_message .= sprintf( __('Trash it: %s'), admin_url("comment.php?action=trash&c=$comment_id") ) . "\r\n";
	else
		$notify_message .= sprintf( __('Delete it: %s'), admin_url("comment.php?action=delete&c=$comment_id") ) . "\r\n";
	$notify_message .= sprintf( __('Spam it: %s'), admin_url("comment.php?action=spam&c=$comment_id") ) . "\r\n";

	$notify_message .= sprintf( _n('Currently %s comment is waiting for approval. Please visit the moderation panel:',
 		'Currently %s comments are waiting for approval. Please visit the moderation panel:', $comments_waiting), number_format_i18n($comments_waiting) ) . "\r\n";
	$notify_message .= admin_url("edit-comments.php?comment_status=moderated") . "\r\n";

	$subject = sprintf( __('[%1$s] Please moderate: "%2$s"'), $blogname, $post->post_title );
	$admin_email = get_option('admin_email');
	$message_headers = '';

	$notify_message = apply_filters('comment_moderation_text', $notify_message, $comment_id);
	$subject = apply_filters('comment_moderation_subject', $subject, $comment_id);
	$message_headers = apply_filters('comment_moderation_headers', $message_headers);

	@wp_mail($admin_email, $subject, $notify_message, $message_headers);

	return true;
}
endif;

if ( !function_exists('wp_password_change_notification') ) :
/**
 * Notify the blog admin of a user changing password, normally via email.
 *
 * @since 2.7
 *
 * @param object $user User Object
 */
function wp_password_change_notification(&$user) {
	// send a copy of password change notification to the admin
	// but check to see if it's the admin whose password we're changing, and skip this
	if ( $user->user_email != get_option('admin_email') ) {
		$message = sprintf(__('Password Lost and Changed for user: %s'), $user->user_login) . "\r\n";
		// The blogname option is escaped with esc_html on the way into the database in sanitize_option
		// we want to reverse this for the plain text arena of emails.
		$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
		wp_mail(get_option('admin_email'), sprintf(__('[%s] Password Lost/Changed'), $blogname), $message);
	}
}
endif;

if ( !function_exists('wp_new_user_notification') ) :
/**
 * Notify the blog admin of a new user, normally via email.
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
	
	// The blogname option is escaped with esc_html on the way into the database in sanitize_option
	// we want to reverse this for the plain text arena of emails.
	$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

	$message  = sprintf(__('New user registration on your blog %s:'), $blogname) . "\r\n\r\n";
	$message .= sprintf(__('Username: %s'), $user_login) . "\r\n\r\n";
	$message .= sprintf(__('E-mail: %s'), $user_email) . "\r\n";

	@wp_mail(get_option('admin_email'), sprintf(__('[%s] New User Registration'), $blogname), $message);

	if ( empty($plaintext_pass) )
		return;

	$message  = sprintf(__('Username: %s'), $user_login) . "\r\n";
	$message .= sprintf(__('Password: %s'), $plaintext_pass) . "\r\n";
	$message .= wp_login_url() . "\r\n";

	wp_mail($user_email, sprintf(__('[%s] Your username and password'), $blogname), $message);

}
endif;

if ( !function_exists('wp_nonce_tick') ) :
/**
 * Get the time-dependent variable for nonce creation.
 *
 * A nonce has a lifespan of two ticks. Nonces in their second tick may be
 * updated, e.g. by autosave.
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
 * Verify that correct nonce was used with time limit.
 *
 * The user is given an amount of time to use the token, so therefore, since the
 * UID and $action remain the same, the independent variable is the time.
 *
 * @since 2.0.3
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
	if ( substr(wp_hash($i . $action . $uid, 'nonce'), -12, 10) == $nonce )
		return 1;
	// Nonce generated 12-24 hours ago
	if ( substr(wp_hash(($i - 1) . $action . $uid, 'nonce'), -12, 10) == $nonce )
		return 2;
	// Invalid nonce
	return false;
}
endif;

if ( !function_exists('wp_create_nonce') ) :
/**
 * Creates a random, one time use token.
 *
 * @since 2.0.3
 *
 * @param string|int $action Scalar value to add context to the nonce.
 * @return string The one use form token
 */
function wp_create_nonce($action = -1) {
	$user = wp_get_current_user();
	$uid = (int) $user->id;

	$i = wp_nonce_tick();

	return substr(wp_hash($i . $action . $uid, 'nonce'), -12, 10);
}
endif;

if ( !function_exists('wp_salt') ) :
/**
 * Get salt to add to hashes to help prevent attacks.
 *
 * The secret key is located in two places: the database in case the secret key
 * isn't defined in the second place, which is in the wp-config.php file. If you
 * are going to set the secret key, then you must do so in the wp-config.php
 * file.
 *
 * The secret key in the database is randomly generated and will be appended to
 * the secret key that is in wp-config.php file in some instances. It is
 * important to have the secret key defined or changed in wp-config.php.
 *
 * If you have installed WordPress 2.5 or later, then you will have the
 * SECRET_KEY defined in the wp-config.php already. You will want to change the
 * value in it because hackers will know what it is. If you have upgraded to
 * WordPress 2.5 or later version from a version before WordPress 2.5, then you
 * should add the constant to your wp-config.php file.
 *
 * Below is an example of how the SECRET_KEY constant is defined with a value.
 * You must not copy the below example and paste into your wp-config.php. If you
 * need an example, then you can have a
 * {@link https://api.wordpress.org/secret-key/1.1/ secret key created} for you.
 *
 * <code>
 * define('SECRET_KEY', 'mAry1HadA15|\/|b17w55w1t3asSn09w');
 * </code>
 *
 * Salting passwords helps against tools which has stored hashed values of
 * common dictionary strings. The added values makes it harder to crack if given
 * salt string is not weak.
 *
 * @since 2.5
 * @link https://api.wordpress.org/secret-key/1.1/ Create a Secret Key for wp-config.php
 *
 * @return string Salt value from either 'SECRET_KEY' or 'secret' option
 */
function wp_salt($scheme = 'auth') {
	global $wp_default_secret_key;
	$secret_key = '';
	if ( defined('SECRET_KEY') && ('' != SECRET_KEY) && ( $wp_default_secret_key != SECRET_KEY) )
		$secret_key = SECRET_KEY;

	if ( 'auth' == $scheme ) {
		if ( defined('AUTH_KEY') && ('' != AUTH_KEY) && ( $wp_default_secret_key != AUTH_KEY) )
			$secret_key = AUTH_KEY;

		if ( defined('AUTH_SALT') ) {
			$salt = AUTH_SALT;
		} elseif ( defined('SECRET_SALT') ) {
			$salt = SECRET_SALT;
		} else {
			$salt = get_option('auth_salt');
			if ( empty($salt) ) {
				$salt = wp_generate_password(64);
				update_option('auth_salt', $salt);
			}
		}
	} elseif ( 'secure_auth' == $scheme ) {
		if ( defined('SECURE_AUTH_KEY') && ('' != SECURE_AUTH_KEY) && ( $wp_default_secret_key != SECURE_AUTH_KEY) )
			$secret_key = SECURE_AUTH_KEY;

		if ( defined('SECURE_AUTH_SALT') ) {
			$salt = SECURE_AUTH_SALT;
		} else {
			$salt = get_option('secure_auth_salt');
			if ( empty($salt) ) {
				$salt = wp_generate_password(64);
				update_option('secure_auth_salt', $salt);
			}
		}
	} elseif ( 'logged_in' == $scheme ) {
		if ( defined('LOGGED_IN_KEY') && ('' != LOGGED_IN_KEY) && ( $wp_default_secret_key != LOGGED_IN_KEY) )
			$secret_key = LOGGED_IN_KEY;

		if ( defined('LOGGED_IN_SALT') ) {
			$salt = LOGGED_IN_SALT;
		} else {
			$salt = get_option('logged_in_salt');
			if ( empty($salt) ) {
				$salt = wp_generate_password(64);
				update_option('logged_in_salt', $salt);
			}
		}
	} elseif ( 'nonce' == $scheme ) {
		if ( defined('NONCE_KEY') && ('' != NONCE_KEY) && ( $wp_default_secret_key != NONCE_KEY) )
			$secret_key = NONCE_KEY;

		if ( defined('NONCE_SALT') ) {
			$salt = NONCE_SALT;
		} else {
			$salt = get_option('nonce_salt');
			if ( empty($salt) ) {
				$salt = wp_generate_password(64);
				update_option('nonce_salt', $salt);
			}
		}
	} else {
		// ensure each auth scheme has its own unique salt
		$salt = hash_hmac('md5', $scheme, $secret_key);
	}

	return apply_filters('salt', $secret_key . $salt, $scheme);
}
endif;

if ( !function_exists('wp_hash') ) :
/**
 * Get hash of given string.
 *
 * @since 2.0.3
 * @uses wp_salt() Get WordPress salt
 *
 * @param string $data Plain text to hash
 * @return string Hash of $data
 */
function wp_hash($data, $scheme = 'auth') {
	$salt = wp_salt($scheme);

	return hash_hmac('md5', $data, $salt);
}
endif;

if ( !function_exists('wp_hash_password') ) :
/**
 * Create a hash (encrypt) of a plain text password.
 *
 * For integration with other applications, this function can be overwritten to
 * instead use the other package password checking algorithm.
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
 * Checks the plaintext password against the encrypted Password.
 *
 * Maintains compatibility between old version and the new cookie authentication
 * protocol using PHPass library. The $hash parameter is the encrypted password
 * and the function compares the plain text password when encypted similarly
 * against the already encrypted password to see if they match.
 *
 * For integration with other applications, this function can be overwritten to
 * instead use the other package password checking algorithm.
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
 * Generates a random password drawn from the defined set of characters.
 *
 * @since 2.5
 *
 * @param int $length The length of password to generate
 * @param bool $special_chars Whether to include standard special characters
 * @return string The random password
 **/
function wp_generate_password($length = 12, $special_chars = true) {
	$chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
	if ( $special_chars )
		$chars .= '!@#$%^&*()';

	$password = '';
	for ( $i = 0; $i < $length; $i++ )
		$password .= substr($chars, wp_rand(0, strlen($chars) - 1), 1);
	return $password;
}
endif;

if ( !function_exists('wp_rand') ) :
 /**
 * Generates a random number
 *
 * @since 2.6.2
 *
 * @param int $min Lower limit for the generated number (optional, default is 0)
 * @param int $max Upper limit for the generated number (optional, default is 4294967295)
 * @return int A random number between min and max
 */
function wp_rand( $min = 0, $max = 0 ) {
	global $rnd_value;

	$seed = get_transient('random_seed');

	// Reset $rnd_value after 14 uses
	// 32(md5) + 40(sha1) + 40(sha1) / 8 = 14 random numbers from $rnd_value
	if ( strlen($rnd_value) < 8 ) {
		$rnd_value = md5( uniqid(microtime() . mt_rand(), true ) . $seed );
		$rnd_value .= sha1($rnd_value);
		$rnd_value .= sha1($rnd_value . $seed);
		$seed = md5($seed . $rnd_value);
		set_transient('random_seed', $seed);
	}

	// Take the first 8 digits for our value
	$value = substr($rnd_value, 0, 8);

	// Strip the first eight, leaving the remainder for the next call to wp_rand().
	$rnd_value = substr($rnd_value, 8);

	$value = abs(hexdec($value));

	// Reduce the value to be within the min - max range
	// 4294967295 = 0xffffffff = max random number
	if ( $max != 0 )
		$value = $min + (($max - $min + 1) * ($value / (4294967295 + 1)));

	return abs(intval($value));
}
endif;

if ( !function_exists('wp_set_password') ) :
/**
 * Updates the user's password with a new encrypted one.
 *
 * For integration with other applications, this function can be overwritten to
 * instead use the other package password checking algorithm.
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
	$wpdb->update($wpdb->users, array('user_pass' => $hash, 'user_activation_key' => ''), array('ID' => $user_id) );

	wp_cache_delete($user_id, 'users');
}
endif;

if ( !function_exists( 'get_avatar' ) ) :
/**
 * Retrieve the avatar for a user who provided a user ID or email address.
 *
 * @since 2.5
 * @param int|string|object $id_or_email A user ID,  email address, or comment object
 * @param int $size Size of the avatar image
 * @param string $default URL to a default image to use if no avatar is available
 * @param string $alt Alternate text to use in image tag. Defaults to blank
 * @return string <img> tag for the user's avatar
*/
function get_avatar( $id_or_email, $size = '96', $default = '', $alt = false ) {
	if ( ! get_option('show_avatars') )
		return false;

	if ( false === $alt)
		$safe_alt = '';
	else
		$safe_alt = esc_attr( $alt );

	if ( !is_numeric($size) )
		$size = '96';

	$email = '';
	if ( is_numeric($id_or_email) ) {
		$id = (int) $id_or_email;
		$user = get_userdata($id);
		if ( $user )
			$email = $user->user_email;
	} elseif ( is_object($id_or_email) ) {
		if ( isset($id_or_email->comment_type) && '' != $id_or_email->comment_type && 'comment' != $id_or_email->comment_type )
			return false; // No avatar for pingbacks or trackbacks

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

	if ( empty($default) ) {
		$avatar_default = get_option('avatar_default');
		if ( empty($avatar_default) )
			$default = 'mystery';
		else
			$default = $avatar_default;
	}

 	if ( is_ssl() )
		$host = 'https://secure.gravatar.com';
	else
		$host = 'http://www.gravatar.com';

	if ( 'mystery' == $default )
		$default = "$host/avatar/ad516503a11cd5ca435acc9bb6523536?s={$size}"; // ad516503a11cd5ca435acc9bb6523536 == md5('unknown@gravatar.com')
	elseif ( 'blank' == $default )
		$default = includes_url('images/blank.gif');
	elseif ( !empty($email) && 'gravatar_default' == $default )
		$default = '';
	elseif ( 'gravatar_default' == $default )
		$default = "$host/avatar/s={$size}";
	elseif ( empty($email) )
		$default = "$host/avatar/?d=$default&amp;s={$size}";
	elseif ( strpos($default, 'http://') === 0 )
		$default = add_query_arg( 's', $size, $default );

	if ( !empty($email) ) {
		$out = "$host/avatar/";
		$out .= md5( strtolower( $email ) );
		$out .= '?s='.$size;
		$out .= '&amp;d=' . urlencode( $default );

		$rating = get_option('avatar_rating');
		if ( !empty( $rating ) )
			$out .= "&amp;r={$rating}";

		$avatar = "<img alt='{$safe_alt}' src='{$out}' class='avatar avatar-{$size} photo' height='{$size}' width='{$size}' />";
	} else {
		$avatar = "<img alt='{$safe_alt}' src='{$default}' class='avatar avatar-{$size} photo avatar-default' height='{$size}' width='{$size}' />";
	}

	return apply_filters('get_avatar', $avatar, $id_or_email, $size, $default, $alt);
}
endif;

if ( !function_exists('wp_setcookie') ) :
/**
 * Sets a cookie for a user who just logged in.
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
 * Clears the authentication cookie, logging the user out.
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
 * Gets the user cookie login.
 *
 * This function is deprecated and should no longer be extended as it won't be
 * used anywhere in WordPress. Also, plugins shouldn't use it either.
 *
 * @since 2.0.3
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
 * Checks a users login information and logs them in if it checks out.
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

if ( !function_exists( 'wp_text_diff' ) ) :
/**
 * Displays a human readable HTML representation of the difference between two strings.
 *
 * The Diff is available for getting the changes between versions. The output is
 * HTML, so the primary use is for displaying the changes. If the two strings
 * are equivalent, then an empty string will be returned.
 *
 * The arguments supported and can be changed are listed below.
 *
 * 'title' : Default is an empty string. Titles the diff in a manner compatible
 *		with the output.
 * 'title_left' : Default is an empty string. Change the HTML to the left of the
 *		title.
 * 'title_right' : Default is an empty string. Change the HTML to the right of
 *		the title.
 *
 * @since 2.6
 * @see wp_parse_args() Used to change defaults to user defined settings.
 * @uses Text_Diff
 * @uses WP_Text_Diff_Renderer_Table
 *
 * @param string $left_string "old" (left) version of string
 * @param string $right_string "new" (right) version of string
 * @param string|array $args Optional. Change 'title', 'title_left', and 'title_right' defaults.
 * @return string Empty string if strings are equivalent or HTML with differences.
 */
function wp_text_diff( $left_string, $right_string, $args = null ) {
	$defaults = array( 'title' => '', 'title_left' => '', 'title_right' => '' );
	$args = wp_parse_args( $args, $defaults );

	if ( !class_exists( 'WP_Text_Diff_Renderer_Table' ) )
		require( ABSPATH . WPINC . '/wp-diff.php' );

	$left_string  = normalize_whitespace($left_string);
	$right_string = normalize_whitespace($right_string);

	$left_lines  = split("\n", $left_string);
	$right_lines = split("\n", $right_string);

	$text_diff = new Text_Diff($left_lines, $right_lines);
	$renderer  = new WP_Text_Diff_Renderer_Table();
	$diff = $renderer->render($text_diff);

	if ( !$diff )
		return '';

	$r  = "<table class='diff'>\n";
	$r .= "<col class='ltype' /><col class='content' /><col class='ltype' /><col class='content' />";

	if ( $args['title'] || $args['title_left'] || $args['title_right'] )
		$r .= "<thead>";
	if ( $args['title'] )
		$r .= "<tr class='diff-title'><th colspan='4'>$args[title]</th></tr>\n";
	if ( $args['title_left'] || $args['title_right'] ) {
		$r .= "<tr class='diff-sub-title'>\n";
		$r .= "\t<td></td><th>$args[title_left]</th>\n";
		$r .= "\t<td></td><th>$args[title_right]</th>\n";
		$r .= "</tr>\n";
	}
	if ( $args['title'] || $args['title_left'] || $args['title_right'] )
		$r .= "</thead>\n";

	$r .= "<tbody>\n$diff\n</tbody>\n";
	$r .= "</table>";

	return $r;
}
endif;

