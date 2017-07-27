<?php
/**
 * Upgrade API: Plugin_Upgrader class
 *
 * @package WordPress
 * @subpackage Upgrader
 * @since 4.6.0
 */

/**
 * Core class used for upgrading/installing plugins.
 *
 * It is designed to upgrade/install plugins from a local zip, remote zip URL,
 * or uploaded zip file.
 *
 * @since 2.8.0
 * @since 4.6.0 Moved to its own file from wp-admin/includes/class-wp-upgrader.php.
 *
 * @see WP_Upgrader
 */
class Plugin_Upgrader extends WP_Upgrader {

	/**
	 * Plugin upgrade result.
	 *
	 * @since 2.8.0
	 * @var array|WP_Error $result
	 *
	 * @see WP_Upgrader::$result
	 */
	public $result;

	/**
	 * Whether a bulk upgrade/install is being performed.
	 *
	 * @since 2.9.0
	 * @var bool $bulk
	 */
	public $bulk = false;

	/**
	 * Initialize the upgrade strings.
	 *
	 * @since 2.8.0
	 */
	public function upgrade_strings() {
		$this->strings['up_to_date'] = __('The plugin is at the latest version.');
		$this->strings['no_package'] = __('Update package not available.');
		$this->strings['downloading_package'] = __('Downloading update from <span class="code">%s</span>&#8230;');
		$this->strings['unpack_package'] = __('Unpacking the update&#8230;');
		$this->strings['remove_old'] = __('Removing the old version of the plugin&#8230;');
		$this->strings['remove_old_failed'] = __('Could not remove the old plugin.');
		$this->strings['process_failed'] = __('Plugin update failed.');
		$this->strings['process_success'] = __('Plugin updated successfully.');
		$this->strings['process_bulk_success'] = __('Plugins updated successfully.');
	}

	/**
	 * Initialize the install strings.
	 *
	 * @since 2.8.0
	 */
	public function install_strings() {
		$this->strings['no_package'] = __('Install package not available.');
		$this->strings['downloading_package'] = __('Downloading install package from <span class="code">%s</span>&#8230;');
		$this->strings['unpack_package'] = __('Unpacking the package&#8230;');
		$this->strings['installing_package'] = __('Installing the plugin&#8230;');
		$this->strings['no_files'] = __('The plugin contains no files.');
		$this->strings['process_failed'] = __('Plugin install failed.');
		$this->strings['process_success'] = __('Plugin installed successfully.');
	}

	/**
	 * Install a plugin package.
	 *
	 * @since 2.8.0
	 * @since 3.7.0 The `$args` parameter was added, making clearing the plugin update cache optional.
	 *
	 * @param string $package The full local path or URI of the package.
	 * @param array  $args {
	 *     Optional. Other arguments for installing a plugin package. Default empty array.
	 *
	 *     @type bool $clear_update_cache Whether to clear the plugin updates cache if successful.
	 *                                    Default true.
	 * }
	 * @return bool|WP_Error True if the install was successful, false or a WP_Error otherwise.
	 */
	public function install( $package, $args = array() ) {

		$defaults = array(
			'clear_update_cache' => true,
		);
		$parsed_args = wp_parse_args( $args, $defaults );

		$this->init();
		$this->install_strings();

		add_filter('upgrader_source_selection', array($this, 'check_package') );
		if ( $parsed_args['clear_update_cache'] ) {
			// Clear cache so wp_update_plugins() knows about the new plugin.
			add_action( 'upgrader_process_complete', 'wp_clean_plugins_cache', 9, 0 );
		}

		$this->run( array(
			'package' => $package,
			'destination' => WP_PLUGIN_DIR,
			'clear_destination' => false, // Do not overwrite files.
			'clear_working' => true,
			'hook_extra' => array(
				'type' => 'plugin',
				'action' => 'install',
			)
		) );

		remove_action( 'upgrader_process_complete', 'wp_clean_plugins_cache', 9 );
		remove_filter('upgrader_source_selection', array($this, 'check_package') );

		if ( ! $this->result || is_wp_error($this->result) )
			return $this->result;

		// Force refresh of plugin update information
		wp_clean_plugins_cache( $parsed_args['clear_update_cache'] );

		return true;
	}

	/**
	 * Upgrade a plugin.
	 *
	 * @since 2.8.0
	 * @since 3.7.0 The `$args` parameter was added, making clearing the plugin update cache optional.
	 *
	 * @param string $plugin The basename path to the main plugin file.
	 * @param array  $args {
	 *     Optional. Other arguments for upgrading a plugin package. Default empty array.
	 *
	 *     @type bool $clear_update_cache Whether to clear the plugin updates cache if successful.
	 *                                    Default true.
	 * }
	 * @return bool|WP_Error True if the upgrade was successful, false or a WP_Error object otherwise.
	 */
	public function upgrade( $plugin, $args = array() ) {

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
		if ( $parsed_args['clear_update_cache'] ) {
			// Clear cache so wp_update_plugins() knows about the new plugin.
			add_action( 'upgrader_process_complete', 'wp_clean_plugins_cache', 9, 0 );
		}

		$this->run( array(
			'package' => $r->package,
			'destination' => WP_PLUGIN_DIR,
			'clear_destination' => true,
			'clear_working' => true,
			'hook_extra' => array(
				'plugin' => $plugin,
				'type' => 'plugin',
				'action' => 'update',
			),
		) );

		// Cleanup our hooks, in case something else does a upgrade on this connection.
		remove_action( 'upgrader_process_complete', 'wp_clean_plugins_cache', 9 );
		remove_filter('upgrader_pre_install', array($this, 'deactivate_plugin_before_upgrade'));
		remove_filter('upgrader_clear_destination', array($this, 'delete_old_plugin'));

		if ( ! $this->result || is_wp_error($this->result) )
			return $this->result;

		// Force refresh of plugin update information
		wp_clean_plugins_cache( $parsed_args['clear_update_cache'] );

		return true;
	}

	/**
	 * Bulk upgrade several plugins at once.
	 *
	 * @since 2.8.0
	 * @since 3.7.0 The `$args` parameter was added, making clearing the plugin update cache optional.
	 *
	 * @param array $plugins Array of the basename paths of the plugins' main files.
	 * @param array $args {
	 *     Optional. Other arguments for upgrading several plugins at once. Default empty array.
	 *
	 *     @type bool $clear_update_cache Whether to clear the plugin updates cache if successful.
	 *                                    Default true.
	 * }
	 * @return array|false An array of results indexed by plugin file, or false if unable to connect to the filesystem.
	 */
	public function bulk_upgrade( $plugins, $args = array() ) {

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

		/*
		 * Only start maintenance mode if:
		 * - running Multisite and there are one or more plugins specified, OR
		 * - a plugin with an update available is currently active.
		 * @TODO: For multisite, maintenance mode should only kick in for individual sites if at all possible.
		 */
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
				$this->skin->set_result('up_to_date');
				$this->skin->before();
				$this->skin->feedback('up_to_date');
				$this->skin->after();
				$results[$plugin] = true;
				continue;
			}

			// Get the URL to the zip file.
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

		// Force refresh of plugin update information.
		wp_clean_plugins_cache( $parsed_args['clear_update_cache'] );

		/** This action is documented in wp-admin/includes/class-wp-upgrader.php */
		do_action( 'upgrader_process_complete', $this, array(
			'action' => 'update',
			'type' => 'plugin',
			'bulk' => true,
			'plugins' => $plugins,
		) );

		$this->skin->bulk_footer();

		$this->skin->footer();

		// Cleanup our hooks, in case something else does a upgrade on this connection.
		remove_filter('upgrader_clear_destination', array($this, 'delete_old_plugin'));

		return $results;
	}

	/**
	 * Check a source package to be sure it contains a plugin.
	 *
	 * This function is added to the {@see 'upgrader_source_selection'} filter by
	 * Plugin_Upgrader::install().
	 *
	 * @since 3.3.0
	 *
	 * @global WP_Filesystem_Base $wp_filesystem Subclass
	 *
	 * @param string $source The path to the downloaded package source.
	 * @return string|WP_Error The source as passed, or a WP_Error object
	 *                         if no plugins were found.
	 */
	public function check_package($source) {
		global $wp_filesystem;

		if ( is_wp_error($source) )
			return $source;

		$working_directory = str_replace( $wp_filesystem->wp_content_dir(), trailingslashit(WP_CONTENT_DIR), $source);
		if ( ! is_dir($working_directory) ) // Sanity check, if the above fails, let's not prevent installation.
			return $source;

		// Check the folder contains at least 1 valid plugin.
		$plugins_found = false;
		$files = glob( $working_directory . '*.php' );
		if ( $files ) {
			foreach ( $files as $file ) {
				$info = get_plugin_data( $file, false, false );
				if ( ! empty( $info['Name'] ) ) {
					$plugins_found = true;
					break;
				}
			}
		}

		if ( ! $plugins_found )
			return new WP_Error( 'incompatible_archive_no_plugins', $this->strings['incompatible_archive'], __( 'No valid plugins were found.' ) );

		return $source;
	}

	/**
	 * Retrieve the path to the file that contains the plugin info.
	 *
	 * This isn't used internally in the class, but is called by the skins.
	 *
	 * @since 2.8.0
	 *
	 * @return string|false The full path to the main plugin file, or false.
	 */
	public function plugin_info() {
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

	/**
	 * Deactivates a plugin before it is upgraded.
	 *
	 * Hooked to the {@see 'upgrader_pre_install'} filter by Plugin_Upgrader::upgrade().
	 *
	 * @since 2.8.0
	 * @since 4.1.0 Added a return value.
	 *
	 * @param bool|WP_Error  $return Upgrade offer return.
	 * @param array          $plugin Plugin package arguments.
	 * @return bool|WP_Error The passed in $return param or WP_Error.
	 */
	public function deactivate_plugin_before_upgrade($return, $plugin) {

		if ( is_wp_error($return) ) //Bypass.
			return $return;

		// When in cron (background updates) don't deactivate the plugin, as we require a browser to reactivate it
		if ( wp_doing_cron() )
			return $return;

		$plugin = isset($plugin['plugin']) ? $plugin['plugin'] : '';
		if ( empty($plugin) )
			return new WP_Error('bad_request', $this->strings['bad_request']);

		if ( is_plugin_active($plugin) ) {
			//Deactivate the plugin silently, Prevent deactivation hooks from running.
			deactivate_plugins($plugin, true);
		}

		return $return;
	}

	/**
	 * Delete the old plugin during an upgrade.
	 *
	 * Hooked to the {@see 'upgrader_clear_destination'} filter by
	 * Plugin_Upgrader::upgrade() and Plugin_Upgrader::bulk_upgrade().
	 *
	 * @since 2.8.0
	 *
	 * @global WP_Filesystem_Base $wp_filesystem Subclass
     *
	 * @param bool|WP_Error $removed
	 * @param string        $local_destination
	 * @param string        $remote_destination
	 * @param array         $plugin
	 * @return WP_Error|bool
	 */
	public function delete_old_plugin($removed, $local_destination, $remote_destination, $plugin) {
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
