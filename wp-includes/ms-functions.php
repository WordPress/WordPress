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
 *
 * @return array Site and user count for the network.
 */
function get_sitestats() {
	$stats = array(
		'blogs' => get_blog_count(),
		'users' => get_user_count(),
	);

	return $stats;
}

/**
 * Get one of a user's active blogs
 *
 * Returns the user's primary blog, if they have one and
 * it is active. If it's inactive, function returns another
 * active blog of the user. If none are found, the user
 * is added as a Subscriber to the Dashboard Blog and that blog
 * is returned.
 *
 * @since MU 1.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param int $user_id The unique ID of the user
 * @return object|void The blog object
 */
function get_active_blog_for_user( $user_id ) {
	global $wpdb;
	$blogs = get_blogs_of_user( $user_id );
	if ( empty( $blogs ) )
		return;

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
			return;
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
 * @param int $network_id Deprecated, not supported.
 * @return int
 */
function get_blog_count( $network_id = 0 ) {
	if ( func_num_args() )
		_deprecated_argument( __FUNCTION__, '3.1' );

	return get_site_option( 'blog_count' );
}

/**
 * Get a blog post from any site on the network.
 *
 * @since MU 1.0
 *
 * @param int $blog_id ID of the blog.
 * @param int $post_id ID of the post you're looking for.
 * @return WP_Post|null WP_Post on success or null on failure
 */
function get_blog_post( $blog_id, $post_id ) {
	switch_to_blog( $blog_id );
	$post = get_post( $post_id );
	restore_current_blog();

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
 * @param int    $blog_id ID of the blog you're adding the user to.
 * @param int    $user_id ID of the user you're adding.
 * @param string $role    The role you want the user to have
 * @return true|WP_Error
 */
function add_user_to_blog( $blog_id, $user_id, $role ) {
	switch_to_blog($blog_id);

	$user = get_userdata( $user_id );

	if ( ! $user ) {
		restore_current_blog();
		return new WP_Error( 'user_does_not_exist', __( 'The requested user does not exist.' ) );
	}

	if ( !get_user_meta($user_id, 'primary_blog', true) ) {
		update_user_meta($user_id, 'primary_blog', $blog_id);
		$details = get_blog_details($blog_id);
		update_user_meta($user_id, 'source_domain', $details->domain);
	}

	$user->set_role($role);

	/**
	 * Fires immediately after a user is added to a site.
	 *
	 * @since MU
	 *
	 * @param int    $user_id User ID.
	 * @param string $role    User role.
	 * @param int    $blog_id Blog ID.
	 */
	do_action( 'add_user_to_blog', $user_id, $role, $blog_id );
	wp_cache_delete( $user_id, 'users' );
	wp_cache_delete( $blog_id . '_user_count', 'blog-details' );
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
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param int    $user_id  ID of the user you're removing.
 * @param int    $blog_id  ID of the blog you're removing the user from.
 * @param string $reassign Optional. A user to whom to reassign posts.
 * @return true|WP_Error
 */
function remove_user_from_blog($user_id, $blog_id = '', $reassign = '') {
	global $wpdb;
	switch_to_blog($blog_id);
	$user_id = (int) $user_id;
	/**
	 * Fires before a user is removed from a site.
	 *
	 * @since MU
	 *
	 * @param int $user_id User ID.
	 * @param int $blog_id Blog ID.
	 */
	do_action( 'remove_user_from_blog', $user_id, $blog_id );

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
	$user = get_userdata( $user_id );
	if ( ! $user ) {
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
		$post_ids = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_author = %d", $user_id ) );
		$link_ids = $wpdb->get_col( $wpdb->prepare( "SELECT link_id FROM $wpdb->links WHERE link_owner = %d", $user_id ) );

		if ( ! empty( $post_ids ) ) {
			$wpdb->query( $wpdb->prepare( "UPDATE $wpdb->posts SET post_author = %d WHERE post_author = %d", $reassign, $user_id ) );
			array_walk( $post_ids, 'clean_post_cache' );
		}

		if ( ! empty( $link_ids ) ) {
			$wpdb->query( $wpdb->prepare( "UPDATE $wpdb->links SET link_owner = %d WHERE link_owner = %d", $reassign, $user_id ) );
			array_walk( $link_ids, 'clean_bookmark_cache' );
		}
	}

	restore_current_blog();

	return true;
}

/**
 * Get the permalink for a post on another blog.
 *
 * @since MU 1.0
 *
 * @param int $blog_id ID of the source blog.
 * @param int $post_id ID of the desired post.
 * @return string The post's permalink
 */
function get_blog_permalink( $blog_id, $post_id ) {
	switch_to_blog( $blog_id );
	$link = get_permalink( $post_id );
	restore_current_blog();

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
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param string $domain
 * @param string $path   Optional. Not required for subdomain installations.
 * @return int 0 if no blog found, otherwise the ID of the matching blog
 */
function get_blog_id_from_url( $domain, $path = '/' ) {
	global $wpdb;

	$domain = strtolower( $domain );
	$path = strtolower( $path );
	$id = wp_cache_get( md5( $domain . $path ), 'blog-id-cache' );

	if ( $id == -1 ) // blog does not exist
		return 0;
	elseif ( $id )
		return (int) $id;

	$id = $wpdb->get_var( $wpdb->prepare( "SELECT blog_id FROM $wpdb->blogs WHERE domain = %s and path = %s /* get_blog_id_from_url */", $domain, $path ) );

	if ( ! $id ) {
		wp_cache_set( md5( $domain . $path ), -1, 'blog-id-cache' );
		return 0;
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
	if ( $banned_names && ! is_array( $banned_names ) )
		$banned_names = explode( "\n", $banned_names );

	$is_email_address_unsafe = false;

	if ( $banned_names && is_array( $banned_names ) ) {
		$banned_names = array_map( 'strtolower', $banned_names );
		$normalized_email = strtolower( $user_email );

		list( $email_local_part, $email_domain ) = explode( '@', $normalized_email );

		foreach ( $banned_names as $banned_domain ) {
			if ( ! $banned_domain )
				continue;

			if ( $email_domain == $banned_domain ) {
				$is_email_address_unsafe = true;
				break;
			}

			$dotted_domain = ".$banned_domain";
			if ( $dotted_domain === substr( $normalized_email, -strlen( $dotted_domain ) ) ) {
				$is_email_address_unsafe = true;
				break;
			}
		}
	}

	/**
	 * Filter whether an email address is unsafe.
	 *
	 * @since 3.5.0
	 *
	 * @param bool   $is_email_address_unsafe Whether the email address is "unsafe". Default false.
	 * @param string $user_email              User email address.
	 */
	return apply_filters( 'is_email_address_unsafe', $is_email_address_unsafe, $user_email );
}

/**
 * Sanitize and validate data required for a user sign-up.
 *
 * Verifies the validity and uniqueness of user names and user email addresses,
 * and checks email addresses against admin-provided domain whitelists and blacklists.
 *
 * The {@see 'wpmu_validate_user_signup'} hook provides an easy way to modify the sign-up
 * process. The value $result, which is passed to the hook, contains both the user-provided
 * info and the error messages created by the function. {@see 'wpmu_validate_user_signup'}
 * allows you to process the data in any way you'd like, and unset the relevant errors if
 * necessary.
 *
 * @since MU
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param string $user_name  The login name provided by the user.
 * @param string $user_email The email provided by the user.
 * @return array Contains username, email, and error messages.
 */
function wpmu_validate_user_signup($user_name, $user_email) {
	global $wpdb;

	$errors = new WP_Error();

	$orig_username = $user_name;
	$user_name = preg_replace( '/\s+/', '', sanitize_user( $user_name, true ) );

	if ( $user_name != $orig_username || preg_match( '/[^a-z0-9]/', $user_name ) ) {
		$errors->add( 'user_name', __( 'Usernames can only contain lowercase letters (a-z) and numbers.' ) );
		$user_name = $orig_username;
	}

	$user_email = sanitize_email( $user_email );

	if ( empty( $user_name ) )
	   	$errors->add('user_name', __( 'Please enter a username.' ) );

	$illegal_names = get_site_option( 'illegal_names' );
	if ( ! is_array( $illegal_names ) ) {
		$illegal_names = array(  'www', 'web', 'root', 'admin', 'main', 'invite', 'administrator' );
		add_site_option( 'illegal_names', $illegal_names );
	}
	if ( in_array( $user_name, $illegal_names ) ) {
		$errors->add( 'user_name',  __( 'Sorry, that username is not allowed.' ) );
	}

	/** This filter is documented in wp-includes/user-functions.php */
	$illegal_logins = (array) apply_filters( 'illegal_user_logins', array() );

	if ( in_array( strtolower( $user_name ), array_map( 'strtolower', $illegal_logins ) ) ) {
		$errors->add( 'user_name',  __( 'Sorry, that username is not allowed.' ) );
	}

	if ( is_email_address_unsafe( $user_email ) )
		$errors->add('user_email',  __('You cannot use that email address to signup. We are having problems with them blocking some of our email. Please use another email provider.'));

	if ( strlen( $user_name ) < 4 )
		$errors->add('user_name',  __( 'Username must be at least 4 characters.' ) );

	if ( strlen( $user_name ) > 60 ) {
		$errors->add( 'user_name', __( 'Username may not be longer than 60 characters.' ) );
	}

	// all numeric?
	if ( preg_match( '/^[0-9]*$/', $user_name ) )
		$errors->add('user_name', __('Sorry, usernames must have letters too!'));

	if ( !is_email( $user_email ) )
		$errors->add('user_email', __( 'Please enter a valid email address.' ) );

	$limited_email_domains = get_site_option( 'limited_email_domains' );
	if ( is_array( $limited_email_domains ) && ! empty( $limited_email_domains ) ) {
		$emaildomain = substr( $user_email, 1 + strpos( $user_email, '@' ) );
		if ( ! in_array( $emaildomain, $limited_email_domains ) ) {
			$errors->add('user_email', __('Sorry, that email address is not allowed!'));
		}
	}

	// Check if the username has been used already.
	if ( username_exists($user_name) )
		$errors->add( 'user_name', __( 'Sorry, that username already exists!' ) );

	// Check if the email address has been used already.
	if ( email_exists($user_email) )
		$errors->add( 'user_email', __( 'Sorry, that email address is already used!' ) );

	// Has someone already signed up for this username?
	$signup = $wpdb->get_row( $wpdb->prepare("SELECT * FROM $wpdb->signups WHERE user_login = %s", $user_name) );
	if ( $signup != null ) {
		$registered_at =  mysql2date('U', $signup->registered);
		$now = current_time( 'timestamp', true );
		$diff = $now - $registered_at;
		// If registered more than two days ago, cancel registration and let this signup go through.
		if ( $diff > 2 * DAY_IN_SECONDS )
			$wpdb->delete( $wpdb->signups, array( 'user_login' => $user_name ) );
		else
			$errors->add('user_name', __('That username is currently reserved but may be available in a couple of days.'));
	}

	$signup = $wpdb->get_row( $wpdb->prepare("SELECT * FROM $wpdb->signups WHERE user_email = %s", $user_email) );
	if ( $signup != null ) {
		$diff = current_time( 'timestamp', true ) - mysql2date('U', $signup->registered);
		// If registered more than two days ago, cancel registration and let this signup go through.
		if ( $diff > 2 * DAY_IN_SECONDS )
			$wpdb->delete( $wpdb->signups, array( 'user_email' => $user_email ) );
		else
			$errors->add('user_email', __('That email address has already been used. Please check your inbox for an activation email. It will become available in a couple of days if you do nothing.'));
	}

	$result = array('user_name' => $user_name, 'orig_username' => $orig_username, 'user_email' => $user_email, 'errors' => $errors);

	/**
	 * Filter the validated user registration details.
	 *
	 * This does not allow you to override the username or email of the user during
	 * registration. The values are solely used for validation and error handling.
	 *
	 * @since MU
	 *
	 * @param array $result {
	 *     The array of user name, email and the error messages.
	 *
	 *     @type string   $user_name     Sanitized and unique username.
	 *     @type string   $orig_username Original username.
	 *     @type string   $user_email    User email address.
	 *     @type WP_Error $errors        WP_Error object containing any errors found.
	 * }
	 */
	return apply_filters( 'wpmu_validate_user_signup', $result );
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
 *
 * @global wpdb   $wpdb
 * @global string $domain
 *
 * @param string         $blogname   The blog name provided by the user. Must be unique.
 * @param string         $blog_title The blog title provided by the user.
 * @param WP_User|string $user       Optional. The user object to check against the new site name.
 * @return array Contains the new site data and error messages.
 */
function wpmu_validate_blog_signup( $blogname, $blog_title, $user = '' ) {
	global $wpdb, $domain;

	$current_site = get_current_site();
	$base = $current_site->path;

	$blog_title = strip_tags( $blog_title );

	$errors = new WP_Error();
	$illegal_names = get_site_option( 'illegal_names' );
	if ( $illegal_names == false ) {
		$illegal_names = array( 'www', 'web', 'root', 'admin', 'main', 'invite', 'administrator' );
		add_site_option( 'illegal_names', $illegal_names );
	}

	/*
	 * On sub dir installs, some names are so illegal, only a filter can
	 * spring them from jail.
	 */
	if ( ! is_subdomain_install() ) {
		$illegal_names = array_merge( $illegal_names, get_subdirectory_reserved_names() );
	}

	if ( empty( $blogname ) )
		$errors->add('blogname', __( 'Please enter a site name.' ) );

	if ( preg_match( '/[^a-z0-9]+/', $blogname ) ) {
		$errors->add( 'blogname', __( 'Site names can only contain lowercase letters (a-z) and numbers.' ) );
	}

	if ( in_array( $blogname, $illegal_names ) )
		$errors->add('blogname',  __( 'That name is not allowed.' ) );

	if ( strlen( $blogname ) < 4 && !is_super_admin() )
		$errors->add('blogname',  __( 'Site name must be at least 4 characters.' ) );

	// do not allow users to create a blog that conflicts with a page on the main blog.
	if ( !is_subdomain_install() && $wpdb->get_var( $wpdb->prepare( "SELECT post_name FROM " . $wpdb->get_blog_prefix( $current_site->blog_id ) . "posts WHERE post_type = 'page' AND post_name = %s", $blogname ) ) )
		$errors->add( 'blogname', __( 'Sorry, you may not use that site name.' ) );

	// all numeric?
	if ( preg_match( '/^[0-9]*$/', $blogname ) )
		$errors->add('blogname', __('Sorry, site names must have letters too!'));

	/**
	 * Filter the new site name during registration.
	 *
	 * The name is the site's subdomain or the site's subdirectory
	 * path depending on the network settings.
	 *
	 * @since MU
	 *
	 * @param string $blogname Site name.
	 */
	$blogname = apply_filters( 'newblogname', $blogname );

	$blog_title = wp_unslash(  $blog_title );

	if ( empty( $blog_title ) )
		$errors->add('blog_title', __( 'Please enter a site title.' ) );

	// Check if the domain/path has been used already.
	if ( is_subdomain_install() ) {
		$mydomain = $blogname . '.' . preg_replace( '|^www\.|', '', $domain );
		$path = $base;
	} else {
		$mydomain = "$domain";
		$path = $base.$blogname.'/';
	}
	if ( domain_exists($mydomain, $path, $current_site->id) )
		$errors->add( 'blogname', __( 'Sorry, that site already exists!' ) );

	if ( username_exists( $blogname ) ) {
		if ( ! is_object( $user ) || ( is_object($user) && ( $user->user_login != $blogname ) ) )
			$errors->add( 'blogname', __( 'Sorry, that site is reserved!' ) );
	}

	// Has someone already signed up for this domain?
	$signup = $wpdb->get_row( $wpdb->prepare("SELECT * FROM $wpdb->signups WHERE domain = %s AND path = %s", $mydomain, $path) ); // TODO: Check email too?
	if ( ! empty($signup) ) {
		$diff = current_time( 'timestamp', true ) - mysql2date('U', $signup->registered);
		// If registered more than two days ago, cancel registration and let this signup go through.
		if ( $diff > 2 * DAY_IN_SECONDS )
			$wpdb->delete( $wpdb->signups, array( 'domain' => $mydomain , 'path' => $path ) );
		else
			$errors->add('blogname', __('That site is currently reserved but may be available in a couple days.'));
	}

	$result = array('domain' => $mydomain, 'path' => $path, 'blogname' => $blogname, 'blog_title' => $blog_title, 'user' => $user, 'errors' => $errors);

	/**
	 * Filter site details and error messages following registration.
	 *
	 * @since MU
	 *
	 * @param array $result {
	 *     Array of domain, path, blog name, blog title, user and error messages.
	 *
	 *     @type string         $domain     Domain for the site.
	 *     @type string         $path       Path for the site. Used in subdirectory installs.
	 *     @type string         $blogname   The unique site name (slug).
	 *     @type string         $blog_title Blog title.
	 *     @type string|WP_User $user       By default, an empty string. A user object if provided.
	 *     @type WP_Error       $errors     WP_Error containing any errors found.
	 * }
	 */
	return apply_filters( 'wpmu_validate_blog_signup', $result );
}

/**
 * Record site signup information for future activation.
 *
 * @since MU
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param string $domain     The requested domain.
 * @param string $path       The requested path.
 * @param string $title      The requested site title.
 * @param string $user       The user's requested login name.
 * @param string $user_email The user's email address.
 * @param array  $meta       By default, contains the requested privacy setting and lang_id.
 */
function wpmu_signup_blog( $domain, $path, $title, $user, $user_email, $meta = array() )  {
	global $wpdb;

	$key = substr( md5( time() . rand() . $domain ), 0, 16 );
	$meta = serialize($meta);

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

	/**
	 * Fires after site signup information has been written to the database.
	 *
	 * @since 4.4.0
	 *
	 * @param string $domain     The requested domain.
	 * @param string $path       The requested path.
	 * @param string $title      The requested site title.
	 * @param string $user       The user's requested login name.
	 * @param string $user_email The user's email address.
	 * @param string $key        The user's activation key
	 * @param array  $meta       By default, contains the requested privacy setting and lang_id.
	 */
	do_action( 'after_signup_site', $domain, $path, $title, $user, $user_email, $key, $meta );
}

/**
 * Record user signup information for future activation.
 *
 * This function is used when user registration is open but
 * new site registration is not.
 *
 * @since MU
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param string $user       The user's requested login name.
 * @param string $user_email The user's email address.
 * @param array  $meta       By default, this is an empty array.
 */
function wpmu_signup_user( $user, $user_email, $meta = array() ) {
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

	/**
	 * Fires after a user's signup information has been written to the database.
	 *
	 * @since 4.4.0
	 *
	 * @param string $user       The user's requested login name.
	 * @param string $user_email The user's email address.
	 * @param string $key        The user's activation key
	 * @param array  $meta       Additional signup meta. By default, this is an empty array.
	 */
	do_action( 'after_signup_user', $user, $user_email, $key, $meta );
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
 * @param string $domain     The new blog domain.
 * @param string $path       The new blog path.
 * @param string $title      The site title.
 * @param string $user       The user's login name.
 * @param string $user_email The user's email address.
 * @param string $key        The activation key created in wpmu_signup_blog()
 * @param array  $meta       By default, contains the requested privacy setting and lang_id.
 * @return bool
 */
function wpmu_signup_blog_notification( $domain, $path, $title, $user, $user_email, $key, $meta = array() ) {
	/**
	 * Filter whether to bypass the new site email notification.
	 *
	 * @since MU
	 *
	 * @param string|bool $domain     Site domain.
	 * @param string      $path       Site path.
	 * @param string      $title      Site title.
	 * @param string      $user       User login name.
	 * @param string      $user_email User email address.
	 * @param string      $key        Activation key created in wpmu_signup_blog().
	 * @param array       $meta       By default, contains the requested privacy setting and lang_id.
	 */
	if ( ! apply_filters( 'wpmu_signup_blog_notification', $domain, $path, $title, $user, $user_email, $key, $meta ) ) {
		return false;
	}

	// Send email with activation link.
	if ( !is_subdomain_install() || get_current_site()->id != 1 )
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
		/**
		 * Filter the message content of the new blog notification email.
		 *
		 * Content should be formatted for transmission via wp_mail().
		 *
		 * @since MU
		 *
		 * @param string $content    Content of the notification email.
		 * @param string $domain     Site domain.
		 * @param string $path       Site path.
		 * @param string $title      Site title.
		 * @param string $user       User login name.
		 * @param string $user_email User email address.
		 * @param string $key        Activation key created in wpmu_signup_blog().
		 * @param array  $meta       By default, contains the requested privacy setting and lang_id.
		 */
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
		/**
		 * Filter the subject of the new blog notification email.
		 *
		 * @since MU
		 *
		 * @param string $subject    Subject of the notification email.
		 * @param string $domain     Site domain.
		 * @param string $path       Site path.
		 * @param string $title      Site title.
		 * @param string $user       User login name.
		 * @param string $user_email User email address.
		 * @param string $key        Activation key created in wpmu_signup_blog().
		 * @param array  $meta       By default, contains the requested privacy setting and lang_id.
		 */
		apply_filters( 'wpmu_signup_blog_notification_subject',
			__( '[%1$s] Activate %2$s' ),
			$domain, $path, $title, $user, $user_email, $key, $meta
		),
		$from_name,
		esc_url( 'http://' . $domain . $path )
	);
	wp_mail( $user_email, wp_specialchars_decode( $subject ), $message, $message_headers );
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
 * @param string $user       The user's login name.
 * @param string $user_email The user's email address.
 * @param string $key        The activation key created in wpmu_signup_user()
 * @param array  $meta       By default, an empty array.
 * @return bool
 */
function wpmu_signup_user_notification( $user, $user_email, $key, $meta = array() ) {
	/**
	 * Filter whether to bypass the email notification for new user sign-up.
	 *
	 * @since MU
	 *
	 * @param string $user       User login name.
	 * @param string $user_email User email address.
	 * @param string $key        Activation key created in wpmu_signup_user().
	 * @param array  $meta       Signup meta data.
	 */
	if ( ! apply_filters( 'wpmu_signup_user_notification', $user, $user_email, $key, $meta ) )
		return false;

	// Send email with activation link.
	$admin_email = get_site_option( 'admin_email' );
	if ( $admin_email == '' )
		$admin_email = 'support@' . $_SERVER['SERVER_NAME'];
	$from_name = get_site_option( 'site_name' ) == '' ? 'WordPress' : esc_html( get_site_option( 'site_name' ) );
	$message_headers = "From: \"{$from_name}\" <{$admin_email}>\n" . "Content-Type: text/plain; charset=\"" . get_option('blog_charset') . "\"\n";
	$message = sprintf(
		/**
		 * Filter the content of the notification email for new user sign-up.
		 *
		 * Content should be formatted for transmission via wp_mail().
		 *
		 * @since MU
		 *
		 * @param string $content    Content of the notification email.
		 * @param string $user       User login name.
		 * @param string $user_email User email address.
		 * @param string $key        Activation key created in wpmu_signup_user().
		 * @param array  $meta       Signup meta data.
		 */
		apply_filters( 'wpmu_signup_user_notification_email',
			__( "To activate your user, please click the following link:\n\n%s\n\nAfter you activate, you will receive *another email* with your login." ),
			$user, $user_email, $key, $meta
		),
		site_url( "wp-activate.php?key=$key" )
	);
	// TODO: Don't hard code activation link.
	$subject = sprintf(
		/**
		 * Filter the subject of the notification email of new user signup.
		 *
		 * @since MU
		 *
		 * @param string $subject    Subject of the notification email.
		 * @param string $user       User login name.
		 * @param string $user_email User email address.
		 * @param string $key        Activation key created in wpmu_signup_user().
		 * @param array  $meta       Signup meta data.
		 */
		apply_filters( 'wpmu_signup_user_notification_subject',
			__( '[%1$s] Activate %2$s' ),
			$user, $user_email, $key, $meta
		),
		$from_name,
		$user
	);
	wp_mail( $user_email, wp_specialchars_decode( $subject ), $message, $message_headers );
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
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param string $key The activation key provided to the user.
 * @return array|WP_Error An array containing information about the activated user and/or blog
 */
function wpmu_activate_signup($key) {
	global $wpdb;

	$signup = $wpdb->get_row( $wpdb->prepare("SELECT * FROM $wpdb->signups WHERE activation_key = %s", $key) );

	if ( empty( $signup ) )
		return new WP_Error( 'invalid_key', __( 'Invalid activation key.' ) );

	if ( $signup->active ) {
		if ( empty( $signup->domain ) )
			return new WP_Error( 'already_active', __( 'The user is already active.' ), $signup );
		else
			return new WP_Error( 'already_active', __( 'The site is already active.' ), $signup );
	}

	$meta = maybe_unserialize($signup->meta);
	$password = wp_generate_password( 12, false );

	$user_id = username_exists($signup->user_login);

	if ( ! $user_id )
		$user_id = wpmu_create_user($signup->user_login, $password, $signup->user_email);
	else
		$user_already_exists = true;

	if ( ! $user_id )
		return new WP_Error('create_user', __('Could not create user'), $signup);

	$now = current_time('mysql', true);

	if ( empty($signup->domain) ) {
		$wpdb->update( $wpdb->signups, array('active' => 1, 'activated' => $now), array('activation_key' => $key) );

		if ( isset( $user_already_exists ) )
			return new WP_Error( 'user_already_exists', __( 'That username is already activated.' ), $signup);

		/**
		 * Fires immediately after a new user is activated.
		 *
		 * @since MU
		 *
		 * @param int   $user_id  User ID.
		 * @param int   $password User password.
		 * @param array $meta     Signup meta data.
		 */
		do_action( 'wpmu_activate_user', $user_id, $password, $meta );
		return array( 'user_id' => $user_id, 'password' => $password, 'meta' => $meta );
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
	/**
	 * Fires immediately after a site is activated.
	 *
	 * @since MU
	 *
	 * @param int    $blog_id       Blog ID.
	 * @param int    $user_id       User ID.
	 * @param int    $password      User password.
	 * @param string $signup_title  Site title.
	 * @param array  $meta          Signup meta data.
	 */
	do_action( 'wpmu_activate_blog', $blog_id, $user_id, $password, $signup->title, $meta );

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
 *
 * @param string $user_name The new user's login name.
 * @param string $password  The new user's password.
 * @param string $email     The new user's email address.
 * @return int|false Returns false on failure, or int $user_id on success
 */
function wpmu_create_user( $user_name, $password, $email ) {
	$user_name = preg_replace( '/\s+/', '', sanitize_user( $user_name, true ) );

	$user_id = wp_create_user( $user_name, $password, $email );
	if ( is_wp_error( $user_id ) )
		return false;

	// Newly created users have no roles or caps until they are added to a blog.
	delete_user_option( $user_id, 'capabilities' );
	delete_user_option( $user_id, 'user_level' );

	/**
	 * Fires immediately after a new user is created.
	 *
	 * @since MU
	 *
	 * @param int $user_id User ID.
	 */
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
 *
 * @param string $domain  The new site's domain.
 * @param string $path    The new site's path.
 * @param string $title   The new site's title.
 * @param int    $user_id The user ID of the new site's admin.
 * @param array  $meta    Optional. Used to set initial site options.
 * @param int    $site_id Optional. Only relevant on multi-network installs.
 * @return int|WP_Error Returns WP_Error object on failure, int $blog_id on success
 */
function wpmu_create_blog( $domain, $path, $title, $user_id, $meta = array(), $site_id = 1 ) {
	$defaults = array( 'public' => 0 );
	$meta = wp_parse_args( $meta, $defaults );

	$domain = preg_replace( '/\s+/', '', sanitize_user( $domain, true ) );

	if ( is_subdomain_install() )
		$domain = str_replace( '@', '', $domain );

	$title = strip_tags( $title );
	$user_id = (int) $user_id;

	if ( empty($path) )
		$path = '/';

	// Check if the domain has been used already. We should return an error message.
	if ( domain_exists($domain, $path, $site_id) )
		return new WP_Error( 'blog_taken', __( 'Sorry, that site already exists!' ) );

	if ( ! wp_installing() ) {
		wp_installing( true );
	}

	if ( ! $blog_id = insert_blog($domain, $path, $site_id) )
		return new WP_Error('insert_blog', __('Could not create site.'));

	switch_to_blog($blog_id);
	install_blog($blog_id, $title);
	wp_install_defaults($user_id);

	add_user_to_blog($blog_id, $user_id, 'administrator');

	foreach ( $meta as $key => $value ) {
		if ( in_array( $key, array( 'public', 'archived', 'mature', 'spam', 'deleted', 'lang_id' ) ) )
			update_blog_status( $blog_id, $key, $value );
		else
			update_option( $key, $value );
	}

	add_option( 'WPLANG', get_site_option( 'WPLANG' ) );
	update_option( 'blog_public', (int) $meta['public'] );

	if ( ! is_super_admin( $user_id ) && ! get_user_meta( $user_id, 'primary_blog', true ) )
		update_user_meta( $user_id, 'primary_blog', $blog_id );

	restore_current_blog();
	/**
	 * Fires immediately after a new site is created.
	 *
	 * @since MU
	 *
	 * @param int    $blog_id Blog ID.
	 * @param int    $user_id User ID.
	 * @param string $domain  Site domain.
	 * @param string $path    Site path.
	 * @param int    $site_id Site ID. Only relevant on multi-network installs.
	 * @param array  $meta    Meta data. Used to set initial site options.
	 */
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

	$msg = sprintf( __( 'New Site: %1$s
URL: %2$s
Remote IP: %3$s

Disable these notifications: %4$s' ), $blogname, $siteurl, wp_unslash( $_SERVER['REMOTE_ADDR'] ), $options_site_url);
	/**
	 * Filter the message body of the new site activation email sent
	 * to the network administrator.
	 *
	 * @since MU
	 *
	 * @param string $msg Email body.
	 */
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

	$user = get_userdata( $user_id );

	$options_site_url = esc_url(network_admin_url('settings.php'));
	$msg = sprintf(__('New User: %1$s
Remote IP: %2$s

Disable these notifications: %3$s'), $user->user_login, wp_unslash( $_SERVER['REMOTE_ADDR'] ), $options_site_url);

	/**
	 * Filter the message body of the new user activation email sent
	 * to the network administrator.
	 *
	 * @since MU
	 *
	 * @param string  $msg  Email body.
	 * @param WP_User $user WP_User instance of the new user.
	 */
	$msg = apply_filters( 'newuser_notify_siteadmin', $msg, $user );
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
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param string $domain  The domain to be checked.
 * @param string $path    The path to be checked.
 * @param int    $site_id Optional. Relevant only on multi-network installs.
 * @return int
 */
function domain_exists($domain, $path, $site_id = 1) {
	global $wpdb;
	$path = trailingslashit( $path );
	$result = $wpdb->get_var( $wpdb->prepare("SELECT blog_id FROM $wpdb->blogs WHERE domain = %s AND path = %s AND site_id = %d", $domain, $path, $site_id) );

	/**
	 * Filter whether a blogname is taken.
	 *
	 * @since 3.5.0
	 *
	 * @param int|null $result  The blog_id if the blogname exists, null otherwise.
	 * @param string   $domain  Domain to be checked.
	 * @param string   $path    Path to be checked.
	 * @param int      $site_id Site ID. Relevant only on multi-network installs.
	 */
	return apply_filters( 'domain_exists', $result, $domain, $path, $site_id );
}

/**
 * Store basic site info in the blogs table.
 *
 * This function creates a row in the wp_blogs table and returns
 * the new blog's ID. It is the first step in creating a new blog.
 *
 * @since MU
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param string $domain  The domain of the new site.
 * @param string $path    The path of the new site.
 * @param int    $site_id Unless you're running a multi-network install, be sure to set this value to 1.
 * @return int|false The ID of the new row
 */
function insert_blog($domain, $path, $site_id) {
	global $wpdb;

	$path = trailingslashit($path);
	$site_id = (int) $site_id;

	$result = $wpdb->insert( $wpdb->blogs, array('site_id' => $site_id, 'domain' => $domain, 'path' => $path, 'registered' => current_time('mysql')) );
	if ( ! $result )
		return false;

	$blog_id = $wpdb->insert_id;
	refresh_blog_details( $blog_id );

	wp_maybe_update_network_site_counts();

	return $blog_id;
}

/**
 * Install an empty blog.
 *
 * Creates the new blog tables and options. If calling this function
 * directly, be sure to use switch_to_blog() first, so that $wpdb
 * points to the new blog.
 *
 * @since MU
 *
 * @global wpdb     $wpdb
 * @global WP_Roles $wp_roles
 *
 * @param int    $blog_id    The value returned by insert_blog().
 * @param string $blog_title The title of the new site.
 */
function install_blog( $blog_id, $blog_title = '' ) {
	global $wpdb, $wp_roles, $current_site;

	// Cast for security
	$blog_id = (int) $blog_id;

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

	$suppress = $wpdb->suppress_errors();
	if ( $wpdb->get_results( "DESCRIBE {$wpdb->posts}" ) )
		die( '<h1>' . __( 'Already Installed' ) . '</h1><p>' . __( 'You appear to have already installed WordPress. To reinstall please clear your old database tables first.' ) . '</p></body></html>' );
	$wpdb->suppress_errors( $suppress );

	$url = get_blogaddress_by_id( $blog_id );

	// Set everything up
	make_db_current_silent( 'blog' );
	populate_options();
	populate_roles();

	// populate_roles() clears previous role definitions so we start over.
	$wp_roles = new WP_Roles();

	$siteurl = $home = untrailingslashit( $url );

	if ( ! is_subdomain_install() ) {

 		if ( 'https' === parse_url( get_site_option( 'siteurl' ), PHP_URL_SCHEME ) ) {
 			$siteurl = set_url_scheme( $siteurl, 'https' );
 		}
 		if ( 'https' === parse_url( get_home_url( $current_site->blog_id ), PHP_URL_SCHEME ) ) {
 			$home = set_url_scheme( $home, 'https' );
 		}

	}

	update_option( 'siteurl', $siteurl );
	update_option( 'home', $home );

	if ( get_site_option( 'ms_files_rewriting' ) )
		update_option( 'upload_path', UPLOADBLOGSDIR . "/$blog_id/files" );
	else
		update_option( 'upload_path', get_blog_option( get_current_site()->blog_id, 'upload_path' ) );

	update_option( 'blogname', wp_unslash( $blog_title ) );
	update_option( 'admin_email', '' );

	// remove all perms
	$table_prefix = $wpdb->get_blog_prefix();
	delete_metadata( 'user', 0, $table_prefix . 'user_level',   null, true ); // delete all
	delete_metadata( 'user', 0, $table_prefix . 'capabilities', null, true ); // delete all
}

/**
 * Set blog defaults.
 *
 * This function creates a row in the wp_blogs table.
 *
 * @since MU
 * @deprecated MU
 * @deprecated Use wp_install_defaults()
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param int $blog_id Ignored in this function.
 * @param int $user_id
 */
function install_blog_defaults($blog_id, $user_id) {
	global $wpdb;

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

	$suppress = $wpdb->suppress_errors();

	wp_install_defaults($user_id);

	$wpdb->suppress_errors( $suppress );
}

/**
 * Notify a user that their blog activation has been successful.
 *
 * Filter 'wpmu_welcome_notification' to disable or bypass.
 *
 * Filter 'update_welcome_email' and 'update_welcome_subject' to
 * modify the content and subject line of the notification email.
 *
 * @since MU
 *
 * @param int    $blog_id
 * @param int    $user_id
 * @param string $password
 * @param string $title    The new blog's title
 * @param array  $meta     Optional. Not used in the default function, but is passed along to hooks for customization.
 * @return bool
 */
function wpmu_welcome_notification( $blog_id, $user_id, $password, $title, $meta = array() ) {
	$current_site = get_current_site();

	/**
	 * Filter whether to bypass the welcome email after site activation.
	 *
	 * Returning false disables the welcome email.
	 *
	 * @since MU
	 *
	 * @param int|bool $blog_id  Blog ID.
	 * @param int      $user_id  User ID.
	 * @param string   $password User password.
	 * @param string   $title    Site title.
	 * @param array    $meta     Signup meta data.
	 */
	if ( ! apply_filters( 'wpmu_welcome_notification', $blog_id, $user_id, $password, $title, $meta ) )
		return false;

	$welcome_email = get_site_option( 'welcome_email' );
	if ( $welcome_email == false ) {
		/* translators: Do not translate USERNAME, SITE_NAME, BLOG_URL, PASSWORD: those are placeholders. */
		$welcome_email = __( 'Howdy USERNAME,

Your new SITE_NAME site has been successfully set up at:
BLOG_URL

You can log in to the administrator account with the following information:

Username: USERNAME
Password: PASSWORD
Log in here: BLOG_URLwp-login.php

We hope you enjoy your new site. Thanks!

--The Team @ SITE_NAME' );
	}

	$url = get_blogaddress_by_id($blog_id);
	$user = get_userdata( $user_id );

	$welcome_email = str_replace( 'SITE_NAME', $current_site->site_name, $welcome_email );
	$welcome_email = str_replace( 'BLOG_TITLE', $title, $welcome_email );
	$welcome_email = str_replace( 'BLOG_URL', $url, $welcome_email );
	$welcome_email = str_replace( 'USERNAME', $user->user_login, $welcome_email );
	$welcome_email = str_replace( 'PASSWORD', $password, $welcome_email );

	/**
	 * Filter the content of the welcome email after site activation.
	 *
	 * Content should be formatted for transmission via wp_mail().
	 *
	 * @since MU
	 *
	 * @param string $welcome_email Message body of the email.
	 * @param int    $blog_id       Blog ID.
	 * @param int    $user_id       User ID.
	 * @param string $password      User password.
	 * @param string $title         Site title.
	 * @param array  $meta          Signup meta data.
	 */
	$welcome_email = apply_filters( 'update_welcome_email', $welcome_email, $blog_id, $user_id, $password, $title, $meta );
	$admin_email = get_site_option( 'admin_email' );

	if ( $admin_email == '' )
		$admin_email = 'support@' . $_SERVER['SERVER_NAME'];

	$from_name = get_site_option( 'site_name' ) == '' ? 'WordPress' : esc_html( get_site_option( 'site_name' ) );
	$message_headers = "From: \"{$from_name}\" <{$admin_email}>\n" . "Content-Type: text/plain; charset=\"" . get_option('blog_charset') . "\"\n";
	$message = $welcome_email;

	if ( empty( $current_site->site_name ) )
		$current_site->site_name = 'WordPress';

	/**
	 * Filter the subject of the welcome email after site activation.
	 *
	 * @since MU
	 *
	 * @param string $subject Subject of the email.
	 */
	$subject = apply_filters( 'update_welcome_subject', sprintf( __( 'New %1$s Site: %2$s' ), $current_site->site_name, wp_unslash( $title ) ) );
	wp_mail( $user->user_email, wp_specialchars_decode( $subject ), $message, $message_headers );
	return true;
}

/**
 * Notify a user that their account activation has been successful.
 *
 * Filter 'wpmu_welcome_user_notification' to disable or bypass.
 *
 * Filter 'update_welcome_user_email' and 'update_welcome_user_subject' to
 * modify the content and subject line of the notification email.
 *
 * @since MU
 *
 * @param int    $user_id
 * @param string $password
 * @param array  $meta     Optional. Not used in the default function, but is passed along to hooks for customization.
 * @return bool
 */
function wpmu_welcome_user_notification( $user_id, $password, $meta = array() ) {
	$current_site = get_current_site();

	/**
 	 * Filter whether to bypass the welcome email after user activation.
	 *
	 * Returning false disables the welcome email.
	 *
	 * @since MU
	 *
	 * @param int    $user_id  User ID.
	 * @param string $password User password.
	 * @param array  $meta     Signup meta data.
	 */
	if ( ! apply_filters( 'wpmu_welcome_user_notification', $user_id, $password, $meta ) )
		return false;

	$welcome_email = get_site_option( 'welcome_user_email' );

	$user = get_userdata( $user_id );

	/**
	 * Filter the content of the welcome email after user activation.
	 *
	 * Content should be formatted for transmission via wp_mail().
	 *
	 * @since MU
	 *
	 * @param type   $welcome_email The message body of the account activation success email.
	 * @param int    $user_id       User ID.
	 * @param string $password      User password.
	 * @param array  $meta          Signup meta data.
	 */
	$welcome_email = apply_filters( 'update_welcome_user_email', $welcome_email, $user_id, $password, $meta );
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

	/**
	 * Filter the subject of the welcome email after user activation.
	 *
	 * @since MU
	 *
	 * @param string $subject Subject of the email.
	 */
	$subject = apply_filters( 'update_welcome_user_subject', sprintf( __( 'New %1$s User: %2$s' ), $current_site->site_name, $user->user_login) );
	wp_mail( $user->user_email, wp_specialchars_decode( $subject ), $message, $message_headers );
	return true;
}

/**
 * Get the current site info.
 *
 * Returns an object containing the 'id', 'domain', 'path', and 'site_name'
 * properties of the site being viewed.
 *
 * @see wpmu_current_site()
 *
 * @since MU
 *
 * @global object $current_site
 *
 * @return object
 */
function get_current_site() {
	global $current_site;
	return $current_site;
}

/**
 * Get a user's most recent post.
 *
 * Walks through each of a user's blogs to find the post with
 * the most recent post_date_gmt.
 *
 * @since MU
 *
 * @global wpdb $wpdb WordPress database abstraction object.
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
 *
 * @param string $directory Full path of a directory.
 * @return int Size of the directory in MB.
 */
function get_dirsize( $directory ) {
	$dirsize = get_transient( 'dirsize_cache' );
	if ( is_array( $dirsize ) && isset( $dirsize[ $directory ][ 'size' ] ) )
		return $dirsize[ $directory ][ 'size' ];

	if ( ! is_array( $dirsize ) )
		$dirsize = array();

	// Exclude individual site directories from the total when checking the main site,
	// as they are subdirectories and should not be counted.
	if ( is_main_site() ) {
		$dirsize[ $directory ][ 'size' ] = recurse_dirsize( $directory, $directory . '/sites' );
	} else {
		$dirsize[ $directory ][ 'size' ] = recurse_dirsize( $directory );
	}

	set_transient( 'dirsize_cache', $dirsize, HOUR_IN_SECONDS );
	return $dirsize[ $directory ][ 'size' ];
}

/**
 * Get the size of a directory recursively.
 *
 * Used by get_dirsize() to get a directory's size when it contains
 * other directories.
 *
 * @since MU
 * @since 4.3.0 $exclude parameter added.
 *
 * @param string $directory Full path of a directory.
 * @param string $exclude   Optional. Full path of a subdirectory to exclude from the total.
 * @return int|false Size in MB if a valid directory. False if not.
 */
function recurse_dirsize( $directory, $exclude = null ) {
	$size = 0;

	$directory = untrailingslashit( $directory );

	if ( ! file_exists( $directory ) || ! is_dir( $directory ) || ! is_readable( $directory ) || $directory === $exclude ) {
		return false;
	}

	if ($handle = opendir($directory)) {
		while(($file = readdir($handle)) !== false) {
			$path = $directory.'/'.$file;
			if ($file != '.' && $file != '..') {
				if (is_file($path)) {
					$size += filesize($path);
				} elseif (is_dir($path)) {
					$handlesize = recurse_dirsize( $path, $exclude );
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
	$site_exts = explode( ' ', get_site_option( 'upload_filetypes', 'jpg jpeg png gif' ) );
	$site_mimes = array();
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
 * are published or unpublished to make sure the count stays current.
 *
 * @since MU
 *
 * @global wpdb $wpdb WordPress database abstraction object.
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
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param int $blog_id
 * @param int $user_id
 */
function wpmu_log_new_registrations( $blog_id, $user_id ) {
	global $wpdb;
	$user = get_userdata( (int) $user_id );
	if ( $user )
		$wpdb->insert( $wpdb->registration_log, array('email' => $user->user_email, 'IP' => preg_replace( '/[^0-9., ]/', '', wp_unslash( $_SERVER['REMOTE_ADDR'] ) ), 'blog_id' => $blog_id, 'date_registered' => current_time('mysql')) );
}

/**
 * Maintains a canonical list of terms by syncing terms created for each blog with the global terms table.
 *
 * @since 3.0.0
 *
 * @see term_id_filter
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 * @staticvar int $global_terms_recurse
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
		$local_id = $wpdb->get_var( $wpdb->prepare( "SELECT term_id FROM $wpdb->terms WHERE term_id = %d", $global_id ) );
		if ( null != $local_id ) {
			global_terms( $local_id );
			if ( 10 < $global_terms_recurse ) {
				$global_id = $term_id;
			}
		}
	}

	if ( $global_id != $term_id ) {
		if ( get_option( 'default_category' ) == $term_id )
			update_option( 'default_category', $global_id );

		$wpdb->update( $wpdb->terms, array('term_id' => $global_id), array('term_id' => $term_id) );
		$wpdb->update( $wpdb->term_taxonomy, array('term_id' => $global_id), array('term_id' => $term_id) );
		$wpdb->update( $wpdb->term_taxonomy, array('parent' => $global_id), array('parent' => $term_id) );

		clean_term_cache($term_id);
	}
	if ( $recurse_start )
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
	return array( get_current_site()->domain );
}

/**
 * Check whether an upload is too big.
 *
 * @since MU
 *
 * @blessed
 *
 * @param array $upload
 * @return string|array If the upload is under the size limit, $upload is returned. Otherwise returns an error message.
 */
function upload_is_file_too_big( $upload ) {
	if ( ! is_array( $upload ) || defined( 'WP_IMPORTING' ) || get_site_option( 'upload_space_check_disabled' ) )
		return $upload;

	if ( strlen( $upload['bits'] )  > ( KB_IN_BYTES * get_site_option( 'fileupload_maxk', 1500 ) ) ) {
		return sprintf( __( 'This file is too big. Files must be less than %d KB in size.' ) . '<br />', get_site_option( 'fileupload_maxk', 1500 ) );
	}

	return $upload;
}

/**
 * Add a nonce field to the signup page.
 *
 * @since MU
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
 *
 * @param array $result
 * @return array
 */
function signup_nonce_check( $result ) {
	if ( !strpos( $_SERVER[ 'PHP_SELF' ], 'wp-signup.php' ) )
		return $result;

	if ( wp_create_nonce('signup_form_' . $_POST[ 'signup_form_id' ]) != $_POST['_signup_form'] )
		wp_die( __( 'Please try again.' ) );

	return $result;
}

/**
 * Correct 404 redirects when NOBLOGREDIRECT is defined.
 *
 * @since MU
 */
function maybe_redirect_404() {
	/**
	 * Filter the redirect URL for 404s on the main site.
	 *
	 * The filter is only evaluated if the NOBLOGREDIRECT constant is defined.
	 *
	 * @since 3.0.0
	 *
	 * @param string $no_blog_redirect The redirect URL defined in NOBLOGREDIRECT.
	 */
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
 */
function maybe_add_existing_user_to_blog() {
	if ( false === strpos( $_SERVER[ 'REQUEST_URI' ], '/newbloguser/' ) )
		return;

	$parts = explode( '/', $_SERVER[ 'REQUEST_URI' ] );
	$key = array_pop( $parts );

	if ( $key == '' )
		$key = array_pop( $parts );

	$details = get_option( 'new_user_' . $key );
	if ( !empty( $details ) )
		delete_option( 'new_user_' . $key );

	if ( empty( $details ) || is_wp_error( add_existing_user_to_blog( $details ) ) )
		wp_die( sprintf(__('An error occurred adding you to this site. Back to the <a href="%s">homepage</a>.'), home_url() ) );

	wp_die( sprintf( __( 'You have been added to this site. Please visit the <a href="%s">homepage</a> or <a href="%s">log in</a> using your username and password.' ), home_url(), admin_url() ), __( 'WordPress &rsaquo; Success' ), array( 'response' => 200 ) );
}

/**
 * Add a user to a blog based on details from maybe_add_existing_user_to_blog().
 *
 * @since MU
 *
 * @global int $blog_id
 *
 * @param array $details
 * @return true|WP_Error|void
 */
function add_existing_user_to_blog( $details = false ) {
	global $blog_id;

	if ( is_array( $details ) ) {
		$result = add_user_to_blog( $blog_id, $details[ 'user_id' ], $details[ 'role' ] );
		/**
		 * Fires immediately after an existing user is added to a site.
		 *
		 * @since MU
		 *
		 * @param int   $user_id User ID.
		 * @param mixed $result  True on success or a WP_Error object if the user doesn't exist.
		 */
		do_action( 'added_existing_user', $details['user_id'], $result );
		return $result;
	}
}

/**
 * Add a newly created user to the appropriate blog
 *
 * To add a user in general, use add_user_to_blog(). This function
 * is specifically hooked into the wpmu_activate_user action.
 *
 * @since MU
 * @see add_user_to_blog()
 *
 * @param int   $user_id
 * @param mixed $password Ignored.
 * @param array $meta
 */
function add_new_user_to_blog( $user_id, $password, $meta ) {
	if ( !empty( $meta[ 'add_to_blog' ] ) ) {
		$blog_id = $meta[ 'add_to_blog' ];
		$role = $meta[ 'new_role' ];
		remove_user_from_blog($user_id, get_current_site()->blog_id); // remove user from main blog.
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
	$phpmailer->Hostname = get_current_site()->domain;
}

/**
 * Check to see whether a user is marked as a spammer, based on user login.
 *
 * @since MU
 *
 * @param string|WP_User $user Optional. Defaults to current user. WP_User object,
 * 	                           or user login name as a string.
 * @return bool
 */
function is_user_spammy( $user = null ) {
    if ( ! ( $user instanceof WP_User ) ) {
		if ( $user ) {
			$user = get_user_by( 'login', $user );
		} else {
			$user = wp_get_current_user();
		}
	}

	return $user && isset( $user->spam ) && 1 == $user->spam;
}

/**
 * Update this blog's 'public' setting in the global blogs table.
 *
 * Public blogs have a setting of 1, private blogs are 0.
 *
 * @since MU
 *
 * @param int $old_value
 * @param int $value     The new public value
 */
function update_blog_public( $old_value, $value ) {
	update_blog_status( get_current_blog_id(), 'public', (int) $value );
}

/**
 * Check whether a usermeta key has to do with the current blog.
 *
 * @since MU
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param string $key
 * @param int    $user_id Optional. Defaults to current user.
 * @param int    $blog_id Optional. Defaults to current blog.
 * @return bool
 */
function is_user_option_local( $key, $user_id = 0, $blog_id = 0 ) {
	global $wpdb;

	$current_user = wp_get_current_user();
	if ( $blog_id == 0 ) {
		$blog_id = $wpdb->blogid;
	}
	$local_key = $wpdb->get_blog_prefix( $blog_id ) . $key;

	return isset( $current_user->$local_key );
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
	return ( $registration == 'all' || $registration == 'user' );
}

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
		remove_filter( 'site_option_welcome_user_email', 'welcome_user_msg_filter' );

		/* translators: Do not translate USERNAME, PASSWORD, LOGINLINK, SITE_NAME: those are placeholders. */
		$text = __( 'Howdy USERNAME,

Your new account is set up.

You can log in with the following information:
Username: USERNAME
Password: PASSWORD
LOGINLINK

Thanks!

--The Team @ SITE_NAME' );
		update_site_option( 'welcome_user_email', $text );
	}
	return $text;
}

/**
 * Whether to force SSL on content.
 *
 * @since 2.8.5
 *
 * @staticvar bool $forced_content
 *
 * @param bool $force
 * @return bool True if forced, false if not forced.
 */
function force_ssl_content( $force = '' ) {
	static $forced_content = false;

	if ( '' != $force ) {
		$old_forced = $forced_content;
		$forced_content = $force;
		return $old_forced;
	}

	return $forced_content;
}

/**
 * Formats a URL to use https.
 *
 * Useful as a filter.
 *
 * @since 2.8.5
 *
 * @param string URL
 * @return string URL with https as the scheme
 */
function filter_SSL( $url ) {
	if ( ! is_string( $url ) )
		return get_bloginfo( 'url' ); // Return home blog url with proper scheme

	if ( force_ssl_content() && is_ssl() )
		$url = set_url_scheme( $url, 'https' );

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

	if ( ! wp_next_scheduled('update_network_counts') && ! wp_installing() )
		wp_schedule_event(time(), 'twicedaily', 'update_network_counts');
}

/**
 *  Update the network-wide counts for the current network.
 *
 *  @since 3.1.0
 */
function wp_update_network_counts() {
	wp_update_network_user_counts();
	wp_update_network_site_counts();
}

/**
 * Update the count of sites for the current network.
 *
 * If enabled through the 'enable_live_network_counts' filter, update the sites count
 * on a network when a site is created or its status is updated.
 *
 * @since 3.7.0
 */
function wp_maybe_update_network_site_counts() {
	$is_small_network = ! wp_is_large_network( 'sites' );

	/**
	 * Filter whether to update network site or user counts when a new site is created.
	 *
	 * @since 3.7.0
	 *
	 * @see wp_is_large_network()
	 *
	 * @param bool   $small_network Whether the network is considered small.
	 * @param string $context       Context. Either 'users' or 'sites'.
	 */
	if ( ! apply_filters( 'enable_live_network_counts', $is_small_network, 'sites' ) )
		return;

	wp_update_network_site_counts();
}

/**
 * Update the network-wide users count.
 *
 * If enabled through the 'enable_live_network_counts' filter, update the users count
 * on a network when a user is created or its status is updated.
 *
 * @since 3.7.0
 */
function wp_maybe_update_network_user_counts() {
	$is_small_network = ! wp_is_large_network( 'users' );

	/** This filter is documented in wp-includes/ms-functions.php */
	if ( ! apply_filters( 'enable_live_network_counts', $is_small_network, 'users' ) )
		return;

	wp_update_network_user_counts();
}

/**
 * Update the network-wide site count.
 *
 * @since 3.7.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 */
function wp_update_network_site_counts() {
	global $wpdb;

	$count = $wpdb->get_var( $wpdb->prepare("SELECT COUNT(blog_id) as c FROM $wpdb->blogs WHERE site_id = %d AND spam = '0' AND deleted = '0' and archived = '0'", $wpdb->siteid) );
	update_site_option( 'blog_count', $count );
}

/**
 * Update the network-wide user count.
 *
 * @since 3.7.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 */
function wp_update_network_user_counts() {
	global $wpdb;

	$count = $wpdb->get_var( "SELECT COUNT(ID) as c FROM $wpdb->users WHERE spam = '0' AND deleted = '0'" );
	update_site_option( 'user_count', $count );
}

/**
 * Returns the space used by the current blog.
 *
 * @since 3.5.0
 *
 * @return int Used space in megabytes
 */
function get_space_used() {
	/**
	 * Filter the amount of storage space used by the current site.
	 *
	 * @since 3.5.0
	 *
	 * @param int|bool $space_used The amount of used space, in megabytes. Default false.
	 */
	$space_used = apply_filters( 'pre_get_space_used', false );
	if ( false === $space_used ) {
		$upload_dir = wp_upload_dir();
		$space_used = get_dirsize( $upload_dir['basedir'] ) / MB_IN_BYTES;
	}

	return $space_used;
}

/**
 * Returns the upload quota for the current blog.
 *
 * @since MU
 *
 * @return int Quota in megabytes
 */
function get_space_allowed() {
	$space_allowed = get_option( 'blog_upload_space' );

	if ( ! is_numeric( $space_allowed ) )
		$space_allowed = get_site_option( 'blog_upload_space' );

	if ( ! is_numeric( $space_allowed ) )
		$space_allowed = 100;

	/**
	 * Filter the upload quota for the current site.
	 *
	 * @since 3.7.0
	 *
	 * @param int $space_allowed Upload quota in megabytes for the current blog.
	 */
	return apply_filters( 'get_space_allowed', $space_allowed );
}

/**
 * Determines if there is any upload space left in the current blog's quota.
 *
 * @since 3.0.0
 *
 * @return int of upload space available in bytes
 */
function get_upload_space_available() {
	$allowed = get_space_allowed();
	if ( $allowed < 0 ) {
		$allowed = 0;
	}
	$space_allowed = $allowed * MB_IN_BYTES;
	if ( get_site_option( 'upload_space_check_disabled' ) )
		return $space_allowed;

	$space_used = get_space_used() * MB_IN_BYTES;

	if ( ( $space_allowed - $space_used ) <= 0 )
		return 0;

	return $space_allowed - $space_used;
}

/**
 * Determines if there is any upload space left in the current blog's quota.
 *
 * @since 3.0.0
 * @return bool True if space is available, false otherwise.
 */
function is_upload_space_available() {
	if ( get_site_option( 'upload_space_check_disabled' ) )
		return true;

	return (bool) get_upload_space_available();
}

/**
 * @since 3.0.0
 *
 * @return int of upload size limit in bytes
 */
function upload_size_limit_filter( $size ) {
	$fileupload_maxk = KB_IN_BYTES * get_site_option( 'fileupload_maxk', 1500 );
	if ( get_site_option( 'upload_space_check_disabled' ) )
		return min( $size, $fileupload_maxk );

	return min( $size, $fileupload_maxk, get_upload_space_available() );
}

/**
 * Whether or not we have a large network.
 *
 * The default criteria for a large network is either more than 10,000 users or more than 10,000 sites.
 * Plugins can alter this criteria using the 'wp_is_large_network' filter.
 *
 * @since 3.3.0
 * @param string $using 'sites or 'users'. Default is 'sites'.
 * @return bool True if the network meets the criteria for large. False otherwise.
 */
function wp_is_large_network( $using = 'sites' ) {
	if ( 'users' == $using ) {
		$count = get_user_count();
		/**
		 * Filter whether the network is considered large.
		 *
		 * @since 3.3.0
		 *
		 * @param bool   $is_large_network Whether the network has more than 10000 users or sites.
		 * @param string $component        The component to count. Accepts 'users', or 'sites'.
		 * @param int    $count            The count of items for the component.
		 */
		return apply_filters( 'wp_is_large_network', $count > 10000, 'users', $count );
	}

	$count = get_blog_count();
	/** This filter is documented in wp-includes/ms-functions.php */
	return apply_filters( 'wp_is_large_network', $count > 10000, 'sites', $count );
}


/**
 * Return an array of sites for a network or networks.
 *
 * @since 3.7.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param array $args {
 *     Array of default arguments. Optional.
 *
 *     @type int|array $network_id A network ID or array of network IDs. Set to null to retrieve sites
 *                                 from all networks. Defaults to current network ID.
 *     @type int       $public     Retrieve public or non-public sites. Default null, for any.
 *     @type int       $archived   Retrieve archived or non-archived sites. Default null, for any.
 *     @type int       $mature     Retrieve mature or non-mature sites. Default null, for any.
 *     @type int       $spam       Retrieve spam or non-spam sites. Default null, for any.
 *     @type int       $deleted    Retrieve deleted or non-deleted sites. Default null, for any.
 *     @type int       $limit      Number of sites to limit the query to. Default 100.
 *     @type int       $offset     Exclude the first x sites. Used in combination with the $limit parameter. Default 0.
 * }
 * @return array An empty array if the install is considered "large" via wp_is_large_network(). Otherwise,
 *               an associative array of site data arrays, each containing the site (network) ID, blog ID,
 *               site domain and path, dates registered and modified, and the language ID. Also, boolean
 *               values for whether the site is public, archived, mature, spam, and/or deleted.
 */
function wp_get_sites( $args = array() ) {
	global $wpdb;

	if ( wp_is_large_network() )
		return array();

	$defaults = array(
		'network_id' => $wpdb->siteid,
		'public'     => null,
		'archived'   => null,
		'mature'     => null,
		'spam'       => null,
		'deleted'    => null,
		'limit'      => 100,
		'offset'     => 0,
	);

	$args = wp_parse_args( $args, $defaults );

	$query = "SELECT * FROM $wpdb->blogs WHERE 1=1 ";

	if ( isset( $args['network_id'] ) && ( is_array( $args['network_id'] ) || is_numeric( $args['network_id'] ) ) ) {
		$network_ids = implode( ',', wp_parse_id_list( $args['network_id'] ) );
		$query .= "AND site_id IN ($network_ids) ";
	}

	if ( isset( $args['public'] ) )
		$query .= $wpdb->prepare( "AND public = %d ", $args['public'] );

	if ( isset( $args['archived'] ) )
		$query .= $wpdb->prepare( "AND archived = %d ", $args['archived'] );

	if ( isset( $args['mature'] ) )
		$query .= $wpdb->prepare( "AND mature = %d ", $args['mature'] );

	if ( isset( $args['spam'] ) )
		$query .= $wpdb->prepare( "AND spam = %d ", $args['spam'] );

	if ( isset( $args['deleted'] ) )
		$query .= $wpdb->prepare( "AND deleted = %d ", $args['deleted'] );

	if ( isset( $args['limit'] ) && $args['limit'] ) {
		if ( isset( $args['offset'] ) && $args['offset'] )
			$query .= $wpdb->prepare( "LIMIT %d , %d ", $args['offset'], $args['limit'] );
		else
			$query .= $wpdb->prepare( "LIMIT %d ", $args['limit'] );
	}

	$site_results = $wpdb->get_results( $query, ARRAY_A );

	return $site_results;
}

/**
 * Retrieves a list of reserved site on a sub-directory Multisite install.
 *
 * @since 4.4.0
 *
 * @return array $names Array of reserved subdirectory names.
 */
function get_subdirectory_reserved_names() {
	$names = array(
		'page', 'comments', 'blog', 'files', 'feed', 'wp-admin',
		'wp-content', 'wp-includes', 'wp-json', 'embed'
	);

	/**
	 * Filter reserved site names on a sub-directory Multisite install.
	 *
	 * @since 3.0.0
	 * @since 4.4.0 'wp-admin', 'wp-content', 'wp-includes', 'wp-json', and 'embed' were added
	 *              to the reserved names list.
	 *
	 * @param array $subdirectory_reserved_names Array of reserved names.
	 */
	return apply_filters( 'subdirectory_reserved_names', $names );
}
