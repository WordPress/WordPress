<?php
/**
 * Used to set up and fix common variables and include
 * the Multisite procedural and class library.
 *
 * Allows for some configuration in wp-config.php (see ms-default-constants.php)
 *
 * @package WordPress
 * @subpackage Multisite
 * @since 3.0.0
 */

/** WP_Network class */
require_once( ABSPATH . WPINC . '/class-wp-network.php' );

/** Multisite loader */
require_once( ABSPATH . WPINC . '/ms-load.php' );

/** Default Multisite constants */
require_once( ABSPATH . WPINC . '/ms-default-constants.php' );

if ( defined( 'SUNRISE' ) ) {
	include_once( WP_CONTENT_DIR . '/sunrise.php' );
}

/** Check for and define SUBDOMAIN_INSTALL and the deprecated VHOST constant. */
ms_subdomain_constants();

if ( !isset( $current_site ) || !isset( $current_blog ) ) {

	// Given the domain and path, let's try to identify the network and site.
	// Usually, it's easier to query the site first, which declares its network.
	// In limited situations, though, we either can or must find the network first.

	$domain = strtolower( stripslashes( $_SERVER['HTTP_HOST'] ) );
	if ( substr( $domain, -3 ) == ':80' ) {
		$domain = substr( $domain, 0, -3 );
		$_SERVER['HTTP_HOST'] = substr( $_SERVER['HTTP_HOST'], 0, -3 );
	} elseif ( substr( $domain, -4 ) == ':443' ) {
		$domain = substr( $domain, 0, -4 );
		$_SERVER['HTTP_HOST'] = substr( $_SERVER['HTTP_HOST'], 0, -4 );
	}

	$path = stripslashes( $_SERVER['REQUEST_URI'] );
	if ( is_admin() ) {
		$path = preg_replace( '#(.*)/wp-admin/.*#', '$1/', $path );
	}
	list( $path ) = explode( '?', $path );

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

	} elseif ( ! is_subdomain_install() ) {
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
				ms_not_installed( $domain, $path );
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

			ms_not_installed( $domain, $path );
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

		ms_not_installed( $domain, $path );
	}

	// @todo Investigate when exactly this can occur.
	if ( empty( $current_blog ) && defined( 'WP_INSTALLING' ) ) {
		$current_blog = new stdClass;
		$current_blog->blog_id = $blog_id = 1;
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

		if ( is_subdomain_install() && ! defined( 'NOBLOGREDIRECT' ) ) {
			// For a "subdomain" install, redirect to the signup form specifically.
			$destination .= 'wp-signup.php?new=' . str_replace( '.' . $current_site->domain, '', $domain );
		} elseif ( is_subdomain_install() ) {
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
			ms_not_installed( $domain, $path );
		}

		header( 'Location: ' . $destination );
		exit;
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

	$blog_id = $current_blog->blog_id;
	$public  = $current_blog->public;

	if ( empty( $current_blog->site_id ) ) {
		// This dates to [MU134] and shouldn't be relevant anymore,
		// but it could be possible for arguments passed to insert_blog() etc.
		$current_blog->site_id = 1;
	}

	$site_id = $current_blog->site_id;
	wp_load_core_site_options( $site_id );
}

$wpdb->set_prefix( $table_prefix, false ); // $table_prefix can be set in sunrise.php
$wpdb->set_blog_id( $current_blog->blog_id, $current_blog->site_id );
$table_prefix = $wpdb->get_blog_prefix();
$_wp_switched_stack = array();
$switched = false;

// need to init cache again after blog_id is set
wp_start_object_cache();

if ( ! $current_site instanceof WP_Network ) {
	$current_site = new WP_Network( $current_site );
}

if ( empty( $current_site->site_name ) ) {
	$current_site->site_name = get_site_option( 'site_name' );
	if ( ! $current_site->site_name ) {
		$current_site->site_name = ucfirst( $current_site->domain );
	}
}

// Define upload directory constants
ms_upload_constants();
