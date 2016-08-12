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

	return ( defined( 'VHOST' ) && VHOST == 'yes' );
}

/**
 * Returns array of network plugin files to be included in global scope.
 *
 * The default directory is wp-content/plugins. To change the default directory
 * manually, define `WP_PLUGIN_DIR` and `WP_PLUGIN_URL` in `wp-config.php`.
 *
 * @access private
 * @since 3.1.0
 *
 * @return array Files to include.
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
 * @return true|string Returns true on success, or drop-in file to include.
 */
function ms_site_check() {

	/**
	 * Filters checking the status of the current blog.
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

	$blog = get_blog_details();

	if ( '1' == $blog->deleted ) {
		if ( file_exists( WP_CONTENT_DIR . '/blog-deleted.php' ) )
			return WP_CONTENT_DIR . '/blog-deleted.php';
		else
			wp_die( __( 'This site is no longer available.' ), '', array( 'response' => 410 ) );
	}

	if ( '2' == $blog->deleted ) {
		if ( file_exists( WP_CONTENT_DIR . '/blog-inactive.php' ) ) {
			return WP_CONTENT_DIR . '/blog-inactive.php';
		} else {
			$admin_email = str_replace( '@', ' AT ', get_site_option( 'admin_email', 'support@' . get_current_site()->domain ) );
			wp_die(
				/* translators: %s: admin email link */
				sprintf( __( 'This site has not been activated yet. If you are having problems activating your site, please contact %s.' ),
					sprintf( '<a href="mailto:%s">%s</a>', $admin_email )
				)
			);
		}
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
 * Retrieve the closest matching network for a domain and path.
 *
 * @since 3.9.0
 *
 * @internal In 4.4.0, converted to a wrapper for WP_Network::get_by_path()
 *
 * @param string   $domain   Domain to check.
 * @param string   $path     Path to check.
 * @param int|null $segments Path segments to use. Defaults to null, or the full path.
 * @return WP_Network|false Network object if successful. False when no network is found.
 */
function get_network_by_path( $domain, $path, $segments = null ) {
	return WP_Network::get_by_path( $domain, $path, $segments );
}

/**
 * Retrieve an object containing information about the requested network.
 *
 * @since 3.9.0
 *
 * @internal In 4.6.0, converted to use get_network()
 *
 * @param object|int $network The network's database row or ID.
 * @return WP_Network|false Object containing network information if found, false if not.
 */
function wp_get_network( $network ) {
	$network = get_network( $network );
	if ( null === $network ) {
		return false;
	}

	return $network;
}

/**
 * Retrieve a site object by its domain and path.
 *
 * @since 3.9.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param string   $domain   Domain to check.
 * @param string   $path     Path to check.
 * @param int|null $segments Path segments to use. Defaults to null, or the full path.
 * @return object|false Site object if successful. False when no site is found.
 */
function get_site_by_path( $domain, $path, $segments = null ) {
	$path_segments = array_filter( explode( '/', trim( $path, '/' ) ) );

	/**
	 * Filters the number of path segments to consider when searching for a site.
	 *
	 * @since 3.9.0
	 *
	 * @param int|null $segments The number of path segments to consider. WordPress by default looks at
	 *                           one path segment following the network path. The function default of
	 *                           null only makes sense when you know the requested path should match a site.
	 * @param string   $domain   The requested domain.
	 * @param string   $path     The requested path, in full.
	 */
	$segments = apply_filters( 'site_by_path_segments_count', $segments, $domain, $path );

	if ( null !== $segments && count( $path_segments ) > $segments ) {
		$path_segments = array_slice( $path_segments, 0, $segments );
	}

	$paths = array();

	while ( count( $path_segments ) ) {
		$paths[] = '/' . implode( '/', $path_segments ) . '/';
		array_pop( $path_segments );
	}

	$paths[] = '/';

	/**
	 * Determine a site by its domain and path.
	 *
	 * This allows one to short-circuit the default logic, perhaps by
	 * replacing it with a routine that is more optimal for your setup.
	 *
	 * Return null to avoid the short-circuit. Return false if no site
	 * can be found at the requested domain and path. Otherwise, return
	 * a site object.
	 *
	 * @since 3.9.0
	 *
	 * @param null|bool|object $site     Site value to return by path.
	 * @param string           $domain   The requested domain.
	 * @param string           $path     The requested path, in full.
	 * @param int|null         $segments The suggested number of paths to consult.
	 *                                   Default null, meaning the entire path was to be consulted.
	 * @param array            $paths    The paths to search for, based on $path and $segments.
	 */
	$pre = apply_filters( 'pre_get_site_by_path', null, $domain, $path, $segments, $paths );
	if ( null !== $pre ) {
		return $pre;
	}

	/*
	 * @todo
	 * get_blog_details(), caching, etc. Consider alternative optimization routes,
	 * perhaps as an opt-in for plugins, rather than using the pre_* filter.
	 * For example: The segments filter can expand or ignore paths.
	 * If persistent caching is enabled, we could query the DB for a path <> '/'
	 * then cache whether we can just always ignore paths.
	 */

	// Either www or non-www is supported, not both. If a www domain is requested,
	// query for both to provide the proper redirect.
	$domains = array( $domain );
	if ( 'www.' === substr( $domain, 0, 4 ) ) {
		$domains[] = substr( $domain, 4 );
	}

	$args = array(
		'domain__in' => $domains,
		'path__in' => $paths,
		'number' => 1,
	);

	if ( count( $domains ) > 1 ) {
		$args['orderby']['domain_length'] = 'DESC';
	}

	if ( count( $paths ) > 1 ) {
		$args['orderby']['path_length'] = 'DESC';
	}

	$result = get_sites( $args );
	$site = array_shift( $result );

	if ( $site ) {
		// @todo get_blog_details()
		return $site;
	}

	return false;
}

/**
 * Identifies the network and site of a requested domain and path and populates the
 * corresponding network and site global objects as part of the multisite bootstrap process.
 *
 * Prior to 4.6.0, this was a procedural block in `ms-settings.php`. It was wrapped into
 * a function to facilitate unit tests. It should not be used outside of core.
 *
 * Usually, it's easier to query the site first, which then declares its network.
 * In limited situations, we either can or must find the network first.
 *
 * If a network and site are found, a `true` response will be returned so that the
 * request can continue.
 *
 * If neither a network or site is found, `false` or a URL string will be returned
 * so that either an error can be shown or a redirect can occur.
 *
 * @since 4.6.0
 * @access private
 *
 * @global wpdb       $wpdb         WordPress database abstraction object.
 * @global WP_Network $current_site The current network.
 * @global WP_Site    $current_blog The current site.
 *
 * @param string $domain    The requested domain.
 * @param string $path      The requested path.
 * @param bool   $subdomain Optional. Whether a subdomain (true) or subdirectory (false) configuration.
 *                          Default false.
 * @return bool|string True if bootstrap successfully populated `$current_blog` and `$current_site`.
 *                     False if bootstrap could not be properly completed.
 *                     Redirect URL if parts exist, but the request as a whole can not be fulfilled.
 */
function ms_load_current_site_and_network( $domain, $path, $subdomain = false ) {
	global $wpdb, $current_site, $current_blog;

	// If the network is defined in wp-config.php, we can simply use that.
	if ( defined( 'DOMAIN_CURRENT_SITE' ) && defined( 'PATH_CURRENT_SITE' ) ) {
		$current_site = new stdClass;
		$current_site->id = defined( 'SITE_ID_CURRENT_SITE' ) ? SITE_ID_CURRENT_SITE : 1;
		$current_site->domain = DOMAIN_CURRENT_SITE;
		$current_site->path = PATH_CURRENT_SITE;
		if ( defined( 'BLOG_ID_CURRENT_SITE' ) ) {
			$current_site->blog_id = BLOG_ID_CURRENT_SITE;
		} elseif ( defined( 'BLOGID_CURRENT_SITE' ) ) { // deprecated.
			$current_site->blog_id = BLOGID_CURRENT_SITE;
		}

		if ( 0 === strcasecmp( $current_site->domain, $domain ) && 0 === strcasecmp( $current_site->path, $path ) ) {
			$current_blog = get_site_by_path( $domain, $path );
		} elseif ( '/' !== $current_site->path && 0 === strcasecmp( $current_site->domain, $domain ) && 0 === stripos( $path, $current_site->path ) ) {
			// If the current network has a path and also matches the domain and path of the request,
			// we need to look for a site using the first path segment following the network's path.
			$current_blog = get_site_by_path( $domain, $path, 1 + count( explode( '/', trim( $current_site->path, '/' ) ) ) );
		} else {
			// Otherwise, use the first path segment (as usual).
			$current_blog = get_site_by_path( $domain, $path, 1 );
		}

	} elseif ( ! $subdomain ) {
		/*
		 * A "subdomain" install can be re-interpreted to mean "can support any domain".
		 * If we're not dealing with one of these installs, then the important part is determining
		 * the network first, because we need the network's path to identify any sites.
		 */
		if ( ! $current_site = wp_cache_get( 'current_network', 'site-options' ) ) {
			// Are there even two networks installed?
			$one_network = $wpdb->get_row( "SELECT * FROM $wpdb->site LIMIT 2" ); // [sic]
			if ( 1 === $wpdb->num_rows ) {
				$current_site = new WP_Network( $one_network );
				wp_cache_add( 'current_network', $current_site, 'site-options' );
			} elseif ( 0 === $wpdb->num_rows ) {
				// A network not found hook should fire here.
				return false;
			}
		}

		if ( empty( $current_site ) ) {
			$current_site = WP_Network::get_by_path( $domain, $path, 1 );
		}

		if ( empty( $current_site ) ) {
			/**
			 * Fires when a network cannot be found based on the requested domain and path.
			 *
			 * At the time of this action, the only recourse is to redirect somewhere
			 * and exit. If you want to declare a particular network, do so earlier.
			 *
			 * @since 4.4.0
			 *
			 * @param string $domain       The domain used to search for a network.
			 * @param string $path         The path used to search for a path.
			 */
			do_action( 'ms_network_not_found', $domain, $path );

			return false;
		} elseif ( $path === $current_site->path ) {
			$current_blog = get_site_by_path( $domain, $path );
		} else {
			// Search the network path + one more path segment (on top of the network path).
			$current_blog = get_site_by_path( $domain, $path, substr_count( $current_site->path, '/' ) );
		}
	} else {
		// Find the site by the domain and at most the first path segment.
		$current_blog = get_site_by_path( $domain, $path, 1 );
		if ( $current_blog ) {
			$current_site = WP_Network::get_instance( $current_blog->site_id ? $current_blog->site_id : 1 );
		} else {
			// If you don't have a site with the same domain/path as a network, you're pretty screwed, but:
			$current_site = WP_Network::get_by_path( $domain, $path, 1 );
		}
	}

	// The network declared by the site trumps any constants.
	if ( $current_blog && $current_blog->site_id != $current_site->id ) {
		$current_site = WP_Network::get_instance( $current_blog->site_id );
	}

	// No network has been found, bail.
	if ( empty( $current_site ) ) {
		/** This action is documented in wp-includes/ms-settings.php */
		do_action( 'ms_network_not_found', $domain, $path );

		return false;
	}

	// During activation of a new subdomain, the requested site does not yet exist.
	if ( empty( $current_blog ) && wp_installing() ) {
		$current_blog = new stdClass;
		$current_blog->blog_id = $blog_id = 1;
		$current_blog->public = 1;
	}

	// No site has been found, bail.
	if ( empty( $current_blog ) ) {
		// We're going to redirect to the network URL, with some possible modifications.
		$scheme = is_ssl() ? 'https' : 'http';
		$destination = "$scheme://{$current_site->domain}{$current_site->path}";

		/**
		 * Fires when a network can be determined but a site cannot.
		 *
		 * At the time of this action, the only recourse is to redirect somewhere
		 * and exit. If you want to declare a particular site, do so earlier.
		 *
		 * @since 3.9.0
		 *
		 * @param object $current_site The network that had been determined.
		 * @param string $domain       The domain used to search for a site.
		 * @param string $path         The path used to search for a site.
		 */
		do_action( 'ms_site_not_found', $current_site, $domain, $path );

		if ( $subdomain && ! defined( 'NOBLOGREDIRECT' ) ) {
			// For a "subdomain" install, redirect to the signup form specifically.
			$destination .= 'wp-signup.php?new=' . str_replace( '.' . $current_site->domain, '', $domain );
		} elseif ( $subdomain ) {
			// For a "subdomain" install, the NOBLOGREDIRECT constant
			// can be used to avoid a redirect to the signup form.
			// Using the ms_site_not_found action is preferred to the constant.
			if ( '%siteurl%' !== NOBLOGREDIRECT ) {
				$destination = NOBLOGREDIRECT;
			}
		} elseif ( 0 === strcasecmp( $current_site->domain, $domain ) ) {
			/*
			 * If the domain we were searching for matches the network's domain,
			 * it's no use redirecting back to ourselves -- it'll cause a loop.
			 * As we couldn't find a site, we're simply not installed.
			 */
			return false;
		}

		return $destination;
	}

	// Figure out the current network's main site.
	if ( empty( $current_site->blog_id ) ) {
		if ( $current_blog->domain === $current_site->domain && $current_blog->path === $current_site->path ) {
			$current_site->blog_id = $current_blog->blog_id;
		} elseif ( ! $current_site->blog_id = wp_cache_get( 'network:' . $current_site->id . ':main_site', 'site-options' ) ) {
			$current_site->blog_id = $wpdb->get_var( $wpdb->prepare( "SELECT blog_id FROM $wpdb->blogs WHERE domain = %s AND path = %s",
				$current_site->domain, $current_site->path ) );
			wp_cache_add( 'network:' . $current_site->id . ':main_site', $current_site->blog_id, 'site-options' );
		}
	}

	return true;
}

/**
 * Displays a failure message.
 *
 * Used when a blog's tables do not exist. Checks for a missing $wpdb->site table as well.
 *
 * @access private
 * @since 3.0.0
 * @since 4.4.0 The `$domain` and `$path` parameters were added.
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param string $domain The requested domain for the error to reference.
 * @param string $path   The requested path for the error to reference.
 */
function ms_not_installed( $domain, $path ) {
	global $wpdb;

	if ( ! is_admin() ) {
		dead_db();
	}

	wp_load_translations_early();

	$title = __( 'Error establishing a database connection' );

	$msg  = '<h1>' . $title . '</h1>';
	$msg .= '<p>' . __( 'If your site does not display, please contact the owner of this network.' ) . '';
	$msg .= ' ' . __( 'If you are the owner of this network please check that MySQL is running properly and all tables are error free.' ) . '</p>';
	$query = $wpdb->prepare( "SHOW TABLES LIKE %s", $wpdb->esc_like( $wpdb->site ) );
	if ( ! $wpdb->get_var( $query ) ) {
		$msg .= '<p>' . sprintf(
			/* translators: %s: table name */
			__( '<strong>Database tables are missing.</strong> This means that MySQL is not running, WordPress was not installed properly, or someone deleted %s. You really should look at your database now.' ),
			'<code>' . $wpdb->site . '</code>'
		) . '</p>';
	} else {
		$msg .= '<p>' . sprintf(
			/* translators: 1: site url, 2: table name, 3: database name */
			__( '<strong>Could not find site %1$s.</strong> Searched for table %2$s in database %3$s. Is that right?' ),
			'<code>' . rtrim( $domain . $path, '/' ) . '</code>',
			'<code>' . $wpdb->blogs . '</code>',
			'<code>' . DB_NAME . '</code>'
		) . '</p>';
	}
	$msg .= '<p><strong>' . __( 'What do I do now?' ) . '</strong> ';
	/* translators: %s: Codex URL */
	$msg .= sprintf( __( 'Read the <a href="%s" target="_blank">bug report</a> page. Some of the guidelines there may help you figure out what went wrong.' ),
		__( 'https://codex.wordpress.org/Debugging_a_WordPress_Network' )
	);
	$msg .= ' ' . __( 'If you&#8217;re still stuck with this message, then check that your database contains the following tables:' ) . '</p><ul>';
	foreach ( $wpdb->tables('global') as $t => $table ) {
		if ( 'sitecategories' == $t )
			continue;
		$msg .= '<li>' . $table . '</li>';
	}
	$msg .= '</ul>';

	wp_die( $msg, $title, array( 'response' => 500 ) );
}

/**
 * This deprecated function formerly set the site_name property of the $current_site object.
 *
 * This function simply returns the object, as before.
 * The bootstrap takes care of setting site_name.
 *
 * @access private
 * @since 3.0.0
 * @deprecated 3.9.0 Use get_current_site() instead.
 *
 * @param object $current_site
 * @return object
 */
function get_current_site_name( $current_site ) {
	_deprecated_function( __FUNCTION__, '3.9.0', 'get_current_site()' );
	return $current_site;
}

/**
 * This deprecated function managed much of the site and network loading in multisite.
 *
 * The current bootstrap code is now responsible for parsing the site and network load as
 * well as setting the global $current_site object.
 *
 * @access private
 * @since 3.0.0
 * @deprecated 3.9.0
 *
 * @global object $current_site
 *
 * @return object
 */
function wpmu_current_site() {
	global $current_site;
	_deprecated_function( __FUNCTION__, '3.9.0' );
	return $current_site;
}
