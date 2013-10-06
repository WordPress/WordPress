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

		/**
		 * Filter whether to return the package.
		 *
		 * @since 3.7.0
		 *
		 * @param bool    $reply   Whether to bail without returning the package. Default is false.
		 * @param string  $package The package file name.
		 * @param object  $this    The WP_Upgrader instance.
		 */
		$reply = apply_filters( 'upgrader_pre_download', false, $package, $this );
		if ( false !== $reply )
			return $reply;

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

	function install_package( $args = array() ) {
		global $wp_filesystem, $wp_theme_directories;

		$defaults = array(
			'source' => '', // Please always pass this
			'destination' => '', // and this
			'clear_destination' => false,
			'clear_working' => false,
			'abort_if_destination_exists' => true,
			'hook_extra' => array()
		);

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

		$defaults = array(
			'package' => '', // Please always pass this.
			'destination' => '', // And this
			'clear_destination' => false,
			'abort_if_destination_exists' => true, // Abort if the Destination directory exists, Pass clear_destination as false please
			'clear_working' => true,
			'is_multi' => false,
			'hook_extra' => array() // Pass any extra $hook_extra args here, this will be passed to any hooked filters.
		);

		$options = wp_parse_args($options, $defaults);
		extract($options);

		//Connect to the Filesystem first.
		$res = $this->fs_connect( array(WP_CONTENT_DIR, $destination) );
		if ( ! $res ) //Mainly for non-connected filesystem.
			return false;

		if ( ! $is_multi ) // call $this->header separately if running multiple times
			$this->skin->header();

		$this->skin->before();

		if ( is_wp_error($res) ) {
			$this->skin->error($res);
			$this->skin->after();
			if ( ! $is_multi )
				$this->skin->footer();
			return $res;
		}

		//Download the package (Note, This just returns the filename of the file if the package is a local file)
		$download = $this->download_package( $package );
		if ( is_wp_error($download) ) {
			$this->skin->error($download);
			$this->skin->after();
			if ( ! $is_multi )
				$this->skin->footer();
			return $download;
		}

		$delete_package = ($download != $package); // Do not delete a "local" file

		//Unzips the file into a temporary directory
		$working_dir = $this->unpack_package( $download, $delete_package );
		if ( is_wp_error($working_dir) ) {
			$this->skin->error($working_dir);
			$this->skin->after();
			if ( ! $is_multi )
				$this->skin->footer();
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

		if ( ! $is_multi )
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

	function install( $package, $args = array() ) {

		$defaults = array(
			'clear_update_cache' => true,
		);
		$parsed_args = wp_parse_args( $args, $defaults );

		$this->init();
		$this->install_strings();

		add_filter('upgrader_source_selection', array($this, 'check_package') );

		$this->run( array(
			'package' => $package,
			'destination' => WP_PLUGIN_DIR,
			'clear_destination' => false, // Do not overwrite files.
			'clear_working' => true,
			'hook_extra' => array()
		) );

		remove_filter('upgrader_source_selection', array($this, 'check_package') );

		if ( ! $this->result || is_wp_error($this->result) )
			return $this->result;

		// Force refresh of plugin update information
		wp_clean_plugins_cache( $parsed_args['clear_update_cache'] );

		do_action( 'upgrader_process_complete', $this, array( 'action' => 'install', 'type' => 'plugin' ), $package );

		return true;
	}

	function upgrade( $plugin, $args = array() ) {

		$defaults = array(
			'clear_update_cache' => true,
		);
		$parsed_args = wp_parse_args( $args, $defaults );

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

		add_filter('upgrader_pre_install', array($this, 'deactivate_plugin_before_upgrade'), 10, 2);
		add_filter('upgrader_clear_destination', array($this, 'delete_old_plugin'), 10, 4);
		//'source_selection' => array($this, 'source_selection'), //there's a trac ticket to move up the directory for zip's which are made a bit differently, useful for non-.org plugins.

		$this->run( array(
			'package' => $r->package,
			'destination' => WP_PLUGIN_DIR,
			'clear_destination' => true,
			'clear_working' => true,
			'hook_extra' => array(
				'plugin' => $plugin
			),
		) );

		// Cleanup our hooks, in case something else does a upgrade on this connection.
		remove_filter('upgrader_pre_install', array($this, 'deactivate_plugin_before_upgrade'));
		remove_filter('upgrader_clear_destination', array($this, 'delete_old_plugin'));

		if ( ! $this->result || is_wp_error($this->result) )
			return $this->result;

		// Force refresh of plugin update information
		wp_clean_plugins_cache( $parsed_args['clear_update_cache'] );

		do_action( 'upgrader_process_complete', $this, array( 'action' => 'update', 'type' => 'plugin' ), $plugin );

		return true;
	}

	function bulk_upgrade( $plugins, $args = array() ) {

		$defaults = array(
			'clear_update_cache' => true,
		);
		$parsed_args = wp_parse_args( $args, $defaults );

		$this->init();
		$this->bulk = true;
		$this->upgrade_strings();

		$current = get_site_transient( 'update_plugins' );

		add_filter('upgrader_clear_destination', array($this, 'delete_old_plugin'), 10, 4);

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

			$result = $this->run( array(
				'package' => $r->package,
				'destination' => WP_PLUGIN_DIR,
				'clear_destination' => true,
				'clear_working' => true,
				'is_multi' => true,
				'hook_extra' => array(
					'plugin' => $plugin
				)
			) );

			$results[$plugin] = $this->result;

			// Prevent credentials auth screen from displaying multiple times
			if ( false === $result )
				break;
		} //end foreach $plugins

		$this->maintenance_mode(false);

		$this->skin->bulk_footer();

		$this->skin->footer();

		// Cleanup our hooks, in case something else does a upgrade on this connection.
		remove_filter('upgrader_clear_destination', array($this, 'delete_old_plugin'));

		// Force refresh of plugin update information
		wp_clean_plugins_cache( $parsed_args['clear_update_cache'] );

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
			add_filter('install_theme_complete_actions', array($this, 'hide_activate_preview_actions') );
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
			add_filter('install_theme_complete_actions', array($this, 'hide_activate_preview_actions') );

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

	function install( $package, $args = array() ) {

		$defaults = array(
			'clear_update_cache' => true,
		);
		$parsed_args = wp_parse_args( $args, $defaults );

		$this->init();
		$this->install_strings();

		add_filter('upgrader_source_selection', array($this, 'check_package') );
		add_filter('upgrader_post_install', array($this, 'check_parent_theme_filter'), 10, 3);

		$this->run( array(
			'package' => $package,
			'destination' => get_theme_root(),
			'clear_destination' => false, //Do not overwrite files.
			'clear_working' => true
		) );

		remove_filter('upgrader_source_selection', array($this, 'check_package') );
		remove_filter('upgrader_post_install', array($this, 'check_parent_theme_filter'));

		if ( ! $this->result || is_wp_error($this->result) )
			return $this->result;

		// Refresh the Theme Update information
		wp_clean_themes_cache( $parsed_args['clear_update_cache'] );

		do_action( 'upgrader_process_complete', $this, array( 'action' => 'install', 'type' => 'theme' ), $package );

		return true;
	}

	function upgrade( $theme, $args = array() ) {

		$defaults = array(
			'clear_update_cache' => true,
		);
		$parsed_args = wp_parse_args( $args, $defaults );

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

		add_filter('upgrader_pre_install', array($this, 'current_before'), 10, 2);
		add_filter('upgrader_post_install', array($this, 'current_after'), 10, 2);
		add_filter('upgrader_clear_destination', array($this, 'delete_old_theme'), 10, 4);

		$this->run( array(
			'package' => $r['package'],
			'destination' => get_theme_root( $theme ),
			'clear_destination' => true,
			'clear_working' => true,
			'hook_extra' => array(
				'theme' => $theme
			),
		) );

		remove_filter('upgrader_pre_install', array($this, 'current_before'));
		remove_filter('upgrader_post_install', array($this, 'current_after'));
		remove_filter('upgrader_clear_destination', array($this, 'delete_old_theme'));

		if ( ! $this->result || is_wp_error($this->result) )
			return $this->result;

		wp_clean_themes_cache( $parsed_args['clear_update_cache'] );

		do_action( 'upgrader_process_complete', $this, array( 'action' => 'update', 'type' => 'theme' ), $theme );

		return true;
	}

	function bulk_upgrade( $themes, $args = array() ) {

		$defaults = array(
			'clear_update_cache' => true,
		);
		$parsed_args = wp_parse_args( $args, $defaults );

		$this->init();
		$this->bulk = true;
		$this->upgrade_strings();

		$current = get_site_transient( 'update_themes' );

		add_filter('upgrader_pre_install', array($this, 'current_before'), 10, 2);
		add_filter('upgrader_post_install', array($this, 'current_after'), 10, 2);
		add_filter('upgrader_clear_destination', array($this, 'delete_old_theme'), 10, 4);

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

			$result = $this->run( array(
				'package' => $r['package'],
				'destination' => get_theme_root( $theme ),
				'clear_destination' => true,
				'clear_working' => true,
				'hook_extra' => array(
					'theme' => $theme
				),
			) );

			$results[$theme] = $this->result;

			// Prevent credentials auth screen from displaying multiple times
			if ( false === $result )
				break;
		} //end foreach $plugins

		$this->maintenance_mode(false);

		$this->skin->bulk_footer();

		$this->skin->footer();

		// Cleanup our hooks, in case something else does a upgrade on this connection.
		remove_filter('upgrader_pre_install', array($this, 'current_before'));
		remove_filter('upgrader_post_install', array($this, 'current_after'));
		remove_filter('upgrader_clear_destination', array($this, 'delete_old_theme'));

		// Refresh the Theme Update information
		wp_clean_themes_cache( $parsed_args['clear_update_cache'] );

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

add_action( 'upgrader_process_complete', array( 'Language_Pack_Upgrader', 'async_upgrade' ), 20, 3 );
class Language_Pack_Upgrader extends WP_Upgrader {

	var $result;
	var $bulk = true;

	static function async_upgrade( $upgrader, $context, $package ) {
		// Avoid recursion.
		if ( $upgrader instanceof Language_Pack_Upgrader )
			return;

		$lp_upgrader = new Language_Pack_Upgrader( new Headerless_Upgrader_Skin() );
		$lp_upgrader->upgrade();
	}

	function upgrade_strings() {
		$this->strings['starting_upgrade'] = __( 'Some of your translations need updating. Sit tight for a few more seconds while we update them as well.' );
		$this->strings['up_to_date'] = __( 'The translation is up to date.' ); // We need to silently skip this case
		$this->strings['no_package'] = __( 'Update package not available.' );
		$this->strings['downloading_package'] = __( 'Downloading translation from <span class="code">%s</span>&#8230;' );
		$this->strings['unpack_package'] = __( 'Unpacking the update&#8230;' );
		$this->strings['process_failed'] = __( 'Translation update failed.' );
		$this->strings['process_success'] = __( 'Translation updated successfully.' );
	}

	function upgrade( $update = false ) {
		if ( $update )
			$update = array( $update );
		$results = $this->bulk_upgrade( $update );
		return $results[0];
	}

	function bulk_upgrade( $language_updates = array() ) {
		global $wp_filesystem;

		$this->init();
		$this->upgrade_strings();

		if ( ! $language_updates )
			$language_updates = wp_get_translation_updates();

		if ( empty( $language_updates ) )
			return true;

		if ( 'upgrader_process_complete' == current_filter() )
			$this->skin->feedback( 'starting_upgrade' );

		add_filter( 'upgrader_source_selection', array( &$this, 'check_package' ), 10, 3 );

		$this->skin->header();

		// Connect to the Filesystem first.
		$res = $this->fs_connect( array( WP_CONTENT_DIR, WP_LANG_DIR ) );
		if ( ! $res ) {
			$this->skin->footer();
			return false;
		}

		$results = array();

		$this->update_count = count( $language_updates );
		$this->update_current = 0;

		// The filesystem's mkdir() is not recursive. Make sure WP_LANG_DIR exists,
		// as we then may need to create a /plugins or /themes directory inside of it.
		$remote_destination = $wp_filesystem->find_folder( WP_LANG_DIR );
		if ( ! $wp_filesystem->exists( $remote_destination ) )
			if ( ! $wp_filesystem->mkdir( $remote_destination, FS_CHMOD_DIR ) )
				return new WP_Error( 'mkdir_failed', $this->strings['mkdir_failed'], $remote_destination );

		foreach ( $language_updates as $language_update ) {

			$destination = WP_LANG_DIR;
			if ( 'plugin' == $language_update->type )
				$destination .= '/plugins';
			elseif ( 'theme' == $language_update->type )
				$destination .= '/themes';

			$this->update_current++;

			$options = array(
				'package' => $language_update->package,
				'destination' => $destination,
				'clear_destination' => false,
				'abort_if_destination_exists' => false, // We expect the destination to exist.
				'clear_working' => true,
				'is_multi' => true,
				'hook_extra' => array(
					'language_update_type' => $language_update->type,
					'language_update' => $language_update,
				)
			);

			$result = $this->run( $options );

			$results[] = $this->result;

			// Prevent credentials auth screen from displaying multiple times.
			if ( false === $result )
				break;
		}

		// Clean up our hooks, in case something else does an upgrade on this connection.
		remove_filter( 'upgrader_source_selection', array( &$this, 'check_package' ), 10, 2 );

		return $results;
	}

	function check_package( $source, $remote_source ) {
		global $wp_filesystem;

		if ( is_wp_error( $source ) )
			return $source;

		// Check that the folder contains a valid language.
		$files = $wp_filesystem->dirlist( $remote_source );

		// Check to see if a .po and .mo exist in the folder.
		$po = $mo = false;
		foreach ( (array) $files as $file => $filedata ) {
			if ( '.po' == substr( $file, -3 ) )
				$po = true;
			elseif ( '.mo' == substr( $file, -3 ) )
				$mo = true;
		}

		if ( ! $mo || ! $po )
			return new WP_Error( 'incompatible_archive', $this->strings['incompatible_archive'],
				__( 'The language pack is missing either the <code>.po</code> or <code>.mo</code> files.' ) );

		return $source;
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
		$this->strings['start_rollback'] = __( 'Attempting to roll back to previous version.' );
		$this->strings['rollback_was_required'] = __( 'Due to an error during updating, WordPress has rolled back to your previous version.' );
	}

	function upgrade( $current, $args = array() ) {
		global $wp_filesystem, $wp_version;

		$defaults = array(
			'pre_check_md5'    => true,
			'attempt_rollback' => false,
			'do_rollback'      => false,
		);
		$parsed_args = wp_parse_args( $args, $defaults );

		$this->init();
		$this->upgrade_strings();

		// Is an update available?
		if ( !isset( $current->response ) || $current->response == 'latest' )
			return new WP_Error('up_to_date', $this->strings['up_to_date']);

		$res = $this->fs_connect( array(ABSPATH, WP_CONTENT_DIR) );
		if ( is_wp_error($res) )
			return $res;

		$wp_dir = trailingslashit($wp_filesystem->abspath());

		// Pre-cache the checksums for the versions we care about
		get_core_checksums( array( $wp_version, $current->version ) );

		$partial = true;
		if ( $parsed_args['do_rollback'] )
			$partial = false;
		elseif ( $parsed_args['pre_check_md5'] && ! $this->check_files() )
			$partial = false;

		// If partial update is returned from the API, use that, unless we're doing a reinstall.
		// If we cross the new_bundled version number, then use the new_bundled zip.
		// Don't though if the constant is set to skip bundled items.
		// If the API returns a no_content zip, go with it. Finally, default to the full zip.
		if ( $parsed_args['do_rollback'] && $current->packages->rollback )
			$to_download = 'rollback';
		elseif ( $current->packages->partial && 'reinstall' != $current->response && $wp_version == $current->partial_version && $partial )
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

		require_once( ABSPATH . 'wp-admin/includes/update-core.php' );

		if ( ! function_exists( 'update_core' ) )
			return new WP_Error( 'copy_failed_space', $this->strings['copy_failed_space'] );

		$result = update_core( $working_dir, $wp_dir );

		// In the event of an error, rollback to the previous version
		if ( is_wp_error( $result ) && $parsed_args['attempt_rollback'] && $current->packages->rollback ) {
			apply_filters( 'update_feedback', $result );
			apply_filters( 'update_feedback', $this->strings['start_rollback'] );

			$this->upgrade( $current, array_merge( $parsed_args, array( 'do_rollback' => true ) ) );

			$result = new WP_Error( 'rollback_was_required', $this->strings['rollback_was_required'] );
		}
		do_action( 'upgrader_process_complete', $this, array( 'action' => 'update', 'type' => 'core' ), $result );
		return $result;
	}

	// Determines if this WordPress Core version should update to $offered_ver or not
	static function should_upgrade_to_version( $offered_ver /* x.y.z */ ) {
		include ABSPATH . WPINC . '/version.php'; // $wp_version; // x.y.z

		$current_branch = implode( '.', array_slice( preg_split( '/[.-]/', $wp_version  ), 0, 2 ) ); // x.y
		$new_branch     = implode( '.', array_slice( preg_split( '/[.-]/', $offered_ver ), 0, 2 ) ); // x.y
		$current_is_development_version = (bool) strpos( $wp_version, '-' );

		// Defaults:
		$upgrade_dev   = true;
		$upgrade_minor = true;
		$upgrade_major = false;

		// WP_AUTO_UPDATE_CORE = true (all), 'minor', false.
		if ( defined( 'WP_AUTO_UPDATE_CORE' ) ) {
			if ( false === WP_AUTO_UPDATE_CORE ) {
				// Defaults to turned off, unless a filter allows it
				$upgrade_dev = $upgrade_minor = $upgrade_major = false;
			} elseif ( true === WP_AUTO_UPDATE_CORE ) {
				// ALL updates for core
				$upgrade_dev = $upgrade_minor = $upgrade_major = true;
			} elseif ( 'minor' === WP_AUTO_UPDATE_CORE ) {
				// Only minor updates for core
				$upgrade_dev = $upgrade_major = false;
				$upgrade_minor = true;
			}
		}

		// 1: If we're already on that version, not much point in updating?
		if ( $offered_ver == $wp_version )
			return false;

		// 2: If we're running a newer version, that's a nope
		if ( version_compare( $wp_version, $offered_ver, '>' ) )
			return false;

		// 3: 3.7-alpha-25000 -> 3.7-alpha-25678 -> 3.7-beta1 -> 3.7-beta2
		if ( $current_is_development_version ) {
			if ( ! apply_filters( 'allow_dev_auto_core_updates', $upgrade_dev ) )
				return false;
			// else fall through to minor + major branches below
		}

		// 4: Minor In-branch updates (3.7.0 -> 3.7.1 -> 3.7.2 -> 3.7.4)
		if ( $current_branch == $new_branch )
			return apply_filters( 'allow_minor_auto_core_updates', $upgrade_minor );

		// 5: Major version updates (3.7.0 -> 3.8.0 -> 3.9.1)
		if ( version_compare( $new_branch, $current_branch, '>' ) )
			return apply_filters( 'allow_major_auto_core_updates', $upgrade_major );

		// If we're not sure, we don't want it
		return false;
	}

	function check_files() {
		global $wp_version;

		$checksums = get_core_checksums( $wp_version );

		if ( empty( $checksums[ $wp_version ] ) || ! is_array( $checksums[ $wp_version ] ) )
			return false;

		foreach ( $checksums[ $wp_version ] as $file => $checksum ) {
			// Skip files which get updated
			if ( 'wp-content' == substr( $file, 0, 10 ) )
				continue;
			if ( ! file_exists( ABSPATH . $file ) || md5_file( ABSPATH . $file ) !== $checksum )
				return false;
		}

		return true;
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

/**
 * WordPress Automatic Upgrader helper class
 *
 * @since 3.7.0
 */
class WP_Automatic_Upgrader {

	static $upgrade_results = array();

	static function upgrader_disabled() {
		// That's a no if you don't want files changes
		if ( defined( 'DISALLOW_FILE_MODS' ) && DISALLOW_FILE_MODS )
			return true;

		// More fine grained control can be done through the WP_AUTO_UPDATE_CORE constant and filters
		if ( defined( 'AUTOMATIC_UPDATER_DISABLED' ) && AUTOMATIC_UPDATER_DISABLED )
			return true;

		if ( defined( 'WP_INSTALLING' ) )
			return true;

		return apply_filters( 'auto_upgrader_disabled', false );
	}

	/**
	 * Check for GIT/SVN checkouts.
	 */
	static function is_vcs_checkout( $context ) {
		$context_dirs = array( untrailingslashit( $context ) );
		if ( $context !== ABSPATH )
			$context_dirs[] = untrailingslashit( ABSPATH );

		$vcs_dirs = array( '.svn', '.git', '.hg', '.bzr' );
		$check_dirs = array();

		foreach ( $context_dirs as $context_dir ) {
			// Walk up from $context_dir to the root.
			do {
				$check_dirs[] = $context_dir;
			} while ( $context_dir != dirname( $context_dir ) && $context_dir = dirname( $context_dir ) );
		}

		$check_dirs = array_unique( $check_dirs );

		// Search all directories we've found for evidence of version control.
		foreach ( $vcs_dirs as $vcs_dir ) {
			foreach ( $check_dirs as $check_dir ) {
				if ( $checkout = is_dir( rtrim( $check_dir, '\\/' ) . "/$vcs_dir" ) )
					break 2;
			}
		}
		return apply_filters( 'auto_upgrade_is_vcs_checkout', $checkout, $context );
	}

	/**
	 * Tests to see if we should upgrade a specific item, does not test to see if we CAN update the item.
	 */
	static function should_auto_update( $type, $item, $context ) {

		if ( self::upgrader_disabled() )
			return false;

		if ( self::is_vcs_checkout( $context ) )
			return false;

		// Next up, do we actually have it enabled for this type of update?
		switch ( $type ) {
			case 'language':
				$upgrade = true;
				break;
			case 'core':
				$upgrade = Core_Upgrader::should_upgrade_to_version( $item->current );
				break;
			default:
			case 'plugin':
			case 'theme':
				$upgrade = false;
				break;
		}

		// And does the user / plugins want it?
		// Plugins may filter on 'auto_upgrade_plugin', and check the 2nd param, $item, to only enable it for certain Plugins/Themes
		if ( ! apply_filters( 'auto_upgrade_' . $type, $upgrade, $item ) )
			return false;

		// If it's a core update, are we actually compatible with it's requirements?
		if ( 'core' == $type ) {
			global $wpdb;

			$php_compat = version_compare( phpversion(), $item->php_version, '>=' );
			if ( file_exists( WP_CONTENT_DIR . '/db.php' ) && empty( $wpdb->is_mysql ) )
				$mysql_compat = true;
			else
				$mysql_compat = version_compare( $wpdb->db_version(), $item->mysql_version, '>=' );

			if ( ! $php_compat || ! $mysql_compat )
				return false;
		}

		return true;
	}

	// Checks to see if WP_Filesystem is setup to allow unattended upgrades
	static function can_auto_update( $context, $skin = false ) {
		if ( ! $skin )
			$skin = new Automatic_Upgrader_Skin();
		return (bool) $skin->request_filesystem_credentials( false, $context );
	}

	static function upgrade( $type, $item ) {

		$skin = new Automatic_Upgrader_Skin();

		switch ( $type ) {
			case 'core':
				// The Core upgrader doesn't use the Upgrader's skin during the actual main part of the upgrade, instead, firing a filter
				add_filter( 'update_feedback', array( $skin, 'feedback' ) );
				$upgrader = new Core_Upgrader( $skin );
				$context  = ABSPATH;
				break;
			case 'plugin':
				$upgrader = new Plugin_Upgrader( $skin );
				$context  = WP_PLUGIN_DIR; // We don't support custom Plugin directories, or updates for WPMU_PLUGIN_DIR
				break;
			case 'theme':
				$upgrader = new Theme_Upgrader( $skin );
				$context  = get_theme_root( $item );
				break;
			case 'language':
				$upgrader = new Language_Pack_Upgrader( $skin );
				$context  = WP_CONTENT_DIR; // WP_LANG_DIR;
				break;
		}

		// Determine if we can perform this upgrade or not
		if ( ! self::should_auto_update( $type, $item, $context ) || ! self::can_auto_update( $context, $skin ) )
			return false;

		switch ( $type ) {
			case 'core':
				$skin->feedback( __( 'Updating to WordPress %s' ), $item->version );
				$item_name = sprintf( __( 'WordPress %s' ), $item->version );
				break;
			case 'theme':
				$theme = wp_get_theme( $item );
				$item_name = $theme->Get( 'Name' );
				$skin->feedback( __( 'Updating theme: %s' ), $item_name );
				break;
			case 'plugin':
				$plugin_data = get_plugin_data( $context . '/' . $item );
				$item_name = $plugin_data['Name'];
				$skin->feedback( __( 'Updating plugin: %s' ), $item_name );
				break;
			case 'language':
				if ( 'theme' == $item->type ) {
					$theme = wp_get_theme( $item->slug );
					$skin->feedback( sprintf(
						__( 'Updating the %1$s translation for the %2$s theme' ),
						$item->language,
						$theme->Get( 'Name' )
					) );
					$item_name = sprintf(
						__( '%1$s translation for the %2$s theme' ),
						$item->language,
						$theme->Get( 'Name' )
					);
				} elseif ( 'plugin' == $item->type ) {
					$plugin_data = get_plugins( '/' . $item->slug );
					$plugin_data = array_shift( $plugin_data );
					$skin->feedback( sprintf(
						__( 'Updating the %1$s translation for the %2$s plugin' ),
						$item->language,
						$plugin_data['Name']
					) );
					$item_name = sprintf(
						__( '%1$s translation for the %2$s plugin' ),
						$item->language,
						$plugin_data['Name']
					);
				} else {
					$skin->feedback( sprintf(
						__( 'Updating %s translation' ),
						$item->language
					) );
					$item_name = sprintf(
						__( '%s translation' ),
						$item->language
					);
				}
				
				break;
		}

		// Boom, This sites about to get a whole new splash of paint!
		$upgrade_result = $upgrader->upgrade( $item, array(
			'clear_update_cache' => false,
			'pre_check_md5'      => false, /* always use partial builds if possible for core updates */
			'attempt_rollback'   => true, /* only available for core updates */
		) );

		// Core doesn't output this, so lets append it so we don't get confused
		if ( 'core' == $type ) {
			if ( is_wp_error( $upgrade_result ) ) {
				$skin->error( __( 'Installation Failed' ), $upgrade_result );
			} else {
				$skin->feedback( __( 'WordPress updated successfully' ) );
			}
		}

		self::$upgrade_results[ $type ][] = (object) array(
			'item'     => $item,
			'result'   => $upgrade_result,
			'name'     => $item_name,
			'messages' => $skin->get_upgrade_messages()
		);

		return $upgrade_result;
	}

	/**
	 * Kicks off a upgrade request for each item in the upgrade "queue"
	 */
	static function perform_auto_updates() {

		$lock_name = 'auto_upgrader.lock';
		if ( get_site_option( $lock_name ) ) {
			// Test to see if it was set more than an hour ago, if so, cleanup.
			if ( get_site_option( $lock_name ) < ( time() - HOUR_IN_SECONDS ) )
				delete_site_option( $lock_name );
			else // The process is already locked
				return;
		}
		// Lock upgrades for us for half an hour
		if ( ! add_site_option( $lock_name, microtime( true ), HOUR_IN_SECONDS / 2 ) )
			return;

		// Don't automatically run these thins, as we'll handle it ourselves
		remove_action( 'upgrader_process_complete', array( 'Language_Pack_Upgrader', 'async_upgrade' ), 20, 3 ); 
		remove_action( 'upgrader_process_complete', 'wp_version_check' ); 
		remove_action( 'upgrader_process_complete', 'wp_update_plugins' ); 
		remove_action( 'upgrader_process_complete', 'wp_update_themes' ); 

		// Next, Plugins
		wp_update_plugins(); // Check for Plugin updates
		$plugin_updates = get_site_transient( 'update_plugins' );
		if ( $plugin_updates && !empty( $plugin_updates->response ) ) {
			foreach ( array_keys( $plugin_updates->response ) as $plugin ) {
				self::upgrade( 'plugin', $plugin );
			}
			// Force refresh of plugin update information
			wp_clean_plugins_cache();
		}

		// Next, those themes we all love
		wp_update_themes();  // Check for Theme updates
		$theme_updates = get_site_transient( 'update_themes' );
		if ( $theme_updates && !empty( $theme_updates->response ) ) {
			foreach ( array_keys( $theme_updates->response ) as $theme ) {
				self::upgrade( 'theme', $theme );
			}
			// Force refresh of theme update information
			wp_clean_themes_cache();
		}

		// Next, Process any core upgrade
		wp_version_check(); // Check for Core updates
		$core_update = find_core_auto_update();
		if ( $core_update ) {
			self::upgrade( 'core', $core_update );
			delete_site_transient( 'update_core' );
		}

		// Cleanup, and check for any pending translations
		wp_version_check();  // check for Core updates
		wp_update_themes();  // Check for Theme updates
		wp_update_plugins(); // Check for Plugin updates

		// Finally, Process any new translations
		$language_updates = wp_get_translation_updates();
		if ( $language_updates ) {
			foreach ( $language_updates as $update ) {
				self::upgrade( 'language', $update );
			}
			// Clear existing caches
			wp_clean_plugins_cache();
			wp_clean_themes_cache();
			delete_site_transient( 'update_core' );

			wp_version_check();  // check for Core updates
			wp_update_themes();  // Check for Theme updates
			wp_update_plugins(); // Check for Plugin updates
		}

		/**
		 * Filter whether to email an update summary to the site administrator.
		 *
		 * @since 3.7.0
		 *
		 * @param bool                         Whether or not email should be sent to administrator. Default true.
		 * @param bool|array $core_update      An array of core update data, false otherwise.
		 * @param object     $theme_updates    Object containing theme update properties.
		 * @param object     $plugin_updates   Object containing plugin update properties.
		 * @param array      $language_updates Array containing the Language updates available.
		 * @param array      $upgrade_results  Array of the upgrade results keyed by upgrade type, and plugin/theme slug.
		 */
		if ( apply_filters( 'enable_auto_upgrade_email', true, $core_update, $theme_updates, $plugin_updates, $language_updates, self::$upgrade_results ) )
			self::send_email();

		// Clear the lock
		delete_site_option( $lock_name );

	}

	static function send_email() {

		if ( empty( self::$upgrade_results ) )
			return;

		$upgrade_count = 0;
		foreach ( self::$upgrade_results as $type => $upgrades )
			$upgrade_count += count( $upgrades );

		$body = array();
		$failures = 0;

		// Core
		if ( isset( self::$upgrade_results['core'] ) ) {
			$result = self::$upgrade_results['core'][0];
			if ( $result->result && ! is_wp_error( $result->result ) ) {
				$body[] = sprintf( 'SUCCESS: WordPress was successfully updated to %s', $result->name );
			} else {
				$body[] = sprintf( 'FAILED: WordPress failed to update to %s', $result->name );
				$failures++;
			}
			$body[] = '';
		}

		// Plugins, Themes, Languages
		foreach ( array( 'plugin', 'theme', 'language' ) as $type ) {
			if ( ! isset( self::$upgrade_results[ $type ] ) )
				continue;
			$success_items = wp_list_filter( self::$upgrade_results[ $type ], array( 'result' => true ) );
			if ( $success_items ) {
				$body[] = "The following {$type}s were successfully updated:";
				foreach ( wp_list_pluck( $success_items, 'name' ) as $name )
					$body[] = ' * SUCCESS: ' . $name;
			}
			if ( $success_items != self::$upgrade_results[ $type ] ) {
				// Failed updates
				$body[] = "The following {$type}s failed to update:";
				foreach ( self::$upgrade_results[ $type ] as $item ) {
					if ( ! $item->result || is_wp_error( $item->result ) ) {
						$body[] = ' * FAILED: ' . $item->name;
						$failures++;
					}
				}
			}
			$body[] = '';
		}

		if ( $failures ) {
			$body[] = '';
			$body[] = 'BETA TESTING?';
			$body[] = '=============';
			$body[] = '';
			$body[] = 'If you think these failures might be due to a bug in WordPress 3.7, could you report it?';
			$body[] = ' * Open a thread in the support forums: http://wordpress.org/support/forum/alphabeta';
			$body[] = " * Or, if you're comfortable writing a bug report: http://core.trac.wordpress.org/";
			$body[] = '';
			$body[] = 'Thanks! -- The WordPress Team';
			$body[] = '';
			$subject = sprintf( '[%s] There were failures during background updates', get_bloginfo( 'name' ) );
		} else {
			$subject = sprintf( '[%s] Background updates have finished', get_bloginfo( 'name' ) );
		}

		$body[] = 'UPGRADE LOG';
		$body[] = '===========';
		$body[] = '';

		foreach ( array( 'core', 'plugin', 'theme', 'language' ) as $type ) {
			if ( ! isset( self::$upgrade_results[ $type ] ) )
				continue;
			foreach ( self::$upgrade_results[ $type ] as $upgrade ) {
				$body[] = $upgrade->name;
				$body[] = str_repeat( '-', strlen( $upgrade->name ) );
				foreach ( $upgrade->messages as $message )
					$body[] = "  " . html_entity_decode( str_replace( '&#8230;', '...', $message ) );
				if ( is_wp_error( $upgrade->result ) )
					$body[] = '  Error: [' . $upgrade->result->get_error_code() . '] ' . $upgrade->result->get_error_message();
				$body[] = '';
			}
		}

		//echo "<h1>\n$subject\n</h1>\n";
		//echo "<pre>\n" . implode( "\n", $body ) . "\n</pre>";

		wp_mail( get_site_option( 'admin_email' ), $subject, implode( "\n", $body ) );
	}
}