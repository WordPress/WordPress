<?php
/**
 * These functions are needed to load Multisite.
 *
 * @since 3.0.0
 *
 * @package WordPress
 * @subpackage Multisite
 */

/**
 * Whether a subdomain configuration is enabled.
 *
 * @since 3.0.0
 *
 * @return bool True if subdomain configuration is enabled, false otherwise.
 */
function is_subdomain_install() {
	if ( defined('SUBDOMAIN_INSTALL') )
		return SUBDOMAIN_INSTALL;

	if ( defined('VHOST') && VHOST == 'yes' )
		return true;

	return false;
}

/**
 * Returns array of network plugin files to be included in global scope.
 *
 * The default directory is wp-content/plugins. To change the default directory
 * manually, define <code>WP_PLUGIN_DIR</code> and <code>WP_PLUGIN_URL</code>
 * in wp-config.php.
 *
 * @access private
 * @since 3.1.0
 * @return array Files to include
 */
function wp_get_active_network_plugins() {
	$active_plugins = (array) get_site_option( 'active_sitewide_plugins', array() );
	if ( empty( $active_plugins ) )
		return array();

	$plugins = array();
	$active_plugins = array_keys( $active_plugins );
	sort( $active_plugins );

	foreach ( $active_plugins as $plugin ) {
		if ( ! validate_file( $plugin ) // $plugin must validate as file
			&& '.php' == substr( $plugin, -4 ) // $plugin must end with '.php'
			&& file_exists( WP_PLUGIN_DIR . '/' . $plugin ) // $plugin must exist
			)
		$plugins[] = WP_PLUGIN_DIR . '/' . $plugin;
	}
	return $plugins;
}

/**
 * Checks status of current blog.
 *
 * Checks if the blog is deleted, inactive, archived, or spammed.
 *
 * Dies with a default message if the blog does not pass the check.
 *
 * To change the default message when a blog does not pass the check,
 * use the wp-content/blog-deleted.php, blog-inactive.php and
 * blog-suspended.php drop-ins.
 *
 * @since 3.0.0
 *
 * @return bool|string Returns true on success, or drop-in file to include.
 */
function ms_site_check() {
	$blog = get_blog_details();

	/**
	 * Filter checking the status of the current blog.
	 *
	 * @since 3.0.0
	 *
	 * @param bool null Whether to skip the blog status check. Default null.
	*/
	$check = apply_filters( 'ms_site_check', null );
	if ( null !== $check )
		return true;

	// Allow super admins to see blocked sites
	if ( is_super_admin() )
		return true;

	if ( '1' == $blog->deleted ) {
		if ( file_exists( WP_CONTENT_DIR . '/blog-deleted.php' ) )
			return WP_CONTENT_DIR . '/blog-deleted.php';
		else
			wp_die( __( 'This user has elected to delete their account and the content is no longer available.' ), '', array( 'response' => 410 ) );
	}

	if ( '2' == $blog->deleted ) {
		if ( file_exists( WP_CONTENT_DIR . '/blog-inactive.php' ) )
			return WP_CONTENT_DIR . '/blog-inactive.php';
		else
			wp_die( sprintf( __( 'This site has not been activated yet. If you are having problems activating your site, please contact <a href="mailto:%1$s">%1$s</a>.' ), str_replace( '@', ' AT ', get_site_option( 'admin_email', 'support@' . get_current_site()->domain ) ) ) );
	}

	if ( $blog->archived == '1' || $blog->spam == '1' ) {
		if ( file_exists( WP_CONTENT_DIR . '/blog-suspended.php' ) )
			return WP_CONTENT_DIR . '/blog-suspended.php';
		else
			wp_die( __( 'This site has been archived or suspended.' ), '', array( 'response' => 410 ) );
	}

	return true;
}

/**
 * Sets current site name.
 *
 * @access private
 * @since 3.0.0
 * @return object $current_site object with site_name
 */
function get_current_site_name( $current_site ) {
	global $wpdb;

	$current_site->site_name = wp_cache_get( $current_site->id . ':site_name', 'site-options' );
	if ( ! $current_site->site_name ) {
		$current_site->site_name = $wpdb->get_var( $wpdb->prepare( "SELECT meta_value FROM $wpdb->sitemeta WHERE site_id = %d AND meta_key = 'site_name'", $current_site->id ) );
		if ( ! $current_site->site_name )
			$current_site->site_name = ucfirst( $current_site->domain );
		wp_cache_set( $current_site->id . ':site_name', $current_site->site_name, 'site-options' );
	}

	return $current_site;
}

/**
 * Retrieve a network object by its domain and path.
 *
 * @since 3.9.0
 *
 * @param string $domain Domain to check.
 * @param string $path   Path to check.
 * @return object|bool Network object if successful. False when no network is found.
 */
function get_network_by_path( $domain, $path ) {
	global $wpdb;

	$network_id = false;

	$domains = $exact_domains = array( $domain );
	$pieces = explode( '.', $domain );

	// It's possible one domain to search is 'com', but it might as well
	// be 'localhost' or some other locally mapped domain.
	while ( array_shift( $pieces ) ) {
		if ( $pieces ) {
			$domains[] = implode( '.', $pieces );
		}
	}

	if ( '/' !== $path ) {
		$paths = array( '/', $path );
	} else {
		$paths = array( '/' );
	}

	$search_domains = "'" . implode( "', '", $wpdb->_escape( $domains ) ) . "'";
	$paths = "'" . implode( "', '", $wpdb->_escape( $paths ) ) . "'";

	$networks = $wpdb->get_results( "SELECT id, domain, path FROM $wpdb->site
		WHERE domain IN ($search_domains) AND path IN ($paths)
		ORDER BY CHAR_LENGTH(domain) DESC, CHAR_LENGTH(path) DESC" );

	/*
	 * Domains are sorted by length of domain, then by length of path.
	 * The domain must match for the path to be considered. Otherwise,
	 * a network with the path of / will suffice.
	 */
	$found = false;
	foreach ( $networks as $network ) {
		if ( $network->domain === $domain || "www.$network->domain" === $domain ) {
			if ( $network->path === $path ) {
				$found = true;
				break;
			}
		}
		if ( $network->path === '/' ) {
			$found = true;
			break;
		}
	}

	if ( $found ) {
		$network = wp_get_network( $network );

		return $network;
	}

	return false;
}

/**
 * Retrieve an object containing information about the requested network.
 *
 * @since 3.9.0
 *
 * @param int $network_id The network's DB row or ID.
 * @return mixed Object containing network information if found, false if not.
 */
function wp_get_network( $network ) {
	global $wpdb;

	if ( ! is_object( $network ) ) {
		$network = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $wpdb->site WHERE id = %d", $network ) );
		if ( ! $network ) {
			return false;
		}
	}

	return $network;
}

/**
 * Sets current_site object.
 *
 * @access private
 * @since 3.0.0
 * @return object $current_site object
 */
function wpmu_current_site() {
	global $wpdb, $current_site, $domain, $path;

	if ( empty( $current_site ) )
		$current_site = new stdClass;

	// 1. If constants are defined, that's our network.
	if ( defined( 'DOMAIN_CURRENT_SITE' ) && defined( 'PATH_CURRENT_SITE' ) ) {
		$current_site->id = defined( 'SITE_ID_CURRENT_SITE' ) ? SITE_ID_CURRENT_SITE : 1;
		$current_site->domain = DOMAIN_CURRENT_SITE;
		$current_site->path   = $path = PATH_CURRENT_SITE;
		if ( defined( 'BLOG_ID_CURRENT_SITE' ) )
			$current_site->blog_id = BLOG_ID_CURRENT_SITE;
		elseif ( defined( 'BLOGID_CURRENT_SITE' ) ) // deprecated.
			$current_site->blog_id = BLOGID_CURRENT_SITE;

	// 2. Pull the network from cache, if possible.
	} elseif ( ! $current_site = wp_cache_get( 'current_site', 'site-options' ) ) {

		// 3. See if they have only one network.
		$networks = $wpdb->get_col( "SELECT id FROM $wpdb->site LIMIT 2" );

		if ( count( $networks ) <= 1 ) {
			$current_site = wp_get_network( $networks[0]->id );

			$current_site->blog_id = $wpdb->get_var( $wpdb->prepare( "SELECT blog_id
				FROM $wpdb->blogs WHERE domain = %s AND path = %s",
				$current_site->domain, $current_site->path ) );

			wp_cache_set( 'current_site', 'site-options' );

		// 4. Multiple networks are in play. Determine which via domain and path.
		} else {
			// Find the first path segment.
			$path = substr( $_SERVER['REQUEST_URI'], 0, 1 + strpos( $_SERVER['REQUEST_URI'], '/', 1 ) );
			$current_site = get_network_by_path( $domain, $path );

			// Option 1. We did not find anything.
			if ( ! $current_site ) {
				wp_load_translations_early();
				wp_die( __( 'No site defined on this host. If you are the owner of this site, please check <a href="http://codex.wordpress.org/Debugging_a_WordPress_Network">Debugging a WordPress Network</a> for help.' ) );
			}
		}
	}

	// Option 2. We found something. Load up site meta and return.
	wp_load_core_site_options();
	$current_site = get_current_site_name( $current_site );
	return $current_site;
}

/**
 * Displays a failure message.
 *
 * Used when a blog's tables do not exist. Checks for a missing $wpdb->site table as well.
 *
 * @access private
 * @since 3.0.0
 */
function ms_not_installed() {
	global $wpdb, $domain, $path;

	wp_load_translations_early();

	$title = __( 'Error establishing a database connection' );
	$msg  = '<h1>' . $title . '</h1>';
	if ( ! is_admin() )
		die( $msg );
	$msg .= '<p>' . __( 'If your site does not display, please contact the owner of this network.' ) . '';
	$msg .= ' ' . __( 'If you are the owner of this network please check that MySQL is running properly and all tables are error free.' ) . '</p>';
	if ( ! $wpdb->get_var( "SHOW TABLES LIKE '$wpdb->site'" ) )
		$msg .= '<p>' . sprintf( __( '<strong>Database tables are missing.</strong> This means that MySQL is not running, WordPress was not installed properly, or someone deleted <code>%s</code>. You really should look at your database now.' ), $wpdb->site ) . '</p>';
	else
		$msg .= '<p>' . sprintf( __( '<strong>Could not find site <code>%1$s</code>.</strong> Searched for table <code>%2$s</code> in database <code>%3$s</code>. Is that right?' ), rtrim( $domain . $path, '/' ), $wpdb->blogs, DB_NAME ) . '</p>';
	$msg .= '<p><strong>' . __( 'What do I do now?' ) . '</strong> ';
	$msg .= __( 'Read the <a target="_blank" href="http://codex.wordpress.org/Debugging_a_WordPress_Network">bug report</a> page. Some of the guidelines there may help you figure out what went wrong.' );
	$msg .= ' ' . __( 'If you&#8217;re still stuck with this message, then check that your database contains the following tables:' ) . '</p><ul>';
	foreach ( $wpdb->tables('global') as $t => $table ) {
		if ( 'sitecategories' == $t )
			continue;
		$msg .= '<li>' . $table . '</li>';
	}
	$msg .= '</ul>';

	wp_die( $msg, $title );
}
