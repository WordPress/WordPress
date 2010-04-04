<?php
/**
 * Defines constants and global variables that can be overridden, generally in wp-config.php.
 *
 * @package WordPress
 * @subpackage Multisite
 * @since 3.0.0
 */

/**
 * Defines Multisite upload constants.
 *
 * @since 3.0.0
 */
function ms_upload_constants(  ) {
	global $wpdb;

	/** @since 3.0.0 */
	// Base uploads dir relative to ABSPATH
	if ( !defined( 'UPLOADBLOGSDIR' ) )
		define( 'UPLOADBLOGSDIR', 'wp-content/blogs.dir' );

	/** @since 3.0.0 */
	if ( !defined( 'UPLOADS' ) ) {
		// Uploads dir relative to ABSPATH
		define( 'UPLOADS', UPLOADBLOGSDIR . "/{$wpdb->blogid}/files/" );
		if ( 'wp-content/blogs.dir' == UPLOADBLOGSDIR )
			define( 'BLOGUPLOADDIR', WP_CONTENT_DIR . "/blogs.dir/{$wpdb->blogid}/files/" );
	}
}

/**
 * Defines Multisite cookie constants.
 *
 * @since 3.0.0
 */
function ms_cookie_constants(  ) {
	global $current_site;

	/**
	 * @since 1.2.0
	 */
	if ( !defined( 'COOKIEPATH' ) )
		define( 'COOKIEPATH', $current_site->path );

	/**
	 * @since 1.5.0
	 */
	if ( !defined( 'SITECOOKIEPATH' ) )
		define( 'SITECOOKIEPATH', $current_site->path );

	/**
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
	 * @since 2.0.0
	 */
	if ( !defined('COOKIE_DOMAIN') && 'localhost' != $current_site->domain ) {
		if ( isset( $current_site->cookie_domain ) )
			define('COOKIE_DOMAIN', '.' . $current_site->cookie_domain);
		else
			define('COOKIE_DOMAIN', '.' . $current_site->domain);
	}
}

/**
 * Defines Multisite file constants.
 *
 * @since 3.0.0
 */
function ms_file_constants(  ) {
	/**
	 * Optional support for X-Sendfile header
	 * @since 3.0.0
	 */
	if ( !defined( 'WPMU_SENDFILE' ) )
		define( 'WPMU_SENDFILE', false );

	/**
	 * Optional support for X-Accel-Redirect header
	 * @since 3.0.0
	 */
	if ( !defined( 'WPMU_ACCEL_REDIRECT' ) )
		define( 'WPMU_ACCEL_REDIRECT', false );
}
?>
