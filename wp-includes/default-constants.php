<?php
/**
 * Defines constants and global variables that can be overridden, generally in wp-config.php.
 *
 * @package WordPress
 */

/**
 * Defines initial WordPress constants
 *
 * @see wp_debug_mode()
 *
 * @since 3.0.0
 */
function wp_initial_constants( ) {
	global $blog_id;

	// set memory limits
	if ( !defined('WP_MEMORY_LIMIT') ) {
		if( is_multisite() ) {
			define('WP_MEMORY_LIMIT', '64M');
		} else {
			define('WP_MEMORY_LIMIT', '32M');
		}
	}

	if ( ! defined( 'WP_MAX_MEMORY_LIMIT' ) ) {
		define( 'WP_MAX_MEMORY_LIMIT', '256M' );
	}

	/**
	 * The $blog_id global, which you can change in the config allows you to create a simple
	 * multiple blog installation using just one WordPress and changing $blog_id around.
	 *
	 * @global int $blog_id
	 * @since 2.0.0
	 */
	if ( ! isset($blog_id) )
		$blog_id = 1;

	// set memory limits.
	if ( function_exists('memory_get_usage') && ( (int) @ini_get('memory_limit') < abs(intval(WP_MEMORY_LIMIT)) ) )
		@ini_set('memory_limit', WP_MEMORY_LIMIT);

	if ( !defined('WP_CONTENT_DIR') )
		define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' ); // no trailing slash, full paths only - WP_CONTENT_URL is defined further down

	// Add define('WP_DEBUG', true); to wp-config.php to enable display of notices during development.
	if ( !defined('WP_DEBUG') )
		define( 'WP_DEBUG', false );

	// Add define('WP_DEBUG_DISPLAY', null); to wp-config.php use the globally configured setting for
	// display_errors and not force errors to be displayed. Use false to force display_errors off.
	if ( !defined('WP_DEBUG_DISPLAY') )
		define( 'WP_DEBUG_DISPLAY', true );

	// Add define('WP_DEBUG_LOG', true); to enable error logging to wp-content/debug.log.
	if ( !defined('WP_DEBUG_LOG') )
		define('WP_DEBUG_LOG', false);

	if ( !defined('WP_CACHE') )
		define('WP_CACHE', false);

	/**
	 * Private
	 */
	if ( !defined('MEDIA_TRASH') )
		define('MEDIA_TRASH', false);

	if ( !defined('SHORTINIT') )
		define('SHORTINIT', false);
}

/**
 * Defines plugin directory WordPress constants
 *
 * Defines must-use plugin directory constants, which may be overridden in the sunrise.php drop-in
 *
 * @since 3.0.0
 */
function wp_plugin_directory_constants( ) {
	if ( !defined('WP_CONTENT_URL') )
		define( 'WP_CONTENT_URL', get_option('siteurl') . '/wp-content'); // full url - WP_CONTENT_DIR is defined further up

	/**
	 * Allows for the plugins directory to be moved from the default location.
	 *
	 * @since 2.6.0
	 */
	if ( !defined('WP_PLUGIN_DIR') )
		define( 'WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins' ); // full path, no trailing slash

	/**
	 * Allows for the plugins directory to be moved from the default location.
	 *
	 * @since 2.6.0
	 */
	if ( !defined('WP_PLUGIN_URL') )
		define( 'WP_PLUGIN_URL', WP_CONTENT_URL . '/plugins' ); // full url, no trailing slash

	/**
	 * Allows for the plugins directory to be moved from the default location.
	 *
	 * @since 2.1.0
	 * @deprecated
	 */
	if ( !defined('PLUGINDIR') )
		define( 'PLUGINDIR', 'wp-content/plugins' ); // Relative to ABSPATH.  For back compat.

	/**
	 * Allows for the mu-plugins directory to be moved from the default location.
	 *
	 * @since 2.8.0
	 */
	if ( !defined('WPMU_PLUGIN_DIR') )
		define( 'WPMU_PLUGIN_DIR', WP_CONTENT_DIR . '/mu-plugins' ); // full path, no trailing slash

	/**
	 * Allows for the mu-plugins directory to be moved from the default location.
	 *
	 * @since 2.8.0
	 */
	if ( !defined('WPMU_PLUGIN_URL') )
		define( 'WPMU_PLUGIN_URL', WP_CONTENT_URL . '/mu-plugins' ); // full url, no trailing slash

	/**
	 * Allows for the mu-plugins directory to be moved from the default location.
	 *
	 * @since 2.8.0
	 * @deprecated
	 */
	if ( !defined( 'MUPLUGINDIR' ) )
		define( 'MUPLUGINDIR', 'wp-content/mu-plugins' ); // Relative to ABSPATH.  For back compat.
}

/**
 * Defines cookie related WordPress constants
 *
 * Defines constants after multisite is loaded. Cookie-related constants may be overridden in ms_network_cookies().
 * @since 3.0.0
 */
function wp_cookie_constants( ) {
	global $wp_default_secret_key;

	/**
	 * Used to guarantee unique hash cookies
	 * @since 1.5
	 */
	if ( !defined( 'COOKIEHASH' ) ) {
		$siteurl = get_site_option( 'siteurl' );
		if ( $siteurl )
			define( 'COOKIEHASH', md5( $siteurl ) );
		else
			define( 'COOKIEHASH', '' );
	}

	/**
	 * Should be exactly the same as the default value of SECRET_KEY in wp-config-sample.php
	 * @since 2.5.0
	 */
	$wp_default_secret_key = 'put your unique phrase here';

	/**
	 * @since 2.0.0
	 */
	if ( !defined('USER_COOKIE') )
		define('USER_COOKIE', 'wordpressuser_' . COOKIEHASH);

	/**
	 * @since 2.0.0
	 */
	if ( !defined('PASS_COOKIE') )
		define('PASS_COOKIE', 'wordpresspass_' . COOKIEHASH);

	/**
	 * @since 2.5.0
	 */
	if ( !defined('AUTH_COOKIE') )
		define('AUTH_COOKIE', 'wordpress_' . COOKIEHASH);

	/**
	 * @since 2.6.0
	 */
	if ( !defined('SECURE_AUTH_COOKIE') )
		define('SECURE_AUTH_COOKIE', 'wordpress_sec_' . COOKIEHASH);

	/**
	 * @since 2.6.0
	 */
	if ( !defined('LOGGED_IN_COOKIE') )
		define('LOGGED_IN_COOKIE', 'wordpress_logged_in_' . COOKIEHASH);

	/**
	 * @since 2.3.0
	 */
	if ( !defined('TEST_COOKIE') )
		define('TEST_COOKIE', 'wordpress_test_cookie');

	/**
	 * @since 1.2.0
	 */
	if ( !defined('COOKIEPATH') )
		define('COOKIEPATH', preg_replace('|https?://[^/]+|i', '', get_option('home') . '/' ) );

	/**
	 * @since 1.5.0
	 */
	if ( !defined('SITECOOKIEPATH') )
		define('SITECOOKIEPATH', preg_replace('|https?://[^/]+|i', '', get_option('siteurl') . '/' ) );

	/**
	 * @since 2.6.0
	 */
	if ( !defined('ADMIN_COOKIE_PATH') )
		define( 'ADMIN_COOKIE_PATH', SITECOOKIEPATH . 'wp-admin' );

	/**
	 * @since 2.6.0
	 */
	if ( !defined('PLUGINS_COOKIE_PATH') )
		define( 'PLUGINS_COOKIE_PATH', preg_replace('|https?://[^/]+|i', '', WP_PLUGIN_URL)  );

	/**
	 * @since 2.0.0
	 */
	if ( !defined('COOKIE_DOMAIN') )
		define('COOKIE_DOMAIN', false);
}

/**
 * Defines cookie related WordPress constants
 *
 * @since 3.0.0
 */
function wp_ssl_constants( ) {
	/**
	 * @since 2.6.0
	 */
	if ( !defined('FORCE_SSL_ADMIN') )
		define('FORCE_SSL_ADMIN', false);
	force_ssl_admin(FORCE_SSL_ADMIN);

	/**
	 * @since 2.6.0
	 */
	if ( !defined('FORCE_SSL_LOGIN') )
		define('FORCE_SSL_LOGIN', false);
	force_ssl_login(FORCE_SSL_LOGIN);
}

/**
 * Defines functionality related WordPress constants
 *
 * @since 3.0.0
 */
function wp_functionality_constants( ) {
	/**
	 * @since 2.5.0
	 */
	if ( !defined( 'AUTOSAVE_INTERVAL' ) )
		define( 'AUTOSAVE_INTERVAL', 60 );

	/**
	 * @since 2.9.0
	 */
	if ( !defined( 'EMPTY_TRASH_DAYS' ) )
		define( 'EMPTY_TRASH_DAYS', 30 );

	if ( !defined('WP_POST_REVISIONS') )
		define('WP_POST_REVISIONS', true);
}

/**
 * Defines templating related WordPress constants
 *
 * @since 3.0.0
 */
function wp_templating_constants( ) {
	/**
	 * Filesystem path to the current active template directory
	 * @since 1.5.0
	 */
	define('TEMPLATEPATH', get_template_directory());

	/**
	 * Filesystem path to the current active template stylesheet directory
	 * @since 2.1.0
	 */
	define('STYLESHEETPATH', get_stylesheet_directory());

	/**
	 * Slug of the default theme for this install.
	 * Used as the default theme when installing new sites.
	 * Will be used as the fallback if the current theme doesn't exist.
	 * @since 3.0.0
	 */
	if ( !defined('WP_DEFAULT_THEME') )
		define( 'WP_DEFAULT_THEME', 'twentyeleven' );

}

?>
