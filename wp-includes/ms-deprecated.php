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
 * @since MU (3.0.0)
 * @deprecated 3.1.0 Use get_site()
 * @see get_site()
 *
 * @return WP_Site Current site object.
 */
function get_dashboard_blog() {
    _deprecated_function( __FUNCTION__, '3.1.0', 'get_site()' );
    if ( $blog = get_site_option( 'dashboard_blog' ) ) {
	    return get_site( $blog );
    }

    return get_site( get_network()->site_id );
}

/**
 * Generates a random password.
 *
 * @since MU (3.0.0)
 * @deprecated 3.0.0 Use wp_generate_password()
 * @see wp_generate_password()
 *
 * @param int $len Optional. The length of password to generate. Default 8.
 */
function generate_random_password( $len = 8 ) {
	_deprecated_function( __FUNCTION__, '3.0.0', 'wp_generate_password()' );
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
 * @since MU (3.0.0)
 * @deprecated 3.0.0 Use is_super_admin()
 * @see is_super_admin()
 *
 * @param string $user_login Optional. Username for the user to check. Default empty.
 */
function is_site_admin( $user_login = '' ) {
	_deprecated_function( __FUNCTION__, '3.0.0', 'is_super_admin()' );

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
 * @since MU (3.0.0)
 * @deprecated 3.0.0 Use wp_die()
 * @see wp_die()
 */
function graceful_fail( $message ) {
	_deprecated_function( __FUNCTION__, '3.0.0', 'wp_die()' );
	$message = apply_filters( 'graceful_fail', $message );
	$message_template = apply_filters( 'graceful_fail_template',
'<!DOCTYPE html>
<html><head>
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
 * @since MU (3.0.0)
 * @deprecated 3.0.0 Use get_user_by()
 * @see get_user_by()
 *
 * @param string $username Username.
 */
function get_user_details( $username ) {
	_deprecated_function( __FUNCTION__, '3.0.0', 'get_user_by()' );
	return get_user_by('login', $username);
}

/**
 * Deprecated functionality to clear the global post cache.
 *
 * @since MU (3.0.0)
 * @deprecated 3.0.0 Use clean_post_cache()
 * @see clean_post_cache()
 *
 * @param int $post_id Post ID.
 */
function clear_global_post_cache( $post_id ) {
	_deprecated_function( __FUNCTION__, '3.0.0', 'clean_post_cache()' );
}

/**
 * Deprecated functionality to determin if the current site is the main site.
 *
 * @since MU (3.0.0)
 * @deprecated 3.0.0 Use is_main_site()
 * @see is_main_site()
 */
function is_main_blog() {
	_deprecated_function( __FUNCTION__, '3.0.0', 'is_main_site()' );
	return is_main_site();
}

/**
 * Deprecated functionality to validate an email address.
 *
 * @since MU (3.0.0)
 * @deprecated 3.0.0 Use is_email()
 * @see is_email()
 *
 * @param string $email        Email address to verify.
 * @param bool   $check_domain Deprecated.
 * @return string|false Valid email address on success, false on failure.
 */
function validate_email( $email, $check_domain = true) {
	_deprecated_function( __FUNCTION__, '3.0.0', 'is_email()' );
	return is_email( $email, $check_domain );
}

/**
 * Deprecated functionality to retrieve a list of all sites.
 *
 * @since MU (3.0.0)
 * @deprecated 3.0.0 Use wp_get_sites()
 * @see wp_get_sites()
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param int    $start      Optional. Offset for retrieving the blog list. Default 0.
 * @param int    $num        Optional. Number of blogs to list. Default 10.
 * @param string $deprecated Unused.
 */
function get_blog_list( $start = 0, $num = 10, $deprecated = '' ) {
	_deprecated_function( __FUNCTION__, '3.0.0', 'wp_get_sites()' );

	global $wpdb;
	$blogs = $wpdb->get_results( $wpdb->prepare( "SELECT blog_id, domain, path FROM $wpdb->blogs WHERE site_id = %d AND public = '1' AND archived = '0' AND mature = '0' AND spam = '0' AND deleted = '0' ORDER BY registered DESC", get_current_network_id() ), ARRAY_A );

	$blog_list = array();
	foreach ( (array) $blogs as $details ) {
		$blog_list[ $details['blog_id'] ] = $details;
		$blog_list[ $details['blog_id'] ]['postcount'] = $wpdb->get_var( "SELECT COUNT(ID) FROM " . $wpdb->get_blog_prefix( $details['blog_id'] ). "posts WHERE post_status='publish' AND post_type='post'" );
	}

	if ( ! $blog_list ) {
		return array();
	}

	if ( 'all' === $num ) {
		return array_slice( $blog_list, $start, count( $blog_list ) );
	} else {
		return array_slice( $blog_list, $start, $num );
	}
}

/**
 * Deprecated functionality to retrieve a list of the most active sites.
 *
 * @since MU (3.0.0)
 * @deprecated 3.0.0
 *
 * @param int  $num     Optional. Number of activate blogs to retrieve. Default 10.
 * @param bool $display Optional. Whether or not to display the most active blogs list. Default true.
 * @return array List of "most active" sites.
 */
function get_most_active_blogs( $num = 10, $display = true ) {
	_deprecated_function( __FUNCTION__, '3.0.0' );

	$blogs = get_blog_list( 0, 'all', false ); // $blog_id -> $details
	if ( is_array( $blogs ) ) {
		reset( $blogs );
		$most_active = array();
		$blog_list = array();
		foreach ( (array) $blogs as $key => $details ) {
			$most_active[ $details['blog_id'] ] = $details['postcount'];
			$blog_list[ $details['blog_id'] ] = $details; // array_slice() removes keys!
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
 * @since MU (3.0.0)
 * @deprecated 3.3.0 Use wp_redirect()
 * @see wp_redirect()
 *
 * @param string $url Optional. Redirect URL. Default empty.
 */
function wpmu_admin_do_redirect( $url = '' ) {
	_deprecated_function( __FUNCTION__, '3.3.0', 'wp_redirect()' );

	$ref = '';
	if ( isset( $_GET['ref'] ) && isset( $_POST['ref'] ) && $_GET['ref'] !== $_POST['ref'] ) {
		wp_die( __( 'A variable mismatch has been detected.' ), __( 'Sorry, you are not allowed to view this item.' ), 400 );
	} elseif ( isset( $_POST['ref'] ) ) {
		$ref = $_POST['ref'];
	} elseif ( isset( $_GET['ref'] ) ) {
		$ref = $_GET['ref'];
	}

	if ( $ref ) {
		$ref = wpmu_admin_redirect_add_updated_param( $ref );
		wp_redirect( $ref );
		exit;
	}
	if ( ! empty( $_SERVER['HTTP_REFERER'] ) ) {
		wp_redirect( $_SERVER['HTTP_REFERER'] );
		exit;
	}

	$url = wpmu_admin_redirect_add_updated_param( $url );
	if ( isset( $_GET['redirect'] ) && isset( $_POST['redirect'] ) && $_GET['redirect'] !== $_POST['redirect'] ) {
		wp_die( __( 'A variable mismatch has been detected.' ), __( 'Sorry, you are not allowed to view this item.' ), 400 );
	} elseif ( isset( $_GET['redirect'] ) ) {
		if ( str_starts_with( $_GET['redirect'], 's_' ) )
			$url .= '&action=blogs&s='. esc_html( substr( $_GET['redirect'], 2 ) );
	} elseif ( isset( $_POST['redirect'] ) ) {
		$url = wpmu_admin_redirect_add_updated_param( $_POST['redirect'] );
	}
	wp_redirect( $url );
	exit;
}

/**
 * Adds an 'updated=true' argument to a URL.
 *
 * @since MU (3.0.0)
 * @deprecated 3.3.0 Use add_query_arg()
 * @see add_query_arg()
 *
 * @param string $url Optional. Redirect URL. Default empty.
 * @return string
 */
function wpmu_admin_redirect_add_updated_param( $url = '' ) {
	_deprecated_function( __FUNCTION__, '3.3.0', 'add_query_arg()' );

	if ( ! str_contains( $url, 'updated=true' ) ) {
		if ( ! str_contains( $url, '?' ) )
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
 * @since MU (3.0.0)
 * @deprecated 3.6.0 Use get_user_by()
 * @see get_user_by()
 *
 * @param string $email_or_login Either an email address or a login.
 * @return int
 */
function get_user_id_from_string( $email_or_login ) {
	_deprecated_function( __FUNCTION__, '3.6.0', 'get_user_by()' );

	if ( is_email( $email_or_login ) )
		$user = get_user_by( 'email', $email_or_login );
	elseif ( is_numeric( $email_or_login ) )
		return $email_or_login;
	else
		$user = get_user_by( 'login', $email_or_login );

	if ( $user )
		return $user->ID;
	return 0;
}

/**
 * Get a full site URL, given a domain and a path.
 *
 * @since MU (3.0.0)
 * @deprecated 3.7.0
 *
 * @param string $domain
 * @param string $path
 * @return string
 */
function get_blogaddress_by_domain( $domain, $path ) {
	_deprecated_function( __FUNCTION__, '3.7.0' );

	if ( is_subdomain_install() ) {
		$url = "http://" . $domain.$path;
	} else {
		if ( $domain != $_SERVER['HTTP_HOST'] ) {
			$blogname = substr( $domain, 0, strpos( $domain, '.' ) );
			$url = 'http://' . substr( $domain, strpos( $domain, '.' ) + 1 ) . $path;
			// We're not installing the main blog.
			if ( 'www.' !== $blogname )
				$url .= $blogname . '/';
		} else { // Main blog.
			$url = 'http://' . $domain . $path;
		}
	}
	return sanitize_url( $url );
}

/**
 * Create an empty blog.
 *
 * @since MU (3.0.0)
 * @deprecated 4.4.0
 *
 * @param string $domain       The new blog's domain.
 * @param string $path         The new blog's path.
 * @param string $weblog_title The new blog's title.
 * @param int    $site_id      Optional. Defaults to 1.
 * @return string|int The ID of the newly created blog
 */
function create_empty_blog( $domain, $path, $weblog_title, $site_id = 1 ) {
	_deprecated_function( __FUNCTION__, '4.4.0' );

	if ( empty($path) )
		$path = '/';

	// Check if the domain has been used already. We should return an error message.
	if ( domain_exists($domain, $path, $site_id) )
		return __( '<strong>Error:</strong> Site URL you&#8217;ve entered is already taken.' );

	/*
	 * Need to back up wpdb table names, and create a new wp_blogs entry for new blog.
	 * Need to get blog_id from wp_blogs, and create new table names.
	 * Must restore table names at the end of function.
	 */

	if ( ! $blog_id = insert_blog($domain, $path, $site_id) )
		return __( '<strong>Error:</strong> There was a problem creating site entry.' );

	switch_to_blog($blog_id);
	install_blog($blog_id);
	restore_current_blog();

	return $blog_id;
}

/**
 * Get the admin for a domain/path combination.
 *
 * @since MU (3.0.0)
 * @deprecated 4.4.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param string $domain Optional. Network domain.
 * @param string $path   Optional. Network path.
 * @return array|false The network admins.
 */
function get_admin_users_for_domain( $domain = '', $path = '' ) {
	_deprecated_function( __FUNCTION__, '4.4.0' );

	global $wpdb;

	if ( ! $domain ) {
		$network_id = get_current_network_id();
	} else {
		$_networks  = get_networks( array(
			'fields' => 'ids',
			'number' => 1,
			'domain' => $domain,
			'path'   => $path,
		) );
		$network_id = ! empty( $_networks ) ? array_shift( $_networks ) : 0;
	}

	if ( $network_id )
		return $wpdb->get_results( $wpdb->prepare( "SELECT u.ID, u.user_login, u.user_pass FROM $wpdb->users AS u, $wpdb->sitemeta AS sm WHERE sm.meta_key = 'admin_user_id' AND u.ID = sm.meta_value AND sm.site_id = %d", $network_id ), ARRAY_A );

	return false;
}

/**
 * Return an array of sites for a network or networks.
 *
 * @since 3.7.0
 * @deprecated 4.6.0 Use get_sites()
 * @see get_sites()
 *
 * @param array $args {
 *     Array of default arguments. Optional.
 *
 *     @type int|int[] $network_id A network ID or array of network IDs. Set to null to retrieve sites
 *                                 from all networks. Defaults to current network ID.
 *     @type int       $public     Retrieve public or non-public sites. Default null, for any.
 *     @type int       $archived   Retrieve archived or non-archived sites. Default null, for any.
 *     @type int       $mature     Retrieve mature or non-mature sites. Default null, for any.
 *     @type int       $spam       Retrieve spam or non-spam sites. Default null, for any.
 *     @type int       $deleted    Retrieve deleted or non-deleted sites. Default null, for any.
 *     @type int       $limit      Number of sites to limit the query to. Default 100.
 *     @type int       $offset     Exclude the first x sites. Used in combination with the $limit parameter. Default 0.
 * }
 * @return array[] An empty array if the installation is considered "large" via wp_is_large_network(). Otherwise,
 *                 an associative array of WP_Site data as arrays.
 */
function wp_get_sites( $args = array() ) {
	_deprecated_function( __FUNCTION__, '4.6.0', 'get_sites()' );

	if ( wp_is_large_network() )
		return array();

	$defaults = array(
		'network_id' => get_current_network_id(),
		'public'     => null,
		'archived'   => null,
		'mature'     => null,
		'spam'       => null,
		'deleted'    => null,
		'limit'      => 100,
		'offset'     => 0,
	);

	$args = wp_parse_args( $args, $defaults );

	// Backward compatibility.
	if( is_array( $args['network_id'] ) ){
		$args['network__in'] = $args['network_id'];
		$args['network_id'] = null;
	}

	if( is_numeric( $args['limit'] ) ){
		$args['number'] = $args['limit'];
		$args['limit'] = null;
	} elseif ( ! $args['limit'] ) {
		$args['number'] = 0;
		$args['limit'] = null;
	}

	// Make sure count is disabled.
	$args['count'] = false;

	$_sites  = get_sites( $args );

	$results = array();

	foreach ( $_sites as $_site ) {
		$_site = get_site( $_site );
		$results[] = $_site->to_array();
	}

	return $results;
}

/**
 * Check whether a usermeta key has to do with the current blog.
 *
 * @since MU (3.0.0)
 * @deprecated 4.9.0
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

	_deprecated_function( __FUNCTION__, '4.9.0' );

	$current_user = wp_get_current_user();
	if ( $blog_id == 0 ) {
		$blog_id = get_current_blog_id();
	}
	$local_key = $wpdb->get_blog_prefix( $blog_id ) . $key;

	return isset( $current_user->$local_key );
}

/**
 * Store basic site info in the blogs table.
 *
 * This function creates a row in the wp_blogs table and returns
 * the new blog's ID. It is the first step in creating a new blog.
 *
 * @since MU (3.0.0)
 * @deprecated 5.1.0 Use wp_insert_site()
 * @see wp_insert_site()
 *
 * @param string $domain  The domain of the new site.
 * @param string $path    The path of the new site.
 * @param int    $site_id Unless you're running a multi-network install, be sure to set this value to 1.
 * @return int|false The ID of the new row
 */
function insert_blog($domain, $path, $site_id) {
	_deprecated_function( __FUNCTION__, '5.1.0', 'wp_insert_site()' );

	$data = array(
		'domain'  => $domain,
		'path'    => $path,
		'site_id' => $site_id,
	);

	$site_id = wp_insert_site( $data );
	if ( is_wp_error( $site_id ) ) {
		return false;
	}

	clean_blog_cache( $site_id );

	return $site_id;
}

/**
 * Install an empty blog.
 *
 * Creates the new blog tables and options. If calling this function
 * directly, be sure to use switch_to_blog() first, so that $wpdb
 * points to the new blog.
 *
 * @since MU (3.0.0)
 * @deprecated 5.1.0
 *
 * @global wpdb     $wpdb     WordPress database abstraction object.
 * @global WP_Roles $wp_roles WordPress role management object.
 *
 * @param int    $blog_id    The value returned by wp_insert_site().
 * @param string $blog_title The title of the new site.
 */
function install_blog( $blog_id, $blog_title = '' ) {
	global $wpdb, $wp_roles;

	_deprecated_function( __FUNCTION__, '5.1.0' );

	// Cast for security.
	$blog_id = (int) $blog_id;

	require_once ABSPATH . 'wp-admin/includes/upgrade.php';

	$suppress = $wpdb->suppress_errors();
	if ( $wpdb->get_results( "DESCRIBE {$wpdb->posts}" ) ) {
		die( '<h1>' . __( 'Already Installed' ) . '</h1><p>' . __( 'You appear to have already installed WordPress. To reinstall please clear your old database tables first.' ) . '</p></body></html>' );
	}
	$wpdb->suppress_errors( $suppress );

	$url = get_blogaddress_by_id( $blog_id );

	// Set everything up.
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
		if ( 'https' === parse_url( get_home_url( get_network()->site_id ), PHP_URL_SCHEME ) ) {
			$home = set_url_scheme( $home, 'https' );
		}
	}

	update_option( 'siteurl', $siteurl );
	update_option( 'home', $home );

	if ( get_site_option( 'ms_files_rewriting' ) ) {
		update_option( 'upload_path', UPLOADBLOGSDIR . "/$blog_id/files" );
	} else {
		update_option( 'upload_path', get_blog_option( get_network()->site_id, 'upload_path' ) );
	}

	update_option( 'blogname', wp_unslash( $blog_title ) );
	update_option( 'admin_email', '' );

	// Remove all permissions.
	$table_prefix = $wpdb->get_blog_prefix();
	delete_metadata( 'user', 0, $table_prefix . 'user_level', null, true );   // Delete all.
	delete_metadata( 'user', 0, $table_prefix . 'capabilities', null, true ); // Delete all.
}

/**
 * Set blog defaults.
 *
 * This function creates a row in the wp_blogs table.
 *
 * @since MU (3.0.0)
 * @deprecated MU
 * @deprecated Use wp_install_defaults()
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param int $blog_id Ignored in this function.
 * @param int $user_id
 */
function install_blog_defaults( $blog_id, $user_id ) {
	global $wpdb;

	_deprecated_function( __FUNCTION__, 'MU' );

	require_once ABSPATH . 'wp-admin/includes/upgrade.php';

	$suppress = $wpdb->suppress_errors();

	wp_install_defaults( $user_id );

	$wpdb->suppress_errors( $suppress );
}

/**
 * Update the status of a user in the database.
 *
 * Previously used in core to mark a user as spam or "ham" (not spam) in Multisite.
 *
 * @since 3.0.0
 * @deprecated 5.3.0 Use wp_update_user()
 * @see wp_update_user()
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param int    $id         The user ID.
 * @param string $pref       The column in the wp_users table to update the user's status
 *                           in (presumably user_status, spam, or deleted).
 * @param int    $value      The new status for the user.
 * @param null   $deprecated Deprecated as of 3.0.2 and should not be used.
 * @return int   The initially passed $value.
 */
function update_user_status( $id, $pref, $value, $deprecated = null ) {
	global $wpdb;

	_deprecated_function( __FUNCTION__, '5.3.0', 'wp_update_user()' );

	if ( null !== $deprecated ) {
		_deprecated_argument( __FUNCTION__, '3.0.2' );
	}

	$wpdb->update( $wpdb->users, array( sanitize_key( $pref ) => $value ), array( 'ID' => $id ) );

	$user = new WP_User( $id );
	clean_user_cache( $user );

	if ( 'spam' === $pref ) {
		if ( $value == 1 ) {
			/** This filter is documented in wp-includes/user.php */
			do_action( 'make_spam_user', $id );
		} else {
			/** This filter is documented in wp-includes/user.php */
			do_action( 'make_ham_user', $id );
		}
	}

	return $value;
}

/**
 * Maintains a canonical list of terms by syncing terms created for each blog with the global terms table.
 *
 * @since 3.0.0
 * @since 6.1.0 This function no longer does anything.
 * @deprecated 6.1.0
 *
 * @param int    $term_id    An ID for a term on the current blog.
 * @param string $deprecated Not used.
 * @return int An ID from the global terms table mapped from $term_id.
 */
function global_terms( $term_id, $deprecated = '' ) {
	_deprecated_function( __FUNCTION__, '6.1.0' );

	return $term_id;
}
