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

/** Include Multisite initialization functions */
require( ABSPATH . WPINC . '/ms-load.php' );
require( ABSPATH . WPINC . '/ms-default-constants.php' );

if ( defined( 'SUNRISE' ) )
	include_once( WP_CONTENT_DIR . '/sunrise.php' );

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

		if ( $current_site->domain === $domain && $current_site->path === $path ) {
			$current_blog = get_site_by_path( $domain, $path );
		} elseif ( '/' !== $current_site->path && $current_site->domain === $domain && 0 === strpos( $path, $current_site->path ) ) {
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
		 * If we're not dealing with one of these installs, then the important part is determing
		 * the network first, because we need the network's path to identify any sites.
		 */
		if ( ! $current_site = wp_cache_get( 'current_network', 'site-options' ) ) {
			// Are there even two networks installed?
			$one_network = $wpdb->get_row( "SELECT * FROM $wpdb->site LIMIT 2" ); // [sic]
			if ( 1 === $wpdb->num_rows ) {
				$current_site = wp_get_network( $one_network );
				wp_cache_set( 'current_network', 'site-options' );
			} elseif ( 0 === $wpdb->num_rows ) {
				ms_not_installed();
			}
		}
		if ( empty( $current_site ) ) {
			$current_site = get_network_by_path( $domain, $path, 1 );
		}

		if ( empty( $current_site ) ) {
			ms_not_installed();
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
			$current_site = wp_get_network( $current_blog->site_id ? $current_blog->site_id : 1 );
		} else {
			// If you don't have a site with the same domain/path as a network, you're pretty screwed, but:
			$current_site = get_network_by_path( $domain, $path, 1 );
		}
	}

	// The network declared by the site trumps any constants.
	if ( $current_blog && $current_blog->site_id != $current_site->id ) {
		$current_site = wp_get_network( $current_blog->site_id );
	}

	// If we don't have a network by now, we have a problem.
	if ( empty( $current_site ) ) {
		ms_not_installed();
	}

	// @todo What if the domain of the network doesn't match the current site?
	$current_site->cookie_domain = $current_site->domain;
	if ( 'www.' === substr( $current_site->cookie_domain, 0, 4 ) ) {
		$current_site->cookie_domain = substr( $current_site->cookie_domain, 4 );
	}

	// Figure out the current network's main site.
	if ( ! isset( $current_site->blog_id ) ) {
		if ( $current_blog && $current_blog->domain === $current_site->domain && $current_blog->path === $current_site->path ) {
			$current_site->blog_id = $current_blog->blog_id;
		} else {
			// @todo we should be able to cache the blog ID of a network's main site easily.
			$current_site->blog_id = $wpdb->get_var( $wpdb->prepare( "SELECT blog_id FROM $wpdb->blogs WHERE domain = %s AND path = %s",
				$current_site->domain, $current_site->path ) );
		}
	}

	// If we haven't figured out our site, give up.
	if ( empty( $current_blog ) ) {
		if ( defined( 'WP_INSTALLING' ) ) {
			$current_blog->blog_id = $blog_id = 1;

		} elseif ( is_subdomain_install() ) {
			// @todo This is only for an open registration subdomain network.
			if ( defined( 'NOBLOGREDIRECT' ) ) {
				if ( '%siteurl%' === NOBLOGREDIRECT ) {
					$destination = "http://" . $current_site->domain . $current_site->path;
				} else {
					$destination = NOBLOGREDIRECT;
				}
			} else {
				$destination = 'http://' . $current_site->domain . $current_site->path . 'wp-signup.php?new=' . str_replace( '.' . $current_site->domain, '', $domain );
			}
			header( 'Location: ' . $destination );
			exit;

		} else {
			if ( 0 !== strcasecmp( $current_site->domain, $domain ) ) {
				header( 'Location: http://' . $current_site->domain . $current_site->path );
				exit;
			}
			ms_not_installed();
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

if ( ! isset( $current_site->site_name ) ) {
	$current_site->site_name = get_site_option( 'site_name' );
	if ( ! $current_site->site_name ) {
		$current_site->site_name = ucfirst( $current_site->domain );
	}
}

// Define upload directory constants
ms_upload_constants();
