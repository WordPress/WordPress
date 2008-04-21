<?php
/**
 * Used to setup and fix common variables and include
 * the WordPress procedural and class library.
 *
 * You should not have to change this file and allows
 * for some configuration in wp-config.php.
 *
 * @package WordPress
 */

if ( !defined('WP_MEMORY_LIMIT') )
	define('WP_MEMORY_LIMIT', '32M');

if ( function_exists('memory_get_usage') && ( (int) @ini_get('memory_limit') < abs(intval(WP_MEMORY_LIMIT)) ) )
	@ini_set('memory_limit', WP_MEMORY_LIMIT);


/**
 * wp_unregister_GLOBALS() - Turn register globals off
 *
 * @access private
 * @since 2.1.0
 * @return null Will return null if register_globals PHP directive was disabled
 */
function wp_unregister_GLOBALS() {
	if ( !ini_get('register_globals') )
		return;

	if ( isset($_REQUEST['GLOBALS']) )
		die('GLOBALS overwrite attempt detected');

	// Variables that shouldn't be unset
	$noUnset = array('GLOBALS', '_GET', '_POST', '_COOKIE', '_REQUEST', '_SERVER', '_ENV', '_FILES', 'table_prefix');

	$input = array_merge($_GET, $_POST, $_COOKIE, $_SERVER, $_ENV, $_FILES, isset($_SESSION) && is_array($_SESSION) ? $_SESSION : array());
	foreach ( $input as $k => $v )
		if ( !in_array($k, $noUnset) && isset($GLOBALS[$k]) ) {
			$GLOBALS[$k] = NULL;
			unset($GLOBALS[$k]);
		}
}

wp_unregister_GLOBALS();

unset( $wp_filter, $cache_lastcommentmodified, $cache_lastpostdate );

/**
 * The $blog_id global, which you can change in the config allows you to create a simple
 * multiple blog installation using just one WordPress and changing $blog_id around.
 *
 * @global int $blog_id
 * @since 2.0.0
 */
if ( ! isset($blog_id) )
	$blog_id = 1;

// Fix for IIS, which doesn't set REQUEST_URI
if ( empty( $_SERVER['REQUEST_URI'] ) ) {

	// IIS Mod-Rewrite
	if (isset($_SERVER['HTTP_X_ORIGINAL_URL'])) {
		$_SERVER['REQUEST_URI'] = $_SERVER['HTTP_X_ORIGINAL_URL'];
	}
	// IIS Isapi_Rewrite
	else if (isset($_SERVER['HTTP_X_REWRITE_URL'])) {
		$_SERVER['REQUEST_URI'] = $_SERVER['HTTP_X_REWRITE_URL'];
	}
	else
	{
		// Some IIS + PHP configurations puts the script-name in the path-info (No need to append it twice)
		if ( isset($_SERVER['PATH_INFO']) ) {
			if ( $_SERVER['PATH_INFO'] == $_SERVER['SCRIPT_NAME'] )
				$_SERVER['REQUEST_URI'] = $_SERVER['PATH_INFO'];
			else
				$_SERVER['REQUEST_URI'] = $_SERVER['SCRIPT_NAME'] . $_SERVER['PATH_INFO'];
		}

		// Append the query string if it exists and isn't null
		if (isset($_SERVER['QUERY_STRING']) && !empty($_SERVER['QUERY_STRING'])) {
			$_SERVER['REQUEST_URI'] .= '?' . $_SERVER['QUERY_STRING'];
		}
	}
}

// Fix for PHP as CGI hosts that set SCRIPT_FILENAME to something ending in php.cgi for all requests
if ( isset($_SERVER['SCRIPT_FILENAME']) && ( strpos($_SERVER['SCRIPT_FILENAME'], 'php.cgi') == strlen($_SERVER['SCRIPT_FILENAME']) - 7 ) )
	$_SERVER['SCRIPT_FILENAME'] = $_SERVER['PATH_TRANSLATED'];

// Fix for Dreamhost and other PHP as CGI hosts
if (strpos($_SERVER['SCRIPT_NAME'], 'php.cgi') !== false)
	unset($_SERVER['PATH_INFO']);

// Fix empty PHP_SELF
$PHP_SELF = $_SERVER['PHP_SELF'];
if ( empty($PHP_SELF) )
	$_SERVER['PHP_SELF'] = $PHP_SELF = preg_replace("/(\?.*)?$/",'',$_SERVER["REQUEST_URI"]);

if ( version_compare( '4.3', phpversion(), '>' ) ) {
	die( 'Your server is running PHP version ' . phpversion() . ' but WordPress requires at least 4.3.' );
}

if ( !extension_loaded('mysql') && !file_exists(ABSPATH . 'wp-content/db.php') )
	die( 'Your PHP installation appears to be missing the MySQL extension which is required by WordPress.' );

/**
 * timer_start() - PHP 4 standard microtime start capture
 *
 * @access private
 * @since 0.71
 * @global int $timestart Seconds and Microseconds added together from when function is called
 * @return bool Always returns true
 */
function timer_start() {
	global $timestart;
	$mtime = explode(' ', microtime() );
	$mtime = $mtime[1] + $mtime[0];
	$timestart = $mtime;
	return true;
}

/**
 * timer_stop() - Return and/or display the time from the page start to when function is called.
 *
 * You can get the results and print them by doing:
 * <code>
 * $nTimePageTookToExecute = timer_stop();
 * echo $nTimePageTookToExecute;
 * </code>
 *
 * Or instead, you can do:
 * <code>
 * timer_stop(1);
 * </code>
 * which will do what the above does. If you need the result, you can assign it to a variable, but
 * most cases, you only need to echo it.
 *
 * @since 0.71
 * @global int $timestart Seconds and Microseconds added together from when timer_start() is called
 * @global int $timeend  Seconds and Microseconds added together from when function is called
 *
 * @param int $display Use '0' or null to not echo anything and 1 to echo the total time
 * @param int $precision The amount of digits from the right of the decimal to display. Default is 3.
 * @return float The "second.microsecond" finished time calculation
 */
function timer_stop($display = 0, $precision = 3) { //if called like timer_stop(1), will echo $timetotal
	global $timestart, $timeend;
	$mtime = microtime();
	$mtime = explode(' ',$mtime);
	$mtime = $mtime[1] + $mtime[0];
	$timeend = $mtime;
	$timetotal = $timeend-$timestart;
	$r = ( function_exists('number_format_i18n') ) ? number_format_i18n($timetotal, $precision) : number_format($timetotal, $precision);
	if ( $display )
		echo $r;
	return $r;
}
timer_start();

// Add define('WP_DEBUG',true); to wp-config.php to enable display of notices during development.
if (defined('WP_DEBUG') and WP_DEBUG == true) {
	error_reporting(E_ALL);
} else {
	error_reporting(E_ALL ^ E_NOTICE ^ E_USER_NOTICE);
}

// For an advanced caching plugin to use, static because you would only want one
if ( defined('WP_CACHE') )
	@include ABSPATH . 'wp-content/advanced-cache.php';

/**
 * Stores the location of the WordPress directory of functions, classes, and core content.
 *
 * @since 1.0.0
 */
define('WPINC', 'wp-includes');

if ( !defined('LANGDIR') ) {
	/**
	 * Stores the location of the language directory. First looks for language folder in wp-content
	 * and uses that folder if it exists. Or it uses the "languages" folder in WPINC.
	 *
	 * @since 2.1.0
	 */
	if ( file_exists(ABSPATH . 'wp-content/languages') && @is_dir(ABSPATH . 'wp-content/languages') )
		define('LANGDIR', 'wp-content/languages'); // no leading slash, no trailing slash
	else
		define('LANGDIR', WPINC . '/languages'); // no leading slash, no trailing slash
}

/**
 * Allows for the plugins directory to be moved from the default location.
 *
 * This isn't used everywhere. Constant is not used in plugin_basename()
 * which might cause conflicts with changing this.
 *
 * @since 2.1
 */
if ( !defined('PLUGINDIR') )
	define('PLUGINDIR', 'wp-content/plugins'); // no leading slash, no trailing slash

require (ABSPATH . WPINC . '/compat.php');
require (ABSPATH . WPINC . '/functions.php');
require (ABSPATH . WPINC . '/classes.php');

require_wp_db();

if ( !empty($wpdb->error) )
	dead_db();

$prefix = $wpdb->set_prefix($table_prefix);

if ( is_wp_error($prefix) )
	wp_die('<strong>ERROR</strong>: <code>$table_prefix</code> in <code>wp-config.php</code> can only contain numbers, letters, and underscores.');

if ( file_exists(ABSPATH . 'wp-content/object-cache.php') )
	require_once (ABSPATH . 'wp-content/object-cache.php');
else
	require_once (ABSPATH . WPINC . '/cache.php');

wp_cache_init();

require (ABSPATH . WPINC . '/plugin.php');
require (ABSPATH . WPINC . '/default-filters.php');
include_once(ABSPATH . WPINC . '/streams.php');
include_once(ABSPATH . WPINC . '/gettext.php');
require_once (ABSPATH . WPINC . '/l10n.php');

if ( !is_blog_installed() && (strpos($_SERVER['PHP_SELF'], 'install.php') === false && !defined('WP_INSTALLING')) ) {
	if ( defined('WP_SITEURL') )
		$link = WP_SITEURL . '/wp-admin/install.php';
	elseif (strpos($_SERVER['PHP_SELF'], 'wp-admin') !== false)
		$link = preg_replace('|/wp-admin/?.*?$|', '/', $_SERVER['PHP_SELF']) . 'wp-admin/install.php';
	else
		$link = preg_replace('|/[^/]+?$|', '/', $_SERVER['PHP_SELF']) . 'wp-admin/install.php';
	require_once(ABSPATH . WPINC . '/kses.php');
	require_once(ABSPATH . WPINC . '/pluggable.php');
	wp_redirect($link);
	die(); // have to die here ~ Mark
}

require (ABSPATH . WPINC . '/formatting.php');
require (ABSPATH . WPINC . '/capabilities.php');
require (ABSPATH . WPINC . '/query.php');
require (ABSPATH . WPINC . '/theme.php');
require (ABSPATH . WPINC . '/user.php');
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
require (ABSPATH . WPINC . '/version.php');
require (ABSPATH . WPINC . '/deprecated.php');
require (ABSPATH . WPINC . '/script-loader.php');
require (ABSPATH . WPINC . '/taxonomy.php');
require (ABSPATH . WPINC . '/update.php');
require (ABSPATH . WPINC . '/canonical.php');
require (ABSPATH . WPINC . '/shortcodes.php');
require (ABSPATH . WPINC . '/media.php');

if (strpos($_SERVER['PHP_SELF'], 'install.php') === false) {
	// Used to guarantee unique hash cookies
	$cookiehash = md5(get_option('siteurl'));
	/**
	 * Used to guarantee unique hash cookies
	 * @since 1.5
	 */
	define('COOKIEHASH', $cookiehash);
}

/**
 * Should be exactly the same as the default value of SECRET_KEY in wp-config-sample.php
 * @since 2.5
 */
$wp_default_secret_key = 'put your unique phrase here';

/**
 * It is possible to define this in wp-config.php
 * @since 2.0.0
 */
if ( !defined('USER_COOKIE') )
	define('USER_COOKIE', 'wordpressuser_' . COOKIEHASH);

/**
 * It is possible to define this in wp-config.php
 * @since 2.0.0
 */
if ( !defined('PASS_COOKIE') )
	define('PASS_COOKIE', 'wordpresspass_' . COOKIEHASH);

/**
 * It is possible to define this in wp-config.php
 * @since 2.5
 */
if ( !defined('AUTH_COOKIE') )
	define('AUTH_COOKIE', 'wordpress_' . COOKIEHASH);

/**
 * It is possible to define this in wp-config.php
 * @since 2.3.0
 */
if ( !defined('TEST_COOKIE') )
	define('TEST_COOKIE', 'wordpress_test_cookie');

/**
 * It is possible to define this in wp-config.php
 * @since 1.2.0
 */
if ( !defined('COOKIEPATH') )
	define('COOKIEPATH', preg_replace('|https?://[^/]+|i', '', get_option('home') . '/' ) );

/**
 * It is possible to define this in wp-config.php
 * @since 1.5.0
 */
if ( !defined('SITECOOKIEPATH') )
	define('SITECOOKIEPATH', preg_replace('|https?://[^/]+|i', '', get_option('siteurl') . '/' ) );

/**
 * It is possible to define this in wp-config.php
 * @since 2.0.0
 */
if ( !defined('COOKIE_DOMAIN') )
	define('COOKIE_DOMAIN', false);
	
/**
 * It is possible to define this in wp-config.php
 * @since 2.5.0
 */
if ( !defined( 'AUTOSAVE_INTERVAL' ) )
	define( 'AUTOSAVE_INTERVAL', 60 );
	

require (ABSPATH . WPINC . '/vars.php');

// Check for hacks file if the option is enabled
if (get_option('hack_file')) {
	if (file_exists(ABSPATH . 'my-hacks.php'))
		require(ABSPATH . 'my-hacks.php');
}

if ( get_option('active_plugins') ) {
	$current_plugins = get_option('active_plugins');
	if ( is_array($current_plugins) ) {
		foreach ($current_plugins as $plugin) {
			if ('' != $plugin && file_exists(ABSPATH . PLUGINDIR . '/' . $plugin))
				include_once(ABSPATH . PLUGINDIR . '/' . $plugin);
		}
	}
}

require (ABSPATH . WPINC . '/pluggable.php');

/*
 * In most cases the default internal encoding is latin1, which is of no use,
 * since we want to use the mb_ functions for utf-8 strings
 */
if (function_exists('mb_internal_encoding')) {
	if (!@mb_internal_encoding(get_option('blog_charset')))
		mb_internal_encoding('UTF-8');
}


if ( defined('WP_CACHE') && function_exists('wp_cache_postload') )
	wp_cache_postload();

do_action('plugins_loaded');

// If already slashed, strip.
if ( get_magic_quotes_gpc() ) {
	$_GET    = stripslashes_deep($_GET   );
	$_POST   = stripslashes_deep($_POST  );
	$_COOKIE = stripslashes_deep($_COOKIE);
}

// Escape with wpdb.
$_GET    = add_magic_quotes($_GET   );
$_POST   = add_magic_quotes($_POST  );
$_COOKIE = add_magic_quotes($_COOKIE);
$_SERVER = add_magic_quotes($_SERVER);

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
 * Web Path to the current active template directory
 * @since 1.5
 */
define('TEMPLATEPATH', get_template_directory());

/**
 * Web Path to the current active template stylesheet directory
 * @since 2.1
 */
define('STYLESHEETPATH', get_stylesheet_directory());

// Load the default text localization domain.
load_default_textdomain();

/**
 * The locale of the blog
 * @since 1.5.0
 */
$locale = get_locale();
$locale_file = ABSPATH . LANGDIR . "/$locale.php";
if ( is_readable($locale_file) )
	require_once($locale_file);

// Pull in locale data after loading text domain.
require_once(ABSPATH . WPINC . '/locale.php');

/**
 * WordPress Locale object for loading locale domain date and various strings.
 * @global object $wp_locale
 * @since 2.1.0
 */
$wp_locale =& new WP_Locale();

// Load functions for active theme.
if ( TEMPLATEPATH !== STYLESHEETPATH && file_exists(STYLESHEETPATH . '/functions.php') )
	include(STYLESHEETPATH . '/functions.php');
if ( file_exists(TEMPLATEPATH . '/functions.php') )
	include(TEMPLATEPATH . '/functions.php');

/**
 * shutdown_action_hook() - Runs just before PHP shuts down execution.
 *
 * @access private
 * @since 1.2
 */
function shutdown_action_hook() {
	do_action('shutdown');
	wp_cache_close();
}
register_shutdown_function('shutdown_action_hook');

$wp->init();  // Sets up current user.

// Everything is loaded and initialized.
do_action('init');

?>
