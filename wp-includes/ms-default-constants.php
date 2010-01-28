<?php
/**
 * Defines constants and global variables that can be overridden, generally in wp-config.php.
 *
 * @package WordPress
 * @subpackage Multisite
 */

/**
 * Defines Multisite default constants.
 *
 * @since 3.0.0
 * @param $context
 */
function ms_default_constants( $context ) {
	switch( $context ) {
		case 'uploads' :
			global $wpdb;
			if ( !defined( 'UPLOADBLOGSDIR' ) )
				define( 'UPLOADBLOGSDIR', 'wp-content/blogs.dir' );
		
			if ( !defined( 'UPLOADS' ) )
				define( 'UPLOADS', UPLOADBLOGSDIR . "/{$wpdb->blogid}/files/" );
		
			if ( !defined( 'BLOGUPLOADDIR' ) )
				define( 'BLOGUPLOADDIR', WP_CONTENT_DIR . "/blogs.dir/{$wpdb->blogid}/files/" );
			break;
		case 'cookies' :
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
			break;
	}
}
?>