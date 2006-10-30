<?php

	/* These functions can be replaced via plugins.  They are loaded after
	 plugins are loaded. */

if ( !function_exists('set_current_user') ) :
function set_current_user($id, $name = '') {
	return wp_set_current_user($id, $name);
}
endif;

if ( !function_exists('wp_set_current_user') ) :
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
function wp_get_current_user() {
	global $current_user;

	get_currentuserinfo();

	return $current_user;
}
endif;

if ( !function_exists('get_currentuserinfo') ) :
function get_currentuserinfo() {
	global $current_user;

	if ( defined('XMLRPC_REQUEST') && XMLRPC_REQUEST )
		return false;

	if ( ! empty($current_user) )
		return;

	if ( empty($_COOKIE[USER_COOKIE]) || empty($_COOKIE[PASS_COOKIE]) || 
		!wp_login($_COOKIE[USER_COOKIE], $_COOKIE[PASS_COOKIE], true) ) {
		wp_set_current_user(0);
		return false;
	}

	$user_login = $_COOKIE[USER_COOKIE];
	wp_set_current_user(0, $user_login);
}
endif;

if ( !function_exists('get_userdata') ) :
function get_userdata( $user_id ) {
	global $wpdb;
	$user_id = (int) $user_id;
	if ( $user_id == 0 )
		return false;

	$user = wp_cache_get($user_id, 'users');

	if ( $user )
		return $user;

	if ( !$user = $wpdb->get_row("SELECT * FROM $wpdb->users WHERE ID = '$user_id' LIMIT 1") )
		return false;

	$wpdb->hide_errors();
	$metavalues = $wpdb->get_results("SELECT meta_key, meta_value FROM $wpdb->usermeta WHERE user_id = '$user_id'");
	$wpdb->show_errors();

	if ($metavalues) {
		foreach ( $metavalues as $meta ) {
			$value = maybe_unserialize($meta->meta_value);
			$user->{$meta->meta_key} = $value;

			// We need to set user_level from meta, not row
			if ( $wpdb->prefix . 'user_level' == $meta->meta_key )
				$user->user_level = $meta->meta_value;
		} // end foreach
	} //end if

	// For backwards compat.
	if ( isset($user->first_name) )
		$user->user_firstname = $user->first_name;
	if ( isset($user->last_name) )
		$user->user_lastname = $user->last_name;
	if ( isset($user->description) )
		$user->user_description = $user->description;

	wp_cache_add($user_id, $user, 'users');
	wp_cache_add($user->user_login, $user, 'userlogins');

	return $user;
}
endif;

if ( !function_exists('update_user_cache') ) :
function update_user_cache() {
	return true;
}
endif;

if ( !function_exists('get_userdatabylogin') ) :
function get_userdatabylogin($user_login) {
	global $wpdb;
	$user_login = sanitize_user( $user_login );

	if ( empty( $user_login ) )
		return false;

	$userdata = wp_cache_get($user_login, 'userlogins');
	if ( $userdata )
		return $userdata;

	if ( !$user = $wpdb->get_row("SELECT * FROM $wpdb->users WHERE user_login = '$user_login'") )
		return false;

	$wpdb->hide_errors();
	$metavalues = $wpdb->get_results("SELECT meta_key, meta_value FROM $wpdb->usermeta WHERE user_id = '$user->ID'");
	$wpdb->show_errors();

	if ($metavalues) {
		foreach ( $metavalues as $meta ) {
			$value = maybe_unserialize($meta->meta_value);
			$user->{$meta->meta_key} = $value;

			// We need to set user_level from meta, not row
			if ( $wpdb->prefix . 'user_level' == $meta->meta_key )
				$user->user_level = $meta->meta_value;
		}
	}

	// For backwards compat.
	if ( isset($user->first_name) )
		$user->user_firstname = $user->first_name;
	if ( isset($user->last_name) )
		$user->user_lastname = $user->last_name;
	if ( isset($user->description) )
		$user->user_description = $user->description;

	wp_cache_add($user->ID, $user, 'users');
	wp_cache_add($user->user_login, $user, 'userlogins');

	return $user;

}
endif;

if ( !function_exists('wp_mail') ) :
function wp_mail($to, $subject, $message, $headers = '') {
	if( $headers == '' ) {
		$headers = "MIME-Version: 1.0\n" .
			"From: wordpress@" . preg_replace('#^www\.#', '', strtolower($_SERVER['SERVER_NAME'])) . "\n" . 
			"Content-Type: text/plain; charset=\"" . get_option('blog_charset') . "\"\n";
	}

	return @mail($to, $subject, $message, $headers);
}
endif;

if ( !function_exists('wp_login') ) :
function wp_login($username, $password, $already_md5 = false) {
	global $wpdb, $error;

	if ( '' == $username )
		return false;

	if ( '' == $password ) {
		$error = __('<strong>ERROR</strong>: The password field is empty.');
		return false;
	}

	$login = get_userdatabylogin($username);
	//$login = $wpdb->get_row("SELECT ID, user_login, user_pass FROM $wpdb->users WHERE user_login = '$username'");

	if (!$login) {
		$error = __('<strong>ERROR</strong>: Invalid username.');
		return false;
	} else {
		// If the password is already_md5, it has been double hashed.
		// Otherwise, it is plain text.
		if ( ($already_md5 && md5($login->user_pass) == $password) || ($login->user_login == $username && $login->user_pass == md5($password)) ) {
			return true;
		} else {
			$error = __('<strong>ERROR</strong>: Incorrect password.');
			$pwd = '';
			return false;
		}
	}
}
endif;

if ( !function_exists('is_user_logged_in') ) :
function is_user_logged_in() {
	$user = wp_get_current_user();

	if ( $user->id == 0 )
		return false;

	return true;
}
endif;

if ( !function_exists('auth_redirect') ) :
function auth_redirect() {
	// Checks if a user is logged in, if not redirects them to the login page
	if ( (!empty($_COOKIE[USER_COOKIE]) && 
				!wp_login($_COOKIE[USER_COOKIE], $_COOKIE[PASS_COOKIE], true)) ||
			 (empty($_COOKIE[USER_COOKIE])) ) {
		nocache_headers();

		wp_redirect(get_option('siteurl') . '/wp-login.php?redirect_to=' . urlencode($_SERVER['REQUEST_URI']));
		exit();
	}
}
endif;

if ( !function_exists('check_admin_referer') ) :
function check_admin_referer($action = -1) {
	$adminurl = strtolower(get_option('siteurl')).'/wp-admin';
	$referer = strtolower(wp_get_referer());
	if ( !wp_verify_nonce($_REQUEST['_wpnonce'], $action) &&
		!(-1 == $action && strstr($referer, $adminurl)) ) {
		wp_nonce_ays($action);
		die();
	}
	do_action('check_admin_referer', $action);
}endif;

if ( !function_exists('check_ajax_referer') ) :
function check_ajax_referer() {
	$cookie = explode('; ', urldecode(empty($_POST['cookie']) ? $_GET['cookie'] : $_POST['cookie'])); // AJAX scripts must pass cookie=document.cookie
	foreach ( $cookie as $tasty ) {
		if ( false !== strpos($tasty, USER_COOKIE) )
			$user = substr(strstr($tasty, '='), 1);
		if ( false !== strpos($tasty, PASS_COOKIE) )
			$pass = substr(strstr($tasty, '='), 1);
	}
	if ( !wp_login( $user, $pass, true ) )
		die('-1');
	do_action('check_ajax_referer');
}
endif;

// Cookie safe redirect.  Works around IIS Set-Cookie bug.
// http://support.microsoft.com/kb/q176113/
if ( !function_exists('wp_redirect') ) :
function wp_redirect($location, $status = 302) {
	global $is_IIS;

	$location = apply_filters('wp_redirect', $location, $status);

	if ( !$location ) // allows the wp_redirect filter to cancel a redirect
		return false; 

	$location = preg_replace('|[^a-z0-9-~+_.?#=&;,/:%]|i', '', $location);
	$location = wp_kses_no_null($location);

	$strip = array('%0d', '%0a');
	$location = str_replace($strip, '', $location);

	if ( $is_IIS ) {
		header("Refresh: 0;url=$location");
	} else {
		status_header($status); // This causes problems on IIS
		header("Location: $location");
	}
}
endif;

if ( !function_exists('wp_get_cookie_login') ):
function wp_get_cookie_login() {
	if ( empty($_COOKIE[USER_COOKIE]) || empty($_COOKIE[PASS_COOKIE]) )
		return false;

	return array('login' => $_COOKIE[USER_COOKIE],	'password' => $_COOKIE[PASS_COOKIE]);
}

endif;

if ( !function_exists('wp_setcookie') ) :
function wp_setcookie($username, $password, $already_md5 = false, $home = '', $siteurl = '', $remember = false) {
	if ( !$already_md5 )
		$password = md5( md5($password) ); // Double hash the password in the cookie.

	if ( empty($home) )
		$cookiepath = COOKIEPATH;
	else
		$cookiepath = preg_replace('|https?://[^/]+|i', '', $home . '/' );

	if ( empty($siteurl) ) {
		$sitecookiepath = SITECOOKIEPATH;
		$cookiehash = COOKIEHASH;
	} else {
		$sitecookiepath = preg_replace('|https?://[^/]+|i', '', $siteurl . '/' );
		$cookiehash = md5($siteurl);
	}

	if ( $remember )
		$expire = time() + 31536000;
	else
		$expire = 0;

	setcookie(USER_COOKIE, $username, $expire, $cookiepath, COOKIE_DOMAIN);
	setcookie(PASS_COOKIE, $password, $expire, $cookiepath, COOKIE_DOMAIN);

	if ( $cookiepath != $sitecookiepath ) {
		setcookie(USER_COOKIE, $username, $expire, $sitecookiepath, COOKIE_DOMAIN);
		setcookie(PASS_COOKIE, $password, $expire, $sitecookiepath, COOKIE_DOMAIN);
	}
}
endif;

if ( !function_exists('wp_clearcookie') ) :
function wp_clearcookie() {
	setcookie(USER_COOKIE, ' ', time() - 31536000, COOKIEPATH, COOKIE_DOMAIN);
	setcookie(PASS_COOKIE, ' ', time() - 31536000, COOKIEPATH, COOKIE_DOMAIN);
	setcookie(USER_COOKIE, ' ', time() - 31536000, SITECOOKIEPATH, COOKIE_DOMAIN);
	setcookie(PASS_COOKIE, ' ', time() - 31536000, SITECOOKIEPATH, COOKIE_DOMAIN);
}
endif;

if ( ! function_exists('wp_notify_postauthor') ) :
function wp_notify_postauthor($comment_id, $comment_type='') {
	global $wpdb;
    
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
	$notify_message .= sprintf( __('To delete this comment, visit: %s'), get_option('siteurl')."/wp-admin/comment.php?action=cdc&c=$comment_id" ) . "\r\n";
	$notify_message .= sprintf( __('To mark this comment as spam, visit: %s'), get_option('siteurl')."/wp-admin/comment.php?action=cdc&dt=spam&c=$comment_id" ) . "\r\n";

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

	$message_headers = "MIME-Version: 1.0\n"
		. "$from\n"
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

/* wp_notify_moderator
   notifies the moderator of the blog (usually the admin)
   about a new comment that waits for approval
   always returns true
 */
if ( !function_exists('wp_notify_moderator') ) :
function wp_notify_moderator($comment_id) {
	global $wpdb;

	if( get_option( "moderation_notify" ) == 0 )
		return true; 
    
	$comment = $wpdb->get_row("SELECT * FROM $wpdb->comments WHERE comment_ID='$comment_id' LIMIT 1");
	$post = $wpdb->get_row("SELECT * FROM $wpdb->posts WHERE ID='$comment->comment_post_ID' LIMIT 1");

	$comment_author_domain = @gethostbyaddr($comment->comment_author_IP);
	$comments_waiting = $wpdb->get_var("SELECT count(comment_ID) FROM $wpdb->comments WHERE comment_approved = '0'");

	$notify_message  = sprintf( __('A new comment on the post #%1$s "%2$s" is waiting for your approval'), $post->ID, $post->post_title ) . "\r\n";
	$notify_message .= get_permalink($comment->comment_post_ID) . "\r\n\r\n";
	$notify_message .= sprintf( __('Author : %1$s (IP: %2$s , %3$s)'), $comment->comment_author, $comment->comment_author_IP, $comment_author_domain ) . "\r\n";
	$notify_message .= sprintf( __('E-mail : %s'), $comment->comment_author_email ) . "\r\n";
	$notify_message .= sprintf( __('URL    : %s'), $comment->comment_author_url ) . "\r\n";
	$notify_message .= sprintf( __('Whois  : http://ws.arin.net/cgi-bin/whois.pl?queryinput=%s'), $comment->comment_author_IP ) . "\r\n";
	$notify_message .= __('Comment: ') . "\r\n" . $comment->comment_content . "\r\n\r\n";
	$notify_message .= sprintf( __('To approve this comment, visit: %s'),  get_option('siteurl')."/wp-admin/comment.php?action=mac&c=$comment_id" ) . "\r\n";
	$notify_message .= sprintf( __('To delete this comment, visit: %s'), get_option('siteurl')."/wp-admin/comment.php?action=cdc&c=$comment_id" ) . "\r\n";
	$notify_message .= sprintf( __('To mark this comment as spam, visit: %s'), get_option('siteurl')."/wp-admin/comment.php?action=cdc&dt=spam&c=$comment_id" ) . "\r\n";
	$notify_message .= sprintf( __('Currently %s comments are waiting for approval. Please visit the moderation panel:'), $comments_waiting ) . "\r\n";
	$notify_message .= get_option('siteurl') . "/wp-admin/moderation.php\r\n";

	$subject = sprintf( __('[%1$s] Please moderate: "%2$s"'), get_option('blogname'), $post->post_title );
	$admin_email = get_option('admin_email');

	$notify_message = apply_filters('comment_moderation_text', $notify_message, $comment_id);
	$subject = apply_filters('comment_moderation_subject', $subject, $comment_id);

	@wp_mail($admin_email, $subject, $notify_message);
    
	return true;
}
endif;

if ( !function_exists('wp_new_user_notification') ) :
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

if ( !function_exists('wp_verify_nonce') ) :
function wp_verify_nonce($nonce, $action = -1) {
	$user = wp_get_current_user();
	$uid = $user->id;

	$i = ceil(time() / 43200);

	//Allow for expanding range, but only do one check if we can
	if( substr(wp_hash($i . $action . $uid), -12, 10) == $nonce || substr(wp_hash(($i - 1) . $action . $uid), -12, 10) == $nonce )
		return true;
	return false;
}
endif;

if ( !function_exists('wp_create_nonce') ) :
function wp_create_nonce($action = -1) {
	$user = wp_get_current_user();
	$uid = $user->id;

	$i = ceil(time() / 43200);
	
	return substr(wp_hash($i . $action . $uid), -12, 10);
}
endif;

if ( !function_exists('wp_salt') ) :
function wp_salt() {
	$salt = get_option('secret');
	if ( empty($salt) )
		$salt = DB_PASSWORD . DB_USER . DB_NAME . DB_HOST . ABSPATH;

	return $salt;
}
endif;

if ( !function_exists('wp_hash') ) :
function wp_hash($data) {
	$salt = wp_salt();

	if ( function_exists('hash_hmac') ) {
		return hash_hmac('md5', $data, $salt);
	} else {
		return md5($data . $salt);
	}
}
endif;

?>
