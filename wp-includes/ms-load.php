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
 * @since 3.0
 *
 * @return bool True if subdomain configuration is enabled, false otherwise.
 */
function is_subdomain_install() {
	if ( defined('VHOST') && VHOST == 'yes' )
		return true;

	return false;
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
 * @return bool|string Returns true on success, or drop-in file to include.
 */
function ms_site_check() {
	global $wpdb, $current_blog;

	if ( '1' == $current_blog->deleted ) {
		if ( file_exists( WP_CONTENT_DIR . '/blog-deleted.php' ) ) {
			return WP_CONTENT_DIR . '/blog-deleted.php';
		} else {
			header( 'HTTP/1.1 410 Gone' );
			wp_die( __( 'This user has elected to delete their account and the content is no longer available.' ) );
		}
	}

	if ( '2' == $current_blog->deleted ) {
		if ( file_exists( WP_CONTENT_DIR . '/blog-inactive.php' ) )
			return WP_CONTENT_DIR . '/blog-inactive.php';
		else
			wp_die( sprintf( __( 'This site has not been activated yet. If you are having problems activating your site, please contact <a href="mailto:%1$s">%1$s</a>.' ), str_replace( '@', ' AT ', get_site_option( 'admin_email', "support@{$current_site->domain}" ) ) ) );
	}

	if ( $current_blog->archived == '1' || $current_blog->spam == '1' ) {
		if ( file_exists( WP_CONTENT_DIR . '/blog-suspended.php' ) ) {
			return WP_CONTENT_DIR . '/blog-suspended.php';
		} else {
			header( 'HTTP/1.1 410 Gone' );
			wp_die( __( 'This blog has been archived or suspended.' ) );
		}
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
	$current_site->site_name = wp_cache_get( $current_site->id . ':current_site_name', 'site-options' );
	if ( ! $current_site->site_name ) {
		$current_site->site_name = wp_cache_get( $current_site->id . ':site_name', 'site-options' );
		if ( ! $current_site->site_name ) {
			$current_site->site_name = $wpdb->get_var( $wpdb->prepare( "SELECT meta_value FROM $wpdb->sitemeta WHERE site_id = %d AND meta_key = 'site_name'", $current_site->id ) );
			if ( ! $current_site->site_name )
				$current_site->site_name = ucfirst( $current_site->domain );
		}
		wp_cache_set( $current_site->id . ':current_site_name', $current_site->site_name, 'site-options' );
	}
	return $current_site;
}

/**
 * Sets current_site object.
 *
 * @access private
 * @since 3.0.0
 * @return object $current_site object
 */
function wpmu_current_site() {
	global $wpdb, $current_site, $domain, $path, $sites, $cookie_domain;
	if ( defined( 'DOMAIN_CURRENT_SITE' ) && defined( 'PATH_CURRENT_SITE' ) ) {
		$current_site->id = defined( 'SITE_ID_CURRENT_SITE' ) ? SITE_ID_CURRENT_SITE : 1;
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

		wp_load_core_site_options( $current_site->id );

		return $current_site;
	}

	$current_site = wp_cache_get( 'current_site', 'site-options' );
	if ( $current_site )
		return $current_site;

	$sites = $wpdb->get_results( "SELECT * FROM $wpdb->site" ); // usually only one site
	if ( 1 == count( $sites ) ) {
		$current_site = $sites[0];
		wp_load_core_site_options( $current_site->id );
		$path = $current_site->path;
		$current_site->blog_id = $wpdb->get_var( $wpdb->prepare( "SELECT blog_id FROM $wpdb->blogs WHERE domain = %s AND path = %s", $current_site->domain, $current_site->path ) );
		$current_site = get_current_site_name( $current_site );
		if ( substr( $current_site->domain, 0, 4 ) == 'www.' )
			$current_site->cookie_domain = substr( $current_site->domain, 4 );
		wp_cache_set( 'current_site', $current_site, 'site-options' );
		return $current_site;
	}
	$path = substr( $_SERVER[ 'REQUEST_URI' ], 0, 1 + strpos( $_SERVER[ 'REQUEST_URI' ], '/', 1 ) );

	if ( $domain == $cookie_domain )
		$current_site = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $wpdb->site WHERE domain = %s AND path = %s", $domain, $path ) );
	else
		$current_site = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $wpdb->site WHERE domain IN ( %s, %s ) AND path = %s ORDER BY CHAR_LENGTH( domain ) DESC LIMIT 1", $domain, $cookie_domain, $path ) );

	if ( ! $current_site ) {
		if ( $domain == $cookie_domain )
			$current_site = $wpdb->get_row( $wpdb->prepare("SELECT * FROM $wpdb->site WHERE domain = %s AND path='/'", $domain ) );
		else
			$current_site = $wpdb->get_row( $wpdb->prepare("SELECT * FROM $wpdb->site WHERE domain IN ( %s, %s ) AND path = '/' ORDER BY CHAR_LENGTH( domain ) DESC LIMIT 1", $domain, $cookie_domain, $path ) );
	}

	if ( $current_site ) {
		$path = $current_site->path;
		$current_site->cookie_domain = $cookie_domain;
		return $current_site;
	}

	if ( is_subdomain_install() ) {
		$sitedomain = substr( $domain, 1 + strpos( $domain, '.' ) );
		$current_site = $wpdb->get_row( $wpdb->prepare("SELECT * FROM $wpdb->site WHERE domain = %s AND path = %s", $sitedomain, $path) );
		if ( $current_site ) {
			$current_site->cookie_domain = $current_site->domain;
			return $current_site;
		}

		$current_site = $wpdb->get_row( $wpdb->prepare("SELECT * FROM $wpdb->site WHERE domain = %s AND path='/'", $sitedomain) );
	}

	if ( $current_site || defined( 'WP_INSTALLING' ) ) {
		$path = '/';
		return $current_site;
	}

	// Still no dice.
	// @todo Update or remove WPMU codex link.
	if ( 1 == count( $sites ) )
		wp_die( sprintf( 'That blog does not exist. Please try <a href="%s">%s</a>.', $sites[0]->domain . $sites[0]->path ) );
	else
		wp_die( 'No site defined on this host. If you are the owner of this site, please check <a href="http://codex.wordpress.org/Debugging_WPMU">Debugging WPMU</a> for further assistance.' );
}

/**
 * Displays a failure message.
 *
 * Used when blog does not exist. Checks for a missing $wpdb->site table as well.
 *
 * @access private
 * @since 3.0.0
 */
function ms_not_installed() {
	global $wpdb, $domain, $path;

	$msg = '<h1>' . esc_html( 'Fatal Error' ) . '</h1>';
	$msg  = '<p>' . 'If your site does not display, please contact the owner of this network.' . '</p>';
	$msg .= '<p>' . 'If you are the owner of this network please check that MySQL is running properly and all tables are error free.' . '</p>';
	if ( ! $wpdb->get_var( "SHOW TABLES LIKE '$wpdb->site'" ) )
		$msg .= '<p>' . sprintf( '<strong>Database tables are missing.</strong> This means that MySQL is not running, WordPress was not installed properly, or someone deleted <code>%s</code>. You really <em>should</em> look at your database now.', $wpdb->site ) . '</p>';
	else
		$msg .= '<p>' . sprintf( '<strong>Could Not Find Site!</strong> Searched for table <em>%1$s</em> in <code>%2$s</code>. Is that right?', $domain . $path, DB_NAME, $wpdb->blogs ) . '</p>';
	$msg .= '<h1>' . esc_html( 'What do I do now?' ) . '</h1>';
	// @todo Update WPMU codex link.
	$msg .= '<p>' . 'Read the <a target="_blank" href="http://codex.wordpress.org/Debugging_WPMU">bug report</a> page. Some of the guidelines there may help you figure out what went wrong.' . '</p>';
	$msg .= '<p>' . "If you're still stuck with this message, then check that your database contains the following tables:" . '</p><ul>';
	foreach ( $wpdb->global_tables as $table ) {
		$msg .= '<li>' . $wpdb->prefix . $table . '</li>';
	}
	$msg .= '</ul>';
	// @todo Update WPMU codex link and support instructions.
	$msg = '<p>' . 'If you suspect a problem please report it to the support forums but you must include the information asked for in the <a target="_blank" href="http://codex.wordpress.org/Debugging_WPMU">WPMU bug reporting guidelines</a>! ' . '</p>';

	// @todo This file no longer exists post-merge.
	if ( is_file( 'release-info.txt' ) ) {
		$msg .= '<p>' . 'Your bug report must include the following text:' . '</p>';
		$info = file( 'release-info.txt' );
		$msg .= $info[ 4 ] . '"';
	}

	die( $msg );
}

?>
