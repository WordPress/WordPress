<?php
/**
 * Used to setup and fix common variables and include
 * the Multisite procedural and class library.
 *
 * Allows for some configuration in wp-config.php (see ms-default-constants.php)
 *
 * @package WordPress
 * @subpackage Multisite
 */

/** Include Multisite initialization functions */
require( ABSPATH . WPINC . '/ms-load.php' );
require( ABSPATH . WPINC . '/ms-default-constants.php' );

if ( defined( 'SUNRISE' ) )
	include_once( WP_CONTENT_DIR . '/sunrise.php' );

if ( isset( $current_site ) && isset( $current_blog ) )
	return;

$domain = addslashes( $_SERVER['HTTP_HOST'] );
if ( strpos( $domain, ':' ) ) {
	if ( substr( $domain, -3 ) == ':80' ) {
		$domain = substr( $domain, 0, -3 );
		$_SERVER['HTTP_HOST'] = substr( $_SERVER['HTTP_HOST'], 0, -3 );
	} elseif ( substr( $domain, -4 ) == ':443' ) {
		$domain = substr( $domain, 0, -4 );
		$_SERVER['HTTP_HOST'] = substr( $_SERVER['HTTP_HOST'], 0, -4 );
	} else {
		die( 'WPMU only works without the port number in the URL.' );
	}
}
$domain = preg_replace('/:.*$/', '', $domain); // Strip ports
if ( substr( $domain, -1 ) == '.' )
	$domain = substr( $domain, 0, -1 );

if ( substr( $domain, 0, 4 ) == 'www.' )
	$cookie_domain = substr( $domain, 4 );
else
	$cookie_domain = $domain;

$path = preg_replace( '|([a-z0-9-]+.php.*)|', '', $_SERVER['REQUEST_URI'] );
$path = str_replace ( '/wp-admin/', '/', $path );
$path = preg_replace( '|(/[a-z0-9-]+?/).*|', '$1', $path );

$current_site = wpmu_current_site();
if ( !isset( $current_site->blog_id ) )
	$current_site->blog_id = $wpdb->get_var( "SELECT blog_id FROM {$wpdb->blogs} WHERE domain='{$current_site->domain}' AND path='{$current_site->path}'" );

if ( is_subdomain_install() ) {
	$current_blog = wp_cache_get( 'current_blog_' . $domain, 'site-options' );
	if ( !$current_blog ) {
		$current_blog = $wpdb->get_row( $wpdb->prepare("SELECT * FROM $wpdb->blogs WHERE domain = %s", $domain) );
		if ( $current_blog )
			wp_cache_set( 'current_blog_' . $domain, $current_blog, 'site-options' );
	}
	if ( $current_blog != null && $current_blog->site_id != $current_site->id )
		$current_site = $wpdb->get_row( $wpdb->prepare("SELECT * FROM $wpdb->site WHERE id = %d", $current_blog->site_id) );
	else
		$blogname = substr( $domain, 0, strpos( $domain, '.' ) );
} else {
	$blogname = htmlspecialchars( substr( $_SERVER[ 'REQUEST_URI' ], strlen( $path ) ) );
	if ( strpos( $blogname, '/' ) )
		$blogname = substr( $blogname, 0, strpos( $blogname, '/' ) );
	if ( strpos( " ".$blogname, '?' ) )
		$blogname = substr( $blogname, 0, strpos( $blogname, '?' ) );
	$reserved_blognames = array( 'page', 'comments', 'blog', 'wp-admin', 'wp-includes', 'wp-content', 'files', 'feed' );
	if ( $blogname != '' && !in_array( $blogname, $reserved_blognames ) && !is_file( $blogname ) )
		$path = $path . $blogname . '/';
	$current_blog = wp_cache_get( 'current_blog_' . $domain . $path, 'site-options' );
	if ( !$current_blog ) {
		$current_blog = $wpdb->get_row( $wpdb->prepare("SELECT * FROM $wpdb->blogs WHERE domain = %s AND path = %s", $domain, $path) );
		if ( $current_blog )
			wp_cache_set( 'current_blog_' . $domain . $path, $current_blog, 'site-options' );
	}
}

if ( ! defined( 'WP_INSTALLING' ) && is_subdomain_install() && !is_object( $current_blog ) ) {

	if ( defined( 'NOBLOGREDIRECT' ) ) {
		$destination = constant( 'NOBLOGREDIRECT' );
		if ( $destination == '%siteurl%' )
			$destination = "http://" . $current_site->domain . $current_site->path;
		header( "Location: " .  $destination);
		die();
	} else {
		header( "Location: http://" . $current_site->domain . $current_site->path . "wp-signup.php?new=" . str_replace( '.' . $current_site->domain, '', $domain ) );
		die();
	}

}

if ( ! defined( 'WP_INSTALLING' ) ) {
	if ( $current_site && $current_blog == null ) {
		if ( $current_site->domain != $_SERVER[ 'HTTP_HOST' ] ) {
			header( "Location: http://" . $current_site->domain . $current_site->path );
			exit;
		}
		$current_blog = $wpdb->get_row( $wpdb->prepare("SELECT * FROM $wpdb->blogs WHERE domain = %s AND path = %s", $current_site->domain, $current_site->path) );
	}
	if ( $current_blog == false || $current_site == false )
		is_installed();
}

$blog_id = $current_blog->blog_id;
$public  = $current_blog->public;

if ( $current_blog->site_id == 0 || $current_blog->site_id == '' )
	$current_blog->site_id = 1;
$site_id = $current_blog->site_id;

$current_site = get_current_site_name( $current_site );

if ( $blog_id == false ) {
    // no blog found, are we installing? Check if the table exists.
    if ( defined('WP_INSTALLING') ) {
		$blog_id = $wpdb->get_var( "SELECT blog_id FROM $wpdb->blogs LIMIT 0,1" );
		if ( $blog_id == false ) {
		    // table doesn't exist. This is the first blog
		    $blog_id = 1;
		} else {
		    // table exists
		    // don't create record at this stage. we're obviously installing so it doesn't matter what the table vars below are like.
		    // default to using the "main" blog.
		    $blog_id = 1;
		}
		$current_blog->blog_id = $blog_id;
    } else {
		$check = $wpdb->get_results( "SELECT * FROM $wpdb->site" );
		if ( $check == false )
		    $msg = ': DB Tables Missing';
		else
		    $msg = '';
		die( "No Blog by that name on this system." . $msg );
    }
}

$wpdb->suppress_errors( false );

if ( '0' == $current_blog->public ) {
	// This just means the blog shouldn't show up in google, etc. Only to registered members
}

$wpdb->blogid = $current_blog->blog_id;
$wpdb->siteid = $current_blog->site_id;
$wpdb->set_prefix($table_prefix); // set up blog tables
$table_prefix = $wpdb->get_blog_prefix();

// Fix empty PHP_SELF
$PHP_SELF = $_SERVER['PHP_SELF'];
if ( empty($PHP_SELF) || ( empty($PHP_SELF) && !is_subdomain_install() && $current_blog->path != '/' ) )
	$_SERVER['PHP_SELF'] = $PHP_SELF = preg_replace("/(\?.*)?$/",'',$_SERVER["REQUEST_URI"]);

wp_cache_init(); // need to init cache again after blog_id is set
if ( function_exists('wp_cache_add_global_groups') ) { // need to add these again. Yes, it's an ugly hack
	wp_cache_add_global_groups(array ('users', 'userlogins', 'usermeta', 'site-options', 'site-lookup', 'blog-lookup', 'blog-details', 'rss'));
	wp_cache_add_non_persistent_groups(array( 'comment', 'counts', 'plugins' ));
}

ms_default_constants( 'uploads' );

?>
