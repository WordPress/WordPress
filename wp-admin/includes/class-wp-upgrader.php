<?php
/**
 * A File upgrader class for WordPress.
 *
 * This set of classes are designed to be used to upgrade/install a local set of files on the filesystem via the Filesystem Abstraction classes.
 *
 * @link http://trac.wordpress.org/ticket/7875 consolidate plugin/theme/core upgrade/install functions
 *
 * @package WordPress
 * @subpackage Upgrader
 * @since 2.8.0
 */

/**
 * WordPress Upgrader class for Upgrading/Installing a local set of files via the Filesystem Abstraction classes from a Zip file.
 *
 * @TODO More Detailed docs, for methods as well.
 *
 * @package WordPress
 * @subpackage Upgrader
 * @since 2.8.0
 */
class WP_Upgrader {
	var $strings = array();
	var $skin = null;
	var $result = array();

	function __construct($skin = null) {
		if ( null == $skin )
			$this->skin = new WP_Upgrader_Skin();
		else
			$this->skin = $skin;
	}

	function init() {
		$this->skin->set_upgrader($this);
		$this->generic_strings();
	}

	function generic_strings() {
		$this->strings['bad_request'] = __('Invalid Data provided.');
		$this->strings['fs_unavailable'] = __('Could not access filesystem.');
		$this->strings['fs_error'] = __('Filesystem error.');
		$this->strings['fs_no_root_dir'] = __('Unable to locate WordPress Root directory.');
		$this->strings['fs_no_content_dir'] = __('Unable to locate WordPress Content directory (wp-content).');
		$this->strings['fs_no_plugins_dir'] = __('Unable to locate WordPress Plugin directory.');
		$this->strings['fs_no_themes_dir'] = __('Unable to locate WordPress Theme directory.');
		/* translators: %s: directory name */
		$this->strings['fs_no_folder'] = __('Unable to locate needed folder (%s).');

		$this->strings['download_failed'] = __('Download failed.');
		$this->strings['installing_package'] = __('Installing the latest version&#8230;');
		$this->strings['folder_exists'] = __('Destination folder already exists.');
		$this->strings['mkdir_failed'] = __('Could not create directory.');
		$this->strings['incompatible_archive'] = __('The package could not be installed.');

		$this->strings['maintenance_start'] = __('Enabling Maintenance mode&#8230;');
		$this->strings['maintenance_end'] = __('Disabling Maintenance mode&#8230;');
	}

	function fs_connect( $directories = array() ) {
		global $wp_filesystem;

		if ( false === ($credentials = $this->skin->request_filesystem_credentials()) )
			return false;

		if ( ! WP_Filesystem($credentials) ) {
			$error = true;
			if ( is_object($wp_filesystem) && $wp_filesystem->errors->get_error_code() )
				$error = $wp_filesystem->errors;
			$this->skin->request_filesystem_credentials($error); //Failed to connect, Error and request again
			return false;
		}

		if ( ! is_object($wp_filesystem) )
			return new WP_Error('fs_unavailable', $this->strings['fs_unavailable'] );

		if ( is_wp_error($wp_filesystem->errors) && $wp_filesystem->errors->get_error_code() )
			return new WP_Error('fs_error', $this->strings['fs_error'], $wp_filesystem->errors);

		foreach ( (array)$directories as $dir ) {
			switch ( $dir ) {
				case ABSPATH:
					if ( ! $wp_filesystem->abspath() )
						return new WP_Error('fs_no_root_dir', $this->strings['fs_no_root_dir']);
					break;
				case WP_CONTENT_DIR:
					if ( ! $wp_filesystem->wp_content_dir() )
						return new WP_Error('fs_no_content_dir', $this->strings['fs_no_content_dir']);
					break;
				case WP_PLUGIN_DIR:
					if ( ! $wp_filesystem->wp_plugins_dir() )
						return new WP_Error('fs_no_plugins_dir', $this->strings['fs_no_plugins_dir']);
					break;
				case WP_CONTENT_DIR . '/themes':
					if ( ! $wp_filesystem->find_folder(WP_CONTENT_DIR . '/themes') )
						return new WP_Error('fs_no_themes_dir', $this->strings['fs_no_themes_dir']);
					break;
				default:
					if ( ! $wp_filesystem->find_folder($dir) )
						return new WP_Error('fs_no_folder', sprintf($this->strings['fs_no_folder'], $dir));
					break;
			}
		}
		return true;
	} //end fs_connect();

	function download_package($package) {

		if ( ! preg_match('!^(http|https|ftp)://!i', $package) && file_exists($package) ) //Local file or remote?
			return $package; //must be a local file..

		if ( empty($package) )
			return new WP_Error('no_package', $this->strings['no_package']);

		$this->skin->feedback('downloading_package', $package);

		$download_file = download_url($package);

		if ( is_wp_error($download_file) )
			return new WP_Error('download_failed', $this->strings['download_failed'], $download_file->get_error_message());

		return $download_file;
	}

	function unpack_package($package, $delete_package = true) {
		global $wp_filesystem;

		$this->skin->feedback('unpack_package');

		$upgrade_folder = $wp_filesystem->wp_content_dir() . 'upgrade/';

		//Clean up contents of upgrade directory beforehand.
		$upgrade_files = $wp_filesystem->dirlist($upgrade_folder);
		if ( !empty($upgrade_files) ) {
			foreach ( $upgrade_files as $file )
				$wp_filesystem->delete($upgrade_folder . $file['name'], true);
		}

		//We need a working directory
		$working_dir = $upgrade_folder . basename($package, '.zip');

		// Clean up working directory
		if ( $wp_filesystem->is_dir($working_dir) )
			$wp_filesystem->delete($working_dir, true);

		// Unzip package to working directory
		$result = unzip_file($package, $working_dir); //TODO optimizations, Copy when Move/Rename would suffice?

		// Once extracted, delete the package if required.
		if ( $delete_package )
			unlink($package);

		if ( is_wp_error($result) ) {
			$wp_filesystem->delete($working_dir, true);
			if ( 'incompatible_archive' == $result->get_error_code() ) {
				return new WP_Error( 'incompatible_archive', $this->strings['incompatible_archive'], $result->get_error_data() );
			}
			return $result;
		}

		return $working_dir;
	}

	function install_package($args = array()) {
		global $wp_filesystem;
		$defaults = array( 'source' => '', 'destination' => '', //Please always pass these
						'clear_destination' => false, 'clear_working' => false,
						'hook_extra' => array());

		$args = wp_parse_args($args, $defaults);
		extract($args);

		@set_time_limit( 300 );

		if ( empty($source) || empty($destination) )
			return new WP_Error('bad_request', $this->strings['bad_request']);

		$this->skin->feedback('installing_package');

		$res = apply_filters('upgrader_pre_install', true, $hook_extra);
		if ( is_wp_error($res) )
			return $res;

		//Retain the Original source and destinations
		$remote_source = $source;
		$local_destination = $destination;

		$source_files = array_keys( $wp_filesystem->dirlist($remote_source) );
		$remote_destination = $wp_filesystem->find_folder($local_destination);

		//Locate which directory to copy to the new folder, This is based on the actual folder holding the files.
		if ( 1 == count($source_files) && $wp_filesystem->is_dir( trailingslashit($source) . $source_files[0] . '/') ) //Only one folder? Then we want its contents.
			$source = trailingslashit($source) . trailingslashit($source_files[0]);
		elseif ( count($source_files) == 0 )
			return new WP_Error( 'incompatible_archive', $this->strings['incompatible_archive'], __( 'The plugin contains no files.' ) ); //There are no files?
		else //Its only a single file, The upgrader will use the foldername of this file as the destination folder. foldername is based on zip filename.
			$source = trailingslashit($source);

		//Hook ability to change the source file location..
		$source = apply_filters('upgrader_source_selection', $source, $remote_source, $this);
		if ( is_wp_error($source) )
			return $source;

		//Has the source location changed? If so, we need a new source_files list.
		if ( $source !== $remote_source )
			$source_files = array_keys( $wp_filesystem->dirlist($source) );

		//Protection against deleting files in any important base directories.
		if ( in_array( $destination, array(ABSPATH, WP_CONTENT_DIR, WP_PLUGIN_DIR, WP_CONTENT_DIR . '/themes') ) ) {
			$remote_destination = trailingslashit($remote_destination) . trailingslashit(basename($source));
			$destination = trailingslashit($destination) . trailingslashit(basename($source));
		}

		if ( $clear_destination ) {
			//We're going to clear the destination if there's something there
			$this->skin->feedback('remove_old');
			$removed = true;
			if ( $wp_filesystem->exists($remote_destination) )
				$removed = $wp_filesystem->delete($remote_destination, true);
			$removed = apply_filters('upgrader_clear_destination', $removed, $local_destination, $remote_destination, $hook_extra);

			if ( is_wp_error($removed) )
				return $removed;
			else if ( ! $removed )
				return new WP_Error('remove_old_failed', $this->strings['remove_old_failed']);
		} elseif ( $wp_filesystem->exists($remote_destination) ) {
			//If we're not clearing the destination folder and something exists there already, Bail.
			//But first check to see if there are actually any files in the folder.
			$_files = $wp_filesystem->dirlist($remote_destination);
			if ( ! empty($_files) ) {
				$wp_filesystem->delete($remote_source, true); //Clear out the source files.
				return new WP_Error('folder_exists', $this->strings['folder_exists'], $remote_destination );
			}
		}

		//Create destination if needed
		if ( !$wp_filesystem->exists($remote_destination) )
			if ( !$wp_filesystem->mkdir($remote_destination, FS_CHMOD_DIR) )
				return new WP_Error('mkdir_failed', $this->strings['mkdir_failed'], $remote_destination);

		// Copy new version of item into place.
		$result = copy_dir($source, $remote_destination);
		if ( is_wp_error($result) ) {
			if ( $clear_working )
				$wp_filesystem->delete($remote_source, true);
			return $result;
		}

		//Clear the Working folder?
		if ( $clear_working )
			$wp_filesystem->delete($remote_source, true);

		$destination_name = basename( str_replace($local_destination, '', $destination) );
		if ( '.' == $destination_name )
			$destination_name = '';

		$this->result = compact('local_source', 'source', 'source_name', 'source_files', 'destination', 'destination_name', 'local_destination', 'remote_destination', 'clear_destination', 'delete_source_dir');

		$res = apply_filters('upgrader_post_install', true, $hook_extra, $this->result);
		if ( is_wp_error($res) ) {
			$this->result = $res;
			return $res;
		}

		//Bombard the calling function will all the info which we've just used.
		return $this->result;
	}

	function run($options) {

		$defaults = array( 	'package' => '', //Please always pass this.
							'destination' => '', //And this
							'clear_destination' => false,
							'clear_working' => true,
							'is_multi' => false,
							'hook_extra' => array() //Pass any extra $hook_extra args here, this will be passed to any hooked filters.
						);

		$options = wp_parse_args($options, $defaults);
		extract($options);

		//Connect to the Filesystem first.
		$res = $this->fs_connect( array(WP_CONTENT_DIR, $destination) );
		if ( ! $res ) //Mainly for non-connected filesystem.
			return false;

		if ( is_wp_error($res) ) {
			$this->skin->error($res);
			return $res;
		}

		if ( !$is_multi ) // call $this->header separately if running multiple times
			$this->skin->header();

		$this->skin->before();

		//Download the package (Note, This just returns the filename of the file if the package is a local file)
		$download = $this->download_package( $package );
		if ( is_wp_error($download) ) {
			$this->skin->error($download);
			$this->skin->after();
			return $download;
		}

		$delete_package = ($download != $package); // Do not delete a "local" file

		//Unzips the file into a temporary directory
		$working_dir = $this->unpack_package( $download, $delete_package );
		if ( is_wp_error($working_dir) ) {
			$this->skin->error($working_dir);
			$this->skin->after();
			return $working_dir;
		}

		//With the given options, this installs it to the destination directory.
		$result = $this->install_package( array(
											'source' => $working_dir,
											'destination' => $destination,
											'clear_destination' => $clear_destination,
											'clear_working' => $clear_working,
											'hook_extra' => $hook_extra
										) );
		$this->skin->set_result($result);
		if ( is_wp_error($result) ) {
			$this->skin->error($result);
			$this->skin->feedback('process_failed');
		} else {
			//Install Succeeded
			$this->skin->feedback('process_success');
		}
		$this->skin->after();

		if ( !$is_multi )
			$this->skin->footer();

		return $result;
	}

	function maintenance_mode($enable = false) {
		global $wp_filesystem;
		$file = $wp_filesystem->abspath() . '.maintenance';
		if ( $enable ) {
			$this->skin->feedback('maintenance_start');
			// Create maintenance file to signal that we are upgrading
			$maintenance_string = '<?php $upgrading = ' . time() . '; ?>';
			$wp_filesystem->delete($file);
			$wp_filesystem->put_contents($file, $maintenance_string, FS_CHMOD_FILE);
		} else if ( !$enable && $wp_filesystem->exists($file) ) {
			$this->skin->feedback('maintenance_end');
			$wp_filesystem->delete($file);
		}
	}

}

/**
 * Plugin Upgrader class for WordPress Plugins, It is designed to upgrade/install plugins from a local zip, remote zip URL, or uploaded zip file.
 *
 * @TODO More Detailed docs, for methods as well.
 *
 * @package WordPress
 * @subpackage Upgrader
 * @since 2.8.0
 */
class Plugin_Upgrader extends WP_Upgrader {

	var $result;
	var $bulk = false;
	var $show_before = '';

	function upgrade_strings() {
		$this->strings['up_to_date'] = __('The plugin is at the latest version.');
		$this->strings['no_package'] = __('Update package not available.');
		$this->strings['downloading_package'] = __('Downloading update from <span class="code">%s</span>&#8230;');
		$this->strings['unpack_package'] = __('Unpacking the update&#8230;');
		$this->strings['remove_old'] = __('Removing the old version of the plugin&#8230;');
		$this->strings['remove_old_failed'] = __('Could not remove the old plugin.');
		$this->strings['process_failed'] = __('Plugin update failed.');
		$this->strings['process_success'] = __('Plugin updated successfully.');
	}

	function install_strings() {
		$this->strings['no_package'] = __('Install package not available.');
		$this->strings['downloading_package'] = __('Downloading install package from <span class="code">%s</span>&#8230;');
		$this->strings['unpack_package'] = __('Unpacking the package&#8230;');
		$this->strings['installing_package'] = __('Installing the plugin&#8230;');
		$this->strings['process_failed'] = __('Plugin install failed.');
		$this->strings['process_success'] = __('Plugin installed successfully.');
	}

	function install($package) {

		$this->init();
		$this->install_strings();

		add_filter('upgrader_source_selection', array(&$this, 'check_package') );

		$this->run(array(
					'package' => $package,
					'destination' => WP_PLUGIN_DIR,
					'clear_destination' => false, //Do not overwrite files.
					'clear_working' => true,
					'hook_extra' => array()
					));

		remove_filter('upgrader_source_selection', array(&$this, 'check_package') );

		if ( ! $this->result || is_wp_error($this->result) )
			return $this->result;

		// Force refresh of plugin update information
		delete_site_transient('update_plugins');
		wp_cache_delete( 'plugins', 'plugins' );

		return true;
	}

	function upgrade($plugin) {

		$this->init();
		$this->upgrade_strings();

		$current = get_site_transient( 'update_plugins' );
		if ( !isset( $current->response[ $plugin ] ) ) {
			$this->skin->before();
			$this->skin->set_result(false);
			$this->skin->error('up_to_date');
			$this->skin->after();
			return false;
		}

		// Get the URL to the zip file
		$r = $current->response[ $plugin ];

		add_filter('upgrader_pre_install', array(&$this, 'deactivate_plugin_before_upgrade'), 10, 2);
		add_filter('upgrader_clear_destination', array(&$this, 'delete_old_plugin'), 10, 4);
		//'source_selection' => array(&$this, 'source_selection'), //there's a trac ticket to move up the directory for zip's which are made a bit differently, useful for non-.org plugins.

		$this->run(array(
					'package' => $r->package,
					'destination' => WP_PLUGIN_DIR,
					'clear_destination' => true,
					'clear_working' => true,
					'hook_extra' => array(
								'plugin' => $plugin
					)
				));

		// Cleanup our hooks, in case something else does a upgrade on this connection.
		remove_filter('upgrader_pre_install', array(&$this, 'deactivate_plugin_before_upgrade'));
		remove_filter('upgrader_clear_destination', array(&$this, 'delete_old_plugin'));

		if ( ! $this->result || is_wp_error($this->result) )
			return $this->result;

		// Force refresh of plugin update information
		delete_site_transient('update_plugins');
		wp_cache_delete( 'plugins', 'plugins' );
	}

	function bulk_upgrade($plugins) {

		$this->init();
		$this->bulk = true;
		$this->upgrade_strings();

		$current = get_site_transient( 'update_plugins' );

		add_filter('upgrader_clear_destination', array(&$this, 'delete_old_plugin'), 10, 4);

		$this->skin->header();

		// Connect to the Filesystem first.
		$res = $this->fs_connect( array(WP_CONTENT_DIR, WP_PLUGIN_DIR) );
		if ( ! $res ) {
			$this->skin->footer();
			return false;
		}

		$this->skin->bulk_header();

		// Only start maintenance mode if running in Multisite OR the plugin is in use
		$maintenance = is_multisite(); // @TODO: This should only kick in for individual sites if at all possible.
		foreach ( $plugins as $plugin )
			$maintenance = $maintenance || (is_plugin_active($plugin) && isset($current->response[ $plugin ]) ); // Only activate Maintenance mode if a plugin is active AND has an update available
		if ( $maintenance )
			$this->maintenance_mode(true);

		$results = array();

		$this->update_count = count($plugins);
		$this->update_current = 0;
		foreach ( $plugins as $plugin ) {
			$this->update_current++;
			$this->skin->plugin_info = get_plugin_data( WP_PLUGIN_DIR . '/' . $plugin, false, true);

			if ( !isset( $current->response[ $plugin ] ) ) {
				$this->skin->set_result(false);
				$this->skin->before();
				$this->skin->error('up_to_date');
				$this->skin->after();
				$results[$plugin] = false;
				continue;
			}

			// Get the URL to the zip file
			$r = $current->response[ $plugin ];

			$this->skin->plugin_active = is_plugin_active($plugin);

			$result = $this->run(array(
						'package' => $r->package,
						'destination' => WP_PLUGIN_DIR,
						'clear_destination' => true,
						'clear_working' => true,
						'is_multi' => true,
						'hook_extra' => array(
									'plugin' => $plugin
						)
					));

			$results[$plugin] = $this->result;

			// Prevent credentials auth screen from displaying multiple times
			if ( false === $result )
				break;
		} //end foreach $plugins

		$this->maintenance_mode(false);

		$this->skin->bulk_footer();

		$this->skin->footer();

		// Cleanup our hooks, in case something else does a upgrade on this connection.
		remove_filter('upgrader_clear_destination', array(&$this, 'delete_old_plugin'));

		// Force refresh of plugin update information
		delete_site_transient('update_plugins');
		wp_cache_delete( 'plugins', 'plugins' );

		return $results;
	}

	function check_package($source) {
		global $wp_filesystem;

		if ( is_wp_error($source) )
			return $source;

		$working_directory = str_replace( $wp_filesystem->wp_content_dir(), trailingslashit(WP_CONTENT_DIR), $source);
		if ( ! is_dir($working_directory) ) // Sanity check, if the above fails, lets not prevent installation.
			return $source;

		// Check the folder contains at least 1 valid plugin.
		$plugins_found = false;
		foreach ( glob( $working_directory . '*.php' ) as $file ) {
			$info = get_plugin_data($file, false, false);
			if ( !empty( $info['Name'] ) ) {
				$plugins_found = true;
				break;
			}
		}

		if ( ! $plugins_found )
			return new WP_Error( 'incompatible_archive', $this->strings['incompatible_archive'], __('No valid plugins were found.') );

		return $source;
	}

	//return plugin info.
	function plugin_info() {
		if ( ! is_array($this->result) )
			return false;
		if ( empty($this->result['destination_name']) )
			return false;

		$plugin = get_plugins('/' . $this->result['destination_name']); //Ensure to pass with leading slash
		if ( empty($plugin) )
			return false;

		$pluginfiles = array_keys($plugin); //Assume the requested plugin is the first in the list

		return $this->result['destination_name'] . '/' . $pluginfiles[0];
	}

	//Hooked to pre_install
	function deactivate_plugin_before_upgrade($return, $plugin) {

		if ( is_wp_error($return) ) //Bypass.
			return $return;

		$plugin = isset($plugin['plugin']) ? $plugin['plugin'] : '';
		if ( empty($plugin) )
			return new WP_Error('bad_request', $this->strings['bad_request']);

		if ( is_plugin_active($plugin) ) {
			//Deactivate the plugin silently, Prevent deactivation hooks from running.
			deactivate_plugins($plugin, true);
		}
	}

	//Hooked to upgrade_clear_destination
	function delete_old_plugin($removed, $local_destination, $remote_destination, $plugin) {
		global $wp_filesystem;

		if ( is_wp_error($removed) )
			return $removed; //Pass errors through.

		$plugin = isset($plugin['plugin']) ? $plugin['plugin'] : '';
		if ( empty($plugin) )
			return new WP_Error('bad_request', $this->strings['bad_request']);

		$plugins_dir = $wp_filesystem->wp_plugins_dir();
		$this_plugin_dir = trailingslashit( dirname($plugins_dir . $plugin) );

		if ( ! $wp_filesystem->exists($this_plugin_dir) ) //If its already vanished.
			return $removed;

		// If plugin is in its own directory, recursively delete the directory.
		if ( strpos($plugin, '/') && $this_plugin_dir != $plugins_dir ) //base check on if plugin includes directory separator AND that its not the root plugin folder
			$deleted = $wp_filesystem->delete($this_plugin_dir, true);
		else
			$deleted = $wp_filesystem->delete($plugins_dir . $plugin);

		if ( ! $deleted )
			return new WP_Error('remove_old_failed', $this->strings['remove_old_failed']);

		return true;
	}
}

/**
 * Theme Upgrader class for WordPress Themes, It is designed to upgrade/install themes from a local zip, remote zip URL, or uploaded zip file.
 *
 * @TODO More Detailed docs, for methods as well.
 *
 * @package WordPress
 * @subpackage Upgrader
 * @since 2.8.0
 */
class Theme_Upgrader extends WP_Upgrader {

	var $result;
	var $bulk = false;

	function upgrade_strings() {
		$this->strings['up_to_date'] = __('The theme is at the latest version.');
		$this->strings['no_package'] = __('Update package not available.');
		$this->strings['downloading_package'] = __('Downloading update from <span class="code">%s</span>&#8230;');
		$this->strings['unpack_package'] = __('Unpacking the update&#8230;');
		$this->strings['remove_old'] = __('Removing the old version of the theme&#8230;');
		$this->strings['remove_old_failed'] = __('Could not remove the old theme.');
		$this->strings['process_failed'] = __('Theme update failed.');
		$this->strings['process_success'] = __('Theme updated successfully.');
	}

	function install_strings() {
		$this->strings['no_package'] = __('Install package not available.');
		$this->strings['downloading_package'] = __('Downloading install package from <span class="code">%s</span>&#8230;');
		$this->strings['unpack_package'] = __('Unpacking the package&#8230;');
		$this->strings['installing_package'] = __('Installing the theme&#8230;');
		$this->strings['process_failed'] = __('Theme install failed.');
		$this->strings['process_success'] = __('Theme installed successfully.');
		/* translators: 1: theme name, 2: version */
		$this->strings['process_success_specific'] = __('Successfully installed the theme <strong>%1$s %2$s</strong>.');
		$this->strings['parent_theme_search'] = __('This theme requires a parent theme. Checking if it is installed&#8230;');
		/* translators: 1: theme name, 2: version */
		$this->strings['parent_theme_prepare_install'] = __('Preparing to install <strong>%1$s %2$s</strong>&#8230;');
		/* translators: 1: theme name, 2: version */
		$this->strings['parent_theme_currently_installed'] = __('The parent theme, <strong>%1$s %2$s</strong>, is currently installed.');
		/* translators: 1: theme name, 2: version */
		$this->strings['parent_theme_install_success'] = __('Successfully installed the parent theme, <strong>%1$s %2$s</strong>.');
		$this->strings['parent_theme_not_found'] = __('<strong>The parent theme could not be found.</strong> You will need to install the parent theme, <strong>%s</strong>, before you can use this child theme.');
	}

	function check_parent_theme_filter($install_result, $hook_extra, $child_result) {
		// Check to see if we need to install a parent theme
		$theme_info = $this->theme_info();

		if ( ! $theme_info->parent() )
			return $install_result;

		$this->skin->feedback( 'parent_theme_search' );

		if ( ! $theme_info->parent()->errors() ) {
			$this->skin->feedback( 'parent_theme_currently_installed', $theme_info->parent()->display('Name'), $theme_info->parent()->display('Version') );
			// We already have the theme, fall through.
			return $install_result;
		}

		// We don't have the parent theme, lets install it
		$api = themes_api('theme_information', array('slug' => $theme_info->get('Template'), 'fields' => array('sections' => false, 'tags' => false) ) ); //Save on a bit of bandwidth.

		if ( ! $api || is_wp_error($api) ) {
			$this->skin->feedback( 'parent_theme_not_found', $theme_info->get('Template') );
			// Don't show activate or preview actions after install
			add_filter('install_theme_complete_actions', array(&$this, 'hide_activate_preview_actions') );
			return $install_result;
		}

		// Backup required data we're going to override:
		$child_api = $this->skin->api;
		$child_success_message = $this->strings['process_success'];

		// Override them
		$this->skin->api = $api;
		$this->strings['process_success_specific'] = $this->strings['parent_theme_install_success'];//, $api->name, $api->version);

		$this->skin->feedback('parent_theme_prepare_install', $api->name, $api->version);

		add_filter('install_theme_complete_actions', '__return_false', 999); // Don't show any actions after installing the theme.

		// Install the parent theme
		$parent_result = $this->run( array(
			'package' => $api->download_link,
			'destination' => WP_CONTENT_DIR . '/themes',
			'clear_destination' => false, //Do not overwrite files.
			'clear_working' => true
		) );

		if ( is_wp_error($parent_result) )
			add_filter('install_theme_complete_actions', array(&$this, 'hide_activate_preview_actions') );

		// Start cleaning up after the parents installation
		remove_filter('install_theme_complete_actions', '__return_false', 999);

		// Reset child's result and data
		$this->result = $child_result;
		$this->skin->api = $child_api;
		$this->strings['process_success'] = $child_success_message;

		return $install_result;
	}

	function hide_activate_preview_actions($actions) {
		unset($actions['activate'], $actions['preview']);
		return $actions;
	}

	function install($package) {

		$this->init();
		$this->install_strings();

		add_filter('upgrader_source_selection', array(&$this, 'check_package') );
		add_filter('upgrader_post_install', array(&$this, 'check_parent_theme_filter'), 10, 3);

		$options = array(
						'package' => $package,
						'destination' => WP_CONTENT_DIR . '/themes',
						'clear_destination' => false, //Do not overwrite files.
						'clear_working' => true
						);

		$this->run($options);

		remove_filter('upgrader_source_selection', array(&$this, 'check_package') );
		remove_filter('upgrader_post_install', array(&$this, 'check_parent_theme_filter'), 10, 3);

		if ( ! $this->result || is_wp_error($this->result) )
			return $this->result;

		// Force refresh of theme update information
		delete_site_transient('update_themes');
		search_theme_directories( true );
		foreach ( wp_get_themes() as $theme )
			$theme->cache_delete();

		return true;
	}

	function upgrade($theme) {

		$this->init();
		$this->upgrade_strings();

		// Is an update available?
		$current = get_site_transient( 'update_themes' );
		if ( !isset( $current->response[ $theme ] ) ) {
			$this->skin->before();
			$this->skin->set_result(false);
			$this->skin->error('up_to_date');
			$this->skin->after();
			return false;
		}

		$r = $current->response[ $theme ];

		add_filter('upgrader_pre_install', array(&$this, 'current_before'), 10, 2);
		add_filter('upgrader_post_install', array(&$this, 'current_after'), 10, 2);
		add_filter('upgrader_clear_destination', array(&$this, 'delete_old_theme'), 10, 4);

		$options = array(
						'package' => $r['package'],
						'destination' => WP_CONTENT_DIR . '/themes',
						'clear_destination' => true,
						'clear_working' => true,
						'hook_extra' => array(
											'theme' => $theme
											)
						);

		$this->run($options);

		remove_filter('upgrader_pre_install', array(&$this, 'current_before'), 10, 2);
		remove_filter('upgrader_post_install', array(&$this, 'current_after'), 10, 2);
		remove_filter('upgrader_clear_destination', array(&$this, 'delete_old_theme'), 10, 4);

		if ( ! $this->result || is_wp_error($this->result) )
			return $this->result;

		// Force refresh of theme update information
		delete_site_transient('update_themes');
		search_theme_directories( true );
		foreach ( wp_get_themes() as $theme )
			$theme->cache_delete();

		return true;
	}

	function bulk_upgrade($themes) {

		$this->init();
		$this->bulk = true;
		$this->upgrade_strings();

		$current = get_site_transient( 'update_themes' );

		add_filter('upgrader_pre_install', array(&$this, 'current_before'), 10, 2);
		add_filter('upgrader_post_install', array(&$this, 'current_after'), 10, 2);
		add_filter('upgrader_clear_destination', array(&$this, 'delete_old_theme'), 10, 4);

		$this->skin->header();

		// Connect to the Filesystem first.
		$res = $this->fs_connect( array(WP_CONTENT_DIR) );
		if ( ! $res ) {
			$this->skin->footer();
			return false;
		}

		$this->skin->bulk_header();

		// Only start maintenance mode if running in Multisite OR the theme is in use
		$maintenance = is_multisite(); // @TODO: This should only kick in for individual sites if at all possible.
		foreach ( $themes as $theme )
			$maintenance = $maintenance || $theme == get_stylesheet() || $theme == get_template();
		if ( $maintenance )
			$this->maintenance_mode(true);

		$results = array();

		$this->update_count = count($themes);
		$this->update_current = 0;
		foreach ( $themes as $theme ) {
			$this->update_current++;

			if ( !isset( $current->response[ $theme ] ) ) {
				$this->skin->set_result(false);
				$this->skin->before();
				$this->skin->error('up_to_date');
				$this->skin->after();
				$results[$theme] = false;
				continue;
			}

			$this->skin->theme_info = $this->theme_info($theme);

			// Get the URL to the zip file
			$r = $current->response[ $theme ];

			$options = array(
							'package' => $r['package'],
							'destination' => WP_CONTENT_DIR . '/themes',
							'clear_destination' => true,
							'clear_working' => true,
							'hook_extra' => array(
												'theme' => $theme
												)
							);

			$result = $this->run($options);

			$results[$theme] = $this->result;

			// Prevent credentials auth screen from displaying multiple times
			if ( false === $result )
				break;
		} //end foreach $plugins

		$this->maintenance_mode(false);

		$this->skin->bulk_footer();

		$this->skin->footer();

		// Cleanup our hooks, in case something else does a upgrade on this connection.
		remove_filter('upgrader_pre_install', array(&$this, 'current_before'), 10, 2);
		remove_filter('upgrader_post_install', array(&$this, 'current_after'), 10, 2);
		remove_filter('upgrader_clear_destination', array(&$this, 'delete_old_theme'), 10, 4);

		// Force refresh of theme update information
		delete_site_transient('update_themes');
		search_theme_directories( true );
		foreach ( wp_get_themes() as $theme )
			$theme->cache_delete();

		return $results;
	}

	function check_package($source) {
		global $wp_filesystem;

		if ( is_wp_error($source) )
			return $source;

		// Check the folder contains a valid theme
		$working_directory = str_replace( $wp_filesystem->wp_content_dir(), trailingslashit(WP_CONTENT_DIR), $source);
		if ( ! is_dir($working_directory) ) // Sanity check, if the above fails, lets not prevent installation.
			return $source;

		// A proper archive should have a style.css file in the single subdirectory
		if ( ! file_exists( $working_directory . 'style.css' ) )
			return new WP_Error( 'incompatible_archive', $this->strings['incompatible_archive'], __('The theme is missing the <code>style.css</code> stylesheet.') );

		$info = get_file_data( $working_directory . 'style.css', array( 'Name' => 'Theme Name', 'Template' => 'Template' ) );

		if ( empty( $info['Name'] ) )
			return new WP_Error( 'incompatible_archive', $this->strings['incompatible_archive'], __("The <code>style.css</code> stylesheet doesn't contain a valid theme header.") );

		// If it's not a child theme, it must have at least an index.php to be legit.
		if ( empty( $info['Template'] ) && ! file_exists( $working_directory . 'index.php' ) )
			return new WP_Error( 'incompatible_archive', $this->strings['incompatible_archive'], __('The theme is missing the <code>index.php</code> file.') );

		return $source;
	}

	function current_before($return, $theme) {

		if ( is_wp_error($return) )
			return $return;

		$theme = isset($theme['theme']) ? $theme['theme'] : '';

		if ( $theme != get_stylesheet() ) //If not current
			return $return;
		//Change to maintenance mode now.
		if ( ! $this->bulk )
			$this->maintenance_mode(true);

		return $return;
	}

	function current_after($return, $theme) {
		if ( is_wp_error($return) )
			return $return;

		$theme = isset($theme['theme']) ? $theme['theme'] : '';

		if ( $theme != get_stylesheet() ) // If not current
			return $return;

		// Ensure stylesheet name hasnt changed after the upgrade:
		// @TODO: Note, This doesn't handle the Template changing, or the Template name changing.
		if ( $theme == get_stylesheet() && $theme != $this->result['destination_name'] ) {
			$theme_info = $this->theme_info();
			$stylesheet = $this->result['destination_name'];
			$template = $theme_info->get_template();
			switch_theme($template, $stylesheet, true);
		}

		//Time to remove maintenance mode
		if ( ! $this->bulk )
			$this->maintenance_mode(false);
		return $return;
	}

	function delete_old_theme($removed, $local_destination, $remote_destination, $theme) {
		global $wp_filesystem;

		$theme = isset($theme['theme']) ? $theme['theme'] : '';

		if ( is_wp_error($removed) || empty($theme) )
			return $removed; //Pass errors through.

		$themes_dir = $wp_filesystem->wp_themes_dir();
		if ( $wp_filesystem->exists( trailingslashit($themes_dir) . $theme ) )
			if ( ! $wp_filesystem->delete( trailingslashit($themes_dir) . $theme, true ) )
				return false;
		return true;
	}

	function theme_info($theme = null) {

		if ( empty($theme) ) {
			if ( !empty($this->result['destination_name']) )
				$theme = $this->result['destination_name'];
			else
				return false;
		}
		return wp_get_theme( $theme, WP_CONTENT_DIR . '/themes/' );
	}

}

/**
 * Core Upgrader class for WordPress. It allows for WordPress to upgrade itself in combination with the wp-admin/includes/update-core.php file
 *
 * @TODO More Detailed docs, for methods as well.
 *
 * @package WordPress
 * @subpackage Upgrader
 * @since 2.8.0
 */
class Core_Upgrader extends WP_Upgrader {

	function upgrade_strings() {
		$this->strings['up_to_date'] = __('WordPress is at the latest version.');
		$this->strings['no_package'] = __('Update package not available.');
		$this->strings['downloading_package'] = __('Downloading update from <span class="code">%s</span>&#8230;');
		$this->strings['unpack_package'] = __('Unpacking the update&#8230;');
		$this->strings['copy_failed'] = __('Could not copy files.');
		$this->strings['copy_failed_space'] = __('Could not copy files. You may have run out of disk space.' );
	}

	function upgrade($current) {
		global $wp_filesystem, $wp_version;

		$this->init();
		$this->upgrade_strings();

		if ( !empty($feedback) )
			add_filter('update_feedback', $feedback);

		// Is an update available?
		if ( !isset( $current->response ) || $current->response == 'latest' )
			return new WP_Error('up_to_date', $this->strings['up_to_date']);

		$res = $this->fs_connect( array(ABSPATH, WP_CONTENT_DIR) );
		if ( is_wp_error($res) )
			return $res;

		$wp_dir = trailingslashit($wp_filesystem->abspath());

		// If partial update is returned from the API, use that, unless we're doing a reinstall.
		// If we cross the new_bundled version number, then use the new_bundled zip.
		// Don't though if the constant is set to skip bundled items.
		// If the API returns a no_content zip, go with it. Finally, default to the full zip.
		if ( $current->packages->partial && 'reinstall' != $current->response && $wp_version == $current->partial_version )
			$to_download = 'partial';
		elseif ( $current->packages->new_bundled && version_compare( $wp_version, $current->new_bundled, '<' )
			&& ( ! defined( 'CORE_UPGRADE_SKIP_NEW_BUNDLED' ) || ! CORE_UPGRADE_SKIP_NEW_BUNDLED ) )
			$to_download = 'new_bundled';
		elseif ( $current->packages->no_content )
			$to_download = 'no_content';
		else
			$to_download = 'full';

		$download = $this->download_package( $current->packages->$to_download );
		if ( is_wp_error($download) )
			return $download;

		$working_dir = $this->unpack_package( $download );
		if ( is_wp_error($working_dir) )
			return $working_dir;

		// Copy update-core.php from the new version into place.
		if ( !$wp_filesystem->copy($working_dir . '/wordpress/wp-admin/includes/update-core.php', $wp_dir . 'wp-admin/includes/update-core.php', true) ) {
			$wp_filesystem->delete($working_dir, true);
			return new WP_Error('copy_failed', $this->strings['copy_failed']);
		}
		$wp_filesystem->chmod($wp_dir . 'wp-admin/includes/update-core.php', FS_CHMOD_FILE);

		require(ABSPATH . 'wp-admin/includes/update-core.php');

		if ( ! function_exists( 'update_core' ) )
			return new WP_Error( 'copy_failed_space', $this->strings['copy_failed_space'] );

		return update_core($working_dir, $wp_dir);
	}

}

/**
 * Generic Skin for the WordPress Upgrader classes. This skin is designed to be extended for specific purposes.
 *
 * @TODO More Detailed docs, for methods as well.
 *
 * @package WordPress
 * @subpackage Upgrader
 * @since 2.8.0
 */
class WP_Upgrader_Skin {

	var $upgrader;
	var $done_header = false;
	var $result = false;

	function __construct($args = array()) {
		$defaults = array( 'url' => '', 'nonce' => '', 'title' => '', 'context' => false );
		$this->options = wp_parse_args($args, $defaults);
	}

	function set_upgrader(&$upgrader) {
		if ( is_object($upgrader) )
			$this->upgrader =& $upgrader;
		$this->add_strings();
	}

	function add_strings() {
	}

	function set_result($result) {
		$this->result = $result;
	}

	function request_filesystem_credentials($error = false) {
		$url = $this->options['url'];
		$context = $this->options['context'];
		if ( !empty($this->options['nonce']) )
			$url = wp_nonce_url($url, $this->options['nonce']);
		return request_filesystem_credentials($url, '', $error, $context); //Possible to bring inline, Leaving as is for now.
	}

	function header() {
		if ( $this->done_header )
			return;
		$this->done_header = true;
		echo '<div class="wrap">';
		echo screen_icon();
		echo '<h2>' . $this->options['title'] . '</h2>';
	}
	function footer() {
		echo '</div>';
	}

	function error($errors) {
		if ( ! $this->done_header )
			$this->header();
		if ( is_string($errors) ) {
			$this->feedback($errors);
		} elseif ( is_wp_error($errors) && $errors->get_error_code() ) {
			foreach ( $errors->get_error_messages() as $message ) {
				if ( $errors->get_error_data() )
					$this->feedback($message . ' ' . $errors->get_error_data() );
				else
					$this->feedback($message);
			}
		}
	}

	function feedback($string) {
		if ( isset( $this->upgrader->strings[$string] ) )
			$string = $this->upgrader->strings[$string];

		if ( strpos($string, '%') !== false ) {
			$args = func_get_args();
			$args = array_splice($args, 1);
			if ( !empty($args) )
				$string = vsprintf($string, $args);
		}
		if ( empty($string) )
			return;
		show_message($string);
	}
	function before() {}
	function after() {}

}

/**
 * Plugin Upgrader Skin for WordPress Plugin Upgrades.
 *
 * @TODO More Detailed docs, for methods as well.
 *
 * @package WordPress
 * @subpackage Upgrader
 * @since 2.8.0
 */
class Plugin_Upgrader_Skin extends WP_Upgrader_Skin {
	var $plugin = '';
	var $plugin_active = false;
	var $plugin_network_active = false;

	function __construct($args = array()) {
		$defaults = array( 'url' => '', 'plugin' => '', 'nonce' => '', 'title' => __('Update Plugin') );
		$args = wp_parse_args($args, $defaults);

		$this->plugin = $args['plugin'];

		$this->plugin_active = is_plugin_active( $this->plugin );
		$this->plugin_network_active = is_plugin_active_for_network( $this->plugin );

		parent::__construct($args);
	}

	function after() {
		$this->plugin = $this->upgrader->plugin_info();
		if ( !empty($this->plugin) && !is_wp_error($this->result) && $this->plugin_active ){
			echo '<iframe style="border:0;overflow:hidden" width="100%" height="170px" src="' . wp_nonce_url('update.php?action=activate-plugin&networkwide=' . $this->plugin_network_active . '&plugin=' . $this->plugin, 'activate-plugin_' . $this->plugin) .'"></iframe>';
		}

		$update_actions =  array(
			'activate_plugin' => '<a href="' . wp_nonce_url('plugins.php?action=activate&amp;plugin=' . $this->plugin, 'activate-plugin_' . $this->plugin) . '" title="' . esc_attr__('Activate this plugin') . '" target="_parent">' . __('Activate Plugin') . '</a>',
			'plugins_page' => '<a href="' . self_admin_url('plugins.php') . '" title="' . esc_attr__('Go to plugins page') . '" target="_parent">' . __('Return to Plugins page') . '</a>'
		);
		if ( $this->plugin_active )
			unset( $update_actions['activate_plugin'] );
		if ( ! $this->result || is_wp_error($this->result) )
			unset( $update_actions['activate_plugin'] );

		$update_actions = apply_filters('update_plugin_complete_actions', $update_actions, $this->plugin);
		if ( ! empty($update_actions) )
			$this->feedback(implode(' | ', (array)$update_actions));
	}

	function before() {
		if ( $this->upgrader->show_before ) {
			echo $this->upgrader->show_before;
			$this->upgrader->show_before = '';
		}
	}
}

/**
 * Plugin Upgrader Skin for WordPress Plugin Upgrades.
 *
 * @package WordPress
 * @subpackage Upgrader
 * @since 3.0.0
 */
class Bulk_Upgrader_Skin extends WP_Upgrader_Skin {
	var $in_loop = false;
	var $error = false;

	function __construct($args = array()) {
		$defaults = array( 'url' => '', 'nonce' => '' );
		$args = wp_parse_args($args, $defaults);

		parent::__construct($args);
	}

	function add_strings() {
		$this->upgrader->strings['skin_upgrade_start'] = __('The update process is starting. This process may take a while on some hosts, so please be patient.');
		$this->upgrader->strings['skin_update_failed_error'] = __('An error occurred while updating %1$s: <strong>%2$s</strong>.');
		$this->upgrader->strings['skin_update_failed'] = __('The update of %1$s failed.');
		$this->upgrader->strings['skin_update_successful'] = __('%1$s updated successfully.').' <a onclick="%2$s" href="#" class="hide-if-no-js"><span>'.__('Show Details').'</span><span class="hidden">'.__('Hide Details').'</span>.</a>';
		$this->upgrader->strings['skin_upgrade_end'] = __('All updates have been completed.');
	}

	function feedback($string) {
		if ( isset( $this->upgrader->strings[$string] ) )
			$string = $this->upgrader->strings[$string];

		if ( strpos($string, '%') !== false ) {
			$args = func_get_args();
			$args = array_splice($args, 1);
			if ( !empty($args) )
				$string = vsprintf($string, $args);
		}
		if ( empty($string) )
			return;
		if ( $this->in_loop )
			echo "$string<br />\n";
		else
			echo "<p>$string</p>\n";
	}

	function header() {
		// Nothing, This will be displayed within a iframe.
	}

	function footer() {
		// Nothing, This will be displayed within a iframe.
	}
	function error($error) {
		if ( is_string($error) && isset( $this->upgrader->strings[$error] ) )
			$this->error = $this->upgrader->strings[$error];

		if ( is_wp_error($error) ) {
			foreach ( $error->get_error_messages() as $emessage ) {
				if ( $error->get_error_data() )
					$messages[] = $emessage . ' ' . $error->get_error_data();
				else
					$messages[] = $emessage;
			}
			$this->error = implode(', ', $messages);
		}
		echo '<script type="text/javascript">jQuery(\'.waiting-' . esc_js($this->upgrader->update_current) . '\').hide();</script>';
	}

	function bulk_header() {
		$this->feedback('skin_upgrade_start');
	}

	function bulk_footer() {
		$this->feedback('skin_upgrade_end');
	}

	function before($title = '') {
		$this->in_loop = true;
		printf( '<h4>' . $this->upgrader->strings['skin_before_update_header'] . ' <img alt="" src="' . admin_url( 'images/wpspin_light.gif' ) . '" class="hidden waiting-' . $this->upgrader->update_current . '" style="vertical-align:middle;" /></h4>',  $title, $this->upgrader->update_current, $this->upgrader->update_count);
		echo '<script type="text/javascript">jQuery(\'.waiting-' . esc_js($this->upgrader->update_current) . '\').show();</script>';
		echo '<div class="update-messages hide-if-js" id="progress-' . esc_attr($this->upgrader->update_current) . '"><p>';
		$this->flush_output();
	}

	function after($title = '') {
		echo '</p></div>';
		if ( $this->error || ! $this->result ) {
			if ( $this->error )
				echo '<div class="error"><p>' . sprintf($this->upgrader->strings['skin_update_failed_error'], $title, $this->error) . '</p></div>';
			else
				echo '<div class="error"><p>' . sprintf($this->upgrader->strings['skin_update_failed'], $title) . '</p></div>';

			echo '<script type="text/javascript">jQuery(\'#progress-' . esc_js($this->upgrader->update_current) . '\').show();</script>';
		}
		if ( !empty($this->result) && !is_wp_error($this->result) ) {
			echo '<div class="updated"><p>' . sprintf($this->upgrader->strings['skin_update_successful'], $title, 'jQuery(\'#progress-' . esc_js($this->upgrader->update_current) . '\').toggle();jQuery(\'span\', this).toggle(); return false;') . '</p></div>';
			echo '<script type="text/javascript">jQuery(\'.waiting-' . esc_js($this->upgrader->update_current) . '\').hide();</script>';
		}

		$this->reset();
		$this->flush_output();
	}

	function reset() {
		$this->in_loop = false;
		$this->error = false;
	}

	function flush_output() {
		wp_ob_end_flush_all();
		flush();
	}
}

class Bulk_Plugin_Upgrader_Skin extends Bulk_Upgrader_Skin {
	var $plugin_info = array(); // Plugin_Upgrader::bulk() will fill this in.

	function __construct($args = array()) {
		parent::__construct($args);
	}

	function add_strings() {
		parent::add_strings();
		$this->upgrader->strings['skin_before_update_header'] = __('Updating Plugin %1$s (%2$d/%3$d)');
	}

	function before() {
		parent::before($this->plugin_info['Title']);
	}

	function after() {
		parent::after($this->plugin_info['Title']);
	}
	function bulk_footer() {
		parent::bulk_footer();
		$update_actions =  array(
			'plugins_page' => '<a href="' . self_admin_url('plugins.php') . '" title="' . esc_attr__('Go to plugins page') . '" target="_parent">' . __('Return to Plugins page') . '</a>',
			'updates_page' => '<a href="' . self_admin_url('update-core.php') . '" title="' . esc_attr__('Go to WordPress Updates page') . '" target="_parent">' . __('Return to WordPress Updates') . '</a>'
		);

		$update_actions = apply_filters('update_bulk_plugins_complete_actions', $update_actions, $this->plugin_info);
		if ( ! empty($update_actions) )
			$this->feedback(implode(' | ', (array)$update_actions));
	}
}

class Bulk_Theme_Upgrader_Skin extends Bulk_Upgrader_Skin {
	var $theme_info = array(); // Theme_Upgrader::bulk() will fill this in.

	function __construct($args = array()) {
		parent::__construct($args);
	}

	function add_strings() {
		parent::add_strings();
		$this->upgrader->strings['skin_before_update_header'] = __('Updating Theme %1$s (%2$d/%3$d)');
	}

	function before() {
		parent::before( $this->theme_info->display('Name') );
	}

	function after() {
		parent::after( $this->theme_info->display('Name') );
	}

	function bulk_footer() {
		parent::bulk_footer();
		$update_actions =  array(
			'themes_page' => '<a href="' . self_admin_url('themes.php') . '" title="' . esc_attr__('Go to themes page') . '" target="_parent">' . __('Return to Themes page') . '</a>',
			'updates_page' => '<a href="' . self_admin_url('update-core.php') . '" title="' . esc_attr__('Go to WordPress Updates page') . '" target="_parent">' . __('Return to WordPress Updates') . '</a>'
		);

		$update_actions = apply_filters('update_bulk_theme_complete_actions', $update_actions, $this->theme_info );
		if ( ! empty($update_actions) )
			$this->feedback(implode(' | ', (array)$update_actions));
	}
}

/**
 * Plugin Installer Skin for WordPress Plugin Installer.
 *
 * @TODO More Detailed docs, for methods as well.
 *
 * @package WordPress
 * @subpackage Upgrader
 * @since 2.8.0
 */
class Plugin_Installer_Skin extends WP_Upgrader_Skin {
	var $api;
	var $type;

	function __construct($args = array()) {
		$defaults = array( 'type' => 'web', 'url' => '', 'plugin' => '', 'nonce' => '', 'title' => '' );
		$args = wp_parse_args($args, $defaults);

		$this->type = $args['type'];
		$this->api = isset($args['api']) ? $args['api'] : array();

		parent::__construct($args);
	}

	function before() {
		if ( !empty($this->api) )
			$this->upgrader->strings['process_success'] = sprintf( __('Successfully installed the plugin <strong>%s %s</strong>.'), $this->api->name, $this->api->version);
	}

	function after() {

		$plugin_file = $this->upgrader->plugin_info();

		$install_actions = array();

		$from = isset($_GET['from']) ? stripslashes($_GET['from']) : 'plugins';

		if ( 'import' == $from )
			$install_actions['activate_plugin'] = '<a href="' . wp_nonce_url('plugins.php?action=activate&amp;from=import&amp;plugin=' . $plugin_file, 'activate-plugin_' . $plugin_file) . '" title="' . esc_attr__('Activate this plugin') . '" target="_parent">' . __('Activate Plugin &amp; Run Importer') . '</a>';
		else
			$install_actions['activate_plugin'] = '<a href="' . wp_nonce_url('plugins.php?action=activate&amp;plugin=' . $plugin_file, 'activate-plugin_' . $plugin_file) . '" title="' . esc_attr__('Activate this plugin') . '" target="_parent">' . __('Activate Plugin') . '</a>';

		if ( is_multisite() && current_user_can( 'manage_network_plugins' ) ) {
			$install_actions['network_activate'] = '<a href="' . wp_nonce_url('plugins.php?action=activate&amp;networkwide=1&amp;plugin=' . $plugin_file, 'activate-plugin_' . $plugin_file) . '" title="' . esc_attr__('Activate this plugin for all sites in this network') . '" target="_parent">' . __('Network Activate') . '</a>';
			unset( $install_actions['activate_plugin'] );
		}

		if ( 'import' == $from )
			$install_actions['importers_page'] = '<a href="' . admin_url('import.php') . '" title="' . esc_attr__('Return to Importers') . '" target="_parent">' . __('Return to Importers') . '</a>';
		else if ( $this->type == 'web' )
			$install_actions['plugins_page'] = '<a href="' . self_admin_url('plugin-install.php') . '" title="' . esc_attr__('Return to Plugin Installer') . '" target="_parent">' . __('Return to Plugin Installer') . '</a>';
		else
			$install_actions['plugins_page'] = '<a href="' . self_admin_url('plugins.php') . '" title="' . esc_attr__('Return to Plugins page') . '" target="_parent">' . __('Return to Plugins page') . '</a>';

		if ( ! $this->result || is_wp_error($this->result) ) {
			unset( $install_actions['activate_plugin'] );
			unset( $install_actions['network_activate'] );
		}
		$install_actions = apply_filters('install_plugin_complete_actions', $install_actions, $this->api, $plugin_file);
		if ( ! empty($install_actions) )
			$this->feedback(implode(' | ', (array)$install_actions));
	}
}

/**
 * Theme Installer Skin for the WordPress Theme Installer.
 *
 * @TODO More Detailed docs, for methods as well.
 *
 * @package WordPress
 * @subpackage Upgrader
 * @since 2.8.0
 */
class Theme_Installer_Skin extends WP_Upgrader_Skin {
	var $api;
	var $type;

	function __construct($args = array()) {
		$defaults = array( 'type' => 'web', 'url' => '', 'theme' => '', 'nonce' => '', 'title' => '' );
		$args = wp_parse_args($args, $defaults);

		$this->type = $args['type'];
		$this->api = isset($args['api']) ? $args['api'] : array();

		parent::__construct($args);
	}

	function before() {
		if ( !empty($this->api) )
			$this->upgrader->strings['process_success'] = sprintf( $this->upgrader->strings['process_success_specific'], $this->api->name, $this->api->version);
	}

	function after() {
		if ( empty($this->upgrader->result['destination_name']) )
			return;

		$theme_info = $this->upgrader->theme_info();
		if ( empty( $theme_info ) )
			return;

		$name       = $theme_info->display('Name');
		$stylesheet = $this->upgrader->result['destination_name'];
		$template   = $theme_info->get_template();

		$preview_link = add_query_arg( array(
			'preview'    => 1,
			'template'   => $template,
			'stylesheet' => $stylesheet,
		), trailingslashit( get_home_url() ) );

		$activate_link = add_query_arg( array(
			'action'     => 'activate',
			'template'   => $template,
			'stylesheet' => $stylesheet,
		), admin_url('themes.php') );
		$activate_link = wp_nonce_url( $activate_link, 'switch-theme_' . $stylesheet );

		$install_actions = array();
		$install_actions['preview']  = '<a href="' . esc_url( $preview_link ) . '" class="hide-if-customize" title="' . esc_attr( sprintf( __('Preview &#8220;%s&#8221;'), $name ) ) . '">' . __('Preview') . '</a>';
		$install_actions['preview'] .= '<a href="' . wp_customize_url( $stylesheet ) . '" class="hide-if-no-customize load-customize" title="' . esc_attr( sprintf( __('Preview &#8220;%s&#8221;'), $name ) ) . '">' . __('Live Preview') . '</a>';
		$install_actions['activate'] = '<a href="' . esc_url( $activate_link ) . '" class="activatelink" title="' . esc_attr( sprintf( __('Activate &#8220;%s&#8221;'), $name ) ) . '">' . __('Activate') . '</a>';

		if ( is_network_admin() && current_user_can( 'manage_network_themes' ) )
			$install_actions['network_enable'] = '<a href="' . esc_url( wp_nonce_url( 'themes.php?action=enable&amp;theme=' . $stylesheet, 'enable-theme_' . $stylesheet ) ) . '" title="' . esc_attr__( 'Enable this theme for all sites in this network' ) . '" target="_parent">' . __( 'Network Enable' ) . '</a>';

		if ( $this->type == 'web' )
			$install_actions['themes_page'] = '<a href="' . self_admin_url('theme-install.php') . '" title="' . esc_attr__('Return to Theme Installer') . '" target="_parent">' . __('Return to Theme Installer') . '</a>';
		else
			$install_actions['themes_page'] = '<a href="' . self_admin_url('themes.php') . '" title="' . esc_attr__('Themes page') . '" target="_parent">' . __('Return to Themes page') . '</a>';

		if ( ! $this->result || is_wp_error($this->result) || is_network_admin() )
			unset( $install_actions['activate'], $install_actions['preview'] );

		$install_actions = apply_filters('install_theme_complete_actions', $install_actions, $this->api, $stylesheet, $theme_info);
		if ( ! empty($install_actions) )
			$this->feedback(implode(' | ', (array)$install_actions));
	}
}

/**
 * Theme Upgrader Skin for WordPress Theme Upgrades.
 *
 * @TODO More Detailed docs, for methods as well.
 *
 * @package WordPress
 * @subpackage Upgrader
 * @since 2.8.0
 */
class Theme_Upgrader_Skin extends WP_Upgrader_Skin {
	var $theme = '';

	function __construct($args = array()) {
		$defaults = array( 'url' => '', 'theme' => '', 'nonce' => '', 'title' => __('Update Theme') );
		$args = wp_parse_args($args, $defaults);

		$this->theme = $args['theme'];

		parent::__construct($args);
	}

	function after() {

		$update_actions = array();
		if ( ! empty( $this->upgrader->result['destination_name'] ) && $theme_info = $this->upgrader->theme_info() ) {
			$name       = $theme_info->display('Name');
			$stylesheet = $this->upgrader->result['destination_name'];
			$template   = $theme_info->get_template();

			$preview_link = add_query_arg( array(
				'preview'    => 1,
				'template'   => $template,
				'stylesheet' => $stylesheet,
			), trailingslashit( get_home_url() ) );

			$activate_link = add_query_arg( array(
				'action'     => 'activate',
				'template'   => $template,
				'stylesheet' => $stylesheet,
			), admin_url('themes.php') );
			$activate_link = wp_nonce_url( $activate_link, 'switch-theme_' . $stylesheet );

			if ( get_stylesheet() == $stylesheet ) {
				$update_actions['preview']  = '<a href="' . wp_customize_url( $stylesheet ) . '" class="hide-if-no-customize load-customize" title="' . esc_attr( sprintf( __('Customize &#8220;%s&#8221;'), $name ) ) . '">' . __('Customize') . '</a>';
			} else {
				$update_actions['preview']  = '<a href="' . esc_url( $preview_link ) . '" class="hide-if-customize" title="' . esc_attr( sprintf( __('Preview &#8220;%s&#8221;'), $name ) ) . '">' . __('Preview') . '</a>';
				$update_actions['preview'] .= '<a href="' . wp_customize_url( $stylesheet ) . '" class="hide-if-no-customize load-customize" title="' . esc_attr( sprintf( __('Preview &#8220;%s&#8221;'), $name ) ) . '">' . __('Live Preview') . '</a>';
				$update_actions['activate'] = '<a href="' . esc_url( $activate_link ) . '" class="activatelink" title="' . esc_attr( sprintf( __('Activate &#8220;%s&#8221;'), $name ) ) . '">' . __('Activate') . '</a>';
			}

			if ( ! $this->result || is_wp_error( $this->result ) || is_network_admin() )
				unset( $update_actions['preview'], $update_actions['activate'] );
		}

		$update_actions['themes_page'] = '<a href="' . self_admin_url('themes.php') . '" title="' . esc_attr__('Return to Themes page') . '" target="_parent">' . __('Return to Themes page') . '</a>';

		$update_actions = apply_filters('update_theme_complete_actions', $update_actions, $this->theme);
		if ( ! empty($update_actions) )
			$this->feedback(implode(' | ', (array)$update_actions));
	}
}

/**
 * Upgrade Skin helper for File uploads. This class handles the upload process and passes it as if its a local file to the Upgrade/Installer functions.
 *
 * @TODO More Detailed docs, for methods as well.
 *
 * @package WordPress
 * @subpackage Upgrader
 * @since 2.8.0
 */
class File_Upload_Upgrader {
	var $package;
	var $filename;
	var $id = 0;

	function __construct($form, $urlholder) {

		if ( empty($_FILES[$form]['name']) && empty($_GET[$urlholder]) )
			wp_die(__('Please select a file'));

		//Handle a newly uploaded file, Else assume its already been uploaded
		if ( ! empty($_FILES) ) {
			$overrides = array( 'test_form' => false, 'test_type' => false );
			$file = wp_handle_upload( $_FILES[$form], $overrides );

			if ( isset( $file['error'] ) )
				wp_die( $file['error'] );

			$this->filename = $_FILES[$form]['name'];
			$this->package = $file['file'];

			// Construct the object array
			$object = array(
				'post_title' => $this->filename,
				'post_content' => $file['url'],
				'post_mime_type' => $file['type'],
				'guid' => $file['url'],
				'context' => 'upgrader',
				'post_status' => 'private'
			);

			// Save the data
			$this->id = wp_insert_attachment( $object, $file['file'] );

			// schedule a cleanup for 2 hours from now in case of failed install
			wp_schedule_single_event( time() + 7200, 'upgrader_scheduled_cleanup', array( $this->id ) );

		} elseif ( is_numeric( $_GET[$urlholder] ) ) {
			// Numeric Package = previously uploaded file, see above.
			$this->id = (int) $_GET[$urlholder];
			$attachment = get_post( $this->id );
			if ( empty($attachment) )
				wp_die(__('Please select a file'));

			$this->filename = $attachment->post_title;
			$this->package = get_attached_file( $attachment->ID );
		} else {
			// Else, It's set to something, Back compat for plugins using the old (pre-3.3) File_Uploader handler.
			if ( ! ( ( $uploads = wp_upload_dir() ) && false === $uploads['error'] ) )
				wp_die( $uploads['error'] );

			$this->filename = $_GET[$urlholder];
			$this->package = $uploads['basedir'] . '/' . $this->filename;
		}
	}

	function cleanup() {
		if ( $this->id )
			wp_delete_attachment( $this->id );

		elseif ( file_exists( $this->package ) )
			return @unlink( $this->package );

		return true;
	}
}