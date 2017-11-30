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

/**
 * Objects representing the current network and current site.
 *
 * These may be populated through a custom `sunrise.php`. If not, then this
 * file will attempt to populate them based on the current request.
 *
 * @global WP_Network $current_site The current network.
 * @global object     $current_blog The current site.
 * @global string     $domain       Deprecated. The domain of the site found on load.
 *                                  Use `get_site()->domain` instead.
 * @global string     $path         Deprecated. The path of the site found on load.
 *                                  Use `get_site()->path` instead.
 * @global int        $site_id      Deprecated. The ID of the network found on load.
 *                                  Use `get_current_network_id()` instead.
 * @global bool       $public       Deprecated. Whether the site found on load is public.
 *                                  Use `get_site()->public` instead.
 *
 * @since 3.0.0
 */
global $current_site, $current_blog, $domain, $path, $site_id, $public;

/** WP_Network class */
require_once( ABSPATH . WPINC . '/class-wp-network.php' );

/** WP_Site class */
require_once( ABSPATH . WPINC . '/class-wp-site.php' );

/** Multisite loader */
require_once( ABSPATH . WPINC . '/ms-load.php' );

/** Default Multisite constants */
require_once( ABSPATH . WPINC . '/ms-default-constants.php' );

if ( defined( 'SUNRISE' ) ) {
	include_once( WP_CONTENT_DIR . '/sunrise.php' );
}

/** Check for and define SUBDOMAIN_INSTALL and the deprecated VHOST constant. */
ms_subdomain_constants();

// This block will process a request if the current network or current site objects
// have not been populated in the global scope through something like `sunrise.php`.
if ( ! isset( $current_site ) || ! isset( $current_blog ) ) {

	$domain = strtolower( stripslashes( $_SERVER['HTTP_HOST'] ) );
	if ( substr( $domain, -3 ) == ':80' ) {
		$domain               = substr( $domain, 0, -3 );
		$_SERVER['HTTP_HOST'] = substr( $_SERVER['HTTP_HOST'], 0, -3 );
	} elseif ( substr( $domain, -4 ) == ':443' ) {
		$domain               = substr( $domain, 0, -4 );
		$_SERVER['HTTP_HOST'] = substr( $_SERVER['HTTP_HOST'], 0, -4 );
	}

	$path = stripslashes( $_SERVER['REQUEST_URI'] );
	if ( is_admin() ) {
		$path = preg_replace( '#(.*)/wp-admin/.*#', '$1/', $path );
	}
	list( $path ) = explode( '?', $path );

	$bootstrap_result = ms_load_current_site_and_network( $domain, $path, is_subdomain_install() );

	if ( true === $bootstrap_result ) {
		// `$current_blog` and `$current_site are now populated.
	} elseif ( false === $bootstrap_result ) {
		ms_not_installed( $domain, $path );
	} else {
		header( 'Location: ' . $bootstrap_result );
		exit;
	}
	unset( $bootstrap_result );

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
$table_prefix       = $wpdb->get_blog_prefix();
$_wp_switched_stack = array();
$switched           = false;

// need to init cache again after blog_id is set
wp_start_object_cache();

if ( ! $current_site instanceof WP_Network ) {
	$current_site = new WP_Network( $current_site );
}

if ( ! $current_blog instanceof WP_Site ) {
	$current_blog = new WP_Site( $current_blog );
}

// Define upload directory constants
ms_upload_constants();

/**
 * Fires after the current site and network have been detected and loaded
 * in multisite's bootstrap.
 *
 * @since 4.6.0
 */
do_action( 'ms_loaded' );
