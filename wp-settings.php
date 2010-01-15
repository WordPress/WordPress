<?php
/**
 * Used to setup and fix common variables and include
 * the WordPress procedural and class library.
 *
 * Allows for some configuration in wp-config.php (see default-constants.php)
 *
 * @package WordPress
 */

/**
 * Stores the location of the WordPress directory of functions, classes, and core content.
 *
 * @since 1.0.0
 */
define('WPINC', 'wp-includes');

require (ABSPATH . WPINC . '/load.php');
require (ABSPATH . WPINC . '/default-constants.php');
require (ABSPATH . WPINC . '/version.php');

wp_default_constants('init');

set_magic_quotes_runtime(0);
@ini_set('magic_quotes_sybase', 0);

if ( function_exists('date_default_timezone_set') )
	date_default_timezone_set('UTC');

wp_unregister_GLOBALS();

unset( $wp_filter, $cache_lastcommentmodified, $cache_lastpostdate );

wp_fix_server_vars();

wp_check_php_mysql_versions();

wp_maintenance();

timer_start();

wp_debug_mode();

// For an advanced caching plugin to use, static because you would only want one
if ( WP_CACHE )
	@include WP_CONTENT_DIR . '/advanced-cache.php';

wp_set_lang_dir();

require (ABSPATH . WPINC . '/compat.php');
require (ABSPATH . WPINC . '/functions.php');
require (ABSPATH . WPINC . '/classes.php');

require_wp_db();

wp_set_wpdb_vars();

wp_start_object_cache();

if( is_multisite() )
    require (ABSPATH . WPINC . '/ms-load.php');

require (ABSPATH . WPINC . '/plugin.php');
require (ABSPATH . WPINC . '/default-filters.php');
include_once(ABSPATH . WPINC . '/pomo/mo.php');

if ( SHORTINIT ) // stop most of WP being loaded, we just want the basics
	return false;

require_once (ABSPATH . WPINC . '/l10n.php');

wp_not_installed();

require (ABSPATH . WPINC . '/formatting.php');
require (ABSPATH . WPINC . '/capabilities.php');
require (ABSPATH . WPINC . '/query.php');
require (ABSPATH . WPINC . '/theme.php');
require (ABSPATH . WPINC . '/user.php');
require (ABSPATH . WPINC . '/meta.php');
require (ABSPATH . WPINC . '/general-template.php');
require (ABSPATH . WPINC . '/link-template.php');
require (ABSPATH . WPINC . '/author-template.php');
require (ABSPATH . WPINC . '/post.php');
require (ABSPATH . WPINC . '/post-template.php');
require (ABSPATH . WPINC . '/category.php');
require (ABSPATH . WPINC . '/category-template.php');
require (ABSPATH . WPINC . '/comment.php');
require (ABSPATH . WPINC . '/comment-template.php');
require (ABSPATH . WPINC . '/rewrite.php');
require (ABSPATH . WPINC . '/feed.php');
require (ABSPATH . WPINC . '/bookmark.php');
require (ABSPATH . WPINC . '/bookmark-template.php');
require (ABSPATH . WPINC . '/kses.php');
require (ABSPATH . WPINC . '/cron.php');
require (ABSPATH . WPINC . '/deprecated.php');
require (ABSPATH . WPINC . '/script-loader.php');
require (ABSPATH . WPINC . '/taxonomy.php');
require (ABSPATH . WPINC . '/update.php');
require (ABSPATH . WPINC . '/canonical.php');
require (ABSPATH . WPINC . '/shortcodes.php');
require (ABSPATH . WPINC . '/media.php');
require (ABSPATH . WPINC . '/http.php');
require (ABSPATH . WPINC . '/widgets.php');

if ( is_multisite() ) {
	require_once( ABSPATH . WPINC . '/ms-functions.php' );
	require_once( ABSPATH . WPINC . '/ms-default-filters.php' );
	require_once( ABSPATH . WPINC . '/ms-deprecated.php' );
}

wp_default_constants('wp_included');

if ( is_multisite() )
    ms_network_settings();

wp_default_constants('ms_network_settings_loaded');

wp_load_mu_plugins();

/**
 * Used to load network wide plugins
 * @since 3.0
 */
if ( is_multisite() )
	ms_network_plugins();

do_action('muplugins_loaded');

/**
 * Used to check site status
 * @since 3.0
 */
if ( is_multisite() ) {
	ms_site_check();
	ms_network_cookies();
}

wp_default_constants('ms_loaded');

require (ABSPATH . WPINC . '/vars.php');

// make taxonomies available to plugins and themes
// @plugin authors: warning: this gets registered again on the init hook
create_initial_taxonomies();

wp_load_plugins();

require (ABSPATH . WPINC . '/pluggable.php');

wp_set_internal_encoding();

if ( WP_CACHE && function_exists('wp_cache_postload') )
	wp_cache_postload();

do_action('plugins_loaded');

wp_default_constants('plugins_loaded');

wp_magic_quotes();

do_action('sanitize_comment_cookies');

/**
 * WordPress Query object
 * @global object $wp_the_query
 * @since 2.0.0
 */
$wp_the_query =& new WP_Query();

/**
 * Holds the reference to @see $wp_the_query
 * Use this global for WordPress queries
 * @global object $wp_query
 * @since 1.5.0
 */
$wp_query     =& $wp_the_query;

/**
 * Holds the WordPress Rewrite object for creating pretty URLs
 * @global object $wp_rewrite
 * @since 1.5.0
 */
$wp_rewrite   =& new WP_Rewrite();

/**
 * WordPress Object
 * @global object $wp
 * @since 2.0.0
 */
$wp           =& new WP();

/**
 * WordPress Widget Factory Object
 * @global object $wp_widget_factory
 * @since 2.8.0
 */
$wp_widget_factory =& new WP_Widget_Factory();

do_action('setup_theme');

wp_default_constants('setup_theme');

// Load the default text localization domain.
load_default_textdomain();

wp_find_locale();

/**
 * WordPress Locale object for loading locale domain date and various strings.
 * @global object $wp_locale
 * @since 2.1.0
 */
$wp_locale =& new WP_Locale();

wp_load_theme_functions();

register_shutdown_function('shutdown_action_hook');

$wp->init();  // Sets up current user.

// Everything is loaded and initialized.
do_action('init');

?>