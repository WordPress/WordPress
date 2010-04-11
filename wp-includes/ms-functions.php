<?php
/**
 * Multi-site WordPress API
 *
 * @package WordPress
 * @subpackage Multisite
 * @since 3.0.0
 */

function get_sitestats() {
	global $wpdb;

	$stats['blogs'] = get_blog_count();

	$count_ts = get_site_option( 'user_count_ts' );
	if ( time() - $count_ts > 3600 ) {
		$count = $wpdb->get_var( "SELECT COUNT(ID) FROM $wpdb->users" );
		update_site_option( 'user_count', $count );
		update_site_option( 'user_count_ts', time() );
	} else {
		$count = get_site_option( 'user_count' );
	}
	$stats['users'] = $count;
	return $stats;
}

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

function get_blogs_of_user( $id, $all = false ) {
	global $wpdb;

	$cache_suffix = $all ? '_all' : '_short';
	$return = wp_cache_get( 'blogs_of_user_' . $id . $cache_suffix, 'users' );
	if ( $return )
		return apply_filters( 'get_blogs_of_user', $return, $id, $all );

	$user = get_userdata( (int) $id );
	if ( !$user )
		return false;

	$blogs = $match = array();
	$prefix_length = strlen($wpdb->base_prefix);
	foreach ( (array) $user as $key => $value ) {
		if ( $prefix_length && substr($key, 0, $prefix_length) != $wpdb->base_prefix )
			continue;
		if ( substr($key, -12, 12) != 'capabilities' )
			continue;
		if ( preg_match( '/^' . $wpdb->base_prefix . '((\d+)_)?capabilities$/', $key, $match ) ) {
			if ( count( $match ) > 2 )
				$blog_id = $match[ 2 ];
			else
				$blog_id = 1;
			$blog = get_blog_details( $blog_id );
			if ( $blog && isset( $blog->domain ) && ( $all == true || $all == false && ( $blog->archived == 0 && $blog->spam == 0 && $blog->deleted == 0 ) ) ) {
				$blogs[ $blog_id ]->userblog_id	= $blog_id;
				$blogs[ $blog_id ]->blogname		= $blog->blogname;
				$blogs[ $blog_id ]->domain		= $blog->domain;
				$blogs[ $blog_id ]->path			= $blog->path;
				$blogs[ $blog_id ]->site_id		= $blog->site_id;
				$blogs[ $blog_id ]->siteurl		= $blog->siteurl;
			}
		}
	}

	wp_cache_add( 'blogs_of_user_' . $id . $cache_suffix, $blogs, 'users', 5 );
	return apply_filters( 'get_blogs_of_user', $blogs, $id, $all );
}

function get_active_blog_for_user( $user_id ) { // get an active blog for user - either primary blog or from blogs list
	global $wpdb;
	$blogs = get_blogs_of_user( $user_id );
	if ( empty( $blogs ) ) {
		$details = get_dashboard_blog();
		add_user_to_blog( $details->blog_id, $user_id, 'subscriber' );
		update_user_meta( $user_id, 'primary_blog', $details->blog_id );
		wp_cache_delete( $user_id, 'users' );
		return $details;
	}

	$primary_blog = get_user_meta( $user_id, 'primary_blog', true );
	$details = get_dashboard_blog();
	if ( $primary_blog ) {
		$blogs = get_blogs_of_user( $user_id );
		if ( isset( $blogs[ $primary_blog ] ) == false ) {
			add_user_to_blog( $details->blog_id, $user_id, 'subscriber' );
			update_user_meta( $user_id, 'primary_blog', $details->blog_id );
			wp_cache_delete( $user_id, 'users' );
		} else {
			$details = get_blog_details( $primary_blog );
		}
	} else {
		add_user_to_blog( $details->blog_id, $user_id, 'subscriber' ); // Add subscriber permission for dashboard blog
		update_user_meta( $user_id, 'primary_blog', $details->blog_id );
	}

	if ( ( is_object( $details ) == false ) || ( is_object( $details ) && $details->archived == 1 || $details->spam == 1 || $details->deleted == 1 ) ) {
		$blogs = get_blogs_of_user( $user_id, true ); // if a user's primary blog is shut down, check their other blogs.
		$ret = false;
		if ( is_array( $blogs ) && count( $blogs ) > 0 ) {
			foreach ( (array) $blogs as $blog_id => $blog ) {
				if ( $blog->site_id != $wpdb->siteid )
					continue;
				$details = get_blog_details( $blog_id );
				if ( is_object( $details ) && $details->archived == 0 && $details->spam == 0 && $details->deleted == 0 ) {
					$ret = $blog;
					$changed = false;
					if ( get_user_meta( $user_id , 'primary_blog', true ) != $blog_id ) {
						update_user_meta( $user_id, 'primary_blog', $blog_id );
						$changed = true;
					}
					if ( !get_user_meta($user_id , 'source_domain', true) ) {
						update_user_meta( $user_id, 'source_domain', $blog->domain );
						$changed = true;
					}
					if ( $changed )
						wp_cache_delete( $user_id, 'users' );
					break;
				}
			}
		} else {
			// Should never get here
			$dashboard_blog = get_dashboard_blog();
			add_user_to_blog( $dashboard_blog->blog_id, $user_id, 'subscriber' ); // Add subscriber permission for dashboard blog
			update_user_meta( $user_id, 'primary_blog', $dashboard_blog->blog_id );
			return $dashboard_blog;
		}
		return $ret;
	} else {
		return $details;
	}
}

function is_user_member_of_blog( $user_id, $blog_id = 0 ) {
	$user_id = (int) $user_id;
	$blog_id = (int) $blog_id;

	if ( $blog_id == 0 ) {
		global $wpdb;
		$blog_id = $wpdb->blogid;
	}

	$blogs = get_blogs_of_user( $user_id );
	if ( is_array( $blogs ) )
		return array_key_exists( $blog_id, $blogs );
	else
		return false;
}

function get_most_active_blogs( $num = 10, $display = true ) {
	$most_active = get_site_option( "most_active" );
	$update = false;
	if ( is_array( $most_active ) ) {
		if ( ( $most_active['time'] + 60 ) < time() ) { // cache for 60 seconds.
			$update = true;
		}
	} else {
		$update = true;
	}

	if ( $update == true ) {
		unset( $most_active );
		$blogs = get_blog_list( 0, 'all', false ); // $blog_id -> $details
		if ( is_array( $blogs ) ) {
			reset( $blogs );
			foreach ( (array) $blogs as $key => $details ) {
				$most_active[ $details['blog_id'] ] = $details['postcount'];
				$blog_list[ $details['blog_id'] ] = $details; // array_slice() removes keys!!
			}
			arsort( $most_active );
			reset( $most_active );
			foreach ( (array) $most_active as $key => $details )
				$t[ $key ] = $blog_list[ $key ];

			unset( $most_active );
			$most_active = $t;
		}
		update_site_option( "most_active", $most_active );
	}

	if ( $display == true ) {
		if ( is_array( $most_active ) ) {
			reset( $most_active );
			foreach ( (array) $most_active as $key => $details ) {
				$url = esc_url("http://" . $details['domain'] . $details['path']);
				echo "<li>" . $details['postcount'] . " <a href='$url'>$url</a></li>";
			}
		}
	}
	return array_slice( $most_active, 0, $num );
}

function get_user_count() {
	global $wpdb;

	$count_ts = get_site_option( "user_count_ts" );
	if ( time() - $count_ts > 3600 ) {
		$count = $wpdb->get_var( $wpdb->prepare("SELECT COUNT(ID) as c FROM $wpdb->users WHERE spam = '0' AND deleted = '0'") );
		update_site_option( "user_count", $count );
		update_site_option( "user_count_ts", time() );
	}

	$count = get_site_option( "user_count" );

	return $count;
}

function get_blog_count( $id = 0 ) {
	global $wpdb;

	if ( $id == 0 )
		$id = $wpdb->siteid;

	$count_ts = get_site_option( "blog_count_ts" );
	if ( time() - $count_ts > 3600 ) {
		$count = $wpdb->get_var( $wpdb->prepare("SELECT COUNT(blog_id) as c FROM $wpdb->blogs WHERE site_id = %d AND spam = '0' AND deleted = '0' and archived = '0'", $id) );
		update_site_option( "blog_count", $count );
		update_site_option( "blog_count_ts", time() );
	}

	$count = get_site_option( "blog_count" );

	return $count;
}

function get_blog_post( $blog_id, $post_id ) {
	global $wpdb;

	$key = $blog_id . "-" . $post_id;
	$post = wp_cache_get( $key, "global-posts" );
	if ( $post == false ) {
		$post = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM " . $wpdb->get_blog_prefix( $blog_id ) . "posts WHERE ID = %d", $post_id ) );
		wp_cache_add( $key, $post, "global-posts" );
	}

	return $post;
}

function add_user_to_blog( $blog_id, $user_id, $role ) {
	switch_to_blog($blog_id);

	$user = new WP_User($user_id);

	if ( empty($user) )
		return new WP_Error('user_does_not_exist', __('That user does not exist.'));

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
}

function create_empty_blog( $domain, $path, $weblog_title, $site_id = 1 ) {
	$domain			= addslashes( $domain );
	$weblog_title	= addslashes( $weblog_title );

	if ( empty($path) )
		$path = '/';

	// Check if the domain has been used already. We should return an error message.
	if ( domain_exists($domain, $path, $site_id) )
		return __( 'Error: Blog URL already taken.' );

	// Need to backup wpdb table names, and create a new wp_blogs entry for new blog.
	// Need to get blog_id from wp_blogs, and create new table names.
	// Must restore table names at the end of function.

	if ( ! $blog_id = insert_blog($domain, $path, $site_id) )
		return __( 'Error: problem creating blog entry.' );

	switch_to_blog($blog_id);
	install_blog($blog_id);
	restore_current_blog();

	return $blog_id;
}

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

// wpmu admin functions

function wpmu_admin_do_redirect( $url = '' ) {
	$ref = '';
	if ( isset( $_GET['ref'] ) )
		$ref = $_GET['ref'];
	if ( isset( $_POST['ref'] ) )
		$ref = $_POST['ref'];

	if ( $ref ) {
		$ref = wpmu_admin_redirect_add_updated_param( $ref );
		wp_redirect( $ref );
		exit();
	}
	if ( empty( $_SERVER['HTTP_REFERER'] ) == false ) {
		wp_redirect( $_SERVER['HTTP_REFERER'] );
		exit();
	}

	$url = wpmu_admin_redirect_add_updated_param( $url );
	if ( isset( $_GET['redirect'] ) ) {
		if ( substr( $_GET['redirect'], 0, 2 ) == 's_' )
			$url .= "&action=blogs&s=". esc_html( substr( $_GET['redirect'], 2 ) );
	} elseif ( isset( $_POST['redirect'] ) ) {
		$url = wpmu_admin_redirect_add_updated_param( $_POST['redirect'] );
	}
	wp_redirect( $url );
	exit();
}

function wpmu_admin_redirect_add_updated_param( $url = '' ) {
	if ( strpos( $url, 'updated=true' ) === false ) {
		if ( strpos( $url, '?' ) === false )
			return $url . '?updated=true';
		else
			return $url . '&updated=true';
	}
	return $url;
}

function is_blog_user( $blog_id = 0 ) {
	global $current_user, $wpdb;

	if ( !$blog_id )
		$blog_id = $wpdb->blogid;

	$cap_key = $wpdb->base_prefix . $blog_id . '_capabilities';

	if ( is_array($current_user->$cap_key) && in_array(1, $current_user->$cap_key) )
		return true;

	return false;
}

function is_email_address_unsafe( $user_email ) {
	$banned_names = get_site_option( "banned_email_domains" );
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

function wpmu_validate_user_signup($user_name, $user_email) {
	global $wpdb;

	$errors = new WP_Error();

	$user_name = preg_replace( "/\s+/", '', sanitize_user( $user_name, true ) );
	$user_email = sanitize_email( $user_email );

	if ( empty( $user_name ) )
	   	$errors->add('user_name', __("Please enter a username"));

	$maybe = array();
	preg_match( "/[a-z0-9]+/", $user_name, $maybe );

	if ( $user_name != $maybe[0] )
		$errors->add('user_name', __("Only lowercase letters and numbers allowed"));

	$illegal_names = get_site_option( "illegal_names" );
	if ( is_array( $illegal_names ) == false ) {
		$illegal_names = array(  "www", "web", "root", "admin", "main", "invite", "administrator" );
		add_site_option( "illegal_names", $illegal_names );
	}
	if ( in_array( $user_name, $illegal_names ) == true )
		$errors->add('user_name',  __("That username is not allowed"));

	if ( is_email_address_unsafe( $user_email ) )
		$errors->add('user_email',  __("You cannot use that email address to signup. We are having problems with them blocking some of our email. Please use another email provider."));

	if ( strlen( $user_name ) < 4 )
		$errors->add('user_name',  __("Username must be at least 4 characters"));

	if ( strpos( " " . $user_name, "_" ) != false )
		$errors->add( 'user_name', __( 'Sorry, usernames may not contain the character &#8220;_&#8221;!' ) );

	// all numeric?
	$match = array();
	preg_match( '/[0-9]*/', $user_name, $match );
	if ( $match[0] == $user_name )
		$errors->add('user_name', __("Sorry, usernames must have letters too!"));

	if ( !is_email( $user_email ) )
		$errors->add('user_email', __("Please enter a correct email address"));

	$limited_email_domains = get_site_option( 'limited_email_domains' );
	if ( is_array( $limited_email_domains ) && empty( $limited_email_domains ) == false ) {
		$emaildomain = substr( $user_email, 1 + strpos( $user_email, '@' ) );
		if ( in_array( $emaildomain, $limited_email_domains ) == false )
			$errors->add('user_email', __("Sorry, that email address is not allowed!"));
	}

	// Check if the username has been used already.
	if ( username_exists($user_name) )
		$errors->add('user_name', __("Sorry, that username already exists!"));

	// Check if the email address has been used already.
	if ( email_exists($user_email) )
		$errors->add('user_email', __("Sorry, that email address is already used!"));

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
			$errors->add('user_name', __("That username is currently reserved but may be available in a couple of days."));

		if ( $signup->active == 0 && $signup->user_email == $user_email )
			$errors->add('user_email_used', __("username and email used"));
	}

	$signup = $wpdb->get_row( $wpdb->prepare("SELECT * FROM $wpdb->signups WHERE user_email = %s", $user_email) );
	if ( $signup != null ) {
		$diff = current_time( 'timestamp', true ) - mysql2date('U', $signup->registered);
		// If registered more than two days ago, cancel registration and let this signup go through.
		if ( $diff > 172800 )
			$wpdb->query( $wpdb->prepare("DELETE FROM $wpdb->signups WHERE user_email = %s", $user_email) );
		else
			$errors->add('user_email', __("That email address has already been used. Please check your inbox for an activation email. It will become available in a couple of days if you do nothing."));
	}

	$result = array('user_name' => $user_name, 'user_email' => $user_email,	'errors' => $errors);

	return apply_filters('wpmu_validate_user_signup', $result);
}

function wpmu_validate_blog_signup($blogname, $blog_title, $user = '') {
	global $wpdb, $domain, $base, $current_site;

	$blog_title = strip_tags( $blog_title );
	$blog_title = substr( $blog_title, 0, 50 );

	$errors = new WP_Error();
	$illegal_names = get_site_option( "illegal_names" );
	if ( $illegal_names == false ) {
		$illegal_names = array( "www", "web", "root", "admin", "main", "invite", "administrator" );
		add_site_option( "illegal_names", $illegal_names );
	}

	if ( empty( $blogname ) )
		$errors->add('blogname', __("Please enter a blog name"));

	$maybe = array();
	preg_match( "/[a-z0-9]+/", $blogname, $maybe );
	if ( $blogname != $maybe[0] )
		$errors->add('blogname', __("Only lowercase letters and numbers allowed"));

	if ( in_array( $blogname, $illegal_names ) == true )
		$errors->add('blogname',  __("That name is not allowed"));

	if ( strlen( $blogname ) < 4 && !is_super_admin() )
		$errors->add('blogname',  __("Blog name must be at least 4 characters"));

	if ( strpos( " " . $blogname, "_" ) != false )
		$errors->add( 'blogname', __( 'Sorry, blog names may not contain the character &#8220;_&#8221;!' ) );

	// do not allow users to create a blog that conflicts with a page on the main blog.
	if ( !is_subdomain_install() && $wpdb->get_var( $wpdb->prepare( "SELECT post_name FROM " . $wpdb->get_blog_prefix( $current_site->blog_id ) . "posts WHERE post_type = 'page' AND post_name = %s", $blogname ) ) )
		$errors->add( 'blogname', __( 'Sorry, you may not use that blog name.' ) );

	// all numeric?
	$match = array();
	preg_match( '/[0-9]*/', $blogname, $match );
	if ( $match[0] == $blogname )
		$errors->add('blogname', __("Sorry, blog names must have letters too!"));

	$blogname = apply_filters( "newblogname", $blogname );

	$blog_title = stripslashes(  $blog_title );

	if ( empty( $blog_title ) )
		$errors->add('blog_title', __("Please enter a blog title"));

	// Check if the domain/path has been used already.
	if ( is_subdomain_install() ) {
		$mydomain = "$blogname.$domain";
		$path = $base;
	} else {
		$mydomain = "$domain";
		$path = $base.$blogname.'/';
	}
	if ( domain_exists($mydomain, $path) )
		$errors->add('blogname', __("Sorry, that blog already exists!"));

	if ( username_exists( $blogname ) ) {
		if ( is_object( $user ) == false || ( is_object($user) && ( $user->user_login != $blogname ) ) )
			$errors->add( 'blogname', __( "Sorry, that blog is reserved!" ) );
	}

	// Has someone already signed up for this domain?
	$signup = $wpdb->get_row( $wpdb->prepare("SELECT * FROM $wpdb->signups WHERE domain = %s AND path = %s", $mydomain, $path) ); // TODO: Check email too?
	if ( ! empty($signup) ) {
		$diff = current_time( 'timestamp', true ) - mysql2date('U', $signup->registered);
		// If registered more than two days ago, cancel registration and let this signup go through.
		if ( $diff > 172800 )
			$wpdb->query( $wpdb->prepare("DELETE FROM $wpdb->signups WHERE domain = %s AND path = %s", $mydomain, $path) );
		else
			$errors->add('blogname', __("That blog is currently reserved but may be available in a couple days."));
	}

	$result = array('domain' => $mydomain, 'path' => $path, 'blogname' => $blogname, 'blog_title' => $blog_title, 'errors' => $errors);
	return apply_filters('wpmu_validate_blog_signup', $result);
}

// Record signup information for future activation. wpmu_validate_signup() should be run
// on the inputs before calling wpmu_signup().
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

function wpmu_signup_user($user, $user_email, $meta = '') {
	global $wpdb;

	// Format data
	$user = preg_replace( "/\s+/", '', sanitize_user( $user, true ) );
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

// Notify user of signup success.
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
	$admin_email = get_site_option( "admin_email" );
	if ( $admin_email == '' )
		$admin_email = 'support@' . $_SERVER['SERVER_NAME'];
	$from_name = get_site_option( "site_name" ) == '' ? 'WordPress' : esc_html( get_site_option( "site_name" ) );
	$message_headers = "From: \"{$from_name}\" <{$admin_email}>\n" . "Content-Type: text/plain; charset=\"" . get_option('blog_charset') . "\"\n";
	$message = sprintf( apply_filters( 'wpmu_signup_blog_notification_email', __( "To activate your blog, please click the following link:\n\n%s\n\nAfter you activate, you will receive *another email* with your login.\n\nAfter you activate, you can visit your blog here:\n\n%s" ) ), $activate_url, esc_url( "http://{$domain}{$path}" ), $key );
	// TODO: Don't hard code activation link.
	$subject = sprintf( apply_filters( 'wpmu_signup_blog_notification_subject', __( '[%1s] Activate %2s' ) ), $from_name, esc_url( 'http://' . $domain . $path ) );
	wp_mail($user_email, $subject, $message, $message_headers);
	return true;
}

function wpmu_signup_user_notification($user, $user_email, $key, $meta = '') {
	if ( !apply_filters('wpmu_signup_user_notification', $user, $user_email, $key, $meta) )
		return false;

	// Send email with activation link.
	$admin_email = get_site_option( "admin_email" );
	if ( $admin_email == '' )
		$admin_email = 'support@' . $_SERVER['SERVER_NAME'];
	$from_name = get_site_option( "site_name" ) == '' ? 'WordPress' : esc_html( get_site_option( "site_name" ) );
	$message_headers = "From: \"{$from_name}\" <{$admin_email}>\n" . "Content-Type: text/plain; charset=\"" . get_option('blog_charset') . "\"\n";
	$message = sprintf( apply_filters( 'wpmu_signup_user_notification_email', __( "To activate your user, please click the following link:\n\n%s\n\nAfter you activate, you will receive *another email* with your login.\n\n" ) ), site_url( "wp-activate.php?key=$key" ), $key );
	// TODO: Don't hard code activation link.
	$subject = sprintf( __( apply_filters( 'wpmu_signup_user_notification_subject', '[%1s] Activate %2s' ) ), $from_name, $user);
	wp_mail($user_email, $subject, $message, $message_headers);
	return true;
}

function wpmu_activate_signup($key) {
	global $wpdb, $current_site;

	$signup = $wpdb->get_row( $wpdb->prepare("SELECT * FROM $wpdb->signups WHERE activation_key = %s", $key) );

	if ( empty($signup) )
		return new WP_Error('invalid_key', __('Invalid activation key.'));

	if ( $signup->active )
		return new WP_Error('already_active', __('The blog is already active.'), $signup);

	$meta = unserialize($signup->meta);
	$user_login = $wpdb->escape($signup->user_login);
	$user_email = $wpdb->escape($signup->user_email);
	wpmu_validate_user_signup($user_login, $user_email);
	$password = wp_generate_password();

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
		$user_site = get_site_option( 'dashboard_blog', $current_site->blog_id );

		if ( $user_site == false )
			add_user_to_blog( '1', $user_id, get_site_option( 'default_user_role', 'subscriber' ) );
		else
			add_user_to_blog( $user_site, $user_id, get_site_option( 'default_user_role', 'subscriber' ) );

		add_new_user_to_blog( $user_id, $user_email, $meta );
		do_action('wpmu_activate_user', $user_id, $password, $meta);
		return array('user_id' => $user_id, 'password' => $password, 'meta' => $meta);
	}

	wpmu_validate_blog_signup($signup->domain, $signup->title);
	$blog_id = wpmu_create_blog( $signup->domain, $signup->path, $signup->title, $user_id, $meta, $wpdb->siteid );

	// TODO: What to do if we create a user but cannot create a blog?
	if ( is_wp_error($blog_id) ) {
		// If blog is taken, that means a previous attempt to activate this blog failed in between creating the blog and
		// setting the activation flag.  Let's just set the active flag and instruct the user to reset their password.
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

function wpmu_create_user( $user_name, $password, $email) {
	$user_name = preg_replace( "/\s+/", '', sanitize_user( $user_name, true ) );

	$user_id = wp_create_user( $user_name, $password, $email );
	if ( is_wp_error($user_id) )
		return false;

	// Newly created users have no roles or caps until they are added to a blog.
	update_user_option($user_id, 'capabilities', '');
	update_user_option($user_id, 'user_level', '');

	do_action( 'wpmu_new_user', $user_id );

	return $user_id;
}

function wpmu_create_blog($domain, $path, $title, $user_id, $meta = '', $site_id = 1) {
	$domain = preg_replace( "/\s+/", '', sanitize_user( $domain, true ) );

	if ( is_subdomain_install() )
		$domain = str_replace( '@', '', $domain );

	$title = strip_tags( $title );
	$user_id = (int) $user_id;

	if ( empty($path) )
		$path = '/';

	// Check if the domain has been used already. We should return an error message.
	if ( domain_exists($domain, $path, $site_id) )
		return new WP_Error('blog_taken', __('Blog already exists.'));

	if ( !defined("WP_INSTALLING") )
		define( "WP_INSTALLING", true );

	if ( ! $blog_id = insert_blog($domain, $path, $site_id) )
		return new WP_Error('insert_blog', __('Could not create blog.'));

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
	update_option( 'blog_public', $meta['public'] );

	if ( !is_super_admin() && get_user_meta( $user_id, 'primary_blog', true ) == get_site_option( 'dashboard_blog', 1 ) )
		update_user_meta( $user_id, 'primary_blog', $blog_id );

	restore_current_blog();
	do_action( 'wpmu_new_blog', $blog_id, $user_id );

	return $blog_id;
}

function newblog_notify_siteadmin( $blog_id, $deprecated = '' ) {
	if ( get_site_option( 'registrationnotification' ) != 'yes' )
		return false;

	$email = get_site_option( 'admin_email' );
	if ( is_email($email) == false )
		return false;

	$options_site_url = esc_url(network_admin_url('ms-options.php'));

	switch_to_blog( $blog_id );
	$blogname = get_option( 'blogname' );
	$siteurl = site_url();
	restore_current_blog();

	$msg = sprintf( __( "New Blog: %1s
URL: %2s
Remote IP: %3s

Disable these notifications: %4s"), $blogname, $siteurl, $_SERVER['REMOTE_ADDR'], $options_site_url);
	$msg = apply_filters( 'newblog_notify_siteadmin', $msg );

	wp_mail( $email, sprintf( __( "New Blog Registration: %s" ), $siteurl ), $msg );
	return true;
}

function newuser_notify_siteadmin( $user_id ) {
	if ( get_site_option( 'registrationnotification' ) != 'yes' )
		return false;

	$email = get_site_option( 'admin_email' );

	if ( is_email($email) == false )
		return false;

	$user = new WP_User($user_id);

	$options_site_url = esc_url(network_admin_url('ms-options.php'));
	$msg = sprintf(__("New User: %1s
Remote IP: %2s

Disable these notifications: %3s"), $user->user_login, $_SERVER['REMOTE_ADDR'], $options_site_url);

	$msg = apply_filters( 'newuser_notify_siteadmin', $msg );
	wp_mail( $email, sprintf(__("New User Registration: %s"), $user->user_login), $msg );
	return true;
}

function domain_exists($domain, $path, $site_id = 1) {
	global $wpdb;
	return $wpdb->get_var( $wpdb->prepare("SELECT blog_id FROM $wpdb->blogs WHERE domain = %s AND path = %s AND site_id = %d", $domain, $path, $site_id) );
}

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

// Install an empty blog.  wpdb should already be switched.
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
	make_db_current_silent();
	populate_options();
	populate_roles();
	$wp_roles->_init();

	// fix url.
	update_option('siteurl', $url);
	update_option('home', $url);
	update_option('fileupload_url', $url . "files" );
	update_option('upload_path', "wp-content/blogs.dir/" . $blog_id . "/files");
	update_option('blogname', stripslashes( $blog_title ) );
	update_option('admin_email', '');
	$wpdb->update( $wpdb->options, array('option_value' => ''), array('option_name' => 'admin_email') );

	// remove all perms
	$wpdb->query( $wpdb->prepare("DELETE FROM $wpdb->usermeta WHERE meta_key = %s", $table_prefix.'user_level') );
	$wpdb->query( $wpdb->prepare("DELETE FROM $wpdb->usermeta WHERE meta_key = %s", $table_prefix.'capabilities') );

	$wpdb->suppress_errors( false );
}

// Deprecated, use wp_install_defaults()
// should be switched already as $blog_id is ignored.
function install_blog_defaults($blog_id, $user_id) {
	global $wpdb;

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

	$wpdb->suppress_errors();

	wp_install_defaults($user_id);

	$wpdb->suppress_errors( false );
}

function wpmu_welcome_notification($blog_id, $user_id, $password, $title, $meta = '') {
	global $current_site;

	if ( !apply_filters('wpmu_welcome_notification', $blog_id, $user_id, $password, $title, $meta) )
		return false;

	$welcome_email = stripslashes( get_site_option( 'welcome_email' ) );
	if ( $welcome_email == false )
		$welcome_email = stripslashes( __( 'Dear User,

Your new SITE_NAME blog has been successfully set up at:
BLOG_URL

You can log in to the administrator account with the following information:
Username: USERNAME
Password: PASSWORD
Login Here: BLOG_URLwp-login.php

We hope you enjoy your new blog.
Thanks!

--The Team @ SITE_NAME' ) );

	$url = get_blogaddress_by_id($blog_id);
	$user = new WP_User($user_id);

	$welcome_email = str_replace( "SITE_NAME", $current_site->site_name, $welcome_email );
	$welcome_email = str_replace( "BLOG_TITLE", $title, $welcome_email );
	$welcome_email = str_replace( "BLOG_URL", $url, $welcome_email );
	$welcome_email = str_replace( "USERNAME", $user->user_login, $welcome_email );
	$welcome_email = str_replace( "PASSWORD", $password, $welcome_email );

	$welcome_email = apply_filters( "update_welcome_email", $welcome_email, $blog_id, $user_id, $password, $title, $meta);
	$admin_email = get_site_option( "admin_email" );

	if ( $admin_email == '' )
		$admin_email = 'support@' . $_SERVER['SERVER_NAME'];

	$from_name = get_site_option( "site_name" ) == '' ? 'WordPress' : esc_html( get_site_option( "site_name" ) );
	$message_headers = "From: \"{$from_name}\" <{$admin_email}>\n" . "Content-Type: text/plain; charset=\"" . get_option('blog_charset') . "\"\n";
	$message = $welcome_email;

	if ( empty( $current_site->site_name ) )
		$current_site->site_name = "WordPress MU";

	$subject = apply_filters( 'update_welcome_subject', sprintf(__('New %1$s Blog: %2$s'), $current_site->site_name, stripslashes( $title ) ) );
	wp_mail($user->user_email, $subject, $message, $message_headers);
	return true;
}

function wpmu_welcome_user_notification($user_id, $password, $meta = '') {
	global $current_site;

	if ( !apply_filters('wpmu_welcome_user_notification', $user_id, $password, $meta) )
		return false;

	$welcome_email = get_site_option( 'welcome_user_email' );

	$user = new WP_User($user_id);

	$welcome_email = apply_filters( "update_welcome_user_email", $welcome_email, $user_id, $password, $meta);
	$welcome_email = str_replace( "SITE_NAME", $current_site->site_name, $welcome_email );
	$welcome_email = str_replace( "USERNAME", $user->user_login, $welcome_email );
	$welcome_email = str_replace( "PASSWORD", $password, $welcome_email );
	$welcome_email = str_replace( "LOGINLINK", wp_login_url(), $welcome_email );

	$admin_email = get_site_option( "admin_email" );

	if ( $admin_email == '' )
		$admin_email = 'support@' . $_SERVER['SERVER_NAME'];

	$from_name = get_site_option( "site_name" ) == '' ? 'WordPress' : esc_html( get_site_option( "site_name" ) );
	$message_headers = "From: \"{$from_name}\" <{$admin_email}>\n" . "Content-Type: text/plain; charset=\"" . get_option('blog_charset') . "\"\n";
	$message = $welcome_email;

	if ( empty( $current_site->site_name ) )
		$current_site->site_name = "WordPress MU";

	$subject = apply_filters( 'update_welcome_user_subject', sprintf(__('New %1$s User: %2$s'), $current_site->site_name, $user->user_login) );
	wp_mail($user->user_email, $subject, $message, $message_headers);
	return true;
}

function get_current_site() {
	global $current_site;
	return $current_site;
}

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

function get_most_recent_post_of_user( $user_id ) {
	global $wpdb;

	$user_blogs = get_blogs_of_user( (int) $user_id );
	$most_recent_post = array();

	// Walk through each blog and get the most recent post
	// published by $user_id
	foreach ( (array) $user_blogs as $blog ) {
		$recent_post = $wpdb->get_row( $wpdb->prepare("SELECT ID, post_date_gmt FROM {$wpdb->base_prefix}{$blog->userblog_id}_posts WHERE post_author = %d AND post_type = 'post' AND post_status = 'publish' ORDER BY post_date_gmt DESC LIMIT 1", $user_id ), ARRAY_A);

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

/* Misc functions */
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

function recurse_dirsize( $directory ) {
	$size = 0;

	if ( substr( $directory, -1 ) == '/' )
		$directory = substr($directory,0,-1);

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

function upload_is_user_over_quota( $echo = true ) {
	if ( get_site_option( 'upload_space_check_disabled' ) )
		return true;

	$spaceAllowed = get_space_allowed();
	if ( empty( $spaceAllowed ) || !is_numeric( $spaceAllowed ) )
		$spaceAllowed = 10;	// Default space allowed is 10 MB

	$dirName = BLOGUPLOADDIR;
	$size = get_dirsize($dirName) / 1024 / 1024;

	if ( ($spaceAllowed-$size) < 0 ) {
		if ( $echo )
			_e( 'Sorry, you have used your space allocation. Please delete some files to upload more files.' ); // No space left
		return true;
	} else {
		return false;
	}
}

function check_upload_mimes( $mimes ) {
	$site_exts = explode( " ", get_site_option( "upload_filetypes" ) );
	foreach ( $site_exts as $ext ) {
		foreach ( $mimes as $ext_pattern => $mime )
			if ( $ext != '' && strpos( $ext_pattern, $ext ) !== false ) {
				$site_mimes[$ext_pattern] = $mime;
		}
	}
	return $site_mimes;
}

function update_posts_count( $deprecated = '' ) {
	global $wpdb;
	update_option( "post_count", (int) $wpdb->get_var( "SELECT COUNT(ID) FROM {$wpdb->posts} WHERE post_status = 'publish' and post_type = 'post'" ) );
}

function wpmu_log_new_registrations( $blog_id, $user_id ) {
	global $wpdb;
	$user = new WP_User( (int) $user_id );
	$wpdb->insert( $wpdb->registration_log, array('email' => $user->user_email, 'IP' => preg_replace( '/[^0-9., ]/', '',$_SERVER['REMOTE_ADDR'] ), 'blog_id' => $blog_id, 'date_registered' => current_time('mysql')) );
}

function fix_import_form_size( $size ) {
	if ( upload_is_user_over_quota( false ) == true )
		return 0;

	$spaceAllowed = 1024 * 1024 * get_space_allowed();
	$dirName = BLOGUPLOADDIR;
	$dirsize = get_dirsize($dirName) ;
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
	global $wpdb, $global_terms_recurse;

	if ( !global_terms_enabled() )
		return $term_id;

	// prevent a race condition
	if ( !isset( $global_terms_recurse ) ) {
		$recurse_start = true;
		$global_terms_recurse = 1;
	} elseif ( 10 < $global_terms_recurse++ ) {
		return $term_id;
		$recurse_start = false;
	}

	$term_id = intval( $term_id );
	$c = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $wpdb->terms WHERE term_id = %d", $term_id ) );

	$global_id = $wpdb->get_var( $wpdb->prepare( "SELECT cat_ID FROM $wpdb->sitecategories WHERE category_nicename = %s", $c->slug ) );
	if ( $global_id == null ) {
		$used_global_id = $wpdb->get_var( $wpdb->prepare( "SELECT cat_ID FROM $wpdb->sitecategories WHERE cat_ID = %d", $c->term_id ) );
		if ( null == $used_global_id ) {
			$wpdb->insert( $wpdb->sitecategories, array( 'cat_ID' => $term_id, 'cat_name' => $c->name, 'category_nicename' => $c->slug ) );
			$global_id = $wpdb->insert_id;
		} else {
			$max_global_id = $wpdb->get_var( "SELECT MAX(cat_ID) FROM $wpdb->sitecategories" );
			$max_global_id += mt_rand( 100, 400 );
			$wpdb->insert( $wpdb->sitecategories, array( 'cat_ID' => $global_id, 'cat_name' => $c->name, 'category_nicename' => $c->slug ) );
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
		unset( $global_terms_recurse );

	return $global_id;
}

function redirect_this_site( $deprecated = '' ) {
	global $current_site;
	return array( $current_site->domain );
}

function upload_is_file_too_big( $upload ) {
	if ( is_array( $upload ) == false || defined( 'WP_IMPORTING' ) )
		return $upload;

	if ( strlen( $upload[ 'bits' ] )  > ( 1024 * get_site_option( 'fileupload_maxk', 1500 ) ) )
		return sprintf(__( "This file is too big. Files must be less than %dKb in size.<br />" ), get_site_option( 'fileupload_maxk', 1500 ));

	return $upload;
}

function wordpressmu_wp_mail_from( $email ) {
	if ( strpos( $email, 'wordpress@' ) !== false )
		$email = get_option( 'admin_email' );
	return $email;
}

function signup_nonce_fields() {
	$id = mt_rand();
	echo "<input type='hidden' name='signup_form_id' value='{$id}' />";
	wp_nonce_field('signup_form_' . $id, '_signup_form', false);
}

function signup_nonce_check( $result ) {
	if ( !strpos( $_SERVER[ 'PHP_SELF' ], 'wp-signup.php' ) )
		return $result;

	if ( wp_create_nonce('signup_form_' . $_POST[ 'signup_form_id' ]) != $_POST['_signup_form'] )
		wp_die( __('Please try again!') );

	return $result;
}

function maybe_redirect_404() {
	global $current_site;
	if ( is_main_site() && is_404() && defined( 'NOBLOGREDIRECT' ) && ( $destination = NOBLOGREDIRECT ) ) {
		if ( $destination == '%siteurl%' )
			$destination = network_home_url();
		wp_redirect( $destination );
		exit();
	}
}

function maybe_add_existing_user_to_blog() {
	if ( false === strpos( $_SERVER[ 'REQUEST_URI' ], '/newbloguser/' ) )
		return false;

	$parts = explode( '/', $_SERVER[ 'REQUEST_URI' ] );
	$key = array_pop( $parts );

	if ( $key == '' )
		$key = array_pop( $parts );

	$details = get_option( "new_user_" . $key );
	add_existing_user_to_blog( $details );
	delete_option( 'new_user_' . $key );
	wp_die( sprintf(__('You have been added to this blog. Please visit the <a href="%s">homepage</a> or <a href="%s">login</a> using your username and password.'), site_url(), admin_url() ) );
}

function add_existing_user_to_blog( $details = false ) {
	if ( is_array( $details ) ) {
		add_user_to_blog( '', $details[ 'user_id' ], $details[ 'role' ] );
		do_action( "added_existing_user", $details[ 'user_id' ] );
	}
}

function add_new_user_to_blog( $user_id, $email, $meta ) {
	global $current_site;
	if ( $meta[ 'add_to_blog' ] ) {
		$blog_id = $meta[ 'add_to_blog' ];
		$role = $meta[ 'new_role' ];
		remove_user_from_blog($user_id, $current_site->blogid); // remove user from main blog.
		add_user_to_blog( $blog_id, $user_id, $role );
		update_user_meta( $user_id, 'primary_blog', $blog_id );
	}
}

function fix_phpmailer_messageid( $phpmailer ) {
	global $current_site;
	$phpmailer->Hostname = $current_site->domain;
}

function is_user_spammy( $username = 0 ) {
	if ( $username == 0 ) {
		global $current_user;
		$user_id = $current_user->ID;
	} else {
		$user_id = get_user_id_from_string( $username );
	}
	$u = new WP_User( $user_id );

	if ( $u->spam == 1 )
		return true;

	return false;
}

function update_blog_public( $old_value, $value ) {
	global $wpdb;
	do_action('update_blog_public');
	update_blog_status( $wpdb->blogid, 'public', (int) $value );
}
add_action('update_option_blog_public', 'update_blog_public', 10, 2);

/* Redirect all hits to "dashboard" blog to wp-admin/ Dashboard. */
function redirect_mu_dashboard() {
	global $current_site, $current_blog;

	$dashboard_blog = get_dashboard_blog();
	if ( $current_blog->blog_id == $dashboard_blog->blog_id && $dashboard_blog->blog_id != $current_site->blog_id ) {
		$protocol = ( is_ssl() ? 'https://' : 'http://' );
		wp_redirect( $protocol . $dashboard_blog->domain . trailingslashit( $dashboard_blog->path ) . 'wp-admin/' );
		die();
	}
}
add_action( 'template_redirect', 'redirect_mu_dashboard' );

function get_dashboard_blog() {
	if ( $blog = get_site_option( 'dashboard_blog' ) )
		return get_blog_details( $blog );

	return get_blog_details( $GLOBALS['current_site']->blog_id );
}

function is_user_option_local( $key, $user_id = 0, $blog_id = 0 ) {
	global $current_user, $wpdb;

	if ( $user_id == 0 )
		$user_id = $current_user->ID;
	if ( $blog_id == 0 )
		$blog_id = $wpdb->blogid;

	$local_key = $wpdb->base_prefix . $blog_id . "_" . $key;

	if ( isset( $current_user->$local_key ) )
		return true;

	return false;
}

function users_can_register_signup_filter() {
	$registration = get_site_option('registration');
	if ( $registration == 'all' || $registration == 'user' )
		return true;

	return false;
}
add_filter('option_users_can_register', 'users_can_register_signup_filter');

function welcome_user_msg_filter( $text ) {
	if ( !$text ) {
		return __( "Dear User,

Your new account is set up.

You can log in with the following information:
Username: USERNAME
Password: PASSWORD
LOGINLINK

Thanks!

--The Team @ SITE_NAME" );
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

?>
