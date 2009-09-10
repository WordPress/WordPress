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
 * <code>
 * /*
 * Plugin Name: Name of Plugin
 * Plugin URI: Link to plugin information
 * Description: Plugin Description
 * Author: Plugin author's name
 * Author URI: Link to the author's web site
 * Version: Must be set in the plugin for WordPress 2.3+
 * Text Domain: Optional. Unique identifier, should be same as the one used in
 *		plugin_text_domain()
 * Domain Path: Optional. Only useful if the translations are located in a
 *		folder above the plugin's base path. For example, if .mo files are
 *		located in the locale folder then Domain Path will be "/locale/" and
 *		must have the first slash. Defaults to the base folder the plugin is
 *		located in.
 *  * / # Remove the space to close comment
 * </code>
 *
 * Plugin data returned array contains the following:
 *		'Name' - Name of the plugin, must be unique.
 *		'Title' - Title of the plugin and the link to the plugin's web site.
 *		'Description' - Description of what the plugin does and/or notes
 *		from the author.
 *		'Author' - The author's name
 *		'AuthorURI' - The authors web site address.
 *		'Version' - The plugin version number.
 *		'PluginURI' - Plugin web site address.
 *		'TextDomain' - Plugin's text domain for localization.
 *		'DomainPath' - Plugin's relative directory path to .mo files.
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
 * @link http://trac.wordpress.org/ticket/5651 Previous Optimizations.
 * @link http://trac.wordpress.org/ticket/7372 Further and better Optimizations.
 * @since 1.5.0
 *
 * @param string $plugin_file Path to the plugin file
 * @param bool $markup If the returned data should have HTML markup applied
 * @param bool $translate If the returned data should be translated
 * @return array See above for description.
 */
function get_plugin_data( $plugin_file, $markup = true, $translate = true ) {
	// We don't need to write to the file, so just open for reading.
	$fp = fopen($plugin_file, 'r');

	// Pull only the first 8kiB of the file in.
	$plugin_data = fread( $fp, 8192 );

	// PHP will close file handle, but we are good citizens.
	fclose($fp);

	preg_match( '|Plugin Name:(.*)$|mi', $plugin_data, $name );
	preg_match( '|Plugin URI:(.*)$|mi', $plugin_data, $uri );
	preg_match( '|Version:(.*)|i', $plugin_data, $version );
	preg_match( '|Description:(.*)$|mi', $plugin_data, $description );
	preg_match( '|Author:(.*)$|mi', $plugin_data, $author_name );
	preg_match( '|Author URI:(.*)$|mi', $plugin_data, $author_uri );
	preg_match( '|Text Domain:(.*)$|mi', $plugin_data, $text_domain );
	preg_match( '|Domain Path:(.*)$|mi', $plugin_data, $domain_path );

	foreach ( array( 'name', 'uri', 'version', 'description', 'author_name', 'author_uri', 'text_domain', 'domain_path' ) as $field ) {
		if ( !empty( ${$field} ) )
			${$field} = _cleanup_header_comment(${$field}[1]);
		else
			${$field} = '';
	}

	$plugin_data = array(
				'Name' => $name, 'Title' => $name, 'PluginURI' => $uri, 'Description' => $description,
				'Author' => $author_name, 'AuthorURI' => $author_uri, 'Version' => $version,
				'TextDomain' => $text_domain, 'DomainPath' => $domain_path
				);
	if ( $markup || $translate )
		$plugin_data = _get_plugin_data_markup_translate($plugin_file, $plugin_data, $markup, $translate);

	return $plugin_data;
}

function _get_plugin_data_markup_translate($plugin_file, $plugin_data, $markup = true, $translate = true) {

	//Translate fields
	if( $translate && ! empty($plugin_data['TextDomain']) ) {
		if( ! empty( $plugin_data['DomainPath'] ) )
			load_plugin_textdomain($plugin_data['TextDomain'], false, dirname($plugin_file). $plugin_data['DomainPath']);
		else
			load_plugin_textdomain($plugin_data['TextDomain'], false, dirname($plugin_file));

		foreach ( array('Name', 'PluginURI', 'Description', 'Author', 'AuthorURI', 'Version') as $field )
			$plugin_data[ $field ] = translate($plugin_data[ $field ], $plugin_data['TextDomain']);
	}

	//Apply Markup
	if ( $markup ) {
		if ( ! empty($plugin_data['PluginURI']) && ! empty($plugin_data['Name']) )
			$plugin_data['Title'] = '<a href="' . $plugin_data['PluginURI'] . '" title="' . __( 'Visit plugin homepage' ) . '">' . $plugin_data['Name'] . '</a>';
		else
			$plugin_data['Title'] = $plugin_data['Name'];

		if ( ! empty($plugin_data['AuthorURI']) && ! empty($plugin_data['Author']) )
			$plugin_data['Author'] = '<a href="' . $plugin_data['AuthorURI'] . '" title="' . __( 'Visit author homepage' ) . '">' . $plugin_data['Author'] . '</a>';

		$plugin_data['Description'] = wptexturize( $plugin_data['Description'] );
		if( ! empty($plugin_data['Author']) )
			$plugin_data['Description'] .= ' <cite>' . sprintf( __('By %s'), $plugin_data['Author'] ) . '.</cite>';
	}

	$plugins_allowedtags = array('a' => array('href' => array(),'title' => array()),'abbr' => array('title' => array()),'acronym' => array('title' => array()),'code' => array(),'em' => array(),'strong' => array());

	// Sanitize all displayed data
	$plugin_data['Title']       = wp_kses($plugin_data['Title'], $plugins_allowedtags);
	$plugin_data['Version']     = wp_kses($plugin_data['Version'], $plugins_allowedtags);
	$plugin_data['Description'] = wp_kses($plugin_data['Description'], $plugins_allowedtags);
	$plugin_data['Author']      = wp_kses($plugin_data['Author'], $plugins_allowedtags);

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
 * (wp-content/plugins/my-plugin). The file it looks for has the plugin data and
 * must be found in those two locations. It is recommended that do keep your
 * plugin files in directories.
 *
 * The file with the plugin data is the file that will be included and therefore
 * needs to have the main execution for the plugin. This does not mean
 * everything must be contained in the file and it is recommended that the file
 * be split for maintainability. Keep everything in one file for extreme
 * optimization purposes.
 *
 * @since unknown
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
	if( !empty($plugin_folder) )
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
				}
			} else {
				if ( substr($file, -4) == '.php' )
					$plugin_files[] = $file;
			}
		}
	}
	@closedir( $plugins_dir );
	@closedir( $plugins_subdir );

	if ( !$plugins_dir || empty($plugin_files) )
		return $wp_plugins;

	foreach ( $plugin_files as $plugin_file ) {
		if ( !is_readable( "$plugin_root/$plugin_file" ) )
			continue;

		$plugin_data = get_plugin_data( "$plugin_root/$plugin_file", false, false ); //Do not apply markup/translate as it'll be cached.

		if ( empty ( $plugin_data['Name'] ) )
			continue;

		$wp_plugins[plugin_basename( $plugin_file )] = $plugin_data;
	}

	uasort( $wp_plugins, create_function( '$a, $b', 'return strnatcasecmp( $a["Name"], $b["Name"] );' ));

	$cache_plugins[ $plugin_folder ] = $wp_plugins;
	wp_cache_set('plugins', $cache_plugins, 'plugins');

	return $wp_plugins;
}

/**
 * Check whether the plugin is active by checking the active_plugins list.
 *
 * @since 2.5.0
 *
 * @param string $plugin Base plugin path from plugins directory.
 * @return bool True, if in the active plugins list. False, not in the list.
 */
function is_plugin_active($plugin) {
	return in_array($plugin, get_option('active_plugins'));
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
 * @since unknown
 *
 * @param string $plugin Plugin path to main plugin file with plugin data.
 * @param string $redirect Optional. URL to redirect to.
 * @return WP_Error|null WP_Error on invalid file or null on success.
 */
function activate_plugin($plugin, $redirect = '') {
	$current = get_option('active_plugins');
	$plugin = plugin_basename(trim($plugin));

	$valid = validate_plugin($plugin);
	if ( is_wp_error($valid) )
		return $valid;

	if ( !in_array($plugin, $current) ) {
		if ( !empty($redirect) )
			wp_redirect(add_query_arg('_error_nonce', wp_create_nonce('plugin-activation-error_' . $plugin), $redirect)); // we'll override this later if the plugin can be included without fatal error
		ob_start();
		@include(WP_PLUGIN_DIR . '/' . $plugin);
		$current[] = $plugin;
		sort($current);
		do_action( 'activate_plugin', trim( $plugin) );
		update_option('active_plugins', $current);
		do_action( 'activate_' . trim( $plugin ) );
		do_action( 'activated_plugin', trim( $plugin) );
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
 * @since unknown
 *
 * @param string|array $plugins Single plugin or list of plugins to deactivate.
 * @param bool $silent Optional, default is false. Prevent calling deactivate hook.
 */
function deactivate_plugins($plugins, $silent= false) {
	$current = get_option('active_plugins');

	if ( !is_array($plugins) )
		$plugins = array($plugins);

	foreach ( $plugins as $plugin ) {
		$plugin = plugin_basename($plugin);
		if( ! is_plugin_active($plugin) )
			continue;
		if ( ! $silent )
			do_action( 'deactivate_plugin', trim( $plugin ) );
		array_splice($current, array_search( $plugin, $current), 1 ); // Fixed Array-fu!
		//Used by Plugin updater to internally deactivate plugin, however, not to notify plugins of the fact to prevent plugin output.
		if ( ! $silent ) {
			do_action( 'deactivate_' . trim( $plugin ) );
			do_action( 'deactivated_plugin', trim( $plugin ) );
		}
	}

	update_option('active_plugins', $current);
}

/**
 * Activate multiple plugins.
 *
 * When WP_Error is returned, it does not mean that one of the plugins had
 * errors. It means that one or more of the plugins file path was invalid.
 *
 * The execution will be halted as soon as one of the plugins has an error.
 *
 * @since unknown
 *
 * @param string|array $plugins
 * @param string $redirect Redirect to page after successful activation.
 * @return bool|WP_Error True when finished or WP_Error if there were errors during a plugin activation.
 */
function activate_plugins($plugins, $redirect = '') {
	if ( !is_array($plugins) )
		$plugins = array($plugins);

	$errors = array();
	foreach ( (array) $plugins as $plugin ) {
		if ( !empty($redirect) )
			$redirect = add_query_arg('plugin', $plugin, $redirect);
		$result = activate_plugin($plugin, $redirect);
		if ( is_wp_error($result) )
			$errors[$plugin] = $result;
	}

	if ( !empty($errors) )
		return new WP_Error('plugins_invalid', __('One of the plugins is invalid.'), $errors);

	return true;
}

/**
 * Remove directory and files of a plugin for a single or list of plugin(s).
 *
 * If the plugins parameter list is empty, false will be returned. True when
 * completed.
 *
 * @since unknown
 *
 * @param array $plugins List of plugin
 * @param string $redirect Redirect to page when complete.
 * @return mixed
 */
function delete_plugins($plugins, $redirect = '' ) {
	global $wp_filesystem;

	if( empty($plugins) )
		return false;

	$checked = array();
	foreach( $plugins as $plugin )
		$checked[] = 'checked[]=' . $plugin;

	ob_start();
	$url = wp_nonce_url('plugins.php?action=delete-selected&verify-delete=1&' . implode('&', $checked), 'bulk-manage-plugins');
	if ( false === ($credentials = request_filesystem_credentials($url)) ) {
		$data = ob_get_contents();
		ob_end_clean();
		if( ! empty($data) ){
			include_once( ABSPATH . 'wp-admin/admin-header.php');
			echo $data;
			include( ABSPATH . 'wp-admin/admin-footer.php');
			exit;
		}
		return;
	}

	if ( ! WP_Filesystem($credentials) ) {
		request_filesystem_credentials($url, '', true); //Failed to connect, Error and request again
		$data = ob_get_contents();
		ob_end_clean();
		if( ! empty($data) ){
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
		return new WP_Error('fs_error', __('Filesystem error'), $wp_filesystem->errors);

	//Get the base plugin folder
	$plugins_dir = $wp_filesystem->wp_plugins_dir();
	if ( empty($plugins_dir) )
		return new WP_Error('fs_no_plugins_dir', __('Unable to locate WordPress Plugin directory.'));

	$plugins_dir = trailingslashit( $plugins_dir );

	$errors = array();

	foreach( $plugins as $plugin_file ) {
		// Run Uninstall hook
		if ( is_uninstallable_plugin( $plugin_file ) )
			uninstall_plugin($plugin_file);

		$this_plugin_dir = trailingslashit( dirname($plugins_dir . $plugin_file) );
		// If plugin is in its own directory, recursively delete the directory.
		if ( strpos($plugin_file, '/') && $this_plugin_dir != $plugins_dir ) //base check on if plugin includes directory seperator AND that its not the root plugin folder
			$deleted = $wp_filesystem->delete($this_plugin_dir, true);
		else
			$deleted = $wp_filesystem->delete($plugins_dir . $plugin_file);

		if ( ! $deleted )
			$errors[] = $plugin_file;
	}

	if ( ! empty($errors) )
		return new WP_Error('could_not_remove_plugin', sprintf(__('Could not fully remove the plugin(s) %s'), implode(', ', $errors)) );

	// Force refresh of plugin update information
	if ( $current = get_transient('update_plugins') ) {
		unset( $current->response[ $plugin_file ] );
		set_transient('update_plugins', $current);
	}

	return true;
}

function validate_active_plugins() {
	$check_plugins = get_option('active_plugins');

	// Sanity check.  If the active plugin list is not an array, make it an
	// empty array.
	if ( !is_array($check_plugins) ) {
		update_option('active_plugins', array());
		return;
	}

	//Invalid is any plugin that is deactivated due to error.
	$invalid = array();

	// If a plugin file does not exist, remove it from the list of active
	// plugins.
	foreach ( $check_plugins as $check_plugin ) {
		$result = validate_plugin($check_plugin);
		if ( is_wp_error( $result ) ) {
			$invalid[$check_plugin] = $result;
			deactivate_plugins( $check_plugin, true);
		}
	}
	return $invalid;
}

/**
 * Validate the plugin path.
 *
 * Checks that the file exists and {@link validate_file() is valid file}.
 *
 * @since unknown
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
		include WP_PLUGIN_DIR . '/' . dirname($file) . '/uninstall.php';

		return true;
	}

	if ( isset( $uninstallable_plugins[$file] ) ) {
		$callable = $uninstallable_plugins[$file];
		unset($uninstallable_plugins[$file]);
		update_option('uninstall_plugins', $uninstallable_plugins);
		unset($uninstallable_plugins);

		include WP_PLUGIN_DIR . '/' . $file;

		add_action( 'uninstall_' . $file, $callable );
		do_action( 'uninstall_' . $file );
	}
}

//
// Menu
//

function add_menu_page( $page_title, $menu_title, $access_level, $file, $function = '', $icon_url = '', $position = NULL ) {
	global $menu, $admin_page_hooks, $_registered_pages;

	$file = plugin_basename( $file );

	$admin_page_hooks[$file] = sanitize_title( $menu_title );

	$hookname = get_plugin_page_hookname( $file, '' );
	if (!empty ( $function ) && !empty ( $hookname ))
		add_action( $hookname, $function );

	if ( empty($icon_url) ) {
		$icon_url = 'images/generic.png';
	} elseif ( is_ssl() && 0 === strpos($icon_url, 'http://') ) {
		$icon_url = 'https://' . substr($icon_url, 7);
	}

	$new_menu = array ( $menu_title, $access_level, $file, $page_title, 'menu-top ' . $hookname, $hookname, $icon_url );

	if ( NULL === $position  ) {
		$menu[] = $new_menu;
	} else {
		$menu[$position] = $new_menu;
	}
	
	$_registered_pages[$hookname] = true;

	return $hookname;
}

function add_object_page( $page_title, $menu_title, $access_level, $file, $function = '', $icon_url = '') {
	global $_wp_last_object_menu;
	
	$_wp_last_object_menu++;
	
	return add_menu_page($page_title, $menu_title, $access_level, $file, $function, $icon_url, $_wp_last_object_menu);
}

function add_utility_page( $page_title, $menu_title, $access_level, $file, $function = '', $icon_url = '') {
	global $_wp_last_utility_menu;
	
	$_wp_last_utility_menu++;
	
	return add_menu_page($page_title, $menu_title, $access_level, $file, $function, $icon_url, $_wp_last_utility_menu);
}

function add_submenu_page( $parent, $page_title, $menu_title, $access_level, $file, $function = '' ) {
	global $submenu;
	global $menu;
	global $_wp_real_parent_file;
	global $_wp_submenu_nopriv;
	global $_registered_pages;

	$file = plugin_basename( $file );

	$parent = plugin_basename( $parent);
	if ( isset( $_wp_real_parent_file[$parent] ) )
		$parent = $_wp_real_parent_file[$parent];

	if ( !current_user_can( $access_level ) ) {
		$_wp_submenu_nopriv[$parent][$file] = true;
		return false;
	}

	// If the parent doesn't already have a submenu, add a link to the parent
	// as the first item in the submenu.  If the submenu file is the same as the
	// parent file someone is trying to link back to the parent manually.  In
	// this case, don't automatically add a link back to avoid duplication.
	if (!isset( $submenu[$parent] ) && $file != $parent  ) {
		foreach ( (array)$menu as $parent_menu ) {
			if ( $parent_menu[2] == $parent && current_user_can( $parent_menu[1] ) )
				$submenu[$parent][] = $parent_menu;
		}
	}

	$submenu[$parent][] = array ( $menu_title, $access_level, $file, $page_title );

	$hookname = get_plugin_page_hookname( $file, $parent);
	if (!empty ( $function ) && !empty ( $hookname ))
		add_action( $hookname, $function );

	$_registered_pages[$hookname] = true;
	// backwards-compatibility for plugins using add_management page.  See wp-admin/admin.php for redirect from edit.php to tools.php
	if ( 'tools.php' == $parent ) 
		$_registered_pages[get_plugin_page_hookname( $file, 'edit.php')] = true;

	return $hookname;
}

/**
 * Add sub menu page to the tools main menu.
 *
 * @param string $page_title
 * @param unknown_type $menu_title
 * @param unknown_type $access_level
 * @param unknown_type $file
 * @param unknown_type $function
 * @return unknown
 */
function add_management_page( $page_title, $menu_title, $access_level, $file, $function = '' ) {
	return add_submenu_page( 'tools.php', $page_title, $menu_title, $access_level, $file, $function );
}

function add_options_page( $page_title, $menu_title, $access_level, $file, $function = '' ) {
	return add_submenu_page( 'options-general.php', $page_title, $menu_title, $access_level, $file, $function );
}

function add_theme_page( $page_title, $menu_title, $access_level, $file, $function = '' ) {
	return add_submenu_page( 'themes.php', $page_title, $menu_title, $access_level, $file, $function );
}

function add_users_page( $page_title, $menu_title, $access_level, $file, $function = '' ) {
	if ( current_user_can('edit_users') )
		$parent = 'users.php';
	else
		$parent = 'profile.php';
	return add_submenu_page( $parent, $page_title, $menu_title, $access_level, $file, $function );
}

function add_dashboard_page( $page_title, $menu_title, $access_level, $file, $function = '' ) {
	return add_submenu_page( 'index.php', $page_title, $menu_title, $access_level, $file, $function );
}

function add_posts_page( $page_title, $menu_title, $access_level, $file, $function = '' ) {
	return add_submenu_page( 'edit.php', $page_title, $menu_title, $access_level, $file, $function );
}

function add_media_page( $page_title, $menu_title, $access_level, $file, $function = '' ) {
	return add_submenu_page( 'upload.php', $page_title, $menu_title, $access_level, $file, $function );
}

function add_links_page( $page_title, $menu_title, $access_level, $file, $function = '' ) {
	return add_submenu_page( 'link-manager.php', $page_title, $menu_title, $access_level, $file, $function );
}

function add_pages_page( $page_title, $menu_title, $access_level, $file, $function = '' ) {
	return add_submenu_page( 'edit-pages.php', $page_title, $menu_title, $access_level, $file, $function );
}

function add_comments_page( $page_title, $menu_title, $access_level, $file, $function = '' ) {
	return add_submenu_page( 'edit-comments.php', $page_title, $menu_title, $access_level, $file, $function );
}

//
// Pluggable Menu Support -- Private
//

function get_admin_page_parent( $parent = '' ) {
	global $parent_file;
	global $menu;
	global $submenu;
	global $pagenow;
	global $plugin_page;
	global $_wp_real_parent_file;
	global $_wp_menu_nopriv;
	global $_wp_submenu_nopriv;

	if ( !empty ( $parent ) && 'admin.php' != $parent ) {
		if ( isset( $_wp_real_parent_file[$parent] ) )
			$parent = $_wp_real_parent_file[$parent];
		return $parent;
	}
/*
	if ( !empty ( $parent_file ) ) {
		if ( isset( $_wp_real_parent_file[$parent_file] ) )
			$parent_file = $_wp_real_parent_file[$parent_file];

		return $parent_file;
	}
*/

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
			if ( $submenu_array[2] == $pagenow ) {
				$parent_file = $parent;
				return $parent;
			} else
				if ( isset( $plugin_page ) && ($plugin_page == $submenu_array[2] ) ) {
					$parent_file = $parent;
					return $parent;
				}
		}
	}

	if ( empty($parent_file) )
		$parent_file = '';
	return '';
}

function get_admin_page_title() {
	global $title;
	global $menu;
	global $submenu;
	global $pagenow;
	global $plugin_page;

	if ( isset( $title ) && !empty ( $title ) ) {
		return $title;
	}

	$hook = get_plugin_page_hook( $plugin_page, $pagenow );

	$parent = $parent1 = get_admin_page_parent();

	if ( empty ( $parent) ) {
		foreach ( (array)$menu as $menu_array ) {
			if ( isset( $menu_array[3] ) ) {
				if ( $menu_array[2] == $pagenow ) {
					$title = $menu_array[3];
					return $menu_array[3];
				} else
					if ( isset( $plugin_page ) && ($plugin_page == $menu_array[2] ) && ($hook == $menu_array[3] ) ) {
						$title = $menu_array[3];
						return $menu_array[3];
					}
			} else {
				$title = $menu_array[0];
				return $title;
			}
		}
	} else {
		foreach (array_keys( $submenu ) as $parent) {
			foreach ( $submenu[$parent] as $submenu_array ) {
				if ( isset( $plugin_page ) &&
					($plugin_page == $submenu_array[2] ) &&
					(($parent == $pagenow ) || ($parent == $plugin_page ) || ($plugin_page == $hook ) || (($pagenow == 'admin.php' ) && ($parent1 != $submenu_array[2] ) ) )
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
		if ( !isset($title) || empty ( $title ) ) {
			foreach ( $menu as $menu_array ) {
				if ( isset( $plugin_page ) &&
					($plugin_page == $menu_array[2] ) &&
					($pagenow == 'admin.php' ) &&
					($parent1 == $menu_array[2] ) )
					{
						$title = $menu_array[3];
						return $menu_array[3];
					}
			}
		}
	}

	return $title;
}

function get_plugin_page_hook( $plugin_page, $parent_page ) {
	$hook = get_plugin_page_hookname( $plugin_page, $parent_page );
	if ( has_action($hook) )
		return $hook;
	else
		return null;
}

function get_plugin_page_hookname( $plugin_page, $parent_page ) {
	global $admin_page_hooks;

	$parent = get_admin_page_parent( $parent_page );

	$page_type = 'admin';
	if ( empty ( $parent_page ) || 'admin.php' == $parent_page || isset( $admin_page_hooks[$plugin_page] ) ) {
		if ( isset( $admin_page_hooks[$plugin_page] ) )
			$page_type = 'toplevel';
		else
			if ( isset( $admin_page_hooks[$parent] ))
				$page_type = $admin_page_hooks[$parent];
	} else if ( isset( $admin_page_hooks[$parent] ) ) {
		$page_type = $admin_page_hooks[$parent];
	}

	$plugin_name = preg_replace( '!\.php!', '', $plugin_page );

	return $page_type.'_page_'.$plugin_name;
}

function user_can_access_admin_page() {
	global $pagenow;
	global $menu;
	global $submenu;
	global $_wp_menu_nopriv;
	global $_wp_submenu_nopriv;
	global $plugin_page;
	global $_registered_pages;

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
			} else if ( $submenu_array[2] == $pagenow ) {
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
 * @param string $option_group A settings group name.  Can be anything.
 * @param string $option_name The name of an option to sanitize and save.
 * @param unknown_type $sanitize_callback A callback function that sanitizes the option's value.
 * @return unknown
 */
function register_setting($option_group, $option_name, $sanitize_callback = '') {
	return add_option_update_handler($option_group, $option_name, $sanitize_callback);
}

/**
 * Unregister a setting
 *
 * @since 2.7.0
 *
 * @param unknown_type $option_group
 * @param unknown_type $option_name
 * @param unknown_type $sanitize_callback
 * @return unknown
 */
function unregister_setting($option_group, $option_name, $sanitize_callback = '') {
	return remove_option_update_handler($option_group, $option_name, $sanitize_callback);
}

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @param unknown_type $option_group
 * @param unknown_type $option_name
 * @param unknown_type $sanitize_callback
 */
function add_option_update_handler($option_group, $option_name, $sanitize_callback = '') {
	global $new_whitelist_options;
	$new_whitelist_options[ $option_group ][] = $option_name;
	if ( $sanitize_callback != '' )
		add_filter( "sanitize_option_{$option_name}", $sanitize_callback );
}

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @param unknown_type $option_group
 * @param unknown_type $option_name
 * @param unknown_type $sanitize_callback
 */
function remove_option_update_handler($option_group, $option_name, $sanitize_callback = '') {
	global $new_whitelist_options;
	$pos = array_search( $option_name, (array) $new_whitelist_options );
	if ( $pos !== false )
		unset( $new_whitelist_options[ $option_group ][ $pos ] );
	if ( $sanitize_callback != '' )
		remove_filter( "sanitize_option_{$option_name}", $sanitize_callback );
}

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @param unknown_type $options
 * @return unknown
 */
function option_update_filter( $options ) {
	global $new_whitelist_options;

	if ( is_array( $new_whitelist_options ) )
		$options = add_option_whitelist( $new_whitelist_options, $options );

	return $options;
}
add_filter( 'whitelist_options', 'option_update_filter' );

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @param unknown_type $new_options
 * @param unknown_type $options
 * @return unknown
 */
function add_option_whitelist( $new_options, $options = '' ) {
	if( $options == '' ) {
		global $whitelist_options;
	} else {
		$whitelist_options = $options;
	}
	foreach( $new_options as $page => $keys ) {
		foreach( $keys as $key ) {
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
 * @since unknown
 *
 * @param unknown_type $del_options
 * @param unknown_type $options
 * @return unknown
 */
function remove_option_whitelist( $del_options, $options = '' ) {
	if( $options == '' ) {
		global $whitelist_options;
	} else {
		$whitelist_options = $options;
	}
	foreach( $del_options as $page => $keys ) {
		foreach( $keys as $key ) {
			if ( isset($whitelist_options[ $page ]) && is_array($whitelist_options[ $page ]) ) {
				$pos = array_search( $key, $whitelist_options[ $page ] );
				if( $pos !== false )
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
 * @param string $option_group A settings group name.  This should match the group name used in register_setting().
 */
function settings_fields($option_group) {
	echo "<input type='hidden' name='option_page' value='" . esc_attr($option_group) . "' />";
	echo '<input type="hidden" name="action" value="update" />';
	wp_nonce_field("$option_group-options");
}

?>
