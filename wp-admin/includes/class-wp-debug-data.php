<?php
/**
 * Class for providing debug data based on a users WordPress environment.
 *
 * @package WordPress
 * @subpackage Site_Health
 * @since 5.2.0
 */

class WP_Debug_Data {
	/**
	 * Calls all core functions to check for updates.
	 *
	 * @since 5.2.0
	 */
	static function check_for_updates() {
		wp_version_check();
		wp_update_plugins();
		wp_update_themes();
	}

	/**
	 * Static function for generating site debug data when required.
	 *
	 * @since 5.2.0
	 *
	 * @throws ImagickException
	 * @global wpdb $wpdb WordPress database abstraction object.
	 *
	 * @param string $locale Optional. An ISO formatted language code to provide debug translations in. Default null.
	 * @return array The debug data for the site.
	 */
	static function debug_data( $locale = null ) {
		global $wpdb;

		if ( ! empty( $locale ) ) {
			// Change the language used for translations
			if ( function_exists( 'switch_to_locale' ) ) {
				$original_locale = get_locale();
				$switched_locale = switch_to_locale( $locale );
			}
		}

		$upload_dir = wp_upload_dir();
		if ( file_exists( ABSPATH . 'wp-config.php' ) ) {
			$wp_config_path = ABSPATH . 'wp-config.php';
			// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
		} elseif ( @file_exists( dirname( ABSPATH ) . '/wp-config.php' ) && ! @file_exists( dirname( ABSPATH ) . '/wp-settings.php' ) ) {
			$wp_config_path = dirname( ABSPATH ) . '/wp-config.php';
		}

		$core_current_version = get_bloginfo( 'version' );
		$core_updates         = get_core_updates();
		$core_update_needed   = '';

		foreach ( $core_updates as $core => $update ) {
			if ( 'upgrade' === $update->response ) {
				// translators: %s: Latest WordPress version number.
				$core_update_needed = ' ' . sprintf( __( '( Latest version: %s )' ), $update->version );
			} else {
				$core_update_needed = '';
			}
		}

		// Set up the array that holds all debug information.
		$info = array(
			'wp-core'             => array(
				'label'  => __( 'WordPress' ),
				'fields' => array(
					'version'                => array(
						'label' => __( 'Version' ),
						'value' => $core_current_version . $core_update_needed,
					),
					'language'               => array(
						'label' => __( 'Language' ),
						'value' => ( ! empty( $locale ) ? $original_locale : get_locale() ),
					),
					'home_url'               => array(
						'label'   => __( 'Home URL' ),
						'value'   => get_bloginfo( 'url' ),
						'private' => true,
					),
					'site_url'               => array(
						'label'   => __( 'Site URL' ),
						'value'   => get_bloginfo( 'wpurl' ),
						'private' => true,
					),
					'permalink'              => array(
						'label' => __( 'Permalink structure' ),
						'value' => get_option( 'permalink_structure' ) ?: __( 'No permalink structure set' ),
					),
					'https_status'           => array(
						'label' => __( 'Is this site using HTTPS?' ),
						'value' => ( is_ssl() ? __( 'Yes' ) : __( 'No' ) ),
					),
					'user_registration'      => array(
						'label' => __( 'Can anyone register on this site?' ),
						'value' => ( get_option( 'users_can_register' ) ? __( 'Yes' ) : __( 'No' ) ),
					),
					'default_comment_status' => array(
						'label' => __( 'Default comment status' ),
						'value' => get_option( 'default_comment_status' ),
					),
					'multisite'              => array(
						'label' => __( 'Is this a multisite?' ),
						'value' => ( is_multisite() ? __( 'Yes' ) : __( 'No' ) ),
					),
				),
			),
			'wp-paths-sizes'      => array(
				'label'  => __( 'Directories and Sizes' ),
				'fields' => array(),
			),
			'wp-dropins'          => array(
				'label'       => __( 'Drop-ins' ),
				'show_count'  => true,
				'description' => __( 'Drop-ins are single files that replace or enhance WordPress features in ways that are not possible for traditional plugins.' ),
				'fields'      => array(),
			),
			'wp-active-theme'     => array(
				'label'  => __( 'Active Theme' ),
				'fields' => array(),
			),
			'wp-themes'           => array(
				'label'      => __( 'Other Themes' ),
				'show_count' => true,
				'fields'     => array(),
			),
			'wp-mu-plugins'       => array(
				'label'      => __( 'Must Use Plugins' ),
				'show_count' => true,
				'fields'     => array(),
			),
			'wp-plugins-active'   => array(
				'label'      => __( 'Active Plugins' ),
				'show_count' => true,
				'fields'     => array(),
			),
			'wp-plugins-inactive' => array(
				'label'      => __( 'Inactive Plugins' ),
				'show_count' => true,
				'fields'     => array(),
			),
			'wp-media'            => array(
				'label'  => __( 'Media Handling' ),
				'fields' => array(),
			),
			'wp-server'           => array(
				'label'       => __( 'Server' ),
				'description' => __( 'The options shown below relate to your server setup. If changes are required, you may need your web host&#8217;s assistance.' ),
				'fields'      => array(),
			),
			'wp-database'         => array(
				'label'  => __( 'Database' ),
				'fields' => array(),
			),
			'wp-constants'        => array(
				'label'       => __( 'WordPress Constants' ),
				'description' => __( 'These settings are defined in your wp-config.php file, and alter where and how parts of WordPress are loaded.' ),
				'fields'      => array(
					'ABSPATH'             => array(
						'label'   => 'ABSPATH',
						'value'   => ( ! defined( 'ABSPATH' ) ? __( 'Undefined' ) : ABSPATH ),
						'private' => true,
					),
					'WP_HOME'             => array(
						'label' => 'WP_HOME',
						'value' => ( ! defined( 'WP_HOME' ) ? __( 'Undefined' ) : WP_HOME ),
					),
					'WP_SITEURL'          => array(
						'label' => 'WP_SITEURL',
						'value' => ( ! defined( 'WP_SITEURL' ) ? __( 'Undefined' ) : WP_SITEURL ),
					),
					'WP_CONTENT_DIR'      => array(
						'label' => 'WP_CONTENT_DIR',
						'value' => ( ! defined( 'WP_CONTENT_DIR' ) ? __( 'Undefined' ) : WP_CONTENT_DIR ),
					),
					'WP_PLUGIN_DIR'       => array(
						'label' => 'WP_PLUGIN_DIR',
						'value' => ( ! defined( 'WP_PLUGIN_DIR' ) ? __( 'Undefined' ) : WP_PLUGIN_DIR ),
					),
					'WP_DEBUG'            => array(
						'label' => 'WP_DEBUG',
						'value' => ( ! defined( 'WP_DEBUG' ) ? __( 'Undefined' ) : ( WP_DEBUG ? __( 'Enabled' ) : __( 'Disabled' ) ) ),
					),
					'WP_MAX_MEMORY_LIMIT' => array(
						'label' => 'WP_MAX_MEMORY_LIMIT',
						'value' => ( ! defined( 'WP_MAX_MEMORY_LIMIT' ) ? __( 'Undefined' ) : WP_MAX_MEMORY_LIMIT ),
					),
					'WP_DEBUG_DISPLAY'    => array(
						'label' => 'WP_DEBUG_DISPLAY',
						'value' => ( ! defined( 'WP_DEBUG_DISPLAY' ) ? __( 'Undefined' ) : ( WP_DEBUG_DISPLAY ? __( 'Enabled' ) : __( 'Disabled' ) ) ),
					),
					'WP_DEBUG_LOG'        => array(
						'label' => 'WP_DEBUG_LOG',
						'value' => ( ! defined( 'WP_DEBUG_LOG' ) ? __( 'Undefined' ) : ( WP_DEBUG_LOG ? __( 'Enabled' ) : __( 'Disabled' ) ) ),
					),
					'SCRIPT_DEBUG'        => array(
						'label' => 'SCRIPT_DEBUG',
						'value' => ( ! defined( 'SCRIPT_DEBUG' ) ? __( 'Undefined' ) : ( SCRIPT_DEBUG ? __( 'Enabled' ) : __( 'Disabled' ) ) ),
					),
					'WP_CACHE'            => array(
						'label' => 'WP_CACHE',
						'value' => ( ! defined( 'WP_CACHE' ) ? __( 'Undefined' ) : ( WP_CACHE ? __( 'Enabled' ) : __( 'Disabled' ) ) ),
					),
					'CONCATENATE_SCRIPTS' => array(
						'label' => 'CONCATENATE_SCRIPTS',
						'value' => ( ! defined( 'CONCATENATE_SCRIPTS' ) ? __( 'Undefined' ) : ( CONCATENATE_SCRIPTS ? __( 'Enabled' ) : __( 'Disabled' ) ) ),
					),
					'COMPRESS_SCRIPTS'    => array(
						'label' => 'COMPRESS_SCRIPTS',
						'value' => ( ! defined( 'COMPRESS_SCRIPTS' ) ? __( 'Undefined' ) : ( COMPRESS_SCRIPTS ? __( 'Enabled' ) : __( 'Disabled' ) ) ),
					),
					'COMPRESS_CSS'        => array(
						'label' => 'COMPRESS_CSS',
						'value' => ( ! defined( 'COMPRESS_CSS' ) ? __( 'Undefined' ) : ( COMPRESS_CSS ? __( 'Enabled' ) : __( 'Disabled' ) ) ),
					),
					'WP_LOCAL_DEV'        => array(
						'label' => 'WP_LOCAL_DEV',
						'value' => ( ! defined( 'WP_LOCAL_DEV' ) ? __( 'Undefined' ) : ( WP_LOCAL_DEV ? __( 'Enabled' ) : __( 'Disabled' ) ) ),
					),
				),
			),
			'wp-filesystem'       => array(
				'label'       => __( 'Filesystem Permissions' ),
				'description' => __( 'Shows whether WordPress is able to write to the directories it needs access to.' ),
				'fields'      => array(
					'all'        => array(
						'label' => __( 'The main WordPress directory' ),
						'value' => ( wp_is_writable( ABSPATH ) ? __( 'Writable' ) : __( 'Not writable' ) ),
					),
					'wp-content' => array(
						'label' => __( 'The wp-content directory' ),
						'value' => ( wp_is_writable( WP_CONTENT_DIR ) ? __( 'Writable' ) : __( 'Not writable' ) ),
					),
					'uploads'    => array(
						'label' => __( 'The uploads directory' ),
						'value' => ( wp_is_writable( $upload_dir['basedir'] ) ? __( 'Writable' ) : __( 'Not writable' ) ),
					),
					'plugins'    => array(
						'label' => __( 'The plugins directory' ),
						'value' => ( wp_is_writable( WP_PLUGIN_DIR ) ? __( 'Writable' ) : __( 'Not writable' ) ),
					),
					'themes'     => array(
						'label' => __( 'The themes directory' ),
						'value' => ( wp_is_writable( get_template_directory() . '/..' ) ? __( 'Writable' ) : __( 'Not writable' ) ),
					),
				),
			),
		);

		// Conditionally add debug information for multisite setups.
		if ( is_multisite() ) {
			$network_query = new WP_Network_Query();
			$network_ids   = $network_query->query(
				array(
					'fields'        => 'ids',
					'number'        => 100,
					'no_found_rows' => false,
				)
			);

			$site_count = 0;
			foreach ( $network_ids as $network_id ) {
				$site_count += get_blog_count( $network_id );
			}

			$info['wp-core']['fields']['user_count']    = array(
				'label' => __( 'User count' ),
				'value' => get_user_count(),
			);
			$info['wp-core']['fields']['site_count']    = array(
				'label' => __( 'Site count' ),
				'value' => $site_count,
			);
			$info['wp-core']['fields']['network_count'] = array(
				'label' => __( 'Network count' ),
				'value' => $network_query->found_networks,
			);
		} else {
			$user_count = count_users();

			$info['wp-core']['fields']['user_count'] = array(
				'label' => __( 'User count' ),
				'value' => $user_count['total_users'],
			);
		}

		// WordPress features requiring processing.
		$wp_dotorg = wp_remote_get(
			'https://wordpress.org',
			array(
				'timeout' => 10,
			)
		);
		if ( ! is_wp_error( $wp_dotorg ) ) {
			$info['wp-core']['fields']['dotorg_communication'] = array(
				'label' => __( 'Communication with WordPress.org' ),
				'value' => sprintf(
					__( 'WordPress.org is reachable' )
				),
			);
		} else {
			$info['wp-core']['fields']['dotorg_communication'] = array(
				'label' => __( 'Communication with WordPress.org' ),
				'value' => sprintf(
					// translators: %1$s: The IP address WordPress.org resolves to. %2$s: The error returned by the lookup.
					__( 'Unable to reach WordPress.org at %1$s: %2$s' ),
					gethostbyname( 'wordpress.org' ),
					$wp_dotorg->get_error_message()
				),
			);
		}

		// Go through the various installation directories and calculate their sizes.
		$uploads_dir = wp_upload_dir();
		$inaccurate  = false;

		/*
		 * We will be using the PHP max execution time to prevent the size calculations
		 * from causing a timeout. We provide a default value of 30 seconds, as some
		 * hosts do not allow you to read configuration values.
		 */
		$max_execution_time   = 30;
		$start_execution_time = microtime( true );
		if ( function_exists( 'ini_get' ) ) {
			$max_execution_time = ini_get( 'max_execution_time' );
		}

		$size_directories = array(
			'wordpress' => array(
				'path' => ABSPATH,
				'size' => 0,
			),
			'themes'    => array(
				'path' => trailingslashit( get_theme_root() ),
				'size' => 0,
			),
			'plugins'   => array(
				'path' => trailingslashit( WP_PLUGIN_DIR ),
				'size' => 0,
			),
			'uploads'   => array(
				'path' => $uploads_dir['basedir'],
				'size' => 0,
			),
		);

		// Loop over all the directories we want to gather the sizes for.
		foreach ( $size_directories as $size => $attributes ) {
			/*
			 * We run a helper function with a RecursiveIterator, which
			 * may throw an exception if it can't access directories.
			 *
			 * If a failure is detected we mark the result as inaccurate.
			 */
			try {
				$calculated_size = WP_Debug_data::get_directory_size( $attributes['path'], $max_execution_time, $start_execution_time );

				$size_directories[ $size ]['size'] = $calculated_size;

				/*
				 * If the size returned is -1, this means execution has
				 * exceeded the maximum execution time, also denoting an
				 * inaccurate value in the end.
				 */
				if ( -1 === $calculated_size ) {
					$inaccurate = true;
				}
			} catch ( Exception $e ) {
				$inaccurate = true;
			}
		}

		$size_db = WP_Debug_Data::get_database_size();

		$size_total = $size_directories['wordpress']['size'] + $size_db;

		$info['wp-paths-sizes']['fields'] = array(
			array(
				'label' => __( 'Uploads Directory Location' ),
				'value' => $size_directories['uploads']['path'],
			),
			array(
				'label' => __( 'Uploads Directory Size' ),
				'value' => ( -1 === $size_directories['uploads']['size'] ? __( 'Unable to determine the size of this directory' ) : size_format( $size_directories['uploads']['size'], 2 ) ),
			),
			array(
				'label' => __( 'Themes Directory Location' ),
				'value' => $size_directories['themes']['path'],
			),
			array(
				'label' => __( 'Current Theme Directory' ),
				'value' => get_template_directory(),
			),
			array(
				'label' => __( 'Themes Directory Size' ),
				'value' => ( -1 === $size_directories['themes']['size'] ? __( 'Unable to determine the size of this directory' ) : size_format( $size_directories['themes']['size'], 2 ) ),
			),
			array(
				'label' => __( 'Plugins Directory Location' ),
				'value' => $size_directories['plugins']['path'],
			),
			array(
				'label' => __( 'Plugins Directory Size' ),
				'value' => ( -1 === $size_directories['plugins']['size'] ? __( 'Unable to determine the size of this directory' ) : size_format( $size_directories['plugins']['size'], 2 ) ),
			),
			array(
				'label' => __( 'WordPress Directory Location' ),
				'value' => $size_directories['wordpress']['path'],
			),
			array(
				'label' => __( 'WordPress Directory Size' ),
				'value' => size_format( $size_directories['wordpress']['size'], 2 ),
			),
			array(
				'label' => __( 'Database size' ),
				'value' => size_format( $size_db, 2 ),
			),
			array(
				'label' => __( 'Total installation size' ),
				'value' => sprintf(
					'%s%s',
					size_format( $size_total, 2 ),
					( false === $inaccurate ? '' : __( '- Some errors, likely caused by invalid permissions, were encountered when determining the size of your installation. This means the values represented may be inaccurate.' ) )
				),
			),
		);

		// Get a list of all drop-in replacements.
		$dropins            = get_dropins();
		$dropin_description = _get_dropins();
		foreach ( $dropins as $dropin_key => $dropin ) {
			$info['wp-dropins']['fields'][ sanitize_key( $dropin_key ) ] = array(
				'label' => $dropin_key,
				'value' => $dropin_description[ $dropin_key ][0],
			);
		}

		// Populate the media fields.
		$info['wp-media']['fields']['image_editor'] = array(
			'label' => __( 'Active editor' ),
			'value' => _wp_image_editor_choose(),
		);

		// Get ImageMagic information, if available.
		if ( class_exists( 'Imagick' ) ) {
			// Save the Imagick instance for later use.
			$imagick         = new Imagick();
			$imagick_version = $imagick->getVersion();
		} else {
			$imagick_version = __( 'Not available' );
		}
		$info['wp-media']['fields']['imagick_module_version'] = array(
			'label' => __( 'ImageMagick version number' ),
			'value' => ( is_array( $imagick_version ) ? $imagick_version['versionNumber'] : $imagick_version ),
		);
		$info['wp-media']['fields']['imagemagick_version']    = array(
			'label' => __( 'ImageMagick version string' ),
			'value' => ( is_array( $imagick_version ) ? $imagick_version['versionString'] : $imagick_version ),
		);

		// If Imagick is used as our editor, provide some more information about its limitations.
		if ( 'WP_Image_Editor_Imagick' === _wp_image_editor_choose() && isset( $imagick ) && $imagick instanceof Imagick ) {
			$limits = array(
				'area'   => ( defined( 'imagick::RESOURCETYPE_AREA' ) ? size_format( $imagick->getResourceLimit( imagick::RESOURCETYPE_AREA ) ) : __( 'Not available' ) ),
				'disk'   => ( defined( 'imagick::RESOURCETYPE_DISK' ) ? $imagick->getResourceLimit( imagick::RESOURCETYPE_DISK ) : __( 'Not available' ) ),
				'file'   => ( defined( 'imagick::RESOURCETYPE_FILE' ) ? $imagick->getResourceLimit( imagick::RESOURCETYPE_FILE ) : __( 'Not available' ) ),
				'map'    => ( defined( 'imagick::RESOURCETYPE_MAP' ) ? size_format( $imagick->getResourceLimit( imagick::RESOURCETYPE_MAP ) ) : __( 'Not available' ) ),
				'memory' => ( defined( 'imagick::RESOURCETYPE_MEMORY' ) ? size_format( $imagick->getResourceLimit( imagick::RESOURCETYPE_MEMORY ) ) : __( 'Not available' ) ),
				'thread' => ( defined( 'imagick::RESOURCETYPE_THREAD' ) ? $imagick->getResourceLimit( imagick::RESOURCETYPE_THREAD ) : __( 'Not available' ) ),
			);

			$info['wp-media']['fields']['imagick_limits'] = array(
				'label' => __( 'Imagick Resource Limits' ),
				'value' => $limits,
			);
		}

		// Get GD information, if available.
		if ( function_exists( 'gd_info' ) ) {
			$gd = gd_info();
		} else {
			$gd = false;
		}
		$info['wp-media']['fields']['gd_version'] = array(
			'label' => __( 'GD version' ),
			'value' => ( is_array( $gd ) ? $gd['GD Version'] : __( 'Not available' ) ),
		);

		// Get Ghostscript information, if available.
		if ( function_exists( 'exec' ) ) {
			$gs = exec( 'gs --version' );
			$gs = ( ! empty( $gs ) ? $gs : __( 'Not available' ) );
		} else {
			$gs = __( 'Unable to determine if Ghostscript is installed' );
		}
		$info['wp-media']['fields']['ghostscript_version'] = array(
			'label' => __( 'Ghostscript version' ),
			'value' => $gs,
		);

		// Populate the server debug fields.
		$info['wp-server']['fields']['server_architecture'] = array(
			'label' => __( 'Server architecture' ),
			'value' => ( ! function_exists( 'php_uname' ) ? __( 'Unable to determine server architecture' ) : sprintf( '%s %s %s', php_uname( 's' ), php_uname( 'r' ), php_uname( 'm' ) ) ),
		);
		$info['wp-server']['fields']['httpd_software']      = array(
			'label' => __( 'Web server' ),
			'value' => ( isset( $_SERVER['SERVER_SOFTWARE'] ) ? $_SERVER['SERVER_SOFTWARE'] : __( 'Unable to determine what web server software is used' ) ),
		);
		$info['wp-server']['fields']['php_version']         = array(
			'label' => __( 'PHP version' ),
			'value' => ( ! function_exists( 'phpversion' ) ? __( 'Unable to determine PHP version' ) : sprintf(
				'%s %s',
				phpversion(),
				( 64 === PHP_INT_SIZE * 8 ? __( '(Supports 64bit values)' ) : __( '(Does not support 64bit values)' ) )
			)
			),
		);
		$info['wp-server']['fields']['php_sapi']            = array(
			'label' => __( 'PHP SAPI' ),
			'value' => ( ! function_exists( 'php_sapi_name' ) ? __( 'Unable to determine PHP SAPI' ) : php_sapi_name() ),
		);

		// Some servers disable `ini_set()` and `ini_get()`, we check this before trying to get configuration values.
		if ( ! function_exists( 'ini_get' ) ) {
			$info['wp-server']['fields']['ini_get'] = array(
				'label' => __( 'Server settings' ),
				'value' => __( 'Unable to determine some settings, as the ini_get() function has been disabled.' ),
			);
		} else {
			$info['wp-server']['fields']['max_input_variables'] = array(
				'label' => __( 'PHP max input variables' ),
				'value' => ini_get( 'max_input_vars' ),
			);
			$info['wp-server']['fields']['time_limit']          = array(
				'label' => __( 'PHP time limit' ),
				'value' => ini_get( 'max_execution_time' ),
			);
			$info['wp-server']['fields']['memory_limit']        = array(
				'label' => __( 'PHP memory limit' ),
				'value' => ini_get( 'memory_limit' ),
			);
			$info['wp-server']['fields']['max_input_time']      = array(
				'label' => __( 'Max input time' ),
				'value' => ini_get( 'max_input_time' ),
			);
			$info['wp-server']['fields']['upload_max_size']     = array(
				'label' => __( 'Upload max filesize' ),
				'value' => ini_get( 'upload_max_filesize' ),
			);
			$info['wp-server']['fields']['php_post_max_size']   = array(
				'label' => __( 'PHP post max size' ),
				'value' => ini_get( 'post_max_size' ),
			);
		}

		if ( function_exists( 'curl_version' ) ) {
			$curl = curl_version();

			$info['wp-server']['fields']['curl_version'] = array(
				'label' => __( 'cURL version' ),
				'value' => sprintf( '%s %s', $curl['version'], $curl['ssl_version'] ),
			);
		} else {
			$info['wp-server']['fields']['curl_version'] = array(
				'label' => __( 'cURL version' ),
				'value' => __( 'Not available' ),
			);
		}

		$info['wp-server']['fields']['suhosin'] = array(
			'label' => __( 'Is SUHOSIN installed?' ),
			'value' => ( ( extension_loaded( 'suhosin' ) || ( defined( 'SUHOSIN_PATCH' ) && constant( 'SUHOSIN_PATCH' ) ) ) ? __( 'Yes' ) : __( 'No' ) ),
		);

		$info['wp-server']['fields']['imagick_availability'] = array(
			'label' => __( 'Is the Imagick library available?' ),
			'value' => ( extension_loaded( 'imagick' ) ? __( 'Yes' ) : __( 'No' ) ),
		);

		// Check if a .htaccess file exists.
		if ( is_file( ABSPATH . '/.htaccess' ) ) {
			// If the file exists, grab the content of it.
			$htaccess_content = file_get_contents( ABSPATH . '/.htaccess' );

			// Filter away the core WordPress rules.
			$filtered_htaccess_content = trim( preg_replace( '/\# BEGIN WordPress[\s\S]+?# END WordPress/si', '', $htaccess_content ) );

			$info['wp-server']['fields']['htaccess_extra_rules'] = array(
				'label' => __( 'htaccess rules' ),
				'value' => ( ! empty( $filtered_htaccess_content ) ? __( 'Custom rules have been added to your htaccess file.' ) : __( 'Your htaccess file contains only core WordPress features.' ) ),
			);
		}

		// Populate the database debug fields.
		if ( is_resource( $wpdb->dbh ) ) {
			// Old mysql extension.
			$extension = 'mysql';
		} elseif ( is_object( $wpdb->dbh ) ) {
			// mysqli or PDO.
			$extension = get_class( $wpdb->dbh );
		} else {
			// Unknown sql extension.
			$extension = null;
		}

		/*
		 * Check what database engine is used, this will throw compatibility
		 * warnings from PHP compatibility testers, but `mysql_*` is
		 * still valid in PHP 5.6, so we need to account for that.
		 */
		if ( method_exists( $wpdb, 'db_version' ) ) {
			if ( $wpdb->use_mysqli ) {
				// phpcs:ignore WordPress.DB.RestrictedFunctions.mysql_mysqli_get_server_info
				$server = mysqli_get_server_info( $wpdb->dbh );
			} else {
				// phpcs:ignore WordPress.DB.RestrictedFunctions.mysql_mysql_get_server_info
				$server = mysql_get_server_info( $wpdb->dbh );
			}
		} else {
			$server = null;
		}

		if ( isset( $wpdb->use_mysqli ) && $wpdb->use_mysqli ) {
			$client_version = $wpdb->dbh->client_info;
		} else {
			// phpcs:ignore WordPress.DB.RestrictedFunctions.mysql_mysql_get_client_info
			if ( preg_match( '|[0-9]{1,2}\.[0-9]{1,2}\.[0-9]{1,2}|', mysql_get_client_info(), $matches ) ) {
				$client_version = $matches[0];
			} else {
				$client_version = null;
			}
		}

		$info['wp-database']['fields']['extension']       = array(
			'label' => __( 'Extension' ),
			'value' => $extension,
		);
		$info['wp-database']['fields']['server_version']  = array(
			'label' => __( 'Server version' ),
			'value' => $server,
		);
		$info['wp-database']['fields']['client_version']  = array(
			'label' => __( 'Client version' ),
			'value' => $client_version,
		);
		$info['wp-database']['fields']['database_user']   = array(
			'label'   => __( 'Database user' ),
			'value'   => $wpdb->dbuser,
			'private' => true,
		);
		$info['wp-database']['fields']['database_host']   = array(
			'label'   => __( 'Database host' ),
			'value'   => $wpdb->dbhost,
			'private' => true,
		);
		$info['wp-database']['fields']['database_name']   = array(
			'label'   => __( 'Database name' ),
			'value'   => $wpdb->dbname,
			'private' => true,
		);
		$info['wp-database']['fields']['database_prefix'] = array(
			'label'   => __( 'Database prefix' ),
			'value'   => $wpdb->prefix,
			'private' => true,
		);

		// List must use plugins if there are any.
		$mu_plugins = get_mu_plugins();

		foreach ( $mu_plugins as $plugin_path => $plugin ) {
			$plugin_version = $plugin['Version'];
			$plugin_author  = $plugin['Author'];

			$plugin_version_string = __( 'No version or author information is available.' );

			if ( ! empty( $plugin_version ) && ! empty( $plugin_author ) ) {
				// translators: %1$s: Plugin version number. %2$s: Plugin author name.
				$plugin_version_string = sprintf( __( 'Version %1$s by %2$s' ), $plugin_version, $plugin_author );
			}
			if ( empty( $plugin_version ) && ! empty( $plugin_author ) ) {
				// translators: %s: Plugin author name.
				$plugin_version_string = sprintf( __( 'By %s' ), $plugin_author );
			}
			if ( ! empty( $plugin_version ) && empty( $plugin_author ) ) {
				// translators: %s: Plugin version number.
				$plugin_version_string = sprintf( __( 'Version %s' ), $plugin_version );
			}

			$info['wp-mu-plugins']['fields'][ sanitize_key( $plugin['Name'] ) ] = array(
				'label' => $plugin['Name'],
				'value' => $plugin_version_string,
			);
		}

		// List all available plugins.
		$plugins        = get_plugins();
		$plugin_updates = get_plugin_updates();

		foreach ( $plugins as $plugin_path => $plugin ) {
			$plugin_part = ( is_plugin_active( $plugin_path ) ) ? 'wp-plugins-active' : 'wp-plugins-inactive';

			$plugin_version = $plugin['Version'];
			$plugin_author  = $plugin['Author'];

			$plugin_version_string = __( 'No version or author information is available.' );

			if ( ! empty( $plugin_version ) && ! empty( $plugin_author ) ) {
				// translators: %1$s: Plugin version number. %2$s: Plugin author name.
				$plugin_version_string = sprintf( __( 'Version %1$s by %2$s' ), $plugin_version, $plugin_author );
			}
			if ( empty( $plugin_version ) && ! empty( $plugin_author ) ) {
				// translators: %s: Plugin author name.
				$plugin_version_string = sprintf( __( 'By %s' ), $plugin_author );
			}
			if ( ! empty( $plugin_version ) && empty( $plugin_author ) ) {
				// translators: %s: Plugin version number.
				$plugin_version_string = sprintf( __( 'Version %s' ), $plugin_version );
			}

			if ( array_key_exists( $plugin_path, $plugin_updates ) ) {
				// translators: %s: Latest plugin version number.
				$plugin_update_needed = ' ' . sprintf( __( '(Latest version: %s)' ), $plugin_updates[ $plugin_path ]->update->new_version );
			} else {
				$plugin_update_needed = '';
			}

			$info[ $plugin_part ]['fields'][ sanitize_key( $plugin['Name'] ) ] = array(
				'label' => $plugin['Name'],
				'value' => $plugin_version_string . $plugin_update_needed,
			);
		}

		// Populate the section for the currently active theme.
		global $_wp_theme_features;
		$theme_features = array();
		if ( ! empty( $_wp_theme_features ) ) {
			foreach ( $_wp_theme_features as $feature => $options ) {
				$theme_features[] = $feature;
			}
		}

		$active_theme  = wp_get_theme();
		$theme_updates = get_theme_updates();

		if ( array_key_exists( $active_theme->stylesheet, $theme_updates ) ) {
			// translators: %s: Latest theme version number.
			$theme_update_needed_active = ' ' . sprintf( __( '(Latest version: %s)' ), $theme_updates[ $active_theme->stylesheet ]->update['new_version'] );
		} else {
			$theme_update_needed_active = '';
		}

		$info['wp-active-theme']['fields'] = array(
			'name'           => array(
				'label' => __( 'Name' ),
				// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				'value' => $active_theme->Name,
			),
			'version'        => array(
				'label' => __( 'Version' ),
				// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				'value' => $active_theme->Version . $theme_update_needed_active,
			),
			'author'         => array(
				'label' => __( 'Author' ),
				// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				'value' => wp_kses( $active_theme->Author, array() ),
			),
			'author_website' => array(
				'label' => __( 'Author website' ),
				'value' => ( $active_theme->offsetGet( 'Author URI' ) ? $active_theme->offsetGet( 'Author URI' ) : __( 'Undefined' ) ),
			),
			'parent_theme'   => array(
				'label' => __( 'Parent theme' ),
				'value' => ( $active_theme->parent_theme ? $active_theme->parent_theme : __( 'None' ) ),
			),
			'theme_features' => array(
				'label' => __( 'Theme features' ),
				'value' => implode( ', ', $theme_features ),
			),
		);

		// Populate a list of all themes available in the install.
		$all_themes = wp_get_themes();

		foreach ( $all_themes as $theme_slug => $theme ) {
			// Ignore the currently active theme from the list of all themes.
			if ( $active_theme->stylesheet == $theme_slug ) {
				continue;
			}
			// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			$theme_version = $theme->Version;
			// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			$theme_author = $theme->Author;

			$theme_version_string = __( 'No version or author information is available.' );

			if ( ! empty( $theme_version ) && ! empty( $theme_author ) ) {
				// translators: %1$s: Theme version number. %2$s: Theme author name.
				$theme_version_string = sprintf( __( 'Version %1$s by %2$s' ), $theme_version, wp_kses( $theme_author, array() ) );
			}
			if ( empty( $theme_version ) && ! empty( $theme_author ) ) {
				// translators: %s: Theme author name.
				$theme_version_string = sprintf( __( 'By %s' ), wp_kses( $theme_author, array() ) );
			}
			if ( ! empty( $theme_version ) && empty( $theme_author ) ) {
				// translators: %s: Theme version number.
				$theme_version_string = sprintf( __( 'Version %s' ), $theme_version );
			}

			if ( array_key_exists( $theme_slug, $theme_updates ) ) {
				// translators: %s: Latest theme version number.
				$theme_update_needed = ' ' . sprintf( __( '(Latest version: %s)' ), $theme_updates[ $theme_slug ]->update['new_version'] );
			} else {
				$theme_update_needed = '';
			}

			$info['wp-themes']['fields'][ sanitize_key( $theme->Name ) ] = array(
				'label' => sprintf(
					// translators: %1$s: Theme name. %2$s: Theme slug.
					__( '%1$s (%2$s)' ),
					// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
					$theme->Name,
					$theme_slug
				),
				'value' => $theme_version_string . $theme_update_needed,
			);
		}

		// Add more filesystem checks
		if ( defined( 'WPMU_PLUGIN_DIR' ) && is_dir( WPMU_PLUGIN_DIR ) ) {
			$info['wp-filesystem']['fields']['mu_plugin_directory'] = array(
				'label' => __( 'The must use plugins directory' ),
				'value' => ( wp_is_writable( WPMU_PLUGIN_DIR ) ? __( 'Writable' ) : __( 'Not writable' ) ),
			);
		}

		/**
		 * Add or modify new debug sections.
		 *
		 * Plugin or themes may wish to introduce their own debug information without creating additional admin pages for this
		 * kind of information as it is rarely needed, they can then utilize this filter to introduce their own sections.
		 *
		 * Array keys added by core are all prefixed with `wp-`, plugins and themes are encouraged to use their own slug as
		 * a prefix, both for consistency as well as avoiding key collisions.
		 *
		 * @since 5.2.0
		 *
		 * @param array $args {
		 *     The debug information to be added to the core information page.
		 *
		 *     @type string  $label        The title for this section of the debug output.
		 *     @type string  $description  Optional. A description for your information section which may contain basic HTML
		 *                                 markup: `em`, `strong` and `a` for linking to documentation or putting emphasis.
		 *     @type boolean $show_count   Optional. If set to `true` the amount of fields will be included in the title for
		 *                                 this section.
		 *     @type boolean $private      Optional. If set to `true` the section and all associated fields will be excluded
		 *                                 from the copy-paste text area.
		 *     @type array   $fields {
		 *         An associative array containing the data to be displayed.
		 *
		 *         @type string  $label    The label for this piece of information.
		 *         @type string  $value    The output that is of interest for this field.
		 *         @type boolean $private  Optional. If set to `true` the field will not be included in the copy-paste text area
		 *                                 on top of the page, allowing you to show, for example, API keys here.
		 *     }
		 * }
		 */
		$info = apply_filters( 'debug_information', $info );

		if ( ! empty( $locale ) ) {
			// Change the language used for translations
			if ( function_exists( 'restore_previous_locale' ) && $switched_locale ) {
				restore_previous_locale();
			}
		}

		return $info;
	}

	/**
	 * Format the information gathered for debugging, in a manner suitable for copying to a forum or support ticket.
	 *
	 * @since 5.2.0
	 *
	 * @param array $info_array Information gathered from the `WP_Debug_Data::debug_data` function.
	 * @param string $type      Optional. The data type to format the information as. Default 'text'.
	 * @return string The formatted data.
	 */
	public static function format( $info_array, $type = 'text' ) {
		$return = '';

		foreach ( $info_array as $section => $details ) {
			// Skip this section if there are no fields, or the section has been declared as private.
			if ( empty( $details['fields'] ) || ( isset( $details['private'] ) && $details['private'] ) ) {
				continue;
			}

			$return .= sprintf(
				"### %s%s ###\n\n",
				$details['label'],
				( isset( $details['show_count'] ) && $details['show_count'] ? sprintf( ' (%d)', count( $details['fields'] ) ) : '' )
			);

			foreach ( $details['fields'] as $field ) {
				if ( isset( $field['private'] ) && true === $field['private'] ) {
					continue;
				}

				$values = $field['value'];
				if ( is_array( $field['value'] ) ) {
					$values = '';

					foreach ( $field['value'] as $name => $value ) {
						$values .= sprintf(
							"\n\t%s: %s",
							$name,
							$value
						);
					}
				}

				$return .= sprintf(
					"%s: %s\n",
					$field['label'],
					$values
				);
			}
			$return .= "\n";
		}

		return $return;
	}

	/**
	 * Return the size of a directory, including all subdirectories.
	 *
	 * @since 5.2.0
	 *
	 * @param string     $path                 The directory to check.
	 * @param string|int $max_execution_time   How long a PHP script can run on this host.
	 * @param float      $start_execution_time When we started executing this section of the script.
	 *
	 * @return int The directory size, in bytes.
	 */
	public static function get_directory_size( $path, $max_execution_time, $start_execution_time ) {
		$size = 0;

		foreach ( new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $path ) ) as $file ) {
			// Check if the maximum execution time is a value considered "infinite".
			if ( 0 !== $max_execution_time && -1 !== $max_execution_time ) {
				$runtime = ( microtime( true ) - $start_execution_time );

				// If the script has been running as long, or longer, as it is allowed, return a failure message.
				if ( $runtime >= $max_execution_time ) {
					return -1;
				}
			}
			$size += $file->getSize();
		}

		return $size;
	}

	/**
	 * Fetch the total size of all the database tables for the active database user.
	 *
	 * @since 5.2.0
	 *
	 * @return int The size of the database, in bytes.
	 */
	public static function get_database_size() {
		global $wpdb;
		$size = 0;
		$rows = $wpdb->get_results( 'SHOW TABLE STATUS', ARRAY_A );

		if ( $wpdb->num_rows > 0 ) {
			foreach ( $rows as $row ) {
				$size += $row['Data_length'] + $row['Index_length'];
			}
		}

		return $size;
	}
}
