<?php
/**
 * A simple set of functions to check the WordPress.org Version Update service.
 *
 * @package WordPress
 * @since 2.3.0
 */

/**
 * Checks WordPress version against the newest version.
 *
 * The WordPress version, PHP version, and locale is sent.
 *
 * Checks against the WordPress API. Will only check if WordPress isn't installing.
 *
 * @since 2.3.0
 *
 * @global string $wp_version       Used to check against the newest WordPress version.
 * @global wpdb   $wpdb             WordPress database abstraction object.
 * @global string $wp_local_package Locale code of the package.
 *
 * @param array $extra_stats Extra statistics to report to the WordPress.org API.
 * @param bool  $force_check Whether to bypass the transient cache and force a fresh update check.
 *                           Defaults to false, true if $extra_stats is set.
 */
function wp_version_check( $extra_stats = array(), $force_check = false ) {
	global $wpdb, $wp_local_package;

	if ( wp_installing() ) {
		return;
	}

	$php_version = PHP_VERSION;

	$current      = get_site_transient( 'update_core' );
	$translations = wp_get_installed_translations( 'core' );

	// Invalidate the transient when $wp_version changes.
	if ( is_object( $current ) && wp_get_wp_version() !== $current->version_checked ) {
		$current = false;
	}

	if ( ! is_object( $current ) ) {
		$current                  = new stdClass();
		$current->updates         = array();
		$current->version_checked = wp_get_wp_version();
	}

	if ( ! empty( $extra_stats ) ) {
		$force_check = true;
	}

	// Wait 1 minute between multiple version check requests.
	$timeout          = MINUTE_IN_SECONDS;
	$time_not_changed = isset( $current->last_checked ) && $timeout > ( time() - $current->last_checked );

	if ( ! $force_check && $time_not_changed ) {
		return;
	}

	/**
	 * Filters the locale requested for WordPress core translations.
	 *
	 * @since 2.8.0
	 *
	 * @param string $locale Current locale.
	 */
	$locale = apply_filters( 'core_version_check_locale', get_locale() );

	// Update last_checked for current to prevent multiple blocking requests if request hangs.
	$current->last_checked = time();
	set_site_transient( 'update_core', $current );

	if ( method_exists( $wpdb, 'db_server_info' ) ) {
		$mysql_version = $wpdb->db_server_info();
	} elseif ( method_exists( $wpdb, 'db_version' ) ) {
		$mysql_version = preg_replace( '/[^0-9.].*/', '', $wpdb->db_version() );
	} else {
		$mysql_version = 'N/A';
	}

	if ( is_multisite() ) {
		$num_blogs         = get_blog_count();
		$wp_install        = network_site_url();
		$multisite_enabled = 1;
	} else {
		$multisite_enabled = 0;
		$num_blogs         = 1;
		$wp_install        = home_url( '/' );
	}

	$extensions = get_loaded_extensions();
	sort( $extensions, SORT_STRING | SORT_FLAG_CASE );
	$query = array(
		'version'            => wp_get_wp_version(),
		'php'                => $php_version,
		'locale'             => $locale,
		'mysql'              => $mysql_version,
		'local_package'      => isset( $wp_local_package ) ? $wp_local_package : '',
		'blogs'              => $num_blogs,
		'users'              => get_user_count(),
		'multisite_enabled'  => $multisite_enabled,
		'initial_db_version' => get_site_option( 'initial_db_version' ),
		'extensions'         => array_combine( $extensions, array_map( 'phpversion', $extensions ) ),
		'platform_flags'     => array(
			'os'   => PHP_OS,
			'bits' => PHP_INT_SIZE === 4 ? 32 : 64,
		),
		'image_support'      => array(),
	);

	if ( function_exists( 'gd_info' ) ) {
		$gd_info = gd_info();
		// Filter to supported values.
		$gd_info = array_filter( $gd_info );

		// Add data for GD WebP, AVIF, HEIC and JPEG XL support.
		$query['image_support']['gd'] = array_keys(
			array_filter(
				array(
					'webp' => isset( $gd_info['WebP Support'] ),
					'avif' => isset( $gd_info['AVIF Support'] ),
					'heic' => isset( $gd_info['HEIC Support'] ),
					'jxl'  => isset( $gd_info['JXL Support'] ),
				)
			)
		);
	}

	if ( class_exists( 'Imagick' ) ) {
		// Add data for Imagick WebP, AVIF, HEIC and JPEG XL support.
		$query['image_support']['imagick'] = array_keys(
			array_filter(
				array(
					'webp' => ! empty( Imagick::queryFormats( 'WEBP' ) ),
					'avif' => ! empty( Imagick::queryFormats( 'AVIF' ) ),
					'heic' => ! empty( Imagick::queryFormats( 'HEIC' ) ),
					'jxl'  => ! empty( Imagick::queryFormats( 'JXL' ) ),
				)
			)
		);
	}

	/**
	 * Filters the query arguments sent as part of the core version check.
	 *
	 * WARNING: Changing this data may result in your site not receiving security updates.
	 * Please exercise extreme caution.
	 *
	 * @since 4.9.0
	 *
	 * @param array $query {
	 *     Version check query arguments.
	 *
	 *     @type string $version            WordPress version number.
	 *     @type string $php                PHP version number.
	 *     @type string $locale             The locale to retrieve updates for.
	 *     @type string $mysql              MySQL version number.
	 *     @type string $local_package      The value of the $wp_local_package global, when set.
	 *     @type int    $blogs              Number of sites on this WordPress installation.
	 *     @type int    $users              Number of users on this WordPress installation.
	 *     @type int    $multisite_enabled  Whether this WordPress installation uses Multisite.
	 *     @type int    $initial_db_version Database version of WordPress at time of installation.
	 * }
	 */
	$query = apply_filters( 'core_version_check_query_args', $query );

	$post_body = array(
		'translations' => wp_json_encode( $translations ),
	);

	if ( is_array( $extra_stats ) ) {
		$post_body = array_merge( $post_body, $extra_stats );
	}

	// Allow for WP_AUTO_UPDATE_CORE to specify beta/RC/development releases.
	if ( defined( 'WP_AUTO_UPDATE_CORE' )
		&& in_array( WP_AUTO_UPDATE_CORE, array( 'beta', 'rc', 'development', 'branch-development' ), true )
	) {
		$query['channel'] = WP_AUTO_UPDATE_CORE;
	}

	$url      = wp_get_update_api_base( $https = false ). '/core/version-check/1.7/?' . http_build_query( $query, '', '&' );
	$http_url = $url;
	$ssl      = wp_http_supports( array( 'ssl' ) );

	if ( $ssl ) {
		$url = set_url_scheme( $url, 'https' );
	}

	$doing_cron = wp_doing_cron();

	$options = array(
		'timeout'    => $doing_cron ? 30 : 3,
		'user-agent' => 'WordPress/' . wp_get_wp_version() . '; ' . home_url( '/' ),
		'headers'    => array(
			'wp_install' => $wp_install,
			'wp_blog'    => home_url( '/' ),
		),
		'body'       => $post_body,
	);

	$response = wp_remote_post( $url, $options );

	if ( $ssl && is_wp_error( $response ) ) {
		wp_trigger_error(
			__FUNCTION__,
			sprintf(
				/* translators: %s: Support forums URL. */
				__( 'An unexpected error occurred. Something may be wrong with WordPress.org or this server&#8217;s configuration. If you continue to have problems, please try the <a href="%s">support forums</a>.' ),
				__( 'https://wordpress.org/support/forums/' )
			) . ' ' . __( '(WordPress could not establish a secure connection to WordPress.org. Please contact your server administrator.)' ),
			headers_sent() || WP_DEBUG ? E_USER_WARNING : E_USER_NOTICE
		);
		$response = wp_remote_post( $http_url, $options );
	}

	if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
		return;
	}

	$body = trim( wp_remote_retrieve_body( $response ) );
	$body = json_decode( $body, true );

	if ( ! is_array( $body ) || ! isset( $body['offers'] ) ) {
		return;
	}

	$offers = $body['offers'];

	foreach ( $offers as &$offer ) {
		foreach ( $offer as $offer_key => $value ) {
			if ( 'packages' === $offer_key ) {
				$offer['packages'] = (object) array_intersect_key(
					array_map( 'esc_url', $offer['packages'] ),
					array_fill_keys( array( 'full', 'no_content', 'new_bundled', 'partial', 'rollback' ), '' )
				);
			} elseif ( 'download' === $offer_key ) {
				$offer['download'] = esc_url( $value );
			} else {
				$offer[ $offer_key ] = esc_html( $value );
			}
		}
		$offer = (object) array_intersect_key(
			$offer,
			array_fill_keys(
				array(
					'response',
					'download',
					'locale',
					'packages',
					'current',
					'version',
					'php_version',
					'mysql_version',
					'new_bundled',
					'partial_version',
					'notify_email',
					'support_email',
					'new_files',
				),
				''
			)
		);
	}

	$updates                  = new stdClass();
	$updates->updates         = $offers;
	$updates->last_checked    = time();
	$updates->version_checked = wp_get_wp_version();

	if ( isset( $body['translations'] ) ) {
		$updates->translations = $body['translations'];
	}

	set_site_transient( 'update_core', $updates );

	if ( ! empty( $body['ttl'] ) ) {
		$ttl = (int) $body['ttl'];

		if ( $ttl && ( time() + $ttl < wp_next_scheduled( 'wp_version_check' ) ) ) {
			// Queue an event to re-run the update check in $ttl seconds.
			wp_schedule_single_event( time() + $ttl, 'wp_version_check' );
		}
	}

	// Trigger background updates if running non-interactively, and we weren't called from the update handler.
	if ( $doing_cron && ! doing_action( 'wp_maybe_auto_update' ) ) {
		/**
		 * Fires during wp_cron, starting the auto-update process.
		 *
		 * @since 3.9.0
		 */
		do_action( 'wp_maybe_auto_update' );
	}
}

/**
 * Checks for available updates to plugins based on the latest versions hosted on WordPress.org.
 *
 * Despite its name this function does not actually perform any updates, it only checks for available updates.
 *
 * A list of all plugins installed is sent to WP, along with the site locale.
 *
 * Checks against the WordPress API server. Will only check if WordPress isn't installing.
 *
 * @since 2.3.0
 *
 * @global string $wp_version The WordPress version string.
 *
 * @param array $extra_stats Extra statistics to report to the WordPress.org API.
 */
function wp_update_plugins( $extra_stats = array() ) {
	if ( wp_installing() ) {
		return;
	}

	// If running blog-side, bail unless we've not checked in the last 12 hours.
	if ( ! function_exists( 'get_plugins' ) ) {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
	}

	$plugins      = get_plugins();
	$translations = wp_get_installed_translations( 'plugins' );

	$active  = get_option( 'active_plugins', array() );
	$current = get_site_transient( 'update_plugins' );

	if ( ! is_object( $current ) ) {
		$current = new stdClass();
	}

	$updates               = new stdClass();
	$updates->last_checked = time();
	$updates->response     = array();
	$updates->translations = array();
	$updates->no_update    = array();

	$doing_cron = wp_doing_cron();

	// Check for update on a different schedule, depending on the page.
	switch ( current_filter() ) {
		case 'upgrader_process_complete':
			$timeout = 0;
			break;
		case 'load-update-core.php':
			$timeout = MINUTE_IN_SECONDS;
			break;
		case 'load-plugins.php':
		case 'load-update.php':
			$timeout = HOUR_IN_SECONDS;
			break;
		default:
			if ( $doing_cron ) {
				$timeout = 2 * HOUR_IN_SECONDS;
			} else {
				$timeout = 12 * HOUR_IN_SECONDS;
			}
	}

	$time_not_changed = isset( $current->last_checked ) && $timeout > ( time() - $current->last_checked );

	if ( $time_not_changed && ! $extra_stats ) {
		$plugin_changed = false;

		foreach ( $plugins as $file => $p ) {
			$updates->checked[ $file ] = $p['Version'];

			if ( ! isset( $current->checked[ $file ] ) || (string) $current->checked[ $file ] !== (string) $p['Version'] ) {
				$plugin_changed = true;
			}
		}

		if ( isset( $current->response ) && is_array( $current->response ) ) {
			foreach ( $current->response as $plugin_file => $update_details ) {
				if ( ! isset( $plugins[ $plugin_file ] ) ) {
					$plugin_changed = true;
					break;
				}
			}
		}

		// Bail if we've checked recently and if nothing has changed.
		if ( ! $plugin_changed ) {
			return;
		}
	}

	// Update last_checked for current to prevent multiple blocking requests if request hangs.
	$current->last_checked = time();
	set_site_transient( 'update_plugins', $current );

	$to_send = compact( 'plugins', 'active' );

	$locales = array_values( get_available_languages() );

	/**
	 * Filters the locales requested for plugin translations.
	 *
	 * @since 3.7.0
	 * @since 4.5.0 The default value of the `$locales` parameter changed to include all locales.
	 *
	 * @param string[] $locales Plugin locales. Default is all available locales of the site.
	 */
	$locales = apply_filters( 'plugins_update_check_locales', $locales );
	$locales = array_unique( $locales );

	if ( $doing_cron ) {
		$timeout = 30; // 30 seconds.
	} else {
		// Three seconds, plus one extra second for every 10 plugins.
		$timeout = 3 + (int) ( count( $plugins ) / 10 );
	}

	$options = array(
		'timeout'    => $timeout,
		'body'       => array(
			'plugins'      => wp_json_encode( $to_send ),
			'translations' => wp_json_encode( $translations ),
			'locale'       => wp_json_encode( $locales ),
			'all'          => wp_json_encode( true ),
		),
		'user-agent' => 'WordPress/' . wp_get_wp_version() . '; ' . home_url( '/' ),
	);

	if ( $extra_stats ) {
		$options['body']['update_stats'] = wp_json_encode( $extra_stats );
	}

	$url      = wp_get_update_api_base( $https = false ). '/plugins/update-check/1.1/';
	$http_url = $url;
	$ssl      = wp_http_supports( array( 'ssl' ) );

	if ( $ssl ) {
		$url = set_url_scheme( $url, 'https' );
	}

	$raw_response = wp_remote_post( $url, $options );

	if ( $ssl && is_wp_error( $raw_response ) ) {
		wp_trigger_error(
			__FUNCTION__,
			sprintf(
				/* translators: %s: Support forums URL. */
				__( 'An unexpected error occurred. Something may be wrong with WordPress.org or this server&#8217;s configuration. If you continue to have problems, please try the <a href="%s">support forums</a>.' ),
				__( 'https://wordpress.org/support/forums/' )
			) . ' ' . __( '(WordPress could not establish a secure connection to WordPress.org. Please contact your server administrator.)' ),
			headers_sent() || WP_DEBUG ? E_USER_WARNING : E_USER_NOTICE
		);
		$raw_response = wp_remote_post( $http_url, $options );
	}

	if ( is_wp_error( $raw_response ) || 200 !== wp_remote_retrieve_response_code( $raw_response ) ) {
		return;
	}

	$response = json_decode( wp_remote_retrieve_body( $raw_response ), true );

	if ( $response && is_array( $response ) ) {
		$updates->response     = $response['plugins'];
		$updates->translations = $response['translations'];
		$updates->no_update    = $response['no_update'];
	}

	// Support updates for any plugins using the `Update URI` header field.
	foreach ( $plugins as $plugin_file => $plugin_data ) {
		if ( ! $plugin_data['UpdateURI'] || isset( $updates->response[ $plugin_file ] ) ) {
			continue;
		}

		$hostname = wp_parse_url( sanitize_url( $plugin_data['UpdateURI'] ), PHP_URL_HOST );

		/**
		 * Filters the update response for a given plugin hostname.
		 *
		 * The dynamic portion of the hook name, `$hostname`, refers to the hostname
		 * of the URI specified in the `Update URI` header field.
		 *
		 * @since 5.8.0
		 *
		 * @param array|false $update {
		 *     The plugin update data with the latest details. Default false.
		 *
		 *     @type string $id           Optional. ID of the plugin for update purposes, should be a URI
		 *                                specified in the `Update URI` header field.
		 *     @type string $slug         Slug of the plugin.
		 *     @type string $version      The version of the plugin.
		 *     @type string $url          The URL for details of the plugin.
		 *     @type string $package      Optional. The update ZIP for the plugin.
		 *     @type string $tested       Optional. The version of WordPress the plugin is tested against.
		 *     @type string $requires_php Optional. The version of PHP which the plugin requires.
		 *     @type bool   $autoupdate   Optional. Whether the plugin should automatically update.
		 *     @type array  $icons        Optional. Array of plugin icons.
		 *     @type array  $banners      Optional. Array of plugin banners.
		 *     @type array  $banners_rtl  Optional. Array of plugin RTL banners.
		 *     @type array  $translations {
		 *         Optional. List of translation updates for the plugin.
		 *
		 *         @type string $language   The language the translation update is for.
		 *         @type string $version    The version of the plugin this translation is for.
		 *                                  This is not the version of the language file.
		 *         @type string $updated    The update timestamp of the translation file.
		 *                                  Should be a date in the `YYYY-MM-DD HH:MM:SS` format.
		 *         @type string $package    The ZIP location containing the translation update.
		 *         @type string $autoupdate Whether the translation should be automatically installed.
		 *     }
		 * }
		 * @param array       $plugin_data      Plugin headers.
		 * @param string      $plugin_file      Plugin filename.
		 * @param string[]    $locales          Installed locales to look up translations for.
		 */
		$update = apply_filters( "update_plugins_{$hostname}", false, $plugin_data, $plugin_file, $locales );

		if ( ! $update ) {
			continue;
		}

		$update = (object) $update;

		// Is it valid? We require at least a version.
		if ( ! isset( $update->version ) ) {
			continue;
		}

		// These should remain constant.
		$update->id     = $plugin_data['UpdateURI'];
		$update->plugin = $plugin_file;

		// WordPress needs the version field specified as 'new_version'.
		if ( ! isset( $update->new_version ) ) {
			$update->new_version = $update->version;
		}

		// Handle any translation updates.
		if ( ! empty( $update->translations ) ) {
			foreach ( $update->translations as $translation ) {
				if ( isset( $translation['language'], $translation['package'] ) ) {
					$translation['type'] = 'plugin';
					$translation['slug'] = isset( $update->slug ) ? $update->slug : $update->id;

					$updates->translations[] = $translation;
				}
			}
		}

		unset( $updates->no_update[ $plugin_file ], $updates->response[ $plugin_file ] );

		if ( version_compare( $update->new_version, $plugin_data['Version'], '>' ) ) {
			$updates->response[ $plugin_file ] = $update;
		} else {
			$updates->no_update[ $plugin_file ] = $update;
		}
	}

	$sanitize_plugin_update_payload = static function ( &$item ) {
		$item = (object) $item;

		unset( $item->translations, $item->compatibility );

		return $item;
	};

	array_walk( $updates->response, $sanitize_plugin_update_payload );
	array_walk( $updates->no_update, $sanitize_plugin_update_payload );

	set_site_transient( 'update_plugins', $updates );
}

/**
 * Checks for available updates to themes based on the latest versions hosted on WordPress.org.
 *
 * Despite its name this function does not actually perform any updates, it only checks for available updates.
 *
 * A list of all themes installed is sent to WP, along with the site locale.
 *
 * Checks against the WordPress API. Will only check if WordPress isn't installing.
 *
 * @since 2.7.0
 *
 * @global string $wp_version The WordPress version string.
 *
 * @param array $extra_stats Extra statistics to report to the WordPress.org API.
 */
function wp_update_themes( $extra_stats = array() ) {
	if ( wp_installing() ) {
		return;
	}

	$installed_themes = wp_get_themes();
	$translations     = wp_get_installed_translations( 'themes' );

	$last_update = get_site_transient( 'update_themes' );

	if ( ! is_object( $last_update ) ) {
		$last_update = new stdClass();
	}

	$themes  = array();
	$checked = array();
	$request = array();

	// Put slug of active theme into request.
	$request['active'] = get_option( 'stylesheet' );

	foreach ( $installed_themes as $theme ) {
		$checked[ $theme->get_stylesheet() ] = $theme->get( 'Version' );

		$themes[ $theme->get_stylesheet() ] = array(
			'Name'       => $theme->get( 'Name' ),
			'Title'      => $theme->get( 'Name' ),
			'Version'    => $theme->get( 'Version' ),
			'Author'     => $theme->get( 'Author' ),
			'Author URI' => $theme->get( 'AuthorURI' ),
			'UpdateURI'  => $theme->get( 'UpdateURI' ),
			'Template'   => $theme->get_template(),
			'Stylesheet' => $theme->get_stylesheet(),
		);
	}

	$doing_cron = wp_doing_cron();

	// Check for update on a different schedule, depending on the page.
	switch ( current_filter() ) {
		case 'upgrader_process_complete':
			$timeout = 0;
			break;
		case 'load-update-core.php':
			$timeout = MINUTE_IN_SECONDS;
			break;
		case 'load-themes.php':
		case 'load-update.php':
			$timeout = HOUR_IN_SECONDS;
			break;
		default:
			if ( $doing_cron ) {
				$timeout = 2 * HOUR_IN_SECONDS;
			} else {
				$timeout = 12 * HOUR_IN_SECONDS;
			}
	}

	$time_not_changed = isset( $last_update->last_checked ) && $timeout > ( time() - $last_update->last_checked );

	if ( $time_not_changed && ! $extra_stats ) {
		$theme_changed = false;

		foreach ( $checked as $slug => $v ) {
			if ( ! isset( $last_update->checked[ $slug ] ) || (string) $last_update->checked[ $slug ] !== (string) $v ) {
				$theme_changed = true;
			}
		}

		if ( isset( $last_update->response ) && is_array( $last_update->response ) ) {
			foreach ( $last_update->response as $slug => $update_details ) {
				if ( ! isset( $checked[ $slug ] ) ) {
					$theme_changed = true;
					break;
				}
			}
		}

		// Bail if we've checked recently and if nothing has changed.
		if ( ! $theme_changed ) {
			return;
		}
	}

	// Update last_checked for current to prevent multiple blocking requests if request hangs.
	$last_update->last_checked = time();
	set_site_transient( 'update_themes', $last_update );

	$request['themes'] = $themes;

	$locales = array_values( get_available_languages() );

	/**
	 * Filters the locales requested for theme translations.
	 *
	 * @since 3.7.0
	 * @since 4.5.0 The default value of the `$locales` parameter changed to include all locales.
	 *
	 * @param string[] $locales Theme locales. Default is all available locales of the site.
	 */
	$locales = apply_filters( 'themes_update_check_locales', $locales );
	$locales = array_unique( $locales );

	if ( $doing_cron ) {
		$timeout = 30; // 30 seconds.
	} else {
		// Three seconds, plus one extra second for every 10 themes.
		$timeout = 3 + (int) ( count( $themes ) / 10 );
	}

	$options = array(
		'timeout'    => $timeout,
		'body'       => array(
			'themes'       => wp_json_encode( $request ),
			'translations' => wp_json_encode( $translations ),
			'locale'       => wp_json_encode( $locales ),
		),
		'user-agent' => 'WordPress/' . wp_get_wp_version() . '; ' . home_url( '/' ),
	);

	if ( $extra_stats ) {
		$options['body']['update_stats'] = wp_json_encode( $extra_stats );
	}

	$url      = wp_get_update_api_base( $https = false ) . '/themes/update-check/1.1/';
	$http_url = $url;
	$ssl      = wp_http_supports( array( 'ssl' ) );

	if ( $ssl ) {
		$url = set_url_scheme( $url, 'https' );
	}

	$raw_response = wp_remote_post( $url, $options );

	if ( $ssl && is_wp_error( $raw_response ) ) {
		wp_trigger_error(
			__FUNCTION__,
			sprintf(
				/* translators: %s: Support forums URL. */
				__( 'An unexpected error occurred. Something may be wrong with WordPress.org or this server&#8217;s configuration. If you continue to have problems, please try the <a href="%s">support forums</a>.' ),
				__( 'https://wordpress.org/support/forums/' )
			) . ' ' . __( '(WordPress could not establish a secure connection to WordPress.org. Please contact your server administrator.)' ),
			headers_sent() || WP_DEBUG ? E_USER_WARNING : E_USER_NOTICE
		);
		$raw_response = wp_remote_post( $http_url, $options );
	}

	if ( is_wp_error( $raw_response ) || 200 !== wp_remote_retrieve_response_code( $raw_response ) ) {
		return;
	}

	$new_update               = new stdClass();
	$new_update->last_checked = time();
	$new_update->checked      = $checked;

	$response = json_decode( wp_remote_retrieve_body( $raw_response ), true );

	if ( is_array( $response ) ) {
		$new_update->response     = $response['themes'];
		$new_update->no_update    = $response['no_update'];
		$new_update->translations = $response['translations'];
	}

	// Support updates for any themes using the `Update URI` header field.
	foreach ( $themes as $theme_stylesheet => $theme_data ) {
		if ( ! $theme_data['UpdateURI'] || isset( $new_update->response[ $theme_stylesheet ] ) ) {
			continue;
		}

		$hostname = wp_parse_url( sanitize_url( $theme_data['UpdateURI'] ), PHP_URL_HOST );

		/**
		 * Filters the update response for a given theme hostname.
		 *
		 * The dynamic portion of the hook name, `$hostname`, refers to the hostname
		 * of the URI specified in the `Update URI` header field.
		 *
		 * @since 6.1.0
		 *
		 * @param array|false $update {
		 *     The theme update data with the latest details. Default false.
		 *
		 *     @type string $id           Optional. ID of the theme for update purposes, should be a URI
		 *                                specified in the `Update URI` header field.
		 *     @type string $theme        Directory name of the theme.
		 *     @type string $version      The version of the theme.
		 *     @type string $url          The URL for details of the theme.
		 *     @type string $package      Optional. The update ZIP for the theme.
		 *     @type string $tested       Optional. The version of WordPress the theme is tested against.
		 *     @type string $requires_php Optional. The version of PHP which the theme requires.
		 *     @type bool   $autoupdate   Optional. Whether the theme should automatically update.
		 *     @type array  $translations {
		 *         Optional. List of translation updates for the theme.
		 *
		 *         @type string $language   The language the translation update is for.
		 *         @type string $version    The version of the theme this translation is for.
		 *                                  This is not the version of the language file.
		 *         @type string $updated    The update timestamp of the translation file.
		 *                                  Should be a date in the `YYYY-MM-DD HH:MM:SS` format.
		 *         @type string $package    The ZIP location containing the translation update.
		 *         @type string $autoupdate Whether the translation should be automatically installed.
		 *     }
		 * }
		 * @param array       $theme_data       Theme headers.
		 * @param string      $theme_stylesheet Theme stylesheet.
		 * @param string[]    $locales          Installed locales to look up translations for.
		 */
		$update = apply_filters( "update_themes_{$hostname}", false, $theme_data, $theme_stylesheet, $locales );

		if ( ! $update ) {
			continue;
		}

		$update = (object) $update;

		// Is it valid? We require at least a version.
		if ( ! isset( $update->version ) ) {
			continue;
		}

		// This should remain constant.
		$update->id = $theme_data['UpdateURI'];

		// WordPress needs the version field specified as 'new_version'.
		if ( ! isset( $update->new_version ) ) {
			$update->new_version = $update->version;
		}

		// Handle any translation updates.
		if ( ! empty( $update->translations ) ) {
			foreach ( $update->translations as $translation ) {
				if ( isset( $translation['language'], $translation['package'] ) ) {
					$translation['type'] = 'theme';
					$translation['slug'] = isset( $update->theme ) ? $update->theme : $update->id;

					$new_update->translations[] = $translation;
				}
			}
		}

		unset( $new_update->no_update[ $theme_stylesheet ], $new_update->response[ $theme_stylesheet ] );

		if ( version_compare( $update->new_version, $theme_data['Version'], '>' ) ) {
			$new_update->response[ $theme_stylesheet ] = (array) $update;
		} else {
			$new_update->no_update[ $theme_stylesheet ] = (array) $update;
		}
	}

	set_site_transient( 'update_themes', $new_update );
}

/**
 * Performs WordPress automatic background updates.
 *
 * Updates WordPress core plus any plugins and themes that have automatic updates enabled.
 *
 * @since 3.7.0
 */
function wp_maybe_auto_update() {
	require_once ABSPATH . 'wp-admin/includes/admin.php';
	require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';

	$upgrader = new WP_Automatic_Updater();
	$upgrader->run();
}

/**
 * Retrieves a list of all language updates available.
 *
 * @since 3.7.0
 *
 * @return object[] Array of translation objects that have available updates.
 */
function wp_get_translation_updates() {
	$updates    = array();
	$transients = array(
		'update_core'    => 'core',
		'update_plugins' => 'plugin',
		'update_themes'  => 'theme',
	);

	foreach ( $transients as $transient => $type ) {
		$transient = get_site_transient( $transient );

		if ( empty( $transient->translations ) ) {
			continue;
		}

		foreach ( $transient->translations as $translation ) {
			$updates[] = (object) $translation;
		}
	}

	return $updates;
}

/**
 * Collects counts and UI strings for available updates.
 *
 * @since 3.3.0
 *
 * @return array
 */
function wp_get_update_data() {
	$counts = array(
		'plugins'      => 0,
		'themes'       => 0,
		'wordpress'    => 0,
		'translations' => 0,
	);

	$plugins = current_user_can( 'update_plugins' );

	if ( $plugins ) {
		$update_plugins = get_site_transient( 'update_plugins' );

		if ( ! empty( $update_plugins->response ) ) {
			$counts['plugins'] = count( $update_plugins->response );
		}
	}

	$themes = current_user_can( 'update_themes' );

	if ( $themes ) {
		$update_themes = get_site_transient( 'update_themes' );

		if ( ! empty( $update_themes->response ) ) {
			$counts['themes'] = count( $update_themes->response );
		}
	}

	$core = current_user_can( 'update_core' );

	if ( $core && function_exists( 'get_core_updates' ) ) {
		$update_wordpress = get_core_updates( array( 'dismissed' => false ) );

		if ( ! empty( $update_wordpress )
			&& ! in_array( $update_wordpress[0]->response, array( 'development', 'latest' ), true )
			&& current_user_can( 'update_core' )
		) {
			$counts['wordpress'] = 1;
		}
	}

	if ( ( $core || $plugins || $themes ) && wp_get_translation_updates() ) {
		$counts['translations'] = 1;
	}

	$counts['total'] = $counts['plugins'] + $counts['themes'] + $counts['wordpress'] + $counts['translations'];
	$titles          = array();

	if ( $counts['wordpress'] ) {
		/* translators: %d: Number of available WordPress updates. */
		$titles['wordpress'] = sprintf( __( '%d WordPress Update' ), $counts['wordpress'] );
	}

	if ( $counts['plugins'] ) {
		/* translators: %d: Number of available plugin updates. */
		$titles['plugins'] = sprintf( _n( '%d Plugin Update', '%d Plugin Updates', $counts['plugins'] ), $counts['plugins'] );
	}

	if ( $counts['themes'] ) {
		/* translators: %d: Number of available theme updates. */
		$titles['themes'] = sprintf( _n( '%d Theme Update', '%d Theme Updates', $counts['themes'] ), $counts['themes'] );
	}

	if ( $counts['translations'] ) {
		$titles['translations'] = __( 'Translation Updates' );
	}

	$update_title = $titles ? esc_attr( implode( ', ', $titles ) ) : '';

	$update_data = array(
		'counts' => $counts,
		'title'  => $update_title,
	);
	/**
	 * Filters the returned array of update data for plugins, themes, and WordPress core.
	 *
	 * @since 3.5.0
	 *
	 * @param array $update_data {
	 *     Fetched update data.
	 *
	 *     @type array   $counts       An array of counts for available plugin, theme, and WordPress updates.
	 *     @type string  $update_title Titles of available updates.
	 * }
	 * @param array $titles An array of update counts and UI strings for available updates.
	 */
	return apply_filters( 'wp_get_update_data', $update_data, $titles );
}

/**
 * Determines whether core should be updated.
 *
 * @since 2.8.0
 *
 * @global string $wp_version The WordPress version string.
 */
function _maybe_update_core() {
	$current = get_site_transient( 'update_core' );

	if ( isset( $current->last_checked, $current->version_checked )
		&& 12 * HOUR_IN_SECONDS > ( time() - $current->last_checked )
		&& wp_get_wp_version() === $current->version_checked
	) {
		return;
	}

	wp_version_check();
}
/**
 * Checks the last time plugins were run before checking plugin versions.
 *
 * This might have been backported to WordPress 2.6.1 for performance reasons.
 * This is used for the wp-admin to check only so often instead of every page
 * load.
 *
 * @since 2.7.0
 * @access private
 */
function _maybe_update_plugins() {
	$current = get_site_transient( 'update_plugins' );

	if ( isset( $current->last_checked )
		&& 12 * HOUR_IN_SECONDS > ( time() - $current->last_checked )
	) {
		return;
	}

	wp_update_plugins();
}

/**
 * Checks themes versions only after a duration of time.
 *
 * This is for performance reasons to make sure that on the theme version
 * checker is not run on every page load.
 *
 * @since 2.7.0
 * @access private
 */
function _maybe_update_themes() {
	$current = get_site_transient( 'update_themes' );

	if ( isset( $current->last_checked )
		&& 12 * HOUR_IN_SECONDS > ( time() - $current->last_checked )
	) {
		return;
	}

	wp_update_themes();
}

/**
 * Schedules core, theme, and plugin update checks.
 *
 * @since 3.1.0
 */
function wp_schedule_update_checks() {
	if ( ! wp_next_scheduled( 'wp_version_check' ) && ! wp_installing() ) {
		wp_schedule_event( time(), 'twicedaily', 'wp_version_check' );
	}

	if ( ! wp_next_scheduled( 'wp_update_plugins' ) && ! wp_installing() ) {
		wp_schedule_event( time(), 'twicedaily', 'wp_update_plugins' );
	}

	if ( ! wp_next_scheduled( 'wp_update_themes' ) && ! wp_installing() ) {
		wp_schedule_event( time(), 'twicedaily', 'wp_update_themes' );
	}
}

/**
 * Clears existing update caches for plugins, themes, and core.
 *
 * @since 4.1.0
 */
function wp_clean_update_cache() {
	if ( function_exists( 'wp_clean_plugins_cache' ) ) {
		wp_clean_plugins_cache();
	} else {
		delete_site_transient( 'update_plugins' );
	}

	wp_clean_themes_cache();

	delete_site_transient( 'update_core' );
}

/**
 * Schedules the removal of all contents in the temporary backup directory.
 *
 * @since 6.3.0
 */
function wp_delete_all_temp_backups() {
	/*
	 * Check if there is a lock, or if currently performing an Ajax request,
	 * in which case there is a chance an update is running.
	 * Reschedule for an hour from now and exit early.
	 */
	if ( get_option( 'core_updater.lock' ) || get_option( 'auto_updater.lock' ) || wp_doing_ajax() ) {
		wp_schedule_single_event( time() + HOUR_IN_SECONDS, 'wp_delete_temp_updater_backups' );
		return;
	}

	// This action runs on shutdown to make sure there are no plugin updates currently running.
	add_action( 'shutdown', '_wp_delete_all_temp_backups' );
}

/**
 * Deletes all contents in the temporary backup directory.
 *
 * @since 6.3.0
 *
 * @access private
 *
 * @global WP_Filesystem_Base $wp_filesystem WordPress filesystem subclass.
 */
function _wp_delete_all_temp_backups() {
	global $wp_filesystem;

	if ( ! function_exists( 'WP_Filesystem' ) ) {
		require_once ABSPATH . '/wp-admin/includes/file.php';
	}

	ob_start();
	$credentials = request_filesystem_credentials( '' );
	ob_end_clean();

	if ( false === $credentials || ! WP_Filesystem( $credentials ) ) {
		wp_trigger_error( __FUNCTION__, __( 'Could not access filesystem.' ) );
		return;
	}

	if ( ! $wp_filesystem->wp_content_dir() ) {
		wp_trigger_error(
			__FUNCTION__,
			/* translators: %s: Directory name. */
			sprintf( __( 'Unable to locate WordPress content directory (%s).' ), 'wp-content' )
		);
		return;
	}

	$temp_backup_dir = $wp_filesystem->wp_content_dir() . 'upgrade-temp-backup/';
	$dirlist         = $wp_filesystem->dirlist( $temp_backup_dir );
	$dirlist         = $dirlist ? $dirlist : array();

	foreach ( array_keys( $dirlist ) as $dir ) {
		if ( '.' === $dir || '..' === $dir ) {
			continue;
		}

		$wp_filesystem->delete( $temp_backup_dir . $dir, true );
	}
}

if ( ( ! is_main_site() && ! is_network_admin() ) || wp_doing_ajax() ) {
	return;
}

add_action( 'admin_init', '_maybe_update_core' );
add_action( 'wp_version_check', 'wp_version_check' );

add_action( 'load-plugins.php', 'wp_update_plugins' );
add_action( 'load-update.php', 'wp_update_plugins' );
add_action( 'load-update-core.php', 'wp_update_plugins' );
add_action( 'admin_init', '_maybe_update_plugins' );
add_action( 'wp_update_plugins', 'wp_update_plugins' );

add_action( 'load-themes.php', 'wp_update_themes' );
add_action( 'load-update.php', 'wp_update_themes' );
add_action( 'load-update-core.php', 'wp_update_themes' );
add_action( 'admin_init', '_maybe_update_themes' );
add_action( 'wp_update_themes', 'wp_update_themes' );

add_action( 'update_option_WPLANG', 'wp_clean_update_cache', 10, 0 );

add_action( 'wp_maybe_auto_update', 'wp_maybe_auto_update' );

add_action( 'init', 'wp_schedule_update_checks' );

add_action( 'wp_delete_temp_updater_backups', 'wp_delete_all_temp_backups' );
