<?php
/**
 * These functions are needed to load WordPress.
 *
 * @package WordPress
 */

/**
 * Return the HTTP protocol sent by the server.
 *
 * @since 4.4.0
 *
 * @return string The HTTP protocol. Default: HTTP/1.0.
 */
function wp_get_server_protocol() {
	$protocol = isset( $_SERVER['SERVER_PROTOCOL'] ) ? $_SERVER['SERVER_PROTOCOL'] : '';
	if ( ! in_array( $protocol, array( 'HTTP/1.1', 'HTTP/2', 'HTTP/2.0' ) ) ) {
		$protocol = 'HTTP/1.0';
	}
	return $protocol;
}

/**
 * Turn register globals off.
 *
 * @since 2.1.0
 * @access private
 */
function wp_unregister_GLOBALS() {  // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.FunctionNameInvalid
	if ( ! ini_get( 'register_globals' ) ) {
		return;
	}

	if ( isset( $_REQUEST['GLOBALS'] ) ) {
		die( 'GLOBALS overwrite attempt detected' );
	}

	// Variables that shouldn't be unset.
	$no_unset = array( 'GLOBALS', '_GET', '_POST', '_COOKIE', '_REQUEST', '_SERVER', '_ENV', '_FILES', 'table_prefix' );

	$input = array_merge( $_GET, $_POST, $_COOKIE, $_SERVER, $_ENV, $_FILES, isset( $_SESSION ) && is_array( $_SESSION ) ? $_SESSION : array() );
	foreach ( $input as $k => $v ) {
		if ( ! in_array( $k, $no_unset ) && isset( $GLOBALS[ $k ] ) ) {
			unset( $GLOBALS[ $k ] );
		}
	}
}

/**
 * Fix `$_SERVER` variables for various setups.
 *
 * @since 3.0.0
 * @access private
 *
 * @global string $PHP_SELF The filename of the currently executing script,
 *                          relative to the document root.
 */
function wp_fix_server_vars() {
	global $PHP_SELF;

	$default_server_values = array(
		'SERVER_SOFTWARE' => '',
		'REQUEST_URI'     => '',
	);

	$_SERVER = array_merge( $default_server_values, $_SERVER );

	// Fix for IIS when running with PHP ISAPI.
	if ( empty( $_SERVER['REQUEST_URI'] ) || ( PHP_SAPI != 'cgi-fcgi' && preg_match( '/^Microsoft-IIS\//', $_SERVER['SERVER_SOFTWARE'] ) ) ) {

		if ( isset( $_SERVER['HTTP_X_ORIGINAL_URL'] ) ) {
			// IIS Mod-Rewrite.
			$_SERVER['REQUEST_URI'] = $_SERVER['HTTP_X_ORIGINAL_URL'];
		} elseif ( isset( $_SERVER['HTTP_X_REWRITE_URL'] ) ) {
			// IIS Isapi_Rewrite.
			$_SERVER['REQUEST_URI'] = $_SERVER['HTTP_X_REWRITE_URL'];
		} else {
			// Use ORIG_PATH_INFO if there is no PATH_INFO.
			if ( ! isset( $_SERVER['PATH_INFO'] ) && isset( $_SERVER['ORIG_PATH_INFO'] ) ) {
				$_SERVER['PATH_INFO'] = $_SERVER['ORIG_PATH_INFO'];
			}

			// Some IIS + PHP configurations put the script-name in the path-info (no need to append it twice).
			if ( isset( $_SERVER['PATH_INFO'] ) ) {
				if ( $_SERVER['PATH_INFO'] == $_SERVER['SCRIPT_NAME'] ) {
					$_SERVER['REQUEST_URI'] = $_SERVER['PATH_INFO'];
				} else {
					$_SERVER['REQUEST_URI'] = $_SERVER['SCRIPT_NAME'] . $_SERVER['PATH_INFO'];
				}
			}

			// Append the query string if it exists and isn't null.
			if ( ! empty( $_SERVER['QUERY_STRING'] ) ) {
				$_SERVER['REQUEST_URI'] .= '?' . $_SERVER['QUERY_STRING'];
			}
		}
	}

	// Fix for PHP as CGI hosts that set SCRIPT_FILENAME to something ending in php.cgi for all requests.
	if ( isset( $_SERVER['SCRIPT_FILENAME'] ) && ( strpos( $_SERVER['SCRIPT_FILENAME'], 'php.cgi' ) == strlen( $_SERVER['SCRIPT_FILENAME'] ) - 7 ) ) {
		$_SERVER['SCRIPT_FILENAME'] = $_SERVER['PATH_TRANSLATED'];
	}

	// Fix for Dreamhost and other PHP as CGI hosts.
	if ( strpos( $_SERVER['SCRIPT_NAME'], 'php.cgi' ) !== false ) {
		unset( $_SERVER['PATH_INFO'] );
	}

	// Fix empty PHP_SELF.
	$PHP_SELF = $_SERVER['PHP_SELF'];
	if ( empty( $PHP_SELF ) ) {
		$_SERVER['PHP_SELF'] = preg_replace( '/(\?.*)?$/', '', $_SERVER['REQUEST_URI'] );
		$PHP_SELF            = $_SERVER['PHP_SELF'];
	}
}

/**
 * Check for the required PHP version, and the MySQL extension or
 * a database drop-in.
 *
 * Dies if requirements are not met.
 *
 * @since 3.0.0
 * @access private
 *
 * @global string $required_php_version The required PHP version string.
 * @global string $wp_version           The WordPress version string.
 */
function wp_check_php_mysql_versions() {
	global $required_php_version, $wp_version;
	$php_version = phpversion();

	if ( version_compare( $required_php_version, $php_version, '>' ) ) {
		$protocol = wp_get_server_protocol();
		header( sprintf( '%s 500 Internal Server Error', $protocol ), true, 500 );
		header( 'Content-Type: text/html; charset=utf-8' );
		printf( 'Your server is running PHP version %1$s but WordPress %2$s requires at least %3$s.', $php_version, $wp_version, $required_php_version );
		exit( 1 );
	}

	if ( ! extension_loaded( 'mysql' ) && ! extension_loaded( 'mysqli' ) && ! extension_loaded( 'mysqlnd' ) && ! file_exists( WP_CONTENT_DIR . '/db.php' ) ) {
		require_once ABSPATH . WPINC . '/functions.php';
		wp_load_translations_early();
		$args = array(
			'exit' => false,
			'code' => 'mysql_not_found',
		);
		wp_die(
			__( 'Your PHP installation appears to be missing the MySQL extension which is required by WordPress.' ),
			__( 'Requirements Not Met' ),
			$args
		);
		exit( 1 );
	}
}

/**
 * Don't load all of WordPress when handling a favicon.ico request.
 *
 * Instead, send the headers for a zero-length favicon and bail.
 *
 * @since 3.0.0
 * @deprecated 5.4.0 Deprecated in favor of do_favicon().
 */
function wp_favicon_request() {
	if ( '/favicon.ico' == $_SERVER['REQUEST_URI'] ) {
		header( 'Content-Type: image/vnd.microsoft.icon' );
		exit;
	}
}

/**
 * Die with a maintenance message when conditions are met.
 *
 * Checks for a file in the WordPress root directory named ".maintenance".
 * This file will contain the variable $upgrading, set to the time the file
 * was created. If the file was created less than 10 minutes ago, WordPress
 * enters maintenance mode and displays a message.
 *
 * The default message can be replaced by using a drop-in (maintenance.php in
 * the wp-content directory).
 *
 * @since 3.0.0
 * @access private
 *
 * @global int $upgrading the unix timestamp marking when upgrading WordPress began.
 */
function wp_maintenance() {
	if ( ! file_exists( ABSPATH . '.maintenance' ) || wp_installing() ) {
		return;
	}

	global $upgrading;

	require ABSPATH . '.maintenance';
	// If the $upgrading timestamp is older than 10 minutes, don't die.
	if ( ( time() - $upgrading ) >= 600 ) {
		return;
	}

	/**
	 * Filters whether to enable maintenance mode.
	 *
	 * This filter runs before it can be used by plugins. It is designed for
	 * non-web runtimes. If this filter returns true, maintenance mode will be
	 * active and the request will end. If false, the request will be allowed to
	 * continue processing even if maintenance mode should be active.
	 *
	 * @since 4.6.0
	 *
	 * @param bool $enable_checks Whether to enable maintenance mode. Default true.
	 * @param int  $upgrading     The timestamp set in the .maintenance file.
	 */
	if ( ! apply_filters( 'enable_maintenance_mode', true, $upgrading ) ) {
		return;
	}

	if ( file_exists( WP_CONTENT_DIR . '/maintenance.php' ) ) {
		require_once WP_CONTENT_DIR . '/maintenance.php';
		die();
	}

	require_once ABSPATH . WPINC . '/functions.php';
	wp_load_translations_early();

	header( 'Retry-After: 600' );

	wp_die(
		__( 'Briefly unavailable for scheduled maintenance. Check back in a minute.' ),
		__( 'Maintenance' ),
		503
	);
}

/**
 * Start the WordPress micro-timer.
 *
 * @since 0.71
 * @access private
 *
 * @global float $timestart Unix timestamp set at the beginning of the page load.
 * @see timer_stop()
 *
 * @return bool Always returns true.
 */
function timer_start() {
	global $timestart;
	$timestart = microtime( true );
	return true;
}

/**
 * Retrieve or display the time from the page start to when function is called.
 *
 * @since 0.71
 *
 * @global float   $timestart Seconds from when timer_start() is called.
 * @global float   $timeend   Seconds from when function is called.
 *
 * @param int|bool $display   Whether to echo or return the results. Accepts 0|false for return,
 *                            1|true for echo. Default 0|false.
 * @param int      $precision The number of digits from the right of the decimal to display.
 *                            Default 3.
 * @return string The "second.microsecond" finished time calculation. The number is formatted
 *                for human consumption, both localized and rounded.
 */
function timer_stop( $display = 0, $precision = 3 ) {
	global $timestart, $timeend;
	$timeend   = microtime( true );
	$timetotal = $timeend - $timestart;
	$r         = ( function_exists( 'number_format_i18n' ) ) ? number_format_i18n( $timetotal, $precision ) : number_format( $timetotal, $precision );
	if ( $display ) {
		echo $r;
	}
	return $r;
}

/**
 * Set PHP error reporting based on WordPress debug settings.
 *
 * Uses three constants: `WP_DEBUG`, `WP_DEBUG_DISPLAY`, and `WP_DEBUG_LOG`.
 * All three can be defined in wp-config.php. By default, `WP_DEBUG` and
 * `WP_DEBUG_LOG` are set to false, and `WP_DEBUG_DISPLAY` is set to true.
 *
 * When `WP_DEBUG` is true, all PHP notices are reported. WordPress will also
 * display internal notices: when a deprecated WordPress function, function
 * argument, or file is used. Deprecated code may be removed from a later
 * version.
 *
 * It is strongly recommended that plugin and theme developers use `WP_DEBUG`
 * in their development environments.
 *
 * `WP_DEBUG_DISPLAY` and `WP_DEBUG_LOG` perform no function unless `WP_DEBUG`
 * is true.
 *
 * When `WP_DEBUG_DISPLAY` is true, WordPress will force errors to be displayed.
 * `WP_DEBUG_DISPLAY` defaults to true. Defining it as null prevents WordPress
 * from changing the global configuration setting. Defining `WP_DEBUG_DISPLAY`
 * as false will force errors to be hidden.
 *
 * When `WP_DEBUG_LOG` is true, errors will be logged to `wp-content/debug.log`.
 * When `WP_DEBUG_LOG` is a valid path, errors will be logged to the specified file.
 *
 * Errors are never displayed for XML-RPC, REST, and Ajax requests.
 *
 * @since 3.0.0
 * @since 5.1.0 `WP_DEBUG_LOG` can be a file path.
 * @access private
 */
function wp_debug_mode() {
	/**
	 * Filters whether to allow the debug mode check to occur.
	 *
	 * This filter runs before it can be used by plugins. It is designed for
	 * non-web run-times. Returning false causes the `WP_DEBUG` and related
	 * constants to not be checked and the default PHP values for errors
	 * will be used unless you take care to update them yourself.
	 *
	 * @since 4.6.0
	 *
	 * @param bool $enable_debug_mode Whether to enable debug mode checks to occur. Default true.
	 */
	if ( ! apply_filters( 'enable_wp_debug_mode_checks', true ) ) {
		return;
	}

	if ( WP_DEBUG ) {
		error_reporting( E_ALL );

		if ( WP_DEBUG_DISPLAY ) {
			ini_set( 'display_errors', 1 );
		} elseif ( null !== WP_DEBUG_DISPLAY ) {
			ini_set( 'display_errors', 0 );
		}

		if ( in_array( strtolower( (string) WP_DEBUG_LOG ), array( 'true', '1' ), true ) ) {
			$log_path = WP_CONTENT_DIR . '/debug.log';
		} elseif ( is_string( WP_DEBUG_LOG ) ) {
			$log_path = WP_DEBUG_LOG;
		} else {
			$log_path = false;
		}

		if ( $log_path ) {
			ini_set( 'log_errors', 1 );
			ini_set( 'error_log', $log_path );
		}
	} else {
		error_reporting( E_CORE_ERROR | E_CORE_WARNING | E_COMPILE_ERROR | E_ERROR | E_WARNING | E_PARSE | E_USER_ERROR | E_USER_WARNING | E_RECOVERABLE_ERROR );
	}

	if ( defined( 'XMLRPC_REQUEST' ) || defined( 'REST_REQUEST' ) || ( defined( 'WP_INSTALLING' ) && WP_INSTALLING ) || wp_doing_ajax() || wp_is_json_request() ) {
		ini_set( 'display_errors', 0 );
	}
}

/**
 * Set the location of the language directory.
 *
 * To set directory manually, define the `WP_LANG_DIR` constant
 * in wp-config.php.
 *
 * If the language directory exists within `WP_CONTENT_DIR`, it
 * is used. Otherwise the language directory is assumed to live
 * in `WPINC`.
 *
 * @since 3.0.0
 * @access private
 */
function wp_set_lang_dir() {
	if ( ! defined( 'WP_LANG_DIR' ) ) {
		if ( file_exists( WP_CONTENT_DIR . '/languages' ) && @is_dir( WP_CONTENT_DIR . '/languages' ) || ! @is_dir( ABSPATH . WPINC . '/languages' ) ) {
			/**
			 * Server path of the language directory.
			 *
			 * No leading slash, no trailing slash, full path, not relative to ABSPATH
			 *
			 * @since 2.1.0
			 */
			define( 'WP_LANG_DIR', WP_CONTENT_DIR . '/languages' );
			if ( ! defined( 'LANGDIR' ) ) {
				// Old static relative path maintained for limited backward compatibility - won't work in some cases.
				define( 'LANGDIR', 'wp-content/languages' );
			}
		} else {
			/**
			 * Server path of the language directory.
			 *
			 * No leading slash, no trailing slash, full path, not relative to `ABSPATH`.
			 *
			 * @since 2.1.0
			 */
			define( 'WP_LANG_DIR', ABSPATH . WPINC . '/languages' );
			if ( ! defined( 'LANGDIR' ) ) {
				// Old relative path maintained for backward compatibility.
				define( 'LANGDIR', WPINC . '/languages' );
			}
		}
	}
}

/**
 * Load the database class file and instantiate the `$wpdb` global.
 *
 * @since 2.5.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 */
function require_wp_db() {
	global $wpdb;

	require_once ABSPATH . WPINC . '/wp-db.php';
	if ( file_exists( WP_CONTENT_DIR . '/db.php' ) ) {
		require_once WP_CONTENT_DIR . '/db.php';
	}

	if ( isset( $wpdb ) ) {
		return;
	}

	$dbuser     = defined( 'DB_USER' ) ? DB_USER : '';
	$dbpassword = defined( 'DB_PASSWORD' ) ? DB_PASSWORD : '';
	$dbname     = defined( 'DB_NAME' ) ? DB_NAME : '';
	$dbhost     = defined( 'DB_HOST' ) ? DB_HOST : '';

	$wpdb = new wpdb( $dbuser, $dbpassword, $dbname, $dbhost );
}

/**
 * Set the database table prefix and the format specifiers for database
 * table columns.
 *
 * Columns not listed here default to `%s`.
 *
 * @since 3.0.0
 * @access private
 *
 * @global wpdb   $wpdb         WordPress database abstraction object.
 * @global string $table_prefix The database table prefix.
 */
function wp_set_wpdb_vars() {
	global $wpdb, $table_prefix;
	if ( ! empty( $wpdb->error ) ) {
		dead_db();
	}

	$wpdb->field_types = array(
		'post_author'      => '%d',
		'post_parent'      => '%d',
		'menu_order'       => '%d',
		'term_id'          => '%d',
		'term_group'       => '%d',
		'term_taxonomy_id' => '%d',
		'parent'           => '%d',
		'count'            => '%d',
		'object_id'        => '%d',
		'term_order'       => '%d',
		'ID'               => '%d',
		'comment_ID'       => '%d',
		'comment_post_ID'  => '%d',
		'comment_parent'   => '%d',
		'user_id'          => '%d',
		'link_id'          => '%d',
		'link_owner'       => '%d',
		'link_rating'      => '%d',
		'option_id'        => '%d',
		'blog_id'          => '%d',
		'meta_id'          => '%d',
		'post_id'          => '%d',
		'user_status'      => '%d',
		'umeta_id'         => '%d',
		'comment_karma'    => '%d',
		'comment_count'    => '%d',
		// Multisite:
		'active'           => '%d',
		'cat_id'           => '%d',
		'deleted'          => '%d',
		'lang_id'          => '%d',
		'mature'           => '%d',
		'public'           => '%d',
		'site_id'          => '%d',
		'spam'             => '%d',
	);

	$prefix = $wpdb->set_prefix( $table_prefix );

	if ( is_wp_error( $prefix ) ) {
		wp_load_translations_early();
		wp_die(
			sprintf(
				/* translators: 1: $table_prefix, 2: wp-config.php */
				__( '<strong>Error</strong>: %1$s in %2$s can only contain numbers, letters, and underscores.' ),
				'<code>$table_prefix</code>',
				'<code>wp-config.php</code>'
			)
		);
	}
}

/**
 * Toggle `$_wp_using_ext_object_cache` on and off without directly
 * touching global.
 *
 * @since 3.7.0
 *
 * @global bool $_wp_using_ext_object_cache
 *
 * @param bool $using Whether external object cache is being used.
 * @return bool The current 'using' setting.
 */
function wp_using_ext_object_cache( $using = null ) {
	global $_wp_using_ext_object_cache;
	$current_using = $_wp_using_ext_object_cache;
	if ( null !== $using ) {
		$_wp_using_ext_object_cache = $using;
	}
	return $current_using;
}

/**
 * Start the WordPress object cache.
 *
 * If an object-cache.php file exists in the wp-content directory,
 * it uses that drop-in as an external object cache.
 *
 * @since 3.0.0
 * @access private
 *
 * @global array $wp_filter Stores all of the filters.
 */
function wp_start_object_cache() {
	global $wp_filter;
	static $first_init = true;

	// Only perform the following checks once.
	if ( $first_init ) {
		if ( ! function_exists( 'wp_cache_init' ) ) {
			/*
			 * This is the normal situation. First-run of this function. No
			 * caching backend has been loaded.
			 *
			 * We try to load a custom caching backend, and then, if it
			 * results in a wp_cache_init() function existing, we note
			 * that an external object cache is being used.
			 */
			if ( file_exists( WP_CONTENT_DIR . '/object-cache.php' ) ) {
				require_once WP_CONTENT_DIR . '/object-cache.php';
				if ( function_exists( 'wp_cache_init' ) ) {
					wp_using_ext_object_cache( true );
				}

				// Re-initialize any hooks added manually by object-cache.php.
				if ( $wp_filter ) {
					$wp_filter = WP_Hook::build_preinitialized_hooks( $wp_filter );
				}
			}
		} elseif ( ! wp_using_ext_object_cache() && file_exists( WP_CONTENT_DIR . '/object-cache.php' ) ) {
			/*
			 * Sometimes advanced-cache.php can load object-cache.php before
			 * this function is run. This breaks the function_exists() check
			 * above and can result in wp_using_ext_object_cache() returning
			 * false when actually an external cache is in use.
			 */
			wp_using_ext_object_cache( true );
		}
	}

	if ( ! wp_using_ext_object_cache() ) {
		require_once ABSPATH . WPINC . '/cache.php';
	}

	/*
	 * If cache supports reset, reset instead of init if already
	 * initialized. Reset signals to the cache that global IDs
	 * have changed and it may need to update keys and cleanup caches.
	 */
	if ( ! $first_init && function_exists( 'wp_cache_switch_to_blog' ) ) {
		wp_cache_switch_to_blog( get_current_blog_id() );
	} elseif ( function_exists( 'wp_cache_init' ) ) {
		wp_cache_init();
	}

	if ( function_exists( 'wp_cache_add_global_groups' ) ) {
		wp_cache_add_global_groups( array( 'users', 'userlogins', 'usermeta', 'user_meta', 'useremail', 'userslugs', 'site-transient', 'site-options', 'blog-lookup', 'blog-details', 'site-details', 'rss', 'global-posts', 'blog-id-cache', 'networks', 'sites', 'blog_meta' ) );
		wp_cache_add_non_persistent_groups( array( 'counts', 'plugins' ) );
	}

	$first_init = false;
}

/**
 * Redirect to the installer if WordPress is not installed.
 *
 * Dies with an error message when Multisite is enabled.
 *
 * @since 3.0.0
 * @access private
 */
function wp_not_installed() {
	if ( is_multisite() ) {
		if ( ! is_blog_installed() && ! wp_installing() ) {
			nocache_headers();

			wp_die( __( 'The site you have requested is not installed properly. Please contact the system administrator.' ) );
		}
	} elseif ( ! is_blog_installed() && ! wp_installing() ) {
		nocache_headers();

		require ABSPATH . WPINC . '/kses.php';
		require ABSPATH . WPINC . '/pluggable.php';

		$link = wp_guess_url() . '/wp-admin/install.php';

		wp_redirect( $link );
		die();
	}
}

/**
 * Retrieve an array of must-use plugin files.
 *
 * The default directory is wp-content/mu-plugins. To change the default
 * directory manually, define `WPMU_PLUGIN_DIR` and `WPMU_PLUGIN_URL`
 * in wp-config.php.
 *
 * @since 3.0.0
 * @access private
 *
 * @return string[] Array of absolute paths of files to include.
 */
function wp_get_mu_plugins() {
	$mu_plugins = array();
	if ( ! is_dir( WPMU_PLUGIN_DIR ) ) {
		return $mu_plugins;
	}
	$dh = opendir( WPMU_PLUGIN_DIR );
	if ( ! $dh ) {
		return $mu_plugins;
	}
	while ( ( $plugin = readdir( $dh ) ) !== false ) {
		if ( substr( $plugin, -4 ) == '.php' ) {
			$mu_plugins[] = WPMU_PLUGIN_DIR . '/' . $plugin;
		}
	}
	closedir( $dh );
	sort( $mu_plugins );

	return $mu_plugins;
}

/**
 * Retrieve an array of active and valid plugin files.
 *
 * While upgrading or installing WordPress, no plugins are returned.
 *
 * The default directory is `wp-content/plugins`. To change the default
 * directory manually, define `WP_PLUGIN_DIR` and `WP_PLUGIN_URL`
 * in `wp-config.php`.
 *
 * @since 3.0.0
 * @access private
 *
 * @return string[] $plugin_file Array of paths to plugin files relative to the plugins directory.
 */
function wp_get_active_and_valid_plugins() {
	$plugins        = array();
	$active_plugins = (array) get_option( 'active_plugins', array() );

	// Check for hacks file if the option is enabled.
	if ( get_option( 'hack_file' ) && file_exists( ABSPATH . 'my-hacks.php' ) ) {
		_deprecated_file( 'my-hacks.php', '1.5.0' );
		array_unshift( $plugins, ABSPATH . 'my-hacks.php' );
	}

	if ( empty( $active_plugins ) || wp_installing() ) {
		return $plugins;
	}

	$network_plugins = is_multisite() ? wp_get_active_network_plugins() : false;

	foreach ( $active_plugins as $plugin ) {
		if ( ! validate_file( $plugin )                     // $plugin must validate as file.
			&& '.php' == substr( $plugin, -4 )              // $plugin must end with '.php'.
			&& file_exists( WP_PLUGIN_DIR . '/' . $plugin ) // $plugin must exist.
			// Not already included as a network plugin.
			&& ( ! $network_plugins || ! in_array( WP_PLUGIN_DIR . '/' . $plugin, $network_plugins ) )
			) {
			$plugins[] = WP_PLUGIN_DIR . '/' . $plugin;
		}
	}

	/*
	 * Remove plugins from the list of active plugins when we're on an endpoint
	 * that should be protected against WSODs and the plugin is paused.
	 */
	if ( wp_is_recovery_mode() ) {
		$plugins = wp_skip_paused_plugins( $plugins );
	}

	return $plugins;
}

/**
 * Filters a given list of plugins, removing any paused plugins from it.
 *
 * @since 5.2.0
 *
 * @param string[] $plugins Array of absolute plugin main file paths.
 * @return string[] Filtered array of plugins, without any paused plugins.
 */
function wp_skip_paused_plugins( array $plugins ) {
	$paused_plugins = wp_paused_plugins()->get_all();

	if ( empty( $paused_plugins ) ) {
		return $plugins;
	}

	foreach ( $plugins as $index => $plugin ) {
		list( $plugin ) = explode( '/', plugin_basename( $plugin ) );

		if ( array_key_exists( $plugin, $paused_plugins ) ) {
			unset( $plugins[ $index ] );

			// Store list of paused plugins for displaying an admin notice.
			$GLOBALS['_paused_plugins'][ $plugin ] = $paused_plugins[ $plugin ];
		}
	}

	return $plugins;
}

/**
 * Retrieves an array of active and valid themes.
 *
 * While upgrading or installing WordPress, no themes are returned.
 *
 * @since 5.1.0
 * @access private
 *
 * @return string[] Array of absolute paths to theme directories.
 */
function wp_get_active_and_valid_themes() {
	global $pagenow;

	$themes = array();

	if ( wp_installing() && 'wp-activate.php' !== $pagenow ) {
		return $themes;
	}

	if ( TEMPLATEPATH !== STYLESHEETPATH ) {
		$themes[] = STYLESHEETPATH;
	}

	$themes[] = TEMPLATEPATH;

	/*
	 * Remove themes from the list of active themes when we're on an endpoint
	 * that should be protected against WSODs and the theme is paused.
	 */
	if ( wp_is_recovery_mode() ) {
		$themes = wp_skip_paused_themes( $themes );

		// If no active and valid themes exist, skip loading themes.
		if ( empty( $themes ) ) {
			add_filter( 'wp_using_themes', '__return_false' );
		}
	}

	return $themes;
}

/**
 * Filters a given list of themes, removing any paused themes from it.
 *
 * @since 5.2.0
 *
 * @param string[] $themes Array of absolute theme directory paths.
 * @return string[] Filtered array of absolute paths to themes, without any paused themes.
 */
function wp_skip_paused_themes( array $themes ) {
	$paused_themes = wp_paused_themes()->get_all();

	if ( empty( $paused_themes ) ) {
		return $themes;
	}

	foreach ( $themes as $index => $theme ) {
		$theme = basename( $theme );

		if ( array_key_exists( $theme, $paused_themes ) ) {
			unset( $themes[ $index ] );

			// Store list of paused themes for displaying an admin notice.
			$GLOBALS['_paused_themes'][ $theme ] = $paused_themes[ $theme ];
		}
	}

	return $themes;
}

/**
 * Is WordPress in Recovery Mode.
 *
 * In this mode, plugins or themes that cause WSODs will be paused.
 *
 * @since 5.2.0
 *
 * @return bool
 */
function wp_is_recovery_mode() {
	return wp_recovery_mode()->is_active();
}

/**
 * Determines whether we are currently on an endpoint that should be protected against WSODs.
 *
 * @since 5.2.0
 *
 * @return bool True if the current endpoint should be protected.
 */
function is_protected_endpoint() {
	// Protect login pages.
	if ( isset( $GLOBALS['pagenow'] ) && 'wp-login.php' === $GLOBALS['pagenow'] ) {
		return true;
	}

	// Protect the admin backend.
	if ( is_admin() && ! wp_doing_ajax() ) {
		return true;
	}

	// Protect AJAX actions that could help resolve a fatal error should be available.
	if ( is_protected_ajax_action() ) {
		return true;
	}

	/**
	 * Filters whether the current request is against a protected endpoint.
	 *
	 * This filter is only fired when an endpoint is requested which is not already protected by
	 * WordPress core. As such, it exclusively allows providing further protected endpoints in
	 * addition to the admin backend, login pages and protected AJAX actions.
	 *
	 * @since 5.2.0
	 *
	 * @param bool $is_protected_endpoint Whether the currently requested endpoint is protected. Default false.
	 */
	return (bool) apply_filters( 'is_protected_endpoint', false );
}

/**
 * Determines whether we are currently handling an AJAX action that should be protected against WSODs.
 *
 * @since 5.2.0
 *
 * @return bool True if the current AJAX action should be protected.
 */
function is_protected_ajax_action() {
	if ( ! wp_doing_ajax() ) {
		return false;
	}

	if ( ! isset( $_REQUEST['action'] ) ) {
		return false;
	}

	$actions_to_protect = array(
		'edit-theme-plugin-file', // Saving changes in the core code editor.
		'heartbeat',              // Keep the heart beating.
		'install-plugin',         // Installing a new plugin.
		'install-theme',          // Installing a new theme.
		'search-plugins',         // Searching in the list of plugins.
		'search-install-plugins', // Searching for a plugin in the plugin install screen.
		'update-plugin',          // Update an existing plugin.
		'update-theme',           // Update an existing theme.
	);

	/**
	 * Filters the array of protected AJAX actions.
	 *
	 * This filter is only fired when doing AJAX and the AJAX request has an 'action' property.
	 *
	 * @since 5.2.0
	 *
	 * @param string[] $actions_to_protect Array of strings with AJAX actions to protect.
	 */
	$actions_to_protect = (array) apply_filters( 'wp_protected_ajax_actions', $actions_to_protect );

	if ( ! in_array( $_REQUEST['action'], $actions_to_protect, true ) ) {
		return false;
	}

	return true;
}

/**
 * Set internal encoding.
 *
 * In most cases the default internal encoding is latin1, which is
 * of no use, since we want to use the `mb_` functions for `utf-8` strings.
 *
 * @since 3.0.0
 * @access private
 */
function wp_set_internal_encoding() {
	if ( function_exists( 'mb_internal_encoding' ) ) {
		$charset = get_option( 'blog_charset' );
		// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
		if ( ! $charset || ! @mb_internal_encoding( $charset ) ) {
			mb_internal_encoding( 'UTF-8' );
		}
	}
}

/**
 * Add magic quotes to `$_GET`, `$_POST`, `$_COOKIE`, and `$_SERVER`.
 *
 * Also forces `$_REQUEST` to be `$_GET + $_POST`. If `$_SERVER`,
 * `$_COOKIE`, or `$_ENV` are needed, use those superglobals directly.
 *
 * @since 3.0.0
 * @access private
 */
function wp_magic_quotes() {
	// Escape with wpdb.
	$_GET    = add_magic_quotes( $_GET );
	$_POST   = add_magic_quotes( $_POST );
	$_COOKIE = add_magic_quotes( $_COOKIE );
	$_SERVER = add_magic_quotes( $_SERVER );

	/*
	 * Revert the type change to string for two indexes which should retain their proper type.
	 * Among other things, this preserves compatibility of WP with PHPUnit Code Coverage generation.
	 */
	if ( isset( $_SERVER['REQUEST_TIME'] ) ) {
		$_SERVER['REQUEST_TIME'] = (int) $_SERVER['REQUEST_TIME'];
	}

	if ( isset( $_SERVER['REQUEST_TIME_FLOAT'] ) ) {
		$_SERVER['REQUEST_TIME_FLOAT'] = (float) $_SERVER['REQUEST_TIME_FLOAT'];
	}

	// Force REQUEST to be GET + POST.
	$_REQUEST = array_merge( $_GET, $_POST );
}

/**
 * Runs just before PHP shuts down execution.
 *
 * @since 1.2.0
 * @access private
 */
function shutdown_action_hook() {
	/**
	 * Fires just before PHP shuts down execution.
	 *
	 * @since 1.2.0
	 */
	do_action( 'shutdown' );

	wp_cache_close();
}

/**
 * Copy an object.
 *
 * @since 2.7.0
 * @deprecated 3.2.0
 *
 * @param object $object The object to clone.
 * @return object The cloned object.
 */
function wp_clone( $object ) {
	// Use parens for clone to accommodate PHP 4. See #17880.
	return clone( $object );
}

/**
 * Determines whether the current request is for an administrative interface page.
 *
 * Does not check if the user is an administrator; use current_user_can()
 * for checking roles and capabilities.
 *
 * For more information on this and similar theme functions, check out
 * the {@link https://developer.wordpress.org/themes/basics/conditional-tags/
 * Conditional Tags} article in the Theme Developer Handbook.
 *
 * @since 1.5.1
 *
 * @global WP_Screen $current_screen WordPress current screen object.
 *
 * @return bool True if inside WordPress administration interface, false otherwise.
 */
function is_admin() {
	if ( isset( $GLOBALS['current_screen'] ) ) {
		return $GLOBALS['current_screen']->in_admin();
	} elseif ( defined( 'WP_ADMIN' ) ) {
		return WP_ADMIN;
	}

	return false;
}

/**
 * Whether the current request is for a site's administrative interface.
 *
 * e.g. `/wp-admin/`
 *
 * Does not check if the user is an administrator; use current_user_can()
 * for checking roles and capabilities.
 *
 * @since 3.1.0
 *
 * @global WP_Screen $current_screen WordPress current screen object.
 *
 * @return bool True if inside WordPress blog administration pages.
 */
function is_blog_admin() {
	if ( isset( $GLOBALS['current_screen'] ) ) {
		return $GLOBALS['current_screen']->in_admin( 'site' );
	} elseif ( defined( 'WP_BLOG_ADMIN' ) ) {
		return WP_BLOG_ADMIN;
	}

	return false;
}

/**
 * Whether the current request is for the network administrative interface.
 *
 * e.g. `/wp-admin/network/`
 *
 * Does not check if the user is an administrator; use current_user_can()
 * for checking roles and capabilities.
 *
 * Does not check if the site is a Multisite network; use is_multisite()
 * for checking if Multisite is enabled.
 *
 * @since 3.1.0
 *
 * @global WP_Screen $current_screen WordPress current screen object.
 *
 * @return bool True if inside WordPress network administration pages.
 */
function is_network_admin() {
	if ( isset( $GLOBALS['current_screen'] ) ) {
		return $GLOBALS['current_screen']->in_admin( 'network' );
	} elseif ( defined( 'WP_NETWORK_ADMIN' ) ) {
		return WP_NETWORK_ADMIN;
	}

	return false;
}

/**
 * Whether the current request is for a user admin screen.
 *
 * e.g. `/wp-admin/user/`
 *
 * Does not check if the user is an administrator; use current_user_can()
 * for checking roles and capabilities.
 *
 * @since 3.1.0
 *
 * @global WP_Screen $current_screen WordPress current screen object.
 *
 * @return bool True if inside WordPress user administration pages.
 */
function is_user_admin() {
	if ( isset( $GLOBALS['current_screen'] ) ) {
		return $GLOBALS['current_screen']->in_admin( 'user' );
	} elseif ( defined( 'WP_USER_ADMIN' ) ) {
		return WP_USER_ADMIN;
	}

	return false;
}

/**
 * If Multisite is enabled.
 *
 * @since 3.0.0
 *
 * @return bool True if Multisite is enabled, false otherwise.
 */
function is_multisite() {
	if ( defined( 'MULTISITE' ) ) {
		return MULTISITE;
	}

	if ( defined( 'SUBDOMAIN_INSTALL' ) || defined( 'VHOST' ) || defined( 'SUNRISE' ) ) {
		return true;
	}

	return false;
}

/**
 * Retrieve the current site ID.
 *
 * @since 3.1.0
 *
 * @global int $blog_id
 *
 * @return int Site ID.
 */
function get_current_blog_id() {
	global $blog_id;
	return absint( $blog_id );
}

/**
 * Retrieves the current network ID.
 *
 * @since 4.6.0
 *
 * @return int The ID of the current network.
 */
function get_current_network_id() {
	if ( ! is_multisite() ) {
		return 1;
	}

	$current_network = get_network();

	if ( ! isset( $current_network->id ) ) {
		return get_main_network_id();
	}

	return absint( $current_network->id );
}

/**
 * Attempt an early load of translations.
 *
 * Used for errors encountered during the initial loading process, before
 * the locale has been properly detected and loaded.
 *
 * Designed for unusual load sequences (like setup-config.php) or for when
 * the script will then terminate with an error, otherwise there is a risk
 * that a file can be double-included.
 *
 * @since 3.4.0
 * @access private
 *
 * @global WP_Locale $wp_locale WordPress date and time locale object.
 *
 * @staticvar bool $loaded
 */
function wp_load_translations_early() {
	global $wp_locale;

	static $loaded = false;
	if ( $loaded ) {
		return;
	}
	$loaded = true;

	if ( function_exists( 'did_action' ) && did_action( 'init' ) ) {
		return;
	}

	// We need $wp_local_package.
	require ABSPATH . WPINC . '/version.php';

	// Translation and localization.
	require_once ABSPATH . WPINC . '/pomo/mo.php';
	require_once ABSPATH . WPINC . '/l10n.php';
	require_once ABSPATH . WPINC . '/class-wp-locale.php';
	require_once ABSPATH . WPINC . '/class-wp-locale-switcher.php';

	// General libraries.
	require_once ABSPATH . WPINC . '/plugin.php';

	$locales   = array();
	$locations = array();

	while ( true ) {
		if ( defined( 'WPLANG' ) ) {
			if ( '' == WPLANG ) {
				break;
			}
			$locales[] = WPLANG;
		}

		if ( isset( $wp_local_package ) ) {
			$locales[] = $wp_local_package;
		}

		if ( ! $locales ) {
			break;
		}

		if ( defined( 'WP_LANG_DIR' ) && @is_dir( WP_LANG_DIR ) ) {
			$locations[] = WP_LANG_DIR;
		}

		if ( defined( 'WP_CONTENT_DIR' ) && @is_dir( WP_CONTENT_DIR . '/languages' ) ) {
			$locations[] = WP_CONTENT_DIR . '/languages';
		}

		if ( @is_dir( ABSPATH . 'wp-content/languages' ) ) {
			$locations[] = ABSPATH . 'wp-content/languages';
		}

		if ( @is_dir( ABSPATH . WPINC . '/languages' ) ) {
			$locations[] = ABSPATH . WPINC . '/languages';
		}

		if ( ! $locations ) {
			break;
		}

		$locations = array_unique( $locations );

		foreach ( $locales as $locale ) {
			foreach ( $locations as $location ) {
				if ( file_exists( $location . '/' . $locale . '.mo' ) ) {
					load_textdomain( 'default', $location . '/' . $locale . '.mo' );
					if ( defined( 'WP_SETUP_CONFIG' ) && file_exists( $location . '/admin-' . $locale . '.mo' ) ) {
						load_textdomain( 'default', $location . '/admin-' . $locale . '.mo' );
					}
					break 2;
				}
			}
		}

		break;
	}

	$wp_locale = new WP_Locale();
}

/**
 * Check or set whether WordPress is in "installation" mode.
 *
 * If the `WP_INSTALLING` constant is defined during the bootstrap, `wp_installing()` will default to `true`.
 *
 * @since 4.4.0
 *
 * @staticvar bool $installing
 *
 * @param bool $is_installing Optional. True to set WP into Installing mode, false to turn Installing mode off.
 *                            Omit this parameter if you only want to fetch the current status.
 * @return bool True if WP is installing, otherwise false. When a `$is_installing` is passed, the function will
 *              report whether WP was in installing mode prior to the change to `$is_installing`.
 */
function wp_installing( $is_installing = null ) {
	static $installing = null;

	// Support for the `WP_INSTALLING` constant, defined before WP is loaded.
	if ( is_null( $installing ) ) {
		$installing = defined( 'WP_INSTALLING' ) && WP_INSTALLING;
	}

	if ( ! is_null( $is_installing ) ) {
		$old_installing = $installing;
		$installing     = $is_installing;
		return (bool) $old_installing;
	}

	return (bool) $installing;
}

/**
 * Determines if SSL is used.
 *
 * @since 2.6.0
 * @since 4.6.0 Moved from functions.php to load.php.
 *
 * @return bool True if SSL, otherwise false.
 */
function is_ssl() {
	if ( isset( $_SERVER['HTTPS'] ) ) {
		if ( 'on' == strtolower( $_SERVER['HTTPS'] ) ) {
			return true;
		}

		if ( '1' == $_SERVER['HTTPS'] ) {
			return true;
		}
	} elseif ( isset( $_SERVER['SERVER_PORT'] ) && ( '443' == $_SERVER['SERVER_PORT'] ) ) {
		return true;
	}
	return false;
}

/**
 * Converts a shorthand byte value to an integer byte value.
 *
 * @since 2.3.0
 * @since 4.6.0 Moved from media.php to load.php.
 *
 * @link https://www.php.net/manual/en/function.ini-get.php
 * @link https://www.php.net/manual/en/faq.using.php#faq.using.shorthandbytes
 *
 * @param string $value A (PHP ini) byte value, either shorthand or ordinary.
 * @return int An integer byte value.
 */
function wp_convert_hr_to_bytes( $value ) {
	$value = strtolower( trim( $value ) );
	$bytes = (int) $value;

	if ( false !== strpos( $value, 'g' ) ) {
		$bytes *= GB_IN_BYTES;
	} elseif ( false !== strpos( $value, 'm' ) ) {
		$bytes *= MB_IN_BYTES;
	} elseif ( false !== strpos( $value, 'k' ) ) {
		$bytes *= KB_IN_BYTES;
	}

	// Deal with large (float) values which run into the maximum integer size.
	return min( $bytes, PHP_INT_MAX );
}

/**
 * Determines whether a PHP ini value is changeable at runtime.
 *
 * @since 4.6.0
 *
 * @staticvar array $ini_all
 *
 * @link https://www.php.net/manual/en/function.ini-get-all.php
 *
 * @param string $setting The name of the ini setting to check.
 * @return bool True if the value is changeable at runtime. False otherwise.
 */
function wp_is_ini_value_changeable( $setting ) {
	static $ini_all;

	if ( ! isset( $ini_all ) ) {
		$ini_all = false;
		// Sometimes `ini_get_all()` is disabled via the `disable_functions` option for "security purposes".
		if ( function_exists( 'ini_get_all' ) ) {
			$ini_all = ini_get_all();
		}
	}

	// Bit operator to workaround https://bugs.php.net/bug.php?id=44936 which changes access level to 63 in PHP 5.2.6 - 5.2.17.
	if ( isset( $ini_all[ $setting ]['access'] ) && ( INI_ALL === ( $ini_all[ $setting ]['access'] & 7 ) || INI_USER === ( $ini_all[ $setting ]['access'] & 7 ) ) ) {
		return true;
	}

	// If we were unable to retrieve the details, fail gracefully to assume it's changeable.
	if ( ! is_array( $ini_all ) ) {
		return true;
	}

	return false;
}

/**
 * Determines whether the current request is a WordPress Ajax request.
 *
 * @since 4.7.0
 *
 * @return bool True if it's a WordPress Ajax request, false otherwise.
 */
function wp_doing_ajax() {
	/**
	 * Filters whether the current request is a WordPress Ajax request.
	 *
	 * @since 4.7.0
	 *
	 * @param bool $wp_doing_ajax Whether the current request is a WordPress Ajax request.
	 */
	return apply_filters( 'wp_doing_ajax', defined( 'DOING_AJAX' ) && DOING_AJAX );
}

/**
 * Determines whether the current request should use themes.
 *
 * @since 5.1.0
 *
 * @return bool True if themes should be used, false otherwise.
 */
function wp_using_themes() {
	/**
	 * Filters whether the current request should use themes.
	 *
	 * @since 5.1.0
	 *
	 * @param bool $wp_using_themes Whether the current request should use themes.
	 */
	return apply_filters( 'wp_using_themes', defined( 'WP_USE_THEMES' ) && WP_USE_THEMES );
}

/**
 * Determines whether the current request is a WordPress cron request.
 *
 * @since 4.8.0
 *
 * @return bool True if it's a WordPress cron request, false otherwise.
 */
function wp_doing_cron() {
	/**
	 * Filters whether the current request is a WordPress cron request.
	 *
	 * @since 4.8.0
	 *
	 * @param bool $wp_doing_cron Whether the current request is a WordPress cron request.
	 */
	return apply_filters( 'wp_doing_cron', defined( 'DOING_CRON' ) && DOING_CRON );
}

/**
 * Check whether variable is a WordPress Error.
 *
 * Returns true if $thing is an object of the WP_Error class.
 *
 * @since 2.1.0
 *
 * @param mixed $thing Check if unknown variable is a WP_Error object.
 * @return bool True, if WP_Error. False, if not WP_Error.
 */
function is_wp_error( $thing ) {
	return ( $thing instanceof WP_Error );
}

/**
 * Determines whether file modifications are allowed.
 *
 * @since 4.8.0
 *
 * @param string $context The usage context.
 * @return bool True if file modification is allowed, false otherwise.
 */
function wp_is_file_mod_allowed( $context ) {
	/**
	 * Filters whether file modifications are allowed.
	 *
	 * @since 4.8.0
	 *
	 * @param bool   $file_mod_allowed Whether file modifications are allowed.
	 * @param string $context          The usage context.
	 */
	return apply_filters( 'file_mod_allowed', ! defined( 'DISALLOW_FILE_MODS' ) || ! DISALLOW_FILE_MODS, $context );
}

/**
 * Start scraping edited file errors.
 *
 * @since 4.9.0
 */
function wp_start_scraping_edited_file_errors() {
	if ( ! isset( $_REQUEST['wp_scrape_key'] ) || ! isset( $_REQUEST['wp_scrape_nonce'] ) ) {
		return;
	}
	$key   = substr( sanitize_key( wp_unslash( $_REQUEST['wp_scrape_key'] ) ), 0, 32 );
	$nonce = wp_unslash( $_REQUEST['wp_scrape_nonce'] );

	if ( get_transient( 'scrape_key_' . $key ) !== $nonce ) {
		echo "###### wp_scraping_result_start:$key ######";
		echo wp_json_encode(
			array(
				'code'    => 'scrape_nonce_failure',
				'message' => __( 'Scrape nonce check failed. Please try again.' ),
			)
		);
		echo "###### wp_scraping_result_end:$key ######";
		die();
	}
	if ( ! defined( 'WP_SANDBOX_SCRAPING' ) ) {
		define( 'WP_SANDBOX_SCRAPING', true );
	}
	register_shutdown_function( 'wp_finalize_scraping_edited_file_errors', $key );
}

/**
 * Finalize scraping for edited file errors.
 *
 * @since 4.9.0
 *
 * @param string $scrape_key Scrape key.
 */
function wp_finalize_scraping_edited_file_errors( $scrape_key ) {
	$error = error_get_last();
	echo "\n###### wp_scraping_result_start:$scrape_key ######\n";
	if ( ! empty( $error ) && in_array( $error['type'], array( E_CORE_ERROR, E_COMPILE_ERROR, E_ERROR, E_PARSE, E_USER_ERROR, E_RECOVERABLE_ERROR ), true ) ) {
		$error = str_replace( ABSPATH, '', $error );
		echo wp_json_encode( $error );
	} else {
		echo wp_json_encode( true );
	}
	echo "\n###### wp_scraping_result_end:$scrape_key ######\n";
}

/**
 * Checks whether current request is a JSON request, or is expecting a JSON response.
 *
 * @since 5.0.0
 *
 * @return bool True if `Accepts` or `Content-Type` headers contain `application/json`.
 *              False otherwise.
 */
function wp_is_json_request() {

	if ( isset( $_SERVER['HTTP_ACCEPT'] ) && false !== strpos( $_SERVER['HTTP_ACCEPT'], 'application/json' ) ) {
		return true;
	}

	if ( isset( $_SERVER['CONTENT_TYPE'] ) && 'application/json' === $_SERVER['CONTENT_TYPE'] ) {
		return true;
	}

	return false;

}

/**
 * Checks whether current request is a JSONP request, or is expecting a JSONP response.
 *
 * @since 5.2.0
 *
 * @return bool True if JSONP request, false otherwise.
 */
function wp_is_jsonp_request() {
	if ( ! isset( $_GET['_jsonp'] ) ) {
		return false;
	}

	if ( ! function_exists( 'wp_check_jsonp_callback' ) ) {
		require_once ABSPATH . WPINC . '/functions.php';
	}

	$jsonp_callback = $_GET['_jsonp'];
	if ( ! wp_check_jsonp_callback( $jsonp_callback ) ) {
		return false;
	}

	/** This filter is documented in wp-includes/rest-api/class-wp-rest-server.php */
	$jsonp_enabled = apply_filters( 'rest_jsonp_enabled', true );

	return $jsonp_enabled;

}

/**
 * Checks whether current request is an XML request, or is expecting an XML response.
 *
 * @since 5.2.0
 *
 * @return bool True if `Accepts` or `Content-Type` headers contain `text/xml`
 *              or one of the related MIME types. False otherwise.
 */
function wp_is_xml_request() {
	$accepted = array(
		'text/xml',
		'application/rss+xml',
		'application/atom+xml',
		'application/rdf+xml',
		'text/xml+oembed',
		'application/xml+oembed',
	);

	if ( isset( $_SERVER['HTTP_ACCEPT'] ) ) {
		foreach ( $accepted as $type ) {
			if ( false !== strpos( $_SERVER['HTTP_ACCEPT'], $type ) ) {
				return true;
			}
		}
	}

	if ( isset( $_SERVER['CONTENT_TYPE'] ) && in_array( $_SERVER['CONTENT_TYPE'], $accepted, true ) ) {
		return true;
	}

	return false;
}
