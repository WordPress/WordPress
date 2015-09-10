<?php
/**
 * WordPress Plugin Administration API
 *
 * @package WordPress
 * @subpackage Administration
 */

/**
 * Parse the plugin contents to retrieve plugin's metadata.
 *
 * The metadata of the plugin's data searches for the following in the plugin's
 * header. All plugin data must be on its own line. For plugin description, it
 * must not have any newlines or only parts of the description will be displayed
 * and the same goes for the plugin data. The below is formatted for printing.
 *
 *     /*
 *     Plugin Name: Name of Plugin
 *     Plugin URI: Link to plugin information
 *     Description: Plugin Description
 *     Author: Plugin author's name
 *     Author URI: Link to the author's web site
 *     Version: Must be set in the plugin for WordPress 2.3+
 *     Text Domain: Optional. Unique identifier, should be same as the one used in
 *    		load_plugin_textdomain()
 *     Domain Path: Optional. Only useful if the translations are located in a
 *    		folder above the plugin's base path. For example, if .mo files are
 *    		located in the locale folder then Domain Path will be "/locale/" and
 *    		must have the first slash. Defaults to the base folder the plugin is
 *    		located in.
 *     Network: Optional. Specify "Network: true" to require that a plugin is activated
 *    		across all sites in an installation. This will prevent a plugin from being
 *    		activated on a single site when Multisite is enabled.
 *      * / # Remove the space to close comment
 *
 * Plugin data returned array contains the following:
 *
 * - 'Name' - Name of the plugin, must be unique.
 * - 'Title' - Title of the plugin and the link to the plugin's web site.
 * - 'Description' - Description of what the plugin does and/or notes
 * - from the author.
 * - 'Author' - The author's name
 * - 'AuthorURI' - The authors web site address.
 * - 'Version' - The plugin version number.
 * - 'PluginURI' - Plugin web site address.
 * - 'TextDomain' - Plugin's text domain for localization.
 * - 'DomainPath' - Plugin's relative directory path to .mo files.
 * - 'Network' - Boolean. Whether the plugin can only be activated network wide.
 *
 * Some users have issues with opening large files and manipulating the contents
 * for want is usually the first 1kiB or 2kiB. This function stops pulling in
 * the plugin contents when it has all of the required plugin data.
 *
 * The first 8kiB of the file will be pulled in and if the plugin data is not
 * within that first 8kiB, then the plugin author should correct their plugin
 * and move the plugin data headers to the top.
 *
 * The plugin file is assumed to have permissions to allow for scripts to read
 * the file. This is not checked however and the file is only opened for
 * reading.
 *
 * @link https://core.trac.wordpress.org/ticket/5651 Previous Optimizations.
 * @link https://core.trac.wordpress.org/ticket/7372 Further and better Optimizations.
 *
 * @since 1.5.0
 *
 * @param string $plugin_file Path to the plugin file
 * @param bool $markup Optional. If the returned data should have HTML markup applied. Defaults to true.
 * @param bool $translate Optional. If the returned data should be translated. Defaults to true.
 * @return array See above for description.
 */
function get_plugin_data( $plugin_file, $markup = true, $translate = true ) {

	$default_headers = array(
		'Name' => 'Plugin Name',
		'PluginURI' => 'Plugin URI',
		'Version' => 'Version',
		'Description' => 'Description',
		'Author' => 'Author',
		'AuthorURI' => 'Author URI',
		'TextDomain' => 'Text Domain',
		'DomainPath' => 'Domain Path',
		'Network' => 'Network',
		// Site Wide Only is deprecated in favor of Network.
		'_sitewide' => 'Site Wide Only',
	);

	$plugin_data = get_file_data( $plugin_file, $default_headers, 'plugin' );

	// Site Wide Only is the old header for Network
	if ( ! $plugin_data['Network'] && $plugin_data['_sitewide'] ) {
		_deprecated_argument( __FUNCTION__, '3.0', sprintf( __( 'The <code>%1$s</code> plugin header is deprecated. Use <code>%2$s</code> instead.' ), 'Site Wide Only: true', 'Network: true' ) );
		$plugin_data['Network'] = $plugin_data['_sitewide'];
	}
	$plugin_data['Network'] = ( 'true' == strtolower( $plugin_data['Network'] ) );
	unset( $plugin_data['_sitewide'] );

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
 * @access private
 * @see get_plugin_data()
 */
function _get_plugin_data_markup_translate( $plugin_file, $plugin_data, $markup = true, $translate = true ) {

	// Sanitize the plugin filename to a WP_PLUGIN_DIR relative path
	$plugin_file = plugin_basename( $plugin_file );

	// Translate fields
	if ( $translate ) {
		if ( $textdomain = $plugin_data['TextDomain'] ) {
			if ( $plugin_data['DomainPath'] )
				load_plugin_textdomain( $textdomain, false, dirname( $plugin_file ) . $plugin_data['DomainPath'] );
			else
				load_plugin_textdomain( $textdomain, false, dirname( $plugin_file ) );
		} elseif ( in_array( basename( $plugin_file ), array( 'hello.php', 'akismet.php' ) ) ) {
			$textdomain = 'default';
		}
		if ( $textdomain ) {
			foreach ( array( 'Name', 'PluginURI', 'Description', 'Author', 'AuthorURI', 'Version' ) as $field )
				$plugin_data[ $field ] = translate( $plugin_data[ $field ], $textdomain );
		}
	}

	// Sanitize fields
	$allowed_tags = $allowed_tags_in_links = array(
		'abbr'    => array( 'title' => true ),
		'acronym' => array( 'title' => true ),
		'code'    => true,
		'em'      => true,
		'strong'  => true,
	);
	$allowed_tags['a'] = array( 'href' => true, 'title' => true );

	// Name is marked up inside <a> tags. Don't allow these.
	// Author is too, but some plugins have used <a> here (omitting Author URI).
	$plugin_data['Name']        = wp_kses( $plugin_data['Name'],        $allowed_tags_in_links );
	$plugin_data['Author']      = wp_kses( $plugin_data['Author'],      $allowed_tags );

	$plugin_data['Description'] = wp_kses( $plugin_data['Description'], $allowed_tags );
	$plugin_data['Version']     = wp_kses( $plugin_data['Version'],     $allowed_tags );

	$plugin_data['PluginURI']   = esc_url( $plugin_data['PluginURI'] );
	$plugin_data['AuthorURI']   = esc_url( $plugin_data['AuthorURI'] );

	$plugin_data['Title']      = $plugin_data['Name'];
	$plugin_data['AuthorName'] = $plugin_data['Author'];

	// Apply markup
	if ( $markup ) {
		if ( $plugin_data['PluginURI'] && $plugin_data['Name'] )
			$plugin_data['Title'] = '<a href="' . $plugin_data['PluginURI'] . '">' . $plugin_data['Name'] . '</a>';

		if ( $plugin_data['AuthorURI'] && $plugin_data['Author'] )
			$plugin_data['Author'] = '<a href="' . $plugin_data['AuthorURI'] . '">' . $plugin_data['Author'] . '</a>';

		$plugin_data['Description'] = wptexturize( $plugin_data['Description'] );

		if ( $plugin_data['Author'] )
			$plugin_data['Description'] .= ' <cite>' . sprintf( __('By %s.'), $plugin_data['Author'] ) . '</cite>';
	}

	return $plugin_data;
}

/**
 * Get a list of a plugin's files.
 *
 * @since 2.8.0
 *
 * @param string $plugin Plugin ID
 * @return array List of files relative to the plugin root.
 */
function get_plugin_files($plugin) {
	$plugin_file = WP_PLUGIN_DIR . '/' . $plugin;
	$dir = dirname($plugin_file);
	$plugin_files = array($plugin);
	if ( is_dir($dir) && $dir != WP_PLUGIN_DIR ) {
		$plugins_dir = @ opendir( $dir );
		if ( $plugins_dir ) {
			while (($file = readdir( $plugins_dir ) ) !== false ) {
				if ( substr($file, 0, 1) == '.' )
					continue;
				if ( is_dir( $dir . '/' . $file ) ) {
					$plugins_subdir = @ opendir( $dir . '/' . $file );
					if ( $plugins_subdir ) {
						while (($subfile = readdir( $plugins_subdir ) ) !== false ) {
							if ( substr($subfile, 0, 1) == '.' )
								continue;
							$plugin_files[] = plugin_basename("$dir/$file/$subfile");
						}
						@closedir( $plugins_subdir );
					}
				} else {
					if ( plugin_basename("$dir/$file") != $plugin )
						$plugin_files[] = plugin_basename("$dir/$file");
				}
			}
			@closedir( $plugins_dir );
		}
	}

	return $plugin_files;
}

/**
 * Check the plugins directory and retrieve all plugin files with plugin data.
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
 * @return array Key is the plugin file path and the value is an array of the plugin data.
 */
function get_plugins($plugin_folder = '') {

	if ( ! $cache_plugins = wp_cache_get('plugins', 'plugins') )
		$cache_plugins = array();

	if ( isset($cache_plugins[ $plugin_folder ]) )
		return $cache_plugins[ $plugin_folder ];

	$wp_plugins = array ();
	$plugin_root = WP_PLUGIN_DIR;
	if ( !empty($plugin_folder) )
		$plugin_root .= $plugin_folder;

	// Files in wp-content/plugins directory
	$plugins_dir = @ opendir( $plugin_root);
	$plugin_files = array();
	if ( $plugins_dir ) {
		while (($file = readdir( $plugins_dir ) ) !== false ) {
			if ( substr($file, 0, 1) == '.' )
				continue;
			if ( is_dir( $plugin_root.'/'.$file ) ) {
				$plugins_subdir = @ opendir( $plugin_root.'/'.$file );
				if ( $plugins_subdir ) {
					while (($subfile = readdir( $plugins_subdir ) ) !== false ) {
						if ( substr($subfile, 0, 1) == '.' )
							continue;
						if ( substr($subfile, -4) == '.php' )
							$plugin_files[] = "$file/$subfile";
					}
					closedir( $plugins_subdir );
				}
			} else {
				if ( substr($file, -4) == '.php' )
					$plugin_files[] = $file;
			}
		}
		closedir( $plugins_dir );
	}

	if ( empty($plugin_files) )
		return $wp_plugins;

	foreach ( $plugin_files as $plugin_file ) {
		if ( !is_readable( "$plugin_root/$plugin_file" ) )
			continue;

		$plugin_data = get_plugin_data( "$plugin_root/$plugin_file", false, false ); //Do not apply markup/translate as it'll be cached.

		if ( empty ( $plugin_data['Name'] ) )
			continue;

		$wp_plugins[plugin_basename( $plugin_file )] = $plugin_data;
	}

	uasort( $wp_plugins, '_sort_uname_callback' );

	$cache_plugins[ $plugin_folder ] = $wp_plugins;
	wp_cache_set('plugins', $cache_plugins, 'plugins');

	return $wp_plugins;
}

/**
 * Check the mu-plugins directory and retrieve all mu-plugin files with any plugin data.
 *
 * WordPress only includes mu-plugin files in the base mu-plugins directory (wp-content/mu-plugins).
 *
 * @since 3.0.0
 * @return array Key is the mu-plugin file path and the value is an array of the mu-plugin data.
 */
function get_mu_plugins() {
	$wp_plugins = array();
	// Files in wp-content/mu-plugins directory
	$plugin_files = array();

	if ( ! is_dir( WPMU_PLUGIN_DIR ) )
		return $wp_plugins;
	if ( $plugins_dir = @ opendir( WPMU_PLUGIN_DIR ) ) {
		while ( ( $file = readdir( $plugins_dir ) ) !== false ) {
			if ( substr( $file, -4 ) == '.php' )
				$plugin_files[] = $file;
		}
	} else {
		return $wp_plugins;
	}

	@closedir( $plugins_dir );

	if ( empty($plugin_files) )
		return $wp_plugins;

	foreach ( $plugin_files as $plugin_file ) {
		if ( !is_readable( WPMU_PLUGIN_DIR . "/$plugin_file" ) )
			continue;

		$plugin_data = get_plugin_data( WPMU_PLUGIN_DIR . "/$plugin_file", false, false ); //Do not apply markup/translate as it'll be cached.

		if ( empty ( $plugin_data['Name'] ) )
			$plugin_data['Name'] = $plugin_file;

		$wp_plugins[ $plugin_file ] = $plugin_data;
	}

	if ( isset( $wp_plugins['index.php'] ) && filesize( WPMU_PLUGIN_DIR . '/index.php') <= 30 ) // silence is golden
		unset( $wp_plugins['index.php'] );

	uasort( $wp_plugins, '_sort_uname_callback' );

	return $wp_plugins;
}

/**
 * Callback to sort array by a 'Name' key.
 *
 * @since 3.1.0
 * @access private
 */
function _sort_uname_callback( $a, $b ) {
	return strnatcasecmp( $a['Name'], $b['Name'] );
}

/**
 * Check the wp-content directory and retrieve all drop-ins with any plugin data.
 *
 * @since 3.0.0
 * @return array Key is the file path and the value is an array of the plugin data.
 */
function get_dropins() {
	$dropins = array();
	$plugin_files = array();

	$_dropins = _get_dropins();

	// These exist in the wp-content directory
	if ( $plugins_dir = @ opendir( WP_CONTENT_DIR ) ) {
		while ( ( $file = readdir( $plugins_dir ) ) !== false ) {
			if ( isset( $_dropins[ $file ] ) )
				$plugin_files[] = $file;
		}
	} else {
		return $dropins;
	}

	@closedir( $plugins_dir );

	if ( empty($plugin_files) )
		return $dropins;

	foreach ( $plugin_files as $plugin_file ) {
		if ( !is_readable( WP_CONTENT_DIR . "/$plugin_file" ) )
			continue;
		$plugin_data = get_plugin_data( WP_CONTENT_DIR . "/$plugin_file", false, false ); //Do not apply markup/translate as it'll be cached.
		if ( empty( $plugin_data['Name'] ) )
			$plugin_data['Name'] = $plugin_file;
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
 * @return array Key is file name. The value is an array, with the first value the
 *	purpose of the drop-in and the second value the name of the constant that must be
 *	true for the drop-in to be used, or true if no constant is required.
 */
function _get_dropins() {
	$dropins = array(
		'advanced-cache.php' => array( __( 'Advanced caching plugin.'       ), 'WP_CACHE' ), // WP_CACHE
		'db.php'             => array( __( 'Custom database class.'         ), true ), // auto on load
		'db-error.php'       => array( __( 'Custom database error message.' ), true ), // auto on error
		'install.php'        => array( __( 'Custom install script.'         ), true ), // auto on install
		'maintenance.php'    => array( __( 'Custom maintenance message.'    ), true ), // auto on maintenance
		'object-cache.php'   => array( __( 'External object cache.'         ), true ), // auto on load
	);

	if ( is_multisite() ) {
		$dropins['sunrise.php'       ] = array( __( 'Executed before Multisite is loaded.' ), 'SUNRISE' ); // SUNRISE
		$dropins['blog-deleted.php'  ] = array( __( 'Custom site deleted message.'   ), true ); // auto on deleted blog
		$dropins['blog-inactive.php' ] = array( __( 'Custom site inactive message.'  ), true ); // auto on inactive blog
		$dropins['blog-suspended.php'] = array( __( 'Custom site suspended message.' ), true ); // auto on archived or spammed blog
	}

	return $dropins;
}

/**
 * Check whether the plugin is active by checking the active_plugins list.
 *
 * @since 2.5.0
 *
 * @param string $plugin Base plugin path from plugins directory.
 * @return bool True, if in the active plugins list. False, not in the list.
 */
function is_plugin_active( $plugin ) {
	return in_array( $plugin, (array) get_option( 'active_plugins', array() ) ) || is_plugin_active_for_network( $plugin );
}

/**
 * Check whether the plugin is inactive.
 *
 * Reverse of is_plugin_active(). Used as a callback.
 *
 * @since 3.1.0
 * @see is_plugin_active()
 *
 * @param string $plugin Base plugin path from plugins directory.
 * @return bool True if inactive. False if active.
 */
function is_plugin_inactive( $plugin ) {
	return ! is_plugin_active( $plugin );
}

/**
 * Check whether the plugin is active for the entire network.
 *
 * @since 3.0.0
 *
 * @param string $plugin Base plugin path from plugins directory.
 * @return bool True, if active for the network, otherwise false.
 */
function is_plugin_active_for_network( $plugin ) {
	if ( !is_multisite() )
		return false;

	$plugins = get_site_option( 'active_sitewide_plugins');
	if ( isset($plugins[$plugin]) )
		return true;

	return false;
}

/**
 * Checks for "Network: true" in the plugin header to see if this should
 * be activated only as a network wide plugin. The plugin would also work
 * when Multisite is not enabled.
 *
 * Checks for "Site Wide Only: true" for backwards compatibility.
 *
 * @since 3.0.0
 *
 * @param string $plugin Plugin to check
 * @return bool True if plugin is network only, false otherwise.
 */
function is_network_only_plugin( $plugin ) {
	$plugin_data = get_plugin_data( WP_PLUGIN_DIR . '/' . $plugin );
	if ( $plugin_data )
		return $plugin_data['Network'];
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
 *
 * @param string $plugin Plugin path to main plugin file with plugin data.
 * @param string $redirect Optional. URL to redirect to.
 * @param bool $network_wide Whether to enable the plugin for all sites in the
 *   network or just the current site. Multisite only. Default is false.
 * @param bool $silent Prevent calling activation hooks. Optional, default is false.
 * @return WP_Error|null WP_Error on invalid file or null on success.
 */
function activate_plugin( $plugin, $redirect = '', $network_wide = false, $silent = false ) {
	$plugin = plugin_basename( trim( $plugin ) );

	if ( is_multisite() && ( $network_wide || is_network_only_plugin($plugin) ) ) {
		$network_wide = true;
		$current = get_site_option( 'active_sitewide_plugins', array() );
		$_GET['networkwide'] = 1; // Back compat for plugins looking for this value.
	} else {
		$current = get_option( 'active_plugins', array() );
	}

	$valid = validate_plugin($plugin);
	if ( is_wp_error($valid) )
		return $valid;

	if ( ( $network_wide && ! isset( $current[ $plugin ] ) ) || ( ! $network_wide && ! in_array( $plugin, $current ) ) ) {
		if ( !empty($redirect) )
			wp_redirect(add_query_arg('_error_nonce', wp_create_nonce('plugin-activation-error_' . $plugin), $redirect)); // we'll override this later if the plugin can be included without fatal error
		ob_start();
		wp_register_plugin_realpath( WP_PLUGIN_DIR . '/' . $plugin );
		$_wp_plugin_file = $plugin;
		include_once( WP_PLUGIN_DIR . '/' . $plugin );
		$plugin = $_wp_plugin_file; // Avoid stomping of the $plugin variable in a plugin.

		if ( ! $silent ) {
			/**
			 * Fires before a plugin is activated.
			 *
			 * If a plugin is silently activated (such as during an update),
			 * this hook does not fire.
			 *
			 * @since 2.9.0
			 *
			 * @param string $plugin       Plugin path to main plugin file with plugin data.
			 * @param bool   $network_wide Whether to enable the plugin for all sites in the network
			 *                             or just the current site. Multisite only. Default is false.
			 */
			do_action( 'activate_plugin', $plugin, $network_wide );

			/**
			 * Fires as a specific plugin is being activated.
			 *
			 * This hook is the "activation" hook used internally by
			 * {@see register_activation_hook()}. The dynamic portion of the
			 * hook name, `$plugin`, refers to the plugin basename.
			 *
			 * If a plugin is silently activated (such as during an update),
			 * this hook does not fire.
			 *
			 * @since 2.0.0
			 *
			 * @param bool $network_wide Whether to enable the plugin for all sites in the network
			 *                           or just the current site. Multisite only. Default is false.
			 */
			do_action( 'activate_' . $plugin, $network_wide );
		}

		if ( $network_wide ) {
			$current = get_site_option( 'active_sitewide_plugins', array() );
			$current[$plugin] = time();
			update_site_option( 'active_sitewide_plugins', $current );
		} else {
			$current = get_option( 'active_plugins', array() );
			$current[] = $plugin;
			sort($current);
			update_option('active_plugins', $current);
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
			 * @param string $plugin       Plugin path to main plugin file with plugin data.
			 * @param bool   $network_wide Whether to enable the plugin for all sites in the network
			 *                             or just the current site. Multisite only. Default is false.
			 */
			do_action( 'activated_plugin', $plugin, $network_wide );
		}

		if ( ob_get_length() > 0 ) {
			$output = ob_get_clean();
			return new WP_Error('unexpected_output', __('The plugin generated unexpected output.'), $output);
		}
		ob_end_clean();
	}

	return null;
}

/**
 * Deactivate a single plugin or multiple plugins.
 *
 * The deactivation hook is disabled by the plugin upgrader by using the $silent
 * parameter.
 *
 * @since 2.5.0
 *
 * @param string|array $plugins Single plugin or list of plugins to deactivate.
 * @param bool $silent Prevent calling deactivation hooks. Default is false.
 * @param mixed $network_wide Whether to deactivate the plugin for all sites in the network.
 * 	A value of null (the default) will deactivate plugins for both the site and the network.
 */
function deactivate_plugins( $plugins, $silent = false, $network_wide = null ) {
	if ( is_multisite() )
		$network_current = get_site_option( 'active_sitewide_plugins', array() );
	$current = get_option( 'active_plugins', array() );
	$do_blog = $do_network = false;

	foreach ( (array) $plugins as $plugin ) {
		$plugin = plugin_basename( trim( $plugin ) );
		if ( ! is_plugin_active($plugin) )
			continue;

		$network_deactivating = false !== $network_wide && is_plugin_active_for_network( $plugin );

		if ( ! $silent ) {
			/**
			 * Fires before a plugin is deactivated.
			 *
			 * If a plugin is silently deactivated (such as during an update),
			 * this hook does not fire.
			 *
			 * @since 2.9.0
			 *
			 * @param string $plugin               Plugin path to main plugin file with plugin data.
			 * @param bool   $network_deactivating Whether the plugin is deactivated for all sites in the network
			 *                                     or just the current site. Multisite only. Default is false.
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
			$key = array_search( $plugin, $current );
			if ( false !== $key ) {
				$do_blog = true;
				unset( $current[ $key ] );
			}
		}

		if ( ! $silent ) {
			/**
			 * Fires as a specific plugin is being deactivated.
			 *
			 * This hook is the "deactivation" hook used internally by
			 * {@see register_deactivation_hook()}. The dynamic portion of the
			 * hook name, `$plugin`, refers to the plugin basename.
			 *
			 * If a plugin is silently deactivated (such as during an update),
			 * this hook does not fire.
			 *
			 * @since 2.0.0
			 *
			 * @param bool $network_deactivating Whether the plugin is deactivated for all sites in the network
			 *                                   or just the current site. Multisite only. Default is false.
			 */
			do_action( 'deactivate_' . $plugin, $network_deactivating );

			/**
			 * Fires after a plugin is deactivated.
			 *
			 * If a plugin is silently deactivated (such as during an update),
			 * this hook does not fire.
			 *
			 * @since 2.9.0
			 *
			 * @param string $plugin               Plugin basename.
			 * @param bool   $network_deactivating Whether the plugin is deactivated for all sites in the network
			 *                                     or just the current site. Multisite only. Default false.
			 */
			do_action( 'deactivated_plugin', $plugin, $network_deactivating );
		}
	}

	if ( $do_blog )
		update_option('active_plugins', $current);
	if ( $do_network )
		update_site_option( 'active_sitewide_plugins', $network_current );
}

/**
 * Activate multiple plugins.
 *
 * When WP_Error is returned, it does not mean that one of the plugins had
 * errors. It means that one or more of the plugins file path was invalid.
 *
 * The execution will be halted as soon as one of the plugins has an error.
 *
 * @since 2.6.0
 *
 * @param string|array $plugins Single plugin or list of plugins to activate.
 * @param string $redirect Redirect to page after successful activation.
 * @param bool $network_wide Whether to enable the plugin for all sites in the network.
 * @param bool $silent Prevent calling activation hooks. Default is false.
 * @return bool|WP_Error True when finished or WP_Error if there were errors during a plugin activation.
 */
function activate_plugins( $plugins, $redirect = '', $network_wide = false, $silent = false ) {
	if ( !is_array($plugins) )
		$plugins = array($plugins);

	$errors = array();
	foreach ( $plugins as $plugin ) {
		if ( !empty($redirect) )
			$redirect = add_query_arg('plugin', $plugin, $redirect);
		$result = activate_plugin($plugin, $redirect, $network_wide, $silent);
		if ( is_wp_error($result) )
			$errors[$plugin] = $result;
	}

	if ( !empty($errors) )
		return new WP_Error('plugins_invalid', __('One of the plugins is invalid.'), $errors);

	return true;
}

/**
 * Remove directory and files of a plugin for a list of plugins.
 *
 * @since 2.6.0
 *
 * @global WP_Filesystem_Base $wp_filesystem
 *
 * @param array  $plugins    List of plugins to delete.
 * @param string $deprecated Deprecated.
 * @return bool|null|WP_Error True on success, false is $plugins is empty, WP_Error on failure.
 *                            Null if filesystem credentials are required to proceed.
 */
function delete_plugins( $plugins, $deprecated = '' ) {
	global $wp_filesystem;

	if ( empty($plugins) )
		return false;

	$checked = array();
	foreach ( $plugins as $plugin )
		$checked[] = 'checked[]=' . $plugin;

	ob_start();
	$url = wp_nonce_url('plugins.php?action=delete-selected&verify-delete=1&' . implode('&', $checked), 'bulk-plugins');
	if ( false === ($credentials = request_filesystem_credentials($url)) ) {
		$data = ob_get_clean();

		if ( ! empty($data) ){
			include_once( ABSPATH . 'wp-admin/admin-header.php');
			echo $data;
			include( ABSPATH . 'wp-admin/admin-footer.php');
			exit;
		}
		return;
	}

	if ( ! WP_Filesystem($credentials) ) {
		request_filesystem_credentials($url, '', true); //Failed to connect, Error and request again
		$data = ob_get_clean();

		if ( ! empty($data) ){
			include_once( ABSPATH . 'wp-admin/admin-header.php');
			echo $data;
			include( ABSPATH . 'wp-admin/admin-footer.php');
			exit;
		}
		return;
	}

	if ( ! is_object($wp_filesystem) )
		return new WP_Error('fs_unavailable', __('Could not access filesystem.'));

	if ( is_wp_error($wp_filesystem->errors) && $wp_filesystem->errors->get_error_code() )
		return new WP_Error('fs_error', __('Filesystem error.'), $wp_filesystem->errors);

	// Get the base plugin folder.
	$plugins_dir = $wp_filesystem->wp_plugins_dir();
	if ( empty( $plugins_dir ) ) {
		return new WP_Error( 'fs_no_plugins_dir', __( 'Unable to locate WordPress Plugin directory.' ) );
	}

	$plugins_dir = trailingslashit( $plugins_dir );

	$plugin_translations = wp_get_installed_translations( 'plugins' );

	$errors = array();

	foreach ( $plugins as $plugin_file ) {
		// Run Uninstall hook.
		if ( is_uninstallable_plugin( $plugin_file ) ) {
			uninstall_plugin($plugin_file);
		}

		$this_plugin_dir = trailingslashit( dirname( $plugins_dir . $plugin_file ) );
		// If plugin is in its own directory, recursively delete the directory.
		if ( strpos( $plugin_file, '/' ) && $this_plugin_dir != $plugins_dir ) { //base check on if plugin includes directory separator AND that it's not the root plugin folder
			$deleted = $wp_filesystem->delete( $this_plugin_dir, true );
		} else {
			$deleted = $wp_filesystem->delete( $plugins_dir . $plugin_file );
		}

		if ( ! $deleted ) {
			$errors[] = $plugin_file;
			continue;
		}

		// Remove language files, silently.
		$plugin_slug = dirname( $plugin_file );
		if ( '.' !== $plugin_slug && ! empty( $plugin_translations[ $plugin_slug ] ) ) {
			$translations = $plugin_translations[ $plugin_slug ];

			foreach ( $translations as $translation => $data ) {
				$wp_filesystem->delete( WP_LANG_DIR . '/plugins/' . $plugin_slug . '-' . $translation . '.po' );
				$wp_filesystem->delete( WP_LANG_DIR . '/plugins/' . $plugin_slug . '-' . $translation . '.mo' );
			}
		}
	}

	// Remove deleted plugins from the plugin updates list.
	if ( $current = get_site_transient('update_plugins') ) {
		// Don't remove the plugins that weren't deleted.
		$deleted = array_diff( $plugins, $errors );

		foreach ( $deleted as $plugin_file ) {
			unset( $current->response[ $plugin_file ] );
		}

		set_site_transient( 'update_plugins', $current );
	}

	if ( ! empty($errors) )
		return new WP_Error('could_not_remove_plugin', sprintf(__('Could not fully remove the plugin(s) %s.'), implode(', ', $errors)) );

	return true;
}

/**
 * Validate active plugins
 *
 * Validate all active plugins, deactivates invalid and
 * returns an array of deactivated ones.
 *
 * @since 2.5.0
 * @return array invalid plugins, plugin as key, error as value
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
		$plugins = array_merge( $plugins, array_keys( $network_plugins ) );
	}

	if ( empty( $plugins ) )
		return array();

	$invalid = array();

	// Invalid plugins get deactivated.
	foreach ( $plugins as $plugin ) {
		$result = validate_plugin( $plugin );
		if ( is_wp_error( $result ) ) {
			$invalid[$plugin] = $result;
			deactivate_plugins( $plugin, true );
		}
	}
	return $invalid;
}

/**
 * Validate the plugin path.
 *
 * Checks that the file exists and {@link validate_file() is valid file}.
 *
 * @since 2.5.0
 *
 * @param string $plugin Plugin Path
 * @return WP_Error|int 0 on success, WP_Error on failure.
 */
function validate_plugin($plugin) {
	if ( validate_file($plugin) )
		return new WP_Error('plugin_invalid', __('Invalid plugin path.'));
	if ( ! file_exists(WP_PLUGIN_DIR . '/' . $plugin) )
		return new WP_Error('plugin_not_found', __('Plugin file does not exist.'));

	$installed_plugins = get_plugins();
	if ( ! isset($installed_plugins[$plugin]) )
		return new WP_Error('no_plugin_header', __('The plugin does not have a valid header.'));
	return 0;
}

/**
 * Whether the plugin can be uninstalled.
 *
 * @since 2.7.0
 *
 * @param string $plugin Plugin path to check.
 * @return bool Whether plugin can be uninstalled.
 */
function is_uninstallable_plugin($plugin) {
	$file = plugin_basename($plugin);

	$uninstallable_plugins = (array) get_option('uninstall_plugins');
	if ( isset( $uninstallable_plugins[$file] ) || file_exists( WP_PLUGIN_DIR . '/' . dirname($file) . '/uninstall.php' ) )
		return true;

	return false;
}

/**
 * Uninstall a single plugin.
 *
 * Calls the uninstall hook, if it is available.
 *
 * @since 2.7.0
 *
 * @param string $plugin Relative plugin path from Plugin Directory.
 * @return true True if a plugin's uninstall.php file has been found and included.
 */
function uninstall_plugin($plugin) {
	$file = plugin_basename($plugin);

	$uninstallable_plugins = (array) get_option('uninstall_plugins');
	if ( file_exists( WP_PLUGIN_DIR . '/' . dirname($file) . '/uninstall.php' ) ) {
		if ( isset( $uninstallable_plugins[$file] ) ) {
			unset($uninstallable_plugins[$file]);
			update_option('uninstall_plugins', $uninstallable_plugins);
		}
		unset($uninstallable_plugins);

		define('WP_UNINSTALL_PLUGIN', $file);
		wp_register_plugin_realpath( WP_PLUGIN_DIR . '/' . dirname( $file ) );
		include( WP_PLUGIN_DIR . '/' . dirname($file) . '/uninstall.php' );

		return true;
	}

	if ( isset( $uninstallable_plugins[$file] ) ) {
		$callable = $uninstallable_plugins[$file];
		unset($uninstallable_plugins[$file]);
		update_option('uninstall_plugins', $uninstallable_plugins);
		unset($uninstallable_plugins);

		wp_register_plugin_realpath( WP_PLUGIN_DIR . '/' . $file );
		include( WP_PLUGIN_DIR . '/' . $file );

		add_action( 'uninstall_' . $file, $callable );

		/**
		 * Fires in uninstall_plugin() once the plugin has been uninstalled.
		 *
		 * The action concatenates the 'uninstall_' prefix with the basename of the
		 * plugin passed to {@see uninstall_plugin()} to create a dynamically-named action.
		 *
		 * @since 2.7.0
		 */
		do_action( 'uninstall_' . $file );
	}
}

//
// Menu
//

/**
 * Add a top level menu page
 *
 * This function takes a capability which will be used to determine whether
 * or not a page is included in the menu.
 *
 * The function which is hooked in to handle the output of the page must check
 * that the user has the required capability as well.
 *
 * @global array $menu
 * @global array $admin_page_hooks
 * @global array $_registered_pages
 * @global array $_parent_pages
 *
 * @param string $page_title The text to be displayed in the title tags of the page when the menu is selected
 * @param string $menu_title The text to be used for the menu
 * @param string $capability The capability required for this menu to be displayed to the user.
 * @param string $menu_slug The slug name to refer to this menu by (should be unique for this menu)
 * @param callback $function The function to be called to output the content for this page.
 * @param string $icon_url The url to the icon to be used for this menu.
 *     * Pass a base64-encoded SVG using a data URI, which will be colored to match the color scheme.
 *       This should begin with 'data:image/svg+xml;base64,'.
 *     * Pass the name of a Dashicons helper class to use a font icon, e.g. 'dashicons-chart-pie'.
 *     * Pass 'none' to leave div.wp-menu-image empty so an icon can be added via CSS.
 * @param int $position The position in the menu order this one should appear
 *
 * @return string The resulting page's hook_suffix
 */
function add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function = '', $icon_url = '', $position = null ) {
	global $menu, $admin_page_hooks, $_registered_pages, $_parent_pages;

	$menu_slug = plugin_basename( $menu_slug );

	$admin_page_hooks[$menu_slug] = sanitize_title( $menu_title );

	$hookname = get_plugin_page_hookname( $menu_slug, '' );

	if ( !empty( $function ) && !empty( $hookname ) && current_user_can( $capability ) )
		add_action( $hookname, $function );

	if ( empty($icon_url) ) {
		$icon_url = 'dashicons-admin-generic';
		$icon_class = 'menu-icon-generic ';
	} else {
		$icon_url = set_url_scheme( $icon_url );
		$icon_class = '';
	}

	$new_menu = array( $menu_title, $capability, $menu_slug, $page_title, 'menu-top ' . $icon_class . $hookname, $hookname, $icon_url );

	if ( null === $position )
		$menu[] = $new_menu;
	else
		$menu[$position] = $new_menu;

	$_registered_pages[$hookname] = true;

	// No parent as top level
	$_parent_pages[$menu_slug] = false;

	return $hookname;
}

/**
 * Add a top level menu page in the 'objects' section
 *
 * This function takes a capability which will be used to determine whether
 * or not a page is included in the menu.
 *
 * The function which is hooked in to handle the output of the page must check
 * that the user has the required capability as well.
 *
 * @global int $_wp_last_object_menu
 *
 * @param string $page_title The text to be displayed in the title tags of the page when the menu is selected
 * @param string $menu_title The text to be used for the menu
 * @param string $capability The capability required for this menu to be displayed to the user.
 * @param string $menu_slug The slug name to refer to this menu by (should be unique for this menu)
 * @param callback $function The function to be called to output the content for this page.
 * @param string $icon_url The url to the icon to be used for this menu
 *
 * @return string The resulting page's hook_suffix
 */
function add_object_page( $page_title, $menu_title, $capability, $menu_slug, $function = '', $icon_url = '') {
	global $_wp_last_object_menu;

	$_wp_last_object_menu++;

	return add_menu_page($page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $_wp_last_object_menu);
}

/**
 * Add a top level menu page in the 'utility' section
 *
 * This function takes a capability which will be used to determine whether
 * or not a page is included in the menu.
 *
 * The function which is hooked in to handle the output of the page must check
 * that the user has the required capability as well.
 *
 * @global int $_wp_last_utility_menu
 *
 * @param string $page_title The text to be displayed in the title tags of the page when the menu is selected
 * @param string $menu_title The text to be used for the menu
 * @param string $capability The capability required for this menu to be displayed to the user.
 * @param string $menu_slug The slug name to refer to this menu by (should be unique for this menu)
 * @param callback $function The function to be called to output the content for this page.
 * @param string $icon_url The url to the icon to be used for this menu
 *
 * @return string The resulting page's hook_suffix
 */
function add_utility_page( $page_title, $menu_title, $capability, $menu_slug, $function = '', $icon_url = '') {
	global $_wp_last_utility_menu;

	$_wp_last_utility_menu++;

	return add_menu_page($page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $_wp_last_utility_menu);
}

/**
 * Add a sub menu page
 *
 * This function takes a capability which will be used to determine whether
 * or not a page is included in the menu.
 *
 * The function which is hooked in to handle the output of the page must check
 * that the user has the required capability as well.
 *
 * @global array $submenu
 * @global array $menu
 * @global type $_wp_real_parent_file
 * @global bool $_wp_submenu_nopriv
 * @global array $_registered_pages
 * @global array $_parent_pages
 *
 * @param string $parent_slug The slug name for the parent menu (or the file name of a standard WordPress admin page)
 * @param string $page_title The text to be displayed in the title tags of the page when the menu is selected
 * @param string $menu_title The text to be used for the menu
 * @param string $capability The capability required for this menu to be displayed to the user.
 * @param string $menu_slug The slug name to refer to this menu by (should be unique for this menu)
 * @param callback $function The function to be called to output the content for this page.
 *
 * @return false|string The resulting page's hook_suffix, or false if the user does not have the capability required.
 */
function add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function = '' ) {
	global $submenu, $menu, $_wp_real_parent_file, $_wp_submenu_nopriv,
		$_registered_pages, $_parent_pages;

	$menu_slug = plugin_basename( $menu_slug );
	$parent_slug = plugin_basename( $parent_slug);

	if ( isset( $_wp_real_parent_file[$parent_slug] ) )
		$parent_slug = $_wp_real_parent_file[$parent_slug];

	if ( !current_user_can( $capability ) ) {
		$_wp_submenu_nopriv[$parent_slug][$menu_slug] = true;
		return false;
	}

	/*
	 * If the parent doesn't already have a submenu, add a link to the parent
	 * as the first item in the submenu. If the submenu file is the same as the
	 * parent file someone is trying to link back to the parent manually. In
	 * this case, don't automatically add a link back to avoid duplication.
	 */
	if (!isset( $submenu[$parent_slug] ) && $menu_slug != $parent_slug ) {
		foreach ( (array)$menu as $parent_menu ) {
			if ( $parent_menu[2] == $parent_slug && current_user_can( $parent_menu[1] ) )
				$submenu[$parent_slug][] = array_slice( $parent_menu, 0, 4 );
		}
	}

	$submenu[$parent_slug][] = array ( $menu_title, $capability, $menu_slug, $page_title );

	$hookname = get_plugin_page_hookname( $menu_slug, $parent_slug);
	if (!empty ( $function ) && !empty ( $hookname ))
		add_action( $hookname, $function );

	$_registered_pages[$hookname] = true;

	/*
	 * Backward-compatibility for plugins using add_management page.
	 * See wp-admin/admin.php for redirect from edit.php to tools.php
	 */
	if ( 'tools.php' == $parent_slug )
		$_registered_pages[get_plugin_page_hookname( $menu_slug, 'edit.php')] = true;

	// No parent as top level.
	$_parent_pages[$menu_slug] = $parent_slug;

	return $hookname;
}

/**
 * Add sub menu page to the tools main menu.
 *
 * This function takes a capability which will be used to determine whether
 * or not a page is included in the menu.
 *
 * The function which is hooked in to handle the output of the page must check
 * that the user has the required capability as well.
 *
 * @param string $page_title The text to be displayed in the title tags of the page when the menu is selected
 * @param string $menu_title The text to be used for the menu
 * @param string $capability The capability required for this menu to be displayed to the user.
 * @param string $menu_slug The slug name to refer to this menu by (should be unique for this menu)
 * @param callback $function The function to be called to output the content for this page.
 *
 * @return false|string The resulting page's hook_suffix, or false if the user does not have the capability required.
 */
function add_management_page( $page_title, $menu_title, $capability, $menu_slug, $function = '' ) {
	return add_submenu_page( 'tools.php', $page_title, $menu_title, $capability, $menu_slug, $function );
}

/**
 * Add sub menu page to the options main menu.
 *
 * This function takes a capability which will be used to determine whether
 * or not a page is included in the menu.
 *
 * The function which is hooked in to handle the output of the page must check
 * that the user has the required capability as well.
 *
 * @param string $page_title The text to be displayed in the title tags of the page when the menu is selected
 * @param string $menu_title The text to be used for the menu
 * @param string $capability The capability required for this menu to be displayed to the user.
 * @param string $menu_slug The slug name to refer to this menu by (should be unique for this menu)
 * @param callback $function The function to be called to output the content for this page.
 *
 * @return false|string The resulting page's hook_suffix, or false if the user does not have the capability required.
 */
function add_options_page( $page_title, $menu_title, $capability, $menu_slug, $function = '' ) {
	return add_submenu_page( 'options-general.php', $page_title, $menu_title, $capability, $menu_slug, $function );
}

/**
 * Add sub menu page to the themes main menu.
 *
 * This function takes a capability which will be used to determine whether
 * or not a page is included in the menu.
 *
 * The function which is hooked in to handle the output of the page must check
 * that the user has the required capability as well.
 *
 * @param string $page_title The text to be displayed in the title tags of the page when the menu is selected
 * @param string $menu_title The text to be used for the menu
 * @param string $capability The capability required for this menu to be displayed to the user.
 * @param string $menu_slug The slug name to refer to this menu by (should be unique for this menu)
 * @param callback $function The function to be called to output the content for this page.
 *
 * @return false|string The resulting page's hook_suffix, or false if the user does not have the capability required.
 */
function add_theme_page( $page_title, $menu_title, $capability, $menu_slug, $function = '' ) {
	return add_submenu_page( 'themes.php', $page_title, $menu_title, $capability, $menu_slug, $function );
}

/**
 * Add sub menu page to the plugins main menu.
 *
 * This function takes a capability which will be used to determine whether
 * or not a page is included in the menu.
 *
 * The function which is hooked in to handle the output of the page must check
 * that the user has the required capability as well.
 *
 * @param string $page_title The text to be displayed in the title tags of the page when the menu is selected
 * @param string $menu_title The text to be used for the menu
 * @param string $capability The capability required for this menu to be displayed to the user.
 * @param string $menu_slug The slug name to refer to this menu by (should be unique for this menu)
 * @param callback $function The function to be called to output the content for this page.
 *
 * @return false|string The resulting page's hook_suffix, or false if the user does not have the capability required.
 */
function add_plugins_page( $page_title, $menu_title, $capability, $menu_slug, $function = '' ) {
	return add_submenu_page( 'plugins.php', $page_title, $menu_title, $capability, $menu_slug, $function );
}

/**
 * Add sub menu page to the Users/Profile main menu.
 *
 * This function takes a capability which will be used to determine whether
 * or not a page is included in the menu.
 *
 * The function which is hooked in to handle the output of the page must check
 * that the user has the required capability as well.
 *
 * @param string $page_title The text to be displayed in the title tags of the page when the menu is selected
 * @param string $menu_title The text to be used for the menu
 * @param string $capability The capability required for this menu to be displayed to the user.
 * @param string $menu_slug The slug name to refer to this menu by (should be unique for this menu)
 * @param callback $function The function to be called to output the content for this page.
 *
 * @return false|string The resulting page's hook_suffix, or false if the user does not have the capability required.
 */
function add_users_page( $page_title, $menu_title, $capability, $menu_slug, $function = '' ) {
	if ( current_user_can('edit_users') )
		$parent = 'users.php';
	else
		$parent = 'profile.php';
	return add_submenu_page( $parent, $page_title, $menu_title, $capability, $menu_slug, $function );
}
/**
 * Add sub menu page to the Dashboard main menu.
 *
 * This function takes a capability which will be used to determine whether
 * or not a page is included in the menu.
 *
 * The function which is hooked in to handle the output of the page must check
 * that the user has the required capability as well.
 *
 * @param string $page_title The text to be displayed in the title tags of the page when the menu is selected
 * @param string $menu_title The text to be used for the menu
 * @param string $capability The capability required for this menu to be displayed to the user.
 * @param string $menu_slug The slug name to refer to this menu by (should be unique for this menu)
 * @param callback $function The function to be called to output the content for this page.
 *
 * @return false|string The resulting page's hook_suffix, or false if the user does not have the capability required.
 */
function add_dashboard_page( $page_title, $menu_title, $capability, $menu_slug, $function = '' ) {
	return add_submenu_page( 'index.php', $page_title, $menu_title, $capability, $menu_slug, $function );
}

/**
 * Add sub menu page to the posts main menu.
 *
 * This function takes a capability which will be used to determine whether
 * or not a page is included in the menu.
 *
 * The function which is hooked in to handle the output of the page must check
 * that the user has the required capability as well.
 *
 * @param string $page_title The text to be displayed in the title tags of the page when the menu is selected
 * @param string $menu_title The text to be used for the menu
 * @param string $capability The capability required for this menu to be displayed to the user.
 * @param string $menu_slug The slug name to refer to this menu by (should be unique for this menu)
 * @param callback $function The function to be called to output the content for this page.
 *
 * @return false|string The resulting page's hook_suffix, or false if the user does not have the capability required.
 */
function add_posts_page( $page_title, $menu_title, $capability, $menu_slug, $function = '' ) {
	return add_submenu_page( 'edit.php', $page_title, $menu_title, $capability, $menu_slug, $function );
}

/**
 * Add sub menu page to the media main menu.
 *
 * This function takes a capability which will be used to determine whether
 * or not a page is included in the menu.
 *
 * The function which is hooked in to handle the output of the page must check
 * that the user has the required capability as well.
 *
 * @param string $page_title The text to be displayed in the title tags of the page when the menu is selected
 * @param string $menu_title The text to be used for the menu
 * @param string $capability The capability required for this menu to be displayed to the user.
 * @param string $menu_slug The slug name to refer to this menu by (should be unique for this menu)
 * @param callback $function The function to be called to output the content for this page.
 *
 * @return false|string The resulting page's hook_suffix, or false if the user does not have the capability required.
 */
function add_media_page( $page_title, $menu_title, $capability, $menu_slug, $function = '' ) {
	return add_submenu_page( 'upload.php', $page_title, $menu_title, $capability, $menu_slug, $function );
}

/**
 * Add sub menu page to the links main menu.
 *
 * This function takes a capability which will be used to determine whether
 * or not a page is included in the menu.
 *
 * The function which is hooked in to handle the output of the page must check
 * that the user has the required capability as well.
 *
 * @param string $page_title The text to be displayed in the title tags of the page when the menu is selected
 * @param string $menu_title The text to be used for the menu
 * @param string $capability The capability required for this menu to be displayed to the user.
 * @param string $menu_slug The slug name to refer to this menu by (should be unique for this menu)
 * @param callback $function The function to be called to output the content for this page.
 *
 * @return false|string The resulting page's hook_suffix, or false if the user does not have the capability required.
 */
function add_links_page( $page_title, $menu_title, $capability, $menu_slug, $function = '' ) {
	return add_submenu_page( 'link-manager.php', $page_title, $menu_title, $capability, $menu_slug, $function );
}

/**
 * Add sub menu page to the pages main menu.
 *
 * This function takes a capability which will be used to determine whether
 * or not a page is included in the menu.
 *
 * The function which is hooked in to handle the output of the page must check
 * that the user has the required capability as well.
 *
 * @param string $page_title The text to be displayed in the title tags of the page when the menu is selected
 * @param string $menu_title The text to be used for the menu
 * @param string $capability The capability required for this menu to be displayed to the user.
 * @param string $menu_slug The slug name to refer to this menu by (should be unique for this menu)
 * @param callback $function The function to be called to output the content for this page.
 *
 * @return false|string The resulting page's hook_suffix, or false if the user does not have the capability required.
*/
function add_pages_page( $page_title, $menu_title, $capability, $menu_slug, $function = '' ) {
	return add_submenu_page( 'edit.php?post_type=page', $page_title, $menu_title, $capability, $menu_slug, $function );
}

/**
 * Add sub menu page to the comments main menu.
 *
 * This function takes a capability which will be used to determine whether
 * or not a page is included in the menu.
 *
 * The function which is hooked in to handle the output of the page must check
 * that the user has the required capability as well.
 *
 * @param string $page_title The text to be displayed in the title tags of the page when the menu is selected
 * @param string $menu_title The text to be used for the menu
 * @param string $capability The capability required for this menu to be displayed to the user.
 * @param string $menu_slug The slug name to refer to this menu by (should be unique for this menu)
 * @param callback $function The function to be called to output the content for this page.
 *
 * @return false|string The resulting page's hook_suffix, or false if the user does not have the capability required.
*/
function add_comments_page( $page_title, $menu_title, $capability, $menu_slug, $function = '' ) {
	return add_submenu_page( 'edit-comments.php', $page_title, $menu_title, $capability, $menu_slug, $function );
}

/**
 * Remove a top level admin menu
 *
 * @since 3.1.0
 *
 * @global array $menu
 *
 * @param string $menu_slug The slug of the menu
 * @return array|bool The removed menu on success, False if not found
 */
function remove_menu_page( $menu_slug ) {
	global $menu;

	foreach ( $menu as $i => $item ) {
		if ( $menu_slug == $item[2] ) {
			unset( $menu[$i] );
			return $item;
		}
	}

	return false;
}

/**
 * Remove an admin submenu
 *
 * @since 3.1.0
 *
 * @global array $submenu
 *
 * @param string $menu_slug The slug for the parent menu
 * @param string $submenu_slug The slug of the submenu
 * @return array|bool The removed submenu on success, False if not found
 */
function remove_submenu_page( $menu_slug, $submenu_slug ) {
	global $submenu;

	if ( !isset( $submenu[$menu_slug] ) )
		return false;

	foreach ( $submenu[$menu_slug] as $i => $item ) {
		if ( $submenu_slug == $item[2] ) {
			unset( $submenu[$menu_slug][$i] );
			return $item;
		}
	}

	return false;
}

/**
 * Get the url to access a particular menu page based on the slug it was registered with.
 *
 * If the slug hasn't been registered properly no url will be returned
 *
 * @since 3.0.0
 *
 * @global array $_parent_pages
 *
 * @param string $menu_slug The slug name to refer to this menu by (should be unique for this menu)
 * @param bool $echo Whether or not to echo the url - default is true
 * @return string the url
 */
function menu_page_url($menu_slug, $echo = true) {
	global $_parent_pages;

	if ( isset( $_parent_pages[$menu_slug] ) ) {
		$parent_slug = $_parent_pages[$menu_slug];
		if ( $parent_slug && ! isset( $_parent_pages[$parent_slug] ) ) {
			$url = admin_url( add_query_arg( 'page', $menu_slug, $parent_slug ) );
		} else {
			$url = admin_url( 'admin.php?page=' . $menu_slug );
		}
	} else {
		$url = '';
	}

	$url = esc_url($url);

	if ( $echo )
		echo $url;

	return $url;
}

//
// Pluggable Menu Support -- Private
//
/**
 *
 * @global string $parent_file
 * @global array $menu
 * @global array $submenu
 * @global string $pagenow
 * @global string $typenow
 * @global string $plugin_page
 * @global string $_wp_real_parent_file
 * @global array $_wp_menu_nopriv
 * @global array $_wp_submenu_nopriv
 */
function get_admin_page_parent( $parent = '' ) {
	global $parent_file, $menu, $submenu, $pagenow, $typenow,
		$plugin_page, $_wp_real_parent_file, $_wp_menu_nopriv, $_wp_submenu_nopriv;

	if ( !empty ( $parent ) && 'admin.php' != $parent ) {
		if ( isset( $_wp_real_parent_file[$parent] ) )
			$parent = $_wp_real_parent_file[$parent];
		return $parent;
	}

	if ( $pagenow == 'admin.php' && isset( $plugin_page ) ) {
		foreach ( (array)$menu as $parent_menu ) {
			if ( $parent_menu[2] == $plugin_page ) {
				$parent_file = $plugin_page;
				if ( isset( $_wp_real_parent_file[$parent_file] ) )
					$parent_file = $_wp_real_parent_file[$parent_file];
				return $parent_file;
			}
		}
		if ( isset( $_wp_menu_nopriv[$plugin_page] ) ) {
			$parent_file = $plugin_page;
			if ( isset( $_wp_real_parent_file[$parent_file] ) )
					$parent_file = $_wp_real_parent_file[$parent_file];
			return $parent_file;
		}
	}

	if ( isset( $plugin_page ) && isset( $_wp_submenu_nopriv[$pagenow][$plugin_page] ) ) {
		$parent_file = $pagenow;
		if ( isset( $_wp_real_parent_file[$parent_file] ) )
			$parent_file = $_wp_real_parent_file[$parent_file];
		return $parent_file;
	}

	foreach (array_keys( (array)$submenu ) as $parent) {
		foreach ( $submenu[$parent] as $submenu_array ) {
			if ( isset( $_wp_real_parent_file[$parent] ) )
				$parent = $_wp_real_parent_file[$parent];
			if ( !empty($typenow) && ($submenu_array[2] == "$pagenow?post_type=$typenow") ) {
				$parent_file = $parent;
				return $parent;
			} elseif ( $submenu_array[2] == $pagenow && empty($typenow) && ( empty($parent_file) || false === strpos($parent_file, '?') ) ) {
				$parent_file = $parent;
				return $parent;
			} elseif ( isset( $plugin_page ) && ($plugin_page == $submenu_array[2] ) ) {
				$parent_file = $parent;
				return $parent;
			}
		}
	}

	if ( empty($parent_file) )
		$parent_file = '';
	return '';
}

/**
 *
 * @global string $title
 * @global array $menu
 * @global array $submenu
 * @global string $pagenow
 * @global string $plugin_page
 * @global string $typenow
 */
function get_admin_page_title() {
	global $title, $menu, $submenu, $pagenow, $plugin_page, $typenow;

	if ( ! empty ( $title ) )
		return $title;

	$hook = get_plugin_page_hook( $plugin_page, $pagenow );

	$parent = $parent1 = get_admin_page_parent();

	if ( empty ( $parent) ) {
		foreach ( (array)$menu as $menu_array ) {
			if ( isset( $menu_array[3] ) ) {
				if ( $menu_array[2] == $pagenow ) {
					$title = $menu_array[3];
					return $menu_array[3];
				} elseif ( isset( $plugin_page ) && ($plugin_page == $menu_array[2] ) && ($hook == $menu_array[3] ) ) {
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
			foreach ( $submenu[$parent] as $submenu_array ) {
				if ( isset( $plugin_page ) &&
					( $plugin_page == $submenu_array[2] ) &&
					(
						( $parent == $pagenow ) ||
						( $parent == $plugin_page ) ||
						( $plugin_page == $hook ) ||
						( $pagenow == 'admin.php' && $parent1 != $submenu_array[2] ) ||
						( !empty($typenow) && $parent == $pagenow . '?post_type=' . $typenow)
					)
					) {
						$title = $submenu_array[3];
						return $submenu_array[3];
					}

				if ( $submenu_array[2] != $pagenow || isset( $_GET['page'] ) ) // not the current page
					continue;

				if ( isset( $submenu_array[3] ) ) {
					$title = $submenu_array[3];
					return $submenu_array[3];
				} else {
					$title = $submenu_array[0];
					return $title;
				}
			}
		}
		if ( empty ( $title ) ) {
			foreach ( $menu as $menu_array ) {
				if ( isset( $plugin_page ) &&
					( $plugin_page == $menu_array[2] ) &&
					( $pagenow == 'admin.php' ) &&
					( $parent1 == $menu_array[2] ) )
					{
						$title = $menu_array[3];
						return $menu_array[3];
					}
			}
		}
	}

	return $title;
}

/**
 * @since 2.3.0
 *
 * @param string $plugin_page
 * @param string $parent_page
 * @return string|null
 */
function get_plugin_page_hook( $plugin_page, $parent_page ) {
	$hook = get_plugin_page_hookname( $plugin_page, $parent_page );
	if ( has_action($hook) )
		return $hook;
	else
		return null;
}

/**
 *
 * @global array $admin_page_hooks
 * @param string $plugin_page
 * @param string $parent_page
 */
function get_plugin_page_hookname( $plugin_page, $parent_page ) {
	global $admin_page_hooks;

	$parent = get_admin_page_parent( $parent_page );

	$page_type = 'admin';
	if ( empty ( $parent_page ) || 'admin.php' == $parent_page || isset( $admin_page_hooks[$plugin_page] ) ) {
		if ( isset( $admin_page_hooks[$plugin_page] ) ) {
			$page_type = 'toplevel';
		} elseif ( isset( $admin_page_hooks[$parent] )) {
			$page_type = $admin_page_hooks[$parent];
		}
	} elseif ( isset( $admin_page_hooks[$parent] ) ) {
		$page_type = $admin_page_hooks[$parent];
	}

	$plugin_name = preg_replace( '!\.php!', '', $plugin_page );

	return $page_type . '_page_' . $plugin_name;
}

/**
 *
 * @global string $pagenow
 * @global array $menu
 * @global array $submenu
 * @global array $_wp_menu_nopriv
 * @global array $_wp_submenu_nopriv
 * @global string $plugin_page
 * @global array $_registered_pages
 */
function user_can_access_admin_page() {
	global $pagenow, $menu, $submenu, $_wp_menu_nopriv, $_wp_submenu_nopriv,
		$plugin_page, $_registered_pages;

	$parent = get_admin_page_parent();

	if ( !isset( $plugin_page ) && isset( $_wp_submenu_nopriv[$parent][$pagenow] ) )
		return false;

	if ( isset( $plugin_page ) ) {
		if ( isset( $_wp_submenu_nopriv[$parent][$plugin_page] ) )
			return false;

		$hookname = get_plugin_page_hookname($plugin_page, $parent);

		if ( !isset($_registered_pages[$hookname]) )
			return false;
	}

	if ( empty( $parent) ) {
		if ( isset( $_wp_menu_nopriv[$pagenow] ) )
			return false;
		if ( isset( $_wp_submenu_nopriv[$pagenow][$pagenow] ) )
			return false;
		if ( isset( $plugin_page ) && isset( $_wp_submenu_nopriv[$pagenow][$plugin_page] ) )
			return false;
		if ( isset( $plugin_page ) && isset( $_wp_menu_nopriv[$plugin_page] ) )
			return false;
		foreach (array_keys( $_wp_submenu_nopriv ) as $key ) {
			if ( isset( $_wp_submenu_nopriv[$key][$pagenow] ) )
				return false;
			if ( isset( $plugin_page ) && isset( $_wp_submenu_nopriv[$key][$plugin_page] ) )
			return false;
		}
		return true;
	}

	if ( isset( $plugin_page ) && ( $plugin_page == $parent ) && isset( $_wp_menu_nopriv[$plugin_page] ) )
		return false;

	if ( isset( $submenu[$parent] ) ) {
		foreach ( $submenu[$parent] as $submenu_array ) {
			if ( isset( $plugin_page ) && ( $submenu_array[2] == $plugin_page ) ) {
				if ( current_user_can( $submenu_array[1] ))
					return true;
				else
					return false;
			} elseif ( $submenu_array[2] == $pagenow ) {
				if ( current_user_can( $submenu_array[1] ))
					return true;
				else
					return false;
			}
		}
	}

	foreach ( $menu as $menu_array ) {
		if ( $menu_array[2] == $parent) {
			if ( current_user_can( $menu_array[1] ))
				return true;
			else
				return false;
		}
	}

	return true;
}

/* Whitelist functions */

/**
 * Register a setting and its sanitization callback
 *
 * @since 2.7.0
 *
 * @global array $new_whitelist_options
 *
 * @param string $option_group A settings group name. Should correspond to a whitelisted option key name.
 * 	Default whitelisted option key names include "general," "discussion," and "reading," among others.
 * @param string $option_name The name of an option to sanitize and save.
 * @param callable $sanitize_callback A callback function that sanitizes the option's value.
 */
function register_setting( $option_group, $option_name, $sanitize_callback = '' ) {
	global $new_whitelist_options;

	if ( 'misc' == $option_group ) {
		_deprecated_argument( __FUNCTION__, '3.0', sprintf( __( 'The "%s" options group has been removed. Use another settings group.' ), 'misc' ) );
		$option_group = 'general';
	}

	if ( 'privacy' == $option_group ) {
		_deprecated_argument( __FUNCTION__, '3.5', sprintf( __( 'The "%s" options group has been removed. Use another settings group.' ), 'privacy' ) );
		$option_group = 'reading';
	}

	$new_whitelist_options[ $option_group ][] = $option_name;
	if ( $sanitize_callback != '' )
		add_filter( "sanitize_option_{$option_name}", $sanitize_callback );
}

/**
 * Unregister a setting
 *
 * @since 2.7.0
 *
 * @global array $new_whitelist_options
 *
 * @param string   $option_group
 * @param string   $option_name
 * @param callable $sanitize_callback
 */
function unregister_setting( $option_group, $option_name, $sanitize_callback = '' ) {
	global $new_whitelist_options;

	if ( 'misc' == $option_group ) {
		_deprecated_argument( __FUNCTION__, '3.0', sprintf( __( 'The "%s" options group has been removed. Use another settings group.' ), 'misc' ) );
		$option_group = 'general';
	}

	if ( 'privacy' == $option_group ) {
		_deprecated_argument( __FUNCTION__, '3.5', sprintf( __( 'The "%s" options group has been removed. Use another settings group.' ), 'privacy' ) );
		$option_group = 'reading';
	}

	$pos = array_search( $option_name, (array) $new_whitelist_options[ $option_group ] );
	if ( $pos !== false )
		unset( $new_whitelist_options[ $option_group ][ $pos ] );
	if ( $sanitize_callback != '' )
		remove_filter( "sanitize_option_{$option_name}", $sanitize_callback );
}

/**
 * {@internal Missing Short Description}}
 *
 * @since 2.7.0
 *
 * @global array $new_whitelist_options
 *
 * @param array $options
 * @return array
 */
function option_update_filter( $options ) {
	global $new_whitelist_options;

	if ( is_array( $new_whitelist_options ) )
		$options = add_option_whitelist( $new_whitelist_options, $options );

	return $options;
}

/**
 * {@internal Missing Short Description}}
 *
 * @since 2.7.0
 *
 * @global array $whitelist_options
 *
 * @param array        $new_options
 * @param string|array $options
 * @return array
 */
function add_option_whitelist( $new_options, $options = '' ) {
	if ( $options == '' )
		global $whitelist_options;
	else
		$whitelist_options = $options;

	foreach ( $new_options as $page => $keys ) {
		foreach ( $keys as $key ) {
			if ( !isset($whitelist_options[ $page ]) || !is_array($whitelist_options[ $page ]) ) {
				$whitelist_options[ $page ] = array();
				$whitelist_options[ $page ][] = $key;
			} else {
				$pos = array_search( $key, $whitelist_options[ $page ] );
				if ( $pos === false )
					$whitelist_options[ $page ][] = $key;
			}
		}
	}

	return $whitelist_options;
}

/**
 * {@internal Missing Short Description}}
 *
 * @since 2.7.0
 *
 * @global array $whitelist_options
 *
 * @param array        $del_options
 * @param string|array $options
 * @return array
 */
function remove_option_whitelist( $del_options, $options = '' ) {
	if ( $options == '' )
		global $whitelist_options;
	else
		$whitelist_options = $options;

	foreach ( $del_options as $page => $keys ) {
		foreach ( $keys as $key ) {
			if ( isset($whitelist_options[ $page ]) && is_array($whitelist_options[ $page ]) ) {
				$pos = array_search( $key, $whitelist_options[ $page ] );
				if ( $pos !== false )
					unset( $whitelist_options[ $page ][ $pos ] );
			}
		}
	}

	return $whitelist_options;
}

/**
 * Output nonce, action, and option_page fields for a settings page.
 *
 * @since 2.7.0
 *
 * @param string $option_group A settings group name. This should match the group name used in register_setting().
 */
function settings_fields($option_group) {
	echo "<input type='hidden' name='option_page' value='" . esc_attr($option_group) . "' />";
	echo '<input type="hidden" name="action" value="update" />';
	wp_nonce_field("$option_group-options");
}

/**
 * Clears the Plugins cache used by get_plugins() and by default, the Plugin Update cache.
 *
 * @since 3.7.0
 *
 * @param bool $clear_update_cache Whether to clear the Plugin updates cache
 */
function wp_clean_plugins_cache( $clear_update_cache = true ) {
	if ( $clear_update_cache )
		delete_site_transient( 'update_plugins' );
	wp_cache_delete( 'plugins', 'plugins' );
}

/**
 * @param string $plugin
 */
function plugin_sandbox_scrape( $plugin ) {
	wp_register_plugin_realpath( WP_PLUGIN_DIR . '/' . $plugin );
	include( WP_PLUGIN_DIR . '/' . $plugin );
}