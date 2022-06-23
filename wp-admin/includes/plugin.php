<?php
/**
 * WordPress Plugin Administration API
 *
 * @package WordPress
 * @subpackage Administration
 */

/**
 * Parses the plugin contents to retrieve plugin's metadata.
 *
 * All plugin headers must be on their own line. Plugin description must not have
 * any newlines, otherwise only parts of the description will be displayed.
 * The below is formatted for printing.
 *
 *     /*
 *     Plugin Name: Name of the plugin.
 *     Plugin URI: The home page of the plugin.
 *     Description: Plugin description.
 *     Author: Plugin author's name.
 *     Author URI: Link to the author's website.
 *     Version: Plugin version.
 *     Text Domain: Optional. Unique identifier, should be same as the one used in
 *          load_plugin_textdomain().
 *     Domain Path: Optional. Only useful if the translations are located in a
 *          folder above the plugin's base path. For example, if .mo files are
 *          located in the locale folder then Domain Path will be "/locale/" and
 *          must have the first slash. Defaults to the base folder the plugin is
 *          located in.
 *     Network: Optional. Specify "Network: true" to require that a plugin is activated
 *          across all sites in an installation. This will prevent a plugin from being
 *          activated on a single site when Multisite is enabled.
 *     Requires at least: Optional. Specify the minimum required WordPress version.
 *     Requires PHP: Optional. Specify the minimum required PHP version.
 *     * / # Remove the space to close comment.
 *
 * The first 8 KB of the file will be pulled in and if the plugin data is not
 * within that first 8 KB, then the plugin author should correct their plugin
 * and move the plugin data headers to the top.
 *
 * The plugin file is assumed to have permissions to allow for scripts to read
 * the file. This is not checked however and the file is only opened for
 * reading.
 *
 * @since 1.5.0
 * @since 5.3.0 Added support for `Requires at least` and `Requires PHP` headers.
 * @since 5.8.0 Added support for `Update URI` header.
 *
 * @param string $plugin_file Absolute path to the main plugin file.
 * @param bool   $markup      Optional. If the returned data should have HTML markup applied.
 *                            Default true.
 * @param bool   $translate   Optional. If the returned data should be translated. Default true.
 * @return array {
 *     Plugin data. Values will be empty if not supplied by the plugin.
 *
 *     @type string $Name        Name of the plugin. Should be unique.
 *     @type string $PluginURI   Plugin URI.
 *     @type string $Version     Plugin version.
 *     @type string $Description Plugin description.
 *     @type string $Author      Plugin author's name.
 *     @type string $AuthorURI   Plugin author's website address (if set).
 *     @type string $TextDomain  Plugin textdomain.
 *     @type string $DomainPath  Plugin's relative directory path to .mo files.
 *     @type bool   $Network     Whether the plugin can only be activated network-wide.
 *     @type string $RequiresWP  Minimum required version of WordPress.
 *     @type string $RequiresPHP Minimum required version of PHP.
 *     @type string $UpdateURI   ID of the plugin for update purposes, should be a URI.
 *     @type string $Title       Title of the plugin and link to the plugin's site (if set).
 *     @type string $AuthorName  Plugin author's name.
 * }
 */
function get_plugin_data( $plugin_file, $markup = true, $translate = true ) {

	$default_headers = array(
		'Name'        => 'Plugin Name',
		'PluginURI'   => 'Plugin URI',
		'Version'     => 'Version',
		'Description' => 'Description',
		'Author'      => 'Author',
		'AuthorURI'   => 'Author URI',
		'TextDomain'  => 'Text Domain',
		'DomainPath'  => 'Domain Path',
		'Network'     => 'Network',
		'RequiresWP'  => 'Requires at least',
		'RequiresPHP' => 'Requires PHP',
		'UpdateURI'   => 'Update URI',
		// Site Wide Only is deprecated in favor of Network.
		'_sitewide'   => 'Site Wide Only',
	);

	$plugin_data = get_file_data( $plugin_file, $default_headers, 'plugin' );

	// Site Wide Only is the old header for Network.
	if ( ! $plugin_data['Network'] && $plugin_data['_sitewide'] ) {
		/* translators: 1: Site Wide Only: true, 2: Network: true */
		_deprecated_argument( __FUNCTION__, '3.0.0', sprintf( __( 'The %1$s plugin header is deprecated. Use %2$s instead.' ), '<code>Site Wide Only: true</code>', '<code>Network: true</code>' ) );
		$plugin_data['Network'] = $plugin_data['_sitewide'];
	}
	$plugin_data['Network'] = ( 'true' === strtolower( $plugin_data['Network'] ) );
	unset( $plugin_data['_sitewide'] );

	// If no text domain is defined fall back to the plugin slug.
	if ( ! $plugin_data['TextDomain'] ) {
		$plugin_slug = dirname( plugin_basename( $plugin_file ) );
		if ( '.' !== $plugin_slug && false === strpos( $plugin_slug, '/' ) ) {
			$plugin_data['TextDomain'] = $plugin_slug;
		}
	}

	if ( $markup || $translate ) {
		$plugin_data = _get_plugin_data_markup_translate( $plugin_file, $plugin_data, $markup, $translate );
	} else {
		$plugin_data['Title']      = $plugin_data['Name'];
		$plugin_data['AuthorName'] = $plugin_data['Author'];
	}

	return $plugin_data;
}

/**
 * Sanitizes plugin data, optionally adds markup, optionally translates.
 *
 * @since 2.7.0
 *
 * @see get_plugin_data()
 *
 * @access private
 *
 * @param string $plugin_file Path to the main plugin file.
 * @param array  $plugin_data An array of plugin data. See `get_plugin_data()`.
 * @param bool   $markup      Optional. If the returned data should have HTML markup applied.
 *                            Default true.
 * @param bool   $translate   Optional. If the returned data should be translated. Default true.
 * @return array Plugin data. Values will be empty if not supplied by the plugin.
 *               See get_plugin_data() for the list of possible values.
 */
function _get_plugin_data_markup_translate( $plugin_file, $plugin_data, $markup = true, $translate = true ) {

	// Sanitize the plugin filename to a WP_PLUGIN_DIR relative path.
	$plugin_file = plugin_basename( $plugin_file );

	// Translate fields.
	if ( $translate ) {
		$textdomain = $plugin_data['TextDomain'];
		if ( $textdomain ) {
			if ( ! is_textdomain_loaded( $textdomain ) ) {
				if ( $plugin_data['DomainPath'] ) {
					load_plugin_textdomain( $textdomain, false, dirname( $plugin_file ) . $plugin_data['DomainPath'] );
				} else {
					load_plugin_textdomain( $textdomain, false, dirname( $plugin_file ) );
				}
			}
		} elseif ( 'hello.php' === basename( $plugin_file ) ) {
			$textdomain = 'default';
		}
		if ( $textdomain ) {
			foreach ( array( 'Name', 'PluginURI', 'Description', 'Author', 'AuthorURI', 'Version' ) as $field ) {
				if ( ! empty( $plugin_data[ $field ] ) ) {
					// phpcs:ignore WordPress.WP.I18n.LowLevelTranslationFunction,WordPress.WP.I18n.NonSingularStringLiteralText,WordPress.WP.I18n.NonSingularStringLiteralDomain
					$plugin_data[ $field ] = translate( $plugin_data[ $field ], $textdomain );
				}
			}
		}
	}

	// Sanitize fields.
	$allowed_tags_in_links = array(
		'abbr'    => array( 'title' => true ),
		'acronym' => array( 'title' => true ),
		'code'    => true,
		'em'      => true,
		'strong'  => true,
	);

	$allowed_tags      = $allowed_tags_in_links;
	$allowed_tags['a'] = array(
		'href'  => true,
		'title' => true,
	);

	// Name is marked up inside <a> tags. Don't allow these.
	// Author is too, but some plugins have used <a> here (omitting Author URI).
	$plugin_data['Name']   = wp_kses( $plugin_data['Name'], $allowed_tags_in_links );
	$plugin_data['Author'] = wp_kses( $plugin_data['Author'], $allowed_tags );

	$plugin_data['Description'] = wp_kses( $plugin_data['Description'], $allowed_tags );
	$plugin_data['Version']     = wp_kses( $plugin_data['Version'], $allowed_tags );

	$plugin_data['PluginURI'] = esc_url( $plugin_data['PluginURI'] );
	$plugin_data['AuthorURI'] = esc_url( $plugin_data['AuthorURI'] );

	$plugin_data['Title']      = $plugin_data['Name'];
	$plugin_data['AuthorName'] = $plugin_data['Author'];

	// Apply markup.
	if ( $markup ) {
		if ( $plugin_data['PluginURI'] && $plugin_data['Name'] ) {
			$plugin_data['Title'] = '<a href="' . $plugin_data['PluginURI'] . '">' . $plugin_data['Name'] . '</a>';
		}

		if ( $plugin_data['AuthorURI'] && $plugin_data['Author'] ) {
			$plugin_data['Author'] = '<a href="' . $plugin_data['AuthorURI'] . '">' . $plugin_data['Author'] . '</a>';
		}

		$plugin_data['Description'] = wptexturize( $plugin_data['Description'] );

		if ( $plugin_data['Author'] ) {
			$plugin_data['Description'] .= sprintf(
				/* translators: %s: Plugin author. */
				' <cite>' . __( 'By %s.' ) . '</cite>',
				$plugin_data['Author']
			);
		}
	}

	return $plugin_data;
}

/**
 * Gets a list of a plugin's files.
 *
 * @since 2.8.0
 *
 * @param string $plugin Path to the plugin file relative to the plugins directory.
 * @return string[] Array of file names relative to the plugin root.
 */
function get_plugin_files( $plugin ) {
	$plugin_file = WP_PLUGIN_DIR . '/' . $plugin;
	$dir         = dirname( $plugin_file );

	$plugin_files = array( plugin_basename( $plugin_file ) );

	if ( is_dir( $dir ) && WP_PLUGIN_DIR !== $dir ) {

		/**
		 * Filters the array of excluded directories and files while scanning the folder.
		 *
		 * @since 4.9.0
		 *
		 * @param string[] $exclusions Array of excluded directories and files.
		 */
		$exclusions = (array) apply_filters( 'plugin_files_exclusions', array( 'CVS', 'node_modules', 'vendor', 'bower_components' ) );

		$list_files = list_files( $dir, 100, $exclusions );
		$list_files = array_map( 'plugin_basename', $list_files );

		$plugin_files = array_merge( $plugin_files, $list_files );
		$plugin_files = array_values( array_unique( $plugin_files ) );
	}

	return $plugin_files;
}

/**
 * Checks the plugins directory and retrieve all plugin files with plugin data.
 *
 * WordPress only supports plugin files in the base plugins directory
 * (wp-content/plugins) and in one directory above the plugins directory
 * (wp-content/plugins/my-plugin). The file it looks for has the plugin data
 * and must be found in those two locations. It is recommended to keep your
 * plugin files in their own directories.
 *
 * The file with the plugin data is the file that will be included and therefore
 * needs to have the main execution for the plugin. This does not mean
 * everything must be contained in the file and it is recommended that the file
 * be split for maintainability. Keep everything in one file for extreme
 * optimization purposes.
 *
 * @since 1.5.0
 *
 * @param string $plugin_folder Optional. Relative path to single plugin folder.
 * @return array[] Array of arrays of plugin data, keyed by plugin file name. See `get_plugin_data()`.
 */
function get_plugins( $plugin_folder = '' ) {

	$cache_plugins = wp_cache_get( 'plugins', 'plugins' );
	if ( ! $cache_plugins ) {
		$cache_plugins = array();
	}

	if ( isset( $cache_plugins[ $plugin_folder ] ) ) {
		return $cache_plugins[ $plugin_folder ];
	}

	$wp_plugins  = array();
	$plugin_root = WP_PLUGIN_DIR;
	if ( ! empty( $plugin_folder ) ) {
		$plugin_root .= $plugin_folder;
	}

	// Files in wp-content/plugins directory.
	$plugins_dir  = @opendir( $plugin_root );
	$plugin_files = array();

	if ( $plugins_dir ) {
		while ( ( $file = readdir( $plugins_dir ) ) !== false ) {
			if ( '.' === substr( $file, 0, 1 ) ) {
				continue;
			}

			if ( is_dir( $plugin_root . '/' . $file ) ) {
				$plugins_subdir = @opendir( $plugin_root . '/' . $file );

				if ( $plugins_subdir ) {
					while ( ( $subfile = readdir( $plugins_subdir ) ) !== false ) {
						if ( '.' === substr( $subfile, 0, 1 ) ) {
							continue;
						}

						if ( '.php' === substr( $subfile, -4 ) ) {
							$plugin_files[] = "$file/$subfile";
						}
					}

					closedir( $plugins_subdir );
				}
			} else {
				if ( '.php' === substr( $file, -4 ) ) {
					$plugin_files[] = $file;
				}
			}
		}

		closedir( $plugins_dir );
	}

	if ( empty( $plugin_files ) ) {
		return $wp_plugins;
	}

	foreach ( $plugin_files as $plugin_file ) {
		if ( ! is_readable( "$plugin_root/$plugin_file" ) ) {
			continue;
		}

		// Do not apply markup/translate as it will be cached.
		$plugin_data = get_plugin_data( "$plugin_root/$plugin_file", false, false );

		if ( empty( $plugin_data['Name'] ) ) {
			continue;
		}

		$wp_plugins[ plugin_basename( $plugin_file ) ] = $plugin_data;
	}

	uasort( $wp_plugins, '_sort_uname_callback' );

	$cache_plugins[ $plugin_folder ] = $wp_plugins;
	wp_cache_set( 'plugins', $cache_plugins, 'plugins' );

	return $wp_plugins;
}

/**
 * Checks the mu-plugins directory and retrieve all mu-plugin files with any plugin data.
 *
 * WordPress only includes mu-plugin files in the base mu-plugins directory (wp-content/mu-plugins).
 *
 * @since 3.0.0
 * @return array[] Array of arrays of mu-plugin data, keyed by plugin file name. See `get_plugin_data()`.
 */
function get_mu_plugins() {
	$wp_plugins   = array();
	$plugin_files = array();

	if ( ! is_dir( WPMU_PLUGIN_DIR ) ) {
		return $wp_plugins;
	}

	// Files in wp-content/mu-plugins directory.
	$plugins_dir = @opendir( WPMU_PLUGIN_DIR );
	if ( $plugins_dir ) {
		while ( ( $file = readdir( $plugins_dir ) ) !== false ) {
			if ( '.php' === substr( $file, -4 ) ) {
				$plugin_files[] = $file;
			}
		}
	} else {
		return $wp_plugins;
	}

	closedir( $plugins_dir );

	if ( empty( $plugin_files ) ) {
		return $wp_plugins;
	}

	foreach ( $plugin_files as $plugin_file ) {
		if ( ! is_readable( WPMU_PLUGIN_DIR . "/$plugin_file" ) ) {
			continue;
		}

		// Do not apply markup/translate as it will be cached.
		$plugin_data = get_plugin_data( WPMU_PLUGIN_DIR . "/$plugin_file", false, false );

		if ( empty( $plugin_data['Name'] ) ) {
			$plugin_data['Name'] = $plugin_file;
		}

		$wp_plugins[ $plugin_file ] = $plugin_data;
	}

	if ( isset( $wp_plugins['index.php'] ) && filesize( WPMU_PLUGIN_DIR . '/index.php' ) <= 30 ) {
		// Silence is golden.
		unset( $wp_plugins['index.php'] );
	}

	uasort( $wp_plugins, '_sort_uname_callback' );

	return $wp_plugins;
}

/**
 * Declares a callback to sort array by a 'Name' key.
 *
 * @since 3.1.0
 *
 * @access private
 *
 * @param array $a array with 'Name' key.
 * @param array $b array with 'Name' key.
 * @return int Return 0 or 1 based on two string comparison.
 */
function _sort_uname_callback( $a, $b ) {
	return strnatcasecmp( $a['Name'], $b['Name'] );
}

/**
 * Checks the wp-content directory and retrieve all drop-ins with any plugin data.
 *
 * @since 3.0.0
 * @return array[] Array of arrays of dropin plugin data, keyed by plugin file name. See `get_plugin_data()`.
 */
function get_dropins() {
	$dropins      = array();
	$plugin_files = array();

	$_dropins = _get_dropins();

	// Files in wp-content directory.
	$plugins_dir = @opendir( WP_CONTENT_DIR );
	if ( $plugins_dir ) {
		while ( ( $file = readdir( $plugins_dir ) ) !== false ) {
			if ( isset( $_dropins[ $file ] ) ) {
				$plugin_files[] = $file;
			}
		}
	} else {
		return $dropins;
	}

	closedir( $plugins_dir );

	if ( empty( $plugin_files ) ) {
		return $dropins;
	}

	foreach ( $plugin_files as $plugin_file ) {
		if ( ! is_readable( WP_CONTENT_DIR . "/$plugin_file" ) ) {
			continue;
		}

		// Do not apply markup/translate as it will be cached.
		$plugin_data = get_plugin_data( WP_CONTENT_DIR . "/$plugin_file", false, false );

		if ( empty( $plugin_data['Name'] ) ) {
			$plugin_data['Name'] = $plugin_file;
		}

		$dropins[ $plugin_file ] = $plugin_data;
	}

	uksort( $dropins, 'strnatcasecmp' );

	return $dropins;
}

/**
 * Returns drop-ins that WordPress uses.
 *
 * Includes Multisite drop-ins only when is_multisite()
 *
 * @since 3.0.0
 * @return array[] Key is file name. The value is an array, with the first value the
 *  purpose of the drop-in and the second value the name of the constant that must be
 *  true for the drop-in to be used, or true if no constant is required.
 */
function _get_dropins() {
	$dropins = array(
		'advanced-cache.php'      => array( __( 'Advanced caching plugin.' ), 'WP_CACHE' ),  // WP_CACHE
		'db.php'                  => array( __( 'Custom database class.' ), true ),          // Auto on load.
		'db-error.php'            => array( __( 'Custom database error message.' ), true ),  // Auto on error.
		'install.php'             => array( __( 'Custom installation script.' ), true ),     // Auto on installation.
		'maintenance.php'         => array( __( 'Custom maintenance message.' ), true ),     // Auto on maintenance.
		'object-cache.php'        => array( __( 'External object cache.' ), true ),          // Auto on load.
		'php-error.php'           => array( __( 'Custom PHP error message.' ), true ),       // Auto on error.
		'fatal-error-handler.php' => array( __( 'Custom PHP fatal error handler.' ), true ), // Auto on error.
	);

	if ( is_multisite() ) {
		$dropins['sunrise.php']        = array( __( 'Executed before Multisite is loaded.' ), 'SUNRISE' ); // SUNRISE
		$dropins['blog-deleted.php']   = array( __( 'Custom site deleted message.' ), true );   // Auto on deleted blog.
		$dropins['blog-inactive.php']  = array( __( 'Custom site inactive message.' ), true );  // Auto on inactive blog.
		$dropins['blog-suspended.php'] = array( __( 'Custom site suspended message.' ), true ); // Auto on archived or spammed blog.
	}

	return $dropins;
}

/**
 * Determines whether a plugin is active.
 *
 * Only plugins installed in the plugins/ folder can be active.
 *
 * Plugins in the mu-plugins/ folder can't be "activated," so this function will
 * return false for those plugins.
 *
 * For more information on this and similar theme functions, check out
 * the {@link https://developer.wordpress.org/themes/basics/conditional-tags/
 * Conditional Tags} article in the Theme Developer Handbook.
 *
 * @since 2.5.0
 *
 * @param string $plugin Path to the plugin file relative to the plugins directory.
 * @return bool True, if in the active plugins list. False, not in the list.
 */
function is_plugin_active( $plugin ) {
	return in_array( $plugin, (array) get_option( 'active_plugins', array() ), true ) || is_plugin_active_for_network( $plugin );
}

/**
 * Determines whether the plugin is inactive.
 *
 * Reverse of is_plugin_active(). Used as a callback.
 *
 * For more information on this and similar theme functions, check out
 * the {@link https://developer.wordpress.org/themes/basics/conditional-tags/
 * Conditional Tags} article in the Theme Developer Handbook.
 *
 * @since 3.1.0
 *
 * @see is_plugin_active()
 *
 * @param string $plugin Path to the plugin file relative to the plugins directory.
 * @return bool True if inactive. False if active.
 */
function is_plugin_inactive( $plugin ) {
	return ! is_plugin_active( $plugin );
}

/**
 * Determines whether the plugin is active for the entire network.
 *
 * Only plugins installed in the plugins/ folder can be active.
 *
 * Plugins in the mu-plugins/ folder can't be "activated," so this function will
 * return false for those plugins.
 *
 * For more information on this and similar theme functions, check out
 * the {@link https://developer.wordpress.org/themes/basics/conditional-tags/
 * Conditional Tags} article in the Theme Developer Handbook.
 *
 * @since 3.0.0
 *
 * @param string $plugin Path to the plugin file relative to the plugins directory.
 * @return bool True if active for the network, otherwise false.
 */
function is_plugin_active_for_network( $plugin ) {
	if ( ! is_multisite() ) {
		return false;
	}

	$plugins = get_site_option( 'active_sitewide_plugins' );
	if ( isset( $plugins[ $plugin ] ) ) {
		return true;
	}

	return false;
}

/**
 * Checks for "Network: true" in the plugin header to see if this should
 * be activated only as a network wide plugin. The plugin would also work
 * when Multisite is not enabled.
 *
 * Checks for "Site Wide Only: true" for backward compatibility.
 *
 * @since 3.0.0
 *
 * @param string $plugin Path to the plugin file relative to the plugins directory.
 * @return bool True if plugin is network only, false otherwise.
 */
function is_network_only_plugin( $plugin ) {
	$plugin_data = get_plugin_data( WP_PLUGIN_DIR . '/' . $plugin );
	if ( $plugin_data ) {
		return $plugin_data['Network'];
	}
	return false;
}

/**
 * Attempts activation of plugin in a "sandbox" and redirects on success.
 *
 * A plugin that is already activated will not attempt to be activated again.
 *
 * The way it works is by setting the redirection to the error before trying to
 * include the plugin file. If the plugin fails, then the redirection will not
 * be overwritten with the success message. Also, the options will not be
 * updated and the activation hook will not be called on plugin error.
 *
 * It should be noted that in no way the below code will actually prevent errors
 * within the file. The code should not be used elsewhere to replicate the
 * "sandbox", which uses redirection to work.
 * {@source 13 1}
 *
 * If any errors are found or text is outputted, then it will be captured to
 * ensure that the success redirection will update the error redirection.
 *
 * @since 2.5.0
 * @since 5.2.0 Test for WordPress version and PHP version compatibility.
 *
 * @param string $plugin       Path to the plugin file relative to the plugins directory.
 * @param string $redirect     Optional. URL to redirect to.
 * @param bool   $network_wide Optional. Whether to enable the plugin for all sites in the network
 *                             or just the current site. Multisite only. Default false.
 * @param bool   $silent       Optional. Whether to prevent calling activation hooks. Default false.
 * @return null|WP_Error Null on success, WP_Error on invalid file.
 */
function activate_plugin( $plugin, $redirect = '', $network_wide = false, $silent = false ) {
	$plugin = plugin_basename( trim( $plugin ) );

	if ( is_multisite() && ( $network_wide || is_network_only_plugin( $plugin ) ) ) {
		$network_wide        = true;
		$current             = get_site_option( 'active_sitewide_plugins', array() );
		$_GET['networkwide'] = 1; // Back compat for plugins looking for this value.
	} else {
		$current = get_option( 'active_plugins', array() );
	}

	$valid = validate_plugin( $plugin );
	if ( is_wp_error( $valid ) ) {
		return $valid;
	}

	$requirements = validate_plugin_requirements( $plugin );
	if ( is_wp_error( $requirements ) ) {
		return $requirements;
	}

	if ( $network_wide && ! isset( $current[ $plugin ] )
		|| ! $network_wide && ! in_array( $plugin, $current, true )
	) {
		if ( ! empty( $redirect ) ) {
			// We'll override this later if the plugin can be included without fatal error.
			wp_redirect( add_query_arg( '_error_nonce', wp_create_nonce( 'plugin-activation-error_' . $plugin ), $redirect ) );
		}

		ob_start();

		// Load the plugin to test whether it throws any errors.
		plugin_sandbox_scrape( $plugin );

		if ( ! $silent ) {
			/**
			 * Fires before a plugin is activated.
			 *
			 * If a plugin is silently activated (such as during an update),
			 * this hook does not fire.
			 *
			 * @since 2.9.0
			 *
			 * @param string $plugin       Path to the plugin file relative to the plugins directory.
			 * @param bool   $network_wide Whether to enable the plugin for all sites in the network
			 *                             or just the current site. Multisite only. Default false.
			 */
			do_action( 'activate_plugin', $plugin, $network_wide );

			/**
			 * Fires as a specific plugin is being activated.
			 *
			 * This hook is the "activation" hook used internally by register_activation_hook().
			 * The dynamic portion of the hook name, `$plugin`, refers to the plugin basename.
			 *
			 * If a plugin is silently activated (such as during an update), this hook does not fire.
			 *
			 * @since 2.0.0
			 *
			 * @param bool $network_wide Whether to enable the plugin for all sites in the network
			 *                           or just the current site. Multisite only. Default false.
			 */
			do_action( "activate_{$plugin}", $network_wide );
		}

		if ( $network_wide ) {
			$current            = get_site_option( 'active_sitewide_plugins', array() );
			$current[ $plugin ] = time();
			update_site_option( 'active_sitewide_plugins', $current );
		} else {
			$current   = get_option( 'active_plugins', array() );
			$current[] = $plugin;
			sort( $current );
			update_option( 'active_plugins', $current );
		}

		if ( ! $silent ) {
			/**
			 * Fires after a plugin has been activated.
			 *
			 * If a plugin is silently activated (such as during an update),
			 * this hook does not fire.
			 *
			 * @since 2.9.0
			 *
			 * @param string $plugin       Path to the plugin file relative to the plugins directory.
			 * @param bool   $network_wide Whether to enable the plugin for all sites in the network
			 *                             or just the current site. Multisite only. Default false.
			 */
			do_action( 'activated_plugin', $plugin, $network_wide );
		}

		if ( ob_get_length() > 0 ) {
			$output = ob_get_clean();
			return new WP_Error( 'unexpected_output', __( 'The plugin generated unexpected output.' ), $output );
		}

		ob_end_clean();
	}

	return null;
}

/**
 * Deactivates a single plugin or multiple plugins.
 *
 * The deactivation hook is disabled by the plugin upgrader by using the $silent
 * parameter.
 *
 * @since 2.5.0
 *
 * @param string|string[] $plugins      Single plugin or list of plugins to deactivate.
 * @param bool            $silent       Prevent calling deactivation hooks. Default false.
 * @param bool|null       $network_wide Whether to deactivate the plugin for all sites in the network.
 *                                      A value of null will deactivate plugins for both the network
 *                                      and the current site. Multisite only. Default null.
 */
function deactivate_plugins( $plugins, $silent = false, $network_wide = null ) {
	if ( is_multisite() ) {
		$network_current = get_site_option( 'active_sitewide_plugins', array() );
	}
	$current    = get_option( 'active_plugins', array() );
	$do_blog    = false;
	$do_network = false;

	foreach ( (array) $plugins as $plugin ) {
		$plugin = plugin_basename( trim( $plugin ) );
		if ( ! is_plugin_active( $plugin ) ) {
			continue;
		}

		$network_deactivating = ( false !== $network_wide ) && is_plugin_active_for_network( $plugin );

		if ( ! $silent ) {
			/**
			 * Fires before a plugin is deactivated.
			 *
			 * If a plugin is silently deactivated (such as during an update),
			 * this hook does not fire.
			 *
			 * @since 2.9.0
			 *
			 * @param string $plugin               Path to the plugin file relative to the plugins directory.
			 * @param bool   $network_deactivating Whether the plugin is deactivated for all sites in the network
			 *                                     or just the current site. Multisite only. Default false.
			 */
			do_action( 'deactivate_plugin', $plugin, $network_deactivating );
		}

		if ( false !== $network_wide ) {
			if ( is_plugin_active_for_network( $plugin ) ) {
				$do_network = true;
				unset( $network_current[ $plugin ] );
			} elseif ( $network_wide ) {
				continue;
			}
		}

		if ( true !== $network_wide ) {
			$key = array_search( $plugin, $current, true );
			if ( false !== $key ) {
				$do_blog = true;
				unset( $current[ $key ] );
			}
		}

		if ( $do_blog && wp_is_recovery_mode() ) {
			list( $extension ) = explode( '/', $plugin );
			wp_paused_plugins()->delete( $extension );
		}

		if ( ! $silent ) {
			/**
			 * Fires as a specific plugin is being deactivated.
			 *
			 * This hook is the "deactivation" hook used internally by register_deactivation_hook().
			 * The dynamic portion of the hook name, `$plugin`, refers to the plugin basename.
			 *
			 * If a plugin is silently deactivated (such as during an update), this hook does not fire.
			 *
			 * @since 2.0.0
			 *
			 * @param bool $network_deactivating Whether the plugin is deactivated for all sites in the network
			 *                                   or just the current site. Multisite only. Default false.
			 */
			do_action( "deactivate_{$plugin}", $network_deactivating );

			/**
			 * Fires after a plugin is deactivated.
			 *
			 * If a plugin is silently deactivated (such as during an update),
			 * this hook does not fire.
			 *
			 * @since 2.9.0
			 *
			 * @param string $plugin               Path to the plugin file relative to the plugins directory.
			 * @param bool   $network_deactivating Whether the plugin is deactivated for all sites in the network
			 *                                     or just the current site. Multisite only. Default false.
			 */
			do_action( 'deactivated_plugin', $plugin, $network_deactivating );
		}
	}

	if ( $do_blog ) {
		update_option( 'active_plugins', $current );
	}
	if ( $do_network ) {
		update_site_option( 'active_sitewide_plugins', $network_current );
	}
}

/**
 * Activates multiple plugins.
 *
 * When WP_Error is returned, it does not mean that one of the plugins had
 * errors. It means that one or more of the plugin file paths were invalid.
 *
 * The execution will be halted as soon as one of the plugins has an error.
 *
 * @since 2.6.0
 *
 * @param string|string[] $plugins      Single plugin or list of plugins to activate.
 * @param string          $redirect     Redirect to page after successful activation.
 * @param bool            $network_wide Whether to enable the plugin for all sites in the network.
 *                                      Default false.
 * @param bool            $silent       Prevent calling activation hooks. Default false.
 * @return bool|WP_Error True when finished or WP_Error if there were errors during a plugin activation.
 */
function activate_plugins( $plugins, $redirect = '', $network_wide = false, $silent = false ) {
	if ( ! is_array( $plugins ) ) {
		$plugins = array( $plugins );
	}

	$errors = array();
	foreach ( $plugins as $plugin ) {
		if ( ! empty( $redirect ) ) {
			$redirect = add_query_arg( 'plugin', $plugin, $redirect );
		}
		$result = activate_plugin( $plugin, $redirect, $network_wide, $silent );
		if ( is_wp_error( $result ) ) {
			$errors[ $plugin ] = $result;
		}
	}

	if ( ! empty( $errors ) ) {
		return new WP_Error( 'plugins_invalid', __( 'One of the plugins is invalid.' ), $errors );
	}

	return true;
}

/**
 * Removes directory and files of a plugin for a list of plugins.
 *
 * @since 2.6.0
 *
 * @global WP_Filesystem_Base $wp_filesystem WordPress filesystem subclass.
 *
 * @param string[] $plugins    List of plugin paths to delete, relative to the plugins directory.
 * @param string   $deprecated Not used.
 * @return bool|null|WP_Error True on success, false if `$plugins` is empty, `WP_Error` on failure.
 *                            `null` if filesystem credentials are required to proceed.
 */
function delete_plugins( $plugins, $deprecated = '' ) {
	global $wp_filesystem;

	if ( empty( $plugins ) ) {
		return false;
	}

	$checked = array();
	foreach ( $plugins as $plugin ) {
		$checked[] = 'checked[]=' . $plugin;
	}

	$url = wp_nonce_url( 'plugins.php?action=delete-selected&verify-delete=1&' . implode( '&', $checked ), 'bulk-plugins' );

	ob_start();
	$credentials = request_filesystem_credentials( $url );
	$data        = ob_get_clean();

	if ( false === $credentials ) {
		if ( ! empty( $data ) ) {
			require_once ABSPATH . 'wp-admin/admin-header.php';
			echo $data;
			require_once ABSPATH . 'wp-admin/admin-footer.php';
			exit;
		}
		return;
	}

	if ( ! WP_Filesystem( $credentials ) ) {
		ob_start();
		// Failed to connect. Error and request again.
		request_filesystem_credentials( $url, '', true );
		$data = ob_get_clean();

		if ( ! empty( $data ) ) {
			require_once ABSPATH . 'wp-admin/admin-header.php';
			echo $data;
			require_once ABSPATH . 'wp-admin/admin-footer.php';
			exit;
		}
		return;
	}

	if ( ! is_object( $wp_filesystem ) ) {
		return new WP_Error( 'fs_unavailable', __( 'Could not access filesystem.' ) );
	}

	if ( is_wp_error( $wp_filesystem->errors ) && $wp_filesystem->errors->has_errors() ) {
		return new WP_Error( 'fs_error', __( 'Filesystem error.' ), $wp_filesystem->errors );
	}

	// Get the base plugin folder.
	$plugins_dir = $wp_filesystem->wp_plugins_dir();
	if ( empty( $plugins_dir ) ) {
		return new WP_Error( 'fs_no_plugins_dir', __( 'Unable to locate WordPress plugin directory.' ) );
	}

	$plugins_dir = trailingslashit( $plugins_dir );

	$plugin_translations = wp_get_installed_translations( 'plugins' );

	$errors = array();

	foreach ( $plugins as $plugin_file ) {
		// Run Uninstall hook.
		if ( is_uninstallable_plugin( $plugin_file ) ) {
			uninstall_plugin( $plugin_file );
		}

		/**
		 * Fires immediately before a plugin deletion attempt.
		 *
		 * @since 4.4.0
		 *
		 * @param string $plugin_file Path to the plugin file relative to the plugins directory.
		 */
		do_action( 'delete_plugin', $plugin_file );

		$this_plugin_dir = trailingslashit( dirname( $plugins_dir . $plugin_file ) );

		// If plugin is in its own directory, recursively delete the directory.
		// Base check on if plugin includes directory separator AND that it's not the root plugin folder.
		if ( strpos( $plugin_file, '/' ) && $this_plugin_dir !== $plugins_dir ) {
			$deleted = $wp_filesystem->delete( $this_plugin_dir, true );
		} else {
			$deleted = $wp_filesystem->delete( $plugins_dir . $plugin_file );
		}

		/**
		 * Fires immediately after a plugin deletion attempt.
		 *
		 * @since 4.4.0
		 *
		 * @param string $plugin_file Path to the plugin file relative to the plugins directory.
		 * @param bool   $deleted     Whether the plugin deletion was successful.
		 */
		do_action( 'deleted_plugin', $plugin_file, $deleted );

		if ( ! $deleted ) {
			$errors[] = $plugin_file;
			continue;
		}

		$plugin_slug = dirname( $plugin_file );

		if ( 'hello.php' === $plugin_file ) {
			$plugin_slug = 'hello-dolly';
		}

		// Remove language files, silently.
		if ( '.' !== $plugin_slug && ! empty( $plugin_translations[ $plugin_slug ] ) ) {
			$translations = $plugin_translations[ $plugin_slug ];

			foreach ( $translations as $translation => $data ) {
				$wp_filesystem->delete( WP_LANG_DIR . '/plugins/' . $plugin_slug . '-' . $translation . '.po' );
				$wp_filesystem->delete( WP_LANG_DIR . '/plugins/' . $plugin_slug . '-' . $translation . '.mo' );

				$json_translation_files = glob( WP_LANG_DIR . '/plugins/' . $plugin_slug . '-' . $translation . '-*.json' );
				if ( $json_translation_files ) {
					array_map( array( $wp_filesystem, 'delete' ), $json_translation_files );
				}
			}
		}
	}

	// Remove deleted plugins from the plugin updates list.
	$current = get_site_transient( 'update_plugins' );
	if ( $current ) {
		// Don't remove the plugins that weren't deleted.
		$deleted = array_diff( $plugins, $errors );

		foreach ( $deleted as $plugin_file ) {
			unset( $current->response[ $plugin_file ] );
		}

		set_site_transient( 'update_plugins', $current );
	}

	if ( ! empty( $errors ) ) {
		if ( 1 === count( $errors ) ) {
			/* translators: %s: Plugin filename. */
			$message = __( 'Could not fully remove the plugin %s.' );
		} else {
			/* translators: %s: Comma-separated list of plugin filenames. */
			$message = __( 'Could not fully remove the plugins %s.' );
		}

		return new WP_Error( 'could_not_remove_plugin', sprintf( $message, implode( ', ', $errors ) ) );
	}

	return true;
}

/**
 * Validates active plugins.
 *
 * Validate all active plugins, deactivates invalid and
 * returns an array of deactivated ones.
 *
 * @since 2.5.0
 * @return WP_Error[] Array of plugin errors keyed by plugin file name.
 */
function validate_active_plugins() {
	$plugins = get_option( 'active_plugins', array() );
	// Validate vartype: array.
	if ( ! is_array( $plugins ) ) {
		update_option( 'active_plugins', array() );
		$plugins = array();
	}

	if ( is_multisite() && current_user_can( 'manage_network_plugins' ) ) {
		$network_plugins = (array) get_site_option( 'active_sitewide_plugins', array() );
		$plugins         = array_merge( $plugins, array_keys( $network_plugins ) );
	}

	if ( empty( $plugins ) ) {
		return array();
	}

	$invalid = array();

	// Invalid plugins get deactivated.
	foreach ( $plugins as $plugin ) {
		$result = validate_plugin( $plugin );
		if ( is_wp_error( $result ) ) {
			$invalid[ $plugin ] = $result;
			deactivate_plugins( $plugin, true );
		}
	}
	return $invalid;
}

/**
 * Validates the plugin path.
 *
 * Checks that the main plugin file exists and is a valid plugin. See validate_file().
 *
 * @since 2.5.0
 *
 * @param string $plugin Path to the plugin file relative to the plugins directory.
 * @return int|WP_Error 0 on success, WP_Error on failure.
 */
function validate_plugin( $plugin ) {
	if ( validate_file( $plugin ) ) {
		return new WP_Error( 'plugin_invalid', __( 'Invalid plugin path.' ) );
	}
	if ( ! file_exists( WP_PLUGIN_DIR . '/' . $plugin ) ) {
		return new WP_Error( 'plugin_not_found', __( 'Plugin file does not exist.' ) );
	}

	$installed_plugins = get_plugins();
	if ( ! isset( $installed_plugins[ $plugin ] ) ) {
		return new WP_Error( 'no_plugin_header', __( 'The plugin does not have a valid header.' ) );
	}
	return 0;
}

/**
 * Validates the plugin requirements for WordPress version and PHP version.
 *
 * Uses the information from `Requires at least` and `Requires PHP` headers
 * defined in the plugin's main PHP file.
 *
 * @since 5.2.0
 * @since 5.3.0 Added support for reading the headers from the plugin's
 *              main PHP file, with `readme.txt` as a fallback.
 * @since 5.8.0 Removed support for using `readme.txt` as a fallback.
 *
 * @param string $plugin Path to the plugin file relative to the plugins directory.
 * @return true|WP_Error True if requirements are met, WP_Error on failure.
 */
function validate_plugin_requirements( $plugin ) {
	$plugin_headers = get_plugin_data( WP_PLUGIN_DIR . '/' . $plugin );

	$requirements = array(
		'requires'     => ! empty( $plugin_headers['RequiresWP'] ) ? $plugin_headers['RequiresWP'] : '',
		'requires_php' => ! empty( $plugin_headers['RequiresPHP'] ) ? $plugin_headers['RequiresPHP'] : '',
	);

	$compatible_wp  = is_wp_version_compatible( $requirements['requires'] );
	$compatible_php = is_php_version_compatible( $requirements['requires_php'] );

	$php_update_message = '</p><p>' . sprintf(
		/* translators: %s: URL to Update PHP page. */
		__( '<a href="%s">Learn more about updating PHP</a>.' ),
		esc_url( wp_get_update_php_url() )
	);

	$annotation = wp_get_update_php_annotation();

	if ( $annotation ) {
		$php_update_message .= '</p><p><em>' . $annotation . '</em>';
	}

	if ( ! $compatible_wp && ! $compatible_php ) {
		return new WP_Error(
			'plugin_wp_php_incompatible',
			'<p>' . sprintf(
				/* translators: 1: Current WordPress version, 2: Current PHP version, 3: Plugin name, 4: Required WordPress version, 5: Required PHP version. */
				_x( '<strong>Error:</strong> Current versions of WordPress (%1$s) and PHP (%2$s) do not meet minimum requirements for %3$s. The plugin requires WordPress %4$s and PHP %5$s.', 'plugin' ),
				get_bloginfo( 'version' ),
				PHP_VERSION,
				$plugin_headers['Name'],
				$requirements['requires'],
				$requirements['requires_php']
			) . $php_update_message . '</p>'
		);
	} elseif ( ! $compatible_php ) {
		return new WP_Error(
			'plugin_php_incompatible',
			'<p>' . sprintf(
				/* translators: 1: Current PHP version, 2: Plugin name, 3: Required PHP version. */
				_x( '<strong>Error:</strong> Current PHP version (%1$s) does not meet minimum requirements for %2$s. The plugin requires PHP %3$s.', 'plugin' ),
				PHP_VERSION,
				$plugin_headers['Name'],
				$requirements['requires_php']
			) . $php_update_message . '</p>'
		);
	} elseif ( ! $compatible_wp ) {
		return new WP_Error(
			'plugin_wp_incompatible',
			'<p>' . sprintf(
				/* translators: 1: Current WordPress version, 2: Plugin name, 3: Required WordPress version. */
				_x( '<strong>Error:</strong> Current WordPress version (%1$s) does not meet minimum requirements for %2$s. The plugin requires WordPress %3$s.', 'plugin' ),
				get_bloginfo( 'version' ),
				$plugin_headers['Name'],
				$requirements['requires']
			) . '</p>'
		);
	}

	return true;
}

/**
 * Determines whether the plugin can be uninstalled.
 *
 * @since 2.7.0
 *
 * @param string $plugin Path to the plugin file relative to the plugins directory.
 * @return bool Whether plugin can be uninstalled.
 */
function is_uninstallable_plugin( $plugin ) {
	$file = plugin_basename( $plugin );

	$uninstallable_plugins = (array) get_option( 'uninstall_plugins' );
	if ( isset( $uninstallable_plugins[ $file ] ) || file_exists( WP_PLUGIN_DIR . '/' . dirname( $file ) . '/uninstall.php' ) ) {
		return true;
	}

	return false;
}

/**
 * Uninstalls a single plugin.
 *
 * Calls the uninstall hook, if it is available.
 *
 * @since 2.7.0
 *
 * @param string $plugin Path to the plugin file relative to the plugins directory.
 * @return true|void True if a plugin's uninstall.php file has been found and included.
 *                   Void otherwise.
 */
function uninstall_plugin( $plugin ) {
	$file = plugin_basename( $plugin );

	$uninstallable_plugins = (array) get_option( 'uninstall_plugins' );

	/**
	 * Fires in uninstall_plugin() immediately before the plugin is uninstalled.
	 *
	 * @since 4.5.0
	 *
	 * @param string $plugin                Path to the plugin file relative to the plugins directory.
	 * @param array  $uninstallable_plugins Uninstallable plugins.
	 */
	do_action( 'pre_uninstall_plugin', $plugin, $uninstallable_plugins );

	if ( file_exists( WP_PLUGIN_DIR . '/' . dirname( $file ) . '/uninstall.php' ) ) {
		if ( isset( $uninstallable_plugins[ $file ] ) ) {
			unset( $uninstallable_plugins[ $file ] );
			update_option( 'uninstall_plugins', $uninstallable_plugins );
		}
		unset( $uninstallable_plugins );

		define( 'WP_UNINSTALL_PLUGIN', $file );

		wp_register_plugin_realpath( WP_PLUGIN_DIR . '/' . $file );
		include_once WP_PLUGIN_DIR . '/' . dirname( $file ) . '/uninstall.php';

		return true;
	}

	if ( isset( $uninstallable_plugins[ $file ] ) ) {
		$callable = $uninstallable_plugins[ $file ];
		unset( $uninstallable_plugins[ $file ] );
		update_option( 'uninstall_plugins', $uninstallable_plugins );
		unset( $uninstallable_plugins );

		wp_register_plugin_realpath( WP_PLUGIN_DIR . '/' . $file );
		include_once WP_PLUGIN_DIR . '/' . $file;

		add_action( "uninstall_{$file}", $callable );

		/**
		 * Fires in uninstall_plugin() once the plugin has been uninstalled.
		 *
		 * The action concatenates the 'uninstall_' prefix with the basename of the
		 * plugin passed to uninstall_plugin() to create a dynamically-named action.
		 *
		 * @since 2.7.0
		 */
		do_action( "uninstall_{$file}" );
	}
}

//
// Menu.
//

/**
 * Adds a top-level menu page.
 *
 * This function takes a capability which will be used to determine whether
 * or not a page is included in the menu.
 *
 * The function which is hooked in to handle the output of the page must check
 * that the user has the required capability as well.
 *
 * @since 1.5.0
 *
 * @global array $menu
 * @global array $admin_page_hooks
 * @global array $_registered_pages
 * @global array $_parent_pages
 *
 * @param string    $page_title The text to be displayed in the title tags of the page when the menu is selected.
 * @param string    $menu_title The text to be used for the menu.
 * @param string    $capability The capability required for this menu to be displayed to the user.
 * @param string    $menu_slug  The slug name to refer to this menu by. Should be unique for this menu page and only
 *                              include lowercase alphanumeric, dashes, and underscores characters to be compatible
 *                              with sanitize_key().
 * @param callable  $callback   Optional. The function to be called to output the content for this page.
 * @param string    $icon_url   Optional. The URL to the icon to be used for this menu.
 *                              * Pass a base64-encoded SVG using a data URI, which will be colored to match
 *                                the color scheme. This should begin with 'data:image/svg+xml;base64,'.
 *                              * Pass the name of a Dashicons helper class to use a font icon,
 *                                e.g. 'dashicons-chart-pie'.
 *                              * Pass 'none' to leave div.wp-menu-image empty so an icon can be added via CSS.
 * @param int|float $position   Optional. The position in the menu order this item should appear.
 * @return string The resulting page's hook_suffix.
 */
function add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $callback = '', $icon_url = '', $position = null ) {
	global $menu, $admin_page_hooks, $_registered_pages, $_parent_pages;

	$menu_slug = plugin_basename( $menu_slug );

	$admin_page_hooks[ $menu_slug ] = sanitize_title( $menu_title );

	$hookname = get_plugin_page_hookname( $menu_slug, '' );

	if ( ! empty( $callback ) && ! empty( $hookname ) && current_user_can( $capability ) ) {
		add_action( $hookname, $callback );
	}

	if ( empty( $icon_url ) ) {
		$icon_url   = 'dashicons-admin-generic';
		$icon_class = 'menu-icon-generic ';
	} else {
		$icon_url   = set_url_scheme( $icon_url );
		$icon_class = '';
	}

	$new_menu = array( $menu_title, $capability, $menu_slug, $page_title, 'menu-top ' . $icon_class . $hookname, $hookname, $icon_url );

	if ( null !== $position && ! is_numeric( $position ) ) {
		_doing_it_wrong(
			__FUNCTION__,
			sprintf(
				/* translators: %s: add_menu_page() */
				__( 'The seventh parameter passed to %s should be numeric representing menu position.' ),
				'<code>add_menu_page()</code>'
			),
			'6.0.0'
		);
		$position = null;
	}

	if ( null === $position || ! is_numeric( $position ) ) {
		$menu[] = $new_menu;
	} elseif ( isset( $menu[ (string) $position ] ) ) {
		$collision_avoider = base_convert( substr( md5( $menu_slug . $menu_title ), -4 ), 16, 10 ) * 0.00001;
		$position          = (string) ( $position + $collision_avoider );
		$menu[ $position ] = $new_menu;
	} else {
		/*
		 * Cast menu position to a string.
		 *
		 * This allows for floats to be passed as the position. PHP will normally cast a float to an
		 * integer value, this ensures the float retains its mantissa (positive fractional part).
		 *
		 * A string containing an integer value, eg "10", is treated as a numeric index.
		 */
		$position          = (string) $position;
		$menu[ $position ] = $new_menu;
	}

	$_registered_pages[ $hookname ] = true;

	// No parent as top level.
	$_parent_pages[ $menu_slug ] = false;

	return $hookname;
}

/**
 * Adds a submenu page.
 *
 * This function takes a capability which will be used to determine whether
 * or not a page is included in the menu.
 *
 * The function which is hooked in to handle the output of the page must check
 * that the user has the required capability as well.
 *
 * @since 1.5.0
 * @since 5.3.0 Added the `$position` parameter.
 *
 * @global array $submenu
 * @global array $menu
 * @global array $_wp_real_parent_file
 * @global bool  $_wp_submenu_nopriv
 * @global array $_registered_pages
 * @global array $_parent_pages
 *
 * @param string    $parent_slug The slug name for the parent menu (or the file name of a standard
 *                               WordPress admin page).
 * @param string    $page_title  The text to be displayed in the title tags of the page when the menu
 *                               is selected.
 * @param string    $menu_title  The text to be used for the menu.
 * @param string    $capability  The capability required for this menu to be displayed to the user.
 * @param string    $menu_slug   The slug name to refer to this menu by. Should be unique for this menu
 *                               and only include lowercase alphanumeric, dashes, and underscores characters
 *                               to be compatible with sanitize_key().
 * @param callable  $callback    Optional. The function to be called to output the content for this page.
 * @param int|float $position    Optional. The position in the menu order this item should appear.
 * @return string|false The resulting page's hook_suffix, or false if the user does not have the capability required.
 */
function add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $callback = '', $position = null ) {
	global $submenu, $menu, $_wp_real_parent_file, $_wp_submenu_nopriv,
		$_registered_pages, $_parent_pages;

	$menu_slug   = plugin_basename( $menu_slug );
	$parent_slug = plugin_basename( $parent_slug );

	if ( isset( $_wp_real_parent_file[ $parent_slug ] ) ) {
		$parent_slug = $_wp_real_parent_file[ $parent_slug ];
	}

	if ( ! current_user_can( $capability ) ) {
		$_wp_submenu_nopriv[ $parent_slug ][ $menu_slug ] = true;
		return false;
	}

	/*
	 * If the parent doesn't already have a submenu, add a link to the parent
	 * as the first item in the submenu. If the submenu file is the same as the
	 * parent file someone is trying to link back to the parent manually. In
	 * this case, don't automatically add a link back to avoid duplication.
	 */
	if ( ! isset( $submenu[ $parent_slug ] ) && $menu_slug !== $parent_slug ) {
		foreach ( (array) $menu as $parent_menu ) {
			if ( $parent_menu[2] === $parent_slug && current_user_can( $parent_menu[1] ) ) {
				$submenu[ $parent_slug ][] = array_slice( $parent_menu, 0, 4 );
			}
		}
	}

	$new_sub_menu = array( $menu_title, $capability, $menu_slug, $page_title );

	if ( null !== $position && ! is_numeric( $position ) ) {
		_doing_it_wrong(
			__FUNCTION__,
			sprintf(
				/* translators: %s: add_submenu_page() */
				__( 'The seventh parameter passed to %s should be numeric representing menu position.' ),
				'<code>add_submenu_page()</code>'
			),
			'5.3.0'
		);
		$position = null;
	}

	if (
		null === $position ||
		( ! isset( $submenu[ $parent_slug ] ) || $position >= count( $submenu[ $parent_slug ] ) )
	) {
		$submenu[ $parent_slug ][] = $new_sub_menu;
	} else {
		// Test for a negative position.
		$position = max( $position, 0 );
		if ( 0 === $position ) {
			// For negative or `0` positions, prepend the submenu.
			array_unshift( $submenu[ $parent_slug ], $new_sub_menu );
		} else {
			$position = absint( $position );
			// Grab all of the items before the insertion point.
			$before_items = array_slice( $submenu[ $parent_slug ], 0, $position, true );
			// Grab all of the items after the insertion point.
			$after_items = array_slice( $submenu[ $parent_slug ], $position, null, true );
			// Add the new item.
			$before_items[] = $new_sub_menu;
			// Merge the items.
			$submenu[ $parent_slug ] = array_merge( $before_items, $after_items );
		}
	}

	// Sort the parent array.
	ksort( $submenu[ $parent_slug ] );

	$hookname = get_plugin_page_hookname( $menu_slug, $parent_slug );
	if ( ! empty( $callback ) && ! empty( $hookname ) ) {
		add_action( $hookname, $callback );
	}

	$_registered_pages[ $hookname ] = true;

	/*
	 * Backward-compatibility for plugins using add_management_page().
	 * See wp-admin/admin.php for redirect from edit.php to tools.php.
	 */
	if ( 'tools.php' === $parent_slug ) {
		$_registered_pages[ get_plugin_page_hookname( $menu_slug, 'edit.php' ) ] = true;
	}

	// No parent as top level.
	$_parent_pages[ $menu_slug ] = $parent_slug;

	return $hookname;
}

/**
 * Adds a submenu page to the Tools main menu.
 *
 * This function takes a capability which will be used to determine whether
 * or not a page is included in the menu.
 *
 * The function which is hooked in to handle the output of the page must check
 * that the user has the required capability as well.
 *
 * @since 1.5.0
 * @since 5.3.0 Added the `$position` parameter.
 *
 * @param string   $page_title The text to be displayed in the title tags of the page when the menu is selected.
 * @param string   $menu_title The text to be used for the menu.
 * @param string   $capability The capability required for this menu to be displayed to the user.
 * @param string   $menu_slug  The slug name to refer to this menu by (should be unique for this menu).
 * @param callable $callback   Optional. The function to be called to output the content for this page.
 * @param int      $position   Optional. The position in the menu order this item should appear.
 * @return string|false The resulting page's hook_suffix, or false if the user does not have the capability required.
 */
function add_management_page( $page_title, $menu_title, $capability, $menu_slug, $callback = '', $position = null ) {
	return add_submenu_page( 'tools.php', $page_title, $menu_title, $capability, $menu_slug, $callback, $position );
}

/**
 * Adds a submenu page to the Settings main menu.
 *
 * This function takes a capability which will be used to determine whether
 * or not a page is included in the menu.
 *
 * The function which is hooked in to handle the output of the page must check
 * that the user has the required capability as well.
 *
 * @since 1.5.0
 * @since 5.3.0 Added the `$position` parameter.
 *
 * @param string   $page_title The text to be displayed in the title tags of the page when the menu is selected.
 * @param string   $menu_title The text to be used for the menu.
 * @param string   $capability The capability required for this menu to be displayed to the user.
 * @param string   $menu_slug  The slug name to refer to this menu by (should be unique for this menu).
 * @param callable $callback   Optional. The function to be called to output the content for this page.
 * @param int      $position   Optional. The position in the menu order this item should appear.
 * @return string|false The resulting page's hook_suffix, or false if the user does not have the capability required.
 */
function add_options_page( $page_title, $menu_title, $capability, $menu_slug, $callback = '', $position = null ) {
	return add_submenu_page( 'options-general.php', $page_title, $menu_title, $capability, $menu_slug, $callback, $position );
}

/**
 * Adds a submenu page to the Appearance main menu.
 *
 * This function takes a capability which will be used to determine whether
 * or not a page is included in the menu.
 *
 * The function which is hooked in to handle the output of the page must check
 * that the user has the required capability as well.
 *
 * @since 2.0.0
 * @since 5.3.0 Added the `$position` parameter.
 *
 * @param string   $page_title The text to be displayed in the title tags of the page when the menu is selected.
 * @param string   $menu_title The text to be used for the menu.
 * @param string   $capability The capability required for this menu to be displayed to the user.
 * @param string   $menu_slug  The slug name to refer to this menu by (should be unique for this menu).
 * @param callable $callback   Optional. The function to be called to output the content for this page.
 * @param int      $position   Optional. The position in the menu order this item should appear.
 * @return string|false The resulting page's hook_suffix, or false if the user does not have the capability required.
 */
function add_theme_page( $page_title, $menu_title, $capability, $menu_slug, $callback = '', $position = null ) {
	return add_submenu_page( 'themes.php', $page_title, $menu_title, $capability, $menu_slug, $callback, $position );
}

/**
 * Adds a submenu page to the Plugins main menu.
 *
 * This function takes a capability which will be used to determine whether
 * or not a page is included in the menu.
 *
 * The function which is hooked in to handle the output of the page must check
 * that the user has the required capability as well.
 *
 * @since 3.0.0
 * @since 5.3.0 Added the `$position` parameter.
 *
 * @param string   $page_title The text to be displayed in the title tags of the page when the menu is selected.
 * @param string   $menu_title The text to be used for the menu.
 * @param string   $capability The capability required for this menu to be displayed to the user.
 * @param string   $menu_slug  The slug name to refer to this menu by (should be unique for this menu).
 * @param callable $callback   Optional. The function to be called to output the content for this page.
 * @param int      $position   Optional. The position in the menu order this item should appear.
 * @return string|false The resulting page's hook_suffix, or false if the user does not have the capability required.
 */
function add_plugins_page( $page_title, $menu_title, $capability, $menu_slug, $callback = '', $position = null ) {
	return add_submenu_page( 'plugins.php', $page_title, $menu_title, $capability, $menu_slug, $callback, $position );
}

/**
 * Adds a submenu page to the Users/Profile main menu.
 *
 * This function takes a capability which will be used to determine whether
 * or not a page is included in the menu.
 *
 * The function which is hooked in to handle the output of the page must check
 * that the user has the required capability as well.
 *
 * @since 2.1.3
 * @since 5.3.0 Added the `$position` parameter.
 *
 * @param string   $page_title The text to be displayed in the title tags of the page when the menu is selected.
 * @param string   $menu_title The text to be used for the menu.
 * @param string   $capability The capability required for this menu to be displayed to the user.
 * @param string   $menu_slug  The slug name to refer to this menu by (should be unique for this menu).
 * @param callable $callback   Optional. The function to be called to output the content for this page.
 * @param int      $position   Optional. The position in the menu order this item should appear.
 * @return string|false The resulting page's hook_suffix, or false if the user does not have the capability required.
 */
function add_users_page( $page_title, $menu_title, $capability, $menu_slug, $callback = '', $position = null ) {
	if ( current_user_can( 'edit_users' ) ) {
		$parent = 'users.php';
	} else {
		$parent = 'profile.php';
	}
	return add_submenu_page( $parent, $page_title, $menu_title, $capability, $menu_slug, $callback, $position );
}

/**
 * Adds a submenu page to the Dashboard main menu.
 *
 * This function takes a capability which will be used to determine whether
 * or not a page is included in the menu.
 *
 * The function which is hooked in to handle the output of the page must check
 * that the user has the required capability as well.
 *
 * @since 2.7.0
 * @since 5.3.0 Added the `$position` parameter.
 *
 * @param string   $page_title The text to be displayed in the title tags of the page when the menu is selected.
 * @param string   $menu_title The text to be used for the menu.
 * @param string   $capability The capability required for this menu to be displayed to the user.
 * @param string   $menu_slug  The slug name to refer to this menu by (should be unique for this menu).
 * @param callable $callback   Optional. The function to be called to output the content for this page.
 * @param int      $position   Optional. The position in the menu order this item should appear.
 * @return string|false The resulting page's hook_suffix, or false if the user does not have the capability required.
 */
function add_dashboard_page( $page_title, $menu_title, $capability, $menu_slug, $callback = '', $position = null ) {
	return add_submenu_page( 'index.php', $page_title, $menu_title, $capability, $menu_slug, $callback, $position );
}

/**
 * Adds a submenu page to the Posts main menu.
 *
 * This function takes a capability which will be used to determine whether
 * or not a page is included in the menu.
 *
 * The function which is hooked in to handle the output of the page must check
 * that the user has the required capability as well.
 *
 * @since 2.7.0
 * @since 5.3.0 Added the `$position` parameter.
 *
 * @param string   $page_title The text to be displayed in the title tags of the page when the menu is selected.
 * @param string   $menu_title The text to be used for the menu.
 * @param string   $capability The capability required for this menu to be displayed to the user.
 * @param string   $menu_slug  The slug name to refer to this menu by (should be unique for this menu).
 * @param callable $callback   Optional. The function to be called to output the content for this page.
 * @param int      $position   Optional. The position in the menu order this item should appear.
 * @return string|false The resulting page's hook_suffix, or false if the user does not have the capability required.
 */
function add_posts_page( $page_title, $menu_title, $capability, $menu_slug, $callback = '', $position = null ) {
	return add_submenu_page( 'edit.php', $page_title, $menu_title, $capability, $menu_slug, $callback, $position );
}

/**
 * Adds a submenu page to the Media main menu.
 *
 * This function takes a capability which will be used to determine whether
 * or not a page is included in the menu.
 *
 * The function which is hooked in to handle the output of the page must check
 * that the user has the required capability as well.
 *
 * @since 2.7.0
 * @since 5.3.0 Added the `$position` parameter.
 *
 * @param string   $page_title The text to be displayed in the title tags of the page when the menu is selected.
 * @param string   $menu_title The text to be used for the menu.
 * @param string   $capability The capability required for this menu to be displayed to the user.
 * @param string   $menu_slug  The slug name to refer to this menu by (should be unique for this menu).
 * @param callable $callback   Optional. The function to be called to output the content for this page.
 * @param int      $position   Optional. The position in the menu order this item should appear.
 * @return string|false The resulting page's hook_suffix, or false if the user does not have the capability required.
 */
function add_media_page( $page_title, $menu_title, $capability, $menu_slug, $callback = '', $position = null ) {
	return add_submenu_page( 'upload.php', $page_title, $menu_title, $capability, $menu_slug, $callback, $position );
}

/**
 * Adds a submenu page to the Links main menu.
 *
 * This function takes a capability which will be used to determine whether
 * or not a page is included in the menu.
 *
 * The function which is hooked in to handle the output of the page must check
 * that the user has the required capability as well.
 *
 * @since 2.7.0
 * @since 5.3.0 Added the `$position` parameter.
 *
 * @param string   $page_title The text to be displayed in the title tags of the page when the menu is selected.
 * @param string   $menu_title The text to be used for the menu.
 * @param string   $capability The capability required for this menu to be displayed to the user.
 * @param string   $menu_slug  The slug name to refer to this menu by (should be unique for this menu).
 * @param callable $callback   Optional. The function to be called to output the content for this page.
 * @param int      $position   Optional. The position in the menu order this item should appear.
 * @return string|false The resulting page's hook_suffix, or false if the user does not have the capability required.
 */
function add_links_page( $page_title, $menu_title, $capability, $menu_slug, $callback = '', $position = null ) {
	return add_submenu_page( 'link-manager.php', $page_title, $menu_title, $capability, $menu_slug, $callback, $position );
}

/**
 * Adds a submenu page to the Pages main menu.
 *
 * This function takes a capability which will be used to determine whether
 * or not a page is included in the menu.
 *
 * The function which is hooked in to handle the output of the page must check
 * that the user has the required capability as well.
 *
 * @since 2.7.0
 * @since 5.3.0 Added the `$position` parameter.
 *
 * @param string   $page_title The text to be displayed in the title tags of the page when the menu is selected.
 * @param string   $menu_title The text to be used for the menu.
 * @param string   $capability The capability required for this menu to be displayed to the user.
 * @param string   $menu_slug  The slug name to refer to this menu by (should be unique for this menu).
 * @param callable $callback   Optional. The function to be called to output the content for this page.
 * @param int      $position   Optional. The position in the menu order this item should appear.
 * @return string|false The resulting page's hook_suffix, or false if the user does not have the capability required.
 */
function add_pages_page( $page_title, $menu_title, $capability, $menu_slug, $callback = '', $position = null ) {
	return add_submenu_page( 'edit.php?post_type=page', $page_title, $menu_title, $capability, $menu_slug, $callback, $position );
}

/**
 * Adds a submenu page to the Comments main menu.
 *
 * This function takes a capability which will be used to determine whether
 * or not a page is included in the menu.
 *
 * The function which is hooked in to handle the output of the page must check
 * that the user has the required capability as well.
 *
 * @since 2.7.0
 * @since 5.3.0 Added the `$position` parameter.
 *
 * @param string   $page_title The text to be displayed in the title tags of the page when the menu is selected.
 * @param string   $menu_title The text to be used for the menu.
 * @param string   $capability The capability required for this menu to be displayed to the user.
 * @param string   $menu_slug  The slug name to refer to this menu by (should be unique for this menu).
 * @param callable $callback   Optional. The function to be called to output the content for this page.
 * @param int      $position   Optional. The position in the menu order this item should appear.
 * @return string|false The resulting page's hook_suffix, or false if the user does not have the capability required.
 */
function add_comments_page( $page_title, $menu_title, $capability, $menu_slug, $callback = '', $position = null ) {
	return add_submenu_page( 'edit-comments.php', $page_title, $menu_title, $capability, $menu_slug, $callback, $position );
}

/**
 * Removes a top-level admin menu.
 *
 * Example usage:
 *
 *  - `remove_menu_page( 'tools.php' )`
 *  - `remove_menu_page( 'plugin_menu_slug' )`
 *
 * @since 3.1.0
 *
 * @global array $menu
 *
 * @param string $menu_slug The slug of the menu.
 * @return array|false The removed menu on success, false if not found.
 */
function remove_menu_page( $menu_slug ) {
	global $menu;

	foreach ( $menu as $i => $item ) {
		if ( $menu_slug === $item[2] ) {
			unset( $menu[ $i ] );
			return $item;
		}
	}

	return false;
}

/**
 * Removes an admin submenu.
 *
 * Example usage:
 *
 *  - `remove_submenu_page( 'themes.php', 'nav-menus.php' )`
 *  - `remove_submenu_page( 'tools.php', 'plugin_submenu_slug' )`
 *  - `remove_submenu_page( 'plugin_menu_slug', 'plugin_submenu_slug' )`
 *
 * @since 3.1.0
 *
 * @global array $submenu
 *
 * @param string $menu_slug    The slug for the parent menu.
 * @param string $submenu_slug The slug of the submenu.
 * @return array|false The removed submenu on success, false if not found.
 */
function remove_submenu_page( $menu_slug, $submenu_slug ) {
	global $submenu;

	if ( ! isset( $submenu[ $menu_slug ] ) ) {
		return false;
	}

	foreach ( $submenu[ $menu_slug ] as $i => $item ) {
		if ( $submenu_slug === $item[2] ) {
			unset( $submenu[ $menu_slug ][ $i ] );
			return $item;
		}
	}

	return false;
}

/**
 * Gets the URL to access a particular menu page based on the slug it was registered with.
 *
 * If the slug hasn't been registered properly, no URL will be returned.
 *
 * @since 3.0.0
 *
 * @global array $_parent_pages
 *
 * @param string $menu_slug The slug name to refer to this menu by (should be unique for this menu).
 * @param bool   $display   Optional. Whether or not to display the URL. Default true.
 * @return string The menu page URL.
 */
function menu_page_url( $menu_slug, $display = true ) {
	global $_parent_pages;

	if ( isset( $_parent_pages[ $menu_slug ] ) ) {
		$parent_slug = $_parent_pages[ $menu_slug ];

		if ( $parent_slug && ! isset( $_parent_pages[ $parent_slug ] ) ) {
			$url = admin_url( add_query_arg( 'page', $menu_slug, $parent_slug ) );
		} else {
			$url = admin_url( 'admin.php?page=' . $menu_slug );
		}
	} else {
		$url = '';
	}

	$url = esc_url( $url );

	if ( $display ) {
		echo $url;
	}

	return $url;
}

//
// Pluggable Menu Support -- Private.
//
/**
 * Gets the parent file of the current admin page.
 *
 * @since 1.5.0
 *
 * @global string $parent_file
 * @global array  $menu
 * @global array  $submenu
 * @global string $pagenow              The filename of the current screen.
 * @global string $typenow              The post type of the current screen.
 * @global string $plugin_page
 * @global array  $_wp_real_parent_file
 * @global array  $_wp_menu_nopriv
 * @global array  $_wp_submenu_nopriv
 *
 * @param string $parent_page Optional. The slug name for the parent menu (or the file name
 *                            of a standard WordPress admin page). Default empty string.
 * @return string The parent file of the current admin page.
 */
function get_admin_page_parent( $parent_page = '' ) {
	global $parent_file, $menu, $submenu, $pagenow, $typenow,
		$plugin_page, $_wp_real_parent_file, $_wp_menu_nopriv, $_wp_submenu_nopriv;

	if ( ! empty( $parent_page ) && 'admin.php' !== $parent_page ) {
		if ( isset( $_wp_real_parent_file[ $parent_page ] ) ) {
			$parent_page = $_wp_real_parent_file[ $parent_page ];
		}

		return $parent_page;
	}

	if ( 'admin.php' === $pagenow && isset( $plugin_page ) ) {
		foreach ( (array) $menu as $parent_menu ) {
			if ( $parent_menu[2] === $plugin_page ) {
				$parent_file = $plugin_page;

				if ( isset( $_wp_real_parent_file[ $parent_file ] ) ) {
					$parent_file = $_wp_real_parent_file[ $parent_file ];
				}

				return $parent_file;
			}
		}
		if ( isset( $_wp_menu_nopriv[ $plugin_page ] ) ) {
			$parent_file = $plugin_page;

			if ( isset( $_wp_real_parent_file[ $parent_file ] ) ) {
					$parent_file = $_wp_real_parent_file[ $parent_file ];
			}

			return $parent_file;
		}
	}

	if ( isset( $plugin_page ) && isset( $_wp_submenu_nopriv[ $pagenow ][ $plugin_page ] ) ) {
		$parent_file = $pagenow;

		if ( isset( $_wp_real_parent_file[ $parent_file ] ) ) {
			$parent_file = $_wp_real_parent_file[ $parent_file ];
		}

		return $parent_file;
	}

	foreach ( array_keys( (array) $submenu ) as $parent_page ) {
		foreach ( $submenu[ $parent_page ] as $submenu_array ) {
			if ( isset( $_wp_real_parent_file[ $parent_page ] ) ) {
				$parent_page = $_wp_real_parent_file[ $parent_page ];
			}

			if ( ! empty( $typenow ) && "$pagenow?post_type=$typenow" === $submenu_array[2] ) {
				$parent_file = $parent_page;
				return $parent_page;
			} elseif ( empty( $typenow ) && $pagenow === $submenu_array[2]
				&& ( empty( $parent_file ) || false === strpos( $parent_file, '?' ) )
			) {
				$parent_file = $parent_page;
				return $parent_page;
			} elseif ( isset( $plugin_page ) && $plugin_page === $submenu_array[2] ) {
				$parent_file = $parent_page;
				return $parent_page;
			}
		}
	}

	if ( empty( $parent_file ) ) {
		$parent_file = '';
	}
	return '';
}

/**
 * Gets the title of the current admin page.
 *
 * @since 1.5.0
 *
 * @global string $title
 * @global array  $menu
 * @global array  $submenu
 * @global string $pagenow     The filename of the current screen.
 * @global string $typenow     The post type of the current screen.
 * @global string $plugin_page
 *
 * @return string The title of the current admin page.
 */
function get_admin_page_title() {
	global $title, $menu, $submenu, $pagenow, $typenow, $plugin_page;

	if ( ! empty( $title ) ) {
		return $title;
	}

	$hook = get_plugin_page_hook( $plugin_page, $pagenow );

	$parent  = get_admin_page_parent();
	$parent1 = $parent;

	if ( empty( $parent ) ) {
		foreach ( (array) $menu as $menu_array ) {
			if ( isset( $menu_array[3] ) ) {
				if ( $menu_array[2] === $pagenow ) {
					$title = $menu_array[3];
					return $menu_array[3];
				} elseif ( isset( $plugin_page ) && $plugin_page === $menu_array[2] && $hook === $menu_array[5] ) {
					$title = $menu_array[3];
					return $menu_array[3];
				}
			} else {
				$title = $menu_array[0];
				return $title;
			}
		}
	} else {
		foreach ( array_keys( $submenu ) as $parent ) {
			foreach ( $submenu[ $parent ] as $submenu_array ) {
				if ( isset( $plugin_page )
					&& $plugin_page === $submenu_array[2]
					&& ( $pagenow === $parent
						|| $plugin_page === $parent
						|| $plugin_page === $hook
						|| 'admin.php' === $pagenow && $parent1 !== $submenu_array[2]
						|| ! empty( $typenow ) && "$pagenow?post_type=$typenow" === $parent )
					) {
						$title = $submenu_array[3];
						return $submenu_array[3];
				}

				if ( $submenu_array[2] !== $pagenow || isset( $_GET['page'] ) ) { // Not the current page.
					continue;
				}

				if ( isset( $submenu_array[3] ) ) {
					$title = $submenu_array[3];
					return $submenu_array[3];
				} else {
					$title = $submenu_array[0];
					return $title;
				}
			}
		}
		if ( empty( $title ) ) {
			foreach ( $menu as $menu_array ) {
				if ( isset( $plugin_page )
					&& $plugin_page === $menu_array[2]
					&& 'admin.php' === $pagenow
					&& $parent1 === $menu_array[2]
				) {
						$title = $menu_array[3];
						return $menu_array[3];
				}
			}
		}
	}

	return $title;
}

/**
 * Gets the hook attached to the administrative page of a plugin.
 *
 * @since 1.5.0
 *
 * @param string $plugin_page The slug name of the plugin page.
 * @param string $parent_page The slug name for the parent menu (or the file name of a standard
 *                            WordPress admin page).
 * @return string|null Hook attached to the plugin page, null otherwise.
 */
function get_plugin_page_hook( $plugin_page, $parent_page ) {
	$hook = get_plugin_page_hookname( $plugin_page, $parent_page );
	if ( has_action( $hook ) ) {
		return $hook;
	} else {
		return null;
	}
}

/**
 * Gets the hook name for the administrative page of a plugin.
 *
 * @since 1.5.0
 *
 * @global array $admin_page_hooks
 *
 * @param string $plugin_page The slug name of the plugin page.
 * @param string $parent_page The slug name for the parent menu (or the file name of a standard
 *                            WordPress admin page).
 * @return string Hook name for the plugin page.
 */
function get_plugin_page_hookname( $plugin_page, $parent_page ) {
	global $admin_page_hooks;

	$parent = get_admin_page_parent( $parent_page );

	$page_type = 'admin';
	if ( empty( $parent_page ) || 'admin.php' === $parent_page || isset( $admin_page_hooks[ $plugin_page ] ) ) {
		if ( isset( $admin_page_hooks[ $plugin_page ] ) ) {
			$page_type = 'toplevel';
		} elseif ( isset( $admin_page_hooks[ $parent ] ) ) {
			$page_type = $admin_page_hooks[ $parent ];
		}
	} elseif ( isset( $admin_page_hooks[ $parent ] ) ) {
		$page_type = $admin_page_hooks[ $parent ];
	}

	$plugin_name = preg_replace( '!\.php!', '', $plugin_page );

	return $page_type . '_page_' . $plugin_name;
}

/**
 * Determines whether the current user can access the current admin page.
 *
 * @since 1.5.0
 *
 * @global string $pagenow            The filename of the current screen.
 * @global array  $menu
 * @global array  $submenu
 * @global array  $_wp_menu_nopriv
 * @global array  $_wp_submenu_nopriv
 * @global string $plugin_page
 * @global array  $_registered_pages
 *
 * @return bool True if the current user can access the admin page, false otherwise.
 */
function user_can_access_admin_page() {
	global $pagenow, $menu, $submenu, $_wp_menu_nopriv, $_wp_submenu_nopriv,
		$plugin_page, $_registered_pages;

	$parent = get_admin_page_parent();

	if ( ! isset( $plugin_page ) && isset( $_wp_submenu_nopriv[ $parent ][ $pagenow ] ) ) {
		return false;
	}

	if ( isset( $plugin_page ) ) {
		if ( isset( $_wp_submenu_nopriv[ $parent ][ $plugin_page ] ) ) {
			return false;
		}

		$hookname = get_plugin_page_hookname( $plugin_page, $parent );

		if ( ! isset( $_registered_pages[ $hookname ] ) ) {
			return false;
		}
	}

	if ( empty( $parent ) ) {
		if ( isset( $_wp_menu_nopriv[ $pagenow ] ) ) {
			return false;
		}
		if ( isset( $_wp_submenu_nopriv[ $pagenow ][ $pagenow ] ) ) {
			return false;
		}
		if ( isset( $plugin_page ) && isset( $_wp_submenu_nopriv[ $pagenow ][ $plugin_page ] ) ) {
			return false;
		}
		if ( isset( $plugin_page ) && isset( $_wp_menu_nopriv[ $plugin_page ] ) ) {
			return false;
		}

		foreach ( array_keys( $_wp_submenu_nopriv ) as $key ) {
			if ( isset( $_wp_submenu_nopriv[ $key ][ $pagenow ] ) ) {
				return false;
			}
			if ( isset( $plugin_page ) && isset( $_wp_submenu_nopriv[ $key ][ $plugin_page ] ) ) {
				return false;
			}
		}

		return true;
	}

	if ( isset( $plugin_page ) && $plugin_page === $parent && isset( $_wp_menu_nopriv[ $plugin_page ] ) ) {
		return false;
	}

	if ( isset( $submenu[ $parent ] ) ) {
		foreach ( $submenu[ $parent ] as $submenu_array ) {
			if ( isset( $plugin_page ) && $submenu_array[2] === $plugin_page ) {
				return current_user_can( $submenu_array[1] );
			} elseif ( $submenu_array[2] === $pagenow ) {
				return current_user_can( $submenu_array[1] );
			}
		}
	}

	foreach ( $menu as $menu_array ) {
		if ( $menu_array[2] === $parent ) {
			return current_user_can( $menu_array[1] );
		}
	}

	return true;
}

/* Allowed list functions */

/**
 * Refreshes the value of the allowed options list available via the 'allowed_options' hook.
 *
 * See the {@see 'allowed_options'} filter.
 *
 * @since 2.7.0
 * @since 5.5.0 `$new_whitelist_options` was renamed to `$new_allowed_options`.
 *              Please consider writing more inclusive code.
 *
 * @global array $new_allowed_options
 *
 * @param array $options
 * @return array
 */
function option_update_filter( $options ) {
	global $new_allowed_options;

	if ( is_array( $new_allowed_options ) ) {
		$options = add_allowed_options( $new_allowed_options, $options );
	}

	return $options;
}

/**
 * Adds an array of options to the list of allowed options.
 *
 * @since 5.5.0
 *
 * @global array $allowed_options
 *
 * @param array        $new_options
 * @param string|array $options
 * @return array
 */
function add_allowed_options( $new_options, $options = '' ) {
	if ( '' === $options ) {
		global $allowed_options;
	} else {
		$allowed_options = $options;
	}

	foreach ( $new_options as $page => $keys ) {
		foreach ( $keys as $key ) {
			if ( ! isset( $allowed_options[ $page ] ) || ! is_array( $allowed_options[ $page ] ) ) {
				$allowed_options[ $page ]   = array();
				$allowed_options[ $page ][] = $key;
			} else {
				$pos = array_search( $key, $allowed_options[ $page ], true );
				if ( false === $pos ) {
					$allowed_options[ $page ][] = $key;
				}
			}
		}
	}

	return $allowed_options;
}

/**
 * Removes a list of options from the allowed options list.
 *
 * @since 5.5.0
 *
 * @global array $allowed_options
 *
 * @param array        $del_options
 * @param string|array $options
 * @return array
 */
function remove_allowed_options( $del_options, $options = '' ) {
	if ( '' === $options ) {
		global $allowed_options;
	} else {
		$allowed_options = $options;
	}

	foreach ( $del_options as $page => $keys ) {
		foreach ( $keys as $key ) {
			if ( isset( $allowed_options[ $page ] ) && is_array( $allowed_options[ $page ] ) ) {
				$pos = array_search( $key, $allowed_options[ $page ], true );
				if ( false !== $pos ) {
					unset( $allowed_options[ $page ][ $pos ] );
				}
			}
		}
	}

	return $allowed_options;
}

/**
 * Outputs nonce, action, and option_page fields for a settings page.
 *
 * @since 2.7.0
 *
 * @param string $option_group A settings group name. This should match the group name
 *                             used in register_setting().
 */
function settings_fields( $option_group ) {
	echo "<input type='hidden' name='option_page' value='" . esc_attr( $option_group ) . "' />";
	echo '<input type="hidden" name="action" value="update" />';
	wp_nonce_field( "$option_group-options" );
}

/**
 * Clears the plugins cache used by get_plugins() and by default, the plugin updates cache.
 *
 * @since 3.7.0
 *
 * @param bool $clear_update_cache Whether to clear the plugin updates cache. Default true.
 */
function wp_clean_plugins_cache( $clear_update_cache = true ) {
	if ( $clear_update_cache ) {
		delete_site_transient( 'update_plugins' );
	}
	wp_cache_delete( 'plugins', 'plugins' );
}

/**
 * Loads a given plugin attempt to generate errors.
 *
 * @since 3.0.0
 * @since 4.4.0 Function was moved into the `wp-admin/includes/plugin.php` file.
 *
 * @param string $plugin Path to the plugin file relative to the plugins directory.
 */
function plugin_sandbox_scrape( $plugin ) {
	if ( ! defined( 'WP_SANDBOX_SCRAPING' ) ) {
		define( 'WP_SANDBOX_SCRAPING', true );
	}

	wp_register_plugin_realpath( WP_PLUGIN_DIR . '/' . $plugin );
	include_once WP_PLUGIN_DIR . '/' . $plugin;
}

/**
 * Declares a helper function for adding content to the Privacy Policy Guide.
 *
 * Plugins and themes should suggest text for inclusion in the site's privacy policy.
 * The suggested text should contain information about any functionality that affects user privacy,
 * and will be shown on the Privacy Policy Guide screen.
 *
 * A plugin or theme can use this function multiple times as long as it will help to better present
 * the suggested policy content. For example modular plugins such as WooCommerse or Jetpack
 * can add or remove suggested content depending on the modules/extensions that are enabled.
 * For more information see the Plugin Handbook:
 * https://developer.wordpress.org/plugins/privacy/suggesting-text-for-the-site-privacy-policy/.
 *
 * The HTML contents of the `$policy_text` supports use of a specialized `.privacy-policy-tutorial`
 * CSS class which can be used to provide supplemental information. Any content contained within
 * HTML elements that have the `.privacy-policy-tutorial` CSS class applied will be omitted
 * from the clipboard when the section content is copied.
 *
 * Intended for use with the `'admin_init'` action.
 *
 * @since 4.9.6
 *
 * @param string $plugin_name The name of the plugin or theme that is suggesting content
 *                            for the site's privacy policy.
 * @param string $policy_text The suggested content for inclusion in the policy.
 */
function wp_add_privacy_policy_content( $plugin_name, $policy_text ) {
	if ( ! is_admin() ) {
		_doing_it_wrong(
			__FUNCTION__,
			sprintf(
				/* translators: %s: admin_init */
				__( 'The suggested privacy policy content should be added only in wp-admin by using the %s (or later) action.' ),
				'<code>admin_init</code>'
			),
			'4.9.7'
		);
		return;
	} elseif ( ! doing_action( 'admin_init' ) && ! did_action( 'admin_init' ) ) {
		_doing_it_wrong(
			__FUNCTION__,
			sprintf(
				/* translators: %s: admin_init */
				__( 'The suggested privacy policy content should be added by using the %s (or later) action. Please see the inline documentation.' ),
				'<code>admin_init</code>'
			),
			'4.9.7'
		);
		return;
	}

	if ( ! class_exists( 'WP_Privacy_Policy_Content' ) ) {
		require_once ABSPATH . 'wp-admin/includes/class-wp-privacy-policy-content.php';
	}

	WP_Privacy_Policy_Content::add( $plugin_name, $policy_text );
}

/**
 * Determines whether a plugin is technically active but was paused while
 * loading.
 *
 * For more information on this and similar theme functions, check out
 * the {@link https://developer.wordpress.org/themes/basics/conditional-tags/
 * Conditional Tags} article in the Theme Developer Handbook.
 *
 * @since 5.2.0
 *
 * @param string $plugin Path to the plugin file relative to the plugins directory.
 * @return bool True, if in the list of paused plugins. False, if not in the list.
 */
function is_plugin_paused( $plugin ) {
	if ( ! isset( $GLOBALS['_paused_plugins'] ) ) {
		return false;
	}

	if ( ! is_plugin_active( $plugin ) ) {
		return false;
	}

	list( $plugin ) = explode( '/', $plugin );

	return array_key_exists( $plugin, $GLOBALS['_paused_plugins'] );
}

/**
 * Gets the error that was recorded for a paused plugin.
 *
 * @since 5.2.0
 *
 * @param string $plugin Path to the plugin file relative to the plugins directory.
 * @return array|false Array of error information as returned by `error_get_last()`,
 *                     or false if none was recorded.
 */
function wp_get_plugin_error( $plugin ) {
	if ( ! isset( $GLOBALS['_paused_plugins'] ) ) {
		return false;
	}

	list( $plugin ) = explode( '/', $plugin );

	if ( ! array_key_exists( $plugin, $GLOBALS['_paused_plugins'] ) ) {
		return false;
	}

	return $GLOBALS['_paused_plugins'][ $plugin ];
}

/**
 * Tries to resume a single plugin.
 *
 * If a redirect was provided, we first ensure the plugin does not throw fatal
 * errors anymore.
 *
 * The way it works is by setting the redirection to the error before trying to
 * include the plugin file. If the plugin fails, then the redirection will not
 * be overwritten with the success message and the plugin will not be resumed.
 *
 * @since 5.2.0
 *
 * @param string $plugin   Single plugin to resume.
 * @param string $redirect Optional. URL to redirect to. Default empty string.
 * @return bool|WP_Error True on success, false if `$plugin` was not paused,
 *                       `WP_Error` on failure.
 */
function resume_plugin( $plugin, $redirect = '' ) {
	/*
	 * We'll override this later if the plugin could be resumed without
	 * creating a fatal error.
	 */
	if ( ! empty( $redirect ) ) {
		wp_redirect(
			add_query_arg(
				'_error_nonce',
				wp_create_nonce( 'plugin-resume-error_' . $plugin ),
				$redirect
			)
		);

		// Load the plugin to test whether it throws a fatal error.
		ob_start();
		plugin_sandbox_scrape( $plugin );
		ob_clean();
	}

	list( $extension ) = explode( '/', $plugin );

	$result = wp_paused_plugins()->delete( $extension );

	if ( ! $result ) {
		return new WP_Error(
			'could_not_resume_plugin',
			__( 'Could not resume the plugin.' )
		);
	}

	return true;
}

/**
 * Renders an admin notice in case some plugins have been paused due to errors.
 *
 * @since 5.2.0
 *
 * @global string $pagenow The filename of the current screen.
 */
function paused_plugins_notice() {
	if ( 'plugins.php' === $GLOBALS['pagenow'] ) {
		return;
	}

	if ( ! current_user_can( 'resume_plugins' ) ) {
		return;
	}

	if ( ! isset( $GLOBALS['_paused_plugins'] ) || empty( $GLOBALS['_paused_plugins'] ) ) {
		return;
	}

	printf(
		'<div class="notice notice-error"><p><strong>%s</strong><br>%s</p><p><a href="%s">%s</a></p></div>',
		__( 'One or more plugins failed to load properly.' ),
		__( 'You can find more details and make changes on the Plugins screen.' ),
		esc_url( admin_url( 'plugins.php?plugin_status=paused' ) ),
		__( 'Go to the Plugins screen' )
	);
}

/**
 * Renders an admin notice when a plugin was deactivated during an update.
 *
 * Displays an admin notice in case a plugin has been deactivated during an
 * upgrade due to incompatibility with the current version of WordPress.
 *
 * @since 5.8.0
 * @access private
 *
 * @global string $pagenow    The filename of the current screen.
 * @global string $wp_version The WordPress version string.
 */
function deactivated_plugins_notice() {
	if ( 'plugins.php' === $GLOBALS['pagenow'] ) {
		return;
	}

	if ( ! current_user_can( 'activate_plugins' ) ) {
		return;
	}

	$blog_deactivated_plugins = get_option( 'wp_force_deactivated_plugins' );
	$site_deactivated_plugins = array();

	if ( false === $blog_deactivated_plugins ) {
		// Option not in database, add an empty array to avoid extra DB queries on subsequent loads.
		update_option( 'wp_force_deactivated_plugins', array() );
	}

	if ( is_multisite() ) {
		$site_deactivated_plugins = get_site_option( 'wp_force_deactivated_plugins' );
		if ( false === $site_deactivated_plugins ) {
			// Option not in database, add an empty array to avoid extra DB queries on subsequent loads.
			update_site_option( 'wp_force_deactivated_plugins', array() );
		}
	}

	if ( empty( $blog_deactivated_plugins ) && empty( $site_deactivated_plugins ) ) {
		// No deactivated plugins.
		return;
	}

	$deactivated_plugins = array_merge( $blog_deactivated_plugins, $site_deactivated_plugins );

	foreach ( $deactivated_plugins as $plugin ) {
		if ( ! empty( $plugin['version_compatible'] ) && ! empty( $plugin['version_deactivated'] ) ) {
			$explanation = sprintf(
				/* translators: 1: Name of deactivated plugin, 2: Plugin version deactivated, 3: Current WP version, 4: Compatible plugin version. */
				__( '%1$s %2$s was deactivated due to incompatibility with WordPress %3$s, please upgrade to %1$s %4$s or later.' ),
				$plugin['plugin_name'],
				$plugin['version_deactivated'],
				$GLOBALS['wp_version'],
				$plugin['version_compatible']
			);
		} else {
			$explanation = sprintf(
				/* translators: 1: Name of deactivated plugin, 2: Plugin version deactivated, 3: Current WP version. */
				__( '%1$s %2$s was deactivated due to incompatibility with WordPress %3$s.' ),
				$plugin['plugin_name'],
				! empty( $plugin['version_deactivated'] ) ? $plugin['version_deactivated'] : '',
				$GLOBALS['wp_version'],
				$plugin['version_compatible']
			);
		}

		printf(
			'<div class="notice notice-warning"><p><strong>%s</strong><br>%s</p><p><a href="%s">%s</a></p></div>',
			sprintf(
				/* translators: %s: Name of deactivated plugin. */
				__( '%s plugin deactivated during WordPress upgrade.' ),
				$plugin['plugin_name']
			),
			$explanation,
			esc_url( admin_url( 'plugins.php?plugin_status=inactive' ) ),
			__( 'Go to the Plugins screen' )
		);
	}

	// Empty the options.
	update_option( 'wp_force_deactivated_plugins', array() );
	if ( is_multisite() ) {
		update_site_option( 'wp_force_deactivated_plugins', array() );
	}
}
