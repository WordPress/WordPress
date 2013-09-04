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

require ABSPATH . 'wp-admin/includes/class-wp-upgrader-skins.php';

/**
 * WordPress Upgrader class for Upgrading/Installing a local set of files via the Filesystem Abstraction classes from a Zip file.
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
		$this->strings['no_files'] = __('The package contains no files.');
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
				case get_theme_root():
					if ( ! $wp_filesystem->wp_themes_dir() )
						return new WP_Error('fs_no_themes_dir', $this->strings['fs_no_themes_dir']);
					break;
				default:
					if ( ! $wp_filesystem->find_folder($dir) )
						return new WP_Error( 'fs_no_folder', sprintf( $this->strings['fs_no_folder'], esc_html( basename( $dir ) ) ) );
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
		global $wp_filesystem, $wp_theme_directories;

		$defaults = array( 'source' => '', 'destination' => '', //Please always pass these
						'clear_destination' => false, 'clear_working' => false,
						'abort_if_destination_exists' => true,
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
			return new WP_Error( 'incompatible_archive', $this->strings['incompatible_archive'], $this->strings['no_files'] ); //There are no files?
		else //It's only a single file, the upgrader will use the foldername of this file as the destination folder. foldername is based on zip filename.
			$source = trailingslashit($source);

		//Hook ability to change the source file location..
		$source = apply_filters('upgrader_source_selection', $source, $remote_source, $this);
		if ( is_wp_error($source) )
			return $source;

		//Has the source location changed? If so, we need a new source_files list.
		if ( $source !== $remote_source )
			$source_files = array_keys( $wp_filesystem->dirlist($source) );

		// Protection against deleting files in any important base directories.
		// Theme_Upgrader & Plugin_Upgrader also trigger this, as they pass the destination directory (WP_PLUGIN_DIR / wp-content/themes)
		// intending to copy the directory into the directory, whilst they pass the source as the actual files to copy.
		$protected_directories = array( ABSPATH, WP_CONTENT_DIR, WP_PLUGIN_DIR, WP_CONTENT_DIR . '/themes' );
		if ( is_array( $wp_theme_directories ) )
			$protected_directories = array_merge( $protected_directories, $wp_theme_directories );
		if ( in_array( $destination, $protected_directories ) ) {
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
		} elseif ( $abort_if_destination_exists && $wp_filesystem->exists($remote_destination) ) {
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
							'abort_if_destination_exists' => true, // Abort if the Destination directory exists, Pass clear_destination as false please
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
											'abort_if_destination_exists' => $abort_if_destination_exists,
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
		$this->strings['no_files'] = __('The plugin contains no files.');
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
		do_action( 'upgrader_process_complete', $this, array( 'action' => 'install', 'type' => 'plugin' ), $package );

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
		do_action( 'upgrader_process_complete', $this, array( 'action' => 'update', 'type' => 'plugin' ), $plugin );

		return true;
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

		// Only start maintenance mode if:
		// - running Multisite and there are one or more plugins specified, OR
		// - a plugin with an update available is currently active.
		// @TODO: For multisite, maintenance mode should only kick in for individual sites if at all possible.
		$maintenance = ( is_multisite() && ! empty( $plugins ) );
		foreach ( $plugins as $plugin )
			$maintenance = $maintenance || ( is_plugin_active( $plugin ) && isset( $current->response[ $plugin] ) );
		if ( $maintenance )
			$this->maintenance_mode(true);

		$results = array();

		$this->update_count = count($plugins);
		$this->update_current = 0;
		foreach ( $plugins as $plugin ) {
			$this->update_current++;
			$this->skin->plugin_info = get_plugin_data( WP_PLUGIN_DIR . '/' . $plugin, false, true);

			if ( !isset( $current->response[ $plugin ] ) ) {
				$this->skin->set_result(true);
				$this->skin->before();
				$this->skin->feedback('up_to_date');
				$this->skin->after();
				$results[$plugin] = true;
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
		do_action( 'upgrader_process_complete', $this, array( 'action' => 'update', 'type' => 'plugin', 'bulk' => true ), $plugins );

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

		if ( ! $wp_filesystem->exists($this_plugin_dir) ) //If it's already vanished.
			return $removed;

		// If plugin is in its own directory, recursively delete the directory.
		if ( strpos($plugin, '/') && $this_plugin_dir != $plugins_dir ) //base check on if plugin includes directory separator AND that it's not the root plugin folder
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
		$this->strings['no_files'] = __('The theme contains no files.');
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
			'destination' => get_theme_root(),
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
			'destination' => get_theme_root(),
			'clear_destination' => false, //Do not overwrite files.
			'clear_working' => true
		);

		$this->run($options);

		remove_filter('upgrader_source_selection', array(&$this, 'check_package') );
		remove_filter('upgrader_post_install', array(&$this, 'check_parent_theme_filter'));

		if ( ! $this->result || is_wp_error($this->result) )
			return $this->result;

		// Force refresh of theme update information
		wp_clean_themes_cache();
		do_action( 'upgrader_process_complete', $this, array( 'action' => 'install', 'type' => 'theme' ), $package );

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
			'destination' => get_theme_root( $theme ),
			'clear_destination' => true,
			'clear_working' => true,
			'hook_extra' => array(
				'theme' => $theme
			),
		);

		$this->run($options);

		remove_filter('upgrader_pre_install', array(&$this, 'current_before'));
		remove_filter('upgrader_post_install', array(&$this, 'current_after'));
		remove_filter('upgrader_clear_destination', array(&$this, 'delete_old_theme'));

		if ( ! $this->result || is_wp_error($this->result) )
			return $this->result;

		// Force refresh of theme update information
		wp_clean_themes_cache();
		do_action( 'upgrader_process_complete', $this, array( 'action' => 'update', 'type' => 'theme' ), $theme );

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

		// Only start maintenance mode if:
		// - running Multisite and there are one or more themes specified, OR
		// - a theme with an update available is currently in use.
		// @TODO: For multisite, maintenance mode should only kick in for individual sites if at all possible.
		$maintenance = ( is_multisite() && ! empty( $themes ) );
		foreach ( $themes as $theme )
			$maintenance = $maintenance || $theme == get_stylesheet() || $theme == get_template();
		if ( $maintenance )
			$this->maintenance_mode(true);

		$results = array();

		$this->update_count = count($themes);
		$this->update_current = 0;
		foreach ( $themes as $theme ) {
			$this->update_current++;

			$this->skin->theme_info = $this->theme_info($theme);

			if ( !isset( $current->response[ $theme ] ) ) {
				$this->skin->set_result(true);
				$this->skin->before();
				$this->skin->feedback('up_to_date');
				$this->skin->after();
				$results[$theme] = true;
				continue;
			}

			// Get the URL to the zip file
			$r = $current->response[ $theme ];

			$options = array(
				'package' => $r['package'],
				'destination' => get_theme_root( $theme ),
				'clear_destination' => true,
				'clear_working' => true,
				'hook_extra' => array(
					'theme' => $theme
				),
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
		remove_filter('upgrader_pre_install', array(&$this, 'current_before'));
		remove_filter('upgrader_post_install', array(&$this, 'current_after'));
		remove_filter('upgrader_clear_destination', array(&$this, 'delete_old_theme'));

		// Force refresh of theme update information
		wp_clean_themes_cache();
		do_action( 'upgrader_process_complete', $this, array( 'action' => 'update', 'type' => 'theme', 'bulk' => true ), $themes );

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

		// Ensure stylesheet name hasn't changed after the upgrade:
		if ( $theme == get_stylesheet() && $theme != $this->result['destination_name'] ) {
			wp_clean_themes_cache();
			$stylesheet = $this->result['destination_name'];
			switch_theme( $stylesheet );
		}

		//Time to remove maintenance mode
		if ( ! $this->bulk )
			$this->maintenance_mode(false);
		return $return;
	}

	function delete_old_theme( $removed, $local_destination, $remote_destination, $theme ) {
		global $wp_filesystem;

		if ( is_wp_error( $removed ) )
			return $removed; // Pass errors through.

		if ( ! isset( $theme['theme'] ) )
			return $removed;

		$theme = $theme['theme'];
		$themes_dir = trailingslashit( $wp_filesystem->wp_themes_dir( $theme ) );
		if ( $wp_filesystem->exists( $themes_dir . $theme ) ) {
			if ( ! $wp_filesystem->delete( $themes_dir . $theme, true ) )
				return false;
		}

		return true;
	}

	function theme_info($theme = null) {

		if ( empty($theme) ) {
			if ( !empty($this->result['destination_name']) )
				$theme = $this->result['destination_name'];
			else
				return false;
		}
		return wp_get_theme( $theme );
	}

}

/**
 * Core Upgrader class for WordPress. It allows for WordPress to upgrade itself in combination with the wp-admin/includes/update-core.php file
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

		$result = update_core( $working_dir, $wp_dir );
		do_action( 'upgrader_process_complete', $this, array( 'action' => 'update', 'type' => 'core' ), $result );
		return $result;
	}

}

/**
 * Upgrade Skin helper for File uploads. This class handles the upload process and passes it as if it's a local file to the Upgrade/Installer functions.
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

		//Handle a newly uploaded file, Else assume it's already been uploaded
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
