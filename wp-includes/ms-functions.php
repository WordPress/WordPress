<?php
/**
 * Multisite WordPress API
 *
 * @package WordPress
 * @subpackage Multisite
 * @since 3.0.0
 */

/**
 * Gets the network's site and user counts.
 *
 * @since MU 1.0
 * @uses get_blog_count()
 * @uses get_user_count()
 *
 * @return array Site and user count for the network.
 */
function get_sitestats() {
	global $wpdb;

	$stats = array(
		'blogs' => get_blog_count(),
		'users' => get_user_count(),
	);

	return $stats;
}

/**
 * Get the admin for a domain/path combination.
 *
 * @since MU 1.0
 *
 * @param string $sitedomain Optional. Site domain.
 * @param string $path Optional. Site path.
 * @return array The network admins
 */
function get_admin_users_for_domain( $sitedomain = '', $path = '' ) {
	global $wpdb;

	if ( ! $sitedomain )
		$site_id = $wpdb->siteid;
	else
		$site_id = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM $wpdb->site WHERE domain = %s AND path = %s", $sitedomain, $path ) );

	if ( $site_id )
		return $wpdb->get_results( $wpdb->prepare( "SELECT u.ID, u.user_login, u.user_pass FROM $wpdb->users AS u, $wpdb->sitemeta AS sm WHERE sm.meta_key = 'admin_user_id' AND u.ID = sm.meta_value AND sm.site_id = %d", $site_id ), ARRAY_A );

	return false;
}

/**
 * Get one of a user's active blogs
 *
 * Returns the user's primary blog, if she has one and
 * it is active. If it's inactive, function returns another
 * active blog of the user. If none are found, the user
 * is added as a Subscriber to the Dashboard Blog and that blog
 * is returned.
 *
 * @since MU 1.0
 * @uses get_blogs_of_user()
 * @uses add_user_to_blog()
 * @uses get_blog_details()
 *
 * @param int $user_id The unique ID of the user
 * @return object The blog object
 */
function get_active_blog_for_user( $user_id ) {
	global $wpdb;
	$blogs = get_blogs_of_user( $user_id );
	if ( empty( $blogs ) )
		return null;

	if ( !is_multisite() )
		return $blogs[$wpdb->blogid];

	$primary_blog = get_user_meta( $user_id, 'primary_blog', true );
	$first_blog = current($blogs);
	if ( false !== $primary_blog ) {
		if ( ! isset( $blogs[ $primary_blog ] ) ) {
			update_user_meta( $user_id, 'primary_blog', $first_blog->userblog_id );
			$primary = get_blog_details( $first_blog->userblog_id );
		} else {
			$primary = get_blog_details( $primary_blog );
		}
	} else {
		//TODO Review this call to add_user_to_blog too - to get here the user must have a role on this blog?
		add_user_to_blog( $first_blog->userblog_id, $user_id, 'subscriber' );
		update_user_meta( $user_id, 'primary_blog', $first_blog->userblog_id );
		$primary = $first_blog;
	}

	if ( ( ! is_object( $primary ) ) || ( $primary->archived == 1 || $primary->spam == 1 || $primary->deleted == 1 ) ) {
		$blogs = get_blogs_of_user( $user_id, true ); // if a user's primary blog is shut down, check their other blogs.
		$ret = false;
		if ( is_array( $blogs ) && count( $blogs ) > 0 ) {
			foreach ( (array) $blogs as $blog_id => $blog ) {
				if ( $blog->site_id != $wpdb->siteid )
					continue;
				$details = get_blog_details( $blog_id );
				if ( is_object( $details ) && $details->archived == 0 && $details->spam == 0 && $details->deleted == 0 ) {
					$ret = $blog;
					if ( get_user_meta( $user_id , 'primary_blog', true ) != $blog_id )
						update_user_meta( $user_id, 'primary_blog', $blog_id );
					if ( !get_user_meta($user_id , 'source_domain', true) )
						update_user_meta( $user_id, 'source_domain', $blog->domain );
					break;
				}
			}
		} else {
			return null;
		}
		return $ret;
	} else {
		return $primary;
	}
}

/**
 * The number of active users in your installation.
 *
 * The count is cached and updated twice daily. This is not a live count.
 *
 * @since MU 2.7
 *
 * @return int
 */
function get_user_count() {
	return get_site_option( 'user_count' );
}

/**
 * The number of active sites on your installation.
 *
 * The count is cached and updated twice daily. This is not a live count.
 *
 * @since MU 1.0
 *
 * @param int $id Optional. A site_id.
 * @return int
 */
function get_blog_count( $id = 0 ) {
	return get_site_option( 'blog_count' );
}

/**
 * Get a blog post from any site on the network.
 *
 * @since MU 1.0
 *
 * @param int $blog_id ID of the blog.
 * @param int $post_id ID of the post you're looking for.
 * @return object The post.
 */
function get_blog_post( $blog_id, $post_id ) {
	global $wpdb;

	$key = $blog_id . '-' . $post_id;
	$post = wp_cache_get( $key, 'global-posts' );
	if ( $post == false ) {
		$post = $wpdb->get_row( $wpdb->prepare( 'SELECT * FROM ' . $wpdb->get_blog_prefix( $blog_id ) . 'posts WHERE ID = %d', $post_id ) );
		wp_cache_add( $key, $post, 'global-posts' );
	}

	return $post;
}

/**
 * Add a user to a blog.
 *
 * Use the 'add_user_to_blog' action to fire an event when
 * users are added to a blog.
 *
 * @since MU 1.0
 *
 * @param int $blog_id ID of the blog you're adding the user to.
 * @param int $user_id ID of the user you're adding.
 * @param string $role The role you want the user to have
 * @return bool
 */
function add_user_to_blog( $blog_id, $user_id, $role ) {
	switch_to_blog($blog_id);

	$user = new WP_User($user_id);

	if ( empty( $user->ID ) ) {
		restore_current_blog();
		return new WP_Error('user_does_not_exist', __('That user does not exist.'));
	}

	if ( !get_user_meta($user_id, 'primary_blog', true) ) {
		update_user_meta($user_id, 'primary_blog', $blog_id);
		$details = get_blog_details($blog_id);
		update_user_meta($user_id, 'source_domain', $details->domain);
	}

	$user->set_role($role);

	do_action('add_user_to_blog', $user_id, $role, $blog_id);
	wp_cache_delete( $user_id, 'users' );
	restore_current_blog();
	return true;
}

/**
 * Remove a user from a blog.
 *
 * Use the 'remove_user_from_blog' action to fire an event when
 * users are removed from a blog.
 *
 * Accepts an optional $reassign parameter, if you want to
 * reassign the user's blog posts to another user upon removal.
 *
 * @since MU 1.0
 *
 * @param int $user_id ID of the user you're removing.
 * @param int $blog_id ID of the blog you're removing the user from.
 * @param string $reassign Optional. A user to whom to reassign posts.
 * @return bool
 */
function remove_user_from_blog($user_id, $blog_id = '', $reassign = '') {
	global $wpdb;
	switch_to_blog($blog_id);
	$user_id = (int) $user_id;
	do_action('remove_user_from_blog', $user_id, $blog_id);

	// If being removed from the primary blog, set a new primary if the user is assigned
	// to multiple blogs.
	$primary_blog = get_user_meta($user_id, 'primary_blog', true);
	if ( $primary_blog == $blog_id ) {
		$new_id = '';
		$new_domain = '';
		$blogs = get_blogs_of_user($user_id);
		foreach ( (array) $blogs as $blog ) {
			if ( $blog->userblog_id == $blog_id )
				continue;
			$new_id = $blog->userblog_id;
			$new_domain = $blog->domain;
			break;
		}

		update_user_meta($user_id, 'primary_blog', $new_id);
		update_user_meta($user_id, 'source_domain', $new_domain);
	}

	// wp_revoke_user($user_id);
	$user = new WP_User($user_id);
	if ( empty( $user->ID ) ) {
		restore_current_blog();
		return new WP_Error('user_does_not_exist', __('That user does not exist.'));
	}

	$user->remove_all_caps();

	$blogs = get_blogs_of_user($user_id);
	if ( count($blogs) == 0 ) {
		update_user_meta($user_id, 'primary_blog', '');
		update_user_meta($user_id, 'source_domain', '');
	}

	if ( $reassign != '' ) {
		$reassign = (int) $reassign;
		$wpdb->query( $wpdb->prepare("UPDATE $wpdb->posts SET post_author = %d WHERE post_author = %d", $reassign, $user_id) );
		$wpdb->query( $wpdb->prepare("UPDATE $wpdb->links SET link_owner = %d WHERE link_owner = %d", $reassign, $user_id) );
	}

	restore_current_blog();

	return true;
}

/**
 * Create an empty blog.
 *
 * @since MU 1.0
 * @uses install_blog()
 *
 * @param string $domain The new blog's domain.
 * @param string $path The new blog's path.
 * @param string $weblog_title The new blog's title.
 * @param int $site_id Optional. Defaults to 1.
 * @return int The ID of the newly created blog
 */
function create_empty_blog( $domain, $path, $weblog_title, $site_id = 1 ) {
	$domain			= addslashes( $domain );
	$weblog_title	= addslashes( $weblog_title );

	if ( empty($path) )
		$path = '/';

	// Check if the domain has been used already. We should return an error message.
	if ( domain_exists($domain, $path, $site_id) )
		return __( '<strong>ERROR</strong>: Site URL already taken.' );

	// Need to back up wpdb table names, and create a new wp_blogs entry for new blog.
	// Need to get blog_id from wp_blogs, and create new table names.
	// Must restore table names at the end of function.

	if ( ! $blog_id = insert_blog($domain, $path, $site_id) )
		return __( '<strong>ERROR</strong>: problem creating site entry.' );

	switch_to_blog($blog_id);
	install_blog($blog_id);
	restore_current_blog();

	return $blog_id;
}

/**
 * Get the permalink for a post on another blog.
 *
 * @since MU 1.0
 *
 * @param int $_blog_id ID of the source blog.
 * @param int $post_id ID of the desired post.
 * @return string The post's permalink
 */
function get_blog_permalink( $_blog_id, $post_id ) {
	$key = "{$_blog_id}-{$post_id}-blog_permalink";
	$link = wp_cache_get( $key, 'site-options' );
	if ( $link == false ) {
		switch_to_blog( $_blog_id );
		$link = get_permalink( $post_id );
		restore_current_blog();
		wp_cache_add( $key, $link, 'site-options', 360 );
	}
	return $link;
}

/**
 * Get a blog's numeric ID from its URL.
 *
 * On a subdirectory installation like example.com/blog1/,
 * $domain will be the root 'example.com' and $path the
 * subdirectory '/blog1/'. With subdomains like blog1.example.com,
 * $domain is 'blog1.example.com' and $path is '/'.
 *
 * @since MU 2.6.5
 *
 * @param string $domain
 * @param string $path Optional. Not required for subdomain installations.
 * @return int
 */
function get_blog_id_from_url( $domain, $path = '/' ) {
	global $wpdb;

	$domain = strtolower( $wpdb->escape( $domain ) );
	$path = strtolower( $wpdb->escape( $path ) );
	$id = wp_cache_get( md5( $domain . $path ), 'blog-id-cache' );

	if ( $id == -1 ) { // blog does not exist
		return 0;
	} elseif ( $id ) {
		return (int)$id;
	}

	$id = $wpdb->get_var( "SELECT blog_id FROM $wpdb->blogs WHERE domain = '$domain' and path = '$path' /* get_blog_id_from_url */" );

	if ( !$id ) {
		wp_cache_set( md5( $domain . $path ), -1, 'blog-id-cache' );
		return false;
	}
	wp_cache_set( md5( $domain . $path ), $id, 'blog-id-cache' );

	return $id;
}

// Admin functions

/**
 * Checks an email address against a list of banned domains.
 *
 * This function checks against the Banned Email Domains list
 * at wp-admin/network/settings.php. The check is only run on
 * self-registrations; user creation at wp-admin/network/users.php
 * bypasses this check.
 *
 * @since MU
 *
 * @param string $user_email The email provided by the user at registration.
 * @return bool Returns true when the email address is banned.
 */
function is_email_address_unsafe( $user_email ) {
	$banned_names = get_site_option( 'banned_email_domains' );
	if ($banned_names && !is_array( $banned_names ))
		$banned_names = explode( "\n", $banned_names);

	if ( is_array( $banned_names ) && empty( $banned_names ) == false ) {
		$email_domain = strtolower( substr( $user_email, 1 + strpos( $user_email, '@' ) ) );
		foreach ( (array) $banned_names as $banned_domain ) {
			if ( $banned_domain == '' )
				continue;
			if (
				strstr( $email_domain, $banned_domain ) ||
				(
					strstr( $banned_domain, '/' ) &&
					preg_match( $banned_domain, $email_domain )
				)
			)
			return true;
		}
	}
	return false;
}

/**
 * Processes new user registrations.
 *
 * Checks the data provided by the user during signup. Verifies
 * the validity and uniqueness of user names and user email addresses,
 * and checks email addresses against admin-provided domain
 * whitelists and blacklists.
 *
 * The hook 'wpmu_validate_user_signup' provides an easy way
 * to modify the signup process. The value $result, which is passed
 * to the hook, contains both the user-provided info and the error
 * messages created by the function. 'wpmu_validate_user_signup' allows
 * you to process the data in any way you'd like, and unset the
 * relevant errors if necessary.
 *
 * @since MU
 * @uses is_email_address_unsafe()
 * @uses username_exists()
 * @uses email_exists()
 *
 * @param string $user_name The login name provided by the user.
 * @param string $user_email The email provided by the user.
 * @return array Contains username, email, and error messages.
 */
function wpmu_validate_user_signup($user_name, $user_email) {
	global $wpdb;

	$errors = new WP_Error();

	$orig_username = $user_name;
	$user_name = preg_replace( '/\s+/', '', sanitize_user( $user_name, true ) );
	$maybe = array();
	preg_match( '/[a-z0-9]+/', $user_name, $maybe );

	if ( $user_name != $orig_username || $user_name != $maybe[0] ) {
		$errors->add( 'user_name', __( 'Only lowercase letters (a-z) and numbers are allowed.' ) );
		$user_name = $orig_username;
	}

	$user_email = sanitize_email( $user_email );

	if ( empty( $user_name ) )
	   	$errors->add('user_name', __('Please enter a username'));

	$illegal_names = get_site_option( 'illegal_names' );
	if ( is_array( $illegal_names ) == false ) {
		$illegal_names = array(  'www', 'web', 'root', 'admin', 'main', 'invite', 'administrator' );
		add_site_option( 'illegal_names', $illegal_names );
	}
	if ( in_array( $user_name, $illegal_names ) == true )
		$errors->add('user_name',  __('That username is not allowed'));

	if ( is_email_address_unsafe( $user_email ) )
		$errors->add('user_email',  __('You cannot use that email address to signup. We are having problems with them blocking some of our email. Please use another email provider.'));

	if ( strlen( $user_name ) < 4 )
		$errors->add('user_name',  __('Username must be at least 4 characters'));

	if ( strpos( ' ' . $user_name, '_' ) != false )
		$errors->add( 'user_name', __( 'Sorry, usernames may not contain the character &#8220;_&#8221;!' ) );

	// all numeric?
	$match = array();
	preg_match( '/[0-9]*/', $user_name, $match );
	if ( $match[0] == $user_name )
		$errors->add('user_name', __('Sorry, usernames must have letters too!'));

	if ( !is_email( $user_email ) )
		$errors->add('user_email', __('Please enter a correct email address'));

	$limited_email_domains = get_site_option( 'limited_email_domains' );
	if ( is_array( $limited_email_domains ) && empty( $limited_email_domains ) == false ) {
		$emaildomain = substr( $user_email, 1 + strpos( $user_email, '@' ) );
		if ( in_array( $emaildomain, $limited_email_domains ) == false )
			$errors->add('user_email', __('Sorry, that email address is not allowed!'));
	}

	// Check if the username has been used already.
	if ( username_exists($user_name) )
		$errors->add('user_name', __('Sorry, that username already exists!'));

	// Check if the email address has been used already.
	if ( email_exists($user_email) )
		$errors->add('user_email', __('Sorry, that email address is already used!'));

	// Has someone already signed up for this username?
	$signup = $wpdb->get_row( $wpdb->prepare("SELECT * FROM $wpdb->signups WHERE user_login = %s", $user_name) );
	if ( $signup != null ) {
		$registered_at =  mysql2date('U', $signup->registered);
		$now = current_time( 'timestamp', true );
		$diff = $now - $registered_at;
		// If registered more than two days ago, cancel registration and let this signup go through.
		if ( $diff > 172800 )
			$wpdb->query( $wpdb->prepare("DELETE FROM $wpdb->signups WHERE user_login = %s", $user_name) );
		else
			$errors->add('user_name', __('That username is currently reserved but may be available in a couple of days.'));

		if ( $signup->active == 0 && $signup->user_email == $user_email )
			$errors->add('user_email_used', __('username and email used'));
	}

	$signup = $wpdb->get_row( $wpdb->prepare("SELECT * FROM $wpdb->signups WHERE user_email = %s", $user_email) );
	if ( $signup != null ) {
		$diff = current_time( 'timestamp', true ) - mysql2date('U', $signup->registered);
		// If registered more than two days ago, cancel registration and let this signup go through.
		if ( $diff > 172800 )
			$wpdb->query( $wpdb->prepare("DELETE FROM $wpdb->signups WHERE user_email = %s", $user_email) );
		else
			$errors->add('user_email', __('That email address has already been used. Please check your inbox for an activation email. It will become available in a couple of days if you do nothing.'));
	}

	$result = array('user_name' => $user_name, 'orig_username' => $orig_username, 'user_email' => $user_email, 'errors' => $errors);

	return apply_filters('wpmu_validate_user_signup', $result);
}

/**
 * Processes new site registrations.
 *
 * Checks the data provided by the user during blog signup. Verifies
 * the validity and uniqueness of blog paths and domains.
 *
 * This function prevents the current user from registering a new site
 * with a blogname equivalent to another user's login name. Passing the
 * $user parameter to the function, where $user is the other user, is
 * effectively an override of this limitation.
 *
 * Filter 'wpmu_validate_blog_signup' if you want to modify
 * the way that WordPress validates new site signups.
 *
 * @since MU
 * @uses domain_exists()
 * @uses username_exists()
 *
 * @param string $blogname The blog name provided by the user. Must be unique.
 * @param string $blog_title The blog title provided by the user.
 * @return array Contains the new site data and error messages.
 */
function wpmu_validate_blog_signup($blogname, $blog_title, $user = '') {
	global $wpdb, $domain, $base, $current_site;

	$blog_title = strip_tags( $blog_title );
	$blog_title = substr( $blog_title, 0, 50 );

	$errors = new WP_Error();
	$illegal_names = get_site_option( 'illegal_names' );
	if ( $illegal_names == false ) {
		$illegal_names = array( 'www', 'web', 'root', 'admin', 'main', 'invite', 'administrator' );
		add_site_option( 'illegal_names', $illegal_names );
	}

	// On sub dir installs, Some names are so illegal, only a filter can spring them from jail
	if (! is_subdomain_install() )
		$illegal_names = array_merge($illegal_names, apply_filters( 'subdirectory_reserved_names', array( 'page', 'comments', 'blog', 'files', 'feed' ) ) );

	if ( empty( $blogname ) )
		$errors->add('blogname', __('Please enter a site name'));

	if ( preg_match( '/[^a-z0-9]+/', $blogname ) )
		$errors->add('blogname', __('Only lowercase letters and numbers allowed'));

	if ( in_array( $blogname, $illegal_names ) == true )
		$errors->add('blogname',  __('That name is not allowed'));

	if ( strlen( $blogname ) < 4 && !is_super_admin() )
		$errors->add('blogname',  __('Site name must be at least 4 characters'));

	if ( strpos( ' ' . $blogname, '_' ) != false )
		$errors->add( 'blogname', __( 'Sorry, site names may not contain the character &#8220;_&#8221;!' ) );

	// do not allow users to create a blog that conflicts with a page on the main blog.
	if ( !is_subdomain_install() && $wpdb->get_var( $wpdb->prepare( "SELECT post_name FROM " . $wpdb->get_blog_prefix( $current_site->blog_id ) . "posts WHERE post_type = 'page' AND post_name = %s", $blogname ) ) )
		$errors->add( 'blogname', __( 'Sorry, you may not use that site name.' ) );

	// all numeric?
	$match = array();
	preg_match( '/[0-9]*/', $blogname, $match );
	if ( $match[0] == $blogname )
		$errors->add('blogname', __('Sorry, site names must have letters too!'));

	$blogname = apply_filters( 'newblogname', $blogname );

	$blog_title = stripslashes(  $blog_title );

	if ( empty( $blog_title ) )
		$errors->add('blog_title', __('Please enter a site title'));

	// Check if the domain/path has been used already.
	if ( is_subdomain_install() ) {
		$mydomain = $blogname . '.' . preg_replace( '|^www\.|', '', $domain );
		$path = $base;
	} else {
		$mydomain = "$domain";
		$path = $base.$blogname.'/';
	}
	if ( domain_exists($mydomain, $path) )
		$errors->add('blogname', __('Sorry, that site already exists!'));

	if ( username_exists( $blogname ) ) {
		if ( is_object( $user ) == false || ( is_object($user) && ( $user->user_login != $blogname ) ) )
			$errors->add( 'blogname', __( 'Sorry, that site is reserved!' ) );
	}

	// Has someone already signed up for this domain?
	$signup = $wpdb->get_row( $wpdb->prepare("SELECT * FROM $wpdb->signups WHERE domain = %s AND path = %s", $mydomain, $path) ); // TODO: Check email too?
	if ( ! empty($signup) ) {
		$diff = current_time( 'timestamp', true ) - mysql2date('U', $signup->registered);
		// If registered more than two days ago, cancel registration and let this signup go through.
		if ( $diff > 172800 )
			$wpdb->query( $wpdb->prepare("DELETE FROM $wpdb->signups WHERE domain = %s AND path = %s", $mydomain, $path) );
		else
			$errors->add('blogname', __('That site is currently reserved but may be available in a couple days.'));
	}

	$result = array('domain' => $mydomain, 'path' => $path, 'blogname' => $blogname, 'blog_title' => $blog_title, 'errors' => $errors);
	return apply_filters('wpmu_validate_blog_signup', $result);
}

/**
 * Record site signup information for future activation.
 *
 * @since MU
 * @uses wpmu_signup_blog_notification()
 *
 * @param string $domain The requested domain.
 * @param string $path The requested path.
 * @param string $title The requested site title.
 * @param string $user The user's requested login name.
 * @param string $user_email The user's email address.
 * @param array $meta By default, contains the requested privacy setting and lang_id.
 */
function wpmu_signup_blog($domain, $path, $title, $user, $user_email, $meta = '') {
	global $wpdb;

	$key = substr( md5( time() . rand() . $domain ), 0, 16 );
	$meta = serialize($meta);
	$domain = $wpdb->escape($domain);
	$path = $wpdb->escape($path);
	$title = $wpdb->escape($title);

	$wpdb->insert( $wpdb->signups, array(
		'domain' => $domain,
		'path' => $path,
		'title' => $title,
		'user_login' => $user,
		'user_email' => $user_email,
		'registered' => current_time('mysql', true),
		'activation_key' => $key,
		'meta' => $meta
	) );

	wpmu_signup_blog_notification($domain, $path, $title, $user, $user_email, $key, $meta);
}

/**
 * Record user signup information for future activation.
 *
 * This function is used when user registration is open but
 * new site registration is not.
 *
 * @since MU
 * @uses wpmu_signup_user_notification()
 *
 * @param string $user The user's requested login name.
 * @param string $user_email The user's email address.
 * @param array $meta By default, this is an empty array.
 */
function wpmu_signup_user($user, $user_email, $meta = '') {
	global $wpdb;

	// Format data
	$user = preg_replace( '/\s+/', '', sanitize_user( $user, true ) );
	$user_email = sanitize_email( $user_email );
	$key = substr( md5( time() . rand() . $user_email ), 0, 16 );
	$meta = serialize($meta);

	$wpdb->insert( $wpdb->signups, array(
		'domain' => '',
		'path' => '',
		'title' => '',
		'user_login' => $user,
		'user_email' => $user_email,
		'registered' => current_time('mysql', true),
		'activation_key' => $key,
		'meta' => $meta
	) );

	wpmu_signup_user_notification($user, $user_email, $key, $meta);
}

/**
 * Notify user of signup success.
 *
 * This is the notification function used when site registration
 * is enabled.
 *
 * Filter 'wpmu_signup_blog_notification' to bypass this function or
 * replace it with your own notification behavior.
 *
 * Filter 'wpmu_signup_blog_notification_email' and
 * 'wpmu_signup_blog_notification_subject' to change the content
 * and subject line of the email sent to newly registered users.
 *
 * @since MU
 *
 * @param string $domain The new blog domain.
 * @param string $path The new blog path.
 * @param string $title The site title.
 * @param string $user The user's login name.
 * @param string $user_email The user's email address.
 * @param array $meta By default, contains the requested privacy setting and lang_id.
 * @param string $key The activation key created in wpmu_signup_blog()
 * @return bool
 */
function wpmu_signup_blog_notification($domain, $path, $title, $user, $user_email, $key, $meta = '') {
	global $current_site;

	if ( !apply_filters('wpmu_signup_blog_notification', $domain, $path, $title, $user, $user_email, $key, $meta) )
		return false;

	// Send email with activation link.
	if ( !is_subdomain_install() || $current_site->id != 1 )
		$activate_url = network_site_url("wp-activate.php?key=$key");
	else
		$activate_url = "http://{$domain}{$path}wp-activate.php?key=$key"; // @todo use *_url() API

	$activate_url = esc_url($activate_url);
	$admin_email = get_site_option( 'admin_email' );
	if ( $admin_email == '' )
		$admin_email = 'support@' . $_SERVER['SERVER_NAME'];
	$from_name = get_site_option( 'site_name' ) == '' ? 'WordPress' : esc_html( get_site_option( 'site_name' ) );
	$message_headers = "From: \"{$from_name}\" <{$admin_email}>\n" . "Content-Type: text/plain; charset=\"" . get_option('blog_charset') . "\"\n";
	$message = sprintf(
		apply_filters( 'wpmu_signup_blog_notification_email',
			__( "To activate your blog, please click the following link:\n\n%s\n\nAfter you activate, you will receive *another email* with your login.\n\nAfter you activate, you can visit your site here:\n\n%s" ),
			$domain, $path, $title, $user, $user_email, $key, $meta
		),
		$activate_url,
		esc_url( "http://{$domain}{$path}" ),
		$key
	);
	// TODO: Don't hard code activation link.
	$subject = sprintf(
		apply_filters( 'wpmu_signup_blog_notification_subject',
			__( '[%1$s] Activate %2$s' ),
			$domain, $path, $title, $user, $user_email, $key, $meta
		),
		$from_name,
		esc_url( 'http://' . $domain . $path )
	);
	wp_mail($user_email, $subject, $message, $message_headers);
	return true;
}

/**
 * Notify user of signup success.
 *
 * This is the notification function used when no new site has
 * been requested.
 *
 * Filter 'wpmu_signup_user_notification' to bypass this function or
 * replace it with your own notification behavior.
 *
 * Filter 'wpmu_signup_user_notification_email' and
 * 'wpmu_signup_user_notification_subject' to change the content
 * and subject line of the email sent to newly registered users.
 *
 * @since MU
 *
 * @param string $user The user's login name.
 * @param string $user_email The user's email address.
 * @param array $meta By default, an empty array.
 * @param string $key The activation key created in wpmu_signup_user()
 * @return bool
 */
function wpmu_signup_user_notification($user, $user_email, $key, $meta = '') {
	if ( !apply_filters('wpmu_signup_user_notification', $user, $user_email, $key, $meta) )
		return false;

	// Send email with activation link.
	$admin_email = get_site_option( 'admin_email' );
	if ( $admin_email == '' )
		$admin_email = 'support@' . $_SERVER['SERVER_NAME'];
	$from_name = get_site_option( 'site_name' ) == '' ? 'WordPress' : esc_html( get_site_option( 'site_name' ) );
	$message_headers = "From: \"{$from_name}\" <{$admin_email}>\n" . "Content-Type: text/plain; charset=\"" . get_option('blog_charset') . "\"\n";
	$message = sprintf(
		apply_filters( 'wpmu_signup_user_notification_email',
			__( "To activate your user, please click the following link:\n\n%s\n\nAfter you activate, you will receive *another email* with your login.\n\n" ),
			$user, $user_email, $key, $meta
		),
		site_url( "wp-activate.php?key=$key" )
	);
	// TODO: Don't hard code activation link.
	$subject = sprintf(
		apply_filters( 'wpmu_signup_user_notification_subject',
			__( '[%1$s] Activate %2$s' ),
			$user, $user_email, $key, $meta
		),
		$from_name,
		$user
	);
	wp_mail($user_email, $subject, $message, $message_headers);
	return true;
}

/**
 * Activate a signup.
 *
 * Hook to 'wpmu_activate_user' or 'wpmu_activate_blog' for events
 * that should happen only when users or sites are self-created (since
 * those actions are not called when users and sites are created
 * by a Super Admin).
 *
 * @since MU
 * @uses wp_generate_password()
 * @uses wpmu_welcome_user_notification()
 * @uses add_user_to_blog()
 * @uses add_new_user_to_blog()
 * @uses wpmu_create_user()
 * @uses wpmu_create_blog()
 * @uses wpmu_welcome_notification()
 *
 * @param string $key The activation key provided to the user.
 * @return array An array containing information about the activated user and/or blog
 */
function wpmu_activate_signup($key) {
	global $wpdb, $current_site;

	$signup = $wpdb->get_row( $wpdb->prepare("SELECT * FROM $wpdb->signups WHERE activation_key = %s", $key) );

	if ( empty( $signup ) )
		return new WP_Error( 'invalid_key', __( 'Invalid activation key.' ) );

	if ( $signup->active ) {
		if ( empty( $signup->domain ) )
			return new WP_Error( 'already_active', __( 'The user is already active.' ), $signup );
		else
			return new WP_Error( 'already_active', __( 'The site is already active.' ), $signup );
	}

	$meta = unserialize($signup->meta);
	$user_login = $wpdb->escape($signup->user_login);
	$user_email = $wpdb->escape($signup->user_email);
	$password = wp_generate_password( 12, false );

	$user_id = username_exists($user_login);

	if ( ! $user_id )
		$user_id = wpmu_create_user($user_login, $password, $user_email);
	else
		$user_already_exists = true;

	if ( ! $user_id )
		return new WP_Error('create_user', __('Could not create user'), $signup);

	$now = current_time('mysql', true);

	if ( empty($signup->domain) ) {
		$wpdb->update( $wpdb->signups, array('active' => 1, 'activated' => $now), array('activation_key' => $key) );

		if ( isset( $user_already_exists ) )
			return new WP_Error( 'user_already_exists', __( 'That username is already activated.' ), $signup);

		wpmu_welcome_user_notification($user_id, $password, $meta);

		add_new_user_to_blog( $user_id, $user_email, $meta );
		do_action('wpmu_activate_user', $user_id, $password, $meta);
		return array('user_id' => $user_id, 'password' => $password, 'meta' => $meta);
	}

	$blog_id = wpmu_create_blog( $signup->domain, $signup->path, $signup->title, $user_id, $meta, $wpdb->siteid );

	// TODO: What to do if we create a user but cannot create a blog?
	if ( is_wp_error($blog_id) ) {
		// If blog is taken, that means a previous attempt to activate this blog failed in between creating the blog and
		// setting the activation flag. Let's just set the active flag and instruct the user to reset their password.
		if ( 'blog_taken' == $blog_id->get_error_code() ) {
			$blog_id->add_data( $signup );
			$wpdb->update( $wpdb->signups, array( 'active' => 1, 'activated' => $now ), array( 'activation_key' => $key ) );
		}
		return $blog_id;
	}

	$wpdb->update( $wpdb->signups, array('active' => 1, 'activated' => $now), array('activation_key' => $key) );
	wpmu_welcome_notification($blog_id, $user_id, $password, $signup->title, $meta);
	do_action('wpmu_activate_blog', $blog_id, $user_id, $password, $signup->title, $meta);

	return array('blog_id' => $blog_id, 'user_id' => $user_id, 'password' => $password, 'title' => $signup->title, 'meta' => $meta);
}

/**
 * Create a user.
 *
 * This function runs when a user self-registers as well as when
 * a Super Admin creates a new user. Hook to 'wpmu_new_user' for events
 * that should affect all new users, but only on Multisite (otherwise
 * use 'user_register').
 *
 * @since MU
 * @uses wp_create_user()
 *
 * @param string $user_name The new user's login name.
 * @param string $password The new user's password.
 * @param string $email The new user's email address.
 * @return mixed Returns false on failure, or int $user_id on success
 */
function wpmu_create_user( $user_name, $password, $email) {
	$user_name = preg_replace( '/\s+/', '', sanitize_user( $user_name, true ) );

	$user_id = wp_create_user( $user_name, $password, $email );
	if ( is_wp_error($user_id) )
		return false;

	// Newly created users have no roles or caps until they are added to a blog.
	delete_user_option( $user_id, 'capabilities' );
	delete_user_option( $user_id, 'user_level' );

	do_action( 'wpmu_new_user', $user_id );

	return $user_id;
}

/**
 * Create a site.
 *
 * This function runs when a user self-registers a new site as well
 * as when a Super Admin creates a new site. Hook to 'wpmu_new_blog'
 * for events that should affect all new sites.
 *
 * On subdirectory installs, $domain is the same as the main site's
 * domain, and the path is the subdirectory name (eg 'example.com'
 * and '/blog1/'). On subdomain installs, $domain is the new subdomain +
 * root domain (eg 'blog1.example.com'), and $path is '/'.
 *
 * @since MU
 * @uses domain_exists()
 * @uses insert_blog()
 * @uses wp_install_defaults()
 * @uses add_user_to_blog()
 *
 * @param string $domain The new site's domain.
 * @param string $path The new site's path.
 * @param string $title The new site's title.
 * @param int $user_id The user ID of the new site's admin.
 * @param array $meta Optional. Used to set initial site options.
 * @param int $site_id Optional. Only relevant on multi-network installs.
 * @return mixed Returns WP_Error object on failure, int $blog_id on success
 */
function wpmu_create_blog($domain, $path, $title, $user_id, $meta = '', $site_id = 1) {
	$domain = preg_replace( '/\s+/', '', sanitize_user( $domain, true ) );

	if ( is_subdomain_install() )
		$domain = str_replace( '@', '', $domain );

	$title = strip_tags( $title );
	$user_id = (int) $user_id;

	if ( empty($path) )
		$path = '/';

	// Check if the domain has been used already. We should return an error message.
	if ( domain_exists($domain, $path, $site_id) )
		return new WP_Error('blog_taken', __('Site already exists.'));

	if ( !defined('WP_INSTALLING') )
		define( 'WP_INSTALLING', true );

	if ( ! $blog_id = insert_blog($domain, $path, $site_id) )
		return new WP_Error('insert_blog', __('Could not create site.'));

	switch_to_blog($blog_id);
	install_blog($blog_id, $title);
	wp_install_defaults($user_id);

	add_user_to_blog($blog_id, $user_id, 'administrator');

	if ( is_array($meta) ) foreach ($meta as $key => $value) {
		if ( $key == 'public' || $key == 'archived' || $key == 'mature' || $key == 'spam' || $key == 'deleted' || $key == 'lang_id' )
			update_blog_status( $blog_id, $key, $value );
		else
			update_option( $key, $value );
	}

	add_option( 'WPLANG', get_site_option( 'WPLANG' ) );
	update_option( 'blog_public', (int)$meta['public'] );

	if ( ! is_super_admin( $user_id ) && ! get_user_meta( $user_id, 'primary_blog', true ) )
		update_user_meta( $user_id, 'primary_blog', $blog_id );

	restore_current_blog();
	do_action( 'wpmu_new_blog', $blog_id, $user_id, $domain, $path, $site_id, $meta );

	return $blog_id;
}

/**
 * Notifies the network admin that a new site has been activated.
 *
 * Filter 'newblog_notify_siteadmin' to change the content of
 * the notification email.
 *
 * @since MU
 *
 * @param int $blog_id The new site's ID.
 * @return bool
 */
function newblog_notify_siteadmin( $blog_id, $deprecated = '' ) {
	if ( get_site_option( 'registrationnotification' ) != 'yes' )
		return false;

	$email = get_site_option( 'admin_email' );
	if ( is_email($email) == false )
		return false;

	$options_site_url = esc_url(network_admin_url('settings.php'));

	switch_to_blog( $blog_id );
	$blogname = get_option( 'blogname' );
	$siteurl = site_url();
	restore_current_blog();

	$msg = sprintf( __( 'New Site: %1s
URL: %2s
Remote IP: %3s

Disable these notifications: %4s' ), $blogname, $siteurl, $_SERVER['REMOTE_ADDR'], $options_site_url);
	$msg = apply_filters( 'newblog_notify_siteadmin', $msg );

	wp_mail( $email, sprintf( __( 'New Site Registration: %s' ), $siteurl ), $msg );
	return true;
}

/**
 * Notifies the network admin that a new user has been activated.
 *
 * Filter 'newuser_notify_siteadmin' to change the content of
 * the notification email.
 *
 * @since MU
 *
 * @param int $user_id The new user's ID.
 * @return bool
 */
function newuser_notify_siteadmin( $user_id ) {
	if ( get_site_option( 'registrationnotification' ) != 'yes' )
		return false;

	$email = get_site_option( 'admin_email' );

	if ( is_email($email) == false )
		return false;

	$user = new WP_User($user_id);

	$options_site_url = esc_url(network_admin_url('settings.php'));
	$msg = sprintf(__('New User: %1s
Remote IP: %2s

Disable these notifications: %3s'), $user->user_login, $_SERVER['REMOTE_ADDR'], $options_site_url);

	$msg = apply_filters( 'newuser_notify_siteadmin', $msg );
	wp_mail( $email, sprintf(__('New User Registration: %s'), $user->user_login), $msg );
	return true;
}

/**
 * Check whether a blogname is already taken.
 *
 * Used during the new site registration process to ensure
 * that each blogname is unique.
 *
 * @since MU
 *
 * @param string $domain The domain to be checked.
 * @param string $path The path to be checked.
 * @param int $site_id Optional. Relevant only on multi-network installs.
 * @return int
 */
function domain_exists($domain, $path, $site_id = 1) {
	global $wpdb;
	return $wpdb->get_var( $wpdb->prepare("SELECT blog_id FROM $wpdb->blogs WHERE domain = %s AND path = %s AND site_id = %d", $domain, $path, $site_id) );
}

/**
 * Store basic site info in the blogs table.
 *
 * This function creates a row in the wp_blogs table and returns
 * the new blog's ID. It is the first step in creating a new blog.
 *
 * @since MU
 *
 * @param string $domain The domain of the new site.
 * @param string $path The path of the new site.
 * @param int $site_id Unless you're running a multi-network install, be sure to set this value to 1.
 * @return int The ID of the new row
 */
function insert_blog($domain, $path, $site_id) {
	global $wpdb;

	$path = trailingslashit($path);
	$site_id = (int) $site_id;

	$result = $wpdb->insert( $wpdb->blogs, array('site_id' => $site_id, 'domain' => $domain, 'path' => $path, 'registered' => current_time('mysql')) );
	if ( ! $result )
		return false;

	refresh_blog_details($wpdb->insert_id);
	return $wpdb->insert_id;
}

/**
 * Install an empty blog.
 *
 * Creates the new blog tables and options. If calling this function
 * directly, be sure to use switch_to_blog() first, so that $wpdb
 * points to the new blog.
 *
 * @since MU
 * @uses make_db_current_silent()
 * @uses populate_roles()
 *
 * @param int $blog_id The value returned by insert_blog().
 * @param string $blog_title The title of the new site.
 */
function install_blog($blog_id, $blog_title = '') {
	global $wpdb, $table_prefix, $wp_roles;
	$wpdb->suppress_errors();

	// Cast for security
	$blog_id = (int) $blog_id;

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

	if ( $wpdb->get_results("SELECT ID FROM $wpdb->posts") )
		die(__('<h1>Already Installed</h1><p>You appear to have already installed WordPress. To reinstall please clear your old database tables first.</p>') . '</body></html>');

	$wpdb->suppress_errors(false);

	$url = get_blogaddress_by_id($blog_id);

	// Set everything up
	make_db_current_silent( 'blog' );
	populate_options();
	populate_roles();
	$wp_roles->_init();

	// fix url.
	update_option('siteurl', $url);
	update_option('home', $url);
	update_option('fileupload_url', $url . "files" );
	update_option('upload_path', UPLOADBLOGSDIR . "/$blog_id/files");
	update_option('blogname', stripslashes( $blog_title ) );
	update_option('admin_email', '');
	$wpdb->update( $wpdb->options, array('option_value' => ''), array('option_name' => 'admin_email') );

	// remove all perms
	$wpdb->query( $wpdb->prepare("DELETE FROM $wpdb->usermeta WHERE meta_key = %s", $table_prefix.'user_level') );
	$wpdb->query( $wpdb->prepare("DELETE FROM $wpdb->usermeta WHERE meta_key = %s", $table_prefix.'capabilities') );

	$wpdb->suppress_errors( false );
}

/**
 * Set blog defaults.
 *
 * This function creates a row in the wp_blogs table.
 *
 * @since MU
 * @deprecated MU
 * @deprecated Use wp_install_defaults()
 * @uses wp_install_defaults()
 *
 * @param int $blog_id Ignored in this function.
 * @param int $user_id
 */
function install_blog_defaults($blog_id, $user_id) {
	global $wpdb;

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

	$wpdb->suppress_errors();

	wp_install_defaults($user_id);

	$wpdb->suppress_errors( false );
}

/**
 * Notify a user that her blog activation has been successful.
 *
 * Filter 'wpmu_welcome_notification' to disable or bypass.
 *
 * Filter 'update_welcome_email' and 'update_welcome_subject' to
 * modify the content and subject line of the notification email.
 *
 * @since MU
 *
 * @param int $blog_id
 * @param int $user_id
 * @param string $password
 * @param string $title The new blog's title
 * @param array $meta Optional. Not used in the default function, but is passed along to hooks for customization.
 * @return bool
 */
function wpmu_welcome_notification($blog_id, $user_id, $password, $title, $meta = '') {
	global $current_site;

	if ( !apply_filters('wpmu_welcome_notification', $blog_id, $user_id, $password, $title, $meta) )
		return false;

	$welcome_email = stripslashes( get_site_option( 'welcome_email' ) );
	if ( $welcome_email == false )
		$welcome_email = stripslashes( __( 'Dear User,

Your new SITE_NAME site has been successfully set up at:
BLOG_URL

You can log in to the administrator account with the following information:
Username: USERNAME
Password: PASSWORD
Log in here: BLOG_URLwp-login.php

We hope you enjoy your new site. Thanks!

--The Team @ SITE_NAME' ) );

	$url = get_blogaddress_by_id($blog_id);
	$user = new WP_User($user_id);

	$welcome_email = str_replace( 'SITE_NAME', $current_site->site_name, $welcome_email );
	$welcome_email = str_replace( 'BLOG_TITLE', $title, $welcome_email );
	$welcome_email = str_replace( 'BLOG_URL', $url, $welcome_email );
	$welcome_email = str_replace( 'USERNAME', $user->user_login, $welcome_email );
	$welcome_email = str_replace( 'PASSWORD', $password, $welcome_email );

	$welcome_email = apply_filters( 'update_welcome_email', $welcome_email, $blog_id, $user_id, $password, $title, $meta);
	$admin_email = get_site_option( 'admin_email' );

	if ( $admin_email == '' )
		$admin_email = 'support@' . $_SERVER['SERVER_NAME'];

	$from_name = get_site_option( 'site_name' ) == '' ? 'WordPress' : esc_html( get_site_option( 'site_name' ) );
	$message_headers = "From: \"{$from_name}\" <{$admin_email}>\n" . "Content-Type: text/plain; charset=\"" . get_option('blog_charset') . "\"\n";
	$message = $welcome_email;

	if ( empty( $current_site->site_name ) )
		$current_site->site_name = 'WordPress';

	$subject = apply_filters( 'update_welcome_subject', sprintf(__('New %1$s Site: %2$s'), $current_site->site_name, stripslashes( $title ) ) );
	wp_mail($user->user_email, $subject, $message, $message_headers);
	return true;
}

/**
 * Notify a user that her account activation has been successful.
 *
 * Filter 'wpmu_welcome_user_notification' to disable or bypass.
 *
 * Filter 'update_welcome_user_email' and 'update_welcome_user_subject' to
 * modify the content and subject line of the notification email.
 *
 * @since MU
 *
 * @param int $user_id
 * @param string $password
 * @param array $meta Optional. Not used in the default function, but is passed along to hooks for customization.
 * @return bool
 */
function wpmu_welcome_user_notification($user_id, $password, $meta = '') {
	global $current_site;

	if ( !apply_filters('wpmu_welcome_user_notification', $user_id, $password, $meta) )
		return false;

	$welcome_email = get_site_option( 'welcome_user_email' );

	$user = new WP_User($user_id);

	$welcome_email = apply_filters( 'update_welcome_user_email', $welcome_email, $user_id, $password, $meta);
	$welcome_email = str_replace( 'SITE_NAME', $current_site->site_name, $welcome_email );
	$welcome_email = str_replace( 'USERNAME', $user->user_login, $welcome_email );
	$welcome_email = str_replace( 'PASSWORD', $password, $welcome_email );
	$welcome_email = str_replace( 'LOGINLINK', wp_login_url(), $welcome_email );

	$admin_email = get_site_option( 'admin_email' );

	if ( $admin_email == '' )
		$admin_email = 'support@' . $_SERVER['SERVER_NAME'];

	$from_name = get_site_option( 'site_name' ) == '' ? 'WordPress' : esc_html( get_site_option( 'site_name' ) );
	$message_headers = "From: \"{$from_name}\" <{$admin_email}>\n" . "Content-Type: text/plain; charset=\"" . get_option('blog_charset') . "\"\n";
	$message = $welcome_email;

	if ( empty( $current_site->site_name ) )
		$current_site->site_name = 'WordPress';

	$subject = apply_filters( 'update_welcome_user_subject', sprintf(__('New %1$s User: %2$s'), $current_site->site_name, $user->user_login) );
	wp_mail($user->user_email, $subject, $message, $message_headers);
	return true;
}

/**
 * Get the current site info.
 *
 * Returns an object containing the ID, domain, path, and site_name
 * of the site being viewed.
 *
 * @since MU
 *
 * @return object
 */
function get_current_site() {
	global $current_site;
	return $current_site;
}

/**
 * Get a numeric user ID from either an email address or a login.
 *
 * @since MU
 * @uses is_email()
 *
 * @param string $string
 * @return int
 */
function get_user_id_from_string( $string ) {
	$user_id = 0;
	if ( is_email( $string ) ) {
		$user = get_user_by('email', $string);
		if ( $user )
			$user_id = $user->ID;
	} elseif ( is_numeric( $string ) ) {
		$user_id = $string;
	} else {
		$user = get_user_by('login', $string);
		if ( $user )
			$user_id = $user->ID;
	}

	return $user_id;
}

/**
 * Get a user's most recent post.
 *
 * Walks through each of a user's blogs to find the post with
 * the most recent post_date_gmt.
 *
 * @since MU
 * @uses get_blogs_of_user()
 *
 * @param int $user_id
 * @return array Contains the blog_id, post_id, post_date_gmt, and post_gmt_ts
 */
function get_most_recent_post_of_user( $user_id ) {
	global $wpdb;

	$user_blogs = get_blogs_of_user( (int) $user_id );
	$most_recent_post = array();

	// Walk through each blog and get the most recent post
	// published by $user_id
	foreach ( (array) $user_blogs as $blog ) {
		$prefix = $wpdb->get_blog_prefix( $blog->userblog_id );
		$recent_post = $wpdb->get_row( $wpdb->prepare("SELECT ID, post_date_gmt FROM {$prefix}posts WHERE post_author = %d AND post_type = 'post' AND post_status = 'publish' ORDER BY post_date_gmt DESC LIMIT 1", $user_id ), ARRAY_A);

		// Make sure we found a post
		if ( isset($recent_post['ID']) ) {
			$post_gmt_ts = strtotime($recent_post['post_date_gmt']);

			// If this is the first post checked or if this post is
			// newer than the current recent post, make it the new
			// most recent post.
			if ( !isset($most_recent_post['post_gmt_ts']) || ( $post_gmt_ts > $most_recent_post['post_gmt_ts'] ) ) {
				$most_recent_post = array(
					'blog_id'		=> $blog->userblog_id,
					'post_id'		=> $recent_post['ID'],
					'post_date_gmt'	=> $recent_post['post_date_gmt'],
					'post_gmt_ts'	=> $post_gmt_ts
				);
			}
		}
	}

	return $most_recent_post;
}

// Misc functions

/**
 * Get the size of a directory.
 *
 * A helper function that is used primarily to check whether
 * a blog has exceeded its allowed upload space.
 *
 * @since MU
 * @uses recurse_dirsize()
 *
 * @param string $directory
 * @return int
 */
function get_dirsize( $directory ) {
	$dirsize = get_transient( 'dirsize_cache' );
	if ( is_array( $dirsize ) && isset( $dirsize[ $directory ][ 'size' ] ) )
		return $dirsize[ $directory ][ 'size' ];

	if ( false == is_array( $dirsize ) )
		$dirsize = array();

	$dirsize[ $directory ][ 'size' ] = recurse_dirsize( $directory );

	set_transient( 'dirsize_cache', $dirsize, 3600 );
	return $dirsize[ $directory ][ 'size' ];
}

/**
 * Get the size of a directory recursively.
 *
 * Used by get_dirsize() to get a directory's size when it contains
 * other directories.
 *
 * @since MU
 *
 * @param string $directory
 * @return int
 */
function recurse_dirsize( $directory ) {
	$size = 0;

	$directory = untrailingslashit( $directory );

	if ( !file_exists($directory) || !is_dir( $directory ) || !is_readable( $directory ) )
		return false;

	if ($handle = opendir($directory)) {
		while(($file = readdir($handle)) !== false) {
			$path = $directory.'/'.$file;
			if ($file != '.' && $file != '..') {
				if (is_file($path)) {
					$size += filesize($path);
				} elseif (is_dir($path)) {
					$handlesize = recurse_dirsize($path);
					if ($handlesize > 0)
						$size += $handlesize;
				}
			}
		}
		closedir($handle);
	}
	return $size;
}

/**
 * Check whether a blog has used its allotted upload space.
 *
 * @since MU
 * @uses get_dirsize()
 *
 * @param bool $echo Optional. If $echo is set and the quota is exceeded, a warning message is echoed. Default is true.
 * @return int
 */
function upload_is_user_over_quota( $echo = true ) {
	if ( get_site_option( 'upload_space_check_disabled' ) )
		return false;

	$spaceAllowed = get_space_allowed();
	if ( empty( $spaceAllowed ) || !is_numeric( $spaceAllowed ) )
		$spaceAllowed = 10;	// Default space allowed is 10 MB

	$size = get_dirsize( BLOGUPLOADDIR ) / 1024 / 1024;

	if ( ($spaceAllowed-$size) < 0 ) {
		if ( $echo )
			_e( 'Sorry, you have used your space allocation. Please delete some files to upload more files.' ); // No space left
		return true;
	} else {
		return false;
	}
}

/**
 * Check an array of MIME types against a whitelist.
 *
 * WordPress ships with a set of allowed upload filetypes,
 * which is defined in wp-includes/functions.php in
 * get_allowed_mime_types(). This function is used to filter
 * that list against the filetype whitelist provided by Multisite
 * Super Admins at wp-admin/network/settings.php.
 *
 * @since MU
 *
 * @param array $mimes
 * @return array
 */
function check_upload_mimes( $mimes ) {
	$site_exts = explode( ' ', get_site_option( 'upload_filetypes' ) );
	foreach ( $site_exts as $ext ) {
		foreach ( $mimes as $ext_pattern => $mime ) {
			if ( $ext != '' && strpos( $ext_pattern, $ext ) !== false )
				$site_mimes[$ext_pattern] = $mime;
		}
	}
	return $site_mimes;
}

/**
 * Update a blog's post count.
 *
 * WordPress MS stores a blog's post count as an option so as
 * to avoid extraneous COUNTs when a blog's details are fetched
 * with get_blog_details(). This function is called when posts
 * are published to make sure the count stays current.
 *
 * @since MU
 */
function update_posts_count( $deprecated = '' ) {
	global $wpdb;
	update_option( 'post_count', (int) $wpdb->get_var( "SELECT COUNT(ID) FROM {$wpdb->posts} WHERE post_status = 'publish' and post_type = 'post'" ) );
}

/**
 * Logs user registrations.
 *
 * @since MU
 *
 * @param int $blog_id
 * @param int $user_id
 */
function wpmu_log_new_registrations( $blog_id, $user_id ) {
	global $wpdb;
	$user = new WP_User( (int) $user_id );
	$wpdb->insert( $wpdb->registration_log, array('email' => $user->user_email, 'IP' => preg_replace( '/[^0-9., ]/', '',$_SERVER['REMOTE_ADDR'] ), 'blog_id' => $blog_id, 'date_registered' => current_time('mysql')) );
}

/**
 * Get the remaining upload space for this blog.
 *
 * @since MU
 * @uses upload_is_user_over_quota()
 * @uses get_space_allowed()
 * @uses get_dirsize()
 *
 * @param int $size
 * @return int
 */
function fix_import_form_size( $size ) {
	if ( upload_is_user_over_quota( false ) == true )
		return 0;

	$spaceAllowed = 1024 * 1024 * get_space_allowed();
	$dirsize = get_dirsize( BLOGUPLOADDIR );
	if ( $size > $spaceAllowed - $dirsize )
		return $spaceAllowed - $dirsize; // remaining space
	else
		return $size; // default
}

/**
 * Maintains a canonical list of terms by syncing terms created for each blog with the global terms table.
 *
 * @since 3.0.0
 *
 * @see term_id_filter
 *
 * @param int $term_id An ID for a term on the current blog.
 * @return int An ID from the global terms table mapped from $term_id.
 */
function global_terms( $term_id, $deprecated = '' ) {
	global $wpdb;
	static $global_terms_recurse = null;

	if ( !global_terms_enabled() )
		return $term_id;

	// prevent a race condition
	$recurse_start = false;
	if ( $global_terms_recurse === null ) {
		$recurse_start = true;
		$global_terms_recurse = 1;
	} elseif ( 10 < $global_terms_recurse++ ) {
		return $term_id;
	}

	$term_id = intval( $term_id );
	$c = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $wpdb->terms WHERE term_id = %d", $term_id ) );

	$global_id = $wpdb->get_var( $wpdb->prepare( "SELECT cat_ID FROM $wpdb->sitecategories WHERE category_nicename = %s", $c->slug ) );
	if ( $global_id == null ) {
		$used_global_id = $wpdb->get_var( $wpdb->prepare( "SELECT cat_ID FROM $wpdb->sitecategories WHERE cat_ID = %d", $c->term_id ) );
		if ( null == $used_global_id ) {
			$wpdb->insert( $wpdb->sitecategories, array( 'cat_ID' => $term_id, 'cat_name' => $c->name, 'category_nicename' => $c->slug ) );
			$global_id = $wpdb->insert_id;
			if ( empty( $global_id ) )
				return $term_id;
		} else {
			$max_global_id = $wpdb->get_var( "SELECT MAX(cat_ID) FROM $wpdb->sitecategories" );
			$max_local_id = $wpdb->get_var( "SELECT MAX(term_id) FROM $wpdb->terms" );
			$new_global_id = max( $max_global_id, $max_local_id ) + mt_rand( 100, 400 );
			$wpdb->insert( $wpdb->sitecategories, array( 'cat_ID' => $new_global_id, 'cat_name' => $c->name, 'category_nicename' => $c->slug ) );
			$global_id = $wpdb->insert_id;
		}
	} elseif ( $global_id != $term_id ) {
		$local_id = $wpdb->get_row( $wpdb->prepare( "SELECT term_id FROM $wpdb->terms WHERE term_id = %d", $global_id ) );
		if ( null != $local_id )
			$local_id = global_terms( $local_id );
			if ( 10 < $global_terms_recurse )
				$global_id = $term_id;
	}

	if ( $global_id != $term_id ) {
		if ( get_option( 'default_category' ) == $term_id )
			update_option( 'default_category', $global_id );

		$wpdb->update( $wpdb->terms, array('term_id' => $global_id), array('term_id' => $term_id) );
		$wpdb->update( $wpdb->term_taxonomy, array('term_id' => $global_id), array('term_id' => $term_id) );
		$wpdb->update( $wpdb->term_taxonomy, array('parent' => $global_id), array('parent' => $term_id) );

		clean_term_cache($term_id);
	}
	if( $recurse_start )
		$global_terms_recurse = null;

	return $global_id;
}

/**
 * Ensure that the current site's domain is listed in the allowed redirect host list.
 *
 * @see wp_validate_redirect()
 * @since MU
 *
 * @return array The current site's domain
 */
function redirect_this_site( $deprecated = '' ) {
	global $current_site;
	return array( $current_site->domain );
}

/**
 * Check whether an upload is too big.
 *
 * @since MU
 *
 * @param array $upload
 * @return mixed If the upload is under the size limit, $upload is returned. Otherwise returns an error message.
 */
function upload_is_file_too_big( $upload ) {
	if ( is_array( $upload ) == false || defined( 'WP_IMPORTING' ) )
		return $upload;

	if ( strlen( $upload['bits'] )  > ( 1024 * get_site_option( 'fileupload_maxk', 1500 ) ) )
		return sprintf( __( 'This file is too big. Files must be less than %d KB in size.' ) . '<br />', get_site_option( 'fileupload_maxk', 1500 ));

	return $upload;
}

/**
 * Add a nonce field to the signup page.
 *
 * @since MU
 * @uses wp_nonce_field()
 */
function signup_nonce_fields() {
	$id = mt_rand();
	echo "<input type='hidden' name='signup_form_id' value='{$id}' />";
	wp_nonce_field('signup_form_' . $id, '_signup_form', false);
}

/**
 * Process the signup nonce created in signup_nonce_fields().
 *
 * @since MU
 * @uses wp_create_nonce()
 *
 * @param array $result
 * @return array
 */
function signup_nonce_check( $result ) {
	if ( !strpos( $_SERVER[ 'PHP_SELF' ], 'wp-signup.php' ) )
		return $result;

	if ( wp_create_nonce('signup_form_' . $_POST[ 'signup_form_id' ]) != $_POST['_signup_form'] )
		wp_die( __('Please try again!') );

	return $result;
}

/**
 * Correct 404 redirects when NOBLOGREDIRECT is defined.
 *
 * @since MU
 */
function maybe_redirect_404() {
	global $current_site;
	if ( is_main_site() && is_404() && defined( 'NOBLOGREDIRECT' ) && ( $destination = apply_filters( 'blog_redirect_404', NOBLOGREDIRECT ) ) ) {
		if ( $destination == '%siteurl%' )
			$destination = network_home_url();
		wp_redirect( $destination );
		exit();
	}
}

/**
 * Add a new user to a blog by visiting /newbloguser/username/.
 *
 * This will only work when the user's details are saved as an option
 * keyed as 'new_user_x', where 'x' is the username of the user to be
 * added, as when a user is invited through the regular WP Add User interface.
 *
 * @since MU
 * @uses add_existing_user_to_blog()
 */
function maybe_add_existing_user_to_blog() {
	if ( false === strpos( $_SERVER[ 'REQUEST_URI' ], '/newbloguser/' ) )
		return false;

	$parts = explode( '/', $_SERVER[ 'REQUEST_URI' ] );
	$key = array_pop( $parts );

	if ( $key == '' )
		$key = array_pop( $parts );

	$details = get_option( 'new_user_' . $key );
	if ( !empty( $details ) )
		delete_option( 'new_user_' . $key );

	if ( empty( $details ) || is_wp_error( add_existing_user_to_blog( $details ) ) )
		wp_die( sprintf(__('An error occurred adding you to this site. Back to the <a href="%s">homepage</a>.'), home_url() ) );

	wp_die( sprintf(__('You have been added to this site. Please visit the <a href="%s">homepage</a> or <a href="%s">log in</a> using your username and password.'), home_url(), admin_url() ), __('Success') );
}

/**
 * Add a user to a blog based on details from maybe_add_existing_user_to_blog().
 *
 * @since MU
 * @uses add_user_to_blog()
 *
 * @param array $details
 */
function add_existing_user_to_blog( $details = false ) {
	global $blog_id;

	if ( is_array( $details ) ) {
		$result = add_user_to_blog( $blog_id, $details[ 'user_id' ], $details[ 'role' ] );
		do_action( 'added_existing_user', $details[ 'user_id' ], $result );
	}
	return $result;
}

/**
 * Add a newly created user to the appropriate blog
 *
 * @since MU
 *
 * @param int $user_id
 * @param string $email
 * @param array $meta
 */
function add_new_user_to_blog( $user_id, $email, $meta ) {
	global $current_site;
	if ( !empty( $meta[ 'add_to_blog' ] ) ) {
		$blog_id = $meta[ 'add_to_blog' ];
		$role = $meta[ 'new_role' ];
		remove_user_from_blog($user_id, $current_site->blog_id); // remove user from main blog.
		add_user_to_blog( $blog_id, $user_id, $role );
		update_user_meta( $user_id, 'primary_blog', $blog_id );
	}
}

/**
 * Correct From host on outgoing mail to match the site domain
 *
 * @since MU
 */
function fix_phpmailer_messageid( $phpmailer ) {
	global $current_site;
	$phpmailer->Hostname = $current_site->domain;
}

/**
 * Check to see whether a user is marked as a spammer, based on username
 *
 * @since MU
 * @uses get_current_user_id()
 * @uses get_user_id_from_string()
 *
 * @param string $username
 * @return bool
 */
function is_user_spammy( $username = 0 ) {
	if ( $username == 0 ) {
		$user_id = get_current_user_id();
	} else {
		$user_id = get_user_id_from_string( $username );
	}
	$u = new WP_User( $user_id );

	return ( isset( $u->spam ) && $u->spam == 1 );
}

/**
 * Update this blog's 'public' setting in the global blogs table.
 *
 * Public blogs have a setting of 1, private blogs are 0.
 *
 * @since MU
 * @uses update_blog_status()
 *
 * @param int $old_value
 * @param int $value The new public value
 * @return bool
 */
function update_blog_public( $old_value, $value ) {
	global $wpdb;
	do_action('update_blog_public');
	update_blog_status( $wpdb->blogid, 'public', (int) $value );
}
add_action('update_option_blog_public', 'update_blog_public', 10, 2);

/**
 * Get the "dashboard blog", the blog where users without a blog edit their profile data.
 *
 * @since MU
 * @uses get_blog_details()
 *
 * @return int
 */
function get_dashboard_blog() {
	if ( $blog = get_site_option( 'dashboard_blog' ) )
		return get_blog_details( $blog );

	return get_blog_details( $GLOBALS['current_site']->blog_id );
}

/**
 * Check whether a usermeta key has to do with the current blog.
 *
 * @since MU
 * @uses wp_get_current_user()
 *
 * @param string $key
 * @param int $user_id Optional. Defaults to current user.
 * @param int $blog_id Optional. Defaults to current blog.
 * @return bool
 */
function is_user_option_local( $key, $user_id = 0, $blog_id = 0 ) {
	global $wpdb;

	$current_user = wp_get_current_user();
	if ( $user_id == 0 )
		$user_id = $current_user->ID;
	if ( $blog_id == 0 )
		$blog_id = $wpdb->blogid;

	$local_key = $wpdb->base_prefix . $blog_id . '_' . $key;

	if ( isset( $current_user->$local_key ) )
		return true;

	return false;
}

/**
 * Check whether users can self-register, based on Network settings.
 *
 * @since MU
 *
 * @return bool
 */
function users_can_register_signup_filter() {
	$registration = get_site_option('registration');
	if ( $registration == 'all' || $registration == 'user' )
		return true;

	return false;
}
add_filter('option_users_can_register', 'users_can_register_signup_filter');

/**
 * Ensure that the welcome message is not empty. Currently unused.
 *
 * @since MU
 *
 * @param string $text
 * @return string
 */
function welcome_user_msg_filter( $text ) {
	if ( !$text ) {
		return __( 'Dear User,

Your new account is set up.

You can log in with the following information:
Username: USERNAME
Password: PASSWORD
LOGINLINK

Thanks!

--The Team @ SITE_NAME' );
	}
	return $text;
}
add_filter( 'site_option_welcome_user_email', 'welcome_user_msg_filter' );

/**
 * Whether to force SSL on content.
 *
 * @since 2.8.5
 *
 * @param string|bool $force
 * @return bool True if forced, false if not forced.
 */
function force_ssl_content( $force = '' ) {
	static $forced_content;

	if ( '' != $force ) {
		$old_forced = $forced_content;
		$forced_content = $force;
		return $old_forced;
	}

	return $forced_content;
}

/**
 * Formats an String URL to use HTTPS if HTTP is found.
 * Useful as a filter.
 *
 * @since 2.8.5
 **/
function filter_SSL( $url ) {
	if ( !is_string( $url ) )
		return get_bloginfo( 'url' ); //return home blog url with proper scheme

	$arrURL = parse_url( $url );

	if ( force_ssl_content() && is_ssl() ) {
		if ( 'http' === $arrURL['scheme'] && 'https' !== $arrURL['scheme'] )
			$url = str_replace( $arrURL['scheme'], 'https', $url );
	}

	return $url;
}

/**
 * Schedule update of the network-wide counts for the current network.
 *
 * @since 3.1.0
 */
function wp_schedule_update_network_counts() {
	if ( !is_main_site() )
		return;

	if ( !wp_next_scheduled('update_network_counts') && !defined('WP_INSTALLING') )
		wp_schedule_event(time(), 'twicedaily', 'update_network_counts');
}

/**
 *  Update the network-wide counts for the current network.
 *
 *  @since 3.1.0
 */
function wp_update_network_counts() {
	global $wpdb;

	$count = $wpdb->get_var( $wpdb->prepare("SELECT COUNT(blog_id) as c FROM $wpdb->blogs WHERE site_id = %d AND spam = '0' AND deleted = '0' and archived = '0'", $wpdb->siteid) );
	update_site_option( 'blog_count', $count );

	$count = $wpdb->get_var( $wpdb->prepare("SELECT COUNT(ID) as c FROM $wpdb->users WHERE spam = '0' AND deleted = '0'") );
	update_site_option( 'user_count', $count );
}
