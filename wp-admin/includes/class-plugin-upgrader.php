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
	 * Whether a bulk upgrade/installation is being performed.
	 *
	 * @since 2.9.0
	 * @var bool $bulk
	 */
	public $bulk = false;

	/**
	 * New plugin info.
	 *
	 * @since 5.5.0
	 * @var array $new_plugin_data
	 *
	 * @see check_package()
	 */
	public $new_plugin_data = array();

	/**
	 * Initialize the upgrade strings.
	 *
	 * @since 2.8.0
	 */
	public function upgrade_strings() {
		$this->strings['up_to_date'] = __( 'The plugin is at the latest version.' );
		$this->strings['no_package'] = __( 'Update package not available.' );
		/* translators: %s: Package URL. */
		$this->strings['downloading_package']  = sprintf( __( 'Downloading update from %s&#8230;' ), '<span class="code">%s</span>' );
		$this->strings['unpack_package']       = __( 'Unpacking the update&#8230;' );
		$this->strings['remove_old']           = __( 'Removing the old version of the plugin&#8230;' );
		$this->strings['remove_old_failed']    = __( 'Could not remove the old plugin.' );
		$this->strings['process_failed']       = __( 'Plugin update failed.' );
		$this->strings['process_success']      = __( 'Plugin updated successfully.' );
		$this->strings['process_bulk_success'] = __( 'Plugins updated successfully.' );
	}

	/**
	 * Initialize the installation strings.
	 *
	 * @since 2.8.0
	 */
	public function install_strings() {
		$this->strings['no_package'] = __( 'Installation package not available.' );
		/* translators: %s: Package URL. */
		$this->strings['downloading_package'] = sprintf( __( 'Downloading installation package from %s&#8230;' ), '<span class="code">%s</span>' );
		$this->strings['unpack_package']      = __( 'Unpacking the package&#8230;' );
		$this->strings['installing_package']  = __( 'Installing the plugin&#8230;' );
		$this->strings['remove_old']          = __( 'Removing the current plugin&#8230;' );
		$this->strings['remove_old_failed']   = __( 'Could not remove the current plugin.' );
		$this->strings['no_files']            = __( 'The plugin contains no files.' );
		$this->strings['process_failed']      = __( 'Plugin installation failed.' );
		$this->strings['process_success']     = __( 'Plugin installed successfully.' );
		/* translators: 1: Plugin name, 2: Plugin version. */
		$this->strings['process_success_specific'] = __( 'Successfully installed the plugin <strong>%1$s %2$s</strong>.' );

		if ( ! empty( $this->skin->overwrite ) ) {
			if ( 'update-plugin' === $this->skin->overwrite ) {
				$this->strings['installing_package'] = __( 'Updating the plugin&#8230;' );
				$this->strings['process_failed']     = __( 'Plugin update failed.' );
				$this->strings['process_success']    = __( 'Plugin updated successfully.' );
			}

			if ( 'downgrade-plugin' === $this->skin->overwrite ) {
				$this->strings['installing_package'] = __( 'Downgrading the plugin&#8230;' );
				$this->strings['process_failed']     = __( 'Plugin downgrade failed.' );
				$this->strings['process_success']    = __( 'Plugin downgraded successfully.' );
			}
		}
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
	 * @return bool|WP_Error True if the installation was successful, false or a WP_Error otherwise.
	 */
	public function install( $package, $args = array() ) {
		$defaults    = array(
			'clear_update_cache' => true,
			'overwrite_package'  => false, // Do not overwrite files.
		);
		$parsed_args = wp_parse_args( $args, $defaults );

		$this->init();
		$this->install_strings();

		add_filter( 'upgrader_source_selection', array( $this, 'check_package' ) );

		if ( $parsed_args['clear_update_cache'] ) {
			// Clear cache so wp_update_plugins() knows about the new plugin.
			add_action( 'upgrader_process_complete', 'wp_clean_plugins_cache', 9, 0 );
		}

		$this->run(
			array(
				'package'           => $package,
				'destination'       => WP_PLUGIN_DIR,
				'clear_destination' => $parsed_args['overwrite_package'],
				'clear_working'     => true,
				'hook_extra'        => array(
					'type'   => 'plugin',
					'action' => 'install',
				),
			)
		);

		remove_action( 'upgrader_process_complete', 'wp_clean_plugins_cache', 9 );
		remove_filter( 'upgrader_source_selection', array( $this, 'check_package' ) );

		if ( ! $this->result || is_wp_error( $this->result ) ) {
			return $this->result;
		}

		// Force refresh of plugin update information.
		wp_clean_plugins_cache( $parsed_args['clear_update_cache'] );

		if ( $parsed_args['overwrite_package'] ) {
			/**
			 * Fires when the upgrader has successfully overwritten a currently installed
			 * plugin or theme with an uploaded zip package.
			 *
			 * @since 5.5.0
			 *
			 * @param string  $package      The package file.
			 * @param array   $data         The new plugin or theme data.
			 * @param string  $package_type The package type ('plugin' or 'theme').
			 */
			do_action( 'upgrader_overwrote_package', $package, $this->new_plugin_data, 'plugin' );
		}

		return true;
	}

	/**
	 * Upgrade a plugin.
	 *
	 * @since 2.8.0
	 * @since 3.7.0 The `$args` parameter was added, making clearing the plugin update cache optional.
	 *
	 * @param string $plugin Path to the plugin file relative to the plugins directory.
	 * @param array  $args {
	 *     Optional. Other arguments for upgrading a plugin package. Default empty array.
	 *
	 *     @type bool $clear_update_cache Whether to clear the plugin updates cache if successful.
	 *                                    Default true.
	 * }
	 * @return bool|WP_Error True if the upgrade was successful, false or a WP_Error object otherwise.
	 */
	public function upgrade( $plugin, $args = array() ) {
		$defaults    = array(
			'clear_update_cache' => true,
		);
		$parsed_args = wp_parse_args( $args, $defaults );

		$this->init();
		$this->upgrade_strings();

		$current = get_site_transient( 'update_plugins' );
		if ( ! isset( $current->response[ $plugin ] ) ) {
			$this->skin->before();
			$this->skin->set_result( false );
			$this->skin->error( 'up_to_date' );
			$this->skin->after();
			return false;
		}

		// Get the URL to the zip file.
		$r = $current->response[ $plugin ];

		add_filter( 'upgrader_pre_install', array( $this, 'deactivate_plugin_before_upgrade' ), 10, 2 );
		add_filter( 'upgrader_pre_install', array( $this, 'active_before' ), 10, 2 );
		add_filter( 'upgrader_clear_destination', array( $this, 'delete_old_plugin' ), 10, 4 );
		add_filter( 'upgrader_post_install', array( $this, 'active_after' ), 10, 2 );
		// There's a Trac ticket to move up the directory for zips which are made a bit differently, useful for non-.org plugins.
		// 'source_selection' => array( $this, 'source_selection' ),
		if ( $parsed_args['clear_update_cache'] ) {
			// Clear cache so wp_update_plugins() knows about the new plugin.
			add_action( 'upgrader_process_complete', 'wp_clean_plugins_cache', 9, 0 );
		}

		$this->run(
			array(
				'package'           => $r->package,
				'destination'       => WP_PLUGIN_DIR,
				'clear_destination' => true,
				'clear_working'     => true,
				'hook_extra'        => array(
					'plugin' => $plugin,
					'type'   => 'plugin',
					'action' => 'update',
				),
			)
		);

		// Cleanup our hooks, in case something else does a upgrade on this connection.
		remove_action( 'upgrader_process_complete', 'wp_clean_plugins_cache', 9 );
		remove_filter( 'upgrader_pre_install', array( $this, 'deactivate_plugin_before_upgrade' ) );
		remove_filter( 'upgrader_pre_install', array( $this, 'active_before' ) );
		remove_filter( 'upgrader_clear_destination', array( $this, 'delete_old_plugin' ) );
		remove_filter( 'upgrader_post_install', array( $this, 'active_after' ) );

		if ( ! $this->result || is_wp_error( $this->result ) ) {
			return $this->result;
		}

		// Force refresh of plugin update information.
		wp_clean_plugins_cache( $parsed_args['clear_update_cache'] );

		// Ensure any future auto-update failures trigger a failure email by removing
		// the last failure notification from the list when plugins update successfully.
		$past_failure_emails = get_option( 'auto_plugin_theme_update_emails', array() );

		if ( isset( $past_failure_emails[ $plugin ] ) ) {
			unset( $past_failure_emails[ $plugin ] );
			update_option( 'auto_plugin_theme_update_emails', $past_failure_emails );
		}

		return true;
	}

	/**
	 * Bulk upgrade several plugins at once.
	 *
	 * @since 2.8.0
	 * @since 3.7.0 The `$args` parameter was added, making clearing the plugin update cache optional.
	 *
	 * @param string[] $plugins Array of paths to plugin files relative to the plugins directory.
	 * @param array    $args {
	 *     Optional. Other arguments for upgrading several plugins at once.
	 *
	 *     @type bool $clear_update_cache Whether to clear the plugin updates cache if successful. Default true.
	 * }
	 * @return array|false An array of results indexed by plugin file, or false if unable to connect to the filesystem.
	 */
	public function bulk_upgrade( $plugins, $args = array() ) {
		$defaults    = array(
			'clear_update_cache' => true,
		);
		$parsed_args = wp_parse_args( $args, $defaults );

		$this->init();
		$this->bulk = true;
		$this->upgrade_strings();

		$current = get_site_transient( 'update_plugins' );

		add_filter( 'upgrader_clear_destination', array( $this, 'delete_old_plugin' ), 10, 4 );

		$this->skin->header();

		// Connect to the filesystem first.
		$res = $this->fs_connect( array( WP_CONTENT_DIR, WP_PLUGIN_DIR ) );
		if ( ! $res ) {
			$this->skin->footer();
			return false;
		}

		$this->skin->bulk_header();

		/*
		 * Only start maintenance mode if:
		 * - running Multisite and there are one or more plugins specified, OR
		 * - a plugin with an update available is currently active.
		 * @todo For multisite, maintenance mode should only kick in for individual sites if at all possible.
		 */
		$maintenance = ( is_multisite() && ! empty( $plugins ) );
		foreach ( $plugins as $plugin ) {
			$maintenance = $maintenance || ( is_plugin_active( $plugin ) && isset( $current->response[ $plugin ] ) );
		}
		if ( $maintenance ) {
			$this->maintenance_mode( true );
		}

		$results = array();

		$this->update_count   = count( $plugins );
		$this->update_current = 0;
		foreach ( $plugins as $plugin ) {
			$this->update_current++;
			$this->skin->plugin_info = get_plugin_data( WP_PLUGIN_DIR . '/' . $plugin, false, true );

			if ( ! isset( $current->response[ $plugin ] ) ) {
				$this->skin->set_result( 'up_to_date' );
				$this->skin->before();
				$this->skin->feedback( 'up_to_date' );
				$this->skin->after();
				$results[ $plugin ] = true;
				continue;
			}

			// Get the URL to the zip file.
			$r = $current->response[ $plugin ];

			$this->skin->plugin_active = is_plugin_active( $plugin );

			$result = $this->run(
				array(
					'package'           => $r->package,
					'destination'       => WP_PLUGIN_DIR,
					'clear_destination' => true,
					'clear_working'     => true,
					'is_multi'          => true,
					'hook_extra'        => array(
						'plugin' => $plugin,
					),
				)
			);

			$results[ $plugin ] = $result;

			// Prevent credentials auth screen from displaying multiple times.
			if ( false === $result ) {
				break;
			}
		} // End foreach $plugins.

		$this->maintenance_mode( false );

		// Force refresh of plugin update information.
		wp_clean_plugins_cache( $parsed_args['clear_update_cache'] );

		/** This action is documented in wp-admin/includes/class-wp-upgrader.php */
		do_action(
			'upgrader_process_complete',
			$this,
			array(
				'action'  => 'update',
				'type'    => 'plugin',
				'bulk'    => true,
				'plugins' => $plugins,
			)
		);

		$this->skin->bulk_footer();

		$this->skin->footer();

		// Cleanup our hooks, in case something else does a upgrade on this connection.
		remove_filter( 'upgrader_clear_destination', array( $this, 'delete_old_plugin' ) );

		// Ensure any future auto-update failures trigger a failure email by removing
		// the last failure notification from the list when plugins update successfully.
		$past_failure_emails = get_option( 'auto_plugin_theme_update_emails', array() );

		foreach ( $results as $plugin => $result ) {
			// Maintain last failure notification when plugins failed to update manually.
			if ( ! $result || is_wp_error( $result ) || ! isset( $past_failure_emails[ $plugin ] ) ) {
				continue;
			}

			unset( $past_failure_emails[ $plugin ] );
		}

		update_option( 'auto_plugin_theme_update_emails', $past_failure_emails );

		return $results;
	}

	/**
	 * Checks that the source package contains a valid plugin.
	 *
	 * Hooked to the {@see 'upgrader_source_selection'} filter by Plugin_Upgrader::install().
	 *
	 * @since 3.3.0
	 *
	 * @global WP_Filesystem_Base $wp_filesystem WordPress filesystem subclass.
	 * @global string             $wp_version    The WordPress version string.
	 *
	 * @param string $source The path to the downloaded package source.
	 * @return string|WP_Error The source as passed, or a WP_Error object on failure.
	 */
	public function check_package( $source ) {
		global $wp_filesystem, $wp_version;

		$this->new_plugin_data = array();

		if ( is_wp_error( $source ) ) {
			return $source;
		}

		$working_directory = str_replace( $wp_filesystem->wp_content_dir(), trailingslashit( WP_CONTENT_DIR ), $source );
		if ( ! is_dir( $working_directory ) ) { // Sanity check, if the above fails, let's not prevent installation.
			return $source;
		}

		// Check that the folder contains at least 1 valid plugin.
		$files = glob( $working_directory . '*.php' );
		if ( $files ) {
			foreach ( $files as $file ) {
				$info = get_plugin_data( $file, false, false );
				if ( ! empty( $info['Name'] ) ) {
					$this->new_plugin_data = $info;
					break;
				}
			}
		}

		if ( empty( $this->new_plugin_data ) ) {
			return new WP_Error( 'incompatible_archive_no_plugins', $this->strings['incompatible_archive'], __( 'No valid plugins were found.' ) );
		}

		$requires_php = isset( $info['RequiresPHP'] ) ? $info['RequiresPHP'] : null;
		$requires_wp  = isset( $info['RequiresWP'] ) ? $info['RequiresWP'] : null;

		if ( ! is_php_version_compatible( $requires_php ) ) {
			$error = sprintf(
				/* translators: 1: Current PHP version, 2: Version required by the uploaded plugin. */
				__( 'The PHP version on your server is %1$s, however the uploaded plugin requires %2$s.' ),
				PHP_VERSION,
				$requires_php
			);

			return new WP_Error( 'incompatible_php_required_version', $this->strings['incompatible_archive'], $error );
		}

		if ( ! is_wp_version_compatible( $requires_wp ) ) {
			$error = sprintf(
				/* translators: 1: Current WordPress version, 2: Version required by the uploaded plugin. */
				__( 'Your WordPress version is %1$s, however the uploaded plugin requires %2$s.' ),
				$wp_version,
				$requires_wp
			);

			return new WP_Error( 'incompatible_wp_required_version', $this->strings['incompatible_archive'], $error );
		}

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
		if ( ! is_array( $this->result ) ) {
			return false;
		}
		if ( empty( $this->result['destination_name'] ) ) {
			return false;
		}

		// Ensure to pass with leading slash.
		$plugin = get_plugins( '/' . $this->result['destination_name'] );
		if ( empty( $plugin ) ) {
			return false;
		}

		// Assume the requested plugin is the first in the list.
		$pluginfiles = array_keys( $plugin );

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
	 * @param bool|WP_Error $response The installation response before the installation has started.
	 * @param array         $plugin   Plugin package arguments.
	 * @return bool|WP_Error The original `$response` parameter or WP_Error.
	 */
	public function deactivate_plugin_before_upgrade( $response, $plugin ) {

		if ( is_wp_error( $response ) ) { // Bypass.
			return $response;
		}

		// When in cron (background updates) don't deactivate the plugin, as we require a browser to reactivate it.
		if ( wp_doing_cron() ) {
			return $response;
		}

		$plugin = isset( $plugin['plugin'] ) ? $plugin['plugin'] : '';
		if ( empty( $plugin ) ) {
			return new WP_Error( 'bad_request', $this->strings['bad_request'] );
		}

		if ( is_plugin_active( $plugin ) ) {
			// Deactivate the plugin silently, Prevent deactivation hooks from running.
			deactivate_plugins( $plugin, true );
		}

		return $response;
	}

	/**
	 * Turns on maintenance mode before attempting to background update an active plugin.
	 *
	 * Hooked to the {@see 'upgrader_pre_install'} filter by Plugin_Upgrader::upgrade().
	 *
	 * @since 5.4.0
	 *
	 * @param bool|WP_Error $response The installation response before the installation has started.
	 * @param array         $plugin   Plugin package arguments.
	 * @return bool|WP_Error The original `$response` parameter or WP_Error.
	 */
	public function active_before( $response, $plugin ) {
		if ( is_wp_error( $response ) ) {
			return $response;
		}

		// Only enable maintenance mode when in cron (background update).
		if ( ! wp_doing_cron() ) {
			return $response;
		}

		$plugin = isset( $plugin['plugin'] ) ? $plugin['plugin'] : '';

		// Only run if plugin is active.
		if ( ! is_plugin_active( $plugin ) ) {
			return $response;
		}

		// Change to maintenance mode. Bulk edit handles this separately.
		if ( ! $this->bulk ) {
			$this->maintenance_mode( true );
		}

		return $response;
	}

	/**
	 * Turns off maintenance mode after upgrading an active plugin.
	 *
	 * Hooked to the {@see 'upgrader_post_install'} filter by Plugin_Upgrader::upgrade().
	 *
	 * @since 5.4.0
	 *
	 * @param bool|WP_Error $response The installation response after the installation has finished.
	 * @param array         $plugin   Plugin package arguments.
	 * @return bool|WP_Error The original `$response` parameter or WP_Error.
	 */
	public function active_after( $response, $plugin ) {
		if ( is_wp_error( $response ) ) {
			return $response;
		}

		// Only disable maintenance mode when in cron (background update).
		if ( ! wp_doing_cron() ) {
			return $response;
		}

		$plugin = isset( $plugin['plugin'] ) ? $plugin['plugin'] : '';

		// Only run if plugin is active.
		if ( ! is_plugin_active( $plugin ) ) {
			return $response;
		}

		// Time to remove maintenance mode. Bulk edit handles this separately.
		if ( ! $this->bulk ) {
			$this->maintenance_mode( false );
		}

		return $response;
	}

	/**
	 * Deletes the old plugin during an upgrade.
	 *
	 * Hooked to the {@see 'upgrader_clear_destination'} filter by
	 * Plugin_Upgrader::upgrade() and Plugin_Upgrader::bulk_upgrade().
	 *
	 * @since 2.8.0
	 *
	 * @global WP_Filesystem_Base $wp_filesystem WordPress filesystem subclass.
	 *
	 * @param bool|WP_Error $removed            Whether the destination was cleared.
	 *                                          True on success, WP_Error on failure.
	 * @param string        $local_destination  The local package destination.
	 * @param string        $remote_destination The remote package destination.
	 * @param array         $plugin             Extra arguments passed to hooked filters.
	 * @return bool|WP_Error
	 */
	public function delete_old_plugin( $removed, $local_destination, $remote_destination, $plugin ) {
		global $wp_filesystem;

		if ( is_wp_error( $removed ) ) {
			return $removed; // Pass errors through.
		}

		$plugin = isset( $plugin['plugin'] ) ? $plugin['plugin'] : '';
		if ( empty( $plugin ) ) {
			return new WP_Error( 'bad_request', $this->strings['bad_request'] );
		}

		$plugins_dir     = $wp_filesystem->wp_plugins_dir();
		$this_plugin_dir = trailingslashit( dirname( $plugins_dir . $plugin ) );

		if ( ! $wp_filesystem->exists( $this_plugin_dir ) ) { // If it's already vanished.
			return $removed;
		}

		// If plugin is in its own directory, recursively delete the directory.
		// Base check on if plugin includes directory separator AND that it's not the root plugin folder.
		if ( strpos( $plugin, '/' ) && $this_plugin_dir !== $plugins_dir ) {
			$deleted = $wp_filesystem->delete( $this_plugin_dir, true );
		} else {
			$deleted = $wp_filesystem->delete( $plugins_dir . $plugin );
		}

		if ( ! $deleted ) {
			return new WP_Error( 'remove_old_failed', $this->strings['remove_old_failed'] );
		}

		return true;
	}
}
