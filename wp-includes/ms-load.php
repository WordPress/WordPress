<?php
/**
 * Used to setup and fix common variables and include
 * the WordPress procedural and class library.
 *
 * You should not have to change this file and allows
 * for some configuration in wp-config.php.
 *
 * @since 3.0
 *
 * @package WordPress
 */
if ( defined( 'SUNRISE' ) )
	include_once( WP_CONTENT_DIR . '/sunrise.php' );

require( ABSPATH . WPINC . '/ms-settings.php' );
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

if ( !defined( 'UPLOADBLOGSDIR' ) )
	define( 'UPLOADBLOGSDIR', 'wp-content/blogs.dir' );

if ( !defined( 'UPLOADS' ) )
	define( 'UPLOADS', UPLOADBLOGSDIR . "/{$wpdb->blogid}/files/" );

if ( !defined( 'BLOGUPLOADDIR' ) )
	define( 'BLOGUPLOADDIR', WP_CONTENT_DIR . "/blogs.dir/{$wpdb->blogid}/files/" );

function ms_network_settings() {
	global $wpdb, $current_site, $cookiehash;

	if ( !isset($current_site->site_name) )
		$current_site->site_name = get_site_option('site_name');

	if ( $current_site->site_name == false )
		$current_site->site_name = ucfirst( $current_site->domain );

	$wpdb->hide_errors();
}

function ms_network_plugins() {
	$network_plugins = array();
	$deleted_sitewide_plugins = array();
	$wpmu_sitewide_plugins = (array) maybe_unserialize( get_site_option( 'wpmu_sitewide_plugins' ) );
	foreach ( $wpmu_sitewide_plugins as $plugin_file => $activation_time ) {
		if ( !$plugin_file )
			continue;

		if ( !file_exists( WP_PLUGIN_DIR . '/' . $plugin_file ) )
			$deleted_sitewide_plugins[] = $plugin_file;
		else
			$network_plugins[] = WP_PLUGIN_DIR . '/' . $plugin_file;
	}

	if ( !empty( $deleted_sitewide_plugins ) ) {
		$active_sitewide_plugins = maybe_unserialize( get_site_option( 'active_sitewide_plugins' ) );

		/* Remove any deleted plugins from the wpmu_sitewide_plugins array */
		foreach ( $deleted_sitewide_plugins as $plugin_file ) {
			unset( $wpmu_sitewide_plugins[$plugin_file] );
			unset( $active_sitewide_plugins[$plugin_file] );
		}

		update_site_option( 'wpmu_sitewide_plugins', $wpmu_sitewide_plugins );
		update_site_option( 'active_sitewide_plugins', $wpmu_sitewide_plugins );
	}

	return $network_plugins;
}

function ms_site_check() {
	global $wpdb, $current_blog;

	$wpdb->show_errors();

	if ( '1' == $current_blog->deleted ) {
			if ( file_exists( WP_CONTENT_DIR . '/blog-deleted.php' ) ) {
					return WP_CONTENT_DIR . '/blog-deleted.php';
			} else {
					header('HTTP/1.1 410 Gone');
					wp_die(__('This user has elected to delete their account and the content is no longer available.'));
			}
	} elseif ( '2' == $current_blog->deleted ) {
			if ( file_exists( WP_CONTENT_DIR . '/blog-inactive.php' ) )
				return WP_CONTENT_DIR . '/blog-inactive.php';
			else
				wp_die( sprintf( __( 'This blog has not been activated yet. If you are having problems activating your blog, please contact <a href="mailto:%1$s">%1$s</a>.' ), str_replace( '@', ' AT ', get_site_option( 'admin_email', "support@{$current_site->domain}" ) ) ) );
	}

	if ( $current_blog->archived == '1' || $current_blog->spam == '1' ) {
			if ( file_exists( WP_CONTENT_DIR . '/blog-suspended.php' ) ) {
					return WP_CONTENT_DIR . '/blog-suspended.php';
			} else {
					header('HTTP/1.1 410 Gone');
					wp_die(__('This blog has been archived or suspended.'));
			}
	}

	return true;
}

function ms_network_cookies() {
	global $current_site;
	/**
	 * It is possible to define this in wp-config.php
	 * @since 1.2.0
	 */
	if ( !defined( 'COOKIEPATH' ) )
			define( 'COOKIEPATH', $current_site->path );

	/**
	 * It is possible to define this in wp-config.php
	 * @since 1.5.0
	 */
	if ( !defined( 'SITECOOKIEPATH' ) )
			define( 'SITECOOKIEPATH', $current_site->path );

	/**
	 * It is possible to define this in wp-config.php
	 * @since 2.6.0
	 */
	if ( !defined( 'ADMIN_COOKIE_PATH' ) ) {
			if( !is_subdomain_install() ) {
					define( 'ADMIN_COOKIE_PATH', SITECOOKIEPATH );
			} else {
					define( 'ADMIN_COOKIE_PATH', SITECOOKIEPATH . 'wp-admin' );
			}
	}
	/**
	 * It is possible to define this in wp-config.php
	 * @since 2.0.0
	 */
	if ( !defined('COOKIE_DOMAIN') )
			define('COOKIE_DOMAIN', '.' . $current_site->cookie_domain);
}
?>
