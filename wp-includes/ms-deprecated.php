<?php
/**
 * Deprecated functions from WordPress MU and the multisite feature. You shouldn't
 * use these functions and look for the alternatives instead. The functions will be
 * removed in a later version.
 *
 * @package WordPress
 * @subpackage Deprecated
 * @since 3.0.0
 */

/*
 * Deprecated functions come here to die.
 */

/**
 * Get the "dashboard blog", the blog where users without a blog edit their profile data.
 * Dashboard blog functionality was removed in WordPress 3.1, replaced by the user admin.
 *
 * @since MU
 * @deprecated 3.1.0 Use get_blog_details()
 * @see get_blog_details()
 *
 * @return int Current site ID.
 */
function get_dashboard_blog() {
    _deprecated_function( __FUNCTION__, '3.1' );
    if ( $blog = get_site_option( 'dashboard_blog' ) )
        return get_blog_details( $blog );

    return get_blog_details( $GLOBALS['current_site']->blog_id );
}

/**
 * Generates a random password.
 *
 * @since MU
 * @deprecated 3.0.0 Use wp_generate_password()
 * @see wp_generate_password()
 *
 * @param int $len Optional. The length of password to generate. Default 8.
 */
function generate_random_password( $len = 8 ) {
	_deprecated_function( __FUNCTION__, '3.0', 'wp_generate_password()' );
	return wp_generate_password( $len );
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
 * @since MU
 * @deprecated 3.0.0 Use is_super_admin()
 * @see is_super_admin()
 *
 * @param string $user_login Optional. Username for the user to check. Default empty.
 */
function is_site_admin( $user_login = '' ) {
	_deprecated_function( __FUNCTION__, '3.0', 'is_super_admin()' );

	if ( empty( $user_login ) ) {
		$user_id = get_current_user_id();
		if ( !$user_id )
			return false;
	} else {
		$user = get_user_by( 'login', $user_login );
		if ( ! $user->exists() )
			return false;
		$user_id = $user->ID;
	}

	return is_super_admin( $user_id );
}

if ( !function_exists( 'graceful_fail' ) ) :
/**
 * Deprecated functionality to gracefully fail.
 *
 * @since MU
 * @deprecated 3.0.0 Use wp_die()
 * @see wp_die()
 */
function graceful_fail( $message ) {
	_deprecated_function( __FUNCTION__, '3.0', 'wp_die()' );
	$message = apply_filters( 'graceful_fail', $message );
	$message_template = apply_filters( 'graceful_fail_template',
'<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml"><head>
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
 * Deprecated functionality to retrieve user information.
 *
 * @since MU
 * @deprecated 3.0.0 Use get_user_by()
 * @see get_user_by()
 *
 * @param string $username Username.
 */
function get_user_details( $username ) {
	_deprecated_function( __FUNCTION__, '3.0', 'get_user_by()' );
	return get_user_by('login', $username);
}

/**
 * Deprecated functionality to clear the global post cache.
 *
 * @since MU
 * @deprecated 3.0.0 Use clean_post_cache()
 * @see clean_post_cache()
 *
 * @param int $post_id Post ID.
 */
function clear_global_post_cache( $post_id ) {
	_deprecated_function( __FUNCTION__, '3.0', 'clean_post_cache()' );
}

/**
 * Deprecated functionality to determin if the current site is the main site.
 *
 * @since MU
 * @deprecated 3.0.0 Use is_main_site()
 * @see is_main_site()
 */
function is_main_blog() {
	_deprecated_function( __FUNCTION__, '3.0', 'is_main_site()' );
	return is_main_site();
}

/**
 * Deprecated functionality to validate an email address.
 *
 * @since MU
 * @deprecated 3.0.0 Use is_email()
 * @see is_email()
 *
 * @param string $email        Email address to verify.
 * @param bool   $check_domain Deprecated.
 * @return string|bool Either false or the valid email address.
 */
function validate_email( $email, $check_domain = true) {
	_deprecated_function( __FUNCTION__, '3.0', 'is_email()' );
	return is_email( $email, $check_domain );
}

/**
 * Deprecated functionality to retrieve a list of all sites.
 *
 * @since MU
 * @deprecated 3.0.0 Use wp_get_sites()
 * @see wp_get_sites()
 *
 * @param int    $start      Optional. Offset for retrieving the blog list. Default 0.
 * @param int    $num        Optional. Number of blogs to list. Default 10.
 * @param string $deprecated Unused.
 */
function get_blog_list( $start = 0, $num = 10, $deprecated = '' ) {
	_deprecated_function( __FUNCTION__, '3.0', 'wp_get_sites()' );

	global $wpdb;
	$blogs = $wpdb->get_results( $wpdb->prepare("SELECT blog_id, domain, path FROM $wpdb->blogs WHERE site_id = %d AND public = '1' AND archived = '0' AND mature = '0' AND spam = '0' AND deleted = '0' ORDER BY registered DESC", $wpdb->siteid), ARRAY_A );

	$blog_list = array();
	foreach ( (array) $blogs as $details ) {
		$blog_list[ $details['blog_id'] ] = $details;
		$blog_list[ $details['blog_id'] ]['postcount'] = $wpdb->get_var( "SELECT COUNT(ID) FROM " . $wpdb->get_blog_prefix( $details['blog_id'] ). "posts WHERE post_status='publish' AND post_type='post'" );
	}

	if ( ! $blog_list ) {
		return array();
	}

	if ( $num == 'all' ) {
		return array_slice( $blog_list, $start, count( $blog_list ) );
	} else {
		return array_slice( $blog_list, $start, $num );
	}
}

/**
 * Deprecated functionality to retrieve a list of the most active sites.
 *
 * @since MU
 * @deprecated 3.0.0
 *
 * @param int  $num     Optional. Number of activate blogs to retrieve. Default 10.
 * @param bool $display Optional. Whether or not to display the most active blogs list. Default true.
 * @return array List of "most active" sites.
 */
function get_most_active_blogs( $num = 10, $display = true ) {
	_deprecated_function( __FUNCTION__, '3.0' );

	$blogs = get_blog_list( 0, 'all', false ); // $blog_id -> $details
	if ( is_array( $blogs ) ) {
		reset( $blogs );
		$most_active = array();
		$blog_list = array();
		foreach ( (array) $blogs as $key => $details ) {
			$most_active[ $details['blog_id'] ] = $details['postcount'];
			$blog_list[ $details['blog_id'] ] = $details; // array_slice() removes keys!!
		}
		arsort( $most_active );
		reset( $most_active );
		$t = array();
		foreach ( (array) $most_active as $key => $details ) {
			$t[ $key ] = $blog_list[ $key ];
		}
		unset( $most_active );
		$most_active = $t;
	}

	if ( $display ) {
		if ( is_array( $most_active ) ) {
			reset( $most_active );
			foreach ( (array) $most_active as $key => $details ) {
				$url = esc_url('http://' . $details['domain'] . $details['path']);
				echo '<li>' . $details['postcount'] . " <a href='$url'>$url</a></li>";
			}
		}
	}
	return array_slice( $most_active, 0, $num );
}

/**
 * Redirect a user based on $_GET or $_POST arguments.
 *
 * The function looks for redirect arguments in the following order:
 * 1) $_GET['ref']
 * 2) $_POST['ref']
 * 3) $_SERVER['HTTP_REFERER']
 * 4) $_GET['redirect']
 * 5) $_POST['redirect']
 * 6) $url
 *
 * @since MU
 * @deprecated 3.3.0 Use wp_redirect()
 * @see wp_redirect()
 *
 * @param string $url Optional. Redirect URL. Default empty.
 */
function wpmu_admin_do_redirect( $url = '' ) {
	_deprecated_function( __FUNCTION__, '3.3' );

	$ref = '';
	if ( isset( $_GET['ref'] ) && isset( $_POST['ref'] ) && $_GET['ref'] !== $_POST['ref'] ) {
		wp_die( __( 'A variable mismatch has been detected.' ), __( 'Sorry, you are not allowed to view this item.' ), 400 );
	} elseif ( isset( $_POST['ref'] ) ) {
		$ref = $_POST[ 'ref' ];
	} elseif ( isset( $_GET['ref'] ) ) {
		$ref = $_GET[ 'ref' ];
	}

	if ( $ref ) {
		$ref = wpmu_admin_redirect_add_updated_param( $ref );
		wp_redirect( $ref );
		exit();
	}
	if ( ! empty( $_SERVER['HTTP_REFERER'] ) ) {
		wp_redirect( $_SERVER['HTTP_REFERER'] );
		exit();
	}

	$url = wpmu_admin_redirect_add_updated_param( $url );
	if ( isset( $_GET['redirect'] ) && isset( $_POST['redirect'] ) && $_GET['redirect'] !== $_POST['redirect'] ) {
		wp_die( __( 'A variable mismatch has been detected.' ), __( 'Sorry, you are not allowed to view this item.' ), 400 );
	} elseif ( isset( $_GET['redirect'] ) ) {
		if ( substr( $_GET['redirect'], 0, 2 ) == 's_' )
			$url .= '&action=blogs&s='. esc_html( substr( $_GET['redirect'], 2 ) );
	} elseif ( isset( $_POST['redirect'] ) ) {
		$url = wpmu_admin_redirect_add_updated_param( $_POST['redirect'] );
	}
	wp_redirect( $url );
	exit();
}

/**
 * Adds an 'updated=true' argument to a URL.
 *
 * @since MU
 * @deprecated 3.3.0 Use add_query_arg()
 * @see add_query_arg()
 *
 * @param string $url Optional. Redirect URL. Default empty.
 * @return string
 */
function wpmu_admin_redirect_add_updated_param( $url = '' ) {
	_deprecated_function( __FUNCTION__, '3.3' );

	if ( strpos( $url, 'updated=true' ) === false ) {
		if ( strpos( $url, '?' ) === false )
			return $url . '?updated=true';
		else
			return $url . '&updated=true';
	}
	return $url;
}

/**
 * Get a numeric user ID from either an email address or a login.
 *
 * A numeric string is considered to be an existing user ID
 * and is simply returned as such.
 *
 * @since MU
 * @deprecated 3.6.0 Use get_user_by()
 * @see get_user_by()
 *
 * @param string $string Either an email address or a login.
 * @return int
 */
function get_user_id_from_string( $string ) {
	_deprecated_function( __FUNCTION__, '3.6', 'get_user_by()' );

	if ( is_email( $string ) )
		$user = get_user_by( 'email', $string );
	elseif ( is_numeric( $string ) )
		return $string;
	else
		$user = get_user_by( 'login', $string );

	if ( $user )
		return $user->ID;
	return 0;
}

/**
 * Get a full blog URL, given a domain and a path.
 *
 * @since MU
 * @deprecated 3.7.0
 *
 * @param string $domain
 * @param string $path
 * @return string
 */
function get_blogaddress_by_domain( $domain, $path ) {
	_deprecated_function( __FUNCTION__, '3.7' );

	if ( is_subdomain_install() ) {
		$url = "http://" . $domain.$path;
	} else {
		if ( $domain != $_SERVER['HTTP_HOST'] ) {
			$blogname = substr( $domain, 0, strpos( $domain, '.' ) );
			$url = 'http://' . substr( $domain, strpos( $domain, '.' ) + 1 ) . $path;
			// we're not installing the main blog
			if ( $blogname != 'www.' )
				$url .= $blogname . '/';
		} else { // main blog
			$url = 'http://' . $domain . $path;
		}
	}
	return esc_url_raw( $url );
}

/**
 * Create an empty blog.
 *
 * @since MU 1.0
 * @deprecated 4.4.0
 *
 * @param string $domain       The new blog's domain.
 * @param string $path         The new blog's path.
 * @param string $weblog_title The new blog's title.
 * @param int    $site_id      Optional. Defaults to 1.
 * @return string|int The ID of the newly created blog
 */
function create_empty_blog( $domain, $path, $weblog_title, $site_id = 1 ) {
	_deprecated_function( __FUNCTION__, '4.4' );

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
 * Get the admin for a domain/path combination.
 *
 * @since MU 1.0
 * @deprecated 4.4.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param string $sitedomain Optional. Site domain.
 * @param string $path       Optional. Site path.
 * @return array|false The network admins
 */
function get_admin_users_for_domain( $sitedomain = '', $path = '' ) {
	_deprecated_function( __FUNCTION__, '4.4' );

	global $wpdb;

	if ( ! $sitedomain )
		$site_id = $wpdb->siteid;
	else
		$site_id = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM $wpdb->site WHERE domain = %s AND path = %s", $sitedomain, $path ) );

	if ( $site_id )
		return $wpdb->get_results( $wpdb->prepare( "SELECT u.ID, u.user_login, u.user_pass FROM $wpdb->users AS u, $wpdb->sitemeta AS sm WHERE sm.meta_key = 'admin_user_id' AND u.ID = sm.meta_value AND sm.site_id = %d", $site_id ), ARRAY_A );

	return false;
}
