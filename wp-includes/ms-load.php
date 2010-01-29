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
 * Whether a subdomain configuration is enabled
 *
 * @since 3.0
 *
 * @return bool True if subdomain configuration is enabled, false otherwise.
 */
function is_subdomain_install() {
	if ( defined('VHOST') && VHOST == 'yes' )
		return true;

	return false;
}

function ms_network_settings() {
	global $wpdb, $current_site, $cookiehash;

	if ( !isset($current_site->site_name) )
		$current_site->site_name = get_site_option('site_name');

	if ( $current_site->site_name == false )
		$current_site->site_name = ucfirst( $current_site->domain );
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

function get_current_site_name( $current_site ) {
	global $wpdb;
	$current_site->site_name = wp_cache_get( $current_site->id . ':current_site_name', "site-options" );
	if ( !$current_site->site_name ) {
		$current_site->site_name = $wpdb->get_var( $wpdb->prepare( "SELECT meta_value FROM $wpdb->sitemeta WHERE site_id = %d AND meta_key = 'site_name'", $current_site->id ) );
		if ( $current_site->site_name == null )
			$current_site->site_name = ucfirst( $current_site->domain );
		wp_cache_set( $current_site->id . ':current_site_name', $current_site->site_name, 'site-options');
	}
	return $current_site;
}

function wpmu_current_site() {
	global $wpdb, $current_site, $domain, $path, $sites, $cookie_domain;
	if ( defined( 'DOMAIN_CURRENT_SITE' ) && defined( 'PATH_CURRENT_SITE' ) ) {
		$current_site->id = (defined( 'SITE_ID_CURRENT_SITE' ) ? constant('SITE_ID_CURRENT_SITE') : 1);
		$current_site->domain = DOMAIN_CURRENT_SITE;
		$current_site->path   = $path = PATH_CURRENT_SITE;
		if ( defined( 'BLOGID_CURRENT_SITE' ) )
			$current_site->blog_id = BLOGID_CURRENT_SITE;
		if ( DOMAIN_CURRENT_SITE == $domain )
			$current_site->cookie_domain = $cookie_domain;
		elseif ( substr( $current_site->domain, 0, 4 ) == 'www.' )
			$current_site->cookie_domain = substr( $current_site->domain, 4 );
		else
			$current_site->cookie_domain = $current_site->domain;

		return $current_site;
	}

	$current_site = wp_cache_get( "current_site", "site-options" );
	if ( $current_site )
		return $current_site;

	$sites = $wpdb->get_results( "SELECT * FROM $wpdb->site" ); // usually only one site
	if ( count( $sites ) == 1 ) {
		$current_site = $sites[0];
		$path = $current_site->path;
		$current_site->blog_id = $wpdb->get_var( "SELECT blog_id FROM {$wpdb->blogs} WHERE domain='{$current_site->domain}' AND path='{$current_site->path}'" );
		$current_site = get_current_site_name( $current_site );
		if ( substr( $current_site->domain, 0, 4 ) == 'www.' )
			$current_site->cookie_domain = substr( $current_site->domain, 4 );
		wp_cache_set( "current_site", $current_site, "site-options" );
		return $current_site;
	}
	$path = substr( $_SERVER[ 'REQUEST_URI' ], 0, 1 + strpos( $_SERVER[ 'REQUEST_URI' ], '/', 1 ) );

	if ( $domain == $cookie_domain )
		$current_site = $wpdb->get_row( $wpdb->prepare("SELECT * FROM $wpdb->site WHERE domain = %s AND path = %s", $domain, $path ) );
	else
		$current_site = $wpdb->get_row( $wpdb->prepare("SELECT * FROM $wpdb->site WHERE domain IN ( %s, %s ) AND path = %s ORDER BY CHAR_LENGTH( domain ) DESC LIMIT 1", $domain, $cookie_domain, $path ) );
	if ( $current_site == null ) {
		if ( $domain == $cookie_domain )
			$current_site = $wpdb->get_row( $wpdb->prepare("SELECT * FROM $wpdb->site WHERE domain = %s AND path='/'", $domain ) );
		else
			$current_site = $wpdb->get_row( $wpdb->prepare("SELECT * FROM $wpdb->site WHERE domain IN ( %s, %s ) AND path = '/' ORDER BY CHAR_LENGTH( domain ) DESC LIMIT 1", $domain, $cookie_domain, $path ) );
	}
	if ( $current_site != null ) {
		$path = $current_site->path;
		$current_site->cookie_domain = $cookie_domain;
		return $current_site;
	} elseif ( is_subdomain_install() ) {
		$sitedomain = substr( $domain, 1 + strpos( $domain, '.' ) );
		$current_site = $wpdb->get_row( $wpdb->prepare("SELECT * FROM $wpdb->site WHERE domain = %s AND path = %s", $sitedomain, $path) );
		if ( $current_site != null ) {
			$current_site->cookie_domain = $current_site->domain;
			return $current_site;
		}
		$current_site = $wpdb->get_row( $wpdb->prepare("SELECT * FROM $wpdb->site WHERE domain = %s AND path='/'", $sitedomain) );
		if ( $current_site == null && defined( "WP_INSTALLING" ) == false ) {
			if ( count( $sites ) == 1 ) {
				$current_site = $sites[0];
				die( "That blog does not exist. Please try <a href='http://{$current_site->domain}{$current_site->path}'>http://{$current_site->domain}{$current_site->path}</a>" );
			} else {
				die( "No WPMU site defined on this host. If you are the owner of this site, please check <a href='http://codex.wordpress.org/Debugging_WPMU'>Debugging WPMU</a> for further assistance." );
			}
		} else {
			$path = '/';
		}
	} elseif ( defined( "WP_INSTALLING" ) == false ) {
		if ( count( $sites ) == 1 ) {
			$current_site = $sites[0];
			die( "That blog does not exist. Please try <a href='http://{$current_site->domain}{$current_site->path}'>http://{$current_site->domain}{$current_site->path}</a>" );
		} else {
			die( "No WPMU site defined on this host. If you are the owner of this site, please check <a href='http://codex.wordpress.org/Debugging_WPMU'>Debugging WPMU</a> for further assistance." );
		}
	} else {
		$path = '/';
	}
	return $current_site;
}

function is_installed() {
	global $wpdb, $domain, $path;
	$base = stripslashes( $base );
	if ( defined( "WP_INSTALLING" ) == false ) {
		$check = $wpdb->get_results( "SELECT * FROM $wpdb->site" );
		$msg = "If your blog does not display, please contact the owner of this site.<br /><br />If you are the owner of this site please check that MySQL is running properly and all tables are error free.<br /><br />";
		if ( $check == false ) {
			$msg .= "<strong>Database Tables Missing.</strong><br />Database tables are missing. This means that MySQL is either not running, WPMU was not installed properly, or someone deleted {$wpdb->site}. You really <em>should</em> look at your database now.<br />";
		} else {
			$msg .= '<strong>Could Not Find Blog!</strong><br />';
			$msg .= "Searched for <em>" . $domain . $path . "</em> in " . DB_NAME . "::" . $wpdb->blogs . " table. Is that right?<br />";
		}
		$msg .= "<br />\n<h1>What do I do now?</h1>";
		$msg .= "Read the <a target='_blank' href='http://codex.wordpress.org/Debugging_WPMU'>bug report</a> page. Some of the guidelines there may help you figure out what went wrong.<br />";
		$msg .= "If you're still stuck with this message, then check that your database contains the following tables:<ul>
			<li> $wpdb->blogs </li>
			<li> $wpdb->users </li>
			<li> $wpdb->usermeta </li>
			<li> $wpdb->site </li>
			<li> $wpdb->sitemeta </li>
			<li> $wpdb->sitecategories </li>
			</ul>";
		$msg .= "If you suspect a problem please report it to the support forums but you must include the information asked for in the <a href='http://codex.wordpress.org/Debugging_WPMU'>WPMU bug reporting guidelines</a>!<br /><br />";
		if ( is_file( 'release-info.txt' ) ) {
			$msg .= 'Your bug report must include the following text: "';
			$info = file( 'release-info.txt' );
			$msg .= $info[ 4 ] . '"';
		}

		die( "<h1>Fatal Error</h1> " . $msg );
	}
}

?>
