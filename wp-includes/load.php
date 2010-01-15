<?php

/**
 * These functions are needed to load WordPress.
 *
 * @package WordPress
 */

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

function wp_fix_server_vars() {
	global $PHP_SELF;
	// Fix for IIS when running with PHP ISAPI
	if ( empty( $_SERVER['REQUEST_URI'] ) || ( php_sapi_name() != 'cgi-fcgi' && preg_match( '/^Microsoft-IIS\//', $_SERVER['SERVER_SOFTWARE'] ) ) ) {

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
}

function wp_check_php_mysql_versions() {
	// we can probably extend this function to check if wp_die() exists then use translated strings, and then use it in install.php etc.

	global $required_php_version, $wp_version;
	$php_version = phpversion();
	if ( version_compare( $required_php_version, $php_version, '>' ) )
		die( sprintf( /*WP_I18N_OLD_PHP*/'Your server is running PHP version %1$s but WordPress %2%s requires at least %2%s.'/*/WP_I18N_OLD_PHP*/, $php_version, $wp_version, $required_php_version ) );

	if ( !extension_loaded('mysql') && !file_exists(WP_CONTENT_DIR . '/db.php') )
		die( /*WP_I18N_OLD_MYSQL*/'Your PHP installation appears to be missing the MySQL extension which is required by WordPress.'/*/WP_I18N_OLD_MYSQL*/ );
}

function wp_maintenance() {
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
			header( 'Retry-After: 600' );
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
}

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

function wp_debug_mode() {
	if ( WP_DEBUG ) {
		if ( defined('E_DEPRECATED') )
			error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
		else
			error_reporting(E_ALL);

		if ( WP_DEBUG_DISPLAY )
			ini_set('display_errors', 1);

		if ( WP_DEBUG_LOG ) {
			ini_set('log_errors', 1);
			ini_set('error_log', WP_CONTENT_DIR . '/debug.log');
		}
	} else {
		if ( defined('E_RECOVERABLE_ERROR') )
			error_reporting(E_ERROR | E_WARNING | E_PARSE | E_USER_ERROR | E_USER_WARNING | E_RECOVERABLE_ERROR);
		else
			error_reporting(E_ERROR | E_WARNING | E_PARSE | E_USER_ERROR | E_USER_WARNING);
	}
}

function wp_set_lang_dir() {
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
}

function wp_set_wpdb_vars() {
	global $wpdb, $table_prefix;
	if ( !empty($wpdb->error) )
		dead_db();

	/**
	 * Format specifiers for DB columns. Columns not listed here default to %s.
	 * @since 2.8.0
	 * @see wpdb:$field_types
	 * @see wpdb:prepare()
	 * @see wpdb:insert()
	 * @see wpdb:update()
	 */
	$wpdb->field_types = array( 'post_author' => '%d', 'post_parent' => '%d', 'menu_order' => '%d', 'term_id' => '%d', 'term_group' => '%d', 'term_taxonomy_id' => '%d',
		'parent' => '%d', 'count' => '%d','object_id' => '%d', 'term_order' => '%d', 'ID' => '%d', 'commment_ID' => '%d', 'comment_post_ID' => '%d', 'comment_parent' => '%d',
		'user_id' => '%d', 'link_id' => '%d', 'link_owner' => '%d', 'link_rating' => '%d', 'option_id' => '%d', 'blog_id' => '%d', 'meta_id' => '%d', 'post_id' => '%d',
		'user_status' => '%d', 'umeta_id' => '%d', 'comment_karma' => '%d', 'comment_count' => '%d');

	$prefix = $wpdb->set_prefix($table_prefix);

	if ( is_wp_error($prefix) )
		wp_die(/*WP_I18N_BAD_PREFIX*/'<strong>ERROR</strong>: <code>$table_prefix</code> in <code>wp-config.php</code> can only contain numbers, letters, and underscores.'/*/WP_I18N_BAD_PREFIX*/);

}

function wp_start_object_cache() {
	global $_wp_using_ext_object_cache;
	if ( file_exists(WP_CONTENT_DIR . '/object-cache.php') ) {
		require_once (WP_CONTENT_DIR . '/object-cache.php');
		$_wp_using_ext_object_cache = true;
	} else {
		require_once (ABSPATH . WPINC . '/cache.php');
		$_wp_using_ext_object_cache = false;
	}

	wp_cache_init();
	if ( function_exists('wp_cache_add_global_groups') ) {
			if( is_multisite() ) {
					wp_cache_add_global_groups(array ('users', 'userlogins', 'usermeta', 'site-transient', 'site-options', 'site-lookup', 'blog-lookup', 'blog-details', 'rss'));
			} else {
				wp_cache_add_global_groups(array ('users', 'userlogins', 'usermeta', 'site-transient'));
			}
		wp_cache_add_non_persistent_groups(array( 'comment', 'counts', 'plugins' ));
	}
}

function wp_not_installed() {
	if ( is_multisite() ) {
			if ( !is_blog_installed() && !defined('WP_INSTALLING') )
					die( __( 'The blog you have requested is not installed properly. Please contact the system administrator.' ) ); // have to die here ~ Mark
	} elseif ( !is_blog_installed() && (strpos($_SERVER['PHP_SELF'], 'install.php') === false && !defined('WP_INSTALLING')) ) {
		if ( defined('WP_SITEURL') )
			$link = WP_SITEURL . '/wp-admin/install.php';
		elseif (strpos($_SERVER['PHP_SELF'], 'wp-admin') !== false)
			$link = preg_replace('|/wp-admin/?.*?$|', '/', $_SERVER['PHP_SELF']) . 'wp-admin/install.php';
		else
			$link = preg_replace('|/[^/]+?$|', '/', $_SERVER['PHP_SELF']) . 'wp-admin/install.php';
		require_once(ABSPATH . WPINC . '/kses.php');
		require_once(ABSPATH . WPINC . '/pluggable.php');
		require_once(ABSPATH . WPINC . '/formatting.php');
		wp_redirect($link);
		die();
	}
}

function wp_load_mu_plugins() {
	if ( is_dir( WPMU_PLUGIN_DIR ) ) {
		if ( $dh = opendir( WPMU_PLUGIN_DIR ) ) {
			$mu_plugins = array ();
			while ( ( $plugin = readdir( $dh ) ) !== false )
				if ( substr( $plugin, -4 ) == '.php' )
					$mu_plugins[] = $plugin;
			closedir( $dh );
					if( is_multisite() )
					sort( $mu_plugins );
			foreach( $mu_plugins as $mu_plugin )
				include_once( WPMU_PLUGIN_DIR . '/' . $mu_plugin );
		}
	}
}

function wp_load_plugins() {
	// Check for hacks file if the option is enabled
	if ( get_option('hack_file') ) {
		if ( file_exists(ABSPATH . 'my-hacks.php') )
			require(ABSPATH . 'my-hacks.php');
	}

	$current_plugins = apply_filters( 'active_plugins', get_option( 'active_plugins' ) );
	if ( is_array($current_plugins) && !defined('WP_INSTALLING') ) {
		foreach ( $current_plugins as $plugin ) {
			// check the $plugin filename
			// Validate plugin filename
			if ( validate_file($plugin) // $plugin must validate as file
				|| '.php' != substr($plugin, -4) // $plugin must end with '.php'
				|| !file_exists(WP_PLUGIN_DIR . '/' . $plugin)	// $plugin must exist
				)
				continue;

			include_once(WP_PLUGIN_DIR . '/' . $plugin);
		}
		unset($plugin);
	}
	unset($current_plugins);
}

function wp_set_internal_encoding() {
	/*
	 * In most cases the default internal encoding is latin1, which is of no use,
	 * since we want to use the mb_ functions for utf-8 strings
	 */
	if (function_exists('mb_internal_encoding')) {
		if (!@mb_internal_encoding(get_option('blog_charset')))
			mb_internal_encoding('UTF-8');
	}
}

function wp_magic_quotes() {
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

	// Force REQUEST to be GET + POST.  If SERVER, COOKIE, or ENV are needed, use those superglobals directly.
	$_REQUEST = array_merge($_GET, $_POST);
}

function wp_find_locale() {
	global $locale, $locale_file;
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
}

function wp_load_theme_functions() {
	// Load functions for active theme.
	if ( TEMPLATEPATH !== STYLESHEETPATH && file_exists(STYLESHEETPATH . '/functions.php') )
		include(STYLESHEETPATH . '/functions.php');
	if ( file_exists(TEMPLATEPATH . '/functions.php') )
		include(TEMPLATEPATH . '/functions.php');

	// Load in support for template functions which the theme supports
	require_if_theme_supports( 'post-thumbnails', ABSPATH . WPINC . '/post-thumbnail-template.php' );
}

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

/**
 * Copy an object.
 *
 * Returns a cloned copy of an object.
 *
 * @since 2.7.0
 *
 * @param object $object The object to clone
 * @return object The cloned object
 */
function wp_clone( $object ) {
	static $can_clone;
	if ( !isset( $can_clone ) ) {
		$can_clone = version_compare( phpversion(), '5.0', '>=' );
	}
	return $can_clone ? clone( $object ) : $object;
}

/**
 * Whether the current request is in WordPress admin Panel
 *
 * Does not inform on whether the user is an admin! Use capability checks to
 * tell if the user should be accessing a section or not.
 *
 * @since 1.5.1
 *
 * @return bool True if inside WordPress administration pages.
 */
function is_admin() {
	if ( defined('WP_ADMIN') )
		return WP_ADMIN;
	return false;
}

/**
 * Whether Multisite support is enabled
 *
 * @since 3.0
 *
 * @return bool True if multisite is enabled, false otherwise.
 */
function is_multisite() {
	if ( ( defined('MULTISITE') && MULTISITE ) || defined('VHOST') || defined('SUNRISE') )
		return true;

	return false;
}

?>