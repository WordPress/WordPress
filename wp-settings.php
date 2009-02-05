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

set_magic_quotes_runtime(0);
@ini_set('magic_quotes_sybase', 0);

/**
 * Turn register globals off.
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
		// Use ORIG_PATH_INFO if there is no PATH_INFO
		if ( !isset($_SERVER['PATH_INFO']) && isset($_SERVER['ORIG_PATH_INFO']) )
			$_SERVER['PATH_INFO'] = $_SERVER['ORIG_PATH_INFO'];

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
	die( sprintf( /*WP_I18N_OLD_PHP*/'Your server is running PHP version %s but WordPress requires at least 4.3.'/*/WP_I18N_OLD_PHP*/, phpversion() ) );
}

if ( !defined('WP_CONTENT_DIR') )
	define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' ); // no trailing slash, full paths only - WP_CONTENT_URL is defined further down

if ( file_exists(ABSPATH . '.maintenance') && !defined('WP_INSTALLING') ) {
	include(ABSPATH . '.maintenance');
	// If the $upgrading timestamp is older than 10 minutes, don't die.
	if ( ( time() - $upgrading ) < 600 ) {
		if ( file_exists( WP_CONTENT_DIR . '/maintenance.php' ) ) {
			require_once( WP_CONTENT_DIR . '/maintenance.php' );
			die();
		}

		$protocol = $_SERVER["SERVER_PROTOCOL"];
		if ( 'HTTP/1.1' != $protocol && 'HTTP/1.0' != $protocol )
			$protocol = 'HTTP/1.0';
		header( "$protocol 503 Service Unavailable", true, 503 );
		header( 'Content-Type: text/html; charset=utf-8' );
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Maintenance</title>

</head>
<body>
	<h1>Briefly unavailable for scheduled maintenance. Check back in a minute.</h1>
</body>
</html>
<?php
		die();
	}
}

if ( !extension_loaded('mysql') && !file_exists(WP_CONTENT_DIR . '/db.php') )
	die( /*WP_I18N_OLD_MYSQL*/'Your PHP installation appears to be missing the MySQL extension which is required by WordPress.'/*/WP_I18N_OLD_MYSQL*/ );

/**
 * PHP 4 standard microtime start capture.
 *
 * @access private
 * @since 0.71
 * @global int $timestart Seconds and Microseconds added together from when function is called.
 * @return bool Always returns true.
 */
function timer_start() {
	global $timestart;
	$mtime = explode(' ', microtime() );
	$mtime = $mtime[1] + $mtime[0];
	$timestart = $mtime;
	return true;
}

/**
 * Return and/or display the time from the page start to when function is called.
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
	// Unicode Extension is in PHP 6.0 only or do version check when this changes.
	if ( function_exists('unicode_decode') ) 
		error_reporting( E_ALL ^ E_DEPRECATED ^ E_NOTICE ^ E_USER_NOTICE ^ E_STRICT );
	else if ( defined( 'E_DEPRECATED' ) ) // Introduced in PHP 5.3
		error_reporting( E_ALL ^ E_DEPRECATED ^ E_NOTICE ^ E_USER_NOTICE );
	else
		error_reporting(E_ALL ^ E_NOTICE ^ E_USER_NOTICE);
}

// For an advanced caching plugin to use, static because you would only want one
if ( defined('WP_CACHE') )
	@include WP_CONTENT_DIR . '/advanced-cache.php';

/**
 * Stores the location of the WordPress directory of functions, classes, and core content.
 *
 * @since 1.0.0
 */
define('WPINC', 'wp-includes');

if ( !defined('WP_LANG_DIR') ) {
	/**
	 * Stores the location of the language directory. First looks for language folder in WP_CONTENT_DIR
	 * and uses that folder if it exists. Or it uses the "languages" folder in WPINC.
	 *
	 * @since 2.1.0
	 */
	if ( file_exists(WP_CONTENT_DIR . '/languages') && @is_dir(WP_CONTENT_DIR . '/languages') ) {
		define('WP_LANG_DIR', WP_CONTENT_DIR . '/languages'); // no leading slash, no trailing slash, full path, not relative to ABSPATH
		if (!defined('LANGDIR')) {
			// Old static relative path maintained for limited backwards compatibility - won't work in some cases
			define('LANGDIR', 'wp-content/languages');
		}
	} else {
		define('WP_LANG_DIR', ABSPATH . WPINC . '/languages'); // no leading slash, no trailing slash, full path, not relative to ABSPATH
		if (!defined('LANGDIR')) {
			// Old relative path maintained for backwards compatibility
			define('LANGDIR', WPINC . '/languages');
		}
	}
}

require (ABSPATH . WPINC . '/compat.php');
require (ABSPATH . WPINC . '/functions.php');
require (ABSPATH . WPINC . '/classes.php');

require_wp_db();

if ( !empty($wpdb->error) )
	dead_db();

$prefix = $wpdb->set_prefix($table_prefix);

if ( is_wp_error($prefix) )
	wp_die(/*WP_I18N_BAD_PREFIX*/'<strong>ERROR</strong>: <code>$table_prefix</code> in <code>wp-config.php</code> can only contain numbers, letters, and underscores.'/*/WP_I18N_BAD_PREFIX*/);

if ( file_exists(WP_CONTENT_DIR . '/object-cache.php') )
	require_once (WP_CONTENT_DIR . '/object-cache.php');
else
	require_once (ABSPATH . WPINC . '/cache.php');

wp_cache_init();
if ( function_exists('wp_cache_add_global_groups') ) {
	wp_cache_add_global_groups(array ('users', 'userlogins', 'usermeta'));
	wp_cache_add_non_persistent_groups(array( 'comment', 'counts', 'plugins' ));
}

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
require (ABSPATH . WPINC . '/http.php');

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
 */
if ( !defined('PLUGINDIR') )
	define( 'PLUGINDIR', 'wp-content/plugins' ); // Relative to ABSPATH.  For back compat.

/**
 * Used to guarantee unique hash cookies
 * @since 1.5
 */
define('COOKIEHASH', md5(get_option('siteurl')));

/**
 * Should be exactly the same as the default value of SECRET_KEY in wp-config-sample.php
 * @since 2.5.0
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
 * @since 2.5.0
 */
if ( !defined('AUTH_COOKIE') )
	define('AUTH_COOKIE', 'wordpress_' . COOKIEHASH);

/**
 * It is possible to define this in wp-config.php
 * @since 2.6.0
 */
if ( !defined('SECURE_AUTH_COOKIE') )
	define('SECURE_AUTH_COOKIE', 'wordpress_sec_' . COOKIEHASH);

/**
 * It is possible to define this in wp-config.php
 * @since 2.6.0
 */
if ( !defined('LOGGED_IN_COOKIE') )
	define('LOGGED_IN_COOKIE', 'wordpress_logged_in_' . COOKIEHASH);

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
 * @since 2.6.0
 */
if ( !defined('ADMIN_COOKIE_PATH') )
	define( 'ADMIN_COOKIE_PATH', SITECOOKIEPATH . 'wp-admin' );

/**
 * It is possible to define this in wp-config.php
 * @since 2.6.0
 */
if ( !defined('PLUGINS_COOKIE_PATH') )
	define( 'PLUGINS_COOKIE_PATH', preg_replace('|https?://[^/]+|i', '', WP_PLUGIN_URL)  );

/**
 * It is possible to define this in wp-config.php
 * @since 2.0.0
 */
if ( !defined('COOKIE_DOMAIN') )
	define('COOKIE_DOMAIN', false);

/**
 * It is possible to define this in wp-config.php
 * @since 2.6.0
 */
if ( !defined('FORCE_SSL_ADMIN') )
	define('FORCE_SSL_ADMIN', false);
force_ssl_admin(FORCE_SSL_ADMIN);

/**
 * It is possible to define this in wp-config.php
 * @since 2.6.0
 */
if ( !defined('FORCE_SSL_LOGIN') )
	define('FORCE_SSL_LOGIN', false);
force_ssl_login(FORCE_SSL_LOGIN);

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

if ( get_option('active_plugins') && !defined('WP_INSTALLING') ) {
	$current_plugins = get_option('active_plugins');
	if ( is_array($current_plugins) ) {
		foreach ($current_plugins as $plugin) {
			if ( '' != $plugin && 0 == validate_file($plugin) && file_exists(WP_PLUGIN_DIR . '/' . $plugin) )
				include_once(WP_PLUGIN_DIR . '/' . $plugin);
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

$default_constants = array( 'WP_POST_REVISIONS' => true );
foreach ( $default_constants as $c => $v )
	@define( $c, $v ); // will fail if the constant is already defined
unset($default_constants, $c, $v);

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

do_action('setup_theme');

/**
 * Web Path to the current active template directory
 * @since 1.5.0
 */
define('TEMPLATEPATH', get_template_directory());

/**
 * Web Path to the current active template stylesheet directory
 * @since 2.1.0
 */
define('STYLESHEETPATH', get_stylesheet_directory());

// Load the default text localization domain.
load_default_textdomain();

/**
 * The locale of the blog
 * @since 1.5.0
 */
$locale = get_locale();
$locale_file = WP_LANG_DIR . "/$locale.php";
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
 * Runs just before PHP shuts down execution.
 *
 * @access private
 * @since 1.2.0
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
