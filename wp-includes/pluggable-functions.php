<?php

	/* These functions can be replaced via plugins.  They are loaded after
	 plugins are loaded. */


if ( !function_exists('get_currentuserinfo') ) :
function get_currentuserinfo() {
	global $user_login, $userdata, $user_level, $user_ID, $user_nickname, $user_email, $user_url, $user_pass_md5, $user_identity;
	// *** retrieving user's data from cookies and db - no spoofing

	if (isset($_COOKIE['wordpressuser_' . COOKIEHASH])) 
		$user_login = $_COOKIE['wordpressuser_' . COOKIEHASH];
	$userdata = get_userdatabylogin($user_login);
	$user_level = $userdata->user_level;
	$user_ID = $userdata->ID;
	$user_nickname = $userdata->user_nickname;
	$user_email = $userdata->user_email;
	$user_url = $userdata->user_url;
	$user_pass_md5 = md5($userdata->user_pass);

	$idmode = $userdata->user_idmode;
	if ($idmode == 'nickname')  $user_identity = $userdata->user_nickname;
	if ($idmode == 'login')     $user_identity = $userdata->user_login;
	if ($idmode == 'firstname') $user_identity = $userdata->user_firstname;
	if ($idmode == 'lastname')  $user_identity = $userdata->user_lastname;
	if ($idmode == 'namefl')    $user_identity = $userdata->user_firstname.' '.$userdata->user_lastname;
	if ($idmode == 'namelf')    $user_identity = $userdata->user_lastname.' '.$userdata->user_firstname;
	if (!$idmode) $user_identity = $userdata->user_nickname;
}
endif;

if ( !function_exists('get_userdata') ) :
function get_userdata($userid) {
	global $wpdb, $cache_userdata;
	$userid = (int) $userid;
	if ( empty($cache_userdata[$userid]) && $userid != 0) {
		$cache_userdata[$userid] = $wpdb->get_row("SELECT * FROM $wpdb->users WHERE ID = $userid");
		$cache_userdata[$cache_userdata[$userid]->user_login] =& $cache_userdata[$userid];
	} 

	return $cache_userdata[$userid];
}
endif;

if ( !function_exists('get_userdatabylogin') ) :
function get_userdatabylogin($user_login) {
	global $cache_userdata, $wpdb;
	if ( !empty($user_login) && empty($cache_userdata[$user_login]) ) {
		$user = $wpdb->get_row("SELECT * FROM $wpdb->users WHERE user_login = '$user_login'"); /* todo: get rid of this intermediate var */
		$cache_userdata[$user->ID] = $user;
		$cache_userdata[$user_login] =& $cache_userdata[$user->ID];
	} else {
		$user = $cache_userdata[$user_login];
	}
	return $user;
}
endif;

if ( !function_exists('wp_mail') ) :
function wp_mail($to, $subject, $message, $headers = '') {
	if( $headers == '' ) {
		$headers = "MIME-Version: 1.0\r\n" .
			"From: " . get_settings('admin_email') . "\r\n" . 
			"Content-Type: text/plain; charset=\"" . get_settings('blog_charset') . "\"\r\n";
	}

	return @mail($to, $subject, $message, $headers);
}
endif;

if ( !function_exists('wp_login') ) :
function wp_login($username, $password, $already_md5 = false) {
	global $wpdb, $error;

	if ( !$username )
		return false;

	if ( !$password ) {
		$error = __('<strong>Error</strong>: The password field is empty.');
		return false;
	}

	$login = $wpdb->get_row("SELECT ID, user_login, user_pass FROM $wpdb->users WHERE user_login = '$username'");

	if (!$login) {
		$error = __('<strong>Error</strong>: Wrong username.');
		return false;
	} else {
		// If the password is already_md5, it has been double hashed.
		// Otherwise, it is plain text.
		if ( ($already_md5 && $login->user_login == $username && md5($login->user_pass) == $password) || ($login->user_login == $username && $login->user_pass == md5($password)) ) {
			return true;
		} else {
			$error = __('<strong>Error</strong>: Incorrect password.');
			$pwd = '';
			return false;
		}
	}
}
endif;

if ( !function_exists('auth_redirect') ) :
function auth_redirect() {
	// Checks if a user is logged in, if not redirects them to the login page
	if ( (!empty($_COOKIE['wordpressuser_' . COOKIEHASH]) && 
				!wp_login($_COOKIE['wordpressuser_' . COOKIEHASH], $_COOKIE['wordpresspass_' . COOKIEHASH], true)) ||
			 (empty($_COOKIE['wordpressuser_' . COOKIEHASH])) ) {
		header('Expires: Mon, 11 Jan 1984 05:00:00 GMT');
		header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
		header('Cache-Control: no-cache, must-revalidate, max-age=0');
		header('Pragma: no-cache');
	
		header('Location: ' . get_settings('siteurl') . '/wp-login.php?redirect_to=' . urlencode($_SERVER['REQUEST_URI']));
		exit();
	}
}
endif;

// Cookie safe redirect.  Works around IIS Set-Cookie bug.
// http://support.microsoft.com/kb/q176113/
if ( !function_exists('wp_redirect') ) :
function wp_redirect($location) {
	global $is_IIS;

	if ($is_IIS)
		header("Refresh: 0;url=$location");
	else
		header("Location: $location");
}
endif;

if ( !function_exists('wp_setcookie') ) :
function wp_setcookie($username, $password, $already_md5 = false, $home = '', $siteurl = '') {
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

	setcookie('wordpressuser_'. $cookiehash, $username, time() + 31536000, $cookiepath);
	setcookie('wordpresspass_'. $cookiehash, $password, time() + 31536000, $cookiepath);

	if ( $cookiepath != $sitecookiepath ) {
		setcookie('wordpressuser_'. $cookiehash, $username, time() + 31536000, $sitecookiepath);
		setcookie('wordpresspass_'. $cookiehash, $password, time() + 31536000, $sitecookiepath);
	}
}
endif;

if ( !function_exists('wp_clearcookie') ) :
function wp_clearcookie() {
	setcookie('wordpressuser_' . COOKIEHASH, ' ', time() - 31536000, COOKIEPATH);
	setcookie('wordpresspass_' . COOKIEHASH, ' ', time() - 31536000, COOKIEPATH);
	setcookie('wordpressuser_' . COOKIEHASH, ' ', time() - 31536000, SITECOOKIEPATH);
	setcookie('wordpresspass_' . COOKIEHASH, ' ', time() - 31536000, SITECOOKIEPATH);
}
endif;

if ( ! function_exists('wp_notify_postauthor') ) :
function wp_notify_postauthor($comment_id, $comment_type='') {
	global $wpdb;
    
	$comment = $wpdb->get_row("SELECT * FROM $wpdb->comments WHERE comment_ID='$comment_id' LIMIT 1");
	$post = $wpdb->get_row("SELECT * FROM $wpdb->posts WHERE ID='$comment->comment_post_ID' LIMIT 1");
	$user = $wpdb->get_row("SELECT * FROM $wpdb->users WHERE ID='$post->post_author' LIMIT 1");

	if ('' == $user->user_email) return false; // If there's no email to send the comment to

	$comment_author_domain = gethostbyaddr($comment->comment_author_IP);

	$blogname = get_settings('blogname');
	
	if ( empty( $comment_type ) ) $comment_type = 'comment';
	
	if ('comment' == $comment_type) {
		$notify_message  = sprintf( __('New comment on your post #%1$s "%2$s"'), $comment->comment_post_ID, $post->post_title ) . "\r\n";
		$notify_message .= sprintf( __('Author : %1$s (IP: %2$s , %3$s)'), $comment->comment_author, $comment->comment_author_IP, $comment_author_domain ) . "\r\n";
		$notify_message .= sprintf( __('E-mail : %s'), $comment->comment_author_email ) . "\r\n";
		$notify_message .= sprintf( __('URI    : %s'), $comment->comment_author_url ) . "\r\n";
		$notify_message .= sprintf( __('Whois  : http://ws.arin.net/cgi-bin/whois.pl?queryinput=%s'), $comment->comment_author_IP ) . "\r\n";
		$notify_message .= __('Comment: ') . "\r\n" . $comment->comment_content . "\r\n\r\n";
		$notify_message .= __('You can see all comments on this post here: ') . "\r\n";
		$subject = sprintf( __('[%1$s] Comment: "%2$s"'), $blogname, $post->post_title );
	} elseif ('trackback' == $comment_type) {
		$notify_message  = sprintf( __('New trackback on your post #%1$s "%2$s"'), $comment->comment_post_ID, $post->post_title ) . "\r\n";
		$notify_message .= sprintf( __('Website: %1$s (IP: %2$s , %3$s)'), $comment->comment_author, $comment->comment_author_IP, $comment_author_domain ) . "\r\n";
		$notify_message .= sprintf( __('URI    : %s'), $comment->comment_author_url ) . "\r\n";
		$notify_message .= __('Excerpt: ') . "\r\n" . $comment->comment_content . "\r\n\r\n";
		$notify_message .= __('You can see all trackbacks on this post here: ') . "\r\n";
		$subject = sprintf( __('[%1$s] Trackback: "%2$s"'), $blogname, $post->post_title );
	} elseif ('pingback' == $comment_type) {
		$notify_message  = sprintf( __('New pingback on your post #%1$s "%2$s"'), $comment->comment_post_ID, $post->post_title ) . "\r\n";
		$notify_message .= sprintf( __('Website: %1$s (IP: %2$s , %3$s)'), $comment->comment_author, $comment->comment_author_IP, $comment_author_domain ) . "\r\n";
		$notify_message .= sprintf( __('URI    : %s'), $comment->comment_author_url ) . "\r\n";
		$notify_message .= __('Excerpt: ') . "\r\n" . sprintf( __('[...] %s [...]'), $comment->comment_content ) . "\r\n\r\n";
		$notify_message .= __('You can see all pingbacks on this post here: ') . "\r\n";
		$subject = sprintf( __('[%1$s] Pingback: "%2$s"'), $blogname, $post->post_title );
	}
	$notify_message .= get_permalink($comment->comment_post_ID) . "#comments\r\n\r\n";
	$notify_message .= sprintf( __('To delete this comment, visit: %s'), get_settings('siteurl').'/wp-admin/post.php?action=confirmdeletecomment&p='.$comment->comment_post_ID."&comment=$comment_id" ) . "\r\n";

	if ('' == $comment->comment_author_email || '' == $comment->comment_author) {
		$from = "From: \"$blogname\" <wordpress@" . $_SERVER['SERVER_NAME'] . '>';
	} else {
		$from = 'From: "' . $comment->comment_author . "\" <$comment->comment_author_email>";
	}

	$message_headers = "MIME-Version: 1.0\r\n"
		. "$from\r\n"
		. "Content-Type: text/plain; charset=\"" . get_settings('blog_charset') . "\"\r\n";

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

	if( get_settings( "moderation_notify" ) == 0 )
		return true; 
    
	$comment = $wpdb->get_row("SELECT * FROM $wpdb->comments WHERE comment_ID='$comment_id' LIMIT 1");
	$post = $wpdb->get_row("SELECT * FROM $wpdb->posts WHERE ID='$comment->comment_post_ID' LIMIT 1");
	$user = $wpdb->get_row("SELECT * FROM $wpdb->users WHERE ID='$post->post_author' LIMIT 1");

	$comment_author_domain = gethostbyaddr($comment->comment_author_IP);
	$comments_waiting = $wpdb->get_var("SELECT count(comment_ID) FROM $wpdb->comments WHERE comment_approved = '0'");

	$notify_message  = sprintf( __('A new comment on the post #%1$s "%2$s" is waiting for your approval'), $post->ID, $post->post_title ) . "\r\n";
	$notify_message .= get_permalink($comment->comment_post_ID) . "\r\n\r\n";
	$notify_message .= sprintf( __('Author : %1$s (IP: %2$s , %3$s)'), $comment->comment_author, $comment->comment_author_IP, $comment_author_domain ) . "\r\n";
	$notify_message .= sprintf( __('E-mail : %s'), $comment->comment_author_email ) . "\r\n";
	$notify_message .= sprintf( __('URI    : %s'), $comment->comment_author_url ) . "\r\n";
	$notify_message .= sprintf( __('Whois  : http://ws.arin.net/cgi-bin/whois.pl?queryinput=%s'), $comment->comment_author_IP ) . "\r\n";
	$notify_message .= __('Comment: ') . "\r\n" . $comment->comment_content . "\r\n\r\n";
	$notify_message .= sprintf( __('To approve this comment, visit: %s'),  get_settings('siteurl').'/wp-admin/post.php?action=mailapprovecomment&p='.$comment->comment_post_ID."&comment=$comment_id" ) . "\r\n";
	$notify_message .= sprintf( __('To delete this comment, visit: %s'), get_settings('siteurl').'/wp-admin/post.php?action=confirmdeletecomment&p='.$comment->comment_post_ID."&comment=$comment_id" ) . "\r\n";
	$notify_message .= sprintf( __('Currently %s comments are waiting for approval. Please visit the moderation panel:'), $comments_waiting ) . "\r\n";
	$notify_message .= get_settings('siteurl') . "/wp-admin/moderation.php\r\n";

	$subject = sprintf( __('[%1$s] Please moderate: "%2$s"'), get_settings('blogname'), $post->post_title );
	$admin_email = get_settings("admin_email");

	@wp_mail($admin_email, $subject, $notify_message);
    
	return true;
}
endif;

?>