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
	 * @since 5.3.0 Added database charset, database collation,
	 *              and timezone information.
	 * @since 5.5.0 Added pretty permalinks support information.
	 *
	 * @throws ImagickException
	 * @global wpdb $wpdb WordPress database abstraction object.
	 *
	 * @return array The debug data for the site.
	 */
	static function debug_data() {
		global $wpdb;

		// Save few function calls.
		$upload_dir             = wp_upload_dir();
		$permalink_structure    = get_option( 'permalink_structure' );
		$is_ssl                 = is_ssl();
		$is_multisite           = is_multisite();
		$users_can_register     = get_option( 'users_can_register' );
		$blog_public            = get_option( 'blog_public' );
		$default_comment_status = get_option( 'default_comment_status' );
		$environment_type       = wp_get_environment_type();
		$core_version           = get_bloginfo( 'version' );
		$core_updates           = get_core_updates();
		$core_update_needed     = '';

		foreach ( $core_updates as $core => $update ) {
			if ( 'upgrade' === $update->response ) {
				/* translators: %s: Latest WordPress version number. */
				$core_update_needed = ' ' . sprintf( __( '(Latest version: %s)' ), $update->version );
			} else {
				$core_update_needed = '';
			}
		}

		// Set up the array that holds all debug information.
		$info = array();

		$info['wp-core'] = array(
			'label'  => __( 'WordPress' ),
			'fields' => array(
				'version'                => array(
					'label' => __( 'Version' ),
					'value' => $core_version . $core_update_needed,
					'debug' => $core_version,
				),
				'site_language'          => array(
					'label' => __( 'Site Language' ),
					'value' => get_locale(),
				),
				'user_language'          => array(
					'label' => __( 'User Language' ),
					'value' => get_user_locale(),
				),
				'timezone'               => array(
					'label' => __( 'Timezone' ),
					'value' => wp_timezone_string(),
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
					'value' => $permalink_structure ? $permalink_structure : __( 'No permalink structure set' ),
					'debug' => $permalink_structure,
				),
				'https_status'           => array(
					'label' => __( 'Is this site using HTTPS?' ),
					'value' => $is_ssl ? __( 'Yes' ) : __( 'No' ),
					'debug' => $is_ssl,
				),
				'multisite'              => array(
					'label' => __( 'Is this a multisite?' ),
					'value' => $is_multisite ? __( 'Yes' ) : __( 'No' ),
					'debug' => $is_multisite,
				),
				'user_registration'      => array(
					'label' => __( 'Can anyone register on this site?' ),
					'value' => $users_can_register ? __( 'Yes' ) : __( 'No' ),
					'debug' => $users_can_register,
				),
				'blog_public'            => array(
					'label' => __( 'Is this site discouraging search engines?' ),
					'value' => $blog_public ? __( 'No' ) : __( 'Yes' ),
					'debug' => $blog_public,
				),
				'default_comment_status' => array(
					'label' => __( 'Default comment status' ),
					'value' => 'open' === $default_comment_status ? _x( 'Open', 'comment status' ) : _x( 'Closed', 'comment status' ),
					'debug' => $default_comment_status,
				),
				'environment_type'       => array(
					'label' => __( 'Environment type' ),
					'value' => $environment_type,
					'debug' => $environment_type,
				),
			),
		);

		if ( ! $is_multisite ) {
			$info['wp-paths-sizes'] = array(
				'label'  => __( 'Directories and Sizes' ),
				'fields' => array(),
			);
		}

		$info['wp-dropins'] = array(
			'label'       => __( 'Drop-ins' ),
			'show_count'  => true,
			'description' => sprintf(
				/* translators: %s: wp-content directory name. */
				__( 'Drop-ins are single files, found in the %s directory, that replace or enhance WordPress features in ways that are not possible for traditional plugins.' ),
				'<code>' . str_replace( ABSPATH, '', WP_CONTENT_DIR ) . '</code>'
			),
			'fields'      => array(),
		);

		$info['wp-active-theme'] = array(
			'label'  => __( 'Active Theme' ),
			'fields' => array(),
		);

		$info['wp-parent-theme'] = array(
			'label'  => __( 'Parent Theme' ),
			'fields' => array(),
		);

		$info['wp-themes-inactive'] = array(
			'label'      => __( 'Inactive Themes' ),
			'show_count' => true,
			'fields'     => array(),
		);

		$info['wp-mu-plugins'] = array(
			'label'      => __( 'Must Use Plugins' ),
			'show_count' => true,
			'fields'     => array(),
		);

		$info['wp-plugins-active'] = array(
			'label'      => __( 'Active Plugins' ),
			'show_count' => true,
			'fields'     => array(),
		);

		$info['wp-plugins-inactive'] = array(
			'label'      => __( 'Inactive Plugins' ),
			'show_count' => true,
			'fields'     => array(),
		);

		$info['wp-media'] = array(
			'label'  => __( 'Media Handling' ),
			'fields' => array(),
		);

		$info['wp-server'] = array(
			'label'       => __( 'Server' ),
			'description' => __( 'The options shown below relate to your server setup. If changes are required, you may need your web host&#8217;s assistance.' ),
			'fields'      => array(),
		);

		$info['wp-database'] = array(
			'label'  => __( 'Database' ),
			'fields' => array(),
		);

		// Check if WP_DEBUG_LOG is set.
		$wp_debug_log_value = __( 'Disabled' );

		if ( is_string( WP_DEBUG_LOG ) ) {
			$wp_debug_log_value = WP_DEBUG_LOG;
		} elseif ( WP_DEBUG_LOG ) {
			$wp_debug_log_value = __( 'Enabled' );
		}

		// Check CONCATENATE_SCRIPTS.
		if ( defined( 'CONCATENATE_SCRIPTS' ) ) {
			$concatenate_scripts       = CONCATENATE_SCRIPTS ? __( 'Enabled' ) : __( 'Disabled' );
			$concatenate_scripts_debug = CONCATENATE_SCRIPTS ? 'true' : 'false';
		} else {
			$concatenate_scripts       = __( 'Undefined' );
			$concatenate_scripts_debug = 'undefined';
		}

		// Check COMPRESS_SCRIPTS.
		if ( defined( 'COMPRESS_SCRIPTS' ) ) {
			$compress_scripts       = COMPRESS_SCRIPTS ? __( 'Enabled' ) : __( 'Disabled' );
			$compress_scripts_debug = COMPRESS_SCRIPTS ? 'true' : 'false';
		} else {
			$compress_scripts       = __( 'Undefined' );
			$compress_scripts_debug = 'undefined';
		}

		// Check COMPRESS_CSS.
		if ( defined( 'COMPRESS_CSS' ) ) {
			$compress_css       = COMPRESS_CSS ? __( 'Enabled' ) : __( 'Disabled' );
			$compress_css_debug = COMPRESS_CSS ? 'true' : 'false';
		} else {
			$compress_css       = __( 'Undefined' );
			$compress_css_debug = 'undefined';
		}

		// Check WP_LOCAL_DEV.
		if ( defined( 'WP_LOCAL_DEV' ) ) {
			$wp_local_dev       = WP_LOCAL_DEV ? __( 'Enabled' ) : __( 'Disabled' );
			$wp_local_dev_debug = WP_LOCAL_DEV ? 'true' : 'false';
		} else {
			$wp_local_dev       = __( 'Undefined' );
			$wp_local_dev_debug = 'undefined';
		}

		$info['wp-constants'] = array(
			'label'       => __( 'WordPress Constants' ),
			'description' => __( 'These settings alter where and how parts of WordPress are loaded.' ),
			'fields'      => array(
				'ABSPATH'             => array(
					'label'   => 'ABSPATH',
					'value'   => ABSPATH,
					'private' => true,
				),
				'WP_HOME'             => array(
					'label' => 'WP_HOME',
					'value' => ( defined( 'WP_HOME' ) ? WP_HOME : __( 'Undefined' ) ),
					'debug' => ( defined( 'WP_HOME' ) ? WP_HOME : 'undefined' ),
				),
				'WP_SITEURL'          => array(
					'label' => 'WP_SITEURL',
					'value' => ( defined( 'WP_SITEURL' ) ? WP_SITEURL : __( 'Undefined' ) ),
					'debug' => ( defined( 'WP_SITEURL' ) ? WP_SITEURL : 'undefined' ),
				),
				'WP_CONTENT_DIR'      => array(
					'label' => 'WP_CONTENT_DIR',
					'value' => WP_CONTENT_DIR,
				),
				'WP_PLUGIN_DIR'       => array(
					'label' => 'WP_PLUGIN_DIR',
					'value' => WP_PLUGIN_DIR,
				),
				'WP_MAX_MEMORY_LIMIT' => array(
					'label' => 'WP_MAX_MEMORY_LIMIT',
					'value' => WP_MAX_MEMORY_LIMIT,
				),
				'WP_DEBUG'            => array(
					'label' => 'WP_DEBUG',
					'value' => WP_DEBUG ? __( 'Enabled' ) : __( 'Disabled' ),
					'debug' => WP_DEBUG,
				),
				'WP_DEBUG_DISPLAY'    => array(
					'label' => 'WP_DEBUG_DISPLAY',
					'value' => WP_DEBUG_DISPLAY ? __( 'Enabled' ) : __( 'Disabled' ),
					'debug' => WP_DEBUG_DISPLAY,
				),
				'WP_DEBUG_LOG'        => array(
					'label' => 'WP_DEBUG_LOG',
					'value' => $wp_debug_log_value,
					'debug' => WP_DEBUG_LOG,
				),
				'SCRIPT_DEBUG'        => array(
					'label' => 'SCRIPT_DEBUG',
					'value' => SCRIPT_DEBUG ? __( 'Enabled' ) : __( 'Disabled' ),
					'debug' => SCRIPT_DEBUG,
				),
				'WP_CACHE'            => array(
					'label' => 'WP_CACHE',
					'value' => WP_CACHE ? __( 'Enabled' ) : __( 'Disabled' ),
					'debug' => WP_CACHE,
				),
				'CONCATENATE_SCRIPTS' => array(
					'label' => 'CONCATENATE_SCRIPTS',
					'value' => $concatenate_scripts,
					'debug' => $concatenate_scripts_debug,
				),
				'COMPRESS_SCRIPTS'    => array(
					'label' => 'COMPRESS_SCRIPTS',
					'value' => $compress_scripts,
					'debug' => $compress_scripts_debug,
				),
				'COMPRESS_CSS'        => array(
					'label' => 'COMPRESS_CSS',
					'value' => $compress_css,
					'debug' => $compress_css_debug,
				),
				'WP_LOCAL_DEV'        => array(
					'label' => 'WP_LOCAL_DEV',
					'value' => $wp_local_dev,
					'debug' => $wp_local_dev_debug,
				),
				'DB_CHARSET'          => array(
					'label' => 'DB_CHARSET',
					'value' => ( defined( 'DB_CHARSET' ) ? DB_CHARSET : __( 'Undefined' ) ),
					'debug' => ( defined( 'DB_CHARSET' ) ? DB_CHARSET : 'undefined' ),
				),
				'DB_COLLATE'          => array(
					'label' => 'DB_COLLATE',
					'value' => ( defined( 'DB_COLLATE' ) ? DB_COLLATE : __( 'Undefined' ) ),
					'debug' => ( defined( 'DB_COLLATE' ) ? DB_COLLATE : 'undefined' ),
				),
			),
		);

		$is_writable_abspath            = wp_is_writable( ABSPATH );
		$is_writable_wp_content_dir     = wp_is_writable( WP_CONTENT_DIR );
		$is_writable_upload_dir         = wp_is_writable( $upload_dir['basedir'] );
		$is_writable_wp_plugin_dir      = wp_is_writable( WP_PLUGIN_DIR );
		$is_writable_template_directory = wp_is_writable( get_theme_root( get_template() ) );

		$info['wp-filesystem'] = array(
			'label'       => __( 'Filesystem Permissions' ),
			'description' => __( 'Shows whether WordPress is able to write to the directories it needs access to.' ),
			'fields'      => array(
				'wordpress'  => array(
					'label' => __( 'The main WordPress directory' ),
					'value' => ( $is_writable_abspath ? __( 'Writable' ) : __( 'Not writable' ) ),
					'debug' => ( $is_writable_abspath ? 'writable' : 'not writable' ),
				),
				'wp-content' => array(
					'label' => __( 'The wp-content directory' ),
					'value' => ( $is_writable_wp_content_dir ? __( 'Writable' ) : __( 'Not writable' ) ),
					'debug' => ( $is_writable_wp_content_dir ? 'writable' : 'not writable' ),
				),
				'uploads'    => array(
					'label' => __( 'The uploads directory' ),
					'value' => ( $is_writable_upload_dir ? __( 'Writable' ) : __( 'Not writable' ) ),
					'debug' => ( $is_writable_upload_dir ? 'writable' : 'not writable' ),
				),
				'plugins'    => array(
					'label' => __( 'The plugins directory' ),
					'value' => ( $is_writable_wp_plugin_dir ? __( 'Writable' ) : __( 'Not writable' ) ),
					'debug' => ( $is_writable_wp_plugin_dir ? 'writable' : 'not writable' ),
				),
				'themes'     => array(
					'label' => __( 'The themes directory' ),
					'value' => ( $is_writable_template_directory ? __( 'Writable' ) : __( 'Not writable' ) ),
					'debug' => ( $is_writable_template_directory ? 'writable' : 'not writable' ),
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

			$info['wp-core']['fields']['user_count'] = array(
				'label' => __( 'User count' ),
				'value' => get_user_count(),
			);

			$info['wp-core']['fields']['site_count'] = array(
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
		$wp_dotorg = wp_remote_get( 'https://wordpress.org', array( 'timeout' => 10 ) );

		if ( ! is_wp_error( $wp_dotorg ) ) {
			$info['wp-core']['fields']['dotorg_communication'] = array(
				'label' => __( 'Communication with WordPress.org' ),
				'value' => __( 'WordPress.org is reachable' ),
				'debug' => 'true',
			);
		} else {
			$info['wp-core']['fields']['dotorg_communication'] = array(
				'label' => __( 'Communication with WordPress.org' ),
				'value' => sprintf(
					/* translators: 1: The IP address WordPress.org resolves to. 2: The error returned by the lookup. */
					__( 'Unable to reach WordPress.org at %1$s: %2$s' ),
					gethostbyname( 'wordpress.org' ),
					$wp_dotorg->get_error_message()
				),
				'debug' => $wp_dotorg->get_error_message(),
			);
		}

		// Remove accordion for Directories and Sizes if in Multisite.
		if ( ! $is_multisite ) {
			$loading = __( 'Loading&hellip;' );

			$info['wp-paths-sizes']['fields'] = array(
				'wordpress_path' => array(
					'label' => __( 'WordPress directory location' ),
					'value' => untrailingslashit( ABSPATH ),
				),
				'wordpress_size' => array(
					'label' => __( 'WordPress directory size' ),
					'value' => $loading,
					'debug' => 'loading...',
				),
				'uploads_path'   => array(
					'label' => __( 'Uploads directory location' ),
					'value' => $upload_dir['basedir'],
				),
				'uploads_size'   => array(
					'label' => __( 'Uploads directory size' ),
					'value' => $loading,
					'debug' => 'loading...',
				),
				'themes_path'    => array(
					'label' => __( 'Themes directory location' ),
					'value' => get_theme_root(),
				),
				'themes_size'    => array(
					'label' => __( 'Themes directory size' ),
					'value' => $loading,
					'debug' => 'loading...',
				),
				'plugins_path'   => array(
					'label' => __( 'Plugins directory location' ),
					'value' => WP_PLUGIN_DIR,
				),
				'plugins_size'   => array(
					'label' => __( 'Plugins directory size' ),
					'value' => $loading,
					'debug' => 'loading...',
				),
				'database_size'  => array(
					'label' => __( 'Database size' ),
					'value' => $loading,
					'debug' => 'loading...',
				),
				'total_size'     => array(
					'label' => __( 'Total installation size' ),
					'value' => $loading,
					'debug' => 'loading...',
				),
			);
		}

		// Get a list of all drop-in replacements.
		$dropins = get_dropins();

		// Get dropins descriptions.
		$dropin_descriptions = _get_dropins();

		// Spare few function calls.
		$not_available = __( 'Not available' );

		foreach ( $dropins as $dropin_key => $dropin ) {
			$info['wp-dropins']['fields'][ sanitize_text_field( $dropin_key ) ] = array(
				'label' => $dropin_key,
				'value' => $dropin_descriptions[ $dropin_key ][0],
				'debug' => 'true',
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

		$info['wp-media']['fields']['imagemagick_version'] = array(
			'label' => __( 'ImageMagick version string' ),
			'value' => ( is_array( $imagick_version ) ? $imagick_version['versionString'] : $imagick_version ),
		);

		if ( ! function_exists( 'ini_get' ) ) {
			$info['wp-media']['fields']['ini_get'] = array(
				'label' => __( 'File upload settings' ),
				'value' => sprintf(
					/* translators: %s: ini_get() */
					__( 'Unable to determine some settings, as the %s function has been disabled.' ),
					'ini_get()'
				),
				'debug' => 'ini_get() is disabled',
			);
		} else {
			// Get the PHP ini directive values.
			$post_max_size       = ini_get( 'post_max_size' );
			$upload_max_filesize = ini_get( 'upload_max_filesize' );
			$max_file_uploads    = ini_get( 'max_file_uploads' );
			$effective           = min( wp_convert_hr_to_bytes( $post_max_size ), wp_convert_hr_to_bytes( $upload_max_filesize ) );

			// Add info in Media section.
			$info['wp-media']['fields']['file_uploads']        = array(
				'label' => __( 'File uploads' ),
				'value' => empty( ini_get( 'file_uploads' ) ) ? __( 'Disabled' ) : __( 'Enabled' ),
				'debug' => 'File uploads is turned off',
			);
			$info['wp-media']['fields']['post_max_size']       = array(
				'label' => __( 'Max size of post data allowed' ),
				'value' => $post_max_size,
			);
			$info['wp-media']['fields']['upload_max_filesize'] = array(
				'label' => __( 'Max size of an uploaded file' ),
				'value' => $upload_max_filesize,
			);
			$info['wp-media']['fields']['max_effective_size']  = array(
				'label' => __( 'Max effective file size' ),
				'value' => size_format( $effective ),
			);
			$info['wp-media']['fields']['max_file_uploads']    = array(
				'label' => __( 'Max number of files allowed' ),
				'value' => number_format( $max_file_uploads ),
			);
		}

		// If Imagick is used as our editor, provide some more information about its limitations.
		if ( 'WP_Image_Editor_Imagick' === _wp_image_editor_choose() && isset( $imagick ) && $imagick instanceof Imagick ) {
			$limits = array(
				'area'   => ( defined( 'imagick::RESOURCETYPE_AREA' ) ? size_format( $imagick->getResourceLimit( imagick::RESOURCETYPE_AREA ) ) : $not_available ),
				'disk'   => ( defined( 'imagick::RESOURCETYPE_DISK' ) ? $imagick->getResourceLimit( imagick::RESOURCETYPE_DISK ) : $not_available ),
				'file'   => ( defined( 'imagick::RESOURCETYPE_FILE' ) ? $imagick->getResourceLimit( imagick::RESOURCETYPE_FILE ) : $not_available ),
				'map'    => ( defined( 'imagick::RESOURCETYPE_MAP' ) ? size_format( $imagick->getResourceLimit( imagick::RESOURCETYPE_MAP ) ) : $not_available ),
				'memory' => ( defined( 'imagick::RESOURCETYPE_MEMORY' ) ? size_format( $imagick->getResourceLimit( imagick::RESOURCETYPE_MEMORY ) ) : $not_available ),
				'thread' => ( defined( 'imagick::RESOURCETYPE_THREAD' ) ? $imagick->getResourceLimit( imagick::RESOURCETYPE_THREAD ) : $not_available ),
			);

			$limits_debug = array(
				'imagick::RESOURCETYPE_AREA'   => ( defined( 'imagick::RESOURCETYPE_AREA' ) ? size_format( $imagick->getResourceLimit( imagick::RESOURCETYPE_AREA ) ) : 'not available' ),
				'imagick::RESOURCETYPE_DISK'   => ( defined( 'imagick::RESOURCETYPE_DISK' ) ? $imagick->getResourceLimit( imagick::RESOURCETYPE_DISK ) : 'not available' ),
				'imagick::RESOURCETYPE_FILE'   => ( defined( 'imagick::RESOURCETYPE_FILE' ) ? $imagick->getResourceLimit( imagick::RESOURCETYPE_FILE ) : 'not available' ),
				'imagick::RESOURCETYPE_MAP'    => ( defined( 'imagick::RESOURCETYPE_MAP' ) ? size_format( $imagick->getResourceLimit( imagick::RESOURCETYPE_MAP ) ) : 'not available' ),
				'imagick::RESOURCETYPE_MEMORY' => ( defined( 'imagick::RESOURCETYPE_MEMORY' ) ? size_format( $imagick->getResourceLimit( imagick::RESOURCETYPE_MEMORY ) ) : 'not available' ),
				'imagick::RESOURCETYPE_THREAD' => ( defined( 'imagick::RESOURCETYPE_THREAD' ) ? $imagick->getResourceLimit( imagick::RESOURCETYPE_THREAD ) : 'not available' ),
			);

			$info['wp-media']['fields']['imagick_limits'] = array(
				'label' => __( 'Imagick Resource Limits' ),
				'value' => $limits,
				'debug' => $limits_debug,
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
			'value' => ( is_array( $gd ) ? $gd['GD Version'] : $not_available ),
			'debug' => ( is_array( $gd ) ? $gd['GD Version'] : 'not available' ),
		);

		// Get Ghostscript information, if available.
		if ( function_exists( 'exec' ) ) {
			$gs = exec( 'gs --version' );

			if ( empty( $gs ) ) {
				$gs       = $not_available;
				$gs_debug = 'not available';
			} else {
				$gs_debug = $gs;
			}
		} else {
			$gs       = __( 'Unable to determine if Ghostscript is installed' );
			$gs_debug = 'unknown';
		}

		$info['wp-media']['fields']['ghostscript_version'] = array(
			'label' => __( 'Ghostscript version' ),
			'value' => $gs,
			'debug' => $gs_debug,
		);

		// Populate the server debug fields.
		if ( function_exists( 'php_uname' ) ) {
			$server_architecture = sprintf( '%s %s %s', php_uname( 's' ), php_uname( 'r' ), php_uname( 'm' ) );
		} else {
			$server_architecture = 'unknown';
		}

		if ( function_exists( 'phpversion' ) ) {
			$php_version_debug = phpversion();
			// Whether PHP supports 64-bit.
			$php64bit = ( PHP_INT_SIZE * 8 === 64 );

			$php_version = sprintf(
				'%s %s',
				$php_version_debug,
				( $php64bit ? __( '(Supports 64bit values)' ) : __( '(Does not support 64bit values)' ) )
			);

			if ( $php64bit ) {
				$php_version_debug .= ' 64bit';
			}
		} else {
			$php_version       = __( 'Unable to determine PHP version' );
			$php_version_debug = 'unknown';
		}

		if ( function_exists( 'php_sapi_name' ) ) {
			$php_sapi = php_sapi_name();
		} else {
			$php_sapi = 'unknown';
		}

		$info['wp-server']['fields']['server_architecture'] = array(
			'label' => __( 'Server architecture' ),
			'value' => ( 'unknown' !== $server_architecture ? $server_architecture : __( 'Unable to determine server architecture' ) ),
			'debug' => $server_architecture,
		);
		$info['wp-server']['fields']['httpd_software']      = array(
			'label' => __( 'Web server' ),
			'value' => ( isset( $_SERVER['SERVER_SOFTWARE'] ) ? $_SERVER['SERVER_SOFTWARE'] : __( 'Unable to determine what web server software is used' ) ),
			'debug' => ( isset( $_SERVER['SERVER_SOFTWARE'] ) ? $_SERVER['SERVER_SOFTWARE'] : 'unknown' ),
		);
		$info['wp-server']['fields']['php_version']         = array(
			'label' => __( 'PHP version' ),
			'value' => $php_version,
			'debug' => $php_version_debug,
		);
		$info['wp-server']['fields']['php_sapi']            = array(
			'label' => __( 'PHP SAPI' ),
			'value' => ( 'unknown' !== $php_sapi ? $php_sapi : __( 'Unable to determine PHP SAPI' ) ),
			'debug' => $php_sapi,
		);

		// Some servers disable `ini_set()` and `ini_get()`, we check this before trying to get configuration values.
		if ( ! function_exists( 'ini_get' ) ) {
			$info['wp-server']['fields']['ini_get'] = array(
				'label' => __( 'Server settings' ),
				'value' => sprintf(
					/* translators: %s: ini_get() */
					__( 'Unable to determine some settings, as the %s function has been disabled.' ),
					'ini_get()'
				),
				'debug' => 'ini_get() is disabled',
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

			if ( WP_Site_Health::get_instance()->php_memory_limit !== ini_get( 'memory_limit' ) ) {
				$info['wp-server']['fields']['memory_limit']       = array(
					'label' => __( 'PHP memory limit' ),
					'value' => WP_Site_Health::get_instance()->php_memory_limit,
				);
				$info['wp-server']['fields']['admin_memory_limit'] = array(
					'label' => __( 'PHP memory limit (only for admin screens)' ),
					'value' => ini_get( 'memory_limit' ),
				);
			} else {
				$info['wp-server']['fields']['memory_limit'] = array(
					'label' => __( 'PHP memory limit' ),
					'value' => ini_get( 'memory_limit' ),
				);
			}

			$info['wp-server']['fields']['max_input_time']      = array(
				'label' => __( 'Max input time' ),
				'value' => ini_get( 'max_input_time' ),
			);
			$info['wp-server']['fields']['upload_max_filesize'] = array(
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
				'value' => $not_available,
				'debug' => 'not available',
			);
		}

		// SUHOSIN.
		$suhosin_loaded = ( extension_loaded( 'suhosin' ) || ( defined( 'SUHOSIN_PATCH' ) && constant( 'SUHOSIN_PATCH' ) ) );

		$info['wp-server']['fields']['suhosin'] = array(
			'label' => __( 'Is SUHOSIN installed?' ),
			'value' => ( $suhosin_loaded ? __( 'Yes' ) : __( 'No' ) ),
			'debug' => $suhosin_loaded,
		);

		// Imagick.
		$imagick_loaded = extension_loaded( 'imagick' );

		$info['wp-server']['fields']['imagick_availability'] = array(
			'label' => __( 'Is the Imagick library available?' ),
			'value' => ( $imagick_loaded ? __( 'Yes' ) : __( 'No' ) ),
			'debug' => $imagick_loaded,
		);

		// Pretty permalinks.
		$pretty_permalinks_supported = got_url_rewrite();

		$info['wp-server']['fields']['pretty_permalinks'] = array(
			'label' => __( 'Are pretty permalinks supported?' ),
			'value' => ( $pretty_permalinks_supported ? __( 'Yes' ) : __( 'No' ) ),
			'debug' => $pretty_permalinks_supported,
		);

		// Check if a .htaccess file exists.
		if ( is_file( ABSPATH . '.htaccess' ) ) {
			// If the file exists, grab the content of it.
			$htaccess_content = file_get_contents( ABSPATH . '.htaccess' );

			// Filter away the core WordPress rules.
			$filtered_htaccess_content = trim( preg_replace( '/\# BEGIN WordPress[\s\S]+?# END WordPress/si', '', $htaccess_content ) );
			$filtered_htaccess_content = ! empty( $filtered_htaccess_content );

			if ( $filtered_htaccess_content ) {
				/* translators: %s: .htaccess */
				$htaccess_rules_string = sprintf( __( 'Custom rules have been added to your %s file.' ), '.htaccess' );
			} else {
				/* translators: %s: .htaccess */
				$htaccess_rules_string = sprintf( __( 'Your %s file contains only core WordPress features.' ), '.htaccess' );
			}

			$info['wp-server']['fields']['htaccess_extra_rules'] = array(
				'label' => __( '.htaccess rules' ),
				'value' => $htaccess_rules_string,
				'debug' => $filtered_htaccess_content,
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

		$server = $wpdb->get_var( 'SELECT VERSION()' );

		if ( isset( $wpdb->use_mysqli ) && $wpdb->use_mysqli ) {
			$client_version = $wpdb->dbh->client_info;
		} else {
			// phpcs:ignore WordPress.DB.RestrictedFunctions.mysql_mysql_get_client_info,PHPCompatibility.Extensions.RemovedExtensions.mysql_DeprecatedRemoved
			if ( preg_match( '|[0-9]{1,2}\.[0-9]{1,2}\.[0-9]{1,2}|', mysql_get_client_info(), $matches ) ) {
				$client_version = $matches[0];
			} else {
				$client_version = null;
			}
		}

		$info['wp-database']['fields']['extension'] = array(
			'label' => __( 'Extension' ),
			'value' => $extension,
		);

		$info['wp-database']['fields']['server_version'] = array(
			'label' => __( 'Server version' ),
			'value' => $server,
		);

		$info['wp-database']['fields']['client_version'] = array(
			'label' => __( 'Client version' ),
			'value' => $client_version,
		);

		$info['wp-database']['fields']['database_user'] = array(
			'label'   => __( 'Database username' ),
			'value'   => $wpdb->dbuser,
			'private' => true,
		);

		$info['wp-database']['fields']['database_host'] = array(
			'label'   => __( 'Database host' ),
			'value'   => $wpdb->dbhost,
			'private' => true,
		);

		$info['wp-database']['fields']['database_name'] = array(
			'label'   => __( 'Database name' ),
			'value'   => $wpdb->dbname,
			'private' => true,
		);

		$info['wp-database']['fields']['database_prefix'] = array(
			'label'   => __( 'Table prefix' ),
			'value'   => $wpdb->prefix,
			'private' => true,
		);

		$info['wp-database']['fields']['database_charset'] = array(
			'label'   => __( 'Database charset' ),
			'value'   => $wpdb->charset,
			'private' => true,
		);

		$info['wp-database']['fields']['database_collate'] = array(
			'label'   => __( 'Database collation' ),
			'value'   => $wpdb->collate,
			'private' => true,
		);

		// List must use plugins if there are any.
		$mu_plugins = get_mu_plugins();

		foreach ( $mu_plugins as $plugin_path => $plugin ) {
			$plugin_version = $plugin['Version'];
			$plugin_author  = $plugin['Author'];

			$plugin_version_string       = __( 'No version or author information is available.' );
			$plugin_version_string_debug = 'author: (undefined), version: (undefined)';

			if ( ! empty( $plugin_version ) && ! empty( $plugin_author ) ) {
				/* translators: 1: Plugin version number. 2: Plugin author name. */
				$plugin_version_string       = sprintf( __( 'Version %1$s by %2$s' ), $plugin_version, $plugin_author );
				$plugin_version_string_debug = sprintf( 'version: %s, author: %s', $plugin_version, $plugin_author );
			} else {
				if ( ! empty( $plugin_author ) ) {
					/* translators: %s: Plugin author name. */
					$plugin_version_string       = sprintf( __( 'By %s' ), $plugin_author );
					$plugin_version_string_debug = sprintf( 'author: %s, version: (undefined)', $plugin_author );
				}

				if ( ! empty( $plugin_version ) ) {
					/* translators: %s: Plugin version number. */
					$plugin_version_string       = sprintf( __( 'Version %s' ), $plugin_version );
					$plugin_version_string_debug = sprintf( 'author: (undefined), version: %s', $plugin_version );
				}
			}

			$info['wp-mu-plugins']['fields'][ sanitize_text_field( $plugin['Name'] ) ] = array(
				'label' => $plugin['Name'],
				'value' => $plugin_version_string,
				'debug' => $plugin_version_string_debug,
			);
		}

		// List all available plugins.
		$plugins        = get_plugins();
		$plugin_updates = get_plugin_updates();
		$transient      = get_site_transient( 'update_plugins' );

		$auto_updates = array();

		$auto_updates_enabled = wp_is_auto_update_enabled_for_type( 'plugin' );

		if ( $auto_updates_enabled ) {
			$auto_updates = (array) get_site_option( 'auto_update_plugins', array() );
		}

		foreach ( $plugins as $plugin_path => $plugin ) {
			$plugin_part = ( is_plugin_active( $plugin_path ) ) ? 'wp-plugins-active' : 'wp-plugins-inactive';

			$plugin_version = $plugin['Version'];
			$plugin_author  = $plugin['Author'];

			$plugin_version_string       = __( 'No version or author information is available.' );
			$plugin_version_string_debug = 'author: (undefined), version: (undefined)';

			if ( ! empty( $plugin_version ) && ! empty( $plugin_author ) ) {
				/* translators: 1: Plugin version number. 2: Plugin author name. */
				$plugin_version_string       = sprintf( __( 'Version %1$s by %2$s' ), $plugin_version, $plugin_author );
				$plugin_version_string_debug = sprintf( 'version: %s, author: %s', $plugin_version, $plugin_author );
			} else {
				if ( ! empty( $plugin_author ) ) {
					/* translators: %s: Plugin author name. */
					$plugin_version_string       = sprintf( __( 'By %s' ), $plugin_author );
					$plugin_version_string_debug = sprintf( 'author: %s, version: (undefined)', $plugin_author );
				}

				if ( ! empty( $plugin_version ) ) {
					/* translators: %s: Plugin version number. */
					$plugin_version_string       = sprintf( __( 'Version %s' ), $plugin_version );
					$plugin_version_string_debug = sprintf( 'author: (undefined), version: %s', $plugin_version );
				}
			}

			if ( array_key_exists( $plugin_path, $plugin_updates ) ) {
				/* translators: %s: Latest plugin version number. */
				$plugin_version_string       .= ' ' . sprintf( __( '(Latest version: %s)' ), $plugin_updates[ $plugin_path ]->update->new_version );
				$plugin_version_string_debug .= sprintf( ' (latest version: %s)', $plugin_updates[ $plugin_path ]->update->new_version );
			}

			if ( $auto_updates_enabled ) {
				if ( isset( $transient->response[ $plugin_path ] ) ) {
					$item = $transient->response[ $plugin_path ];
				} elseif ( isset( $transient->no_update[ $plugin_path ] ) ) {
					$item = $transient->no_update[ $plugin_path ];
				} else {
					$item = array(
						'id'            => $plugin_path,
						'slug'          => '',
						'plugin'        => $plugin_path,
						'new_version'   => '',
						'url'           => '',
						'package'       => '',
						'icons'         => array(),
						'banners'       => array(),
						'banners_rtl'   => array(),
						'tested'        => '',
						'requires_php'  => '',
						'compatibility' => new stdClass(),
					);
					$item = array_merge( $item, array_intersect_key( $plugin, $item ) );
				}

				$auto_update_forced = wp_is_auto_update_forced_for_item( 'plugin', null, (object) $item );

				if ( ! is_null( $auto_update_forced ) ) {
					$enabled = $auto_update_forced;
				} else {
					$enabled = in_array( $plugin_path, $auto_updates, true );
				}

				if ( $enabled ) {
					$auto_updates_string = __( 'Auto-updates enabled' );
				} else {
					$auto_updates_string = __( 'Auto-updates disabled' );
				}

				/**
				 * Filters the text string of the auto-updates setting for each plugin in the Site Health debug data.
				 *
				 * @since 5.5.0
				 *
				 * @param string $auto_updates_string The string output for the auto-updates column.
				 * @param string $plugin_path         The path to the plugin file.
				 * @param array  $plugin              An array of plugin data.
				 * @param bool   $enabled             Whether auto-updates are enabled for this item.
				 */
				$auto_updates_string = apply_filters( 'plugin_auto_update_debug_string', $auto_updates_string, $plugin_path, $plugin, $enabled );

				$plugin_version_string       .= ' | ' . $auto_updates_string;
				$plugin_version_string_debug .= ', ' . $auto_updates_string;
			}

			$info[ $plugin_part ]['fields'][ sanitize_text_field( $plugin['Name'] ) ] = array(
				'label' => $plugin['Name'],
				'value' => $plugin_version_string,
				'debug' => $plugin_version_string_debug,
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
		$transient     = get_site_transient( 'update_themes' );

		$active_theme_version       = $active_theme->version;
		$active_theme_version_debug = $active_theme_version;

		$auto_updates         = array();
		$auto_updates_enabled = wp_is_auto_update_enabled_for_type( 'theme' );
		if ( $auto_updates_enabled ) {
			$auto_updates = (array) get_site_option( 'auto_update_themes', array() );
		}

		if ( array_key_exists( $active_theme->stylesheet, $theme_updates ) ) {
			$theme_update_new_version = $theme_updates[ $active_theme->stylesheet ]->update['new_version'];

			/* translators: %s: Latest theme version number. */
			$active_theme_version       .= ' ' . sprintf( __( '(Latest version: %s)' ), $theme_update_new_version );
			$active_theme_version_debug .= sprintf( ' (latest version: %s)', $theme_update_new_version );
		}

		$active_theme_author_uri = $active_theme->display( 'AuthorURI' );

		if ( $active_theme->parent_theme ) {
			$active_theme_parent_theme = sprintf(
				/* translators: 1: Theme name. 2: Theme slug. */
				__( '%1$s (%2$s)' ),
				$active_theme->parent_theme,
				$active_theme->template
			);
			$active_theme_parent_theme_debug = sprintf(
				'%s (%s)',
				$active_theme->parent_theme,
				$active_theme->template
			);
		} else {
			$active_theme_parent_theme       = __( 'None' );
			$active_theme_parent_theme_debug = 'none';
		}

		$info['wp-active-theme']['fields'] = array(
			'name'           => array(
				'label' => __( 'Name' ),
				'value' => sprintf(
					/* translators: 1: Theme name. 2: Theme slug. */
					__( '%1$s (%2$s)' ),
					$active_theme->name,
					$active_theme->stylesheet
				),
			),
			'version'        => array(
				'label' => __( 'Version' ),
				'value' => $active_theme_version,
				'debug' => $active_theme_version_debug,
			),
			'author'         => array(
				'label' => __( 'Author' ),
				'value' => wp_kses( $active_theme->author, array() ),
			),
			'author_website' => array(
				'label' => __( 'Author website' ),
				'value' => ( $active_theme_author_uri ? $active_theme_author_uri : __( 'Undefined' ) ),
				'debug' => ( $active_theme_author_uri ? $active_theme_author_uri : '(undefined)' ),
			),
			'parent_theme'   => array(
				'label' => __( 'Parent theme' ),
				'value' => $active_theme_parent_theme,
				'debug' => $active_theme_parent_theme_debug,
			),
			'theme_features' => array(
				'label' => __( 'Theme features' ),
				'value' => implode( ', ', $theme_features ),
			),
			'theme_path'     => array(
				'label' => __( 'Theme directory location' ),
				'value' => get_stylesheet_directory(),
			),
		);

		if ( $auto_updates_enabled ) {
			if ( isset( $transient->response[ $active_theme->stylesheet ] ) ) {
				$item = $transient->response[ $active_theme->stylesheet ];
			} elseif ( isset( $transient->no_update[ $active_theme->stylesheet ] ) ) {
				$item = $transient->no_update[ $active_theme->stylesheet ];
			} else {
				$item = array(
					'theme'        => $active_theme->stylesheet,
					'new_version'  => $active_theme->version,
					'url'          => '',
					'package'      => '',
					'requires'     => '',
					'requires_php' => '',
				);
			}

			$auto_update_forced = wp_is_auto_update_forced_for_item( 'theme', null, (object) $item );

			if ( ! is_null( $auto_update_forced ) ) {
				$enabled = $auto_update_forced;
			} else {
				$enabled = in_array( $active_theme->stylesheet, $auto_updates, true );
			}

			if ( $enabled ) {
				$auto_updates_string = __( 'Enabled' );
			} else {
				$auto_updates_string = __( 'Disabled' );
			}

			/** This filter is documented in wp-admin/includes/class-wp-debug-data.php */
			$auto_updates_string = apply_filters( 'theme_auto_update_debug_string', $auto_updates_string, $active_theme, $enabled );

			$info['wp-active-theme']['fields']['auto_update'] = array(
				'label' => __( 'Auto-updates' ),
				'value' => $auto_updates_string,
				'debug' => $auto_updates_string,
			);
		}

		$parent_theme = $active_theme->parent();

		if ( $parent_theme ) {
			$parent_theme_version       = $parent_theme->version;
			$parent_theme_version_debug = $parent_theme_version;

			if ( array_key_exists( $parent_theme->stylesheet, $theme_updates ) ) {
				$parent_theme_update_new_version = $theme_updates[ $parent_theme->stylesheet ]->update['new_version'];

				/* translators: %s: Latest theme version number. */
				$parent_theme_version       .= ' ' . sprintf( __( '(Latest version: %s)' ), $parent_theme_update_new_version );
				$parent_theme_version_debug .= sprintf( ' (latest version: %s)', $parent_theme_update_new_version );
			}

			$parent_theme_author_uri = $parent_theme->display( 'AuthorURI' );

			$info['wp-parent-theme']['fields'] = array(
				'name'           => array(
					'label' => __( 'Name' ),
					'value' => sprintf(
						/* translators: 1: Theme name. 2: Theme slug. */
						__( '%1$s (%2$s)' ),
						$parent_theme->name,
						$parent_theme->stylesheet
					),
				),
				'version'        => array(
					'label' => __( 'Version' ),
					'value' => $parent_theme_version,
					'debug' => $parent_theme_version_debug,
				),
				'author'         => array(
					'label' => __( 'Author' ),
					'value' => wp_kses( $parent_theme->author, array() ),
				),
				'author_website' => array(
					'label' => __( 'Author website' ),
					'value' => ( $parent_theme_author_uri ? $parent_theme_author_uri : __( 'Undefined' ) ),
					'debug' => ( $parent_theme_author_uri ? $parent_theme_author_uri : '(undefined)' ),
				),
				'theme_path'     => array(
					'label' => __( 'Theme directory location' ),
					'value' => get_template_directory(),
				),
			);

			if ( $auto_updates_enabled ) {
				if ( isset( $transient->response[ $parent_theme->stylesheet ] ) ) {
					$item = $transient->response[ $parent_theme->stylesheet ];
				} elseif ( isset( $transient->no_update[ $parent_theme->stylesheet ] ) ) {
					$item = $transient->no_update[ $parent_theme->stylesheet ];
				} else {
					$item = array(
						'theme'        => $parent_theme->stylesheet,
						'new_version'  => $parent_theme->version,
						'url'          => '',
						'package'      => '',
						'requires'     => '',
						'requires_php' => '',
					);
				}

				$auto_update_forced = wp_is_auto_update_forced_for_item( 'theme', null, (object) $item );

				if ( ! is_null( $auto_update_forced ) ) {
					$enabled = $auto_update_forced;
				} else {
					$enabled = in_array( $parent_theme->stylesheet, $auto_updates, true );
				}

				if ( $enabled ) {
					$parent_theme_auto_update_string = __( 'Enabled' );
				} else {
					$parent_theme_auto_update_string = __( 'Disabled' );
				}

				/** This filter is documented in wp-admin/includes/class-wp-debug-data.php */
				$parent_theme_auto_update_string = apply_filters( 'theme_auto_update_debug_string', $auto_updates_string, $parent_theme, $enabled );

				$info['wp-parent-theme']['fields']['auto_update'] = array(
					'label' => __( 'Auto-update' ),
					'value' => $parent_theme_auto_update_string,
					'debug' => $parent_theme_auto_update_string,
				);
			}
		}

		// Populate a list of all themes available in the install.
		$all_themes = wp_get_themes();

		foreach ( $all_themes as $theme_slug => $theme ) {
			// Exclude the currently active theme from the list of all themes.
			if ( $active_theme->stylesheet === $theme_slug ) {
				continue;
			}

			// Exclude the currently active parent theme from the list of all themes.
			if ( ! empty( $parent_theme ) && $parent_theme->stylesheet === $theme_slug ) {
				continue;
			}

			$theme_version = $theme->version;
			$theme_author  = $theme->author;

			// Sanitize.
			$theme_author = wp_kses( $theme_author, array() );

			$theme_version_string       = __( 'No version or author information is available.' );
			$theme_version_string_debug = 'undefined';

			if ( ! empty( $theme_version ) && ! empty( $theme_author ) ) {
				/* translators: 1: Theme version number. 2: Theme author name. */
				$theme_version_string       = sprintf( __( 'Version %1$s by %2$s' ), $theme_version, $theme_author );
				$theme_version_string_debug = sprintf( 'version: %s, author: %s', $theme_version, $theme_author );
			} else {
				if ( ! empty( $theme_author ) ) {
					/* translators: %s: Theme author name. */
					$theme_version_string       = sprintf( __( 'By %s' ), $theme_author );
					$theme_version_string_debug = sprintf( 'author: %s, version: (undefined)', $theme_author );
				}

				if ( ! empty( $theme_version ) ) {
					/* translators: %s: Theme version number. */
					$theme_version_string       = sprintf( __( 'Version %s' ), $theme_version );
					$theme_version_string_debug = sprintf( 'author: (undefined), version: %s', $theme_version );
				}
			}

			if ( array_key_exists( $theme_slug, $theme_updates ) ) {
				/* translators: %s: Latest theme version number. */
				$theme_version_string       .= ' ' . sprintf( __( '(Latest version: %s)' ), $theme_updates[ $theme_slug ]->update['new_version'] );
				$theme_version_string_debug .= sprintf( ' (latest version: %s)', $theme_updates[ $theme_slug ]->update['new_version'] );
			}

			if ( $auto_updates_enabled ) {
				if ( isset( $transient->response[ $theme_slug ] ) ) {
					$item = $transient->response[ $theme_slug ];
				} elseif ( isset( $transient->no_update[ $theme_slug ] ) ) {
					$item = $transient->no_update[ $theme_slug ];
				} else {
					$item = array(
						'theme'        => $theme_slug,
						'new_version'  => $theme->version,
						'url'          => '',
						'package'      => '',
						'requires'     => '',
						'requires_php' => '',
					);
				}

				$auto_update_forced = wp_is_auto_update_forced_for_item( 'theme', null, (object) $item );

				if ( ! is_null( $auto_update_forced ) ) {
					$enabled = $auto_update_forced;
				} else {
					$enabled = in_array( $theme_slug, $auto_updates, true );
				}

				if ( $enabled ) {
					$auto_updates_string = __( 'Auto-updates enabled' );
				} else {
					$auto_updates_string = __( 'Auto-updates disabled' );
				}

				/**
				 * Filters the text string of the auto-updates setting for each theme in the Site Health debug data.
				 *
				 * @since 5.5.0
				 *
				 * @param string   $auto_updates_string The string output for the auto-updates column.
				 * @param WP_Theme $theme               An object of theme data.
				 * @param bool     $enabled             Whether auto-updates are enabled for this item.
				 */
				$auto_updates_string = apply_filters( 'theme_auto_update_debug_string', $auto_updates_string, $theme, $enabled );

				$theme_version_string       .= ' | ' . $auto_updates_string;
				$theme_version_string_debug .= ', ' . $auto_updates_string;
			}

			$info['wp-themes-inactive']['fields'][ sanitize_text_field( $theme->name ) ] = array(
				'label' => sprintf(
					/* translators: 1: Theme name. 2: Theme slug. */
					__( '%1$s (%2$s)' ),
					$theme->name,
					$theme_slug
				),
				'value' => $theme_version_string,
				'debug' => $theme_version_string_debug,
			);
		}

		// Add more filesystem checks.
		if ( defined( 'WPMU_PLUGIN_DIR' ) && is_dir( WPMU_PLUGIN_DIR ) ) {
			$is_writable_wpmu_plugin_dir = wp_is_writable( WPMU_PLUGIN_DIR );

			$info['wp-filesystem']['fields']['mu-plugins'] = array(
				'label' => __( 'The must use plugins directory' ),
				'value' => ( $is_writable_wpmu_plugin_dir ? __( 'Writable' ) : __( 'Not writable' ) ),
				'debug' => ( $is_writable_wpmu_plugin_dir ? 'writable' : 'not writable' ),
			);
		}

		/**
		 * Add or modify the debug information.
		 *
		 * Plugin or themes may wish to introduce their own debug information without creating additional admin pages
		 * they can utilize this filter to introduce their own sections or add more data to existing sections.
		 *
		 * Array keys for sections added by core are all prefixed with `wp-`, plugins and themes should use their own slug as
		 * a prefix, both for consistency as well as avoiding key collisions. Note that the array keys are used as labels
		 * for the copied data.
		 *
		 * All strings are expected to be plain text except $description that can contain inline HTML tags (see below).
		 *
		 * @since 5.2.0
		 *
		 * @param array $args {
		 *     The debug information to be added to the core information page.
		 *
		 *     This is an associative multi-dimensional array, up to three levels deep. The topmost array holds the sections.
		 *     Each section has a `$fields` associative array (see below), and each `$value` in `$fields` can be
		 *     another associative array of name/value pairs when there is more structured data to display.
		 *
		 *     @type string  $label        The title for this section of the debug output.
		 *     @type string  $description  Optional. A description for your information section which may contain basic HTML
		 *                                 markup, inline tags only as it is outputted in a paragraph.
		 *     @type boolean $show_count   Optional. If set to `true` the amount of fields will be included in the title for
		 *                                 this section.
		 *     @type boolean $private      Optional. If set to `true` the section and all associated fields will be excluded
		 *                                 from the copied data.
		 *     @type array   $fields {
		 *         An associative array containing the data to be displayed.
		 *
		 *         @type string  $label    The label for this piece of information.
		 *         @type string  $value    The output that is displayed for this field. Text should be translated. Can be
		 *                                 an associative array that is displayed as name/value pairs.
		 *         @type string  $debug    Optional. The output that is used for this field when the user copies the data.
		 *                                 It should be more concise and not translated. If not set, the content of `$value` is used.
		 *                                 Note that the array keys are used as labels for the copied data.
		 *         @type boolean $private  Optional. If set to `true` the field will not be included in the copied data
		 *                                 allowing you to show, for example, API keys here.
		 *     }
		 * }
		 */
		$info = apply_filters( 'debug_information', $info );

		return $info;
	}

	/**
	 * Format the information gathered for debugging, in a manner suitable for copying to a forum or support ticket.
	 *
	 * @since 5.2.0
	 *
	 * @param array  $info_array Information gathered from the `WP_Debug_Data::debug_data` function.
	 * @param string $type       The data type to return, either 'info' or 'debug'.
	 * @return string The formatted data.
	 */
	public static function format( $info_array, $type ) {
		$return = "`\n";

		foreach ( $info_array as $section => $details ) {
			// Skip this section if there are no fields, or the section has been declared as private.
			if ( empty( $details['fields'] ) || ( isset( $details['private'] ) && $details['private'] ) ) {
				continue;
			}

			$section_label = 'debug' === $type ? $section : $details['label'];

			$return .= sprintf(
				"### %s%s ###\n\n",
				$section_label,
				( isset( $details['show_count'] ) && $details['show_count'] ? sprintf( ' (%d)', count( $details['fields'] ) ) : '' )
			);

			foreach ( $details['fields'] as $field_name => $field ) {
				if ( isset( $field['private'] ) && true === $field['private'] ) {
					continue;
				}

				if ( 'debug' === $type && isset( $field['debug'] ) ) {
					$debug_data = $field['debug'];
				} else {
					$debug_data = $field['value'];
				}

				// Can be array, one level deep only.
				if ( is_array( $debug_data ) ) {
					$value = '';

					foreach ( $debug_data as $sub_field_name => $sub_field_value ) {
						$value .= sprintf( "\n\t%s: %s", $sub_field_name, $sub_field_value );
					}
				} elseif ( is_bool( $debug_data ) ) {
					$value = $debug_data ? 'true' : 'false';
				} elseif ( empty( $debug_data ) && '0' !== $debug_data ) {
					$value = 'undefined';
				} else {
					$value = $debug_data;
				}

				if ( 'debug' === $type ) {
					$label = $field_name;
				} else {
					$label = $field['label'];
				}

				$return .= sprintf( "%s: %s\n", $label, $value );
			}

			$return .= "\n";
		}

		$return .= '`';

		return $return;
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

		return (int) $size;
	}

	/**
	 * Fetch the sizes of the WordPress directories: `wordpress` (ABSPATH), `plugins`, `themes`, and `uploads`.
	 * Intended to supplement the array returned by `WP_Debug_Data::debug_data()`.
	 *
	 * @since 5.2.0
	 *
	 * @return array The sizes of the directories, also the database size and total installation size.
	 */
	public static function get_sizes() {
		$size_db    = self::get_database_size();
		$upload_dir = wp_get_upload_dir();

		/*
		 * We will be using the PHP max execution time to prevent the size calculations
		 * from causing a timeout. The default value is 30 seconds, and some
		 * hosts do not allow you to read configuration values.
		 */
		if ( function_exists( 'ini_get' ) ) {
			$max_execution_time = ini_get( 'max_execution_time' );
		}

		// The max_execution_time defaults to 0 when PHP runs from cli.
		// We still want to limit it below.
		if ( empty( $max_execution_time ) ) {
			$max_execution_time = 30;
		}

		if ( $max_execution_time > 20 ) {
			// If the max_execution_time is set to lower than 20 seconds, reduce it a bit to prevent
			// edge-case timeouts that may happen after the size loop has finished running.
			$max_execution_time -= 2;
		}

		// Go through the various installation directories and calculate their sizes.
		// No trailing slashes.
		$paths = array(
			'wordpress_size' => untrailingslashit( ABSPATH ),
			'themes_size'    => get_theme_root(),
			'plugins_size'   => WP_PLUGIN_DIR,
			'uploads_size'   => $upload_dir['basedir'],
		);

		$exclude = $paths;
		unset( $exclude['wordpress_size'] );
		$exclude = array_values( $exclude );

		$size_total = 0;
		$all_sizes  = array();

		// Loop over all the directories we want to gather the sizes for.
		foreach ( $paths as $name => $path ) {
			$dir_size = null; // Default to timeout.
			$results  = array(
				'path' => $path,
				'raw'  => 0,
			);

			if ( microtime( true ) - WP_START_TIMESTAMP < $max_execution_time ) {
				if ( 'wordpress_size' === $name ) {
					$dir_size = recurse_dirsize( $path, $exclude, $max_execution_time );
				} else {
					$dir_size = recurse_dirsize( $path, null, $max_execution_time );
				}
			}

			if ( false === $dir_size ) {
				// Error reading.
				$results['size']  = __( 'The size cannot be calculated. The directory is not accessible. Usually caused by invalid permissions.' );
				$results['debug'] = 'not accessible';

				// Stop total size calculation.
				$size_total = null;
			} elseif ( null === $dir_size ) {
				// Timeout.
				$results['size']  = __( 'The directory size calculation has timed out. Usually caused by a very large number of sub-directories and files.' );
				$results['debug'] = 'timeout while calculating size';

				// Stop total size calculation.
				$size_total = null;
			} else {
				if ( null !== $size_total ) {
					$size_total += $dir_size;
				}

				$results['raw']   = $dir_size;
				$results['size']  = size_format( $dir_size, 2 );
				$results['debug'] = $results['size'] . " ({$dir_size} bytes)";
			}

			$all_sizes[ $name ] = $results;
		}

		if ( $size_db > 0 ) {
			$database_size = size_format( $size_db, 2 );

			$all_sizes['database_size'] = array(
				'raw'   => $size_db,
				'size'  => $database_size,
				'debug' => $database_size . " ({$size_db} bytes)",
			);
		} else {
			$all_sizes['database_size'] = array(
				'size'  => __( 'Not available' ),
				'debug' => 'not available',
			);
		}

		if ( null !== $size_total && $size_db > 0 ) {
			$total_size    = $size_total + $size_db;
			$total_size_mb = size_format( $total_size, 2 );

			$all_sizes['total_size'] = array(
				'raw'   => $total_size,
				'size'  => $total_size_mb,
				'debug' => $total_size_mb . " ({$total_size} bytes)",
			);
		} else {
			$all_sizes['total_size'] = array(
				'size'  => __( 'Total size is not available. Some errors were encountered when determining the size of your installation.' ),
				'debug' => 'not available',
			);
		}

		return $all_sizes;
	}
}
